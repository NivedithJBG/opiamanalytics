<div class="panel panel-default  allocate-resource-tab tab tab-wrapper acco-eleven">
			  <input type="radio" class="assignresourcetab" id="rd5" name="rd">
				<div class="panel-heading" >
				  <h4 class="panel-title">
					<a  href="#">
					<span class="icon-directions_run"></span>Schedule Allocate Resources</a>
				  </h4>
				</div>
				<div  class="tab-content cOrder-body panel-collapse ">
				  <div class="panel-body ">
					<div class="search-and-content-wrpr">
						<div class="content-wrpr">
                            <!-- list start here -->
							<div class="allocated-list-cntnt-wrpr added-alloc-items-wrpr data-content-list">
								<div class="row added-alloc-items-heading">
								<div class="col-md-12 ">
									<div class="row  alloc-activity-title">
										<div class="col-md-8 type" id="headone">
										</div>
										<div class="col-md-4 text-right">
											<a href="#" class="btn btn-primary resource-list-btn">Add Allocation</a>
											<input type="hidden" id="assignresourceload">
											<a href="#" class="btn btn-primary close-resource-list-btn">Close Allocation</a>
										</div>
									</div>
								</div>
								</div>
								<div class="allocation-list-items-cntnr">
									<div id="scheduleassignlist"></div>
								</div>
							</div>
							<div class="allocation-cntnt-wrpr resource-not-allocated">
					
								<div class="allocation-left-bar items-select-bar" id="SAbuttonlist">
									<a class="btn btn-primary rounded-coner-btn" href="#">Investments</a>
									<a class="btn btn-primary rounded-coner-btn" href="#">Project Setup</a>
									<a class="btn btn-primary rounded-coner-btn active" href="#">Materials</a>
									<a class="btn btn-primary rounded-coner-btn" href="#">Sub. Contractors</a>
								</div>
								<div class="resource-not-allocated-info">
									<div class="icon-info3"></div>
									<div class="info-text">
										<p>Resources not allocated to the activity - Investments in Floating Crafts</p>
										<span>To allocate resources, click on the left tab</span>
									</div>
								</div>
								<div class="resources-from-selected-item-wrpr ">
									<div class="row">
										<div class="col-md-12 ">
											<div class="search-and-allocate-content-wrpr">
											<div class="search-and-actions-wrpr row">
												<div class="content-search-wrpr col-md-12 col-sm-12">
													<div class="col-md-3">
														<select id="resource_groupselection" class="form-control">

														</select>
														<input type="hidden" id="selectresource_id">
													</div>
													<div class="col-md-6">
														<input class="form-control" id="resource_name" type="text" placeholder="Search">
													</div>
													<div class="col-md-3">
														<button type="button" class="btn btn-primary resourcesearch123" id="resource_search123">
															<span class="icon-search5"></span>
														</button>
													</div>
												</div>
											</div>
											
											<div class="panel-group allocation-items" id="accordion">
												<div class="panel panel-default active">
												<div class="preloader" style="display: none;" align="center">
													<img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
												</div>
												<div id="projects-Activity-Search-Lists2">
												
												</div>
												</div>
											</div>
											
										</div>	
										
									</div>
									
								</div>
					
							</div>
							
					
							
							<!-- list end here -->
						
							
						</div>
					</div>
				  
				  </div>
				</div>
			  </div>