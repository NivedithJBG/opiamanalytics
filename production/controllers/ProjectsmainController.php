<?php

namespace app\controllers;  

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\Projects;
use app\models\UserTabs;
use app\models\EstimateActivityType;
use app\models\WorkgroupsNew;
use app\models\Boq;
use app\models\WorkgroupActivitiesNew;
use app\models\ClientBill;
use app\models\Wbsscheduleitems;
use app\models\Scheduleactivities;
use app\models\ScheduleProgressReport;
use app\models\ActivityRelations;
use app\models\ProgressReport;
use app\models\ScheduleActivityNew;
use app\models\PricingEstimateNew;
use app\models\PricingEstimate;
use app\models\PricingEstimateResourcesNew;
use app\models\ProjuserSelection;
use app\models\ScheduleResource;
use app\models\Resources;
use app\models\ResourceGroup;  
use app\models\Resourcetype;
use app\models\TasksNew;
use app\models\Jobcard;
use app\models\Cart;
use app\models\Invoice;
use app\models\Orders;
use app\models\InvoiceResources;
use app\models\OrderedResource;  
use amnah\yii2\user\models\User;
use app\models\PlaceorderResAudit;
use app\models\PlaceorderStatus;
use app\models\UserProjects;
use app\models\Pricingordereddiesel;
use app\models\PlaceorderRes;
use app\models\ScheduleTaskReport;
use app\models\ScheduleProgressReportLog;
use app\models\IowGroup;
use app\models\Holidays;
use app\models\PurchaseOrderActivities;



class ProjectsmainController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	//public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['login'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    
    public function beforeAction($action) 
    { 
        $this->enableCsrfValidation = false; 
        return parent::beforeAction($action); 
    }

 

    public function actionIndex()
    {
        $user_id = Yii::$app->user->id;
        $user = User::find()->where(['id'=>Yii::$app->user->id])->one();
        if($user->account_type == 'project_performance_only'){
            $this->redirect(array('projectsmain/projectperformance'));
        }

        if(Yii::$app->helper->isMobile() && ($user->account_type == 'perfm_pad_reporting_only') ){
            $this->layout = '@app/views/layouts/main_mobile';

            $connection = Yii::$app->db;
            $sql        = "SELECT * FROM projects WHERE status='0' AND Project_Delete_Status='0' AND favourite=1";
            if (isset($_POST['projectname'])  && $_POST['projectname'] != '')
                $sql    .= "AND Name LIKE '%" . $_POST['projectname'] . "%'";
            $sql        .= " ORDER BY Project_Id ASC, Name ASC";
            $command    = $connection->createCommand($sql);
            $projects   = $command->query()->readAll();

            $seleted_project_id = '';
            if($projuser = ProjuserSelection::find()->where(['userid' => $user_id])->one())
                $seleted_project_id = $projuser->projectid;

            return $this->render('index_mobile', compact('projects', 'seleted_project_id'));
        }
        else
            return $this->render('index');

    }

    public function actionDashboard()
    {
        return $this->render('dashboard');
    }

    public function actionPerformancedashboardview()
    {
        $this->layout = false;
        $uid = Yii::$app->user->Id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        $pid = $projuser ? (int)$projuser->projectid : 0;
        $connection = \Yii::$app->db;

        $project = $pid ? $connection->createCommand(
            "SELECT Name, start_date FROM projects WHERE Project_Id=$pid"
        )->queryOne() : null;

        return $this->render('performancedashboard', [
            'projectName' => $project ? $project['Name'] : 'No Project Selected',
            'projectId'   => $pid,
        ]);
    }

    public function actionPerformancedashboard()
    {
        $uid  = Yii::$app->user->Id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) { return json_encode(['error' => 'No project selected']); }

        $pid = (int)$projuser->projectid;
        $connection = \Yii::$app->db;

        // Project info
        $project = $connection->createCommand(
            "SELECT Name, start_date FROM projects WHERE Project_Id=$pid"
        )->queryOne();
        $proj_start = $project ? $project['start_date'] : date('Y-m-d');

        // All activities for this project
        $activities = $connection->createCommand(
            "SELECT sa.id, sa.name, sa.scheduleitem_id, sa.duration, sa.delay,
                    sa.completed_status, sa.start_date, sa.end_date,
                    sa.actual_start_date, sa.actual_end_date, sa.unit, sa.resource_units,
                    sa.quantity, sa.old_duration, sa.critical_status,
                    DATEDIFF(sa.start_date, '$proj_start') AS padding,
                    COALESCE(pr.report_count, 0)  AS pr_report_count,
                    pr.cumulated_qty,
                    pr.spr_start_date,
                    pr.last_report_date,
                    CASE
                        WHEN pr.cumulated_qty > 0 AND sa.quantity > 0
                             AND pr.spr_start_date IS NOT NULL AND pr.spr_start_date != '0000-00-00'
                        THEN ROUND(
                               (DATEDIFF(pr.last_report_date, pr.spr_start_date) + 1)
                               / pr.cumulated_qty * sa.quantity
                             )
                        ELSE NULL
                    END AS actual_duration,
                    COALESCE(cost.activity_cost, 0) AS activity_cost,
                    COALESCE(cost.unit_cost, 0)    AS unit_cost,
                    COALESCE(ac.actual_cost, 0)    AS actual_cost
             FROM scheduleactivities sa
             LEFT JOIN (
                 SELECT
                     spr.activity_id,
                     COUNT(*)                                        AS report_count,
                     MAX(spr.cumulated_qty)                         AS cumulated_qty,
                     MIN(spr.start_date)                            AS spr_start_date,
                     COALESCE(MAX(sprl.report_date), MAX(spr.updated_at)) AS last_report_date
                 FROM schedule_progress_report spr
                 LEFT JOIN schedule_progress_report_log sprl
                        ON sprl.activity_id = spr.activity_id AND sprl.currentqty > 0
                 GROUP BY spr.activity_id
             ) pr ON pr.activity_id = sa.id
             LEFT JOIN (
                 SELECT pen.activity_Id,
                        COALESCE(pen.activity_qty, 0) * COALESCE(SUM(pern.quantity * pern.rate), 0) AS activity_cost,
                        COALESCE(SUM(pern.quantity * pern.rate), 0) AS unit_cost
                 FROM pricing_estimate_new pen
                 LEFT JOIN pricing_estimate_resources_new pern
                        ON pern.activity_id = pen.activity_Id AND pern.project_id = pen.project_Id AND pern.pricing_status = 0
                 WHERE pen.project_Id = $pid AND pen.pricing_status = 0
                 GROUP BY pen.activity_Id
             ) cost ON cost.activity_Id = sa.activity_id
             LEFT JOIN (
                 SELECT pern.activity_id,
                        SUM(g.GRN_Quantity * por.rate) AS actual_cost
                 FROM pricing_estimate_resources_new pern
                 JOIN purchase_order_resources por ON por.allocation_id = pern.pricing_resourceid
                 JOIN goods_received_note g         ON g.GRN_Purchase_Order = por.order_id
                                                   AND g.GRN_Item = pern.resource_Id
                 JOIN purchase_orders po            ON po.order_id = por.order_id
                 WHERE pern.project_id = $pid
                   AND po.project_id   = $pid
                   AND po.delete_status = 0
                   AND por.delete_status = 0
                 GROUP BY pern.activity_id
             ) ac ON ac.activity_id = sa.id
             LEFT JOIN workgroup_activities_new wa ON wa.id = sa.activity_id
             WHERE sa.projectId=$pid AND sa.status=0
             ORDER BY COALESCE(wa.sortorder, 999999) ASC, sa.id ASC"
        )->queryAll();

        foreach ($activities as &$a) {
            $a['projected_duration'] = $a['duration'];
            // Anchor = earlier of planned start (sa.start_date) and reported start (spr.start_date)
            $plannedStart = (!empty($a['start_date']) && $a['start_date'] !== '0000-00-00') ? $a['start_date'] : '';
            $reportedStart = (!empty($a['spr_start_date']) && $a['spr_start_date'] !== '0000-00-00') ? $a['spr_start_date'] : '';
            $anchorStart = ($plannedStart && $reportedStart) ? min($plannedStart, $reportedStart) : ($reportedStart ?: $plannedStart);
            $a['spr_start_date'] = $anchorStart;
            if ((int)($a['pr_report_count'] ?? 0) > 0
                && $anchorStart
                && !empty($a['last_report_date'])
                && (float)($a['cumulated_qty'] ?? 0) > 0
                && (float)($a['quantity']      ?? 0) > 0)
            {
                $elapsed = max(1, (strtotime($a['last_report_date']) - strtotime($anchorStart)) / 86400);
                $a['projected_duration'] = round(($elapsed / (float)$a['cumulated_qty']) * (float)$a['quantity'], 1);
            }
        }
        unset($a);

        // Project-level duration bar — matches Gantt B./A. Duration columns exactly:
        // B. Duration = span from earliest schedule start to latest schedule end across all WBS items
        // A. Duration = span from earliest actual start to latest actual/projected end
        $dur_row = $connection->createCommand("
            SELECT
                DATEDIFF(MAX(wbs.b_end), MIN(wbs.b_start)) + 1 AS budgeted,
                CASE WHEN MIN(wbs.a_start) IS NOT NULL AND MAX(wbs.a_end) IS NOT NULL
                     THEN DATEDIFF(MAX(wbs.a_end), MIN(wbs.a_start)) + 1
                     ELSE NULL END AS actual,
                MAX(wbs.b_end) AS b_end_date,
                MAX(wbs.a_end) AS a_end_date
            FROM (
                SELECT
                    MIN(sa.actual_start_date) AS b_start,
                    MAX(sa.actual_end_date)   AS b_end,
                    MIN(COALESCE(
                        CASE WHEN sa.start_date IS NOT NULL AND sa.start_date != '0000-00-00'
                                  AND spr.start_date IS NOT NULL AND spr.start_date != '0000-00-00'
                             THEN LEAST(sa.start_date, spr.start_date)
                             WHEN spr.start_date IS NOT NULL AND spr.start_date != '0000-00-00'
                             THEN spr.start_date
                             ELSE sa.start_date END,
                        sa.actual_start_date
                    )) AS a_start,
                    MAX(COALESCE(
                        CASE WHEN rpt.cumulated_qty > 0 AND sa.quantity > 0
                                  AND (
                                      (sa.start_date IS NOT NULL AND sa.start_date != '0000-00-00')
                                      OR (spr.start_date IS NOT NULL AND spr.start_date != '0000-00-00')
                                  )
                             THEN DATE_ADD(
                                      CASE WHEN sa.start_date IS NOT NULL AND sa.start_date != '0000-00-00'
                                                AND spr.start_date IS NOT NULL AND spr.start_date != '0000-00-00'
                                           THEN LEAST(sa.start_date, spr.start_date)
                                           WHEN spr.start_date IS NOT NULL AND spr.start_date != '0000-00-00'
                                           THEN spr.start_date
                                           ELSE sa.start_date END,
                                      INTERVAL GREATEST(0, ROUND(
                                          (DATEDIFF(rpt.last_report_date,
                                              CASE WHEN sa.start_date IS NOT NULL AND sa.start_date != '0000-00-00'
                                                        AND spr.start_date IS NOT NULL AND spr.start_date != '0000-00-00'
                                                   THEN LEAST(sa.start_date, spr.start_date)
                                                   WHEN spr.start_date IS NOT NULL AND spr.start_date != '0000-00-00'
                                                   THEN spr.start_date
                                                   ELSE sa.start_date END
                                          ) + 1)
                                          / rpt.cumulated_qty * sa.quantity
                                      ) - 1) DAY
                                  )
                             ELSE NULL END,
                        sa.actual_end_date
                    )) AS a_end
                FROM wbsscheduleitems w
                JOIN scheduleactivities sa ON sa.scheduleitem_id = w.scheduleitem_id
                    AND sa.status = 0
                    AND sa.actual_start_date IS NOT NULL AND sa.actual_start_date != '0000-00-00'
                LEFT JOIN schedule_progress_report spr ON spr.activity_id = sa.id
                LEFT JOIN (
                    SELECT activity_id,
                           MAX(report_date) AS last_report_date,
                           SUM(currentqty)  AS cumulated_qty
                    FROM schedule_progress_report_log
                    GROUP BY activity_id
                ) AS rpt ON rpt.activity_id = sa.id
                WHERE w.projectId = $pid AND w.status = 0
                GROUP BY w.scheduleitem_id
                HAVING MAX(sa.actual_end_date) IS NOT NULL
            ) AS wbs
        ")->queryOne();
        $proj_budgeted    = max(1, (int)($dur_row['budgeted']  ?? 0));
        $proj_actual      = max(0, (int)($dur_row['actual']    ?? 0));
        $proj_b_end_date  = $dur_row['b_end_date'] ?? '';
        $proj_a_end_date  = $dur_row['a_end_date'] ?? '';

        // Per-activity overrun (projected_duration - old_duration, floored at 0) using the
        // canonical start anchor (earlier of planned start and reported start) — reused by
        // both IOW Groups and IOW Items below so their "delay" matches the per-activity figure
        // shown in the Ongoing/Upcoming bar lists instead of the legacy scheduleactivities.delay
        // column. Two cases, same as toBarItems() on the frontend:
        //   - progress reported:    overrun = projected_duration - old_duration
        //   - no progress reported: overrun = today - planned start (if start date has passed)
        $anchorOverrunExpr = "
            GREATEST(0, ROUND(
                CASE
                    WHEN rpt.cumulated_qty > 0 AND sa.quantity > 0
                         AND (
                             (sa.start_date IS NOT NULL AND sa.start_date != '0000-00-00')
                             OR (spr.start_date IS NOT NULL AND spr.start_date != '0000-00-00')
                         )
                    THEN (
                        (DATEDIFF(rpt.last_report_date,
                            CASE WHEN sa.start_date IS NOT NULL AND sa.start_date != '0000-00-00'
                                      AND spr.start_date IS NOT NULL AND spr.start_date != '0000-00-00'
                                 THEN LEAST(sa.start_date, spr.start_date)
                                 WHEN spr.start_date IS NOT NULL AND spr.start_date != '0000-00-00'
                                 THEN spr.start_date
                                 ELSE sa.start_date END
                        ) + 1) / rpt.cumulated_qty * sa.quantity
                    ) - sa.old_duration
                    WHEN (rpt.cumulated_qty IS NULL OR rpt.cumulated_qty = 0)
                         AND sa.start_date IS NOT NULL AND sa.start_date != '0000-00-00'
                         AND sa.start_date < CURDATE()
                    THEN DATEDIFF(CURDATE(), sa.start_date)
                    ELSE 0
                END
            ))";

        // IOW/Group-level delay: if any critical activity underneath has a delay, surface
        // that (critical path drives the schedule); otherwise fall back to any activity's
        // delay so a non-critical overrun still isn't hidden.
        $iowDelayExpr = "
            CASE WHEN SUM(CASE WHEN sa.critical_status = 'Yes' THEN 1 ELSE 0 END) > 0
                 THEN MAX(CASE WHEN sa.critical_status = 'Yes' THEN $anchorOverrunExpr ELSE NULL END)
                 ELSE MAX($anchorOverrunExpr)
            END";

        // IOW Groups — route through workgroups_new (bridge: iow_groups → workgroups_new → wbsscheduleitems)
        $groups_raw = $connection->createCommand(
            "SELECT g.id, g.name,
                    COALESCE(DATEDIFF(MAX(sa.end_date), MIN(sa.start_date)) + 1, 0) AS scheduled,
                    COALESCE($iowDelayExpr,0) AS delay,
                    MIN(sa.start_date) AS start_date,
                    MAX(sa.end_date) AS end_date,
                    MAX(sa.actual_end_date) AS actual_end_date
             FROM iow_groups g
             JOIN workgroups_new wn ON wn.iowGroupid = g.id
                                    AND wn.Project_Id = $pid AND wn.Status = 0
             LEFT JOIN wbsscheduleitems w  ON w.wbsid = wn.Workgroup_Id AND w.status = 0
             LEFT JOIN scheduleactivities sa ON sa.scheduleitem_id = w.scheduleitem_id
                                             AND sa.projectId = $pid AND sa.status = 0
             LEFT JOIN schedule_progress_report spr ON spr.activity_id = sa.id
             LEFT JOIN (
                 SELECT activity_id, MAX(report_date) AS last_report_date, SUM(currentqty) AS cumulated_qty
                 FROM schedule_progress_report_log
                 GROUP BY activity_id
             ) rpt ON rpt.activity_id = sa.id
             WHERE g.status = 0
             GROUP BY g.id, g.name
             ORDER BY MIN(COALESCE(wn.sortorder, 999999)) ASC, g.id ASC"
        )->queryAll();

        // IOW Items — carry iow_groups.id as group_id via workgroups_new
        $iow_items_raw = $connection->createCommand(
            "SELECT w.scheduleitem_id AS id, w.name, wn.iowGroupid AS group_id,
                    COALESCE(DATEDIFF(MAX(sa.end_date), MIN(sa.start_date)) + 1, 0) AS scheduled,
                    COALESCE($iowDelayExpr,0) AS delay,
                    MIN(sa.start_date) AS start_date,
                    MAX(sa.end_date) AS end_date,
                    MAX(sa.actual_end_date) AS actual_end_date
             FROM wbsscheduleitems w
             JOIN workgroups_new wn ON wn.Workgroup_Id = w.wbsid
                                    AND wn.Project_Id = $pid AND wn.Status = 0
             LEFT JOIN scheduleactivities sa ON sa.scheduleitem_id = w.scheduleitem_id
                                             AND sa.projectId = $pid AND sa.status = 0
             LEFT JOIN schedule_progress_report spr ON spr.activity_id = sa.id
             LEFT JOIN (
                 SELECT activity_id, MAX(report_date) AS last_report_date, SUM(currentqty) AS cumulated_qty
                 FROM schedule_progress_report_log
                 GROUP BY activity_id
             ) rpt ON rpt.activity_id = sa.id
             WHERE w.projectId = $pid AND w.status = 0
             GROUP BY w.scheduleitem_id, w.name, wn.iowGroupid
             ORDER BY COALESCE(wn.sortorder, 999999) ASC, w.scheduleitem_id ASC"
        )->queryAll();

        // Default: first IOW item of first group
        $default_iow_id = null;
        if (!empty($groups_raw)) {
            $first_gid = (int)$groups_raw[0]['id'];
            foreach ($iow_items_raw as $iow) {
                if ((int)$iow['group_id'] === $first_gid) {
                    $default_iow_id = (int)$iow['id'];
                    break;
                }
            }
        }

        // Ongoing / upcoming for default IOW
        $ongoing = $upcoming = [];
        foreach ($activities as $a) {
            if ($default_iow_id && (int)$a['scheduleitem_id'] !== $default_iow_id) continue;
            if ($a['completed_status'] != 0) continue;
            if ((int)($a['pr_report_count'] ?? 0) > 0) {
                $ongoing[]  = $a;
            } else {
                $upcoming[] = $a;
            }
        }
        $ongoing  = array_values($ongoing);
        usort($upcoming, function($a, $b){
            return strcmp($a['start_date'] ?? '', $b['start_date'] ?? '');
        });
        $upcoming = array_values($upcoming);

        // Default KPI: first ongoing activity of default IOW, else first activity
        $kpi_act = !empty($ongoing) ? $ongoing[0] : (!empty($activities) ? $activities[0] : null);
        $kpi = $this->_buildKpi($kpi_act, $pid, $connection);

        return json_encode([
            'error'          => 'No',
            'project_name'   => $project ? $project['Name'] : '',
            'project_bar'    => [
                'budgeted'   => $proj_budgeted,
                'actual'     => $proj_actual,
                'b_end_date' => $proj_b_end_date,
                'a_end_date' => $proj_a_end_date,
            ],
            'iow_groups'     => $groups_raw,
            'iow_items'      => $iow_items_raw,
            'activities'     => $activities,
            'ongoing'        => $ongoing,
            'upcoming'       => $upcoming,
            'kpi'            => $kpi,
            'default_iow_id' => $default_iow_id,
        ]);
    }

    public function actionPerformancedashboardkpi()
    {
        $actid = (int)$_POST['actid'];
        $uid   = Yii::$app->user->Id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) { return json_encode(['error' => 'No project']); }
        $pid = (int)$projuser->projectid;
        $connection = \Yii::$app->db;
        $act = $connection->createCommand(
            "SELECT id, name, duration, old_duration, unit, quantity, completed_status, start_date, actual_start_date, actual_end_date, end_date, resource_units, critical_status
             FROM scheduleactivities WHERE id=$actid AND projectId=$pid"
        )->queryOne();
        return json_encode(['error'=>'No', 'kpi' => $this->_buildKpi($act, $pid, $connection)]);
    }

    private function _buildKpi($act, $pid, $connection)
    {
        if (!$act) return null;
        $actid = (int)$act['id'];

        // Project name
        $proj = $connection->createCommand("SELECT Name FROM projects WHERE Project_Id=$pid")->queryOne();
        $project_name = $proj ? ($proj['Name'] ?? '') : '';

        // schedule_activity_new data
        $san = $connection->createCommand(
            "SELECT progress, cycle_qty, Workhours, Cycles, Resourceunits,
                    cycle_unit_type
             FROM schedule_activity_new WHERE actvity_id=$actid"
        )->queryOne();

        $target_qty = (float)($act['quantity'] ?? 0);
        $unit       = $act['unit'] ?? '';
        $wh         = $san ? (int)$san['Workhours']      : 8;
        $cycles     = $san ? (float)$san['Cycles']       : 1;
        $progress   = $san ? (float)$san['progress']     : 0;

        // Actual qty from progress report
        $pr = $connection->createCommand(
            "SELECT cumulated_qty FROM schedule_progress_report
             WHERE activity_id=$actid ORDER BY updated_at DESC LIMIT 1"
        )->queryOne();
        $actual_qty = $pr ? (float)$pr['cumulated_qty'] : 0;

        // Work done %
        $work_done_pct = ($target_qty > 0) ? round($actual_qty / $target_qty * 100, 1) : $progress;

        // Target productivity = schedule_qty / duration (planned_per_day, computed below)
        // Actual productivity = actual_qty / elapsed days (last_reported_date - act_start_date)
        $actual_prod = 0;

        $target_cycle = ($target_qty > 0)
            ? round((float)($act['old_duration'] ?? 0) / $target_qty, 3) : 0;
        $resource_units = max(1, (float)($act['resource_units'] ?? 1));
        $actual_cycle = 0;

        // Capacity utilisation: used = (elapsed × wh) - cumulated break hours
        $brk = $connection->createCommand(
            "SELECT SUM(break_hour) AS total_break FROM schedule_progress_report_log
             WHERE activity_id=$actid"
        )->queryOne();
        $cum_break = $brk ? (float)$brk['total_break'] : 0;
        $cap_max   = 0;
        $cap_used  = 0;

        // Cause of delay
        $cod_rows = $connection->createCommand(
            "SELECT cd.title, COUNT(strc.id) AS cnt
             FROM schedule_task_report_cause_of_delays strc
             JOIN cause_of_delays cd ON cd.id = strc.cause_of_delay_id
             WHERE strc.activity_id=$actid
             GROUP BY cd.id, cd.title"
        )->queryAll();
        $total_cod = array_sum(array_column($cod_rows, 'cnt'));
        $cause_of_delay = array_map(function($r) use ($total_cod) {
            return [
                'name'  => $r['title'],
                'count' => (int)$r['cnt'],
                'pct'   => $total_cod > 0 ? round($r['cnt']/$total_cod*100) : 0,
            ];
        }, $cod_rows);

        // Resource capacity
        $res_rows = $connection->createCommand(
            "SELECT r.Name AS name, strr.count AS cnt
             FROM schedule_task_report_resources strr
             JOIN resources r ON r.Resource_Id = strr.res_id
             WHERE strr.activity_id=$actid
             ORDER BY strr.count DESC LIMIT 8"
        )->queryAll();

        // Last reported date for target production needle
        $lrd = $connection->createCommand(
            "SELECT MAX(report_date) AS last_date FROM schedule_progress_report_log
             WHERE activity_id=$actid AND currentqty > 0"
        )->queryOne();
        $last_reported_date = ($lrd && !empty($lrd['last_date'])) ? $lrd['last_date'] : '';

        // Activity start anchor = earlier of planned schedule start and the date progress reporting began
        // (covers an initial start delay either way: a late actual start, or a schedule entered after work began)
        $spr = $connection->createCommand(
            "SELECT start_date FROM schedule_progress_report
             WHERE activity_id=$actid LIMIT 1"
        )->queryOne();
        $reported_start = ($spr && !empty($spr['start_date']) && $spr['start_date'] != '0000-00-00')
            ? $spr['start_date'] : '';
        $planned_start = (!empty($act['start_date']) && $act['start_date'] != '0000-00-00')
            ? $act['start_date'] : '';
        $act_start_date = ($planned_start && $reported_start)
            ? min($planned_start, $reported_start)
            : ($reported_start ?: $planned_start);

        $duration   = (float)($act['duration']     ?? 0);
        $b_duration = (float)($act['old_duration'] ?? 0);
        $planned_per_day = ($b_duration > 0 && $target_qty > 0)
            ? round($target_qty / $b_duration, 3) : 0;

        $target_prod = $planned_per_day;
        $elapsed = 0;
        if ($act_start_date && $last_reported_date && $actual_qty > 0) {
            $elapsed = max(1, (strtotime($last_reported_date) - strtotime($act_start_date)) / 86400);
            $actual_prod  = round($actual_qty / $elapsed, 3);
            $actual_cycle = round($elapsed / $actual_qty, 3);
            $cap_max  = round($elapsed * $wh, 2);
            $cap_used = round(max(0, $cap_max - $cum_break), 2);
        }
        $projected_duration = ($actual_qty > 0 && $elapsed > 0)
            ? (int)round($elapsed / $actual_qty * $target_qty)
            : 0;

        // Tasks for Resource Productivity panel
        $sa_act = $connection->createCommand(
            "SELECT activity_id FROM scheduleactivities WHERE id=$actid"
        )->queryOne();
        $wbn_id = $sa_act ? (int)$sa_act['activity_id'] : 0;
        $wbn_row = $wbn_id ? $connection->createCommand(
            "SELECT activity_Id FROM workgroup_activities_new WHERE id=$wbn_id"
        )->queryOne() : null;
        $masterActId = ($wbn_row && $wbn_row['activity_Id']) ? (int)$wbn_row['activity_Id'] : $wbn_id;
        $task_rows = $masterActId ? $connection->createCommand(
            "SELECT at.id AS task_id, at.task_name, at.task_unit,
                    COALESCE(stn.task_productivity, at.productivity, 0) AS productivity,
                    COALESCE(stn.task_qty, 0) AS task_qty,
                    COALESCE(stn.Budgeted_Duration, 0) AS planned_duration
             FROM activity_tasks at
             LEFT JOIN schedule_task_new stn ON stn.task_Id = at.id AND stn.activity_Id = $actid
             WHERE at.activity_id = $masterActId
             ORDER BY at.sort_order ASC"
        )->queryAll() : [];

        // Sum measurement-book "work done" qty per task for this activity (Site Office > Measurement Book)
        $taskMbQty = [];
        $mbRows = $connection->createCommand(
            "SELECT entries FROM wo_measurement_book WHERE project_id=$pid AND sent_status=1"
        )->queryAll();
        foreach ($mbRows as $mbRow) {
            foreach (json_decode($mbRow['entries'] ?? '[]', true) ?: [] as $entry) {
                if ((string)($entry['activity_id'] ?? '') !== (string)$wbn_id) continue;
                foreach ($entry['tasks'] ?? [] as $t) {
                    $tid = (int)($t['task_id'] ?? 0);
                    if (!$tid) continue;
                    $taskMbQty[$tid] = ($taskMbQty[$tid] ?? 0) + (float)($t['work_done'] ?? 0);
                }
            }
        }

        $tasks = array_map(function($t) use ($target_qty, $actual_qty, $elapsed, $taskMbQty) {
            $tqu = (float)$t['task_qty'];
            $actual = ($elapsed > 0 && $actual_qty > 0 && $tqu > 0)
                ? round($actual_qty * $tqu / $elapsed, 3) : 0;
            $mbQty = $taskMbQty[(int)$t['task_id']] ?? 0;
            return [
                'name'             => $t['task_name'],
                'unit'             => $t['task_unit'],
                'val'              => (float)$t['productivity'],
                'actual'           => $actual,
                'qty'              => round($tqu * $target_qty, 3),
                'planned_duration' => (float)$t['planned_duration'],
                'actual_duration'  => ($elapsed > 0 && $mbQty > 0) ? round($elapsed / $mbQty, 3) : 0,
            ];
        }, $task_rows);

        return [
            'activity_id'          => $actid,
            'activity_name'        => $act['name'],
            'work_done_pct'        => $work_done_pct,
            'target_qty'           => $target_qty,
            'actual_qty'           => $actual_qty,
            'unit'                 => $unit,
            'duration'             => $duration,
            'b_duration'           => $b_duration,
            'act_start_date'       => $act_start_date,
            'last_reported_date'   => $last_reported_date,
            'planned_per_day'      => $planned_per_day,
            'resource_units'       => $resource_units,
            'target_productivity'  => $target_prod,
            'actual_productivity'  => $actual_prod,
            'wh'                   => $wh,
            'target_cycle_time'    => $target_cycle,
            'actual_cycle_time'    => $actual_cycle,
            'cap_max'              => $cap_max,
            'cap_used'             => $cap_used,
            'cause_of_delay'       => $cause_of_delay,
            'resources'            => $res_rows,
            'tasks'                => $tasks,
            'elapsed'              => (int)round($elapsed),
            'projected_duration'   => $projected_duration,
            'planned_end_date'     => ($act['end_date'] ?? ''),
            'critical'             => (($act['critical_status'] ?? '') === 'Yes'),
            'project_name'         => $project_name,
        ];
    }

    public function actionCostdashboardactivity()
    {
        $uid      = Yii::$app->user->Id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return json_encode(['error' => 'No project selected', 'items' => []]);

        $pid   = (int)$projuser->projectid;
        $actid = (int)($_POST['actid'] ?? 0);
        if (!$actid) return json_encode(['error' => 'No activity', 'items' => []]);

        $db = \Yii::$app->db;

        $sa = $db->createCommand(
            "SELECT name, activity_id, quantity, unit FROM scheduleactivities WHERE id = :actid AND projectId = :pid",
            [':actid' => $actid, ':pid' => $pid]
        )->queryOne();
        if (!$sa) return json_encode(['error' => 'Not found', 'items' => []]);

        $wbId = (int)$sa['activity_id'];

        // Activity quantity from estimate
        $actQty = 0;
        if ($wbId) {
            $actQty = (float)($db->createCommand(
                "SELECT COALESCE(activity_qty, 0) FROM pricing_estimate_new
                 WHERE activity_Id = :wid AND project_Id = :pid AND pricing_status = 0 LIMIT 1",
                [':wid' => $wbId, ':pid' => $pid]
            )->queryScalar() ?: 0);
        }

        // Last reported quantity from progress reporting
        $lastQty = (float)($db->createCommand(
            "SELECT COALESCE(cumulated_qty, 0) FROM schedule_progress_report
             WHERE activity_id = :actid ORDER BY updated_at DESC LIMIT 1",
            [':actid' => $actid]
        )->queryScalar() ?: 0);

        $items = [];
        if ($wbId) {
            $rows = $db->createCommand(
                "SELECT pern.pricing_resourceid,
                        COALESCE(pern.display_name, r.Name, rt.Name) AS name,
                        COALESCE(rt.Name, '') AS type_name,
                        pern.rate, pern.quantity,
                        pern.resourcetype_Id AS type_id,
                        COALESCE(r.Unit, '') AS unit,
                        grn_cost.actual_unit_cost,
                        grn_cost.grn_qty,
                        si.stock_at_site,
                        pern.task_ids
                 FROM pricing_estimate_resources_new pern
                 LEFT JOIN resources r     ON r.Resource_Id        = pern.resource_Id
                 LEFT JOIN resourcetype rt ON rt.ResourceType_Id   = pern.resourcetype_Id
                 LEFT JOIN (
                     SELECT por.allocation_id,
                            SUM(CAST(g.GRN_Quantity AS DECIMAL(15,4)) * por.rate)
                                / NULLIF(SUM(CAST(g.GRN_Quantity AS DECIMAL(15,4))), 0) AS actual_unit_cost,
                            SUM(CAST(g.GRN_Quantity AS DECIMAL(15,4))) AS grn_qty
                     FROM purchase_order_resources por
                     JOIN goods_received_note g ON g.GRN_Purchase_Order = por.order_id
                                                AND g.GRN_Item = por.resource_id
                     JOIN purchase_orders po    ON po.order_id = por.order_id
                     WHERE po.project_id = :pid2
                       AND po.delete_status = 0
                       AND por.delete_status = 0
                     GROUP BY por.allocation_id
                 ) grn_cost ON grn_cost.allocation_id = pern.pricing_resourceid
                 LEFT JOIN (
                     SELECT si.pricing_resourceid, si.stock_at_site
                     FROM store_indents si
                     INNER JOIN (
                         SELECT pricing_resourceid, MAX(id) AS max_id
                         FROM store_indents
                         WHERE project_id = :pid3
                         GROUP BY pricing_resourceid
                     ) latest ON latest.pricing_resourceid = si.pricing_resourceid
                              AND si.id = latest.max_id
                 ) si ON si.pricing_resourceid = pern.pricing_resourceid
                 WHERE pern.activity_id = :wid AND pern.project_id = :pid AND pern.pricing_status = 0
                 ORDER BY pern.rate DESC",
                [':wid' => $wbId, ':pid' => $pid, ':pid2' => $pid, ':pid3' => $pid]
            )->queryAll();
            // Build task planned qty per unit from schedule_task_new (Schedule tab → Activity → Task → Qty/Unit)
            $taskQtyMap  = [];  // task_id => task_qty (planned qty per activity unit)
            $taskUnitMap = [];  // task_id => task_unit from activity_tasks
            $stnRows = $db->createCommand(
                "SELECT stn.task_Id AS task_id, stn.task_qty, COALESCE(at.task_unit, '') AS task_unit
                 FROM schedule_task_new stn
                 LEFT JOIN activity_tasks at ON at.id = stn.task_Id
                 WHERE stn.activity_Id = :actid",
                [':actid' => $actid]
            )->queryAll();
            foreach ($stnRows as $stn) {
                $taskQtyMap[(int)$stn['task_id']]  = (float)($stn['task_qty'] ?? 0);
                $taskUnitMap[(int)$stn['task_id']] = $stn['task_unit'] ?? '';
            }

            $mbAmount = 0.0; $mbQty = 0.0; $taskWorkMap = [];
            $taskAmountById   = []; // task_id => SUM(rate × work_done)
            $taskWorkDoneById = []; // task_id => SUM(work_done)
            $mbs = $db->createCommand(
                "SELECT entries FROM wo_measurement_book WHERE project_id=:pid AND sent_status=1 AND delete_status=0",
                [':pid' => $pid]
            )->queryAll();
            foreach ($mbs as $mb) {
                foreach (json_decode($mb['entries'] ?? '[]', true) ?: [] as $entry) {
                    if ((int)($entry['activity_id'] ?? 0) !== $wbId) continue;
                    $mbQty += (float)($entry['qty'] ?? 0);
                    foreach ($entry['tasks'] ?? [] as $task) {
                        $wd  = (float)($task['work_done'] ?? 0);
                        $rt  = (float)($task['rate']      ?? 0);
                        $mbAmount += $rt * $wd;
                        $key = strtolower(trim($task['task_name'] ?? ''));
                        if ($key !== '') {
                            $taskWorkMap[$key] = ($taskWorkMap[$key] ?? 0.0) + $wd;
                        }
                        $tid = (int)($task['task_id'] ?? 0);
                        if ($tid) {
                            $taskAmountById[$tid]   = ($taskAmountById[$tid]   ?? 0.0) + $rt * $wd;
                            $taskWorkDoneById[$tid] = ($taskWorkDoneById[$tid] ?? 0.0) + $wd;
                        }
                    }
                }
            }
            $scActualUnitCost    = $mbQty > 0 ? $mbAmount / $mbQty : null;
            $actualUnitCostTotal = 0.0;
            $schedQty = (float)($sa['quantity'] ?? 0);
            $ratio    = ($schedQty > 0) ? $actQty / $schedQty : 0.0;
            foreach ($rows as $r) {
                $resQty  = (float)$r['quantity'];
                $typeId  = (int)$r['type_id'];
                if ($typeId === 4) {
                    // Actual rate = SUM(work_done_value) / SUM(work_done_qty) from sent MBs
                    $taskIds = array_filter(array_map('intval', explode(',', $r['task_ids'] ?? '')));
                    $actUnit = null;
                    if (!empty($taskIds)) {
                        $totalWdValue = 0.0;
                        $totalWdQty   = 0.0;
                        foreach ($taskIds as $tid) {
                            $totalWdValue += $taskAmountById[$tid]   ?? 0.0;
                            $totalWdQty   += $taskWorkDoneById[$tid] ?? 0.0;
                        }
                        if ($totalWdQty > 0) {
                            $actUnit = $totalWdValue / $totalWdQty;
                        }
                    }
                } else {
                    $actUnit = (in_array($typeId, [2, 6, 7]) && $r['actual_unit_cost'] !== null
                        ? (float)$r['actual_unit_cost']
                        : null);
                }
                $grnQty   = (float)($r['grn_qty'] ?? 0);
                $stockQty = (float)($r['stock_at_site'] ?? 0);
                if ($typeId === 4 && $lastQty > 0) {
                    $nameKey = strtolower(trim($r['name']));
                    $actualResQty = isset($taskWorkMap[$nameKey])
                        ? $taskWorkMap[$nameKey] / $lastQty
                        : null;
                } elseif (in_array($typeId, [2, 6, 7]) && $lastQty > 0 && $grnQty > 0) {
                    $actualResQty = ($grnQty - $stockQty) / $lastQty;
                } else {
                    $actualResQty = null;
                }

                $mappedTaskId   = (int)trim($r['task_ids'] ?? '');
                $taskWorkDone   = ($mappedTaskId > 0 && isset($taskWorkDoneById[$mappedTaskId]))
                    ? (float)$taskWorkDoneById[$mappedTaskId] : null;
                $taskQtyPerUnit = $mappedTaskId > 0 ? ($taskQtyMap[$mappedTaskId] ?? null) : null;

                if ($typeId === 4) {
                    // SC: planned = res_qty (resource page) × task_qty_per_unit (schedule task page)
                    //     actual  = MB work_done / MB reported qty (per schedule unit)
                    $plannedConsumption = $taskQtyPerUnit !== null
                        ? round($resQty * $taskQtyPerUnit, 3)
                        : null;
                    $actualConsumption = ($taskWorkDone !== null && $mbQty > 0)
                        ? round($taskWorkDone / $mbQty, 3)
                        : null;
                } else {
                    // Materials/Consumables/Purchased Inputs/Tools: planned = res_qty × (schedQty / estQty)
                    //                                               actual  = (GRN_qty − stock) / lastQty
                    $plannedConsumption = round($resQty * $ratio, 3);
                    $actualConsumption  = (in_array($typeId, [2, 6, 7]) && $grnQty > 0 && $lastQty > 0)
                        ? round(max(0, $grnQty - $stockQty) / $lastQty, 3)
                        : null;
                }

                // Actual unit cost contribution for this resource (per estimate unit)
                $actualContrib = 0.0;
                if ($typeId === 4 && $lastQty > 0) {
                    $scTaskIds = array_filter(array_map('intval', explode(',', $r['task_ids'] ?? '')));
                    foreach ($scTaskIds as $stid) {
                        $actualContrib += ($taskAmountById[$stid] ?? 0.0) / $lastQty;
                    }
                } elseif (in_array($typeId, [2, 6, 7]) && $lastQty > 0 && $grnQty > 0 && $actUnit !== null) {
                    $consumed       = max(0, $grnQty - $stockQty);
                    $actualContrib  = (float)$actUnit * $consumed / $lastQty;
                }
                $actualUnitCostTotal += $actualContrib;

                $taskUnit = '';
                if ($typeId === 4) {
                    $scTaskIds = array_filter(array_map('intval', explode(',', $r['task_ids'] ?? '')));
                    if (!empty($scTaskIds)) {
                        $taskUnit = $taskUnitMap[reset($scTaskIds)] ?? '';
                    }
                }

                $items[] = [
                    'name'                 => $r['name'],
                    'type_name'            => $r['type_name'],
                    'rate'                 => (float)$r['rate'],
                    'type_id'              => $typeId,
                    'unit'                 => $r['unit'],
                    'task_unit'            => $taskUnit,
                    'res_qty'              => $resQty,
                    'consumption'          => round($lastQty * $resQty, 3),
                    'actual_unit_cost'     => $actUnit,
                    'actual_res_qty'       => $actualResQty,
                    'planned_consumption'  => $plannedConsumption,
                    'actual_consumption'   => $actualConsumption,
                ];
            }
        }

        return json_encode([
            'activity_name'       => $sa['name'],
            'activity_qty'        => $actQty,
            'schedule_qty'        => (float)($sa['quantity'] ?? 0),
            'unit'                => $sa['unit'] ?? '',
            'last_report_qty'     => $lastQty,
            'actual_unit_cost_raw'=> round($actualUnitCostTotal, 4),
            'items'               => $items,
        ]);
    }

    public function actionReports(){

		return $this->render('newreports');

    }
    
    public function actionDashboards(){
        $uid = Yii::$app->user->Id; 
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if($projuser){
            $project = Projects::findOne($projuser->projectid);
        }
        return $this->render('tableau',['project' => $project]);
    }


    public function actionTableaurefresh(){
        return Yii::$app->helper->tableauRefresh(['workBookName' => $_POST['workBookName']]);
    }


    public function actionTableaurefreshprogress(){
        return Yii::$app->helper->tableauRefreshProgress(['jobid' => $_POST['jobid']]);
    }



    function count_digit($number) {
        return strlen($number);
    }
      
    function divider($number_of_digits) {
        $tens="1";
    
        if($number_of_digits>8)
            return 10000000;
        
        while(($number_of_digits-1)>0)
        {
            $tens.="0";
            $number_of_digits--;
        }
        return $tens;
    }
    
    public function actionSearch()
    {
        $connection = Yii::$app->db;
        $sql = "SELECT * FROM projects WHERE status='0' AND Project_Delete_Status='0' AND favourite=1";
        if ($_POST['projectname'] != '')
            $sql .= "AND Name LIKE '%" . $_POST['projectname'] . "%'";
        $sql .= " ORDER BY Project_Id ASC, Name ASC";
        $command = $connection->createCommand($sql);
        $dataReader = $command->query();
        $dataProvider = $dataReader->readAll();
        $datarows = '';
        //$datarows .= '<div class="projects-list-wrpr">';
        $uid = Yii::$app->user->Id; 
        $row = User::findOne($uid);

        if($row->role_id != 0)
        {
            $assignedprojects =  UserProjects::find()->where(['userid'=>$uid])->all();
            if(count($assignedprojects)>0)
            {

                foreach($assignedprojects as $usprojects){
                    $projs = Projects::findOne($usprojects->projectid);

                    $days = $projs->duration;
                    if($projs->duration!=0){

                        //$years = intval($days / 365); 
                        //$days = $days % 365;
                        //$months = intval($days / 30); 
                        //$days = $days % 30;
                        //$prjctdate = $years.' years '.$months.' months '.$days.' days';
                        $prjctdate = $days.' days';

                    }
                    else{
                        //$prjctdate = '0 years 0 months 0 days';
                        $prjctdate = '0 days';
                    }

                    $datarows .= '<div class="col-md-4">';

                    $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
                    if($projuser && $projuser->projectid==$usprojects->projectid){

                            $datarows .= '<div class="projselctid fav-project-wrpr card active" id="projselctid'.$usprojects->projectid.'" data-id='.$usprojects->projectid.'>';
                    }
                    else
                    {
                        $datarows .= '<div class="projselctid fav-project-wrpr card" id="projselctid'.$usprojects->projectid.'" data-id='.$usprojects->projectid.'>';

                    }

          $datarows .= ' <div class="card-body">
                                    <a href="#"><span class="icon-check"></span>'. $projs->Name.'</a>
                                </div>
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="type project-client-name">
                                                <label>Client</label>
                                                <span>'.$projs->client_name.'</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="type">
                                                <label>Project Duration</label>
                                                <span>'.$prjctdate.'</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="type text-right">
                                                <label>Project Value</label>
                                                <span>'.$projs->project_value.'</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
                            <div class="project-doc-upload" style="margin-top:5px;">
                                <div class="input-group input-group-sm">
                                    <input type="file" class="form-control project-doc-input" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" style="font-size:11px;">
                                    <span class="input-group-btn">
                                        <button class="btn btn-success btn-sm project-doc-save" data-id="'.$usprojects->projectid.'" title="Save Documents">
                                            <span class="icon-floppy-disk"></span>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>';



                }

            }else{
                $datarows = '<div class="row"><div class="col-md-12"><div class="text-center">No Featured Projects Found</div></div></div>';
            }

        }else{
            if (count($dataProvider) > 0):
            foreach ($dataProvider AS $key => $data):
                // $sqlst = "SELECT COUNT(*) AS total FROM iowactivities AS a,itemofworks AS b WHERE a.Budgeted_Duration!=0 AND a.End_Duration!=0 AND a.IOW_Id=b.IOW_Id AND b.Project_Id='" . $data['Project_Id'] . "'";
                $sqlst = "SELECT COUNT(*) AS total FROM schedule_activity WHERE progress!=0 AND Project_Id='" . $data['Project_Id'] . "'";
                $command = $connection->createCommand($sqlst);
                $dataReader = $command->query();
                $status = $dataReader->read();
                $changestat = '';
                if ($status['total'] != 0)
                    $changestat = '<a href="' . Yii::$app->request->baseUrl . "/projects/GanttChart/" . $data['Project_Id'] . '" title="Click to view Project Crictical Path"><button type="button" class="btn btn-primary" >CPM</button></a>';
                else
                    $changestat = '<button type="button" class="btn btn-primary" disabled="disabled">CPM</button>';

                if ($status['total'] != 0)
                    $changeiow = '<a href="' . Yii::$app->request->baseUrl . "/dashboard/BarChart/" . $data['Project_Id'] . '" title="Click to view Overhead Status"><button type="button" class="btn btn-primary" >Dashboard</button></a>';
                else
                    $changeiow = '<button title="Click to view  Overhead Status" type="button" class="btn btn-primary" disabled="disabled">Dashboard</button></a>';

                $sqlcost = "SELECT SUM(Price) AS totalcost FROM iowproducts AS a JOIN itemofworks AS b ON a.IOW_Id=b.IOW_Id WHERE b.Project_Id='" . $data['Project_Id'] . "'";

                $command = $connection->createCommand($sqlcost);
                $dataReader = $command->query();
                $totalcost = $dataReader->read();
                if ($totalcost['totalcost'] == '') {
                    $cost = '0';
                } else {
                    $cost = $totalcost['totalcost'];
                }

                $days = $data['duration'];
                if($data['duration']!=0){

                    //$years = intval($days / 365); 
                    //$days = $days % 365;
                    //$months = intval($days / 30); 
                    //$days = $days % 30;
                    //$prjctdate = $years.' years '.$months.' months '.$days.' days';
                    $prjctdate = $days.' days';

                }
                else{
                    //$prjctdate = '0 years 0 months 0 days';
                    $prjctdate = '0 days';
                }

                $num = $data['project_value'];
                $ext="";//thousand,lac, crore
                $number_of_digits = $this->count_digit($num); //this is call :)
                    if($number_of_digits>3)
                {
                    if($number_of_digits%2!=0)
                        $divider= $this->divider($number_of_digits-1);
                    else
                        $divider= $this->divider($number_of_digits);
                }
                else
                    $divider=1;

                $fraction=$num/$divider;
                $fraction=number_format($fraction,2);
                if($number_of_digits==4 ||$number_of_digits==5)
                    $ext="k";
                if($number_of_digits==6 ||$number_of_digits==7)
                    $ext="Lac";
                if($number_of_digits==8 ||$number_of_digits==9)
                    $ext="Cr";
                $provalue =$fraction." ".$ext;

                $uid = Yii::$app->user->Id; 

                $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();



                if($projuser && $projuser->projectid==$data['Project_Id']){
                    $datarows .= '
                        <div class="col-md-4">
                            <div class="projselctid fav-project-wrpr card active" id="projselctid'.$data['Project_Id'].'" data-id='.$data['Project_Id'].'>
                                <div class="card-body">
                                    <a href="#"><span class="icon-check"></span>'. $data['Name'] .'</a>
                                </div>
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="type project-client-name">
                                                <label>Client</label>
                                                <span>'.$data['client_name'].'</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="type">
                                                <label>Project Duration</label>
                                                <span>'.$prjctdate.'</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="type text-right">
                                                <label>Project Value</label>
                                                <span>'.$provalue.'</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="project-doc-upload" style="margin-top:5px;">
                                <div class="input-group input-group-sm">
                                    <input type="file" class="form-control project-doc-input" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" style="font-size:11px;">
                                    <span class="input-group-btn">
                                        <button class="btn btn-success btn-sm project-doc-save" data-id="'.$data['Project_Id'].'" title="Save Documents">
                                            <span class="icon-floppy-disk"></span>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>  ';
                }
                else{
                    $datarows .= '
                        <div class="col-md-4">
                            <div class="projselctid fav-project-wrpr card" id="projselctid'.$data['Project_Id'].'" data-id='.$data['Project_Id'].'>
                                <div class="card-body">
                                    <a href="#"><span class="icon-check"></span>'. $data['Name'] .'</a>
                                </div>
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="type project-client-name">
                                                <label>Client</label>
                                                <span>'.$data['client_name'].'</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="type">
                                                <label>Project Duration</label>
                                                <span>'.$prjctdate.'</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="type text-right">
                                                <label>Project Value</label>
                                                <span>'.$provalue.'</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="project-doc-upload" style="margin-top:5px;">
                                <div class="input-group input-group-sm">
                                    <input type="file" class="form-control project-doc-input" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" style="font-size:11px;">
                                    <span class="input-group-btn">
                                        <button class="btn btn-success btn-sm project-doc-save" data-id="'.$data['Project_Id'].'" title="Save Documents">
                                            <span class="icon-floppy-disk"></span>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>  ';
                }

                /*$datarows .= '<div class="row" id="projectrow' . $data['Project_Id'] . '">
                                    <div class="col-md-8 type">
                                        <label></label>
                                        <span id="projecttext'. $data['Project_Id'] .'"><a href="#" title="Edit">'. $data['Name'] .'</a></span>
                                    </div>
                                    <div class="col-md-4 icon-groups project-tab-buttons">
                                        <a href="#" class="btn btn-primary text-button viewestimate" data-id="'.$data['Project_Id'].'" value="' . $data['Project_Id'] . '" title="Estimate"><span class="icon-calculator1"></span>Estimate</a>
                                        <a href="#" class="btn btn-primary text-button viewwbsSchedule" data-id="' . $data['Project_Id'] . '" value="' . $data['Project_Id'] . '" title="Click to view Work groups under Project '.$data['Name'].' "><span class="icon-time1"></span>Schedule</a>
                                        <a href="#" class="btn btn-primary viewBOQ" data-id="'.$data['Project_Id'].'" value="' . $data['Project_Id'] . '"  title="BOQ"><span class="icon-assignment"></span></a>
                                        <a href="#" class="btn btn-primary viewworkgroups" data-id="' . $data['Project_Id'] . '" value="' . $data['Project_Id'] . '" title="Click to view Work groups under Project '.$data['Name'].' "><span class="icon-chart6"></span></a>
                                        <div class="dropdown icons-group-dropmenu">
                                            <a href="#" class="btn btn-primary nav-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><span class="icon-more_vert"></span></a>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item viewnotes" data-id="'.$data['Project_Id'].'" value="' . $data['Project_Id'] . '" title="Notes" data-toggle="modal" data-target="#notesModel" data-name="' . $data['Name'] . '" href="#"><span class="icon-comment"></span> Notes</a>
                                                <a class="dropdown-item billsquantity" data-id="' . $data['Project_Id'] . '" value="' . $data['Project_Id'] . '" title="Click to view Work groups under Project \'' . $data['Name'] . '\'"" href="#"><span class="icon-note1"></span>Reports</a>
                                                <a class="dropdown-item chart" data-id="' . $data['Project_Id'] . '" value="' . $data['Project_Id'] . '" title="Click to view chart under Project \'' . $data['Name'] . '\'" href="#"><span class="icon-chart2"></span>Chart</a>
                                            </div>
                                        </div>    
                                    </div></div>';*/
            endforeach;
            //$datarows .= '</div>';
        else:
            $datarows = '<div class="row"><div class="col-md-12"><div class="text-center">No Featured Projects Found</div></div></div>';
        endif;
        }
        $arr = array('result' => $datarows, 'error' => 'No');
        return json_encode($arr);
    }

    public function actionProjectperformance()
    {
        $connection = Yii::$app->db;
        $sql = "SELECT * FROM projects WHERE status='0' AND Project_Delete_Status='0' AND favourite=1";
        $sql .= " ORDER BY Project_Id ASC, Name ASC";
        $command = $connection->createCommand($sql);
        $dataReader = $command->query();
        $dataProvider = $dataReader->readAll();
        $datarows = '';
        //$datarows .= '<div class="projects-list-wrpr">';
        $uid = Yii::$app->user->Id; 
        $row = User::findOne($uid);

        if($row->role_id != 0)
        {
            $assignedprojects =  UserProjects::find()->where(['userid'=>$uid])->all();
            if(count($assignedprojects)>0)
            {

                foreach($assignedprojects as $usprojects){
                    $projs = Projects::findOne($usprojects->projectid);

                    $days = $projs->duration;
                    if($projs->duration!=0){

                        //$years = intval($days / 365); 
                        //$days = $days % 365;
                        //$months = intval($days / 30); 
                        //$days = $days % 30;
                        //$prjctdate = $years.' years '.$months.' months '.$days.' days';
                        $prjctdate = $days.' days';

                    }
                    else{
                        //$prjctdate = '0 years 0 months 0 days';
                        $prjctdate = '0 days';
                    }

                    $datarows .= '<div class="col-md-4">';

                    $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
                    if($projuser && $projuser->projectid==$usprojects->projectid){

                            $datarows .= '<div class="projselctid fav-project-wrpr card active" id="projselctid'.$usprojects->projectid.'" data-id='.$usprojects->projectid.'>';
                    }
                    else
                    {
                        $datarows .= '<div class="projselctid fav-project-wrpr card" id="projselctid'.$usprojects->projectid.'" data-id='.$usprojects->projectid.'>';

                    }

                    $datarows .= ' 
                                <div class="card-body">
                                    <a href="#"><span class="icon-check"></span>'. $projs->Name.'</a>
                                </div>
                                <div class="card-footer">                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="type project-client-name">
                                                <label>Client</label>
                                                <span>'.$projs->client_name.'</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="type">
                                                <label>Project Duration</label>
                                                <span>'.$prjctdate.'</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="type text-right">
                                                <label>Project Value</label>
                                                <span>'.$projs->project_value.'</span>
                                            </div>
                                        </div>
                                    </div>                                   
                                </div>
                            </div>
                        </div>';
                }

            }else{
                $datarows = '<div class="row"><div class="col-md-12"><div class="text-center">No Featured Projects Found</div></div></div>';
            }

        }else{
            if (count($dataProvider) > 0):
            foreach ($dataProvider AS $key => $data):
                // $sqlst = "SELECT COUNT(*) AS total FROM iowactivities AS a,itemofworks AS b WHERE a.Budgeted_Duration!=0 AND a.End_Duration!=0 AND a.IOW_Id=b.IOW_Id AND b.Project_Id='" . $data['Project_Id'] . "'";
                $sqlst = "SELECT COUNT(*) AS total FROM schedule_activity WHERE progress!=0 AND Project_Id='" . $data['Project_Id'] . "'";
                $command = $connection->createCommand($sqlst);
                $dataReader = $command->query();
                $status = $dataReader->read();
                $changestat = '';
                if ($status['total'] != 0)
                    $changestat = '<a href="' . Yii::$app->request->baseUrl . "/projects/GanttChart/" . $data['Project_Id'] . '" title="Click to view Project Crictical Path"><button type="button" class="btn btn-primary" >CPM</button></a>';
                else
                    $changestat = '<button type="button" class="btn btn-primary" disabled="disabled">CPM</button>';

                if ($status['total'] != 0)
                    $changeiow = '<a href="' . Yii::$app->request->baseUrl . "/dashboard/BarChart/" . $data['Project_Id'] . '" title="Click to view Overhead Status"><button type="button" class="btn btn-primary" >Dashboard</button></a>';
                else
                    $changeiow = '<button title="Click to view  Overhead Status" type="button" class="btn btn-primary" disabled="disabled">Dashboard</button></a>';

                $sqlcost = "SELECT SUM(Price) AS totalcost FROM iowproducts AS a JOIN itemofworks AS b ON a.IOW_Id=b.IOW_Id WHERE b.Project_Id='" . $data['Project_Id'] . "'";

                $command = $connection->createCommand($sqlcost);
                $dataReader = $command->query();
                $totalcost = $dataReader->read();
                if ($totalcost['totalcost'] == '') {
                    $cost = '0';
                } else {
                    $cost = $totalcost['totalcost'];
                }

                $days = $data['duration'];
                if($data['duration']!=0){

                    //$years = intval($days / 365); 
                    //$days = $days % 365;
                    //$months = intval($days / 30); 
                    //$days = $days % 30;
                    //$prjctdate = $years.' years '.$months.' months '.$days.' days';
                    $prjctdate = $days.' days';

                }
                else{
                    //$prjctdate = '0 years 0 months 0 days';
                    $prjctdate = '0 days';
                }

                $num = $data['project_value'];
                $ext="";//thousand,lac, crore
                $number_of_digits = $this->count_digit($num); //this is call :)
                    if($number_of_digits>3)
                {
                    if($number_of_digits%2!=0)
                        $divider= $this->divider($number_of_digits-1);
                    else
                        $divider= $this->divider($number_of_digits);
                }
                else
                    $divider=1;

                $fraction=$num/$divider;
                $fraction=number_format($fraction,2);
                if($number_of_digits==4 ||$number_of_digits==5)
                    $ext="k";
                if($number_of_digits==6 ||$number_of_digits==7)
                    $ext="Lac";
                if($number_of_digits==8 ||$number_of_digits==9)
                    $ext="Cr";
                $provalue =$fraction." ".$ext;

                $uid = Yii::$app->user->Id; 

                $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();

                $selProjClass = '';
                if($projuser && $projuser->projectid==$data['Project_Id']){
                    $selProjClass = 'active';
                }

                /*$sql="  SELECT DATEDIFF(MAX(end_date), MAX(actual_end_date)) as date_diff, 
                        MIN(actual_start_date) as start_date, MAX(actual_end_date) as end_date 
                        FROM scheduleactivities 
                        WHERE projectId = '".$data['Project_Id']."' AND status=0";*/

                $sql="  SELECT DATEDIFF(MAX(end_date), MAX(actual_end_date)) as date_diff,
                                MIN(start_date) as sched_start_date,
                                MAX(end_date) as sched_end_date,
                                DATEDIFF(MAX(end_date), MIN(start_date)) + 1 as sched_duration
                        FROM scheduleactivities
                        WHERE projectId = '".$data['Project_Id']."' AND status=0";
                $command = $connection->createCommand($sql);
                $dataReader = $command->query();
                $projSchedData = $dataReader->readAll();

                if (!empty($projSchedData[0]['sched_duration']) && $projSchedData[0]['sched_duration'] > 0) {
                    $prjctdate = $projSchedData[0]['sched_duration'] . ' days';
                }
                $schedStartDate = (!empty($projSchedData[0]['sched_start_date']) && $projSchedData[0]['sched_start_date'] !== '0000-00-00') ? $projSchedData[0]['sched_start_date'] : $data['start_date'];
                $schedEndDate   = (!empty($projSchedData[0]['sched_end_date'])   && $projSchedData[0]['sched_end_date']   !== '0000-00-00') ? $projSchedData[0]['sched_end_date']   : $data['end_date'];

                $scheduleStatusBar = 'green-bar';
                $scheduleStatusVal = '';
                if($projSchedData[0]['date_diff'] > 0){
                    $scheduleStatusBar = 'red-bar';
                    $scheduleStatusVal = '<span class="icon-warning3"></span>'.$projSchedData[0]['date_diff'].' Days';
                }


                $totEstAmount  = Yii::$app->helper->getDataFromQuery(' 
                                        SELECT SUM( round((round(pern.rate,2) * round(pern.quantity*pen.activity_qty,2)),2)) as totAmount 
                                        FROM `pricing_estimate_resources_new` as pern 
                                        INNER JOIN pricing_estimate_new as pen ON pen.activity_Id=pern.activity_id AND pen.project_Id = '.$data['Project_Id'].'
                                        INNER JOIN workgroup_activities_new as wact ON wact.id=pern.activity_id AND wact.project_Id = '.$data['Project_Id'].'
                                        WHERE pern.pricing_status = 0 AND pen.pricing_status = 0 AND pern.project_id = '.$data['Project_Id']
                                )['totAmount'];



                $totMatPurAmount  = Yii::$app->helper->getDataFromQuery(' SELECT SUM(a.amount) as totAmount 
                                                                        FROM purchase_order_resources as a 
                                                                        INNER JOIN purchase_orders as c ON c.order_id = a.order_id 
                                                                        WHERE c.project_id='.$data['Project_Id'].' AND c.order_type=1'
                                                                    )['totAmount'];

                $totSubconAmount  = Yii::$app->helper->getDataFromQuery('SELECT SUM((a.no_of_skilled_labour*a.trade_rate)+(a.no_of_unskilled_labour*a.trade_rate)) as totRate 
                                                                          FROM `schedule_task_report_resources` as a 
                                                                          JOIN scheduleactivities as b ON a.activity_id=b.id AND b.projectId='.$data['Project_Id'].' 
                                                                          WHERE a.res_type = 19 AND (a.no_of_skilled_labour!=0 OR a.no_of_unskilled_labour !=0)'
                                                                    )['totRate'];



                $activities      =  WorkgroupActivitiesNew::find()
                                    ->where(['project_Id' => $data['Project_Id']])
                                    ->andWhere(['estimate' => 1])
                                    ->andWhere(['pricing_status' => 0])
                                    ->all();

                $totEstAmount    = 0;
                $totActualAmount = 0;
                if($activities){
                    foreach ($activities as $activity) {
                            
                        $totActAmount  =    Yii::$app->helper->getDataFromQuery(' 
                                                SELECT SUM( round((round(pern.rate,2) * round(pern.quantity*pen.activity_qty,2)),2)) as totAmount 
                                                FROM `pricing_estimate_resources_new` as pern 
                                                INNER JOIN pricing_estimate_new as pen ON pen.activity_Id=pern.activity_id AND pen.project_Id = '.$data['Project_Id'].'
                                                WHERE pern.pricing_status = 0 AND pern.activity_id = '.$activity->id.' AND pen.pricing_status = 0 AND pern.project_id = '.$data['Project_Id']
                                            )['totAmount'];
                        $totEstAmount   +=  $totActAmount;

                        $scheduleAct    = Scheduleactivities::find()->where(['activity_id' => $activity->id])->one();
                        $progReport     = ($scheduleAct) ? (ScheduleProgressReport::find()->where(['activity_id' => $scheduleAct->id])->one()) : '';
                        
                        if($progReport){

                            $cumulated_qty    = $progReport->cumulated_qty;
                            $pricResources    = PricingEstimateResourcesNew::find()
                                                ->where(['activity_id' => $activity->id])
                                                ->andWhere(['pricing_status' => 0])
                                                ->all();
                            if($pricResources){
                                foreach ($pricResources as $pricResource){
                                    $schActId           = $scheduleAct->id;

                                    $pricingEstimateNew = PricingEstimateNew::find()
                                                            ->where(['project_Id'=>$data['Project_Id']])
                                                            ->andWhere(['activity_Id'=>$activity->id])
                                                            ->andWhere(['pricing_status'=>0])
                                                            ->one();
                                    $activity_qty       = $pricingEstimateNew->activity_qty;

                                    //----------Materials / Major Consumables / Purchased Inputs----------------
                                    if($pricResource->resourcetype_Id == 16 || $pricResource->resourcetype_Id == 20 || $pricResource->resourcetype_Id == 21){

                                        /*$totMatPurAmount  = Yii::$app->helper->getDataFromQuery(' SELECT SUM(rate * purchased_qty) as totAmount 
                                                                FROM purchase_order_activities
                                                                WHERE activity_id ='.$schActId.' AND resource_id ='.$pricResource->resource_Id
                                                            )['totAmount'];*/

                                        $matPurUnitRate   = PurchaseOrderActivities::find()
                                                            ->where(['activity_id' => $schActId])
                                                            ->andWhere(['resource_id' => $pricResource->resource_Id])
                                                            ->one();

                                        $resConsumption   = Yii::$app->helper->getDataFromQuery(" SELECT SUM(quantity) AS totalqty 
                                                                    FROM schedule_resource_report_log 
                                                                    WHERE  activity_id=".$schActId."
                                                                    AND  project_id=".$data['Project_Id']."
                                                                    AND resource_id=".$pricResource->resource_Id
                                                                )['totalqty'];
                                        if($matPurUnitRate)
                                            $totMatPurAmount  = $matPurUnitRate->rate * $resConsumption;


                                        $totActualAmount    += ($totMatPurAmount / $cumulated_qty) * $activity_qty;

                                    }

                                    //-----Sub Contractor
                                    if($pricResource->resourcetype_Id == 19){

                                        $labourData     = Yii::$app->helper->getDataFromQuery(" 
                                                                    SELECT SUM(no_of_skilled_labour) AS totalSkilled,
                                                                    SUM(no_of_unskilled_labour) AS totalUnskilled
                                                                    FROM schedule_task_report_resources 
                                                                    WHERE  activity_id=".$schActId."
                                                                    AND res_id=".$pricResource->resource_Id
                                                                );

                                        $skilledRate   = Yii::$app->helper->getDataFromQuery(" 
                                                                    SELECT ((SUM(no_of_skilled_labour) * trade_rate) ) AS totalRate
                                                                    FROM schedule_task_report_resources 
                                                                    WHERE  activity_id=".$schActId."
                                                                    AND res_id=".$pricResource->resource_Id."
                                                                    AND no_of_skilled_labour != 0"
                                                                )['totalRate'];

                                        $unskilledRate  = Yii::$app->helper->getDataFromQuery(" 
                                                                    SELECT ((SUM(no_of_unskilled_labour) * trade_rate)) AS totalRate
                                                                    FROM schedule_task_report_resources 
                                                                    WHERE  activity_id=".$schActId."
                                                                    AND res_id=".$pricResource->resource_Id."
                                                                    AND no_of_unskilled_labour != 0"
                                                                )['totalRate'];

                                        if(($skilledRate || $unskilledRate) && $labourData){
                                            $labourUnitCost = ( ($skilledRate + $unskilledRate) 
                                                                / 
                                                                ($labourData['totalSkilled'] + $labourData['totalUnskilled'])
                                                              );

                                            $startDateQuery =   ScheduleTaskReport::find()
                                                                ->where(['activityid' => $schActId])
                                                                ->orderBy(['task_date'=>SORT_ASC, 'start_time'=>SORT_ASC])
                                                                ->one();
                                            $endDateQuery   =   ScheduleTaskReport::find()
                                                                ->where(['activityid' => $schActId])
                                                                ->orderBy(['task_enddate'=>SORT_DESC, 'end_time'=>SORT_DESC])
                                                                ->one();
                                            $schactnew      =   ScheduleActivityNew::find()->where(['actvity_id' => $schActId])->one();
                                            $workhours      =  ($schactnew) ? $schactnew->Workhours : 8;
                                            

                                            $from_time      = strtotime($startDateQuery->task_date." ".$startDateQuery->start_time); 
                                            $to_time        = strtotime($endDateQuery->task_enddate." ".$endDateQuery->end_time); 
                                            $diff_minutes   = round(abs($from_time - $to_time) / 60,2);
                                            $holidayCount   = Yii::$app->helper->getHolidayCount($startDateQuery->task_date, $endDateQuery->task_enddate, $data['Project_Id']);
                                            $stoppageDaysDeduct = 0;
                                            $totCumDays     = ($diff_minutes/(24*60)) - $stoppageDaysDeduct;
                                            $totCumDuration = (floor($totCumDays) * $workhours) + ceil($diff_minutes%(24*60)/60);
                                            $reptdDays      = ($totCumDuration/$workhours) - $holidayCount;

                                            $cyclesReported = $endDateQuery->current_cycle;

                                            $actualManDays = (($labourData['totalSkilled']/$cyclesReported) + ($labourData['totalUnskilled']/$cyclesReported)) * $reptdDays;

                                            $actualCost     = $labourUnitCost * $actualManDays;

                                            $totActualAmount    += ($actualCost / $cumulated_qty) * $activity_qty;

                                        }

                                    }


                                }
                            }

                        }
                        else{
                            $totActualAmount += $totActAmount;
                        }
                    }
                }





                $costStatusBar = 'green-bar';
                $costStatusVal = '';
                if($totEstAmount < $totActualAmount){
                    $costStatusBar = 'red-bar';
                    $costStatusVal = '<span class="icon-warning3"></span>'.number_format(($totActualAmount - $totEstAmount),2);
                }
                
                $projDuration = $prjctdate;
                if($schedStartDate && $schedEndDate){
                    $projDuration .= '<span style="font-size:11px;"> ('.date('jS M Y', strtotime($schedStartDate)).' - '.date('jS M Y', strtotime($schedEndDate)).')</span>';
                }


                $datarows .= '
                    <div class="col-md-4">
                        <div class="projselctid fav-project-wrpr card '.$selProjClass.'" id="projselctid'.$data['Project_Id'].'" data-id='.$data['Project_Id'].'>
                            <div class="card-body">
                                <a href="#"><span class="icon-check"></span>'. $data['Name'] .'</a>
                            </div>
                            <div class="card-footer ">                                    
                                
                                <div class="row" style="padding:5px 5px 0 5px">
                                    <div class="project-info">
                                        <!--
                                        <div class="col-md-12">
                                            <div class="type project-client-name">
                                                <label style="min-width:95px;">Client Name</label>
                                                <span>: '.$data['client_name'].'</span>
                                            </div>
                                        </div>
                                        -->
                                        <div class="col-md-8">
                                            <div class="type project-spec text-left">
                                                <!--<label>Client Name</label>-->
                                                <span class="project-spec-icon icon-users1" title="Client Name"></span>
                                                <span class="project-spec-text  project-spec-dates"> '.$data['client_name'].'</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="type project-spec text-left">
                                                <!--<label>Project Value</label>-->
                                                <span class="project-spec-icon icon-monetization_on" title="Project Value"></span>
                                                <span class="project-spec-text  project-spec-dates"> '.$provalue.'</span>
                                            </div>
                                        </div>

                                        <!--<div class="col-md-7">
                                            <div class="type project-spec text-left">
                                                <label>Start Date</label>
                                                <span style="font-size:12px;">: '.date('jS M Y', strtotime($data['start_date'])).'</span>
                                            </div>
                                        </div>
                                        <div class="col-md-5" style="padding:0px;">
                                            <div class="type project-spec text-left">
                                                <label  style="min-width:60px;" class="text-left" title="">End Date</label>
                                                <span style="font-size:12px;">: '.date('jS M Y', strtotime($data['end_date'])).'</span>
                                            </div>
                                        </div>
                                        -->

                                        <div class="col-md-8">
                                            <div class="type project-spec text-left">
                                                <span class="project-spec-icon icon-calendar1" title="Start Date - End Date"></span>
                                                <span class="project-spec-text project-spec-dates"> '.date('jS M Y', strtotime($schedStartDate)).' to '.date('jS M Y', strtotime($schedEndDate)).'</span>
                                            </div>
                                        </div>

                                        <div class="col-md-4" >
                                            <div class="type project-spec text-left">
                                                <!--<label style="min-width:60px;" class="text-left">Duration</label>-->
                                                <span class="project-spec-icon icon-schedule"  title="Project Duration"></span>
                                                <span class="project-spec-text  project-spec-dates"> '.$prjctdate.'</span>
                                            </div>
                                        </div>

                                        <!--<div class="col-md-12" ><hr style="margin:0;"></div>-->
                                    </div>
                                </div>


                                <div class="row project-status-bar-container">

                                    <div class="col-md-12 no-padding" style="">
                                        <div class="row type project-status">
                                            <div class="col-md-3" ><label>Schedule</label></div>
                                            <div class="col-md-7" ><div class="status-bar '.$scheduleStatusBar.'">'.$scheduleStatusVal.'</div></div>
                                            <div class="col-md-2 text-right" >
                                                <div class="dashboard-link">
                                                    <a href="#dashboardPopup" class="btn icon-schedule dropdown-toggle" data-toggle="modal" data-target="#dashboardPopup" onclick="getTableauDashboard(\'Performance\', 1, \''. $data['Name'] .'\')"  data-type="Performance"></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 no-padding" style="">
                                        <div class="row type project-status">
                                            <div class="col-md-3" ><label>Cost</label></div>
                                            <div class="col-md-7" ><div class="status-bar '.$costStatusBar.'">'.$costStatusVal.'</div></div>
                                            <div class="col-md-2  text-right">
                                                <div class="dashboard-link">
                                                    <a href="#dashboardPopup" class="btn icon-attach_money dropdown-toggle" data-toggle="modal" data-target="#dashboardPopup" onclick="getTableauDashboard(\'Cost\', 1, \''. $data['Name'] .'\')"  data-type="Cost"></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 no-padding" style="">
                                        <div class="row type project-status">
                                            <div class="col-md-3" ><label>Cashflow</label></div>
                                            <div class="col-md-7" ><div class="status-bar "></div></div>
                                            <div class="col-md-2  text-right">
                                                <div class="dashboard-link">
                                                    <a class="btn icon-compare_arrows" title="View Cashflow Dashboard" href="javascript:void(0);"></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>




                                    <!--<div class="col-md-12" style="">
                                        <div class="type project-status ">
                                            <label>Schedule</label>
                                            <div class="status-bar '.$scheduleStatusBar.'"></div>
                                            <div class="dashboard-link">

                                                <a href="#dashboardPopup" class="btn icon-schedule dropdown-toggle" data-toggle="modal" data-target="#dashboardPopup" onclick="getTableauDashboard(\'Performance\', 1, \''. $data['Name'] .'\')"  data-type="Performance"></a>


                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="type project-status ">
                                            <label>Cost</label>
                                            <div class="status-bar '.$costStatusBar.'"></div>
                                            <div class="dashboard-link">
                                                <a href="#dashboardPopup" class="btn icon-attach_money dropdown-toggle" data-toggle="modal" data-target="#dashboardPopup" onclick="getTableauDashboard(\'Cost\', 1, \''. $data['Name'] .'\')"  data-type="Cost"></a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="type project-status ">
                                            <label>Cashflow</label>
                                            <div class="status-bar"></div>
                                            <div class="dashboard-link">
                                                <a class="btn icon-compare_arrows" title="View Cashflow Dashboard" href="javascript:void(0);"></a>
                                            </div>
                                        </div>
                                    </div>
                                    -->

                                    <!--<div class="col-md-12">
                                        <div class="type project-status ">
                                            <label>Sales</label>
                                            <div class="status-bar"></div>
                                            <div class="dashboard-link">
                                                <a class="btn icon-graph" title="View Sales Dashboard" href="javascript:void(0);"></a>
                                            </div>
                                        </div>
                                    </div>
                                    -->

                                </div>                                   
                            </div>
                        </div>
                    </div>  ';


            endforeach;
            //$datarows .= '</div>';
        else:
            $datarows = '<div class="row"><div class="col-md-12"><div class="text-center">No Featured Projects Found</div></div></div>';
        endif;
        }
        $arr = array('result' => $datarows, 'error' => 'No');
        //return json_encode($arr);

        return $this->render('projectPerformance', array(
          'datarows' => $datarows
        ));
    }

    public function actionUserproject()
    {
        $uid = Yii::$app->user->Id; 
        $projectid = $_POST['prjctid'];
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if($projuser){
            $projuser->projectid = $projectid;
            $projuser->save(false);
        }
        else{
            $model = new ProjuserSelection();
            $model->userid = $uid;
            $model->projectid = $projectid;
            $model->save(false);
        }

        $prjct = Projects::findOne($projectid);

        $datarows = '
                <h4 class="panel-title" id="rprojectlist">
                    <a data-toggle="collapse" data-parent="#accordionoper" href="#collapseproj">
                    <span class="icon-note1"></span>Projects - '.$prjct->Name.'</a>
                </h4>';

        $arr = array('error' => 'No','result'=>$datarows);
        return json_encode($arr);
    }
     public function actionUserprojectmain()
    {
        $uid = Yii::$app->user->Id; 
        $projectid = $_POST['prjctid'];
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if($projuser){
            $projuser->projectid = $projectid;
            $projuser->save(false);
        }
        else{
            $model = new ProjuserSelection();
            $model->userid = $uid;
            $model->projectid = $projectid;
            $model->save(false);
        }

        $prjct = Projects::findOne($projectid);

        $datarows = '
                <h4 class="panel-title" id="projtitle">
                    <a data-toggle="collapse" data-parent="#accordionprojindex" href="#collapseprojindex">
                    <span class="icon-note1"></span>Projects - '.$prjct->Name.'</a>
                </h4>';

        $arr = array('error' => 'No','result'=>$datarows);
        return json_encode($arr);
    }
    public function actionUserprojectprocu()
    {
        $uid = Yii::$app->user->Id; 
        $projectid = $_POST['prjctid'];
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if($projuser){
            $projuser->projectid = $projectid;
            $projuser->save(false);
        }
        else{
            $model = new ProjuserSelection();
            $model->userid = $uid;
            $model->projectid = $projectid;
            $model->save(false);
        }

        $prjct = Projects::findOne($projectid);

        $datarows = '
                <h4 class="panel-title" id="procuprojectlist">
                    <a data-toggle="collapse" data-parent="#accordionindex" href="#collapseprojs">
                    <span class="icon-note1"></span>Projects - '.$prjct->Name.'</a>
                </h4>';

        $arr = array('error' => 'No','result'=>$datarows);
        return json_encode($arr);
    }

    public function actionNotes()
    {
        $connection = Yii::$app->db;
        $sql = "SELECT notes FROM notes WHERE projectID='" . $_POST['id'] . "' ";
        $command = $connection->createCommand($sql);
        $dataReader = $command->query();
        $notes = $dataReader->read();
        if(!empty($notes)){
            if ($notes['notes'] == ''):
                $notestext = '<textarea rows="8" cols="50" rows="100" name="notes" placeholder="Notes" id="notes" class="form-control notes" style="height: 200%" ></textarea>';

            else:
                $notestext = '<textarea rows="25" id="notes"  name="notes" placeholder="Notes" id="methedology" class="form-control methedology" style="height: 200%" >' . $notes['notes'] . ' </textarea>';

            endif;
        }else{
            $notestext = '<textarea rows="8" name="notes" placeholder="Notes" id="notes" class="form-control notes" style="height: 200%" ></textarea>';
        }
        $arr = array('result' => $notestext, 'project' => Projects::findOne($_POST['id'])->Name, 'error' => 'No');
        return json_encode($arr);
    }

    public function actionSavenotes()
    {
        $connection = Yii::$app->db;
        $sql = "SELECT notes FROM notes WHERE projectID='" . $_POST['currentproj'] . "' ";
        $command = $connection->createCommand($sql);
        $dataReader = $command->query();
        $notes = $dataReader->read();
        if ($notes['notes'] == ''):
            $sql = "INSERT INTO notes (projectID,notes) VALUES('" . $_POST['currentproj'] . "','" . $_POST['notes'] . "')";
            $command = $connection->createCommand($sql);
            $dataReader = $command->query();
        else:
            $sql = "UPDATE notes SET notes='".$_POST['notes']."' WHERE projectID='".$_POST['currentproj']."' ";
            $command = $connection->createCommand($sql);
            $dataReader = $command->query();
        endif;

    }

    public function actionGetname()
    {
        if (isset($_POST['id'])):
            $connection = Yii::$app->db;
            $sql = "SELECT Name FROM projects WHERE Project_Id='" . $_POST['id'] . "'";
            $command = $connection->createCommand($sql);
            $dataReader = $command->query();
            $data = $dataReader->read();
            return $data['Name'];
        endif;
    }

     public function actionGetnames()
    {
        if (isset($_POST['id'])):
            $connection = Yii::$app->db;
            $sql = "SELECT Name FROM projects WHERE Project_Id='" . $_POST['id'] . "'";
            $command = $connection->createCommand($sql);
            $dataReader = $command->query();
            $data = $dataReader->read();
            return $data['Name'];
        endif;
    }

	public function actionBoqsearch()
	{
		$connection = Yii::$app->db;
		$uid = Yii::$app->user->Id; 
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if($projuser){
    		$sql = "SELECT a.wbs,b.sortorder FROM boq AS a INNER JOIN workgroups_new AS b ON a.wbs=b.Workgroup_Id WHERE a.projectid='".$projuser->projectid."' GROUP BY a.wbs ORDER BY a.slno ASC";
    		//echo $sql;exit;
    		$command = $connection->createCommand($sql);
    		$dataReader = $command->query();
    		$dataProvider = $dataReader->readAll();
            $datarows = '';
    		if (count($dataProvider) > 0):
    			$projecttot=0;
    			foreach ($dataProvider AS $key => $data):
    				$wbs=WorkgroupsNew::findOne($data['wbs']);
    				$boqitems=Boq::find()->where(['wbs' => $data['wbs']])->orderBy(['slno' => SORT_ASC])->all();
    		        if($boqitems[0]['slno']!=0){
    				 $array = $boqitems[0]['slno'];
    				 $myArray = explode('.', $array);
    				 $slno1 = $myArray[0];
    				}
    		        else{
    				 $slno1 = '';
    				}
    				if($boqitems[0]['iowname']!='' && $boqitems[0]['iowname']!=$wbs['Name']){
    					$iowname = $boqitems[0]['iowname'];
    				}
    				else{
    					$iowname = $wbs['Name'];
    				}
    		        $datarows.='<tr>
                    <td colspan="7" style="font-weight: bold;"><span></span> '.$iowname.'</td>
                    </tr>';
    				$itemstot=0;
    				$countboq = count($boqitems);
    				$countNow = 0;
    				foreach($boqitems AS $boqitem):
    					$amount=$boqitem['quantity'] * $boqitem['rate'];
    					//$total=$total + $amount;
    		            if($boqitem['slno']!=0){
    					 $slno = $boqitem['slno'];
    					}
    		            else{
    					 $slno = '';
    					}
    		            $activity = $boqitem['activity'];
    		            $iowid=$boqitem['wbs'];
    					$wbsactivities=WorkgroupActivitiesNew::find()->where(['wbs_id' => $iowid])->andWhere(['pricing_status' =>0])->orderBy(['wbs_id'=> SORT_ASC])->all();
    					if(count($wbsactivities)>0):
    						$datarows1="<option value='0'>Select Activity</option>";
    						foreach($wbsactivities AS $key=>$wbsactivity):
    		                 if($wbsactivity->id==$boqitem['activity']){
    						 $selected = 'selected';
    						 }
    		                 else{
    						 $selected = '';
    						 }
    						 $datarows1.="<option value='".$wbsactivity->id."' $selected>$wbsactivity->activity_Name</option>";
    						endforeach;
    					endif;
    					$workgroupact=WorkgroupActivitiesNew::find()->where(['id' => $boqitem['activity']])->andWhere(['pricing_status' =>0])->one();
    					$datarows.="<tr id='boqrow".$boqitem['boq_id']."'>
    								<td>
    								<span id='slnotext".$boqitem['boq_id']."'>".$slno."</span>
    								<input style='display:none;' class='form-control editslno' type='text' id='editslno".$boqitem['boq_id']."' value='".$slno."' placeholder='Sl No'>
    								</td>
    								<!--<td>
    								<span id='activity".$boqitem['boq_id']."'></span>
    								<select style='display:none;' class='form-control editactivity' id='editactivity".$boqitem['boq_id']."' ></select> 
    								<span class='error'></span>
    								</td>-->
    								<td>
    								<span id='itemtext".$boqitem['boq_id']."'>".$boqitem['item']."</span>
    								<input style='display:none;' class='form-control edititemname' type='text' id='edititemname".$boqitem['boq_id']."' value='".$boqitem['item']."'>
    								<span class='error'></span></td>
    								<td colspan='1'><span id='unittext".$boqitem['boq_id']."'>".$boqitem['unit']."</span>
    								<input style='display:none;' class='form-control editunitname' type='text' id='editunitname".$boqitem['boq_id']."' value='".$boqitem['unit']."'>
    								<span class='error'></span></td>
    								<td><span id='quantitytext".$boqitem['boq_id']."'>".$boqitem['quantity']."</span>
    								<input style='display:none;' class='form-control editquantityname' type='text' id='editquantityname".$boqitem['boq_id']."' value='".$boqitem['quantity']."'>
    								<span class='error'></span></td>
    								<td style='text-align: right !important;'><span id='ratetext".$boqitem['boq_id']."'>".number_format($boqitem['rate'], 2)."</span>
    								<input style='display:none;' class='form-control editratename' type='text' id='editratename".$boqitem['boq_id']."' value='".$boqitem['rate']."'>
    								<span class='error'></span></td>
    								<td style='text-align: right !important;'>".number_format($amount, 2)."</td>
                                    <td><a class='btn btn-primary icon-pencil editboqbutton' style='border-radius:30px;' data-v='".$boqitem['boq_id']."' value='".$boqitem['boq_id']."' id='editboqbutton".$boqitem['boq_id']."' title='Edit BOQ' href='javascript:void(0)'></a>
                                    <a class='btn btn-primary icon-save  saveboqbutton' style='border-radius:30px;display:none;' data-v='".$boqitem['boq_id']."' title='Save BOQ' value='".$boqitem['boq_id']."' id='saveboqbutton".$boqitem['boq_id']."' href='javascript:void(0)'></a>
    								</td>
    								<!--<td><button type='button' class='btn btn-primary deleteboqbutton' value='" . $boqitem['boq_id'] . "' id='deleteboqbutton" . $boqitem['boq_id'] . "' title='Delete BOQ'><span class='glyphicon glyphicon-trash'></span></button></td>-->
    								</tr>";
    					$itemstot=$itemstot + $amount;
    					
                    endforeach;
                    $datarows.="<tr><th></th><th>Total</th><th colspan='3'></th><th style='text-align: right !important;'>".number_format($itemstot, 2)."</th><th></th></tr>";
    				$projecttot=$projecttot + $itemstot;
    				
    			endforeach;
    			$datarows.="<tr><th>Project Total</th><th colspan='4'></th><th style='text-align: right !important;'>".number_format($projecttot, 2)."</th><th></th></tr>";
    		else:
    			$datarows = '<tr><td colspan="7" style="text-align: center;">No BOQ Found</td></tr>';
    		endif;
    		$print="<a href='".Yii::$app->request->baseUrl."/ProjectPricing/Export/".$projuser->projectid."' target='_blank'><button type='button' class='btn btn-primary small75 pull-right' title='Export'>Export</button></a>";
    		$import ='<a href="#" class="btn btn-primary addForm" id="boqimport" data-id="'.$projuser->projectid.'" title="Import"><span class="icon-download7"></span> Import</a>';

    		$client_bills = ClientBill::find()->where(['projectid' => $projuser->projectid])->andWhere(['status' =>0])->groupBy(['bill_no'])->orderBy(['raise_date'=> SORT_DESC])->all();
    		if(count($client_bills)>0){
    			$boqclear='<a href="#" class="btn btn-primary clearboq-btn" id="clearboq" data-id="'.$projuser->projectid.'" title="Clear BOQ"><span class="icon-trashcan" disabled></span> Clear BOQ DD</a>';
    		}
    		else{
    			$boqclear='<a href="#" class="btn btn-primary clearboq-btn" id="clearboq" data-id="'.$projuser->projectid.'" title="Clear BOQ"><span class="icon-trashcan"></span> Clear BOQ</a>';
    		}

    		$arr = array('result' => $datarows,'projectid'=>$projuser->projectid,'print'=>$print,'import'=>$import,'boqclear'=>$boqclear, 'error' => 'No');
    		return json_encode($arr);
        }
        else{
            $datarows = '<tr><td colspan="7" style="text-align: center;">No Project Selected</td></tr>';

            $arr = array('result' => $datarows,'projectid'=>'','print'=>'','import'=>'','boqclear'=>'', 'error' => 'No');
            return json_encode($arr);
        }
	}

	public function actionImport()
	{
		$connection = Yii::$app->db;
		if($_FILES["import_boq_file"]["name"] != '')
		{
			$allowed_extension = array('csv');
 			$file_array = explode(".", $_FILES["import_boq_file"]["name"]);
 			$file_extension = end($file_array);
 			if(in_array($file_extension, $allowed_extension))
 			{
 				
				if (($handle = fopen($_FILES['import_boq_file']['tmp_name'], "r")) !== FALSE) {
					$count=0;
					$values=fgetcsv($handle);

					while (($values = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        //print_r($values[0]);exit;
	            		if(fmod($values[0],1) == 0.00 && is_numeric($values[0])){
	            			//echo 'iow'; exit;
	            			//$wrkgrpname = explode("  ", $values[1]);
	            			$var = $values[0];
	            			$patt = '/^\d+\.{1}\d+$/';
							$resp = preg_match($patt,$var);
                            $wrkgrpname='';
	            			if($resp){
		            			$wrkgrpname = $values[1];
								//$workgroup=WorkgroupsNew::model()->find(array('condition'=>'Name LIKE "'.$wrkgrpname.'" AND Project_Id='.$_POST['projectid'].''));
								$workgroup=WorkgroupsNew::find()->where(['Project_Id' => $_POST['projectid']])->andWhere(['Status' => 0])->andFilterWhere(['like','Name', $wrkgrpname])->one();
		            			if(!$workgroup && $wrkgrpname!=''){
		            				$model=new WorkgroupsNew;
						            $model->Project_Id=$_POST['projectid'];
						            $model->Name=$wrkgrpname;
						            //$model->unit=$_POST['unit'];
						            //$model->quantity=$_POST['quantity'];
						            $model->parent=$_POST['projectid'];
						            $model->itemtype= 'workgroup';
									$model->wbs_estimate_id=0;
						            $model->save(false);
		            			}
		            		}
	            		}

	            		//print_r($values); exit;

	            		if(fmod((float)$values[0], 1) !== 0.00){

							//if($count!='0' & $wrkgrpname[1]!=''):
							//$wrkgrpname = $values[1];//var_dump($wrkgrpname);exit;
                            //echo $wrkgrpname; exit;
            				if($count!='0' && $wrkgrpname!=''):
		            			//$workgroupname=WorkgroupsNew::model()->find(array('condition'=>'Name LIKE "'.$wrkgrpname[1].'" AND Project_Id='.$_POST['projectid'].''));
								//$workgroupname=WorkgroupsNew::model()->find(array('condition'=>'Name LIKE "'.$wrkgrpname.'" AND Project_Id='.$_POST['projectid'].''));
								$workgroupname=WorkgroupsNew::find()->where(['Project_Id' => $_POST['projectid']])->andWhere(['Status' => 0])->where(['LIKE','Name',$wrkgrpname])->one();

								if($workgroupname){

                                    $insert_data = array(
                                    ':wbs'  => $workgroupname->Workgroup_Id,
                                    ':slno'  => $values[0],
                                    ':item'  => $values[1],
                                    ':unit'  => $values[2],
                                    ':quantity'  => str_replace(',', '', $values[3]),
                                    ':rate'  => str_replace(',', '', $values[4]),
                                    ':amount'  => str_replace(',', '', $values[5]),
                                    ':project'=>$_POST['projectid']
                                   );
                                    $workgroupact = WorkgroupActivitiesNew::find()->where(['LIKE','activity_Name',$values[1]])->andWhere(['wbs_id' => $workgroupname->Workgroup_Id])->one();
                                    if(!empty($workgroupact)){
                                        $wg = $workgroupact->id;
                                    }else{
                                        $wg = 0;
                                    }

									//$boq = Boq::model()->find(array('condition'=>'wbs = '.$workgroupname->Workgroup_Id.' AND item LIKE "'.$values[1].'"'));
									//$boq = Boq::find()->where(['wbs' => $workgroupname->Workgroup_Id])->andFilterWhere(['like','item',$values[1]])->one();

                                    $boq = Boq::find()->where(['wbs' => $workgroupname->Workgroup_Id])->andWhere(['like','item',$values[1]])->andWhere(['quantity' => $values[3]])->andWhere(['rate' => $values[4]])->one();

			            			if($boq){
			            				$boq->slno=$values[0];
										$boq->item=$values[1];
										$boq->iowname=$workgroupname->Name;
										$boq->unit=$values[2];
										$boq->quantity=str_replace(',', '', $values[3]);
										$boq->rate=str_replace(',', '', $values[4]);
										$boq->amount=str_replace(',', '', $values[5]);
										$boq->projectid=$_POST['projectid'];
										$boq->wbs=$workgroupname->Workgroup_Id;
										$boq->activity=$wg;
										$boq->save(false);
			            			}
			            			else{
			            				$model=New Boq();
										$model->slno=$values[0];
										$model->item=$values[1];
										$model->iowname=$workgroupname->Name;
										$model->unit=$values[2];
										$model->quantity=str_replace(',', '', $values[3]);
										$model->rate=str_replace(',', '', $values[4]);
										$model->amount=str_replace(',', '', $values[5]);
										$model->projectid=$_POST['projectid'];
										$model->wbs=$workgroupname->Workgroup_Id;
										$model->save(false);
									}

								}
		            		endif;

	            		}
	            		$count++; 

	            		/*if($count!='0' & $values[0]!=''):
	            			$workgroupname=WorkgroupsNew::model()->find(array('condition'=>'Name LIKE "'.$values[0].'" AND Project_Id='.$_POST['projectid'].''));
	            			$insert_data = array(
						    ':wbs'  => $workgroupname->Workgroup_Id,
						    ':slno'  => $values[1],
						    ':item'  => $values[2],
						    ':unit'  => $values[3],
						    ':quantity'  => $values[4],
						    ':rate'  => $values[5],
						    ':project'=>$_POST['projectid']
						   );
	            			//print_r($insert_data);exit;
	            			$workgroupact = WorkgroupActivitiesNew::model()->find(array('condition'=>'activity_Name LIKE "'.$values[2].'" AND wbs_id='.$workgroupname->Workgroup_Id.''));

	            			$boq = Boq::model()->find(array('condition'=>'wbs = '.$workgroupname->Workgroup_Id.' AND activity='.$workgroupact->id.''));

	            			if($boq){
	            				$boq->slno=$values[1];
								$boq->item=$values[2];
								$boq->unit=$values[3];
								$boq->quantity=$values[4];
								$boq->rate=$values[5];
								$boq->projectid=$_POST['projectid'];
								$boq->wbs=$workgroupname->Workgroup_Id;
								$boq->activity=$workgroupact->id;
								$boq->save(false);
	            			}
	            			else{
	            				$model=New Boq();
								$model->slno=$values[1];
								$model->item=$values[2];
								$model->unit=$values[3];
								$model->quantity=$values[4];
								$model->rate=$values[5];
								$model->projectid=$_POST['projectid'];
								$model->wbs=$workgroupname->Workgroup_Id;
								$model->activity=$workgroupact->id;
								$model->save(false);
							}
	            		endif;
	            		$count++; */
	            	}
	            	fclose($handle);
	        	}
		        
 				$message = '<span class="alert alert-success">Data Imported Successfully</span>';
 			}
 			else
 			{
  				$message = '<span class="alert alert-danger">Only .csv file allowed</span>';
 			}
		}	
		else {
			$message = '<span class="alert alert-danger">Please Select File</span>';
		}
		return $message;
	}

	public function actionClearboq()
    {
        $projectid = $_POST['projectid'];

        $connection = Yii::$app->db;

        $sql = "SELECT a.wbs,b.sortorder FROM boq AS a INNER JOIN workgroups_new AS b ON a.wbs=b.Workgroup_Id WHERE a.projectid='".$_POST['projectid']."' GROUP BY a.wbs ORDER BY a.slno ASC";
		//echo $sql;exit;
		$command = $connection->createCommand($sql);
		$dataReader = $command->query();
		$dataProvider = $dataReader->readAll();
		if (count($dataProvider) > 0):
			foreach ($dataProvider AS $key => $data):
				$wbs=WorkgroupsNew::findOne($data['wbs']);
				$boqitems=Boq::find()->where(['wbs' => $data['wbs']])->orderBy(['slno' => SORT_ASC])->all();
				foreach($boqitems AS $boqitem):
					$boqitem->delete(); 
				endforeach;

                $wbs->Status = 1;
                $wbs->save(false);

			endforeach;
		endif;

		$arr = array('error'=>'No');
        return json_encode($arr);
	}
	
	public function actionSearchscheduleitem()
    {
        $uid = Yii::$app->user->Id; 
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        $user=User::find()->where(['id'=>Yii::$app->user->id])->one();
        if($projuser)
        {
            //$scheduleItems = Wbsscheduleitems::find()->where(['projectId' => $projuser->projectid])->andWhere(['status' => 0])->orderBy(['sortorder' => SORT_ASC])->all();

            $connection = Yii::$app->db;

            $sql="SELECT a.scheduleitem_id,a.wbsid,a.name,a.projectId FROM wbsscheduleitems AS a
                INNER JOIN workgroups_new AS b ON  a.wbsid=b.Workgroup_Id
                WHERE  a.projectId='".$projuser->projectid."' AND b.status=0  ORDER BY b.sortorder  ASC,b.Workgroup_Id ASC";
                
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $scheduleItems=$dataReader->readAll();



            $datarows="";
            if($scheduleItems) {
                $datarows.='<div class="col-md-12 scheduleitemheader schdhead">
                <div class="row">
                <div class="col-md-1 hash">
                                <label>#</label>
                            </div>
                            <div class="col-md-7 all">
                            <label>Schedule Item</label>
                            </div>
                            <div class="col-md-4 lin">
                                <label>&nbsp;</label>
                            </div>
                            </div></div>
                ';
                foreach($scheduleItems as $key => $data) {
                    $scheduleactivities = Scheduleactivities::find()->where(['projectId' => $projuser->projectid])->andWhere(['scheduleitem_id'=>$data['scheduleitem_id']])->andWhere(['status'=>0])->all();
                  
                  //if($data['wbsid']==""){
                  if($data['wbsid']){
                    $schdleitmname=$data['name'];
                  }else{
                    $schudleitemname = WorkgroupsNew::find()->where(['Workgroup_Id' => $data['wbsid']])->one();
                    $schdleitmname=$schudleitemname['Name'];
                  }

					$datarows.='<div style="bottom: 15px;" class="col-md-12 datalists scheduleitemcontent" id="scheduleitemrow'.$data['scheduleitem_id'].'" data-id="'.$data['scheduleitem_id'].'">
                    <div class="row  datslis" style="cursor: pointer;" id="scheduleitemrow'.$data['scheduleitem_id'].'" data-id="'.$data['scheduleitem_id'].'">
									<div class="col-md-1">
										<span class="number">'.($key + 1).'</span>
										<input type="hidden" value="'.$data['scheduleitem_id'].'">
									</div>
									<div class="col-md-7 type">
										
										<span class="schedul" id="scheduleitemname'.$data['scheduleitem_id'].'">'.$schdleitmname.'</span>
										<input class="form-control editscheduleitemname" type="text" id="editscheduleitemname'.$data['scheduleitem_id'].'" value="'.$schdleitmname.'" style="display:none;">
										<span class="error"></span>
									</div>
									<div class="col-md-4 icon-groups">
                                        <a class="btn btn-primary text-button listscheduleactivity" value="'.$data['scheduleitem_id'].'" id="listscheduleactivity'.$data['scheduleitem_id'].'" data-v="'.$data['scheduleitem_id'].'" href="#" title="Activities"><span class="icon-directions_run"></span>Activities</a>
                                    ';

					if($user->account_type != 'perfm_pad_reporting_only'){ 					
					   $datarows.='	
										<a class="btn btn-primary icon-pencil editscheduleitemgroupbuttonq" value="'.$data['scheduleitem_id'].'" id="editscheduleitemgroupbutton'.$data['scheduleitem_id'].'" data-v="'.$data['scheduleitem_id'].'" title="Rename Work Group" href="#"></a>
										<a class="btn btn-primary icon-save savewbsscheduleitembutton" value="'.$data['scheduleitem_id'].'" style="display:none;" data-v="'.$data['scheduleitem_id'].'" id="savewbsscheduleitembutton'.$data['scheduleitem_id'].'" title="Save" href="#"></a>
										<a style="display:none;" class="btn btn-danger  icon-trash1 deletewbsscheduleitembutton" value="'.$data['scheduleitem_id'].'" id="deletewbsscheduleitembutton'.$data['scheduleitem_id'].'" data-v="'.$data['scheduleitem_id'].'" title="Delete Work Group" href="#"></a>';
                    }
									

					$datarows.='</div></div></div>';	
                }
            }

			$gantt = '<a target="_blank" class="btn btn-primary text-button listactssganttback" value="' . $projuser->projectid . '" href="' . Yii::$app->request->baseUrl . '/projectsmain/newganttchart?id=' . $projuser->projectid . '#schgantt" title="Click to view Gantt chart"><span class="icon-chart6" style="font-size: 15px;"></span>  Gantt Chart</a>';
                      
            $arr = array('result' => $datarows, 'gantt'=> $gantt,  'projectID'=> $projuser->projectid, 'error'=>'No');
            return json_encode($arr);

        }  
        else{

            $datarows = '<div style="text-align: center;">No Project Selected</div>';

            $arr = array('result' => $datarows, 'gantt'=> '', 'error'=>'No project');
            return json_encode($arr);

        }       
	}
    public function actionUpdateitemlistsort()
    {
        if(isset($_POST['datavalue']))
        {
            foreach($_POST['datavalue'] AS $data)
            {
                $work=Wbsscheduleitems::findOne($data['rowid']);
                $work->sortorder=$data['rowindex'];
                $work->save(false);
            }
        }
    }
	
	public function actionAddwbschedulegroup()
    {        
        $uid = Yii::$app->user->Id; 
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        $isscheduleItemexists = Wbsscheduleitems::find()->where(['LIKE','name',$_POST['scheduleitemname']])->andWhere(['projectId'=>$projuser->projectid])->one();
        if(!$isscheduleItemexists)
        {
            $scheduleItem = New Wbsscheduleitems();
            $scheduleItem->name=$_POST['scheduleitemname'];
            $scheduleItem->schedulegrp_id = 0;
            $scheduleItem->projectId = $projuser->projectid;
            $scheduleItem->save(false);
            $arr = array('Id' => $scheduleItem->scheduleitem_id, 'Name' => $scheduleItem->name, 'error' => 'No');
        }
        else
            $arr = array('Name' => $_POST['scheduleitemname'], 'error' => 'Yes', 'Message'=>'Schedule Item with name "'.$_POST['scheduleitemname'].'" exists' );
        return json_encode($arr);
	}
	
	public function actionUpdatescheduleitem()
    {
        if(isset($_POST['itemid']))
        {
            $model=Wbsscheduleitems::findOne($_POST['itemid']);
            $model->name=$_POST['Itemname'];
            $error=0;
            if($model->save(false))
            {
                /*$modelGroup=Wbsschedulegroups::model()->findByPk($model->schedulegrp_id);
                if(count($modelGroup)>0):
                $modelGroup->name=$_POST['scheduleGroup'];
                if(!$modelGroup->save(false)){
                    $error = 1;
                }
                endif; */
            }
            else
                $error=1;
           
            if($error == 0):
                $arr = array('Id' => $_POST['itemid'], 'itemName' => $model->name,'groupName' =>'','error'=>'No');
                return json_encode($arr);
            else:
                $arr = array('Id' => $_POST['itemid'],'error'=>'Yes','errortext'=>'Can not update now.');
                return json_encode($arr);
            endif;
        }
	}
	
	public function actionDeletescheduleitem()
    {
        if($_POST['itemId']){
            $model=Wbsscheduleitems::findOne($_POST['itemId']);
            if($model)
            {
                $model->status = 1;
                if($model->save(false))
                {

                    $scheduleactivities = Scheduleactivities::find()->where(['projectId' => $model->projectId])->andWhere(['scheduleitem_id'=>$model->scheduleitem_id])->all();

                    foreach($scheduleactivities as $scheduleactivity):

                        $scheduleactivity->status = 1;
                        $scheduleactivity->save(false);

                        if($scheduleactivity->status==1){

                            //$relations = ActivityRelations::model()->findAll(array('condition'=>'precedent_activity ='.$scheduleactivity->id.' OR dependent_activity ='.$scheduleactivity->id.' AND projectId ='.$scheduleactivity->projectId.' AND status =0'));

                            $relations = ActivityRelations::find()->where(['status' => 0])->andWhere(['or',['precedent_activity'=>$scheduleactivity->id],['dependent_activity'=>$scheduleactivity->id]])->andWhere(['projectId'=> $scheduleactivity->projectId])->all();

                            foreach($relations as $relation):

                                $relation->status = 1;
                                $relation->save(false);

                            endforeach;

                        }

                    endforeach;

                    $arr = array('Id' => $_POST['itemId'],'error'=>'No');
                    return json_encode($arr);
                }
                else
                {
                    $arr = array('Id' => $_POST['itemId'],'error'=>'Yes','errortext'=>'Can not delete now.');
                    return json_encode($arr);  
                }
            }
        }
	}

    public function actionCorrectscheduledata()
    {
        $models=Wbsscheduleitems::find()->where(['status' => 1])->all();

        if($models){

            foreach ($models as $key => $model) 
            {

                $scheduleactivities = Scheduleactivities::find()->where(['projectId' => $model->projectId])->andWhere(['scheduleitem_id'=>$model->scheduleitem_id])->all();

                foreach($scheduleactivities as $scheduleactivity):

                    $scheduleactivity->status = 1;
                    $scheduleactivity->save(false);

                    if($scheduleactivity->status==1){

                        //$relations = ActivityRelations::model()->findAll(array('condition'=>'precedent_activity ='.$scheduleactivity->id.' OR dependent_activity ='.$scheduleactivity->id.' AND projectId ='.$scheduleactivity->projectId.' AND status =0'));

                        $relations = ActivityRelations::find()->where(['status' => 0])->andWhere(['or',['precedent_activity'=>$scheduleactivity->id],['dependent_activity'=>$scheduleactivity->id]])->andWhere(['projectId'=> $scheduleactivity->projectId])->all();

                        foreach($relations as $relation):

                            $relation->status = 1;
                            $relation->save(false);

                        endforeach;

                    }

                endforeach;
            }

            $arr = array('error'=>'No');
            return json_encode($arr);
        }
    }
	
	public function actionGetitemname()
    {
        $model=Wbsscheduleitems::findOne($_POST['id']);
        if($model)
        {
            $arr = array('itemName' => $model->name,'error'=>'No');
            return json_encode($arr);
        }
        else
        {
            $arr = array('error'=>'Yes');
            return json_encode($arr);
        }

	}
	
	public function actionListscheduleactivities()
    {
        $uid = Yii::$app->user->Id; 
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        $user = User::find()->where(['id'=>Yii::$app->user->id])->one();

        if($projuser){
            //------ Holidays-----------------------------
            $holiday_arr = $holiday_week_arr = [];
            $holiday_dates = $holiday_weeks = '';
            if($holiday = Holidays::find()->where(['project_id'=>$projuser->projectid])->one()){
                $holiday_dates      = $holiday->dates;
                $holiday_weeks      = $holiday->weeks;
                $holiday_arr        = ($holiday->dates) ? explode(',', $holiday->dates) : '';
                $holiday_week_arr   = ($holiday->weeks != '') ? explode(',', $holiday->weeks) : '';
            }
            //---------------------------------------------

            //$activities=Scheduleactivities::find()->where(['status' => 0])->all();
            $connection = Yii::$app->db;

            $schActWhereCond = '';
            if($user->account_type == 'perfm_pad_reporting_only')
                $schActWhereCond = " AND a.assigned_to='".$uid."'";

            $sql = "SELECT a.reference_activityid,a.id,a.name,a.actual_start_date,a.actual_end_date,
                        a.start_date,a.end_date,a.activity_id,a.process,a.unit,a.old_duration,
                        a.projectId,a.quantity as sch_quantity, a.assigned_to, a.resource_units,
                        IF(a.quantity > 0, a.quantity, COALESCE(e.activity_qty, 0)) as display_qty
                    FROM scheduleactivities AS a
                    LEFT JOIN workgroup_activities_new AS b ON  a.activity_id=b.id
                    LEFT JOIN pricing_estimate_new AS e ON e.activity_Id = a.activity_id AND e.project_Id = a.projectId AND e.pricing_status = 0
                    WHERE a.scheduleitem_id='".$_POST['itemId']."'
                    AND a.projectId='".$projuser->projectid."'
                    AND a.status=0
                    ".$schActWhereCond."
                    ORDER BY b.sortorder ASC, a.id ASC";

            $command = $connection->createCommand($sql);
            $dataReader = $command->query();
            $dataProvider = $dataReader->readAll();

            $datarows='';
            $datarows='<input type="hidden" id="holiday_arr" value="'.$holiday_dates.'">
                        <input type="hidden" id="holiday_week_arr" value="'.$holiday_weeks.'">
                        <style>
                            #scheduleact-wrap{margin:0 -8px;}
                            #scheduleact-table{width:100%;border-collapse:collapse;margin-bottom:0;font-size:14px;table-layout:auto;}
                            #scheduleact-table thead th{background-color:#555;color:#fff;font-weight:bold;padding:10px 12px;text-align:center;border:1px solid #444;white-space:nowrap;}
                            #scheduleact-table thead th:nth-child(2){text-align:left;}
                            #scheduleact-table tbody td{padding:8px 10px;border:1px solid #ddd;vertical-align:middle;text-align:center;white-space:nowrap;}
                            #scheduleact-table tbody td:nth-child(2){text-align:left;white-space:normal;min-width:150px;}
                            #scheduleact-table tbody tr:hover{background-color:#f9f9f9;}
                            #scheduleact-table .form-control{padding:4px 7px;height:30px;font-size:14px;}
                            #scheduleact-table .btn{padding:4px 9px;font-size:14px;}
                            #scheduleact-table tbody td:last-child{padding:5px 8px;white-space:nowrap;}
                            #scheduleact-table tbody td:last-child .btn{padding:4px 9px;font-size:13px;line-height:1.4;}
                            #scheduleact-table tbody td:last-child .hover{display:inline-block;vertical-align:middle;}
                        </style>
                        <div id="scheduleact-wrap"><table id="scheduleact-table" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Activity Name</th>
                                <th>Unit</th>
                                <th>Quantity</th>
                                <th>Duration</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>';
            if(count($dataProvider)>0):
                $totalindex = 0;
                foreach($dataProvider AS $key=>$data):
                   $reportedstyle = ''; // activity editing always allowed regardless of progress reports
                $reportlog = "SELECT SUM(currentqty) AS totalqty FROM schedule_progress_report_log WHERE activity_id='".$data['id']."'";
                $command = $connection->createCommand($reportlog);
                $dataReader = $command->query();
                $totalQty = $dataReader->read();
                if($totalQty['totalqty']>0){
                    $cumulatedQty = $totalQty['totalqty'];
                }
                else{
                    $cumulatedQty = 0;
                }
                if($data['actual_start_date']!='' && $data['actual_end_date']!='' && $data['actual_start_date']!='0000-00-00' && $data['actual_end_date']!='0000-00-00'){
                    $start_date      = date("d-m-Y", strtotime($data['actual_start_date']));
                    $end_date        = date("d-m-Y", strtotime($data['actual_end_date']));
                    $start_date_edit = $data['actual_start_date'];
                    $end_date_edit   = $data['actual_end_date'];
                } else {
                    $currentDate     = Yii::$app->helper->getDateAfterHoliday(date("d-m-Y"), $projuser->projectid);
                    $start_date      = $currentDate;
                    $end_date        = $currentDate;
                    $start_date_edit = $currentDate;
                    $end_date_edit   = $currentDate;
                }
                // Lock start date only when this activity has predecessors (CPM controls when it can start).
                // Having only successors is fine — GetRelationcorrect() cascades date changes downstream.
                $hasPredecessors = ActivityRelations::find()
                    ->where(['status' => 0])
                    ->andWhere(['dependent_activity' => $data['id']])
                    ->exists();
                $start_date_visible = $hasPredecessors ? 'readonly' : '';
                $end_date_visible   = '';
                $duration_visible   = '';

                $duration_visible = 'readonly';

                  $Wbsscheduleitems=Wbsscheduleitems::find()->where(['scheduleitem_id' => $_POST['itemId']])->andWhere(['status' => 0])->one();
                  //$workgroupact = WorkgroupActivitiesNew::model()->findByPk($data['reference_activityid']);
                  $workgroupact = WorkgroupActivitiesNew::find()->where(['id'=> $data['reference_activityid']])->andWhere(['wbs_id' => $Wbsscheduleitems->reference_itemid])->andWhere(['schedule'=> 1])->andWhere(['pricing_status'=> 0])->one();
    			  //$schedulecheck = $workgroupact->schedule;
                  
                  /*if(!empty($workgroupact)){
                    if($workgroupact->process_Id==2 || $workgroupact->process_Id==5):
                        $totalindex++;
                        $sql1 = "SELECT SUM(Budgeted_Duration) AS duration FROM schedule_task_new WHERE activity_Id=".$data['id']." AND status=0 ";
                        $command = $connection->createCommand($sql1);
                        $dataReader = $command->query();
                        $cycletime = $dataReader->read();
                        $task_created = '';
                        if($cycletime['duration']!=0){
                            $task = ScheduleActivityNew::find()->where(['actvity_id'=> $data['id']])->one();
                            $duration = ($cycletime['duration'] * $task['Cycles']) / $task['Resourceunits'];
                            $task_created = 'readonly';
    					}
    					$datarows.='<div class="col-md-12 activitiess activitycontent" id="enggactivities'.$data['id'].'"  data-id="'.$data['id'].'">
                        <div style="padding:0px !important;margin-right:0px;" class="row " id="enggactivities'.$data['id'].'"  data-id="'.$data['id'].'">
    									<div class="col-md-1 nums">
    										<span class="number">'.($totalindex).'</span>
    									</div>
    									<div class="col-md-2 type aname">
    										
    										<span class="activityvertical" id="'.str_replace(' ','',$data['process']).'activityname'.$data['id'].'">'.$data['name'].'</span>
    										<input class="form-control editenggactivityname" style="display:none;" type="text" id="edit'.str_replace(' ','',$data['process']).'activityname'.$data['id'].'" value="'.$data['name'].'">
                                        	<span class="error"></span>
    									</div>
    									<div class="col-md-1 type utss">
    										
    										<span class="unitvertical" id="'.str_replace(' ','',$data['process']).'activityunit'.$data['id'].'">'.$data['unit'].'</span>
    										<input class="form-control editenggactivityunit" style="display:none;" type="text" id="edit'.str_replace(' ','',$data['process']).'activityunit'.$data['id'].'" value="'.$data['unit'].'">
                                        	<span class="error"></span>
    									</div>
    									<div class="col-md-1 type qtyssss">
    										
    										<span class="quantityvertical" id="'.str_replace(' ','',$data['process']).'activityquantity'.$data['id'].'">'.$data['quantity'].'</span>
    										<input type="hidden" id="cumulatedqty'.$data['id'].'" value="'.$cumulatedQty.'">
                                        	<input class="form-control editenggactivityquantity" style="display:none;" type="text" id="edit'.str_replace(' ','',$data['process']).'activityquantity'.$data['id'].'" value="'.$data['quantity'].'">
                                        	<span class="error"></span>
    									</div>
    									<div class="col-md-1 type drtn">
    										
    										<span class="durationvertical" id="'.str_replace(' ','',$data['process']).'activityduration'.$data['id'].'">'.$data['old_duration'].'</span>
    										<input class="form-control editenggactivityduration" style="display:none;" data-id="'.$data['id'].'" type="text" id="edit'.str_replace(' ','',$data['process']).'activityduration'.$data['id'].'" value="'.$data['old_duration'].'" '.$duration_visible.' '.$task_created.'>
                                        	<span class="error"></span>
    									</div>
                                        <div class="col-md-2 type srdte">
                                            
                                            <span class="startvertical" id="'.str_replace(' ','',$data['process']).'activitystartdate'.$data['id'].'">'.$start_date.'</span>
                                            <input class="form-control editactivitystartdate" style="display:none;" data-id="'.$data['id'].'" type="date" id="edit'.str_replace(' ','',$data['process']).'activitystartdate'.$data['id'].'" value="'.$start_date_edit.'" '.$start_date_visible.'>
                                            <span class="error"></span>
                                        </div>
                                        <div class="col-md-2 type">
                                            
                                            <span class="enddatevertical" id="'.str_replace(' ','',$data['process']).'activityenddate'.$data['id'].'">'.$end_date.'</span>
                                            <input class="form-control editactivityenddate" style="display:none;" data-id="'.$data['id'].'" type="date" id="edit'.str_replace(' ','',$data['process']).'activityenddate'.$data['id'].'" value="'.$end_date_edit.'" '.$end_date_visible.' readonly>
                                            <span class="error"></span>
                                        </div>
                                        <div class="col-md-2 icon-groups" style="bottom: 3px;">
                                            <a class="btn btn-primary assignresource icon-dns" value="'. $data['id'] .'" id="assignresource'. $data['id'] .'" data-v="'. $data['id'] .'" data-proid="'. $data['projectId'] .'" href="javascript:void(0);" title="Assign Resources"></a>

    										<button style="width: unset !important;min-width: 0px;" type="button" class="btn btn-primary viewwbsestimatetasks tstbtnn" value="'. $data['id'] .'" id="viewwbsestimatetask' . $data['id'] . '" title="Task">Tasks</button>



    										<a class="btn btn-primary icon-pencil  editscheduleactivitybutton" value="' . $data['id'] . '" id="edit'.str_replace(' ','',$data['process']).'activitybut' . $data['id'] . '" data-type="'.$data['process'].'" title="Edit Activity" href="javascript:void(0);"></a>
    										<a class="btn btn-primary icon-save  savescheduleactivitybutton" value="' . $data['id'] . '" data-type="'.$data['process'].'" id="save'.str_replace(' ','',$data['process']).'activitybutton' . $data['id'] . '" title="Save" style="display:none;" href="javascript:void(0);"></a>
    										<a class="btn btn-danger  icon-trash1 deletescheduleactivity" value="'.$data['id'].'" data-type="'.$data['process'].'" id="deletescheactivitybotton'.$data['id'].'" title="Delete Activity" '.$reportedstyle.' href="javascript:void(0);"></a>
    									</div>
                                        
                                        

    								</div></div>
    					
    					';
                    endif;
                  }
                  elseif(empty($workgroupact) && $data['reference_activityid']==0){*/
                    $totalindex++;
                    $task_created = '';
                    $est_qtyy = ($data['display_qty'] !== null) ? $data['display_qty'] : 0;
                    $display_duration = $data['old_duration'];
                    
    				$datarows.='<tr class="activitycontent" id="enggactivities'.$data['id'].'" data-id="'.$data['id'].'">
    								<td style="text-align:center;">
    									<span class="number">'.($totalindex).'</span>
    								</td>
    								<td>
    									<span id="'.str_replace(' ','',$data['process']).'activityname'.$data['id'].'">'.$data['name'].'</span>
    									<input class="form-control editenggactivityname" style="display:none;" type="text" id="edit'.str_replace(' ','',$data['process']).'activityname'.$data['id'].'" value="'.$data['name'].'">
    									<span class="error"></span>
    								</td>
    								<td>
    									<span id="'.str_replace(' ','',$data['process']).'activityunit'.$data['id'].'">'.$data['unit'].'</span>
    									<input class="form-control editenggactivityunit" style="display:none;" type="text" id="edit'.str_replace(' ','',$data['process']).'activityunit'.$data['id'].'" value="'.$data['unit'].'">
    									<span class="error"></span>
    								</td>
    								<td>
    									<span id="'.str_replace(' ','',$data['process']).'activityquantity'.$data['id'].'">'.$est_qtyy.'</span>
    									<input type="hidden" id="cumulatedqty'.$data['id'].'" value="'.$cumulatedQty.'">
    									<input class="form-control editenggactivityquantity" style="display:none;" type="text" id="edit'.str_replace(' ','',$data['process']).'activityquantity'.$data['id'].'" value="'.$est_qtyy.'" data-original="'.$est_qtyy.'">
    									<span class="error"></span>
    								</td>
    								<td>
    									<span id="'.str_replace(' ','',$data['process']).'activityduration'.$data['id'].'">'.$display_duration.'</span>
    									<input class="form-control editenggactivityduration" style="display:none;" data-id="'.$data['id'].'" type="text" id="edit'.str_replace(' ','',$data['process']).'activityduration'.$data['id'].'" value="'.$display_duration.'" '.$duration_visible.' '.$task_created.'>
    									<span class="error"></span>
    								</td>
    								<td>
    									<span id="'.str_replace(' ','',$data['process']).'activitystartdate'.$data['id'].'">'.$start_date.'</span>
    									<input class="form-control editactivitystartdate holidayAppliedDatepicker" style="display:none;" data-id="'.$data['id'].'" type="text" id="edit'.str_replace(' ','',$data['process']).'activitystartdate'.$data['id'].'" value="'.$start_date_edit.'" '.$start_date_visible.'>
    									<span class="error"></span>
    								</td>
    								<td>
    									<span class="enddatevertical" id="'.str_replace(' ','',$data['process']).'activityenddate'.$data['id'].'">'.$end_date.'</span>
    									<input class="form-control editactivityenddate" style="display:none;" data-id="'.$data['id'].'" type="text" id="edit'.str_replace(' ','',$data['process']).'activityenddate'.$data['id'].'" value="'.$end_date_edit.'" '.$end_date_visible.' readonly>
    									<span class="error"></span>
    								</td>
    								<td style="white-space:nowrap;">
                                        <a class="btn btn-primary  assignresource icon-dns" style="display:none;" value="'. $data['id'] .'" id="assignresource'. $data['id'] .'" data-v="'. $data['id'] .'" data-proid="'. $data['projectId'] .'" href="javascript:void(0);" title="Assign Resources"></a>



                                        
    									<button type="button" class="btn btn-primary viewwbsestimatetasks tstbtnn" data-p="'.$projuser->projectid.'" data-v="' . $data['id'] . '" 
                                        data-q="' . $data['id'] . '"  value="'. $data['id'] .'" id="viewwbsestimatetask' . $data['id'] . '" title="Tasks">Tasks</button>';

                                    if($user->account_type != 'perfm_pad_reporting_only'){

                                        $reportOnlyUsers = User::find()->where(['account_type'=> 'perfm_pad_reporting_only'])->all();

                                        $user_dropdown = '<select class="form-control" name="activity_assign_user" id="activity_assign_user'.$data['id'].'" data-act="'.$data['id'].'" >';
                                        $user_dropdown .= '<option value="">Select User</option>';
                                        if($reportOnlyUsers){
                                            foreach ($reportOnlyUsers as $reportOnlyUser) {
                                                $reportOnlyUserSelected = ($data['assigned_to'] == $reportOnlyUser->id) ? 'Selected' : '';
                                                $user_dropdown .= '<option value="'.$reportOnlyUser->id.'" '.$reportOnlyUserSelected.'>'.$reportOnlyUser->displayName.'</option>';
                                            }
                                        }
                                        $user_dropdown .= '</select>';



    								$datarows.='

                                        <div class="hover" data-tooltip="tooltip_task_report" style="cursor:pointer; ">
                                            <a  class="btn icon-user3 assignActivity hover" data-tooltip="tooltip_task_report" data-v="' . $data['id'] . '" value="'.$data['id'].'" data-type="'.$data['process'].'" id="assignActivity'.$data['id'].'" title="Assign to User"  href="javascript:void(0);"></a>
                                            <div class="tooltiptable" id="tooltip_task_report" style="min-width: 250px;height: auto;padding: 20px !important;right: -90px;">
                                                <div>
                                                    <div style="padding-bottom:10px;"><b>Assign User</b></div>
                                                    <div>
                                                        '.$user_dropdown.'
                                                        <span class="error"></span>
                                                    </div>
                                                    <div id="assignuser_btn_contanier'.$data['id'].'" style="text-align: center; padding-top:10px;">
                                                        <button class="btn btn-primary btn-assignuser" id="btn-assignuser'.$data['id'].'"  style="background-color: #087acf;border-color: #087acf;" data-act="'.$data['id'].'" value="">'.(($data['assigned_to']) ? 'Re-Assign' : 'Assign').'</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>




                                        <a  class="btn btn-primary icon-pencil editscheduleactivitybutton" data-v="' . $data['id'] . '" value="' . $data['id'] . '" id="edit'.str_replace(' ','',$data['process']).'activitybut' . $data['id'] . '" data-type="'.$data['process'].'" title="Edit Activity" href="javascript:void(0);" '.$reportedstyle.'></a>
    									
                                        <a class="btn btn-primary icon-save savescheduleactivitybutton" data-v="' . $data['id'] . '" value="' . $data['id'] . '" data-type="'.$data['process'].'" id="save'.str_replace(' ','',$data['process']).'activitybutton' . $data['id'] . '" title="Save" style="display:none;" href="javascript:void(0);"></a>
                                        
                                        <a  class="btn btn-danger  icon-trash1 deletescheduleactivity" data-v="' . $data['id'] . '" value="'.$data['id'].'" data-type="'.$data['process'].'" id="deletescheactivitybotton'.$data['id'].'" title="Delete Activity" '.$reportedstyle.' href="javascript:void(0);"></a>';
                                    }

                                $datarows.='
    							</td>
    						</tr>

    				';
                  //}
                endforeach;
                $datarows .= '</tbody></table></div>';
            else:
                $datarows .= '<tr><td colspan="9" class="text-center" style="padding:20px;">No Activities Found</td></tr></tbody></table></div>';
            endif;

                $gantt = '<a class="btn btn-primary text-button listactganttback" value="' . $projuser->projectid . '" href="' . Yii::$app->request->baseUrl . '/projectsmain/newganttchart?id=' . $projuser->projectid . '#listgnt" title="Click to view Gantt chart"><span class="icon-chart6"></span>  Gantt Chart</a>';
            /*$gantt = 'href="' . Yii::$app->request->baseUrl . '/projectsmain/newganttchart?id=' . $projuser->projectid . '"';*/

            $arr = array(   'result' => $datarows,
                            'gantt'=> $gantt,
                            'holiday_arr'=> $holiday_arr,
                            'holiday_week_arr'=> $holiday_week_arr,
                            'error'=>'No'
                        );
            return json_encode($arr);

        }
        else{

            $datarows='<div class="row"><div class="col-md-12"><div class="text-center">No Project Selected</div></div></div>';

            $arr = array('result' => $datarows,'gantt'=> '','error'=>'No');
            return json_encode($arr);
        }

    }


  

    public function actionWbsestimatetasklists() 
    {

        $uid = Yii::$app->user->Id; 
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        $user = User::find()->where(['id'=>Yii::$app->user->id])->one();

        if($projuser){
            //$activities=Scheduleactivities::find()->where(['status' => 0])->all();
    
            $activityid = $_POST['wrkactivityid'];
            //$estimate = Scheduleactivities::model()->findByPk($activityid);

            $estimate=Scheduleactivities::find()->where(['id' => $activityid])->andWhere(['projectId'=>$projuser->projectid])->one();

            //print_r($estimate); exit;
            // $wbsactivity=WorkgroupActivitiesNew::model()->findByPk($estimate->reference_activityid);
            $wbsactivity=WorkgroupActivitiesNew::findOne($estimate->reference_activityid); 

            $name = $estimate['name'];
            $unit = $estimate['cycle_unit'];
            $quantity = $estimate['cycle_quantity'];
            $est_qty = $estimate['quantity'];
            $est_unit = $estimate['unit'];
                
            $connection = \Yii::$app->db;
            
            $estActivityId = $estimate['activity_id'];
            // scheduleactivities.activity_id = workgroup_activities_new.id
            // activity_tasks uses workgroup_activities_new.activity_Id — bridge through the table
            $wbnRow = $connection->createCommand(
                "SELECT activity_Id FROM workgroup_activities_new WHERE id='".(int)$estActivityId."'"
            )->queryOne();
            if ($wbnRow && !empty($wbnRow['activity_Id'])) {
                $estActivityId = $wbnRow['activity_Id'];
            }
            $estActivity = $connection->createCommand("SELECT working_hours FROM estimateactivities WHERE activity_id='".$estActivityId."'")->queryOne();
            $defaultWorkHours = !empty($estActivity) ? $estActivity['working_hours'] : 8;
            $sql = "SELECT id AS Id, task_name AS task, task_unit, productivity FROM activity_tasks WHERE activity_id='".$estActivityId."' ORDER BY sort_order ASC";
            $command = $connection->createCommand($sql);
            $dataReader = $command->query();
            $tasks = $dataReader->readAll();
            // $datarows = '';

            //$sqlforactschedule = "SELECT * FROM schedule_activity_new WHERE actvity_id='".$activityid."' AND activity_name='".$name."' ";
            $sqlforactschedule = "SELECT * FROM schedule_activity_new WHERE actvity_id='".$activityid."' ";
            $command1 = $connection->createCommand($sqlforactschedule);
            $dataReader1 = $command1->query();
            $sche_act = $dataReader1->read();
            
            $sch_cycle_fixed_qty = '';
            $sch_cycle_var_qty = '';
            $sch_rep_mothod_daily = '';
            $sch_rep_mothod_weekly = '';
            $sch_cycle_unit_style = '';

            if(!empty($sche_act))
            { 
                $sch_cycle_type     = $sche_act['cycle_type'];
                if($sch_cycle_type == 'fixed_qty')      $sch_cycle_fixed_qty = 'checked';
                if($sch_cycle_type == 'variable_qty')   $sch_cycle_var_qty = 'checked';

                $sch_rep_mothod     = $sche_act['report_method'];
                if($sch_rep_mothod == 'daily'){
                    $sch_rep_mothod_daily = 'checked';
                    $sch_rep_mothod_weekly = '';
                }
                elseif($sch_rep_mothod == 'weekly') {
                    $sch_rep_mothod_weekly = 'checked';
                    $sch_rep_mothod_daily = '';
                }

                //$sch_cycle_unit_style = 'readonly';
            }

            $disabledForm = '';
            if($user->account_type == 'perfm_pad_reporting_only')
                $disabledForm = 'disabled';

            // Pre-compute cycle parameters
            if(!empty($sche_act)) {
                $sch_res             = $sche_act['Resourceunits'];
                $sch_cyc             = $sche_act['Cycles'];
                $sch_work            = $sche_act['Workhours'];
                $sch_cyc_qty         = $sche_act['cycle_qty'];
                $sch_cycle_units     = $sche_act['cycle_units'];
                $sch_cycle_unit_type = $sche_act['cycle_unit_type'];
                $sch_cycle_type_val  = $sche_act['cycle_type'];
                $sch_rep_mothod_val  = $sche_act['report_method'];
            } else {
                $sch_res             = '';
                $sch_cyc             = '1';
                $sch_work            = '';
                $sch_cyc_qty         = $est_qty;
                $sch_cycle_units     = 1;
                $sch_cycle_unit_type = '';
                $sch_cycle_type_val  = 'fixed_qty';
                $sch_rep_mothod_val  = 'daily';
            }

            // Pre-compute task rows HTML and btime
            $btime = 0;
            $atime = 0;
            $taskRowsHtml = '';

            // Qty/Unit formula: SUM(SC res_qty mapped to task) × (estQty / schedQty)
            $estPenRow = $connection->createCommand(
                "SELECT activity_qty FROM pricing_estimate_new WHERE activity_Id=:wid AND project_Id=:pid AND pricing_status=0 LIMIT 1",
                [':wid' => $estimate['activity_id'], ':pid' => $projuser->projectid]
            )->queryOne();
            $estActQtyForTask = $estPenRow ? (float)$estPenRow['activity_qty'] : 0;
            $schedQtyForTask  = (float)($est_qty ?? 0);
            $taskRatio        = $schedQtyForTask > 0 ? $estActQtyForTask / $schedQtyForTask : 0.0;

            if(count($tasks) > 0) {
                foreach($tasks AS $key => $task):
                    $sqlqr = "SELECT Budgeted_Duration,End_Duration,status,task_qty,task_productivity,task_resource_units FROM schedule_task_new WHERE activity_Id='".$activityid."' AND task_Id='".$task['Id']."'";
                    $command1 = $connection->createCommand($sqlqr);
                    $dataReader1 = $command1->query();
                    $sche_tasks = $dataReader1->read();

                    // SC qty/unit = SUM(SC res_qty mapped to this task) × (estQty / schedQty)
                    $scResQty = (float)($connection->createCommand(
                        "SELECT COALESCE(SUM(quantity), 0) FROM pricing_estimate_resources_new
                         WHERE activity_id=:wid AND project_id=:pid AND resourcetype_Id=4 AND pricing_status=0
                           AND FIND_IN_SET(:tid, task_ids)",
                        [':wid' => $estimate['activity_id'], ':tid' => $task['Id'], ':pid' => $projuser->projectid]
                    )->queryScalar() ?: 0);
                    $computedTaskQty = round($scResQty * $taskRatio, 3);

                    $repquery = "SELECT reportid FROM new_report WHERE activity_Id='".$activityid."' AND status=0 AND totalduration!=0";
                    $command = $connection->createCommand($repquery);
                    $dataReader = $command->query();
                    $report = $dataReader->readAll();
                    if(count($report) != 0) {
                        $totalofactual = 0;
                        $count = 0;
                        foreach($report AS $reportid) {
                            $report_id = $reportid['reportid'];
                            $actquery = "SELECT actualduration FROM reporttasks WHERE reportid='".$report_id."' AND taskid='".$task['Id']."'";
                            $command = $connection->createCommand($actquery);
                            $dataReader = $command->query();
                            $actdur = $dataReader->read();
                            $totalofactual += $actdur['actualduration'];
                            $count++;
                        }
                        $averageactulal = substr($totalofactual/$count, 0, 4);
                    } else {
                        $averageactulal = !empty($sche_tasks) ? $sche_tasks['Budgeted_Duration'] : 0;
                    }

                    $buttonrow = '';
                    if($user->account_type != 'perfm_pad_reporting_only') {
                        if($key == 0) {
                            $buttonrow = '<a class="btn btn-primary icon-plus icon_plus" id="tasknewrow" title="Add more Task"></a>';
                        } else {
                            $buttonrow = '<a class="btn btn-danger icon-times taskremoverow" data-id="'.$key.'" id="taskremoverow'.$key.'" title="Remove Task"></a>';
                        }
                    }

                    $savedResUnits = (!empty($sche_tasks) && $sche_tasks['task_resource_units'] > 0) ? $sche_tasks['task_resource_units'] : 1;
                    $taskRowsHtml .= '<tr class="tastrow" id="activityrow'.$key.'">
                        <td><input type="hidden" name="tasknewid[]" value="'.$task['Id'].'">
                            <input type="text" class="form-control taskname_edit" name="taskname[]" value="'.$task['task'].'" '.$disabledForm.'></td>
                        <td><input type="text" class="form-control" value="'.htmlspecialchars($task['task_unit']).'" readonly></td>
                        <td><input type="number" step="0.001" class="form-control task-productivity-val" name="task_productivity_val[]" value="'.(!empty($sche_tasks) && $sche_tasks['task_productivity'] > 0 ? number_format((float)$sche_tasks['task_productivity'], 3, '.', '') : number_format((float)$task['productivity'], 3, '.', '')).'" '.$disabledForm.'></td>
                        <td><input type="number" step="0.001" class="form-control" name="task_qty[]" value="'.$computedTaskQty.'" readonly style="background-color:#f0f0f0;color:#555;cursor:not-allowed;"></td>
                        <td><input type="number" step="0.001" min="0.001" class="form-control task-resource-units-val" name="task_resource_units[]" value="'.$savedResUnits.'"></td>
                        <td><input type="number" class="form-control taskduration_edit" name="taskduration[]" value="'.(!empty($sche_tasks) ? $sche_tasks['Budgeted_Duration'] : '').'" readonly style="background-color:#e9ecef;"></td>
                        <td style="text-align:center;">'.$buttonrow.'</td>
                    </tr>';

                    if(!empty($sche_tasks) && $sche_tasks['status'] == 0) {
                        $btime = $btime + $sche_tasks['Budgeted_Duration'];
                        $atime = $atime + $averageactulal;
                    }
                endforeach;
            } else {
                $addBtn = ($user->account_type != 'perfm_pad_reporting_only') ? '<a class="btn btn-primary icon-plus icon_plus" id="tasknewrow"></a>' : '';
                $taskRowsHtml = '<tr class="tastrow" id="activityrow0">
                    <td><input type="hidden" name="tasknewid[]" value="">
                        <input type="text" class="form-control taskname_edit" name="taskname[]" value=""></td>
                    <td></td>
                    <td><input type="number" step="0.001" class="form-control task-productivity-val" name="task_productivity_val[]" value=""></td>
                    <td><input type="number" step="0.001" class="form-control" name="task_qty[]" value=""></td>
                    <td><input type="number" step="0.001" min="0.001" class="form-control task-resource-units-val" name="task_resource_units[]" value="1"></td>
                    <td><input type="number" class="form-control taskduration_edit" name="taskduration[]" value="" readonly style="background-color:#e9ecef;"></td>
                    <td style="text-align:center;">'.$addBtn.'</td>
                </tr>';
            }

            // Build new form
            $form = '<style>
                #scheduleform{display:flex;flex-direction:column;}
                .task-screen-title{text-align:center;font-size:18px;font-weight:bold;letter-spacing:1px;margin-bottom:14px;padding-top:8px;}
                .task-screen-info{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;margin-bottom:16px;padding:0 20px;}
                .task-screen-info .info-item{display:flex;align-items:center;gap:8px;white-space:nowrap;}
                .task-screen-info .info-item label{font-weight:600;margin:0;font-size:13px;}
                .task-screen-info .info-item span{font-size:13px;}
                #activitytasktable thead tr th{background-color:#b0b0b0;font-weight:bold;padding:8px 10px;}
                #activitytasktable tbody tr td:last-child{text-align:center;vertical-align:middle;}
                .table>thead>tr>th,.table>tbody>tr>th,.table>tfoot>tr>th,
                .table>thead>tr>td,.table>tbody>tr>td,.table>tfoot>tr>td{padding:6px 8px;}
            </style>';

            $form .= '<form method="POST" action="" id="scheduleform" class="col-md-12 scheduleform">';

            // Preserved hidden fields
            $form .= '<input type="hidden" name="activityid" value="'.$activityid.'">';
            if($wbsactivity):
                $form .= '<input type="hidden" name="wbsid" value="'.$wbsactivity['wbs_id'].'">';
                $form .= '<input type="hidden" name="estimateid" value="'.$wbsactivity['id'].'">';
                $form .= '<input type="hidden" name="iowid" value="'.$wbsactivity['wbs_id'].'">';
            endif;
            $form .= '<input type="hidden" name="activityname" value="'.$name.'">';
            $form .= '<input type="hidden" name="type" value="schedule">';
            $form .= '<input type="hidden" id="est_qty" name="est_qty" value="'.$est_qty.'">';
            $form .= '<input type="hidden" name="cycle_type" value="'.$sch_cycle_type_val.'">';
            $form .= '<input type="hidden" name="report_method" value="'.$sch_rep_mothod_val.'">';
            $form .= '<input type="hidden" name="cycle_qty" id="cycle_qty" value="'.$sch_cyc_qty.'">';
            $form .= '<input type="hidden" name="cycles" id="no_of_cycles" value="'.$sch_cyc.'">';
            $form .= '<input type="hidden" name="resourceunit" id="resourceunit" value="'.$sch_res.'">';
            $form .= '<input type="hidden" name="cycle_units" id="cycle_units" value="'.$sch_cycle_units.'">';
            $form .= '<input type="hidden" name="cycle_unit_type" id="cycle_unit_type" value="'.$sch_cycle_unit_type.'">';
            $form .= '<input type="hidden" name="totalbudget" id="budcycle" value="'.$btime.'">';
            $form .= '<input type="hidden" id="default_working_hours" value="'.$defaultWorkHours.'">';

            // Header: TASKS title on top, info row below
            $activeWork = !empty($sch_work) ? $sch_work : $defaultWorkHours;
            $form .= '<div class="task-screen-title">TASKS</div>
            <div class="task-screen-info">
                <div class="info-item">
                    <label>Activity Name:</label>
                    <span>'.htmlspecialchars($name).'</span>
                </div>
                <div class="info-item">
                    <label>Activity Unit:</label>
                    <span>'.htmlspecialchars($est_unit).'</span>
                </div>
                <div class="info-item">
                    <label>Working Hours:</label>
                    <select class="form-control" name="wrkhrs" id="wrkkhrs" style="width:90px;" '.$disabledForm.'>
                        <option value="8" '.(($activeWork=="8")?"selected":"").'>8</option>
                        <option value="10" '.(($activeWork=="10")?"selected":"").'>10</option>
                        <option value="12" '.(($activeWork=="12")?"selected":"").'>12</option>
                        <option value="24" '.(($activeWork=="24")?"selected":"").'>24</option>
                    </select>
                </div>
            </div>';

            // Task table
            $form .= '<div class="task-table-wrap"><table cellpadding="0" cellspacing="0" id="activitytasktable" class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width:25%;">Tasks</th>
                        <th style="width:8%;">Unit</th>
                        <th style="width:14%;">Productivity / Day</th>
                        <th style="width:13%;">Quantity / Unit</th>
                        <th style="width:12%;">Resource Units</th>
                        <th style="width:13%;">Duration in Days</th>
                        <th style="width:8%;"></th>
                    </tr>
                </thead>
                <tbody id="tasklistname">
                    '.$taskRowsHtml.'
                </tbody>
            </table></div>';

            // Cycle time
            $form .= '<div style="text-align:right;margin:8px 0;padding-right:14%;">
                <strong>Cycle Time:</strong>&nbsp;<span id="cyclebud" style="font-size:15px;">'.number_format($btime, 4).'</span>&nbsp;Days
            </div>';

            // Buttons
            $form .= '<div class="text-center"><span class="error" id="taskScheduleError"></span></div>
            <div class="text-center" style="margin-top:10px;">
                <button type="button" class="btn btn-danger cancel" id="cancelschedule_new" name="cancelschedule" data-id="'.$activityid.'" data-iow="'.$estimate->scheduleitem_id.'"><span class="icon-close"></span>Cancel</button>&nbsp;';

            if($user->account_type != 'perfm_pad_reporting_only'){
                $form .= '<button type="button" class="btn btn-primary" id="saveschedule_new" name="saveschedule"><span class="icon-check"></span>Save</button>';
            }

            $form .= '</div>
            </form>';
        }
        $arr=array('result'=>$form,'error'=>'No');
        return json_encode($arr);
        //echo json_encode($arr);
    
    }


    public function actionAssignuser()
    {
        $id = $_POST['id'];

        $schActivity=Scheduleactivities::findOne($id); 
        $schActivity->assigned_to = $_POST['user_id'];
        $schActivity->save(false);
        
        $arr=array('error'=>'No');
        return json_encode($arr);
    }



     public function actionTaskeditname()
    {
        $id = $_POST['id'];
       // $task = TasksNew::model()->findByPk($id);
        $task=TasksNew::findOne($id); 


        $task->Name = $_POST['taskname'];
        $task->save(false);
        
        $arr=array('error'=>'No');
        return json_encode($arr);
        
    }


    public function actionTaskdelete()
    {
        $id = $_POST['id'];
       // $task = TasksNew::model()->findByPk($id);
         $task=TasksNew::findOne($id); 
        $task->delete();
        
        $arr=array('error'=>'No');
        return json_encode($arr);
        
    }


    public function actionSchedulecreate()
    {

        $actid = $_POST['activityid'];
        $activityname=$_POST['activityname'];

        $wrk=Scheduleactivities::findOne($actid); 

        if(isset($_POST['tasknewid'])){
            $count = count($_POST['tasknewid']);
        }
        else{
            $count = 0;
        }

        //echo $count; exit;

        $connection = \Yii::$app->db; 

        //$delquery = 'DELETE FROM schedule_task_new WHERE activity_name LIKE "'.$activityname.'" AND activity_Id="'.$actid.'"';
        $delquery = 'DELETE FROM schedule_task_new WHERE activity_Id="'.$actid.'"';
        //echo $delquery;exit;
        $command1=$connection->createCommand($delquery);
        $dataReader1=$command1->query();

        $allacttasks=TasksNew::find()->Where(['activity_Id'=>$actid])->all();

        if($allacttasks){

            foreach($allacttasks as $allacttask):

                $allacttask->status = 1;
                  
                $allacttask->save(false);

            endforeach;
        }

        if($count!=0):

            for ($i = 0; $i < $count; $i++) {

                $taskid = $_POST['tasknewid'][$i] != '' ? $_POST['tasknewid'][$i] : 0;

                if($taskid != 0){

                    $tqty      = isset($_POST['task_qty'][$i])              ? (float)$_POST['task_qty'][$i]              : 0;
                    $tprod     = isset($_POST['task_productivity_val'][$i])  ? (float)$_POST['task_productivity_val'][$i]  : 0;
                    $tres      = isset($_POST['task_resource_units'][$i])    ? (float)$_POST['task_resource_units'][$i]    : 1;
                    if ($tres <= 0) $tres = 1;

                    $sql1 = 'INSERT INTO schedule_task_new ( wbs_id, activity_Id, process_Id,wbs_activity_Id, task_Id, Budgeted_Duration, End_Duration, status,activity_name, task_qty, task_productivity, task_resource_units )
                        VALUES
                        ( "0","'.$actid.'","0","0","'.$taskid.'","'.$_POST['taskduration'][$i].'","0","0","'.$activityname.'","'.$tqty.'","'.$tprod.'","'.$tres.'")';
                    $command=$connection->createCommand($sql1);
                    $dataReader=$command->query();

                }

            }

        endif;

        $startdate=date('Y-m-d');

        $enddate=date('Y-m-d');

        //$delquery2 = 'DELETE FROM schedule_activity_new WHERE activity_name="'.$activityname.'" AND actvity_id="'.$actid.'"';
        $delquery2 = 'DELETE FROM schedule_activity_new WHERE  actvity_id="'.$actid.'"';
        //echo $delquery2;exit;
        $command2=$connection->createCommand($delquery2);
        $dataReader2=$command2->query();

        if($_POST['resourceunit']=='' || $_POST['resourceunit']==0){
            $_POST['resourceunit'] = 1;
        }

        if($_POST['cycles']==''){
            $_POST['cycles'] = 0;
        }
        if($_POST['cycle_qty']==''){
            $_POST['cycle_qty'] = 0;
        }
        if($_POST['wrkhrs']==''){
            $_POST['wrkhrs'] = 0;
        }

        $sql = 'INSERT INTO schedule_activity_new ( actvity_id,wbs_activity_Id,Project_Id, process_id, wbs_id, cycle_type, report_method, cycle_units, cycle_unit_type,Resourceunits,Workhours,Cycles,cycle_qty,progress,duration,start_date,end_date,activity_name )
                    VALUES
                    ( "'.$actid.'","0","'.$wrk['projectId'].'","0","0","'.$_POST['cycle_type'].'","'.$_POST['report_method'].'","'.$_POST['cycle_units'].'","'.$_POST['cycle_unit_type'].'","'.$_POST['resourceunit'].'","'.$_POST['wrkhrs'].'","'.$_POST['cycles'].'","'.$_POST['cycle_qty'].'","0","0","'.$startdate.'","'.$enddate.'","'.$activityname.'")';
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();

        // Calculate duration: Cycle Time × Activity Quantity ÷ No of Resources
        $cycleTime = 0;
        if (!empty($_POST['taskduration']) && is_array($_POST['taskduration'])) {
            foreach ($_POST['taskduration'] as $td) { $cycleTime += (float)$td; }
        }
        if ($cycleTime == 0) {
            $cycleTime = isset($_POST['totalbudget']) ? (float)$_POST['totalbudget'] : 0;
        }
        $cycleTime = round($cycleTime, 4);

        if ($cycleTime > 0) {
            $activityQty   = (float)$wrk->quantity > 0 ? (float)$wrk->quantity : 1;
            $wrk->old_duration = (int)ceil(round($cycleTime * $activityQty, 6));
            $startDate  = $wrk->actual_start_date ?: date('Y-m-d');
            $endDateRaw = Yii::$app->helper->getDateAfterHoliday($startDate, $wrk->projectId, max(0, (int)$wrk->old_duration - 1));
            $wrk->actual_end_date = date('Y-m-d', strtotime($endDateRaw));
            $wrk->save(false);
        }

        Yii::$app->helper->GetRelationcorrect($wrk->projectId);

        $arr = array('error' => 'No');
        return json_encode($arr);

    }

    /*public function actionSchedulecreate()
    {
        echo 'hai'; exit;

        $actid=$_POST['activityid'];
        $activityname=$_POST['activityname'];
        if(isset($_POST['processid'])){
        $processid=$_POST['processid'];
        }else{
            $processid="";
        }
        $estimateid=$_POST['estimateid'];
         if(isset($_POST['workhours'])){
        $workhourss=$_POST['workhours'];
        }else{
           $workhourss=""; 
        }

        $wbsid=$_POST['wbsid'];
        $type=$_POST['type'];
       // $wbsact=Scheduleactivities::model()->findByPk($actid);
        $wbsact=Scheduleactivities::findOne($actid);
        $connection = \Yii::$app->db; 
        // $model = itemofworks::model()->findByPk($_POST['iowid']);
        $count = count($_POST['task_id']);
        $duration = 0;
        $budgetedduration = 0;
        $actualduration = 0;
        //deleting the current schedule
        //$delquery = 'DELETE FROM schedule_task WHERE wbs_activity_Id="'.$estimateid.'" AND wbs_id="'.$wbsid.'" AND activity_Id="'.$actid.'" AND process_Id="'.$processid.'"';
        $delquery = 'DELETE FROM schedule_task_new WHERE activity_name LIKE "'.$activityname.'" AND activity_Id="'.$actid.'"';
        //echo $delquery;exit;
        $command1=$connection->createCommand($delquery);
        $dataReader1=$command1->query();

        $q = '';
        for ($i = 0; $i < $count; $i++) {
            $curactualduration=0;
            $curbudgetdur = 0;
            $taskid = $_POST['task_id'][$i];
            if (isset($_POST['cyclexclude' . $taskid])) {
                $status = '1';
            } else {
                $status = '0';
            }
            if (!isset($_POST['cyclexclude' . $taskid])) {
                $budgetedduration = $budgetedduration + $_POST['bduration'][$i];
                if(isset($_POST['eduration'][$i]) && $_POST['eduration'][$i]!=''){
                $actualduration = $actualduration + $_POST['eduration'][$i];
                 }
                 else{
                     $actualduration="";
                 }
                $curbudgetdur = $_POST['bduration'][$i];
                 if(isset($_POST['eduration'][$i])){
                $curactualduration = $_POST['eduration'][$i];
            }
            }
            $sql1 = 'INSERT INTO schedule_task_new ( wbs_id, activity_Id, process_Id,wbs_activity_Id, task_Id, Budgeted_Duration, End_Duration, status,activity_name )
                    VALUES
                    ( "'.$wbsid.'","'.$actid.'","'.$processid.'","'.$estimateid.'","'.$taskid.'","'.$curbudgetdur.'","'.$curactualduration.'","'.$status.'","'.$activityname.'")';
            //echo $sql1;exit;
            $command=$connection->createCommand($sql1);
            $dataReader=$command->query();

        }
        
        if(isset($_POST['workhours'])){
        $days = $_POST['workhours'] / 12;
        }
        else{
             $days="";

        }

        $rdays = round($days, 2);
       // echo $rdays; exit;
        if($rdays!=0){
        $budgeteddurationcalc = ($budgetedduration * $_POST['cycles']) / ($rdays);
        }else{
           $budgeteddurationcalc=0; 
        }
        $budgeteddurationcalc = round($budgeteddurationcalc, 2);
        $finalbuddur = $budgeteddurationcalc / $_POST['resourceunit'];
        $finalbuddur = round($finalbuddur, 2);
         if($rdays!=0){
        $actualdurationcalc = ($actualduration * $_POST['cycles']) / ($rdays);
       }else{
         $actualdurationcalc=0;
       }
        $actualdurationcalc = round($actualdurationcalc, 2);
        $finalactdur = $actualdurationcalc / $_POST['resourceunit'];
        $finalactdur = round($finalactdur, 2);
        $startdate=date('Y-m-d');
        if($finalactdur>0):
            $enddate=date('Y-m-d', strtotime("+".$finalactdur." days"));
        else:
            $enddate=date('Y-m-d');;
        endif;
        if($finalactdur==0):
            $finalactdur=$finalbuddur;
        endif;
        
        //$ssqq = 'SELECT Project_Id FROM workgroups_new WHERE Workgroup_Id="'.$wbsid.'" ';
        //$command=$connection->createCommand($ssqq);
        //$dataReader=$command->query();
        //$projid = $dataReader->read();
        
        $delquery2 = 'DELETE FROM schedule_activity_new WHERE activity_name="'.$activityname.'" AND actvity_id="'.$actid.'"';
        //echo $delquery2;exit;
        $command2=$connection->createCommand($delquery2);
        $dataReader2=$command2->query();
        $sql = 'INSERT INTO schedule_activity_new ( actvity_id,wbs_activity_Id,Project_Id, process_id, wbs_id,Resourceunits,Workhours,Cycles,progress,duration,start_date,end_date,activity_name )
                    VALUES
                    ( "'.$actid.'","'.$estimateid.'","'.$wbsact['projectId'].'","'.$processid.'","'.$wbsid.'","'.$_POST['resourceunit'].'","'.$workhourss.'","'.$_POST['cycles'].'","'.$finalbuddur.'","'.$finalactdur.'","'.$startdate.'","'.$enddate.'","'.$activityname.'")';
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();

        $activityduration = "SELECT SUM(Budgeted_Duration) AS duration FROM schedule_task_new WHERE activity_Id=".$actid." AND status=0 ";
        $command = $connection->createCommand($activityduration);
        $dataReader = $command->query();
        $cycletime = $dataReader->read();
        //if($cycletime['duration']!=0){

            //$task = ScheduleActivityNew::model()->find(array('condition'=>'actvity_id ='.$actid.' '));

            //$task=ScheduleActivityNew::find()->where(['actvity_id' =>$actid])->one();

            //$duration = round(($cycletime['duration'] * $task['Cycles']) / $task['Resourceunits']);
            //$wbsact->duration= $duration;
            //$dateadjust = $duration - 1;
            //$wbsact->end_date = date('Y-m-d', strtotime($wbsact->start_date. ' + '.$dateadjust.' days')); 
        //}

    //}
        if(isset($_POST['activityqty'])){
        $wbsact->cycle_quantity=$_POST['activityqty'];
        }
        if(isset($_POST['activityunit'])){
        $wbsact->cycle_unit=$_POST['activityunit'];
        }
        $wbsact->status=0;
        $wbsact->save(false);

        //$availableActivites = Scheduleactivities::model()->findAll(array('condition'=>'projectId ='.$wbsact->projectId.' AND status =0','order'=>'sortorder ASC'));

        $availableActivites=Scheduleactivities::find()->Where(['projectId'=>$wbsact->projectId])->andWhere(['status'=>0])->orderBy(['sortorder'=> 'SORT_ASC'])->all();  


        if(count($availableActivites) > 0)
        {       
            // echo $actID;exit;
            foreach ($availableActivites as $activity) 
            {
                $acId = $activity->id;
               // $allRelations = ActivityRelations::model()->findAll(array('condition'=>'precedent_activity ='.$acId.' AND status =0'));
              
               $allRelations=ActivityRelations::find()->Where(['precedent_activity'=>$acId])->andWhere(['status'=>0])->all();

                if(count($allRelations) > 0)
                {

                    // echo "test";exit;
                    foreach ($allRelations AS $relation)
                    {

                       // $precAct = Scheduleactivities::model()->findByPk($acId);
                        $precAct=Scheduleactivities::findOne($acId);

                       // $dependAc = Scheduleactivities::model()->findByPk($relation->dependent_activity);
                       $dependAc=Scheduleactivities::findOne($relation->dependent_activity);

                        // check if already reported or not
                        //$reported = ProgressReport::model()->find(array('condition'=>'activity_id ='.$relation->dependent_activity.' AND status =1'));

                    
                        $reported=ProgressReport::find()->where(['activity_id' =>$relation->dependent_activity])->andWhere(['status'=>1])->one();


                       // $precedentActual = ProgressReport::model()->find(array('condition'=>'activity_id ='.$acId));

                     $precedentActual=ProgressReport::find()->where(['activity_id' =>$acId])->one();

                        if($precedentActual)
                        {

                            $start_date = strtotime($precedentActual->start_date);
                            if($precedentActual->status==1)
                            {
                                $end_date = strtotime($precedentActual->end_date);
                                $datediff = $end_date - $start_date;
                                $duration = round($datediff / (60 * 60 * 24)+1);
                                $dayForOneUnitQty = $duration/$precedentActual->total_quantity;
                                $totalDaysForComp = $dayForOneUnitQty*$precAct->quantity;

                                $actEndDate = strtotime("+".floor($totalDaysForComp)." day", strtotime($precedentActual->start_date));
                                $enDateWillBe =  date('Y-m-d', $actEndDate);
                                // echo floor($totalDaysForComp);exit;

                            }
                            else
                            {
                                $actEndDate = strtotime("+".round($precAct->duration)." day", strtotime($precedentActual->start_date));
                                $enDateWillBe =  date('Y-m-d', $actEndDate);

                            }       
                        }
                        if(!$precedentActual){  
                            $precedentActual = $precAct;
                            $start_date = strtotime($precedentActual->start_date);
                            $end_date = strtotime($precedentActual->end_date);
                            $enDateWillBe =  date('Y-m-d', $end_date);
                        }
                        //print_r($reportedpre); exit;
                        // if(!$reported)
                        // {
                            if($relation->relation_type == 1)
                            {
                                // echo $dependAc->start_date;exit;
                                //  if($precAct->start_date != $dependAc->start_date)
                                //  {
                                    $lag = $dependAc->lag;
                                    $dependAc->start_date = date('Y-m-d', strtotime($precAct->start_date. ' + '.$lag.' days'));
                                    $duration = $dependAc->duration + $lag - 1;
                                    $dependAc->end_date = date('Y-m-d', strtotime($precAct->start_date. ' + '.$duration.' days'));
                                    $dependAc->save(false);

                                // }
                                $lag = $dependAc->lag;
                               // $ProgressReport = ProgressReport::model()->find(array('condition'=>'activity_id ='.$relation->dependent_activity.' AND status =0'));

                         $ProgressReport=ProgressReport::find()->where(['activity_id' =>$relation->dependent_activity])->andWhere(['status'=>0])->one();



                                if(($ProgressReport)>0):
                                    $ProgressReport->start_date = date('Y-m-d', strtotime($precedentActual->start_date. ' + '.$lag.' days'));
                                    $ProgressReport->save(false);
                                endif;                                   
                            }
                            elseif($relation->relation_type == 2)
                            {
                                // echo $dependAc->start_date;exit;
                               // $ProgressReport = ProgressReport::model()->find(array('condition'=>'activity_id ='.$relation->dependent_activity.' AND status =0'));
                            $ProgressReport=ProgressReport::find()->where(['activity_id' =>$relation->dependent_activity])->andWhere(['status'=>0])->one();



                                if(($ProgressReport)>0):
                                    $ProgressReport->start_date = date('Y-m-d', strtotime($enDateWillBe.' + 0 days'));
                                    $ProgressReport->save(false);
                                endif;

                                if(date('Y-m-d', strtotime($precAct->end_date.' + 1 days')) != $dependAc->start_date)
                                {
                                    $dependAc->start_date = date('Y-m-d', strtotime($precAct->end_date.' + 1 days'));
                                    $start = date('Y-m-d', strtotime($precAct->end_date));
                                    $duration = $dependAc->duration;
                                    $dependAc->end_date = date('Y-m-d', strtotime($start. ' + '.$duration.' days'));
                                    if($dependAc->save(false))
                                    {
                                        // echo $dependAc->end_date;
                                        // exit;
                                    }
                                }  
                            }
                        //}
                        // var_dump($dependAc);
                    }

                }
                // exit;
                
            }
        }

    //}

        $arr = array('error' => 'No');
        return json_encode($arr);

    }*/

    public function actionNewtaskcreate()
    {
      $activityid = $_POST['activityid'];
     // echo $activityid; exit;
      $wrk=Scheduleactivities::findOne($activityid); 

      $model = New TasksNew();
      $model->activity_Id = $activityid;
      $model->Name = $_POST['taskname'];
      if($wrk->process==""){
         $model->process_id = 0;
      }else{
       $model->process_id = $wrk->process;
      }
      
      if($model->save(false)){
        //$tasks = TasksNew::model()->findAll(array('condition'=>'activity_Id ='.$activityid.''));
        $tasks = TasksNew::find()->where(['activity_Id' => $activityid])->all();
        $key = count($tasks);
        $form = '<tr id="activityrow' . $model['task_Id'] . '">
                    <td><span id="taskname'.$model['task_Id'].'" data-id="'.$model['task_Id'].'">' . $model['Name'] . '</span>
                    <input type="text" class="form-control taskname_edit" id="taskname_edit'.$model['task_Id'].'"  data-id="' . $model['task_Id'] . '" name="taskname" value="' . $model['Name'] . '" style="display:none;">
                    </td>
                    <td id="b">
                      <input type="hidden" name="task_id[]" value="'. $model['task_Id'] .'">
                      <input type="text" class="form-control bduration" id="bduration' . $key . '"  data-id="' . $key . '" name="bduration[]" value=""><span class="error"></span>
                    </td>
                    <!--<td><input type="text" class="form-control eduration"  readonly="readonly" id="eduratio' . $model['task_Id'] . '"  data-id="' . $model['task_Id'] . '" name="eduration[]" value=""><span class="error"></span></td>-->
                    <td class="icon-groups">
                    <!--<input type="checkbox" name="cyclexclude' . $model['task_Id'] . '" class="activity" data-id="' . $model['task_Id'] . '" value="' . $model['task_Id'] . '" style="visibility: visible;">-->
                    <a class="btn btn-primary icon-pencil edittaskname" id="edittaskname'.$model['task_Id'].'" data-id="'.$model['task_Id'].'"></a>
                    <a class="btn btn-primary icon-save savetaskname" id="savetaskname'.$model['task_Id'].'" data-id="'.$model['task_Id'].'" style="display:none;"></a>
                    <a class="btn btn-danger icon-trash1" id="removetask" data-id="'.$model['task_Id'].'"></a></td>
                 </tr>';
      }
      
      $arr=array('result'=>$form,'error'=>'No');
      return json_encode($arr);
    }
    




    public function actionUpdateganttsort()
    {
       if(isset($_POST['datavalue']))
        {
            foreach($_POST['datavalue'] AS $data)
            {
                $activities=Scheduleactivities::findOne($data['rowid']);
                $activities->sortorder=$data['rowindex'];
                $activities->save(false);
            }
        } 
    }
    public function actionResourceassign()
    {
        $connection = Yii::$app->db;
        $activityid=$_POST['activityid'];
        $projectid=$_POST['projectid'];
        $project = Projects::findOne($_POST['projectid']);

        $scheduleactivity = Scheduleactivities::find()->where(['id' => $activityid])->one();
        
        $resourcelists = ScheduleResource::find()->where(['activityid' => $activityid])->andWhere(['status' => 0])->all();
        
        $headone='<span>Activity: '.$scheduleactivity->name.' <em>Quantity: '.number_format((float)$scheduleactivity->quantity, 3, '.', '').'</em>  <em>Unit: '.$scheduleactivity->unit.'</em></span>
            <input type="hidden" id="activityid" name="activityid" value="'.$activityid.'">
            <input type="hidden" id="Project_Id" name="Project_Id" value="'.$projectid.'">';
        $addedones='';
        $SAbuttonlist='';

        $addedones.='<div class="row scacvts">
                        <div class="col-md-1">
                            <label>#</label>
                        </div>
                        <div class="col-md-4">
                            <label>Resource</label>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6" style="padding-left:0px;">
                                    <label>Resource Available Capacity</label>
                                </div>
                                <div class="col-md-6">
                                    <label>Resource Utilised/ Hours</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1">
                        </div>
                     </div>';

        foreach($resourcelists as $key => $resourcelist):
            $addedones.='<div class="row added-item" id="scheresrow'.$resourcelist->id.'" style="padding:0px;">
                                <div class="col-md-1 srnoact">
                                    <span class="number">'.intval($key+1).'</span>
                                </div>
                                <div class="col-md-4 type rsnmee ">

                                    <span>'.Resources::findOne( $resourcelist->resourceid)->Name.'</span>
                                </div>
                                <div class="col-md-6 type vendor-column " style="margin-bottom:0px;">
                                    <div class="row">
                                    <div class="col-md-6 type reavil">
                                        
                                        <span id="resource_cpcty'.$resourcelist->id.'">'.number_format((float)$resourcelist->capacity, 3, '.', '').'</span>
                                        <input type="text" data-id="'.$resourcelist->id.'" class="form-control resourcecpcty" id="resourcecpcty'.$resourcelist->id.'" value="'.number_format((float)$resourcelist->capacity, 3, '.', '').'" style="display:none;"><span class="error"></span></td>
                                    </div>
                                    <div class="col-md-6 ton-column type rshrs">
                                        
                                        <span id="resource_utilzd'.$resourcelist->id.'">'.number_format((float)$resourcelist->utilised, 3, '.', '').'</span>
                                        <input type="text" data-id="'.$resourcelist->id.'" class="form-control resourceutilzd" id="resourceutilzd'.$resourcelist->id.'" value="'.number_format((float)$resourcelist->utilised, 3, '.', '').'" style="display:none;"><span class="error"></span></td>
                                    </div>
                                    </div>
                                </div>
                                <div class="col-md-1 icon-groups grpsicnss" style="bottom: 8px;">
                                    <a href="javascript:void(0);" class="btn btn-primary icon-pen editresourceschitem" id="editresourceschitem'.$resourcelist->id.'" data-v="'.$resourcelist->id.'" value="'.$resourcelist->id.'" title="Edit Item"></a>
                                    <a href="javascript:void(0);" class="btn btn-primary icon-save saveresourceschitem" id="saveresourceschitem'.$resourcelist->id.'" data-v="'.$resourcelist->id.'" value="'.$resourcelist->id.'" title="Save Item" style="display:none;"></a>
                                    <a href="javascript:void(0);" class="btn btn-primary icon-trash1 removeresourceschitem" id="removeresourceschitem'.$resourcelist->id.'" data-v="'.$resourcelist->id.'" value="'.$resourcelist->id.'" title="Remove Item"></a>
                                </div>
                            </div>';
        endforeach;
        $resourcetypes = Resourcetype::find()->orderBy(['sortorder'=> SORT_ASC])->all();
        foreach($resourcetypes AS $key => $resourcetype):
            if($key == 1){
                $activelist = 'active';
            }else{
                $activelist = '';
            }
            if($resourcetype->ResourceType_Id==26 || $resourcetype->ResourceType_Id==24 || $resourcetype->ResourceType_Id==19 || $resourcetype->ResourceType_Id==33)
            {
                $SAbuttonlist.='<a class="btn btn-primary rounded-coner-btn '.$activelist.' resourcesearch1" href="javascript:void(0);" id="selected_resource" data-v="'.$resourcetype->ResourceType_Id.'" value="'.$resourcetype->ResourceType_Id.'" title="'.$resourcetype->Name .'">'.$resourcetype->Name.'</a>';
            }
        endforeach;

        $arr = array('headone' => $headone,'error'=>'No','addedones' =>$addedones,'SAbuttonlist' =>$SAbuttonlist);
		return json_encode($arr);

        // $this->render('assignresource', array(
        //     'project' => $project,
        //     'scheduleactivity' => $scheduleactivity,
        //     'addedones' => $addedones,
        // ));
    }

    public function actionResourcesearchbytyid()
    {
        $restypeid = $_POST['resourcetypeid'];
        //$resourcegroupid = $_POST['resourcegroup'];
        if(isset($_POST['vendorid'])){
            $vendorid = $_POST['vendorid'];
        }else{
            $vendorid = '';
        }
        $connection = Yii::$app->db;
        $sql="SELECT Resource_Id,Name,Unit,Price,ResourceType_Id,Resource_group_Id,Resource_Location,Vendor_Id FROM resources WHERE status='0' AND pricing_status=0 ";

        if($restypeid!='')
            $sql.="AND ResourceType_Id ='".$restypeid."' ";
        /*if($resourcegroupid!='none' && $resourcegroupid!='')
            $sql.="AND Resource_group_Id ='".$resourcegroupid."' ";*/
        if($vendorid!='none' && $vendorid!='')
            $sql.="AND Vendor_Id ='".$vendorid."' ";
        if($_POST['name']!='')
            $sql .= "AND Name LIKE '%" . $_POST['name'] . "%'";
        $sql.=" GROUP BY Name ORDER BY Resource_Id ASC, Name ASC";
        //echo $sql;exit;
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $dataProvider=$dataReader->readAll();
        $datarows='';
        /*$grouplist = ResourceGroup::find()->where(['ResourceType_Id' => $restypeid])->all();
        $resgpname = "<option value='none'>Select Resource Group</option>";
        foreach($grouplist AS $list):
            if($resourcegroupid==$list->Resource_group_Id):
                $selected='selected';
            else:
                $selected='';
            endif;
            $resgpname .="<option value='".$list->Resource_group_Id."' ".$selected.">".$list->Resource_group_Name."</option>";
        endforeach;*/
        $restypename=Resourcetype::findOne($restypeid)->Name;
        /*if($resourcegroupid!='none' && $resourcegroupid!=''):
            $groupname = ResourceGroup::findOne($resourcegroupid)->Resource_group_Name;
        else:
            $groupname='';
        endif;*/
        $datarows.='<div class="row">
                                <div class="add-activity-search-and-list-cntnr col-md-12">
                            
                                    <div class="add-activity-search-results-title-wpr row">
                                        <div class="col-md-1">
                                            <label>#</label>
                                        </div>
                                        <div class="col-md-6">
                                            <label>Resource Name</label>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Resource Capacity</label>
                                        </div>
                                        <div class="col-md-2">
                                            <label>&nbsp;</label>
                                        </div>
                                    </div>';
        if(count($dataProvider)>0):
            foreach($dataProvider AS $key=>$data):
                /*$sql1="SELECT Resource_Id,Name,Unit,Price,ResourceType_Id,Resource_group_Id,Resource_Location,Vendor_Id FROM resources WHERE status='0' AND pricing_status=0 AND Name='".$data['Name']."' ";
                if($restypeid!='')
                    $sql1.="AND ResourceType_Id ='".$restypeid."' ";
                if($resourcegroupid!='none' && $resourcegroupid!='')
                    $sql1.="AND Resource_group_Id ='".$resourcegroupid."' ";
                if($vendorid!='none' && $vendorid!='')
                    $sql1.="AND Vendor_Id ='".$vendorid."' ";
                if($_POST['name']!='')
                    $sql1 .= "AND Name LIKE '%" . $_POST['name'] . "%'";
                $sql1.=" ORDER BY Resource_Id ASC, Name ASC";
                //echo $sql1;exit;
                $command=$connection->createCommand($sql1);
                $dataReader=$command->query();
                $childdataProvider=$dataReader->readAll();
                $childdatarows='';
                foreach($childdataProvider AS $childdata):
                    $vendor=Vendors::model()->findByPk($childdata['Vendor_Id']);
                    $vendorname=$vendor->Name." ".$vendor->Brand;
                    $childdatarows.="<tr id='resource_vendorrow".$childdata['Resource_Id']."' class='resourcetype".$childdata['ResourceType_Id']."'>
                            <td colspan='1'></td>
                            <td>".$vendorname."</td>
                            <td>".$childdata['Resource_Location']."</td>
                            <td>".$childdata['Unit']."</td>
                            <td>".$childdata['Price']." <input type='hidden' id='resourceprice".$childdata['Resource_Id']."' value='".$childdata['Price']."'></td>
                            <td class='small75'><button type='button' class='btn btn-primary addresource' value='".$childdata['Resource_Id']."' id='addresource_button".$childdata['Resource_Id']."' title='Add Item'> <span class='glyphicon glyphicon-upload'></span></button></td>
                        </tr>";
                endforeach;*/
                //$groupname = ResourceGroup::findOne($data['Resource_group_Id'])->Resource_group_Name;
                $project = Projects::findOne($_POST['Project_Id']); 
                $rescapacity = $project->duration * $project->wrkhours;
                $datarows.='<div class="add-activity-search-results-content-wpr row resourcetype'.$data['ResourceType_Id'].'" id="resourcerow'.$data['Resource_Id'].'">    
                                <div class="col-md-1">
                                    <label>&nbsp;</label>	
                                    <span>'.intval($key+1).'</span>
                                </div>
                                <div class="col-md-6">
                                    <label>&nbsp;</label>
                                    <span >'.$data['Name'].'</span></div>
                                <div class="col-md-3">
                                    <label>&nbsp;</label>	
                                    <span>'.$rescapacity.'</span>
                                </div>
                                <div class="col-md-2">
                                    <label>&nbsp;</label>
                                    <input type="hidden" class="form-control" id="iowactivityamount3" value="0">
                                    <!--<span><b>Added</b></span>-->
                                    <button type="button" class="btn btn-primary addresource add-alloc-item" value="'.$data['Resource_Id'].'" id="addresource_button'.$data['Resource_Id'].'" title="Add Item"> <span class="glyphicon glyphicon-upload"></span></button>
                                </div>
                            </div>';
            endforeach;
            $datarows.='</div>
            </div>';
        else:
            $datarows= '<div style="text-align: center;">No Resources Found</div>';
        endif;
        $arr = array('result' => $datarows, 'group' => '', 'error'=>'No');
        return json_encode($arr);


    }

    public function actionAddresourceassign()
    {
        $connection = Yii::$app->db;
        $addedones='';
        $resource=Resources::findOne($_POST['resid']);
        $restype=$resource['ResourceType_Id'];
        $resname=$resource['Name'];
        $resunit=$resource['Unit'];
        $resid=$_POST['resid'];
        $scheduleres = new ScheduleResource();
        $scheduleres->activityid = $_POST['activityid'];
        $scheduleres->projectid = $_POST['Project_Id'];
        $scheduleres->resourceid = $resid;
        $project = Projects::findOne($_POST['Project_Id']); 
        $schactvty = Scheduleactivities::findOne($_POST['activityid']);
        $scheduleres->capacity = $project->duration * $project->wrkhours;
        $scheduleres->utilised = $schactvty->duration * $project->wrkhours;
        $scheduleres->status = 0;
        $scheduleres->save(false);

        $addedones.="<tr id='scheresrow".$scheduleres->id."'>
                            <td>".Resources::findOne( $scheduleres->resourceid)->Name."</td>
                            <td style='width: 20%'>
                            <span id='resource_cpcty".$scheduleres->id."'>".number_format((float)$scheduleres->capacity, 3, '.', '')."</span>
                            <input type='text' data-id='".$scheduleres->id."' class='form-control resourcecpcty' id='resourcecpcty".$scheduleres->id."' value='".number_format((float)$scheduleres->capacity, 3, '.', '')."' style='display:none;'><span class='error'></span></td>
                            <td style='width: 20%'>
                            <span id='resource_utilzd".$scheduleres->id."'>".number_format((float)$scheduleres->utilised, 3, '.', '')."</span>
                            <input type='text' data-id='".$scheduleres->id."' class='form-control resourceutilzd' id='resourceutilzd".$scheduleres->id."' value='".number_format((float)$scheduleres->utilised, 3, '.', '')."' style='display:none;'><span class='error'></span></td>
                            <td>
                                <button type='button' class='btn btn-primary editresourceschitem' id='editresourceschitem".$scheduleres->id."' value='".$scheduleres->id."' title='Edit Item'><span>Edit</span>
                                </button>
                                <button type='button' class='btn btn-primary saveresourceschitem' id='saveresourceschitem".$scheduleres->id."' value='".$scheduleres->id."' title='Save Item' style='display:none;'><span>Save</span>
                                </button>
                            </td>
                            <td><button type='button' class='btn btn-primary removeresourceschitem' id='removeresourceschitem".$scheduleres->id."' value='".$scheduleres->id."' title='Remove Item'><span>Remove</span></button></td>
                                </tr>";

        //$arr = array('result' => $addedones,'price'=>$price,'error'=>'No');
        $arr = array('result' => $addedones,'error'=>'No');
        return json_encode($arr);
    }

    public function actionDeletescheduleres()
    {
        $resource = ScheduleResource::find()->where(['id' => $_POST['projresid']])->one();
        $resource->status = 1;
        $resource->save(false);

        $arr = array('error'=>'No');
        return json_encode($arr);
    }

    public function actionSavescheduleres()
    {
        $resource = ScheduleResource::find()->where(['id' => $_POST['projresid']])->one();
        $resource->capacity = $_POST['capacity'];
        $resource->utilised = $_POST['utilised'];
        $resource->save(false);

        $arr = array('error'=>'No');
        return json_encode($arr);
    }

	public function actionAddscheduleactivities()
    {
        
        $model = New Scheduleactivities();
        $model->name=$_POST['activityname'];
        $model->unit=$_POST['activityunit'];
        $model->start_date = $_POST['startDate'];
        if($_POST['duration'] != '')
            $model->duration = $_POST['duration'];
        $model->end_date = $_POST['endDate'];
        $model->actual_start_date = $_POST['startDate'];
        $model->actual_end_date = $_POST['endDate'];
        $model->old_duration = $_POST['duration'];
        $model->quantity = $_POST['quantity'];
        $model->resource_units = isset($_POST['resourceunits']) && $_POST['resourceunits'] != '' ? (float)$_POST['resourceunits'] : 1;
        $model->scheduleitem_id=$_POST['itemId'];

        $uid = Yii::$app->user->Id; 
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        $projectid = $projuser->projectid;

        $model->projectId=$projectid;
        $activity=Scheduleactivities::find()->orderBy(['id'=> SORT_DESC])->one();
        $sortorder=$activity['sortorder'] + 1;
        $model->sortorder=$sortorder;
        $model->save(false);
    
        
        $arr = array('Name' => $_POST['activityname'], 'Unit' => $_POST['activityunit'], 'error' => 'No');
        return json_encode($arr);
    }

	public function actionScheduleactivitydelete()
    {
        $activity=Scheduleactivities::findOne($_POST['id']);
        $connection = Yii::$app->db;
        if(!empty($activity))
        {
            $act_Id = $activity->activity_id;
            $sch_Id = $activity->id;
            //check data in project schedule - relation
            $relations = ActivityRelations::find()->where(['or',['precedent_activity' => $_POST['id']],['dependent_activity' => $_POST['id']]])->andWhere(['status' => 0])->all();

            if(!empty($relations))
            {
                $arr = array('error'=>'Yes','errortext'=>'Cannot delete this item as it has relation in schedule. Please delete activity relation.');
                return json_encode($arr);
            }
            else
            {
                //no relation delete all data
                $model=WorkgroupActivitiesNew::findOne($act_Id);
                $model->delete();

                $activity->status = 1;
                
                if($activity->save(false)):
                    $relations = ActivityRelations::find()->where(['or',['precedent_activity' => $_POST['id']],['dependent_activity' => $_POST['id']]])->andWhere(['status' => 0])->all();
                    foreach($relations as $relation):
                        $relation->status = 1;
                        $relation->save(false);
                    endforeach;
                endif;

                $delquery = 'DELETE FROM schedule_task WHERE wbs_activity_Id="'.$act_Id.'" ';
                $command1=$connection->createCommand($delquery);
                $dataReader1=$command1->query();
                $delquery2 = 'DELETE FROM schedule_activity WHERE wbs_activity_Id="'.$act_Id.'"';
                $command2=$connection->createCommand($delquery2);
                $dataReader2=$command2->query();
                $delquery3 = 'DELETE FROM schedule_activity_new WHERE actvity_id="'.$act_Id.'"';
                $command3=$connection->createCommand($delquery3);
                $dataReader3=$command3->query();

                $arr = array('Id' => $_POST['id'],'processid'=>$_POST['type'],'error'=>'No');
                return json_encode($arr);

            }

        }
        
        

       

        

    }
	
	public function actionScheduleactivityupdate()
    {
        // var_dump($_POST['id']);exit;
        $activity1=Scheduleactivities::findOne($_POST['id']);
        $activity1->name=$_POST['name'];
        $activity1->unit=$_POST['unit'];
        $activity1->quantity=$_POST['quantity'];
        $activity1->resource_units = isset($_POST['resourceunits']) && $_POST['resourceunits'] != '' ? (float)$_POST['resourceunits'] : 1;
      /*  if($_POST['lag']!=''){
            $activity1->previous_lag=$_POST['lag'];
            $activity1->lag=$_POST['lag'];
        }
        else{
            $activity1->previous_lag=0;
            $activity1->lag=0;
        }*/
        
        $activity1->actual_start_date= $_POST['startdate'];
        $activity1->actual_end_date= $_POST['enddate'];
        //$activity1->duration=$_POST['duration'];
        $activity1->old_duration=$_POST['duration'];

        // Check predecessors BEFORE saving so we can set the CPM anchor correctly.
        // GetRelationcorrect() uses act_start_date as the immutable anchor for activities
        // without predecessors, then runs: actual_start_date = start_date via a mass UPDATE.
        // If act_start_date still holds the old baseline, the mass UPDATE resets our new date.
        $hasPredecessors = ActivityRelations::find()
            ->where(['status' => 0])
            ->andWhere(['dependent_activity' => $activity1->id])
            ->exists();

        if (!$hasPredecessors) {
            // Anchor the CPM to the new date so GetRelationcorrect() preserves it.
            $activity1->start_date     = $activity1->actual_start_date;
            $activity1->act_start_date = $activity1->actual_start_date;
        }

        if($activity1->save(false)):
        $availableActivites = Scheduleactivities::find()->where(['scheduleitem_id'=> $activity1['scheduleitem_id']])->andWhere(['status'=> 0])->orderBy(['scheduleitem_id' => SORT_ASC])->all();
        Yii::$app->helper->GetRelationcorrect($activity1['projectId']);
        endif;

        // For no-predecessor, no-task activities: recalculate end_date from new start + duration.
        // (Task-based activities: cycleTime block below handles end_date.)
        if (!$hasPredecessors) {
            $taskCheck = \Yii::$app->db->createCommand(
                "SELECT COALESCE(SUM(Budgeted_Duration), 0) AS total FROM schedule_task_new WHERE activity_Id = :id AND status = 0",
                [':id' => (int)$activity1->id]
            )->queryOne();
            if (!$taskCheck || (float)$taskCheck['total'] == 0) {
                $newEnd = Yii::$app->helper->getDateAfterHoliday(
                    $activity1->actual_start_date,
                    $activity1->projectId,
                    max(0, (int)$activity1->old_duration - 1)
                );
                $activity1->actual_end_date = date('Y-m-d', strtotime($newEnd));
                $activity1->end_date = $activity1->actual_end_date;
                $activity1->save(false);
            }
        }

        // Recalculate duration when tasks exist (cycle time stored in schedule_task_new)
        $cycleRow = \Yii::$app->db->createCommand(
            "SELECT COALESCE(SUM(Budgeted_Duration), 0) AS total FROM schedule_task_new WHERE activity_Id = :id AND status = 0",
            [':id' => (int)$activity1->id]
        )->queryOne();
        $cycleTime = $cycleRow ? round((float)$cycleRow['total'], 4) : 0;

        if ($cycleTime > 0) {
            $actQty   = (float)$activity1->quantity > 0 ? (float)$activity1->quantity : 1;
            $activity1->old_duration = (int)ceil(round($cycleTime * $actQty, 6));
            $startDate  = $activity1->actual_start_date ?: date('Y-m-d');
            $endDateRaw = Yii::$app->helper->getDateAfterHoliday($startDate, $activity1->projectId, max(0, (int)$activity1->old_duration - 1));
            $activity1->actual_end_date = date('Y-m-d', strtotime($endDateRaw));
            $activity1->save(false);
            Yii::$app->helper->GetRelationcorrect($activity1->projectId);
        }

        $arr = array('Id' => $_POST['id'],'Name'=>$_POST['name'],'Unit'=>$_POST['unit'],'Duration'=>$activity1->old_duration,'Startdate'=>date("d-m-Y", strtotime($activity1->actual_start_date)),'Enddate'=>date("d-m-Y", strtotime($activity1->actual_end_date)),'Editstartdate'=>date("Y-m-d", strtotime($activity1->actual_start_date)),'Editenddate'=>date("Y-m-d", strtotime($activity1->actual_end_date)),'Quantity'=>$_POST['quantity'],'Resourceunits'=>$activity1->resource_units,'Lag'=>$_POST['lag'],'error'=>'No');
        return json_encode($arr);
	}

    public function actionSavescheduleqty()
    {
        $uid = Yii::$app->user->Id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        $projectId = $projuser->projectid;

        $ids        = isset($_POST['ids'])        ? $_POST['ids']        : [];
        $quantities = isset($_POST['quantities']) ? $_POST['quantities'] : [];

        for ($i = 0; $i < count($ids); $i++) {
            $schedActivity = Scheduleactivities::findOne((int)$ids[$i]);
            if (!$schedActivity || $schedActivity->projectId != $projectId) continue;

            $newQty  = $quantities[$i];
            $oldQty  = $schedActivity->quantity;
            $schedActivity->quantity = $newQty;
            $schedActivity->save(false);

        }

        return json_encode(['error' => 'No', 'message' => 'Saved']);
    }


	//functions

	public function GetRelationcorrect($projectid)
    {
        // DEACTIVATED legacy — all CPM scheduling now runs through the textbook
        // engine in HelperComponent::GetRelationcorrect(). Body below is unreachable.
        return Yii::$app->helper->GetRelationcorrect($projectid);

        $scheduleactivities = Scheduleactivities::find()->where(['projectId'=>$projectid])->andWhere(['status'=> 0])->orderBy(['scheduleitem_id'=> SORT_ASC,'sortorder'=>SORT_ASC])->all();
        $connection = Yii::$app->db;
        //if(count($scheduleactivities) > 0)
        if(!empty($scheduleactivities))
        {
            foreach ($scheduleactivities as $scheduleactivity) 
            {
                $today = date('Y-m-d');
                $activityid = $scheduleactivity->id;
                $schedulereport = ScheduleProgressReport::find()->where(['activity_id'=> $activityid])->one(); 

                $sql1 = "SELECT SUM(currentqty) AS totalqty FROM schedule_progress_report_log WHERE activity_id='".$activityid."'";
                $command = $connection->createCommand($sql1);
                $dataReader = $command->query();
                $totalQty = $dataReader->read();



                if($schedulereport && $totalQty['totalqty']>0){
                    $start_date1 = date('Y-m-d', strtotime($schedulereport->start_date));
                    $start_date = $schedulereport->start_date;
                }
                else{
                    $start_date1 = date('Y-m-d', strtotime($scheduleactivity->actual_start_date));
                    if($today>$scheduleactivity->actual_start_date){
                        $start_date = $today;
                    }
                    else{
                        $start_date = $scheduleactivity->actual_start_date;
                    }
                }

                //--- NEW ACTUAL DURATION CALCULATION BASED ON TASK REPORTING --------------

                $totCumDuration     =   Yii::$app->helper->secondsToTime(
                            Yii::$app->helper->getDataFromQuery("   
                                SELECT (SUM( (act_duration)) + 
                                        SUM( (waste_hour)) + 
                                        SUM( (break_hour)) 
                                      ) as tot_duration_sum  
                                FROM schedule_task_report 
                                WHERE status = 1
                                AND activityid=".$activityid
                            )['tot_duration_sum']
                        );

                $totStoppageTime    =   Yii::$app->helper->secondsToTime(
                                            Yii::$app->helper->getDataFromQuery("   
                                                SELECT SUM(stoppage_time) as tot_stoppage_time  
                                                FROM schedule_task_report 
                                                WHERE status = 1
                                                AND activityid=".$activityid
                                            )['tot_stoppage_time']
                                        );
                $actualDuration = 0;
                if($totCumDuration && $totCumDuration != '00:00'){
                    $cumqty         = Yii::$app->helper->getDataFromQuery(" SELECT SUM(currentqty) AS totalqty 
                                                                FROM schedule_progress_report_log 
                                                                WHERE  activity_id=".$activityid
                                             )['totalqty'];
                    $cumqty = round($cumqty,3);

                    $schactnew  = ScheduleActivityNew::find()->where(['actvity_id' => $activityid])->one();
                    $Cycles     = $schactnew->Cycles;
                    $workhours  = $schactnew->Workhours;
                    $resunit    = $schactnew->Resourceunits;
                    
                    $activityQty = $scheduleactivity->quantity;
                    $actualDuration = 0;
                    if($cumqty){
                        //$workhours = 24;//Consider only 24
                        $totStoppageDays = 0;
                        if($totStoppageTime)
                            $totStoppageDays = Yii::$app->helper->hoursToMinutes($totStoppageTime)/(24*60);

                        //$actualDuration   = round((($totCumDuration / $cumqty) * $activityQty)  / $workhours);
                        //$actualDuration   = round((((Yii::$app->helper->hoursToMinutes($totCumDuration)/($workhours*60))/$cumqty)*$activityQty)+ $totStoppageDays);


                        $startDateQuery =   ScheduleTaskReport::find()
                                            ->where(['activityid' => $activityid])
                                            ->orderBy(['task_date'=>SORT_ASC, 'start_time'=>SORT_ASC])
                                            ->one();
                        $endDateQuery   =   ScheduleTaskReport::find()
                                            ->where(['activityid' => $activityid])
                                            ->orderBy(['task_enddate'=>SORT_DESC, 'end_time'=>SORT_DESC])
                                            ->one();

                        $from_time      = strtotime($startDateQuery->task_date." ".$startDateQuery->start_time); 
                        $to_time        = strtotime($endDateQuery->task_enddate." ".$endDateQuery->end_time); 
                        $diff_minutes   = round(abs($from_time - $to_time) / 60,2);

                        /*$totCumDuration = Yii::$app->helper->secondsToTime(($diff_minutes - Yii::$app->helper->hoursToMinutes($totStoppageTime))*60);
                        //$reptdDays      = (Yii::$app->helper->hoursToMinutes($totCumDuration)/($workhours*60));
                        $reptdDays      = (
                                            (  
                                                (floor(Yii::$app->helper->hoursToMinutes($totCumDuration)/(24*60)) * $workhours) + 
                                                (((Yii::$app->helper->hoursToMinutes($totCumDuration)%($workhours*60))/($workhours*60)) * $workhours)
                                            )
                                            - (floor(Yii::$app->helper->hoursToMinutes($totStoppageTime)/(24*60)) * $workhours)
                                          ) / $workhours;*/

                        $stoppageDaysDeduct = 0;
                        if($totStoppageDays)
                            $stoppageDaysDeduct = ($totStoppageDays > 1) ? floor($totStoppageDays) : 1;

                        $totCumDays     = ($diff_minutes/(24*60)) - $stoppageDaysDeduct;
                        $totCumDuration = (floor($totCumDays) * $workhours) + ceil($diff_minutes%(24*60)/60);
                        $reptdDays      = $totCumDuration/$workhours;
                        
                        $actualDuration = round((($reptdDays/$cumqty)*$activityQty)+ $totStoppageDays);

                        if(round($reptdDays) > $actualDuration){
                            $task_start_date = date_create($startDateQuery->task_date);
                            $task_end_date   = date_create($endDateQuery->task_enddate);
                            $diff            = date_diff($task_start_date, $task_end_date);
                            $duration        = $diff->format("%a");

                            $scheduleactivity->duration =  $duration;
                            $scheduleactivity->end_date = date('Y-m-d', strtotime($endDateQuery->task_enddate));
                        }
                        else{
                            $scheduleactivity->duration = $actualDuration;
                            $scheduleactivity->end_date = date('Y-m-d', strtotime($scheduleactivity->start_date. ' + '.$actualDuration.' days'));
                        }
                    }
                }
                if($actualDuration)
                    $no_of_days = $actualDuration;
                else
                    $no_of_days = $scheduleactivity->old_duration;

                //----END-----------------------------------------------------------------------------


                if($start_date!='' && $start_date!='0000-00-00'){
                    $duration = $no_of_days - 1;
                    $scheduleactivity->duration = $no_of_days;
                    $scheduleactivity->start_date = $start_date;
                    $scheduleactivity->end_date = date('Y-m-d', strtotime($start_date. ' + '.$duration.' days'));
                }
                else{
                    $duration = $no_of_days;
                    $scheduleactivity->duration = $no_of_days;
                    $scheduleactivity->start_date = $start_date;
                    $scheduleactivity->end_date = date('Y-m-d', strtotime($start_date. ' + '.$duration.' days'));
                }
                $scheduleactivity->save(false);

                if($actualDuration){
                    $schProgRepLog = ScheduleProgressReportLog::find()
                                    ->where(['activity_id' => $activityid])
                                    ->orderBy(['id'=>SORT_DESC])
                                    ->one();
                    $schProgRepLog->activity_duration    = $actualDuration;
                    $schProgRepLog->last_activity        = Yii::$app->helper->getLongestActivity($scheduleactivity->projectId)['id'];
                    $schProgRepLog->last_activity_date   = Yii::$app->helper->getLongestActivity($scheduleactivity->projectId)['end_date'];
                    $schProgRepLog->save(false);
                }
            }
        }


        $sql = "SELECT a.* FROM scheduleactivities AS a INNER JOIN wbsscheduleitems AS b ON a.scheduleitem_id=b.scheduleitem_id WHERE a.projectId=".$projectid." AND a.status=0 ORDER BY b.sortorder ASC,a.sortorder ASC";
        //echo $sql;exit;
        $command = $connection->createCommand($sql);
        $dataReader = $command->query();
        $availableActivites = $dataReader->readAll();

        if(!empty($availableActivites))
        {       
            foreach ($availableActivites as $activity) 
            {
                $acId = $activity['id'];
                $today = date('Y-m-d');
                $allRelations = ActivityRelations::find()->where(['precedent_activity' => $acId])->andWhere(['status'=>0])->all();
                if(!empty($allRelations))
                {
                    foreach ($allRelations AS $relation)
                    {
                        $precAct = Scheduleactivities::findOne($acId);
                        $dependAc = Scheduleactivities::findOne($relation->dependent_activity);

                        $actreported = ScheduleProgressReport::find()->where(['activity_id' => $dependAc->id])->one();
                        $reportlog = "SELECT SUM(currentqty) AS totalqty FROM schedule_progress_report_log WHERE activity_id='".$dependAc->id."'";
                        $command = $connection->createCommand($reportlog);
                        $dataReader = $command->query();
                        $totalQty = $dataReader->read();
                        $reported = ProgressReport::find()->where(['activity_id'=> $relation->dependent_activity])->andWhere(['status' => 1])->one();
                        $precedentActual = ProgressReport::find()->where(['activity_id'=>$acId])->one();

                        if(!$precedentActual){  
                            $precedentActual = $precAct;
                            $start_date = strtotime($precedentActual->start_date);
                            $end_date = strtotime($precedentActual->end_date);
                            $enDateWillBe =  date('Y-m-d', $end_date);
                        }

                        
                        if($relation->relation_type == 1)//SS
                            {

                                $depRelations = ActivityRelations::find()->where(['dependent_activity' => $dependAc->id])->andWhere(['status'=>0])->all();

                                $scheduleID = '';
                                $scheduleEnddate = '';
                                foreach($depRelations as $depRelation):
                                    $thisAct = Scheduleactivities::findOne($depRelation->precedent_activity);
                                    $thisdepAct = Scheduleactivities::findOne($depRelation->dependent_activity);
                                    
                                    if($depRelation->relation_type == 1){//SS
                                        $deplag = $thisdepAct->lag;
                                        $depduration = $thisdepAct->duration + $deplag - 1;
                                        $depend = date('Y-m-d', strtotime($thisAct->start_date. ' + '.$depduration.' days'));
                                    }
                                    elseif($depRelation->relation_type == 2)//FS
                                    {
                                        $deplag = $dependAc->lag + 1;
                                        $depstart = date('Y-m-d', strtotime($thisAct->end_date. ' + '.$deplag.' days'));
                                        $depduration = $thisdepAct->duration - 1;
                                        $depend = date('Y-m-d', strtotime($depstart. ' + '.$depduration.' days'));
                                    }
                                    elseif($depRelation->relation_type == 3)//FF
                                    {
                                        $deplag = $dependAc->lag;
                                        $depend = date('Y-m-d', strtotime($thisAct->end_date. ' + '.$deplag.' days'));
                                    }

                                    if($scheduleEnddate=='' || $depend > $scheduleEnddate){
                                        //if($depRelation->relation_type==2){
                                            $scheduleID = $thisAct->id;
                                            //$scheduleEnddate = $thisAct->end_date;
                                            $scheduleEnddate = $depend;
                                        //}
                                    }
                                endforeach;

                                if($scheduleID == $precAct->id){

                                    $lag = $dependAc->lag;
                                    $depstart = date('Y-m-d', strtotime($precAct->start_date. ' + '.$lag.' days'));
                                    if($actreported && $totalQty['totalqty']>0){
                                        if($actreported->start_date > $depstart){
                                            $dependAc->start_date = $actreported->start_date;
                                            $dependAc->actual_start_date = date('Y-m-d', strtotime($precAct->actual_start_date. ' + '.$lag.' days'));
                                        }
                                        else{
                                            $dependAc->start_date = date('Y-m-d', strtotime($precAct->start_date. ' + '.$lag.' days'));
                                            $dependAc->actual_start_date = date('Y-m-d', strtotime($precAct->actual_start_date. ' + '.$lag.' days'));
                                        }
                                    }
                                    else{
                                        /*if($today > $depstart){
                                            $dependAc->start_date = $today;
                                            $dependAc->actual_start_date = date('Y-m-d', strtotime($precAct->actual_start_date. ' + '.$lag.' days'));
                                        }
                                        else{
                                            $dependAc->start_date = date('Y-m-d', strtotime($precAct->start_date. ' + '.$lag.' days'));
                                            $dependAc->actual_start_date = date('Y-m-d', strtotime($precAct->actual_start_date. ' + '.$lag.' days'));
                                        }*/

                                        //-----/\/\COMMENTED BY SREEJITH - To avoid the today restrictions-----/\/\------
                                        $dependAc->start_date = date('Y-m-d', strtotime($precAct->start_date. ' + '.$lag.' days'));
                                        $dependAc->actual_start_date = date('Y-m-d', strtotime($precAct->actual_start_date. ' + '.$lag.' days'));

                                    }
                                    //$duration = $dependAc->duration + $lag - 1;
                                    $duration = $dependAc->duration - 1;
                                    $oduration = $dependAc->old_duration - 1;
                                    $dependAc->end_date = date('Y-m-d', strtotime($dependAc->start_date. ' + '.$duration.' days'));
                                    $dependAc->actual_end_date = date('Y-m-d', strtotime($dependAc->actual_start_date. ' + '.$oduration.' days'));
                                    $dependAc->save(false);

                                }

                               // }
                                $lag = $dependAc->lag;
                                
                            }
                            elseif($relation->relation_type == 2)//FS
                            {

                                $depRelations = ActivityRelations::find()->where(['dependent_activity' => $dependAc->id])->andWhere(['status' => 0])->all();

                                $scheduleID = '';
                                $scheduleEnddate = '';
                                foreach($depRelations as $depRelation):
                                    $thisAct = Scheduleactivities::findOne($depRelation->precedent_activity);
                                    $thisdepAct = Scheduleactivities::findOne($depRelation->dependent_activity);
                                    if($depRelation->relation_type == 1){
                                        $deplag = $thisdepAct->lag;
                                        $depduration = $thisdepAct->duration + $deplag - 1;
                                        $depend = date('Y-m-d', strtotime($thisAct->start_date. ' + '.$depduration.' days'));
                                    }
                                    elseif($depRelation->relation_type == 2)
                                    {
                                        $deplag = $dependAc->lag + 1;
                                        $depstart = date('Y-m-d', strtotime($thisAct->end_date. ' + '.$deplag.' days'));
                                        $depduration = $thisdepAct->duration - 1;
                                        $depend = date('Y-m-d', strtotime($depstart. ' + '.$depduration.' days'));
                                    }
                                    elseif($depRelation->relation_type == 3)
                                    {
                                        $deplag = $dependAc->lag;
                                        $depend = date('Y-m-d', strtotime($thisAct->end_date. ' + '.$deplag.' days'));
                                    }
                                    if($scheduleEnddate=='' || $depend > $scheduleEnddate){
                                        //if($depRelation->relation_type==2){
                                            $scheduleID = $thisAct->id;
                                            //$scheduleEnddate = $thisAct->end_date;
                                            $scheduleEnddate = $depend;
                                        //}
                                    }
                                endforeach;

                                if($scheduleID == $precAct->id){

                                    //if(date('Y-m-d', strtotime($precAct->end_date.' + 1 days')) != $dependAc->start_date)
                                    //{
                                        $lead = $dependAc->lag + 1;
                                        $depstart = date('Y-m-d', strtotime($precAct->end_date. ' + '.$lead.' days'));
                                        if($actreported && $totalQty['totalqty']>0){
                                            if($actreported->start_date > $depstart){
                                                $dependAc->start_date = $actreported->start_date;
                                                //$dependAc->actual_start_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                            }
                                            else{
                                                $dependAc->start_date = date('Y-m-d', strtotime($precAct->end_date. ' + '.$lead.' days'));
                                                $dependAc->actual_start_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                            }
                                        }
                                        else{
                                            /*if($today > $depstart){
                                                $dependAc->start_date = $today;
                                                $dependAc->actual_start_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                            }
                                            else{
                                                $dependAc->start_date = $depstart;
                                                $dependAc->actual_start_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                            }*/
                                            //-----/\/\COMMENTED BY SREEJITH - To avoid the today restrictions-----/\/\------
                                            $dependAc->start_date = $depstart;
                                            $dependAc->actual_start_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                        }
                                        $start = date('Y-m-d', strtotime($precAct->end_date));
                                        $duration = $dependAc->duration - 1;
                                        $oduration = $dependAc->old_duration - 1;
                                        $dependAc->end_date = date('Y-m-d', strtotime($dependAc->start_date. ' + '.$duration.' days'));
                                        $dependAc->actual_end_date = date('Y-m-d', strtotime($dependAc->actual_start_date. ' + '.$oduration.' days'));
                                        $dependAc->save(false);
                                    //}  

                                }
                            }
                            elseif($relation->relation_type == 3)//FF
                            {

                                //$depRelations = ActivityRelations::model()->findAll(array('condition'=>'dependent_activity ='.$dependAc->id.' AND relation_type=3 AND status =0'));

                                $depRelations = ActivityRelations::find()->where(['dependent_activity' => $dependAc->id])->andWhere(['status'=> 0])->all();

                                $scheduleID = '';
                                $scheduleEnddate = '';
                                foreach($depRelations as $depRelation):
                                    $thisAct = Scheduleactivities::findOne($depRelation->precedent_activity);
                                    $thisdepAct = Scheduleactivities::findOne($depRelation->dependent_activity);
                                    if($depRelation->relation_type == 1){
                                        $deplag = $thisdepAct->lag;
                                        $depduration = $thisdepAct->duration + $deplag - 1;
                                        $depend = date('Y-m-d', strtotime($thisAct->start_date. ' + '.$depduration.' days'));
                                    }
                                    elseif($depRelation->relation_type == 2)
                                    {
                                        $deplag = $dependAc->lag + 1;
                                        $depstart = date('Y-m-d', strtotime($thisAct->end_date. ' + '.$deplag.' days'));
                                        $depduration = $thisdepAct->duration - 1;
                                        $depend = date('Y-m-d', strtotime($depstart. ' + '.$depduration.' days'));
                                    }
                                    elseif($depRelation->relation_type == 3)
                                    {
                                        $deplag = $dependAc->lag;
                                        $depend = date('Y-m-d', strtotime($thisAct->end_date. ' + '.$deplag.' days'));
                                    }
                                    if($scheduleEnddate=='' || $depend > $scheduleEnddate){
                                        //if($depRelation->relation_type==2){
                                            $scheduleID = $thisAct->id;
                                            //$scheduleEnddate = $thisAct->end_date;
                                            $scheduleEnddate = $depend;
                                        //}
                                    }
                                endforeach;

                                if($scheduleID == $precAct->id){

                                    $lead = $dependAc->lag;
                                    $today = date('Y-m-d');
                                    if($actreported && $totalQty['totalqty']>0){
                                        //$duration = $dependAc->duration + $lead - 1;
                                        $duration = $dependAc->duration - 1;
                                        $oduration = $dependAc->old_duration - 1;
                                        $end_date = date('Y-m-d', strtotime($actreported->start_date. ' + '.$duration.' days'));
                                        $prec_end_date = date('Y-m-d', strtotime($precAct->end_date. ' + '.$lead.' days'));
                                        //$dependAc->duration = $duration + 1;
                                        $dependAc->start_date = $actreported->start_date;
                                        if($end_date>$prec_end_date){
                                            $dependAc->end_date = $end_date;
                                            $actual_end_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                           $dependAc->actual_start_date = date('Y-m-d', strtotime($actual_end_date. ' - '.$oduration.' days'));
                                           $dependAc->actual_end_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                        }
                                        else{
                                            $datediff = strtotime($prec_end_date) - strtotime($actreported->start_date);
                                            $bduration = round($datediff / (60 * 60 * 24)) + 1;
                                            $dependAc->duration = $bduration;
                                            $dependAc->end_date = $prec_end_date;
                                            $actual_end_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                           $dependAc->actual_start_date = date('Y-m-d', strtotime($actual_end_date. ' - '.$oduration.' days'));
                                           $dependAc->actual_end_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                        }
                                        
                                    }
                                    else{

                                        $duration = $dependAc->duration - 1;
                                        $oduration = $dependAc->old_duration - 1;
                                        $end_date = date('Y-m-d', strtotime($precAct->end_date. ' + '.$lead.' days'));
                                        $dependAc->start_date = date('Y-m-d', strtotime($end_date. ' - '.$duration.' days'));
                                        $dependAc->end_date = date('Y-m-d', strtotime($precAct->end_date. ' + '.$lead.' days'));
                                        $actual_end_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                       $dependAc->actual_start_date = date('Y-m-d', strtotime($actual_end_date. ' - '.$oduration.' days'));
                                       $dependAc->actual_end_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));

                                        /*if($today > $dependAc->start_date){
                                            $duration = $dependAc->duration - 1;
                                            $oduration = $dependAc->old_duration - 1;
                                            $end_date = date('Y-m-d', strtotime($today. ' + '.$duration.' days'));
                                            $prec_end_date = date('Y-m-d', strtotime($precAct->end_date. ' + '.$lead.' days'));
                                            if($end_date>$prec_end_date){
                                                $dependAc->start_date = $today;
                                                $dependAc->end_date = $end_date;
                                                $actual_end_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                               $dependAc->actual_start_date = date('Y-m-d', strtotime($actual_end_date. ' - '.$oduration.' days'));
                                               $dependAc->actual_end_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                            }
                                            else{
                                                $datediff = strtotime($prec_end_date) - strtotime($today);
                                                $bduration = round($datediff / (60 * 60 * 24)) + 1;
                                                $dependAc->duration = $bduration;
                                                $dependAc->start_date = $today;
                                                $dependAc->end_date = $prec_end_date;
                                                $actual_end_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                               $dependAc->actual_start_date = date('Y-m-d', strtotime($actual_end_date. ' - '.$oduration.' days'));
                                               $dependAc->actual_end_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                            }
                                        }
                                        else{
                                            $duration = $dependAc->duration - 1;
                                            $oduration = $dependAc->old_duration - 1;
                                            $end_date = date('Y-m-d', strtotime($precAct->end_date. ' + '.$lead.' days'));
                                            $dependAc->start_date = date('Y-m-d', strtotime($end_date. ' - '.$duration.' days'));
                                            $dependAc->end_date = date('Y-m-d', strtotime($precAct->end_date. ' + '.$lead.' days'));
                                            $actual_end_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                           $dependAc->actual_start_date = date('Y-m-d', strtotime($actual_end_date. ' - '.$oduration.' days'));
                                           $dependAc->actual_end_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                        }*/
                                        //-----/\/\COMMENTED BY SREEJITH - To avoid the today restrictions-----/\/\------
                                        $duration = $dependAc->duration - 1;
                                        $oduration = $dependAc->old_duration - 1;
                                        $end_date = date('Y-m-d', strtotime($precAct->end_date. ' + '.$lead.' days'));
                                        $dependAc->start_date = date('Y-m-d', strtotime($end_date. ' - '.$duration.' days'));
                                        $dependAc->end_date = date('Y-m-d', strtotime($precAct->end_date. ' + '.$lead.' days'));
                                        $actual_end_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                       $dependAc->actual_start_date = date('Y-m-d', strtotime($actual_end_date. ' - '.$oduration.' days'));
                                       $dependAc->actual_end_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));

                                    }
                                    $dependAc->save(false);

                                }
                            }
                        //}
                        // var_dump($dependAc);
                    }

                }
                // exit;
                
            }
        }
        $this->GetRelationcorrectfinal($projectid);
    }

   public function GetRelationcorrectfinal($projectid)
    {
        // DEACTIVATED legacy — superseded by the textbook CPM engine in
        // HelperComponent::GetRelationcorrect(). Body below is unreachable.
        return Yii::$app->helper->GetRelationcorrect($projectid);

        $connection = Yii::$app->db;
        $sql = "SELECT a.* FROM scheduleactivities AS a INNER JOIN wbsscheduleitems AS b ON a.scheduleitem_id=b.scheduleitem_id WHERE a.projectId=".$projectid." AND a.status=0 ORDER BY b.sortorder ASC,a.sortorder ASC";
        //echo $sql;exit;
        $command = $connection->createCommand($sql);
        $dataReader = $command->query();
        $availableActivites = $dataReader->readAll();

        $today = date('Y-m-d');

        //if(count($availableActivites) > 0)
        if(!empty($availableActivites))
        {       
            // echo $actID;exit;
            foreach ($availableActivites as $activity) 
            {
                //$acId = $activity->id;
                $acId = $activity['id'];
                //$acId = $activity->precedent_activity;
                $allRelations = ActivityRelations::find()->where(['precedent_activity' => $acId])->andWhere(['status' => 0])->all();
                //if(count($allRelations) > 0)
                if(!empty($allRelations))
                {

                    // echo "test";exit;
                    foreach ($allRelations AS $relation)
                    {

                        $precAct = Scheduleactivities::findone($acId);
                        $dependAc = Scheduleactivities::findOne($relation->dependent_activity);
                        // check if already reported or not
                        $actreported = ScheduleProgressReport::find()->where(['activity_id' => $dependAc->id])->one();
                        $reportlog = "SELECT SUM(currentqty) AS totalqty FROM schedule_progress_report_log WHERE activity_id='".$dependAc->id."'";
                        $command = $connection->createCommand($reportlog);
                        $dataReader = $command->query();
                        $totalQty = $dataReader->read();
                        $reported = ProgressReport::find()->where(['activity_id' => $relation->dependent_activity])->andWhere(['status' => 1])->one();
                        $precedentActual = ProgressReport::find()->where(['activity_id' => $acId])->one();

                        if(!$precedentActual){  
                            $precedentActual = $precAct;
                            $start_date = strtotime($precedentActual->start_date);
                            $end_date = strtotime($precedentActual->end_date);
                            $enDateWillBe =  date('Y-m-d', $end_date);
                        }

                        if($relation->relation_type == 1)
                            {

                                $depRelations = ActivityRelations::find()->where(['dependent_activity' => $dependAc->id])->andWhere(['status' => 0])->all();

                                $scheduleID = '';
                                $scheduleEnddate = '';
                                foreach($depRelations as $depRelation):
                                    $thisAct = Scheduleactivities::findOne($depRelation->precedent_activity);
                                    $thisdepAct = Scheduleactivities::findOne($depRelation->dependent_activity);
                                    if($depRelation->relation_type == 1){
                                        $deplag = $thisdepAct->lag;
                                        $depduration = $thisdepAct->duration + $deplag - 1;
                                        $depend = date('Y-m-d', strtotime($thisAct->start_date. ' + '.$depduration.' days'));
                                    }
                                    elseif($depRelation->relation_type == 2)
                                    {
                                        $deplag = $dependAc->lag + 1;
                                        $depstart = date('Y-m-d', strtotime($thisAct->end_date. ' + '.$deplag.' days'));
                                        $depduration = $thisdepAct->duration - 1;
                                        $depend = date('Y-m-d', strtotime($depstart. ' + '.$depduration.' days'));
                                    }
                                    elseif($depRelation->relation_type == 3)
                                    {
                                        $deplag = $dependAc->lag;
                                        $depend = date('Y-m-d', strtotime($thisAct->end_date. ' + '.$deplag.' days'));
                                    }
                                    if($scheduleEnddate=='' || $depend > $scheduleEnddate){
                                        //if($depRelation->relation_type==2){
                                            $scheduleID = $thisAct->id;
                                            //$scheduleEnddate = $thisAct->end_date;
                                            $scheduleEnddate = $depend;
                                        //}
                                    }
                                endforeach;

                                if($scheduleID == $precAct->id){

                                    $lag = $dependAc->lag;
                                    $depstart = date('Y-m-d', strtotime($precAct->start_date. ' + '.$lag.' days'));
                                    if($actreported && $totalQty['totalqty']>0){
                                        if($actreported->start_date > $depstart){
                                            $dependAc->start_date = $actreported->start_date;
                                            $dependAc->actual_start_date = date('Y-m-d', strtotime($precAct->actual_start_date. ' + '.$lag.' days'));
                                        }
                                        else{
                                            $dependAc->start_date = date('Y-m-d', strtotime($precAct->start_date. ' + '.$lag.' days'));
                                            $dependAc->actual_start_date = date('Y-m-d', strtotime($precAct->actual_start_date. ' + '.$lag.' days'));
                                        }
                                    }
                                    else{
                                        /*if($today > $depstart){
                                            $dependAc->start_date = $today;
                                            $dependAc->actual_start_date = date('Y-m-d', strtotime($precAct->actual_start_date. ' + '.$lag.' days'));
                                        }
                                        else{
                                            $dependAc->start_date = date('Y-m-d', strtotime($precAct->start_date. ' + '.$lag.' days'));
                                            $dependAc->actual_start_date = date('Y-m-d', strtotime($precAct->actual_start_date. ' + '.$lag.' days'));
                                        }*/
                                        //-----/\/\COMMENTED BY SREEJITH - To avoid the today restrictions-----/\/\------
                                        $dependAc->start_date = date('Y-m-d', strtotime($precAct->start_date. ' + '.$lag.' days'));
                                        $dependAc->actual_start_date = date('Y-m-d', strtotime($precAct->actual_start_date. ' + '.$lag.' days'));
                                    }
                                    //$duration = $dependAc->duration + $lag - 1;
                                    $duration = $dependAc->duration - 1;
                                    $oduration = $dependAc->old_duration - 1;
                                    $dependAc->end_date = date('Y-m-d', strtotime($dependAc->start_date. ' + '.$duration.' days'));
                                    $dependAc->actual_end_date = date('Y-m-d', strtotime($dependAc->actual_start_date. ' + '.$oduration.' days'));
                                    $dependAc->save(false);

                                }

                               // }
                                $lag = $dependAc->lag;
                                
                            }
                            elseif($relation->relation_type == 2)
                            {

                                $depRelations = ActivityRelations::find()->where(['dependent_activity' => $dependAc->id])->andWhere(['status' => 0])->all();

                                $scheduleID = '';
                                $scheduleEnddate = '';
                                foreach($depRelations as $depRelation):
                                    $thisAct = Scheduleactivities::findOne($depRelation->precedent_activity);
                                    $thisdepAct = Scheduleactivities::findOne($depRelation->dependent_activity);
                                    if($depRelation->relation_type == 1){
                                        $deplag = $thisdepAct->lag;
                                        $depduration = $thisdepAct->duration + $deplag - 1;
                                        $depend = date('Y-m-d', strtotime($thisAct->start_date. ' + '.$depduration.' days'));
                                    }
                                    elseif($depRelation->relation_type == 2)
                                    {
                                        $deplag = $dependAc->lag + 1;
                                        $depstart = date('Y-m-d', strtotime($thisAct->end_date. ' + '.$deplag.' days'));
                                        $depduration = $thisdepAct->duration - 1;
                                        $depend = date('Y-m-d', strtotime($depstart. ' + '.$depduration.' days'));
                                    }
                                    elseif($depRelation->relation_type == 3)
                                    {
                                        $deplag = $dependAc->lag;
                                        $depend = date('Y-m-d', strtotime($thisAct->end_date. ' + '.$deplag.' days'));
                                    }
                                    if($scheduleEnddate=='' || $depend > $scheduleEnddate){
                                        //if($depRelation->relation_type==2){
                                            $scheduleID = $thisAct->id;
                                            //$scheduleEnddate = $thisAct->end_date;
                                            $scheduleEnddate = $depend;
                                        //}
                                    }
                                endforeach;

                                if($scheduleID == $precAct->id){

                                    //if(date('Y-m-d', strtotime($precAct->end_date.' + 1 days')) != $dependAc->start_date)
                                    //{
                                        $lead = $dependAc->lag + 1;
                                        $depstart = date('Y-m-d', strtotime($precAct->end_date. ' + '.$lead.' days'));
                                        if($actreported && $totalQty['totalqty']>0){
                                            if($actreported->start_date > $depstart){
                                                $dependAc->start_date = $actreported->start_date;
                                                $dependAc->actual_start_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                            }
                                            else{
                                                $dependAc->start_date = date('Y-m-d', strtotime($precAct->end_date. ' + '.$lead.' days'));
                                                $dependAc->actual_start_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                            }
                                        }
                                        else{
                                            /*if($today > $depstart){
                                                $dependAc->start_date = $today;
                                                $dependAc->actual_start_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                            }
                                            else{
                                                $dependAc->start_date = $depstart;
                                                $dependAc->actual_start_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                            }*/
                                            //-----/\/\COMMENTED BY SREEJITH - To avoid the today restrictions-----/\/\------
                                            $dependAc->start_date = $depstart;
                                            $dependAc->actual_start_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));

                                        }
                                        $start = date('Y-m-d', strtotime($precAct->end_date));
                                        $duration = $dependAc->duration - 1;
                                        $oduration = $dependAc->old_duration - 1;
                                        $dependAc->end_date = date('Y-m-d', strtotime($dependAc->start_date. ' + '.$duration.' days'));
                                        $dependAc->actual_end_date = date('Y-m-d', strtotime($dependAc->actual_start_date. ' + '.$oduration.' days'));
                                        $dependAc->save(false);
                                    //}  

                                }
                            }
                            elseif($relation->relation_type == 3)
                            {

                                //$depRelations = ActivityRelations::model()->findAll(array('condition'=>'dependent_activity ='.$dependAc->id.' AND relation_type=3 AND status =0'));

                                $depRelations = ActivityRelations::find()->where(['dependent_activity' => $dependAc->id])->andWhere(['status' => 0])->all();

                                $scheduleID = '';
                                $scheduleEnddate = '';
                                foreach($depRelations as $depRelation):
                                    $thisAct = Scheduleactivities::findOne($depRelation->precedent_activity);
                                    $thisdepAct = Scheduleactivities::findOne($depRelation->dependent_activity);
                                    if($depRelation->relation_type == 1){
                                        $deplag = $thisdepAct->lag;
                                        $depduration = $thisdepAct->duration + $deplag - 1;
                                        $depend = date('Y-m-d', strtotime($thisAct->start_date. ' + '.$depduration.' days'));
                                    }
                                    elseif($depRelation->relation_type == 2)
                                    {
                                        $deplag = $dependAc->lag + 1;
                                        $depstart = date('Y-m-d', strtotime($thisAct->end_date. ' + '.$deplag.' days'));
                                        $depduration = $thisdepAct->duration - 1;
                                        $depend = date('Y-m-d', strtotime($depstart. ' + '.$depduration.' days'));
                                    }
                                    elseif($depRelation->relation_type == 3)
                                    {
                                        $deplag = $dependAc->lag;
                                        $depend = date('Y-m-d', strtotime($thisAct->end_date. ' + '.$deplag.' days'));
                                    }
                                    if($scheduleEnddate=='' || $depend > $scheduleEnddate){
                                        //if($depRelation->relation_type==2){
                                            $scheduleID = $thisAct->id;
                                            //$scheduleEnddate = $thisAct->end_date;
                                            $scheduleEnddate = $depend;
                                        //}
                                    }
                                endforeach;

                                if($scheduleID == $precAct->id){

                                    $lead = $dependAc->lag;
                                    $today = date('Y-m-d');
                                    if($actreported && $totalQty['totalqty']>0){
                                        //$duration = $dependAc->duration + $lead - 1;
                                        $duration = $dependAc->duration - 1;
                                        $oduration = $dependAc->old_duration - 1;
                                        $end_date = date('Y-m-d', strtotime($actreported->start_date. ' + '.$duration.' days'));
                                        $prec_end_date = date('Y-m-d', strtotime($precAct->end_date. ' + '.$lead.' days'));
                                        //$dependAc->duration = $duration + 1;
                                        $actual_end_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                       $dependAc->actual_start_date = date('Y-m-d', strtotime($actual_end_date. ' - '.$oduration.' days'));
                                        $dependAc->actual_end_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                        $dependAc->start_date = $actreported->start_date;
                                        if($end_date>$prec_end_date){
                                            $dependAc->end_date = $end_date;
                                            $actual_end_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                           $dependAc->actual_start_date = date('Y-m-d', strtotime($actual_end_date. ' - '.$oduration.' days'));
                                           $dependAc->actual_end_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                        }
                                        else{
                                            $datediff = strtotime($prec_end_date) - strtotime($actreported->start_date);
                                            $bduration = round($datediff / (60 * 60 * 24)) + 1;
                                            $dependAc->duration = $bduration;
                                            $dependAc->end_date = $prec_end_date;
                                            $actual_end_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                           $dependAc->actual_start_date = date('Y-m-d', strtotime($actual_end_date. ' - '.$oduration.' days'));
                                           $dependAc->actual_end_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                        }
                                        
                                    }
                                    /*elseif(count($actreported)>0 && $totalQty['totalqty']==0){
                                        if($dependAc->actual_start_date > $today){
                                            $duration = $dependAc->duration - 1;
                                            $end_date = date('Y-m-d', strtotime($today. ' + '.$duration.' days'));
                                            $prec_end_date = date('Y-m-d', strtotime($precAct->end_date. ' + '.$lead.' days'));
                                            if($end_date>$prec_end_date){
                                                $dependAc->start_date = $today;
                                                $dependAc->end_date = $end_date;
                                            }
                                            else{
                                                $datediff = strtotime($prec_end_date) - strtotime($today);
                                                $bduration = round($datediff / (60 * 60 * 24)) + 1;
                                                $dependAc->duration = $bduration;
                                                $dependAc->start_date = $today;
                                                $dependAc->end_date = $prec_end_date;
                                            }
                                        }
                                        else{
                                            $duration = $dependAc->duration - 1;
                                            $end_date = date('Y-m-d', strtotime($precAct->end_date. ' + '.$lead.' days'));
                                            $dependAc->start_date = date('Y-m-d', strtotime($end_date. ' - '.$duration.' days'));
                                            $dependAc->end_date = date('Y-m-d', strtotime($precAct->end_date. ' + '.$lead.' days'));
                                        }
                                        //echo $actreported->start_date; exit;
                                    }*/
                                    else{
                                        /*$duration = $dependAc->duration - 1;
                                        $end_date = date('Y-m-d', strtotime($precAct->end_date. ' + '.$lead.' days'));
                                        $dependAc->start_date = date('Y-m-d', strtotime($end_date. ' - '.$duration.' days'));
                                        $dependAc->end_date = date('Y-m-d', strtotime($precAct->end_date. ' + '.$lead.' days'));*/

                                        $duration = $dependAc->duration - 1;
                                        $oduration = $dependAc->old_duration - 1;
                                        $end_date = date('Y-m-d', strtotime($precAct->end_date. ' + '.$lead.' days'));
                                        $dependAc->start_date = date('Y-m-d', strtotime($end_date. ' - '.$duration.' days'));
                                        $dependAc->end_date = date('Y-m-d', strtotime($precAct->end_date. ' + '.$lead.' days'));
                                        $actual_end_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                        $dependAc->actual_start_date = date('Y-m-d', strtotime($actual_end_date. ' - '.$oduration.' days'));
                                        $dependAc->actual_end_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                        
                                        /*if($today > $dependAc->start_date){
                                            $duration = $dependAc->duration - 1;
                                            $end_date = date('Y-m-d', strtotime($today. ' + '.$duration.' days'));
                                            $prec_end_date = date('Y-m-d', strtotime($precAct->end_date. ' + '.$lead.' days'));
                                            if($end_date>$prec_end_date){
                                                $dependAc->start_date = $today;
                                                $dependAc->end_date = $end_date;
                                                $actual_end_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                                $dependAc->actual_start_date = date('Y-m-d', strtotime($actual_end_date. ' - '.$oduration.' days'));
                                                $dependAc->actual_end_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                            }
                                            else{
                                                $datediff = strtotime($prec_end_date) - strtotime($today);
                                                $bduration = round($datediff / (60 * 60 * 24)) + 1;
                                                $dependAc->duration = $bduration;
                                                $dependAc->start_date = $today;
                                                $dependAc->end_date = $prec_end_date;
                                                $actual_end_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                                $dependAc->actual_start_date = date('Y-m-d', strtotime($actual_end_date. ' - '.$oduration.' days'));
                                                $dependAc->actual_end_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                            }
                                        }
                                        else{
                                            $duration = $dependAc->duration - 1;
                                            $end_date = date('Y-m-d', strtotime($precAct->end_date. ' + '.$lead.' days'));
                                            $dependAc->start_date = date('Y-m-d', strtotime($end_date. ' - '.$duration.' days'));
                                            $dependAc->end_date = date('Y-m-d', strtotime($precAct->end_date. ' + '.$lead.' days'));
                                            $actual_end_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                            $dependAc->actual_start_date = date('Y-m-d', strtotime($actual_end_date. ' - '.$oduration.' days'));
                                            $dependAc->actual_end_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                        }*/
                                        //-----/\/\COMMENTED BY SREEJITH - To avoid the today restrictions-----/\/\------
                                        $duration = $dependAc->duration - 1;
                                        $end_date = date('Y-m-d', strtotime($precAct->end_date. ' + '.$lead.' days'));
                                        $dependAc->start_date = date('Y-m-d', strtotime($end_date. ' - '.$duration.' days'));
                                        $dependAc->end_date = date('Y-m-d', strtotime($precAct->end_date. ' + '.$lead.' days'));
                                        $actual_end_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));
                                        $dependAc->actual_start_date = date('Y-m-d', strtotime($actual_end_date. ' - '.$oduration.' days'));
                                        $dependAc->actual_end_date = date('Y-m-d', strtotime($precAct->actual_end_date. ' + '.$lead.' days'));

                                        
                                    }
                                    $dependAc->save(false);

                                }
                            }
                        //}
                        // var_dump($dependAc);
                    }

                }
                // exit;
                
            }
        }
    }

    public function actionActivityrelation()
    {
        $uid = Yii::$app->user->Id; 
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if($projuser)
        {
            $prjct_id = $projuser->projectid;
            //$scheduleItems = Wbsscheduleitems::find()->where(['projectId' => $prjct_id])->andWhere(['status' => 0])->all();

            $connection = Yii::$app->db;
            $sql="  SELECT a.scheduleitem_id,a.wbsid,a.name,a.projectId 
                    FROM wbsscheduleitems AS a
                    INNER JOIN workgroups_new AS b ON  a.wbsid=b.Workgroup_Id
                    WHERE  a.projectId='".$prjct_id."' AND b.status=0  ORDER BY b.sortorder  ASC";




            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $scheduleItems=$dataReader->readAll();

            $scheduleItemSelectBox = '<option value="">Select Schedule Item</option>';
            foreach($scheduleItems as $key => $data) {
                $selected = "";
                if(isset($_POST['filter_schedule_item']) && $data['scheduleitem_id'] == $_POST['filter_schedule_item'])
                    $selected = "selected";
                $scheduleItemSelectBox .= '<option value="'.$data['scheduleitem_id'].'" '.$selected.'>'.$data['name'].'</option>';
            }

            $scheduleactivities =Scheduleactivities::find()->where(['projectId' => $prjct_id])->andWhere(['status' => 0])->orderBy(['id' => SORT_ASC])->all();
            $scheduleActivitySelectBox = '<option value="">Select Activity</option>';
            foreach($scheduleactivities as $key => $data) {
                $selected = "";
                if(isset($_POST['filter_schedule_activity']) && $data['id'] == $_POST['filter_schedule_activity'])
                    $selected = "selected";
                $scheduleActivitySelectBox .= '<option value="'.$data['id'].'" '.$selected.'>'.$data['name'].'</option>';
            }


            $datarows='';
            $datarows.='<div class="row relationlist">
    					<div class="col-md-5">
                            <div class="card relations-card">
                                <div class="card-header">
                                    <h2><span class="icon-directions_run"></span> Precedent Activity</h2>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label> Item</label>
                                            <div class="list-group">';
                                            foreach($scheduleItems as $key => $data) {
                                                if($key == 0){
                                                    $selecteditemone = $data['scheduleitem_id'];
                                                    $activeNow = 'active';
                                                }else{
                                                    $activeNow = '';
                                                }
                                                $datarows.='<a href="javascript:void(0);" class="list-group-item schedule_item_first '.$activeNow.'" id="PrecedentScheduleItem-'.$prjct_id.'" data-v="'.$data['scheduleitem_id'].'">'.$data['name'].'</a>';
                                            }
                                $datarows.='<span class="error" id="first_item_error" style="display: none;">Please select an Item</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label> Activity</label>
                                            <div class="list-group" id="schedule_activity_first-data">'; 
                                foreach($scheduleItems as $key => $data) {
                                    if($key == 0){
                                    $Wbsscheduleitems=Wbsscheduleitems::find()->where(['scheduleitem_id' => $data['scheduleitem_id']])->andWhere(['status' => 0])->one();
                                    //$worktypes=Scheduleactivities::find()->where(['scheduleitem_id' => $data['scheduleitem_id']])->andWhere(['projectId' => $Wbsscheduleitems['projectId']])->andWhere(['status' => 0])->orderBy(['id' => SORT_ASC])->all();
                                    
                                   $sql = "SELECT a.*, b.wbs_id
                                            FROM scheduleactivities AS a 
                                            LEFT JOIN workgroup_activities_new AS b ON  a.activity_id=b.id 
                                            WHERE a.scheduleitem_id='".$data['scheduleitem_id']."' 
                                            AND a.projectId='".$Wbsscheduleitems['projectId']."' AND a.status=0  ORDER BY b.sortorder ASC";
                                    $worktypes = $connection->createCommand($sql)->query()->readAll();


                                    foreach($worktypes AS $worktype):
                                        $workgroupact = WorkgroupActivitiesNew::find()->where(['id' => $worktype['reference_activityid']])->andWhere(['wbs_id' => $Wbsscheduleitems->reference_itemid])->andWhere(['schedule' => 1])->andWhere(['pricing_status' => 0])->one();
                                        if(!empty($workgroupact)){
                                            $datarows.='<a href="javascript:void(0);" class="list-group-item schedule_activity_first" id="PrecedentScheduleactivity" data-v="'.$worktype['id'].'">'.$worktype['name'].'</a>';
                                        }
                                        elseif(empty($workgroupact) && $worktype['reference_activityid']==0){
                                            $datarows.='<a href="javascript:void(0);" class="list-group-item schedule_activity_first" id="PrecedentScheduleactivity" data-v="'.$worktype['id'].'">'.$worktype['name'].'</a>';
                                        }
                                    endforeach; 
                                    }
                                }                   
                                $datarows.='</div>
                                            <span class="error" id="first_activity_error" style="display: none;">Please select an Activity</span>
                                        </div>
                                    </div>  
                                </div>
                            </div>     
                        </div>
                        <div class="col-md-5 dependent-activity-wrpr">			
                            <div class="card relations-card">
                                <div class="card-header">
                                    <h2><span class="icon-directions_run"></span> Dependent Activity</h2>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label> Item</label>
                                            <div class="list-group">';
                                            foreach($scheduleItems as $key => $data) {
                                                if($key == 0){
                                                    $selecteditemtwo = $data['scheduleitem_id'];
                                                    $activeNow = 'active';
                                                }else{
                                                    $activeNow = '';
                                                }
                                                $datarows.='<a href="javascript:void(0);" class="list-group-item schedule_item_second '.$activeNow.'" id="DependentScheduleItem-'.$prjct_id.'" data-v="'.$data['scheduleitem_id'].'">'.$data['name'].'</a>';
                                            }
                                $datarows.='<span class="error" id="second_item_error" style="display: none;">Please select an Item</span></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label> Activity</label>
                                            <div class="list-group" id="schedule_activity_second-data">';  
                                            foreach($scheduleItems as $key => $data) {
                                                if($key == 0){
                                                $Wbsscheduleitems=Wbsscheduleitems::find()->where(['scheduleitem_id' => $data['scheduleitem_id']])->andWhere(['status' => 0])->one();

                                                /*$worktypes = Scheduleactivities::find()
                                                            ->where(['scheduleitem_id' => $data['scheduleitem_id']])
                                                            ->andWhere(['projectId' => $Wbsscheduleitems['projectId']])
                                                            ->andWhere(['status' => 0])
                                                            ->orderBy(['id' => SORT_ASC])
                                                            ->all();*/

                                               $sql = "SELECT a.*, b.wbs_id
                                                        FROM scheduleactivities AS a 
                                                        LEFT JOIN workgroup_activities_new AS b ON  a.activity_id=b.id 
                                                        WHERE a.scheduleitem_id='".$data['scheduleitem_id']."' 
                                                        AND a.projectId='".$Wbsscheduleitems['projectId']."' AND a.status=0  ORDER BY b.sortorder ASC";
                                                $worktypes = $connection->createCommand($sql)->query()->readAll();

                                                
                                                foreach($worktypes AS $worktype):
                                                    $workgroupact = WorkgroupActivitiesNew::find()->where(['id' => $worktype['reference_activityid']])->andWhere(['wbs_id' => $Wbsscheduleitems->reference_itemid])->andWhere(['schedule' => 1])->andWhere(['pricing_status' => 0])->one();
                                                    if(!empty($workgroupact)){
                                                        $datarows.='<a href="javascript:void(0);" class="list-group-item schedule_activity_second" id="DependentScheduleactivity" data-v="'.$worktype['id'].'">'.$worktype['name'].'</a>';
                                                    }
                                                    elseif(empty($workgroupact) && $worktype['reference_activityid']==0){
                                                        $datarows.='<a href="javascript:void(0);" class="list-group-item schedule_activity_second" id="DependentScheduleactivity" data-v="'.$worktype['id'].'">'.$worktype['name'].'</a>';
                                                    }
                                                endforeach; 
                                                }
                                            } 
                                        
                                    $datarows.='</div>
                                            <span class="error" id="second_activity_error" style="display: none;">Please select an Activity</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        <div class="col-md-2">			
                            <div class="card relations-card">
                                <div class="card-header">
                                    <h2><span class="icon-link1"></span> Relation</h2>
                                </div>
                                <div class="card-body">
                                    <div class="radio">
                                        <label><input type="radio" class="relation_type" name="relation_type" value=1>SS <span style="font-size: 11px;"> (Start to Start)</span></label>
                                    </div>
                                    <div class="radio">
                                        <label><input type="radio" class="relation_type" name="relation_type" value=2>FS <span style="font-size: 11px;"> (Finish to Start)</span></label>
                                    </div>
                                    <div class="radio">
                                        <label><input type="radio" class="relation_type" name="relation_type" value=3>FF <span style="font-size: 11px;"> (Finish to Finish)</span></label>
                                    </div>
                                    <span class="error" id="relation_error" style="display: none;">Please Select a Relation</span>
                                    <label>&nbsp;</label>
                                    <div class="form-group">
                                        <label>Lag/ Lead</label>
                                        <input type="text" class="form-control lag" id="lag" name="lag" value="" placeholder="Lag/ Lead">
                                    </div>
                                    <div class="text-center">
                                        <label>&nbsp;</label>
                                        <input type="hidden" id="schedule_item_first-new" value="">
                                        <input type="hidden" id="schedule_activity_first-new" value="">
                                        <input type="hidden" id="schedule_item_second-new" value="">
                                        <input type="hidden" id="schedule_activity_second-new" value="">
                                        <input type="hidden" id="relation_type-new" value="">
                                        <button type="button" class="btn btn-primary save_relation_new" value="'.$data['scheduleitem_id'].'" id="saverelationbutton'.$data['scheduleitem_id'].'"><span class="icon-check"></span>Save &amp; Add</button>
                                        <button type="button" class="btn btn-danger cancel" id=""><span class="icon-close"></span> Close</button>
                                    </div>  
                                </div>
                            </div> 
                        </div>
                    </div>';
            if(!empty($_POST['Relactionename'])){
                //$relations = ActivityRelations::find()->where(['projectId' => $prjct_id])->andWhere(['like','relation_type',$_POST['Relactionename']])->andWhere(['status' => 0])->orderBy(['id' => SORT_DESC])->all();
                $relations=ActivityRelations::find()->select(['activity_relations.*'])
                    ->innerJoin('wbsscheduleitems AS b', 'b.scheduleitem_id  = activity_relations.precedent_schedule_item')
                    ->andwhere(['activity_relations.projectId' => $prjct_id])->andWhere(['like','b.name',$_POST['Relactionename']])
                    ->andWhere(['activity_relations.status' => 0])->orderBy(['activity_relations.sortorder' => SORT_ASC])->all();
            }else{
                $relations = ActivityRelations::find()->where(['projectId' => $prjct_id])->andWhere(['status' => 0]);
                if(!empty($_POST['filter_schedule_item'])){
                    $relations->andWhere(['or',['precedent_schedule_item' => $_POST['filter_schedule_item']], ['dependent_schedule_item' => $_POST['filter_schedule_item']]]);
                }
                if(!empty($_POST['filter_schedule_activity'])){
                    $relations->andWhere(['or',['precedent_activity' => $_POST['filter_schedule_activity']], ['dependent_activity' => $_POST['filter_schedule_activity']]]);
                }
                $relations = $relations->orderBy(['sortorder' => SORT_ASC])->all();
            }
             $html = '';
            if(!empty($relations))
            {
                $sino = 1;
                $html .='<div class="row relheads">
                            <div class="col-md-1">
                                <label>#</label>
                            </div>
                            <div class="col-md-2">
                                <label>Schedule Item</label>
                            </div>
                            <div class="col-md-2">
                                <label>Precedent Activity</label>
                            </div>
                            <div class="col-md-2">
                                <label>Schedule Item</label>
                            </div>
                            <div class="col-md-2">
                                <label>Dependent Activity</label>
                            </div>
                            <div class="col-md-1">
                                <label class="desktoponly">Relation</label>
                                <label class="phoneonly">Rel</label>
                            </div>
                            <div class="col-md-1">
                                <label>Lag/Lead</label>
                            </div>
                            <div class="col-md-1">
                            </div>
                         </div>';
                foreach($relations AS $key => $relation)
                {
                    $typesval ='';
                    $precedentactivity ='';
                    $dependentitem ='';
                    $dependentactivity ='';
                    
                    $precedentItem = Wbsscheduleitems::findOne($relation['precedent_schedule_item'])->name;
                    $allprecedentItems = Wbsscheduleitems::find()->where(['projectId' => $prjct_id])->andWhere(['status' => 0])->all();
                    foreach ($allprecedentItems AS $key => $allprecedentItem):
                        if ($precedentItem == $allprecedentItem->name):
                            $seletedtype = 'Selected="selected"';
                        else:
                            $seletedtype = '';
                        endif;
                        $typesval .= "<option value='" . $allprecedentItem->scheduleitem_id . "' " . $seletedtype . ">" . $allprecedentItem->name . "</option>";
                    endforeach;
                    $precedentActivity=Scheduleactivities::findOne($relation['precedent_activity'])->name;
                    $allprecedentActivitys = Scheduleactivities::find()->where(['projectId' => $prjct_id])->andWhere(['scheduleitem_id' => $relation->precedent_schedule_item])->andWhere(['status' => 0])->all();
                    foreach ($allprecedentActivitys AS $key => $allprecedentActivity):
                        if ($precedentActivity == $allprecedentActivity->name):
                            $seletedtype = 'Selected="selected"';
                        else:
                            $seletedtype = '';
                        endif;
                        $precedentactivity .= "<option value='" . $allprecedentActivity->id . "' " . $seletedtype . ">" . $allprecedentActivity->name . "</option>";
                    endforeach;
                    $dependentItem = Wbsscheduleitems::findOne($relation['dependent_schedule_item'])->name;
                    $alldependentItems = Wbsscheduleitems::find()->where(['projectId' => $prjct_id])->andWhere(['status' => 0])->all();
                    foreach ($alldependentItems AS $key => $alldependentItem):
                        if ($dependentItem == $alldependentItem->name):
                            $seletedtype = 'Selected="selected"';
                        else:
                            $seletedtype = '';
                        endif;
                        $dependentitem .= "<option value='" . $alldependentItem->scheduleitem_id . "' " . $seletedtype . ">" . $alldependentItem->name . "</option>";
                    endforeach;
                    $dependentActivity= Scheduleactivities::findOne($relation['dependent_activity'])->name;
                    $alldependentActivitys = Scheduleactivities::find()->where(['projectId' => $prjct_id])->andWhere(['scheduleitem_id' => $relation->dependent_schedule_item])->andWhere(['status' => 0])->all();
                    foreach ($alldependentActivitys AS $key => $alldependentActivity):
                        if ($dependentActivity == $alldependentActivity->name):
                            $seletedtype = 'Selected="selected"';
                        else:
                            $seletedtype = '';
                        endif;
                        $dependentactivity .= "<option value='" . $alldependentActivity->id . "' " . $seletedtype . ">" . $alldependentActivity->name . "</option>";
                    endforeach;
                    $ssselected = '';
                    $fsselected = '';
                    $ffselected = '';
                    if($relation['relation_type'] == 1)
                    {
                        $ssselected = 'selected';
                        $reType = 'SS';
                    }
                    elseif($relation['relation_type'] == 2)
                    {
                        $fsselected = 'selected';
                        $reType = 'FS';
                    }
                    elseif($relation['relation_type'] == 3)
                    {
                        $ffselected = 'selected';
                        $reType = 'FF';
                    }
                    $Activitylag = Scheduleactivities::findOne($relation['dependent_activity'])->lag;
                    $reported = ScheduleProgressReport::find()->where(['activity_id' => $relation['dependent_activity']])->andWhere(['!=', 'cumulated_qty', 0])->all();
                       if(!$reported){
                         $relationstyle = 'title="Delete Relation" ';
                       }
                       else{
                         //$relationstyle = 'disabled';
                         $relationstyle = 'disabled style="pointer-events: none; cursor: not-allowed;" title="Already Reported!"';
                       }

                    $prec_item = Wbsscheduleitems::findOne($relation['precedent_schedule_item']);
                    $dep_item = Wbsscheduleitems::findOne($relation['dependent_schedule_item']);
                    $prec_actvty = Scheduleactivities::findOne($relation['precedent_activity']);
                    $dep_actvty = Scheduleactivities::findOne($relation['dependent_activity']);
                    if($prec_item->status==0 && $dep_item->status==0 && $prec_actvty->status==0 && $dep_actvty->status==0){
                        $html .= '
                        <div class="row relationlist" id="relationrow'.$relation['id'].'" style="cursor: pointer;min-height: 100px;" data-id="'.$relation['id'].'">
                                    <div class="col-md-1">
                                        <span class="number">'.($sino++).'</span>
                                        <input type="hidden" value="'.$relation['id'].'">
                                    </div>
                                    <div class="col-md-2 type">
                                        
                                        <span id="precedentitem'.$relation['id'].'">'.$precedentItem.'</span>
                                        <select class="form-control editrelationprecedentitem" type="text" data-id='.$relation['id'].' id="editrelationprecedentitem'.$relation['id'].'" style="display:none;">
                                        <option value="">Select Precedent Schedule Item</option>'.$typesval.'</select>
                                        <span class="error" id="relationprecedentitem_error'.$relation['id'].'"></span>
                                    </div>
                                    <div class="col-md-2 type">
                                        
                                        <span id="precedentactivity'.$relation['id'].'">'.$precedentActivity.'</span>
                                        <select class="form-control editrelationprecedentactivity" type="text" id="editrelationprecedentactivity'.$relation['id'].'" style="display:none;">
                                        <option value="">Select Precedent Activity</option>'.$precedentactivity.'</select>
                                        <span class="error" id="relationprecedentactivity_error'.$relation['id'].'"></span>
                                    </div>
                                    <div class="col-md-2 type">
                                        
                                        <span id="dependentitem'.$relation['id'].'">'.$dependentItem.'</span>
                                        <select class="form-control editrelationdependentitem" type="text" data-id='.$relation['id'].' id="editrelationdependentitem'.$relation['id'].'" style="display:none;">
                                        <option value="">Select Dependent Schedule Item</option>'.$dependentitem.'</select>
                                        <span class="error" id="relationdependentitem_error'.$relation['id'].'"></span>
                                    </div>
                                    <div class="col-md-2 type">
                                        
                                        <span id="dependentactivity'.$relation['id'].'">'.$dependentActivity.'</span>
                                        <select class="form-control editrelationdependentactivity" type="text" id="editrelationdependentactivity'.$relation['id'].'" style="display:none;">
                                        <option value="">Select Dependent Activity</option>'.$dependentactivity.'</select>
                                        <span class="error" id="relationdependentactivity_error'.$relation['id'].'"></span>
                                    </div>
                                    <div class="col-md-1 type">
                                        
                                        <span id="relationtype'.$relation['id'].'">'.$reType.'</span>
                                        <select class="form-control editrelationrelationtype" type="text" data-id="'.$relation['id'].'" id="editrelationrelationtype'.$relation['id'].'" style="display:none;">
                                        <option value=1 '.$ssselected.'>SS</option><option value=2 '.$fsselected.'>FS</option><option value=3 '.$ffselected.'>FF</option>
                                        </select>
                                        <span class="error" id="relationrelationtype_error'.$relation['id'].'"></span>
                                    </div>
                                    <div class="col-md-1 type">
                                        <label class="desktoponly"></label>
                                        <label class="phoneonly">Lg/Ld</label>
                                        <span id="lag'.$relation['id'].'">'.$Activitylag.'</span>
                                        <input type="text" class="form-control editlag" id="editlag'.$relation['id'].'" name="lag_'.$relation['id'].'" value="'.$Activitylag.'" style="display:none;" >
                                    </div>
                                    <div class="col-md-1 icon-groups" style="bottom:14px;">				
                                       
                                        
                                        <a class="btn btn-primary icon-pencil editrelation" data-v="'.$relation['id'].'" value="'.$relation['id'].'" id="editrelationbutton'.$relation['id'].'" title="Edit Relation" href="javascript:void(0);"></a>
                                        <a class="btn btn-primary icon-save saveeditrelation" data-v="'.$relation['id'].'" value="'.$relation['id'].'" id="saveeditrelationbutton'.$relation['id'].'" title="Save" style="display:none;" href="javascript:void(0);"></a>
                                        <a class="btn btn-danger  icon-trash1 deleterelation" data-v="'.$relation['id'].'" value="'.$relation['id'].'"  id="deleterelationbutton'.$relation['id'].'" '.$relationstyle.' href="javascript:void(0);"></a>
                                    </div>
                                </div>

                        ';
                    }
                }
            }
            else{
                $html.='<div class="row"><div class="col-md-12"><div class="text-center">No Relations Found</div></div></div>';
            }
            // 
               
            $arr = array(   'result' => $datarows,
                            'relationList' => $html,
                            'error'=>'No',
                            'selecteditemone' => $selecteditemone,
                            'selecteditemtwo' => $selecteditemtwo,
                            'scheduleItemSelectBox' => $scheduleItemSelectBox,
                            'scheduleActivitySelectBox' => $scheduleActivitySelectBox
                        );
            return json_encode($arr);

        }
        else{

            $datarows = '<div style="text-align: center;">No Project Selected</div>';

            $arr = array('result' => $datarows,'error'=>'No');
            return json_encode($arr);

        }
    }

    public function actionUpdaterelationsort()
    {
       if(isset($_POST['datavalue']))
        {

            foreach($_POST['datavalue'] AS $data)
            {
                //print_r($data);exit;
                $activities=ActivityRelations::findOne($data['rowid']);
                $activities->sortorder=$data['rowindex'];
                $activities->save(false);
            }
        } 
    }

    public function actionGetscheduleactivity()
    {
        $Wbsscheduleitems=Wbsscheduleitems::find()->where(['scheduleitem_id' => $_POST['scheduleItem']])->andWhere(['status' => 0])->one();
        $worktypes=Scheduleactivities::find()->where(['scheduleitem_id' => $_POST['scheduleItem']])->andWhere(['projectId' => $Wbsscheduleitems['projectId']])->andWhere(['status' => 0])->orderBy(['sortorder' => SORT_ASC])->all();
        $list='<option value="">Select Precedent Activity</option>';
        foreach($worktypes AS $worktype):
            $workgroupact = WorkgroupActivitiesNew::find()->where(['id' => $worktype['reference_activityid']])->andWhere(['wbs_id' => $Wbsscheduleitems->reference_itemid])->andWhere(['schedule' => 1])->andWhere(['pricing_status' => 0])->one();
            if(!empty($workgroupact)){
              $list.='<option value="'.$worktype['id'].'">'.$worktype['name'].'</option>';
            }
            elseif(empty($workgroupact) && $worktype['reference_activityid']==0){
              $list.='<option value="'.$worktype['id'].'">'.$worktype['name'].'</option>';
            }
            //$list.='<option value="'.$worktype['id'].'">'.$worktype['name'].'</option>';
        endforeach;
        $arr=array('result'=>$list,'error'=>'No');
        return json_encode($arr);
    }

    public function actionGetscheduleactivityone()
    {
        $Wbsscheduleitems=Wbsscheduleitems::find()->where(['scheduleitem_id' => $_POST['scheduleItem']])->andWhere(['status' => 0])->one();
       // $worktypes=Scheduleactivities::find()->where(['scheduleitem_id' => $_POST['scheduleItem']])->andWhere(['projectId' => $Wbsscheduleitems['projectId']])->andWhere(['status' => 0])->orderBy(['id' => SORT_ASC])->all();

        $connection = \Yii::$app->db;
        $worktypesnew="SELECT a.reference_activityid,a.id,a.name FROM scheduleactivities AS a
                INNER JOIN workgroup_activities_new AS b ON  a.activity_id=b.id
                WHERE a.scheduleitem_id='".$_POST['scheduleItem']."' AND a.projectId='".$Wbsscheduleitems['projectId']."' AND a.status=0  ORDER BY b.sortorder ASC";

 

        $command = $connection->createCommand($worktypesnew);
        $dataReader = $command->query();
        $worktypes = $dataReader->readAll();

  

        $list='';
        foreach($worktypes AS $worktype):
            $workgroupact = WorkgroupActivitiesNew::find()->where(['id' => $worktype['reference_activityid']])->andWhere(['wbs_id' => $Wbsscheduleitems->reference_itemid])->andWhere(['schedule' => 1])->andWhere(['pricing_status' => 0])->one();
            if(!empty($workgroupact)){
              $list.='<a href="javascript:void(0);" class="list-group-item schedule_activity_first" id="PrecedentScheduleactivity" data-v="'.$worktype['id'].'">'.$worktype['name'].'</a>';
            }
            elseif(empty($workgroupact) && $worktype['reference_activityid']==0){
              $list.='<a href="javascript:void(0);" class="list-group-item schedule_activity_first" id="PrecedentScheduleactivity" data-v="'.$worktype['id'].'">'.$worktype['name'].'</a>';
            }
            //$list.='<option value="'.$worktype['id'].'">'.$worktype['name'].'</option>';
        endforeach;
        $arr=array('result'=>$list,'error'=>'No');
        return json_encode($arr);
    }

    public function actionGetscheduleactivitytwo()
    {
        $Wbsscheduleitems=Wbsscheduleitems::find()->where(['scheduleitem_id' => $_POST['scheduleItem']])->andWhere(['status' => 0])->one();
        //$worktypes=Scheduleactivities::find()->where(['scheduleitem_id' => $_POST['scheduleItem']])->andWhere(['projectId' => $Wbsscheduleitems['projectId']])->andWhere(['status' => 0])->orderBy(['id' => SORT_ASC])->all();


        $connection = \Yii::$app->db;
        $worktypesnez="SELECT a.reference_activityid,a.id,a.name FROM scheduleactivities AS a
                INNER JOIN workgroup_activities_new AS b ON  a.activity_id=b.id
                WHERE a.scheduleitem_id='".$_POST['scheduleItem']."' AND a.projectId='".$Wbsscheduleitems['projectId']."' AND a.status=0  ORDER BY b.sortorder ASC";

 

        $command = $connection->createCommand($worktypesnez);
        $dataReader = $command->query();
        $worktypes = $dataReader->readAll();


        $list='';
        foreach($worktypes AS $worktype):
            $workgroupact = WorkgroupActivitiesNew::find()->where(['id' => $worktype['reference_activityid']])->andWhere(['wbs_id' => $Wbsscheduleitems->reference_itemid])->andWhere(['schedule' => 1])->andWhere(['pricing_status' => 0])->one();
            if(!empty($workgroupact)){
              $list.='<a href="javascript:void(0);" class="list-group-item schedule_activity_second" id="DependentScheduleactivity" data-v="'.$worktype['id'].'">'.$worktype['name'].'</a>';
            }
            elseif(empty($workgroupact) && $worktype['reference_activityid']==0){
              $list.='<a href="javascript:void(0);" class="list-group-item schedule_activity_second" id="DependentScheduleactivity" data-v="'.$worktype['id'].'">'.$worktype['name'].'</a>';
            }
            //$list.='<option value="'.$worktype['id'].'">'.$worktype['name'].'</option>';
        endforeach;
        $arr=array('result'=>$list,'error'=>'No');
        return json_encode($arr);
    }

    public function actionSaverelation()
    {

        $connection = Yii::$app->db;
        $uid = Yii::$app->user->Id; 
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        $_POST['projectId'] = $projectid = $projuser->projectid;

        $sql = "SELECT * FROM scheduleactivities AS a INNER JOIN wbsscheduleitems AS b ON  a.scheduleitem_id=b.scheduleitem_id WHERE a.projectId='".$_POST['projectId']."' AND a.status=0 AND b.status=0 ORDER BY end_date DESC";
        $command = $connection->createCommand($sql);
        $dataReader = $command->query();
        $activityend = $dataReader->read();

        $sql = "SELECT * FROM scheduleactivities AS a INNER JOIN wbsscheduleitems AS b ON  a.scheduleitem_id=b.scheduleitem_id WHERE a.projectId='".$_POST['projectId']."' AND a.status=0 AND b.status=0 ORDER BY start_date ASC";
        $command = $connection->createCommand($sql);
        $dataReader = $command->query();
        $activitystart = $dataReader->read();

        $datediff = strtotime($activityend['end_date']) - strtotime($activitystart['start_date']);
        $duration_first = round($datediff / (60 * 60 * 24)) + 1;

        $project = Projects::findOne($_POST['projectId']); 

        $projdur = $project->duration;

        //echo $duration_first; exit;

        if($projdur >= $duration_first){

            $model = New ActivityRelations();
            $model->precedent_schedule_item=$_POST['firstItem'];
            $model->precedent_activity=$_POST['firstActivity'];
            $model->dependent_schedule_item=$_POST['secondItem'];
            $model->dependent_activity=$_POST['secondActivity'];
            $model->relation_type=$_POST['relationType'];
            // Derive projectId from the actual activity, not from user's active project selection,
            // to prevent cross-project contamination when the user's active project doesn't match.
            $precedentAct = Scheduleactivities::findOne((int)$_POST['firstActivity']);
            $model->projectId = $precedentAct ? $precedentAct->projectId : $projectid;
            if($precedentAct->start_date=='' && $precedentAct->end_date==''){
                $getDateAfterHoliday = Yii::$app->helper->getDateAfterHoliday(date("d-m-Y"), $projectid);
                $today               = date('Y-m-d', strtotime($getDateAfterHoliday));

                $precedentAct->start_date        = $today;
                $precedentAct->end_date          = $today;
                $precedentAct->actual_start_date = $today;
                $precedentAct->actual_end_date   = $today;
                $precedentAct->duration          = 1;
                $precedentAct->save(false);
            }
            $dependentAct = Scheduleactivities::findOne($_POST['secondActivity']);
            if($_POST['lag']==''){
                $_POST['lag'] = 0;
            }
            $lag = $_POST['lag'];
            $duration                   = $dependentAct->duration + $lag - 1;


            //$dependentAct->start_date   = date('Y-m-d', strtotime($precedentAct->start_date. ' + '.$lag.' days'));
            //$dependentAct->end_date     = date('Y-m-d', strtotime($precedentAct->start_date. ' + '.$duration.' days'));

            // Preserve original baseline before relation shifts the start date
            if (!$dependentAct->act_start_date || $dependentAct->act_start_date == '0000-00-00') {
                $dependentAct->act_start_date = $dependentAct->start_date ?: date('Y-m-d');
            }

            $dependentAct->start_date   = date('Y-m-d',
                                                strtotime(
                                                    Yii::$app->helper->getDateAfterHoliday($precedentAct->start_date, $projectid, $lag)
                                                )
                                              );
            $dependentAct->end_date   = date('Y-m-d',
                                                strtotime(
                                                    Yii::$app->helper->getDateAfterHoliday(
                                                        $precedentAct->start_date,
                                                        $projectid,
                                                        ($dependentAct->duration + $lag)
                                                    )
                                                )
                                            );

            $dependentAct->lag          = $_POST['lag'];
            $dependentAct->save(false);
            $availableActivites = Scheduleactivities::find()->where(['projectId' => $_POST['projectId']])->andWhere(['status' => 0])->orderBy(['scheduleitem_id' => SORT_ASC])->all();
            if($precedentAct->start_date!='' && $precedentAct->end_date!='')
            {
                   
                if($model->save(false))
                {

                    //correcting relation dates

                    Yii::$app->helper->GetRelationcorrect($_POST['projectId']);

                    $relations = ActivityRelations::find()->where(['projectId' => $_POST['projectId']])->andWhere(['status' => 0])->all();
                    $html = '';
                    if(count($relations) > 0)
                    {
                        $sino = 1;
                        foreach($relations AS $key => $relation)
                        {
                            $typesval ='';
                            $precedentactivity ='';
                            $dependentitem ='';
                            $dependentactivity ='';
                            $precedentItem = Wbsscheduleitems::findOne($relation['precedent_schedule_item'])->name;
                            $allprecedentItems = Wbsscheduleitems::find()->where(['projectId' => $_POST['projectId']])->andWhere(['status' => 0])->all();
                            foreach ($allprecedentItems AS $key => $allprecedentItem):
                                if ($precedentItem == $allprecedentItem->name):
                                    $seletedtype = 'Selected="selected"';
                                else:
                                    $seletedtype = '';
                                endif;
                                $typesval .= "<option value='" . $allprecedentItem->scheduleitem_id . "' " . $seletedtype . ">" . $allprecedentItem->name . "</option>";
                            endforeach;
                            
                            $precedentActivity=Scheduleactivities::findOne($relation['precedent_activity'])->name;
                            $allprecedentActivitys = Scheduleactivities::find()->where(['projectId' => $_POST['projectId']])->andWhere(['scheduleitem_id' => $relation->precedent_schedule_item])->andWhere(['status' => 0])->all();
                            foreach ($allprecedentActivitys AS $key => $allprecedentActivity):
                                if ($precedentActivity == $allprecedentActivity->name):
                                    $seletedtype = 'Selected="selected"';
                                else:
                                    $seletedtype = '';
                                endif;
                                $precedentactivity .= "<option value='" . $allprecedentActivity->id . "' " . $seletedtype . ">" . $allprecedentActivity->name . "</option>";
                            endforeach;
                            
                            $dependentItem = Wbsscheduleitems::findOne($relation['dependent_schedule_item'])->name;
                            $alldependentItems = Wbsscheduleitems::find()->where(['projectId'=>$_POST['projectId']])->andWhere(['status'=>0])->all();
                            foreach ($alldependentItems AS $key => $alldependentItem):
                                if ($dependentItem == $alldependentItem->name):
                                    $seletedtype = 'Selected="selected"';
                                else:
                                    $seletedtype = '';
                                endif;
                                $dependentitem .= "<option value='" . $alldependentItem->scheduleitem_id . "' " . $seletedtype . ">" . $alldependentItem->name . "</option>";
                            endforeach;

                            $dependentActivity= Scheduleactivities::findOne($relation['dependent_activity'])->name;
                            $alldependentActivitys = Scheduleactivities::find()->where(['projectId' => $_POST['projectId']])->andWhere(['scheduleitem_id' => $relation->dependent_schedule_item])->andWhere(['status' => 0])->all();
                            foreach ($alldependentActivitys AS $key => $alldependentActivity):
                                if ($dependentActivity == $alldependentActivity->name):
                                    $seletedtype = 'Selected="selected"';
                                else:
                                    $seletedtype = '';
                                endif;
                                $dependentactivity .= "<option value='" . $alldependentActivity->id . "' " . $seletedtype . ">" . $alldependentActivity->name . "</option>";
                            endforeach;
                            $ssselected = '';
                            $fsselected = '';
                            if($relation['relation_type'] == 1)
                            {
                                $ssselected = 'selected';
                                $reType = 'SS';
                            }
                            elseif($relation['relation_type'] == 2)
                            {
                                $fsselected = 'selected';
                                $reType = 'FS';
                            }
                            else{
                                $reType = '';
                            }
                            $Activitylag = Scheduleactivities::findOne($relation['dependent_activity'])->lag;
                            $reported = ProgressReport::find()->where(['activity_id' => $relation['dependent_activity']])->andWhere(['status' => 1])->all();
                               if(!$reported){
                                 $relationstyle = '';
                               }
                               else{
                                 $relationstyle = 'readonly';
                               }
                               $html .= '<div class="row" id="relationrow'.$relation['id'].'" style="cursor: pointer;" data-id="'.$relation['id'].'">
                               <div class="col-md-1">
                                   <span class="number">'.($sino++).'</span>
                                   <input type="hidden" value="'.$relation['id'].'">
                               </div>
                               <div class="col-md-2 type">
                                   <label>Schedule Item</label>
                                   <span id="precedentitem'.$relation['id'].'">'.$precedentItem.'</span>
                                   <select class="form-control editrelationprecedentitem" type="text" data-id='.$relation['id'].' id="editrelationprecedentitem'.$relation['id'].'" style="display:none;">
                                   <option value="">Select Precedent Schedule Item</option>'.$typesval.'</select>
                                   <span class="error" id="relationprecedentitem_error'.$relation['id'].'"></span>
                               </div>
                               <div class="col-md-2 type">
                                   <label>Precedent Activity</label>
                                   <span id="precedentactivity'.$relation['id'].'">'.$precedentActivity.'</span>
                                   <select class="form-control editrelationprecedentactivity" type="text" id="editrelationprecedentactivity'.$relation['id'].'" style="display:none;">
                                   <option value="">Select Precedent Activity</option>'.$precedentactivity.'</select>
                                   <span class="error" id="relationprecedentactivity_error'.$relation['id'].'"></span>
                               </div>

                               <div class="col-md-2 type">
                                   <label>	Schedule Item</label>
                                   <span id="dependentitem'.$relation['id'].'">'.$dependentItem.'</span>
                                   <select class="form-control editrelationdependentitem" type="text" data-id='.$relation['id'].' id="editrelationdependentitem'.$relation['id'].'" style="display:none;">
                                   <option value="">Select Dependent Schedule Item</option>'.$dependentitem.'</select>
                                   <span class="error" id="relationdependentitem_error'.$relation['id'].'"></span>
                               </div>
                               <div class="col-md-2 type">
                                   <label>Dependent Activity</label>
                                   <span id="dependentactivity'.$relation['id'].'">'.$dependentActivity.'</span>
                                   <select class="form-control editrelationdependentactivity" type="text" id="editrelationdependentactivity'.$relation['id'].'" style="display:none;">
                                   <option value="">Select Precedent Activity</option>'.$dependentactivity.'</select>
                                   <span class="error" id="relationdependentactivity_error'.$relation['id'].'"></span>
                               </div>
                               <div class="col-md-1 type">
                                   <label>Relation</label>
                                   <span id="relationtype'.$relation['id'].'">'.$reType.'</span>
                                   <select class="form-control editrelationrelationtype" type="text" data-id="'.$relation['id'].'" id="editrelationrelationtype'.$relation['id'].'" style="display:none;">
                                   <option value=1 '.$ssselected.'>SS</option><option value=2 '.$fsselected.'>FS</option>
                                   </select>
                                   <span class="error" id="relationrelationtype_error'.$relation['id'].'"></span>
                               </div>
                               <div class="col-md-1 type">
                                   <label>Lag/Lead</label>
                                   <span id="lag'.$relation['id'].'">'.$Activitylag.'</span>
                                   <input type="text" class="form-control editlag" id="editlag'.$relation['id'].'" name="lag_'.$relation['id'].'" value="'.$Activitylag.'" style="display:none;" '.$relationstyle.'>
                               </div>
                               <div class="col-md-1 icon-groups">				
                                  
                                   
                                   <a class="btn btn-primary icon-pencil editrelation" data-v="'.$relation['id'].'" value="'.$relation['id'].'" id="editrelationbutton'.$relation['id'].'" title="Edit Relation" href="#"></a>
                                   <a class="btn btn-primary icon-save saveeditrelation" data-v="'.$relation['id'].'" value="'.$relation['id'].'" id="saveeditrelationbutton'.$relation['id'].'" title="Save" style="display:none;" href="#"></a>
                                   <a class="btn btn-danger  icon-trash1 deleterelation" data-v="'.$relation['id'].'" value="'.$relation['id'].'"  id="deleterelationbutton'.$relation['id'].'" title="Delete Relation" '.$relationstyle.' href="#"></a>
                               </div>
                           </div>';

                        }
                    }
                    $arr = array('relationList' => $html, 'error' => 'No');
                    return json_encode($arr);
                }
                else{
                    $arr = array('error' => 'Yes', 'errortext'=> 'some error occured while saving');
                    return json_encode($arr);
                }
            }
            else{
                $arr = array('error' => 'Yes', 'errortext'=> "Please provide the dates first");
                return json_encode($arr);
            }

        }
        else{

            $model = New ActivityRelations();
            $model->precedent_schedule_item=$_POST['firstItem'];
            $model->precedent_activity=$_POST['firstActivity'];
            $model->dependent_schedule_item=$_POST['secondItem'];
            $model->dependent_activity=$_POST['secondActivity'];
            $model->relation_type=$_POST['relationType'];
            $model->projectId=$_POST['projectId'];
            
            // checking the dates
            $precedentAct = Scheduleactivities::findOne($_POST['firstActivity']);
            if($precedentAct->start_date=='' && $precedentAct->end_date==''){

                $getDateAfterHoliday = Yii::$app->helper->getDateAfterHoliday(date("d-m-Y"), $projectid);
                $today               = date('Y-m-d', strtotime($getDateAfterHoliday));

                $precedentAct->start_date        = $today;
                $precedentAct->end_date          = $today;
                $precedentAct->actual_start_date = $today;
                $precedentAct->actual_end_date   = $today;
                $precedentAct->duration          = 1;
                $precedentAct->save(false);
            }
            $dependentAct = Scheduleactivities::findOne($_POST['secondActivity']);
            $lag = $_POST['lag'];

            //$dependentAct->start_date = date('Y-m-d', strtotime($precedentAct->start_date. ' + '.$lag.' days'));

            $dependentAct->start_date   = date('Y-m-d', 
                                                strtotime(
                                                    Yii::$app->helper->getDateAfterHoliday($precedentAct->start_date, $projectid, $lag)
                                                )
                                              );
            

            if(!empty($lag)){
                $duration = $dependentAct->duration + $lag - 1;
            }else{
                $duration = '0';
            }

            //$dependentAct->end_date = date('Y-m-d', strtotime($precedentAct->start_date. ' + '.$duration.' days'));
            $dependentAct->end_date   = date('Y-m-d', 
                                                strtotime(
                                                    Yii::$app->helper->getDateAfterHoliday(
                                                        $precedentAct->start_date, 
                                                        $projectid, 
                                                        ($dependentAct->duration + $lag)
                                                    )
                                                )
                                            );
            $dependentAct->lag = $_POST['lag'];
            $dependentAct->save(false);
            $availableActivites = Scheduleactivities::find()->where(['projectId' => $_POST['projectId']])->andWhere(['status' => 0])->orderBy(['scheduleitem_id' => SORT_ASC])->all();
            if($precedentAct->start_date!='' && $precedentAct->end_date!='')
            {
                   
                if($model->save(false))
                {

                    //correcting relation dates

                    Yii::$app->helper->GetRelationcorrect($_POST['projectId']);

                    $relations = ActivityRelations::find()->where(['projectId' => $_POST['projectId']])->andWhere(['status' => 0])->all();
                    $html = '';
                    if(count($relations) > 0)
                    {
                        $sino = 1;
                        foreach($relations AS $key => $relation)
                        {
                            $typesval ='';
                            $precedentactivity ='';
                            $dependentitem ='';
                            $dependentactivity ='';
                            $precedentItem = Wbsscheduleitems::findOne($relation['precedent_schedule_item'])->name;
                            $allprecedentItems = Wbsscheduleitems::find()->where(['projectId' => $_POST['projectId']])->andWhere(['status' => 0])->all();
                            foreach ($allprecedentItems AS $key => $allprecedentItem):
                                if ($precedentItem == $allprecedentItem->name):
                                    $seletedtype = 'Selected="selected"';
                                else:
                                    $seletedtype = '';
                                endif;
                                $typesval .= "<option value='" . $allprecedentItem->scheduleitem_id . "' " . $seletedtype . ">" . $allprecedentItem->name . "</option>";
                            endforeach;
                            $precedentActivity=Scheduleactivities::findOne($relation['precedent_activity'])->name;
                            $allprecedentActivitys = Scheduleactivities::find()->where(['projectId' => $_POST['projectId']])->andWhere(['scheduleitem_id' => $relation->precedent_schedule_item])->andWhere(['status' => 0])->all();
                            foreach ($allprecedentActivitys AS $key => $allprecedentActivity):
                                if ($precedentActivity == $allprecedentActivity->name):
                                    $seletedtype = 'Selected="selected"';
                                else:
                                    $seletedtype = '';
                                endif;
                                $precedentactivity .= "<option value='" . $allprecedentActivity->id . "' " . $seletedtype . ">" . $allprecedentActivity->name . "</option>";
                            endforeach;
                            $dependentItem = Wbsscheduleitems::findOne($relation['dependent_schedule_item'])->name;
                            $alldependentItems = Wbsscheduleitems::find()->where(['projectId' => $_POST['projectId']])->andWhere(['status' => 0])->all();
                            foreach ($alldependentItems AS $key => $alldependentItem):
                                if ($dependentItem == $alldependentItem->name):
                                    $seletedtype = 'Selected="selected"';
                                else:
                                    $seletedtype = '';
                                endif;
                                $dependentitem .= "<option value='" . $alldependentItem->scheduleitem_id . "' " . $seletedtype . ">" . $alldependentItem->name . "</option>";
                            endforeach;
                            $dependentActivity= Scheduleactivities::findOne($relation['dependent_activity'])->name;
                            $alldependentActivitys = Scheduleactivities::find()->where(['projectId' => $_POST['projectId']])->andWhere(['scheduleitem_id' => $relation->dependent_schedule_item])->andWhere(['status' => 0])->all();
                            foreach ($alldependentActivitys AS $key => $alldependentActivity):
                                if ($dependentActivity == $alldependentActivity->name):
                                    $seletedtype = 'Selected="selected"';
                                else:
                                    $seletedtype = '';
                                endif;
                                $dependentactivity .= "<option value='" . $alldependentActivity->id . "' " . $seletedtype . ">" . $alldependentActivity->name . "</option>";
                            endforeach;
                            $ssselected = '';
                            $fsselected = '';
                            $reType = '';
                            if($relation['relation_type'] == 1)
                            {
                                $ssselected = 'selected';
                                $reType = 'SS';
                            }
                            elseif($relation['relation_type'] == 2)
                            {
                                $fsselected = 'selected';
                                $reType = 'FS';
                            }
                            $Activitylag = Scheduleactivities::findOne($relation['dependent_activity'])->lag;
                            $reported = ProgressReport::find()->where(['activity_id' => $relation['dependent_activity']])->andWhere(['status' => 1])->all();
                               if(!$reported){
                                 $relationstyle = '';
                               }
                               else{
                                 $relationstyle = 'readonly';
                               }
                            $html .= '<div class="row" id="relationrow'.$relation['id'].'" style="cursor: pointer;" data-id="'.$relation['id'].'">
                               <div class="col-md-1">
                                   <span class="number">'.($sino++).'</span>
                                   <input type="hidden" value="'.$relation['id'].'">
                               </div>
                               <div class="col-md-2 type">
                                   <label>Schedule Item</label>
                                   <span id="precedentitem'.$relation['id'].'">'.$precedentItem.'</span>
                                   <select class="form-control editrelationprecedentitem" type="text" data-id='.$relation['id'].' id="editrelationprecedentitem'.$relation['id'].'" style="display:none;">
                                   <option value="">Select Precedent Schedule Item</option>'.$typesval.'</select>
                                   <span class="error" id="relationprecedentitem_error'.$relation['id'].'"></span>
                               </div>
                               <div class="col-md-2 type">
                                   <label>Precedent Activity</label>
                                   <span id="precedentactivity'.$relation['id'].'">'.$precedentActivity.'</span>
                                   <select class="form-control editrelationprecedentactivity" type="text" id="editrelationprecedentactivity'.$relation['id'].'" style="display:none;">
                                   <option value="">Select Precedent Activity</option>'.$precedentactivity.'</select>
                                   <span class="error" id="relationprecedentactivity_error'.$relation['id'].'"></span>
                               </div>

                               <div class="col-md-2 type">
                                   <label>	Schedule Item</label>
                                   <span id="dependentitem'.$relation['id'].'">'.$dependentItem.'</span>
                                   <select class="form-control editrelationdependentitem" type="text" data-id='.$relation['id'].' id="editrelationdependentitem'.$relation['id'].'" style="display:none;">
                                   <option value="">Select Dependent Schedule Item</option>'.$dependentitem.'</select>
                                   <span class="error" id="relationdependentitem_error'.$relation['id'].'"></span>
                               </div>
                               <div class="col-md-2 type">
                                   <label>Dependent Activity</label>
                                   <span id="dependentactivity'.$relation['id'].'">'.$dependentActivity.'</span>
                                   <select class="form-control editrelationdependentactivity" type="text" id="editrelationdependentactivity'.$relation['id'].'" style="display:none;">
                                   <option value="">Select Precedent Activity</option>'.$dependentactivity.'</select>
                                   <span class="error" id="relationdependentactivity_error'.$relation['id'].'"></span>
                               </div>
                               <div class="col-md-1 type">
                                   <label>Relation</label>
                                   <span id="relationtype'.$relation['id'].'">'.$reType.'</span>
                                   <select class="form-control editrelationrelationtype" type="text" data-id="'.$relation['id'].'" id="editrelationrelationtype'.$relation['id'].'" style="display:none;">
                                   <option value=1 '.$ssselected.'>SS</option><option value=2 '.$fsselected.'>FS</option>
                                   </select>
                                   <span class="error" id="relationrelationtype_error'.$relation['id'].'"></span>
                               </div>
                               <div class="col-md-1 type">
                                   <label>Lag/Lead</label>
                                   <span id="lag'.$relation['id'].'">'.$Activitylag.'</span>
                                   <input type="text" class="form-control editlag" id="editlag'.$relation['id'].'" name="lag_'.$relation['id'].'" value="'.$Activitylag.'" style="display:none;" '.$relationstyle.'>
                               </div>
                               <div class="col-md-1 icon-groups">				
                                  
                                   
                                   <a class="btn btn-primary icon-pencil editrelation" data-v="'.$relation['id'].'" value="'.$relation['id'].'" id="editrelationbutton'.$relation['id'].'" title="Edit Relation" href="#"></a>
                                   <a class="btn btn-primary icon-save saveeditrelation" data-v="'.$relation['id'].'" value="'.$relation['id'].'" id="saveeditrelationbutton'.$relation['id'].'" title="Save" style="display:none;" href="#"></a>
                                   <a class="btn btn-danger  icon-trash1 deleterelation" data-v="'.$relation['id'].'" value="'.$relation['id'].'"  id="deleterelationbutton'.$relation['id'].'" title="Delete Relation" '.$relationstyle.' href="#"></a>
                               </div>
                           </div>';
                        }
                    }
                }
            }

            $arr = array('error' => 'Durerror', 'relationList' => $html, 'errortext'=> "The planned duration exceeds the duration of the project.");
            return json_encode($arr);
        }
    }

    public function actionUpdaterelation()
    {
        $uid = Yii::$app->user->Id; 
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        $_POST['projectId'] = $projuser->projectid;

        $model = ActivityRelations::findOne($_POST['id']);
        $model->precedent_schedule_item=$_POST['firstItem'];
        $model->precedent_activity=$_POST['firstActivity'];
        $model->dependent_schedule_item=$_POST['secondItem'];
        $model->dependent_activity=$_POST['secondActivity'];
        $model->relation_type=$_POST['relationType'];
        $model->projectId=$_POST['projectId'];
        $model->status=0;

        $precedentAct = Scheduleactivities::findOne($_POST['firstActivity']);
        $wbs = Wbsscheduleitems::findOne($precedentAct->scheduleitem_id);
        $dependentAct = Scheduleactivities::findOne($_POST['secondActivity']);
        // Preserve original baseline before relation shifts the start date
        if (!$dependentAct->act_start_date || $dependentAct->act_start_date == '0000-00-00') {
            $dependentAct->act_start_date = $dependentAct->start_date ?: date('Y-m-d');
        }
        if($_POST['relationType']==1){
            $lag = $_POST['lag'];
            $dependentAct->start_date = date('Y-m-d', strtotime($precedentAct->start_date. ' + '.$lag.' days'));
            $duration = $dependentAct->duration + $lag - 1;
            $dependentAct->end_date = date('Y-m-d', strtotime($precedentAct->start_date. ' + '.$duration.' days'));
        }
        elseif($_POST['relationType']==3){
            $lead = $_POST['lag'];
            $duration = $dependentAct->duration - 1;
            $end_date = date('Y-m-d', strtotime($precedentAct->end_date. ' + '.$lead.' days'));
            $dependentAct->start_date = date('Y-m-d', strtotime($end_date. ' - '.$duration.' days'));
            $dependentAct->end_date = date('Y-m-d', strtotime($precedentAct->end_date. ' + '.$lead.' days'));
        }
        else{ 
            $lead = $_POST['lag'] + 1;
            $dependentAct->start_date = date('Y-m-d', strtotime($precedentAct->end_date. ' + '.$lead.' days'));
            $duration = $dependentAct->duration - 1;
            $dependentAct->end_date = date('Y-m-d', strtotime($dependentAct->start_date. ' + '.$duration.' days'));
        }


        $dependentAct->lag = $_POST['lag'];
        $dependentAct->save(false);
        
        $availableActivites = Scheduleactivities::find()->where(['projectId' => $model->projectId])->andWhere(['status' => 0])->orderBy(['scheduleitem_id' => SORT_ASC])->all();
        
        if($precedentAct->start_date!='' && $precedentAct->end_date!='' && $dependentAct->start_date!='' && $dependentAct->end_date!=''){

        if($model->save(false)){

            //$availableActivites = ActivityRelations::model()->findAll(array('condition'=>'projectId ='.$model->projectId.' AND status =0 AND wbs_schedule_status=0','order'=>'precedent_schedule_item ASC'));

            //correcting relation dates
            
            Yii::$app->helper->GetRelationcorrect($model->projectId);

            $relations = ActivityRelations::find()->where(['projectId' => $model->projectId])->andWhere(['precedent_schedule_item' => $_POST['firstItem']])->andWhere(['dependent_schedule_item' => $_POST['firstItem']])->andWhere(['status' => 0])->all();
            $html = '';
            if(count($relations) > 0)
            {
                foreach($relations AS $key => $relation)
                {
                    $precedentItem = Wbsscheduleitems::findOne($relation['precedent_schedule_item'])->name;
                    $precedentActivity=Scheduleactivities::findOne($relation['precedent_activity'])->name;
                    $dependentItem = Wbsscheduleitems::findOne($relation['dependent_schedule_item'])->name;
                    $dependentActivity= Scheduleactivities::findOne($relation['dependent_activity'])->name;
                    if($relation['relation_type'] == 1)
                        $reType = 'SS';
                    elseif($relation['relation_type'] == 2)
                        $reType = 'FS';
                    elseif($relation['relation_type'] == 3)
                        $reType = 'FF';
                    
                    $html .= '<div class="row" id="relationrow'.$relation['id'].'" style="cursor: pointer;" data-id="'.$relation['id'].'">
                        <div class="col-md-1">
                            <span class="number">'.intval($key+1).'</span>
                        </div>
                        <div class="col-md-2 type">
                            <label>Schedule Item</label>
                            <span id="precedentitem'.$relation['id'].'">'.$precedentItem.'</span>
                        </div>
                        <div class="col-md-2 type">
                            <label>Precedent Activity</label>
                            <span id="precedentactivity'.$relation['id'].'">'.$precedentActivity.'</span>
                        </div>
                        <div class="col-md-2 type">
                            <label>	Schedule Item</label>
                            <span id="dependentitem'.$relation['id'].'">'.$dependentItem.'</span>
                        </div>
                        <div class="col-md-2 type">
                            <label>Dependent Activity</label>
                            <span id="dependentactivity'.$relation['id'].'">'.$dependentActivity.'</span>
                        </div>
                        <div class="col-md-1 type">
                            <label>Relation</label>
                            <span id="relationtype'.$relation['id'].'">'.$reType.'</span>
                        </div>
                        <div class="col-md-1 type">
                            <label>&nbsp</label>
                       </div>
                        <div class="col-md-1 icon-groups">				
                            <a class="btn btn-primary icon-pencil editrelation" data-v="'.$relation['id'].'" value="'.$relation['id'].'" id="editrelationbutton'.$relation['id'].'" title="Edit Relation" href="#"></a>
                            <a class="btn btn-primary icon-save saveeditrelation" data-v="'.$relation['id'].'" value="'.$relation['id'].'" id="saveeditrelationbutton'.$relation['id'].'" title="Save" style="display:none;" href="#"></a>
                            <a class="btn btn-danger  icon-trash1 deleterelation" data-v="'.$relation['id'].'" value="'.$relation['id'].'"  id="deleterelationbutton'.$relation['id'].'" title="Delete Relation" href="#"></a>
                        </div>
                    </div>';
                }
            }
            $arr = array('relationList' => $html, 'error' => 'No');
        }
        else
            $arr = array('error' => 'Yes', 'errortext'=> 'some error occured while saving');
        return json_encode($arr);
      }
      else{
        $arr = array('error' => 'Dates', 'errortext'=> "Please provide the dates first");
        return json_encode($arr);
      }
    }

    public function actionUpdatestructurerelation()
    {
        $model = ActivityRelations::findOne($_POST['id']);
        $model->precedent_schedule_item=$_POST['firstItem'];
        $model->precedent_activity=$_POST['firstActivity'];
        $model->dependent_schedule_item=$_POST['secondItem'];
        $model->dependent_activity=$_POST['secondActivity'];
        $model->relation_type=$_POST['relationType'];
        $model->projectId= $model->projectId;
        $model->status=0;
        
        $precedentAct = Scheduleactivities::findOne($_POST['firstActivity']);
        $wbs = Wbsscheduleitems::findOne($precedentAct->scheduleitem_id);
        $dependentAct = Scheduleactivities::findOne($_POST['secondActivity']);
        if($_POST['relationType']==1){
            $lag = $_POST['lag'];
            $dependentAct->start_date = date('Y-m-d', strtotime($precedentAct->start_date. ' + '.$lag.' days'));
            $duration = $dependentAct->duration + $lag - 1;
            $dependentAct->end_date = date('Y-m-d', strtotime($precedentAct->start_date. ' + '.$duration.' days'));    
        }
        elseif($_POST['relationType']==3){
            $lead = $_POST['lag'];
            //$duration = $dependentAct->duration + $lead - 1; 
            $duration = $dependentAct->duration - 1; 
            $end_date = date('Y-m-d', strtotime($precedentAct->end_date. ' + '.$lead.' days'));
            $dependentAct->start_date = date('Y-m-d', strtotime($end_date. ' - '.$duration.' days'));
            $dependentAct->end_date = date('Y-m-d', strtotime($precedentAct->end_date. ' + '.$lead.' days'));
        }
        else{ 
            $lead = $_POST['lag'] + 1;
            $dependentAct->start_date = date('Y-m-d', strtotime($precedentAct->end_date. ' + '.$lead.' days'));
            $duration = $dependentAct->duration - 1;
            $dependentAct->end_date = date('Y-m-d', strtotime($dependentAct->start_date. ' + '.$duration.' days'));
        }
        
        $dependentAct->lag = $_POST['lag'];
        $dependentAct->save(false);
        
        $availableActivites = Scheduleactivities::find()->where(['projectId' => $model->projectId])->andWhere(['status' => 0])->orderBy(['scheduleitem_id' => SORT_ASC,'sortorder'=>SORT_ASC])->all();
        
        if($precedentAct->start_date!='' && $precedentAct->end_date!='' && $dependentAct->start_date!='' && $dependentAct->end_date!=''){
        
        if($model->save(false)){

            //$availableActivites = ActivityRelations::model()->findAll(array('condition'=>'projectId ='.$model->projectId.' AND status =0 AND wbs_schedule_status=0','order'=>'precedent_schedule_item ASC'));

            //correcting relation dates
            Yii::$app->helper->GetRelationcorrect($model->projectId);

            //$relations = ActivityRelations::model()->findAll(array('condition'=>'projectId ='.$precedentAct->projectId.' AND status =0 AND wbs_schedule_status=1'));
            $relations = ActivityRelations::find()->where(['projectId' => $precedentAct->projectId])->andWhere(['status' => 0])->all();
            $html = '';
            $datarows = '';
            if(count($relations) > 0)
            {
                $sino = 1;
                foreach($relations AS $key => $relation)
                {
                    $typesval ='';
                    $precedentactivity ='';
                    $dependentitem ='';
                    $dependentactivity ='';
                    $precedentItem = Wbsscheduleitems::findOne($relation['precedent_schedule_item'])->name;
                    $allprecedentItems = Wbsscheduleitems::find()->where(['projectId' => $precedentAct->projectId])->andWhere(['status' => 0])->all();
                    foreach ($allprecedentItems AS $key => $allprecedentItem):
                        if ($precedentItem == $allprecedentItem->name):
                            $seletedtype = 'Selected="selected"';
                        else:
                            $seletedtype = '';
                        endif;
                        $typesval .= "<option value='" . $allprecedentItem->scheduleitem_id . "' " . $seletedtype . ">" . $allprecedentItem->name . "</option>";
                    endforeach;
                    $precedentActivity=Scheduleactivities::findOne($relation['precedent_activity'])->name;
                    $allprecedentActivitys = Scheduleactivities::find()->where(['projectId' => $precedentAct->projectId])->andWhere(['scheduleitem_id' => $relation->precedent_schedule_item])->andWhere(['status' => 0])->all();
                    foreach ($allprecedentActivitys AS $key => $allprecedentActivity):
                        if ($precedentActivity == $allprecedentActivity->name):
                            $seletedtype = 'Selected="selected"';
                        else:
                            $seletedtype = '';
                        endif;
                        $precedentactivity .= "<option value='" . $allprecedentActivity->id . "' " . $seletedtype . ">" . $allprecedentActivity->name . "</option>";
                    endforeach;
                    $dependentItem = Wbsscheduleitems::findOne($relation['dependent_schedule_item'])->name;
                    $alldependentItems = Wbsscheduleitems::find()->where(['projectId' => $precedentAct->projectId])->andWhere(['status' => 0])->all();
                    foreach ($alldependentItems AS $key => $alldependentItem):
                        if ($dependentItem == $alldependentItem->name):
                            $seletedtype = 'Selected="selected"';
                        else:
                            $seletedtype = '';
                        endif;
                        $dependentitem .= "<option value='" . $alldependentItem->scheduleitem_id . "' " . $seletedtype . ">" . $alldependentItem->name . "</option>";
                    endforeach;
                    $dependentActivity=Scheduleactivities::findOne($relation['dependent_activity'])->name;
                    $alldependentActivitys = Scheduleactivities::find()->where(['projectId' => $precedentAct->projectId])->andWhere(['scheduleitem_id' => $relation->precedent_schedule_item])->andWhere(['status' => 0])->all();
                    foreach ($alldependentActivitys AS $key => $alldependentActivity):
                        if ($dependentActivity == $alldependentActivity->name):
                            $seletedtype = 'Selected="selected"';
                        else:
                            $seletedtype = '';
                        endif;
                        $dependentactivity .= "<option value='" . $alldependentActivity->id . "' " . $seletedtype . ">" . $alldependentActivity->name . "</option>";
                    endforeach;
                    $ssselected = '';
                    $fsselected = '';
                    if($relation['relation_type'] == 1)
                    {
                        $ssselected = 'selected';
                        $reType = 'SS';
                    }
                    elseif($relation['relation_type'] == 2)
                    {
                        $fsselected = 'selected';
                        $reType = 'FS';
                    }
                    
                    $Activitylag = Scheduleactivities::findOne($relation['dependent_activity'])->lag;

                    $html .= '<tr id="relationrow'.$relation['id'].'">
                                <td class="small75">'.($sino++).'<input type="hidden" value="'.$relation['id'].'"></td>
                                <td><span id="precedentitem'.$relation['id'].'">'.$precedentItem.'</span><select class="form-control editrelationprecedentitem" type="text" data-id='.$relation['id'].' id="editrelationprecedentitem'.$relation['id'].'" style="display:none;"><option value="">Select Precedent Schedule Item</option>'.$typesval.'</select><span class="error" id="relationprecedentitem_error'.$relation['id'].'"></span></td>
                                <td><span id="precedentactivity'.$relation['id'].'">'.$precedentActivity.'</span><select class="form-control editrelationprecedentactivity" type="text" id="editrelationprecedentactivity'.$relation['id'].'" style="display:none;"><option value="">Select Precedent Activity</option>'.$precedentactivity.'</select><span class="error" id="relationprecedentactivity_error'.$relation['id'].'"></span></td>
                                <td><span id="dependentitem'.$relation['id'].'">'.$dependentItem.'</span><select class="form-control editrelationdependentitem" type="text" data-id='.$relation['id'].' id="editrelationdependentitem'.$relation['id'].'" style="display:none;"><option value="">Select Dependent Schedule Item</option>'.$dependentitem.'</select><span class="error" id="relationdependentitem_error'.$relation['id'].'"></span></td>
                                <td><span id="dependentactivity'.$relation['id'].'">'.$dependentActivity.'</span><select class="form-control editrelationdependentactivity" type="text" id="editrelationdependentactivity'.$relation['id'].'" style="display:none;"><option value="">Select Precedent Activity</option>'.$dependentactivity.'</select><span class="error" id="relationdependentactivity_error'.$relation['id'].'"></span></td> 

                                <td><span id="relationtype'.$relation['id'].'">'.$reType.'</span>
                                <select class="form-control editrelationrelationtype" type="text" data-id="'.$relation['id'].'" id="editrelationrelationtype'.$relation['id'].'" style="display:none;">
                                <option value=1 '.$ssselected.'>SS</option><option value=2 '.$fsselected.'>FS</option>
                                </select>
                                <span class="error" id="relationrelationtype_error'.$relation['id'].'"></span></td>
                                <td><span id="lag'.$relation['id'].'">'.$Activitylag.'</span><input type="text" class="form-control editlag" id="editlag'.$relation['id'].'" name="lag_'.$relation['id'].'" value="'.$Activitylag.'" style="display:none;"></td>
                                <td><button type="button" class="btn btn-primary editrelation" value="'.$relation['id'].'" id="editrelationbutton'.$relation['id'].'" title="Edit Relation"> 
                                <span class="glyphicon glyphicon-pencil"></span></button>
                                <button type="button" class="btn btn-primary saveeditrelation" value="'.$relation['id'].'" id="saveeditrelationbutton'.$relation['id'].'" title="Save" style="display:none;"> <span class="glyphicon glyphicon-save"></span></button></td>                            
                                <td><button type="button" class="btn btn-primary deleterelation" data-v="'.$relation['id'].'" value="'.$relation['id'].'"  id="deleterelationbutton'.$relation['id'].'" title="Delete Relation">
                                <span class="glyphicon glyphicon-trash"></span></button></td>
                                </tr>';
                }
            }
            
                $connection = \Yii::$app->db;
                $query = "SELECT * FROM wbsscheduleitems WHERE projectId='" . $model->projectId . "' AND status=0";
                $command = $connection->createCommand($query);
                $dataReader = $command->query();
                $items = $dataReader->readAll();
                $datarows = '';
                foreach($items as $item){
                    $sql = "SELECT * FROM scheduleactivities WHERE projectId='".$item['projectId']."' AND   scheduleitem_id='".$item['scheduleitem_id']."' AND status=0 ORDER BY sortorder ASC ";
                    $command = $connection->createCommand($sql);
                    $dataReader = $command->query();
                    $tasks = $dataReader->readAll();
                    // var_dump($tasks);
                    //print_r($tasks); exit;
                    $sortstatus=true;
                    if($sortstatus)
                    {
                        $class='no';
                    }
                    else
                    {
                        $class='disabled';
                    }
                    $datarows .= '<tr style="background-color: #f3f3f3;">
                                    <td><b>'.$item['name'].'</b></td>

                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td> 
                                </tr>';
                    foreach($tasks as $task){
                       $progressreport=ProgressReport::find()->where(['activity_id' => $task['id']])->andWhere(['status' => 0])->all();
                        if($progressreport){
                         $visible = '';   
                         $progressreportstart = $progressreport->start_date;
                        }
                        else{
                         $visible = 'style="display:none;"';  
                         $progressreportstart = '';
                        }
                        if($task['lag']!='' && $task['lag']!=0){
                          $readonly = 'readonly';
                        }
                        else{
                          $readonly = '';
                        }
                        $datarows .= '<tr class="ui-state-default  '.$class.'" data-id="'.$task['id'].'">
                                    <td>&nbsp&nbsp'.$task['name'].'</td>
                                    <td><input type="text" class="form-control duration duration_'.$task['id'].'" data-id="duration_'.$task['id'].'" name="duration_'.$task['id'].'" value="'.$task['duration'].'" readonly></td>
                                    <td>
                                    <input type="date" class="form-control date_field_start start_date_'.$task['id'].'" data-id="start_date_'.$task['id'].'" name="start_date_'.$task['id'].'" value="'.$task['start_date'].'" readonly>
                                    <span class="error" style="display:none;color:red;font-size: 12px;" id="start_date_'.$task['id'].'"></span>
                                    </td>
                                    <td>
                                    <input type="date" class="form-control date_field_end end_date_'.$task['id'].'" data-id="end_date_'.$task['id'].'" name="end_date_'.$task['id'].'" value="'.$task['end_date'].'" readonly>
                                    <span class="error" style="display:none;color:red;font-size: 12px;" id="end_date_'.$task['id'].'"></span>
                                    </td>
                                    <td><input type="text" class="form-control lag lag_'.$task['id'].'" data-id="lag_'.$task['id'].'" name="lag_'.$task['id'].'" value="'.$task['lag'].'" readonly></td>
                                    <td>
                                    <input type="date" class="form-control actual_start_date_'.$task['id'].'" data-id="actual_start_date_'.$task['id'].'" name="actual_start_date_'.$task['id'].'" value="'.$progressreportstart.'" '.$visible.'>
                                    <span class="error" style="display:none;color:red;font-size: 12px;"></span>
                                    </td>
                                   <!-- <td>
                                    <input type="date" class="form-control actual_end_date_'.$task['id'].'" data-id="actual_end_date_'.$task['id'].'" name="actual_end_date_'.$task['id'].'" value="'.$task['actual_end_date'].'">
                                    <span class="error" style="display:none;color:red;font-size: 12px;" id="end_date_'.$task['id'].'"></span>
                                    </td>   -->
                                </tr>';
                    }
                }
            
             $arr = array('relationList' => $html, 'datarows' => $datarows, 'error' => 'No');
        }
        else
            $arr = array('error' => 'Yes', 'errortext'=> 'some error occured while saving');
            return json_encode($arr);
            
        }
      else{
        $arr = array('error' => 'Dates', 'errortext'=> "Please provide the dates first");
        return json_encode($arr);
      }
    }

    public function actionDeleterelation()
    {
        $model = ActivityRelations::findOne($_POST['relationId']);
        $model->status = 1;
        if($model->save(false)){
           
           $precedentAct = Scheduleactivities::findOne($model->precedent_activity);
           $precedentAct->critical_start = '0000-00-00';
           $precedentAct->critical_end = '0000-00-00';
           $precedentAct->save(false);

           // Reset lag on the dependent activity so it has no residual constraint
           $dependentAct = Scheduleactivities::findOne($model->dependent_activity);
           $dependentAct->lag = 0;
           $dependentAct->save(false);

            
           /*$reported_deleted = ProgressReport::model()->find(array('condition'=>'activity_id ='.$model->dependent_activity.' AND status =0'));
            if(count($reported_deleted)>0)
            {
                $reported_deleted->start_date = $precedentAct->start_date;
                $reported_deleted->save(false);
            }*/

            Yii::$app->helper->GetRelationcorrect($model->projectId);
            
            $arr = array('relationId'=> $_POST['relationId'] ,'error' => 'No');
        }
        else{
            $arr = array('relationId'=> $_POST['relationId'] ,'error' => 'Yes', 'errortext' => 'some error occured while saving');
        }
            
        return json_encode($arr);
    }
    public function actionListestimateitems()
    {
        $uid = Yii::$app->user->Id; 
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();

        if($projuser)
        {
            $project = Projects::findOne($projuser->projectid); 
            if($project->project_value!=0){
              $projectvalue = $project->project_value;
            }
            else{
              $projectvalue = '';
            }
            $structurerows= "<option value='0'>IOW-Wise</option>";
            $connection = \Yii::$app->db;
            $sqltemvals1 = "SELECT * FROM workgroups_new WHERE Project_Id=' ".$projuser->projectid." ' AND Status=0 ORDER BY sortorder ASC, Workgroup_Id ASC, Name ASC";
            $command = $connection->createCommand($sqltemvals1);
            $dataReader1 = $command->query();
            $structures = $dataReader1->readAll();
            $datarows='';
            $pricesum=0;
            if($structures){
            	$sno=0;
                foreach($structures AS $structure):
                	$sno++;
                     $sql = "SELECT * FROM workgroup_activities_new WHERE project_Id='".$structure['Project_Id']."' AND wbs_id='".$structure['Workgroup_Id']."' AND estimate=1 AND pricing_status=0 ORDER BY sortorder ASC ";
                     $command = $connection->createCommand($sql);
                     $dataReader = $command->query();
                     $wbsactivities = $dataReader->readAll();
                     if(count($wbsactivities)>0){
                       $datarows.="<tr id='tempprodrow' class='vendor-column'>
                                            <td>".$sno."</td>
                                            <td colspan='6'>IOW : <b>".$structure['Name']."</b></td>
                                            <td colspan='2'></td></tr>";  
                        $subtot=0;
                        foreach($wbsactivities as $key=> $wbsactivity):
                            
                            /*$sqltemvals = " SELECT est_resource_amount AS Price 
                                            FROM estactivity_resources 
                                            WHERE estactivity_id='".$wbsactivity['activity_Id']."' ";
                            $command = $connection->createCommand($sqltemvals);
                            $dataReader = $command->query();
                            $resourcesadded=$dataReader->readAll();
                            $price=0;
                            foreach($resourcesadded AS $key=>$data):
                                $price=$price+$data['Price'];
                            endforeach;*/

                            if($wbsactivity['activitytype_id']==0):
                                $processtypes=$wbsactivity['process_Id'];
                            else:
                                $processtypes=$wbsactivity['activitytype_id'];
                            endif;
                            $pricingestimate=PricingEstimateNew::find()->where(['project_Id'=>$projuser->projectid])->andWhere(['activity_Id'=>$wbsactivity['id']])->andWhere(['pricing_status'=>0])->one();
                             $pricingestimateres=PricingEstimateResourcesNew::find()->where(['project_Id'=>$projuser->projectid])->andWhere(['activity_id'=>$wbsactivity['id']])->andWhere(['pricing_status'=>0])->andWhere(['process_Id'=>$processtypes])->all();
                            
                            if($pricingestimateres):
                                $specrate = 0;
                                foreach($pricingestimateres AS $pricingestimatere):
                                    $specrate = $specrate + ($pricingestimatere['rate'] * $pricingestimatere['quantity']);
                                endforeach;
                                //$specrate=$pricingestimate['specific_rate'];

                            else:
                                $specrate = 0;
                                //$specrate=$price;

                            endif;
                            $amount=0;
                            if($pricingestimate){
                                $activityqty=$pricingestimate->activity_qty;
                                $amount=$pricingestimate->activity_qty * $specrate;
                            }
                            else{
                                $activityqty='';
                            }
                            $processname='';

                            $sql="SELECT * FROM estimateactivities WHERE activity_status=0 AND activity_id=".$wbsactivity['activity_Id']." ";
                            $command1 = $connection->createCommand($sql);
                            $dataReader1 = $command1->query();
                            $est_act = $dataReader1->read();

                            /*if($wbsactivity['activitytype_id']>0)
                            {
                            	$processname=EstimateActivityType::findOne($wbsactivity['activitytype_id'])->activitytype_name;
                            }*/
                            if($est_act){
                                $processname=EstimateActivityType::findOne($est_act['activity_type'])->activitytype_name;
                            }
                            if($wbsactivity['primavera_id'])
                                $processname = 'Construction Activities';

                        
                            $name='';
                            if($est_act){
                            $name=$est_act['activity_name'];
                            }
                            if($wbsactivity['activity_Name']!=''):
                                $activityname=$wbsactivity['activity_Name'];
                            else:
                                $activityname=$name;
                            endif;
                            if($wbsactivity['activitytype_id']==0):
                                $processtype=$wbsactivity['process_Id'];
                            else:
                                $processtype=$wbsactivity['activitytype_id'];
                            endif;
                            
                            $datarows.='<tr  id="Estimateiowactivitiesrow' . $wbsactivity['id'] . '" data-id="' . $wbsactivity['id'] . '">
                                            <input type="hidden" name="itemid[]" value='.$wbsactivity['id'].'>
                                            <input type="hidden" name="activityid[]" value='.$wbsactivity['activity_Id'].'>
                                            <input type="hidden" name="processid[]" value='.$wbsactivity['activitytype_id'].'>
                                            <input type="hidden" value='. $specrate . ' id='.str_replace(' ','',$processname)."rate" . $wbsactivity['id'] .' data-id=' . $wbsactivity['id'] . ' name="rate[]">
                                            <td>'.$sno.'.'.($key + 1).'</td>
                                            <td><span id="Estimateiowprocess">'.$processname.'</span></td>
                                            <td><span id="Estimateiowactivityname'.$wbsactivity['id'].'">'.$activityname.'</span>
                                        <input class="form-control editEstimateiowactivityname" style="display:none;" type="text" id="editEstimateiowactivityname"'.$wbsactivity['id'].'" value="'.$activityname.'">
                                        <span class="error"></span></td>
                                            <td style="text-align: center"><span id="Estimateiowactivityunit'.$wbsactivity['id'].'">'.$wbsactivity['activity_Unit'].'</span>
                                        <input class="form-control editEstimateiowactivityunit" style="display:none;" type="text" id="editEstimateiowactivityunit'.$wbsactivity['id'].'" value="'.$wbsactivity['activity_Unit'].'">
                                        <span class="error"></span></td>
                                            
                                            <td><input type="number" data-type="'.$processname.'" data-iow="'.$wbsactivity['wbs_id'].'" data-id="' . $wbsactivity['id'] . '" class="form-control quantity" name="quantity[]" id="'.str_replace(' ','',$processname).'quantity' . $wbsactivity['id'] .'" value="'. $activityqty.'" >
                                        <span class="error"></span></td>
                                            <td><input type="text" readonly="readonly" data-type="'.$processname.'" data-id="'. $wbsactivity['id'] .'" class="form-control specrate" style="text-align: right !important;" name="specrate[]" id="'.str_replace(' ','',$processname).'rate' . $wbsactivity['id'] .'" value="'.number_format($specrate,2).'" >
                                        <span class="error"></span></td>
                                        <td style="text-align:right;padding-right:14px;"><span id="'.str_replace(' ','',$processname).'amount' . $wbsactivity['id'] . '">' . number_format((float)$amount,2) . '</span>
                                         <input type="hidden" value="' . $amount . '" class="iowamount'.$wbsactivity['wbs_id'].'" id="amount' . $wbsactivity['id'] . '"></td>

                                            <td colspan="2">
                                                <div class="icon-groups">
                                                    
                                                    <a href="javascript:void(0);" data-project="'.$projuser->projectid.'" data-proestimate="'.$wbsactivity['id'].'" data-activity="'.$wbsactivity['activity_Id'].'" data-process="'.$processtype.'" class="estimateallocation btn btn-primary icon-dns" title="Allocate Resources"></a>
                                                    <a  data-id="'.$wbsactivity['id'].'" title="Delete Resource" href="javascript:void(0);" class="icon-trash1 removeiowitem" id="removeiowitem'.$wbsactivity['id'].'"></a>
                                                </div>
                                            </td>
                                        </tr>';

                            $subtot = $subtot + $amount;
                        endforeach;
                        if($subtot > 0):
                            $datarows.="<tr>
                            <th>
                                </th>
                                <th colspan='4'>IOW Total</th>
                                <th></th>
                                <th class='iow-total-class' id='totaliowcost".$structure['Workgroup_Id']."' style='text-align: right'>" . number_format($subtot,2) . "</th>
                                <th colspan='2'><input type='hidden' class='totaliowcost' id='totaliowcostval".$structure['Workgroup_Id']."' value='".$subtot."'></th>
                            </tr>";
                         endif; 
                         $pricesum=$pricesum + $subtot; 
                    }
                 endforeach;

            }
            $datarows.="<tr style='font-size:16px;'>
                        <th></th><th colspan='4' class='all-total-class'>Total Cost</th>
                        <th></th>
                        <th class='all-total-class' id='totalcost' style='text-align: right;font-size: 15px;'>" . number_format($pricesum,2) . "</th>
                        <th colspan='2'></th></tr>
                        "; 
            $arr = array('result' => $structurerows,'projectid'=>$projuser->projectid,'items'=>$datarows,'projectvalue'=> $projectvalue, 'error'=>'No');
            return json_encode($arr);

        }
        else{

            $datarows = '<tr><td colspan="8" style="text-align: center;">No Project Selected</td></tr>';

            $arr = array('result' => '','projectid'=>'','items'=>$datarows,'projectvalue'=> '', 'error'=>'No');
            return json_encode($arr);

        }
    }

    public function Proreportsemitable($Process,$activityid,$project_estimate_Id,$Project_Id)
    {
    	$sno=0;
        $connection = Yii::$app->db;
        $activityid=$activityid;
        $itemid=$project_estimate_Id;
        $sqltemvals="SELECT a.Vendor_Id,a.Resource_Id,a.Name AS resource,a.Unit,a.ResourceType_Id,a.Resource_Location,b.est_resource_quantity AS Quantity,b.est_resource_amount AS Price,b.est_resource_rate AS actual_price FROM resources AS a
                INNER JOIN estactivity_resources AS b ON  a.Resource_Id=b.est_resource_id
                WHERE b.estactivity_id='".$activityid."' ORDER BY a.Resource_Id";
        //echo $sqltemvals;exit;
        $estrescount=PricingEstimateResourcesNew::find()->where(['project_id'=>$Project_Id])->andWhere(['activity_id'=>$itemid])->andWhere(['est_activity_Id'=>$activityid])->andWhere(['process_Id'=>$Process])->andWhere(['pricing_status'=>0])->count();
        //echo $estrescount;exit;
        if($estrescount==0):
            $command=$connection->createCommand($sqltemvals);
            $dataReader=$command->query();
            $resourcesadded=$dataReader->readAll();
            $price=0;
            $addedones='';
            foreach($resourcesadded AS $key=>$data):
                $price=$price+$data['Price'];
                $res_type = Resourcetype::find()->where(['ResourceType_Id'=>$data['ResourceType_Id']])->one(); 
                $addedones.='<tr>
                <td>'.($key + 1).'</td>
                <td><span>'.$data['resource'].'</span></td>
                <td style="text-align: center"><span>'.$data['Unit'].'</span>
                <td style="text-align: center"><span>'.$data['Quantity'].'</span>
                <td style="text-align: center"><span>'.$data['actual_price'].'</span></td>
                <td style="text-align: right"><span>'.number_format((float)$data['Price'], 2, '.', '').'</span></td>
            </tr>';
            endforeach;
            $addedones.="<tr>
                            <th colspan='4'>Resource Total</th>
                            <th></th>
                            <th colspan='2' style='text-align: right'>".number_format((float)$price, 2, '.', '')."</th>
                        </tr>";
        else:
        $estimateresources=PricingEstimateResourcesNew::find()->where(['activity_id'=>$itemid])->andWhere(['est_activity_Id'=>$activityid])->andWhere(['project_id'=>$Project_Id])->andWhere(['process_Id'=>$Process])->andWhere(['pricing_status'=>0])->all();
            $addedones='';
            $price=0;
            //echo count($estimateresources);exit;
          
            foreach($estimateresources AS $key => $estimateresource):
			$res_type = Resourcetype::find()->where(['ResourceType_Id'=>$estimateresource['resourcetype_Id']])->one(); 
			$res = Resources::find()->where(['Resource_Id'=>$estimateresource['resource_Id']])->one();

                $price=$price + ($estimateresource['rate'] * $estimateresource['quantity']);
                $amount=$estimateresource['quantity'] * $estimateresource['rate'];
                $addedones.='<tr>
                    <td>'.($key + 1).'</td>
                    <td><span>'.$res->Name.'</span></td>
                    <td style="text-align: center"><span>'.$res->Unit.'</span>
                    <td style="text-align: center"><span>'.number_format((float)$estimateresource['quantity'], 3, '.', '').'</span>
                    <td style="text-align: center"><span>'.$estimateresource['rate'].'</span></td>
                    <td style="text-align: right"><span>'.number_format((float)$amount, 2, '.', '').'</span></td>
                    </tr>';
            endforeach;
            $addedones.="<tr>
                            <th colspan='4'>Resource Total</th>
                            <th colspan='2' style='text-align: right'>".number_format((float)$price, 2, '.', '')."</th>    
                        </tr>";
        endif;

        return $addedones;
    }

    public function actionListestimateitemsreport()
    {
        $uid = Yii::$app->user->Id; 
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();

        if($projuser)
        {
            $project = Projects::findOne($projuser->projectid); 
            if($project->project_value!=0){
              $projectvalue = $project->project_value;
            }
            else{
              $projectvalue = '';
            }
            $structurerows= "<option value='0'>IOW-Wise</option>";
            $connection = \Yii::$app->db;
            $sqltemvals1 = "SELECT * FROM workgroups_new WHERE Project_Id=' ".$projuser->projectid." ' AND Status=0 ORDER BY sortorder ASC, Workgroup_Id ASC, Name ASC";
            $command = $connection->createCommand($sqltemvals1);
            $dataReader1 = $command->query();
            $structures = $dataReader1->readAll();
            $datarows='';
            $pricesum=0;
            if($structures){
            	$sno=0;
                foreach($structures AS $structure):
                	$sno++;
                     $sql = "SELECT * FROM workgroup_activities_new WHERE project_Id='".$structure['Project_Id']."' AND wbs_id='".$structure['Workgroup_Id']."' AND estimate=1 AND pricing_status=0 ORDER BY sortorder ASC ";
                     $command = $connection->createCommand($sql);
                     $dataReader = $command->query();
                     $wbsactivities = $dataReader->readAll();
                     if(count($wbsactivities)>0){
                       $datarows.="<tr class='vendor-column'>
                                        <td>".$sno."</td>
                                        <td colspan='6'><b>IOW : </b>".$structure['Name']."</td>
                                        <td></td><td></td>
                                    </tr>";  
                        $subtot=0;
                        foreach($wbsactivities as $key=> $wbsactivity):
                            $sqltemvals="SELECT est_resource_amount AS Price FROM estactivity_resources WHERE estactivity_id='".$wbsactivity['activity_Id']."' ";
                            $command=$connection->createCommand($sqltemvals);
                            $dataReader=$command->query();
                            $resourcesadded=$dataReader->readAll();
                            $price=0;
                            foreach($resourcesadded AS $key=>$data):
                                $price=$price+$data['Price'];
                            endforeach;
                            $pricingestimate=PricingEstimateNew::find()->where(['project_Id'=>$projuser->projectid])->andWhere(['activity_Id'=>$wbsactivity['id']])->andWhere(['pricing_status'=>0])->one();
                             $pricingestimateres=PricingEstimateResourcesNew::find()->where(['project_Id'=>$projuser->projectid])->andWhere(['activity_id'=>$wbsactivity['id']])->andWhere(['pricing_status'=>0])->all();
                            if($pricingestimateres):
                                $specrate=0;
                                foreach($pricingestimateres AS $pricingestimatere):
                                    $specrate=$specrate + ($pricingestimatere['rate'] * $pricingestimatere['quantity']);
                                endforeach;
                                //$specrate=$pricingestimate['specific_rate'];
                            else:
                                $specrate=$price;
                                //$specrate=0;
                            endif;
                            $amount=0;
                            if($pricingestimate){
                                $activityqty=$pricingestimate->activity_qty;
                            $amount=$pricingestimate->activity_qty * $specrate;
                        }
                            $processname='';
                            if($wbsactivity['activitytype_id']>0)
                            {
                            	$processname=EstimateActivityType::findOne($wbsactivity['activitytype_id'])->activitytype_name;
                            }
                        
         					
                            $sql="SELECT * FROM estimateactivities WHERE activity_status=0 AND activity_id=".$wbsactivity['activity_Id']." ";
                            $command1 = $connection->createCommand($sql);
                            $dataReader1 = $command1->query();
                            $est_act = $dataReader1->read();
                            $name='';
                            if($est_act){
                            $name=$est_act['activity_name'];
                            }
                            if($wbsactivity['activity_Name']!=''):
                                $activityname=$wbsactivity['activity_Name'];
                            else:
                                $activityname=$name;
                            endif;
                            if($wbsactivity['activitytype_id']==0):
                                $processtype=$wbsactivity['process_Id'];
                            else:
                                $processtype=$wbsactivity['activitytype_id'];
                            endif;
                            $content = $this->Proreportsemitable($processtype,$wbsactivity['activity_Id'],$wbsactivity['id'],$projuser->projectid);
                            $datarows.='<tr><td></td>
                                            <td colspan="2"><span id="Estimateiowprocess">'.$processname.'</span></td>
                                            <td>
                                            <div class="hover" data-tooltip="tooltip'.$wbsactivity['id'].'" style="cursor:pointer;">
                                                <span><a>'.$activityname.'</a></span>
                                                <div class="tooltiptable" id="tooltip'.$wbsactivity['id'].'" style="width:600px;">
                                                    <table cellpadding="0" cellspacing="0" width="100%">
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Resources Name</th>
                                                            <th style="text-align: center">Unit</th>
                                                            <th style="text-align: center">Quantity</th>
                                                            <th style="text-align: center">Rate</th>
                                                            <th style="text-align: center">Amount</th>
                                                        </tr>
                                                        '.$content.'
                                                    </table>
                                                </div>
                                            </div>
                                            </td>
                                            <td style="text-align: center"><span id="Estimateiowactivityunit'.$wbsactivity['id'].'">'.$wbsactivity['activity_Unit'].'</span>
                                        <span class="error"></span></td>
                                            
                                            <td style="text-align: center"><span>'. $activityqty.'</span></td>
                                            <td style="text-align: right"><span>'.number_format($specrate,2).'</span></td>
                                            <td style="text-align: right"><span>' . number_format((float)$amount,2) . '</span></td>

                                            <td colspan="2">
                                                <div class="icon-groups">  
                                                <a class="btn btn-primary text-button estimateallocationView" href="javascript:void(0);" data-iowName="'.$structure['Name'].'" data-project="'.$projuser->projectid.'" data-proestimate="'.$wbsactivity['id'].'" data-activity="'.$wbsactivity['activity_Id'].'" data-process="'.$processtype.'"><span class="icon-receipt"></span>Details</a>
                                                </div>
                                            </td>
                                        </tr>';

                            $subtot = $subtot + $amount;
                        endforeach;
                        if($subtot > 0):
                            $datarows.="<tr>
                            <th>
                                </th>
                                <th colspan='6'>IOW Total</th>
                                <th colspan='1' id='totaliowcost".$structure['Workgroup_Id']."' style='text-align: right'>" . number_format($subtot,2) . "</th>
                                </tr>";
                         endif; 
                         $pricesum=$pricesum + $subtot; 
                    }
                 endforeach;

            }
            $datarows.="<tr class='vendor-column' style='font-size:16px;'>
                            <th></th>
                            <th colspan='6'>Total Cost</th>
                            <th colspan='1' id='totalcost' style='text-align: right'>" . number_format($pricesum,2) . "</th>
                            <th></th>
                        </tr>";
            $arr = array('result' => $structurerows,'projectid'=>$projuser->projectid,'items'=>$datarows,'projectvalue'=> $projectvalue, 'error'=>'No');
            return json_encode($arr);

        }
        else{

            $datarows = '<tr><td colspan="8" style="text-align: center;">No Project Selected</td></tr>';

            $arr = array('result' => '','projectid'=>'','items'=>$datarows,'projectvalue'=> '', 'error'=>'No');
            return json_encode($arr);

        }
    }

	
    public function actionPricingestimate()
    {
        //Resource allocation tab checking
        /*for($i=0; $i<count($_POST['itemid']); $i++)
        {
            if($_POST['rate'][$i]!='0')
            {
                $activity_id = $_POST['itemid'][$i];
                $sql_1 = "SELECT * FROM pricing_estimate_resources_new where activity_id='".$activity_id."' ";
                $estimatecount = 0;
                $connection = \Yii::$app->db;
                $command=$connection->createCommand($sql_1);
                $dataReader=$command->query();
                $estimates=$dataReader->readAll();
                $estimatecount = count($estimates);
                if($estimatecount==0)
                {
                    $sql_2 = "SELECT * FROM scheduleactivities where activity_id='".$activity_id."' ";
                    $connection = \Yii::$app->db;
                    $command=$connection->createCommand($sql_2);
                    $dataReader=$command->query();
                    if($scheduleactivities=$dataReader->readAll()){
                        $arr=array('result'=>$scheduleactivities[0]['name'],'error'=>'yes');
                        return json_encode($arr);
                    }
                }
            }
        }*/
        //Resource allocation tab checking ending
       // echo 'hai'; exit;
        $uid = Yii::$app->user->Id; 
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        $_POST['Project_Id'] = $projuser->projectid;
        if (isset($_POST['itemid'])) {
            $project = Projects::findOne($_POST['Project_Id']);
            $a = str_replace(',', '', $_POST['projectvalue']);
            $project->project_value = $a;
            $project->save(false);
            $connection = \Yii::$app->db;
            $estimatecount=PricingEstimateNew::find()->where(['project_Id'=>$_POST['Project_Id']])->count();
            //echo $estimatecount;exit;
           
            if($estimatecount > 0):
                for ($i = 0; $i < count($_POST['itemid']); $i++) {
                    $itemid = $_POST['itemid'][$i];
                    $activityid = $_POST['activityid'][$i];
                    $processid = $_POST['processid'][$i];

                    $estimateactdat = PricingEstimateNew::find()->where(['project_Id'=>$_POST['Project_Id']])->andWhere(['activity_Id'=>$itemid])->andWhere(['pricing_status'=>0])->one();

                    if($estimateactdat):
                        $sql="UPDATE pricing_estimate_new SET activity_qty='".$_POST['quantity'][$i]."',est_activity_Id='".$activityid."' WHERE activity_Id='".$itemid."' ";
                        $command = $connection->createCommand($sql);
                        $dataReader = $command->query();

                        
                        $estrescount = PricingEstimateResourcesNew::find()->where(['project_id' => $_POST['Project_Id']])->andWhere(['activity_id' => $itemid])->count();
                        if($estrescount==0):
                            $sqltemvals="SELECT a.Resource_Id,a.Name AS resource,a.Unit,a.ResourceType_Id,b.est_resource_quantity AS Quantity,b.est_resource_amount AS Price,b.est_resource_rate AS actual_price FROM resources AS a
                                INNER JOIN estactivity_resources AS b ON  a.Resource_Id=b.est_resource_id
                                WHERE b.estactivity_id='".$activityid."' ORDER BY a.Resource_Id";
                            $command=$connection->createCommand($sqltemvals);
                            $dataReader=$command->query();
                            $resourcesadded=$dataReader->readAll();
                            
                            foreach($resourcesadded AS $resource):
                                $sql = "INSERT INTO pricing_estimate_resources_new (activity_id,resource_Id,resourcetype_Id,quantity,rate,project_id,est_activity_Id,process_Id) VALUES('" . $itemid . "','" . $resource['Resource_Id'] . "','".$resource['ResourceType_Id']."','" . $resource['Quantity'] . "','" . $resource['actual_price'] . "','" . $_POST['Project_Id'] . "','".$activityid."','".$processid."')";
                                //echo $sql;exit;
                                $command = $connection->createCommand($sql);
                                $dataReader = $command->query();
                            endforeach;
                        endif;

                    else:
                        $sql="INSERT INTO pricing_estimate_new (project_Id,activity_Id,activity_qty,process_Id,est_activity_Id) VALUES ('".$_POST['Project_Id']."','".$itemid."','".$_POST['quantity'][$i]."','".$processid."','".$activityid."')";
                        $command = $connection->createCommand($sql);
                        $dataReader = $command->query();
                        $sqltemvals="SELECT a.Resource_Id,a.Name AS resource,a.Unit,a.ResourceType_Id,b.est_resource_quantity AS Quantity,b.est_resource_amount AS Price,b.est_resource_rate AS actual_price FROM resources AS a
                                INNER JOIN estactivity_resources AS b ON  a.Resource_Id=b.est_resource_id
                                WHERE b.estactivity_id='".$activityid."' ORDER BY a.Resource_Id";
                        $command=$connection->createCommand($sqltemvals);
                        $dataReader=$command->query();
                        $resourcesadded=$dataReader->readAll();
                        $estrescount=PricingEstimateResourcesNew::find()->where(['project_id'=>$_POST['Project_Id']])->andWhere(['activity_id'=>$itemid])->count();
                        if($estrescount==0):
                            foreach($resourcesadded AS $resource):
                                $sql = "INSERT INTO pricing_estimate_resources_new (activity_id,resource_Id,resourcetype_Id,quantity,rate,project_id,est_activity_Id,process_Id) VALUES('" . $itemid . "','" . $resource['Resource_Id'] . "','".$resource['ResourceType_Id']."','" . $resource['Quantity'] . "','" . $resource['actual_price'] . "','" . $_POST['Project_Id'] . "','".$activityid."','".$processid."')";
                                //echo $sql;exit;
                                $command = $connection->createCommand($sql);
                                $dataReader = $command->query();
                            endforeach;
                        endif;
                    endif;
                }
            else:
                for ($i = 0; $i < count($_POST['itemid']); $i++) {
                    $itemid = $_POST['itemid'][$i];
                    $activityid = $_POST['activityid'][$i];
                    $processid = $_POST['processid'][$i];
                    //$rate = $_POST['quantity'][$i] * $_POST['specrate'][$i];
                    //echo $rate;exit;
                    $sql="INSERT INTO pricing_estimate_new (project_Id,activity_Id,activity_qty,process_Id,est_activity_Id) VALUES ('".$_POST['Project_Id']."','".$itemid."','".$_POST['quantity'][$i]."','".$processid."','".$activityid."')";
                    //echo $sql;
                    $command = $connection->createCommand($sql);
                    $dataReader = $command->query();
                    $sqltemvals="SELECT a.Resource_Id,a.Name AS resource,a.Unit,a.ResourceType_Id,b.est_resource_quantity AS Quantity,b.est_resource_amount AS Price,b.est_resource_rate AS actual_price FROM resources AS a
                                INNER JOIN estactivity_resources AS b ON  a.Resource_Id=b.est_resource_id
                                WHERE b.estactivity_id='".$activityid."' ORDER BY a.Resource_Id";
                    $command=$connection->createCommand($sqltemvals);
                    $dataReader=$command->query();
                    $resourcesadded=$dataReader->readAll();
                    $estrescount=PricingEstimateResourcesNew::find()->where(['project_id'=>$_POST['Project_Id']])->andWhere(['activity_id'=>$itemid])->count();
                    if($estrescount==0):
                        foreach($resourcesadded AS $resource):
                            $sql = "INSERT INTO pricing_estimate_resources_new (activity_id,resource_Id,resourcetype_Id,quantity,rate,project_id,est_activity_Id,process_Id) VALUES('" . $itemid . "','" . $resource['Resource_Id'] . "','".$resource['ResourceType_Id']."','" . $resource['Quantity'] . "','" . $resource['actual_price'] . "','" . $_POST['Project_Id'] . "','".$activityid."','".$processid."')";
                            //echo $sql;exit;
                            $command = $connection->createCommand($sql);
                            $dataReader = $command->query();
                        endforeach;
                    endif;
                }
            
            endif;
            //$this->redirect(array('projects/index'));
            $arr=array('result'=>$_POST['Project_Id'],'error'=>'No');
            return json_encode($arr);
        }
    }

    public function actionCheckremoveitem()
    {
        $pricingres=PricingEstimateResourcesNew::find()->where(['activity_id'=>$_POST['actid']])->andWhere(['pricing_status'=>0])->one();
        if(!empty($pricingres))
        {
            $arr = array('result' => 'Can not Delete this activity, Please delete estimated resources. ','error'=>'Yes');
        }else
        {
            $arr = array('result' => $_POST['actid'],'error'=>'No');
        }
        return json_encode($arr);
    }


   public function actionRemoveitemold()
   {
        $model=WorkgroupActivitiesNew::findOne($_POST['actid']);
        $model->pricing_status=1;
        $model->save(false);
        //$model->delete();
        $pricing=PricingEstimateNew::find()->where(['activity_Id'=>$_POST['actid']])->one();
        
            $pricing->pricing_status=1;
            $pricing->save(false);
      
         $pricingres=PricingEstimateResourcesNew::find()->where(['activity_id'=>$_POST['actid']])->all();
        
            foreach($pricingres AS $pricingre):
                $pricingre->pricing_status=1;
                $pricingre->save(false);
            endforeach;
        
        $arr = array('result' => $_POST['actid'],'error'=>'No');
        return json_encode($arr);

    }

    public function actionRemoveitem()
    {

        $activityid=$_POST['actid']; 
        $connection = Yii::$app->db;
        //check orders data   
        $sts_val = array();
        $orderids = array();
        $jobids = array();
        array_push($sts_val,0);
        $jobid = Jobcard::find()->where(['app_activity'=>$activityid])->andWhere(['delete_status'=>0])->all();
        if(!empty($jobid))
        {
            foreach ($jobid as $key => $value) 
            {
                $jobid = $value->job_id;
                $jobids[] = $value->job_id;
                if($jobid)
                {
                    $odresid = OrderedResource::find()->where(['jobcard_id'=>$jobid])->andWhere(['delete_status'=>0])->all();

                    if(!empty($odresid))
                    {
                        foreach ($odresid as $key => $valueres) 
                        {
                            $orderid = $valueres->order_id;
                            
                            $orderids[] = $valueres->order_id;
                            if($orderid)
                            {
                                $odres_id = Orders::find()->where(['order_id'=>$orderid])->andWhere(['delete_status'=>0])->one();
                                if(!empty($odres_id))
                                {
                                    $order_status = $odres_id->status;
                                    if($order_status==2)
                                    {
                                        array_push($sts_val,1);
                                        //push val to array and checkk 
                                    }
                                    else
                                    {
                                        array_push($sts_val,0);
                                        
                                    }
                                }
                            }
                            
                        }
                    }
                }


            }
        }

        if(!empty($sts_val))
        {

            if( in_array("1", $sts_val))
            {
                $arr = array('error'=>'Yes','result'=>'Cannot delete this item as it is already despatched.');
                return json_encode($arr);
            }
        
            else
            {   
                //check schedule data
                $activityid=$_POST['actid'];
                $connection = Yii::$app->db;
               
                //check data in project schedule
                $scheduleid=Scheduleactivities::find()->where(['activity_id' => $activityid])->one();
                if(!empty($scheduleid))
                {
                    // schedule data is there - alert for delete schedule
                    $arr = array('error'=>'Yes','result'=>'For delete this activity please delete it from schedule.');
                    return json_encode($arr);
                }
                else
                {
                    //delete from projest estimate all data..
                    //deleted from confirm order, despatch order
                    if(!empty($orderids))
                    {
                        foreach ($orderids as $key => $odrvalue) 
                        { 
                            $odresids = Orders::find()->where(['order_id'=>$odrvalue])->andWhere(['delete_status'=>0])->one();
                            if($odresids){
                                $odresids->delete_status = 1;
                                $odresids->save(false);
                            }
                            
                        }
                    }

                    //deleted from place order- cart, jobcard
                    
                    if(!empty($jobids))
                    {
                        foreach ($jobids as $key => $jobvalue) 
                        { 
                            $cartdata = Cart::find()->where(['Job_Id'=>$jobvalue])->one();
                            if($cartdata){
                                $cartdata->status = 5; //deleted
                                $cartdata->save(false);
                            }

                            $jobdata = jobcard::findOne($jobvalue);
                            if($jobdata){
                                $jobdata->delete_status = 1; //deleted
                                $jobdata->save(false);
                            }
                            
                        }
                    }
                    
                    
                // $pricing=PricingEstimateNew::deleteAll(['activity_Id' => $_POST['actid']]);
                    $pricing=PricingEstimateNew::find()->where(['activity_Id'=>$_POST['actid']])->all();
                    foreach($pricing AS $pricings):
                        $pricings->pricing_status=1;
                        $pricings->save(false);
                    endforeach;

                    $pricingres=PricingEstimateResourcesNew::find()->where(['activity_id'=>$_POST['actid']])->all();
                
                    foreach($pricingres AS $pricingre):
                        $pricingre->pricing_status=1;
                        $pricingre->save(false);
                    endforeach;

                    
                    //no schedule data- then delete all actvity form everywher
                    $model=WorkgroupActivitiesNew::findOne($_POST['actid']);
                    $model->delete();

                    $arr = array('error'=>'No','id'=>$_POST['actid']);
                    return json_encode($arr);
                }
               

                
                
                
            }
        }

    }

    public function actionHolidays(){

        $ProjectId = $_POST['projectid'];
        $ProjectName = '';
        if($Project = Projects::findOne($ProjectId))
            $ProjectName = $Project->Name;

        if($selectedDate = date('j-n-Y', strtotime($_POST['selectedDate']))){
            if($holiday = Holidays::find()->where(['project_id'=>$ProjectId])->one()){
                $datesArr = explode(',', $holiday->dates);
                if(in_array($selectedDate, $datesArr))
                    unset($datesArr[array_search($selectedDate, $datesArr)]);
                else
                    $datesArr[] = $selectedDate;
                $holiday->dates = implode(',',array_filter($datesArr,'strlen'));
                $holiday->save(false);
            }
            else{
                $holiday = New Holidays();
                $holiday->project_id = $ProjectId;
                $holiday->dates = $selectedDate;
                $holiday->save(false);
            }
        }

        $holidayWeekSelectorVisible = 'display:none;';
        $holidayWeekSelectorIcon    = 'icon-keyboard_arrow_down';

        if(($selectedWeek = $_POST['selectedWeek']) != ''){

            $holidayWeekSelectorVisible = 'display:block;';
            $holidayWeekSelectorIcon    = 'icon-keyboard_arrow_up';

            if($holiday = Holidays::find()->where(['project_id'=>$ProjectId])->one()){
                $weeksArr = explode(',', $holiday->weeks);
                if(in_array($selectedWeek, $weeksArr))
                    unset($weeksArr[array_search($selectedWeek, $weeksArr)]);
                else
                    $weeksArr[] = $selectedWeek;
                $holiday->weeks = implode(',',array_filter($weeksArr,'strlen'));
                $holiday->save(false);
            }
            else{
                $holiday = New Holidays();
                $holiday->project_id = $ProjectId;
                $holiday->weeks = $selectedWeek;
                $holiday->save(false);
            }
        }
        
        Yii::$app->helper->GetRelationcorrect($ProjectId);

        $holiday_arr = [];
        $holiday_week_arr = [];
        if($holiday = Holidays::find()->where(['project_id'=>$ProjectId])->one()){
            $holiday_arr = ($holiday->dates) ? explode(',', $holiday->dates) : '';
            $holiday_week_arr = ($holiday->weeks != '') ? explode(',', $holiday->weeks) : '';
        }

        $holidayWeekArr      = array(0 => 'Su', 1 => 'Mo', 2 => 'Tu', 3 => 'We', 4 => 'Th', 5 => 'Fr', 6 => 'Sa');
        $holidayWeekSelector = '';
        
        foreach ($holidayWeekArr as $weekVal => $weekName) {
            $weekClass = "holidayWeekGreen";
            $weekTitle = "Add as Holiday Week";
            $weekStyle = "";

            if($holiday_week_arr && in_array($weekVal, $holiday_week_arr)){
                $weekClass = "holidayWeekRed";
                $weekTitle = "Remove from Holiday Week";
            }

            if($weekVal == 6) $weekStyle = 'style="margin-right:0;"';

            $holidayWeekSelector .= '<div class="holidayWeekSelector '.$weekClass.'" data-week="'.$weekVal.'" data-projectid="'.$ProjectId.'" title="'.$weekTitle.'" '.$weekStyle.'>'.$weekName.'</div>';
        }


        $result = '
                    <div class="holidayContainer">
                        <div class="row">
                            <div class="col-md-12" >
                                <div class="row" style="text-align:center;">
                                    <div class="col-md-12" style="  ">
                                        <span style="font-size:15px; font-weight:bold; ">Holidays of Project - '.$ProjectName.'</span>
                                    </div>
                                </div>

                                <div class="row" style="padding-top:25px;text-align:center; padding-bottom:10px;">
                                    <div class="col-md-1" ></div>
                                    <div class="col-md-10" style="padding-left:15px;">

                                        <div class="row" style="border-top:1px solid #e5e5e5; border-bottom:1px solid #e5e5e5; padding:5px 0 5px 5px;">
                                            <div class="col-md-11 no-padding text-left weekly_off_selector" style="font-weight:500; font-size:14px; cursor:pointer;" >
                                                Select Week wise off days
                                            </div>
                                            <div class="col-md-1 no-padding weekly_off_selector" style="cursor:pointer;">
                                                <span style="font-size: 20px;" class="weekly_off_arrow '.$holidayWeekSelectorIcon.'"></span>
                                            </div>

                                            <div class="col-md-12 weekly_off_container" style="'.$holidayWeekSelectorVisible.' padding: 10px 0px;">
                                                '.$holidayWeekSelector.'
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-1" ></div>
                                </div>


                                <div class="row" style="">
                                    <div class="col-md-1"></div>
                                    <div class="col-md-10" style="padding-left:17px;">
                                        <div class="holidayDatepicker"></div>
                                    </div>
                                    <div class="col-md-1"></div>
                                </div>

                            </div>

                       </div>
                   </div>

                   ';

        $endDateQuery       =   ScheduleTaskReport::find()
                                ->where(['projectid' => $ProjectId])
                                ->orderBy(['task_enddate'=>SORT_DESC, 'end_time'=>SORT_DESC])
                                ->one();
        $last_reported_date = ($endDateQuery)? date("Y-m-d", strtotime($endDateQuery->task_enddate .' +1 day')) : '';

        $arr = array(   'result' => $result, 
                        'holiday_arr' => $holiday_arr,
                        'holiday_week_arr' => $holiday_week_arr,
                        'last_reported_date' => $last_reported_date,
                        'error' => 'No'
                    );
        return json_encode($arr);

    }


    

    public function actionBoqupdate()
	{
		$boq=Boq::findOne($_POST['boqid']);
		$boq->item=$_POST['item'];
		$boq->slno=$_POST['slno'];
		$boq->unit=$_POST['unit'];
		$boq->quantity=$_POST['quantity'];
		$boq->rate=$_POST['rate'];
		//$boq->activity=$_POST['activity'];
		$boq->save(false);
		$arr = array('result' => $_POST['boqid'], 'error' => 'No');
		return json_encode($arr);
	}

    public function actionNewganttchart($id, $layout = 'true')
    {
        if ($layout === 'false') {
            $this->layout = false;
        }
        Yii::$app->helper->GetRelationcorrect($id);
        $project = Projects::findOne($id);
        return $this->render('newganttview', ['projectId' => $id, 'project' => $project]);
    }

    public function actionSavescheduledata()
    {
        // validating dates
        $error = 0;
        if($error == 0)
        {

            foreach($_POST as $key => $item)
            {
                $activityId =  substr($key, strrpos($key, '_') + 1);
                $fieldName = substr($key, 0,strrpos($key, '_'));
                $activity = Scheduleactivities::findOne($activityId);
                if($fieldName == 'actual_start_date' || $fieldName == 'actual_end_date')
                {

                }
                else
                {              
                    $activity[$fieldName] = $item;
                    $activity->save(false);
                               
                }

            //do something with $item
            }
            
            $arr = array('result' => '', 'error' => 'No');
            return json_encode($arr);
        }
        else
        {
            // echo $error;exit;
            $arr = array('result' => '', 'error' => 'Yes');
            return json_encode($arr);

        }
        // exit;
    }

    public function actionGetstructureactivites()
    {
        // echo $_POST['project']; exit;
        $connection = \Yii::$app->db; 
        $query = "SELECT * FROM wbsscheduleitems WHERE projectId='" . $_POST['projectid']. "' AND status=0 ORDER BY sortorder ASC";
        $command = $connection->createCommand($query);
        $dataReader = $command->query();
        $items = $dataReader->readAll();
        
        $array = array();
        
        foreach($items as $items1){
         
            $sql = "SELECT * FROM scheduleactivities WHERE scheduleitem_id='".$items1['scheduleitem_id']."' AND projectId='".$items1['projectId']."' AND status=0 ORDER BY sortorder ASC ";
            $command = $connection->createCommand($sql);
            $dataReader = $command->query();
            $tasks = $dataReader->readAll();
            foreach($tasks as $item)
            {
                if($item['reference_activityid']!=0):
                  $Wbsscheduleitems=Wbsscheduleitems::find()->Where(['scheduleitem_id' => $items1['scheduleitem_id']])->andWhere(['status'=>0])->one();

                  $workgroupact = WorkgroupActivitiesNew::find()->Where(['id' => $item['reference_activityid']])->andWhere(['wbs_id'=>$Wbsscheduleitems->reference_itemid])->andWhere(['schedule'=>1])->andWhere(['pricing_status'=>0])->one();

                endif;

                if(isset($workgroupact)){

                    if($workgroupact->process_Id==2 || $workgroupact->process_Id==5):

                        $sql1 = "SELECT * FROM activity_relations WHERE dependent_activity='" .$item['id']. "' AND projectId='".$item['projectId']."' AND status =0 ORDER BY precedent_activity ASC";
                        $command = $connection->createCommand($sql1);
                        $dataReader = $command->query();
                        //$relation = $dataReader->read();
                        $relations = $dataReader->readAll();
                        // var_dump($item);
                        if($relations){
                            $relationParam1 = '';
                            foreach($relations as $relation){
                                if($relation['relation_type'] == 1)
                                    $relationType = 'SS';
                                elseif($relation['relation_type'] == 2)
                                    $relationType = 'FS';
                                elseif($relation['relation_type'] == 3)
                                    $relationType = 'FF';
                                else
                                    $rel =  $relationType = '';
                                $relationParam1.= $relation['precedent_activity'].'ABC'.$relation['precedent_activity'].$relationType.',';
                            }
                            $relationParam = rtrim($relationParam1, ',');
                        }
                        else{
                            $relationParam = '';
                        }

                          $spr1 = $connection->createCommand(
                              "SELECT spr.cumulated_qty, spr.start_date AS act_start,
                                      COALESCE(MAX(sprl.report_date), spr.updated_at) AS last_report
                               FROM schedule_progress_report spr
                               LEFT JOIN schedule_progress_report_log sprl
                                      ON sprl.activity_id = spr.activity_id AND sprl.currentqty > 0
                               WHERE spr.activity_id = " . (int)$item['id'] . "
                               GROUP BY spr.id"
                          )->queryOne();
                          if ($spr1 && (float)$spr1['cumulated_qty'] > 0
                              && !empty($spr1['act_start']) && $spr1['act_start'] !== '0000-00-00'
                              && !empty($spr1['last_report'])) {
                              $completedQuantity = (float)$spr1['cumulated_qty'];
                              $actualStart       = $spr1['act_start'];
                              $endDate           = $spr1['last_report'];
                          } else {
                              $completedQuantity = 0;
                              $actualStart       = $item['start_date'];
                              $endDate           = $item['end_date'];
                          }

                          $datediff = strtotime($item['actual_end_date']) - strtotime($item['actual_start_date']);
                          $duration_first = round($datediff / (60 * 60 * 24)) + 1;
                          if($duration_first < $item['duration']){
                            $delay = $item['duration'] - $duration_first; 
                            $delaystart = ($delay / $item['duration']) * 100;
                          }
                          else{
                            $delaystart = 0;
                          }

                        if($item['actual_start_date']!='' && $item['actual_start_date']!='0000-00-00' && $item['actual_end_date']!='' && $item['actual_end_date']!='0000-00-00'){
                            $datediff = strtotime($item['actual_end_date']) - strtotime($item['actual_start_date']);
                            $bduration1 = round($datediff / (60 * 60 * 24)) + 1;
                            $bduration = $bduration1 .' Days';
                        }
                        else{
                            $bduration = '';
                        }

                            // echo 

                        if($relations){
                            $array[]=array('id'=> $item['id'], 'name' => $item['name'], 'precedent_activity' => $relation['precedent_activity'], 'scheduleitem_id' => $item['scheduleitem_id'], 'status' => $item['status'], 'projectId' => $item['projectId'], 'quantity' => $item['quantity'], 'duration' => $item['duration'], 'start_date' => $item['start_date'], 'end_date'=> $item['end_date'], 'actual_start_date'=> $actualStart, 'actual_end_date'=> $endDate,'completedQuantity'=>$completedQuantity,'delaystart' => $delaystart, 'relation' => $relationParam,'float'=> $item['float_duration'],'delay'=>$item['delay'],'bduration'=>$bduration); 
                        }
                        else{
                            $array[]=array('id'=> $item['id'], 'name' => $item['name'], 'precedent_activity' => null, 'scheduleitem_id' => $item['scheduleitem_id'], 'status' => $item['status'], 'projectId' => $item['projectId'], 'quantity' => $item['quantity'], 'duration' => $item['duration'], 'start_date' => $item['start_date'], 'end_date'=> $item['end_date'], 'actual_start_date'=> $actualStart, 'actual_end_date'=> $endDate,'completedQuantity'=>$completedQuantity,'delaystart' => $delaystart, 'relation' => $relationParam,'float'=> $item['float_duration'],'delay'=>$item['delay'],'bduration'=>$bduration); 
                        }
                    endif;

                }
                elseif(empty($workgroupact) && $item['reference_activityid']==0)
                {

                    $sql1 = "SELECT * FROM activity_relations WHERE dependent_activity='" .$item['id']. "' AND projectId='".$item['projectId']."' AND status =0 ORDER BY precedent_activity ASC";
                    $command = $connection->createCommand($sql1);
                    $dataReader = $command->query();
                    //$relation = $dataReader->read();
                    $relations = $dataReader->readAll();
                    // var_dump($item);
                    if($relations){
                        $relationParam1 = '';
                        foreach($relations as $relation){
                            if($relation['relation_type'] == 1)
                                $relationType = 'SS';
                            elseif($relation['relation_type'] == 2)
                                $relationType = 'FS';
                            elseif($relation['relation_type'] == 3)
                                $relationType = 'FF';
                            else
                                $rel =  $relationType = '';
                            $relationParam1.= $relation['precedent_activity'].'ABC'.$relation['precedent_activity'].$relationType.',';
                        }
                        $relationParam = rtrim($relationParam1, ',');
                    }
                    else{
                        $relationParam = '';
                    }

                    $spr1 = $connection->createCommand(
                        "SELECT spr.cumulated_qty, spr.start_date AS act_start,
                                COALESCE(MAX(sprl.report_date), spr.updated_at) AS last_report
                         FROM schedule_progress_report spr
                         LEFT JOIN schedule_progress_report_log sprl
                                ON sprl.activity_id = spr.activity_id AND sprl.currentqty > 0
                         WHERE spr.activity_id = " . (int)$item['id'] . "
                         GROUP BY spr.id"
                    )->queryOne();
                    if ($spr1 && (float)$spr1['cumulated_qty'] > 0
                        && !empty($spr1['act_start']) && $spr1['act_start'] !== '0000-00-00'
                        && !empty($spr1['last_report'])) {
                        $completedQuantity = (float)$spr1['cumulated_qty'];
                        $actualStart       = $spr1['act_start'];
                        $endDate           = $spr1['last_report'];
                    } else {
                        $completedQuantity = 0;
                        $actualStart       = $item['start_date'];
                        $endDate           = $item['end_date'];
                    }

                    $datediff = strtotime($item['actual_end_date']) - strtotime($item['actual_start_date']);
                    $duration_first = round($datediff / (60 * 60 * 24)) + 1;

                    $datediff1 = strtotime($item['end_date']) - strtotime($item['actual_start_date']);
                    $fullduration = round($datediff1 / (60 * 60 * 24)) + 1;
                    if($duration_first < $item['duration']){
                        $delay = $item['duration'] - $duration_first; 
                        if($item['start_date']>$item['actual_start_date'] && count($relations)==0){
                            $delaystart = ($delay / $fullduration) * 100;
                            //$delaystart = ($delay / $item['duration']) * 100;
                        }
                        else{
                            $delaystart = ($delay / $item['duration']) * 100;
                        }
                    }
                    else{
                        $delaystart = 0;
                    }

                    if($item['actual_start_date']!='' && $item['actual_start_date']!='0000-00-00' && $item['actual_end_date']!='' && $item['actual_end_date']!='0000-00-00'){
                            $datediff = strtotime($item['actual_end_date']) - strtotime($item['actual_start_date']);
                            $bduration1 = round($datediff / (60 * 60 * 24)) + 1;
                            $bduration = $bduration1 .'/'.$item['duration'] .' Days';
                    }
                    else{
                        $bduration = '';
                    }

                    //$connection = CActiveRecord::getDbConnection();
                    $reportlog = "SELECT SUM(currentqty) AS totalqty FROM schedule_progress_report_log WHERE activity_id='".$item['id']."'";
                    $command = $connection->createCommand($reportlog);
                    $dataReader = $command->query();
                    $totalQty = $dataReader->read();

                    if($totalQty['totalqty']>0){
                        $reprtdqty = $totalQty['totalqty'];
                    }
                    else{
                        $reprtdqty = 0;
                    }

                    $Qty = $item['quantity'];

                    $depRelations = ActivityRelations::find()->Where(['dependent_activity' => $item['id']])->andWhere(['projectId' => $item['projectId']])->andWhere(['status'=>0])->all();

                    $scheduleID = '';

                    $scheduleStartdate = '';
                    $scheduleEnddate = '';
                    foreach($depRelations as $depRelation):
                        $thisAct = Scheduleactivities::findOne($depRelation->precedent_activity);
                        $thisdepAct = Scheduleactivities::findOne($depRelation->dependent_activity);
                        if (!$thisAct || !$thisdepAct) continue;
                        if($depRelation->relation_type == 1){
                            $deplag = $thisdepAct->lag;
                            $depduration = $thisdepAct->duration + $deplag - 1;
                            $depstart = date('Y-m-d', strtotime($thisAct->start_date. ' + '.$deplag.' days'));
                            $depend = date('Y-m-d', strtotime($thisAct->start_date. ' + '.$depduration.' days'));
                        }
                        elseif($depRelation->relation_type == 2)
                        {
                            $deplag = $thisdepAct->lag + 1;
                            $depstart = date('Y-m-d', strtotime($thisAct->end_date. ' + '.$deplag.' days'));
                            $depduration = $thisdepAct->duration - 1;
                            $depend = date('Y-m-d', strtotime($depstart. ' + '.$depduration.' days'));
                        }
                        elseif($depRelation->relation_type == 3)
                        {
                            $deplag = $thisdepAct->lag;
                            $duration = $thisdepAct->duration - 1;
                            $depend = date('Y-m-d', strtotime($thisAct->end_date. ' + '.$deplag.' days'));
                            $depstart = date('Y-m-d', strtotime($depend. ' - '.$duration.' days'));
                        }
                        if($scheduleEnddate=='' || $depend > $scheduleEnddate){
                            //if($depRelation->relation_type==2){
                                $scheduleID = $thisAct->id;
                                $scheduleStartdate = $depstart;
                                $scheduleEnddate = $depend;
                            //}
                        }
                    endforeach;

                    if(count($depRelations)>0){
                        $budgtstart1 = $scheduleStartdate;
                    }
                    else{
                        $budgtstart1 = $item['actual_start_date'];
                    }

                    if($relationParam==''){
                        $budgtstart = $item['actual_start_date'];
                    }
                    else{
                        $budgtstart = $budgtstart1;
                    }

                    if(isset($bduration1)){

                        $oldduration = $bduration1;

                    }
                    else{
                        $oldduration = 0;
                    }

                    if($relations){

                        $array[]=array('id'=> $item['id'], 'name' => $item['name'], 'precedent_activity' => $relation['precedent_activity'], 'dependent_activity' => $relation['dependent_activity'], 'scheduleitem_id' => $item['scheduleitem_id'], 'status' => $item['status'], 'projectId' => $item['projectId'], 'quantity' => $Qty, 'duration' => $item['duration'], 'start_date' => $item['start_date'], 'end_date'=> $item['end_date'], 'actual_start_date'=> $actualStart, 'actual_end_date'=> $endDate, 'activity_start'=> $budgtstart, 'activity_end'=> $item['actual_end_date'],'completedQuantity'=>$completedQuantity,'delaystart' => $delaystart, 'relation' => $relationParam,'float'=> $item['float_duration'],'delay'=>$item['delay'],'bduration'=>$bduration,'depstart'=>$scheduleStartdate,'reprtdqty'=>$reprtdqty,'olddur'=>$oldduration);

                    }
                    else{
                      $array[]=array('id'=> $item['id'], 'name' => $item['name'], 'precedent_activity' => null, 'dependent_activity' => null, 'scheduleitem_id' => $item['scheduleitem_id'], 'status' => $item['status'], 'projectId' => $item['projectId'], 'quantity' => $Qty, 'duration' => $item['duration'], 'start_date' => $item['start_date'], 'end_date'=> $item['end_date'], 'actual_start_date'=> $actualStart, 'actual_end_date'=> $endDate, 'activity_start'=> $budgtstart, 'activity_end'=> $item['actual_end_date'],'completedQuantity'=>$completedQuantity,'delaystart' => $delaystart, 'relation' => $relationParam,'float'=> $item['float_duration'],'delay'=>$item['delay'],'bduration'=>$bduration,'depstart'=>$scheduleStartdate,'reprtdqty'=>$reprtdqty,'olddur'=>$oldduration);  
                    }

                }

            }
            
        }

        $arr = array('result' => $array, 'error' => 'No');
        return json_encode($arr);
    }

    public function actionGetcriticalpath()
    {
        // Textbook CPM: the engine in HelperComponent::GetRelationcorrect()
        // computes ES/EF/LS/LF, total float and critical_status for every
        // activity (forward pass, virtual finish, backward pass). This action
        // just runs it and reports the zero-float activities.
        $dependentAct1 = Scheduleactivities::findOne($_POST['activityid']);
        if (!$dependentAct1) {
            return json_encode(['error' => 'Yes', 'errortext' => 'Activity not found.']);
        }
        Yii::$app->helper->GetRelationcorrect($dependentAct1->projectId);

        $criticalids = Scheduleactivities::find()
            ->select('id')
            ->where(['projectId' => $dependentAct1->projectId, 'status' => 0, 'critical_status' => 'Yes'])
            ->column();

        return json_encode(['error' => 'No', 'criticalIDs' => array_map('intval', $criticalids)]);
    }

    /* Legacy critical-path machinery (anchor scan, recursive zero-float walk,
       parallel-activity hack, Getbackwardpass) removed — superseded by the
       textbook CPM engine in HelperComponent::GetRelationcorrect(). */
    private function getbackwardpassRemoved($dependentAct)
    {
        $projectid = $dependentAct->projectId;
        $availableActivites = Scheduleactivities::find()->where(['projectId' => $projectid])->andWhere(['status' => 0])->orderBy(['end_date' => SORT_DESC])->all();
        
        //if(count($availableActivites) > 0)
        if(!empty($availableActivites))
        {
            //$Ids = array($_POST['activityid']);
            $Ids = array($dependentAct->id);
            $dependentid = '';
            foreach ($availableActivites as $activity) 
            {          
                  $acId = $activity->id;
                  //$allRelations = ActivityRelations::model()->findAll(array('condition'=>'precedent_activity ='.$acId.' AND dependent_activity ='.$dependentid.' AND status =0'));
                  $allRelations = ActivityRelations::find()->Where(['precedent_activity' => $acId])->andWhere(['projectId' => $projectid])->andWhere(['status'=>0])->all();

                //if(count($allRelations) > 0)
                if(!empty($allRelations))
                {
                    foreach ($allRelations AS $relation)
                    {
                        $depentAct = Scheduleactivities::findOne($relation->dependent_activity);
                        $precAct = Scheduleactivities::findOne($relation->precedent_activity);

                        if (!$depentAct || !$precAct) continue;

                        // Use projected duration from CPM table; fall back to scheduleactivities.duration
                        $cpmDurRow = \Yii::$app->db->createCommand(
                            "SELECT proj_duration FROM activity_cpm_dates WHERE activity_id = :aid AND cpm_end IS NOT NULL LIMIT 1",
                            [':aid' => $precAct->id]
                        )->queryOne();
                        $precProjDur = $cpmDurRow ? (int)round((float)$cpmDurRow['proj_duration']) : $precAct->duration;

                        if($relation->relation_type==1){
                            //SS — backward: prec.LateStart = dep.LateStart - lag
                            $lag = $depentAct->lag;
                            $duration = $precProjDur - 1;
                            $dep_late_start = ($depentAct->critical_start && $depentAct->critical_start != '0000-00-00')
                                ? $depentAct->critical_start : $depentAct->start_date;
                            $backward_start = date('Y-m-d', strtotime(Yii::$app->helper->getDateAfterHoliday($dep_late_start, $projectid, $lag, 'previous')));
                            $precAct->critical_start = $backward_start;
                            $precAct->critical_end = date('Y-m-d', strtotime(Yii::$app->helper->getDateAfterHoliday($backward_start, $projectid, $duration, 'next')));
                        }
                        elseif($relation->relation_type==2){
                            //FS — backward: prec.LateFinish = dep.LateStart - lag - 1
                            $lag = $depentAct->lag + 1;
                            $duration = $precProjDur - 1;
                            $dep_late_start = ($depentAct->critical_start && $depentAct->critical_start != '0000-00-00')
                                ? $depentAct->critical_start : $depentAct->start_date;
                            $backward_end = date('Y-m-d', strtotime(Yii::$app->helper->getDateAfterHoliday($dep_late_start, $projectid, $lag, 'previous')));
                            $precAct->critical_start = date('Y-m-d', strtotime(Yii::$app->helper->getDateAfterHoliday($backward_end, $projectid, $duration, 'previous')));
                            $precAct->critical_end = $backward_end;
                        }
                        elseif($relation->relation_type==3){
                            //FF
                            $lag = $depentAct->lag;
                            $duration = $precProjDur - 1;
                            if($depentAct->critical_end && $depentAct->critical_end != '0000-00-00'){
                                $backward_end = date('Y-m-d', strtotime(Yii::$app->helper->getDateAfterHoliday($depentAct->critical_end, $projectid, $lag, 'previous')));
                            }
                            else{
                                $backward_end = date('Y-m-d', strtotime(Yii::$app->helper->getDateAfterHoliday($depentAct->end_date, $projectid, $lag, 'previous')));
                            }
                            $precAct->critical_end   = $backward_end;
                            $precAct->critical_start = date('Y-m-d', strtotime(Yii::$app->helper->getDateAfterHoliday($backward_end, $projectid, $duration, 'previous')));
                        }
                        $precAct->save(false);
                        /*if($precAct->critical_start == $precAct->start_date){
                            array_push($Ids, $relation->precedent_activity);
                            $dependentid = $relation->precedent_activity;
                        } */
                    }
                }
            }
        }
    }

    public function actionGanttitems()
    {
        $projectId = (int)$_POST['projectid'];
        $rows = \Yii::$app->db->createCommand("
            SELECT a.scheduleitem_id, a.name,
                   MIN(s.actual_start_date)       AS start_date,
                   MAX(s.actual_end_date)         AS end_date,
                   b.iowGroupid,
                   COALESCE(ig.name, '')          AS iow_group_name,
                   COALESCE(b.sortorder, 999999)  AS wg_sortorder,
                   b.Workgroup_Id,
                   MIN(COALESCE(act_a.a_start, s.actual_start_date))      AS a_min_start,
                   MAX(COALESCE(act_a.a_end_computed, s.actual_end_date)) AS a_max_end,
                   CASE
                     WHEN MIN(COALESCE(act_a.a_start, s.actual_start_date)) IS NOT NULL
                          AND MAX(COALESCE(act_a.a_end_computed, s.actual_end_date)) IS NOT NULL
                     THEN DATEDIFF(
                            MAX(COALESCE(act_a.a_end_computed, s.actual_end_date)),
                            MIN(COALESCE(act_a.a_start, s.actual_start_date))
                          ) + 1
                     ELSE NULL
                   END AS a_duration
            FROM wbsscheduleitems AS a
            LEFT JOIN workgroups_new AS b ON a.wbsid = b.Workgroup_Id
            LEFT JOIN iow_groups AS ig ON ig.id = b.iowGroupid AND ig.status = 0
            JOIN scheduleactivities AS s
                 ON s.scheduleitem_id = a.scheduleitem_id
                 AND s.status = 0
                 AND s.actual_start_date IS NOT NULL
                 AND s.actual_start_date != '0000-00-00'
            LEFT JOIN (
                SELECT
                    spr_i.activity_id,
                    spr_i.start_date AS a_start,
                    CASE
                      WHEN rpt_i.cumulated_qty > 0 AND sa_i.quantity > 0
                           AND spr_i.start_date IS NOT NULL
                           AND spr_i.start_date != '0000-00-00'
                      THEN DATE_ADD(
                             spr_i.start_date,
                             INTERVAL GREATEST(0, ROUND(
                                 (DATEDIFF(rpt_i.last_report_date, spr_i.start_date) + 1)
                                 / rpt_i.cumulated_qty * sa_i.quantity
                             ) - 1) DAY
                           )
                      ELSE NULL
                    END AS a_end_computed
                FROM schedule_progress_report AS spr_i
                JOIN scheduleactivities AS sa_i
                     ON sa_i.id = spr_i.activity_id AND sa_i.status = 0
                JOIN (
                    SELECT activity_id,
                           MAX(report_date) AS last_report_date,
                           SUM(currentqty)  AS cumulated_qty
                    FROM schedule_progress_report_log
                    GROUP BY activity_id
                ) AS rpt_i ON rpt_i.activity_id = spr_i.activity_id
                WHERE spr_i.start_date IS NOT NULL AND spr_i.start_date != '0000-00-00'
            ) AS act_a ON act_a.activity_id = s.id
            WHERE a.projectId = :projectId AND a.status = 0
            GROUP BY a.scheduleitem_id
            ORDER BY COALESCE(b.sortorder, 999999) ASC, b.Workgroup_Id ASC, a.scheduleitem_id ASC
        ", [':projectId' => $projectId])->queryAll();
        return json_encode(['result' => $rows, 'error' => 'No']);
    }

    public function actionGanttactivities()
    {
        $itemId = (int)$_POST['itemId'];
        $rows = \Yii::$app->db->createCommand("
            SELECT sa.id, sa.name,
                   sa.start_date, sa.actual_start_date, sa.actual_end_date,
                   sa.old_duration, sa.quantity,
                   sa.scheduleitem_id, sa.critical_status,
                   CASE
                     WHEN rpt.cumulated_qty > 0
                          AND sa.quantity > 0
                          AND (
                              (sa.start_date IS NOT NULL AND sa.start_date != '0000-00-00')
                              OR (spr.start_date IS NOT NULL AND spr.start_date != '0000-00-00')
                          )
                     THEN ROUND(
                            (DATEDIFF(rpt.last_report_date,
                                CASE WHEN sa.start_date IS NOT NULL AND sa.start_date != '0000-00-00'
                                          AND spr.start_date IS NOT NULL AND spr.start_date != '0000-00-00'
                                     THEN LEAST(sa.start_date, spr.start_date)
                                     WHEN spr.start_date IS NOT NULL AND spr.start_date != '0000-00-00'
                                     THEN spr.start_date
                                     ELSE sa.start_date END
                            ) + 1)
                            / rpt.cumulated_qty * sa.quantity
                          )
                     ELSE NULL
                   END AS actual_duration,
                   CASE WHEN sa.start_date IS NOT NULL AND sa.start_date != '0000-00-00'
                             AND spr.start_date IS NOT NULL AND spr.start_date != '0000-00-00'
                        THEN LEAST(sa.start_date, spr.start_date)
                        WHEN spr.start_date IS NOT NULL AND spr.start_date != '0000-00-00'
                        THEN spr.start_date
                        ELSE sa.start_date END AS spr_start_date,
                   rpt.last_report_date  AS spr_end_date,
                   GROUP_CONCAT(
                       CONCAT(ar.precedent_activity,'ABC',ar.precedent_activity,
                           CASE ar.relation_type WHEN 1 THEN 'SS' WHEN 2 THEN 'FS' WHEN 3 THEN 'FF' ELSE 'FS' END)
                       ORDER BY ar.id SEPARATOR ','
                   ) AS depends
            FROM scheduleactivities AS sa
            LEFT JOIN (
                SELECT activity_id,
                       MAX(report_date) AS last_report_date,
                       SUM(currentqty)  AS cumulated_qty
                FROM schedule_progress_report_log
                GROUP BY activity_id
            ) AS rpt ON rpt.activity_id = sa.id
            LEFT JOIN schedule_progress_report AS spr ON spr.activity_id = sa.id
            LEFT JOIN activity_relations AS ar
                   ON ar.dependent_activity = sa.id AND ar.status = 0 AND ar.projectId = sa.projectId
            LEFT JOIN workgroup_activities_new AS wa ON wa.id = sa.activity_id
            WHERE sa.scheduleitem_id = :itemId AND sa.status = 0
            GROUP BY sa.id
            ORDER BY COALESCE(wa.sortorder, 999999) ASC, sa.id ASC
        ", [':itemId' => $itemId])->queryAll();
        return json_encode(['result' => $rows, 'error' => 'No']);
    }

    public function actionGanttlastactivity()
    {
        $projectId = (int)$_POST['projectid'];
        $act = \Yii::$app->db->createCommand(
            "SELECT id FROM scheduleactivities WHERE projectId = :p AND status = 0 AND actual_end_date IS NOT NULL AND actual_end_date != '0000-00-00' ORDER BY actual_end_date DESC LIMIT 1",
            [':p' => $projectId]
        )->queryOne();
        return json_encode(['id' => $act ? (int)$act['id'] : null, 'error' => 'No']);
    }

    public function actionResourcereport()
    {
        $connection = Yii::$app->db;
        //$projectid=36;
        $uid = Yii::$app->user->Id; 
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        $projectid= $projuser->projectid;
        $resourcetypes = Resourcetype::find()->all();
        $datarows='';
        $pricesum=0;
        $count=0;
        $totalrestypecost=0;
        if($resourcetypes):

            foreach($resourcetypes AS $resourcetype):

                $sqltemvals1 = "SELECT * FROM workgroups_new WHERE Project_Id=' ".$projectid." ' AND Status=0 ORDER BY sortorder ASC, Workgroup_Id ASC, Name ASC";
                $command = $connection->createCommand($sqltemvals1);
                $dataReader1 = $command->query();
                $structures = $dataReader1->readAll();
                if($structures){
                    $restypecost = 0;
                    
                    foreach($structures AS $structure):
                        $sql = "SELECT * FROM workgroup_activities_new WHERE project_Id='".$structure['Project_Id']."' AND wbs_id='".$structure['Workgroup_Id']."' AND estimate=1 AND pricing_status=0 ORDER BY sortorder ASC ";
                        $command = $connection->createCommand($sql);
                        $dataReader = $command->query();
                        $wbsactivities = $dataReader->readAll();
                        if($wbsactivities){
                            $subtot=0;
                            foreach($wbsactivities as $key=> $wbsactivity):
                                $pricingestimate=PricingEstimateNew::find()->where(['project_Id' => $projectid])->andWhere(['activity_Id' =>$wbsactivity['id']])->andWhere(['pricing_status' =>0])->one();

                                $pricingestimateres=PricingEstimateResourcesNew::find()->where(['project_Id' => $projectid])->andWhere(['activity_Id' =>$wbsactivity['id']])->andWhere(['resourcetype_Id' =>$resourcetype->ResourceType_Id])->andWhere(['pricing_status' =>0])->all();

                                $specrate=0;

                                if($pricingestimateres):
                                    foreach($pricingestimateres AS $pricingestimatere):
                                        $specrate=$specrate + ($pricingestimatere['rate'] * $pricingestimatere['quantity']);
                                    endforeach;
                                endif;
                                if($pricingestimate){
                                $amount=$pricingestimate->activity_qty * $specrate;
                                $subtot = $subtot + $amount;
                                $restypecost = $restypecost + $amount;
                            }
                            endforeach;
                            
                            $pricesum=$pricesum + $subtot; 
                            $totalrestypecost+=$restypecost;
                        }
                    endforeach;

                    if($restypecost!=0){
                        $datarows.= '<tr><td>'.(++$count).'</td><td colspan="9">'.$resourcetype->Name.'</td>
                        <td colspan="9" style="text-align: end;">'.number_format($restypecost,2).'</td>
                        <td><a href="#" class="btn btn-primary text-button viewresourcedetails" data-id="'.$resourcetype['Name'].'"  title="Details"><span class="icon-receipt" style="margin-right: 7px;""></span>Details</a></td>
                        </tr>';
                    }
                }  

            endforeach;
            
             $datarows.='<tr><td></td><td colspan="9" ><b>Total</b></td>
                            <td colspan="9" style="text-align: end;">'.number_format($totalrestypecost,2).'</td>

                          </tr>';


        endif;
        $arr = array('result'=>$datarows,'projectid'=>$projectid,'error'=>'No');
        // echo $arr;
        return json_encode($arr);
    }

    public function actionResourcereportdetails()
    {
         $connection = Yii::$app->db;
        //$projectid=36;
        $uid = Yii::$app->user->Id; 
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        $projectid= $projuser->projectid;

        

        $resourcelist = '';

        $sql = "SELECT * FROM resourcetype WHERE Name LIKE '%".$_POST['resourcetype']."%' AND Status=0 ORDER BY sortorder ASC";
        $command = $connection->createCommand($sql);
        $dataReader1 = $command->query();
        $resourcetypes = $dataReader1->readAll();

        $pricesum=0;
        if($resourcetypes):

            foreach($resourcetypes AS $resourcetype):

                $resnamearr=array();

                $sqltemvals1 = "SELECT * FROM workgroups_new WHERE Project_Id=' ".$projectid." ' AND Status=0 ORDER BY sortorder ASC, Workgroup_Id ASC, Name ASC";
                $command = $connection->createCommand($sqltemvals1);
                $dataReader1 = $command->query();
                $structures = $dataReader1->readAll();
                if($structures){
                    $restypecost = 0;
                    foreach($structures AS $structure):
                        $sql = "SELECT * FROM workgroup_activities_new WHERE project_Id='".$structure['Project_Id']."' AND wbs_id='".$structure['Workgroup_Id']."' AND estimate=1 AND pricing_status=0 ORDER BY sortorder ASC ";
                        $command = $connection->createCommand($sql);
                        $dataReader = $command->query();
                        $wbsactivities = $dataReader->readAll();
                        if($wbsactivities){
                            $subtot=0;
                            foreach($wbsactivities as $key=> $wbsactivity):
                                $pricingestimate=PricingEstimateNew::find()->where(['project_Id' => $projectid])->andWhere(['activity_Id' =>$wbsactivity['id']])->andWhere(['pricing_status' =>0])->one();

                                $pricingestimateres=PricingEstimateResourcesNew::find()->where(['project_Id' => $projectid])->andWhere(['activity_Id' =>$wbsactivity['id']])->andWhere(['resourcetype_Id' =>$resourcetype['ResourceType_Id']])->andWhere(['pricing_status' =>0])->all();

                                $specrate=0;

                                if($pricingestimateres):
                                    foreach($pricingestimateres AS $pricingestimatere):
                                        $specrate=$specrate + ($pricingestimatere['rate'] * $pricingestimatere['quantity']);

                                        $specrate1 =$pricingestimatere['rate'] * $pricingestimatere['quantity'];

                                        $amount1=$pricingestimate->activity_qty * $specrate1;

                                        $resource = Resources::findOne($pricingestimatere['resource_Id']);

                                        array_push($resnamearr, array("resname" => $resource->Name, "amount" => $amount1));

                                        //$datarows.= "['".$resource->Name."', ".$amount1."],";
                                    endforeach;
                                endif;
                                if($pricingestimate){
                                $amount=$pricingestimate->activity_qty * $specrate;
                                $subtot = $subtot + $amount;
                                $restypecost = $restypecost + $amount;
                            }
                            endforeach;
                            $pricesum=$pricesum + $subtot; 
                        }
                    endforeach;

                    $restotals = array_reduce($resnamearr, function ($a, $b) {
                        isset($a[$b['resname']]) ? $a[$b['resname']]['amount'] += $b['amount'] : $a[$b['resname']] = $b;  
                        return $a;
                    });

                    $allresources = array_values($restotals);
                    $counts=0;
                    foreach($allresources as $allresource):
                        $sql1 = "SELECT *  FROM `resources` WHERE `Name` LIKE '".$allresource['resname']."' AND pricing_status=0";
                        $command = $connection->createCommand($sql1);
                        $dataReader = $command->query();
                        $resources = $dataReader->readAll();
                        $resids='';
                        
                        foreach ($resources as $key => $resource) {  
                            if($key==0):
                                $resids.=$resource['Resource_Id'];
                                $resunit = $resource['Unit'];
                            else:
                                $resids.=','.$resource['Resource_Id'];
                            endif;
                        }

                        $pricingestimateres=PricingEstimateResourcesNew::find()->where(['project_Id' => $projectid])->andWhere('resource_Id IN('.$resids.')')->andWhere(['pricing_status' =>0])->all();

                        $specrate=0;

                        $specqty=0;

                        $count=0;

                        if($pricingestimateres):
                            foreach($pricingestimateres AS $pricingestimatere):
                                $pricingestimate=PricingEstimateNew::find()->where(['project_Id' => $projectid])->andWhere(['activity_Id' =>$pricingestimatere->activity_id])->andWhere(['pricing_status' =>0])->one();
                                if($pricingestimate){
                                $specrate=$specrate + $pricingestimatere['rate'];
                                $specqty = $specqty + ($pricingestimate->activity_qty*$pricingestimatere['quantity']);
                            }
                                $count++;
                            endforeach;
                        endif;
                        $resourceqty = $specqty;

                        $resourcelist.= '<tr>
                                        <td>'.(++$counts).'</td>
                                        <td>'.$allresource['resname'].'</td>
                                        <td>'.$resourceqty.'</td>
                                        <td style="text-align: end;" width="25px">'.number_format($allresource['amount'],2).'</td>
                                    </tr>';

                    endforeach;

                    $resourcelist.= '<tr>
                                        <td></td>
                                        <td><b>Total</b></td>
                                        <td></td>
                                        <td style="text-align: end;" width="25px"><b>'.number_format($pricesum,2).'</b></td>
                                        <td></td>
                                    </tr>';    

                }  

            endforeach;

        endif;
        $restypename = ' '.$_POST['resourcetype'].' ';
        
        //echo $datarows; exit;
        //print_r(array_values($restotals)); exit;

        $arr = array('result'=>$resourcelist, 'restypename'=>$restypename, 'error'=>'No');
        // echo $arr;
        return json_encode($arr);
    }

    public function actionPrjschedulesearch()
    {
        $uid = Yii::$app->user->Id; 
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        $prjectid=$projuser->projectid;
        if($projuser)
        {
            $scheduleItems = Wbsscheduleitems::find()->where(['projectId' => $prjectid])->andWhere(['status' => 0])->orderBy(['scheduleitem_id' => SORT_ASC])->all();
            $datarows="";
            $datarows='<option value="none">Select Item</option>
                        <option value="0">All</option>';

            if($scheduleItems) {
                foreach($scheduleItems as $key => $data) {
                    $scheduleactivities = Scheduleactivities::find()->where(['projectId' => $prjectid])->andWhere(['scheduleitem_id'=>$data->scheduleitem_id])->andWhere(['status'=>0])->all();

                    
                            $datarows.='
                               
                                <option value="'.$data['scheduleitem_id'].'">'.$data['name'].'</option>
                            
                            
                            ';

                            
                }
            }
           

            
                      
            $arr = array('result' => $datarows,'error'=>'No');
            return json_encode($arr);

        }      
    }
    public function actionGetstructuresactivites()
    {
        // echo $_POST['project']; exit;
        $connection = \Yii::$app->db; 
        $query = "SELECT * FROM wbsscheduleitems WHERE scheduleitem_id='" . $_POST['scheduleitemid']. "' AND status=0 ORDER BY sortorder ASC";
        $command = $connection->createCommand($query);
        $dataReader = $command->query();
        $items = $dataReader->readAll();
        
        $array = array();
        
        foreach($items as $items1){
         
            $sql = "SELECT * FROM scheduleactivities WHERE scheduleitem_id='".$items1['scheduleitem_id']."'  AND status=0 ORDER BY sortorder ASC ";
            $command = $connection->createCommand($sql);
            $dataReader = $command->query();
            $tasks = $dataReader->readAll();
            foreach($tasks as $item)
            {
                if($item['reference_activityid']!=0):
                  $Wbsscheduleitems=Wbsscheduleitems::find()->Where(['scheduleitem_id' => $items1['scheduleitem_id']])->andWhere(['status'=>0])->one();

                  $workgroupact = WorkgroupActivitiesNew::find()->Where(['id' => $item['reference_activityid']])->andWhere(['wbs_id'=>$Wbsscheduleitems->reference_itemid])->andWhere(['schedule'=>1])->andWhere(['pricing_status'=>0])->one();

                endif;

                /*if(isset($workgroupact)){

                    if($workgroupact->process_Id==2 || $workgroupact->process_Id==5):

                        $sql1 = "SELECT * FROM activity_relations WHERE dependent_activity='" .$item['id']. "' AND projectId='".$item['projectId']."' AND status =0 ORDER BY precedent_activity ASC";
                        $command = $connection->createCommand($sql1);
                        $dataReader = $command->query();
                        //$relation = $dataReader->read();
                        $relations = $dataReader->readAll();
                        // var_dump($item);
                        if($relations){
                            $relationParam1 = '';
                            foreach($relations as $relation){
                                if($relation['relation_type'] == 1)
                                    $relationType = 'SS';
                                elseif($relation['relation_type'] == 2)
                                    $relationType = 'FS';
                                elseif($relation['relation_type'] == 3)
                                    $relationType = 'FF';
                                else
                                    $rel =  $relationType = '';
                                $relationParam1.= $relation['precedent_activity'].'ABC'.$relation['precedent_activity'].$relationType.',';
                            }
                            $relationParam = rtrim($relationParam1, ',');
                        }
                        else{
                            $relationParam = '';
                        }

                          $spr1 = $connection->createCommand(
                              "SELECT spr.cumulated_qty, spr.start_date AS act_start,
                                      COALESCE(MAX(sprl.report_date), spr.updated_at) AS last_report
                               FROM schedule_progress_report spr
                               LEFT JOIN schedule_progress_report_log sprl
                                      ON sprl.activity_id = spr.activity_id AND sprl.currentqty > 0
                               WHERE spr.activity_id = " . (int)$item['id'] . "
                               GROUP BY spr.id"
                          )->queryOne();
                          if ($spr1 && (float)$spr1['cumulated_qty'] > 0
                              && !empty($spr1['act_start']) && $spr1['act_start'] !== '0000-00-00'
                              && !empty($spr1['last_report'])) {
                              $completedQuantity = (float)$spr1['cumulated_qty'];
                              $actualStart       = $spr1['act_start'];
                              $endDate           = $spr1['last_report'];
                          } else {
                              $completedQuantity = 0;
                              $actualStart       = $item['start_date'];
                              $endDate           = $item['end_date'];
                          }

                          $datediff = strtotime($item['actual_end_date']) - strtotime($item['actual_start_date']);
                          $duration_first = round($datediff / (60 * 60 * 24)) + 1;
                          if($duration_first < $item['duration']){
                            $delay = $item['duration'] - $duration_first; 
                            $delaystart = ($delay / $item['duration']) * 100;
                          }
                          else{
                            $delaystart = 0;
                          }

                        if($item['actual_start_date']!='' && $item['actual_start_date']!='0000-00-00' && $item['actual_end_date']!='' && $item['actual_end_date']!='0000-00-00'){
                            $datediff = strtotime($item['actual_end_date']) - strtotime($item['actual_start_date']);
                            $bduration1 = round($datediff / (60 * 60 * 24)) + 1;
                            $bduration = $bduration1 .' Days';
                        }
                        else{
                            $bduration = '';
                        }

                            // echo 

                        if($relations){
                            $array[]=array('id'=> $item['id'], 'name' => $item['name'], 'precedent_activity' => $relation['precedent_activity'], 'scheduleitem_id' => $item['scheduleitem_id'], 'status' => $item['status'], 'projectId' => $item['projectId'], 'quantity' => $item['quantity'], 'duration' => $item['duration'], 'start_date' => $item['start_date'], 'end_date'=> $item['end_date'], 'actual_start_date'=> $actualStart, 'actual_end_date'=> $endDate,'completedQuantity'=>$completedQuantity,'delaystart' => $delaystart, 'relation' => $relationParam,'float'=> $item['float_duration'],'delay'=>$item['delay'],'bduration'=>$bduration); 
                        }
                        else{
                            $array[]=array('id'=> $item['id'], 'name' => $item['name'], 'precedent_activity' => null, 'scheduleitem_id' => $item['scheduleitem_id'], 'status' => $item['status'], 'projectId' => $item['projectId'], 'quantity' => $item['quantity'], 'duration' => $item['duration'], 'start_date' => $item['start_date'], 'end_date'=> $item['end_date'], 'actual_start_date'=> $actualStart, 'actual_end_date'=> $endDate,'completedQuantity'=>$completedQuantity,'delaystart' => $delaystart, 'relation' => $relationParam,'float'=> $item['float_duration'],'delay'=>$item['delay'],'bduration'=>$bduration); 
                        }
                    endif;

                }
                elseif(empty($workgroupact) && $item['reference_activityid']==0)
                {*/

                    $sql1 = "SELECT * FROM activity_relations WHERE dependent_activity='" .$item['id']. "' AND projectId='".$item['projectId']."' AND status =0 ORDER BY precedent_activity ASC";
                    $command = $connection->createCommand($sql1);
                    $dataReader = $command->query();
                    //$relation = $dataReader->read();
                    $relations = $dataReader->readAll();
                    // var_dump($item);
                    if($relations){
                        $relationParam1 = '';
                        foreach($relations as $relation){
                            if($relation['relation_type'] == 1)
                                $relationType = 'SS';
                            elseif($relation['relation_type'] == 2)
                                $relationType = 'FS';
                            elseif($relation['relation_type'] == 3)
                                $relationType = 'FF';
                            else
                                $rel =  $relationType = '';
                            $relationParam1.= $relation['precedent_activity'].'ABC'.$relation['precedent_activity'].$relationType.',';
                        }
                        $relationParam = rtrim($relationParam1, ',');
                    }
                    else{
                        $relationParam = '';
                    }

                    $spr1 = $connection->createCommand(
                        "SELECT spr.cumulated_qty, spr.start_date AS act_start,
                                COALESCE(MAX(sprl.report_date), spr.updated_at) AS last_report
                         FROM schedule_progress_report spr
                         LEFT JOIN schedule_progress_report_log sprl
                                ON sprl.activity_id = spr.activity_id AND sprl.currentqty > 0
                         WHERE spr.activity_id = " . (int)$item['id'] . "
                         GROUP BY spr.id"
                    )->queryOne();
                    if ($spr1 && (float)$spr1['cumulated_qty'] > 0
                        && !empty($spr1['act_start']) && $spr1['act_start'] !== '0000-00-00'
                        && !empty($spr1['last_report'])) {
                        $completedQuantity = (float)$spr1['cumulated_qty'];
                        $actualStart       = $spr1['act_start'];
                        $endDate           = $spr1['last_report'];
                    } else {
                        $completedQuantity = 0;
                        $actualStart       = $item['start_date'];
                        $endDate           = $item['end_date'];
                    }

                    $datediff = strtotime($item['actual_end_date']) - strtotime($item['actual_start_date']);
                    $duration_first = round($datediff / (60 * 60 * 24)) + 1;

                    $datediff1 = strtotime($item['end_date']) - strtotime($item['actual_start_date']);
                    $fullduration = round($datediff1 / (60 * 60 * 24)) + 1;
                    if($duration_first < $item['duration']){
                        $delay = $item['duration'] - $duration_first; 
                        if($item['start_date']>$item['actual_start_date'] && count($relations)==0){
                            $delaystart = ($delay / $fullduration) * 100;
                            //$delaystart = ($delay / $item['duration']) * 100;
                        }
                        else{
                            $delaystart = ($delay / $item['duration']) * 100;
                        }
                    }
                    else{
                        $delaystart = 0;
                    }

                    if($item['actual_start_date']!='' && $item['actual_start_date']!='0000-00-00' && $item['actual_end_date']!='' && $item['actual_end_date']!='0000-00-00'){
                            $datediff = strtotime($item['actual_end_date']) - strtotime($item['actual_start_date']);
                            $bduration1 = round($datediff / (60 * 60 * 24)) + 1;
                            $bduration = $bduration1 .'/'.$item['duration'] .' Days';
                    }
                    else{
                        $bduration = '';
                    }

                    //$connection = CActiveRecord::getDbConnection();
                    $reportlog = "SELECT SUM(currentqty) AS totalqty FROM schedule_progress_report_log WHERE activity_id='".$item['id']."'";
                    $command = $connection->createCommand($reportlog);
                    $dataReader = $command->query();
                    $totalQty = $dataReader->read();

                    if($totalQty['totalqty']>0){
                        $reprtdqty = $totalQty['totalqty'];
                    }
                    else{
                        $reprtdqty = 0;
                    }

                    $Qty = $item['quantity'];

                    $depRelations = ActivityRelations::find()->Where(['dependent_activity' => $item['id']])->andWhere(['projectId' => $item['projectId']])->andWhere(['status'=>0])->all();

                    $scheduleID = '';

                    $scheduleStartdate = '';
                    $scheduleEnddate = '';
                    foreach($depRelations as $depRelation):
                        $thisAct = Scheduleactivities::findOne($depRelation->precedent_activity);
                        $thisdepAct = Scheduleactivities::findOne($depRelation->dependent_activity);
                        if (!$thisAct || !$thisdepAct) continue;
                        if($depRelation->relation_type == 1){
                            $deplag = $thisdepAct->lag;
                            $depduration = $thisdepAct->duration + $deplag - 1;
                            $depstart = date('Y-m-d', strtotime($thisAct->start_date. ' + '.$deplag.' days'));
                            $depend = date('Y-m-d', strtotime($thisAct->start_date. ' + '.$depduration.' days'));
                        }
                        elseif($depRelation->relation_type == 2)
                        {
                            $deplag = $thisdepAct->lag + 1;
                            $depstart = date('Y-m-d', strtotime($thisAct->end_date. ' + '.$deplag.' days'));
                            $depduration = $thisdepAct->duration - 1;
                            $depend = date('Y-m-d', strtotime($depstart. ' + '.$depduration.' days'));
                        }
                        elseif($depRelation->relation_type == 3)
                        {
                            $deplag = $thisdepAct->lag;
                            $duration = $thisdepAct->duration - 1;
                            $depend = date('Y-m-d', strtotime($thisAct->end_date. ' + '.$deplag.' days'));
                            $depstart = date('Y-m-d', strtotime($depend. ' - '.$duration.' days'));
                        }
                        if($scheduleEnddate=='' || $depend > $scheduleEnddate){
                            //if($depRelation->relation_type==2){
                                $scheduleID = $thisAct->id;
                                $scheduleStartdate = $depstart;
                                $scheduleEnddate = $depend;
                            //}
                        }
                    endforeach;

                    if(count($depRelations)>0){
                        $budgtstart1 = $scheduleStartdate;
                    }
                    else{
                        $budgtstart1 = $item['actual_start_date'];
                    }

                    if($relationParam==''){
                        $budgtstart = $item['actual_start_date'];
                    }
                    else{
                        $budgtstart = $budgtstart1;
                    }

                    if(isset($bduration1)){

                        $oldduration = $bduration1;

                    }
                    else{
                        $oldduration = 0;
                    }

                    if($relations){

                        $array[]=array('id'=> $item['id'], 'name' => $item['name'], 'precedent_activity' => $relation['precedent_activity'], 'dependent_activity' => $relation['dependent_activity'], 'scheduleitem_id' => $item['scheduleitem_id'], 'status' => $item['status'], 'projectId' => $item['projectId'], 'quantity' => $Qty, 'duration' => $item['duration'], 'start_date' => $item['start_date'], 'end_date'=> $item['end_date'], 'actual_start_date'=> $actualStart, 'actual_end_date'=> $endDate, 'activity_start'=> $budgtstart, 'activity_end'=> $item['actual_end_date'],'completedQuantity'=>$completedQuantity,'delaystart' => $delaystart, 'relation' => $relationParam,'float'=> $item['float_duration'],'delay'=>$item['delay'],'bduration'=>$bduration,'depstart'=>$scheduleStartdate,'reprtdqty'=>$reprtdqty,'olddur'=>$oldduration);

                    }
                    else{
                      $array[]=array('id'=> $item['id'], 'name' => $item['name'], 'precedent_activity' => null, 'dependent_activity' => null, 'scheduleitem_id' => $item['scheduleitem_id'], 'status' => $item['status'], 'projectId' => $item['projectId'], 'quantity' => $Qty, 'duration' => $item['duration'], 'start_date' => $item['start_date'], 'end_date'=> $item['end_date'], 'actual_start_date'=> $actualStart, 'actual_end_date'=> $endDate, 'activity_start'=> $budgtstart, 'activity_end'=> $item['actual_end_date'],'completedQuantity'=>$completedQuantity,'delaystart' => $delaystart, 'relation' => $relationParam,'float'=> $item['float_duration'],'delay'=>$item['delay'],'bduration'=>$bduration,'depstart'=>$scheduleStartdate,'reprtdqty'=>$reprtdqty,'olddur'=>$oldduration);  
                    }

                //}

            }
            
        }

        $arr = array('result' => $array, 'error' => 'No');
        return json_encode($arr);
    }
    public function actionEstpurchaseorder()
    {
        $uid = Yii::$app->user->Id; 
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();

        $rescount=0;

        if($projuser)
        {

            $project = Projects::findOne($projuser->projectid); 

            $connection = \Yii::$app->db;
            $sqltemvals1 = "SELECT * FROM workgroups_new WHERE Project_Id=' ".$projuser->projectid." ' AND Status=0 ORDER BY sortorder ASC, Workgroup_Id ASC, Name ASC";
            $command = $connection->createCommand($sqltemvals1);
            $dataReader1 = $command->query();
            $structures = $dataReader1->readAll();

            if($structures){
                $sno=0;
                foreach($structures AS $structure):

                    $sql = "SELECT * FROM workgroup_activities_new WHERE project_Id='".$structure['Project_Id']."' AND wbs_id='".$structure['Workgroup_Id']."' AND estimate=1 AND pricing_status=0 ORDER BY sortorder ASC ";
                    $command = $connection->createCommand($sql);
                    $dataReader = $command->query();
                    $wbsactivities = $dataReader->readAll();

                    if(!empty($wbsactivities)){

                        foreach($wbsactivities as $key=> $wbsactivity):
                            $sno++;
                            if($wbsactivity['activitytype_id']==0):
                                $processtype=$wbsactivity['process_Id'];
                            else:
                                $processtype=$wbsactivity['activitytype_id'];
                            endif;

                            $estimateresources=PricingEstimateResourcesNew::find()->where(['activity_id'=>$wbsactivity['id']])->andWhere(['est_activity_Id'=>$wbsactivity['activity_Id']])->andWhere(['project_id'=>$wbsactivity['project_Id']])->andWhere(['process_Id'=>$processtype])->andWhere(['pricing_status'=>0])->andWhere(['powerstatus'=>0])->andWhere(['diesel_orderd_status'=>0])->all();
                            $sfno=0;
                            if(!empty($estimateresources))
                            {

                                foreach($estimateresources AS $key1 =>$estimateresource):
                                    $sfno++;

                                    if($estimateresource->resourcetype_Id == 19)
                                    {
                                        $resource=Resources::findOne($estimateresource->resource_Id);

                                        $Jobcardd=Jobcard::find()->where(['vendor_id'=>$resource->Vendor_Id])->andWhere(['resource'=>$resource->Resource_Id])->andWhere(['delete_status'=>0])->andWhere(['project_id'=>$estimateresource->project_id])->andWhere(['app_activity'=>$estimateresource->activity_id])->orderBy(['job_id'=>SORT_DESC])->one(); // activity wise
                                    
                                        $cart = cart::find()->where(['Vendor_Id'=>$resource->Vendor_Id])->andWhere(['Resource_Id'=>$resource->Resource_Id])->andWhere(['status'=>0])->andWhere(['Project'=>$estimateresource->project_id])->andWhere(['rate'=>$estimateresource->rate])->one();


                                        $editodrdata = "SELECT * FROM `cart` as c WHERE c.status=1 AND c.Vendor_Id='".$resource->Vendor_Id."' AND c.Resource_Id ='".$resource->Resource_Id."' AND c.Project='".$estimateresource->project_id."' AND c.rate ='".$estimateresource->rate."' AND c.pricing_resourceid = '".$estimateresource->pricing_resourceid."' ";

                                        $command=$connection->createCommand($editodrdata);
                                        $dataReader=$command->query();
                                        $cartedit=$dataReader->read();
                              

                                        $odrdata = "SELECT c.status as status, c.Qnty as Qnty FROM `cart` as c INNER JOIN jobcard as j on j.job_id=c.Job_Id WHERE j.vendor_id='".$resource->Vendor_Id."' AND c.status = 0 AND j.resource ='".$resource->Resource_Id."' AND j.delete_status= 0 AND j.project_id='".$estimateresource->project_id."' AND j.app_activity ='".$estimateresource->activity_id."' ";



                                        $command=$connection->createCommand($odrdata);
                                        $dataReader=$command->query();
                                        $pdata=$dataReader->read();

                                        //calcul quanty dif aftr place odr
                                        if(empty($pdata))
                                        {
  

                                            $odreditdata = "SELECT * FROM `cart` as c INNER JOIN jobcard as j on j.job_id=c.Job_Id WHERE j.vendor_id='".$resource->Vendor_Id."' AND j.resource ='".$resource->Resource_Id."' AND j.delete_status= 0 AND j.project_id='".$estimateresource->project_id."' AND j.app_activity ='".$estimateresource->activity_id."'  AND c.pricing_resourceid = '".$estimateresource->pricing_resourceid."' AND c.status = 1";

                                            $command=$connection->createCommand($odreditdata);
                                            $dataReader=$command->query();
                                            $peditdata=$dataReader->read();

                                            if($peditdata){
                                            //already orderd res, again estimated, calculate- qntyt difference    
                                            //echo "no-cart-if-edit"; 
                                              $estimateress=PricingEstimateResourcesNew::find()->where(['resource_Id'=>$estimateresource->resource_Id])->andWhere(['project_id'=>$estimateresource->project_id])->andWhere(['activity_id'=>$estimateresource->activity_id])->andWhere(['pricing_status'=>0])->all();


                                               $totqty= 0;
                                               $no_mens= 0;
                                               $no_dys= 0;
                                               

                                                foreach($estimateress as $estimateres)
                                                {

                                                   $estimateact=PricingEstimateNew::find()->where(['project_Id'=>$projuser->projectid])->andWhere(['activity_Id'=>$estimateres->activity_id])->andWhere(['pricing_status' => 0])->one();

                                                   if($estimateact['activity_qty']!=0){

                                                   $wrkact_qty= $estimateact->activity_qty;

                                                        }
                                                        else{
                                                   $wrkact_qty= 1;
                                                        }

                                                   $qty= ($estimateres->quantity) * ($wrkact_qty);

                                                   $totqty=  $qty;

                                                   $resourcee_rate= $estimateres->rate;

                                                   $no_mens= $no_mens + $estimateres->no_of_men;

                                                   $no_dys= $no_dys + $estimateres->no_of_days;


                                                }

                                                $cartqtys = cart::find()->where(['Vendor_Id'=>$resource->Vendor_Id])->andWhere(['Resource_Id'=>$resource->Resource_Id])->andWhere(['status'=>1])->andWhere(['Project'=>$estimateresource->project_id])->all();

                                               
                                               if($cartqtys)
                                               {

                                                    $odrqty=0;
                                                    foreach($cartqtys as $cartqty)
                                                    {
                                                        $odrqty = $odrqty + $cartqty->Qnty;

                                                    }

                                                    $newqunty = $totqty - $odrqty; //Q1-Q2
                                                }else
                                                {
                                                    $newqunty = $totqty;
                                                }
                                            //echo $newqunty;  exit;                                         
                                                if($newqunty>0)
                                                {

                                                    $sql="SELECT name FROM jobcard ORDER BY name DESC limit 1";
                                                    $command=$connection->createCommand($sql);
                                                    $dataReader=$command->query();
                                                    $no=$dataReader->read();
                                                    if($no)
                                                    {
                                                    $name=$no['name'] + 1;
                                                    }
                                                    else
                                                    {
                                                        $name= 1;
                                                    }
                                                    
                                                    $groupid=time();

                                                    $processid = $estimateresource->process_Id;

                                                    $activity = $wbsactivity['activity_Name'];

                                                    $project_id = $projuser->projectid;

                                                    $resource_id = $estimateresource->resource_Id;

                                                    $res = Resources::find()->where(['Resource_Id'=>$resource_id])->one();

                                                    $resource_unit = $res->Unit;

                                                    $estimateact=PricingEstimateNew::find()->where(['project_Id'=>$projuser->projectid])->andWhere(['activity_Id'=>$wbsactivity['id']])->andWhere(['pricing_status' => 0])->one();

                                                    if($estimateact->activity_qty!=0){

                                                        $wrkact_qty = $estimateact->activity_qty;

                                                    }
                                                    else{
                                                        $wrkact_qty = 1;
                                                    }
                                                    

                                                    $resource_qty = $newqunty;

                                                    $resource_rate = $estimateresource->rate;

                                                    $activity_id = $wbsactivity['id'];

                                                    $iow_id = $wbsactivity['wbs_id'];

                                                    $vendor_id = $res->Vendor_Id;
                                                    
                                                    
                                                    $Jobcard = New Jobcard();
                                                    $Jobcard->name= $name;
                                                    $Jobcard->date= date('Y-m-d');
                                                    $Jobcard->process= $processid;
                                                    $Jobcard->activity= $activity;
                                                    $Jobcard->groupid= $groupid;
                                                    $Jobcard->project_id= $project_id;
                                                    $Jobcard->resource= $resource_id;
                                                    
                                                    $Jobcard->res_quantity= $resource_qty;
                                                    $Jobcard->res_rate= $resource_rate;
                                                    $Jobcard->est_resqty= $resource_qty;
                                                    $Jobcard->vendor_id= $vendor_id;
                                                    $Jobcard->user_id= $uid;
                                                    $Jobcard->app_activity= $activity_id;
                                                    $Jobcard->iow= $iow_id;
                                                    $Jobcard->status= 1;
                                                    
                                                    $Jobcard->pricing_resourceid= $estimateresource->pricing_resourceid;
                                                
                                                    $Jobcard->unit= $resource_unit;
                                                
                                                    if($Jobcard->save(false)):

                                                        $nwgroupid = time();
                                                        $Jobcard->groupid= $Jobcard->job_id.$Jobcard->job_id;
                                                        $Jobcard->save(false);

                                                        $model=New Cart();

                                                        $model->Job_Id= $Jobcard->job_id;
                                                        $model->Vendor_Id= $Jobcard->vendor_id;
                                                        $model->ResourceType_Id= $res->ResourceType_Id;
                                                        $model->Resource_Id= $Jobcard->resource;
                                                        
                                                        $model->rate= $resource_rate;
                                                        $model->Qnty= $resource_qty;
                                                        $model->EstresQnty= $resource_qty;
                                                        $amount = $resource_rate * $resource_qty;
                                                        $model->amount= $amount;
                                                        $model->Project= $Jobcard->project_id;
                                                        $model->groupid= $Jobcard->groupid;
                                                        if($estimateresource->no_of_men){
                                                            $model->no_workers=$estimateresource->no_of_men;
                                                        }
                                                        if($estimateresource->no_of_days){
                                                            $model->no_days=$estimateresource->no_of_days;
                                                        }

                                                        $model->fuel_status = 0;
                                                        $model->unit= $Jobcard->unit;

                                                        $model->pricing_resourceid= $estimateresource->pricing_resourceid;
                                                        

                                                        //$model->alrdy_odr_sts = 1;                    
                                                        $model->save(false);

                                                        $Jobcard->cart_status=1;
                                                        $Jobcard->save(false);

                                                    endif;

                                                    $rescount=1;


                                                }


                                            }  

                                            else  //very first time gen. new data, nothing in cart
                                            {
                                                
                                                    //echo "no-cart-if-new";                                                    
                                                    $sql="SELECT name FROM jobcard ORDER BY name DESC limit 1";
                                                    $command=$connection->createCommand($sql);
                                                    $dataReader=$command->query();
                                                    $no=$dataReader->read();
                                                    if($no)
                                                    {
                                                       $name=$no['name'] + 1;
                                                    }
                                                    else
                                                    {
                                                        $name= 1;
                                                    }
                                                    
                                                    $groupid=time();

                                                    $processid = $estimateresource->process_Id;

                                                    $activity = $wbsactivity['activity_Name'];

                                                    $project_id = $projuser->projectid;

                                                    $resource_id = $estimateresource->resource_Id;

                                                    $res = Resources::find()->where(['Resource_Id'=>$resource_id])->one();

                                                    $resource_unit = $res->Unit;

                                                     $estimateact=PricingEstimateNew::find()->where(['project_Id'=>$projuser->projectid])->andWhere(['activity_Id'=>$wbsactivity['id']])->andWhere(['pricing_status' => 0])->one();

                                                    if($estimateact->activity_qty!=0){

                                                        $wrkact_qty = $estimateact->activity_qty;

                                                    }
                                                    else{
                                                        $wrkact_qty = 1;
                                                    }
                                                    

                                                    $resource_qty = $estimateresource->quantity * $wrkact_qty;

                                                    $resource_rate = $estimateresource->rate;

                                                    $activity_id = $wbsactivity['id'];

                                                    $iow_id = $wbsactivity['wbs_id'];

                                                    $vendor_id = $res->Vendor_Id;

                                                    
                                                    $Jobcard = New Jobcard();
                                                    $Jobcard->name= $name;
                                                    $Jobcard->date= date('Y-m-d');
                                                    $Jobcard->process= $processid;
                                                    $Jobcard->activity= $activity;
                                                    $Jobcard->groupid= $groupid;
                                                    $Jobcard->project_id= $project_id;
                                                    $Jobcard->resource= $resource_id;
                                                    
                                                    $Jobcard->res_quantity= $resource_qty;
                                                    $Jobcard->res_rate= $resource_rate;
                                                    $Jobcard->est_resqty= $resource_qty;
                                                    $Jobcard->vendor_id= $vendor_id;
                                                    $Jobcard->user_id= $uid;
                                                    $Jobcard->app_activity= $activity_id;
                                                    $Jobcard->iow= $iow_id;
                                                    $Jobcard->status= 1;
                                                    
                                                    $Jobcard->pricing_resourceid= $estimateresource->pricing_resourceid;
                                                   
                                                    $Jobcard->unit= $resource_unit;
                                                 
                                                    if($Jobcard->save(false)):

                                                        $nwgroupid = time();
                                                        $Jobcard->groupid= $Jobcard->job_id.$Jobcard->job_id;
                                                        $Jobcard->save(false);

                                                        $model=New Cart();

                                                        $model->Job_Id= $Jobcard->job_id;
                                                        $model->Vendor_Id= $Jobcard->vendor_id;
                                                        $model->ResourceType_Id= $res->ResourceType_Id;
                                                        $model->Resource_Id= $Jobcard->resource;
                                                        
                                                        $model->rate= $resource_rate;
                                                        $model->Qnty= $resource_qty;
                                                        $model->EstresQnty= $resource_qty;
                                                        $amount = $resource_rate * $resource_qty;
                                                        $model->amount= $amount;
                                                        $model->Project= $Jobcard->project_id;
                                                        $model->groupid= $Jobcard->groupid;
                                                        if($estimateresource->no_of_men){
                                                            $model->no_workers=$estimateresource->no_of_men;
                                                        }
                                                        if($estimateresource->no_of_days){
                                                            $model->no_days=$estimateresource->no_of_days;
                                                        }

                                                        $model->fuel_status = 0;
                                                        $model->unit= $Jobcard->unit;

                                                        $model->pricing_resourceid= $estimateresource->pricing_resourceid;                         
                                                        $model->save(false);

                                                        $Jobcard->cart_status=1;
                                                        $Jobcard->save(false);

                                                    endif;

                                                    $rescount=1;
                                                
                                            }
                                           

                                        }
                                        elseif(!empty($pdata))  //2nd case, aftr estimate..
                                        { 
          
              
                                            $orderstatus = $pdata['status']; 
                                            
                                            $cartdata = cart::find()->where(['Vendor_Id'=>$resource->Vendor_Id])->andWhere(['Resource_Id'=>$resource->Resource_Id])->andWhere(['status'=>0])->andWhere(['Project'=>$estimateresource->project_id])->one();

                                            if($orderstatus==1 && empty($cartdata))
                                            {
                                                // echo "*ys cart-if-sts-1empcart-*"; 
                                                $Qnty = $pdata['Qnty']; 
                                                $editestimateqnty = $estimateresource->quantity;
                                                $newquntity = 0;
                                                if($Qnty<$editestimateqnty)
                                                {
                                                     $newquntity = $editestimateqnty - $Qnty;
                                                }
                                                if($newquntity>0)
                                                {


 
                                                    $sql="SELECT name FROM jobcard ORDER BY name DESC limit 1";
                                                    $command=$connection->createCommand($sql);
                                                    $dataReader=$command->query();
                                                    $no=$dataReader->read();
                                                    if($no)
                                                    {
                                                       $name=$no['name'] + 1;
                                                    }
                                                    else
                                                    {
                                                        $name= 1;
                                                    }
                                                    
                                                    $groupid=time();

                                                    $processid = $estimateresource->process_Id;

                                                    $activity = $wbsactivity['activity_Name'];

                                                    $project_id = $projuser->projectid;

                                                    $resource_id = $estimateresource->resource_Id;

                                                    $res = Resources::find()->where(['Resource_Id'=>$resource_id])->one();

                                                    $resource_unit = $res->Unit;

                                                     $estimateact=PricingEstimateNew::find()->where(['project_Id'=>$projuser->projectid])->andWhere(['activity_Id'=>$wbsactivity['id']])->andWhere(['pricing_status' => 0])->one();

                                                    if($estimateact->activity_qty!=0){

                                                        $wrkact_qty = $estimateact->activity_qty;

                                                    }
                                                    else{
                                                        $wrkact_qty = 1;
                                                    }
                                                    

                                                    $resource_qty = $newquntity * $wrkact_qty;

                                                    $resource_rate = $estimateresource->rate;

                                                    $activity_id = $wbsactivity['id'];

                                                    $iow_id = $wbsactivity['wbs_id'];

                                                    $vendor_id = $res->Vendor_Id;

                                                    
                                                    $Jobcard = New Jobcard();
                                                    $Jobcard->name= $name;
                                                    $Jobcard->date= date('Y-m-d');
                                                    $Jobcard->process= $processid;
                                                    $Jobcard->activity= $activity;
                                                    $Jobcard->groupid= $groupid;
                                                    $Jobcard->project_id= $project_id;
                                                    $Jobcard->resource= $resource_id;
                                                    
                                                    $Jobcard->res_quantity= $resource_qty;
                                                    $Jobcard->res_rate= $resource_rate;
                                                    $Jobcard->est_resqty= $resource_qty;
                                                    $Jobcard->vendor_id= $vendor_id;
                                                    $Jobcard->user_id= $uid;
                                                    $Jobcard->app_activity= $activity_id;
                                                    $Jobcard->iow= $iow_id;
                                                    $Jobcard->status= 1;
                                                    
                                                    $Jobcard->pricing_resourceid= $estimateresource->pricing_resourceid;
                                                   
                                                    $Jobcard->unit= $resource_unit;
                                                    if($Jobcard->save(false)):

                                                        $nwgroupid = time();
                                                        $Jobcard->groupid= $Jobcard->job_id.$Jobcard->job_id;
                                                        $Jobcard->save(false);

                                                        $model=New Cart();

                                                        $model->Job_Id= $Jobcard->job_id;
                                                        $model->Vendor_Id= $Jobcard->vendor_id;
                                                        $model->ResourceType_Id= $res->ResourceType_Id;
                                                        $model->Resource_Id= $Jobcard->resource;
                                                        
                                                        $model->rate= $resource_rate;
                                                        $model->Qnty= $resource_qty;
                                                        $model->EstresQnty= $resource_qty;
                                                        $amount = $resource_rate * $resource_qty;
                                                        $model->amount= $amount;
                                                        $model->Project= $Jobcard->project_id;
                                                        $model->groupid= $Jobcard->groupid;
                                                        if($estimateresource->no_of_men){
                                                            $model->no_workers=$estimateresource->no_of_men;
                                                        }
                                                        if($estimateresource->no_of_days){
                                                            $model->no_days=$estimateresource->no_of_days;
                                                        }

                                                        $model->fuel_status = 0;
                                                        $model->unit= $Jobcard->unit;

                                                        $model->pricing_resourceid= $estimateresource->pricing_resourceid;                         
                                                        $model->save(false);

                                                        $Jobcard->cart_status=1;
                                                        $Jobcard->save(false);

                                                    endif;

                                                    $rescount=1;

                                                }


                                            }

                                            elseif($orderstatus==0 && !empty($cartdata))
                                            {
                                            //clubbing of quantity before place order 
                                                    // echo "*ys cart-els-sts-0-cart-*";                                           
                                               $catsne= cart::find()->where(['Vendor_Id'=>$resource->Vendor_Id])->andWhere(['Resource_Id'=>$resource->Resource_Id])->andWhere(['status'=>0])->andWhere(['Project'=>$estimateresource->project_id])->andWhere(['rate'=>$estimateresource->rate])->andWhere(['Job_Id'=>$Jobcardd->job_id])->one();
                                               $estimateress=PricingEstimateResourcesNew::find()->where(['resource_Id'=>$estimateresource->resource_Id])->andWhere(['project_id'=>$estimateresource->project_id])->andWhere(['activity_id'=>$estimateresource->activity_id])->andWhere(['pricing_status'=>0])->all();

                                               $totqty= 0;
                                               $no_mens= 0;
                                               $no_dys= 0;


                                                foreach($estimateress as $estimateres)
                                                {

                                                   $estimateact=PricingEstimateNew::find()->where(['project_Id'=>$projuser->projectid])->andWhere(['activity_Id'=>$estimateres->activity_id])->andWhere(['pricing_status' => 0])->one();

                                                   if($estimateact['activity_qty']!=0){

                                                   $wrkact_qty= $estimateact->activity_qty;

                                                        }
                                                        else{
                                                   $wrkact_qty= 1;
                                                        }

                                                   $qty= $estimateres->quantity * $wrkact_qty;

                                                   $totqty= $totqty + $qty;

                                                   $resourcee_rate= $estimateres->rate;

                                                   $no_mens= $no_mens + $estimateres->no_of_men;

                                                   $no_dys= $no_dys + $estimateres->no_of_days;


                                                }

                                                $cartqtys = cart::find()->where(['Vendor_Id'=>$resource->Vendor_Id])->andWhere(['Resource_Id'=>$resource->Resource_Id])->andWhere(['status'=>1])->andWhere(['Project'=>$estimateresource->project_id])->all();
                                               if($cartqtys){

                                                    $odrqty=0;
                                                    foreach($cartqtys as $cartqty)
                                                    {
                                                        $odrqty = $odrqty + $cartqty->Qnty;

                                                    }

                                                    $newqunty = $totqty - $odrqty;
                                                }else
                                                {
                                                  $newqunty = $totqty;
                                                }

                                                if($newqunty>0)
                                                {

                                                    $Jobcardd->res_quantity= $newqunty;
                                                    $Jobcardd->res_rate= $resourcee_rate;
                                                    $Jobcardd->est_resqty= $newqunty;
                                                    $Jobcardd->status= 1;

                                                    if($Jobcardd->save(false)):
                                                            if($catsne)
                                                                {

                                                                $catsne->rate= $Jobcardd->res_rate;
                                                                $catsne->Qnty= $Jobcardd->res_quantity;
                                                                $catsne->EstresQnty=$Jobcardd->res_quantity;
                                                                $amount= $resourcee_rate * $Jobcardd->res_quantity;
                                                                $catsne->amount= $amount;

                                                                    //if($estimateresource->no_of_men){
                                                                $catsne->no_workers=$no_mens;
                                                                            //}
                                                                            //if($estimateresource->no_of_days){
                                                                $catsne->no_days=$no_dys;
                                                                            //}

                                                                        
                                                                $catsne->fuel_status= 0;
                                                                $catsne->save(false);
                                                                }

                                                                $Jobcardd->cart_status=1;
                                                                $Jobcardd->save(false);


                                                            endif;

                                                        $rescount=1;

                                                }
                                               
                                               //clubbing of quantity-edit  end
                                            }
                                        }

                                    }
                                   
                                    elseif($estimateresource->resourcetype_Id == 24)
                                    {   

                                        $resource=Resources::findOne($estimateresource->resource_Id);

                                        $Jobcardd=Jobcard::find()->where(['vendor_id'=>$resource->Vendor_Id])->andWhere(['resource'=>$resource->Resource_Id])->andWhere(['app_activity'=>$estimateresource->activity_id])->andWhere(['delete_status'=>0])->andWhere(['pricing_resourceid'=>$estimateresource->pricing_resourceid])->one();
                                        //print_r($Jobcardd);exit;

                                        $Jobcarddl=Jobcard::find()->andWhere(['resource'=>184])->andWhere(['delete_status'=>0])->andWhere(['project_id'=>$estimateresource->project_id])->andWhere(['pricing_resourceid'=>$estimateresource->pricing_resourceid])->one(); 
                                        $cartl = cart::find()->where(['Vendor_Id'=>9])->andWhere(['Resource_Id'=>184])->andWhere(['status'=>0])->andWhere(['Project'=>$estimateresource->project_id])->one();
                                       
                                        $cart = cart::find()->where(['Vendor_Id'=>$resource->Vendor_Id])->andWhere(['Resource_Id'=>$resource->Resource_Id])->andWhere(['status'=>0])->andWhere(['Project'=>$estimateresource->project_id])->andWhere(['pricing_resourceid'=>$estimateresource->pricing_resourceid])->andWhere(['rate'=>$estimateresource->rate])->one();

                                      if(empty($Jobcardd)){
                                        
                                       
                                            $sql="SELECT name FROM jobcard ORDER BY name DESC limit 1";
                                            $command=$connection->createCommand($sql);
                                            $dataReader=$command->query();
                                            $no=$dataReader->read();
                                            $name=$no['name'] + 1;
                                            $groupid=time();

                                            $processid = $estimateresource->process_Id;

                                            $activity = $wbsactivity['activity_Name'];

                                            $project_id = $projuser->projectid;

                                            $resource_id = $estimateresource->resource_Id;

                                            $res = Resources::find()->where(['Resource_Id'=>$resource_id])->one();

                                            $resource_unit = $res->Unit;

                                            $estimateact=PricingEstimateNew::find()->where(['project_Id'=>$projuser->projectid])->andWhere(['activity_Id'=>$wbsactivity['id']])->andWhere(['pricing_status' => 0])->one();


                                            if($estimateact->activity_qty!=0){

                                                $wrkact_qty = $estimateact->activity_qty;

                                            }
                                            else{
                                                $wrkact_qty = 1;
                                            }

                                            

                                            $resource_rate = $estimateresource->rate;

                                            $activity_id = $wbsactivity['id'];

                                            $iow_id = $wbsactivity['wbs_id'];

                                            $vendor_id = $res->Vendor_Id;


                                            


                                            $Jobcarddfuel=Jobcard::find()->where(['vendor_id'=>$resource->Vendor_Id])->andWhere(['in','resource',[183,184]])->andWhere(['app_activity'=>$estimateresource->activity_id])->andWhere(['delete_status'=>0])->andWhere(['pricing_resourceid'=>$estimateresource->pricing_resourceid])->andWhere(['project_id'=>$estimateresource->project_id])->one();
                                            //$Jobcarddfuel1=Jobcard::find()->andWhere(['in','resource',[183,184]])->andWhere(['delete_status'=>0])->andWhere(['project_id'=>$estimateresource->project_id])->one(); 
                                            
                                            if(empty($Jobcarddfuel)){
                                                
                                                
                                            $jobfindfuel = Jobcard::find()->where(['resource' => 184])->andWhere(['delete_status'=>0])->andWhere(['project_id'=>$estimateresource->project_id])->one();
                                            
                                            if(!$jobfindfuel)
                                            {
                                                
                                                
                                                if($estimateresource->fuel_type != null){

                                                    $fuel_qty = $estimateresource->fuel_qty * $estimateresource->hours_no;
        
                                                    $resource_qty = $fuel_qty * $wrkact_qty;
                                                    
                                                    $resource_unit = 'Ltr';
                                                    $resource_rate = $estimateresource->fuel_rate;
        
                                                    if($estimateresource->fuel_type == 'Petrol'){
                                                        $resids = Resources::find()->where(['Vendor_Id' => 0])->andWhere(['LIKE','Name','Petrol'])->one();
                                                        $resource_id = '183';
        
                                                    }elseif($estimateresource->fuel_type == 'Diesel'){
                                                        $resids = Resources::find()->where(['Vendor_Id' => 0])->andWhere(['LIKE','Name','Diesel'])->one();
                                                        $resource_id = '184';
                                                    }
                                                }else{
                                                    $resource_id = $estimateresource->resource_Id;
                                                    
                                                    $resource_unit = $res->Unit;
                                                    $resource_qty = $estimateresource->quantity * $wrkact_qty;
                                                    $resource_rate = $estimateresource->rate;
                                                }

                                                $Jobcard = New Jobcard();
                                                $Jobcard->name= $name;
                                                $Jobcard->date= date('Y-m-d');
                                                $Jobcard->process= $processid;
                                                $Jobcard->activity= $activity;
                                                $Jobcard->groupid= $groupid;
                                                $Jobcard->project_id= $project_id;
                                                $Jobcard->resource= $resource_id;
                                                $Jobcard->unit= $resource_unit;
                                                $Jobcard->res_quantity= $resource_qty;
                                                $Jobcard->res_rate= $resource_rate;
                                                $Jobcard->est_resqty= $resource_qty;

                                            
                                                $Jobcard->vendor_id= $vendor_id;
                                                

                                            
                                                $Jobcard->user_id= $uid;
                                                $Jobcard->app_activity= $activity_id;
                                                $Jobcard->iow= $iow_id;
                                                $Jobcard->status= 1;
                                                //$Jobcard->save(false);
                                                $Jobcard->pricing_resourceid= $estimateresource->pricing_resourceid;
                                                if($Jobcard->save(false)):

                                                    $nwgroupid = time();
                                                    $Jobcard->groupid= $Jobcard->job_id.$Jobcard->job_id;
                                                    $Jobcard->save(false);

                                                    $model=New Cart();

                                                    $model->Job_Id= $Jobcard->job_id;
                                                    if($estimateresource->resourcetype_Id == 24 && $estimateresource->lease_period == null){                      
                                                        $model->Vendor_Id= '9';                                               
                                                    }else{
                                                        $model->Vendor_Id= $Jobcard->vendor_id;
                                                    }
                                                    $model->ResourceType_Id= $res->ResourceType_Id;
                                                    $model->Resource_Id= $Jobcard->resource;
                                                    $model->unit= $Jobcard->unit;
                                                    $model->rate= $resource_rate;
                                                    $model->Qnty= $resource_qty;
                                                    $model->EstresQnty= $resource_qty;

                                                    
                                                        $amount = $resource_rate * $resource_qty;
                                                    
                                                    $model->amount= $amount;
                                                    $model->Project= $Jobcard->project_id;
                                                    $model->groupid= $Jobcard->groupid;
                                                    if($estimateresource->no_of_men){
                                                        $model->no_workers=$estimateresource->no_of_men;
                                                    }
                                                    if($estimateresource->no_of_days){
                                                        $model->no_days=$estimateresource->no_of_days;
                                                    }

                                                    if($estimateresource->resourcetype_Id == 24 && $estimateresource->lease_period != null){
                                                        $model->fuel_status = 1;
                                                    }else{
                                                        $model->fuel_status = 0;
                                                    }
                                                    
                                                    $model->pricing_resourceid= $estimateresource->pricing_resourceid;
                                                    $model->save(false);

                                                    $Jobcard->cart_status=1;
                                                    $Jobcard->save(false);

                                                    if($estimateresource->fuel_type != null){

                                                        $diesletabs = Pricingordereddiesel::find()->where(['pricing_res_id'=>$estimateresource->pricing_resourceid])->one();

                                                        if($diesletabs){

                                                        }else{
                                                            $dieselcapturing = new Pricingordereddiesel;
                                                            $dieselcapturing->cartid = $model->cartID;
                                                            $dieselcapturing->pricing_res_id = $estimateresource->pricing_resourceid;

                                                            $dieselcapturing->save(false);
                                                        }

                                                       
                                                    }
    
                                                endif;


                                            }elseif($estimateresource->fuel_type == ''){
                                                
                                               
                                                $resource_id = $estimateresource->resource_Id;
                                                    
                                                $resource_unit = $res->Unit;
                                                $resource_qty = $estimateresource->quantity * $wrkact_qty;
                                                $resource_rate = $estimateresource->rate;

                                            $Jobcard = New Jobcard();
                                            $Jobcard->name= $name;
                                            $Jobcard->date= date('Y-m-d');
                                            $Jobcard->process= $processid;
                                            $Jobcard->activity= $activity;
                                            $Jobcard->groupid= $groupid;
                                            $Jobcard->project_id= $project_id;
                                            $Jobcard->resource= $resource_id;
                                            $Jobcard->unit= $resource_unit;
                                            $Jobcard->res_quantity= $resource_qty;
                                            $Jobcard->res_rate= $resource_rate;
                                            $Jobcard->est_resqty= $resource_qty;

                                           
                                            $Jobcard->vendor_id= $vendor_id;
                                            

                                           
                                            $Jobcard->user_id= $uid;
                                            $Jobcard->app_activity= $activity_id;
                                            $Jobcard->iow= $iow_id;
                                            $Jobcard->status= 1;
                                            //$Jobcard->save(false);
                                            $Jobcard->pricing_resourceid= $estimateresource->pricing_resourceid;
                                            if($Jobcard->save(false)):

                                                $nwgroupid = time();
                                                $Jobcard->groupid= $Jobcard->job_id.$Jobcard->job_id;
                                                $Jobcard->save(false);

                                                $model=New Cart();

                                                $model->Job_Id= $Jobcard->job_id;
                                                /* if($estimateresource->resourcetype_Id == 24 && $estimateresource->lease_period == null){                      
                                                    $model->Vendor_Id= '9';                                               
                                                }else{ */
                                                    $model->Vendor_Id= $Jobcard->vendor_id;
                                                //}
                                                $model->ResourceType_Id= $res->ResourceType_Id;
                                                $model->Resource_Id= $Jobcard->resource;
                                                $model->unit= $Jobcard->unit;
                                                $model->rate= $resource_rate;
                                                $model->Qnty= $resource_qty;
                                                $model->EstresQnty= $resource_qty;

                                                 
                                                    $amount = $resource_rate * $resource_qty;
                                                
                                                $model->amount= $amount;
                                                $model->Project= $Jobcard->project_id;
                                                $model->groupid= $Jobcard->groupid;
                                                if($estimateresource->no_of_men){
                                                    $model->no_workers=$estimateresource->no_of_men;
                                                }
                                                if($estimateresource->no_of_days){
                                                    $model->no_days=$estimateresource->no_of_days;
                                                }

                                                if($estimateresource->resourcetype_Id == 24 && $estimateresource->lease_period != null){
                                                    $model->fuel_status = 1;
                                                }/* else{
                                                    $model->fuel_status = 0;
                                                } */
                                                
                                                $model->pricing_resourceid= $estimateresource->pricing_resourceid;
                                                $model->save(false);

                                                $Jobcard->cart_status=1;
                                                $Jobcard->save(false);
 
                                            endif;

                                            }else{
                                                $Jobcarddf = Jobcard::find()->where(['resource'=>184])->andWhere(['delete_status'=>0])->andWhere(['project_id'=>$estimateresource->project_id])->one();

                                                $cartf = cart::find()->where(['Resource_Id'=>184])->andWhere(['status'=>0])->andWhere(['Project'=>$estimateresource->project_id])->one();


                                                if($cartf)
                                                {
                                                    $estimateress=PricingEstimateResourcesNew::find()->where(['resource_Id'=>$estimateresource->resource_Id])->andWhere(['project_id'=>$estimateresource->project_id])->andWhere(['pricing_status'=>0])->andWhere(['diesel_orderd_status'=>0])->all();
                                                    $totqty = 0;
                                                    $resourcee_rate = 0;
                                                    $tot_f_qty = 0;
                                                    //$fuelrate = 0;

                                                    $fuelestimateress=PricingEstimateResourcesNew::find()->andWhere(['project_id'=>$estimateresource->project_id])->andWhere(['pricing_status'=>0])->andWhere(['lease_period'=>null])->andWhere(['fuel_type' => 'Diesel'])->andWhere(['IN','resourcetype_Id',[24,26]])->andWhere(['diesel_orderd_status'=>0])->all();

                                                    //print_r($fuelestimateress);exit;
                                                    foreach($fuelestimateress as $estimateres){

                                                        // $estimateres->fuel_type.'<br>';

                                                        $estimateact=PricingEstimateNew::find()->where(['project_Id'=>$projuser->projectid])->andWhere(['activity_Id'=>$estimateres->activity_id])->andWhere(['pricing_status' => 0])->one();

                                                        if($estimateact['activity_qty']!=0){

                                                            $wrkact_qty = $estimateact->activity_qty;

                                                        }
                                                        else{
                                                            $wrkact_qty = 1;
                                                        }

                                                        $qty = $estimateres->quantity * $wrkact_qty;

                                                        $totqty = $totqty + $qty;

                                                        $resourcee_rate = $resourcee_rate + $estimateres->rate;

                                                        // if($estimateres->resourcetype_Id==26 && $estimateres->fuel_type != ''){

                                                            $count=PricingEstimateResourcesNew::find()->where(['resource_Id'=>$estimateresource->resource_Id])->andWhere(['project_id'=>$estimateresource->project_id])->andWhere(['pricing_status'=>0])->andWhere(['resourcetype_Id'=>26])->count();                                                 

                                                            $fuelqty = $estimateres->fuel_qty * $estimateres->hours_no;

                                                            $f_qty = $fuelqty * $wrkact_qty;
                                                            

                                                            $tot_f_qty = $tot_f_qty + $f_qty;

                                                            $fuelrate =  $estimateres->fuel_rate;

                                                        //}

                                                        $diesletabs = Pricingordereddiesel::find()->where(['pricing_res_id'=>$estimateres->pricing_resourceid])->one();

                                                        if($diesletabs){

                                                        }else{
                                                            $dieselcapturing = new Pricingordereddiesel;
                                                            $dieselcapturing->cartid = $cartf->cartID;
                                                            $dieselcapturing->pricing_res_id = $estimateres->pricing_resourceid;

                                                            $dieselcapturing->save(false);
                                                        }

                                                    }

                                                    //$countmachine = count($estimateress);

                                                    //if($countmachine !=0){
                                                        //$resource_rate = $resourcee_rate;
                                                    //}

                                                    $Jobcarddf->res_quantity = $tot_f_qty;
                                                    $Jobcarddf->res_rate = $fuelrate;
                                                    $Jobcarddf->est_resqty = $tot_f_qty;
                                                    $Jobcarddf->status = 1;


                                                    if($Jobcarddf->save(false)):

                                                        $cartf->rate= $Jobcarddf->res_rate;
                                                        $cartf->Qnty= $tot_f_qty;
                                                        $cartf->EstresQnty=$tot_f_qty;
                                                        $amount = $fuelrate * $tot_f_qty;
                                                        $cartf->amount= $amount;
                                                        
                                                        $cartf->save(false);

                                                        $Jobcarddf->cart_status=1;
                                                        $Jobcarddf->save(false);


                                                    endif;

                                                $rescount=1;

                                                }else{

                                                    $activity = $wbsactivity['activity_Name'];

                                                    $project_id = $projuser->projectid;

                                                    $resource_id = $estimateresource->resource_Id;

                                                    $res = Resources::find()->where(['Resource_Id'=>$resource_id])->one();

                                                    $resource_unit = $res->Unit;

                                                    $estimateact=PricingEstimateNew::find()->where(['project_Id'=>$projuser->projectid])->andWhere(['activity_Id'=>$wbsactivity['id']])->andWhere(['pricing_status' => 0])->one();


                                                    if($estimateact->activity_qty!=0){

                                                        $wrkact_qty = $estimateact->activity_qty;

                                                    }
                                                    else{
                                                        $wrkact_qty = 1;
                                                    }

                                                    

                                                    $resource_rate = $estimateresource->rate;

                                                    $activity_id = $wbsactivity['id'];

                                                    $iow_id = $wbsactivity['wbs_id'];

                                                    $vendor_id = $res->Vendor_Id;


                                                    if($estimateresource->fuel_type != null){

                                                        $fuel_qty = $estimateresource->fuel_qty * $estimateresource->hours_no;
            
                                                        $resource_qty = $fuel_qty * $wrkact_qty;
                                                        
                                                        $resource_unit = 'Ltr';
                                                        $resource_rate = $estimateresource->fuel_rate;
            
                                                        if($estimateresource->fuel_type == 'Petrol'){
                                                            $resids = Resources::find()->where(['Vendor_Id' => 0])->andWhere(['LIKE','Name','Petrol'])->one();
                                                            $resource_id = '183';
            
                                                        }elseif($estimateresource->fuel_type == 'Diesel'){
                                                            $resids = Resources::find()->where(['Vendor_Id' => 0])->andWhere(['LIKE','Name','Diesel'])->one();
                                                            $resource_id = '184';
                                                        }
                                                    }

                                                    $Jobcard = New Jobcard();
                                                    $Jobcard->name= $name;
                                                    $Jobcard->date= date('Y-m-d');
                                                    $Jobcard->process= $processid;
                                                    $Jobcard->activity= $activity;
                                                    $Jobcard->groupid= $groupid;
                                                    $Jobcard->project_id= $project_id;
                                                    $Jobcard->resource= $resource_id;
                                                    $Jobcard->unit= $resource_unit;
                                                    $Jobcard->res_quantity= $resource_qty;
                                                    $Jobcard->res_rate= $resource_rate;
                                                    $Jobcard->est_resqty= $resource_qty;

                                                
                                                    $Jobcard->vendor_id= $vendor_id;
                                                    

                                                
                                                    $Jobcard->user_id= $uid;
                                                    $Jobcard->app_activity= $activity_id;
                                                    $Jobcard->iow= $iow_id;
                                                    $Jobcard->status= 1;
                                                    //$Jobcard->save(false);
                                                    $Jobcard->pricing_resourceid= $estimateresource->pricing_resourceid;
                                                    if($Jobcard->save(false)):
                                                        $nwgroupid = time();
                                                        $Jobcard->groupid= $Jobcard->job_id.$Jobcard->job_id;
                                                        $Jobcard->save(false);

                                                        $model=New Cart();

                                                        $model->Job_Id= $Jobcard->job_id;
                                                        if($estimateresource->resourcetype_Id == 24 && $estimateresource->lease_period == null){                      
                                                            $model->Vendor_Id= '9';                                               
                                                        }else{
                                                            $model->Vendor_Id= $Jobcard->vendor_id;
                                                        }
                                                        $model->ResourceType_Id= $res->ResourceType_Id;
                                                        $model->Resource_Id= $Jobcard->resource;
                                                        $model->unit= $Jobcard->unit;
                                                        $model->rate= $resource_rate;
                                                        $model->Qnty= $resource_qty;
                                                        $model->EstresQnty= $resource_qty;

                                                        
                                                            $amount = $resource_rate * $resource_qty;
                                                        
                                                        $model->amount= $amount;
                                                        $model->Project= $Jobcard->project_id;
                                                        $model->groupid= $Jobcard->groupid;
                                                        if($estimateresource->no_of_men){
                                                            $model->no_workers=$estimateresource->no_of_men;
                                                        }
                                                        if($estimateresource->no_of_days){
                                                            $model->no_days=$estimateresource->no_of_days;
                                                        }

                                                        if($estimateresource->resourcetype_Id == 24 && $estimateresource->lease_period != null){
                                                            $model->fuel_status = 1;
                                                        }else{
                                                            $model->fuel_status = 0;
                                                        }
                                                        
                                                        $model->pricing_resourceid= $estimateresource->pricing_resourceid;
                                                        $model->save(false);

                                                        $Jobcard->cart_status=1;
                                                        $Jobcard->save(false);

                                                        if($estimateresource->fuel_type != null){

                                                            $diesletabs = Pricingordereddiesel::find()->where(['pricing_res_id'=>$estimateresource->pricing_resourceid])->one();

                                                            if($diesletabs){

                                                            }else{
                                                                $dieselcapturing = new Pricingordereddiesel;
                                                                $dieselcapturing->cartid = $model->cartID;
                                                                $dieselcapturing->pricing_res_id = $estimateresource->pricing_resourceid;

                                                                $dieselcapturing->save(false);
                                                            }

                                                        
                                                        }
                                                    endif;


                                                    
                                                }

                                                
                                        }

                                            
                                            }else{
                                                
                                              
                                                $Jobcarddf = Jobcard::find()->where(['resource'=>184])->andWhere(['delete_status'=>0])->andWhere(['project_id'=>$estimateresource->project_id])->one();

                                                $cartf = cart::find()->where(['Resource_Id'=>184])->andWhere(['status'=>0])->andWhere(['Project'=>$estimateresource->project_id])->one();

                                                if($cartf)
                                                {

                                                    $estimateress=PricingEstimateResourcesNew::find()->where(['resource_Id'=>$estimateresource->resource_Id])->andWhere(['project_id'=>$estimateresource->project_id])->andWhere(['pricing_status'=>0])->andWhere(['diesel_orderd_status'=>0])->all();
                                                $totqty = 0;
                                                $resourcee_rate = 0;
                                                $tot_f_qty = 0;
                                                //$fuelrate = 0;

                                                $fuelestimateress=PricingEstimateResourcesNew::find()->andWhere(['project_id'=>$estimateresource->project_id])->andWhere(['pricing_status'=>0])->andWhere(['fuel_type' => 'Diesel'])->andWhere(['IN','resourcetype_Id',[24,26]])->andWhere(['diesel_orderd_status'=>0])->all();

                                                foreach($fuelestimateress as $estimateres){

                                                    $estimateact=PricingEstimateNew::find()->where(['project_Id'=>$projuser->projectid])->andWhere(['activity_Id'=>$estimateres->activity_id])->andWhere(['pricing_status' => 0])->one();

                                                    if($estimateact->activity_qty!=0){

                                                        $wrkact_qty = $estimateact->activity_qty;

                                                    }
                                                    else{
                                                        $wrkact_qty = 1;
                                                    }

                                                    $qty = $estimateres->quantity * $wrkact_qty;

                                                    $totqty = $totqty + $qty;

                                                    $resourcee_rate = $resourcee_rate + $estimateres->rate;

                                                    //if($estimateres->resourcetype_Id==24 && $estimateres->fuel_type != ''){

                                                        $count=PricingEstimateResourcesNew::find()->where(['resource_Id'=>$estimateresource->resource_Id])->andWhere(['project_id'=>$estimateresource->project_id])->andWhere(['pricing_status'=>0])->andWhere(['resourcetype_Id'=>24])->count();                                                 

                                                        $fuelqty = $estimateres->fuel_qty * $estimateres->hours_no;

                                                        $f_qty = $fuelqty * $wrkact_qty;
                                                        

                                                        $tot_f_qty = $tot_f_qty + $f_qty;

                                                        $fuelrate =  $estimateres->fuel_rate;

                                                   // }

                                                  //echo $estimateres->pricing_resourceid.'<br>';
                                                  $diesletabs = Pricingordereddiesel::find()->where(['pricing_res_id'=>$estimateres->pricing_resourceid])->one();

                                                  if($diesletabs){

                                                  }else{
                                                    $dieselcapturing = new Pricingordereddiesel;
                                                    $dieselcapturing->cartid = $cartf->cartID;
                                                    $dieselcapturing->pricing_res_id = $estimateres->pricing_resourceid;

                                                    $dieselcapturing->save(false);
                                                  }
                                                   



                                                }//exit;

                                                //$countmachine = count($estimateress);

                                                //if($countmachine !=0){
                                                    $resource_rate = $resourcee_rate;
                                                //}

                                                $Jobcarddf->res_quantity = $tot_f_qty;
                                                $Jobcarddf->res_rate = $fuelrate;
                                                $Jobcarddf->est_resqty = $tot_f_qty;
                                                $Jobcarddf->status = 1;


                                                    if($Jobcarddf->save(false)):

                                                        $cartf->rate= $Jobcarddf->res_rate;
                                                        $cartf->Qnty= $tot_f_qty;
                                                        $cartf->EstresQnty=$tot_f_qty;
                                                        $amount = $fuelrate * $tot_f_qty;
                                                        $cartf->amount= $amount;
                                                        
                                                        $cartf->save(false);

                                                        $Jobcarddf->cart_status=1;
                                                        $Jobcarddf->save(false);

                                                    endif;

                                                $rescount=1;

                                                }else{

                                                    $activity = $wbsactivity['activity_Name'];

                                                    $project_id = $projuser->projectid;

                                                    $resource_id = $estimateresource->resource_Id;

                                                    $res = Resources::find()->where(['Resource_Id'=>$resource_id])->one();

                                                    $resource_unit = $res->Unit;

                                                    $estimateact=PricingEstimateNew::find()->where(['project_Id'=>$projuser->projectid])->andWhere(['activity_Id'=>$wbsactivity['id']])->andWhere(['pricing_status' => 0])->one();


                                                    if($estimateact->activity_qty!=0){

                                                        $wrkact_qty = $estimateact->activity_qty;

                                                    }
                                                    else{
                                                        $wrkact_qty = 1;
                                                    }

                                                    

                                                    $resource_rate = $estimateresource->rate;

                                                    $activity_id = $wbsactivity['id'];

                                                    $iow_id = $wbsactivity['wbs_id'];

                                                    $vendor_id = $res->Vendor_Id;


                                                    if($estimateresource->fuel_type != null){

                                                        $fuel_qty = $estimateresource->fuel_qty * $estimateresource->hours_no;
            
                                                        $resource_qty = $fuel_qty * $wrkact_qty;
                                                        
                                                        $resource_unit = 'Ltr';
                                                        $resource_rate = $estimateresource->fuel_rate;
            
                                                        if($estimateresource->fuel_type == 'Petrol'){
                                                            $resids = Resources::find()->where(['Vendor_Id' => 0])->andWhere(['LIKE','Name','Petrol'])->one();
                                                            $resource_id = '183';
            
                                                        }elseif($estimateresource->fuel_type == 'Diesel'){
                                                            $resids = Resources::find()->where(['Vendor_Id' => 0])->andWhere(['LIKE','Name','Diesel'])->one();
                                                            $resource_id = '184';
                                                        }
                                                    }

                                                    $Jobcard = New Jobcard();
                                                    $Jobcard->name= $name;
                                                    $Jobcard->date= date('Y-m-d');
                                                    $Jobcard->process= $processid;
                                                    $Jobcard->activity= $activity;
                                                    $Jobcard->groupid= $groupid;
                                                    $Jobcard->project_id= $project_id;
                                                    $Jobcard->resource= $resource_id;
                                                    $Jobcard->unit= $resource_unit;
                                                    $Jobcard->res_quantity= $resource_qty;
                                                    $Jobcard->res_rate= $resource_rate;
                                                    $Jobcard->est_resqty= $resource_qty;

                                                
                                                    $Jobcard->vendor_id= $vendor_id;
                                                    

                                                
                                                    $Jobcard->user_id= $uid;
                                                    $Jobcard->app_activity= $activity_id;
                                                    $Jobcard->iow= $iow_id;
                                                    $Jobcard->status= 1;
                                                    //$Jobcard->save(false);
                                                    $Jobcard->pricing_resourceid= $estimateresource->pricing_resourceid;
                                                    if($Jobcard->save(false)):
                                                        $nwgroupid = time();
                                                        $Jobcard->groupid= $Jobcard->job_id.$Jobcard->job_id;
                                                        $Jobcard->save(false);

                                                        $model=New Cart();

                                                        $model->Job_Id= $Jobcard->job_id;
                                                        if($estimateresource->resourcetype_Id == 24 && $estimateresource->lease_period == null){                      
                                                            $model->Vendor_Id= '9';                                               
                                                        }else{
                                                            $model->Vendor_Id= $Jobcard->vendor_id;
                                                        }
                                                        $model->ResourceType_Id= $res->ResourceType_Id;
                                                        $model->Resource_Id= $Jobcard->resource;
                                                        $model->unit= $Jobcard->unit;
                                                        $model->rate= $resource_rate;
                                                        $model->Qnty= $resource_qty;
                                                        $model->EstresQnty= $resource_qty;

                                                        
                                                            $amount = $resource_rate * $resource_qty;
                                                        
                                                        $model->amount= $amount;
                                                        $model->Project= $Jobcard->project_id;
                                                        $model->groupid= $Jobcard->groupid;
                                                        if($estimateresource->no_of_men){
                                                            $model->no_workers=$estimateresource->no_of_men;
                                                        }
                                                        if($estimateresource->no_of_days){
                                                            $model->no_days=$estimateresource->no_of_days;
                                                        }

                                                        if($estimateresource->resourcetype_Id == 24 && $estimateresource->lease_period != null){
                                                            $model->fuel_status = 1;
                                                        }else{
                                                            $model->fuel_status = 0;
                                                        }
                                                        
                                                        $model->pricing_resourceid= $estimateresource->pricing_resourceid;
                                                        $model->save(false);

                                                        $Jobcard->cart_status=1;
                                                        $Jobcard->save(false);

                                                        if($estimateresource->fuel_type != null){

                                                            $diesletabs = Pricingordereddiesel::find()->where(['pricing_res_id'=>$estimateresource->pricing_resourceid])->one();

                                                            if($diesletabs){

                                                            }else{
                                                                $dieselcapturing = new Pricingordereddiesel;
                                                                $dieselcapturing->cartid = $model->cartID;
                                                                $dieselcapturing->pricing_res_id = $estimateresource->pricing_resourceid;

                                                                $dieselcapturing->save(false);
                                                            }

                                                        
                                                        }
                                                    endif;
                                                    
                                                }

                                                

                                            }

                                            $rescount=1;

                                        
                                        }

                                    }elseif($estimateresource->resourcetype_Id == 26)
                                    {
                                        $resource=Resources::findOne($estimateresource->resource_Id);
                                        
                                        $vendor_id = $resource->Vendor_Id;
                                        if($estimateresource->fuel_type != null)
                                            $vendor_id = $estimateresource->fuel_vendor_id;

                                        $Jobcardd=Jobcard::find()->where(['vendor_id'=>$vendor_id])->andWhere(['resource'=>$resource->Resource_Id])->andWhere(['app_activity'=>$estimateresource->activity_id])->andWhere(['delete_status'=>0])->andWhere(['pricing_resourceid'=>$estimateresource->pricing_resourceid])->one();
                                        //print_r($Jobcardd);exit;

                                        if(empty($Jobcardd)){
                                            
                                       
                                            $sql="SELECT name FROM jobcard ORDER BY name DESC limit 1";
                                            $command=$connection->createCommand($sql);
                                            $dataReader=$command->query();
                                            $no=$dataReader->read();
                                            $name=$no['name'] + 1;
                                            $groupid=time();

                                            $processid = $estimateresource->process_Id;
                                            $activity = $wbsactivity['activity_Name'];
                                            $project_id = $projuser->projectid;
                                            $resource_id = $estimateresource->resource_Id;
                                            $res = Resources::find()->where(['Resource_Id'=>$resource_id])->one();
                                            $resource_unit = $res->Unit;
                                            $estimateact=PricingEstimateNew::find()->where(['project_Id'=>$projuser->projectid])->andWhere(['activity_Id'=>$wbsactivity['id']])->andWhere(['pricing_status' => 0])->one();


                                            if($estimateact->activity_qty!=0){
                                                $wrkact_qty = $estimateact->activity_qty;
                                            }
                                            else{
                                                $wrkact_qty = 1;
                                            }

                                            
                                            $resource_rate = $estimateresource->rate;
                                            $activity_id = $wbsactivity['id'];
                                            $iow_id = $wbsactivity['wbs_id'];
                                            $vendor_id = $res->Vendor_Id;
                                            if($estimateresource->fuel_type != null)
                                                $vendor_id = $estimateresource->fuel_vendor_id;


                                            $Jobcarddfuel=Jobcard::find()->where(['vendor_id'=>$vendor_id])->andWhere(['in','resource',[183,184]])->andWhere(['app_activity'=>$estimateresource->activity_id])->andWhere(['delete_status'=>0])->andWhere(['pricing_resourceid'=>$estimateresource->pricing_resourceid])->one();
                                            
                                            if(empty($Jobcarddfuel)){
                                                
                                                $jobfindfuel = '';
                                                if($estimateresource->fuel_type != null)
                                                {
                                                    if($estimateresource->fuel_type == 'Petrol')
                                                        $resource_id = '183';
                                                    elseif($estimateresource->fuel_type == 'Diesel')
                                                        $resource_id = '184';
                                                    
                                                    $jobfindfuel = Jobcard::find()->where(['resource' => $resource_id])->andWhere(['vendor_id'=>$vendor_id])->andWhere(['delete_status'=>0])->andWhere(['project_id'=>$estimateresource->project_id])->one();
                                                }

                                                if($estimateresource->fuel_type != null && !$jobfindfuel)
                                                {
                                                    $fuel_qty = $estimateresource->fuel_qty * $estimateresource->hours_no;
                                                    $resource_qty = $fuel_qty * $wrkact_qty;
                                                    $resource_unit = 'Ltr';
                                                    $resource_rate = $estimateresource->fuel_rate;

                                                    $Jobcard = New Jobcard();
                                                    $Jobcard->name= $name;
                                                    $Jobcard->date= date('Y-m-d');
                                                    $Jobcard->process= $processid;
                                                    $Jobcard->activity= $activity;
                                                    $Jobcard->groupid= $groupid;
                                                    $Jobcard->project_id= $project_id;
                                                    $Jobcard->resource= $resource_id;
                                                    $Jobcard->unit= $resource_unit;
                                                    $Jobcard->res_quantity= $resource_qty;
                                                    $Jobcard->res_rate= $resource_rate;
                                                    $Jobcard->est_resqty= $resource_qty;
                                                
                                                    $Jobcard->vendor_id= $estimateresource->fuel_vendor_id;
                                                
                                                    $Jobcard->user_id= $uid;
                                                    $Jobcard->app_activity= $activity_id;
                                                    $Jobcard->iow= $iow_id;
                                                    $Jobcard->status= 1;
                                                    //$Jobcard->save(false);
                                                    $Jobcard->pricing_resourceid= $estimateresource->pricing_resourceid;
                                                    if($Jobcard->save(false)):

                                                        $nwgroupid = time();
                                                        $Jobcard->groupid= $Jobcard->job_id.$Jobcard->job_id;
                                                        $Jobcard->save(false);

                                                        $model=New Cart();

                                                        $model->Job_Id= $Jobcard->job_id;
                                                        $model->Vendor_Id= $estimateresource->fuel_vendor_id;   

                                                        $amount = $resource_rate * $resource_qty;
                                                        
                                                        $model->ResourceType_Id= $res->ResourceType_Id;
                                                        $model->Resource_Id= $Jobcard->resource;
                                                        $model->unit= $Jobcard->unit;
                                                        $model->rate= $resource_rate;
                                                        $model->Qnty= $resource_qty;
                                                        $model->EstresQnty= $resource_qty;                                                        
                                                        $model->amount= $amount;
                                                        $model->Project= $Jobcard->project_id;
                                                        $model->groupid= $Jobcard->groupid;
                                                        if($estimateresource->no_of_men){
                                                            $model->no_workers=$estimateresource->no_of_men;
                                                        }
                                                        if($estimateresource->no_of_days){
                                                            $model->no_days=$estimateresource->no_of_days;
                                                        }

                                                        $model->fuel_status = 1;
                                                        $model->pricing_resourceid= $estimateresource->pricing_resourceid;
                                                        $model->save(false);

                                                        $Jobcard->cart_status=1;
                                                        $Jobcard->save(false);

                                                        if($estimateresource->fuel_type != null){

                                                            $diesletabs = Pricingordereddiesel::find()->where(['pricing_res_id'=>$estimateresource->pricing_resourceid])->one();

                                                            if($diesletabs){

                                                            }else{
                                                                $dieselcapturing = new Pricingordereddiesel;
                                                                $dieselcapturing->cartid = $model->cartID;
                                                                $dieselcapturing->pricing_res_id = $estimateresource->pricing_resourceid;

                                                                $dieselcapturing->save(false);
                                                            }
                                                        }


                                                    endif;

                                                }

                                                elseif($estimateresource->fuel_type == ''){

                                                    $resource_id = $estimateresource->resource_Id;
                                                    
                                                    $resource_unit = $res->Unit;
                                                    $resource_qty = $estimateresource->quantity * $wrkact_qty;
                                                    $resource_rate = $estimateresource->rate;
                                                    

                                                    $resource_id = $estimateresource->resource_Id;
                                                        
                                                    $resource_unit = $res->Unit;
                                                    $resource_qty = $estimateresource->quantity * $wrkact_qty;
                                                    $resource_rate = $estimateresource->rate;

                                                    $Jobcard = New Jobcard();
                                                    $Jobcard->name= $name;
                                                    $Jobcard->date= date('Y-m-d');
                                                    $Jobcard->process= $processid;
                                                    $Jobcard->activity= $activity;
                                                    $Jobcard->groupid= $groupid;
                                                    $Jobcard->project_id= $project_id;
                                                    $Jobcard->resource= $resource_id;
                                                    $Jobcard->unit= $resource_unit;
                                                    $Jobcard->res_quantity= $resource_qty;
                                                    $Jobcard->res_rate= $resource_rate;
                                                    $Jobcard->est_resqty= $resource_qty;
                                                
                                                    $Jobcard->vendor_id= $vendor_id;
                                                
                                                    
                                                    $Jobcard->user_id= $uid;
                                                    $Jobcard->app_activity= $activity_id;
                                                    $Jobcard->iow= $iow_id;
                                                    $Jobcard->status= 1;
                                                    //$Jobcard->save(false);
                                                    $Jobcard->pricing_resourceid= $estimateresource->pricing_resourceid;
                                                    if($Jobcard->save(false)):
        
                                                        $nwgroupid = time();
                                                        $Jobcard->groupid= $Jobcard->job_id.$Jobcard->job_id;
                                                        $Jobcard->save(false);
        
                                                        $model=New Cart();
        
                                                        $model->Job_Id= $Jobcard->job_id;
                                                        $model->Vendor_Id= $Jobcard->vendor_id;
                                                        $model->ResourceType_Id= $res->ResourceType_Id;
                                                        $model->Resource_Id= $Jobcard->resource;
                                                        $model->unit= $Jobcard->unit;
                                                        $model->rate= $resource_rate;
                                                        $model->Qnty= $resource_qty;
                                                        $model->EstresQnty= $resource_qty;
                                                        
                                                        $amount = $resource_rate * $resource_qty;
                                                        $model->amount= $amount;
                                                        $model->Project= $Jobcard->project_id;
                                                        $model->groupid= $Jobcard->groupid;
                                                        if($estimateresource->no_of_men){
                                                            $model->no_workers=$estimateresource->no_of_men;
                                                        }
                                                        if($estimateresource->no_of_days){
                                                            $model->no_days=$estimateresource->no_of_days;
                                                        }
        
                                                        $model->fuel_status = 0;
                                                        $model->pricing_resourceid= $estimateresource->pricing_resourceid;
                                                        $model->save(false);
        
                                                        $Jobcard->cart_status=1;
                                                        $Jobcard->save(false);
        
                                                    endif;
                                                }

                                                else{
                                                    
                                                    $fuel_type = $estimateresource->fuel_type;
                                                    if($fuel_type != null)
                                                    {
                                                        if($estimateresource->fuel_type == 'Petrol')
                                                            $resource_id = '183';
                                                        elseif($estimateresource->fuel_type == 'Diesel')
                                                            $resource_id = '184';
                                                    }

                                                    $Jobcarddf = Jobcard::find()->where(['resource'=>$resource_id])->andWhere(['delete_status'=>0])->andWhere(['project_id'=>$estimateresource->project_id])->andWhere(['vendor_id'=>$vendor_id])->one();

                                                    $cartf = cart::find()->where(['Resource_Id'=>$resource_id])->andWhere(['status'=>0])->andWhere(['Project'=>$estimateresource->project_id])->andWhere(['Vendor_Id'=>$vendor_id])->one();


                                                    if($cartf)
                                                    {

                                                        $totqty = 0;
                                                        $resourcee_rate = 0;
                                                        $tot_f_qty = 0;

                                                        $fuelestimateress=PricingEstimateResourcesNew::find()->andWhere(['project_id'=>$estimateresource->project_id])->andWhere(['pricing_status'=>0])/*->andWhere(['lease_period'=>null])*/->andWhere(['fuel_type' => $fuel_type])->andWhere(['IN','resourcetype_Id',[24,26]])->andWhere(['diesel_orderd_status'=>0])->andWhere(['fuel_vendor_id'=>$vendor_id])->all();

                                                        foreach($fuelestimateress as $estimateres){

                                                            $estimateact=PricingEstimateNew::find()->where(['project_Id'=>$projuser->projectid])->andWhere(['activity_Id'=>$estimateres->activity_id])->andWhere(['pricing_status' => 0])->one();

                                                            if($estimateact['activity_qty']!=0){

                                                                $wrkact_qty = $estimateact->activity_qty;

                                                            }
                                                            else{
                                                                $wrkact_qty = 1;
                                                            }

                                                            $qty = $estimateres->quantity * $wrkact_qty;
                                                            $totqty = $totqty + $qty;
                                                            $resourcee_rate = $resourcee_rate + $estimateres->rate;

                                                           // if($estimateres->resourcetype_Id==26 && $estimateres->fuel_type != ''){

                                                                $fuelqty = $estimateres->fuel_qty * $estimateres->hours_no;
                                                                $f_qty = $fuelqty * $wrkact_qty;
                                                                $tot_f_qty = $tot_f_qty + $f_qty;
                                                                $fuelrate =  $estimateres->fuel_rate;

                                                            //}

                                                            $diesletabs = Pricingordereddiesel::find()->where(['pricing_res_id'=>$estimateres->pricing_resourceid])->one();

                                                            if($diesletabs){

                                                            }else{
                                                                $dieselcapturing = new Pricingordereddiesel;
                                                                $dieselcapturing->cartid = $cartf->cartID;
                                                                $dieselcapturing->pricing_res_id = $estimateres->pricing_resourceid;

                                                                $dieselcapturing->save(false);
                                                            }

                                                        }


                                                        $Jobcarddf->res_quantity = $tot_f_qty;
                                                        $Jobcarddf->res_rate = $fuelrate;
                                                        $Jobcarddf->est_resqty = $tot_f_qty;
                                                        $Jobcarddf->status = 1;

                                                        if($Jobcarddf->save(false)):

                                                            $cartf->rate= $Jobcarddf->res_rate;
                                                            $cartf->Qnty= $tot_f_qty;
                                                            $cartf->EstresQnty=$tot_f_qty;
                                                            $amount = $fuelrate * $tot_f_qty;
                                                            $cartf->amount= $amount;
                                                            
                                                            $cartf->save(false);

                                                            $Jobcarddf->cart_status=1;
                                                            $Jobcarddf->save(false);

                                                        endif;

                                                        $rescount=1;


                                                    }

                                                    else{

                                                        $activity = $wbsactivity['activity_Name'];
                                                        $project_id = $projuser->projectid;
                                                        $resource_id = $estimateresource->resource_Id;
                                                        $res = Resources::find()->where(['Resource_Id'=>$resource_id])->one();
                                                        $resource_unit = $res->Unit;
                                                        $estimateact=PricingEstimateNew::find()->where(['project_Id'=>$projuser->projectid])->andWhere(['activity_Id'=>$wbsactivity['id']])->andWhere(['pricing_status' => 0])->one();


                                                        if($estimateact->activity_qty!=0){
                                                            $wrkact_qty = $estimateact->activity_qty;
                                                        }
                                                        else{
                                                            $wrkact_qty = 1;
                                                        }
                                                    

                                                        $resource_rate = $estimateresource->rate;
                                                        $activity_id = $wbsactivity['id'];
                                                        $iow_id = $wbsactivity['wbs_id'];
                                                        $vendor_id = $res->Vendor_Id;


                                                        if($estimateresource->fuel_type != null){

                                                            $fuel_qty = $estimateresource->fuel_qty * $estimateresource->hours_no;
                
                                                            $resource_qty = $fuel_qty * $wrkact_qty;
                                                            
                                                            $resource_unit = 'Ltr';
                                                            $resource_rate = $estimateresource->fuel_rate;
                
                                                            $fuel_type = $estimateresource->fuel_type;
                                                            if($fuel_type != null)
                                                            {
                                                                if($estimateresource->fuel_type == 'Petrol')
                                                                    $resource_id = '183';
                                                                elseif($estimateresource->fuel_type == 'Diesel')
                                                                    $resource_id = '184';
                                                            }
                                                        }

                                                        $Jobcard = New Jobcard();
                                                        $Jobcard->name= $name;
                                                        $Jobcard->date= date('Y-m-d');
                                                        $Jobcard->process= $processid;
                                                        $Jobcard->activity= $activity;
                                                        $Jobcard->groupid= $groupid;
                                                        $Jobcard->project_id= $project_id;
                                                        $Jobcard->resource= $resource_id;
                                                        $Jobcard->unit= $resource_unit;
                                                        $Jobcard->res_quantity= $resource_qty;
                                                        $Jobcard->res_rate= $resource_rate;
                                                        $Jobcard->est_resqty= $resource_qty;

                                                    
                                                        $Jobcard->vendor_id= $vendor_id;

                                                        $Jobcard->user_id= $uid;
                                                        $Jobcard->app_activity= $activity_id;
                                                        $Jobcard->iow= $iow_id;
                                                        $Jobcard->status= 1;
                                                        //$Jobcard->save(false);
                                                        $Jobcard->pricing_resourceid= $estimateresource->pricing_resourceid;
                                                        if($Jobcard->save(false)):
                                                            $nwgroupid = time();
                                                            $Jobcard->groupid= $Jobcard->job_id.$Jobcard->job_id;
                                                            $Jobcard->save(false);

                                                            $model=New Cart();

                                                            $model->Job_Id= $Jobcard->job_id;
                                                            //if($estimateresource->resourcetype_Id == 24 /*&& $estimateresource->lease_period == null*/){
                                                            if($estimateresource->resourcetype_Id == 26 && $estimateresource->fuel_type != null){
                                                                $model->Vendor_Id= $estimateresource->fuel_vendor_id;                                              
                                                            }else{
                                                                $model->Vendor_Id= $Jobcard->vendor_id;
                                                            }
                                                            $model->ResourceType_Id= $res->ResourceType_Id;
                                                            $model->Resource_Id= $Jobcard->resource;
                                                            $model->unit= $Jobcard->unit;
                                                            $model->rate= $resource_rate;
                                                            $model->Qnty= $resource_qty;
                                                            $model->EstresQnty= $resource_qty;
                                                            
                                                            $amount = $resource_rate * $resource_qty;
                                                            
                                                            $model->amount= $amount;
                                                            $model->Project= $Jobcard->project_id;
                                                            $model->groupid= $Jobcard->groupid;
                                                            if($estimateresource->no_of_men){
                                                                $model->no_workers=$estimateresource->no_of_men;
                                                            }
                                                            if($estimateresource->no_of_days){
                                                                $model->no_days=$estimateresource->no_of_days;
                                                            }

                                                            //if($estimateresource->resourcetype_Id == 24 /*&& $estimateresource->lease_period != null*/){
                                                            if($estimateresource->resourcetype_Id == 26 && $estimateresource->fuel_type != null){ 
                                                                $model->fuel_status = 1;
                                                            }else{
                                                                $model->fuel_status = 0;
                                                            }
                                                            
                                                            $model->pricing_resourceid= $estimateresource->pricing_resourceid;
                                                            $model->save(false);

                                                            $Jobcard->cart_status=1;
                                                            $Jobcard->save(false);

                                                            if($estimateresource->fuel_type != null){

                                                                $diesletabs = Pricingordereddiesel::find()->where(['pricing_res_id'=>$estimateresource->pricing_resourceid])->one();

                                                                if($diesletabs){

                                                                }else{
                                                                    $dieselcapturing = new Pricingordereddiesel;
                                                                    $dieselcapturing->cartid = $model->cartID;
                                                                    $dieselcapturing->pricing_res_id = $estimateresource->pricing_resourceid;

                                                                    $dieselcapturing->save(false);
                                                                }

                                                            
                                                            }
                                                        endif;
                                                            
                                                    }
                                                    

                                                }

                                            

                                            }else{
                                                
                                                $resource_id = '0';
                                                $fuel_type = $estimateresource->fuel_type;
                                                if($fuel_type != null)
                                                {
                                                    if($estimateresource->fuel_type == 'Petrol')
                                                        $resource_id = '183';
                                                    elseif($estimateresource->fuel_type == 'Diesel')
                                                        $resource_id = '184';
                                                }

                                                $Jobcarddf = Jobcard::find()->where(['resource'=>$resource_id])->andWhere(['delete_status'=>0])->andWhere(['project_id'=>$estimateresource->project_id])->andWhere(['vendor_id'=>$vendor_id])->one();

                                                $cartf = cart::find()->where(['Resource_Id'=>$resource_id])->andWhere(['status'=>0])->andWhere(['Project'=>$estimateresource->project_id])->andWhere(['Vendor_Id'=>$vendor_id])->one();
                                                
                                                if($cartf)
                                                {
                                                    
                                                    $totqty = 0;
                                                    $resourcee_rate = 0;
                                                    $tot_f_qty = 0;
                                                    //$fuelrate = 0;

                                                    $fuelestimateress=PricingEstimateResourcesNew::find()->andWhere(['project_id'=>$estimateresource->project_id])->andWhere(['pricing_status'=>0])->andWhere(['fuel_type' => $fuel_type])->andWhere(['IN','resourcetype_Id',[24,26]])->andWhere(['diesel_orderd_status'=>0])->andWhere(['fuel_vendor_id'=>$vendor_id])->all();

                                                    foreach($fuelestimateress as $estimateres){

                                                        $estimateact=PricingEstimateNew::find()->where(['project_Id'=>$projuser->projectid])->andWhere(['activity_Id'=>$estimateres->activity_id])->andWhere(['pricing_status' => 0])->one();

                                                        if($estimateact['activity_qty']!=0){

                                                            $wrkact_qty = $estimateact->activity_qty;

                                                        }
                                                        else{
                                                            $wrkact_qty = 1;
                                                        }

                                                        $qty = $estimateres->quantity * $wrkact_qty;

                                                        $totqty = $totqty + $qty;

                                                        $resourcee_rate = $resourcee_rate + $estimateres->rate;
                                                        $fuelqty = $estimateres->fuel_qty * $estimateres->hours_no;
                                                        $f_qty = $fuelqty * $wrkact_qty;
                                                        $tot_f_qty = $tot_f_qty + $f_qty;
                                                        $fuelrate =  $estimateres->fuel_rate;

                                                        $diesletabs = Pricingordereddiesel::find()->where(['pricing_res_id'=>$estimateres->pricing_resourceid])->one();

                                                            if($diesletabs){

                                                            }else{
                                                                $dieselcapturing = new Pricingordereddiesel;
                                                                $dieselcapturing->cartid = $cartf->cartID;
                                                                $dieselcapturing->pricing_res_id = $estimateres->pricing_resourceid;

                                                                $dieselcapturing->save(false);
                                                            }

                                                    }

                                                    //$countmachine = count($estimateress);

                                                    //if($countmachine !=0){
                                                        $resource_rate = $resourcee_rate;
                                                    //}

                                                    $Jobcarddf->res_quantity = $tot_f_qty;
                                                    $Jobcarddf->res_rate = $fuelrate;
                                                    $Jobcarddf->est_resqty = $tot_f_qty;
                                                    $Jobcarddf->status = 1;


                                                    if($Jobcarddf->save(false)):

                                                        $cartf->rate= $Jobcarddf->res_rate;
                                                        $cartf->Qnty= $tot_f_qty;
                                                        $cartf->EstresQnty=$tot_f_qty;
                                                        $amount = $fuelrate * $tot_f_qty;
                                                        $cartf->amount= $amount;
                                                        
                                                        $cartf->save(false);

                                                        $Jobcarddf->cart_status=1;
                                                        $Jobcarddf->save(false);

                                                    endif;

                                                    $rescount=1;

                                                }else{
                                                    $activity = $wbsactivity['activity_Name'];
                                                    $project_id = $projuser->projectid;
                                                    $resource_id = $estimateresource->resource_Id;
                                                    $res = Resources::find()->where(['Resource_Id'=>$resource_id])->one();
                                                    $resource_unit = $res->Unit;

                                                    $estimateact=PricingEstimateNew::find()->where(['project_Id'=>$projuser->projectid])->andWhere(['activity_Id'=>$wbsactivity['id']])->andWhere(['pricing_status' => 0])->one();

                                                    if($estimateact->activity_qty!=0){
                                                        $wrkact_qty = $estimateact->activity_qty;
                                                    }
                                                    else{
                                                        $wrkact_qty = 1;
                                                    }

                                                    $resource_rate = $estimateresource->rate;
                                                    $activity_id = $wbsactivity['id'];
                                                    $iow_id = $wbsactivity['wbs_id'];
                                                    $vendor_id = $res->Vendor_Id;


                                                    if($estimateresource->fuel_type != null){

                                                        $fuel_qty = $estimateresource->fuel_qty * $estimateresource->hours_no;
                                                        $resource_qty = $fuel_qty * $wrkact_qty;
                                                        $resource_unit = 'Ltr';
                                                        $resource_rate = $estimateresource->fuel_rate;
            
                                                        if($estimateresource->fuel_type == 'Petrol'){
                                                            $resids = Resources::find()->where(['Vendor_Id' => 0])->andWhere(['LIKE','Name','Petrol'])->one();
                                                            $resource_id = '183';
            
                                                        }elseif($estimateresource->fuel_type == 'Diesel'){
                                                            $resids = Resources::find()->where(['Vendor_Id' => 0])->andWhere(['LIKE','Name','Diesel'])->one();
                                                            $resource_id = '184';
                                                        }
                                                    }

                                                    $Jobcard = New Jobcard();
                                                    $Jobcard->name= $name;
                                                    $Jobcard->date= date('Y-m-d');
                                                    $Jobcard->process= $processid;
                                                    $Jobcard->activity= $activity;
                                                    $Jobcard->groupid= $groupid;
                                                    $Jobcard->project_id= $project_id;
                                                    $Jobcard->resource= $resource_id;
                                                    $Jobcard->unit= $resource_unit;
                                                    $Jobcard->res_quantity= $resource_qty;
                                                    $Jobcard->res_rate= $resource_rate;
                                                    $Jobcard->est_resqty= $resource_qty;

                                                    $Jobcard->vendor_id= $vendor_id;
                                                    
                                                    $Jobcard->user_id= $uid;
                                                    $Jobcard->app_activity= $activity_id;
                                                    $Jobcard->iow= $iow_id;
                                                    $Jobcard->status= 1;
                                                    //$Jobcard->save(false);
                                                    $Jobcard->pricing_resourceid= $estimateresource->pricing_resourceid;
                                                    if($Jobcard->save(false)):
                                                        $nwgroupid = time();
                                                        $Jobcard->groupid= $Jobcard->job_id.$Jobcard->job_id;
                                                        $Jobcard->save(false);

                                                        $model=New Cart();

                                                        $model->Job_Id= $Jobcard->job_id;
                                                        //if($estimateresource->resourcetype_Id == 24 /*&& $estimateresource->lease_period == null*/){
                                                        if($estimateresource->resourcetype_Id == 26 && $estimateresource->fuel_type != null){                       
                                                            $model->Vendor_Id= $estimateresource->fuel_vendor_id;                                               
                                                        }else{
                                                            $model->Vendor_Id= $Jobcard->vendor_id;
                                                        }
                                                        $model->ResourceType_Id= $res->ResourceType_Id;
                                                        $model->Resource_Id= $Jobcard->resource;
                                                        $model->unit= $Jobcard->unit;
                                                        $model->rate= $resource_rate;
                                                        $model->Qnty= $resource_qty;
                                                        $model->EstresQnty= $resource_qty;

                                                        
                                                            $amount = $resource_rate * $resource_qty;
                                                        
                                                        $model->amount= $amount;
                                                        $model->Project= $Jobcard->project_id;
                                                        $model->groupid= $Jobcard->groupid;
                                                        if($estimateresource->no_of_men){
                                                            $model->no_workers=$estimateresource->no_of_men;
                                                        }
                                                        if($estimateresource->no_of_days){
                                                            $model->no_days=$estimateresource->no_of_days;
                                                        }

                                                        //if($estimateresource->resourcetype_Id == 24/* && $estimateresource->lease_period != null*/){
                                                        if($estimateresource->resourcetype_Id == 26 && $estimateresource->fuel_type != null){ 
                                                            $model->fuel_status = 1;
                                                        }else{
                                                            $model->fuel_status = 0;
                                                        }
                                                        
                                                        $model->pricing_resourceid= $estimateresource->pricing_resourceid;
                                                        $model->save(false);

                                                        $Jobcard->cart_status=1;
                                                        $Jobcard->save(false);

                                                        if($estimateresource->fuel_type != null){

                                                            $diesletabs = Pricingordereddiesel::find()->where(['pricing_res_id'=>$estimateresource->pricing_resourceid])->one();

                                                            if($diesletabs){

                                                            }else{
                                                                $dieselcapturing = new Pricingordereddiesel;
                                                                $dieselcapturing->cartid = $model->cartID;
                                                                $dieselcapturing->pricing_res_id = $estimateresource->pricing_resourceid;

                                                                $dieselcapturing->save(false);
                                                            }

                                                        
                                                        }
                                                    endif;

                                                }
                                                
                                            }

                                            $rescount=1;

                                        
                                        }


                                    }

                                    elseif($estimateresource->resourcetype_Id == 33)
                                    { 
                                        //----- New Calculation added Sreejith 0n 27/02/23 Picking the data from Master--------
                                        //if(!$estimateresource->no_of_men || !$estimateresource->no_of_days){

                                            $scheduleActivity = Scheduleactivities::find()->where(['activity_id' => $estimateresource->activity_id])->one();

                                            if($scheduleActivity){
                                                    //$man_qty = ($resource['est_resource_quantity']);
                                                    $man_qty = $estimateresource->quantity;
                                                    $act_qty = $scheduleActivity->quantity;
                                                    $duration = $scheduleActivity->duration;
                                                    $no_of_men = ceil(($man_qty * $act_qty) / $duration);
                                                    //$no_of_days = $duration;    
                                                if($no_of_men){
                                                    $no_of_days = floor(($man_qty * $act_qty) / $no_of_men);
                                                    $estimateresource->no_of_men  = $no_of_men;
                                                    $estimateresource->no_of_days = $no_of_days;
                                                    $estimateresource->save(false);
                                                }
                                            }
                                        //--------------------------------------------------


                                        $resource=Resources::findOne($estimateresource->resource_Id);

                                        $Jobcardd=Jobcard::find()->where(['vendor_id'=>$resource->Vendor_Id])->andWhere(['resource'=>$resource->Resource_Id])->andWhere(['delete_status'=>0])->andWhere(['project_id'=>$estimateresource->project_id])->andWhere(['app_activity'=>$estimateresource->activity_id])->orderBy(['job_id'=>SORT_DESC])->one(); // activity wise
                                    
                                        $cart = cart::find()->where(['Vendor_Id'=>$resource->Vendor_Id])->andWhere(['Resource_Id'=>$resource->Resource_Id])->andWhere(['status'=>0])->andWhere(['Project'=>$estimateresource->project_id])->andWhere(['rate'=>$estimateresource->rate])->one();


                                        $editodrdata = "SELECT * FROM `cart` as c WHERE c.status=1 AND c.Vendor_Id='".$resource->Vendor_Id."' AND c.Resource_Id ='".$resource->Resource_Id."' AND c.Project='".$estimateresource->project_id."' AND c.rate ='".$estimateresource->rate."' AND c.pricing_resourceid = '".$estimateresource->pricing_resourceid."' ";

                                        $command=$connection->createCommand($editodrdata);
                                        $dataReader=$command->query();
                                        $cartedit=$dataReader->read();
                              

                                        $odrdata = "SELECT c.status as status, c.Qnty as Qnty FROM `cart` as c INNER JOIN jobcard as j on j.job_id=c.Job_Id WHERE j.vendor_id='".$resource->Vendor_Id."' AND c.status = 0 AND j.resource ='".$resource->Resource_Id."' AND j.delete_status= 0 AND j.project_id='".$estimateresource->project_id."' AND j.app_activity ='".$estimateresource->activity_id."' ";

                                        $command=$connection->createCommand($odrdata);
                                        $dataReader=$command->query();
                                        $pdata=$dataReader->read();

                                        //calcul quanty dif aftr place odr
                                        if(empty($pdata))
                                        {
                        

                                            $odreditdata = "SELECT * FROM `cart` as c INNER JOIN jobcard as j on j.job_id=c.Job_Id WHERE j.vendor_id='".$resource->Vendor_Id."' AND j.resource ='".$resource->Resource_Id."' AND j.delete_status= 0 AND j.project_id='".$estimateresource->project_id."' AND j.app_activity ='".$estimateresource->activity_id."'  AND c.pricing_resourceid = '".$estimateresource->pricing_resourceid."' AND c.status = 1 ";

                                            $command=$connection->createCommand($odreditdata);
                                            $dataReader=$command->query();
                                            $peditdata=$dataReader->read();

                                            if($peditdata){
                                                

                                                $estimateress=PricingEstimateResourcesNew::find()->where(['resource_Id'=>$estimateresource->resource_Id])->andWhere(['project_id'=>$estimateresource->project_id])->andWhere(['activity_id'=>$estimateresource->activity_id])->andWhere(['pricing_status'=>0])->all();

                                               $totqty= 0;
                                               $no_mens= 0;
                                               $no_dys= 0;
                                               
                                                foreach($estimateress as $estimateres)
                                                {

                                                   $estimateact=PricingEstimateNew::find()->where(['project_Id'=>$projuser->projectid])->andWhere(['activity_Id'=>$estimateres->activity_id])->andWhere(['pricing_status' => 0])->one();

                                                   if($estimateact['activity_qty']!=0){

                                                   $wrkact_qty= $estimateact->activity_qty;

                                                        }
                                                        else{
                                                   $wrkact_qty= 1;
                                                        }

                                                   $qty= $estimateres->quantity * $wrkact_qty;
                                                   $totqty= $totqty + $qty;
                                                   $resourcee_rate= $estimateres->rate;
                                                   $no_mens= $no_mens + $estimateres->no_of_men;
                                                   $no_dys= $no_dys + $estimateres->no_of_days;
                                                }

                                                $cartqtys = cart::find()->where(['Vendor_Id'=>$resource->Vendor_Id])->andWhere(['Resource_Id'=>$resource->Resource_Id])->andWhere(['status'=>1])->andWhere(['Project'=>$estimateresource->project_id])->all();

                                               if($cartqtys){
                                                    $odrqty=0;
                                                    foreach($cartqtys as $cartqty)
                                                    {
                                                        $odrqty = $odrqty + $cartqty->Qnty;
                                                    }
                                                    $newqunty = $totqty - $odrqty;
                                                }else
                                                {
                                                    $newqunty = $totqty;
                                                }
                                                
                                                if($newqunty>0)
                                                {

                                                    $sql="SELECT name FROM jobcard ORDER BY name DESC limit 1";
                                                    $command=$connection->createCommand($sql);
                                                    $dataReader=$command->query();
                                                    $no=$dataReader->read();
                                                    if($no)
                                                    {
                                                    $name=$no['name'] + 1;
                                                    }
                                                    else
                                                    {
                                                        $name= 1;
                                                    }
                                                    
                                                    $groupid=time();

                                                    $processid = $estimateresource->process_Id;

                                                    $activity = $wbsactivity['activity_Name'];

                                                    $project_id = $projuser->projectid;

                                                    $resource_id = $estimateresource->resource_Id;

                                                    $res = Resources::find()->where(['Resource_Id'=>$resource_id])->one();

                                                    $resource_unit = $res->Unit;

                                                    $estimateact=PricingEstimateNew::find()->where(['project_Id'=>$projuser->projectid])->andWhere(['activity_Id'=>$wbsactivity['id']])->andWhere(['pricing_status' => 0])->one();

                                                    if($estimateact->activity_qty!=0){

                                                        $wrkact_qty = $estimateact->activity_qty;

                                                    }
                                                    else{
                                                        $wrkact_qty = 1;
                                                    }
                                                    

                                                    $resource_qty = $newqunty;

                                                    $resource_rate = $estimateresource->rate;

                                                    $activity_id = $wbsactivity['id'];

                                                    $iow_id = $wbsactivity['wbs_id'];

                                                    $vendor_id = $res->Vendor_Id;
                                                    
                                                    
                                                    $Jobcard = New Jobcard();
                                                    $Jobcard->name= $name;
                                                    $Jobcard->date= date('Y-m-d');
                                                    $Jobcard->process= $processid;
                                                    $Jobcard->activity= $activity;
                                                    $Jobcard->groupid= $groupid;
                                                    $Jobcard->project_id= $project_id;
                                                    $Jobcard->resource= $resource_id;
                                                    
                                                    $Jobcard->res_quantity= $resource_qty;
                                                    $Jobcard->res_rate= $resource_rate;
                                                    $Jobcard->est_resqty= $resource_qty;
                                                    $Jobcard->vendor_id= $vendor_id;
                                                    $Jobcard->user_id= $uid;
                                                    $Jobcard->app_activity= $activity_id;
                                                    $Jobcard->iow= $iow_id;
                                                    $Jobcard->status= 1;
                                                    
                                                    $Jobcard->pricing_resourceid= $estimateresource->pricing_resourceid;
                                                
                                                    $Jobcard->unit= $resource_unit;
                                                
                                                    if($Jobcard->save(false)):

                                                        $nwgroupid = time();
                                                        $Jobcard->groupid= $Jobcard->job_id.$Jobcard->job_id;
                                                        $Jobcard->save(false);

                                                        $model=New Cart();

                                                        $model->Job_Id= $Jobcard->job_id;
                                                        $model->Vendor_Id= $Jobcard->vendor_id;
                                                        $model->ResourceType_Id= $res->ResourceType_Id;
                                                        $model->Resource_Id= $Jobcard->resource;
                                                        
                                                        $model->rate= $resource_rate;
                                                        $model->Qnty= $resource_qty;
                                                        $model->EstresQnty= $resource_qty;
                                                        $amount = $resource_rate * $resource_qty;
                                                        $model->amount= $amount;
                                                        $model->Project= $Jobcard->project_id;
                                                        $model->groupid= $Jobcard->groupid;
                                                        if($estimateresource->no_of_men){
                                                            $model->no_workers=$estimateresource->no_of_men;
                                                        }
                                                        if($estimateresource->no_of_days){
                                                            $model->no_days=$estimateresource->no_of_days;
                                                        }

                                                        $model->fuel_status = 0;
                                                        $model->unit= $Jobcard->unit;

                                                        $model->pricing_resourceid= $estimateresource->pricing_resourceid;                         
                                                        $model->save(false);

                                                        $Jobcard->cart_status=1;
                                                        $Jobcard->save(false);

                                                    endif;

                                                    $rescount=1;


                                                }


                                            }
                                            else
                                            {

                                                $sql="SELECT name FROM jobcard ORDER BY name DESC limit 1";
                                                $command=$connection->createCommand($sql);
                                                $dataReader=$command->query();
                                                $no=$dataReader->read();
                                                if($no)
                                                {
                                                   $name=$no['name'] + 1;
                                                }
                                                else
                                                {
                                                    $name= 1;
                                                }
                                                
                                                $groupid=time();

                                                $processid = $estimateresource->process_Id;

                                                $activity = $wbsactivity['activity_Name'];

                                                $project_id = $projuser->projectid;

                                                $resource_id = $estimateresource->resource_Id;

                                                $res = Resources::find()->where(['Resource_Id'=>$resource_id])->one();

                                                $resource_unit = $res->Unit;

                                                 $estimateact=PricingEstimateNew::find()->where(['project_Id'=>$projuser->projectid])->andWhere(['activity_Id'=>$wbsactivity['id']])->andWhere(['pricing_status' => 0])->one();

                                                if($estimateact->activity_qty!=0){

                                                    $wrkact_qty = $estimateact->activity_qty;

                                                }
                                                else{
                                                    $wrkact_qty = 1;
                                                }
                                                

                                                $resource_qty = $estimateresource->quantity * $wrkact_qty;

                                                $resource_rate = $estimateresource->rate;

                                                $activity_id = $wbsactivity['id'];

                                                $iow_id = $wbsactivity['wbs_id'];

                                                $vendor_id = $res->Vendor_Id;

                                                
                                                $Jobcard = New Jobcard();
                                                $Jobcard->name= $name;
                                                $Jobcard->date= date('Y-m-d');
                                                $Jobcard->process= $processid;
                                                $Jobcard->activity= $activity;
                                                $Jobcard->groupid= $groupid;
                                                $Jobcard->project_id= $project_id;
                                                $Jobcard->resource= $resource_id;
                                                
                                                $Jobcard->res_quantity= $resource_qty;
                                                $Jobcard->res_rate= $resource_rate;
                                                $Jobcard->est_resqty= $resource_qty;
                                                $Jobcard->vendor_id= $vendor_id;
                                                $Jobcard->user_id= $uid;
                                                $Jobcard->app_activity= $activity_id;
                                                $Jobcard->iow= $iow_id;
                                                $Jobcard->status= 1;
                                                
                                                $Jobcard->pricing_resourceid= $estimateresource->pricing_resourceid;
                                               
                                                $Jobcard->unit= $resource_unit;
                                             
                                                if($Jobcard->save(false)):

                                                    $nwgroupid = time();
                                                    $Jobcard->groupid= $Jobcard->job_id.$Jobcard->job_id;
                                                    $Jobcard->save(false);

                                                    $model=New Cart();

                                                    $model->Job_Id= $Jobcard->job_id;
                                                    $model->Vendor_Id= $Jobcard->vendor_id;
                                                    $model->ResourceType_Id= $res->ResourceType_Id;
                                                    $model->Resource_Id= $Jobcard->resource;
                                                    
                                                    $model->rate= $resource_rate;
                                                    $model->Qnty= $resource_qty;
                                                    $model->EstresQnty= $resource_qty;
                                                    $amount = $resource_rate * $resource_qty;
                                                    $model->amount= $amount;
                                                    $model->Project= $Jobcard->project_id;
                                                    $model->groupid= $Jobcard->groupid;
                                                    if($estimateresource->no_of_men){
                                                        $model->no_workers=$estimateresource->no_of_men;
                                                    }
                                                    if($estimateresource->no_of_days){
                                                        $model->no_days=$estimateresource->no_of_days;
                                                    }

                                                    $model->fuel_status = 0;
                                                    $model->unit= $Jobcard->unit;

                                                    $model->pricing_resourceid= $estimateresource->pricing_resourceid;                         
                                                    $model->save(false);

                                                    $Jobcard->cart_status=1;
                                                    $Jobcard->save(false);

                                                endif;

                                                $rescount=1;
                                            }
                                           

                                        }
                                        elseif(!empty($pdata))
                                        { 
                       
                                            $orderstatus = $pdata['status']; 
                                            
                                            $cartdata = cart::find()->where(['Vendor_Id'=>$resource->Vendor_Id])->andWhere(['Resource_Id'=>$resource->Resource_Id])->andWhere(['status'=>0])->andWhere(['Project'=>$estimateresource->project_id])->one();

                                            if($orderstatus==1 && empty($cartdata))
                                            {
                
                                                $Qnty = $pdata['Qnty']; 
                                                $editestimateqnty = $estimateresource->quantity;
                                                $newquntity = 0;
                                                if($Qnty<$editestimateqnty)
                                                {
                                                     $newquntity = $editestimateqnty - $Qnty;
                                                }
                                                if($newquntity>0)
                                                {


 
                                                    $sql="SELECT name FROM jobcard ORDER BY name DESC limit 1";
                                                    $command=$connection->createCommand($sql);
                                                    $dataReader=$command->query();
                                                    $no=$dataReader->read();
                                                    if($no)
                                                    {
                                                       $name=$no['name'] + 1;
                                                    }
                                                    else
                                                    {
                                                        $name= 1;
                                                    }
                                                    
                                                    $groupid=time();

                                                    $processid = $estimateresource->process_Id;

                                                    $activity = $wbsactivity['activity_Name'];

                                                    $project_id = $projuser->projectid;

                                                    $resource_id = $estimateresource->resource_Id;

                                                    $res = Resources::find()->where(['Resource_Id'=>$resource_id])->one();

                                                    $resource_unit = $res->Unit;

                                                     $estimateact=PricingEstimateNew::find()->where(['project_Id'=>$projuser->projectid])->andWhere(['activity_Id'=>$wbsactivity['id']])->andWhere(['pricing_status' => 0])->one();

                                                    if($estimateact->activity_qty!=0){

                                                        $wrkact_qty = $estimateact->activity_qty;

                                                    }
                                                    else{
                                                        $wrkact_qty = 1;
                                                    }
                                                    

                                                    $resource_qty = $newquntity * $wrkact_qty;

                                                    $resource_rate = $estimateresource->rate;

                                                    $activity_id = $wbsactivity['id'];

                                                    $iow_id = $wbsactivity['wbs_id'];

                                                    $vendor_id = $res->Vendor_Id;

                                                    
                                                    $Jobcard = New Jobcard();
                                                    $Jobcard->name= $name;
                                                    $Jobcard->date= date('Y-m-d');
                                                    $Jobcard->process= $processid;
                                                    $Jobcard->activity= $activity;
                                                    $Jobcard->groupid= $groupid;
                                                    $Jobcard->project_id= $project_id;
                                                    $Jobcard->resource= $resource_id;
                                                    
                                                    $Jobcard->res_quantity= $resource_qty;
                                                    $Jobcard->res_rate= $resource_rate;
                                                    $Jobcard->est_resqty= $resource_qty;
                                                    $Jobcard->vendor_id= $vendor_id;
                                                    $Jobcard->user_id= $uid;
                                                    $Jobcard->app_activity= $activity_id;
                                                    $Jobcard->iow= $iow_id;
                                                    $Jobcard->status= 1;
                                                    
                                                    $Jobcard->pricing_resourceid= $estimateresource->pricing_resourceid;
                                                   
                                                    $Jobcard->unit= $resource_unit;
                                                    if($Jobcard->save(false)):

                                                        $nwgroupid = time();
                                                        $Jobcard->groupid= $Jobcard->job_id.$Jobcard->job_id;
                                                        $Jobcard->save(false);

                                                        $model=New Cart();

                                                        $model->Job_Id= $Jobcard->job_id;
                                                        $model->Vendor_Id= $Jobcard->vendor_id;
                                                        $model->ResourceType_Id= $res->ResourceType_Id;
                                                        $model->Resource_Id= $Jobcard->resource;
                                                        
                                                        $model->rate= $resource_rate;
                                                        $model->Qnty= $resource_qty;
                                                        $model->EstresQnty= $resource_qty;
                                                        $amount = $resource_rate * $resource_qty;
                                                        $model->amount= $amount;
                                                        $model->Project= $Jobcard->project_id;
                                                        $model->groupid= $Jobcard->groupid;
                                                        if($estimateresource->no_of_men){
                                                            $model->no_workers=$estimateresource->no_of_men;
                                                        }
                                                        if($estimateresource->no_of_days){
                                                            $model->no_days=$estimateresource->no_of_days;
                                                        }

                                                        $model->fuel_status = 0;
                                                        $model->unit= $Jobcard->unit;

                                                        $model->pricing_resourceid= $estimateresource->pricing_resourceid;                         
                                                        $model->save(false);

                                                        $Jobcard->cart_status=1;
                                                        $Jobcard->save(false);

                                                    endif;

                                                    $rescount=1;

                                                }


                                            }
                                            else
                                            {
                                                
                                                 //clubbing of quantity  
      
                                               $catsne= cart::find()->where(['Vendor_Id'=>$resource->Vendor_Id])->andWhere(['Resource_Id'=>$resource->Resource_Id])->andWhere(['status'=>0])->andWhere(['Project'=>$estimateresource->project_id])->andWhere(['rate'=>$estimateresource->rate])->andWhere(['Job_Id'=>$Jobcardd->job_id])->one();
                                               $estimateress=PricingEstimateResourcesNew::find()->where(['resource_Id'=>$estimateresource->resource_Id])->andWhere(['project_id'=>$estimateresource->project_id])->andWhere(['activity_id'=>$estimateresource->activity_id])->andWhere(['pricing_status'=>0])->all();

                                               $totqty= 0;
                                               $no_mens= 0;
                                               $no_dys= 0;


                                                foreach($estimateress as $estimateres)
                                                {

                                                   $estimateact=PricingEstimateNew::find()->where(['project_Id'=>$projuser->projectid])->andWhere(['activity_Id'=>$estimateres->activity_id])->andWhere(['pricing_status' => 0])->one();

                                                   if($estimateact['activity_qty']!=0){

                                                   $wrkact_qty= $estimateact->activity_qty;

                                                        }
                                                        else{
                                                   $wrkact_qty= 1;
                                                        }

                                                   $qty= $estimateres->quantity * $wrkact_qty;

                                                   $totqty= $totqty + $qty;

                                                   $resourcee_rate= $estimateres->rate;

                                                   $no_mens= $no_mens + $estimateres->no_of_men;

                                                   $no_dys= $no_dys + $estimateres->no_of_days;


                                                }

                                                $cartqtys = cart::find()->where(['Vendor_Id'=>$resource->Vendor_Id])->andWhere(['Resource_Id'=>$resource->Resource_Id])->andWhere(['status'=>1])->andWhere(['Project'=>$estimateresource->project_id])->all();
                                               if($cartqtys){

                                                    $odrqty=0;
                                                    foreach($cartqtys as $cartqty)
                                                    {
                                                        $odrqty = $odrqty + $cartqty->Qnty;

                                                    }

                                                    $newqunty = $totqty - $odrqty;
                                                }else
                                                {
                                                  $newqunty = $totqty;
                                                }

                                                if($newqunty>0)
                                                {

                                                    $Jobcardd->res_quantity= $newqunty;
                                                    $Jobcardd->res_rate= $resourcee_rate;
                                                    $Jobcardd->est_resqty= $newqunty;
                                                    $Jobcardd->status= 1;

                                                    if($Jobcardd->save(false)):
                                                            if($catsne)
                                                                {

                                                                $catsne->rate= $Jobcardd->res_rate;
                                                                $catsne->Qnty= $Jobcardd->res_quantity;
                                                                $catsne->EstresQnty=$Jobcardd->res_quantity;
                                                                $amount= $resourcee_rate * $Jobcardd->res_quantity;
                                                                $catsne->amount= $amount;

                                                                    //if($estimateresource->no_of_men){
                                                                $catsne->no_workers=$no_mens;
                                                                            //}
                                                                            //if($estimateresource->no_of_days){
                                                                $catsne->no_days=$no_dys;
                                                                            //}

                                                                        
                                                                $catsne->fuel_status= 0;
                                                                $catsne->save(false);
                                                                }

                                                                $Jobcardd->cart_status=1;
                                                                $Jobcardd->save(false);


                                                            endif;

                                                        $rescount=1;

                                                }
                                               
                                               //clubbing of quantity  end
                                            }
                                        }
                                    }


                                    //other cases
                                    else
                                    {

                                        $resource=Resources::findOne($estimateresource->resource_Id);

                                       // $Jobcardd=Jobcard::find()->where(['vendor_id'=>$resource->Vendor_Id])->andWhere(['resource'=>$resource->Resource_Id])->andWhere(['app_activity'=>$estimateresource->activity_id])->andWhere(['delete_status'=>0])->andWhere(['project_id'=>$estimateresource->project_id])->andWhere(['pricing_resourceid'=>$estimateresource->pricing_resourceid])->one(); //chnges in 22/12/2021
                                        
                                        // print_r($Jobcardd);exit;
                                        
                                        $Jobcardd=Jobcard::find()->where(['vendor_id'=>$resource->Vendor_Id])->andWhere(['resource'=>$resource->Resource_Id])->andWhere(['delete_status'=>0])->andWhere(['project_id'=>$estimateresource->project_id])->orderBy(['job_id'=>SORT_DESC])->one(); 
                                        //$Jobcardd=Jobcard::find()->where(['vendor_id'=>$resource->Vendor_Id])->andWhere(['resource'=>$resource->Resource_Id])->andWhere(['app_activity'=>$estimateresource->activity_id])->andWhere(['delete_status'=>0])->andWhere(['project_id'=>$estimateresource->project_id])->one(); //activity wise 
                                        $cart = cart::find()->where(['Vendor_Id'=>$resource->Vendor_Id])->andWhere(['Resource_Id'=>147])->andWhere(['status'=>0])->andWhere(['Project'=>$estimateresource->project_id])->andWhere(['rate'=>$estimateresource->rate])->andWhere(['reorder_cart'=> NULL])->one();

                                        $Jobcardd_1=Jobcard::find()->where(['vendor_id'=>$resource->Vendor_Id])->andWhere(['resource'=>$resource->Resource_Id])->andWhere(['app_activity'=>$estimateresource->activity_id])->andWhere(['delete_status'=>0])->andWhere(['project_id'=>$estimateresource->project_id])->one();

                                        $cart_1 = cart::find()->where(['Vendor_Id'=>$resource->Vendor_Id])->andWhere(['Resource_Id'=>$resource->Resource_Id])->andWhere(['status'=>0])->andWhere(['Project'=>$estimateresource->project_id])->one();

                                        // If we edit resource after receiving
                                        
                                        $jobcardeditres = Jobcard::find()->where(['vendor_id'=>$resource->Vendor_Id])->andWhere(['resource'=>$resource->Resource_Id])->andWhere(['delete_status'=>0])->andWhere(['project_id'=>$estimateresource->project_id])->andWhere(['cart_status'=>1])->one(); 
                                        $carteditres = cart::find()->where(['Vendor_Id'=>$resource->Vendor_Id])->andWhere(['Resource_Id'=>$resource->Resource_Id])->andWhere(['status'=>1])->andWhere(['Project'=>$estimateresource->project_id])->one();
                                       
                                       
                                        if(empty($Jobcardd))
                                        {
                                            
                                            $sql="SELECT name FROM jobcard ORDER BY name DESC limit 1";
                                            $command=$connection->createCommand($sql);
                                            $dataReader=$command->query();
                                            $no=$dataReader->read();
                                            $name=$no['name'] + 1;
                                            $groupid=time();

                                            $processid = $estimateresource->process_Id;

                                            $activity = $wbsactivity['activity_Name'];

                                            $project_id = $projuser->projectid;

                                            $resource_id = $estimateresource->resource_Id;

                                            $res = Resources::find()->where(['Resource_Id'=>$resource_id])->one();

                                            $resource_unit = $res->Unit;

                                            $estimateact=PricingEstimateNew::find()->where(['project_Id'=>$projuser->projectid])->andWhere(['activity_Id'=>$wbsactivity['id']])->andWhere(['pricing_status' => 0])->one();


                                            if($estimateact->activity_qty!=0){

                                                $wrkact_qty = $estimateact->activity_qty;

                                            }
                                            else{
                                                $wrkact_qty = 1;
                                            }

                                            $resource_qty = $estimateresource->quantity * $wrkact_qty;

                                            $resource_rate = $estimateresource->rate;

                                            $activity_id = $wbsactivity['id'];

                                            $iow_id = $wbsactivity['wbs_id'];

                                            $vendor_id = $res->Vendor_Id;

                                            $Jobcard = New Jobcard();
                                            $Jobcard->name= $name;
                                            $Jobcard->date= date('Y-m-d');
                                            $Jobcard->process= $processid;
                                            $Jobcard->activity= $activity;
                                            $Jobcard->groupid= $groupid;
                                            $Jobcard->project_id= $project_id;
                                            $Jobcard->resource= $resource_id;
                                            
                                            $Jobcard->res_quantity= $resource_qty;
                                            $Jobcard->res_rate= $resource_rate;
                                            $Jobcard->est_resqty= $resource_qty;
                                            $Jobcard->vendor_id= $vendor_id;
                                            $Jobcard->user_id= $uid;
                                            $Jobcard->app_activity= $activity_id;
                                            $Jobcard->iow= $iow_id;
                                            $Jobcard->status= 1;
                                            //$Jobcard->save(false);
                                            $Jobcard->pricing_resourceid= $estimateresource->pricing_resourceid;
                                            /* if($estimateresource->resourcetype_Id == 26){
                                                $Jobcard->unit = 'Nos';
                                            }else{ */
                                                $Jobcard->unit= $resource_unit;
                                           // }

                                            //if($estimateresource->resourcetype_Id != 24 && $estimateresource->resourcetype_Id != 26)
                                            /* if($estimateresource->resourcetype_Id != 26)
                                            { */
                                                if($Jobcard->save(false)):
                                                $nwgroupid = time();
                                                $Jobcard->groupid= $Jobcard->job_id.$Jobcard->job_id;
                                                $Jobcard->save(false);

                                                $model=New Cart();

                                                $model->Job_Id= $Jobcard->job_id;
                                                $model->Vendor_Id= $Jobcard->vendor_id;
                                                $model->ResourceType_Id= $res->ResourceType_Id;
                                                $model->Resource_Id= $Jobcard->resource;
                                                
                                                $model->rate= $resource_rate;
                                                $model->Qnty= $resource_qty;
                                                $model->EstresQnty= $resource_qty;
                                                $amount = $resource_rate * $resource_qty;
                                                $model->amount= $amount;
                                                $model->Project= $Jobcard->project_id;
                                                $model->groupid= $Jobcard->groupid;
                                                if($estimateresource->no_of_men){
                                                    $model->no_workers=$estimateresource->no_of_men;
                                                }
                                                if($estimateresource->no_of_days){
                                                    $model->no_days=$estimateresource->no_of_days;
                                                }

                                                if($estimateresource->resourcetype_Id == 26){
                                                    $model->fuel_status = 1;
                                                    $model->unit= 'Nos';

                                                }else{
                                                    $model->fuel_status = 0;
                                                    $model->unit= $Jobcard->unit;
                                                }

                                                $model->pricing_resourceid= $estimateresource->pricing_resourceid;                         
                                                $model->save(false);

                                                $Jobcard->cart_status=1;
                                                $Jobcard->save(false);

                                            endif;

                                            $rescount=1;

                                        }
                                        elseif(!empty($Jobcardd) && !empty($cart)){
                                           
                                            $cart = cart::find()->where(['Vendor_Id'=>$resource->Vendor_Id])->andWhere(['Resource_Id'=>$resource->Resource_Id])->andWhere(['status'=>0])->andWhere(['Project'=>$estimateresource->project_id])->andWhere(['rate'=>$estimateresource->rate])->andWhere(['Job_Id'=>$Jobcardd->job_id])->one();
                                            
                                            $estimateress=PricingEstimateResourcesNew::find()->where(['resource_Id'=>$estimateresource->resource_Id])->andWhere(['project_id'=>$estimateresource->project_id])->andWhere(['pricing_status'=>0])->all();
                                            //$estimateress=PricingEstimateResourcesNew::find()->where(['resource_Id'=>$estimateresource->resource_Id])->andWhere(['project_id'=>$estimateresource->project_id])->andWhere(['activity_id'=>$estimateresource->activity_id])->andWhere(['pricing_status'=>0])->all();//activity wise
                                           //$estimateress=PricingEstimateResourcesNew::find()->where(['resource_Id'=>$estimateresource->resource_Id])->andWhere(['project_id'=>$estimateresource->project_id])->andWhere(['pricing_status'=>0])->all();
                                            $totqty = 0;
                                           // $resourcee_rate = 0;
                                            $tot_f_qty = 0;
                                           // $fuelrate = 0;

                                            foreach($estimateress as $estimateres){

                                                $estimateact=PricingEstimateNew::find()->where(['project_Id'=>$projuser->projectid])->andWhere(['activity_Id'=>$estimateres->activity_id])->andWhere(['pricing_status' => 0])->one();

                                                if($estimateact['activity_qty']!=0){

                                                    $wrkact_qty = $estimateact->activity_qty;

                                                }
                                                else{
                                                    $wrkact_qty = 1;
                                                }

                                                $qty = $estimateres->quantity * $wrkact_qty;

                                                $totqty = $totqty + $qty;

                                                //$resourcee_rate = $resourcee_rate + $estimateres->rate;
                                                $resourcee_rate =$estimateres->rate;

                                            }

                                            $countmachine = count($estimateress);

                                            //if($countmachine !=0){
                                                //$resource_rate = $resourcee_rate/$countmachine;
                                                $resource_rate = $resourcee_rate;
                                            //}
                                            
                                       
                                            $Jobcardd->res_quantity = $totqty;
                                            $Jobcardd->res_rate = $resource_rate;
                                            $Jobcardd->est_resqty = $totqty;
                                            $Jobcardd->status = 1;

                                            //if($estimateresource->resourcetype_Id != 24 && $estimateresource->resourcetype_Id != 26)
                                           // if($estimateresource->resourcetype_Id != 26)
                                             //{
                                                if($Jobcardd->save(false)):
                                                    
                                                    if($cart)
                                                    {
                                                        $cart->rate= $resource_rate;
                                                        $cart->Qnty= $totqty;
                                                        $cart->EstresQnty=$totqty;
                                                        $amount = $resource_rate * $totqty;
                                                        $cart->amount= $amount;
                                                        
                                                        if($estimateresource->no_of_men){
                                                            $cart->no_workers=$estimateresource->no_of_men;
                                                        }
                                                        if($estimateresource->no_of_days){
                                                            $cart->no_days=$estimateresource->no_of_days;
                                                        }

                                                        if($estimateresource->resourcetype_Id == 26){
                                                            $cart->fuel_status = 1;
                                                        }else{
                                                            $cart->fuel_status = 0;
                                                        }

                                                        $cart->save(false);

                                                        $Jobcardd->cart_status=1;
                                                        $Jobcardd->save(false);
                                                        
                                                    }
                                             
                                                    

                                                endif;

                                             //}

                                            $rescount=1;
                                        }elseif(!empty($jobcardeditres) && !empty($carteditres))   // editing resource of received partially or fully
                                        { 
                                           
                                            /* if($estimateresource->quantity > $estimateresource->actual_qty)
                                            { */
                                               
                                                $estimateact=PricingEstimateNew::find()->where(['project_Id'=>$projuser->projectid])->andWhere(['activity_Id'=>$estimateresource->activity_id])->andWhere(['pricing_status' => 0])->one();

                                                if($estimateact['activity_qty']!=0){

                                                    $wrkact_qty = $estimateact->activity_qty;

                                                }
                                                else{
                                                    $wrkact_qty = 1;
                                                }

                                                
                                                
                                                $sqle = "SELECT a.status as status,a.qnty as qnty,a.order_id as order_id,a.order_res_id as order_res_id  FROM `ordered_resource` as a INNER JOIN `orders` as b ON a.order_id=b.order_id WHERE a.order_res_id=".$jobcardeditres->order_resid." AND b.project_id=".$projuser->projectid." ";
                                                $command = $connection->createCommand($sqle);
                                                $dataReader1 = $command->query();
                                                $order_res = $dataReader1->read();
                                                
                                                if($order_res)
                                                {
                                                   
                                                    //if($order_res['status'] == 2)
                                                    if($order_res['status'] == 2 || $order_res['status'] == 3)
                                                    {
                                                        
                                                        
                                                        $orderdqnty = $order_res['qnty'];

                                                        $invoices = InvoiceResources::find()->where(['order_id'=>$order_res['order_id']])->all();
                                                        
                                                        if($invoices) 
                                                        {

                                                            $receive_qty = 0;
                                                            foreach($invoices as $invoice)
                                                            {
                                                                $receive_qty = $receive_qty + $invoice->resource_qty;
                                                            }
                                                        
                                                            
                                                            $newpricingestimate_qnty = $wrkact_qty * $estimateresource->quantity;

                                                            $alljobcards = Jobcard::find()->where(['resource' => $jobcardeditres->resource])->andWhere(['cart_status' => 1])->andWhere(['delete_status' => 0])->andWhere(['project_id' =>$estimateresource->project_id])->all();


                                                            $oldissueslipstotal = 0;

                                                            foreach($alljobcards as $alljobcard):

                                                                $jobplaceorderes = PlaceorderRes::find()->where(['Job_Id' => $alljobcard->job_id])->andwhere(['Resource_Id' =>$alljobcard->resource])->andWhere(['Project' =>$alljobcard->project_id])->one();

                                                                if($jobplaceorderes){

                                                                    $oldissueslipstotal = $oldissueslipstotal + $jobplaceorderes->req_qty;

                                                                }

                                                            endforeach;

                                                            //echo $carteditres->EstresQnty.'<br>'.$oldissueslipstotal;
                                                            $balance1 = $carteditres->EstresQnty - $oldissueslipstotal;
                                                            
                                                            $balance = round($balance1, 2);

                                                            //echo $balance;exit;
                                                            $estimateress=PricingEstimateResourcesNew::find()->where(['resource_Id'=>$estimateresource->resource_Id])->andWhere(['project_id'=>$estimateresource->project_id])->andWhere(['pricing_status'=>0])->all();
                                                            $totqty = 0;
                                                            foreach($estimateress as $estimateres){
                                                                $estimateact=PricingEstimateNew::find()->where(['project_Id'=>$projuser->projectid])->andWhere(['activity_Id'=>$estimateres->activity_id])->andWhere(['pricing_status' => 0])->one();

                                                                if($estimateact['activity_qty']!=0){

                                                                    $wrkact_qty = $estimateact->activity_qty;

                                                                }
                                                                else{
                                                                    $wrkact_qty = 1;
                                                                }

                                                                $qty = $estimateres->quantity * $wrkact_qty;

                                                                $totqty = $totqty + $qty;
                                                            }  


                                                            //$pricingresource = PlaceorderRes::find()->where(['Job_Id'=>$jobcardeditres->job_id])->one();
                                                           

                                                            $connection = Yii::$app->db;
                                                            $sqlre = "SELECT a.*,b.status FROM jobcard as a INNER JOIN cart as b ON a.job_id = b.Job_Id WHERE b.reorder_cart=1 AND a.resource=".$jobcardeditres->resource." AND a.project_id=".$projuser->projectid." AND b.status=0 ORDER BY a.job_id DESC";
                                                            $command = $connection->createCommand($sqlre);
                                                            $dataReader = $command->query();
                                                            $repetedjobs = $dataReader->read();
                                                                

                                                            //echo '<br>'.$balance;
                                                            //if(($balance != 0 && $balance>0) || ($balance == 0 && $repetedjobs['status']==0) )
                                                        if($repetedjobs)
                                                        {  
                                                            if(($balance != 0) || ($balance == 0 && $repetedjobs['status']==0) )
                                                            {
                                                                
                                                                $jobcardeditres->est_resqty = $totqty;
                                                                $jobcardeditres->save(false);

                                                                
                                                                $carteditres->EstresQnty = $totqty;

                                                                
                                                                $carteditres->save(false);



                                                                
    
                                                                if($repetedjobs)
                                                                { 

                                                                    //$newbalqty = $estimateresource->quantity - $estimateresource->actual_qty;

                                                                    $jobcardre = Jobcard::findOne($repetedjobs['job_id']);
                                                                    $jobcardre->est_resqty = $totqty;
                                                                    $jobcardre->save(false);
    
                                                                    $cartrep = Cart::find()->where(['Job_Id'=>$repetedjobs['job_id']])->one();
                                                                    $cartrep->EstresQnty = $totqty;
    
                                                                    $amount = $cartrep->Qnty * $cartrep->rate;
    
                                                                    $cartrep->amount = $amount;
                                                                    $cartrep->save(false);
    
                                                                }
                                                            
                                                                


                                                                
                                                            }else{
                                                               // echo $totqty-$jobcardeditres->est_resqty;exit;
                                                               
                                                               if($sfno == 1 && $sno==1)
                                                               {echo $jobcardeditres->resource;
                                                                $sql="SELECT name FROM jobcard ORDER BY name DESC limit 1";
                                                                $command=$connection->createCommand($sql);
                                                                $dataReader=$command->query();
                                                                $no=$dataReader->read();
                                                                $name=$no['name'] + 1;
                                                                $groupid=time();

                                                                $processid = $estimateresource->process_Id;

                                                                $activity = $wbsactivity['activity_Name'];

                                                                $project_id = $projuser->projectid;

                                                                $resource_id = $estimateresource->resource_Id;

                                                                $res = Resources::find()->where(['Resource_Id'=>$resource_id])->one();

                                                                $resource_unit = $res->Unit;

                                                                $estimateact=PricingEstimateNew::find()->where(['project_Id'=>$projuser->projectid])->andWhere(['activity_Id'=>$wbsactivity['id']])->andWhere(['pricing_status' => 0])->one();


                                                                if($estimateact->activity_qty!=0){

                                                                    $wrkact_qty = $estimateact->activity_qty;

                                                                }
                                                                else{
                                                                    $wrkact_qty = 1;
                                                                }

                                                                

                                                                $resource_rate = $estimateresource->rate;

                                                                $activity_id = $wbsactivity['id'];

                                                                $iow_id = $wbsactivity['wbs_id'];

                                                                $vendor_id = $res->Vendor_Id;
                                                            
                                                                $newpricingestimate_qnty = $wrkact_qty * $estimateresource->quantity;
                                                                
                                                               /*  if($receive_qty < $estimateresource->quantity)
                                                                { */
                                                                    
                                                                        //echo $totqty.'<br>'.$repetedjobs['est_resqty'];exit;
                                                                        $remaining_qty =  ($totqty-$repetedjobs['est_resqty']);
                                                                    
                                                                        $Jobcard = New Jobcard();
                                                                        $Jobcard->name= $name;
                                                                        $Jobcard->date= date('Y-m-d');
                                                                        $Jobcard->process= $processid;
                                                                        $Jobcard->activity= $activity;
                                                                        $Jobcard->groupid= $groupid;
                                                                        $Jobcard->project_id= $project_id;
                                                                        $Jobcard->resource= $resource_id;
                                                                        
                                                                        $Jobcard->res_quantity= $remaining_qty;
                                                                        $Jobcard->res_rate= $resource_rate;
                                                                        $Jobcard->est_resqty= $remaining_qty;
                                                                        $Jobcard->vendor_id= $vendor_id;
                                                                        $Jobcard->user_id= $uid;
                                                                        $Jobcard->app_activity= $activity_id;
                                                                        $Jobcard->iow= $iow_id;
                                                                        $Jobcard->status= 1;
                                                                        $Jobcard->pricing_resourceid= $estimateresource->pricing_resourceid;
                                                                        $Jobcard->unit= $resource_unit;
                                                                        
                                                                        if($Jobcard->save(false)):
                                                                        
                                                                            $nwgroupid = time();
                                                                            $Jobcard->groupid= $Jobcard->job_id.$Jobcard->job_id;
                                                                            $Jobcard->save(false);

                                                                            $model1= New Cart();

                                                                            $model1->Job_Id= $Jobcard->job_id;
                                                                            $model1->Vendor_Id= $Jobcard->vendor_id;
                                                                            $model1->ResourceType_Id= $res->ResourceType_Id;
                                                                            $model1->Resource_Id= $Jobcard->resource;
                                                                            
                                                                            $model1->rate= $resource_rate;
                                                                            $model1->Qnty= $Jobcard->res_quantity;
                                                                            $model1->EstresQnty= $Jobcard->res_quantity;
                                                                            $amount = $resource_rate * $Jobcard->res_quantity;
                                                                            $model1->amount= $amount;
                                                                            $model1->Project= $Jobcard->project_id;
                                                                            $model1->groupid= $Jobcard->groupid;
                                                                            if($estimateresource->no_of_men){
                                                                                $model1->no_workers=$estimateresource->no_of_men;
                                                                            }
                                                                            if($estimateresource->no_of_days){
                                                                                $model1->no_days=$estimateresource->no_of_days;
                                                                            }

                                                                            if($estimateresource->resourcetype_Id == 26){
                                                                                $model1->fuel_status = 1;
                                                                                $model1->unit= 'Nos';

                                                                            }else{
                                                                                $model1->fuel_status = 0;
                                                                                $model1->unit= $Jobcard->unit;
                                                                            }

                                                                            $model1->pricing_resourceid= $estimateresource->pricing_resourceid;                         
                                                                            $model1->save(false);

                                                                            $Jobcard->cart_status=1;
                                                                            $Jobcard->save(false);

                                                                        endif;
                                                                    }
 
                                                                //}
                                                               
                                                            }
                                                        }
                                                        // else{
                                                             
                                                            
                                                           
                                                        //     //   if($sfno == 1 && $sno==1)
                                                        //     //   {//echo $jobcardeditres->resource.'</br>';
                                                        //         $sql="SELECT name FROM jobcard ORDER BY name DESC limit 1";
                                                        //         $command=$connection->createCommand($sql);
                                                        //         $dataReader=$command->query();
                                                        //         $no=$dataReader->read();
                                                        //         $name=$no['name'] + 1;
                                                        //         $groupid=time();

                                                        //         $processid = $estimateresource->process_Id;

                                                        //         $activity = $wbsactivity['activity_Name'];

                                                        //         $project_id = $projuser->projectid;

                                                        //         $resource_id = $estimateresource->resource_Id;

                                                        //         $res = Resources::find()->where(['Resource_Id'=>$resource_id])->one();

                                                        //         $resource_unit = $res->Unit;

                                                        //         $estimateact=PricingEstimateNew::find()->where(['project_Id'=>$projuser->projectid])->andWhere(['activity_Id'=>$wbsactivity['id']])->andWhere(['pricing_status' => 0])->one();


                                                        //         if($estimateact->activity_qty!=0){

                                                        //             $wrkact_qty = $estimateact->activity_qty;

                                                        //         }
                                                        //         else{
                                                        //             $wrkact_qty = 1;
                                                        //         }

                                                                

                                                        //         $resource_rate = $estimateresource->rate;

                                                        //         $activity_id = $wbsactivity['id'];

                                                        //         $iow_id = $wbsactivity['wbs_id'];

                                                        //         $vendor_id = $res->Vendor_Id;
                                                            
                                                        //         $newpricingestimate_qnty = $wrkact_qty * $estimateresource->quantity;
                                                                
                                                        //       /*  if($receive_qty < $estimateresource->quantity)
                                                        //         { */
                                                                    
                                                        //                 //echo $totqty.'<br>'.$repetedjobs['est_resqty'];exit;
                                                        //                 $remaining_qty =  ($totqty-$repetedjobs['est_resqty']);
                                                                    
                                                        //                 $Jobcard = New Jobcard();
                                                        //                 $Jobcard->name= $name;
                                                        //                 $Jobcard->date= date('Y-m-d');
                                                        //                 $Jobcard->process= $processid;
                                                        //                 $Jobcard->activity= $activity;
                                                        //                 $Jobcard->groupid= $groupid;
                                                        //                 $Jobcard->project_id= $project_id;
                                                        //                 $Jobcard->resource= $resource_id;
                                                                        
                                                        //                 $Jobcard->res_quantity= $remaining_qty;
                                                        //                 $Jobcard->res_rate= $resource_rate;
                                                        //                 $Jobcard->est_resqty= $remaining_qty;
                                                        //                 $Jobcard->vendor_id= $vendor_id;
                                                        //                 $Jobcard->user_id= $uid;
                                                        //                 $Jobcard->app_activity= $activity_id;
                                                        //                 $Jobcard->iow= $iow_id;
                                                        //                 $Jobcard->status= 1;
                                                        //                 $Jobcard->pricing_resourceid= $estimateresource->pricing_resourceid;
                                                        //                 $Jobcard->unit= $resource_unit;
                                                                        
                                                        //                 if($Jobcard->save(false)):
                                                                        
                                                        //                     $nwgroupid = time();
                                                        //                     $Jobcard->groupid= $Jobcard->job_id.$Jobcard->job_id;
                                                        //                     $Jobcard->save(false);

                                                        //                     $model1= New Cart();

                                                        //                     $model1->Job_Id= $Jobcard->job_id;
                                                        //                     $model1->Vendor_Id= $Jobcard->vendor_id;
                                                        //                     $model1->ResourceType_Id= $res->ResourceType_Id;
                                                        //                     $model1->Resource_Id= $Jobcard->resource;
                                                                            
                                                        //                     $model1->rate= $resource_rate;
                                                        //                     $model1->Qnty= $Jobcard->res_quantity;
                                                        //                     $model1->EstresQnty= $Jobcard->res_quantity;
                                                        //                     $amount = $resource_rate * $Jobcard->res_quantity;
                                                        //                     $model1->amount= $amount;
                                                        //                     $model1->Project= $Jobcard->project_id;
                                                        //                     $model1->groupid= $Jobcard->groupid;
                                                        //                     if($estimateresource->no_of_men){
                                                        //                         $model1->no_workers=$estimateresource->no_of_men;
                                                        //                     }
                                                        //                     if($estimateresource->no_of_days){
                                                        //                         $model1->no_days=$estimateresource->no_of_days;
                                                        //                     }

                                                        //                     if($estimateresource->resourcetype_Id == 26){
                                                        //                         $model1->fuel_status = 1;
                                                        //                         $model1->unit= 'Nos';

                                                        //                     }else{
                                                        //                         $model1->fuel_status = 0;
                                                        //                         $model1->unit= $Jobcard->unit;
                                                        //                     }

                                                        //                     $model1->pricing_resourceid= $estimateresource->pricing_resourceid;                         
                                                        //                     $model1->save(false);

                                                        //                     $Jobcard->cart_status=1;
                                                        //                     $Jobcard->save(false);

                                                        //                 endif;
                                                        //             //}
                                                        // }
                                                            

                                                        }
                                                        $rescount=1;
                                                    
        
                                                    }elseif($order_res['status'] == 3)  // if completely received new order will be generated
                                                    { 
                                                        
                                                        $sql="SELECT name FROM jobcard ORDER BY name DESC limit 1";
                                                        $command=$connection->createCommand($sql);
                                                        $dataReader=$command->query();
                                                        $no=$dataReader->read();
                                                        $name=$no['name'] + 1;
                                                        $groupid=time();

                                                        $processid = $estimateresource->process_Id;

                                                        $activity = $wbsactivity['activity_Name'];

                                                        $project_id = $projuser->projectid;

                                                        $resource_id = $estimateresource->resource_Id;

                                                        $res = Resources::find()->where(['Resource_Id'=>$resource_id])->one();

                                                        $resource_unit = $res->Unit;

                                                        $estimateact=PricingEstimateNew::find()->where(['project_Id'=>$projuser->projectid])->andWhere(['activity_Id'=>$wbsactivity['id']])->andWhere(['pricing_status' => 0])->one();


                                                        if($estimateact->activity_qty!=0){

                                                            $wrkact_qty = $estimateact->activity_qty;

                                                        }
                                                        else{
                                                            $wrkact_qty = 1;
                                                        }

                                                        

                                                        $resource_rate = $estimateresource->rate;

                                                        $activity_id = $wbsactivity['id'];

                                                        $iow_id = $wbsactivity['wbs_id'];

                                                        $vendor_id = $res->Vendor_Id;
                                                       
                                                        $newpricingestimate_qnty = $wrkact_qty * $estimateresource->quantity;

                                                        $cinvoices = InvoiceResources::find()->where(['order_id'=>$order_res['order_id']])->all();
                                                        $receive_qty = 0;
                                                        if($cinvoices)
                                                        {
                                                            foreach($cinvoices as $invoice)
                                                            {
                                                                $receive_qty = $receive_qty+$invoice->resource_qty;  // received qty

                                                            }
                                                                
                                                            $remqty = $newpricingestimate_qnty - $receive_qty;echo $newpricingestimate_qnty.'<br>';
                                                        }

                                                        // $Jobcard = New Jobcard();
                                                        // $Jobcard->name= $name;
                                                        // $Jobcard->date= date('Y-m-d');
                                                        // $Jobcard->process= $processid;
                                                        // $Jobcard->activity= $activity;
                                                        // $Jobcard->groupid= $groupid;
                                                        // $Jobcard->project_id= $project_id;
                                                        // $Jobcard->resource= $resource_id;
                                                        
                                                        // $Jobcard->res_quantity= $remqty;
                                                        // $Jobcard->res_rate= $resource_rate;
                                                        // $Jobcard->est_resqty= $remqty;
                                                        // $Jobcard->vendor_id= $vendor_id;
                                                        // $Jobcard->user_id= $uid;
                                                        // $Jobcard->app_activity= $activity_id;
                                                        // $Jobcard->iow= $iow_id;
                                                        // $Jobcard->status= 1;
                                                        // $Jobcard->pricing_resourceid= $estimateresource->pricing_resourceid;
                                                        // $Jobcard->unit= $resource_unit;
                                                        
                                                        // if($Jobcard->save(false)):

                                                        //     $nwgroupid = time();
                                                        //     $Jobcard->groupid= $Jobcard->job_id.$Jobcard->job_id;
                                                        //     $Jobcard->save(false);

                                                        //     $model=New Cart();

                                                        //     $model->Job_Id= $Jobcard->job_id;
                                                        //     $model->Vendor_Id= $Jobcard->vendor_id;
                                                        //     $model->ResourceType_Id= $res->ResourceType_Id;
                                                        //     $model->Resource_Id= $Jobcard->resource;
                                                            
                                                        //     $model->rate= $resource_rate;
                                                        //     $model->Qnty= $remqty;
                                                        //     $model->EstresQnty= $remqty;
                                                        //     $amount = $resource_rate * $remqty;
                                                        //     $model->amount= $amount;
                                                        //     $model->Project= $Jobcard->project_id;
                                                        //     $model->groupid= $Jobcard->groupid;
                                                        //     if($estimateresource->no_of_men){
                                                        //         $model->no_workers=$estimateresource->no_of_men;
                                                        //     }
                                                        //     if($estimateresource->no_of_days){
                                                        //         $model->no_days=$estimateresource->no_of_days;
                                                        //     }

                                                        //     if($estimateresource->resourcetype_Id == 26){
                                                        //         $model->fuel_status = 1;
                                                        //         $model->unit= 'Nos';

                                                        //     }else{
                                                        //         $model->fuel_status = 0;
                                                        //         $model->unit= $Jobcard->unit;
                                                        //     }

                                                        //     $model->pricing_resourceid= $estimateresource->pricing_resourceid;                         
                                                        //     $model->save(false);

                                                        //     $Jobcard->cart_status=1;
                                                        //     $Jobcard->save(false);

                                                        // endif;


                                                        $rescount=1;

                                                    }

                                                }
                                            //}

                                        }
                                    }
                                    

                                endforeach;

                            }

                        endforeach;

                    }

                endforeach;

            }

        }

        if($rescount==1){
            $arr = array('error' => 'No');
        }
        else{
            $arr = array('error' => 'Yes');
        }
        
        return json_encode($arr);


    }



    public function actionReceivepurchaseordercheck()
    {
        

        $connection = \Yii::$app->db;

        $estres = $_POST['estres'];

        $pricingestimateres=PricingEstimateResourcesNew::findOne($estres);

        $resource = Resources::findOne($pricingestimateres->resource_Id);

        $vendorid = $resource->Vendor_Id;

        $sql = "SELECT *  FROM `resources` WHERE Vendor_Id ='".$vendorid."' AND pricing_status=0";
        $command = $connection->createCommand($sql);
        $dataReader = $command->query();
        $stockresitems = $dataReader->readAll();
        $stresids='';
        if(count($stockresitems)>0):
            foreach ($stockresitems as $key2 => $stockresitem) {
                if($key2==0):
                    $stresids.=$stockresitem['Resource_Id'];
                else:
                    $stresids.=",".$stockresitem['Resource_Id'];
                endif;
            }
        endif;

        $sql2 = "SELECT *  FROM `resources` WHERE `Name` LIKE '".$resource->Name."' AND pricing_status=0";
        $command = $connection->createCommand($sql2);
        $dataReader = $command->query();
        $resnames = $dataReader->readAll();
        $resnameids='';
        if(count($resnames)>0):
            foreach ($resnames as $key3 => $resname) {
                if($key3==0):
                    $resnameids.=$resname['Resource_Id'];
                else:
                    $resnameids.=",".$resname['Resource_Id'];
                endif;
            }
        endif;
        $type =0;
        $invoices=Invoice::find()->where(['project_id' => $pricingestimateres->project_id])->all();

        $receivedres = 0; 

        foreach($invoices as $invoice):

            $invoiceres=InvoiceResources::find()->where(['invoice_id' => $invoice->invoice_id])->andWhere('resource_id IN('.$stresids.')')->all();

            //$invoiceres2=InvoiceResources::find()->where(['invoice_id' => $invoice->invoice_id])->andWhere('resource_id IN('.$resnameids.')')->all();

            $invoiceres2=InvoiceResources::find()->where(['invoice_id' => $invoice->invoice_id])->andWhere(['resource_id' =>$pricingestimateres->resource_Id])->all();

          /*  if($invoiceres){
                $receivedres++; 

            }
            else{*/
                
                if($invoiceres2){

                    foreach($invoiceres2 as $invoiceres):

                        $orderresmodel=OrderedResource::find()->Where(['order_id'=> $invoiceres->order_id])->andWhere(['resource_id'=> $invoiceres->resource_id])->one();

                        $jobcard=Jobcard::findOne($orderresmodel->jobcard_id); 
                        $orders = Orders::findOne($orderresmodel->order_id);
                        $type = $orders->order_type;

                        //if($jobcard->app_activity==$pricingestimateres->activity_id){
                        if($jobcard->pricing_resourceid == $pricingestimateres->pricing_resourceid){
                            $receivedres++;
                        }


                    endforeach;

                }
            //}

        endforeach;

        $arr = array('error' => 'No', 'receivedres'=>$receivedres,'type'=>$type);      
        return json_encode($arr);


    }

    public function actionResourceauditlog()
    {

        $allaudits=PlaceorderResAudit::find()->all();
        $counts = count($allaudits);
        $scndcount = $counts+1;

        $datarows='';

        foreach($allaudits as $key => $allaudit):

            $estimateres =PricingEstimateResourcesNew::findOne($allaudit->pricing_resourceid);


            $resource=Resources::findOne($estimateres->resource_Id);
            

            $restype =Resourcetype::findOne($resource->ResourceType_Id);

            if($restype->Project_Type == 1){
                $order_type = "Purchase Order";
            }elseif($restype->Project_Type == 2){
                $order_type = "Work Order";
            }elseif($restype->Project_Type == 3){
                $order_type = "Direct Workers-Order";
            }elseif($restype->Project_Type == 4){
                $order_type = "Lease Order";
            }else{
                $order_type = "Despatch Order";
            }

            if($allaudit->added_by!=''){

                $action= 'Added';

                $date= date('d-m-Y',strtotime($allaudit->added_date));

                $userid= $allaudit->added_by;

            }

            if($allaudit->edited_by!=''){

                $action= 'Edited';

                $date= date('d-m-Y',strtotime($allaudit->edited_date));

                $userid= $allaudit->edited_by;

            }

            if($allaudit->deleted_by!=''){

                $action= 'Deleted';

                $date= date('d-m-Y',strtotime($allaudit->deleted_date));

                $userid= $allaudit->deleted_by;

            }
            
            $user=User::findOne($userid);

            $datarows.='<div>
                            <div style="margin-bottom:10px">'.($key+1).'.&nbsp;&nbsp;The&nbsp;&nbsp;'.$order_type.'&nbsp;&nbsp;of resource name&nbsp;&nbsp;'.$resource->Name.'&nbsp;&nbsp;has been '.$action.'&nbsp;&nbsp; by the user&nbsp;&nbsp;'.$user->username.'&nbsp;&nbsp; on &nbsp;&nbsp;'.$date.'</div>
                                                    
                        </div>';

        endforeach;

        $canclld_audits = PlaceorderStatus::find()->all();

        foreach($canclld_audits as $canclld_audit):
             $placrorderres =OrderedResource::find()->where(['order_id' => $canclld_audit->order_id])->one();
             


            $resourcename=Resources::findOne($placrorderres['resource_id']);

            $restypes =Resourcetype::findOne($resourcename['ResourceType_Id']);

            if($restypes['Project_Type'] == 1){
                $order_types = "Purchase Order";
            }elseif($restypes['Project_Type'] == 2){
                $order_types = "Work Order";
            }elseif($restypes['Project_Type'] == 3){
                $order_types = "Direct Workers-Order";
            }elseif($restypes['Project_Type'] == 4){
                $order_types = "Lease Order";
            }else{
                $order_types = "Despatch Order";
            }

             if($canclld_audit->cancelled_by!=''){

                $actions= 'Cancelled';

                $datec= date('d-m-Y',strtotime($canclld_audit->cancelled_date));

                $userid= $canclld_audit->cancelled_by;

            }
            if($canclld_audit->deleted_by!=''){

                $actions= 'Deleted';

                $datec= date('d-m-Y',strtotime($canclld_audit->deleted_date));

                $userid= $canclld_audit->deleted_by;

            }

                $users=User::findOne($userid);

                $datarows.='<div>

                                <div style="margin-bottom:10px">'.($scndcount++).'.&nbsp;&nbsp;The&nbsp;&nbsp;'.$order_types.'&nbsp;&nbsp;of resource name&nbsp;&nbsp;'.$resourcename['Name'].'&nbsp;&nbsp;has been '.$actions.'&nbsp;&nbsp; by the user&nbsp;&nbsp;'.$users->username.'&nbsp;&nbsp; on &nbsp;&nbsp;'.$datec.'</div>
                                                        
                            </div>';

        endforeach;

        $arr = array('error' => 'No', 'result'=>$datarows);
        return json_encode($arr);

    }

    public function actionUploadprojectfiles()
    {
        try {
            $projectId = (int)($_POST['project_id'] ?? 0);
            if (!$projectId) {
                return json_encode(['error' => 'Yes', 'errortext' => 'No project selected']);
            }
            $uploadDir = Yii::getAlias('@webroot') . '/uploads/projects/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $allowed = ['pdf','doc','docx','xls','xlsx','jpg','jpeg','png'];
            if (!empty($_FILES['pf_documents']['name'][0])) {
                foreach ($_FILES['pf_documents']['name'] as $i => $origName) {
                    $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
                    if (!in_array($ext, $allowed)) continue;
                    $storedName = uniqid('pf_') . '.' . $ext;
                    if (move_uploaded_file($_FILES['pf_documents']['tmp_name'][$i], $uploadDir . $storedName)) {
                        \Yii::$app->db->createCommand()->insert('project_files', [
                            'project_id'    => $projectId,
                            'file_type'     => 'documents',
                            'filename'      => $storedName,
                            'original_name' => $origName,
                            'uploaded_at'   => date('Y-m-d H:i:s'),
                        ])->execute();
                    }
                }
            }
            return json_encode(['error' => 'No']);
        } catch (\Exception $e) {
            return json_encode(['error' => 'Yes', 'errortext' => $e->getMessage()]);
        }
    }

}
 

