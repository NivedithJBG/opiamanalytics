
<?php
use app\models\Resources;
?>
 <div class="panel panel-default  allocate-resource-tab tab tab-wrapper acco-ten">
 <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projects/allocation.js" type="text/javascript"></script>
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
					
					<div class="estimation-left-bar allocation-left-bar items-select-bar">
					
						
						
						
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
										<a href="#" id="deleterefresh"></a>
									<input type="hidden" id="resourcegroupval" name="" value="">
									<input type="hidden" id="EstActivity_Id" name="" value="">
									<input type="hidden" id="project_id" name="" value="">
									<input type="hidden" id="proces_id" name="" value="">
									<input type="hidden" id="activity_id" name="">
									<input type="hidden" id="activityunit" value="" name="">
									<input type="hidden" id="activity_name" value="" name="">


									 <input type="search" onsearch="OnSearch()" class="form-control searchdefault resources" list="resourcelist" id="resourcegroup" name="accounthead-choice" placeholder="Search"/>

						                 <button id="" class="btn btn-primary searchresoureskey" type="button"><span class="icon-search5"></span></button>

												        <datalist id="resourcelist">
												            <?php
												           
									$resources=Resources::find()->where(['status'=>0])->andwhere(['pricing_status'=>0])->groupBy(['Name'])->all();

												            foreach($resources AS $resource):
												                echo "<option value='".$resource->Name."'></option>";
												            endforeach;
												            ?>
												        </datalist>
									</div>
								</div>
								
								<div class="panel-group allocation-items" id="accordion">
			

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