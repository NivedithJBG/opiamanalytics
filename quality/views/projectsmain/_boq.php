<?php 
use amnah\yii2\user\models\User;


?>
                <div class="panel panel-default BOQ-tab acco-two tab tab-wrapper">
                	<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projectsmain/_boq.js" type="text/javascript"></script>
				  <!--<input type="radio" id="rd5" class="boq-all-list" name="rd">-->
					
					<div class="panel-heading" >
					  <h4 class="panel-title" id="boq-all-list">
						<a data-toggle="collapse" data-parent="#accordionprojindex" href="#collapseboq">
						<span class="icon-assignment"></span>Bill of Quantities</a>
					  </h4>
					</div>
					
					
					<div id="collapseboq" class="tab-content cOrder-body panel-collapse collapse">
					  <div class="panel-body">
						<div class="search-and-content-wrpr" >
							
							<div class="search-and-actions-wrpr row">
								<div class="content-search-wrpr col-md-3 col-sm-3">
									
								</div>
								
								<div class="content-action-wrpr col-md-9 col-sm-9">
                                    <span id="clearboqbtn"></span>
                                    <span id="importbtn"></span>
									<!--<a href="#" class="btn btn-primary clearboq-btn"><span class="icon-trashcan"></span> Clear BOQ</a>
									<a href="#" class="btn btn-primary addForm"><span class="icon-download7"></span> Import</a>
                                    <a href="#" class="btn btn-primary list-fundreceipt" id="listboq"><span class="icon-th-list"></span> List</a> -->
                                    <input type="hidden" id="listboq">
								</div>
							</div>
							
							<div class="content-wrpr">
							
                                <!-- boq import form -->
								<div class="row"><div class="col-md-12"><div class="text-center" id="message"></div></div></div>
								<div class="add-form boq-import-form">
                                <form method="POST" id="import_boq_form" enctype="multipart/form-data">
									<div class="row">
										<div class="col-md-3">
											
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label>Select CSV file*</label>
												<div class="custom-file mb-3">
                                                  <input type="file" class="custom-file-input" name="import_boq_file" id="import_boq_file"  required>
                                                  <input type="hidden" name="projectid" id="boq_projid">
												  <label class="custom-file-label" for="customFile">Choose file</label>
												
												</div>
												
											</div>
										</div>
										<div class="col-md-3 icon-groups text-right">
											<a href="<?= Yii::$app->urlManager->createUrl(['pdf/sampleboq.csv']) ?>" class="btn btn-primary text-button" download="sampleboq.csv"> Sample BOQ File</a>
										</div>
										<div class="col-md-12 text-center">
												<button type="button" class="btn btn-danger cancel" id="cancel-import_boq"><span class="icon-close"></span> Cancel</button>
												<button type="submit" class="btn btn-primary import_boq" name="import_boq" id="import_boq" value="Import"><span class="icon-download7"></span> Import BOQ</button>
										</div>
									</div>
									
									
                                </form>
                                </div>
                                <!-- boq import form end-->
								
								
								<!-- boq list -->
								<div class="boq-list-wrpr data-content-list">
									<div class="row">
                                        <div id="projectnameBoq" style="display: none;">
                                            <div class="col-md-12 type project-boq">
                                                <label>Project <em id="boqprojectname"></em></label>
                                                <input type="hidden" id="boqprojectid" value="">
                                            </div>
                                        </div>
										<div class="col-md-12">
											<div id="boqlistsection" style="display: none;">
											<div class="row show-grid">
													<table class="table table-bordered" id="boqtable" style="display: table; overflow: hidden;">
														<thead class="boqhds">
														<tr>
															<th width="10%">WBS</th>
															<!--<th>Activity</th>-->
															<th>Item</th>
															<th>Unit</th>
															<th>Quantity</th>
															<th>Rate</th>
															<th width="8%">Amount</th>
															<th></th>
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
								
								<!-- boq list end -->
								
							</div>
								</div>
					  
					  
					  </div>
					</div>
			  </div>