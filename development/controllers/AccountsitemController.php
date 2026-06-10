<?php


namespace app\controllers;  

use Yii;
use yii\filters\AccessControl; 
use yii\web\Controller;
use yii\web\Response;
use yii\helpers\ArrayHelper; 
use yii\filters\VerbFilter;  
use app\models\AccountTypes; 
use app\models\Resources;  
use app\models\ResourceGroup;    
use app\models\AccountsSub;    
use app\models\AccountsItem;
use app\models\Accountsmaster; 
use app\models\Bsitems; 
use app\models\SubgroupAccounts;
use app\models\LedgerOpeningbalance;
use app\models\Resourcetype;
class AccountsitemController extends Controller
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

    /* Action Status Active Start*/

    public function actionCheckname()
    {
       // $connection = CActiveRecord::getDbConnection();
        $connection = \Yii::$app->db; 
        $sql="SELECT COUNT(*) AS total FROM accounts_item WHERE name='".$_POST['name']."' ";
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $data=$dataReader->read();
        if($data['total']>0):
            echo 'Yes';
        else:
            echo 'No';
        endif;
    }
    
    public function actionUpdateaccoundheads()
    {
        $connection = \Yii::$app->db; 
        if($_POST['subgrpid']!='none')
        {
            $sql="SELECT a.id,a.name,a.tds,a.servicetax,a.account_type,a.schedule,c.name AS subname FROM accounts_item AS a LEFT JOIN subgroup_accounts AS b on a.id=b.account_id LEFT JOIN accounts_sub AS c on b.subgrp_id=c.id where b.subgrp_id='".$_POST['subgrpid']."' ";
            if($_POST['accounts']!='')
                $sql.="AND a.name LIKE '%".$_POST['accounts']."%' ";
            $sql.=" ORDER BY name ASC";
            //echo $sql;exit;
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $dataProvider=$dataReader->readAll();
        }
        else
        {
            //$sql="SELECT id,name,tds,servicetax,account_type,schedule FROM accounts_item";
            $sql="SELECT a.id,a.name,a.tds,a.servicetax,a.account_type,a.schedule,c.name AS subname FROM accounts_item AS a LEFT JOIN subgroup_accounts AS b  on a.id=b.account_id LEFT JOIN accounts_sub AS c on b.subgrp_id=c.id " ;
            if($_POST['accounts']!='')
                $sql.=" WHERE a.name LIKE '%".$_POST['accounts']."%' ";
            $sql.=" ORDER BY a.name ASC";
            //echo $sql;exit;
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $dataProvider=$dataReader->readAll();
        }
        $datarows='';
        if(count($dataProvider)>0):
            foreach($dataProvider AS $key=>$data):
                $accounttype=array('1'=>'Cash','2'=>'Bank','4'=>'Income','5'=>'Expense','6'=>'Asset','7'=>'Liability');
                //echo $data['account_type'];
                for($i==1;$i<=count($accounttype);$i++)
                {
                    echo $i;
                    $type=$accounttype[$data['account_type']];
                    if($i==$data['account_type'])
                    {
                        $selected='selected';
                    }
                    else
                    {
                        $selected='';
                    }
                }
                
                if($data['account_type']==1){
                    $type='Cash';
                    $cash='selected';
                    $bank='';
                    $Income='';
                    $Expense='';
                    $Asset='';
                    $Liability='';
                }
                elseif($data['account_type']==2){
                    $type='Bank';
                    $cash='';
                    $bank='selected';
                    $Income='';
                    $Expense='';
                    $Asset='';
                    $Liability='';
                }
                elseif($data['account_type']==4){
                    $type='Income';
                    $cash='';
                    $bank='';
                    $Income='selected';
                    $Expense='';
                    $Asset='';
                    $Liability='';
                }
                elseif($data['account_type']==5){
                    $type='Expense';
                    $cash='';
                    $bank='';
                    $Income='';
                    $Expense='selected';
                    $Asset='';
                    $Liability='';
                }
                elseif($data['account_type']==6){
                    $type='Asset';
                    $cash='';
                    $bank='';
                    $Income='';
                    $Expense='';
                    $Asset='selected';
                    $Liability='';
                }
                elseif($data['account_type']==7){
                    $type='Liability';
                    $cash='';
                    $bank='';
                    $Income='';
                    $Expense='';
                    $Asset='';
                    $Liability='selected';
                }
                if($data['schedule']==3){
                    $schedule='Schedule';
                    $checked='checked="checked"';
                }
                else
                {
                    $schedule='';
                    $checked='';
                }

                $datarows.="<tr id='accountsrow".$data['id']."' class='accounts".$data['id']."'>
                            <td  class='small75'>".$data['id']."</td>
                            <td style='width: 17%;'><span id='accountstext".$data['id']."'>".$data['name']."</span>
                            <input class='form-control editaccountsname' type='text' id='editaccountsname".$data['id']."' value='".$data['name']."'>
                            <span class='error'></span></td>
                            <td style='width: 19%;'><span id='accountype".$data['id']."'>$type</span>
                            <select id='editaccountype".$data['id']."' name='editaccountype' class='form-control editaccountype' >
                                <option value='0'>Select Account type</option>
                                <option value='4' $Income>Income</option>
                                <option value='5' $Expense>Expense</option>
                                <option value='6' $Asset>Asset</option>
                                <option value='7' $Liability>Liability</option>
                                <option value='1' $cash>Cash</option>
                                <option value='2' $bank>Bank</option>
                                </select></td>

                            <td style='width: 12%;'><span id='accountstdstext".$data['id']."'>".$data['tds']."</span>

                            <input class='form-control editaccountstds' type='text' id='editaccountstds".$data['id']."' value='".$data['tds']."'>
                            <span class='error'></span></td>
                            <td style='width: 12%;'><span id='accountservtaxtext".$data['id']."'>".$data['servicetax']."</span>
                            <input class='form-control editaccountservtax' type='text' id='editaccountservtax".$data['id']."' value='".$data['servicetax']."'>
                            <span class='error'></span></td>
                            <td  class='' style='width: 12%;' >".$data['subname']."</td>
                            <td style='width: 12%;'><span id='schedule".$data['id']."'>$schedule</span>
                            <span class='editschedule' id='editschedule".$data['id']."'>
                            <input type='checkbox' style='visibility: visible;' value='3' $checked id='schedulecheck".$data['id']."'>Schedule</td>
                            <td class='small75'><button type='button' class='btn btn-primary saveaccountsbutton' title='Save Accounts' value='".$data['id']."' id='saveaccountsbutton".$data['id']."'> <span class='glyphicon glyphicon-save'></span></button></td>";

                $datarows.="<td class='small75'><button type='button' title='Delete Accounts' class='btn btn-primary deleteaccountsbutton' value='".$data['id']."' id='deleteaccountsbutton".$data['id']."'> <span class='glyphicon glyphicon-trash'></span></button></td>";

            endforeach;
        else:
            $datarows='<tr id="nodata"><td colspan="8" style="text-align: center;">No Accounts Found</td></tr>';
        endif;
        $arr = array('result' => $datarows,'error'=>'No');
        return json_encode($arr);
    } 

    public function actionCreate()
    {
        //echo "hi"; exit;
        $model=new AccountsItem();
        $connection = \Yii::$app->db; 
        if(isset($_POST['accountsname'])):
            //echo 'hai'; exit;
            $model->name=str_replace("'", "", $_POST['accountsname']);
            $model->tds=$_POST['accounttds'];
            if(isset($_POST['accountservtax'])):
                $model->servicetax=$_POST['accountservtax'];
            endif;
            $model->account_type=$_POST['accounttype'];
            if(isset($_POST['schedule'])):
                $model->schedule=$_POST['schedule'];
            endif;
            if(isset($_POST['resourcetype'])):
                $model->resource_group=$_POST['resourcetype'];
            endif;
             if(isset($_POST['resourcetype_cor'])):
                if($_POST['resourcetype_cor']!=0):
                    $model->resource_group=$_POST['resourcetype_cor'];
                endif;
            endif;
            $groups=$_POST['accountgroup'];
            if(isset($_POST['subgroups'])):
                $subgroups=$_POST['subgroups'];
            endif;

            if(isset($_POST['resources'])):
                $resources=$_POST['resources'];
            endif;
        
            if($model->save(false)):
                for($i=0;$i<count($groups);$i++)
                {
                    if(isset($_POST['bsitems'.$groups[$i]]))
                    {
                        $bsitems=$_POST['bsitems'.$groups[$i]];
                    }
                    else
                    {
                        $bsitems=0;
                    }
               
                    $sql="INSERT INTO subgroup_accounts (group_id,subgrp_id,accountschedule_id,account_id,bsitem_id) VALUES ('".$groups[$i]."','".$_POST['accountsubgrps'.$groups[$i]]."','','".$model->id."','".$bsitems."')";

                    $command=$connection->createCommand($sql);
                    $dataReader=$command->query();

                    $subgroup=Accountsmaster::findOne($groups[$i]); 

                    if($subgroup->name=='Project Expenditure'){

                        $subgrpid1=$subgroup->id;

                    }
                        
                    if($subgroup->name=='Corporate Expenditure'){

                        $subgrpid2=$subgroup->id;

                    }


                }

                /*if($_POST['accounttype']==8){

                    $resources=Resources::model()->find(array('condition'=>'Name LIKE "'.$model->name.'" AND pricing_status=0'));

                    if($subgrpid1){
                        $accsub=AccountsSub::model()->findByPk($_POST['accountsubgrps'.$subgrpid1.'']);
                    }

                    if($subgrpid2){
                        $accsub2=AccountsSub::model()->findByPk($_POST['accountsubgrps'.$subgrpid2.'']);
                    }

                    if(count($resources)>0)
                    {
                        if($subgrpid1){
                            if($accsub->name=='Project Support Expenses'){
                                $sql="SELECT * FROM estimateactivities WHERE activity_name LIKE 'Project Support Activities' ";
                                $command=$connection->createCommand($sql);
                                $dataReader=$command->query();
                                $estimateactivities=$dataReader->read();

                                $sql="SELECT * FROM estactivity_resources WHERE estactivity_id='".$estimateactivities['activity_id']."' AND est_resource_id='".$resources['Resource_Id']."'";
                                $command=$connection->createCommand($sql);
                                $dataReader=$command->query();
                                $estresources=$dataReader->read();

                                if(count($estresources)==0){

                                    $sql1="INSERT INTO estactivity_resources (estactivity_id,est_resource_id,est_resource_quantity,est_resource_status) VALUES('".$estimateactivities['activity_id']."','".$resources['Resource_Id']."','1','0')";
                                    //echo $sql;exit;
                                    $command=$connection->createCommand($sql1);
                                    $dataReader=$command->query();
                                }
                            }
                            elseif($accsub->name=='Project General Expense'){
                                $sql="SELECT * FROM estimateactivities WHERE activity_name LIKE 'Project General Support Activities' ";
                                $command=$connection->createCommand($sql);
                                $dataReader=$command->query();
                                $estimateactivities=$dataReader->read();

                                $sql="SELECT * FROM estactivity_resources WHERE estactivity_id='".$estimateactivities['activity_id']."' AND est_resource_id='".$resources['Resource_Id']."'";
                                $command=$connection->createCommand($sql);
                                $dataReader=$command->query();
                                $estresources=$dataReader->read();

                                if(count($estresources)==0){

                                    $sql1="INSERT INTO estactivity_resources (estactivity_id,est_resource_id,est_resource_quantity,est_resource_status) VALUES('".$estimateactivities['activity_id']."','".$resources['Resource_Id']."','1','0')";
                                    //echo $sql;exit;
                                    $command=$connection->createCommand($sql1);
                                    $dataReader=$command->query();
                                }
                            }
                        }

                        if($subgrpid2){
                            if($accsub2->name=='Corporate Expenses'){
                                $sql="SELECT * FROM estimateactivities WHERE activity_name LIKE 'Corporate Activities' ";
                                $command=$connection->createCommand($sql);
                                $dataReader=$command->query();
                                $estimateactivities=$dataReader->read();

                                $sql="SELECT * FROM estactivity_resources WHERE estactivity_id='".$estimateactivities['activity_id']."' AND est_resource_id='".$resources['Resource_Id']."'";
                                $command=$connection->createCommand($sql);
                                $dataReader=$command->query();
                                $estresources=$dataReader->read();

                                if(count($estresources)==0){

                                    $sql1="INSERT INTO estactivity_resources (estactivity_id,est_resource_id,est_resource_quantity,est_resource_status) VALUES('".$estimateactivities['activity_id']."','".$resources['Resource_Id']."','1','0')";
                                    //echo $sql;exit;
                                    $command=$connection->createCommand($sql1);
                                    $dataReader=$command->query();
                                }
                            }
                        }
                    }
                    else
                    {
                        $resource = new Resources;
                        $resource->Name = $model->name;

                        if($subgrpid1){

                            if($accsub->name=='Project Support Expenses'){
                                $resource->Unit = 'No of Days';
                                $Resgroup=ResourceGroup::model()->findByPk($_POST['resourcetype']);
                                $resource->Resource_group_Id = $_POST['resourcetype'];
                                $resource->ResourceType_Id = $Resgroup->ResourceType_Id;
                                $resource->Quantity = 1;
                                $resource->Added_On = date('y-m-d h:i:s');
                                $resource->save();

                                $sql="SELECT * FROM estimateactivities WHERE activity_name LIKE 'Project Support Activities' ";
                                $command=$connection->createCommand($sql);
                                $dataReader=$command->query();
                                $estimateactivities=$dataReader->read();

                                if($estimateactivities){           
                                    $sql1="INSERT INTO estactivity_resources (estactivity_id,est_resource_id,est_resource_quantity,est_resource_status) VALUES('".$estimateactivities['activity_id']."','".$resource->Resource_Id."','1','0')";
                                    //echo $sql;exit;
                                    $command=$connection->createCommand($sql1);
                                    $dataReader=$command->query();
                                }

                            }
                            elseif($accsub->name=='Project General Expense'){
                                $resource->Unit = 'Lumsum';
                                $Resgroup=ResourceGroup::model()->findByPk($_POST['resourcetype']);
                                $resource->Resource_group_Id = $_POST['resourcetype'];
                                $resource->ResourceType_Id = $Resgroup->ResourceType_Id;
                                $resource->Quantity = 1;
                                $resource->Added_On = date('y-m-d h:i:s');
                                $resource->save();

                                $sql="SELECT * FROM estimateactivities WHERE activity_name LIKE 'Project General Support Activities' ";
                                $command=$connection->createCommand($sql);
                                $dataReader=$command->query();
                                $estimateactivities=$dataReader->read();

                                if($estimateactivities){
                                    $sql1="INSERT INTO estactivity_resources (estactivity_id,est_resource_id,est_resource_quantity,est_resource_status) VALUES('".$estimateactivities['activity_id']."','".$resource->Resource_Id."','1','0')";
                                    //echo $sql;exit;
                                    $command=$connection->createCommand($sql1);
                                    $dataReader=$command->query();
                                }
                            }

                        }

                        if($subgrpid2){

                            if($accsub2->name=='Corporate Expenses'){
                                $resource->Unit = 'No of Days';
                                $Resgroup=ResourceGroup::model()->findByPk($_POST['resourcetype_cor']);
                                $resource->Resource_group_Id = $_POST['resourcetype_cor'];
                                $resource->ResourceType_Id = $Resgroup->ResourceType_Id;
                                $resource->Quantity = 1;
                                $resource->Added_On = date('y-m-d h:i:s');
                                $resource->save();
                            }

                            $sql="SELECT * FROM estimateactivities WHERE activity_name LIKE 'Corporate Activities' ";
                            $command=$connection->createCommand($sql);
                            $dataReader=$command->query();
                            $estimateactivities=$dataReader->read();

                            if($estimateactivities){
                                $sql1="INSERT INTO estactivity_resources (estactivity_id,est_resource_id,est_resource_quantity,est_resource_status) VALUES('".$estimateactivities['activity_id']."','".$resource->Resource_Id."','1','0')";
                                //echo $sql;exit;
                                $command=$connection->createCommand($sql1);
                                $dataReader=$command->query();
                            }

                        }
                        
                    }

                }*/

                /*if(isset($_POST['save_create'])!='mode'){
                  $this->redirect(array('/projects/masters'));
                }
                else{
                  $this->redirect(array('/accountsitem/create'));
                }*/

            endif;

            $arr = array('Id' => $model->id,'error'=>'No');
            return json_encode($arr);

        else:
            $arr = array('id' => '', 'Name' => '','error'=>'Yes','errortext'=>'Cannot add now. Please try again later');
            return json_encode($arr);
        endif;

    }

    public function actionCreateaccountype()  
    {
        $model=New AccountTypes();
        $model->name=$_POST['accounttypename'];
        $model->save(false);
        $arr=array('result'=>$model->name,'error'=>'No');
        return json_encode($arr);
    }

    public function actionAccounttypes()
    {
        $connection = \Yii::$app->db; 
        $sql="SELECT type_id,name FROM account_types WHERE Status=0 ORDER BY sortorder ASC";
        if($_POST['acntgrpname']!='')
            $sql.=" AND name LIKE '%".$_POST['acntgrpname']."%'";
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $dataProvider=$dataReader->readAll();
        $datarows='';
        $acctype='';
        if(count($dataProvider)>0):
            foreach($dataProvider AS $key=>$data):

                $acctype.=' 
                    <div class="finance-type-cntnt-wrpr row acnttypes'.$data['type_id'].'" id="acnttypesrow'.$data['type_id'].'">
                        <div class="col-md-10 col-sm-10">
                            <div class="row">
                                <div class="col-md-1 col-sm-1 finance-list-number">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <span class="number indent-number">'.($key+1).'</span>  
                                        </div>
                                            
                                    </div>
                                </div>                                  
                                <div class="col-md-5 col-sm-5 type finance-type">
                                    
                                    <span id="acnttypestext'.$data['type_id'].'">'.$data['name'].'</span>
                                    <input class="form-control editacnttypesname" style="display:none" type="text" id="editacnttypesname'.$data['type_id'].'" value="'.$data['name'].'">
                                    <span class="error"></span>
                                </div>
                                    
                                <div class="col-md-6 col-sm-6"></div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-2 icon-groups">

                            <button type="" class="editacnttypesbutton" value="'.$data['type_id'].'" id="editacnttypesbutton'.$data['type_id'].'" title="Edit Account Types">
                            <a  href="#" class="icon-pencil"></a></button>

                            <button type="button" class="saveacnttypesbutton" style="display:none" title="Save Account Type" value="'.$data['type_id'].'" id="saveacnttypesbutton'.$data['type_id'].'">
                            <a  href="#" class="icon-save"></a></button>

                            <button type="button" title="Account Heads" class="accountheads" value="'.$data['type_id'].'" id="accountheads'.$data['type_id'].'">
                            <a title="Account Heads" href="#" class="icon-list"></a></button>

  
                            <button type="button" title="Delete Account Type" class="  deletecnttypesbutton" value="'.$data['type_id'].'" id="deletecnttypesbutton'.$data['type_id'].'">
                            <a title="Delete Account Type" href="#" class="icon-trash1"></a></button>

                        </div>
                    </div>';

            endforeach;
        else:
            $acctype.=' <div class="finance-type-cntnt-wrpr row acnttypes"><div colspan="9" style="text-align: center">No Account Types Found</div></div>';
        endif;
        $arr = array('result' => $acctype,'error'=>'No');
        return json_encode($arr);
    }

    public function actionUpdateaccounttype()
    {
        $model=AccountTypes::findOne($_POST['acnttypeid']);
        if(isset($_POST['name'])):
            $model->name=$_POST['name'];
            $model->save(false);
            $arr=array('result'=>$_POST['name'],'error'=>'No');
            return json_encode($arr);
        endif;
    }

    public function actionDeleteaccounttype()
    {
        $model=AccountTypes::findOne($_POST['acnttypeid']);
        $model->Status=1;
        $model->save(false);
        $arr=array('result'=>$_POST['acnttypeid'],'error'=>'No');
        return json_encode($arr);
    }

    public function actionSearch()
    {
        $connection = \Yii::$app->db;
         
        $sids = $_POST['subgrpid'];
        
        if($res = AccountsSub::findOne($_POST['subgrpid'])){
            $subids = ResourceType::findOne($res->ResourceType_Id);
            if($subids)
                $sids = $subids->accountsub_id;
        }
       
        
        if ($_POST['subgrpid'] != 'none') {
            $sql = "SELECT a.id,a.Status,a.name,a.tds,a.servicetax,a.account_type,a.resource_id,a.schedule,a.favourite,c.name AS subname,a.resource_group FROM accounts_item AS a LEFT JOIN subgroup_accounts AS b on a.id=b.account_id LEFT JOIN accounts_sub AS c on b.subgrp_id=c.id where b.subgrp_id='" . $sids . "' ";
            if ($_POST['accounts'] != '')
                $sql .= "AND a.name LIKE '%" . $_POST['accounts'] . "%' ";
            if ($_POST['type'] != 'none')
                $sql .= "AND a.account_type ='" . $_POST['type'] . "' ";
            $sql .= "AND a.Status=0 AND c.Status=0 GROUP BY a.id ORDER BY a.name ASC";
            //echo $sql;exit;
            $command = $connection->createCommand($sql);
            $dataReader = $command->query();
            $dataProvider = $dataReader->readAll();
        }
        elseif($_POST['type'] != 'none') {
            $sql = "SELECT a.id,a.Status,a.name,a.tds,a.servicetax,a.account_type,a.resource_id,a.schedule,a.favourite,c.name AS subname,a.resource_group FROM accounts_item AS a LEFT JOIN subgroup_accounts AS b  on a.id=b.account_id LEFT JOIN accounts_sub AS c on b.subgrp_id=c.id WHERE a.account_type ='" . $_POST['type'] . "' ";
            if ($_POST['accounts'] != '')
                $sql .= " AND a.name LIKE '%" . $_POST['accounts'] . "%' ";
            $sql .= "AND a.Status=0 AND c.Status=0 GROUP BY a.id ORDER BY a.name ASC";
            //echo $sql;exit;
            $command = $connection->createCommand($sql);
            $dataReader = $command->query();
            $dataProvider = $dataReader->readAll();
            
        }
        else {
            //$sql="SELECT id,name,tds,servicetax,account_type,schedule FROM accounts_item";
            $sql = "SELECT a.id,a.Status,a.name,a.tds,a.servicetax,a.account_type,a.resource_id,a.schedule,a.favourite,c.name AS subname,a.resource_group FROM accounts_item AS a LEFT JOIN subgroup_accounts AS b  on a.id=b.account_id LEFT JOIN accounts_sub AS c on b.subgrp_id=c.id ";
            if ($_POST['accounts'] != ''){
                $sql .= " WHERE a.name LIKE '%" . $_POST['accounts'] . "%' ";
                //$sql .= "AND a.Status=0 AND c.Status=0 GROUP BY a.id ORDER BY a.name ASC";
                $sql .= "AND a.Status=0 GROUP BY a.id ORDER BY a.name ASC";
            }else{
                 //$sql .= "WHERE a.Status=0 AND c.Status=0 GROUP BY a.id ORDER BY a.name ASC";
                 $sql .= "WHERE a.Status=0 GROUP BY a.id ORDER BY a.name ASC";
            }
           
            //echo $sql;exit;
            $command = $connection->createCommand($sql);
            $dataReader = $command->query();
            $dataProvider = $dataReader->readAll();
        }
        $datarows = '';
        $accheads = '';
        if($_POST['type']==1 || $_POST['type']==2){

            $accheads.='
                    <!--<div class="col-md-12 list-head">-->
                        <div class="account-heads-cntnt-wrpr row sbgrp">
                            <div class="col-md-10 col-sm-10">
                                <div class="row">
                                    <div class="col-md-1 col-sm-1 ">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label>#</label> 
                                            </div>
                                            
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4 col-sm-4 type ">
                                        <label>Account Heads</label><br/>
                                        
                                    </div>
                                    
                                    <div class="col-md-3 col-sm-3 type ">
                                        <label>Account Type</label><br/>
                                        
                                    </div>

                                    <div class="col-md-4 col-sm-4 type ">
                                        <label>Opening Balance</label><br/>
                                        
                                    </div>
                                    
                                    
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-2 ">
                            
                                
                            </div>
                        </div>
                    <!--</div>-->';

        }
        else
        {

            $accheads.='
                    <!--<div class="col-md-12 list-head">-->
                        <div class="account-heads-cntnt-wrpr row sbgrp">
                            <div class="col-md-10 col-sm-10">
                                <div class="row">
                                    <div class="col-md-1 col-sm-1 ">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label>#</label> 
                                            </div>
                                            
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4 col-sm-4 type ">
                                        <label>Account Heads</label><br/>
                                        
                                    </div>
                                    
                                    <div class="col-md-4 col-sm-4 type ">
                                        <label>Account Type</label><br/>
                                        
                                    </div>
                                    
                                    
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-2 ">
                            
                                
                            </div>
                        </div>
                    <!--</div>-->';

        }

        $accountheadnames='';

        if (count($dataProvider) > 0):
            foreach ($dataProvider AS $key => $data):

                $accountheadnames.= '<option value="'.$data['name'].'"></option>';

                if ($data['schedule'] == 3) {
                   // $schedule = 'Schedule';
                    $schedules='<a title="Schedule" href="#" class="icon-schedule" ></a>';
                    $checked = 'checked="checked"';
                } else {
                   // $schedule = '';
                    $schedules='';
                    $checked = '';
                }

                if ($data['account_type'] == 8) {
                    $linkbutton ="<button type='button' class='btn btn-primary linkexpresunitbtn' id='linkexpresunitbtn".$data['id']."' data-toggle='modal' data-target='#LinkExpRes' title='Edit Resource Group' value='".$data['id']."'>Link Resource Group</button>";
                } else {
                    $linkbutton = '';
                }

                $editresbtn="<td>
                                <button type='button' class='btn btn-primary linkresunitbtn' id='linkresunitbtn".$data['id']."' data-toggle='modal' data-target='#LinkRes' title='Edit Resource Unit' value='".$data['id']."' >Edit Resource</button>
                                </td>";

                $types=AccountTypes::findOne($data['account_type']); 
                if($data['resource_id']!=0){
                    $resunits=Resources::find($data['resource_id'])->one()->Name; 
                }
                else{
                    $resunits='';
                }
                
                $resourcegrps=ResourceGroup::findOne($data['resource_group']);

                if($_POST['type']==1 || $_POST['type']==2)
                {

                    $ledgerbalance= LedgerOpeningbalance::find()->where(['accountid' => $data['id']])->one();

                    if($ledgerbalance && $ledgerbalance->balance!=0){

                        $ledgeropenblnce1 = abs($ledgerbalance->balance);

                        $ledgeropenblnce = number_format((float)$ledgeropenblnce1, 2);

                        $accounttype = ' ('.$ledgerbalance->type.')';

                    }
                    else{

                        $ledgeropenblnce = '';

                        $accounttype = '';

                    }

                    $accheads.='
                            <div class="list-wrpr" id="accountsrow' . $data['id'] . '">
                                                         
                                <div class="col-md-12 list-content" style="max-height:400px; overflow-y:auto;">
                                    <div class="account-heads-cntnt-wrpr row">
                                        <div class="col-md-10 col-sm-10">
                                            <div class="row">
                                                <div class="col-md-1 col-sm-1 ">
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <label></label>
                                                            <span class="number indent-number">'.($key + 1).'</span>  
                                                            <input type="hidden" value="' . $data['id'] . '">           
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-4 col-sm-4 type ">
                                                    <label></label>
                                                    
                                                    <span id="accountstext' . $data['id'] . '">' . $data['name'] . '</span>

                                                    <input type="hidden" id="accname'.$data['id'].'" value="'.$data['name'].'">

                                                   
                                                </div>
                                                
                                                <div class="col-md-3 col-sm-3 type ">
                                                    <label></label>
                                                    
                                                    <span  id="accountype' . $data['id'] . '">'.$types['name'].'</span>

                                                    <input type="hidden" id="acctype'.$data['id'].'" value="'.$types['name'].'">
                                                    
                                                    <input type="hidden" id="acctds'.$data['id'].'" value="'.$data['tds'].'">

                                                    <input type="hidden" id="accservicetax'.$data['id'].'" value="'.$data['servicetax'].'">                                     

                                                </div>

                                                <div class="col-md-4 col-sm-4 type ">
                                                    <label></label>

                                                    <span  id="ledgerbalance' . $data['id'] . '">'.$ledgeropenblnce.'<span>'.$accounttype.'</span></span>

                                                </div>
                                                                                               
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-2 icon-groups">';
                                        if($data['account_type']==1 || $data['account_type']==2){
                                            if($data['favourite']==0){
                                                $accheads.='<a title="Add to favourites" href="javascript:void(0)" id="favourites'.$data['id'].'" data-id="'.$data['id'].'" data-value="'.$data['favourite'].'" class="btn btn-primary starred add-to-favorite" title="Add to Favorite"><span class="icon-star2"></span></a>';
                                            }elseif($data['favourite']==1){

                                                 $accheads.='<a  title="Remove from favourites" href="javascript:void(0)" id="favourites'.$data['id'].'" data-id="'.$data['id'].'" data-value="'.$data['favourite'].'" class="btn btn-primary add-to-favorite starred added-to-fav" title="Edit Favorite"><span class="icon-star2"></span></a>';
                                            }
                                        }
                                        
                                        $accheads.='<span id="schedule' . $data['id'] . '">'.$schedules.'</span>                                    
                                             
                                            <input type="checkbox" style="visibility: hidden;" value="3" $checked id="schedulecheck'.$data['id'].'">                                  

                                            <button type="button" class="editaccountsbutton" value="'.$data['id'].'" id="editaccountsbutton'.$data['id'].'"> <a title="Edit Account Heads" href="#" class="icon-pencil"></a></button>

                                            <button type="button"  class="deleteaccountsbutton" value="' . $data['id'] . '" id="deleteaccountsbutton' . $data['id'] . '"><a title="Delete Account Heads" href="#" class="icon-trash1"></a> </button>

                                        </div>
                                    </div>
                                                       
                                </div>
                            </div>';

                }
                else
                {

                    $accheads.='
                            <div class="list-wrpr" id="accountsrow' . $data['id'] . '">
                                                         
                                <div class="col-md-12 list-content" style="max-height:400px; overflow-y:auto;">
                                    <div class="account-heads-cntnt-wrpr row">
                                        <div class="col-md-10 col-sm-10">
                                            <div class="row">
                                                <div class="col-md-1 col-sm-1 ">
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <label></label>
                                                            <span class="number indent-number">'.($key + 1).'</span>  
                                                            <input type="hidden" value="' . $data['id'] . '">           
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-4 col-sm-4 type ">
                                                    <label></label>
                                                    
                                                    <span id="accountstext' . $data['id'] . '">' . $data['name'] . '</span>

                                                    <input type="hidden" id="accname'.$data['id'].'" value="'.$data['name'].'">

                                                   
                                                </div>
                                                
                                                <div class="col-md-4 col-sm-4 type ">
                                                    <label></label>
                                                    
                                                    <span  id="accountype' . $data['id'] . '">'.$types['name'].'</span>

                                                    <input type="hidden" id="acctype'.$data['id'].'" value="'.$types['name'].'">
                                                    
                                                    <input type="hidden" id="acctds'.$data['id'].'" value="'.$data['tds'].'">

                                                    <input type="hidden" id="accservicetax'.$data['id'].'" value="'.$data['servicetax'].'">                                     

                                                </div>
                                                                                               
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-2 icon-groups">';
                                        if($data['account_type']==1 || $data['account_type']==2){
                                            if($data['favourite']==0){
                                                $accheads.='<a title="Add to favourites" href="javascript:void(0)" id="favourites'.$data['id'].'" data-id="'.$data['id'].'" data-value="'.$data['favourite'].'" class="btn btn-primary starred add-to-favorite" title="Add to Favorite"><span class="icon-star2"></span></a>';
                                            }elseif($data['favourite']==1){

                                                 $accheads.='<a  title="Remove from favourites" href="javascript:void(0)" id="favourites'.$data['id'].'" data-id="'.$data['id'].'" data-value="'.$data['favourite'].'" class="btn btn-primary add-to-favorite starred added-to-fav" title="Edit Favorite"><span class="icon-star2"></span></a>';
                                            }
                                        }

                                        if($data['account_type']==8){

                                            //$scheduleitems = '<button type="button" class="editschedule_item" value="'.$data['id'].'" id="editschedule_item'.$data['id'].'"><a title="Schedule Items" href="#" class="account-subgroup-btn">Schedule Items</a></button>';
                                            $scheduleitems = '<button type="button" class="editschedule_item" value="'.$data['id'].'" id="editschedule_item'.$data['id'].'"><a title="Account Subgroups" href="#" class="account-subgroup-btn">Account Subgroups</a></button>';

                                        }
                                        else{

                                            $scheduleitems = '
                                                <button type="button" class="editaccountsbutton" value="'.$data['id'].'" id="editaccountsbutton'.$data['id'].'"> <a title="Edit Account Heads" href="#" class="icon-pencil"></a>
                                                </button>';

                                        }
                                        
                                        $accheads.='<span id="schedule' . $data['id'] . '">'.$schedules.'</span>                                    
                                             
                                            <input type="checkbox" style="visibility: hidden;" value="3" $checked id="schedulecheck'.$data['id'].'">  

                                            '.$scheduleitems.'                              

                                            <!--<button type="button" class="editaccountsbutton" value="'.$data['id'].'" id="editaccountsbutton'.$data['id'].'"> <a title="Edit Account Heads" href="#" class="icon-pencil"></a></button>-->

                                            <button type="button"  class="deleteaccountsbutton" value="' . $data['id'] . '" id="deleteaccountsbutton' . $data['id'] . '"><a title="Delete Account Heads" href="#" class="icon-trash1"></a> </button>

                                        </div>
                                    </div>
                                                       
                                </div>
                            </div>';

                }

            endforeach;
            $data_type = $data['account_type'];
        else:
            $data_type = '';
            $accheads='<div class="account-heads-cntnt-wrpr row"><div colspan="9" style="text-align: center">No Accounts Found</div></div>';
        endif;
        $arr = array('result' => $accheads,'accountheadnames'=>$accountheadnames,'type'=>$data_type, 'error' => 'No');
        return json_encode($arr);
    }
    public function actionFavouriteaccnt()
    {
        $connection = \Yii::$app->db;
        if($_POST['val']==0)
        {
            $status=1;
        }
        else
        {
             $status=0; 
        }

            $sql = "UPDATE accounts_item SET favourite='".$status."' WHERE id='".$_POST['id']."'";

            $command = $connection->createCommand($sql);
            $dataReader = $command->query();

            $arr = array('error' => 'No');
            return json_encode($arr);


    }

    public function actionUpdateaccountnew()
    {
        $id=$_POST['accountheadid'];
        $model=AccountsItem::findOne($id); 

        $datarows='';
        $sched='';
        $checkcondition='';
        $subvalues2='';
        $condition='';
        $condition2='';
        $checkconditions=$model->id;

        $sched.=$model['schedule']=='3'?'checked="checked"':'';                                    

        $datarows.='
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Account Type</label>
                        <select id="accounttype'. $checkconditions . '" name="accounttype" class="form-control" >
                        <option value="">Select Account Type</option>';  
               
                        $acnttypes=AccountTypes::find()->where(['Status'=>0])->andWhere(['NOT LIKE', 'name', 'Expense'])->all();

                        foreach($acnttypes AS $acnttype):
                            if($model->account_type == $acnttype->type_id):
                                $selected="selected";
                            else:
                                $selected='';
                            endif;
                           $datarows.="<option value='".$acnttype->type_id."' $selected id='acnttype'>".$acnttype->name."</option>";
                        endforeach;

        $ledgerbalance= LedgerOpeningbalance::find()->where(['accountid' => $model['id']])->one();

        if($ledgerbalance){
            $ledgeropenblnce = abs($ledgerbalance->balance);
            if($ledgerbalance->type=='Debit')
            {
                $select1 = 'selected';
                $select2 = '';
            }
            else{
                $select1 = '';
                $select2 = 'selected';
            }
        }
        else{
            $ledgeropenblnce = '';
            $select1 = '';
            $select2 = '';
        }

        if($model->account_type==1 || $model->account_type==2)
        {

            $datarows.= '   </select>     
                        <span class="error" style="display: none;"></span>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Account Name</label>
                        <input class="form-control" type="text" id="accountsname'. $checkconditions . '" name="accountsname"
                       placeholder="Accounts Name" value="'.$model->name.'">
                        <span class="error" style="display: none;"></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>TDS(%)</label>
                                <input class="form-control" type="text" id="accounttds'. $checkconditions . '" name="accounttds"
                                       placeholder="TDS(%)" value="'.$model->tds.'">
                                <span class="error" style="display: none;"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>GST(%)</label>
                                <input class="form-control"  type="text" id="accountservtax'. $checkconditions . '" name="accountservtax"
                              placeholder="Service Tax(%)" value="'.$model->servicetax.'">
                                <span class="error" style="display: none;"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="text-center">Schedule</label>
                                <input type="checkbox" class="form-control" style="visibility: visible;height:20px;box-shadow: none;" 
                                      '.$sched.' value="3" id="schedule'. $checkconditions . '" name="schedule" style="visibility: visible;">
                                <span class="error" style="display: none;"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Opening Balance</label>
                                <input class="form-control"  type="text" id="accountopeningblnce'. $checkconditions . '" name="accountopeningblnce" value="'.$ledgeropenblnce.'">
                                <span class="error" style="display: none;"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Type</label>
                                <select class="form-control" id="balancetype" name="balancetype">
                                    <option value="">Select Type</option>
                                    <option value="Debit" '.$select1.'>Debit</option>
                                    <option value="Credit" '.$select2.'>Credit</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6"></div>

                <div class="col-md-1"></div>
                <div class="col-md-12">
                    <hr class="customHr" />
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th><span class="headings">Account Groups</span></th>
                                <th><span class="headings">Account Sub-Groups</span></th>
                                <th><span class="headings">Schedule Item</span></th>
                                <!--<th><span class="headings">&nbsp;</span></th>-->
                            </tr>';
        }
        elseif($model->account_type!=1 && $model->account_type!=2 && $model->account_type!=8){

            $datarows.= '   </select>     
                        <span class="error" style="display: none;"></span>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Account Name</label>
                        <input class="form-control" type="text" id="accountsname'. $checkconditions . '" name="accountsname"
                       placeholder="Accounts Name" value="'.$model->name.'">
                        <span class="error" style="display: none;"></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>TDS(%)</label>
                                <input class="form-control" type="text" id="accounttds'. $checkconditions . '" name="accounttds"
                                       placeholder="TDS(%)" value="'.$model->tds.'">
                                <span class="error" style="display: none;"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>GST(%)</label>
                                <input class="form-control"  type="text" id="accountservtax'. $checkconditions . '" name="accountservtax"
                              placeholder="Service Tax(%)" value="'.$model->servicetax.'">
                                <span class="error" style="display: none;"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="text-center">Schedule</label>
                                <input type="checkbox" class="form-control" style="visibility: visible;height:20px;box-shadow: none;" 
                                      '.$sched.' value="3" id="schedule'. $checkconditions . '" name="schedule" style="visibility: visible;">
                                <span class="error" style="display: none;"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Opening Balance</label>
                                <input class="form-control"  type="text" id="accountopeningblnce'. $checkconditions . '" name="accountopeningblnce" value="'.$ledgeropenblnce.'">
                                <span class="error" style="display: none;"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Type</label>
                                <select class="form-control" id="balancetype" name="balancetype">
                                    <option value="">Select Type</option>
                                    <option value="Debit" '.$select1.'>Debit</option>
                                    <option value="Credit" '.$select2.'>Credit</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6"></div>
                <div class="col-md-1"></div>
                <div class="col-md-12">
                    <hr class="customHr" />
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th><span class="headings">Account Groups</span></th>
                                <th><span class="headings">Account Sub-Groups</span></th>
                                <th><span class="headings">Schedule Item</span></th>
                                <!--<th><span class="headings">&nbsp;</span></th>-->
                            </tr>';

        }
        else{

            $datarows.= '   </select>     
                        <span class="error" style="display: none;"></span>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Account Name</label>
                        <input class="form-control" type="text" id="accountsname'. $checkconditions . '" name="accountsname"
                       placeholder="Accounts Name" value="'.$model->name.'">
                        <span class="error" style="display: none;"></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>TDS(%)</label>
                                <input class="form-control" type="text" id="accounttds'. $checkconditions . '" name="accounttds"
                                       placeholder="TDS(%)" value="'.$model->tds.'">
                                <span class="error" style="display: none;"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>GST(%)</label>
                                <input class="form-control"  type="text" id="accountservtax'. $checkconditions . '" name="accountservtax"
                              placeholder="Service Tax(%)" value="'.$model->servicetax.'">
                                <span class="error" style="display: none;"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="text-center">Schedule</label>
                                <input type="checkbox" class="form-control" style="visibility: visible;height:20px;box-shadow: none;" 
                                      '.$sched.' value="3" id="schedule'. $checkconditions . '" name="schedule" style="visibility: visible;">
                                <span class="error" style="display: none;"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-1"></div>
                <div class="col-md-12">
                    <hr class="customHr" />
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th><span class="headings">Account Groups</span></th>
                                <th><span class="headings">Account Sub-Groups</span></th>
                                <th><span class="headings">Schedule Item</span></th>
                                <!--<th><span class="headings">&nbsp;</span></th>-->
                            </tr>';

        }
                                        
        $groups=Accountsmaster::find()->where(['Status'=>0])->andWhere(['NOT LIKE', 'name', 'Project Expenditure'])->andWhere(['NOT LIKE', 'name', 'Corporate Expenditure'])->all();

        foreach($groups AS $group):
            
            $check=$this->GetCheckedvalues($group->id,$checkconditions);

            $check2=$check['count']>0?'checked':'';

            if($check2==1)
            {

                $datarows.=' 
                            <tr>
                                <td>
                                    <input type="checkbox"  style="visibility: visible;" class="form-control accountgroup selectcheckbox" data-id="'.$group->id.'" name="accountgroup[]" value="'.$group->id.'"  '.$check2.'/> '.$group->name.'  
                                </td>
                                <td>                                                    
                                    <select id="account_subgrps'.$group->id.'" data-id="'.$group->id.'" name="accountsubgrps'.$group->id.'" class="form-control accountsubgrps">

                                    <option value="">Select Account Sub-Groups</option>';
            } 
            else
            {
               $datarows.=' <tr>
                                <td>                            
                                  <input type="checkbox"  style="visibility: visible;" class="form-control accountgroup selectcheckbox" data-id="'.$group->id.'" name="accountgroup[]" value="'.$group->id.'"  '.$check2.'/> '.$group->name.'  

                                </td>
                                <td>                                                    
                                    <select id="account_subgrps'.$group->id.'" data-id="'.$group->id.'" name="accountsubgrps'.$group->id.'" class="form-control accountsubgrps">
                                           
                                       
                                       <option value="">Select Account Sub-Groups</option>';
            }                                              
            
            if($check['subgroup']!=0):
                $code = AccountsSub::find()->where(['Status'=>0])->andWhere(['master_id'=>$group->id])->all();
                $data = ArrayHelper::map($code, 'id', 'name');

                foreach($data as $value=>$name):  
                    if($check['subgroup']==$value):
                        $selected="selected"; 
                    else:
                        $selected="";
                    endif;                                                              
                                              
                    $datarows.= '<option value="'.$value.'" '.$selected.'>'.$name.'</option>';
                endforeach;
            endif;              
                                              
                    $datarows.=' </select>  
                                </td>';

            $subgroupaccts = SubgroupAccounts::find()->where(['account_id' => $id])->one();
            
            if($group->id==6 && $subgroupaccts && $subgroupaccts->group_id==6):
                $datarows.=' <td>
                        <select id="bs_items'.$group->id.'" data-id="'.$group->id.'" name="bsitems'.$group->id.'" class="form-control bsitems">
                          <option value="0">Select Schedule Item</option>';

                $selectedbsitem = Bsitems::findOne($subgroupaccts->bsitem_id);

                $bsitems = Bsitems::find()->where(['status'=> 0])->all();

                foreach($bsitems as $bsitem):  
                    if($selectedbsitem && $selectedbsitem->item_id==$bsitem->item_id):
                        $selected="selected"; 
                    else:
                        $selected="";
                    endif;                                                              
                                              
                    $datarows.= '<option value="'.$bsitem->item_id.'" '.$selected.'>'.$bsitem->itemname.'</option>';
                endforeach;

                $datarows.='</select>
                      </td>';

            else:

                $datarows.=' <td>
                        <select id="bs_items'.$group->id.'" data-id="'.$group->id.'" name="bsitems'.$group->id.'" class="form-control bsitems" style=" display: none;">
                          <option value="0">Select Schedule Item</option>';

                if($subgroupaccts){
                    $selectedbsitem = Bsitems::findOne($subgroupaccts->bsitem_id);
                }
                else{
                    $selectedbsitem ='';
                }

                $bsitems = Bsitems::find()->where(['status'=> 0])->all();

                foreach($bsitems as $bsitem):  
                    if($selectedbsitem && $selectedbsitem->item_id==$bsitem->item_id):
                        $selected="selected"; 
                    else:
                        $selected="";
                    endif;                                                              
                                              
                    $datarows.= '<option value="'.$bsitem->item_id.'" '.$selected.'>'.$bsitem->itemname.'</option>';
                endforeach;

                $datarows.='</select>
                      </td>';

            endif;
            
            $datarows.='</tr>';
                                               
                                                 
        endforeach;     

        $datarows.='    </tbody>                                    
                    </table>                                    
                </div>
                <div class="col-md-1"></div>
                <div class="col-md-12 buttonCstmPos">
                                
                    <div class="text-center">
                        <label>&nbsp;</label>
                        <input type="hidden"name="accountheadid" id="accountheadid" value="'.$id.'" />
                        <button type="button" class="btn btn-danger cancel"  id="cancel"><span class="icon-close"></span> Cancel</button>
                        <button type="button" class="btn btn-primary saveaccountheadss" value="'.$checkconditions.'"  id="saveaccountheadss'.$checkconditions .'"><span class="icon-check"></span> Edit Account Head</button>
                    </div>
                </div>';

        $arr = array('result' => $datarows, 'error' => 'No');
        return json_encode($arr);                     
      
    }

    public function actionUpdateaccount()
    {
        //echo 'hai'; exit;
        $id=$_POST['accountheadid'];
        $model=AccountsItem::findOne($id); 
        $connection = \Yii::$app->db; 
        if (isset($_POST['accountsname'])):
            $model->name = str_replace("'", "", $_POST['accountsname']);
            if(isset($_POST['accounttds'])):
                $model->tds = $_POST['accounttds'];
            endif;
            if(isset($_POST['accountservtax'])):
                $model->servicetax = $_POST['accountservtax'];
            endif;
             if(isset($_POST['accountigst'])):
                $model->igst = $_POST['accountigst'];
            endif;
            if(isset($_POST['accounttype']) && $_POST['accounttype']!=''):
                $model->account_type = $_POST['accounttype'];
            endif;
            if(isset($_POST['schedule'])):
                $model->schedule = $_POST['schedule'];
            endif;
            //$groups=isset($_POST['accountgroup']);  
            if(isset($_POST['accountgroup'])){
                $groups=$_POST['accountgroup']; 
            } 
            else{
                $groups=0;
            }                  
            $subgroups=isset($_POST['subgroups']);
            if($model->save(false)):
                if(isset($_POST['accountopeningblnce'])){

                    if($_POST['accountopeningblnce']!=''){

                        $ledgerbalance= LedgerOpeningbalance::find()->where(['accountid' => $model->id])->one();

                        if($_POST['balancetype']=='Debit')
                        {

                            $ledger_balance = '-'.$_POST['accountopeningblnce'];

                            $ledger_type = $_POST['balancetype'];

                        }
                        else{

                            $ledger_balance = $_POST['accountopeningblnce'];
                            $ledger_type = 'Credit';

                        }

                        if($ledgerbalance)
                        {
                            $ledgerbalance->balance = $ledger_balance;
                            $ledgerbalance->type = $ledger_type;
                            $ledgerbalance->save(false);
                        }
                        else
                        {
                            $ledgerbalance = new LedgerOpeningbalance();
                            $ledgerbalance->projectid = 12;
                            $ledgerbalance->accountid = $model->id;
                            $ledgerbalance->balance = $ledger_balance;
                            $ledgerbalance->type = $ledger_type;
                            $ledgerbalance->save(false);

                        }
                    }
                }

                //$sql = "DELETE FROM subgroup_accounts WHERE account_id='" . $model->id . "'";
                $sql = "DELETE FROM subgroup_accounts WHERE account_id='" . $model->id . "' AND group_id!=1";
                ///echo $sql;exit;
                $command = $connection->createCommand($sql);            
                $dataReader=$command->query();
                //echo count($groups);exit;
                if($groups!=0){
                    for($i=0;$i<count($groups);$i++)
                    {
                        if(isset($_POST['bsitems'.$groups[$i]]))
                        {
                            $bsitems=$_POST['bsitems'.$groups[$i]];
                        }
                        else
                        {
                            $bsitems=0;
                        }

                        //echo $groups[$i]; exit;
                   
                        $sql="INSERT INTO subgroup_accounts (group_id,subgrp_id,accountschedule_id,account_id,bsitem_id) VALUES ('".$groups[$i]."','".$_POST['accountsubgrps'.$groups[$i]]."','','".$model->id."','".$bsitems."')";
                        $command=$connection->createCommand($sql);
                        $dataReader=$command->query();
                    }
                }
            endif;                        

            $arr = array('Id' => $model->id,'error'=>'No');
            return json_encode($arr);
        else:
            $arr = array('id' => '', 'Name' => '','error'=>'Yes','errortext'=>'Cannot add now. Please try again later');
            return json_encode($arr);
        endif;

    }

    public function actionUpdate()
    {
        if(isset($_POST['accountid']))
        {
            $model=$this->findModel($_POST['accountid']);
            $model->id=$_POST['accountid'];
            $model->name=$_POST['accountname'];
            $model->tds=$_POST['accounttds'];
            $model->servicetax=$_POST['servicetax'];
            $model->account_type=$_POST['account_type'];
            $model->schedule=$_POST['schedule'];
            $model->sub_id=$_POST['acntsubgrp'];
            $acntgrp=AccountsSub::findOne($model->sub_id); 

            $model->master_id=$acntgrp->master_id;
            if($model->save(false)):
                if($model->account_type==1){
                    $type='Cash';
                }
                elseif($model->account_type==2){
                    $type='Bank';
                }
                elseif($model->account_type==4)
                {
                    $type='Income';
                }
                elseif($model->account_type==5)
                {
                    $type='Expense';
                }
                elseif($model->account_type==6)
                {
                    $type='Asset';
                }
                elseif($model->account_type==7)
                {
                    $type='Liability';
                }
                if($model->schedule==3)
                {
                    $scheduletype='Schedule';
                }
                else
                {
                    $scheduletype='';
                }

                $arr = array('Id' => $_POST['accountid'], 'Name' => $_POST['accountname'],'TDS' => $_POST['accounttds'],'type'=>$type,'scheduletype'=>$scheduletype,'Account Subgroup'=>$model->sub_id,'Account group'=>$model->master_id,'error'=>'No');
                return json_encode($arr);
            else:
                $arr = array('Id' => $_POST['accountid'],'error'=>'Yes','errortext'=>'Can not update now.');
                return json_encode($arr);
            endif;
        }
    }

    public function actionResourcetype()
    {
        $resourcetype=AccountsSub::findOne($_POST['accountsubgrp']); 
        $resourcetypelist='<option value="0">Select Resource Group</option>';
        $restype= $resourcetype['ResourceType_Id'];

        $grouplists=ResourceGroup::find()->Where(['ResourceType_Id'=>$restype])->all();

        foreach($grouplists AS $grouplist):
            $resourcetypelist.="<option value='".$grouplist->Resource_group_Id."'>".$grouplist->Resource_group_Name."</option>";
        endforeach;
        $arr=array('result'=>$resourcetypelist,'error'=>'No');
        return json_encode($arr);
    }

    public function actionDeleteitem()
    {
        if(isset($_POST['accountid'])):
          //  $connection = CActiveRecord::getDbConnection();
            $connection = \Yii::$app->db; 
            $sql="(SELECT id,voucher_no FROM voucher WHERE (account_id='".$_POST['accountid']."' OR creditacnt='".$_POST['accountid']."') )
                  UNION
                  (SELECT id,voucher_no FROM journalvoucher WHERE (debitacnt='".$_POST['accountid']."' OR creditacnt='".$_POST['accountid']."') )";
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $dataProvider=$dataReader->readAll();
            if(count($dataProvider)>0):
                $arr = array('Id' => $_POST['accountid'],'error'=>'Yes','errortext'=>'Can not Delete this accounthead as there is a transaction');
                return json_encode($arr);
            else:
                $model = $this->findModel($_POST['accountid']);
                $model->Status = 1;
                $model->save(false);
                $arr = array('Id' => $_POST['accountid'],'error'=>'No');
                return json_encode($arr);
            endif;
        endif;
    }

    public function GetCheckedvalues($group,$account)
    {  
        $connection = \Yii::$app->db; 
        $sql = "SELECT COUNT(id) AS total FROM subgroup_accounts WHERE group_id='".$group."'AND account_id='" .$account. "'";
        $command = $connection->createCommand($sql);
        $dataReader = $command->query();
        $count = $dataReader->read(); 

        $sql = "SELECT group_id,subgrp_id,accountschedule_id,bsitem_id FROM subgroup_accounts WHERE group_id='".$group."'AND account_id='" .$account. "'";
        $command = $connection->createCommand($sql);
        $dataReader = $command->query();
        $datasub = $dataReader->read();  

        return array('count'=>$count['total'],'subgroup'=>$datasub['subgrp_id'],'bsitem'=>$datasub['bsitem_id'],'schedule'=>$datasub['accountschedule_id']);
            
    } 

    public function actionGetsubgroups()
    {
        $connection = \Yii::$app->db; 
        $sql = "SELECT COUNT(id) AS total FROM subgroup_accounts WHERE group_id='7' AND account_id='" .$_POST['accountid']. "'";
        $command = $connection->createCommand($sql);
        $dataReader = $command->query();
        $count = $dataReader->read();
        $sql = "SELECT group_id,subgrp_id,accountschedule_id,bsitem_id FROM subgroup_accounts WHERE group_id='7' AND account_id='" .$_POST['accountid']. "'";
        $command = $connection->createCommand($sql);
        $dataReader = $command->query();
        $datasub = $dataReader->read();
        if($datasub['subgrp_id']!=0):

            $code = AccountsSub::find()->where(['master_id'=>7])->all();
            $data = ArrayHelper::map($code, 'id', 'name');

            $html='';
            $acntsubgrps='';
            foreach($data as $value=>$name):
                if($datasub['subgrp_id']==$value):
                     $selected="selected"; 
                else:
                    $selected="";
                endif;
            endforeach;
        endif;
        $html='
            <tr>
                <td><input type="checkbox" class="accountgroup" style="visibility: visible;" '.($count['total']>0?'Checked':'').' data-id="7" name="accountgroup" value="7"/> Cash Flow</td>
                <td><select id="accountsubgrps7" data-id="7" name="accountsubgrps" class="form-control accountsubgrps">';
        $code = AccountsSub::find()->where(['master_id'=>7])->all();
        $data = ArrayHelper::map($code, 'id', 'name');

        $acntsubgrps='';

        foreach($data as $value=>$name):
            if($datasub['subgrp_id']==$value):
                 $selected="selected"; 
            else:
                $selected="";
            endif;
            $acntsubgrps='<option value="'.$value.'" '.$selected.'>'.$name.'</option>';
        endforeach;

        $html=' <option value="">Select Account Sub-Groups</option>'.$acntsubgrps.'</select>
                </td>
           </tr>';

        $arr=array('result'=>$html,'error'=>'No');
        return json_encode($arr);
    }

    /* Action Status Active End*/

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	/*public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['AccountsItem']))
		{
			$model->attributes=$_POST['AccountsItem'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}*/

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('AccountsItem');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new AccountsItem('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['AccountsItem']))
			$model->attributes=$_GET['AccountsItem'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return AccountsItem the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=AccountsItem::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}


    public function findModel($id)
    {
         $model=AccountsItem::findOne($id);

        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }

	/**
	 * Performs the AJAX validation.
	 * @param AccountsItem $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='accounts-item-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

    public function actionUpdateacntitem()
    {
        $model = AccountsItem::model()->findByPk($_POST['acntitemid']);
        $model->resource_id=$_POST['resid'];
        $model->save();
        $resource=Resources::model()->findByPk($_POST['resid']);
        $resource->Resource_Acc_Id=$_POST['acntitemid'];
        $resource->save(false);
        $arr=array('error'=>'No','itemid'=>$_POST['acntitemid'],'resname'=>$resource['Name']);
        echo json_encode($arr);

    }

    public function actionUpdatesubgroups()
    {
        if($_POST['accountgroup']==7):
            $subacnts=SubgroupAccounts::model()->find(array('condition'=>'group_id=7 AND account_id='.$_POST['account_id'].' '));
            if(count($subacnts)>0):
                $subacnts->subgrp_id=$_POST['accountsubgrps'];
                $subacnts->save(false);
            else:
                $subgrp=New SubgroupAccounts();
                $subgrp->group_id=7;
                $subgrp->subgrp_id=$_POST['accountsubgrps'];
                $subgrp->account_id=$_POST['account_id'];
                $subgrp->save(false);
            endif;
            echo "success";
        else:
            $subacnts=SubgroupAccounts::model()->deleteAll(array('condition'=>'group_id=7 AND account_id='.$_POST['account_id'].' '));
            echo "success";
        endif;

    }

    public function actionGetacntresitem()
    {
        $subgroupacct = SubgroupAccounts::model()->find(array('condition'=>'account_id="'.$_POST['account_id'].'" AND group_id=1'));
        $acnttypes= AccountsSub::model()->findAll(array('condition'=>'master_id=1 ORDER BY sortorder ASC,id DESC'));
        $accval='<option value="0">Select Account Subgroup</option>';
        if(count($acnttypes)>0):
            foreach ($acnttypes AS $acnttype):
                if ($subgroupacct->subgrp_id == $acnttype->id):
                    $seletedtype = 'selected';
                else:
                    $seletedtype = '';
                endif;
                $accval .= "<option value='" . $acnttype->id . "' " . $seletedtype . ">" . $acnttype->name . "</option>";
            endforeach;
        endif;
        $resourcetype = AccountsSub::model()->findByPk($subgroupacct->subgrp_id);
        $resourcegroup=AccountsItem::model()->findByPk($_POST['account_id']);
        //echo $resourcetype;exit;
        if($resourcetype!=''):
            $grouplists = ResourceGroup::model()->findAll(array('condition'=>'ResourceType_Id='.$resourcetype->ResourceType_Id.' '));
            $resourcetypelist='<option value="0">Select Resource Group</option>';
            if(count($grouplists)>0):
                foreach($grouplists AS $grouplist):
                    if ($resourcegroup->resource_group == $grouplist->Resource_group_Id):
                        $seletedtype = 'selected';
                    else:
                        $seletedtype = '';
                    endif;
                    $resourcetypelist.="<option value='".$grouplist->Resource_group_Id."' ".$seletedtype." >".$grouplist->Resource_group_Name."</option>";
                endforeach;
            endif;
        else:
            $resourcetypelist='<option value="0">Select Resource Group</option>';
        endif;
        $criteria = new CDbCriteria;
        $criteria->condition = 'Resource_group_Id=' . $resourcegroup->resource_group . ' AND pricing_status=0 AND Status=0 group by Name';
        $resources = Resources::model()->findAll($criteria);
        $grouparray=array();
        if (count($resources) > 0):
            //$groupsjson .='{"id":0, "name":"All"}';
            foreach ($resources AS $key => $resource):
                $grouparray[]=array("id"=>$resource->Resource_Id,"name"=>$resource->Name);
            endforeach;
        endif;
        $resourceitems=Resources::model()->findAll(array('condition'=>'Resource_group_Id ='.$resourcegroup->resource_group.' AND Resource_Acc_Id='.$_POST['account_id'].' AND pricing_status=0 AND Status=0 group by Name'));
        $grpids=array();
        foreach($resourceitems AS $key=>$resourceitem):
            array_push($grpids,$resourceitem['Resource_Id']);
        endforeach;
        $arr=array('accval'=>$accval,'resourcetypelist'=>$resourcetypelist,'grouparray'=>$grouparray,'grpids'=>$grpids,'accname'=>$resourcegroup->name,'error'=>'No');
        echo json_encode($arr);

    }

    public function actionGetresourcegroup()
    {
        $resourcetype = AccountsSub::model()->findByPk($_POST['subgroup']);
        $grouplists = ResourceGroup::model()->findAll(array('condition'=>'ResourceType_Id='.$resourcetype->ResourceType_Id.' '));
        $resourcetypelist='<option value="0">Select Resource Group</option>';
        if(count($grouplists)>0):
            foreach($grouplists AS $grouplist):
                $resourcetypelist.="<option value='".$grouplist->Resource_group_Id."'>".$grouplist->Resource_group_Name."</option>";
            endforeach;
        endif;
        $arr=array('resourcetypelist'=>$resourcetypelist,'error'=>'No');
        echo json_encode($arr);
    }

    public function actionResources()
    {
        $criteria = new CDbCriteria;
        $criteria->condition = 'Resource_group_Id=' . $_POST['groupid'] . ' AND pricing_status=0 AND Status=0 group by Name';
        $resources = Resources::model()->findAll($criteria);
        $grouparray=array();
        if (count($resources) > 0):
            //$groupsjson .='{"id":0, "name":"All"}';
            foreach ($resources AS $key => $resource):
                $grouparray[]=array("id"=>$resource->Resource_Id,"name"=>$resource->Name);
            endforeach;
        endif;
        echo json_encode($grouparray);
    }
    public function actionUpdateresitem()
    {
        $connection = CActiveRecord::getDbConnection();
        if($_POST['accountsubgrp']!='0' && $_POST['accountsubgrp']!=''):
            $sql = "SELECT COUNT(id) AS total FROM subgroup_accounts WHERE group_id='1' AND account_id='" .$_POST['account_id']. "'";
            $command = $connection->createCommand($sql);
            $dataReader = $command->query();
            $count = $dataReader->read();
            if($count['total']==0):
                $sql1="INSERT INTO subgroup_accounts (group_id,subgrp_id,account_id) VALUES ('1','".$_POST['accountsubgrp']."','".$_POST['account_id']."')";
                $command=$connection->createCommand($sql1);
                $dataReader=$command->query();
            else:
                $sql1="UPDATE subgroup_accounts SET subgrp_id='".$_POST['accountsubgrp']."' WHERE group_id='1' AND account_id='" .$_POST['account_id']. "' ";
                $command=$connection->createCommand($sql1);
                $dataReader=$command->query();
            endif;
        endif;
        $acntmodel=AccountsItem::model()->findByPk($_POST['account_id']);
        $acntmodel->resource_group=$_POST['resourcegroup'];
        $acntmodel->save(false);
        $resources=$_POST['resources'];
        //echo count($resources);exit;
        if (count($resources)>0){
            for($i=0;$i<count($resources);$i++)
            {
                $resource=Resources::model()->findByPk($resources[$i]);
                $allresources=Resources::model()->findAll(array('condition'=>'Name LIKE "%'.$resource->Name.'%" AND Resource_group_Id='.$resource->Resource_group_Id.' AND Status=0 AND pricing_status=0','order'=>'Resource_Id ASC'));
                foreach($allresources as $allresource):
                    $allresource->Resource_Acc_Id=$_POST['account_id'];
                    $allresource->save(false);
                endforeach;
            }
        }
        echo "success";
    }

    public function actionAccountSubgroups()
    {
        $connection = CActiveRecord::getDbConnection();
        for($i=0;$i<count($_POST['grpid']);$i++)
        {
            $sql="SELECT name FROM accountsmaster where id='".$_POST['grpid'][$i]."' ";
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $group=$dataReader->read();
            $options.="<option value='0'>Select Account Sub Groups</option>";
            $options.="<optgroup label='".$group['name']."'>";
            $sql1="SELECT id,name FROM accounts_sub WHERE master_id='".$_POST['grpid'][$i]."' ORDER BY sortorder ASC ";
            $command=$connection->createCommand($sql1);
            $dataReader=$command->query();
            $dataval=$dataReader->readAll();
            foreach($dataval AS $data):
                $options.="<option value=".$data['id'].">".$data['name']."</option>";
            endforeach;
            $options.="</optgroup>";
        }
        $arr = array('result' => $options,'error'=>'No');
        echo json_encode($arr);
    }

    public function actionExpensesearch()
    {
        $connection = CActiveRecord::getDbConnection();
        if ($_POST['subgrpid'] != 'none') {
            $sql = "SELECT a.id,a.name,a.tds,a.servicetax,a.account_type,a.resource_id,a.schedule,a.resource_group,c.name AS subname FROM accounts_item AS a LEFT JOIN subgroup_accounts AS b on a.id=b.account_id LEFT JOIN accounts_sub AS c on b.subgrp_id=c.id where a.account_type=8 AND b.subgrp_id='" . $_POST['subgrpid'] . "' ";
            if ($_POST['accounts'] != '')
                $sql .= "AND a.name LIKE '%" . $_POST['accounts'] . "%' ";
            $sql .= " GROUP BY a.id ORDER BY a.name ASC";
            //echo $sql;exit;
            $command = $connection->createCommand($sql);
            $dataReader = $command->query();
            $dataProvider = $dataReader->readAll();
        }
        else {
            $sql = "SELECT a.id,a.name,a.tds,a.servicetax,a.account_type,a.resource_id,a.schedule,a.resource_group,c.name AS subname FROM accounts_item AS a LEFT JOIN subgroup_accounts AS b on a.id=b.account_id LEFT JOIN accounts_sub AS c on b.subgrp_id=c.id WHERE a.account_type=8";
            if ($_POST['accounts'] != '')
                $sql .= " AND a.name LIKE '%" . $_POST['accounts'] . "%' ";
            $sql .= " GROUP BY a.id ORDER BY a.name ASC";
            //echo $sql;exit;
            $command = $connection->createCommand($sql);
            $dataReader = $command->query();
            $dataProvider = $dataReader->readAll();
        }
        $datarows = '';
        if (count($dataProvider) > 0):
            foreach ($dataProvider AS $key => $data):
                $subgroupacct = SubgroupAccounts::model()->find(array('condition'=>'account_id="'.$data['id'].'" AND group_id=1'));
                $acntsubgrp= AccountsSub::model()->findByPk($subgroupacct->subgrp_id);
                $resourcegrp= ResourceGroup::model()->findByPk($data['resource_group']);
                $datarows .= "<tr id='expaccountsrow" . $data['id'] . "' class='accounts" . $data['id'] . "'>
                            <td  class='small75'>".($key + 1)."<input type='hidden' value='" . $data['id'] . "'></td>
                            <td><span id='expaccountstext" . $data['id'] . "'>" . $data['name'] . "</span></td>
                            <td><span id='expacntsubtext".$data['id']."'>".$acntsubgrp->name."</span></td>
                            <td><span id='expacntresgrptext".$data['id']."'>".$resourcegrp->Resource_group_Name."</span></td>
                            <td>
                            <button type='button' class='btn btn-primary linkexpresunitbtn' id='linkexpresunitbtn".$data['id']."' data-toggle='modal' data-target='#LinkExpRes' title='Edit Resource Group' value='".$data['id']."' >Link Resource Group</button>
                            </td>";
            endforeach;
        else:
            $datarows = '<tr id="nodata"><td colspan="10" style="text-align: center;">No Expense Accounts Found</td></tr>';
        endif;
        $arr = array('result' => $datarows, 'error' => 'No');
        echo json_encode($arr);
    }
}
