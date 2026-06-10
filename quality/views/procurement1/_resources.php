<?php
use app\models\Vendors;  
use app\models\Resourcetype;  
use app\models\Brand;  
use app\models\AccountsItem;  
use app\models\AccountsSub;
?>


<div class="panel panel-default resources-tab tab acco-two">
	<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/procurement/resourcefunctions.js" type="text/javascript"></script>
	<script src="<?php echo Yii::$app->request->baseUrl; ?>/js/magicsuggest.js" type="text/javascript"></script>
  	<!-- <input type="radio" id="rd5" class="res-tab" name="rd"> -->
			
	<div class="panel-heading" >
		  <h4 class="panel-title acc_trigger"  id="Resources">
			<a data-toggle="collapse" data-parent="#accordionmaster" href="#collapseresource">
				<span class="image-icon acc_trigger"></span>Resources
			</a>
		  </h4>
	</div>
			
	<div id="collapseresource" class="tab-content cOrder-body panel-collapse collapse" id="res-tab-show">
	  	<div class="panel-body">				  
		  	<div id="resourceslistsections" style="overflow: hidden; display: none; ">
				<div class="search-and-content-wrpr">
                    <div class="search-and-actions-wrpr row" style="padding-bottom:0px;">
						<div class="content-search-wrpr col-md-9 col-sm-9" id="resourcesearchdiv">
							<select class="form-control" id="searcselectvendor" >
							    <option value="none">Select Vendor</option>
	                            <?php
	                            $vendorlist=Vendors::find()->where(['Status'=>0])->all();
	                            foreach($vendorlist AS $list):
	                                echo "<option value='".$list->Vendor_Id."'>".$list->Name."</option>";
	                            endforeach;
	                            ?> 
                            </select>

                            <input type="text" placeholder="Location" id="searchlocation" class="form-control location">
							<select class="form-control" id="searchbrand">
								<option value="none">Select Brand</option>
								<?php
                            		$brandlist=Brand::find()->where(['status'=>0])->all();
		                            foreach($brandlist AS $list):
		                                echo "<option value='".$list->id."'>".$list->name."</option>";
		                            endforeach;
	                            ?> 
							</select>	

                     		<input type="text" placeholder="Resource Search" id="searchresourcename" class="form-control">
                     		<button id="resourcesearch" class="btn btn-primary" type="button"><span class="icon-search5"></span></button>
						</div>

						<div class="col-md-3" id="restyperefresh" style="display: none">
                          	<select id="searcselectrestype" class="form-control">
	                            <option value="none">Select Resource Type</option>
	                            <?php
		                            $typelist=Resourcetype::find()->where(['Status'=>0])->all();
		                            foreach($typelist AS $list):
		                                echo "<option value='".$list->ResourceType_Id."'>".$list->Name."</option>";
		                            endforeach;
	                            ?>
                            </select>

                       	</div>

                        <div class="col-md-3" style="display: none">
                            <select id="searcselectgroup" class="form-control">
                            	<option value="none">Select Resource Group</option>
                            </select>

                     	</div>

						<div class="content-action-wrpr col-md-3 col-sm-3">

							<button type="button" class="btn btn-primary addForm" id="addresources" title="Add Resource"><span class="icon-add"></span> Add</button>

							<button type="button" class="btn btn-danger" id="listresources" style="display: none"><span class="glyphicon glyphicon-list-alt"></span>List Resources</button>

						</div>
						
							
					</div>

					<div class="content-wrpr">
						<div class="row">
							<div class="col-md-12 col-sm-12 typeGroup-indication ">
							<span class="type">Resource Type:</span><span id="procrestypename"></span><input type="hidden" id="procrestypeid" value="">
						</div>
						</div>
					 	<div id="resourceeaddsections" style="overflow: hidden;/* display: none; */"> 
							<form id="addresourceform">
								<!-- form starts here -->
								<div class="resources-add-cntnt-wrpr row" style="padding-top:3px; padding-bottom:3px;">
									<div class="col-md-1"></div>
										<div class="col-md-10">
											<div class="row">
												<div class="col-md-3" style="width: 180px;">
													<div class="form-title" style="display:block; margin-top:12px; font-size:17px; margin-bottom:0px; margin-top:10px; text-align:left;">Resource Type: 

												 	</div> 
												</div>

						                     	<div class="col-md-4" style="display:block; margin-top:11px; font-size:17px; margin-bottom:0px; margin-top:9px; text-align:left;padding-right: 0px;width: 250px;">
							                     	<select class="form-control" id="restyperes" name="resourcetype"  tabindex="1">
								                  		<option value="none">Select Resource Type</option>
								                     	<?php

								      						$typelist=Resourcetype::find()->where(['Status'=>0])->andWhere(['Status' => 0])->all();

								             				foreach($typelist AS $list):  
								                        		echo "<option value='".$list->ResourceType_Id."'>".$list->Name."</option>";
								                     		endforeach;
								                    	?>
							        
							                 		</select>
						                 		</div>

												<div class="col-md-12">
													&nbsp;
												</div>
												<div class="col-md-4">
										
													<div class="form-group">
														<label>Resource Name<span style="color:red;"> *</span></label>
														<input class="form-control" type="text" id="resourcename" name="resourcename" placeholder="Resource Name" tabindex="2">
														<span class="error" style="display: none;"></span>
													</div>
											
											
						                			<div class="form-group">
														<label>Vendor<span style="color:red;"> *</span></label>
						            					<input class="form-control" list="vendorlist" id="vendor" name="vendor-choice" placeholder="Select Vendor" tabindex="6"/>

						        						<datalist id="vendorlist">
												            <?php

												             $vendors=Vendors::find()->Where(['Status'=>0])->all();
												            foreach($vendors AS $vendor):
												                echo "<option value='".$vendor->Name."'></option>";
												            endforeach;
												            ?>
												        </datalist>

						        						<span class="error" style="display: none;"></span>

													</div>

													<div class="form-group">
														<label>Email Address</label>
														<input class="form-control" id="vendoremail" name="vendoremail" placeholder="Enter email address" type="text" tabindex="9"/>
													</div>

													<div class="form-group" id="rsgrpshow" style="display:none;">
														<label>Equipment Type</label>
							                            <select id="resgroup" class="form-control">
							                            	<option value="none">Select Equipment Type</option>
							                            	<option value="102">Engine Driven Equipments </option>
							                            	<option value="154">Motor Driven Equipments</option>
							                            </select>

							                     	</div>
											
												</div>
										
												<div class="col-md-4">
													<div class="form-group">
														<label>Brand<span style="color:red;"> *</span></label>

									 					<input class="form-control" list="brandlist" id="brand" name="brand-choice" placeholder="Select Brand" tabindex="3"/>

						        						<datalist id="brandlist">
												            <?php
												           // $vendors = Vendors::model()->findAll('Status=:type', array(':type' => '0'));

												             $brands=Brand::find()->Where(['Status'=>0])->all();
												            foreach($brands AS $brand):
												                echo "<option value='".$brand->name."'></option>";
												            endforeach;
												            ?>
						        						</datalist>

						        						<span class="error" style="display: none;"></span> 

						         					</div>  
						         					
													<div class="form-group">
														<label>Vendor Location<span style="color:red;"> *</span></label>

														<input class="form-control" type="text" id="vendorlocation" name="vendorlocation" placeholder="Vendor Location" tabindex="7">
														<span class="error" style="display: none;"></span>
												
													</div>
											
													<div class="form-group">
														<label>Account Head<span style="color:red;"> *</span></label>

						                 				<input class="form-control accounthead" list="accountheadlist" id="accounthead" name="accounthead-choice" placeholder="Account Head" tabindex="10"/>

												        <datalist id="accountheadlist">
												            <?php
												           // $accountheads=AccountsItem::model()->findAll();
												             $accountheads=AccountsItem::find()->where(['Status'=>0])->andWhere(['account_type' => 8])->orderBy(['name' => SORT_ASC])->all();

												            foreach($accountheads AS $accounthead):
												                echo "<option value='".$accounthead->name."'></option>";
												            endforeach;
												            ?>
												        </datalist>

						        						<span class="error" style="display: none;"></span>
												
													</div>	

													<div class="row equipmentdetails" style="display:none;">
						                    			<div class="col-md-6" id="pownam">
						                            		<div class="form-group">
						                            			<label id="consumptionlabel">Diesel (L/H)</label>
						                        				<input class="form-control" type="text" id="equconsumption" name="equconsumption" tabindex="4">
						                    					<span class="error" style="display: none;"></span>
						                            		</div>
						                        		</div>

														
						                        
						                        		<div class="col-md-6" id="dirate">
						                            		<div class="form-group">
						                                		<label id="ratelabel">Diesel Rate</label>
						                            			<input class="form-control"  type="text" id="equrate" name="equrate" tabindex="5">
						                            			<span class="error" style="display: none;"></span>                                         
						                            		</div>
						                        		</div>
						                    
						                    		</div>
											
												</div>
										
												<div class="col-md-4">

										  			<div class="row">
						                    			<div class="col-md-3">
						                            		<div class="form-group">
						                            			<label id="unitheads">Unit</label>
						                        				<input class="form-control" type="text" id="resourceunit" name="resourceunit" placeholder="Unit" tabindex="4">
						                    					<span class="error" style="display: none;"></span>
						                            		</div>
						                        		</div>
						                        
						                        		<div class="col-md-4 plantrate">
						                            		<div class="form-group">
						                                		<label id="rateheads">Rate</label>
						                            			<input class="form-control"  type="text" id="resourcerate" name="resourcerate" placeholder="Rate" tabindex="5" required="">
						                            			<span class="error" style="display: none;"></span>                                         
						                            		</div>
						                        		</div>
						                        		

						                        		<div class="col-md-1 plantrate">
    
														    <div class="row">
														        &nbsp;
														    </div>
														</div>
														<div class="col-md-4 schedulediv" style="display:none;">    
														    <div class="row">
														        <label for="schedule">Schedule item</label>
														        <input class="" type="checkbox" id="schedules" name="schedules" value="1">
														        
														        <div class="col-md-6">
														            &nbsp;
														        </div>                                  
														    </div>
														</div>

						                        	
						                        		
						                    
						                    		</div>

													<div class="form-group">
														<label>Phone<span style="color:red;"> *</span></label>
														<input class="form-control" id="vendorphone" name="vendorphone" placeholder="Enter Phone Number" type="text" tabindex="8" />
														 <span class="error" style="display: none;"></span> 
														
													</div>
											
													<div class="form-group">
														<label>Account Sub Group<span style="color:red;"> *</span></label>
						                  				<input class="form-control" list="accountsubgrplist" id="accountsubgrp" name="accountsubgrp-choice" value="" placeholder="Select Account Subgroup" tabindex="11"/>

												        <datalist id="accountsubgrplist">
												            <?php

												        	$accountsubs=AccountsSub::find()->where(['Status'=>0])->andWhere(['master_id'=>5])->all();
												    
												            foreach($accountsubs AS $accountsub):
												                echo "<option value='".$accountsub->name."'></option>";
												            endforeach;
												            ?>
												        </datalist>

						        						<span class="error" style="display: none;"></span>
												
													</div>

													<div class="row equipmentdetails" style="display:none;">
						                    			<div class="col-md-6">
						                            		<div class="form-group">
						                            			<label>Maint Cost/Hr</label>
						                        				<input class="form-control" type="text" id="equmaintenancecost" name="equmaintenancecost" tabindex="4">
						                    					<span class="error" style="display: none;"></span>
						                            		</div>
						                        		</div>
						                        
						                        		<div class="col-md-6">
						                            		<div class="form-group">
						                                		<label>Machine Rate</label>
						                            			<input class="form-control"  type="text" id="machinerate" name="machinerate" tabindex="5" readonly>
						                            			<span class="error" style="display: none;"></span>                                         
						                            		</div>
						                        		</div>
						                    
						                    		</div>
											
												</div>

												<div class="col-md-12 text-center">
													<div class="form-group text-center" style="position:relative; top:6px;">
														<span>&nbsp;</span><br/>
														<button type="button" class="btn btn-danger cancel" id="cancelres" ><span class="icon-close"></span> Cancel</button>

														<!-- <button type="button" class="btn btn-danger" id="saveresource"><span class="glyphicon glyphicon-saved"></span>Save</button> -->

														<button type="button" class="btn btn-primary" 
														id="saveresource"><span class="icon-check"></span> Add Resource</button>
													</div>
												</div>				
											</div>

											<div class="text-center">

											</div>
									
										</div>

										<div class="col-md-1"></div>
									</div>
								</form>
							</div>

							<!-- form ends here -->
							
							<div class="addvendor-resource-cntnt-wrpr row">
							     
								<div class="col-md-1"></div>
								<div class="col-md-10">
									<div class="row">
										<div class="col-md-12">
											<div class="form-title" style="display:block; margin-top:10px; font-size:17px;">Resource: <span class="resourcename"></span>
											</div>
										</div>
										<div class="col-md-12">
											&nbsp;
										</div>
										<div id="resvendoradd">
										
										</div>	
										
									</div>
							
									<div class="text-center">										
										
									</div>
		
								</div>
								<div class="col-md-1"></div>
								
							</div>
                       
                       		<!-- edit (Resrouces) form starts here -->
							<div class="edit-resources-edit-cntnt-wrpr row">

								<div id="editresource">

								</div>
								
							</div>
							<!-- edit (Resrouces) form ends here -->
							
							<!-- list start here -->
							<div class="resources-cntnt-wrpr row">
								<form>
									<!-- <table class="table table-bordered" id="procresourcetable" style="display: table; overflow: hidden;">
		                                <thead>
			                                <tr>
			                                    <th style="width:5%;">#</th>
			                                    <th style="width:40%;">Resource</th>
			                                    <th style="width:10%;">Location</th>
			                                     <th style="width:230px;"></th> 
												<th style="width:10%;">Brand</th>
			                                    <th style="width:5%;">Unit</th>
			                                    <th style="width:10%;">Rate</th>
			                                    <th style="width:10%;">Last Updated</th>
			                                    <th style="width:10%;" ></th>
			                                </tr>
			                                <tr class="preloader" style="display: none;">
			                                	<td colspan="12" align="center"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"> </td>
			                                </tr>
		                                </thead>
										<tbody id="procresourceitems">

										</tbody>-->
		                               
		                            <!-- </table>  -->
		                            <div class="panel-group" id="procresourceitems">

									</div>
								</form>
								
							</div>	
							<!-- list end here -->
						</div>



				</div>
			</div>
				  
	  	</div>
	</div>
</div>
