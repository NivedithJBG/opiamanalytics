<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Resources;

class ResourcesController extends Controller
{
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionSearch()
    {
        $connection = \Yii::$app->db;
        $search = isset($_POST['resourcename']) ? trim($_POST['resourcename']) : '';
        $typeId = isset($_POST['restypeid'])    ? (int)$_POST['restypeid']    : 0;
        $sql = "SELECT r.Resource_Id, r.Name, r.Unit, r.ResourceType_Id, r.Resource_group_Id, r.sortorder,
                       rt.Name AS TypeName, rg.Resource_group_Name AS GroupName
                FROM resources r
                LEFT JOIN resourcetype rt ON rt.ResourceType_Id = r.ResourceType_Id
                LEFT JOIN resource_group rg ON rg.Resource_group_Id = r.Resource_group_Id
                WHERE r.Status = 0";
        if ($typeId > 0) {
            $sql .= " AND r.ResourceType_Id = " . $typeId;
        }
        if ($search !== '') {
            $sql .= " AND r.Name LIKE '%" . addslashes($search) . "%'";
        }
        $sql .= " ORDER BY rt.Name ASC, rg.Resource_group_Name ASC, r.sortorder ASC, r.Resource_Id ASC";
        $dataProvider = $connection->createCommand($sql)->queryAll();

        if (empty($dataProvider)) {
            $resource = '<p style="color:#aaa;font-size:13px;text-align:center;padding:30px 0;">No resources found.</p>';
            return json_encode(['error' => 'No', 'resource' => $resource]);
        }

        // Group by Resource Type → Resource Group
        $grouped = [];
        foreach ($dataProvider as $row) {
            $type  = $row['TypeName']  ?: 'Unassigned';
            $group = $row['GroupName'] ?: 'Unassigned';
            $grouped[$type][$group][] = $row;
        }

        $resource = '<table style="width:100%;border-collapse:collapse;font-size:13px;">'
                  . '<thead><tr>'
                  . '<th style="background:#555;color:#fff;font-weight:bold;padding:7px 10px;border:1px solid #444;text-align:center;width:36px;">#</th>'
                  . '<th style="background:#555;color:#fff;font-weight:bold;padding:7px 10px;border:1px solid #444;">Resource</th>'
                  . '<th style="background:#555;color:#fff;font-weight:bold;padding:7px 10px;border:1px solid #444;width:80px;">Unit</th>'
                  . '<th style="background:#555;color:#fff;font-weight:bold;padding:7px 10px;border:1px solid #444;text-align:center;width:90px;"></th>'
                  . '</tr></thead>';

        $rowIndex = 1;
        foreach ($grouped as $typeName => $groups) {
            $safeType = htmlspecialchars($typeName, ENT_QUOTES);
            $resource .= '<tbody>'
                       . '<tr><td colspan="4" style="background:#e8e8e8;color:#444;font-weight:bold;padding:8px 12px;border:1px solid #ccc;font-size:12px;letter-spacing:0.5px;">'
                       . '<span class="icon-settings" style="margin-right:6px;"></span>' . $safeType
                       . '</td></tr>';

            foreach ($groups as $groupName => $rows) {
                $safeGroup = htmlspecialchars($groupName, ENT_QUOTES);
                $resource .= '<tr><td colspan="4" style="background:#e8f0f7;color:#2c3e50;font-weight:600;padding:6px 12px 6px 28px;border:1px solid #ccd9e8;font-size:12px;">'
                           . '<span class="icon-folder" style="margin-right:5px;color:#5b8ab5;"></span>' . $safeGroup
                           . '</td></tr>';

                foreach ($rows as $data) {
                    $safeName  = htmlspecialchars($data['Name'], ENT_QUOTES);
                    $safeUnit  = htmlspecialchars($data['Unit'] ?? '', ENT_QUOTES);

                    $resource .= '<tr class="resourcesort resource' . $data['Resource_Id'] . '" '
                               .     'data-id="' . $data['Resource_Id'] . '" '
                               .     'id="resourcerow' . $data['Resource_Id'] . '">'
                               . '<td style="padding:6px 10px;border:1px solid #ddd;text-align:center;color:#999;">' . $rowIndex . '</td>'
                               . '<td style="padding:6px 10px;border:1px solid #ddd;">' . $safeName
                               .     '<input type="hidden" id="resname'     . $data['Resource_Id'] . '" value="' . $safeName . '">'
                               .     '<input type="hidden" id="resunit'     . $data['Resource_Id'] . '" value="' . $safeUnit . '">'
                               .     '<input type="hidden" id="restypeidx'  . $data['Resource_Id'] . '" value="' . (int)$data['ResourceType_Id'] . '">'
                               .     '<input type="hidden" id="resgroupidx' . $data['Resource_Id'] . '" value="' . (int)$data['Resource_group_Id'] . '">'
                               . '</td>'
                               . '<td style="padding:6px 10px;border:1px solid #ddd;color:#555;">' . $safeUnit . '</td>'
                               . '<td style="padding:4px 8px;border:1px solid #ddd;text-align:center;white-space:nowrap;">'
                               .     '<button type="button" class="btn btn-default btn-xs" '
                               .         'data-id="' . $data['Resource_Id'] . '" '
                               .         'data-name="' . $safeName . '" '
                               .         'data-unit="' . $safeUnit . '" '
                               .         'data-typeid="' . (int)$data['ResourceType_Id'] . '" '
                               .         'data-groupid="' . (int)$data['Resource_group_Id'] . '" '
                               .         'onclick="openEditResource(this)" '
                               .         'style="border-radius:50%;width:28px;height:28px;padding:0;margin-right:4px;">'
                               .         '<span class="icon-pencil"></span></button>'
                               .     '<button type="button" class="deleteresourcebutton btn btn-danger btn-xs" '
                               .         'value="' . $data['Resource_Id'] . '" '
                               .         'style="border-radius:50%;width:28px;height:28px;padding:0;background-color:#d9534f!important;border-color:#d43f3a!important;color:#fff!important;">'
                               .         '<span class="icon-trash1"></span></button>'
                               . '</td></tr>';
                    $rowIndex++;
                }
            }
            $resource .= '</tbody>';
        }

        $resource .= '</table>';
        return json_encode(['error' => 'No', 'resource' => $resource]);
    }

    public function actionCreate()
    {
        if (isset($_POST['resourcename'])) {
            $model = new Resources();
            $model->Name              = $_POST['resourcename'];
            $model->ResourceType_Id   = isset($_POST['restypeid'])  ? (int)$_POST['restypeid']  : 0;
            $model->Resource_group_Id = isset($_POST['resgroupid']) ? (int)$_POST['resgroupid'] : 0;
            $model->Added_On          = date('Y-m-d H:i:s');
            $model->Added_By          = Yii::$app->user->id;
            $model->sortorder         = 0;
            $model->Status            = 0;
            $model->Unit              = isset($_POST['unit']) ? trim($_POST['unit']) : '';
            $model->fuel_unit         = '';
            $model->Price             = 0;
            $model->Vendor_Id         = 0;
            $model->lead_time         = 0;
            $model->credit_period     = 0;
            $model->Project_Id        = 0;
            $model->Resource_Acc_Id   = 0;
            $model->Resource_Location = '';
            $model->pricing_status    = 0;
            $model->log_check         = 0;
            $model->Quantity          = 0;
            $model->reg_number        = '';
            $model->id_number         = '';
            $model->capacity          = '';
            $model->current_project   = 0;
            $model->asset_register_item_id = 0;
            if ($model->save(false)) {
                $model->sortorder = $model->Resource_Id;
                $model->save(false);
                return json_encode(['Id' => $model->Resource_Id, 'Name' => $model->Name, 'error' => 'No']);
            } else {
                return json_encode(['error' => 'Yes', 'errortext' => 'Cannot save resource. Please try again.']);
            }
        }
    }

    public function actionUpdate()
    {
        if (isset($_POST['resourceid'])) {
            $model = Resources::findOne((int)$_POST['resourceid']);
            if ($model) {
                $model->Name = $_POST['name'];
                $model->Unit = isset($_POST['unit']) ? trim($_POST['unit']) : $model->Unit;
                if (isset($_POST['restypeid'])  && $_POST['restypeid']  != '') $model->ResourceType_Id   = (int)$_POST['restypeid'];
                if (isset($_POST['resgroupid']) && $_POST['resgroupid'] != '') $model->Resource_group_Id = (int)$_POST['resgroupid'];
                if ($model->save(false)) {
                    return json_encode(['Id' => $model->Resource_Id, 'Name' => $model->Name, 'error' => 'No']);
                } else {
                    return json_encode(['error' => 'Yes', 'errortext' => 'Cannot update resource.']);
                }
            } else {
                return json_encode(['error' => 'Yes', 'errortext' => 'Resource not found.']);
            }
        }
    }

    public function actionDeleteitem()
    {
        if (isset($_POST['resourceid'])) {
            $model = Resources::findOne((int)$_POST['resourceid']);
            if ($model) {
                $model->Status = 1;
                $model->save(false);
                return json_encode(['Id' => $_POST['resourceid'], 'error' => 'No']);
            } else {
                return json_encode(['error' => 'Yes', 'errortext' => 'Resource not found.']);
            }
        }
    }

    public function actionUpdatesort()
    {
        if (isset($_POST['datavalue'])) {
            foreach ($_POST['datavalue'] as $data) {
                $model = Resources::findOne($data['rowid']);
                if ($model) {
                    $model->sortorder = $data['rowindex'] + 1;
                    $model->save(false);
                }
            }
        }
        return json_encode(['error' => 'No']);
    }
}
