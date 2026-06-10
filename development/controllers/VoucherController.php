<?php

namespace app\controllers;  

use Yii;
use DateTime;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\Projects;
use app\models\Voucher;
use amnah\yii2\user\models\User;   
use app\models\UserAccounts;
use app\models\AccountsItem; 
use app\models\OrderedResource; 
use app\models\CActiveRecord;
use app\models\UserProjects;
use app\models\Notifications;
use app\models\Journels;
use app\models\Journalitems;
use app\models\Journalvoucher;
use app\models\Cashadvance;
use app\models\itemofworks;
use app\models\Resources;
use app\models\Schedule;
use app\models\FinanceRequests;
use app\models\Fundreceipt;
use app\models\Bsitems;
use app\models\PricingEstimateResourcesNew;
use kartik\mpdf\Pdf;



class VoucherController extends Controller
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
    
    public function actionCashsearch()
    {
        $userid = Yii::$app->user->id;
        $user = User::find()->where(['id'=>$userid])->one();
        if($user['superuser']==1 || $user['superuser']==2)
        {
            $condition="";
        }
        else
        {
            $userprojs=UserProjects::find()->where(['userid' => $userid])->all();
            if(count($userprojs)>0):
                $projids="";
                foreach($userprojs AS $key=>$userproj):
                    if($key==0):
                        $projids.=$userproj['projectid'];
                    else:
                        $projids.=",".$userproj['projectid'];
                    endif;
                endforeach;
                $condition=" AND a.project_id IN (".$projids.") ";
            else:
                $condition="";
            endif;
        }
        $connection = Yii::$app->db;

        $sql="SELECT a.Id,a.User_Id,a.Purpose,a.alloted_amount,a.date,a.Contra,a.project_id,a.place,a.edit,b.username,c.Name,a.credit_account,a.date_tmp FROM finance_requests AS a inner join user as b on a.User_Id=b.id INNER JOIN projects as c on a.project_id=c.Project_Id WHERE a.Status='1' AND (a.payment='1' OR a.payment='') AND (a.contra=1 OR a.contra=0) AND (a.Contratype=1 OR a.Contratype=0) $condition order by a.date_tmp ASC "; 

        //echo $sql;exit;
        /*if($_POST['name']!='')
            $sql.="AND b.username LIKE '%".$_POST['name']."%'";*/
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $dataProvider=$dataReader->readAll();

        $datarows='';
        $count=0;
        //$datarows.='<div class="row"><div class="col-md-12">';
        $datarows='<div class="row cashhead vouchertablehead">

                        <div class="col-md-3 headfrst">
                            <div class="row">
                                <div class="col-md-3">
                                    <label>#</label>
                                </div>
                                <div class="col-md-3">
                                    <label>&nbsp;</label>
                                </div>
                                <div class="col-md-6">
                                   <label>Date</label>
                                </div>
                            </div>
                        </div>
                         <div class="col-md-9 "><div class="row">
                        <div class="col-md-3">
                            <label>Project</label>
                        </div>
                        
                            <div class="col-md-4">
                                <label>Account Head</label>
                            </div>
                            
                            <div class="col-md-2">
                                <label style="/*text-align: right;*/">Amount</label>
                            </div>
                           
                        <div class="col-md-3"></div>
                        </div></div>
                        
                </div>';
                $totss=0;
        if(count($dataProvider)>0):
            foreach($dataProvider AS $key=>$data):
                if($data['Contra']==1)
                {
                    $link='<a href="#" class="btn btn-primary text-button generate-voucher-btn" id="G-cashsearch-'.$data['Id'].'" data-v="contravoucher" data-id="'.$data['Id'].'" title="Generate Voucher"><span class="icon-receipt"></span>Generate Voucher</a>';
                    if($user['superuser']==1):
                        $editbtn="<td><button type='button' class='btn btn-primary editpayment' value='".$data['Id']."' id='editpayment".$data['Id']."' title='Edit Payment'> <span class='glyphicon glyphicon-pencil'></span></button>
                                <button type='button' class='btn btn-primary savepayment' value='".$data['Id']."' id='savepayment".$data['Id']."' title='Save'> <span class='glyphicon glyphicon-save'></span></button></td>";
                    else:
                        $editbtn="";
                    endif;

                }
                else
                {
                    $link='<a href="#" class="btn btn-primary text-button generate-voucher-btn" id="G-cashsearch-'.$data['Id'].'" data-v="generatepayment"  data-id="'.$data['Id'].'" title="Generate Voucher"><span class="icon-receipt"></span>Generate Voucher</a>';
                    $editbtn="<td><button type='button' class='btn btn-primary editpayment' value='".$data['Id']."' id='editpayment".$data['Id']."' title='Edit Payment'> <span class='glyphicon glyphicon-pencil'></span></button>
                                <button type='button' class='btn btn-primary savepayment' value='".$data['Id']."' id='savepayment".$data['Id']."' title='Save'> <span class='glyphicon glyphicon-save'></span></button></td>";
                }
                /*if($data['edit']==1):
                    $button="<td></td><td></td>";
                else:*/
                    $button='<a href="#" class="btn btn-danger icon-trash1 deletepayvoucher" data-id="'.$data['Id'].'" id="deletepayvoucher'.$data['Id'].'" title="Delete Payment Voucher"></a>';
                /*endif;*/
                $accounthead = AccountsItem::find()->where(['id'=> $data['credit_account']])->andWhere(['Status'=>0])->one();
                $content=$this->accntheaddetails($data['Id']);
                $datarows.='<div class="row voucherss" id="cashsearch-'.$data['Id'].'">

                <div class="col-md-3 headfrst">
                    <div class="row frstrw">
                        <div class="headfrst1 col-md-3 numsl" style="padding-top: 2px;"><label></label><span class="number voucher-number"><b>'.++$count.'</b></span></div>
                        <div class="headfrst1 col-md-3 tooltips" style="padding-top: 2px;">
                            <label></label>
                            <span class="tooltip-user">
                                <span class="icon-user3"></span>
                                <span class="tooltip-content">
                                '.$data['username'].'</span>
                            </span>
                             </div>
                             <div class="headfrst1 col-md-6" style="text-align: center;">
                                <label></label>
                                <span class="date"><em class="cal-icon icon-calendar1"></em>'.$data['date'].'</span>
                                </div>
                    </div>
                </div>
                <div class="col-md-9"><div class="row scndrow">
                <div class="scndrow1 col-md-3" style="/*bottom: 8px;*/">
                            <label></label>
                            <span style="min-height: 32px;">'.Projects::findOne($data['project_id'])->Name.'</span></div>
                            
                            <div class="scndrow1 col-md-4 type">

                                <div class="hover">  
                                <label></label>      
                                <span class="prpse">'.$accounthead->name.'</span> 
                                <div class="tooltiptable" id="tooltip'.$data['Id'].'" style="width:600px;">
                                <table cellpadding="0" cellspacing="0" width="100%">
                                <tr><th>Purpose</th></tr>
                                '.$content.'
                                </table>
                                </div>
                                </div>
                                </div>
                                
                            <div class="scndrow1 col-md-2 type" style="text-align: right;">
                                <label></label>
                                <span class="voucheramnt">'.number_format((float)$data['alloted_amount'], 2).'</span>
                            </div>
                            <div class="col-md-3 text-right icon-groups" style="bottom: 10px;">';
                $datarows.=$link; 
                $datarows.=$button;   
                $datarows.='</div></div></div></div>';
                $totss= $totss + $data['alloted_amount'];
            endforeach;
            
            $datarows.='<div class="row voucherss"><div class="col-md-3">
                        <div class="row"><div class="col-md-12" style="padding-left: 35px;font-size: 14px;"><b>Total</b></div></div>

            </div><div class="col-md-9"><div class="row"><div class="col-md-9" style="text-align: right;"><span class="vouchertotamnt"><b>'.number_format((float)$totss, 2).'</b></span></div><div class="col-md-3"></div></div></div></div>';
        else:
            $datarows='<div class="row"><div class="col-md-12"><div class="text-center">No Cash Payment Found</div></div></div>';
        endif;
        $arr = array('result' => $datarows,'error'=>'No');
        return json_encode($arr);
    }
 public function accntheaddetails($id)
 {
    $purposes=FinanceRequests::findOne($id);
    if($purposes){
    $result="<tr><td>".$purposes->Purpose."</td></tr>";

    return $result;
 }
}
public function accntheaddetailsreceipt($id)
 {
    $purposes=Fundreceipt::findOne($id);
    if($purposes){
    $result="<tr><td>".$purposes->Purpose."</td></tr>";

    return $result;
 }
}

  public function journalnarration($id)
 {
    $purposes=Journalitems::find()->where(['journalid'=>$id])->one();
    if($purposes){
    $result="<tr><td>".$purposes->narration."</td></tr>";

    return $result;
}
 }


    public function actionAuditcashsearch()
    {
       // $voucherid = $_POST['aheadid'];
        $userid = Yii::$app->user->id;
        $user = User::find()->where(['id'=>$userid])->one();

        if($user['superuser']==1 || $user['superuser']==2)
        {
            $condition="";
        }
        else
        {
            $userprojs=UserProjects::find()->where(['userid' => $userid])->all();
            if(count($userprojs)>0):
                $projids="";
                foreach($userprojs AS $key=>$userproj):
                    if($key==0):
                        $projids.=$userproj['projectid'];
                    else:
                        $projids.=",".$userproj['projectid'];
                    endif;
                endforeach;
                $condition=" AND a.project_id IN (".$projids.") ";
            else:
                $condition="";
            endif;
        }
        
        $connection = Yii::$app->db;

        $sql="SELECT a.id,a.account_id,a.narration,a.date,a.voucher_no,a.project_id,a.place,a.amount,c.Name,a.audit_comment FROM voucher AS a INNER JOIN projects as c on a.place=c.Project_Id WHERE a.payment='1' AND a.audit_status='1' AND a.audited IS NULL AND  (a.contra=1 OR a.contra=0) $condition order by a.id desc ";

        //echo $sql;exit;
        /*if($_POST['name']!='')
            $sql.="AND b.username LIKE '%".$_POST['name']."%'";*/
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $dataProvider=$dataReader->readAll();

        //print_r($dataProvider); exit;
     
        $datarows='';

        $datarows ='<table class="table table-bordered">
                        <thead class="auditheadcolor">
                            <tr>
                                <th width="5%">#</th>
                                <th width="10%">Date</th>
                                <th width="10%">Vr No</th>
                                <th width="30%">Purpose</th>
                                <th width="20%">Account Head </th>
                                <th width="15%">Amount</th>
                                <th width="10%"></th>
                            </tr>
                        </thead><tbody>';


        //$datarows.='<div class="row"><div class="col-md-12">';
        if(count($dataProvider)>0):
           
            foreach($dataProvider AS $key=>$data):

                $accountdata=$data['account_id'];

                //echo $accountdata; exit;

                $accounthead = AccountsItem::find()->where(['id'=> $accountdata])->andWhere(['Status'=>0])->one();

                  $finreqstid = $data['id'];
                
                //$link='<a href="#" class="btn btn-primary text-button generate-voucher-btn" id="" data-v="" data-id=""><span class="icon-receipt"></span>Audited</a>';
                /*if($data['edit']==1):
                    $button="<td></td><td></td>";
                else:*/
                    //$button='<a href="#" class="btn btn-danger icon-trash1 deletepayvoucher" data-id="'.$data['Id'].'" id="deletepayvoucher'.$data['Id'].'" title="Delete Payment Voucher"></a>';
                /*endif;*/

                $projectname=Projects::findOne($data['project_id']);


                   $datarows.='
                   <tr  class="colspanned" id="auditcashrow-'.$finreqstid.'">

                                            <td class="text-center">
                                                <span class="number">'.($key+1).'</span>
                                            </td>

                                            <td>
                                                <span>'.date('d-m-Y',strtotime($data['date'])).'</span>
                                              </td>

                                                <td>
                                                <span>'. $data['voucher_no'].'</span>
                                              
                                            </td>

                                             <td>
                                                <span>'.$data['narration'].'</span>
                                                 
                                            </td>


                                             <td>
                                                <span>'.$accounthead['name'].'</span>
                                               
                                            </td>

                                            <td>
                                                <span class="finamntpan">'.number_format($data['amount'],2).'</span>
                                               
                                            </td>

                                            <td style="text-align: center;">
                                           
                                                <a class="btn btn-primary text-button auditvoucher" id="auditvoucher'.$finreqstid.'" data-id="'.$finreqstid.'" data-toggle="modal" data-target="#auditModal'.$finreqstid.'" title="Audit">Audit</a>

                                                <div class="modal fade" id="auditModal'.$finreqstid.'" role="dialog">
                                                    <div class="modal-dialog">
                                                    
                                                      <!-- Modal content-->
                                                      <div class="modal-content">
                                                        <div class="modal-header">
                                                          <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                          <h4 class="modal-title">Audit</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <label style="text-align:left;"> Comment </label>
                                                            <textarea  class="form-control auditcmnt" type="text" name="auditcmnt" id="auditcmnt-'.$finreqstid.'" value="'.$data['audit_comment'].'">'.$data['audit_comment'].'</textarea>
                                                        </div>
                                                        <div class="modal-footer">

                                                            <button id="commentapprove-'.$finreqstid.'" type="button" class="btn btn-primary text-button commentapprove" data-dismiss="modal" data-id="'.$finreqstid.'">Approve</button>

                                                            <button id="commentsave-'.$finreqstid.'" type="button" class="btn btn-primary text-button commentsave" data-dismiss="modal" data-id="'.$finreqstid.'">Save</button>
                                                            <button type="button" class="btn btn-primary text-button" data-dismiss="modal">Close</button>
                                                        </div>
                                                      </div>
                                                      
                                                    </div>
                                                </div>
                                           
                                           </td>

                                        </tr>';


            endforeach;
        else:

            $datarows='<div class="row"><div class="col-md-12"><div class="text-center">No Cash Vouchers Found</div></div></div>';
        endif;

         $datarows.=' </tbody>
                    </table>';

        $arr = array('result' => $datarows,'error'=>'No');
        return json_encode($arr);
    }

    public function actionAuditbanksearch()
    {
       // $voucherid = $_POST['aheadid'];
        $userid = Yii::$app->user->id;
        $user = User::find()->where(['id'=>$userid])->one();

        if($user['superuser']==1 || $user['superuser']==2)
        {
            $condition="";
        }
        else
        {
            $userprojs=UserProjects::find()->where(['userid' => $userid])->all();
            if(count($userprojs)>0):
                $projids="";
                foreach($userprojs AS $key=>$userproj):
                    if($key==0):
                        $projids.=$userproj['projectid'];
                    else:
                        $projids.=",".$userproj['projectid'];
                    endif;
                endforeach;
                $condition=" AND a.project_id IN (".$projids.") ";
            else:
                $condition="";
            endif;
        }
        
        $connection = Yii::$app->db;

        $sql="SELECT a.id,a.account_id,a.narration,a.date,a.voucher_no,a.project_id,a.place,a.amount,c.Name,a.audit_comment FROM voucher AS a INNER JOIN projects as c on a.place=c.Project_Id WHERE a.payment='2' AND a.audit_status='2' AND a.audited IS NULL AND  (a.contra=1 OR a.contra=0) $condition order by a.id desc ";

        //echo $sql;exit;
        /*if($_POST['name']!='')
            $sql.="AND b.username LIKE '%".$_POST['name']."%'";*/
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $dataProvider=$dataReader->readAll();

        //print_r($dataProvider); exit;
     
        $datarows='';

        $datarows ='<table class="table table-bordered">
                        <thead class="auditheadcolor">
                            <tr>
                                <th width="5%">#</th>
                                <th width="10%">Date</th>
                                <th width="10%">Vr No</th>
                                <th width="30%">Purpose</th>
                                <th width="20%">Account Head </th>
                                <th width="15%">Amount</th>
                                <th width="10%"></th>
                            </tr>
                        </thead><tbody>';


        //$datarows.='<div class="row"><div class="col-md-12">';
        if(count($dataProvider)>0):
           
            foreach($dataProvider AS $key=>$data):

                $accountdata=$data['account_id'];

                //echo $accountdata; exit;

                $accounthead = AccountsItem::find()->where(['id'=> $accountdata])->andWhere(['Status'=>0])->one();

                  $finreqstid = $data['id'];
                
                //$link='<a href="#" class="btn btn-primary text-button generate-voucher-btn" id="" data-v="" data-id=""><span class="icon-receipt"></span>Audited</a>';
                /*if($data['edit']==1):
                    $button="<td></td><td></td>";
                else:*/
                    //$button='<a href="#" class="btn btn-danger icon-trash1 deletepayvoucher" data-id="'.$data['Id'].'" id="deletepayvoucher'.$data['Id'].'" title="Delete Payment Voucher"></a>';
                /*endif;*/

                $projectname=Projects::findOne($data['project_id']);


                   $datarows.='
                   <tr  class="colspanned" id="auditbankrow-'.$finreqstid.'">

                                            <td class="text-center">
                                                <span class="number">'.($key+1).'</span>
                                            </td>

                                            <td>
                                                <span>'.date('d-m-Y',strtotime($data['date'])).'</span>
                                              </td>

                                                <td>
                                                <span>'. $data['voucher_no'].'</span>
                                              
                                            </td>

                                             <td>
                                                <span>'.$data['narration'].'</span>
                                                 
                                            </td>


                                             <td>
                                                <span>'.$accounthead['name'].'</span>
                                               
                                            </td>

                                             <td>
                                                <span class="finamntpan">'.number_format($data['amount'],2).'</span>
                                               
                                            </td>

                                            <td style="text-align: center;">
                                          
                                                <!--<a href="#" class="btn btn-primary text-button generate-voucher-btn" id="" data-v="" data-id=""><span class="icon-receipt"></span>Audited</a>-->

                                                <a class="btn btn-primary text-button auditvoucher" id="auditvoucher'.$finreqstid.'" data-id="'.$finreqstid.'" data-toggle="modal" data-target="#auditModal'.$finreqstid.'" title="Audit">Audit</a>

                                                <div class="modal fade" id="auditModal'.$finreqstid.'" role="dialog">
                                                    <div class="modal-dialog">
                                                    
                                                      <!-- Modal content-->
                                                      <div class="modal-content">
                                                        <div class="modal-header">
                                                          <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                          <h4 class="modal-title">Audit</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <label style="text-align:left;"> Comment </label>
                                                            <textarea  class="form-control auditcmnt" type="text" name="auditcmnt" id="auditcmnt-'.$finreqstid.'" value="'.$data['audit_comment'].'">'.$data['audit_comment'].'</textarea>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button id="commentbankapprove-'.$finreqstid.'" type="button" class="btn btn-primary text-button commentbankapprove" data-dismiss="modal" data-id="'.$finreqstid.'">Approve</button>


                                                            <button id="commentsave-'.$finreqstid.'" type="button" class="btn btn-primary text-button commentbanksave" data-dismiss="modal" data-id="'.$finreqstid.'">Save</button>
                                                            <button type="button" class="btn btn-primary text-button" data-dismiss="modal">Close</button>
                                                        </div>
                                                      </div>
                                                      
                                                    </div>
                                                </div>
                                           

                                           </td>

                                        </tr>';


            endforeach;
        else:

            $datarows='<div class="row"><div class="col-md-12"><div class="text-center">No Bank Vouchers Found</div></div></div>';
        endif;

         $datarows.=' </tbody>
                    </table>';

        $arr = array('result' => $datarows,'error'=>'No');
        return json_encode($arr);
    }

    public function actionAuditjounalsearch()
    {
       // $voucherid = $_POST['aheadid'];
        $userid = Yii::$app->user->id;
        $user = User::find()->where(['id'=>$userid])->one();

        if($user['superuser']==1 || $user['superuser']==2)
        {
            $condition="";
        }
        else
        {
            $userprojs=UserProjects::find()->where(['userid' => $userid])->all();
            if(count($userprojs)>0):
                $projids="";
                foreach($userprojs AS $key=>$userproj):
                    if($key==0):
                        $projids.=$userproj['projectid'];
                    else:
                        $projids.=",".$userproj['projectid'];
                    endif;
                endforeach;
                $condition=" AND a.project_id IN (".$projids.") ";
            else:
                $condition="";
            endif;
        }
        
        $connection = Yii::$app->db;

        $sql="SELECT a.* FROM journalvoucher AS a INNER JOIN projects as c on a.project_id=c.Project_Id WHERE a.type='2' AND a.audit_status='1' AND a.audited IS NULL $condition order by a.id desc ";

        //echo $sql;exit;
        /*if($_POST['name']!='')
            $sql.="AND b.username LIKE '%".$_POST['name']."%'";*/
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $dataProvider=$dataReader->readAll();

        //print_r($dataProvider); exit;
     
        $datarows='';

        $datarows ='<table class="table table-bordered">
                        <thead class="auditheadcolor">
                            <tr>
                                <th width="5%">#</th>
                                <th width="8%">Date</th>
                                <th width="8%">Vr No</th>
                                <th width="25%">Purpose</th>
                                <th width="17%">Debit Account</th>
                                <th width="17%">Credit Account</th>
                                <th width="12%">Amount</th>
                                <th width="8%"></th>
                            </tr>
                        </thead><tbody>';


        //$datarows.='<div class="row"><div class="col-md-12">';
        if(count($dataProvider)>0):
           
            foreach($dataProvider AS $key=>$data):

                //$accountdata=$data['account_id'];

                //echo $accountdata; exit;

                $debitacnt = AccountsItem::find()->where(['id'=> $data['debitacnt']])->andWhere(['Status'=>0])->one();

                $creditacnt = AccountsItem::find()->where(['id'=> $data['creditacnt']])->andWhere(['Status'=>0])->one();

                $finreqstid = $data['id'];
                
                //$link='<a href="#" class="btn btn-primary text-button generate-voucher-btn" id="" data-v="" data-id=""><span class="icon-receipt"></span>Audited</a>';
                /*if($data['edit']==1):
                    $button="<td></td><td></td>";
                else:*/
                    //$button='<a href="#" class="btn btn-danger icon-trash1 deletepayvoucher" data-id="'.$data['Id'].'" id="deletepayvoucher'.$data['Id'].'" title="Delete Payment Voucher"></a>';
                /*endif;*/

                $projectname=Projects::findOne($data['project_id']);


                   $datarows.='
                   <tr  class="colspanned" id="auditjournalrow-'.$finreqstid.'">

                                            <td class="text-center">
                                                <span class="number">'.($key+1).'</span>
                                            </td>

                                            <td>
                                                <span>'.date('d-m-Y',strtotime($data['date'])).'</span>
                                            </td>

                                            <td>
                                                <span>'. $data['voucher_no'].'</span>
                                              
                                            </td>

                                            <td>
                                                <span>'.$data['narration'].'</span>
                                                 
                                            </td>


                                            <td>
                                                <span>'.$debitacnt['name'].'</span>
                                               
                                            </td>

                                            <td>
                                                <span>'.$creditacnt['name'].'</span>
                                               
                                            </td>

                                            <td>
                                                <span class="finamntpan">'.number_format($data['amount'],2).'</span>
                                               
                                            </td>

                                            <td style="text-align: center;">
                                          
                                                <!--<a href="#" class="btn btn-primary text-button generate-voucher-btn" id="" data-v="" data-id=""><span class="icon-receipt"></span>Audited</a>-->

                                                <a class="btn btn-primary text-button auditjournalvoucher" id="auditjournalvoucher'.$finreqstid.'" data-id="'.$finreqstid.'" data-toggle="modal" data-target="#auditjournalModal'.$finreqstid.'" title="Audit">Audit</a>

                                                <div class="modal fade" id="auditjournalModal'.$finreqstid.'" role="dialog">
                                                    <div class="modal-dialog">
                                                    
                                                      <!-- Modal content-->
                                                      <div class="modal-content">
                                                        <div class="modal-header">
                                                          <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                          <h4 class="modal-title">Audit</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <label style="text-align:left;"> Comment </label>
                                                            <textarea  class="form-control auditjournalcmnt" type="text" name="auditjournalcmnt" id="auditjournalcmnt-'.$finreqstid.'" value="'.$data['audit_comment'].'">'.$data['audit_comment'].'</textarea>
                                                        </div>
                                                        <div class="modal-footer">

                                                            <button id="commentjournalapprove-'.$finreqstid.'" type="button" class="btn btn-primary text-button commentjournalapprove" data-dismiss="modal" data-id="'.$finreqstid.'">Approve</button>

                                                            <button id="commentsave-'.$finreqstid.'" type="button" class="btn btn-primary text-button commentjournalsave" data-dismiss="modal" data-id="'.$finreqstid.'">Save</button>
                                                            <button type="button" class="btn btn-primary text-button" data-dismiss="modal">Close</button>
                                                        </div>
                                                      </div>
                                                      
                                                    </div>
                                                </div>
                                           

                                           </td>

                                        </tr>';


            endforeach;
        else:

            $datarows='<div class="row"><div class="col-md-12"><div class="text-center">No Journal Vouchers Found</div></div></div>';
        endif;

         $datarows.=' </tbody>
                    </table>';

        $arr = array('result' => $datarows,'error'=>'No');
        return json_encode($arr);
    }

    public function actionAuditvouchersearch()
    {
        $userid = Yii::$app->user->id;
        $user = User::find()->where(['id'=>$userid])->one();

        if($user['superuser']==1 || $user['superuser']==2)
        {
            $condition="";
        }
        else
        {
            $userprojs=UserProjects::find()->where(['userid' => $userid])->all();
            if(count($userprojs)>0):
                $projids="";
                foreach($userprojs AS $key=>$userproj):
                    if($key==0):
                        $projids.=$userproj['projectid'];
                    else:
                        $projids.=",".$userproj['projectid'];
                    endif;
                endforeach;
                $condition=" AND a.project_id IN (".$projids.") ";
            else:
                $condition="";
            endif;
        }
        
        $connection = Yii::$app->db;

        $condition1 = '';

        $condition2 = '';

        if($_POST['fromdate']!=''){
            $condition1 = "AND a.date BETWEEN '".$_POST['fromdate']."'";
            $condition2 = "AND '".date('Y-m-d')."'";
        }

        if($_POST['todate']!=''){
            $condition2 = "AND '".$_POST['todate']."'";
        }

        if($_POST['vouchtype']==1){
            $sql="SELECT a.id,a.account_id,a.audit_comment,a.narration,a.date,a.voucher_no,a.project_id,a.place,a.amount,c.Name FROM voucher AS a INNER JOIN projects as c on a.place=c.Project_Id WHERE a.payment='1' AND a.audit_status='1' AND a.audited='1' AND  (a.contra=1 OR a.contra=0) $condition $condition1 $condition2 order by a.id desc ";

            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $dataProvider=$dataReader->readAll();
         
            $datarows='';

            $datarows ='<table class="table table-bordered">
                            <thead class="auditheadcolor">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%">Date</th>
                                    <th width="10%">Vr No</th>
                                    <th width="25%">Purpose</th>
                                    <th width="18%">Account Head</th>
                                    <th width="12%">Amount</th>
                                    <th width="20%">Comment</th>
                                </tr>
                            </thead><tbody>';

            if(count($dataProvider)>0):
               
                foreach($dataProvider AS $key=>$data):

                    $accountdata=$data['account_id'];

                    $accounthead = AccountsItem::find()->where(['id'=> $accountdata])->andWhere(['Status'=>0])->one();

                    $finreqstid = $data['id'];
                    
                    $projectname=Projects::findOne($data['project_id']);

                    $datarows.='
                       <tr  class="colspanned" id="auditcashrow-'.$finreqstid.'">

                            <td class="text-center">
                                <span class="number">'.($key+1).'</span>
                            </td>

                            <td>
                                <span>'.date('d-m-Y',strtotime($data['date'])).'</span>
                              </td>

                                <td>
                                <span>'. $data['voucher_no'].'</span>
                              
                            </td>

                             <td>
                                <span>'.$data['narration'].'</span>
                                 
                            </td>


                             <td>
                                <span>'.$accounthead['name'].'</span>
                               
                            </td>

                            <td>
                                <span class="finamntpan">'.$data['amount'].'</span>
                               
                            </td>

                            <td>
                                <span>'.$data['audit_comment'].'</span>
                               
                            </td>

                        </tr>';

                endforeach;
            else:

                $datarows='<div class="row"><div class="col-md-12"><div class="text-center">No Result Found</div></div></div>';
            endif;
        }
        elseif ($_POST['vouchtype']==2) {
            $sql="SELECT a.id,a.account_id,a.audit_comment,a.narration,a.date,a.voucher_no,a.project_id,a.place,a.amount,c.Name FROM voucher AS a INNER JOIN projects as c on a.place=c.Project_Id WHERE a.payment='2' AND a.audit_status='2' AND a.audited='1' AND  (a.contra=1 OR a.contra=0) $condition $condition1 $condition2 order by a.id desc ";

            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $dataProvider=$dataReader->readAll();
         
            $datarows='';

            $datarows ='<table class="table table-bordered">
                            <thead style="background: #f5f5f5;">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%">Date</th>
                                    <th width="10%">Vr No</th>
                                    <th width="25%">Purpose</th>
                                    <th width="18%">Account Head</th>
                                    <th width="12%">Amount</th>
                                    <th width="20%">Comment</th>
                                </tr>
                            </thead><tbody>';

            if(count($dataProvider)>0):
               
                foreach($dataProvider AS $key=>$data):

                    $accountdata=$data['account_id'];

                    $accounthead = AccountsItem::find()->where(['id'=> $accountdata])->andWhere(['Status'=>0])->one();

                    $finreqstid = $data['id'];
                    
                    $projectname=Projects::findOne($data['project_id']);

                    $datarows.='
                       <tr  class="colspanned" id="auditcashrow-'.$finreqstid.'">

                            <td class="text-center">
                                <span class="number">'.($key+1).'</span>
                            </td>

                            <td>
                                <span>'.date('d-m-Y',strtotime($data['date'])).'</span>
                              </td>

                                <td>
                                <span>'. $data['voucher_no'].'</span>
                              
                            </td>

                             <td>
                                <span>'.$data['narration'].'</span>
                                 
                            </td>


                             <td>
                                <span>'.$accounthead['name'].'</span>
                               
                            </td>

                            <td>
                                <span class="finamntpan">'.$data['amount'].'</span>
                               
                            </td>

                            <td>
                                <span>'.$data['audit_comment'].'</span>
                               
                            </td>

                        </tr>';

                endforeach;
            else:

                $datarows='<div class="row"><div class="col-md-12"><div class="text-center">No Result Found</div></div></div>';
            endif;
        }
        else{

            $sql="SELECT a.* FROM journalvoucher AS a INNER JOIN projects as c on a.project_id=c.Project_Id WHERE a.type='2' AND a.audit_status='1' AND a.audited='1' $condition $condition1 $condition2 order by a.id desc ";

            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $dataProvider=$dataReader->readAll();
         
            $datarows='';

            $datarows ='<table class="table table-bordered">
                            <thead style="background: #f5f5f5;">
                                <tr>
                                    <th width="4%">#</th>
                                    <th width="8%">Date</th>
                                    <th width="8%">Vr No</th>
                                    <th width="20%">Purpose</th>
                                    <th width="15%">Debit Account</th>
                                    <th width="15%">Credit Account</th>
                                    <th width="10%">Amount</th>
                                    <th width="20%">Comment</th>
                                </tr>
                            </thead><tbody>';

            if(count($dataProvider)>0):
               
                foreach($dataProvider AS $key=>$data):

                    $debitacnt = AccountsItem::find()->where(['id'=> $data['debitacnt']])->andWhere(['Status'=>0])->one();

                    $creditacnt = AccountsItem::find()->where(['id'=> $data['creditacnt']])->andWhere(['Status'=>0])->one();

                    $finreqstid = $data['id'];

                    $projectname=Projects::findOne($data['project_id']);

                    $datarows.='
                       <tr  class="colspanned" id="auditjournalrow-'.$finreqstid.'">

                            <td class="text-center">
                                <span class="number">'.($key+1).'</span>
                            </td>

                            <td>
                                <span>'.date('d-m-Y',strtotime($data['date'])).'</span>
                            </td>

                            <td>
                                <span>'. $data['voucher_no'].'</span>
                              
                            </td>

                            <td>
                                <span>'.$data['narration'].'</span>
                                 
                            </td>


                            <td>
                                <span>'.$debitacnt['name'].'</span>
                               
                            </td>

                            <td>
                                <span>'.$creditacnt['name'].'</span>
                               
                            </td>

                            <td>
                                <span class="finamntpan">'.$data['amount'].'</span>
                               
                            </td>

                            <td>
                                <span>'.$data['audit_comment'].'</span>
                               
                            </td>                           

                        </tr>';


                endforeach;
            else:

                $datarows='<div class="row"><div class="col-md-12"><div class="text-center">No Result Found</div></div></div>';
            endif;

        }

         $datarows.=' </tbody>
                    </table>';

        $arr = array('result' => $datarows,'error'=>'No');
        return json_encode($arr);
    }

    public function actionAuditcomment()
    {

        $id = $_POST['voucherID'];
        $voucher=Voucher::findOne($id);
        if($_POST['comment']!=''){

            $voucher->audit_comment = $_POST['comment'];

        }
        //$voucher->audited = 1;
        $voucher->save(false);

        $arr = array('error'=>'No');
        return json_encode($arr);

    }
    public function actionAuditapprove()
    {
        $id = $_POST['voucherID'];
        $voucher=Voucher::findOne($id);

        if($_POST['comment']!=''){

            $voucher->audit_comment = $_POST['comment'];

        }

        $voucher->audited = 1;
        $voucher->save(false);

        $arr = array('error'=>'No');
        return json_encode($arr);

    }

    public function actionAuditjournalcomment()
    {

        $id = $_POST['voucherID'];
        $voucher=Journalvoucher::findOne($id);
        if($_POST['comment']!=''){

            $voucher->audit_comment = $_POST['comment'];

        }
        //$voucher->audited = 1;
        $voucher->save(false);

        $arr = array('error'=>'No');
        return json_encode($arr);

    }
    public function actionAuditjournalapprove()
    {
        $id = $_POST['voucherID'];
        $voucher=Journalvoucher::findOne($id);
        if($_POST['comment']!=''){

            $voucher->audit_comment = $_POST['comment'];

        }
        $voucher->audited = 1;
        $voucher->save(false);

        $arr = array('error'=>'No');
        return json_encode($arr);

    }

    public function actionCashreceipt()
    {
        $userid = Yii::$app->user->id;
        $user = User::find()->where(['id'=>$userid])->one();
        if($user['superuser']==1 || $user['superuser']==2)
        {
            $condition="";
        }
        else
        {
            $userprojs=UserProjects::find()->where(['userid' => $userid])->all();
            if(count($userprojs)>0):
                $projids="";
                foreach($userprojs AS $key=>$userproj):
                    if($key==0):
                        $projids.=$userproj['projectid'];
                    else:
                        $projids.=",".$userproj['projectid'];
                    endif;
                endforeach;
                $condition=" AND a.project_id IN (".$projids.") ";
            else:
                $condition="";
            endif;
        }
        $connection = Yii::$app->db;
        $sql="SELECT a.Id,a.User_Id,a.Purpose,a.Amount,a.debitacnt,a.date,DATE_FORMAT(a.date,'%d %b %y') AS date,b.username,c.Name,a.project_id FROM fundreceipt AS a inner join user as b on a.User_Id=b.id INNER JOIN projects as c on a.project_id=c.Project_Id WHERE a.Status='1' AND payment='1' $condition order by a.date ASC";
        //echo $sql;exit;
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $dataProvider=$dataReader->readAll();
        $datarows='';
        $count=0;
        $datarows.='<div class="row cashhead vouchertablehead">

                        <div class="col-md-3 headfrst">
                            <div class="row">
                                <div class="col-md-3">
                                    <label>#</label>
                                </div>
                                <div class="col-md-3">
                                    <label>&nbsp;</label>
                                </div>
                                <div class="col-md-6">
                                   <label>Date</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9 "><div class="row">
                        <div class="col-md-3">
                            <label>Project</label>
                        </div>
                        
                            <div class="col-md-4">
                                <label>Account Head</label>
                            </div>
                            
                            <div class="col-md-2">
                                <label style="/*text-align: right;*/">Amount</label>
                            </div>
                           
                        <div class="col-md-3"></div>
                        </div></div>
                        
                </div>';
                //print_r($dataProvider);exit;
        if(count($dataProvider)>0):
            foreach($dataProvider AS $key=>$data):

                $accounthead = AccountsItem::find()->where(['id'=> $data['debitacnt']])->andWhere(['Status'=>0])->one();
                $content=$this->accntheaddetailsreceipt($data['Id']);
                $datarows.='<div class="row voucherss" id="cashreceipt-'.$data['Id'].'">

                <div class="col-md-3 headfrst">
                    <div class="row frstrw">
                    <div class="headfrst1 col-md-3 numsl"><label></label><span class="number voucher-number"><b>'.++$count.'</b></span></div>
                <div class="headfrst1 col-md-3 tooltips">
                            <label></label>
                            <span class="tooltip-user">
                                <span class="icon-user3"></span>
                                <span class="tooltip-content">
                                '.$data['username'].'</span>
                            </span>
                             </div>
                             <div class="headfrst1 col-md-6">
                                <label></label>
                                <span class="date"><em class="cal-icon icon-calendar1"></em>'.$data['date'].'</span>
                                </div>

                    </div>
                </div>
                <div class="col-md-9 "><div class="row scndrow">
                <div class="scndrow1 col-md-3" style="/*bottom: 8px;*/">
                            <label></label>
                            <span>'.Projects::findOne($data['project_id'])->Name.'</span></div>
                            
                            <div class="scndrow1 col-md-4 type">

                                <div class="hover"> 
                                <label></label>       
                                <span class="prpse">'.$accounthead->name.'</span> 
                                <div class="tooltiptable" id="tooltip'.$data['Id'].'" style="width:600px;">
                                <table cellpadding="0" cellspacing="0" width="100%">
                                <tr><th>Purpose</th></tr>
                                '.$content.'
                                </table>
                                </div>
                                </div>
                                </div>
                                
                            <div class="scndrow1 col-md-2 type" style="text-align: right;">
                                <label></label>
                                <span class="voucheramnt">'.number_format((float)$data['Amount'], 2).'</span>
                            </div>
                            <div class="col-md-3 text-right icon-groups" style="bottom: 10px;">
                <a href="#" class="btn btn-primary text-button generate-voucher-btn" id="G-cashreceipt-'.$data['Id'].'" data-v="generatereceipt"  data-id="'.$data['Id'].'" value="'.$data['Id'].'" title="Generate voucher"><span class="icon-receipt"></span>Generate Voucher</a>
                <a href="#" class="btn btn-danger icon-trash1 deletereceiptvoucher" data-id="'.$data['Id'].'" id="deletereceiptvoucher'.$data['Id'].'" title="Delete Receipt Voucher"></a></div></div></div></div>';



/*
                $datarows.='<div class="row" id="cashreceipt-'.$data['Id'].'><div class="col-md-12">
                <h5>'.Projects::findOne($data['project_id'])->Name.'</h5></div>
                <div class="col-md-3">
                <span class="icon-user3"></span> <em class="username">'.$data['username'].'</em></div>
                <div class="col-md-6"><div class="row"><div class="col-md-8 type">
                    <label>Purpose</label>
                    <span>'.$data['Purpose'].'</span> <span class="devider">|</span> <span class="date"><em class="cal-icon icon-calendar1"></em>'.$data['date'].'</span></div>
                <div class="col-md-4 type">
                    <label>Amount</label>
                    <span>'.number_format((float)$data['Amount'], 2).'</span>
                </div></div></div>
                <div class="col-md-3 text-right icon-groups">
                <a href="#" class="btn btn-primary text-button generate-voucher-btn" id="G-cashreceipt-'.$data['Id'].'" data-v="generatereceipt"  data-id="'.$data['Id'].'" value="'.$data['Id'].'" title="Generate voucher"><span class="icon-receipt"></span>Generate Voucher</a>
                <a href="#" class="btn btn-danger icon-trash1 deletereceiptvoucher" data-id="'.$data['Id'].'" id="deletereceiptvoucher'.$data['Id'].'" title="Delete Receipt Voucher"></a>
                </div></div>';*/
            endforeach;
        else:
            $datarows='<div class="row"><div class="col-md-12"><div class="text-center">No Cash Receipt Found</div></div></div>';
        endif;
        $arr = array('result' => $datarows,'error'=>'No');
        return json_encode($arr);
    }

    public function actionBanksearch()
    {
        $userid = Yii::$app->user->id;
        $user = User::find()->where(['id'=>$userid])->one();
        if($user['superuser']==1 || $user['superuser']==2)
        {
            $condition="";
        }
        else
        {
            $userprojs=UserProjects::find()->where(['userid' => $userid])->all();
            if(count($userprojs)>0):
                $projids="";
                foreach($userprojs AS $key=>$userproj):
                    if($key==0):
                        $projids.=$userproj['projectid'];
                    else:
                        $projids.=",".$userproj['projectid'];
                    endif;
                endforeach;
                $condition=" AND a.project_id IN (".$projids.") ";
            else:
                $condition="";
            endif;
        }
        $connection = Yii::$app->db;

        $sql="SELECT a.Id,a.User_Id,a.Purpose,a.place,a.alloted_amount,a.date,a.Contra,a.project_id,a.edit,b.username,c.Name,a.credit_account,a.date_tmp FROM finance_requests AS a inner join user as b on a.User_Id=b.id INNER JOIN projects as c on a.project_id=c.Project_Id WHERE a.Status='1' AND (a.payment='2' OR a.payment='') AND ((a.contra=1 AND a.Contratype=2) OR a.contra=0) $condition order by a.date_tmp ASC";

        //echo $sql;exit;
        /*if($_POST['name']!='')
            $sql.="AND b.username LIKE '%".$_POST['name']."%'";*/
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $dataProvider=$dataReader->readAll();

        
        $count=0;
        $datarows='';
        $datarows.='<div class="row bankhead vouchertablehead">
                    <div class="col-md-3 headfrst">
                            <div class="row">
                                <div class="col-md-3">
                                    <label>#</label>
                                </div>
                                <div class="col-md-3">
                                    <label>&nbsp;</label>
                                </div>
                                <div class="col-md-6">
                                   <label>Date</label>
                                </div>
                            </div>
                        </div>
                    <div class="col-md-9 "><div class="row">
                    <div class="col-md-3">
                        <label>Project</label>
                    </div>
                    <div class="col-md-4">
                        <label>Account Head</label>
                    </div>
                    <div class="col-md-2">
                        <label style="/*text-align: right;*/">Amount</label>
                    </div>
                    <div class="col-md-3">
                    </div></div></div>
               </div>';
        if(count($dataProvider)>0):
            foreach($dataProvider AS $key=>$data):
                if($data['Contra']==1)
                {
                    $link='<a href="#" class="btn btn-primary text-button generate-voucher-btn" id="G-banksearch-'.$data['Id'].'" data-v="contravoucher" data-id="'.$data['Id'].'"><span class="icon-receipt"></span>Generate Voucher</a>';
                }
                else
                {
                    $link='<a href="#" class="btn btn-primary text-button generate-voucher-btn" id="G-banksearch-'.$data['Id'].'" data-v="generatepayment" data-id="'.$data['Id'].'"><span class="icon-receipt"></span>Generate Voucher</a>';
                }
                /*if($data['edit']==1):
                    $button="<td></td><td></td>";
                else:
                */    
                $delbutton='<a href="#" class="btn btn-danger icon-trash1 deletebankpayvoucher" data-id="'.$data['Id'].'" id="deletepayvoucher'.$data['Id'].'" title="Delete Payment Voucher"></a>';
                /*endif;*/
                if($user['superuser']==1):
                    $editbtn="<td><button type='button' class='btn btn-primary editpayment' value='".$data['Id']."' id='editpayment".$data['Id']."' title='Edit Payment'> <span class='glyphicon glyphicon-pencil'></span></button>
                            <button type='button' class='btn btn-primary savepayment' value='".$data['Id']."' id='savepayment".$data['Id']."' title='Save'> <span class='glyphicon glyphicon-save'></span></button></td>";
                endif;
                $accounthead = AccountsItem::find()->where(['id'=> $data['credit_account']])->andWhere(['Status'=>0])->one();

                $contents=$this->accntheaddetails($data['Id']);
                $datarows.='<div class="row bankss" id="banksearch-'.$data['Id'].'">
                <div class="col-md-3 headfrst">
                    <div class="row frstrw">
                          <div class="headfrst1 col-md-3 banknum" style="padding-top: 2px;">
                            <label></label>
                            <span class="number bank-number"><b>'.++$count.'</b></span>
                        </div>
                            <div class="headfrst1 col-md-3 bankuser" style="padding-top: 2px;">
                                <label></label>
                                <span class="tooltip-user">
                                <span class="icon-user3"></span>
                                <span class="tooltip-content">
                                 <em class="username">'.$data['username'].'</em></span>
                                 </span>
                             </div>
                        <div class="headfrst1 col-md-6 type">
                                <label></label>
                                <span class="date"><em class="cal-icon icon-calendar1"></em>'.$data['date'].'</span>
                            </div>
                    </div>
                </div>
              <div class="col-md-9"><div class="row scndrow">
                <div class="scndrow1 col-md-3" style="/*bottom: 8px;*/">
                <label></label>
                <span>'.Projects::findOne($data['project_id'])->Name.'</span></div>
                
                
                <div class="scndrow1 col-md-4 type">
                    <div class="hover">
                    <label></label>
                    <span class="purp">'.$accounthead->name.'</span>
                    <div class="tooltiptable" id="tooltip'.$data['Id'].'" style="width:600px;">
                        <table cellpadding="0" cellspacing="0" width="100%">
                        <tr><th>Purpose</th></tr>
                        '.$contents.'
                        </table>
                        </div> 
                    </div>
                    </div>
                    
                <div class="scndrow1 col-md-2 type" style="text-align:right;">
                    <label></label>
                    <span class="bankamn">'.number_format((float)$data['alloted_amount'], 2).'</span>
                </div>
                <div class="col-md-3 text-right icon-groups" style="bottom: 0px;">';
                $datarows.=$link; 
                $datarows.=$delbutton;   
                $datarows.='</div></div></div></div>';
            endforeach;
        else:
            $datarows='<div class="row"><div class="col-md-12"><div class="text-center">No Bank Voucher Found</div></div></div>';
        endif;
        $arr = array('result' => $datarows,'error'=>'No');
        return json_encode($arr);
    }

    public function actionBankreceipt()
    {
        $userid = Yii::$app->user->id;
        $user = User::find()->where(['id'=>$userid])->one();
        if($user['superuser']==1 || $user['superuser']==2)
        {
            $condition="";
        }
        else
        {
            $userprojs=UserProjects::find()->where(['userid' => $userid])->all();
            if(count($userprojs)>0):
                $projids="";
                foreach($userprojs AS $key=>$userproj):
                    if($key==0):
                        $projids.=$userproj['projectid'];
                    else:
                        $projids.=",".$userproj['projectid'];
                    endif;
                endforeach;
                $condition=" AND a.project_id IN (".$projids.") ";
            else:
                $condition="";
            endif;
        }
        $connection = Yii::$app->db;
        $sql="SELECT a.Id,a.User_Id,a.Purpose,a.project_id,a.Amount,a.debitacnt,a.date,DATE_FORMAT(a.date,'%d %b %y') AS date,b.username,c.Name FROM fundreceipt AS a inner join user as b on a.User_Id=b.id INNER JOIN projects as c on a.project_id=c.Project_Id WHERE a.Status='1' AND payment='2' $condition order by a.date ASC";
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $dataProvider=$dataReader->readAll();
        $datarows='';
        $count=0;
        $datarows.='<div class="row cashhead vouchertablehead">


                        <div class="col-md-3 headfrst">
                            <div class="row">
                                <div class="col-md-3">
                                    <label>#</label>
                                </div>
                                <div class="col-md-3">
                                    <label>&nbsp;</label>
                                </div>
                                <div class="col-md-6">
                                   <label>Date</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9 "><div class="row">
                        <div class="col-md-3">
                            <label>Project</label>
                        </div>
                        
                            <div class="col-md-4">
                                <label>Account Head</label>
                            </div>
                            
                            <div class="col-md-2">
                                <label style="/*text-align: right;*/">Amount</label>
                            </div>
                           
                        <div class="col-md-3"></div></div></div>
                        
                </div>';
        if(count($dataProvider)>0):
            foreach($dataProvider AS $key=>$data):
                 $accounthead = AccountsItem::find()->where(['id'=> $data['debitacnt']])->andWhere(['Status'=>0])->one();

                $contents=$this->accntheaddetailsreceipt($data['Id']);

                $datarows.='<div class="row bankss" id="banksearch-'.$data['Id'].'">

                <div class="col-md-3 headfrst">
                    <div class="row frstrw">
                        <div class="headfrst1 col-md-3 banknum" style="padding-top: 2px;">
                            <label></label>
                            <span class="number bank-number"><b>'.++$count.'</b></span>
                        </div>
                        <div class="headfrst1 col-md-3 bankuser" style="padding-top: 2px;">
                            <label></label>
                            <span class="tooltip-user">
                            <span class="icon-user3"></span>
                            <span class="tooltip-content">
                             <em class="username">'.$data['username'].'</em></span>
                             </span>
                         </div>
                <div class="headfrst1 col-md-6 type">
                        <label></label>
                        <span class="date"><em class="cal-icon icon-calendar1"></em>'.$data['date'].'</span>
                    </div>

                    </div>
                </div>
                <div class="col-md-9"><div class="row scndrow">  
                <div class="scndrow1 col-md-3" style="/*bottom: 8px;*/">
                <label></label>
                <span>'.Projects::findOne($data['project_id'])->Name.'</span></div>
                
                
                <div class="scndrow1 col-md-4 type">
                    <div class="hover">
                    <label></label>
                    <span class="purp">'.$accounthead->name.'</span>
                    <div class="tooltiptable" id="tooltip'.$data['Id'].'" style="width:600px;">
                        <table cellpadding="0" cellspacing="0" width="100%">
                        <tr><th>Purpose</th></tr>
                        '.$contents.'
                        </table>
                        </div> 
                    </div>
                    </div>
                    
                <div class="scndrow1 col-md-2 type" style="text-align:right;">
                    <label></label>
                    <span class="bankamn">'.number_format((float)$data['Amount'], 2).'</span>
                </div>
                <div class="col-md-3 text-right icon-groups" style="bottom: 0px;">
                <a href="#" class="btn btn-primary text-button generate-voucher-btn" id="G-bankreceipt-'.$data['Id'].'" data-v="generatereceipt"  data-id="'.$data['Id'].'" title="Generate voucher"><span class="icon-receipt"></span>Generate Voucher</a>
                <a href="#" class="btn btn-danger icon-trash1 deletebankreceiptvoucher" data-id="'.$data['Id'].'" id="deletereceiptvoucher'.$data['Id'].'" title="Delete Receipt Voucher"></a>
                </div></div></div></div>';


                /*$datarows.='<div class="row" id="bankreceipt-'.$data['Id'].'"><div class="col-md-12">
                <h5>'.Projects::findOne($data['project_id'])->Name.'</h5></div>
                <div class="col-md-3">
                <span class="icon-user3"></span> <em class="username">'.$data['username'].'</em></div>
                <div class="col-md-6"><div class="row"><div class="col-md-8 type">
                    <label>Purpose</label>
                    <span>'.$data['Purpose'].'</span> <span class="devider">|</span> <span class="date"><em class="cal-icon icon-calendar1"></em>'.$data['date'].'</span></div>
                <div class="col-md-4 type">
                    <label>Amount</label>
                    <span>'.number_format((float)$data['Amount'], 2).'</span>
                </div></div></div>
                <div class="col-md-3 text-right icon-groups">
                <a href="#" class="btn btn-primary text-button generate-voucher-btn" id="G-bankreceipt-'.$data['Id'].'" data-v="generatereceipt"  data-id="'.$data['Id'].'" title="Generate voucher"><span class="icon-receipt"></span>Generate Voucher</a>
                <a href="#" class="btn btn-danger icon-trash1 deletebankreceiptvoucher" data-id="'.$data['Id'].'" id="deletereceiptvoucher'.$data['Id'].'" title="Delete Receipt Voucher"></a>
                </div></div>';*/
            endforeach;
        else:
            $datarows='<div class="row"><div class="col-md-12"><div class="text-center">No Bank Receipt Found</div></div></div>';
        endif;
        $arr = array('result' => $datarows,'error'=>'No');
        return json_encode($arr);
    }

    public function actionJournals()
    {
        $userid = Yii::$app->user->id;
        $user = User::find()->where(['id'=>$userid])->one();
        if($user['superuser']==1 || $user['superuser']==2)
        {
            $condition="";
        }
        else
        {
            $userprojs=UserProjects::find()->where(['userid' => $userid])->all();
            if(count($userprojs)>0):
                $projids="";
                foreach($userprojs AS $key=>$userproj):
                    if($key==0):
                        $projids.=$userproj['projectid'];
                    else:
                        $projids.=",".$userproj['projectid'];
                    endif;
                endforeach;
                $condition=" AND a.projectid IN (".$projids.") ";
            else:
                $condition="";
            endif;
        }
        $connection = Yii::$app->db;
        //$sql="SELECT a.User_Id,DATE_FORMAT(a.date,'%d %b %y') AS date,a.narration,b.username,c.id,c.amount,a.projectid FROM journels AS a inner join users as b on a.User_Id=b.id inner join journalitems as c on a.id=c.journalid WHERE a.Status='1' AND c.Status='0' ORDER BY a.id DESC";

        $datarows='';
        
         $datarows='<div class="row vouchertablehead">
                        <div class="col-md-1">
                            <label>#</label>
                        </div>
                        <div class="col-md-1">
                            <label></label>
                        </div>
                        <div class="col-md-2">
                            <label>Date</label>
                        </div>
                        
                        <div class="col-md-2">
                            <label>Debit Account Head</label>
                        </div>
                        <div class="col-md-2">
                            <label>Credit Account Head</label>
                        </div>
                        
                        <div class="col-md-1">
                            <label style="/*text-align: right;*/">Amount</label>
                        </div>
                        <div class="col-md-3">
                            
                        </div>
                    </div>
                  ';
 

        $sql1="SELECT a.id,a.projectid,DATE_FORMAT(a.date,'%d %b %y') AS date,a.narration,b.username,a.projectid,a.creditacnt,a.debitacnt FROM journels AS a inner join user as b on a.User_Id=b.id WHERE a.Status='1' $condition GROUP BY a.projectid ORDER BY a.id DESC";

        $command=$connection->createCommand($sql1);
        $dataReader=$command->query();
        $items=$dataReader->readAll();

        foreach ($items as $key => $item) {
            $count=0;

            $sql="SELECT a.id,DATE_FORMAT(a.date,'%d %b %y') AS date,a.narration,b.username,a.projectid,a.creditacnt,a.debitacnt FROM journels AS a inner join user as b on a.User_Id=b.id WHERE a.Status='1' $condition AND a.projectid=$item[projectid] ORDER BY a.id DESC";
        //echo $sql;exit;
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $dataProvider=$dataReader->readAll();

            $projname=Projects::findOne($item['projectid']);
             //print_r($cartitem['Project']);exit;
             $datarows.='<div class="row" style="background-color: #f7f8fc;"><div  class="col-md-12" ><b>'.$projname['Name'].'</b></div></div>';

            
            if(count($dataProvider)>0):
                foreach($dataProvider AS $key=>$data):
                    $journalitems=Journalitems::find()->where(['journalid' => $data['id']])->orderBy(['id' => SORT_DESC])->all();
                    $totalamount=0;
                    $vendordbtacn = '';
                    foreach($journalitems AS $journalitem):
                        $totalamount=$totalamount + $journalitem['amount'];
                        $vendordebtacnt=AccountsItem::find()->where(['id' =>$journalitem['debitaccnt']])->andWhere(['Status'=>0])->one();
                        if(!empty($vendordebtacnt)){
                            $vendordbtacn = $vendordebtacnt->name; 
                        }else{
                            $vendordbtacn = '';
                        }

                    endforeach;
                    $vendorcreditacnt=AccountsItem::find()->where(['id' =>$data['creditacnt']])->andWhere(['Status'=>0])->one();
                    

                    if(!empty($vendorcreditacnt->name)){
                        $vendoracn = $vendorcreditacnt->name; 
                    }else{
                        $vendoracn = '';
                    }
                   
                    $content=$this->journalnarration($data['id']);
                    $datarows.='<div class="row journalss" id="journelsrow-'.$data['id'].'">
                    <div class="col-md-1 journnum">
                        <label></label>
                        <span class="number journal-number"><b>'.++$count.'</b></span>
                    </div>

                    <div class="col-md-1 tooltipjournal">
                    <label></label>
                    <span class="tooltip-user">
                    <span class="icon-user3"></span> 
                    <span class="tooltip-content">
                    <em class="username">'.$data['username'].'</em></span>
                    </span>
                    </div>

                     <div class="col-md-2">
                        <label></label>
                        <span class="date"><em class="cal-icon icon-calendar1"></em>'.$data['date'].'</span>
                        </div>

                    
                    
                    

                        <div class="col-md-2">
                        <label></label>
                        <span class="journprpse" id="journalpurpose"'.$data['id'].'">'.$vendordbtacn.'</span>

                        </div>

                        <div class="col-md-2 type">
                        <div class="hover">
                        <label></label>
                        <span class="journprpse" id="journalpurpose"'.$data['id'].'">'.$vendoracn.'</span> 
                        <div class="tooltiptable" id="tooltip'.$data['id'].'" style="width:600px;">
                            <table cellpadding="0" cellspacing="0" width="100%">
                                <tr><th>Purpose</th></tr>
                                    '.$content.'
                            </table>
                        </div>
                        </div>
                        </div>
                       
                    <div class="col-md-1 type" style="text-align:right;">
                        <label></label>
                        <span class="journamnt" id="journalamount'.$data['id'].'">'.number_format((float)$totalamount, 2).'</span>
                    </div>
                    <div class="col-md-3 text-right icon-groups" style="bottom: 0px;">
                    <a href="#" class="btn btn-primary text-button generate-voucher-btn" data-id="'.$data['id'].'" data-v="journelvoucher" id="G-journelvoucher'.$data['id'].'" title="Generate voucher"><span class="icon-receipt"></span>Generate Voucher</a>
                    <a href="#" class="btn btn-danger icon-trash1 deletejournalsbutton" data-id="'.$data['id'].'" value="'.$data['id'].'" id="deletejournalsbutton'.$data['id'].'" title="Delete Journal Request"></a>
                    </div></div>';
                endforeach;
               
        



        
        else:
            $datarows='<div class="row"><div class="col-md-12"><div class="text-center">No Journals Found</div></div></div>';
        endif;
    }
        $arr = array('result' => $datarows,'error'=>'No');
        return json_encode($arr);
    }

    public function actionGeneratepayment(){

        if($_POST['generateID']){
            $connection = Yii::$app->db;
            $generateID=$_POST['generateID'];
            $sql="SELECT a.Id,a.project_id,a.IOW_Id,a.place,a.Purpose,a.alloted_amount,a.date,a.Requested_On,a.payment,a.account_id,a.transfer,a.credit_account,b.Name,c.name,c.servicetax,c.igst FROM finance_requests AS a INNER JOIN projects AS b ON a.project_id=b.Project_Id left JOIN accounts_item AS c ON a.account_id=c.id WHERE a.Id='".$generateID."' AND c.Status=0";
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();

            $data=$dataReader->read();
            $sql3="SELECT Name FROM projects WHERE Project_Id='".$data['project_id']."'";
            $command=$connection->createCommand($sql3);
            $dataReader=$command->query();
            $project=$dataReader->read();
            //if($data['payment']=='1'):$type='Cash' ;else: $type='Bank';endif;
            $sql2="SELECT schedule,account_type FROM accounts_item WHERE id='".$data['account_id']."' ";
            $command=$connection->createCommand($sql2);
            $dataReader=$command->query();
            $account=$dataReader->read();
            $sql="SELECT id FROM accounts_item WHERE id='".$data['account_id']."' AND account_type='".$data['payment']."' ";
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $credit=$dataReader->read();
            $userid=Yii::$app->user->id;
            $sql4 = "SELECT * FROM `user_projects` WHERE `userid` = ".$userid." ORDER BY projectid ASC";
            //echo $sql4;exit;
            $command=$connection->createCommand($sql4);
            $dataReader=$command->query();
            $userproject=$dataReader->readAll();
            $arr=array();
            foreach($userproject AS $userproj):
                array_push($arr,$userproj['projectid']);
            endforeach;
            //print_r($arr);exit;
            if(in_array("12", $arr)):
                $sql1="select id,name,projectid from accounts_item where account_type='2' ";
            else:
                $sql1="select a.id,a.name,a.projectid from accounts_item as a INNER JOIN user_accounts as b on a.id=b.account_id where a.account_type='2' AND b.user_id=".$userid." ";
            endif;
            //echo $sql1;exit;
            $command=$connection->createCommand($sql1);
            $dataReader=$command->query();
            $banks=$dataReader->readAll();

            $dataVrow ='';
            $dataVrow.='<div class="row"><form id="generateVfrom" ><div class="text-center">';
            if($data['payment']=='1'){
                $dataVrow.='<h3>Cash Payment Voucher</h3></div>';
            }else{
                $dataVrow.='<h3>Bank Payment Voucher</h3></div>';
            }
            if($data['payment']=='2'){
            $dataVrow.='<div class="col-md-4 type"><div class="form-group">
                        <label>Date</label>
                        <input type="hidden" value="'.$data['Id'].'" id="voucherid">
                        <input type="date" id="Gpaymentdate" class="form-control" name="paymentdate" value="'.date('Y-m-d',strtotime($data['date'])).'"><span class="error"></span></div></div>';
                        }else{
                            $dataVrow.='<div class="col-md-4 type"><div class="form-group">
                        <label>Date</label>
                        <input type="hidden" value="'.$data['Id'].'" id="voucherid">
                        <input type="date" id="Gpaymentdate" class="form-control" name="paymentdate" value="'.date('Y-m-d',strtotime($data['date'])).'"><span class="error"></span></div></div>';
                        }
                 $dataVrow.='<div class="col-md-4 type">
                            <div class="form-group">
                                <label>Project</label>
                                <select class="form-control" id="Gprojectid" name="projectid">
                                    <option value="0">Select Project</option>';
            $project=Projects::find()->where(['Project_Delete_Status' => 0])->all();
            foreach($project AS $projects):
                if($data['project_id']==$projects->Project_Id):$selected='selected';else:$selected='';endif;
                $dataVrow.='<option value="'.$projects->Project_Id.'"'. $selected.'>'.$projects->Name.'</option>';
            endforeach;
                    $dataVrow.='</select><span class="error"></span>
                                <input type="hidden" value="" name="project">
                            </div>
                        </div>
                        <div class="col-md-4 type">
                            <div class="form-group">
                                <label>Place</label>
                                <select class="form-control" id="Gplace" name="place">
                                    <option value="0">Select Place</option>';
            $project=Projects::find()->where(['Project_Delete_Status' => 0])->all();
            foreach($project AS $projects):
                if($projects->Project_Id==12):$selected='selected';else:$selected='';endif;
                $dataVrow.='<option value="'.$projects->Project_Id.'"'. $selected.'>'.$projects->Name.'</option>';
            endforeach;


            $accounthead = AccountsItem::find()->where(['id'=> $data['credit_account']])->one();
            $accountheads = AccountsItem::find()->where(['id'=> $data['account_id']])->one();
                    $dataVrow.='</select><span class="error"></span>
                            </div>
                        </div>

                       <div class="col-md-4 type">
                    <div class="form-group">
                        <label>Account Head</label>
                         <span>'.$accounthead->name.'</span>
                    </div>
                </div>

                        <div class="col-md-4 type" style="display:none">
                            <div class="form-group">
                                <label>Bank</label>
                                <span>'.$accountheads->name.'</span>
                                <input type="hidden" value="'.$data['credit_account'].'" name="debitacnt">
                                <input type="hidden" value="'.$credit['id'].'" name="creditacnt">
                            </div>
                        </div>';
            if($data['payment']=='2'){
                    $dataVrow.='<div class="col-md-4 type">
                    <div class="form-group">
                        <label>Amount</label>
                        <input type="hidden" value="'.$data['alloted_amount'].'" name="allotedamount">
                        <span>'.number_format((float)$data['alloted_amount'], 2).'</span>
                    </div>
                </div>

                    <div class="col-md-4 type">
                    <div class="form-group">
                        <input type="hidden" value="'.$data['Id'].'" name="requestid">
                        <input type="hidden" value="'.$data['payment'].'" name="payment">
                        <input type="hidden" value="'.$data['transfer'].'" name="transfer">
                        <label>Narration</label>';
            $userid = Yii::$app->user->id;
            $user = User::find()->where(['id'=>$userid])->one();
            if($user['superuser']==1){
                $dataVrow.='<input type="text" class="form-control" id="narration" name="narration" value="'.$data['Purpose'].'"><span class="error"></span>';
            }else{
                $dataVrow.='<input type="text" class="form-control" id="narration" name="narration" value="'.$data['Purpose'].'"><span class="error"></span>';
            }
                        $dataVrow.='</div>

                </div>';
            }else{ //////   
                    $dataVrow.='<div class="col-md-4 type">
                    <div class="form-group">
                        <input type="hidden" value="'.$data['Id'].'" name="requestid">
                        <input type="hidden" value="'.$data['payment'].'" name="payment">
                        <input type="hidden" value="'.$data['transfer'].'" name="transfer">
                        <label>Narration</label>
                        <input type="text" class="form-control" id="narration" name="narration" value="'.$data['Purpose'].'"><span class="error"></span>
                    </div>
                </div>
                <div class="col-md-4 type heightadjust">
                    <div class="form-group">
                        <label>Amount</label>';
            $userid = Yii::$app->user->id;
            $user = User::find()->where(['id'=>$userid])->one();
                if($user['superuser']==2){
                    $dataVrow.='<input type="hidden" value="'.$data['alloted_amount'].'" name="allotedamount"><span class="error"></span>';
                }else{
                    $dataVrow.='<input type="hidden" value="'.$data['alloted_amount'].'" name="allotedamount">
                                <span>'.number_format((float)$data['alloted_amount'], 2).'</span>';
                }
                    $dataVrow.='</div></div>';
            }

            $bsitems = Bsitems::find()->where(['status'=> 0])->andWhere(['accnt_id'=> $data['credit_account']])->all();

            //optional Area
            if($data['payment']=='2' && $bsitems){

                $dataVrow.='
                <div class="col-md-4 type">
                    <div class="form-group">
                        <label>Cheque No</label>
                        <input type="text" class="form-control" name="chequenum" id="chequenum"><span class="error" style="color: red;"></span>
                    </div>
                </div>';

            }
            elseif($data['payment']=='2' && empty($bsitems)){

                $dataVrow.='
                <div class="col-md-4 type">
                    <div class="form-group">
                        <label>Cheque No</label>
                        <input type="text" class="form-control" name="chequenum" id="chequenum"><span class="error" style="color: red;"></span>
                    </div>
                </div>
           
                <div class="col-md-4 type heightadjust"><div class="form-group"></div></div>

                <div class="col-md-4 type" style="display:none;">
                    <div class="form-group">
                        <label>Bank</label>
                        <select class="form-control" name="bankid" id="bankid">
                            <option value="0">Select Bank</option>';
                foreach($banks AS $key=>$bank):
                   $dataVrow.='<option value="'.$bank['id'].'" '.$selected.' >'.$bank['name'].'</option>';
                endforeach;
                    $dataVrow.='</select><span class="error" style="color: red;"></span>
                        </div>
                    </div>
                    <div class="col-md-4 type heightadjust"><div class="form-group"></div></div>';

            }

            if($account['account_type']=='5' && $bsitems){

                $dataVrow.='<div class="col-md-4 type">
                <div class="form-group">
                    <label>Select IOW</label>
                    <select class="form-control" name="iowid" id="iowid">
                        <option value="none">Select IOW</option>';
                $iows=itemofworks::find()->where(['Project_Id' => $data['place']])->all();
                foreach($iows AS $items):
                    if($items['IOW_Id']==$data['IOW_Id']){
                        $dataVrow.='<option value="'.$items['IOW_Id'].'" selected>'.$items['Name'].'</option>';
                    }else{
                        $dataVrow.='<option value="'.$items['IOW_Id'].'">'.$items['Name'].'</option>';
                    }
                endforeach;
                    $dataVrow.='</select><span class="error"></span>
                        </div>
                    </div>
                    <div class="col-md-4 type">
                        <div class="form-group">
                            <label>Resource</label>
                            <select class="form-control" data-id="0" name="resourceid">
                                <option value="none">Select Resource</option>';
                    $typelist = Resources::find()->all();
                    foreach ($typelist AS $list):
                        //$dataVrow.='"<option value="'. $list->Resource_Id .'">' . $list->Name . '</option>"';
                    endforeach;
                $accountheads = AccountsItem::find()->where(['id'=> $data['account_id']])->one();
                    $dataVrow.='</select><span class="error"></span>
                        </div>
                    </div>';

            }
            elseif($account['account_type']=='5' && empty($bsitems)){

                $dataVrow.='<div class="col-md-4 type">
                <div class="form-group">
                    <label>Select IOW</label>
                    <select class="form-control" name="iowid" id="iowid">
                        <option value="none">Select IOW</option>';
                $iows=itemofworks::find()->where(['Project_Id' => $data['place']])->all();
                foreach($iows AS $items):
                    if($items['IOW_Id']==$data['IOW_Id']){
                        $dataVrow.='<option value="'.$items['IOW_Id'].'" selected>'.$items['Name'].'</option>';
                    }else{
                        $dataVrow.='<option value="'.$items['IOW_Id'].'">'.$items['Name'].'</option>';
                    }
                endforeach;
                    $dataVrow.='</select><span class="error"></span>
                        </div>
                    </div>
                    <div class="col-md-4 type">
                        <div class="form-group">
                            <label>Resource</label>
                            <select class="form-control" data-id="0" name="resourceid">
                                <option value="none">Select Resource</option>';
                    $typelist = Resources::find()->all();
                    foreach ($typelist AS $list):
                        //$dataVrow.='"<option value="'. $list->Resource_Id .'">' . $list->Name . '</option>"';
                    endforeach;
                $accountheads = AccountsItem::find()->where(['id'=> $data['account_id']])->one();
                    $dataVrow.='</select><span class="error"></span>
                        </div>
                    </div>
                    <div class="col-md-4 type heightadjust">
                    <div class="form-group">
                    </div></div>';

            }

            if($account['schedule']=='3' && $bsitems){

                $dataVrow.='<div class="col-md-4 type">
                    <div class="form-group">
                        <label>Schedule</label>
                        <select class="form-control" name="schedule" id="schedule">
                            <option value="none">Select Resource</option>';
                $schedule=Resources::find()->where(['ResourceType_Id'=>26])->andWhere(['pricing_status' => 0])->orderBy(['Name' => SORT_ASC])->all();
                foreach($schedule AS $items):
                    $dataVrow.='<option value="'.$items['Resource_Id'].'">'.$items['Name'].'</option>';
                endforeach;
                $dataVrow.='</select><span class="error"></span>
                    </div>
                </div>';

            }
            elseif($account['schedule']=='3' && empty($bsitems)){

                $dataVrow.='<div class="col-md-4 type">
                    <div class="form-group">
                        <label>Schedule</label>
                        <select class="form-control" name="schedule" id="schedule">
                            <option value="none">Select Resource</option>';
                $schedule=Resources::find()->where(['ResourceType_Id'=>26])->andWhere(['pricing_status' => 0])->orderBy(['Name' => SORT_ASC])->all();
                foreach($schedule AS $items):
                    $dataVrow.='<option value="'.$items['Resource_Id'].'">'.$items['Name'].'</option>';
                endforeach;
                $dataVrow.='</select><span class="error"></span>
                    </div>
                </div>
                <div class="col-md-4 type"><div class="form-group"></div></div><div class="col-md-4 type"><div class="form-group"></div></div>';

            }

            if($data['payment']=='2' && $bsitems){

                $dataVrow.='<div class="col-md-4 type scheduleitemdrop">';

                $dataVrow.='
                        <div class="form-group">
                            <label>Schedule Item</label>
                            <select class="form-control" name="accntschitem" id="accntschitem">
                                <option value="none">Select Schedule Item</option>';

                foreach($bsitems as $key => $bsitem):  
                    if($key==0):
                        $selected="selected"; 
                    else:
                        $selected="";
                    endif; 

                    $dataVrow.= '<option value="'.$bsitem->item_id.'" '.$selected.'>'.$bsitem->itemname.'</option>';

                endforeach;

                $dataVrow.='   </select><span class="error"></span>
                        </div>';

                $dataVrow.='
                    </div>
                    <div class="col-md-4 type heightadjust">
                        <div class="form-group">
                        </div>
                    </div>';

            }
            elseif($data['payment']!='2' && $bsitems){

                $dataVrow.='<div class="col-md-4 type scheduleitemdrop">';

                $dataVrow.='
                        <div class="form-group">
                            <label>Schedule Item</label>
                            <select class="form-control" name="accntschitem" id="accntschitem">
                                <option value="none">Select Schedule Item</option>';

                foreach($bsitems as $key => $bsitem):  
                    if($key==0):
                        $selected="selected"; 
                    else:
                        $selected="";
                    endif; 

                    $dataVrow.= '<option value="'.$bsitem->item_id.'" '.$selected.'>'.$bsitem->itemname.'</option>';

                endforeach;

                $dataVrow.='   </select><span class="error"></span>
                        </div>';

                $dataVrow.='
                    </div>
                    <div class="col-md-4 type heightadjust">
                        <div class="form-group">
                        </div>
                    </div>
                    <div class="col-md-4 type heightadjust">
                        <div class="form-group">
                        </div>
                    </div>';

            }

           /* if($data['servicetax']!=0){
                $gstper=$data['servicetax'] / 2;
                $gstamount=($data['alloted_amount'] * $gstper) /100;
                $dataVrow.='<div class="col-md-4 type"><div class="form-group">
                    <label>Account</label>
                    <span>Input CGST</span>
                    <input type="hidden" value="498" name="debitcgstacnt"></div></div>
                    <div class="col-md-4 type">
                    <div class="form-group">
                        <label>Narration</label>
                        <input type="hidden" value="1" name="gst" id="type">
                        <input type="hidden" value="'.$gstper.'" name="gstper" id="gstper">
                        <input type="text" class="form-control" name="cgstnarration" id="narration" value="Being input CGST"><span class="error"></span>
                    </div></div>
                    <div class="col-md-4 type"><div class="form-group">
                        <label>Amount</label>';
                $userid = Yii::$app->user->id;
                $user = User::find()->where(['id'=>$userid])->one();
                if($user['superuser']==2){
                $dataVrow.='<input type="text" class="form-control gstamount" value="'.$gstamount.'" name="gstamount" id="gstamount"><span class="error"></span>';
                }else{
                $dataVrow.='<input type="hidden" value="'.$gstamount.'" name="gstamount"><span>'.number_format((float)$gstamount, 2).'</span>';  
                }
                $dataVrow.='</div></div><div class="col-md-4 type"><div class="form-group">
                    <label>Account</label>
                    <span>Input SGST</span>
                    <input type="hidden" value="499" name="debitsgstacnt"></div></div>
                    <div class="col-md-4 type">
                    <div class="form-group">
                        <label>Narration</label>
                        <input type="text" class="form-control" name="sgstnarration" id="narration" value="Being input SGST"><span class="error"></span>
                    </div></div>
                    <div class="col-md-4 type"><div class="form-group">
                        <label>Amount</label>';
                $userid = Yii::$app->user->id;
                $user = User::find()->where(['id'=>$userid])->one();
                if($user['superuser']==2){
                $dataVrow.='<input type="text" class="form-control gstamount" value="'.$gstamount.'" name="gstamount" id="gstamount"><span class="error"></span>';
                }else{
                $dataVrow.='<input type="hidden" value="'.$gstamount.'" name="gstamount"><span>'.number_format((float)$gstamount, 2).'</span>';  
                }
                $dataVrow.='</div></div>';

            }elseif($data['igst']!=0){
                $gstper=$data['igst'];
                $gstamount=($data['alloted_amount'] * $gstper) /100;
                $dataVrow.='<div class="col-md-4 type"><div class="form-group">
                <label>Account</label>
                <span>Input IGST</span>
                <input type="hidden" value="506" name="debitigstacnt"></div></div>
                <div class="col-md-4 type">
                <div class="form-group">
                    <label>Narration</label>
                    <input type="hidden" value="2" name="gst" id="type">
                    <input type="hidden" value="'.$gstper.'" name="gstper" id="gstper">
                    <input type="text" class="form-control" id="narration" name="igstnarration" value="Being input IGST"><span class="error"></span>
                </div></div>
                <div class="col-md-4 type"><div class="form-group">
                    <label>Amount</label>';
                $userid = Yii::$app->user->id;
                $user = User::find()->where(['id'=>$userid])->one();
                if($user['superuser']==2){
                $dataVrow.='<input type="text" class="form-control gstamount" value="'.$gstamount.'" name="gstamount" id="gstamount"><span class="error"></span>';
                }else{
                $dataVrow.='<input type="hidden" value="'.$gstamount.'" name="gstamount"><span>'.number_format((float)$gstamount, 2).'</span>';  
                }
                $dataVrow.='</div></div>';

            }*/

            if($account['schedule']!='3' && empty($bsitems)){

                $dataVrow.='
                        <div class="col-md-4 type">
                            <div class="form-group">
                            </div>
                        </div>
                        <div class="col-md-4 text-right">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-danger cancel" id="cancelvoucher"><span class="icon-close"></span> Close</button>
                                <button type="button" class="btn btn-primary" id="generatevoucher" name="generatevoucher" value="Save"><span class="icon-check"></span> Generate Voucher</button>
                                <!-- <a href="#" class="btn btn-primary text-button" id="printvoucher123" target="_blank" style="display: none;border-radius: 25px;"><span class="icon-print"></span> Print</a> -->
                                <a href="#" id="printvoucher" target="_blank" style="display: none;color: green;">Voucher Created Successfully!<br><span class="icon-print"style="color: blue;"> Print Now</span></a>
                            </div>
                        </div>
                        <div class="col-md-4 type">
                            <div class="form-group">
                            </div>
                        </div>
                    </form>
                </div>';

            }
            elseif($account['schedule']!='3' && $account['account_type']!='5'){

                $dataVrow.='
                        <div class="col-md-4 type">
                            <div class="form-group">
                            </div>
                        </div>
                        <div class="col-md-4 text-right">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-danger cancel" id="cancelvoucher"><span class="icon-close"></span> Close</button>
                                <button type="button" class="btn btn-primary" id="generatevoucher" name="generatevoucher" value="Save"><span class="icon-check"></span> Generate Voucher</button>
                                <!-- <a href="#" class="btn btn-primary text-button" id="printvoucher123" target="_blank" style="display: none;border-radius: 25px;"><span class="icon-print"></span> Print</a> -->
                                <a href="#" id="printvoucher" target="_blank" style="display: none;color: green;">Voucher Created Successfully!<br><span class="icon-print"style="color: blue;"> Print Now</span></a>
                            </div>
                        </div>
                        <div class="col-md-4 type">
                            <div class="form-group">
                            </div>
                        </div>
                    </form>
                </div>';

            }
            else{

                $dataVrow.='<div class="col-md-4 text-right">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-danger cancel" id="cancelvoucher"><span class="icon-close"></span> Close</button>
                        <button type="button" class="btn btn-primary" id="generatevoucher" name="generatevoucher" value="Save"><span class="icon-check"></span> Generate Voucher</button>
                        <!-- <a href="#" class="btn btn-primary text-button" id="printvoucher123" target="_blank" style="display: none;border-radius: 25px;"><span class="icon-print"></span> Print</a> -->
                        <a href="#" id="printvoucher" target="_blank" style="display: none;color: green;">Voucher Created Successfully!<br><span class="icon-print"style="color: blue;"> Print Now</span></a>
                    </div>
                </div></form>
                </div>';

            }
            
        $arr = array('result' => $dataVrow,'error'=>'No');
        return json_encode($arr);

        }
    }

    public function actionJournelvoucher()
    {
        $connection = Yii::$app->db;
        $userid = Yii::$app->user->id;
        $user = User::find()->where(['id'=>$userid])->one();
        if(isset($_POST['generateID'])){
            $generateID=$_POST['generateID'];
            //$sql="SELECT a.date,a.projectid,a.place,a.narration,a.type,a.debitacnt,a.creditacnt,b.id,b.journalid,b.amount,b.debitaccnt,b.creditaccnt,c.Name as projectname,d.name FROM journels as a inner join journalitems as b on a.id=b.journalid inner join projects as c on a.projectid=c.Project_Id LEFT JOIN accounts_item as d on (a.creditacnt=d.id OR a.debitacnt=d.id) WHERE b.id='".$id."'";
            $sql="SELECT a.id,a.order_id,a.date,a.projectid,a.place,a.narration,a.type,a.debitacnt,a.creditacnt,c.Name as projectname,d.name FROM journels as a inner join projects as c on a.projectid=c.Project_Id LEFT JOIN accounts_item as d on (a.creditacnt=d.id OR a.debitacnt=d.id) WHERE a.id='".$generateID."'";
            //echo $sql;exit;
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $data=$dataReader->read();

            if($data['type']=='credit'):
                $debit=$data['debitacnt'];
                $credit=$data['creditacnt'];
                $sql1="select name from accounts_item where id='".$data['debitacnt']."' ";
                $command=$connection->createCommand($sql1);
                $dataReader=$command->query();
                $debitname=$dataReader->read();
            else:
                $debit=$data['debitacnt'];
                $credit=$data['creditacnt'];
                $sql1="select name from accounts_item where id='".$data['debitacnt']."' ";
                $command=$connection->createCommand($sql1);
                $dataReader=$command->query();
                $debitname=$dataReader->read();
            endif;

            $sql2="SELECT schedule FROM accounts_item WHERE id='".$data['debitacnt']."' ";
            $command=$connection->createCommand($sql2);
            $dataReader=$command->query();
            $account=$dataReader->read();

            $sql3="SELECT id,debitaccnt,creditaccnt,amount,narration FROM journalitems WHERE journalid='".$generateID."' ";
            $command=$connection->createCommand($sql3);
            $dataReader=$command->query();
            $journalitems=$dataReader->readAll();
            $totalamount=0;
            foreach($journalitems AS $journalitem):
                $totalamount=$totalamount + $journalitem['amount'];
            endforeach;
            $dataVrow ='';
            $dataVrow.='<div class="row"><form id="journalvoucherform" ><div class="text-center"><h3>Journal Voucher</h3></div>
                            <div class="col-md-4 type">
                                <div class="form-group">
                                    <label>Date</label>';
                if($user['superuser']==2){
                    $dataVrow.='<input type="text" id="journaldate" class="form-control" name="date"  value="'.date('d-m-Y',strtotime($data['date'])).'"><span class="error"></span>';
                }else{
                    $dataVrow.='<input type="date" class="form-control" name="date" value="'.date('Y-m-d',strtotime($data['date'])).'"><span style="display:none;">'.date('d-m-Y',strtotime($data['date'])).'</span>';
                }
                        $dataVrow.='</div></div>
                                <div class="col-md-4 type">
                                    <div class="form-group">
                                        <label>Project</label>';
                if($user['superuser']==2){
                    $dataVrow.='<select class="form-control projectid" id="projectid" name="projectid">
                                    <option value="0">Select Project</option>';
                    $project=Projects::find()->all();
                    foreach($project AS $projects):
                        if($data['projectid']==$projects->Project_Id):$selected='selected';else:$selected='';endif;
                        $dataVrow.='<option value="'.$projects->Project_Id.'" id="acnts" '.$selected.'>'.$projects->Name.'</option>';
                    endforeach;
                        $dataVrow.='</select><span class="error"></span>
                        <input type="hidden" value="'.$data['id'].'" name="journalid"></div></div>';
                }else{
                        $dataVrow.='<input type="hidden" value="'.$data['id'].'" name="journalid">
                                        <input type="hidden" value="'.$data['projectid'].'" id="projectid" name="projectid"><span class="styleclass">'.$data['projectname'].'</span>
                                        </div>
                                    </div>';
                }
                        $dataVrow.='<div class="col-md-4 type">
                        <div class="form-group">
                            <label>Place</label>';
                if($user['superuser']==2){
                    $dataVrow.='<select class="form-control placeid" id="placeid" name="place">
                                    <option value="0">Select Place</option>';
                    $project=Projects::find()->all();
                    foreach($project AS $projects):
                        if($data['place']==$projects->Project_Id):$selected='selected';else:$selected='';endif;
                        $dataVrow.='<option value="'.$projects->Project_Id.'" id="acnts" '.$selected.'>'.$projects->Name.'</option>';
                    endforeach;
                        $dataVrow.='</select><span class="error"></span>';
                }else{
                        $proName = Projects::findOne($data['place'])->Name;
                        $dataVrow.='<input type="hidden" value="'.$data['place'].'" name="place">
                                        <span class="styleclass">Office-Corporate</span>
                                        </div>
                                    </div>';
                }   
                //if($user['superuser']==1 || $user['superuser']==2){//if22222

                    if($data['type']=='debit'){//f4

                        $totalamount=0;
                        foreach($journalitems AS $key => $journalitem):

                            if($key==0){

                                $addbutn = '<a class="btn btn-primary icon-add add_debit_row" data-id="'.$key.'" href="#"></a>';

                            }
                            else{

                                $addbutn = '<a class="btn btn-primary icon-remove remove_debit_row" data-id="'.$key.'" href="#"></a>';

                            }

                            $debitaccount=$journalitem['debitaccnt'];
                            $debit=AccountsItem::findOne($debitaccount);
                            if($debit){
                               $debitname = $debit->name;
                            }
                            else{
                                $debitname = '';
                            }
                            $totalamount=$totalamount + $journalitem['amount'];
                        $dataVrow.='<div class="debit_row" id="debit_row_'.$key.'"><div class="col-md-4 type">
                                            <div class="form-group">
                                                <label>Debit Account</label>
                                                <select class="form-control debitaccounts" name="debitaccounts[]" id="debitaccounts_'.$key.'" data-id="'.$key.'">
                                                    <option value="0">Select Account</option>';
                        $acnts=AccountsItem::find()->where(['Status'=>0])->orderBy(['name'=> SORT_ASC])->all();
                        foreach($acnts AS $accounts):
                            if($debitaccount==$accounts->id):$selected='selected';else:$selected='';endif;
                            $dataVrow.='<option value="'.$accounts->id.'" id="acnts" '.$selected.'>'.$accounts->name.'</option>';
                        endforeach;
                            $dataVrow.='</select><span class="error"></span>
                                        <input type="hidden" name="journalitem[]" value="'.$journalitem['id'].'">
                                        <input type="hidden" name="type" value="'.$data['type'].'">
                                            </div>
                                        </div>
                                        <div class="col-md-4 type">
                                            <div class="form-group">
                                                <label>Narration</label>
                                                <input type="text" class="form-control narration" name="narration[]" id="narration_'.$key.'" value="'.$journalitem['narration'].'" data-id="'.$key.'">
                                                <!--<input type="text" class="form-control narration" name="narration[]" id="narration_'.$key.'" value="" data-id="'.$key.'">-->
                                                <span class="error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4 type">
                                            <div class="col-md-10">
                                                <div class="form-group">
                                                    <label>Amount</label>
                                                    <input type="text" class="form-control debitamount" name="debitamount[]" id="debitamount_'.$key.'" value="'.$journalitem['amount'].'" data-id="'.$key.'"><span class="error"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-2 icon-groups">
                                                '.$addbutn.'
                                            </div>
                                        </div> </div>';
                        endforeach;

                    }else{//f4
                        $totalamount=0;
                        foreach($journalitems AS $key => $journalitem):
                            $keyid = $key + 100;
                            if($key==0){

                                $addbutn = '<a class="btn btn-primary icon-add add_credit_row" data-id="'.$keyid.'" href="#"></a>';

                            }
                            else{

                                $addbutn = '<a class="btn btn-primary icon-remove remove_credit_row" data-id="'.$keyid.'" href="#"></a>';

                            }
                            $creditaccount=$journalitem['creditaccnt'];
                            $creditname=AccountsItem::findOne($creditaccount)->name;
                            $totalamount=$totalamount + $journalitem['amount'];
                            $dataVrow.='<div class="credit_row" id="credit_row_'.$keyid.'"><div class="col-md-4 type">
                                            <div class="form-group">
                                                <label>Credit Account</label>
                                                <select class="form-control creditaccounts" name="creditaccounts[]" id="creditaccounts_'.$keyid.'" data-id="'.$keyid.'">
                                                    <option value="0">Select Account</option>';
                        $acnts=AccountsItem::find()->where(['Status'=>0])->orderBy(['name'=> SORT_ASC])->all();
                        foreach($acnts AS $accounts):
                            if($creditaccount==$accounts->id):$selected='selected';else:$selected='';endif;
                            $dataVrow.='<option value="'.$accounts->id.'" id="acnts" '.$selected.'>'.$accounts->name.'</option>';
                        endforeach;
                            $dataVrow.='</select><span class="error"></span>
                                        <input type="hidden" name="journalitem[]" value="'.$journalitem['id'].'">
                                        <input type="hidden" name="type" value="'.$data['type'].'">
                                            </div>
                                        </div>
                                        <div class="col-md-4 type">
                                            <div class="form-group">
                                                <label>Narration</label>
                                                <input type="text" class="form-control narration" name="narration[]" id="narration_'.$keyid.'" value="'.$journalitem['narration'].'" data-id="'.$keyid.'">
                                                <!--<input type="text" class="form-control narration" name="narration[]" id="narration_'.$keyid.'" value="" data-id="'.$keyid.'">-->
                                                <span class="error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4 type">
                                            <div class="col-md-10">
                                                <div class="form-group">
                                                    <label>Amount</label>
                                                    <input type="text" class="form-control creditamount" name="creditamount[]" id="creditamount_'.$keyid.'" value="'.$journalitem['amount'].'" data-id="'.$keyid.'"><span class="error"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-2 icon-groups">
                                                '.$addbutn.'
                                            </div>
                                        </div></div>';
                        endforeach;
                    }//f4//////////////////////////////////////////
                    if(count($journalitems)>1){
                        $butndisable = 'disabled="disabled"';
                    }
                    else{
                        $butndisable = '';
                    }

                    if($data['order_id']!=0)
                    {
                        $orderresmodel=OrderedResource::find()->where(['order_id' => $data['order_id']])->one();
                        $resmodel=Resources::findOne($orderresmodel->resource_id);
                        $resnarration = 'Hire charges of '.$resmodel->Name;
                    }
                    else{
                        $resnarration = $data['narration'];
                    }

                    if($data['type']=='debit'){ //f5
                        
                        $creditaccount=$data['creditacnt'];
                        //$creditname=AccountsItem::findOne($creditaccount)->name;
                        $dataVrow.='<div class="credit_row" id="credit_row_100"><div class="col-md-4 type">
                                        <div class="form-group">
                                            <label>Credit Account</label>
                                            
                                            <select class="form-control creditaccounts" name="creditaccounts[]" id="creditaccounts_100">
                                                <option value="0">Select Account</option>';
                        $acnts=AccountsItem::find()->where(['Status'=>0])->orderBy(['name'=> SORT_ASC])->all();
                        foreach($acnts AS $accounts):
                            if($creditaccount==$accounts->id):$selected='selected';else:$selected='';endif;
                            $dataVrow.='<option value="'.$accounts->id.'" id="acnts" '.$selected.'>'.$accounts->name.'</option>';
                        endforeach;
                                $dataVrow.='</select><span class="error"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 type">
                                        <div class="form-group">
                                            <div class="form-group">
                                                <label>Narration</label>
                                                <input type="text" class="form-control narration" name="narration[]" id="narration_100" value="'.$resnarration.'">
                                                <!--<input type="text" class="form-control narration" name="narration[]" id="narration_100" value="">-->
                                                <span class="error"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 type">
                                        <div class="col-md-10">
                                            <div class="form-group">
                                            <label>Amount</label>
                                                <input type="text" class="form-control creditamount" name="creditamount[]" id="creditamount_100" value="'.$totalamount.'" data-id="100"><span class="error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-2 icon-groups">
                                            <a class="btn btn-primary icon-add add_credit_row" data-id="100" href="#" '.$butndisable.'></a>
                                        </div>
                                    </div></div>';
                    }else{ //f5
                        $debitaccount=$data['debitacnt'];
                        $debitname=AccountsItem::findOne($debitaccount)->name;
                        $dataVrow.='<div class="debit_row" d="debit_row_0"><div class="col-md-4 type">
                                        <div class="form-group">
                                            <label>Debit Account</label>
                                            
                                            <select class="form-control debitaccounts" name="debitaccounts[]" id="debitaccounts_0">
                                                <option value="0">Select Account</option>';
                        $acnts=AccountsItem::find()->where(['Status'=>0])->orderBy(['name'=> SORT_ASC])->all();
                        foreach($acnts AS $accounts):
                            if($debitaccount==$accounts->id):$selected='selected';else:$selected='';endif;
                            $dataVrow.='<option value="'.$accounts->id.'" id="acnts" '.$selected.'>'.$accounts->name.'</option>';
                        endforeach;
                                $dataVrow.='</select><span class="error"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 type">
                                        <div class="form-group">
                                            <div class="form-group">
                                                <label>Narration</label>
                                                <input type="text" class="form-control" name="narration[]" id="narration" value="'.$resnarration.'">
                                               <!-- <input type="text" class="form-control" name="narration[]" id="narration" value="">-->
                                                <span class="error"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 type">
                                        <div class="col-md-10">
                                            <div class="form-group">
                                                <label>Amount</label>
                                                <input type="text" class="form-control debitamount" name="debitamount[]" id="debitamount_0" value="'.$totalamount.'" data-id="0"><span class="error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-2 icon-groups">
                                            <a class="btn btn-primary icon-add add_debit_row" data-id="0" href="#" '.$butndisable.'></a>
                                        </div>
                                    </div>
                                    <div class="col-md-4 type">
                                        <div class="form-group">
                                        </div>
                                    </div></div>';
                    }//f5

                /*}else{ //if22222

                    if($data['type']=='debit'){//f4

                        $totalamount=0;
                        foreach($journalitems AS $key => $journalitem):
                            if($key==0){

                                $addbutn = '<a class="btn btn-primary icon-add add_debit_row" data-id="'.$key.'" href="#"></a>';

                            }
                            else{

                                $addbutn = '<a class="btn btn-primary icon-remove remove_debit_row" data-id="'.$key.'" href="#"></a>';

                            }

                            $debitaccount=$journalitem['debitaccnt'];
                            $debitname=AccountsItem::findOne($debitaccount)->name;
                            $totalamount=$totalamount + $journalitem['amount'];
                        $dataVrow.='<div class="col-md-4 type">
                                            <div class="form-group">
                                                <label>Debit Account</label>
                                                <span>'.$debitname.'</span>
                                                <input type="hidden" class="form-control" name="debitaccounts[]" value="'.$debitaccount.'">
                                                <input type="hidden" name="journalitem[]" value="'.$journalitem['id'].'">
                                                <input type="hidden" name="type" value="'.$data['type'].'">
                                            </div>
                                        </div>
                                        <div class="col-md-4 type">
                                            <div class="form-group">
                                                <label>Narration</label>
                                                <span>'.$journalitem['narration'].'</span>                       
                                                <input type="hidden" name="narration[]" value="'.$journalitem['narration'].'">
                                            </div>
                                        </div>
                                        <div class="col-md-4 type">
                                            <div class="col-md-10">
                                                <div class="form-group">
                                                    <label>Amount</label>
                                                    <input type="text" class="form-control" name="debitamount[]" id="debitamount" value="'.$journalitem['amount'].'"><span class="error"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-2 icon-groups">
                                                '.$addbutn.'
                                            </div>
                                        </div>';
                        endforeach;

                    }else{//f4
                        $totalamount=0;
                        foreach($journalitems AS $key => $journalitem):
                            $keyid = $key + 100;
                            if($key==0){

                                $addbutn = '<a class="btn btn-primary icon-add add_credit_row" data-id="'.$keyid.'" href="#"></a>';

                            }
                            else{

                                $addbutn = '<a class="btn btn-primary icon-remove remove_credit_row" data-id="'.$keyid.'" href="#"></a>';

                            }
                            $creditaccount=$journalitem['creditaccnt'];
                            $creditname=AccountsItem::findOne($creditaccount)->name;
                            $totalamount=$totalamount + $journalitem['amount'];
                            $dataVrow.='<div class="col-md-4 type">
                                            <div class="form-group">
                                                <label>Credit Account</label>
                                                <span>'.$creditname.'</span>    
                                                <input type="hidden" class="form-control" name="creditaccounts[]" value="'.$creditaccount.'">
                                                <input type="hidden" name="journalitem[]" value="'.$journalitem['id'].'">
                                                <input type="hidden" name="type" value="'.$data['type'].'">
                                            </div>
                                        </div>
                                        <div class="col-md-4 type">
                                            <div class="form-group">
                                                <label>Narration</label>
                                                <span>'.$journalitem['narration'].'</span>  
                                                <input type="hidden" name="narration[]" value="'. $journalitem['narration'].'>">
                                            </div>
                                        </div>
                                        <div class="col-md-4 type">
                                            <div class="col-md-10">
                                                <div class="form-group">
                                                    <label>Amount</label>
                                                    <input type="text" class="form-control" name="debitamount[]" id="debitamount" value="'.$journalitem['amount'].'"><span class="error"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-2 icon-groups">
                                                '.$addbutn.'
                                            </div>
                                        </div>';
                        endforeach;
                    }//f4
                    if($data['type']=='debit'){ //f5
                        $creditaccount=$data['creditacnt'];
                        $creditname=AccountsItem::findOne($creditaccount)->name;
                        $dataVrow.='<div class="col-md-4 type">
                                        <div class="form-group">
                                            <label>Credit Account</label>
                                            <span>'.$creditname.'</span>  
                                            <input type="hidden" class="form-control" name="creditaccount" value="'.$creditaccount.'">
                                        </div>
                                    </div>
                                    <div class="col-md-4 type">
                                        <div class="form-group">
                                            <label>Amount</label>
                                            <input type="hidden" id="creditamount" value="'.$totalamount.'">
                                            <input type="text" class="form-control" name="creditamount" id="creditamount" value="'.$totalamount.'"><span class="error"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 type">
                                        <div class="form-group">
                                        </div>
                                    </div>';
                    }else{ //f5
                        $debitaccount=$data['debitacnt'];
                        $debitname=AccountsItem::findOne($debitaccount)->name;
                        $dataVrow.='<div class="col-md-4 type">
                                        <div class="form-group">
                                            <label>Debit Account</label>
                                            <span>'.$debitname.'</span>  
                                            <input type="hidden" class="form-control" name="debitaccount" value="'.$debitaccount.'">
                                        </div>
                                    </div>
                                    <div class="col-md-4 type">
                                        <div class="form-group">
                                            <label>Amount</label>
                                            <input type="hidden" id="creditamount" value="'.$totalamount.'">
                                            <input type="text" class="form-control" name="creditamount" id="debitamount" value="'.$totalamount.'"><span class="error"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 type">
                                        <div class="form-group">
                                        </div>
                                    </div>';
                    }//f5
                }//if22222*/
                if(!empty($account)){
                    if($account['schedule']=='3'){
                        $dataVrow.='<div class="col-md-4 type">
                                        <div class="form-group">
                                            <label>Schedule</label>
                                            <select class="form-control" name="schedule" id="schedule">
                                                <option value="none">Select Schedule</option>';
                            $schedule=Schedule::find()->all();
                            foreach($schedule AS $items):
                                $dataVrow.='<option value="'.$items['groupid'].'">'.$items['name'].'</option>';
                            endforeach;
                                $dataVrow.='</select><span class="error"></span>
                                        </div>
                                    </div><div class="col-md-4 type">
                                    <div class="form-group">
                                    </div>
                                </div><div class="col-md-4 type">
                                <div class="form-group">
                                </div>
                            </div>';
                    }
                }

            $debitsql="SELECT * FROM journalitems WHERE journalid='".$generateID."' ";
            $command=$connection->createCommand($debitsql);
            $dataReader=$command->query();
            $debit_account=$dataReader->read();

            $dataVrow.='<div class="col-md-4 type scheduleitemdrop">';
            
            $bsitems = Bsitems::find()->where(['status'=> 0])->andWhere(['accnt_id'=> $debit_account['debitaccnt']])->all();

            if($bsitems):

                $dataVrow.='
                        <div class="form-group">
                            <label>Schedule Item</label>
                            <select class="form-control" name="accntschitem" id="accntschitem">
                                <option value="none">Select Schedule Item</option>';

                foreach($bsitems as $key => $bsitem):  
                    if($key==0):
                        $selected="selected"; 
                    else:
                        $selected="";
                    endif; 

                    $dataVrow.= '<option value="'.$bsitem->item_id.'" '.$selected.'>'.$bsitem->itemname.'</option>';

                endforeach;

                $dataVrow.='   </select><span class="error"></span>
                        </div>';

            endif;

            $dataVrow.='
                    </div>
                    <div class="col-md-4 type">
                        <div class="form-group">
                        </div>
                    </div>
                    <div class="col-md-4 type">
                        <div class="form-group">
                        </div>
                    </div>';

            $dataVrow.='<div class="col-md-12 text-right journalalign">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-danger cancel" id="canceljournal"  value="Cancel"><span class="icon-close"></span> Close</button>
                            <button type="button" class="btn btn-primary" id="journalvoucher" name="journalvoucher" value="Save"><span class="icon-check"></span> Generate Voucher</button>
                            <a href="#" id="printjournal" target="_blank" style="display: none;color: green;">Voucher Created Successfully!<br><span class="icon-print"style="color: blue;"> Print Now</span></a>
                        </div>
                    </div></form></div>';

            $arr = array('result' => $dataVrow,'error'=>'No');
            return json_encode($arr);
        }
    }

    public function actionAddcreditrow()
    {
        $rowid = 100 + $_POST['numItems'] + 1; 
        $lastrowid = 100 + $_POST['numItems']; 
        $dataVrow='
            <div class="credit_row" id="credit_row_'.$rowid.'"><div class="col-md-4 type">
                <div class="form-group">
                    <label>Credit Account</label>
                    
                    <select class="form-control creditaccounts" name="creditaccounts[]" id="creditaccounts_'.$rowid.'" data-id="'.$rowid.'">
                        <option value="0">Select Account</option>';

        $acnts=AccountsItem::find()->orderBy(['name'=> SORT_ASC])->all();
        foreach($acnts AS $accounts):
            $dataVrow.='<option value="'.$accounts->id.'" id="acnts">'.$accounts->name.'</option>';
        endforeach;
        $dataVrow.='</select><span class="error"></span>
                </div>
            </div>
            <div class="col-md-4 type">
                <div class="form-group">
                    <div class="form-group">
                        <label>Narration</label>
                        <input type="text" class="form-control" name="narration[]" id="narration_'.$rowid.'" value=""><span class="error"></span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 type">
                <div class="col-md-10">
                    <div class="form-group">
                    <label>Amount</label>
                        <input type="text" class="form-control creditamount" name="creditamount[]" id="creditamount_'.$rowid.'" value="" data-id="'.$rowid.'"><span class="error"></span>
                    </div>
                </div>
                <div class="col-md-2 icon-groups">
                    <a class="btn btn-primary icon-remove remove_credit_row" data-id="'.$rowid.'" href="#"></a>
                </div>
            </div></div>';

        $arr = array('result' => $dataVrow,'lastrow' => $lastrowid,'error'=>'No');
        return json_encode($arr);
    }

    public function actionAdddebitrow()
    {
        $rowid = $_POST['numItems'] + 1; 
        $lastrowid = $_POST['numItems']; 
        $dataVrow='
            <div class="debit_row" id="debit_row_'.$rowid.'"><div class="col-md-4 type">
                <div class="form-group">
                    <label>Debit Account</label>
                    
                    <select class="form-control debitaccounts" name="debitaccounts[]" id="debitaccounts_'.$rowid.'" data-id="'.$rowid.'">
                        <option value="0">Select Account</option>';

        $acnts=AccountsItem::find()->orderBy(['name'=> SORT_ASC])->all();
        foreach($acnts AS $accounts):
            $dataVrow.='<option value="'.$accounts->id.'" id="acnts">'.$accounts->name.'</option>';
        endforeach;
        $dataVrow.='</select><span class="error"></span>
                </div>
            </div>
            <div class="col-md-4 type">
                <div class="form-group">
                    <div class="form-group">
                        <label>Narration</label>
                        <input type="text" class="form-control" name="narration[]" id="narration_'.$rowid.'" value=""><span class="error"></span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 type">
                <div class="col-md-10">
                    <div class="form-group">
                    <label>Amount</label>
                        <input type="text" class="form-control debitamount" name="debitamount[]" id="debitamount_'.$rowid.'" value="" data-id="'.$rowid.'"><span class="error"></span>
                    </div>
                </div>
                <div class="col-md-2 icon-groups">
                    <a class="btn btn-primary icon-remove remove_debit_row" data-id="'.$rowid.'" href="#"></a>
                </div>
            </div></div>';

        $arr = array('result' => $dataVrow,'lastrow' => $lastrowid,'error'=>'No');
        return json_encode($arr);
    }

    public function actionContravoucher()
    {
        $connection = Yii::$app->db;
        if(isset($_POST['generateID'])){
            $generateID=$_POST['generateID'];
            $sql="SELECT a.Id,a.place,a.project_id,a.account_id,a.Purpose,a.alloted_amount,a.payment,a.date,a.advance_id,b.Name,b.Project_Id,c.name FROM finance_requests AS a INNER JOIN projects AS b ON a.place=b.Project_Id INNER JOIN accounts_item AS c ON a.account_id=c.id WHERE a.Id='$generateID'";
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $data=$dataReader->read();
            $dataVrow ='';
            $dataVrow.='<div class="row"><form id="contraform" ><div class="text-center"><h3>Contra Voucher</h3></div>
                        <div class="col-md-4 type">
                            <div class="form-group">
                                <label>Date</label>
                                <input type="text" id="contradate" class="form-control" name="contradate" value="'.$data['date'].'"><span class="error"></span>
                            </div>
                        </div>
                        <div class="col-md-4 type">
                            <div class="form-group">
                                <label>Place</label>
                                <span>'.$data['Name'].'</span>
                                <input type="hidden" value="'.$data['place'].'" name="place">
                            </div>
                        </div>
                        <div class="col-md-4 type">
                            <div class="form-group">
                                <label>Credit Account</label>
                                <span>'.$data['name'].'</span>
                                <input type="hidden" value="'.$data['account_id'].'" name="creditacnt">
                            </div>
                        </div>
                        <div class="col-md-4 type">
                            <div class="form-group">
                                <label>Debit Account</label>
                                <span>'.$data['Name'].'</span>
                                <input type="hidden" id="bank" class="form-control" value="'.$data['Project_Id'].'" name="accounts">
                                <span class="error" id="acntinfo" style="display: none;">Select Accounthead</span>
                            </div>
                        </div>';
            $userid = Yii::$app->user->id;
            $user = User::find()->where(['id'=>$userid])->one();
            if($user['superuser']==1){
                $dataVrow.='<div class="col-md-4 type">
                                <div class="form-group">
                                    <label>Narration</label>
                                    <input type="hidden" value="'.$data['Id'].'" name="requestid">
                                    <input type="hidden" value="'.$data['advance_id'].'" name="advanceid">
                                    <input type="hidden" value="'.$data['payment'].'" name="payment">
                                    <input type="text" class="form-control" id="narration" name="narration" value="'.$data['Purpose'].'"><span class="error"></span>
                                </div>
                            </div>
                            <div class="col-md-4 type">
                                <div class="form-group">
                                    <label>Amount</label>
                                    <input type="hidden" id="amount" value="'.$data['alloted_amount'].'">
                                    <input type="text" class="form-control" value="'.$data['alloted_amount'].'" name="allotedamount" id="contraamount"><span class="error"></span>
                                </div>
                            </div>';
            }else{

                $dataVrow.='<div class="col-md-4 type">
                                <div class="form-group">
                                    <label>Narration</label>
                                    <input type="hidden" value="'.$data['Id'].'" name="requestid">
                                    <input type="hidden" value="'.$data['advance_id'].'" name="advanceid">
                                    <input type="hidden" value="'.$data['payment'].'" name="payment">
                                    <input type="text" class="form-control" id="narration" name="narration" value="'.$data['Purpose'].'"><span class="error"></span>
                                </div>
                            </div>
                            <div class="col-md-4 type">
                                <div class="form-group">
                                    <label>Amount</label>
                                    <input type="hidden" id="amount" value="'.$data['alloted_amount'].'" name="allotedamount">
                                    <span>'.$data['alloted_amount'].'</span>
                                </div>
                            </div>';
            }
            $dataVrow.='<div class="col-md-4 type">
                            <div class="form-group">
                                <label>Cheque no :</label>
                                <input type="text" class="form-control" id="chequenocontra" name="chequeno"><span class="error"></span>
                            </div>
                        </div>
                        <div class="col-md-4 type">
                        <div class="form-group">
                            <label>Project</label>
                            <select class="form-control" id="Cprojectid" name="projectid">
                                <option value="0">Select Project</option>';
                $project=Projects::find()->where(['Project_Delete_Status' => 0])->all();
                foreach($project AS $projects):
                if($data['place']==$projects->Project_Id):$selected='selected';else:$selected='';endif;
                $dataVrow.='<option value="'.$projects->Project_Id.'"'. $selected.'>'.$projects->Name.'</option>';
                endforeach;
                $dataVrow.='</select><span class="error"></span>
                        </div>
                    </div>
                    <div class="col-md-4 text-right">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-danger cancel" id="cancelvoucher"><span class="icon-close"></span> Cancel</button>
                            <button type="button" class="btn btn-primary" id="contravoucher" name="contravoucher" value="Save"><span class="icon-check"></span> Generate Voucher</button>
                            <!-- <a href="#" class="btn btn-primary text-button" id="printcontra123" target="_blank" style="display: none;border-radius: 25px;"><span class="icon-print"></span> Print</a> -->
                            <a href="#" id="printcontra" target="_blank" style="display: none;color: green;">Voucher Created Successfully!<br><span class="icon-print"style="color: blue;"> Print Now</span></a>
                        </div>
                    </div></form></div>';
            $arr = array('result' => $dataVrow,'error'=>'No');
            return json_encode($arr);
        }
    }

    public function actionGeneratereceipt()
    {
        $connection = Yii::$app->db;
        if(isset($_POST['generateID'])){
            $generateID=$_POST['generateID'];
            $sql="SELECT a.Id,a.project_id,a.place,a.Purpose,a.Amount,a.date,a.payment,a.debitacnt,a.account_id,b.Name FROM fundreceipt AS a INNER JOIN projects AS b ON a.project_id=b.Project_Id WHERE Id='".$generateID."'";
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $data=$dataReader->read();
            $sql3="SELECT Name FROM projects WHERE Project_Id='".$data['project_id']."'";
            $command=$connection->createCommand($sql3);
            $dataReader=$command->query();
            $project=$dataReader->read();
            ///if($data['payment']=='1'):$type='Cash' ;else: $type='Bank';endif;
            $sql1="select * from projects WHERE Project_Delete_Status=0";
            $command=$connection->createCommand($sql1);
            $dataReader=$command->query();
            $adminprojects=$dataReader->readAll();
            $userid=Yii::$app->user->id;
            $sql4 = "SELECT * FROM `user_projects` WHERE `userid` = ".$userid."";
            $command=$connection->createCommand($sql4);
            $dataReader=$command->query();
            $userproject=$dataReader->readAll();
            $arr=array();
            foreach($userproject AS $userproj):
                array_push($arr,$userproj['projectid']);
            endforeach;
            if(in_array("12", $arr)):
                $sql1="select id,name,projectid from accounts_item where account_type='2' ";
            else:
                $sql1="select a.id,a.name,a.projectid from accounts_item as a INNER JOIN user_accounts as b on a.id=b.account_id where a.account_type='2' AND b.user_id=".$userid." ";
            endif;
            $command=$connection->createCommand($sql1);
            $dataReader=$command->query();
            $banks=$dataReader->readAll();
            //$sql="SELECT id FROM accounts_item WHERE projectid='".$data['place']."' AND account_type='".$data['payment']."' ";
            $sql="SELECT id FROM accounts_item WHERE id='".$data['account_id']."' AND account_type='".$data['payment']."' ";
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $debit=$dataReader->read();
            $sql="SELECT id,name FROM accounts_item WHERE id='".$data['debitacnt']."' ";
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $credit=$dataReader->read();
            $dataVrow ='';
            $dataVrow.='<div class="row"><form id="receiptform" ><div class="text-center">';
            if($data['payment']=='1'){
                $dataVrow.='<h3>Cash Receipt Voucher</h3></div>';
            }else{
                $dataVrow.='<h3>Bank Receipt Voucher</h3></div>';
            }
            $dataVrow.='<div class="col-md-12">
            <div class="col-md-4 type">
                            <div class="form-group">
                                <label>Date</label>
                                <input type="date" id="receiptdate" class="form-control" name="receiptdate" value="'.date('Y-m-d',strtotime($data['date'])).'">
                                <span class="error"></span>
                            </div>
                        </div>';
            if($data['project_id']=='12'){
                $dataVrow.='<div class="col-md-4 type">
                                <div class="form-group">
                                    <label>Project</label>
                                    <select class="form-control" name="projectid" id="projectid">';
            foreach($adminprojects AS $key=>$projdata):
                if($projdata['Project_Id']==12):$selected='selected'; else: $selected='';endif;
                $dataVrow.='<option value="'.$projdata['Project_Id'].'" '.$selected.' >'.$projdata['Name'].'</option>';
                endforeach;
                $dataVrow.='</select></div></div>';
            }else{
                $dataVrow.='<div class="col-md-4 type">
                                <div class="form-group">
                                    <label>Project</label>
                                    <span>'.$project['Name'].'</span>
                                    <input type="hidden" value="'.$project['Name'].'" name="project">
                                    <input type="hidden" value="'.$data['project_id'].'" name="projectid">
                                </div>
                            </div> ';
            }
            $dataVrow.='<div class="col-md-4 type">
                            <div class="form-group">
                                <label>Place</label>
                                <select class="form-control" id="Gplace" name="place">
                                    <option value="0">Select Place</option>';
            $project=Projects::find()->where(['Project_Delete_Status' => 0])->all();
            foreach($project AS $projects):
                if($projects->Project_Id==12):$selected='selected';else:$selected='';endif;
                $dataVrow.='<option value="'.$projects->Project_Id.'"'. $selected.'>'.$projects->Name.'</option>';
            endforeach;
                 $dataVrow.='</div>
                        </div></div>
                        <div class="col-md-12">
                        
                        <div class="col-md-4 type">
                            <div class="form-group">
                                <label>Account</label>
                                <span>'.$credit['name'].'</span>
                                <input type="hidden" value="'.$data['account_id'].'" name="debitacnt">
                                <input type="hidden" value="'.$credit['id'].'" name="creditacnt">
                            </div>
                        </div>
                        <div class="col-md-4 type">
                            <div class="form-group">
                                <label>Narration</label>
                                <input type="hidden" value="'.$data['Id'].'" name="requestid">
                                <input type="text" class="form-control" id="narration" name="narration" value="'.$data['Purpose'].'"><span class="error"></span>
                            </div>
                        </div>
                        <div class="col-md-4 type">
                            <div class="form-group">
                                <label>Amount</label>
                                <input type="hidden" id="recamount" value="'.$data['Amount'].'">
                                <input type="hidden" value="'.$data['payment'].'" name="payment">
                                <input type="text" class="form-control" value="'.$data['Amount'].'" name="amount" id="amount"><span class="error"></span>
                            </div>
                        </div>
                        </div>';

            $bsitems = Bsitems::find()->where(['status'=> 0])->andWhere(['accnt_id'=> $data['debitacnt']])->all();

            if($data['payment']=='2' && $bsitems){
                $dataVrow.='<div class="col-md-12">
                            <div class="col-md-4 type">
                                <div class="form-group">
                                    <label>Cheque No</label>
                                    <input type="text" class="form-control" name="chequenum" id="chequenum"><span class="error" style="color: red;"></span>
                                </div>
                            </div>';
            }
            elseif($data['payment']=='2' && empty($bsitems)){
                $dataVrow.='<div class="col-md-12">
                            <div class="col-md-4 type">
                                <div class="form-group">
                                    <label>Cheque No</label>
                                    <input type="text" class="form-control" name="chequenum" id="chequenum"><span class="error" style="color: red;"></span>
                                </div>
                            </div>
                            <div class="col-md-4 type" style="display:none;">
                                <div class="form-group">
                                <label>Bank</label>
                                <select class="form-control" name="bankid" id="bankid">
                                <option value="0" selected="selected">Select Bank</option>';
                    foreach($banks AS $key=>$bank):
                        if($bank['projectid']==12):$selected='selected'; else: $selected='';endif;
                        $dataVrow.='<option value="'.$bank['id'].'" '.$selected.' >'.$bank['name'].'</option>';
                    endforeach;
                    $dataVrow.='</select><span class="error" style="color: red;"></span></div>
                            </div>
                            <div class="col-md-4 type"><div class="form-group"></div></div></div>';
            }

            if($data['payment']=='2' && $bsitems){

                $dataVrow.='<div class="col-md-4 type scheduleitemdrop">';

                $dataVrow.='
                        <div class="form-group">
                            <label>Schedule Item</label>
                            <select class="form-control" name="accntschitem" id="accntschitem">
                                <option value="none">Select Schedule Item</option>';

                foreach($bsitems as $key => $bsitem):  
                    if($key==0):
                        $selected="selected"; 
                    else:
                        $selected="";
                    endif; 

                    $dataVrow.= '<option value="'.$bsitem->item_id.'" '.$selected.'>'.$bsitem->itemname.'</option>';

                endforeach;

                $dataVrow.='   </select><span class="error"></span>
                        </div>';

                $dataVrow.='
                    </div>
                    <div class="col-md-4 type heightadjust">
                        <div class="form-group">
                        </div>
                    </div>
                    </div>';

            }
            elseif($data['payment']!='2' && $bsitems){

                $dataVrow.='<div class="col-md-12"><div class="col-md-4 type scheduleitemdrop">';

                $dataVrow.='
                        <div class="form-group">
                            <label>Schedule Item</label>
                            <select class="form-control" name="accntschitem" id="accntschitem">
                                <option value="none">Select Schedule Item</option>';

                foreach($bsitems as $key => $bsitem):  
                    if($key==0):
                        $selected="selected"; 
                    else:
                        $selected="";
                    endif; 

                    $dataVrow.= '<option value="'.$bsitem->item_id.'" '.$selected.'>'.$bsitem->itemname.'</option>';

                endforeach;

                $dataVrow.='   </select><span class="error"></span>
                        </div>';

                $dataVrow.='
                    </div>
                    <div class="col-md-4 type heightadjust">
                        <div class="form-group">
                        </div>
                    </div>
                    <div class="col-md-4 type heightadjust">
                        <div class="form-group">
                        </div>
                    </div>
                    </div>';

            }

            $dataVrow.='<div class="col-md-12">
                        <div class="col-md-4 type heightadjust">
                            <div class="form-group">
                            </div>
                        </div>
                        <div class="col-md-4 text-right">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-danger cancel" id="cancelvoucher"><span class="icon-close"></span> Cancel</button>
                                <button type="button" class="btn btn-primary" id="receiptvoucher" name="receiptvoucher" value="Save"><span class="icon-check"></span> Generate Voucher</button>
                                <!-- <a href="#" class="btn btn-primary text-button" id="printreceipt123" target="_blank" style="display: none;border-radius: 25px;"><span class="icon-print"></span> Print</a> -->
                                <a href="#" id="printreceipt" target="_blank" style="display: none;color: green;">Voucher Created Successfully!<br><span class="icon-print"style="color: blue;"> Print Now</span></a>
                            </div>
                        </div>
                        <div class="col-md-4 type heightadjust">
                            <div class="form-group">
                            </div>
                        </div>
                        </div>
                        </div></form></div>';

            $arr = array('result' => $dataVrow,'error'=>'No');
            return json_encode($arr);
            
        }
    }

    public function actionPaymentvoucher()
    {
        
        $model=new Voucher;
        $connection = Yii::$app->db;
        if(isset($_POST['requestid']))
        {
            $finrequest = FinanceRequests::findOne($_POST['requestid']);
            $finvoucherno = $finrequest->voucher_no;
            $model->account_id=$_POST['debitacnt'];
            $model->amount=$_POST['allotedamount'];
            /*if(!empty($_POST['bankid'])){
                $model->bank_id=$_POST['bankid'];
            }*/
            if($_POST['payment']==1):
                $bankacnt=0;
                $credit=$_POST['creditacnt'];
            else:
                $bankacnt=$_POST['creditacnt'];
                $credit=$_POST['creditacnt'];
                //$credit=$_POST['bankid'];
            endif;
            //$model->creditacnt=$_POST['bankid'];
            $model->bank_id=$bankacnt;
            $model->creditacnt=$credit;
            $model->place=$_POST['place'];
            $model->project_id=$_POST['projectid'];

            if($_POST['payment']==1):
                $audistatus=1;
            else:
                $audistatus=2;
            endif;
            $model->audit_status=$audistatus;

            if(isset($_POST['accntschitem'])):

                if($_POST['accntschitem']!='none'){

                    $model->bsitem_id=$_POST['accntschitem'];

                }

            endif;
            
            //$model->sub_schedule=$_POST['schedule'];
            if(!empty($_POST['iowid'])){
                $model->IOW_Id=$_POST['iowid'];
            }
            //$model->Resource_Id=$_POST['resourceid'];
            if(!empty($_POST['schedule'])){
                $model->Resource_Id=$_POST['schedule'];
            }
            $sql="SELECT voucher_no FROM voucher WHERE date = '".date('Y-m-d',strtotime($_POST['paymentdate']))."' AND payment ='".$_POST['payment']."' AND type='Payment' AND place='".$_POST['place']."' ORDER BY voucher_no DESC limit 1";
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $no=$dataReader->read();
            //echo $sql;exit;
            if(!empty($no)){
                if($no['voucher_no']!='')
                {
                    //$voucherno=$no['voucher_no'] + 1;
                    $voucherno='001';
                }
                else
                {
                    $voucherno='1';
                }
            }else{
                $voucherno='1';
            }
            $date=date('M/Y',strtotime($_POST['paymentdate']));
            //$model->voucher_no='00'. $voucherno ;
            if($finvoucherno!=''){
                $model->voucher_no=$finvoucherno;
            }
            else{
                $model->voucher_no='00'. $voucherno ;
            }

            $model->date=date('Y-m-d',strtotime($_POST['paymentdate']));
            $model->currentdate=date('Y-m-d');
            $model->type='Payment';
            $model->narration=$_POST['narration'];
            $model->payment=$_POST['payment'];
            //$model->creditacnt = 0;
            if(!empty($_POST['chequenum'])){
            $model->cheque_no=$_POST['chequenum']; }
            $reqid=$_POST['requestid'];
            //print_r($model);exit;
            if($model->save(false))
            {
                if($_POST['transfer']==1)
                {
                    if($model->payment==1):$narration='Cash';else:$narration='Amount';endif;
                    $count=$model->voucher_no + 1 ;
                    //echo $model->voucher_no;exit;
                    $vouchernum='00'. $count;
                    $sql="SELECT projectid FROM accounts_item WHERE id='".$model->account_id."' ";
                    $command=$connection->createCommand($sql);
                    $dataReader=$command->query();
                    $project=$dataReader->read();
                    $sql="SELECT account_type FROM accounts_item WHERE id='".$model->account_id."'";
                    $command=$connection->createCommand($sql);
                    $dataReader=$command->query();
                    $debit=$dataReader->read();
                    $sql="SELECT account_type FROM accounts_item WHERE id='".$model->creditacnt."'";
                    $command=$connection->createCommand($sql);
                    $dataReader=$command->query();
                    $credittype=$dataReader->read();
                    if($debit['account_type']==1 && $credittype['account_type']==1):
                        $bank='0';
                    elseif($debit['account_type']==2 && $credittype['account_type']==2):
                        $bank=$model->account_id;
                    endif;
                    $sql="INSERT INTO voucher (place,project_id,voucher_no,date,amount,account_id,creditacnt,type,narration,payment,cheque_no,bank_id,sub_schedule,contra) values ('".$model->project_id."','".$model->place."','$vouchernum','".$model->date."','".$model->amount."','".$model->account_id."','".$model->creditacnt."','Receipt','$narration Received from Office-Corporate','".$model->payment."','".$model->cheque_no."','".$bank."','".$model->sub_schedule."','1')";
                    //echo $sql;exit;
                    $command=$connection->createCommand($sql);
                    $dataReader=$command->query();
                }
                if(!empty($_POST['gst'])){
                    if($_POST['gst']==1)
                    {
                        $sql="INSERT INTO voucher (place,project_id,voucher_no,date,amount,account_id,creditacnt,type,narration,payment,cheque_no,bank_id,sub_schedule) values ('".$model->project_id."','".$model->place."','$model->voucher_no','".$model->date."','".$_POST['gstamount']."','".$_POST['debitcgstacnt']."','".$model->creditacnt."','Payment','".$_POST['cgstnarration']."','".$model->payment."','".$model->cheque_no."','".$model->bank_id."','".$model->sub_schedule."')";
                        //echo $sql;exit;
                        $command=$connection->createCommand($sql);
                        $dataReader=$command->query();
                        $sql1="INSERT INTO voucher (place,project_id,voucher_no,date,amount,account_id,creditacnt,type,narration,payment,cheque_no,bank_id,sub_schedule) values ('".$model->project_id."','".$model->place."','$model->voucher_no','".$model->date."','".$_POST['gstamount']."','".$_POST['debitsgstacnt']."','".$model->creditacnt."','Payment','".$_POST['sgstnarration']."','".$model->payment."','".$model->cheque_no."','".$model->bank_id."','".$model->sub_schedule."')";
                        //echo $sql;exit;
                        $command=$connection->createCommand($sql1);
                        $dataReader=$command->query();
                    }
                    elseif($_POST['gst']==2){
                        $sql1="INSERT INTO voucher (place,project_id,voucher_no,date,amount,account_id,creditacnt,type,narration,payment,cheque_no,bank_id,sub_schedule) values ('".$model->project_id."','".$model->place."','$model->voucher_no','".$model->date."','".$_POST['gstamount']."','".$_POST['debitigstacnt']."','".$model->creditacnt."','Payment','".$_POST['igstnarration']."','".$model->payment."','".$model->cheque_no."','".$model->bank_id."','".$model->sub_schedule."')";
                        //echo $sql;exit;
                        $command=$connection->createCommand($sql1);
                        $dataReader=$command->query();
                    } 
                }
                $sql="UPDATE finance_requests SET Status='3' WHERE Id='$reqid'";
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();
                $resource=AccountsItem::findOne($_POST['debitacnt'])->resource_id;
                if($resource!=0):
                    $resourcetype=Resources::findOne($resource)->ResourceType_Id;
                    if($resourcetype==25):
                        $sql="(SELECT date,amount,type,contra FROM voucher WHERE (account_id='".$_POST['debitacnt']."' OR creditacnt='".$_POST['debitacnt']."') AND place='".$_POST['place']."' )
                        UNION
                        (SELECT date,amount,type,contra FROM journalvoucher WHERE (debitacnt='".$_POST['debitacnt']."' OR creditacnt='".$_POST['debitacnt']."') AND project_id='".$_POST['place']."') ORDER BY date ASC";
                        //echo $sql;exit;
                        $command=$connection->createCommand($sql);
                        $dataReader=$command->query();
                        $dataProvider=$dataReader->readAll();
                        $totalamount=0;
                        if(count($dataProvider)>0):
                            foreach($dataProvider AS $item):
                                $totalamount+=$item['amount'];
                            endforeach;
                            $initialdate=$dataProvider[0]['date'];
                            $lastdate=$dataProvider[count($dataProvider)-1]['date'];
                            $date1 = New DateTime($initialdate);
                            $date2 = New DateTime($lastdate);
                            $diff = $date1->diff($date2);
                            $months = $diff->y * 12 + $diff->m + $diff->d / 30;
                            $totmonths=round($months);
                            //echo $totmonths;exit;
                            $permonthrate=$totalamount / $totmonths;
                            ///echo number_format($permonthrate,2,'.','');exit;
                            //$pricingestimateress=PricingEstimateResourcesNew::model()->findAll(array('condition'=>'project_Id='.$_POST['place'].' AND resource_Id='.$resource.'  AND pricing_status=0'));
                            $pricingestimateress=PricingEstimateResourcesNew::find()->where(['project_Id' => $_POST['place']])->andWhere(['resource_Id' => $resource])->andWhere(['pricing_status' => 0])->all();
                            /*if(count($pricingestimateress)>0):
                                foreach($pricingestimateress AS $pricingestimateres):
                                    $pricingestimateres->rate = number_format($permonthrate,2,'.','');
                                    $pricingestimateres->save(false);
                                endforeach;
                            endif;*/
                        endif;
                    endif;
                endif;
                $url=Yii::$app->request->baseUrl."/voucher/printvoucher?pid=".$model->id;
                $arr = array('Id' => $_POST['requestid'],'url'=>$url,'error'=>'No');
                return json_encode($arr);
            }
        }
    }

    public function actionGeneratecontra()
    {
        $model=new Voucher();
        $connection = Yii::$app->db;
        if(isset($_POST['requestid']))
        {
            $model->place=$_POST['place'];
            $model->project_id=$_POST['projectid'];
            $debitacnt = isset($_POST['accounts']) ? $_POST['accounts'] : array();
            $model->account_id=$debitacnt[0];
            $model->creditacnt=$_POST['creditacnt'];
            $model->amount=$_POST['allotedamount'];
            $sql="SELECT voucher_no FROM voucher WHERE date = '".date('Y-m-d',strtotime($_POST['contradate']))."' AND payment ='".$_POST['payment']."' AND contra='1' ORDER BY voucher_no DESC limit 1";
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $no=$dataReader->read();
            if(!empty($no)){
                if($no['voucher_no']!='')
                {
                    $voucherno=$no['voucher_no'] + 1;
                }
                else
                {
                    $voucherno='1';
                }
            }else{
                $voucherno='1';
            }
            $sql="SELECT account_type FROM accounts_item WHERE id='".$model->creditacnt."'";
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $credit=$dataReader->read();
            if($credit['account_type']==1):$payment='1';$bank='0';else:$payment='2';$bank=$model->creditacnt;endif;
            $date=date('M/Y',strtotime($_POST['contradate']));
            $model->voucher_no='00'. $voucherno ;
            $model->date=date('Y-m-d',strtotime($_POST['contradate']));
            $model->narration=$_POST['narration'];
            $model->payment=$payment;
            $model->bank_id=$bank;

            $model->cheque_no=$_POST['chequeno'];
            //if($_POST['payment']==1):$type="Receipt";else:$type="Payment";endif;
            $model->type="Payment";
            //echo $model->type;exit;
            $model->contra='1';
            $groupid=time();
            $model->contragroup=$groupid;
            $reqid=$_POST['requestid'];
            if($model->save(false))
            {
                ///$count=$model->voucher_no + 1 ;
                $count=$model->voucher_no;
                $vouchernum='00'. $count;
                $sql="SELECT account_type FROM accounts_item WHERE id='".$model->account_id."'";
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();
                $debit=$dataReader->read();
                if($credit['account_type']==1 & $debit['account_type']==1):
                    $sql="INSERT INTO voucher (place,project_id,account_id,creditacnt,amount,date,narration,payment,cheque_no,type,contra,voucher_no,contragroup) VALUES ('".$model->project_id."','".$model->place."','".$model->account_id."','".$model->creditacnt."','".$model->amount."','".$model->date."','".$model->narration."','1','".$model->cheque_no."','Receipt','1','$vouchernum','$groupid')";
                    $command=$connection->createCommand($sql);
                    $dataReader=$command->query();
                elseif($credit['account_type']==2 & $debit['account_type']==2):
                    $sql="INSERT INTO voucher (place,project_id,account_id,creditacnt,bank_id,amount,date,narration,payment,cheque_no,type,contra,voucher_no,contragroup) VALUES ('".$model->project_id."','".$model->place."','".$model->account_id."','".$model->creditacnt."','".$model->account_id."','".$model->amount."','".$model->date."','".$model->narration."','2','".$model->cheque_no."','Receipt','1','$vouchernum','$groupid')";
                    $command=$connection->createCommand($sql);
                    $dataReader=$command->query();
                else:
                    if($model->payment==1):$bankid=$debitacnt[0];$paytype=2;else:$bankid='0';$paytype='1';endif;
                    $sql="INSERT INTO voucher (place,project_id,account_id,creditacnt,bank_id,amount,date,narration,payment,cheque_no,type,contra,voucher_no,contragroup) VALUES ('".$model->project_id."','".$model->place."','".$model->account_id."','".$model->creditacnt."','".$bankid."','".$model->amount."','".$model->date."','".$model->narration."','$paytype','".$model->cheque_no."','Receipt','1','$vouchernum','$groupid')";
                    $command=$connection->createCommand($sql);
                    $dataReader=$command->query();
                endif;
                $sql="UPDATE finance_requests SET Status='3' WHERE Id='$reqid'";
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();
                $advanceid=$_POST['advanceid'];
                if($advanceid!=''):
                    if($advanceid!=0):
                        $advancemodel=Cashadvance::model()->findAll(array('condition'=>'cashadvance_id IN ('.$advanceid.')'));
                        $place=$advancemodel['project_id'];
                        if($place==12):
                            if($debit['account_type']==1):
                                $expense=1;
                            else:
                                $expense=0;
                            endif;
                        else:
                            if($debit['account_type']==1):
                                $expense=1;
                            else:
                                $expense=0;
                            endif;
                        endif;
                        foreach($advancemodel AS $item):
                            $item->expense_status=$expense;
                            $item->save(false);
                        endforeach;
                    endif;
                endif;
                $url=Yii::$app->request->baseUrl."/voucher/printcontravoucher?pid=".$model->id;
                $arr = array('Id' => $_POST['requestid'],'url'=>$url,'error'=>'No');
                return json_encode($arr);
            }
        }
    }

    public function actionReceiptvoucher()
    {

        $model=new Voucher; 
        $connection = Yii::$app->db;
        if(isset($_POST['requestid']))
        {
            $model->creditacnt=$_POST['creditacnt'];
            $model->place=$_POST['place'];
            $model->amount=$_POST['amount']; 
            
            if($_POST['payment']==1):
                $bankacnt=0;
                $debit=$_POST['debitacnt'];
            else:
                $bankacnt=$_POST['debitacnt'];
                $debit=$_POST['debitacnt'];
            endif;
            if(isset($_POST['accntschitem'])):

                if($_POST['accntschitem']!='none'){

                    $model->bsitem_id=$_POST['accntschitem'];

                }

            endif;
            $model->bank_id=$bankacnt;
            $model->account_id=$debit;
            $model->project_id=$_POST['projectid'];
            $sql="SELECT voucher_no FROM voucher WHERE date = '".date('Y-m-d',strtotime($_POST['receiptdate']))."' AND payment ='".$_POST['payment']."' AND type='Receipt' ORDER BY voucher_no DESC limit 1";
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $no=$dataReader->read();
            if(!empty($no)){
                if($no['voucher_no']!='')
                {
                    $voucherno=$no['voucher_no'] + 1;
                }
                else
                {
                    $voucherno='1';
                }
            }else{
                $voucherno='1';
            }
            //echo $sql;exit;
            $date=date('M/Y',strtotime($_POST['receiptdate']));
            $model->voucher_no='00'.$voucherno;
            $model->date=date('Y-m-d',strtotime($_POST['receiptdate']));
            $model->type='Receipt';
            $model->narration=$_POST['narration'];
            $model->payment=$_POST['payment'];
            if(isset($_POST['chequenum'])){
            $model->cheque_no=$_POST['chequenum'];
        }
            $reqid=$_POST['requestid'];
            //print_r($model);exit;
            if($model->save(false))
            {
                /*$sql="select count(id) as total from accounts_item where id='".$model->account_id."' and account_type='2' ";
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();
                $data=$dataReader->read();
                if($data['total']!=0)
                {
                    $count=$model->voucher_no + 1 ;
                    $vouchernum='00'. $count;
                    $sql="INSERT INTO voucher (place,project_id,voucher_no,date,amount,creditacnt,type,narration,payment,bank_id) values ('".$model->place."','".$model->project_id."','$vouchernum','".$model->date."','".$model->amount."','".$model->account_id."','Payment','".$model->narration."','2','".$model->account_id."')";
                    $command=$connection->createCommand($sql);
                    $dataReader=$command->query();
                }*/
                $sql="UPDATE fundreceipt SET Status='3' WHERE Id='$reqid'";
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();
                $url=Yii::$app->request->baseUrl."/voucher/printvoucher?pid=".$model->id;
                $arr = array('Id' => $_POST['requestid'],'url'=>$url,'error'=>'No');
                return json_encode($arr);

            }
        }
    }

    public function actionPrintvoucher()
    {   
        $id = $_GET['pid'];
        $connection = Yii::$app->db;
        $sql="SELECT a.project_id,a.IOW_Id,a.voucher_no,DATE_FORMAT(a.date,'%d %b %y') AS date,a.amount,a.account_id,a.creditacnt,a.type,a.narration,a.payment,a.place,a.bank_id,a.cheque_no,b.Name AS placename FROM voucher AS a INNER JOIN projects AS b ON a.place=b.Project_Id WHERE a.id='$id' ";
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $data=$dataReader->read();
        $iow=itemofworks::findOne($data['IOW_Id']);
        $sql3="SELECT Name FROM projects WHERE Project_Id='".$data['project_id']."'";
        $command=$connection->createCommand($sql3);
        $dataReader=$command->query();
        $project=$dataReader->read();
        $sql1="SELECT name from accounts_item where id='".$data['bank_id']."' ";
        $command=$connection->createCommand($sql1);
        $dataReader=$command->query();
        $bankname=$dataReader->read();
        $sql4="SELECT name FROM accounts_item where id='".$data['account_id']."'";
        $command=$connection->createCommand($sql4);
        $dataReader=$command->query();
        $drname=$dataReader->read();
        $sql4="SELECT name FROM accounts_item where id='".$data['creditacnt']."'";
        $command=$connection->createCommand($sql4);
        $dataReader=$command->query();
        $crname=$dataReader->read();

        $sql4="SELECT account_id FROM finance_requests where voucher_no='".$data['voucher_no']."'";
        $command=$connection->createCommand($sql4);
        $dataReader=$command->query();
        $bkname=$dataReader->read();

        $accounthead = AccountsItem::find()->where(['id'=> $bkname['account_id']])->one();
         


        $number = $data['amount'];
        $no = floor($number);
        $point = round($number - $no, 2) * 100;
        $hundred = null;
        $digits_1 = strlen($no);
        $i = 0;
        $str = array();
        $words = array('0' => '', '1' => 'one', '2' => 'two',
        '3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six',
        '7' => 'seven', '8' => 'eight', '9' => 'nine',
        '10' => 'ten', '11' => 'eleven', '12' => 'twelve',
        '13' => 'thirteen', '14' => 'fourteen',
        '15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen',
        '18' => 'eighteen', '19' =>'nineteen', '20' => 'twenty',
        '30' => 'thirty', '40' => 'forty', '50' => 'fifty',
        '60' => 'sixty', '70' => 'seventy',
        '80' => 'eighty', '90' => 'ninety');
        $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
        while ($i < $digits_1) {
         $divider = ($i == 2) ? 10 : 100;
         $number = floor($no % $divider);
         $no = floor($no / $divider);
         $i += ($divider == 10) ? 1 : 2;
         if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str [] = ($number < 21) ? $words[$number] .
                " " . $digits[$counter] . $plural . " " . $hundred
                :
                $words[floor($number / 10) * 10]
                . " " . $words[$number % 10] . " "
                . $digits[$counter] . $plural . " " . $hundred;
         } else $str[] = null;
        }
        $str = array_reverse($str);
        $result = implode('', $str);
        $points = ($point) ?
        "." . $words[$point / 10] . " " . 
              $words[$point = $point % 10] : '';
        $numtoword= $result .''. $points;
        //$numtoword= $result . "Rupees  " . $points . " Paise";



         $dt=$data['date'];
         $dte=date('d-M-Y', strtotime($dt));

         // $amt_words=855600.45;
 
                // $get_amount= AmountInWords($amt_words);
                 //echo $get_amount;
            $host = explode('.', $_SERVER['HTTP_HOST']);
            $subDomain = array_shift($host);

            if($subDomain ==  'fin'){
                $company_name = 'GEO-TECH OFFSHORE STRUCTURES.(P) LTD';
                $company_logo =  '<img src="'.Yii::$app->request->baseUrl.'/images/logo_geotech.jpg" alt="logo">';
            }
            elseif($subDomain  ==  'fin-opiam'){
                $company_name = '<span style="font-size:20px;">OPIAM ANALYTICS (P) LTD</span>';
                $company_logo =  '<img src="'.Yii::$app->request->baseUrl.'/images/opiam-analytics-logo-trans.png" alt="logo" style="max-width:200px;">';
            }
            elseif($subDomain  ==  'fin-test'){
                $company_name = 'GEO-TECH OFFSHORE STRUCTURES.(P) LTD';
                $company_logo =  '<img src="'.Yii::$app->request->baseUrl.'/images/logo_geotech.jpg" alt="logo">';
            }
      

        if($data['payment']=='1'):$type='Cash';else: $type='Bank';endif;
        if($data['type']=='Payment'):$accounttype='Debit';$accountname=$drname['name'];else: $accounttype='Credit';$accountname=$crname['name'];endif;
        if($data['payment']==2){$bank=$bankname['name'].'<br>'.'Cheque Number : '.$data['cheque_no'];}else{$bank='';};
        $html="<style>table,td{border:1px solid black;border-collapse:collapse}</style><br><br><br><br><table width='800' cellpadding='7px'><tbody>
                <tr>
                  <th width='200' height='43' align='left' scope='row' style='padding-left: 25px;'>".$company_logo."</th>
                  <th width='500' align='center' style='padding-top: 15px;'>".$company_name."<br>$type ".$data['type']." Voucher<br>$bank</th>
                  <th width='200'></th>
                </tr>
                <tr>
                <th colspan='2' align='left' style='padding-left: 25px;'>Place : ".$data['placename']."<br><br>Project : ".$project['Name']."</th>

                <th align='left'>No : ".$data['voucher_no']."<br><br>Date : ".$dte."</th>
                </tr>";

        if($iow):
            $html.="<tr ><th colspan='3' align='left' style='padding-left: 25px;'>IOW : " .$iow['Name']."</th></tr>";
        endif;

        $html.="<tr>
                <td colspan='2' align='center' ><b>Narration</b></td><td align='center'><b>Amount(Rs)</b></td></tr>
                <tr>
                <td height='150' colspan='2' align='left' scope='row' style='padding-left: 25px;'>$bank<br><b>$accounttype A/c.</b><br>".$accountname."<br><span class='white-text' style='margin-right: 5em;'>".$data['narration']."<br><b>Bank : ".$accounthead['name']."</b></td>
                <td align='right' style='padding-right: 25px;'>".number_format((float)$data['amount'], 2, '.', '')."</td>
                </tr>
                <tr>

                
                
                <td colspan='2' align='left' style='padding-left: 25px;'>Total :Rupees ".$numtoword." Only</td><td align='right' style='padding-right: 25px;'>".number_format((float)$data['amount'], 2, '.', '')."</td>
                </tr>
                <tr><td colspan='3'>
                <table width='800' style='height: 92px;border:0'>
                <tr><th><br><br>Prepared By/Cash paid<br>(Accountant)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                <th><br><br>Checked By<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(Accounts Manager)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                <th><br><br>Approved By<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(M.D/Director)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                <th><br><br>Receiver Signature..........</th></tr></table></td>
                </tr></tbody></table>";
        $vname = 'voucher-'.$id;
        $pdf = new Pdf();
        $mpdf = $pdf->api;
        $mpdf->WriteHtml($html); 
        $mpdf->Output(''.$vname.'.pdf','I');
    }

    public function actionPrintcontravoucher()
    {
        $id = $_GET['pid'];
        $connection = Yii::$app->db;
        $sql="SELECT a.id,a.place,a.project_id,a.voucher_no,a.date,a.account_id,a.creditacnt,a.amount,a.narration,b.Name FROM voucher AS a INNER JOIN projects AS b ON a.project_id=b.Project_Id WHERE a.id='$id' ";
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $data=$dataReader->read();
        $sql3="SELECT Name FROM projects WHERE Project_Id='".$data['place']."'";
        $command=$connection->createCommand($sql3);
        $dataReader=$command->query();
        $place=$dataReader->read();
        $sql4="SELECT a.account_id,b.name FROM voucher AS a INNER JOIN accounts_item AS b ON a.account_id=b.id where a.id='".$data['id']."'";
        $command=$connection->createCommand($sql4);
        $dataReader=$command->query();
        $drname=$dataReader->read();
        $sql4="SELECT a.creditacnt,b.name FROM voucher AS a INNER JOIN accounts_item AS b ON a.creditacnt=b.id where a.id='".$data['id']."'";
        $command=$connection->createCommand($sql4);
        $dataReader=$command->query();
        $crname=$dataReader->read();

        $host = explode('.', $_SERVER['HTTP_HOST']);
        $subDomain = array_shift($host);

        if($subDomain ==  'fin'){
            $company_name = 'GEO-TECH OFFSHORE STRUCTURES.(P) LTD';
            $company_logo =  '<img src="'.Yii::$app->request->baseUrl.'/images/logo_geotech.jpg" alt="logo">';
        }
        elseif($subDomain  ==  'fin-opiam'){
            $company_name = '<span style="font-size:20px;">OPIAM ANALYTICS (P) LTD</span>';
            $company_logo =  '<img src="'.Yii::$app->request->baseUrl.'/images/opiam-analytics-logo-trans.png" alt="logo" style="max-width:200px;">';
        }
        elseif($subDomain  ==  'fin-test'){
            $company_name = 'GEO-TECH OFFSHORE STRUCTURES.(P) LTD';
            $company_logo =  '<img src="'.Yii::$app->request->baseUrl.'/images/logo_geotech.jpg" alt="logo">';
        }



        $html="<style>table,td{border:1px solid black;border-collapse:collapse}</style><br><br><br><br><table width='800' cellpadding='7px'><tbody>
                <tr>
                  <th width='250' height='43' align='left' scope='row' style='padding-left: 25px;'>".$company_logo."</th>
                  <th width='500' align='center' style='padding-top: 15px;'>".$company_name."<br>Contra Voucher</th>
                  <th width='200'></th>
                </tr>
                <tr>
                <th colspan='2' align='left' style='padding-left: 25px;'>Place : ".$place['Name']."<br><br>Project : ".$data['Name']."</th>

                <th align='left'>No : ".$data['voucher_no']."<br><br>Date : ".date('d M Y',strtotime($data['date']))."</th>
                </tr>
                <tr>
                <td colspan='2' align='center'><b>Narration</b></td><td align='center'><b>Amount(Rs)</b></td>
                </tr>
                <tr>
                <td height='150' colspan='2' align='left' scope='row' style='padding-left: 25px;'><b>Debit A/c.</b><br>".$drname['name']."<br><b>Credit A/c.</b><br>".$crname['name']."<br><span class='white-text' style='margin-right: 5em;'>".$data['narration']."</td>
                <td align='right' style='padding-right: 25px;'>".number_format((float)$data['amount'], 2, '.', '')."</td>
                <tr>
                <td colspan='2' align='left' style='padding-left: 25px;'>Total : Rupees ".$data['amount']." Only</td><td align='right' style='padding-right: 25px;'>".number_format((float)$data['amount'], 2, '.', '')."</td>
                </tr>
                <tr><td colspan='3'><table width='800' style='height: 92px;border:0'>
                <tr><th><br><br>Prepared By/Cash paid<br>(Accountant)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                <th><br><br>Checked By<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(Accounts Manager)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                <th><br><br>Approved By<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(M.D/Director)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                <th><br><br>Receiver Signature..........</th></tr></table></td>
                </tr>
                </tbody></table>";
        $vname = 'Contravoucher-'.$id;
        $pdf = new Pdf();
        $mpdf = $pdf->api;
        $mpdf->WriteHtml($html); 
        $mpdf->Output(''.$vname.'.pdf','I');
    }

    
    public function actionPrintjournal()
    {
        if($_GET['pid']){
            $id = $_GET['pid'];
            $groupid=Journalvoucher::findOne($id)->group_id;
            $connection = Yii::$app->db;
            //$sql="SELECT a.id,a.place,a.project_id,a.voucher_no,a.date,a.debitacnt,a.creditacnt,a.amount,a.narration,a.type,a.narration,b.Name FROM journalvoucher AS a INNER JOIN projects AS b ON a.place=b.Project_Id WHERE a.id='$id' ";
            $sql="SELECT a.id,a.place,a.project_id,a.voucher_no,a.date,a.debitacnt,a.creditacnt,a.amount,a.narration,a.type,a.narration FROM journalvoucher AS a WHERE a.id='$id' ";
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $data=$dataReader->read();
            if($data['type']==1)
            {
                $type='Bill';
            }
            else
            {
                $type='Journal';
            }
            $sql5="SELECT Name FROM projects WHERE Project_Id='".$data['place']."'";
            $command=$connection->createCommand($sql5);
            $dataReader=$command->query();
            $projectname=$dataReader->read();

            $sql5="SELECT Name FROM projects WHERE Project_Id='".$data['project_id']."'";
            $command=$connection->createCommand($sql5);
            $dataReader=$command->query();
            $projectname1=$dataReader->read();

            $host = explode('.', $_SERVER['HTTP_HOST']);
            $subDomain = array_shift($host);
            if($subDomain ==  'fin'){
                $company_name = 'GEO-TECH OFFSHORE STRUCTURES.(P) LTD';
                $company_logo =  '<img src="'.Yii::$app->request->baseUrl.'/images/logo_geotech.jpg" alt="logo">';
            }
            elseif($subDomain  ==  'fin-opiam'){
                $company_name = '<span style="font-size:20px;">OPIAM ANALYTICS (P) LTD</span>';
                $company_logo =  '<img src="'.Yii::$app->request->baseUrl.'/images/opiam-analytics-logo-trans.png" alt="logo" style="max-width:200px;">';
            }
            elseif($subDomain  ==  'fin-test'){
                $company_name = 'GEO-TECH OFFSHORE STRUCTURES.(P) LTD';
                $company_logo =  '<img src="'.Yii::$app->request->baseUrl.'/images/logo_geotech.jpg" alt="logo">';
            }


            if($groupid==0):
                $sql4="SELECT a.debitacnt,b.name FROM journalvoucher AS a INNER JOIN accounts_item AS b ON a.debitacnt=b.id where a.id='".$data['id']."'";
                $command=$connection->createCommand($sql4);
                $dataReader=$command->query();
                $drname=$dataReader->read();
                $sql4="SELECT a.creditacnt,b.name FROM journalvoucher AS a INNER JOIN accounts_item AS b ON a.creditacnt=b.id where a.id='".$data['id']."'";
                $command=$connection->createCommand($sql4);
                $dataReader=$command->query();
                $crname=$dataReader->read();

              
               

               $number = $data['amount'];
               $no = floor($number);
               $point = round($number - $no, 2) * 100;
               $hundred = null;
               $digits_1 = strlen($no);
               $i = 0;
               $str = array();
               $words = array('0' => '', '1' => 'one', '2' => 'two',
                '3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six',
                '7' => 'seven', '8' => 'eight', '9' => 'nine',
                '10' => 'ten', '11' => 'eleven', '12' => 'twelve',
                '13' => 'thirteen', '14' => 'fourteen',
                '15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen',
                '18' => 'eighteen', '19' =>'nineteen', '20' => 'twenty',
                '30' => 'thirty', '40' => 'forty', '50' => 'fifty',
                '60' => 'sixty', '70' => 'seventy',
                '80' => 'eighty', '90' => 'ninety');
               $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
               while ($i < $digits_1) {
                 $divider = ($i == 2) ? 10 : 100;
                 $number = floor($no % $divider);
                 $no = floor($no / $divider);
                 $i += ($divider == 10) ? 1 : 2;
                 if ($number) {
                    $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                    $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                    $str [] = ($number < 21) ? $words[$number] .
                        " " . $digits[$counter] . $plural . " " . $hundred
                        :
                        $words[floor($number / 10) * 10]
                        . " " . $words[$number % 10] . " "
                        . $digits[$counter] . $plural . " " . $hundred;
                 } else $str[] = null;
              }
              $str = array_reverse($str);
              $result = implode('', $str);
              $points = ($point) ?
                "." . $words[$point / 10] . " " . 
                      $words[$point = $point % 10] : '';
              $numtowordz= $result . "Rupees  " . $points;






                $html="<style>table,td{border:1px solid black;border-collapse:collapse}</style><br><br><br><br><table width='800' cellpadding='7px'><tbody>
                    <tr>
                    <th width='250' height='43' align='left' scope='row' style='padding-left: 25px;'>".$company_logo."</th>
                    <th width='500' align='center' >".$company_name."<br>$type Voucher</th>
                    <th width='200' colspan='2'></th>
                    </tr>
                    <tr>
                        <th colspan='2' align='left' style='padding-left: 25px;'>Place : ".$projectname['Name']."<br><br>Project : ".$projectname1['Name']."</th>
                        <th align='left' colspan='2' style='padding-left: 25px;'>No : ".$data['voucher_no']."<br>Date : ".date('d M Y',strtotime($data['date']))."</th>
                    </tr>
                    <tr>
                        <td align='left' colspan='2' style='padding-left: 25px;'>Particulars</td>
                        <td>Debit</td>
                        <td>Credit</td>
                    </tr>
                    <tr>
                        <td height='150' colspan='2' align='left' scope='row' style='padding-left: 25px;'><b>Debit A/c.".$drname['name']."</b><br><br><b>Credit A/c.".$crname['name']."</b><br>".$data['narration']."</td>
                        <td align='right' style='padding-right: 25px;'>".number_format((float)$data['amount'], 2, '.', '')."</td>
                        <td align='right' style='padding-right: 25px;'><br><br>".number_format((float)$data['amount'], 2, '.', '')."</td>
                    </tr>
                    <tr>
                        <td colspan='2' align='left' style='padding-left: 25px;'>Rupees : ".$numtowordz." Only</td><td align='right' style='padding-right: 25px;'>".number_format((float)$data['amount'], 2, '.', '')."</td><td align='right' style='padding-right: 25px;'>".number_format((float)$data['amount'], 2, '.', '')."</td>
                    </tr>
                    <tr><td colspan='4'><table width='800' style='height: 92px;border:0'>
                    <tr><th><br><br>Prepared By/Cash paid<br>(Accountant)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                    <th><br><br>Checked By<br>&nbsp;&nbsp;&nbsp;&nbsp;(Accounts Manager)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>

                    <th><br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                    Approved By<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    (M.D/Director)</th>
                    </tr></table></td>
                    </tr>
                    </tbody></table>";
            else:
                $sql="SELECT debitacnt,creditacnt,amount FROM journalvoucher WHERE group_id='$groupid' ";
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();
                $voucheritems=$dataReader->readAll();
                $creditcount=0;
                $totamount=0;
                foreach($voucheritems AS $key=>$voucheritem):
                    if($key==0):
                        $oldone=$voucheritem['creditacnt'];
                    endif;
                    if($oldone==$voucheritem['creditacnt']):
                        $creditcount++;
                    endif;
                    $totamount=$totamount + $voucheritem['amount'];
                endforeach;
                //echo $creditcount;exit;
                //$sql="SELECT a.id,a.place,a.project_id,a.voucher_no,a.date,a.debitacnt,a.creditacnt,a.amount,a.narration,a.type,a.narration,b.Name FROM journalvoucher AS a INNER JOIN projects AS b ON a.place=b.Project_Id WHERE a.group_id='$groupid' ";
                $sql="SELECT a.id,a.place,a.project_id,a.voucher_no,a.date,a.debitacnt,a.creditacnt,a.amount,a.narration,a.type,a.narration FROM journalvoucher AS a WHERE a.group_id='$groupid' ";
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();
                $accountitems=$dataReader->readAll();

                if($creditcount>1){
                    $sql4="SELECT a.creditacnt,b.name FROM journalvoucher AS a INNER JOIN accounts_item AS b ON a.creditacnt=b.id where a.id='".$data['id']."'";
                    $command=$connection->createCommand($sql4);
                    $dataReader=$command->query();
                    $crname=$dataReader->read();
                    $acntrows='';
                    $acntrows.='<tr>
                                <td colspan="2" align="left" scope="row" style="padding-left: 25px;"><b>Credit A/c.'.$crname['name'].'</b></td>
                                <td></td>
                                <td>'.number_format((float)$totamount, 2, '.', '').'</td>
                                </tr>';
                    $toalamount=0;
                    foreach($accountitems AS $accountitem):
                        $sql4="SELECT a.debitacnt,b.name FROM journalvoucher AS a INNER JOIN accounts_item AS b ON a.debitacnt=b.id where a.id='".$accountitem['id']."'";
                        $command=$connection->createCommand($sql4);
                        $dataReader=$command->query();
                        $drname=$dataReader->read();
                        $acntrows.="<tr>
                        <td height='50' colspan='2' align='left' scope='row' style='padding-left: 25px;'><b>Debit A/c.".$drname['name']."</b><br>".$accountitem['narration']."</td>
                        <td align='right' style='padding-right: 25px;padding-bottom: 25px;'>".number_format((float)$accountitem['amount'], 2, '.', '')."</td>
                        <td align='right' style='padding-right: 25px;'></td>
                        </tr>";
                        $toalamount=$toalamount + $accountitem['amount'];
                    endforeach;
                }
                else{
                    $sql4="SELECT a.debitacnt,b.name FROM journalvoucher AS a INNER JOIN accounts_item AS b ON a.debitacnt=b.id where a.id='".$data['id']."'";
                    $command=$connection->createCommand($sql4);
                    $dataReader=$command->query();
                    $drname=$dataReader->read();
                    $acntrows='';
                    $acntrows.='<tr>
                                <td colspan="2" align="left" scope="row" style="padding-left: 25px;"><b>Debit A/c.'.$drname['name'].'</b></td>
                                <td>'.number_format((float)$totamount, 2, '.', '').'</td>
                                <td></td>
                                </tr>';
                    $toalamount=0;
                    foreach($accountitems AS $accountitem):
                        $sql4="SELECT a.creditacnt,b.name FROM journalvoucher AS a INNER JOIN accounts_item AS b ON a.creditacnt=b.id where a.id='".$accountitem['id']."'";
                        $command=$connection->createCommand($sql4);
                        $dataReader=$command->query();
                        $crname=$dataReader->read();
                        $acntrows.="<tr>
                        <td height='50' colspan='2' align='left' scope='row' style='padding-left: 25px;'><b>Credit A/c.".$crname['name']."</b><br>".$accountitem['narration']."</td>
                        <td align='right' style='padding-right: 25px;'></td>
                        <td align='right' style='padding-right: 25px;padding-bottom: 25px;'>".number_format((float)$accountitem['amount'], 2, '.', '')."</td>
                        </tr>";
                        $toalamount=$toalamount + $accountitem['amount'];
                    endforeach;
                }
                   

                     $number =  $toalamount;
                   $no = floor($number);
                   $point = round($number - $no, 2) * 100;
                   $hundred = null;
                   $digits_1 = strlen($no);
                   $i = 0;
                   $str = array();
                   $words = array('0' => '', '1' => 'one', '2' => 'two',
                    '3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six',
                    '7' => 'seven', '8' => 'eight', '9' => 'nine',
                    '10' => 'ten', '11' => 'eleven', '12' => 'twelve',
                    '13' => 'thirteen', '14' => 'fourteen',
                    '15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen',
                    '18' => 'eighteen', '19' =>'nineteen', '20' => 'twenty',
                    '30' => 'thirty', '40' => 'forty', '50' => 'fifty',
                    '60' => 'sixty', '70' => 'seventy',
                    '80' => 'eighty', '90' => 'ninety');
                   $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
                   while ($i < $digits_1) {
                     $divider = ($i == 2) ? 10 : 100;
                     $number = floor($no % $divider);
                     $no = floor($no / $divider);
                     $i += ($divider == 10) ? 1 : 2;
                     if ($number) {
                        $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                        $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                        $str [] = ($number < 21) ? $words[$number] .
                            " " . $digits[$counter] . $plural . " " . $hundred
                            :
                            $words[floor($number / 10) * 10]
                            . " " . $words[$number % 10] . " "
                            . $digits[$counter] . $plural . " " . $hundred;
                     } else $str[] = null;
                  }
                  $str = array_reverse($str);
                  $result = implode('', $str);
                  $points = ($point) ?
                    "." . $words[$point / 10] . " " . 
                          $words[$point = $point % 10] : '';
                  $numtoword= $result . "Rupees  " . $points;


                $html="<style>table,td{border:1px solid black;border-collapse:collapse}</style><br><br><br><br><table width='800' cellpadding='7px'><tbody>
                    <tr>
                    <th width='250' height='43' align='left' scope='row' style='padding-left: 25px; padding-top: 15px;'>".$company_logo."</th>
                    <th width='500' align='center' >".$company_name."<br>$type Voucher</th>
                    <th width='200' colspan='2'></th>
                    </tr>
                    <tr>
                    <th colspan='2' align='left' style='padding-left: 25px;'>Place : ".$projectname['Name']."<br><br>Project : ".$projectname1['Name']."</th>

                    <th align='left' colspan='2' style='padding-left: 25px;'>No : ".$data['voucher_no']."<br>Date : ".date('d M Y',strtotime($data['date']))."</th>
                    </tr>
                    <tr>
                    <td align='left' colspan='2' style='padding-left: 25px;'>Particulars</th>
                    <td>Debit</td>
                    <td>Credit</td>
                    </tr>
                    ".$acntrows."
                    <tr>
                    <td colspan='2' align='left' style='padding-left: 25px;'>Rupees : ".$numtoword." Only</td><td align='right' style='padding-right: 25px;'>".number_format((float)$toalamount, 2, '.', '')."</td><td align='right' style='padding-right: 25px;'>".number_format((float)$toalamount, 2, '.', '')."</td>
                    </tr>
                    <tr><td colspan='4'><table width='800' style='height: 92px;border:0'>
                    <tr><th><br><br>Prepared By/Cash paid<br>(Accountant)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                    <th><br><br>Checked By<br>&nbsp;&nbsp;&nbsp;&nbsp;(Accounts Manager)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                    <th><br><br>Approved By<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(M.D/Director)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                    </tr></table></td>
                    </tr>
                    </tbody></table>";
            endif;
            $vname = 'voucher-'.$id;
            $pdf = new Pdf();
            $mpdf = $pdf->api;
            $mpdf->WriteHtml($html); 
            $mpdf->Output(''.$vname.'.pdf','I');
        }
    }

    public function actionDeletepayment()
    {
        $connection = Yii::$app->db;
        $sql1="UPDATE finance_requests SET Status='4' WHERE Id='".$_POST['voucherid']."' ";
        $command=$connection->createCommand($sql1);
        $dataReader=$command->query();
        $arr = array('Id' => $_POST['voucherid'],'error'=>'No');
        return json_encode($arr);
    }
    public function actionDeletereceipt()
    {
        $connection = Yii::$app->db;
        $sql1="DELETE FROM fundreceipt WHERE Id='".$_POST['voucherid']."' ";
        $command=$connection->createCommand($sql1);
        $dataReader=$command->query();
        $arr = array('Id' => $_POST['voucherid'],'error'=>'No');
        return json_encode($arr);
    }
    public function actionDeletejournal()
    {
        $connection = Yii::$app->db;
        /*$sql2="SELECT billid from billitems where itemid='".$_POST['billid']."' ";Status
        $command=$connection->createCommand($sql2);
        $dataReader=$command->query();
        $billid=$dataReader->read();*/
        $sql1="UPDATE journalitems SET Status='4' WHERE journalid='".$_POST['journalid']."' ";
        $command=$connection->createCommand($sql1);
        $dataReader=$command->query();
        //$journal=Journels::model()->findByPk($_POST['journalid']);
        //$journal->delete();
        $sql2="UPDATE journels SET Status='4' WHERE id='".$_POST['journalid']."' ";
        $command=$connection->createCommand($sql2);
        $dataReader=$command->query();
        /*$sql3="DELETE FROM bills WHERE bill_id='".$billid['billid']."' ";
        $command=$connection->createCommand($sql3);
        $dataReader=$command->query();*/
        $arr = array('Id' => $_POST['journalid'],'error'=>'No');
        return json_encode($arr);
    }

    /*public function actionGeneratejournal()
    {
        echo 'hai'; exit;
        $connection = Yii::$app->db;
        if(isset($_POST['journalid']))
        {
            $groupid=time();
            //$sql="SELECT voucher_no FROM journalvoucher WHERE date = '".date('Y-m-d')."' AND type='2' ORDER BY voucher_no DESC limit 1";
            $sql="SELECT id FROM journalvoucher ORDER BY id DESC limit 1";
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $no=$dataReader->read();
            if(!empty($no)){
                if($no['id']!='')
                {
                    $voucherno=$no['id'] + 1;
                }
                else
                {
                    $voucherno='1';
                }
            }else{
                $voucherno='1';
            }
            //echo count($_POST['journalitem']);exit;
            if(isset($_POST['journalitem'])){
                for($i=0;$i<count($_POST['journalitem']);$i++)
                {
                    $model=new Journalvoucher();
                    if($_POST['type']=='debit'):
                        $model->debitacnt=$_POST['debitaccounts'][$i];
                        $model->creditacnt=$_POST['creditaccount'];
                    else:
                        $model->debitacnt=$_POST['debitaccount'];
                        $model->creditacnt=$_POST['creditaccounts'][$i];
                    endif;
                    $model->place=$_POST['projectid'];
                    $model->amount=$_POST['debitamount'][$i];
                    $model->project_id=$_POST['place'];
                    $model->narration=$_POST['narration'][$i];
                    $model->type='2';
                    $date=date('M/Y');
                    $model->voucher_no='v'.$voucherno;
                    //$model->date=date('Y-m-d',strtotime($_POST['date']));
                    $model->date=date('Y-m-d');
                    //$model->payment=$_POST['payment'];
                    $model->audit_status=1;
                    $model->group_id=$groupid;
                    $journalid=$_POST['journalid']; 
                    //print_r($model);

                   
                    if($model->save(false))
                    {
                        
                        $sql="UPDATE journalitems SET Status='1' WHERE id='".$_POST['journalitem'][$i]."'";
                        $command=$connection->createCommand($sql);
                        $dataReader=$command->query();
                        $sql="select COUNT(id) as total from journalitems where journalid='$journalid' and Status='0' ";
                        $command=$connection->createCommand($sql);
                        $dataReader=$command->query();
                        $rows=$dataReader->read();
                        if($rows['total']==0)
                        {
                            $sql="UPDATE journels SET Status='3' WHERE id='$journalid'";
                            $command=$connection->createCommand($sql);
                            $dataReader=$command->query();
                        }
                    }
                }
            }else{
                $model=new Journalvoucher();
                    //if($_POST['type']=='debit'):
                        //$model->debitacnt=$_POST['debitaccounts'][$i];
                        //$model->creditacnt=$_POST['creditaccount'];
                    //else:
                        //$model->debitacnt=$_POST['debitaccount'];
                        $model->creditacnt=$_POST['creditaccounts'];
                    //endif;
                    $model->place=$_POST['projectid'];
                    $model->amount=$_POST['creditamount'];
                    $model->project_id=$_POST['place'];
                    //$model->narration=$_POST['narration'];
                    //$model->type='2';
                    $date=date('M/Y');
                    $model->voucher_no='v'.$voucherno;
                    $model->date=date('Y-m-d',strtotime($_POST['date']));
                    //$model->payment=$_POST['payment'];
                    $model->group_id=$groupid;
                    $journalid=$_POST['journalid'];
                    $model->audit_status=1;
                    //print_r($model);
                    if($model->save(false))
                $journalid=$_POST['journalid'];
                $sql="UPDATE journels SET Status='3' WHERE id='$journalid'";
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();

            }
            $url=Yii::$app->request->baseUrl."/voucher/printjournal?pid=".$model->id;
            $arr = array('Journalitem' => $journalid,'url'=>$url,'error'=>'No');
            return json_encode($arr);

        }
    }*/

    public function actionAddscheduledropdown()
    {
        $connection = Yii::$app->db;

        $debitID=$_POST['debitid'];

        $dataVrow='';

        $bsitems = Bsitems::find()->where(['status'=> 0])->andWhere(['accnt_id'=> $debitID])->all();

        if($bsitems):

            $dataVrow.='<div class="form-group">
                    <label>Schedule Item</label>
                        <select class="form-control" name="accntschitem" id="accntschitem">
                            <option value="none">Select Schedule Item</option>';

            foreach($bsitems as $key => $bsitem):  
                if($key==0):
                    $selected="selected"; 
                else:
                    $selected="";
                endif; 

                $dataVrow.= '<option value="'.$bsitem->item_id.'" '.$selected.'>'.$bsitem->itemname.'</option>';

            endforeach;

            $dataVrow.='   </select><span class="error"></span>
                            </div>';
        endif;

        $arr = array('result' => $dataVrow,'error'=>'No');
        return json_encode($arr);
    }

    public function actionGeneratejournal()
    {
        //echo 'hai'; exit;
        $connection = Yii::$app->db;
        if(isset($_POST['journalid']))
        {
            $groupid=time();
            //$sql="SELECT voucher_no FROM journalvoucher WHERE date = '".date('Y-m-d')."' AND type='2' ORDER BY voucher_no DESC limit 1";
            $sql="SELECT id FROM journalvoucher ORDER BY id DESC limit 1";
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $no=$dataReader->read();
            if(!empty($no)){
                if($no['id']!='')
                {
                    $voucherno=$no['id'] + 1;
                }
                else
                {
                    $voucherno='1';
                }
            }else{
                $voucherno='1';
            }
            //echo count($_POST['journalitem']);exit;
            if(isset($_POST['journalitem'])){
                $debitcount=count($_POST['debitaccounts']);
                $creditcount=count($_POST['creditaccounts']);
                if($debitcount > $creditcount){
                    $nocount = $debitcount;
                }
                else{
                    $nocount = $creditcount;
                }
                for($i=0;$i<$nocount;$i++)
                {
                    $model=new Journalvoucher();
                    if($debitcount > $creditcount):
                        $model->debitacnt=$_POST['debitaccounts'][$i];
                        $model->creditacnt=$_POST['creditaccounts'][0];
                        $model->amount=$_POST['debitamount'][$i];
                    else:
                        $model->debitacnt=$_POST['debitaccounts'][0];
                        $model->creditacnt=$_POST['creditaccounts'][$i];
                        $model->amount=$_POST['creditamount'][$i];
                    endif;
                    //$model->place=$_POST['projectid'];
                    $model->place=$_POST['place'];
                    //$model->amount=$_POST['debitamount'][$i];
                    //$model->project_id=$_POST['place'];
                    $model->project_id=$_POST['projectid'];
                    $model->narration=$_POST['narration'][$i];
                    $model->type='2';
                    $date=date('M/Y');
                    $model->voucher_no='v'.$voucherno;
                    $model->date=date('Y-m-d',strtotime($_POST['date']));
                   // $model->date=date('Y-m-d');
                  //$model->payment=$_POST['payment'];
                    if(isset($_POST['accntschitem'])):
                        if($_POST['accntschitem']!='none'){
                            $model->bsitem_id=$_POST['accntschitem'];
                        }
                    endif;
                    $model->audit_status=1;
                    $model->group_id=$groupid;
                    $journalid=$_POST['journalid'];
                    //print_r($model);
                    if($model->save(false))
                    {
                        if(isset($_POST['journalitem'][$i])){
                            $sql="UPDATE journalitems SET Status='1' WHERE id='".$_POST['journalitem'][$i]."'";
                            $command=$connection->createCommand($sql);
                            $dataReader=$command->query();
                        }
                        $sql="select COUNT(id) as total from journalitems where journalid='$journalid' and Status='0' ";
                        $command=$connection->createCommand($sql);
                        $dataReader=$command->query();
                        $rows=$dataReader->read();
                        if($rows['total']==0)
                        {
                            $sql="UPDATE journels SET Status='3' WHERE id='$journalid'";
                            $command=$connection->createCommand($sql);
                            $dataReader=$command->query();
                        }
                    }
                }
            }else{
                $model=new Journalvoucher();
                    //if($_POST['type']=='debit'):
                        //$model->debitacnt=$_POST['debitaccounts'][$i];
                        //$model->creditacnt=$_POST['creditaccount'];
                    //else:
                        //$model->debitacnt=$_POST['debitaccount'];
                        $model->creditacnt=$_POST['creditaccounts'];
                    //endif;
                    //$model->place=$_POST['projectid'];
                    $model->place=$_POST['place'];
                    $model->amount=$_POST['creditamount'];
                   // $model->project_id=$_POST['place'];
                   $model->project_id=$_POST['projectid'];
                    //$model->narration=$_POST['narration'];
                    //$model->type='2';
                    $date=date('M/Y');
                    $model->voucher_no='v'.$voucherno;
                    $model->date=date('Y-m-d',strtotime($_POST['date']));
                    //$model->payment=$_POST['payment'];
                    $model->group_id=$groupid;
                    $journalid=$_POST['journalid'];
                    $model->audit_status=1;
                    //print_r($model);
                    if($model->save(false))
                $journalid=$_POST['journalid'];
                $sql="UPDATE journels SET Status='3' WHERE id='$journalid'";
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();

            }
            $url=Yii::$app->request->baseUrl."/voucher/printjournal?pid=".$model->id;
            $arr = array('Journalitem' => $journalid,'url'=>$url,'error'=>'No');
            return json_encode($arr);

        }
    }


}
