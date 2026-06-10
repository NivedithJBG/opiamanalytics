<?php

namespace app\controllers;  

use Yii;
use yii\filters\AccessControl; 
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;  
use yii\web\CHttpException;
use yii\helpers\Html;  
use yii\helpers\ArrayHelper;  
use app\models\Accountsmaster; 
use app\models\AccountsItem; 
use app\models\AccountTypes;
use app\models\ResourceGroup;
use app\models\AccountsSub;  
use app\models\Bsitems;   
use app\models\Resourcetype;
use app\models\LedgerOpeningbalance;
use app\models\SubgroupAccounts;
use app\models\Resources;

class AccountssubController extends Controller
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

    /* Actions Status Active Start */
    public function actionCreate()
    {
        $model=new AccountsSub();
        if(isset($_POST['acntsubgrpsname'])):
            $model->master_id=$_POST['acntgrp'];
            $model->name=$_POST['acntsubgrpsname'];
			//$model->spread=$_POST['spread'];
            if(isset($_POST['type'])):
                $model->type=$_POST['type'];
            endif;
		    $model->ResourceType_Id=$_POST['restypeid'];
            /*$model->Added_By=Yii::app()->user->id;*/
            if($model->save(false)):
                $model->sortorder= $model->id;
                $model->save(false);
                $arr = array('Id' => $model->id, 'Name' => $model->name,'Account group'=>$model->master_id,'error'=>'No');
                return json_encode($arr);
            else:
                $arr = array('id' => '', 'name' => '','error'=>'Yes','errortext'=>'Cannot add now. Please try again later');
                return json_encode($arr);
            endif;

        endif;

    }
    public function actionSearch()
    {
        $connection = \Yii::$app->db; 
        $sql="SELECT a.id,a.name,a.spread,a.master_id,a.type,b.name as accountgroup FROM accounts_sub as a inner join accountsmaster as b on a.master_id=b.id WHERE a.Status=0";
        if(isset($_POST['acntgrp'])):
            if($_POST['acntgrp']!='none')
            $sql.=" AND a.master_id='".$_POST['acntgrp']."'";
        endif;
        if(isset($_POST['acntsubgrpsname'])):
            if($_POST['acntsubgrpsname']!='')
            $sql.=" AND a.name LIKE '%".$_POST['acntsubgrpsname']."%'";
        endif;
        $sql.=" ORDER BY sortorder ASC,id DESC";
        //echo $sql;exit;
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $dataProvider=$dataReader->readAll();
        $datarows='';
        $accsubgrp='';
        $accsubgrps='';
        $accsubgrpsmaster='';
        $accounts=Accountsmaster::find()->all();
        $accsubgrp.='<div class="row sbgrp"> <div class="col-md-10 col-sm-10">
                        <div class="row">
                           <div class="col-md-1 col-sm-1">
                                <label>#</label>
                           </div>
                            <div class="col-md-9 col-sm-9">
                                <label>Account Sub Groups</label>
                           </div>
                           <div class="col-md-2 col-sm-2">
                                <label></label>
                           </div>
                        </div>

                    </div>
                    <div class="col-md-2 col-sm-2">
                        <label></label>
                    </div> </div>';
        if(count($dataProvider)>0):
            foreach($dataProvider AS $key=>$data):
                $acntgrp='';
                foreach($accounts AS $acnts):
                    if($data['master_id']==$acnts->id):
                        $seletedtype='Selected="selected"';
                    else:
                        $seletedtype='';
                    endif;
                    $acntgrp.= "<option value='".$acnts->id."' ".$seletedtype.">".$acnts->name."</option>";
                endforeach;
                $spread='';
                if($data['master_id']==7)
                {
                    switch ($data['spread']) {
                        case '0':
                            $spread='Begin';
                            break;
                        case '1':
                            $spread='In Between';
                            break;
                        case '2':
                            $spread='End';
                            break;
                        default:
                            $spread='';
                            break;
                    }
                }
                if($data['master_id']==6){
                    $bsbtn="<button type='button' title='BS Items' class='btn btn-primary childbsitems' value='".$data['id']."' id='childbsitems".$data['id']."'>Schedule Items</button>";
                }
                else{
                    $bsbtn="<button type='button' title='Account Heads' class='btn btn-primary childaccounts' value='".$data['id']."' id='childaccounts".$data['id']."'>Account Heads</button>";
                }

                 if($data['master_id']==6){
                    $bsbtns="<button type='button' title='BS Items' class='childbsitems' value='".$data['id']."' id='childbsitems".$data['id']."'> <a title='Schedule  Items' href='#' class='account-subgroup-btn'>Schedule Items</a></button>";

                     
                }
                else{
                    $bsbtns="<button type='button' title='Account Heads' class='childaccounts'  value='".$data['id']."' id='childaccounts".$data['id']."'><a title='Account  Heads' href='#' class='account-subgroup-btn'>Account Heads</a></button>";

                }

                if($data['type']==1){
                    $type="Cash Inflow";
                }
                elseif($data['type']==2){
                    $type="Cash Outflow";
                }
                else{
                    $type='';
                }

                $accsubgrp.=' 
                        <div class="account-subgroup-cntnt-wrpr row marginrow">
                            <div class="col-md-10 col-sm-10">
                                <div class="row">
                                    <div class="col-md-1 col-sm-1 ">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label></label>
                                                <span class="number indent-number">'.($key + 1).'</span>                                          
                                            </div>
                                            
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-9 col-sm-9 type ">
                                        <label></label>
                                        <span id="acntsubgrpstext'.$data['id'].'">'.$data['name'].'</span>
                                    </div>                                   
                                    
                                    <div class="col-md-2 col-sm-2  "></div>
                                    
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-2 icon-groups">
                                <input type="hidden" id="resgrp" value="'.$data['master_id'].'">
                                <input type="hidden" id="sbgrp" value="'.$data['id'].'">
                                <button type="button" class="editaccountsubgrpbutton" value="'.$data['id'].'" id="editaccountsubgrpbutton'.$data['id'].'"><a href="#" title="Edit Account Subgroup" class="icon-pencil"></a></button>
                              
                                '.$bsbtns.'
                               
                                <button type="" class="deleteacntsubgrpsbutton" value="'.$data['id'].'" id="deleteacntsubgrpsbutton'.$data['id'].'">  <a title="Delete Account Sub Group" href="#" class="icon-trash1"></a></button>
                            </div>
                        </div>';

            endforeach;

        $accsubgrpsmaster.=$data['master_id'];

        if($_POST['acntgrp']!='none'){

            $accsubgrps.='
                    <div class="account-subgroup-cntnt-wrpr group-heading row">
                        <div class="col-md-12 col-sm-12">
                            <div class="row">   

                                <div class="col-md-2 col-sm-2"></div>                                                         
                                <div class="col-md-8 col-sm-8 text-center">
                                    
                                    <label id="acntgrpstext'.$data['id'].'" style="font-size:15px;">Account Groups Of '.$data['accountgroup'].'</span>
                                     
                                </div> 
                                <div class="col-md-2 col-sm-2"></div> 
                            </div>
                        </div>
                                
                    </div>';

        }
        else{

            $accsubgrps.='
                    <div class="account-subgroup-cntnt-wrpr group-heading row">
                        <div class="col-md-12 col-sm-12">
                            <div class="row">    

                                <div class="col-md-2 col-sm-2"></div>                                                          
                                <div class="col-md-8 col-sm-8 text-center">
                                    
                                    <label id="acntgrpstext" style="font-size:15px;">Account Groups </label>
                                     
                                </div>  

                                <div class="col-md-2 col-sm-2"></div> 
                            </div>
                        </div>
                                
                    </div>';


                    

        }

        else:
             $accsubgrp='<div class="account-subgroup-cntnt-wrpr row"><div colspan="9" style="text-align: center">No Account Subgroups Found</div></div>';

        endif;
        $arr = array('result' => $accsubgrp,'accsubgrpss'=>$accsubgrps,'mastergrp'=> $accsubgrpsmaster,'error'=>'No');
        return json_encode($arr);
    }

    public function actionEditaccsubgrp()
    {
        $id=$_POST['accountsubgroups'];
        $connection = \Yii::$app->db; 

        $model=$this->findModel($id); 

        $datarows='';
        $sql="SELECT * FROM accounts_sub WHERE id='$id'";
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $group=$dataReader->read();
        $sql1="SELECT a.id,a.subgrp_id,a.account_id,b.name,b.tds,b.servicetax,b.account_type,b.schedule FROM subgroup_accounts AS a INNER JOIN accounts_item AS b on a.account_id=b.id WHERE a.subgrp_id='$id'";
        //echo $sql1;exit;
        $command=$connection->createCommand($sql1);
        $dataReader=$command->query();
        $dataProvider=$dataReader->readAll();
        if(isset($_POST['groupid']))
        {
            $model->master_id=$_POST['groupid'];
            $model->name=$_POST['subgrpname'];
            //$model->spread=$_POST['spread'];
            $model->type=$_POST['type'];
            $model->ResourceType_Id=$_POST['resource_typeid'];
            if($model->save(false)):
                $this->redirect(array('projects/masters'));
            endif;
        }

           
        $datarows.=' 
            <div class="col-md-12">
                <div class="form-title">Edit Account Sub Group</div>
            </div>
            <div class="col-md-1"></div>
                <div class="col-md-3">                           
                    <div class="form-group">
                        <label>Account Group</label>
                        <select name="groupid" disabled="disabled" class="form-control" id="accgrp'.$id.'">';

        $accountgrp=Accountsmaster::find()->where(['Status'=>0])->all();
        foreach($accountgrp AS $groups):
            if($groups['id']==$group['master_id']):
                $selected="selected";else:$selected="";
            endif;           
           $datarows.= '<option value="'.$groups['id'].'" '.$selected.'>'.$groups['name'].'</option>';
        endforeach;
        
        $datarows.= ' 
                        </select>        
                        <span class="error" style="display: none;"></span>
                    </div>
                </div>
                <div class="col-md-4">                                    
                    <div class="add-new-form-wrpr">
                        <div class="form-group">
                            <label>Account Sub Group Name</label>
                            <input type="text"  id="accsubgrpname'.$id.'" class="form-control" name="subgrpname" value="'. $model->name.'">
                            <span class="error" style="display: none;"></span>
                        </div>      
                    </div>                                   
                </div>';

        if($model->master_id==1):

            $datarows.='
                <div class="col-md-3 hiding">
                    <div class="add-new-form-wrpr">
                        <div class="form-group">
                            <label>Resource Type</label>
                            <select name="resource_typeid" class="form-control" id="accrestypes'.$id.'">
                            <option value="none">Select Resource Type</option>'; 
                                                
            $resourcetypes=Resourcetype::find()->where(['Status'=>0])->all();
            foreach($resourcetypes AS $resourcetype):
                if($resourcetype['ResourceType_Id']==$group['ResourceType_Id']):$selected="selected";else:$selected="";
                endif;
                $datarows.= '<option value="'.$resourcetype['ResourceType_Id'].'" '.$selected.'>'.$resourcetype['Name'].'</option>';
            endforeach;
            $datarows.= '   </select>
                            <span class="error" style="display: none;"></span>
                        </div>
                    </div>
                </div>';

        elseif($model->master_id==9):

            $datarows.='
                <div class="col-md-3 hiding">
                    <div class="add-new-form-wrpr">
                        <div class="form-group">
                            <label>Resource Type</label>
                            <select name="resource_typeid" class="form-control" id="accrestypes'.$id.'">
                            <option value="none">Select Resource Type</option>'; 
                                                
            $resourcetypes=Resourcetype::find()->where(['Status'=>0])->all();
            foreach($resourcetypes AS $resourcetype):
                if($resourcetype['ResourceType_Id']==$group['ResourceType_Id']):$selected="selected";else:$selected="";
                endif;
                $datarows.= '<option value="'.$resourcetype['ResourceType_Id'].'" '.$selected.'>'.$resourcetype['Name'].'</option>';
            endforeach;
                    
            $datarows.= '   </select>
                            <span class="error" style="display: none;"></span>
                        </div>
                    </div>
                </div>';
                       

        endif;
        
        $datarows.=' 
                <div class="col-md-1"></div>
                <div class="col-md-12">
                    <div class="text-center">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-danger cancel" id="cancel"><span class="icon-close"></span> Cancel</button>
                        <button type="button" class="btn btn-primary saveaccountsubgrous" value="'.$id.'" id="saveaccountsubgrous'.$id .'"><span class="icon-check"></span> Edit Account Sub Group</button>   
                    </div>
                </div>';

        $arr = array('result' => $datarows, 'error' => 'No');
        return json_encode($arr);  
    }

    public function actionDeleteitem()
    {
        if(isset($_POST['acntsubgrpsid'])):
            $model = $this->findModel($_POST['acntsubgrpsid']);
            $model->Status = 1;
            $model->save(false);
            $arr = array('Id' => $_POST['acntsubgrpsid'],'error'=>'No');
           return json_encode($arr);
        else:
            $arr = array('Id' => $_POST['restypeid'],'error'=>'Yes','errortext'=>'Can not Delete Resource Type with ID '.$_POST['resid'].'.');
            return json_encode($arr);
        endif;
    }

    public function actionEdit()
    {

        $id=$_POST['accsubgrps'];
        $connection = \Yii::$app->db; 
        $model=$this->findModel($id);
        $sql="SELECT * FROM accounts_sub WHERE id='$id'";
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $group=$dataReader->read();
        $sql1="SELECT a.id,a.subgrp_id,a.account_id,b.name,b.tds,b.servicetax,b.account_type,b.schedule FROM subgroup_accounts AS a INNER JOIN accounts_item AS b on a.account_id=b.id WHERE a.subgrp_id='$id'";
        //echo $sql1;exit;
        $command=$connection->createCommand($sql1);
        $dataReader=$command->query();
        $dataProvider=$dataReader->readAll();
        if(isset($_POST['groupid']))
        {
            $model->master_id=$_POST['groupid'];
            $model->name=$_POST['subgrpname'];
            if(isset($_POST['resource_typeid'])):
                $model->ResourceType_Id=$_POST['resource_typeid'];
            endif;
            if($model->save(false)):
                $arr = array('Id' => $model->id, 'error' => 'No');
                return json_encode($arr);  
            endif;
        }

    }

    /*public function actionGetbsitems()
    {
        if($_POST['accountsubgroup']!=''):

            $code = Bsitems::find()->where(['acntsubgrp_id'=>$_POST['accountsubgroup']])->all();
            $data = ArrayHelper::map($code, 'item_id', 'itemname');

            echo "<option value=''>Select Balancesheet Items</option>"; 
            
            foreach($data as $value=>$name)
            {    
                echo "<option value='".$value."'>".$name."</option>";                 
            }
        else:
            echo Html::tag('option',
                           array('value'=>''),'Select Balancesheet Items',true);                                    
        endif;

    }*/

    public function actionGetbsitems()
    {
        if($_POST['accountsubgroup']!=''):

            $bsitems = Bsitems::find()->where(['acntsubgrp_id'=>$_POST['accountsubgroup']])->andWhere(['status'=> 0])->all();
            //$data = ArrayHelper::map($code, 'item_id', 'itemname');

            $datarows='<option value="0">Select Schedule Items</option>'; 
            
            foreach($bsitems as $bsitem)
            {    
                $datarows.='<option value="'.$bsitem->item_id.'">'.$bsitem->itemname.'</option>';                 
            }
            //echo $datarows; exit;
            $arr = array('result'=>$datarows,'error' =>'No');
            return json_encode($arr); 
        else:
            //echo Html::tag('option',
            //array('value'=>''),'Select Balancesheet Items',true);                                    
        endif;

    }

    public function actionSearchbsitems()
    {
        $dataitems=Bsitems::find()->where(['acntsubgrp_id'=>$_POST['subgrpid']])->andWhere(['status'=>0])->all();

        $acntsubgrp=AccountsSub::findOne($_POST['subgrpid']);
        $acntmaster=Accountsmaster::findOne($acntsubgrp->master_id);
        $datarows='
                <div class="search-and-actions-wrpr row">
                    <div class="col-md-2 col-sm-2">
                    </div>
                    <div class="col-md-8 col-sm-8">
                        <div class="col-md-12">
                                
                            <label style="text-align: center;font-size: 15px;">Schedule Items of '.$acntsubgrp->name.'</label>                                
                            
                        </div>
                    </div>
                    <div class="content-action-wrpr col-md-2 col-sm-2">
                        <button type="button" class="btn btn-danger"  id="addbsitems" value=""><span class="glyphicon glyphicon-plus-sign"></span>Add</button>
                        <button type="button" class="btn btn-danger backbsitems"></span>Back</button>
                    </div>
                </div>
                <div class="bs-items-wrpr group-heading row sbgrp">
                    <!--<div class="col-md-6 col-sm-6">
                        <div class="row">                                                             
                            <div class="col-md-12 col-sm-12 type ">

                                <span id="acntgrpstext">Account SubGroup: '.$acntsubgrp->name.'</span>
                                                                    
                            </div>  
                        </div>
                    </div>

                    <div class="col-md-6 col-sm-6">
                        <div class="row">                                                             
                            <div class="col-md-12 col-sm-12 type ">
                                
                                <span id="acntgrpstext">Account Group: '.$acntmaster->name.'</span>
                                 
                            </div>  
                        </div>
                    </div>-->

                    <div class="col-md-10 col-sm-10">
                        <div class="row">
                           <div class="col-md-1 col-sm-1">
                                <label>#</label>
                           </div>
                            <div class="col-md-9 col-sm-9">
                                <label>Schedule Items</label>
                           </div>
                           <div class="col-md-2 col-sm-2">
                                <label></label>
                           </div>
                        </div>

                    </div>
                    <div class="col-md-2 col-sm-2">
                        <label></label>
                    </div>
                            
                </div>';

        if($dataitems):

            foreach($dataitems AS $key=>$data):
                
                $datarows.=' 
                        <div class="marginrow bsitemrow'.$data['item_id'].'">
                            <div class="col-md-9 col-sm-9">
                                <div class="row">
                                    <div class="col-md-1 col-sm-1">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label></label>
                                                <span class="number indent-number">'.($key + 1).'</span>                                          
                                            </div>
                                            
                                        </div>
                                    </div>
                                    <div class="col-md-11 col-sm-11 type">
                                        <label></label>
                                        <span id="acntbsitemtext'.$data['item_id'].'">'.$data['itemname'].'</span>
                                        <input type="text" class="form-control" id="editbsname'.$data['item_id'].'" value="'.$data['itemname'].'" style="display:none">
                                        <span class="error" style="display: none;color:red;">Enter Schedule Item</span>
                                    </div>                                   
                                                                     
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-3 icon-groups">
                                <button type="button" class="editbsitembutton" value="'.$data['item_id'].'" id="editbsitembutton'.$data['item_id'].'"><a href="#" title="Edit Schedule Item" class="icon-pencil"></a></button>
                                <button type="button" class="savebsitembutton" value="'.$data['item_id'].'" id="savebsitembutton'.$data['item_id'].'" style="display:none"><a href="#" title="Edit Schedule Item" class="icon-save"></a></button>

                                <button type="button" title="Account Heads" class="btn btn-primary schedule_accountheads" value="'.$data['item_id'].'" id="schedule_accountheads'.$data['item_id'].'">Account Heads</button>
                               
                                <button type="button" class="deletebsitembutton" value="'.$data['item_id'].'" id="deletebsitembutton'.$data['item_id'].'">  <a title="Delete Schedule Item" href="#" class="icon-trash1"></a></button>
                            </div>
                        </div>';
            endforeach;
        else:
            $datarows.='<div style="text-align: center;">No Schedule Items Found</div>';
        endif;
        $arr = array('result' => $datarows,'error'=>'No');
        return json_encode($arr);
    }

    public function actionAccountheadbsitems()
    {
        $dataitems=Bsitems::find()->where(['accnt_id'=>$_POST['accountid']])->andWhere(['status'=>0])->all();

        $accnthead=AccountsItem::findOne($_POST['accountid']); 
        $datarows='
                <div class="search-and-actions-wrpr row">
                    <div class="col-md-2 col-sm-2">
                    </div>
                    <div class="col-md-8 col-sm-8">
                        <div class="col-md-12">
                                
                            <label style="text-align: center;font-size: 15px;">Account Subgroups of '.$accnthead->name.'</label>

                        </div>
                    </div>
                    <div class="content-action-wrpr col-md-2 col-sm-2">
                        <button type="button" class="btn btn-danger" style="display:none;" id="accountaddbsitems" value=""><span class="glyphicon glyphicon-plus-sign"></span>Add</button>
                        <button type="button" class="btn btn-danger accountbackbsitems"></span>Back</button>
                    </div>
                </div>
                <div class="bs-items-wrpr group-heading row sbgrp">
                    <!--<div class="col-md-6 col-sm-6">
                        <div class="row">                                                             
                            <div class="col-md-12 col-sm-12 type ">

                                <span id="accountacntgrpstext">Account Head: '.$accnthead->name.'</span>
                                                                    
                            </div>  
                        </div>
                    </div>

                    <div class="col-md-6 col-sm-6">
                        <div class="row">                                                             
                            <div class="col-md-12 col-sm-12 type ">
                                
                                <span id="acntgrpstext">Account Group: </span>
                                 
                            </div>  
                        </div>
                    </div>-->

                    <div class="col-md-10 col-sm-10">
                        <div class="row">
                           <div class="col-md-1 col-sm-1">
                                <label>#</label>
                           </div>
                            <div class="col-md-9 col-sm-9">
                                <label>Schedule Items</label>
                           </div>
                           <div class="col-md-2 col-sm-2">
                                <label></label>
                           </div>
                        </div>

                    </div>
                    <div class="col-md-2 col-sm-2">
                        <label></label>
                    </div>
                            
                </div>';

        if($dataitems):

            foreach($dataitems AS $key=>$data):
                
                $datarows.=' 
                        <div class="marginrow bsitemrow'.$data['item_id'].'">
                            <div class="col-md-10 col-sm-10">
                                <div class="row">
                                    <div class="col-md-1 col-sm-1">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label></label>
                                                <span class="number indent-number">'.($key + 1).'</span>                                          
                                            </div>
                                            
                                        </div>
                                    </div>
                                    <div class="col-md-11 col-sm-11 type">
                                        <label></label>
                                        <span id="accountacntbsitemtext'.$data['item_id'].'">'.$data['itemname'].'</span>
                                        <input type="text" class="form-control" id="accounteditbsname'.$data['item_id'].'" value="'.$data['itemname'].'" style="display:none">
                                        <span class="error" style="display: none;color:red;">Enter Schedule Item</span>
                                    </div>                                   
                                                                     
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-2 icon-groups">
                                <button type="button" class="accounteditbsitembutton" value="'.$data['item_id'].'" id="accounteditbsitembutton'.$data['item_id'].'"><a href="#" title="Edit Schedule Item" class="icon-pencil"></a></button>
                                <button type="button" class="accountsavebsitembutton" value="'.$data['item_id'].'" id="accountsavebsitembutton'.$data['item_id'].'" style="display:none"><a href="#" title="Edit Schedule Item" class="icon-save"></a></button>
                               
                                <button type="button" class="accountdeletebsitembutton" value="'.$data['item_id'].'" id="accountdeletebsitembutton'.$data['item_id'].'">  <a title="Delete Schedule Item" href="#" class="icon-trash1"></a></button>
                            </div>
                        </div>';
            endforeach;
        else:
            $datarows.='<div style="text-align: center;">No Schedule Items Found</div>';
        endif;
        $arr = array('result' => $datarows,'error'=>'No');
        return json_encode($arr);
    }

    public function actionCreatebsitem()
    {
        $model= new Bsitems();
        $model->acntsubgrp_id=$_POST['acntsubgrp'];
        $model->itemname=$_POST['bsitemname'];
        $model->status=0;
        $model->save(false);

        $dataitems=Bsitems::find()->where(['acntsubgrp_id'=>$_POST['acntsubgrp']])->andWhere(['status'=>0])->all();

        $acntsubgrp=AccountsSub::findOne($_POST['acntsubgrp']);
        $acntmaster=Accountsmaster::findOne($acntsubgrp->master_id);
        $datarows='
                <div class="search-and-actions-wrpr row">
                    <div class="col-md-2 col-sm-2">
                    </div>
                    <div class="col-md-8 col-sm-8">
                        <div class="col-md-12">
                                
                            <label style="text-align: center;font-size: 15px;">Schedule Items</label>                                
                            
                        </div>
                    </div>
                    <div class="content-action-wrpr col-md-2 col-sm-2">
                        <button type="button" class="btn btn-danger"  id="addbsitems" value=""><span class="glyphicon glyphicon-plus-sign"></span>Add</button>
                        <button type="button" class="btn btn-danger backbsitems"></span>Back</button>
                    </div>
                </div>
                <div class="bs-items-wrpr group-heading row">
                    <div class="col-md-6 col-sm-6">
                        <div class="row">                                                             
                            <div class="col-md-12 col-sm-12 type ">

                                <span id="acntgrpstext">Account SubGroup: '.$acntsubgrp->name.'</span>
                                                                    
                            </div>  
                        </div>
                    </div>

                    <div class="col-md-6 col-sm-6">
                        <!--<div class="row">                                                             
                            <div class="col-md-12 col-sm-12 type ">
                                
                                <span id="acntgrpstext">Account Group: '.$acntmaster->name.'</span>
                                 
                            </div>  
                        </div>-->
                    </div>
                            
                </div>';

        if($dataitems):

            foreach($dataitems AS $key=>$data):
                
                $datarows.=' 
                        <div class="bsitemrow'.$data['item_id'].'">
                            <div class="col-md-10 col-sm-10">
                                <div class="row">
                                    <div class="col-md-1 col-sm-1">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <span>'.($key + 1).'</span>                                          
                                            </div>
                                            
                                        </div>
                                    </div>
                                    <div class="col-md-11 col-sm-11 type">
                                        <label>Schedule Item</label><br/>
                                        <span id="acntbsitemtext'.$data['item_id'].'">'.$data['itemname'].'</span>
                                        <input type="text" class="form-control" id="editbsname'.$data['item_id'].'" value="'.$data['itemname'].'" style="display:none">
                                        <span class="error" style="display: none;color:red;">Enter Schedule Item</span>
                                    </div>                                   
                                                                     
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-2 icon-groups">
                                <button type="button" class="editbsitembutton" value="'.$data['item_id'].'" id="editbsitembutton'.$data['item_id'].'"><a href="#" title="Edit Schedule Item" class="icon-pencil"></a></button>
                                <button type="button" class="savebsitembutton" value="'.$data['item_id'].'" id="savebsitembutton'.$data['item_id'].'" style="display:none"><a href="#" title="Save Schedule Item" class="icon-save"></a></button>
                               
                                <button type="button" class="deletebsitembutton" value="'.$data['item_id'].'" id="deletebsitembutton'.$data['item_id'].'">  <a title="Delete Schedule Item" href="#" class="icon-trash1"></a></button>
                            </div>
                        </div>';
            endforeach;
        else:
            $datarows.='<div style="text-align: center;">No Schedule Items Found</div>';
        endif;
        $arr = array('result' => $datarows,'error'=>'No');
        return json_encode($arr);
    }

    public function actionAccountcreatebsitem()
    {
        $model= new Bsitems();
        $model->accnt_id=$_POST['accounthead_id'];
        $model->itemname=$_POST['bsitemname'];
        $model->status=0;
        $model->save(false);

        $dataitems=Bsitems::find()->where(['accnt_id'=>$_POST['accounthead_id']])->andWhere(['status'=>0])->all();

        $accnthead=AccountsItem::findOne($_POST['accounthead_id']); 
        $datarows='
                <div class="search-and-actions-wrpr row">
                    <div class="col-md-2 col-sm-2">
                    </div>
                    <div class="col-md-8 col-sm-8">
                        <div class="col-md-12">
                                
                            <label style="text-align: center;font-size: 15px;">Schedule Items</label>                                
                            
                        </div>
                    </div>
                    <div class="content-action-wrpr col-md-2 col-sm-2">
                        <button type="button" class="btn btn-danger"  id="accountaddbsitems" value=""><span class="glyphicon glyphicon-plus-sign"></span>Add</button>
                        <button type="button" class="btn btn-danger accountbackbsitems"></span>Back</button>
                    </div>
                </div>
                <div class="bs-items-wrpr group-heading row">
                    <div class="col-md-6 col-sm-6">
                        <div class="row">                                                             
                            <div class="col-md-12 col-sm-12 type ">

                                <span id="accountacntgrpstext">Account Head: '.$accnthead->name.'</span>
                                                                    
                            </div>  
                        </div>
                    </div>

                    <div class="col-md-6 col-sm-6">
                        <!--<div class="row">                                                             
                            <div class="col-md-12 col-sm-12 type ">
                                
                                <span id="acntgrpstext">Account Group: </span>
                                 
                            </div>  
                        </div>-->
                    </div>
                            
                </div>';

        if($dataitems):

            foreach($dataitems AS $key=>$data):
                
                $datarows.=' 
                        <div class="bsitemrow'.$data['item_id'].'">
                            <div class="col-md-10 col-sm-10">
                                <div class="row">
                                    <div class="col-md-1 col-sm-1">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <span>'.($key + 1).'</span>                                          
                                            </div>
                                            
                                        </div>
                                    </div>
                                    <div class="col-md-11 col-sm-11 type">
                                        <label>Schedule Item</label><br/>
                                        <span id="accountacntbsitemtext'.$data['item_id'].'">'.$data['itemname'].'</span>
                                        <input type="text" class="form-control" id="accounteditbsname'.$data['item_id'].'" value="'.$data['itemname'].'" style="display:none">
                                        <span class="error" style="display: none;color:red;">Enter Schedule Item</span>
                                    </div>                                   
                                                                     
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-2 icon-groups">
                                <button type="button" class="accounteditbsitembutton" value="'.$data['item_id'].'" id="accounteditbsitembutton'.$data['item_id'].'"><a href="#" title="Edit Schedule Item" class="icon-pencil"></a></button>
                                <button type="button" class="accountsavebsitembutton" value="'.$data['item_id'].'" id="accountsavebsitembutton'.$data['item_id'].'" style="display:none"><a href="#" title="Edit Schedule Item" class="icon-save"></a></button>
                               
                                <button type="button" class="accountdeletebsitembutton" value="'.$data['item_id'].'" id="accountdeletebsitembutton'.$data['item_id'].'">  <a title="Delete Schedule Item" href="#" class="icon-trash1"></a></button>
                            </div>
                        </div>';
            endforeach;
        else:
            $datarows.='<div style="text-align: center;">No Schedule Items Found</div>';
        endif;
       
        $arr = array('result' => $datarows,'error'=>'No');
        return json_encode($arr);
    }

    public function actionUpdatebsitem()
    {
        $model=Bsitems::findOne($_POST['itemid']);
        $model->itemname=$_POST['name'];
        $model->save(false);
        $arr = array('result' => $_POST['name'],'error'=>'No');
        return json_encode($arr);
    }

    public function actionDeletebsitem()
    {
        $model=Bsitems::findOne($_POST['bsitemid']);
        $model->status=1;
        $model->save(false);
        $arr = array('result' => $_POST['bsitemid'],'error'=>'No');
        return json_encode($arr);
    }

    public function actionScheduleaccountsearch()
    {
        //echo 'hai'; exit;
        $connection = \Yii::$app->db; 
        $sql = "SELECT a.id,a.Status,a.name,a.tds,a.servicetax,a.account_type,a.resource_id,a.schedule,a.favourite,c.name AS subname,a.resource_group FROM accounts_item AS a LEFT JOIN subgroup_accounts AS b  on a.id=b.account_id LEFT JOIN accounts_sub AS c on b.subgrp_id=c.id ";
        $sql .= " WHERE b.bsitem_id =" . $_POST['scheduleitem'] . " ";
        $sql .= "AND a.Status=0 GROUP BY a.id ORDER BY a.name ASC";
       
        //echo $sql;exit;
        $command = $connection->createCommand($sql);
        $dataReader = $command->query();
        $dataProvider = $dataReader->readAll();
        $datarows = '';
        //$accheads = '';

        $bsitem=Bsitems::findOne($_POST['scheduleitem']);

        $accheads='
                <div class="search-and-actions-wrpr row">
                    <div class="col-md-2 col-sm-2">
                    </div>
                    <div class="col-md-8 col-sm-8">
                        <div class="col-md-12">
                                
                            <label style="text-align: center;font-size: 15px;">Account Heads of '.$bsitem->itemname.'</label>                                
                            
                        </div>
                    </div>
                    <div class="content-action-wrpr col-md-2 col-sm-2">
                        <button type="button" class="btn btn-danger backschbsitems"></span>Back</button>
                    </div>
                </div>';

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
                                    /*if($data['account_type']==1 || $data['account_type']==2){
                                        if($data['favourite']==0){
                                            $accheads.='<a title="Add to favourites" href="javascript:void(0)" id="favourites'.$data['id'].'" data-id="'.$data['id'].'" data-value="'.$data['favourite'].'" class="btn btn-primary starred add-to-favorite" title="Add to Favorite"><span class="icon-star2"></span></a>';
                                        }elseif($data['favourite']==1){

                                             $accheads.='<a  title="Remove from favourites" href="javascript:void(0)" id="favourites'.$data['id'].'" data-id="'.$data['id'].'" data-value="'.$data['favourite'].'" class="btn btn-primary add-to-favorite starred added-to-fav" title="Edit Favorite"><span class="icon-star2"></span></a>';
                                        }
                                    }

                                    if($data['account_type']==8){

                                        $scheduleitems = '<button type="button" class="editschedule_item" value="'.$data['id'].'" id="editschedule_item'.$data['id'].'"><a title="Schedule Items" href="#" class="account-subgroup-btn">Schedule Items</a></button>';

                                    }
                                    else{*/

                                    $scheduleitems = '';

                                    //}
                                    
                                    $accheads.='<span id="schedule' . $data['id'] . '">'.$schedules.'</span>    

                                        <input type="hidden" class="schitemid" value="'.$_POST['scheduleitem'].'">                                
                                         
                                        <input type="checkbox" style="visibility: hidden;" value="3" $checked id="schedulecheck'.$data['id'].'">  

                                        '.$scheduleitems.'                              

                                        <button type="button" class="scheditaccountsbutton" value="'.$data['id'].'" id="scheditaccountsbutton'.$data['id'].'"> <a title="Edit Account Heads" href="#" class="icon-pencil"></a></button>

                                        <button type="button"  class="schdeleteaccountsbutton" value="' . $data['id'] . '" id="schdeleteaccountsbutton' . $data['id'] . '"><a title="Delete Account Heads" href="#" class="icon-trash1"></a> </button>

                                    </div>
                                </div>
                                                   
                            </div>
                        </div>';

            endforeach;
            $data_type = $data['account_type'];
        else:
            $data_type = '';
            $accheads.='<div class="row"><div colspan="9" style="text-align: center">No Accounts Found</div></div>';
        endif;
        $arr = array('result' => $accheads,'error' => 'No');
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
                        <button type="button" class="btn btn-danger cancel"  id="schcancel"><span class="icon-close"></span> Cancel</button>
                        <button type="button" class="btn btn-primary schsaveaccountheadss" value="'.$checkconditions.'"  id="schsaveaccountheadss'.$checkconditions .'"><span class="icon-check"></span> Edit Account Head</button>
                    </div>
                </div>';

        $arr = array('result' => $datarows, 'error' => 'No');
        return json_encode($arr);                     
      
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

    /* Action Status Active End */

    /*public function actionUpdatesort()
    {
        if(isset($_POST['datavalue']))
        {
            foreach($_POST['datavalue'] AS $data)
            {
                $work=AccountsSub::model()->findByPk($data['rowid']);
                $work->sortorder=$data['rowindex'] + 1;
                $work->save(false);
            }
        }
        $arr = array('error'=>'No');
        echo json_encode($arr);
    }

    public function actionAccountSearch()
    {
        $connection = CActiveRecord::getDbConnection();
        $sql="SELECT id,name,tds,servicetax FROM accounts_item";
        if($_POST['name']!='')
            $sql.=" WHERE name LIKE '%".$_POST['name']."%'";
        //echo $sql;
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $dataProvider=$dataReader->readAll();
        $datarows='';
        if(count($dataProvider)>0):
            foreach($dataProvider AS $key=>$data):
                $datarows.='<tr id="accountrow'.$data['id'].'" class="accountrow'.$data['id'].'">
                <td>'.$data['name'].'</td>
                <td>'.$data['tds'].'</td>
                <td>'.$data['servicetax'].'</td>
                <td><button type="button" class="btn btn-primary addaccount" value="'.$data['id'].'" id="addaccount'.$data['id'].'"> <span class="glyphicon glyphicon-pencil"></span>Add</button></td>
                 </tr>';
            endforeach;
        else:
            $datarows='<tr id="nodata"><td colspan="4" style="text-align: center;">No Accounts Found</td></tr>';
        endif;
        $arr = array('result' => $datarows,'error'=>'No');
        echo json_encode($arr);
    }
    public function actionAddaccount()
    {
        $connection = CActiveRecord::getDbConnection();
        if(isset($_POST['subgrpid'])):
            $connection = CActiveRecord::getDbConnection();
            $sql="INSERT INTO subgroup_accounts (subgrp_id,account_id) VALUES('".$_POST['subgrpid']."','".$_POST['accountid']."')";
            //echo $sql;exit;
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $sql1="SELECT a.id,a.subgrp_id,a.account_id,b.name,b.tds,b.servicetax,b.account_type,b.schedule FROM subgroup_accounts AS a INNER JOIN accounts_item AS b on a.account_id=b.id WHERE a.subgrp_id='".$_POST['subgrpid']."'";
            //echo $sql;exit;
            $command=$connection->createCommand($sql1);
            $dataReader=$command->query();
            $dataProvider=$dataReader->readAll();
            $datarows='';
            foreach($dataProvider AS $key=>$data):
                if($data['account_type']==1){
                    $type='Cash';
                }
                elseif($data['account_type']==2){
                    $type='Bank';
                }
                elseif($data['account_type']==4){
                    $type='Income';
                }
                elseif($data['account_type']==5){
                    $type='Expense';
                }
                elseif($data['account_type']==6){
                    $type='Asset';
                }
                elseif($data['account_type']==7){
                    $type='Liability';
                }
                $datarows.="<tr id='tempprodrow".$data['IOWProducts_Id']."'>
                            <td>".$data['name']."</td>
                            <td>".$data['tds']."</td>
                            <td>".$data['servicetax']."</td>
                            <td>$type</td>
                            <td><button type='button' class='btn btn-primary removeaccount' id='removeaccount".$data['id']."' value='".$data['id']."'>Remove</button></td>
                            </tr>";
            endforeach;
            $arr = array('result' => $datarows,'error'=>'No');
            echo json_encode($arr);
        endif;
    }
    public function actionRemoveaccount()
    {
        if(isset($_POST['id'])):
            $connection = CActiveRecord::getDbConnection();
            $sql="DELETE FROM subgroup_accounts WHERE id='".$_POST['id']."'";
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $arr = array('error'=>'No','id'=>$_POST['id']);
            echo json_encode($arr);
        endif;
    }
    public function actionUpdate()
    {
        if(isset($_POST['acntsubgrpsid']))
        {
            $model=$this->loadModel($_POST['acntsubgrpsid']);
            $model->id=$_POST['acntsubgrpsid'];
            $model->name=$_POST['name'];
            $model->master_id=$_POST['acntgrp'];
            if($model->save()):
                $arr = array('Id' => $_POST['acntsubgrpsid'], 'Name' => $_POST['name'],'Account group'=>$model->master_id,'error'=>'No');
                echo json_encode($arr);
            else:
                $arr = array('Id' => $_POST['resid'],'error'=>'Yes','errortext'=>'Can not update now.');
                echo json_encode($arr);
            endif;
        }
    }

    public function actionCreatebsitem()
    {
        $model=New Bsitems();
        $model->acntsubgrp_id=$_POST['acntsubgrp'];
        $model->itemname=$_POST['bsitemname'];
        $model->save(false);
        $arr = array('name' => $_POST['bsitemname'],'error'=>'No');
        echo json_encode($arr);
    }

    public function actionSearchbsitems()
    {
        $condition='';
        if($_POST['acntsubgrp']!='none' && $_POST['bsitemname']!=''):
            $condition='acntsubgrp_id='.$_POST['acntsubgrp'].' AND itemname LIKE "%'.$_POST['bsitemname'].'%" ';
        elseif($_POST['acntsubgrp']!='none' && $_POST['bsitemname']==''):
            $condition='acntsubgrp_id='.$_POST['acntsubgrp'].' ';
        elseif($_POST['acntsubgrp']=='none' && $_POST['bsitemname']!=''):
            $condition='itemname LIKE "%'.$_POST['bsitemname'].'%" ';
        endif;
        $dataitems=Bsitems::model()->findAll(array('condition'=>$condition,'order'=>'acntsubgrp_id ASC,itemname ASC'));
        if(count($dataitems)>0):
            $datarows='';
            $accounts=AccountsSub::model()->findAll(array('condition'=>'master_id=6','order'=>'sortorder ASC'));
            foreach($dataitems AS $key=>$data):
                $acntgrp='';
                foreach($accounts AS $acnts):
                    if($data['acntsubgrp_id']==$acnts->id):
                        $seletedtype='Selected="selected"';
                    else:
                        $seletedtype='';
                    endif;
                    $acntgrp.= "<option value='".$acnts->id."' ".$seletedtype.">".$acnts->name."</option>";
                endforeach;
                $acntsubgrp=AccountsSub::model()->findByPk($data['acntsubgrp_id'])->name;
                $datarows.="<tr id='bsitemsrow".$data['item_id']."' class='acnsubtgrps".$data['item_id']."' data-id='".$data['item_id']."'>
                            <td class='small75'>".($key + 1)."</td>
                            <td><span id='bsacntsubgrpstext".$data['item_id']."'>".$acntsubgrp."</span>
                            <select class='form-control editbsacntsubgrp' style='display:none' id='editbsacntsubgrp".$data['item_id']."'>
                                <option value='none'>Select Account SubGroup</option>".$acntgrp."
                            </select></td>
                            <td><span id='bsitemtext".$data['item_id']."'>".$data['itemname']."</span>
                            <input class='form-control editbsitemname' style='display:none' type='text' id='editbsitemname".$data['item_id']."' value='".$data['itemname']."'>
                            <span class='error'></span></td>
                            <td class='small75'>
                            <button type='button' title='Edit bsitem' class='btn btn-primary editbsitembutton' id='editbsitembutton".$data['item_id']."' value='".$data['item_id']."'><span class='glyphicon glyphicon-pencil'></span></button>
                            <button type='button' class='btn btn-primary savebsitembutton' style='display:none' title='Save BS Item' value='".$data['item_id']."' id='savebsitembutton".$data['item_id']."'><span class='glyphicon glyphicon-save'></span></button>
                            </td>
                            <td class='small75'><button type='button' title='Delete BS Item' class='btn btn-primary deletebsitembutton' value='".$data['item_id']."' id='deletebsitembutton".$data['item_id']."'><span class='glyphicon glyphicon-trash'></span></button></td>
                            </tr>";
            endforeach;
        else:
            $datarows='<tr id="nodata"><td colspan="5" style="text-align: center;">No BS Items Found</td></tr>';
        endif;
        $arr = array('result' => $datarows,'error'=>'No');
        echo json_encode($arr);
    }

    public function actionUpdatebsitem()
    {
        $model=Bsitems::model()->findByPk($_POST['itemid']);
        $model->acntsubgrp_id=$_POST['acntsubgrp'];
        $model->itemname=$_POST['name'];
        $model->save(false);
        $acntsubgrp=AccountsSub::model()->findByPk($_POST['acntsubgrp'])->name;
        $arr = array('result' => $_POST['itemid'],'acntsubgrp'=>$acntsubgrp,'error'=>'No');
        echo json_encode($arr);
    }

    public function actionDeletebsitem()
    {
        $model=Bsitems::model()->findByPk($_POST['bsitemid']);
        $model->delete();
        $arr = array('result' => $_POST['bsitemid'],'error'=>'No');
        echo json_encode($arr);
    }

    */
    /**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('AccountsSub');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new AccountsSub('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['AccountsSub']))
			$model->attributes=$_GET['AccountsSub'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return AccountsSub the loaded model
	 * @throws CHttpException
	 */
	// public function loadModel($id)
	// {
	// 	$model=AccountsSub::model()->findByPk($id);
	// 	if($model===null)
	// 		throw new CHttpException(404,'The requested page does not exist.');
	// 	return $model;
	// }

    public function findModel($id)
    {
         $model=AccountsSub::findOne($id);

        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }

	/**
	 * Performs the AJAX validation.
	 * @param AccountsSub $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='accounts-sub-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
