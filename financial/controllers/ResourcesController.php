<?php

namespace app\controllers;  

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;  
use app\models\Vendors;  
use app\models\Resources; 
use app\models\AccountsItem;
use app\models\AccountsSub;   
use app\models\Brand;
use app\models\Bsitems;
use app\models\Resourcetype;  
use app\models\ResourceGroup;
use app\models\PricingEstimateResourcesNew;
use app\models\WorkgroupActivitiesNew; 
use app\models\ProjectSetupResources;   
use app\models\ProductResources;
use app\models\LogisticsResources;
use app\models\ConstructionResources;
use app\models\OverheadsResources;  
use app\models\SubgroupAccounts;
use app\models\Projects;
//ini_set('memory_limit', '1024M');

class ResourcesController extends Controller
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

  public function beforeAction($action) 
  { 
      $this->enableCsrfValidation = false; 
      return parent::beforeAction($action); 
  }

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','create','Search','Update','deleteresource','CheckResourceName','Updatesort','getresourceitems','getresourcedetails'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('Resourcedetails','Addvendor','Getresourcegroups','Updateresvendor'),
				'users'=>array('@'),
			),
/*			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),*/
			array('deny',  // deny all users
                'actions'=>array('view','admin','delete'),
				'users'=>array('*'),
			),
		);
	}

  public function actionAccounthead()
  {

      $restype=$_POST['acchead'];

      $accounthead=AccountsItem::find()->where(['LIKE', 'Name',$_POST['acchead']])->one();

      $datarow = "";

      if($restype!=''){

        $accountheadid = $accounthead->id;
        $accountsubgroups=SubgroupAccounts::find()->where(['account_id'=>$accountheadid])->one();
        $accountsubgrpid=$accountsubgroups->account_id;
        $accountsubgrpids=$accountsubgroups->subgrp_id;
        $subgroupdata=AccountsSub::find()->where(['id'=>$accountsubgrpids])->one();
        $subgrpname=$subgroupdata->name;

        //$datarow.= "<option value='".$subgrpname."'></option>";

        $accountsubs=AccountsSub::find()->where(['Status'=>0])->andWhere(['master_id'=>5])->all();
                            
        foreach($accountsubs AS $accountsub):
            $datarow.= "<option value='".$accountsub->name."'></option>";
        endforeach;

      }
      else{
        $subgrpname='';
        $accountsubs=AccountsSub::find()->where(['Status'=>0])->andWhere(['master_id'=>5])->all();
                            
        foreach($accountsubs AS $accountsub):
            $datarow.= "<option value='".$accountsub->name."'></option>";
        endforeach;

      }

      $arr=array('error'=>'No','result'=>$subgrpname,'datarow'=>$datarow);
      return json_encode($arr);

  }

  public function actionCreate()
  {

    //echo 'hai'; exit;

      $model = new Resources();

      if (isset($_POST['resourcename'])) {

          $model->Name = $_POST['resourcename'];
          if(isset($_POST['resourcelocation'])):
              $model->Resource_Location = $_POST['vendorlocation'];
          endif;
          $model->Unit = $_POST['unit'];
          //$model->fuel_unit = $_POST['equuunit'];
          $model->Price = $_POST['rate'];
          $model->ResourceType_Id = $_POST['restype'];

          if($_POST['resgroup']!='none'):
              $model->Resource_group_Id = $_POST['resgroup'];
          endif;

          if($_POST['equconsumption']!=''):
              $model->consumption = $_POST['equconsumption'];
          endif;

          if($_POST['equrate']!=''):
              $model->rate = $_POST['equrate'];
          endif;

          if($_POST['equmaintenancecost']!=''):
              $model->maintenance = $_POST['equmaintenancecost'];
          endif;

          if($_POST['machinerate']!=''):
              $model->machinecost = $_POST['machinerate'];
          endif;

          $vendor=Vendors::find()->Where(['LIKE', 'Name',$_POST['vendor']])->one();
          if($vendor)
          {
            
            $vendorid=$vendor->Vendor_Id;
            $vendor->City= $_POST['vendorlocation'];
            $vendor->Phone= $_POST['vendorphone'];
            $vendor->Email= $_POST['vendoremail'];
            $vendor->save(false);
          }
          else
          {
            

            $model2=new Vendors();
            $model2->Name= $_POST['vendor'];
            $model2->City= $_POST['vendorlocation'];
            $model2->Phone= $_POST['vendorphone'];
            $model2->Email= $_POST['vendoremail'];
            $model2->Added_On=date('y-m-d h:i:s');
            $model2->save(false);

            $accounthead_check=AccountsItem::find()->where(['LIKE', 'Name',$_POST['vendor']])->one();

            if($accounthead_check){
              $venaccnt=AccountsItem::findOne($accounthead_check->id);
            }
            else{

              $venaccnt=new AccountsItem();
              $venaccnt->name= $_POST['vendor'];
              $venaccnt->master_id=6;
              $venaccnt->account_type= 7;
              //$venaccnt->resource_id= $model->Resource_Id;
              $venaccnt->save(false);
              
              $vensbgrp=new SubgroupAccounts();
              $vensbgrp->group_id=6;
              $vensbgrp->subgrp_id=30;
              $vensbgrp->bsitem_id=12;
              $vensbgrp->account_id=$venaccnt->id;

              $vensbgrp->save(false);

            }
                             
            $vendorid = $model2->Vendor_Id;

            $vendordetails=Vendors::findOne($vendorid);
            $vendordetails->account_id=$venaccnt->id;
            $vendordetails->save(false);

          }

          $brand=Brand::find()->Where(['LIKE', 'name',$_POST['brand']])->one();

          if($brand)
          {
            $brandid=$brand->id;

          }
          else
          {
            $model3=new Brand();
            $model3->name= $_POST['brand'];
            $model3->save(false);
            $brandid = $model3->id;
          }

          $model->Resource_Acc_Id = 0;
          if(isset($_POST['resourcequantity'])):
            $model->Quantity = $_POST['resourcequantity'];
          endif;
          $model->Added_On = date('y-m-d h:i:s');

          if ($model->save(false)):
              
            $connection = \Yii::$app->db; 
            $sql="INSERT INTO vendor_brand (id,Vendor_Id,brand_id,resource_id,status) VALUES ('','". $vendorid."','".$brandid."','".$model->Resource_Id."',0)";
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();

            $accounthead=AccountsItem::find()->where(['LIKE', 'Name',$_POST['accounthead']])->one();

            if($accounthead)
            {
                $accountheadid = $accounthead->id;

                $accountsub=AccountsSub::find()->where(['LIKE', 'name',$_POST['accountsubgrp']])->one();

                $subgroupaccount=SubgroupAccounts::find()->where(['account_id'=>$accountheadid])->andWhere(['group_id'=>5])->one();
                if($subgroupaccount){
                  $subgroupaccount->subgrp_id = $accountsub->id;
                  $subgroupaccount->save(false);
                }
                else{
                    $sql="INSERT INTO subgroup_accounts (group_id,subgrp_id,accountschedule_id,account_id,bsitem_id) VALUES (5,'".$accountsub->id."',0,'".$accountheadid."',0)";
                    $command=$connection->createCommand($sql);
                    $dataReader=$command->query();
                }

                $acctsubforrestype = AccountsSub::find()->where(['ResourceType_Id' => $_POST['restype']])->one();

                $sql="INSERT INTO subgroup_accounts (group_id,subgrp_id,accountschedule_id,account_id,bsitem_id) VALUES (5,'".$acctsubforrestype->id."',0,'".$accountheadid."',0)";
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();


            }
            else
            {
                $model1=new AccountsItem();
                $model1->name= $_POST['accounthead'];
                $model1->account_type= 8;
                $model1->resource_id= $model->Resource_Id;
                if($model1->save(false)):

                    $accountsubid=AccountsSub::find()->where(['LIKE', 'name',$_POST['accountsubgrp']])->one();

                    $accountsub=AccountsSub::find()->where(['ResourceType_Id'=>$_POST['restype']])->one();

                    if($accountsub){


                    $connection = \Yii::$app->db; 
                    $sql="INSERT INTO subgroup_accounts (group_id,subgrp_id,accountschedule_id,account_id,bsitem_id) VALUES (1,'".$accountsub->id."',0,'".$model1->id."',0)";
                    $command=$connection->createCommand($sql);
                    $dataReader=$command->query();

                    }else{

                    $connection = \Yii::$app->db; 
                    $sql="INSERT INTO subgroup_accounts (group_id,subgrp_id,accountschedule_id,account_id,bsitem_id) VALUES (5,'".$accountsubid->id."',0,'".$model1->id."',0)";
                    $command=$connection->createCommand($sql);
                    $dataReader=$command->query();

                    }
                    

                    $acctsubforrestype = AccountsSub::find()->where(['ResourceType_Id' => $_POST['restype']])->one();

                    $sql="INSERT INTO subgroup_accounts (group_id,subgrp_id,accountschedule_id,account_id,bsitem_id) VALUES (5,'".$acctsubforrestype->id."',0,'".$model1->id."',0)";
                    $command=$connection->createCommand($sql);
                    $dataReader=$command->query();



                endif;

                $accountheadid = $model1->id;
            }

            if($model->ResourceType_Id==26):

              if($_POST['schedules']==1)
              {
                $bsitem = new Bsitems();
                $bsitem->acntsubgrp_id = 0;
                $bsitem->accnt_id = $accountheadid;
                $bsitem->res_id = $model->Resource_Id;  
                $bsitem->itemname = $model->Name;
                $bsitem->status = 0;
                $bsitem->save(false);
              }

            endif;

            $resource=Resources::findOne($model->Resource_Id); 
            $resource->Vendor_Id = $vendorid;
            $resource->Resource_Acc_Id = $accountheadid;
            $resource->save(false);

            //$vendordetails=Vendors::findOne($vendorid);
            //$vendordetails->account_id=$accountheadid;
            //$vendordetails->save(false);
        

            /*if(isset($_POST['addresourcetypeaccount'])):
              $account=AccountsItem::findOne($_POST['addresourcetypeaccount']);
             
              if($account->schedule=='3' & $model->ResourceType_Id=='26'):
                  $schedule=New Schedule();
                  $schedule->name=$_POST['resourcename'];
                  $schedule->resource_id=$model->Resource_Id;
                  $schedule->resource_type=$model->ResourceType_Id;
                  $schedule->save(false);
              endif;
            endif;*/
              $arr = array('Id' => $model->Resource_Id, 'Name' => $model->Name, 'Unit' => $model->Unit, 'Price' => $model->Price, 'ResourceType_Id' => $model->ResourceType_Id, 'error' => 'No');
              return json_encode($arr);
          else:
              $arr = array('error' => 'Yes', 'errortext' => 'Cannot add now. Please try again later');
               return json_encode($arr);
          endif;
      }

  }

  public function actionUpdate()
  {

    if (isset($_POST['resourceid'])) 
    {
        $connection = \Yii::$app->db;
              $sql="SELECT * FROM resources WHERE Name LIKE '%".$_POST['oldresourcename']."%' AND Status=0 AND pricing_status=0";
    		$command=$connection->createCommand($sql);
         		$dataReader=$command->query();
          	$resources=$dataReader->readAll();
    		$count = 0;
    		foreach($resources as $resource):
    			$model = $this->loadModel($resource['Resource_Id']);
    			$model->Name = $_POST['resourcename'];
          if (isset($_POST['resourceunit'])) :
              $model->Unit = $_POST['resourceunit'];
          endif;
          $model->save(false);
          $count++;
    		endforeach;

        if ($count > 0):
            $arr = array('Id' => $_POST['resourceid'], 'Name' => $_POST['resourcename'],'error' => 'No');
            return json_encode($arr);
        else:
            $arr = array('Id' => $_POST['resourceid'], 'error' => 'Yes', 'errortext' => 'Can not update now.');
            return json_encode($arr);
        endif;

    }

  }

  public function actionIndex()
  {
      $connection = CActiveRecord::getDbConnection();
      $sql="SELECT Name FROM resources  WHERE status='0' ORDER BY Resource_Id ASC, Name ASC";
      $command=$connection->createCommand($sql);
      $dataReader=$command->query();
      $resname=$dataReader->readAll();
      $dataProvider='';
      foreach($resname AS $key=>$res):
          if($key==0):
              $dataProvider="'".$res['Name']."'";
          else:
              $dataProvider.=",'".$res['Name']."'";
          endif;
      endforeach;
      $this->render('index',array(
          'dataProvider'=>$dataProvider,
      ));
  }

  /*public function actionSearch()
  {

      $connection = \Yii::$app->db; 
      $restypename='';
      $sql = "SELECT a.Resource_Id,a.Name,Unit,a.Price,a.ResourceType_Id,a.Vendor_Id,a.Project_Id,a.sortorder,a.Updated_On,b.Name AS restype,c.Name AS vendor,c.Email,c.Phone,c.Address,c.City,a.Resource_Acc_Id,a.Resource_Location,d.name,a.Resource_group_Id,a.Quantity  FROM resources AS a
      LEFT JOIN resourcetype AS b ON a.ResourceType_Id=b.ResourceType_Id
      LEFT JOIN vendors AS c ON c.Vendor_Id=a.Vendor_Id
      LEFT JOIN accounts_item AS d ON d.id=a.Resource_Acc_Id
      WHERE a.status='0' AND pricing_status=0 ";
      if(isset($_POST['resourcetype'])):
      if ($_POST['resourcetype'] != 'none')
          $sql .= "AND a.ResourceType_Id='" . $_POST['resourcetype'] . "'";
      endif;
      if(isset($_POST['resgroup'])):
      if ($_POST['resgroup'] != 'none')
          $sql .= "AND a.Resource_group_Id='" . $_POST['resgroup'] . "'";
      endif;
       if(isset($_POST['vendor'])):
      if ($_POST['vendor'] != 'none')
          $sql .= "AND a.Vendor_Id='" . $_POST['vendor'] . "'";
      endif;       
      if(isset($_POST['resourcename'])): 
      if ($_POST['resourcename'] != '')
          $sql .= "AND a.Name LIKE '%" . $_POST['resourcename'] . "%'";
      endif;
      if(isset($_POST['location'])):
      if ($_POST['location'] != '')
          $sql .= "AND a.Resource_Location LIKE '%" . $_POST['location'] . "%'";            
      $sql .= " GROUP BY a.Name ORDER BY a.Resource_group_Id ASC,sortorder ASC,a.Resource_Id ASC, a.Name ASC";
      endif;
      $command = $connection->createCommand($sql);
      $dataReader = $command->query();
      $dataProvider = $dataReader->readAll();
       if(isset($_POST['resourcetype'])):
      if ($_POST['resourcetype'] != 'none'):
           $restypenames=Resourcetype::findOne($_POST['resourcetype']);
           $restypename=":".$restypenames['Name'];
       endif;
      else:
          $restypename='';
      endif;
      if(isset($_POST['resgroup'])):
      if ($_POST['resgroup'] != 'none'):
          $resgroupname=":".ResourceGroup::find($_POST['resgroup'])->one()->Resource_group_Name;
      endif;
      else:
          $resgroupname='';
      endif;
      $datarows = '';
      $resource = '';
      $arr = array();

      $vendorlist=Vendors::find()->Where(['Status'=>0])->all();

      if (count($dataProvider) > 0):
          foreach ($dataProvider AS $key => $data):
              if($data['Quantity']==0):
                  $resourcename=$data['Name'];
              else:
                  $resourcename=$data['Name']." (".$data['Quantity'].")";
              endif;
              $typesval = '';
              $vendorval = '';
              $projectval = '';
              $accval = '';

              if($data['Updated_On']!='0000-00-00 00:00:00'):
                  $date=date('M, j ,  Y',strtotime($data['Updated_On']));    
              else:
                  $date='';
              endif;
              $sql = "SELECT a.Resource_Id,a.Name,Unit,a.Price,a.ResourceType_Id,a.Vendor_Id,a.Project_Id,a.sortorder,a.Updated_On,b.Name AS restype,c.Name AS vendor,c.Email,c.Phone,c.Address,c.City,a.Resource_Acc_Id,a.Resource_Location,d.name,a.Resource_group_Id,a.Quantity  FROM resources AS a
              LEFT JOIN resourcetype AS b ON a.ResourceType_Id=b.ResourceType_Id
              LEFT JOIN vendors AS c ON c.Vendor_Id=a.Vendor_Id
              LEFT JOIN accounts_item AS d ON d.id=a.Resource_Acc_Id
              WHERE a.status='0' AND pricing_status=0 AND a.Name='".$data['Name']."' ";
              if(isset($_POST['resourcetype'])):
              if ($_POST['resourcetype'] != 'none')
                  $sql .= "AND a.ResourceType_Id='" . $_POST['resourcetype'] . "'";
              endif;
              if(isset($_POST['resgroup'])):
              if ($_POST['resgroup'] != 'none')
                  $sql .= "AND a.Resource_group_Id='" . $_POST['resgroup'] . "'";
              endif;
              if(isset($_POST['vendor'])):
              if ($_POST['vendor'] != 'none')
                  $sql .= "AND a.Vendor_Id='" . $_POST['vendor'] . "'";
              endif;
              if(isset($_POST['resourcename'])):            
              if ($_POST['resourcename'] != '')
                  $sql .= "AND a.Name LIKE '%" . $_POST['resourcename'] . "%'";
              endif;
              if(isset($_POST['location'])):
              if ($_POST['location'] != '')
                  $sql .= "AND a.Resource_Location LIKE '%" . $_POST['location'] . "%'";
              endif;
              $sql .= " ORDER BY a.Resource_group_Id ASC,sortorder ASC,a.Resource_Id ASC, a.Name ASC";
              $command = $connection->createCommand($sql);
              $dataReader = $command->query();
              $childdataProvider = $dataReader->readAll();
              $childdatarows = '';
              $reschild='';

              $resource.='
                <tr  id="resourcerow' . $data['Resource_Id'] . '" class="vendor-column resource' . $data['Resource_Id'] . '" data-id="' . $data['Resource_Id'] . '">
                  <td><input type="hidden" value="' . $data['Resource_Id'] . '">'.($key+1).'</td>
                  <input type="hidden" value="' . $data['ResourceType_Id'] . '" id="resourcetypes' . $data['Resource_Id'] . '">
                  <td colspan="6">
                    <span>
                      <p style="font-weight: 0;">
                        <span id="resourcetext' . $data['Resource_Id'] . '">' . $resourcename . '</span>
                        <input class="form-control editresourcername" style="display:none;width: 300px;"   maxlength="30"  type="text" id="editresourcername' . $data['Resource_Id'] . '" value="' . $resourcename . '">
                        <input type="hidden" id="resourcername_old' . $data['Resource_Id'] . '" value="' . $resourcename . '">
                        <button type="button" class="editresourcebuttons" style="display: inline-block;"  value="' . $data['Resource_Id'] . '" id="editresourcebuttons' . $data['Resource_Id'] . '" data-vendor="'.$data['Vendor_Id'].'" data-mode="old" title="Edit Vendor"> <span class="icon-pencil editVendor-pencil" title="Edit Resource"></span></button>
                      </p>
                    </span>
                  </td>
                  <td colspan="3">
                    <div class="icon-groups">
                      <button type="button" class="saveresorbutton" style="display:none" value="' . $data['Resource_Id'] . '" id="saveresorbutton' . $data['Resource_Id'] . '" data-vendor="'.$data['Vendor_Id'].'" data-mode="old"><a title="Update Resource" href="#" class="icon-save"></a>
                      </button>
                      <button type="button" class="addvendor" value="' . $data['Resource_Id'] . '" id="addvendor' . $data['Resource_Id'] . '" title="Add Vendor">
                                <a  href="#" class="icon-add add-vendor-at-resource"></a>
                      </button>                          
                      <button type="button" class="deletresourcebutton" value="' . $data['Resource_Id'] . '" id="delet_resourcebutton' . $data['Resource_Id'] . '" data-mode="master" title="Delete Resource">
                        <a title="Delete Resource" href="#" class="icon-trash1"></a>
                      </button>                       
                    </div>
                  </td>
                </tr>';  

                foreach ($childdataProvider AS $key1 => $childdata):
                  if($childdata['Vendor_Id']!=0):
                      if(isset($_POST['brand']) && $_POST['brand']!='none'){
                          
                          $vendorbrandsqls='SELECT * FROM vendor_brand WHERE Vendor_Id='. $childdata['Vendor_Id'].' AND resource_id='.$childdata['Resource_Id'].' AND brand_id='.$_POST['brand'].' ';  
                          $command = $connection->createCommand($vendorbrandsqls);
                          $dataReader = $command->query();
                          $vendorbrand1 = $dataReader->read();
                          
                          if($vendorbrand1):
                              
                            $resource.='
                              <tr id="resvendorrow' . $childdata['Resource_Id'] . '">
                                <td colspan="2"></td>
                                <td><span id="resourceloctext' . $childdata['Resource_Id'] . '">' . $childdata['City'] . '</span>
                                  <input class="form-control" style="display:none" type="text" id="editresourcelocation'.$childdata['Resource_Id'].'" name="resourcelocation" placeholder="Location" value="'.$childdata['City'].'">
                                  <span class="error" ></span></td>
                                <td><span id="resourcevendtext' . $childdata['Resource_Id'] . '">' . $childdata['vendor'] . '</span>
                                  <select class="form-control" style="display:none" id="editresourcevendor'.$childdata['Resource_Id'].'">
                                  <option value="none">Select Vendor</option>'.$vendorval.'</select><span class="error"></span>
                                </td>';                     
                  
                                $vendorbrandsql='SELECT * FROM vendor_brand WHERE Vendor_Id='. $childdata['Vendor_Id'].' AND resource_id='.$childdata['Resource_Id'].' ';  
                                $command = $connection->createCommand($vendorbrandsql);
                                $dataReader = $command->query();
                                $vendorbrand = $dataReader->read();
                                $brand=Brand::find()->where(['id'=>$vendorbrand['brand_id']])->one();

                                $resource.='
                                <td>'.$brand['name'].'</td><td>' . $childdata['Unit'] . '</td>
                                <td><span id="resourceratetext' . $childdata['Resource_Id'] . '">' .number_format( $childdata['Price'],2) . '</span>
                                          <input class="form-control editresourcerate" style="display:none"  type="text" id="editresourcerate' . $childdata['Resource_Id'] . '" value="' . $childdata['Price'] . '">
                                          <span class="error"></span>
                                </td>
                                <td></td>                 
                                <td colspan="2">
                                  <div class="icon-groups">                 
                                    <button type="button" class="editvendorbutton" style="display: inline-block;"  value="' . $childdata['Resource_Id'] . '" id="edit_vendorbutton' . $childdata['Resource_Id'] . '" data-vendor="'.$childdata['Vendor_Id'].'" data-mode="old" title="Edit Vendor"> <span class="icon-pencil editVendor-pencil" title="Edit Resource"></span></button>
                                    <button type="button" class="saveresvendorbutton" style="display:none" value="' . $childdata['Resource_Id'] . '" id="saveresvendorbutton' . $childdata['Resource_Id'] . '" data-vendor="'.$childdata['Vendor_Id'].'" data-mode="old" title="Update Vendor"><a href="#" class="icon-save"></a></button>                       
                            
                                    <button type="button" class="deletresourcebutton" value="' . $childdata['Resource_Id'] . '" id="deletresourcebutton' . $childdata['Resource_Id'] . '" data-mode="old" title="Delete Resource">
                                    <a href="#" class="icon-trash1"></a></button>
                                  </div>
                                </td>
                              </tr>'; 
                              
                          endif;
                          
                      }
                      else
                      {
                           $resource.='
                              <tr id="resvendorrow' . $childdata['Resource_Id'] . '">
                                <td></td>
                                <td width="30%"><span id="resourcevendtext' . $childdata['Resource_Id'] . '">' . $childdata['vendor'] . '</span>
                                </td>
                                <td><span id="resourceloctext' . $childdata['Resource_Id'] . '">' . $childdata['City'] . '</span>
                                </td>';                    
              
                                $vendorbrandsql='SELECT * FROM vendor_brand WHERE Vendor_Id='. $childdata['Vendor_Id'].' AND resource_id='.$childdata['Resource_Id'].' ';  
                                $command = $connection->createCommand($vendorbrandsql);
                                $dataReader = $command->query();
                                $vendorbrand = $dataReader->read();
                                $brand=Brand::find()->where(['id'=>$vendorbrand['brand_id']])->one();

                                $resource.='
                                <td>'.$brand['name'].'</td><td>' . $childdata['Unit'] . '</td>
                                <td><span id="resourceratetext' . $childdata['Resource_Id'] . '">' .number_format( $childdata['Price'],2) . '</span>
                                  <input class="form-control editresourcerate"    style="display:none;width: 100px;"  type="text" id="editresourcerate' . $childdata['Resource_Id'] . '" value="' . $childdata['Price'] . '">
                                  <span class="error"></span>


                                  <button type="button" class="editvendorbutton" style="display: inline-block;"  value="' . $childdata['Resource_Id'] . '" id="edit_vendorbutton' . $childdata['Resource_Id'] . '" data-vendor="'.$childdata['Vendor_Id'].'" data-mode="old" title="Edit Vendor"> <span class="icon-pencil editVendor-pencil" title="Edit Resource"></span></button>
                                </td>   

                                <td><span id="lastupdated">'.$date.'</span></td>      
                                  <input type="hidden" value="' . $childdata['ResourceType_Id'] . '" id="resourcetypesnames'. $childdata['Resource_Id'] . '">         
                              
                                <td colspan="3">
                                    <div class="icon-groups">
                                        <button type="button" class="saveresvendorbutton" style="display:none" value="' . $childdata['Resource_Id'] . '" id="saveresvendorbutton' . $childdata['Resource_Id'] . '" data-vendor="'.$childdata['Vendor_Id'].'" data-mode="old" title="Update Vendor"><a href="#" class="icon-save"></a></button>
                                                      
                                        <button type="button" class="deletresourcebutton" value="' . $childdata['Resource_Id'] . '" id="deletresourcebutton' . $childdata['Resource_Id'] . '" data-mode="old" title="Delete Resource">
                                        <a href="#" class="icon-trash1"></a></button>
                                    </div>
                                </td>
                              </tr>';       
                
                      }
             
                  else:
                       $resource.=''; 
                  endif;
                endforeach;                         
          endforeach;
          $arr['result'] = $resource;
          $arr['error'] = 'No';
          $arr['hasdata'] = 'Yes';
          $arr['restypename'] = $restypename; 
      else:
          $resource = '<tr id="nodata"><td colspan="11" style="text-align: center;">No Resources Found</td></tr>';
          $arr['result'] = $resource;
          $arr['error'] = 'No';
          $arr['hasdata'] = 'No';
          $arr['restypename'] = $restypename;
      endif;
      return json_encode($arr); 
  }*/

  public function actionSearch() 
  {

      $connection = \Yii::$app->db; 
      $whitespace = 'UPDATE resources SET Name = RTRIM(Name)';
      $command = $connection->createCommand($whitespace);
      $dataReader = $command->query();
      $restype_id = $_POST['resourcetype'];
      $restypename='';
      $sql = "SELECT a.Resource_Id,a.Name,Unit,a.Price,a.ResourceType_Id,a.Vendor_Id,a.Project_Id,a.sortorder,a.Updated_On,b.Name AS restype,c.Name AS vendor,c.Email,c.Phone,c.Address,c.City,a.Resource_Acc_Id,a.Resource_Location,d.name,a.Resource_group_Id,a.Quantity  FROM resources AS a
      LEFT JOIN resourcetype AS b ON a.ResourceType_Id=b.ResourceType_Id
      LEFT JOIN vendors AS c ON c.Vendor_Id=a.Vendor_Id
      LEFT JOIN accounts_item AS d ON d.id=a.Resource_Acc_Id
      WHERE a.Status=0 AND pricing_status=0 ";

      if(isset($_POST['resourcetype'])):
        if ($_POST['resourcetype'] != 'none')
          $sql .= "AND a.ResourceType_Id='" . $_POST['resourcetype'] . "'";
      endif;
      if(isset($_POST['resgroup'])):
        if ($_POST['resgroup'] != 'none')
          $sql .= "AND a.Resource_group_Id='" . $_POST['resgroup'] . "'";
      endif;
      if(isset($_POST['vendor'])):
        if ($_POST['vendor'] != 'none')
          $sql .= "AND a.Vendor_Id='" . $_POST['vendor'] . "'";
      endif;       
      if(isset($_POST['resourcename'])): 
        if ($_POST['resourcename'] != '')
          $sql .= "AND a.Name LIKE '%" . $_POST['resourcename'] . "%'";
      endif;
      if(isset($_POST['location'])):
        if ($_POST['location'] != '')
          $sql .= "AND a.Resource_Location LIKE '%" . $_POST['location'] . "%'";            
          //$sql .= " ORDER BY a.Resource_Id  DESC";
      endif;

      $sql .= " GROUP BY a.Name ORDER BY a.Resource_Id  DESC";

      $command = $connection->createCommand($sql);
      $dataReader = $command->query();
      $dataProvider = $dataReader->readAll();

      if(isset($_POST['resourcetype'])):
        if ($_POST['resourcetype'] != 'none'):
           $restypenames=Resourcetype::findOne($_POST['resourcetype']);
           $restypename=":".$restypenames['Name'];
        endif;
      else:
        $restypename='';
      endif;
      if(isset($_POST['resgroup'])):
        if ($_POST['resgroup'] != 'none'):
            $resgroupname=":".ResourceGroup::find($_POST['resgroup'])->one()->Resource_group_Name;
        endif;
      else:
          $resgroupname='';
      endif;

      $datarows = '';
      $resource = '';
      $arr = array();

      $vendorlist=Vendors::find()->Where(['Status'=>0])->all();    

      if (count($dataProvider) > 0):
          $resource_Name = '';
          $res_Namedrop = '';
          $resource_count = 0;

          foreach ($dataProvider AS $key => $data):
              if($data['Quantity']==0):
                  $resourcename=$data['Name'];
              else:
                  $resourcename=$data['Name']." (".$data['Quantity'].")";
              endif;
              $typesval = '';
              //$vendorval = '';
              $projectval = '';
              $accval = '';

              if($data['Updated_On']!='0000-00-00 00:00:00'):
                  $date=date('M, j ,  Y',strtotime($data['Updated_On']));    
              else:
                  $date='';
              endif;

              $childdatarows = '';
              $reschild='';

              $string1 = str_replace(' ', '', $resource_Name);
              $string2 = str_replace(' ', '', $data['Name']);
              $casesenstve = strnatcasecmp($string1,$string2);
              if($casesenstve!=0){

                $resource_Name = $data['Name'];

                $resource_count++;

                $ven_count = 0;

                $resource.='
                          <div class="panel panel-default">
                          
                            <div class="panel-heading">
                              <h4 class="panel-title" style="font-size: 15px;">
                             
                              <a data-toggle="collapse" id="resourcetabname'.$data['Resource_Id'].'" data-parent="#accordion" href="#collapse'.$resource_count.'">'.($key+1).'.&nbsp;&nbsp;'. $resource_Name . '</a></h4>
                              <input class="form-control editresourcername" style="display:none;width: 300px;"   maxlength="30"  type="text" id="editresourcername' . $data['Resource_Id'] . '" value="' . $resourcename . '">
                              <div class="iconss icon-groups">
                              
                              <button type="button" class="saveresorbutton" style="display:none;background: none;" value="' . $data['Resource_Id'] . '" id="saveresorbutton' . $data['Resource_Id'] . '" data-vendor="'.$data['Vendor_Id'].'" data-mode="old"><a title="Update Resource" href="#" class="icon-save"></a>
                                </button>

                                <button type="button" class="editresourcebuttons" style="display: inline-block;"  value="' . $data['Resource_Id'] . '" id="editresourcebuttons' . $data['Resource_Id'] . '" data-vendor="'.$data['Vendor_Id'].'" data-mode="old" title="Edit Vendor"> <a href="#" class="icon-pencil editVendor-pencil" title="Edit Resource"></a></button>

                                <button type="button" class="deletresourcebutton" value="' . $data['Resource_Id'] . '" id="delet_resourcebutton' . $data['Resource_Id'] . '" data-mode="master" title="Delete Resource">
                                  <a title="Delete Resource" href="#" class="icon-trash1"></a>
                                </button> 
                              
                              </div>
                            </div>
                          ';

                $resource.='
                    <div id="collapse'.$resource_count.'" class="vv panel-collapse collapse">
                      <div class="panel-body">
        
                        <table class="table reshds">
                          <tr style="background: #f5f5f5;font-size:15px;" id="resourcerow' . $data['Resource_Id'] . '" class="vendor-column reshead resource' . $data['Resource_Id'] . '" data-id="' . $data['Resource_Id'] . '">
                            <td><input type="hidden" value="' . $data['Resource_Id'] . '"><label>#</label></td>
                            <input type="hidden" value="' . $data['ResourceType_Id'] . '" id="resourcetypes' . $data['Resource_Id'] . '">
                            <td><label>Vendor</label>
                              <span>
                                <p>
                                  
                                  
                                  <input type="hidden" id="resourcername_old' . $data['Resource_Id'] . '" value="' . $resourcename . '">
                                  
                                </p>
                              </span>
                            </td>

                            <td><label>Location</label></td>
                            <td><label>Brand</label></td>
                            <td><label>Unit</label></td>
                            <td><label>Rate</label></td>
                            <td><label>Last Updated</label></td>
                            <td colspan="3">
                              <div class="icon-groups">
                                

                                <button type="button" class="addvendor" value="' . $data['Resource_Id'] . '" id="addvendor' . $data['Resource_Id'] . '" title="Add Vendor">
                                          <a  href="#" class="icon-add add-vendor-at-resource"></a>
                                </button>                          
                                                      
                              </div>
                            </td>
                          </tr>
                        ';  

              }

              //$childdata = $data;

              $resnamefinal = rtrim($resource_Name," ");

              if ($_POST['vendor'] != 'none')
              {
                $sql = 'SELECT * FROM resources WHERE Name LIKE "'.$resnamefinal.'" AND Vendor_Id='.$_POST['vendor'].' AND Status=0 AND pricing_status=0 ';
              }
              else{
                $sql = 'SELECT * FROM resources WHERE Name LIKE "'.$resnamefinal.'" AND Vendor_Id!=0 AND Status=0 AND pricing_status=0 ';
              }

              $command = $connection->createCommand($sql);
              $dataReader = $command->query();
              $vendortot = $dataReader->readAll();

              $vendorcount = count($vendortot);
              $vcount=0;
              if($vendorcount>0){

                foreach ($vendortot AS $key1 => $childdata):
                  $vcount++;
                  $vendorval = '';
                  foreach ($vendorlist AS $type):
                    if($type->Vendor_Id==$childdata['Vendor_Id']){
                      $vendorval .= "<option value='" . $type->Vendor_Id . "' selected>" . $type->Name . " " . $type->Brand . "</option>";
                    }
                    else{
                      $vendorval .= "<option value='" . $type->Vendor_Id . "'>" . $type->Name . " " . $type->Brand . "</option>";
                    }
                    
                  endforeach;
                  if($childdata['Vendor_Id']!=0):
                      $ven_count++;
                      if($childdata['Updated_On']!='0000-00-00 00:00:00'):
                          $date=date('M, j ,  Y',strtotime($childdata['Updated_On']));    
                      else:
                          $date='';
                      endif;
                      if(isset($_POST['brand']) && $_POST['brand']!='none'){

                          
                          $vendorbrandsqls='SELECT * FROM vendor_brand WHERE Vendor_Id='. $childdata['Vendor_Id'].' AND resource_id='.$childdata['Resource_Id'].' AND brand_id='.$_POST['brand'].' ';  
                          $command = $connection->createCommand($vendorbrandsqls);
                          $dataReader = $command->query();
                          $vendorbrand1 = $dataReader->read();
                          
                          if($vendorbrand1):

                            $vendor=Vendors::findOne($childdata['Vendor_Id']);
                            if($vendor){
                              $vendorname = $vendor->Name;
                              $vendorcity = $vendor['City'];
                            }
                            else{
                              $vendorname = '';
                              $vendorcity = '';
                            }
                              
                            $resource.='
                              <tr id="resvendorrow' . $childdata['Resource_Id'] . '" style="height:55px;">
                                <td colspan="2" style="width:5%;">'.$vcount.'</td>
                                <td style="width:40%;"><span id="resourcevendtext' . $childdata['Resource_Id'] . '">' . $vendorname . '</span>
                                  <select class="form-control" style="display:none" id="editresourcevendor'.$childdata['Resource_Id'].'">
                                  <option value="none">Select Vendor</option><option>'.$vendorval.'</option></select><span class="error"></span>
                                </td>
                                <td style="width:10%;"><span id="resourceloctext' . $childdata['Resource_Id'] . '">' . $vendorcity . '</span>

                                  <input class="form-control" style="display:none" type="text" id="editresourcelocation'.$childdata['Resource_Id'].'" name="resourcelocation" placeholder="Location" value="'.$vendorcity.'">
                                  <span class="error" ></span></td>';                     
                  
                                $vendorbrandsql='SELECT * FROM vendor_brand WHERE Vendor_Id='. $childdata['Vendor_Id'].' AND resource_id='.$childdata['Resource_Id'].' ';  
                                $command = $connection->createCommand($vendorbrandsql);
                                $dataReader = $command->query();
                                $vendorbrand = $dataReader->read();
                                $brand=Brand::find()->where(['id'=>$vendorbrand['brand_id']])->one();

                                $allbrands=Brand::find()->where(['Status'=> 0])->all();

                                $vend_brnd = '';

                                foreach ($allbrands AS $allbrand):
                                  if(!empty($vendorbrand) && $allbrand->id==$vendorbrand['brand_id']){
                                    $vend_brnd .= "<option value='" . $allbrand->id . "' selected>" . $allbrand->name . "</option>";
                                  }
                                  else{
                                    $vend_brnd .= "<option value='" . $allbrand->id . "'>" . $allbrand->name . "</option>";
                                  }
                                  
                                endforeach;

                                $resource.='
                                <td style="width:10%;"><span id="resourcevenbrandtext'.$childdata['Resource_Id'].'">'.$brand['name'].'</span>
                                  <select class="form-control" style="display:none" id="editresourcevenbrand'.$childdata['Resource_Id'].'">
                                  <option value="none">Brand</option>'.$vend_brnd.'</select><span class="error"></span>
                                </td>
                                <td style="width:5%;"><span id="resourceunitstext'.$childdata['Resource_Id'].'">' . $childdata['Unit'] . '</span>

                                  <input class="form-control editresourceunits"    style="display:none;width: 100px;"  type="text" id="editresourceunits' . $childdata['Resource_Id'] . '" value="' . $childdata['Unit'] . '">
                                  <span class="error"></span>


                                </td>
                                <td style="width:10%;"><span id="resourceratetext' . $childdata['Resource_Id'] . '">' .number_format( $childdata['Price'],2) . '</span>
                                          <input class="form-control editresourcerate" style="display:none"  type="text" id="editresourcerate' . $childdata['Resource_Id'] . '" value="' . number_format($childdata['Price'],2) . '">
                                          <span class="error"></span>
                                </td>

                                <td style="width:10%;"><span id="lastupdated' . $childdata['Resource_Id'] . '">'.$date.'</span>

                                  <input class="form-control editresourcelast"    style="display:none;width: 100px;"  type="text" id="editresourcelast' . $childdata['Resource_Id'] . '" value="' . $date . '">
                                  <span class="error"></span>


                                </td>

                                <input type="hidden" value="' . $childdata['ResourceType_Id'] . '" id="resourcetypesnames'. $childdata['Resource_Id'] . '">  
                
                                <td colspan="3" style="width:10%;">
                                  <div class="icon-groups">                 
                                    
                                    <button type="button" class="editvendorsbutton" value="' . $childdata['Resource_Id'] .'" id="editvendorbutton' . $childdata['Resource_Id'] . '" data-vendor="'.$childdata['Vendor_Id'].'" data-mode="old" title="Edit Vendor"><a href="#" class="icon-pencil editVendor-pencil" title="Edit Resource"></a></button>

                                    <button type="button" class="saveresvendorbutton" style="display:none;" value="' . $childdata['Resource_Id'] . '" id="saveresvendorbutton' . $childdata['Resource_Id'] . '" data-vendor="'.$childdata['Vendor_Id'].'" data-mode="old" title="Update Vendor"><a href="#" class="icon-save"></a></button>
                                                          
                            
                                    <button type="button" class="deletresourcebutton" value="' . $childdata['Resource_Id'] . '" id="deletresourcebutton' . $childdata['Resource_Id'] . '" data-mode="old" title="Delete Resource">
                                    <a href="#" class="icon-trash1"></a></button>
                                  </div>
                                </td>
                              </tr>'; 
                              
                          endif;
                          
                      }
                      else
                      {
                        $vendor=Vendors::findOne($childdata['Vendor_Id']);
                        if($vendor){
                          $vendorname = $vendor->Name;
                          $vendorcity = $vendor['City'];
                        }
                        else{
                          $vendorname = '';
                          $vendorcity = '';
                        }

                           $resource.='
                              <tr id="resvendorrow' . $childdata['Resource_Id'] . '" style="height:55px;">
                                <td style="width:5%;">'.$vcount.'</td>
                                <td style="width:40%;"><span id="resourcevendtext' . $childdata['Resource_Id'] . '">' . $vendorname . '</span>
                                <select class="form-control" style="display:none" id="editresourcevendor'.$childdata['Resource_Id'].'">
                                  <option value="none">Select Vendor</option>'.$vendorval.'</select><span class="error"></span>
                                </td>
                                <td style="width:10%;"><span id="resourceloctext' . $childdata['Resource_Id'] . '">' .$vendorcity. '</span>
                                <input class="form-control" style="display:none" type="text" id="editresourcelocation'.$childdata['Resource_Id'].'" name="resourcelocation" placeholder="Location" value="'.$vendorcity .'">
                                </td>';                    
              
                                $vendorbrandsql='SELECT * FROM vendor_brand WHERE Vendor_Id='. $childdata['Vendor_Id'].' AND resource_id='.$childdata['Resource_Id'].' ';  
                                $command = $connection->createCommand($vendorbrandsql);
                                $dataReader = $command->query();
                                $vendorbrand = $dataReader->read();
                                if(!empty($vendorbrand)){
                                  $brand=Brand::find()->where(['id'=>$vendorbrand['brand_id']])->one();
                                  $bname =$brand['name'];
                                }else{
                                  $bname='';
                                }

                                $allbrands=Brand::find()->where(['Status'=> 0])->all();

                                $vend_brnd = '';

                                foreach ($allbrands AS $allbrand):
                                  if(!empty($vendorbrand) && $allbrand->id==$vendorbrand['brand_id']){
                                    $vend_brnd .= "<option value='" . $allbrand->id . "' selected>" . $allbrand->name . "</option>";
                                  }
                                  else{
                                    $vend_brnd .= "<option value='" . $allbrand->id . "'>" . $allbrand->name . "</option>";
                                  }
                                  
                                endforeach;

                                $resource.='
                                <td style="width:10%;"><span id="resourcevenbrandtext'.$childdata['Resource_Id'].'">'.$bname.'</span>
                                  <select class="form-control" style="display:none" id="editresourcevenbrand'.$childdata['Resource_Id'].'">
                                  <option value="none">Brand</option>'.$vend_brnd.'</select><span class="error"></span>
                                </td>
                                <td style="width:5%;"><span id="resourceunitstext'.$childdata['Resource_Id'].'">' . $childdata['Unit'] . '</span>

                                <input class="form-control editresourceunits"    style="display:none;width: 100px;"  type="text" id="editresourceunits' . $childdata['Resource_Id'] . '" value="' . $childdata['Unit'] . '">
                                  <span class="error"></span>


                                </td>
                                <td style="width:10%;"><span id="resourceratetext' . $childdata['Resource_Id'] . '">' .number_format( $childdata['Price'],2) . '</span>
                                  <input class="form-control editresourcerate"    style="display:none;width: 100px;"  type="text" id="editresourcerate' . $childdata['Resource_Id'] . '" value="' . number_format($childdata['Price'],2) . '">
                                  <span class="error"></span>

                                </td>   

                                <td style="width:10%;"><span id="lastupdated' . $childdata['Resource_Id'] . '">'.$date.'</span>

                                  <input class="form-control editresourcelast"    style="display:none;width: 100px;"  type="text" id="editresourcelast' . $childdata['Resource_Id'] . '" value="' . $date . '">
                                  <span class="error"></span>


                                </td>      
                                  <input type="hidden" value="' . $childdata['ResourceType_Id'] . '" id="resourcetypesnames'. $childdata['Resource_Id'] . '">         
                              
                                <td colspan="3" style="width:10%;">
                                    <div class="icon-groups">
                                        
                                        <button type="button" class="editvendorsbutton" value="' . $childdata['Resource_Id'] .'" id="editvendorbutton' . $childdata['Resource_Id'] . '" data-vendor="'.$childdata['Vendor_Id'].'" data-mode="old" title="Edit Vendor"><a href="#" class="icon-pencil editVendor-pencil" title="Edit Resource"></a></button>

                                        <button type="button" class="saveresvendorbutton" style="display:none;" value="' . $childdata['Resource_Id'] . '" id="saveresvendorbutton' . $childdata['Resource_Id'] . '" data-vendor="'.$childdata['Vendor_Id'].'" data-mode="old" title="Update Vendor"><a href="#" class="icon-save"></a></button>
                                                      
                                        <button type="button" class="deletresourcebutton" value="' . $childdata['Resource_Id'] . '" id="deletresourcebutton' . $childdata['Resource_Id'] . '" data-mode="old" title="Delete Resource">
                                        <a href="#" class="icon-trash1"></a></button>
                                    </div>
                                </td>
                              </tr>';     
                
                      }
             
                  else:
                       $resource.=''; 
                  endif;
                endforeach;  
              }

              if($vendorcount==$ven_count){ 
                  $resource.='</table>
                        </div>
                      </div></div>';  
              } 

              $res_Namedrop = $data['Name'];                   
          endforeach;

          //$resource.='</div>';

          $arr['result'] = $resource;
          $arr['error'] = 'No';
          $arr['hasdata'] = 'Yes';
          $arr['restypename'] = $restypename; 
          $arr['restypeid'] = $restype_id;
      else:
          $resource = '<tr id="nodata"><td colspan="11" style="text-align: center;padding-left: 32px;">No Resources Found</td></tr>';
          $arr['result'] = $resource;
          $arr['error'] = 'No';
          $arr['hasdata'] = 'No';
          $arr['restypename'] = $restypename;
          $arr['restypeid'] = $restype_id;
      endif;
      return json_encode($arr); 
  }

  public function actionUpdateresvendor()
  {
    $vendorname='';
    $brandname='';
      if($_POST['mode']=='old'):

          $model = $this->loadModel($_POST['resourceid']);
          $model->Price = $_POST['resourcerate'];
          $model->Unit = $_POST['units'];
          //$model->Updated_On =$_POST['lstupdated'];
          $model->Updated_On = date('y-m-d h:i:s');
          if(isset($_POST['resourcevendor']) && $_POST['resourcevendor']!='none'):
              $model->Vendor_Id = $_POST['resourcevendor'];
          else:
            $model->Vendor_Id =  $model->Vendor_Id;
              
          endif;

          $connection = \Yii::$app->db; 
          if($_POST['brand']!='none'){

            $vendorbrandsqls='SELECT * FROM vendor_brand WHERE Vendor_Id='. $_POST['resourcevendor'].' AND resource_id='.$_POST['resourceid'].' ';  
            $command = $connection->createCommand($vendorbrandsqls);
            $dataReader = $command->query();
            $vendorbrand = $dataReader->read();

            if(!$vendorbrand){

              $sql="INSERT INTO vendor_brand (id,Vendor_Id,brand_id,resource_id,status) VALUES ('','". $_POST['resourcevendor']."','".$_POST['brand']."','".$_POST['resourceid']."',0)";
              $command=$connection->createCommand($sql);
              $dataReader=$command->query();

            }
            else{

              $whitespace = 'UPDATE vendor_brand SET brand_id = '.$_POST['brand'].' WHERE Vendor_Id = '.$_POST['resourcevendor'].' AND resource_id='.$_POST['resourceid'].' ';
              $command = $connection->createCommand($whitespace);
              $dataReader = $command->query();

            }

            $brand=Brand::findOne($_POST['brand']); 

            $brandname=$brand->name;
          }

          if(isset($_POST['resourcevendor']) && $_POST['resourcevendor']!='none'):
              $vendor=Vendors::find()->where(['Vendor_Id'=>$_POST['resourcevendor']])->one(); 
              $vendor->City = $_POST['location'];
              $vendor->save(false);
              $vendorname = $vendor->Name;

          else:
              $vendorname=Vendors::find()->where(['Vendor_Id'=>$model->Vendor_Id])->one()->Name; 
              
          endif;

          if(isset($_POST['location'])):
              $model->Resource_Location = $_POST['location'];
          endif;
          $model->save(false);
      else:
          $model=ResourceVendors::findOne($_POST['resourcevendor']); 
          $model->res_rate = $_POST['resourcerate'];
          $model->vendor_id = $_POST['resourcevendor'];
          $vendorname=Vendors::find()->where(['Vendor_Id'=>$_POST['resourcevendor']])->one()->Name;
          $model->res_location = $_POST['location'];
          $model->save(false);
      endif;
      $date=date('M, j ,  Y',strtotime($model['Updated_On']));
      $arr = array('Id' => $_POST['resourceid'],'Location'=>$_POST['location'],'Vendor'=>$vendorname,'Units' => $_POST['units'] , 'Price' => $_POST['resourcerate'], 'Lastupdated' => $date, 'brandname' => $brandname, 'error' => 'No');
      return json_encode($arr);

  }

 /*  public function actionDeleteresource()
  {
    if(isset($_POST['resourceid'])):
        $type=$_POST['mode'];
        if($type=='old'):
            $model=$this->loadModel($_POST['resourceid']);
            $model->pricing_status=1;
            $model->save(false);
            $arr = array('Id' => $_POST['resourceid'],'error'=>'No');
            return json_encode($arr);
        else:

            $pricingest= PricingEstimateResourcesNew::find()->where(['resource_Id' => $_POST['resourceid']])->andWhere(['pricing_status' => 0])->one();

            if($pricingest):

                 $actname=WorkgroupActivitiesNew::find($pricingest['activity_id'])->one()->activity_Name; 

                $projname1=Projects::find($pricingest['project_id'])->one(); 
                if($projname1){
                  $projname=$projname1->Name;
                }else{
                  $projname='';
                }
                $arr = array('Id' => $_POST['resourceid'],'estimateid'=>$pricingest['pricing_resourceid'],'error'=>'Yes','errortext'=>'Cannot Delete Resource as it is linked in under the Activity: '.$actname.' in the Project: '.$projname.' estimate');
                return json_encode($arr);
            else:
                $model=$this->loadModel($_POST['resourceid']);
                $model->pricing_status=1;
                $model->save(false);
                 
                ProjectSetupResources::deleteAll(['PSR_Resource_Id' => $_POST['resourceid']]);

                ProductResources::deleteAll(['Resource_Id' => $_POST['resourceid']]);

                LogisticsResources::deleteAll(['Resource_Id' => $_POST['resourceid']]);

                ConstructionResources::deleteAll(['COR_Resource_Id' => $_POST['resourceid']]);

                OverheadsResources::deleteAll(['Resource_Id' => $_POST['resourceid']]);

                $arr = array('Id' => $_POST['resourceid'],'error'=>'No');
                return json_encode($arr);
            endif;
        endif;
    endif;
  } */

  public function actionDeleteresource()
  {
    if(isset($_POST['resourceid'])):
        $type=$_POST['mode'];
        if($type=='old'):
            $model=$this->loadModel($_POST['resourceid']);
            $model->pricing_status=1;
            $model->save(false);

            $modelbis = Bsitems::find()->where(['res_id'=>$_POST['resourceid']])->one();
            if($modelbis)
            { 
              $modelbis->status = 1; //deleted
              $modelbis->save(false);
            }
            $arr = array('Id' => $_POST['resourceid'],'error'=>'No');
            return json_encode($arr);
        else:

            //$pricingest= PricingEstimateResourcesNew::find()->where(['resource_Id' => $_POST['resourceid']])->andWhere(['pricing_status' => 0])->one();
            $connection = Yii::$app->db;
            $sql="SELECT * FROM `pricing_estimate_resources_new` WHERE `resource_Id` = ".$_POST['resourceid']." AND `pricing_status` = 0";
            $command = $connection->createCommand($sql);
            $dataReader = $command->query();
            $pricingest = $dataReader->read();

            $Resourceone = Resources::findOne($_POST['resourceid']);


            $sql="(SELECT id,voucher_no FROM voucher WHERE (account_id='".$Resourceone->Resource_Acc_Id ."' OR creditacnt='".$Resourceone->Resource_Acc_Id ."') )
            UNION
            (SELECT id,voucher_no FROM journalvoucher WHERE (debitacnt='".$Resourceone->Resource_Acc_Id ."' OR creditacnt='".$Resourceone->Resource_Acc_Id ."') )";
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $dataProvider=$dataReader->readAll();


            if($pricingest){

                //$actname=WorkgroupActivitiesNew::find($pricingest['activity_id'])->one()->activity_Name;
                $sqlact="SELECT * FROM `workgroup_activities_new` WHERE `id` = ".$pricingest['activity_id']."";
                $command = $connection->createCommand($sqlact);
                $dataReader = $command->query();
                $activity = $dataReader->read();
                $actname=$activity['activity_Name'];

                $sqlpro="SELECT * FROM `projects` WHERE `Project_Id` = ".$pricingest['project_id']."";
               // $projname1=Projects::find($pricingest['project_id'])->one(); 
               $command = $connection->createCommand($sqlpro);
               $dataReader = $command->query();
               $projname1 = $dataReader->read();
                if($projname1){
                  $projname=$projname1['Name'];
                }else{
                  $projname='';
                }
                $arr = array('project'=>$pricingest['project_id'], 'Id' => $_POST['resourceid'],'estimateid'=>$pricingest['pricing_resourceid'],'error'=>'Yes','errortext'=>'Cannot Delete Resource as it is linked in under the Activity: '.$actname.' in the Project: '.$projname.' estimate');
                return json_encode($arr);
              }elseif(!empty($dataProvider)){

                $arr = array('Id' => $_POST['resourceid'],'error'=>'Yes','errortext'=>'Can not Delete the resource contains accounthead has transaction');
                return json_encode($arr);
                
              }else{
                $model=$this->loadModel($_POST['resourceid']);
                $model->pricing_status=1;
                $model->save(false);

                $modelbis = Bsitems::find()->where(['res_id'=>$_POST['resourceid']])->one();
                if($modelbis)
                { 
                  $modelbis->status = 1; //deleted
                  $modelbis->save(false);
                }
                 
                ProjectSetupResources::deleteAll(['PSR_Resource_Id' => $_POST['resourceid']]);

                ProductResources::deleteAll(['Resource_Id' => $_POST['resourceid']]);

                LogisticsResources::deleteAll(['Resource_Id' => $_POST['resourceid']]);

                ConstructionResources::deleteAll(['COR_Resource_Id' => $_POST['resourceid']]);

                OverheadsResources::deleteAll(['Resource_Id' => $_POST['resourceid']]);

                $arr = array('Id' => $_POST['resourceid'],'error'=>'No');
                return json_encode($arr);
            }
        endif;
    endif;
  }

  /**
   * Returns the data model based on the primary key given in the GET variable.
   * If the data model is not found, an HTTP exception will be raised.
   * @param integer $id the ID of the model to be loaded
   * @return Resources the loaded model
   * @throws CHttpException
   */
  public function loadModel($id)
  {
     // $model=Resources::model()->findByPk($id);
      $model=Resources::findOne($id);
      if($model===null)
          throw new CHttpException(404,'The requested page does not exist.');
      return $model;
  }

  /**
   * Performs the AJAX validation.
   * @param Resources $model the model to be validated
   */
  protected function performAjaxValidation($model)
  {
      if(isset($_POST['ajax']) && $_POST['ajax']==='resources-form')
      {
          echo CActiveForm::validate($model);
          Yii::app()->end();
      }
  }

  public function actionCheckname()
  {
      
      $data['total']='';
      $connection = \Yii::$app->db; 
      if(isset($_POST['name'])):
        $sql="SELECT count(*) AS total FROM resources  WHERE Name='".$_POST['name']."'";
        
        if(isset($_POST['type'])):
          if($_POST['type']!='none'):
            $sql.=" AND ResourceType_Id='".$_POST['type']."'";
          endif;
        endif;
        if(isset($_POST['vendor'])):
          if($_POST['vendor']!='none'):
              $sql.="AND Vendor_Id='".$_POST['vendor']."'";
          endif;
        endif;

        $sql.="ORDER BY Resource_Id ASC,Name ASC";
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $data=$dataReader->read();
      endif;
      if($data['total']>0):
          echo 'Yes';
      else:
          echo 'No';
      endif;
  }

  public function actionEditresource()
  {
      $connection = \Yii::$app->db; 
      $sql = "SELECT * FROM resources WHERE Resource_Id='" . $_POST['id'] . "'";
      $command = $connection->createCommand($sql);
      $dataReader = $command->query();
      $data = $dataReader->read();

      $res_id = $_POST['id'];
      $sch_item = 0;
      $bsitem = Bsitems::find()->where(['res_id'=>$res_id])->one(); 
      if($bsitem)
      { 
        $sch_item = 1;
        /*if($bsitem->item_id!='')
        {
          $sch_item = 1;
        }*/
        
      }else{
        $sch_item = 0;
      }
      
      $schedulehead = '';
      if($data['ResourceType_Id']==26){
        $ratehead = 'Depreciation<span style="color:red;">*</span>';
        $unithead = 'Unit<span style="color:red;">*</span>';
        $schedulehead = 'Scheduleitem';
        //$ratehide = 'display:none;';
      }
      elseif($data['ResourceType_Id']==24){
        $ratehead = 'Lease Rate';
        $unithead = 'Lease Unit';
        //$ratehide = '';
      }else{
        $ratehead = 'Rate';
        $unithead = 'Unit';
      }

      $datarows = '';

      $list = Resourcetype::find()->where(['Status'=>0])->all();

      $sql12= "SELECT * FROM vendor_brand WHERE resource_id='".$data['Resource_Id']."' ";
      $command=$connection->createCommand($sql12);
      $dataReader=$command->query();
      $vend=$dataReader->read();


      $sql12= "SELECT * FROM brand WHERE id ='".$vend['brand_id']."' ";
      $command=$connection->createCommand($sql12);
      $dataReader=$command->query();
      $vendbrnad=$dataReader->read();


      $sql12= "SELECT * FROM vendors WHERE Vendor_Id ='".$vend['Vendor_Id']."' ";
      $command=$connection->createCommand($sql12);
      $dataReader=$command->query();
      $vendname=$dataReader->read();





      if($data):

        $datarows.= '
            <form id="editresourceform">
            <input type="hidden" name="hidschedule" id="hidschedule" value="'.$sch_item.'" >
              <div class="col-md-1"></div>
              <div class="col-md-10">
                <div class="row">
                  <div class="col-md-3" style="width: 180px;">
                    <div class="form-title" style="display:block; margin-top:12px; font-size:17px; margin-bottom:0px; margin-top:10px; text-align:left;">Resource Type: 

                    </div> 
                  </div>
                  <div class="col-md-4" style="display:block; margin-top:11px; font-size:17px; margin-bottom:0px; margin-top:9px; text-align:left;padding-right: 0px;width: 250px;">
                    <select class="form-control" id="editrestyperes" name="resourcetype"  tabindex="1">
                      <option value="none">Select Resource Type</option>';

        foreach ($list AS $type):
            if ($data['ResourceType_Id'] == $type->ResourceType_Id):
                $seletedtype = 'Selected="selected"';
            else:
                $seletedtype = '';
            endif;
            $datarows.= "
                  <option value='" . $type->ResourceType_Id . "' " . $seletedtype . ">" . $type->Name . "</option>";
        endforeach;

        $datarows.= '
                    </select>
                  </div>';

        $datarows.= '
                </div>
              </div>
              <div class="col-md-1"></div>
              <div class="col-md-12">
                &nbsp;
              </div>';

        $datarows.= '
              <div class="col-md-1"></div>
              <div class="col-md-10">
                <div class="col-md-4">                    
                  <div class="form-group">
                    <label>Resource Name<span style="color:red;"> *</span></label>
                    <input class="form-control" type="text" id="editresourcename" name="resourcename" placeholder="Resource Name" tabindex="2" value="'.$data['Name'].'">
                    <span class="error" style="display: none;"></span>
                  </div>
                </div>';

                $datarows.= '
                <div class="col-md-4">                    
                  <div class="form-group">
                        <label>Brand<span style="color:red;"> *</span></label>
                      <input class="form-control" list="editbrandlist" type="text" id="editresourcebrnd" name="editresourcebrand" tabindex="4" value="'.$vendbrnad['name'].'">

                      <datalist id="editbrandlist">';
                          
                         
                          $brands=Brand::find()->Where(['Status'=>0])->all();
                          foreach($brands AS $brand):
                            if($brand->name == $vendbrnad['name'])
                            {
                              $selectedb = "selected";
                            }else{
                              $selectedb ="";
                            }
                            $datarows.= '<option selected="'.$selectedb.'" value="'.$brand->name.'"></option>';
                          endforeach;
                         
                      $datarows.= '</datalist>
                    <span class="error" style="display: none;"></span>
                  </div>            
                </div>';

        $datarows.= '
                <div class="col-md-2">                    
                  <div class="form-group">
                        <label>'.$unithead.'</label>
                      <input class="form-control" type="text" id="editresourceunit" name="resourceunit" placeholder="Unit" tabindex="4" value="'.$data['Unit'].'">
                    <span class="error" style="display: none;"></span>
                  </div>            
                </div>';

        $datarows.= '
                <div class="col-md-2 edtplant">                    
                    <div class="form-group">
                        <label id="editratehd">'.$ratehead.'</label>
                      <input class="form-control"  type="text" id="editresourcerate" name="resourcerate" placeholder="Rate" tabindex="5" value="'.number_format($data['Price'],2).'" >
                      <span class="error" style="display: none;"></span>                                         
                    </div>            
                </div>

                <div class="col-md-1 edtplant">   
                    <div class="row">
                      &nbsp;
                    </div>
                </div>

                <div class="col-md-1 scheduleeditdiv" id="schdshw" style="display:none;">  
                    <div class="row hiderow">
                        <label>'.$schedulehead.'</label>';
                        if($sch_item == 1)
                        {
                          $datarows.= '<input type="checkbox" class="sch_box" id="editresourcerate" id="editschedules" value="1" checked>';
                        }
                        else
                        {
                          $datarows.= '<input type="checkbox" class="sch_box" id="editresourcerate"  id="editschedules" value="0" >';
                        }             

                        $datarows.= '
                                    <span class="error" style="display: none;"></span>                                         
                     </div>            
                </div>

                </div>
              </div>
           
              <div class="col-md-12">
                &nbsp;
              </div>';

              $datarows.= '
              <div class="col-md-1"></div>
              <div class="col-md-10">
                <div class="col-md-4">                    
                  <div class="form-group">
                    <label>Vendor<span style="color:red;"> *</span></label>
                    <input class="form-control" list="editvendorlist" type="text" id="editVendor" name="editVendorname" placeholder="Vendor Name" tabindex="2" value="'.$vendname['Name'].'">
                    <datalist id="editvendorlist">';
                  

                     $vendors=Vendors::find()->Where(['Status'=>0])->all();
                    foreach($vendors AS $vendor):

                      if($vendor->Name == $vendname['Name'])
                      {
                        $selectedvn = "selected";
                      }else{
                        $selectedvn ="";
                      }
                       $datarows.= '<option selected="'.$selectedb.'" value="'.$vendor->Name.'"></option>';
                    endforeach;
                   
     $datarows.= ' </datalist>
              <span class="error" style="display: none;"></span>
                  </div>
                </div>';

                $datarows.= '
                <div class="col-md-4">                    
                  <div class="form-group">
                        <label>Vendor Location<span style="color:red;"> *</span></label>
                      <input class="form-control" type="text" id="editresourceloc" name="editresourceloc" tabindex="4" value="'.$vendname['City'].'">
                    <span class="error" style="display: none;"></span>
                  </div>            
                </div>';

        $datarows.= '
                <div class="col-md-4">                    
                  <div class="form-group">
                        <label>Phone</label>
                      <input class="form-control" type="text" id="editresourcephne" name="resourceunieditresourcephnet" placeholder="Phone" tabindex="4" value="'.$vendname['Phone'].'">
                    <span class="error" style="display: none;"></span>
                  </div>            
                </div></div><div class="col-md-1"></div>
                <div class="col-md-12">
                  &nbsp;
                </div>';

        $accountheads=AccountsItem::find()->where(['Status'=>0])->andWhere(['account_type' => 8])->orderBy(['name' => SORT_ASC])->all();

        $accntrows = '';

        foreach($accountheads AS $accounthead):

          $accntrows.= '<option value="'.$accounthead->name.'"></option>';

        endforeach;

        $selectedaccnthead=AccountsItem::findOne($data['Resource_Acc_Id']);

        $selectedaccntheadname = '';

        if($selectedaccnthead){
          $selectedaccntheadname = $selectedaccnthead->name;
        }

        $datarows.= '<div class="col-md-1"></div>
        <div class="col-md-10">
                <div class="col-md-4">                    
                  <div class="form-group">
                        <label>Email</label>
                      <input class="form-control" type="text" id="editresourcemail" name="resourceunieditresourceemail" placeholder="Email" tabindex="4" value="'.$vendname['Email'].'">
                    <span class="error" style="display: none;"></span>
                  </div>            
                </div>';

        $datarows.= '
              
                <div class="col-md-4">                    
                  <div class="form-group">
                    <label>Account Head<span style="color:red;"> *</span></label>

                      <input class="form-control editaccounthead" list="accountheadlist" id="editaccounthead" name="accounthead-choice" value="'.$selectedaccntheadname.'" placeholder="Account Head" tabindex="10"/>

                      <datalist id="accountheadlist">
                          '.$accntrows.'
                      </datalist>

                      <span class="error" style="display: none;"></span>
                  </div>
                </div>';

        $accountsubs=AccountsSub::find()->where(['Status'=>0])->andWhere(['master_id'=>5])->all();

        $subgroupaccount=SubgroupAccounts::find()->where(['account_id'=>$data['Resource_Acc_Id']])->andWhere(['group_id'=>5])->one();

        $selectedaccntsubgrpname = '';

        if($subgroupaccount){
          $selectedaccntsubgrp=AccountsSub::findOne($subgroupaccount->subgrp_id);
          $selectedaccntsubgrpname = $selectedaccntsubgrp->name;
        }

        $accntsubrows = '';

        foreach($accountsubs AS $accountsub):
            $accntsubrows.= '<option value="'.$accountsub->name.'"></option>';
        endforeach;

        $datarows.= '
                <div class="col-md-4">                    
                  <div class="form-group">
                    <label>Account Sub Group<span style="color:red;"> *</span></label>
                        <input class="form-control" list="accountsubgrplist" id="editaccountsubgrp" name="accountsubgrp-choice" value="'.$selectedaccntsubgrpname.'" placeholder="Select Account Subgroup" tabindex="11"/>

                      <datalist id="accountsubgrplist">
                          '.$accntsubrows.'
                      </datalist>

                      <span class="error" style="display: none;"></span>
                  </div>
                </div></div><div class="col-md-1"></div>
                <div class="col-md-12">
                  &nbsp;
                </div>';

        if ($data['Resource_group_Id']==102):
            $selected1= 'Selected="selected"';
            $selected2 = '';
        elseif ($data['Resource_group_Id']==154):
            $selected2= 'Selected="selected"';
            $selected1 = '';
        else :
            $selected1 = '';
            $selected2 = '';
        endif;

        if($data['ResourceType_Id']==26 || $data['ResourceType_Id']==24){

          $reshrpshow = '';

        }
        else{
          $reshrpshow = 'style="display:none;"';
        }

        if($data['Resource_group_Id']==102 || $data['Resource_group_Id']==154){

          $reshrpshow1 = '';

        }
        else{
          $reshrpshow1 = 'style="display:none;"';
        }

        $datarows.= '<div class="col-md-1"></div>
        <div class="col-md-10">
                <div class="col-md-4" id="editrsgrpshow" '.$reshrpshow.'>                    
                  <div class="form-group">
                      <label>Equipment Type</label>
                      <select id="editresgroup" name="resgroup" class="form-control">
                        <option value="none">Select Equipment Type</option>
                        <option value="102" '.$selected1.'>Engine Driven Equipments </option>
                        <option value="154" '.$selected2.'>Motor Driven Equipments</option>
                      </select>
                  </div>
                </div>
              
              ';

/* 
        $datarows.= '<div class="col-md-1"></div>
                <div class="col-md-10">';
 */
      if ($data['Resource_group_Id']==102){

        $datarows.= '
                

                  <div id="editfueleqt" class="col-md-2 equipmentdetails" '.$reshrpshow1.'> 

                    <div class="form-group">
                        <label id="editconsumptionlabel">Diesel (L/H)</label>
                        <input class="form-control" type="text" id="editequconsumption" name="editequconsumption" value="'.$data['consumption'].'" tabindex="4">
                        <span class="error" style="display: none;"></span>
                    </div>

                  </div>

                  

                  <div class="col-md-2 equipmentdetails" '.$reshrpshow1.'>

                      <div class="form-group">
                          <label id="editratelabel">Diesel Rate</label>
                          <input class="form-control" type="text" id="editequrate" name="editequrate" value="'.$data['rate'].'" tabindex="5">
                          <span class="error" style="display: none;"></span>                                         
                      </div>

                  </div>';
      }elseif($data['Resource_group_Id']==154)
      {
        $datarows.= '
                

                  <div id="editfueleqt" class="col-md-2 equipmentdetails" '.$reshrpshow1.'> 

                    <div class="form-group">
                        <label id="editconsumptionlabel">Power (KWh)</label>
                        <input class="form-control" type="text" id="editequconsumption" name="editequconsumption" value="'.$data['consumption'].'" tabindex="4">
                        <span class="error" style="display: none;"></span>
                    </div>

                  </div>

                 

                  <div class="col-md-2 equipmentdetails" '.$reshrpshow1.'>

                      <div class="form-group">
                          <label id="editratelabel">Power Rate</label>
                          <input class="form-control" type="text" id="editequrate" name="editequrate" value="'.$data['rate'].'" tabindex="5">
                          <span class="error" style="display: none;"></span>                                         
                      </div>

                  </div>';

        
      }else{

        $datarows.= '
                

        <div class="col-md-2 equipmentdetails" '.$reshrpshow1.'> 

          <div class="form-group">
              <label id="editconsumptionlabel">Diesel (L/H)</label>
              <input class="form-control" type="text" id="editequconsumption" name="editequconsumption" value="'.$data['consumption'].'" tabindex="4">
              <span class="error" style="display: none;"></span>
          </div>

        </div>

        <div class="col-md-2 equipmentdetails" '.$reshrpshow1.'>

            <div class="form-group">
                <label id="editratelabel">Diesel Rate</label>
                <input class="form-control" type="text" id="editequrate" name="editequrate" value="'.$data['rate'].'" tabindex="5">
                <span class="error" style="display: none;"></span>                                         
            </div>

        </div>';

      }
         $datarows.= '<div class="col-md-2 equipmentdetails" '.$reshrpshow1.'>

                      <div class="form-group">
                          <label>Maint Cost/Hr</label>
                          <input class="form-control" type="text" id="editequmaintenancecost" name="editequmaintenancecost" value="'.$data['maintenance'].'" tabindex="4">
                          <span class="error" style="display: none;"></span>
                      </div>

                  </div>

                  <div class="col-md-2 equipmentdetails" '.$reshrpshow1.'>

                      <div class="form-group">
                          <label>Machine Rate</label>
                          <input class="form-control"  type="text" id="editmachinerate" name="editmachinerate" value="'.$data['machinecost'].'" tabindex="5" readonly>
                          <span class="error" style="display: none;"></span>                                         
                      </div>

                  </div>

                </div>
               ';

        $datarows.= '
          <div class="col-md-12 text-center">
            <div class="form-group text-center" style="position:relative; top:6px;">
              <span>&nbsp;</span><br/>
              <button type="button" class="btn btn-danger cancel" id="canceleditres"><span class="icon-close"></span> Cancel</button>
              
              <button type="button" class="btn btn-primary" value="'.$data['Resource_Id'].'" id="saveresourcebutton" title="Save Resource"><span class="icon-check"></span> Edit Resources</button>
            </div>
            <input type="hidden" name="resourceid" value="'.$_POST['id'].'" />
          </div>';

        $datarows.= '           
            </form>';

      endif;

      $arr=array('error'=>'No','result'=> $datarows);
      return json_encode($arr);

  }

  public function actionResourceupdate()
  {

    $currentresource = Resources::findOne($_POST['resourceid']);
    if(isset($_POST['resourcerate'])):
      $currentresource->Price = $_POST['resourcerate'];
    endif;

    $schedule = '';
    $accountheadid = '';

    if(isset($_POST['editschedules'])):
       $schedule = $_POST['editschedules'];

      $accounthead=AccountsItem::find()->where(['LIKE', 'Name',$_POST['accounthead-choice']])->one();
      if($accounthead)
      {
        $accountheadid = $accounthead->id;
      }
        if($schedule==1)
        {
          $bsitem = new Bsitems();
          $bsitem->acntsubgrp_id = 0;
          $bsitem->accnt_id = $accountheadid;
          $bsitem->res_id = $_POST['resourceid'];  
          $bsitem->itemname = $_POST['resourcename'];
          $bsitem->status = 0;
          $bsitem->save(false);
        }
    endif;  
 
    $currentresource->save(false);

    $vendor=Vendors::find()->Where(['LIKE', 'Name',$_POST['editVendorname']])->one();
     if($vendor)
          {
            //echo "hello";exit;
            
            $vendorid=$vendor->Vendor_Id;
            $vendor->City= $_POST['editresourceloc'];
            $vendor->Phone= $_POST['resourceunieditresourcephnet'];
            $vendor->Email= $_POST['resourceunieditresourceemail'];
            $vendor->save(false);

            
          }
          else
          {
            //echo "hii";exit;
            
            $model2=new Vendors();
            $model2->Name= $_POST['editVendorname'];
            $model2->City= $_POST['editresourceloc'];
            $model2->Phone= $_POST['resourceunieditresourcephnet'];
            $model2->Email= $_POST['resourceunieditresourceemail'];
            $model2->Added_On=date('y-m-d h:i:s');
            $model2->save(false);

            $accounthead_check=AccountsItem::find()->where(['LIKE', 'Name',$_POST['editVendorname']])->one();

            if($accounthead_check){
              $venaccnt=AccountsItem::findOne($accounthead_check->id);
            }
            else{

              $venaccnt=new AccountsItem();
              $venaccnt->name= $_POST['editVendorname'];
              $venaccnt->master_id=6;
              $venaccnt->account_type= 7;
              //$venaccnt->resource_id= $model->Resource_Id;
              $venaccnt->save(false);
              
              $vensbgrp=new SubgroupAccounts();
              $vensbgrp->group_id=6;
              $vensbgrp->subgrp_id=30;
              $vensbgrp->bsitem_id=12;
              $vensbgrp->account_id=$venaccnt->id;

              $vensbgrp->save(false);

            }
                             
            $vendorid = $model2->Vendor_Id;
            $vendordetails=Vendors::findOne($vendorid);
            $vendordetails->account_id=$venaccnt->id;
            $vendordetails->save(false);

          }


          $brand=Brand::find()->Where(['LIKE', 'name',$_POST['editresourcebrand']])->one();

          if($brand)
          {
            $brandid=$brand->id;

          }
          else
          {
            $model3=new Brand();
            $model3->name= $_POST['editresourcebrand'];
            $model3->save(false);
            $brandid = $model3->id;
          }

          $connection = \Yii::$app->db; 
          
          $sql="UPDATE vendor_brand SET Vendor_Id=". $vendorid.", brand_id = ".$brandid." WHERE  resource_id='".$_POST['resourceid']."' ";
          $command=$connection->createCommand($sql);
          $dataReader=$command->query();



          $current_resource = Resources::findOne($_POST['resourceid']); 
          $current_resource->Vendor_Id = $vendorid;
          $current_resource->save(false);

          


    $connection = \Yii::$app->db;
    $sql='SELECT * FROM resources WHERE Name LIKE ' .'"' .$currentresource->Name.'"'. ' AND Status=0 AND pricing_status=0';
    $command=$connection->createCommand($sql);
    $dataReader=$command->query();
    $allresources=$dataReader->readAll();

    foreach($allresources as $allresource):

      $resource = Resources::findOne($allresource['Resource_Id']);

      $resource->Name = $_POST['resourcename'];
      $resource->Unit = $_POST['resourceunit']; 
      //$resource->fuel_unit = $_POST['editequunit'];    
      $resource->ResourceType_Id = $_POST['resourcetype'];
      if($_POST['resgroup']!='none'){
          $resource->Resource_group_Id = $_POST['resgroup'];

          $resource->consumption = $_POST['editequconsumption'];
          $resource->rate = $_POST['editequrate'];
          $resource->maintenance = $_POST['editequmaintenancecost'];
          $resource->machinecost = $_POST['editmachinerate'];
          $resource->Price = $_POST['resourcerate'];
      }
      else{
          $resource->Resource_group_Id = 0;
          $resource->consumption = NULL;
          $resource->rate = NULL;
          $resource->maintenance = NULL;
          $resource->machinecost = NULL;
      }

      $accounthead=AccountsItem::find()->where(['LIKE', 'Name',$_POST['accounthead-choice']])->one();

      if($accounthead)
      {
          $accountheadid = $accounthead->id;

          $accountsubid=AccountsSub::find()->where(['LIKE', 'name',$_POST['accountsubgrp-choice']])->one();
          if($accountsubid)
          {

            $subgroupaccount=SubgroupAccounts::find()->where(['account_id'=>$accountheadid])->andWhere(['group_id'=>$accountsubid->master_id])->one();
            if($subgroupaccount){
              $subgroupaccount->subgrp_id = $accountsubid->id;
              $subgroupaccount->save(false);
            }
            else{
              $sql="INSERT INTO subgroup_accounts (group_id,subgrp_id,accountschedule_id,account_id,bsitem_id) VALUES ('".$accountsubid->master_id."','".$accountsubid->id."',0,'".$accountheadid."',0)";
              $command=$connection->createCommand($sql);
              $dataReader=$command->query();
            }

          }

          $accountsub=AccountsSub::find()->where(['ResourceType_Id'=>$_POST['resourcetype']])->one();

          if($accountsub){
            $subgroupaccount=SubgroupAccounts::find()->where(['account_id'=>$accountheadid])->andWhere(['group_id'=>$accountsub->master_id])->one();
            if($subgroupaccount){
              $subgroupaccount->subgrp_id = $accountsub->id;
              $subgroupaccount->save(false);
            }
            else{
              $sql="INSERT INTO subgroup_accounts (group_id,subgrp_id,accountschedule_id,account_id,bsitem_id) VALUES ('".$accountsub->master_id."','".$accountsub->id."',0,'".$accountheadid."',0)";
              $command=$connection->createCommand($sql);
              $dataReader=$command->query();
            }
          }
          /*else{
            $subgroupaccount=SubgroupAccounts::find()->where(['account_id'=>$accountheadid])->andWhere(['group_id'=>5])->one();
            if($subgroupaccount){
              $subgroupaccount->subgrp_id = $accountsubid->id;
              $subgroupaccount->save(false);
            }
          }*/
      }
      else
      {
          $model1=new AccountsItem();
          $model1->name= $_POST['accounthead-choice'];
          $model1->account_type= 8;
          $model1->resource_id= $resource->Resource_Id;
          if($model1->save(false)):

              $accountsubid=AccountsSub::find()->where(['LIKE', 'name',$_POST['accountsubgrp-choice']])->one();

              $accountsub=AccountsSub::find()->where(['ResourceType_Id'=>$_POST['resourcetype']])->one();

              $connection = \Yii::$app->db; 
              if($accountsub){
                $sql="INSERT INTO subgroup_accounts (group_id,subgrp_id,accountschedule_id,account_id,bsitem_id) VALUES (1,'".$accountsub->id."',0,'".$model1->id."',0)";
              }
              if($accountsubid){
                $sql="INSERT INTO subgroup_accounts (group_id,subgrp_id,accountschedule_id,account_id,bsitem_id) VALUES (5,'".$accountsubid->id."',0,'".$model1->id."',0)";
              }
              
              $command=$connection->createCommand($sql);
              $dataReader=$command->query();

          endif;

          $accountheadid = $model1->id;
      }

      $resource->Resource_Acc_Id = $accountheadid;

      $resource->save(false);

    endforeach;

    $arr=array('error'=>'No');
    return json_encode($arr);

  }

  public function actionResourcedetails()
  {
      $connection = \Yii::$app->db; 
      $sql = "SELECT * FROM resources WHERE Resource_Id='" . $_POST['id'] . "'";
      $command = $connection->createCommand($sql);
      $dataReader = $command->query();
      $data = $dataReader->read();

      $list = Resourcetype::find()->where(['Status'=>0])->all();

      $vendorlist = Vendors::find()->where(['Status'=>0])->all();

      $accountheads = AccountsItem::find()->all();
      $resourcegroups=array(array( 'ID'=>'2','name'=>'Project Setup'),
          array( 'ID'=>'3','name'=>'Production'),
          array( 'ID'=>'4','name'=>'Logistics'),
          array( 'ID'=>'5','name'=>'Construction'),
          array( 'ID'=>'8','name'=>'Overheads'),);
      //print_r($resourcegroups);exit;
      $resgrps=ResourceGroup::find()->where(['ResourceType_Id'=>$data['ResourceType_Id']])->all();
      $resgrplist='<option value="none">Select Resource Group</option>';
      foreach($resgrps AS $resgrp):
          if ($data['Resource_group_Id'] == $resgrp['Resource_group_Id']):
              $seletedgroup = 'Selected="selected"';
          else:
              $seletedgroup = '';
          endif;
          $resgrplist.='<option value="'.$resgrp['Resource_group_Id'].'" '.$seletedgroup.'>'.$resgrp['Resource_group_Name'].'</option>';
      endforeach;
      $arr = array();
      $groupval = '';
      $typesval = '';
      $accval = '';
      $vendorval = '';
      $datarows = '';

      foreach ($resourcegroups AS $resourcegroup):
          if ($data['Resource_group_Id'] == $resourcegroup['ID']):
              $seletedgroup = 'Selected="selected"';
          else:
              $seletedgroup = '';
          endif;
          $groupval .= "<option value='" . $resourcegroup['ID'] . "' " . $seletedgroup . ">" . $resourcegroup['name'] . "</option>";
      endforeach;
      foreach ($list AS $type):
          if ($data['ResourceType_Id'] == $type->ResourceType_Id):
              $seletedtype = 'Selected="selected"';
          else:
              $seletedtype = '';
          endif;
          $typesval .= "<option value='" . $type->ResourceType_Id . "' " . $seletedtype . ">" . $type->Name . "</option>";
      endforeach;

      foreach ($accountheads AS $accounthead):
          if ($data['Resource_Acc_Id'] == $accounthead->id):
              $seletedtype = 'Selected="selected"';
          else:
              $seletedtype = '';
          endif;
          $accval .= "<option value='" . $accounthead->id . "' " . $seletedtype . ">" . $accounthead->name . "</option>";
      endforeach;

      foreach ($vendorlist AS $type):
          $vendorval .= "<option value='" . $type->Vendor_Id . "' >" . $type->Name . "</option>";
      endforeach;

      $reschildss='
          <div class="col-md-4">
              <div class="form-group">
                  <label>Vendor</label>
                  <input type="text" class="form-control nwvendor" list="vendorlist" id="vendoradd'.$data['Resource_Id'].'" name="vendor-choice" placeholder="Select Vendor"/>
                  <span class="error"></span>
                  <datalist id="vendorlist">';

                  $vendors=Vendors::find()->Where(['Status'=>0])->all();
                  foreach($vendors AS $vendor):
                      $reschildss.='<option value='.$vendor->Name.'></option>';
                  endforeach;
                                
      $reschildss.='
                  </datalist>
              </div>
                    
              <div class="form-group">
                    <label>Location</label>
                    <input class="form-control" id="vendlocation'.$data['Resource_Id'].'" name="vendlocation" placeholder="Enter Vendor Location" type="text" />
                    <span class="error"></span>
                      
              </div>

          </div>
                  
          <div class="col-md-4">
              <div class="form-group">
                  <label>Brand</label>
                  <input class="form-control" list="brandlist" id="brandadd'.$data['Resource_Id'].'" name="brand-choice" placeholder="Select Brand"/>
                  <span class="error"></span>';

      $reschildss.='<datalist id="brandlist">';

                      $brands=Brand::find()->Where(['Status'=>0])->all();
                      foreach($brands AS $brand):
                            $reschildss.='<option value='.$brand->name.'></option>';
                      endforeach;
                                  
      $reschildss.='</datalist>

              </div>
              <div class="form-group">
                  <label>Phone</label>
                  <input class="form-control" id="vendorph'.$data['Resource_Id'].'" name="vendorph" placeholder="Enter Phone Number" type="text" />
                  <span class="error"></span>                      
              </div>
                  
          </div>
                  
          <div class="col-md-4">
              <div class="row">
                  <div class="col-md-6">
                      <div class="form-group">
                          <label>Unit</label>
                          <input class="form-control" type="text" id="resunit'.$data['Resource_Id'].'" name="resourceunit" placeholder="Unit" value="'.$data['Unit'].'" readonly>
                          <span class="error"></span>                               
                      </div>
                  </div>
                  <div class="col-md-6">
                      <div class="form-group">
                            <label>Rate</label>
                            <input class="form-control" type="text" id="resrate'.$data['Resource_Id'].'" name="resourcerate" placeholder="Rate" value="'.number_format($data['Price'],2).'">
                            <span class="error"></span>                                
                      </div>
                  </div>
              </div>
       
              <div class="form-group">
                <label>Email Address</label>
                <input class="form-control" id="vendemail'.$data['Resource_Id'].'" name="vendemail" placeholder="Enter email address" type="text" />
                <span class="error"></span>
              </div>

          </div>

          <div class="col-md-12">
                <div class="form-group text-center">
                    <span>&nbsp;</span><br/>
                    <button type="button" class="btn btn-danger cancel" id="canceladdvendor"><span class="icon-close"></span> Cancel</button>

                    <button type="button" class="btn btn-primary" value="'.$data['Resource_Id'].'" id="addvendor" title="Save vendor"><span class="icon-check"></span> Add Vendor</button>
                    <input type="hidden" id="saveresourceval" value="'.$data['Resource_Id'].'">                   
                </div>
          </div>';

      $arr['result'] = $reschildss;
      $arr['resourcename'] = $data['Name'];
      $arr['error'] = 'No';
      $arr['hasdata'] = 'Yes';
      return json_encode($arr);
  }

  public function actionAddvendor()
  {

      if(isset($_POST['resourcevendor'])):
        $vendor=Vendors::find()->Where(['LIKE', 'Name',$_POST['resourcevendor']])->one();

        if($vendor)
        {
            $vendorid=$vendor->Vendor_Id;
            $vendor->City= $_POST['vendorlocation'];
            $vendor->Phone= $_POST['vendorphone'];
            $vendor->Email= $_POST['vendoremail'];
            $vendor->save(false);

        }
        else
        {
            $model2=new Vendors();
            if(isset($_POST['resourcevendor'])):
             $model2->Name= $_POST['resourcevendor']; 
            endif;
            $model2->City= $_POST['vendorlocation'];
            $model2->Phone= $_POST['vendorphone'];
            $model2->Email= $_POST['vendoremail'];
            $model2->save(false);
            $vendorid = $model2->Vendor_Id;

            $accounthead_check=AccountsItem::find()->where(['LIKE', 'Name',$_POST['resourcevendor']])->one();

            if($accounthead_check){
              $venaccnt=AccountsItem::findOne($accounthead_check->id);
            }
            else{

              $venaccnt=new AccountsItem();
              $venaccnt->name= $_POST['resourcevendor'];
              $venaccnt->master_id=6;
              $venaccnt->account_type= 7;
              //$venaccnt->resource_id= $model->Resource_Id;
              $venaccnt->save(false);

              $vensbgrp=new SubgroupAccounts();
              $vensbgrp->group_id=6;
              $vensbgrp->subgrp_id=30;
              $vensbgrp->bsitem_id=12;
              $vensbgrp->account_id=$venaccnt->id;

              $vensbgrp->save(false);

            }
                             
            $vendorid = $model2->Vendor_Id;

            $vendordetails=Vendors::findOne($vendorid);
            $vendordetails->account_id=$venaccnt->id;
            $vendordetails->save(false);

        }
      endif;

      $brand=Brand::find()->Where(['LIKE', 'name',$_POST['brand']])->one();

      if($brand){
        $brandid=$brand->id;
      }

      else{
        $model3=new Brand();
        $model3->name= $_POST['brand'];
        $model3->save(false);
        $brandid = $model3->id;
      }

      if(isset($_POST['resourcevendor']))
      {
            $resmodel=Resources::findOne($_POST['resourceid']);
            $model=New Resources();
            $model->Name = $resmodel['Name'];
            $model->Unit = $resmodel['Unit'];
            $model->Resource_group_Id = $resmodel['Resource_group_Id'];
            $model->Price = $_POST['resrate'];
            $model->ResourceType_Id = $resmodel['ResourceType_Id'];
            $model->Project_Id = $resmodel['Project_Id'];
            $model->Resource_Acc_Id = $resmodel['Resource_Acc_Id'];
            $model->Status = $resmodel['Status'];
            $model->Added_By = $resmodel['Added_By'];
            $model->Vendor_Id = $vendorid;
            $model->Added_On = date('y-m-d h:i:s');
            $model->save(false);
            
            $connection = \Yii::$app->db; 
            $sql="INSERT INTO vendor_brand (id,Vendor_Id,brand_id,resource_id,status) VALUES ('','". $vendorid."','".$brandid."','".$model->Resource_Id."',0)";
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $arr=array('error'=>'No','vendor'=>$_POST['resourcevendor']);
            return json_encode($arr);
      }
  }

  public function actionGetresourcegroups()
  {
      $restype=$_POST['restype'];

      $resgrps=ResourceGroup::find()->Where(['ResourceType_Id'=>$restype])->all();

      $list='<option value="none">Select Resource Group</option>';
      foreach($resgrps AS $resgrp):
          $list.='<option value="'.$resgrp['Resource_group_Id'].'">'.$resgrp['Resource_group_Name'].'</option>';
      endforeach;
      $arr=array('error'=>'No','result'=>$list);
      return json_encode($arr);
  }

  public function actionGetresourcedetails()
  {

      $connection = \Yii::$app->db; 
      $sql = "SELECT * FROM resources WHERE Resource_Id='" . $_POST['id'] . "'";
      $command = $connection->createCommand($sql);
      $dataReader = $command->query();
      $data = $dataReader->read();

      $list=Resourcetype::find()->Where(['Status' => 0])->all();

      $vendorlist=Vendors::find()->Where(['Status' => 0])->all();

      $accountheads=AccountsItem::find()->all();

      $grouplist=ResourceGroup::find()->Where(['ResourceType_Id'=>$data['ResourceType_Id']])->all();

      $resourcegroups=array(array( 'ID'=>'2','name'=>'Project Setup'),
          array( 'ID'=>'3','name'=>'Production'),
          array( 'ID'=>'4','name'=>'Logistics'),
          array( 'ID'=>'5','name'=>'Construction'),
          array( 'ID'=>'8','name'=>'Overheads'),);

      $arr = array();
      $groupval = '';
      $typesval = '';
      $accval = '';
      $vendorval = '';
      $datarows = '';
      $editres = '';
      $resgrp = '';

      foreach ($resourcegroups AS $resourcegroup):
          if ($data['Resource_group_Id'] == $resourcegroup['ID']):
              $seletedgroup = 'Selected="selected"';
          else:
              $seletedgroup = '';
          endif;
          $groupval .= "<option value='" . $resourcegroup['ID'] . "' " . $seletedgroup . ">" . $resourcegroup['name'] . "</option>";
      endforeach;
      foreach ($list AS $type):
          if ($data['ResourceType_Id'] == $type->ResourceType_Id):
              $seletedtype = 'Selected="selected"';
          else:
              $seletedtype = '';
          endif;
          $typesval .= "<option value='" . $type->ResourceType_Id . "' " . $seletedtype . ">" . $type->Name . "</option>";
      endforeach;


      foreach ($accountheads AS $accounthead):
          if ($data['Resource_Acc_Id'] == $accounthead->id):
              $seletedtype = 'Selected="selected"';
          else:
              $seletedtype = '';
          endif;
          $accval .= "<option value='" . $accounthead->id . "' " . $seletedtype . ">" . $accounthead->name . "</option>";
      endforeach;


      foreach ($vendorlist AS $type):
          if ($data['Vendor_Id'] == $type->Vendor_Id):
              $seletedvendor = 'Selected="selected"';
          else:
              $seletedvendor = '';
          endif;
          $vendorval .= "<option value='" . $type->Vendor_Id . "' " . $seletedvendor . ">" . $type->Name . "</option>";
      endforeach;
      foreach ($grouplist AS $type):
          if ($data['Resource_group_Id'] == $type->Resource_group_Id):
              $seletedgroup = 'Selected="selected"';
          else:
              $seletedgroup = '';
          endif;
          $resgrp .= "<option value='" . $type->Resource_group_Id . "' " . $seletedgroup . ">" . $type->Resource_group_Name . "</option>";
      endforeach;
                   
       $editres.='
        <div class="col-md-6">
          <div class="form-group">
            <label>Select Resrouce Type</label>
              <select class="form-control editrestypelist" id="editresourcetype'.$data['Resource_Id'].'" data-id="'.$data['Resource_Id'].'">
                   <option value="none">Select Resource Type</option>'.$typesval.'
                   </select><span class="error"></span>
          </div>
          <div class="form-group">
            <label>Select Resrouce Group</label>
              <select class="form-control" id="editgrouptypetext'.$data['Resource_Id'].'">
                <option value="none">Select Resource Group</option>'.$resgrp.'
              </select><span class="error"></span>
          </div>
          <div class="form-group">
            <label>Unit</label>
            <input class="form-control" type="text" id="resunit'.$data['Resource_Id'].'" name="resourceunit" placeholder="Unit" value="'.$data['Unit'].'">
            <span class="error"></span>
              
          </div>
                    
        </div>
                  
        <div class="col-md-6">

          <div class="form-group">
            <label>Resource Name</label>
            <input class="form-control" type="text" id="editresourcename'.$data['Resource_Id'].'" value="'.$data['Name'].'">
                                     <span class="error"></span>
          </div>
                    
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Quantity</label>
                <input class="form-control"  type="text" id="resquantity'.$data['Resource_Id'].'" name="resquantity" value="'.$data['Quantity'].'">
                <span class="error"></span>
              </div>  
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label>Log</label>
                  <input type="checkbox" id="logcheck" style="visibility: visible;" '.($data['log_check']==1?'checked':'').' name="logcheck" value="1">
                          
              </div>
            </div>
          </div>
                    
          <div class="form-group text-right">
            <span>&nbsp;</span><br/>
            <button type="button" class="btn btn-danger cancel" id="canceleditres"><span class="icon-close"></span> Cancel</button>
            
            <button type="button" class="btn btn-primary" value="'.$data['Resource_Id'].'" id="saveresourcebutton" title="Save Resource"><span class="icon-check"></span> Edit Resources</button>
          </div>
                    
        </div>';            
                   
      $arr['result'] = $editres;
      $arr['error'] = 'No';
      $arr['hasdata'] = 'Yes';
      return json_encode($arr);

  }

  public function actionVendoraccount()
  {
    $vendors = Vendors::find()->where(['Status'=>0])->all();

    foreach($vendors as $vendor):

      $venaccnt=new AccountsItem();
      $venaccnt->name= $vendor->Name;
      $venaccnt->account_type= 5;
      //$venaccnt->resource_id= $model->Resource_Id;
      $venaccnt->save(false);

      $vendor->account_id = $venaccnt->id;
      $vendor->save(false);

    endforeach;

  }

  public function actionVendordetails()
  {
    $vendor = Vendors::find()->where(['like', 'Name',$_POST['vendor']])->one();

    $location = '';
    $phone = '';
    $email = '';
    $count = 0;

    if($vendor){
      $location = $vendor->City;
      $phone = $vendor->Phone;
      $email = $vendor->Email;
      $count = 1;
    }

    $arr = array('count' =>$count,'location' =>$location,'phone' =>$phone,'email' =>$email, 'No','error' => 'No');

    return json_encode($arr); 

  }

  public function actionResdataclear()
  {
    $accountheads=AccountsItem::find()->where(['account_type'=>8])->all();

    foreach($accountheads as $accounthead):

      /*$subgroupaccnts=SubgroupAccounts::find()->where(['account_id'=>$accounthead->id])->andWhere(['group_id'=>5])->all();

      foreach($subgroupaccnts as $subgroupaccnt):

        $resource=Resources::find()->where(['LIKE', 'Name',$accounthead->name])->one();

        if($resource){

          $accountsub=AccountsSub::find()->where(['ResourceType_Id'=>$resource->ResourceType_Id])->one();   
          if($accountsub){
            $subgroupaccnt->group_id = 1;
            $subgroupaccnt->subgrp_id = $accountsub->id;
            $subgroupaccnt->account_id = $accounthead->id;
            $subgroupaccnt->save(false);
          }

        }

      endforeach;*/

      $resource=Resources::find()->where(['LIKE', 'Name',$accounthead->name])->one();

      if($resource){

        $accountsub=AccountsSub::find()->where(['ResourceType_Id'=>$resource->ResourceType_Id])->one();

        if($accountsub){

          $subgroupaccnts=SubgroupAccounts::deleteAll(['account_id'=>$accounthead->id]);

          $connection = \Yii::$app->db; 
          $sql="INSERT INTO subgroup_accounts (group_id,subgrp_id,accountschedule_id,account_id,bsitem_id) VALUES (1,'".$accountsub->id."',0,'".$accounthead->id."',12)";
          $command=$connection->createCommand($sql);
          $dataReader=$command->query();

        }

      }

    endforeach;


  }

  public function actionResestdata()
  {

    $childdata=Resources::findOne($_POST['resid']);

    $vendor=Vendors::find()->where(['Vendor_Id'=>$childdata['Vendor_Id']])->one();
    $vendorname='';
    if($vendor)
    {
      $vendorname=$vendor->Name." ".$vendor->Brand;
    }

    if($_POST['type'] == 'no_of_hrs'){
        $datarows='
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

                      $datarows.='<option value="Petrol">Petrol</option>
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
                        <label>No of Hours</label>
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
                </div>';
    }
    else
    {
      $datarows='
                <div class="col-md-6">
                  <div class="row">
                    <div class="col-md-6 type">
                      <label>Location</label>
                      <span>'.$childdata['Resource_Location'].'</span>
                    </div>
                    <div class="col-md-6 type">
                      <label>Unit</label>
                      <span>'.$childdata['Unit'].'</span>
                      
                    </div>
                  </div>
                </div>
                <div class="col-md-6" style="padding-right:25px">
                  <div class="row">
                    
                    

                    <div class="col-md-4 type">
                      <div class="form-group">
                        <label>Rate</label>
                        <input style="text-align: right;" id="estimate-specificrate'.$childdata['Resource_Id'].'" type="text" class="form-control" value="'.number_format($childdata['Price'],2).'" />
                      </div>                      
                    </div>
                    <div class="col-md-4 type">
                      <div class="form-group">
                        <label>Lease Period</label>
                        
                        <input style="text-align: right;" type="text" id="estimate-leaseperiod'.$childdata['Resource_Id'].'" class="form-control" value="">



                      </div>                      
                    </div>

                    <div class="col-md-3 type">
                      <div class="form-group">
                        <label>&nbsp;</label>
                        <button value="'.$childdata['Resource_Id'].'" class="btn btn-primary add-alloc-item estimate-addresource" ><span class="icon-add"></span>Add</button>
                      </div>
                    </div>
                  </div>
                </div>';
    }

    $arr = array('datarows' =>$datarows,'error' => 'No');

    return json_encode($arr);


  }

  public function actionResplantestdata()
  {

    $childdata=Resources::findOne($_POST['resid']);

    $vendor=Vendors::find()->where(['Vendor_Id'=>$childdata['Vendor_Id']])->one();
    $vendorname='';
    if($vendor)
    {
      $vendorname=$vendor->Name." ".$vendor->Brand;
    }

    if($_POST['type'] == 'plant_no_of_hrs'){
        $datarows='
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
              $datarows.='<option value="Petrol">Petrol</option>
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
                </div>';
    }
    else
    {
      $datarows='
      <!-- <div class="col-md-6">
                  <div class="row">
                    <div class="col-md-6 type">
                      <label>Location</label>
                      <span>'.$childdata['Resource_Location'].'</span>
                    </div>
                    <div class="col-md-6 type">
                      <label>Unit</label>
                      <span>'.$childdata['Unit'].'</span>
                      
                    </div>
                  </div>
                </div>
                <div class="col-md-6" style="padding-right:25px">
                  <div class="row">
                    
                    

                    <div class="col-md-4 type">
                      <div class="form-group">
                        <label>Rate</label>
                        <input style="text-align: right;" id="estimate-specificrate'.$childdata['Resource_Id'].'" type="text" class="form-control" value="'.number_format($childdata['Price'],2).'" />
                      </div>                    
                    </div>
                    <div class="col-md-6 type">
                      <div class="form-group">
                        <label>Depreciation</label>
                        
                        <input style="text-align: right;" type="text" id="estimate-leaseperiod'.$childdata['Resource_Id'].'" class="form-control" value="">



                      </div>                      
                    </div>

                    <div class="col-md-3 type">
                      <div class="form-group">
                        <label>&nbsp;</label>
                        <button value="'.$childdata['Resource_Id'].'" class="btn btn-primary add-alloc-item estimate-addresource" ><span class="icon-add"></span>Add</button>
                      </div>
                    </div>
                  </div>
                </div> --> 
                
                
                <div class="col-md-12">
                  <div class="row">
                    <div class="col-md-3 type">
                      <label>Location</label>
                      <span>'.$childdata['Resource_Location'].'</span>
                    </div>
                    <div class="col-md-3 type">
                      <label>Unit</label>
                      <span>'.$childdata['Unit'].'</span>
                    </div>
                    <div class="col-md-3 type">
                      <div class="form-group">
                      <label>Depreciation</label>
                      
                      <input style="text-align: right;" type="text" id="estimate-plantrate'.$childdata['Resource_Id'].'" class="form-control" value="'.number_format($childdata['Price'],2).'">



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
                ';
    }

    $arr = array('datarows' =>$datarows,'error' => 'No');

    return json_encode($arr);


  }

  public function actionMasterplantestdata()
  {

    $childdata=Resources::findOne($_POST['resid']);

    $vendor=Vendors::find()->where(['Vendor_Id'=>$childdata['Vendor_Id']])->one();
    $vendorname='';
    if($vendor)
    {
      $vendorname=$vendor->Name." ".$vendor->Brand;
    }

    if($_POST['type'] == 'mplant_no_of_hrs'){
        $datarows='
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
                      <select class="form-control fueltype" name="mastful" data-id='.$childdata['Resource_Id'].' type="text" id="fueltype'.$childdata['Resource_Id'].'">';
                      $enggselected = '';
                      $motoselected = '';

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

                      $datarows.='<option value="Petrol">Petrol</option>
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
                        <input style="text-align: right;" id="mast-machrate'.$childdata['Resource_Id'].'" data-id="'.$childdata['Resource_Id'].'" type="text" class="form-control estimate-machrate" value="'.$childdata['machinecost'].'" />
                      </div>
                      
                    </div>
                    

                    <div class="col-md-3 type">
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
                        <button value="'.$childdata['Resource_Id'].'" data-id="'.$_POST['restype'].'" class="btn btn-primary add-alloc-item master-addresource" ><span class="icon-add"></span>Add</button>
                      </div>
                    </div>
                  </div>
                </div>';
    }
    else
    {
      $datarows='
      <!-- <div class="col-md-6">
                  <div class="row">
                    <div class="col-md-6 type">
                      <label>Location</label>
                      <span>'.$childdata['Resource_Location'].'</span>
                    </div>
                    <div class="col-md-6 type">
                      <label>Unit</label>
                      <span>'.$childdata['Unit'].'</span>
                      
                    </div>
                  </div>
                </div>
                <div class="col-md-6" style="padding-right:25px">
                  <div class="row">
                    
                    

                    <div class="col-md-4 type">
                      <div class="form-group">
                        <label>Rate</label>
                        <input style="text-align: right;" id="estimate-specificrate'.$childdata['Resource_Id'].'" type="text" class="form-control" value="'.number_format($childdata['Price'],2).'" />
                      </div>                    
                    </div>
                    <div class="col-md-6 type">
                      <div class="form-group">
                        <label>Depreciation</label>
                        
                        <input style="text-align: right;" type="text" id="estimate-leaseperiod'.$childdata['Resource_Id'].'" class="form-control" value="">



                      </div>                      
                    </div>

                    <div class="col-md-3 type">
                      <div class="form-group">
                        <label>&nbsp;</label>
                        <button value="'.$childdata['Resource_Id'].'" class="btn btn-primary add-alloc-item estimate-addresource" ><span class="icon-add"></span>Add</button>
                      </div>
                    </div>
                  </div>
                </div> --> 
                
                
                <div class="col-md-12">
                  <div class="row">
                    <div class="col-md-3 type">
                      <label>Location</label>
                      <span>'.$childdata['Resource_Location'].'</span>
                    </div>
                    <div class="col-md-3 type">
                      <label>Unit</label>
                      <span>'.$childdata['Unit'].'</span>
                    </div>
                    <div class="col-md-3 type">
                      <div class="form-group">
                      <label>Depreciation</label>
                      
                      <input style="text-align: right;" type="text" id="estimate-plantrate'.$childdata['Resource_Id'].'" class="form-control" value="'.number_format($childdata['Price'],2).'">



                    </div>                      
                    </div>
                    <div class="col-md-3 type">
                    <div class="form-group">
                    <label>&nbsp;</label>
                    <button value="'.$childdata['Resource_Id'].'" data-id="'.$_POST['restype'].'" class="btn btn-primary add-alloc-item master-addresource" ><span class="icon-add"></span>Add</button>
                  </div>
                    </div>
                        
                  </div>
                  
                </div>
                ';
    }

    $arr = array('datarows' =>$datarows,'error' => 'No');

    return json_encode($arr);


  }

  public function actionMasterestdata()
  {

    $childdata=Resources::findOne($_POST['resid']);

    $vendor=Vendors::find()->where(['Vendor_Id'=>$childdata['Vendor_Id']])->one();
    $vendorname='';
    if($vendor)
    {
      $vendorname=$vendor->Name." ".$vendor->Brand;
    }

    if($_POST['type'] == 'mlno_of_hrs'){
        $datarows='
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

                      $datarows.='<option value="Petrol">Petrol</option>
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
                        <button value="'.$childdata['Resource_Id'].'" data-id="'.$_POST['restype'].'" class="btn btn-primary add-alloc-item master-addresource" ><span class="icon-add"></span>Add</button>
                      </div>
                    </div>
                  </div>
                </div>';
    }
    else
    {
      
      $datarows='
                <div class="col-md-6">
                  <div class="row">
                    <div class="col-md-6 type">
                      <label>Location</label>
                      <span>'.$childdata['Resource_Location'].'</span>
                    </div>
                    <div class="col-md-6 type">
                      <label>Unit</label>
                      <span>'.$childdata['Unit'].'</span>
                      
                    </div>
                  </div>
                </div>
                <div class="col-md-6" style="padding-right:25px">
                  <div class="row">
                    
                    

                    <div class="col-md-4 type">
                      <div class="form-group">
                        <label>Rate</label>
                        <input style="text-align: right;" id="estimate-specificrate'.$childdata['Resource_Id'].'" type="text" class="form-control" value="'.number_format($childdata['Price'],2).'" />
                      </div>                      
                    </div>
                    <div class="col-md-4 type">
                      <div class="form-group">
                        <label>Lease Period</label>
                        
                        <input style="text-align: right;" type="text" id="estimate-leaseperiod'.$childdata['Resource_Id'].'" class="form-control" value="">



                      </div>                      
                    </div>

                    <div class="col-md-3 type">
                      <div class="form-group">
                        <label>&nbsp;</label>
                        <button value="'.$childdata['Resource_Id'].'" data-id="'.$_POST['restype'].'" class="btn btn-primary add-alloc-item master-addresource" ><span class="icon-add"></span>Add</button>
                      </div>
                    </div>
                  </div>
                </div>';
    }

    $arr = array('datarows' =>$datarows,'error' => 'No');

    return json_encode($arr);


  }

}
