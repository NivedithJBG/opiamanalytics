<?php

namespace app\controllers;  

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use app\components\MetricsCacheHelper;
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

        return $this->render('index');

    }

    // Direct entry point for the unified site-engineer mobile app, independent
    // of device/account-type auto-detection (used for testing and can be
    // bookmarked/added-to-home-screen directly).
    public function actionSiteapp()
    {
        $this->layout = '@app/views/layouts/mobile';
        $user_id = Yii::$app->user->id;

        $projectName = 'No Project Selected';
        if($projuser = ProjuserSelection::find()->where(['userid' => $user_id])->one()){
            if($proj = Projects::findOne($projuser->projectid))
                $projectName = $proj->Name;
        }

        return $this->render('siteapp_home', ['projectName' => $projectName]);
    }

    // Standalone storekeeper-only mobile app — same shell as actionSiteapp()
    // but scoped to just Indents + GRN, for a separate home-screen bookmark
    // that doesn't expose M.Book/Report Progress.
    public function actionStorekeeperapp()
    {
        $this->layout = '@app/views/layouts/mobile';
        $user_id = Yii::$app->user->id;

        $projectName = 'No Project Selected';
        if($projuser = ProjuserSelection::find()->where(['userid' => $user_id])->one()){
            if($proj = Projects::findOne($projuser->projectid))
                $projectName = $proj->Name;
        }

        return $this->render('storekeeperapp_home', ['projectName' => $projectName]);
    }

    public function actionDashboard()
    {
        return $this->render('dashboard');
    }



    public function actionGetrestypelist()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $connection = \Yii::$app->db;
        $rows = $connection->createCommand(
            "SELECT ResourceType_Id AS id, Name AS name FROM resourcetype WHERE Status=0 ORDER BY sortorder ASC"
        )->queryAll();
        return ['items' => $rows];
    }

    public function actionGetresgrouplist()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $typeId = (int)\Yii::$app->request->post('type_id', 0);
        $sql = "SELECT Resource_group_Id AS id, Resource_group_Name AS name FROM resource_group WHERE status=0";
        if ($typeId) $sql .= " AND ResourceType_Id=" . $typeId;
        $sql .= " ORDER BY RG_sortorder ASC, Resource_group_Name ASC";
        $rows = \Yii::$app->db->createCommand($sql)->queryAll();
        return ['items' => $rows];
    }

    public function actionGetwbresources()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $typeId  = (int)\Yii::$app->request->post('type_id', 0);
        $groupId = (int)\Yii::$app->request->post('group_id', 0);
        if (!$typeId && !$groupId) return ['items' => []];
        $sql = "SELECT Resource_Id AS id, Name AS name, Unit AS unit, Price AS price
                FROM resources WHERE Status=0";
        if ($typeId)  $sql .= " AND ResourceType_Id=" . $typeId;
        if ($groupId) $sql .= " AND Resource_group_Id=" . $groupId;
        $sql .= " ORDER BY Name ASC";
        $rows = \Yii::$app->db->createCommand($sql)->queryAll();
        return ['items' => $rows];
    }

    public function actionGetactivityresources()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $activityId = (int)\Yii::$app->request->post('activity_id', 0);
        if(!$activityId) return ['items' => [], 'tasks' => [], 'unit' => '', 'rate' => 0, 'qty' => 0];
        $db = \Yii::$app->db;

        $activity = $db->createCommand(
            "SELECT activity_unit, activity_rate FROM estimateactivities WHERE activity_id = :aid LIMIT 1"
        )->bindValue(':aid', $activityId)->queryOne();

        $resources = $db->createCommand(
            "SELECT ar.estactres_id, ar.est_resource_id, ar.est_resource_rate, ar.est_resource_quantity, ar.est_resource_amount,
                    r.Name AS resource_name, r.Unit AS resource_unit, r.ResourceType_Id AS type_id, r.Resource_group_Id AS group_id
             FROM estactivity_resources ar
             JOIN resources r ON r.Resource_Id = ar.est_resource_id
             WHERE ar.estactivity_id = :aid AND ar.est_resource_status = 0
             ORDER BY ar.estactres_id ASC"
        )->bindValue(':aid', $activityId)->queryAll();

        $tasks = $db->createCommand(
            "SELECT id, task_name, task_unit, task_qty, productivity, resource_units AS resunits FROM activity_tasks WHERE activity_id = :aid ORDER BY sort_order ASC, id ASC"
        )->bindValue(':aid', $activityId)->queryAll();

        // estimate qty + schedule unit/qty for this project
        $uid      = \Yii::$app->user->Id;
        $projuser = \app\models\ProjuserSelection::find()->where(['userid' => $uid])->one();
        $pid      = $projuser ? (int)$projuser->projectid : 0;
        $estQty   = 0.0;
        $schUnit  = '';
        $schQty   = 0.0;
        if ($pid) {
            $peRow = $db->createCommand(
                "SELECT activity_qty FROM pricing_estimate_new
                 WHERE activity_Id = :a AND project_Id = :p AND pricing_status = 0 LIMIT 1",
                [':a' => $activityId, ':p' => $pid]
            )->queryOne();
            if ($peRow) $estQty = (float)$peRow['activity_qty'];

            // schedule unit and qty from workgroup_activities_new
            $wanRow = $db->createCommand(
                "SELECT cycle_Unit, cycle_Quantity FROM workgroup_activities_new
                 WHERE project_Id = :p AND activity_Id = :a LIMIT 1",
                [':p' => $pid, ':a' => $activityId]
            )->queryOne();
            if ($wanRow) {
                $schUnit = $wanRow['cycle_Unit']     ?? '';
                $schQty  = (float)($wanRow['cycle_Quantity'] ?? 0);
            }
        }

        // calculated rate from resources
        $calcRate = 0.0;
        foreach ($resources as $r) { $calcRate += (float)$r['est_resource_rate'] * (float)$r['est_resource_quantity']; }
        if (!$calcRate && $activity) $calcRate = (float)$activity['activity_rate'];

        return [
            'items'    => $resources,
            'tasks'    => $tasks,
            'unit'     => $activity ? $activity['activity_unit'] : '',
            'rate'     => round($calcRate, 2),
            'qty'      => $estQty,
            'sch_unit' => $schUnit,
            'sch_qty'  => $schQty,
        ];
    }

    public function actionGetwbtypelist()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $rows = \Yii::$app->db->createCommand(
            "SELECT estworktype_id AS id, estworktype_name AS name FROM estimateworktypes WHERE estworktype_status = 0 ORDER BY sortorder ASC, estworktype_name ASC"
        )->queryAll();
        return ['items' => $rows];
    }

    public function actionGetwbgrouplist()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $rows = \Yii::$app->db->createCommand(
            "SELECT activitytype_id AS id, activitytype_name AS name FROM estimateactivitytypes WHERE activitytype_status = 0 ORDER BY sortorder ASC, activitytype_name ASC"
        )->queryAll();
        return ['items' => $rows];
    }



    /* ── Library mini-popup endpoints ──────────────────────────────────── */



    public function actionGetactivitiesbytypeandgroup()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $typeId    = (int)\Yii::$app->request->post('typeId');
        $groupId   = (int)\Yii::$app->request->post('groupId');
        $iowGroupId = (int)\Yii::$app->request->post('iowGroupId');
        if (!$typeId && !$groupId && !$iowGroupId) return ['items' => []];
        $sql  = "SELECT ea.activity_id AS id, ea.activity_name AS name, ea.activity_unit AS unit
                 FROM estimateactivities ea
                 WHERE ea.activity_status = 0";
        if ($typeId)     $sql .= " AND ea.work_type = " . $typeId;
        if ($groupId)    $sql .= " AND ea.activity_type = " . $groupId;
        if ($iowGroupId) $sql .= " AND ea.iow_group_id = " . $iowGroupId;
        $sql .= " ORDER BY ea.activity_name ASC";
        $rows = \Yii::$app->db->createCommand($sql)->queryAll();

        return ['items' => $rows];
    }

    /**
     * actionGetiowgroupsbytype — IOW groups that have at least one active
     * activity of the given Project Type (estimateactivities.work_type).
     * Feeds the Create WBS "IOW Group" dropdown so it only lists groups
     * belonging to the selected type.
     */
    public function actionGetiowgroupsbytype()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $typeId = (int)\Yii::$app->request->post('typeId');
        if (!$typeId) return ['items' => []];
        $rows = \Yii::$app->db->createCommand(
            "SELECT g.id, g.name FROM iow_groups g
             JOIN iow_group_worktypes m ON m.iow_group_id = g.id
             WHERE g.status = 0 AND m.worktype_id = :typeId
             ORDER BY g.name ASC",
            [':typeId' => $typeId]
        )->queryAll();
        return ['items' => $rows];
    }

    /**
     * actionWbsaddiows — creates one or more bare IOWs (workgroups_new
     * rows) under a single IOW Group, with no activity/estimate/schedule
     * bar attached. This is the entry point for the simplified "Add
     * IOWs" popup that replaces the old WBS Quick Entry form — adding an
     * activity under an IOW is a separate, later step, not part of this
     * action.
     *
     * Reuses the exact same IOW-Group-resolve + workgroups_new-resolve-
     * or-create logic as actionWbssave() (see lines ~330-388), just
     * looped once per IOW name and with no activity-library step
     * following it.
     */
    public function actionWbsaddiows()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $uid      = Yii::$app->user->Id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return ['error' => 'No project selected'];
        $pid = (int)$projuser->projectid;
        $db  = \Yii::$app->db;

        $raw = \Yii::$app->request->post('payload');
        if (!$raw) return ['error' => 'No payload'];
        $p = json_decode($raw, true);
        if (!$p) return ['error' => 'Bad payload'];

        $iowGroupId   = (int)($p['iow_group_id']   ?? 0);
        $iowGroupName = trim($p['iow_group_name'] ?? '');
        $iowNames     = array_values(array_filter(array_map('trim', $p['iow_names'] ?? []), function ($n) {
            return $n !== '';
        }));

        if (!$iowGroupId && $iowGroupName === '') return ['error' => 'IOW Group is required'];
        if (empty($iowNames)) return ['error' => 'At least one IOW name is required'];

        // Resolve IOW group by ID (from dropdown) or fall back to creating one from name
        if (!$iowGroupId) {
            $igRow = $db->createCommand(
                "SELECT id FROM iow_groups WHERE name=:n AND project_id=:p AND status=0 LIMIT 1",
                [':n' => $iowGroupName, ':p' => $pid]
            )->queryOne();
            if ($igRow) {
                $iowGroupId = (int)$igRow['id'];
            } else {
                $db->createCommand(
                    "INSERT INTO iow_groups (name, project_id, status, primavera_id) VALUES (:n, :p, 0, 0)",
                    [':n' => $iowGroupName, ':p' => $pid]
                )->execute();
                $iowGroupId = (int)$db->lastInsertID;
            }
        }

        $now = date('Y-m-d H:i:s');
        $created = [];
        $skipped = [];

        foreach ($iowNames as $iowName) {
            $wgRow = $db->createCommand(
                "SELECT Workgroup_Id FROM workgroups_new WHERE Project_Id=:p AND iowGroupid=:g AND Name=:n AND Status=0 LIMIT 1",
                [':p' => $pid, ':g' => $iowGroupId, ':n' => $iowName]
            )->queryOne();
            if ($wgRow) {
                // Already exists under this IOW Group — skip rather than duplicate,
                // but still report its id/name so the caller can open it directly.
                $skipped[] = ['id' => (int)$wgRow['Workgroup_Id'], 'name' => $iowName];
                continue;
            }
            // Excludes the Overhead IOW group (17/18, pinned at sortorder
            // 999999 so it always sorts last in the Estimate report — see
            // ProjectsController::_seedProjectOverheadActivity) from this
            // MAX so real IOW groups keep growing sortorder from a normal
            // small range instead of jumping past 999999 forever.
            $nextSort = (int)($db->createCommand(
                "SELECT COALESCE(MAX(sortorder),0)+1 AS s FROM workgroups_new WHERE Project_Id=:p AND iowGroupid NOT IN (17, 18)", [':p' => $pid]
            )->queryOne()['s'] ?? 0);
            $db->createCommand(
                "INSERT INTO workgroups_new
                 (Project_Id, Name, iowGroupid, Status, sortorder, Added_On, Updated_On, Added_By,
                  start_date, end_date, duration, progress, open, parent, itemtype, worktype, worktypegroup,
                  wbs_estimate_id, pricing_status, notes, primavera_id)
                 VALUES (:p, :n, :g, 0, :s, :t, :t, :uid,
                  :t, :t, 0, 0, 0, 0, '', 0, 0, 0, 0, '', 0)",
                [':p' => $pid, ':n' => $iowName, ':g' => $iowGroupId, ':s' => $nextSort, ':t' => $now, ':uid' => $uid]
            )->execute();
            $created[] = ['id' => (int)$db->lastInsertID, 'name' => $iowName];
        }

        return ['error' => 'No', 'created' => $created, 'skipped' => $skipped];
    }

    /**
     * actionListiowbygroup — lists existing IOWs (workgroups_new rows) for
     * the current project, optionally filtered to one IOW Group. Feeds the
     * "Add Activity" tab's IOW dropdown — that tab only attaches activities
     * to IOWs that already exist, never creates new ones.
     */
    public function actionListiowbygroup()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $uid      = Yii::$app->user->Id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return ['error' => 'No project selected', 'items' => []];
        $pid = (int)$projuser->projectid;
        $db  = \Yii::$app->db;

        $iowGroupId = (int)(\Yii::$app->request->post('iow_group_id', 0));

        $sql = "SELECT Workgroup_Id AS id, Name AS name, iowGroupid FROM workgroups_new WHERE Project_Id=:p AND Status=0";
        $params = [':p' => $pid];
        if ($iowGroupId > 0) {
            $sql .= " AND iowGroupid=:g";
            $params[':g'] = $iowGroupId;
        }
        $sql .= " ORDER BY Name ASC";

        $rows = $db->createCommand($sql, $params)->queryAll();
        $items = array_map(function ($r) {
            return ['id' => (int)$r['id'], 'name' => $r['name'], 'iow_group_id' => (int)$r['iowGroupid']];
        }, $rows);

        return ['error' => 'No', 'items' => $items];
    }

    /**
     * actionWbsactivitiesbyiow — lists the activities already attached to
     * one IOW Group (scheduleactivities rows under it, for this project),
     * each with its tasks, for the "edit attached activities" popup opened
     * by clicking an existing IOW Group row/bar. Purpose-built read-only
     * endpoint — actionGanttactivities is shaped for Gantt-bar rendering
     * (progress/critical-path fields this popup has no use for), so a
     * lighter query is clearer than reusing/extending it.
     */
    public function actionWbsactivitiesbyiow()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $uid      = Yii::$app->user->Id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return ['error' => 'No project selected', 'items' => []];
        $pid        = (int)$projuser->projectid;
        $iowGroupId = (int)(\Yii::$app->request->post('iow_group_id', 0));
        if (!$iowGroupId) return ['error' => 'No iow_group_id', 'items' => []];
        $db = \Yii::$app->db;

        $rows = $db->createCommand(
            "SELECT sa.id, sa.name, sa.unit, sa.quantity, sa.duration, sa.activity_id AS wan_id, sa.resource_units AS r_unit
             FROM scheduleactivities sa
             WHERE sa.iow_group_id=:g AND sa.projectId=:p AND sa.status=0
             ORDER BY sa.sortorder ASC, sa.id ASC",
            [':g' => $iowGroupId, ':p' => $pid]
        )->queryAll();

        $items = [];
        foreach ($rows as $r) {
            $tasks = $db->createCommand(
                "SELECT stn.task_Id AS id, COALESCE(stn.task_name, at.task_name) AS name, COALESCE(stn.task_unit, at.task_unit) AS unit,
                        stn.task_qty AS qty, stn.task_productivity AS productivity, stn.task_resource_units AS resunits
                 FROM schedule_task_new stn
                 LEFT JOIN activity_tasks at ON at.id = stn.task_Id
                 WHERE stn.activity_Id=:sa AND stn.status=0
                 ORDER BY COALESCE(stn.sort_order, 999999), stn.id ASC",
                [':sa' => $r['id']]
            )->queryAll();
            // No task values saved on this Gantt activity yet: fall back to the
            // library's tasks for the same activity, so the Task popup and Save
            // use the library Qty/Productivity instead of an empty task (which
            // made the save fall back to a 1-day duration).
            if (!$tasks) {
                $tasks = $db->createCommand(
                    "SELECT at.id AS id, at.task_name AS name, at.task_unit AS unit, at.task_qty AS qty,
                            at.productivity AS productivity, at.resource_units AS resunits
                     FROM activity_tasks at
                     WHERE at.activity_id = (SELECT activity_Id FROM workgroup_activities_new WHERE id = :w)
                     ORDER BY at.sort_order ASC, at.id ASC",
                    [':w' => $r['wan_id']]
                )->queryAll();
            }
            $items[] = [
                'sa_id'    => (int)$r['id'],
                'name'     => $r['name'],
                'unit'     => $r['unit'],
                'quantity' => (float)$r['quantity'],
                'duration' => (int)$r['duration'],
                'wan_id'   => (int)$r['wan_id'],
                'r_unit'   => (float)$r['r_unit'] ?: 1.0,
                'tasks'    => array_map(function ($t) {
                    return [
                        'id' => (int)$t['id'], 'name' => $t['name'], 'qty' => (float)$t['qty'],
                        'productivity' => (float)$t['productivity'], 'resunits' => (float)$t['resunits'],
                    ];
                }, $tasks),
            ];
        }

        return ['error' => 'No', 'items' => $items];
    }

    /**
     * actionWbsactivityupdate — edits Qty/Task on an activity ALREADY
     * attached to an IOW (a live scheduleactivities row). actionWbsadd()
     * cannot be reused for this: it no-ops on scheduleactivities whenever
     * a bar already exists for the wan_id (see the already_exists early
     * return there) — this action is the direct, targeted update that
     * path is missing. Same schedule_task_new delete+reinsert mirroring
     * convention actionWbssave() already uses when editing an activity's
     * WBS values (kept in sync with the Progress Report screen, which
     * reads tasks from schedule_task_new, not activity_tasks).
     */
    public function actionWbsactivityupdate()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $uid      = Yii::$app->user->Id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return ['error' => 'No project selected'];
        $pid = (int)$projuser->projectid;
        $db  = \Yii::$app->db;

        $raw = \Yii::$app->request->post('payload');
        if (!$raw) return ['error' => 'No payload'];
        $p = json_decode($raw, true);
        if (!$p) return ['error' => 'Bad payload'];

        $saId  = (int)($p['sa_id'] ?? 0);
        $qty   = (float)($p['qty'] ?? 0);
        $name  = trim($p['name'] ?? '');
        $unit  = trim($p['unit'] ?? '');
        $rUnit = (float)($p['r_unit'] ?? 1) ?: 1.0;
        $tasks = $p['tasks'] ?? [];
        if (!$saId) return ['error' => 'No sa_id'];
        if (!$qty)  return ['error' => 'Qty is required'];
        if ($name === '') return ['error' => 'Activity name is required'];

        // Ownership check — the row must belong to the current project.
        $sa = $db->createCommand(
            "SELECT sa.id, sa.start_date, sa.activity_id AS wan_id FROM scheduleactivities sa WHERE sa.id=:id AND sa.projectId=:p AND sa.status=0 LIMIT 1",
            [':id' => $saId, ':p' => $pid]
        )->queryOne();
        if (!$sa) return ['error' => 'Activity not found'];

        // Same duration formula Create WBS's Add button uses:
        // duration = ceil((qty * sum(task durPerUnit)) / r_unit),
        // durPerUnit = task.qty / task.productivity (summed across tasks,
        // no per-task division — r_unit divides the whole row total).
        $sumDurPerUnit = 0.0;
        foreach ($tasks as $t) {
            $tProd = (float)($t['productivity'] ?? 0);
            $tQty  = (float)($t['qty'] ?? 0);
            if ($tProd > 0) $sumDurPerUnit += ($tQty / $tProd);
        }
        $duration = ($qty > 0 && $sumDurPerUnit > 0) ? (int)ceil(($qty * $sumDurPerUnit) / $rUnit) : 1;

        $saStart = $sa['start_date'] ?: date('Y-m-d');
        $saEnd   = date('Y-m-d', strtotime($saStart . ' + ' . ($duration - 1) . ' days'));

        // Renaming/re-unit-ing here is scoped strictly to this one
        // project-instance row (scheduleactivities + its
        // workgroup_activities_new mirror) — never estimateactivities,
        // so the shared Activity Library / "+" icon's picker are
        // unaffected. Matches the user's explicit ask: a project-specific
        // display name for this Gantt activity, not a library rename.
        $db->createCommand(
            "UPDATE scheduleactivities SET name=:n, unit=:u, quantity=:q, duration=:d, old_duration=:d, end_date=:ed, resource_units=:ru WHERE id=:id",
            [':n' => $name, ':u' => $unit, ':q' => $qty, ':d' => $duration, ':ed' => $saEnd, ':ru' => $rUnit, ':id' => $saId]
        )->execute();

        $wanId = (int)$sa['wan_id'];
        if ($wanId) {
            $db->createCommand(
                "UPDATE workgroup_activities_new SET activity_Name=:n, activity_Unit=:u, cycle_Unit=:u, cycle_Quantity=:q, duration=:d WHERE id=:id",
                [':n' => $name, ':u' => $unit, ':q' => $qty, ':d' => $duration, ':id' => $wanId]
            )->execute();
            // The Estimate report takes the quantity from the project's pricing row,
            // so keep it in step with the Gantt quantity (this project only).
            $db->createCommand(
                "UPDATE pricing_estimate_new SET activity_qty=:q WHERE activity_Id=:w AND project_Id=:p AND pricing_status=0",
                [':q' => $qty, ':w' => $wanId, ':p' => $pid]
            )->execute();
        }

        $db->createCommand("DELETE FROM schedule_task_new WHERE activity_Id=:id", [':id' => $saId])->execute();
        foreach ($tasks as $t) {
            $tname = trim($t['name'] ?? '');
            if ($tname === '') continue;
            $tQty  = (float)($t['qty'] ?? 0);
            $tProd = (float)($t['productivity'] ?? 0);
            $budgetedDuration = ($tQty > 0 && $tProd > 0) ? round($tQty / $tProd, 4) : 0;
            $taskLibId = $this->_projTaskId($db, ['id' => (int)($t['id'] ?? 0), 'name' => $tname, 'unit' => $t['unit'] ?? '', 'qty' => $tQty, 'prod' => $tProd, 'resunits' => $t['resunits'] ?? 1]);
            $tSortU = isset($tSortU) ? $tSortU + 1 : 0;
            $db->createCommand()->insert('schedule_task_new', [
                'wbs_id'              => 0,
                'activity_Id'         => $saId,
                'process_Id'          => 0,
                'task_Id'             => $taskLibId,
                'Budgeted_Duration'   => $budgetedDuration,
                'End_Duration'        => 0,
                'status'              => 0,
                'wbs_activity_Id'     => 0,
                'activity_name'       => $tname,
                'primavera_id'        => 0,
                'task_qty'            => $tQty,
                'task_productivity'   => $tProd,
                'task_resource_units' => (float)($t['resunits'] ?? 1) ?: 1,
                'task_name'           => $tname,
                'task_unit'           => trim((string)($t['unit'] ?? '')),
                'sort_order'          => $tSortU,
            ])->execute();
            // Project edits stay in the project: the Activity Library is not updated.
        }

        return ['error' => 'No', 'sa_id' => $saId, 'duration' => $duration, 'duration_is_fallback' => ($sumDurPerUnit <= 0)];
    }

    /**
     * actionWbssave — saves all WBS data to DB (does NOT create Gantt bar).
     * Returns wan_id so the frontend can pass it to actionWbsadd.
     */
    public function actionWbssave()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $uid      = Yii::$app->user->Id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return ['error' => 'No project selected'];
        $pid = (int)$projuser->projectid;
        $db  = \Yii::$app->db;

        $raw = \Yii::$app->request->post('payload');
        if (!$raw) return ['error' => 'No payload'];
        $p = json_decode($raw, true);
        if (!$p) return ['error' => 'Bad payload'];

        $iowGroupId   = (int)($p['iow_group_id']   ?? 0);
        $iowGroupName = trim($p['iow_group_name'] ?? ($p['iow_group'] ?? ''));
        $iowName      = trim($p['iow_name']       ?? ($p['iow_group'] ?? ''));
        $iowActId     = (int)($p['iow_act_id']    ?? 0);
        $wgIdInput    = (int)($p['wg_id']          ?? 0);
        $actNameInput = trim($p['act_name']        ?? '');
        $unit         = trim($p['unit']            ?? '');
        $schUnit      = trim($p['sch_unit']        ?? '');
        $qty          = (float)($p['qty']           ?? 0);
        $schQty       = (float)($p['sch_qty']       ?? 0);
        $duration     = max(1, (int)($p['duration'] ?? 1));
        $tasks        = $p['tasks']     ?? [];
        $resources    = $p['resources'] ?? [];
        $projTypeId   = (int)($p['proj_type_id']   ?? 0);
        $actTypeId    = (int)($p['group_id']        ?? 0);

        if (!$actNameInput) return ['error' => 'Activity name is required'];

        // Resolve IOW group by ID (from dropdown) or fall back to creating one from name
        if (!$iowGroupId && $iowGroupName !== '') {
            $igRow = $db->createCommand(
                "SELECT id FROM iow_groups WHERE name=:n AND project_id=:p AND status=0 LIMIT 1",
                [':n' => $iowGroupName, ':p' => $pid]
            )->queryOne();
            if ($igRow) {
                $iowGroupId = (int)$igRow['id'];
            } else {
                $db->createCommand(
                    "INSERT INTO iow_groups (name, project_id, status, primavera_id) VALUES (:n, :p, 0, 0)",
                    [':n' => $iowGroupName, ':p' => $pid]
                )->execute();
                $iowGroupId = (int)$db->lastInsertID;
            }
        }
        // IOW name: if not supplied, use activity name
        if ($iowName === '') $iowName = $actNameInput;

        $now = date('Y-m-d H:i:s');

        // 1. Editing an existing WBS item (wg_id supplied): rename/re-group it in place.
        //    Otherwise, find-or-create a workgroups_new row for this IOW group + IOW name.
        $wgId = 0;
        if ($wgIdInput) {
            $wgExists = $db->createCommand(
                "SELECT Workgroup_Id FROM workgroups_new WHERE Workgroup_Id=:id AND Project_Id=:p AND Status=0 LIMIT 1",
                [':id' => $wgIdInput, ':p' => $pid]
            )->queryScalar();
            if ($wgExists) {
                $wgId = (int)$wgExists;
                $db->createCommand(
                    "UPDATE workgroups_new SET Name=:n, iowGroupid=:g, Updated_On=:t WHERE Workgroup_Id=:id",
                    [':n' => $iowName, ':g' => $iowGroupId ?: null, ':t' => $now, ':id' => $wgId]
                )->execute();
            }
        }
        if (!$wgId && $iowGroupId) {
            $wgRow = $db->createCommand(
                "SELECT Workgroup_Id FROM workgroups_new WHERE Project_Id=:p AND iowGroupid=:g AND Name=:n AND Status=0 LIMIT 1",
                [':p' => $pid, ':g' => $iowGroupId, ':n' => $iowName]
            )->queryOne();
            if (!$wgRow) {
                // Excludes the Overhead IOW group (17/18, pinned at
                // sortorder 999999 to always sort last — see
                // ProjectsController::_seedProjectOverheadActivity) so
                // real IOW groups keep a normal small sortorder range.
                $nextSort = (int)($db->createCommand(
                    "SELECT COALESCE(MAX(sortorder),0)+1 AS s FROM workgroups_new WHERE Project_Id=:p AND iowGroupid NOT IN (17, 18)", [':p'=>$pid]
                )->queryOne()['s'] ?? 0);
                $db->createCommand(
                    "INSERT INTO workgroups_new
                     (Project_Id, Name, iowGroupid, Status, sortorder, Added_On, Updated_On, Added_By,
                      start_date, end_date, duration, progress, open, parent, itemtype, worktype, worktypegroup,
                      wbs_estimate_id, pricing_status, notes, primavera_id)
                     VALUES (:p, :n, :g, 0, :s, :t, :t, :uid,
                      :t, :t, 0, 0, 0, 0, '', 0, 0, 0, 0, '', 0)",
                    [':p'=>$pid, ':n'=>$iowName, ':g'=>$iowGroupId, ':s'=>$nextSort, ':t'=>$now, ':uid'=>$uid]
                )->execute();
                $wgId = (int)$db->lastInsertID;
            } else {
                $wgId = (int)$wgRow['Workgroup_Id'];
            }
        }

        // 2. Find or create activity in estimateactivities; if iowActId=0 create new one
        if (!$iowActId) {
            // Check if activity with this name already exists for this type/group
            $existAct = $db->createCommand(
                "SELECT activity_id FROM estimateactivities
                 WHERE activity_name=:n AND work_type=:wt AND activity_type=:at AND activity_status=0 LIMIT 1",
                [':n' => $actNameInput, ':wt' => $projTypeId, ':at' => $actTypeId]
            )->queryOne();
            if ($existAct) {
                $iowActId = (int)$existAct['activity_id'];
            } else {
                $db->createCommand(
                    "INSERT INTO estimateactivities
                     (activity_name, activity_unit, work_type, activity_type, iow_group_id, activity_rate, activity_status)
                     VALUES (:n, :u, :wt, :at, :g, 0, 0)",
                    [':n' => $actNameInput, ':u' => $unit, ':wt' => $projTypeId, ':at' => $actTypeId, ':g' => $iowGroupId]
                )->execute();
                $iowActId = (int)$db->lastInsertID;
            }
        } else {
            // Update existing activity's unit/type in estimateactivities
            $db->createCommand(
                "UPDATE estimateactivities SET activity_unit=:u, work_type=:wt, activity_type=:at, iow_group_id=:g
                 WHERE activity_id=:id",
                [':u' => $unit, ':wt' => $projTypeId, ':at' => $actTypeId, ':g' => $iowGroupId, ':id' => $iowActId]
            )->execute();
        }

        // 3. Save tasks to activity_tasks
        //    $taskOrdinalToId maps the 0-based ordinal of each saved task
        //    (skipping blank names, same filtering the frontend applies in
        //    collectPayload()/openMapPopup()) to its freshly-inserted real
        //    id — tasks are DELETE+re-INSERTed every save so old ids are
        //    invalid, and this is the only place the new ids are known.
        //    resources[].task_map below is by this same ordinal, so it's
        //    translated through this map before being written as task_ids.
        $taskOrdinalToId = [];
        if (!empty($tasks)) {
            $db->createCommand(
                "DELETE FROM activity_tasks WHERE activity_id=:id",
                [':id' => $iowActId]
            )->execute();
            $sort = 0;
            foreach ($tasks as $task) {
                $tname = trim($task['name'] ?? '');
                if (!$tname) continue;
                $db->createCommand(
                    "INSERT INTO activity_tasks (activity_id, task_name, task_unit, task_qty, productivity, resource_units, sort_order)
                     VALUES (:a, :n, :u, :q, :p, :ru, :s)",
                    [':a' => $iowActId, ':n' => $tname,
                     ':u' => trim($task['unit'] ?? ''),
                     ':q' => (float)($task['qty'] ?? 0),
                     ':p' => (float)($task['prod'] ?? 0),
                     ':ru' => max(1, (float)($task['resunits'] ?? 1)),
                     ':s' => $sort]
                )->execute();
                $taskOrdinalToId[$sort] = (int)$db->lastInsertID;
                $sort++;
            }
        }

        // 5. Find or update workgroup_activities_new row (schedule data) — resolved
        //    BEFORE pricing_estimate_new/pricing_estimate_resources_new below, since
        //    those tables key off wan_id (the project-specific activity instance),
        //    not iowActId (the library-level estimateactivities id).
        $calcRate = 0.0;
        foreach ($resources as $res) { $calcRate += (float)($res['rate'] ?? 0) * (float)($res['qty'] ?? 0); }

        $wanId = 0;
        if ($iowActId && $wgId) {
            $wanRow = $db->createCommand(
                "SELECT id FROM workgroup_activities_new WHERE project_Id=:p AND activity_Id=:a AND wbs_id=:w LIMIT 1",
                [':p' => $pid, ':a' => $iowActId, ':w' => $wgId]
            )->queryOne();
            if ($wanRow) {
                $wanId = (int)$wanRow['id'];
                $db->createCommand(
                    "UPDATE workgroup_activities_new
                     SET activity_Unit=:u, estimate=1, amount=:amt, duration=:d,
                         cycle_Unit=:cu, cycle_Quantity=:cq, activity_Name=:n
                     WHERE id=:id",
                    [':u' => $unit ?: $schUnit, ':amt' => round($qty * $calcRate, 2),
                     ':d' => $duration, ':cu' => $schUnit, ':cq' => $schQty,
                     ':n' => $actNameInput, ':id' => $wanId]
                )->execute();
            } else {
                $lastSort = $db->createCommand(
                    "SELECT COALESCE(MAX(sortorder),0) AS s FROM workgroup_activities_new WHERE wbs_id=:w",
                    [':w' => $wgId]
                )->queryOne();
                $sortorder = ((int)($lastSort['s'] ?? 0)) + 1;
                $db->createCommand(
                    "INSERT INTO workgroup_activities_new
                     (project_Id, wbs_id, worktype_Id, activitytype_id, activity_Id, activity_Unit,
                      estimate, amount, sortorder, type, activity_Name, process_Id, schedule, duration,
                      cycle_Unit, cycle_Quantity, pr_status, pricing_status, operations_status, wbs_estimate_id)
                     VALUES (:p, :w, 0, 0, :ia, :u, 1, :amt, :s, 0, :n, 0, 1, :d, :cu, :cq, 0, 0, 0, 0)",
                    [':p' => $pid, ':w' => $wgId, ':ia' => $iowActId,
                     ':u' => $unit ?: $schUnit, ':amt' => round($qty * $calcRate, 2),
                     ':s' => $sortorder, ':n' => $actNameInput,
                     ':d' => $duration, ':cu' => $schUnit, ':cq' => $schQty]
                )->execute();
                $wanId = (int)$db->lastInsertID;
            }
        }

        // 5b. If this activity already has a live Gantt bar, propagate the
        //     edited Quantity/Duration into scheduleactivities too — this is
        //     the table the Gantt chart itself reads, and it was previously
        //     only ever written once at bar creation (actionWbsadd), so
        //     editing an existing activity's WBS values here never used to
        //     reach it. End date is recomputed from the (unchanged) start
        //     date + the new duration, mirroring actionWbsadd's own formula.
        if ($wanId) {
            $saRow = $db->createCommand(
                "SELECT id, start_date FROM scheduleactivities WHERE activity_id=:w AND projectId=:p AND status=0 LIMIT 1",
                [':w' => $wanId, ':p' => $pid]
            )->queryOne();
            if ($saRow) {
                $saId = (int)$saRow['id'];
                $saStart = $saRow['start_date'] ?: date('Y-m-d');
                $saEnd   = date('Y-m-d', strtotime($saStart . ' + ' . ($duration - 1) . ' days'));
                $db->createCommand(
                    "UPDATE scheduleactivities
                     SET name=:n, unit=:u, quantity=:q, duration=:d, old_duration=:d, end_date=:ed
                     WHERE id=:id",
                    [':n' => $actNameInput, ':u' => $schUnit ?: $unit, ':q' => $schQty ?: $qty,
                     ':d' => $duration, ':ed' => $saEnd, ':id' => $saId]
                )->execute();

                // Re-mirror this activity's tasks into schedule_task_new,
                // keyed by this live scheduleactivities row — the Progress
                // Report screen (ReportController::actionScheduletaskreporting)
                // reads tasks from schedule_task_new, not activity_tasks
                // directly, so edits made here (step 3 above, which only
                // touches activity_tasks) never reached Progress Report
                // until this re-sync existed. Delete+re-insert, same
                // convention actionWbsadd() already uses for the initial
                // mirror when a bar is first created.
                $db->createCommand(
                    "DELETE FROM schedule_task_new WHERE activity_Id=:id",
                    [':id' => $saId]
                )->execute();
                $wanTasks = $db->createCommand(
                    "SELECT id, task_name, task_qty, productivity FROM activity_tasks WHERE activity_id=:id",
                    [':id' => $iowActId]
                )->queryAll();
                foreach ($wanTasks as $wt) {
                    $tQty  = (float)$wt['task_qty'];
                    $tProd = (float)$wt['productivity'];
                    $budgetedDuration = ($tQty > 0 && $tProd > 0) ? round($tQty / $tProd, 4) : 0;
                    $db->createCommand()->insert('schedule_task_new', [
                        'wbs_id'              => 0,
                        'activity_Id'         => $saId,
                        'process_Id'          => 0,
                        'task_Id'             => (int)$wt['id'],
                        'Budgeted_Duration'   => $budgetedDuration,
                        'End_Duration'        => 0,
                        'status'              => 0,
                        'wbs_activity_Id'     => 0,
                        'activity_name'       => $actNameInput,
                        'primavera_id'        => 0,
                        'task_qty'            => $tQty,
                        'task_productivity'   => $tProd,
                        'task_resource_units' => 1,
                    ])->execute();
                }
            }
        }

        // 6. Save estimate qty and calculated rate to pricing_estimate_new.
        //    activity_Id = wan_id (project-instance), est_activity_Id = iowActId
        //    (library activity) — same two-column convention as
        //    pricing_estimate_resources_new below. No unique key exists on this
        //    table to use ON DUPLICATE KEY UPDATE, so check-then-write explicitly.
        $peKey = $wanId ?: $iowActId;
        $peExists = $db->createCommand(
            "SELECT pricing_estimate_Id FROM pricing_estimate_new WHERE project_Id=:p AND activity_Id=:a LIMIT 1",
            [':p' => $pid, ':a' => $peKey]
        )->queryScalar();
        if ($peExists) {
            $db->createCommand(
                "UPDATE pricing_estimate_new SET activity_qty=:qty, specific_rate=:rate, est_activity_Id=:ea WHERE pricing_estimate_Id=:id",
                [':qty' => $qty, ':rate' => round($calcRate, 2), ':ea' => $iowActId, ':id' => $peExists]
            )->execute();
        } else {
            $db->createCommand(
                "INSERT INTO pricing_estimate_new
                 (project_Id, activity_Id, activity_qty, specific_rate, est_activity_Id,
                  pricing_status, operations_status, primavera_id)
                 VALUES (:p, :a, :qty, :rate, :ea, 0, 0, 0)",
                [':p' => $pid, ':a' => $peKey, ':qty' => $qty,
                 ':rate' => round($calcRate, 2), ':ea' => $iowActId]
            )->execute();
        }

        // 7. Sync resource allocation to pricing_estimate_resources_new — the table
        //    Procurement/Storekeeper read to know what's allocated to this activity.
        //    Always upserts (unlike wbsadd's insert-once-on-first-Gantt-add), so
        //    edits to an activity's resources after it's already scheduled still
        //    reach Procurement. task_ids comes from the Map-to-Task selection
        //    already collected client-side (resources[].task_map). No unique key
        //    exists on this table either, so check-then-write explicitly.
        if ($wanId && !empty($resources)) {
            $keptResIds = [];
            foreach ($resources as $res) {
                $resId  = (int)($res['resource_id'] ?? 0);
                $typeId = (int)($res['type_id']     ?? 0);
                $resQty = (float)($res['qty']  ?? 0);
                $resRate= (float)($res['rate'] ?? 0);
                if (!$resId) continue;
                $keptResIds[] = $resId;
                // task_map entries are ordinals into the tasks list (see
                // $taskOrdinalToId above), NOT real activity_tasks ids —
                // translate, and don't drop ordinal/id 0 (array_filter's
                // default truthiness check would wrongly erase a mapping
                // to the very first task).
                $taskMap = $res['task_map'] ?? [];
                $resolvedTaskIds = [];
                if (is_array($taskMap)) {
                    foreach ($taskMap as $ord) {
                        $ord = (int)$ord;
                        if (isset($taskOrdinalToId[$ord])) $resolvedTaskIds[] = $taskOrdinalToId[$ord];
                    }
                }
                $taskIds = implode(',', $resolvedTaskIds);
                $perExists = $db->createCommand(
                    "SELECT pricing_resourceid FROM pricing_estimate_resources_new
                     WHERE activity_id=:a AND project_id=:p AND resource_Id=:r LIMIT 1",
                    [':a' => $wanId, ':p' => $pid, ':r' => $resId]
                )->queryScalar();
                if ($perExists) {
                    $db->createCommand(
                        "UPDATE pricing_estimate_resources_new
                         SET quantity=:q, rate=:rt, resourcetype_Id=:t, task_ids=:tids,
                             est_activity_Id=:ea, pricing_status=0
                         WHERE pricing_resourceid=:id",
                        [':q' => $resQty, ':rt' => $resRate, ':t' => $typeId, ':tids' => $taskIds,
                         ':ea' => $iowActId, ':id' => $perExists]
                    )->execute();
                } else {
                    $db->createCommand()->insert('pricing_estimate_resources_new', [
                        'activity_id'       => $wanId,
                        'resource_Id'       => $resId,
                        'resourcetype_Id'   => $typeId,
                        'quantity'          => $resQty,
                        'rate'              => $resRate,
                        'project_id'        => $pid,
                        'est_activity_Id'   => $iowActId,
                        'process_Id'        => 0,
                        'pricing_status'    => 0,
                        'operations_status' => 0,
                        'task_ids'          => $taskIds,
                    ])->execute();
                }

                // Two-way sync: mirror this resource back into the shared
                // Activity Library (estactivity_resources), so it becomes
                // visible to every other project using this same library
                // activity too — see ProjectsController::actionAddactivityresource
                // etc. for the reverse direction. Keyed by
                // (estactivity_id=$iowActId, est_resource_id), same
                // check-then-write convention, since neither table has a
                // unique constraint to upsert against.
                if ($iowActId) {
                    $libExists = $db->createCommand(
                        "SELECT estactres_id FROM estactivity_resources WHERE estactivity_id=:a AND est_resource_id=:r LIMIT 1",
                        [':a' => $iowActId, ':r' => $resId]
                    )->queryScalar();
                    $resAmount = round($resQty * $resRate, 2);
                    if ($libExists) {
                        $db->createCommand(
                            "UPDATE estactivity_resources
                             SET est_resource_quantity=:q, est_resource_rate=:rt, est_resource_amount=:amt, est_resource_status=0
                             WHERE estactres_id=:id",
                            [':q' => $resQty, ':rt' => $resRate, ':amt' => $resAmount, ':id' => $libExists]
                        )->execute();
                    } else {
                        $db->createCommand()->insert('estactivity_resources', [
                            'estactivity_id'        => $iowActId,
                            'est_resource_id'       => $resId,
                            'est_resource_quantity' => $resQty,
                            'est_resource_rate'     => $resRate,
                            'est_resource_amount'   => $resAmount,
                            'est_resource_status'   => 0,
                        ])->execute();
                    }
                }
            }
            // Soft-remove allocations for resources no longer in the payload.
            if (!empty($keptResIds)) {
                $params = [':a' => $wanId, ':p' => $pid];
                $placeholders = [];
                foreach (array_values($keptResIds) as $i => $rid) {
                    $key = ":r$i";
                    $placeholders[] = $key;
                    $params[$key] = $rid;
                }
                $db->createCommand(
                    "UPDATE pricing_estimate_resources_new
                     SET pricing_status = 1
                     WHERE activity_id = :a AND project_id = :p AND resource_Id NOT IN (" . implode(',', $placeholders) . ")",
                    $params
                )->execute();

                // Mirror the removal into the Library too.
                if ($iowActId) {
                    $libParams = [':a' => $iowActId];
                    $libPlaceholders = [];
                    foreach (array_values($keptResIds) as $i => $rid) {
                        $key = ":r$i";
                        $libPlaceholders[] = $key;
                        $libParams[$key] = $rid;
                    }
                    $db->createCommand(
                        "UPDATE estactivity_resources
                         SET est_resource_status = 1
                         WHERE estactivity_id = :a AND est_resource_id NOT IN (" . implode(',', $libPlaceholders) . ")",
                        $libParams
                    )->execute();
                }
            }
        }

        return [
            'error'       => 'No',
            'iow_act_id'  => $iowActId,
            'wan_id'      => $wanId,
            'wg_id'       => $wgId,
            'act_name'    => $actNameInput,
            'duration'    => $duration,
            'calc_rate'   => round($calcRate, 2),
            'sch_unit'    => $schUnit,
            'sch_qty'     => $schQty,
        ];
    }

    /**
     * actionWbsadd — saves WBS data and creates a Gantt bar in one step.
     * Accepts the full form payload; wan_id may be 0 (new) or set (existing).
     *
     * Structure is Project → IOW Group → Activity, no WBS-item tier in
     * between (workgroups_new removed) — an activity attaches directly
     * to iow_group_id everywhere below.
     */
    // A project task's id: the library task it was copied from, or — for a task
    // added only for this project — a new project-only task row
    // (activity_tasks.activity_id = 0, never listed by the Activity Library).
    // Work Orders, M. Book, Map to Task and progress all use this id.
    private function _projTaskId($db, array $task)
    {
        $id = (int)($task['id'] ?? 0);
        if ($id && $db->createCommand("SELECT 1 FROM activity_tasks WHERE id = :i", [':i' => $id])->queryScalar()) return $id;
        $db->createCommand()->insert('activity_tasks', [
            'activity_id' => 0, 'task_name' => trim((string)($task['name'] ?? '')), 'task_unit' => trim((string)($task['unit'] ?? '')),
            'task_qty' => (float)($task['qty'] ?? 0), 'productivity' => (float)($task['prod'] ?? 0),
            'resource_units' => max(1, (float)($task['resunits'] ?? 1)), 'sort_order' => 0,
        ])->execute();
        return (int)$db->getLastInsertID();
    }

    public function actionWbsadd()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        try {
        $uid      = Yii::$app->user->Id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return ['error' => 'No project selected'];
        $pid = (int)$projuser->projectid;
        $db  = \Yii::$app->db;

        $raw = \Yii::$app->request->post('payload');
        if (!$raw) return ['error' => 'No payload'];
        $p = json_decode($raw, true);
        if (!$p) return ['error' => 'Bad payload'];

        $wanId        = (int)($p['wan_id']         ?? 0);
        $iowGroupId   = (int)($p['iow_group_id']   ?? 0);
        $iowGroupName = trim($p['iow_group_name']  ?? ($p['iow_group'] ?? ''));
        $iowActId     = (int)($p['iow_act_id']     ?? 0);
        $actNameInput = trim($p['act_name']         ?? '');
        $unit         = trim($p['unit']             ?? '');
        $schUnit      = trim($p['sch_unit']         ?? '');
        $qty          = (float)($p['qty']            ?? 0);
        $schQty       = (float)($p['sch_qty']        ?? 0);
        $duration     = max(1, (int)($p['duration']  ?? 1));
        $rUnit        = (float)($p['r_unit']         ?? 1) ?: 1.0;
        $tasks        = $p['tasks']     ?? [];
        $resources    = $p['resources'] ?? [];
        $projTypeId   = (int)($p['proj_type_id']    ?? 0);
        $actTypeId    = (int)($p['group_id']         ?? 0);

        if (!$actNameInput) return ['error' => 'Activity name is required'];

        // ── Run the full wbssave logic to get/create wan_id ──────────────────
        if (!$wanId) {
            if (!$iowGroupId && $iowGroupName !== '') {
                $igRow = $db->createCommand(
                    "SELECT id FROM iow_groups WHERE name=:n AND project_id=:p AND status=0 LIMIT 1",
                    [':n' => $iowGroupName, ':p' => $pid]
                )->queryOne();
                if ($igRow) {
                    $iowGroupId = (int)$igRow['id'];
                } else {
                    $db->createCommand(
                        "INSERT INTO iow_groups (name, project_id, status, primavera_id) VALUES (:n, :p, 0, 0)",
                        [':n' => $iowGroupName, ':p' => $pid]
                    )->execute();
                    $iowGroupId = (int)$db->lastInsertID;
                }
            }
            if (!$iowGroupId) return ['error' => 'IOW Group is required'];

            if (!$iowActId) {
                $existAct = $db->createCommand(
                    "SELECT activity_id FROM estimateactivities
                     WHERE activity_name=:n AND work_type=:wt AND activity_type=:at AND activity_status=0 LIMIT 1",
                    [':n' => $actNameInput, ':wt' => $projTypeId, ':at' => $actTypeId]
                )->queryOne();
                if ($existAct) {
                    $iowActId = (int)$existAct['activity_id'];
                } else {
                    $db->createCommand(
                        "INSERT INTO estimateactivities
                         (activity_name, activity_unit, work_type, activity_type, iow_group_id, activity_rate, activity_status)
                         VALUES (:n, :u, :wt, :at, :g, 0, 0)",
                        [':n' => $actNameInput, ':u' => $unit, ':wt' => $projTypeId, ':at' => $actTypeId, ':g' => $iowGroupId]
                    )->execute();
                    $iowActId = (int)$db->lastInsertID;
                }
            } else {
                // iow_group_id / work_type / activity_type intentionally
                // NOT unconditionally overwritten here — they're the
                // library activity's OWN categorization, set once when
                // the activity is first created (see the INSERT branch
                // above) or edited via the real Activity Library screen.
                // Adding an EXISTING library activity under a WBS/IOW
                // (e.g. via the Gantt's Activity Picker, which has no
                // Project Type / Activity Type / IOW Group fields of its
                // own to submit) must never silently blank out that
                // activity's real categorization — this previously
                // corrupted the Activity Library's own data every time
                // the Activity Picker added an existing activity. Only
                // write a field here if the caller actually supplied a
                // non-zero value for it (the old WBS form, which DOES
                // have these fields, still updates them as before).
                // Adding an activity to a project never changes the Activity
                // Library (it is the general template); project changes live in
                // the project's own copy (workgroup_activities_new, tasks in
                // schedule_task_new, resources in pricing_estimate_resources_new).
            }

            // $taskOrdinalToId maps each saved task's 0-based ordinal (skipping
            // blank names, same filtering the frontend applies) to its
            // freshly-inserted real id — see the matching comment in
            // actionWbssave() for why this indirection is needed.
            $taskOrdinalToId = [];
            if (!empty($tasks)) {
                $sort = 0;
                foreach ($tasks as $task) {
                    if (!trim($task['name'] ?? '')) continue;
                    $taskOrdinalToId[$sort] = $this->_projTaskId($db, $task);
                    $sort++;
                }
            }

            $calcRate = 0.0;
            foreach ($resources as $res) { $calcRate += (float)($res['rate'] ?? 0) * (float)($res['qty'] ?? 0); }

            // pricing_estimate_new is written AFTER wan_id is resolved below (its
            // activity_Id column must hold wan_id, not iowActId — see the note on
            // the equivalent write in actionWbssave). Defer this insert/update
            // until $wanId is known.

            if ($iowActId && $iowGroupId) {
                $wanRow2 = $db->createCommand(
                    "SELECT id FROM workgroup_activities_new WHERE project_Id=:p AND activity_Id=:a AND iow_group_id=:g LIMIT 1",
                    [':p' => $pid, ':a' => $iowActId, ':g' => $iowGroupId]
                )->queryOne();
                if ($wanRow2) {
                    $wanId = (int)$wanRow2['id'];
                    $db->createCommand(
                        "UPDATE workgroup_activities_new
                         SET activity_Unit=:u, estimate=1, amount=:amt, duration=:d, cycle_Unit=:cu, cycle_Quantity=:cq, activity_Name=:n
                         WHERE id=:id",
                        [':u' => $unit ?: $schUnit, ':amt' => round($qty * $calcRate, 2),
                         ':d' => $duration, ':cu' => $schUnit, ':cq' => $schQty, ':n' => $actNameInput, ':id' => $wanId]
                    )->execute();
                } else {
                    $lastSort = $db->createCommand(
                        "SELECT COALESCE(MAX(sortorder),0) AS s FROM workgroup_activities_new WHERE iow_group_id=:g", [':g' => $iowGroupId]
                    )->queryOne();
                    $db->createCommand(
                        "INSERT INTO workgroup_activities_new
                         (project_Id, iow_group_id, worktype_Id, activitytype_id, activity_Id, activity_Unit,
                          estimate, amount, sortorder, type, activity_Name, process_Id, schedule, duration,
                          cycle_Unit, cycle_Quantity, pr_status, pricing_status, operations_status, wbs_estimate_id)
                         VALUES (:p, :g, 0, 0, :ia, :u, 1, :amt, :s, 0, :n, 0, 1, :d, :cu, :cq, 0, 0, 0, 0)",
                        [':p' => $pid, ':g' => $iowGroupId, ':ia' => $iowActId, ':u' => $unit ?: $schUnit,
                         ':amt' => round($qty * $calcRate, 2), ':s' => ((int)($lastSort['s'] ?? 0)) + 1,
                         ':n' => $actNameInput, ':d' => $duration, ':cu' => $schUnit, ':cq' => $schQty]
                    )->execute();
                    $wanId = (int)$db->lastInsertID;
                }
            }

            if (!$wanId) return ['error' => 'Could not save activity — check IOW group and activity name'];

            // pricing_estimate_new: activity_Id = wan_id (project-instance),
            // est_activity_Id = iowActId (library activity). No unique key on
            // this table, so check-then-write explicitly.
            $peExists = $db->createCommand(
                "SELECT pricing_estimate_Id FROM pricing_estimate_new WHERE project_Id=:p AND activity_Id=:a LIMIT 1",
                [':p' => $pid, ':a' => $wanId]
            )->queryScalar();
            if ($peExists) {
                $db->createCommand(
                    "UPDATE pricing_estimate_new SET activity_qty=:qty, specific_rate=:rate, est_activity_Id=:ea WHERE pricing_estimate_Id=:id",
                    [':qty' => $qty, ':rate' => round($calcRate, 2), ':ea' => $iowActId, ':id' => $peExists]
                )->execute();
            } else {
                $db->createCommand(
                    "INSERT INTO pricing_estimate_new
                     (project_Id, activity_Id, activity_qty, specific_rate, est_activity_Id, pricing_status, operations_status, primavera_id)
                     VALUES (:p, :a, :qty, :rate, :ea, 0, 0, 0)",
                    [':p' => $pid, ':a' => $wanId, ':qty' => $qty, ':rate' => round($calcRate, 2), ':ea' => $iowActId]
                )->execute();
            }
        }

        // ── Now fetch the wan row and create the scheduleactivities bar ───────
        $wanRow = $db->createCommand(
            "SELECT iow_group_id, activity_Name, activity_Unit, cycle_Unit, cycle_Quantity, duration
             FROM workgroup_activities_new WHERE id=:id LIMIT 1",
            [':id' => $wanId]
        )->queryOne();
        if (!$wanRow) return ['error' => 'WAN row not found'];

        $savedIowGroupId = (int)$wanRow['iow_group_id'];
        $savedName   = $wanRow['activity_Name'] ?: $actNameInput;
        $savedUnit   = $wanRow['cycle_Unit']    ?: ($schUnit ?: $unit);
        $savedSchQty = (float)$wanRow['cycle_Quantity'] ?: $schQty;
        $savedDur    = (int)$wanRow['duration']         ?: $duration;

        // Check if a gantt bar already exists for this wan_id (avoid duplicates)
        $existingSa = $db->createCommand(
            "SELECT id FROM scheduleactivities WHERE activity_id=:w AND projectId=:p AND status=0 LIMIT 1",
            [':w' => $wanId, ':p' => $pid]
        )->queryScalar();
        if ($existingSa) {
            // The Gantt bar already exists, so we never re-create it — but the
            // caller (the Add Activity picker's Task popup) may still have
            // submitted edited Task Name/Unit/Productivity/Qty for this same
            // activity, e.g. the user reopened the Task popup on an
            // already-added row to fix a value. Previously this branch
            // returned immediately with zero writes, so any such edit looked
            // like it saved (no error shown) but silently never reached
            // activity_tasks or schedule_task_new. Sync them here the same
            // way a fresh add would, keyed off the existing sa_id/wan_id.
            if (!empty($tasks)) {
                $db->createCommand("DELETE FROM schedule_task_new WHERE activity_Id=:id", [':id' => $existingSa])->execute();
                $sort = 0;
                foreach ($tasks as $task) {
                    $tname = trim($task['name'] ?? '');
                    if (!$tname) continue;
                    $tQty  = (float)($task['qty']  ?? 0);
                    $tProd = (float)($task['prod'] ?? 0);
                    $tRes  = max(1, (float)($task['resunits'] ?? 1));
                    $newTaskId = $this->_projTaskId($db, $task);
                    $budgetedDuration = ($tQty > 0 && $tProd > 0) ? round($tQty / $tProd, 4) : 0;
                    $db->createCommand()->insert('schedule_task_new', [
                        'wbs_id' => 0, 'activity_Id' => $existingSa, 'process_Id' => 0, 'task_Id' => $newTaskId,
                        'Budgeted_Duration' => $budgetedDuration, 'End_Duration' => 0, 'status' => 0,
                        'wbs_activity_Id' => 0, 'activity_name' => $tname, 'primavera_id' => 0,
                        'task_qty' => $tQty, 'task_productivity' => $tProd, 'task_resource_units' => $tRes,
                        'task_name' => $tname, 'task_unit' => trim($task['unit'] ?? ''), 'sort_order' => $sort,
                    ])->execute();
                    $sort++;
                }
            }
            if ($unit || $schUnit || $schQty) {
                $db->createCommand(
                    "UPDATE workgroup_activities_new SET activity_Unit=:u, cycle_Unit=:cu, cycle_Quantity=:cq WHERE id=:id",
                    [':u' => $unit ?: $schUnit, ':cu' => $schUnit, ':cq' => $schQty ?: null, ':id' => $wanId]
                )->execute();
                $db->createCommand(
                    "UPDATE scheduleactivities SET unit=:u WHERE id=:id",
                    [':u' => $unit ?: $schUnit, ':id' => $existingSa]
                )->execute();
            }
            return ['error' => 'No', 'sa_id' => (int)$existingSa, 'already_exists' => true];
        }

        $today   = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime($today . ' + ' . ($savedDur - 1) . ' days'));

        $lastSchSort = $db->createCommand(
            "SELECT COALESCE(MAX(sortorder),0) AS s FROM scheduleactivities WHERE projectId=:p",
            [':p' => $pid]
        )->queryOne();
        $schSort = ((int)($lastSchSort['s'] ?? 0)) + 1;

        $db->createCommand(
            "INSERT INTO scheduleactivities
             (name, activity_id, unit, quantity, start_date, end_date,
              actual_start_date, actual_end_date, duration, old_duration,
              iow_group_id, projectId, status, sortorder, resource_units)
             VALUES (:n, :aid, :u, :q, :sd, :ed, :sd, :ed, :dur, :dur, :g, :p, 0, :so, :ru)",
            [
                ':n'   => $savedName,
                ':aid' => $wanId,
                ':u'   => $savedUnit,
                ':q'   => $savedSchQty ?: $qty,
                ':sd'  => $today,
                ':ed'  => $endDate,
                ':dur' => $savedDur,
                ':g'   => $savedIowGroupId,
                ':p'   => $pid,
                ':so'  => $schSort,
                ':ru'  => $rUnit,
            ]
        )->execute();
        $saId = (int)$db->lastInsertID;

        // Maintain per-project IOW Group display order (workgroups_new.sortorder's
        // replacement, since there is no longer a WBS-item row to hold it) —
        // first activity added under a group in this project stakes its
        // position at the end of the project's current group ordering.
        $groupOrderExists = $db->createCommand(
            "SELECT 1 FROM iow_group_order WHERE project_id=:p AND iow_group_id=:g LIMIT 1",
            [':p' => $pid, ':g' => $savedIowGroupId]
        )->queryScalar();
        if (!$groupOrderExists) {
            $nextGroupSort = (int)($db->createCommand(
                "SELECT COALESCE(MAX(sortorder),0)+1 AS s FROM iow_group_order WHERE project_id=:p", [':p' => $pid]
            )->queryOne()['s'] ?? 1);
            $db->createCommand(
                "INSERT INTO iow_group_order (project_id, iow_group_id, sortorder) VALUES (:p, :g, :s)",
                [':p' => $pid, ':g' => $savedIowGroupId, ':s' => $nextGroupSort]
            )->execute();
        }

        // Save this activity's resource allocation into pricing_estimate_resources_new
        // (the per-project table Storekeeper/Procurement read). This only runs on
        // first creation of the Gantt bar (the $existingSa check above returns
        // early on subsequent calls) — later resource edits are synced by
        // actionWbssave's own upsert instead, which runs on every save regardless
        // of Gantt state.
        // $taskOrdinalToId is only populated when $wanId was resolved above
        // (the !$wanId branch); if a wan_id was passed in directly and had no
        // existing schedule bar, tasks were never touched in this request and
        // there is nothing to translate against.
        if (!isset($taskOrdinalToId)) $taskOrdinalToId = [];
        // Activities added from Project Workflow Tools (Create WBS) send no
        // resources: the activity's Activity Library allocation comes with it,
        // copied once into the project. From then on the project's copy is
        // edited only in Project Estimate > Resource Allocation.
        if (empty($resources) && $iowActId) {
            foreach ($db->createCommand(
                "SELECT ear.est_resource_id AS resource_id, COALESCE(r.ResourceType_Id, 0) AS type_id,
                        ear.est_resource_quantity AS qty, ear.est_resource_rate AS rate
                 FROM estactivity_resources ear LEFT JOIN resources r ON r.Resource_Id = ear.est_resource_id
                 WHERE ear.estactivity_id = :a AND ear.est_resource_status = 0 ORDER BY ear.estactres_id",
                [':a' => $iowActId]
            )->queryAll() as $lr) {
                $resources[] = ['resource_id' => (int)$lr['resource_id'], 'type_id' => (int)$lr['type_id'],
                                'qty' => (float)$lr['qty'], 'rate' => (float)$lr['rate'], 'task_map' => []];
            }
        }
        if (!empty($resources)) {
            foreach ($resources as $res) {
                $resId  = (int)($res['resource_id'] ?? 0);
                $typeId = (int)($res['type_id']     ?? 0);
                $resQty = (float)($res['qty']  ?? 0);
                $resRate= (float)($res['rate'] ?? 0);
                if (!$resId) continue;
                // task_map entries are ordinals into the tasks list, translated
                // via $taskOrdinalToId — see actionWbssave() for full rationale.
                $taskMap = $res['task_map'] ?? [];
                $resolvedTaskIds = [];
                if (is_array($taskMap)) {
                    foreach ($taskMap as $ord) {
                        $ord = (int)$ord;
                        if (isset($taskOrdinalToId[$ord])) $resolvedTaskIds[] = $taskOrdinalToId[$ord];
                    }
                }
                $taskIds = implode(',', $resolvedTaskIds);
                $exists = $db->createCommand(
                    "SELECT 1 FROM pricing_estimate_resources_new
                     WHERE project_id=:p AND activity_id=:a AND resource_Id=:r LIMIT 1",
                    [':p' => $pid, ':a' => $wanId, ':r' => $resId]
                )->queryScalar();
                if ($exists) continue;
                $db->createCommand()->insert('pricing_estimate_resources_new', [
                    'activity_id'       => $wanId,
                    'resource_Id'       => $resId,
                    'resourcetype_Id'   => $typeId,
                    'quantity'          => $resQty,
                    'rate'              => $resRate,
                    'project_id'        => $pid,
                    'est_activity_Id'   => $iowActId,
                    'process_Id'        => 0,
                    'pricing_status'    => 0,
                    'operations_status' => 0,
                    'task_ids'          => $taskIds,
                ])->execute();

                // No mirror into the Activity Library: a project's allocation
                // never changes the library template.
                if (false) {
                    $libExists = $db->createCommand(
                        "SELECT estactres_id FROM estactivity_resources WHERE estactivity_id=:a AND est_resource_id=:r LIMIT 1",
                        [':a' => $iowActId, ':r' => $resId]
                    )->queryScalar();
                    if (!$libExists) {
                        $db->createCommand()->insert('estactivity_resources', [
                            'estactivity_id'        => $iowActId,
                            'est_resource_id'       => $resId,
                            'est_resource_quantity' => $resQty,
                            'est_resource_rate'     => $resRate,
                            'est_resource_amount'   => round($resQty * $resRate, 2),
                            'est_resource_status'   => 0,
                        ])->execute();
                    }
                }
            }
        }

        // Mirror this activity's tasks into schedule_task_new, keyed by the
        // scheduleactivities row just created (matches how Storekeeper/
        // Procurement already read task_Id → activity_tasks.id elsewhere).
        //
        // Sourced from the SUBMITTED $tasks payload (what the client's Task
        // popup actually showed and what $duration above was computed
        // from) rather than re-reading activity_tasks fresh from the DB —
        // the Task popup (main.php's openTaskPopupFor/closeTaskPopup) only
        // ever edits an in-memory copy and never saves back to
        // activity_tasks, so a task's Qty/Productivity typed into that
        // popup right before clicking the tick-to-add button was
        // previously invisible to this mirror: it would silently re-read
        // whatever activity_tasks still held (often 0/blank defaults),
        // writing a 0-duration or missing task row while the Gantt bar
        // itself got the correct client-computed duration — the two fell
        // out of sync with no error, until a later Edit Activities save
        // happened to re-derive and overwrite the real duration. If the
        // client sent no tasks at all (an integration that never builds a
        // task list), fall back to activity_tasks so those callers keep
        // working exactly as before.
        // When $tasks came from the client payload, each task object has
        // no real activity_tasks.id of its own (the client never had one
        // to send — see cwbsLoadActivities()/addOne() in main.php, which
        // build { name, qty, prod, resunits } from getactivityresources()
        // with no id field). $taskOrdinalToId (built above, same ordinal
        // order, same blank-name skip) is the only reliable map from this
        // submitted list back to the real activity_tasks row just
        // inserted for it — using a nonexistent $wt['id'] here always
        // evaluated to 0, leaving every schedule_task_new.task_Id
        // permanently unlinked (LEFT JOIN activity_tasks never matched,
        // so the Edit Activities Task popup showed blank task names).
        $usingSubmittedTasks = !empty($tasks);
        $taskSource = $usingSubmittedTasks ? $tasks : array_map(function($wt){
            return ['id' => (int)$wt['id'], 'name' => $wt['task_name'], 'unit' => $wt['task_unit'], 'qty' => (float)$wt['task_qty'], 'prod' => (float)$wt['productivity'], 'resunits' => 1];
        }, $db->createCommand(
            "SELECT id, task_name, task_unit, task_qty, productivity FROM activity_tasks WHERE activity_id=:id ORDER BY sort_order, id",
            [':id' => $iowActId]
        )->queryAll());
        $ord = 0;
        foreach ($taskSource as $wt) {
            $tName = trim($wt['name'] ?? '');
            if ($tName === '') continue;
            $tQty  = (float)($wt['qty']  ?? 0);
            $tProd = (float)($wt['prod'] ?? 0);
            $tRes  = (float)($wt['resunits'] ?? 1) ?: 1;
            $budgetedDuration = ($tQty > 0 && $tProd > 0) ? round($tQty / $tProd, 4) : 0;
            $taskId = $usingSubmittedTasks ? (int)($taskOrdinalToId[$ord] ?? 0) : (int)($wt['id'] ?? 0);
            $tSort = $ord;
            $ord++;
            $db->createCommand()->insert('schedule_task_new', [
                'wbs_id'           => 0,
                'activity_Id'      => $saId,
                'process_Id'       => 0,
                'task_Id'          => $taskId,
                'Budgeted_Duration'=> $budgetedDuration,
                'End_Duration'     => 0,
                'status'           => 0,
                'wbs_activity_Id'  => 0,
                'activity_name'    => $tName,
                'primavera_id'     => 0,
                'task_qty'         => $tQty,
                'task_productivity'=> $tProd,
                'task_resource_units' => $tRes,
                'task_name'        => $tName,
                'task_unit'        => trim((string)($wt['unit'] ?? '')),
                'sort_order'       => $tSort,
            ])->execute();
        }

        $db->createCommand(
            "UPDATE activity_relations ar
             JOIN scheduleactivities sa_pre ON sa_pre.id = ar.precedent_activity AND sa_pre.projectId = :p AND sa_pre.status = 0
             JOIN scheduleactivities sa_dep ON sa_dep.id = ar.dependent_activity AND sa_dep.projectId = :p AND sa_dep.status = 0
             SET ar.status = 0
             WHERE ar.projectId = :p AND ar.status = 1",
            [':p' => $pid]
        )->execute();

        return [
            'error'           => 'No',
            'sa_id'           => $saId,
            'scheduleitem_id' => $scheduleItemId,
            'duration'        => $savedDur,
            'act_name'        => $savedName,
        ];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function actionWbsget()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $saId  = (int)\Yii::$app->request->post('sa_id', 0);
        $wanId = (int)\Yii::$app->request->post('wan_id', 0);
        if (!$saId && !$wanId) return ['error' => 'No id'];
        $db = \Yii::$app->db;

        // Look up via scheduleactivities (preferred) or directly via workgroup_activities_new
        if ($saId) {
            $sa = $db->createCommand(
                "SELECT sa.name, sa.unit, sa.quantity, sa.duration,
                        wga.id AS wan_id, wga.activity_Id AS iow_act_id, wga.activitytype_id, wga.wbs_id,
                        wga.activity_Unit AS est_unit, wga.estimate AS est_qty,
                        wga.cycle_Unit AS sch_unit, wga.cycle_Quantity AS sch_qty,
                        wg.iowGroupid, ig.name AS iow_group_name, wg.Name AS iow_name,
                        ea.activity_name, ea.activity_unit
                 FROM scheduleactivities sa
                 LEFT JOIN workgroup_activities_new wga ON wga.id = sa.activity_id
                 LEFT JOIN workgroups_new wg ON wg.Workgroup_Id = wga.wbs_id
                 LEFT JOIN iow_groups ig ON ig.id = wg.iowGroupid
                 LEFT JOIN estimateactivities ea ON ea.activity_id = wga.activity_Id
                 WHERE sa.id = :id AND sa.status = 0 LIMIT 1",
                [':id' => $saId]
            )->queryOne();
        } else {
            // Fallback: look up directly from workgroup_activities_new (old bars without scheduleactivities row)
            $sa = $db->createCommand(
                "SELECT wga.id AS wan_id, wga.activity_Name AS name, wga.activity_Unit AS unit,
                        wga.estimate AS quantity, wga.duration,
                        wga.activity_Id AS iow_act_id, wga.activitytype_id, wga.wbs_id,
                        wga.activity_Unit AS est_unit, wga.estimate AS est_qty,
                        wga.cycle_Unit AS sch_unit, wga.cycle_Quantity AS sch_qty,
                        wg.iowGroupid, ig.name AS iow_group_name, wg.Name AS iow_name,
                        ea.activity_name, ea.activity_unit
                 FROM workgroup_activities_new wga
                 LEFT JOIN workgroups_new wg ON wg.Workgroup_Id = wga.wbs_id
                 LEFT JOIN iow_groups ig ON ig.id = wg.iowGroupid
                 LEFT JOIN estimateactivities ea ON ea.activity_id = wga.activity_Id
                 WHERE wga.id = :id LIMIT 1",
                [':id' => $wanId]
            )->queryOne();
            // Also look up the scheduleactivities.id so the JS can use it for delete
            if ($sa && !$saId) {
                $saId = (int)$db->createCommand(
                    "SELECT id FROM scheduleactivities WHERE activity_id = :w AND status = 0 LIMIT 1",
                    [':w' => $wanId]
                )->queryScalar();
            }
        }
        if (!$sa) return ['error' => 'Not found'];

        $iowActId = (int)($sa['iow_act_id'] ?? 0);
        $uid      = \Yii::$app->user->Id;
        $projuser = \app\models\ProjuserSelection::find()->where(['userid' => $uid])->one();
        $pid      = $projuser ? (int)$projuser->projectid : 0;

        // tasks — from activity_tasks, the table actionWbssave/actionWbsadd actually write to.
        // resource_units aliased to resunits to match the key name the WBS
        // form's prefill code (main.php _prefillModal) already reads.
        $tasks = $saId ? $db->createCommand(
            "SELECT stn.task_Id AS id, COALESCE(stn.task_name, at.task_name) AS task_name, COALESCE(stn.task_unit, at.task_unit) AS task_unit,
                    stn.task_qty, stn.task_productivity AS productivity, stn.task_resource_units AS resunits
             FROM schedule_task_new stn LEFT JOIN activity_tasks at ON at.id = stn.task_Id
             WHERE stn.activity_Id = :sa AND stn.status = 0 ORDER BY COALESCE(stn.sort_order, 999999), stn.id",
            [':sa' => $saId]
        )->queryAll() : [];
        if (!$tasks) $tasks = $db->createCommand(
            "SELECT id, task_name, task_unit, task_qty, productivity, resource_units AS resunits FROM activity_tasks WHERE activity_id = :id ORDER BY sort_order ASC, id ASC",
            [':id' => $iowActId]
        )->queryAll();

        // resources — from pricing_estimate_resources_new, the project-scoped
        // allocation table Procurement/Storekeeper already read. estactivity_resources
        // is a library-wide table (no project_id column) and its save path wipes
        // and re-inserts all rows for the shared activity on every save, which can
        // silently drop resources — pricing_estimate_resources_new is upserted
        // per-resource instead, so it stays accurate per project.
        $resources = [];
        $wanIdForRes = (int)($sa['wan_id'] ?? 0) ?: $wanId;
        if ($wanIdForRes) {
            $resRows = $db->createCommand(
                "SELECT pern.pricing_resourceid, pern.resource_Id AS resource_id,
                        r.Name AS resource_name, r.Unit AS resource_unit,
                        pern.resourcetype_Id AS type_id, r.Resource_group_Id AS group_id,
                        pern.rate, pern.quantity, pern.task_ids
                 FROM pricing_estimate_resources_new pern
                 JOIN resources r ON r.Resource_Id = pern.resource_Id
                 WHERE pern.activity_id = :a AND pern.project_id = :p AND pern.pricing_status = 0
                 ORDER BY pern.pricing_resourceid ASC",
                [':a' => $wanIdForRes, ':p' => $pid]
            )->queryAll();

            // ordinal position of each task's real id, in the same order the
            // tasks array above is returned — task_map sent to the frontend
            // is by ordinal (matching collectPayload()'s filtered tasks list),
            // not by raw activity_tasks.id, so translate here.
            $taskOrdinalById = [];
            foreach ($tasks as $ord => $t) { $taskOrdinalById[(int)$t['id']] = $ord; }

            foreach ($resRows as $r) {
                $taskMap = [];
                if (!empty($r['task_ids'])) {
                    foreach (explode(',', $r['task_ids']) as $tid) {
                        $tid = (int)$tid;
                        if (isset($taskOrdinalById[$tid])) $taskMap[] = $taskOrdinalById[$tid];
                    }
                }
                $resources[] = [
                    'estactres_id' => (int)$r['pricing_resourceid'],
                    'resource_id'  => (int)$r['resource_id'],
                    'type_id'      => (int)$r['type_id'],
                    'group_id'     => (int)$r['group_id'],
                    'unit'         => $r['resource_unit'] ?? '',
                    'rate'         => (float)$r['rate'],
                    'qty'          => (float)$r['quantity'],
                    'amount'       => round((float)$r['rate'] * (float)$r['quantity'], 2),
                    'task_map'     => $taskMap,
                ];
            }
        }

        // proj type, activity type, unit, rate — from estimateactivities
        $projTypeId = 0; $actTypeId = 0; $estUnit = ''; $estRate = 0.0;
        if ($iowActId) {
            $ea = $db->createCommand(
                "SELECT work_type AS proj_type_id, activity_type AS act_type_id,
                        activity_unit AS unit, activity_rate AS rate
                 FROM estimateactivities WHERE activity_id = :id LIMIT 1",
                [':id' => $iowActId]
            )->queryOne();
            if ($ea) {
                $projTypeId = (int)$ea['proj_type_id'];
                $actTypeId  = (int)$ea['act_type_id'];
                $estUnit    = $ea['unit'] ?? '';
                $estRate    = (float)$ea['rate'];
            }
        }

        // Estimate quantity — from pricing_estimate_new, keyed by wan_id
        // (workgroup_activities_new.id, the project-specific activity
        // instance) — NOT iowActId (the shared library activity id). This
        // matches the exact convention actionWbssave()/actionWbsadd() write
        // under: 'activity_Id' => wan_id, 'est_activity_Id' => iowActId.
        // Querying by iowActId here (the previous bug) meant this lookup
        // essentially never matched, so the field silently fell through to
        // workgroup_activities_new.estimate, which actionWbssave() writes as
        // a hardcoded 1 (a legacy has-estimate flag, not a real quantity) —
        // that's why Estimate Quantity showed blank/wrong when editing.
        $estQty = 0.0;
        if ($wanIdForRes && $pid) {
            $peRow = $db->createCommand(
                "SELECT activity_qty FROM pricing_estimate_new
                 WHERE activity_Id = :a AND project_Id = :p AND pricing_status = 0 LIMIT 1",
                [':a' => $wanIdForRes, ':p' => $pid]
            )->queryOne();
            if ($peRow) $estQty = (float)$peRow['activity_qty'];
        }

        // if estQty still 0, fall back to workgroup_activities_new.estimate
        if (!$estQty) $estQty = (float)($sa['est_qty'] ?? $sa['quantity'] ?? 0);

        // calculated rate = sum of (resource rate × qty) — recalculated fresh
        $calcRate = 0.0;
        foreach ($resources as $r) { $calcRate += $r['rate'] * $r['qty']; }
        if (!$calcRate) $calcRate = $estRate;

        // wan_id = workgroup_activities_new.id
        $wanIdReturn = (int)($sa['wan_id'] ?? 0) ?: $wanId;

        return [
            'sa_id'          => $saId,
            'wan_id'         => $wanIdReturn,
            'proj_type_id'   => $projTypeId,
            'act_type_id'    => $actTypeId,
            'iow_group_id'   => (int)($sa['iowGroupid'] ?? 0),
            'iow_group_name' => $sa['iow_group_name'] ?? '',
            'iow_name'       => $sa['iow_name'] ?? '',
            'iow_act_id'     => $iowActId,
            'act_name'       => $sa['name'] ?: ($sa['activity_name'] ?? ''),
            'est_unit'       => $estUnit ?: ($sa['est_unit'] ?? $sa['activity_unit'] ?? ''),
            'est_qty'        => $estQty,
            'est_rate'       => round($calcRate, 2),
            'est_amt'        => round($estQty * $calcRate, 2),
            'sch_unit'       => ($sa['sch_unit'] !== null && $sa['sch_unit'] !== '') ? $sa['sch_unit'] : ($sa['unit'] ?? ''),
            'sch_qty'        => ($sa['sch_qty'] !== null && (float)$sa['sch_qty'] > 0) ? (float)$sa['sch_qty'] : (float)($sa['quantity'] ?? 0),
            'duration'       => (int)($sa['duration'] ?? 0),
            'tasks'          => $tasks,
            'resources'      => $resources,
        ];
    }

    public function actionWbsdelete()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $saId  = (int)\Yii::$app->request->post('sa_id', 0);
        $force = (bool)\Yii::$app->request->post('force', false);
        if (!$saId) return ['error' => 'No activity id'];

        $db = \Yii::$app->db;

        // Check for any active relationships (as predecessor or successor)
        $relCount = (int)$db->createCommand(
            "SELECT COUNT(*) FROM activity_relations
             WHERE (precedent_activity = :id OR dependent_activity = :id) AND status = 0",
            [':id' => $saId]
        )->queryScalar();

        if ($relCount > 0) {
            return ['error' => 'Please remove the relation before deleting this activity'];
        }

        // Get the linked workgroup_activities_new id before deleting
        $sa = $db->createCommand(
            "SELECT activity_id FROM scheduleactivities WHERE id = :id",
            [':id' => $saId]
        )->queryOne();

        $wanIdForCascade = $sa ? (int)$sa['activity_id'] : 0;

        // Data-integrity conflict check: block a permanent delete if progress
        // has already been reported against this activity, unless the user
        // has explicitly confirmed with force=1.
        if ($wanIdForCascade && !$force) {
            $conflicts = $this->checkActivityDeleteConflicts($saId, $wanIdForCascade);
            if (!empty($conflicts)) {
                return [
                    'error'     => 'Yes',
                    'conflict'  => true,
                    'errortext' => 'Deleting this activity will also permanently delete: ' . implode('; ', $conflicts) . '. This cannot be undone.',
                ];
            }
        }

        $this->hardDeleteScheduleActivity($saId, $sa, $wanIdForCascade);

        return ['ok' => true];
    }

    /**
     * actionWbsdeleteiow — deletes every activity attached to an IOW Group
     * within this project (via the same cascade actionWbsdelete uses
     * per-activity). Never touches the IOW Group itself (iow_groups) — that
     * table is a shared library, reused across projects. Blocks with a
     * conflict list (same shape actionWbsdelete returns) if any attached
     * activity has active relationships or downstream progress/procurement
     * data, unless force=1.
     */
    public function actionWbsdeleteiow()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $uid      = Yii::$app->user->Id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return ['error' => 'No project selected'];
        $pid        = (int)$projuser->projectid;
        $iowGroupId = (int)\Yii::$app->request->post('iow_group_id', 0);
        $force      = (bool)\Yii::$app->request->post('force', false);
        if (!$iowGroupId) return ['error' => 'No iow_group_id'];

        $db = \Yii::$app->db;

        $saRows = $db->createCommand(
            "SELECT sa.id, sa.activity_id
             FROM scheduleactivities sa
             WHERE sa.iow_group_id=:g AND sa.projectId=:p AND sa.status=0",
            [':g' => $iowGroupId, ':p' => $pid]
        )->queryAll();

        if (!$force) {
            $conflicts = [];
            foreach ($saRows as $sa) {
                $relCount = (int)$db->createCommand(
                    "SELECT COUNT(*) FROM activity_relations
                     WHERE (precedent_activity=:id OR dependent_activity=:id) AND status=0",
                    [':id' => $sa['id']]
                )->queryScalar();
                if ($relCount > 0) {
                    return ['error' => 'Please remove the relation on "' . $sa['id'] . '" before deleting this IOW Group'];
                }
                $wanId = (int)$sa['activity_id'];
                if ($wanId) {
                    $conflicts = array_merge($conflicts, $this->checkActivityDeleteConflicts((int)$sa['id'], $wanId));
                }
            }
            if (!empty($conflicts)) {
                return [
                    'error'     => 'Yes',
                    'conflict'  => true,
                    'errortext' => 'Deleting this IOW Group will also permanently delete: ' . implode('; ', array_unique($conflicts)) . '. This cannot be undone.',
                ];
            }
        }

        foreach ($saRows as $sa) {
            $this->hardDeleteScheduleActivity((int)$sa['id'], $sa, (int)$sa['activity_id']);
        }

        $db->createCommand()->delete('iow_group_order', ['project_id' => $pid, 'iow_group_id' => $iowGroupId])->execute();

        return ['ok' => true];
    }

    // Shared cascade used by every path that permanently removes a single
    // scheduleactivities row — nothing is left orphaned or soft-flagged in
    // workgroup_activities_new, pricing tables, or downstream procurement
    // data. $sa/$wanId may be passed in if the caller already fetched them
    // (e.g. actionWbsdelete), otherwise they're looked up here.
    private function hardDeleteScheduleActivity($saId, $sa = null, $wanId = null)
    {
        $db = \Yii::$app->db;

        if ($sa === null) {
            $sa = $db->createCommand(
                "SELECT activity_id FROM scheduleactivities WHERE id = :id",
                [':id' => $saId]
            )->queryOne();
        }
        if ($wanId === null) {
            $wanId = $sa ? (int)$sa['activity_id'] : 0;
        }

        // Cascade: permanently remove any procurement/indent/work-order/progress
        // data raised against this activity — nothing is left orphaned or soft-flagged.
        if ($sa) {
            $pricingIds = $db->createCommand(
                "SELECT pricing_resourceid FROM pricing_estimate_resources_new WHERE activity_id=:aid",
                [':aid' => $wanId]
            )->queryColumn();
            if (!empty($pricingIds)) {
                $db->createCommand()->delete('store_indents', ['pricing_resourceid' => $pricingIds])->execute();
            }

            $db->createCommand()->delete('wo_task_rates',   ['activity_id' => $wanId])->execute();
            $db->createCommand()->delete('wo_sc_rates',     ['activity_id' => $wanId])->execute();
            $db->createCommand()->delete('wo_activity_qty', ['activity_id' => $wanId])->execute();

            $woNums = $db->createCommand(
                "SELECT WO_Number FROM work_order
                 WHERE JSON_SEARCH(WO_Subject, 'one', :aid, NULL, '\$[*].activity_id') IS NOT NULL",
                [':aid' => (string)$wanId]
            )->queryColumn();
            if (!empty($woNums)) {
                $db->createCommand()->delete('wo_measurement_book', ['wo_number' => $woNums])->execute();
                $db->createCommand()->delete('work_order', ['WO_Number' => $woNums])->execute();
            }

            $poIds = $db->createCommand(
                "SELECT DISTINCT order_id FROM purchase_order_activities WHERE activity_id=:sid",
                [':sid' => $saId]
            )->queryColumn();
            if (!empty($poIds)) {
                $db->createCommand()->delete('goods_received_note',      ['GRN_Purchase_Order' => $poIds])->execute();
                $db->createCommand()->delete('purchase_order_resources',  ['order_id' => $poIds])->execute();
                $db->createCommand()->delete('purchase_order_activities', ['order_id' => $poIds])->execute();
                $db->createCommand()->delete('purchase_orders',           ['order_id' => $poIds])->execute();
            }

            $db->createCommand()->delete('pricing_estimate_resources_new', ['activity_id' => $wanId])->execute();
            $db->createCommand()->delete('pricing_estimate_new', ['activity_Id' => $wanId])->execute();
            $db->createCommand()->delete('workgroup_activities_new', ['id' => $wanId])->execute();
        }

        // Permanently remove progress-report history for this activity.
        $db->createCommand()->delete('schedule_progress_report', ['activity_id' => $saId])->execute();
        $db->createCommand()->delete('schedule_progress_report_log', ['activity_id' => $saId])->execute();
        $db->createCommand()->delete('schedule_resource', ['activityid' => $saId])->execute();

        // Permanently remove any relationships this activity was part of.
        $db->createCommand()->delete('activity_relations', [
            'or', ['precedent_activity' => $saId], ['dependent_activity' => $saId]
        ])->execute();

        // Permanently remove the scheduleactivities row. Its parent IOW
        // Group (iow_groups) is a shared library table, never touched here —
        // even if this was the last activity under it in this project, the
        // group itself persists (it may be in use by other projects).
        $db->createCommand()->delete('scheduleactivities', ['id' => $saId])->execute();
    }

    // Shared conflict check used before permanently deleting an activity —
    // lists (in plain English) what dependent data exists so the user can
    // make an informed decision before forcing the delete through.
    private function checkActivityDeleteConflicts($saId, $wanId)
    {
        $db = \Yii::$app->db;
        $conflicts = [];

        $progressCount = (int)$db->createCommand(
            "SELECT COUNT(*) FROM schedule_progress_report WHERE activity_id = :id",
            [':id' => $saId]
        )->queryScalar();
        if ($progressCount) {
            $conflicts[] = $progressCount . ' reported progress ' . ($progressCount === 1 ? 'entry' : 'entries');
        }

        $indentCount = (int)$db->createCommand(
            "SELECT COUNT(*) FROM store_indents si
             JOIN pricing_estimate_resources_new p ON p.pricing_resourceid = si.pricing_resourceid
             WHERE p.activity_id = :id",
            [':id' => $wanId]
        )->queryScalar();
        if ($indentCount) {
            $conflicts[] = $indentCount . ' raised ' . ($indentCount === 1 ? 'indent' : 'indents');
        }

        $poCount = (int)$db->createCommand(
            "SELECT COUNT(DISTINCT order_id) FROM purchase_order_activities WHERE activity_id = :id",
            [':id' => $saId]
        )->queryScalar();
        if ($poCount) {
            $conflicts[] = $poCount . ' purchase ' . ($poCount === 1 ? 'order' : 'orders') . ' (including any goods received against them)';
        }

        $woCount = (int)$db->createCommand(
            "SELECT COUNT(*) FROM work_order
             WHERE JSON_SEARCH(WO_Subject, 'one', :aid, NULL, '\$[*].activity_id') IS NOT NULL",
            [':aid' => (string)$wanId]
        )->queryScalar();
        if ($woCount) {
            $conflicts[] = $woCount . ' work ' . ($woCount === 1 ? 'order' : 'orders') . ' (including any measurement book billing against them)';
        }

        return $conflicts;
    }

    // ── Cashflow Dashboard editable targets ─────────────────────────────
    // Generic per-project, per-metric target store backing the dial gauges
    // on the Cashflow dashboard (e.g. Cash-To-Cash Cycle) — one row per
    // project+metric, upserted on save.
    public function actionCfgettarget()
    {
        $uid = Yii::$app->user->Id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) { return json_encode(['error' => 'No project selected']); }
        $pid = (int)$projuser->projectid;
        $metricKey = (string)($_POST['metric_key'] ?? '');
        if ($metricKey === '') { return json_encode(['error' => 'No metric_key']); }

        $row = Yii::$app->db->createCommand(
            "SELECT target_value FROM cf_dashboard_targets WHERE project_id=:pid AND metric_key=:mk",
            [':pid' => $pid, ':mk' => $metricKey]
        )->queryOne();

        return json_encode([
            'error'  => 'No',
            'target' => $row ? (float)$row['target_value'] : 0,
        ]);
    }

    public function actionCfsavetarget()
    {
        $uid = Yii::$app->user->Id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) { return json_encode(['error' => 'No project selected']); }
        $pid = (int)$projuser->projectid;
        $metricKey = (string)($_POST['metric_key'] ?? '');
        $value = (float)($_POST['target_value'] ?? 0);
        if ($metricKey === '') { return json_encode(['error' => 'No metric_key']); }

        Yii::$app->db->createCommand(
            "INSERT INTO cf_dashboard_targets (project_id, metric_key, target_value, updated_by)
             VALUES (:pid, :mk, :val, :uid)
             ON DUPLICATE KEY UPDATE target_value=:val, updated_by=:uid",
            [':pid' => $pid, ':mk' => $metricKey, ':val' => $value, ':uid' => $uid]
        )->execute();

        return json_encode(['error' => 'No']);
    }

    // Cashflow Dashboard "Bank" panel — current balance across ANY Bank
    // account (accounts_item.account_type = 2), NOT restricted to a bank
    // account linked to this project via accounts_item.projectid, since
    // a project's transactions can pass through a shared/company-wide
    // bank account that isn't itself tied to any one project. Project
    // scoping comes entirely from each voucher/journalvoucher row's own
    // project_id column — same correction already applied to
    // actionCfwip/actionCfcurrentassets/actionCfcurrentliabilities.
    // Computed the same way the Finance module's own Ledger/Bank Book
    // do: sum every voucher and journalvoucher row where a Bank account
    // is on either side, restricted to this project. A net CREDIT
    // balance (money drawn against the account, e.g. a CC account in
    // use) is "Availed" — shown as an orange overlay + needle on the
    // dial. A net DEBIT balance means nothing is availed (needle at
    // 0/left tip, dial fully grey, no overlay).
    public function actionCfbankbalance()
    {
        $uid = Yii::$app->user->Id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) { return json_encode(['error' => 'No project selected']); }
        $pid = (int)$projuser->projectid;
        // Agreed cashflow rules: Bank = bank heads' balance from cash and bank vouchers (no contra, no journals).
        $fg = $this->_cfFlowGroups($pid); $bal = $fg['bank'];
        return json_encode(['error' => 'No', 'has_bank' => abs($bal) > 0.004, 'total_debit' => 0, 'total_credit' => 0,
            'balance_type' => $bal < 0 ? 'credit' : 'debit', 'availed' => $bal < 0 ? round(-$bal, 2) : 0, 'balance' => round($bal, 2)]);

        $sum = function($sql) use ($pid) {
            $v = Yii::$app->db->createCommand($sql, [':pid' => $pid])->queryScalar();
            return $v ? (float)$v : 0;
        };

        $debit  = $sum("SELECT SUM(v.amount) FROM voucher v INNER JOIN accounts_item ai ON ai.id=v.account_id WHERE ai.account_type=2 AND v.contra=0 AND v.project_id=:pid");
        $credit = $sum("SELECT SUM(v.amount) FROM voucher v INNER JOIN accounts_item ai ON ai.id=v.creditacnt WHERE ai.account_type=2 AND v.contra=0 AND v.project_id=:pid");
        $contraDebit  = $sum("SELECT SUM(v.amount) FROM voucher v INNER JOIN accounts_item ai ON ai.id=v.account_id WHERE ai.account_type=2 AND v.contra=1 AND v.type='Receipt' AND v.project_id=:pid");
        $contraCredit = $sum("SELECT SUM(v.amount) FROM voucher v INNER JOIN accounts_item ai ON ai.id=v.creditacnt WHERE ai.account_type=2 AND v.contra=1 AND v.type='Payment' AND v.project_id=:pid");
        $journalDebit  = $sum("SELECT SUM(j.amount) FROM journalvoucher j INNER JOIN accounts_item ai ON ai.id=j.debitacnt WHERE ai.account_type=2 AND j.project_id=:pid");
        $journalCredit = $sum("SELECT SUM(j.amount) FROM journalvoucher j INNER JOIN accounts_item ai ON ai.id=j.creditacnt WHERE ai.account_type=2 AND j.project_id=:pid");

        $totalDebit  = $debit + $contraDebit + $journalDebit;
        $totalCredit = $credit + $contraCredit + $journalCredit;
        $net = $totalCredit - $totalDebit; // positive = net credit balance (availed)

        $hasBank = ($totalDebit > 0 || $totalCredit > 0);

        return json_encode([
            'error'    => 'No',
            'has_bank' => $hasBank,
            'total_debit'  => round($totalDebit, 2),
            'total_credit' => round($totalCredit, 2),
            'balance_type' => $net > 0 ? 'credit' : 'debit',
            'availed'      => $net > 0 ? round($net, 2) : 0,
        ]);
    }

    // Bank popup: projected bank balance, day by day from today to the end of the
    // last billing cycle, and its lowest and highest point in each cycle.
    //  - Opening = today's Bank panel balance.
    //  - Cost: each cycle's planned cost (WIP target) paid evenly over its days.
    //  - Receipts: each cycle billed at its end for its Bills Receivable target and
    //    paid after the average collection time (billing cycle length if no bills
    //    yet); today's Bills Receivable balance comes in after the same time.
    //  - Advances and Deposits: if the target is above today's actual advances, the
    //    difference is paid out on day 1 (advances already paid are in today's balance).
    public function actionCfbankprojection()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $pid = $this->_cfPid();
        if (!$pid) return ['error' => 'No project selected'];
        $w = $this->actionCfwipcycles();
        $b = $this->actionCfbillsreceivablecycles();
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $cur = self::cfCurrentCycle($pid);
        $cyc = $w['cycles'] ?? [];
        if (!$cyc || !$cur) return ['error' => 'No', 'cycles' => []];
        $fg = $this->_cfFlowGroups($pid);
        $coll = (int)round($this->_cfCollectionCycleDaysAverage($pid));
        if ($coll <= 0) $coll = (int)$cur['length'];
        $day = 86400; $today = strtotime(date('Y-m-d'));
        $in = [];   // receipts by date
        $addIn = function($ts, $amt) use (&$in) { $k = date('Y-m-d', $ts); $in[$k] = ($in[$k] ?? 0) + $amt; };
        if ($fg['br'] > 0) $addIn($today + $coll * $day, $fg['br']);
        $brT = $b['cycles'] ?? [];
        $out = []; $bal = (float)$fg['bank'];
        $advTarget = 100000 * (float)Yii::$app->db->createCommand(
            "SELECT target_value FROM cf_dashboard_targets WHERE project_id = :p AND metric_key = 'investments_target'", [':p' => $pid])->queryScalar();
        $advExtra = max(0, $advTarget - (float)$fg['adv']);
        $bal -= $advExtra;
        $prevEnd = null;
        foreach ($cyc as $i => $c) {
            $k = (int)$c['cycle'];
            $end = strtotime($c['end_date']);
            $start = $k === (int)$cur['cycle'] ? strtotime($cur['start']) : ($prevEnd !== null ? $prevEnd + $day : $end);
            $prevEnd = $end;
            if ($k < (int)$cur['cycle']) continue;
            $days = max(1, (int)round(($end - $start) / $day) + 1);
            $perDay = (float)$c['target'] / $days;
            $addIn($end + $coll * $day, (float)($brT[$i]['target'] ?? 0));
            $lo = null; $hi = null;
            for ($t = max($start, $today); $t <= $end; $t += $day) {
                $bal += ($in[date('Y-m-d', $t)] ?? 0) - $perDay;
                $lo = $lo === null ? $bal : min($lo, $bal);
                $hi = $hi === null ? $bal : max($hi, $bal);
            }
            if ($lo === null) { $lo = $hi = $bal; }
            $out[] = ['cycle' => $k, 'end_date' => $c['end_date'], 'current' => $k === (int)$cur['cycle'],
                      'low' => round($lo, 2), 'high' => round($hi, 2), 'end' => round($bal, 2)];
        }
        return ['error' => 'No', 'cycles' => $out, 'opening' => round((float)$fg['bank'], 2), 'collection_days' => $coll, 'advances_extra' => round($advExtra, 2)];
    }

    // Bank/Working Capital panel breakdown (Cashflow Dashboard popup):
    // a LINE chart (unlike every other breakdown popup here, which are
    // bar charts) of the PEAK working-capital-drawn (Availed, see
    // actionCfbankbalance) level reached in each stretch of time between
    // consecutive client payments — i.e. how high the overdraft climbed
    // before each payment came in and (presumably) paid it back down.
    //
    // Walks every Bank-touching voucher/journalvoucher row chronologically
    // (same six-way debit/credit union actionCfbankbalance sums, just
    // signed per-row instead of summed once: a CREDIT to Bank increases
    // Availed, a DEBIT decreases it) and keeps a running balance,
    // tracking the highest point reached within each window. Window
    // boundaries are the client's payment dates — same "Client" account
    // definition actionCfbillsreceivableavgtime uses (the debit side of
    // the journal entry crediting "Contract Income"), specifically each
    // date a plain Bank-debiting voucher credited that account. The
    // first window runs from the start of all transactions to the first
    // payment; each subsequent window runs between two consecutive
    // payments; the final, still-open window (last payment to today) is
    // also included, using every transaction after that last payment.
    // One point per window, labelled by the month the window's OWN
    // ending payment landed in ("Current" for the open window). Returns
    // no points if there's no Contract Income account, no Client
    // account, or no bank transactions at all.
    // Forward-looking projected working-capital requirement, in fixed
    // 45-day cycles from the project's planned start_date to its planned
    // end_date (projects.start_date/end_date — not actual transaction
    // history, unlike every other Cashflow Dashboard panel). Two curves
    // feed this, both planned/estimated rather than actual:
    //   - Cumulative planned COST: each scheduleactivities row's activity
    //     cost (pricing_estimate_new.activity_qty * SUM(pricing_estimate_
    //     resources_new.quantity * rate), the same formula
    //     actionPerformancedashboard uses) is spread evenly over its own
    //     duration in days (cost / duration_days = a daily rate), then
    //     each 45-day cycle picks up daily_rate * however many of the
    //     activity's days fall inside that cycle. Summed cumulatively.
    //   - Cumulative expected INCOME: projects.project_value (the total
    //     contract value from the project name card) divided evenly across
    //     every cycle and accrued cumulatively — there is no milestone/
    //     billing-schedule table in this schema to derive real expected
    //     payment dates from, so this is a deliberate straight-line
    //     assumption, not a projection of actual billing terms.
    // Net requirement per cycle = cumulative cost - cumulative income; can
    // go negative (a projected surplus) and is plotted as such rather than
    // floored at zero, per explicit request.
    public function actionCfbankbalancebreakdown()
    {
        $uid = Yii::$app->user->Id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) { return json_encode(['error' => 'No project selected']); }
        $pid = (int)$projuser->projectid;

        $project = Yii::$app->db->createCommand(
            "SELECT start_date, end_date, project_value FROM projects WHERE Project_Id = :pid",
            [':pid' => $pid]
        )->queryOne();
        if (!$project || !$project['start_date'] || !$project['end_date']) {
            return json_encode(['error' => 'No', 'points' => []]);
        }

        $projStart = new \DateTime(date('Y-m-d', strtotime($project['start_date'])));
        $projEnd   = new \DateTime(date('Y-m-d', strtotime($project['end_date'])));
        if ($projEnd <= $projStart) {
            return json_encode(['error' => 'No', 'points' => []]);
        }
        $totalDays = (int)$projStart->diff($projEnd)->days;
        $cycleLen = 45;
        $cycleCount = max(1, (int)ceil($totalDays / $cycleLen));
        $projectValue = (float)$project['project_value'];
        $incomePerCycle = $projectValue / $cycleCount;

        // Every activity's schedule window + planned cost, same cost
        // formula as actionPerformancedashboard.
        $activities = Yii::$app->db->createCommand(
            "SELECT sa.start_date, sa.end_date,
                    pen.activity_qty * COALESCE(res.cost_per_unit, 0) AS activity_cost
               FROM scheduleactivities sa
               INNER JOIN pricing_estimate_new pen
                       ON pen.activity_Id = sa.activity_id AND pen.project_Id = sa.projectId
               LEFT JOIN (
                    SELECT activity_id, project_id, SUM(quantity * rate) AS cost_per_unit
                      FROM pricing_estimate_resources_new
                     WHERE pricing_status = 0
                     GROUP BY activity_id, project_id
               ) res ON res.activity_id = pen.activity_Id AND res.project_id = pen.project_Id
              WHERE sa.projectId = :pid AND sa.start_date IS NOT NULL AND sa.end_date IS NOT NULL",
            [':pid' => $pid]
        )->queryAll();

        // Bucket 1: cost attributed to each 45-day cycle, via an even
        // daily spread over each activity's own schedule duration.
        $cycleCost = array_fill(0, $cycleCount, 0.0);
        foreach ($activities as $act) {
            $actStart = new \DateTime(date('Y-m-d', strtotime($act['start_date'])));
            $actEnd   = new \DateTime(date('Y-m-d', strtotime($act['end_date'])));
            if ($actEnd < $actStart) { continue; }
            $actDays = max(1, (int)$actStart->diff($actEnd)->days + 1);
            $dailyRate = ((float)$act['activity_cost']) / $actDays;
            if ($dailyRate == 0) { continue; }

            // Walk this activity's own day range, clipped to the project
            // window, and add each day's share into its containing cycle.
            $walk = clone $actStart;
            if ($walk < $projStart) { $walk = clone $projStart; }
            $clipEnd = $actEnd < $projEnd ? $actEnd : $projEnd;
            while ($walk <= $clipEnd) {
                $dayOffset = (int)$projStart->diff($walk)->days;
                $cycleIdx = min($cycleCount - 1, (int)floor($dayOffset / $cycleLen));
                $cycleCost[$cycleIdx] += $dailyRate;
                $walk->modify('+1 day');
            }
        }

        $points = [];
        $cumCost = 0.0;
        $cumIncome = 0.0;
        $cycleStart = clone $projStart;
        for ($c = 0; $c < $cycleCount; $c++) {
            $cumCost += $cycleCost[$c];
            $cumIncome += $incomePerCycle;
            $cycleEnd = clone $cycleStart;
            $cycleEnd->modify('+' . ($cycleLen - 1) . ' day');
            if ($cycleEnd > $projEnd) { $cycleEnd = clone $projEnd; }
            $points[] = [
                'name'   => $cycleEnd->format('d M Y'),
                'amount' => round($cumCost - $cumIncome, 2),
            ];
            $cycleStart->modify('+' . $cycleLen . ' day');
        }

        return json_encode([
            'error' => 'No',
            'points' => $points,
        ]);
    }

    // Shared helper: sums Bank/Cash PAYMENT transactions for a project,
    // filtered to only those whose debited counterparty account belongs
    // to a given account_type. In a bank/cash book, Bank/Cash is
    // CREDITED when an expense is incurred or an asset is purchased —
    // the debited side is the actual Expense/Asset account head. This
    // walks every non-contra voucher where the CREDITED side is ANY
    // Bank or Cash account head (account_type IN (1,2)) — NOT restricted
    // to a Bank/Cash account linked to this project via
    // accounts_item.projectid, since a project's transactions can pass
    // through a shared/company-wide Bank or Cash account that isn't
    // itself tied to any one project. Project scoping comes entirely
    // from the voucher row's own project_id column. Sums the amount only
    // when the debited account_id's account_type matches
    // $debitAccountType (e.g. 8 = Expenses for Work In Progress). Contra
    // (internal transfer) rows are excluded — they're not real external
    // expense transactions. journalvoucher entries are NOT included here
    // (journal entries don't touch Bank/Cash directly by definition in
    // this schema's voucher/journalvoucher split).
    private function _cfBankCashDebitedByType($pid, $debitAccountType)
    {
        $sql = "SELECT SUM(v.amount)
                FROM voucher v
                INNER JOIN accounts_item debitAi ON debitAi.id = v.account_id
                INNER JOIN accounts_item creditAi ON creditAi.id = v.creditacnt
                WHERE creditAi.account_type IN (1,2)
                  AND v.contra = 0
                  AND debitAi.account_type = :debitType
                  AND v.project_id = :pid";

        $v = Yii::$app->db->createCommand($sql, [':debitType' => $debitAccountType, ':pid' => $pid])->queryScalar();
        return $v ? (float)$v : 0;
    }

    // Shared helper: net (Debit - Credit) balance of one account head,
    // restricted to one project's own voucher/journalvoucher
    // transactions. Positive = debit balance (the account is behaving as
    // an Asset); negative = credit balance (behaving as a Liability).
    // Opening balances excluded — pure transaction movement.
    private function _cfAccountDebitBalance($accountId, $pid)
    {
        $sum = function($sql) use ($accountId, $pid) {
            $v = Yii::$app->db->createCommand($sql, [':id' => $accountId, ':pid' => $pid])->queryScalar();
            return $v ? (float)$v : 0;
        };
        $debit  = $sum("SELECT SUM(amount) FROM voucher WHERE account_id=:id AND contra=0 AND project_id=:pid");
        $credit = $sum("SELECT SUM(amount) FROM voucher WHERE creditacnt=:id AND contra=0 AND project_id=:pid");
        $contraDebit  = $sum("SELECT SUM(amount) FROM voucher WHERE account_id=:id AND contra=1 AND type='Receipt' AND project_id=:pid");
        $contraCredit = $sum("SELECT SUM(amount) FROM voucher WHERE creditacnt=:id AND contra=1 AND type='Payment' AND project_id=:pid");
        $journalDebit  = $sum("SELECT SUM(amount) FROM journalvoucher WHERE debitacnt=:id AND project_id=:pid");
        $journalCredit = $sum("SELECT SUM(amount) FROM journalvoucher WHERE creditacnt=:id AND project_id=:pid");

        $totalDebit  = $debit + $contraDebit + $journalDebit;
        $totalCredit = $credit + $contraCredit + $journalCredit;
        return $totalDebit - $totalCredit;
    }

    // Same as _cfAccountDebitBalance, but excludes journal entries whose
    // CREDIT side is ANY Income-type account (accounts_item.account_type
    // = 4 — Contract Income included, but not limited to it by name).
    // Used ONLY by the Investments panel (actionCfcurrentassets/
    // breakdown): a bill raised (or any other income recognized) but not
    // yet collected is Bills Receivable, not an Investment, so it must
    // not inflate this account's balance here — every OTHER caller of
    // _cfAccountDebitBalance keeps counting it (Bills Receivable itself
    // depends on the Contract Income-specific version of this).
    private function _cfAccountDebitBalanceExcludingContractIncome($accountId, $pid)
    {
        $incomeAcctIds = Yii::$app->db->createCommand(
            "SELECT id FROM accounts_item WHERE account_type = 4"
        )->queryColumn();

        $sum = function($sql, $params = []) use ($accountId, $pid) {
            $v = Yii::$app->db->createCommand($sql, array_merge([':id' => $accountId, ':pid' => $pid], $params))->queryScalar();
            return $v ? (float)$v : 0;
        };
        $debit  = $sum("SELECT SUM(amount) FROM voucher WHERE account_id=:id AND contra=0 AND project_id=:pid");
        $credit = $sum("SELECT SUM(amount) FROM voucher WHERE creditacnt=:id AND contra=0 AND project_id=:pid");
        $contraDebit  = $sum("SELECT SUM(amount) FROM voucher WHERE account_id=:id AND contra=1 AND type='Receipt' AND project_id=:pid");
        $contraCredit = $sum("SELECT SUM(amount) FROM voucher WHERE creditacnt=:id AND contra=1 AND type='Payment' AND project_id=:pid");

        $journalDebitSql = "SELECT SUM(amount) FROM journalvoucher WHERE debitacnt=:id AND project_id=:pid";
        $journalCreditSql = "SELECT SUM(amount) FROM journalvoucher WHERE creditacnt=:id AND project_id=:pid";
        if (!empty($incomeAcctIds)) {
            $placeholders = implode(',', array_map('intval', $incomeAcctIds));
            $journalDebitSql .= " AND creditacnt NOT IN ($placeholders)";
            $journalCreditSql .= " AND creditacnt NOT IN ($placeholders)";
        }
        $journalDebit  = $sum($journalDebitSql);
        $journalCredit = $sum($journalCreditSql);

        $totalDebit  = $debit + $contraDebit + $journalDebit;
        $totalCredit = $credit + $contraCredit + $journalCredit;
        return $totalDebit - $totalCredit;
    }

    // Same as _cfAccountDebitBalance, but restricted to transactions
    // dated ON OR BEFORE $uptoDate (inclusive) — used to reconstruct
    // what an account's balance would have read as of a past cycle's
    // closing date, for the Total Investment per-cycle breakdown chart.
    // Pass null for $uptoDate to mean "no upper bound" (same as
    // _cfAccountDebitBalance).
    private function _cfAccountDebitBalanceAsOf($accountId, $pid, $uptoDate)
    {
        if (!$uptoDate) {
            return $this->_cfAccountDebitBalance($accountId, $pid);
        }
        $sum = function($sql) use ($accountId, $pid, $uptoDate) {
            $v = Yii::$app->db->createCommand($sql, [':id' => $accountId, ':pid' => $pid, ':ud' => $uptoDate])->queryScalar();
            return $v ? (float)$v : 0;
        };
        $debit  = $sum("SELECT SUM(amount) FROM voucher WHERE account_id=:id AND contra=0 AND project_id=:pid AND date(date) <= :ud");
        $credit = $sum("SELECT SUM(amount) FROM voucher WHERE creditacnt=:id AND contra=0 AND project_id=:pid AND date(date) <= :ud");
        $contraDebit  = $sum("SELECT SUM(amount) FROM voucher WHERE account_id=:id AND contra=1 AND type='Receipt' AND project_id=:pid AND date(date) <= :ud");
        $contraCredit = $sum("SELECT SUM(amount) FROM voucher WHERE creditacnt=:id AND contra=1 AND type='Payment' AND project_id=:pid AND date(date) <= :ud");
        $journalDebit  = $sum("SELECT SUM(amount) FROM journalvoucher WHERE debitacnt=:id AND project_id=:pid AND date(date) <= :ud");
        $journalCredit = $sum("SELECT SUM(amount) FROM journalvoucher WHERE creditacnt=:id AND project_id=:pid AND date(date) <= :ud");

        $totalDebit  = $debit + $contraDebit + $journalDebit;
        $totalCredit = $credit + $contraCredit + $journalCredit;
        return $totalDebit - $totalCredit;
    }

    // Same as _cfAccountDebitBalanceExcludingContractIncome, but also
    // restricted to transactions dated ON OR BEFORE $uptoDate (pass null
    // for "no upper bound", same as _cfAccountDebitBalanceAsOf). Used by
    // _cfNetInvestmentAsOf (Total Investment) for its own Investments
    // component, at each past cycle's closing date.
    private function _cfAccountDebitBalanceAsOfExcludingIncome($accountId, $pid, $uptoDate)
    {
        if (!$uptoDate) {
            return $this->_cfAccountDebitBalanceExcludingContractIncome($accountId, $pid);
        }
        $incomeAcctIds = Yii::$app->db->createCommand(
            "SELECT id FROM accounts_item WHERE account_type = 4"
        )->queryColumn();

        $sum = function($sql) use ($accountId, $pid, $uptoDate) {
            $v = Yii::$app->db->createCommand($sql, [':id' => $accountId, ':pid' => $pid, ':ud' => $uptoDate])->queryScalar();
            return $v ? (float)$v : 0;
        };
        $debit  = $sum("SELECT SUM(amount) FROM voucher WHERE account_id=:id AND contra=0 AND project_id=:pid AND date(date) <= :ud");
        $credit = $sum("SELECT SUM(amount) FROM voucher WHERE creditacnt=:id AND contra=0 AND project_id=:pid AND date(date) <= :ud");
        $contraDebit  = $sum("SELECT SUM(amount) FROM voucher WHERE account_id=:id AND contra=1 AND type='Receipt' AND project_id=:pid AND date(date) <= :ud");
        $contraCredit = $sum("SELECT SUM(amount) FROM voucher WHERE creditacnt=:id AND contra=1 AND type='Payment' AND project_id=:pid AND date(date) <= :ud");

        $journalDebitSql = "SELECT SUM(amount) FROM journalvoucher WHERE debitacnt=:id AND project_id=:pid AND date(date) <= :ud";
        $journalCreditSql = "SELECT SUM(amount) FROM journalvoucher WHERE creditacnt=:id AND project_id=:pid AND date(date) <= :ud";
        if (!empty($incomeAcctIds)) {
            $placeholders = implode(',', array_map('intval', $incomeAcctIds));
            $journalDebitSql .= " AND creditacnt NOT IN ($placeholders)";
            $journalCreditSql .= " AND creditacnt NOT IN ($placeholders)";
        }
        $journalDebit  = $sum($journalDebitSql);
        $journalCredit = $sum($journalCreditSql);

        $totalDebit  = $debit + $contraDebit + $journalDebit;
        $totalCredit = $credit + $contraCredit + $journalCredit;
        return $totalDebit - $totalCredit;
    }

    // Shared helper: every account head mapped under EITHER the
    // "Current Assets" (accounts_sub.id = 32) OR "Current Liabilities"
    // (accounts_sub.id = 30) subgroup, via subgroup_accounts. This is
    // the combined candidate pool for both actionCfcurrentassets and
    // actionCfcurrentliabilities — an account head doesn't statically
    // "belong" to one bucket or the other; each one's OWN current
    // balance sign (Debit - Credit, project-scoped) decides which
    // bucket it falls into for a given project: a debit balance means
    // it's currently behaving as an Asset, a credit balance means it's
    // currently behaving as a Liability. The same account can move
    // between the two panels project to project, or period to period,
    // as its balance sign changes.
    private function _cfCurrentAssetLiabilityAccountIds()
    {
        return Yii::$app->db->createCommand(
            "SELECT DISTINCT account_id FROM subgroup_accounts WHERE subgrp_id IN (32, 30)"
        )->queryColumn();
    }

    // Shared helper: every Bank/Cash payment voucher (contra=0, credit
    // side account_type IN 1,2) for this project, grouped by its DEBIT
    // account head, restricted to a given date window (pass null for
    // either bound to mean unbounded). Returns [acctId => amount].
    // Feeds both the Investments and WIP formulas — each account's own
    // current ledger balance (see _cfAccountDebitBalance) then decides
    // which of the two panels its grouped amount counts toward:
    //   - Expense-type (account_type=8) accounts always go to WIP — the
    //     debit itself IS the recognized cost, regardless of balance.
    //   - Any OTHER account with a current DEBIT balance is behaving
    //     like an advance/deposit — no liability has been cleared yet —
    //     so it goes to Investments.
    //   - Any OTHER account with a CREDIT or ZERO balance means the
    //     benefit has already been received and this payment is
    //     clearing/part-settling that liability — so it goes to WIP.
    private function _cfBankCashPaymentsByDebitAccount($pid, $sinceDate, $uptoDate)
    {
        $cond = "";
        $params = [':pid' => $pid];
        if ($sinceDate) { $cond .= " AND date(v.date) > :sd"; $params[':sd'] = $sinceDate; }
        if ($uptoDate) { $cond .= " AND date(v.date) <= :ud"; $params[':ud'] = $uptoDate; }

        $rows = Yii::$app->db->createCommand(
            "SELECT v.account_id, SUM(v.amount) AS amt
             FROM voucher v
             INNER JOIN accounts_item creditAi ON creditAi.id = v.creditacnt
             WHERE creditAi.account_type IN (1,2)
               AND v.contra = 0
               AND v.project_id = :pid
               $cond
             GROUP BY v.account_id",
            $params
        )->queryAll();

        $byAccount = [];
        foreach ($rows as $row) {
            $byAccount[(int)$row['account_id']] = (float)$row['amt'];
        }
        return $byAccount;
    }

    // Investments panel (Cashflow Dashboard, formerly "Net Investments"):
    // sum of Bank/Cash payment amounts (see
    // _cfBankCashPaymentsByDebitAccount) grouped by debit account head,
    // keeping only accounts whose OWN current ledger balance (vouchers +
    // journals, project-scoped) is a DEBIT balance — behaving like an
    // advance/deposit, not yet cleared against a liability. Expense-type
    // accounts are excluded entirely here — they always count as WIP
    // regardless of balance.
    // Cashflow groups, agreed rules (2026-10-20):
    //  - Built from the project's cash and bank vouchers only (journals excluded);
    //    contra entries (bank-cash transfers) count only for the bank and cash
    //    balances. Balance per account head = debit - credit.
    //  - Bank = bank heads (type 2). Cash in hand (cash heads, type 1, debit
    //    balance) goes into Advances and Deposits.
    //  - Supplier heads (type 7) are split by their MAIN account balance (vouchers +
    //    journals): if the main balance is debit, that excess paid over billed is an
    //    advance and the rest of the payment is work done; if credit or zero, the whole
    //    payment is work done (part payment for goods/work received).
    //  - Advances and Deposits = debit balances of other heads + supplier advances.
    //  - WIP = expense heads (type 8) + supplier work done, counted from the latest
    //    bill date (the cost up to each bill date is cleared when the bill is
    //    raised), less any non-client money received.
    //  - Total Investment = Advances and Deposits + WIP (Bills Receivable is not added).
    //  - Bills Receivable = balance of the "Bills Receivable" head (0 if credit).
    // $uptoDate (Y-m-d) limits every balance to that date; null = today.
    private function _cfFlowGroups($pid, $uptoDate = null, $costOnly = false)
    {
        $db = Yii::$app->db;
        $dc = $uptoDate ? " AND date(date) <= " . $db->quoteValue($uptoDate) : '';
        $p = [':p1' => $pid, ':p2' => $pid, ':p3' => $pid, ':p4' => $pid];
        $cash = $db->createCommand("SELECT a, SUM(x) FROM (
                SELECT account_id a, amount x FROM voucher WHERE contra=0 AND project_id=:p1 $dc
                UNION ALL SELECT creditacnt, -amount FROM voucher WHERE contra=0 AND project_id=:p2 $dc UNION ALL SELECT account_id, amount FROM voucher WHERE contra=1 AND type='Receipt' AND project_id=:p3 $dc UNION ALL SELECT creditacnt, -amount FROM voucher WHERE contra=1 AND type='Payment' AND project_id=:p4 $dc) t GROUP BY a",
                $p)->queryAll(\PDO::FETCH_KEY_PAIR);
        $main = $db->createCommand("SELECT a, SUM(x) FROM (
                SELECT account_id a, amount x FROM voucher WHERE contra=0 AND project_id=:p1 $dc
                UNION ALL SELECT creditacnt, -amount FROM voucher WHERE contra=0 AND project_id=:p2 $dc
                UNION ALL SELECT debitacnt, amount FROM journalvoucher WHERE project_id=:p3 $dc
                UNION ALL SELECT creditacnt, -amount FROM journalvoucher WHERE project_id=:p4 $dc) t GROUP BY a", $p)->queryAll(\PDO::FETCH_KEY_PAIR);
        static $types = null, $brIds = null, $vendorAcc = null;
        if ($types === null) {
            $types = $db->createCommand("SELECT id, account_type FROM accounts_item")->queryAll(\PDO::FETCH_KEY_PAIR);
            // Supplier / sub-contractor accounts (linked to a vendor), as opposed to loans etc.
            $vendorAcc = array_flip(array_map('intval', $db->createCommand("SELECT DISTINCT account_id FROM vendors WHERE account_id > 0")->queryColumn()));
            $brIds = $db->createCommand("SELECT id FROM accounts_item WHERE name='Bills Receivable'")->queryColumn();
        }
        $g = ['bank' => 0.0, 'cash' => 0.0, 'adv' => 0.0, 'wip_cost' => 0.0, 'received' => 0.0, 'refunds' => 0.0, 'wip_acc' => []];
        foreach ($cash as $id => $b) {
            $id = (int)$id; $b = (float)$b; $t = (int)($types[$id] ?? 0);
            if ($t === 2)      { $g['bank'] += $b; }
            elseif ($t === 1)  { $g['cash'] += $b; if ($b > 0) { $g['adv'] += $b; } else { $g['received'] += -$b; } }   // cash in hand is an advance
            elseif ($t === 8)  { $g['wip_cost'] += $b; $g['wip_acc'][$id] = $b; }
            elseif ($t === 7) {
                if ($b > 0) {
                    $m = (float)($main[$id] ?? 0);
                    $a = $m > 0 ? min($m, $b) : 0;
                    $g['adv'] += $a;
                    $g['wip_cost'] += $b - $a;
                    $g['wip_acc'][$id] = $b - $a;
                } else {
                    $g['received'] += -$b;
                    if (isset($vendorAcc[$id])) $g['refunds'] += -$b;   // money returned by a supplier / sub-contractor
                }
            }
            elseif ($b > 0)    { $g['adv'] += $b; }
            else               { $g['received'] += -$b; }
        }
        if ($costOnly) { return $g; }
        $br = 0.0;
        foreach ($brIds as $bid) { $br += (float)($main[$bid] ?? 0); }
        // Bills raised = journal debits to Bills Receivable (bill amount incl. GST).
        // Client payments credit Bills Receivable, so they are already reflected in
        // its outstanding balance; any other money received reduces WIP directly.
        $billed = 0.0; $clientPaid = 0.0;
        foreach ($brIds as $bid) {
            $billed += (float)$db->createCommand(
                "SELECT COALESCE(SUM(amount),0) FROM journalvoucher WHERE project_id=:p AND debitacnt=:b" . $dc,
                [':p' => $pid, ':b' => $bid])->queryScalar();
            $cb = (float)($cash[$bid] ?? 0);
            if ($cb < 0) { $clientPaid += -$cb; }
        }
        $g['billed'] = $billed;
        // On each bill date the cost spent up to that date is cleared from WIP (the
        // bill amount itself is not deducted), so WIP = cost spent since the latest
        // bill, less refunds from suppliers / sub-contractors.
        $lastBill = null;
        if ($brIds) {
            $in = implode(',', array_map('intval', $brIds));
            $lastBill = $db->createCommand(
                "SELECT MAX(date(date)) FROM journalvoucher WHERE project_id=:p AND debitacnt IN ($in)" . $dc,
                [':p' => $pid])->queryScalar();
        }
        $costAtBill = $lastBill ? $this->_cfFlowGroups($pid, $lastBill, true)['wip_cost'] : 0.0;
        $g['last_bill'] = $lastBill;
        // Refunds from suppliers / sub-contractors reduce WIP (refunds on expense accounts
        // already net off in wip_cost); loans, capital and advances received do not.
        $g['wip'] = $g['wip_cost'] - $costAtBill - $g['refunds'];
        // Total Investment = Advances and Deposits + X (agreed 2026-10-23):
        //  - Bills Receivable 0 (or no bill yet): X = current WIP (cost since the latest bill).
        //  - Bill raised and nothing credited to Bills Receivable since: X = the unpaid
        //    balance carried from earlier bills + the WIP deducted at that bill (cost since
        //    the previous bill); cost spent after the bill is not added.
        //  - The client has paid the bill (Bank/Cash receipt crediting Bills Receivable)
        //    after the latest bill and a debit balance is still left over: X = that left-over
        //    Bills Receivable balance + current WIP, until it is paid or a new bill is raised.
        //    Journal credits (TDS etc.) alone do not count as payment.
        $g['ti_mode'] = 'wip';
        $g['ti'] = $g['adv'] + $g['wip'];
        if ($lastBill && $br > 0.005) {
            $in = implode(',', array_map('intval', $brIds));
            $lastCredit = $db->createCommand(
                "SELECT MAX(date(date)) FROM voucher WHERE project_id=:p1 AND creditacnt IN ($in)$dc",
                [':p1' => $pid])->queryScalar();
            if ($lastCredit && $lastCredit > $lastBill) {
                $g['ti_mode'] = 'br';
                $g['ti'] = $g['adv'] + $br + $g['wip'];
            } else {
                // Left-over from earlier bills = Bills Receivable balance on the day before this bill.
                $carry = max(0, (float)$db->createCommand(
                    "SELECT COALESCE(SUM(x),0) FROM (
                       SELECT amount x FROM voucher WHERE project_id=:p1 AND account_id IN ($in) AND date(date) < :d1
                       UNION ALL SELECT -amount FROM voucher WHERE project_id=:p2 AND creditacnt IN ($in) AND date(date) < :d2
                       UNION ALL SELECT amount FROM journalvoucher WHERE project_id=:p3 AND debitacnt IN ($in) AND date(date) < :d3
                       UNION ALL SELECT -amount FROM journalvoucher WHERE project_id=:p4 AND creditacnt IN ($in) AND date(date) < :d4) t",
                    [':p1' => $pid, ':p2' => $pid, ':p3' => $pid, ':p4' => $pid, ':d1' => $lastBill, ':d2' => $lastBill, ':d3' => $lastBill, ':d4' => $lastBill])->queryScalar());
                $prevBill = $db->createCommand(
                    "SELECT MAX(date(date)) FROM journalvoucher WHERE project_id=:p AND debitacnt IN ($in) AND date(date) < :d",
                    [':p' => $pid, ':d' => $lastBill])->queryScalar();
                $deducted = $costAtBill - ($prevBill ? $this->_cfFlowGroups($pid, $prevBill, true)['wip_cost'] : 0.0);
                $g['ti_mode'] = 'billed';
                $g['ti_deducted'] = $deducted; $g['ti_carry'] = $carry;
                $g['ti'] = $g['adv'] + $carry + $deducted;
            }
        }
        $g['br']  = max(0, $br);
        return $g;
    }

    // Average investment in the project from its first transaction to today: the
    // Total Investment after each transaction date, weighted by the number of days
    // it stayed at that value (until the next transaction date, or today).
    private function _cfAverageInvestment($pid)
    {
        $dates = Yii::$app->db->createCommand(
            "SELECT DISTINCT d FROM (SELECT date(date) d FROM voucher WHERE project_id=:p1
               UNION SELECT date(date) FROM journalvoucher WHERE project_id=:p2) t
             WHERE d IS NOT NULL AND d > '1900-01-01' AND d <= CURDATE() ORDER BY d", [':p1' => $pid, ':p2' => $pid]
        )->queryColumn();
        if (!$dates) return ['avg' => null, 'from' => null];
        $sum = 0.0; $days = 0;
        $end = strtotime(date('Y-m-d')) + 86400;   // today counts as a full day
        foreach ($dates as $i => $d) {
            $next = isset($dates[$i + 1]) ? strtotime($dates[$i + 1]) : $end;
            $w = max(0, (int)round(($next - strtotime($d)) / 86400));
            $sum += $this->_cfFlowGroups($pid, $d)['ti'] * $w;
            $days += $w;
        }
        return ['avg' => $days ? round($sum / $days, 2) : null, 'from' => $dates[0]];
    }

    // Cycle-to-cycle cash flow: a cycle ends on each date a client payment is
    // credited to Bills Receivable; the last row is the open cycle up to today.
    // Each row's amount is Total Investment at the end of the cycle (= money tied up).
    private function _cfFlowCycles($pid)
    {
        $db = Yii::$app->db;
        $ends = $db->createCommand(
            "SELECT DISTINCT date(v.date) FROM voucher v JOIN accounts_item ai ON ai.id = v.creditacnt
             WHERE v.contra=0 AND v.project_id=:p AND ai.name='Bills Receivable' ORDER BY 1", [':p' => $pid]
        )->queryColumn();
        $rows = [];
        foreach ($ends as $i => $e) {
            $rows[] = ['name' => 'C' . ($i + 1) . ' ' . date('d M', strtotime($e)), 'amount' => round($this->_cfFlowGroups($pid, $e)['ti'], 2)];
        }
        return $rows;
    }

    // Current Assets panel (agreed 2026-10-20): sum of every account head that is
    // not Bank (2), Income (4) or Expense (8) type and has a DEBIT balance in the project's
    // main account (vouchers + journals; contra entries counted once). Input GST is
    // counted in full (not netted against Output GST).
    public function actionCfcurrentassetstotal()
    {
        $uid = Yii::$app->user->Id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) { return json_encode(['error' => 'No project selected']); }
        $pid = (int)$projuser->projectid;
        $p = [];
        for ($i = 1; $i <= 6; $i++) { $p[':p' . $i] = $pid; }
        $rows = Yii::$app->db->createCommand("SELECT t.a, SUM(t.x) bal FROM (
                SELECT account_id a, amount x FROM voucher WHERE contra=0 AND project_id=:p1
                UNION ALL SELECT creditacnt, -amount FROM voucher WHERE contra=0 AND project_id=:p2
                UNION ALL SELECT account_id, amount FROM voucher WHERE contra=1 AND type='Receipt' AND project_id=:p3
                UNION ALL SELECT creditacnt, -amount FROM voucher WHERE contra=1 AND type='Payment' AND project_id=:p4
                UNION ALL SELECT debitacnt, amount FROM journalvoucher WHERE project_id=:p5
                UNION ALL SELECT creditacnt, -amount FROM journalvoucher WHERE project_id=:p6) t
             JOIN accounts_item ai ON ai.id = t.a
             WHERE ai.account_type NOT IN (2, 4, 8)
             GROUP BY t.a HAVING SUM(t.x) > 0", $p)->queryAll();
        $total = 0;
        foreach ($rows as $r) { $total += (float)$r['bal']; }
        return json_encode(['error' => 'No', 'total' => round($total, 2)]);
    }

    // Current Assets popup: the account heads behind the Current Assets figure
    // (same rule as actionCfcurrentassetstotal), largest first.
    public function actionCfcurrentassetslist()
    {
        $uid = Yii::$app->user->Id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) { return json_encode(['error' => 'No project selected']); }
        $pid = (int)$projuser->projectid;
        $p = [];
        for ($i = 1; $i <= 6; $i++) { $p[':p' . $i] = $pid; }
        $rows = Yii::$app->db->createCommand("SELECT ai.name, SUM(t.x) amt FROM (
                SELECT account_id a, amount x FROM voucher WHERE contra=0 AND project_id=:p1
                UNION ALL SELECT creditacnt, -amount FROM voucher WHERE contra=0 AND project_id=:p2
                UNION ALL SELECT account_id, amount FROM voucher WHERE contra=1 AND type='Receipt' AND project_id=:p3
                UNION ALL SELECT creditacnt, -amount FROM voucher WHERE contra=1 AND type='Payment' AND project_id=:p4
                UNION ALL SELECT debitacnt, amount FROM journalvoucher WHERE project_id=:p5
                UNION ALL SELECT creditacnt, -amount FROM journalvoucher WHERE project_id=:p6) t
             JOIN accounts_item ai ON ai.id = t.a
             WHERE ai.account_type NOT IN (2, 4, 8)
             GROUP BY t.a, ai.name HAVING SUM(t.x) > 0 ORDER BY amt DESC", $p)->queryAll();
        $out = [];
        foreach ($rows as $r) { $out[] = ['name' => $r['name'], 'amount' => round((float)$r['amt'], 2)]; }
        return json_encode(['error' => 'No', 'accounts' => $out]);
    }

    public function actionCfcurrentassets()
    {
        $uid = Yii::$app->user->Id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) { return json_encode(['error' => 'No project selected']); }
        $pid = (int)$projuser->projectid;
        // Agreed cashflow rules: Advances and Deposits (see _cfFlowGroups).
        return json_encode(['error' => 'No', 'total' => round($this->_cfFlowGroups($pid)['adv'], 2)]);

        $byAccount = $this->_cfBankCashPaymentsByDebitAccount($pid, null, null);
        $gstPairedIds = $this->_cfGstPairedAccountIds();

        $total = $this->_cfGstNetInvestments($pid);
        foreach ($byAccount as $acctId => $amt) {
            if (in_array($acctId, $gstPairedIds, true)) { continue; }
            $accountType = (int)Yii::$app->db->createCommand(
                "SELECT account_type FROM accounts_item WHERE id=:id", [':id' => $acctId]
            )->queryScalar();
            if ($accountType === 8) { continue; }
            $balance = $this->_cfAccountDebitBalance($acctId, $pid);
            if ($balance > 0) {
                $total += $balance;
            }
        }

        return json_encode([
            'error' => 'No',
            'total' => round($total, 2),
        ]);
    }

    // Investments panel breakdown (Cashflow Dashboard tooltip): same
    // account pool and debit-balance filter as actionCfcurrentassets,
    // but returns each contributing account head individually instead
    // of just the sum — for the click-to-open per-account bar chart.
    // Each row also carries a `members` array of the individual voucher
    // transactions behind that account's payment total (see
    // _cfAccountTransactionsDetail), consumed by the shared per-bar
    // hover tooltip (_cfBreakdownTip in _performancedashboard.js) to
    // show transaction-level detail on hover — same tooltip mechanism
    // WIP's group bars already use for their member account heads.
    public function actionCfcurrentassetsbreakdown()
    {
        $uid = Yii::$app->user->Id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) { return json_encode(['error' => 'No project selected']); }
        $pid = (int)$projuser->projectid;

        $byAccount = $this->_cfBankCashPaymentsByDebitAccount($pid, null, null);
        $gstPairedIds = $this->_cfGstPairedAccountIds();

        $rows = [];
        foreach ($this->_cfGstAccountPairs() as $pair) {
            $net = $this->_cfAccountDebitBalance($pair[0], $pid) + $this->_cfAccountDebitBalance($pair[1], $pid);
            if ($net > 0) {
                $inputName = Yii::$app->db->createCommand(
                    "SELECT name FROM accounts_item WHERE id=:id", [':id' => $pair[0]]
                )->queryScalar();
                $label = preg_replace('/^Input\s*/i', '', $inputName ?: '') ?: 'GST';
                $rows[] = ['name' => 'Net ' . $label . ' Credit', 'amount' => round($net, 2), 'members' => []];
            }
        }
        foreach ($byAccount as $acctId => $amt) {
            if (in_array($acctId, $gstPairedIds, true)) { continue; }
            $accountType = (int)Yii::$app->db->createCommand(
                "SELECT account_type FROM accounts_item WHERE id=:id", [':id' => $acctId]
            )->queryScalar();
            if ($accountType === 8) { continue; }
            $balance = $this->_cfAccountDebitBalance($acctId, $pid);
            if ($balance > 0) {
                $name = Yii::$app->db->createCommand(
                    "SELECT name FROM accounts_item WHERE id=:id",
                    [':id' => $acctId]
                )->queryScalar();
                $rows[] = [
                    'name' => $name ?: ('Account #' . $acctId),
                    'amount' => round($balance, 2),
                    'members' => $this->_cfAccountTransactionsDetail($acctId, $pid),
                ];
            }
        }
        usort($rows, function($a, $b) { return $b['amount'] <=> $a['amount']; });

        return json_encode([
            'error' => 'No',
            'accounts' => $rows,
        ]);
    }

    // Shared helper: every individual voucher/journalvoucher transaction
    // touching a single account head for a project, signed so a debit
    // (increases the account's debit balance) is positive and a credit
    // is negative — same net-effect convention as _cfAccountDebitBalance,
    // just broken out row-by-row instead of summed. Each row is labelled
    // "date · voucher_no · narration" (falling back to the voucher type
    // when narration is blank) for display in the Investments breakdown
    // hover tooltip. Ordered oldest-first so the tooltip reads like a
    // running ledger.
    private function _cfAccountTransactionsDetail($accountId, $pid)
    {
        $rows = Yii::$app->db->createCommand(
            "SELECT date, voucher_no, narration, type, amount, 1 AS sign
             FROM voucher WHERE account_id=:id AND contra=0 AND project_id=:pid
             UNION ALL
             SELECT date, voucher_no, narration, type, amount, -1 AS sign
             FROM voucher WHERE creditacnt=:id AND contra=0 AND project_id=:pid
             UNION ALL
             SELECT date, voucher_no, narration, type, amount, 1 AS sign
             FROM voucher WHERE account_id=:id AND contra=1 AND type='Receipt' AND project_id=:pid
             UNION ALL
             SELECT date, voucher_no, narration, type, amount, -1 AS sign
             FROM voucher WHERE creditacnt=:id AND contra=1 AND type='Payment' AND project_id=:pid
             UNION ALL
             SELECT date, voucher_no, narration, 'Journal' AS type, amount, 1 AS sign
             FROM journalvoucher WHERE debitacnt=:id AND project_id=:pid
             UNION ALL
             SELECT date, voucher_no, narration, 'Journal' AS type, amount, -1 AS sign
             FROM journalvoucher WHERE creditacnt=:id AND project_id=:pid
             ORDER BY date ASC",
            [':id' => $accountId, ':pid' => $pid]
        )->queryAll();

        $members = [];
        foreach ($rows as $r) {
            $label = date('d-M-Y', strtotime($r['date'])) . ' · ' . ($r['voucher_no'] ?: $r['type']);
            if (!empty($r['narration'])) { $label .= ' · ' . $r['narration']; }
            $members[] = [
                'name' => $label,
                'amount' => round(((float)$r['sign']) * (float)$r['amount'], 2),
            ];
        }
        return $members;
    }

    // Shared helper: the Bills Receivable value — the current balance of
    // the account head that has a CREDIT posting against "Contract
    // Income" through a JOURNAL entry (journalvoucher only) for the
    // selected project. There is only ever one such debit account per
    // project. Returns 0 if there's no "Contract Income" account or no
    // qualifying journal entry yet. Shared by actionCfbillsreceivable
    // and actionCfwip (which subtracts this from the WIP total — see
    // Shared helper: the date of the MOST RECENT journal entry crediting
    // "Contract Income" for this project — the start of the current,
    // still-open WIP accrual cycle. Every time Contract Income is
    // credited, the WIP accumulated up to that date is considered
    // billed out (at its real cost, not the invoice/quoted value — see
    // actionCfwip's comment) and permanently excluded from future WIP
    // totals; only transactions dated AFTER this date belong to the
    // still-open cycle. Returns null if there's no "Contract Income"
    // account or no credit entry yet (the whole project-to-date range is
    // one open cycle in that case).
    private function _cfLatestContractIncomeDate($pid)
    {
        $contractIncomeId = Yii::$app->db->createCommand(
            "SELECT id FROM accounts_item WHERE name='Contract Income' AND Status=0 LIMIT 1"
        )->queryScalar();
        if (!$contractIncomeId) {
            return null;
        }

        $latestDate = Yii::$app->db->createCommand(
            "SELECT MAX(date) FROM journalvoucher WHERE creditacnt = :cid AND project_id = :pid",
            [':cid' => (int)$contractIncomeId, ':pid' => $pid]
        )->queryScalar();

        return $latestDate ?: null;
    }

    // Shared helper: every distinct date on which a journal entry
    // credited "Contract Income" for this project, in ascending order —
    // the full list of WIP accrual cycle boundaries (see
    // _cfLatestContractIncomeDate, which just returns the last one of
    // this list). Used by _cfNetInvestmentClosedCyclesWip to walk each
    // now-closed cycle in turn. Returns an empty array if there's no
    // "Contract Income" account or no credit entries yet.
    private function _cfAllContractIncomeDates($pid)
    {
        $contractIncomeId = Yii::$app->db->createCommand(
            "SELECT id FROM accounts_item WHERE name='Contract Income' AND Status=0 LIMIT 1"
        )->queryScalar();
        if (!$contractIncomeId) {
            return [];
        }

        return Yii::$app->db->createCommand(
            "SELECT DISTINCT date FROM journalvoucher WHERE creditacnt = :cid AND project_id = :pid ORDER BY date ASC",
            [':cid' => (int)$contractIncomeId, ':pid' => $pid]
        )->queryColumn();
    }

    // Shared helper: every date the client actually PAID (a Bank receipt
    // — a plain, non-contra voucher debiting a Bank account and crediting
    // the "Client" account, i.e. the debit side of the Contract Income
    // journal entry — same single-account assumption
    // _cfCollectionCycleDaysAverage already makes for this schema), as
    // opposed to _cfAllContractIncomeDates (bill-RAISED dates). Used as
    // the Total Investment per-cycle boundary — cycles are bounded by
    // when cash actually lands, not when it was invoiced, per explicit
    // direction. Returns an empty array if there's no Contract Income
    // account, no Client account, or no payments received yet.
    private function _cfAllContractIncomePaymentDates($pid)
    {
        $contractIncomeId = Yii::$app->db->createCommand(
            "SELECT id FROM accounts_item WHERE name='Contract Income' AND Status=0 LIMIT 1"
        )->queryScalar();
        if (!$contractIncomeId) { return []; }

        $clientAcctId = Yii::$app->db->createCommand(
            "SELECT j.debitacnt FROM journalvoucher j
             WHERE j.creditacnt = :cid AND j.project_id = :pid
             LIMIT 1",
            [':cid' => (int)$contractIncomeId, ':pid' => $pid]
        )->queryScalar();
        if (!$clientAcctId) { return []; }

        return Yii::$app->db->createCommand(
            "SELECT DISTINCT v.date FROM voucher v
             INNER JOIN accounts_item ai ON ai.id = v.account_id
             WHERE ai.account_type = 2 AND v.creditacnt = :cid
               AND v.contra = 0 AND v.project_id = :pid
             ORDER BY v.date ASC",
            [':cid' => (int)$clientAcctId, ':pid' => $pid]
        )->queryColumn();
    }

    // Core Work In Progress computation for an arbitrary accrual window:
    // transactions dated strictly AFTER $sinceDate, up to and including
    // $uptoDate (pass null for either bound to mean unbounded on that
    // side). Two components, both restricted to real Bank/Cash payment
    // vouchers (contra=0, credit side account_type IN 1,2):
    //   1. ALL CASH AND BANK EXPENSES — debit side is an Expense-type
    //      account (account_type=8) — always WIP regardless of balance.
    //   2. Payments made against any OTHER account head (see
    //      _cfBankCashPaymentsByDebitAccount) whose own CURRENT LEDGER
    //      BALANCE (vouchers + journals, full picture) is credit or
    //      zero — meaning the benefit has already been received and
    //      this payment is clearing/part-settling that liability. An
    //      account still sitting at a genuine open DEBIT balance (an
    //      advance/deposit not yet consumed) is excluded — that counts
    //      as an Investment instead (see actionCfcurrentassets).
    // Shared by actionCfwip (open cycle: sinceDate=latest boundary,
    // uptoDate=null) and _cfNetInvestmentClosedCyclesWip (each closed
    // cycle in turn).
    private function _cfWipForWindow($pid, $sinceDate, $uptoDate)
    {
        $byAccount = $this->_cfBankCashPaymentsByDebitAccount($pid, $sinceDate, $uptoDate);

        $total = 0;
        foreach ($byAccount as $acctId => $amt) {
            $accountType = (int)Yii::$app->db->createCommand(
                "SELECT account_type FROM accounts_item WHERE id=:id", [':id' => $acctId]
            )->queryScalar();
            if ($accountType === 8) {
                $total += $amt;
                continue;
            }
            $balance = $this->_cfAccountDebitBalance($acctId, $pid);
            if ($balance <= 0) {
                $total += $amt;
            }
        }

        return max(0, $total);
    }

    // Net Investment panel: Investments + WIP + Bills Receivable — see
    // _cfNetInvestmentAsOf below for the exact formula.
    //
    // Target reuses the Bank-Working Capital target (bank_wcapital)
    // rather than having its own — Net Investment and Working Capital
    // represent the same capital commitment viewed from different
    // angles.
    public function actionCfnetinvestment()
    {
        $uid = Yii::$app->user->Id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) { return json_encode(['error' => 'No project selected']); }
        $pid = (int)$projuser->projectid;
        // Agreed cashflow rules: Total Investment = Advances and Deposits + WIP (= money tied up).
        return json_encode(['error' => 'No', 'total' => round($this->_cfFlowGroups($pid)['ti'], 2)]);

        $total = $this->_cfNetInvestmentAsOf($pid, null);

        return json_encode([
            'error' => 'No',
            'total' => round($total, 2),
        ]);
    }

    // Core Total/Net Investment computation, reconstructed as of an
    // arbitrary date ($uptoDate — pass null for "as of today", which is
    // what the live actionCfnetinvestment dial uses). Formula:
    // Investments-as-of-that-date + WIP-as-of-that-date +
    // Bills-Receivable-as-of-that-date — the same three panel figures
    // shown elsewhere on this dashboard, using the SAME formulas
    // (Investments: Bank/Cash payments grouped by debit account, kept
    // where that account's own balance is debit — see
    // actionCfcurrentassets; WIP: Bank/Cash payments to Expense accounts
    // plus payments to any other account now at a credit/zero balance,
    // current open cycle only; Bills Receivable: client account balance,
    // GST excluded). Used both by the live dial (uptoDate=null) and by
    // the Total Investment breakdown popup (uptoDate = each past cycle's
    // own closing date).
    private function _cfNetInvestmentAsOf($pid, $uptoDate)
    {
        // Investments component — same rule as the live actionCfcurrentassets
        // dial (Bank/Cash payments grouped by debit account, kept only
        // where that account's own current balance is debit), capped to
        // $uptoDate so historical cycle bars reconstruct correctly too.
        $investments = $this->_cfGstNetInvestments($pid);
        $gstPairedIds = $this->_cfGstPairedAccountIds();
        $byAccount = $this->_cfBankCashPaymentsByDebitAccount($pid, null, $uptoDate);
        foreach ($byAccount as $acctId => $amt) {
            if (in_array($acctId, $gstPairedIds, true)) { continue; }
            $accountType = (int)Yii::$app->db->createCommand(
                "SELECT account_type FROM accounts_item WHERE id=:id", [':id' => $acctId]
            )->queryScalar();
            if ($accountType === 8) { continue; }
            $balance = $uptoDate
                ? $this->_cfAccountDebitBalanceAsOf($acctId, $pid, $uptoDate)
                : $this->_cfAccountDebitBalance($acctId, $pid);
            if ($balance > 0) {
                $investments += $balance;
            }
        }

        // WIP here must be the CURRENT OPEN CYCLE ONLY (same window
        // actionCfwip itself uses) — transactions dated strictly after
        // the latest Contract Income boundary at or before $uptoDate.
        // Cost accrued before that boundary was already billed out and
        // is what Bills Receivable is measuring; counting it again here
        // via an unbounded WIP window double-counted the same money in
        // both terms.
        $boundaries = $this->_cfAllContractIncomeDates($pid);
        $latestBoundaryUpTo = null;
        foreach ($boundaries as $b) {
            if (!$uptoDate || $b <= $uptoDate) { $latestBoundaryUpTo = $b; }
        }
        $wip = $this->_cfWipForWindow($pid, $latestBoundaryUpTo, $uptoDate);

        $billsReceivable = $this->_cfBillsReceivableAsOf($pid, $uptoDate);

        // Net Investment = (Investments + WIP + Stock + Bills Receivable) - Current
        // Liabilities, as of the same date. Can be negative (project funded by its
        // creditors). Stock has no formula yet, so it counts as 0 for now.
        $stock = 0;
        $currentLiabilities = $this->_cfCurrentLiabilitiesAsOf($pid, $uptoDate);

        return ($investments + $wip + $stock + $billsReceivable) - $currentLiabilities;
    }

    // Current Liabilities as of a date (null = today, the same as the live
    // Current Liabilities dial). Same rule as _cfCurrentLiabilitiesTotal, with
    // every balance taken up to $uptoDate.
    private function _cfCurrentLiabilitiesAsOf($pid, $uptoDate)
    {
        if (!$uptoDate) { return $this->_cfCurrentLiabilitiesTotal($pid); }
        // Called once per day by the per-cycle daily averages, so every account's
        // balance is read in one query (same rules as _cfAccountDebitBalanceAsOf).
        $rows = Yii::$app->db->createCommand(
            "SELECT acct, SUM(amt) AS bal FROM (
                SELECT account_id AS acct,  amount AS amt FROM voucher WHERE contra=0 AND project_id=:p1 AND date(date) <= :u1
                UNION ALL SELECT creditacnt, -amount FROM voucher WHERE contra=0 AND project_id=:p2 AND date(date) <= :u2
                UNION ALL SELECT account_id,  amount FROM voucher WHERE contra=1 AND type='Receipt' AND project_id=:p3 AND date(date) <= :u3
                UNION ALL SELECT creditacnt, -amount FROM voucher WHERE contra=1 AND type='Payment' AND project_id=:p4 AND date(date) <= :u4
                UNION ALL SELECT debitacnt,   amount FROM journalvoucher WHERE project_id=:p5 AND date(date) <= :u5
                UNION ALL SELECT creditacnt, -amount FROM journalvoucher WHERE project_id=:p6 AND date(date) <= :u6
             ) t GROUP BY acct",
            [':p1' => $pid, ':u1' => $uptoDate, ':p2' => $pid, ':u2' => $uptoDate, ':p3' => $pid, ':u3' => $uptoDate,
             ':p4' => $pid, ':u4' => $uptoDate, ':p5' => $pid, ':u5' => $uptoDate, ':p6' => $pid, ':u6' => $uptoDate]
        )->queryAll();
        $bal = [];
        foreach ($rows as $r) { $bal[(int)$r['acct']] = (float)$r['bal']; }

        static $types = null;
        if ($types === null) {
            $types = [];
            foreach (Yii::$app->db->createCommand("SELECT id, account_type FROM accounts_item")->queryAll() as $t) {
                $types[(int)$t['id']] = (int)$t['account_type'];
            }
        }

        $total = 0;
        foreach ($this->_cfGstAccountPairs() as $pair) {
            $net = ($bal[$pair[0]] ?? 0) + ($bal[$pair[1]] ?? 0);
            if ($net < 0) { $total += -$net; }
        }
        $gstPairedIds = $this->_cfGstPairedAccountIds();
        foreach ($bal as $acctId => $balance) {
            if (in_array($acctId, $gstPairedIds, true)) { continue; }
            $accountType = $types[$acctId] ?? 0;
            if ($accountType === 2 || $accountType === 4) { continue; }
            if ($balance < 0) { $total += -$balance; }
        }
        return $total;
    }

    // Total Investment panel breakdown (Cashflow Dashboard popup): one
    // bar per bill actually raised — each CLOSED cycle's own TRUE
    // DAY-BY-DAY AVERAGE Total Investment balance (see
    // _cfNetInvestmentPerCycleRows/_cfDailyAverageNetInvestment), not
    // just its closing-day snapshot. NO "Current" bar for the still-open
    // cycle among the plotted bars, per earlier explicit direction —
    // only bars for bills that have actually been raised. Each bar is
    // labelled by the month/year its cycle closed. The "average" field
    // returned here (shown as "Current:" in the popup) is the still-open
    // current cycle's OWN day-by-day average so far — the same "Current"
    // row _cfNetInvestmentPerCycleRows already computes, reused here
    // rather than re-fetched as today's raw snapshot.
    public function actionCfnetinvestmentbreakdown()
    {
        $uid = Yii::$app->user->Id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) { return json_encode(['error' => 'No project selected']); }
        $pid = (int)$projuser->projectid;
        // One bar per billing cycle of the whole project (Project duration / Billing
        // cycle, from the earliest activity start — same cycles as the Project Cost
        // popup):
        //   target = Advances and Deposits target + that cycle's planned cost (WIP target)
        //   actual = Total Investment (Advances + WIP) at the cycle end, as of today
        //            for the current cycle, none for cycles still to come.
        $w = $this->actionCfwipcycles();
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $advTarget = 100000 * (float)Yii::$app->db->createCommand(
            "SELECT target_value FROM cf_dashboard_targets WHERE project_id = :p AND metric_key = 'investments_target'", [':p' => $pid]
        )->queryScalar();
        $cur = (int)($w['current_cycle'] ?? 0);
        $today = date('Y-m-d');
        $cycles = [];
        foreach (($w['cycles'] ?? []) as $c) {
            $k = (int)$c['cycle'];
            // Actual Advances and Deposits and WIP at the cycle end (today for the
            // current cycle); none for cycles still to come.
            $g = null;
            if ($k < $cur) $g = $this->_cfFlowGroups($pid, $c['end_date']);
            elseif ($k === $cur) $g = $this->_cfFlowGroups($pid);
            $cycles[] = ['name' => 'C' . $k . ' ' . date('M', strtotime($c['end_date'])), 'end_date' => $c['end_date'],
                         'target' => round($advTarget + (float)$c['target'], 2), 'adv' => round($advTarget, 2), 'wip' => round((float)$c['target'], 2),
                         'end_label' => $c['end_label'] ?? '', 'current' => $k === $cur,
                         'actual' => $g ? round($g['ti'], 2) : null,
                         'actual_adv' => $g ? round($g['adv'], 2) : null, 'actual_wip' => $g ? round($g['wip'], 2) : null,
                         'actual_br' => $g ? round($g['br'], 2) : null];
        }
        $avg = $this->_cfAverageInvestment($pid);
        return json_encode(['error' => 'No', 'cycles' => $cycles, 'adv_target' => $advTarget, 'current_cycle' => $cur,
                            'avg_ti' => $avg['avg'], 'avg_from' => $avg['from']]);

        $allRows = $this->_cfNetInvestmentPerCycleRows($pid);
        $rows = array_values(array_filter($allRows, function($r) { return $r['name'] !== 'Current'; }));
        $currentRow = array_values(array_filter($allRows, function($r) { return $r['name'] === 'Current'; }));
        $average = $currentRow ? $currentRow[0]['amount'] : 0;

        return json_encode([
            'error' => 'No',
            'accounts' => $rows,
            'average' => round($average, 2),
        ]);
    }

    // Shared helper: one {name, amount} row per Total Investment cycle —
    // "amount" is each cycle's own TRUE DAY-BY-DAY AVERAGE Total
    // Investment balance, not just its closing-day value. Per explicit
    // request: a cycle's average investment = the mean of
    // _cfNetInvestmentAsOf() evaluated on EVERY individual day the cycle
    // was open (day 1's balance + day 2's + ... + day N's, divided by N),
    // which represents how much capital was actually tied up on average
    // during that cycle rather than just its ending snapshot.
    //
    // Cycle boundaries here are PAYMENT-RECEIVED dates (see
    // _cfAllContractIncomePaymentDates — when the client's cash actually
    // lands), NOT bill-raised dates — a deliberate departure from
    // _cfWipCycleDaysAverage/_cfCollectionCycleDaysAverage's own
    // bill-to-bill boundaries, per explicit direction: "the new cycle's
    // investment value starts from the day contract income is received."
    // Cycle 1 starts at the project's first Bank/Cash transaction (same
    // start point C2C/WIP use); every cycle after that starts ON the
    // previous cycle's own payment-received date. The CLOSING cycle's
    // averaging window stops the day BEFORE that payment date, not on
    // it — per explicit direction: once the receivable clears, the cycle
    // is done, and any spending that also happens to post on that same
    // calendar day (e.g. vendor advances paid right after the client's
    // payment lands) belongs to the NEW cycle, not the closing one. So
    // the payment date itself is exclusively the new cycle's day 1, never
    // counted toward the cycle that just closed. The still-open current
    // cycle is averaged from its own start (the latest payment date, or
    // Cycle 1's start if no payment has landed yet) through today.
    // Deliberately O(days) calls to _cfNetInvestmentAsOf per cycle —
    // slower than a single snapshot, but exact, per explicit direction.
    // Same rows feed both actionCfnetinvestmentbreakdown's popup AND
    // actionCfreecash/actionCfroi's "Average Investment" denominator, so
    // both stay consistent with each other.
    private function _cfNetInvestmentPerCycleRows($pid)
    {
        $boundaries = $this->_cfAllContractIncomePaymentDates($pid);
        $firstDate = $this->_cfProjectFirstBankCashDate($pid);
        if (!$firstDate) { return []; }

        $rows = [];
        $cycleStart = $firstDate;
        foreach ($boundaries as $b) {
            // Closing cycle's average stops the day BEFORE the payment
            // lands, not on the payment date itself — per explicit
            // direction: once the receivable clears, the cycle is done,
            // and any spending that also happens to post on that same
            // calendar day belongs to the NEW cycle, not this closing
            // one (otherwise same-day vendor advances paid right after
            // the client's payment lands would inflate the closing
            // cycle's figure with money that was never part of it).
            $closeDate = date('Y-m-d', strtotime($b . ' -1 day'));
            $rows[] = [
                'name' => date('M Y', strtotime($b)),
                'amount' => round($this->_cfDailyAverageNetInvestment($pid, $cycleStart, $closeDate), 2),
            ];
            // Next cycle starts ON the payment-received date itself.
            $cycleStart = $b;
        }
        // Current still-open cycle: averaged from its own start day
        // through today.
        $today = date('Y-m-d');
        $rows[] = [
            'name' => 'Current',
            'amount' => round($this->_cfDailyAverageNetInvestment($pid, $cycleStart, $today), 2),
        ];

        return $rows;
    }

    // True day-by-day mean of _cfNetInvestmentAsOf() across every date
    // from $startDate to $endDate inclusive — see _cfNetInvestmentPerCycleRows
    // for why. Returns 0 if the range is empty/inverted.
    private function _cfDailyAverageNetInvestment($pid, $startDate, $endDate)
    {
        $start = strtotime($startDate);
        $end = strtotime($endDate);
        if ($end < $start) { return 0; }

        $sum = 0;
        $count = 0;
        for ($t = $start; $t <= $end; $t += 86400) {
            $sum += $this->_cfNetInvestmentAsOf($pid, date('Y-m-d', $t));
            $count++;
        }
        return $count ? $sum / $count : 0;
    }

    // Shared helper: plain average of _cfNetInvestmentPerCycleRows'
    // amounts — the "Average Investment" figure ROI's and Free Cash's
    // percentages are measured against (see actionCfroi/actionCfreecash).
    // Per explicit direction, ROI/Free Cash are only ever measured
    // against COMPLETED cycles — the still-open "Current" row is
    // excluded here, even though it's still included in the Total
    // Investment breakdown popup's own bars/"Current:" label
    // (actionCfnetinvestmentbreakdown reads _cfNetInvestmentPerCycleRows
    // directly, unaffected by this exclusion). Returns 0 if no cycle has
    // completed yet — nothing to measure ROI/Free Cash against.
    private function _cfNetInvestmentAverage($pid, $rows = null)
    {
        if ($rows === null) { $rows = $this->_cfNetInvestmentPerCycleRows($pid); }
        $rows = array_values(array_filter($rows, function($r) { return $r['name'] !== 'Current'; }));
        $sum = 0;
        foreach ($rows as $r) { $sum += $r['amount']; }
        return count($rows) ? $sum / count($rows) : 0;
    }

    // Free Cash panel (Cashflow Dashboard): a PERCENTAGE dial, same
    // presentation pattern as Contribution Margin. The money you "take
    // home" — the return before tax — after every investment and
    // liability is accounted for.
    //
    // Free Cash (rupee value) = Contribution Margin (see
    // _cfContributionMarginValue) MINUS Total Investment (see
    // _cfNetInvestmentAsOf, as of today — same figure as the live Total
    // Investment dial) MINUS Current Liabilities (see
    // _cfCurrentLiabilitiesTotal).
    //
    // The dial's needle position/percentage shown at its center = (Free
    // Cash / Average Investment) * 100, where Average Investment is the
    // SAME "Average" figure shown in the Total Investment breakdown
    // popup (see _cfNetInvestmentAverage — the plain average of every
    // cycle's own closing Total Investment value, including the current
    // open cycle).
    //
    // The rupee value itself is shown to the LEFT of the dial (Actual),
    // in Lakhs if <= 99 Lakhs or Crores if greater. Target is a
    // user-editable PERCENTAGE (not a rupee value), saved under
    // metric_key "free_cash_target".
    public function actionCfreecash()
    {
        $uid = Yii::$app->user->Id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) { return json_encode(['error' => 'No project selected']); }
        $pid = (int)$projuser->projectid;

        $freeCashValue = $this->_cfFreeCashValue($pid);
        $averageInvestment = $this->_cfNetInvestmentAverage($pid);
        $freeCashPct = $averageInvestment > 0 ? ($freeCashValue / $averageInvestment) * 100 : 0;

        return json_encode([
            'error' => 'No',
            'value' => round($freeCashValue, 2),
            'percent' => round($freeCashPct, 2),
            // Lets the frontend convert the user-editable percentage
            // Target into its rupee equivalent for display, without
            // re-deriving Average Investment client-side.
            'average_investment' => round($averageInvestment, 2),
        ]);
    }

    // Shared helper: Free Cash's rupee value — Contribution Margin (see
    // _cfContributionMarginValue) MINUS (Total Investment (see
    // _cfNetInvestmentAsOf, as of today) PLUS Current Liabilities (see
    // _cfCurrentLiabilitiesTotal)). Current Liabilities was missing from
    // this formula entirely until this fix — the surrounding comments
    // always described it as part of Free Cash, but the code only ever
    // subtracted Total Investment.
    private function _cfFreeCashValue($pid)
    {
        $contributionMargin = $this->_cfContributionMarginValue($pid);
        $totalInvestment = $this->_cfNetInvestmentAsOf($pid, null);
        $currentLiabilities = $this->_cfCurrentLiabilitiesTotal($pid);
        return $contributionMargin - ($totalInvestment + $currentLiabilities);
    }

    // ROI panel (Cashflow Dashboard): a PERCENTAGE dial, same
    // presentation pattern as Contribution Margin/Free Cash. ROI =
    // (Contribution Margin / Average Investment) * 100 — same Average
    // Investment figure used by Free Cash's own percentage (see
    // _cfNetInvestmentAverage — the plain average of every WIP accrual
    // cycle's own closing Total Investment value, including the current
    // open cycle). The rupee value shown to the LEFT of the dial
    // (Actual) is Contribution Margin's own rupee value (see
    // _cfContributionMarginValue) — ROI is a ratio of that same figure,
    // not a separate rupee amount of its own. Target is a user-editable
    // PERCENTAGE, saved under metric_key "roi_target".
    public function actionCfroi()
    {
        $uid = Yii::$app->user->Id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) { return json_encode(['error' => 'No project selected']); }
        $pid = (int)$projuser->projectid;

        $contributionMarginValue = $this->_cfContributionMarginValue($pid);
        $averageInvestment = $this->_cfNetInvestmentAverage($pid);
        $roiPct = $averageInvestment > 0 ? ($contributionMarginValue / $averageInvestment) * 100 : 0;

        return json_encode([
            'error' => 'No',
            'value' => round($contributionMarginValue, 2),
            'percent' => round($roiPct, 2),
        ]);
    }

    // Client account's balance, EXCLUDING journal lines that credited a
    // GST account (Output CGST/SGST/IGST, matched by name — same
    // convention as the Current Liabilities GST mapping). A bill raised
    // debits the client for the base amount PLUS GST; the GST portion
    // isn't money owed to the project — it's tax collected on the
    // government's behalf and passed straight to Current Liabilities —
    // so it must not inflate Bills Receivable. Once the client actually
    // pays, the payment (a plain credit to this account, no GST
    // account involved) reduces the full balance including the GST
    // that had never been added here, which is the correct net effect.
    private function _cfClientAccountBalanceExcludingGst($accountId, $pid, $uptoDate = null)
    {
        $gstAcctIds = Yii::$app->db->createCommand(
            "SELECT id FROM accounts_item WHERE name LIKE '%GST%'"
        )->queryColumn();
        $gstFilter = '';
        if (!empty($gstAcctIds)) {
            $placeholders = implode(',', array_map('intval', $gstAcctIds));
            $gstFilter = " AND creditacnt NOT IN ($placeholders)";
        }

        $dateCond = $uptoDate ? " AND date(date) <= :ud" : "";
        $params = [':id' => $accountId, ':pid' => $pid];
        if ($uptoDate) { $params[':ud'] = $uptoDate; }

        $sum = function($sql) use ($params) {
            $v = Yii::$app->db->createCommand($sql, $params)->queryScalar();
            return $v ? (float)$v : 0;
        };
        $debit  = $sum("SELECT SUM(amount) FROM voucher WHERE account_id=:id AND contra=0 AND project_id=:pid" . $dateCond);
        $credit = $sum("SELECT SUM(amount) FROM voucher WHERE creditacnt=:id AND contra=0 AND project_id=:pid" . $dateCond);
        $contraDebit  = $sum("SELECT SUM(amount) FROM voucher WHERE account_id=:id AND contra=1 AND type='Receipt' AND project_id=:pid" . $dateCond);
        $contraCredit = $sum("SELECT SUM(amount) FROM voucher WHERE creditacnt=:id AND contra=1 AND type='Payment' AND project_id=:pid" . $dateCond);
        $journalDebit  = $sum("SELECT SUM(amount) FROM journalvoucher WHERE debitacnt=:id AND project_id=:pid" . $gstFilter . $dateCond);
        $journalCredit = $sum("SELECT SUM(amount) FROM journalvoucher WHERE creditacnt=:id AND project_id=:pid" . $dateCond);

        $totalDebit  = $debit + $contraDebit + $journalDebit;
        $totalCredit = $credit + $contraCredit + $journalCredit;
        return $totalDebit - $totalCredit;
    }

    private function _cfBillsReceivable($pid)
    {
        $contractIncomeId = Yii::$app->db->createCommand(
            "SELECT id FROM accounts_item WHERE name='Contract Income' AND Status=0 LIMIT 1"
        )->queryScalar();
        if (!$contractIncomeId) {
            return 0;
        }

        $debitAcctId = Yii::$app->db->createCommand(
            "SELECT j.debitacnt FROM journalvoucher j
             WHERE j.creditacnt = :cid AND j.project_id = :pid
             LIMIT 1",
            [':cid' => (int)$contractIncomeId, ':pid' => $pid]
        )->queryScalar();
        if (!$debitAcctId) {
            return 0;
        }

        $balance = $this->_cfClientAccountBalanceExcludingGst((int)$debitAcctId, $pid);
        return max(0, $balance);
    }

    // Same as _cfBillsReceivable, but reconstructed as of an arbitrary
    // date ($uptoDate — pass null for "as of today", same as the
    // unbounded version). Used by actionCfbillsreceivablebreakdown for
    // the per-cycle breakdown popup.
    private function _cfBillsReceivableAsOf($pid, $uptoDate)
    {
        if (!$uptoDate) {
            return $this->_cfBillsReceivable($pid);
        }
        $contractIncomeId = Yii::$app->db->createCommand(
            "SELECT id FROM accounts_item WHERE name='Contract Income' AND Status=0 LIMIT 1"
        )->queryScalar();
        if (!$contractIncomeId) {
            return 0;
        }

        $debitAcctId = Yii::$app->db->createCommand(
            "SELECT j.debitacnt FROM journalvoucher j
             WHERE j.creditacnt = :cid AND j.project_id = :pid
             LIMIT 1",
            [':cid' => (int)$contractIncomeId, ':pid' => $pid]
        )->queryScalar();
        if (!$debitAcctId) {
            return 0;
        }

        $balance = $this->_cfClientAccountBalanceExcludingGst((int)$debitAcctId, $pid, $uptoDate);
        return max(0, $balance);
    }

    // Bills Receivable panel (Cashflow Dashboard): see _cfBillsReceivable.
    public function actionCfbillsreceivable()
    {
        $uid = Yii::$app->user->Id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) { return json_encode(['error' => 'No project selected']); }
        $pid = (int)$projuser->projectid;
        // Agreed cashflow rules: Bills Receivable = balance of the Bills Receivable head (0 if credit).
        return json_encode(['error' => 'No', 'total' => round($this->_cfFlowGroups($pid)['br'], 2)]);

        return json_encode([
            'error' => 'No',
            'total' => round($this->_cfBillsReceivable($pid), 2),
        ]);
    }

    // Bills Receivable panel breakdown (Cashflow Dashboard popup): one
    // bar per bill actually raised — each bar valued at the Bills
    // Receivable balance AS OF that bill's own Contract Income credit
    // date (see _cfBillsReceivableAsOf). NO "Current" bar for the
    // still-open cycle — only bars for bills that have actually been
    // raised, per explicit direction. Bars are labelled by the month/
    // year each bill was raised. Also returns the plain average of all
    // returned bar values.
    public function actionCfbillsreceivablebreakdown()
    {
        $uid = Yii::$app->user->Id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) { return json_encode(['error' => 'No project selected']); }
        $pid = (int)$projuser->projectid;

        $boundaries = $this->_cfAllContractIncomeDates($pid);

        $rows = [];
        foreach ($boundaries as $b) {
            $value = $this->_cfBillsReceivableAsOf($pid, $b);
            $label = date('M Y', strtotime($b));
            $rows[] = ['name' => $label, 'amount' => round($value, 2)];
        }

        $sum = 0;
        foreach ($rows as $r) { $sum += $r['amount']; }
        $average = count($rows) ? $sum / count($rows) : 0;

        return json_encode([
            'error' => 'No',
            'accounts' => $rows,
            'average' => round($average, 2),
        ]);
    }

    // Contract Income panel (Cashflow Dashboard): the CUMULATIVE sum of
    // every transaction crediting "Contract Income" for this project to
    // date — see _cfLatestContractIncomeAmount. Each cycle's own bill
    // adds to a running total; this is NOT a single cycle's own amount.
    public function actionCfcontractincome()
    {
        $uid = Yii::$app->user->Id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) { return json_encode(['error' => 'No project selected']); }
        $pid = (int)$projuser->projectid;

        return json_encode([
            'error' => 'No',
            'total' => round($this->_cfLatestContractIncomeAmount($pid), 2),
        ]);
    }

    // Contract Income panel breakdown (Cashflow Dashboard popup): one
    // bar per Contract Income credit transaction (voucher or
    // journalvoucher), in date order, labelled by the month/year it was
    // credited. Each bar's VALUE is the CUMULATIVE (running) total up to
    // and including that credit — not that single cycle's own billed
    // amount — matching Contract Income's accumulated nature (each new
    // bill adds to a running total). Also returns the plain average of
    // these cumulative bar values and the final cumulative total.
    public function actionCfcontractincomebreakdown()
    {
        $uid = Yii::$app->user->Id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) { return json_encode(['error' => 'No project selected']); }
        $pid = (int)$projuser->projectid;

        $contractIncomeId = Yii::$app->db->createCommand(
            "SELECT id FROM accounts_item WHERE name='Contract Income' AND Status=0 LIMIT 1"
        )->queryScalar();
        if (!$contractIncomeId) {
            return json_encode(['error' => 'No', 'accounts' => [], 'average' => 0, 'cumulative' => 0]);
        }

        $credits = Yii::$app->db->createCommand(
            "SELECT amount, txn_date FROM (
                 SELECT v.amount AS amount, v.date AS txn_date FROM voucher v
                     WHERE v.creditacnt = :cid1 AND v.contra = 0 AND v.project_id = :pid1
                 UNION ALL
                 SELECT j.amount AS amount, j.date AS txn_date FROM journalvoucher j
                     WHERE j.creditacnt = :cid2 AND j.project_id = :pid2
             ) t
             ORDER BY t.txn_date ASC",
            [':cid1' => (int)$contractIncomeId, ':pid1' => $pid, ':cid2' => (int)$contractIncomeId, ':pid2' => $pid]
        )->queryAll();

        $rows = [];
        $cumulative = 0;
        $sumOfBars = 0;
        foreach ($credits as $c) {
            $cycleAmount = (float)$c['amount'];
            $cumulative += $cycleAmount;
            $rows[] = [
                'name' => date('M Y', strtotime($c['txn_date'])),
                'amount' => round($cumulative, 2),
                // Each bill's own standalone amount for this one cycle
                // (not summed with prior cycles) — shown as a paired bar
                // alongside the cumulative 'amount' above.
                'cycle_amount' => round($cycleAmount, 2),
            ];
            $sumOfBars += $cumulative;
        }
        $average = count($rows) ? $sumOfBars / count($rows) : 0;

        return json_encode([
            'error' => 'No',
            'accounts' => $rows,
            'average' => round($average, 2),
            'cumulative' => round($cumulative, 2),
        ]);
    }

    // Shared helper: the SAME "Project Expenditure Total" computation as
    // FinancerequestsController::actionCfprojectexpenditure — every
    // account under a master_id=1 accounts_sub subgroup (excluding
    // "Investments"), account_type IN (5,8,6), summed by closing
    // balance — duplicated here (controller-local, matching this
    // codebase's existing pattern) rather than called cross-controller,
    // WITH the same latest-Contract-Income-credit date cap: once
    // Contract Income has been credited at least once, the sum only
    // covers expenses from project inception through that date. Used by
    // actionCfcontributionmargin.
    private function _cfProjectExpenditureTotal($pid, $capDate)
    {
        $startdate = $capDate ? '2013-04-01' : '';
        $enddate = $capDate ? $capDate : '';

        $subgroups = Yii::$app->db->createCommand(
            "SELECT id FROM accounts_sub WHERE master_id='1' AND Status=0 AND name != 'Investments'"
        )->queryColumn();

        $total = 0;
        foreach ($subgroups as $subgrpId) {
            $accounts = Yii::$app->db->createCommand(
                "SELECT DISTINCT b.id FROM subgroup_accounts AS a
                 INNER JOIN accounts_item AS b ON a.account_id = b.id
                 WHERE a.subgrp_id=:sid AND a.accountschedule_id=0 AND b.account_type IN (5,8,6)",
                [':sid' => $subgrpId]
            )->queryColumn();

            foreach ($accounts as $acctId) {
                $openingbal = Yii::$app->db->createCommand(
                    "SELECT balance FROM ledger_openingbalance WHERE accountid=:id",
                    [':id' => $acctId]
                )->queryScalar();
                $openingbal = $openingbal ?: 0;
                $closingbal = $openingbal + $this->_cfClosingBalanceForRange((int)$acctId, $pid, $startdate, $enddate);
                $total += abs($closingbal);
            }
        }
        return $total;
    }

    // Same date-range/credit-minus-debit logic as
    // FinancerequestsController::closingbalance, duplicated here
    // (controller-local) for _cfProjectExpenditureTotal's use.
    private function _cfClosingBalanceForRange($acctId, $pid, $startdate, $enddate)
    {
        $initialdate = '2013-04-01';
        $dateCond = ($startdate !== '' && $enddate !== '')
            ? "date BETWEEN '" . $startdate . "' AND '" . $enddate . "'"
            : "date >= '" . $initialdate . "'";

        $rows = Yii::$app->db->createCommand(
            "(SELECT amount, type, creditacnt, contra, account_id FROM voucher
                WHERE (account_id=:id1 OR creditacnt=:id2) AND (project_id=:pid1) AND $dateCond)
             UNION ALL
             (SELECT amount, type, creditacnt, contra, debitacnt AS account_id FROM journalvoucher
                WHERE (debitacnt=:id3 OR creditacnt=:id4) AND (place=:pid2) AND $dateCond)",
            [':id1' => $acctId, ':id2' => $acctId, ':pid1' => $pid, ':id3' => $acctId, ':id4' => $acctId, ':pid2' => $pid]
        )->queryAll();

        $creditTotal = 0;
        $debitTotal = 0;
        foreach ($rows as $row) {
            if ((int)$row['contra'] === 0) {
                if ((int)$row['creditacnt'] === $acctId) { $creditTotal += (float)$row['amount']; }
                elseif ((int)$row['account_id'] === $acctId) { $debitTotal += (float)$row['amount']; }
            } else {
                if ($row['type'] === 'Payment' && (int)$row['creditacnt'] === $acctId) { $creditTotal += (float)$row['amount']; }
                elseif ($row['type'] === 'Receipt' && (int)$row['account_id'] === $acctId) { $debitTotal += (float)$row['amount']; }
            }
        }
        return $creditTotal - $debitTotal;
    }

    // Contribution Margin panel (Cashflow Dashboard): a PERCENTAGE dial.
    // Contribution Margin (rupee value) = Contract Income (see
    // actionCfcontractincome — the amount of the single most recent
    // transaction crediting Contract Income) MINUS Project Expenditure
    // (see _cfProjectExpenditureTotal — cumulative, capped at the latest
    // Contract Income credit date, same as the Project Expenditure
    // panel). The dial's needle position/percentage shown at its center
    // = (Contribution Margin / Contract Income) * 100. The rupee value
    // itself is shown below the dial, in Lakhs if <= 99 Lakhs or Crores
    // if greater. Target is a user-editable PERCENTAGE (not a rupee
    // value), saved under metric_key "contribution_margin_target".
    public function actionCfcontributionmargin()
    {
        $uid = Yii::$app->user->Id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) { return json_encode(['error' => 'No project selected']); }
        $pid = (int)$projuser->projectid;

        $marginValue = $this->_cfContributionMarginValue($pid);
        $contractIncome = $this->_cfLatestContractIncomeAmount($pid);
        $marginPct = $contractIncome > 0 ? ($marginValue / $contractIncome) * 100 : 0;

        return json_encode([
            'error' => 'No',
            'value' => round($marginValue, 2),
            'percent' => round($marginPct, 2),
        ]);
    }

    // Shared helper: the CUMULATIVE sum of every transaction crediting
    // "Contract Income" for this project to date — each cycle's own
    // billed amount adds to a running total (cycle 1's bill, plus cycle
    // 2's, plus cycle 3's, and so on), NOT just the latest single bill's
    // amount. Used by actionCfcontractincome and (via
    // _cfContributionMarginValue) actionCfcontributionmargin,
    // actionCfroi, and actionCfreecash — all four are worked out from
    // this same accumulated Contract Income figure.
    private function _cfLatestContractIncomeAmount($pid)
    {
        $contractIncomeId = Yii::$app->db->createCommand(
            "SELECT id FROM accounts_item WHERE name='Contract Income' AND Status=0 LIMIT 1"
        )->queryScalar();
        if (!$contractIncomeId) {
            return 0;
        }
        $total = Yii::$app->db->createCommand(
            "SELECT SUM(amount) FROM (
                 SELECT v.amount AS amount FROM voucher v
                     WHERE v.creditacnt = :cid1 AND v.contra = 0 AND v.project_id = :pid1
                 UNION ALL
                 SELECT j.amount AS amount FROM journalvoucher j
                     WHERE j.creditacnt = :cid2 AND j.project_id = :pid2
             ) t",
            [':cid1' => (int)$contractIncomeId, ':pid1' => $pid, ':cid2' => (int)$contractIncomeId, ':pid2' => $pid]
        )->queryScalar();
        return $total ? (float)$total : 0;
    }

    // Shared helper: Contribution Margin's rupee value — Contract Income
    // (see _cfLatestContractIncomeAmount) MINUS Project Expenditure (see
    // _cfProjectExpenditureTotal — cumulative, capped at the latest
    // Contract Income credit date). Extracted so actionCfreecash can
    // reuse it without duplicating actionCfcontributionmargin's logic.
    private function _cfContributionMarginValue($pid)
    {
        $contractIncome = $this->_cfLatestContractIncomeAmount($pid);
        $capDate = $this->_cfLatestContractIncomeDate($pid);
        $projectExpenditure = $this->_cfProjectExpenditureTotal($pid, $capDate);
        return $contractIncome - $projectExpenditure;
    }

    // Shared helper: every account head this project has EVER
    // transacted with — either side of a voucher OR a journalvoucher
    // row — used by the Current Liabilities scan (no subgroup
    // restriction; any account can be a liability if its own balance
    // says so).
    private function _cfAllProjectTouchedAccountIds($pid)
    {
        return Yii::$app->db->createCommand(
            "SELECT id FROM accounts_item ai WHERE
                EXISTS (SELECT 1 FROM voucher v WHERE (v.account_id=ai.id OR v.creditacnt=ai.id) AND v.project_id=:pid)
                OR EXISTS (SELECT 1 FROM journalvoucher j WHERE (j.debitacnt=ai.id OR j.creditacnt=ai.id) AND j.project_id=:pid)",
            [':pid' => $pid]
        )->queryColumn();
    }

    // Current Liabilities panel (Cashflow Dashboard): every account head
    // this project has ever transacted with (see
    // _cfAllProjectTouchedAccountIds — no subgroup restriction) whose
    // own current balance is a CREDIT balance is summed here (as a
    // positive magnitude) — EXCEPT Bank-type (account_type=2, e.g. a CC
    // facility — working capital, not a trade/other liability) and
    // Income-type (account_type=4) accounts, which are excluded
    // regardless of their balance sign.
    public function actionCfcurrentliabilities()
    {
        $uid = Yii::$app->user->Id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) { return json_encode(['error' => 'No project selected']); }
        $pid = (int)$projuser->projectid;
        // Agreed rule (2026-10-20): Current Liabilities = sum of CREDIT main-account
        // balances of every head except Bank (2), Income (4) and Expense (8) types. The
        // bank is excluded; Output GST is counted in full.
        $p = [];
        for ($i = 1; $i <= 6; $i++) { $p[':p' . $i] = $pid; }
        $rows = Yii::$app->db->createCommand("SELECT t.a, SUM(t.x) bal FROM (
                SELECT account_id a, amount x FROM voucher WHERE contra=0 AND project_id=:p1
                UNION ALL SELECT creditacnt, -amount FROM voucher WHERE contra=0 AND project_id=:p2
                UNION ALL SELECT account_id, amount FROM voucher WHERE contra=1 AND type='Receipt' AND project_id=:p3
                UNION ALL SELECT creditacnt, -amount FROM voucher WHERE contra=1 AND type='Payment' AND project_id=:p4
                UNION ALL SELECT debitacnt, amount FROM journalvoucher WHERE project_id=:p5
                UNION ALL SELECT creditacnt, -amount FROM journalvoucher WHERE project_id=:p6) t
             JOIN accounts_item ai ON ai.id = t.a
             WHERE ai.account_type NOT IN (2, 4, 8)
             GROUP BY t.a HAVING SUM(t.x) < 0", $p)->queryAll();
        $clTotal = 0;
        foreach ($rows as $r) { $clTotal += -(float)$r['bal']; }
        return json_encode(['error' => 'No', 'total' => round($clTotal, 2)]);

        return json_encode([
            'error' => 'No',
            'total' => round($this->_cfCurrentLiabilitiesTotal($pid), 2),
        ]);
    }

    // GST Input/Output account id pairs (matched by name — CGST/SGST/
    // IGST each have one Input account and one Output account). Each
    // pair is netted together (Input balance - Output balance) before
    // deciding Investment vs Current Liability — a net CREDIT goes to
    // Current Liabilities, a net DEBIT goes to Investments — rather than
    // treating the Input and Output legs as two independent accounts.
    // Returns [[inputId, outputId], ...].
    private function _cfGstAccountPairs()
    {
        static $pairs = null;
        if ($pairs !== null) { return $pairs; }
        $rows = Yii::$app->db->createCommand(
            "SELECT id, name FROM accounts_item WHERE name LIKE '%CGST%' OR name LIKE '%SGST%' OR name LIKE '%IGST%'"
        )->queryAll();
        $input = []; $output = [];
        foreach ($rows as $row) {
            foreach (['CGST', 'SGST', 'IGST'] as $tax) {
                if (stripos($row['name'], $tax) === false) { continue; }
                if (stripos($row['name'], 'Input') !== false) { $input[$tax] = (int)$row['id']; }
                if (stripos($row['name'], 'Output') !== false) { $output[$tax] = (int)$row['id']; }
            }
        }
        $pairs = [];
        foreach (['CGST', 'SGST', 'IGST'] as $tax) {
            if (isset($input[$tax]) && isset($output[$tax])) {
                $pairs[] = [$input[$tax], $output[$tax]];
            }
        }
        return $pairs;
    }

    // Sum of each GST pair's NET balance (Input - Output) that comes out
    // CREDIT (net > 0 credit, i.e. Output side heavier) — this is the
    // amount actually owed to the tax department, added to Current
    // Liabilities. Positive magnitude.
    private function _cfGstNetCurrentLiabilities($pid)
    {
        $total = 0;
        foreach ($this->_cfGstAccountPairs() as $pair) {
            $net = $this->_cfAccountDebitBalance($pair[0], $pid) + $this->_cfAccountDebitBalance($pair[1], $pid);
            if ($net < 0) { $total += -$net; }
        }
        return $total;
    }

    // Sum of each GST pair's NET balance (Input - Output) that comes out
    // DEBIT (net > 0, i.e. Input side heavier — GST credit still to be
    // claimed) — added to Investments. Positive magnitude.
    private function _cfGstNetInvestments($pid)
    {
        $total = 0;
        foreach ($this->_cfGstAccountPairs() as $pair) {
            $net = $this->_cfAccountDebitBalance($pair[0], $pid) + $this->_cfAccountDebitBalance($pair[1], $pid);
            if ($net > 0) { $total += $net; }
        }
        return $total;
    }

    // Flat list of every account id involved in a GST Input/Output pair
    // — excluded from the normal per-account Current Liabilities/
    // Investments scans, since they're handled as netted pairs instead
    // (see _cfGstAccountPairs).
    private function _cfGstPairedAccountIds()
    {
        $ids = [];
        foreach ($this->_cfGstAccountPairs() as $pair) {
            $ids[] = $pair[0];
            $ids[] = $pair[1];
        }
        return $ids;
    }

    // Current Liabilities total: see actionCfcurrentliabilities for the
    // exact rule. GST Input/Output pairs are netted together first (see
    // _cfGstNetCurrentLiabilities) rather than scanned as independent
    // accounts.
    private function _cfCurrentLiabilitiesTotal($pid)
    {
        $gstPairedIds = $this->_cfGstPairedAccountIds();
        $total = $this->_cfGstNetCurrentLiabilities($pid);
        foreach ($this->_cfAllProjectTouchedAccountIds($pid) as $acctId) {
            $acctId = (int)$acctId;
            if (in_array($acctId, $gstPairedIds, true)) { continue; }
            $accountType = (int)Yii::$app->db->createCommand(
                "SELECT account_type FROM accounts_item WHERE id=:id",
                [':id' => $acctId]
            )->queryScalar();
            if ($accountType === 2 || $accountType === 4) {
                continue;
            }
            $balance = $this->_cfAccountDebitBalance($acctId, $pid);
            if ($balance < 0) {
                $total += -$balance;
            }
        }
        return $total;
    }


    // Current Liabilities panel breakdown (Cashflow Dashboard tooltip):
    // same account pool/filter as actionCfcurrentliabilities, but
    // returns each contributing account head (party) individually
    // instead of just the sum — for the click-to-open per-account bar
    // chart, same pattern as actionCfcurrentassetsbreakdown.
    public function actionCfcurrentliabilitiesbreakdown()
    {
        $uid = Yii::$app->user->Id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) { return json_encode(['error' => 'No project selected']); }
        $pid = (int)$projuser->projectid;
        // Agreed rule: same list as the Current Liabilities figure (cfcurrentliabilities):
        // every head except Bank, Income and Expense with a CREDIT main-account balance.
        $p = [];
        for ($i = 1; $i <= 6; $i++) { $p[':p' . $i] = $pid; }
        $clRows = Yii::$app->db->createCommand("SELECT ai.name, -SUM(t.x) amt FROM (
                SELECT account_id a, amount x FROM voucher WHERE contra=0 AND project_id=:p1
                UNION ALL SELECT creditacnt, -amount FROM voucher WHERE contra=0 AND project_id=:p2
                UNION ALL SELECT account_id, amount FROM voucher WHERE contra=1 AND type='Receipt' AND project_id=:p3
                UNION ALL SELECT creditacnt, -amount FROM voucher WHERE contra=1 AND type='Payment' AND project_id=:p4
                UNION ALL SELECT debitacnt, amount FROM journalvoucher WHERE project_id=:p5
                UNION ALL SELECT creditacnt, -amount FROM journalvoucher WHERE project_id=:p6) t
             JOIN accounts_item ai ON ai.id = t.a
             WHERE ai.account_type NOT IN (2, 4, 8)
             GROUP BY t.a, ai.name HAVING SUM(t.x) < 0 ORDER BY amt DESC", $p)->queryAll();
        $out = [];
        foreach ($clRows as $r) { $out[] = ['name' => $r['name'], 'amount' => round((float)$r['amt'], 2)]; }
        return json_encode(['error' => 'No', 'accounts' => $out]);

        $gstPairedIds = $this->_cfGstPairedAccountIds();
        $rows = [];
        foreach ($this->_cfGstAccountPairs() as $pair) {
            $net = $this->_cfAccountDebitBalance($pair[0], $pid) + $this->_cfAccountDebitBalance($pair[1], $pid);
            if ($net < 0) {
                $outputName = Yii::$app->db->createCommand(
                    "SELECT name FROM accounts_item WHERE id=:id", [':id' => $pair[1]]
                )->queryScalar();
                $label = preg_replace('/^Output\s*/i', '', $outputName ?: '') ?: 'GST';
                $rows[] = ['name' => 'Net ' . $label . ' Payable', 'amount' => round(-$net, 2)];
            }
        }
        foreach ($this->_cfAllProjectTouchedAccountIds($pid) as $acctId) {
            $acctId = (int)$acctId;
            if (in_array($acctId, $gstPairedIds, true)) { continue; }
            $accountType = (int)Yii::$app->db->createCommand(
                "SELECT account_type FROM accounts_item WHERE id=:id",
                [':id' => $acctId]
            )->queryScalar();
            if ($accountType === 2 || $accountType === 4) {
                continue;
            }
            $balance = $this->_cfAccountDebitBalance($acctId, $pid);
            if ($balance < 0) {
                $name = Yii::$app->db->createCommand(
                    "SELECT name FROM accounts_item WHERE id=:id",
                    [':id' => $acctId]
                )->queryScalar();
                $rows[] = ['name' => $name ?: ('Account #' . $acctId), 'amount' => round(-$balance, 2)];
            }
        }
        usort($rows, function ($a, $b) { return $b['amount'] <=> $a['amount']; });

        return json_encode([
            'error' => 'No',
            'accounts' => $rows,
        ]);
    }

    // Current Work In Progress panel (Cashflow Dashboard):
    // 1) Bank/Cash voucher payments AND journalvoucher postings for the
    //    selected project whose debited counterparty is an Expense-type
    //    account head (accounts_item.account_type = 8).
    // 2) PLUS every OTHER (non-Expense, non-Income, non-Bank/Cash)
    //    account head that received a debit posting — via a Bank/Cash
    //    voucher payment OR a journal entry — for this project, i.e.
    //    payments/postings to parties/suppliers/sub-contractors/assets/
    //    liabilities/etc. Each such account is checked EVERY time: only
    //    counted toward WIP when that account's own CURRENT live balance
    //    (Debit - Credit, project-scoped, across ALL its transactions,
    //    NOT date-restricted — the balance sign is a running/lifetime
    //    check) is credit OR exactly zero (still outstanding, or fully
    //    reconciled — e.g. a Bank advance later matched 1:1 against an
    //    Expense journal entry nets to 0, but that money still really
    //    left the bank and became a real expense). Only a strictly
    //    positive (debit) balance is excluded from WIP — that's a
    //    genuine asset/deposit still held, which belongs in Investments/
    //    Current Assets instead (see actionCfcurrentassets). Excludes
    //    Income (4) so Contract Income postings never get pulled into
    //    WIP.
    //
    // WIP ACCRUAL CYCLES: every time Contract Income is credited via a
    // journal entry, the WIP accumulated up to that date is considered
    // billed out and permanently excluded from the total going forward
    // — NOT by subtracting the Bills Receivable (invoice/quoted) value,
    // since a bill is prepared from the quoted amount and includes
    // profit/margin that has no counterpart in raw WIP cost. Instead,
    // only transactions dated AFTER the LATEST Contract Income credit
    // (see _cfLatestContractIncomeDate) count toward this total — the
    // still-open accrual cycle. Everything from project inception up to
    // that date belonged to a prior, now-closed cycle and is dropped
    // entirely, cycle by cycle, as each new Contract Income credit
    // lands.
    public function actionCfwip()
    {
        $uid = Yii::$app->user->Id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) { return json_encode(['error' => 'No project selected']); }
        $pid = (int)$projuser->projectid;
        // Agreed cashflow rules: WIP = expenses + supplier work done - cash received (see _cfFlowGroups).
        return json_encode(['error' => 'No', 'total' => round($this->_cfFlowGroups($pid)['wip'], 2)]);

        $sinceDate = $this->_cfLatestContractIncomeDate($pid);
        $total = $this->_cfWipForWindow($pid, $sinceDate, null);

        return json_encode([
            'error' => 'No',
            'total' => round($total, 2),
        ]);
    }

    // WIP dial target, per payment cycle (Billing Cycle).
    // cycles = ceil(duration / billing cycle days). Cycle k covers project
    // days [(k-1)B, min(kB, duration) - 1] counted from the project start.
    // An activity counts in a cycle in proportion to the planned Gantt days
    // it has inside that cycle: quantity * (overlap days / planned days)
    // * estimate rate. The cycle's bar is the sum over activities. The dial
    // target is the bar of the cycle that contains today.
    // Current billing cycle of a project: cycles of Billing Cycle days counted from
    // the project start, enough to cover the project duration (Project creation).
    // Returns null when either value is missing. 'end' is null for the last cycle
    // (open-ended: days after the project end count there).
    // Start of billing cycle 1: the earliest start of any of the project's
    // activities — the start reported in the progress report, otherwise the
    // scheduled start, never earlier than today for one not yet started (so until
    // the first activity starts, cycle 1 moves forward a day every day). The first
    // bill falls at this date + the billing cycle. Falls back to the project start.
    public static function cfCycleBase($pid, $fallback)
    {
        $d = Yii::$app->db->createCommand(
            "SELECT MIN(COALESCE(
                        NULLIF((SELECT MIN(NULLIF(r.start_date, '0000-00-00')) FROM schedule_progress_report r WHERE r.activity_id = s.id), ''),
                        GREATEST(s.actual_start_date, CURDATE())))
             FROM scheduleactivities s
             WHERE s.projectId = :pid AND s.status = 0 AND s.quantity > 0
               AND s.actual_start_date IS NOT NULL AND s.actual_start_date <> '0000-00-00'
               AND s.iow_group_id IN (SELECT iow_group_id FROM iow_group_order WHERE project_id = :pid2)",
            [':pid' => (int)$pid, ':pid2' => (int)$pid]
        )->queryScalar();
        return $d ? substr($d, 0, 10) : $fallback;
    }

    public static function cfCurrentCycle($pid)
    {
        $db = Yii::$app->db;
        $proj = $db->createCommand("SELECT start_date, duration FROM projects WHERE Project_Id=:pid", [':pid' => (int)$pid])->queryOne();
        $len = (int)round((float)$db->createCommand(
            "SELECT target_value FROM cf_dashboard_targets WHERE project_id=:pid AND metric_key='billing_cycle'", [':pid' => (int)$pid]
        )->queryScalar());
        if (!$proj || empty($proj['start_date']) || substr($proj['start_date'], 0, 10) === '0000-00-00' || (int)$proj['duration'] <= 0 || $len <= 0) return null;
        $base = strtotime(self::cfCycleBase($pid, substr($proj['start_date'], 0, 10)));
        $duration = (int)$proj['duration'];
        $n = (int)ceil($duration / $len);
        $today = (int)round((strtotime(date('Y-m-d')) - $base) / 86400);
        $k = max(1, min($n, (int)floor(max(0, $today) / $len) + 1));
        return [
            'cycle' => $k, 'count' => $n, 'length' => $len,
            'start' => date('Y-m-d', $base + ($k - 1) * $len * 86400),
            'end'   => $k === $n ? null : date('Y-m-d', $base + (min($k * $len, $duration) - 1) * 86400),
        ];
    }

    // Project for the cycle endpoints: the user's selected project, or a project
    // id set by server-side code that calls these actions internally.
    public $cfPidOverride = null;
    private function _cfPid()
    {
        if ($this->cfPidOverride) return (int)$this->cfPidOverride;
        $projuser = ProjuserSelection::find()->where(['userid' => Yii::$app->user->Id])->one();
        return $projuser ? (int)$projuser->projectid : 0;
    }

    // Cashflow, VALUE DRIVERS: Project Resources popup. The resources allocated to
    // the activities of the current billing cycle (the same list as the Project
    // Cost popup and the Gantt "Activities in this Cycle" window), one row per
    // resource, grouped by resource type. Project Overhead is not in the list.
    //   E. Quantity   = consumption per unit of activity x activity quantity
    //   Cycle Qnty    = E. Quantity x activity days in this cycle / activity duration
    //   Cost in cycle = Cycle Qnty x rate (adds up to the activity's Cost in cycle)
    //   Purchased     = GRN quantity of the project for the resource
    //   Used          = average consumption (Cost Dashboard formula) x quantity executed
    //   Remaining     = Purchased - Used
    public function actionCfcycleresources()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $pid = $this->_cfPid();
        if (!$pid) { return ['error' => 'No project selected']; }
        $db = Yii::$app->db;
        $w = $this->actionCfwipcycles();
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $origPost = $_POST;
        $res = [];
        foreach (($w['current_items'] ?? []) as $it) {
            if (empty($it['sched_id'])) continue;
            $_POST = ['actid' => (int)$it['sched_id']];
            $cd = json_decode($this->actionCostdashboardactivity(), true) ?: [];
            $share = ((int)$it['duration'] > 0) ? (float)$it['days'] / (float)$it['duration'] : 0.0;
            $done = (float)($cd['last_report_qty'] ?? 0);
            foreach (($cd['items'] ?? []) as $ri) {
                $key = !empty($ri['resource_id']) ? 'r' . (int)$ri['resource_id'] : 'n' . $ri['name'];
                $perUnit = (float)($ri['planned_consumption'] ?? 0);
                $estQty = $perUnit * (float)$it['quantity'];
                $cycQty = $estQty * $share;
                if (!isset($res[$key])) {
                    $res[$key] = [
                        'resource_id' => (int)($ri['resource_id'] ?? 0),
                        'type' => $ri['type_name'] !== '' ? $ri['type_name'] : 'Other',
                        'name' => $ri['name'], 'unit' => $ri['unit'], 'rate' => (float)$ri['rate'],
                        'est_qty' => 0.0, 'cycle_qty' => 0.0, 'cost' => 0.0, 'used' => 0.0,
                        // first allocation of the resource, used to raise its PO from the popup
                        'allocation_id' => (int)($ri['allocation_id'] ?? 0),
                        'activity_id' => (int)($ri['activity_id'] ?? 0),
                        'task_ids' => (string)($ri['task_ids'] ?? ''),
                    ];
                }
                $res[$key]['est_qty']   += $estQty;
                $res[$key]['cycle_qty'] += $cycQty;
                $res[$key]['cost']      += $cycQty * (float)$ri['rate'];
                $res[$key]['used']      += (float)($ri['actual_consumption'] ?? 0) * $done;
            }
        }
        $_POST = $origPost;

        // Lot size (project resource parameters), lead time, credit period
        // (resource master; lead time from the parameters when set there) and
        // the GRN quantity received for the project.
        $ids = array_values(array_filter(array_map(function($r){ return $r['resource_id']; }, $res)));
        $meta = []; $grn = [];
        if ($ids) {
            $in = implode(',', array_map('intval', $ids));
            foreach ($db->createCommand(
                "SELECT r.Resource_Id AS id, rp.lot_size, COALESCE(NULLIF(rp.lead_time, ''), r.lead_time) AS lead_time, r.credit_period
                 FROM resources r LEFT JOIN resource_parameters rp ON rp.resource_id = r.Resource_Id AND rp.project_id = :pid
                 WHERE r.Resource_Id IN ($in)", [':pid' => $pid])->queryAll() as $m) $meta[(int)$m['id']] = $m;
            foreach ($db->createCommand(
                "SELECT g.GRN_Item AS id, SUM(CAST(g.GRN_Quantity AS DECIMAL(15,4))) AS qty
                 FROM goods_received_note g JOIN purchase_orders po ON po.order_id = g.GRN_Purchase_Order
                 WHERE po.project_id = :pid AND po.delete_status = 0 AND g.GRN_Item IN ($in)
                 GROUP BY g.GRN_Item", [':pid' => $pid])->queryAll() as $g) $grn[(int)$g['id']] = (float)$g['qty'];
        }
        $rows = [];
        foreach ($res as $r) {
            $m = $meta[$r['resource_id']] ?? [];
            $purchased = $grn[$r['resource_id']] ?? 0.0;
            $rows[] = $r + [
                'lot_size' => isset($m['lot_size']) && $m['lot_size'] !== null ? (float)$m['lot_size'] : null,
                'lead_time' => isset($m['lead_time']) && $m['lead_time'] !== '' ? $m['lead_time'] : null,
                'credit_period' => isset($m['credit_period']) && $m['credit_period'] !== '' ? $m['credit_period'] : null,
                'purchased' => $purchased,
                'remaining' => $purchased - $r['used'],
            ];
        }
        usort($rows, function($a, $b){ return [$a['type'], $b['cost']] <=> [$b['type'], $a['cost']]; });
        $cyc = (($w['cycles'] ?? [])[($w['current_cycle'] ?? 1) - 1]) ?? [];
        return ['error' => 'No', 'current_cycle' => $w['current_cycle'] ?? 0, 'cycle' => $cyc, 'resources' => $rows];
    }

    // Cashflow, VALUE DRIVERS: Project Resources panel (bar).
    //   Estimated = value of every resource allocated in the project
    //               (rate x quantity per unit x activity quantity, project allocation)
    //   Used      = used quantity (Cost Dashboard consumption x quantity executed) x rate
    //   Purchased = GRN quantity x rate for goods (materials, consumables, purchased
    //               inputs, tools); for services (labour, sub-contractors, equipment)
    //               what is used is what is bought, so purchased = used
    //   Stock     = purchased - used (goods lying at site)
    // Values are at each resource's allocated rate, so the three are comparable.
    public function actionCfresourcesvalue()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $pid = $this->_cfPid();
        if (!$pid) return ['error' => 'No project selected'];
        $db = Yii::$app->db;
        // Allocated value and average rate per resource across the project's activities.
        $res = [];
        foreach ($db->createCommand(
            "SELECT pern.resource_Id AS rid, COALESCE(NULLIF(pern.resourcetype_Id, 0), r.ResourceType_Id) AS type_id,
                    SUM(pern.rate * pern.quantity * COALESCE(pe.activity_qty, 0)) AS est_val,
                    SUM(pern.quantity * COALESCE(pe.activity_qty, 0)) AS est_qty
             FROM pricing_estimate_resources_new pern
             LEFT JOIN resources r ON r.Resource_Id = pern.resource_Id
             LEFT JOIN pricing_estimate_new pe ON pe.activity_Id = pern.activity_id AND pe.project_Id = pern.project_id AND pe.pricing_status = 0
             WHERE pern.project_id = :p AND pern.pricing_status = 0
             GROUP BY pern.resource_Id, type_id", [':p' => $pid]
        )->queryAll() as $r) {
            $q = (float)$r['est_qty'];
            $res[(int)$r['rid']] = ['type' => (int)$r['type_id'], 'est' => (float)$r['est_val'], 'rate' => $q > 0 ? (float)$r['est_val'] / $q : 0.0, 'used' => 0.0, 'grn' => 0.0];
        }
        // Used quantity per resource: the Cost Dashboard's consumption x quantity executed.
        $origPost = $_POST;
        foreach ($db->createCommand("SELECT id FROM scheduleactivities WHERE projectId = :p AND status = 0", [':p' => $pid])->queryColumn() as $sid) {
            $_POST = ['actid' => (int)$sid];
            $cd = json_decode($this->actionCostdashboardactivity(), true) ?: [];
            $done = (float)($cd['last_report_qty'] ?? 0);
            if ($done <= 0) continue;
            foreach (($cd['items'] ?? []) as $it) {
                $rid = (int)($it['resource_id'] ?? 0);
                if (isset($res[$rid])) $res[$rid]['used'] += (float)($it['actual_consumption'] ?? 0) * $done;
            }
        }
        $_POST = $origPost;
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        foreach ($db->createCommand(
            "SELECT g.GRN_Item AS rid, SUM(CAST(g.GRN_Quantity AS DECIMAL(15,4))) AS qty
             FROM goods_received_note g JOIN purchase_orders po ON po.order_id = g.GRN_Purchase_Order
             WHERE po.project_id = :p AND po.delete_status = 0 GROUP BY g.GRN_Item", [':p' => $pid]
        )->queryAll() as $g) if (isset($res[(int)$g['rid']])) $res[(int)$g['rid']]['grn'] = (float)$g['qty'];
        $goods = [2, 6, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18];   // received on a GRN (materials, purchased inputs, consumables, tools)
        $est = 0.0; $used = 0.0; $purch = 0.0; $stock = 0.0;
        foreach ($res as $r) {
            $est += $r['est'];
            $u = $r['used'] * $r['rate'];
            $used += $u;
            if (in_array($r['type'], $goods, true)) {
                $p = $r['grn'] * $r['rate'];
                $purch += $p;
                $stock += max(0, $p - $u);
            } else {
                $purch += $u;
            }
        }
        return ['error' => 'No', 'estimated' => round($est, 2), 'purchased' => round($purch, 2), 'used' => round($used, 2), 'stock' => round($stock, 2)];
    }

    // Work In Progress popup: the account heads making up the current WIP — each
    // head's cash and bank payments counted as work cost (expense heads in full;
    // suppliers/contractors for the part not treated as an advance), since the last
    // bill (cost up to the last bill date is cleared from WIP). Other money received
    // reduces WIP and is returned separately.
    public function actionCfwipaccounts()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $pid = $this->_cfPid();
        if (!$pid) return ['error' => 'No project selected'];
        $g = $this->_cfFlowGroups($pid);
        $at = $g['last_bill'] ? $this->_cfFlowGroups($pid, $g['last_bill'], true)['wip_acc'] : [];
        $names = Yii::$app->db->createCommand("SELECT id, name FROM accounts_item")->queryAll(\PDO::FETCH_KEY_PAIR);
        $rows = [];
        foreach ($g['wip_acc'] as $id => $v) {
            $amt = round((float)$v - (float)($at[$id] ?? 0), 2);
            if (abs($amt) >= 0.01) $rows[] = ['name' => $names[$id] ?? ('Account ' . $id), 'amount' => $amt];
        }
        usort($rows, function($a, $b){ return $b['amount'] <=> $a['amount']; });
        return ['error' => 'No', 'accounts' => $rows, 'wip' => round($g['wip'], 2), 'last_bill' => $g['last_bill'],
                'other_received' => round(array_sum(array_column($rows, 'amount')) - $g['wip'], 2)];
    }

    // Current Assets popup: first bar = Advances and Deposits (cash and bank
    // payments, the Advances and Deposits panel), then one bar per account head for
    // what Current Assets holds beyond it (amounts booked by journal: input GST,
    // retention, TDS, Bills Receivable, deposits partly by journal ...). The bars
    // add up to Current Assets.
    public function actionCfcurrentassetsdiff()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $pid = $this->_cfPid();
        if (!$pid) return ['error' => 'No project selected'];
        $db = Yii::$app->db;
        $p = [':p1' => $pid, ':p2' => $pid, ':p3' => $pid, ':p4' => $pid];
        $cash = $db->createCommand("SELECT a, SUM(x) FROM (
                SELECT account_id a, amount x FROM voucher WHERE contra=0 AND project_id=:p1
                UNION ALL SELECT creditacnt, -amount FROM voucher WHERE contra=0 AND project_id=:p2
                UNION ALL SELECT account_id, amount FROM voucher WHERE contra=1 AND type='Receipt' AND project_id=:p3
                UNION ALL SELECT creditacnt, -amount FROM voucher WHERE contra=1 AND type='Payment' AND project_id=:p4) t GROUP BY a", $p)->queryAll(\PDO::FETCH_KEY_PAIR);
        $main = $db->createCommand("SELECT a, SUM(x) FROM (
                SELECT account_id a, amount x FROM voucher WHERE contra=0 AND project_id=:p1
                UNION ALL SELECT creditacnt, -amount FROM voucher WHERE contra=0 AND project_id=:p2
                UNION ALL SELECT debitacnt, amount FROM journalvoucher WHERE project_id=:p3
                UNION ALL SELECT creditacnt, -amount FROM journalvoucher WHERE project_id=:p4) t GROUP BY a", $p)->queryAll(\PDO::FETCH_KEY_PAIR);
        $types = $db->createCommand("SELECT id, account_type FROM accounts_item")->queryAll(\PDO::FETCH_KEY_PAIR);
        $names = $db->createCommand("SELECT id, name FROM accounts_item")->queryAll(\PDO::FETCH_KEY_PAIR);
        $advTotal = 0.0; $diffs = [];
        foreach (array_unique(array_merge(array_keys($cash), array_keys($main))) as $id) {
            $t = (int)($types[$id] ?? 0); $b = (float)($cash[$id] ?? 0); $m = (float)($main[$id] ?? 0);
            // Advances and Deposits share (same rule as _cfFlowGroups).
            if (in_array($t, [2, 8], true)) $adv = 0.0;
            elseif ($t === 7) $adv = ($b > 0 && $m > 0) ? min($m, $b) : 0.0;
            else $adv = $b > 0 ? $b : 0.0;
            // Current Assets share (debit main balance, not Bank/Income/Expense).
            $ca = (!in_array($t, [2, 4, 8], true) && $m > 0) ? $m : 0.0;
            $advTotal += $adv;
            if (abs($ca - $adv) >= 0.01) $diffs[] = ['name' => $names[$id] ?? ('Account ' . $id), 'amount' => round($ca - $adv, 2)];
        }
        usort($diffs, function($a, $b){ return $b['amount'] <=> $a['amount']; });
        $rows = array_merge([['name' => 'Advances and Deposits', 'amount' => round($advTotal, 2)]], $diffs);
        return ['error' => 'No', 'accounts' => $rows, 'total' => round(array_sum(array_column($rows, 'amount')), 2)];
    }

    // Cashflow dashboard Notifications panel (right of the three windows):
    // messages to the user, alerts raised for the selected project (e.g. the
    // chatbot's ROI Health alert in the notifications table), and action plans.
    public function actionCfnotifications()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $pid = $this->_cfPid();
        $uid = (int)Yii::$app->user->Id;
        $db = Yii::$app->db;
        // Due action plans are pushed to phones once a day; if the cron job has not
        // run yet today, the first refresh after 8 AM IST does it.
        $ist = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
        $flag = Yii::getAlias('@runtime') . '/push_due_date.txt';
        if ((int)$ist->format('G') >= 8 && @file_get_contents($flag) !== $ist->format('Y-m-d')) {
            @file_put_contents($flag, $ist->format('Y-m-d'));
            try { self::cfPushDuePlans(); } catch (\Throwable $e) { Yii::warning('Due push failed: ' . $e->getMessage()); }
        }
        // New messages and alerts are pushed to phones (first refresh that sees them).
        try { self::cfPushNewItems(); } catch (\Throwable $e) { Yii::warning('Message/alert push failed: ' . $e->getMessage()); }
        // archived = 1 when the item has been archived from the window.
        $messages = $db->createCommand(
            "SELECT id, title, message AS text, message_read AS archived FROM messages
             WHERE to_user_id = :u AND draft = 0 ORDER BY message_read ASC, id DESC LIMIT 50", [':u' => $uid]
        )->queryAll();
        $alerts = $pid ? $db->createCommand(
            "SELECT notf_id AS id, type AS title, remarks AS text, status AS archived FROM notifications
             WHERE itemid = :p AND type LIKE '%Health%' ORDER BY status ASC, notf_id DESC LIMIT 50", [':p' => $pid]
        )->queryAll() : [];
        // Action plans written by users for the project (action_plans table): open
        // ones first by target date, done ones after.
        $plans = $pid ? $db->createCommand(
            "SELECT ap.id, ap.plan_text AS text, ap.responsible_user_id, ap.target_date, ap.status, ap.created_at,
                    ru.username AS responsible, cu.username AS created_by
             FROM action_plans ap
             LEFT JOIN user ru ON ru.id = ap.responsible_user_id
             LEFT JOIN user cu ON cu.id = ap.created_by
             WHERE ap.project_id = :p AND ap.deleted = 0
             ORDER BY ap.status ASC, (ap.target_date IS NULL) ASC, ap.target_date ASC, ap.id DESC", [':p' => $pid]
        )->queryAll() : [];
        $users = $db->createCommand("SELECT id, username FROM user WHERE status = 1 ORDER BY username")->queryAll();
        return ['error' => 'No', 'messages' => $messages, 'alerts' => $alerts, 'action_plans' => $plans, 'users' => $users, 'today' => $ist->format('Y-m-d')];
    }

    // ── Phone / browser push notifications (see components/WebPush.php) ──────
    public function actionPushkey()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return ['error' => 'No', 'key' => \app\components\WebPush::keys()['public']];
    }

    // Saves this device's subscription for the logged-in user.
    public function actionPushsubscribe()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $uid = (int)Yii::$app->user->Id;
        $sub = json_decode((string)($_POST['subscription'] ?? ''), true);
        if (!$uid || empty($sub['endpoint']) || empty($sub['keys']['p256dh']) || empty($sub['keys']['auth'])) {
            return ['error' => 'Yes', 'errortext' => 'Invalid subscription.'];
        }
        \app\components\WebPush::ensureTables();
        $db = Yii::$app->db;
        $hash = hash('sha256', $sub['endpoint']);
        $row = ['user_id' => $uid, 'endpoint' => $sub['endpoint'], 'endpoint_hash' => $hash,
                'p256dh' => $sub['keys']['p256dh'], 'auth' => $sub['keys']['auth'],
                'user_agent' => substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255), 'created_at' => date('Y-m-d H:i:s')];
        $exists = $db->createCommand("SELECT id FROM push_subscriptions WHERE endpoint_hash = :h", [':h' => $hash])->queryScalar();
        if ($exists) $db->createCommand()->update('push_subscriptions', $row, ['id' => $exists])->execute();
        else $db->createCommand()->insert('push_subscriptions', $row)->execute();
        $sent = \app\components\WebPush::sendToUser($uid, ['title' => 'Opiam notifications are on',
            'body' => 'You will be told here when an action plan is assigned to you and when it falls due.', 'tag' => 'opiam-welcome']);
        return ['error' => 'No', 'sent' => $sent];
    }

    // Open plans of a user that are due or overdue today (IST), for the icon badge.
    private static function _apDueCount($uid)
    {
        $today = (new \DateTime('now', new \DateTimeZone('Asia/Kolkata')))->format('Y-m-d');
        return (int)Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM action_plans WHERE deleted = 0 AND status = 0 AND target_date <= :d
               AND COALESCE(responsible_user_id, created_by) = :u", [':d' => $today, ':u' => (int)$uid]
        )->queryScalar();
    }

    private static function _apEnsureColumns()
    {
        $db = Yii::$app->db;
        $has = $db->createCommand("SHOW COLUMNS FROM action_plans LIKE 'last_push_date'")->queryOne();
        if (!$has) $db->createCommand("ALTER TABLE action_plans ADD COLUMN last_push_date DATE NULL")->execute();
    }

    // Pushes every open action plan that is due today or overdue to its responsible
    // person (else its author), once a day. Run by the daily cron job (cron/push_due.php)
    // and, as a fallback, by the first notification refresh after 8 AM IST.
    public static function cfPushDuePlans()
    {
        self::_apEnsureColumns();
        $db = Yii::$app->db;
        $today = (new \DateTime('now', new \DateTimeZone('Asia/Kolkata')))->format('Y-m-d');
        $plans = $db->createCommand(
            "SELECT ap.*, p.Name AS project_name FROM action_plans ap LEFT JOIN projects p ON p.Project_Id = ap.project_id
             WHERE ap.deleted = 0 AND ap.status = 0 AND ap.target_date IS NOT NULL AND ap.target_date <= :d
               AND (ap.last_push_date IS NULL OR ap.last_push_date < :d2)", [':d' => $today, ':d2' => $today]
        )->queryAll();
        $n = 0;
        foreach ($plans as $pl) {
            $uid = (int)($pl['responsible_user_id'] ?: $pl['created_by']);
            $due = substr($pl['target_date'], 0, 10);
            $title = $due === $today ? 'Action plan due today' : 'Action plan overdue since ' . date('j M', strtotime($due));
            $n += \app\components\WebPush::sendToUser($uid, [
                'title' => $title,
                'body' => ($pl['project_name'] ? $pl['project_name'] . ' — ' : '') . mb_substr($pl['plan_text'], 0, 1000),
                'tag' => 'opiam-ap-' . $pl['id'], 'url' => '/projectsmain/notifview?k=ap&id=' . $pl['id'], 'badge' => self::_apDueCount($uid),
            ]);
            $db->createCommand()->update('action_plans', ['last_push_date' => $today], ['id' => $pl['id']])->execute();
        }
        return $n;
    }

    // Pushes every message and alert created since the last run to the phones of
    // the people concerned — whatever created them (chatbot ROI health check,
    // future message screen ...). Messages go to their recipient; an alert goes
    // to its assigned user, else its owner, else everyone with notifications on.
    // Runs from the 2-minute notification refresh and the daily job; the first
    // run only records where it starts, so old items are not pushed.
    public static function cfPushNewItems()
    {
        $db = Yii::$app->db;
        $file = Yii::getAlias('@runtime') . '/push_state.json';
        $fh = @fopen($file, 'c+');
        if (!$fh || !flock($fh, LOCK_EX | LOCK_NB)) { if ($fh) fclose($fh); return 0; }   // another run is busy
        $st = json_decode(stream_get_contents($fh), true) ?: [];
        $maxMsg = (int)$db->createCommand("SELECT COALESCE(MAX(id), 0) FROM messages")->queryScalar();
        $maxAl = (int)$db->createCommand("SELECT COALESCE(MAX(notf_id), 0) FROM notifications")->queryScalar();
        $n = 0;
        if (isset($st['msg'], $st['alert'])) {
            foreach ($db->createCommand(
                "SELECT m.id, m.to_user_id, m.title, m.message, u.username AS sender FROM messages m LEFT JOIN user u ON u.id = m.from_user_id
                 WHERE m.id > :i AND m.draft = 0 ORDER BY m.id", [':i' => (int)$st['msg']])->queryAll() as $m) {
                $n += \app\components\WebPush::sendToUser((int)$m['to_user_id'], [
                    'title' => (string)($m['title'] ?: $m['sender']),   // the Opiam icon already shows where it is from
                    'body' => mb_substr(strip_tags((string)$m['message']), 0, 1000), 'tag' => 'opiam-msg-' . $m['id'], 'url' => '/projectsmain/notifview?k=msg&id=' . $m['id'],
                ]);
            }
            \app\components\WebPush::ensureTables();
            $everyone = null;
            foreach ($db->createCommand(
                "SELECT n.notf_id, n.type, n.remarks, n.user_id, n.assigned_user, p.Name AS project FROM notifications n
                 LEFT JOIN projects p ON p.Project_Id = n.itemid
                 WHERE n.notf_id > :i AND n.type LIKE '%Health%' ORDER BY n.notf_id", [':i' => (int)$st['alert']])->queryAll() as $a) {
                $to = (int)$a['assigned_user'] ?: (int)$a['user_id'];
                if ($to) $users = [$to];
                else $users = $everyone ?? ($everyone = $db->createCommand("SELECT DISTINCT user_id FROM push_subscriptions")->queryColumn());
                foreach ($users as $u) {
                    $n += \app\components\WebPush::sendToUser((int)$u, [
                        'title' => (string)$a['type'], 'body' => mb_substr((string)($a['remarks'] ?: $a['project']), 0, 1000),
                        'tag' => 'opiam-alert-' . $a['notf_id'], 'url' => '/projectsmain/notifview?k=alert&id=' . $a['notf_id'],
                    ]);
                }
            }
        }
        ftruncate($fh, 0); rewind($fh);
        fwrite($fh, json_encode(['msg' => max($maxMsg, (int)($st['msg'] ?? 0)), 'alert' => max($maxAl, (int)($st['alert'] ?? 0))]));
        flock($fh, LOCK_UN); fclose($fh);
        return $n;
    }

    // Light page a phone notification opens: one message / alert / action plan
    // as a card (views/projectsmain/notifview.php), without loading the dashboard.
    public function actionNotifview()
    {
        $kind = (string)($_GET['k'] ?? '');
        $item = self::_notifItem($kind, (int)($_GET['id'] ?? 0), (int)Yii::$app->user->Id);
        return $this->renderPartial('notifview', ['kind' => $kind, 'item' => $item]);
    }

    // Buttons on the notification page: archive / restore / delete a message or
    // an alert, mark an action plan done / open.
    public function actionNotifviewact()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $kind = (string)($_POST['k'] ?? ''); $id = (int)($_POST['id'] ?? 0); $do = (string)($_POST['do'] ?? '');
        $uid = (int)Yii::$app->user->Id;
        if (!self::_notifItem($kind, $id, $uid)) return ['error' => 'Yes', 'errortext' => 'This item no longer exists.'];
        $db = Yii::$app->db;
        if ($kind === 'msg') {
            if ($do === 'delete') $db->createCommand()->delete('messages', ['id' => $id, 'to_user_id' => $uid])->execute();
            else $db->createCommand()->update('messages', ['message_read' => $do === 'archive' ? 1 : 0], ['id' => $id, 'to_user_id' => $uid])->execute();
        } elseif ($kind === 'alert') {
            if ($do === 'delete') $db->createCommand()->delete('notifications', ['notf_id' => $id])->execute();
            else $db->createCommand()->update('notifications', ['status' => $do === 'archive' ? 1 : 0], ['notf_id' => $id])->execute();
        } elseif ($kind === 'ap') {
            $db->createCommand()->update('action_plans', ['status' => $do === 'done' ? 1 : 0, 'updated_at' => date('Y-m-d H:i:s')], ['id' => $id])->execute();
        }
        return ['error' => 'No'];
    }

    private static function _notifItem($kind, $id, $uid)
    {
        $db = Yii::$app->db;
        if ($kind === 'msg') return $db->createCommand(
            "SELECT m.id, m.title, m.message AS text, m.message_read AS archived, u.username AS sender
             FROM messages m LEFT JOIN user u ON u.id = m.from_user_id WHERE m.id = :i AND m.to_user_id = :u", [':i' => $id, ':u' => $uid])->queryOne() ?: null;
        if ($kind === 'alert') return $db->createCommand(
            "SELECT n.notf_id AS id, n.type AS title, n.remarks AS text, n.status AS archived, p.Name AS project
             FROM notifications n LEFT JOIN projects p ON p.Project_Id = n.itemid WHERE n.notf_id = :i", [':i' => $id])->queryOne() ?: null;
        if ($kind === 'ap') return $db->createCommand(
            "SELECT ap.id, ap.plan_text AS text, ap.target_date, ap.status, p.Name AS project, ru.username AS responsible, cu.username AS created_by
             FROM action_plans ap LEFT JOIN projects p ON p.Project_Id = ap.project_id
             LEFT JOIN user ru ON ru.id = ap.responsible_user_id LEFT JOIN user cu ON cu.id = ap.created_by
             WHERE ap.id = :i AND ap.deleted = 0", [':i' => $id])->queryOne() ?: null;
        return null;
    }

    // Archive / restore a message or an alert from the Notifications window.
    public function actionNotifarchive()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $id = (int)($_POST['id'] ?? 0); $on = !empty($_POST['archive']) ? 1 : 0;
        $db = Yii::$app->db;
        if (($_POST['kind'] ?? '') === 'messages') {
            $db->createCommand()->update('messages', ['message_read' => $on], ['id' => $id, 'to_user_id' => (int)Yii::$app->user->Id])->execute();
        } elseif (($_POST['kind'] ?? '') === 'alerts') {
            $db->createCommand()->update('notifications', ['status' => $on], ['notf_id' => $id, 'itemid' => $this->_cfPid()])->execute();
        }
        return ['error' => 'No'];
    }

    // Delete a message (the user's own) or an alert (of the selected project)
    // from the Notifications window, instead of archiving it.
    public function actionNotifdelete()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $id = (int)($_POST['id'] ?? 0);
        $db = Yii::$app->db;
        if (($_POST['kind'] ?? '') === 'messages') {
            $db->createCommand()->delete('messages', ['id' => $id, 'to_user_id' => (int)Yii::$app->user->Id])->execute();
        } elseif (($_POST['kind'] ?? '') === 'alerts') {
            $db->createCommand()->delete('notifications', ['notf_id' => $id, 'itemid' => $this->_cfPid()])->execute();
        }
        return ['error' => 'No'];
    }

    // Action plans: add / edit (id given), mark done or open, delete (kept, flagged).
    public function actionActionplansave()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $pid = $this->_cfPid();
        if (!$pid) return ['error' => 'Yes', 'errortext' => 'No project selected.'];
        $db = Yii::$app->db;
        $id = (int)($_POST['id'] ?? 0);
        $text = trim((string)($_POST['text'] ?? ''));
        if ($text === '') return ['error' => 'Yes', 'errortext' => 'Please write the action plan.'];
        $resp = (int)($_POST['responsible'] ?? 0) ?: null;
        $date = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)($_POST['target_date'] ?? '')) ? $_POST['target_date'] : null;
        $now = date('Y-m-d H:i:s');
        $prevResp = $id ? $db->createCommand("SELECT responsible_user_id FROM action_plans WHERE id = :i", [':i' => $id])->queryScalar() : null;
        if ($id) {
            $db->createCommand()->update('action_plans',
                ['plan_text' => $text, 'responsible_user_id' => $resp, 'target_date' => $date, 'updated_at' => $now],
                ['id' => $id, 'project_id' => $pid, 'deleted' => 0])->execute();
        } else {
            $db->createCommand()->insert('action_plans', [
                'project_id' => $pid, 'plan_text' => $text, 'responsible_user_id' => $resp, 'target_date' => $date,
                'status' => 0, 'created_by' => (int)Yii::$app->user->Id, 'created_at' => $now,
            ])->execute();
            $id = (int)$db->getLastInsertID();
        }
        // Tell the responsible person's phone when a plan is assigned to them.
        if ($resp && (int)$prevResp !== (int)$resp) {
            try {
                $pname = (string)$db->createCommand("SELECT Name FROM projects WHERE Project_Id = :p", [':p' => $pid])->queryScalar();
                \app\components\WebPush::sendToUser($resp, [
                    'title' => 'New action plan for you' . ($date ? ' · due ' . date('j M', strtotime($date)) : ''),
                    'body' => ($pname ? $pname . ' — ' : '') . mb_substr($text, 0, 1000),
                    'tag' => 'opiam-ap-' . $id, 'url' => '/projectsmain/notifview?k=ap&id=' . $id, 'badge' => self::_apDueCount($resp),
                ]);
            } catch (\Throwable $e) { Yii::warning('Action plan push failed: ' . $e->getMessage()); }
        }
        return ['error' => 'No', 'id' => $id];
    }

    public function actionActionplanstatus()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $pid = $this->_cfPid();
        $id = (int)($_POST['id'] ?? 0);
        $del = !empty($_POST['delete']);
        $set = $del ? ['deleted' => 1] : ['status' => ((int)($_POST['status'] ?? 0)) ? 1 : 0];
        $set['updated_at'] = date('Y-m-d H:i:s');
        Yii::$app->db->createCommand()->update('action_plans', $set, ['id' => $id, 'project_id' => $pid])->execute();
        return ['error' => 'No'];
    }

    public function actionCfwipcycles()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $pid = $this->_cfPid();
        if (!$pid) { return ['error' => 'No project selected']; }
        $db = Yii::$app->db;

        $proj = $db->createCommand(
            "SELECT start_date, duration FROM projects WHERE Project_Id=:pid", [':pid' => $pid]
        )->queryOne();
        $billing = (float)$db->createCommand(
            "SELECT target_value FROM cf_dashboard_targets WHERE project_id=:pid AND metric_key='billing_cycle'",
            [':pid' => $pid]
        )->queryScalar();
        if (!$proj || empty($proj['start_date']) || $proj['start_date'] === '0000-00-00'
            || (int)$proj['duration'] <= 0 || $billing <= 0) {
            return ['error' => 'No', 'cycles' => [], 'current_cycle' => 0, 'current_target' => 0];
        }

        // Cycles start at the earliest activity start (see cfCycleBase), not the project start.
        $base     = strtotime(self::cfCycleBase($pid, substr($proj['start_date'], 0, 10)));
        $duration = (int)$proj['duration'];
        $cycleLen = (int)round($billing);
        $n        = (int)ceil($duration / $cycleLen);
        $dayOf    = function ($date) use ($base) { return (int)round((strtotime($date) - $base) / 86400); };

        $acts = $db->createCommand(
            "SELECT s.id, s.activity_id AS wan_id,
                    (SELECT w.activity_Id FROM workgroup_activities_new w WHERE w.id = s.activity_id) AS lib_activity_id,
                    s.name, s.unit, s.start_date, s.duration, s.quantity,
                    -- same rate as the Project Estimate window: the project's allocation only (0 if none)
                    COALESCE((SELECT ROUND(SUM(pern.rate * pern.quantity), 2) FROM pricing_estimate_resources_new pern
                              WHERE pern.activity_id = s.activity_id AND pern.project_id = s.projectId AND pern.pricing_status = 0), 0) AS rate,
                    COALESCE(ig.name, 'Other') AS group_name, s.completed_status,
                    COALESCE(NULLIF((SELECT MIN(spr.start_date) FROM schedule_progress_report spr WHERE spr.activity_id = s.id), '0000-00-00'), s.actual_start_date) AS bar_start,
                    COALESCE((SELECT SUM(l.currentqty) FROM schedule_progress_report_log l WHERE l.activity_id = s.id), 0) AS executed,
                    DATEDIFF(s.actual_end_date, s.actual_start_date) + 1 AS bar_days,
                    (SELECT MIN(NULLIF(spr2.start_date, '0000-00-00')) FROM schedule_progress_report spr2 WHERE spr2.activity_id = s.id) AS spr_start,
                    (SELECT MAX(l2.report_date) FROM schedule_progress_report_log l2 WHERE l2.activity_id = s.id) AS last_report
             FROM scheduleactivities s
             LEFT JOIN iow_groups ig ON ig.id = s.iow_group_id
             LEFT JOIN pricing_estimate_new p ON p.project_Id = s.projectId AND p.activity_Id = s.activity_id
             LEFT JOIN estimateactivities ea ON ea.activity_id = p.est_activity_Id
             WHERE s.projectId = :pid
               AND s.start_date IS NOT NULL AND s.end_date IS NOT NULL
               AND s.start_date <> '0000-00-00' AND s.end_date <> '0000-00-00'
               -- Only activities listed in the Gantt chart: active, with a quantity,
               -- under one of the project's IOW groups.
               AND s.status = 0 AND s.quantity > 0
               AND s.iow_group_id IN (SELECT iow_group_id FROM iow_group_order WHERE project_id = :pid2)
             ORDER BY (SELECT MIN(o.sortorder) FROM iow_group_order o WHERE o.iow_group_id = s.iow_group_id AND o.project_id = s.projectId),
                      s.iow_group_id, s.sortorder, s.id",
            [':pid' => $pid, ':pid2' => $pid]
        )->queryAll();

        // Overlap method: an activity's value (quantity x rate) is spread over
        // its current duration in days. Each cycle gets the value of the days
        // the activity spends inside that cycle. Days before the project start
        // count in cycle 1, and days after the project end count in the last cycle.
        $today = $dayOf(date('Y-m-d'));
        $current = max(1, min($n, (int)floor(max(0, $today) / $cycleLen) + 1));

        // current_items: each activity's share of the current cycle (same overlap
        // formula), listed in the Project Cost panel popup. Their costs add up to
        // the current cycle's bar.
        // The list itself is the same as the Gantt "Activities in this Cycle"
        // window: every activity not completed whose bar starts on or before the
        // cycle end — started, ongoing, starting during the cycle, or carried
        // forward unfinished from an earlier cycle.
        $cyc = self::cfCurrentCycle($pid);
        $cycEnd = $cyc ? $cyc['end'] : null;
        $bars = array_fill(1, $n, 0.0);
        $currentItems = [];
        foreach ($acts as $a) {
            $curDuration = max(1, (int)$a['duration']);
            $value = (float)$a['quantity'] * (float)$a['rate'];
            $aStart = $dayOf($a['start_date']);
            $aEnd = $aStart + $curDuration - 1;
            $curPart = 0.0; $curDays = 0;
            for ($k = 1; $k <= $n; $k++) {
                $cStart = ($k === 1) ? -100000000 : ($k - 1) * $cycleLen;
                $cEnd   = ($k === $n) ? 100000000 : min($k * $cycleLen, $duration) - 1;
                $overlap = min($aEnd, $cEnd) - max($aStart, $cStart) + 1;
                if ($overlap > 0) {
                    $part = $value * $overlap / $curDuration;
                    $bars[$k] += $part;
                    if ($k === $current) { $curPart = $part; $curDays = $overlap; }
                }
            }
            $barStart = substr((string)$a['bar_start'], 0, 10);
            if ((int)$a['completed_status'] === 1) continue;
            if ($cycEnd !== null && $barStart > $cycEnd) continue;
            // Quantity still to do this cycle (same as the bar tip in the Gantt
            // window): quantity planned by the cycle end, spread evenly over the
            // bar's days, minus the quantity already executed.
            $barDays = max(1, (int)$a['bar_days']);
            // Progress reported: bar as on the Gantt — reported start, duration
            // projected from the pace so far (elapsed days / quantity done x total
            // quantity), end never before the last report. Slow progress lengthens
            // the bar, so less of the quantity falls due by the cycle end.
            if ((float)$a['executed'] > 0 && (float)$a['quantity'] > 0 && $a['last_report']) {
                $st = substr((string)$a['start_date'], 0, 10);
                $ss = $a['spr_start'] ? substr((string)$a['spr_start'], 0, 10) : '';
                if ($ss && ($st === '' || $st === '0000-00-00' || $ss < $st)) $st = $ss;
                $lr = substr((string)$a['last_report'], 0, 10);
                $dDays = function($x, $y){ return (int)round((strtotime($y) - strtotime($x)) / 86400); };
                $projDur = (int)round(($dDays($st, $lr) + 1) / (float)$a['executed'] * (float)$a['quantity']);
                $endDay = max($projDur, $dDays($st, $lr) + 1);
                $barStart = $st;
                $barDays = max(1, $endDay);
            }
            $daysIn = $cycEnd === null ? $barDays
                : (int)round((strtotime($cycEnd) - strtotime($barStart)) / 86400) + 1;
            $daysIn = max(0, min($barDays, $daysIn));
            $cycleQty = max(0, (float)$a['quantity'] * $daysIn / $barDays - (float)$a['executed']);
            // Cycle Qnty (Project Cost popup): quantity planned in this cycle only =
            // per-day production x the bar's days inside this cycle; nothing deducted,
            // nothing carried forward. Cycle 1 also takes days before its start, the
            // last cycle days after its end.
            $bS = strtotime($barStart); $bE = $bS + ($barDays - 1) * 86400;
            $cS = ($cyc && (int)$cyc['cycle'] > 1) ? strtotime($cyc['start']) : $bS;
            $cE = $cycEnd !== null ? strtotime($cycEnd) : $bE;
            $inCycle = max(0, (int)round((min($bE, $cE) - max($bS, $cS)) / 86400) + 1);
            $cycleOnlyQty = (float)$a['quantity'] * min($barDays, $inCycle) / $barDays;
            $currentItems[] = [
                'sched_id' => (int)$a['id'],
                'wan_id'   => (int)$a['wan_id'],
                'lib_activity_id' => (int)$a['lib_activity_id'],
                'group'    => $a['group_name'],
                'name'     => $a['name'],
                'unit'     => $a['unit'],
                'quantity' => (float)$a['quantity'],
                'executed' => (float)$a['executed'],
                'remaining' => round($cycleQty, 2),
                'cycle_qty' => round($cycleOnlyQty, 2),
                'rate'     => (float)$a['rate'],
                'days'     => $curDays,
                'duration' => $curDuration,
                'cost'     => round($curPart, 2),
            ];
        }
        // Quoted rate per project activity, entered in the Project Cost popup (kept in
        // cf_dashboard_targets as qtd_rate_<wan_id> until it is filled from the BOQ).
        $qtd = $db->createCommand(
            "SELECT metric_key, target_value FROM cf_dashboard_targets WHERE project_id = :p AND metric_key LIKE 'qtd\\_rate\\_%'", [':p' => $pid]
        )->queryAll(\PDO::FETCH_KEY_PAIR);
        foreach ($currentItems as &$ci) {
            $k = 'qtd_rate_' . $ci['wan_id'];
            $ci['qtd_rate'] = isset($qtd[$k]) ? (float)$qtd[$k] : null;
        }
        unset($ci);

        $cycles = [];
        for ($k = 1; $k <= $n; $k++) {
            $labelDay = min($k * $cycleLen, $duration) - 1;
            $cycles[] = [
                'cycle'     => $k,
                'end_date'  => date('Y-m-d', $base + $labelDay * 86400),
                'end_label' => date('M', $base + $labelDay * 86400),
                'target'    => round($bars[$k], 2),
            ];
        }

        $totalCost = 0.0;
        foreach ($acts as $a) {
            $totalCost += (float)$a['quantity'] * (float)$a['rate'];
        }

        return [
            'error'          => 'No',
            'cycles'         => $cycles,
            'current_cycle'  => $current,
            'current_target' => $cycles[$current - 1]['target'],
            'current_items'  => $currentItems,
            'total_cost'     => round($totalCost, 2),
        ];
    }

    // Cashflow dashboard, VALUE DRIVERS: Project Duration panel.
    // Plan = the project's own start, end and duration (Project creation panel).
    // Actual = from the project start to the latest scheduled activity end.
    public function actionCfprojectduration()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $uid = Yii::$app->user->Id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) { return ['error' => 'No project selected']; }
        $pid = (int)$projuser->projectid;
        $db = Yii::$app->db;

        $proj = $db->createCommand(
            "SELECT Name, start_date, end_date, duration FROM projects WHERE Project_Id = :p", [':p' => $pid]
        )->queryOne();
        if (!$proj) { return ['error' => 'No project']; }

        // Recalculate the schedule for today first — the same two passes the Gantt
        // runs on opening (actionGetcriticalpath) — so this panel and the Gantt
        // always show the same end date.
        Yii::$app->helper->RecalculatePlannedDates($pid);
        Yii::$app->helper->GetRelationcorrect($pid);

        $forecastEnd = $db->createCommand(
            "SELECT MAX(end_date) FROM scheduleactivities WHERE projectId = :p AND status = 0", [':p' => $pid]
        )->queryScalar();
        $actualStart = $db->createCommand(
            "SELECT MIN(spr.start_date) FROM schedule_progress_report spr
             JOIN scheduleactivities sa ON sa.id = spr.activity_id
             WHERE sa.projectId = :p AND sa.status = 0
               AND spr.start_date IS NOT NULL AND spr.start_date <> '0000-00-00'", [':p' => $pid]
        )->queryScalar();

        $budgeted = (int)$proj['duration'];
        $startDay = substr($proj['start_date'], 0, 10);
        $endDay   = (!empty($forecastEnd) && $forecastEnd !== '0000-00-00 00:00:00')
            ? substr($forecastEnd, 0, 10) : '';
        $actual = $endDay
            ? max(0, (int)round((strtotime($endDay) - strtotime($startDay)) / 86400) + 1) : 0;

        return [
            'error'         => 'No',
            'project_name'  => $proj['Name'],
            'budgeted'      => $budgeted,
            'actual'        => $actual,
            'delay'         => max(0, $actual - $budgeted),
            'b_start_date'  => $startDay,
            'b_end_date'    => substr($proj['end_date'], 0, 10),
            'proj_end_date' => $endDay,
            'actual_start'  => $actualStart ? substr($actualStart, 0, 10) : '',
        ];
    }

    // Cashflow dashboard, VALUE DRIVERS: Project Cost panel.
    // Estimated = sum of project quantity x library rate over the project's estimate.
    public function actionCfprojectcost()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $uid = Yii::$app->user->Id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) { return ['error' => 'No project selected']; }
        $pid = (int)$projuser->projectid;
        // Estimated = the Project Estimate window's total (Amount column).
        $origPost = $_POST;
        $_POST = ['projectid' => $pid];
        $est = json_decode((new \app\controllers\EstimatereportController('estimatereport', Yii::$app))->actionGet(), true) ?: [];
        $_POST = $origPost;
        $estimated = (float)($est['grand_total'] ?? 0);
        // Actual = the Cost Dashboard's "Project Cost · Actual": for each activity
        // its actual cost (actual unit cost x quantity) where there is one, its
        // estimated cost otherwise (see actionCostdashboardbatch).
        $batch = json_decode($this->actionCostdashboardbatch(), true) ?: [];
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $actual = 0.0; $hasReal = false;
        foreach (($batch['data'] ?? []) as $c) {
            $acoa = (float)($c['acoa'] ?? 0);
            $actual += $acoa > 0 ? $acoa : (float)($c['est'] ?? 0);
            if ($acoa > 0) $hasReal = true;
        }
        return ['error' => 'No', 'estimated' => round($estimated, 2), 'actual' => round($actual, 2), 'has_actual' => $hasReal];
    }

    // Bills Receivable, per payment cycle.
    // target(k) = WIP target of cycle k / total estimate cost x project value.
    // actual(k) = amount credited to Contract Income with a date in cycle k.
    // Cycle 1 is open at the start and the last cycle is open at the end, the
    // same as the WIP cycles.
    public function actionCfbillsreceivablecycles()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $pid = $this->_cfPid();
        if (!$pid) { return ['error' => 'No project selected']; }
        $db = Yii::$app->db;

        $wip = $this->actionCfwipcycles();
        $proj = $db->createCommand(
            "SELECT start_date, duration, project_value FROM projects WHERE Project_Id=:pid", [':pid' => $pid]
        )->queryOne();
        $totalCost = (float)($wip['total_cost'] ?? 0);
        $projectValue = (float)($proj['project_value'] ?? 0);
        $wipCycles = $wip['cycles'] ?? [];
        if (!$proj || empty($wipCycles) || $totalCost <= 0 || $projectValue <= 0) {
            return ['error' => 'No', 'cycles' => [], 'current_cycle' => 0, 'current_target' => 0];
        }

        $contractIncomeId = $db->createCommand(
            "SELECT id FROM accounts_item WHERE name='Contract Income' AND Status=0 LIMIT 1"
        )->queryScalar();

        $base = strtotime($proj['start_date']);
        $duration = (int)$proj['duration'];
        $cycleLen = (int)round((float)$db->createCommand(
            "SELECT target_value FROM cf_dashboard_targets WHERE project_id=:pid AND metric_key='billing_cycle'",
            [':pid' => $pid]
        )->queryScalar());
        $n = count($wipCycles);

        $cycles = [];
        foreach ($wipCycles as $i => $c) {
            $k = $i + 1;
            $from = ($k === 1) ? '1900-01-01' : date('Y-m-d', $base + (($k - 1) * $cycleLen) * 86400);
            $to   = ($k === $n) ? '2999-12-31' : date('Y-m-d', $base + (min($k * $cycleLen, $duration) - 1) * 86400);
            $actual = 0.0;
            if ($contractIncomeId) {
                $actual = (float)$db->createCommand(
                    "SELECT COALESCE(SUM(amount), 0) FROM journalvoucher
                     WHERE creditacnt = :cid AND project_id = :pid AND COALESCE(delete_status, 0) = 0
                       AND date BETWEEN :from AND :to",
                    [':cid' => (int)$contractIncomeId, ':pid' => $pid, ':from' => $from, ':to' => $to]
                )->queryScalar();
            }
            $cycles[] = [
                'cycle'     => $k,
                'end_label' => $c['end_label'],
                'target'    => round($c['target'] / $totalCost * $projectValue, 2),
                'actual'    => round($actual, 2),
            ];
        }

        $current = max(1, min($n, (int)$wip['current_cycle']));
        return [
            'error'          => 'No',
            'cycles'         => $cycles,
            'current_cycle'  => $current,
            'current_target' => $cycles[$current - 1]['target'],
        ];
    }

    // WIP-to-billing cycle time (Cashflow Dashboard, WIP panel): the
    // length in days of the MOST RECENT CLOSED WIP accrual cycle — NOT
    // an average across every cycle, and NOT extended to today. Cycle 1
    // runs from the project's very first voucher/journalvoucher
    // transaction date to the first Contract Income credit date (a
    // journal entry credits "Contract Income" — see
    // _cfAllContractIncomeDates); every cycle after that runs from the
    // previous Contract Income credit date to the next one. A cycle only
    // has a defined length once it CLOSES with a Contract Income credit
    // — the currently open (not yet billed) cycle has no end date to
    // measure, so it's excluded entirely (same "no Current bar" rule
    // _cfActualC2cCycleGaps already uses). Returns 0 if no Contract
    // Income has ever been credited yet.
    public function actionCfwipavgtime()
    {
        $uid = Yii::$app->user->Id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) { return json_encode(['error' => 'No project selected']); }
        $pid = (int)$projuser->projectid;

        return json_encode([
            'error' => 'No',
            // Headline figure is now the TRUE average across every CLOSED
            // cycle (was previously just the most recent cycle's own
            // length, which didn't match the panel's own "Avg." label).
            'avg_days' => round($this->_cfWipCycleDaysAverage($pid), 1),
            'avg_all_cycles' => round($this->_cfWipCycleDaysAverage($pid), 1),
        ]);
    }

    // True average WIP cycle length across every CLOSED cycle the
    // project has ever had — the ORIGINAL averaging formula, kept
    // around purely for the hover-tooltip context number (see
    // actionCfwipavgtime); the live headline figure is
    // _cfWipCycleDays (most recent cycle only). A cycle only counts
    // once it's closed by a Contract Income credit — the still-open
    // current cycle is excluded, same as _cfWipCycleDays.
    // WIP Cycle 1 = first BANK/CASH transaction (not just any voucher —
    // same starting point actionCfactualc2c's C2C average uses, via
    // _cfActualC2cCycleGaps) -> Bill 1 raised. WIP Cycle n (n>1) = Bill
    // (n-1) raised -> Bill n raised. This alignment matters: C2C is
    // defined as Avg WIP + Avg Bills Receivable (see _cfActualC2cCycle),
    // so both halves must share the exact same cycle-1 starting point for
    // that sum to be meaningful.
    private function _cfWipCycleDaysAverage($pid)
    {
        $firstDate = $this->_cfProjectFirstBankCashDate($pid);
        if (!$firstDate) { return 0; }

        $boundaries = $this->_cfAllContractIncomeDates($pid);
        if (!count($boundaries)) { return 0; }

        $cycleStarts = array_merge([$firstDate], $boundaries);
        $totalDays = 0;
        $cycleCount = count($boundaries);
        for ($i = 0; $i < $cycleCount; $i++) {
            $days = (strtotime($cycleStarts[$i + 1]) - strtotime($cycleStarts[$i])) / 86400;
            $totalDays += max(0, $days);
        }
        return $cycleCount ? $totalDays / $cycleCount : 0;
    }

    // Shared helper — see actionCfwipavgtime for the full rule. Returns
    // 0 if there's no first transaction or no Contract Income credit yet.
    private function _cfWipCycleDays($pid)
    {
        $firstDate = $this->_cfProjectFirstTransactionDate($pid);
        if (!$firstDate) { return 0; }

        $boundaries = $this->_cfAllContractIncomeDates($pid);
        if (!count($boundaries)) { return 0; }

        // Length of the most recent CLOSED cycle: the last two
        // boundaries in sequence (previous credit -> latest credit), or
        // first transaction -> latest credit if only one credit exists
        // so far.
        $cycleStarts = array_merge([$firstDate], $boundaries);
        $latestCreditIdx = count($cycleStarts) - 1;
        $days = (strtotime($cycleStarts[$latestCreditIdx]) - strtotime($cycleStarts[$latestCreditIdx - 1])) / 86400;
        return max(0, $days);
    }

    // Average Bills Receivable collection time (Cashflow Dashboard,
    // Bills Receivable panel): the average number of days between a
    // bill being raised (a journal entry crediting "Contract Income")
    // and the client actually paying it (a Bank receipt — a plain,
    // non-contra voucher debiting a Bank account and crediting the same
    // "Client" account that bill was raised against). "Client" account
    // = the debit side of the Contract Income journal entry, same
    // single-account assumption _cfBillsReceivable already makes for
    // this schema (only one such debit account per project).
    //
    // Shows the length of the MOST RECENT bill-to-payment cycle only —
    // NOT an average across every bill the project has ever raised.
    // Bills and payments are each pulled as their own individual
    // transaction rows (not DISTINCT dates — two bills on the same
    // calendar day must stay two separate data points) and paired up in
    // chronological order (1st bill <-> 1st payment, 2nd <-> 2nd, etc.)
    // so the LATEST bill is matched to the payment at the same
    // chronological position, if one exists. If the latest bill hasn't
    // been paid yet, this shows days-elapsed-so-far (today - bill date)
    // — a bill cycle has no future boundary to eventually close it the
    // way a WIP cycle does (see actionCfwipavgtime), so a running count
    // is shown instead of leaving the panel blank. Returns 0 if no bill
    // has ever been raised.
    public function actionCfbillsreceivableavgtime()
    {
        $uid = Yii::$app->user->Id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) { return json_encode(['error' => 'No project selected']); }
        $pid = (int)$projuser->projectid;

        return json_encode([
            'error' => 'No',
            // Headline figure is now the TRUE average across every bill
            // raised so far (was previously just the most recent bill's
            // own cycle, which didn't match the panel's own "Avg." label).
            'avg_days' => round($this->_cfCollectionCycleDaysAverage($pid), 1),
            'avg_all_cycles' => round($this->_cfCollectionCycleDaysAverage($pid), 1),
        ]);
    }

    // True average collection time across every bill the project has
    // ever raised — the ORIGINAL averaging formula, kept around purely
    // for the hover-tooltip context number (see
    // actionCfbillsreceivableavgtime); the live headline figure is
    // _cfCollectionCycleDays (most recent bill only). An unpaid bill
    // still counts here too, using today as its end date so far — same
    // convention as the single-cycle version.
    private function _cfCollectionCycleDaysAverage($pid)
    {
        $contractIncomeId = Yii::$app->db->createCommand(
            "SELECT id FROM accounts_item WHERE name='Contract Income' AND Status=0 LIMIT 1"
        )->queryScalar();
        if (!$contractIncomeId) { return 0; }

        $clientAcctId = Yii::$app->db->createCommand(
            "SELECT j.debitacnt FROM journalvoucher j
             WHERE j.creditacnt = :cid AND j.project_id = :pid
             LIMIT 1",
            [':cid' => (int)$contractIncomeId, ':pid' => $pid]
        )->queryScalar();
        if (!$clientAcctId) { return 0; }

        $billDates = Yii::$app->db->createCommand(
            "SELECT date FROM journalvoucher
             WHERE creditacnt = :cid AND project_id = :pid
             ORDER BY date ASC",
            [':cid' => (int)$contractIncomeId, ':pid' => $pid]
        )->queryColumn();
        if (!count($billDates)) { return 0; }

        $paymentDates = Yii::$app->db->createCommand(
            "SELECT v.date FROM voucher v
             INNER JOIN accounts_item ai ON ai.id = v.account_id
             WHERE ai.account_type = 2 AND v.creditacnt = :cid
               AND v.contra = 0 AND v.project_id = :pid
             ORDER BY v.date ASC",
            [':cid' => (int)$clientAcctId, ':pid' => $pid]
        )->queryColumn();

        $today = date('Y-m-d');
        $totalDays = 0;
        $count = count($billDates);
        for ($i = 0; $i < $count; $i++) {
            $endDate = isset($paymentDates[$i]) ? $paymentDates[$i] : $today;
            $days = (strtotime($endDate) - strtotime($billDates[$i])) / 86400;
            $totalDays += max(0, $days);
        }
        return $count ? $totalDays / $count : 0;
    }

    // Shared helper — see actionCfbillsreceivableavgtime for the full
    // rule. Returns 0 if Contract Income has never been credited, or no
    // client account can be identified.
    private function _cfCollectionCycleDays($pid)
    {
        $contractIncomeId = Yii::$app->db->createCommand(
            "SELECT id FROM accounts_item WHERE name='Contract Income' AND Status=0 LIMIT 1"
        )->queryScalar();
        if (!$contractIncomeId) { return 0; }

        $clientAcctId = Yii::$app->db->createCommand(
            "SELECT j.debitacnt FROM journalvoucher j
             WHERE j.creditacnt = :cid AND j.project_id = :pid
             LIMIT 1",
            [':cid' => (int)$contractIncomeId, ':pid' => $pid]
        )->queryScalar();
        if (!$clientAcctId) { return 0; }

        $billDates = Yii::$app->db->createCommand(
            "SELECT date FROM journalvoucher
             WHERE creditacnt = :cid AND project_id = :pid
             ORDER BY date ASC",
            [':cid' => (int)$contractIncomeId, ':pid' => $pid]
        )->queryColumn();
        if (!count($billDates)) { return 0; }

        $paymentDates = Yii::$app->db->createCommand(
            "SELECT v.date FROM voucher v
             INNER JOIN accounts_item ai ON ai.id = v.account_id
             WHERE ai.account_type = 2 AND v.creditacnt = :cid
               AND v.contra = 0 AND v.project_id = :pid
             ORDER BY v.date ASC",
            [':cid' => (int)$clientAcctId, ':pid' => $pid]
        )->queryColumn();

        // Only the MOST RECENT bill's own cycle — paired to the payment
        // at the same chronological index, if one exists yet.
        $today = date('Y-m-d');
        $lastIdx = count($billDates) - 1;
        $endDate = isset($paymentDates[$lastIdx]) ? $paymentDates[$lastIdx] : $today;
        $days = (strtotime($endDate) - strtotime($billDates[$lastIdx])) / 86400;
        return max(0, $days);
    }

    // Shared helper: the date of the project's very first voucher or
    // journalvoucher transaction (whichever is earliest) — the start
    // point for actionCfwipavgtime's first WIP cycle. Returns null if
    // the project has no transactions yet.
    private function _cfProjectFirstTransactionDate($pid)
    {
        $voucherMin = Yii::$app->db->createCommand(
            "SELECT MIN(date) FROM voucher WHERE project_id=:pid",
            [':pid' => $pid]
        )->queryScalar();
        $journalMin = Yii::$app->db->createCommand(
            "SELECT MIN(date) FROM journalvoucher WHERE project_id=:pid",
            [':pid' => $pid]
        )->queryScalar();

        if ($voucherMin && $journalMin) { return min($voucherMin, $journalMin); }
        return $voucherMin ?: ($journalMin ?: null);
    }

    // Shared helper: the date of the project's very first transaction
    // touching any Bank or Cash account (account_type 1 or 2) — the
    // shared Cycle-1 starting point for C2C, WIP, and (via that) Bills
    // Receivable's averages, so all three reconcile as C2C = WIP + Bills
    // Receivable. Returns null if the project has no Bank/Cash
    // transactions yet.
    private function _cfProjectFirstBankCashDate($pid)
    {
        return Yii::$app->db->createCommand(
            "SELECT MIN(d) FROM (
                SELECT v.date AS d FROM voucher v
                INNER JOIN accounts_item ai ON ai.id IN (v.account_id, v.creditacnt)
                WHERE ai.account_type IN (1,2) AND v.project_id = :pid1
                UNION ALL
                SELECT j.date FROM journalvoucher j
                INNER JOIN accounts_item ai ON ai.id IN (j.debitacnt, j.creditacnt)
                WHERE ai.account_type IN (1,2) AND j.project_id = :pid2
             ) t",
            [':pid1' => $pid, ':pid2' => $pid]
        )->queryScalar();
    }

    // Work In Progress panel breakdown (Cashflow Dashboard tooltip): same
    // computation as actionCfwip — including the same accrual-cycle date
    // scoping (see _cfLatestContractIncomeDate) — but instead of a
    // single total, buckets every contributing amount by its Project
    // Expenditure group (accounts_sub, via subgroup_accounts) for the
    // click-to-open bar chart. Accounts not mapped into any Project
    // Expenditure subgroup (e.g. a Party/Liability account like Pearl
    // Enterprises, routed into WIP via the balance<=0 rule rather than
    // being a real expense head) are bucketed as "Vendors and Others".
    // Each bucket also carries its individual contributing account heads
    // (name + amount) so the frontend can show a per-account hover
    // tooltip on each bar. No proportional scaling is needed here (that
    // was only an artifact of the old Bills-Receivable-value deduction)
    // — every underlying query is already date-restricted to the open
    // cycle, so the bucket totals sum to actionCfwip's total exactly.
    public function actionCfwipbreakdown()
    {
        $uid = Yii::$app->user->Id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) { return json_encode(['error' => 'No project selected']); }
        $pid = (int)$projuser->projectid;

        $sinceDate = $this->_cfLatestContractIncomeDate($pid);

        $groupOf = function ($acctId) {
            $name = Yii::$app->db->createCommand(
                "SELECT s.name FROM subgroup_accounts sa
                 INNER JOIN accounts_sub s ON s.id = sa.subgrp_id
                 WHERE sa.account_id = :id AND sa.accountschedule_id = 0
                 LIMIT 1",
                [':id' => (int)$acctId]
            )->queryScalar();
            if (!$name || $name === 'Current Liabilities' || $name === 'Current Assets') {
                return 'Vendors and Others';
            }
            return $name;
        };

        // buckets[group] = total; bucketAccounts[group][acctId] = amount
        $buckets = [];
        $bucketAccounts = [];
        $addTo = function ($group, $acctId, $amount) use (&$buckets, &$bucketAccounts) {
            if ($amount == 0) { return; }
            if (!isset($buckets[$group])) { $buckets[$group] = 0; $bucketAccounts[$group] = []; }
            $buckets[$group] += $amount;
            if (!isset($bucketAccounts[$group][$acctId])) { $bucketAccounts[$group][$acctId] = 0; }
            $bucketAccounts[$group][$acctId] += $amount;
        };

        $byAccount = $this->_cfBankCashPaymentsByDebitAccount($pid, $sinceDate, null);
        foreach ($byAccount as $acctId => $amt) {
            $accountType = (int)Yii::$app->db->createCommand(
                "SELECT account_type FROM accounts_item WHERE id=:id", [':id' => $acctId]
            )->queryScalar();
            if ($accountType === 8) {
                $addTo($groupOf($acctId), $acctId, $amt);
                continue;
            }
            $balance = $this->_cfAccountDebitBalance($acctId, $pid);
            if ($balance <= 0) {
                $addTo($groupOf($acctId), $acctId, $amt);
            }
        }

        // Batch-resolve every contributing account id's name in one query.
        $allAcctIds = [];
        foreach ($bucketAccounts as $accts) {
            foreach ($accts as $acctId => $amt) { $allAcctIds[$acctId] = true; }
        }
        $acctNames = [];
        if ($allAcctIds) {
            $nameRows = Yii::$app->db->createCommand(
                "SELECT id, name FROM accounts_item WHERE id IN (" . implode(',', array_keys($allAcctIds)) . ")"
            )->queryAll();
            foreach ($nameRows as $nr) { $acctNames[$nr['id']] = $nr['name']; }
        }

        $rows = [];
        foreach ($buckets as $name => $amount) {
            if ($amount <= 0) { continue; }
            $members = [];
            foreach ($bucketAccounts[$name] as $acctId => $amt) {
                if ($amt <= 0) { continue; }
                $members[] = [
                    'name' => $acctNames[$acctId] ?? ('Account #' . $acctId),
                    'amount' => round($amt, 2),
                ];
            }
            usort($members, function ($a, $b) { return $b['amount'] <=> $a['amount']; });
            $rows[] = ['name' => $name, 'amount' => round($amount, 2), 'members' => $members];
        }
        usort($rows, function ($a, $b) { return $b['amount'] <=> $a['amount']; });

        return json_encode([
            'error' => 'No',
            'accounts' => $rows,
        ]);
    }

    // Shared helper: the length, in days, of EACH cash-to-cash cycle —
    // where each cycle's length is the gap between consecutive Contract
    // Income credit-side transactions for the selected project — EXCEPT
    // the first cycle, which is measured from the project's very first
    // Bank/Cash transaction (any Bank/Cash account, project-scoped) to
    // the first Contract Income credit. If there are N Contract Income
    // credits, there are N gaps (cycle 1 = first Bank/Cash txn ->
    // credit 1; cycle 2 = credit 1 -> credit 2; ...; cycle N = credit
    // N-1 -> credit N). Returns an array of ['date' => creditDate,
    // 'days' => gapDays] — one entry per gap/cycle, in ascending date
    // order — or an empty array if there's no first Bank/Cash
    // transaction, no Contract Income account head, or no Contract
    // Income credits yet. Used by both actionCfactualc2c (which
    // averages these) and actionCfactualc2cbreakdown (which shows them
    // individually).
    private function _cfActualC2cCycleGaps($pid)
    {
        $firstBankCashDate = Yii::$app->db->createCommand(
            "SELECT MIN(d) FROM (
                SELECT v.date AS d FROM voucher v
                INNER JOIN accounts_item ai ON ai.id IN (v.account_id, v.creditacnt)
                WHERE ai.account_type IN (1,2) AND v.project_id = :pid1
                UNION ALL
                SELECT j.date FROM journalvoucher j
                INNER JOIN accounts_item ai ON ai.id IN (j.debitacnt, j.creditacnt)
                WHERE ai.account_type IN (1,2) AND j.project_id = :pid2
             ) t",
            [':pid1' => $pid, ':pid2' => $pid]
        )->queryScalar();

        if (!$firstBankCashDate) {
            return [];
        }

        $contractIncomeId = Yii::$app->db->createCommand(
            "SELECT id FROM accounts_item WHERE name='Contract Income' AND Status=0 LIMIT 1"
        )->queryScalar();

        if (!$contractIncomeId) {
            return [];
        }

        $creditDates = Yii::$app->db->createCommand(
            "SELECT date FROM (
                SELECT date FROM voucher WHERE creditacnt = :id1 AND contra = 0 AND project_id = :pid1
                UNION ALL
                SELECT date FROM journalvoucher WHERE creditacnt = :id2 AND project_id = :pid2
             ) t
             ORDER BY date ASC",
            [':id1' => (int)$contractIncomeId, ':pid1' => $pid, ':id2' => (int)$contractIncomeId, ':pid2' => $pid]
        )->queryColumn();

        if (!$creditDates) {
            return [];
        }

        $prevDate = $firstBankCashDate;
        $gaps = [];
        foreach ($creditDates as $creditDate) {
            $gapDays = (strtotime($creditDate) - strtotime($prevDate)) / 86400;
            $gaps[] = ['date' => $creditDate, 'days' => $gapDays];
            $prevDate = $creditDate;
        }

        return $gaps;
    }

    // Cash-to-Cash Cycle = Avg WIP (_cfWipCycleDaysAverage) + Avg Bills
    // Receivable (_cfCollectionCycleDaysAverage), NOT computed
    // independently from payment-to-payment gaps any more. WIP covers
    // start-of-project (first Bank/Cash txn) through each bill being
    // raised; Bills Receivable covers each bill being raised through
    // that same bill's payment — together they span the complete cycle,
    // so this sum is definitionally the same thing "cash out to cash
    // back in" measures, per explicit request that the three figures
    // reconcile exactly (C2C = WIP + Bills Receivable, always).
    // _cfActualC2cCycleGaps/actionCfactualc2cbreakdown's own per-cycle
    // payment-to-payment bar chart is intentionally left as a separate,
    // unchanged view.
    private function _cfActualC2cCycle($pid)
    {
        return $this->_cfWipCycleDaysAverage($pid) + $this->_cfCollectionCycleDaysAverage($pid);
    }

    // Actual Cash-to-Cash Cycle panel (Cashflow Dashboard) — see
    // _cfActualC2cCycle for the full explanation.
    public function actionCfactualc2c()
    {
        $uid = Yii::$app->user->Id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) { return json_encode(['error' => 'No project selected']); }
        $pid = (int)$projuser->projectid;

        return json_encode([
            'error' => 'No',
            'total' => round($this->_cfActualC2cCycle($pid), 2),
        ]);
    }

    // Cash-to-Cash Cycle panel breakdown (Cashflow Dashboard popup): one
    // bar per completed cycle, showing that cycle's own length in days
    // (see _cfActualC2cCycleGaps) — bars labelled by the month/year the
    // cycle's Contract Income credit landed. Unlike Total
    // Investment/Bills Receivable, there's no "Current" bar here — a
    // cycle's length is only defined once it CLOSES with a Contract
    // Income credit; an open, not-yet-credited cycle has no end date to
    // measure against. Also returns the plain average across all
    // cycles (same figure as the live dial's actionCfactualc2c total).
    public function actionCfactualc2cbreakdown()
    {
        $uid = Yii::$app->user->Id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) { return json_encode(['error' => 'No project selected']); }
        $pid = (int)$projuser->projectid;

        $gaps = $this->_cfActualC2cCycleGaps($pid);

        $rows = [];
        foreach ($gaps as $g) {
            $rows[] = ['name' => date('M Y', strtotime($g['date'])), 'amount' => round($g['days'], 2)];
        }

        $sum = 0;
        foreach ($rows as $r) { $sum += $r['amount']; }
        $average = count($rows) ? $sum / count($rows) : 0;

        return json_encode([
            'error' => 'No',
            'accounts' => $rows,
            'average' => round($average, 2),
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
            "SELECT Name, start_date, end_date, duration FROM projects WHERE Project_Id=$pid"
        )->queryOne();
        $proj_start = $project ? $project['start_date'] : date('Y-m-d');

        // All activities for this project
        $activities = $connection->createCommand(
            "SELECT sa.id, sa.name, sa.scheduleitem_id, sa.activity_id, sa.duration, sa.delay,
                    sa.completed_status, sa.start_date, sa.end_date, sa.act_start_date,
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
             ) ac ON ac.activity_id = sa.activity_id
             LEFT JOIN workgroup_activities_new wa ON wa.id = sa.activity_id
             WHERE sa.projectId=$pid AND sa.status=0
             ORDER BY COALESCE(wa.sortorder, 999999) ASC, sa.id ASC"
        )->queryAll();

        foreach ($activities as &$a) {
            // Use old_duration as the current planned duration for the bar
            $a['duration'] = (float)($a['old_duration'] ?? $a['duration']);
            $a['projected_duration'] = $a['duration'];
            // Anchor: if activity has started use reported start, otherwise use planned start
            $plannedStart  = (!empty($a['start_date'])     && $a['start_date']     !== '0000-00-00') ? $a['start_date']     : '';
            $reportedStart = (!empty($a['spr_start_date']) && $a['spr_start_date'] !== '0000-00-00') ? $a['spr_start_date'] : '';
            $hasStarted    = (int)($a['pr_report_count'] ?? 0) > 0 && (float)($a['cumulated_qty'] ?? 0) > 0;
            $anchorStart   = ($hasStarted && $reportedStart) ? $reportedStart : $plannedStart;
            $a['spr_start_date'] = $anchorStart;
            if ((int)($a['pr_report_count'] ?? 0) > 0
                && $anchorStart
                && !empty($a['last_report_date'])
                && (float)($a['cumulated_qty'] ?? 0) > 0
                && (float)($a['quantity']      ?? 0) > 0)
            {
                $elapsed = max(1, (strtotime(date('Y-m-d')) - strtotime($anchorStart)) / 86400 + 1);
                $a['projected_duration'] = ($elapsed / (float)$a['cumulated_qty']) * (float)$a['quantity'];
                $a['delay'] = max(0, $a['projected_duration'] - $a['duration']);
            }
        }
        unset($a);

        // Compute MB (Measurement Book) costs per WBS activity — adds SC actual costs
        $mbCostByWbs = [];
        $mbRows = $connection->createCommand(
            "SELECT entries FROM wo_measurement_book WHERE project_id=:pid AND sent_status=1 AND delete_status=0",
            [':pid' => $pid]
        )->queryAll();
        foreach ($mbRows as $mb) {
            foreach (json_decode($mb['entries'] ?? '[]', true) ?: [] as $entry) {
                $wbsId = (int)($entry['activity_id'] ?? 0);
                if (!$wbsId) continue;
                foreach ($entry['tasks'] ?? [] as $task) {
                    $wd = (float)($task['work_done'] ?? 0);
                    $rt = (float)($task['rate']      ?? 0);
                    $mbCostByWbs[$wbsId] = ($mbCostByWbs[$wbsId] ?? 0.0) + $rt * $wd;
                }
            }
        }
        // Merge MB cost into each activity as actual_work_done = GRN cost + MB cost
        foreach ($activities as &$a) {
            $wbsId = (int)($a['activity_id'] ?? 0);
            $a['actual_work_done'] = (float)($a['actual_cost'] ?? 0) + ($mbCostByWbs[$wbsId] ?? 0.0);
        }
        unset($a);

        // Anchor overrun expression — used by project bar, IOW groups and IOW items
        // Two cases: progress reported = projected overrun; no progress = start delay
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
                         AND sa.act_start_date IS NOT NULL AND sa.act_start_date != '0000-00-00'
                         AND sa.act_start_date < CURDATE()
                    THEN DATEDIFF(CURDATE(), sa.act_start_date)
                    ELSE 0
                END
            ))";

        // Project-level duration bar — matches Gantt B./A. Duration columns exactly:
        // B. Duration = span from earliest schedule start to latest schedule end across all WBS items
        // A. Duration = span from earliest actual start to latest actual/projected end
        $dur_row = $connection->createCommand("
            SELECT
                DATEDIFF(MAX(wbs.b_end_plain), MIN(wbs.b_start_plain)) + 1 AS budgeted,
                CASE WHEN MIN(wbs.a_start) IS NOT NULL AND MAX(wbs.a_end) IS NOT NULL
                     THEN DATEDIFF(MAX(wbs.a_end), MIN(wbs.a_start)) + 1
                     ELSE NULL END AS actual,
                MIN(wbs.b_start_plain) AS b_start_date,
                MAX(wbs.b_end_plain) AS b_end_date,
                MAX(wbs.a_end) AS a_end_date
            FROM (
                SELECT
                    MIN(COALESCE(NULLIF(sa.act_start_date, '0000-00-00'), sa.start_date)) AS b_start_plain,
                    MAX(DATE_ADD(COALESCE(NULLIF(sa.act_start_date, '0000-00-00'), sa.start_date),
                                 INTERVAL GREATEST(1, COALESCE(sa.old_duration, 1)) - 1 DAY)) AS b_end_plain,
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
        // Planned Duration/Completion shown in the Project Snapshot card
        // (#gsc-tip) come from the Gantt/WBS rollup ($dur_row) — the same
        // span the Gantt's own "B. Duration" column reports (earliest
        // scheduleactivities.start_date to latest .end_date across the
        // whole WBS) — NOT from the project master's Duration/End Date
        // fields (projects.duration/end_date), which only reflect what was
        // typed into the Edit Project form and can disagree with the real
        // schedule once activities are added/resequenced. A prior version
        // of this code sourced Planned Duration from projects.duration
        // instead; reverted per explicit direction that "as per the Gantt
        // chart" is the correct answer for how long the project takes.
        // Plan duration and dates are the project's own (Project creation panel):
        // the end date stays fixed unless it is changed manually.
        $proj_budgeted    = ($project && (int)($project['duration'] ?? 0) > 0)
            ? (int)$project['duration']
            : max(1, (int)($dur_row['budgeted']  ?? 0));
        $proj_actual      = max(0, (int)($dur_row['actual']    ?? 0));
        $proj_b_start_raw = $dur_row['b_start_date'] ?? '';
        $proj_b_end_date  = ($project && !empty($project['end_date']) && $project['end_date'] !== '0000-00-00 00:00:00')
            ? substr($project['end_date'], 0, 10)
            : ($dur_row['b_end_date'] ?? '');
        $proj_a_end_date  = $dur_row['a_end_date'] ?? '';

        // Project delay = DATEDIFF(actual_end, planned_end) of critical activities
        // actual_end comes from projected end (anchor_overrun applied to planned end)
        $proj_delay_row = $connection->createCommand("
            SELECT
                MAX(sa.end_date) AS planned_end,
                MAX(DATE_ADD(sa.end_date, INTERVAL GREATEST(0, ROUND($anchorOverrunExpr)) DAY)) AS actual_end
            FROM scheduleactivities sa
            LEFT JOIN schedule_progress_report spr ON spr.activity_id = sa.id
            LEFT JOIN (
                SELECT activity_id, MAX(report_date) AS last_report_date, SUM(currentqty) AS cumulated_qty
                FROM schedule_progress_report_log GROUP BY activity_id
            ) rpt ON rpt.activity_id = sa.id
            WHERE sa.projectId = $pid AND sa.status = 0 AND sa.critical_status = 'Yes'
        ")->queryOne();
        $proj_planned_end = $proj_delay_row['planned_end'] ?? '';
        $proj_actual_end  = $proj_delay_row['actual_end']  ?? '';
        $proj_delay = ($proj_planned_end && $proj_actual_end)
            ? max(0, (int)((strtotime($proj_actual_end) - strtotime($proj_planned_end)) / 86400))
            : 0;
        // Plan start = the project's start date (Project creation panel)
        $proj_b_start_date = ($project && !empty($project['start_date']) && $project['start_date'] !== '0000-00-00 00:00:00')
            ? substr($project['start_date'], 0, 10)
            : $proj_b_start_raw;
        // Actual start = earliest reported start date across all activities
        $actual_start_row = $connection->createCommand(
            "SELECT MIN(spr.start_date) AS actual_start
             FROM schedule_progress_report spr
             JOIN scheduleactivities sa ON sa.id = spr.activity_id
             WHERE sa.projectId = $pid AND sa.status = 0
               AND spr.start_date IS NOT NULL AND spr.start_date != '0000-00-00'"
        )->queryOne();
        $proj_actual_start = ($actual_start_row && !empty($actual_start_row['actual_start']))
            ? $actual_start_row['actual_start'] : '';
        // End date = actual end of critical activities (planned end + delay)
        $proj_b_end_computed = $proj_actual_end ?: $proj_b_end_date;

        // Per-activity overrun (projected_duration - old_duration, floored at 0) using the
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
                    MAX(sa.actual_end_date) AS actual_end_date,
                    MAX(DATE_ADD(sa.end_date, INTERVAL GREATEST(0, ROUND($anchorOverrunExpr)) DAY)) AS proj_end_date
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
                    MAX(sa.actual_end_date) AS actual_end_date,
                    MAX(DATE_ADD(sa.end_date, INTERVAL GREATEST(0, ROUND($anchorOverrunExpr)) DAY)) AS proj_end_date
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

        // Write dashboard-computed totals to chatbot cache — exactly what the UI displays
        $cache_estimated   = 0.0;
        $cache_actual_work = 0.0;
        $cache_completed   = 0;
        $cache_total       = 0;
        foreach ($activities as $a) {
            $cache_estimated   += (float)($a['activity_cost']    ?? 0);
            $cache_actual_work += (float)($a['actual_work_done'] ?? 0);
            $cache_total++;
            if (!empty($a['completed_status'])) $cache_completed++;
        }
        MetricsCacheHelper::writeMetrics($pid, [
            'estimated_cost'       => $cache_estimated,
            'actual_cost_work_done'=> $cache_actual_work,
            'total_activities'     => $cache_total,
            'completed_activities' => $cache_completed,
        ]);

        return json_encode([
            'error'          => 'No',
            'project_name'   => $project ? $project['Name'] : '',
            'project_bar'    => [
                'budgeted'      => $proj_budgeted,
                'actual'        => $proj_actual,
                'delay'         => $proj_delay,
                'b_start_date'  => $proj_b_start_date,
                'actual_start'  => $proj_actual_start,
                'b_end_date'    => $proj_b_end_date,
                'proj_end_date' => $proj_b_end_computed,
                'a_end_date'    => $proj_a_end_date,
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
            "SELECT id, name, duration, old_duration, unit, quantity, completed_status, start_date, act_start_date, actual_start_date, actual_end_date, end_date, resource_units, critical_status
             FROM scheduleactivities WHERE id=$actid AND projectId=$pid"
        )->queryOne();
        $kpi = $this->_buildKpi($act, $pid, $connection);
        file_put_contents(Yii::getAlias('@app') . '/runtime/kpi_debug.json', json_encode($kpi, JSON_PRETTY_PRINT));
        return json_encode(['error'=>'No', 'kpi' => $kpi, 'activity_name' => $act ? $act['name'] : '']);
    }


    /**
     * Public wrapper around _buildKpi() for callers outside this
     * controller (e.g. ChatbotController::toolDiagnoseProductivityGap)
     * that need the exact same per-activity KPI data — same convention
     * as computeRoiThresholds() being called directly by
     * ChatbotController rather than re-implemented. Looks the activity
     * up by id, scoped to the given project, same query shape as
     * actionKpidebug() above. Returns null if not found.
     */
    public function buildKpiForActivity($activityId, $projectId)
    {
        $connection = \Yii::$app->db;
        $actid = (int)$activityId;
        $pid = (int)$projectId;
        $act = $connection->createCommand(
            "SELECT id, name, duration, old_duration, unit, quantity, completed_status, start_date, act_start_date, actual_start_date, actual_end_date, end_date, resource_units, critical_status
             FROM scheduleactivities WHERE id=$actid AND projectId=$pid"
        )->queryOne();
        if (!$act) return null;
        return $this->_buildKpi($act, $pid, $connection);
    }

    /**
     * Public wrappers around the Cashflow dashboard's existing private
     * Cash-to-Cash Cycle / Total Investment / Contribution Margin
     * helpers, for ChatbotController::toolCheckRoiHealth() — the CCC
     * driver reuses these AS-IS rather than re-deriving cash-cycle or
     * investment math from per-bill dates, per explicit instruction to
     * just use the dashboard's existing actual/target C2C. Same
     * public-wrapper convention as buildKpiForActivity() /
     * checkRoiHealthForProject() above.
     */
    public function cfActualC2cCycle($pid)
    {
        return $this->_cfActualC2cCycle((int)$pid);
    }

    public function cfNetInvestmentCurrent($pid)
    {
        return $this->_cfNetInvestmentAsOf((int)$pid, null);
    }

    public function cfContributionMarginCurrent($pid)
    {
        return $this->_cfContributionMarginValue((int)$pid);
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
            ? round(((float)($act['old_duration'] ?? 0) / $target_qty) * $wh, 3) : 0;
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

        // Resource capacity — latest reported qty per resource (one bar per
        // resource row, not grouped by type — two resources sharing the same
        // type each get their own bar), from the Report Resource popup's own
        // save table. One row per resource_id: the most recent date logged
        // for that resource on this activity.
        $res_latest_rows = $connection->createCommand(
            "SELECT srl.resource_id, srl.name, srl.quantity, rt.Name AS type_name
             FROM schedule_resource_report_log srl
             LEFT JOIN resourcetype rt ON rt.ResourceType_Id = srl.resource_type_id
             INNER JOIN (
                 SELECT resource_id, MAX(date) AS max_date
                 FROM schedule_resource_report_log
                 WHERE activity_id=$actid
                 GROUP BY resource_id
             ) latest ON latest.resource_id = srl.resource_id AND latest.max_date = srl.date
             WHERE srl.activity_id=$actid
             ORDER BY srl.name ASC"
        )->queryAll();

        // Last reported date for target production needle
        $lrd = $connection->createCommand(
            "SELECT MAX(report_date) AS last_date FROM schedule_progress_report_log
             WHERE activity_id=$actid AND currentqty > 0"
        )->queryOne();
        $last_reported_date = ($lrd && !empty($lrd['last_date'])) ? $lrd['last_date'] : '';

        // Activity start anchor:
        // - Started (has progress): use the reported start date from schedule_progress_report
        // - Not started: use the budgeted start date from scheduleactivities
        $spr = $connection->createCommand(
            "SELECT start_date FROM schedule_progress_report
             WHERE activity_id=$actid LIMIT 1"
        )->queryOne();
        $reported_start = ($spr && !empty($spr['start_date']) && $spr['start_date'] != '0000-00-00')
            ? $spr['start_date'] : '';
        // act_start_date is the true immutable baseline — start_date can now be
        // rescheduled to today by GetRelationcorrect() for an activity that has
        // no progress and whose baseline has already passed (see HelperComponent),
        // so it can no longer be used to detect/measure start delay here.
        $planned_start = (!empty($act['act_start_date']) && $act['act_start_date'] != '0000-00-00')
            ? $act['act_start_date']
            : ((!empty($act['start_date']) && $act['start_date'] != '0000-00-00') ? $act['start_date'] : '');
        // Use reported start if activity has started, otherwise planned start
        $act_start_date = ($actual_qty > 0 && $reported_start)
            ? $reported_start
            : $planned_start;

        $duration   = (float)($act['duration']     ?? 0);
        $b_duration = (float)($act['old_duration'] ?? 0);
        $planned_per_day = ($b_duration > 0 && $target_qty > 0)
            ? round($target_qty / $b_duration, 3) : 0;

        $target_prod = $planned_per_day;
        $elapsed_work       = 0; // for Productivity: (last_reported - start + 1) - break_days (days)
        $elapsed_work_hours = 0; // for Cycle Time only: (elapsed calendar hours) - break hours (hours)
        $elapsed_calendar = 0; // for Capacity Utilisation and the "Elapsed" display only: today - start + 1
        $elapsed          = 0; // sent to JS as k.elapsed (calendar-based)
        $start_delay = 0;
        $today_ts = strtotime(date('Y-m-d'));
        if ($act_start_date && $last_reported_date && $actual_qty > 0) {
            // break_hour column stores hours (user enters break hours directly).
            // elapsed_work is day-based (used by Productivity), so break hours are
            // converted to days for it. Cycle Time uses its own hours-based elapsed
            // value instead, so break hours are subtracted directly with no conversion.
            $break_days       = $wh > 0 ? ($cum_break / $wh) : 0;
            $elapsed_work     = max(1, (strtotime($last_reported_date) - strtotime($act_start_date)) / 86400 + 1 - $break_days);
            $elapsed_calendar = max(1, ($today_ts - strtotime($act_start_date)) / 86400 + 1);
            $elapsed          = $elapsed_calendar;
            $elapsed_work_hours = max(1, ((strtotime($last_reported_date) - strtotime($act_start_date)) / 86400 + 1) * $wh - $cum_break);
            $actual_prod  = round($actual_qty / $elapsed_work, 3);
            $actual_cycle = round($elapsed_work_hours / $actual_qty, 3);
            $cap_max  = round($elapsed_calendar * $wh, 2);
            $cap_used = round(max(0, $cap_max - $cum_break), 2); // cap_used = cap_max - cumulative break hours
        } elseif ($planned_start && $actual_qty == 0 && strtotime($planned_start) < $today_ts) {
            // No progress yet but start date has passed — count start delay as elapsed
            $start_delay      = (int)floor(($today_ts - strtotime($planned_start)) / 86400);
            $elapsed_calendar = $start_delay;
            $elapsed          = $start_delay;
        }
        // Start delay only has meaning BEFORE an activity has actually
        // started (the elseif branch above — no progress reported yet,
        // planned start already passed). The instant any progress exists
        // (actual_qty > 0), start delay stops applying entirely and the
        // projection becomes a pure performance/pace calculation:
        //   (last_reported_date − actual_start_date) ÷ quantity_done × target_quantity
        // — anchored to the LAST REPORT DATE, never to today. Using
        // elapsed_calendar (today − start) here was wrong even for a
        // still-in-progress activity, not just a finished one: it made
        // the projected duration keep drifting later every single day
        // regardless of whether any new work was actually reported, and
        // for a fully-reported activity it kept growing forever after
        // completion (a 4-day activity finished on time would show a
        // 53-day "delay" just because 53 days have since passed). elapsed_work
        // (start to last_reported_date, already used correctly for
        // Productivity above) is the right basis any time actual_qty > 0.
        $projected_duration = ($actual_qty > 0 && $elapsed_work > 0)
            ? (int)round($elapsed_work / $actual_qty * $target_qty)
            : ($start_delay > 0 ? $b_duration + $start_delay : 0);

        // Tasks for Resource Productivity panel
        $sa_act = $connection->createCommand(
            "SELECT activity_id FROM scheduleactivities WHERE id=$actid"
        )->queryOne();
        $wbn_id = $sa_act ? (int)$sa_act['activity_id'] : 0;
        $wbn_row = $wbn_id ? $connection->createCommand(
            "SELECT activity_Id FROM workgroup_activities_new WHERE id=$wbn_id"
        )->queryOne() : null;
        $masterActId = ($wbn_row && $wbn_row['activity_Id']) ? (int)$wbn_row['activity_Id'] : $wbn_id;
        // The project's own tasks of this activity (never the Activity Library's).
        $task_rows = $actid ? $connection->createCommand(
            "SELECT stn.task_Id AS task_id, COALESCE(stn.task_name, at.task_name) AS task_name, COALESCE(stn.task_unit, at.task_unit) AS task_unit,
                    COALESCE(stn.task_productivity, 0) AS productivity,
                    COALESCE(stn.task_resource_units, 1) AS resource_units,
                    COALESCE(stn.task_qty, 0) AS task_qty,
                    COALESCE(stn.Budgeted_Duration, 0) AS planned_duration
             FROM schedule_task_new stn
             LEFT JOIN activity_tasks at ON at.id = stn.task_Id
             WHERE stn.activity_Id = " . (int)$actid . " AND stn.status = 0 AND stn.task_Id > 0
             ORDER BY COALESCE(stn.sort_order, 999999), stn.id"
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

        $tasks = array_map(function($t) use ($target_qty, $actual_qty, $elapsed, $elapsed_work, $taskMbQty) {
            $tqu = (float)$t['task_qty'];
            $actual = ($elapsed_work > 0 && $actual_qty > 0 && $tqu > 0)
                ? round($actual_qty * $tqu / $elapsed_work, 3) : 0;
            $mbQty = $taskMbQty[(int)$t['task_id']] ?? 0;
            return [
                'name'             => $t['task_name'],
                'unit'             => $t['task_unit'],
                'val'              => round((float)$t['productivity'] * max(1, (float)$t['resource_units']), 3),
                'actual'           => $actual,
                'qty'              => round($tqu * $target_qty, 3),
                'planned_duration' => (float)$t['planned_duration'],
                'actual_duration'  => ($elapsed_work > 0 && $mbQty > 0) ? round($elapsed_work / $mbQty, 3) : 0,
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
            'resource_latest'      => array_map(function($r){
                return ['resource_id' => (int)$r['resource_id'], 'name' => $r['name'], 'type_name' => $r['type_name'] ?: '', 'qty' => round((float)$r['quantity'], 2)];
            }, $res_latest_rows),
            'tasks'                => $tasks,
            'elapsed'              => (int)round($elapsed),
            'start_delay'          => $start_delay,
            'projected_duration'   => $projected_duration,
            'planned_end_date'     => ($act['end_date'] ?? ''),
            'reported_start_date'  => $reported_start ?: '',
            'adj_start_date'       => $planned_start ?: $act_start_date,
            'adj_end_date'         => $planned_start
                ? date('Y-m-d', strtotime($planned_start . ' +' . (max(1, $b_duration) - 1) . ' days'))
                : ($act['end_date'] ?? ''),
            'critical'             => (($act['critical_status'] ?? '') === 'Yes'),
            'completed'            => ((int)($act['completed_status'] ?? 0) === 1),
            'project_name'         => $project_name,
        ];
    }

    // ROI Target Thresholds baseline derivation — see
    // docs/dashboard-chatbot-roi-health-tool.md section 2 for the full
    // spec. Computes and upserts one roi_target_thresholds row for
    // $project_id. Deterministic PHP only — no LLM involvement, per the
    // spec's "deterministic PHP, never LLM-inferred" principle.
    //
    // NOTE: this only builds the BASELINE thresholds row. It does NOT
    // implement the check_roi_health tool (comparing live values against
    // these thresholds) — that is explicitly deferred to a future
    // session per the spec's section 4 / the instruction that created
    // this function.
    //
    // pur_min / cde_min derivation: PUR and CDE are not yet first-class
    // metrics anywhere else in this codebase (no existing "PUR"/"CDE"
    // dashboard function to call). Built from the three per-activity
    // metric families _buildKpi() already computes — Capacity
    // Utilisation, Cycle Time, and Productivity — per explicit mapping:
    //   - CDE (Required Capacity ÷ Deployed Capacity) = Capacity
    //     Utilisation alone = cap_used / cap_max — how effectively the
    //     available time was used by the resources.
    //   - PUR (Productive Hours ÷ Available Hours) = an EQUAL-WEIGHT
    //     (50/50) blend, WITHIN each activity, of:
    //       - Productivity ratio  = actual_productivity / target_productivity
    //         (how much they produced per day)
    //       - Cycle Time ratio    = target_cycle_time / actual_cycle_time
    //         (how efficiently they worked — how long to produce one
    //         unit; NOT inverted the other way, since dividing target by
    //         actual already reads "higher is better", the same
    //         direction as the other two ratios, without needing any
    //         sign flip)
    // Both the per-activity CDE and per-activity PUR are then aggregated
    // into ONE project-level pur_min/cde_min, weighted by each
    // activity's ESTIMATED cost (activity_cost) — heavier activities
    // pull the project-level threshold more than small ones, since they
    // matter more for ROI. Activities with zero estimated cost, or
    // missing the inputs a given ratio needs, are excluded from that
    // ratio's weighted average rather than treated as 0.
    //
    // cost_variance_max_adverse_pct derivation: back-calculated from
    // target_roi_pct against planned revenue (projects.project_value)
    // and planned cost (SUM of every activity's estimated activity_cost)
    // — the max adverse cost overrun, as a % of planned cost, that still
    // leaves target_roi_pct of margin. Planned profit = revenue - cost;
    // required minimum profit to hit target_roi_pct is
    // revenue * target_roi_pct/100 — this baseline derivation uses
    // profit as a % of revenue, NOT the Cashflow Dashboard's live
    // Contribution Margin / Average Investment ROI formula (see
    // actionCfroi), because Contribution Margin (Contract Income minus
    // actual Project Expenditure) is purely ledger-driven and doesn't
    // exist yet before any site activity — planned revenue/cost from the
    // estimate is the only data available at baseline. The LIVE
    // check_roi_health/recommend_corrective_action comparison, once a
    // project has real ledger activity, DOES use the dashboard's actual
    // Contribution Margin / Investment formula (see toolCheckRoiHealth's
    // own projected_roi_pct comment) — only this baseline threshold
    // stays on the planned-data proxy. The tolerable overrun is: current
    // planned profit minus that required minimum, expressed as a % of
    // planned cost.
    //
    // critical_float_min_days: a scheduling buffer POLICY, not derived
    // from ROI math (per spec) — defaults to 5 days here; callers may
    // override via $criticalFloatMinDays.
    // $persist: when true (default), upserts the computed values into
    // roi_target_thresholds as the project's baseline — the normal
    // "compute the baseline" call. When false, computes and returns the
    // exact same figures WITHOUT writing to the table — used by
    // check_roi_health to read live current PUR/CDE/planned-cost figures
    // for comparison against the ALREADY-STORED baseline, without
    // silently overwriting that baseline every time someone asks a
    // health question.
    public function computeRoiThresholds($projectId, $targetRoiPct = 40.00, $criticalFloatMinDays = 5, $persist = true)
    {
        $pid = (int)$projectId;
        $connection = \Yii::$app->db;

        // Same activity list / cost joins as actionPerformancedashboard's
        // main query (see lines ~3104-3166), trimmed to just what this
        // baseline needs: activity id and estimated activity_cost.
        $activities = $connection->createCommand(
            "SELECT sa.id, COALESCE(cost.activity_cost, 0) AS activity_cost
             FROM scheduleactivities sa
             LEFT JOIN (
                 SELECT pen.activity_Id,
                        COALESCE(pen.activity_qty, 0) * COALESCE(SUM(pern.quantity * pern.rate), 0) AS activity_cost
                 FROM pricing_estimate_new pen
                 LEFT JOIN pricing_estimate_resources_new pern
                        ON pern.activity_id = pen.activity_Id AND pern.project_id = pen.project_Id AND pern.pricing_status = 0
                 WHERE pen.project_Id = $pid AND pen.pricing_status = 0
                 GROUP BY pen.activity_Id
             ) cost ON cost.activity_Id = sa.activity_id
             WHERE sa.projectId=$pid AND sa.status=0"
        )->queryAll();

        // For each activity, pull the same raw fields _buildKpi() uses to
        // derive target_productivity/actual_productivity and
        // cap_max/cap_used, WITHOUT running the full _buildKpi() (which
        // also builds tasks/resources/cause-of-delay — unneeded overhead
        // here). We reuse _buildKpi() directly instead: it is already the
        // single source of truth for these ratios (ChatbotController's
        // KPI tool is an "exact port" of it), and re-deriving the same
        // logic separately would risk the two silently drifting apart.
        $purWeightedSum = 0; $purWeightTotal = 0;
        $cdeWeightedSum = 0; $cdeWeightTotal = 0;
        $plannedCostTotal = 0;

        foreach ($activities as $actRow) {
            $weight = (float)$actRow['activity_cost'];
            $plannedCostTotal += $weight;
            if ($weight <= 0) { continue; }

            $act = $connection->createCommand(
                "SELECT sa.id, sa.name, sa.scheduleitem_id, sa.activity_id, sa.duration, sa.delay,
                        sa.completed_status, sa.start_date, sa.end_date, sa.act_start_date,
                        sa.actual_start_date, sa.actual_end_date, sa.unit, sa.resource_units,
                        sa.quantity, sa.old_duration, sa.critical_status
                 FROM scheduleactivities sa WHERE sa.id = " . (int)$actRow['id']
            )->queryOne();
            if (!$act) { continue; }

            $kpi = $this->_buildKpi($act, $pid, $connection);
            if (!$kpi) { continue; }

            // PUR: equal-weight (50/50) blend of Productivity ratio and
            // Cycle Time ratio, WITHIN this activity. Each sub-ratio only
            // contributes if its own inputs are available; if only one
            // of the two is available, PUR for this activity falls back
            // to that one ratio alone rather than being skipped entirely
            // or averaged against a missing value.
            $productivityRatio = null;
            if ((float)$kpi['target_productivity'] > 0 && (float)$kpi['actual_productivity'] > 0) {
                $productivityRatio = (float)$kpi['actual_productivity'] / (float)$kpi['target_productivity'];
            }
            $cycleTimeRatio = null;
            if ((float)$kpi['actual_cycle_time'] > 0 && (float)$kpi['target_cycle_time'] > 0) {
                $cycleTimeRatio = (float)$kpi['target_cycle_time'] / (float)$kpi['actual_cycle_time'];
            }
            if ($productivityRatio !== null || $cycleTimeRatio !== null) {
                $parts = array_filter([$productivityRatio, $cycleTimeRatio], function ($v) { return $v !== null; });
                $pur = array_sum($parts) / count($parts);
                $purWeightedSum += $pur * $weight;
                $purWeightTotal += $weight;
            }

            // CDE: Capacity Utilisation alone — only meaningful once
            // cap_max is non-zero (activity has started).
            if ((float)$kpi['cap_max'] > 0) {
                $cde = (float)$kpi['cap_used'] / (float)$kpi['cap_max'];
                $cdeWeightedSum += $cde * $weight;
                $cdeWeightTotal += $weight;
            }
        }

        $purMin = $purWeightTotal > 0 ? round($purWeightedSum / $purWeightTotal, 4) : 0.0000;
        $cdeMin = $cdeWeightTotal > 0 ? round($cdeWeightedSum / $cdeWeightTotal, 4) : 0.0000;

        // Planned revenue = projects.project_value (contract value) —
        // same field already used elsewhere as the project's revenue
        // baseline (see FinancerequestsController::actionCfprojectexpenditure,
        // Project Expenditure panel's 90% target).
        $projectValue = $connection->createCommand(
            "SELECT project_value FROM projects WHERE Project_Id=$pid"
        )->queryScalar();
        $plannedRevenue = (float)($projectValue ?: 0);

        $costVarianceMaxAdversePct = 0.00;
        if ($plannedRevenue > 0 && $plannedCostTotal > 0) {
            $plannedProfit = $plannedRevenue - $plannedCostTotal;
            $requiredMinProfit = $plannedRevenue * ((float)$targetRoiPct / 100);
            $tolerableOverrun = $plannedProfit - $requiredMinProfit; // rupees of cost cushion before target ROI is missed
            $costVarianceMaxAdversePct = round(max(0, $tolerableOverrun) / $plannedCostTotal * 100, 2);
        }

        // ccc_max_days: NOT back-calculated from target_roi_pct. Per
        // explicit instruction, this reads the Cashflow dashboard's own
        // existing user-editable Cash-to-Cash Cycle target — the same
        // number the user already sets via the pencil icon on the C2C
        // dial (cf_dashboard_targets, metric_key='c2c_cycle') — rather
        // than deriving a threshold from ROI math against per-bill data
        // this app doesn't capture. Falls back to the project's current
        // actual C2C if no target has been set yet, so a fresh project
        // reads as "on target" rather than red against a phantom 0-day
        // threshold.
        $cccTarget = $connection->createCommand(
            "SELECT target_value FROM cf_dashboard_targets WHERE project_id=$pid AND metric_key='c2c_cycle'"
        )->queryScalar();
        $cccMaxDays = $cccTarget !== false && (float)$cccTarget > 0
            ? (int)round((float)$cccTarget)
            : (int)round($this->_cfActualC2cCycle($pid));

        $data = [
            'project_id' => $pid,
            'target_roi_pct' => $targetRoiPct,
            'pur_min' => $purMin,
            'cde_min' => $cdeMin,
            'cost_variance_max_adverse_pct' => $costVarianceMaxAdversePct,
            'critical_float_min_days' => (int)$criticalFloatMinDays,
            'ccc_max_days' => $cccMaxDays,
        ];

        if ($persist) {
            $now = date('Y-m-d H:i:s');
            $existing = $connection->createCommand(
                "SELECT id FROM roi_target_thresholds WHERE project_id=$pid"
            )->queryScalar();

            if ($existing) {
                $data['last_reviewed_at'] = $now;
                $connection->createCommand()->update('roi_target_thresholds', $data, ['project_id' => $pid])->execute();
            } else {
                $data['baseline_computed_at'] = $now;
                $connection->createCommand()->insert('roi_target_thresholds', $data)->execute();
            }
        }

        return array_merge($data, [
            'project_id' => $pid,
            'planned_revenue' => $plannedRevenue,
            'planned_cost_total' => round($plannedCostTotal, 2),
        ]);
    }

    public function actionCostdashboardbatch()
    {
        $uid      = Yii::$app->user->Id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return json_encode(['error' => 'No project selected', 'data' => []]);
        $pid = (int)$projuser->projectid;
        $db  = \Yii::$app->db;

        // Load all schedule activities for the project
        $schedActs = $db->createCommand(
            "SELECT sa.id, sa.activity_id, sa.quantity, sa.name
             FROM scheduleactivities sa WHERE sa.projectId=:pid AND sa.status=0",
            [':pid' => $pid]
        )->queryAll();
        if (!$schedActs) return json_encode(['error' => 'No', 'data' => []]);

        $actIds    = array_column($schedActs, 'id');
        $wbIds     = array_unique(array_column($schedActs, 'activity_id'));
        $actMap    = [];  // schedId => sa row
        foreach ($schedActs as $sa) { $actMap[(int)$sa['id']] = $sa; }

        // Last reported qty per schedule activity
        $lqRows = $db->createCommand(
            "SELECT activity_id, MAX(cumulated_qty) AS lq FROM schedule_progress_report
             WHERE activity_id IN (".implode(',', $actIds).") GROUP BY activity_id"
        )->queryAll();
        $lastQtyMap = [];
        foreach ($lqRows as $r) { $lastQtyMap[(int)$r['activity_id']] = (float)$r['lq']; }

        // Estimate activity qty per WBS id
        $penRows = $db->createCommand(
            "SELECT activity_Id, activity_qty FROM pricing_estimate_new
             WHERE activity_Id IN (".implode(',', array_map('intval', $wbIds)).") AND project_Id=:pid AND pricing_status=0",
            [':pid' => $pid]
        )->queryAll();
        $estQtyMap = [];
        foreach ($penRows as $r) { $estQtyMap[(int)$r['activity_Id']] = (float)$r['activity_qty']; }

        // All resources per WBS id — activity-specific store_indents for correct stock
        $resRows = $db->createCommand(
            "SELECT pern.activity_id, pern.pricing_resourceid, pern.resourcetype_Id AS type_id,
                    pern.rate, pern.quantity AS res_qty, pern.task_ids, pern.resource_Id,
                    grn.actual_unit_cost, grn.grn_qty,
                    si.stock_at_site
             FROM pricing_estimate_resources_new pern
             LEFT JOIN (
                 SELECT por.allocation_id,
                        SUM(CAST(g.GRN_Quantity AS DECIMAL(15,4))*por.rate)/NULLIF(SUM(CAST(g.GRN_Quantity AS DECIMAL(15,4))),0) AS actual_unit_cost,
                        SUM(CAST(g.GRN_Quantity AS DECIMAL(15,4))) AS grn_qty
                 FROM purchase_order_resources por
                 JOIN goods_received_note g ON g.GRN_Purchase_Order=por.order_id AND g.GRN_Item=por.resource_id
                 JOIN purchase_orders po ON po.order_id=por.order_id
                 WHERE po.project_id=:pid2 AND po.delete_status=0 AND por.delete_status=0
                 GROUP BY por.allocation_id
             ) grn ON grn.allocation_id=pern.pricing_resourceid
             LEFT JOIN (
                 SELECT si2.resource_id, si2.activity_id AS si_activity_id, si2.stock_at_site FROM store_indents si2
                 INNER JOIN (SELECT resource_id, activity_id, MAX(id) AS mx FROM store_indents WHERE project_id=:pid3 GROUP BY resource_id, activity_id) lsi
                 ON lsi.resource_id=si2.resource_id AND lsi.activity_id=si2.activity_id AND si2.id=lsi.mx
             ) si ON si.resource_id=pern.resource_Id AND si.si_activity_id=pern.activity_id
             WHERE pern.activity_id IN (".implode(',', array_map('intval', $wbIds)).") AND pern.project_id=:pid AND pern.pricing_status=0",
            [':pid' => $pid, ':pid2' => $pid, ':pid3' => $pid]
        )->queryAll();

        // Group resources by WBS id
        $resByWbs = [];
        foreach ($resRows as $r) {
            $resByWbs[(int)$r['activity_id']][] = $r;
        }

        // MB data: task amounts and work done by task_id, grouped by wbsId
        $taskAmountById   = [];  // task_id => total value
        $taskWorkDoneById = [];  // task_id => total work_done qty
        $mbQtyByWbs       = [];  // wbsId   => total MB qty
        $mbs = $db->createCommand(
            "SELECT entries FROM wo_measurement_book WHERE project_id=:pid AND sent_status=1 AND delete_status=0",
            [':pid' => $pid]
        )->queryAll();
        foreach ($mbs as $mb) {
            foreach (json_decode($mb['entries'] ?? '[]', true) ?: [] as $entry) {
                $wbsId = (int)($entry['activity_id'] ?? 0);
                if (!$wbsId) continue;
                $mbQtyByWbs[$wbsId] = ($mbQtyByWbs[$wbsId] ?? 0.0) + (float)($entry['qty'] ?? 0);
                foreach ($entry['tasks'] ?? [] as $task) {
                    $wd  = (float)($task['work_done'] ?? 0);
                    $rt  = (float)($task['rate']      ?? 0);
                    $tid = (int)($task['task_id']     ?? 0);
                    if ($tid) {
                        $taskAmountById[$tid]   = ($taskAmountById[$tid]   ?? 0.0) + $rt * $wd;
                        $taskWorkDoneById[$tid] = ($taskWorkDoneById[$tid] ?? 0.0) + $wd;
                    }
                }
            }
        }

        // Compute est and acoa per schedule activity — same logic as actionCostdashboardactivity
        $data = [];
        foreach ($schedActs as $sa) {
            $schedId   = (int)$sa['id'];
            $wbId      = (int)$sa['activity_id'];
            $schedQty  = (float)$sa['quantity'];
            $estQty    = $estQtyMap[$wbId] ?? 0;
            $lastQty   = $lastQtyMap[$schedId] ?? 0;
            $resources = $resByWbs[$wbId] ?? [];
            $ratio     = ($schedQty > 0) ? $estQty / $schedQty : 0.0;

            // Estimated cost
            $unitCost = 0.0;
            foreach ($resources as $r) { $unitCost += (float)$r['res_qty'] * (float)$r['rate']; }
            $est = $unitCost * $estQty;

            // Actual cost — mirrors actionCostdashboardactivity logic
            $actualTotal = 0.0;
            foreach ($resources as $r) {
                $typeId  = (int)$r['type_id'];
                $resQty  = (float)$r['res_qty'];
                $plannedConsumption = $resQty * $ratio;

                // Actual unit cost
                if ($typeId === 4) {
                    // Subcontractor: weighted avg from MB task work done
                    $taskIds = array_filter(array_map('intval', explode(',', $r['task_ids'] ?? '')));
                    $wdVal = 0.0; $wdQty = 0.0;
                    foreach ($taskIds as $tid) {
                        $wdVal += $taskAmountById[$tid]   ?? 0.0;
                        $wdQty += $taskWorkDoneById[$tid] ?? 0.0;
                    }
                    $actUnit = $wdQty > 0 ? $wdVal / $wdQty : null;
                } else {
                    $actUnit = (in_array($typeId, [2, 6, 7, 8]) && $r['actual_unit_cost'] !== null)
                        ? (float)$r['actual_unit_cost'] : null;
                }

                // Actual consumption
                if ($typeId === 4) {
                    $taskIds = array_filter(array_map('intval', explode(',', $r['task_ids'] ?? '')));
                    $scWd = 0.0;
                    foreach ($taskIds as $tid) { $scWd += $taskWorkDoneById[$tid] ?? 0.0; }
                    $actualConsumption = ($lastQty > 0 && $scWd > 0)
                        ? $scWd / $lastQty : $plannedConsumption;
                } elseif (in_array($typeId, [2, 6, 7, 8])) {
                    $indentRaised = ($r['stock_at_site'] !== null);
                    if ($indentRaised && $lastQty > 0) {
                        $actualConsumption = max(0, (float)($r['grn_qty'] ?? 0) - (float)($r['stock_at_site'] ?? 0)) / $lastQty;
                    } else {
                        $actualConsumption = $plannedConsumption;
                    }
                } else {
                    $actualConsumption = $plannedConsumption;
                }

                if ($actUnit !== null) {
                    $actualTotal += $actUnit * $actualConsumption;
                }
            }

            // estUCTotal = Σ(rate × resQty × ratio) — matches JS renderCdCostOfActivity formula
            // ratio = estQty/schedQty, so estUCTotal = unitCost × ratio
            $estUCTotal = $unitCost * $ratio;
            $acoa    = round($actualTotal * $schedQty, 2);
            $estwd   = round($estUCTotal * $lastQty, 2);
            $actwd   = $actualTotal > 0 ? round($actualTotal * $lastQty, 2) : $estwd;
            $data[$schedId] = ['est' => round($est, 2), 'acoa' => $acoa, 'estwd' => $estwd, 'actwd' => $actwd];
        }

        // Cache the project-level totals so the chatbot reads the same numbers the UI displays
        $totalEstWd = 0.0;
        $totalActWd = 0.0;
        foreach ($data as $row) {
            $totalEstWd += (float)$row['estwd'];
            $totalActWd += (float)$row['actwd'];
        }
        \app\components\MetricsCacheHelper::writeMetrics($pid, [
            'estimated_cost_work_done'  => $totalEstWd,
            'actual_cost_work_done'     => $totalActWd,
        ]);

        return json_encode(['error' => 'No', 'data' => $data]);
    }

    public function actionCostdashboardactivity()
    {
        $pid   = $this->_cfPid();
        if (!$pid) return json_encode(['error' => 'No project selected', 'items' => []]);

        $actid = (int)($_POST['actid'] ?? 0);
        $wanIdParam = (int)($_POST['wan_id'] ?? 0);
        if (!$actid && !$wanIdParam) return json_encode(['error' => 'No activity', 'items' => []]);

        $db = \Yii::$app->db;

        // Estimate-only activities (e.g. the auto-seeded "Project Overhead"/
        // "Project General Expenses" — see ProjectsController::
        // _seedProjectOverheadActivity) deliberately have no scheduleactivities
        // row (no Gantt bar). The caller passes wan_id (workgroup_activities_new.id)
        // directly for these instead of actid; resource-cost data below is
        // keyed by $wbId regardless of which path resolved it, so it works
        // identically either way. Only the task/work-done panels ($actid-keyed,
        // schedule_task_new/schedule_progress_report) stay empty for these —
        // there is no Gantt-derived task/progress data to show, which is
        // correct, not an error.
        if ($actid) {
            $sa = $db->createCommand(
                "SELECT name, activity_id, quantity, unit FROM scheduleactivities WHERE id = :actid AND projectId = :pid",
                [':actid' => $actid, ':pid' => $pid]
            )->queryOne();
            if (!$sa) return json_encode(['error' => 'Not found', 'items' => []]);
            $wbId = (int)$sa['activity_id'];
        } else {
            $wan = $db->createCommand(
                "SELECT id, activity_Name AS name, cycle_Quantity AS quantity, cycle_Unit AS unit
                 FROM workgroup_activities_new WHERE id = :wid AND project_Id = :pid",
                [':wid' => $wanIdParam, ':pid' => $pid]
            )->queryOne();
            if (!$wan) return json_encode(['error' => 'Not found', 'items' => []]);
            $sa   = $wan;
            $wbId = (int)$wan['id'];
            $actid = 0; // no Gantt bar — task/progress-report queries below intentionally find nothing
        }

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
                        pern.resource_Id AS resource_id,
                        COALESCE(rt.Name, '') AS type_name,
                        pern.rate, pern.quantity,
                        ear.est_resource_quantity AS alloc_qty,
                        pern.resourcetype_Id AS type_id,
                        COALESCE(r.Unit, '') AS unit,
                        r.Resource_group_Id AS group_id,
                        COALESCE(rg.Resource_group_Name, '') AS group_name,
                        grn_cost.actual_unit_cost,
                        grn_cost.grn_qty,
                        si.stock_at_site,
                        pern.task_ids,
                        r.Resource_Acc_Id,
                        acc_cost.total_expense  AS acc_total_expense,
                        acc_cost.first_txn_date AS acc_first_txn_date
                 FROM pricing_estimate_resources_new pern
                 LEFT JOIN resources r      ON r.Resource_Id        = pern.resource_Id
                 LEFT JOIN resourcetype rt  ON rt.ResourceType_Id   = pern.resourcetype_Id
                 LEFT JOIN resource_group rg ON rg.Resource_group_Id = r.Resource_group_Id
                 LEFT JOIN workgroup_activities_new wa ON wa.id = pern.activity_id
                 -- Yield's \"Resource Quantity\" is the Activity Library
                 -- master allocation (est_resource_quantity), keyed by the
                 -- library activity id (wa.activity_Id) — NOT
                 -- pern.quantity, which is a different, project-scaled
                 -- figure used by the Cost/Rate formulas instead.
                 LEFT JOIN estactivity_resources ear
                     ON ear.estactivity_id = wa.activity_Id
                    AND ear.est_resource_id = pern.resource_Id
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
                     SELECT si.resource_id, si.stock_at_site, si.avg_consumption
                     FROM store_indents si
                     INNER JOIN (
                         SELECT resource_id, MAX(id) AS max_id
                         FROM store_indents
                         WHERE project_id = :pid3 AND activity_id = :wid2
                         GROUP BY resource_id
                     ) latest ON latest.resource_id = si.resource_id
                              AND si.id = latest.max_id
                 ) si ON si.resource_id = pern.resource_Id
                 LEFT JOIN (
                     -- Actual cost of a Support Resource = current ledger
                     -- balance on its linked Account Head for this project
                     -- (payment vouchers + journal vouchers, the two
                     -- ledger-entry tables this app posts transactions
                     -- into) divided by Elapsed Days (Today minus Project
                     -- Planned Start Date, computed once below in PHP and
                     -- reused for every row). Support Resources never get
                     -- a GRN (they are not purchase-order items), so this
                     -- is a distinct actual-cost source from grn_cost
                     -- above, not a replacement for it.
                     SELECT t.account_id,
                            SUM(t.amount) AS total_expense,
                            MIN(t.date) AS first_txn_date
                     FROM (
                         SELECT account_id, date, amount FROM voucher
                         WHERE project_id = :pid4 AND delete_status = 0
                         UNION ALL
                         SELECT debitacnt AS account_id, date, amount FROM journalvoucher
                         WHERE project_id = :pid4 AND delete_status = 0
                     ) t
                     GROUP BY t.account_id
                 ) acc_cost ON acc_cost.account_id = r.Resource_Acc_Id
                 WHERE pern.activity_id = :wid AND pern.project_id = :pid AND pern.pricing_status = 0
                 ORDER BY pern.rate DESC",
                [':wid' => $wbId, ':pid' => $pid, ':pid2' => $pid, ':pid3' => $pid, ':wid2' => $wbId, ':pid4' => $pid]
            )->queryAll();
            // Build task planned qty per unit from schedule_task_new (Schedule tab → Activity → Task → Qty/Unit)
            $taskQtyMap  = [];  // task_id => task_qty (planned qty per activity unit)
            $taskUnitMap = [];  // task_id => task_unit from activity_tasks
            $stnRows = $db->createCommand(
                "SELECT stn.task_Id AS task_id, stn.task_qty, COALESCE(stn.task_unit, at.task_unit, '') AS task_unit
                 FROM schedule_task_new stn
                 LEFT JOIN activity_tasks at ON at.id = stn.task_Id
                 WHERE stn.activity_Id = :actid",
                [':actid' => $actid]
            )->queryAll();
            foreach ($stnRows as $stn) {
                $taskQtyMap[(int)$stn['task_id']]  = (float)($stn['task_qty'] ?? 0);
                $taskUnitMap[(int)$stn['task_id']] = $stn['task_unit'] ?? '';
            }

            // Cumulate work_done across ALL sent MBs — cum_work_done = Qty Till Today
            $taskWorkDoneById = []; // task_id => SUM(work_done) across all MBs
            $taskAmountById   = []; // task_id => SUM(rate × work_done) across all MBs
            $mbQty = 0.0; $mbAmount = 0.0;
            $allMbs = $db->createCommand(
                "SELECT entries FROM wo_measurement_book
                 WHERE project_id=:pid AND sent_status=1 AND delete_status=0",
                [':pid' => $pid]
            )->queryAll();
            foreach ($allMbs as $mb) {
                foreach (json_decode($mb['entries'] ?? '[]', true) ?: [] as $entry) {
                    if ((int)($entry['activity_id'] ?? 0) !== $wbId) continue;
                    $mbQty += (float)($entry['qty'] ?? 0);
                    foreach ($entry['tasks'] ?? [] as $task) {
                        $wd  = (float)($task['work_done'] ?? 0);
                        $rt  = (float)($task['rate']      ?? 0);
                        $tid = (int)($task['task_id']     ?? 0);
                        if ($tid) {
                            $taskWorkDoneById[$tid] = ($taskWorkDoneById[$tid] ?? 0.0) + $wd;
                            $taskAmountById[$tid]   = ($taskAmountById[$tid]   ?? 0.0) + $rt * $wd;
                            $mbAmount += $rt * $wd;
                        }
                    }
                }
            }
            $scActualUnitCost = $mbQty > 0 ? $mbAmount / $mbQty : null;
            $actualUnitCostTotal = 0.0;
            $schedQty = (float)($sa['quantity'] ?? 0);
            $ratio    = ($schedQty > 0) ? $actQty / $schedQty : 0.0;

            // Support Resources' Actual Consumption = Actual Project Duration
            // (days) itself — reuses the exact same "Actual Duration"
            // definition already shown project-wide elsewhere in this app
            // (Project Snapshot card / Project Duration panel): Planned
            // Duration + Start Delay when no progress has been reported yet
            // (Project Overhead itself is never scheduled on the Gantt, so
            // it never has progress), Elapsed-Work-based projection once
            // progress exists. Computed once per activity, reused for every
            // Support Resource row below.
            $supportResActualDuration = null;
            $elapsedDays = null;
            $proj = $db->createCommand(
                "SELECT start_date, end_date FROM projects WHERE Project_Id = :pid",
                [':pid' => $pid]
            )->queryOne();
            if ($proj && $proj['start_date'] && $proj['end_date']) {
                $plannedStartTs = strtotime($proj['start_date']);
                $plannedEndTs   = strtotime($proj['end_date']);
                $plannedDuration = ($plannedEndTs > $plannedStartTs) ? round(($plannedEndTs - $plannedStartTs) / 86400) + 1 : 0;
                $todayTs = strtotime(date('Y-m-d'));
                if ($plannedDuration > 0 && $plannedStartTs) {
                    $startDelay = ($todayTs > $plannedStartTs) ? (int)floor(($todayTs - $plannedStartTs) / 86400) : 0;
                    $elapsedDays = max(0, $startDelay);
                    $supportResActualDuration = $plannedDuration + $elapsedDays;
                }
            }

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
                    $actUnit = (in_array($typeId, [2, 6, 7, 8]) && $r['actual_unit_cost'] !== null
                        ? (float)$r['actual_unit_cost']
                        : null);
                }
                // Support Resources (and any other resource linked to an
                // Account Head but never GRN'd, e.g. type 22) get their
                // actual cost from real project expenses instead: current
                // ledger balance on that Account Head for this project
                // (payment + journal vouchers) ÷ Elapsed Days (Today −
                // Project Planned Start Date — the same figure shown on
                // the Elapsed Days dial, not tied to the account's own
                // transaction dates). Only applied when nothing above
                // already produced a value, and only when a transaction
                // actually exists (first_txn_date from the join is NULL
                // until then) — no transaction yet means unknown, not zero.
                if ($actUnit === null && $r['Resource_Acc_Id'] && $r['acc_first_txn_date'] !== null) {
                    $actUnit = (float)$r['acc_total_expense'] / max(1, (int)($elapsedDays ?? 0));
                }
                // Planned Consumption = res_qty × (estQty / schedQty)
                $plannedConsumption = round($resQty * $ratio, 3);

                // Actual Consumption:
                // SC (type 4): task Qty-Till-Today from last MB / lastReportedQty
                // Materials/Consumables/Purchased Inputs/Tools (type 2,6,7,8):
                //   Use saved avg_consumption from store_indents (calculated at indent time)
                //   If no indent raised: use planned consumption
                // All other types: same as planned
                if ($typeId === 4) {
                    $scTaskIds = array_filter(array_map('intval', explode(',', $r['task_ids'] ?? '')));
                    $scWorkDone = 0.0;
                    foreach ($scTaskIds as $stid) {
                        $scWorkDone += $taskWorkDoneById[$stid] ?? 0.0;
                    }
                    $actualConsumption = ($lastQty > 0 && $scWorkDone > 0)
                        ? round($scWorkDone / $lastQty, 3)
                        : $plannedConsumption;
                } elseif (in_array($typeId, [2, 6, 7, 8])) {
                    $indentRaised = ($r['stock_at_site'] !== null);
                    if ($indentRaised && $lastQty > 0) {
                        $grnQty = (float)($r['grn_qty']       ?? 0);
                        $stock  = (float)($r['stock_at_site'] ?? 0);
                        $actualConsumption = round(max(0, $grnQty - $stock) / $lastQty, 3);
                    } else {
                        $actualConsumption = $plannedConsumption;
                    }
                } elseif ($r['Resource_Acc_Id']) {
                    // Support Resources: Unit Cost of Activity uses Actual
                    // Rate × Planned Consumption (same Consumption factor as
                    // Estimate — only the Rate side changes to Actual).
                    // Cost of Resources instead uses Actual Rate × Actual
                    // Duration (see $actualConsumptionForCost below) — the
                    // two panels intentionally use different Consumption
                    // factors for Support Resources.
                    $actualConsumption = $plannedConsumption;
                } else {
                    $actualConsumption = $plannedConsumption;
                }

                $actualConsumptionForCost = $r['Resource_Acc_Id'] && $supportResActualDuration !== null
                    ? round($supportResActualDuration, 3)
                    : $actualConsumption;

                $actualResQty = $actualConsumption;

                // Actual cost contribution = actual_unit_cost × actual_consumption
                $actualContrib = 0.0;
                if ($actUnit !== null) {
                    $actualContrib = (float)$actUnit * $actualConsumption;
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
                    'resource_id'          => (int)($r['resource_id'] ?? 0),
                    'allocation_id'        => (int)($r['pricing_resourceid'] ?? 0),
                    'activity_id'          => (int)$wbId,
                    'task_ids'             => (string)($r['task_ids'] ?? ''),
                    'type_name'            => $r['type_name'],
                    'group_id'             => (int)($r['group_id'] ?? 0),
                    'group_name'           => $r['group_name'] ?? '',
                    'rate'                 => (float)$r['rate'],
                    'type_id'              => $typeId,
                    'unit'                 => $r['unit'],
                    'task_unit'            => $taskUnit,
                    'res_qty'              => $resQty,
                    'alloc_qty'            => isset($r['alloc_qty']) ? (float)$r['alloc_qty'] : $resQty,
                    'consumption'          => round($lastQty * $resQty, 3),
                    'actual_unit_cost'     => $actUnit,
                    'actual_res_qty'       => $actualResQty,
                    'planned_consumption'  => $plannedConsumption,
                    'actual_consumption'   => $actualConsumption,
                    'actual_consumption_cost' => $actualConsumptionForCost,
                    'indent_raised'        => ($r['stock_at_site'] !== null),
                    'has_mb'               => ($typeId === 4 && isset($taskWorkDoneById) && !empty(array_filter(array_map('intval', explode(',', $r['task_ids'] ?? '')), function($tid) use ($taskWorkDoneById){ return ($taskWorkDoneById[$tid] ?? 0) > 0; }))),
                    'has_acc_head'         => !empty($r['Resource_Acc_Id']),
                ];
            }
        }

        return json_encode([
            'activity_name'          => $sa['name'],
            'activity_qty'           => $actQty,
            'schedule_qty'           => (float)($sa['quantity'] ?? 0),
            'unit'                   => $sa['unit'] ?? '',
            'last_report_qty'        => $lastQty,
            'actual_unit_cost_raw'   => round($actualUnitCostTotal, 4),
            // Project-level Planned/Actual Duration (Planned Duration +
            // Start Delay) — same figures used for the Support Resources'
            // Yield ratio above, exposed directly so the Yield panel can
            // show a single Planned-vs-Actual bar for Project Overhead
            // without recomputing this on the client.
            'planned_duration'       => isset($plannedDuration) ? (int)$plannedDuration : 0,
            'actual_duration'        => isset($supportResActualDuration) ? (int)$supportResActualDuration : 0,
            'elapsed_days'           => isset($elapsedDays) ? (int)$elapsedDays : 0,
            'items'                  => $items,
        ]);
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

        MetricsCacheHelper::refresh($projectid);

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

        MetricsCacheHelper::refresh($projectid);

        $prjct = Projects::findOne($projectid);

        $datarows = '
                <h4 class="panel-title" id="projtitle">
                    <a data-toggle="collapse" data-parent="#accordionprojindex" href="#collapseprojindex">
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
	
	// Deletes an entire WBS schedule item (group) — every activity under it
    // is permanently removed via the same hardDeleteScheduleActivity cascade
    // used for single-activity deletes, so nothing is left orphaned in
    // workgroup_activities_new / pricing tables / procurement data. A
    // conflict check runs first (unless force=1) and reports back in plain
    // English what would also be deleted, matching actionWbsdelete's UX.
    public function actionDeletescheduleitem()
    {
        $itemId = (int)($_POST['itemId'] ?? 0);
        $force  = (bool)($_POST['force'] ?? false);
        if (!$itemId) {
            return json_encode(['error' => 'Yes', 'errortext' => 'No item id.']);
        }

        $model = Wbsscheduleitems::findOne($itemId);
        if (!$model) {
            return json_encode(['error' => 'Yes', 'errortext' => 'Item not found.']);
        }

        $activities = Scheduleactivities::find()
            ->where(['projectId' => $model->projectId, 'scheduleitem_id' => $model->scheduleitem_id])
            ->all();

        if (!$force) {
            $conflicts = [];
            foreach ($activities as $activity) {
                $wanId = (int)$activity->activity_id;
                $conflicts = array_merge($conflicts, $this->checkActivityDeleteConflicts($activity->id, $wanId));
            }
            if (!empty($conflicts)) {
                return json_encode([
                    'error'     => 'Yes',
                    'conflict'  => true,
                    'errortext' => 'Deleting this WBS item will also permanently delete: ' . implode('; ', array_unique($conflicts)) . '. This cannot be undone.',
                ]);
            }
        }

        foreach ($activities as $activity) {
            $this->hardDeleteScheduleActivity($activity->id, ['scheduleitem_id' => $activity->scheduleitem_id, 'activity_id' => $activity->activity_id], (int)$activity->activity_id);
        }

        // hardDeleteScheduleActivity already removes the wbsscheduleitems row
        // once its last activity is gone; only clean up here if it somehow
        // had none to begin with (an empty WBS item deleted with no children).
        $stillExists = Wbsscheduleitems::findOne($itemId);
        if ($stillExists) {
            $db = \Yii::$app->db;
            $wgId = (int)$stillExists->schedulegrp_id;
            $db->createCommand()->delete('wbsscheduleitems', ['scheduleitem_id' => $itemId])->execute();

            $remainWsi = (int)$db->createCommand(
                "SELECT COUNT(*) FROM wbsscheduleitems WHERE schedulegrp_id = :g",
                [':g' => $wgId]
            )->queryScalar();
            if ($remainWsi === 0) {
                $db->createCommand()->delete('workgroups_new', ['Workgroup_Id' => $wgId])->execute();
            }
        }

        return json_encode(['Id' => $itemId, 'error' => 'No']);
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
                    
    				$datarows.='<tr class="activitycontent activitiess" id="enggactivities'.$data['id'].'" data-id="'.$data['id'].'">
    								<td style="text-align:center;">
    									<span class="number">'.($totalindex).'</span>
    								</td>
    								<td>
    									<span id="'.str_replace(' ','',$data['process']).'activityname'.$data['id'].'">'.$data['name'].'</span>
    									<input class="form-control editenggactivityname" style="display:none;" type="text" id="edit'.str_replace(' ','',$data['process']).'activityname'.$data['id'].'" value="'.$data['name'].'">
    									<span class="error"></span>
    								</td>
    								<td>
    									<span id="'.str_replace(' ','',$data['process']).'activityunit'.$data['id'].'" style="display:none;">'.$data['unit'].'</span>
    									<input class="form-control editenggactivityunit" type="text" id="edit'.str_replace(' ','',$data['process']).'activityunit'.$data['id'].'" value="'.$data['unit'].'">
    									<span class="error"></span>
    								</td>
    								<td>
    									<span id="'.str_replace(' ','',$data['process']).'activityquantity'.$data['id'].'" style="display:none;">'.$est_qtyy.'</span>
    									<input type="hidden" id="cumulatedqty'.$data['id'].'" value="'.$cumulatedQty.'">
    									<input class="form-control editenggactivityquantity" type="text" id="edit'.str_replace(' ','',$data['process']).'activityquantity'.$data['id'].'" value="'.$est_qtyy.'" data-original="'.$est_qtyy.'">
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
            $tasks = $connection->createCommand(
                "SELECT stn.task_Id AS Id, COALESCE(stn.task_name, at.task_name) AS task, COALESCE(stn.task_unit, at.task_unit) AS task_unit,
                        COALESCE(stn.task_productivity, at.productivity) AS productivity
                 FROM schedule_task_new stn LEFT JOIN activity_tasks at ON at.id = stn.task_Id
                 WHERE stn.activity_Id = :sa AND stn.status = 0 AND stn.task_Id > 0
                 ORDER BY COALESCE(stn.sort_order, 999999), stn.id", [':sa' => (int)$activityid]
            )->queryAll();
            if (!$tasks) {
                $sql = "SELECT id AS Id, task_name AS task, task_unit, productivity FROM activity_tasks WHERE activity_id='".$estActivityId."' ORDER BY sort_order ASC";
                $tasks = $connection->createCommand($sql)->queryAll();
            }
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

                    // task_qty = SUM(res_qty of all resources mapped to this task) × (estQty / schedQty)
                    $scResQty = (float)($connection->createCommand(
                        "SELECT COALESCE(SUM(quantity), 0) FROM pricing_estimate_resources_new
                         WHERE activity_id=:wid AND project_id=:pid AND pricing_status=0
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
                        <td><input type="text" class="form-control" name="task_unit[]" value="'.htmlspecialchars($task['task_unit']).'"></td>
                        <td><input type="number" step="0.001" class="form-control task-productivity-val" name="task_productivity_val[]" value="'.(!empty($sche_tasks) && $sche_tasks['task_productivity'] > 0 ? number_format((float)$sche_tasks['task_productivity'], 3, '.', '') : number_format((float)$task['productivity'], 3, '.', '')).'" '.$disabledForm.'></td>
                        <td><input type="number" step="0.001" class="form-control" name="task_qty[]" value="'.(!empty($sche_tasks) && $sche_tasks['task_qty'] > 0 ? $sche_tasks['task_qty'] : $computedTaskQty).'"></td>
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
        $sql="SELECT r.Resource_Id,r.Name,r.Unit,r.Price,r.ResourceType_Id,r.Resource_group_Id,r.Resource_Location,r.Vendor_Id,
                     rg.Resource_group_Name AS GroupName
              FROM resources r
              LEFT JOIN resource_group rg ON rg.Resource_group_Id = r.Resource_group_Id
              WHERE r.status='0' AND r.pricing_status=0 ";

        if($restypeid!='')
            $sql.="AND r.ResourceType_Id ='".$restypeid."' ";
        /*if($resourcegroupid!='none' && $resourcegroupid!='')
            $sql.="AND r.Resource_group_Id ='".$resourcegroupid."' ";*/
        if($vendorid!='none' && $vendorid!='')
            $sql.="AND r.Vendor_Id ='".$vendorid."' ";
        if($_POST['name']!='')
            $sql .= "AND r.Name LIKE '%" . $_POST['name'] . "%'";
        $sql.=" GROUP BY r.Name ORDER BY rg.Resource_group_Name ASC, r.Resource_Id ASC, r.Name ASC";
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
            $project = Projects::findOne($_POST['Project_Id']);
            $rescapacity = $project->duration * $project->wrkhours;
            $currentGroup = null;
            $rowIndex = 1;
            foreach($dataProvider AS $key=>$data):
                $groupName = $data['GroupName'] ?: 'Unassigned';
                if($groupName !== $currentGroup):
                    $currentGroup = $groupName;
                    $safeGroup = htmlspecialchars($groupName, ENT_QUOTES);
                    $datarows.='<div class="row resource-group-hdr" style="background:#e8f0f7;color:#2c3e50;font-weight:600;padding:6px 12px;margin:0;">
                                    <span class="icon-folder" style="margin-right:5px;color:#5b8ab5;"></span>'.$safeGroup.'
                                </div>';
                endif;
                $datarows.='<div class="add-activity-search-results-content-wpr row resourcetype'.$data['ResourceType_Id'].'" id="resourcerow'.$data['Resource_Id'].'">
                                <div class="col-md-1">
                                    <label>&nbsp;</label>
                                    <span>'.$rowIndex.'</span>
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
                $rowIndex++;
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
        $_POST['sa_id'] = $_POST['id'] ?? 0;
        return $this->actionWbsdelete();
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

        if(ActivityRelations::find()->where(['status' => 0, 'precedent_activity' => (int)$_POST['firstActivity'], 'dependent_activity' => (int)$_POST['secondActivity']])->exists()){
            return json_encode(['error' => 'Yes', 'errortext' => 'A relationship between these two activities already exists.']);
        }

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
            // Seed the precedent's Planned baseline if it has none yet —
            // RecalculatePlannedDates() below needs an anchor for it.
            if(!$precedentAct->act_start_date || $precedentAct->act_start_date=='0000-00-00'){
                $getDateAfterHoliday = Yii::$app->helper->getDateAfterHoliday(date("d-m-Y"), $projectid);
                $precedentAct->act_start_date = date('Y-m-d', strtotime($getDateAfterHoliday));
                $precedentAct->old_duration = $precedentAct->old_duration ?: 1;
                $precedentAct->save(false);
            }
            $dependentAct = Scheduleactivities::findOne($_POST['secondActivity']);
            if($_POST['lag']==''){
                $_POST['lag'] = 0;
            }
            $lag = $_POST['lag'];
            $dependentAct->lag = $lag;
            $dependentAct->save(false);
            $availableActivites = Scheduleactivities::find()->where(['projectId' => $_POST['projectId']])->andWhere(['status' => 0])->orderBy(['scheduleitem_id' => SORT_ASC])->all();
            if($precedentAct->act_start_date!='' && $precedentAct->act_start_date!='0000-00-00')
            {

                if($model->save(false))
                {

                    // Adding a relationship is an explicit user action —
                    // Planned recalculates for every affected activity.
                    // Actual also refreshes immediately for this page view.
                    Yii::$app->helper->RecalculatePlannedDates($_POST['projectId']);
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
        // Seed the precedent's Planned baseline if it has none yet —
        // RecalculatePlannedDates() below needs an anchor for it. Actual
        // date math for the dependent (start/end per relation type) is no
        // longer hand-computed here — RecalculatePlannedDates() (Planned)
        // and GetRelationcorrect() (Actual) both derive it properly via the
        // real FS/SS/FF/lag CPM propagation.
        if (!$precedentAct->act_start_date || $precedentAct->act_start_date == '0000-00-00') {
            $precedentAct->act_start_date = $precedentAct->start_date ?: date('Y-m-d');
            $precedentAct->old_duration = $precedentAct->old_duration ?: 1;
            $precedentAct->save(false);
        }

        $dependentAct->lag = $_POST['lag'];
        $dependentAct->save(false);

        $availableActivites = Scheduleactivities::find()->where(['projectId' => $model->projectId])->andWhere(['status' => 0])->orderBy(['scheduleitem_id' => SORT_ASC])->all();

        if($precedentAct->act_start_date!='' && $precedentAct->act_start_date!='0000-00-00'){

        if($model->save(false)){

            // Editing a relationship is an explicit user action — Planned
            // recalculates for every affected activity. Actual also
            // refreshes immediately for this page view.
            Yii::$app->helper->RecalculatePlannedDates($model->projectId);
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

            // Deleting a relationship is an explicit user action — Planned
            // recalculates so the dependent's baseline is no longer derived
            // from a relation that no longer exists.
            Yii::$app->helper->ResetUnlinkedDependentToToday($model->projectId, $model->dependent_activity);
            Yii::$app->helper->RecalculatePlannedDates($model->projectId);
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

        // Changing the working-day calendar is an explicit user action that
        // affects the CPM working-day math for both Planned and Actual.
        Yii::$app->helper->RecalculatePlannedDates($ProjectId);
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
        return $this->render('newganttview', ['projectId' => $id, 'project' => $project, 'embedMode' => ($layout === 'false')]);
    }

    /* Experimental "Dependency view" — same Gantt rendering engine as
       actionNewganttchart, but a separate view file (newganttview_dependency)
       that regroups rows into Parallel Activities / Sequenced Activities
       sections instead of the normal WBS-only grouping. Kept fully
       separate so it can be removed cleanly if it doesn't prove useful. */
    public function actionNewganttchartdep($id, $layout = 'true')
    {
        if ($layout === 'false') {
            $this->layout = false;
        }
        Yii::$app->helper->GetRelationcorrect($id);
        $project = Projects::findOne($id);
        return $this->render('newganttview_dependency', ['projectId' => $id, 'project' => $project, 'embedMode' => ($layout === 'false')]);
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
        Yii::$app->helper->RecalculatePlannedDates($dependentAct1->projectId);
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

    // Driven from workgroups_new (every WBS/IOW node for the project),
    // LEFT JOINed out to wbsscheduleitems and scheduleactivities — a bare
    // IOW with no wbsscheduleitems row and no activities yet still
    // returns a row here (scheduleitem_id/dates/duration all NULL), so
    // it appears in the Gantt as a name-only header immediately after
    // creation via actionWbsaddiows(). Previously this started from
    // wbsscheduleitems with an INNER JOIN to scheduleactivities, which
    // silently omitted any WBS node with no scheduled activity at all.
    // The "Support Activities" / Overhead IOW group is excluded outright
    // — a WBS node built from it (Project Overhead, Project General
    // Expenses) is a cost-side Estimate concept seeded automatically for
    // every new project by ProjectsController::_seedProjectOverheadActivity
    // (iow_groups.id=17), never a schedulable Gantt activity, and must
    // never appear in the Gantt chart regardless of whether activities
    // are ever added under it — per explicit direction. Both 17 and 18
    // are excluded: live data has group id 17 for projects created after
    // the auto-seed feature shipped, and a pre-existing legacy id 18 on
    // at least one older project (same "Support Activities" concept,
    // just created before that convention settled on 17).
    public function actionGanttitems()
    {
        $projectId = (int)$_POST['projectid'];
        $rows = \Yii::$app->db->createCommand("
            SELECT go.iow_group_id,
                   -- B. span = baseline (planned start + planned duration); A. span = the
                   -- current dates. A start delay already moves actual_start/end_date
                   -- (GetRelationcorrect), so nothing is added on top of them here.
                   MIN(COALESCE(NULLIF(s.act_start_date, '0000-00-00'), s.actual_start_date)) AS start_date,
                   MAX(DATE_ADD(COALESCE(NULLIF(s.act_start_date, '0000-00-00'), s.actual_start_date),
                                INTERVAL GREATEST(1, COALESCE(s.old_duration, 1)) - 1 DAY)) AS end_date,
                   go.iow_group_id                AS iowGroupid,
                   COALESCE(ig.name, '')          AS iow_group_name,
                   COALESCE(go.sortorder, 999999) AS wg_sortorder,
                   MIN(COALESCE(act_a.a_start, s.actual_start_date)) AS a_min_start,
                   MAX(COALESCE(act_a.a_end_computed, s.actual_end_date)) AS a_max_end,
                   CASE
                     WHEN MIN(COALESCE(act_a.a_start, s.actual_start_date)) IS NOT NULL
                          AND MAX(COALESCE(act_a.a_end_computed, s.actual_end_date)) IS NOT NULL
                     THEN DATEDIFF(MAX(COALESCE(act_a.a_end_computed, s.actual_end_date)),
                                   MIN(COALESCE(act_a.a_start, s.actual_start_date))) + 1
                     ELSE NULL
                   END AS a_duration
            FROM iow_group_order AS go
            LEFT JOIN iow_groups AS ig ON ig.id = go.iow_group_id AND ig.status = 0
            LEFT JOIN scheduleactivities AS s
                 ON s.iow_group_id = go.iow_group_id
                 AND s.projectId = go.project_id
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
            WHERE go.project_id = :projectId
              AND go.iow_group_id NOT IN (17, 18)
            GROUP BY go.iow_group_id
            ORDER BY COALESCE(go.sortorder, 999999) ASC, go.iow_group_id ASC
        ", [':projectId' => $projectId])->queryAll();
        return json_encode(['result' => $rows, 'error' => 'No']);
    }

    public function actionGanttactivities()
    {
        $iowGroupId = (int)($_POST['iowGroupId'] ?? $_POST['itemId'] ?? 0);
        $projectId  = (int)($_POST['projectId'] ?? 0);
        $rows = \Yii::$app->db->createCommand("
            SELECT sa.id, sa.name,
                   sa.activity_id AS wan_id,
                   sa.start_date, sa.act_start_date, sa.actual_start_date, sa.actual_end_date,
                   sa.old_duration, sa.quantity, sa.unit,
                   sa.scheduleitem_id, sa.iow_group_id, sa.critical_status,
                   sa.completed_status,
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
                   COALESCE(rpt.cumulated_qty, 0) AS cumulated_qty,
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
            WHERE sa.iow_group_id = :g AND sa.projectId = :p AND sa.status = 0
            GROUP BY sa.id
            ORDER BY COALESCE(wa.sortorder, 999999) ASC, sa.id ASC
        ", [':g' => $iowGroupId, ':p' => $projectId])->queryAll();
        return json_encode(['result' => $rows, 'error' => 'No']);
    }

    /* Inline edit of an activity's PLANNED Start date from the Gantt
       name-click tooltip. Actual Start/End are system-only (driven by
       progress/delay via GetRelationcorrect()) and are never editable here
       — the client no longer renders them as clickable.
       Blocked once the activity is complete (completed_status = 1): its
       Planned is frozen forever at that point, per the agreed baseline
       model — this is NOT the old "any progress reported" gate, since the
       user may freely revise Planned dates while an activity is still in
       progress. Also blocked when this activity is the dependent side of an
       active relationship — its Planned Start is derived from its
       precedent via RecalculatePlannedDates(), not a direct input; remove
       the relationship first to edit it directly. */
    public function actionUpdateactivitystartdate()
    {
        $id    = (int)($_POST['id'] ?? 0);
        $field = $_POST['field'] ?? '';
        $date  = $_POST['date'] ?? '';
        if (!$id || $field !== 'planned' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return json_encode(['error' => 'Yes', 'errortext' => 'Invalid request.']);
        }

        $db = \Yii::$app->db;

        $hasRelation = $db->createCommand(
            "SELECT COUNT(*) FROM activity_relations WHERE dependent_activity = :id AND status = 0",
            [':id' => $id]
        )->queryScalar();
        if ($hasRelation) {
            return json_encode(['error' => 'Yes', 'errortext' => 'This activity\'s start is set by a relationship. Remove the relationship first to edit its start date.']);
        }

        $sa = $db->createCommand(
            "SELECT projectId, completed_status, old_duration FROM scheduleactivities WHERE id = :id AND status = 0",
            [':id' => $id]
        )->queryOne();
        if (!$sa) return json_encode(['error' => 'Yes', 'errortext' => 'Activity not found.']);
        if ((int)$sa['completed_status'] === 1) {
            return json_encode(['error' => 'Yes', 'errortext' => 'This activity is complete — its Planned dates are locked.']);
        }

        $db->createCommand(
            "UPDATE scheduleactivities SET act_start_date = :s WHERE id = :id",
            [':s' => $date, ':id' => $id]
        )->execute();

        // A direct Planned-date edit is an explicit user action — Planned
        // recalculates for every affected activity. Actual also refreshes
        // immediately for this page view.
        Yii::$app->helper->RecalculatePlannedDates((int)$sa['projectId']);
        Yii::$app->helper->GetRelationcorrect((int)$sa['projectId']);

        return json_encode(['error' => 'No']);
    }

    /* Drag-and-drop reorder of activities within a single WBS/schedule item
       on the Gantt chart (newganttview.php's jQuery UI sortable on the
       list-side rows). Only workgroup_activities_new.sortorder changes —
       no dates are touched. Scoped to one scheduleitem_id, matching the
       existing ORDER BY in actionGanttactivities() (COALESCE(wa.sortorder,
       999999) ASC), which is itself scoped per WBS item, not per IOW group.
       Reordering is purely a display-order/default-sequence change — it
       never touches any activity's dates, and activity_relations references
       activities by id, not by list position, so inserting an unrelated
       activity between two related ones (or reordering around an existing
       relationship) doesn't affect that relationship at all. No
       relationship check is needed here. */
    public function actionReordertaskswithinwbs()
    {
        $iowGroupId = (int)($_POST['iow_group_id'] ?? 0);
        $projectId  = (int)($_POST['project_id'] ?? 0);
        $wanIds = $_POST['wan_ids'] ?? [];
        if (!$iowGroupId || !$projectId || !is_array($wanIds) || !count($wanIds)) {
            return json_encode(['error' => 'Yes', 'errortext' => 'Invalid request.']);
        }
        $wanIds = array_map('intval', $wanIds);

        $db = \Yii::$app->db;

        // Resolve each wan_id to its live scheduleactivities.id within this
        // IOW Group (and project) — reject the whole request if any wan_id
        // doesn't resolve (stale client-side state, e.g. an activity
        // removed from the Gantt after the page was loaded but before the
        // drag).
        foreach ($wanIds as $wanId) {
            $saId = $db->createCommand(
                "SELECT id FROM scheduleactivities WHERE activity_id = :w AND iow_group_id = :g AND projectId = :p AND status = 0 LIMIT 1",
                [':w' => $wanId, ':g' => $iowGroupId, ':p' => $projectId]
            )->queryScalar();
            if (!$saId) {
                return json_encode(['error' => 'Yes', 'errortext' => 'Could not reorder — the activity list has changed. Please refresh and try again.']);
            }
        }

        $sort = 1;
        foreach ($wanIds as $wanId) {
            $db->createCommand(
                "UPDATE workgroup_activities_new SET sortorder = :s WHERE id = :id",
                [':s' => $sort, ':id' => $wanId]
            )->execute();
            $sort++;
        }

        return json_encode(['error' => 'No']);
    }

    /* Reorders whole IOW Groups (and everything under them) within a
       project's Gantt — dragging an IOW Group header onto another one's
       header, same drag mechanism as reordering activities within a
       group. Only iow_group_order.sortorder changes; no activity's own
       sortorder, dates, or iow_group_id are touched — the groups' own
       internal activity order is untouched, the groups themselves just
       move as a block. */
    public function actionReorderiowgroups()
    {
        $projectId   = (int)($_POST['project_id'] ?? 0);
        $iowGroupIds = $_POST['iow_group_ids'] ?? [];
        if (!$projectId || !is_array($iowGroupIds) || !count($iowGroupIds)) {
            return json_encode(['error' => 'Yes', 'errortext' => 'Invalid request.']);
        }
        $iowGroupIds = array_map('intval', $iowGroupIds);

        $db = \Yii::$app->db;

        // Every id must already have an iow_group_order row for this
        // project — reject the whole request otherwise (stale client-side
        // state, e.g. a group removed after the page was loaded).
        foreach ($iowGroupIds as $gid) {
            $exists = $db->createCommand(
                "SELECT 1 FROM iow_group_order WHERE project_id=:p AND iow_group_id=:g LIMIT 1",
                [':p' => $projectId, ':g' => $gid]
            )->queryScalar();
            if (!$exists) {
                return json_encode(['error' => 'Yes', 'errortext' => 'Could not reorder — the group list has changed. Please refresh and try again.']);
            }
        }

        $sort = 1;
        foreach ($iowGroupIds as $gid) {
            $db->createCommand(
                "UPDATE iow_group_order SET sortorder = :s WHERE project_id=:p AND iow_group_id=:g",
                [':s' => $sort, ':p' => $projectId, ':g' => $gid]
            )->execute();
            $sort++;
        }

        return json_encode(['error' => 'No']);
    }

    /* Moves one activity from its current IOW Group to a different one
       (dragged onto a row belonging to another IOW Group on the Gantt
       chart, within the same project). Only scheduleactivities.iow_group_id
       and workgroup_activities_new.iow_group_id are repointed — dates,
       quantities, tasks and resources are untouched. Both the source and
       target groups' activity lists are re-sequenced afterwards (source to
       close the gap, target to place the moved activity at
       drop_after_wan_id's position, or at the end if omitted/not found). */
    public function actionMoveactivitytoiow()
    {
        $wanId          = (int)($_POST['wan_id'] ?? 0);
        $targetGroupId  = (int)($_POST['target_iow_group_id'] ?? 0);
        $dropAfterWanId = (int)($_POST['drop_after_wan_id'] ?? 0);
        if (!$wanId || !$targetGroupId) {
            return json_encode(['error' => 'Yes', 'errortext' => 'Invalid request.']);
        }

        $db = \Yii::$app->db;

        $saRow = $db->createCommand(
            "SELECT id, projectId, iow_group_id FROM scheduleactivities WHERE activity_id = :w AND status = 0 LIMIT 1",
            [':w' => $wanId]
        )->queryOne();
        if (!$saRow) {
            return json_encode(['error' => 'Yes', 'errortext' => 'Could not move — the activity list has changed. Please refresh and try again.']);
        }
        $projectId      = (int)$saRow['projectId'];
        $sourceGroupId  = (int)$saRow['iow_group_id'];
        if ($sourceGroupId === $targetGroupId) {
            return json_encode(['error' => 'Yes', 'errortext' => 'Activity is already in this IOW Group.']);
        }

        // Repoint the activity itself
        $db->createCommand(
            "UPDATE scheduleactivities SET iow_group_id = :g WHERE activity_id = :w AND status = 0",
            [':g' => $targetGroupId, ':w' => $wanId]
        )->execute();
        $db->createCommand(
            "UPDATE workgroup_activities_new SET iow_group_id = :g WHERE id = :id",
            [':g' => $targetGroupId, ':id' => $wanId]
        )->execute();

        // Ensure the target group has a display-order entry in this project
        $targetOrderExists = $db->createCommand(
            "SELECT 1 FROM iow_group_order WHERE project_id=:p AND iow_group_id=:g LIMIT 1",
            [':p' => $projectId, ':g' => $targetGroupId]
        )->queryScalar();
        if (!$targetOrderExists) {
            $nextGroupSort = (int)($db->createCommand(
                "SELECT COALESCE(MAX(sortorder),0)+1 AS s FROM iow_group_order WHERE project_id=:p", [':p' => $projectId]
            )->queryOne()['s'] ?? 1);
            $db->createCommand(
                "INSERT INTO iow_group_order (project_id, iow_group_id, sortorder) VALUES (:p, :g, :s)",
                [':p' => $projectId, ':g' => $targetGroupId, ':s' => $nextGroupSort]
            )->execute();
        }

        // Re-sequence the source group's remaining activities (close the gap)
        $sourceWanIds = $db->createCommand(
            "SELECT wan.id FROM scheduleactivities sa
             JOIN workgroup_activities_new wan ON wan.id = sa.activity_id
             WHERE sa.iow_group_id = :g AND sa.projectId = :p AND sa.status = 0
             ORDER BY wan.sortorder ASC, wan.id ASC",
            [':g' => $sourceGroupId, ':p' => $projectId]
        )->queryColumn();
        $sort = 1;
        foreach ($sourceWanIds as $id) {
            $db->createCommand("UPDATE workgroup_activities_new SET sortorder = :s WHERE id = :id", [':s' => $sort, ':id' => $id])->execute();
            $sort++;
        }

        // Re-sequence the target group's activities, placing the moved
        // one right after drop_after_wan_id (or at the end if not given/found)
        $targetWanIds = $db->createCommand(
            "SELECT wan.id FROM scheduleactivities sa
             JOIN workgroup_activities_new wan ON wan.id = sa.activity_id
             WHERE sa.iow_group_id = :g AND sa.projectId = :p AND sa.status = 0
             ORDER BY wan.sortorder ASC, wan.id ASC",
            [':g' => $targetGroupId, ':p' => $projectId]
        )->queryColumn();
        $targetWanIds = array_map('intval', $targetWanIds);
        $movedIdx = array_search($wanId, $targetWanIds, true);
        if ($movedIdx !== false) array_splice($targetWanIds, $movedIdx, 1);
        $insertAt = count($targetWanIds);
        if ($dropAfterWanId) {
            $afterIdx = array_search($dropAfterWanId, $targetWanIds, true);
            if ($afterIdx !== false) $insertAt = $afterIdx + 1;
        }
        array_splice($targetWanIds, $insertAt, 0, [$wanId]);
        $sort = 1;
        foreach ($targetWanIds as $id) {
            $db->createCommand("UPDATE workgroup_activities_new SET sortorder = :s WHERE id = :id", [':s' => $sort, ':id' => $id])->execute();
            $sort++;
        }

        return json_encode(['error' => 'No']);
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

    // BOQ upload popup (top-bar icon next to Project Estimate) — stores the
    // uploaded file exactly like actionUploadprojectfiles() above, but under
    // its own file_type='boq' so it can be listed/replaced independently of
    // general project documents. Only saves the file for now; what happens
    // when "Create Project Schedule and Estimate" is clicked is separate,
    // pending scope from the user.
    public function actionUploadboq()
    {
        try {
            $projectId = (int)($_POST['project_id'] ?? 0);
            if (!$projectId) {
                return json_encode(['error' => 'Yes', 'errortext' => 'No project selected']);
            }
            if (empty($_FILES['boq_file']['name'])) {
                return json_encode(['error' => 'Yes', 'errortext' => 'No file selected']);
            }

            $origName = $_FILES['boq_file']['name'];
            $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
            $allowed = ['xlsx', 'xls', 'pdf'];
            if (!in_array($ext, $allowed)) {
                return json_encode(['error' => 'Yes', 'errortext' => 'Only Excel (.xlsx, .xls) or PDF files are allowed.']);
            }

            $uploadDir = Yii::getAlias('@webroot') . '/uploads/boq/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $storedName = uniqid('boq_') . '.' . $ext;

            if (!move_uploaded_file($_FILES['boq_file']['tmp_name'], $uploadDir . $storedName)) {
                return json_encode(['error' => 'Yes', 'errortext' => 'Could not save the uploaded file.']);
            }

            \Yii::$app->db->createCommand()->insert('project_files', [
                'project_id'    => $projectId,
                'file_type'     => 'boq',
                'filename'      => $storedName,
                'original_name' => $origName,
                'uploaded_at'   => date('Y-m-d H:i:s'),
                'uploaded_by'   => Yii::$app->user->id,
            ])->execute();

            return json_encode(['error' => 'No', 'id' => (int)Yii::$app->db->lastInsertID, 'original_name' => $origName]);
        } catch (\Exception $e) {
            return json_encode(['error' => 'Yes', 'errortext' => $e->getMessage()]);
        }
    }

    public function actionListboq()
    {
        $projectId = (int)($_POST['project_id'] ?? 0);
        if (!$projectId) return json_encode(['error' => 'Yes', 'errortext' => 'No project selected']);

        $rows = \Yii::$app->db->createCommand(
            "SELECT id, filename, original_name, uploaded_at FROM project_files
             WHERE project_id=:p AND file_type='boq' ORDER BY uploaded_at DESC",
            [':p' => $projectId]
        )->queryAll();

        return json_encode(['error' => 'No', 'rows' => $rows]);
    }

    public function actionDeleteboq()
    {
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) return json_encode(['error' => 'Yes', 'errortext' => 'No file specified']);

        $row = \Yii::$app->db->createCommand(
            "SELECT filename FROM project_files WHERE id=:id AND file_type='boq' LIMIT 1",
            [':id' => $id]
        )->queryOne();
        if (!$row) return json_encode(['error' => 'Yes', 'errortext' => 'File not found']);

        $path = Yii::getAlias('@webroot') . '/uploads/boq/' . $row['filename'];
        if (is_file($path)) @unlink($path);

        \Yii::$app->db->createCommand()->delete('project_files', ['id' => $id, 'file_type' => 'boq'])->execute();

        return json_encode(['error' => 'No']);
    }

}
 

