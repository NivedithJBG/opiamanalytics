<?php 
use amnah\yii2\user\models\User;
use app\models\AccountsItem;
use app\models\UserAccounts;
use app\models\Voucher;
use app\models\LedgerOpeningbalance;
use app\models\UserProjects;
use app\models\FinanceRequests;
use app\models\CashbankuserSelection;

?>

<!-- Finance Verification tab -->
                 
<div class="panel panel-default acco-one FinanceVerifi-tab tab" id="FinanceVerifi-tab">
    <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/finance/Fundverifications.js" type="text/javascript"></script>
    <style>
        tbody#requestappritems .form-control {
            font-size: 14px;
        }
    </style>
    <!-- <input type="radio" id="rd5" class="fundreqradio" name="rd"> -->
    <div class="panel-heading" id="selectedaccount">
      
    <h4 class="panel-title" id="fundreqradio">
        <a data-toggle="collapse" data-parent="#accordionfin" href="#collapsecashbank">
        <span class="icon-dollar1"></span>Cash/Bank Payments</a>
      </h4>
 
    </div>
    <div id="collapsecashbank" class="tab-content cOrder-body panel-collapse collapse">
        <div class="panel-body" >
            <div class="search-and-content-wrpr" >
                <div class="content-wrpr" id="fin-Veri-tab-content">
                    <div class="account-heads-cards-wrpr">
                        <div class="col-md-12" style="padding-bottom: 30px;">
                            
                                <label style="text-align: center;font-size: 15px;">Cash/Bank Payments</label>
                           
                        </div>
                        <div class="row">
                            <div class="preloader" id="fin-preloader" style="display: none;" align="center">
                                <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                            </div>
                            <!-- fin-verification Head -->
                            <div class="fin-header" id="fin-header">
                                
                            </div>
                            <!-- fin-verification Head-end -->
                        </div>
                        <span class="aprovalsent" style="color:green;display:none;">Fund request has been sent for approval</span>
                    </div> 
                    <div class="account-heads-table-wrpr row">
                        <div class="col-md-12">
                            <!-- fin-verification inline-Body-start -->
                            <div id="fin-tab-body">
                                <div id="cashbankvouchadd"> </div>
                                <div id="displayaddrows" style="display:none;">
                                
                                </div>

                                <div id="fin-Approval-listing">
                                
                                </div>
                            </div>
                            <!-- fin-verification inline-Body--end -->
                                
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















