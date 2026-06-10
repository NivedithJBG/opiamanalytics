
			  <div class="panel panel-default client-bill-tab tab tab-wrapper acco-thirteen">
              <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/operations/clientbill.js" type="text/javascript"></script>
			  <!-- <input type="radio" class="clientbill" id="rd5" name="rd"> -->
				<div class="panel-heading" >
				  <h4 class="panel-title" id="clientbill">
					<a data-toggle="collapse" data-parent="#accordionoper" href="#collapsesbill">
					<span class="icon-files1"></span>Client Bill</a>
				  </h4>
				</div>
				<div id="collapsesbill" class="tab-content cOrder-body panel-collapse collapse">
				  <div class="panel-body ">
					<div class="search-and-content-wrpr">
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
								<div class="add-form raise-bill-form  row">
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
							<div class="client-bill-BOQ-list-wrpr data-content-list">
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
							<div class="client-bill-history-list-wrpr data-content-list">
                                <div class="preloader" style="display: none;" align="center">
                                    <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                                </div>
                                <div id="billitems"></div>					
							</div>
							<!-- client-bill-history-list end -->
							
							<!-- view client-list starts here -->
							<div class="view-client-bill-list-wrpr data-content-list">
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
				</div>
			  </div>