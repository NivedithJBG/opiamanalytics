<?php

namespace app\controllers;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\Worktype;
use app\models\WorkgroupsNew;
use app\models\itemofworks;
use app\models\WorkgroupActivitiesNew;
use app\models\PricingEstimate;
use app\models\PricingEstimateResourcesNew;
use app\models\ProjuserSelection;
use app\models\Wbsscheduleitems;
use app\models\Projects;
use app\models\IowGroup;
use amnah\yii2\user\models\User;


class Workgroups1Controller extends \yii\web\Controller
{
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

    public function actionSearch()
    {
		$uid = Yii::$app->user->Id; 
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if($projuser)
        {
            $projectinfo = Projects::findOne($projuser->projectid);
            $connection = Yii::$app->db;
		    $sql="SELECT Workgroup_Id,Project_Id,Name,worktype,unit,quantity,iowGroupid FROM workgroups_new WHERE Status='0' ";
			if($_POST['workgroupname']!='')
				$sql.="AND Name LIKE '%".$_POST['workgroupname']."%'";
            //$sql.="AND  Project_Id='".$projuser->projectid."' ORDER BY sortorder ASC, Workgroup_Id ASC, Name ASC";
            $sql.="AND  Project_Id='".$projuser->projectid."' ORDER BY sortorder ASC ,Workgroup_Id ASC"; 
			$command=$connection->createCommand($sql);
			$dataReader=$command->query();
			$dataProvider=$dataReader->readAll();
			$datarows='';
			//$sortstatus=$this->Checksort($_POST['projectid']);
			$sortstatus=true;
			if($sortstatus)
			{
				$class='no';
			}
			else
			{
				$class='disabled';
			}
			if(count($dataProvider)>0):
                $datarows.='<div class="row wbsheads">
                                <div class="col-md-5 lines">
                                    <div class="col-md-1 num">
                                            <label>#</label><br>
                                    </div>
                                    <div class="col-md-11" style="padding-left: 0px;">
                                            <label>IOW</label><br>
                                    </div>
                                </div>
                                <div class="col-md-7 qntittys no-padding">
                                        <div class="col-md-4 no-padding">
                                            <label class="align-center">Group Name</label><br>
                                        </div>
                                        <div class="col-md-2 no-padding">
                                            <label class="align-center">Unit</label><br>
                                        </div>
                                        <div class="col-md-2 no-padding">
                                            <label class="align-center">Quantity</label<br>
                                        </div>
                                        <div class="col-md-4">
                                        </div>
                                 </div></div>';
				foreach($dataProvider AS $key=>$data):
					//$worktypename=Worktype::findOne($data['worktype'])->name;
					/*$worktypes=Worktype::find()->orderBy(['name' => SORT_ASC])->all();
					$worktypelist='';
					foreach($worktypes AS $worktype):
						if($worktype['worktype_id']==$data['worktype']):
							$selected='selected';
						else:
							$selected='';
						endif;
						$worktypelist.="<option value='".$worktype['worktype_id']."' ".$selected.">".$worktype['name']."</option>";
					endforeach;
					$sqlst = "SELECT COUNT(*) AS total FROM schedule_activity WHERE progress!=0 AND Project_Id='" . $data['Project_Id'] . "' AND wbs_id='".$data['Workgroup_Id']."' ";
					$command = $connection->createCommand($sqlst);
					$dataReader = $command->query();
					$status = $dataReader->read();
					$changestat = '';
					if ($status['total'] != 0)
						$changestat = '';
					else
                        $changestat = 'disabled';*/

                    $iowGroupName = '';
                    if($iowGroup = IowGroup::findOne($data['iowGroupid']))
                        $iowGroupName = $iowGroup->name;

                    $iowGroups = '';
                    if($projuser)
                        $iowGroups = IowGroup::find()->where(['project_id' => $projuser->projectid, 'status' => 0])->all();

                    $iowGroupSelectBox = '<option value="">Select IOW Group</option>';
                    if($iowGroups){
                        foreach($iowGroups as $iowGroup) {
                            $iowGroupSelected = '';
                            if($iowGroup['id'] == $data['iowGroupid'])$iowGroupSelected = 'selected';
                            $iowGroupSelectBox .= '<option '.$iowGroupSelected.' value="'.$iowGroup['id'].'" >'.$iowGroup['name'].'</option>';
                        }
                    }

                    $datarows.='<div class="row listdats datass" id="workgrouprow'.$data['Workgroup_Id'].'" data-id="'.$data['Workgroup_Id'].'">
                                     <div class="col-md-5">
                                         <div class="row">
                                            <div class="col-md-1">
                                                <span class="number">'.($key + 1).'</span><input type="hidden" value="'.$data['Workgroup_Id'].'">
                                            </div>
                                            <div class="col-md-11 type">
                                                <span class="prjctlist" id="workgroupname'.$data['Workgroup_Id'].'">'.$data['Name'].'</span>
                                                <input class="form-control editworkgroupname" style="display:none;" type="text" id="editworkgroupname'.$data['Workgroup_Id'].'" value="'.$data['Name'].'">
                                                <span class="error"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                    <div class="row">
                                        <div class="col-md-4 type" style="text-align: center; padding:8px;">

                                            <span id="iowGroupId'.$data['Workgroup_Id'].'">'.$iowGroupName.'</span>

                                            <select class="form-control" id="editiowgroupid'.$data['Workgroup_Id'].'" name="iowgroupid"  style="display:none;">
                                                '.$iowGroupSelectBox.'
                                            </select>
                                            <span class="error" style="display: none;"></span>
                                        </div>
                                        <div class="col-md-2 type" style="text-align: center;">
                                            
                                            <span class="unitlist" id="workgroupunit'.$data['Workgroup_Id'].'">'.$data['unit'].'</span>
                                            <input class="form-control editworkgroupunit" style="display:none;" type="text" id="editworkgroupunit'.$data['Workgroup_Id'].'" value="'.$data['unit'].'">
                                            <span class="error"></span>
                                        </div>
                                        <div class="col-md-2 type" style="text-align: center;">
                                           
                                            <span class="qnthtylist" id="workgroupquantity'.$data['Workgroup_Id'].'">'.number_format($data['quantity'],2).'</span>
                                            <input class="form-control editworkgroupquantity" style="display:none;" type="text" id="editworkgroupquantity'.$data['Workgroup_Id'].'" value="'.$data['quantity'].'">
                                            <span class="error"></span>
                                        </div>
                                        <div class="col-md-4 icon-groups" style="padding:5px;">
                                            
                                            <a class="btn btn-primary text-button listactivity"  data-v="'.$data['Workgroup_Id'].'" value="'.$data['Workgroup_Id'].'" data-id="'.$data['worktype'].'" href="javascript:void(0); " title="Activities"><span class="icon-directions_run"></span>Activities</a>
                                            <!-- <a class="btn btn-primary icon-file-text2 viewiownotes" value="'.$data['Workgroup_Id'].'" title="Notes" data-toggle="modal" data-target="#notesModel1" data-name="'. $data['Name'] .'" title="Notes" href="#"></a> -->
                                            <a class="btn btn-primary icon-pencil editworkgroupbutton" data-v="'.$data['Workgroup_Id'].'" id="editworkgroupbutton'.$data['Workgroup_Id'].'" title="Edit IOW" href="javascript:void(0); "></a>
                                            <a class="btn btn-primary icon-save saveworkgroupbutton" style="display:none;" data-v="'.$data['Workgroup_Id'].'" id="saveworkgroupbutton'.$data['Workgroup_Id'].'" title="Save" href="javascript:void(0); "></a>
                                            <a class="btn btn-danger  icon-trash1 deleteworkgroupbutton" data-v="'.$data['Workgroup_Id'].'" id="deleteworkgroupbutton'.$data['Workgroup_Id'].'" title="Delete IOW" href="javascript:void(0); "></a>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                    
                    ';
				endforeach;
			else:
				$datarows='<div class="row"><div class="col-md-12"><div class="text-center">No Workgroup Found</div></div></div>';
			endif;
			$arr = array('result' => $datarows,'projectName' =>$projectinfo->Name,'projectID' =>$projuser->projectid, 'error'=>'No');
			return json_encode($arr);

        }
        else{
            $datarows='<div class="row"><div class="col-md-12"><div class="text-center">No Project Selected</div></div></div>';

            $arr = array('result' => $datarows, 'error'=>'No');
            return json_encode($arr);
        }
    }

    public function actionUpdate()
    {

        if(isset($_POST['workid']))
        {
            $model= WorkgroupsNew::findOne($_POST['workid']);
            $model->Name=$_POST['name'];
            $model->unit=$_POST['unit'];
            $model->quantity=$_POST['quantity'];
            $model->iowGroupid=$_POST['iowgroupid'];
            //$model->worktype=$_POST['worktype'];
            if($model->save(false)):

                //---Upadating Schedule Activity Name----
                $wbsscheduleitem = Wbsscheduleitems::find()->where(['wbsid' => $model->Workgroup_Id])->one();
                $wbsscheduleitem->name = $_POST['name'];
                $wbsscheduleitem->save(false);
                //---------------------------------------
                $arr = array('Id' => $_POST['workid'], 'Name' => $_POST['name'],'error'=>'No');
                return json_encode($arr);
            else:
                $arr = array('Id' => $_POST['workid'],'error'=>'Yes','errortext'=>'Can not update now.');
                return json_encode($arr);
            endif;
        }
    }
    public function actionUpdatesort()
    {

        if(isset($_POST['datavalue']))
        {
            foreach($_POST['datavalue'] AS $data)
            {
                //print_r($data);exit;
                $work=WorkgroupsNew::findOne($data['rowid']);
               // print_r($work);exit;
               // echo $data['rowid'] ."     ". $data['rowindex'];exit;
                $work->sortorder=$data['rowindex'];
                $work->save(false);
            }
        }
    }

    public function actionCheckdeleteworkgroup()
    {
        if(isset($_POST['workid'])):
            $model= WorkgroupActivitiesNew::find()->where(['wbs_id'=>$_POST['workid']])->andwhere(['pricing_status'=>0])->one();

           
        endif;
        if(!empty($model))
        {
            $arr = array('Id' => $_POST['workid'],'error'=>'Yes','errortext'=>'Can not Delete this IOW, Please delete activities. ');
        }
        else{
            $arr = array('Id' => $_POST['workid'],'error'=>'No','errortext'=>'');
        }

        
        return json_encode($arr);
        

    }

    public function actionDeleteworkgroup()
    {
        if(isset($_POST['workid'])):
            $model= WorkgroupsNew::findOne($_POST['workid']);
            $model->Status=1;
            if($model->save(false)):


                $connection = Yii::$app->db;
                // Delete Item of Works
                $sql="SELECT IOW_Id FROM itemofworks WHERE Workgroup_Id='".$_POST['workid']."'";
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();
                $ioworks=$dataReader->readAll();
                 $totalduration=0;

                foreach($ioworks AS $iow):
                    $modeliow=itemofworks::findOne($iow['IOW_Id']);
                    $sql="SELECT SUM(Budgeted_Duration) AS duration FROM  iowactivities WHERE IOW_Id='".$iow['IOW_Id']."'";
                    $command=$connection->createCommand($sql);
                    $dataReader=$command->query();
                    $ioworksduration=$dataReader->read();
                    $calcduration=($ioworksduration['duration']*$modeliow->Cycles)/$modeliow->Resourceunits;
                    $totalduration=$totalduration+$calcduration;
                endforeach;

               

                $sql="DELETE FROM itemofworks WHERE Workgroup_Id='".$_POST['workid']."'";
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();
                if(!empty($ioworks)){
                    $sql="DELETE FROM iowactivities WHERE IOW_Id='".$iow['IOW_Id']."'";
                    $command=$connection->createCommand($sql);
                    $dataReader=$command->query();
                    $sql="DELETE FROM gantt_links WHERE source='".$iow['IOW_Id']."' OR target='".$iow['IOW_Id']."' ";
                    $command=$connection->createCommand($sql);
                    $dataReader=$command->query();
                }
                $wbsactivities=WorkgroupActivitiesNew::find()->where(['wbs_id' => $_POST['workid']])->all();
                foreach($wbsactivities AS $wbsactivity):
                    //$pricing=PricingEstimate::model()->deleteAll(array('condition'=>'activity_Id='.$wbsactivity['id'].' '));
                    $pricing=PricingEstimate::find()->where(['activity_Id' => $wbsactivity['id']])->all();
                    foreach($pricing AS $item):
                        $item->pricing_status=1;
                        $item->save(false);
                    endforeach;
                    //$pricingres=PricingEstimateResources::model()->deleteAll(array('condition'=>'activity_id='.$wbsactivity['id'].' '));
                    $pricingres=PricingEstimateResourcesNew::find()->where(['activity_id' => $wbsactivity['id']])->all();
                    foreach($pricingres AS $pricingre):
                        $pricingre->pricing_status=1;
                        $pricingre->save(false);
                    endforeach;
                    $wbsactivity->pricing_status=1;
                    $wbsactivity->save(false);
                endforeach;

                // new line added by anil on sept 20
 
                 $sql="DELETE FROM wbsscheduleitems WHERE wbsid ='".$_POST['workid']."'";
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();

                  // new line end by anil on sept 20

                $sql="DELETE FROM workgroup_activities_new WHERE wbs_id ='".$_POST['workid']."'";
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();
                $arr = array('Id' => $_POST['workid'],'error'=>'No');
                return json_encode($arr);


            else:
                $arr = array('Id' => $_POST['workid'],'error'=>'Yes','errortext'=>'Can not Delete Project with ID '.$_POST['resid'].'.');
                return json_encode($arr);
            endif;
        endif;

    }

    public function actionCreate()
    {
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if(isset($_POST['Project_Id'])&& isset($_POST['workgroupname']))
        {

            $WorkgroupExist = WorkgroupsNew::find()->where(['Project_Id' => $_POST['Project_Id'], 'Name' => $_POST['workgroupname']])->one();
            if($WorkgroupExist){
                $arr = array('error'=>'Yes','errortext'=>'Item of Work already exist!');
                return json_encode($arr);
            }
            
            $max_sortOrd = WorkgroupsNew::find()->where(['Project_Id' => $_POST['Project_Id']])->max('sortorder');

            $model=new WorkgroupsNew;
            $model->Project_Id=$_POST['Project_Id'];
            $model->Name=$_POST['workgroupname'];
            $model->iowGroupid = $_POST['iowgroupid'];
            $model->unit=$_POST['unit'];
            $model->quantity=$_POST['quantity'];
            $model->wbs_estimate_id=0;
            $model->sortorder = (($max_sortOrd) ? $max_sortOrd : 0) + 1 ;
            

            if($model->save(false)):

                $scheduleItem = New Wbsscheduleitems();
                $scheduleItem->name=$_POST['workgroupname'];
                $scheduleItem->schedulegrp_id = 0;
                $scheduleItem->projectId = $_POST['Project_Id'];
                $scheduleItem->wbsid = $model->Workgroup_Id;
                $scheduleItem->save(false);

                $connection = Yii::$app->db;
                $sql="UPDATE workgroups_new SET parent='".$model->Project_Id."',itemtype='workgroup' WHERE Workgroup_Id='".$model->Workgroup_Id."'";
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();
                $arr = array('Id' => $model->Workgroup_Id, 'Name' => $model->Name,'error'=>'No');
                return json_encode($arr);
            else:
                $arr = array('error'=>'Yes','errortext'=>'Cannot add now. Please try again later');
                return json_encode($arr);
            endif;
        }

    }



    public function actionIowgrouplist(){
        $uid = Yii::$app->user->Id; 
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        $iowGroups = '';
        if($projuser)
            $iowGroups = IowGroup::find()->where(['project_id' => $projuser->projectid, 'status' => 0])->all();

        if($iowGroups){

            $datarows = '<div class="col-md-12 scheduleitemheader schdhead" style="padding: 6px 15px; font-size: 13px; margin-top: 0;">
                                <div class="row">
                                    <div class="col-md-1">
                                        <label>#</label>
                                    </div>
                                    <div class="col-md-7" style="padding-left:5px;">
                                    <label>Group Name</label>
                                    </div>
                                    <div class="col-md-4">
                                        <label>&nbsp;</label>
                                    </div>
                                </div>
                            </div>';



            foreach ($iowGroups as $key => $iowGroup) {

                $datarows .= '<div style="bottom: 15px;" class="col-md-12 datalists scheduleitemcontent" id="" >
                                                                
                                <div class="row  datslis" style="cursor: pointer;" id="">
                                    <div class="col-md-1">
                                        <span class="number">'.($key+1).'</span>
                                    </div>
                                    <div class="col-md-7 type">
                                        <span id="iowgroupname'.$iowGroup->id.'">'.$iowGroup->name.'</span>
                                        <input class="form-control editiowgroupname" type="text" id="editiowgroupname'.$iowGroup->id.'" value="'.$iowGroup->name.'" style="display:none;">
                                        <span class="error"></span>
                                    </div>
                                    <div class="col-md-4 icon-groups">
                                        <a class="btn btn-primary icon-pencil editiowgroup" id="editiowgroup'.$iowGroup->id.'" data-v="'.$iowGroup->id.'" title="Rename IOW Group" href="#"></a>
                                        <a class="btn btn-primary icon-save saveiowgroupbutton"  style="display:none;" data-v="'.$iowGroup->id.'" id="saveiowgroupbutton'.$iowGroup->id.'" title="Save" href="#"></a>
                                    </div>
                                </div>
                            </div>';
            }

        }
        else
            $datarows='<div class="row"><div class="col-md-12"><div class="text-center">No IOW groups found</div></div></div>';

        $arr = array('result' => $datarows, 'error'=>'No');
        return json_encode($arr);

    }

    public function actionIowgroupcreate()
    {
        $uid = Yii::$app->user->Id; 
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        
        if(!$projuser){
            $arr = array('error'=>'Yes','errortext'=>'Project not selected!');
            return json_encode($arr);
        }

        $iowGroup = IowGroup::find()->where(['project_id' => $projuser->projectid, 'name' => $_POST['iowgroupname']])->one();
        if($iowGroup){
            $arr = array('error'=>'Yes','errortext'=>'IOW group already added for this project!');
            return json_encode($arr);
        }

        if(isset($_POST['iowgroupname']) && $_POST['iowgroupname'])
        {
            $model=new IowGroup;
            $model->name=$_POST['iowgroupname'];
            $model->project_id = $projuser->projectid;
            if($model->save(false)):
                $arr = array('error'=>'No');
                return json_encode($arr);
            else:
                $arr = array('error'=>'Yes','errortext'=>'Cannot add now. Please try again later');
                return json_encode($arr);
            endif;
        }
    }

    public function actionIowgroupupdate()
    {

        $iowGroup = IowGroup::findOne($_POST['groupid']);
        if(!$iowGroup){
            $arr = array('error'=>'Yes','errortext'=>'IOW group not exist!');
            return json_encode($arr);
        }

        if(isset($_POST['groupname']) && $_POST['groupname'])
        {
            $iowGroup->name=$_POST['groupname'];
            if($iowGroup->save(false)):
                $arr = array('error'=>'No');
                return json_encode($arr);
            else:
                $arr = array('error'=>'Yes','errortext'=>'Cannot add now. Please try again later');
                return json_encode($arr);
            endif;
        }
    }


}
