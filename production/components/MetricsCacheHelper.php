<?php
namespace app\components;

use Yii;

/**
 * Computes project metrics and writes them to chatbot_metrics_cache.
 * Called on project selection, dashboard open, and hourly cron.
 *
 * Metrics stored per project_id:
 *   estimated_cost        — cost dashboard formula (DashboardController)
 *   actual_cost_po        — sum of PO resource amounts (committed spend)
 *   actual_cost_grn       — GRN-received × PO rate (goods received)
 *   physical_progress_pct — KPI dashboard formula
 *   total_activities      — schedule activity count
 *   completed_activities  — completed activity count
 *   critical_count        — critical path activity count
 *   delayed_critical      — critical activities past end date
 *   max_delay_days        — worst delay on critical path
 *   planned_end_date      — latest planned end across critical activities (unix ts)
 */
class MetricsCacheHelper
{
    /**
     * Refresh cache for one project. Safe to call multiple times.
     */
    public static function refresh($projectId)
    {
        $db  = Yii::$app->db;
        $pid = (int)$projectId;
        if (!$pid) return;

        $metrics = [];

        // ------------------------------------------------------------------
        // ESTIMATED COST
        // Formula: SUM(rate * quantity * activity_qty) per activity
        // Source:  DashboardController (same as cost dashboard chart)
        //          workgroup_activities_new WHERE estimate=1 AND pricing_status=0
        // ------------------------------------------------------------------
        $est = $db->createCommand("
            SELECT SUM(pern.rate * pern.quantity * pen.activity_qty) AS estimated_cost
            FROM workgroup_activities_new wa
            JOIN pricing_estimate_new pen
                ON pen.activity_Id = wa.id
               AND pen.project_Id  = wa.project_Id
               AND pen.pricing_status = 0
            JOIN pricing_estimate_resources_new pern
                ON pern.activity_id = wa.id
               AND pern.project_id  = wa.project_Id
               AND pern.pricing_status = 0
            WHERE wa.estimate = 1
              AND wa.pricing_status = 0
              AND wa.project_Id = :pid
        ", [':pid' => $pid])->queryScalar();

        $metrics['estimated_cost'] = (float)($est ?? 0);

        // ------------------------------------------------------------------
        // ACTUAL COST — PO committed (purchase_order_resources total)
        // Source: DashboardController actionEstimatecost (totMatPurAmount path)
        // ------------------------------------------------------------------
        $actPo = $db->createCommand("
            SELECT SUM(por.amount) AS actual_cost
            FROM purchase_order_resources por
            JOIN purchase_orders po ON po.order_id = por.order_id
            WHERE po.project_id  = :pid
              AND po.delete_status  = 0
              AND por.delete_status = 0
        ", [':pid' => $pid])->queryScalar();

        $metrics['actual_cost_po'] = (float)($actPo ?? 0);

        // ------------------------------------------------------------------
        // ACTUAL COST — GRN received (goods physically received × PO rate)
        // ------------------------------------------------------------------
        $actGrn = $db->createCommand("
            SELECT SUM(g.GRN_Quantity * por.rate) AS actual_cost
            FROM pricing_estimate_resources_new pern
            JOIN purchase_order_resources por ON por.allocation_id = pern.pricing_resourceid
            JOIN goods_received_note g         ON g.GRN_Purchase_Order = por.order_id
                                              AND g.GRN_Item = pern.resource_Id
            JOIN purchase_orders po            ON po.order_id = por.order_id
            WHERE pern.project_id   = :pid
              AND po.project_id     = :pid
              AND po.delete_status  = 0
              AND por.delete_status = 0
        ", [':pid' => $pid])->queryScalar();

        $metrics['actual_cost_grn'] = (float)($actGrn ?? 0);

        // ------------------------------------------------------------------
        // PHYSICAL PROGRESS %
        // Source: KPI dashboard — SUM(cumulated/quantity) / count * 100
        // ------------------------------------------------------------------
        $prog = $db->createCommand("
            SELECT
                COUNT(sa.id) AS total_activities,
                SUM(CASE WHEN sa.completed_status = 1 THEN 1 ELSE 0 END) AS completed_activities,
                ROUND(
                    SUM(
                        CASE
                            WHEN sa.quantity > 0
                            THEN LEAST(COALESCE(rpt.cumulated_qty, 0) / sa.quantity, 1.0)
                            ELSE (CASE WHEN sa.completed_status = 1 THEN 1.0 ELSE 0.0 END)
                        END
                    ) / NULLIF(COUNT(sa.id), 0) * 100, 1
                ) AS physical_progress_pct
            FROM scheduleactivities sa
            LEFT JOIN (
                SELECT activity_id, SUM(currentqty) AS cumulated_qty
                FROM schedule_progress_report_log
                GROUP BY activity_id
            ) rpt ON rpt.activity_id = sa.id
            WHERE sa.projectId = :pid
              AND sa.status = 0
        ", [':pid' => $pid])->queryOne();

        $metrics['physical_progress_pct'] = (float)($prog['physical_progress_pct'] ?? 0);
        $metrics['total_activities']      = (int)($prog['total_activities'] ?? 0);
        $metrics['completed_activities']  = (int)($prog['completed_activities'] ?? 0);

        // ------------------------------------------------------------------
        // SCHEDULE DELAY — critical path only
        // Source: Gantt / performance dashboard critical_status = 'Yes'
        // ------------------------------------------------------------------
        $sched = $db->createCommand("
            SELECT
                COUNT(sa.id) AS critical_count,
                MAX(sa.end_date) AS planned_end_date,
                SUM(CASE
                    WHEN sa.completed_status = 0
                     AND sa.end_date < CURDATE()
                     AND COALESCE(rpt.cumulated_qty, 0) < sa.quantity
                    THEN 1 ELSE 0
                END) AS delayed_critical,
                MAX(CASE
                    WHEN sa.completed_status = 0 AND sa.end_date < CURDATE()
                    THEN DATEDIFF(CURDATE(), sa.end_date) ELSE 0
                END) AS max_delay_days
            FROM scheduleactivities sa
            LEFT JOIN (
                SELECT activity_id, SUM(currentqty) AS cumulated_qty
                FROM schedule_progress_report_log
                GROUP BY activity_id
            ) rpt ON rpt.activity_id = sa.id
            WHERE sa.projectId = :pid
              AND sa.status = 0
              AND sa.critical_status = 'Yes'
        ", [':pid' => $pid])->queryOne();

        $metrics['critical_count']    = (int)($sched['critical_count'] ?? 0);
        $metrics['delayed_critical']  = (int)($sched['delayed_critical'] ?? 0);
        $metrics['max_delay_days']    = (int)($sched['max_delay_days'] ?? 0);
        $metrics['planned_end_date']  = $sched['planned_end_date'] ? strtotime($sched['planned_end_date']) : 0;

        // ------------------------------------------------------------------
        // Write all metrics to cache (upsert)
        // ------------------------------------------------------------------
        foreach ($metrics as $name => $value) {
            $db->createCommand("
                INSERT INTO chatbot_metrics_cache (project_id, metric_name, metric_value, updated_at)
                VALUES (:pid, :name, :val, NOW())
                ON DUPLICATE KEY UPDATE metric_value = :val, updated_at = NOW()
            ", [':pid' => $pid, ':name' => $name, ':val' => $value])->execute();
        }
    }

    /**
     * Write a specific set of metrics directly to cache.
     * Used by actionPerformancedashboard to store exactly what the UI computed.
     */
    public static function writeMetrics($projectId, array $metrics)
    {
        $db  = Yii::$app->db;
        $pid = (int)$projectId;
        foreach ($metrics as $name => $value) {
            $db->createCommand("
                INSERT INTO chatbot_metrics_cache (project_id, metric_name, metric_value, updated_at)
                VALUES (:pid, :name, :val, NOW())
                ON DUPLICATE KEY UPDATE metric_value = :val, updated_at = NOW()
            ", [':pid' => $pid, ':name' => $name, ':val' => (float)$value])->execute();
        }
    }

    /**
     * Refresh cache for ALL active projects. Used by hourly cron.
     */
    public static function refreshAll()
    {
        $db = Yii::$app->db;
        $projects = $db->createCommand(
            "SELECT Project_Id FROM projects WHERE Status = 0"
        )->queryColumn();

        foreach ($projects as $pid) {
            static::refresh($pid);
        }
    }

    /**
     * Read cached metrics for a project as key=>value array.
     * Returns empty array if no cache exists yet.
     */
    public static function get($projectId)
    {
        $rows = Yii::$app->db->createCommand("
            SELECT metric_name, metric_value, updated_at
            FROM chatbot_metrics_cache
            WHERE project_id = :pid
        ", [':pid' => (int)$projectId])->queryAll();

        $result = [];
        foreach ($rows as $r) {
            $result[$r['metric_name']] = [
                'value'      => $r['metric_value'],
                'updated_at' => $r['updated_at'],
            ];
        }
        return $result;
    }
}
