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

    public function actionIndex()
    {
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
                                        AND r.Status       = 0
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

    public function actionRaiseindent()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();

        if (!$projuser) {
            return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);
        }

        $projectid = $projuser->projectid;
        $db = Yii::$app->db;

        $items = json_decode(isset($_POST['items']) ? $_POST['items'] : '[]', true);
        if (empty($items)) {
            return json_encode(['error' => 'Yes', 'errortext' => 'No items selected.']);
        }

        $saved   = 0;
        $blocked = [];
        foreach ($items as $item) {
            $rid     = (int)($item['id']      ?? 0);
            $stock   = (float)($item['stock']   ?? 0);
            $reorder = (float)($item['reorder'] ?? 0);

            if (!$rid) continue;

            // Gate: every ongoing activity using this resource must have a progress report today,
            // since stock at site is derived from reported consumption
            $unreported = $db->createCommand(
                "SELECT DISTINCT sa.name
                 FROM pricing_estimate_resources_new per
                 JOIN scheduleactivities sa ON sa.activity_id = per.activity_id
                                           AND sa.projectId   = per.project_id
                                           AND sa.status      = 0
                 WHERE per.project_id = :pid AND per.resource_Id = :rid AND per.pricing_status = 0
                   AND sa.completed_status = 0
                   AND sa.actual_start_date IS NOT NULL
                   AND sa.actual_start_date != '0000-00-00'
                   AND sa.actual_start_date <= CURDATE()
                   AND NOT EXISTS (SELECT 1 FROM schedule_progress_report_log l
                                   WHERE l.activity_id = sa.id AND l.report_date = CURDATE())",
                [':pid' => $projectid, ':rid' => $rid]
            )->queryColumn();
            if (!empty($unreported)) {
                $resName = $db->createCommand(
                    'SELECT Name FROM resources WHERE Resource_Id = :rid', [':rid' => $rid]
                )->queryScalar();
                $blocked[] = ['resource' => $resName ?: ('#' . $rid), 'activities' => $unreported];
                continue;
            }

            // Skip if GRN received quantity has reached or exceeded estimated quantity
            $estimateCheck = $db->createCommand(
                "SELECT
                     SUM(p.quantity * pe.activity_qty) AS estimated_qty,
                     COALESCE((SELECT SUM(grn.GRN_Quantity) FROM goods_received_note grn
                               WHERE grn.GRN_Project = :pid AND grn.GRN_Item = :rid), 0) AS received_qty
                 FROM pricing_estimate_resources_new p
                 JOIN pricing_estimate_new pe ON p.activity_id = pe.activity_Id AND pe.project_Id = p.project_id
                 WHERE p.project_id = :pid AND p.resource_Id = :rid AND p.pricing_status = 0",
                [':pid' => $projectid, ':rid' => $rid]
            )->queryOne();
            if ($estimateCheck && (float)$estimateCheck['received_qty'] >= (float)$estimateCheck['estimated_qty']) continue;

            // Re-indent before a PO is raised → refresh the existing indent
            $exists = $db->createCommand(
                'SELECT id FROM store_indents WHERE project_id = :pid AND resource_id = :rid',
                [':pid' => $projectid, ':rid' => $rid]
            )->queryOne();
            if ($exists) {
                $db->createCommand()->update('store_indents', [
                    'stock_at_site'    => $stock,
                    'reorder_quantity' => $reorder,
                    'raised_by'        => $uid,
                    'raised_at'        => date('Y-m-d H:i:s'),
                ], ['id' => $exists['id']])->execute();
                $saved++;
                continue;
            }

            // Fetch resource details from resources table
            $res = $db->createCommand(
                'SELECT r.Name, r.Unit, rt.Name AS rt_name
                 FROM resources r
                 JOIN resourcetype rt ON r.ResourceType_Id = rt.ResourceType_Id
                 WHERE r.Resource_Id = :rid',
                [':rid' => $rid]
            )->queryOne();
            if (!$res) continue;

            $db->createCommand()->insert('store_indents', [
                'project_id'         => $projectid,
                'resource_id'        => $rid,
                'pricing_resourceid' => 0,
                'resource_name'      => $res['Name'],
                'resource_type'      => $res['rt_name'],
                'unit'               => $res['Unit'],
                'stock_at_site'      => $stock,
                'reorder_quantity'   => $reorder,
                'raised_by'          => $uid,
                'raised_at'          => date('Y-m-d H:i:s'),
            ])->execute();
            $saved++;
        }

        return json_encode(['error' => 'No', 'saved' => $saved, 'blocked' => $blocked]);
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

        // Build cumulative qty map across all M.Books for this WO
        $allMbs = $db->createCommand(
            'SELECT entries FROM wo_measurement_book WHERE wo_number = :num AND project_id = :pid',
            [':num' => $row['wo_number'], ':pid' => $row['project_id']]
        )->queryAll();
        $cumulativeMap = [];
        foreach ($allMbs as $mb) {
            foreach (json_decode($mb['entries'] ?? '[]', true) ?: [] as $e) {
                $aid = $e['activity_id'] ?? '';
                $cumulativeMap[$aid] = ($cumulativeMap[$aid] ?? 0) + (float)($e['qty'] ?? 0);
            }
        }

        // Enrich entries with wo_qty and cumulative_qty
        $entries = json_decode($row['entries'] ?? '[]', true) ?: [];
        foreach ($entries as &$entry) {
            $aid = $entry['activity_id'] ?? '';
            $entry['wo_qty']         = $woQtyMap[$aid]      ?? 0;
            $entry['cumulative_qty'] = $cumulativeMap[$aid] ?? 0;
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
            foreach ($sentMbs as $mbRow) {
                foreach (json_decode($mbRow['entries'] ?? '[]', true) ?: [] as $e) {
                    $aid = $e['activity_id'] ?? '';
                    $cumulativeMap[$aid] = ($cumulativeMap[$aid] ?? 0) + (float)($e['qty'] ?? 0);
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
                $aid = $act['activity_id'] ?? '';
                $act['cumulative_qty'] = $cumulativeMap[$aid] ?? 0;
                if (isset($savedMap[$aid])) {
                    $act['mb_length'] = $savedMap[$aid]['length'] ?? '';
                    $act['mb_width']  = $savedMap[$aid]['width']  ?? '';
                    $act['mb_height'] = $savedMap[$aid]['height'] ?? '';
                    $act['mb_nos']    = $savedMap[$aid]['nos']    ?? '';
                    $act['mb_unit']   = $savedMap[$aid]['unit']   ?? '';
                    $act['mb_qty']    = $savedMap[$aid]['qty']    ?? '';
                    $stMap = [];
                    foreach ($savedMap[$aid]['tasks'] ?? [] as $st) { $stMap[$st['task_id']] = $st['work_done'] ?? ''; }
                    if (isset($act['tasks']) && is_array($act['tasks'])) {
                        foreach ($act['tasks'] as &$task) {
                            $task['mb_work_done'] = $stMap[$task['task_id']] ?? '';
                        }
                    }
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
        $mb_date   = preg_match('/^\d{4}-\d{2}-\d{2}$/', $rawDate) ? $rawDate : date('Y-m-d');
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

        // Gate: each measured activity must have a progress report dated today (the send date)
        $unreported = [];
        foreach ($newEntries as $entry) {
            $aid = (int)($entry['activity_id'] ?? 0);
            $qty = (float)($entry['qty'] ?? 0);
            if (!$aid || $qty <= 0) continue;
            $hasReport = (int)$db->createCommand(
                'SELECT COUNT(*) FROM scheduleactivities sa
                 JOIN schedule_progress_report_log l ON l.activity_id = sa.id
                 WHERE sa.activity_id = :aid AND sa.projectId = :pid AND sa.status = 0
                   AND l.report_date = CURDATE()',
                [':aid' => $aid, ':pid' => $projectid]
            )->queryScalar();
            if (!$hasReport) $unreported[] = $entry['activity_name'] ?? ('#' . $aid);
        }
        if (!empty($unreported)) {
            return json_encode(['error' => 'Yes',
                'errortext' => "Measurement Book cannot be sent — progress must be reported TODAY for:\n• "
                               . implode("\n• ", array_unique($unreported))
                               . "\n\nPlease report progress, then send the Measurement Book."]);
        }

        // Validate new entries against WO limits
        foreach ($newEntries as $entry) {
            $aid     = $entry['activity_id'] ?? '';
            $newQty  = (float)($entry['qty'] ?? 0);
            $woQty   = $woQtyMap[$aid] ?? 0;
            $prevQty = $cumulativeMap[$aid] ?? 0;

            if ($woQty > 0 && ($prevQty + $newQty) > $woQty) {
                $remaining = max(0, $woQty - $prevQty);
                return json_encode(['error' => 'Yes',
                    'errortext' => 'Activity "' . ($entry['activity_name'] ?? $aid) . '": quantity exceeds WO order. Only ' . $remaining . ' units remaining.']);
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
                    por.unit, por.qnty AS ordered_qty, por.rate AS po_rate
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
