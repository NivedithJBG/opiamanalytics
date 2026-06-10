<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\ResourceGroup;
use app\models\Resourcetype;

class ResourcegroupController extends Controller
{
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionSearch()
    {
        $connection = \Yii::$app->db;
        $search = isset($_POST['resgroupname']) ? trim($_POST['resgroupname']) : '';
        $sql = "SELECT rg.Resource_group_Id, rg.Resource_group_Name, rg.ResourceType_Id, rg.RG_sortorder,
                       rt.Name AS TypeName
                FROM resource_group rg
                LEFT JOIN resourcetype rt ON rt.ResourceType_Id = rg.ResourceType_Id
                WHERE rg.status = 0";
        if ($search !== '') {
            $sql .= " AND rg.Resource_group_Name LIKE '%" . addslashes($search) . "%'";
        }
        $sql .= " ORDER BY rt.Name ASC, rg.RG_sortorder ASC, rg.Resource_group_Id ASC";
        $dataProvider = $connection->createCommand($sql)->queryAll();

        if (empty($dataProvider)) {
            $resgroup = '<p style="color:#aaa;font-size:13px;text-align:center;padding:30px 0;">No resource groups found.</p>';
            return json_encode(['error' => 'No', 'resgroup' => $resgroup]);
        }

        // Group by Resource Type
        $grouped = [];
        foreach ($dataProvider as $row) {
            $type = $row['TypeName'] ?: 'Unassigned';
            $grouped[$type][] = $row;
        }

        $resgroup = '<table style="width:100%;border-collapse:collapse;font-size:13px;">'
                  . '<thead><tr>'
                  . '<th style="background:#555;color:#fff;font-weight:bold;padding:7px 10px;border:1px solid #444;text-align:center;width:36px;">#</th>'
                  . '<th style="background:#555;color:#fff;font-weight:bold;padding:7px 10px;border:1px solid #444;">Resource Group</th>'
                  . '<th style="background:#555;color:#fff;font-weight:bold;padding:7px 10px;border:1px solid #444;text-align:center;width:90px;"></th>'
                  . '</tr></thead>';

        $rowIndex = 1;
        foreach ($grouped as $typeName => $rows) {
            $safeType = htmlspecialchars($typeName, ENT_QUOTES);
            $resgroup .= '<tbody>'
                       . '<tr><td colspan="3" style="background:#e8e8e8;color:#444;font-weight:bold;padding:8px 12px;border:1px solid #ccc;font-size:12px;letter-spacing:0.5px;">'
                       . '<span class="icon-settings" style="margin-right:6px;"></span>' . $safeType
                       . '</td></tr>';

            foreach ($rows as $data) {
                $safeName = htmlspecialchars($data['Resource_group_Name'], ENT_QUOTES);

                $resgroup .= '<tr class="resgroupsort resourcegroup' . $data['Resource_group_Id'] . '" '
                           .     'data-id="' . $data['Resource_group_Id'] . '" '
                           .     'id="resourcegrouprow' . $data['Resource_group_Id'] . '">'
                           . '<td style="padding:6px 10px;border:1px solid #ddd;text-align:center;color:#999;">' . $rowIndex . '</td>'
                           . '<td style="padding:6px 10px;border:1px solid #ddd;">' . $safeName
                           .     '<input type="hidden" id="resordrpnames'  . $data['Resource_group_Id'] . '" value="' . $safeName . '">'
                           .     '<input type="hidden" id="resordrptypeid' . $data['Resource_group_Id'] . '" value="' . (int)$data['ResourceType_Id'] . '">'
                           . '</td>'
                           . '<td style="padding:4px 8px;border:1px solid #ddd;text-align:center;white-space:nowrap;">'
                           .     '<button type="button" class="btn btn-default btn-xs" '
                           .         'data-id="' . $data['Resource_group_Id'] . '" '
                           .         'data-name="' . $safeName . '" '
                           .         'data-typeid="' . (int)$data['ResourceType_Id'] . '" '
                           .         'onclick="openEditResGroup(this)" '
                           .         'style="border-radius:50%;width:28px;height:28px;padding:0;margin-right:4px;">'
                           .         '<span class="icon-pencil"></span></button>'
                           .     '<button type="button" class="deletresourcegroupbutton btn btn-danger btn-xs" '
                           .         'value="' . $data['Resource_group_Id'] . '" '
                           .         'id="deletresourcegroupbutton' . $data['Resource_group_Id'] . '" '
                           .         'style="border-radius:50%;width:28px;height:28px;padding:0;background-color:#d9534f!important;border-color:#d43f3a!important;color:#fff!important;">'
                           .         '<span class="icon-trash1"></span></button>'
                           . '</td></tr>';
                $rowIndex++;
            }
            $resgroup .= '</tbody>';
        }

        $resgroup .= '</table>';
        return json_encode(['error' => 'No', 'resgroup' => $resgroup]);
    }

    public function actionCreate()
    {
        if (isset($_POST['resourcegroup'])) {
            $model = new ResourceGroup();
            $model->ResourceType_Id = isset($_POST['restypeid']) ? (int)$_POST['restypeid'] : 0;
            $model->Resource_group_Name = $_POST['resourcegroup'];
            $model->RG_Added_On = date('Y-m-d');
            $model->RG_Updated_On = date('Y-m-d');
            $model->RG_Added_By = Yii::$app->user->id;
            $model->RG_sortorder = 0;
            $model->status = 0;
            if ($model->save(false)) {
                $model->RG_sortorder = $model->Resource_group_Id;
                $model->save(false);
                $arr = array('Id' => $model->Resource_group_Id, 'Name' => $model->Resource_group_Name, 'error' => 'No');
            } else {
                $arr = array('error' => 'Yes', 'errortext' => 'Cannot save resource group. Please try again.');
            }
            return json_encode($arr);
        }
    }

    public function actionUpdate()
    {
        if (isset($_POST['resgrpid'])) {
            $model = ResourceGroup::findOne((int)$_POST['resgrpid']);
            if ($model) {
                $model->Resource_group_Name = $_POST['name'];
                if (isset($_POST['restypeid']) && $_POST['restypeid'] != '')
                    $model->ResourceType_Id = (int)$_POST['restypeid'];
                $model->RG_Updated_On = date('Y-m-d');
                if ($model->save(false)) {
                    $arr = array('Id' => $model->Resource_group_Id, 'Name' => $model->Resource_group_Name, 'error' => 'No');
                } else {
                    $arr = array('error' => 'Yes', 'errortext' => 'Cannot update resource group.');
                }
            } else {
                $arr = array('error' => 'Yes', 'errortext' => 'Resource group not found.');
            }
            return json_encode($arr);
        }
    }

    public function actionDeleteitem()
    {
        if (isset($_POST['restypeid'])) {
            $model = ResourceGroup::findOne((int)$_POST['restypeid']);
            if ($model) {
                $model->status = 1;
                $model->save(false);
                $arr = array('Id' => $_POST['restypeid'], 'error' => 'No');
            } else {
                $arr = array('error' => 'Yes', 'errortext' => 'Resource group not found.');
            }
            return json_encode($arr);
        }
    }

    public function actionGetbytype()
    {
        $typeId = isset($_POST['restypeid']) ? (int)$_POST['restypeid'] : 0;
        $connection = \Yii::$app->db;
        $sql = "SELECT Resource_group_Id, Resource_group_Name FROM resource_group WHERE status=0 AND ResourceType_Id=:tid ORDER BY RG_sortorder ASC, Resource_group_Name ASC";
        $groups = $connection->createCommand($sql)->bindValue(':tid', $typeId)->queryAll();
        return json_encode(['error' => 'No', 'groups' => $groups]);
    }

    public function actionUpdatesort()
    {
        if (isset($_POST['datavalue'])) {
            foreach ($_POST['datavalue'] as $data) {
                $model = ResourceGroup::findOne($data['rowid']);
                if ($model) {
                    $model->RG_sortorder = $data['rowindex'] + 1;
                    $model->save(false);
                }
            }
        }
        $arr = array('error' => 'No');
        return json_encode($arr);
    }
}
