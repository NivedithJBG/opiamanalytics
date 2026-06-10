<?php

namespace app\controllers;  
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\Projects;
use app\models\WorkgroupActivitiesNew;
use app\models\PricingEstimateResourcesNew;
use app\models\Resourcetype;
use app\models\Resources;
use app\models\Vendors;
use app\models\PlaceorderResAudit;
use app\models\PlaceorderStatus;
use app\models\Jobcard;
use app\models\Cart;
use app\models\PlaceorderRes;
use app\models\Orders;  
use app\models\OrderedResource;
use app\models\InvoiceResources;

class EstimateprojectmainController extends Controller
{ /**
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

    public function actionUpdateestimatererate() 
    {


        $estimateres =PricingEstimateResourcesNew::findOne($_POST['PRID']);

        $neglect = 0;
        /* if($_POST['qty'] < $estimateres->actual_qty)
        { */
            $resource=Resources::findOne($estimateres->resource_Id);

            $jobcardcheck=Jobcard::find()->where(['vendor_id'=>$resource->Vendor_Id])->andWhere(['resource'=>$resource->Resource_Id])->andWhere(['delete_status'=>0])->one();

            /*if($jobcardcheck)
            {
                $orderedres = OrderedResource::findOne($jobcardcheck->order_resid);
                if($orderedres)
                {
                    $invoices = InvoiceResources::find()->where(['order_id'=>$orderedres->order_id])->all();

                    $totreceive = 0;
                    foreach($invoices as $invoice)
                    {
                        $totreceive = $totreceive + $invoice->resource_qty;

                    }

                    if($_POST['qty'] < $totreceive)
                    {
                        $neglect = 1;
                    }else{
                        $neglect = 0;
                    }

                }else{
                    $neglect = 0;
                }

                
            }*/

        //}
        
       // if( $neglect == 0){

            $estimateres->rate=$_POST['updatevalue'];


            // new line added by anil on sept 12


            $sql = "UPDATE cart SET rate=".$_POST['updatevalue']." WHERE pricing_resourceid=".$_POST['PRID'].";";
                //echo $sql; exit;
            $query = Yii::$app->db->createCommand($sql)->execute();


              // new line ended by anil



            $estimateres->quantity=$_POST['qty'];
            $uid = Yii::$app->user->Id;

            if($estimateres->save(false)):
                $amounttot = $_POST['updatevalue'] * $_POST['qty'];
    
                $resource=Resources::findOne($estimateres->resource_Id);
    
                $jobcard=Jobcard::find()->where(['vendor_id'=>$resource->Vendor_Id])->andWhere(['resource'=>$resource->Resource_Id])->andWhere(['app_activity'=>$estimateres->activity_id])->andWhere(['delete_status'=>0])->one();
                
    
                $cartid = '';
    
                if($jobcard){
                    $cart = Cart::find()->where(['Job_Id'=>$jobcard->job_id])->one();
                    if($cart){
                        $cartid = $cart->cartID;
                    }
    
                    $model = new PlaceorderResAudit();
                    $model->pricing_resourceid = $estimateres->pricing_resourceid;
                    $model->edited_by = $uid;
                    $model->edited_date = date("Y-m-d H:i:s");
                    if($model->save(false)):
    
                        $placeorderstatus = new PlaceorderStatus();
                        $placeorderstatus->cart_id = $cartid;
                        $placeorderstatus->cancelled_by = $uid;
                        $placeorderstatus->cancelled_date = date("Y-m-d H:i:s");
                        if($placeorderstatus->save(false)):
    
                            //$cart->status = 5;
                            $cart->save(false);
    
                            //$jobcard->delete_status = 1;
                            $jobcard->save(false);
    /* 
                            $placeorderes = PlaceorderRes::find()->where(['cartID' => $cart->cartID])->andWhere(['Vendor_Id' => $cart->Vendor_Id])->andwhere(['Resource_Id' =>$cart->Resource_Id])->andWhere(['Project' =>$cart->Project])->one();
                            if($placeorderes){
                                $placeorderes->delete();
                            } */
    
                            /* $ores = OrderedResource::find()->Where(['jobcard_id'=>$cart->Job_Id])->one();
    
                            if($ores):
    
                                $orderedresourceitems=OrderedResource::find()->Where(['order_id'=>$ores->order_id])->all();
    
                                if($orderedresourceitems):
    
                                    foreach($orderedresourceitems as $orderedresourceitem):
    
                                        $orderedresourceitem->delete_status = 1;
                                        $orderedresourceitem->save(false);                       
    
                                    endforeach;
    
                                endif;
    
                                $order=Orders::findOne($ores->order_id);
                                $order->delete_status = 1;
                                $order->save(false);
    
                                $placeorderstatus->order_id = $ores->order_id;
                                $placeorderstatus->save(false);
    
                            endif; */
    
                        endif;
    
                    endif;   
                
                }
    
            endif;
            
        /*}else{
            $arr = array( 'error' => 'Yes');
		    return json_encode($arr);
        
        }*/
        
        
		$arr = array('result' => $_POST['PRID'],'amounttot'=>$amounttot, 'error' => 'No');
		return json_encode($arr);

    }


    public function actionActivityresources()
    {
    	$sno=0;
        $connection = Yii::$app->db;
        $process=$_POST['Process'];
        $activityid=$_POST['activityid'];
        $itemid=$_POST['project_estimate_Id'];
        $project = Projects::findOne($_POST['Project_Id']);
        $sql="SELECT * FROM estimateactivities WHERE activity_status=0 AND activity_id=".$activityid." ";
        $command1 = $connection->createCommand($sql);
        $dataReader1 = $command1->query();
        $est_act = $dataReader1->read();
        $item = WorkgroupActivitiesNew::findOne($_POST['project_estimate_Id']);
        if($item['activity_Name']!=''){
            $activityname=$item['activity_Name'];
        }else{
            $activityname=$est_act['activity_name'];
        }
        if($item['activity_Unit']!=''){
            $activityunit=$item['activity_Unit'];
        }else{
            $activityunit=$est_act['activity_unit'];
        }

         $resourcetypes = Resourcetype::find()->where(['Status'=>0])->orderBy(['sortorder' => SORT_ASC])->all();
         $datarows='';
         foreach ($resourcetypes as $key => $resourcetype) {
         if($key==0)
         {
            $datarows.='<label>Resource Types</label>';   
         }
          $datarows.='<a  class="btn btn-primary rounded-coner-btn resourcesearch-estimate" data-id="'.$resourcetype->ResourceType_Id.'" href="javascript:void(0)">'.$resourcetype->Name.'</a>';   
         }

         if (isset($_POST['Product_Name'])) {
            $name = $_POST['Product_Name'];
            $unit = $_POST['Product_Unit'];
            // echo $name;exit;
            $estrescount=PricingEstimateResourcesNew::find()->where(['project_id'=>$_POST['Project_Id']])->andWhere(['activity_id'=>$itemid])->andWhere(['est_activity_Id'=>$_POST['activityid']])->andWhere(['process_Id'=>$_POST['Process']])->andWhere(['pricing_status'=>0])->count();

            $res_type = $_POST['resourcetypeid'];

            if($res_type==26){ 

                if($_POST['type'] == 'plant_no_of_hrs')
                {

                    $total = 0;
                    $res_Qty = $_POST['hoursno'];
                    if($_POST['repair']!=''){
                        $res_Rate = ($_POST['fuelrate'] * $_POST['fuelqty']) + $_POST['repair'];
                    }
                    else{
                        $res_Rate = ($_POST['fuelrate'] * $_POST['fuelqty']);
                    }
                    
                    $rate = ($res_Rate * $res_Qty);
                    $total = $total + $rate;
                    if($_POST['fueltype'] != 'Power')
                    {
                        $sql = "INSERT INTO pricing_estimate_resources_new (activity_id,resource_Id,resourcetype_Id,quantity,rate,actual_qty,actual_rate,actual_amount,project_id,est_activity_Id,process_Id,pricing_status,operations_status,fuel_type,fuel_rate,fuel_qty,hours_no,repair_cost) VALUES('" . $_POST['project_estimate_Id'] . "','" . $_POST['resourceid'] . "','".$_POST['resourcetypeid']."','" . $res_Qty . "','" . $res_Rate . "','" . $res_Qty . "','" . $res_Rate . "','" . $rate . "','" . $_POST['Project_Id'] . "','".$_POST['activityid']."','".$_POST['Process']."',0,0,'" . $_POST['fueltype'] . "','" . $_POST['fuelrate'] . "','" . $_POST['fuelqty'] . "','" . $_POST['hoursno'] . "','" . $_POST['repair'] . "')";
                    }else{
                        $sql = "INSERT INTO pricing_estimate_resources_new (activity_id,resource_Id,resourcetype_Id,quantity,rate,actual_qty,actual_rate,actual_amount,project_id,est_activity_Id,process_Id,pricing_status,operations_status,fuel_type,fuel_rate,fuel_qty,hours_no,repair_cost,powerstatus) VALUES('" . $_POST['project_estimate_Id'] . "','" . $_POST['resourceid'] . "','".$_POST['resourcetypeid']."','" . $res_Qty . "','" . $res_Rate . "','" . $res_Qty . "','" . $res_Rate . "','" . $rate . "','" . $_POST['Project_Id'] . "','".$_POST['activityid']."','".$_POST['Process']."',0,0,'" . $_POST['fueltype'] . "','" . $_POST['fuelrate'] . "','" . $_POST['fuelqty'] . "','" . $_POST['hoursno'] . "','" . $_POST['repair'] . "','1')";

                    }

                }else{

                    $rate = 1 * $_POST['resourcerate'];

                    $sql = "INSERT INTO pricing_estimate_resources_new (activity_id,resource_Id,resourcetype_Id,quantity,rate,actual_qty,actual_rate,actual_amount,project_id,est_activity_Id,process_Id,pricing_status,operations_status,lease_period) VALUES('" . $_POST['project_estimate_Id'] . "','" . $_POST['resourceid'] . "','".$_POST['resourcetypeid']."','1','" . $_POST['resourcerate'] . "','1','" .$_POST['resourcerate'] . "','" . $rate . "','" . $_POST['Project_Id'] . "','".$_POST['activityid']."','".$_POST['Process']."',0,0,'1')";

                    $total = $rate;

                }


                //echo $sql;exit;
                $command = $connection->createCommand($sql);
                $dataReader = $command->query();

                $sql1 = "UPDATE pricing_estimate_new SET specific_rate=" . $total . "  WHERE activity_Id='".$_POST['project_estimate_Id']."' ";
                //echo $sql1;exit;
                $command = $connection->createCommand($sql1);
                $dataReader = $command->query();

            }elseif($res_type==24){

                

                if($_POST['type'] == 'no_of_hrs')
                {

                    $total = 0;
                    $res_Qty = $_POST['hoursno'];
                    /*if($_POST['repair']!=''){
                        $res_Rate = ($_POST['fuelrate'] * $_POST['fuelqty']) + $_POST['repair'];
                    }
                    else{*/
                        $res_Rate = ($_POST['fuelrate'] * $_POST['fuelqty']);
                    //}
                    
                    $rate = ($res_Rate * $res_Qty);
                    $total = $total + $rate;

                    if($_POST['fueltype'] != 'Power')
                    {

                        $sql = "INSERT INTO pricing_estimate_resources_new (activity_id,resource_Id,resourcetype_Id,quantity,rate,actual_qty,actual_rate,actual_amount,project_id,est_activity_Id,process_Id,pricing_status,operations_status,fuel_type,fuel_rate,fuel_qty,hours_no) VALUES('" . $_POST['project_estimate_Id'] . "','" . $_POST['resourceid'] . "','".$_POST['resourcetypeid']."','" . $res_Qty . "','" . $res_Rate . "','" . $res_Qty . "','" . $res_Rate . "','" . $rate . "','" . $_POST['Project_Id'] . "','".$_POST['activityid']."','".$_POST['Process']."',0,0,'" . $_POST['fueltype'] . "','" . $_POST['fuelrate'] . "','" . $_POST['fuelqty'] . "','" . $_POST['hoursno'] . "')";
                    }else{
                        $sql = "INSERT INTO pricing_estimate_resources_new (activity_id,resource_Id,resourcetype_Id,quantity,rate,actual_qty,actual_rate,actual_amount,project_id,est_activity_Id,process_Id,pricing_status,operations_status,fuel_type,fuel_rate,fuel_qty,hours_no,powerstatus) VALUES('" . $_POST['project_estimate_Id'] . "','" . $_POST['resourceid'] . "','".$_POST['resourcetypeid']."','" . $res_Qty . "','" . $res_Rate . "','" . $res_Qty . "','" . $res_Rate . "','" . $rate . "','" . $_POST['Project_Id'] . "','".$_POST['activityid']."','".$_POST['Process']."',0,0,'" . $_POST['fueltype'] . "','" . $_POST['fuelrate'] . "','" . $_POST['fuelqty'] . "','" . $_POST['hoursno'] . "','1')";
                    }

                }else{

                    $rate = $_POST['leaseperiod'] * $_POST['resourcerate'];

                    $sql = "INSERT INTO pricing_estimate_resources_new (activity_id,resource_Id,resourcetype_Id,quantity,rate,actual_qty,actual_rate,actual_amount,project_id,est_activity_Id,process_Id,pricing_status,operations_status,lease_period) VALUES('" . $_POST['project_estimate_Id'] . "','" . $_POST['resourceid'] . "','".$_POST['resourcetypeid']."','" . $_POST['leaseperiod'] . "','" . $_POST['resourcerate'] . "','" . $_POST['leaseperiod'] . "','" .$_POST['resourcerate'] . "','" . $rate . "','" . $_POST['Project_Id'] . "','".$_POST['activityid']."','".$_POST['Process']."',0,0,'" . $_POST['leaseperiod'] . "')";

                    $total = $rate;

                }
                

                //echo $sql;exit;
                $command = $connection->createCommand($sql);
                $dataReader = $command->query();

                $sql1 = "UPDATE pricing_estimate_new SET specific_rate=" . $total . "  WHERE activity_Id='".$_POST['project_estimate_Id']."' ";
                //echo $sql1;exit;
                $command = $connection->createCommand($sql1);
                $dataReader = $command->query();

            
            }elseif($res_type==33){

                $total = 0;
                $qty = ($_POST['mandays']);
                $rate = ($qty * $_POST['resourcerate']);
                $total = $total + $rate;
                $sql = "INSERT INTO pricing_estimate_resources_new (activity_id,resource_Id,resourcetype_Id,quantity,rate,actual_qty,actual_rate,actual_amount,project_id,est_activity_Id,process_Id,pricing_status,operations_status,no_of_men,no_of_days) VALUES('" . $_POST['project_estimate_Id'] . "','" . $_POST['resourceid'] . "','".$_POST['resourcetypeid']."','" . $qty . "','" . $_POST['resourcerate'] . "','" . $qty . "','" . $_POST['resourcerate'] . "','" . $rate . "','" . $_POST['Project_Id'] . "','".$_POST['activityid']."','".$_POST['Process']."',0,0,'".$_POST['mannumber']."','".$_POST['nodays']."')";

                //echo $sql;exit;
                $command = $connection->createCommand($sql);
                $dataReader = $command->query();

                $sql1 = "UPDATE pricing_estimate_new SET specific_rate=" . $total . "  WHERE activity_Id='".$_POST['project_estimate_Id']."' ";
                //echo $sql1;exit;
                $command = $connection->createCommand($sql1);
                $dataReader = $command->query();

            }
            else{
                $total = 0;
                $rate = ($_POST['resourceqty'] * $_POST['resourcerate']);
                $total = $total + $rate;
                $sql = "INSERT INTO pricing_estimate_resources_new (activity_id,resource_Id,resourcetype_Id,quantity,rate,actual_qty,actual_rate,actual_amount,project_id,est_activity_Id,process_Id,pricing_status,operations_status) VALUES('" . $_POST['project_estimate_Id'] . "','" . $_POST['resourceid'] . "','".$_POST['resourcetypeid']."','" . $_POST['resourceqty'] . "','" . $_POST['resourcerate'] . "','" . $_POST['resourceqty'] . "','" . $_POST['resourcerate'] . "','" . $rate . "','" . $_POST['Project_Id'] . "','".$_POST['activityid']."','".$_POST['Process']."',0,0)";

                //echo $sql;exit;
                $command = $connection->createCommand($sql);
                $dataReader = $command->query();

                $sql1 = "UPDATE pricing_estimate_new SET specific_rate=" . $total . "  WHERE activity_Id='".$_POST['project_estimate_Id']."' ";
                //echo $sql1;exit;
                $command = $connection->createCommand($sql1);
                $dataReader = $command->query();

            }

            $sql2 = "UPDATE workgroup_activities_new SET activity_Name='" . $name . "',activity_Unit='".$unit."'  WHERE id='".$_POST['project_estimate_Id']."' ";
            //echo $sql2;exit;
            $command = $connection->createCommand($sql2);
            $dataReader = $command->query();
        }
        $sqltemvals="SELECT a.Vendor_Id,a.Resource_Id,a.Name AS resource,a.Unit,a.ResourceType_Id,a.Resource_Location,b.est_resource_quantity AS Quantity,b.est_resource_amount AS Price,b.est_resource_rate AS actual_price,b.fuel_type,b.fuel_rate,b.fuel_qty,b.hours_no,b.repair_cost,b.no_of_men,b.no_of_days,b.lease_period FROM resources AS a
                INNER JOIN estactivity_resources AS b ON  a.Resource_Id=b.est_resource_id
                WHERE b.estactivity_id='".$activityid."' ORDER BY a.Resource_Id";
        //echo $sqltemvals;exit;
        $estrescount_1=PricingEstimateResourcesNew::find()->where(['project_id'=>$_POST['Project_Id']])->andWhere(['activity_id'=>$itemid])->andWhere(['est_activity_Id'=>$_POST['activityid']])->andWhere(['process_Id'=>$_POST['Process']])->count();

        if($estrescount_1==0):

            $command=$connection->createCommand($sqltemvals);
            $dataReader=$command->query();
            $resourcesadded=$dataReader->readAll();

            foreach($resourcesadded AS $key=>$resourcesadd):

                //$sql = "INSERT INTO pricing_estimate_resources_new (activity_id,resource_Id,resourcetype_Id,quantity,rate,actual_qty,actual_rate,actual_amount,project_id,est_activity_Id,process_Id) VALUES('" . $_GET['project_estimate_Id'] . "','" . $_POST['resourceid'][$i] . "','".$_POST['resourcetypeid'][$i]."','" . $_POST['resourceqty'][$i] . "','" . $_POST['resourcerate'][$i] . "','" . $_POST['resourceqty'][$i] . "','" . $_POST['resourcerate'][$i] . "','" . $rate . "','" . $_GET['Project_Id'] . "','".$_GET['activityid']."','".$_GET['Process']."')";

                $amt = ($resourcesadd['Quantity'] * $resourcesadd['actual_price']);


                if($resourcesadd['ResourceType_Id'] == 26)
                {
                    if($resourcesadd['fuel_type'] != null)
                    {
                        if($resourcesadd['fuel_type'] != 'Power')
                        {
                            $sql = "INSERT INTO pricing_estimate_resources_new (activity_id,resource_Id,resourcetype_Id,quantity,rate,actual_qty,actual_rate,actual_amount,project_id,est_activity_Id,process_Id,pricing_status,operations_status,fuel_type,fuel_rate,fuel_qty,hours_no,repair_cost) VALUES('" . $itemid. "','" . $resourcesadd['Resource_Id'] . "','".$resourcesadd['ResourceType_Id']."','" . $resourcesadd['Quantity'] . "','" . $resourcesadd['actual_price'] . "','" . $resourcesadd['Quantity'] . "','" . $resourcesadd['actual_price'] . "','" . $amt . "','" . $_POST['Project_Id'] . "','".$activityid ."','".$_POST['Process']."',0,0,'" . $resourcesadd['fuel_type'] . "','" . $resourcesadd['fuel_rate'] . "','" . $resourcesadd['fuel_qty'] . "','" . $resourcesadd['hours_no'] . "','" . $resourcesadd['repair_cost'] . "')";

                        }else{
                            $sql = "INSERT INTO pricing_estimate_resources_new (activity_id,resource_Id,resourcetype_Id,quantity,rate,actual_qty,actual_rate,actual_amount,project_id,est_activity_Id,process_Id,pricing_status,operations_status,fuel_type,fuel_rate,fuel_qty,hours_no,repair_cost,powerstatus) VALUES('" . $itemid. "','" . $resourcesadd['Resource_Id'] . "','".$resourcesadd['ResourceType_Id']."','" . $resourcesadd['Quantity'] . "','" . $resourcesadd['actual_price'] . "','" . $resourcesadd['Quantity'] . "','" . $resourcesadd['actual_price'] . "','" . $amt . "','" . $_POST['Project_Id'] . "','".$activityid ."','".$_POST['Process']."',0,0,'" . $resourcesadd['fuel_type'] . "','" . $resourcesadd['fuel_rate'] . "','" . $resourcesadd['fuel_qty'] . "','" . $resourcesadd['hours_no'] . "','" . $resourcesadd['repair_cost'] . "','1')";

                        }

                        

                    }else{
                        $sql = "INSERT INTO pricing_estimate_resources_new (activity_id,resource_Id,resourcetype_Id,quantity,rate,actual_qty,actual_rate,actual_amount,project_id,est_activity_Id,process_Id,pricing_status,operations_status,lease_period) VALUES('" . $itemid. "','" . $resourcesadd['Resource_Id'] . "','".$resourcesadd['ResourceType_Id']."','" . $resourcesadd['Quantity'] . "','" . $resourcesadd['actual_price'] . "','" . $resourcesadd['Quantity'] . "','" . $resourcesadd['actual_price'] . "','" . $amt . "','" . $_POST['Project_Id'] . "','".$activityid ."','".$_POST['Process']."',0,0,1)";
                    }

                    

                }elseif($resourcesadd['ResourceType_Id'] == 24)
                {

                    if($resourcesadd['fuel_type'] != null)
                    {

                        if($resourcesadd['fuel_type'] != 'Power')
                        {
                            $sql = "INSERT INTO pricing_estimate_resources_new (activity_id,resource_Id,resourcetype_Id,quantity,rate,actual_qty,actual_rate,actual_amount,project_id,est_activity_Id,process_Id,pricing_status,operations_status,fuel_type,fuel_rate,fuel_qty,hours_no) VALUES('" . $itemid. "','" . $resourcesadd['Resource_Id'] . "','".$resourcesadd['ResourceType_Id']."','" . $resourcesadd['Quantity'] . "','" . $resourcesadd['actual_price'] . "','" . $resourcesadd['Quantity'] . "','" . $resourcesadd['actual_price'] . "','" . $amt . "','" . $_POST['Project_Id'] . "','".$activityid ."','".$_POST['Process']."',0,0,'" . $resourcesadd['fuel_type'] . "','" . $resourcesadd['fuel_rate'] . "','" . $resourcesadd['fuel_qty'] . "','" . $resourcesadd['hours_no'] . "')";

                        }else{
                            $sql = "INSERT INTO pricing_estimate_resources_new (activity_id,resource_Id,resourcetype_Id,quantity,rate,actual_qty,actual_rate,actual_amount,project_id,est_activity_Id,process_Id,pricing_status,operations_status,fuel_type,fuel_rate,fuel_qty,hours_no,powerstatus) VALUES('" . $itemid. "','" . $resourcesadd['Resource_Id'] . "','".$resourcesadd['ResourceType_Id']."','" . $resourcesadd['Quantity'] . "','" . $resourcesadd['actual_price'] . "','" . $resourcesadd['Quantity'] . "','" . $resourcesadd['actual_price'] . "','" . $amt . "','" . $_POST['Project_Id'] . "','".$activityid ."','".$_POST['Process']."',0,0,'" . $resourcesadd['fuel_type'] . "','" . $resourcesadd['fuel_rate'] . "','" . $resourcesadd['fuel_qty'] . "','" . $resourcesadd['hours_no'] . "','1')";
                        }
                        

                    }else{
                        $sql = "INSERT INTO pricing_estimate_resources_new (activity_id,resource_Id,resourcetype_Id,quantity,rate,actual_qty,actual_rate,actual_amount,project_id,est_activity_Id,process_Id,pricing_status,operations_status,lease_period) VALUES('" . $itemid. "','" . $resourcesadd['Resource_Id'] . "','".$resourcesadd['ResourceType_Id']."','" . $resourcesadd['Quantity'] . "','" . $resourcesadd['actual_price'] . "','" . $resourcesadd['Quantity'] . "','" . $resourcesadd['actual_price'] . "','" . $amt . "','" . $_POST['Project_Id'] . "','".$activityid ."','".$_POST['Process']."',0,0,'".$resourcesadd['lease_period']."')";
                    }

                }elseif($resourcesadd['ResourceType_Id'] == 33)
                {
                    $sql = "INSERT INTO pricing_estimate_resources_new (activity_id,resource_Id,resourcetype_Id,quantity,rate,actual_qty,actual_rate,actual_amount,project_id,est_activity_Id,process_Id,pricing_status,operations_status,no_of_men,no_of_days) VALUES('" . $itemid. "','" . $resourcesadd['Resource_Id'] . "','".$resourcesadd['ResourceType_Id']."','" . $resourcesadd['Quantity'] . "','" . $resourcesadd['actual_price'] . "','" . $resourcesadd['Quantity'] . "','" . $resourcesadd['actual_price'] . "','" . $amt . "','" . $_POST['Project_Id'] . "','".$activityid ."','".$_POST['Process']."',0,0,'".$resourcesadd['no_of_men']."','".$resourcesadd['no_of_days']."')";
                }else{

                

                $sql = "INSERT INTO pricing_estimate_resources_new (activity_id,resource_Id,resourcetype_Id,quantity,rate,actual_qty,actual_rate,actual_amount,project_id,est_activity_Id,process_Id,pricing_status,operations_status) VALUES('" . $itemid. "','" . $resourcesadd['Resource_Id'] . "','".$resourcesadd['ResourceType_Id']."','" . $resourcesadd['Quantity'] . "','" . $resourcesadd['actual_price'] . "','" . $resourcesadd['Quantity'] . "','" . $resourcesadd['actual_price'] . "','" . $amt . "','" . $_POST['Project_Id'] . "','".$activityid ."','".$_POST['Process']."',0,0)";
                //echo $sql;exit;
                }
                $command = $connection->createCommand($sql);
                $dataReader = $command->query();

            endforeach;

        endif;

        $estrescount=PricingEstimateResourcesNew::find()->where(['project_id'=>$_POST['Project_Id']])->andWhere(['activity_id'=>$itemid])->andWhere(['est_activity_Id'=>$_POST['activityid']])->andWhere(['process_Id'=>$_POST['Process']])->andWhere(['pricing_status'=>0])->count();
        //echo $estrescount;exit;
        $price=0;
        $addedones='';
        if($estrescount!=0):
            /*$command=$connection->createCommand($sqltemvals);
            $dataReader=$command->query();
            $resourcesadded=$dataReader->readAll();
            $addedones='';
            $price=0;
            foreach($resourcesadded AS $key=>$data):
                $price=$price+$data['Price'];
                $res_type = Resourcetype::find()->where(['ResourceType_Id'=>$data['ResourceType_Id']])->one(); 
                $addedones.='<div class="row">
                                        <div class="col-md-1">
                                        <span class="number">'.$sno.'</span>
                                        </div>
                                        <!-- <div class="col-md-3 type  ">
                                            <label>&nbsp;</label><br>
                                            <span>'.$res_type->Name.'</span>
                                        </div> -->
                                        <div class="col-md-3 type">
                                            <label>Resource:</label><br>
                                            <span>'.$data['resource'].'</span>
                                        </div>
										<div class="col-md-6 type vendor-column ">
											<div class="row">
											<!-- <div class="col-md-3 type">
												<label>Resource:</label><br>
												<span>'.$data['resource'].'</span>
											</div> -->
                                            <div class="col-md-4 type">
												<label>Quantity:</label><br>
                                                <span>'.$data['Quantity'].' - '.$data['Unit'].'</span>
											</div>
											<div class="col-md-4 type">
												<label>Rate:</label><br>
                                                <span>'.$data['actual_price'].'</span>
                                                <span id="Estimateiowactivityunit'.$data['pricing_resourceid'].'">'.$data['actual_price'].'</span>
                                                <input class="form-control editallocateresourceunit" style="display:none;" type="text" id="editEstimateiowactivityunit'.$data['pricing_resourceid'].'" data-v="'.$data['pricing_resourceid'].'" value="'.$data['actual_price'].'">
											</div>
											<div class="col-md-4 amount-column type ">
												<label>Amount:</label><br>
												<span>'.number_format((float)$data['Price'], 2, '.', '').'</span>
											</div>
											</div>
										</div>
										
										
                                        <div class="col-md-2 icon-groups">
                                            <a href="javascript:void(0);" class="btn btn-primary updateallocateresourceunit icon-edit" data-v="'.$data['pricing_resourceid'].'" id="updateallocateresourceunit'.$data['pricing_resourceid'].'"></a>
                                            <a href="javascript:void(0);" style="display:none;" class="btn btn-primary saveallocateresourceunit icon-save" data-v="'.$data['pricing_resourceid'].'" data-activity="'.$data['activity_id'].'" id="saveallocateresourceunit'.$data['pricing_resourceid'].'"></a>
											<a data-id="'.$data['pricing_resourceid'].'" href="javascript:void(0);" class="btn btn-primary removeresourceitem icon-trash1"></a>
										</div>
									</div>';
            endforeach;
        else:*/
            $estimateresources=PricingEstimateResourcesNew::find()->where(['activity_id'=>$itemid])->andWhere(['est_activity_Id'=>$_POST['activityid']])->andWhere(['project_id'=>$_POST['Project_Id']])->andWhere(['process_Id'=>$_POST['Process']])->andWhere(['pricing_status'=>0])->orderBy(['pricing_resourceid'=>SORT_DESC])->all();
            
            
            //echo count($estimateresources);exit;
            $addedones.='<div class="row reshds">
                            <div class="col-md-1">
                                    <label>#</label>
                            </div>
                            <div class="col-md-2">
                                <label>Resource Type</label>
                            </div>
                            <div class="col-md-2">
                                <label>Resource</label>
                            </div>
                            <div class="col-md-5">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label>Unit</label>
                                    </div>

                                    <div class="col-md-3">
                                        <label>Quantity</label>
                                    </div>

                                    <div class="col-md-3">
                                        <label>Rate</label>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <label>Amount</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                            </div>
                        </div>';
            foreach($estimateresources AS $estimateresource):
            $sno++;
			$res_type = Resourcetype::find()->where(['ResourceType_Id'=>$estimateresource['resourcetype_Id']])->one(); 
            if($res_type){
               $res_typename = $res_type->Name; 
            }
            else{
                $res_typename = ''; 
            } 
			$res = Resources::find()->where(['Resource_Id'=>$estimateresource['resource_Id']])->one();

                /*if($estimateresource['resourcetype_Id']==26){

                    $rate = $estimateresource['fuel_rate'];
                    $qty = $estimateresource['batch_unit'] * $estimateresource['fuel_qty'] * $estimateresource['hours_no'];
                    $price=$price + ($rate * $qty);
                    $amount= $rate * $qty;

                    $addedones.='<div id="tempresrow'.$estimateresource['pricing_resourceid'].'" class="row estlist">
                                            <div class="col-md-1 numvert" style="bottom: 5px;">
                                                <span class="number">'.$sno.'</span>
                                            </div>
                                                <!-- <div class="col-md-3 type  ">
                                                    
                                                    <span>'.$res_typename.'</span>
                                                </div> -->
                                            <div class="col-md-3 type renam">
                                               
                                                <span>'.$res->Name.'</span>
                                            </div>
                                            <div class="col-md-6 type vendor-column ">
                                                <div class="row">
                                                
                                                <!-- <div class="col-md-3 type">
                                                    
                                                    <span>'.$res->Name.'</span>
                                                </div> -->
                                                <div class="col-md-3 type actunntt">
                                                    
                                                    <span>'.$res->Unit.'</span>
                                                </div>
                                                
                                                <div class="col-md-3 type actrat" style="text-align: right;">
                                                    
                                                    <span id="Estimateiowactivityunit'.$estimateresource['pricing_resourceid'].'">'.number_format($rate,2).'</span>
                                                    <input class="form-control editallocateresourceunit" style="display:none;" type="text" id="editEstimateiowactivityunit'.$estimateresource['pricing_resourceid'].'" data-v="'.$estimateresource['pricing_resourceid'].'" value="'.$rate.'">
                                                    <span class="error" style="color:red"></span>
                                                </div>
                                                <div class="col-md-3 type actqtty">
                                                    
                                                    <span id="Estimateiowactivityqty'.$estimateresource['pricing_resourceid'].'">'.$qty.'</span>
                                                    <input class="form-control editallocateresourceqty" style="display:none;" type="text" id="editEstimateiowactivityqty'.$estimateresource['pricing_resourceid'].'" data-v="'.$estimateresource['pricing_resourceid'].'" value="'.$qty.'">
                                                    <span class="error" style="color:red"></span>
                                                </div>
                                                <div class="col-md-3 amount-column type text-right actamnnnt">
                                                    
                                                    <span class="resource-amount" id="editallocateresourceamount'.$estimateresource['pricing_resourceid'].'">'.number_format((float)$amount, 2, '.', '').'</span>
                                                </div>
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="col-md-2 icon-groups">
                                                <a href="javascript:void(0);" class="btn btn-primary updateallocateresourceunit icon-edit" data-v="'.$estimateresource['pricing_resourceid'].'" id="updateallocateresourceunit'.$estimateresource['pricing_resourceid'].'" title="Edit Resource"></a>
                                                <a href="javascript:void(0);" style="display:none;" class="btn btn-primary saveallocateresourceunit icon-save" data-v="'.$estimateresource['pricing_resourceid'].'" data-activity="'.$estimateresource['activity_id'].'" id="saveallocateresourceunit'.$estimateresource['pricing_resourceid'].'" title="save"></a>
                                                <a data-id="'.$estimateresource['pricing_resourceid'].'" href="javascript:void(0);" title="Delete Resource" class="btn btn-primary rem-res-estimate icon-trash1"></a>
                                            </div>
                                        </div>';

                }
                else{*/

                    $price=$price + ($estimateresource['rate'] * $estimateresource['quantity']);
                    $amount=$estimateresource['quantity'] * $estimateresource['rate'];
                    /*if($estimateresource['lease_period'] != null)
                    {
                        $amount = $estimateresource['rate'];
                    }*/
                    if($res_type->ResourceType_Id ==26 && $estimateresource['fuel_type'] != null || $res_type->ResourceType_Id ==24 && $estimateresource['fuel_type'] != null)
                    {
                       
                        $units = 'No of Hours';

                    }else{
                        
                        $units = $res->Unit;
                    }
                    $addedones.='<div id="tempresrow'.$estimateresource['pricing_resourceid'].'" class="row estlist">
                                            <div class="col-md-1 numvert" style="bottom: 5px;">
                                                <span class="number">'.$sno.'</span>
                                            </div>
                                                <!-- <div class="col-md-3 type  ">
                                                    
                                                    <span>'.$res_typename.'</span>
                                                </div> -->

                                            <div class="col-md-2 type renam">
                                               
                                                <span>'.$res_typename.'</span>
                                            </div>

                                            <div class="col-md-2 type renam">
                                               
                                                <span>'.$res->Name.'</span>
                                            </div>
                                            
                                            <div class="col-md-5 type vendor-column ">
                                                <div class="row">
                                                
                                                <!-- <div class="col-md-3 type">
                                                    
                                                    <span>'.$res->Name.'</span>
                                                </div> -->
                                                <div class="col-md-3 type actunntt">';
                                                
                                                  $addedones.='  <span>'.$units.'</span>';
                                                
                                                    
                                                    
                                               $addedones.=' </div>
                                                
                                                
                                                <div class="col-md-3 type actqtty">
                                                    
                                                    <span id="Estimateiowactivityqty'.$estimateresource['pricing_resourceid'].'">'.$estimateresource['quantity'].'</span>
                                                    <input class="form-control editallocateresourceqty" style="display:none;" type="text" id="editEstimateiowactivityqty'.$estimateresource['pricing_resourceid'].'" data-v="'.$estimateresource['pricing_resourceid'].'" value="'.$estimateresource['quantity'].'">
                                                    <span class="error" style="color:red"></span>
                                                </div>

                                                <div class="col-md-3 type actrat" style="text-align: right;">
                                                    
                                                    <span id="Estimateiowactivityunit'.$estimateresource['pricing_resourceid'].'">'.number_format($estimateresource['rate'],2).'</span>
                                                    <input class="form-control editallocateresourceunit" style="display:none;" type="text" id="editEstimateiowactivityunit'.$estimateresource['pricing_resourceid'].'" data-v="'.$estimateresource['pricing_resourceid'].'" value="'.$estimateresource['rate'].'">
                                                    <span class="error" style="color:red"></span>
                                                </div>
                                                
                                                <div class="col-md-3 amount-column type text-right actamnnnt">
                                                    
                                                    <span class="resource-amount" id="editallocateresourceamount'.$estimateresource['pricing_resourceid'].'">'.number_format((float)$amount, 2, '.', '').'</span>
                                                </div>
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="col-md-2 icon-groups">
                                                <a href="javascript:void(0);" class="btn btn-primary updateallocateresourceunit icon-edit" data-v="'.$estimateresource['pricing_resourceid'].'" id="updateallocateresourceunit'.$estimateresource['pricing_resourceid'].'" title="Edit Resource"></a>
                                                <a href="javascript:void(0);" style="display:none;" class="btn btn-primary saveallocateresourceunit icon-save" data-v="'.$estimateresource['pricing_resourceid'].'" data-activity="'.$estimateresource['activity_id'].'" id="saveallocateresourceunit'.$estimateresource['pricing_resourceid'].'" title="save"></a>
                                                <a data-id="'.$estimateresource['pricing_resourceid'].'" href="javascript:void(0);" title="Delete Resource" class="btn btn-primary rem-res-estimate icon-trash1"></a>
                                            </div>
                                        </div>';

                //}

            endforeach;
        endif;


        $acivitycost='Activity: '.$activityname.' '; 
        $acivitycostnew='Rate: <span id="productratetotal"> '.number_format($price,2).'</span>';
        $acivityunitnew='Unit: <span> '.$activityunit.'</span>';
 		$arr = array('datarows'=>$datarows,'sno'=>$sno, 'activityname'=>$activityname, 'activityunit'=>$activityunit, 'result' => $addedones,'acivitycost'=>$acivitycost,'acivitycostnew'=>$acivitycostnew, 'activityunitnew'=>$acivityunitnew, 'error' => 'No');
        return json_encode($arr);

    }

    public function actionActivityresourcesreport()
    {
    	$sno=0;
        $connection = Yii::$app->db;
        $process=$_POST['Process'];
        $activityid=$_POST['activityid'];
        $itemid=$_POST['project_estimate_Id'];
        $project = Projects::findOne($_POST['Project_Id']);
        $sql="SELECT * FROM estimateactivities WHERE activity_status=0 AND activity_id=".$activityid." ";
        $command1 = $connection->createCommand($sql);
        $dataReader1 = $command1->query();
        $est_act = $dataReader1->read();
        $item = WorkgroupActivitiesNew::findOne($_POST['project_estimate_Id']);
        if($item['activity_Name']!=''){
            $activityname=$item['activity_Name'];
        }else{
            $activityname=$est_act['activity_name'];
        }
        if($item['activity_Unit']!=''){
            $activityunit=$item['activity_Unit'];
        }else{
            $activityunit=$est_act['activity_unit'];
        }

         $resourcetypes = Resourcetype::find()->orderBy(['sortorder' => SORT_ASC])->all();
         $datarows='';
         foreach ($resourcetypes as $key => $resourcetype) {
         if($key==0)
         {
            $datarows.='<label>Resource Types</label>';   
         }
          $datarows.='<a  class="btn btn-primary rounded-coner-btn resourcesearch" data-id="'.$resourcetype->ResourceType_Id.'" href="javascript:void(0)">'.$resourcetype->Name.'</a>';   
         }

        $sqltemvals="SELECT a.Vendor_Id,a.Resource_Id,a.Name AS resource,a.Unit,a.ResourceType_Id,a.Resource_Location,b.est_resource_quantity AS Quantity,b.est_resource_amount AS Price,b.est_resource_rate AS actual_price FROM resources AS a
                INNER JOIN estactivity_resources AS b ON  a.Resource_Id=b.est_resource_id
                WHERE b.estactivity_id='".$activityid."' ORDER BY a.Resource_Id";
        //echo $sqltemvals;exit;
        $estrescount=PricingEstimateResourcesNew::find()->where(['project_id'=>$_POST['Project_Id']])->andWhere(['activity_id'=>$itemid])->andWhere(['est_activity_Id'=>$_POST['activityid']])->andWhere(['process_Id'=>$_POST['Process']])->andWhere(['pricing_status'=>0])->count();
        //echo $estrescount;exit;
        if($estrescount==0):
            $command=$connection->createCommand($sqltemvals);
            $dataReader=$command->query();
            $resourcesadded=$dataReader->readAll();
            $price=0;
            $addedones='';
            foreach($resourcesadded AS $key=>$data):
                $price=$price+$data['Price'];
                $res_type = Resourcetype::find()->where(['ResourceType_Id'=>$data['ResourceType_Id']])->one(); 
                $addedones.='<tr>
                <td>'.($key + 1).'</td>
                <td><span>'.$data['resource'].'</span></td>
                <td style="text-align: center"><span>'.$data['Unit'].'</span>
                <td style="text-align: center"><span>'.$data['Quantity'].'</span>
                <td style="text-align: right"><span>'.$data['actual_price'].'</span></td>
                <td style="text-align: right"><span>'.number_format((float)$data['Price'], 2, '.', '').'</span></td>
            </tr>';
            endforeach;
            $addedones.="<tr>
                            <th>
                                </th>
                                <th colspan='4'>Resource Total</th>
                                
                                <th colspan='2' style='text-align: right'>".number_format((float)$price, 2, '.', '')."</th>
                                <th colspan='2'></th>
                            </tr>";
        else:
        $estimateresources=PricingEstimateResourcesNew::find()->where(['activity_id'=>$itemid])->andWhere(['est_activity_Id'=>$_POST['activityid']])->andWhere(['project_id'=>$_POST['Project_Id']])->andWhere(['process_Id'=>$_POST['Process']])->andWhere(['pricing_status'=>0])->all();
            $addedones='';
            $price=0;
            //echo count($estimateresources);exit;
          
            foreach($estimateresources AS $key => $estimateresource):
			$res_type = Resourcetype::find()->where(['ResourceType_Id'=>$estimateresource['resourcetype_Id']])->one(); 
			$res = Resources::find()->where(['Resource_Id'=>$estimateresource['resource_Id']])->one();

                $price=$price + ($estimateresource['rate'] * $estimateresource['quantity']);
                $amount=$estimateresource['quantity'] * $estimateresource['rate'];
                $addedones.='<tr>
                    <td>'.($key + 1).'</td>
                    <td><span>'.$res->Name.'</span></td>
                    <td style="text-align: center"><span>'.$res->Unit.'</span>
                    <td style="text-align: center"><span>'.number_format((float)$estimateresource['quantity'], 3, '.', '').'</span>
                    <td style="text-align: right"><span>'.$estimateresource['rate'].'</span></td>
                    <td style="text-align: right"><span>'.number_format((float)$amount, 2, '.', '').'</span></td>
                    </tr>';
            endforeach;
            $addedones.="<tr>
                            <th>
                                </th>
                                <th colspan='4'>Resource Total</th>
                                
                                <th colspan='2' style='text-align: right'>".number_format((float)$price, 2, '.', '')."</th>
                                <th colspan='2'></th>
                            </tr>";
        endif;


        $acivityhead='IOW: '.$_POST['iowName'].' ';
        $acivityhead2=' Activity: '.$activityname;
 		$arr = array('datarows'=>$datarows,'activityunit'=>$activityunit, 'result' => $addedones,'acivityhead'=>$acivityhead,'acivityhead2'=>$acivityhead2, 'error' => 'No');
        return json_encode($arr);

    }
    public function actionDeleteprojres()
    {
        $estimateres=PricingEstimateResourcesNew::findOne($_POST['projresid']);
        $estimateres->pricing_status=1;
        //$estimateres->operations_status=1;
        $uid = Yii::$app->user->Id;
        if($estimateres->save(false)):

            $resource=Resources::findOne($estimateres->resource_Id);

            $jobcards=Jobcard::find()->where(['vendor_id'=>$resource->Vendor_Id])->andWhere(['app_activity'=>$estimateres->activity_id])->andWhere(['pricing_resourceid'=>$_POST['projresid']])->andWhere(['delete_status'=>0])->all();
            

            $cartid = '';

            if($jobcards){
                foreach($jobcards AS $jobcard){
                $cart = Cart::find()->where(['Job_Id'=>$jobcard->job_id])->one();
                if($cart){
                    $cartid = $cart->cartID;
                }

                $model = new PlaceorderResAudit();
                $model->pricing_resourceid = $estimateres->pricing_resourceid;
                $model->deleted_by = $uid;
                $model->deleted_date = date("Y-m-d H:i:s");
                if($model->save(false)):

                    $placeorderstatus = new PlaceorderStatus();
                    $placeorderstatus->cart_id = $cartid;
                    $placeorderstatus->deleted_by = $uid;
                    $placeorderstatus->deleted_date = date("Y-m-d H:i:s");
                    if($placeorderstatus->save(false)):

                        $cart->status = 5;
                        $cart->save(false);

                        $jobcard->delete_status = 1;
                        $jobcard->save(false);

                        $placeorderes = PlaceorderRes::find()->where(['cartID' => $cart->cartID])->andWhere(['Vendor_Id' => $cart->Vendor_Id])->andwhere(['Resource_Id' =>$cart->Resource_Id])->andWhere(['Project' =>$cart->Project])->one();
                        if($placeorderes){
                            $placeorderes->delete();
                        }

                        $ores = OrderedResource::find()->Where(['jobcard_id'=>$cart->Job_Id])->one();

                        if($ores):

                            $orderedresourceitems=OrderedResource::find()->Where(['order_id'=>$ores->order_id])->all();

                            if($orderedresourceitems):

                                foreach($orderedresourceitems as $orderedresourceitem):

                                    $orderedresourceitem->delete_status = 1;
                                    $orderedresourceitem->save(false);                       

                                endforeach;

                            endif;

                            $order=Orders::findOne($ores->order_id);
                            $order->delete_status = 1;
                            $order->save(false);

                            $placeorderstatus->order_id = $ores->order_id;
                            $placeorderstatus->save(false);

                        endif;

                    endif;

                endif;   
            }
            
            }

        endif;

        //$estimateres->delete();
        $arr = array('error' => 'No','projestres'=>$_POST['projresid']);
        return json_encode($arr);

    }

    public function actionCheckresexist()
    {

       

        if (isset($_POST['resid'])):
        $connection = \Yii::$app->db;
        $sql = "SELECT COUNT(*) AS total FROM pricing_estimate_resources_new WHERE resource_Id='" . $_POST['resid'] . "' AND pricing_status=0";
        $command = $connection->createCommand($sql);
        $dataReader = $command->query();
        $data = $dataReader->read();
        if ($data['total'] > 0):
            return 'Yes';
        else:
            return 'No';
        endif;

        endif;

        
    }

}
