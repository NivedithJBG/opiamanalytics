<?php 
use amnah\yii2\user\models\User;


?>
            <!--<div class="panel panel-default schedule-activity-relations-tab tab tab-wrapper acco-eight">-->
            	<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projectsmain/_schedulerelation.js" type="text/javascript"></script>
			  <!--<input type="radio" id="rd5" class="act-relatn-tab" name="rd">
				
				<div class="panel-heading" >
				  <h4 class="panel-title">
					<a  href="#">
					<span class="icon-time1"></span>Schedule Activity - Relations</a>
				  </h4>
				</div>
				
				
				<div  class="tab-content cOrder-body panel-collapse ">
				  <div class="panel-body ">
				  
					<div class="search-and-content-wrpr">-->

						<div id="relation-tabscreen" style="display:none;">
										
							<div class="search-and-actions-wrpr row" id="search-show-relation">
								<div class="content-search-wrpr col-md-4 col-sm-4 text-left">
									<input type="text" placeholder="Search" id="Relactionsval" class="form-control">
	                                <button id="Relactionssearch" class="btn btn-primary"  type="button"><span class="icon-search5"></span></button>
								</div>
								<div class="col-md-4 col-sm-4 text-center">
									
	                                <label>Schedule Activity - Relations</label>
								</div>
								<!--<div class="col-md-2 col-sm-2"></div>-->
								
								<div class="content-action-wrpr col-md-4 col-sm-2">
									<a href="javascript:void(0);" class="btn btn-primary addForm showrelatntab"><span class="icon-add"></span> Add Relation</a>
									<a href="javascript:void(0);" class="btn btn-primary list-accountType" id="wbs_schedule_relation_new" ><span class="icon-th-list"></span> List</a>
									<a href="#" class="btn btn-primary text-button back-butn-new close-schedulerelatn" data-id="1">Back</a>
	                            </div>
	                           
							</div>
							
							
							<div class="content-wrpr">
							
								<!-- form starts here -->
								<div class="add-form add-relations-form" id="ScheduleActivity-Relation-add-form">
								</div>
								<!-- form ends here -->

								<!-- list start here -->
								<!--<div class="schedule-activity-relations-cntnt-wrpr data-content-list">-->
								<div class="schedule-activity-relations-cntnt-wrpr data-content-list-custom">
	                                <div class="preloader" id="Promain-preloader-ScheduleActivity" style="display: none;" align="center">
	                                    <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
	                                </div>
	                                
	                                <div id="scheduleactivityitems-Relation-search-form" style="padding:15px 0;" align="left">
	                                	
	                                	<div  class="row">
		                                	<div  class="col-md-4">
		                                		<select id="filter_schedule_item" class="form-control">
		                                			<option value="">Select Schedule Item</option>
		                                		</select>
		                                	</div>
		                                	<div  class="col-md-4">
		                                		<select id="filter_schedule_activity" class="form-control">
		                                			<option value="">Select Activity</option>
		                                		</select>
	                                		</div>
	                                		<div class="col-md-4"></div>
	                                	</div>
	                                </div>

	                                <div id="scheduleactivityitems-Relation" class="relationss"></div>
								</div>
								<!-- list end here -->
							
								
							</div>

						</div>
					<!--</div>
				  
				  </div>
				</div>
			</div>-->