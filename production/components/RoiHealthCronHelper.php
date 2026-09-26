<?php
namespace app\components;

use Yii;

/**
 * Runs check_roi_health for every active project, records the result in
 * roi_health_status_log, and queues an in-app notification (existing
 * notifications table / admin bell — see Controller::getNotifications())
 * only on a status transition or when status is red. Unchanged status
 * (including repeated red/amber) does not re-alert, to avoid alert
 * fatigue. See docs/dashboard-chatbot-roi-health-tool.md section 3,
 * "Trigger points" -> cron-triggered PHP script.
 */
class RoiHealthCronHelper
{
    /**
     * Run the check for all active projects. Used by the hourly/daily cron.
     */
    public static function runAll()
    {
        $db = Yii::$app->db;
        $projects = $db->createCommand(
            "SELECT Project_Id, Name FROM projects WHERE Status = 0"
        )->queryAll();

        $summary = ['checked' => 0, 'alerted' => 0, 'skipped_no_baseline' => 0];

        foreach ($projects as $project) {
            $result = static::runOne((int)$project['Project_Id'], $project['Name']);
            $summary['checked']++;
            if ($result === 'alerted') $summary['alerted']++;
            if ($result === 'no_baseline') $summary['skipped_no_baseline']++;
        }

        return $summary;
    }

    /**
     * Run the check for a single project, update the status log, and
     * queue a notification if warranted. Returns one of:
     * 'alerted' | 'no_change' | 'no_baseline'.
     */
    public static function runOne($projectId, $projectName = null)
    {
        $db = Yii::$app->db;
        $pid = (int)$projectId;

        $chatbotCtrl = new \app\controllers\ChatbotController('chatbot', Yii::$app);
        $health = $chatbotCtrl->checkRoiHealthForProject($pid);

        if (!isset($health['status']) || $health['status'] !== 'ok') {
            // No roi_target_thresholds baseline yet for this project —
            // check_roi_health intentionally refuses to guess thresholds.
            // Nothing to log or alert on.
            return 'no_baseline';
        }

        $newStatus = $health['overall_status'];
        $projectedRoiPct = (float)$health['projected_roi_pct'];
        $now = date('Y-m-d H:i:s');

        $existing = $db->createCommand(
            "SELECT overall_status FROM roi_health_status_log WHERE project_id=:pid",
            [':pid' => $pid]
        )->queryOne();

        $previousStatus = $existing ? $existing['overall_status'] : null;

        if ($existing) {
            $db->createCommand()->update('roi_health_status_log', [
                'overall_status' => $newStatus,
                'projected_roi_pct' => $projectedRoiPct,
                'last_checked_at' => $now,
            ], ['project_id' => $pid])->execute();
        } else {
            $db->createCommand()->insert('roi_health_status_log', [
                'project_id' => $pid,
                'overall_status' => $newStatus,
                'projected_roi_pct' => $projectedRoiPct,
                'last_checked_at' => $now,
            ])->execute();
        }

        // Alert only on a transition (status changed from the last run,
        // including the first-ever run landing on amber/red). A project
        // that stays red across multiple runs does NOT re-alert every
        // time — re-alerting on every tick while stuck at red is exactly
        // the alert fatigue this is meant to avoid.
        $isTransition = ($previousStatus !== $newStatus);
        $shouldAlert = $isTransition && $newStatus !== 'green';

        if (!$shouldAlert) {
            return 'no_change';
        }

        $projectName = $projectName ?: $db->createCommand(
            "SELECT Name FROM projects WHERE Project_Id=:pid", [':pid' => $pid]
        )->queryScalar();

        // Inserted via raw SQL rather than the Notifications ActiveRecord:
        // this runs from a Yii2 console command (no web app / no request
        // context), and Notifications' AuditTrailBehavior throws
        // "Audit module cannot be loaded" outside a web request — the
        // audit module is only registered in config/web.php, not
        // config/console.php. Raw insert matches this codebase's existing
        // convention for console/cron code paths anyway (see
        // MetricsCacheHelper, web/refresh-metrics-cache.php).
        $remarks = $projectName . ' - ROI health is ' . strtoupper($newStatus)
            . ' (projected ROI ' . $projectedRoiPct . '%)';
        $db->createCommand()->insert('notifications', [
            'itemid' => $pid,
            'type' => 'ROI Health',
            'user_id' => 1,
            'assigned_user' => 0,
            'status' => 0,
            'assigned_dep' => 0,
            'remarks' => $remarks,
        ])->execute();

        $db->createCommand()->update('roi_health_status_log', [
            'last_alerted_at' => $now,
        ], ['project_id' => $pid])->execute();

        return 'alerted';
    }
}
