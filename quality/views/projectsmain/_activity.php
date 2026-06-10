
<?php 
use amnah\yii2\user\models\User;
use app\models\EstimateWorkType;
use app\models\EstimateActivityType;


?>

				<!--<div class="panel panel-default project-activities-tab  acco-four tab tab-wrapper">-->
				<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projectsmain/_activity.js" type="text/javascript"></script>
				  <!--<input type="radio" id="rd5" class="prjctactivities" name="rd">
					<div class="panel-heading" >
					  <h4 class="panel-title">
						<a  href="#">
						<span class="icon-directions_run"></span>Project Activities</a>
					  </h4>
					</div>
					<div  class="tab-content cOrder-body panel-collapse ">
					  <div class="panel-body">
						<div class="search-and-content-wrpr" >
							<div class="content-wrpr">-->
								<div id="project-activities-Body" style="display: none;">
									<!-- project-activities list -->
									<div class="project-activities-list-wrpr data-content-list">
										<div class="row">
											<div class="col-md-12 type project-boq proj" >
												<label><span>Project Activity - <b id="workgroupnamedisplay" style="color: black;"></b></span></label>
												<a href="#" class="btn btn-primary text-button back-butn-new close-prjctactvty" title="back">Back</a>
												<!--<a href="javascript:void(0);" style="display: none;"  class="btn btn-primary add-project-activities-btn showsearch"><span class="icon-add"></span>Add Activity</a>-->
												<a href="javascript:void(0);"   class="expand-collapse-palist icon-arrow-up1" data-toggle="collapse" data-target=".added-activity-list-wrpr" style="display: none;"></a>
												<a href="javascript:void(0);" class="btn btn-primary" id="listiow" style="display: none;"></a>
												<a href="javascript:void(0);" class="btn btn-primary addForm" id="addBOQMap" style="display: none;"></a>
												<input type="hidden" id="IOWWorkgroupId2">
												<!-- <input type="hidden" id="IOWorkgroupId" name="IOWorkgroupId"> -->
												<input type="hidden" id="ProId" name="ProId">
												<input type="hidden" id="IOWorkgroupsId" name="IOWorkgroupId">
											</div>
											<input type="hidden" id="selectedWorktypeId">
											<input type="hidden" id="selectedWorkgrouId">	
											<div class="col-md-12 added-activity-list-wrpr collapse in" >
												<div class="preloader" id="Promain-preloader-projectactivity" style="display: none;" align="center">
													<img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
												</div>
												<div class="activityitem text-center">
													<label>Activities</label>
												</div>
												<div id="projects-Activity-Lists"></div>
		
											</div><hr>
											<div class="col-md-12 type project-boq" >
													<div class="col-md-2">
														<a href="javascript:void(0);"  class="btn btn-primary add-project-activities-btn showsearch"><span class="icon-add"></span>Add Activity</a>
														<a href="#"  class="btn btn-primary close-activity-list-btn" style="display:none;">Close Activity</a>
													</div>
													<a href="javascript:void(0);" style="display: none;" class="expand-collapse-palist icon-arrow-up1" data-toggle="collapse" data-target=".added-activity-list-wrpr"></a>
													
													<div class="col-md-10" id="search-show" style="display:none;margin-left: 104px;">
														<input type="text" class="wbs-act-search" id="searchenggactname" placeholder="Search Activities">
														<button class="btn btn-primary wbs-act-search-btn search-button" id="enggactsearchNow" 0 type="button"><span class="icon-search5"></span></button>
														<input type="hidden" id="enggactsearch">
													</div>
											</div>
											<!--add activities search and list -->
											<!--<div class="col-md-12 add-activities-section-title">
												<label>Add Activities</label>
												<a href="#"  class="btn btn-primary close-activity-list-btn"><span class="icon-close"></span> Close</a>
											</div>-->
											
											<div class="col-md-3 add-activity-filter">
												<div class="row">
													<!--<div class="col-md-12">
														<div class="form-group">
															<label>Search</label>
															<input type="text" id="searchenggactname" placeholder="Search Activities" style="min-width:215px;">
															<button class="btn btn-primary search-button" id="enggactsearchNow" 0 type="button"><span class="icon-search5"></span></button>
															<input type="hidden" id="enggactsearch">
														</div>
													</div>-->
													<div class="col-md-12">
														<div class="form-group">
															<label>Project Type</label>
														<?php
														$typelist=EstimateWorkType::find()->where(['estworktype_status' => 0])->orderBy(['sortorder' => SORT_ASC])->all();
														foreach($typelist AS $key=>$list):
															if($key == 0){
																$activeNow = '';
																//$activeNow = 'active';
																//echo '<input type="hidden" id="wbsworktypelist" value="'.$list->estworktype_id.'">';
																echo '<input type="hidden" id="wbsworktypelist" value="">';
															}else{
																$activeNow = '';
															}
															echo "<a href='javascript:void(0);' class='btn btn-primary rounded-coner-btn wbsworktypelist ".$activeNow."' data-v='".$list->estworktype_id."'>".$list->estworktype_name."</a>";
															endforeach;
														?>	
														</div>
													</div>
													<div class="col-md-12">
														<div class="form-group">
															<label>Activity Type</label>
														<?php $typelist=EstimateActivityType::find()->where(['activitytype_status' => 0])->orderBy(['sortorder' => SORT_ASC])->all(); 
															foreach($typelist AS $key=>$list):
															if($key == 0){
																$activeNow = 'active';
																//echo '<input type="hidden" id="wbsactivitytypelist" value="'.$list->activitytype_id.'">';
																echo '<input type="hidden" id="wbsactivitytypelist" value="">';
															}else{
																$activeNow = '';
															}
															echo "<a href='javascript:void(0);' class='btn btn-primary rounded-coner-btn wbsactivitytypelist ".$activeNow."' data-v='".$list->activitytype_id."'>".$list->activitytype_name."</a>";
															endforeach;
														?>
														</div>
													</div>
												</div>
											</div>
											<div class="col-md-12 activity-results-wrpr custom-padding-activity ">
												<div class="preloader" id="Promain-preloader-projectactivity-Search" style="display: none;" align="center">
													<img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
												</div>
												<div id="projects-Activity-Search-Lists">
													<div class="row"><div class="col-md-12"><div class="text-center" style="padding-top: 100px;">Select Project type or Activity type filters</div></div></div>
												</div>
											</div>
											<!--add activities search and list end-->
										</div>
									</div>
									<!-- project-activities list end -->
									<!-- add Map BOQ -->
									<div class="add-form add-wbs-form" id="boqactsearch-area">	
										<div class="row">
											<div class="col-md-12 text-center">
												<h3><span>Search BOQ Items</span></h3>
											</div>
											<div class="content-search-wrpr-new col-md-12 col-sm-12">
												<input class="form-control" id="searchboqitem" placeholder="Search BOQ slno or item"> 
												
												<button id="boqactsearch" class="btn btn-primary" type="button" value=""><span class="icon-search5"></span>Search</button>
												<span>&nbsp;</span><span>&nbsp;</span>
												<button id="boqactsearchCancel" class="btn btn-danger cancel" type="button"><span class="icon-check"></span>Cancel</button>
											</div>
											<div class="col-md-12">
												<span>&nbsp;</span>
											</div>
											<div class="col-md-12 custom-padding-activity-new">
												<div id="boqsearchlisting"></div>
											</div>
										</div>	
									</div>
									<!-- add Map BOQ end here-->
								</div>
								<!--<div id="project-activities-Body-NOData">
								<div class="row"><div class="col-md-12"><div class="text-center">Please Select IOW Activities</div></div></div>
								</div>
							</div>
						</div> 
					</div>
					</div>
			  </div>-->