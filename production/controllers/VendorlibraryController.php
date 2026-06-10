<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;

class VendorlibraryController extends Controller
{
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    // ─── Vendor Types ────────────────────────────────────────────────────────

    public function actionSearch()
    {
        $db     = Yii::$app->db;
        $search = trim($_POST['vtypename'] ?? '');
        $sql    = 'SELECT vendor_type_id, name FROM vendortypes';
        $params = [];
        if ($search !== '') {
            $sql .= ' WHERE name LIKE :s';
            $params[':s'] = '%' . $search . '%';
        }
        $sql .= ' ORDER BY name ASC';
        $rows = $db->createCommand($sql, $params)->queryAll();

        if (empty($rows)) {
            $html = '<p style="color:#aaa;font-size:13px;text-align:center;padding:30px 0;">No vendor types found.</p>';
            return json_encode(['error' => 'No', 'result' => $html]);
        }

        $html = '<table style="width:100%;border-collapse:collapse;font-size:13px;">'
              . '<thead><tr>'
              . '<th style="background:#555;color:#fff;font-weight:bold;padding:7px 10px;border:1px solid #444;text-align:center;width:36px;">#</th>'
              . '<th style="background:#555;color:#fff;font-weight:bold;padding:7px 10px;border:1px solid #444;">Vendor Type</th>'
              . '<th style="background:#555;color:#fff;font-weight:bold;padding:7px 10px;border:1px solid #444;text-align:center;width:80px;"></th>'
              . '</tr></thead><tbody>';

        foreach ($rows as $i => $row) {
            $safeName = htmlspecialchars($row['name'], ENT_QUOTES);
            $id       = (int)$row['vendor_type_id'];
            $html .= '<tr id="vtrow' . $id . '">'
                   . '<td style="padding:6px 10px;border:1px solid #ddd;text-align:center;color:#999;">' . ($i + 1) . '</td>'
                   . '<td style="padding:6px 10px;border:1px solid #ddd;">' . $safeName
                   .     '<input type="hidden" id="vtname' . $id . '" value="' . $safeName . '"></td>'
                   . '<td style="padding:4px 8px;border:1px solid #ddd;text-align:center;white-space:nowrap;">'
                   .     '<button type="button" class="btn btn-default btn-xs" data-id="' . $id . '" onclick="openEditVendorType(this)" '
                   .         'style="border-radius:50%;width:28px;height:28px;padding:0;margin-right:4px;"><span class="icon-pencil"></span></button>'
                   .     '<button type="button" class="delvtypebutton btn btn-danger btn-xs" data-id="' . $id . '" '
                   .         'style="border-radius:50%;width:28px;height:28px;padding:0;background-color:#d9534f!important;border-color:#d43f3a!important;color:#fff!important;">'
                   .         '<span class="icon-trash1"></span></button>'
                   . '</td></tr>';
        }
        $html .= '</tbody></table>';

        return json_encode(['error' => 'No', 'result' => $html]);
    }

    public function actionCreate()
    {
        $name = trim($_POST['vtypename'] ?? '');
        if ($name === '') {
            return json_encode(['error' => 'Yes', 'errortext' => 'Vendor type name is required.']);
        }
        $db = Yii::$app->db;
        $db->createCommand()->insert('vendortypes', ['name' => $name])->execute();
        $id = $db->getLastInsertID();
        return json_encode(['error' => 'No', 'Id' => $id, 'Name' => $name]);
    }

    public function actionUpdate()
    {
        $id   = (int)($_POST['vtypeid'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        if (!$id || $name === '') {
            return json_encode(['error' => 'Yes', 'errortext' => 'Invalid data.']);
        }
        Yii::$app->db->createCommand()
            ->update('vendortypes', ['name' => $name], ['vendor_type_id' => $id])
            ->execute();
        return json_encode(['error' => 'No']);
    }

    public function actionDeleteitem()
    {
        $id = (int)($_POST['vtypeid'] ?? 0);
        if (!$id) {
            return json_encode(['error' => 'Yes', 'errortext' => 'Invalid id.']);
        }
        Yii::$app->db->createCommand()
            ->delete('vendortypes', ['vendor_type_id' => $id])
            ->execute();
        return json_encode(['error' => 'No', 'Id' => $id]);
    }

    // ─── Vendors ─────────────────────────────────────────────────────────────

    public function actionGetresources()
    {
        $typeId = (int)($_POST['resource_type_id'] ?? 0);
        $rows   = Yii::$app->db->createCommand(
            'SELECT Resource_Id, Name FROM resources WHERE ResourceType_Id = :tid AND Status = 0 ORDER BY Name ASC',
            [':tid' => $typeId]
        )->queryAll();
        return json_encode(['error' => 'No', 'resources' => $rows]);
    }

    public function actionVendors()
    {
        $db         = Yii::$app->db;
        $search     = trim($_POST['vendorname']       ?? '');
        $resTypeId  = (int)($_POST['res_type_id']     ?? 0);
        $resId      = (int)($_POST['res_id']          ?? 0);

        $sql    = 'SELECT v.Vendor_Id, v.Name, v.contact_person, v.Address, v.Phone, v.Email,
                          v.resource_type_id, v.resource_id,
                          vt.name AS vendor_type_name, vt.vendor_type_id,
                          rt.Name AS res_type_name, r.Name AS res_name
                   FROM vendors v
                   LEFT JOIN vendortypes vt ON v.vendor_type = vt.vendor_type_id
                   LEFT JOIN resourcetype rt ON v.resource_type_id = rt.ResourceType_Id
                   LEFT JOIN resources r ON v.resource_id = r.Resource_Id
                   WHERE v.Status = 0';
        $params = [];
        if ($resTypeId) {
            $sql .= ' AND v.resource_type_id = :rtid';
            $params[':rtid'] = $resTypeId;
        }
        if ($resId) {
            $sql .= ' AND v.resource_id = :rid';
            $params[':rid'] = $resId;
        }
        if ($search !== '') {
            $sql .= ' AND v.Name LIKE :s';
            $params[':s'] = '%' . $search . '%';
        }
        $sql .= ' ORDER BY rt.Name ASC, r.Name ASC, v.Name ASC';
        $rows = $db->createCommand($sql, $params)->queryAll();

        $tdStyle = 'style="padding:6px 10px;border:1px solid #ddd;font-size:13px;"';

        if (empty($rows)) {
            $html = '<tr><td colspan="8" style="text-align:center;padding:30px;color:#aaa;font-size:13px;border:1px solid #eee;">No vendors found.</td></tr>';
            return json_encode(['error' => 'No', 'result' => $html]);
        }

        // Group by resource type
        $grouped = [];
        foreach ($rows as $row) {
            $key = $row['res_type_name'] ?? 'Unassigned';
            $grouped[$key][] = $row;
        }

        $html = '';
        foreach ($grouped as $resTypeName => $vendors) {
            // Group sub-header when multiple resource types shown
            if (count($grouped) > 1) {
                $html .= '<tr style="background:#e8ecf0;">'
                       . '<td colspan="8" style="padding:6px 12px;font-weight:700;font-size:12px;color:#333;border:1px solid #c5ccd4;">'
                       . htmlspecialchars($resTypeName, ENT_QUOTES) . '</td></tr>';
            }

            foreach ($vendors as $i => $row) {
                $id        = (int)$row['Vendor_Id'];
                $typeId    = (int)($row['vendor_type_id']   ?? 0);
                $rTypeId   = (int)($row['resource_type_id'] ?? 0);
                $rId       = (int)($row['resource_id']      ?? 0);
                $html .= '<tr id="vendorrow' . $id . '">'
                       . '<td ' . $tdStyle . ' style="padding:6px 10px;border:1px solid #ddd;font-size:13px;text-align:center;color:#999;">' . ($i + 1) . '</td>'
                       . '<td ' . $tdStyle . '>' . htmlspecialchars($row['res_name'] ?? '', ENT_QUOTES) . '</td>'
                       . '<td ' . $tdStyle . '>' . htmlspecialchars($row['Name'], ENT_QUOTES) . '</td>'
                       . '<td ' . $tdStyle . '>' . htmlspecialchars($row['Address'], ENT_QUOTES) . '</td>'
                       . '<td ' . $tdStyle . '>' . htmlspecialchars($row['contact_person'], ENT_QUOTES) . '</td>'
                       . '<td ' . $tdStyle . '>' . htmlspecialchars($row['Phone'], ENT_QUOTES) . '</td>'
                       . '<td ' . $tdStyle . '>' . htmlspecialchars($row['Email'], ENT_QUOTES) . '</td>'
                       . '<td style="padding:4px 8px;border:1px solid #ddd;text-align:center;white-space:nowrap;">'
                       .     '<button type="button" class="btn btn-default btn-xs editvendorbutton" data-id="' . $id . '" '
                       .         'style="border-radius:50%;width:28px;height:28px;padding:0;margin-right:4px;"><span class="icon-pencil"></span></button>'
                       .     '<button type="button" class="delvendorbutton btn btn-danger btn-xs" data-id="' . $id . '" '
                       .         'style="border-radius:50%;width:28px;height:28px;padding:0;background-color:#d9534f!important;border-color:#d43f3a!important;color:#fff!important;">'
                       .         '<span class="icon-trash1"></span></button>'
                       . '</td>'
                       . '<td style="display:none;" id="vdata-name-'    . $id . '">' . htmlspecialchars($row['Name'], ENT_QUOTES) . '</td>'
                       . '<td style="display:none;" id="vdata-type-'    . $id . '">' . $typeId . '</td>'
                       . '<td style="display:none;" id="vdata-addr-'    . $id . '">' . htmlspecialchars($row['Address'], ENT_QUOTES) . '</td>'
                       . '<td style="display:none;" id="vdata-contact-' . $id . '">' . htmlspecialchars($row['contact_person'], ENT_QUOTES) . '</td>'
                       . '<td style="display:none;" id="vdata-phone-'   . $id . '">' . htmlspecialchars($row['Phone'], ENT_QUOTES) . '</td>'
                       . '<td style="display:none;" id="vdata-email-'   . $id . '">' . htmlspecialchars($row['Email'], ENT_QUOTES) . '</td>'
                       . '<td style="display:none;" id="vdata-restype-' . $id . '">' . $rTypeId . '</td>'
                       . '<td style="display:none;" id="vdata-resid-'   . $id . '">' . $rId . '</td>'
                       . '</tr>';
            }
        }

        return json_encode(['error' => 'No', 'result' => $html]);
    }

    public function actionCreatevendor()
    {
        $name       = trim($_POST['vname']    ?? '');
        $address    = trim($_POST['vaddress'] ?? '');
        $contact    = trim($_POST['vcontact'] ?? '');
        $phone      = trim($_POST['vphone']   ?? '');
        $email      = trim($_POST['vemail']   ?? '');
        $resTypeId  = (int)($_POST['vrestype'] ?? 0);
        $resId      = (int)($_POST['vresid']   ?? 0);

        if ($name === '') {
            return json_encode(['error' => 'Yes', 'errortext' => 'Vendor name is required.']);
        }

        $db = Yii::$app->db;
        $db->createCommand()->insert('vendors', [
            'Name'             => $name,
            'contact_person'   => $contact,
            'Address'          => $address,
            'City'             => '',
            'Phone'            => $phone,
            'Email'            => $email,
            'vendor_type'      => 0,
            'resource_type_id' => $resTypeId,
            'resource_id'      => $resId,
            'Added_On'         => date('Y-m-d H:i:s'),
            'Added_By'         => Yii::$app->user->id,
            'Status'           => 0,
            'account_id'       => 0,
            'vendor_groupid'   => 0,
            'Brand'            => '',
        ])->execute();

        return json_encode(['error' => 'No']);
    }

    public function actionUpdatevendor()
    {
        $id        = (int)($_POST['vid']      ?? 0);
        $name      = trim($_POST['vname']     ?? '');
        $address   = trim($_POST['vaddress']  ?? '');
        $contact   = trim($_POST['vcontact']  ?? '');
        $phone     = trim($_POST['vphone']    ?? '');
        $email     = trim($_POST['vemail']    ?? '');
        $resTypeId = (int)($_POST['vrestype'] ?? 0);
        $resId     = (int)($_POST['vresid']   ?? 0);

        if (!$id || $name === '') {
            return json_encode(['error' => 'Yes', 'errortext' => 'Invalid data.']);
        }

        Yii::$app->db->createCommand()->update('vendors', [
            'Name'             => $name,
            'contact_person'   => $contact,
            'Address'          => $address,
            'Phone'            => $phone,
            'Email'            => $email,
            'vendor_type'      => 0,
            'resource_type_id' => $resTypeId,
            'resource_id'      => $resId,
        ], ['Vendor_Id' => $id])->execute();

        return json_encode(['error' => 'No']);
    }

    public function actionDeletevendor()
    {
        $id = (int)($_POST['vid'] ?? 0);
        if (!$id) {
            return json_encode(['error' => 'Yes', 'errortext' => 'Invalid id.']);
        }
        Yii::$app->db->createCommand()
            ->update('vendors', ['Status' => 1], ['Vendor_Id' => $id])
            ->execute();
        return json_encode(['error' => 'No', 'Id' => $id]);
    }
}
