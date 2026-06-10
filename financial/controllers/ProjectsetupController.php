<?php

namespace app\controllers;  

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\Projects;
use app\models\EstimateActivityType;
use app\models\PricingEstimateNew;
use app\models\Resourcetype;
use app\models\Resources;
use app\models\ResourceGroup;
use app\models\Vendors;
class ProjectsetupController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

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

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$model=$this->loadModel($id);
		$connection = CActiveRecord::getDbConnection();
		$sql="SELECT a.Name,b.PSR_Quantity,b.PSR_Price AS resprice FROM resources AS a
                INNER JOIN project_setup_resources AS b ON  a.Resource_Id=b.PSR_Resource_Id
                WHERE b.PSR_PS_Id='".$id."' ORDER BY a.Resource_Id";
		$command=$connection->createCommand($sql);
		$dataReader=$command->query();
		$dataProvider=$dataReader->readAll();
		$this->render('view',array(
				'model'=>$model,
				'dataProvider'=>$dataProvider,
		));
	}
	public function actionTask(){
		$model=ProjectSetup::model()->findByPk($_GET['id']);
		$this->render('tasklist',array(
				'model'=>$model));

	}
	public function actionListTask()
	{
		$connection = CActiveRecord::getDbConnection();
        $sql="SELECT Name AS Task,task_Id FROM tasks WHERE activity_Id ='".$_POST['id']."' AND process_id='2' ORDER BY task_Id ASC, Name ASC";
        $command = $connection->createCommand($sql);
        $dataReader = $command->query();
        $dataProvider = $dataReader->readAll();
        $datarows = '';
        if (count($dataProvider) > 0):
            foreach ($dataProvider AS $key => $data):
                $checkedActive = '';
                $checkedInActive = '';
                $datarows .= '<tr id="taskrow' . $data['task_Id'] . '" class="taskrow' . $data['task_Id'] . '">
                                <td class="small75">' . $data['task_Id'] . '</td>
                                <td><span id="tasktext' . $data['task_Id'] . '">' . $data['Task'] . '</span><input class="form-control edittask" type="text" style="display:none;" id="edittask' . $data['task_Id'] . '" value="' . $data['Task'] . '"><span class="error"></span></td>
								<td class="small75" style="width: 120px !important">
                                <button type="button" class="btn btn-primary edittask_button" value="'.$data['task_Id'].'" id="edittask_button'.$data['task_Id'].'"><span class="glyphicon glyphicon-pencil"></span></button>
								<button type="button" class="btn btn-primary savetaskbutton" style="display:none;" value="'.$data['task_Id'].'" id="savetaskbutton'.$data['task_Id'].'">
                                <span class="glyphicon glyphicon-save"></span></button>
								</td>
                                <td class="small75" style="width: 120px !important">
                                <button type="button" class="btn btn-primary deletetask_prosetup" id="deletetask_prosetup" value="'.$data['task_Id'].'"><span class="glyphicon glyphicon-trash"></span></button>                         
								</td>
                             </tr>';
            endforeach;
        else:
            $datarows = '<tr id="nodata"><td colspan="8" style="text-align: center;">No Tasks Found</td></tr>';
        endif;
        $arr = array('result' => $datarows, 'error' => 'No');
        echo json_encode($arr);

	}
	public function actionSearch()
	{
		$connection = CActiveRecord::getDbConnection();
		$sql="SELECT PS_Id,PS_Name,PS_Unit,PS_Price AS Price,PS_Quantity,PS_Amount,sortorder FROM project_setup WHERE PS_Status='0' ";

		if($_POST['investmentname']!='')
			$sql.="AND PS_Name LIKE '%".$_POST['investmentname']."%'";
		$sql.="ORDER BY sortorder ASC";
		$command=$connection->createCommand($sql);
		$dataReader=$command->query();
		$dataProvider=$dataReader->readAll();
		$datarows='';
		if(count($dataProvider)>0):
			foreach($dataProvider AS $key=>$data):
				$datarows.="<tr id='prosetuprow".$data['PS_Id']."' class='ui-state-default no prosetup".$data['PS_Id']."' data-id='".$data['PS_Id']."'>
                                <td  class='small75'>".$data['sortorder']."</td>
								
								
                                <td><span id='overheadname".$data['PS_Id']."'>".$data['PS_Name']."</span>
                                <input class='form-control editprosetupname' type='text' id='editprosetupname".$data['PS_Id']."' value='".$data['PS_Name']."' style='display:none'>
                                <span class='error'></span></td>
                                <td><span id='overheadunit".$data['PS_Id']."'>".$data['PS_Unit']."</span>
                                <input class='form-control editprosetupunit' type='text' id='editprosetupunit".$data['PS_Id']."' value='".$data['PS_Unit']."'  style='display:none'>
                                <span class='error'></span></td>
                                <td ><span id='investmentamount".$data['PS_Id']."'>".$data['Price']."</span>
                                <input class='form-control editinvestmentamount listinvestment' type='hidden' id='editinvestmentamount".$data['PS_Id']."' value='".$data['PS_Price']."'></td>
                                <td class='small75'>
                                    <a href='".Yii::app()->request->baseUrl."/ProjectSetup/task?id=".$data['PS_Id']."'>
                                    <button type='button' title='Task Management' class='btn btn-primary taskmanagement' value='".$data['PS_Id']."' id='taskmanagement".$data['PS_Id']."'> <span >Tasks</span></button></a>
                                </td>
								<td class='small75'>
                                    <a href='".Yii::app()->request->baseUrl."/ProjectSetup/update?id=".$data['PS_Id']."'>
                                    <button type='button' title='Edit investment' class='btn btn-primary editinvestment' value='".$data['PS_Id']."' id='editinvestmentbutton".$data['PS_Id']."'> <span >Estimate</span></button></a>
                                </td>
                                <td class='small75'>
								    <button type='button' class='btn btn-primary editprojectsetup' value='" . $data['PS_Id'] . "' id='editprojectsetup" . $data['PS_Id'] . "' title='Edit Project Setup'> <span class='glyphicon glyphicon-pencil'></span></button><button type='button' class='btn btn-primary saveprojectsetup' value='" . $data['PS_Id'] . "' id='saveprojectsetupbutton" . $data['PS_Id'] . "' title='Save' style='display:none;'> <span class='glyphicon glyphicon-save'></span></button>
                                    
                                </td>
								<td class='small75'>
                                    <button type='button' class='btn btn-primary deleteprojectsetup' value='".$data['PS_Id']."' id='deleteprojectsetup".$data['PS_Id']."' title='Delete investment'> <span class='glyphicon glyphicon-trash'></span></button>
                                </td>
                            </tr>";
			endforeach;
		else:
			$datarows='<tr id="nodata"><td colspan="7" style="text-align: center;">No Project Setups Found</td></tr>';
		endif;
		$arr = array('result' => $datarows,'error'=>'No');
		echo json_encode($arr);
	}
	
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$connection = CActiveRecord::getDbConnection();
		$sql="SELECT Name FROM resources  WHERE status='0' ORDER BY Resource_Id ASC, Name ASC";
		$command=$connection->createCommand($sql);
		$dataReader=$command->query();
		$resources=$dataReader->readAll();
		$dataProvider='';
		foreach($resources AS $key=>$res):
			if($key==0):
				$dataProvider="'".$res['Name']."'";
			else:
				$dataProvider.=",'".$res['Name']."'";
			endif;
		endforeach;
		if(isset($_POST['Setup_Name'])):
			$model= new ProjectSetup();
			$model->PS_Name=$_POST['Setup_Name'];
			$model->PS_Unit=$_POST['Setup_Unit'];
			if($model->save(false)):
				$this->redirect(array('projects/masters#Projectsetup'));
			endif;
		endif;
		$this->render('create',array(
				'dataProvider'=>$dataProvider,
		));
	}
	public function actionTaskCreate(){
			// var_dump($_POST['taskname']);
			// return;
		if (isset($_POST['taskname'])) {
			// var_dump($_POST['taskname']);
			// exit;
            $model = new Tasks;
			$model->activity_Id = $_POST['activityid'];
            $model->Name = $_POST['taskname'];
			$model->process_id = 2;
       
	        if ($model->save()):   
			    $arr = array('Id' => $model->task_Id, 'Name' => $model->Name, 'error' => 'No');
                echo json_encode($arr);
            else:
                $arr = array('error' => 'Yes', 'errortext' => 'Cannot add now. Please try again later');
                echo json_encode($arr);
            endif;
        }


	}
	public function actionUpdateTask(){
	
		// var_dump($_POST['taskid']);
		// var_dump($_POST['name']);

		if (isset($_POST['taskid']) && isset($_POST['name'])):
                // update into tasks
                $connection = CActiveRecord::getDbConnection();
				$sql = "UPDATE tasks SET Name = '".$_POST['name']."' WHERE task_Id = '" . $_POST['taskid'] . "'";
                $command = $connection->createCommand($sql);
                $dataReader = $command->query();
                $arr = array('Id' => $_POST['taskid'], 'error' => 'No');
				// var_dump(json_encode($arr));
                echo json_encode($arr);
        else:
            $arr = array('Id' => $_POST['taskid'], 'error' => 'Yes', 'errortext' => 'Can not Delete task with ID ' . $_POST['taskid'] . '.');
            echo json_encode($arr);
            
       endif;

	}

	public function actionDeleteTask(){
		
		if (isset($_POST['taskid'])):
            
                // delete from work groups
                $connection = CActiveRecord::getDbConnection();
                $sql = "DELETE FROM tasks WHERE task_Id='" . $_POST['taskid'] . "'";
                $command = $connection->createCommand($sql);
                $dataReader = $command->query();
                $arr = array('Id' => $_POST['taskid'], 'error' => 'No');
				// var_dump($arr);
                echo json_encode($arr);
        else:
            $arr = array('Id' => $_POST['taskid'], 'error' => 'Yes', 'errortext' => 'Can not Delete task with ID ' . $_POST['taskid'] . '.');
            echo json_encode($arr);
            
       endif;
	}
	public function actionCheckname()
	{
		$connection = CActiveRecord::getDbConnection();
		$sql="SELECT COUNT(*) AS total FROM investments WHERE Name='".$_POST['name']."' ";
		$command=$connection->createCommand($sql);
		$dataReader=$command->query();
		$data=$dataReader->read();
		if($data['total']>0):
			echo 'Yes';
		else:
			echo 'No';
		endif;

	}
	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate()
	{


		$model=ProjectSetup::model()->findByPk($_GET['id']);

		$connection = CActiveRecord::getDbConnection();
		$sqltemvals="SELECT a.Name AS resource,a.Unit,a.ResourceType_Id,a.Resource_Location,b.* FROM resources AS a
                INNER JOIN project_setup_resources AS b ON  a.Resource_Id=b.PSR_Resource_Id
                WHERE b.PSR_PS_Id='".$_GET['id']."' ORDER BY a.Resource_Id";
		// echo $sqltemvals;exit;
		$command=$connection->createCommand($sqltemvals);
		$dataReader=$command->query();
		$resourcesadded=$dataReader->readAll();
		$sql="SELECT Name FROM resources  WHERE status='0' ORDER BY Resource_Id ASC, Name ASC";
		$command=$connection->createCommand($sql);
		$dataReader=$command->query();
		$resources=$dataReader->readAll();
		foreach($resources AS $key=>$res):
			if($key==0):
				$dataProvider="'".$res['Name']."'";
			else:
				$dataProvider.=",'".$res['Name']."'";
			endif;
		endforeach;
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		if($_POST['Setup_Name'])
		{
			$model->PS_Name=$_POST['Setup_Name'];
			$model->PS_Unit=$_POST['Setup_Unit'];
			$model->PS_Price=$_POST['Consumables_Rate'];
			if($model->save(false)):
				for($i=0;$i<count($_POST['resourceid']);$i++)
				{
					$amount=($_POST['resourceqty'][$i] * $_POST['resourcerate'][$i]);
					$sql="UPDATE project_setup_resources SET PSR_Quantity='".$_POST['resourceqty'][$i]."',PSR_Price='$amount',PSR_Actual_Price='".$_POST['resourcerate'][$i]."' WHERE PSR_Id='".$_POST['resourceid'][$i]."' ";
					// echo $sql;exit;
					$command=$connection->createCommand($sql);
					$dataReader=$command->query();
				}
				$this->redirect(array('engineering/index#worktype'));

			endif;
		}

		$this->render('update',array(
				'model'=>$model,
				'resourcesadded'=>$resourcesadded,
				'dataProvider'=>$dataProvider,
		));
	}
	public function actionResourcesearch()
	{ 
		$connection = CActiveRecord::getDbConnection();
		$sql="SELECT Resource_Id,Name,Unit,Price,ResourceType_Id,Resource_group_Id,Resource_Location FROM resources WHERE status='0' AND Resource_group_Id='2'";

		/*if($_POST['resourcetype']!='none')
			$sql.="AND ResourceType_Id='".$_POST['resourcetype']."'";*/
		if($_POST['resourcename']!='')
			$sql.="AND Name LIKE '%".$_POST['resourcename']."%'";
		$sql.=" ORDER BY Resource_Id ASC, Name ASC";
		$command=$connection->createCommand($sql);
		$dataReader=$command->query();
		$dataProvider=$dataReader->readAll();
		$datarows='';

		if(count($dataProvider)>0):
			foreach($dataProvider AS $key=>$data):
				$datarows.="<tr id='resourcerow".$data['Resource_Id']."' class='resourcetype".$data['ResourceType_Id']."'>
				<td>".Resourcetype::model()->findByPk( $data['ResourceType_Id'])->Name."</td>
                            <td>".$data['Name']."</td>
                            <td>".$data['Resource_Location']."</td>
                            <td>".$data['Unit']."</td>
                            <td>".$data['Price']." <input type='hidden' id='resourceprice".$data['Resource_Id']."' value='".$data['Price']."'></td>
                            <td><input type='text' id='specificrate".$data['Resource_Id']."' value='".$data['Price']."' class='form-control' placeholder='Specific rate'></td>
                            <td><input type='text' id='resourcequantity".$data['Resource_Id']."' class='form-control' placeholder='Quantity'><span class='error'></span></td>
                            <td class='small75'><button type='button' class='btn btn-primary addresource' value='".$data['Resource_Id']."' id='addresourcebutton".$data['Resource_Id']."' title='Add Item'> <span class='glyphicon glyphicon-upload'></span></button></td>
                        </tr>";
			endforeach;
		else:
			$datarows='<tr id="nodata"><td colspan="5" style="text-align: center;">No Resources Found</td></tr>';
		endif;
		$arr = array('result' => $datarows,'error'=>'No');
		echo json_encode($arr);
	}
	public function actionResourcesearchbytyid()
	{  

		$restypeid = $_POST['resourcetypeid'];
		$resourcegroupid='';
		$vendorid='';

		$_POST['resgroup'] = trim($_POST['resgroup']," ");

		if(isset($_POST['resgroup']) && $_POST['resgroup']!='' )
		{
			$model = Resources::find()->where(['LIKE','Name',$_POST['resgroup'] ])->one();
			$resourcegroupid = $model->Resource_group_Id;

		}
		if(isset($_POST['vendorid']))
		{
			 $vendorid = $_POST['vendorid'];
		}

		$connection = \Yii::$app->db; 
		$activitynameval='';
		if(isset($_POST['activity_id']))
		{
		$sqlactivity="SELECT * FROM estimateactivities WHERE activity_id=".$_POST['activity_id']."";
        $command=$connection->createCommand($sqlactivity);
		$dataReader=$command->query();
		$activityname=$dataReader->read();
		 	if($activityname){
        		$activitynameval.= 'Activity: '.$activityname['activity_name'].'  Estimation: '.$activityname['activity_rate'];
    		}
        }

        $act_qty = 0;
        $act_qtys = PricingEstimateNew::find()->where(['activity_Id'=>$_POST['activity_id']])->one();
        if($act_qtys)
        {
        	$act_qty = $act_qtys->activity_qty;
        }
        $act_id = $_POST['activity_id'];
       
 
		$sql="SELECT Resource_Id,Name,Unit,fuel_unit,Price,ResourceType_Id,Resource_group_Id,Resource_Location,Vendor_Id FROM resources WHERE Status='0' AND pricing_status=0";

		if($restypeid!='')
			$sql.=" AND ResourceType_Id ='".$restypeid."' ";

		//if($resourcegroupid!='none' && $resourcegroupid!='')
		if(isset($_POST['resgroup']) && $_POST['resgroup']!='' )
			$sql.=" AND Name LIKE '%".$_POST['resgroup']."%' ";

        if($vendorid!='none' && $vendorid!='')
            $sql.=" AND Vendor_Id ='".$vendorid."' ";
        if(isset($_POST['name']))
		if($_POST['name']!='')
			$sql .= " AND Name LIKE '%" . $_POST['name'] . "%'";
		$sql.=" GROUP BY Name ORDER BY Resource_Id DESC, Name DESC";
			//		echo $sql; exit;
		$command=$connection->createCommand($sql);
		$dataReader=$command->query();
		$dataProvider=$dataReader->readAll();
		$datarows='';
/*
		$grouplist = ResourceGroup::find()->where("ResourceType_Id" =>  $restypeid])->all();

		$resgpname = "<option value='none'>Select Resource Group</option>";
		foreach($grouplist AS $list):
			if($resourcegroupid==$list->Resource_group_Id):
				$selected='selected';
			else:
				$selected='';
			endif;
			$resgpname .="<option value='".$list->Resource_group_Id."' ".$selected.">".$list->Resource_group_Name."</option>";
		endforeach;
		$restypename=Resourcetype::find($restypeid)->one()->Name; 

		if($resourcegroupid!='none' && $resourcegroupid!=''):
			$groupname=ResourceGroup::find($resourcegroupid)->one()->Resource_group_Name; 
		else:
			$groupname='';
		endif;*/

		if(count($dataProvider)>0):
			$sno=0;
			foreach($dataProvider AS $key=>$data):
				$sno++;
				$sql1='SELECT Resource_Id,Name,Unit,fuel_unit,Price,ResourceType_Id,Resource_group_Id,Resource_Location,Vendor_Id,rate,consumption,maintenance,machinecost FROM resources WHERE Status="0" AND pricing_status=0 AND Name="'.$data['Name'].'" ';
				if($restypeid!='')
					$sql1.="AND ResourceType_Id ='".$restypeid."' ";
				//if($resourcegroupid!='none' && $resourcegroupid!='')
					//$sql1.=" AND Name LIKE '%".$_POST['resgroup']."%' ";
				if($vendorid!='none' && $vendorid!='')
					$sql1.="AND Vendor_Id ='".$vendorid."' ";
				//if(isset($_POST['name']))
				//if($_POST['name']!='')
					//$sql1 .= "AND Name LIKE '%" . $_POST['name'] . "%'";
				$sql1.=" ORDER BY Resource_Id ASC, Name ASC";
				//echo $sql1;exit;
				$command=$connection->createCommand($sql1);
				$dataReader=$command->query();
				$childdataProvider=$dataReader->readAll();
				$childdatarows='';
				foreach($childdataProvider AS $childdata):
					$vendor=Vendors::find()->where(['Vendor_Id'=>$childdata['Vendor_Id']])->one();
					$vendorname='';
					if($vendor)
					{
						$vendorname=$vendor->Name." ".$vendor->Brand;
					} 

					if(isset($_POST['mode'])){
						if($restypeid==26){

							$childdatarows.='<div class="row">
												<div class="col-md-12 type">
													<div class="col-md-6">
														<span class="c-name">'.$vendorname.'</span>
													</div>
												<div class="col-md-6 machine-radio">
													<label class="radio-inline">
													<input type="radio" id="no_of_hrs" name="pequ-select" value="plant_no_of_hrs" data-resid="'.$childdata['Resource_Id'].'">Operation Cost
													</label>
													<label class="radio-inline">
													<input type="radio" id="lease_period" name="pequ-select" data-resid="'.$childdata['Resource_Id'].'" value="depreciation">Depreciation
													</label>
												</div>
											</div>
											<div class="plantrow'.$childdata['Resource_Id'].'">
												<div class="col-md-6">
													<div class="row">
														<div class="col-md-3 type">
															<label>Location</label>
															<span>'.$childdata['Resource_Location'].'</span>
														</div>
														
														<!--<div class="col-md-2 type">
															<label>Nos</label>
															<input style="text-align: right;" id="estimate-batchingunit'.$childdata['Resource_Id'].'" type="text" class="form-control" value="" />
														</div>-->
														<div class="col-md-3 type">
															<label>Fuel Type</label>
															<select class="form-control fueltype" name="fuelstyp" data-id='.$childdata['Resource_Id'].' type="text" id="fueltype'.$childdata['Resource_Id'].'">';

															$enggselected = '';
															$motoselected = '';
															$head='';
															
																if($childdata['Resource_group_Id'] == 102)
																{
																$enggselected='selected';
																$head1 = 'Fuel (Ltr/Hr)';
																$head2 = 'Fuel Rate';
																}elseif($childdata['Resource_group_Id'] == 154)
																{
																$motoselected='selected';
																$head1 = 'Power (KWh)';
																$head2 = 'Power Rate';
																}else{
																	$enggselected='selected';
																	$head1 = 'Fuel (Ltr/Hr)';
																	$head2 = 'Fuel Rate';
											
																  }


                                        		$childdatarows.='<option value="Petrol">Petrol</option>
                                        						<option value="Diesel" '.$enggselected.'>Diesel</option>
																<option value="Power" '.$motoselected.'>Power</option>
                                        					</select>
														</div>
														<div class="col-md-3 type">
															<div class="form-group">
																<label id="rateh'.$childdata['Resource_Id'].'">'.$head2.'</label>
																<input style="text-align: right;" name="esteqrate" data-id="'.$childdata['Resource_Id'].'" id="estimate-fuelrate'.$childdata['Resource_Id'].'" type="text" class="form-control estimate-fuelrate" value="'.$childdata['rate'].'" />
															</div>
															
														</div>

														<div class="col-md-3 type">
															<div class="form-group">
																<label id="fueh'.$childdata['Resource_Id'].'">'.$head1.'</label>
																<input style="text-align: right;" name="estequnt" data-id="'.$childdata['Resource_Id'].'" id="estimate-fuelqty'.$childdata['Resource_Id'].'" type="text" class="form-control" value="'.$childdata['consumption'].'" />
															</div>
															
														</div>
													</div>
												</div>
												<div class="col-md-6" style="padding-right:25px">
													<div class="row">
														
														
														
														<div class="col-md-3 type">
															<div class="form-group">
																<label>Maint Cost/Hr</label>
																<input style="text-align: right;" name="esteqmaint" data-id="'.$childdata['Resource_Id'].'" id="estimate-repair'.$childdata['Resource_Id'].'" data-id="'.$childdata['Resource_Id'].'" type="text" class="form-control estimate-repair" value="'.$childdata['maintenance'].'" />
															</div>
															
														</div>
														<div class="col-md-3 type">
															<div class="form-group">
																<label>Machine Rate</label>
																<input style="text-align: right;" id="estimate-machnrate'.$childdata['Resource_Id'].'" type="text" class="form-control" value="'.$childdata['machinecost'].'" />
															</div>
															
														</div>

														<div class="col-md-3 type">
															<div class="form-group">
																<label>No of Hrs</label>
																<input id="estimate-resourcetime'.$childdata['Resource_Id'].'" data-id="'.$childdata['Resource_Id'].'" type="text" class="form-control estimate-resourcetime" value="" />
																<span style="font-size:11px; color: red;font-weight: normal;"  class="error" style="display: none;"></span>
															</div>
														</div>
														
														<!-- <div class="col-md-2 type">
															<div class="form-group">
																<label>Amount</label>
																<input style="text-align: right;" id="estimate-acamnt'.$childdata['Resource_Id'].'" type="text" class="form-control"  readonly/>
															</div>
															
														</div> -->
														<div class="col-md-3 type">
															<div class="form-group">
																<label>&nbsp;</label>
																<button value="'.$childdata['Resource_Id'].'" class="btn btn-primary add-alloc-item estimate-addresource" ><span class="icon-add"></span>Add</button>
															</div>
														</div>
													</div>
												</div>
												</div>
											</div>';

						}elseif($restypeid==24){

							$childdatarows.='<div class="row">
												<div class="col-md-12 type">
													<div class="col-md-6">
														<span class="c-name">'.$vendorname.'</span>
													</div>
													<div class="col-md-6 machine-radio">
														<label class="radio-inline">
													      <input type="radio" id="no_of_hrs" name="equ-select" value="no_of_hrs" data-resid="'.$childdata['Resource_Id'].'">Operation Cost
													    </label>
													    <label class="radio-inline">
													      <input type="radio" id="lease_period" name="equ-select" data-resid="'.$childdata['Resource_Id'].'" value="lease_period">Lease Period
													    </label>
													</div>
												</div>
												<div class="leasedrow'.$childdata['Resource_Id'].'">
													<div class="col-md-6">
														<div class="row">
															<div class="col-md-3 type">
																<label>Location</label>
																<span>'.$childdata['Resource_Location'].'</span>
															</div>
															<div class="col-md-3 type">
																<label>Unit</label>
																<span>No of Hours</span>
																
															</div>
															<!--<div class="col-md-2 type">
																<label>Nos</label>
																<input style="text-align: right;" id="estimate-batchingunit'.$childdata['Resource_Id'].'" type="text" class="form-control" value="" />
															</div>-->
															<div class="col-md-3 type">
																<label>Fuel Type</label>
																<select class="form-control fueltype" name="fuelleastyp" data-id='.$childdata['Resource_Id'].' type="text" id="fueltype'.$childdata['Resource_Id'].'">';

																$enggselected = '';
																$motoselected = '';
																$head1 = '';
																$head2 = '';
																
																if($childdata['Resource_group_Id'] == 102)
																{
																$enggselected='selected';
																$head1 = 'Fuel (Ltr/Hr)';
																$head2 = 'Fuel Rate';
																}elseif($childdata['Resource_group_Id'] == 154)
																{
																$motoselected='selected';
																$head1 = 'Power (KWh)';
																$head2 = 'Power Rate';
																}else{
																	$enggselected='selected';
																	$head1 = 'Fuel (Ltr/Hr)';
																	$head2 = 'Fuel Rate';
											
																}


													$childdatarows.='<option value="Petrol">Petrol</option>
																	<option value="Diesel" '.$enggselected.'>Diesel</option>
																	<option value="Power" '.$motoselected.'>Power</option>
	                                        					</select>
															</div>
															<div class="col-md-3 type">
																<div class="form-group">
																	<label id="lrateh'.$childdata['Resource_Id'].'">'.$head2.'</label>
																	<input style="text-align: right;" id="estimate-fuelrate'.$childdata['Resource_Id'].'" type="text" class="form-control estimate-fuelrate" value="'.$childdata['rate'].'" />
																</div>
																
															</div>
														</div>
													</div>
													<div class="col-md-6" style="padding-right:25px">
														<div class="row">
															
															<div class="col-md-4 type">
																<div class="form-group">
																	<label id="lfueh'.$childdata['Resource_Id'].'">'.$head1.'</label>
																	<input style="text-align: right;" id="estimate-fuelqty'.$childdata['Resource_Id'].'" type="text" class="form-control" value="'.$childdata['consumption'].'" />
																</div>
																
															</div>
															
															<!--<div class="col-md-3 type">
																<div class="form-group">
																	<label>R&M</label>
																	<input style="text-align: right;" id="estimate-repair'.$childdata['Resource_Id'].'" data-id="'.$childdata['Resource_Id'].'" type="text" class="form-control estimate-repair" value="" />
																</div>
																
															</div>-->

															<div class="col-md-4 type">
																<div class="form-group">
																	<label>No of Hrs</label>
																	<input id="estimate-resourcetime'.$childdata['Resource_Id'].'" data-id="'.$childdata['Resource_Id'].'" type="text" class="form-control estimate-resourcetime" value="" />
																	<span style="font-size:11px; color: red;font-weight: normal;"  class="error" style="display: none;"></span>
																</div>
															</div>
															
															<!-- <div class="col-md-3 type">
																<div class="form-group">
																	<label>Amount</label>
																	<input style="text-align: right;" id="estimate-acamnt'.$childdata['Resource_Id'].'" type="text" class="form-control"  readonly/>
																</div>
																
															</div> -->
															<div class="col-md-3 type">
																<div class="form-group">
																	<label>&nbsp;</label>
																	<button value="'.$childdata['Resource_Id'].'" class="btn btn-primary add-alloc-item estimate-addresource" ><span class="icon-add"></span>Add</button>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>';


						}
						elseif($restypeid==33)
						{

							/* $childdatarows.='<div class="row">
												<div class="col-md-12 type">
													<span class="c-name" style="display:none;">'.$vendorname.'</span>
												</div>
												<div class="col-md-5">
													<div class="row">
														<div class="col-md-4 type">
															<label>Vendor</label>
															<span>'.$vendorname.'</span>
														</div>
														<div class="col-md-4 type">
															<label>Location</label>
															<span>'.$childdata['Resource_Location'].'</span>
														</div>
														
														<div class="col-md-4 type">
															<label>Unit</label>
															<span>'.$childdata['Unit'].'</span>
														</div>
														<div class="col-md-4 type">
														
														</div>
													</div>
												</div>
												<div class="col-md-7">
													<div class="row">
														<div class="col-md-4 type">
															<div class="form-group">
																<label>Quantity</label>
																<input style="text-align: right;" id="estimate-no-ofdays'.$childdata['Resource_Id'].'" type="text" class="form-control" value="" />
															</div>
															
														</div>
														<div class="col-md-5 type">
															<div class="form-group">
																<label>Rate</label>
																<input style="text-align: right;" id="estimate-specificrate'.$childdata['Resource_Id'].'" type="text" class="form-control" value="'.number_format($childdata['Price'],2).'" />
															</div>
															
														</div>
														<div class="col-md-3 type">
															<div class="form-group">
																<label>&nbsp;</label>
																<button value="'.$childdata['Resource_Id'].'" class="btn btn-primary add-alloc-item estimate-addresource" ><span class="icon-add"></span>Add</button>
															</div>
														</div>
													</div>
												</div>
											</div>';*/

											$childdatarows.='<div class="row">
												<div class="col-md-12 type">
													<span class="c-name">'.$vendorname.'</span>
												</div>
												<div class="col-md-5">
													<div class="row">
														<div class="col-md-4 type">
															<label>Location</label>
															<span>'.$childdata['Resource_Location'].'</span>
														</div>
														<div class="col-md-4 type">
															<label>Unit</label>
															<span>'.$childdata['Unit'].'</span>
														</div>

														<div class="col-md-4 type">
															<div class="form-group">
																<label>Rate</label>
																<input style="text-align: right;" id="estimate-specificrate'.$childdata['Resource_Id'].'" type="text" class="form-control" value="'.number_format($childdata['Price'],2).'" />
															</div>
															
														</div>

														
													</div>
												</div>
												<div class="col-md-7">
													<div class="row">

														<div class="col-md-3 type">
															<div class="form-group">
																<label>No of men</label>
																<input style="text-align: right;" id="estimate-mannumbr'.$childdata['Resource_Id'].'"  data-id='.$childdata['Resource_Id'].' data-qty='.$act_qty.'  type="text" class="form-control mannumbr" value="" />
															</div>
														</div>


														<div class="col-md-3 type">
															<div class="form-group">
																<label>No of days</label>
																<input style="text-align: right;" id="estimate-no-ofdays'.$childdata['Resource_Id'].'" data-id='.$childdata['Resource_Id'].' type="text" class="form-control ofdays" data-qty='.$act_qty.' value="" />
															</div>
															
														</div>

														<div class="col-md-3 type">
															<div class="form-group">
																<label>Man Days</label>
																<input style="text-align: right;" id="estimate-no-ofmdays'.$childdata['Resource_Id'].'" data-id='.$childdata['Resource_Id'].' type="text" class="form-control manvalue" value="" disabled />
															</div>
															
														</div>

														
														<div class="col-md-3 type">
															<div class="form-group">
																<label>&nbsp;</label>
																<button value="'.$childdata['Resource_Id'].'" data-qty='.$act_qty.' class="btn btn-primary add-alloc-item estimate-addresource btnclass'.$childdata['Resource_Id'].' actid'.$act_id.'" ><span class="icon-add"></span>Add</button>
															</div>
														</div>
													</div>
												</div>
											</div>';
						}
						else
						{

							$childdatarows.='<div class="row">
												<div class="col-md-12 type">
													<span class="c-name">'.$vendorname.'</span>
												</div>
												<div class="col-md-5">
													<div class="row">
														<div class="col-md-4 type">
															<label>Location</label>
															<span>'.$childdata['Resource_Location'].'</span>
														</div>
														<div class="col-md-4 type">
															<label>Unit</label>
															<span>'.$childdata['Unit'].'</span>
														</div>
														<div class="col-md-4 type">
															<label>Rate</label>
															<span>'.number_format($childdata['Price'],2).'</span>
															<input type="hidden" id="estimate-resourceprice'.$childdata['Resource_Id'].'" value="'.$childdata['Price'].'">
														</div>
													</div>
												</div>
												<div class="col-md-7">
													<div class="row">
														<div class="col-md-5 type">
															<div class="form-group">
																<label>Specific Rate</label>
																<input style="text-align: right;" id="estimate-specificrate'.$childdata['Resource_Id'].'" type="text" class="form-control" value="'.number_format($childdata['Price'],2).'" />
															</div>
															
														</div>
														<div class="col-md-4 type">
															<div class="form-group">
																<label>Quantity</label>
																<input id="estimate-resourcequantity'.$childdata['Resource_Id'].'" type="text" class="form-control" value="" placeholder="Quantity" />
																<span style="font-size:11px; color: red;font-weight: normal;"  class="error" style="display: none;"></span>
															</div>
														</div>
														<div class="col-md-3 type">
															<div class="form-group">
																<label>&nbsp;</label>
																<button value="'.$childdata['Resource_Id'].'" class="btn btn-primary add-alloc-item estimate-addresource" ><span class="icon-add"></span>Add</button>
															</div>
														</div>
													</div>
												</div>
											</div>';

						}

					}
					else{

						$childdatarows.='<input type="hidden" id="mastalloc" value="'.$restypeid.'" >';
						if($restypeid==26){

							$childdatarows.='<div class="row">
												<div class="col-md-12 type">
													<div class="col-md-6">
														<span class="c-name">'.$vendorname.'</span>
													</div>
												<div class="col-md-6 machine-radio">
													<label class="radio-inline">
													<input type="radio" id="no_of_hrs" data-type="'.$restypeid.'" name="mpequ-select" value="mplant_no_of_hrs" data-resid="'.$childdata['Resource_Id'].'">Operation Cost
													
													</label>
													<label class="radio-inline">
													<input type="radio" id="lease_period" data-type="'.$restypeid.'" name="mpequ-select" data-resid="'.$childdata['Resource_Id'].'" value="mdepreciation">Depreciation
													</label>
													
												</div>
											</div>
											<div class="plantrow'.$childdata['Resource_Id'].'">
												<div class="col-md-6">
													<div class="row">
														<div class="col-md-3 type">
															<label>Location</label>
															<span>'.$childdata['Resource_Location'].'</span>
														</div>
														
														<!--<div class="col-md-2 type">
															<label>Nos</label>
															<input style="text-align: right;" id="estimate-batchingunit'.$childdata['Resource_Id'].'" type="text" class="form-control" value="" />
														</div>-->
														<div class="col-md-3 type">
															<label>Fuel Type</label>
															<select class="form-control fueltype" name="mastful" data-id='.$childdata['Resource_Id'].'  type="text" id="fueltype'.$childdata['Resource_Id'].'">';

															$enggselected = '';
															$motoselected = '';
															$head1 = '';
															$head2 = '';
															
															if($childdata['Resource_group_Id'] == 102)
															{
															$enggselected='selected';
															$head1 = 'Fuel (Ltr/Hr)';
															$head2 = 'Fuel Rate';
															}elseif($childdata['Resource_group_Id'] == 154)
															{
															$motoselected='selected';
															$head1 = 'Power (KWh)';
															$head2 = 'Power Rate';
															}else{
																$enggselected='selected';
																$head1 = 'Fuel (Ltr/Hr)';
																$head2 = 'Fuel Rate';
										
															  }


												$childdatarows.='<option value="Petrol">Petrol</option>
																<option value="Diesel" '.$enggselected.'>Diesel</option>
																<option value="Power" '.$motoselected.'>Power</option>
                                        					</select>
														</div>
														<div class="col-md-3 type">
															<div class="form-group">
																<label id="mastfrate'.$childdata['Resource_Id'].'">'.$head2.'</label>
																<input style="text-align: right;" name="masteqrate" data-id="'.$childdata['Resource_Id'].'" id="mast-fuelrate'.$childdata['Resource_Id'].'" type="text" class="form-control estimate-fuelrate" value="'.$childdata['rate'].'" />
															</div>
															
														</div>
													
														<div class="col-md-3 type">
															<div class="form-group">
																<label id="mastfqt'.$childdata['Resource_Id'].'">'.$head1.'</label>
																<input style="text-align: right;" name="mastqty" data-id="'.$childdata['Resource_Id'].'" id="mast-fuelqty'.$childdata['Resource_Id'].'" type="text" class="form-control" value="'.$childdata['consumption'].'" />
															</div>
															
														</div>
													</div>
												</div>
												<div class="col-md-6" style="padding-right:25px">
													<div class="row">
														
														
														
														<div class="col-md-3 type">
															<div class="form-group">
																<label>Maint Cost/Hr</label>
																<input style="text-align: right;" name="mastrep" data-id="'.$childdata['Resource_Id'].'" id="mast-repair'.$childdata['Resource_Id'].'" data-id="'.$childdata['Resource_Id'].'" type="text" class="form-control estimate-repair" value="'.$childdata['maintenance'].'" />
															</div>
															
														</div>
														<div class="col-md-3 type">
															<div class="form-group">
																<label>Machine Rate</label>
																<input style="text-align: right;" id="mast-machrate'.$childdata['Resource_Id'].'" type="text" class="form-control" value="'.$childdata['machinecost'].'" />
															</div>
															
														</div>

														<div class="col-md-3 type">
															<div class="form-group">
																<label>No of Hrs</label>
																<input id="estimate-resourcetime'.$childdata['Resource_Id'].'" data-id="'.$childdata['Resource_Id'].'" type="text" class="form-control estimate-resourcetime" value="" />
																<span style="font-size:11px; color: red;font-weight: normal;"  class="error" style="display: none;"></span>
															</div>
														</div>
														
														<!-- <div class="col-md-2 type">
															<div class="form-group">
																<label>Amount</label>
																<input style="text-align: right;" id="estimate-acamnt'.$childdata['Resource_Id'].'" type="text" class="form-control"  readonly/>
															</div>
															
														</div> -->
														<div class="col-md-3 type">
															<div class="form-group">
																<label>&nbsp;</label>
																<button value="'.$childdata['Resource_Id'].'" data-id="'.$restypeid.'" class="btn btn-primary add-alloc-item master-addresource" ><span class="icon-add"></span>Add</button>
															</div>
														</div>
													</div>
												</div>
												</div>
											</div>';

						}elseif($restypeid==24){

							$childdatarows.='<div class="row">
												<div class="col-md-12 type">
													<div class="col-md-6">
														<span class="c-name">'.$vendorname.'</span>
													</div>
													<div class="col-md-6 machine-radio">
														<label class="radio-inline">
													      <input type="radio" id="no_of_hrs" data-type="'.$restypeid.'" name="mlequ-select" value="mlno_of_hrs" data-resid="'.$childdata['Resource_Id'].'">Operation Cost
													    </label>
													    <label class="radio-inline">
													      <input type="radio" id="lease_period" data-type="'.$restypeid.'" name="mlequ-select" data-resid="'.$childdata['Resource_Id'].'" value="mlease_period">Lease Period
													    </label>
													</div>
												</div>
												<div class="mleasedrow'.$childdata['Resource_Id'].'">
													<div class="col-md-6">
														<div class="row">
															<div class="col-md-3 type">
																<label>Location</label>
																<span>'.$childdata['Resource_Location'].'</span>
															</div>
															<div class="col-md-3 type">
																<label>Unit</label>
																<span>No of Hours</span>
																
															</div>
															<!--<div class="col-md-2 type">
																<label>Nos</label>
																<input style="text-align: right;" id="estimate-batchingunit'.$childdata['Resource_Id'].'" type="text" class="form-control" value="" />
															</div>-->
															<div class="col-md-3 type">
																<label>Fuel Type</label>
																<select class="form-control fueltype" name="mastlfue" data-id="'.$childdata['Resource_Id'].'" type="text" id="fueltype'.$childdata['Resource_Id'].'">';


																$enggselected = '';
																$motoselected = '';
																$head1 = '';
																$head2 = '';
																
																if($childdata['Resource_group_Id'] == 102)
																{
																$enggselected='selected';
																$head1 = 'Fuel (Ltr/Hr)';
																$head2 = 'Fuel Rate';
																}elseif($childdata['Resource_group_Id'] == 154)
																{
																$motoselected='selected';
																$head1 = 'Power (KWh)';
																$head2 = 'Power Rate';
																}else{
																	$enggselected='selected';
																	$head1 = 'Fuel (Ltr/Hr)';
																	$head2 = 'Fuel Rate';
											
																}


													$childdatarows.='<option value="Petrol">Petrol</option>
																	<option value="Diesel" '.$enggselected.'>Diesel</option>
																	<option value="Power" '.$motoselected.'>Power</option>
	                                        						
	                                        					</select>
															</div>
															<div class="col-md-3 type">
																<div class="form-group">
																	<label id="mastlfrate'.$childdata['Resource_Id'].'">'.$head2.'</label>
																	<input style="text-align: right;" id="estimate-fuelrate'.$childdata['Resource_Id'].'" type="text" class="form-control estimate-fuelrate" value="'.$childdata['rate'].'" />
																</div>
																
															</div>
														</div>
													</div>
													<div class="col-md-6" style="padding-right:25px">
														<div class="row">
															
															<div class="col-md-4 type">
																<div class="form-group">
																	<label id="mastlfqt'.$childdata['Resource_Id'].'">'.$head1.'</label>
																	<input style="text-align: right;" id="estimate-fuelqty'.$childdata['Resource_Id'].'" type="text" class="form-control" value="'.$childdata['consumption'].'" />
																</div>
																
															</div>
															
															<!--<div class="col-md-3 type">
																<div class="form-group">
																	<label>R&M</label>
																	<input style="text-align: right;" id="estimate-repair'.$childdata['Resource_Id'].'" data-id="'.$childdata['Resource_Id'].'" type="text" class="form-control estimate-repair" value="" />
																</div>
																
															</div>-->

															<div class="col-md-4 type">
																<div class="form-group">
																	<label>No of Hrs</label>
																	<input id="estimate-resourcetime'.$childdata['Resource_Id'].'" data-id="'.$childdata['Resource_Id'].'" type="text" class="form-control estimate-resourcetime" value="" />
																	<span style="font-size:11px; color: red;font-weight: normal;"  class="error" style="display: none;"></span>
																</div>
															</div>
															
															<!-- <div class="col-md-3 type">
																<div class="form-group">
																	<label>Amount</label>
																	<input style="text-align: right;" id="estimate-acamnt'.$childdata['Resource_Id'].'" type="text" class="form-control"  readonly/>
																</div>
																
															</div> -->
															<div class="col-md-3 type">
																<div class="form-group">
																	<label>&nbsp;</label>
																	<button value="'.$childdata['Resource_Id'].'" data-id="'.$restypeid.'" class="btn btn-primary add-alloc-item master-addresource" ><span class="icon-add"></span>Add</button>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>';


						}elseif($restypeid==33){

							$childdatarows.='<div class="row">
												<div class="col-md-12 type">
													<span class="c-name">'.$vendorname.'</span>
												</div>
												<div class="col-md-5">
													<div class="row">
														<div class="col-md-4 type">
															<label>Location</label>
															<span>'.$childdata['Resource_Location'].'</span>
														</div>
														<div class="col-md-4 type">
															<label>Unit</label>
															<span>'.$childdata['Unit'].'</span>
														</div>
														<div class="col-md-4 type">
															<div class="form-group">
																<label>No of men</label>
																<input style="text-align: right;" id="estimate-mannumbr'.$childdata['Resource_Id'].'" type="text" class="form-control" value="" />
															</div>
														</div>
													</div>
												</div>
												<div class="col-md-7">
													<div class="row">
														<div class="col-md-4 type">
															<div class="form-group">
																<label>No of days</label>
																<input style="text-align: right;" id="estimate-no-ofdays'.$childdata['Resource_Id'].'" type="text" class="form-control" value="" />
															</div>
															
														</div>
														<div class="col-md-5 type">
															<div class="form-group">
																<label>Rate</label>
																<input style="text-align: right;" id="estimate-specificrate'.$childdata['Resource_Id'].'" type="text" class="form-control" value="'.number_format($childdata['Price'],2).'" />
															</div>
															
														</div>
														<div class="col-md-3 type">
															<div class="form-group">
																<label>&nbsp;</label>
																<button value="'.$childdata['Resource_Id'].'" data-id="'.$restypeid.'" class="btn btn-primary add-alloc-item master-addresource" ><span class="icon-add"></span>Add</button>
															</div>
														</div>
													</div>
												</div>
											</div>';

						}
						else{

							$childdatarows.='<div class="row">
												<div class="col-md-12 type">
													<span class="c-name">'.$vendorname.'</span>
												</div>
												<div class="col-md-5">
													<div class="row">
														<div class="col-md-4 type">
															<label>Location</label>
															<span>'.$childdata['Resource_Location'].'</span>
														</div>
														<div class="col-md-4 type">
															<label>Unit</label>
															<span>'.$childdata['Unit'].'</span>
														</div>
														<div class="col-md-4 type">
															<label>Rate</label>
															<span>'.number_format($childdata['Price'],2).'</span>
															<input type="hidden" id="master-resourceprice'.$childdata['Resource_Id'].'" value="'.$childdata['Price'].'">
														</div>
													</div>
												</div>
												<div class="col-md-7">
													<div class="row">
														<div class="col-md-5 type">
															<div class="form-group">
																<label>Specific Rate</label>
																<input style="text-align: right;" id="estimate-specificrate'.$childdata['Resource_Id'].'" type="text" class="form-control" value="'.number_format($childdata['Price'],2).'" />
															</div>
															
														</div>
														<div class="col-md-4 type">
															<div class="form-group">
																<label>Quantity</label>
																<input id="estimate-resourcequantity'.$childdata['Resource_Id'].'" type="text" class="form-control" value="" placeholder="Quantity" />
																<span style="font-size:11px; color: red;font-weight: normal;"  class="error" style="display: none;"></span>
															</div>
														</div>
														<div class="col-md-3 type">
															<div class="form-group">
																<label>&nbsp;</label>
																<button value="'.$childdata['Resource_Id'].'" data-id="'.$restypeid.'" class="btn btn-primary add-alloc-item master-addresource" ><span class="icon-add"></span>Add</button>
															</div>
														</div>
													</div>
												</div>
											</div>';

						}

					}
					
					
				endforeach;
				//$groupname=ResourceGroup::find($data['Resource_group_Id'])->one()->Resource_group_Name; 
				if($key==0)
				{
					$datarows.='<div class="panel panel-default active">';
				}
				else
				{
					$datarows.='<div class="panel panel-default">';
				}
				$datarows.='<div class="panel-heading">
										  <h4 class="panel-title">
											<a data-toggle="collapse" data-parent="#accordionmast" href="#collapseact'.$sno.'">'.($key+1).'.&nbsp;&nbsp;'.$data['Name'].'</a>
										  </h4>
										</div>';
				if($key==0)
				{
					//$datarows.='<div id="collapseact'.$sno.'" class="panel-collapse collapse in">';
					$datarows.='<div id="collapseact'.$sno.'" class="panel-collapse collapse">';
				}
				else
				{
					$datarows.='<div id="collapseact'.$sno.'" class="panel-collapse collapse">';
				}
										

					$datarows.='<div class="panel-body">
										  '.$childdatarows.'
										  </div>
										 </div>
										 </div>';
                        //$datarows.=$childdatarows;
			endforeach;
		else:
			$datarows= '<tr id="nodata"><td colspan="10" style="text-align: center;">No Resources Found</td></tr>';
		endif;
		$arr = array('result' => $datarows,'activityname'=>$activitynameval, 'error'=>'No');
		return json_encode($arr);


	}
	public function actionAddresourcetemp()
	{
		if(isset($_POST['quantity'])):


			$connection = CActiveRecord::getDbConnection();
			$rate=$_POST['quantity']*$_POST['srate'];

			$sql="INSERT INTO project_setup_resources (PSR_PS_Id,PSR_Resource_Id,PSR_Quantity,PSR_Price,PSR_Actual_Price) VALUES('".$_POST['Consumables_Id']."','".$_POST['resid']."','".$_POST['quantity']."','".($rate)."','".$_POST['srate']."')";
			$command=$connection->createCommand($sql);
			$dataReader=$command->query();


			$sql1="UPDATE project_setup SET PS_Price=PS_Price+".$rate."  WHERE PS_Id='".$_POST['Consumables_Id']."'";
			$command=$connection->createCommand($sql1);
			$dataReader=$command->query();

			$sqltemvals="SELECT a.*,b.* FROM resources AS a
                    INNER JOIN project_setup_resources AS b ON  a.Resource_Id=b.PSR_Resource_Id
                    WHERE b.PSR_PS_Id='".$_POST['Consumables_Id']."' ORDER BY a.Resource_Id";
			$command=$connection->createCommand($sqltemvals);
			$dataReader=$command->query();
			$dataProvider=$dataReader->readAll();

			$datarows='';
			$price=0;
			foreach($dataProvider AS $key=>$data):
				$price=$price+$data['PSR_Price'];
				$datarows.="<tr id='tempresrow".$data['PSR_Id']."'>
				<td>".Resourcetype::model()->findByPk( $data['ResourceType_Id'])->Name."</td>
                            <td>".$data['Name']."</td>
                            <td style='width: 20%'>".$data['Unit']."</td>
                            <td style='width: 20%'><input type='hidden' name='resourceid[]' value='".$data['PSR_Id']."'>
                            <input type='text' data-id='".$data['PSR_Id']."' class='form-control resourceqty' name='resourceqty[]' id='quantity".$data['PSR_Id']."' value='".$data['PSR_Quantity']."' >
                            <span class='error'></span></td>
                            <td style='width: 20%'><input type='text' data-id='".$data['PSR_Id']."' class='form-control resourcerate' name='resourcerate[]' id='rate".$data['PSR_Id']."' value='".$data['PSR_Actual_Price']."' ></td>
                            <td class='resource-amount' id='amount".$data['PSR_Id']."'>".$data['PSR_Price']."</td>
                            <td><button type='button' class='btn btn-primary removeresourceitem' id='removeresourceitem".$data['PSR_Id']."' value='".$data['PSR_Id']."'>Remove</button></td>
                            </tr>";
			endforeach;
			$Investments=ProjectSetup::model()->findByPk($_POST['Consumables_Id']);
			if($Investments->save(false)):
				$arr = array('result' => $datarows,'price'=>$price,'error'=>'No');
				echo json_encode($arr);
			endif;
		endif;
	}
	public function actionDeleteresourcetemp()
	{
		if(isset($_POST['resid'])):
			$connection = CActiveRecord::getDbConnection();
			$sql="SELECT PSR_PS_Id,PSR_Price AS Price FROM project_setup_resources WHERE PSR_Id='".$_POST['resid']."'";
			$command=$connection->createCommand($sql);
			$dataReader=$command->query();
			$overheads=$dataReader->read();
			$sql="DELETE FROM project_setup_resources WHERE PSR_Id='".$_POST['resid']."'";
			$command=$connection->createCommand($sql);
			$dataReader=$command->query();
			$sql1="UPDATE project_setup SET PS_Price=PS_Price-".$overheads['Price']." WHERE PS_Id='".$overheads['PSR_PS_Id']."'";
			$command=$connection->createCommand($sql1);
			$dataReader=$command->query();

			$sqltemvals="SELECT a.*,b.* FROM resources AS a
                    INNER JOIN project_setup_resources AS b ON  a.Resource_Id=b.PSR_Resource_Id
                    WHERE b.PSR_PS_Id='".$overheads['PSR_PS_Id']."' ORDER BY a.Resource_Id";
			$command=$connection->createCommand($sqltemvals);
			$dataReader=$command->query();
			$dataProvider=$dataReader->readAll();
			//print_r($dataProvider);

			$datarows='';
			$price=0;
			foreach($dataProvider AS $key=>$data):
				$price=$price+$data['PSR_Price'];
				$datarows.="<tr id='tempresrow".$data['PSR_Id']."'>
				<td>".Resourcetype::model()->findByPk( $data['ResourceType_Id'])->Name."</td>
                            <td>".$data['Name']."</td>
                            <td style='width: 20%'>".$data['Unit']."</td>
                            <td style='width: 20%'><input type='hidden' name='resourceid[]' value='".$data['PSR_Id']."'>
                            <input type='text' data-id='".$data['PSR_Id']."' class='form-control resourceqty' name='resourceqty[]' id='quantity".$data['PSR_Id']."' value='".$data['PSR_Quantity']."' >
                            <span class='error'></span></td>
                            <td style='width: 20%'><input type='text' data-id='".$data['PSR_Id']."' class='form-control resourcerate' name='resourcerate[]' id='rate".$data['PSR_Id']."' value='".$data['PSR_Actual_Price']."' ></td>
                            <td class='resource-amount' id='amount".$data['PSR_Id']."'>".$data['PSR_Price']."</td>
                            <td><button type='button' class='btn btn-primary removeresourceitem' id='removeresourceitem".$data['PSR_Id']."' value='".$data['PSR_Id']."'>Remove</button></td>
                            </tr>";
			endforeach;
			$Overhead=ProjectSetup::model()->findByPk($overheads['PSR_PS_Id']);
			/*echo $datarows;
			exit;*/

			//$amount=$price/($product->quantity);
			//$product->amount=$amount;
			if($Overhead->save(false)):
				$arr = array('result' => $datarows,'price'=>$price,'error'=>'No'); 
				echo json_encode($arr);
			endif;
		endif;
	}
	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();
		$connection = CActiveRecord::getDbConnection();
		/*$sql="DELETE FROM project_setup_resources WHERE PS_Id='".$id."'";
		$command=$connection->createCommand($sql);
		$dataReader=$command->query();*/
		$arr = array('error'=>'No');
		echo json_encode($arr);
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('MajorConsumables');
		$this->render('index',array(
				'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new MajorConsumables('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['MajorConsumables']))
			$model->attributes=$_GET['MajorConsumables'];

		$this->render('admin',array(
				'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Overheads the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=ProjectSetup::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	  public function actionUpdatesort()
    {
		// echo "test";exit;
        if(isset($_POST['datavalue']))
        {
            foreach($_POST['datavalue'] AS $data)
            {
                $work=ProjectSetup::model()->findByPk($data['rowid']);
                $work->sortorder=$data['rowindex'] + 1;
                $work->save(false);
            }
        }
        $arr = array('error'=>'No');
        echo json_encode($arr);
    }

	 public function actionActUpdate()
    {
        $connection = CActiveRecord::getDbConnection();
        $sql="UPDATE project_setup SET PS_Name='".$_POST['name']."',PS_Unit='".$_POST['unit']."' WHERE PS_Id='".$_POST['id']."'";
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $arr = array('Id' => $_POST['id'], 'Name' => $_POST['name'],'Unit' => $_POST['unit'],'error'=>'No');
        echo json_encode($arr);
    }

	public function actionCopyresources()
	{
		$connection = CActiveRecord::getDbConnection();
		$sqltemvals="SELECT a.Name AS resource,a.Unit,a.ResourceType_Id,a.Resource_Location,b.* FROM resources AS a
                INNER JOIN project_setup_resources AS b ON  a.Resource_Id=b.PSR_Resource_Id
                WHERE b.PSR_PS_Id='528' ORDER BY a.Resource_Id";
		// echo $sqltemvals;exit;
		$command=$connection->createCommand($sqltemvals);
		$dataReader=$command->query();
		$resourcesadded=$dataReader->readAll();
		//echo count($resourcesadded);exit;
		foreach($resourcesadded AS $item):
			$rate=$item['PSR_Quantity']*$item['PSR_Actual_Price'];

			$sql="INSERT INTO project_setup_resources (PSR_PS_Id,PSR_Resource_Id,PSR_Quantity,PSR_Price,PSR_Actual_Price) VALUES('526','".$item['PSR_Resource_Id']."','".$item['PSR_Quantity']."','".($rate)."','".$item['PSR_Actual_Price']."')";
			//$command=$connection->createCommand($sql);
			//$dataReader=$command->query();


			$sql1="UPDATE project_setup SET PS_Price=PS_Price+".$rate."  WHERE PS_Id='526'";
			//$command=$connection->createCommand($sql1);
			//$dataReader=$command->query();
		endforeach;
		echo "sucess";

	}
	/**
	 * Performs the AJAX validation.
	 * @param Overheads $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='overheads-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}


}