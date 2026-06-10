<div class="panel panel-default activity-report-tab tab tab-wrapper acco-eleven">
<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/operations/newactivityreport.js" type="text/javascript"></script>
			  <!-- <input type="radio" class="new_act_reprt" id="rd5" name="rd"> -->
				<div class="panel-heading" >
				  <h4 class="panel-title" id="new_act_reprt">
					<a data-toggle="collapse" data-parent="#accordionoper" href="#collapseactivity">
					<span class="icon-files1"></span>Activity Report</a>
				  </h4>
				</div>
				<div id="collapseactivity" class="tab-content cOrder-body panel-collapse collapse">
				  <div class="panel-body ">				  
					<div class="search-and-content-wrpr">					
						<div class="search-and-actions-wrpr row">
							<div class="content-search-wrpr col-md-6 col-sm-6 text-left ">								
                                
								<input type="text" class="form-control datepicker edit_current_date" placeholder="Date" id="current_date" value="<?php echo date("d-m-Y");?>" data-id="1" />
							</div>							
							<div class="col-md-4 col-sm-4"></div>
							<div class="content-action-wrpr col-md-2 col-sm-2">
                            <input type="hidden" name="progressPrject" id="progrep_project" class="form-control" value="" />								
                                <button class="btn btn-primary btn-hist" id="act_pr_hist" title="History" style="cursor:auto;"> History</button>
								<button class="btn btn-primary btn-back" id="act_pr_back" title="Back" style="cursor:auto;display:none;"> Back</button>
								<button class="btn btn-primary btn-act" id="act_pr_main" title="Report" style="cursor:auto;opacity:0;display: none;"><span class="icon-check"></span> Report</button>							
							</div>
						</div>
						<div class="col-md-12 text-center" style="padding-bottom: 35px;">
                            <div class="row actheads">
                                <label style="font-size: 15px;">Activity Report</label>
                            </div>
                           
                        </div>
						<div class="content-wrpr">
							<!-- form starts here -->
								
							<!-- form ends here -->
							
							<!-- edit form starts here -->
							
							
                            <!-- edit form ends here -->
                            
							<!-- list start here -->
							<div class="asset-register-list-wrpr data-content-list">
                                <div class="preloader"style="display: none;" align="center">
                                    <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                                </div>
                                <form id="activity-task">
                                <div id="activitytaskitems"></div>
                                <div id="success-message"></div>
								<div id="activitycompletehist" style="display:none;"></div>	
                                </form>  
							</div>
							<!-- list end here -->
						</div>
					</div>
				  
				  </div>
				</div>
			  </div>