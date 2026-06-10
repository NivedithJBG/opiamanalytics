<?php 
use amnah\yii2\user\models\User;
use app\models\AccountsItem;
use app\models\UserAccounts;
use app\models\Voucher;
use app\models\LedgerOpeningbalance;
use app\models\UserProjects;
use app\models\FinanceRequests;
use app\models\Projects;

?>

<div class="panel panel-default acco-two tab Financeapproval-tab" id="Financeapproval-tab">
    <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/finance/_financeapprovals.js" type="text/javascript"></script>
    <style>

        tbody#requestappritems .form-control {
            font-size: 14px;
        }
    </style>
    <input type="radio" id="rd5" class="fundverradio" name="rd">
					
	<div class="panel-heading" >
	  <h4 class="panel-title">
		<a  href="#">
		<span class="icon-playlist_add_check"></span>Fund Approval</a>
	  </h4>
	</div>
	<div  class="tab-content cOrder-body panel-collapse ">
        <div class="panel-body">
			<div class="search-and-content-wrpr">
				<div class="content-wrpr" id="Fin-app-tab-content">
					<div class="account-heads-cards-wrpr">
						<div class="row">
                            <!-- fin-verification Head -->
                            <div class="fin-header" id="fin-ver-header">
                            
                            </div>
						</div>
					</div>
					
                    <div class="fund-approval-list-wrpr">
                        <div class="row">
                            <div class="col-md-3 type">
                                <div id="fin-Approval-header" style="display: none;">
                                    <label>Project</label>
                                    <input type="hidden" id="projectBank" value="">
                                    <select class="form-control fin-app-project" id="fin-app-project">
                                        <option value="none">All</option>
                                    <?php
                                    $project = Projects::find()->where(['Status' => 0])->andWhere(['Project_Delete_Status' => 0])->all();
                                    foreach($project AS $list){
                                        echo '<option value="'.$list->Project_Id.'">'.$list->Name.'</option>';
                                    }
                                    ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-9">
                            </div>
                            <div id="fin-Approval-body" style="display: none;">
                                <div id="fin-Approval-content">
                                    
                                </div> 
                                    
                            <!-- fa-list loop -->
                           
                            <!-- fa-list loop end -->
                            

                            </div>

                        </div>

                    </div>
				</div>
			</div>		
	
        </div>
	</div>
</div>
