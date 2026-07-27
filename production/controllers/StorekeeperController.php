<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\ProjuserSelection;

class StorekeeperController extends Controller
{
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionIndex($layout = 'true')
    {
        if ($layout === 'false') {
            $this->layout = false;
        }
        return $this->render('index');
    }

    public function actionIndents()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();

        if (!$projuser) {
            return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);
        }

        $projectid = $projuser->projectid;
        $db = Yii::$app->db;

        $sql = "
            SELECT
                r.Resource_Id                                                  AS resource_id,
                r.Name                                                         AS resource_name,
                r.Unit                                                         AS unit,
                rt.Name                                                        AS resource_type,
                COALESCE(rp.reorder_level, 0)                                 AS reorder_level,
                COALESCE(rp.lot_size, 0)                                      AS reorder_quantity,
                COALESCE(pq.purchased_qty, 0)                                 AS purchased_quantity,
                COALESCE(cq.consumed_qty,  0)                                 AS consumed_quantity,
                COALESCE(pq.purchased_qty, 0) - COALESCE(cq.consumed_qty, 0) AS stock,
                SUM(p.quantity * pe.activity_qty)                              AS estimated_quantity,
                CASE WHEN COALESCE(pq.purchased_qty, 0) >= SUM(p.quantity * pe.activity_qty)
                     THEN 1 ELSE 0 END                                        AS estimate_reached
            FROM pricing_estimate_resources_new p
            JOIN pricing_estimate_new pe ON p.activity_id  = pe.activity_Id
                                        AND pe.project_Id  = p.project_id
            JOIN resources r             ON p.resource_Id  = r.Resource_Id
            JOIN resourcetype rt         ON p.resourcetype_Id = rt.ResourceType_Id
            LEFT JOIN resource_parameters rp ON rp.resource_id = r.Resource_Id
                                             AND rp.project_id = :pid1
            LEFT JOIN (
                SELECT grn.GRN_Item AS resource_id, SUM(grn.GRN_Quantity) AS purchased_qty
                FROM goods_received_note grn
                WHERE grn.GRN_Project = :pid2
                GROUP BY grn.GRN_Item
            ) pq ON r.Resource_Id = pq.resource_id
            LEFT JOIN (
                SELECT per.resource_Id,
                       SUM(sprl.currentqty * per.quantity) AS consumed_qty
                FROM pricing_estimate_resources_new per
                JOIN scheduleactivities sa
                     ON sa.activity_id = per.activity_id
                    AND sa.projectId   = per.project_id
                JOIN schedule_progress_report_log sprl
                     ON sprl.activity_id = sa.id
                WHERE per.project_id     = :pid3
                  AND per.pricing_status = 0
                GROUP BY per.resource_Id
            ) cq ON r.Resource_Id = cq.resource_Id
            WHERE p.project_id       = :pid4
              AND p.pricing_status   = 0
              AND p.resourcetype_Id IN (2, 6, 7)
            GROUP BY r.Resource_Id, rt.Name, r.Name, r.Unit,
                     rp.reorder_level, rp.lot_size
            ORDER BY rt.Name ASC, r.Name ASC
        ";

        $rows = $db->createCommand($sql, [
            ':pid1' => $projectid,
            ':pid2' => $projectid,
            ':pid3' => $projectid,
            ':pid4' => $projectid,
        ])->queryAll();

        return json_encode(['error' => 'No', 'rows' => $rows]);
    }

    public function actionGetresourcetasks()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);
        $projectid = $projuser->projectid;
        $db  = Yii::$app->db;
        $rid = (int)($_POST['resource_id'] ?? 0);
        if (!$rid) return json_encode(['error' => 'Yes', 'errortext' => 'Invalid resource.']);

        $rows = $db->createCommand("
            SELECT
                at.id             AS task_id,
                at.task_name,
                at.task_unit,
                pern.activity_id,
                wan.activity_Name AS activity_name
            FROM pricing_estimate_resources_new pern
            JOIN activity_tasks at        ON FIND_IN_SET(at.id, pern.task_ids) > 0
            JOIN workgroup_activities_new wan ON wan.id = pern.activity_id
            WHERE pern.resource_Id    = :rid
              AND pern.project_id     = :pid
              AND pern.pricing_status = 0
              AND pern.task_ids IS NOT NULL
              AND pern.task_ids != ''
            GROUP BY at.id, at.task_name, at.task_unit, pern.activity_id, wan.activity_Name
            ORDER BY wan.activity_Name ASC, at.sort_order ASC, at.task_name ASC
        ", [':pid' => $projectid, ':rid' => $rid])->queryAll();

        return json_encode(['error' => 'No', 'tasks' => $rows]);
    }

    public function actionCheckindentprogress()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);
        $projectid = $projuser->projectid;
        $db    = Yii::$app->db;
        $items = json_decode($_POST['items'] ?? '[]', true);

        $blocked = [];
        foreach ($items as $item) {
            $rid    = (int)($item['id']      ?? 0);
            $taskId = !empty($item['task_id']) ? (int)$item['task_id'] : null;
            if (!$rid || !$taskId) continue;

            $actRow = $db->createCommand("
                SELECT wan.activity_Name AS activity_name, sa.id AS sa_id
                FROM pricing_estimate_resources_new pern
                JOIN workgroup_activities_new wan ON wan.id = pern.activity_id
                LEFT JOIN scheduleactivities sa   ON sa.activity_id = pern.activity_id
                                                AND sa.projectId        = :pid
                                                AND sa.status           = 0
                                                AND sa.completed_status = 0
                                                AND sa.actual_start_date IS NOT NULL
                                                AND sa.actual_start_date != '0000-00-00'
                                                AND sa.actual_start_date <= CURDATE()
                WHERE pern.project_id = :pid AND pern.resource_Id = :rid
                  AND pern.pricing_status = 0 AND FIND_IN_SET(:tid, pern.task_ids) > 0
                LIMIT 1
            ", [':pid' => $projectid, ':rid' => $rid, ':tid' => $taskId])->queryOne();

            if (!$actRow || !$actRow['sa_id']) continue;

            $reported = (int)$db->createCommand(
                "SELECT COUNT(*) FROM schedule_progress_report_log
                 WHERE activity_id = :said AND report_date = CURDATE()",
                [':said' => $actRow['sa_id']]
            )->queryScalar();

            if (!$reported) {
                $name = $actRow['activity_name'];
                if (!in_array($name, $blocked)) $blocked[] = $name;
            }
        }

        return json_encode(['error' => 'No', 'blocked' => $blocked]);
    }

    public function actionRaiseindent()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);

        $projectid = $projuser->projectid;
        $db = Yii::$app->db;

        $items = json_decode(isset($_POST['items']) ? $_POST['items'] : '[]', true);
        if (empty($items)) return json_encode(['error' => 'Yes', 'errortext' => 'No items selected.']);

        $saved = 0;
        foreach ($items as $item) {
            $rid        = (int)($item['id'] ?? 0);
            $taskId     = !empty($item['task_id'])    ? (int)$item['task_id']    : null;
            $taskName   = !empty($item['task_name'])  ? trim($item['task_name']) : null;
            $activityId = (int)($item['activity_id'] ?? 0);
            $stock      = (float)($item['stock']   ?? 0);
            $reorder    = (float)($item['reorder'] ?? 0);
            if (!$rid) continue;

            // Calculate avg_consumption at indent time
            // Step 1: Get cumulative GRN qty for this resource in this project
            $grnQty = (float)($db->createCommand(
                "SELECT COALESCE(SUM(CAST(g.GRN_Quantity AS DECIMAL(15,4))), 0)
                 FROM goods_received_note g
                 JOIN purchase_order_resources por ON g.GRN_Purchase_Order = por.order_id AND g.GRN_Item = por.resource_id
                 JOIN purchase_orders po ON po.order_id = por.order_id
                 WHERE po.project_id = :pid AND po.delete_status = 0 AND por.delete_status = 0 AND por.resource_id = :rid",
                [':pid' => $projectid, ':rid' => $rid]
            )->queryScalar() ?: 0);

            // Step 2: Get last reported qty for this activity
            $lastQty = 0.0;
            if ($activityId) {
                // Find schedule activity id for this WBS activity
                $schedAct = $db->createCommand(
                    "SELECT id FROM scheduleactivities WHERE activity_id = :aid AND projectId = :pid LIMIT 1",
                    [':aid' => $activityId, ':pid' => $projectid]
                )->queryOne();
                if ($schedAct) {
                    $lastQty = (float)($db->createCommand(
                        "SELECT COALESCE(cumulated_qty, 0) FROM schedule_progress_report
                         WHERE activity_id = :actid ORDER BY updated_at DESC LIMIT 1",
                        [':actid' => $schedAct['id']]
                    )->queryScalar() ?: 0);
                }
            }

            // Step 3: Get planned consumption as fallback (res_qty × estQty / schedQty)
            $plannedConsumption = null;
            if ($activityId) {
                $pernRow = $db->createCommand(
                    "SELECT pern.quantity, pen.activity_qty, sa.quantity AS sched_qty
                     FROM pricing_estimate_resources_new pern
                     JOIN pricing_estimate_new pen ON pen.activity_Id = pern.activity_id AND pen.project_Id = :pid AND pen.pricing_status = 0
                     JOIN scheduleactivities sa ON sa.activity_id = pern.activity_id AND sa.projectId = :pid2
                     WHERE pern.resource_Id = :rid AND pern.activity_id = :aid AND pern.pricing_status = 0
                     LIMIT 1",
                    [':pid' => $projectid, ':pid2' => $projectid, ':rid' => $rid, ':aid' => $activityId]
                )->queryOne();
                if ($pernRow && $pernRow['sched_qty'] > 0) {
                    $plannedConsumption = (float)$pernRow['quantity'] * ((float)$pernRow['activity_qty'] / (float)$pernRow['sched_qty']);
                }
            }

            // Step 4: Calculate avg_consumption
            if ($grnQty > 0 && $lastQty > 0) {
                $avgConsumption = ($grnQty - $stock) / $lastQty;
            } else {
                $avgConsumption = $plannedConsumption; // null if no estimate data
            }

            $exists = $db->createCommand(
                'SELECT id FROM store_indents WHERE project_id = :pid AND resource_id = :rid AND activity_id = :aid',
                [':pid' => $projectid, ':rid' => $rid, ':aid' => $activityId]
            )->queryOne();
            if ($exists) {
                $db->createCommand()->update('store_indents',
                    ['task_id' => $taskId, 'task_name' => $taskName, 'stock_at_site' => $stock, 'avg_consumption' => $avgConsumption, 'reorder_quantity' => $reorder, 'raised_by' => $uid, 'raised_at' => date('Y-m-d H:i:s')],
                    ['id' => $exists['id']]
                )->execute();
                $saved++;
                continue;
            }

            $res = $db->createCommand(
                'SELECT r.Name, r.Unit, rt.Name AS rt_name FROM resources r
                 JOIN resourcetype rt ON r.ResourceType_Id = rt.ResourceType_Id
                 WHERE r.Resource_Id = :rid',
                [':rid' => $rid]
            )->queryOne();
            if (!$res) continue;

            $db->createCommand()->insert('store_indents', [
                'project_id'         => $projectid,
                'activity_id'        => $activityId,
                'resource_id'        => $rid,
                'task_id'            => $taskId,
                'task_name'          => $taskName,
                'pricing_resourceid' => 0,
                'resource_name'      => $res['Name'],
                'resource_type'      => $res['rt_name'],
                'unit'               => $res['Unit'],
                'stock_at_site'      => $stock,
                'avg_consumption'    => $avgConsumption,
                'reorder_quantity'   => $reorder,
                'raised_by'          => $uid,
                'raised_at'          => date('Y-m-d H:i:s'),
            ])->execute();
            $saved++;
        }

        return json_encode(['error' => 'No', 'saved' => $saved]);
    }

    public function actionIssuedmbooks()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();

        if (!$projuser) {
            return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);
        }

        $projectid = $projuser->projectid;
        $db = Yii::$app->db;

        $rows = $db->createCommand(
            'SELECT wmb.id, wmb.mb_number, wmb.mb_date, wmb.wo_number, wmb.created_at,
                    wmb.delete_status,
                    v.Name AS vendor_name,
                    JSON_UNQUOTE(JSON_EXTRACT(wo.WO_Subject, \'$[0].activity_name\')) AS activity_name
             FROM wo_measurement_book wmb
             LEFT JOIN work_order wo ON wo.WO_Number = wmb.wo_number AND wo.Project_Id = wmb.project_id
             LEFT JOIN vendors v ON v.Vendor_Id = wo.WO_Vendor
             WHERE wmb.project_id = :pid AND wmb.sent_status = 1
             ORDER BY wmb.created_at DESC',
            [':pid' => $projectid]
        )->queryAll();

        return json_encode(['error' => 'No', 'rows' => $rows]);
    }

    public function actionViewmb()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) return ['error' => 'Yes', 'errortext' => 'Invalid ID.'];

        $db  = Yii::$app->db;
        $row = $db->createCommand(
            'SELECT wmb.mb_number, wmb.mb_date, wmb.wo_number, wmb.entries, wmb.project_id,
                    wo.WO_Subject,
                    v.Name AS vendor_name
             FROM wo_measurement_book wmb
             LEFT JOIN work_order wo ON wo.WO_Number = wmb.wo_number AND wo.Project_Id = wmb.project_id
             LEFT JOIN vendors v ON v.Vendor_Id = wo.WO_Vendor
             WHERE wmb.id = :id',
            [':id' => $id]
        )->queryOne();

        if (!$row) return ['error' => 'Yes', 'errortext' => 'Record not found.'];

        // Build WO qty map from WO_Subject
        $woActivities = json_decode($row['WO_Subject'] ?? '[]', true) ?: [];
        $woQtyMap = [];
        foreach ($woActivities as $a) {
            $woQtyMap[$a['activity_id'] ?? ''] = (float)($a['qty'] ?? 0);
        }

        // Build cumulative qty map across all sent M.Books for this WO
        $allMbs = $db->createCommand(
            'SELECT entries FROM wo_measurement_book WHERE wo_number = :num AND project_id = :pid AND sent_status = 1',
            [':num' => $row['wo_number'], ':pid' => $row['project_id']]
        )->queryAll();
        $cumulativeMap = [];
        $taskCumMap    = [];
        foreach ($allMbs as $mb) {
            foreach (json_decode($mb['entries'] ?? '[]', true) ?: [] as $e) {
                $aid = $e['activity_id'] ?? '';
                $cumulativeMap[$aid] = ($cumulativeMap[$aid] ?? 0) + (float)($e['qty'] ?? 0);
                foreach ($e['tasks'] ?? [] as $t) {
                    $tid = $t['task_id'] ?? '';
                    $wd  = (float)($t['work_done'] ?? 0);
                    $rt  = (float)($t['rate']      ?? 0);
                    $key = $aid . '_' . $tid;
                    $taskCumMap[$key]['work_done'] = ($taskCumMap[$key]['work_done'] ?? 0) + $wd;
                    $taskCumMap[$key]['amount']    = ($taskCumMap[$key]['amount']    ?? 0) + ($wd * $rt);
                }
            }
        }

        // Enrich entries with wo_qty, cumulative_qty, and per-task cumulative totals
        $entries = json_decode($row['entries'] ?? '[]', true) ?: [];
        foreach ($entries as &$entry) {
            $aid = $entry['activity_id'] ?? '';
            $entry['wo_qty']         = $woQtyMap[$aid]      ?? 0;
            $entry['cumulative_qty'] = $cumulativeMap[$aid] ?? 0;
            foreach ($entry['tasks'] ?? [] as &$task) {
                $key = $aid . '_' . ($task['task_id'] ?? '');
                $task['cum_work_done'] = round($taskCumMap[$key]['work_done'] ?? 0, 2);
                $task['cum_amount']    = round($taskCumMap[$key]['amount']    ?? 0, 2);
            }
            unset($task);
        }

        return [
            'error'       => 'No',
            'mb_number'   => $row['mb_number'],
            'mb_date'     => $row['mb_date'],
            'wo_number'   => $row['wo_number'],
            'vendor_name' => $row['vendor_name'] ?: '',
            'entries'     => $entries,
        ];
    }

    public function actionCancelmb()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) return ['error' => 'Yes', 'errortext' => 'Invalid ID.'];
        Yii::$app->db->createCommand()->update('wo_measurement_book',
            ['delete_status' => 1, 'updated_at' => date('Y-m-d H:i:s')],
            ['id' => $id]
        )->execute();
        return ['error' => 'No'];
    }

    public function actionRecovermb()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) return ['error' => 'Yes', 'errortext' => 'Invalid ID.'];
        Yii::$app->db->createCommand()->update('wo_measurement_book',
            ['delete_status' => 0, 'updated_at' => date('Y-m-d H:i:s')],
            ['id' => $id]
        )->execute();
        return ['error' => 'No'];
    }

    public function actionIssuedindents()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();

        if (!$projuser) {
            return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);
        }

        $projectid = $projuser->projectid;
        $db = Yii::$app->db;

        $rows = $db->createCommand(
            'SELECT si.id, si.resource_name, si.resource_type, si.unit,
                    si.task_name,
                    si.stock_at_site, si.reorder_quantity, si.raised_at,
                    u.username AS raised_by
             FROM store_indents si
             LEFT JOIN user u ON u.id = si.raised_by
             WHERE si.project_id = :pid
             ORDER BY si.resource_type ASC, si.resource_name ASC',
            [':pid' => $projectid]
        )->queryAll();

        return json_encode(['error' => 'No', 'rows' => $rows]);
    }

    public function actionIssued()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();

        if (!$projuser) {
            return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);
        }

        $projectid = $projuser->projectid;
        $db = Yii::$app->db;

        $filterSent = !empty($_POST['filter_sent']);
        $grnFilter  = $filterSent
            ? "AND EXISTS (
                      SELECT 1 FROM goods_received_note grn
                      WHERE grn.GRN_Purchase_Order = po.order_id AND grn.GRN_Project = :pid2
                  )"
            : "";

        $sql = "SELECT po.order_id, po.ordernumber, po.orderdate, po.delete_status AS cancelled,
                       v.Name AS vendor_name,
                       (SELECT por2.resource_name
                        FROM purchase_order_resources por2
                        WHERE por2.order_id = po.order_id AND por2.delete_status = 0
                        ORDER BY por2.order_res_id ASC LIMIT 1) AS first_item,
                       (SELECT rt.Name
                        FROM purchase_order_resources por2
                        JOIN resources r ON r.Resource_Id = por2.resource_id
                        JOIN resourcetype rt ON rt.ResourceType_Id = r.ResourceType_Id
                        WHERE por2.order_id = po.order_id AND por2.delete_status = 0
                        ORDER BY por2.order_res_id ASC LIMIT 1) AS resource_type_name,
                       (SELECT CASE WHEN EXISTS (
                            SELECT 1 FROM goods_received_note g
                            WHERE g.GRN_Purchase_Order = po.order_id
                              AND g.GRN_Project = po.project_id
                        ) THEN 1 ELSE 0 END) AS grn_fully_received
                FROM purchase_orders po
                JOIN vendors v ON v.Vendor_Id = po.vendor_id
                WHERE po.project_id = :pid
                  {$grnFilter}
                ORDER BY resource_type_name ASC, po.orderdate DESC, po.order_id DESC";

        $params = [':pid' => $projectid];
        if ($filterSent) $params[':pid2'] = $projectid;
        $rows = $db->createCommand($sql, $params)->queryAll();
        return json_encode(['error' => 'No', 'rows' => $rows]);
    }

    public function actionIssuedwo()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();

        if (!$projuser) {
            return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);
        }

        $projectid = $projuser->projectid;
        $db = Yii::$app->db;

        $filterSent = !empty($_POST['filter_sent']);
        $mbFilter   = $filterSent
            ? "AND EXISTS (
                      SELECT 1 FROM wo_measurement_book mb
                      WHERE mb.wo_number = wo.WO_Number AND mb.project_id = :pid2
                  )"
            : "";

        $sql = "SELECT wo.WO_Id, wo.WO_Number, wo.Date_requested,
                       wo.WO_Status AS cancelled,
                       v.Name AS vendor_name,
                       rt.Name AS resource_type_name,
                       JSON_UNQUOTE(JSON_EXTRACT(wo.WO_Subject, '$[0].activity_name')) AS activity_name
                FROM work_order wo
                JOIN vendors v ON v.Vendor_Id = wo.WO_Vendor
                LEFT JOIN resourcetype rt ON rt.ResourceType_Id = v.resource_type_id
                WHERE wo.Project_Id = :pid AND wo.WO_Status IN (0, 1)
                  {$mbFilter}
                ORDER BY rt.Name ASC, wo.Date_requested DESC, wo.WO_Id DESC";

        $params = [':pid' => $projectid];
        if ($filterSent) $params[':pid2'] = $projectid;
        $rows = $db->createCommand($sql, $params)->queryAll();
        return json_encode(['error' => 'No', 'rows' => $rows]);
    }

    public function actionWoactivities()
    {
        try {
            $uid      = Yii::$app->user->id;
            $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
            if (!$projuser) return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);

            $db        = Yii::$app->db;
            $wo_number = trim($_POST['wo_number'] ?? '');
            $projectid = $projuser->projectid;

            if (!$wo_number) return json_encode(['error' => 'Yes', 'errortext' => 'No WO number provided.']);

            $wo = $db->createCommand(
                'SELECT wo.WO_Subject, v.Name AS vendor_name
                 FROM work_order wo
                 LEFT JOIN vendors v ON v.Vendor_Id = wo.WO_Vendor
                 WHERE wo.WO_Number = :num AND wo.Project_Id = :pid',
                [':num' => $wo_number, ':pid' => $projectid]
            )->queryOne();

            if (!$wo) return json_encode(['error' => 'Yes', 'errortext' => 'Work order not found.']);

            $activities = json_decode($wo['WO_Subject'] ?? '[]', true) ?: [];

            // Check for an unsent draft for this WO
            $draft = $db->createCommand(
                'SELECT entries, mb_number, mb_date FROM wo_measurement_book
                 WHERE wo_number = :num AND project_id = :pid AND sent_status = 0
                 ORDER BY id DESC LIMIT 1',
                [':num' => $wo_number, ':pid' => $projectid]
            )->queryOne();

            // Cumulative qty only from sent records
            $sentMbs = $db->createCommand(
                'SELECT entries FROM wo_measurement_book
                 WHERE wo_number = :num AND project_id = :pid AND sent_status = 1',
                [':num' => $wo_number, ':pid' => $projectid]
            )->queryAll();

            $cumulativeMap = [];
            $taskCumMap    = []; // keyed aid_taskid
            foreach ($sentMbs as $mbRow) {
                foreach (json_decode($mbRow['entries'] ?? '[]', true) ?: [] as $e) {
                    $aid = $e['activity_id'] ?? '';
                    $cumulativeMap[$aid] = ($cumulativeMap[$aid] ?? 0) + (float)($e['qty'] ?? 0);
                    foreach ($e['tasks'] ?? [] as $t) {
                        $tid = $t['task_id'] ?? '';
                        $wd  = (float)($t['work_done'] ?? 0);
                        $rt  = (float)($t['rate']      ?? 0);
                        $key = $aid . '_' . $tid;
                        $taskCumMap[$key]['work_done'] = ($taskCumMap[$key]['work_done'] ?? 0) + $wd;
                        $taskCumMap[$key]['amount']    = ($taskCumMap[$key]['amount']    ?? 0) + ($wd * $rt);
                    }
                }
            }

            // Pre-fill from draft if it exists
            $savedMap = [];
            if ($draft) {
                foreach (json_decode($draft['entries'] ?? '[]', true) ?: [] as $s) {
                    $savedMap[$s['activity_id'] ?? ''] = $s;
                }
            }

            foreach ($activities as &$act) {
                $aid    = $act['activity_id'] ?? '';
                $billed = $cumulativeMap[$aid] ?? 0;
                $act['cumulative_qty'] = $billed;
                // Pull unit and last reported qty from schedule
                $saRow = $db->createCommand(
                    "SELECT sa.unit, sa.quantity,
                            COALESCE(MAX(spr.cumulated_qty), 0) AS last_reported_qty
                     FROM scheduleactivities sa
                     LEFT JOIN schedule_progress_report spr ON spr.activity_id = sa.id
                     WHERE sa.activity_id = :wid AND sa.projectId = :pid AND sa.status = 0",
                    [':wid' => (int)$aid, ':pid' => $projectid]
                )->queryOne();
                $act['unit']              = $saRow['unit']              ?? ($act['unit'] ?? '');
                $act['schedule_qty']      = (float)($saRow['quantity']  ?? 0);
                $act['last_reported_qty'] = (float)($saRow['last_reported_qty'] ?? 0);
                $act['mb_unit'] = $act['unit'];
                $act['mb_qty']  = 0;
                // Attach task-level cumulative totals and per-unit qty from wo_task_rates
                if (isset($act['tasks']) && is_array($act['tasks'])) {
                    foreach ($act['tasks'] as &$task) {
                        $key = $aid . '_' . ($task['task_id'] ?? '');
                        $task['cum_work_done'] = round($taskCumMap[$key]['work_done'] ?? 0, 2);
                        $task['cum_amount']    = round($taskCumMap[$key]['amount']    ?? 0, 2);
                        // Per-unit qty from wo_task_rates (not the WO_Subject total)
                        $tid = (int)($task['task_id'] ?? 0);
                        $uqty = $db->createCommand(
                            "SELECT task_qty FROM wo_task_rates WHERE activity_id=:aid AND task_id=:tid AND project_id=:pid",
                            [':aid' => (int)$aid, ':tid' => $tid, ':pid' => $projectid]
                        )->queryScalar();
                        $task['unit_qty'] = (float)($uqty !== false ? $uqty : ($task['task_qty'] ?? 0));
                    }
                    unset($task);
                }
                if (isset($savedMap[$aid])) {
                    $act['mb_unit'] = $savedMap[$aid]['unit'] ?? ($act['unit'] ?? '');
                    $act['mb_qty']  = $savedMap[$aid]['qty']  ?? $act['mb_qty'];
                }
            }

            return json_encode([
                'error'       => 'No',
                'activities'  => $activities,
                'vendor_name' => $wo['vendor_name'] ?? '',
                'has_draft'   => $draft ? true : false,
                'mb_number'   => $draft ? ($draft['mb_number'] ?? '') : '',
                'mb_date'     => $draft ? ($draft['mb_date']   ?? '') : '',
            ]);
        } catch (\Exception $e) {
            return json_encode(['error' => 'Yes', 'errortext' => 'Exception: ' . $e->getMessage()]);
        }
    }

    public function actionMbnext()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);

        $projectid = $projuser->projectid;
        $db        = Yii::$app->db;
        $yearMonth = date('Ym');
        $prefix    = 'MB-' . $projectid . '-' . $yearMonth . '-';

        $count = (int)$db->createCommand(
            "SELECT COUNT(*) FROM wo_measurement_book
             WHERE project_id = :pid AND mb_number LIKE :pfx AND sent_status = 1",
            [':pid' => $projectid, ':pfx' => $prefix . '%']
        )->queryScalar();

        return json_encode(['error' => 'No', 'mb_number' => $prefix . str_pad($count + 1, 3, '0', STR_PAD_LEFT)]);
    }

    public function actionSavemb()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);

        $db        = Yii::$app->db;
        $wo_number = trim($_POST['wo_number'] ?? '');
        $entries   = $_POST['entries'] ?? '[]';
        $mb_number = trim($_POST['mb_number'] ?? '');
        $rawDate   = trim($_POST['mb_date'] ?? '');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $rawDate)) {
            return json_encode(['error' => 'Yes', 'errortext' => 'Please enter the Date.']);
        }
        $mb_date   = $rawDate;
        $projectid = $projuser->projectid;

        if (!$wo_number) return json_encode(['error' => 'Yes', 'errortext' => 'Invalid data.']);

        // Build WO qty and task_qty maps from WO_Subject
        $wo = $db->createCommand(
            'SELECT WO_Subject FROM work_order WHERE WO_Number = :num AND Project_Id = :pid',
            [':num' => $wo_number, ':pid' => $projectid]
        )->queryOne();
        $woActivities = json_decode($wo['WO_Subject'] ?? '[]', true) ?: [];
        $woQtyMap   = [];
        $taskQtyMap = [];
        foreach ($woActivities as $act) {
            $aid = $act['activity_id'] ?? '';
            $woQtyMap[$aid] = (float)($act['qty'] ?? 0);
            $taskQtyMap[$aid] = [];
            foreach ($act['tasks'] ?? [] as $t) {
                $taskQtyMap[$aid][$t['task_id']] = (float)($t['task_qty'] ?? 0);
            }
        }

        // Cumulative quantities from sent MBs only (drafts excluded)
        $existingMbs = $db->createCommand(
            'SELECT entries FROM wo_measurement_book WHERE wo_number = :num AND project_id = :pid AND sent_status = 1',
            [':num' => $wo_number, ':pid' => $projectid]
        )->queryAll();
        $cumulativeMap = [];
        foreach ($existingMbs as $row) {
            foreach (json_decode($row['entries'] ?? '[]', true) ?: [] as $e) {
                $aid = $e['activity_id'] ?? '';
                $cumulativeMap[$aid] = ($cumulativeMap[$aid] ?? 0) + (float)($e['qty'] ?? 0);
            }
        }

        $newEntries = json_decode($entries, true) ?: [];

        // Build last_reported_qty map from schedule progress reports
        $lastReportedMap = [];
        foreach ($woActivities as $act) {
            $wbsId = (int)($act['activity_id'] ?? 0);
            $lr = $db->createCommand(
                "SELECT COALESCE(MAX(spr.cumulated_qty), 0)
                 FROM scheduleactivities sa
                 LEFT JOIN schedule_progress_report spr ON spr.activity_id = sa.id
                 WHERE sa.activity_id = :wid AND sa.projectId = :pid AND sa.status = 0",
                [':wid' => $wbsId, ':pid' => $projectid]
            )->queryScalar();
            $lastReportedMap[(string)$wbsId] = (float)($lr ?? 0);
        }

        // Validate new entries against last reported qty (not WO qty)
        foreach ($newEntries as $entry) {
            $aid     = $entry['activity_id'] ?? '';
            $newQty  = (float)($entry['qty'] ?? 0);
            $prevQty = $cumulativeMap[$aid] ?? 0;
            $lastRep = $lastReportedMap[$aid] ?? 0;

            if ($lastRep > 0 && ($prevQty + $newQty) > $lastRep + 0.001) {
                $remaining = max(0, $lastRep - $prevQty);
                return json_encode(['error' => 'Yes',
                    'errortext' => 'Activity "' . ($entry['activity_name'] ?? $aid) . '": billed quantity would exceed last reported progress (' . $lastRep . '). Only ' . round($remaining, 3) . ' remaining.']);
            }

            foreach ($entry['tasks'] ?? [] as $task) {
                $tid      = $task['task_id'] ?? 0;
                $workDone = (float)($task['work_done'] ?? 0);
                $tqpu     = $taskQtyMap[$aid][$tid] ?? 0;
                $maxWd    = $newQty * $tqpu;
                if ($tqpu > 0 && $workDone > $maxWd) {
                    return json_encode(['error' => 'Yes',
                        'errortext' => 'Task "' . ($task['task_name'] ?? $tid) . '": work done (' . $workDone . ') exceeds maximum (' . round($maxWd, 4) . ') for this MB quantity.']);
                }
            }
        }

        $now = date('Y-m-d H:i:s');

        // Promote draft to sent if one exists, otherwise insert new
        $draftId = (int)$db->createCommand(
            'SELECT id FROM wo_measurement_book WHERE wo_number = :num AND project_id = :pid AND sent_status = 0 LIMIT 1',
            [':num' => $wo_number, ':pid' => $projectid]
        )->queryScalar();

        if ($draftId) {
            $db->createCommand()->update('wo_measurement_book', [
                'mb_number'   => $mb_number,
                'mb_date'     => $mb_date,
                'entries'     => $entries,
                'sent_status' => 1,
                'updated_at'  => $now,
            ], ['id' => $draftId])->execute();
        } else {
            $db->createCommand()->insert('wo_measurement_book', [
                'mb_number'   => $mb_number,
                'mb_date'     => $mb_date,
                'wo_number'   => $wo_number,
                'project_id'  => $projectid,
                'entries'     => $entries,
                'sent_status' => 1,
                'created_by'  => $uid,
                'created_at'  => $now,
                'updated_at'  => $now,
            ])->execute();
        }

        return json_encode(['error' => 'No']);
    }

    public function actionSavedraft()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);

        $db        = Yii::$app->db;
        $wo_number = trim($_POST['wo_number'] ?? '');
        $entries   = $_POST['entries'] ?? '[]';
        $mb_number = trim($_POST['mb_number'] ?? '');
        $rawDate   = trim($_POST['mb_date'] ?? '');
        $mb_date   = preg_match('/^\d{4}-\d{2}-\d{2}$/', $rawDate) ? $rawDate : null;
        $projectid = $projuser->projectid;

        if (!$wo_number) return json_encode(['error' => 'Yes', 'errortext' => 'Invalid data.']);

        $now     = date('Y-m-d H:i:s');
        $draftId = (int)$db->createCommand(
            'SELECT id FROM wo_measurement_book WHERE wo_number = :num AND project_id = :pid AND sent_status = 0 LIMIT 1',
            [':num' => $wo_number, ':pid' => $projectid]
        )->queryScalar();

        if ($draftId) {
            $db->createCommand()->update('wo_measurement_book', [
                'mb_number'  => $mb_number,
                'mb_date'    => $mb_date,
                'entries'    => $entries,
                'updated_at' => $now,
            ], ['id' => $draftId])->execute();
        } else {
            $db->createCommand()->insert('wo_measurement_book', [
                'mb_number'   => $mb_number,
                'mb_date'     => $mb_date,
                'wo_number'   => $wo_number,
                'project_id'  => $projectid,
                'entries'     => $entries,
                'sent_status' => 0,
                'created_by'  => $uid,
                'created_at'  => $now,
                'updated_at'  => $now,
            ])->execute();
        }

        return json_encode(['error' => 'No']);
    }

    public function actionGrnnext()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);

        $projectid = $projuser->projectid;
        $db        = Yii::$app->db;
        $yearMonth = date('Ym');
        $prefix    = 'GRN-' . $projectid . '-' . $yearMonth . '-';

        $count = (int)$db->createCommand(
            "SELECT COUNT(DISTINCT grn_number) FROM goods_received_note
             WHERE GRN_Project = :pid AND grn_number LIKE :pfx",
            [':pid' => $projectid, ':pfx' => $prefix . '%']
        )->queryScalar();

        $grn_number = $prefix . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
        return json_encode(['error' => 'No', 'grn_number' => $grn_number]);
    }

    public function actionGrnitems()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();

        if (!$projuser) {
            return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);
        }

        $projectid = $projuser->projectid;
        $db = Yii::$app->db;
        $order_id = (int)($_POST['order_id'] ?? 0);

        $rows = $db->createCommand(
            'SELECT por.order_res_id, por.resource_id, por.resource_name, por.unit,
                    por.qnty AS ordered_qty, por.rate AS po_rate,
                    COALESCE((
                        SELECT SUM(g2.GRN_Quantity) FROM goods_received_note g2
                        WHERE g2.GRN_Item = por.resource_id
                          AND g2.GRN_Project = po.project_id
                    ), 0) AS total_received,
                    COALESCE((
                        SELECT SUM(g2.GRN_Quantity * COALESCE(g2.GRN_Rate, 0)) FROM goods_received_note g2
                        WHERE g2.GRN_Item = por.resource_id
                          AND g2.GRN_Project = po.project_id
                    ), 0) AS total_amount,
                    g.GRN_Quantity AS grn_qty, g.GRN_Rate AS grn_rate, g.grn_number
             FROM purchase_order_resources por
             JOIN purchase_orders po ON po.order_id = por.order_id
             LEFT JOIN goods_received_note g ON g.GRN_Id = (
                 SELECT MAX(g2.GRN_Id) FROM goods_received_note g2
                 WHERE g2.GRN_Purchase_Order = por.order_id
                   AND g2.GRN_Item = por.resource_id
                   AND g2.GRN_Project = po.project_id
             )
             WHERE por.order_id = :oid AND por.delete_status = 0 AND po.project_id = :pid
             ORDER BY por.order_res_id ASC',
            [':oid' => $order_id, ':pid' => $projectid]
        )->queryAll();

        return json_encode(['error' => 'No', 'rows' => $rows]);
    }

    public function actionSavegrn()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();

        if (!$projuser) {
            return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);
        }

        $projectid = $projuser->projectid;
        $db = Yii::$app->db;

        $order_id   = (int)($_POST['order_id'] ?? 0);
        $items      = json_decode(isset($_POST['items']) ? $_POST['items'] : '[]', true);
        $remarks    = trim($_POST['remarks'] ?? '');
        $grn_number = trim($_POST['grn_number'] ?? '');

        // One GRN per order: any receipt (even partial) closes the order;
        // further quantities must come through a new purchase order.
        $grnExists = (int)$db->createCommand(
            'SELECT COUNT(*) FROM goods_received_note WHERE GRN_Purchase_Order = :oid AND GRN_Project = :pid',
            [':oid' => $order_id, ':pid' => $projectid]
        )->queryScalar();
        if ($grnExists) {
            return json_encode(['error' => 'Yes',
                'errortext' => 'A GRN has already been issued against this order. Receive further quantities via a new purchase order.']);
        }

        if (!$order_id || empty($items)) {
            return json_encode(['error' => 'Yes', 'errortext' => 'Invalid data.']);
        }

        $po = $db->createCommand(
            'SELECT vendor_id FROM purchase_orders WHERE order_id = :oid AND project_id = :pid',
            [':oid' => $order_id, ':pid' => $projectid]
        )->queryOne();

        if (!$po) {
            return json_encode(['error' => 'Yes', 'errortext' => 'PO not found.']);
        }

        $rawDate = trim($_POST['date_of_receipt'] ?? '');
        $grnDate = preg_match('/^\d{4}-\d{2}-\d{2}$/', $rawDate) ? $rawDate : date('Y-m-d');

        // Server-side: validate cumulative qty per item does not exceed ordered qty
        foreach ($items as $item) {
            $rid = (int)($item['resource_id'] ?? 0);
            $qty = (float)($item['qty'] ?? 0);
            if (!$rid || $qty <= 0) continue;

            $row = $db->createCommand(
                'SELECT por.qnty AS ordered_qty,
                        COALESCE((
                            SELECT SUM(g.GRN_Quantity) FROM goods_received_note g
                            WHERE g.GRN_Purchase_Order = :oid AND g.GRN_Item = :rid AND g.GRN_Project = :pid2
                        ), 0) AS total_received
                 FROM purchase_order_resources por
                 WHERE por.order_id = :oid2 AND por.resource_id = :rid2 AND por.delete_status = 0',
                [':oid' => $order_id, ':rid' => $rid, ':pid2' => $projectid,
                 ':oid2' => $order_id, ':rid2' => $rid]
            )->queryOne();

            if ($row && ($row['total_received'] + $qty) > $row['ordered_qty']) {
                $alreadyReceived = (float)$row['total_received'];
                $remaining = max(0, (float)$row['ordered_qty'] - $alreadyReceived);
                return json_encode([
                    'error'     => 'Yes',
                    'errortext' => "Quantity exceeds PO order. Only {$remaining} unit(s) remaining for one or more items.",
                ]);
            }
        }

        $first = true;
        foreach ($items as $item) {
            $rid  = (int)($item['resource_id'] ?? 0);
            $qty  = (float)($item['qty'] ?? 0);
            $rate = (float)($item['rate'] ?? 0);
            if (!$rid || $qty <= 0) continue;
            $db->createCommand()->insert('goods_received_note', [
                'grn_number'         => $grn_number,
                'GRN_Date'           => $grnDate,
                'GRN_Vehicle'        => '',
                'GRN_Project'        => $projectid,
                'GRN_Vendor'         => $po['vendor_id'],
                'GRN_Purchase_Order' => $order_id,
                'GRN_Item'           => $rid,
                'GRN_Quantity'       => $qty,
                'GRN_Rate'           => $rate,
                'GRN_User'           => $uid,
                'GRN_Status'         => 0,
                'remarks'            => $first ? $remarks : '',
            ])->execute();
            $first = false;
        }

        return json_encode(['error' => 'No']);
    }

    public function actionIssuedgrns()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);

        $projectid = $projuser->projectid;
        $db = Yii::$app->db;

        $rows = $db->createCommand(
            'SELECT g.GRN_Id, g.grn_number, g.GRN_Date, g.delete_status, g.remarks,
                    po.ordernumber,
                    v.Name AS vendor_name
             FROM goods_received_note g
             LEFT JOIN purchase_orders po ON po.order_id = g.GRN_Purchase_Order
             LEFT JOIN vendors v ON v.Vendor_Id = g.GRN_Vendor
             WHERE g.GRN_Project = :pid
               AND g.GRN_Id = (
                   SELECT MIN(g2.GRN_Id) FROM goods_received_note g2
                   WHERE g2.grn_number = g.grn_number
               )
             ORDER BY g.GRN_Id DESC',
            [':pid' => $projectid]
        )->queryAll();

        return json_encode(['error' => 'No', 'rows' => $rows]);
    }

    public function actionViewgrn()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $grn_number = trim($_POST['grn_number'] ?? '');
        if (!$grn_number) return ['error' => 'Yes', 'errortext' => 'Invalid GRN number.'];

        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return ['error' => 'Yes', 'errortext' => 'No project selected.'];

        $projectid = $projuser->projectid;
        $db = Yii::$app->db;

        $header = $db->createCommand(
            'SELECT g.grn_number, g.GRN_Date, g.remarks,
                    po.ordernumber,
                    v.Name AS vendor_name
             FROM goods_received_note g
             LEFT JOIN purchase_orders po ON po.order_id = g.GRN_Purchase_Order
             LEFT JOIN vendors v ON v.Vendor_Id = g.GRN_Vendor
             WHERE g.grn_number = :num AND g.GRN_Project = :pid
             ORDER BY g.GRN_Id ASC LIMIT 1',
            [':num' => $grn_number, ':pid' => $projectid]
        )->queryOne();

        if (!$header) return ['error' => 'Yes', 'errortext' => 'GRN not found.'];

        $items = $db->createCommand(
            'SELECT g.GRN_Item AS resource_id, g.GRN_Quantity AS qty, g.GRN_Rate AS rate,
                    COALESCE(por.resource_name, g.GRN_Item) AS resource_name,
                    por.unit, por.qnty AS ordered_qty, por.rate AS po_rate,
                    COALESCE((
                        SELECT SUM(g2.GRN_Quantity) FROM goods_received_note g2
                        WHERE g2.GRN_Item = g.GRN_Item AND g2.GRN_Project = :pid
                    ), 0) AS total_received,
                    COALESCE((
                        SELECT SUM(g2.GRN_Quantity * COALESCE(g2.GRN_Rate, 0)) FROM goods_received_note g2
                        WHERE g2.GRN_Item = g.GRN_Item AND g2.GRN_Project = :pid
                    ), 0) AS total_amount
             FROM goods_received_note g
             LEFT JOIN purchase_order_resources por
                    ON por.resource_id = g.GRN_Item AND por.order_id = g.GRN_Purchase_Order AND por.delete_status = 0
             WHERE g.grn_number = :num AND g.GRN_Project = :pid
             ORDER BY g.GRN_Id ASC',
            [':num' => $grn_number, ':pid' => $projectid]
        )->queryAll();

        return [
            'error'       => 'No',
            'grn_number'  => $header['grn_number'],
            'grn_date'    => $header['GRN_Date'],
            'ordernumber' => $header['ordernumber'] ?: '',
            'vendor_name' => $header['vendor_name'] ?: '',
            'remarks'     => $header['remarks'] ?: '',
            'items'       => $items,
        ];
    }

    // DEACTIVATED: an issued GRN is final — it cannot be cancelled or recovered.
    // Receipt against an order is one-time; shortfalls require a new purchase order.
    public function actionCancelgrn()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return ['error' => 'Yes', 'errortext' => 'An issued GRN cannot be cancelled.'];
    }

    public function actionRecovergrn()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return ['error' => 'Yes', 'errortext' => 'An issued GRN cannot be cancelled or recovered.'];
    }
}
