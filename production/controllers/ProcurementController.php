<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\ProjuserSelection;
use kartik\mpdf\Pdf;

class ProcurementController extends Controller
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

    public function actionPurchaseorders()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();

        if (!$projuser) {
            return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);
        }

        $projectid = $projuser->projectid;
        $db  = Yii::$app->db;

        $sql = "SELECT
                    p.pricing_resourceid                           AS allocation_id,
                    r.Resource_Id                                  AS resource_id,
                    rt.Name                                        AS resource_type,
                    COALESCE(p.display_name, r.Name)               AS resource_name,
                    r.Unit                                         AS unit,
                    p.quantity * pe.activity_qty                   AS estimated_quantity,
                    p.rate                                         AS rate,
                    p.quantity * pe.activity_qty * p.rate          AS amount,
                    COALESCE(pq.purchased_quantity, 0)             AS purchased_quantity,
                    COALESCE(pq.purchased_value, 0)                AS purchased_value,
                    si.stock_at_site                               AS indent_stock,
                    si.reorder_quantity                            AS indent_reorder
                FROM pricing_estimate_resources_new p
                JOIN pricing_estimate_new pe ON p.activity_id  = pe.activity_Id
                                            AND pe.project_Id  = p.project_id
                JOIN resources r             ON p.resource_Id  = r.Resource_Id
                JOIN resourcetype rt         ON p.resourcetype_Id = rt.ResourceType_Id
                LEFT JOIN (
                    SELECT
                        por.allocation_id,
                        SUM(g.GRN_Quantity)            AS purchased_quantity,
                        SUM(g.GRN_Quantity * por.rate) AS purchased_value
                    FROM goods_received_note g
                    JOIN purchase_order_resources por ON por.order_id = g.GRN_Purchase_Order
                                                     AND por.resource_id = g.GRN_Item
                    JOIN purchase_orders po            ON po.order_id = g.GRN_Purchase_Order
                    WHERE g.GRN_Project    = :pid2
                      AND po.project_id   = :pid4
                      AND po.delete_status = 0
                      AND por.delete_status = 0
                      AND por.allocation_id IS NOT NULL
                    GROUP BY por.allocation_id
                ) pq ON p.pricing_resourceid = pq.allocation_id
                LEFT JOIN store_indents si ON si.resource_id = r.Resource_Id
                                          AND si.project_id  = :pid3
                WHERE p.project_id      = :pid
                  AND rt.Name IN ('Materials', 'Consumables', 'Purchased Inputs')
                  AND p.pricing_status  = 0
                ORDER BY rt.Name, resource_name";

        $rows = $db->createCommand($sql, [
            ':pid'  => $projectid,
            ':pid2' => $projectid,
            ':pid3' => $projectid,
            ':pid4' => $projectid,
        ])->queryAll();

        $proj = $db->createCommand(
            'SELECT Name, location FROM projects WHERE Project_Id = :pid',
            [':pid' => $projectid]
        )->queryOne();
        $projectName     = $proj ? trim($proj['Name']     ?? '') : '';
        $projectLocation = $proj ? trim($proj['location'] ?? '') : '';

        return json_encode([
            'error'            => 'No',
            'rows'             => $rows,
            'project_name'     => $projectName,
            'project_location' => $projectLocation,
        ]);
    }

    public function actionGetparameters()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);

        $projectid  = $projuser->projectid;
        $resourceid = (int)($_POST['resource_id'] ?? 0);
        $db = Yii::$app->db;

        $row = $db->createCommand(
            'SELECT reorder_level, lot_size FROM resource_parameters
             WHERE project_id = :pid AND resource_id = :rid',
            [':pid' => $projectid, ':rid' => $resourceid]
        )->queryOne();

        return json_encode(['error' => 'No', 'data' => $row ?: ['reorder_level' => '', 'lot_size' => '']]);
    }

    public function actionSaveparameters()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);

        $projectid   = $projuser->projectid;
        $resourceid  = (int)($_POST['resource_id']   ?? 0);
        $reorderLevel = $_POST['reorder_level'] ?? '';
        $lotSize      = $_POST['lot_size']      ?? '';
        $db = Yii::$app->db;

        $exists = $db->createCommand(
            'SELECT id FROM resource_parameters WHERE project_id = :pid AND resource_id = :rid',
            [':pid' => $projectid, ':rid' => $resourceid]
        )->queryOne();

        if ($exists) {
            $db->createCommand()->update('resource_parameters',
                ['reorder_level' => $reorderLevel, 'lot_size' => $lotSize, 'updated_at' => date('Y-m-d H:i:s')],
                ['project_id' => $projectid, 'resource_id' => $resourceid]
            )->execute();
        } else {
            $db->createCommand()->insert('resource_parameters', [
                'project_id'    => $projectid,
                'resource_id'   => $resourceid,
                'reorder_level' => $reorderLevel,
                'lot_size'      => $lotSize,
                'updated_at'    => date('Y-m-d H:i:s'),
            ])->execute();
        }

        return json_encode(['error' => 'No']);
    }

    public function actionGetposettings()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);

        $projectid  = $projuser->projectid;
        $resourceid = (int)($_POST['resource_id'] ?? 0);

        $row = Yii::$app->db->createCommand(
            'SELECT vendor_id, unit, rate, ship_to, terms FROM po_settings WHERE project_id = :pid AND resource_id = :rid',
            [':pid' => $projectid, ':rid' => $resourceid]
        )->queryOne();

        return json_encode(['error' => 'No', 'data' => $row ?: ['vendor_id' => '', 'unit' => '', 'rate' => '', 'ship_to' => '', 'terms' => '']]);
    }

    public function actionSaveposettings()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);

        $projectid  = $projuser->projectid;
        $resourceid = (int)($_POST['resource_id'] ?? 0);
        $vendorId   = (int)($_POST['vendor_id']   ?? 0) ?: null;
        $unit       = trim($_POST['unit']     ?? '');
        $rate       = (float)($_POST['rate']  ?? 0);
        $shipTo     = trim($_POST['ship_to']  ?? '');
        $terms      = trim($_POST['terms']    ?? '');
        $db         = Yii::$app->db;

        $exists = $db->createCommand(
            'SELECT id FROM po_settings WHERE project_id = :pid AND resource_id = :rid',
            [':pid' => $projectid, ':rid' => $resourceid]
        )->queryOne();

        if ($exists) {
            $db->createCommand()->update('po_settings',
                ['vendor_id' => $vendorId, 'unit' => $unit, 'rate' => $rate, 'ship_to' => $shipTo, 'terms' => $terms, 'updated_at' => date('Y-m-d H:i:s')],
                ['project_id' => $projectid, 'resource_id' => $resourceid]
            )->execute();
        } else {
            $db->createCommand()->insert('po_settings', [
                'project_id'  => $projectid,
                'resource_id' => $resourceid,
                'vendor_id'   => $vendorId,
                'unit'        => $unit,
                'rate'        => $rate,
                'ship_to'     => $shipTo,
                'terms'       => $terms,
                'updated_at'  => date('Y-m-d H:i:s'),
            ])->execute();
        }

        return json_encode(['error' => 'No']);
    }

    public function actionGetvendors()
    {
        $resourceId = (int)($_POST['resource_id'] ?? 0);
        $context    = trim($_POST['context'] ?? '');
        $db = Yii::$app->db;

        if ($resourceId) {
            $rows = $db->createCommand(
                'SELECT DISTINCT v.Vendor_Id, v.Name FROM vendors v
                 JOIN vendor_resources vr ON vr.vendor_id = v.Vendor_Id
                 WHERE v.Status = 0 AND vr.resource_id = :rid ORDER BY v.Name',
                [':rid' => $resourceId]
            )->queryAll();
        } elseif ($context === 'po') {
            $rows = $db->createCommand(
                "SELECT v.Vendor_Id, v.Name FROM vendors v
                 JOIN resourcetype rt ON rt.ResourceType_Id = v.resource_type_id
                 WHERE v.Status = 0
                   AND rt.Name IN ('Materials', 'Purchased Inputs', 'Consumables')
                 ORDER BY v.Name"
            )->queryAll();
        } elseif ($context === 'wo') {
            $rows = $db->createCommand(
                "SELECT v.Vendor_Id, v.Name FROM vendors v
                 JOIN resourcetype rt ON rt.ResourceType_Id = v.resource_type_id
                 WHERE v.Status = 0
                   AND rt.Name = 'Sub Contractors'
                 ORDER BY v.Name"
            )->queryAll();
        } else {
            $rows = $db->createCommand(
                'SELECT Vendor_Id, Name FROM vendors WHERE Status = 0 ORDER BY Name'
            )->queryAll();
        }

        return json_encode(['error' => 'No', 'vendors' => $rows]);
    }

    public function actionRaisepobulk()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);

        $projectid    = $projuser->projectid;
        $vendorId     = (int)($_POST['vendor_id']     ?? 0);
        $shipTo       = trim($_POST['ship_to']        ?? '');
        $creditPeriod = (int)($_POST['credit_period'] ?? 0);
        $leadTime     = trim($_POST['lead_time']      ?? '');
        $termsJson    = trim($_POST['terms']          ?? '[]');
        $resourcesJson = trim($_POST['resources']     ?? '[]');

        if (!$vendorId) return json_encode(['error' => 'Yes', 'errortext' => 'Please select a vendor.']);

        $resources = json_decode($resourcesJson, true);
        if (empty($resources)) return json_encode(['error' => 'Yes', 'errortext' => 'No resources selected.']);

        $db = Yii::$app->db;

        $seq = (int)$db->createCommand(
            'SELECT COUNT(*) FROM purchase_orders WHERE project_id = :pid',
            [':pid' => $projectid]
        )->queryScalar() + 1;
        $ordernumber = 'PO-' . $projectid . '-' . date('Ym') . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT);

        $totalAmount = 0;
        foreach ($resources as $r) {
            $totalAmount += (float)($r['qty'] ?? 0) * (float)($r['rate'] ?? 0);
        }

        $today = date('Y-m-d');
        $db->createCommand()->insert('purchase_orders', [
            'project_id'         => $projectid,
            'vendor_id'          => $vendorId,
            'specification'      => null,
            'advance'            => 0,
            'payment'            => '',
            'mobilization_charge'=> 0,
            'contact'            => '',
            'deliverydate'       => $today,
            'orderdate'          => $today,
            'place'              => '',
            'order_type'         => 1,
            'status'             => 0,
            'emailstatus'        => 0,
            'terms'              => 0,
            'Retention'          => 0,
            'Period'             => $leadTime,
            'Leaseperiod'        => '',
            'fromdate'           => $today,
            'todate'             => $today,
            'sgst'               => 0,
            'cgst'               => 0,
            'igst'               => 0,
            'from'               => '',
            'to'                 => '',
            'vehicle_no'         => '',
            'gstn_no'            => '',
            'date'               => $today,
            'raisedby'           => (string)$uid,
            'receivedby'         => '',
            'musterroll'         => 0,
            'delete_status'      => 0,
            'transportation'     => 0,
            'despatch_status'    => 0,
            'billing_address'    => $shipTo,
            'billing_to'         => '',
            'total_amount'       => $totalAmount,
            'tcStatus'           => !empty(json_decode($termsJson, true)) ? 1 : 0,
            'tcContent'          => $termsJson,
            'des_status'         => 0,
            'ordernumber'        => $ordernumber,
            'bilingcycle'        => 0,
            'paymentcycle'       => 0,
            'creditperiod'       => $creditPeriod,
            'bill_status'        => 0,
            'complete_status'    => 0,
            'no_of_workers'      => 0,
            'no_of_equipments'   => 0,
        ])->execute();

        $orderId = (int)$db->getLastInsertID();

        foreach ($resources as $r) {
            $qty    = (float)($r['qty']  ?? 0);
            $rate   = (float)($r['rate'] ?? 0);
            $amount = $qty * $rate;
            $allocationId = isset($r['allocation_id']) && $r['allocation_id'] !== '' ? (int)$r['allocation_id'] : null;
            $db->createCommand()->insert('purchase_order_resources', [
                'order_id'          => $orderId,
                'resource_id'       => (int)($r['resource_id'] ?? 0),
                'allocation_id'     => $allocationId,
                'unit'              => $r['unit']          ?? '',
                'rate'              => $rate,
                'qnty'              => $qty,
                'ordered_qty'       => $qty,
                'amount'            => $amount,
                'resource_name'     => $r['resource_name'] ?? '',
                'item_specification'=> '',
                'status'            => 0,
                'no_workers'        => 0,
                'no_days'           => 0,
                'ot_rate'           => 0,
                'date'              => $today,
                'movefrom'          => '',
                'moveto'            => '',
                'vehicle_no'        => '',
                'delete_status'     => 0,
                'received_date'     => $today,
                'despatch_status'   => 0,
                'amend_sts'         => 0,
            ])->execute();
        }

        $rids = array_values(array_filter(array_map(fn($r) => (int)($r['resource_id'] ?? 0), $resources)));
        if (!empty($rids)) {
            $db->createCommand()->delete('store_indents', [
                'project_id'  => $projectid,
                'resource_id' => $rids,
            ])->execute();
        }

        $vendor = $db->createCommand(
            'SELECT Email FROM vendors WHERE Vendor_Id = :vid',
            [':vid' => $vendorId]
        )->queryOne();

        $poHtml = $this->buildPoHtml($orderId);

        return json_encode([
            'error'        => 'No',
            'html'         => $poHtml,
            'order_id'     => $orderId,
            'ordernumber'  => $ordernumber,
            'vendor_email' => $vendor['Email'] ?? '',
        ]);
    }

    public function actionEmailpo()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);

        $orderId = (int)($_POST['order_id'] ?? 0);
        $to      = trim($_POST['to']      ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $body    = trim($_POST['body']    ?? '');

        if (!$orderId) return json_encode(['error' => 'Yes', 'errortext' => 'Invalid order.']);
        if (!$to)      return json_encode(['error' => 'Yes', 'errortext' => 'Recipient email is required.']);

        $db = Yii::$app->db;
        $po = $db->createCommand(
            'SELECT po.ordernumber FROM purchase_orders po
             WHERE po.order_id = :oid AND po.project_id = :pid AND po.delete_status = 0',
            [':oid' => $orderId, ':pid' => $projuser->projectid]
        )->queryOne();

        if (!$po) return json_encode(['error' => 'Yes', 'errortext' => 'Purchase order not found.']);

        if (!$subject) $subject = 'Purchase Order ' . $po['ordernumber'];
        if (!$body)    $body    = 'Please find the Purchase Order attached.';

        $poHtml = $this->buildPoHtml($orderId);

        try {
            $pdf  = new Pdf();
            $mpdf = $pdf->api;
            $mpdf->WriteHtml($poHtml);
            $pdfContent = $mpdf->Output('', 'S');
        } catch (\Exception $e) {
            return json_encode(['error' => 'Yes', 'errortext' => 'Failed to generate PDF: ' . $e->getMessage()]);
        }

        try {
            Yii::$app->mailer->compose()
                ->setFrom(['procurement@geotech.net.in' => 'GEO-TECH Procurement'])
                ->setTo($to)
                ->setSubject($subject)
                ->setHtmlBody(nl2br(htmlspecialchars($body, ENT_QUOTES)))
                ->attachContent($pdfContent, [
                    'fileName'    => 'PO-' . $po['ordernumber'] . '.pdf',
                    'contentType' => 'application/pdf',
                ])
                ->send();
        } catch (\Exception $e) {
            return json_encode(['error' => 'Yes', 'errortext' => 'Failed to send email: ' . $e->getMessage()]);
        }

        return json_encode(['error' => 'No']);
    }

    public function actionIssuedorders()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);

        $projectid = $projuser->projectid;
        $db = Yii::$app->db;

        $sql = "SELECT po.order_id, po.ordernumber, po.orderdate, po.delete_status,
                       v.Name AS vendor_name, v.Email AS vendor_email
                FROM purchase_orders po
                JOIN vendors v ON v.Vendor_Id = po.vendor_id
                WHERE po.project_id = :pid
                ORDER BY po.orderdate DESC, po.order_id DESC";

        $rows = $db->createCommand($sql, [':pid' => $projectid])->queryAll();
        return json_encode(['error' => 'No', 'rows' => $rows]);
    }

    public function actionCancelpo()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);

        $projectid = $projuser->projectid;
        $db = Yii::$app->db;
        $order_id = (int)($_POST['order_id'] ?? 0);

        $po = $db->createCommand(
            'SELECT order_id FROM purchase_orders WHERE order_id = :oid AND project_id = :pid AND delete_status = 0',
            [':oid' => $order_id, ':pid' => $projectid]
        )->queryOne();

        if (!$po) return json_encode(['error' => 'Yes', 'errortext' => 'Purchase order not found.']);

        $grnExists = $db->createCommand(
            'SELECT COUNT(*) FROM goods_received_note WHERE GRN_Purchase_Order = :oid',
            [':oid' => $order_id]
        )->queryScalar();

        if ($grnExists > 0) {
            return json_encode(['error' => 'Yes', 'errortext' => 'This order cannot be cancelled as goods have already been received against it.']);
        }

        $db->createCommand()->update('purchase_orders', ['delete_status' => 1], ['order_id' => $order_id])->execute();
        return json_encode(['error' => 'No']);
    }

    public function actionRecoverpo()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);

        $db = Yii::$app->db;
        $order_id = (int)($_POST['order_id'] ?? 0);

        $po = $db->createCommand(
            'SELECT order_id FROM purchase_orders WHERE order_id = :oid AND project_id = :pid AND delete_status = 1',
            [':oid' => $order_id, ':pid' => $projuser->projectid]
        )->queryOne();

        if (!$po) return json_encode(['error' => 'Yes', 'errortext' => 'Cancelled purchase order not found.']);

        $db->createCommand()->update('purchase_orders', ['delete_status' => 0], ['order_id' => $order_id])->execute();
        return json_encode(['error' => 'No']);
    }

    public function actionViewwo()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);

        $db        = Yii::$app->db;
        $wo_number = trim($_POST['wo_number'] ?? '');

        $wo = $db->createCommand(
            'SELECT WO_Number FROM work_order WHERE WO_Number = :num AND Project_Id = :pid',
            [':num' => $wo_number, ':pid' => $projuser->projectid]
        )->queryOne();

        if (!$wo) return json_encode(['error' => 'Yes', 'errortext' => 'Work order not found.']);

        $html = $this->buildWoHtml($wo_number);
        return json_encode(['error' => 'No', 'html' => $html]);
    }

    public function actionSendcancellation()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);

        $projectid = $projuser->projectid;
        $db = Yii::$app->db;
        $order_id = (int)($_POST['order_id'] ?? 0);
        $body     = trim($_POST['body'] ?? '');

        $po = $db->createCommand(
            'SELECT po.ordernumber, po.orderdate, v.Email AS vendor_email
             FROM purchase_orders po
             JOIN vendors v ON v.Vendor_Id = po.vendor_id
             WHERE po.order_id = :oid AND po.project_id = :pid',
            [':oid' => $order_id, ':pid' => $projectid]
        )->queryOne();

        if (!$po) return json_encode(['error' => 'Yes', 'errortext' => 'Purchase order not found.']);
        if (empty($po['vendor_email'])) return json_encode(['error' => 'Yes', 'errortext' => 'Vendor email not found.']);

        $subject = 'Cancellation Notice — Order ' . $po['ordernumber'];

        try {
            Yii::$app->mailer->compose()
                ->setFrom(['procurement@geotech.net.in' => 'GEO-TECH Procurement'])
                ->setTo($po['vendor_email'])
                ->setSubject($subject)
                ->setHtmlBody(nl2br(htmlspecialchars($body, ENT_QUOTES)))
                ->send();
        } catch (\Exception $e) {
            return json_encode(['error' => 'Yes', 'errortext' => 'Failed to send email: ' . $e->getMessage()]);
        }

        return json_encode(['error' => 'No']);
    }

    public function actionViewpo()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();

        if (!$projuser) {
            return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);
        }

        $projectid = $projuser->projectid;
        $db = Yii::$app->db;
        $order_id  = (int)($_POST['order_id'] ?? 0);

        $po = $db->createCommand(
            'SELECT order_id FROM purchase_orders WHERE order_id = :oid AND project_id = :pid',
            [':oid' => $order_id, ':pid' => $projectid]
        )->queryOne();

        if (!$po) {
            return json_encode(['error' => 'Yes', 'errortext' => 'Purchase Order not found.']);
        }

        $html = $this->buildPoHtml($order_id);
        return json_encode(['error' => 'No', 'html' => $html]);
    }

    private function buildPoHtml($orderId)
    {
        $db = Yii::$app->db;

        $po = $db->createCommand(
            'SELECT po.*, v.Name AS vendor_name, v.Address AS vendor_address,
                    v.Phone AS vendor_phone, v.Email AS vendor_email
             FROM purchase_orders po
             LEFT JOIN vendors v ON po.vendor_id = v.Vendor_Id
             WHERE po.order_id = :oid',
            [':oid' => $orderId]
        )->queryOne();

        if (!$po) return '<p>Purchase Order not found.</p>';

        $items = $db->createCommand(
            'SELECT por.*, r.Name AS res_name
             FROM purchase_order_resources por
             LEFT JOIN resources r ON por.resource_id = r.Resource_Id
             WHERE por.order_id = :oid AND por.delete_status = 0',
            [':oid' => $orderId]
        )->queryAll();

        $logoUrl  = Yii::$app->request->hostInfo . Yii::$app->request->baseUrl . '/images/logo.png';
        $terms    = json_decode($po['tcContent'] ?? '[]', true) ?: [];
        $total    = 0;
        foreach ($items as $item) $total += (float)$item['amount'];

        $h = '<div style="font-family:Arial,sans-serif;max-width:860px;margin:0 auto;border:1px solid #888;font-size:13px;">';

        // Header
        $h .= '<table style="width:100%;border-collapse:collapse;background:#909090;">'
            . '<tr><td style="padding:12px 18px;width:45%;">'
            . '<div style="display:inline-block;background:#b53535;border-radius:50px;padding:7px 20px;vertical-align:middle;">'
            . '<span style="color:#fff;font-size:13px;font-weight:700;letter-spacing:1.5px;font-family:Arial,sans-serif;">GEO-TECH</span>'
            . '</div>'
            . '</td><td style="padding:12px 18px;text-align:right;">'
            . '<div style="color:#fff;font-size:20px;font-weight:700;letter-spacing:1px;">PURCHASE ORDER</div>'
            . '<div style="color:#e8e8e8;font-size:11px;margin-top:3px;">GEO-TECH OFFSHORE STRUCTURES.(P) LTD</div>'
            . '</td></tr></table>';

        // PO No + Date
        $h .= '<table style="width:100%;border-collapse:collapse;border-bottom:2px solid #909090;background:#f8f8f8;">'
            . '<tr>'
            . '<td style="padding:8px 18px;font-size:12px;color:#333;"><strong>PO No:</strong> ' . htmlspecialchars($po['ordernumber'], ENT_QUOTES) . '</td>'
            . '<td style="padding:8px 18px;font-size:12px;color:#333;text-align:right;"><strong>Date:</strong> ' . date('d M Y', strtotime($po['date'])) . '</td>'
            . '</tr></table>';

        // Vendor + Ship To
        $h .= '<table style="width:100%;border-collapse:collapse;border-bottom:1px solid #999;">'
            . '<tr>'
            . '<td style="width:50%;padding:14px 18px;border-right:1px solid #999;vertical-align:top;">'
            . '<div style="font-size:10px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;">Vendor</div>'
            . '<div style="font-size:13px;font-weight:600;color:#222;">' . htmlspecialchars($po['vendor_name'] ?? '', ENT_QUOTES) . '</div>'
            . '<div style="font-size:12px;color:#555;margin-top:3px;">' . nl2br(htmlspecialchars($po['vendor_address'] ?? '', ENT_QUOTES)) . '</div>'
            . '<div style="font-size:12px;color:#555;margin-top:3px;">';
        if (!empty($po['vendor_phone'])) $h .= 'Ph: ' . htmlspecialchars($po['vendor_phone'], ENT_QUOTES) . '&nbsp;&nbsp;';
        if (!empty($po['vendor_email'])) $h .= 'Email: ' . htmlspecialchars($po['vendor_email'], ENT_QUOTES);
        $h .= '</div></td>'
            . '<td style="width:50%;padding:14px 18px;vertical-align:top;">'
            . '<div style="font-size:10px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;">Ship To</div>'
            . '<div style="font-size:12px;color:#555;">' . nl2br(htmlspecialchars($po['billing_address'] ?? '', ENT_QUOTES)) . '</div>'
            . '</td></tr></table>';

        // Credit Period + Lead Time
        $h .= '<table style="width:100%;border-collapse:collapse;background:#f8f8f8;border-bottom:1px solid #999;">'
            . '<tr>'
            . '<td style="padding:7px 18px;font-size:12px;color:#333;width:50%;border-right:1px solid #999;">'
            . '<strong>Credit Period:</strong> ' . (int)$po['creditperiod'] . ' days'
            . '</td>'
            . '<td style="padding:7px 18px;font-size:12px;color:#333;">'
            . '<strong>Lead Time:</strong> ' . htmlspecialchars($po['Period'] ?? '', ENT_QUOTES)
            . '</td>'
            . '</tr></table>';

        // Items table
        $h .= '<table style="width:100%;border-collapse:collapse;">'
            . '<thead><tr style="background:#909090;color:#fff;">'
            . '<th style="padding:7px 10px;text-align:center;width:36px;font-size:11px;border:1px solid #7a7a7a;">S.No</th>'
            . '<th style="padding:7px 10px;text-align:left;font-size:11px;border:1px solid #7a7a7a;">Resource</th>'
            . '<th style="padding:7px 10px;text-align:center;width:65px;font-size:11px;border:1px solid #7a7a7a;">Unit</th>'
            . '<th style="padding:7px 10px;text-align:right;width:75px;font-size:11px;border:1px solid #7a7a7a;">Qty</th>'
            . '<th style="padding:7px 10px;text-align:right;width:90px;font-size:11px;border:1px solid #7a7a7a;">Rate</th>'
            . '<th style="padding:7px 10px;text-align:right;width:110px;font-size:11px;border:1px solid #7a7a7a;">Amount</th>'
            . '</tr></thead><tbody>';

        foreach ($items as $i => $item) {
            $bg = ($i % 2 === 1) ? 'background:#f5f5f5;' : '';
            $h .= '<tr style="' . $bg . '">'
                . '<td style="padding:6px 10px;text-align:center;border:1px solid #999;font-size:12px;color:#888;">' . ($i + 1) . '</td>'
                . '<td style="padding:6px 10px;border:1px solid #999;font-size:12px;">' . htmlspecialchars($item['resource_name'] ?? '', ENT_QUOTES) . '</td>'
                . '<td style="padding:6px 10px;text-align:center;border:1px solid #999;font-size:12px;">' . htmlspecialchars($item['unit'] ?? '', ENT_QUOTES) . '</td>'
                . '<td style="padding:6px 10px;text-align:right;border:1px solid #999;font-size:12px;">' . number_format((float)$item['qnty'], 2) . '</td>'
                . '<td style="padding:6px 10px;text-align:right;border:1px solid #999;font-size:12px;">' . number_format((float)$item['rate'], 2) . '</td>'
                . '<td style="padding:6px 10px;text-align:right;border:1px solid #999;font-size:12px;font-weight:600;">' . number_format((float)$item['amount'], 2) . '</td>'
                . '</tr>';
        }
        $h .= '</tbody></table>';

        // Total
        $h .= '<table style="width:100%;border-collapse:collapse;border-top:2px solid #909090;">'
            . '<tr style="background:#efefef;">'
            . '<td style="padding:9px 18px;font-size:13px;font-weight:700;color:#555;text-align:right;">'
            . 'Total Amount: ' . number_format($total, 2)
            . '</td></tr></table>';

        // Signatory (left) + Terms (right) side by side
        $termsHtml = '';
        if (!empty($terms)) {
            $termsHtml .= '<div style="font-size:10px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;">Terms &amp; Conditions</div>'
                        . '<ol style="margin:0;padding-left:18px;">';
            foreach ($terms as $t) {
                $termsHtml .= '<li style="font-size:12px;color:#555;margin-bottom:3px;">' . htmlspecialchars($t, ENT_QUOTES) . '</li>';
            }
            $termsHtml .= '</ol>';
        }

        $h .= '<table style="width:100%;border-collapse:collapse;border-top:1px solid #999;">'
            . '<tr>'
            . '<td style="width:45%;padding:20px 18px 20px;vertical-align:bottom;border-right:1px solid #999;">'
            . '<div style="font-size:12px;color:#333;margin-bottom:24px;">For GEO-TECH OFFSHORE STRUCTURES.(P) LTD</div>'
            . '<div style="display:inline-block;border-top:1px solid #333;width:180px;padding-top:4px;font-size:11px;color:#555;">Authorized Signatory</div>'
            . '</td>'
            . '<td style="width:55%;padding:14px 18px 20px;vertical-align:top;">'
            . $termsHtml
            . '</td>'
            . '</tr></table>';

        $h .= '</div>';
        return $h;
    }

    public function actionWorkorders()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);

        $projectid = $projuser->projectid;
        $db = Yii::$app->db;

        $db->createCommand("CREATE TABLE IF NOT EXISTS `wo_wdq` (
            `id`            INT(11)        NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `project_id`    INT(11)        NOT NULL DEFAULT 0,
            `activity_id`   INT(11)        NOT NULL DEFAULT 0,
            `work_done_qty` DECIMAL(12,3)  NOT NULL DEFAULT 0,
            `updated_at`    TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY `uq_proj_act` (`project_id`, `activity_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8")->execute();

        $sql = "
            SELECT
                wa.id                           AS activity_id,
                wa.activity_Name                AS activity_name,
                COALESCE(sa.unit, wa.activity_Unit) AS unit,
                COALESCE(sa.quantity, 0)            AS estimated_quantity,
                COALESCE(sa.quantity, 0) * COALESCE((
                    SELECT SUM(pr.quantity * pr.rate)
                    FROM pricing_estimate_resources_new pr
                    WHERE pr.activity_id     = wa.id
                      AND pr.project_id      = :pid5
                      AND pr.resourcetype_Id = 4
                      AND pr.pricing_status  = 0
                ), 0)                               AS sc_estimated_value,
                COALESCE(wdq.work_done_qty, 0)  AS work_done_qty,
                eat.activitytype_name           AS activity_type,
                wn.Name                         AS workgroup_name,
                sa.id                           AS schedule_activity_id
            FROM workgroup_activities_new wa
            JOIN workgroups_new wn
                ON  wn.Workgroup_Id = wa.wbs_id
                AND wn.Project_Id   = :pid
                AND wn.Status       = 0
            LEFT JOIN estimateactivitytypes eat
                ON  eat.activitytype_id     = wa.activitytype_id
                AND eat.activitytype_status = 0
            LEFT JOIN pricing_estimate_new pe
                ON  pe.activity_Id    = wa.id
                AND pe.project_Id     = :pid2
                AND pe.pricing_status = 0
            LEFT JOIN scheduleactivities sa
                ON  sa.activity_id = wa.id
                AND sa.projectId   = :pid3
                AND sa.status      = 0
            LEFT JOIN wo_wdq wdq
                ON  wdq.activity_id = wa.id
                AND wdq.project_id  = :pid4
            WHERE wa.pricing_status = 0
            ORDER BY COALESCE(wn.sortorder, 999999) ASC, COALESCE(wa.sortorder, 999999) ASC, sa.id ASC
        ";

        $rows = $db->createCommand($sql, [':pid' => $projectid, ':pid2' => $projectid, ':pid3' => $projectid, ':pid4' => $projectid, ':pid5' => $projectid])->queryAll();

        // Build M.Book qty map: sum qty per activity_id from all issued (sent_status=1) M.Books
        $mbBooks = $db->createCommand(
            'SELECT entries FROM wo_measurement_book WHERE project_id = :pid AND sent_status = 1 AND delete_status = 0',
            [':pid' => $projectid]
        )->queryAll();
        $mbQtyMap   = [];
        $mbValueMap = [];
        foreach ($mbBooks as $mb) {
            $entries = json_decode($mb['entries'] ?? '[]', true) ?: [];
            foreach ($entries as $e) {
                $aid = (string)($e['activity_id'] ?? '');
                if ($aid !== '') {
                    $mbQtyMap[$aid] = ($mbQtyMap[$aid] ?? 0) + (float)($e['qty'] ?? 0);
                    foreach ($e['tasks'] ?? [] as $t) {
                        $mbValueMap[$aid] = ($mbValueMap[$aid] ?? 0)
                            + (float)($t['rate'] ?? 0) * (float)($t['work_done'] ?? 0);
                    }
                }
            }
        }
        foreach ($rows as &$row) {
            $row['mb_work_done_qty']   = $mbQtyMap[(string)$row['activity_id']]   ?? 0;
            $row['mb_work_done_value'] = $mbValueMap[(string)$row['activity_id']] ?? 0;
        }
        unset($row);

        return json_encode(['error' => 'No', 'rows' => $rows]);
    }

    public function actionSavewdq()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);

        $activityId  = (int)($_POST['activity_id']   ?? 0);
        $workDoneQty = (float)($_POST['work_done_qty'] ?? 0);
        if (!$activityId) return json_encode(['error' => 'Yes', 'errortext' => 'Invalid data.']);

        $db = Yii::$app->db;
        $db->createCommand("CREATE TABLE IF NOT EXISTS `wo_wdq` (
            `id`            INT(11)        NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `project_id`    INT(11)        NOT NULL DEFAULT 0,
            `activity_id`   INT(11)        NOT NULL DEFAULT 0,
            `work_done_qty` DECIMAL(12,3)  NOT NULL DEFAULT 0,
            `updated_at`    TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY `uq_proj_act` (`project_id`, `activity_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8")->execute();

        $db->createCommand(
            "INSERT INTO wo_wdq (project_id, activity_id, work_done_qty)
             VALUES (:pid, :aid, :qty)
             ON DUPLICATE KEY UPDATE work_done_qty = :qty2",
            [':pid' => $projuser->projectid, ':aid' => $activityId, ':qty' => $workDoneQty, ':qty2' => $workDoneQty]
        )->execute();

        return json_encode(['error' => 'No']);
    }

    public function actionWotasks()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);

        $schedId   = (int)$_POST['schedule_activity_id'];
        $projectid = $projuser->projectid;
        $db        = Yii::$app->db;

        // Auto-create task rates table if not exists
        $db->createCommand("CREATE TABLE IF NOT EXISTS `wo_task_rates` (
            `id`          INT(11)        NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `project_id`  INT(11)        NOT NULL DEFAULT 0,
            `activity_id` INT(11)        NOT NULL DEFAULT 0,
            `task_id`     INT(11)        NOT NULL DEFAULT 0,
            `rate`        DECIMAL(12,2)  NOT NULL DEFAULT 0,
            `task_qty`    DECIMAL(12,3)  NOT NULL DEFAULT 0,
            `selected`    TINYINT(1)     NOT NULL DEFAULT 1,
            `updated_at`  TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY `uq_act_task` (`activity_id`, `task_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8")->execute();
        try { $db->createCommand("ALTER TABLE `wo_task_rates` ADD COLUMN `selected` TINYINT(1) NOT NULL DEFAULT 1")->execute(); } catch (\Exception $e) {}

        $sa = $db->createCommand(
            "SELECT id, name, unit, activity_id, quantity FROM scheduleactivities WHERE id=:id AND projectId=:pid AND status=0",
            [':id' => $schedId, ':pid' => $projectid]
        )->queryOne();

        if (!$sa) return json_encode(['error' => 'Yes', 'errortext' => 'Activity not found.']);

        // Bridge to master activity_Id in estimateactivities via workgroup_activities_new
        $wbn = $db->createCommand(
            "SELECT activity_Id FROM workgroup_activities_new WHERE id=:id",
            [':id' => $sa['activity_id']]
        )->queryOne();
        $masterActivityId = ($wbn && $wbn['activity_Id']) ? $wbn['activity_Id'] : $sa['activity_id'];

        // Auto-create saved qty table
        $db->createCommand("CREATE TABLE IF NOT EXISTS `wo_activity_qty` (
            `id`          INT(11)       NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `project_id`  INT(11)       NOT NULL DEFAULT 0,
            `activity_id` INT(11)       NOT NULL DEFAULT 0,
            `qty`         DECIMAL(12,3) NOT NULL DEFAULT 0,
            `updated_at`  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY `uq_proj_act` (`project_id`, `activity_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8")->execute();

        // Default from schedule; use saved override if present
        $activityQty = (float)($sa['quantity'] ?? 0);
        $savedQtyRow = $db->createCommand(
            "SELECT qty FROM wo_activity_qty WHERE activity_id=:aid AND project_id=:pid LIMIT 1",
            [':aid' => $sa['activity_id'], ':pid' => $projectid]
        )->queryOne();
        if ($savedQtyRow) $activityQty = (float)$savedQtyRow['qty'];

        $taskRows = $db->createCommand(
            "SELECT id, task_name, task_unit, productivity FROM activity_tasks
             WHERE activity_id=:mid ORDER BY sort_order ASC",
            [':mid' => $masterActivityId]
        )->queryAll();

        $rows = [];
        foreach ($taskRows as $t) {
            // task_unit_qty: qty per unit from schedule-task screen (no wo_task_rates override)
            $stRow = $db->createCommand(
                "SELECT task_qty FROM schedule_task_new WHERE activity_Id=:sid AND task_Id=:tid LIMIT 1",
                [':sid' => $schedId, ':tid' => $t['id']]
            )->queryOne();
            $taskUnitQty = ($stRow && $stRow['task_qty'] > 0) ? (float)$stRow['task_qty'] : (float)$t['productivity'];

            $savedRate = $db->createCommand(
                "SELECT rate, task_qty, total_qty, selected FROM wo_task_rates WHERE activity_id=:aid AND task_id=:tid LIMIT 1",
                [':aid' => $sa['activity_id'], ':tid' => $t['id']]
            )->queryOne();

            $rows[] = [
                'task_id'         => (int)$t['id'],
                'task_name'       => $t['task_name'],
                'task_unit'       => $t['task_unit'],
                'task_unit_qty'   => $taskUnitQty,
                'task_qty'        => $taskUnitQty,
                'saved_total_qty' => $savedRate && $savedRate['total_qty'] !== null ? (float)$savedRate['total_qty'] : null,
                'saved_rate'      => $savedRate ? (float)$savedRate['rate'] : null,
                'selected'        => $savedRate ? (int)$savedRate['selected'] : 1,
            ];
        }

        return json_encode(['error' => 'No', 'activity_name' => $sa['name'], 'unit' => $sa['unit'],
                            'activity_qty' => $activityQty, 'rows' => $rows]);
    }

    public function actionCheckworesources()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);
        $projectid = (int)$projuser->projectid;
        $db  = Yii::$app->db;
        $ids = json_decode($_POST['activity_ids'] ?? '[]', true);
        foreach ((array)$ids as $actId) {
            $actId = (int)$actId;
            if (!$actId) continue;
            $n = $db->createCommand(
                "SELECT COUNT(*) FROM pricing_estimate_resources_new
                 WHERE activity_id=:aid AND project_id=:pid AND resourcetype_Id=4 AND pricing_status=0",
                [':aid' => $actId, ':pid' => $projectid]
            )->queryScalar();
            if (!$n) return json_encode(['error' => 'Yes', 'errortext' => 'Please allocate a contractor for this task']);

            // Check each selected task in wo_task_rates is mapped to a SC resource via task_ids
            $selectedTasks = $db->createCommand(
                "SELECT task_id FROM wo_task_rates WHERE activity_id=:aid AND project_id=:pid AND selected=1",
                [':aid' => $actId, ':pid' => $projectid]
            )->queryColumn();
            foreach ($selectedTasks as $tid) {
                $mapped = $db->createCommand(
                    "SELECT COUNT(*) FROM pricing_estimate_resources_new
                     WHERE activity_id=:aid AND project_id=:pid AND resourcetype_Id=4
                       AND pricing_status=0 AND FIND_IN_SET(:tid, task_ids)",
                    [':aid' => $actId, ':pid' => $projectid, ':tid' => (int)$tid]
                )->queryScalar();
                if (!$mapped) return json_encode(['error' => 'Yes',
                    'errortext' => 'Please map the resource to this task in the Resource Allocation page before raising the Work Order.']);
            }
        }
        return json_encode(['error' => 'No']);
    }

    public function actionSavewotaskrates()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return ['error' => 'Yes', 'errortext' => 'No project selected.'];

        $activityId  = (int)($_POST['activity_id'] ?? 0);
        $activityQty = isset($_POST['activity_qty']) && $_POST['activity_qty'] !== '' ? (float)$_POST['activity_qty'] : null;
        $tasksJson   = trim($_POST['tasks'] ?? '[]');
        $tasks       = json_decode($tasksJson, true);

        if (!$activityId || empty($tasks)) return ['error' => 'Yes', 'errortext' => 'Invalid data.'];

        $db = Yii::$app->db;
        $projectid = (int)$projuser->projectid;

        // Ensure a sub-contractor resource is allocated for this activity
        $scCount = $db->createCommand(
            "SELECT COUNT(*) FROM pricing_estimate_resources_new
             WHERE activity_id=:aid AND project_id=:pid AND resourcetype_Id=4 AND pricing_status=0",
            [':aid' => $activityId, ':pid' => $projectid]
        )->queryScalar();
        if (!$scCount) {
            return ['error' => 'Yes', 'errortext' => 'Please allocate a contractor for this task'];
        }

        // Auto-create table on first use
        $db->createCommand("CREATE TABLE IF NOT EXISTS `wo_task_rates` (
            `id`          INT(11)        NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `project_id`  INT(11)        NOT NULL DEFAULT 0,
            `activity_id` INT(11)        NOT NULL DEFAULT 0,
            `task_id`     INT(11)        NOT NULL DEFAULT 0,
            `rate`        DECIMAL(12,2)  NOT NULL DEFAULT 0,
            `task_qty`    DECIMAL(12,3)  NOT NULL DEFAULT 0,
            `total_qty`   DECIMAL(12,3)  NULL,
            `selected`    TINYINT(1)     NOT NULL DEFAULT 1,
            `updated_at`  TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY `uq_act_task` (`activity_id`, `task_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8")->execute();
        try { $db->createCommand("ALTER TABLE `wo_task_rates` ADD COLUMN `total_qty` DECIMAL(12,3) NULL")->execute(); } catch (\Exception $e) {}
        try { $db->createCommand("ALTER TABLE `wo_task_rates` ADD COLUMN `selected` TINYINT(1) NOT NULL DEFAULT 1")->execute(); } catch (\Exception $e) {}

        try {
            // Save activity qty override if provided
            if ($activityQty !== null) {
                $db->createCommand("CREATE TABLE IF NOT EXISTS `wo_activity_qty` (
                    `id`          INT(11)       NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    `project_id`  INT(11)       NOT NULL DEFAULT 0,
                    `activity_id` INT(11)       NOT NULL DEFAULT 0,
                    `qty`         DECIMAL(12,3) NOT NULL DEFAULT 0,
                    `updated_at`  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    UNIQUE KEY `uq_proj_act` (`project_id`, `activity_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8")->execute();
                $existsQty = $db->createCommand(
                    "SELECT id FROM wo_activity_qty WHERE activity_id=:aid AND project_id=:pid",
                    [':aid' => $activityId, ':pid' => $projuser->projectid]
                )->queryOne();
                if ($existsQty) {
                    $db->createCommand()->update('wo_activity_qty',
                        ['qty' => $activityQty],
                        ['activity_id' => $activityId, 'project_id' => $projuser->projectid]
                    )->execute();
                } else {
                    $db->createCommand()->insert('wo_activity_qty', [
                        'project_id'  => $projuser->projectid,
                        'activity_id' => $activityId,
                        'qty'         => $activityQty,
                    ])->execute();
                }
            }

            // Pre-validate all selected tasks before saving
            foreach ($tasks as $task) {
                if ((int)($task['selected'] ?? 1) !== 1) continue;
                $taskId = (int)($task['task_id'] ?? 0);
                if (!$taskId) continue;
                if ((float)($task['rate'] ?? 0) <= 0) {
                    return ['error' => 'Yes', 'errortext' => 'Please enter the rate for all selected tasks before saving.'];
                }
                $mapped = $db->createCommand(
                    "SELECT COUNT(*) FROM pricing_estimate_resources_new
                     WHERE activity_id=:aid AND project_id=:pid AND resourcetype_Id=4
                       AND pricing_status=0 AND FIND_IN_SET(:tid, task_ids)",
                    [':aid' => $activityId, ':pid' => $projectid, ':tid' => $taskId]
                )->queryScalar();
                if (!$mapped) {
                    return ['error' => 'Yes', 'errortext' => 'Please map a Sub Contractor resource to this task in the Resource Allocation page before saving.'];
                }
            }

            foreach ($tasks as $task) {
                $taskId   = (int)($task['task_id']  ?? 0);
                $rate     = (float)($task['rate']   ?? 0);
                $taskQty  = (float)($task['task_qty'] ?? 0);
                $totalQty = isset($task['total_qty']) && $task['total_qty'] !== '' ? (float)$task['total_qty'] : null;
                $selected = isset($task['selected']) ? (int)$task['selected'] : 1;
                if (!$taskId) continue;

                $exists = $db->createCommand(
                    "SELECT id FROM wo_task_rates WHERE activity_id=:aid AND task_id=:tid",
                    [':aid' => $activityId, ':tid' => $taskId]
                )->queryOne();

                if ($exists) {
                    $db->createCommand()->update('wo_task_rates',
                        ['rate' => $rate, 'task_qty' => $taskQty, 'total_qty' => $totalQty, 'selected' => $selected, 'project_id' => $projuser->projectid],
                        ['activity_id' => $activityId, 'task_id' => $taskId]
                    )->execute();
                } else {
                    $db->createCommand()->insert('wo_task_rates', [
                        'project_id'  => $projuser->projectid,
                        'activity_id' => $activityId,
                        'task_id'     => $taskId,
                        'rate'        => $rate,
                        'task_qty'    => $taskQty,
                        'total_qty'   => $totalQty,
                        'selected'    => $selected,
                    ])->execute();
                }
            }
        } catch (\Exception $e) {
            return ['error' => 'Yes', 'errortext' => 'DB error: ' . $e->getMessage()];
        }

        return ['error' => 'No'];
    }

    public function actionSavewoscrates()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);

        $activityId    = (int)($_POST['activity_id'] ?? 0);
        $resourcesJson = trim($_POST['resources'] ?? '[]');
        $resources     = json_decode($resourcesJson, true);

        if (!$activityId || empty($resources)) return json_encode(['error' => 'Yes', 'errortext' => 'Invalid data.']);

        $db = Yii::$app->db;

        $db->createCommand("CREATE TABLE IF NOT EXISTS `wo_sc_rates` (
            `id`          INT(11)        NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `project_id`  INT(11)        NOT NULL DEFAULT 0,
            `activity_id` INT(11)        NOT NULL DEFAULT 0,
            `resource_id` INT(11)        NOT NULL DEFAULT 0,
            `rate`        DECIMAL(12,2)  NOT NULL DEFAULT 0,
            `qty`         DECIMAL(12,3)  NOT NULL DEFAULT 0,
            `selected`    TINYINT(1)     NOT NULL DEFAULT 1,
            `updated_at`  TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY `uq_act_res` (`activity_id`, `resource_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8")->execute();
        try { $db->createCommand("ALTER TABLE `wo_sc_rates` ADD COLUMN `selected` TINYINT(1) NOT NULL DEFAULT 1")->execute(); } catch (\Exception $e) {}

        foreach ($resources as $res) {
            $resourceId = (int)($res['resource_id'] ?? 0);
            $rate       = (float)($res['rate']       ?? 0);
            $selected   = isset($res['selected']) ? (int)$res['selected'] : 1;
            if (!$resourceId) continue;

            $exists = $db->createCommand(
                "SELECT id FROM wo_sc_rates WHERE activity_id=:aid AND resource_id=:rid",
                [':aid' => $activityId, ':rid' => $resourceId]
            )->queryOne();

            if ($exists) {
                $db->createCommand()->update('wo_sc_rates',
                    ['rate' => $rate, 'selected' => $selected, 'project_id' => $projuser->projectid],
                    ['activity_id' => $activityId, 'resource_id' => $resourceId]
                )->execute();
            } else {
                $db->createCommand()->insert('wo_sc_rates', [
                    'project_id'  => $projuser->projectid,
                    'activity_id' => $activityId,
                    'resource_id' => $resourceId,
                    'rate'        => $rate,
                    'selected'    => $selected,
                    'qty'         => 0,
                ])->execute();
            }
        }

        return json_encode(['error' => 'No']);
    }

    public function actionRaiseworkorder()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);

        $projectid      = $projuser->projectid;
        $vendorId       = (int)($_POST['vendor_id']     ?? 0);
        $subject        = trim($_POST['subject']        ?? '');
        $scope          = trim($_POST['scope']          ?? '');
        $leadTime       = trim($_POST['lead_time']       ?? '');
        $startDate      = trim($_POST['start_date']      ?? '');
        $paymentCycle   = trim($_POST['payment_cycle']   ?? '');
        $termsJson      = trim($_POST['terms']          ?? '[]');
        $activitiesJson = trim($_POST['activities']     ?? '[]');

        if (!$vendorId)   return json_encode(['error' => 'Yes', 'errortext' => 'Please select a vendor.']);

        $activities = json_decode($activitiesJson, true);
        if (empty($activities)) return json_encode(['error' => 'Yes', 'errortext' => 'No activities selected.']);

        $db = Yii::$app->db;

        // Auto-build delivery address from project details
        $project = $db->createCommand(
            'SELECT Name, location, client_name FROM projects WHERE Project_Id = :pid',
            [':pid' => $projectid]
        )->queryOne();
        $shipTo = $project['Name'] ?? '';
        if (!empty($project['location']))    $shipTo .= "\n" . $project['location'];
        if (!empty($project['client_name'])) $shipTo .= "\nClient: " . $project['client_name'];

        $seq = (int)$db->createCommand(
            'SELECT COUNT(*) FROM work_order WHERE Project_Id = :pid', [':pid' => $projectid]
        )->queryScalar() + 1;
        $ordernumber = 'WO-' . $projectid . '-' . date('Ym') . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT);

        $grandTotal = 0;
        // For each activity, if tasks came in empty from frontend cache,
        // hydrate them from the DB (wo_task_rates + activity_tasks + pricing_estimate_new)
        foreach ($activities as &$act) {
            if (empty($act['tasks'])) {
                $actId = (int)($act['activity_id'] ?? 0);

                // Get estimated quantity
                $pe = $db->createCommand(
                    "SELECT activity_qty FROM pricing_estimate_new WHERE activity_Id=:id AND project_Id=:pid AND pricing_status=0 LIMIT 1",
                    [':id' => $actId, ':pid' => $projectid]
                )->queryOne();
                $actQty = $pe ? (float)$pe['activity_qty'] : 0;

                // Get master activity_Id
                $wbn = $db->createCommand(
                    "SELECT activity_Id FROM workgroup_activities_new WHERE id=:id", [':id' => $actId]
                )->queryOne();
                $masterActId = $wbn ? (int)$wbn['activity_Id'] : $actId;

                $taskRows = $db->createCommand(
                    "SELECT id, task_name, task_unit FROM activity_tasks WHERE activity_id=:id ORDER BY sort_order ASC",
                    [':id' => $masterActId]
                )->queryAll();

                $hydratedTasks = [];
                foreach ($taskRows as $tr) {
                    $stRow = $db->createCommand(
                        "SELECT task_qty FROM schedule_task_new WHERE activity_Id=(SELECT id FROM scheduleactivities WHERE activity_id=:aid AND projectId=:pid AND status=0 LIMIT 1) AND task_Id=:tid LIMIT 1",
                        [':aid' => $actId, ':pid' => $projectid, ':tid' => $tr['id']]
                    )->queryOne();
                    $savedRate = $db->createCommand(
                        "SELECT rate, task_qty, selected FROM wo_task_rates WHERE activity_id=:aid AND task_id=:tid LIMIT 1",
                        [':aid' => $actId, ':tid' => $tr['id']]
                    )->queryOne();

                    // skip tasks explicitly deselected by the user
                    if ($savedRate && isset($savedRate['selected']) && (int)$savedRate['selected'] === 0) continue;

                    $taskQty  = $savedRate && $savedRate['task_qty'] > 0 ? (float)$savedRate['task_qty']
                              : ($stRow && $stRow['task_qty'] > 0 ? (float)$stRow['task_qty'] : 0);
                    $rate     = $savedRate ? (float)$savedRate['rate'] : 0;
                    $approx   = $actQty * $taskQty;
                    $hydratedTasks[] = [
                        'task_id'   => (int)$tr['id'],
                        'task_name' => $tr['task_name'],
                        'task_unit' => $tr['task_unit'],
                        'task_qty'  => $taskQty,
                        'approx_qty'=> $approx,
                        'rate'      => $rate,
                        'amount'    => $rate * $approx,
                    ];
                }
                $act['tasks'] = $hydratedTasks;
                if (empty($act['qty'])) $act['qty'] = $actQty;
            }

            foreach (($act['tasks'] ?? []) as $task) {
                $grandTotal += (float)($task['amount'] ?? 0);
            }
        }
        unset($act);

        $today = date('Y-m-d');
        $db->createCommand()->insert('work_order', [
            'Project_Id'       => $projectid,
            'WBS_Id'           => 0,
            'IOW_Id'           => 0,
            'Scope'            => $scope ?: ($subject ?: ('Work Order ' . $ordernumber)),
            'Item_id'          => 0,
            'Unit'             => '',
            'Quantity'         => '',
            'Rate'             => '',
            'Total'            => (string)$grandTotal,
            'Taxes'            => '',
            'Duration'         => $leadTime,
            'start_date'       => $startDate,
            'payment_cycle'    => $paymentCycle,
            'Terms'            => $termsJson,
            'Date_requested'   => $today,
            'User_Id'          => (int)$uid,
            'WO_Status'        => 2,
            'WO_Address'       => $shipTo,
            'WO_tax'           => 0,
            'WO_Vendor'        => $vendorId,
            'WO_Approved_Name' => '',
            'WO_Designation'   => '',
            'WO_retention'     => 0,
            'WO_tds'           => 0,
            'WO_vat'           => 0,
            'WO_Number'        => $ordernumber,
            'WO_Subject'       => json_encode($activities),
            'WO_Function'      => 0,
            'WO_Folder'        => 0,
            'WO_assigned'      => 0,
            'WO_desp_no'       => 0,
            'WO_desp_date'     => $today,
        ])->execute();

        $vendor = $db->createCommand(
            'SELECT Email FROM vendors WHERE Vendor_Id = :vid', [':vid' => $vendorId]
        )->queryOne();

        $woHtml = $this->buildWoHtml($ordernumber);

        return json_encode([
            'error'        => 'No',
            'html'         => $woHtml,
            'order_id'     => $ordernumber,
            'ordernumber'  => $ordernumber,
            'vendor_email' => $vendor['Email'] ?? '',
        ]);
    }

    private function buildWoHtml($ordernumber)
    {
        $db = Yii::$app->db;
        $wo = $db->createCommand(
            'SELECT wo.*, v.Name AS vendor_name, v.Email AS vendor_email,
                    v.Address AS vendor_address,
                    p.Name AS project_name, p.location AS project_location
             FROM work_order wo
             LEFT JOIN vendors v ON v.Vendor_Id = wo.WO_Vendor
             LEFT JOIN projects p ON p.Project_Id = wo.Project_Id
             WHERE wo.WO_Number = :num', [':num' => $ordernumber]
        )->queryOne();
        if (!$wo) return '<p>Work order not found.</p>';

        $activities = json_decode($wo['WO_Subject'] ?? '[]', true) ?: [];
        $terms      = json_decode($wo['Terms']      ?? '[]', true) ?: [];
        $grandTotal = 0;

        $td  = 'padding:7px 10px;border:1px solid #999;font-size:12px;vertical-align:middle;';
        $tdR = $td . 'text-align:right;';
        $tdC = $td . 'text-align:center;';
        $thS = 'padding:7px 10px;border:1px solid #666;background:#888;color:#fff;font-size:11px;font-weight:700;';

        $itemsHtml = '';
        foreach ($activities as $actIdx => $act) {
            $actTotal = 0;
            foreach (($act['tasks'] ?? []) as $t) { $actTotal += (float)($t['amount'] ?? 0); }
            $grandTotal += $actTotal;

            // Spacer between activities (not before the first)
            if ($actIdx > 0) {
                $itemsHtml .= '<tr><td colspan="6" style="padding:0;height:10px;border:none;background:#f0f0f0;"></td></tr>';
            }

            // Activity detail row
            $itemsHtml .= '<tr style="background:#e8ecf0;">'
                . '<td colspan="6" style="' . $td . 'font-weight:700;font-size:12px;padding:8px 12px;">'
                . htmlspecialchars($act['activity_name'] ?? '') . ' &nbsp;&mdash;&nbsp; ' . htmlspecialchars($act['unit'] ?? '')
                . '<span style="float:right;font-weight:400;font-size:11px;color:#555;">Est. Qty: ' . number_format((float)($act['qty'] ?? 0), 2) . '</span>'
                . '</td></tr>';

            // Column headers for this activity's tasks
            $itemsHtml .= '<tr>'
                . '<th style="' . $thS . 'width:32px;text-align:center;">#</th>'
                . '<th style="' . $thS . '">Task</th>'
                . '<th style="' . $thS . 'width:70px;text-align:center;">Unit</th>'
                . '<th style="' . $thS . 'width:110px;text-align:right;">Approx. Qty</th>'
                . '<th style="' . $thS . 'width:90px;text-align:right;">Rate</th>'
                . '<th style="' . $thS . 'width:110px;text-align:right;">Amount</th>'
                . '</tr>';

            // Task rows
            $taskNum = 0;
            foreach (($act['tasks'] ?? []) as $task) {
                $taskNum++;
                $bg = $taskNum % 2 === 0 ? 'background:#f9f9f9;' : 'background:#fff;';
                $itemsHtml .= '<tr style="' . $bg . '">'
                    . '<td style="' . $tdC . 'color:#999;">' . $taskNum . '</td>'
                    . '<td style="' . $td . '">' . htmlspecialchars($task['task_name'] ?? '') . '</td>'
                    . '<td style="' . $tdC . '">' . htmlspecialchars($task['task_unit'] ?? '') . '</td>'
                    . '<td style="' . $tdR . '">' . number_format((float)($task['approx_qty'] ?? 0), 3) . '</td>'
                    . '<td style="' . $tdR . '">' . number_format((float)($task['rate'] ?? 0), 2) . '</td>'
                    . '<td style="' . $tdR . 'font-weight:600;">' . number_format((float)($task['amount'] ?? 0), 2) . '</td>'
                    . '</tr>';
            }
        }

        $termsHtml = '';
        if (!empty($terms)) {
            $termsHtml = '<ol style="margin:0;padding-left:18px;">';
            foreach ($terms as $t) {
                $termsHtml .= '<li style="font-size:12px;color:#555;margin-bottom:3px;">' . htmlspecialchars($t) . '</li>';
            }
            $termsHtml .= '</ol>';
        }

        $h  = '<div style="font-family:Arial,sans-serif;max-width:860px;margin:0 auto;border:1px solid #888;font-size:13px;">';

        // Header — same design as PO: grey background, red emblem left, title right
        $h .= '<table style="width:100%;border-collapse:collapse;background:#909090;">'
            . '<tr>'
            . '<td style="padding:12px 18px;width:45%;">'
            . '<div style="display:inline-block;background:#b53535;border-radius:50px;padding:7px 20px;vertical-align:middle;">'
            . '<span style="color:#fff;font-size:13px;font-weight:700;letter-spacing:1.5px;font-family:Arial,sans-serif;">GEO-TECH</span>'
            . '</div>'
            . '</td>'
            . '<td style="padding:12px 18px;text-align:right;">'
            . '<div style="color:#fff;font-size:20px;font-weight:700;letter-spacing:1px;">WORK ORDER</div>'
            . '<div style="color:#e8e8e8;font-size:11px;margin-top:3px;">GEO-TECH OFFSHORE STRUCTURES.(P) LTD</div>'
            . '</td>'
            . '</tr></table>';

        // WO No + Date bar
        $h .= '<table style="width:100%;border-collapse:collapse;border-bottom:2px solid #909090;background:#f8f8f8;">'
            . '<tr>'
            . '<td style="padding:8px 18px;font-size:12px;color:#333;"><strong>WO No:</strong> ' . htmlspecialchars($wo['WO_Number']) . '</td>'
            . '<td style="padding:8px 18px;font-size:12px;color:#333;text-align:right;"><strong>Date:</strong> ' . date('d M Y', strtotime($wo['Date_requested'] ?? 'now')) . '</td>'
            . '</tr></table>';

        $h .= '<table style="width:100%;border-collapse:collapse;border-bottom:1px solid #999;">'
            . '<tr>'
            . '<td style="width:50%;padding:14px 18px;vertical-align:top;border-right:2px solid #999;">'
            . '<div style="font-size:10px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;">Vendor</div>'
            . '<div style="font-size:13px;font-weight:600;color:#222;">' . htmlspecialchars($wo['vendor_name'] ?? '') . '</div>'
            . (!empty($wo['vendor_address']) ? '<div style="font-size:12px;color:#555;margin-top:4px;">' . htmlspecialchars($wo['vendor_address']) . '</div>' : '')
            . '</td>'
            . '<td style="padding:14px 18px;vertical-align:top;">'
            . '<div style="font-size:10px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;">Project Name</div>'
            . '<div style="font-size:13px;font-weight:600;color:#222;">' . htmlspecialchars($wo['project_name'] ?? '') . '</div>'
            . (!empty($wo['project_location']) ? '<div style="font-size:12px;color:#555;margin-top:4px;">' . htmlspecialchars($wo['project_location']) . '</div>' : '')
            . '</td></tr>'
            . (!empty($wo['Scope']) ? '<tr><td colspan="2" style="padding:8px 18px;font-size:12px;border-top:1px solid #999;"><strong>Scope of Work:</strong> <span style="white-space:pre-line;">' . htmlspecialchars($wo['Scope']) . '</span></td></tr>' : '')
            . '</table>';

        // Start Date / Duration / Payment Cycle row — just above activities
        $metaRow = '';
        if (!empty($wo['start_date']) || !empty($wo['Duration']) || !empty($wo['payment_cycle'])) {
            $metaRow .= '<table style="width:100%;border-collapse:collapse;border-bottom:1px solid #999;">'
                . '<tr>';
            if (!empty($wo['start_date'])) {
                $metaRow .= '<td style="padding:8px 18px;font-size:12px;color:#333;border-right:1px solid #999;">'
                    . '<strong>Start Date:</strong> ' . htmlspecialchars(date('d M Y', strtotime($wo['start_date']))) . '</td>';
            }
            if (!empty($wo['Duration'])) {
                $metaRow .= '<td style="padding:8px 18px;font-size:12px;color:#333;border-right:1px solid #999;">'
                    . '<strong>Duration:</strong> ' . htmlspecialchars($wo['Duration']) . '</td>';
            }
            if (!empty($wo['payment_cycle'])) {
                $metaRow .= '<td style="padding:8px 18px;font-size:12px;color:#333;">'
                    . '<strong>Payment Cycle:</strong> ' . htmlspecialchars($wo['payment_cycle']) . '</td>';
            }
            $metaRow .= '</tr></table>';
        }
        $h .= $metaRow;

        $h .= '<table style="width:100%;border-collapse:collapse;margin-top:12px;">'
            . '<tbody>' . $itemsHtml
            . '<tr style="background:#efefef;border-top:2px solid #909090;">'
            .   '<td colspan="5" style="padding:9px 12px;border:1px solid #888;font-size:12px;font-weight:700;color:#555;text-align:right;">Total Amount</td>'
            .   '<td style="padding:9px 12px;border:1px solid #888;font-size:13px;font-weight:700;color:#333;text-align:right;width:110px;">' . number_format($grandTotal, 2) . '</td>'
            . '</tr>'
            . '</tbody></table>';

        // Signatory (left) + Terms (right) side by side — same layout as PO
        $termsColHtml = !empty($termsHtml)
            ? '<div style="font-size:10px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;">Terms &amp; Conditions</div>' . $termsHtml
            : '';

        $h .= '<table style="width:100%;border-collapse:collapse;border-top:1px solid #999;">'
            . '<tr>'
            . '<td style="width:45%;padding:20px 18px;vertical-align:bottom;border-right:2px solid #999;">'
            . '<div style="font-size:12px;color:#333;margin-bottom:24px;">For GEO-TECH OFFSHORE STRUCTURES.(P) LTD</div>'
            . '<div style="display:inline-block;border-top:1px solid #333;width:180px;padding-top:4px;font-size:11px;color:#555;">Authorized Signatory</div>'
            . '</td>'
            . '<td style="width:55%;padding:14px 18px 20px;vertical-align:top;">'
            . $termsColHtml
            . '</td>'
            . '</tr></table>';

        $h .= '</div>';
        return $h;
    }

    private function numberToWords($number)
    {
        $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine',
                 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen',
                 'Seventeen', 'Eighteen', 'Nineteen'];
        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

        $convert = function($n) use ($ones, $tens, &$convert) {
            if ($n === 0) return '';
            if ($n < 20)  return $ones[$n] . ' ';
            if ($n < 100) return $tens[(int)($n / 10)] . ' ' . ($n % 10 ? $ones[$n % 10] . ' ' : '');
            return $ones[(int)($n / 100)] . ' Hundred ' . $convert($n % 100);
        };

        $number   = round((float)$number, 2);
        $rupees   = (int)floor($number);
        $paise    = (int)round(($number - $rupees) * 100);

        $words = '';
        if ($rupees === 0) {
            $words = 'Zero';
        } else {
            $crore  = (int)floor($rupees / 10000000);
            $lakh   = (int)floor(($rupees % 10000000) / 100000);
            $thou   = (int)floor(($rupees % 100000) / 1000);
            $rem    = $rupees % 1000;

            if ($crore) $words .= $convert($crore) . 'Crore ';
            if ($lakh)  $words .= $convert($lakh)  . 'Lakh ';
            if ($thou)  $words .= $convert($thou)  . 'Thousand ';
            if ($rem)   $words .= $convert($rem);
        }

        $words = trim($words);
        $result = 'Rupees ' . $words;
        if ($paise > 0) {
            $result .= ' and ' . trim($convert($paise)) . ' Paise';
        }
        return $result . ' Only';
    }

    public function actionEmailwo()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);

        $orderId = (int)($_POST['order_id'] ?? 0);
        $to      = trim($_POST['to']      ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $body    = trim($_POST['body']    ?? '');

        if (!$orderId) return json_encode(['error' => 'Yes', 'errortext' => 'Invalid order.']);
        if (!$to)      return json_encode(['error' => 'Yes', 'errortext' => 'Recipient email is required.']);

        $db = Yii::$app->db;
        $wo = $db->createCommand(
            'SELECT WO_Number FROM work_order WHERE WO_Number = :num AND Project_Id = :pid',
            [':num' => $orderId, ':pid' => $projuser->projectid]
        )->queryOne();
        if (!$wo) return json_encode(['error' => 'Yes', 'errortext' => 'Work order not found.']);

        if (!$subject) $subject = 'Work Order ' . $wo['WO_Number'];
        if (!$body)    $body    = 'Please find the Work Order attached.';

        $woHtml = $this->buildWoHtml($wo['WO_Number']);

        try {
            $pdf  = new Pdf();
            $mpdf = $pdf->api;
            $mpdf->WriteHtml($woHtml);
            $pdfContent = $mpdf->Output('', 'S');
        } catch (\Exception $e) {
            return json_encode(['error' => 'Yes', 'errortext' => 'Failed to generate PDF: ' . $e->getMessage()]);
        }

        try {
            Yii::$app->mailer->compose()
                ->setFrom(['procurement@geotech.net.in' => 'GEO-TECH Procurement'])
                ->setTo($to)
                ->setSubject($subject)
                ->setHtmlBody(nl2br(htmlspecialchars($body, ENT_QUOTES)))
                ->attachContent($pdfContent, [
                    'fileName'    => 'WO-' . $wo['WO_Number'] . '.pdf',
                    'contentType' => 'application/pdf',
                ])
                ->send();
        } catch (\Exception $e) {
            return json_encode(['error' => 'Yes', 'errortext' => 'Failed to send email: ' . $e->getMessage()]);
        }

        // Promote draft to issued now that the email was sent
        $db->createCommand()->update(
            'work_order', ['WO_Status' => 0],
            ['WO_Number' => $wo['WO_Number'], 'WO_Status' => 2]
        )->execute();

        return json_encode(['error' => 'No']);
    }

    public function actionIssuedworkorders()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);

        $projectid = $projuser->projectid;
        $db = Yii::$app->db;

        $sql = "SELECT wo.WO_Id, wo.WO_Number, wo.Date_requested, wo.WO_Status,
                       v.Name AS vendor_name, v.Email AS vendor_email
                FROM work_order wo
                JOIN vendors v ON v.Vendor_Id = wo.WO_Vendor
                WHERE wo.Project_Id = :pid AND wo.WO_Status IN (0, 1)
                ORDER BY wo.Date_requested DESC, wo.WO_Id DESC";

        $rows = $db->createCommand($sql, [':pid' => $projectid])->queryAll();
        return json_encode(['error' => 'No', 'rows' => $rows]);
    }

    public function actionCancelwo()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);

        $projectid = $projuser->projectid;
        $db = Yii::$app->db;
        $wo_id = (int)($_POST['wo_id'] ?? 0);

        $wo = $db->createCommand(
            'SELECT WO_Id FROM work_order WHERE WO_Id = :id AND Project_Id = :pid AND WO_Status = 0',
            [':id' => $wo_id, ':pid' => $projectid]
        )->queryOne();

        if (!$wo) return json_encode(['error' => 'Yes', 'errortext' => 'Work order not found or already cancelled.']);

        $db->createCommand()->update('work_order', ['WO_Status' => 1], ['WO_Id' => $wo_id])->execute();
        return json_encode(['error' => 'No']);
    }

    public function actionRecoverwo()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);

        $db    = Yii::$app->db;
        $wo_id = (int)($_POST['wo_id'] ?? 0);

        $wo = $db->createCommand(
            'SELECT WO_Id FROM work_order WHERE WO_Id = :id AND Project_Id = :pid AND WO_Status = 1',
            [':id' => $wo_id, ':pid' => $projuser->projectid]
        )->queryOne();

        if (!$wo) return json_encode(['error' => 'Yes', 'errortext' => 'Cancelled work order not found.']);

        $db->createCommand()->update('work_order', ['WO_Status' => 0], ['WO_Id' => $wo_id])->execute();
        return json_encode(['error' => 'No']);
    }

    public function actionIssueworkorder()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);

        $ordernumber = trim($_POST['order_id'] ?? '');
        if (!$ordernumber) return json_encode(['error' => 'Yes', 'errortext' => 'Invalid order.']);

        Yii::$app->db->createCommand()->update(
            'work_order', ['WO_Status' => 0],
            ['WO_Number' => $ordernumber, 'WO_Status' => 2]
        )->execute();

        return json_encode(['error' => 'No']);
    }

    public function actionDiscardworkorder()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);

        $ordernumber = trim($_POST['order_id'] ?? '');
        if (!$ordernumber) return json_encode(['error' => 'No']);

        Yii::$app->db->createCommand(
            'DELETE FROM work_order WHERE WO_Number = :num AND WO_Status = 2',
            [':num' => $ordernumber]
        )->execute();

        return json_encode(['error' => 'No']);
    }

    public function actionSendwocancellation()
    {
        $uid      = Yii::$app->user->id;
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if (!$projuser) return json_encode(['error' => 'Yes', 'errortext' => 'No project selected.']);

        $projectid = $projuser->projectid;
        $db = Yii::$app->db;
        $wo_id = (int)($_POST['wo_id'] ?? 0);
        $body  = trim($_POST['body'] ?? '');

        $wo = $db->createCommand(
            'SELECT wo.WO_Number, wo.Date_requested, v.Email AS vendor_email
             FROM work_order wo
             JOIN vendors v ON v.Vendor_Id = wo.WO_Vendor
             WHERE wo.WO_Id = :id AND wo.Project_Id = :pid',
            [':id' => $wo_id, ':pid' => $projectid]
        )->queryOne();

        if (!$wo) return json_encode(['error' => 'Yes', 'errortext' => 'Work order not found.']);
        if (empty($wo['vendor_email'])) return json_encode(['error' => 'Yes', 'errortext' => 'Vendor email not found.']);

        $subject = 'Cancellation Notice — Work Order ' . $wo['WO_Number'];

        try {
            Yii::$app->mailer->compose()
                ->setFrom(['procurement@geotech.net.in' => 'GEO-TECH Procurement'])
                ->setTo($wo['vendor_email'])
                ->setSubject($subject)
                ->setHtmlBody(nl2br(htmlspecialchars($body, ENT_QUOTES)))
                ->send();
        } catch (\Exception $e) {
            return json_encode(['error' => 'Yes', 'errortext' => 'Failed to send email: ' . $e->getMessage()]);
        }

        return json_encode(['error' => 'No']);
    }

}
