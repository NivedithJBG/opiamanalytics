
<?php
use app\models\Resources;
?>
 <div class="panel panel-default  allocate-resource-tab tab tab-wrapper acco-ten">
 <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projects/allocation.js?v=<?php echo filemtime(__DIR__ . '/../../web/jsnew/projects/allocation.js'); ?>" type="text/javascript"></script>
			  <input type="radio" class="estimateallocationaccordion" id="rd5" name="rd">

				<div class="panel-heading" >
				  <h4 class="panel-title">
					<a  href="#">
					<span class="icon-directions_run"></span>Allocate Resources</a>
				  </h4>
				</div>


				<div  class="tab-content cOrder-body panel-collapse ">
				  <div class="panel-body ">

					<div class="search-and-content-wrpr">


						<div class="content-wrpr">

							<a href="#" id="estimateallocationlist"></a>
							<!-- list start here -->
							<div class="allocated-list-cntnt-wrpr added-alloc-items-wrpr data-content-list">
								<div class="row added-alloc-items-heading">
								<div class="col-md-12 ">
									<div class="row  alloc-activity-title">
										<div id="activitycosthead" class="col-md-8 type">
											<span></span>
										</div>
										<div class="col-md-4 text-right">
											<a href="#" class="btn btn-primary resource-list-btn">Add Allocation</a>
											<a href="#" class="btn btn-primary close-resource-list-btn">Close Allocation</a>
										</div>
									</div>
								</div>
								</div>
								<div class="estimationaddedallocation allocation-list-items-cntnr">

								</div>


							</div>


							<div class="allocation-cntnt-wrpr resource-not-allocated">

								<!-- Hidden inputs used by JS handlers -->
								<a href="#" id="deleterefresh"></a>
								<input type="hidden" id="resourcegroupval" value="">
								<input type="hidden" id="EstActivity_Id" value="">
								<input type="hidden" id="project_id" value="">
								<input type="hidden" id="proces_id" value="">
								<input type="hidden" id="activity_id" value="">
								<input type="hidden" id="activityunit" value="">
								<input type="hidden" id="activity_name" value="">

								<div class="row" style="margin-top:16px;">

									<!-- Left panel: Resource Types -->
									<div class="col-md-3" style="border-right:1px solid #e0e0e0; padding-right:16px; min-height:300px;">
										<div class="estimation-left-bar allocation-left-bar items-select-bar">
											<!-- Resource type buttons injected here by actionActivityresources -->
										</div>
									</div>

									<!-- Right panel: Resource Groups accordion -->
									<div class="col-md-9" style="padding-left:20px;">
										<div id="alloc-groups-panel">
											<p style="color:#aaa; padding-top:20px; font-size:13px;">Select a Resource Type on the left to view groups</p>
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
