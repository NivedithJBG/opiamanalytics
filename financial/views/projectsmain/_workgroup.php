<?php 
use amnah\yii2\user\models\User;


?>
                <div class="panel panel-default WBS-tab acco-three tab tab-wrapper">
                	<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projectsmain/_workgroup.js" type="text/javascript"></script>
                	<!-- <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  					<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> 
  					<script src="http://code.jquery.com/jquery-1.8.2.js"></script>
                    <script src="htdata-toggle="collapse" data-parent="#accordionprojindex" href="#collapseboq"tp://code.jquery.com/ui/1.9.1/jquery-ui.js"></script>
				  <input type="radio" id="rd5" class="view_workgroups" name="rd">-->
					
					<div class="panel-heading">
					  <h4 class="panel-title" id="view_workgroups">
						<a data-toggle="collapse" data-parent="#accordionprojindex" href="#collapsewbs" id="workgroup_name">
						<span class="icon-chart6"></span>Work Breakdown Structure</a>
					  </h4>
					</div>
					<!-- WBS BLOCK -->
					<div id="collapsewbs" class="tab-content cOrder-body panel-collapse collapse">
						<div class="panel-body">
							<div class="search-and-content-wrpr" >
								<div id="wbs_estimate_block" style="display:none;">
									<div id="wbs-header">
										<div class="search-and-actions-wrpr row heads3">
											<div class="content-search-wrpr col-md-3 col-sm-3">
											</div>
											<div class="content-action-wrpr col-md-9 col-sm-9">
												<a href="javascript:void(0);" class="btn btn-primary addForm" id="addworkgroup" title="Add IOW"><span class="icon-add"></span> Add</a>
												<a href="javascript:void(0);" class="btn btn-primary list-fundreceipt" id="listworkgroup"><span class="icon-th-list"></span> List</a>
											</div>
										</div>
									</div>
									
									<div class="content-wrpr">
									
										<!-- add wbs form -->
										<div class="sort" id="sortable">
										<div class="add-form add-wbs-form" id="add-wbs-form">
											<form id="addworkgroupform">
												<div class="row">
													<div class="col-md-5">
														<div class="form-group">
															<label>Item of Work Name</label>
															<input type="text" class="form-control" id="workgroupname" name="projectname" placeholder="IOW Name">
															<span class="error" style="display: none;"></span>
														</div>
													</div>
													<div class="col-md-2">
														<div class="form-group">
															<label>IOW Unit</label>
															<input type="text" class="form-control" id="workgroupunit" name="workgroupunit" placeholder="IOW Unit">
															<span class="error" style="display: none;"></span>
															
														</div>
													</div>
													<div class="col-md-2">
														<div class="form-group">
															<label>IOW Quantity</label>
															<input type="text" class="form-control" id="workgroupquantity" name="workgroupquantity" placeholder="IOW Quantity">
															<span class="error" style="display: none;"></span>
															
														</div>
													</div>
													<div class="col-md-3 text-right">
															<label>&nbsp;</label>
															<button type="button" class="btn btn-danger cancel" id="cancelworkgroup"><span class="icon-close"></span> Cancel</button>
															<button type="button" class="btn btn-primary" id="saveworkgroup"><span class="icon-check"></span> Add WBS</button>
													</div>
												</div>
											</form>
										</div>
									</div>
										<!-- add wbs form end-->
										
										
										
										<!-- edit wbs form -->
										<div class="edit-form edit-wbs-form">
											
											<div class="row">
												<div class="col-md-3">
													<div class="form-group">
														<label>IOW Name</label>
														<input type="text" class="form-control" />
														
													</div>
												</div>
												<div class="col-md-3">
													<div class="form-group">
														<label>IOW Unit</label>
														<input type="text" class="form-control" />
														
													</div>
												</div>
												<div class="col-md-3">
													<div class="form-group">
														<label>IOW Quantity</label>
														<input type="text" class="form-control" />
														
													</div>
												</div>
												<div class="col-md-3 text-right">
														<label>&nbsp;</label>
														<button type="button" class="btn btn-danger cancel" id=""><span class="icon-close"></span> Cancel</button>
														<button type="button" class="btn btn-primary" id=""><span class="icon-check"></span> Edit WBS</button>
												</div>
											</div>
											
											
										</div>
										<!-- edit wbs form end-->
										
										
										
										<!-- wbs list -->
										<div class="wbs-list-wrpr data-content-list">
											<div class="row">
												<div class="workgroupitem wbhds text-center">
													<label>Items Of Work</label>
												</div>
												<div id="projectnameWBS" style="display: none;">
													<div class="col-md-12 type project-boq">
														<label>Project <em id="dispprojectname"></em></label>
														<input type="hidden" id="selectedProjectId" value="">
													</div>
												</div>
												
												
												<div class="col-md-12">
													<div class="preloader" id="Promain-preloader-Listwbs" style="display: none;" align="center">
														<img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
													</div>
													<div id="listworkgroup-data" class="selectedsortable row"></div>
												</div>
												
											</div>
										</div>
										
										<!-- wbs list end -->
		
										
									</div>
								</div>
								<!-- Project Activities List Start -->

									<?php echo \Yii::$app->view->renderFile(Yii::getAlias('@app') . '/views/projectsmain/_activity.php'); ?>

								<!-- Project Activities List End -->
							</div>
						</div>
					</div>
			  </div>
			

			