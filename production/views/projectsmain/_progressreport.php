<div class="panel panel-default activity-report-tab tab tab-wrapper acco-six">
<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/operations/progressreport.js" type="text/javascript"></script>
				  <!-- <input type="radio" class="progress_act_reprt" id="rd5" name="rd"> -->
					<div class="panel-heading" >
					  <h4 class="panel-title" id="progress_act_reprt">
						<a data-toggle="collapse" data-parent="#accordionprojindex" href="#collapseschedulerep">
						<span class="icon-files1"></span>Reporting</a>
					  </h4>
					</div>
					<div id="collapseschedulerep" class="tab-content cOrder-body panel-collapse collapse">
					  <div class="panel-body">

					<div class="search-and-content-wrpr" id="pgrsrpt">
						<div class="col-md-12 text-center" style="padding-top: 35px;">
							<div class="row prgheads">
                            <label style="font-size: 15px;">Progress Report</label>
                        </div>
                    </div>
                    <form id="schedule-task">
						<div class="search-and-actions-wrpr row">
							<div class="content-search-wrpr col-md-6 col-sm-6 text-left ">
								<input type="hidden" name="select_report_date" id="select_report_date" value="<?php echo date("d-m-Y");?>" />
							</div>
							<div class="col-md-4 col-sm-4"></div>
							<div class="content-action-wrpr col-md-2 col-sm-2">
								<button type="button" class="btn btn-primary activity_history_btn" id="activity_history_btn">History</button>
								<button type="button" class="btn btn-primary activity_back_btn" id="activity_back_btn">Back</button>
								<input type="hidden" id="activity_pr_main"/>
							</div>
						</div>
						<div class="content-wrpr">
							<div class="asset-register-list-wrpr data-content-list">
	                            <div class="preloader" style="display: none;" align="center">
	                                <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
	                            </div>
	                            <div id="schedule_report_activityitems"></div>
	                            <div id="activitycompletehist"></div>
	                            <div id="success-messages"></div>
	                        </div>
						</div>
					</form>

					</div>

				  </div>
				</div>
			  </div>
