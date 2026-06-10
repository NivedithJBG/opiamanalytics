<div class="panel panel-default activity-report-tab tab tab-wrapper acco-seven">
<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/operations/progressreport.js" type="text/javascript"></script>
  <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/operations/clientbill.js" type="text/javascript"></script>
			  <!-- <input type="radio" class="progress_act_reprt" id="rd5" name="rd"> -->
				<div class="panel-heading" >
				  <h4 class="panel-title" id="progress_act_reprt">
					<a data-toggle="collapse" data-parent="#accordionoper" href="#collapseschedulerep">
					<span class="icon-files1"></span>Reporting</a>
				  </h4>
				</div>
				<div id="collapseschedulerep" class="tab-content cOrder-body panel-collapse collapse">
				  <div class="panel-body ">


			<!-- 	<ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#rpt"><span class="icon-document-text"></span> Progress Report</a></li>
                
                </ul> -->


                  <div class="container">
                <ul class="nav nav-tabs nav-justified mb-3" id="headersz">
                <li class="active"><a data-toggle="tab" href="#homepr" style="background: #F5F5F5;"><span class="icon-document-text"></span> Progress Report</a></li>
                <li><a data-toggle="tab" href="#menucb" id="clientbill" style="background: #F5F5F5;margin: inherit;"><span class="icon-document-text"></span>Client Bill</a></li>
                
                </ul>
                </div>



                <div class="tab-content">
                   <!-- progress report start -->
                    <div id="homepr" class="tab-pane fade in active">

					<div class="search-and-content-wrpr" id="pgrsrpt">
						<div class="col-md-12 text-center" style="padding-top: 35px;">
							<div class="row prgheads">
                            
                                <label style="font-size: 15px;">Progress Report</label>
                            </div>
                           
                        </div>
                        <form id="schedule-task">
							<div class="search-and-actions-wrpr row">
								<div class="content-search-wrpr col-md-6 col-sm-6 text-left ">
									<!-- <span style="font-weight: bold;padding-right: 10px;">Report Date</span> -->
									<!-- <input type="hidden" class="form-control datepicker select_report_date" id="select_report_date" name="select_report_date" value="<?php //echo date("d-m-Y");?>" data-id="1" /> -->
									<input type="hidden" name="select_report_date" id="select_report_date" value="<?php echo date("d-m-Y");?>" />
								</div>
								<div class="col-md-4 col-sm-4"></div>
								<div class="content-action-wrpr col-md-2 col-sm-2">
									<!--<button type="button" class="btn btn-danger taskreport" id="taskreport">Tasks</button>-->
									<button type="button" class="btn btn-primary activity_history_btn" id="activity_history_btn">History</button>
									<button type="button" class="btn btn-primary activity_back_btn" id="activity_back_btn">Back</button>

									<input type="hidden" id="activity_pr_main"/>
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
	                                <div id="schedule_report_activityitems"></div>
	                                <div id="activitycompletehist"></div>
	                                <div id="success-messages"></div>	
	                            </div>
	                            <!-- list end here -->
							</div>
						</form> 

						<!--<form id="schedule-task-reporting" style="display:none">
							<div class="search-and-actions-wrpr row">
								<div class="content-search-wrpr col-md-6 col-sm-6 text-left ">
									<input type="text" class="form-control datepicker select_report_date_task" id="select_report_date_task" name="select_report_date_task" value="<php echo date("d-m-Y");?>" data-id="1" />
								</div>
								<div class="col-md-4 col-sm-4"></div>
								<div class="content-action-wrpr col-md-2 col-sm-2">
									<button type="button" class="btn btn-danger backprgrsreprt" id="backprgrsreprt">Back</button>
								</div>
							</div>
							<div class="content-wrpr">
																
								<div class="asset-register-list-wrpr data-content-list">
	                                <div id="scheduleactivityitemstask"></div>
	                                <div id="success-messages-task"></div>	
	                            </div>
							</div>
						</form>-->

					</div>
				</div>

                <!-- progress report end -->


                  <!-- client bill start -->


                 <div id="menucb" class="tab-pane fade">
                  <div class="search-and-content-wrpr" id="cntbill">
						<div class="search-and-actions-wrpr row">
							<div class="content-search-wrpr col-md-4 col-sm-4 text-left " >
                                	
							</div>
							<div class="col-md-3 col-sm-3"></div>
							<div class="content-action-wrpr billhead col-md-5 col-sm-5">
                                <a href="#" type="hidden" id="boq_list"></a>
								<a class="btn btn-primary btn-bill-history"  id="bill_list" title="Bill History"><span class="icon-history"></span> Bill History</a> 
								<a class="btn btn-primary btn-bill-history-close"><span class="icon-history"></span> Close Bill History</a> 
								<a class="btn btn-primary btn-raise-bill addForm" id="raise_bill" title="Raise Bill"><span class="icon-bill"></span> Raise Bill</a>								
							</div>
						</div>
						<div class="col-md-12 text-center" style="padding-bottom: 35px;">
							<div class="row clntheads">
                            
                                <label style="font-size: 15px;">Client Bill</label>
                            </div>
                           
                        </div>
						<div class="content-wrpr">
							<!-- form starts here -->
								<div class="add-form raise-bill-form  row" id="rbll">
                                    <div class="preloader" style="display: none;" align="center">
                                        <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                                    </div>
                                    <form id="clientbillform">
                                        <div id="raisebillitems123"></div>
                                    </form>
								</div>
							<!-- form ends here -->

							<!-- edit form starts here -->
							
							
							<!-- edit form ends here -->

							
							<!-- list start here -->
							
							
							<!-- client-bill-BOQ-list -->

							<div class="client-bill-BOQ-list-wrpr data-content-list" id="cllist">
								<div class="row">
									<div id="projectnameBoq" style="display: none;">
                                            <div class="col-md-12 type project-boq">
                                                <label>Project <em id="projectname-CB"></em></label>
                                                <input type="hidden" id="boqprojectid" value="">
                                            </div>
                                        </div>
                                        	<div class="col-md-12">
											<div id="boqlistsection" style="display: none;">
                                				<div class="row show-grid">
													<table class="table table-bordered" id="boqtable" style="display: table; overflow: hidden;">
														<thead class="clientcls">
														<tr>
															<th width="10%">WBS</th>
															<!--<th>Activity</th>-->
															<th>Item</th>
															<th>Unit</th>
															<th>Quantity</th>
															<th>Rate</th>
															<th width="8%">Amount</th>
															
														</tr>
														<tr>
														<div class="preloader" id="Promain-preloader-Listboq" style="display: none;" align="center">
															<img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
														</div>
														</tr>
														</thead>
														<tbody id="boq-list-body">
														</tbody>
													</table>
												</div>
											</div>
										</div>
									</div>
							</div>
							<!-- client-bill-BOQ-list end -->
							
							<!-- client-bill-history-list start -->
							<div class="client-bill-history-list-wrpr data-content-list" id="billhis">
                                <div class="preloader" style="display: none;" align="center">
                                    <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                                </div>
                                <div id="billitems"></div>					
							</div>
							<!-- client-bill-history-list end -->
							
							<!-- view client-list starts here -->
							<div class="view-client-bill-list-wrpr data-content-list" id="vcbill">
                                <div class="preloader" style="display: none;" align="center">
                                    <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                                </div>
                                <form id="client_billformedit">
                                    <div id="View-Client-Bill"></div>
                                </form>
							</div>
							
							<!-- view client-list ends here -->
							<!-- list end here -->
						</div>
					</div>

				</div>






                  <!-- client bill end -->

                   </div>




				  </div>
				</div>
			  </div>