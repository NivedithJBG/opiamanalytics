<?php

namespace app\controllers;  

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\Projects;
use app\models\UserTabs;
use app\models\WorkgroupsNew;
use app\models\Boq;
use app\models\WorkgroupActivitiesNew;
use app\models\ClientBill;

class ProjectpricingController extends Controller
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

	public function actionGetname()
    {
        if (isset($_POST['id'])):
            $connection = Yii::$app->db;
            $sql = "SELECT Name FROM projects WHERE Project_Id='" . $_POST['id'] . "'";
            $command = $connection->createCommand($sql);
            $dataReader = $command->query();
            $data = $dataReader->read();
            return $data['Name'];
        endif;
    }

	public function actionBoqsearch()
	{
		$connection = Yii::$app->db;
		echo 'inside controller';
		//$sql = "SELECT a.wbs,b.sortorder FROM boq AS a INNER JOIN workgroups_new AS b ON a.wbs=b.Workgroup_Id WHERE a.projectid='".$_POST['projectid']."' AND b.Status=0 GROUP BY a.wbs ORDER BY b.sortorder ASC,b.Workgroup_Id ASC ";
		//$sql = "SELECT a.wbs,b.sortorder FROM boq AS a INNER JOIN workgroups_new AS b ON a.wbs=b.Workgroup_Id WHERE a.projectid='".$_POST['projectid']."' AND b.Status=0 GROUP BY a.wbs ORDER BY a.slno ASC";
		$sql = "SELECT a.wbs,b.sortorder FROM boq AS a INNER JOIN workgroups_new AS b ON a.wbs=b.Workgroup_Id WHERE a.projectid='".$_POST['projectid']."' GROUP BY a.wbs ORDER BY a.slno ASC";
		//echo $sql;exit;
		$command = $connection->createCommand($sql);
		$dataReader = $command->query();
		$dataProvider = $dataReader->readAll();
		$datarows = '';
		if (count($dataProvider) > 0):
			$projecttot=0;
			foreach ($dataProvider AS $key => $data):
				$wbs=WorkgroupsNew::findOne($data['wbs']);
				$boqitems=Boq::find()->where(['wbs' => $data['wbs']])->orderBy(['slno' => SORT_ASC])->all();
		        if($boqitems[0]['slno']!=0){
				 $array = $boqitems[0]['slno'];
				 $myArray = explode('.', $array);
				 $slno1 = $myArray[0];
				}
		        else{
				 $slno1 = '';
				}
				if($boqitems[0]['iowname']!='' && $boqitems[0]['iowname']!=$wbs['Name']){
					$iowname = $boqitems[0]['iowname'];
				}
				else{
					$iowname = $wbs['Name'];
				}
		        $datarows.='<div class="boq-list-cntnr row">
								<div class="col-md-12 type bill-number">
									<span>'.$iowname.'</span>
								</div>';
				$itemstot=0;
				$countboq = count($boqitems);
				$countNow = 0;
				foreach($boqitems AS $boqitem):
					$amount=$boqitem['quantity'] * $boqitem['rate'];
					//$total=$total + $amount;
		            if($boqitem['slno']!=0){
					 $slno = $boqitem['slno'];
					}
		            else{
					 $slno = '';
					}
		            $activity = $boqitem['activity'];
		            $iowid=$boqitem['wbs'];
					$wbsactivities=WorkgroupActivitiesNew::find()->where(['wbs_id' => $iowid])->andWhere(['pricing_status' =>0])->orderBy(['wbs_id'=> SORT_ASC])->all();
					if(count($wbsactivities)>0):
						$datarows1="<option value='0'>Select Activity</option>";
						foreach($wbsactivities AS $key=>$wbsactivity):
		                 if($wbsactivity->id==$boqitem['activity']){
						 $selected = 'selected';
						 }
		                 else{
						 $selected = '';
						 }
						 $datarows1.="<option value='".$wbsactivity->id."' $selected>$wbsactivity->activity_Name</option>";
						endforeach;
					endif;
					$workgroupact=WorkgroupActivitiesNew::find()->where(['id' => $boqitem['activity']])->andWhere(['pricing_status' =>0])->one();
					$datarows.='<div class="col-md-3 type wbs-unit-qty-row">
									<label>WBS</label>
									<span id="slnotext'.$boqitem['boq_id'].'">'.$slno.'</span>
								</div>
								<div class="col-md-3 type wbs-unit-qty-row">
									<label>Unit</label>
									<span id="unittext'.$boqitem['boq_id'].'">'.$boqitem['unit'].'</span>
								</div>
								<div class="col-md-3 type wbs-unit-qty-row">
									<label>Quantity</label>
									<span id="quantitytext'.$boqitem['boq_id'].'">'.$boqitem['quantity'].'</span>
								</div>
								<div class="col-md-3 type wbs-unit-qty-row">
									<label>Rate</label>
									<span id="ratetext'.$boqitem['boq_id'].'">'.number_format($boqitem['rate'], 2).'</span>
								</div>
								<div class="col-md-6 type">
									<label>Item</label>
									<span id="activity'.$boqitem['boq_id'].'">'.$boqitem['item'].'</span>
								</div>
								<div class="col-md-3 type">
									<label>Amount</label>
									<span>'.number_format($amount, 2).'</span>
								</div>';
					$itemstot=$itemstot + $amount;
					if($countNow != $countboq && $countboq != 1){
						$datarows.='<div class="col-md-3 total-amount type">
						<label>Total</label>
						<span>'.number_format($itemstot, 2).'</span>
					</div></div><div class="boq-list-cntnr row">';
					}
					
					$countNow = $countNow + 1;
				endforeach;
				if($countboq == 1){
					$datarows.='<div class="col-md-3 total-amount type">
					<label>Total</label>
					<span>'.number_format($itemstot, 2).'</span>
				</div></div>';

				}
				
				$projecttot=$projecttot + $itemstot;
			endforeach;
			$datarows.='<div class="col-md-12 type project-boq">
							<label style="text-align: right !important;">&nbsp<em>Project Total - '.number_format($projecttot, 2).'</em></label>
						</div>';
		else:
			$datarows = '<div class="row"><div class="col-md-12"><div class="text-center">No BOQ Found</div></div></div>';
		endif;
		$print="<a href='".Yii::$app->request->baseUrl."/ProjectPricing/Export/".$_POST['projectid']."' target='_blank'><button type='button' class='btn btn-primary small75 pull-right' title='Export'>Export</button></a>";
		$import ='<a href="#" class="btn btn-primary addForm" id="boqimport" data-id="'.$_POST['projectid'].'"><span class="icon-download7"></span> Import</a>';

		$client_bills = ClientBill::find()->where(['projectid' => $_POST['projectid']])->andWhere(['status' =>0])->groupBy(['bill_no'])->orderBy(['raise_date'=> SORT_DESC])->all();
		if(count($client_bills)>0){
			$boqclear='<a href="#" class="btn btn-primary clearboq-btn" id="clearboq" data-id="'.$_POST['projectid'].'"><span class="icon-trashcan" disabled></span> Clear BOQ DD</a>';
		}
		else{
			$boqclear='<a href="#" class="btn btn-primary clearboq-btn" id="clearboq" data-id="'.$_POST['projectid'].'" ><span class="icon-trashcan"></span> Clear BOQ</a>';
		}

		$arr = array('result' => $datarows,'print'=>$print,'import'=>$import,'boqclear'=>$boqclear, 'error' => 'No');
		return json_encode($arr);
	}

	public function actionImport()
	{
		$connection = Yii::$app->db;
		if($_FILES["import_boq_file"]["name"] != '')
		{
			$allowed_extension = array('csv');
 			$file_array = explode(".", $_FILES["import_boq_file"]["name"]);
 			$file_extension = end($file_array);
 			if(in_array($file_extension, $allowed_extension))
 			{
 				
				if (($handle = fopen($_FILES['import_boq_file']['tmp_name'], "r")) !== FALSE) {
					$count=0;
					$values=fgetcsv($handle);
					while (($values = fgetcsv($handle, 1000, ",")) !== FALSE) {
	            		if(fmod($values[0],1) == 0.00 && is_numeric($values[0])){
	            			//echo 'iow'; exit;
	            			//$wrkgrpname = explode("  ", $values[1]);
	            			$var = $values[0];
	            			$patt = '/^\d+\.{1}\d+$/';
							$resp = preg_match($patt,$var);
	            			if($resp){
		            			$wrkgrpname = $values[1];
								//$workgroup=WorkgroupsNew::model()->find(array('condition'=>'Name LIKE "'.$wrkgrpname.'" AND Project_Id='.$_POST['projectid'].''));
								$workgroup=WorkgroupsNew::find()->where(['Project_Id' => $_POST['projectid']])->andFilterWhere(['like','Name', $wrkgrpname])->one();
		            			if(!$workgroup && $wrkgrpname!=''){
		            				$model=new WorkgroupsNew;
						            $model->Project_Id=$_POST['projectid'];
						            $model->Name=$wrkgrpname;
						            //$model->unit=$_POST['unit'];
						            //$model->quantity=$_POST['quantity'];
						            $model->parent=$_POST['projectid'];
						            $model->itemtype= 'workgroup';
									$model->wbs_estimate_id=0;
						            $model->save(false);
		            			}
		            		}
	            		}

	            		//print_r($values); exit;

	            		if(fmod((float)$values[0], 1) !== 0.00){

							//if($count!='0' & $wrkgrpname[1]!=''):
							$wrkgrpname = $values[1];//var_dump($wrkgrpname);exit;
            				if($count!='0' && $wrkgrpname!=''):
		            			//$workgroupname=WorkgroupsNew::model()->find(array('condition'=>'Name LIKE "'.$wrkgrpname[1].'" AND Project_Id='.$_POST['projectid'].''));
								//$workgroupname=WorkgroupsNew::model()->find(array('condition'=>'Name LIKE "'.$wrkgrpname.'" AND Project_Id='.$_POST['projectid'].''));
								$workgroupname=WorkgroupsNew::find()->where(['Project_Id' => $_POST['projectid']])->where(['LIKE','Name','piling'])->one();
		            			$insert_data = array(
							    ':wbs'  => $workgroupname->Workgroup_Id,
							    ':slno'  => $values[0],
							    ':item'  => $values[1],
							    ':unit'  => $values[3],
							    ':quantity'  => $values[2],
							    ':rate'  => $values[4],
							    ':amount'  => $values[5],
							    ':project'=>$_POST['projectid']
							   );
		            			//print_r($insert_data);exit;
		            			/*$workgroupact = WorkgroupActivitiesNew::model()->find(array('condition'=>'activity_Name LIKE "'.$values[1].'" AND wbs_id='.$workgroupname->Workgroup_Id.''));*/
								$workgroupact = WorkgroupActivitiesNew::find()->where(['LIKE','activity_Name',$values[1]])->andWhere(['wbs_id' => $workgroupname->Workgroup_Id])->one();
								if(!empty($workgroupact)){
									$wg = $workgroupact->id;
								}else{
									$wg = 0;
								}
								if($workgroupname){

									//$boq = Boq::model()->find(array('condition'=>'wbs = '.$workgroupname->Workgroup_Id.' AND item LIKE "'.$values[1].'"'));
									$boq = Boq::find()->where(['wbs' => $workgroupname->Workgroup_Id])->andFilterWhere(['like','item',$values[1]])->one();

			            			if($boq){
			            				$boq->slno=$values[0];
										$boq->item=$values[1];
										$boq->iowname=$workgroupname->Name;
										$boq->unit=$values[3];
										$boq->quantity=$values[2];
										$boq->rate=$values[4];
										$boq->amount=$values[5];
										$boq->projectid=$_POST['projectid'];
										$boq->wbs=$workgroupname->Workgroup_Id;
										$boq->activity=$wg;
										$boq->save(false);
			            			}
			            			else{
			            				$model=New Boq();
										$model->slno=$values[0];
										$model->item=$values[1];
										$model->iowname=$workgroupname->Name;
										$model->unit=$values[3];
										$model->quantity=$values[2];
										$model->rate=$values[4];
										$model->amount=$values[5];
										$model->projectid=$_POST['projectid'];
										$model->wbs=$workgroupname->Workgroup_Id;
										$model->save(false);
									}

								}
		            		endif;

	            		}
	            		$count++; 

	            		/*if($count!='0' & $values[0]!=''):
	            			$workgroupname=WorkgroupsNew::model()->find(array('condition'=>'Name LIKE "'.$values[0].'" AND Project_Id='.$_POST['projectid'].''));
	            			$insert_data = array(
						    ':wbs'  => $workgroupname->Workgroup_Id,
						    ':slno'  => $values[1],
						    ':item'  => $values[2],
						    ':unit'  => $values[3],
						    ':quantity'  => $values[4],
						    ':rate'  => $values[5],
						    ':project'=>$_POST['projectid']
						   );
	            			//print_r($insert_data);exit;
	            			$workgroupact = WorkgroupActivitiesNew::model()->find(array('condition'=>'activity_Name LIKE "'.$values[2].'" AND wbs_id='.$workgroupname->Workgroup_Id.''));

	            			$boq = Boq::model()->find(array('condition'=>'wbs = '.$workgroupname->Workgroup_Id.' AND activity='.$workgroupact->id.''));

	            			if($boq){
	            				$boq->slno=$values[1];
								$boq->item=$values[2];
								$boq->unit=$values[3];
								$boq->quantity=$values[4];
								$boq->rate=$values[5];
								$boq->projectid=$_POST['projectid'];
								$boq->wbs=$workgroupname->Workgroup_Id;
								$boq->activity=$workgroupact->id;
								$boq->save(false);
	            			}
	            			else{
	            				$model=New Boq();
								$model->slno=$values[1];
								$model->item=$values[2];
								$model->unit=$values[3];
								$model->quantity=$values[4];
								$model->rate=$values[5];
								$model->projectid=$_POST['projectid'];
								$model->wbs=$workgroupname->Workgroup_Id;
								$model->activity=$workgroupact->id;
								$model->save(false);
							}
	            		endif;
	            		$count++; */
	            	}
	            	fclose($handle);
	        	}
		        
 				$message = '<span class="alert alert-success">Data Imported Successfully</span>';
 			}
 			else
 			{
  				$message = '<span class="alert alert-danger">Only .csv file allowed</span>';
 			}
		}	
		else {
			$message = '<span class="alert alert-danger">Please Select File</span>';
		}
		return $message;
	}

	public function actionClearboq()
    {
        $projectid = $_POST['projectid'];

        $connection = Yii::$app->db;

        $sql = "SELECT a.wbs,b.sortorder FROM boq AS a INNER JOIN workgroups_new AS b ON a.wbs=b.Workgroup_Id WHERE a.projectid='".$_POST['projectid']."' GROUP BY a.wbs ORDER BY a.slno ASC";
		//echo $sql;exit;
		$command = $connection->createCommand($sql);
		$dataReader = $command->query();
		$dataProvider = $dataReader->readAll();
		if (count($dataProvider) > 0):
			foreach ($dataProvider AS $key => $data):
				$wbs=WorkgroupsNew::findOne($data['wbs']);
				$boqitems=Boq::find()->where(['wbs' => $data['wbs']])->orderBy(['slno' => SORT_ASC])->all();
				foreach($boqitems AS $boqitem):
					$boqitem->delete(); 
				endforeach;

			endforeach;
		endif;

		$arr = array('error'=>'No');
        return json_encode($arr);
    }
	
}