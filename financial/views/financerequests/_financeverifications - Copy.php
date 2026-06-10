<?php 
use amnah\yii2\user\models\User;
use app\models\AccountsItem;
use app\models\UserAccounts;
use app\models\Voucher;
use app\models\LedgerOpeningbalance;
use app\models\UserProjects;
use app\models\FinanceRequests;

?>

<!-- Finance Verification tab -->
                 
<div class="panel panel-default acco-one FinanceVerifi-tab tab" id="FinanceVerifi-tab">
    <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/finance/Fundverifications.js" type="text/javascript"></script>
    <style>
        tbody#requestappritems .form-control {
            font-size: 14px;
        }
    </style>
    <input type="radio" id="rd5" class="fundreqradio" name="rd">
    <div class="panel-heading" >
      <h4 class="panel-title">
        <a href="#">
        <span class="icon-dollar1"></span>Fund Requests</a>
      </h4>
    </div>
    <div  class="tab-content cOrder-body panel-collapse " >
        <div class="panel-body" >
            <div class="search-and-content-wrpr" >
                <div class="content-wrpr" id="fin-Veri-tab-content">
                    <div class="account-heads-cards-wrpr">
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
                                <div id="displayaddrows">
                                
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















