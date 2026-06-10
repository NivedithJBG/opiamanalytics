<?php

namespace app\controllers;  

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\UserTabs;  
use app\models\FinanceRequests; 
use app\models\Projects;
use app\models\Voucher;
use amnah\yii2\user\models\User;   
use app\models\UserAccounts;
use app\models\AccountsItem; 
use app\models\CActiveRecord;
use app\models\UserProjects;
use app\models\Notifications;
use app\models\Journels;
use app\models\Cashbills;
use app\models\Cashadvance;
use app\models\LedgerOpeningbalance;
use app\widgets\Alert;
use app\models\AccountTypes;
use app\models\Journalvoucher;
use app\models\Micropermission;
use app\models\Fundreceipt;
use app\models\AccountsSub;
use app\models\ProjuserSelection;
use app\models\CashbankuserSelection;
use kartik\mpdf\Pdf;
use app\models\Bsitems;


class FinancerequestsController extends Controller
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
       
        $userid=Yii::$app->user->id;
        $tabs = UserTabs::find()->where(['user_id' => $userid])->andWhere('function_id =2')->all();

        return $this->render('index',[
            'tabs'=>$tabs
        ]);
    }
    public function actionFindashboard()
    {
        return $this->render('findashboard');
    } 

    // table rows Action

    /*public function actionFundreqdata()
    {
        $datarows = '<div id="fin-veri-headOne">';
       
        $userid = Yii::$app->user->Id; 
        $user = User::find()->where(['id'=>$userid])->one();
        $useraccount = UserAccounts::find()->where(['user_id'=>$userid])->orderBy(['account_id' => SORT_ASC])->one();
        $userProjects = UserProjects::find()->where(['userid'=>$userid])->all();
        if( $user->superuser == 1 || $user->superuser == 2){
            foreach($userProjects as $userProject)
            {
                $accountitem = AccountsItem::find()->where(['projectid'=> $userProject->projectid])->andWhere(['favourite'=>1])->all();
                $initialdate='2013-04-01';
                $startdate=date('Y-m-d');
                 //print_r($accountitem);exit;                               
                foreach($accountitem as $bankslist)
                {
                    if($bankslist->account_type == 1){ //cash
                        $ledgerbal = LedgerOpeningbalance::find()->where(['accountid'=> $bankslist->id])->one();
                        $paymenttotal = Voucher::find()->where(['type'=> 'Payment'])->andWhere(['place'=> '12'])->andWhere(['payment'=> '1'])->andWhere(['between', 'date', $initialdate, $startdate])->sum('amount');
                        $receipttotal = Voucher::find()->where(['type'=> 'Receipt'])->andWhere(['place'=> '12'])->andWhere(['payment'=> '1'])->andWhere(['between', 'date', $initialdate, $startdate])->sum('amount');
                        if(is_null($paymenttotal)){
                            $paymenttotal = 0;
                        }
                        if(is_null($receipttotal)){
                            $receipttotal = 0;
                        }
                        $checkRbal = $paymenttotal - $receipttotal;

                        if($ledgerbal){
                        $openbalance = $ledgerbal->balance + $checkRbal;
                   }else{
                    $openbalance =  $checkRbal;
                   }
                    }else{  //bank
                        $ledger = LedgerOpeningbalance::find()->where(['accountid'=> $bankslist->id])->one();
                        $paymenttotal = Voucher::find()->where(['type'=> 'Payment'])->andWhere(['place'=> '12'])->andWhere(['bank_id'=> $bankslist->id])->andWhere(['payment'=> '2'])->andWhere(['between', 'date', $initialdate, $startdate])->sum('amount');
                        $receipttotal = Voucher::find()->where(['type'=> 'Receipt'])->andWhere(['place'=> '12'])->andWhere(['bank_id'=> $bankslist->id])->andWhere(['payment'=> '2'])->andWhere(['between', 'date', $initialdate, $startdate])->sum('amount');
                        if(is_null($paymenttotal)){
                            $paymenttotal = 0;
                        }
                        if(is_null($receipttotal)){
                            $receipttotal = 0;
                        }
                        $checkRbal = $paymenttotal - $receipttotal;
                        if(is_null($ledger)){
                            $ledgerbal = 0;
                        }else{
                            $ledgerbal = $ledger->balance;
                        }
                        $openbalance = $ledgerbal + $checkRbal;
                    }
                    $finRequest = FinanceRequests::find()->where(['Status' => 5])->andWhere(['account_id' => $bankslist->id])->andWhere(['User_Id' => $userid])->all();
                    if(!empty($finRequest)){
                        $totalfin = 0;
                        foreach ($finRequest as $totalfinamount){
                            
                            $totalfin = $totalfin + $totalfinamount->netamount;
                        }
                    }else{
                        $totalfin = 0;
                    }


                    $datarows.='
                                <div class="col-md-3">
                                    <!--card starts here -->
                                    <div class="acctheadid card" data-id="'.$bankslist->id.'" id="acctheadid_'.$bankslist->id.'">
                                        <input name="ah" type="radio" />
                                        <div class="card-header">
                                            <span class="icon-library1"></span>
                                            <div class="account-head-number">
                                                <span>Accounthead</span>
                                                <span>'.$bankslist->name.'</span>
                                            </div>
                                        </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 account-head-number">
                                                <span>Opening Balance</span>
                                                <input type="hidden" id="bankopenbalance_'.$bankslist->id.'" value="'.$openbalance.'">
                                                <input type="hidden" id="bankrowtotal_'.$bankslist->id.'" value="'.$totalfin.'">
                                                <span>'.number_format($openbalance,2).'</span>
                                            </div>
                                            <div class="col-md-6 text-right account-head-number">
                                                <span >Total Amount</span>
                                                <span id="TotalAmount_'.$bankslist->id.'">'.number_format($totalfin,2).'</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="row">
                                            <div class="col-md-6  icon-groups">
                                                <a href="#" class="btn btn-primary icon-plus add-fv-btn" id="selectbank" data-id="'.$bankslist->id.'"></a>
                                                <input type="hidden" id="selectbankname_'.$bankslist->id.'" value="'.$bankslist->name.'" /> 
                                            </div>
                                            <div class="col-md-6 text-right account-head-number">
                                                <span>Closing Balance</span>
                                                <span id="ClosingBalance_'.$bankslist->id.'">'.number_format($openbalance-$totalfin,2).'</span>
                                            </div>
                                        </div>  
                                    </div>
                                    </div>
                                    <!--card end here -->
                                </div>';
   
                }
                
                $bankLists = array("Axis Bank - 915020065069515"=>"169","SIB - OD - 0025081000002165"=>"631");

                foreach($bankLists as $bankname => $code) 
                {
                    
                    $ledger = LedgerOpeningbalance::find()->where(['accountid'=> $code])->one();
                    $paymenttotal = Voucher::find()->where(['type'=> 'Payment'])->andWhere(['place'=> '12'])->andWhere(['bank_id'=> $code])->andWhere(['payment'=> '2'])->andWhere(['between', 'date', $initialdate, $startdate])->sum('amount');
                    $receipttotal = Voucher::find()->where(['type'=> 'Receipt'])->andWhere(['place'=> '12'])->andWhere(['bank_id'=> $code])->andWhere(['payment'=> '2'])->andWhere(['between', 'date', $initialdate, $startdate])->sum('amount');
                    if(is_null($paymenttotal)){
                        $paymenttotal = 0;
                    }
                    if(is_null($receipttotal)){
                        $receipttotal = 0;
                    }
                    $checkRbal = $paymenttotal - $receipttotal;
                    if(is_null($ledger)){
                        $ledgerbal = 0;
                    }else{
                        $ledgerbal = $ledger->balance;
                    }
                    $openbalance = $ledgerbal + $checkRbal;
                    $finRequestt = FinanceRequests::find()->where(['Status' => 5])->andWhere(['account_id' => $code])->andWhere(['User_Id' => $userid])->all();
                    if(!empty($finRequestt)){
                        $totalfin = 0;
                        foreach ($finRequestt as $totalfinamount){
                            
                            $totalfin = $totalfin + $totalfinamount->netamount;
                        }
                    }else{
                        $totalfin = 0;
                    }

                    $datarows.='
                                <div class="col-md-3">
                                    <!--<div class="card ">-->
                                    <div class="acctheadid card" data-id="'.$code.'" id="acctheadid_'.$code.'">
                                        <input name="ah" type="radio" />
                                        <div class="card-header">
                                            <span class="icon-library1"></span>
                                            <div class="account-head-number">
                                                <span>Accounthead</span>
                                                <span>'.$bankname.'</span>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6 account-head-number">
                                                    <span>Opening Balance</span>
                                                    <input type="hidden" id="bankopenbalance_'.$code.'" value="'.$openbalance.'">
                                                    <input type="hidden" id="bankrowtotal_'.$code.'" value="'.$totalfin.'">
                                                    <span>'.number_format($openbalance,2).'</span>
                                                </div>
                                                <div class="col-md-6 text-right account-head-number">
                                                    <span >Total Amount</span>
                                                    <span id="TotalAmount_'.$code.'">'.number_format($totalfin,2).'</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <div class="row">
                                                <div class="col-md-6  icon-groups">
                                                    <a href="#" class="btn btn-primary icon-plus add-fv-btn" id="selectbank" data-id="'.$code.'"></a>
                                                    <input type="hidden" id="selectbankname_'.$code.'" value="'.$bankname.'" /> 
                                                </div>
                                                <div class="col-md-6 text-right account-head-number">
                                                    <span>Closing Balance</span>
                                                    <span id="ClosingBalance_'.$code.'">'.number_format($openbalance-$totalfin,2).'</span>
                                                </div>
                                            </div>  
                                        </div>
                                    </div>
                                </div>';
                }  
            }
        }
        else
        {
            foreach($userProjects as $userProject)
            {
                $accountitem = AccountsItem::find()->where(['projectid'=> $userProject->projectid])->andWhere(['favourite'=>1])->one();
                $initialdate='2013-04-01';
                $startdate=date('Y-m-d');

                foreach($accountitem as $bankslist)
                {
                    if($bankslist->account_type == 1){ //cash
                        $ledgerbal = LedgerOpeningbalance::find()->where(['accountid'=> $bankslist->id])->one();
                        $paymenttotal = Voucher::find()->where(['type'=> 'Payment'])->andWhere(['place'=> '12'])->andWhere(['payment'=> '1'])->andWhere(['between', 'date', $initialdate, $startdate])->sum('amount');
                        $receipttotal = Voucher::find()->where(['type'=> 'Receipt'])->andWhere(['place'=> '12'])->andWhere(['payment'=> '1'])->andWhere(['between', 'date', $initialdate, $startdate])->sum('amount');
                        if(is_null($paymenttotal)){
                            $paymenttotal = 0;
                        }
                        if(is_null($receipttotal)){
                            $receipttotal = 0;
                        }
                        $checkRbal = $paymenttotal - $receipttotal;
                        $openbalance = $ledgerbal->balance + $checkRbal;
                    }else{  //bank
                        $ledger = LedgerOpeningbalance::find()->where(['accountid'=> $bankslist->id])->one();
                        $paymenttotal = Voucher::find()->where(['type'=> 'Payment'])->andWhere(['place'=> '12'])->andWhere(['bank_id'=> $bankslist->id])->andWhere(['payment'=> '2'])->andWhere(['between', 'date', $initialdate, $startdate])->sum('amount');
                        $receipttotal = Voucher::find()->where(['type'=> 'Receipt'])->andWhere(['place'=> '12'])->andWhere(['bank_id'=> $bankslist->id])->andWhere(['payment'=> '2'])->andWhere(['between', 'date', $initialdate, $startdate])->sum('amount');
                        if(is_null($paymenttotal)){
                            $paymenttotal = 0;
                        }
                        if(is_null($receipttotal)){
                            $receipttotal = 0;
                        }
                        $checkRbal = $paymenttotal - $receipttotal;
                        if(is_null($ledger)){
                            $ledgerbal = 0;
                        }else{
                            $ledgerbal = $ledger->balance;
                        }
                        $openbalance = $ledgerbal + $checkRbal;
                        $finRequestt = FinanceRequests::find()->where(['Status' => 5])->andWhere(['account_id' => $code])->andWhere(['User_Id' => $userid])->all();
                        if(!empty($finRequestt)){
                            $totalfin = 0;
                            foreach ($finRequestt as $totalfinamount){
                                
                                $totalfin = $totalfin + $totalfinamount->netamount;
                            }
                        }else{
                            $totalfin = 0;
                        }
                        
                    }

                    $datarows.='
                                <div class="col-md-3">
                                    <!--card starts here -->
                                    <!--<div class="card" >-->
                                    <div class="acctheadid card" data-id="'.$bankslist->id.'" id="acctheadid_'.$bankslist->id.'">
                                        <input name="ah" type="radio" />
                                        <div class="card-header">
                                            <span class="icon-library1"></span>
                                            <div class="account-head-number">
                                                <span>Accounthead</span>
                                                <span>'.$bankslist->name.'</span>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6 account-head-number">
                                                    <span>Opening Balance</span>
                                                    <input type="hidden" id="bankopenbalance_'.$bankslist->id.'" value="'.$openbalance.'">
                                                     <input type="hidden" id="bankrowtotal_'.$bankslist->id.'" value="'.$totalfin.'">
                                                    <span>'.number_format($openbalance,2).'</span>
                                                </div>
                                                <div class="col-md-6 text-right account-head-number">
                                                    <span>Total Amount</span>
                                                    <span id="TotalAmount_'.$bankslist->id.'">0.00</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <div class="row">
                                                <div class="col-md-6  icon-groups">
                                                    <a href="#" class="btn btn-primary icon-plus add-fv-btn" id="selectbank" data-id="'.$bankslist->id.'"></a>
                                                    <input type="hidden" id="selectbankname_'.$bankslist->id.'" value="'.$bankslist->name.'" /> 
                                                </div>
                                                <div class="col-md-6 text-right account-head-number">
                                                    <span>Closing Balance</span>
                                                    <span id="ClosingBalance_'.$bankslist->id.'">'.number_format($openbalance-$totalfin,2).'</span>
                                                </div>
                                            </div>  
                                        </div>
                                    </div>
                                    <!--card end here -->
                                </div>';   
                                                
                }    
            }
        }

        $datarows.='
                    </div>
                    <div class="col-md-3" id="fin-veri-headTwo" style="display: none;">

                    </div>';

        $arr=array('error'=>'No','result'=>$datarows);
        return json_encode($arr);

                                
    } */


    public function actionFundreqdata()
    {
        $datarows = '<div id="fin-veri-headOne">';
       
        $userid = Yii::$app->user->Id; 
        $connection =  \Yii::$app->db;
        $user = User::find()->where(['id'=>$userid])->one();
           
                $accountitem = AccountsItem::find()->where(['favourite'=>1])->andWhere(['Status'=>0])->all();
                $initialdate='2013-04-01';
                $startdate=date('Y-m-d');
                 //print_r($accountitem);exit;                               
                foreach($accountitem as $bankslist)
                {
                    if($bankslist->account_type == 1){ //cash
                        $ledgerbal = LedgerOpeningbalance::find()->where(['accountid'=> $bankslist->id])->one();
                        //$paymenttotal = Voucher::find()->where(['type'=> 'Payment'])->andWhere(['place'=> '12'])->andWhere(['creditacnt'=> $bankslist->id])->andWhere(['payment'=> '1'])->andWhere(['between', 'date', $initialdate, $startdate])->sum('amount');
                        //$receipttotal = Voucher::find()->where(['type'=> 'Receipt'])->andWhere(['place'=> '12'])->andWhere(['creditacnt'=> $bankslist->id])->andWhere(['payment'=> '1'])->andWhere(['between', 'date', $initialdate, $startdate])->sum('amount');
                        /*$paymenttotal = Voucher::find()->where(['type'=> 'Payment'])->andWhere(['creditacnt'=> $bankslist->id])->andWhere(['payment'=> '1'])->andWhere(['between', 'date', $initialdate, $startdate])->sum('amount');
                        $receipttotal = Voucher::find()->where(['type'=> 'Receipt'])->andWhere(['creditacnt'=> $bankslist->id])->andWhere(['payment'=> '1'])->andWhere(['between', 'date', $initialdate, $startdate])->sum('amount');
                        if(is_null($paymenttotal)){
                            $paymenttotal = 0;
                        }
                        if(is_null($receipttotal)){
                            $receipttotal = 0;
                        }
                        $checkRbal = $paymenttotal - $receipttotal;*/

                        $credtcondtn = '';
                        $debitcondtn = '';
                        $accntcondtn = '';

                        if($bankslist->id!=0){
                            $credtcondtn.= "creditacnt='".$bankslist->id."' AND ";
                            $debitcondtn.= "debitacnt='".$bankslist->id."' AND ";
                            $accntcondtn.= "account_id='".$bankslist->id."' AND ";
                        }

                        $debittotalsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$accntcondtn." contra=0 AND date BETWEEN '".$initialdate."' AND '".$startdate."'";
                        $command=$connection->createCommand($debittotalsql);
                        $dataReader=$command->query();
                        $debittotal=$dataReader->read();

                        $credittotalsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$credtcondtn." contra=0 AND date BETWEEN '".$initialdate."' AND '".$startdate."'";
                        $command=$connection->createCommand($credittotalsql);
                        $dataReader=$command->query();
                        $credittotal=$dataReader->read();

                        $contradebitsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$accntcondtn." contra=1 AND type='Receipt' AND`date` BETWEEN '".$initialdate."' AND '".$startdate."'";
                        $command=$connection->createCommand($contradebitsql);
                        $dataReader=$command->query();
                        $contradebit=$dataReader->read();

                        $contracreditsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$credtcondtn." contra=1 AND type='Payment' AND`date` BETWEEN '".$initialdate."' AND '".$startdate."'";
                        $command=$connection->createCommand($contracreditsql);
                        $dataReader=$command->query();
                        $contracredit=$dataReader->read();

                        $journeldebitsql = "SELECT SUM(`amount`) as amount FROM `journalvoucher` WHERE ".$debitcondtn." date BETWEEN '".$initialdate."' AND '".$startdate."'";
                        $command=$connection->createCommand($journeldebitsql);
                        $dataReader=$command->query();
                        $journeldebit=$dataReader->read();

                        $journelcreditsql = "SELECT SUM(`amount`) as amount FROM `journalvoucher` WHERE ".$credtcondtn." date BETWEEN '".$initialdate."' AND '".$startdate."'";
                        $command=$connection->createCommand($journelcreditsql);
                        $dataReader=$command->query();
                        $journelcredit=$dataReader->read();

                        if(is_null($debittotal['amount'])){
                            $debittotalamt = 0;
                        }
                        else{
                            $debittotalamt = $debittotal['amount'];
                        }

                        if(is_null($credittotal['amount'])){
                            $credittotalamt = 0;
                        }
                        else{
                            $credittotalamt = $credittotal['amount'];
                        }

                        if(is_null($contradebit['amount'])){
                            $contradebitamt = 0;
                        }
                        else{
                            $contradebitamt = $contradebit['amount'];
                        }

                        if(is_null($contracredit['amount'])){
                            $contracreditamt = 0;
                        }
                        else{
                            $contracreditamt = $contracredit['amount'];
                        }

                        if(is_null($journeldebit['amount'])){
                            $journeldebitamt = 0;
                        }
                        else{
                            $journeldebitamt = $journeldebit['amount'];
                        }

                        if(is_null($journelcredit['amount'])){
                            $journelcreditamt = 0;
                        }
                        else{
                            $journelcreditamt = $journelcredit['amount'];
                        }

                        $checkRbal = ($credittotalamt + $contracreditamt + $journelcreditamt) - ($debittotalamt + $contradebitamt + $journeldebitamt);

                        if($ledgerbal)
                        {
                            $openbalance1 = $ledgerbal->balance + $checkRbal;
                            $openbalance = abs($openbalance1);
                        }else
                        {
                            $openbalance1 = $checkRbal;
                            $openbalance =  $checkRbal;
                        }

                    }else{  //bank
                        $ledger = LedgerOpeningbalance::find()->where(['accountid'=> $bankslist->id])->one();
                        //$paymenttotal = Voucher::find()->where(['type'=> 'Payment'])->andWhere(['place'=> '12'])->andWhere(['bank_id'=> $bankslist->id])->andWhere(['payment'=> '2'])->andWhere(['between', 'date', $initialdate, $startdate])->sum('amount');
                        //$receipttotal = Voucher::find()->where(['type'=> 'Receipt'])->andWhere(['place'=> '12'])->andWhere(['bank_id'=> $bankslist->id])->andWhere(['payment'=> '2'])->andWhere(['between', 'date', $initialdate, $startdate])->sum('amount');
                        /*$paymenttotal = Voucher::find()->where(['type'=> 'Payment'])->andWhere(['bank_id'=> $bankslist->id])->andWhere(['payment'=> '2'])->andWhere(['between', 'date', $initialdate, $startdate])->sum('amount');
                        $receipttotal = Voucher::find()->where(['type'=> 'Receipt'])->andWhere(['bank_id'=> $bankslist->id])->andWhere(['payment'=> '2'])->andWhere(['between', 'date', $initialdate, $startdate])->sum('amount');
                        if(is_null($paymenttotal)){
                            $paymenttotal = 0;
                        }
                        if(is_null($receipttotal)){
                            $receipttotal = 0;
                        }
                        $checkRbal = $paymenttotal - $receipttotal;*/

                        $credtcondtn = '';
                        $debitcondtn = '';
                        $accntcondtn = '';

                        if($bankslist->id!=0){
                            $credtcondtn.= "creditacnt='".$bankslist->id."' AND ";
                            $debitcondtn.= "debitacnt='".$bankslist->id."' AND ";
                            $accntcondtn.= "account_id='".$bankslist->id."' AND ";
                        }

                        $debittotalsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$accntcondtn." contra=0 AND date BETWEEN '".$initialdate."' AND '".$startdate."'";
                        $command=$connection->createCommand($debittotalsql);
                        $dataReader=$command->query();
                        $debittotal=$dataReader->read();

                        $credittotalsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$credtcondtn." contra=0 AND date BETWEEN '".$initialdate."' AND '".$startdate."'";
                        $command=$connection->createCommand($credittotalsql);
                        $dataReader=$command->query();
                        $credittotal=$dataReader->read();

                        $contradebitsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$accntcondtn." contra=1 AND type='Receipt' AND`date` BETWEEN '".$initialdate."' AND '".$startdate."'";
                        $command=$connection->createCommand($contradebitsql);
                        $dataReader=$command->query();
                        $contradebit=$dataReader->read();

                        $contracreditsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$credtcondtn." contra=1 AND type='Payment' AND`date` BETWEEN '".$initialdate."' AND '".$startdate."'";
                        $command=$connection->createCommand($contracreditsql);
                        $dataReader=$command->query();
                        $contracredit=$dataReader->read();

                        $journeldebitsql = "SELECT SUM(`amount`) as amount FROM `journalvoucher` WHERE ".$debitcondtn." date BETWEEN '".$initialdate."' AND '".$startdate."'";
                        $command=$connection->createCommand($journeldebitsql);
                        $dataReader=$command->query();
                        $journeldebit=$dataReader->read();

                        $journelcreditsql = "SELECT SUM(`amount`) as amount FROM `journalvoucher` WHERE ".$credtcondtn." date BETWEEN '".$initialdate."' AND '".$startdate."'";
                        $command=$connection->createCommand($journelcreditsql);
                        $dataReader=$command->query();
                        $journelcredit=$dataReader->read();

                        if(is_null($debittotal['amount'])){
                            $debittotalamt = 0;
                        }
                        else{
                            $debittotalamt = $debittotal['amount'];
                        }

                        if(is_null($credittotal['amount'])){
                            $credittotalamt = 0;
                        }
                        else{
                            $credittotalamt = $credittotal['amount'];
                        }

                        if(is_null($contradebit['amount'])){
                            $contradebitamt = 0;
                        }
                        else{
                            $contradebitamt = $contradebit['amount'];
                        }

                        if(is_null($contracredit['amount'])){
                            $contracreditamt = 0;
                        }
                        else{
                            $contracreditamt = $contracredit['amount'];
                        }

                        if(is_null($journeldebit['amount'])){
                            $journeldebitamt = 0;
                        }
                        else{
                            $journeldebitamt = $journeldebit['amount'];
                        }

                        if(is_null($journelcredit['amount'])){
                            $journelcreditamt = 0;
                        }
                        else{
                            $journelcreditamt = $journelcredit['amount'];
                        }

                        $checkRbal = ($credittotalamt + $contracreditamt + $journelcreditamt) - ($debittotalamt + $contradebitamt + $journeldebitamt);

                        if(is_null($ledger)){
                            $ledgerbal = 0;
                        }else{
                            $ledgerbal = $ledger->balance;
                        }
                        $openbalance1 = $ledgerbal + $checkRbal;
                        $openbalance = abs($openbalance1);
                    }
                    //$finRequest = FinanceRequests::find()->where(['Status' => 5])->andWhere(['account_id' => $bankslist->id])->andWhere(['User_Id' => $userid])->all();
                    $finRequest = FinanceRequests::find()->where(['Status' => 1])->andWhere(['account_id' => $bankslist->id])->all();
                    if(!empty($finRequest)){
                        $totalfin_apprvd = 0;
                        foreach ($finRequest as $totalfinamount){
                            
                            $totalfin_apprvd = $totalfin_apprvd + $totalfinamount->netamount;
                        }
                    }else{
                        $totalfin_apprvd = 0;
                    }

                    $finRequestchecked = FinanceRequests::find()->where(['appstatus' => 1])->andWhere(['Status' => 0])->andWhere(['account_id' => $bankslist->id])->all();
                    if(!empty($finRequestchecked)){
                        $totalfin_checked = 0;
                        foreach ($finRequestchecked as $totalfinamount){
                            
                            $totalfin_checked = $totalfin_checked + $totalfinamount->netamount;
                        }
                    }else{
                        $totalfin_checked = 0;
                    }

                    $totalfin = $totalfin_apprvd + $totalfin_checked;

                    $closing_blnce1 = $openbalance1 + $totalfin;

                    $closing_blnce = abs($closing_blnce1);

                    if($openbalance1>0):                       
                        $bnk='CR';
                        //$clos = 'DR';                     
                    else:
                        //$clos = 'CR';
                        $bnk='DR';
                    endif;

                    if($closing_blnce1>0):                       
                        $clos = 'CR';                     
                    else:
                        $clos = 'DR';
                    endif;



                     $accountselect=CashbankuserSelection::find()->where(['userid'=>$userid])->one();
                     if($accountselect && $accountselect->accountid==$bankslist['id']){

                        
                    $datarows.='
                                <div class="col-md-3">
                                    <!--card starts here -->
                                    <div class="acctheadid card active" data-value="'.$bankslist->account_type.'" data-id="'.$bankslist->id.'" id="acctheadid_'.$bankslist->id.'">
                                        <input name="ah" type="radio" />
                                        <div class="card-header">
                                            <span class="icon-library1"></span>
                                            <div class="account-head-number">
                                                <span>Accounthead</span>
                                                <span>'.$bankslist->name.'</span>
                                            </div>
                                        </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 account-head-number">
                                                <span>Opening Balance ('.$bnk.')</span>
                                                <input type="hidden" id="bankopenbalance_'.$bankslist->id.'" value="'.$openbalance.'">
                                                <input type="hidden" id="bankrealopenbalance_'.$bankslist->id.'" value="'.$openbalance1.'">
                                                <input type="hidden" id="bankrowtotal_'.$bankslist->id.'" value="'.$totalfin.'">
                                                <span>'.number_format($openbalance,2).'</span>
                                            </div>
                                            <div class="col-md-6 text-right account-head-number">
                                                <span >Approved Total</span>
                                                <span id="TotalAmount_'.$bankslist->id.'">'.number_format($totalfin,2).'</span>
                                                <input type="hidden" id="realtotalamt_'.$bankslist->id.'" value="'.$totalfin.'">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="row">
                                            <div class="col-md-6  icon-groups">
                                                <a href="#" class="btn btn-primary icon-plus add-fv-btn" id="selectbank" data-id="'.$bankslist->id.'"></a>
                                                <input type="hidden" id="selectbankname_'.$bankslist->id.'" value="'.$bankslist->name.'" /> 
                                            </div>
                                            <div class="col-md-6 text-right account-head-number">
                                                <span>Closing Balance ('.$clos.')</span>
                                                <span id="ClosingBalance_'.$bankslist->id.'">'.number_format($closing_blnce,2).'</span>
                                            </div>
                                        </div>  
                                    </div>
                                    </div>
                                    <!--card end here -->
                                </div>';
                            }else{
                                $datarows.='
                                <div class="col-md-3">
                                    <!--card starts here -->
                                    <div class="acctheadid card" data-value="'.$bankslist->account_type.'" data-id="'.$bankslist->id.'" id="acctheadid_'.$bankslist->id.'">
                                        <input name="ah" type="radio" />
                                        <div class="card-header">
                                            <span class="icon-library1"></span>
                                            <div class="account-head-number">
                                                <span>Accounthead</span>
                                                <span>'.$bankslist->name.'</span>
                                            </div>
                                        </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 account-head-number">
                                                <span>Opening Balance ('.$bnk.')</span>
                                                <input type="hidden" id="bankopenbalance_'.$bankslist->id.'" value="'.$openbalance.'">
                                                <input type="hidden" id="bankrealopenbalance_'.$bankslist->id.'" value="'.$openbalance1.'">
                                                <input type="hidden" id="bankrowtotal_'.$bankslist->id.'" value="'.$totalfin.'">
                                                <span>'.number_format($openbalance,2).'</span>
                                            </div>
                                            <div class="col-md-6 text-right account-head-number">
                                                <span >Approved Total</span>
                                                <span id="TotalAmount_'.$bankslist->id.'">'.number_format($totalfin,2).'</span>
                                                <input type="hidden" id="realtotalamt_'.$bankslist->id.'" value="'.$totalfin.'">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="row">
                                            <div class="col-md-6  icon-groups">
                                                <a href="#" class="btn btn-primary icon-plus add-fv-btn" id="selectbank" data-id="'.$bankslist->id.'"></a>
                                                <input type="hidden" id="selectbankname_'.$bankslist->id.'" value="'.$bankslist->name.'" /> 
                                            </div>
                                            <div class="col-md-6 text-right account-head-number">
                                                <span>Closing Balance ('.$clos.')</span>
                                                <span id="ClosingBalance_'.$bankslist->id.'">'.number_format($closing_blnce,2).'</span>
                                            </div>
                                        </div>  
                                    </div>
                                    </div>
                                    <!--card end here -->
                                </div>';
                            }
   
                }
                
        $datarows.='
                    </div>
                    <div class="col-md-3" id="fin-veri-headTwo" style="display: none;">

                    </div>';

        $arr=array('error'=>'No','result'=>$datarows);
        return json_encode($arr);

                                
    } 
    public function actionFundreceipt()
    {
        $datarows = '<div id="fin-receipt-headOne">';
       
        $userid = Yii::$app->user->Id; 
        $user = User::find()->where(['id'=>$userid])->one();
        $connection =  \Yii::$app->db;
           
                $accountitem = AccountsItem::find()->where(['favourite'=>1])->andWhere(['Status'=>0])->all();
                $initialdate='2013-04-01';
                $startdate=date('Y-m-d');
                 //print_r($accountitem);exit;                               
                foreach($accountitem as $bankslist)
                {
                    if($bankslist->account_type == 1){ //cash
                        $ledgerbal = LedgerOpeningbalance::find()->where(['accountid'=> $bankslist->id])->one();
                        //$paymenttotal = Voucher::find()->where(['type'=> 'Payment'])->andWhere(['place'=> '12'])->andWhere(['payment'=> '1'])->andWhere(['between', 'date', $initialdate, $startdate])->sum('amount');
                        //$receipttotal = Voucher::find()->where(['type'=> 'Receipt'])->andWhere(['place'=> '12'])->andWhere(['payment'=> '1'])->andWhere(['between', 'date', $initialdate, $startdate])->sum('amount');

                        /*$paymenttotal = Voucher::find()->where(['type'=> 'Payment'])->andWhere(['creditacnt'=> $bankslist->id])->andWhere(['payment'=> '1'])->andWhere(['between', 'date', $initialdate, $startdate])->sum('amount');
                        $receipttotal = Voucher::find()->where(['type'=> 'Receipt'])->andWhere(['creditacnt'=> $bankslist->id])->andWhere(['payment'=> '1'])->andWhere(['between', 'date', $initialdate, $startdate])->sum('amount');

                        if(is_null($paymenttotal)){
                            $paymenttotal = 0;
                        }
                        if(is_null($receipttotal)){
                            $receipttotal = 0;
                        }
                        $checkRbal = $paymenttotal - $receipttotal;*/

                        $credtcondtn = '';
                        $debitcondtn = '';
                        $accntcondtn = '';

                        if($bankslist->id!=0){
                            $credtcondtn.= "creditacnt='".$bankslist->id."' AND ";
                            $debitcondtn.= "debitacnt='".$bankslist->id."' AND ";
                            $accntcondtn.= "account_id='".$bankslist->id."' AND ";
                        }

                        $debittotalsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$accntcondtn." contra=0 AND date BETWEEN '".$initialdate."' AND '".$startdate."'";
                        $command=$connection->createCommand($debittotalsql);
                        $dataReader=$command->query();
                        $debittotal=$dataReader->read();

                        $credittotalsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$credtcondtn." contra=0 AND date BETWEEN '".$initialdate."' AND '".$startdate."'";
                        $command=$connection->createCommand($credittotalsql);
                        $dataReader=$command->query();
                        $credittotal=$dataReader->read();

                        $contradebitsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$accntcondtn." contra=1 AND type='Receipt' AND`date` BETWEEN '".$initialdate."' AND '".$startdate."'";
                        $command=$connection->createCommand($contradebitsql);
                        $dataReader=$command->query();
                        $contradebit=$dataReader->read();

                        $contracreditsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$credtcondtn." contra=1 AND type='Payment' AND`date` BETWEEN '".$initialdate."' AND '".$startdate."'";
                        $command=$connection->createCommand($contracreditsql);
                        $dataReader=$command->query();
                        $contracredit=$dataReader->read();

                        $journeldebitsql = "SELECT SUM(`amount`) as amount FROM `journalvoucher` WHERE ".$debitcondtn." date BETWEEN '".$initialdate."' AND '".$startdate."'";
                        $command=$connection->createCommand($journeldebitsql);
                        $dataReader=$command->query();
                        $journeldebit=$dataReader->read();

                        $journelcreditsql = "SELECT SUM(`amount`) as amount FROM `journalvoucher` WHERE ".$credtcondtn." date BETWEEN '".$initialdate."' AND '".$startdate."'";
                        $command=$connection->createCommand($journelcreditsql);
                        $dataReader=$command->query();
                        $journelcredit=$dataReader->read();

                        if(is_null($debittotal['amount'])){
                            $debittotalamt = 0;
                        }
                        else{
                            $debittotalamt = $debittotal['amount'];
                        }

                        if(is_null($credittotal['amount'])){
                            $credittotalamt = 0;
                        }
                        else{
                            $credittotalamt = $credittotal['amount'];
                        }

                        if(is_null($contradebit['amount'])){
                            $contradebitamt = 0;
                        }
                        else{
                            $contradebitamt = $contradebit['amount'];
                        }

                        if(is_null($contracredit['amount'])){
                            $contracreditamt = 0;
                        }
                        else{
                            $contracreditamt = $contracredit['amount'];
                        }

                        if(is_null($journeldebit['amount'])){
                            $journeldebitamt = 0;
                        }
                        else{
                            $journeldebitamt = $journeldebit['amount'];
                        }

                        if(is_null($journelcredit['amount'])){
                            $journelcreditamt = 0;
                        }
                        else{
                            $journelcreditamt = $journelcredit['amount'];
                        }

                        $checkRbal = ($credittotalamt + $contracreditamt + $journelcreditamt) - ($debittotalamt + $contradebitamt + $journeldebitamt);

                        if($ledgerbal){
                            $openbalance1 = $ledgerbal->balance + $checkRbal;
                            $openbalance = abs($openbalance1);
                        }
                        else
                        {
                            $openbalance1 = $checkRbal;
                            $openbalance =  $checkRbal;
                        }
                    }else{  //bank
                        $ledger = LedgerOpeningbalance::find()->where(['accountid'=> $bankslist->id])->one();
                        //$paymenttotal = Voucher::find()->where(['type'=> 'Payment'])->andWhere(['place'=> '12'])->andWhere(['bank_id'=> $bankslist->id])->andWhere(['payment'=> '2'])->andWhere(['between', 'date', $initialdate, $startdate])->sum('amount');
                        //$receipttotal = Voucher::find()->where(['type'=> 'Receipt'])->andWhere(['place'=> '12'])->andWhere(['bank_id'=> $bankslist->id])->andWhere(['payment'=> '2'])->andWhere(['between', 'date', $initialdate, $startdate])->sum('amount');
                        /*$paymenttotal = Voucher::find()->where(['type'=> 'Payment'])->andWhere(['bank_id'=> $bankslist->id])->andWhere(['payment'=> '2'])->andWhere(['between', 'date', $initialdate, $startdate])->sum('amount');
                        $receipttotal = Voucher::find()->where(['type'=> 'Receipt'])->andWhere(['bank_id'=> $bankslist->id])->andWhere(['payment'=> '2'])->andWhere(['between', 'date', $initialdate, $startdate])->sum('amount');
                        if(is_null($paymenttotal)){
                            $paymenttotal = 0;
                        }
                        if(is_null($receipttotal)){
                            $receipttotal = 0;
                        }
                        $checkRbal = $paymenttotal - $receipttotal;*/

                        $credtcondtn = '';
                        $debitcondtn = '';
                        $accntcondtn = '';

                        if($bankslist->id!=0){
                            $credtcondtn.= "creditacnt='".$bankslist->id."' AND ";
                            $debitcondtn.= "debitacnt='".$bankslist->id."' AND ";
                            $accntcondtn.= "account_id='".$bankslist->id."' AND ";
                        }

                        $debittotalsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$accntcondtn." contra=0 AND date BETWEEN '".$initialdate."' AND '".$startdate."'";
                        $command=$connection->createCommand($debittotalsql);
                        $dataReader=$command->query();
                        $debittotal=$dataReader->read();

                        $credittotalsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$credtcondtn." contra=0 AND date BETWEEN '".$initialdate."' AND '".$startdate."'";
                        $command=$connection->createCommand($credittotalsql);
                        $dataReader=$command->query();
                        $credittotal=$dataReader->read();

                        $contradebitsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$accntcondtn." contra=1 AND type='Receipt' AND`date` BETWEEN '".$initialdate."' AND '".$startdate."'";
                        $command=$connection->createCommand($contradebitsql);
                        $dataReader=$command->query();
                        $contradebit=$dataReader->read();

                        $contracreditsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$credtcondtn." contra=1 AND type='Payment' AND`date` BETWEEN '".$initialdate."' AND '".$startdate."'";
                        $command=$connection->createCommand($contracreditsql);
                        $dataReader=$command->query();
                        $contracredit=$dataReader->read();

                        $journeldebitsql = "SELECT SUM(`amount`) as amount FROM `journalvoucher` WHERE ".$debitcondtn." date BETWEEN '".$initialdate."' AND '".$startdate."'";
                        $command=$connection->createCommand($journeldebitsql);
                        $dataReader=$command->query();
                        $journeldebit=$dataReader->read();

                        $journelcreditsql = "SELECT SUM(`amount`) as amount FROM `journalvoucher` WHERE ".$credtcondtn." date BETWEEN '".$initialdate."' AND '".$startdate."'";
                        $command=$connection->createCommand($journelcreditsql);
                        $dataReader=$command->query();
                        $journelcredit=$dataReader->read();

                        if(is_null($debittotal['amount'])){
                            $debittotalamt = 0;
                        }
                        else{
                            $debittotalamt = $debittotal['amount'];
                        }

                        if(is_null($credittotal['amount'])){
                            $credittotalamt = 0;
                        }
                        else{
                            $credittotalamt = $credittotal['amount'];
                        }

                        if(is_null($contradebit['amount'])){
                            $contradebitamt = 0;
                        }
                        else{
                            $contradebitamt = $contradebit['amount'];
                        }

                        if(is_null($contracredit['amount'])){
                            $contracreditamt = 0;
                        }
                        else{
                            $contracreditamt = $contracredit['amount'];
                        }

                        if(is_null($journeldebit['amount'])){
                            $journeldebitamt = 0;
                        }
                        else{
                            $journeldebitamt = $journeldebit['amount'];
                        }

                        if(is_null($journelcredit['amount'])){
                            $journelcreditamt = 0;
                        }
                        else{
                            $journelcreditamt = $journelcredit['amount'];
                        }

                        $checkRbal = ($credittotalamt + $contracreditamt + $journelcreditamt) - ($debittotalamt + $contradebitamt + $journeldebitamt);

                        if(is_null($ledger)){
                            $ledgerbal = 0;
                        }else{
                            $ledgerbal = $ledger->balance;
                        }
                        $openbalance1 = $ledgerbal + $checkRbal;
                        $openbalance = abs($openbalance1);
                    }
                    //$finRequest = FinanceRequests::find()->where(['Status' => 5])->andWhere(['account_id' => $bankslist->id])->andWhere(['User_Id' => $userid])->all();
                    $finRequest = Fundreceipt::find()->where(['Status' => 1])->andWhere(['account_id' => $bankslist->id])->all();
                    if(!empty($finRequest)){
                        $totalfin = 0;
                        foreach ($finRequest as $totalfinamount){
                            
                            $totalfin = $totalfin + $totalfinamount->Amount;
                        }
                    }else{
                        $totalfin = 0;
                    }

                    $closing_blnce1 = $openbalance1 + $totalfin;

                    $closing_blnce = abs($closing_blnce1);

                    if($openbalance1>0):                       
                        $bnk='CR';
                        //$clos = 'DR';                     
                    else:
                        //$clos = 'CR';
                        $bnk='DR';
                    endif;

                    if($closing_blnce1>0):                       
                        $clos = 'CR';                     
                    else:
                        $clos = 'DR';
                    endif;

                    $accountselect=CashbankuserSelection::find()->where(['userid'=>$userid])->one();
                     if($accountselect && $accountselect->accountid==$bankslist['id']){

                    $datarows.='
                                <div class="col-md-3">
                                    <!--card starts here -->
                                    <div class="receiptacctheadid card active" data-value="'.$bankslist->account_type.'" data-id="'.$bankslist->id.'" id="receiptacctheadid_'.$bankslist->id.'">
                                        <input name="ah" type="radio" />
                                        <div class="card-header">
                                            <span class="icon-library1"></span>
                                            <div class="account-head-number">
                                                <span>Accounthead</span>
                                                <span>'.$bankslist->name.'</span>
                                            </div>
                                        </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 account-head-number">
                                                <span>Opening Balance ('.$bnk.')</span>
                                                <input type="hidden" id="bankopenbalance_'.$bankslist->id.'" value="'.$openbalance.'">
                                                <input type="hidden" id="bankrealopenbalance_'.$bankslist->id.'" value="'.$openbalance1.'">
                                                <input type="hidden" id="bankrowtotal_'.$bankslist->id.'" value="'.$totalfin.'">
                                                <span>'.number_format($openbalance,2).'</span>
                                            </div>
                                            <div class="col-md-6 text-right account-head-number">
                                                <span >Approved Total</span>
                                                <span id="recTotalAmount_'.$bankslist->id.'">'.number_format($totalfin,2).'</span>
                                                <input type="hidden" id="recrealtotalamt_'.$bankslist->id.'" value="'.$totalfin.'">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="row">
                                            <div class="col-md-6  icon-groups">
                                                <a href="#" class="btn btn-primary icon-plus add-fv-btn" id="selectbanks" data-id="'.$bankslist->id.'"></a>
                                                <input type="hidden" id="selectbankname_'.$bankslist->id.'" value="'.$bankslist->name.'" /> 
                                            </div>
                                            <div class="col-md-6 text-right account-head-number">
                                                <span>Closing Balance ('.$clos.')</span>
                                                <span id="recClosingBalance_'.$bankslist->id.'">'.number_format($closing_blnce,2).'</span>
                                            </div>
                                        </div>  
                                    </div>
                                    </div>
                                    <!--card end here -->
                                </div>';
                            }else{
                                $datarows.='
                                <div class="col-md-3">
                                    <!--card starts here -->
                                    <div class="receiptacctheadid card" data-value="'.$bankslist->account_type.'" data-id="'.$bankslist->id.'" id="receiptacctheadid_'.$bankslist->id.'">
                                        <input name="ah" type="radio" />
                                        <div class="card-header">
                                            <span class="icon-library1"></span>
                                            <div class="account-head-number">
                                                <span>Accounthead</span>
                                                <span>'.$bankslist->name.'</span>
                                            </div>
                                        </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 account-head-number">
                                                <span>Opening Balance ('.$bnk.')</span>
                                                <input type="hidden" id="bankopenbalance_'.$bankslist->id.'" value="'.$openbalance.'">
                                                <input type="hidden" id="bankrealopenbalance_'.$bankslist->id.'" value="'.$openbalance1.'">
                                                <input type="hidden" id="bankrowtotal_'.$bankslist->id.'" value="'.$totalfin.'">
                                                <span>'.number_format($openbalance,2).'</span>
                                            </div>
                                            <div class="col-md-6 text-right account-head-number">
                                                <span >Approved Total</span>
                                                <span id="TotalAmount_'.$bankslist->id.'">'.number_format($totalfin,2).'</span>
                                                <input type="hidden" id="realtotalamt_'.$bankslist->id.'" value="'.$totalfin.'">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="row">
                                            <div class="col-md-6  icon-groups">
                                                <a href="#" class="btn btn-primary icon-plus add-fv-btn" id="selectbanks" data-id="'.$bankslist->id.'"></a>
                                                <input type="hidden" id="selectbankname_'.$bankslist->id.'" value="'.$bankslist->name.'" /> 
                                            </div>
                                            <div class="col-md-6 text-right account-head-number">
                                                <span>Closing Balance ('.$clos.')</span>
                                                <span id="ClosingBalance_'.$bankslist->id.'">'.number_format($closing_blnce,2).'</span>
                                            </div>
                                        </div>  
                                    </div>
                                    </div>
                                    <!--card end here -->
                                </div>';
                            }
   
                }
                
        $datarows.='
                    </div>
                    <div class="col-md-3" id="fin-veri-headTwo" style="display: none;">

                    </div>';

        $arr=array('error'=>'No','result'=>$datarows);
        return json_encode($arr);
    }
    /*public function actionUsercashbankselect()
    {
        
        $uid= Yii::$app->user->Id;
        $account_id=$_POST['accountid'];
        $account_type=$_POST['account_type'];

        $account=CashbankuserSelection::find()->where(['userid'=>$uid])->one();
        if($account)
        {
            $account->account_typeid= $account_type;
            $account->accountid= $account_id;

            $account->save(false);
        }else{
            $model=new CashbankuserSelection();

            $model->userid= $uid;
            $model->account_typeid= $account_type;
            $model->accountid= $account_id;

            $model->save(false);

        }

        $accounts=AccountsItem::findOne($account_id);

        $datarows = '
                <h4 class="panel-title" id="fundreqradio">
                    <a data-toggle="collapse" data-parent="#accordionfin" href="#collapsecashbank">
                    <span class="icon-note1"></span>Cash/Bank Payments - '.$accounts->name.'</a>
                </h4>';

        $arr = array('error' => 'No','result'=>$datarows);
        return json_encode($arr);

    }*/
    /*public function actionUsercashbankreceiptselect()
    {
        
        $uid= Yii::$app->user->Id;
        $account_id=$_POST['accountid'];
        $account_type=$_POST['account_type'];

        $account=CashbankuserSelection::find()->where(['userid'=>$uid])->one();
        if($account)
        {
            $account->account_typeid= $account_type;
            $account->accountid= $account_id;

            $account->save(false);
        }else{
            $model=new CashbankuserSelection();

            $model->userid= $uid;
            $model->account_typeid= $account_type;
            $model->accountid= $account_id;

            $model->save(false);

        }

        $accounts=AccountsItem::findOne($account_id);

        $datarows = '
                <h4 class="panel-title" id="fundreceiptqradio">
                    <a data-toggle="collapse" data-parent="#accordionfin" href="#collapsereceipt">
                    <span class="icon-note1"></span>Cash/Bank Receipts - '.$accounts->name.'</a>
                </h4>';

        $arr = array('error' => 'No','result'=>$datarows);
        return json_encode($arr);

    }*/

    public function actionFundverdata()
    {
        $datarows = '<div id="fin-veri-headOne">';

        $userid = Yii::$app->user->Id; 
        $user = User::find()->where(['id'=>$userid])->one();
        $useraccount = UserAccounts::find()->where(['user_id'=>$userid])->orderBy(['account_id' => SORT_ASC])->one();
        $userProjects = UserProjects::find()->where(['userid'=>$userid])->all();
        if( $user->superuser == 1 || $user->superuser == 2)
        {
            foreach($userProjects as $userProject)
            {
                $accountitem = AccountsItem::find()->where(['projectid'=> $userProject->projectid])->all();
                $initialdate='2013-04-01';
                $startdate=date('Y-m-d');
                
                foreach($accountitem as $bankslist)
                {
                    if($bankslist->account_type == 1){ //cash
                        $ledgerbal = LedgerOpeningbalance::find()->where(['accountid'=> $bankslist->id])->one();
                        $paymenttotal = Voucher::find()->where(['type'=> 'Payment'])->andWhere(['place'=> '12'])->andWhere(['payment'=> '1'])->andWhere(['between', 'date', $initialdate, $startdate])->sum('amount');
                        $receipttotal = Voucher::find()->where(['type'=> 'Receipt'])->andWhere(['place'=> '12'])->andWhere(['payment'=> '1'])->andWhere(['between', 'date', $initialdate, $startdate])->sum('amount');
                        if(is_null($paymenttotal)){
                            $paymenttotal = 0;
                        }
                        if(is_null($receipttotal)){
                            $receipttotal = 0;
                        }
                        $checkRbal = $paymenttotal - $receipttotal;
                        $openbalance = $ledgerbal->balance + $checkRbal;
                    }else{  //bank
                        $ledger = LedgerOpeningbalance::find()->where(['accountid'=> $bankslist->id])->one();
                        $paymenttotal = Voucher::find()->where(['type'=> 'Payment'])->andWhere(['place'=> '12'])->andWhere(['bank_id'=> $bankslist->id])->andWhere(['payment'=> '2'])->andWhere(['between', 'date', $initialdate, $startdate])->sum('amount');
                        $receipttotal = Voucher::find()->where(['type'=> 'Receipt'])->andWhere(['place'=> '12'])->andWhere(['bank_id'=> $bankslist->id])->andWhere(['payment'=> '2'])->andWhere(['between', 'date', $initialdate, $startdate])->sum('amount');
                        if(is_null($paymenttotal)){
                            $paymenttotal = 0;
                        }
                        if(is_null($receipttotal)){
                            $receipttotal = 0;
                        }
                        $checkRbal = $paymenttotal - $receipttotal;
                        if(is_null($ledger)){
                            $ledgerbal = 0;
                        }else{
                            $ledgerbal = $ledger->balance;
                        }
                        $openbalance = $ledgerbal + $checkRbal;
                    }
                    $finRequest = FinanceRequests::find()->where(['Status' => 0])->andWhere(['account_id' => $bankslist->id])->andWhere(['User_Id' => $userid])->all();
                    if(!empty($finRequest)){
                        $totalfin = 0;
                        
                        foreach ($finRequest as $totalfinamount){
                            if($totalfinamount->netamount != null){
                            
                            $totalfin = $totalfin + $totalfinamount->netamount;  
                            }else{
                                $totalfin = 0;
                            }
                        }
        
                    }else{
                        $totalfin = 0;
                    }
  
                    $datarows.= '
                            <div class="col-md-3">
                                <!--card starts here -->
                                <div class="card selectbankapp" id="selectbankapp" data-id="'.$bankslist->id.'">
                                    <input name="ah" type="radio" />
                                    <div class="card-header">
                                        <span class="icon-library1"></span>
                                        <div class="account-head-number">
                                            <span>Accounthead</span>
                                            <span>'.$bankslist->name.'</span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 account-head-number">
                                                <span>Opening Balance</span>
                
                                                <span>'.number_format($openbalance,2).'</span>
                                            </div>
                                            <div class="col-md-6 text-right account-head-number">
                                                <span >Total Amount</span>
                                                <span>'.number_format($totalfin,2).'</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="row">
                                            <div class="col-md-6 ">
                                                <a href="#" class="btn btn-primary btn-notification selectbankapp" id="selectbankapp-'.$bankslist->id.'" data-id="'.$bankslist->id.'"><span class="icon-notifications"></span> <span id="noti-bank">'.count($finRequest).'</span></a>   
                                                <input type="hidden" id="selectbankname_'.$bankslist->id.'" value="'.$bankslist->name.'" /> 
                                            </div>
                                            <div class="col-md-6 text-right account-head-number">
                                                <span>Closing Balance</span>
                                                <span>'.number_format($openbalance-$totalfin,2).'</span>
                                            </div>
                                        </div>  
                                    </div>
                                </div>
                                <!--card end here -->
                            </div>';                                        
        
                }
                
                $bankLists = array("Axis Bank - 915020065069515"=>"169","SIB - OD - 0025081000002165"=>"631");
                foreach($bankLists as $bankname => $code) 
                {
                    
                    $ledger = LedgerOpeningbalance::find()->where(['accountid'=> $code])->one();
                    $paymenttotal = Voucher::find()->where(['type'=> 'Payment'])->andWhere(['place'=> '12'])->andWhere(['bank_id'=> $code])->andWhere(['payment'=> '2'])->andWhere(['between', 'date', $initialdate, $startdate])->sum('amount');
                    $receipttotal = Voucher::find()->where(['type'=> 'Receipt'])->andWhere(['place'=> '12'])->andWhere(['bank_id'=> $code])->andWhere(['payment'=> '2'])->andWhere(['between', 'date', $initialdate, $startdate])->sum('amount');
                    if(is_null($paymenttotal)){
                        $paymenttotal = 0;
                    }
                    if(is_null($receipttotal)){
                        $receipttotal = 0;
                    }
                    $checkRbal = $paymenttotal - $receipttotal;
                    if(is_null($ledger)){
                        $ledgerbal = 0;
                    }else{
                        $ledgerbal = $ledger->balance;
                    }
                    $openbalance = $ledgerbal + $checkRbal;
                    $finRequestt = FinanceRequests::find()->where(['Status' => 0])->andWhere(['account_id' => $code])->andWhere(['User_Id' => $userid])->all();
                    if(!empty($finRequestt)){
                        $totalfin = 0;
                        foreach ($finRequestt as $totalfinamount){
                            
                            $totalfin = $totalfin + $totalfinamount->netamount;
                        }
                    }else{
                        $totalfin = 0;
                    }
                    
                    $datarows.= '
                                <div class="col-md-3">
                                    <div class="card selectbankapp" id="selectbankapp" data-id="'.$code.'">
                                        <input name="ah" type="radio" />
                                        <div class="card-header">
                                            <span class="icon-library1"></span>
                                            <div class="account-head-number">
                                                <span>Accounthead</span>
                                                <span>'.$bankname.'</span>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6 account-head-number">
                                                    <span>Opening Balance</span>
                                                    <span>'.number_format($openbalance,2).'</span>
                                                </div>
                                                <div class="col-md-6 text-right account-head-number">
                                                    <span >Total Amount</span>
                                                    <span id="TotalAmount_'.$code.'">'.number_format($totalfin,2).'</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <div class="row">
                                                <div class="col-md-6 ">
                                                    <a href="#" class="btn btn-primary btn-notification selectbankapp" id="selectbankapp-'.$code.'" data-id="'.$code.'"><span class="icon-notifications"></span> <span id="noti-bank">'.count($finRequestt).'</span></a>    
                                                    <input type="hidden" id="selectbankname_'.$code.'" value="'.$bankname.'" />  
                                                    
                                                </div>
                                                <div class="col-md-6 text-right account-head-number">
                                                    <span>Closing Balance</span>
                                                    <span>'.number_format($openbalance-$totalfin,2).'</span>
                                                </div>
                                            </div>  
                                        </div>
                                    </div>
                                </div>';

                }  
            }
        }

        $datarows.= '
                    </div>
                    <div class="col-md-3" id="fin-veri-headTwo" style="display: none;">

                    </div>';
                                

        $arr=array('error'=>'No','result'=>$datarows);
        return json_encode($arr);

                                
    } 

    public function actionFinjornldata()
    {
        $datarows = '';

        $cashbillid = '';//$_GET['cashbill'];   issue to check
        $userid = Yii::$app->user->id;
        $user = User::find()->where(['id'=>$userid])->one();
        if($user['superuser']==1 || $user['superuser']==2)
        {
            $adminprojects = Projects::find()->where(['Project_Delete_Status' => 0])->all();
        
        }else{
            $userprojects = UserProjects::find()->select(['projectid','Name']);
            $sql="select a.projectid,b.Name from user_projects as a inner join projects as b on a.projectid=b.Project_Id where userid='$userid'";
            $command= Yii::$app->db->createCommand($sql);
            $dataReader=$command->query();
            $userprojects=$dataReader->readAll();
        }
        if($cashbillid!=''){
            //$cashbillmodel=Cashbills::model()->find(array('condition'=>'advance_id='.$cashbillid.' '));
            $cashbillmodel = Cashbills::find()->where(['advance_id' => $cashbillid])->one();
            //$cashadvancemodel=Cashadvance::model()->find(array('condition'=>'group_id='.$cashbillid.' '));
            $cashadvancemodel = Cashadvance::find()->where(['group_id' => $cashbillid])->one();
        }
        
        $datarows.= '
                <div class="add-journal-form">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Date</label>';
                            
                                if($cashbillid!=''){
                                    $datarows.= '    
                                        <input type="text" class="form-control" place="Date" name="Journal_Date" id="datepicker" value="'.date("d-m-Y",strtotime($cashbillmodel['date'])).'" />';
                                }
                                else
                                {
                                    $datarows.= '    
                                        <input type="text" class="form-control" place="Date" name="Journal_Date" id="datepicker" value="'.date("d-m-Y").'" />';
                                } 

        $datarows.= '
                            </div>
                        </div>
                        <div class="col-md-4">
                                <div class="form-group">
                                    <label>Place</label>
                                    <select class="form-control" name="place" id="jplace">
                                        <option value="0">Select Place</option>';
                                            

                                if($user['superuser']==1 || $user['superuser']==2)
                                {
                                    foreach($adminprojects AS $data)
                                    {
                                        if(!empty($cashadvancemodel)){
                                            if($data['Project_Id']==$cashadvancemodel['project_id']){
                                                $selected='selected';
                                            }else{
                                                $selected='';
                                            }
                                        }else{
                                            $selected='';
                                        }
                                        $datarows.= '<option value="'.$data['Project_Id'].'" '.$selected.' >'.$data['Name'].'</option>'; 
                                                        
                                    }
                                    }
                                    else
                                    {
                                        foreach($userprojects AS $data)
                                        {
                                            if(!empty($cashadvancemodel)){
                                                if($data['projectid']==$cashadvancemodel['project_id']){
                                                    $selected='selected';
                                                }else{
                                                    $selected='';
                                                }
                                            }else{
                                                $selected='';
                                            }

                                            $datarows.= '<option value="'.$data['Project_Id'].'" '.$selected.' >'.$data['Name'].'</option>'; 

                                        }
                                    } 

        $datarows.= '
                                    </select>
                                    <span class="error"></span>
                                </div>
                            </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Project</label>
                                    <select class="form-control" name="projectid" id="projectid">
                                        <option value="0">Select Project</option>';

                                if($user['superuser']==1 || $user['superuser']==2)
                                { 
                                    foreach($adminprojects AS $data)
                                    {
                                        if(!empty($cashadvancemodel)){
                                            if($data['Project_Id']==$cashadvancemodel['project_id']){
                                                $selected='selected';
                                            }else{
                                                $selected='';
                                            }
                                        }else{
                                            $selected='';
                                        }
                                                        
                                        $datarows.= '<option value="'.$data['Project_Id'].'" '.$selected.' >'.$data['Name'].'</option>';                        
                                    }
                                }
                                else
                                {
                                    foreach($userprojects AS $data)
                                    {
                                        if(!empty($cashadvancemodel)){
                                            if($data['projectid']==$cashadvancemodel['project_id']){
                                                $selected='selected';
                                            }else{
                                                $selected='';
                                            }
                                        }else{
                                            $selected='';
                                        }
                                        
                                        $datarows.= '<option value="'.$data['projectid'].'" '.$selected.' >'.$data['Name'].'</option>';                
                                                        
                                    }
                                }
        $datarows.= '
                                    </select>
                                    <span class="error"></span>
                                </div>
                            </div>
                            ';        

                    if($cashbillid!='')
                    {
                        $cashbills = Cashbills::find()->where(['advance_id'=>$cashbillid])->all();
                        $totalamount=0;
                        foreach($cashbills AS $cashbill)
                        {
                            $debit = Vendors::find($cashbill->vendor)->one()->account_id;
                            $credit=$cashbill->accounthead;
                            $totalamount=$totalamount + $cashbill->amount;

        $datarows.= '
                            <div class="col-md-12 add-row-wrpr" id="adddebitrow" >
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Debit Account</label>
                                                <select class="form-control debitaccount" id="debitaccount0" name="debitaccount[]" data-id="0">
                                                    <option value="0">Select Account</option>';        
                                    
                                    
                                            $acnts=AccountsItem::find()->where(['Status'=>0])->orderBy(['name'=> SORT_ASC])->all();
                                            foreach($acnts AS $accounts){
                                                if($accounts->id==$debit){
                                                    $selected='selected';
                                                }else{
                                                    $selected='';
                                                }
                                                
                                                $datarows.= "
                                                        <option value='".$accounts->id."' id='acnts' ".$selected.">".$accounts->name."</option>";
                                            }

        $datarows.='
                                                </select><span class="error"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Naration</label>
                                            <textarea class="form-control Narration" id="debitNarration0" name="Journal_Narration[]" data-id="0">'.$cashbill->purpose.'</textarea>
                                            <span class="error"></span>
                                        </div>
                                    </div>';

        $datarows.='
                                    <div class="col-md-4">
                                        <div class="row">
                                            <div class="col-md-10">
                                                <label>Amount</label>
                                                <input type="text" class="form-control debitamount" placeholder="Amount" name="debitamount[]" id="debitamount0" data-id="0" value="'.$cashbill->amount.'"place="Date" />
                                                <span class="error"></span>
                                            </div>
                                            <div class="col-md-2 icon-groups">
                                                <a class="btn btn-primary icon-add add_field_buttonn" name="addmore" id="debitaddmore" data-id="debit" title="Add more" value="Add more" href="javascript:void(0)"></a>
                                                <a class="btn btn-primary icon-remove" href="#"></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                                                    
                                            
                        }
                    }
                    else
                    {

        $datarows.='
                        <div class="col-md-12 add-row-wrpr" id="adddebitrow" >
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Debit Account</label>
                                        <select class="form-control debitaccount" id="debitaccount0" name="debitaccount[]" data-id="0">
                                            <option value="0">Select Account</option>';

                                        
                                            $acnts=AccountsItem::find()->where(['Status'=>0])->orderBy(['name'=> SORT_ASC])->all();
                                            foreach($acnts AS $accounts)
                                            {
                                                $datarows.="<option value='".$accounts->id."' id='acnts' >".$accounts->name."</option>";

                                            }
        $datarows.='
                                        </select>
                                        <span class="error"></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Naration</label>
                                        <textarea class="form-control Narration" id="debitNarration0" name="Journal_Narration[]" data-id="0"></textarea>
                                        <span class="error"></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-10">
                                            <label>Amount</label>
                                            <input type="text" class="form-control debitamount" placeholder="Amount" name="debitamount[]" id="debitamount0" data-id="0" place="Date" />
                                            <span class="error"></span>
                                        </div>
                                        <div class="col-md-2 icon-groups">
                                            <a class="btn btn-primary icon-add add_field_buttonn" name="addmore" id="debitaddmore" data-id="debit" title="Add more" value="Add more" href="javascript:void(0)"></a>
                                            <!-- <a class="btn btn-primary icon-remove" href="#"></a> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>';
                                                    

                    }
        
        $datarows.='
                    <div class="col-md-12 add-row-wrpr" id="addcreditrow">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Credit Account</label>
                                    <select class="form-control creditaccount" id="creditaccount0" name="creditaccount[]" data-id="0">
                                        <option value="0">Select Account</option>';                            
                                    
                                        $acnts=AccountsItem::find()->where(['Status'=>0])->orderBy(['name'=> SORT_ASC])->all();
                                        foreach($acnts AS $accounts){
                                            /*if($bills->party==$accounts->id):
                                                $selected="selected";
                                            else:
                                                $selected='';
                                            endif;*/
                                            if($cashbillid!=''){
                                                if($credit == $accounts->id){
                                                    $selected="selected";
                                                }else{
                                                    $selected='';
                                                }
                                            }else{
                                                $selected='';
                                            }
                                            $datarows.="
                                                    <option value='".$accounts->id."' id='acnts' ".$selected.">".$accounts->name."</option>";

                                        }

                                        if($cashbillid!='') {
                                            $totalamount1 = $totalamount;
                                        }
                                        else{
                                            $totalamount1 = '';
                                        }

        $datarows.='
                                    </select>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Naration</label>
                                    <textarea class="form-control Narration" id="creditNarration0" name="Journal_Narration[]" data-id="0"></textarea>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row">
                                    <div class="col-md-10">
                                        <label>Amount</label>
                                        <input type="text" class="form-control creditamount" placeholder="Amount" name="creditamount[]" id="creditamount0" data-id="0" value="'.$totalamount1.'" place="Date" />
                                        <span class="error"></span>
                                    </div>
                                    <div class="col-md-2 icon-groups">
                                        <a class="btn btn-primary icon-add add_field_buttonn" name="addmore" id="creditaddmore" data-id="credit" title="Add more" value="Add more" href="javascript:void(0)"></a>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>';


        $datarows.=' <div class="col-md-12 add-row-wrpr" id="addcreditrow">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                        <label>Schedule Item</label>
                                        <select class="form-control" name="shitem" id="shitem">
                                       
                                            ';


                      

                            $datarows.='</select>
                                        </span> 
                                        <br>
                                        <span class="error erorshitem" style="display:none;">please select any credit account</span>

                                </div>
                            </div>
                        </div>
                    </div>';
                
                                    
    $datarows.='    <div class="col-md-12">
                        <div class="text-center">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-danger cancel" id="JcancelB"><span class="icon-close"></span> Cancel</button>
                            <button type="button" class="btn btn-primary saveandcreate" id="saveandcreate" name="saveandcreate"><span class="icon-check"></span> Save and Create New</button>
                            <button type="button"  class="btn btn-primary savejournal" id="savejournal" name="Journal_save"><span class="icon-check"></span> Save</button>
                            
                        </div>
                    </div>
                                                                        
                    </div>
                </div>';
                                                    

        $arr=array('error'=>'No','result'=>$datarows);
        return json_encode($arr);
    }

    /*public function actionGetaddrows(){

        $aheadid = $_POST['aheadid'];
        $userid = Yii::$app->user->id;
        $user = User::find()->where(['id'=>$userid])->one();
        if (isset($aheadid) && !empty($aheadid)) {
            $finCount = FinanceRequests::find()->where(['Status' => 5])->andWhere(['User_Id' => $userid])->andWhere(['account_id' => $aheadid])->count();
            if($finCount > 0){
                $datarows = '';
                $finRequest = FinanceRequests::find()->where(['Status' => 5])->andWhere(['User_Id' => $userid])->andWhere(['account_id' => $aheadid])->all();
                $finRequestone = FinanceRequests::find()->where(['Status' => 5])->andWhere(['User_Id' => $userid])->andWhere(['account_id' => $aheadid])->one();
                    $datarows = '';
                    $datarows.='<form action="" id="fundrequestform">
                                <div class="row fund-request-form">     
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>Date</label><br/>
                                            <input class="form-control datepicker" name="editRequest_Date" id="datepicker0" value="'.$finRequestone->date.'">
                                        </div>
                                        <div class="col-md-4">
                                            <label>&nbsp</label><br/>
                                            <input type="hidden" class="form-control userrequser" id="userrequser0-'.$aheadid.'" data-id="'.$aheadid.'" name="userrequser_id" value="'.$userid.'">
                                        </div>
                                        <div class="col-md-4">
                                            <label>&nbsp</label><br/>
                                        </div>
                                    </div> 
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Project</label><br/>
                                            <select class="form-control project" name="editRequest_Project" id="project0-'.$aheadid.'" data-id="0-'.$aheadid.'">
                                                    <option value="none">Select Project</option>';
                                                    $userProjects = UserProjects::find()->where(['userid'=>$userid])->one();
                                                    if($user->superuser == 1)
                                                    {
                                                        $project = Projects::find()->where(['Status' => 0])->andWhere(['Project_Delete_Status' => 0])->all();
                                                        foreach($project AS $list){
                                                            if( $list->Project_Id == $finRequestone->project_id){
                                                                $selected="selected";
                                                            }else{
                                                                $selected="";
                                                            }
                                                            $datarows.='<option value="'.$list->Project_Id.'"'.$selected.'>'.$list->Name.'</option>';
                                                        }
                                                    }
                                                    elseif($user->superuser == 2)
                                                    {
                                                        $project = Projects::find()->where(['Status' => 0])->andWhere(['Project_Delete_Status' => 0])->all();
                                                        foreach($project AS $list){
                                                            if($list->Project_Id == $finRequestone->project_id){
                                                                $selected="selected";
                                                            }else{
                                                                $selected="";
                                                            }
                                                            $datarows.='<option value="'.$list->Project_Id.'"'.$selected.'>'.$list->Name.'</option>';
                                                        }
                                                    }
                                                    else{
                                                        if($finRequestone->$userid != ''){
                                                            //$userprojects=UserProjects::model()->findAll(
                                                                //array('condition'=>'userid =:id','params'=> array(':id' => $userid))
                                                            //);
                                                            $userprojects = UserProjects::find()->where(['userid'=>$userid])->all();
                                                            foreach($userprojects AS $projects){
                                                                $project=Projects::find()->where(['Project_Id'=> $projects->projectid])->andWhere(['Project_Delete_Status' => 0])->one();
                                                                //$project=Projects::model()->find(
                                                                // array('condition'=>'Project_Id =:id AND Project_Delete_Status=0','params'=> array(':id' => $projects['projectid']))
                                                                //);
                                                                if($projects->Project_Id == $finRequestone->project_id):
                                                                    $selected="selected";
                                                                else:
                                                                    $selected="";
                                                                endif;
                                                                $datarows.=' <option value="'.$project->Project_Id.'"'.$selected.'>'.$project->Name.'</option>';
                                                            }
                                                        }
                                                    }
                                    $datarows.='</select><span class="error"></span>
                                        </div>
                                        <div class="col-md-6">
                                            
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                                <div class="add-fr-cntnr row" >
                                <div clsss="col-md-12">
                                
                                <input type="hidden" id="fundentryuser" name="fundentryuser" value="'.$userid.'" />
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                        <th width="30">#</th>
                                        <th width="320">Purpose </th>
                                        <th width="100">Mode </th>
                                        <th width="200">Account Head </th>
                                        <th width="200">Net Amount </th>
                                    </tr>
                                </thead>
                                <tbody >';
                foreach($finRequest as $key=>$frequest){
                    $aheadid = $frequest->account_id;
                    //$datarows = '';
                    $datarows.='<tr  class="colspanned" id="newtr10-'.$aheadid.'">
                                <td class="text-center"><span class="number">'.$key.'</span></td>
                                <td><textarea class="form-control fundpurpose" name="editfundpurpose[]" placeholder="Purpose" data-id="0-'.$aheadid.'" id="fundpurpose0-'.$aheadid.'">'.$frequest->Purpose.'</textarea>
                                <span class="error"></span></td>
                                <td>
                                    <select class="form-control fundpaymode" name="editfundpaymode[]" id="fundpaymode0-'.$aheadid.'" data-id="0-'.$aheadid.'">
                                    <option value="none">Select Payment Mode</option>
                                    <option value="1" '.($frequest->payment == 1?'selected':'').'>Cash</option>
                                    <option value="2" '.($frequest->payment == 2?'selected':'').'>Bank</option>
                                    <option value="3" '.($frequest->payment == 1?'selected':'').'>Contra</option>
                                    </select>
                                    <span class="error"></span>
                                </td>
                                <td>';
                                $accountheadd = AccountsItem::find()->where(['id'=> $frequest->credit_account])->one();
                                $datarows.='
                                    <select class="form-control editcredit_account" id="editcredit_account0-'.$aheadid.'" name="editcredit_account[]" id="requser_account0-'.$aheadid.'">
                                        <option value="none">'.$accountheadd->name.'</option>';
                                    $accountheadds = AccountsItem::find()->all();
                                    foreach($accountheadds AS $list){
                                        if( $list->id == $accountheadd->id){
                                            $selected="selected";
                                        }else{
                                            $selected="";
                                        }
                                        $datarows.='<option value="'.$list->id.'"'.$selected.'>'.$list->name.'</option>';
                                    }
                                $datarows.='</select>
                                    <span class="error"></span>
                                </td>
                        
                                <td>
                                    <div class="icon-groups">
                                        <a class="btn btn-primary  icon-add add_field_button" href="#" id="add_field_button" data-id="'.$aheadid.'"></a>
                                        <a class="btn btn-primary icon-remove remove_field" id="remove_field0-'.$aheadid.'" data-id="0-'.$aheadid.'"></a>
                                        <input type="hidden" id="selectbankname_'.$aheadid.'" value="'.$accountheadd->name.'" />
                                        
                                        <input type="hidden" id="selectRecord0-'.$aheadid.'" name="editfundreqid[]" value="'.$frequest->Id.'">  
                                    </div>
                                </td>
                            </tr>
                            <tr id="newtr20-'.$aheadid.'">
                            <td class="text-center"><span></span></td>
                            <td colspan="2">
                            <div class="tds-sgst-cgst-igst-wrpr">
                                <div class="form-groups">
                                    <label>TDS</label>
                                    <input class="form-control fundtdsamount" name="editfundtdsamount[]" data-id="0-'.$aheadid.'" id="fundtdsamount0-'.$aheadid.'" value="'.$frequest->tds.'" placeholder="TDS" />
                                    <span class="error"></span>
                                </div>
                                <div class="form-groups">
                                    <label>SGST</label>
                                    <input class="form-control fundsgstamount" name="editfundsgstamount[]" data-id="0-'.$aheadid.'" id="fundsgstamount0-'.$aheadid.'" value="'.$frequest->tax.'" placeholder="SGST" />
                                    <span class="error"></span>
                                </div>
                                <div class="form-groups">
                                    <label>CGST</label>
                                    <input class="form-control fundcgstamount" readonly name="editfundcgstamount[]" data-id="0-'.$aheadid.'" id="fundcgstamount0-'.$aheadid.'" value="'.$frequest->tax.'" placeholder="CGST" />
                                </div>
                                <div class="form-groups">
                                    <label>IGST</label>
                                    <input class="form-control fundigstamount" name="editfundigstamount[]" data-id="0-'.$aheadid.'" id="fundigstamount0-'.$aheadid.'" value="'.$frequest->igst_tax.'" placeholder="IGST" />
                                    <span class="error"></span>
                                </div>
                            </div>
                        <td >
                                <div class="form-groups">
                                    <label>Amount</label>
                                    <input style="width:80px" class="purpose-amount form-control" type="text" name="editfundamount[]" data-id="0-'.$aheadid.'" id="fundamount0-'.$aheadid.'" value="'.$frequest->Amount.'" /> 
                                    <span class="error"></span>
                                </div>
                        
                        </td>
                        <td colspan="2">
                            <div class="form-groups">
                                    <label>&nbsp</label>
                                    <input type="hidden" id="fundreq_net0-'.$aheadid.'" name="editfundreq_net[]" value="'.$frequest->netamount.'" />
                                    <span id="fundreqnet0-'.$aheadid.'">'.$frequest->netamount.'</span></td>
                                </div>
                        
                        </td>
                        <tr id="fundreqsaverow123"></tr>
                                        
                    </tr>
                    </tbody>
                    
                    </table>
                </div>
                <div class="col-md-12">
                    &nbsp;
                </div>
                <div class="col-md-12 text-center">
                    <div class="form-groups">
                        <button class="btn btn-primary SaveasDraft" id="SaveasDraft'.$aheadid.'" data-id="'.$aheadid.'"><span class="icon-file3"></span> Save as Draft</button>
                        <button class="btn btn-primary btn-send-for-approval SendforApprove" id="SendforApprove'.$aheadid.'" data-id="'.$aheadid.'"><span class="icon-check"></span> Send for Approval</button>
                    </div>
                   
                    </form>
                </div>
                </div>';
                                }
            }else{

                $datarows = '';
                $datarows.='<div class="add-fr-cntnr row" >
                            <form action="" id="fundrequestform">
                            <div class="row fund-request-form">     
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>Date</label><br/>
                                        <input class="form-control datepicker" name="Request_Date" id="datepicker0" value="'.date('d-m-Y').'">
                                    </div>
                                    <div class="col-md-4">
                                        <label>&nbsp</label><br/>
                                        <input type="hidden" class="form-control userrequser" id="userrequser0-'.$aheadid.'" data-id="'.$aheadid.'" name="userrequser_id" value="'.$userid.'">
                                    </div>
                                    <div class="col-md-4">
                                        <label>&nbsp</label><br/>
                                    </div>
                                </div> 
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Project</label><br/>
                                        <select class="form-control project" name="Request_Project" id="project0-'.$aheadid.'" data-id="0-'.$aheadid.'">
                                    <option value="none">Select Project</option>';
                                    $userProjects = UserProjects::find()->where(['userid'=>$userid])->one();
                                    if($user->superuser == 1)
                                    {
                                        $project = Projects::find()->where(['Status' => 0])->andWhere(['Project_Delete_Status' => 0])->all();
                                        foreach($project AS $list){
                                            //if( $list->Project_Id == $userProjects->projectid ){
                                                //$selected="selected";
                                            //}else{
                                                //$selected="";
                                            //}
                                            $datarows.='<option value="'.$list->Project_Id.'">'.$list->Name.'</option>';
                                        }
                                    }
                                    elseif($user->superuser == 2)
                                    {
                                        $project = Projects::find()->where(['Status' => 0])->andWhere(['Project_Delete_Status' => 0])->all();
                                        foreach($project AS $list){
                                            //if($list->Project_Id == $userProjects->projectid){
                                                // $selected="selected";
                                            //}else{
                                                //$selected="";
                                            //}
                                            $datarows.='<option value="'.$list->Project_Id.'">'.$list->Name.'</option>';
                                        }
                                    }
                                    else{
                                        
                                        //$userprojects=UserProjects::model()->findAll(
                                            //array('condition'=>'userid =:id','params'=> array(':id' => $userid))
                                        //);
                                        $userprojects = UserProjects::find()->where(['userid'=>$userid])->all();
                                        foreach($userprojects AS $projects){
                                            $project=Projects::find()->where(['Project_Id'=> $projects->projectid])->andWhere(['Project_Delete_Status' => 0])->one();
                                            //if($projects['projectid']==$userproject['projectid']):
                                                //$selected="selected";
                                            //else:
                                                //$selected="";
                                            //endif;
                                            $datarows.=' <option value="'.$project->Project_Id.'">'.$project->Name.'</option>';
                                        }
                                    }
                    $datarows.='</select><span class="error"></span>
                                    </div>
                                    <div class="col-md-6">
                                        
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                            <div clsss="col-md-12">
                            <input type="hidden" id="fundentryuser" name="fundentryuser" value="'.$userid.'" />
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                    <th width="30">#</th>
                                    <th width="320">Purpose </th>
                                    <th width="100">Mode </th>
                                    <th width="200">Account Head </th>
                                    <th width="200">Net Amount </th>
                                    
                                </tr>
                            </thead>
                            <tbody >
                            <tr  class="colspanned" id="newtr10-'.$aheadid.'">
                            <td class="text-center"><span class="number">1</span></td>
                            <td><textarea class="form-control fundpurpose" name="fundpurpose[]" placeholder="Purpose" data-id="0-'.$aheadid.'" id="fundpurpose0-'.$aheadid.'"></textarea>
                            <span class="error"></span></td>
                            <td>
                                <select class="form-control fundpaymode" name="fundpaymode[]" id="fundpaymode0-'.$aheadid.'" data-id="0-'.$aheadid.'">
                                <option value="none">Select Payment Mode</option>
                                <option value="1">Cash</option>
                                <option value="2">Bank</option>
                                <option value="3">Contra</option>
                                </select>
                                <span class="error"></span>
                            </td>
                            <td>
                                <select class="form-control reqcredit_account" data-id="0-'.$aheadid.'" name="reqcredit_account[]" id="reqcredit_account0-'.$aheadid.'">
                                    <option value="none">Select AccountHead</option>';
                                    $accountheadds = AccountsItem::find()->all();
                                    foreach($accountheadds AS $list){
                                        $datarows.='<option value="'.$list->id.'">'.$list->name.'</option>';
                                    }
                                $datarows.='</select>
                                <span class="error"></span>
                            </td>
                            <td>
                                <div class="icon-groups">
                                    
                                    <a class="btn btn-primary  icon-add add_field_button" href="#" id="add_field_button" data-id="'.$aheadid.'"></a>
                                    <a class="btn btn-primary icon-remove remove_field" id="remove_field0-'.$aheadid.'" data-id="0-'.$aheadid.'"></a>
                                    <input type="hidden" id="selectbankname_'.$aheadid.'" value="'.$_POST['aheadname'].'" />
                                   
                                    <input type="hidden" id="selectRecord0-'.$aheadid.'" value="0">
                                    
                                </div>
                            </td>
                        </tr>
                        <tr id="newtr20-'.$aheadid.'">
                            <td class="text-center"><span></span></td>
                            <td colspan="2">
                            <div class="tds-sgst-cgst-igst-wrpr">
                                <div class="form-groups">
                                    <label>TDS</label>
                                    <input class="form-control fundtdsamount" name="fundtdsamount[]" data-id="0-'.$aheadid.'" id="fundtdsamount0-'.$aheadid.'" value="" placeholder="TDS" />
                                    <span class="error"></span>
                                </div>
                                <div class="form-groups">
                                    <label>SGST</label>
                                    <input class="form-control fundsgstamount" name="fundsgstamount[]" data-id="0-'.$aheadid.'" id="fundsgstamount0-'.$aheadid.'" value="" placeholder="SGST" />
                                    <span class="error"></span>
                                </div>
                                <div class="form-groups">
                                    <label>CGST</label>
                                    <input class="form-control fundcgstamount" readonly name="fundcgstamount[]" data-id="0-'.$aheadid.'" id="fundcgstamount0-'.$aheadid.'" value="0" placeholder="CGST" />
                                </div>
                                <div class="form-groups">
                                    <label>IGST</label>
                                    <input class="form-control fundigstamount" name="fundigstamount[]" data-id="0-'.$aheadid.'" id="fundigstamount0-'.$aheadid.'" value="" placeholder="IGST" />
                                    <span class="error"></span>
                                </div>
                            </div>
                            <td >
                                    <div class="form-groups">
                                        <label>Amount</label>
                                        <input class="purpose-amount form-control" type="text" name="fundamount[]" data-id="0-'.$aheadid.'" id="fundamount0-'.$aheadid.'" value="" /> 
                                        <span class="error"></span>
                                    </div>
                            
                            </td>
                            <td colspan="2">
                                <div class="form-groups">
                                        <label>&nbsp</label>
                                        <input type="hidden" id="fundreq_net0-'.$aheadid.'" name="fundreq_net[]" value="" />
                                        <input type="hidden" id="funddebtbank0-'.$aheadid.'" name="funddebtbank[]" value="'.$aheadid.'" />
                                        <span id="fundreqnet0-'.$aheadid.'"></span>
                                    </div>
                            
                            </td>
                            <tr id="fundreqsaverow123"></tr>
                            
                        </tr>
                        </tbody>
                        
                            </table>
                        </div>
                        <div class="col-md-12">
                            &nbsp;
                        </div>
                        <div class="col-md-12 text-center">
                            <div class="form-groups">
                                <button class="btn btn-primary SaveasDraft" id="SaveasDraft'.$aheadid.'" data-id="'.$aheadid.'"><span class="icon-file3"></span> Save as Draft</button>
                                <button class="btn btn-primary btn-send-for-approval SendforApprove" id="SendforApprove'.$aheadid.'"><span class="icon-check"></span> Send for Approval</button>
                            </div>
                            
                             </form>
                            </div>
                            </div>';

                       

            }
            $arr=array('error'=>'No','result'=>$datarows);
            return json_encode($arr);
        }

    } */

    public function actionGetaddrows(){

        $aheadid = $_POST['aheadid'];
        $accounttype=$_POST['acnttype'];
        $userid = Yii::$app->user->id;

        $uid= Yii::$app->user->Id;
       

        $account=CashbankuserSelection::find()->where(['userid'=>$userid])->one();
        if($account)
        {
            $account->account_typeid= $accounttype;
            $account->accountid= $aheadid;

            $account->save(false);
        }else{
            $model=new CashbankuserSelection();

            $model->userid= $userid;
            $model->account_typeid= $accounttype;
            $model->accountid= $aheadid;

            $model->save(false);

        }
        $user = User::find()->where(['id'=>$userid])->one();
        if (isset($aheadid) && !empty($aheadid)) {
            $finCount = FinanceRequests::find()->where(['Status' => 5])->andWhere(['User_Id' => $userid])->andWhere(['account_id' => $aheadid])->count();
            /*if($finCount > 0){
                $datarows = '';
                $finRequest = FinanceRequests::find()->where(['Status' => 5])->andWhere(['User_Id' => $userid])->andWhere(['account_id' => $aheadid])->all();
                $finRequestone = FinanceRequests::find()->where(['Status' => 5])->andWhere(['User_Id' => $userid])->andWhere(['account_id' => $aheadid])->one();
                    $datarows = '';
                    $datarows.='
                        <form action="" id="fundrequestform">
                            <div class="add-fr-cntnr row" >
                                <div clsss="col-md-12">
                                
                                    <input type="hidden" id="fundentryuser" name="fundentryuser" value="'.$userid.'" />
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th width="30">#</th>
                                                <th width="30">Date</th>
                                                <th width="30">Vr No</th>
                                                <th width="320">Purpose </th>
                                                <th width="200">Account Head </th>
                                                <th width="200">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody >';
                foreach($finRequest as $key=>$frequest){
                    $aheadid = $frequest->account_id;
                    //$datarows = '';
                    $datarows.='<tr  class="colspanned" id="newtr10-'.$aheadid.'">
                                <td class="text-center"><span class="number">'.$key.'</span></td>
                                <td><input class="form-control datepicker" name="editRequest_Date" id="datepicker0" value="'.$finRequestone->date.'">
                                <span class="error"></span></td>
                                <td><textarea class="form-control fundpurpose" name="editfundpurpose[]" placeholder="Purpose" data-id="0-'.$aheadid.'" id="fundpurpose0-'.$aheadid.'">'.$frequest->Purpose.'</textarea>
                                <span class="error"></span></td>
                                <td>
                                    <select class="form-control fundpaymode" name="editfundpaymode[]" id="fundpaymode0-'.$aheadid.'" data-id="0-'.$aheadid.'">
                                    <option value="none">Select Payment Mode</option>
                                    <option value="1" '.($frequest->payment == 1?'selected':'').'>Cash</option>
                                    <option value="2" '.($frequest->payment == 2?'selected':'').'>Bank</option>
                                    <option value="3" '.($frequest->payment == 1?'selected':'').'>Contra</option>
                                    </select>
                                    <span class="error"></span>
                                </td>
                                <td>';
                                $accountheadd = AccountsItem::find()->where(['id'=> $frequest->credit_account])->one();
                                $datarows.='
                                    <select class="form-control editcredit_account" id="editcredit_account0-'.$aheadid.'" name="editcredit_account[]" id="requser_account0-'.$aheadid.'">
                                        <option value="none">'.$accountheadd->name.'</option>';
                                    $accountheadds = AccountsItem::find()->all();
                                    foreach($accountheadds AS $list){
                                        if( $list->id == $accountheadd->id){
                                            $selected="selected";
                                        }else{
                                            $selected="";
                                        }
                                        $datarows.='<option value="'.$list->id.'"'.$selected.'>'.$list->name.'</option>';
                                    }
                                $datarows.='</select>
                                    <span class="error"></span>
                                </td>
                        
                                <td>
                                    <div class="icon-groups">
                                        <a class="btn btn-primary  icon-add add_field_button" href="#" id="add_field_button" data-id="'.$aheadid.'"></a>
                                        <a class="btn btn-primary icon-remove remove_field" id="remove_field0-'.$aheadid.'" data-id="0-'.$aheadid.'"></a>
                                        <input type="hidden" id="selectbankname_'.$aheadid.'" value="'.$accountheadd->name.'" />
                                        
                                        <input type="hidden" id="selectRecord0-'.$aheadid.'" name="editfundreqid[]" value="'.$frequest->Id.'">  
                                    </div>
                                </td>
                            </tr>
                            <tr id="newtr20-'.$aheadid.'">
                            <td class="text-center"><span></span></td>
                            <td colspan="2">
                            <div class="tds-sgst-cgst-igst-wrpr">
                                <div class="form-groups">
                                    <label>TDS</label>
                                    <input class="form-control fundtdsamount" name="editfundtdsamount[]" data-id="0-'.$aheadid.'" id="fundtdsamount0-'.$aheadid.'" value="'.$frequest->tds.'" placeholder="TDS" />
                                    <span class="error"></span>
                                </div>
                                <div class="form-groups">
                                    <label>SGST</label>
                                    <input class="form-control fundsgstamount" name="editfundsgstamount[]" data-id="0-'.$aheadid.'" id="fundsgstamount0-'.$aheadid.'" value="'.$frequest->tax.'" placeholder="SGST" />
                                    <span class="error"></span>
                                </div>
                                <div class="form-groups">
                                    <label>CGST</label>
                                    <input class="form-control fundcgstamount" readonly name="editfundcgstamount[]" data-id="0-'.$aheadid.'" id="fundcgstamount0-'.$aheadid.'" value="'.$frequest->tax.'" placeholder="CGST" />
                                </div>
                                <div class="form-groups">
                                    <label>IGST</label>
                                    <input class="form-control fundigstamount" name="editfundigstamount[]" data-id="0-'.$aheadid.'" id="fundigstamount0-'.$aheadid.'" value="'.$frequest->igst_tax.'" placeholder="IGST" />
                                    <span class="error"></span>
                                </div>
                            </div>
                        <td >
                                <div class="form-groups">
                                    <label>Amount</label>
                                    <input style="width:80px" class="purpose-amount form-control" type="text" name="editfundamount[]" data-id="0-'.$aheadid.'" id="fundamount0-'.$aheadid.'" value="'.$frequest->Amount.'" /> 
                                    <span class="error"></span>
                                </div>
                        
                        </td>
                        <td colspan="2">
                            <div class="form-groups">
                                    <label>&nbsp</label>
                                    <input type="hidden" id="fundreq_net0-'.$aheadid.'" name="editfundreq_net[]" value="'.$frequest->netamount.'" />
                                    <span id="fundreqnet0-'.$aheadid.'">'.$frequest->netamount.'</span></td>
                                </div>
                        
                        </td>
                        <tr id="fundreqsaverow123"></tr>
                                        
                    </tr>
                    </tbody>
                    
                    </table>
                </div>
                <div class="col-md-12">
                    &nbsp;
                </div>
                <div class="col-md-12 text-center">
                    <div class="form-groups">
                        <button class="btn btn-primary SaveasDraft" id="SaveasDraft'.$aheadid.'" data-id="'.$aheadid.'"><span class="icon-file3"></span> Save as Draft</button>
                        <button class="btn btn-primary btn-send-for-approval SendforApprove" id="SendforApprove'.$aheadid.'" data-id="'.$aheadid.'"><span class="icon-check"></span> Send for Approval</button>
                    </div>
                   
                    </form>
                </div>
                </div>';
                                }
            }
            else
            {*/
                $datarows = '';
                 $addbutton='';

                if($vouchr = FinanceRequests::find()->orderBy(['Id' => SORT_DESC])->one())
                    $vouchrno = $vouchr->Id + 1;
                else
                    $vouchrno = 1;
                
                $addbutton.= '  <div class="search-and-actions-wrpr">
                                    <div class="content-search-wrpr col-md-3 col-sm-3">
                                    <b>'.$_POST['aheadname'].'</b>
                                    </div>';

                       $datarows.='<form action="" id="fundrequestform"> 

                       <input style="width:154px;margin-left:336px;margin-top: -41px;" class="form-control datepicker" type="Date" name="Request_Date[]" data-id="0-'.$aheadid.'" id="datepicker0-'.$aheadid.'" value="'.date('Y-m-d').'">
                                                <span class="error"></span>
                                               
                       ';

                                $addbutton.='<div class="content-action-wrpr col-md-6 col-sm-6">
                                        <a href="javascript:void(0);" class="btn btn-primary addForm addcashbankvouchr" id="addcashbankvouchr" title="Add"><span class="icon-add"></span> Add</a>
                                    </div>
                                </div>';

                $datarows.='

                    <div class="add-fr-cntnr row" >
                       
                      

                </div>
                            <div clsss="col-md-12">
                                <input type="hidden" id="fundentryuser" name="fundentryuser" value="'.$userid.'" />
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="16%">Project</th>
                                            <!--<th width="10%">Vr No</th>-->
                                            <th width="30%">Purpose</th>
                                            <th width="20%">Account Head </th>
                                            <th width="13%">Amount</th>
                                            <th width="5%"></th>
                                        </tr>
                                    </thead>
                                    <tbody >
                                        <tr  class="colspanned" id="newtr10-'.$aheadid.'">
                                            <td class="text-center">
                                                <span class="number">1</span>
                                            </td>
                                            <td><select class="form-control vouchersprojects" id="vouchersprojects0-'.$aheadid.'" name="project[]" data-id="0-'.$aheadid.'">
                                            
                                                    <option value="0">Select Project</option>';
                                                    $project=Projects::find()->where(['Project_Delete_Status' => 0])->all();
                                                foreach($project AS $list){
                                                    $datarows.='<option value="'.$list->Project_Id.'">'.$list->Name.'</option>
                                                    ';
                                                }
                                            $datarows.='</select><span class="error"></span>
                                                
                                            </td>
                                            <!--<td>-->
                                                <input type="hidden" class="form-control voucher_no" data-id="0-'.$aheadid.'" name="voucher_no[]" id="voucher_no0-'.$aheadid.'" value="v'.$vouchrno.'">
                                                <span class="error"></span>
                                            <!--</td>-->
                                            <td>
                                                <textarea class="form-control fundpurpose" name="fundpurpose[]" placeholder="Purpose" data-id="0-'.$aheadid.'" id="fundpurpose0-'.$aheadid.'"></textarea>
                                                <span class="error"></span>
                                            </td>
                                            <td>
                                                <select class="form-control reqcredit_account" data-id="0-'.$aheadid.'" name="reqcredit_account[]" id="reqcredit_account0-'.$aheadid.'">
                                                    <option value="none">Select AccountHead</option>';
                                                    $accountheadds = AccountsItem::find()->where(['Status'=>0])->orderBy([
                                                        'name' => SORT_ASC])->all();
                                                    foreach($accountheadds AS $list){
                                                        $datarows.='<option value="'.$list->id.'">'.$list->name.'</option>';
                                                    }
                                                $datarows.='</select>
                                                <span class="error"></span>
                                            </td>
                                            <td>
                                                <input class="form-control fundamount" type="text" name="fundamount[]" data-id="0-'.$aheadid.'" id="fundamount0-'.$aheadid.'" value="" /> 
                                                <span class="error"></span>
                                            </td>
                                            <td>
                                                <div class="icon-groups">
                                                    
                                                    <a class="btn btn-primary  icon-add add_field_button" href="#" id="add_field_button" data-id="'.$aheadid.'" data-no="0"></a>
                                                    <a class="btn btn-primary icon-remove remove_field" id="remove_field0-'.$aheadid.'" data-id="0-'.$aheadid.'"></a>
                                                    <input type="hidden" id="selectbankname_'.$aheadid.'" value="'.$_POST['aheadname'].'" />
                                                   
                                                    <input type="hidden" id="selectRecord0-'.$aheadid.'" value="0">
                                                    <input type="hidden" id="funddebtbank0-'.$aheadid.'" name="funddebtbank[]" value="'.$aheadid.'" />
                                                    
                                                </div>
                                            </td>
                                        </tr>
                                        <tr id="fundreqsaverow123"></tr>
                                    </tbody>
                        
                                </table>
                            </div>
                            <div class="col-md-12">
                                &nbsp;
                            </div>
                            <div class="col-md-12 text-center">
                                <div class="form-groups">
                                    <button class="btn btn-danger cancel CancelVouch" id="CancelVouch"> <span class="icon-close"></span> Cancel </button>
                                    <button class="btn btn-primary SaveasVouch" id="SaveasVouch'.$aheadid.'" data-id="'.$aheadid.'"> Save </button>
                                    <input type="hidden" id="typevalue" value="">
                                </div>
                            </div>
                            
                        </form>
                            
                    </div>';

                $accountitem = AccountsItem::find()->where(['id'=> $aheadid])->andWhere(['Status'=>0])->one();

                if($accountitem->account_type==1){
                    $vouchertype = 'Cash';
                    $balancetext = 'Cash Balance';
                }
                else{
                    $vouchertype = 'Bank';
                    $balancetext = 'Bank Balance';
                }

                $Project = Projects::find()->where(['Project_Id'=> $accountitem->projectid])->one();

                /*$finaprvlrows ='
                            <div class="row fund-request-form">     
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>Project Name</label><br/>
                                            <span>'.$Project->Name.'</span>
                                        </div>
                                    </div> 
                                </div>
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>Voucher Type</label><br/>
                                            <span>'.$vouchertype.'</span>
                                        </div>
                                    </div> 
                                </div>
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>'.$balancetext.'</label><br/>
                                            <span></span>
                                        </div>
                                    </div> 
                                </div>
                            </div>';    */

             $finrequests = FinanceRequests::find()->where(['Status' => 0])->andWhere(['account_id' => $aheadid])->orderBy(['date_tmp' => SORT_ASC])->all(); 
             $apptot=0;
                $finaprvlrows ='';

                if($finrequests):

                    $finaprvlrows.='
                        <div class="add-fr-cntnr row" >
                            <form action="" id="fundrequestacceptform">
                            <div clsss="col-md-12">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="13%">Date</th>
                                            <th width="8%">Vr No</th>
                                            <th width="26%">Purpose</th>
                                            <th width="22%">Account Head </th>
                                            <th width="15%" style="text-align: right;">Amount</th>
                                            <th width="10%"></th>
                                        </tr>
                                    </thead>
                                    <tbody >';
                                    $totamnt=0;

                    foreach($finrequests as $key => $finrequest)
                    {
                        $accounthead = AccountsItem::find()->where(['id'=> $finrequest->credit_account])->one();
                        $finreqstid = $finrequest->Id;
                        $accountheadrow ='
                                <select style="display:none;" class="form-control finaccthead"  name="finaccthead" id="finaccthead-'.$finreqstid.'">
                                    <option value="none">Select AccountHead</option>';

                        $accountheadds = AccountsItem::find()->all();
                        foreach($accountheadds AS $list){
                            if($list->id==$finrequest->credit_account){
                                $selected = 'selected';
                            }
                            else{
                                $selected = '';
                            }
                            $accountheadrow.='<option value="'.$list->id.'" '.$selected.'>'.$list->name.'</option>';
                        }
                        $accountheadrow.='
                                </select>
                                <span class="error"></span>';

                        if($finrequest->hold_status!='')
                        {
                            $editbutton = 'disabled';
                            $pausebutton = 'active';
                        }
                        else{
                            $editbutton = '';
                            $pausebutton = 'innactive';
                        }

                        $currentuser = User::findOne($finrequest->reqforUser);

                        $finaprvlrows.='
                                        <tr  class="colspanned" id="funaprverow-'.$finreqstid.'">

                                            <td class="text-center">
                                                <span class="number">'.($key+1).'</span>
                                            </td>

                                            <td>
                                                <span id="findatespan'.$finreqstid.'">'.$finrequest->date.'</span>
                                                <input style="display:none;" class="form-control editfindate" type="Date" name="editfindate" id="editfindate-'.$finreqstid.'" value="'.date('Y-m-d',strtotime($finrequest->date)).'">
                                                <span class="error"></span>
                                            </td>

                                            <td>
                                                <span id="finvrnospan'.$finreqstid.'">'.$finrequest->voucher_no.'</span>
                                                <input style="display:none;" class="form-control editfinvrno" name="editfinvrno" id="editfinvrno-'.$finreqstid.'" value="'.$finrequest->voucher_no.'">
                                                <span class="error"></span>
                                            </td>

                                            <td>
                                                <span id="finpurpspan'.$finreqstid.'">'.$finrequest->Purpose.'</span>
                                                <textarea style="display:none;" class="form-control editfinpurp fundpurpose" name="editfinpurp" placeholder="Purpose" id="editfinpurp-'.$finreqstid.'">'.$finrequest->Purpose.'</textarea>
                                                <span class="error"></span>
                                            </td>

                                            <td>
                                                <span id="finacctheadspan'.$finreqstid.'">'.$accounthead->name.'</span>
                                                '.$accountheadrow.'
                                            </td>

                                            <td>
                                                <span class="finamntpan" id="finamntpan'.$finreqstid.'">'.number_format((float)$finrequest->Amount, 2).'</span>
                                                <input style="display:none;" class="form-control editfinamnt" type="text" name="editfinamnt" id="editfinamnt-'.$finreqstid.'" value="'.$finrequest->Amount.'" /> 
                                                <span class="error"></span>
                                            </td>

                                            <td>

                                                <div class="icon-groups">

                                                    <a>
                                                        <span class="tooltip-user">
                                                            <span class="icon-user3"></span>
                                                            <span class="tooltip-content">
                                                                <span class="username" style="color:#2c3e50;">'.(isset($currentuser->username) ? $currentuser->username :  '').'</span>
                                                            </span>
                                                        </span>
                                                    </a>';

                                                    $totamnt+=$finrequest->Amount;
                            $user=User::find()->where(['id'=>Yii::$app->user->id])->one();

                            if($user->role_id !=0){

                                $micro_per=UserTabs::find()->Where(['tab_id'=>11])->andWhere(['role_id'=>$user->role_id])->all();


                                $approve='display:none';
                                $reject='display:none';
                                $hold='display:none';
                                $mainapprove='display:none';

                                foreach ($micro_per as $key => $data) {
                                    
                                    if($data->micro_id==1){
                                       $approve='';
                                    }elseif($data->micro_id==2){
                                        $reject='';
                                    }elseif($data->micro_id==3){
                                        $hold='';
                                    }elseif($data->micro_id==9){
                                        $mainapprove='';
                                    }
                                }
                               
                                    $finaprvlrows.='<a class="btn btn-primary icon-pencil editfinreq" id="editfinreq'.$finreqstid.'" title="Edit" data-id="'.$finreqstid.'" href="javascript:void(0)" '.$editbutton.'></a>
                                                    <a style="display:none;" class="btn btn-primary icon-save savefinreq" id="savefinreq'.$finreqstid.'" data-id="'.$finreqstid.'" href="javascript:void(0)"></a>';

                                                   if($finrequest->appstatus==1){
                                                    $finaprvlrows.='<a style="'.$approve.'" class="btn btn-primary icon-check active ApproveMyfund apprrv" title="Approve" href="javascript:void(0)" id="ApproveMyfund-'.$finreqstid.'" data-id="'.$finreqstid.'" '.$editbutton.'></a>';
                                                }elseif($finrequest->appstatus==0){
                                                    $finaprvlrows.='<a style="'.$approve.'" class="btn btn-primary icon-check innactive ApproveMyfund nonapprrv" title="Approve" href="javascript:void(0)" id="ApproveMyfund-'.$finreqstid.'" data-id="'.$finreqstid.'" '.$editbutton.'></a>';
                                                }

                                                  $finaprvlrows.='<input type="hidden" id="ApprovefundBank-'.$finreqstid.'" data-id="'.$finrequest->account_id.'" />
                                                    <a style="'.$reject.'" class="btn btn-primary icon-close innactive RejectMyfund" title="Reject" href="javascript:void(0)" id="RejectMyfund-'.$finreqstid.'" data-id="'.$finreqstid.'" '.$editbutton.'></a>   
                                                    <a style="'.$hold.'" class="btn btn-primary icon-pause '.$pausebutton.' PauseMyfund" title="pause" href="javascript:void(0)" id="PauseMyfund-'.$finreqstid.'" data-id="'.$finreqstid.'" data-status="'.$finrequest->hold_status.'"></a>'; 

                                                    if($finrequest->appstatus==1){
                                                        $apptot=$apptot+$finrequest->Amount;
                                                        
                                        $finaprvlrows.='<input type="hidden" id="apprvestatus-'.$finreqstid.'" name="apprvestatus[]" value="1">';
                                                        }else{

                                        $finaprvlrows.='<input type="hidden" id="apprvestatus-'.$finreqstid.'" name="apprvestatus[]" value="0">';
                                    }

                                        $finaprvlrows.='<input type="hidden" id="selfinreqid-'.$finreqstid.'" name="selfinreqid[]" value="'.$finreqstid.'">
                                                    
                                                </div>

                                            </td>

                                        </tr>';
                                    

                                   
                                
                            }else{
 

                                $finaprvlrows.='<a class="btn btn-primary icon-pencil editfinreq" id="editfinreq'.$finreqstid.'" title="Edit" data-id="'.$finreqstid.'" href="javascript:void(0)" '.$editbutton.'></a>
                                <a style="display:none;" class="btn btn-primary icon-save savefinreq" id="savefinreq'.$finreqstid.'" data-id="'.$finreqstid.'" href="javascript:void(0)"></a>';
                                if($finrequest->appstatus==1){
                                    $apptot=$apptot+$finrequest->Amount;
                               $finaprvlrows.='<a class="btn btn-primary icon-check active ApproveMyfund apprrv" title="Approve" href="javascript:void(0)" id="ApproveMyfund-'.$finreqstid.'" data-id="'.$finreqstid.'" '.$editbutton.'></a>';
                           }elseif($finrequest->appstatus==0){
                            $finaprvlrows.='<a class="btn btn-primary icon-check innactive ApproveMyfund nonapprrv" title="Approve" href="javascript:void(0)" id="ApproveMyfund-'.$finreqstid.'" data-id="'.$finreqstid.'" '.$editbutton.'></a>';
                           }
                            $finaprvlrows.='<input type="hidden" id="ApprovefundBank-'.$finreqstid.'" data-id="'.$finrequest->account_id.'" />
                                <a class="btn btn-primary icon-close innactive RejectMyfund" title="Reject" href="javascript:void(0)" id="RejectMyfund-'.$finreqstid.'" data-id="'.$finreqstid.'" '.$editbutton.'></a>   
                                <a class="btn btn-primary icon-pause '.$pausebutton.' PauseMyfund" title="pause" href="javascript:void(0)" id="PauseMyfund-'.$finreqstid.'" data-id="'.$finreqstid.'" data-status="'.$finrequest->hold_status.'"></a>'; 
                                if($finrequest->appstatus==1){
                            $finaprvlrows.='<input type="hidden" id="apprvestatus-'.$finreqstid.'" name="apprvestatus[]" value="1">';
                        }else{
                            $finaprvlrows.='<input type="hidden" id="apprvestatus-'.$finreqstid.'" name="apprvestatus[]" value="0">';
                        }

                       $finaprvlrows.='<input type="hidden" id="selfinreqid-'.$finreqstid.'" name="selfinreqid[]" value="'.$finreqstid.'">
                                
                                    </div>

                                </td>

                            </tr>';
                            }
                    }
                     $user=User::find()->where(['id'=>Yii::$app->user->id])->one();

                            if($user->role_id !=0){

                                $micro_per=UserTabs::find()->Where(['tab_id'=>11])->andWhere(['role_id'=>$user->role_id])->all();


                                $approve='display:none';
                                $mainapprove='display:none;';
                                

                                foreach ($micro_per as $key => $data) {
                                    if($data->micro_id==1){
                                        $approve='';
                                    }elseif($data->micro_id==9){
                                        $mainapprove='';
                                    }
                                }

                                $apprvs = FinanceRequests::find()->where(['Status' => 0])->andWhere(['account_id' => $aheadid])->andWhere(['appstatus' => 1])->orderBy(['date_tmp' => SORT_ASC])->count();
                                if($apprvs>0){
                                    $shows ='';
                                    $appshws='display:none';

                                }else{
                                    $shows ='display:none';
                                    $appshws='';
                                }
                                


                                    $finaprvlrows.='<tr style="'.$approve.'">
                                        <td colspan="4"></td>
                                        <td><b>Total</b></td> 
                                        <td><span class="selectedreqstto" id="selectedreqsttotl-'.$aheadid.'" style="'.$shows.'">'.number_format($apptot,2).'</span>
                                        <span class="selectedappr" style="'.$appshws.'" id="apptot-'.$aheadid.'" >'.number_format((float)$totamnt, 2).'</span>
                                        </td>
                                        <td style="'.$mainapprove.'text-align: center;"><button type="button" class="btn btn-primary apprveselectedreqst">Approve</button></td>
                                    </tr>';
                                
                            }else{

                                 $apprvs = FinanceRequests::find()->where(['Status' => 0])->andWhere(['account_id' => $aheadid])->andWhere(['appstatus' => 1])->orderBy(['date_tmp' => SORT_ASC])->count();
                                if($apprvs>0){
                                    $shows ='';
                                    $appshws='display:none';
                                }else{
                                    $shows ='display:none';
                                    $appshws='';
                                }

                    $finaprvlrows.='<tr>
                                        <td colspan="4"></td>
                                        <td><b>Total</b></td> 
                                        <td><span class="selectedreqstto" id="selectedreqsttotl-'.$aheadid.'" style="'.$shows.'">'.number_format($apptot,2).'</span>
                                        <span class="selectedappr" style="'.$appshws.'" id="apptot-'.$aheadid.'">'.number_format((float)$totamnt, 2).'</span>
                                        </td>
                                        <td style="text-align: center;"><button type="button" class="btn btn-primary apprveselectedreqst">Approve</button></td>
                                    </tr>';
                                }

                    $finaprvlrows.='
                                    </tbody>
                            
                                </table>
                            </div> 
                            </form>                                                                      
                        </div>';

                endif;

            //}

            $arr=array('error'=>'No','result'=>$datarows,'addbutton'=>$addbutton,'finaprve'=>$finaprvlrows);
            return json_encode($arr);
        }

    }
    public function actionReceiptaddrows()
    {
        $aheadid = $_POST['aheadid'];
        $userid = Yii::$app->user->id;
        $account_type = $_POST['account_type'];
        $uid= Yii::$app->user->Id;
        

        $account=CashbankuserSelection::find()->where(['userid'=>$userid])->one();
        if($account)
        {
            $account->account_typeid= $account_type;
            $account->accountid= $aheadid;

            $account->save(false);
        }else{
            $model=new CashbankuserSelection();

            $model->userid= $userid;
            $model->account_typeid= $account_type;
            $model->accountid= $aheadid;

            $model->save(false);

        }

        $user = User::find()->where(['id'=>$userid])->one();
        if (isset($aheadid) && !empty($aheadid)) {
            
            $datarows = '';
            $addbutton='';
            
            if($vouchr = FinanceRequests::find()->orderBy(['Id' => SORT_DESC])->one())
                $vouchrno = $vouchr->Id + 1;
            else
                $vouchrno = 1;


            $addbutton.= '  <div class="search-and-actions-wrpr">
                                    <div class="content-search-wrpr col-md-3 col-sm-3">
                                    <b>'.$_POST['aheadname'].'</b>
                                    </div>';

            $datarows.='<form action="" id="fundreceiptform"> 

                       <input style="width:154px;margin-left:336px;margin-top: -41px;" class="form-control datepicker" type="Date" name="Request_Date[]" data-id="0-'.$aheadid.'" id="datepicker0-'.$aheadid.'" value="'.date('Y-m-d').'">
                                                <span class="error"></span>
                                               
                       ';

            $addbutton.='<div class="content-action-wrpr col-md-6 col-sm-6">
                                        <a href="javascript:void(0);" class="btn btn-primary addForm addcashbankreceipt" id="addcashbankreceipt" title="Add"><span class="icon-add"></span> Add</a>
                                    </div>
                                </div>';

            $datarows.='<div class="add-fr-cntnr row" ></div>

                        <div clsss="col-md-12">
                                <input type="hidden" id="fundentryuser" name="fundentryuser" value="'.$userid.'" />
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="16%">Project</th>
                                            <!--<th width="10%">Vr No</th>-->
                                            <th width="30%">Purpose</th>
                                            <th width="20%">Account Head </th>
                                            <th width="13%">Amount</th>
                                            <th width="5%"></th>
                                        </tr>
                                    </thead>
                                    <tbody >
                                        <tr  class="colspanned" id="newtr10-'.$aheadid.'">
                                            <td class="text-center">
                                                <span class="number">1</span>
                                            </td>
                                            <td><select class="form-control vouchersproject" data-id="0-'.$aheadid.'" id="vouchersproject0-'.$aheadid.'" name="Receipt_Project[]">
                                                    <option value="0">Select Project</option>';
                                                    $project=Projects::find()->where(['Project_Delete_Status' => 0])->all();
                                                foreach($project AS $list){
                                                    $datarows.='<option value="'.$list->Project_Id.'">'.$list->Name.'</option>';
                                                }
                                            $datarows.='</select>
                                                <span class="error"></span>
                                            </td>
                                            <!--<td>-->
                                                <input type="hidden" class="form-control voucher_no" data-id="0-'.$aheadid.'" name="voucher_no[]" id="voucher_no0-'.$aheadid.'" value="v'.$vouchrno.'">
                                                <span class="error"></span>
                                            <!--</td>-->
                                            <td>
                                                <textarea class="form-control receiptpurpose" name="receiptpurpose[]" placeholder="Purpose" style="height: 34px !important;" data-id="0-'.$aheadid.'" id="receiptpurpose0-'.$aheadid.'"></textarea>
                                                <span class="error"></span>
                                            </td>
                                            <td>
                                                <select class="form-control receiptcredit_account" data-id="0-'.$aheadid.'" name="receiptcredit_account[]" id="receiptcredit_account0-'.$aheadid.'">
                                                    <option value="none">Select AccountHead</option>';
                                                    $accountheadds = AccountsItem::find()->where(['Status'=>0])->orderBy([
                                                        'name' => SORT_ASC])->all();
                                                    foreach($accountheadds AS $list){
                                                        $datarows.='<option value="'.$list->id.'">'.$list->name.'</option>';
                                                    }
                                                $datarows.='</select>
                                                <span class="error"></span>
                                            </td>
                                            <td>
                                                <input class="form-control receiptamount" type="text" name="receiptamount[]" data-id="0-'.$aheadid.'" id="receiptamount0-'.$aheadid.'" value="" /> 
                                                <span class="error"></span>
                                            </td>
                                            <td>
                                                <div class="icon-groups">
                                                    
                                                    <a class="btn btn-primary  icon-add add_receipt_button" href="#" id="add_receipt_button" data-id="'.$aheadid.'" data-no="0"></a>
                                                    <a class="btn btn-primary icon-remove remove_field" id="remove_field0-'.$aheadid.'" data-id="0-'.$aheadid.'"></a>
                                                    <input type="hidden" id="selectbankname_'.$aheadid.'" value="'.$_POST['aheadname'].'" />
                                                   
                                                    <input type="hidden" id="selectRecord0-'.$aheadid.'" value="0">
                                                    <input type="hidden" id="funddebtbank0-'.$aheadid.'" name="funddebtbank[]" value="'.$aheadid.'" />
                                                    
                                                </div>
                                            </td>
                                        </tr>
                                        <tr id="fundrecsaverow123"></tr>
                                    </tbody>
                        
                                </table>
                            </div>
                            <div class="col-md-12">
                                &nbsp;
                            </div>
                            <div class="col-md-12 text-center">
                                <div class="form-groups">
                                    <button class="btn btn-danger cancel Cancelreceipt" id="Cancelreceipt"> <span class="icon-close"></span> Cancel </button>
                                    <button class="btn btn-primary Saveasreceipts" id="Saveasreceipts'.$aheadid.'" data-id="'.$aheadid.'"> Save </button>
                                </div>
                            </div>
                            
                        </form>
                            
                    </div>';

            $finreceipts = Fundreceipt::find()->where(['Status' => 0])->andWhere(['account_id' => $aheadid])->orderBy(['date' => SORT_ASC])->all(); 

            $finaprvlrows ='';

            if($finreceipts){

                $finaprvlrows.='
                        <div class="add-fr-cntnr row" >
                            <form action="" id="fundreceiptacceptform">
                            <div clsss="col-md-12">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="13%">Date</th>
                                            <th width="8%">Vr No</th>
                                            <th width="26%">Purpose</th>
                                            <th width="22%">Account Head </th>
                                            <th width="15%" style="text-align: right;">Amount</th>
                                            <th width="10%"></th>
                                        </tr>
                                    </thead>
                                    <tbody >';
                                    $totreceiptamnt=0;
                    foreach ($finreceipts as $key => $finreceipt) {

                        $accounthead = AccountsItem::find()->where(['id'=> $finreceipt->debitacnt])->andWhere(['Status'=>0])->one();
                         $finrecstid = $finreceipt->Id;

                         $accountheadrow ='
                                <select style="display:none;" class="form-control finacctheads"  name="finacctheads" id="finacctheads-'.$finrecstid.'">
                                    <option value="none">Select AccountHead</option>';

                        $accountheadds = AccountsItem::find()->where(['Status'=>0])->all();
                        foreach($accountheadds AS $list){
                            if($list->id==$finreceipt->debitacnt){
                                $selected = 'selected';
                            }
                            else{
                                $selected = '';
                            }
                            $accountheadrow.='<option value="'.$list->id.'" '.$selected.'>'.$list->name.'</option>';
                        }
                        $accountheadrow.='
                                </select>
                                <span class="error"></span>';

                        if($finreceipt->hold_status!='')
                        {
                            $editbutton = 'disabled';
                            $pausebutton = 'active';
                        }
                        else{
                            $editbutton = '';
                            $pausebutton = 'innactive';
                        }

                        $currentuser = User::findOne($finreceipt->User_Id);
                        
                       // $finamnt=str_replace(',','',$finreceipt->Amount);

                         $finaprvlrows.='
                                        <tr  class="colspanned" id="funrecaprverow-'.$finrecstid.'">

                                            <td class="text-center">
                                                <span class="number">'.($key+1).'</span>
                                            </td>

                                            <td>
                                                <span id="finrecdatespan'.$finrecstid.'">'.date('d-m-Y',strtotime($finreceipt->date)).'</span>
                                                <input style="display:none;" class="form-control editfinrecdate" type="Date" name="editfinrecdate" id="editfinrecdate-'.$finrecstid.'" value="'.date('Y-m-d',strtotime($finreceipt->date)).'">
                                                <span class="error"></span>
                                            </td>

                                            <td>
                                                <span id="finrecvrnospan'.$finrecstid.'">'.$finreceipt->voucher_no.'</span>
                                                <input style="display:none;" class="form-control editfinrecvrno" name="editfinrecvrno" id="editfinrecvrno-'.$finrecstid.'" value="'.$finreceipt->voucher_no.'">
                                                <span class="error"></span>
                                            </td>

                                            <td>
                                                <span id="finrecpurpspan'.$finrecstid.'">'.$finreceipt->Purpose.'</span>
                                                <textarea style="display:none;height: 34px !important;" class="form-control editfinrecpurp fundrecpurpose" name="editfinrecpurp" placeholder="Purpose" id="editfinrecpurp-'.$finrecstid.'">'.$finreceipt->Purpose.'</textarea>
                                                <span class="error"></span>
                                            </td>

                                            <td>
                                                <span id="finrecacctheadspan'.$finrecstid.'">'.$accounthead->name.'</span>
                                                '.$accountheadrow.'
                                            </td>

                                            <td style="text-align:right;">
                                                <span class="finrecamntpan" id="finrecamntpan'.$finrecstid.'">'.number_format($finreceipt->Amount, 2).'</span>
                                                <input style="display:none;" class="form-control editfinrecamnt" type="text" name="editfinrecamnt" id="editfinrecamnt-'.$finrecstid.'" value="'.$finreceipt->Amount.'" /> 
                                                <span class="error"></span>
                                            </td>

                                            <td>

                                                <div class="icon-groups">

                                                    <a>
                                                        <span class="tooltip-user">
                                                            <span class="icon-user3"></span>
                                                            <span class="tooltip-content">
                                                                <span class="username" style="color:#2c3e50;">'.$currentuser->username.'</span>
                                                            </span>
                                                        </span>
                                                    </a>';

                                                    $totreceiptamnt+=$finreceipt->Amount;

                        $finaprvlrows.='<a class="btn btn-primary icon-pencil editfinrec" id="editfinrec'.$finrecstid.'" title="Edit" data-id="'.$finrecstid.'" href="javascript:void(0)" '.$editbutton.'></a>
                        
                                                    <a style="display:none;" class="btn btn-primary icon-save savefinrec" id="savefinrec'.$finrecstid.'" data-id="'.$finrecstid.'" href="javascript:void(0)"></a>

                                                    <a class="btn btn-primary icon-check innactive ApproveMyfundrec" title="Approverec" href="javascript:void(0)" id="ApproveMyfundrec-'.$finrecstid.'" data-id="'.$finrecstid.'" '.$editbutton.'></a>


                                                    <input type="hidden" id="ApprovefundrecBank-'.$finrecstid.'" data-id="'.$finreceipt->account_id.'" />

                                                    <a class="btn btn-primary icon-close innactive RejectMyfundrec" title="Reject" href="javascript:void(0)" id="RejectMyfundrec-'.$finrecstid.'" data-id="'.$finrecstid.'" '.$editbutton.'></a>   
                                                    <a class="btn btn-primary icon-pause '.$pausebutton.' PauseMyfundrec" title="pause" href="javascript:void(0)" id="PauseMyfundrec-'.$finrecstid.'" data-id="'.$finrecstid.'" data-status="'.$finreceipt->hold_status.'"></a> 

                                                    <input type="hidden" id="apprvestatusrec-'.$finrecstid.'" name="apprvestatusrec[]" value="0">

                                                    <input type="hidden" id="selfinreqidrec-'.$finrecstid.'" name="selfinreqidrec[]" value="'.$finrecstid.'">
                                                    
                                                </div>

                                            </td>

                                        </tr>';                        
                        


                                       
                    }
                    $finaprvlrows.='<tr>
                                        <td colspan="4"></td>
                                        <td><b>Total</b></td> 
                                        <td><span id="fndselectedreqsttotl-'.$aheadid.'" style="float:right;">'.number_format((float)$totreceiptamnt, 2).'</span></td>
                                        <td style="text-align: center;"><button type="button" class="btn btn-primary apprveselectedrec">Approve</button></td>
                                    </tr>';   
                    $finaprvlrows.='</tbody></table></form>';             
            }  



            
            $arr=array('error'=>'No','result'=>$datarows,'addbutton'=>$addbutton,'finaprve'=>$finaprvlrows);
            return json_encode($arr);                                              

            

        }
    } 
     public function actionUpdatefundreceipt()
    {
        $finreceipt = Fundreceipt::find()->where(['Id' => $_POST['id']])->one();
        $finreceipt->Amount = $_POST['finamnt'];
        $finreceipt->Purpose= $_POST['finpurp'];
        $finreceipt->Requested_On = date('Y-m-d',strtotime($_POST['findate']));
        $finreceipt->date = date('d-m-Y',strtotime($_POST['findate']));
        $finreceipt->debitacnt = $_POST['finaccthead'];
        $finreceipt->voucher_no = $_POST['finvrno'];
        $finreceipt->save(false);

        $accountitemss = AccountsItem::find()->where(['id'=> $_POST['finaccthead']])->one();

        $arr=array('error'=>'No','accheadname'=>$accountitemss->name,'date'=>date('d-m-Y',strtotime($_POST['findate'])));
        return json_encode($arr); 
    }
     public function actionApprovemyfundreceipt(){

        //echo 'hai'; exit;

        if(isset($_POST['selfinreqidrec'])):

            $bankid = '';

            for($i=0;$i<count($_POST['selfinreqidrec']);$i++)
            {
                $checkid = Fundreceipt::find()->where(['Id' => $_POST['selfinreqidrec'][$i]])->one();
                $bankid = $checkid->account_id;
                if($_POST['apprvestatusrec'][$i]==1){
                    //print_r($_POST['deleid']); exit;
                    if(!empty($checkid)){
                        $checkid->Status = $_POST['apprvestatusrec'][$i];
                        $checkid->Approved_By = Yii::$app->user->id;
                        $checkid->Approved_On = date('y-m-d h:i:s');
                        $checkid->save(false);
                    }

                }
            }

            $arr = array('error' => 'No','bankid'=>$bankid);
            return json_encode($arr); 

        endif;

    
    }
     public function actionDeletereceipt()
    {
        $id=$_POST['finreqID'];
        $connection =  \Yii::$app->db;
        if(isset($_POST['finreqID'])):
            $sql="DELETE FROM fundreceipt WHERE Id='$id'";
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $arr = array('Id' => $_POST['finreqID'],'error'=>'No');
            return json_encode($arr);
        endif;
    }
    public function actionHoldmyfundreceipt(){

        if($_POST['finrecID']){

            $checkid = Fundreceipt::find()->where(['Id' => $_POST['finrecID']])->one();
            //print_r($_POST['deleid']); exit;
            if(!empty($checkid)){
                $checkid->hold_status = $_POST['status'];
                $checkid->save(false);
                $arr = array('error' => 'Yes');
                return json_encode($arr); 
            }else{
                $arr = array('error' => 'No');
                return json_encode($arr); 
            }
            
        }
    
    }
    public function actionCashbankreceiptrow()
    {
        $aheadid = $_POST['id'];
        $row = $_POST['rowno'];


        if($vouchr = FinanceRequests::find()->orderBy(['Id' => SORT_DESC])->one())
            $vouchrno = $vouchr->Id + $row;
        else
            $vouchrno = 1;

        
        
        $datarows ='
            <tr  class="colspanned" id="newtr1'.$row.'-'.$aheadid.'">
                <td class="text-center">
                    <span class="number">'.$row.'</span>
                </td>
                <td>
                <!--<input style="display:none;" class="form-control datepicker" type="Date" name="Request_Date[]" data-id="'.$row.'-'.$aheadid.'" id="datepicker'.$row.'-'.$aheadid.'" value="'.date('Y-m-d').'">
                    <span class="error"></span>-->
                <select class="form-control vouchersproject" data-id="'.$row.'-'.$aheadid.'" id="vouchersproject'.$row.'-'.$aheadid.'" name="Receipt_Project[]">
                    <option value="0">Select Project</option>';
                        $project=Projects::find()->where(['Project_Delete_Status' => 0])->all();
                        foreach($project AS $list){
                $datarows.='<option value="'.$list->Project_Id.'">'.$list->Name.'</option>';
                    }
                $datarows.='</select><span class="error"></span></td>
                <!--<td>-->
                    <input type="hidden" class="form-control voucher_no" name="voucher_no[]" data-id="'.$row.'-'.$aheadid.'" id="voucher_no'.$row.'-'.$aheadid.'" value="v'.$vouchrno.'">
                    <span class="error"></span>
                <!--</td>-->
                <td>
                    <textarea style="height: 34px !important;" class="form-control receiptpurpose" name="receiptpurpose[]" placeholder="Purpose" data-id="'.$row.'-'.$aheadid.'" id="receiptpurpose'.$row.'-'.$aheadid.'"></textarea>
                    <span class="error"></span>
                </td>
                <td>
                    <select class="form-control receiptcredit_account" data-id="'.$row.'-'.$aheadid.'" name="receiptcredit_account[]" id="receiptcredit_account'.$row.'-'.$aheadid.'">
                        <option value="none">Select AccountHead</option>';
                        $accountheadds = AccountsItem::find()->where(['Status'=>0])->all();
                        foreach($accountheadds AS $list){
                            $datarows.='<option value="'.$list->id.'">'.$list->name.'</option>';
                        }
                    $datarows.='</select>
                    <span class="error"></span>
                </td>
                <td>
                    <input class="form-control receiptamount" type="text" name="receiptamount[]" data-id="'.$row.'-'.$aheadid.'" id="receiptamount'.$row.'-'.$aheadid.'" value="" /> 
                    <span class="error"></span>
                </td>
                <td>
                    <div class="icon-groups">
                        
                        <a class="btn btn-primary  icon-add add_receipt_button" href="#" id="add_receipt_button" data-id="'.$aheadid.'" data-no="'.$row.'"></a>
                        <a class="btn btn-primary icon-remove remove_field" id="remove_field'.$row.'-'.$aheadid.'" data-id="'.$row.'-'.$aheadid.'"></a>
                        <input type="hidden" id="selectbankname_'.$aheadid.'" value="'.$_POST['aheadname'].'" />
                       
                        <input type="hidden" id="selectRecord'.$row.'-'.$aheadid.'" value="0">

                        <input type="hidden" id="funddebtbank'.$row.'-'.$aheadid.'" name="funddebtbank[]" value="'.$aheadid.'" />
                        
                    </div>
                </td>
            </tr>';

        $arr=array('error'=>'No','result'=>$datarows);
        return json_encode($arr);
    }
    public function actionSavefundreceipt()
    {
        

        $userid=Yii::$app->user->Id;
        //print_r($_POST['receiptamount']);exit;
        if(isset($_POST['receiptamount']))
        {
             for($i=0;$i<count($_POST['receiptamount']);$i++)
                {
                     $accountitem = AccountsItem::find()->where(['id'=> $_POST['funddebtbank'][$i]])->one();
                     $model=new Fundreceipt();

                    $model->User_Id=$userid;
                    $model->Amount=$_POST['receiptamount'][$i];
                    $model->Purpose=$_POST['receiptpurpose'][$i];
                    $model->debitacnt=$_POST['receiptcredit_account'][$i];
                    //$model->Requested_On=date('y-m-d h:i:s');
                    $model->Requested_On=date('y-m-d h:i:s',strtotime($_POST['Request_Date'][0]));
                    $model->project_id=$_POST['Receipt_Project'][$i];
                    //$model->date=date('Y-m-d',strtotime($_POST['Request_Date'][$i]));
                    $model->date=date('Y-m-d',strtotime($_POST['Request_Date'][0]));
                    $model->place=$accountitem->projectid;
                    $model->payment= $accountitem->account_type;
                    $model->account_id=$_POST['funddebtbank'][$i];
                    $model->voucher_no=$_POST['voucher_no'][$i];
                    $model->save(false);

                    $notifications=New Notifications();
                    $notifications->user_id=1;
                    $notifications->assigned_user=Yii::$app->user->id;
                    $notifications->itemid=$model->Id;
                    $notifications->type="Fund Receipt";
                    $notifications->save(false);
                    
                }

                    $arr=array('result'=>'Yes');
                    return json_encode($arr);

        }
        

    }

    public function actionCashbankvoucherrow()
    {

        $aheadid = $_POST['id'];
        $row = $_POST['rowno'];
        
        if($vouchr = FinanceRequests::find()->orderBy(['Id' => SORT_DESC])->one())
            $vouchrno = $vouchr->Id + $row;
        else
            $vouchrno = 1;
        

        $datarows ='
            <tr  class="colspanned" id="newtr1'.$row.'-'.$aheadid.'">
                <td class="text-center">
                    <span class="number">'.$row.'</span>
                </td>
                <td>
                <!--<input style="display:none;" class="form-control datepicker" type="Date" name="Request_Date[]" data-id="'.$row.'-'.$aheadid.'" id="datepicker'.$row.'-'.$aheadid.'" value="'.date('Y-m-d').'">
                    <span class="error"></span>-->
                <select class="form-control vouchersprojects" data-id="'.$row.'-'.$aheadid.'" id="vouchersprojects'.$row.'-'.$aheadid.'" name="project[]">
                    <option value="0">Select Project</option>';
                        $project=Projects::find()->where(['Project_Delete_Status' => 0])->all();
                        foreach($project AS $list){
                $datarows.='<option value="'.$list->Project_Id.'">'.$list->Name.'</option>';
                    }
                $datarows.='</select><span class="error"></span></td>
                <!--<td>-->
                    <input type="hidden" class="form-control voucher_no" name="voucher_no[]" data-id="'.$row.'-'.$aheadid.'" id="voucher_no'.$row.'-'.$aheadid.'" value="v'.$vouchrno.'">
                    <span class="error"></span>
                <!--</td>-->
                <td>
                    <textarea class="form-control fundpurpose" name="fundpurpose[]" placeholder="Purpose" data-id="'.$row.'-'.$aheadid.'" id="fundpurpose'.$row.'-'.$aheadid.'"></textarea>
                    <span class="error"></span>
                </td>
                <td>
                    <select class="form-control reqcredit_account" data-id="'.$row.'-'.$aheadid.'" name="reqcredit_account[]" id="reqcredit_account'.$row.'-'.$aheadid.'">
                        <option value="none">Select AccountHead</option>';
                        //$accountheadds = AccountsItem::find()->all();
                        $accountheadds = AccountsItem::find()->where(['Status'=>0])->orderBy([
                                                        'name' => SORT_ASC])->all();
                        foreach($accountheadds AS $list){
                            $datarows.='<option value="'.$list->id.'">'.$list->name.'</option>';
                        }
                    $datarows.='</select>
                    <span class="error"></span>
                </td>
                <td>
                    <input class="form-control fundamount" type="text" name="fundamount[]" data-id="'.$row.'-'.$aheadid.'" id="fundamount'.$row.'-'.$aheadid.'" value="" /> 
                    <span class="error"></span>
                </td>
                <td>
                    <div class="icon-groups">
                        
                        <a class="btn btn-primary  icon-add add_field_button" href="#" id="add_field_button" data-id="'.$aheadid.'" data-no="'.$row.'"></a>
                        <a class="btn btn-primary icon-remove remove_field" id="remove_field'.$row.'-'.$aheadid.'" data-id="'.$row.'-'.$aheadid.'"></a>
                        <input type="hidden" id="selectbankname_'.$aheadid.'" value="'.$_POST['aheadname'].'" />
                       
                        <input type="hidden" id="selectRecord'.$row.'-'.$aheadid.'" value="0">

                        <input type="hidden" id="funddebtbank'.$row.'-'.$aheadid.'" name="funddebtbank[]" value="'.$aheadid.'" />
                        
                    </div>
                </td>
            </tr>';

        $arr=array('error'=>'No','result'=>$datarows);
        return json_encode($arr);
    }
    
    // list projects -users names 
    public function actionGetprojectslists(){

        if($_POST['selectedProjectlist']){
            $userid = Yii::$app->user->id;
            $user = User::find()->where(['id'=>$userid])->one();
            $dataone = '<option value="">Select Projects</option>';
            $userProjects = UserProjects::find()->where(['userid'=> $userid])->one();
            if($user->superuser == 1)
            {
                $project = Projects::find()->where(['Status' => 0])->andWhere(['Project_Delete_Status' => 0])->all();
                foreach($project AS $list){
                    //if( $list->Project_Id == $userProjects->projectid ){
                        //$selected="selected";
                    //}else{
                        //$selected="";
                    //}
                    $dataone.='<option value="'.$list->Project_Id.'">'.$list->Name.'</option>';
                }
            }
            elseif($user->superuser == 2)
            {
                $project = Projects::find()->where(['Status' => 0])->andWhere(['Project_Delete_Status' => 0])->all();
                foreach($project AS $list){
                    //if($list->Project_Id == $userProjects->projectid){
                        // $selected="selected";
                    //}else{
                        //$selected="";
                    //}
                    $dataone.='<option value="'.$list->Project_Id.'">'.$list->Name.'</option>';
                }
            }
            else{
                //$userprojects=UserProjects::model()->findAll(
                    //array('condition'=>'userid =:id','params'=> array(':id' => $userid))
                //);
                $userprojects = UserProjects::find()->where(['userid'=>$userid])->all();
                foreach($userprojects AS $projects){
                    $project=Projects::find()->where(['Project_Id'=> $projects->projectid])->andWhere(['Project_Delete_Status' => 0])->one();
                    //if($projects['projectid']==$userproject['projectid']):
                        //$selected="selected";
                    //else:
                        //$selected="";
                    //endif;
                    $dataone.=' <option value="'.$project->Project_Id.'">'.$project->Name.'</option>';
                }
            }
            $dataone.='</select><span class="error"></span>';
            $arr=array('error'=>'No','result'=>$dataone);
            return json_encode($arr); 
        }
    
    }

    public function actionGetprojectsnames(){

        if($_POST['selectedProject']){
            $dataone = '';
            $dataone.= '<option value="">Select User</option>';
            $userPro = UserProjects::find()->where(['projectid' => $_POST['selectedProject']])->all();
            foreach($userPro as $userList){
                $userNamess = User::find()->where(['id' => $userList->userid])->one();

                $dataone.='<option value="'.$userList->userid.'">'.$userNamess->username.'</option>';
            }
            $arr=array('error'=>'No','result'=>$dataone);
            return json_encode($arr); 
        }
    
    }

    public function actionGetdebtsaccounts(){

        if($_POST['selecteddebtsaccounts']){
            $dataones = '';
            $dataones.= '<option value="none">Select AccountHead</option>';
            $accountheadds = AccountsItem::find()->all();
            foreach($accountheadds AS $list){
                $dataones.='<option value="'.$list->id.'">'.$list->name.'</option>';
            }
            $arr=array('error'=>'No','results'=>$dataones);
            return json_encode($arr); 
        }
    
    }

    // save as draft

    /*public function actionSavefundrequest()
    {
        if(isset($_POST['fundamount'])):
            $groupid=time();
            for($i=0;$i<count($_POST['fundamount']);$i++)
            {
                $newmodel=new FinanceRequests();
                $newmodel->User_Id = $_POST['fundentryuser'];
                $newmodel->Amount = $_POST['fundamount'][$i];
                $newmodel->Purpose= $_POST['fundpurpose'][$i];
                $newmodel->Requested_On = date('Y-m-d',strtotime($_POST['Request_Date']));
                $newmodel->project_id = $_POST['Request_Project'];
                $newmodel->group_id = $groupid;
                $newmodel->date = $_POST['Request_Date'];
                $newmodel->date_tmp = date('Y-m-d',strtotime($_POST['Request_Date']));
                $newmodel->place = $_POST['Request_Project'];
                $newmodel->credit_account = $_POST['reqcredit_account'][$i];
                $newmodel->account_id = $_POST['funddebtbank'][$i];
                //$newmodel->payment_type=$_POST['fundpaytype'][$i];
                //$newmodel->payment_type = 0;
                $newmodel->Status=$_POST['status'];
                $newmodel->tax=$_POST['fundcgstamount'][$i];
                if($_POST['fundigstamount'][$i]!=''){
                    $newmodel->igst_tax=$_POST['fundigstamount'][$i];
                }
                else{
                    $newmodel->igst_tax=0;
                }
                if($_POST['fundcgstamount'][$i]!=0):
                    $newmodel->alloted_amount=$_POST['fundamount'][$i] + ($_POST['fundcgstamount'][$i]*2);
                    $newmodel->netamount=$_POST['fundamount'][$i] + ($_POST['fundcgstamount'][$i]*2);
                else:
                    if($_POST['fundigstamount'][$i]!=''){
                        $newmodel->alloted_amount=$_POST['fundamount'][$i] + $_POST['fundigstamount'][$i];
                        $newmodel->netamount=$_POST['fundamount'][$i] + $_POST['fundigstamount'][$i];
                    }
                    else{
                        $newmodel->alloted_amount=$_POST['fundamount'][$i];
                        $newmodel->netamount=$_POST['fundamount'][$i];
                    }
                endif;
                if($_POST['fundpaymode'][$i]==3):
                    $newmodel->Contra=1;
                else:
                    $newmodel->payment=$_POST['fundpaymode'][$i];
                endif;
                //$newmodel->purchase_advance=$_POST['fundpurchaseadv'][$i];
                $newmodel->purchase_advance = 0;
                $newmodel->reqforUser = $_POST['userrequser_id'];
                //$newmodel->isNewRecord = true;
                $newmodel->save(false);
            }
            //$notifications= new Notifications();
            //$notifications->user_id = 1;
            //$notifications->assigned_user = Yii::$app->user->id;
            //$notifications->itemid = $groupid;
            //$notifications->type = "Fund Request";
            //$notifications->save(false);
            $arr=array('result'=>'Yes');

        endif;
        if(isset($_POST['editfundamount'])):
            //print_r($_POST['editfundamount']);exit;
            $groupid=time();
            for($i=0;$i<count($_POST['editfundamount']);$i++)
            {
                $fundmodel = FinanceRequests::find()->where(['id' => ($_POST['editfundreqid'])])->one();
                $fundmodel->Amount =$_POST['editfundamount'][$i];
                $fundmodel->Purpose=$_POST['editfundpurpose'][$i];
                $fundmodel->Requested_On=date('Y-m-d',strtotime($_POST['editRequest_Date']));
                $fundmodel->project_id=$_POST['editRequest_Project'];
                $fundmodel->group_id=$groupid;
                $fundmodel->date=$_POST['editRequest_Date'][$i];
                $fundmodel->date_tmp=date('Y-m-d',strtotime($_POST['editRequest_Date']));
                $fundmodel->place=$_POST['editRequest_Project'];
                $fundmodel->credit_account=$_POST['editcredit_account'][$i];
                //$newmodel->account_id = $_POST['editfunddebtbank'][$i];
                $fundmodel->Status=$_POST['status'];
                $fundmodel->tax=$_POST['editfundcgstamount'][$i];
                $fundmodel->igst_tax=$_POST['editfundigstamount'][$i];
                if($_POST['editfundcgstamount'][$i]!=0):
                    $fundmodel->alloted_amount=$_POST['editfundamount'][$i] + ($_POST['editfundcgstamount'][$i]*2);
                    $fundmodel->netamount=$_POST['editfundamount'][$i] + ($_POST['editfundcgstamount'][$i]*2);
                else:
                    $fundmodel->alloted_amount=$_POST['editfundamount'][$i] + $_POST['editfundigstamount'][$i];
                    $fundmodel->netamount=$_POST['editfundamount'][$i] + $_POST['editfundigstamount'][$i];
                endif;
                if($_POST['editfundpaymode'][$i]==3):
                    $fundmodel->Contra=1;
                else:
                    $fundmodel->payment=$_POST['editfundpaymode'][$i];
                endif;
                $fundmodel->reqforUser = $_POST['userrequser_id'];
                $fundmodel->update(false);
            }
            $arr=array('result'=>'Yes');
        endif;
        return json_encode($arr); 
    }*/
   
    public function actionSavefundrequest()
    {
        //echo $_POST['Request_Date'][0]; exit;
        if(isset($_POST['fundamount'])):
            $groupid=time();
            for($i=0;$i<count($_POST['fundamount']);$i++)
            {
                $userProject = UserProjects::find()->where(['userid'=> $_POST['fundentryuser']])->one();

                $accountitem = AccountsItem::find()->where(['id'=> $_POST['funddebtbank'][$i]])->one();

                $newmodel=new FinanceRequests();
                $newmodel->User_Id = $_POST['fundentryuser'];
                $newmodel->Amount = $_POST['fundamount'][$i];
                $newmodel->Purpose= $_POST['fundpurpose'][$i];
                //$newmodel->Requested_On = date('Y-m-d',strtotime($_POST['Request_Date'][$i]));
                $newmodel->Requested_On = date('Y-m-d',strtotime($_POST['Request_Date'][0]));
                $newmodel->project_id =$_POST['project'][$i];
                //$newmodel->project_id = $accountitem->projectid;
                $newmodel->group_id = $groupid;
                //$newmodel->date = date('d-m-Y',strtotime($_POST['Request_Date'][$i]));
                //$newmodel->date_tmp = date('Y-m-d',strtotime($_POST['Request_Date'][$i]));
                $newmodel->date = date('d-m-Y',strtotime($_POST['Request_Date'][0]));
                $newmodel->date_tmp = date('Y-m-d',strtotime($_POST['Request_Date'][0]));
                $newmodel->place = $accountitem->projectid;
                $newmodel->credit_account = $_POST['reqcredit_account'][$i];
                $newmodel->account_id = $_POST['funddebtbank'][$i];
                //$newmodel->payment_type=$_POST['fundpaytype'][$i];
                //$newmodel->payment_type = 0;
                $newmodel->Status=0;
                $newmodel->tax=0;
                $newmodel->igst_tax=0;


                $newmodel->alloted_amount=$_POST['fundamount'][$i];
                $newmodel->netamount=$_POST['fundamount'][$i];

                $newmodel->Contra=0;
                $newmodel->payment= $accountitem->account_type;
                //$newmodel->purchase_advance=$_POST['fundpurchaseadv'][$i];
                $newmodel->purchase_advance = 0;
                $newmodel->reqforUser = $_POST['fundentryuser'];
                $newmodel->voucher_no = $_POST['voucher_no'][$i];
                //$newmodel->isNewRecord = true;
                $newmodel->save(false);
            }

            $arr=array('result'=>'Yes');

        endif;
        return json_encode($arr); 
    }

    public function actionUpdatefundrequest()
    {
        $finreqst = FinanceRequests::find()->where(['Id' => $_POST['id']])->one();
        $groupid=time();
        $finreqst->Amount = $_POST['finamnt'];
        $finreqst->Purpose= $_POST['finpurp'];
        $finreqst->Requested_On = date('Y-m-d',strtotime($_POST['findate']));
        $finreqst->group_id = $groupid;
        $finreqst->date = date('d-m-Y',strtotime($_POST['findate']));
        $finreqst->date_tmp = date('Y-m-d',strtotime($_POST['findate']));
        $finreqst->credit_account = $_POST['finaccthead'];

        $finreqst->alloted_amount=$_POST['finamnt'];
        $finreqst->netamount=$_POST['finamnt'];
        $finreqst->voucher_no = $_POST['finvrno'];
        //$newmodel->isNewRecord = true;
        $finreqst->save(false);

        $accountitemss = AccountsItem::find()->where(['id'=> $_POST['finaccthead']])->one();

        $arr=array('error'=>'No','accountid'=> $finreqst->account_id,'accheadname'=>$accountitemss->name,'date'=>date('d-m-Y',strtotime($_POST['findate'])));
        return json_encode($arr); 
    }

    //remove requests

    public function actionDeleterowss(){

        if($_POST['deleid']){

            $checkid = FinanceRequests::find()->where(['Id' => $_POST['deleid']])->one();
            //print_r($_POST['deleid']); exit;
            if(!empty($checkid)){
                $checkid->Status=8;
                $checkid->save(false);
                $arr = array('error' => 'Yes');
                echo json_encode($arr); 
            }else{
                $arr = array('error' => 'No');
                echo json_encode($arr); 
            }
            
        }
    
    }

    // finance Approve tab start//
    public function actionGetaddrowsapprov(){

        $aheadapprovid = $_POST['aheadapprovid'];
        $userid = Yii::$app->user->id;
        if(!empty($aheadapprovid)){
            $finRequestts = FinanceRequests::find()->where(['Status' => 0])->andWhere(['account_id' => $aheadapprovid])->orderBy(['Id' => SORT_DESC])->all();
            $datatwo = '';
            foreach($finRequestts as $requests){

                $projectname = Projects::find()->where(['Project_Id' => $requests->project_id])->one();
                //$useraccount = UserAccounts::find()->where(['user_id'=> $requests->User_Id])->one();
                $auser = User::find()->where(['id' => $requests->User_Id])->one();
                $ruser = User::find()->where(['id' => $requests->reqforUser])->one();
                $accountitemss = AccountsItem::find()->where(['id'=> $requests->credit_account])->one();
                $datatwo.= '<div class="col-md-12 fa-list-cntnr">
                <div class="row">
                    <div class="col-md-12 type">
                        <h5>'.$projectname->Name.'</h5>
                    </div>
                    
                    <div class="col-md-3 type">
                        <label>Purpose</label> 
                        <span>'.$requests->Purpose.'</span>
                    </div>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-3 type">
                                <label>Date</label>
                                <span>'.$requests->date.'</span>
                            </div>
                            <div class="col-md-2 type">
                                <label>Accountant</label>
                                <span>'.$auser->username.'</span>
                            </div>';
                            if($requests->Contra == 1){
                                $Cashmode = 'Contract';
                            }else{
                                if($requests->payment == 1){
                                    $Cashmode = 'Cash';
                                }elseif($requests->payment == 2){
                                    $Cashmode = 'Bank';
                                }elseif($requests->payment == 3){
                                    $Cashmode = 'Contract';
                                }
                            }
                $datatwo.= '<div class="col-md-1 type">
                                <label>Mode</label>
                                <span>'.$Cashmode.'</span>
                            </div>
                            <div class="col-md-2 type">
                                <label>Amount</label>
                                <span>₹ '.number_format($requests->Amount,2).'</span>
                            </div>
                            <div class="col-md-2 type">
                                <label>Net Amount</label>
                                <span>₹ '.number_format($requests->netamount,2).'</span>
                            </div>
                            
                        </div>
                    </div>
                    <div class="col-md-1 icon-groups">
                        <a class="btn btn-primary icon-eye btn-show-fa-hidden" href="#"></a>
                        <a class="btn btn-success icon-check innactive ApproveMyfund" href="#" id="ApproveMyfund-'.$requests->Id.'" data-id="'.$requests->Id.'"></a>
                        
                        <input type="hidden" id="ApprovefundBank-'.$requests->Id.'" data-id="'.$requests->account_id.'" />  
                        <a class="btn btn-primary icon-close innactive RejectMyfund" href="#" id="RejectMyfund-'.$requests->Id.'" data-id="'.$requests->Id.'"></a>
                        
                    </div>
                    
                    <div class="col-md-12">&nbsp;</div>
                    <div class="col-md-3 type fa-hidden">
                        <label>Requested By</label> 
                        <span>'.$ruser->username.'</span>
                    </div>
                    <div class="col-md-8  fa-hidden">
                        <div class="row">
                            <div class="col-md-1 type">
                                <label>TDS</label>
                                <span>'.$requests->tds.'</span>
                            </div>
                            <div class="col-md-1 type">
                                <label>SGST</label>
                                <span>'.$requests->tax.'</span>
                            </div>
                            <div class="col-md-1 type">
                                <label>CGST</label>
                                <span>'.$requests->tax.'</span>
                            </div>
                            <div class="col-md-2 type">
                                <label>IGST</label>
                                <span>'.$requests->igst_tax.'</span>
                            </div>
                            <div class="col-md-7 type">
                                <label>Account Head</label>
                                <span>'.$accountitemss->name.'</span>
                            </div>
                            
                        </div>
                    </div>
                    <div class="col-md-1 fa-hidden">
                        
                    </div>
                    
                    
                    
                </div>
            </div>
                ';
            }

            $arr=array('error'=>'No','resulttwo'=>$datatwo);
            return json_encode($arr);
        }

    }

    public function actionGetprojectsforapprove(){

        if($_POST['searchproject']){
            $finRequestts = FinanceRequests::find()->where(['Status' => 0])->andWhere(['account_id' => $_POST['searchBank']])->andWhere(['project_id' => $_POST['searchproject']])->orderBy(['Id' => SORT_DESC])->all();
            //print_r($finRequestts);exit;
            if(!empty($finRequestts)){
            $datatwo = '';
            foreach($finRequestts as $requests){

                $projectname = Projects::find()->where(['Project_Id' => $requests->project_id])->one();
                //$useraccount = UserAccounts::find()->where(['user_id'=> $requests->User_Id])->one();
                $auser = User::find()->where(['id' => $requests->User_Id])->one();
                $ruser = User::find()->where(['id' => $requests->reqforUser])->one();
                if(!empty($auser)){
                    $acountuser = $auser->username;
                }else{
                    $acountuser = '';
                }
                if(!empty($ruser)){
                    $requser = $ruser->username;
                }else{
                    $requser = '';
                }
                $accountitemss = AccountsItem::find()->where(['id'=> $requests->credit_account])->one();
                $datatwo.= '<div class="col-md-12 fa-list-cntnr">
                <div class="row">
                    <div class="col-md-12 type">
                        <h5>'.$projectname->Name.'</h5>
                    </div>
                    
                    <div class="col-md-3 type">
                        <label>Purpose</label> 
                        <span>'.$requests->Purpose.'</span>
                    </div>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-3 type">
                                <label>Date</label>
                                <span>'.$requests->date.'</span>
                            </div>
                            <div class="col-md-2 type">
                                <label>Accountant</label>
                                <span>'.$acountuser.'</span>
                            </div>';
                            if($requests->Contra == 1){
                                $Cashmode = 'Contract';
                            }else{
                                if($requests->payment == 1){
                                    $Cashmode = 'Cash';
                                }elseif($requests->payment == 2){
                                    $Cashmode = 'Bank';
                                }elseif($requests->payment == 3){
                                    $Cashmode = 'Contract';
                                }
                            }
                $datatwo.= '<div class="col-md-1 type">
                                <label>Mode</label>
                                <span>'.$Cashmode.'</span>
                            </div>
                            <div class="col-md-2 type">
                                <label>Amount</label>
                                <span>₹ '.number_format(intval($requests->Amount),2).'</span>
                            </div>
                            <div class="col-md-2 type">
                                <label>Net Amount</label>
                                <span>₹ '.number_format(intval($requests->netamount),2).'</span>
                            </div>
                            
                        </div>
                    </div>
                    <div class="col-md-1 icon-groups">
                        <a class="btn btn-primary icon-eye btn-show-fa-hidden" href="#"></a>
                        <a class="btn btn-success  icon-check innactive ApproveMyfund" href="#" id="ApproveMyfund-'.$requests->Id.'" data-id="'.$requests->Id.'"></a>
                        
                        <input type="hidden" id="ApprovefundBank-'.$requests->Id.'" data-id="'.$requests->account_id.'" />  
                        <a class="btn btn-primary icon-close innactive RejectMyfund" href="#" id="RejectMyfund-'.$requests->Id.'" data-id="'.$requests->Id.'"></a>
                    </div>
                    
                    <div class="col-md-12">&nbsp;</div>
                    <div class="col-md-3 type fa-hidden">
                        <label>Requested By</label> 
                        <span>'.$requser.'</span>
                    </div>
                    <div class="col-md-8  fa-hidden">
                        <div class="row">
                            <div class="col-md-1 type">
                                <label>TDS</label>
                                <span>'.$requests->tds.'</span>
                            </div>
                            <div class="col-md-1 type">
                                <label>SGST</label>
                                <span>'.$requests->tax.'</span>
                            </div>
                            <div class="col-md-1 type">
                                <label>CGST</label>
                                <span>'.$requests->tax.'</span>
                            </div>
                            <div class="col-md-2 type">
                                <label>IGST</label>
                                <span>'.$requests->igst_tax.'</span>
                            </div>
                            <div class="col-md-7 type">
                                <label>Account Head</label>
                                <span>'.$accountitemss->name.'</span>
                            </div>
                            
                        </div>
                    </div><br><br><br><br>
                    <div class="col-md-1 fa-hidden">
                        
                    </div>
                    
                    
                    
                </div>
            </div>';
            }

            $arr=array('error'=>'No','resultsearch'=>$datatwo);
            return json_encode($arr);

        }else{

            $arr=array('error'=>'Yes');
            return json_encode($arr);
        }
            
        }
    
    }
    public function actionApprovestatus()
    {
        $finrqst=FinanceRequests::find()->where(['Id'=>$_POST['finreqID']])->one();

        if($finrqst->appstatus==0){
            $finrqst->appstatus=1;
            $finrqst->save(false);
        }elseif($finrqst->appstatus==1){
            $finrqst->appstatus=0;
            $finrqst->save(false);
        }

         $arr=array('error'=>'NO');
         return json_encode($arr);

    }

    public function actionApprovemyfund(){

        //echo 'hai'; exit;

        if(isset($_POST['selfinreqid'])):

            $bankid = '';

            for($i=0;$i<count($_POST['selfinreqid']);$i++)
            {
                $checkid = FinanceRequests::find()->where(['Id' => $_POST['selfinreqid'][$i]])->one();
                $bankid = $checkid->account_id;
                if($_POST['apprvestatus'][$i]==1){
                    //print_r($_POST['deleid']); exit;
                    if(!empty($checkid)){
                        $checkid->Status = $_POST['apprvestatus'][$i];
                        $checkid->Approved_By = Yii::$app->user->id;
                        $checkid->Approved_On = date('y-m-d h:i:s');
                        $checkid->save(false);
                    }

                }
            }

            $arr = array('error' => 'No','bankid'=>$bankid);
            return json_encode($arr); 

        endif;

        /*if($_POST['finreqID']){

            $checkid = FinanceRequests::find()->where(['Id' => $_POST['finreqID']])->one();
            //print_r($_POST['deleid']); exit;
            if(!empty($checkid)){
                $checkid->Status = $_POST['status'];
                $checkid->Approved_By = Yii::$app->user->id;
                $checkid->Approved_On = date('y-m-d h:i:s');
                $checkid->save(false);
                $arr = array('error' => 'Yes');
                return json_encode($arr); 
            }else{
                $arr = array('error' => 'No');
                return json_encode($arr); 
            }
            
        }*/
    
    }

    public function actionHoldmyfund(){

        if($_POST['finreqID']){

            $checkid = FinanceRequests::find()->where(['Id' => $_POST['finreqID']])->one();
            //print_r($_POST['deleid']); exit;
            if(!empty($checkid)){
                $checkid->hold_status = $_POST['status'];
                $checkid->save(false);
                $arr = array('error' => 'Yes');
                return json_encode($arr); 
            }else{
                $arr = array('error' => 'No');
                return json_encode($arr); 
            }
            
        }
    
    }
    public function actionDeleterequest()
    {
        $id=$_POST['finreqID'];
        $connection =  \Yii::$app->db;
        if(isset($_POST['finreqID'])):
            $sql="DELETE FROM finance_requests WHERE Id='$id'";
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $arr = array('Id' => $_POST['finreqID'],'error'=>'No');
            return json_encode($arr);
        endif;
    }
    public function actionCashbook()
    {
        //$projectid=$_POST['id'];
        $uid= Yii::$app->user->Id;
        $cashaccntuser=CashbankuserSelection::find()->where(['userid'=>$uid])->one();
        if($cashaccntuser && $_POST['account']==''){

            $account= $cashaccntuser->accountid;

        }

            $startdate=date('Y-m-d',strtotime($_POST['fromdate']));
            $enddate=date('Y-m-d',strtotime($_POST['todate']));

            if($_POST['account']!=''){
                $account=$_POST['account'];
            }
            else{
                $account= $cashaccntuser->accountid;
            }
            $acntitms=AccountsItem::find()->where(['id'=>$account])->andWhere(['Status'=>0])->one();
            $ar=array("startdate"=>$startdate,"enddate"=>$enddate,'account'=>$account);
            $str=http_build_query($ar);
            //$connection = CActiveRecord::getDbConnection();
            $connection =  \Yii::$app->db;
            $sql="SELECT Name FROM projects";
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $value=$dataReader->read();
            $datarows='';
            $condition='';
            if($account!=''):
                //$condition.=" AND (account_id='".$account."' OR creditacnt='".$account."')";
                $condition.=" (account_id='".$account."' OR creditacnt='".$account."')";
            endif;
            $fromdate = date('M-d-Y',strtotime($startdate));
            $todate = date('M-d-Y',strtotime($enddate));
            //$projectname="<p style='padding-left: 10%;'>Cash Book of ".$value['Name']."</p><p style='font-size: 18px;padding-left: 10%;'>".$_POST['fromdate']." to ".$_POST['todate']."</p>";
            $datarows.="
                        <div class='row list-head'>
                            <div class='col-md-12 text-center'>
                                <label style='text-align: center;font-size: 15px;'>Cash Book Of ".$acntitms->name."</label>From 
                                <span class='date'><em class='cal-icon icon-calendar1'></em>".$fromdate."</span> 
                                to <span class='date'><em class='cal-icon icon-calendar1'></em>
                                ".$todate."</span>
                                <div align='right' style='padding-right: 13px;
                                padding-left: 13px;margin-top: -48px;'>
                                    <a href='".Yii::$app->request->baseUrl."/financerequests/printcashbook?".$str."' title='Print' target='_blank'><i class='glyphicon glyphicon-print'></i>Print</a>
                                </div>
                            </div>
                        </div>";
            //$print="<a href='".Yii::app()->request->baseUrl."/FinanceRequests/printcashbook?".$str."' target='_blank'><i class='glyphicon glyphicon-print'></i>Print</a>";
           /* $datarows.="
                        <div class='custom-print-btn'>
                            <div class='icon-groups'>
                                <a href='".Yii::$app->request->baseUrl."/financerequests/printcashbook?".$str."' target='_blank' class='btn btn-primary text-button'><span class='icon-print'></span>Print</a>
                                <!--<a href='#' class='btn btn-primary text-button'><span class='icon-print'></span>Print</a>-->
                            </div>
                        </div>";*/
            //$sql="SELECT id,date,amount,account_id,creditacnt,narration,type,voucher_no,contra FROM voucher WHERE payment='1' ".$condition." AND date BETWEEN '$startdate' AND '$enddate' ORDER BY UNIX_TIMESTAMP(date) ASC";
            $sql="SELECT id,date,amount,account_id,creditacnt,narration,type,voucher_no,contra FROM voucher WHERE ".$condition." AND date BETWEEN '$startdate' AND '$enddate' ORDER BY UNIX_TIMESTAMP(date) ASC";

            //echo $sql; exit;
            
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $dataProvider=$dataReader->readAll();
            $sql2="select date from voucher where date=(select max(date) from voucher where date < '".$startdate."' AND payment='1')";
            $command=$connection->createCommand($sql2);
            $dataReader=$command->query();
            $data=$dataReader->read();
            //print_r($data);exit;
            //$sql1="SELECT open_balance FROM opening_balance WHERE projectid='$projectid' AND payment='1' ";
            /*$month=date('m');
            if($month=='01' || $month=='02' || $month=='03'):
                $initialdate=date("Y",strtotime("-1 year")).'-04-01';
            else:
                $initialdate=date('Y').'-04-01';
            endif;*/
            $sql1="SELECT id FROM accounts_item WHERE account_type='1'";
            $command=$connection->createCommand($sql1);
            $dataReader=$command->query();
            $data=$dataReader->read();
            //$accountid=$data['id'];
            $accountid= $account;
            $sql3="SELECT balance FROM ledger_openingbalance WHERE accountid=".$accountid." ";
            $command=$connection->createCommand($sql3);
            $dataReader=$command->query();
            $data=$dataReader->read();
            $openingbal=$data['balance'];
            //echo $openingbal;exit;
            $initialdate='2013-04-01';
            $enddate=date('Y-m-d', strtotime($startdate .' -1 day'));
            if($data){
                $command = Yii::$app->db->createCommand("CALL GetBalance(:startdate, :currentdate,NULL,1,NULL,".$accountid.",@outval)");
            }
            else{
                $command = Yii::$app->db->createCommand("CALL GetBalance(:startdate, :currentdate,NULL,1,NULL,NULL,@outval)");
            }
            
            $command->bindParam(":startdate",$initialdate);
            $command->bindParam(":currentdate", $enddate);
            $command->query();    

            $newstartdate=date('Y-m-d', strtotime($startdate .' -1 day'));        

            /*$paymenttotal = Voucher::find()->where(['type'=> 'Payment'])->andWhere(['creditacnt'=> $accountid])->andWhere(['payment'=> '1'])->andWhere(['between', 'date', $initialdate, $newstartdate])->sum('amount');
            $receipttotal = Voucher::find()->where(['type'=> 'Receipt'])->andWhere(['creditacnt'=> $accountid])->andWhere(['payment'=> '1'])->andWhere(['between', 'date', $initialdate, $newstartdate])->sum('amount');
            if(is_null($paymenttotal)){
                $paymenttotal = 0;
            }
            if(is_null($receipttotal)){
                $receipttotal = 0;
            }
            $checkRbal = $paymenttotal - $receipttotal;*/

            $credtcondtn = '';
            $debitcondtn = '';
            $accntcondtn = '';

            if($accountid!=0){
                $credtcondtn.= "creditacnt='".$accountid."' AND ";
                $debitcondtn.= "debitacnt='".$accountid."' AND ";
                $accntcondtn.= "account_id='".$accountid."' AND ";
            }

            $debittotalsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$accntcondtn." contra=0 AND date BETWEEN '".$initialdate."' AND '".$newstartdate."'";
            $command=$connection->createCommand($debittotalsql);
            $dataReader=$command->query();
            $debittotal=$dataReader->read();

            $credittotalsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$credtcondtn." contra=0 AND date BETWEEN '".$initialdate."' AND '".$newstartdate."'";
            $command=$connection->createCommand($credittotalsql);
            $dataReader=$command->query();
            $credittotal=$dataReader->read();

            $contradebitsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$accntcondtn." contra=1 AND type='Receipt' AND`date` BETWEEN '".$initialdate."' AND '".$newstartdate."'";
            $command=$connection->createCommand($contradebitsql);
            $dataReader=$command->query();
            $contradebit=$dataReader->read();

            $contracreditsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$credtcondtn." contra=1 AND type='Payment' AND`date` BETWEEN '".$initialdate."' AND '".$newstartdate."'";
            $command=$connection->createCommand($contracreditsql);
            $dataReader=$command->query();
            $contracredit=$dataReader->read();

            $journeldebitsql = "SELECT SUM(`amount`) as amount FROM `journalvoucher` WHERE ".$debitcondtn." date BETWEEN '".$initialdate."' AND '".$newstartdate."'";
            $command=$connection->createCommand($journeldebitsql);
            $dataReader=$command->query();
            $journeldebit=$dataReader->read();

            $journelcreditsql = "SELECT SUM(`amount`) as amount FROM `journalvoucher` WHERE ".$credtcondtn." date BETWEEN '".$initialdate."' AND '".$newstartdate."'";
            $command=$connection->createCommand($journelcreditsql);
            $dataReader=$command->query();
            $journelcredit=$dataReader->read();

            if(is_null($debittotal['amount'])){
                $debittotalamt = 0;
            }
            else{
                $debittotalamt = $debittotal['amount'];
            }

            if(is_null($credittotal['amount'])){
                $credittotalamt = 0;
            }
            else{
                $credittotalamt = $credittotal['amount'];
            }

            if(is_null($contradebit['amount'])){
                $contradebitamt = 0;
            }
            else{
                $contradebitamt = $contradebit['amount'];
            }

            if(is_null($contracredit['amount'])){
                $contracreditamt = 0;
            }
            else{
                $contracreditamt = $contracredit['amount'];
            }

            if(is_null($journeldebit['amount'])){
                $journeldebitamt = 0;
            }
            else{
                $journeldebitamt = $journeldebit['amount'];
            }

            if(is_null($journelcredit['amount'])){
                $journelcreditamt = 0;
            }
            else{
                $journelcreditamt = $journelcredit['amount'];
            }

            $checkRbal = ($credittotalamt + $contracreditamt + $journelcreditamt) - ($debittotalamt + $contradebitamt + $journeldebitamt);

            //$openbalance = $openingbal + (Yii::$app->db->createCommand("select @outval as result;")->queryScalar());
            $openbalance = $openingbal + $checkRbal;
            //echo $openingbal;exit;
            $totalpamount=0;
            $totalramount=0;
            if(count($dataProvider)>0):
                //if($openbalance>0):
                if($openbalance<0):
                    $rectot=abs($openbalance);
                    $recamount=number_format((float)$rectot, 2);
                    $payamount='';
                else:            
                    $recamount='';
                    $paytot=abs($openbalance);
                    $payamount=number_format((float)$paytot, 2);
                endif;
                //$datarows.="<tr><td colspan='4'>Opening Balance</td><td style='text-align: right;'>$recamount</td><td colspan='3'>$payamount</td></tr>";
                
                
                
                $datarows.="<div class='row cash-book-list'> </div>
                            <div class='row cashbook_headings sbgrp' style='margin-bottom: 0px;'>
                                <div class='col-md-10'>
                                    <div class='row'>
                                    <div class='col-md-2' style='display:none;'></div>
                                    <div class='col-md-2'>
                                        <label>Date</label>
                                    </div>
                                    <div class='col-md-3'>
                                        <label>Account Head</label>
                                    </div>
                                    <div class='col-md-3'>
                                        <label>Narration</label>
                                    </div>
                                    <div class='col-md-2' style='text-align: right;'>
                                        <label>Receipt</label>
                                    </div>
                                    <div class='col-md-2' style='text-align: right;'>
                                        <label>Payment</label>
                                    </div>
                                    </div>
                                </div>
                                <div class='col-md-2'>
                                </div>

                            </div>";

                $datarows.="
                        <div class='marginrow2 row cash-book-list closing-balance-receipt-payment'>
                            <div class='col-md-10'>
                                <div class='row disptab'>
                                    <div class='col-md-2' style='display:none;'></div>
                                    <div class='col-md-2'>
                                    <label></label>
                                        <span style='min-height:44px;display:inline-block;'></span>

                                    </div>
                                    
                                    <div class='col-md-3'>
                                    <label></label>
                                    <span style='min-height:44px;display:inline-block;'></span>
                                    </div>
                                    <div class='col-md-3 type'>
                                        <label></label>
                                        <span style='min-height:44px;display:inline-block;'>Opening Balance</span>
                                    </div>
                                    <div class='col-md-2 text-right'>
                                        <label></label>
                                        <span style='min-height:44px;display:inline-block;'>$recamount</span>

                                    </div>
                                    <div class='col-md-2 text-right'>
                                        <label></label>
                                        <span style='min-height:44px;display:inline-block;'>$payamount</span>

                                    </div>
                            
                                    
                            
                                </div>
                            </div>
                            <div class='col-md-2 icon-groups' style='border-right:0px !important;'>
                                
                            </div>
                        </div>";


                foreach($dataProvider AS $key=>$data):
                    $sql="SELECT name FROM accounts_item WHERE id='".$data['account_id']."' AND Status=0 ";
                    $command=$connection->createCommand($sql);
                    $dataReader=$command->query();
                    $debit=$dataReader->read();

                    $sql="SELECT name FROM accounts_item WHERE id='".$data['creditacnt']."' AND Status=0 ";
                    $command=$connection->createCommand($sql);
                    $dataReader=$command->query();
                    $credit=$dataReader->read();
                    if($data['contra']==0){
                        if($data['creditacnt']==$accountid):
                            $pamount=number_format((float)$data['amount'], 2);
                            $ramount='';
                            $totalpamount=$totalpamount + str_replace(",", "", $pamount);
                            $accountname=$debit['name'];
                        elseif($data['account_id']==$accountid):
                            $pamount='';
                            $ramount=number_format((float)$data['amount'], 2);
                            $totalramount=$totalramount + str_replace(",", "", $ramount);
                            $accountname=$credit['name'];
                        endif;
                    }
                    elseif($data['type']=='Payment')
                    {
                        $pamount=number_format((float)$data['amount'], 2);
                        $ramount='';
                        $totalpamount=$totalpamount + str_replace(",", "", $pamount);
                        $accountname=$debit['name'];
                    }
                    else
                    {
                        $pamount='';
                        $ramount=number_format((float)$data['amount'], 2);
                        $totalramount=$totalramount + str_replace(",", "", $ramount);
                        $accountname=$credit['name'];
                    }
                    $listdate = date('M-d-Y',strtotime($data['date']));
                    if($data['contra']=='0'):
                        $datarows.="
                                <div class='marginrow2 row cash-book-list cashlists' style='padding-top: 0px;padding-bottom: 0px;'>
                                    <div class='col-md-10'>
                                        <div class='row disptab'>
                                            <div class='col-md-2 type' style='display:none;'>
                                                <label>Voucher No</label>
                                                <span>".$data['voucher_no']."</span>
                                            </div>

                                            <div class='col-md-2 type'>
                                               
                                                <span class='date'><em class='cal-icon icon-calendar1'></em>".$listdate."</span>
                                            </div>

                                            <div class='col-md-3 type'>
                                               
                                                <span class='aname'>".$accountname."</span>
                                            </div>
                                            

                                             <div class='col-md-3 type'>
                                                
                                                <span class='anarrtn'>".$data['narration']."</span>
                                            </div>  
                                            
                                            <div class='col-md-2 text-right type'>
                                                
                                                <span class='ramnt'>".$ramount."</span>
                                            </div>
                                            <div class='col-md-2 text-right type'>
                                                
                                                <span class='pamnt'>".$pamount."</span>
                                            </div>
                                            

                                        </div>
                                    </div>
                                    <div class='col-md-2 icon-groups' style='min-height:auto;border-right:0px !important;'>";

                        //$datarows.="<a href='#' class='btn btn-primary text-button'><span class='icon-print'></span>Print Voucher</a>";
                        $urlnow=Yii::$app->request->baseUrl."/voucher/printvoucher?pid=".$data['id'];
                        $datarows.="<a href='".$urlnow."'target='_blank' class='btn btn-primary text-button' title='Print'><span class='icon-print'></span>Print</a>";

                        $userid = Yii::$app->user->Id; 
                        $user = User::find()->where(['id'=>$userid])->one();

                        if($user->superuser==1 || $userid==31):
                            $datarows.="<a style='display:none;' href='#' class='btn btn-primary icon-pencil'></a>";
                        endif;

                        $datarows.="</div> </div>";
                    else:
                        $urlnow=Yii::$app->request->baseUrl."/voucher/printcontravoucher?pid=".$data['id'];
                        $datarows.="
                            <div class='marginrow2 row cash-book-list' style='padding-top: 10px;padding-bottom: 10px;'>
                                <div class='col-md-10'>
                                    <div class='row disptab'>
                                
                                        <div class='col-md-2 type' style='display:none;'>
                                            
                                            <span>".$data['voucher_no']."</span>
                                        </div>

                                        <div class='col-md-2 type'>
                                            
                                            <span class='date'><em class='cal-icon icon-calendar1'></em>".$listdate."</span>
                                        </div>

                                        <div class='col-md-3 type'>
                                            
                                            <span>".$accountname."</span>
                                        </div>

                                        <div class='col-md-3 type'>
                                            
                                            <span>".$data['narration']."</span>
                                        </div> 
                                        
                                        
                                        <div class='col-md-2 text-right type'>
                                            
                                            <span>".$ramount."</span>
                                        </div>
                                        <div class='col-md-2 text-right type'>
                                            
                                            <span>".$pamount."</span>
                                        </div>
                                        
                                                                        
                                
                                    </div>
                                </div>
                                <div class='col-md-2 icon-groups' style='border-right:0px !important;'>
                                    <!--<a href='#' target='_blank'>Print Contra Voucher</a>-->
                                    <a href='".$urlnow."' target='_blank' class='btn btn-primary text-button' title='Print'><span class='icon-print'></span>Print</a>
                                </div>
                            </div>";
                    endif;

                endforeach;
                //if($openbalance>0):
                if($openbalance<0):
                    $debittot=abs($openbalance);
                    $totalramount=$totalramount + $debittot;
                else:            
                    $credtot=abs($openbalance);
                    $totalpamount=$totalpamount + $credtot;
                endif;
                $rec=str_replace( ',', '', $recamount );
                $pay=str_replace( ',', '', $payamount );
                //$balance=$totalpamount - $totalramount;
                $balance=$totalramount - $totalpamount;
                $closingbal=$balance;
                //$closingbal=$this->getbalance($openbalance,$balance);
                if($closingbal>0):
                    $rectot=abs($closingbal);
                    $recamount=number_format((float)$rectot, 2);
                    $payamount='';                
                else:
                    $recamount='';
                    $paytot=abs($closingbal);
                    $payamount=number_format((float)$paytot, 2);
                endif;
                $datarows.="
                            <div class='marginrow2 row cash-book-list total-receipt-payment'>
                                <div class='col-md-10'>
                                    <div class='row'>

                                    <div class='col-md-2' style='display:none;'></div>
                                    <div class='col-md-2'>
                                    <label></label>
                                        <span style='min-height: 44px;display: inline-block;'></span>

                                    </div>
                                    
                                    <div class='col-md-3'>
                                        <label></label>
                                        <span style='min-height: 44px;display: inline-block;'></span>
                                    </div>
                                    <div class='col-md-3 type'>
                                        <label></label>
                                        <span style='min-height: 44px;display: inline-block;'>Total</span>
                                    </div>
                                    <div class='col-md-2 text-right'>
                                        <label></label>
                                        <span style='min-height: 44px;display: inline-block;'>".number_format((float)$totalramount, 2)."</span>

                                    </div>
                                    <div class='col-md-2 text-right'>
                                        <label></label>
                                        <span style='min-height: 44px;display: inline-block;'>".number_format((float)$totalpamount, 2)."</span>

                                    </div>
                                
                                        
                                
                                    </div>
                                </div>
                                <div class='col-md-2 icon-groups' style='border-right:0px !important;'>
                                    
                                </div>
                            </div>";

                $datarows.="
                            <div class='marginrow2 row cash-book-list closing-balance-receipt-payment'>
                                <div class='col-md-10'>
                                    <div class='row'>

                                    <div class='col-md-2' style='display:none;'></div>
                                    <div class='col-md-2'>
                                    <label></label>
                                        <span style='min-height: 44px;display: inline-block;'></span>

                                    </div>
                                    
                                    <div class='col-md-3'>
                                        <label></label>
                                        <span style='min-height: 44px;display: inline-block;'></span>
                                    </div>
                                    <div class='col-md-3 type'>
                                        <label></label>
                                        <span style='min-height: 44px;display: inline-block;'>Closing Balance</span>
                                    </div>
                                    <div class='col-md-2 text-right'>
                                        <label></label>
                                        <span style='min-height: 44px;display: inline-block;'>$recamount</span>

                                    </div>
                                    <div class='col-md-2 text-right'>
                                        <label></label>
                                        <span style='min-height: 44px;display: inline-block;'>$payamount</span>

                                    </div>

                                
                                        
                                    </div>
                                </div>
                                <div class='col-md-2 icon-groups' style='border-right:0px !important;'>
                                    
                                </div>
                            </div>";
            else:
                //if($openbalance>0):
                if($openbalance<0):
                    $rectot=abs($openbalance);$recamount=number_format((float)$rectot, 2);$payamount='';
                else:
                    $recamount='';$paytot=abs($openbalance);$payamount=number_format((float)$paytot, 2);
                endif;
                $datarows.="
                            <div class='marginrow2 row cash-book-list closing-balance-receipt-payment'>
                                <div class='col-md-10'>
                                    <div class='row'>
                                
                                        <div class='col-md-8 type text-right'>
                                            <label></label>
                                            <span>Opening Balance</span>
                                        </div>
                                            
                                        <div class='col-md-2 text-right type' style='border-right:0px !important;'>
                                            <label></label>
                                            <span>$recamount</span>
                                        </div>
                                        <div class='col-md-2 text-right type' style='border-right:0px !important;'>
                                            <label></label>
                                            <span>$payamount</span>
                                        </div>
                                
                                    </div>
                                </div>
                                <div class='col-md-2 icon-groups' style='border-right:0px !important;'>
                                    
                                </div>
                            </div>";
                $datarows.="<div class='marginrow2 row cash-book-list' style='text-align: center;'>No Cash Book Entries Found</div>";
                $datarows.="
                            <div class='marginrow2 row cash-book-list closing-balance-receipt-payment'>
                                <div class='col-md-10'>
                                    <div class='row'>
                                
                                        <div class='col-md-8 type text-right'>
                                            <label></label>
                                            <span>Closing Balance</span>
                                        </div>
                                            
                                        <div class='col-md-2 text-right type' style='border-right:0px !important;'>
                                            <label></label>
                                            <span>$recamount</span>
                                        </div>
                                        <div class='col-md-2 text-right type' style='border-right:0px !important;'>
                                            <label></label>
                                            <span>$payamount</span>
                                        </div>
                                
                                    </div>
                                </div>
                                <div class='col-md-2 icon-groups' style='border-right:0px !important;'>
                                    
                                </div>
                            </div>";
            endif;
            $arr = array('result' => $datarows,'error'=>'No');
            return json_encode($arr);

    }
    public function actionCurrentcashbook()
    {
        $uid= Yii::$app->user->Id;
        $cashaccntuser=CashbankuserSelection::find()->where(['userid'=>$uid])->one();
        if($cashaccntuser){

            $connection =  \Yii::$app->db;
            $curr_date=date('Y-m-d');

            $acntitms=AccountsItem::find()->where(['id'=> $cashaccntuser->accountid])->one();

           /* $sql="SELECT id,date,amount,account_id,creditacnt,narration,type,voucher_no,contra FROM voucher WHERE date='$curr_date' AND payment=1 ORDER BY id ASC";*/
           $sql="SELECT id,date,amount,account_id,creditacnt,narration,type,voucher_no,contra FROM voucher WHERE `currentdate` = '$curr_date' AND `payment` = 1 ORDER BY `id` ASC";

            //echo $sql; exit;
            
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $dataProvider=$dataReader->readAll();
            $datarows='';
            $datarows.="<div style='padding-bottom: 31px;'>
                    <label style='text-align: center;font-size: 15px;'>Cash Book Of  ".$acntitms->name."</label>
                    </div>
            <div class='row cashbook_headings'>
                                <div class='col-md-10'>
                                    <div class='row'>
                                    <div class='col-md-2' style='display:none;'></div>
                                    <div class='col-md-2'>
                                        <label>Date</label>
                                    </div>
                                    <div class='col-md-4'>
                                        <label>Account Head</label>
                                    </div>
                                    <div class='col-md-3'>
                                        <label>Narration</label>
                                    </div>
                                    <div class='col-md-1 text-right'>
                                        <label>Receipt</label>
                                    </div>
                                    <div class='col-md-2 text-right'>
                                        <label>Payment</label>
                                    </div>
                                    </div>
                                </div>
                                <div class='col-md-2'>
                                </div>

                            </div>";

            foreach($dataProvider AS $key=>$data):
                    $sql="SELECT name FROM accounts_item WHERE id='".$data['account_id']."' ";
                    $command=$connection->createCommand($sql);
                    $dataReader=$command->query();
                    $debit=$dataReader->read();
                    $sql="SELECT name FROM accounts_item WHERE id='".$data['creditacnt']."' ";
                    $command=$connection->createCommand($sql);
                    $dataReader=$command->query();
                    $credit=$dataReader->read();
                    $totalramount=0;
                    $totalpamount=0;
                    $pamount=0;
                    if($data['type']=='Payment')
                    {
                        $pamount=number_format((float)$data['amount'], 2);
                        $ramount='';
                        $totalpamount=$totalpamount + str_replace(",", "", $pamount);
                        $accountname=$debit['name'];
                    }
                    else
                    {
                        $pamount='';
                        $ramount=number_format((float)$data['amount'], 2);
                        $totalramount=$totalramount + str_replace(",", "", $ramount);
                        $accountname=$credit['name'];
                    }
                    $listdate = date('M-d-Y',strtotime($data['date']));
                    if($data['contra']=='0'):
                        $datarows.="
                                <div class='row cash-book-list cashes' style='height: 80px;'>
                                    <div class='col-md-10'>
                                        <div class='row'>
                                            <div class='col-md-2 type' style='display:none;'>
                                                <label>Voucher No</label>
                                                <span>".$data['voucher_no']."</span>
                                            </div>

                                            <div class='col-md-2 type'>
                                                
                                                <span class='date'><em class='cal-icon icon-calendar1'></em>".$listdate."</span>
                                            </div>

                                            <div class='col-md-4 type'>
                                                
                                                <span>".$accountname."</span>
                                            </div>
                                            

                                             <div class='col-md-3 type'>
                                                
                                                <span>".$data['narration']."</span>
                                            </div>  
                                            
                                            <div class='col-md-1 text-right type'>
                                                
                                                <span>".$ramount."</span>
                                            </div>
                                            <div class='col-md-2 text-right type'>
                                                
                                                <span>".$pamount."</span>
                                            </div>
                                            

                                        </div>
                                    </div>
                                    <div class='col-md-2 icon-groups' style='min-height:auto'>";

                        //$datarows.="<a href='#' class='btn btn-primary text-button'><span class='icon-print'></span>Print Voucher</a>";
                        $urlnow=Yii::$app->request->baseUrl."/voucher/printvoucher?pid=".$data['id'];
                        $datarows.="<a href='".$urlnow."'target='_blank' class='btn btn-primary text-button' title='Print'><span class='icon-print'></span>Print</a>";

                        $userid = Yii::$app->user->Id; 
                        $user = User::find()->where(['id'=>$userid])->one();

                        if($user->superuser==1 || $userid==31):
                            $datarows.="<a style='display:none;' href='#' class='btn btn-primary icon-pencil'></a>";
                        endif;

                        $datarows.="</div> </div>";
                    else:
                        $urlnow=Yii::$app->request->baseUrl."/voucher/printcontravoucher?pid=".$data['id'];
                        $datarows.="
                            <div class='row cash-book-list'>
                                <div class='col-md-10'>
                                    <div class='row'>
                                
                                        <div class='col-md-2 type' style='display:none;'>
                                            <label>Voucher No</label>
                                            <span>".$data['voucher_no']."</span>
                                        </div>

                                        <div class='col-md-2 type'>
                                            <label>Date</label>
                                            <span class='date'><em class='cal-icon icon-calendar1'></em>".$listdate."</span>
                                        </div>

                                        <div class='col-md-4 type'>
                                            <label>Account Head </label>
                                            <span>".$accountname."</span>
                                        </div>

                                        <div class='col-md-3 type'>
                                            <label>Narration</label>
                                            <span>".$data['narration']."</span>
                                        </div> 
                                        
                                        
                                        <div class='col-md-1 text-right type'>
                                            <label>Receipt</label>
                                            <span>".$ramount."</span>
                                        </div>
                                        <div class='col-md-2 text-right type'>
                                            <label>Payment</label>
                                            <span>".$pamount."</span>
                                        </div>
                                        
                                                                        
                                
                                    </div>
                                </div>
                                <div class='col-md-2 icon-groups'>
                                    <!--<a href='#' target='_blank'>Print Contra Voucher</a>-->
                                    <a href='".$urlnow."' target='_blank' class='btn btn-primary text-button' title='Print'><span class='icon-print'></span>Print</a>
                                </div>
                            </div>";
                    endif;

                endforeach;
                $arr = array('result' => $datarows,'error'=>'No');
                return json_encode($arr);
        }else{        
                
                
            }
    }

    public function actionBankbook()
    {
        $uid= Yii::$app->user->Id;
        $cashaccntuser=CashbankuserSelection::find()->where(['userid'=>$uid])->one();
        if($cashaccntuser && $_POST['bankid']==''){
            $bankid=$cashaccntuser->accountid;
        }
        else{
            $bankid=0;
        }
        //$connection = CActiveRecord::getDbConnection();
        $connection =  \Yii::$app->db;
        $datarows='';

        
        if($_POST['bankid']!=''){
            $bankid=$_POST['bankid'];
        }
        else{
            $bankid=$cashaccntuser->accountid;
        }
        $acntitms=AccountsItem::find()->where(['id'=>$bankid])->andWhere(['Status'=>0])->one();
        $startdate=date('Y-m-d',strtotime($_POST['fromdate']));
        $enddate=date('Y-m-d',strtotime($_POST['todate']));
        $account=$_POST['account'];                     
        $ar=array("Bank"=>$bankid,"startdate"=>$startdate,"enddate"=>$enddate,'account'=>$account);
        if($account!=''){
            $condition=" AND (account_id='".$account."' OR creditacnt='".$account."') AND";
        }else{
            $condition=" AND";
        }       
        $str=http_build_query($ar);
        
            $sql="SELECT Name FROM projects";
        
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $value=$dataReader->read();
        $fromdate = date('M-d-Y',strtotime($_POST['fromdate']));
        $todate = date('M-d-Y',strtotime($_POST['todate']));
        $datarows.="
                <div class='row list-head'>
                    <div class='col-md-12 text-center'>
                        <label style='text-align: center;font-size: 15px;'>Bank Book Of  ".$acntitms->name."</label>From 
                        <span class='date'><em class='cal-icon icon-calendar1'></em>".$fromdate."</span> 
                        to <span class='date'><em class='cal-icon icon-calendar1'></em>
                        ".$todate."</span>
                        <div align='right' style='padding-right: 13px;
                        padding-left: 13px;margin-top: -48px;'>
                            <a href='".Yii::$app->request->baseUrl."/financerequests/printbankbook?".$str."' target='_blank' title='Print'><i class='glyphicon glyphicon-print'></i>Print</a>
                        </div>
                    </div>
                </div>";

        /*$datarows.="
                <div class='custom-print-btn'>
                    <div class='icon-groups'>
                        <a href='".Yii::$app->request->baseUrl."/financerequests/printbankbook?".$str."' target='_blank' class='btn btn-primary text-button'><span class='icon-print'></span>Print</a>
                        <!--<a href='#' class='btn btn-primary text-button'><span class='icon-print'></span>Print</a>-->
                    </div>
                </div>";*/

        
        $sql="SELECT id,date,amount,account_id,creditacnt,narration,type,voucher_no,contra,project_id FROM voucher WHERE ";

        
        if($bankid!='0')
        $sql.=" (bank_id='".$bankid."' OR account_id='".$bankid."') ".$condition;
        //$sql.=" payment='2' AND date BETWEEN '$startdate' AND '$enddate' ORDER BY UNIX_TIMESTAMP(date) ASC";
        $sql.=" date BETWEEN '$startdate' AND '$enddate' ORDER BY UNIX_TIMESTAMP(date) ASC";
        //echo $sql;exit;
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $dataProvider=$dataReader->readAll();
        
            $sql2="select date from voucher where date=(select max(date) from voucher where date < '".$startdate."' AND payment='2')";
        
        $command=$connection->createCommand($sql2);
        $dataReader=$command->query();
        $data=$dataReader->read();
        //print_r($data);exit;
        //$sql1="SELECT open_balance FROM opening_balance WHERE projectid='$projectid' AND payment='1' ";
        /*$month=date('m');
        if($month=='01' || $month=='02' || $month=='03'):
            $initialdate=date("Y",strtotime("-1 year")).'-04-01';
        else:
            $initialdate=date('Y').'-04-01';
        endif;*/
        $initialdate='2014-04-01';
        $enddate=date('Y-m-d', strtotime($startdate .' -1 day'));

        //if($placeid!='' && $projectid!=''):
            if($bankid=='0'):
                //$command = Yii::app()->db->createCommand("CALL GetBalance(:startdate, :currentdate,".$projectid.",2,NULL,".$bankid.",@outval)");
                $command = Yii::$app->db->createCommand("CALL GetChartBalance(:startdate, :currentdate,NULL,NULL,2,NULL,NULL,@outval)");
            else:
                //$command = Yii::app()->db->createCommand("CALL GetBalance(:startdate, :currentdate,".$projectid.",2,".$bankid.",".$bankid.",@outval)"); 
                $command = Yii::$app->db->createCommand("CALL GetChartBalance(:startdate, :currentdate,NULL,NULL,2,".$bankid.",".$bankid.",@outval)");           
            endif;
       //endif;

        
            /*if($bankid=='0'):
                //$command = Yii::app()->db->createCommand("CALL GetBalance(:startdate, :currentdate,".$projectid.",2,NULL,".$bankid.",@outval)");
                $command = Yii::$app->db->createCommand("CALL GetChartBalance(:startdate, :currentdate,NULL,".$projectid.",2,NULL,NULL,@outval)");
            else:
                //$command = Yii::app()->db->createCommand("CALL GetBalance(:startdate, :currentdate,".$projectid.",2,".$bankid.",".$bankid.",@outval)"); 
                $command = Yii::$app->db->createCommand("CALL GetChartBalance(:startdate, :currentdate,NULL,NULL,2,".$bankid.",".$bankid.",@outval)");           
            endif;*/
        

       

        $command->bindParam(":startdate",$initialdate);
        $command->bindParam(":currentdate", $enddate);
        $command->query();
        if($bankid!='0'){
            $sql3="SELECT balance FROM ledger_openingbalance WHERE accountid='$bankid' ";
            $command=$connection->createCommand($sql3);
            $dataReader=$command->query();
            $data=$dataReader->read();
            $openingbal=$data['balance']; 
        }
        else{
            $openingbal=0; 
        }      

        $newstartdate=date('Y-m-d', strtotime($startdate .' -1 day'));

        /*$paymenttotal = Voucher::find()->where(['type'=> 'Payment'])->andWhere(['bank_id'=> $bankid])->andWhere(['payment'=> '2'])->andWhere(['between', 'date', $initialdate, $newstartdate])->sum('amount');
        $receipttotal = Voucher::find()->where(['type'=> 'Receipt'])->andWhere(['bank_id'=> $bankid])->andWhere(['payment'=> '2'])->andWhere(['between', 'date', $initialdate, $newstartdate])->sum('amount');
        if(is_null($paymenttotal)){
            $paymenttotal = 0;
        }
        if(is_null($receipttotal)){
            $receipttotal = 0;
        }
        $checkRbal = $paymenttotal - $receipttotal;*/

        $credtcondtn = '';
        $debitcondtn = '';
        $accntcondtn = '';

        if($bankid!=0){
            $credtcondtn.= "creditacnt='".$bankid."' AND ";
            $debitcondtn.= "debitacnt='".$bankid."' AND ";
            $accntcondtn.= "account_id='".$bankid."' AND ";
        }

        $debittotalsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$accntcondtn." contra=0 AND date BETWEEN '".$initialdate."' AND '".$newstartdate."'";
        $command=$connection->createCommand($debittotalsql);
        $dataReader=$command->query();
        $debittotal=$dataReader->read();

        $credittotalsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$credtcondtn." contra=0 AND date BETWEEN '".$initialdate."' AND '".$newstartdate."'";
        $command=$connection->createCommand($credittotalsql);
        $dataReader=$command->query();
        $credittotal=$dataReader->read();

        $contradebitsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$accntcondtn." contra=1 AND type='Receipt' AND`date` BETWEEN '".$initialdate."' AND '".$newstartdate."'";
        $command=$connection->createCommand($contradebitsql);
        $dataReader=$command->query();
        $contradebit=$dataReader->read();

        $contracreditsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$credtcondtn." contra=1 AND type='Payment' AND`date` BETWEEN '".$initialdate."' AND '".$newstartdate."'";
        $command=$connection->createCommand($contracreditsql);
        $dataReader=$command->query();
        $contracredit=$dataReader->read();

        $journeldebitsql = "SELECT SUM(`amount`) as amount FROM `journalvoucher` WHERE ".$debitcondtn." date BETWEEN '".$initialdate."' AND '".$newstartdate."'";
        $command=$connection->createCommand($journeldebitsql);
        $dataReader=$command->query();
        $journeldebit=$dataReader->read();

        $journelcreditsql = "SELECT SUM(`amount`) as amount FROM `journalvoucher` WHERE ".$credtcondtn." date BETWEEN '".$initialdate."' AND '".$newstartdate."'";
        $command=$connection->createCommand($journelcreditsql);
        $dataReader=$command->query();
        $journelcredit=$dataReader->read();

        if(is_null($debittotal['amount'])){
            $debittotalamt = 0;
        }
        else{
            $debittotalamt = $debittotal['amount'];
        }

        if(is_null($credittotal['amount'])){
            $credittotalamt = 0;
        }
        else{
            $credittotalamt = $credittotal['amount'];
        }

        if(is_null($contradebit['amount'])){
            $contradebitamt = 0;
        }
        else{
            $contradebitamt = $contradebit['amount'];
        }

        if(is_null($contracredit['amount'])){
            $contracreditamt = 0;
        }
        else{
            $contracreditamt = $contracredit['amount'];
        }

        if(is_null($journeldebit['amount'])){
            $journeldebitamt = 0;
        }
        else{
            $journeldebitamt = $journeldebit['amount'];
        }

        if(is_null($journelcredit['amount'])){
            $journelcreditamt = 0;
        }
        else{
            $journelcreditamt = $journelcredit['amount'];
        }

        $checkRbal = ($credittotalamt + $contracreditamt + $journelcreditamt) - ($debittotalamt + $contradebitamt + $journeldebitamt); 

        //$openbalance = $openingbal + (Yii::$app->db->createCommand("select @outval as result;")->queryScalar());
        $openbalance = $openingbal + $checkRbal;

        $totalpamount=0;
        $totalramount=0;
        if(count($dataProvider)>0):
            //if($openbalance>0):
            if($openbalance<0):
                $rectot=abs($openbalance);$recamount=number_format((float)$rectot, 2);$payamount='';                
            else:
                $recamount='';$paytot=abs($openbalance);$payamount=number_format((float)$paytot, 2);
            endif;
            


            $datarows.="<div class='row bank-book-list'> </div>
                        <div class='row bankbook_headings sbgrp'>
                            <div class='col-md-10'>
                                <div class='row'>
                                <div class='col-md-2' style='display:none;'></div>
                                <div class='col-md-2'>
                                    <label>Date</label>
                                </div>
                                <div class='col-md-3'>
                                    <label>Account Head</label>
                                </div>
                                <div class='col-md-3'>
                                    <label>Narration</label>
                                </div>
                                <div class='col-md-2' style='text-align: right;'>
                                    <label>Receipt</label>
                                </div>
                                <div class='col-md-2' style='text-align: right;'>
                                    <label>Payment</label>
                                </div>
                                </div>
                            </div>
                            <div class='col-md-2'>
                            </div>

                        </div>";

            $datarows.="
                    <div class='marginrow2 row bank-book-list closing-balance-receipt-payment'>
                        <div class='col-md-10'>
                            <div class='row'>

                                <div class='col-md-2' style='display:none;'></div>
                                    <div class='col-md-2'>
                                    <label></label>
                                        <span></span>

                                    </div>
                                    
                                    <div class='col-md-3'>
                                    </div>
                                    <div class='col-md-3 type'>
                                        <label></label>
                                        <span>Opening Balance</span>
                                    </div>
                                    <div class='col-md-2 text-right'>
                                        <label></label>
                                        <span>$recamount</span>

                                    </div>
                                    <div class='col-md-2 text-right'>
                                        <label></label>
                                        <span>$payamount</span>

                                    </div>
                        
                                
                        
                            </div>
                        </div>
                        <div class='col-md-2 icon-groups'>
                            
                        </div>
                    </div>";

            foreach($dataProvider AS $key=>$data):
                $sql="SELECT name FROM accounts_item WHERE id='".$data['account_id']."' AND Status=0 ";
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();
                $debit=$dataReader->read();
                $sql="SELECT name FROM accounts_item WHERE id='".$data['creditacnt']."' AND Status=0 ";
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();
                $credit=$dataReader->read();
                if($data['contra']==0){
                    if($data['creditacnt']==$bankid):
                        $pamount=number_format((float)$data['amount'], 2);
                        $ramount='';
                        $totalpamount=$totalpamount + str_replace(",", "", $pamount);
                        $accountname=$debit['name'];
                    elseif($data['account_id']==$bankid):
                        $pamount='';
                        $ramount=number_format((float)$data['amount'], 2);
                        $totalramount=$totalramount + str_replace(",", "", $ramount);
                        $accountname=$credit['name'];
                    endif;
                }
                elseif($data['type']=='Payment')
                {
                    $pamount=number_format((float)$data['amount'], 2);
                    $ramount='';
                    $totalpamount=$totalpamount + str_replace(",", "", $pamount);
                    $accountname=$debit['name'];
                }
                else
                {
                    $pamount='';
                    $ramount=number_format((float)$data['amount'], 2);
                    $totalramount=$totalramount + str_replace(",", "", $ramount);
                    $accountname=$credit['name'];
                }
                /*$voucherproject = Projects::model()->findByPk($data['project_id']);
                if($voucherproject){
                    $projname = $voucherproject->Name;
                }
                else{
                    $projname = '';
                }*/
                $listdate = date('M-d-Y',strtotime($data['date']));
                if($data['contra']=='0'):

                    $datarows.="
                            <div class='marginrow2 row bank-book-list' style='padding-bottom: 10px;padding-top: 10px;'>
                                <div class='col-md-10'>
                                    <div class='row'>

                                        <div class='col-md-2 type' style='display:none;'>
                                            <label>Voucher No</label>
                                            <span>".$data['voucher_no']."</span>
                                        </div>

                                        <div class='col-md-2 type'>
                                            
                                            <span class='date'><em class='cal-icon icon-calendar1'></em>".$listdate."</span>
                                        </div>

                                        <div class='col-md-3 type'>
                                            
                                            <span>".$accountname."</span>
                                        </div>


                                         <div class='col-md-3 type'>
                                            
                                            <span>".$data['narration']."</span>
                                        </div>  
                                        
                                        
                                        <div class='col-md-2 text-right type'>
                                            
                                            <span>".$ramount."</span>
                                        </div>
                                        <div class='col-md-2 text-right type'>
                                            
                                            <span>".$pamount."</span>
                                        </div>
                                        
                                                                      
                                    
                                    </div>
                                </div>
                                <div class='col-md-2 icon-groups' style='min-height:auto;'>";

                    $datarows.="<a href=".Yii::$app->request->baseUrl."/voucher/printvoucher/?pid=".$data['id']." target='_blank' class='btn btn-primary text-button' title='Print'><span class='icon-print'></span>Print</a>";

                    $userid = Yii::$app->user->Id; 
                    $user = User::find()->where(['id'=>$userid])->one();

                    if($user->superuser==1 || $userid==31):
                        $datarows.="<a style='display:none;' href='#' class='btn btn-primary icon-pencil'></a>";
                    endif;

                    $datarows.="</div> </div>";

                else:

                    $datarows.="
                        <div class='marginrow2 row bank-book-list'>
                            <div class='col-md-10'>
                                <div class='row'>
                                    <div class='col-md-2 type' style='display:none;'>
                                        <label>Voucher No</label>
                                        <span>".$data['voucher_no']."</span>
                                    </div>

                                    <div class='col-md-2 type'>
                                        
                                        <span class='date'><em class='cal-icon icon-calendar1'></em>".$listdate."</span>
                                    </div>

                                    <div class='col-md-3 type'>
                                       
                                        <span>".$accountname."</span>
                                    </div>


                                    <div class='col-md-3 type'>
                                        
                                        <span>".$data['narration']."</span>
                                    </div> 
                                    
                                    
                                    <div class='col-md-2 text-right type'>
                                        
                                        <span>".$ramount."</span>
                                    </div>
                                    <div class='col-md-2 text-right type'>
                                        
                                        <span>".$pamount."</span>
                                    </div>
                                    
                                                                    
                            
                                </div>
                            </div>
                            <div class='col-md-2 icon-groups' style='min-height:auto;;'>
                                <!--<a href='#' target='_blank'>Print Contra Voucher</a>-->
                                <a href=".Yii::$app->request->baseUrl."/voucher/printcontravoucher/?pid=".$data['id']." target='_blank' class='btn btn-primary text-button' title='Print'><span class='icon-print'></span>Print</a>
                            </div>
                        </div>";
                    
                endif;

            endforeach;
            if($openbalance<0):
                $debittot=abs($openbalance);
                $totalramount=$totalramount + $debittot;
            else:            
                $credtot=abs($openbalance);
                $totalpamount=$totalpamount + $credtot;
            endif;
            $rec=str_replace( ',', '', $recamount );
            $pay=str_replace( ',', '', $payamount );
            //$balance=$totalpamount - $totalramount;
            $balance=$totalramount - $totalpamount;
            $closingbal=$balance;
            //$closingbal=$this->getbalance($openbalance,$balance);
            if($closingbal>0):
                $rectot=abs($closingbal);$recamount=number_format((float)$rectot, 2);$payamount='';
            else:
                $recamount='';$paytot=abs($closingbal);$payamount=number_format((float)$paytot, 2);
            endif;
            $datarows.="
                        <div class='marginrow2 row bank-book-list total-receipt-payment'>
                            <div class='col-md-10'>
                                <div class='row'>


                                <div class='col-md-2' style='display:none;'></div>
                                    <div class='col-md-2'>
                                    <label></label>
                                        <span></span>

                                    </div>
                                    
                                    <div class='col-md-3'>
                                    </div>
                                    <div class='col-md-3 type'>
                                        <label></label>
                                        <span>Total</span>
                                    </div>
                                    <div class='col-md-2 text-right'>
                                        <label></label>
                                        <span>".number_format((float)$totalramount, 2)."</span>

                                    </div>
                                    <div class='col-md-2 text-right'>
                                        <label></label>
                                        <span>".number_format((float)$totalpamount, 2)."</span>

                                    </div>
                            
                                    
                            
                                </div>
                            </div>
                            <div class='col-md-2 icon-groups'>
                                
                            </div>
                        </div>";

            $datarows.="
                        <div class='marginrow2 row bank-book-list closing-balance-receipt-payment'>
                            <div class='col-md-10'>
                                <div class='row'>

                                <div class='col-md-2' style='display:none;'></div>
                                    <div class='col-md-2'>
                                    <label></label>
                                        <span></span>

                                    </div>
                                    
                                    <div class='col-md-3'>
                                    </div>
                                    <div class='col-md-3 type'>
                                        <label></label>
                                        <span>Closing Balance</span>
                                    </div>
                                    <div class='col-md-2 text-right'>
                                        <label></label>
                                        <span>$recamount</span>

                                    </div>
                                    <div class='col-md-2 text-right'>
                                        <label></label>
                                        <span>$payamount</span>

                                    </div>
                            
                                    
                            
                                </div>
                            </div>
                            <div class='col-md-2 icon-groups'>
                                
                            </div>
                        </div>";
        else:
            if($openbalance>0):
                $rectot=abs($openbalance);$recamount=number_format((float)$rectot, 2);$payamount='';
            else:
                $recamount='';$paytot=abs($openbalance);$payamount=number_format((float)$paytot, 2);
            endif;

            $datarows.="
                        <div class='marginrow2 row bank-book-list closing-balance-receipt-payment'>
                            <div class='col-md-10'>
                                <div class='row'>
                            
                                    <div class='col-md-8 type text-right'>
                                        <label></label>
                                        <span>Opening Balance</span>
                                    </div>
                                        
                                    <div class='col-md-2 text-right type'>
                                        <label></label>
                                        <span>$recamount</span>
                                    </div>
                                    <div class='col-md-2 text-right type'>
                                        <label></label>
                                        <span>$payamount</span>
                                    </div>
                            
                                </div>
                            </div>
                            <div class='col-md-2 icon-groups'>
                                
                            </div>
                        </div>";
            $datarows.="<div class='marginrow2 row bank-book-list' style='text-align: center;'>No Bank Book Entries Found</div>";
            $datarows.="
                        <div class='marginrow2 row bank-book-list closing-balance-receipt-payment'>
                            <div class='col-md-10'>
                                <div class='row'>
                            
                                    <div class='col-md-8 type text-right'>
                                        <label></label>
                                        <span>Closing Balance</span>
                                    </div>
                                        
                                    <div class='col-md-2 text-right type'>
                                        <label></label>
                                        <span>$recamount</span>
                                    </div>
                                    <div class='col-md-2 text-right type'>
                                        <label></label>
                                        <span>$payamount</span>
                                    </div>
                            
                                </div>
                            </div>
                            <div class='col-md-2 icon-groups'>
                                
                            </div>
                        </div>";
        endif;
        $arr = array('result' => $datarows,'error'=>'No');
        return json_encode($arr);

    } 
    public function actionCurrentbankbook()
    {
        $uid= Yii::$app->user->Id;
        $cashaccntuser=CashbankuserSelection::find()->where(['userid'=>$uid])->one();
        if($cashaccntuser){
        $connection =  \Yii::$app->db;
        //$projectid=$_POST['project'];
        $acntitms=AccountsItem::find()->where(['id'=> $cashaccntuser->accountid])->one();
        $curr_date=date('Y-m-d');
        $sql="SELECT id,date,amount,account_id,creditacnt,narration,type,voucher_no,contra,project_id FROM voucher where currentdate='$curr_date' AND payment=2 ORDER BY id ASC";
        //echo $sql;exit;
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $dataProvider=$dataReader->readAll();
        $datarows='';
         $datarows.="<div style='padding-bottom: 31px;'>
                    <label style='text-align: center;font-size: 15px;'>Bank Book Of  ".$acntitms->name."</label>
                    </div>
                    <div class='row bankbook_headings'>
                                <div class='col-md-10'>
                                    <div class='row'>
                                    <div class='col-md-2' style='display:none;'></div>
                                    <div class='col-md-2'>
                                        <label>Date</label>
                                    </div>
                                    <div class='col-md-3'>
                                        <label>Account Head</label>
                                    </div>
                                    <div class='col-md-4'>
                                        <label>Narration</label>
                                    </div>
                                    <div class='col-md-1 text-right'>
                                        <label>Receipt</label>
                                    </div>
                                    <div class='col-md-2 text-right'>
                                        <label>Payment</label>
                                    </div>
                                    </div>
                                </div>
                                <div class='col-md-2'>
                                </div>

                            </div>";

         foreach($dataProvider AS $key=>$data):
                $sql="SELECT name FROM accounts_item WHERE id='".$data['account_id']."' ";
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();
                $debit=$dataReader->read();

                $sql="SELECT name FROM accounts_item WHERE id='".$data['creditacnt']."' ";
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();
                $credit=$dataReader->read();

                $totalpamount=0;
                $totalramount=0;
                if($data['type']=='Payment')
                {
                    $pamount=number_format((float)$data['amount'], 2);
                    $ramount='';
                    $totalpamount=$totalpamount + str_replace(",", "", $pamount);
                    $accountname=$debit['name'];
                }
                else
                {
                    $pamount='';
                    $ramount=number_format((float)$data['amount'], 2);
                    $totalramount=$totalramount + str_replace(",", "", $ramount);
                    $accountname=$credit['name'];
                }
                /*$voucherproject = Projects::model()->findByPk($data['project_id']);
                if($voucherproject){
                    $projname = $voucherproject->Name;
                }
                else{
                    $projname = '';
                }*/
                $listdate = date('M-d-Y',strtotime($data['date']));
               
                if($data['contra']=='0'):

                    $datarows.="
                            <div class='row bank-book-list' style='padding-bottom: 10px;padding-top: 10px;'>
                                <div class='col-md-10'>
                                    <div class='row'>

                                        <div class='col-md-2 type' style='display:none;'>
                                            <label>Voucher No</label>
                                            <span>".$data['voucher_no']."</span>
                                        </div>

                                        <div class='col-md-2 type'>
                                            
                                            <span class='date'><em class='cal-icon icon-calendar1'></em>".$listdate."</span>
                                        </div>

                                        <div class='col-md-3 type'>
                                            
                                            <span>".$accountname."</span>
                                        </div>


                                         <div class='col-md-4 type'>
                                            
                                            <span>".$data['narration']."</span>
                                        </div>  
                                        
                                        
                                        <div class='col-md-1 text-right type'>
                                            
                                            <span>".$ramount."</span>
                                        </div>
                                        <div class='col-md-2 text-right type'>
                                           
                                            <span>".$pamount."</span>
                                        </div>
                                        
                                                                      
                                    
                                    </div>
                                </div>
                                <div class='col-md-2 icon-groups' style='bottom:17px;'>";

                    $datarows.="<a href=".Yii::$app->request->baseUrl."/voucher/printvoucher/?pid=".$data['id']." target='_blank' class='btn btn-primary text-button' title='Print'><span class='icon-print'></span>Print</a>";

                    $userid = Yii::$app->user->Id; 
                    $user = User::find()->where(['id'=>$userid])->one();

                    if($user->superuser==1 || $userid==31):
                        $datarows.="<a style='display:none;' href='#' class='btn btn-primary icon-pencil'></a>";
                    endif;

                    $datarows.="</div> </div>";

                else:

                    $datarows.="
                        <div class='row bank-book-list'>
                            <div class='col-md-10'>
                                <div class='row'>
                                    <div class='col-md-2 type' style='display:none;'>
                                        <label>Voucher No</label>
                                        <span>".$data['voucher_no']."</span>
                                    </div>

                                    <div class='col-md-2 type'>
                                        <label>Date</label>
                                        <span class='date'><em class='cal-icon icon-calendar1'></em>".$listdate."</span>
                                    </div>

                                    <div class='col-md-3 type'>
                                        <label>Account Head </label>
                                        <span>".$accountname."</span>
                                    </div>


                                    <div class='col-md-4 type'>
                                        <label>Narration</label>
                                        <span>".$data['narration']."</span>
                                    </div> 
                                    
                                    
                                    <div class='col-md-1 text-right type'>
                                        <label>Receipt</label>
                                        <span>".$ramount."</span>
                                    </div>
                                    <div class='col-md-2 text-right type'>
                                        <label>Payment</label>
                                        <span>".$pamount."</span>
                                    </div>
                                    
                                                                    
                            
                                </div>
                            </div>
                            <div class='col-md-2 icon-groups'>
                                <!--<a href='#' target='_blank'>Print Contra Voucher</a>-->
                                <a href=".Yii::$app->request->baseUrl."/voucher/printcontravoucher/?pid=".$data['id']." target='_blank' class='btn btn-primary text-button' title='Print'><span class='icon-print'></span>Print</a>
                            </div>
                        </div>";
                    
                endif;

            endforeach;
            $arr = array('result' => $datarows,'error'=>'No');
            return json_encode($arr);
        }

    }

    public function actionJournalbook()
    {
        $projectid=$_POST['id'];
        $startdate=date('Y-m-d',strtotime($_POST['fromdate']));
        $enddate=date('Y-m-d',strtotime($_POST['todate']));
        $account=$_POST['account'];
        $datarows='';
        $condition='';
        $ar=array("Projectid"=>$projectid,"startdate"=>$startdate,"enddate"=>$enddate,'account'=>$account);
        if($account!=''):
            $condition.=" AND (debitacnt='".$account."' OR creditacnt='".$account."')";
        endif;          
        $str=http_build_query($ar);
        //$print="<a href='".Yii::app()->request->baseUrl."/FinanceRequests/printjournal?".$str."' target='_blank'><i class='glyphicon glyphicon-print'></i>Print</a>";

        $connection =  \Yii::$app->db;
        $sql="SELECT Name FROM projects WHERE Project_Id='$projectid'";
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $value=$dataReader->read();

        $projName = (isset($value['Name'])) ? $value['Name'] : 'All';

        if($projectid)
            $condition.= " AND a.project_id='$projectid'";

        $fromdate = date('M-d-Y',strtotime($startdate));
        $todate = date('M-d-Y',strtotime($enddate));

        $datarows.="<div class='col-md-12' style='padding-top: 30px;'>
                    <label style='text-align: center;font-size: 15px;'>Journal Book Of ".$projName."</label>
                    </div>
        <div class='row list-head'>
                        <div class='col-md-12 text-center type'>
                            <h5>".$projName."</h5>From 
                            <span class='date'><em class='cal-icon icon-calendar1'></em>".$fromdate."</span> 
                            to <span class='date'><em class='cal-icon icon-calendar1'></em>
                            ".$todate."</span>
                           <div align='right' style='padding-right: 13px;
                            padding-left: 13px;margin-top: -48px;'>
                           <a href='".Yii::$app->request->baseUrl."/financerequests/printjournal?".$str."' target='_blank'><i class='glyphicon glyphicon-print'></i>Print</a>
                           </div>
                        </div>
                    </div><br><br><br>";

        /*$datarows.="
                    <div class='custom-print-btn'>
                        <div class='icon-groups'>
                            <a href='".Yii::$app->request->baseUrl."/financerequests/printjournal?".$str."' target='_blank' class='btn btn-primary text-button'><span class='icon-print'></span>Print</a>
                            <!--<a href='#' class='btn btn-primary text-button'><span class='icon-print'></span>Print</a>-->
                        </div>
                    </div>";*/

        $sql1="SELECT a.id,a.date,a.amount,a.debitacnt,a.creditacnt,a.narration,a.voucher_no,b.name FROM journalvoucher as a inner join accounts_item as b on a.debitacnt=b.id WHERE 1  ".$condition." AND a.date BETWEEN '$startdate' AND '$enddate' ORDER BY a.date ASC";
        //echo $sql1;exit;
        $command=$connection->createCommand($sql1);
        $dataReader=$command->query();
        $dataProvider=$dataReader->readAll();
        //$datarows='';
        $sql1="SELECT open_balance FROM opening_balance WHERE date='$startdate' AND projectid='$projectid' ";
        $command=$connection->createCommand($sql1);
        $dataReader=$command->query();
        $openbal=$dataReader->read();

            $datarows.="<div class='row journalbook_headings sbgrp'>
                                <div class='col-md-10'>
                                    <div class='row'>
                                    <div class='col-md-2' style='display:none;'></div>
                                    <div class='col-md-2'>
                                        <label>Date</label>
                                    </div>
                                    <div class='col-md-4'>
                                        <label>Narration</label>
                                    </div>
                                    <div class='col-md-2'>
                                        <label>Debit Account Head</label>
                                    </div>
                                    <div class='col-md-2'>
                                        <label>Credit Account Head</label>
                                    </div>
                                    <div class='col-md-1 text-right'>
                                        <label>Debit</label>
                                    </div>
                                    <div class='col-md-1 text-right'>
                                        <label>Credit</label>
                                    </div>
                                    </div>
                                </div>
                                
                                </div>

                            </div>";
        if(count($dataProvider)>0):
            foreach($dataProvider AS $key=>$data):
                $sql="SELECT name FROM accounts_item WHERE id='".$data['debitacnt']."' AND Status=0";
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();
                $debitaccount=$dataReader->read();
                $sql="SELECT name FROM accounts_item WHERE id='".$data['creditacnt']."' AND Status=0";
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();
                $creditaccount=$dataReader->read();

                $listdate = date('M-d-Y',strtotime($data['date']));

                $datarows.="
                        <div class='marginrow2 row journal-book-list' style='height: 80px;'>
                            <div class='col-md-10'>
                                <div class='row'> 
                                <div class='col-md-2 type' style='display:none;'>
                                        <label>Voucher No</label>
                                        <span>".$data['voucher_no']."</span>
                                    </div>

                                     <div class='col-md-2 type'>
                                        
                                        <span class='date'><em class='cal-icon icon-calendar1'></em>".$listdate."</span>
                                    </div>

                                     <div class='col-md-4 type'>
                                       
                                        <span>".$data['narration']."</span>
                                    </div>

                                    <div class='col-md-2 type'>
                                        
                                        <span>".$debitaccount['name']."</span>
                                    </div>
                                    <div class='col-md-2 type'>
                                       
                                        <span>".$creditaccount['name']."</span>
                                    </div>
                                    
                                    
                                    
                                    <div class='col-md-1 text-right type'>
                                        
                                        <span>".number_format((float)$data['amount'], 2)."</span>
                                    </div>
                                    <div class='col-md-1 text-right type'>
                                        
                                        <span>".number_format((float)$data['amount'], 2)."</span>
                                    </div>
                                    <div class='col-md-12 type '>
                                        
                                    </div>
                                
                                   
                                    <div class='col-md-4 type '>
                                    
                                    </div>                      
                                </div>
                            </div>
                            <div class='col-md-2 icon-groups' style='bottom: 17px;'>";

                $userid = Yii::$app->user->Id; 
                $user = User::find()->where(['id'=>$userid])->one();

                $datarows.="<a href='".Yii::$app->request->baseUrl."/voucher/printjournal/?pid=".$data['id']."' target='_blank'' class='btn btn-primary text-button' title='Print'><span class='icon-print'></span>Print</a>";

                if($user->superuser==1 || $userid==31):
                    $datarows.="<a style='display:none;' href='#'' class='btn btn-primary icon-pencil'></a>";
                endif;

                $datarows.="</div></div>";

            endforeach;

        else:
            $datarows="<div class='marginrow2 row journal-book-list' style='text-align: center;'>No Journal Found</div>";
        endif;
        $arr = array('result' => $datarows,'error'=>'No');
        return json_encode($arr);
    }
    public function actionCurrentjournalbook()
    {
         $connection =  \Yii::$app->db;
        $curr_date=date('Y-m-d');

         $sql1="SELECT a.id,a.date,a.amount,a.debitacnt,a.creditacnt,a.narration,a.voucher_no,b.name FROM journalvoucher as a inner join accounts_item as b on a.debitacnt=b.id WHERE date='$curr_date'";
        //echo $sql1;exit;
        $command=$connection->createCommand($sql1);
        $dataReader=$command->query();
        $dataProvider=$dataReader->readAll();

        $datarows='';
        if(count($dataProvider)>0):
            foreach($dataProvider AS $key=>$data):
                $sql="SELECT name FROM accounts_item WHERE id='".$data['debitacnt']."'";
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();
                $debitaccount=$dataReader->read();
                $sql="SELECT name FROM accounts_item WHERE id='".$data['creditacnt']."'";
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();
                $creditaccount=$dataReader->read();

                $listdate = date('M-d-Y',strtotime($data['date']));

                $datarows.="
                        <div class='row journal-book-list'>
                            <div class='col-md-10'>
                                <div class='row'> 
                                <div class='col-md-2 type' style='display:none;'>
                                        <label>Voucher No</label>
                                        <span>".$data['voucher_no']."</span>
                                    </div>

                                     <div class='col-md-2 type'>
                                        <label>Date</label>
                                        <span class='date'><em class='cal-icon icon-calendar1'></em>".$listdate."</span>
                                    </div>

                                     <div class='col-md-4 type'>
                                        <label>Narration</label>
                                        <span>".$data['narration']."</span>
                                    </div>

                                    <div class='col-md-2 type'>
                                        <label>Debit Account Head   </label>
                                        <span>".$debitaccount['name']."</span>
                                    </div>
                                    <div class='col-md-2 type'>
                                        <label>Credit Account Head  </label>
                                        <span>".$creditaccount['name']."</span>
                                    </div>
                                    
                                    
                                    
                                    <div class='col-md-1 text-right type'>
                                        <label>Debit</label>
                                        <span>".number_format((float)$data['amount'], 2)."</span>
                                    </div>
                                    <div class='col-md-1 text-right type'>
                                        <label>Credit</label>
                                        <span>".number_format((float)$data['amount'], 2)."</span>
                                    </div>
                                    <div class='col-md-12 type '>
                                        
                                    </div>
                                
                                   
                                    <div class='col-md-4 type '>
                                    
                                    </div>                      
                                </div>
                            </div>
                            <div class='col-md-2 icon-groups'>";

                $userid = Yii::$app->user->Id; 
                $user = User::find()->where(['id'=>$userid])->one();

                $datarows.="<a href='".Yii::$app->request->baseUrl."/voucher/printjournal/?pid=".$data['id']."' target='_blank'' class='btn btn-primary text-button'><span class='icon-print'></span>Print</a>";

                if($user->superuser==1 || $userid==31):
                    $datarows.="<a style='display:none;' href='#'' class='btn btn-primary icon-pencil'></a>";
                endif;

                $datarows.="</div></div>";

            endforeach;

        else:
            $datarows="<div class='row journal-book-list' style='text-align: center;'>No Journal Found</div>";
        endif;
        $arr = array('result' => $datarows,'error'=>'No');
        return json_encode($arr);
    }

    public function actionLedger()
    {
        $connection =  \Yii::$app->db;
        $userid = Yii::$app->user->Id; 
        $user = User::find()->where(['id'=>$userid])->one();
        $startdate=date('Y-m-d',strtotime($_POST['fromdate']));
        $enddate=date('Y-m-d',strtotime($_POST['todate']));
        $datarows='';
        $condition='';
        if($_POST['fromdate']!='' && $_POST['todate']!=''):
            $ar=array("Accounthead"=>$_POST['accountid'],"Place"=>$_POST['place'],"Project"=>$_POST['projectID'],"startdate"=>$startdate,"enddate"=>$enddate);
        else:
            $ar=array("Accounthead"=>$_POST['accountid'],"Place"=>$_POST['place'],"Project"=>$_POST['projectID']);
        endif;
        $str=http_build_query($ar);
        $print="<a href='".Yii::$app->request->baseUrl."/financerequests/printledger?".$str."' class='btn btn-primary text-button' target='_blank'><i class='glyphicon glyphicon-print'></i>Print</a>";
        $excel="<a href='".Yii::$app->request->baseUrl."/financerequests/ledgerexcel?".$str."' class='btn btn-primary text-button'><span class='icon-print'></span>Excel</a>";
        $sql="SELECT Name FROM accounts_item WHERE id='".$_POST['accountid']."'";
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $value=$dataReader->read();
        $month=date('m');
        if($month=='01' || $month=='02' || $month=='03'):
            $initialdate=date("Y",strtotime("-1 year")).'-04-01';
        else:
            $initialdate=date('Y').'-04-01';
        endif;

        $fromdate = date('M-d-Y',strtotime($startdate));
        $todate = date('M-d-Y',strtotime($enddate));
        if($_POST['accountid']!=0){
        $datarows.="<div class='col-md-12'>
                    
                    </div>
            <div class='row list-head'>
                <div class='col-md-12 text-center type'>
                    <h5>".$value['Name']."</h5>";
                }else{
                    $datarows.="<div class='col-md-12' style='padding-top: 20px;
                padding-bottom: 15px;'>
                    <label style='text-align: center;font-size: 15px;'>Ledger</label>
                    </div>
            <div class='row list-head'>
                <div class='col-md-12 text-center type'>
                    <h5>".$value['Name']."</h5>";
                }

        if($_POST['fromdate']!='' || $_POST['todate']!=''):
            if($_POST['todate']==''){
                $enddate = date('Y-m-d');
                $todate = '';
            }
            if($_POST['fromdate']==''){
                $startdate = '2014-03-31';
                $fromdate = '';
            }
            $datarows.="
                Ledger From <span class='date'><em class='cal-icon icon-calendar1'></em>".$fromdate."</span> to <span class='date'><em class='cal-icon icon-calendar1'></em>".$todate."</span>
                <div align='right' style='padding-right: 13px;
    padding-left: 13px;margin-top: -48px;'>
                    <!--<a href='#'><i class='glyphicon glyphicon-print'></i>Print</a> -->
                </div>

                ";
            //$condition.="AND date BETWEEN '$startdate' AND '$enddate'";
            $condition.=" date BETWEEN '$startdate' AND '$enddate'";
        else:
            //$condition.="AND date >= '$initialdate'";
            $initialdate='2014-03-31';
            $condition.=" date >= '$initialdate'";
        endif;

        $datarows.="
                </div>
            </div>";

        /*$datarows.="
                <div class='custom-print-btn'>
                    <div class='icon-groups'>
                        <a href='#'' class='btn btn-primary text-button'><span class='icon-print'></span>Print</a>                       
                    </div>
                </div>";*/

        $datarows.="<div class='custom-print-btn'>   
                        <div class='icon-groups' style='top:-65px;'>
                            <div>".$print."</div>
                            <div>".$excel."</div>
                        </div>
                    </div>
                    ";

        $sql="SELECT projectid FROM user_projects WHERE userid='".$userid."'";
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $userprojs=$dataReader->readAll();
        $projarr=array();
        foreach($userprojs AS $userproj):
            array_push($projarr,$userproj['projectid']);
        endforeach;
        $var=array_search("12",$projarr);
        //echo $var;exit;
        //var_dump($var);exit;
        /*if($var === false):
            $ledproc="GetSiteLedgerBalance";
            $sql="(SELECT id,voucher_no,date,narration,amount,type,creditacnt,bank_id,contra,account_id,'Voucher' AS rawtype FROM voucher WHERE (account_id='".$_POST['accountid']."' OR creditacnt='".$_POST['accountid']."') AND project_id='".$_POST['place']."' $condition )
                  UNION
                  (SELECT id,voucher_no,date,narration,amount,type,creditacnt,bank_id,contra,debitacnt AS account_id,'Journal' AS rawtype FROM journalvoucher WHERE (debitacnt='".$_POST['accountid']."' OR creditacnt='".$_POST['accountid']."') AND place='".$_POST['place']."' $condition ) ORDER BY DATE_FORMAT(date,'%d/%m/%y') ASC,id DESC";
        else:
            $ledproc="GetLedgerBalance";
            if($_POST['place']==12):
                if($_POST['projectID']==0){
                    $sql="(SELECT id,voucher_no,date,narration,amount,type,creditacnt,bank_id,contra,account_id,'Voucher' AS rawtype FROM voucher WHERE (account_id='".$_POST['accountid']."' OR creditacnt='".$_POST['accountid']."') $condition )
                      UNION
                      (SELECT id,voucher_no,date,narration,amount,type,creditacnt,bank_id,contra,debitacnt AS account_id,'Journal' AS rawtype FROM journalvoucher WHERE (debitacnt='".$_POST['accountid']."' OR creditacnt='".$_POST['accountid']."') $condition ) ORDER BY DATE_FORMAT(date,'%d/%m/%y') ASC,id DESC";
                }
                else{
                    $sql="(SELECT id,voucher_no,date,narration,amount,type,creditacnt,bank_id,contra,account_id,'Voucher' AS rawtype FROM voucher WHERE (account_id='".$_POST['accountid']."' OR creditacnt='".$_POST['accountid']."') AND (place='".$_POST['projectID']."' OR project_id='".$_POST['projectID']."') $condition )
                      UNION
                      (SELECT id,voucher_no,date,narration,amount,type,creditacnt,bank_id,contra,debitacnt AS account_id,'Journal' AS rawtype FROM journalvoucher WHERE (debitacnt='".$_POST['accountid']."' OR creditacnt='".$_POST['accountid']."') AND (project_id='".$_POST['projectID']."' OR place='".$_POST['projectID']."') $condition ) ORDER BY DATE_FORMAT(date,'%d/%m/%y') ASC,id DESC";
                }
            else:

                  $sql="(SELECT id,voucher_no,date,narration,amount,type,creditacnt,bank_id,contra,account_id,'Voucher' AS rawtype FROM voucher WHERE (account_id='".$_POST['accountid']."' OR creditacnt='".$_POST['accountid']."') $condition )
                  UNION
                  (SELECT id,voucher_no,date,narration,amount,type,creditacnt,bank_id,contra,debitacnt AS account_id,'Journal' AS rawtype FROM journalvoucher WHERE (debitacnt='".$_POST['accountid']."' OR creditacnt='".$_POST['accountid']."') $condition ) ORDER BY DATE_FORMAT(date,'%d/%m/%y') ASC,id DESC";
            endif;
        endif;*/

        $ledproc="GetLedgerBalance";
        $condition_j1 = '';
        $condition_v1 = '';
        $condition_j2 = '';
        $condition_v2 = '';
        $condition_j3 = '';
        $condition_v3 = '';

        if($_POST['accountid']!=0)
        {
            $condition_j1.= "AND (debitacnt='".$_POST['accountid']."' OR creditacnt='".$_POST['accountid']."')";
            $condition_v1.= "AND (account_id='".$_POST['accountid']."' OR creditacnt='".$_POST['accountid']."')";
        }
        if($_POST['place']!=0)
        {
            $condition_v2.= " AND place='".$_POST['place']."' ";
            $condition_j2.= " AND project_id='".$_POST['place']."' ";
        }
        if($_POST['projectID']!=0)
        {
            $condition_v3.= " AND project_id='".$_POST['projectID']."' ";
            $condition_j3.= " AND place='".$_POST['projectID']."' ";
        }

        $sql="(SELECT id,voucher_no,project_id,date,narration,amount,type,creditacnt,bank_id,contra,account_id,'Voucher' AS rawtype FROM voucher WHERE $condition $condition_v1 $condition_v2 $condition_v3 )
          UNION
          (SELECT id,voucher_no,project_id,date,narration,amount,type,creditacnt,bank_id,contra,debitacnt AS account_id,'Journal' AS rawtype FROM journalvoucher WHERE $condition $condition_j1 $condition_j2 $condition_j3 ) ORDER BY date ASC,id DESC";

        //echo $sql;exit;
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $dataProvider=$dataReader->readAll();
        $debitamount=0;

        if($_POST['fromdate']!=''):
            $initialdate='2014-03-31';
            $enddate=date('Y-m-d', strtotime($_POST['fromdate'] .' -1 day'));
            //echo $enddate;exit();
            $accounthead=$_POST['accountid'];
            $command = Yii::$app->db->createCommand("CALL ".$ledproc."(:startdate, :currentdate,:place,:accounthead,:project,@outval)");
            $command->bindParam(":startdate",$initialdate);
            $command->bindParam(":currentdate", $enddate);
            /*if($_POST['place']==12):
                $place='NULL';
            else:
                $place=$_POST['place'];
            endif;*/ 
            $place=$_POST['place'];   
            $project=$_POST['projectID'];                    
            $command->bindParam(":place", $place);
            $command->bindParam(":accounthead", $accounthead);
            $command->bindParam(":project", $project);
            $command->query();

            $vcondtn1 = '';
            $jcondtn2 = '';
            $vcondtn3 = '';
            $jcondtn4 = '';
            $credtcondtn = '';
            $debitcondtn = '';
            $accntcondtn = '';

            if($place!=0){
                $vcondtn1.= "place='".$place."' AND ";
                $jcondtn2.= "project_id='".$place."' AND ";
            }

            if($project!=0){
                $vcondtn3.= "project_id='".$project."' AND ";
                $jcondtn4.= "place='".$project."' AND ";
            }

            if($accounthead!=0){
                $credtcondtn.= "creditacnt='".$accounthead."' AND ";
                $debitcondtn.= "debitacnt='".$accounthead."' AND ";
                $accntcondtn.= "account_id='".$accounthead."' AND ";
            }

            $debittotalsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$vcondtn1." ".$vcondtn3." ".$accntcondtn." contra=0 AND date BETWEEN '".$initialdate."' AND '".$enddate."'";
            $command=$connection->createCommand($debittotalsql);
            $dataReader=$command->query();
            $debittotal=$dataReader->read();

            $credittotalsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$vcondtn1." ".$vcondtn3." ".$credtcondtn." contra=0 AND date BETWEEN '".$initialdate."' AND '".$enddate."'";
            $command=$connection->createCommand($credittotalsql);
            $dataReader=$command->query();
            $credittotal=$dataReader->read();

            $contradebitsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$vcondtn1." ".$vcondtn3." ".$accntcondtn." contra=1 AND type='Receipt' AND`date` BETWEEN '".$initialdate."' AND '".$enddate."'";
            $command=$connection->createCommand($contradebitsql);
            $dataReader=$command->query();
            $contradebit=$dataReader->read();

            $contracreditsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$vcondtn1." ".$vcondtn3." ".$credtcondtn." contra=1 AND type='Payment' AND`date` BETWEEN '".$initialdate."' AND '".$enddate."'";
            $command=$connection->createCommand($contracreditsql);
            $dataReader=$command->query();
            $contracredit=$dataReader->read();

            $journeldebitsql = "SELECT SUM(`amount`) as amount FROM `journalvoucher` WHERE ".$jcondtn2." ".$jcondtn4." ".$debitcondtn." date BETWEEN '".$initialdate."' AND '".$enddate."'";
            $command=$connection->createCommand($journeldebitsql);
            $dataReader=$command->query();
            $journeldebit=$dataReader->read();

            $journelcreditsql = "SELECT SUM(`amount`) as amount FROM `journalvoucher` WHERE ".$jcondtn2." ".$jcondtn4." ".$credtcondtn." date BETWEEN '".$initialdate."' AND '".$enddate."'";
            $command=$connection->createCommand($journelcreditsql);
            $dataReader=$command->query();
            $journelcredit=$dataReader->read();

            if(is_null($debittotal['amount'])){
                $debittotalamt = 0;
            }
            else{
                $debittotalamt = $debittotal['amount'];
            }

            if(is_null($credittotal['amount'])){
                $credittotalamt = 0;
            }
            else{
                $credittotalamt = $credittotal['amount'];
            }

            if(is_null($contradebit['amount'])){
                $contradebitamt = 0;
            }
            else{
                $contradebitamt = $contradebit['amount'];
            }

            if(is_null($contracredit['amount'])){
                $contracreditamt = 0;
            }
            else{
                $contracreditamt = $contracredit['amount'];
            }

            if(is_null($journeldebit['amount'])){
                $journeldebitamt = 0;
            }
            else{
                $journeldebitamt = $journeldebit['amount'];
            }

            if(is_null($journelcredit['amount'])){
                $journelcreditamt = 0;
            }
            else{
                $journelcreditamt = $journelcredit['amount'];
            }

            $totalamt = ($credittotalamt + $contracreditamt + $journelcreditamt) - ($debittotalamt + $contradebitamt + $journeldebitamt); 

            //echo $totalamt; exit;

            //print_r($command);exit;
            $sql3="SELECT type,balance FROM ledger_openingbalance WHERE accountid='".$accounthead."' ";
            $command=$connection->createCommand($sql3);
            $dataReader=$command->query();
            $result=$dataReader->read();
            $openingbal=$result['balance'];                        
            //$openbalance = $openingbal + (Yii::$app->db->createCommand("select @outval as result;")->queryScalar());
            $openbalance = $openingbal + $totalamt;
        else:
            $initialdate='2014-03-31';
            if($month=='01' || $month=='02' || $month=='03'):
                $enddate=date("Y",strtotime("-1 year")).'-03-31';
            else:
                $enddate=date('Y').'-03-31';
            endif;
            $enddate=$initialdate;
            //echo $initialdate.'<br>'.$enddate;exit;
            $accounthead=$_POST['accountid'];
            $command = Yii::$app->db->createCommand("CALL ".$ledproc."(:startdate, :currentdate,:place,:accounthead,:project,@outval)");
            $command->bindParam(":startdate",$initialdate);
            $command->bindParam(":currentdate", $enddate);
            /*if($_POST['place']==12):
                $place='NULL';
            else:
                $place=$_POST['place'];
            endif;*/
            $place=$_POST['place'];
            $project=$_POST['projectID'];
            $command->bindParam(":place", $place);
            $command->bindParam(":accounthead", $accounthead);
            $command->bindParam(":project", $project);
            $command->query();
            //print_r($command);exit;
            //$datavouchers=Yii::$app->db->createCommand("select @outval as result;")->queryScalar();
            //if($datavouchers!=0):
                $sql1="SELECT balance FROM ledger_openingbalance WHERE accountid='".$_POST['accountid']."'";
                $command=$connection->createCommand($sql1);
                $dataReader=$command->query();
                $balance=$dataReader->read();
                $openingbal=$balance['balance'];
            //else:
                //$openingbal=0;
            //endif;

            $vcondtn1 = '';
            $jcondtn2 = '';
            $vcondtn3 = '';
            $jcondtn4 = '';
            $credtcondtn = '';
            $debitcondtn = '';
            $accntcondtn = '';

            if($place!=0){
                $vcondtn1.= "place='".$place."' AND ";
                $jcondtn2.= "project_id='".$place."' AND ";
            }

            if($project!=0){
                $vcondtn3.= "project_id='".$project."' AND ";
                $jcondtn4.= "place='".$project."' AND ";
            }

            if($accounthead!=0){
                $credtcondtn.= "creditacnt='".$accounthead."' AND ";
                $debitcondtn.= "debitacnt='".$accounthead."' AND ";
                $accntcondtn.= "account_id='".$accounthead."' AND ";
            }

            $debittotalsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$vcondtn1." ".$vcondtn3." ".$accntcondtn." contra=0 AND date BETWEEN '".$initialdate."' AND '".$enddate."'";
            $command=$connection->createCommand($debittotalsql);
            $dataReader=$command->query();
            $debittotal=$dataReader->read();

            $credittotalsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$vcondtn1." ".$vcondtn3." ".$credtcondtn." contra=0 AND date BETWEEN '".$initialdate."' AND '".$enddate."'";
            $command=$connection->createCommand($credittotalsql);
            $dataReader=$command->query();
            $credittotal=$dataReader->read();

            $contradebitsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$vcondtn1." ".$vcondtn3." ".$accntcondtn." contra=1 AND type='Receipt' AND`date` BETWEEN '".$initialdate."' AND '".$enddate."'";
            $command=$connection->createCommand($contradebitsql);
            $dataReader=$command->query();
            $contradebit=$dataReader->read();

            $contracreditsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$vcondtn1." ".$vcondtn3." ".$credtcondtn." contra=1 AND type='Payment' AND`date` BETWEEN '".$initialdate."' AND '".$enddate."'";
            $command=$connection->createCommand($contracreditsql);
            $dataReader=$command->query();
            $contracredit=$dataReader->read();

            $journeldebitsql = "SELECT SUM(`amount`) as amount FROM `journalvoucher` WHERE ".$jcondtn2." ".$jcondtn4." ".$debitcondtn." date BETWEEN '".$initialdate."' AND '".$enddate."'";
            $command=$connection->createCommand($journeldebitsql);
            $dataReader=$command->query();
            $journeldebit=$dataReader->read();

            $journelcreditsql = "SELECT SUM(`amount`) as amount FROM `journalvoucher` WHERE ".$jcondtn2." ".$jcondtn4." ".$credtcondtn." date BETWEEN '".$initialdate."' AND '".$enddate."'";
            $command=$connection->createCommand($journelcreditsql);
            $dataReader=$command->query();
            $journelcredit=$dataReader->read();

            if(is_null($debittotal['amount'])){
                $debittotalamt = 0;
            }
            else{
                $debittotalamt = $debittotal['amount'];
            }

            if(is_null($credittotal['amount'])){
                $credittotalamt = 0;
            }
            else{
                $credittotalamt = $credittotal['amount'];
            }

            if(is_null($contradebit['amount'])){
                $contradebitamt = 0;
            }
            else{
                $contradebitamt = $contradebit['amount'];
            }

            if(is_null($contracredit['amount'])){
                $contracreditamt = 0;
            }
            else{
                $contracreditamt = $contracredit['amount'];
            }

            if(is_null($journeldebit['amount'])){
                $journeldebitamt = 0;
            }
            else{
                $journeldebitamt = $journeldebit['amount'];
            }

            if(is_null($journelcredit['amount'])){
                $journelcreditamt = 0;
            }
            else{
                $journelcreditamt = $journelcredit['amount'];
            }

            $totalamt = ($credittotalamt + $contracreditamt + $journelcreditamt) - ($debittotalamt + $contradebitamt + $journeldebitamt); 

            //$openbalance = $openingbal + (Yii::$app->db->createCommand("select @outval as result;")->queryScalar());
            $openbalance = $openingbal + $totalamt;
        endif;
        //echo $openbalance;exit;
        //$enddate=date('Y-m-d', strtotime($startdate .' -1 day'));
        $credittotal=0;
        $debittotal=0;

         
        if(count($dataProvider)>0):
            if($openbalance<0):$debittot=abs($openbalance);$debamount=number_format((float)$debittot, 2);$credamount='';else:$debamount='';$credamount=number_format((float)$openbalance, 2);endif;

                $datarows.="<div class='row ledger-book-list'> </div>
                <div class='row ledger_headings sbgrp'>
                                <div class='col-md-10'>
                                    <div class='row'>
                                    <div class='col-md-2' style='display:none;'></div>
                                    <div class='col-md-2'>
                                        <label>Date</label>
                                    </div>
                                    <div class='col-md-3' style='display:none;'>
                                        <label></label>
                                    </div>
                                    <div class='col-md-2'>
                                        <label>Project</label>
                                    </div>
                                    <div class='col-md-4'>
                                        <label>Narration</label>
                                    </div>
                                    <div class='col-md-2 text-right'>
                                        <label>Debit Amount</label>
                                    </div>
                                    <div class='col-md-2 text-right'>
                                        <label>Credit Amount</label>
                                    </div>
                                    
                                    </div>
                                </div>
                                <div class='col-md-2'>
                                </div>

                            </div>";

                $datarows.="
                        <div class='marginrow2 row ledger-book-list closing-balance-receipt-payment'>
                            <div class='col-md-10'>
                                <div class='row'>

                                <div class='col-md-2' style='display:none;'></div>
                                
                                <div class='col-md-3' style='display:none;'></div>
                                <div class='col-md-2'></div>
                                <div class='col-md-2'></div>
                                <div class='col-md-4'>

                                    <label></label>
                                    <span>Opening Balance</span>

                                </div>
                                <div class='col-md-2 text-right'>
                                    <label></label>
                                    <span>".$debamount."</span>

                                </div>
                                <div class='col-md-2 text-right'>
                                    <label></label>
                                    <span>".$credamount."</span>

                                </div>
                            
                                 
                            
                                </div>
                            </div>
                            <div class='col-md-2 icon-groups'>
                                
                            </div>
                        </div>";

            foreach($dataProvider AS $key=>$data):
                $deletebutton="<button type='button' class='deleteledger' value='".$data['id']."' data-type='".$data['rawtype']."' id='deleteledger".$data['id']."' title='Delete Ledger Entry' style='border: none;background: none;'><a href='#'' class='icon-trash1'></a></button>";
                $sql="SELECT Name FROM accounts_item WHERE id='".$data['creditacnt']."'";
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();
                $credit=$dataReader->read();
                $listdate = date('M-d-Y',strtotime($data['date']));
                $prjj=Projects::findOne($data['project_id']);
                if($prjj){
                    if($data['contra']==0):
                        if($data['creditacnt']==$_POST['accountid']):
                            $creditamount=number_format((float)$data['amount'], 2);
                            $debitamount='';
                            $credittotal=$credittotal + $data['amount'];
                        elseif($data['account_id']==$_POST['accountid']):
                            $debitamount=number_format((float)$data['amount'], 2);
                            $creditamount='';
                            $debittotal=$debittotal + $data['amount'];
                        endif;
                        $content=$this->Voucherdetails($data['id'],$data['rawtype']);
                        $datarows.="
                            <div class='marginrow2 row ledger-book-list' style='height: 81px;'>
                                <div class='col-md-10'>
                                    <div class='row'>

                                        <div class='col-md-2 type' style='display:none;'>
                                            <label>Voucher No</label>
                                            <span>".$data['voucher_no']."</span>
                                        </div>

                                        <div class='col-md-2 type'>
                                            
                                            <span class='date'><em class='cal-icon icon-calendar1'></em>".$listdate."</span>
                                        </div>
                                        <div class='col-md-2'><span>".$prjj->Name."</span></div>

                                        <div class='col-md-3 type' style='display:none;'>
                                            <label>Credit Account </label>
                                            <span>".$credit['Name']."</span>
                                        </div>


                                        <div class='col-md-4 type'>
                                           
                                            <div class='hover'>
                                                <span>".$data['narration']."</span>
                                                <div class='tooltiptable' id='tooltip".$data['id']."' style='width:600px;'>
                                                    <table cellpadding='0' cellspacing='0' width='100%'><tr><th>Type</th><th>Narration</th><th>Debit</th><th>Credit</th><th>Amount</th></tr>".$content."</table>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='col-md-2 text-right type'>
                                            
                                            <span>".$debitamount."</span>
                                        </div>
                                        <div class='col-md-2 text-right type'>
                                           
                                            <span>".$creditamount."</span>
                                        </div>
                                        
                                        
                                        
                                
                                    </div>
                                </div>
                                <div class='col-md-2 icon-groups' style='bottom: 17px;'>
                                    ".(($user->superuser==1)?$deletebutton:'')."
                                </div>
                            </div>";
                    else:
                        if($data['type']=='Payment' && $data['creditacnt']==$_POST['accountid'] ):
                            $creditamount=number_format((float)$data['amount'], 2);
                            $debitamount='';
                            $credittotal=$credittotal + $data['amount'];
                            $content=$this->Voucherdetails($data['id'],$data['rawtype']);

                            $datarows.="
                                <div class='marginrow2 row ledger-book-list' style='height: 81px;'>
                                    <div class='col-md-10'>
                                        <div class='row'>
                                            
                                            <div class='col-md-2 type' style='display:none;'>
                                                <label>Voucher No</label>
                                                <span>".$data['voucher_no']."</span>
                                            </div>
                                            <div class='col-md-2 type'>
                                                <!--<label>Date</label>-->
                                                <span class='date'><em class='cal-icon icon-calendar1'></em>".$listdate."</span>
                                            </div>
                                            <div class='col-md-2'><span>".$prjj->Name."</span></div>

                                            <div class='col-md-3 type' style='display:none;'>
                                                <label>Credit Account </label>
                                                <span>".$credit['Name']."</span>
                                            </div>


                                             <div class='col-md-4 type'>
                                                <!--<label>Narration</label>-->
                                                <div class='hover'>
                                                    <span>".$data['narration']."</span>
                                                    <div class='tooltiptable' id='tooltip".$data['id']."' style='width:600px;'>
                                                        <table cellpadding='0' cellspacing='0' width='100%'><tr><th>Type</th><th>Narration</th><th>Debit</th><th>Credit</th><th>Amount</th></tr>".$content."</table>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class='col-md-2 text-right type'>
                                                <!--<label>Debit Amount</label>-->
                                                <span>".$debitamount."</span>
                                            </div>
                                            <div class='col-md-2 text-right type'>
                                                <!--<label>Credit Amount</label>-->
                                                <span>".$creditamount."</span>
                                            </div>
                                            
                                           
                                            
                                    
                                        </div>
                                    </div>
                                    <div class='col-md-2 icon-groups' style='bottom: 17px;'>
                                        ".(($user->superuser==1)?$deletebutton:'')."
                                    </div>
                                </div>";
                        elseif($data['type']=='Receipt' && $data['account_id']==$_POST['accountid']):
                            $debitamount=number_format((float)$data['amount'], 2);
                            $creditamount='';
                            $debittotal=$debittotal + $data['amount'];
                            $content=$this->Voucherdetails($data['id'],$data['rawtype']);

                            $datarows.="
                                <div class='marginrow2 row ledger-book-list' style='height: 81px;'>
                                    <div class='col-md-10'>
                                        <div class='row'>
                                            
                                            <div class='col-md-2 type' style='display:none;'>
                                                <label>Voucher No</label>
                                                <span>".$data['voucher_no']."</span>
                                            </div>
                                            <div class='col-md-2 type'>
                                                <!--<label>Date</label>-->
                                                <span class='date'><em class='cal-icon icon-calendar1'></em>".$listdate."</span>
                                            </div>
                                            <div class='col-md-2'><span>".$prjj->Name."</span></div>

                                            <div class='col-md-3 type' style='display:none;'>
                                                <label>Credit Account </label>
                                                <span>".$credit['Name']."</span>
                                            </div>

                                            
                                              <div class='col-md-4 type'>
                                                <!--<label>Narration</label>-->
                                                <div class='hover'>
                                                    <span>".$data['narration']."</span>
                                                    <div class='tooltiptable' id='tooltip".$data['id']."' style='width:600px;'>
                                                        <table cellpadding='0' cellspacing='0' width='100%'><tr><th>Type</th><th>Narration</th><th>Debit</th><th>Credit</th><th>Amount</th></tr>".$content."</table>
                                                    </div>
                                                </div>
                                            </div>    


                                            
                                            <div class='col-md-2 text-right type'>
                                                <!--<label>Debit Amount</label>-->
                                                <span>".$debitamount."</span>
                                            </div>
                                            <div class='col-md-2 text-right type'>
                                                <!--<label>Credit Amount</label>-->
                                                <span>".$creditamount."</span>
                                            </div>
                                            
                                                                              
                                    
                                        </div>
                                    </div>
                                    <div class='col-md-2 icon-groups' style='bottom: 17px;'>
                                        ".(($user->superuser==1)?$deletebutton:'')."
                                    </div>
                                </div>";
                        endif;
                    endif;
                }
            endforeach;
            if($openbalance<0):
                $debittot=abs($openbalance);
                $debittotal=$debittotal + $debittot;
            else:
                $credtot=abs($openbalance);
                $credittotal=$credittotal + $credtot;
            endif;

            $datarows.="<div class='marginrow2 row ledger-book-list total-receipt-payment'>
                            <div class='col-md-10'>
                                <div class='row'>


                                <div class='col-md-2' style='display:none;'></div>
                                <div class='col-md-3'></div>
                                <div class='col-md-3' style='display:none;'></div>
                                <div class='col-md-5'>

                                    <label></label>
                                    <span>Total</span>

                                </div>
                                <div class='col-md-2 text-right'>
                                    <label></label>
                                    <span>".number_format((float)$debittotal, 2)."</span>

                                </div>
                                <div class='col-md-2 text-right'>
                                    <label></label>
                                    <span>".number_format((float)$credittotal, 2)."</span>

                                </div>
                            
                                    
                            
                                </div>
                            </div>
                            <div class='col-md-2 icon-groups'>
                                
                            </div>
                        </div>";
            $acountbal=$credittotal - $debittotal;
            //$closingbal=$acountbal;
            //$closingbal=$this->getbalance($openbalance,$acountbal);
            if($acountbal<0):
                $debitbal=abs($acountbal);
                $creditbal='';

                $datarows.="
                        <div class='marginrow2 row ledger-book-list closing-balance-receipt-payment'>
                            <div class='col-md-10'>
                                <div class='row'>


                                <div class='col-md-2' style='display:none;'></div>
                                <div class='col-md-3'></div>
                                <div class='col-md-3' style='display:none;'></div>
                                <div class='col-md-5'>

                                    <label></label>
                                    <span>Closing Balance</span>

                                </div>
                                <div class='col-md-2 text-right'>
                                    <label></label>
                                    <span>".number_format((float)$debitbal, 2)."</span>

                                </div>
                                <div class='col-md-2 text-right'>
                                    <label></label>
                                    <span></span>

                                </div>
                            
                                
                            
                                </div>
                            </div>
                            <div class='col-md-2 icon-groups'>
                                
                            </div>
                        </div>";
            else:
                $creditbal=number_format((float)$acountbal, 2);
                $debitbal='';

                $datarows.="
                        <div class='marginrow2 row ledger-book-list closing-balance-receipt-payment'>
                            <div class='col-md-10'>
                                <div class='row'>

                                <div class='col-md-2' style='display:none;'></div>
                                <div class='col-md-3'></div>
                                <div class='col-md-3' style='display:none;'></div>
                                <div class='col-md-5'>

                                    <label></label>
                                    <span>Closing Balance</span>

                                </div>
                                <div class='col-md-2 text-right'>
                                    <label></label>
                                    <span></span>

                                </div>
                                <div class='col-md-2 text-right'>
                                    <label></label>
                                    <span>".$creditbal."</span>

                                </div>
                            
                                    
                            
                                </div>
                            </div>
                            <div class='col-md-2 icon-groups'>
                                
                            </div>
                        </div>";
            endif;
        else:
            if($openbalance<0):$debamount=number_format((float)abs($openbalance), 2);$credamount='';else:$debamount='';$credamount=number_format((float)$openbalance, 2);endif;

            $datarows.="
                        <div class='marginrow2 row ledger-book-list closing-balance-receipt-payment'>
                            <div class='col-md-10'>
                                <div class='row'>
                            
                                    <div class='col-md-8 type text-right'>
                                        <label></label>
                                        <span>Opening Balance</span>
                                    </div>
                                        
                                    <div class='col-md-2 text-right type'>
                                        <label></label>
                                        <span>".$debamount."</span>
                                    </div>
                                    <div class='col-md-2 text-right type'>
                                        <label></label>
                                        <span>".$credamount."</span>
                                    </div>
                            
                                </div>
                            </div>
                            <div class='col-md-2 icon-groups'>
                                
                            </div>
                        </div>";

            if($debamount==''):
                $debittotal=number_format((float)$debamount, 2);
            else:
                $debittotal=$debamount;
            endif;
            if($credamount==''):
                $credittotal=number_format((float)$credamount, 2);
            else:
                $credittotal=$credamount;
            endif;

            /*$datarows.="
                        <div class='row ledger-book-list total-receipt-payment'>
                            <div class='col-md-10'>
                                <div class='row'>
                            
                                    <div class='col-md-8 type'>
                                        <label></label>
                                        <span>Total</span>
                                    </div>
                                    
                                    <div class='col-md-2 text-right type'>
                                        <label></label>
                                        <span>".number_format((float)$debittotal, 2)."</span>
                                    </div>
                                    <div class='col-md-2 text-right type'>
                                        <label></label>
                                        <span>".number_format((float)$credittotal, 2)."</span>
                                    </div>
                            
                                </div>
                            </div>
                            <div class='col-md-2 icon-groups'>
                                
                            </div>
                        </div>";*/

            $datarows.="<div class='marginrow2 row ledger-book-list' style='text-align: center;'>No Ledger Entries Found</div>";

            $datarows.="
                        <div class='marginrow2 row ledger-book-list closing-balance-receipt-payment'>
                            <div class='col-md-10'>
                                <div class='row'>
                            
                                    <div class='col-md-8 type text-right'>
                                        <label></label>
                                        <span>Closing Balance</span>
                                    </div>
                                        
                                    <div class='col-md-2 text-right type'>
                                        <label></label>
                                        <span>".$debamount."</span>
                                    </div>
                                    <div class='col-md-2 text-right type'>
                                        <label></label>
                                        <span>".$credamount."</span>
                                    </div>
                            
                                </div>
                            </div>
                            <div class='col-md-2 icon-groups'>
                                
                            </div>
                        </div>";

        endif;
        $arr = array('result' => $datarows,'error'=>'No');
        return json_encode($arr);
    }

    public function actionLedgerexcel()
    {
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=ledger.xls");

        $_POST['accountid'] = $_GET['Accounthead'];
        $_POST['place'] = $_GET['Place'];
        $_POST['projectID'] = $_GET['Project'];

        if(isset($_GET['startdate'])){
            $_POST['fromdate'] = $_GET['startdate'];
        }
        else{
            $_POST['fromdate'] = '';
        }

        if(isset($_GET['enddate'])){
            $_POST['todate'] = $_GET['enddate'];
        }
        else{
            $_POST['todate'] = '';
        }

        $connection =  \Yii::$app->db;
        $userid = Yii::$app->user->Id; 
        $user = User::find()->where(['id'=>$userid])->one();
        $startdate=date('Y-m-d',strtotime($_POST['fromdate']));
        $enddate=date('Y-m-d',strtotime($_POST['todate']));
        $datarows='';
        $condition='';

        $sql="SELECT Name FROM accounts_item WHERE id='".$_POST['accountid']."'";
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $value=$dataReader->read();
        $month=date('m');
        if($month=='01' || $month=='02' || $month=='03'):
            $initialdate=date("Y",strtotime("-1 year")).'-04-01';
        else:
            $initialdate=date('Y').'-04-01';
        endif;

        $fromdate = date('M-d-Y',strtotime($startdate));
        $todate = date('M-d-Y',strtotime($enddate));

        if($_POST['fromdate']!='' || $_POST['todate']!=''):
            if($_POST['todate']==''){
                $enddate = date('Y-m-d');
                $todate = '';
            }
            if($_POST['fromdate']==''){
                $startdate = '2014-03-31';
                $fromdate = '';
            }
            $condition.=" date BETWEEN '$startdate' AND '$enddate'";
        else:
            $initialdate='2014-03-31';
            $condition.=" date >= '$initialdate'";
        endif;

        $sql="SELECT projectid FROM user_projects WHERE userid='".$userid."'";
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $userprojs=$dataReader->readAll();
        $projarr=array();
        foreach($userprojs AS $userproj):
            array_push($projarr,$userproj['projectid']);
        endforeach;
        $var=array_search("12",$projarr);

        $ledproc="GetLedgerBalance";
        $condition_j1 = '';
        $condition_v1 = '';
        $condition_j2 = '';
        $condition_v2 = '';
        $condition_j3 = '';
        $condition_v3 = '';

        if($_POST['accountid']!=0)
        {
            $condition_j1.= "AND (debitacnt='".$_POST['accountid']."' OR creditacnt='".$_POST['accountid']."')";
            $condition_v1.= "AND (account_id='".$_POST['accountid']."' OR creditacnt='".$_POST['accountid']."')";
        }
        if($_POST['place']!=0)
        {
            $condition_v2.= " AND place='".$_POST['place']."' ";
            $condition_j2.= " AND project_id='".$_POST['place']."' ";
        }
        if($_POST['projectID']!=0)
        {
            $condition_v3.= " AND project_id='".$_POST['projectID']."' ";
            $condition_j3.= " AND place='".$_POST['projectID']."' ";
        }

        $sql="(SELECT id,voucher_no,date,narration,amount,type,creditacnt,bank_id,contra,account_id,'Voucher' AS rawtype FROM voucher WHERE $condition $condition_v1 $condition_v2 $condition_v3 )
          UNION
          (SELECT id,voucher_no,date,narration,amount,type,creditacnt,bank_id,contra,debitacnt AS account_id,'Journal' AS rawtype FROM journalvoucher WHERE $condition $condition_j1 $condition_j2 $condition_j3 ) ORDER BY date ASC,id DESC";

        //echo $sql;exit;
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $dataProvider=$dataReader->readAll();
        $debitamount=0;

        if($_POST['fromdate']!=''):
            $initialdate='2014-03-31';
            $enddate=date('Y-m-d', strtotime($_POST['fromdate'] .' -1 day'));
            //echo $enddate;exit();
            $accounthead=$_POST['accountid'];
            $command = Yii::$app->db->createCommand("CALL ".$ledproc."(:startdate, :currentdate,:place,:accounthead,:project,@outval)");
            $command->bindParam(":startdate",$initialdate);
            $command->bindParam(":currentdate", $enddate); 
            $place=$_POST['place'];   
            $project=$_POST['projectID'];                    
            $command->bindParam(":place", $place);
            $command->bindParam(":accounthead", $accounthead);
            $command->bindParam(":project", $project);
            $command->query();

            $vcondtn1 = '';
            $jcondtn2 = '';
            $vcondtn3 = '';
            $jcondtn4 = '';
            $credtcondtn = '';
            $debitcondtn = '';
            $accntcondtn = '';

            if($place!=0){
                $vcondtn1.= "place='".$place."' AND ";
                $jcondtn2.= "project_id='".$place."' AND ";
            }

            if($project!=0){
                $vcondtn3.= "project_id='".$project."' AND ";
                $jcondtn4.= "place='".$project."' AND ";
            }

            if($accounthead!=0){
                $credtcondtn.= "creditacnt='".$accounthead."' AND ";
                $debitcondtn.= "debitacnt='".$accounthead."' AND ";
                $accntcondtn.= "account_id='".$accounthead."' AND ";
            }

            $debittotalsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$vcondtn1." ".$vcondtn3." ".$accntcondtn." contra=0 AND date BETWEEN '".$initialdate."' AND '".$enddate."'";
            $command=$connection->createCommand($debittotalsql);
            $dataReader=$command->query();
            $debittotal=$dataReader->read();

            $credittotalsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$vcondtn1." ".$vcondtn3." ".$credtcondtn." contra=0 AND date BETWEEN '".$initialdate."' AND '".$enddate."'";
            $command=$connection->createCommand($credittotalsql);
            $dataReader=$command->query();
            $credittotal=$dataReader->read();

            $contradebitsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$vcondtn1." ".$vcondtn3." ".$accntcondtn." contra=1 AND type='Receipt' AND`date` BETWEEN '".$initialdate."' AND '".$enddate."'";
            $command=$connection->createCommand($contradebitsql);
            $dataReader=$command->query();
            $contradebit=$dataReader->read();

            $contracreditsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$vcondtn1." ".$vcondtn3." ".$credtcondtn." contra=1 AND type='Payment' AND`date` BETWEEN '".$initialdate."' AND '".$enddate."'";
            $command=$connection->createCommand($contracreditsql);
            $dataReader=$command->query();
            $contracredit=$dataReader->read();

            $journeldebitsql = "SELECT SUM(`amount`) as amount FROM `journalvoucher` WHERE ".$jcondtn2." ".$jcondtn4." ".$debitcondtn." date BETWEEN '".$initialdate."' AND '".$enddate."'";
            $command=$connection->createCommand($journeldebitsql);
            $dataReader=$command->query();
            $journeldebit=$dataReader->read();

            $journelcreditsql = "SELECT SUM(`amount`) as amount FROM `journalvoucher` WHERE ".$jcondtn2." ".$jcondtn4." ".$credtcondtn." date BETWEEN '".$initialdate."' AND '".$enddate."'";
            $command=$connection->createCommand($journelcreditsql);
            $dataReader=$command->query();
            $journelcredit=$dataReader->read();

            if(is_null($debittotal['amount'])){
                $debittotalamt = 0;
            }
            else{
                $debittotalamt = $debittotal['amount'];
            }

            if(is_null($credittotal['amount'])){
                $credittotalamt = 0;
            }
            else{
                $credittotalamt = $credittotal['amount'];
            }

            if(is_null($contradebit['amount'])){
                $contradebitamt = 0;
            }
            else{
                $contradebitamt = $contradebit['amount'];
            }

            if(is_null($contracredit['amount'])){
                $contracreditamt = 0;
            }
            else{
                $contracreditamt = $contracredit['amount'];
            }

            if(is_null($journeldebit['amount'])){
                $journeldebitamt = 0;
            }
            else{
                $journeldebitamt = $journeldebit['amount'];
            }

            if(is_null($journelcredit['amount'])){
                $journelcreditamt = 0;
            }
            else{
                $journelcreditamt = $journelcredit['amount'];
            }

            $totalamt = ($credittotalamt + $contracreditamt + $journelcreditamt) - ($debittotalamt + $contradebitamt + $journeldebitamt); 

            $sql3="SELECT type,balance FROM ledger_openingbalance WHERE accountid='".$accounthead."' ";
            $command=$connection->createCommand($sql3);
            $dataReader=$command->query();
            $result=$dataReader->read();
            $openingbal=$result['balance'];                        
            $openbalance = $openingbal + $totalamt;
        else:
            $initialdate='2014-03-31';
            if($month=='01' || $month=='02' || $month=='03'):
                $enddate=date("Y",strtotime("-1 year")).'-03-31';
            else:
                $enddate=date('Y').'-03-31';
            endif;
            $enddate=$initialdate;
            $accounthead=$_POST['accountid'];
            $command = Yii::$app->db->createCommand("CALL ".$ledproc."(:startdate, :currentdate,:place,:accounthead,:project,@outval)");
            $command->bindParam(":startdate",$initialdate);
            $command->bindParam(":currentdate", $enddate);
            $place=$_POST['place'];
            $project=$_POST['projectID'];
            $command->bindParam(":place", $place);
            $command->bindParam(":accounthead", $accounthead);
            $command->bindParam(":project", $project);
            $command->query();
            $sql1="SELECT balance FROM ledger_openingbalance WHERE accountid='".$_POST['accountid']."'";
            $command=$connection->createCommand($sql1);
            $dataReader=$command->query();
            $balance=$dataReader->read();
            $openingbal=$balance['balance'];

            $vcondtn1 = '';
            $jcondtn2 = '';
            $vcondtn3 = '';
            $jcondtn4 = '';
            $credtcondtn = '';
            $debitcondtn = '';
            $accntcondtn = '';

            if($place!=0){
                $vcondtn1.= "place='".$place."' AND ";
                $jcondtn2.= "project_id='".$place."' AND ";
            }

            if($project!=0){
                $vcondtn3.= "project_id='".$project."' AND ";
                $jcondtn4.= "place='".$project."' AND ";
            }

            if($accounthead!=0){
                $credtcondtn.= "creditacnt='".$accounthead."' AND ";
                $debitcondtn.= "debitacnt='".$accounthead."' AND ";
                $accntcondtn.= "account_id='".$accounthead."' AND ";
            }

            $debittotalsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$vcondtn1." ".$vcondtn3." ".$accntcondtn." contra=0 AND date BETWEEN '".$initialdate."' AND '".$enddate."'";
            $command=$connection->createCommand($debittotalsql);
            $dataReader=$command->query();
            $debittotal=$dataReader->read();

            $credittotalsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$vcondtn1." ".$vcondtn3." ".$credtcondtn." contra=0 AND date BETWEEN '".$initialdate."' AND '".$enddate."'";
            $command=$connection->createCommand($credittotalsql);
            $dataReader=$command->query();
            $credittotal=$dataReader->read();

            $contradebitsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$vcondtn1." ".$vcondtn3." ".$accntcondtn." contra=1 AND type='Receipt' AND`date` BETWEEN '".$initialdate."' AND '".$enddate."'";
            $command=$connection->createCommand($contradebitsql);
            $dataReader=$command->query();
            $contradebit=$dataReader->read();

            $contracreditsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$vcondtn1." ".$vcondtn3." ".$credtcondtn." contra=1 AND type='Payment' AND`date` BETWEEN '".$initialdate."' AND '".$enddate."'";
            $command=$connection->createCommand($contracreditsql);
            $dataReader=$command->query();
            $contracredit=$dataReader->read();

            $journeldebitsql = "SELECT SUM(`amount`) as amount FROM `journalvoucher` WHERE ".$jcondtn2." ".$jcondtn4." ".$debitcondtn." date BETWEEN '".$initialdate."' AND '".$enddate."'";
            $command=$connection->createCommand($journeldebitsql);
            $dataReader=$command->query();
            $journeldebit=$dataReader->read();

            $journelcreditsql = "SELECT SUM(`amount`) as amount FROM `journalvoucher` WHERE ".$jcondtn2." ".$jcondtn4." ".$credtcondtn." date BETWEEN '".$initialdate."' AND '".$enddate."'";
            $command=$connection->createCommand($journelcreditsql);
            $dataReader=$command->query();
            $journelcredit=$dataReader->read();

            if(is_null($debittotal['amount'])){
                $debittotalamt = 0;
            }
            else{
                $debittotalamt = $debittotal['amount'];
            }

            if(is_null($credittotal['amount'])){
                $credittotalamt = 0;
            }
            else{
                $credittotalamt = $credittotal['amount'];
            }

            if(is_null($contradebit['amount'])){
                $contradebitamt = 0;
            }
            else{
                $contradebitamt = $contradebit['amount'];
            }

            if(is_null($contracredit['amount'])){
                $contracreditamt = 0;
            }
            else{
                $contracreditamt = $contracredit['amount'];
            }

            if(is_null($journeldebit['amount'])){
                $journeldebitamt = 0;
            }
            else{
                $journeldebitamt = $journeldebit['amount'];
            }

            if(is_null($journelcredit['amount'])){
                $journelcreditamt = 0;
            }
            else{
                $journelcreditamt = $journelcredit['amount'];
            }

            $totalamt = ($credittotalamt + $contracreditamt + $journelcreditamt) - ($debittotalamt + $contradebitamt + $journeldebitamt); 

            $openbalance = $openingbal + $totalamt;
        endif;

        $credittotal=0;
        $debittotal=0;

        $datarows.="<table style='width:100%'' border='1'>
                                <tr>
                                    <th>Date</th>
                                    <th>Narration</th> 
                                    <th>Debit Amount</th>
                                    <th>Credit Amount</th>
                                </tr>";

         
        if(count($dataProvider)>0):
            if($openbalance<0):$debittot=abs($openbalance);$debamount=number_format((float)$debittot, 2);$credamount='';else:$debamount='';$credamount=number_format((float)$openbalance, 2);endif;

                    $datarows.="<tr>
                                    <td colspan='2'>Opening Balance</td>
                                    <td>".$debamount."</td> 
                                    <td>".$credamount."</td>
                                </tr>";

            foreach($dataProvider AS $key=>$data):
                $sql="SELECT Name FROM accounts_item WHERE id='".$data['creditacnt']."'";
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();
                $credit=$dataReader->read();
                $listdate = date('M-d-Y',strtotime($data['date']));
                if($data['contra']==0):
                    if($data['creditacnt']==$_POST['accountid']):
                        $creditamount=number_format((float)$data['amount'], 2);
                        $debitamount='';
                        $credittotal=$credittotal + $data['amount'];
                    elseif($data['account_id']==$_POST['accountid']):
                        $debitamount=number_format((float)$data['amount'], 2);
                        $creditamount='';
                        $debittotal=$debittotal + $data['amount'];
                    endif;
                    $content=$this->Voucherdetails($data['id'],$data['rawtype']);
                    $datarows.="<tr>
                                    <td>".$listdate."</td> 
                                    <td>".$data['narration']."</td> 
                                    <td>".$debitamount."</td> 
                                    <td>".$creditamount."</td>
                                </tr>";
                else:
                    if($data['type']=='Payment' && $data['creditacnt']==$_POST['accountid'] ):
                        $creditamount=number_format((float)$data['amount'], 2);
                        $debitamount='';
                        $credittotal=$credittotal + $data['amount'];
                        $content=$this->Voucherdetails($data['id'],$data['rawtype']);

                        $datarows.="<tr>
                                        <td>".$listdate."</td> 
                                        <td>".$data['narration']."</td> 
                                        <td>".$debitamount."</td> 
                                        <td>".$creditamount."</td>
                                    </tr>";
                    elseif($data['type']=='Receipt' && $data['account_id']==$_POST['accountid']):
                        $debitamount=number_format((float)$data['amount'], 2);
                        $creditamount='';
                        $debittotal=$debittotal + $data['amount'];
                        $content=$this->Voucherdetails($data['id'],$data['rawtype']);

                        $datarows.="<tr>
                                        <td>".$listdate."</td> 
                                        <td>".$data['narration']."</td> 
                                        <td>".$debitamount."</td> 
                                        <td>".$creditamount."</td>
                                    </tr>";
                    endif;
                endif;
            endforeach;
            if($openbalance<0):
                $debittot=abs($openbalance);
                $debittotal=$debittotal + $debittot;
            else:
                $credtot=abs($openbalance);
                $credittotal=$credittotal + $credtot;
            endif;

            $datarows.="<tr>
                            <td></td> 
                            <td>Total</td> 
                            <td>".number_format((float)$debittotal, 2)."</td> 
                            <td>".number_format((float)$credittotal, 2)."</td> 
                        </tr>";
            $acountbal=$credittotal - $debittotal;
            //$closingbal=$acountbal;
            //$closingbal=$this->getbalance($openbalance,$acountbal);
            if($acountbal<0):
                $debitbal=abs($acountbal);
                $creditbal='';

                $datarows.="<tr>
                                <td colspan='2'>Closing Balance</td>
                                <td>".number_format((float)$debitbal, 2)."</td> 
                                <td></td>
                            </tr>";
            else:
                $creditbal=number_format((float)$acountbal, 2);
                $debitbal='';

                $datarows.="<tr>
                                <td colspan='2'>Closing Balance</td>
                                <td></td> 
                                <td>".$creditbal."</td>
                            </tr>";
            endif;
        else:
            if($openbalance<0):$debamount=number_format((float)abs($openbalance), 2);$credamount='';else:$debamount='';$credamount=number_format((float)$openbalance, 2);endif;

            $datarows.="<tr>
                            <td colspan='2'>Opening Balance</td>
                            <td>".$debamount."</td> 
                            <td>".$credamount."</td>
                        </tr>";

            if($debamount==''):
                $debittotal=number_format((float)$debamount, 2);
            else:
                $debittotal=$debamount;
            endif;
            if($credamount==''):
                $credittotal=number_format((float)$credamount, 2);
            else:
                $credittotal=$credamount;
            endif;

            $datarows.="<tr>
                            <td colspan='2'>Closing Balance</td>
                            <td>".$debamount."</td> 
                            <td>".$credamount."</td>
                        </tr>";

        endif;

        $datarows.="</table>";

        return $datarows;
    }
    public function actionPrintledger()
    {
        $_POST['place']=$_GET['Place'];
        $_POST['projectID']=$_GET['Project'];
        $_POST['accountid']=$_GET['Accounthead'];

        if(isset($_GET['startdate'])){
            $_POST['fromdate'] = $_GET['startdate'];
        }
        else{
            $_POST['fromdate'] = '';
        }

        if(isset($_GET['enddate'])){
            $_POST['todate'] = $_GET['enddate'];
        }
        else{
            $_POST['todate'] = '';
        }


        $connection =  \Yii::$app->db;
        $userid = Yii::$app->user->Id; 
        $user = User::find()->where(['id'=>$userid])->one();
        $startdate=date('Y-m-d',strtotime($_POST['fromdate']));
        $enddate=date('Y-m-d',strtotime($_POST['todate']));
        $datarows='';
        $condition='';

        $sql="SELECT Name FROM accounts_item WHERE id='".$_POST['accountid']."'";
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $value=$dataReader->read();
        $month=date('m');
        if($month=='01' || $month=='02' || $month=='03'):
            $initialdate=date("Y",strtotime("-1 year")).'-04-01';
        else:
            $initialdate=date('Y').'-04-01';
        endif;

        $fromdate = date('M-d-Y',strtotime($startdate));
        $todate = date('M-d-Y',strtotime($enddate));

        if($_POST['fromdate']!='' || $_POST['todate']!=''):
            if($_POST['todate']==''){
                $enddate = date('Y-m-d');
                $todate = '';
            }
            if($_POST['fromdate']==''){
                $startdate = '2014-03-31';
                $fromdate = '';
            }
            $condition.=" date BETWEEN '$startdate' AND '$enddate'";
        else:
            $initialdate='2014-03-31';
            $condition.=" date >= '$initialdate'";
        endif;

        $sql="SELECT projectid FROM user_projects WHERE userid='".$userid."'";
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $userprojs=$dataReader->readAll();
        $projarr=array();
        foreach($userprojs AS $userproj):
            array_push($projarr,$userproj['projectid']);
        endforeach;
        $var=array_search("12",$projarr);

        $ledproc="GetLedgerBalance";
        $condition_j1 = '';
        $condition_v1 = '';
        $condition_j2 = '';
        $condition_v2 = '';
        $condition_j3 = '';
        $condition_v3 = '';



        if($_POST['accountid']!=0)
        {
            $condition_j1.= "AND (debitacnt='".$_POST['accountid']."' OR creditacnt='".$_POST['accountid']."')";
            $condition_v1.= "AND (account_id='".$_POST['accountid']."' OR creditacnt='".$_POST['accountid']."')";
        }
        if($_POST['place']!=0)
        {
            $condition_v2.= " AND place='".$_POST['place']."' ";
            $condition_j2.= " AND project_id='".$_POST['place']."' ";
        }
        if($_POST['projectID']!=0)
        {
            $condition_v3.= " AND project_id='".$_POST['projectID']."' ";
            $condition_j3.= " AND place='".$_POST['projectID']."' ";
        }

        $sql="(SELECT id,voucher_no,date,narration,amount,type,creditacnt,bank_id,contra,account_id,'Voucher' AS rawtype FROM voucher WHERE $condition $condition_v1 $condition_v2 $condition_v3 )
          UNION
          (SELECT id,voucher_no,date,narration,amount,type,creditacnt,bank_id,contra,debitacnt AS account_id,'Journal' AS rawtype FROM journalvoucher WHERE $condition $condition_j1 $condition_j2 $condition_j3 ) ORDER BY date ASC,id DESC";

        //echo $sql;exit;
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $dataProvider=$dataReader->readAll();
        $debitamount=0;

        if($_POST['fromdate']!=''):
            $initialdate='2014-03-31';
            $enddate=date('Y-m-d', strtotime($_POST['fromdate'] .' -1 day'));
            //echo $enddate;exit();
            $accounthead=$_POST['accountid'];
            $command = Yii::$app->db->createCommand("CALL ".$ledproc."(:startdate, :currentdate,:place,:accounthead,:project,@outval)");
            $command->bindParam(":startdate",$initialdate);
            $command->bindParam(":currentdate", $enddate); 
            $place=$_POST['place'];   
            $project=$_POST['projectID'];                    
            $command->bindParam(":place", $place);
            $command->bindParam(":accounthead", $accounthead);
            $command->bindParam(":project", $project);
            $command->query();

            $vcondtn1 = '';
            $jcondtn2 = '';
            $vcondtn3 = '';
            $jcondtn4 = '';
            $credtcondtn = '';
            $debitcondtn = '';
            $accntcondtn = '';

            if($place!=0){
                $vcondtn1.= "place='".$place."' AND ";
                $jcondtn2.= "project_id='".$place."' AND ";
            }

            if($project!=0){
                $vcondtn3.= "project_id='".$project."' AND ";
                $jcondtn4.= "place='".$project."' AND ";
            }

            if($accounthead!=0){
                $credtcondtn.= "creditacnt='".$accounthead."' AND ";
                $debitcondtn.= "debitacnt='".$accounthead."' AND ";
                $accntcondtn.= "account_id='".$accounthead."' AND ";
            }

            $debittotalsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$vcondtn1." ".$vcondtn3." ".$accntcondtn." contra=0 AND date BETWEEN '".$initialdate."' AND '".$enddate."'";
            $command=$connection->createCommand($debittotalsql);
            $dataReader=$command->query();
            $debittotal=$dataReader->read();

            $credittotalsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$vcondtn1." ".$vcondtn3." ".$credtcondtn." contra=0 AND date BETWEEN '".$initialdate."' AND '".$enddate."'";
            $command=$connection->createCommand($credittotalsql);
            $dataReader=$command->query();
            $credittotal=$dataReader->read();

            $contradebitsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$vcondtn1." ".$vcondtn3." ".$accntcondtn." contra=1 AND type='Receipt' AND`date` BETWEEN '".$initialdate."' AND '".$enddate."'";
            $command=$connection->createCommand($contradebitsql);
            $dataReader=$command->query();
            $contradebit=$dataReader->read();

            $contracreditsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$vcondtn1." ".$vcondtn3." ".$credtcondtn." contra=1 AND type='Payment' AND`date` BETWEEN '".$initialdate."' AND '".$enddate."'";
            $command=$connection->createCommand($contracreditsql);
            $dataReader=$command->query();
            $contracredit=$dataReader->read();

            $journeldebitsql = "SELECT SUM(`amount`) as amount FROM `journalvoucher` WHERE ".$jcondtn2." ".$jcondtn4." ".$debitcondtn." date BETWEEN '".$initialdate."' AND '".$enddate."'";
            $command=$connection->createCommand($journeldebitsql);
            $dataReader=$command->query();
            $journeldebit=$dataReader->read();

            $journelcreditsql = "SELECT SUM(`amount`) as amount FROM `journalvoucher` WHERE ".$jcondtn2." ".$jcondtn4." ".$credtcondtn." date BETWEEN '".$initialdate."' AND '".$enddate."'";
            $command=$connection->createCommand($journelcreditsql);
            $dataReader=$command->query();
            $journelcredit=$dataReader->read();

            if(is_null($debittotal['amount'])){
                $debittotalamt = 0;
            }
            else{
                $debittotalamt = $debittotal['amount'];
            }

            if(is_null($credittotal['amount'])){
                $credittotalamt = 0;
            }
            else{
                $credittotalamt = $credittotal['amount'];
            }

            if(is_null($contradebit['amount'])){
                $contradebitamt = 0;
            }
            else{
                $contradebitamt = $contradebit['amount'];
            }

            if(is_null($contracredit['amount'])){
                $contracreditamt = 0;
            }
            else{
                $contracreditamt = $contracredit['amount'];
            }

            if(is_null($journeldebit['amount'])){
                $journeldebitamt = 0;
            }
            else{
                $journeldebitamt = $journeldebit['amount'];
            }

            if(is_null($journelcredit['amount'])){
                $journelcreditamt = 0;
            }
            else{
                $journelcreditamt = $journelcredit['amount'];
            }

            $totalamt = ($credittotalamt + $contracreditamt + $journelcreditamt) - ($debittotalamt + $contradebitamt + $journeldebitamt); 

            $sql3="SELECT type,balance FROM ledger_openingbalance WHERE accountid='".$accounthead."' ";
            $command=$connection->createCommand($sql3);
            $dataReader=$command->query();
            $result=$dataReader->read();
            $openingbal=$result['balance'];                        
            $openbalance = $openingbal + $totalamt;
        else:
            $initialdate='2014-03-31';
            if($month=='01' || $month=='02' || $month=='03'):
                $enddate=date("Y",strtotime("-1 year")).'-03-31';
            else:
                $enddate=date('Y').'-03-31';
            endif;
            $enddate=$initialdate;
            $accounthead=$_POST['accountid'];
            $command = Yii::$app->db->createCommand("CALL ".$ledproc."(:startdate, :currentdate,:place,:accounthead,:project,@outval)");
            $command->bindParam(":startdate",$initialdate);
            $command->bindParam(":currentdate", $enddate);
            $place=$_POST['place'];
            $project=$_POST['projectID'];
            $command->bindParam(":place", $place);
            $command->bindParam(":accounthead", $accounthead);
            $command->bindParam(":project", $project);
            $command->query();
            $sql1="SELECT balance FROM ledger_openingbalance WHERE accountid='".$_POST['accountid']."'";
            $command=$connection->createCommand($sql1);
            $dataReader=$command->query();
            $balance=$dataReader->read();
            $openingbal=$balance['balance'];

            $vcondtn1 = '';
            $jcondtn2 = '';
            $vcondtn3 = '';
            $jcondtn4 = '';
            $credtcondtn = '';
            $debitcondtn = '';
            $accntcondtn = '';

            if($place!=0){
                $vcondtn1.= "place='".$place."' AND ";
                $jcondtn2.= "project_id='".$place."' AND ";
            }

            if($project!=0){
                $vcondtn3.= "project_id='".$project."' AND ";
                $jcondtn4.= "place='".$project."' AND ";
            }

            if($accounthead!=0){
                $credtcondtn.= "creditacnt='".$accounthead."' AND ";
                $debitcondtn.= "debitacnt='".$accounthead."' AND ";
                $accntcondtn.= "account_id='".$accounthead."' AND ";
            }

            $debittotalsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$vcondtn1." ".$vcondtn3." ".$accntcondtn." contra=0 AND date BETWEEN '".$initialdate."' AND '".$enddate."'";
            $command=$connection->createCommand($debittotalsql);
            $dataReader=$command->query();
            $debittotal=$dataReader->read();

            $credittotalsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$vcondtn1." ".$vcondtn3." ".$credtcondtn." contra=0 AND date BETWEEN '".$initialdate."' AND '".$enddate."'";
            $command=$connection->createCommand($credittotalsql);
            $dataReader=$command->query();
            $credittotal=$dataReader->read();

            $contradebitsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$vcondtn1." ".$vcondtn3." ".$accntcondtn." contra=1 AND type='Receipt' AND`date` BETWEEN '".$initialdate."' AND '".$enddate."'";
            $command=$connection->createCommand($contradebitsql);
            $dataReader=$command->query();
            $contradebit=$dataReader->read();

            $contracreditsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$vcondtn1." ".$vcondtn3." ".$credtcondtn." contra=1 AND type='Payment' AND`date` BETWEEN '".$initialdate."' AND '".$enddate."'";
            $command=$connection->createCommand($contracreditsql);
            $dataReader=$command->query();
            $contracredit=$dataReader->read();

            $journeldebitsql = "SELECT SUM(`amount`) as amount FROM `journalvoucher` WHERE ".$jcondtn2." ".$jcondtn4." ".$debitcondtn." date BETWEEN '".$initialdate."' AND '".$enddate."'";
            $command=$connection->createCommand($journeldebitsql);
            $dataReader=$command->query();
            $journeldebit=$dataReader->read();

            $journelcreditsql = "SELECT SUM(`amount`) as amount FROM `journalvoucher` WHERE ".$jcondtn2." ".$jcondtn4." ".$credtcondtn." date BETWEEN '".$initialdate."' AND '".$enddate."'";
            $command=$connection->createCommand($journelcreditsql);
            $dataReader=$command->query();
            $journelcredit=$dataReader->read();

            if(is_null($debittotal['amount'])){
                $debittotalamt = 0;
            }
            else{
                $debittotalamt = $debittotal['amount'];
            }

            if(is_null($credittotal['amount'])){
                $credittotalamt = 0;
            }
            else{
                $credittotalamt = $credittotal['amount'];
            }

            if(is_null($contradebit['amount'])){
                $contradebitamt = 0;
            }
            else{
                $contradebitamt = $contradebit['amount'];
            }

            if(is_null($contracredit['amount'])){
                $contracreditamt = 0;
            }
            else{
                $contracreditamt = $contracredit['amount'];
            }

            if(is_null($journeldebit['amount'])){
                $journeldebitamt = 0;
            }
            else{
                $journeldebitamt = $journeldebit['amount'];
            }

            if(is_null($journelcredit['amount'])){
                $journelcreditamt = 0;
            }
            else{
                $journelcreditamt = $journelcredit['amount'];
            }

            $totalamt = ($credittotalamt + $contracreditamt + $journelcreditamt) - ($debittotalamt + $contradebitamt + $journeldebitamt); 

            $openbalance = $openingbal + $totalamt;
        endif;

        $datarows.="<style>table,td{border:1px solid black;border-collapse:collapse}</style>
        <table cellpadding='7px' class='table table-bordered' id='ledgertable' style='display: table; overflow: hidden;font-size: 8pt;'>
        <thead><tr>
                <th>Voucher No</th>
                <th>Date</th>
                <th>Narration</th>
                <th>Debit Account</th>
                <th>Debit Amount</th>
                <th>Credit Amount</th>
            </tr>
        </thead><tbody id='ledgeritems'>";
        $credittotal=0;
        $debittotal=0;
        if(count($dataProvider)>0):
            if($openbalance<0):$debittot=abs($openbalance);$debamount=number_format((float)$debittot, 2);$credamount='';else:$debamount='';$credamount=number_format((float)$openbalance, 2);endif;
            $datarows.="<tr><td colspan='4' style='text-align: center;'>Opening Balance</td><td >$debamount</td><td>$credamount</td></tr>";


            foreach($dataProvider AS $key=>$data):
                $sql="SELECT Name FROM accounts_item WHERE id='".$data['creditacnt']."'";
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();
                $credit=$dataReader->read();
                $listdate = date('M-d-Y',strtotime($data['date']));

                if($data['contra']==0):
                    if($data['creditacnt']==$_POST['accountid']):
                        $creditamount=number_format((float)$data['amount'], 2);
                        $debitamount='';
                        $credittotal=$credittotal + $data['amount'];
                    elseif($data['account_id']==$_POST['accountid']):
                        $debitamount=number_format((float)$data['amount'], 2);
                        $creditamount='';
                        $debittotal=$debittotal + $data['amount'];
                    endif;
                    $datarows.="<tr>
                            <td>".$data['voucher_no']."</td>
                            <td>".$data['date']."</td>
                            <td>".$data['narration']."</td>
                            <td>".$credit['Name']."</td>
                            <td style='text-align: right;'>".$debitamount."</td>
                            <td style='text-align: right;'>".$creditamount."</td>
                            </tr>";
                else:
                    if($data['type']=='Payment' && $data['creditacnt']==$_POST['accountid'] ):
                        $creditamount=number_format((float)$data['amount'], 2);
                        $debitamount='';
                        $credittotal=$credittotal + $data['amount'];
                        $datarows.="<tr>
                            <td>".$data['voucher_no']."</td>
                            <td>".$data['date']."</td>
                            <td>".$data['narration']."</td>
                            <td>".$credit['Name']."</td>
                            <td style='text-align: right;'>".$debitamount."</td>
                            <td style='text-align: right;'>".$creditamount."</td>
                            </tr>";
                    elseif($data['type']=='Receipt' && $data['account_id']==$_POST['accountid']):
                        $debitamount=number_format((float)$data['amount'], 2);
                        $creditamount='';
                        $debittotal=$debittotal + $data['amount'];
                        $datarows.="<tr>
                            <td>".$data['voucher_no']."</td>
                            <td>".$data['date']."</td>
                            <td>".$data['narration']."</td>
                            <td>".$credit['Name']."</td>
                            <td style='text-align: right;'>".$debitamount."</td>
                            <td style='text-align: right;'>".$creditamount."</td>
                            </tr>";
                    endif;
                endif;
            endforeach;
            if($openbalance<0):
                $debittot=abs($openbalance);
                $debittotal=$debittotal + $debittot;
            else:
                $credtot=abs($openbalance);
                $credittotal=$credittotal + $credtot;
            endif;
            $datarows.="<tr>
                            <td colspan='4' style='text-align: center;'>Total</td>
                            <td style='text-align: right;'>".number_format((float)$debittotal, 2)."</td>
                            <td style='text-align: right;'>".number_format((float)$credittotal, 2)."</td>
                            </tr>";
            $acountbal=$credittotal - $debittotal;
            //$closingbal=$this->getbalance($openbalance,$acountbal);
            if($acountbal<0):
                $debitbal=abs($acountbal);
                $creditbal='';
                $datarows.="<tr>
                            <td colspan='4' style='text-align: center;'>Closing Balance</td>
                            <td style='text-align: right;'>".number_format((float)$debitbal, 2)."</td>
                            <td></td>
                            </tr>";
            else:
                $creditbal=number_format((float)$acountbal, 2);
                $debitbal='';
                $datarows.="<tr>
                            <td colspan='4' style='text-align: center;'>Closing Balance</td>
                            <td></td>
                            <td style='text-align: right;'>".$creditbal."</td>
                            </tr>";
            endif;
        else:
            if($openbalance<0):$debamount=number_format((float)abs($openbalance), 2);$credamount='';else:$debamount='';$credamount=number_format((float)$openbalance, 2);endif;
            $datarows.="<tr><td colspan='4' style='text-align: center;'>Opening Balance</td><td style='text-align: right;'>".$debamount."</td><td style='text-align: right;'>$credamount</td></tr>";
            if($debamount==''):
                $debittotal=number_format((float)$debamount, 2);
            else:
                $debittotal=$debamount;
            endif;
            if($credamount==''):
                $credittotal=number_format((float)$credamount, 2);
            else:
                $credittotal=$credamount;
            endif;
            $datarows.="<tr>
                            <td colspan='4' style='text-align: center;'>Total</td>
                            <td style='text-align: right;'>".$debittotal."</td>
                            <td style='text-align: right;'>".$credittotal."</td>
                            </tr>";
            $datarows.="<tr><td colspan='4' style='text-align: center;'>Closing Balance</td><td style='text-align: right;'>".$debamount."</td><td style='text-align: right;'>$credamount</td></tr>";
        endif;
        $datarows.="</tbody></table>";
        //echo $datarows;exit;
        /*$mpdf=new mPDF('utf-8', 'A4');
        $mpdf->WriteHTML($datarows);
        $mpdf->Output('ledger.pdf','I');*/


        $pdf = new Pdf();
        $mpdf = $pdf->api;
        $mpdf->SetHeader('Geo Tech Construction Company Pvt Ltd'); 
        $mpdf->SetFooter('Page {PAGENO}');
        $mpdf->WriteHtml($datarows); 
        $mpdf->Output('ledger.pdf','I');

    }

    public function Voucherdetails($id,$type)
    {
       if($type=='Voucher')
       {
           $voucher=Voucher::findOne($id);
           if($voucher->payment==1):
               $vouchertype='Cash';
           else:
               $vouchertype='Bank';
           endif;
           $debit=AccountsItem::findOne($voucher->account_id);
           $credit=AccountsItem::findOne($voucher->creditacnt);

           $datarow="<tr><td>".$vouchertype."</td>
           <td>".$voucher->narration."</td>
           <td>".$debit->name."</td>
           <td>".$credit->name."</td>
           <td>".number_format((float)$voucher->amount, 2)."</td></tr>";
       }
        else
        {
            $voucher=Journalvoucher::findOne($id);
            $vouchertype='Journal';
            $debit=AccountsItem::findOne($voucher->debitacnt);
            $credit=AccountsItem::findOne($voucher->creditacnt);
            if($credit){
                $name = $credit->name;
            }
            else{
                $name = '';
            }

            $datarow="<tr><td>".$vouchertype."</td>
           <td>".$voucher->narration."</td>
           <td>".$debit->name."</td>
           <td>".$name."</td>
           <td>".number_format((float)$voucher->amount, 2)."</td></tr>";
        }
        return $datarow;
    }

    public function actionDeleteledger()
    {
       if(isset($_POST['item']) & isset($_POST['type']))
       {
            //$connection = CActiveRecord::getDbConnection();
            $connection =  \Yii::$app->db;
            $sql="SELECT * FROM voucher WHERE id='".$_POST['item']."' ";
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $data=$dataReader->read();
            $groupid=$data['contragroup'];

           

            $sqljournal="SELECT * FROM journalvoucher WHERE id='".$_POST['item']."' ";
            $command=$connection->createCommand($sqljournal);
            $dataReader=$command->query();
            $dataj=$dataReader->read();

                if($_POST['type']=='Voucher'):
                    
                     $sqlsv="INSERT INTO voucher_backup (place,project_id,voucher_no,date,amount,account_id,creditacnt,type,narration,payment,cheque_no,bank_id,sub_schedule,contra,import,IOW_Id,Resource_Id,contragroup,delete_status,audit_status,audited,audit_comment,currentdate) values ('".$data['place']."','".$data['project_id']."','".$data['voucher_no']."','".$data['date']."','".$data['amount']."','".$data['account_id']."','".$data['creditacnt']."','".$data['type']."','".$data['narration']."','".$data['payment']."','".$data['cheque_no']."','".$data['bank_id']."','".$data['sub_schedule']."','".$data['contra']."','".$data['import']."','".$data['IOW_Id']."','".$data['Resource_Id']."','".$data['contragroup']."','".$data['delete_status']."','".$data['audit_status']."','".$data['audited']."','".$data['audit_comment']."','".$data['currentdate']."')";
                    //echo $sql;exit;
                    $command=$connection->createCommand($sqlsv);
                    $dataReader=$command->query();


                   // $sql1="DELETE FROM voucher WHERE id='".$_POST['item']."'";

                    $modelv=Voucher::findOne($_POST['item']);
                    $modelv->delete();
                else:
                     

                      $sqlsj="INSERT INTO journalvoucher_backup (place,project_id,voucher_no,date,amount,debitacnt,creditacnt,type,narration,payment,bank_id,contra,import,group_id,delete_status,audit_status,audited,audit_comment) values ('".$dataj['place']."','".$dataj['project_id']."','".$dataj['voucher_no']."','".$dataj['date']."','".$dataj['amount']."','".$dataj['debitacnt']."','".$dataj['creditacnt']."','".$dataj['type']."','".$dataj['narration']."','".$dataj['payment']."','".$dataj['bank_id']."','".$dataj['contra']."','".$dataj['import']."','".$dataj['group_id']."','".$dataj['delete_status']."','".$dataj['audit_status']."','".$dataj['audited']."','".$dataj['audit_comment']."')";
                    //echo $sql;exit;
                    $command=$connection->createCommand($sqlsj);
                    $dataReader=$command->query();



                    //$sql1="DELETE FROM journalvoucher WHERE id='".$_POST['item']."'";
                    $modelj=Journalvoucher::findOne($_POST['item']);
                    $modelj->delete();

                endif;                    
            //$command=$connection->createCommand($sql1);
            //echo $groupid;exit;
            if($dataReader=$command->query()):
                if($groupid!=''):
                    /*$sql2="DELETE FROM voucher WHERE contragroup='".$groupid."'";
                    $command=$connection->createCommand($sql2);
                    $dataReader=$command->query();*/
                    $model=Voucher::find()->where(['contragroup'=>$invoice['invoice_id']])->one();
                    $model->delete();
                endif;
                $returnarray=array('error'=>'no');
            else:
                $returnarray=array('error'=>'yes','errortext'=>'Cannot delete now. try again later');
            endif;
            
       } 
       else
       {
            $returnarray=array('error'=>'yes','errortext'=>'Not a valid request');
       }
       return json_encode($returnarray);
    }

    public function actionProjectexpenditure()
    {
        echo 'hai'; exit;
        $connection = CActiveRecord::getDbConnection();
        if($_POST['fromdate']!='' && $_POST['todate']!=''):
            $startdate=date('Y-m-d',strtotime($_POST['fromdate']));
            $enddate=date('Y-m-d',strtotime($_POST['todate']));
            $dateinfo="<p style='font-size: 18px;'>".$_POST['fromdate']." to ".$_POST['todate']."</p>";
        endif;
        $projectid=$_POST['project'];
        $ar=array("Projectid"=>$projectid,"startdate"=>$startdate,"enddate"=>$enddate);
        $str=http_build_query($ar);
        $print="<a href='".Yii::app()->request->baseUrl."/FinanceRequests/printprojectexpenditure?".$str."' target='_blank'><i class='glyphicon glyphicon-print'></i>Print</a>";
        $sql="SELECT Project_Id,Name FROM projects WHERE Project_Id='".$_POST['project']."' ";
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $project=$dataReader->read();
        $peinfo="<p>Project Expenditure of ".$project['Name']."</p>$dateinfo";
        $sql="SELECT id,name FROM accounts_sub WHERE master_id='1' ORDER BY sortorder ASC,id DESC";
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $subgroups=$dataReader->readAll();
        $datarows='';
        $projexptot=0;
        foreach($subgroups AS $subgroup):
            $sql="SELECT COUNT(account_id) as count FROM subgroup_accounts AS a INNER JOIN accounts_item AS b ON a.account_id=b.id WHERE a.subgrp_id='".$subgroup['id']."' AND b.account_type IN (5,8,6)";
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $dataval=$dataReader->read();
            $datasubgroupows='';
            if($dataval['count']>0):
                $datasubgroupows.="<tr><td colspan='5'><span style='font-weight: bold;'>".$subgroup['name']."</span></td></tr>";
            endif;
            
            $sql1="SELECT a.subgrp_id,b.id,b.name FROM subgroup_accounts AS a INNER JOIN accounts_item AS b ON a.account_id=b.id WHERE a.subgrp_id='".$subgroup['id']."' AND accountschedule_id=0 AND b.account_type IN (5,8,6)";
            $command=$connection->createCommand($sql1);
            $dataReader=$command->query();
            $accounts=$dataReader->readAll();
            $grptotal=0;
            $dataaccountrows='';
            if(count($accounts)>0):
                foreach($accounts AS $accounthead):
                    $closingbal=$this->closingbalance($accounthead['id'],$projectid,$startdate,$enddate);
                    // echo $closingbal;
                    if($closingbal!=0 ):
                        $content = $this->Ledgeritems($projectid,$accounthead['id'],$startdate,$enddate);
                        $dataaccountrows.="<tr><td><div class='hover' data-tooltip='tooltip".$accounthead['id']."' style='cursor:pointer;'><a>".$accounthead['name']."</a>
                        <div class='tooltiptable' id='tooltip".$accounthead['id']."' style='width:500px;'>
                        <table cellpadding='0' cellspacing='0' width='100%'><tr><th>Date</th><th>Narration</th><th>Amount</th></tr>".$content."</table>
                        </div></div></td><td style='text-align: right;'>".number_format((float)abs($closingbal), 2)."</td><td> </td><td> </td></tr>";
                    endif;
                    $grptotal=$grptotal + abs($closingbal);
                endforeach;
                if($dataaccountrows!=''):
                $datarows.=$datasubgroupows.$dataaccountrows."<tr><td style='text-align: center;font-weight: bold;'>Total</td><td> </td><td> </td><td style='text-align: right;'>".number_format((float)abs($grptotal), 2)."</td></tr>";
                // echo $datarows;
                endif;
            //$datarows.="<tr><td style='text-align: center;font-weight: bold;'>Total</td><td>".number_format((float)abs($grptotal), 2)."</td></tr>";
            $projexptot=$projexptot + $grptotal;
            endif;
        endforeach;
                // echo $datarows;exit;
        $datarows.="<tr><td style='text-align: center;font-weight: bold;'>Project Expenditure Total</td>
                    <td> </td><td> </td><td style='text-align: right;'>".number_format((float)abs($projexptot), 2)."</td></tr>";
        $datarows.="<tr><td colspan='4' style='font-weight: bold;'>Advances</td></tr>";
        // echo $datarows;
        $sql2="SELECT id,name FROM accounts_item WHERE account_type IN (5,7,14,15,16)";
        $command=$connection->createCommand($sql2);
        $dataReader=$command->query();
        $liabilities=$dataReader->readAll();
        $liabtotal=0;
        foreach($liabilities AS $liability):
            $liabclosingbal=$this->closingbalance($liability['id'],$projectid,$startdate,$enddate);
            if($liabclosingbal!=0 && $liabclosingbal<0):
                $datarows.="<tr><td>".$liability['name']."</td><td style='text-align: right;'>".number_format((float)abs($liabclosingbal), 2)."</td><td> </td><td> </td></tr>";
                $liabtotal=$liabtotal + abs($liabclosingbal);
            endif;
        endforeach;
        $grandtotal=$liabtotal + $projexptot;
        $datarows.="<tr><td style='text-align: center;font-weight: bold;'>Total</td><td> </td><td> </td><td style='text-align: right;'>".number_format((float)abs($liabtotal), 2)."</td></tr>";
        $datarows.="<tr><td style='text-align: center;font-weight: bold;'>Grand Total</td><td> </td><td> </td><td style='text-align: right;'>".number_format((float)abs($grandtotal), 2)."</td></tr>";
        /*$sql3="SELECT id,name FROM accounts_item WHERE account_type='4'";
        $command=$connection->createCommand($sql3);
        $dataReader=$command->query();
        $income=$dataReader->readAll();*/
        //foreach($income AS $incomeacnt):
            $incomeclosingbal=$this->closingbalance(140,$projectid,$startdate,$enddate);
            //$incometotal=$incometotal + $incomeclosingbal;
        //endforeach;
        $datarows.="<tr><td style='text-align: center;font-weight: bold;'>Contract Income</td><td style='text-align: right;'>".number_format((float)abs($incomeclosingbal), 2)."</td><td> </td><td> </td></tr>";
        // echo $datarows;
        $arr = array('result'=>$datarows,'peinfo' => $peinfo,'print'=>$print,'error'=>'No');
        // echo $arr;
        echo json_encode($arr);
    }
   
    // FinanceApprove Tab end


    // Journals Tab start

    public function actionJournals()
    {
        $userid = Yii::$app->user->id;
        $user = User::find()->where(['id'=>$userid])->one();
        if(isset($_POST['debitamount'])):
            $debitcount=count($_POST['debitamount']);
            $creditcount=count($_POST['creditamount']);
            $model = new Journels();
            $groupid=time();
            $model->date=date('Y-m-d',strtotime($_POST['Journal_Date']));
            $model->projectid=$_POST['projectid'];
            //$model->narration=$_POST['Journal_Narration'][0];
            if($debitcount==1 & $creditcount>1):
                $model->total=$_POST['debitamount'][0];
                $model->debitacnt=$_POST['debitaccount'][0];
                $model->type="credit";
            elseif($creditcount==1 & $debitcount>1):
                $model->total=$_POST['creditamount'][0];
                $model->creditacnt=$_POST['creditaccount'][0];
                $model->type="debit";
            else:
                $model->type="debit";
                $model->total=$_POST['creditamount'][0];
                $model->creditacnt=$_POST['creditaccount'][0];
                $model->narration=$_POST['Journal_Narration'][1];
            endif;
            //echo $model->total;exit;
            $model->groupid=$groupid;
            $model->User_Id=$userid;
            $model->place=$_POST['place'];
            if(isset($_POST['shitem']))
            {
                if($_POST['shitem'] > 0)
                {
                    $model->bsitem_id = $_POST['shitem'];
                }
            }
            
            $model->save(false);
            if($debitcount==1 & $creditcount>1):
                for($i=0;$i<count($_POST['creditamount']);$i++)
                {
                    
                    $sql="INSERT INTO journalitems(journalid,amount,creditaccnt,narration) values ('".$model->id."','".$_POST['creditamount'][$i]."','".$_POST['creditaccount'][$i]."','".$_POST['Journal_Narration'][$i]."')";
                    //echo $sql;
                    $command=Yii::$app->db->createCommand($sql);
                    $dataReader=$command->query();
                }
            elseif($creditcount==1 & $debitcount>1):
                for($i=0;$i<count($_POST['debitamount']);$i++)
                {
                    $sql="INSERT INTO journalitems(journalid,debitaccnt,amount,narration) values ('".$model->id."','".$_POST['debitaccount'][$i]."','".$_POST['debitamount'][$i]."','".$_POST['Journal_Narration'][$i]."')";
                    //echo $sql;
                    $command=Yii::$app->db->createCommand($sql);
                    $dataReader=$command->query();
                }
            else:

                
                $sql="INSERT INTO journalitems(journalid,debitaccnt,amount,narration) values ('".$model->id."','".$_POST['debitaccount'][0]."','".$_POST['debitamount'][0]."','".$_POST['Journal_Narration'][0]."')";
                //echo $sql;
                $command=Yii::$app->db->createCommand($sql);
                $dataReader=$command->query();
            endif;
            //if($_GET['cashbill']!=''):
                //$cashbills = Cashbills::find()->where(['advance_id' =>$_GET['cashbill']])->all();
                //foreach($cashbills AS $cashbill):
                    //$cashbill->status='1';
                    //$cashbill->save(false);
                //endforeach;
            //endif;
            //if($_GET['cashadvance']!=''):
                //$cashbill=Cashadvance::model()->findByPk($_GET['cashadvance']);
                //$cashbill=Cashadvance::find($_GET['cashadvance'])->one();
                //$cashbill->status='1';
                //$cashbill->save(false);
            //endif;
            if(isset($_POST['saveandcreate'])):

            else:
                //$this->redirect(array('FinanceRequests/index'));
            endif;
            //$this->redirect(array('FinanceRequests/index'));
            $arr = array('error' => 'Yes');
            return json_encode($arr); 
        endif;
    }

    public function actionJournalsearch()
    {
        $userid = Yii::$app->user->id;
        $user = User::find()->where(['id'=>$userid])->one();
        if ($user['superuser']==1 || $user['superuser']==2) {

            $sql = "SELECT a.id,DATE_FORMAT(a.date,'%d %b %y') AS date,a.projectid,a.creditacnt,a.debitacnt,a.narration,a.total,a.type,b.username,c.Name FROM journels AS a INNER JOIN user as b on a.User_Id=b.id inner join projects as c on a.place=c.Project_Id INNER JOIN journalitems as j on a.id=j.journalid WHERE a.Status='0'";
            if($_POST['jsearch']!=''){
                $sql.=" AND c.Name LIKE '%".$_POST['jsearch']."%' OR b.username LIKE '%".$_POST['jsearch']."%' OR a.narration LIKE '%".$_POST['jsearch']."%'";
            }
            //$sql.=" ORDER BY a.date DESC ,a.id DESC ";
            $sql.=" GROUP BY a.id ORDER BY a.date DESC ,a.id DESC ";
            $command = Yii::$app->db->createCommand($sql);
            $dataReader = $command->query();
            $dataProvider = $dataReader->readAll();

            /*elseif($user['superuser']==2)
            {
                $sql1="SELECT projectid FROM user_projects WHERE userid='$userid'";
                $command=$connection->createCommand($sql1);
                $dataReader=$command->query();
                $dataProvider=$dataReader->readAll();
                $projectids='';
                foreach($dataProvider AS $key=>$data):
                    if($key=='0'):
                        $projectids="'".$data['projectid']."'";
                    else:
                        $projectids.=",'".$data['projectid']."'";
                    endif;
                endforeach;
                $sql="SELECT a.bill_id,a.billno,a.party,DATE_FORMAT(a.date,'%d %b %y') AS date,DATE_FORMAT(a.duedate,'%d %b %y') AS duedate,b.username,c.Name FROM bills AS a INNER JOIN users as b on a.User_Id=b.id inner join projects as c on a.projectid=c.Project_Id WHERE a.Status='0' AND a.projectid IN (".$projectids.") AND a.User_Id='$userid' ORDER BY a.Status ASC,a.bill_id DESC ";
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();
                $dataProvider=$dataReader->readAll();
            }*/
            $datarows = '';
            if (count($dataProvider) > 0):
                foreach ($dataProvider AS $key => $data):
                    $jiid = $data['id'];
                    $sql="SELECT a.id,a.narration,DATE_FORMAT(a.date,'%d %b %y') AS date,a.creditacnt,a.total,a.place,b.username,c.Name,d.name as accountname FROM journels AS a INNER JOIN user as b on a.User_Id=b.id inner join projects as c on a.projectid=c.Project_Id left join accounts_item AS d on (a.creditacnt=d.id OR a.debitacnt=d.id) WHERE a.id='$jiid' ";
                    $command=Yii::$app->db->createCommand($sql);
                    $dataReader=$command->query();
                    $dataProviders=$dataReader->read();
                    $sql1="SELECT a.*,b.name as accountname FROM journalitems as a INNER JOIN accounts_item as b on a.debitaccnt=b.id WHERE journalid='$jiid'";
                    //echo $sql1;exit;
                    $command=Yii::$app->db->createCommand($sql1);
                    $dataReader=$command->query();
                    $debits=$dataReader->readAll();
            
                    $sql1="SELECT a.*,b.name as accountname FROM journalitems as a INNER JOIN accounts_item as b on a.creditaccnt=b.id WHERE journalid='$jiid'";
                    //echo $sql1;exit;
                    $command=Yii::$app->db->createCommand($sql1);
                    $dataReader=$command->query();
                    $credits=$dataReader->readAll();

                    //if($data['type']=='credit'):
                        //$debit=AccountsItem::model()->findbyPk($data['debitacnt'])->name;
                        //$debit=AccountsItem::find($data['debitacnt'])->one()->name;
                        //$credit="";
                    //else:
                        //$debit="";
                        //$credit=AccountsItem::model()->findbyPk($data['creditacnt'])->name;
                        //$credit=AccountsItem::find($data['creditacnt'])->one()->name;
                    //endif;
                    if(count($debits)>0)
                    {
                        $datarows .= '
                            <div id="jl-'.$data['id'].'"><br><br><hr><div class="row">
                                <div class="col-md-11">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <span class="icon-user3"></span> <em class="username">'. $dataProviders['username'] .'</em>
                                        </div>
                            
                                        <div class="col-md-2 type">
                                            <label>Date</label>
                                            <br>
                                            <span class="date"><em class="cal-icon icon-calendar1"></em>'. $dataProviders['date'] .'</span>                                             
                                        </div>
                            
                                        <div class="col-md-2 type">
                                            <label>Debit Account</label>';

                                foreach($debits AS $debit)
                                {
                                    $datarows .= '<br>
                                            <span >'. $debit['accountname'] .'</span>';
                                }
                                $datarows .='
                                        </div>
                                        <div class="col-md-3 type">
                                            <label>Debit Account Narration</label>';

                                foreach($debits AS $debit){
                                    $datarows .= '<br>
                                            <span >'. $debit['narration'] .'</span>';
                                }

                                $datarows .= '
                                
                                        </div>
                                        <div class="col-md-1 type"></div>
                                        <div class="col-md-2 type">
                                            <label>Amount</label>
                                                ';
                                foreach($debits AS $debit){
                                    $datarows .= '<br>
                                            <span >'. number_format((float)$debit['amount'], 2) .'</span>';
                                }
                                
                        $datarows .= ' </div>';

                        $datarows .= '
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="icon-groups">
                                        <a class="btn btn-danger delete-journal icon-trash1" title="Delete" href="#" style="border-color: white" id="delete-journal-'.$data['id'].'" data-id="'.$data['id'].'"></a>
                                    </div>
                                </div>
                            </div><br>';

                        $datarows .= '
                            <div class="row">
                                <div class="col-md-11">
                                    <div class="row">
                                        <div class="col-md-2">
                                
                                        </div>
                            
                                        <div class="col-md-2 type">
                                                                           
                                        </div>
                                        <div class="col-md-2 type">
                                            <label>Credit Account</label>
                               
                                            <span >'.$dataProviders['accountname'].'</span>';
                           
                        $datarows .= '
                                        </div>
                            
                                        <div class="col-md-3 type">
                                            <label>Credit Account Naration</label>';
                                            /*<span >'. $data['narration'] .'</span>*/
                                            //foreach($credits AS $credit){
                                                $datarows .= '<br>
                                                <span >'. $dataProviders['narration'] .'</span>';
                                            //}
                        $datarows .= '  </div>
                                            
                                        <div class="col-md-1 type"></div>
                                        <div class="col-md-2 type">
                                            <label>Amount</label>
                                            <span >'. number_format((float)$data['total'], 2) .'</span>';
                                            /*foreach($debits AS $debit){
                                                $datarows .= '<br>
                                                <span >'. number_format((float)$debit['amount'], 2) .'</span>';
                                            }*/
                        $datarows .= '  </div>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="icon-groups">
                                    <a class="btn btn-success  icon-check innactive ApproveMyjounarl" title="Approve" href="#" id="ApproveMyjounarl-'.$data['id'].'" data-id="'.$data['id'].'"></a>
                                    <input type="hidden" id="finjournalid">
                                    <a class="btn btn-primary icon-close innactive Rejectjounarl" title="Reject" href="#" id="Rejectjounarl-'.$data['id'].'" data-id="'.$data['id'].'"></a>
                                    </div>
                                </div>
                            </div>';
                    }
                    else
                    {
                        $datarows .= '
                            <div id="jl-'.$data['id'].'"><br><br><hr>
                                <div class="row">
                                    <div class="col-md-11">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <span class="icon-user3"></span> <em class="username">'. $dataProviders['username'] .'</em>
                                            </div>
                                    
                                            <div class="col-md-2 type">
                                                <label>Date</label>
                                                <br>
                                                <span class="date"><em class="cal-icon icon-calendar1"></em>'. $dataProviders['date'] .'</span>                                             
                                            </div>
                                            <div class="col-md-2 type">
                                                <label>Debit Account</label>
                                                <span>'.$dataProviders['accountname'].'</span>
                                            </div>';
                        $datarows .='       <div class="col-md-3 type">
                                                <label>Debit Account Narration</label>';
                                                    /*<span >'. $data['narration'] .'</span>*/
                                                    foreach($credits AS $credit){
                                                        $datarows .= '<br>
                                                        <span >'. $credit['narration'] .'</span>';
                                                    }
                        $datarows .= '      </div>
                                            <div class="col-md-1 type"></div>                                               
                                            <div class="col-md-2 type">
                                                <label>Amount</label>
                                                    <br>
                                                    <span >'. number_format((float)$data['total'], 2) .'</span>';
                                                    /*foreach($credits AS $credit){
                                                        $datarows .= '
                                                        <span >'. number_format((float)$credit['amount'], 2) .'</span>';
                                                    }*/

                        $datarows .= '      </div>';

                        $datarows .= '  </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="icon-groups">
                                            <a class="btn btn-danger delete-journal icon-trash1" title="Delete" href="#" style="border-color: white" id="delete-journal-'.$data['id'].'" data-id="'.$data['id'].'"></a>
                                        </div>
                                    </div>
                                </div><br>';                       

                        $datarows .= '
                                <div class="row">
                                    <div class="col-md-11">
                                        <div class="row">
                                            <div class="col-md-2">
                                                
                                            </div>
                                            
                                            <div class="col-md-2 type">
                                                                                           
                                            </div>
                                            <div class="col-md-2 type">
                                                <label>Credit Account</label>';
                                                foreach($credits AS $cedit){

                                                    $datarows .='<br><span >'.$cedit['accountname'].'</span>';
                                                }
                                    
                        $datarows .= '      </div>
                                            <div class="col-md-3 type">
                                                <label>Credit Account Naration</label>';
                                                foreach($credits AS $cedit){
                                                    $datarows .= '<br>
                                                    <span >'. $cedit['narration'] .'</span>';
                                                }
                        $datarows .= '      </div>
                                                               
                                            <div class="col-md-1 type"></div>
                                            <div class="col-md-2 type">
                                                <label>Amount</label>';

                                                foreach($credits AS $credit){
                                                    $datarows .= '<br>
                                                        <span >'. number_format((float)$credit['amount'], 2) .'</span>';
                                                }
                        $datarows .= '      </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="icon-groups">
                                        <a class="btn btn-success  icon-check innactive ApproveMyjounarl" title="Approve" href="#" id="ApproveMyjounarl-'.$data['id'].'" data-id="'.$data['id'].'"></a>
                                        <input type="hidden" id="finjournalid">
                                        <a class="btn btn-primary icon-close innactive Rejectjounarl" title="Reject" href="#" id="Rejectjounarl-'.$data['id'].'" data-id="'.$data['id'].'"></a>
                                        </div>
                                    </div>
                                </div>';
                    }                                  
                endforeach;
                $datarows .= '<div style="text-align: right;"><button type="button" class="btn btn-primary apprveselectedjournal" data-id="'.$data['id'].'" title="Approve">Approve</button></div>';
            else:
                $datarows = '<span style="text-align: center;">No Journals Found<span>';
            endif;
            $arr = array('result' => $datarows, 'error' => 'No');
            return json_encode($arr);
        }
        else
        {
            $sql = "SELECT a.id,DATE_FORMAT(a.date,'%d %b %y') AS date,a.projectid,a.creditacnt,a.narration,a.total,b.username,c.Name FROM journels AS a INNER JOIN user as b on a.User_Id=b.id inner join projects as c on a.projectid=c.Project_Id WHERE a.Status='0' AND a.User_Id='$userid' ORDER BY a.date DESC ,a.id DESC ";
            $command = Yii::$app->db->createCommand($sql);
            $dataReader = $command->query();
            $dataProvider = $dataReader->readAll();
            $datarows = '';
            if (count($dataProvider) > 0):
                foreach ($dataProvider AS $key => $data):
                    if($data['type']=='credit'):
                        //$debit=AccountsItem::model()->findbyPk($data['debitacnt'])->name;
                        $debit=AccountsItem::find($data['debitacnt'])->one()->name;
                        $credit="";
                    else:
                        $debit="";
                        //$credit=AccountsItem::model()->findbyPk($data['creditacnt'])->name;
                        $credit=AccountsItem::find($data['creditacnt'])->one()->name;
                    endif;
                    //$projectname = Projects::find($dataProvider['projectid'])->one()->Project_Id;
                    $datarows .= '<br><br><hr><div class="row">
                    <div class="col-md-11">
                        <div class="row">
                            <div class="col-md-2">
                                <span class="icon-user3"></span> <em class="username">'. $data['username'] .'</em>
                            </div>
                            
                            <div class="col-md-2 type">
                                <label>Date</label>
                                <br>
                                <span class="date"><em class="cal-icon icon-calendar1"></em>'. $data['date'] .'</span>                                          
                            </div>
                            
                            <div class="col-md-3 type">
                                <label>Credit Account</label>
                                <br>
                                <span >' . $credit .'</span>
                            </div>
                            
                            <div class="col-md-3 type">
                                <label>Debit Account</label>
                                <br>
                                <span >'. $debit .'</span>
                            </div>
                            
                            <div class="col-md-2 type">
                                <label>Amount</label>
                                <br>
                                <span class="order-amount">'. number_format((float)$data['total'], 2) .'</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="icon-groups">
                            <a class="btn btn-primary icon-eye btn-show-fa-hidden view-journal" href="#"></a>
                            
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-11">
                        <div class="row">
                            <div class="col-md-2">
                                
                            </div>
                            
                            <div class="col-md-2 type">
                                <label>Project</label>
                                <br>
                                <span>'.$data['Name'].'</span>                                          
                            </div>
                            
                            <div class="col-md-3 type">
                                <label>Place</label>
                                <br>
                                <span >'.$data['Name'].'</span>
                            </div>
                            
                            <div class="col-md-3 type">
                                <label>Naration
                                </label>
                                <br>
                                <span >'. $data['narration'] .'</span>
                            </div>
                            
                            <div class="col-md-2 type">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1">
                    </div>
                </div>';
                endforeach;
            else:
                $datarows = '<span style="text-align: center;">No Journals Found<span>';
            endif;
            $arr = array('result' => $datarows, 'error' => 'No');
            return json_encode($arr);
        }
    }

    public function actionJournalapprove()
    {
        $userid = Yii::$app->user->id;
        if($_POST['journalid'])
        {
            $jid = $_POST['journalid'];
            if($_POST['journalstatus']=='1')
            {
                $sql3="UPDATE journels SET Status='1',Approved_By='$userid',Approved_On='".date('y-m-d h:i:s')."' WHERE id='$jid'";
                $command=Yii::$app->db->createCommand($sql3);
                $dataReader=$command->query();
            }
            elseif($_POST['journalstatus']=='2')
            {
                $sql4="UPDATE journels SET Status='2',Approved_By='$userid',Approved_On='".date('y-m-d h:i:s')."' WHERE id='$jid'";
                $command=Yii::$app->db->createCommand($sql4);
                $dataReader=$command->query();
            }
            elseif($_POST['journalstatus']=='4')
            {
                $sql4="UPDATE journels SET Status='4',Approved_By='$userid',Approved_On='".date('y-m-d h:i:s')."' WHERE id='$jid'";
                $command=Yii::$app->db->createCommand($sql4);
                $dataReader=$command->query();
            }
            $arr = array('error' => 'Yes');
            return json_encode($arr);    
        }
    }

    public function actionTrialbalance()
    {
        $connection = Yii::$app->db;
        $startdate=date('d-m-Y',strtotime($_POST['fromdate']));
        $enddate=date('d-m-Y',strtotime($_POST['todate']));
        if($_POST['fromdate']!='' && $_POST['todate']!=''):
            $ar=array("startdate"=>$startdate,"enddate"=>$enddate);
        endif;
        $str=http_build_query($ar);
        if($str!=''):
            $str="?".$str;
        endif;
        /*$print="<a href='".Yii::$app->request->baseUrl."/FinanceRequests/printtrialbalance".$str."' target='_blank'><i class='glyphicon glyphicon-print'></i>Print</a>
                <a href='".Yii::$app->request->baseUrl."/FinanceRequests/exporttrialbalance".$str."' style='padding-left:25px' target='_blank'><i class='glyphicon glyphicon-export'></i>Export</a>";
        $export="<a href='".Yii::$app->request->baseUrl."/FinanceRequests/exporttrialbalance".$str."' style='padding-left:25px' target='_blank'><i class='glyphicon glyphicon-export'></i>Export</a>";*/
        $export="<div class='custom-print-btn'>   
                        <div class='icon-groups' style='top:-65px;'><a href='".Yii::$app->request->baseUrl."/financerequests/trialbalancecsv".$str."' style='padding-left:25px' class='btn btn-primary text-button'><span class='icon-print'></span>Excel</a></div>
                    </div>";
        //$sql="SELECT id,name FROM accounts_item ORDER BY name ASC ";
        $sql="SELECT type_id,name FROM account_types ORDER BY sortorder ASC";
        //$sql="SELECT a.id,a.name FROM accounts_item AS a LEFT JOIN ledger_openingbalance AS b ON b.accountid = a.id WHERE b.accountid IS NULL ORDER BY a.id ASC ";
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $dataProvider=$dataReader->readAll();//print_r($dataProvider);exit;
        $datarows='';
        $month=date('m');
        if($month=='01' || $month=='02' || $month=='03'):
            $initialdate=date("Y",strtotime("-1 year")).'-04-01';
        else:
            $initialdate=date('Y').'-04-01';
        endif;
        if($_POST['fromdate']!='' && $_POST['todate']!=''):
            $dateinfo='<div class="row list-head">
                            <div class="col-md-12 text-center type">
                                <h5>Geotech Offshore Structures (P) Ltd</h5>
                                Trial Balance From <span class="date"><em class="cal-icon icon-calendar1"></em>'.date('d-m-Y',strtotime($_POST['fromdate'])).'</span> to <span class="date"><em class="cal-icon icon-calendar1"></em>'.date('d-m-Y',strtotime($_POST['todate'])).'</span>
                                <div align="right"><!--<a href="'.Yii::$app->request->baseUrl.'/financerequests/printtrialbalance'.$str.'" target="_blank"><i class="glyphicon glyphicon-print"></i>Print</a>&nbsp&nbsp&nbsp
                                <a href="'.Yii::$app->request->baseUrl.'/financerequests/exporttrialbalance'.$str.'" target="_blank"><i class="glyphicon glyphicon-export"></i>Export</a>-->'.$export.'
                                </div></div></div>';
            $condition="AND date BETWEEN '$startdate' AND '$enddate'";
        else:
            $dateinfo='<div class="row list-head">
            <div class="col-md-12 text-center type">
                <h5>Geotech Offshore Structures (P) Ltd</h5>
                Trial Balance as on <span class="date"><em class="cal-icon icon-calendar1"></em>'.date("d/m/Y").'</span>
                <div align="right"><!--<a href="'.Yii::$app->request->baseUrl.'/financerequests/printtrialbalance'.$str.'" target="_blank"><i class="glyphicon glyphicon-print"></i>Print</a>&nbsp&nbsp&nbsp
                <a href="'.Yii::$app->request->baseUrl.'/financerequests/exporttrialbalance'.$str.'" target="_blank"><i class="glyphicon glyphicon-export"></i>Export</a>-->'.$export.'
            </div></div>';
            $condition="AND date >= '$initialdate'";
        endif;
        $datarows.= $dateinfo;
        $totaldebit=0;
        $totalcredit=0;
        $datarows.='<div class="row"><div class="table-wrpr"><table class="table table-bordered"><thead style="border-bottom: #CCC solid 2px;">
                    <tr><th>Account Head</th><th>Debit</th><th>Credit</th></tr></thead><tbody>';
        //foreach($dataProvider AS $key=>$data):
            //$type=AccountTypes::model()->findByPk($data['type_id'])->name;
            //$type=AccountTypes::findOne($data['type_id'])->name;
            //$datarows.='<tr><td class="table-subhead" colspan="3"><strong>'.$type.'</strong></td></tr>';
            //$sql2="SELECT id,name FROM accounts_item WHERE account_type ='".$data['type_id']."' ORDER BY name ASC ";
                   
                   
            foreach($dataProvider AS $key=>$data):
            $type=AccountTypes::find()->where(['type_id'=>$data['type_id']])->ORDERBY(['name'=>SORT_ASC])->one();
            
            //$sql2="SELECT id,name FROM accounts_item WHERE account_type ='".$data['type_id']."' AND Status=0 ORDER BY name ASC ";
            $sql2="
            SELECT accitem.id as id,accitem.name as name, accsub.name as subgrp_name 
            FROM accounts_item as accitem 
            LEFT JOIN subgroup_accounts as subacc ON subacc.account_id = accitem.id 
            LEFT JOIN accounts_sub as accsub ON accsub.id = subacc.subgrp_id 
            WHERE accitem.account_type ='".$data['type_id']."' AND accitem.Status=0 
            ORDER BY accsub.name, accitem.name ASC";

            $command=$connection->createCommand($sql2);
            $dataReader=$command->query();
            $accounts=$dataReader->readAll();
            
            $accountrows='';
            $gdebittot=0;
            $gcredittot=0;
            $subgrp_name = '';
            foreach($accounts AS $account):
                if($account['subgrp_name'] != $subgrp_name)
                    $accountrows.="<tr><td colspan='3' style='padding-left: 15px;'><b>".$account['subgrp_name']."</b></td></tr>";
                $subgrp_name = $account['subgrp_name'];
                $sql3="SELECT balance FROM ledger_openingbalance WHERE accountid='".$account['id']."' ";
                $command=$connection->createCommand($sql3);
                $dataReader=$command->query();
                $result=$dataReader->read();
                $openingbal=$result['balance'];
                if($_POST['fromdate']!=''):
                    $initialdate='2013-04-01';
                    $enddate=date('Y-m-d',strtotime($_POST['todate']));

                    $accounthead=$account['id'];
                    $command = Yii::$app->db->createCommand("CALL GetTrialBalance(:startdate, :currentdate,:accounthead,@outval)");
                    $command->bindParam(":startdate",$initialdate);
                    $command->bindParam(":currentdate", $enddate);
                    $command->bindParam(":accounthead", $accounthead);
                    $command->query();
                    //print_r($command);exit;
                    $openingbalance = $openingbal + (Yii::$app->db->createCommand("select @outval as result;")->queryScalar());
                else:
                    $initialdate='2013-04-01';
                    $enddate=date('Y-m-d');

                    $accounthead=$account['id'];
                    $command = Yii::$app->db->createCommand("CALL GetTrialBalance(:startdate, :currentdate,:accounthead,@outval)");
                    $command->bindParam(":startdate",$initialdate);
                    $command->bindParam(":currentdate", $enddate);
                    $command->bindParam(":accounthead", $accounthead);
                    $command->query();
                    //print_r($command);exit;
                    $openingbalance = $openingbal + (Yii::$app->db->createCommand("select @outval as result;")->queryScalar());
                endif;
                if($openingbalance!=0):
                    if($openingbalance<0):
                        $debit=abs($openingbalance);
                        $debitbal=number_format((float)$debit, 2);
                        $credit='';
                    else:
                        $debitbal='';
                        $credit=number_format((float)$openingbalance, 2);
                    endif;
                    $accountrows.="<tr>
                        <td style='padding-left: 25px;'>".$account['name']."</td>
                        <td style='text-align: right;'>".$debitbal."</td>
                        <td style='text-align: right;'>".$credit."</td>
                        </tr>";

                    $deb=str_replace( ',', '', $debitbal );
                    $cr=str_replace( ',', '', $credit );
                    //$gdebittot=$gdebittot + (int)$deb;
                    //$gcredittot=$gcredittot + (int)$cr;

                    $gdebittot=$gdebittot + (float)$deb;
                    $gcredittot=$gcredittot + (float)$cr;

                endif;
            endforeach;
            if($gdebittot!=0 || $gcredittot!=0 ){
                $datarows.="<tr><td colspan='3' style='background-color: #f1f1f1;font-size: 15px;'><b>".$type->name."</b></td></tr>";
                $datarows.=$accountrows;
                $datarows.="<tr>
                        <td style='text-align: center;'>Group Total</td>
                        <td style='text-align: right;'>".number_format((float)$gdebittot, 2)."</td>
                        <td style='text-align: right;'>".number_format((float)$gcredittot, 2)."</td>

                        </tr>";
            }
            //$openingbalance = Yii::app()->db->createCommand("select @outval as result;")->queryScalar();
            $deb=str_replace( ',', '', $gdebittot );
            $cr=str_replace( ',', '', $gcredittot );
            //$totaldebit=$totaldebit + $deb;
            //$totalcredit=$totalcredit + $cr;

            $totaldebit=$totaldebit + (float)$deb;
            $totalcredit=$totalcredit + (float)$cr;

        endforeach;

        $datarows.="<tr>
                    <td style='text-align: center;'>Total</td>
                    <td style='text-align: right;'>".number_format((float)$totaldebit, 2)."</td>
                    <td style='text-align: right;'>".number_format((float)$totalcredit, 2)."</td>
                    </tr>";
        //$arr = array('result' => $datarows,'print'=>$print,'export'=>$export,'error'=>'No');
        $arr = array('result' => $datarows,'error'=>'No');
        return json_encode($arr);
    }

    public function actionTrialbalancecsv()
    {
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=trialbalance.xls");

        $connection = Yii::$app->db;
        $sql="SELECT type_id,name FROM account_types ORDER BY sortorder ASC";
        //$sql="SELECT a.id,a.name FROM accounts_item AS a LEFT JOIN ledger_openingbalance AS b ON b.accountid = a.id WHERE b.accountid IS NULL ORDER BY a.id ASC ";
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $dataProvider=$dataReader->readAll();
        $datarows='';
        $month=date('m');
        if($month=='01' || $month=='02' || $month=='03'):
            $initialdate=date("Y",strtotime("-1 year")).'-04-01';
        else:
            $initialdate=date('Y').'-04-01';
        endif;

        if($_GET['startdate']!='' && $_GET['enddate']!=''):
            $dateinfo='Trial Balance From <span class="date"><em class="cal-icon icon-calendar1"></em>'.date('d-m-Y',strtotime($_GET['startdate'])).'</span> to <span class="date"><em class="cal-icon icon-calendar1"></em>'.date('d-m-Y',strtotime($_GET['enddate'])).'</span>';
            //$condition="AND date BETWEEN '$startdate' AND '$enddate'";
        else:
            $dateinfo='Trial Balance';
            //$condition="AND date >= '$initialdate'";
        endif;

        //$datarows.= $dateinfo;
        $totaldebit=0;
        $totalcredit=0;

        $datarows.="
            <table style='width:100%'' border='1'>
                <tr><th colspan='3'><b>".$dateinfo."</b></th></tr>
                <tr>
                    <th>Account Head</th>
                    <th>Debit</th>
                    <th>Credit</th>
                </tr>";

        foreach($dataProvider AS $key=>$data):
            $type=AccountTypes::find()->where(['type_id'=>$data['type_id']])->ORDERBY(['name'=>SORT_ASC])->one();
            
            $sql2="SELECT id,name FROM accounts_item WHERE account_type ='".$data['type_id']."' AND Status=0 ORDER BY name ASC ";

            $command=$connection->createCommand($sql2);
            $dataReader=$command->query();
            $accounts=$dataReader->readAll();
            
            $accountrows='';
            $gdebittot=0;
            $gcredittot=0;
            foreach($accounts AS $account):
                $sql3="SELECT balance FROM ledger_openingbalance WHERE accountid='".$account['id']."' ";
                $command=$connection->createCommand($sql3);
                $dataReader=$command->query();
                $result=$dataReader->read();
                $openingbal=$result['balance'];
                if($_GET['startdate']!=''):
                    $initialdate='2013-04-01';
                    $enddate=date('Y-m-d',strtotime($_GET['enddate']));

                    $accounthead=$account['id'];
                    $command = Yii::$app->db->createCommand("CALL GetTrialBalance(:startdate, :currentdate,:accounthead,@outval)");
                    $command->bindParam(":startdate",$initialdate);
                    $command->bindParam(":currentdate", $enddate);
                    $command->bindParam(":accounthead", $accounthead);
                    $command->query();
                    //print_r($command);exit;
                    $openingbalance = $openingbal + (Yii::$app->db->createCommand("select @outval as result;")->queryScalar());
                else:
                    $initialdate='2013-04-01';
                    $enddate=date('Y-m-d');

                    $accounthead=$account['id'];
                    $command = Yii::$app->db->createCommand("CALL GetTrialBalance(:startdate, :currentdate,:accounthead,@outval)");
                    $command->bindParam(":startdate",$initialdate);
                    $command->bindParam(":currentdate", $enddate);
                    $command->bindParam(":accounthead", $accounthead);
                    $command->query();
                    //print_r($command);exit;
                    $openingbalance = $openingbal + (Yii::$app->db->createCommand("select @outval as result;")->queryScalar());
                endif;
                if($openingbalance!=0):
                    if($openingbalance<0):
                        $debit=abs($openingbalance);
                        $debitbal=number_format((float)$debit, 2);
                        $credit='';
                    else:
                        $debitbal='';
                        $credit=number_format((float)$openingbalance, 2);
                    endif;
                    $accountrows.="<tr>
                        <td>".$account['name']."</td>
                        <td style='text-align: right;'>".$debitbal."</td>
                        <td style='text-align: right;'>".$credit."</td>
                        </tr>";

                    $deb=str_replace( ',', '', $debitbal );
                    $cr=str_replace( ',', '', $credit );

                    $gdebittot=$gdebittot + (float)$deb;
                    $gcredittot=$gcredittot + (float)$cr;

                endif;
            endforeach;
            if($gdebittot!=0 || $gcredittot!=0 ){
                $datarows.="<tr><td colspan='3'><b>".$type->name."</b></td></tr>";
                $datarows.=$accountrows;
                $datarows.="<tr>
                        <td style='text-align: center;'>Group Total</td>
                        <td style='text-align: right;'>".number_format((float)$gdebittot, 2)."</td>
                        <td style='text-align: right;'>".number_format((float)$gcredittot, 2)."</td>

                        </tr>";
            }
            $deb=str_replace( ',', '', $gdebittot );
            $cr=str_replace( ',', '', $gcredittot );

            $totaldebit=$totaldebit + (float)$deb;
            $totalcredit=$totalcredit + (float)$cr;

        endforeach;

        $datarows.="
                <tr>
                    <td style='text-align: center;'>Total</td>
                    <td style='text-align: right;'>".number_format((float)$totaldebit, 2)."</td>
                    <td style='text-align: right;'>".number_format((float)$totalcredit, 2)."</td>
                </tr>";

        $datarows.="</table>";

        return $datarows;

    }


   public function actionProfitnloss()
    {
        $connection = Yii::$app->db;
        $startdate=date('d-m-Y',strtotime($_POST['fromdate']));
        $enddate=date('d-m-Y',strtotime($_POST['todate']));
        if($_POST['fromdate']!='' && $_POST['todate']!=''):
            $ar=array("startdate"=>$startdate,"enddate"=>$enddate);
        endif;
        $str=http_build_query($ar);
        if($str!=''):
            $str="?".$str;
        endif;
        /*$print="<a href='".Yii::$app->request->baseUrl."/FinanceRequests/printtrialbalance".$str."' target='_blank'><i class='glyphicon glyphicon-print'></i>Print</a>
                <a href='".Yii::$app->request->baseUrl."/FinanceRequests/exporttrialbalance".$str."' style='padding-left:25px' target='_blank'><i class='glyphicon glyphicon-export'></i>Export</a>";
        $export="<a href='".Yii::$app->request->baseUrl."/FinanceRequests/exporttrialbalance".$str."' style='padding-left:25px' target='_blank'><i class='glyphicon glyphicon-export'></i>Export</a>";*/
        $export="<div class='custom-print-btn'>   
                        <div class='icon-groups' style='top:-65px;'><a href='".Yii::$app->request->baseUrl."/financerequests/trialbalancecsv".$str."' style='padding-left:25px' class='btn btn-primary text-button'><span class='icon-print'></span>Excel</a></div>
                    </div>";
        //$sql="SELECT id,name FROM accounts_item ORDER BY name ASC ";
        $sql="SELECT type_id,name FROM account_types where type_id IN(4,8) ORDER BY sortorder ASC";
        //$sql="SELECT a.id,a.name FROM accounts_item AS a LEFT JOIN ledger_openingbalance AS b ON b.accountid = a.id WHERE b.accountid IS NULL ORDER BY a.id ASC ";
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $dataProvider=$dataReader->readAll();//print_r($dataProvider);exit;
        $datarows='';
        $month=date('m');
        if($month=='01' || $month=='02' || $month=='03'):
            $initialdate=date("Y",strtotime("-1 year")).'-04-01';
        else:
            $initialdate=date('Y').'-04-01';
        endif;
        if($_POST['fromdate']!='' && $_POST['todate']!=''):
            $dateinfo='<div class="row list-head">
                            <div class="col-md-12 text-center type">
                                <h5>Geotech Offshore Structures (P) Ltd</h5>
                                Trial Balance From <span class="date"><em class="cal-icon icon-calendar1"></em>'.date('d-m-Y',strtotime($_POST['fromdate'])).'</span> to <span class="date"><em class="cal-icon icon-calendar1"></em>'.date('d-m-Y',strtotime($_POST['todate'])).'</span>
                                <div align="right"><!--<a href="'.Yii::$app->request->baseUrl.'/financerequests/printtrialbalance'.$str.'" target="_blank"><i class="glyphicon glyphicon-print"></i>Print</a>&nbsp&nbsp&nbsp
                                <a href="'.Yii::$app->request->baseUrl.'/financerequests/exporttrialbalance'.$str.'" target="_blank"><i class="glyphicon glyphicon-export"></i>Export</a>-->'.$export.'
                                </div></div></div>';
            $condition="AND date BETWEEN '$startdate' AND '$enddate'";
        else:
            $dateinfo='<div class="row list-head">
            <div class="col-md-12 text-center type">
                <h5>Geotech Offshore Structures (P) Ltd</h5>
                Trial Balance as on <span class="date"><em class="cal-icon icon-calendar1"></em>'.date("d/m/Y").'</span>
                <div align="right"><!--<a href="'.Yii::$app->request->baseUrl.'/financerequests/printtrialbalance'.$str.'" target="_blank"><i class="glyphicon glyphicon-print"></i>Print</a>&nbsp&nbsp&nbsp
                <a href="'.Yii::$app->request->baseUrl.'/financerequests/exporttrialbalance'.$str.'" target="_blank"><i class="glyphicon glyphicon-export"></i>Export</a>-->'.$export.'
            </div></div>';
            $condition="AND date >= '$initialdate'";
        endif;
        $datarows.= $dateinfo;
        $totaldebit=0;
        $totalcredit=0;
        $datarows.='<div class="row"><div class="table-wrpr"><table class="table table-bordered"><thead style="border-bottom: #CCC solid 2px;">
                    <tr><th>Account Head</th><th>Debit</th><th>Credit</th></tr></thead><tbody>';
        //foreach($dataProvider AS $key=>$data):
            //$type=AccountTypes::model()->findByPk($data['type_id'])->name;
            //$type=AccountTypes::findOne($data['type_id'])->name;
            //$datarows.='<tr><td class="table-subhead" colspan="3"><strong>'.$type.'</strong></td></tr>';
            //$sql2="SELECT id,name FROM accounts_item WHERE account_type ='".$data['type_id']."' ORDER BY name ASC ";
                   
                   
            foreach($dataProvider AS $key=>$data):
            $type=AccountTypes::find()->where(['type_id'=>$data['type_id']])->ORDERBY(['name'=>SORT_ASC])->one();
            
            //$sql2="SELECT id,name FROM accounts_item WHERE account_type ='".$data['type_id']."' AND Status=0 ORDER BY name ASC ";
            $sql2="
            SELECT accitem.id as id,accitem.name as name, accsub.name as subgrp_name 
            FROM accounts_item as accitem 
            LEFT JOIN subgroup_accounts as subacc ON subacc.account_id = accitem.id 
            LEFT JOIN accounts_sub as accsub ON accsub.id = subacc.subgrp_id 
            WHERE accitem.account_type ='".$data['type_id']."' AND accitem.Status=0 
            ORDER BY accsub.name, accitem.name ASC";

            $command=$connection->createCommand($sql2);
            $dataReader=$command->query();
            $accounts=$dataReader->readAll();
            
            $accountrows='';
            $gdebittot=0;
            $gcredittot=0;
            $subgrp_name = '';
            foreach($accounts AS $account):
                if($account['subgrp_name'] != $subgrp_name)
                    $accountrows.="<tr><td colspan='3' style='padding-left: 15px;'><b>".$account['subgrp_name']."</b></td></tr>";
                $subgrp_name = $account['subgrp_name'];
                $sql3="SELECT balance FROM ledger_openingbalance WHERE accountid='".$account['id']."' ";
                $command=$connection->createCommand($sql3);
                $dataReader=$command->query();
                $result=$dataReader->read();
                $openingbal=$result['balance'];
                if($_POST['fromdate']!=''):
                    $initialdate='2013-04-01';
                    $enddate=date('Y-m-d',strtotime($_POST['todate']));

                    $accounthead=$account['id'];
                    $command = Yii::$app->db->createCommand("CALL GetTrialBalance(:startdate, :currentdate,:accounthead,@outval)");
                    $command->bindParam(":startdate",$initialdate);
                    $command->bindParam(":currentdate", $enddate);
                    $command->bindParam(":accounthead", $accounthead);
                    $command->query();
                    //print_r($command);exit;
                    $openingbalance = $openingbal + (Yii::$app->db->createCommand("select @outval as result;")->queryScalar());
                else:
                    $initialdate='2013-04-01';
                    $enddate=date('Y-m-d');

                    $accounthead=$account['id'];
                    $command = Yii::$app->db->createCommand("CALL GetTrialBalance(:startdate, :currentdate,:accounthead,@outval)");
                    $command->bindParam(":startdate",$initialdate);
                    $command->bindParam(":currentdate", $enddate);
                    $command->bindParam(":accounthead", $accounthead);
                    $command->query();
                    //print_r($command);exit;
                    $openingbalance = $openingbal + (Yii::$app->db->createCommand("select @outval as result;")->queryScalar());
                endif;
                if($openingbalance!=0):
                    if($openingbalance<0):
                        $debit=abs($openingbalance);
                        $debitbal=number_format((float)$debit, 2);
                        $credit='';
                    else:
                        $debitbal='';
                        $credit=number_format((float)$openingbalance, 2);
                    endif;
                    $accountrows.="<tr>
                        <td style='padding-left: 25px;'>".$account['name']."</td>
                        <td style='text-align: right;'>".$debitbal."</td>
                        <td style='text-align: right;'>".$credit."</td>
                        </tr>";

                    $deb=str_replace( ',', '', $debitbal );
                    $cr=str_replace( ',', '', $credit );
                    //$gdebittot=$gdebittot + (int)$deb;
                    //$gcredittot=$gcredittot + (int)$cr;

                    $gdebittot=$gdebittot + (float)$deb;
                    $gcredittot=$gcredittot + (float)$cr;

                endif;
            endforeach;
            if($gdebittot!=0 || $gcredittot!=0 ){
                $datarows.="<tr><td colspan='3' style='background-color: #f1f1f1;font-size: 15px;'><b>".$type->name."</b></td></tr>";
                $datarows.=$accountrows;
                $datarows.="<tr>
                        <td style='text-align: center;'>Group Total</td>
                        <td style='text-align: right;'>".number_format((float)$gdebittot, 2)."</td>
                        <td style='text-align: right;'>".number_format((float)$gcredittot, 2)."</td>

                        </tr>";
            }
            //$openingbalance = Yii::app()->db->createCommand("select @outval as result;")->queryScalar();
            $deb=str_replace( ',', '', $gdebittot );
            $cr=str_replace( ',', '', $gcredittot );
            //$totaldebit=$totaldebit + $deb;
            //$totalcredit=$totalcredit + $cr;

            $totaldebit=$totaldebit + (float)$deb;
            $totalcredit=$totalcredit + (float)$cr;

        endforeach;

        $datarows.="<tr>
                    <td style='text-align: center;'>Total</td>
                    <td style='text-align: right;'>".number_format((float)$totaldebit, 2)."</td>
                    <td style='text-align: right;'>".number_format((float)$totalcredit, 2)."</td>
                    </tr>";
        //$arr = array('result' => $datarows,'print'=>$print,'export'=>$export,'error'=>'No');
        $arr = array('result' => $datarows,'error'=>'No');
        return json_encode($arr);
    }

   public function actionBalancesheet()
    {
        $connection = Yii::$app->db;
        $startdate=date('d-m-Y',strtotime($_POST['fromdate']));
        $enddate=date('d-m-Y',strtotime($_POST['todate']));
        if($_POST['fromdate']!='' && $_POST['todate']!=''):
            $ar=array("startdate"=>$startdate,"enddate"=>$enddate);
        endif;
        $str=http_build_query($ar);
        if($str!=''):
            $str="?".$str;
        endif;
        /*$print="<a href='".Yii::$app->request->baseUrl."/FinanceRequests/printtrialbalance".$str."' target='_blank'><i class='glyphicon glyphicon-print'></i>Print</a>
                <a href='".Yii::$app->request->baseUrl."/FinanceRequests/exporttrialbalance".$str."' style='padding-left:25px' target='_blank'><i class='glyphicon glyphicon-export'></i>Export</a>";
        $export="<a href='".Yii::$app->request->baseUrl."/FinanceRequests/exporttrialbalance".$str."' style='padding-left:25px' target='_blank'><i class='glyphicon glyphicon-export'></i>Export</a>";*/
        $export="<div class='custom-print-btn'>   
                        <div class='icon-groups' style='top:-65px;'><a href='".Yii::$app->request->baseUrl."/financerequests/trialbalancecsv".$str."' style='padding-left:25px' class='btn btn-primary text-button'><span class='icon-print'></span>Excel</a></div>
                    </div>";
        //$sql="SELECT id,name FROM accounts_item ORDER BY name ASC ";
        $sql="SELECT type_id,name FROM account_types where type_id IN(6,7) ORDER BY sortorder ASC";
        //$sql="SELECT a.id,a.name FROM accounts_item AS a LEFT JOIN ledger_openingbalance AS b ON b.accountid = a.id WHERE b.accountid IS NULL ORDER BY a.id ASC ";
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $dataProvider=$dataReader->readAll();//print_r($dataProvider);exit;
        $datarows='';
        $month=date('m');
        if($month=='01' || $month=='02' || $month=='03'):
            $initialdate=date("Y",strtotime("-1 year")).'-04-01';
        else:
            $initialdate=date('Y').'-04-01';
        endif;
        if($_POST['fromdate']!='' && $_POST['todate']!=''):
            $dateinfo='<div class="row list-head">
                            <div class="col-md-12 text-center type">
                                <h5>Geotech Offshore Structures (P) Ltd</h5>
                                Trial Balance From <span class="date"><em class="cal-icon icon-calendar1"></em>'.date('d-m-Y',strtotime($_POST['fromdate'])).'</span> to <span class="date"><em class="cal-icon icon-calendar1"></em>'.date('d-m-Y',strtotime($_POST['todate'])).'</span>
                                <div align="right"><!--<a href="'.Yii::$app->request->baseUrl.'/financerequests/printtrialbalance'.$str.'" target="_blank"><i class="glyphicon glyphicon-print"></i>Print</a>&nbsp&nbsp&nbsp
                                <a href="'.Yii::$app->request->baseUrl.'/financerequests/exporttrialbalance'.$str.'" target="_blank"><i class="glyphicon glyphicon-export"></i>Export</a>-->'.$export.'
                                </div></div></div>';
            $condition="AND date BETWEEN '$startdate' AND '$enddate'";
        else:
            $dateinfo='<div class="row list-head">
            <div class="col-md-12 text-center type">
                <h5>Geotech Offshore Structures (P) Ltd</h5>
                Trial Balance as on <span class="date"><em class="cal-icon icon-calendar1"></em>'.date("d/m/Y").'</span>
                <div align="right"><!--<a href="'.Yii::$app->request->baseUrl.'/financerequests/printtrialbalance'.$str.'" target="_blank"><i class="glyphicon glyphicon-print"></i>Print</a>&nbsp&nbsp&nbsp
                <a href="'.Yii::$app->request->baseUrl.'/financerequests/exporttrialbalance'.$str.'" target="_blank"><i class="glyphicon glyphicon-export"></i>Export</a>-->'.$export.'
            </div></div>';
            $condition="AND date >= '$initialdate'";
        endif;
        $datarows.= $dateinfo;
        $totaldebit=0;
        $totalcredit=0;
        $datarows.='<div class="row"><div class="table-wrpr"><table class="table table-bordered"><thead style="border-bottom: #CCC solid 2px;">
                    <tr><th>Account Head</th><th>Debit</th><th>Credit</th></tr></thead><tbody>';
        //foreach($dataProvider AS $key=>$data):
            //$type=AccountTypes::model()->findByPk($data['type_id'])->name;
            //$type=AccountTypes::findOne($data['type_id'])->name;
            //$datarows.='<tr><td class="table-subhead" colspan="3"><strong>'.$type.'</strong></td></tr>';
            //$sql2="SELECT id,name FROM accounts_item WHERE account_type ='".$data['type_id']."' ORDER BY name ASC ";
                   
                   
            foreach($dataProvider AS $key=>$data):
            $type=AccountTypes::find()->where(['type_id'=>$data['type_id']])->ORDERBY(['name'=>SORT_ASC])->one();
            
            //$sql2="SELECT id,name FROM accounts_item WHERE account_type ='".$data['type_id']."' AND Status=0 ORDER BY name ASC ";
            $sql2="
            SELECT accitem.id as id,accitem.name as name, accsub.name as subgrp_name 
            FROM accounts_item as accitem 
            LEFT JOIN subgroup_accounts as subacc ON subacc.account_id = accitem.id 
            LEFT JOIN accounts_sub as accsub ON accsub.id = subacc.subgrp_id 
            WHERE accitem.account_type ='".$data['type_id']."' AND accitem.Status=0 
            ORDER BY accsub.name, accitem.name ASC";

            $command=$connection->createCommand($sql2);
            $dataReader=$command->query();
            $accounts=$dataReader->readAll();
            
            $accountrows='';
            $gdebittot=0;
            $gcredittot=0;
            $subgrp_name = '';
            foreach($accounts AS $account):
                if($account['subgrp_name'] != $subgrp_name)
                    $accountrows.="<tr><td colspan='3' style='padding-left: 15px;'><b>".$account['subgrp_name']."</b></td></tr>";
                $subgrp_name = $account['subgrp_name'];
                $sql3="SELECT balance FROM ledger_openingbalance WHERE accountid='".$account['id']."' ";
                $command=$connection->createCommand($sql3);
                $dataReader=$command->query();
                $result=$dataReader->read();
                $openingbal=$result['balance'];
                if($_POST['fromdate']!=''):
                    $initialdate='2013-04-01';
                    $enddate=date('Y-m-d',strtotime($_POST['todate']));

                    $accounthead=$account['id'];
                    $command = Yii::$app->db->createCommand("CALL GetTrialBalance(:startdate, :currentdate,:accounthead,@outval)");
                    $command->bindParam(":startdate",$initialdate);
                    $command->bindParam(":currentdate", $enddate);
                    $command->bindParam(":accounthead", $accounthead);
                    $command->query();
                    //print_r($command);exit;
                    $openingbalance = $openingbal + (Yii::$app->db->createCommand("select @outval as result;")->queryScalar());
                else:
                    $initialdate='2013-04-01';
                    $enddate=date('Y-m-d');

                    $accounthead=$account['id'];
                    $command = Yii::$app->db->createCommand("CALL GetTrialBalance(:startdate, :currentdate,:accounthead,@outval)");
                    $command->bindParam(":startdate",$initialdate);
                    $command->bindParam(":currentdate", $enddate);
                    $command->bindParam(":accounthead", $accounthead);
                    $command->query();
                    //print_r($command);exit;
                    $openingbalance = $openingbal + (Yii::$app->db->createCommand("select @outval as result;")->queryScalar());
                endif;
                if($openingbalance!=0):
                    if($openingbalance<0):
                        $debit=abs($openingbalance);
                        $debitbal=number_format((float)$debit, 2);
                        $credit='';
                    else:
                        $debitbal='';
                        $credit=number_format((float)$openingbalance, 2);
                    endif;
                    $accountrows.="<tr>
                        <td style='padding-left: 25px;'>".$account['name']."</td>
                        <td style='text-align: right;'>".$debitbal."</td>
                        <td style='text-align: right;'>".$credit."</td>
                        </tr>";

                    $deb=str_replace( ',', '', $debitbal );
                    $cr=str_replace( ',', '', $credit );
                    //$gdebittot=$gdebittot + (int)$deb;
                    //$gcredittot=$gcredittot + (int)$cr;

                    $gdebittot=$gdebittot + (float)$deb;
                    $gcredittot=$gcredittot + (float)$cr;

                endif;
            endforeach;
            if($gdebittot!=0 || $gcredittot!=0 ){
                $datarows.="<tr><td colspan='3' style='background-color: #f1f1f1;font-size: 15px;'><b>".$type->name."</b></td></tr>";
                $datarows.=$accountrows;
                $datarows.="<tr>
                        <td style='text-align: center;'>Group Total</td>
                        <td style='text-align: right;'>".number_format((float)$gdebittot, 2)."</td>
                        <td style='text-align: right;'>".number_format((float)$gcredittot, 2)."</td>

                        </tr>";
            }
            //$openingbalance = Yii::app()->db->createCommand("select @outval as result;")->queryScalar();
            $deb=str_replace( ',', '', $gdebittot );
            $cr=str_replace( ',', '', $gcredittot );
            //$totaldebit=$totaldebit + $deb;
            //$totalcredit=$totalcredit + $cr;

            $totaldebit=$totaldebit + (float)$deb;
            $totalcredit=$totalcredit + (float)$cr;

        endforeach;

        $datarows.="<tr>
                    <td style='text-align: center;'>Total</td>
                    <td style='text-align: right;'>".number_format((float)$totaldebit, 2)."</td>
                    <td style='text-align: right;'>".number_format((float)$totalcredit, 2)."</td>
                    </tr>";
        //$arr = array('result' => $datarows,'print'=>$print,'export'=>$export,'error'=>'No');
        $arr = array('result' => $datarows,'error'=>'No');
        return json_encode($arr);
    }



    public function actionBsreport()
    {
        $connection = \Yii::$app->db; 

        $accountsubs = AccountsSub::find()->where(['master_id'=>6])->all();

        $datarows='';

        $datarows.='<div class="row"><div class="table-wrpr"><table class="table table-bordered"><thead>
                        <tr><th>Account Head</th><th>Opening Balance</th><th>Debit</th><th>Credit</th></tr></thead><tbody>';

        $a=array();

        $akey = 'A';

        $b=array();

        //$nocount = 0;

        foreach($accountsubs as $accountsub):

            //$sql = "SELECT a.id,a.Status,a.name,a.tds,a.servicetax,a.account_type,a.resource_id,a.schedule,a.favourite,c.name AS subname,a.resource_group FROM accounts_item AS a LEFT JOIN subgroup_accounts AS b  on a.id=b.account_id LEFT JOIN accounts_sub AS c on b.subgrp_id=c.id ";
            $sql = "SELECT a.id,a.Status,a.name,a.tds,a.servicetax,a.account_type,a.resource_id,a.schedule,a.favourite,c.name AS subname,c.id AS accountsub_id,a.resource_group,d.item_id as bsitem_id,d.itemname as bsitem_name FROM accounts_item AS a LEFT JOIN subgroup_accounts AS b  on a.id=b.account_id LEFT JOIN accounts_sub AS c on b.subgrp_id=c.id INNER JOIN bsitems as d ON d.item_id=b.bsitem_id";
            //$sql .= " WHERE b.bsitem_id =" . $_POST['scheduleitem'] . " ";
            //$sql .= "WHERE a.Status=0 AND c.master_id=6 GROUP BY a.id ORDER BY a.name ASC";
            //$sql .= " WHERE a.Status=0 GROUP BY a.id ORDER BY a.name ASC";
            //$sql .= " WHERE a.Status=0 AND c.master_id=6 GROUP BY a.id ORDER BY subname ASC";
            $sql .= " WHERE a.Status=0 AND c.master_id=6 AND c.id='".$accountsub->id."' GROUP BY a.id ORDER BY bsitem_id ASC";
            //echo $sql;exit;
            $command = $connection->createCommand($sql);
            $dataReader = $command->query();
            $allaccounts = $dataReader->readAll();

            if($allaccounts){

                $gdebittot=0;
                $gcredittot=0;
                $totaldebit=0;
                $totalcredit=0;

                foreach($allaccounts AS $key => $account):

                    $sql3="SELECT balance FROM ledger_openingbalance WHERE accountid='".$account['id']."' ";
                    $command=$connection->createCommand($sql3);
                    $dataReader=$command->query();
                    $result=$dataReader->read();
                    $openingbal=$result['balance'];

                    $initialdate='2013-04-01';
                    $enddate=date('Y-m-d');

                    $accounthead=$account['id'];
                    $command = Yii::$app->db->createCommand("CALL GetTrialBalance(:startdate, :currentdate,:accounthead,@outval)");
                    $command->bindParam(":startdate",$initialdate);
                    $command->bindParam(":currentdate", $enddate);
                    $command->bindParam(":accounthead", $accounthead);
                    $command->query();
                    //print_r($command);exit;
                    $openingbalance = $openingbal + (Yii::$app->db->createCommand("select @outval as result;")->queryScalar());

                    if($openingbalance!=0):
                        //$nocount++;
                        if($openingbalance<0):
                            $debit=abs($openingbalance);
                            $debitbal=number_format((float)$debit, 2);
                            $credit='';
                        else:
                            $debitbal='';
                            $credit=number_format((float)$openingbalance, 2);
                        endif;
                        $openbalance = abs($openingbal);

                        if(!in_array($account['accountsub_id'], $a)):

                            $bkey = 1;

                            $sub_name = $akey.') '.$account['subname'];

                            $datarows.="<tr>
                                            <td colspan='4'><b>".$sub_name."</b></td>
                                        </tr>";

                            $akey++;

                            array_push($a,$account['accountsub_id']);

                        endif;

                        if(!in_array($account['bsitem_id'], $b)):

                            $bsitem_name = $bkey.') '.$account['bsitem_name'];

                            $datarows.="<tr>
                                            <td colspan='4'><b>".$bsitem_name."</b></td>
                                        </tr>";

                            $bkey++;

                            array_push($b,$account['bsitem_id']);

                        endif;

                        $datarows.="<tr>
                            <td>".$account['name']."</td>
                            <td style='text-align: right;'>".number_format((float)$openbalance, 2)."</td>
                            <td style='text-align: right;'>".$debitbal."</td>
                            <td style='text-align: right;'>".$credit."</td>
                            </tr>";

                        $deb=str_replace( ',', '', $debitbal );
                        $cr=str_replace( ',', '', $credit );

                        $gdebittot=$gdebittot + (float)$deb;
                        $gcredittot=$gcredittot + (float)$cr;

                    endif;

                    $deb=str_replace( ',', '', $gdebittot );
                    $cr=str_replace( ',', '', $gcredittot );

                    $totaldebit=$totaldebit + (float)$deb;
                    $totalcredit=$totalcredit + (float)$cr;

                endforeach;

                /*$datarows.="<tr>
                        <td style='text-align: center;' col-span='2'>Total</td>
                        <td style='text-align: right;'>".number_format((float)$totaldebit, 2)."</td>
                        <td style='text-align: right;'>".number_format((float)$totalcredit, 2)."</td>
                        </tr>";*/

            }

        endforeach;

        $datarows.='</tbody></table></div></div>';

        $arr = array('result' => $datarows,'error'=>'No');
        return json_encode($arr);

    }

    public function actionPrinttrialbalance()
    {
        $connection = Yii::$app->db;
        $startdate=$_GET['startdate'];
        $enddate=$_GET['enddate'];
        $sql="SELECT type_id,name FROM account_types ORDER BY sortorder ASC ";
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $dataProvider=$dataReader->readAll();
        $datarows='';
        $month=date('m');
        if($month=='01' || $month=='02' || $month=='03'):
            $initialdate=date("Y",strtotime("-1 year")).'-04-01';
        else:
            $initialdate=date('Y').'-04-01';
        endif;
        if($startdate!='' && $enddate!=''):
            $dateinfo="<p style='font-size: 18px;text-align: center;'>Trial Balance ".$startdate." to ".$enddate."</p>";
            $condition="AND date BETWEEN '$startdate' AND '$enddate'";
        else:
            $dateinfo="<p style='font-size: 18px;text-align: center;'>Trial Balance as on ".date("d/m/Y")."</p>";
            $condition="AND date >= '$initialdate'";
        endif;
        $datarows.="<p style='font-size: 21px;text-align: center;'>Geotech Offshore Structures (P) Ltd</p>$dateinfo";
        $datarows.="<style>table,td{border:1px solid black;border-collapse:collapse}</style>
        <table width='800' cellpadding='7px' class='table table-bordered' id='trialbaltable' style='display: table; overflow: hidden;'>
        <thead><tr>
                <th>Account Head</th>
                <th>Debit</th>
                <th>Credit</th>
            </tr>
        </thead><tbody id='trialbalitems'>";
        $totaldebit=0;
        $totalcredit=0;
        //foreach($dataProvider AS $key=>$data):
            /*$sql1="(SELECT id,account_id,creditacnt,amount,narration,type,contra FROM voucher WHERE (account_id='".$data['id']."' OR creditacnt='".$data['id']."') $condition)
                    UNION
                    (SELECT id,debitacnt AS account_id,creditacnt,amount,narration,type,contra FROM journalvoucher WHERE (debitacnt='".$data['id']."' OR creditacnt='".$data['id']."') $condition)";
            //echo $sql1;exit;
            $command=$connection->createCommand($sql1);
            $dataReader=$command->query();
            $databalance=$dataReader->readAll();
            $debittotal=0;
            $credittotal=0;
            foreach($databalance AS $databal):
                if($databal['contra']==0):
                    if($databal['creditacnt']==$data['id']):
                        $credittotal=$credittotal + $databal['amount'];
                    elseif($databal['account_id']==$data['id']):
                        $debittotal=$debittotal + $databal['amount'];
                    endif;
                else:
                    if($databal['type']=='Payment' && $databal['creditacnt']==$data['id']):
                        $credittotal=$credittotal + $databal['amount'];
                    elseif($databal['type']=='Receipt' && $databal['account_id']==$data['id']):
                        $debittotal=$debittotal + $databal['amount'];
                    endif;
                endif;
            endforeach;*/
            //$type=AccountTypes::model()->findByPk($data['type_id'])->name;
            //$type=AccountTypes::findOne($data['type_id'])->name;
            //$datarows.="<tr><td colspan='3'><b>".$type."</b></td></tr>";
            $sql2="SELECT id,name FROM accounts_item WHERE Status=0 ORDER BY name ASC ";
            $command=$connection->createCommand($sql2);
            $dataReader=$command->query();
            $accounts=$dataReader->readAll();
            $accountrows='';
            $gdebittot=0;
            $gcredittot=0;
            foreach($accounts AS $account):
                $sql3="SELECT balance FROM ledger_openingbalance WHERE accountid='".$account['id']."' ";
                $command=$connection->createCommand($sql3);
                $dataReader=$command->query();
                $result=$dataReader->read();
                if(!empty($result)){
                    $openingbal=$result['balance'];
                }else{
                    $openingbal=0;
                }
                if($startdate!=''):
                    $initialdate='2013-04-01';
                    $enddate=date('Y-m-d',strtotime($_GET['enddate']));
                    $accounthead=$account['id'];
                    $command = Yii::$app->db->createCommand("CALL GetTrialBalance(:startdate, :currentdate,:accounthead,@outval)");
                    $command->bindParam(":startdate",$initialdate);
                    $command->bindParam(":currentdate", $enddate);
                    $command->bindParam(":accounthead", $accounthead);
                    $command->query();
                    //print_r($command);exit;
                    $openingbalance = $openingbal + (Yii::$app->db->createCommand("select @outval as result;")->queryScalar());
                else:
                    $initialdate='2013-04-01';
                    $enddate=date('Y-m-d');
                    //echo $enddate;exit;
                    $accounthead=$account['id'];
                    $command = Yii::$app->db->createCommand("CALL GetTrialBalance(:startdate, :currentdate,:accounthead,@outval)");
                    $command->bindParam(":startdate",$initialdate);
                    $command->bindParam(":currentdate", $enddate);
                    $command->bindParam(":accounthead", $accounthead);
                    $command->query();
                    //print_r($command);exit;
                    $openingbalance = $openingbal + (Yii::$app->db->createCommand("select @outval as result;")->queryScalar());
                endif;
                if($openingbalance!=0):
                    if($openingbalance<0):
                        $debit=abs($openingbalance);
                        $debitbal=number_format((float)$debit, 2);
                        $credit='';
                    else:
                        $debitbal='';
                        $credit=number_format((float)$openingbalance, 2);
                    endif;
                    $accountrows.="<tr>
                        <td>".$account['name']."</td>
                        <td style='text-align: right;'>".$debitbal."</td>
                        <td style='text-align: right;'>".$credit."</td>
                        </tr>";
                    $deb=str_replace( ',', '', $debitbal );
                    $cr=str_replace( ',', '', $credit );
                    $gdebittot=$gdebittot + (float)$deb;
                    $gcredittot=$gcredittot + (float)$cr;
                endif;
            endforeach;
            $datarows.=$accountrows;
            //$datarows.="<tr>
                    //<td style='text-align: center;'>Group Total</td>
                   // <td style='text-align: right;'>".number_format((float)$gdebittot, 2)."</td>
                   // <td style='text-align: right;'>".number_format((float)$gcredittot, 2)."</td>
                   // </tr>";
            $deb=str_replace( ',', '', $gdebittot );
            $cr=str_replace( ',', '', $gcredittot );
            $totaldebit=$totaldebit + (float)$deb;
            $totalcredit=$totalcredit + (float)$cr;
        //endforeach;
        $datarows.="<tr>
                    <td style='text-align: center;'>Total</td>
                    <td style='text-align: right;'>".number_format((float)$totaldebit, 2)."</td>
                    <td style='text-align: right;'>".number_format((float)$totalcredit, 2)."</td>
                    </tr>";
        $datarows.="</tbody></table>";
        $pdf = new Pdf();
        $mpdf = $pdf->api;
        $mpdf->SetHeader('Geo Tech Construction Company Pvt Ltd'); 
        $mpdf->SetFooter('Page {PAGENO}');
        $mpdf->WriteHtml($datarows); 
        $mpdf->Output('trialbalance.pdf','I');
    }

    public function actionExporttrialbalance()
    {
        $connection = Yii::$app->db;
        $startdate=$_GET['startdate'];
        $enddate=$_GET['enddate'];
        $sql="SELECT type_id,name FROM account_types ORDER BY sortorder ASC ";
        //$sql="SELECT a.id,a.name FROM accounts_item AS a LEFT JOIN ledger_openingbalance AS b ON b.accountid = a.id WHERE b.accountid IS NULL ORDER BY a.id ASC ";
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $dataProvider=$dataReader->readAll();

        $totaldebit=0;
        $totalcredit=0;
        //foreach($dataProvider AS $key=>$data):
            //$type=AccountTypes::findOne($data['type_id'])->name;
            $items[] = array(
                "Account Head" => "",
                "Debit" => "",
                "Credit" => "",
            );

            $sql2="SELECT id,name FROM accounts_item ORDER BY name ASC ";
            $command=$connection->createCommand($sql2);
            $dataReader=$command->query();
            $accounts=$dataReader->readAll();
            $gdebittot=0;
            $gcredittot=0;
            foreach($accounts AS $account):
                if($startdate!=''):
                    $initialdate='2013-04-01';
                    $enddate=date('Y-m-d',strtotime($enddate));

                    $accounthead=$account['id'];
                    $command = Yii::$app->db->createCommand("CALL GetTrialBalance(:startdate, :currentdate,:accounthead,@outval)");
                    $command->bindParam(":startdate",$initialdate);
                    $command->bindParam(":currentdate", $enddate);
                    $command->bindParam(":accounthead", $accounthead);
                    $command->query();
                    //print_r($command);exit;
                    $openingbalance = (Yii::$app->db->createCommand("select @outval as result;")->queryScalar());
                else:
                    $initialdate='2013-04-01';
                    $enddate=date('Y-m-d');

                    $accounthead=$account['id'];
                    $command = Yii::$app->db->createCommand("CALL GetTrialBalance(:startdate, :currentdate,:accounthead,@outval)");
                    $command->bindParam(":startdate",$initialdate);
                    $command->bindParam(":currentdate", $enddate);
                    $command->bindParam(":accounthead", $accounthead);
                    $command->query();
                    //print_r($command);exit;
                    $openingbalance = (Yii::$app->db->createCommand("select @outval as result;")->queryScalar());
                endif;
                if($openingbalance!=0):
                    if($openingbalance<0):
                        $debit=abs($openingbalance);
                        $debitbal=number_format((float)$debit, 2);
                        $credit='';
                    else:
                        $debitbal='';
                        $credit=number_format((float)$openingbalance, 2);
                    endif;
                    $items[] = array(
                        "Account Head" => $account['name'],
                        "Debit" => $debitbal,
                        "Credit" => $credit,
                    );
                    $deb=str_replace( ',', '', $debitbal );
                    $cr=str_replace( ',', '', $credit );
                    $gdebittot=$gdebittot + (int)$deb;
                    $gcredittot=$gcredittot + (int)$cr;
                endif;
            endforeach;
            //$items[] = array(
                //"Account Head" => "Group Total",
                //"Debit" => number_format((float)$gdebittot, 2),
                //"Credit" => number_format((float)$gcredittot, 2),
            //);
            $deb=str_replace( ',', '', $gdebittot );
            $cr=str_replace( ',', '', $gcredittot );
            $totaldebit=$totaldebit + (int)$deb;
            $totalcredit=$totalcredit + (int)$cr;
        //endforeach;
        $items[] = array(
            "Account Head" => "Total",
            "Debit" => number_format((float)$totaldebit, 2),
            "Credit" => number_format((float)$totalcredit, 2),
        );
        //print_r($items);exit;
        $filename = 'trialbalance.csv';       
        header("Content-type: text/csv");       
        header("Content-Disposition: attachment; filename=$filename");       
        $output = fopen("php://output", "w");       
        $header = array_keys($items[0]);       
        fputcsv($output, $header);       
        foreach($items as $row)       
        {  
            fputcsv($output, $row);  
        }       
        fclose($output);
    }

    public function actionPrintcashbook()
    {
        //$project=$_GET['Projectid'];
        $startdate=$_GET['startdate'];
        $enddate=$_GET['enddate'];
        $account=$_GET['account'];
        $connection = Yii::$app->db;
        $sql="SELECT name FROM accounts_item WHERE id=".$account."";
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $value=$dataReader->read();
        if($account!=''){
            $condition=" AND (account_id='".$account."' OR creditacnt='".$account."')";
        }else{
            $condition=" ";
        }       
        $datarows='';
        $datarows.="<p style='font-size: 21px;text-align: center;'>Cash Book of ".$value['name']."</p><p style='font-size: 18px;text-align: center;'>".date("d-m-Y",strtotime($startdate))." to ".date("d-m-Y",strtotime($enddate))."</p>";
        $sql="SELECT id,DATE_FORMAT(date,'%d/%m/%y') AS date,amount,account_id,creditacnt,narration,type,voucher_no,contra FROM voucher WHERE payment='1' ".$condition." AND date BETWEEN '$startdate' AND '$enddate' ORDER BY UNIX_TIMESTAMP(date) ASC";
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $dataProvider=$dataReader->readAll();
        $sql2="select date from voucher where date=(select max(date) from voucher where date < '".$startdate."' AND payment='1')";
        $command=$connection->createCommand($sql2);
        $dataReader=$command->query();
        $data=$dataReader->read();
        $sql1="SELECT id FROM accounts_item WHERE account_type='1'";
        $command=$connection->createCommand($sql1);
        $dataReader=$command->query();
        $data=$dataReader->read();
       // $accountid=$data['id'];
         $accountid= $account;
        $sql3="SELECT balance FROM ledger_openingbalance WHERE accountid='$accountid' ";
        $command=$connection->createCommand($sql3);
        $dataReader=$command->query();
        $data=$dataReader->read();
        $openingbal=$data['balance'];
        $initialdate='2014-04-01';
        $enddate=date('Y-m-d', strtotime($startdate .' -1 day'));
        if($data){
            $command = Yii::$app->db->createCommand("CALL GetBalance(:startdate, :currentdate,NULL,1,NULL,".$accountid.",@outval)");
        }
        else{
            $command = Yii::$app->db->createCommand("CALL GetBalance(:startdate, :currentdate,NULL,1,NULL,NULL,@outval)");
        }
        
        $command->bindParam(":startdate",$initialdate);
        $command->bindParam(":currentdate", $enddate);
        $command->query();

        $newstartdate=date('Y-m-d', strtotime($startdate .' -1 day'));

        $credtcondtn = '';
        $debitcondtn = '';
        $accntcondtn = '';

        if($accountid!=0){
            $credtcondtn.= "creditacnt='".$accountid."' AND ";
            $debitcondtn.= "debitacnt='".$accountid."' AND ";
            $accntcondtn.= "account_id='".$accountid."' AND ";
        }

        $debittotalsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$accntcondtn." contra=0 AND date BETWEEN '".$initialdate."' AND '".$newstartdate."'";
        $command=$connection->createCommand($debittotalsql);
        $dataReader=$command->query();
        $debittotal=$dataReader->read();

        $credittotalsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$credtcondtn." contra=0 AND date BETWEEN '".$initialdate."' AND '".$newstartdate."'";
        $command=$connection->createCommand($credittotalsql);
        $dataReader=$command->query();
        $credittotal=$dataReader->read();

        $contradebitsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$accntcondtn." contra=1 AND type='Receipt' AND`date` BETWEEN '".$initialdate."' AND '".$newstartdate."'";
        $command=$connection->createCommand($contradebitsql);
        $dataReader=$command->query();
        $contradebit=$dataReader->read();

        $contracreditsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$credtcondtn." contra=1 AND type='Payment' AND`date` BETWEEN '".$initialdate."' AND '".$newstartdate."'";
        $command=$connection->createCommand($contracreditsql);
        $dataReader=$command->query();
        $contracredit=$dataReader->read();

        $journeldebitsql = "SELECT SUM(`amount`) as amount FROM `journalvoucher` WHERE ".$debitcondtn." date BETWEEN '".$initialdate."' AND '".$newstartdate."'";
        $command=$connection->createCommand($journeldebitsql);
        $dataReader=$command->query();
        $journeldebit=$dataReader->read();

        $journelcreditsql = "SELECT SUM(`amount`) as amount FROM `journalvoucher` WHERE ".$credtcondtn." date BETWEEN '".$initialdate."' AND '".$newstartdate."'";
        $command=$connection->createCommand($journelcreditsql);
        $dataReader=$command->query();
        $journelcredit=$dataReader->read();

        if(is_null($debittotal['amount'])){
                $debittotalamt = 0;
        }
        else{
            $debittotalamt = $debittotal['amount'];
        }

        if(is_null($credittotal['amount'])){
            $credittotalamt = 0;
        }
        else{
            $credittotalamt = $credittotal['amount'];
        }

        if(is_null($contradebit['amount'])){
            $contradebitamt = 0;
        }
        else{
            $contradebitamt = $contradebit['amount'];
        }

        if(is_null($contracredit['amount'])){
            $contracreditamt = 0;
        }
        else{
            $contracreditamt = $contracredit['amount'];
        }

        if(is_null($journeldebit['amount'])){
            $journeldebitamt = 0;
        }
        else{
            $journeldebitamt = $journeldebit['amount'];
        }

        if(is_null($journelcredit['amount'])){
            $journelcreditamt = 0;
        }
        else{
            $journelcreditamt = $journelcredit['amount'];
        }


        $checkRbal = ($credittotalamt + $contracreditamt + $journelcreditamt) - ($debittotalamt + $contradebitamt + $journeldebitamt);

        
        
        $openbalance = $openingbal + $checkRbal;

        //$openbalance = $openingbal + (Yii::$app->db->createCommand("select @outval as result;")->queryScalar());
        $totalpamount=0;
        $totalramount=0;
        $datarows.="<style>table,td{border:1px solid black;border-collapse:collapse}</style>
        <table cellpadding='7px' class='table table-bordered' id='cashbooktable' style='display: table; overflow: hidden;'>
        <thead><tr>
                <th>Date</th>
                <th>Voucher No</th>
                <th>Account Head</th>
                <th>Narration</th>
                <th >Receipt</th>
                <th >Payment</th>
            </tr>
        </thead><tbody id='cashbookitems'>";
        $recamount='';
        if(count($dataProvider)>0):
            if($openbalance<0):
                $rectot=abs($openbalance);
                $recamount=number_format((float)$rectot, 2);
                $payamount='';                
            else:
                $recamount='';
                $paytot=abs($openbalance);
                $payamount=number_format((float)$paytot, 2);
            endif;
            $datarows.="<tr><td colspan='4'>Opening Balance</td><td style='text-align: right;'>$recamount</td><td style='text-align: right;'>$payamount</td></tr>";
            foreach($dataProvider AS $key=>$data):
                $sql="SELECT name FROM accounts_item WHERE id='".$data['account_id']."' ";
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();
                $debit=$dataReader->read();
                $sql="SELECT name FROM accounts_item WHERE id='".$data['creditacnt']."' ";
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();
                $credit=$dataReader->read();
                if($data['contra']==0){
                        if($data['creditacnt']==$accountid):
                            $pamount=$data['amount'];
                            $ramount='';
                            $totalpamount=$totalpamount + $pamount;
                            $accountname=$debit['name'];
                        elseif($data['account_id']==$accountid):
                            $pamount='';
                            $ramount=$data['amount'];
                            $totalramount=$totalramount + $ramount;
                            $accountname=$credit['name'];
                        endif;
                    }
                elseif($data['type']=='Payment')
                {
                    $pamount=$data['amount'];
                    $ramount='';
                    $totalpamount=$totalpamount + $pamount;
                    $accountname=$debit['name'];
                }
                else
                {
                    $pamount='';
                    $ramount=$data['amount'];
                    $totalramount=$totalramount + $ramount;
                    $accountname=$credit['name'];
                }
                
                    $datarows.="<tr>
                                <td >".$data['date']."</td>
                                <td >".$data['voucher_no']."</td>
                                <td>".$accountname."</td>
                                <td>".$data['narration']."</td>
                                <td style='text-align: right;'>".number_format((float)$ramount, 2)."</td>
                                <td style='text-align: right;'>".number_format((float)$pamount, 2)."</td>
                            </tr>";
               

            endforeach;
            if($openbalance<0):
                $debittot=abs($openbalance);
                $totalramount=$totalramount + $debittot;
            else:            
                $credtot=abs($openbalance);
                $totalpamount=$totalpamount + $credtot;
            endif;
            $rec=str_replace( ',', '', $recamount );
            $pay=str_replace( ',', '', $payamount );
            //$balance=$totalpamount-$totalramount;
            $balance=$totalramount - $totalpamount;
            $closingbal=$balance;
            //$closingbal=$this->getbalance($openbalance,$balance);
            if($closingbal>0):
                $rectot=abs($closingbal);
                $recamount=number_format((float)$rectot, 2);
                $payamount='';
            else:
                $recamount='';
                $paytot=abs($closingbal);
                $payamount=number_format((float)$paytot, 2);
            endif;
            $datarows.="<tr><td colspan='4'></td><td style='text-align: right;'>".number_format((float)$totalramount, 2)."</td><td style='text-align: right;'>".number_format((float)$totalpamount, 2)."</td></tr>";
            $datarows.="<tr><td colspan='4'>Closing Balance</td><td style='text-align: right;'>$recamount</td><td style='text-align: right;'>$payamount</td></tr>";
        else:
            if($openbalance<0):$recamount='';
                $rectot=abs($openbalance);$recamount=number_format((float)$rectot, 2);
                $payamount='';
            else:
                $paytot=abs($openbalance);$payamount=number_format((float)$paytot, 2);
            endif;
            $datarows.="<tr><td colspan='4'>Opening Balance</td><td style='text-align: right;'>$recamount</td><td style='text-align: right;'>$payamount</td></tr>";
            $datarows.="<tr><td colspan='6' style='text-align: center;'>No Cash Book Entries Found</td></tr>";
            $datarows.="<tr><td colspan='4'>Closing Balance</td><td style='text-align: right;'>$recamount</td><td style='text-align: right;'>$payamount</td></td></tr>";
        endif;
        $datarows.="</tbody></table>";
        $pdf = new Pdf();
        $mpdf = $pdf->api;
        $mpdf->SetHeader('Geo Tech Construction Company Pvt Ltd'); 
        $mpdf->SetFooter('Page {PAGENO}');
        $mpdf->WriteHtml($datarows); 
        $mpdf->Output('cashbook.pdf','I');
    }

    public function actionPrintjournal()
    {
        $project=$_GET['Projectid'];
        $startdate=$_GET['startdate'];
        $enddate=$_GET['enddate'];
        $account=$_GET['account'];
        $connection = Yii::$app->db;
        $sql="SELECT Name FROM projects WHERE Project_Id='$project'";
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $value=$dataReader->read();
        if($account!=''){
            $condition=" AND (debitacnt='".$account."' OR creditacnt='".$account."')";
        }else{
            $condition=" ";
        }       
        if($project){
            $condition .= " AND a.project_id='$project'";
        }

        $projName = 'All';
        if(isset($value['Name']))
            $projName = $value['Name'];

        $datarows='';
        $datarows.="<p style='font-size: 21px;text-align: center;'>Journal Register of ".$projName."</p><p style='font-size: 18px;text-align: center;'>".$startdate." to ".$enddate."</p>";
        $sql1="SELECT a.id,DATE_FORMAT(a.date,'%d/%m/%y') AS date,a.amount,a.debitacnt,a.creditacnt,a.narration,a.voucher_no,b.name FROM journalvoucher as a inner join accounts_item as b on a.debitacnt=b.id WHERE 1  ".$condition." AND a.date BETWEEN '$startdate' AND '$enddate' ORDER BY a.date ASC";
        //echo $sql1;exit;
        $command=$connection->createCommand($sql1);
        $dataReader=$command->query();
        $dataProvider=$dataReader->readAll();
        $datarows.="<style>table,td{border:1px solid black;border-collapse:collapse}</style>
        <table cellpadding='7px' class='table table-bordered' id='journalbooktable' style='display: table; overflow: hidden;font-size: 8pt;'>
        <thead><tr>
                <th>Date</th>
                <th>Voucher No</th>
                <th>Debit Account Head</th>
                <th>Credit Account Head</th>
                <th>Narration</th>
                <th >Debit</th>
                <th >Credit</th>
                </tr>
        </thead><tbody id='journalbookitems'>";
        if(count($dataProvider)>0):
            foreach($dataProvider AS $key=>$data):
                $sql="SELECT name FROM accounts_item WHERE id='".$data['debitacnt']."'";
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();
                $debitaccount=$dataReader->read();
                $sql="SELECT name FROM accounts_item WHERE id='".$data['creditacnt']."'";
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();
                $creditaccount=$dataReader->read();
                $datarows.="<tr>
                                <td >".$data['date']."</td>
                                <td >".$data['voucher_no']."</td>
                                <td>".$debitaccount['name']."</td>
                                <td></td>
                                <td>".$data['narration']."</td>
                                <td style='text-align: right;'>".number_format((float)$data['amount'], 2)."</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>".$data['date']."</td>
                                <td>".$data['voucher_no']."</td>
                                <td></td>
                                <td>".$creditaccount['name']."</td>
                                <td>".$data['narration']."</td>
                                <td></td>
                                <td style='text-align: right;'>".number_format((float)$data['amount'], 2)."</td>
                            </tr>";
            endforeach;
        else:
            $datarows.='<tr id="nodata"><td colspan="7" style="text-align: center;">No Journal Found</td></tr>';
        endif;
        $datarows.="</tbody></table>";
        $pdf = new Pdf();
        $mpdf = $pdf->api;
        $mpdf->SetHeader('Geo Tech Construction Company Pvt Ltd'); 
        $mpdf->SetFooter('Page {PAGENO}');
        $mpdf->WriteHtml($datarows); 
        $mpdf->Output('journalbook.pdf','I');
    }

    public function actionPrintbankbook()
    {
       // $place=$_GET['Place'];
       // $project=$_GET['Projectid'];
        $bankid=$_GET['Bank'];
        $startdate=$_GET['startdate'];
        $enddate=$_GET['enddate'];
        $account=$_GET['Bank'];
                
        $connection = Yii::$app->db;
       
        $sql="SELECT name FROM accounts_item WHERE id='".$account."'";
       
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $value=$dataReader->read();
        $condition='';
        if($account!=''):
            $condition=" AND (account_id='".$account."' OR creditacnt='".$account."')";
        endif;      
        $datarows='';
        $datarows.="<p style='font-size: 21px;text-align: center;'>Bank Book of ".$value['name']."</p><p style='font-size: 18px;text-align: center;'>".date("d-m-Y",strtotime($startdate))." to ".date("d-m-Y",strtotime($enddate))."</p>";
        //$sql="SELECT id,DATE_FORMAT(date,'%d/%m/%y') AS date,amount,account_id,creditacnt,narration,type,voucher_no,contra FROM voucher WHERE place='$project' AND payment='2' AND date BETWEEN '$startdate' AND '$enddate' ORDER BY date ASC";
       // if($project!='' && $place!=''){
           // $sql="SELECT id,DATE_FORMAT(date,'%d/%m/%y') AS date,amount,account_id,creditacnt,narration,type,voucher_no,contra,project_id FROM voucher WHERE project_id='$project' ";
       // }
       // elseif($project=='' && $place!=''){
            $sql="SELECT id,DATE_FORMAT(date,'%d/%m/%y') AS date,amount,account_id,creditacnt,narration,type,voucher_no,contra,project_id FROM voucher WHERE";
      //  }
       // else{
           // $sql="SELECT id,DATE_FORMAT(date,'%d/%m/%y') AS date,amount,account_id,creditacnt,narration,type,voucher_no,contra,project_id FROM voucher WHERE project_id='$project'";

       // }
        
        if($bankid!='0')
            $sql.=" (bank_id='".$bankid."' OR account_id='".$bankid."') ".$condition;
        $sql.="AND payment='2' AND date BETWEEN '$startdate' AND '$enddate' ORDER BY UNIX_TIMESTAMP(date) ASC";
        //echo $sql;exit;
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $dataProvider=$dataReader->readAll();

       /* if($project!=''){
            $sql2="select date from voucher where date=(select max(date) from voucher where date < '".$startdate."' AND project_id='$project' AND payment='2')";
        }
        else{*/
            $sql2="select date from voucher where date=(select max(date) from voucher where date < '".$startdate."' AND payment='2')";
       //}

        //$sql2="select date from voucher where date=(select max(date) from voucher where date < '".$startdate."' AND project_id='$project' AND payment='1')";
        $command=$connection->createCommand($sql2);
        $dataReader=$command->query();
        $data=$dataReader->read();
        //print_r($data);exit;
        //$sql1="SELECT open_balance FROM opening_balance WHERE projectid='$projectid' AND payment='1' ";
        /*$month=date('m');
        if($month=='01' || $month=='02' || $month=='03'):
            $initialdate=date("Y",strtotime("-1 year")).'-04-01';
        else:
            $initialdate=date('Y').'-04-01';
        endif;*/
        $initialdate='2014-04-01';
        $enddate=date('Y-m-d', strtotime($startdate .' -1 day'));
        //$command = Yii::app()->db->createCommand("CALL GetBalance(:startdate, :currentdate,".$project.",1,NULL,@outval)");
        //echo $project; exit;

        if($bankid=='0'):
            $command = Yii::$app->db->createCommand("CALL GetBalance(:startdate, :currentdate,NULL,2,NULL,".$bankid.",@outval)");
        else:
            $command = Yii::$app->db->createCommand("CALL GetBalance(:startdate, :currentdate,NULL,2,".$bankid.",".$bankid.",@outval)");
        endif;

        //if($place!='' && $project!=''):
           /* if($bankid=='0'):
                //$command = Yii::app()->db->createCommand("CALL GetBalance(:startdate, :currentdate,".$projectid.",2,NULL,".$bankid.",@outval)");
                $command = Yii::$app->db->createCommand("CALL GetChartBalance(:startdate, :currentdate,NULL,NULL,2,NULL,NULL,@outval)");
            else:
                //$command = Yii::app()->db->createCommand("CALL GetBalance(:startdate, :currentdate,".$projectid.",2,".$bankid.",".$bankid.",@outval)"); 
                $command = Yii::$app->db->createCommand("CALL GetChartBalance(:startdate, :currentdate,NULL,NULL,2,".$bankid.",".$bankid.",@outval)");           
            endif;*/
        //endif;

        //if($place=='' && $project!=''):
            if($bankid=='0'):
                //$command = Yii::app()->db->createCommand("CALL GetBalance(:startdate, :currentdate,".$projectid.",2,NULL,".$bankid.",@outval)");
                $command = Yii::$app->db->createCommand("CALL GetChartBalance(:startdate, :currentdate,NULL,NULL,2,NULL,NULL,@outval)");
            else:
                //$command = Yii::app()->db->createCommand("CALL GetBalance(:startdate, :currentdate,".$projectid.",2,".$bankid.",".$bankid.",@outval)"); 
                $command = Yii::$app->db->createCommand("CALL GetChartBalance(:startdate, :currentdate,NULL,NULL,2,".$bankid.",".$bankid.",@outval)");           
            endif;
       // endif;

        //if($place!='' && $project==''):
            if($bankid=='0'):
                //$command = Yii::app()->db->createCommand("CALL GetBalance(:startdate, :currentdate,".$projectid.",2,NULL,".$bankid.",@outval)");
                $command = Yii::$app->db->createCommand("CALL GetChartBalance(:startdate, :currentdate,NULL,NULL,2,NULL,NULL,@outval)");
            else:
                //$command = Yii::app()->db->createCommand("CALL GetBalance(:startdate, :currentdate,".$projectid.",2,".$bankid.",".$bankid.",@outval)"); 
                $command = Yii::$app->db->createCommand("CALL GetChartBalance(:startdate, :currentdate,NULL,NULL,2,".$bankid.",".$bankid.",@outval)");           
            endif;
       // endif;

        $command->bindParam(":startdate",$initialdate);
        $command->bindParam(":currentdate", $enddate);
        $command->query();
        if($bankid!='0'){
        $sql3="SELECT balance FROM ledger_openingbalance WHERE accountid='$bankid' ";
        $command=$connection->createCommand($sql3);
        $dataReader=$command->query();
        $data=$dataReader->read();
        $openingbal=$data['balance'];
        }else{
            $openingbal=0;
        }

        $newstartdate=date('Y-m-d', strtotime($startdate .' -1 day'));

        /*$paymenttotal = Voucher::find()->where(['type'=> 'Payment'])->andWhere(['bank_id'=> $bankid])->andWhere(['payment'=> '2'])->andWhere(['between', 'date', $initialdate, $newstartdate])->sum('amount');
        $receipttotal = Voucher::find()->where(['type'=> 'Receipt'])->andWhere(['bank_id'=> $bankid])->andWhere(['payment'=> '2'])->andWhere(['between', 'date', $initialdate, $newstartdate])->sum('amount');
        if(is_null($paymenttotal)){
            $paymenttotal = 0;
        }
        if(is_null($receipttotal)){
            $receipttotal = 0;
        }
        $checkRbal = $paymenttotal - $receipttotal;*/

        $credtcondtn = '';
        $debitcondtn = '';
        $accntcondtn = '';

        if($bankid!=0){
            $credtcondtn.= "creditacnt='".$bankid."' AND ";
            $debitcondtn.= "debitacnt='".$bankid."' AND ";
            $accntcondtn.= "account_id='".$bankid."' AND ";
        }

        $debittotalsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$accntcondtn." contra=0 AND date BETWEEN '".$initialdate."' AND '".$newstartdate."'";
        $command=$connection->createCommand($debittotalsql);
        $dataReader=$command->query();
        $debittotal=$dataReader->read();

        $credittotalsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$credtcondtn." contra=0 AND date BETWEEN '".$initialdate."' AND '".$newstartdate."'";
        $command=$connection->createCommand($credittotalsql);
        $dataReader=$command->query();
        $credittotal=$dataReader->read();

        $contradebitsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$accntcondtn." contra=1 AND type='Receipt' AND`date` BETWEEN '".$initialdate."' AND '".$newstartdate."'";
        $command=$connection->createCommand($contradebitsql);
        $dataReader=$command->query();
        $contradebit=$dataReader->read();

        $contracreditsql = "SELECT SUM(`amount`) as amount FROM `voucher` WHERE ".$credtcondtn." contra=1 AND type='Payment' AND`date` BETWEEN '".$initialdate."' AND '".$newstartdate."'";
        $command=$connection->createCommand($contracreditsql);
        $dataReader=$command->query();
        $contracredit=$dataReader->read();

        $journeldebitsql = "SELECT SUM(`amount`) as amount FROM `journalvoucher` WHERE ".$debitcondtn." date BETWEEN '".$initialdate."' AND '".$newstartdate."'";
        $command=$connection->createCommand($journeldebitsql);
        $dataReader=$command->query();
        $journeldebit=$dataReader->read();

        $journelcreditsql = "SELECT SUM(`amount`) as amount FROM `journalvoucher` WHERE ".$credtcondtn." date BETWEEN '".$initialdate."' AND '".$newstartdate."'";
        $command=$connection->createCommand($journelcreditsql);
        $dataReader=$command->query();
        $journelcredit=$dataReader->read();

        if(is_null($debittotal['amount'])){
            $debittotalamt = 0;
        }
        else{
            $debittotalamt = $debittotal['amount'];
        }

        if(is_null($credittotal['amount'])){
            $credittotalamt = 0;
        }
        else{
            $credittotalamt = $credittotal['amount'];
        }

        if(is_null($contradebit['amount'])){
            $contradebitamt = 0;
        }
        else{
            $contradebitamt = $contradebit['amount'];
        }

        if(is_null($contracredit['amount'])){
            $contracreditamt = 0;
        }
        else{
            $contracreditamt = $contracredit['amount'];
        }

        if(is_null($journeldebit['amount'])){
            $journeldebitamt = 0;
        }
        else{
            $journeldebitamt = $journeldebit['amount'];
        }

        if(is_null($journelcredit['amount'])){
            $journelcreditamt = 0;
        }
        else{
            $journelcreditamt = $journelcredit['amount'];
        }

        $checkRbal = ($credittotalamt + $contracreditamt + $journelcreditamt) - ($debittotalamt + $contradebitamt + $journeldebitamt);

        $openbalance = $openingbal + $checkRbal;

       // $openbalance = $openingbal + (Yii::$app->db->createCommand("select @outval as result;")->queryScalar());
        $totalpamount=0;
        $totalramount=0;
        $datarows.="<style>table,td{border:1px solid black;border-collapse:collapse}</style>
        <table cellpadding='7px' class='table table-bordered' id='bankbooktable' style='display: table; overflow: hidden;'>
        <thead><tr>
                <th>Date</th>
                <th>Voucher No</th>
                <th>Project</th>
                <th>Account Head</th>
                <th>Narration</th>
                <th >Receipt</th>
                <th >Payment</th>
            </tr>
        </thead><tbody id='bankbookitems'>";
        if(count($dataProvider)>0):
            if($openbalance<0):
                $rectot=abs($openbalance);
                $recamount=number_format((float)$rectot, 2);
                $payamount='';
            else:
                $recamount='';
                $paytot=abs($openbalance);
                $payamount=number_format((float)$paytot, 2);                
            endif;
            $datarows.="<tr><td colspan='5'>Opening Balance</td><td style='text-align: right;'>$recamount</td><td style='text-align: right;'>$payamount</td></tr>";
            foreach($dataProvider AS $key=>$data):
                $sql="SELECT name FROM accounts_item WHERE id='".$data['account_id']."' ";
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();
                $debit=$dataReader->read();
                $sql="SELECT name FROM accounts_item WHERE id='".$data['creditacnt']."' ";
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();
                $credit=$dataReader->read();
                if($data['contra']==0){
                    if($data['creditacnt']==$bankid):
                        $pamount=$data['amount'];
                        $ramount='';
                        $totalpamount=$totalpamount + $pamount;
                        $accountname=$debit['name'];
                    elseif($data['account_id']==$bankid):
                        $pamount='';
                        $ramount=$data['amount'];
                        $totalramount=$totalramount + $ramount;
                        $accountname=$credit['name'];
                    endif;
                }
                elseif($data['type']=='Payment')
                {
                    $pamount=$data['amount'];
                    $ramount='';
                    $totalpamount=$totalpamount + $pamount;
                    $accountname=$debit['name'];
                }
                else
                {
                    $pamount='';
                    $ramount=$data['amount'];
                    $totalramount=$totalramount + $ramount;
                    $accountname=$credit['name'];
                }
                $voucherproject = Projects::findOne($data['project_id']);
                if($voucherproject){
                    $projname = $voucherproject->Name;
                }
                else{
                    $projname = '';
                }
                if($data['contra']=='0'):
                    $datarows.="<tr>
                                <td >".$data['date']."</td>
                                <td >".$data['voucher_no']."</td>
                                <td>".$projname."</td>
                                <td>".$accountname."</td>
                                <td>".$data['narration']."</td>
                                <td style='text-align: right;'>".$ramount."</td>
                                <td style='text-align: right;'>".$pamount."</td>
                            </tr>";
                else:
                    $datarows.="<tr>
                                <td >".$data['date']."</td>
                                <td >".$data['voucher_no']."</td>
                                <td>".$projname."</td>
                                <td>".$accountname."</td>
                                <td>".$data['narration']."</td>
                                <td style='text-align: right;'>".$ramount."</td>
                                <td style='text-align: right;'>".$pamount."</td>
                            </tr>";
                endif;

            endforeach;
            if($openbalance<0):
                $debittot=abs($openbalance);
                $totalramount=$totalramount + $debittot;
            else:            
                $credtot=abs($openbalance);
                $totalpamount=$totalpamount + $credtot;
            endif;
            $rec=str_replace( ',', '', $recamount );
            $pay=str_replace( ',', '', $payamount );
            //$balance=$totalpamount - $totalramount;
            $balance= $totalramount - $totalpamount;
            $closingbal=$balance;
            //$closingbal=$this->getbalance($openbalance,$balance);
            if($closingbal>0):
                $rectot=abs($closingbal);$recamount=number_format((float)$rectot, 2);$payamount='';
            else:
                $recamount='';$paytot=abs($closingbal);$payamount=number_format((float)$paytot, 2);
            endif;
            $datarows.="<tr><td colspan='5'></td><td style='text-align: right;'>".number_format((float)$totalramount, 2)."</td><td style='text-align: right;'>".number_format((float)$totalpamount, 2)."</td></tr>";
            $datarows.="<tr><td colspan='5'>Closing Balance</td><td style='text-align: right;'>$recamount</td><td style='text-align: right;'>$payamount</td></tr>";
        else:
            if($openbalance>0):
                $rectot=abs($openbalance);$recamount=number_format((float)$rectot, 2);$payamount='';
            else:
                $recamount='';$paytot=abs($openbalance);$payamount=number_format((float)$paytot, 2);
            endif;
            $datarows.="<tr><td colspan='5'>Opening Balance</td><td style='text-align: right;'>$recamount</td><td style='text-align: right;'>$payamount</td></tr>";
            $datarows.="<tr><td colspan='7' style='text-align: center;'>No Bank Book Entries Found</td></tr>";
            $datarows.="<tr><td colspan='5'>Closing Balance</td><td style='text-align: right;'>$recamount</td><td style='text-align: right;'>$payamount</td></td></tr>";
        endif;
        $datarows.="</tbody></table>";
        $pdf = new Pdf();
        $mpdf = $pdf->api;
        $mpdf->SetHeader('Geo Tech Construction Company Pvt Ltd'); 
        $mpdf->SetFooter('Page {PAGENO}');
        $mpdf->WriteHtml($datarows); 
        $mpdf->Output('bankbook.pdf','I');
    }

    public function actionProexpenditure(){

        $datarows='';

        $connection = \Yii::$app->db;

        $uid = Yii::$app->user->Id; 
        $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();

        $projectid=$projuser->projectid;

        $project = Projects::findOne($projectid);

        $startdate='';
        $enddate='';

        if(isset($_POST['from']) && isset($_POST['to'])){
            if($_POST['from']!='' && $_POST['to']!=''){
                $startdate= $_POST['from'];
                $enddate= $_POST['to'];
            }
        }

        $datarows.="<div class='row list-head'>
                        <div class='col-md-12 text-center type'>
                            <h5>Project Expenditure of ".$project->Name."</h5>
                        </div>
                        <div class='search-and-actions-wrpr row'>
                            <div class='content-search-wrpr col-md-12 col-sm-12'>
                                <input class='form-control datepickerfrom' id='proexpofromdate' name='proexpofromdate' type='date' placeholder='Select Date' value='$startdate'>
                                <input class='form-control datepickerto'  id='proexpotodate' name='proexpotodate'  type='date' placeholder='Select Date' value='$enddate'>                        
                                <button id='proexpotypesearch' class='btn btn-primary' type='button'><span class='icon-search5'></span></button>
                            </div>
                        </div>
                    </div>";

        $datarows.='
                    <div class="row"><div class="table-wrpr">
                        <table class="table table-bordered">
                        <thead>
                            <tr><th>Expense</th><th>Amount</th><th></th>
                            </tr>
                        </thead>
                        <tbody>';

        $sql="SELECT id,name FROM accounts_sub WHERE master_id='1' AND Status=0 ORDER BY sortorder ASC,id DESC";
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $subgroups=$dataReader->readAll();
        $projexptot=0;

        foreach($subgroups AS $subgroup):
            $sql="SELECT COUNT(account_id) as count FROM subgroup_accounts AS a INNER JOIN accounts_item AS b ON a.account_id=b.id WHERE a.subgrp_id='".$subgroup['id']."' AND b.account_type IN (5,8,6)";
            $command=$connection->createCommand($sql);
            $dataReader=$command->query();
            $dataval=$dataReader->read();
            $datasubgroupows='';
            if($dataval['count']>0):
                $datasubgroupows.="<tr><td colspan='5'><span style='font-weight: bold;'>".$subgroup['name']."</span></td></tr>";
            endif;
            
            $sql1="SELECT a.subgrp_id,b.id,b.name FROM subgroup_accounts AS a INNER JOIN accounts_item AS b ON a.account_id=b.id WHERE a.subgrp_id='".$subgroup['id']."' AND accountschedule_id=0 AND b.account_type IN (5,8,6) GROUP BY b.id";
            $command=$connection->createCommand($sql1);
            $dataReader=$command->query();
            $accounts=$dataReader->readAll();
            $grptotal=0;
            $dataaccountrows='';
            if(!empty($accounts)):
                foreach($accounts AS $accounthead):
                    
                    $sql3="SELECT type,balance FROM ledger_openingbalance WHERE accountid='".$accounthead['id']."' ";
                    $command=$connection->createCommand($sql3);
                    $dataReader=$command->query();
                    $result=$dataReader->read();
                    $openingbal= (isset($result['balance'])) ? $result['balance'] : 0;
                    $closingbal= $openingbal + $this->closingbalance($accounthead['id'],$projectid,$startdate,$enddate);
                    //$closingbal=$this->closingbalance(5,$projectid,$startdate,$enddate);
                    //echo $closingbal; exit;
                    if($closingbal!=0 ):
                        $content = $this->Ledgeritems($projectid,$accounthead['id'],$startdate,$enddate);
                        $dataaccountrows.="<tr><td><div class='hover' data-tooltip='tooltip".$accounthead['id']."' style='cursor:pointer;'><a>".$accounthead['name']."</a>
                        <div class='tooltiptable' id='tooltip".$accounthead['id']."' style='width:500px;'>
                        <table cellpadding='0' cellspacing='0' width='100%'><tr><th>Date</th><th>Narration</th><th>Debit Amount</th><th>Credit Amount</th></tr>".$content."</table>
                        </div></div></td><td style='text-align: right;'>".number_format((float)abs($closingbal), 2)."</td><td> </td></tr>";
                    endif;
                    $grptotal=$grptotal + abs($closingbal);
                endforeach;
                if($dataaccountrows!=''):
                $datarows.=$datasubgroupows.$dataaccountrows."<tr><td style='text-align: center;font-weight: bold;'>Total</td><td> </td><td style='text-align: right;'>".number_format((float)abs($grptotal), 2)."</td></tr>";
                // echo $datarows;
                endif;
            //$datarows.="<tr><td style='text-align: center;font-weight: bold;'>Total</td><td>".number_format((float)abs($grptotal), 2)."</td></tr>";
            $projexptot=$projexptot + $grptotal;
            endif;
        endforeach;

        $datarows.="<tr><td style='text-align: center;font-weight: bold;'>Project Expenditure Total</td>
                    <td> </td><td style='text-align: right;'>".number_format((float)abs($projexptot), 2)."</td></tr>";

        $datarows.="<tr><td colspan='4' style='font-weight: bold;'>Advances</td></tr>";
        // echo $datarows;
        //$sql2="SELECT id,name FROM accounts_item WHERE account_type IN (5,7,14,15,16)";
        $sql2="SELECT a.id,a.name FROM accounts_item as a INNER JOIN subgroup_accounts as b ON a.id = b.account_id INNER JOIN bsitems as c ON b.bsitem_id = c.item_id WHERE a.account_type IN (5,7,14,15,16) AND b.bsitem_id = 12 AND a.Status=0 ORDER BY a.name ASC";
        $command=$connection->createCommand($sql2);
        $dataReader=$command->query();
        $liabilities=$dataReader->readAll();
        $liabtotal=0;
        foreach($liabilities AS $liability):
            $sql3="SELECT type,balance FROM ledger_openingbalance WHERE accountid='".$liability['id']."' ";
            $command=$connection->createCommand($sql3);
            $dataReader=$command->query();
            $result=$dataReader->read();
            //$openingbal=$result['balance'];
            $openingbal= (isset($result['balance'])) ? $result['balance'] : 0;

            //$liabclosingbal= $this->closingbalance($liability['id'],$projectid,$startdate,$enddate);
            $liabclosingbal = $openingbal + $this->closingbalance($liability['id'],$projectid,$startdate,$enddate);         
            if($liabclosingbal!=0 && $liabclosingbal<0):

                $liacloseblnce = number_format((float)abs($liabclosingbal), 2);

                if($liacloseblnce!=0){

                    if($projectid==72 && $liability['id']!=599){
                        $datarows.="<tr><td>".$liability['name']."</td><td style='text-align: right;'>".number_format((float)abs($liabclosingbal), 2)."</td><td> </td></tr>";
                        $liabtotal=$liabtotal + abs($liabclosingbal);
                    }
                    elseif($projectid!=72){
                        $datarows.="<tr><td>".$liability['name']."</td><td style='text-align: right;'>".number_format((float)abs($liabclosingbal), 2)."</td><td> </td></tr>";
                        $liabtotal=$liabtotal + abs($liabclosingbal);
                    }

                }
                
            endif;
        endforeach;
        $grandtotal=$liabtotal + $projexptot;
        $datarows.="<tr><td style='text-align: center;font-weight: bold;'>Total</td><td> </td><td style='text-align: right;'>".number_format((float)abs($liabtotal), 2)."</td></tr>";
        $datarows.="<tr><td style='text-align: center;font-weight: bold;'>Grand Total</td><td> </td><td style='text-align: right;'>".number_format((float)abs($grandtotal), 2)."</td></tr>";
        $incomeclosingbal=$this->closingbalance(140,$projectid,$startdate,$enddate);
        $datarows.="<tr><td style='text-align: center;font-weight: bold;'>Contract Income</td><td style='text-align: right;'>".number_format((float)abs($incomeclosingbal), 2)."</td><td> </td></tr>";


        $datarows.='</tbody></table></div></div>';
        
        $arr = array('result' => $datarows,'error'=>'No');
        return json_encode($arr);

    }

    function closingbalance($acntid,$projid,$startdate,$enddate)
    {
        $connection = \Yii::$app->db;
        $initialdate='2013-04-01';
        if($startdate!='' && $enddate!=''):
            $condition="AND  date BETWEEN '$startdate' AND '$enddate'";
        else:
            $condition="AND date >= '$initialdate'";
        endif;
        if($projid!=''):
                //$condition1=" AND (project_id='".$projid."' OR place='".$projid."')";
                //$condition2=" AND (place='".$projid."' OR project_id='".$projid."')";
                $condition1=" AND (project_id='".$projid."')";
                $condition2=" AND (place='".$projid."')";
        else:
            $condition1="";
            $condition2="";
        endif;
        //echo $condition1;exit;
        $sql="(SELECT amount,type,creditacnt,bank_id,contra,account_id FROM voucher WHERE (account_id='".$acntid."' OR creditacnt='".$acntid."') $condition1 $condition)
              UNION ALL
              (SELECT amount,type,creditacnt,bank_id,contra,debitacnt AS account_id FROM journalvoucher WHERE (debitacnt='".$acntid."' OR creditacnt='".$acntid."') $condition2 $condition)";
        //echo $sql;exit;
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $dataProvider=$dataReader->readAll();
        //print_r($dataProvider);
        $debitamount=0;
        $creditamount=0;
        $credittotal=0;
        $debittotal=0;
        foreach($dataProvider AS $key=>$data):
            if($data['contra']==0):
                if($data['creditacnt']==$acntid):
                    $creditamount=number_format((float)$data['amount'], 2);
                    $debitamount='';
                    $credittotal=$credittotal + $data['amount'];
                elseif($data['account_id']==$acntid):
                    $debitamount=number_format((float)$data['amount'], 2);
                    $creditamount='';
                    $debittotal=$debittotal + $data['amount'];
                endif;
            else:
                if($data['type']=='Payment' && $data['creditacnt']==$acntid ):
                    $creditamount=number_format((float)$data['amount'], 2);
                    $debitamount='';
                    $credittotal=$credittotal + $data['amount'];
                elseif($data['type']=='Receipt' && $data['account_id']==$acntid):
                    $debitamount=number_format((float)$data['amount'], 2);
                    $creditamount='';
                    $debittotal=$debittotal + $data['amount'];

                endif;
            endif;
            //echo $creditamount.' - '.$debitamount.' - '.$credittotal.' - '.$debittotal.'<br>';
            //echo $datarows;exit;
        endforeach;
        $acountbal=$credittotal - $debittotal;
        return $acountbal;
        //$closingbal=$this->getbalance($openbalance,$acountbal);
    }

    public function Ledgeritems($projectid,$accountid,$startdate1,$enddate1)
    {
        $connection = \Yii::$app->db;
        //$startdate=date('Y-m-d',strtotime($startdate1));
        //$enddate=date('Y-m-d',strtotime($enddate1));

        $sql="SELECT Name FROM accounts_item WHERE id='".$accountid."'";
        // echo $sql;exit;
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $value=$dataReader->read();
        /*$month=date('m');
        if($month=='01' || $month=='02' || $month=='03'):
            $initialdate=date("Y",strtotime("-1 year")).'-04-01';
        else:
            $initialdate=date('Y').'-04-01';
        endif;*/
        $initialdate='2013-04-01';
        if($startdate1!='' && $enddate1!=''):
            $condition="AND date BETWEEN '$startdate1' AND '$enddate1'";
        else:
            $condition="AND date >= '$initialdate'";
        endif;

        $sql="(SELECT id,voucher_no,DATE_FORMAT(date,'%d/%m/%y') AS date,narration,amount,type,creditacnt,bank_id,contra,account_id,'Voucher' AS rawtype FROM voucher WHERE (account_id='".$accountid."' OR creditacnt='".$accountid."') AND project_id='".$projectid."' $condition )
              UNION
              (SELECT id,voucher_no,DATE_FORMAT(date,'%d/%m/%y') AS date,narration,amount,type,creditacnt,bank_id,contra,debitacnt AS account_id,'Journal' AS rawtype FROM journalvoucher WHERE (debitacnt='".$accountid."' OR creditacnt='".$accountid."') AND place='".$projectid."' $condition ) ORDER BY DATE_FORMAT(date,'%d/%m/%y') ASC,id DESC";
        //echo $sql;exit;
        $command=$connection->createCommand($sql);
        $dataReader=$command->query();
        $dataProvider=$dataReader->readAll();

        $datarows='';
        $debitamount=0;

        if($startdate1!=''):
            $initialdate='2014-03-31';
            $enddate=date('Y-m-d', strtotime($startdate1 .' -1 day'));

            $accounthead=$accountid;
            $command = Yii::$app->db->createCommand("CALL GetLedgerBalance(:startdate, :currentdate,:place,:accounthead,:project,@outval)");
            $command->bindParam(":startdate",$initialdate);
            $command->bindParam(":currentdate", $enddate);

            $place=$projectid;
            $project=0;
            $command->bindParam(":place", $place);
            $command->bindParam(":accounthead", $accounthead);
            $command->bindParam(":project", $project);
            $command->query();

        else:
            $initialdate='2014-03-31';
            $enddate=date("Y",strtotime("-1 year")).'-03-31';
            //echo $initialdate.'<br>'.$enddate;exit;
            $accounthead=$accountid;
            $command = Yii::$app->db->createCommand("CALL GetLedgerBalance(:startdate, :currentdate,:place,:accounthead,:project,@outval)");
            $command->bindParam(":startdate",$initialdate);
            $command->bindParam(":currentdate", $enddate);
            /*if($_POST['place']==12):
                $place='NULL';
            else:
                $place=$_POST['place'];
            endif;*/
            $place=$projectid;
            $project=0;
            $command->bindParam(":place", $place);
            $command->bindParam(":accounthead", $accounthead);
            $command->bindParam(":project", $project);
            $command->query();

        endif;
        //echo $accounthead;exit;
        //$enddate=date('Y-m-d', strtotime($startdate .' -1 day'));
        $credittotal=0;
        $debittotal=0;
        $total=0;
        // echo count($dataProvider);exit;
        if(!empty($dataProvider)):
            // if($openbalance<0):$debittot=abs($openbalance);$debamount=number_format((float)$debittot, 2);$credamount='';else:$debamount='';$credamount=number_format((float)$openbalance, 2);endif;
            // $datarows.="<tr><td colspan='4' style='text-align: center;'>Opening Balance</td><td style='text-align: right;'>$debamount</td><td style='text-align: right;'>$credamount</td>".(Yii::app()->user->isAdmin()?'<td></td>':'')."</tr>";
            foreach($dataProvider AS $key=>$data):
                // $deletebutton="<td><button type='button' class='btn btn-primary deleteledger' value='".$data[id]."' data-type='".$data['rawtype']."' id='deleteledger".$data[id]."' title='Delete Ledger Entry'> <span class='glyphicon glyphicon-trash'></span></button></td>";
                $sql="SELECT Name FROM accounts_item WHERE id='".$accountid."'";
                // echo $sql;exit;
                $command=$connection->createCommand($sql);
                $dataReader=$command->query();
                $credit=$dataReader->read();
                if($data['contra']==0):
                    // echo $_POST['accountid'];
                    if($data['creditacnt']==$accountid):
                        $creditamount=number_format((float)$data['amount'], 2);
                        $debitamount='';
                        $credittotal=$credittotal + $data['amount'];
                    elseif($data['account_id']==$accountid):
                        $debitamount=number_format((float)$data['amount'], 2);
                        $creditamount='';
                        $debittotal=$debittotal + $data['amount'];
                    endif;
                    $datarows.="<tr id='ledgeraw".$data['id']."'>

                            <td>".$data['date']."</td>
                            <td>".$data['narration']."</td>
                            <td style='text-align: right;'>".$debitamount."</td>
                            <td style='text-align: right;'>".$creditamount."</td>
                            </tr>";
                else:
                    if($data['type']=='Payment' && $data['creditacnt']==$_POST['accountid'] ):
                        $creditamount=number_format((float)$data['amount'], 2);
                        $debitamount='';
                        $credittotal=$credittotal + $data['amount'];
                        $datarows.="<tr id='ledgeraw".$data['id']."'>
                            <td>".$data['date']."</td>
                            <td>".$data['narration']."</td>
                            <td style='text-align: right;'>".$debitamount."</td>
                            <td style='text-align: right;'>".$creditamount."</td>
                            </tr>";
                    elseif($data['type']=='Receipt' && $data['account_id']==$_POST['accountid']):
                        $debitamount=number_format((float)$data['amount'], 2);
                        $creditamount='';
                        $debittotal=$debittotal + $data['amount'];
                        $datarows.="<tr id='ledgeraw".$data['id']."'>
                            <td>".$data['date']."</td>
                            <td>".$data['narration']."</td>
                            <td style='text-align: right;'>".$debitamount."</td>
                            <td style='text-align: right;'>".$creditamount."</td>
                            </tr>";
                    endif;
                endif;
            $total=$total + $data['amount'];
            endforeach;

            if($credittotal>$debittotal){
                $difamount = $credittotal - $debittotal;
            }else{
                $difamount = $debittotal - $credittotal;
            }
            
            $datarows.="<tr><td colspan='2'>SubTotal</td><td style='text-align: right'>".number_format((float)$debittotal, 2)."</td><td style='text-align: right'>".number_format((float)$credittotal, 2)."</td></tr>

                        <tr><td colspan='2'>Total</td><td colspan='2' style='text-align: right'>".number_format((float)$difamount, 2)."</tr>";
        endif;
        // echo $datarows;
        return $datarows;
    }

    public function actionScheduleditems(){
        $accid = $_POST['accnt_id']; 
        $schedule = Bsitems::find()->where(['accnt_id'=>$accid])->andWhere(['status'=>0])->all();
        $datarows = '';
        
        if($schedule)
        {   $datarows.= '<option value="0">select schedule item</option>';
            foreach ($schedule as $key => $schedules) {
              
                $datarows.= '<option value="'.$schedules->item_id.'">'.$schedules->itemname.'</option>';
            } 

        }
        

        $arr = array('result'=>$datarows, 'error'=>'No');
        return json_encode($arr);
        
    }

}
