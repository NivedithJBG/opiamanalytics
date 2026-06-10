<?php

namespace app\controllers;  

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\Projects;
use app\models\Overheads;
use app\models\Construction;
use app\models\Resources;
use app\models\Musterroll;
use app\models\Logistics;
use app\models\IssueSlips;
use app\models\Products;
use app\models\InvoiceResources;
use app\models\ProjectSetup;
use app\models\OrderedResource;
use amnah\yii2\user\models\User;
use app\models\ProjuserSelection;
use app\models\Jobcard;
use app\models\PricingEstimateNew;
use app\models\WorkgroupActivities;
use app\models\WorkgroupActivitiesNew;
use app\models\PricingEstimateResourcesNew;

class JobcardController extends Controller
{

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

	public function beforeAction($action) 
    { 
        $this->enableCsrfValidation = false; 
        return parent::beforeAction($action); 
    }
	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Jobcard;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Jobcard']))
		{
			$model->attributes=$_POST['Jobcard'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->job_id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	public function actionReportjobcard()
	{
	    //echo  "asd";exit();
		$connection = \Yii::$app->db;
		$sql="SELECT name FROM jobcard ORDER BY name DESC limit 1";
		$command=$connection->createCommand($sql);
		$dataReader=$command->query();
		$no=$dataReader->read();
		$name=$no['name'] + 1;
		$groupid=time();
		$userid=Yii::$app->user->id;
		///$itemid=WorkgroupActivities::model()->findByPk($_POST['Job_Activity'])->activity_Id;
		if($_POST['Job_Activity']=='010'):
			$activity="General Use";
		else:
			$itemid=WorkgroupActivitiesNew::findOne($_POST['Job_Activity']);
			$activity=$itemid['activity_Name'];
		endif;
		/*if($_POST['jobcardprocess']==2):
			$activity=ProjectSetup::model()->findByPk($itemid)->PS_Name;
		elseif($_POST['jobcardprocess']==3):
			$activity=Products::model()->findByPk($itemid)->Name;
		elseif($_POST['jobcardprocess']==4):
			$activity=Logistics::model()->findByPk($itemid)->Name;
		elseif($_POST['jobcardprocess']==5):
			$activity=Construction::model()->findByPk($itemid)->CO_Name;
		elseif($_POST['jobcardprocess']==8):
			$activity=Overheads::model()->findByPk($itemid)->Name;
		endif;*/
		
		for ($i = 0; $i < count($_POST['Resource']); $i++) {
			$sql = "INSERT INTO  jobcard (name,date,process,activity,groupid,project_id,resource,unit,res_quantity,user_id,app_activity,iow,status) VALUES('".$name."','" .date('Y-m-d', strtotime($_POST['Job_Date'])). "','" . $itemid['process_Id'] . "','" .$activity  . "','".$groupid."','".$_POST['project_id']."','" . $_POST['Resource'][$i] . "','" . $_POST['Unit'][$i] . "','".$_POST['PropQuantity'][$i]."','".$userid."','".$_POST['Job_Activity']."','".$_POST['jobcardiow']."','1')";
			//echo $sql;exit;
			$command = $connection->createCommand($sql);
			$dataReader = $command->query();
		}
		$arr = array('error' => 'No');
		return json_encode($arr);
	}

	public function actionJobcardsearch()
	{
		$connection = CActiveRecord::getDbConnection();
		$sql="SELECT a.name,a.activity,DATE_FORMAT(a.date,'%d %b %y') AS date,a.groupid,b.username FROM jobcard AS a INNER JOIN user as b on a.user_id=b.id ";
		$sql.="WHERE project_id='".$_POST['projectid']."' AND a.status='0' AND delete_status=0 GROUP BY a.groupid ORDER BY a.date DESC,a.job_id DESC ";
		//echo $sql;exit;
		$command=$connection->createCommand($sql);
		$dataReader=$command->query();
		$dataProvider=$dataReader->readAll();
		$datarows='';
		if(count($dataProvider)>0):
			foreach($dataProvider AS $key=>$data):
				$user=User::model()->active()->findbyPk(Yii::app()->user->id);
				if(Yii::app()->user->isAdmin() || $user['superuser']==2):
					$appbtn="";
				else:
					$appbtn="disabled";
				endif;
				$project=Projects::model()->findByPk($_POST['projectid'])->Name;
				$number=substr($project, 0, 3).'0'.$data['name'];
				$datarows.="<tr id='jobcardrow".$data['groupid']."'>
                                <td>".$number."</td>
                                <td>".$data['date']."</td>
                                <td >".$data['username']."<input type='hidden' value='".$data['groupid']."' id='group_id' name='grpid'></td>
                                <td>".$data['activity']."</td>
                                <td><a href='".Yii::app()->request->baseUrl."/Jobcard/Jobcardview/".$data['groupid']."' class='btn btn-primary jobcardview' ".$appbtn." title='View/Approve'>View/Approve</a></td>
                                <td class='small75'><button type='button' class='btn btn-primary deletejobcardbutton' value='" . $data['groupid'] . "' id='deletejobcardbutton" . $data['groupid'] . "' title='Delete Job Card'> <span class='glyphicon glyphicon-trash'></span></button></td>
                            </tr>";
			endforeach;
		else:
			$datarows='<tr id="nodata"><td colspan="6" style="text-align: center;">No Job Cards For Approval</td></tr>';
		endif;
		$arr = array('result' => $datarows,'error'=>'No');
		echo json_encode($arr);
	}

	public function actionApprovedjobcards()
	{
		$uid = Yii::$app->user->Id; 
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if(!empty($projuser)){
			$projectinfo = Projects::findOne($projuser->projectid);
			$connection = \Yii::$app->db;
			$sql="SELECT a.name,a.job_id,a.activity,DATE_FORMAT(a.date,'%d %b %y') AS date,a.groupid,b.username FROM jobcard AS a INNER JOIN user as b on a.user_id=b.id ";
			$sql.="WHERE project_id='".$projuser->projectid."' AND a.status='1' AND ar_status=0 AND delete_status=0 GROUP BY a.groupid ORDER BY Approved_On DESC";
			//echo $sql;exit;
			$command=$connection->createCommand($sql);
			$dataReader=$command->query();
			$dataProvider=$dataReader->readAll();
			$datarows='';
			$count=0;
			if(!empty($dataProvider)):
				foreach($dataProvider AS $key=>$data):
					$project=Projects::findOne($projuser->projectid)->Name;
					$number=substr($project, 0, 3).'0'.$data['name'];
					$datarows.='<div class="row" id="jobcardrow'.$data['groupid'].'">
									<div class="col-md-1">
									<label>&nbsp;</label>
										<span class="number job-number">'.++$count.'</span>
									</div>
									<div class="col-md-1">
										<label>&nbsp;</label>
										<span class="tooltip-user">
										<span class="icon-user3"></span> 
										<span class="tooltip-content">
										<span class="username">'.$data['username'].'</span>
										</span></span>
									</div>
									<div class="col-md-2 type">
										<label>&nbsp;</label>
										<span>'.$number.'</span>
									</div>
									<div class="col-md-6 type">
										<label>Activity</label>
										<span>'.$data['activity'].'</span>
										<input type="hidden" value="'.$data['groupid'].'" id="group_id" name="grpid">
										<span class="devider">|</span>
										<span class="date"><em class="cal-icon icon-calendar1"></em>'.$data['date'].'</span>
									</div>
									<div class="col-md-2 icon-groups type">
										<a href="javascript:void(0);" class="btn btn-primary icon-eye viewForm" data-v="' . $data['groupid'] . '" title="View"></a>
										<a href="javascript:void(0);" class="btn btn-danger icon-trash1 deleteappjobcard" data-v="' . $data['groupid'] . '" id="deleteappjobcard' . $data['groupid'] . '"title="Delete"></a>
									</div>
								</div>';
				endforeach;
			else:
				$datarows='<div style="text-align: center;">No Approved Job Cards Found</div>';
			endif;
			$arr = array('result' => $datarows,'error'=>'No','projectName' =>$projectinfo->Name);
			return json_encode($arr);
		}else{
			$datarows='<div style="text-align: center;">No Project Selected</div>';
			$arr=array('result'=>$datarows,'error'=>'No');
			return json_encode($arr);
		}
	}

	public function actionCompletedJobcards()
	{
		$connection = CActiveRecord::getDbConnection();
		$sql="SELECT a.name,a.job_id,a.activity,DATE_FORMAT(a.date,'%d %b %y') AS date,a.groupid,b.username FROM jobcard AS a INNER JOIN user as b on a.user_id=b.id ";
		$sql.="WHERE project_id='".$_POST['projectid']."' AND a.status='2' GROUP BY a.groupid ORDER BY date DESC,job_id DESC ";
		//echo $sql;exit;
		$command=$connection->createCommand($sql);
		$dataReader=$command->query();
		$dataProvider=$dataReader->readAll();
		$datarows='';
		if(count($dataProvider)>0):
			foreach($dataProvider AS $key=>$data):
				$project=Projects::model()->findByPk($_POST['projectid'])->Name;
				$number=substr($project, 0, 3).'0'.$data['name'];
				$datarows.="<tr id='jobcardrow".$data['groupid']."'>
                                <td>".$number."</td>
                                <td>".$data['date']."</td>
                                <td >".$data['username']."<input type='hidden' value='".$data['groupid']."' id='group_id' name='grpid'></td>
                                <td>".$data['activity']."</td>
								<td class='small75'><button type='button' class='btn btn-primary deleteappjobcard' value='" . $data['groupid'] . "' id='deleteappjobcard" . $data['groupid'] . "' title='Delete Job Card'> <span class='glyphicon glyphicon-trash'></span></button></td>
                            </tr>";
			endforeach;
		else:
			$datarows='<tr id="nodata"><td colspan="5" style="text-align: center;">No Job Card Found</td></tr>';
		endif;
		$arr = array('result' => $datarows,'error'=>'No');
		echo json_encode($arr);
	}

	public function actionComplete()
	{
		$id=$_POST['jobid'];
		$connection = CActiveRecord::getDbConnection();
		if(isset($_POST['jobid'])):
			$sql="UPDATE jobcard SET status='2' WHERE job_id='".$id."' ";
			$command=$connection->createCommand($sql);
			$dataReader=$command->query();
			$arr = array('Id' => $_POST['jobid'],'error'=>'No');
			echo json_encode($arr);
		endif;
	}
	
	public function actionGetstructure()
    {
        $connection = CActiveRecord::getDbConnection();
        $sql = "SELECT * FROM wbs_estimate_structure WHERE project_id='".$_POST['projectid']."' AND status='0'";
        //echo $sql;exit;
        $command = $connection->createCommand($sql);
        $dataReader = $command->query();
        $dataProvider = $dataReader->readAll();
        $datarows="";
        $datarows.="<option value='none'>Select Structure</option>";
        foreach($dataProvider AS $data):
            $datarows.="<option value='".$data['wbs_estimate_id']."'>".$data['wbs_estimate_name']."</option>";
        endforeach;
        $datarows.="<span class='error' style='float:left;display: none;'></span> ";
        $arr = array('result' => $datarows, 'error' => 'No');
        echo json_encode($arr);
	}
	public function actionGetjobcards()
    {
		$uid = Yii::$app->user->Id; 
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
        if(!empty($projuser)){ 
			$connection = \Yii::$app->db;
			$sql = "SELECT * FROM workgroups_new WHERE Project_Id='".$projuser->projectid."' AND status='0' AND pricing_status=0 ORDER BY sortorder ASC ";
			//echo $sql;exit;
			$command = $connection->createCommand($sql);
			$dataReader = $command->query();
			$dataProvider = $dataReader->readAll();
			$datarows='';
			$raisejobdate='';
			$raisejobdate.='<label><b>&nbsp;&nbsp;Date</b></label></br>&nbsp;&nbsp;<input class="form-control" id="datejobcard" name="Job_Date" placeholder="Date" value="'.date("d-m-Y").'" type="text">
										<input type="hidden" name="project_id" id="jobcardprojectid" value="'.$projuser->projectid.'">';
			$datarows.='<div class="col-md-12 ">
							<table class="table table-bordered ">
									<thead>
										<tr>
											<th class="slno" ></th>
											
											<th class="iows">IOW</th>
											<th class="act" colspan="2">Activity</th>
											<th class="datez"></th>
											
										</tr>
									</thead>
									<tbody id="jobcard-head">
										<tr>
											<td></td>
											
											<td>
												<select id="jobcardiowlist" name="jobcardiow" class="form-control iows">
											<option value="none">Select IOW</option>';
											foreach($dataProvider AS $data):
												$datarows.='<option value="'.$data['Workgroup_Id'].'">'.$data['Name'].'</option>';
											endforeach;
								$datarows.='</select>
											<span class="error" style="display: none;float: left;font-size: 12px;color:red"></span>	
											</td>
											<td colspan="2">
												<select id="jobcardactivity" name="Job_Activity" class="form-control">
											<option value="none">Select Activity</option>
										</select>
										<span class="error" style="display: none;float: left;font-size: 12px;color:red" id="jobcardserror"></span>
										<input type="hidden" id="actqty">
										<input type="hidden" id="jobcards">
											</td>
											<td></td>
											<tr>
												<td colspan="5"></td>

											</tr>
											

						
										</tr>
									</tbody>
									<thead>
										<tr>
											<th class="firsts">#</th>
											<th class="res">Resource</th>
											<th class="un">Unit</th>
											<th class="prp">Proposed Qty</th>
											<th class="text-right ent"> </th>
										</tr>
										
									</thead>
									
									</div>
							<div class="col-md-12">
								
									
									
									<tbody id="jobcard_wrap">
										<tr>
											<td>1</td>
											<td>
											<select id="JobcardResource1" data-id="1" name="Resource[]" class="form-control JobcardResource">
												<option value="0">Select Resource</option>
											</select>
											<span class="error"></span></td>
											<td>
											<div class="unitres">
											<input type="text" class="form-control" readonly name="Unit[]" id="Unit1" data-id="1" placeholder="Unit">
											<input type="hidden" id="res_id" value="">
												<button  type="button" class="editunitbuttons" style="display: none;" data-id="1" id="editunitbuttons"  data-mode="old" title="Edit Unit"> <span class="icon-pencil editVendor-pencil" title="Edit Unit"></span></button>
												<input class="form-control editunitplant" style="display: none;" type="text" id="editunitplant1" value="">
												<button type="button" class="saveunitbuttons" style="display:none" data-id="1" value="" id="saveunitbuttons" data-mode="old"><a title="Update Unit" href="#" class="icon-save"></a>
                        						</button>
												</div>

											</td>
											
											<td class="text-right">
											<input type="text" class="form-control Quantity" name="PropQuantity[]" id="PropQuantity1" data-id="1" placeholder="Quantity">
                                   			<span class="error" style="display: none;"></span></td>
											<td class="text-right icon-groups">
												<a href="#" class="btn btn-primary icon-add" id="addmorerows"></a>
											</td>
										</tr>
										<tr id="jobcardrow"></tr>	
									</tbody>
								</table>
								<div class="row">
									<div class="col-md-12 text-center">
										<label>&nbsp;</label>
										
										<button type="button" class="btn btn-primary" name="jobcardreport" id="jobcardreport"><span class="icon-check"></span> Save</button>
										<button type="button" class="btn btn-primary cancel" name="jobcardreport" id="cancel" style="display:none;"><span class="icon-check"></span> cancel</button>
									</div>
								</div>
							</div>';
							
			$arr = array('result' => $datarows,'raisejobdate'=>$raisejobdate, 'error' => 'No');
			return json_encode($arr);
		}else{
			$datarows='<div style="text-align: center;">No Project Selected</div>';
			$arr=array('result'=>$datarows,'error'=>'No');
			return json_encode($arr);
		}
    }

	public function actionProcess() 
	{
		$connection = CActiveRecord::getDbConnection();
		//$sql = "SELECT DISTINCT process_Id FROM pricing_estimate WHERE project_Id='".$_POST['projectid']."' ORDER BY process_Id ASC ";
		$sql = "SELECT DISTINCT b.process_Id FROM workgroup_activities AS a INNER JOIN pricing_estimate AS b ON a.id=b.activity_Id WHERE a.wbs_id=".$_POST['iow']." AND a.pricing_status=0 AND b.project_Id=".$_POST['projectid']." AND b.pricing_status=0 ";
		//echo $sql;exit;
		$command = $connection->createCommand($sql);
		$dataReader = $command->query();
		$dataProvider = $dataReader->readAll();
		$datarows="";
		$datarows.="<option value='none'>Select Process</option>";
		foreach($dataProvider AS $data):
			if($data['process_Id']==2):
				$datarows.="<option value='2'>Project Setup</option>";
			elseif($data['process_Id']==3):
				$datarows.="<option value='3'>Production</option>";
			elseif($data['process_Id']==4):
				$datarows.="<option value='4'>Logistics</option>";
			elseif($data['process_Id']==5):
				$datarows.="<option value='5'>Construction</option>";
			elseif($data['process_Id']==8):
				$datarows.="<option value='8'>Overheads</option>";
			endif;
		endforeach;
		$datarows.="<span class='error' style='float:left;display: none;'></span> ";
		$arr = array('result' => $datarows, 'error' => 'No');
		echo json_encode($arr);
	}

	public function actionGetactivity()
	{
		$uid = Yii::$app->user->Id; 
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
		$connection = \Yii::$app->db;
		//$sql = "SELECT * FROM pricing_estimate WHERE project_Id='".$_POST['projectid']."' AND process_Id='".$_POST['process']."' AND activity_qty!=0 AND pricing_status=0 ORDER BY process_Id ASC ";
		//$sql = "SELECT b.* FROM workgroup_activities_new AS a INNER JOIN pricing_estimate_new AS b ON a.id=b.activity_Id WHERE a.wbs_id=".$_POST['iow']." AND a.pricing_status=0 AND b.project_Id=".$_POST['projectid']." AND b.pricing_status=0";
		$sql = "SELECT b.* FROM workgroup_activities_new AS a INNER JOIN pricing_estimate_new AS b ON a.id=b.activity_Id WHERE a.wbs_id=".$_POST['iow']." AND a.pricing_status=0 AND b.project_Id=".$projuser->projectid." AND b.activity_qty!=0 AND b.pricing_status=0 ORDER BY b.process_Id ASC";
		//echo $sql;exit;
		$command = $connection->createCommand($sql);
		$dataReader = $command->query();
		$dataProvider = $dataReader->readAll();
		$datarows="<option value='none'>Select Activity</option>";
		$datarows.='<option value="010">General Use</option>';
		foreach($dataProvider AS $data):
			$activityname=WorkgroupActivitiesNew::findOne($data['activity_Id'])->activity_Name;
			$activity_Id=WorkgroupActivitiesNew::findOne($data['activity_Id'])->activity_Id;
			$datarows.="<option value='".$data['activity_Id']."'>".$activityname."</option>";
		endforeach;
		$arr = array('result' => $datarows, 'error' => 'No');
		return json_encode($arr);
	}

	public function actionGetresources()
	{
		$connection = \Yii::$app->db;
		$sqltemvals = "SELECT a.Name AS resource,a.Unit,a.Resource_Id,a.ResourceType_Id,b.* FROM resources AS a
                INNER JOIN pricing_estimate_resources_new AS b ON  a.Resource_Id=b.resource_Id
                WHERE b.activity_id='" . $_POST['activityid'] . "' AND b.pricing_status=0 ORDER BY a.Name";
		$command = $connection->createCommand($sqltemvals);
		$dataReader = $command->query();
		$resourcesadded = $dataReader->readAll();
		$datarows="<option value='0'>Select Resources</option>";
		if($_POST['activityid']=='010'):
			$datarows.="<option value='940'>Diesel</option>";
		endif;
		foreach($resourcesadded AS $resources):
			$wbsactivity=WorkgroupActivitiesNew::findOne($resources['activity_id']);

			if($wbsactivity->activitytype_id==0):
                $processtype=$wbsactivity->process_Id;
            else:
                $processtype=$wbsactivity->activitytype_id;
            endif;

            if($processtype==$resources['process_Id']){

            	$datarows.="<option value='".$resources['Resource_Id']."'>".$resources['resource']."</option>";

            }

		endforeach;
		$datarows.='<span class="error"></span>';
		$actqty=PricingEstimateNew::find()->where(['activity_Id' =>$_POST['activityid']])->one();
		$activityid=WorkgroupActivitiesNew::findOne($_POST['activityid'])->activity_Id;
		// if($_POST['process']==2):
		// 	$actunit=ProjectSetup::findOne($activityid)->PS_Unit;
		// elseif($_POST['process']==3):
		// 	$actunit=Products::findOne($activityid)->Unit;
		// elseif($_POST['process']==4):
		// 	$actunit=Logistics::findOne($activityid)->Unit;
		// elseif($_POST['process']==5):
		// 	$actunit=Construction::findOne($activityid)->CO_Unit;
		// elseif($_POST['process']==8):
		// 	$actunit=Overheads::findOne($activityid)->Unit;
		//endif;

		//$jobcards=Jobcard::model()->count(array('condition'=>'app_activity='.$_POST['activityid'].' '));
		$jobcards=Jobcard::find()->where(['app_activity' => $_POST['activityid']])->all();
		//$exstqty=$jobcards['act_quantity'];
		$sql = "SELECT distinct groupid FROM `jobcard` WHERE `app_activity` = ".$_POST['activityid']." ORDER BY `groupid` ASC ";
		$command = $connection->createCommand($sql);
		$dataReader = $command->query();
		$jobcards = $dataReader->readAll();
		$exstqty=0;
		foreach($jobcards AS $jobcard):
			$asd=Jobcard::find()->where(['groupid' => $jobcard['groupid']])->one();
			$exstqty=$exstqty + $asd['act_quantity'];
		endforeach;
		/*if(count($jobcards)>0):
			$totqty=0;
			foreach($jobcards AS $jobcard):
				$totqty=$totqty + $jobcard['act_quantity'];
			endforeach;
			$exstqty=$totqty / 2;
		else:
			$exstqty=0;
		endif;*/
		if($_POST['activityid']=='010'):
			$activitynewunit="";
		else:
			$activitynew=WorkgroupActivitiesNew::findOne($_POST['activityid']);
			$activitynewunit=$activitynew->activity_Unit;
		endif;

		$arr = array('result' => $datarows,'actqty'=>$actqty->activity_qty,'actunit'=>$activitynewunit,'jobcards'=>$exstqty, 'error' => 'No');
		return json_encode($arr);
	}

	public function actionActivityResources()
	{
		$connection = CActiveRecord::getDbConnection();
		$sqltemvals = "SELECT a.Name AS resource,a.Unit,a.Resource_Id,a.ResourceType_Id,b.* FROM resources AS a
                INNER JOIN pricing_estimate_resources AS b ON  a.Resource_Id=b.resource_Id
                WHERE b.activity_id='" . $_POST['activityid'] . "' ";
		if($_POST['resname']):
			$sqltemvals.="AND a.Name LIKE '%".$_POST['resname']."%' ";
		endif;
		if($_POST['notinres']):
			$sqltemvals.="AND b.resource_Id NOT IN (".$_POST['notinres'].") ";
		endif;
			$sqltemvals.="ORDER BY a.Name";
		//echo $sqltemvals;exit;
		$command = $connection->createCommand($sqltemvals);
		$dataReader = $command->query();
		$resourcesadded = $dataReader->readAll();
		$datarows="";
		if(count($resourcesadded)>0):
			foreach($resourcesadded AS $key=>$resources):
				$resource=Resources::model()->find(array("condition" =>"Resource_Id='".$resources['Resource_Id']."' "));
				$unit=$resource->Unit;
				$resqty=PricingEstimateResources::model()->find(array('condition'=>'resource_Id='.$resources['Resource_Id'].' AND activity_id='.$_POST['activityid'].' '))->quantity;
				$actqty=PricingEstimate::model()->find(array('condition'=>'activity_Id='.$_POST['activityid'].' '))->activity_qty;
				$invoices=InvoiceResources::model()->findAll(array('condition'=>'resource_id='.$resources['Resource_Id'].' '));
				if(count($invoices)>0):
					$recresqty=0;
					foreach($invoices AS $invoice):
						$recresqty=$recresqty + $invoice['resource_qty'];
					endforeach;
				else:
					$recresqty=0;
				endif;
				$datarows.="<tr id='resourcerow".$resources['Resource_Id']."'>
							<td>".($key + 1)."</td>
							<td>".$resources['resource']."
							<input type='hidden' value='".$resources['Resource_Id']."'>
							<input type='hidden' id='resunit".$resources['Resource_Id']."' value='".$resources['Unit']."'>
							<input type='hidden' id='EstresQty".$resources['Resource_Id']."' value='".($actqty * $resqty)."'>
							</td>
							<td>".$unit."</td>
							<td>".($actqty * $resqty)."</td>
							<td>".$recresqty."</td>
							<td><input type='text' class='form-control ResQuantity' id='PropQuantity".$resources['Resource_Id']."' data-id='".$resources['Resource_Id']."' placeholder='Quantity'>
							<span class='error'></span>
							</td>
							<td id='remresquantity".$resources['Resource_Id']."'></td>
							<td><button type='button' class='btn btn-primary small75 addresource' id='addresource".$resources['Resource_Id']."' value='".$resources['Resource_Id']."'>Add</td>
							</tr>";
			endforeach;
		else:
			$datarows.="<tr><td colspan='8' style='text-align: center'>No Resource Found</td></tr>";
		endif;
		$arr = array('result' => $datarows,'error' => 'No');
		echo json_encode($arr);
	}

	public function actionUpdateJobcard()
	{
		$groupid=$_POST['groupid'];
		$resourceid=$_POST['resourceid'];
		$quantity=$_POST['quantity'];
		$resunit=$_POST['resunit'];
		$jobcard=Jobcard::model()->find(array('condition'=>'groupid='.$groupid.' '));
		$model=New Jobcard();
		$model->name=$jobcard['name'];
		$model->date=$jobcard['date'];
		$model->process=$jobcard['process'];
		$model->activity=$jobcard['activity'];
		$model->groupid=$groupid;
		$model->resource=$resourceid;
		$model->unit=$resunit;
		$model->act_quantity=$jobcard['act_quantity'];
		$model->res_quantity=$quantity;
		$model->project_id=$jobcard['project_id'];
		$model->user_id=Yii::app()->user->id;
		$model->app_activity=$jobcard['app_activity'];
		$model->status=1;
		$model->save(false);
		$connection = CActiveRecord::getDbConnection();
		$sql1="SELECT job_id,resource,unit,act_quantity,res_quantity,app_activity,status ";
		$sql1.="FROM jobcard WHERE groupid='$groupid' ORDER BY resource DESC";
		//echo $sql1;
		$command=$connection->createCommand($sql1);
		$dataReader=$command->query();
		$dataProvider=$dataReader->readAll();
		$datarows="";
		$notinres="";
		foreach($dataProvider AS $key=>$data):
			$resestqty=PricingEstimateResources::model()->find(array('condition'=>'resource_Id='.$data['resource'].' AND activity_id='.$data['app_activity'].' '))->quantity;
			$actqty=PricingEstimate::model()->find(array('condition'=>'activity_Id='.$data['app_activity'].' '))->activity_qty;
			$resqty=$resestqty * $actqty;
			$exstjobcards=Jobcard::model()->findAll(array('condition'=>'resource='.$data['resource'].' '));

			if(count($exstjobcards)>0):
				$totresqty=0;
				foreach($exstjobcards AS $exstjobcard):
					$totresqty=$totresqty + $exstjobcard['res_quantity'];
				endforeach;
				$exstresqty=$totresqty - $data['res_quantity'];
			else:
				$exstresqty=0;
			endif;
			$remqty=$resqty - $data['res_quantity'];
			if($data['status']==0):
				$btn="";
			else:
				$btn="disabled";
			endif;

			if($key=='0'):
				$notinres.="".$data['resource']."";
			else:
				$notinres.=",".$data['resource']."";
			endif;
			$datarows.="<tr id='jobcardrow".$data['job_id']."'>
                        <td>".($key + 1)."</td>
                        <td colspan='2'>".Resources::model()->findByPk($data['resource'])->Name."
                        <input type='hidden' name='resid[]' value='".$data['resource']."'>
                        <input type='hidden' name='resunit[]' value='".$data['unit']."'>
                        <input type='hidden' id='groupid' value='".$groupid."' name='groupid'>
                        </td>
                        <td>".$data['unit']."<input type='hidden' name='jobcardid[]' value='".$data['job_id']."'></td>
                        <td>".($resqty)."</td>
                        <td>".$data['res_quantity']."</td>
                        <td>
                        <input type='text' class='form-control propqty' id='propqty".$data['job_id']."' data-id='".$data['job_id']."' data-item='".$data['resource']."' name='propqty[]' value=''>
                        <span class='error' style='displa:none'></span>
                        <input type='hidden' id='currentresqty".$data['job_id']."' value='".$data['res_quantity']."'>
                        </td>
                        <td>".$remqty."<input type='hidden' id='remqty".$data['resource']."' data-id='".$data['resource']."' value='".$remqty."'></td>
                        <td><button type='button' class='btn btn-primary removeresitem' id='removeresitem" . $data['job_id'] . "' value='" . $data['job_id'] . "'>Remove</button></td>
                        </tr>";
		endforeach;
		$datarows.='<tr>
            <td colspan="6">
            	<input type="hidden" id="ordercount">
            	<input type="hidden" id="notinres" value="'.$notinres.'">
			</td>
            <td><button type="button" class="btn btn-primary" id="canceljobcard" name="canceljobcard" onclick="goBack()">Cancel</button></td>
            <td colspan="2"><button type="submit" class="btn btn-primary" name="Update">Update</button></td>
        </tr>';
		$arr = array('result' => $datarows,'error' => 'No');
		echo json_encode($arr);
	}

	public function actionRemoveJobcard()
	{
		$order=OrderedResource::model()->find(array('condition'=>'jobcard_id='.$_POST['jobcardid'].''));
		$ordercount=count($order);
		$cart=Cart::model()->find(array('condition'=>'Job_Id='.$_POST['jobcardid'].' AND status=0'));
		$cartcount=count($cart);
		$total=$ordercount + $cartcount;
		if($total==0){
			$jobcard=Jobcard::model()->findByPk($_POST['jobcardid']);
			$jobcard->delete();
			$connection = CActiveRecord::getDbConnection();
			$sql1="SELECT job_id,resource,unit,act_quantity,res_quantity,app_activity,status ";
			$sql1.="FROM jobcard WHERE groupid=".$_POST['groupid']." ORDER BY resource DESC";
			//echo $sql1;exit;
			$command=$connection->createCommand($sql1);
			$dataReader=$command->query();
			$dataProvider=$dataReader->readAll();
			$notinres="";
			foreach($dataProvider AS $key=>$data):
				if($key=='0'):
					$notinres.="".$data['resource']."";
				else:
					$notinres.=",".$data['resource']."";
				endif;
			endforeach;
			$arr = array('result' => $_POST['jobcardid'],'notinres'=>$notinres,'error' => 'No');
			echo json_encode($arr);
		}
		else{
			$arr = array('result' => $_POST['jobcardid'],'cartcount'=>$cartcount,'ordercount'=>$ordercount,'error' => 'Yes');
			echo json_encode($arr);
		}

	}

	public function actionActivityreport()
	{
		$activityid=WorkgroupActivitiesNew::findOne($_POST['activity'])->activity_Id;
		$connection = \Yii::$app->db;
		$sql="SELECT quantity_total FROM site_report WHERE activityid='".$activityid."' ";
		$command=$connection->createCommand($sql);
		$dataReader=$command->query();
		$data=$dataReader->read();
		if(isset($_POST['resourceo'])){
			$sql1="SELECT SUM(quantity_used) AS total FROM site_report_resources WHERE activityid='".$activityid."' AND resource_id='".$_POST['resourceo']."' ";
			$command=$connection->createCommand($sql1);
			$dataReader=$command->query();
			$datares=$dataReader->read();
		}else{
			$sql1="SELECT SUM(quantity_used) AS total FROM site_report_resources WHERE activityid='".$activityid."' ";
			$command=$connection->createCommand($sql1);
			$dataReader=$command->query();
			$datares=$dataReader->read();
		}
		if(!empty($datares)){
			$resqty = $datares['total'];
		}else{
			$resqty = '';
		}
		if(!empty($data)){
			$actqty = $data['quantity_total'];
		}else{
			$actqty = '';
		}
		$arr = array('actqty'=>$actqty,'resqty'=>$resqty,'error' => 'No');
		echo json_encode($arr);
	}

	public function actionJobcardview()
	{
		$id=$_POST['viewid'];
		$connection = \Yii::$app->db;
		$sql="SELECT a.process,DATE_FORMAT(a.date,'%d %b %y') AS date,a.activity,a.activity_unit,a.app_activity,a.act_quantity,a.project_id,a.startdate,a.enddate,a.status,b.username FROM jobcard as a left join user as b on a.user_id=b.id WHERE groupid='".$id."' ";
		$command=$connection->createCommand($sql);
		$dataReader=$command->query();
		$jobcard=$dataReader->read();

		//$activityid=WorkgroupActivities::model()->findByPk($jobcard['app_activity'])->activity_Id;
		$activity=WorkgroupActivitiesNew::findOne($jobcard['app_activity']);
		$activityid=$activity['activity_Id'];
		$actunit=$activity['activity_Unit'];

		$sql="SELECT quantity_total FROM site_report WHERE activityid='".$activityid."' AND resourcegroup_id='".$jobcard['process']."' ";
		$command=$connection->createCommand($sql);
		$dataReader=$command->query();
		$execqty=$dataReader->read();

		/*$sql1="SELECT SUM(act_quantity) AS total FROM jobcard WHERE app_activity='".$jobcard['app_activity']."'";
		$command=$connection->createCommand($sql1);
		$dataReader=$command->query();
		$exstactqty=$dataReader->read();*/

		/*if($jobcard['process']==2):
			$actunit=ProjectSetup::model()->findByPk($activityid)->PS_Unit;
		elseif($jobcard['process']==3):
			$actunit=Products::model()->findByPk($activityid)->Unit;
		elseif($jobcard['process']==4):
			$actunit=Logistics::model()->findByPk($activityid)->Unit;
		elseif($jobcard['process']==5):
			$actunit=Construction::model()->findByPk($activityid)->CO_Unit;
		elseif($jobcard['process']==8):
			$actunit=Overheads::model()->findByPk($activityid)->Unit;
		endif;*/

		$sql = "SELECT distinct groupid FROM `jobcard` WHERE `app_activity` = ".$jobcard['app_activity']." ORDER BY `groupid` ASC ";
		$command = $connection->createCommand($sql);
		$dataReader = $command->query();
		$datavalues = $dataReader->readAll();
		$exstactqty=0;
		foreach($datavalues AS $datavalue):
			$asd=Jobcard::find()->where(['groupid' => $datavalue['groupid']])->one();
			$exstactqty=$exstactqty + $asd['act_quantity'];
		endforeach;

		$sql1="SELECT job_id,resource,unit,act_quantity,res_quantity,app_activity,status ";
		$sql1.="FROM jobcard WHERE groupid='$id'";
		//echo $sql1;
		$command=$connection->createCommand($sql1);
		$dataReader=$command->query();
		$dataProvider=$dataReader->readAll();
		$datarows='';
		$datarows.='<div class="col-md-12"><div class="text-center"><b><h4>Job Card</h4></b></div></div>
					<div class="col-md-12 raise-muster-roll"> 
						<div class="row">
							<div class="col-md-2 type">
								<div class="form-group">
									<label>Date</label>
									<input class="form-control" id="" name="" placeholder="Date" value="'.$jobcard['date'].'" type="text">
								</div>
							</div>
							<div class="col-md-3 type">
								<div class="form-group">
									<label>User</label>
									<span>'.$jobcard['username'].'</span>
									
								</div>
							</div>
							<div class="col-md-3 type">
								<div class="form-group">
									<label>Activity</label>
									<span>'.$jobcard['activity'].'</span>
									
								</div>
							</div>
							<div class="col-md-2 type">
								<div class="form-group">
									<label>Activity Unit</label>';
									if($jobcard['activity_unit']==''):
										$datarows.='<span>'.$actunit.'</span>';
									else:
										$datarows.='<span>'.$jobcard['activity_unit'].'</span>';
									endif;
								$datarows.='</div>
							</div>
							<div class="col-md-2 type">
								<div class="form-group">
									<label>Proposed Quantity</label>
									<span>'.$jobcard['act_quantity'].'</span>
									<!-- <input class="form-control" id="" name="" placeholder="Proposed Quantity" value="'.$jobcard['act_quantity'].'" type="text"> -->
									
								</div>
							</div>
							
						</div>
					</div>
					<div class="col-md-12">
						<table class="table table-bordered">
						<thead>
							<tr>
								<th>#</th>
								<th width="150">Resource</th>
								<th width="150">Unit</th>
								<th class="text-right">Estimated Qty</th>
								<th class="text-right">Received Qty</th>
								<th class="text-right">Issued Qty</th>
								<th class="text-right">Stock Qty</th>
								<th width="150" class="text-right">Proposed Qty</th>
								<th class="text-right">Remaining Qty</th>
							</tr>
						</thead><tbody>';
		foreach($dataProvider AS $key=>$data):
			$resestqty=PricingEstimateResourcesNew::find()->where(['resource_Id' => $data['resource']])->andWhere(['activity_id' => $data['app_activity']])->one();

			$sql1="SELECT SUM(quantity_used) AS total FROM site_report_resources WHERE activityid='".$activityid."' AND resource_id='".$data['resource']."' ";
			$command=$connection->createCommand($sql1);
			$dataReader=$command->query();
			$datares=$dataReader->read();

			$reslist="<select class='form-control reslist' id='reslist' name='reslist[]' data-id='".$data['job_id']."'>";
			$reslist.="<option value='none'>Select Resource</option></select>";

			$unit="<span id='resunit".$data['job_id']."'></span><input type='hidden' name='jobcardid[]' value='".$data['job_id']."'>";
			$actqty=PricingEstimateNew::find()->where(['activity_Id' => $jobcard['app_activity']])->one();
			$resqty=$resestqty->quantity * $actqty->activity_qty;

			$exstjobcards=Jobcard::find()->where(['=','resource',$data['resource']])->all();

			if(count($exstjobcards)>0):
				$totresqty=0;
				foreach($exstjobcards AS $exstjobcard):
					$totresqty=$totresqty + $exstjobcard['res_quantity'];
				endforeach;
				$exstresqty=$totresqty - $data['res_quantity'];
			else:
				$exstresqty=0;
			endif;

			if($data['status']==0):
				$style="background-color: lightgray;";
				$text="";
			else:
				$style="";
				$text="readonly";
			endif;

			// $invoices=InvoiceResources::model()->findAll(array('condition'=>'resource_id='.$data['resource'].' '));
			// if(count($invoices)>0):
			// 	$recresqty=0;
			// 	foreach($invoices AS $invoice):
			// 		$recresqty=$recresqty + $invoice['resource_qty'];
			// 	endforeach;
			// else:
			// 	$recresqty=0;
			// endif;
			$resourcemod=Resources::findOne($data['resource']);
	        $sql = "SELECT *  FROM `resources` WHERE `Name` LIKE '".$resourcemod->Name."' AND pricing_status=0";
	        //$sql = "SELECT *  FROM `resources` WHERE `Name` LIKE '8mm Plates' AND pricing_status=0";
	        //echo $sql;exit;
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
			$sql2="SELECT SUM(a.resource_qty) as usedqnty FROM invoice_resources as a INNER JOIN invoice as b on a.invoice_id=b.invoice_id WHERE a.resource_id IN (".$stresids.") AND b.status!=3  AND b.project_id=".$activity['project_Id']." ";
			$command = $connection->createCommand($sql2);
			$dataReader = $command->query();
			$dataused = $dataReader->read();
			$recresqty=$dataused['usedqnty'];
			//$issueslip=IssueSlips::model()->find(array('condition'=>'project_id='.$activity['project_Id'].' AND iow_id='.$activity['wbs_id'].' AND activity_id='.$jobcard['app_activity'].' AND resource_id='.$data['resource'].' AND delete_status=0'));
			$sql = "SELECT SUM(resource_qty) as issuedqty FROM `issue_slips` `t` WHERE project_id=".$activity['project_Id']." AND iow_id=".$activity['wbs_id']." AND activity_id=".$jobcard['app_activity']." AND resource_id IN (".$stresids.") AND delete_status=0";
			$command = $connection->createCommand($sql);
			$dataReader = $command->query();
			$issueslip = $dataReader->read();
			$stockqty=$recresqty - $issueslip['issuedqty'];
			//$remqty=$resqty - $exstresqty - $data['res_quantity'];
			$remqty=$resqty - $data['res_quantity'];
			$rate=Resources::findOne($data['resource'])->Price;
			$amount=$data['res_quantity'] * $rate;
			$datarows.='<tr>
							<td>'.($key + 1).'</td>
							<td>
							'.Resources::findOne($data['resource'])->Name.'
							<input type="hidden" name="resid[]" value="'.$data['resource'].'">
                        	<input type="hidden" name="resunit[]" value="'.$data['unit'].'">
							</td>
							<td>'.$data['unit'].'<input type="hidden" name="jobcardid[]" value="'.$data['job_id'].'"></td>
							<td class="text-right">'.($resqty).' </td>
							<td class="text-right">'.number_format((float)$recresqty, 2).'</td>
							<td class="text-right">'.number_format((float)$issueslip['issuedqty'], 2).'</td>
							<td class="text-right">'.number_format((float)$stockqty, 2).'</td>
							<td class="text-right"><input class="form-control" '.$text.' name="quantity[]" id="quantity'.$data['resource'].'" data-id="'.$data['resource'].'" placeholder="Unit" value="'.$data['res_quantity'].'" type="text"><span class="error" style="display: none;"></span></td>
							<td class="text-right">'.$remqty.'<input type="hidden" id="remqty'.$data['resource'].'" data-id="'.$data['resource'].'" value="'.$remqty.'"></td>
						</tr>';
		endforeach;
		$datarows.='</tbody>
					</table> 
					<div class="row">
						<div class="col-md-12 text-center">
							<label>&nbsp;</label>
							<button type="button" class="btn btn-danger cancel"><span class="icon-close"></span> Cancel</button>
						</div>
					</div>	
				</div>';
		// if($_POST['jobcardid'])
		// {
		// 	/*if($_GET['action']=='update'):
		// 		//$status=0;
		// 		$sql="SELECT name FROM jobcard ORDER BY name DESC limit 1";
		// 		$command=$connection->createCommand($sql);
		// 		$dataReader=$command->query();
		// 		$no=$dataReader->read();
		// 		$name=$no['name'] + 1;
		// 		$groupid=time();
		// 		$userid=Yii::app()->user->id;
		// 		for($i=0;$i<count($_POST['quantity']);$i++)
		// 		{
		// 			$sql = "INSERT INTO  jobcard (name,date,process,activity,groupid,project_id,resource,unit,act_quantity,res_quantity,user_id,app_activity) VALUES('".$name."','" .date('Y-m-d'). "','" . $jobcard['process'] . "','" .$jobcard['activity']. "','".$groupid."','".$jobcard['project_id']."','" . $_POST['resid'][$i] . "','" . $_POST['resunit'][$i] . "','" . $_POST['act_quantity'] . "','".$_POST['quantity'][$i]."','".$userid."','".$jobcard['app_activity']."')";
		// 			//echo $sql;exit;
		// 			$command = $connection->createCommand($sql);
		// 			$dataReader = $command->query();
		// 		}
		// 	endif;*/
		// 	$userid=Yii::$app->user->id;

		// 	for($i=0;$i<count($_POST['quantity']);$i++)
		// 	{
		// 		$newactqty=$_POST['act_quantity'];
		// 		$newresqty=$_POST['quantity'][$i];
		// 		$sql="UPDATE jobcard SET status='1',act_quantity='".$newactqty."',res_quantity='".$newresqty."',Approved_By='$userid',Approved_On='".date('y-m-d h:i:s')."' WHERE job_id='".$_POST['jobcardid'][$i]."' ";
		// 		//echo $sql;exit;
		// 		$command=$connection->createCommand($sql);
		// 		$dataReader=$command->query();
		// 	}
		// 	$this->redirect(array('projects/report'));
		// }
		$arr = array('result' => $datarows, 'error' => 'No');
        return json_encode($arr);
	}

	public function actionViewjobcard($id)
	{
		$connection = CActiveRecord::getDbConnection();
		$sql="SELECT a.process,DATE_FORMAT(a.date,'%d %b %y') AS date,a.activity,a.app_activity,a.act_quantity,a.project_id,a.startdate,a.enddate,b.username FROM jobcard as a left join user as b on a.user_id=b.id WHERE groupid='".$id."' ";
		$command=$connection->createCommand($sql);
		$dataReader=$command->query();
		$jobcard=$dataReader->read();

		$activityid=WorkgroupActivities::model()->findByPk($jobcard['app_activity'])->activity_Id;

		if($jobcard['process']==2):
			$actunit=ProjectSetup::model()->findByPk($activityid)->PS_Unit;
		elseif($jobcard['process']==3):
			$actunit=Products::model()->findByPk($activityid)->Unit;
		elseif($jobcard['process']==4):
			$actunit=Logistics::model()->findByPk($activityid)->Unit;
		elseif($jobcard['process']==5):
			$actunit=Construction::model()->findByPk($activityid)->CO_Unit;
		elseif($jobcard['process']==8):
			$actunit=Overheads::model()->findByPk($activityid)->Unit;
		endif;

		$sql = "SELECT distinct groupid FROM `jobcard` WHERE `app_activity` = ".$jobcard['app_activity']." ORDER BY `groupid` ASC ";
		$command = $connection->createCommand($sql);
		$dataReader = $command->query();
		$datavalues = $dataReader->readAll();
		$exstactqty=0;
		foreach($datavalues AS $datavalue):
			$asd=Jobcard::model()->find(array('condition'=>'groupid='.$datavalue['groupid'].' '));
			$exstactqty=$exstactqty + $asd['act_quantity'];
		endforeach;

		$sql1="SELECT job_id,resource,unit,act_quantity,res_quantity,app_activity,status ";
		$sql1.="FROM jobcard WHERE groupid='$id' ORDER BY resource DESC";
		//echo $sql1;
		$command=$connection->createCommand($sql1);
		$dataReader=$command->query();
		$dataProvider=$dataReader->readAll();
		$datarows="";
		$notinres="";
		foreach($dataProvider AS $key=>$data):
			if($data['status']==0):
				$btn="";
			else:
				$btn="disabled";
			endif;

			if($key=='0'):
				$notinres.="".$data['resource']."";
			else:
				$notinres.=",".$data['resource']."";
			endif;
			$resestqty=PricingEstimateResources::model()->find(array('condition'=>'resource_Id='.$data['resource'].' AND activity_id='.$data['app_activity'].' '))->quantity;
			$actqty=PricingEstimate::model()->find(array('condition'=>'activity_Id='.$jobcard['app_activity'].' '))->activity_qty;

			$resqty=$resestqty * $actqty;

			$remqty=$resqty - $data['res_quantity'];

			$datarows.="<tr id='jobcardrow".$data['job_id']."'>
                        <td>".($key + 1)."</td>
                        <td colspan='2'>".Resources::model()->findByPk($data['resource'])->Name."
                        <input type='hidden' name='resid[]' value='".$data['resource']."'>
                        <input type='hidden' name='resunit[]' value='".$data['unit']."'>
                        <input type='hidden' id='groupid' name='groupid' value='".$id."'>
                        </td>
                        <td>".$data['unit']."<input type='hidden' name='jobcardid[]' value='".$data['job_id']."'></td>
                        <td>".($resqty)."</td>
                        <td>".$data['res_quantity']."</td>
                        <td>
                        <input type='text' class='form-control propqty' id='propqty".$data['job_id']."' data-id='".$data['job_id']."' data-item='".$data['resource']."' name='propqty[]' value=''>
                        <span class='error' style='display:none'></span>
                        <input type='hidden' id='currentresqty".$data['job_id']."' value='".$data['res_quantity']."'>
                        </td>
                        <td>".$remqty."<input type='hidden' id='remqty".$data['resource']."' data-id='".$data['resource']."' value='".$remqty."'></td>
                        <td><button type='button' class='btn btn-primary removeresitem' id='removeresitem" . $data['job_id'] . "' value='" . $data['job_id'] . "'>Remove</button></td>
                        </tr>";
		endforeach;

		if (isset($_POST['Update'])) {
			for($i=0;$i<count($_POST['jobcardid']);$i++){
				$jobcardmodel=Jobcard::model()->findByPk($_POST['jobcardid'][$i]);
				if($_POST['propqty'][$i]!=0):
					$order=OrderedResource::model()->find(array('condition'=>'jobcard_id='.$_POST['jobcardid'][$i].' AND resource_id='.$_POST['resid'][$i].' '));
					$ordercount=count($order);
					$cart=Cart::model()->find(array('condition'=>'Job_Id='.$_POST['jobcardid'][$i].' AND Resource_Id='.$_POST['resid'][$i].' AND status=0'));
					$cartcount=count($cart);
					$totalcount=$ordercount + $cartcount;
					//echo $totalcount;exit;
					if($totalcount>0){
						$currentqty=$_POST['propqty'][$i];
						$exsqty=$jobcardmodel->res_quantity;
						$totalqty=$exsqty + $currentqty;
						$jobcardmodel->res_quantity=$totalqty;
						//$jobcardmodel->status=0;
						$jobcardmodel->cart_status=0;
						$jobcardmodel->save(false);
					}
					else{
						$currentqty=$_POST['propqty'][$i];
						$exsqty=$jobcardmodel->res_quantity;
						$totalqty=$exsqty + $currentqty;
						$jobcardmodel->res_quantity=$totalqty;
						//$jobcardmodel->status=0;
						//$jobcardmodel->cart_status=0;
						$jobcardmodel->save(false);
					}
				endif;
				if($_POST['activityqty']!=0):
					$currentqty=$_POST['activityqty'];
					$exsqty=$jobcardmodel->act_quantity;
					$totalqty=$exsqty + $currentqty;
					$jobcardmodel->act_quantity=$totalqty;
					$jobcardmodel->save(false);
				endif;
			}
			$this->redirect(array('projects/report'));
		}
		$this->render('viewjobcard',array(
				'datarows'=>$datarows,
				'jobcard'=>$jobcard,
				'exstactqty'=>$exstactqty,
				'actunit'=>$actunit,
				'notinres'=>$notinres,
		));
	}

	public function actionCheckorders()
	{
		$order=OrderedResource::model()->find(array('condition'=>'jobcard_id='.$_POST['jobcardid'].' AND resource_id='.$_POST['resourceid'].' '));
		$ordercount=count($order);
		$cart=Cart::model()->find(array('condition'=>'Job_Id='.$_POST['jobcardid'].' AND Resource_Id='.$_POST['resourceid'].' '));
		$cartcount=count($cart);
		$total=$ordercount + $cartcount;
		$arr = array('count'=>$total,'ordercount'=>$ordercount,'cartcount'=>$cartcount,'error' => 'No');
		echo json_encode($arr);
	}

	/*public function actionGetresources()
	{
		$connection = CActiveRecord::getDbConnection();
		if($_POST['process']==2):
			$sqltemvals="SELECT a.Resource_Id,a.Name AS resource FROM resources AS a
                INNER JOIN project_setup_resources AS b ON  a.Resource_Id=b.PSR_Resource_Id
                WHERE b.PSR_PS_Id='".$_POST['activityid']."' ORDER BY a.Resource_Id";
		elseif($_POST['process']==3):
			$sqltemvals="SELECT a.Name AS resource,a.Resource_Id FROM resources AS a
                INNER JOIN product_resources AS b ON  a.Resource_Id=b.Resource_Id
                WHERE b.Product_Id='".$_POST['activityid']."' ORDER BY a.Resource_Id";
		elseif($_POST['process']==4):
			$sqltemvals="SELECT a.Name AS resource,a.Resource_Id FROM resources AS a
                INNER JOIN logistics_resources AS b ON  a.Resource_Id=b.Resource_Id
                WHERE b.Logistics_Id='".$_POST['activityid']."' ORDER BY a.Resource_Id";
		elseif($_POST['process']==5):
			$sqltemvals="SELECT a.Name AS resource,a.Resource_Id FROM resources AS a
                INNER JOIN construction_resources AS b ON  a.Resource_Id=b.COR_Resource_Id
                WHERE b.COR_CO_Id='".$_POST['activityid']."' ORDER BY a.Resource_Id";
		elseif($_POST['process']==8):
			$sqltemvals="SELECT a.Name AS resource,a.Resource_Id FROM resources AS a
                INNER JOIN overheads_resources AS b ON  a.Resource_Id=b.Resource_Id
                WHERE b.Overhead_id='".$_POST['activityid']."' ORDER BY a.Resource_Id";
		endif;
		$command=$connection->createCommand($sqltemvals);
		$dataReader=$command->query();
		$resourcesadded=$dataReader->readAll();
		$resources=Resources::model()->findAll(array('order'=>'Name ASC'));
		$reslist="<option value='none'>Select Resource</option>";
		foreach($resourcesadded AS $resources):
			$reslist.="<option value='".$resources['Resource_Id']."'>".$resources['resource']."</option>";
		endforeach;
		$arr = array('result' => $reslist,'error'=>'No');
		echo json_encode($arr);
	}*/
	public function actionResourceunitupdate()
	{
		$resources=Resources::find()->where(['Resource_Id' => $_POST['resid']])->one();
		$resources->Unit=$_POST['unit'];
		$resources->save(false);

		$arr = array('resunit'=>$resources->Unit,'error'=>'No');
		return json_encode($arr);
	}
	public function actionResdetails()
	{
		$resources=Resources::find()->where(['Resource_Id' => $_POST['resid']])->one();
		//print_r($resources);exit;
		$restypeid=$resources->ResourceType_Id;

		$unit=$resources->Unit;
		//echo $unit;exit;

		$recresqty='';
		$resqty=PricingEstimateResourcesNew::find()->where(['resource_Id' => $_POST['resid']])->andWhere(['activity_id' => $_POST['activityid']])->andWhere(['pricing_status' => 0])->one();

		//var_dump($resqty);exit;
		///$jobcards=Jobcard::model()->findAll(array('condition'=>'resource='.$_POST['resid'].' '));
		$jobcards=Jobcard::find()->where(['resource' => $_POST['resid']])->andWhere(['app_activity' => $_POST['activityid']])->all();

		if(count($jobcards)>0):
			$exstresqty=0;
			$jobcardids='';
			foreach($jobcards AS $key=>$jobcard):
				if($key==0):
					$jobcardids.=$jobcard['job_id'];
				else:
					$jobcardids.=",".$jobcard['job_id'];
				endif;
				$exstresqty=$exstresqty + $jobcard['res_quantity'];
			endforeach;
		else:
			$exstresqty=0;
			$jobcardids=0;
		endif;

		if($jobcardids!=0):

			$orders=OrderedResource::find()->where(['in','jobcard_id',$jobcardids])->all();
			if(count($orders)>0):
				$orderids='';
				foreach($orders AS $key=>$order):
					if($key==0):
						$orderids.=$order['order_id'];
					else:
						$orderids.=",".$order['order_id'];
					endif;
				endforeach;
			else:
				$orderids=0;
			endif;
			if($orderids!=0):

				///$invoices=InvoiceResources::model()->findAll(array('condition'=>'resource_id='.$_POST['resid'].' '));
				$invoices=InvoiceResources::find()->where(['in','order_id',$orderids])->all();
				if(!empty($invoices)):

					/*$recresqty=0;
					foreach($invoices AS $invoice):
						$recresqty=$recresqty + $invoice['resource_qty'];
					endforeach;*/
					$connection = \Yii::$app->db;
					$sql2 = "SELECT SUM(a.resource_qty) as usedqnty FROM invoice_resources as a INNER JOIN invoice as b on a.invoice_id=b.invoice_id WHERE a.resource_id =".$_POST['resid']." AND b.status!=3";
					//echo $sql2;exit;
					$command = $connection->createCommand($sql2);
					$dataReader = $command->query();
					$dataused = $dataReader->read();
					$recresqty=$dataused['usedqnty'];
				else:
					$recresqty=0;
				endif;
			endif;
		endif;

		/*$invoices=InvoiceResources::model()->findAll(array('condition'=>'resource_id='.$_POST['resid'].' '));
		if(count($invoices)>0):
			$recresqty=0;
			foreach($invoices AS $invoice):
				$recresqty=$recresqty + $invoice['resource_qty'];
			endforeach;
		else:
			$recresqty=0;
		endif;*/

		$issueslip=IssueSlips::find()->where(['project_id' => $_POST['projectid']])->andWhere(['iow_id' => $_POST['iowid']])->andWhere(['activity_id' => $_POST['activityid']])->andWhere(['resource_id' => $_POST['resid']])->andWhere(['delete_status' =>0])->one();
		if(!empty($issueslip)){
			$issuedqty= $issueslip['resource_qty'];
		}else{
			$issuedqty='';
		}

		$arr = array('result' => $unit,'resqty'=>$resqty->quantity,'exstresqty'=>$exstresqty,'recresqty'=>$recresqty,'issuedqty'=>$issuedqty,'restype'=>$restypeid,'error'=>'No');
		return json_encode($arr);
	}

	public function actionDelete()
	{
		$id=$_POST['jobid'];
		$connection = \Yii::$app->db;
		if(isset($_POST['jobid'])):
			$jobcards=Jobcard::find()->where(['groupid' => $id])->all();
			$count=0;
			$orderid='';
			foreach($jobcards AS $key=>$jobcard):
				$order=OrderedResource::find()->where(['jobcard_id' => $jobcard['job_id']])->one();
				if(!empty($order)):
					$count++;
				endif;
				$orderid.=" ".$order['order_id'];
				/*if($key==0):
					$orderid.=$order['order_id'];
				else:
					$orderid.=",".$order['order_id'];
				endif;*/
			endforeach;

			if($count!=0):
				$arr = array('Id' => $_POST['jobid'],'error'=>'Yes','errortext'=>'Can not Delete this Jobcard as it is associate with Order ID '.$orderid.'');
				return json_encode($arr);
			else:
				//$sql="DELETE FROM jobcard WHERE groupid='$id'";
				$sql="UPDATE jobcard SET delete_status=1 WHERE groupid='$id'";
				$command=$connection->createCommand($sql);
				$dataReader=$command->query();
				$arr = array('Id' => $_POST['jobid'],'error'=>'No');
				return json_encode($arr);
			endif;
		endif;
	}

	public function actionDeleteactivity()
	{
		$jobcards=Jobcard::model()->findAll(array('condition'=>'app_activity='.$_POST['actid'].' AND status=1'));
		foreach($jobcards AS $jobcard):
			$jobcard->ar_status=1;
			$jobcard->save(false);
		endforeach;
		$arr=array('error'=>'No','activity'=>$_POST['actid']);
		echo json_encode($arr);
	}

	public function actionDeletemusterroll()
	{
		$groupid=$_POST['groupid'];
		$muster=Musterroll::updateAll(['Status' => 1], ['=', 'groupid', $groupid]);
		$arr=array('groupid'=>$groupid,'error'=>'No');
		return json_encode($arr);
	}

	public function actionDeletepractivity()
	{
		$wbsact=WorkgroupActivities::model()->findByPk($_POST['actid']);
		$wbsact->pr_status=1;
		$wbsact->save(false);
		$reports=NewReport::model()->findAll(array('condition'=>'wbs_activity_Id='.$_POST['actid'].' '));
		$repids='';
		foreach($reports AS $key=>$report):
			if($key==0):
				$repids.=$report['reportid'];
			else:
				$repids.=','.$report['reportid'];
			endif;
		endforeach;
		if($repids!=''):
			$reptasks=Reporttasks::model()->deleteAll(array('condition'=>'reportid IN ('.$repids.')'));
		endif;
		NewReport::model()->deleteAll(array('condition'=>'wbs_activity_Id='.$_POST['actid'].' '));
		$arr=array('activityid'=>$_POST['actid'],'error'=>'No');
		echo json_encode($arr);

	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Jobcard']))
		{
			$model->attributes=$_POST['Jobcard'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->job_id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	/*public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}*/

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Jobcard');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Jobcard('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Jobcard']))
			$model->attributes=$_GET['Jobcard'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Jobcard the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Jobcard::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Jobcard $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='jobcard-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
