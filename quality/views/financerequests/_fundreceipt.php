<?php 
use amnah\yii2\user\models\User;
use app\models\AccountsItem;
use app\models\CashbankuserSelection;

?>

<div class="panel panel-default fundreceipt-tab acco-two tab">
	<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/finance/receipt.js" type="text/javascript"></script>

	<div class="panel-heading" id="selecteduseraccount">
        
         <h4 class="panel-title" id="fundreceiptqradio">
        <a data-toggle="collapse" data-parent="#accordionfin" href="#collapsereceipt">
        <span class="icon-dollar1"></span>Cash/Bank Receipts</a>
      </h4>
  
    </div>
    <div id="collapsereceipt" class="tab-content cOrder-body panel-collapse collapse">
    	<div class="panel-body" >
            <div class="search-and-content-wrpr" >
            	<div class="content-wrpr" id="">

            		<div class="account-heads-cards-wrpr">
                        <div class="col-md-12" style="padding-bottom: 30px;">
                            
                                <label style="text-align: center;font-size: 15px;">Cash/Bank Receipt</label>
                           
                        </div>
                        <div class="row">
                            <div class="preloader" id="fin-preloader" style="display: none;" align="center">
                                <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                            </div>
                            <!-- fin-verification Head -->
                            <div class="fund-header" id="fund-header">
                                
                            </div>
                            <!-- fin-verification Head-end -->
                        </div>
                        
                    </div> 

                    <div class="account-heads-table-wrpr row">
                        <div class="col-md-12">
                            <!-- fin-verification inline-Body-start -->
                            <div id="fin-tab-body">
                                <div id="receiptcashbankvouchadd"> </div>
                                <div id="displayaddreceiptrows" style="display:none;">
                                
                                </div>

                                <div id="funrec-Approval-listing">
                                
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