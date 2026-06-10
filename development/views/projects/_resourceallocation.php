<?php
use app\models\Resources;
?>


<div class="panel panel-default  allocate-resource-tab tab tab-wrapper acco-five">
			  <input type="radio" class="resourceallocationaccordian" id="rd5" name="rd">
				
				<div class="panel-heading" >
				  <h4 class="panel-title">
					<a  href="#">
					<span class="icon-directions_run"></span>Allocate Resources</a>
				  </h4>
				</div>
				
				
				<div  class="tab-content cOrder-body panel-collapse ">
				  <div class="panel-body ">
				  
					<div class="search-and-content-wrpr">
		
						<div class="content-wrpr"><br>
							<div class="col-md-12 type project-boq" >
								<label><span><b id="activityrate" style="color: black;"></b></span></label>
							</div></div>
							<!-- list start here -->
							<div class="allocated-list-cntnt-wrpr added-alloc-items-wrpr data-content-list">
								<div class="infodisplay"></div>
								<input type="hidden" id="EstActivity_Id" name="">
								<a id="addedlist" href="#"></a>
								<div class="row added-alloc-items-heading">
								<!--<div class="col-md-12 ">
									<div class="row  alloc-activity-title">
										<div class="col-md-8 type activityrate">
											<span></span>
										</div>
										<div class="col-md-4 text-right">
											<a href="javascript:void(0);" class="btn btn-primary resource-list-btn">Add Allocation</a>
											<a href="javascript:void(0);" class="btn btn-primary close-resource-list-btn">Close Allocation</a>
										</div>
									</div>
								</div> -->
								</div>
								<div class="allocation-list-items-cntnr added-activity-list-wrpr collapse in">

								</div>
								
							</div><br>
							<div class="col-md-12 type project-boq" >
								<a href="javascript:void(0);"  class="btn btn-primary resource-list-btn"><span class="icon-add"></span>Add Activity</a>
								<a href="javascript:void(0);" class="btn btn-primary close-resource-list-btn">Close Allocation</a>&nbsp;&nbsp;&nbsp;
								<a href="javascript:void(0);" class="expand-collapse-palist icon-arrow-up1" data-toggle="collapse" data-target=".added-activity-list-wrpr"></a>
							</div>
							<div class="col-md-12 add-activities-section-title">
								<label>Add Activities</label>
								<a href="#" class="btn btn-primary close-activity-list-btn"><span class="icon-close"></span> Close</a>
							</div>
							</div>
							
							<div class="allocation-cntnt-wrpr resource-not-allocated">
					<div class="allocation-left-bar items-select-bar">
						
						
						
						
					</div>
					<div class="resource-not-allocated-info">
						<div class="icon-info3"></div>
						<div class="info-text">
							<p id="alredyadded">Resources not allocated to the activity</p>
							<span>To allocate resources, click on the left tab</span>
						</div>
					</div>
					<div class="resources-from-selected-item-wrpr ">
						<div class="row">
							<div class="col-md-12 ">
							
								
								<div class="search-and-allocate-content-wrpr">
								<div class="search-and-actions-wrpr row">
									<div class="content-search-wrpr col-md-12 col-sm-12">
						                 <input type="hidden" id="resourcegroupval" name="">
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
						<div class="preloader" style="display: none;  margin-left: 480px;"><center><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"></center></div>
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