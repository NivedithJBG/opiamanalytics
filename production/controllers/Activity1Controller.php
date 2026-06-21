<?php

namespace app\controllers;  

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\Boq;
use app\models\PricingEstimateResourcesNew;
use app\models\PricingEstimateNew;
use app\models\PricingEstimate;
use app\models\WorkgroupActivitiesNew;
use app\models\WorkgroupsNew;
use app\models\EstimateActivityType;
use app\models\EstimateWorkType;
use app\models\ProjuserSelection;
use app\models\Scheduleactivities;
use app\models\Wbsscheduleitems;
use app\models\ScheduleProgressReport;
use app\models\ActivityRelations;

class Activity1Controller extends Controller
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
        return $this->render('index');
    }

    public function actionGetparents()
    {

        $connection = Yii::$app->db;
        $sqltemvals="SELECT a.Name AS Project,a.Project_Id,b.Name AS Workgroup,b.Workgroup_Id FROM workgroups_new AS b,projects AS a
                     WHERE a.Project_Id=b.Project_Id AND b.Workgroup_Id='".$_POST['id']."'";
        // echo $sqltemvals;exit;
        $command=$connection->createCommand($sqltemvals);
        $dataReader=$command->query();
        $dataProvider=$dataReader->read();
        $arr = array('Project' => $dataProvider['Project'],'Project_Id'=>$dataProvider['Project_Id'],'Workgroup'=>$dataProvider['Workgroup'],'Workgroup_Id'=>$dataProvider['Workgroup_Id'],'error'=>'No');
        return json_encode($arr);
    }

    public function actionListactivities()
    {
        $connection = Yii::$app->db;
        $iowid=$_POST['Workgroup_Id'];
        $projid=$_POST['proid'];
        //$worktypeid=$_POST['worktypeid'];
        //$wbsactivities=WorkgroupActivitiesNew::model()->findAll(array('condition'=>'wbs_id='.$iowid.' AND worktype_Id='.$worktypeid.' AND pricing_status=0','order'=>'sortorder ASC'));
        $wbsactivities=WorkgroupActivitiesNew::find()->where(['wbs_id' => $iowid])->andWhere(['pricing_status' => 0])->orderBy(['sortorder' => SORT_ASC])->all();
        if(count($wbsactivities)>0):
            $datarows='';
            $datarows.='<style>
                #act-list-wrap{margin:20px -8px 0;}
                #act-list-table{width:100%;border-collapse:collapse;margin-bottom:0;font-size:13px;table-layout:auto;}
                #act-list-table thead th{background-color:#555;color:#fff;font-weight:bold;padding:10px 12px;text-align:center;border:1px solid #444;white-space:nowrap;}
                #act-list-table thead th:nth-child(3){text-align:left;}
                #act-list-table tbody td{padding:8px 10px;border:1px solid #ddd;vertical-align:middle;text-align:center;white-space:nowrap;}
                #act-list-table tbody td:nth-child(3){text-align:left;white-space:normal;min-width:180px;}
                #act-list-table tbody tr:hover{background-color:#f9f9f9;}
                #act-list-table .form-control{padding:4px 7px;height:30px;font-size:13px;}
                #act-list-table tbody td:last-child{padding:5px 8px;white-space:nowrap;}
                #act-list-table .editiowactivitybutton,#act-list-table .saveiowactivitybtndrop,#act-list-table .iowactdelbtndrop{width:32px;height:32px;border-radius:50%;padding:0;line-height:30px;text-align:center;margin-left:5px;font-size:14px;}
            </style>
            <div id="act-list-wrap"><table id="act-list-table" class="table table-bordered">
            <thead><tr>
                <th>#</th>
                <th>Activity Type</th>
                <th>Activity Name</th>
                <th>Unit</th>
                <th>Rate</th>
                <th></th>
            </tr></thead>
            <tbody id="act-list-tbody">';
            foreach($wbsactivities AS $key=>$wbsactivity):
                $sql="SELECT * FROM estimateactivities WHERE activity_status=0 AND activity_id=".$wbsactivity['activity_Id']." ";
                $command1 = $connection->createCommand($sql);
                $dataReader1 = $command1->query();
                $est_act = $dataReader1->read();
                if(!empty($est_act)){
                    $name=$est_act['activity_name'];
                }else{
                    $name= '';
                }
                
                //echo $name;exit;
                if($wbsactivity['activity_Name']!=''):
                    $activityname=$wbsactivity['activity_Name'];
                else:
                    $activityname=$name;
                endif;
                if($wbsactivity['type']=='0'):
                    $type="new";
                    //$checkbox="<td></td>";
                else:
                    $type="old";
                    //$checkbox="<td style='text-align:center;'><input type='checkbox' value='1' ".($wbsactivity['estimate']==1?'checked':'')." disabled id='checkestimate" . $wbsactivity['id'] . "' name='checkestimate' style='visibility: visible;'></td>";
                endif;
                $worktypeid=$wbsactivity['worktype_Id'];
                $estimate=$wbsactivity['estimate'];
                $schedule=$wbsactivity['schedule'];
                //$worktypename=Worktype::model()->findByPk($wbsactivity['worktype_Id'])->name;
                //$sqlforactschedule = "SELECT duration,progress FROM schedule_activity WHERE wbs_activity_Id='".$wbsactivity['id']."' AND wbs_id='".$iowid."' AND actvity_id='".$wbsactivity['activity_Id']."' AND process_id='".$wbsactivity['process_Id']."'";
                //echo $sqlforactschedule;exit;
                //$command1 = $connection->createCommand($sqlforactschedule);
                //$dataReader1 = $command1->query();
                //$sche_act = $dataReader1->read();
                //$duration=$sche_act['duration'];
                //$duration=$sche_act['progress'];
		
	
                $sqltemvals="SELECT est_resource_amount AS Price FROM estactivity_resources WHERE estactivity_id='".$wbsactivity['activity_Id']."' ";
                $command=$connection->createCommand($sqltemvals);
                $dataReader=$command->query();
                $resourcesadded=$dataReader->readAll();
                $price=0;
                foreach($resourcesadded AS $data):
                    $price=$price+$data['Price'];
                endforeach;
                $pricingestimate=PricingEstimateNew::find()->where(['project_Id'=>$projid])->andWhere(['activity_Id'=>$wbsactivity['id']])->andWhere(['pricing_status'=>0])->one();
                 $pricingestimateres=PricingEstimateResourcesNew::find()->where(['project_Id'=>$projid])->andWhere(['activity_id'=>$wbsactivity['id']])->andWhere(['pricing_status'=>0])->all();
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

                $sql="SELECT * FROM estimateactivities WHERE activity_status=0 AND activity_id=".$wbsactivity['activity_Id']." ";
                $command1 = $connection->createCommand($sql);
                $dataReader1 = $command1->query();
                $est_act = $dataReader1->read();

                $activitytypename = '';
                if($est_act){
                    $activitytypename=EstimateActivityType::findOne($est_act['activity_type'])->activitytype_name;
                }

                /*$activity_types = EstimateActivityType::findOne($wbsactivity['activitytype_id']);
                if($activity_types){
                    $activitytypename = $activity_types->activitytype_name;
                }
                else{
                    $activitytypename = '';
                }*/
                
                if($wbsactivity['primavera_id'])
                    $activitytypename = 'Construction Activities';


                $datarows.='<tr class="prjactivityss" id="iowactivitiesrow'.$wbsactivity['id'].'" data-id="'.$wbsactivity['id'].'" data-type="'.$wbsactivity['process_Id'].'">
                    <td>'.($key+1).'
                        <input type="hidden" id="activid" name="activid[]" value="'.$wbsactivity['activity_Id'].'">
                        <input type="hidden" id="activunit" name="activunit[]" value="'.$wbsactivity['activity_Unit'].'">
                        <input type="hidden" id="process_id" name="process_id[]" value="'.$wbsactivity['process_Id'].'">
                        <input type="hidden" id="worktype_id" name="worktype_id" value="'.$worktypeid.'">
                        <input type="hidden" id="iow_id" name="iow_id" value="'.$iowid.'">
                        <input type="hidden" id="project_id" name="project_id" value="'.$projid.'">
                        <input type="hidden" id="estimate" name="estimate[]" value="'.$estimate.'">
                        <input type="hidden" id="schedule" name="schedule[]" value="'.$schedule.'">
                        <input type="hidden" id="wbsactid" name="wbsactid[]" value="'.$wbsactivity['id'].'">
                        <input type="hidden" id="sortorder" name="sortorder[]" value="'.$wbsactivity['sortorder'].'">
                    </td>
                    <td>'.$activitytypename.'</td>
                    <td>
                        <span id="iowactivityname'.$wbsactivity['id'].'">'.$activityname.'</span>
                        <input class="form-control editiowactivityname" style="display:none;" type="text" id="editiowactivityname'.$wbsactivity['id'].'" value="'.$activityname.'">
                        <span class="error"></span>
                    </td>
                    <td>
                        <span id="iowactivityunit'.$wbsactivity['id'].'">'.$wbsactivity['activity_Unit'].'</span>
                        <input class="form-control editiowactivityunit" style="display:none;" type="text" id="editiowactivityunit'.$wbsactivity['id'].'" value="'.$wbsactivity['activity_Unit'].'">
                        <input type="hidden" id="editiowactivityestimate'.$wbsactivity['id'].'" name="editiowactivityestimate" value="'.$estimate.'">
                        <span class="error"></span>
                    </td>
                    <td>'.number_format($specrate,2).'</td>
                    <td>
                        <a class="btn btn-primary icon-pencil editiowactivitybutton" data-v="'.$wbsactivity['id'].'" value="'.$wbsactivity['id'].'" data-id="'.$wbsactivity['activity_Id'].'" id="editiowactivitybutton'.$wbsactivity['id'].'" title="Edit Activity" href="javascript:void(0)"></a>
                        <a class="btn btn-primary icon-save saveiowactivitybtndrop" data-v="'.$wbsactivity['id'].'" value="'.$wbsactivity['id'].'" data-id="'.$wbsactivity['activity_Id'].'" data-type="'.$type.'" id="saveiowactivitybtn'.$wbsactivity['id'].'" title="Save" style="display:none;" href="javascript:void(0)"></a>
                        <a class="btn btn-danger icon-trash1 iowactdelbtndrop" data-v="'.$wbsactivity['id'].'" value="'.$wbsactivity['id'].'" data-id="'.$wbsactivity['activity_Id'].'" id="iowactdelbtn'.$wbsactivity['id'].'" title="Delete Activity" href="javascript:void(0);"></a>
                    </td>
                </tr>';
            endforeach;
            $datarows .= '</tbody></table></div>';
        else:
            $datarows='<div class="row"><div class="col-md-12"><div class="text-center">No Activities Found</div></div></div>';
        endif;
        $arr = array('result' => $datarows,'error'=>'No');
        return json_encode($arr);
    }
     public function actionUpdatesort()
    {
        if(isset($_POST['datavalue']))
        {
            foreach($_POST['datavalue'] AS $data)
            {
                $work=WorkgroupActivitiesNew::findOne($data['rowid']);
                $work->sortorder=$data['rowindex'];
                $work->save(false);
            }
        }
    }

    public function actionUpdateiowactivity()
    {
        $iowactid=$_POST['id'];
        $model=WorkgroupActivitiesNew::findOne($iowactid);
        $model->activity_Name=$_POST['name'];
        $model->activity_Unit=$_POST['unit'];
        $model->estimate=$_POST['estimate'];
        $model->save(false);

        if($model2=Scheduleactivities::find()->where(['activity_id'=>$iowactid])->andWhere(['status'=>0])->one()){
            $model2->name=$_POST['name'];
            $model2->unit=$_POST['unit'];
            $model2->save(false);
        }
        
        if(!empty($_POST['slno'])){
            $sl_no = Boq::findOne($_POST['slno']);
            $sl_nos = $sl_no->slno; 
        }else{
            $sl_nos = '';  
        }
        $arr = array('Id' => $iowactid,'Name'=>$_POST['name'],'Unit'=>$_POST['unit'],'Slno'=>$sl_nos,'error'=>'No');
        return json_encode($arr);
    }

    public function actionDeleteiowactivity()
    {
        $activityid=$_POST['actid'];
        $connection = Yii::$app->db;
        //check data in progress report
        $scheduleid=Scheduleactivities::find()->where(['activity_id' => $activityid])->one();

        {
            $db = Yii::$app->db;

            // --- Cascade: store_indents (via pricing_estimate_resources_new) ---
            $pricingIds = $db->createCommand(
                "SELECT pricing_resourceid FROM pricing_estimate_resources_new WHERE activity_id=:aid",
                [':aid' => $activityid]
            )->queryColumn();
            if (!empty($pricingIds)) {
                $db->createCommand()->delete('store_indents', ['pricing_resourceid' => $pricingIds])->execute();
            }

            // --- Cascade: WO task/SC rates and activity qty ---
            $db->createCommand()->delete('wo_task_rates',   ['activity_id' => $activityid])->execute();
            $db->createCommand()->delete('wo_sc_rates',     ['activity_id' => $activityid])->execute();
            $db->createCommand()->delete('wo_activity_qty', ['activity_id' => $activityid])->execute();

            // --- Cascade: Work Orders containing this activity (JSON search) ---
            $woNums = $db->createCommand(
                "SELECT WO_Number FROM work_order
                 WHERE JSON_SEARCH(WO_Subject, 'one', :aid, NULL, '\$[*].activity_id') IS NOT NULL",
                [':aid' => (string)$activityid]
            )->queryColumn();
            if (!empty($woNums)) {
                $db->createCommand()->delete('wo_measurement_book', ['wo_number' => $woNums])->execute();
                $db->createCommand()->delete('work_order', ['WO_Number' => $woNums])->execute();
            }

            // --- Cascade: Purchase Orders linked to this schedule activity ---
            if ($scheduleid) {
                $poIds = $db->createCommand(
                    "SELECT DISTINCT order_id FROM purchase_order_activities WHERE activity_id=:sid",
                    [':sid' => $scheduleid->id]
                )->queryColumn();
                if (!empty($poIds)) {
                    $db->createCommand()->delete('goods_received_note',      ['GRN_Purchase_Order' => $poIds])->execute();
                    $db->createCommand()->delete('purchase_order_resources',  ['order_id' => $poIds])->execute();
                    $db->createCommand()->delete('purchase_order_activities', ['order_id' => $poIds])->execute();
                    $db->createCommand()->delete('purchase_orders',           ['order_id' => $poIds])->execute();
                }
            }

            //delete from prjt estimate (including pricing_estimate_new — no longer blocks deletion)
            $model=WorkgroupActivitiesNew::findOne($_POST['actid']);
            $model->delete();
            PricingEstimateNew::deleteAll(['activity_Id' => $_POST['actid']]);
            $pricing=PricingEstimate::deleteAll(['activity_Id' => $_POST['actid']]);
            $pricingres=PricingEstimateResourcesNew::deleteAll(['activity_Id' => $_POST['actid']]);
            $connection = Yii::$app->db;
            $delquery = 'DELETE FROM schedule_task WHERE wbs_activity_Id="'.$_POST['actid'].'" ';
            $command1=$connection->createCommand($delquery);
            $dataReader1=$command1->query();
            $delquery2 = 'DELETE FROM schedule_activity WHERE wbs_activity_Id="'.$_POST['actid'].'"';
            $command2=$connection->createCommand($delquery2);
            $dataReader2=$command2->query();
            $delquery3 = 'DELETE FROM schedule_activity_new WHERE actvity_id="'.$_POST['actid'].'"';
            $command3=$connection->createCommand($delquery3);
            $dataReader3=$command3->query();

            //delete from progres report

            //delete from project schedule
            if($scheduleid)
                $delete_sch=Scheduleactivities::deleteAll(['id' => $scheduleid->id]);


            $arr = array('error'=>'No','id'=>$_POST['actid']);
            return json_encode($arr);
        }

    }

    public function actionDeleteiowactivity1234()
    {
        $activityid=$_POST['actid'];
        $estimate=PricingEstimateNew::find()->where(['activity_Id' => $activityid])->count();
        if($estimate>0):
            $arr = array('error'=>'Yes','errortext'=>'Cannot delete this item as it is estimated. Please remove from the estimate.');
            return json_encode($arr);
        else:
            $model=WorkgroupActivitiesNew::findOne($_POST['actid']);
            $model->delete();
            $pricing=PricingEstimate::deleteAll(['activity_Id' => $_POST['actid']]);
            $pricingres=PricingEstimateResourcesNew::deleteAll(['activity_Id' => $_POST['actid']]);
            $connection = Yii::$app->db;
            $delquery = 'DELETE FROM schedule_task WHERE wbs_activity_Id="'.$_POST['actid'].'" ';
            $command1=$connection->createCommand($delquery);
            $dataReader1=$command1->query();
            $delquery2 = 'DELETE FROM schedule_activity WHERE wbs_activity_Id="'.$_POST['actid'].'"';
            $command2=$connection->createCommand($delquery2);
            $dataReader2=$command2->query();
            $arr = array('error'=>'No','id'=>$_POST['actid']);
            return json_encode($arr);
        endif;

    }

    /*public function actionDeleteiowactivity()
    {
        $activityid=$_POST['actid'];
        $estimate=PricingEstimateNew::find()->where(['activity_Id' => $activityid])->count();
        if($estimate>0):
            $arr = array('error'=>'Yes','errortext'=>'Cannot delete this item as it is estimated. Please remove from the estimate.');
            return json_encode($arr);
        else:
            $model=WorkgroupActivitiesNew::findOne($_POST['actid']);
            $model->delete();
            $pricing=PricingEstimate::deleteAll(['activity_Id' => $_POST['actid']]);
            $pricingres=PricingEstimateResourcesNew::deleteAll(['activity_Id' => $_POST['actid']]);
            $connection = Yii::$app->db;
            $delquery = 'DELETE FROM schedule_task WHERE wbs_activity_Id="'.$_POST['actid'].'" ';
            $command1=$connection->createCommand($delquery);
            $dataReader1=$command1->query();
            $delquery2 = 'DELETE FROM schedule_activity WHERE wbs_activity_Id="'.$_POST['actid'].'"';
            $command2=$connection->createCommand($delquery2);
            $dataReader2=$command2->query();
            $arr = array('error'=>'No','id'=>$_POST['actid']);
            return json_encode($arr);
        endif;

    }*/

    public function actionAddiowactivities()
    {	
        $connection = Yii::$app->db;
        $sqlqry = "SELECT activity_Id,process_Id FROM workgroup_activities_new WHERE wbs_id='".$_POST['Workgroup_Id']."' AND pricing_status=0";
        $command = $connection->createCommand($sqlqry);
        $dataReader = $command->query();
        $dataItems = $dataReader->readAll();
        $exwbsacts='';
        if(count($dataItems)>0):
            foreach ($dataItems as $key => $dataItem) {
                if($key==0):
                    $exwbsacts.=$dataItem['activity_Id'];
                else:
                    $exwbsacts.=','.$dataItem['activity_Id'];
                endif;
            }
            $condition = 'AND activity_id NOT IN ('. $exwbsacts.')';
        else:
            $condition = '';
        endif;
        //echo "exwbsacts: ".$exwbsacts;exit;
        $sql="SELECT * FROM estimateactivities WHERE activity_status=0 ";
        if($_POST['type'] == 'search'){
            if($_POST['activityname']!='')
                $sql.="  AND activity_name LIKE '%".$_POST['activityname']."%'";

            if(isset($_POST['activitytypeid']) && $_POST['activitytypeid'])
                $sql.=" AND activity_type =".$_POST['activitytypeid']." ";
        }
        if($_POST['type'] == 'nosearch'){
            if(isset($_POST['worktypeid']) && $_POST['worktypeid'])
                    $sql.=" AND work_type =".$_POST['worktypeid']." ";

            if($_POST['activitytypeid'])
                $sql.=" AND activity_type =".$_POST['activitytypeid']." ";
        }
        $sql.=" ORDER BY sortorder ASC,activity_id ASC";
        //echo $sql;exit;
        $command = $connection->createCommand($sql);
        $dataReader = $command->query();
        $dataProvider = $dataReader->readAll();
        $datarows = '';
        if (!empty($dataProvider)):
            $datarows .='<div class="row">
            <div class="add-activity-search-and-list-cntnr col-md-12">
                                                
                <div class="add-activity-search-results-title-wpr row">
                    <div class="col-md-1">
                        <label>#</label>
                    </div>
                    
                    <div class="col-md-6">
                        <label>Activity</label>
                    </div>
                    <div class="col-md-2">
                        <label>Unit</label>
                    </div>
                    <div class="col-md-2" style="text-align: right;">
                        <label>Rate</label>
                    </div>
                    <div class="col-md-1">
                        <label>&nbsp;</label>
                    </div>
                </div>';
            foreach ($dataProvider AS $key => $data):
                $activity_type = EstimateActivityType::findOne($data['activity_type']);
                if($activity_type){
                    $activitytypename = $activity_type->activitytype_name;
                }
                else{
                    $activitytypename = '';
                }
                if($data['work_type']!=0){
                    $worktypename = EstimateWorkType::findOne($data['work_type'])->estworktype_name;
                }
                else{
                    $worktypename = '';
                }
                $connection = Yii::$app->db;
                //$sql="SELECT COUNT(*) AS total FROM  workgroup_activities_new WHERE wbs_id='".$_POST['wbsid']."' AND activity_Name='".$data['activity_name']."' ";
                //$command=$connection->createCommand($sql);
                //$dataReader=$command->query();
                //$data=$dataReader->read();
                /*$ccount = WorkgroupActivitiesNew::find()->where(['wbs_id' => $_POST['Workgroup_Id']])->andWhere(['activity_Name' => $data['activity_name']])->count();*/
                //echo $activitytypename;exit;
                $ccount = WorkgroupActivitiesNew::find()->where(['wbs_id' => $_POST['Workgroup_Id']])->andWhere(['activity_Name' => $data['activity_name']])->andWhere(['pricing_status' => 0])->count();
                $datarows .= ' <div class="add-activity-search-results-content-wpr actwbs row" id="addiowactivityrow'. $data['activity_id'] . '"  data-id=" '. $data['activity_id'] .' " data-type=" '. $data['work_type'] .' ">    
                                        <div class="col-md-1">
                                            <label>&nbsp;</label>	
                                            <span>'. ($key + 1) .'</span>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <label>&nbsp;</label>
                                            <span id="iowactivitynamespan'.$data['activity_id'].'">'.$data['activity_name'].'</span>
                                            <input type="hidden" class="form-control" id="iowactivityname'.$data['activity_id'].'" value="'.$data['activity_name'].'">
                                            <input type="hidden" id="iowworktypeid'.$data['activity_id'].'" value="'.$data['work_type'].'">
                                            <input type="hidden" id="iowactivitytypeid'.$data['activity_id'].'" value="'.$data['activity_type'].'">
                                            <span class="error"></span></div>
                                        <div class="col-md-2">
                                            <label>&nbsp;</label>
                                            <span id="iowactivityunit' . $data['activity_id'] . '">'. $data['activity_unit'] .'</span>
                                            <input class="form-control editiowactivityunit" style="display:none;" type="text" id="masteriowactivityunit' . $data['activity_id'] .'" value="' . $data['activity_unit'] .'">
                                            <span class="error"></span>
                                        </div>
                                        <div class="col-md-2" style="text-align:right;">
                                            <label>&nbsp;</label>   
                                            <span>'.$data['activity_rate'].'</span>
                                        </div>';
                                        /*if(!empty($ccount)){

                            $datarows .= '<div class="col-md-1">
                                                <label>&nbsp;</label>
                                                <input type="hidden" class="form-control" id="iowactivityamount'.$data['activity_id'].'" value="'.$data['activity_rate'].'">
                                                <span><b>Added</b></span>
                                            </div>';
                                        }else{*/
                            $datarows .= '<div class="col-md-1 icon-groups">
                                                <label>&nbsp;</label>
                                                <input type="hidden" class="form-control" id="iowactivityamount'.$data['activity_id'].'" value="'.$data['activity_rate'].'">
                                                <a class="btn btn-primary icon-add addiowactivitydrop" data-v="'. $data['activity_id'] .'" value="' . $data['activity_id'] .'" data-id=" '. $data['work_type'] .' " id="addiowactivity' . $data['activity_id'] . '" title="Add Activity" href="javascript:void(0);"></a>
                                            <span style="font-size:15px;color:green;"></span>
                                            </div>';

                                        //}
                            $datarows .= '</div>';
            endforeach;
            $datarows.='</div></div>';
        else:
            $datarows = '<div class="row"><div class="col-md-12"><div class="text-center">No Activities Found</div></div></div>';
        endif;
        $arr = array('result' => $datarows,'error'=>'No');
        return json_encode($arr);
    }

    public function actionAddiowactivity()
    {
        $connection = Yii::$app->db;
        $wrkgrpid = $_POST['wbsid'];
        $projectid = $_POST['projectid'];
        $worktype_Id = $_POST['worktypeid'];
        $activitytype_id = $_POST['activitytypeid'];
        $activid = $_POST['actid'];
        $amount=$_POST['amount'];
        $activity_Unit = $_POST['activityunit'];
        $activityName = $_POST['activityname'];


        $exist_activity = WorkgroupActivitiesNew::find()->where(['project_Id' => $projectid])->andWhere(['activity_Name' => $activityName])->one();

        $activityNameCnt = 1;
        while ($exist_activity) {
            $activityName = $_POST['activityname'].' '.$activityNameCnt;
            $exist_activity = WorkgroupActivitiesNew::find()->where(['project_Id' => $projectid])->andWhere(['activity_Name' => $activityName])->one();
            $activityNameCnt++;
        }

        $activity=WorkgroupActivitiesNew::find()->where(['wbs_id' => $wrkgrpid])->orderBy(['sortorder' => SORT_DESC])->limit(1)->one();


        if($activity && $activity->activity_Name=="Fabrication of Liners"){
           $activitytype_id=2; 
        }

        if($activity){
            $sortorder=$activity->sortorder + 1;
        }
        else{
            $sortorder= 0;
        }

        $schsql = "SELECT schedule_type FROM `estimateactivitytypes` WHERE activitytype_id = '".$activitytype_id."' ";
        $command=$connection->createCommand($schsql);
        $dataReader=$command->query();
        $schdata = $dataReader->read();  
        if($schdata['schedule_type']==1)
        {
            $schtype = 1;
        }else{
            $schtype = 0;
        }
        
        $typeval=0;
        //$activity=WorkgroupActivities::model()->find(array('condition'=>'wbs_id='.$wrkgrpid.'' ,'order'=>'sortorder DESC','limit'=>'1'));
        //$sortorder=$activity['sortorder'] + 1;
		
        $workgroupsnew= WorkgroupsNew::findOne($wrkgrpid);
        $sql = 'INSERT INTO workgroup_activities_new (project_Id,wbs_id,worktype_Id,activitytype_id,activity_Id,activity_Unit,estimate,amount,sortorder,type,activity_Name,process_Id,schedule,duration,cycle_Unit,cycle_Quantity,pr_status,pricing_status,operations_status,wbs_estimate_id)
                VALUES( "'.$projectid.'","'.$wrkgrpid.'","'.$worktype_Id.'","'.$activitytype_id.'","'.$activid.'","'.$activity_Unit.'","1","'.$amount.'","'.$sortorder.'","'.$typeval.'","'.$activityName.'",0,"'.$schtype.'",0,0,0,0,0,0,0)';
        //echo $sql;exit;
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();

        $work_actids = WorkgroupActivitiesNew::find()->orderBy(['id' => SORT_DESC])->one()->id;

        //$today = date('Y-m-d');
        $getDateAfterHoliday = Yii::$app->helper->getDateAfterHoliday(date("d-m-Y"), $projectid);
        $today = date('Y-m-d', strtotime($getDateAfterHoliday));

        $scheduleItem = Wbsscheduleitems::find()->where(['wbsid' => $wrkgrpid])->one();

        if($scheduleItem){
            if($schtype==1){

                $model = New Scheduleactivities();
                $model->name=$activityName; 
                $model->activity_id=$work_actids;
                $model->unit=$activity_Unit;
                $model->start_date = $today;
                $model->duration = 1;
                $model->end_date = $today;
                $model->actual_start_date = $today;
                $model->actual_end_date = $today;
                $model->old_duration = 1;
                $model->quantity = 1;
                $model->scheduleitem_id=$scheduleItem->scheduleitem_id;
                $model->projectId=$projectid;
                $lastSch=Scheduleactivities::find()->orderBy(['id'=> SORT_DESC])->one();
                $sortorder=$lastSch ? ($lastSch['sortorder'] + 1) : 1;
                $model->sortorder=$sortorder;
                $model->save(false);
            }

        }
        else{

            $newscheduleItem = New Wbsscheduleitems();
            $newscheduleItem->name=$workgroupsnew->Name;
            $newscheduleItem->schedulegrp_id = 0;
            $newscheduleItem->projectId = $projectid;
            $newscheduleItem->wbsid = $workgroupsnew->Workgroup_Id;
            if($newscheduleItem->save(false)):

                if($schtype==1){
                    $model = New Scheduleactivities();
                    $model->name=$activityName;
                    $model->activity_id=$work_actids;
                    $model->unit=$activity_Unit;
                    $model->start_date = $today;
                    $model->duration = 1;
                    $model->end_date = $today;
                    $model->actual_start_date = $today;
                    $model->actual_end_date = $today;
                    $model->old_duration = 1;
                    $model->quantity = 1;
                    $model->scheduleitem_id=$newscheduleItem->scheduleitem_id;
                    $model->projectId=$projectid;
                    $lastSch=Scheduleactivities::find()->orderBy(['id'=> SORT_DESC])->one();
                    $sortorder=$lastSch ? ($lastSch['sortorder'] + 1) : 1;
                    $model->sortorder=$sortorder;
                    $model->save(false);
                }

            endif;

        }

        $arr = array('error'=>'No');
        return json_encode($arr);
    }

    public function actionCheckactivityname()
    {
        if(isset($_POST['name'])):
            $connection = Yii::$app->db;
            $uid = Yii::$app->user->Id; 
            $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
            $projectid = $projuser->projectid;
            $sql="SELECT COUNT(*) AS total FROM  workgroup_activities_new WHERE wbs_id='".$_POST['wbsid']."' AND activity_Name='".$_POST['name']."' AND project_Id='".$projectid."' AND pricing_status=0 ";
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $data=$dataReader->read();
            if($data['total']>0):
                return 'Yes';
            else:
                return 'No';
            endif;
        endif;
    }

    public function actionBoqsearch()
    {
        $slno = $_POST['slno'];

        $activity= WorkgroupActivitiesNew::findOne($_POST['actid']);

        $boqs = Boq::find()->where(['projectid' => $activity->project_Id])->andFilterWhere(['or',['like', 'slno', $slno],['like', 'item', $slno],])->orderBy(['slno' => SORT_ASC])->all();
        $datarows = '';
        $datarows .= '<div class="row" id="iowtable">
                        <div class="add-activity-search-and-list-cntnr col-md-12">
                                                            
                            <div class="add-activity-search-results-title-wpr row">
                                <div class="col-md-1 text-center">
                                    <label><h5>#</h5></label>
                                </div>
                                <div class="col-md-2 text-center">
                                    <label><h5>BOQ No</h5></label>
                                </div>
                                <div class="col-md-8">
                                    <label><h5>Item</h5></label>
                                </div>
                                <div class="col-md-1">
                                    <label>&nbsp;</label>
                                </div>
                            </div>';

        if(!empty($boqs)){
            foreach($boqs as $key => $boq):
                $datarows .= '<div class="add-activity-search-results-content-wpr row">    
                                <div class="col-md-1 text-center">
                                    <label>&nbsp;</label>	
                                    <span style="color: #2c3e50;font-weight: 500;">'. ($key + 1) .'</span>
                                </div>
                                <div class="col-md-1 text-center">
                                    <label>&nbsp;</label>
                                    <span style="color: #2c3e50;font-weight: 500;font-size: 15px;">'.$boq->slno.'</span>
                                </div>
                                <div class="col-md-8">
                                    <label>&nbsp;</label>	
                                    <span style="color: #2c3e50;font-weight: 500;">'.$boq->item.'</span>
                                </div>
                                <div class="col-md-2 text-center icon-groups">
                                    <label>&nbsp;</label>
                                    <button type="button" style="font-size: 11px;" class="btn btn-primary" data-v="'. $boq->boq_id .'" value="' . $boq->boq_id .'" data-id=" '. $_POST['actid'] .' " id="addboqslno" title="Add">Assign to BOQ</button>
                                    <!-- <a class="btn btn-primary icon-add" data-v="'. $boq->boq_id .'" value="' . $boq->boq_id .'" data-id=" '. $_POST['actid'] .' " id="addboqslno" title="Add" href="javascript:void(0);"></a> -->
                                </div>
                            </div><hr>';
            endforeach;
            $datarows.='</div></div>';
        }
        else{
            $datarows.='<div class="row"><div class="col-md-12"><div class="text-center">No Activities Found</div></div></div>';
        }
        $arr=array('result'=>$datarows,'error'=>'No');
        return json_encode($arr);

    }

    public function actionMapboqtoactivity()
    {
        $boqid = $_POST['boqid'];
        $actid = $_POST['actid'];

        $model=WorkgroupActivitiesNew::findOne($actid);
        $model->boq_slno=$boqid;
        $model->save(false);

        $arr=array('error'=>'No');
        return json_encode($arr);

    }


       
   


}
