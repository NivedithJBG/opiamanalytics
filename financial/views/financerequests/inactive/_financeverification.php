<?php 
use amnah\yii2\user\models\User;
use app\models\AccountsItem;
use app\models\UserAccounts;
use app\models\Voucher;
use app\models\LedgerOpeningbalance;
use app\models\UserProjects;

?>

<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/Fundverification.js" type="text/javascript"></script>
<style>

    tbody#requestappritems .form-control {
        font-size: 14px;
    }
</style>

<!-- Finance Verification tab -->
<div class="container procu-accordion">
    <div class="row">
        <div class="col-md-12">   
            <div class="panel-group acco-one-active" >
                <div class="panel panel-default acco-two  FinanceVerifi-tab tab" id="FinanceVerifi-tab">
                    <input type="radio" id="rd5" name="rd">
                    <div class="panel-heading" >
                        <h4 class="panel-title acc_trigger">
                            <a  href="#">
                            <span class="icon-dollar1 acc_trigger"></span>Finance Verification</a>
                        </h4>
                    </div>
                    <div  class="tab-content cOrder-body panel-collapse " >
                        <div class="panel-body" >
                            <div class="search-and-content-wrpr" >
                                <div class="content-wrpr">
                                    <div class="account-heads-cards-wrpr">
                                        <div class="row">
                                            
                                            <?php
                                                $userid = Yii::$app->user->id;
                                                $user = User::find()->where(['id'=>$userid])->one();
                                                $useraccount = UserAccounts::find()->where(['user_id'=>$userid])->orderBy(['account_id' => SORT_ASC])->one();
                                                $userProjects = UserProjects::find()->where(['userid'=>$userid])->all();
                                                if( $user->superuser == 1 || $user->superuser == 2){
                                                    foreach($userProjects as $userProject){
                                                        $accountitem = AccountsItem::find()->where(['projectid'=> $userProject->projectid])->all();
                                                        $initialdate='2013-04-01';
                                                        $startdate=date('Y-m-d');
                                                        
                                                        foreach($accountitem as $bankslist){
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
                                                        ?>    
                                                        <div class="col-md-3">
                                                            <!--card starts here -->
                                                            <div class="card" id="selectbank" data-id="<?= $bankslist->id; ?>">
                                                                <input name="ah" type="radio" />
                                                                <div class="card-header">
                                                                    <span class="icon-library1"></span>
                                                                    <div class="account-head-number">
                                                                        <span>Accounthead</span>
                                                                        <span><?= $bankslist->name; ?></span>
                                                                    </div>
                                                                </div>
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-md-6 account-head-number">
                                                                        <span>Opening Balance</span>
                                                                        <span><?= number_format($openbalance,2); ?></span>
                                                                    </div>
                                                                    <div class="col-md-6 text-right account-head-number">
                                                                        <span>Total Amount</span>
                                                                        <span id="Accounthead<?= $bankslist->id; ?>">0.00</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="card-footer">
                                                                <div class="row">
                                                                    <div class="col-md-6  icon-groups">
                                                                        <a href="#" class="btn btn-primary icon-plus add-fv-btn" id="selectbank" data-id="<?= $bankslist->id; ?>"></a>
                                                                        <input type="hidden" id="selectbankname_<?= $bankslist->id; ?>" value="<?= $bankslist->name; ?>" />	
                                                                    </div>
                                                                    <div class="col-md-6 text-right account-head-number">
                                                                        <span>Closing Balance</span>
                                                                        <span><?= number_format($openbalance,2); ?></span>
                                                                    </div>
                                                                </div>	
                                                            </div>
                                                            </div>
                                                            <!--card end here -->
                                                        </div>
                                                        <?php
                                                        }
                                                        $bankLists = array("Axis Bank - 915020065069515"=>"169","SIB - OD - 0025081000002165"=>"631");
                                                        foreach($bankLists as $bankname => $code) {
                                                            
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
                                                            
                                                            $datarows = '';
                                                            $datarows.= ' <div class="col-md-3">
                                                            <div class="card " id="selectbank" data-id="'.$code.'">
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
                                                                    <span>Total Amount</span>
                                                                    <span>0.00</span>
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
                                                                        <span>'.number_format($openbalance,2).'</span>
                                                                    </div>
                                                                </div>	
                                                            </div>
                                                        </div>
                                                        </div>';
                                                        echo $datarows;
                                                    }  
                                                }
                                            }else{
                                                foreach($userProjects as $userProject){
                                                    $accountitem = AccountsItem::find()->where(['projectid'=> $userProject->projectid])->all();
                                                    $initialdate='2013-04-01';
                                                    $startdate=date('Y-m-d');
            
                                                    foreach($accountitem as $bankslist){
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
                                                    ?>    
                                                    <div class="col-md-3">
                                                        <!--card starts here -->
                                                        <div class="card" id="selectbank" data-id="<?= $bankslist->id; ?>">
                                                            <input name="ah" type="radio" />
                                                            <div class="card-header">
                                                                <span class="icon-library1"></span>
                                                                <div class="account-head-number">
                                                                    <span>Accounthead</span>
                                                                    <span><?= $bankslist->name; ?></span>
                                                                </div>
                                                            </div>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-md-6 account-head-number">
                                                                    <span>Opening Balance</span>
                                                                    <span><?= number_format($openbalance,2); ?></span>
                                                                </div>
                                                                <div class="col-md-6 text-right account-head-number">
                                                                    <span>Total Amount</span>
                                                                    <span>0.00</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card-footer">
                                                            <div class="row">
                                                                <div class="col-md-6  icon-groups">
                                                                    <a href="#" class="btn btn-primary icon-plus add-fv-btn" id="selectbank" data-id="<?= $bankslist->id; ?>"></a>
                                                                    <input type="hidden" id="selectbankname_<?= $bankslist->id; ?>" value="<?= $bankslist->name; ?>" />	
                                                                </div>
                                                                <div class="col-md-6 text-right account-head-number">
                                                                    <span>Closing Balance</span>
                                                                    <span><?= number_format($openbalance,2); ?></span>
                                                                </div>
                                                            </div>	
                                                        </div>
                                                        </div>
                                                        <!--card end here -->
                                                    </div>
                                                    <?php
                                                    }    
                                                }
                                            }    
                                            ?>
                                           	
                                        
                                        </div> 
                                    </div> 
                                    <div class="account-heads-table-wrpr row">
								        <div class="col-md-12">
                                            <div id="displayaddrows">
                                            
                                            </div>	
                                        </div>
                                        <!-- <div class="col-md-12 text-center">
                                            <div class="form-groups">
                                                <button class="btn btn-primary "><span class="icon-file3"></span>Save as Draft</button>
                                                <button class="btn btn-primary "><span class="icon-check"></span>Approve</button>
                                            </div>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
   
<!-- Finance Verification tab-end -->















