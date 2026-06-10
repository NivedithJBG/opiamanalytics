<?php 
use amnah\yii2\user\models\User;

$user=User::find()->where(['id'=>Yii::$app->user->id])->one();

?>

<div class="panel panel-default Schedule-tab tab-wrapper acco-five tab">
	<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projectsmain/_schedulelist.js" type="text/javascript"></script>
  	<!--<input type="radio" id="rd5" class="view_wbsSchedule" name="rd">-->
	
	<div class="panel-heading" >
	  <h4 class="panel-title view_wbsSchedule">
		<a data-toggle="collapse" data-parent="#accordionprojindex" href="#collapseschedule" id="workgroup_name">
		<span class="icon-chart6"></span>Project Schedule</a>
	  </h4>
	</div>
	<!-- WBS SCHEDULE BLOCK -->
	<div id="collapseschedule" class="tab-content cOrder-body panel-collapse collapse">
		<div class="panel-body">
			<div class="search-and-content-wrpr" >
				<!-- First listing Start -->
				<div id="wbs_schedule_block" style="display:none;">
					<div id="wbs-schedule-header" style="display: none;">
						<div class="search-and-actions-wrpr row head1">
							<div class="content-search-wrpr col-md-2 col-sm-2"> </div>
							<div class="content-action-wrpr col-md-10 col-sm-10" id="listscreenshow">
								<!-- <a href="#" class="btn btn-primary addForm" id="addschedule_item" title="Add Schedule Item"><span class="icon-add"></span> Add Schedule Item</a> -->
								<input type="hidden" id="gttid">
								<a href="javascript:;" style="display:none;" class="btn btn-primary list-fundreceipt" id="listwbs_schedule"><span class="icon-th-list"></span> List</a>
								<?php if($user->account_type != 'perfm_pad_reporting_only'){ ?>
									<a  href="javascript:;" class="btn btn-primary list-fundreceipt schedule_relation-tab reltnmain"><span class="icon-dns"></span> Create Relationships</a>
									<span id="ganttchartshows"></span>
								<?php } ?>
								<input type="hidden" id="mode-edit" name="mode" value="" />
							</div>
							<!--  <div class="content-action-wrpr col-md-3 col-sm-3" id="ganttchartshow2"> + </div> -->
						</div>
					</div>
					<div class="content-wrpr">
					
						<!-- add wbs schedule form -->
						<div class="add-form add-wbs-form" id="add-wbs-Schedule-form">
							<form id="addscheduleitemform">
								<div class="row">
									<div class="col-md-9">
										<div class="form-group">
											<label>Schedule Item Name</label>
											<input type="text" class="form-control" id="scheduleitem_name" name="projectname">
											<span class="error" style="display: none;">
		
										</div>
									</div>
									<div class="col-md-3 text-right">
											<label>&nbsp;</label>
											<button type="button" class="btn btn-danger cancel" id="cancelschedulegroup"><span class="icon-close"></span> Cancel</button>
											<button type="button" class="btn btn-primary" id="savescheduleitem"><span class="icon-check"></span> Save</button>
									</div>
								</div>
							</form>
						</div>
						<!-- add wbs schedule form end-->

						<!-- wbs schedule list -->
						<div class="wbs-list-wrpr data-content-list">
							<div class="row">
								
								
								
								<div class="col-md-12">
									<div class="preloader" id="Promain-preloaderwbsestimateactivities-Schedulewbs" style="display: none;" align="center">
										<img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
									</div>
									<div class="scheduitem schdheads text-center">
										<label>Schedule Items</label>
									</div>

									<div id="projectnameScheduleWBS" style="display: none; padding-top: 25px;">
										<div class="col-md-12 type project-boq">
											<label>Project <em id="dispprojectname_schedule"></em></label>
										</div>
									</div>


									<div id="listworkgroup-Schedul-data" class="row sortinglist">
										
										</div>
									</div>
								</div>
								
							</div>
						</div>
						
						<!-- wbs schedule list end -->
	
					</div>
				</div>
				<!-- First listing End -->

				<!-- Second listing Start -->

					<?php echo \Yii::$app->view->renderFile(Yii::getAlias('@app') . '/views/projectsmain/_scheduleactivity.php'); ?>

				<!-- Second listing End -->

				<!-- Third listing Start -->

					<?php echo \Yii::$app->view->renderFile(Yii::getAlias('@app') . '/views/projectsmain/_schedulerelations.php'); ?>

				<!-- Third listing End -->

                  <?php echo \Yii::$app->view->renderFile(Yii::getAlias('@app') . '/views/projectsmain/_tasklistingg.php'); ?>



			</div>
		</div>
	</div>
