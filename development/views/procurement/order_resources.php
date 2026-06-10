<?php
use app\models\Projects;
use app\models\Resources;
use app\models\Resourcetype;


$resourceTypeSelect = '';
if($resourceTypes){
	$resourceTypeSelect = '<select id="resourceTypeFilter" name="resourceTypeFilter" class="form-control">
		                     						<option value="none">Select Resource Type</option>';
	foreach ($resourceTypes as $key => $resourceType) {
		$resourceTypeSelect .= '<option value="'.$resourceType->ResourceType_Id.'">'.$resourceType->Name.'</option>';
	}	                     						
	$resourceTypeSelect .= '<select>';
}

$resourceGroupSelect = '';
if($resourceGroups){
	$resourceGroupSelect = '<select id="resourceGroupFilter" name="resourceGroupFilter" class="form-control">
		                     						<option value="none">Select Resource Group</option>';
	foreach ($resourceGroups as $key => $resourceGroup) {
		$resourceGroupSelect .= '<option value="'.$resourceGroup->Resource_group_Id.'">'.$resourceGroup->Resource_group_Name.'</option>';
	}	                     						
	$resourceGroupSelect .= '<select>';
}

?>

<div class="panel panel-default  acco-two tab">

	<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/procurement/allorders.js" type="text/javascript"></script>

	<div class="panel-heading">
      <h4 class="panel-title " id="chooseallorder">
        <a data-toggle="collapse" data-parent="#accordionindex" href="#collapseallorders">
        <span class="icon-shopping_cart"></span>Order Management</a>
      </h4>
    </div>

    <div id="collapseallorders" class="tab-content panel-collapse cOrder-body panel-collapse collapse">

    	<div style=" padding: 20px; padding-top: 30px;">

    			<div class="row">
              <div class="col-md-1">&nbsp;</div>
              <div class="col-md-10 text-center">
	              	<div class="order_management_nav_bar">
				    					<ul class="nav nav-tabs text-center topsbars" style="padding-left:0;">
												<li class="frstcl active">
													<a data-toggle="pill" class="orderManagementTab" data-type="Material"  href="#Material">
														<span class="icon-shopping_cart"></span> Purchase Orders
														<?php if($mareial_due_cnt) { ?>
														<span class="due_cnt_type_notification"><?php echo $mareial_due_cnt ?></span>
														<?php } ?>
													</a>
												</li> 
												<li class="">
													<a data-toggle="pill" class="orderManagementTab" data-type="SubContractor" href="#SubContractor">
														<span class="icon-tools"></span> Work Orders
														<?php if($subcon_due_cnt) { ?>
														<span class="due_cnt_type_notification"><?php echo $subcon_due_cnt ?></span>
														<?php } ?>
													</a>
												</li>
												<li class="">
													<a data-toggle="pill" class="orderManagementTab" data-type="DirectLabour" href="#DirectLabour">
														<span class="icon-th-list"></span> Direct Work Order
														<?php if($subcon_due_cnt) { ?>
														<span class="due_cnt_type_notification"><?php echo $subcon_due_cnt ?></span>
														<?php } ?>
													</a>
												</li>
												<li class="">
													<a data-toggle="pill" class="orderManagementTab" data-type="PlantEquipment" href="#PlantEquipment">
														<span class="icon-truck"></span> Plant & Equipment
													</a>
												</li>
											</ul>
									</div>
							</div>
              <div class="col-md-1">&nbsp;</div>
    			</div>


    			<div class="order_management_wrapper">

    					<!------MATERIALS ---------------->
					   	<div class="order_management_container" id="orderMaterialContainer" style="display:block;">

								<div class="row">
                    <div class="col-md-1">&nbsp;</div>
                    <div class="col-md-10" >

                    	<?php
                    	if($resourcesArr){
                    	?>
                    	<!-- 
                    	<div style="margin-bottom: 30px !important;background: #eee;padding: 10px;border: 1px solid #ccc;">
	                    	<div class="row "  >
					                    <div class="col-md-12 text-center" style="padding-bottom: 15px; font-weight: bold;">
					                    		Search & Filter
					                    </div>
		                    </div>
	                    	<div class="row " style="display: flex;align-items: center;" >
					                    <div class="col-md-4 text-left" style="">
					                    		<input type="text" class="form-control" placeholder="Enter Resource Name" name="">
					                    </div>
					                    <div class="col-md-4 text-left" >
					                    	<div><?php echo $resourceTypeSelect ?></div>
					                    </div>
					                    <div class="col-md-1 text-center" style="padding-right: 0;  padding-left: 25px;">
					                    	<div >
																		<label class="switch po_history_switch">
																		  <input type="checkbox" class="po_history_checkbox" >
																		  <span class="slider round"></span>
																		</label>
					                    	</div>
					                    </div>
					                    <div class="col-md-1 text-left" style="padding-left: 5px;">
					                    	<span style="font-weight: bold; cursor: pointer;" onclick="$('.po_history_switch').trigger('click');">History</span>
					                    </div>
					                    <div class="col-md-1 text-center" style="padding-right: 0; padding-left: 25px;">
					                    	<div >
																		<label class="switch po_dueorder_switch">
																		  <input type="checkbox">
																		  <span class="slider round"></span>
																		</label>
					                    	</div>
					                    </div>
					                    <div class="col-md-1 text-left no-padding" style="padding-left: 5px !important;">
					                    	<span style="font-weight: bold; cursor: pointer;" onclick="$('.po_dueorder_switch').trigger('click');">Due Orders</span>
					                    </div>
					                    
			                    		<input type="hidden" id="selectedResource" value="">
	                  		</div>
                  		</div>
                  		 -->



                  		<div class="order_po_container">
		                    	<?php
		                    		foreach ($resourcesArr as $key => $resource) {
		                    			$vendor_id 	 = $resource['vendor_id'];
		                    			$resource_id = $resource['resource_id'];
		                    			$ven_res_id	 = $vendor_id.'_'.$resource_id;

			                				$dueDateClass = ($resource['due_notify_flag']) ? 'text-due' : '';
		                    	?>

		                    	<div class="row "  style="margin-bottom: 30px !important;">
		                    			<div class="collapsibleSimple">
		                    				<div class="collapsibleSimpleTitle row">
								                  	<div class="col-md-4" style="font-weight:600;" > 
								                  		<?php echo $resource['resource_name'] ?> 
								                  	</div>
								                  	<div class="col-md-3 text-center <?php echo $dueDateClass ?> ">
								                    		<?php echo $resource['due_date'] ?> 
								                  	</div>
								                    <div class="col-md-4 text-left" > 
								                    		<?php echo $resource['vendor'] ?> 
								                    </div>
								                    <div class="col-md-1 no-padding text-center" > 
								                    		
								                    		<div>
								                    			<?php 
								                    			if($resource['due_notify_cnt'] > 0){
								                    			?>
								                        	<span class="due_cnt_notification"><?php echo $resource['due_notify_cnt'] ?></span>
								                        	<?php } ?>
								                      	</div>
								                    </div>                    				
								           			</div>
		                    			</div>

															<div class="collapsibleContent">
																	<div class="collapsibleContentInner">
																		<div class="row resource_top_bar" >
										                    <div class="col-md-2 ">
										                    	Unit :<b> <?php echo $resource['resource_unit'] ?></b>
														    				</div>    
										                    <div class="col-md-6 text-center">
										                    	Resource Type : <b><?php echo $resource['res_type_name'] ?></b>
														    				</div>    
										                    <div class="col-md-1 text-right no-padding">
										                    	Vendor : 
									                    	</div>
										                    <div class="col-md-3">
										                    	<select name="vendor" id="vendor_<?php echo $ven_res_id; ?>" class="form-control">
										                    		<option value="<?php echo $resource['vendor_id'] ?>"><?php echo $resource['vendor'] ?></option>
										                    	</select>
														    				</div>    
																	  </div>


							                    	<?php
							                    		foreach ($resource['projects'] as $proj_id => $project) {
								                				$proj_ven_res_id 		= $project['project_id'].'_'.$ven_res_id;

							                  		?>
																	  <div class="po-project-container">
						  	                    		<div class="project-title">
						  	                    				<div class="row">
							  	                    				<div class="col-md-11">
												                    		Project : <b><?php echo $project['project_name'] ?></b>
												                    	</div>
											                    		<div  class="col-md-1 text-right">
												                        <a href="#dashboardPopup" class="dropdown-toggle resource-icon icon-chart2" data-toggle="modal" data-target="#dashboardPopup"  id="resourceResource" data-type="Resource"  data-project="<?php echo $project['project_name'] ?>"   data-recourceid="<?php echo $resource['resource_id'] ?>"></a>
						  					                    	</div>		
					  					                    	</div>						                    		
										                    </div>


										                    <div class="po-resource-list-container">
												                    	<div class="row po-resource-list-header " >
														                    <div class="col-md-12" >
														                        <div class="col-md-5 text-left no-padding" >
														                        		<div class="row" >
																			                    <div class="col-md-12 no-padding" >
																			                        <div class="col-md-1 no-padding text-center" >
																		                        		<input type="checkbox" class="actvtyResSelectAll" data-venresid="<?php echo $proj_ven_res_id ?>"> 
																	                            </div>
																			                        <div class="col-md-11 " >
																		                            <label style="margin-bottom:0px;">Activity</label>
																	                            </div>
															                            </div>
															                          </div>
														                        </div>
														                        <div class="col-md-2 text-center " >
														                            <label style="margin-bottom:0px;  " title="">Due Date</label>
														                        </div>
														                        <div class="col-md-1 text-center no-padding"  >
														                            <label style="margin-bottom:0px; ">Total Qty</label>
														                        </div>
														                        <div class="col-md-1 text-center"  >
														                            <label style="margin-bottom:0px; ">Rate</label>
														                        </div>
														                        <div class="col-md-1 text-center"  >
														                            <label style="margin-bottom:0px; ">Quantity</label>
														                        </div>
														                        <div class="col-md-2 text-right" >
													                            <label style="margin-bottom:0px;" title="">Amount</label>
												                            </div>

														                    </div>
														                	</div>

															                <div class="row "  style="">
			                	                    		<form id="place_order_form_<?php echo $proj_ven_res_id; ?>" method="POST">

															                	<?php 
															                		if($activity_resources = $project['acty_resources']) { 
															                			foreach ($activity_resources as $key => $activity_res) {
															                				$dueResourceRow = ($activity_res['due_notify_flag']) ? 'resourceRowDue' : '';
															                				$ven_res_act_id = $project['project_id'].'_'.$ven_res_id.'_'.$activity_res['activity_id'];
															                				//$proj_ven_res_act_id = $project['project_id'].'_'.$ven_res_act_id;
															                	?>
															                			<div class="col-md-12 resourceRow <?php echo $dueResourceRow ?>" style="">


									  							                      <div class="col-md-5 text-left no-padding" >
																                        		<div class="row" >
																					                    <div class="col-md-12 no-padding" >
																					                        <div class="col-md-1 no-padding text-center">
																					                        	<input type="checkbox" class="actvtyResSelect actvtyResSelect<?php echo $proj_ven_res_id ?>" name="activity[]" value="<?php echo $activity_res['activity_id'] ?>" data-venresactid="<?php echo $ven_res_act_id ?>"  data-venresid="<?php echo $proj_ven_res_id ?>"> 
																					                        </div>
																					                        <div class="col-md-11">
																					                            <?php echo $activity_res['activity_name'] ?>
																					                        </div>
																	                            </div>
																	                          </div>
																                        </div>

																                        <div class="col-md-2 text-center" >
																                            <?php echo $activity_res['due_date'] ?>
																                        </div>
																                        <div class="col-md-1 text-center">
																                            <?php echo $activity_res['tot_quantity'] ?>
																                        </div>
																                        <div class="col-md-1 text-center" style="padding: 0px;">
																                            <input class="form-control resourceRate" name="rate[<?php echo $activity_res['activity_id'] ?>]" id="resourceRate<?php echo $ven_res_act_id ?>" type="number" value="<?php echo $activity_res['rate'] ?>" data-venresid="<?php echo $proj_ven_res_id ?>" data-venresactid="<?php echo $ven_res_act_id ?>" style="padding: 6px 7px; display:inline;">
																                        </div>
																                        <div class="col-md-1 text-center "  style="padding-right: 0px;">
																                            <input class="form-control resourceQty" name="quantity[<?php echo $activity_res['activity_id'] ?>]" id="resourceQty<?php echo $ven_res_act_id ?>" type="number" value="<?php echo $activity_res['quantity'] ?>" data-venresid="<?php echo $proj_ven_res_id ?>" data-venresactid="<?php echo $ven_res_act_id ?>" style="padding: 6px 7px; display:inline; ">
																                        </div>									                        

																                        <div class="col-md-2 text-right"  id="resourceAmt<?php echo $ven_res_act_id ?>">
															                            <?php echo $activity_res['amount'] ?>
														                            </div>
																                    </div>
															                	<?php } } ?>
		          						                    		<input type="hidden" name="project_id" value="<?php echo $project['project_id'] ?> ">
		          						                    		<input type="hidden" name="resource_id" value="<?php echo $resource['resource_id'] ?> ">
																									<input type="hidden" name="ordertype" value="1">
															                	</form>
													                    </div>

												                    	<div class="row po-resource-list-footer" style="">
																								<div class="col-md-12 po-resource-total">
																									<div  class="col-md-2 text-left no-padding">
																										<button type="button" class="btn btn-primary place_order_btn" id="place_order_btn_<?php echo $proj_ven_res_id; ?>" 
																											href="#placeOrderPopup" 
																											data-target="#placeOrderPopup" 
																											data-toggle="modal" 
																											data-vendorid="<?php echo $vendor_id; ?>"
																											data-resourceid="<?php echo $resource_id; ?>"  
																											data-proj_ven_res_id="<?php echo $proj_ven_res_id; ?>"  
																											style="padding:3px 12px; font-size:13px;" 
																											value="" 
																											disabled
																											title="Select atleast one Resource" >
																												Place Order
																										</button>
																									</div>

																									<div class="col-md-7 text-right" style="">
																										Total
																									</div>
																									<div class="col-md-1 text-center" id="qtyTotal<?php echo $proj_ven_res_id ?>"  >
																										00
																									</div>
																									<div class="col-md-2 text-right" id="amtTotal<?php echo $proj_ven_res_id ?>" >00.00</div>
																								</div>
												                    	</div>
												                </div>

																				
																	  </div>
																		<?php } ?>
																	  
																	  
							                    </div>

															</div>

		                    	</div>

		                    	<?php
		                    	} }
		                    	else{
		                    	?>
		                    	<div style="text-align:center; padding: 50px;">No Resources Found!</div>
		                    	<?php
		                    	}
		                    	?>
                    	</div>

                    	<div class="order_po_history_container" style="display:none;">
                    				

                    				<div class="invoiceContent">
		                  				<?php if($purchaseOrdersHistory) { ?>

					                    	<div class="row po-resource-list-header ">
							                    <div class="col-md-12">
							                        <div class="col-md-1 text-left no-padding">
							                            <label style="margin-bottom:0px;  " title="">Order Date</label>
							                        </div>
							                        <div class="col-md-3 text-left " >
							                            <label style="margin-bottom:0px;  " title="">Project</label>
							                        </div>
							                        <div class="col-md-3 text-left">
							                            <label style="margin-bottom:0px; ">Item</label>
							                        </div>
							                        <div class="col-md-3 text-left">
							                            <label style="margin-bottom:0px; ">Vendor</label>
							                        </div>
							                        <div class="col-md-1 text-center">
							                            <label style="margin-bottom:0px;" title="">Amount</label>
							                        </div>
							                        <div class="col-md-1 text-center">
							                            <label style="margin-bottom:0px;">Actions</label>
							                        </div>
							                    </div>
							                	</div>

								                <div class="row " style="">

								                			<?php
								                			foreach ($purchaseOrdersHistory as $purchaseOrder) {
								                				if($purchaseOrder->order_type == 1) { 

								                				$amount = 0;
								                				foreach($purchaseOrder->resources AS $resourceitem):
																					$amount = $amount + $resourceitem['amount'];
																					if($resourceitem['cgst']!=0 & $resourceitem['sgst']!=0):
																						$gst=$resourceitem['cgst'] + $resourceitem['sgst'];
																					elseif($resourceitem['igst']!=0):
																						$gst=$resourceitem['igst'];
																					else:
																						$gst=0;
																					endif;
																				endforeach;

																        $gstamount=($amount * $gst) / 100;
																				$amntincgst=$amount + $gstamount;
								                			
								                			?>
								                			<div class="col-md-12 resourceRow " style="">
									                        <div class="col-md-1 text-left no-padding">
									                            <?php echo $purchaseOrder->orderdate ?>									                        
									                         </div>
									                        <div class="col-md-3 text-left" >
									                            <?php echo $purchaseOrder->project->Name ?>									                        
									                         </div>
									                        <div class="col-md-3 text-left">
									                            <?php echo $purchaseOrder->resources[0]['resource_name'] ?>									                        
									                        </div>
									                        <div class="col-md-3 text-left no-padding">
									                            <?php echo $purchaseOrder->vendor['Name'] ?>									                        
									                        </div>
									                        <div class="col-md-1 text-right">
									                            <?php echo $amntincgst ?>									                        
									                        </div>
									                        <div class="col-md-1 text-center">
									                            <a target="_blank" href="printorderresource?id=<?php echo $purchaseOrder->order_id ?>" data-url="printorderresource?id=<?php echo $purchaseOrder->order_id ?>" class="btn btn-primary btn-sm icon-eye" title="View Order"></a>	
									                        </div>
									                    </div>
									                  <?php } } ?>

						                    </div>

					                    	<div class="row po-resource-list-footer" style="padding: 15px;"></div>
					                    <?php } else { ?>
																<div style="padding:50px; text-align:center;">No Invoices found!!</div>
					                    <?php }  ?>

					              </div>


                    	</div>


                    </div>
                    <div class="col-md-1">&nbsp;</div>
								</div>

							</div>
    					<!----- END ---------------->


    					<!------SUB CONTRACTOR ---------------->
							<div class="order_management_container" id="orderSubContractorContainer"  style="display:none;">
								<div class="row">
                    <div class="col-md-1">&nbsp;</div>
                    <div class="col-md-10" >

                    	<?php
                    	if($subContArr){
                    	?>
                    	<!-- 
                    	<div style="margin-bottom: 30px !important;background: #eee;padding: 10px;border: 1px solid #ccc;">
	                    	<div class="row "  >
					                    <div class="col-md-12 text-center" style="padding-bottom: 10px; font-weight: bold;">
					                    		Search & Filter
					                    </div>
		                    </div>
	                    	<div class="row " style="display: flex;align-items: center;" >
					                    <div class="col-md-4 text-left" style="">
					                    		<input type="text" class="form-control" placeholder="Enter SubContractor Name" name="">
					                    </div>
					                    <div class="col-md-3 text-left" >
					                    	<div><?php echo $resourceTypeSelect ?></div>
					                    </div>
					                    <div class="col-md-3 text-center" >
					                    	<div ><?php echo $resourceGroupSelect ?></div>
					                    </div>
					                    <div class="col-md-2 text-right">
					                    	<label><input type="checkbox"> Due Orders Only</label>
					                    </div>
	                  		</div>
                  		</div>
                  		 -->


                    	<?php
                    		foreach ($subContArr as $proj_id => $subContProj) {
                    	?>

                    	<div class="row "  style="margin-bottom: 30px !important;">
                    			<div class="collapsibleSimple">
                    				<div class="collapsibleSimpleTitle row">
						                  	<div class="col-md-8" style="" > 
                                    Project : <b><?php echo $subContProj['project_name'] ?></b>

						                  	</div>
						                  	<div class="col-md-3 text-center <?php //echo $dueDateClass ?> ">
						                    		<?php echo $resource['due_date'] ?> 
						                  	</div>
						                    <div class="col-md-1 no-padding text-center" > 
						                    </div>                    				
						           			</div>
                    			</div>

													<div class="collapsibleContent">
															<div class="collapsibleContentInner">

																<!-- <div class="row resource_top_bar" >
								                    <div class="col-md-2 ">
								                    	Unit :<b> <?php echo $resource['resource_unit'] ?></b>
												    				</div>    
								                    <div class="col-md-6 text-center">
								                    	Resource Type : <b><?php echo $resource['res_type_name'] ?></b>
												    				</div>    
								                    <div class="col-md-1 text-right no-padding">
								                    	Vendor : 
							                    	</div>
								                    <div class="col-md-3">
								                    	<select name="vendor" id="vendor_<?php echo $ven_res_id; ?>" class="form-control">
								                    		<option value="<?php echo $resource['vendor_id'] ?>"><?php echo $resource['vendor'] ?></option>
								                    	</select>
												    				</div>    
															  </div> -->




                                <?php

                                    foreach ($subContProj['resources'] as $key => $resource) {
						                    			$vendor_id 	 = $resource['vendor_id'];
						                    			$resource_id = $resource['resource_id'];
						                    			$ven_res_id	 = $vendor_id.'_'.$resource_id;

							                				$dueDateClass = ($resource['due_notify_flag']) ? 'text-due' : '';
                                      $proj_ven_res_id        = $subContProj['project_id'].'_'.$ven_res_id;
                                ?>


                                <div class="po-project-container">
                                    <div class="project-title">

                                    		<div class="row title-row">
                                    				<div class="col-md-4" style="font-weight:600;" > 
												                  		<?php echo $resource['resource_name'] ?> 
												                  	</div>
												                  	<div class="col-md-3 text-center <?php //echo $dueDateClass ?> ">
												                    		<?php echo $resource['due_date'] ?> 
												                  	</div>
												                    <div class="col-md-4 text-left" > 
												                    		<?php echo $resource['vendor'] ?> 
												                    </div>
												                    <div class="col-md-1 no-padding text-center" > 
												                    		<div style="float:left; margin-right: 10px;">
													                        <!-- <a href="#dashboardPopup" class="dropdown-toggle resource-icon icon-chart2" data-toggle="modal" data-target="#dashboardPopup"  id="resourceResource" data-type="Resource" data-recourceid="<?php //echo $resource['resource_id'] ?>"></a> -->
							  					                    	</div>
												                    		<div>
												                    			<?php 
												                    			if($resource['due_notify_cnt'] > 0){
												                    			?>
												                        	<span class="due_cnt_notification"><?php echo $resource['due_notify_cnt'] ?></span>
												                        	<?php } ?>
												                      	</div>
												                    </div> 
                                    		</div>



                                    </div>


			                            <div class="po-resource-list-container">
								                    	<div class="row po-resource-list-header " >
										                    <div class="col-md-12" >
										                        <div class="col-md-4 text-left no-padding" >
										                        		<div class="row" >
															                    <div class="col-md-12 no-padding" >
															                        <div class="col-md-1 no-padding text-center" >
														                        		<input type="checkbox" class="actvtyResSelectAll" data-venresid="<?php echo $proj_ven_res_id ?>"> 
													                            </div>
															                        <div class="col-md-11 " >
														                            <label style="margin-bottom:0px;">Activity</label>
													                            </div>
											                            </div>
											                          </div>
										                        </div>
										                        <div class="col-md-2 text-center " >
										                            <label style="margin-bottom:0px;  " title="">Due Date</label>
										                        </div>
										                        <div class="col-md-1 text-center"  >
										                            <label style="margin-bottom:0px; ">Unit</label>
										                        </div>
										                        <div class="col-md-1 text-center no-padding"  >
										                            <label style="margin-bottom:0px; ">Total Qty</label>
										                        </div>
										                        <div class="col-md-1 text-center"  >
										                            <label style="margin-bottom:0px; ">Rate</label>
										                        </div>
										                        <div class="col-md-1 text-center"  >
										                            <label style="margin-bottom:0px; ">Quantity</label>
										                        </div>
										                        <div class="col-md-2 text-right" >
									                            <label style="margin-bottom:0px;" title="">Amount</label>
								                            </div>

										                    </div>
										                	</div>

											                <div class="row "  style="">
                                        <form id="place_order_form_<?php echo $proj_ven_res_id; ?>" method="POST">
													                	<?php 
													                		if($activity_resources = $resource['acty_resources']) { 
													                			foreach ($activity_resources as $key => $activity_res) {
													                				$dueResourceRow = ($activity_res['due_notify_flag']) ? 'resourceRowDue' : '';
													                				$ven_res_act_id = $subContProj['project_id'].'_'.$ven_res_id.'_'.$activity_res['activity_id'];
													                	?>
											                			<div class="col-md-12 resourceRow <?php echo $dueResourceRow ?>" style="">


					  							                      <div class="col-md-4 text-left no-padding" >
												                        		<div class="row" >
																	                    <div class="col-md-12 no-padding" >
																	                        <div class="col-md-1 no-padding text-center">
																	                        	<input type="checkbox" class="actvtyResSelect actvtyResSelect<?php echo $proj_ven_res_id ?>" name="activity[]" value="<?php echo $activity_res['activity_id'] ?>" data-venresactid="<?php echo $ven_res_act_id ?>"  data-venresid="<?php echo $proj_ven_res_id ?>"> 
																	                        </div>
																	                        <div class="col-md-11">
																	                            <?php echo $activity_res['activity_name'] ?>
																	                        </div>
													                            </div>
													                          </div>
												                        </div>

												                        <div class="col-md-2 text-center" >
												                            <?php echo $activity_res['due_date'] ?>
												                        </div>
												                        <div class="col-md-1 text-center">
													                        <?php echo $resource['resource_unit'] ?>
												                        </div>
												                        <div class="col-md-1 text-center">
												                            <?php echo $activity_res['tot_quantity'] ?>
												                        </div>
												                        <div class="col-md-1 text-center" style="padding-right: 0px;">
												                            <input class="form-control resourceRate" name="rate[<?php echo $activity_res['activity_id'] ?>]" id="resourceRate<?php echo $ven_res_act_id ?>" type="number" value="<?php echo $activity_res['rate'] ?>" data-venresid="<?php echo $proj_ven_res_id ?>" data-venresactid="<?php echo $ven_res_act_id ?>" style="padding: 6px 7px; display:inline;">
												                        </div>
												                        <div class="col-md-1 text-center " style="padding-right: 0px;">
												                            <input class="form-control resourceQty" name="quantity[<?php echo $activity_res['activity_id'] ?>]" id="resourceQty<?php echo $ven_res_act_id ?>" type="number" value="<?php echo $activity_res['quantity'] ?>" data-venresid="<?php echo $proj_ven_res_id ?>" data-venresactid="<?php echo $ven_res_act_id ?>" style="padding: 6px 7px; display:inline; ">
												                        </div>									                        

												                        <div class="col-md-2 text-right"  id="resourceAmt<?php echo $ven_res_act_id ?>">
											                            <?php echo $activity_res['amount'] ?>
										                            </div>

												                    </div>
																						<input type="hidden" name="ordertype" value="2">
										                    		<input type="hidden" name="resource_id" value="<?php echo $resource['resource_id'] ?> ">
        						                    		<input type="hidden" name="project_id" value="<?php echo $subContProj['project_id'] ?> ">

											                			<?php } } ?>	
											                	</form>
									                    </div>

								                    	<div class="row po-resource-list-footer" style="">
																				<div class="col-md-12 po-resource-total">
																					<div  class="col-md-2 text-left no-padding">
																						<button type="button" class="btn btn-primary place_order_btn" id="place_order_btn_<?php echo $proj_ven_res_id; ?>" 
																							href="#placeOrderPopup" 
																							data-target="#placeOrderPopup" 
																							data-toggle="modal" 
																							data-vendorid="<?php echo $vendor_id; ?>"
																							data-resourceid="<?php echo $resource_id; ?>" 
                                              data-proj_ven_res_id="<?php echo $proj_ven_res_id; ?>"  
																							style="padding:3px 12px; font-size:13px;" 
																							value="" 
																							disabled
																							title="Select atleast one Resource" >
																								Place Order
																						</button>
																					</div>

																					<div class="col-md-7 text-right" style="">
																						Total
																					</div>
																					<div class="col-md-1 text-center" id="qtyTotal<?php echo $proj_ven_res_id ?>"  >
																						00
																					</div>
																					<div class="col-md-2 text-right" id="amtTotal<?php echo $proj_ven_res_id ?>" >00.00</div>
																				</div>
								                    	</div>
								                  </div>
							                  </div>
					                    	<?php } ?>



					                    </div>


													</div>

                    	</div>

                    	<?php
                    	} }
                    	else{
                    	?>
                    	<div style="text-align:center; padding: 50px;">No Resources Found!</div>
                    	<?php
                    	}
                    	?>



                    </div>
                    <div class="col-md-1">&nbsp;</div>
								</div>
							</div>
    					<!----- END ---------------->

    					<!------DIRECT LABOUR ---------------->
							<div class="order_management_container" id="orderDirectLabourContainer" style="display:none;">
								<div class="row">
                    <div class="col-md-1">&nbsp;</div>
                    <div class="col-md-10" >

                    	<?php
                    	if($labourArr){
                    	?>
                    	<!-- 
                    	<div style="margin-bottom: 30px !important;background: #eee;padding: 10px;border: 1px solid #ccc;">
	                    	<div class="row "  >
					                    <div class="col-md-12 text-center" style="padding-bottom: 10px; font-weight: bold;">
					                    		Search & Filter
					                    </div>
		                    </div>
	                    	<div class="row " style="display: flex;align-items: center;" >
					                    <div class="col-md-4 text-left" style="">
					                    		<input type="text" class="form-control" placeholder="Enter SubContractor Name" name="">
					                    </div>
					                    <div class="col-md-3 text-left" >
					                    	<div><?php echo $resourceTypeSelect ?></div>
					                    </div>
					                    <div class="col-md-3 text-center" >
					                    	<div ><?php echo $resourceGroupSelect ?></div>
					                    </div>
					                    <div class="col-md-2 text-right">
					                    	<label><input type="checkbox"> Due Orders Only</label>
					                    </div>
	                  		</div>
                  		</div>
                  		 -->


                    	<?php
                    		foreach ($labourArr as $proj_id => $labourProj) {
                    	?>

                    	<div class="row "  style="margin-bottom: 30px !important;">
                    			<div class="collapsibleSimple">
                    				<div class="collapsibleSimpleTitle row">
						                  	<div class="col-md-8" style="" > 
                                    Project : <b><?php echo $labourProj['project_name'] ?></b>

						                  	</div>
						                  	<div class="col-md-3 text-center <?php //echo $dueDateClass ?> ">
						                    		<?php echo $resource['due_date'] ?> 
						                  	</div>
						                    <div class="col-md-1 no-padding text-center" > 
						                    </div>                    				
						           			</div>
                    			</div>

													<div class="collapsibleContent">
															<div class="collapsibleContentInner">

																<!-- <div class="row resource_top_bar" >
								                    <div class="col-md-2 ">
								                    	Unit :<b> <?php echo $resource['resource_unit'] ?></b>
												    				</div>    
								                    <div class="col-md-6 text-center">
								                    	Resource Type : <b><?php echo $resource['res_type_name'] ?></b>
												    				</div>    
								                    <div class="col-md-1 text-right no-padding">
								                    	Vendor : 
							                    	</div>
								                    <div class="col-md-3">
								                    	<select name="vendor" id="vendor_<?php echo $ven_res_id; ?>" class="form-control">
								                    		<option value="<?php echo $resource['vendor_id'] ?>"><?php echo $resource['vendor'] ?></option>
								                    	</select>
												    				</div>    
															  </div> -->


                                <?php
						                    		foreach ($labourProj['resources'] as $key => $resource) {
						                    			$vendor_id 	 = $resource['vendor_id'];
						                    			$resource_id = $resource['resource_id'];
						                    			$ven_res_id	 = $vendor_id.'_'.$resource_id;

							                				$dueDateClass = ($resource['due_notify_flag']) ? 'text-due' : '';
                                      $proj_ven_res_id        = $labourProj['project_id'].'_'.$ven_res_id;
                                ?>

                                <div class="po-project-container">
					                    		<form id="place_order_form_<?php echo $labourProj['project_id']; ?>" method="POST" action="<?php echo Yii::$app->request->baseUrl.'/procurement/purchaseorder' ?>">
                                    <div class="project-title">
                                    		<div class="row title-row">
                                    				<div class="col-md-4" style="font-weight:600;" > 
												                  		<?php echo $resource['resource_name'] ?> 
												                  	</div>
												                  	<div class="col-md-3 text-center <?php //echo $dueDateClass ?> ">
												                    		<?php echo $resource['due_date'] ?> 
												                  	</div>
												                    <div class="col-md-4 text-left" > 
												                    		<?php echo $resource['vendor'] ?> 
												                    </div>
												                    <div class="col-md-1 no-padding text-center" > 
												                    		<div style="float:left; margin-right: 10px;">
													                        <a href="#dashboardPopup" class="dropdown-toggle resource-icon icon-chart2" data-toggle="modal" data-target="#dashboardPopup"  id="resourceResource" data-type="Resource" data-recourceid="<?php echo $resource['resource_id'] ?>"></a>
							  					                    	</div>
												                    		<div>
												                    			<?php 
												                    			if($resource['due_notify_cnt'] > 0){
												                    			?>
												                        	<span class="due_cnt_notification"><?php echo $resource['due_notify_cnt'] ?></span>
												                        	<?php } ?>
												                      	</div>
												                    </div> 
                                    		</div>
                                    </div>


			                            	<div class="po-resource-list-container">
								                    	<div class="row po-resource-list-header " >
										                    <div class="col-md-12" >
										                        <div class="col-md-5 text-left no-padding" >
										                        		<div class="row" >
															                    <div class="col-md-12 no-padding" >
															                        <div class="col-md-1 no-padding text-center" >
														                        		<input type="checkbox" data-resource_type_id="<?php echo $resource['resource_type_id'] ?>" class="actvtyResSelectAll" data-venresid="<?php echo $proj_ven_res_id ?>"> 
													                            </div>
															                        <div class="col-md-11 " >
														                            <label style="margin-bottom:0px;">Activity</label>
													                            </div>
											                            </div>
											                          </div>
										                        </div>
										                        <div class="col-md-1 text-center no-padding" >
										                            <label style="margin-bottom:0px;  " title="">Due Date</label>
										                        </div>
										                        <div class="col-md-1 text-center"  >
										                            <label style="margin-bottom:0px; ">Rate</label>
										                        </div>
										                        <div class="col-md-1 text-center"  >
										                            <label style="margin-bottom:0px; ">OT Rate</label>
										                        </div>
										                        <div class="col-md-1 text-center no-padding"  >
										                            <label style="margin-bottom:0px; " title="No of Workers">No of Wrkrs</label>
										                        </div>
										                        <div class="col-md-1 text-center"  style="padding: 0;">
									                            <label style="margin-bottom:0px;" title="Working Hours">Wrkng Hrs</label>
								                            </div>
										                        <div class="col-md-2 text-center"  style="padding: 0;">
									                            <label style="margin-bottom:0px;" title="Payment Cycle">Payment Cycle</label>
								                            </div>
										                    </div>
										                	</div>

											                <div class="row "  style="">
													                	<?php 
													                		if($activity_resources = $resource['acty_resources']) { 
													                			foreach ($activity_resources as $key => $activity_res) {
													                				$dueResourceRow = ($activity_res['due_notify_flag']) ? 'resourceRowDue' : '';
													                				$ven_res_act_id = $labourProj['project_id'].'_'.$ven_res_id.'_'.$activity_res['activity_id'];
													                	?>
											                			<div class="col-md-12 resourceRow <?php echo $dueResourceRow ?>" style="">


					  							                      <div class="col-md-5 text-left no-padding" >
												                        		<div class="row" >
																	                    <div class="col-md-12 no-padding" >
																	                        <div class="col-md-1 no-padding text-center">
																	                        	<input type="checkbox" class="actvtyResSelect actvtyResSelect<?php echo $proj_ven_res_id ?>" name="activity[]" value="<?php echo $activity_res['activity_id'] ?>" data-venresactid="<?php echo $ven_res_act_id ?>"  data-venresid="<?php echo $proj_ven_res_id ?>" data-resource_type_id="<?php echo $resource['resource_type_id'] ?>"> 
																	                        </div>
																	                        <div class="col-md-11">
																	                            <?php echo $activity_res['activity_name'] ?>
																	                        </div>
													                            </div>
													                          </div>
												                        </div>

												                        <div class="col-md-1  no-padding text-center" >
												                            <?php echo $activity_res['due_date'] ?>
												                        </div>
												                        <div class="col-md-1 text-center"  style="padding: 0px 10px;">
												                            <input class="form-control resourceRate" name="rate[<?php echo $activity_res['activity_id'] ?>]" id="resourceRate<?php echo $ven_res_act_id ?>" type="number" value="<?php echo $activity_res['rate'] ?>" data-venresid="<?php echo $proj_ven_res_id ?>" data-venresactid="<?php echo $ven_res_act_id ?>" style="padding: 6px 7px; display:inline;">
												                        </div>
												                        <div class="col-md-1 text-center " style="padding: 0px 10px;">
												                            <input class="form-control resourceOtRate" name="ot_rate[<?php echo $activity_res['activity_id'] ?>]" id="resourceOtRate<?php echo $ven_res_act_id ?>" type="number" value="<?php echo $activity_res['ot_rate'] ?>" data-venresid="<?php echo $proj_ven_res_id ?>" data-venresactid="<?php echo $ven_res_act_id ?>" style="padding: 6px 7px; display:inline; ">
												                        </div>									                        
												                        <div class="col-md-1 text-center">
												                            <input class="form-control resourceRate" name="no_of_workers[<?php echo $activity_res['activity_id'] ?>]" id="no_of_workers<?php echo $ven_res_act_id ?>" type="number" value="<?php echo $activity_res['labour_cnt'] ?>" data-venresid="<?php echo $proj_ven_res_id ?>" data-venresactid="<?php echo $ven_res_act_id ?>" style="padding: 6px 7px; display:inline;">
												                        </div>

												                        <div class="col-md-1 text-right"   style="padding: 0px 10px;">
											                            <select class="form-control">
											                            	<option>8</option>
											                            	<option>12</option>
											                            	<option>24</option>
											                            </select>
										                            </div>

												                        <div class="col-md-2 text-center"  id="paymentCycle<?php echo $ven_res_act_id ?>"  style="padding: 0px 10px;">
											                            <select name="payment_cycle" class="form-control" style="display: inline; width: 70%;">
											                            	<option value="1">1 Day</option>
											                            	<option value="2">2 Days</option>
											                            	<option value="3">3 Days</option>
											                            	<option value="4">4 Days</option>
											                            	<option value="5">5 Days</option>
											                            	<option value="6">6 Days</option>
											                            	<option value="7">7 Days</option>
											                            	<option value="8">8 Days</option>
											                            	<option value="8">8 Days</option>
											                            	<option value="9">9 Days</option>
											                            	<option value="10">10 Days</option>
											                            	<option value="14">14 Days</option>
											                            	<option value="30">30 Days</option>
											                            	<option value="60">60 Days</option>
											                            </select>
										                            </div>

												                    </div>

										                    		<input type="hidden" name="resource[]" value="<?php echo $resource['resource_id'] ?> ">
																						<input type="hidden" name="ordertype" value="3">
										                    		<input type="hidden" name="resource_id" value="<?php echo $resource['resource_id'] ?> ">
        						                    		<input type="hidden" name="project_id" value="<?php echo $labourProj['project_id'] ?> ">
        						                    		<input type="hidden" name="vendor" value="<?php echo $resource['vendor_id'] ?>">

											                			<?php } } ?>	
									                    </div>

								                    	<div class="row po-resource-list-footer" style="">
																				<div class="col-md-12 po-resource-total">
																					<div  class="col-md-2 text-left no-padding">
																						<button type="submit" class="btn btn-primary dwo_submit_btn" id="dwo_submit_btn<?php echo $proj_ven_res_id; ?>" 
																							data-vendorid="<?php echo $vendor_id; ?>"
																							data-resourceid="<?php echo $resource_id; ?>" 
                                              data-proj_ven_res_id="<?php echo $proj_ven_res_id; ?>"  
																							style="padding:3px 12px; font-size:13px;" 
																							value="submit" 
																							name="submit"
																							title="Select atleast one Resource" >
																								Submit
																						</button>
																					</div>
																					<div class="col-md-9" style=""></div>
																					<!-- <div class="col-md-7 text-right" style="">
																						Total
																					</div>
																					<div class="col-md-1 text-center" id="qtyTotal<?php echo $proj_ven_res_id ?>"  >
																						00
																					</div>
																					<div class="col-md-2 text-right" id="amtTotal<?php echo $proj_ven_res_id ?>" >00.00</div> -->
																				</div>
								                    	</div>
								                  	</div>
								                  </form>
							                  </div>
					                    	<?php } ?>



					                    </div>


													</div>

                    	</div>

                    	<?php
                    	} }
                    	else{
                    	?>
                    	<div style="text-align:center; padding: 50px;">No Resources Found!</div>
                    	<?php
                    	}
                    	?>



                    </div>
                    <div class="col-md-1">&nbsp;</div>
								</div>
							</div>
    					<!----- END ---------------->

    					<!------PLANT & EQUIPMENT ---------------->

    					<div class="order_management_container" id="orderPlantEquipmentContainer" style="display:none;">
								<div class="row">
                    <div class="col-md-1">&nbsp;</div>
                    <div class="col-md-10" >

                    	<?php
                    	if($plantEquipArr){
                    	?>
                    	<!-- <div style="margin-bottom: 30px !important;background: #eee;padding: 10px;border: 1px solid #ccc;">
	                    	<div class="row "  >
					                    <div class="col-md-12 text-center" style="padding-bottom: 10px; font-weight: bold;">
					                    		Search & Filter
					                    </div>
		                    </div>
	                    	<div class="row " style="display: flex;align-items: center;" >
					                    <div class="col-md-4 text-left" style="">
					                    		<input type="text" class="form-control" placeholder="Enter SubContractor Name" name="">
					                    </div>
					                    <div class="col-md-3 text-left" >
					                    	<div><?php echo $resourceTypeSelect ?></div>
					                    </div>
					                    <div class="col-md-3 text-center" >
					                    	<div ><?php echo $resourceGroupSelect ?></div>
					                    </div>
					                    <div class="col-md-2 text-right">
					                    	<label><input type="checkbox"> Due Orders Only</label>
					                    </div>
	                  		</div>
                  		</div> -->


                    	<?php
                    		foreach ($plantEquipArr['items'] as $key => $plantEquip) {
                    			$itemId = $plantEquip['itemId'];
                    	?>

                    	<div class="row "  style="margin-bottom: 30px !important;">
                    			<div class="collapsibleSimple">
                    				<div class="collapsibleSimpleTitle row">
						                  	<div class="col-md-4" style="font-weight:600;" > 
						                  		<?php echo $plantEquip['itemName'] ?> 
					                    		<input type="hidden" name="resource_id" value="<?php echo $resource['resource_id'] ?> ">
						                  	</div>
						                  	<div class="col-md-3 text-center  ">
						                  	</div>
						                    <div class="col-md-4 text-left" > 
						                    		<?php //echo 'Vendor name' ?> 
						                    </div>
						                    <div class="col-md-1 no-padding text-center" > 
						                    		
						                    </div>                    				
						           			</div>
                    			</div>

													<div class="collapsibleContent">
															<div class="collapsibleContentInner">

																<!-- <div class="row resource_top_bar" >
								                    <div class="col-md-2 ">
								                    	Unit :<b> <?php echo $resource['resource_unit'] ?></b>
												    				</div>    
								                    <div class="col-md-6 text-center">
								                    	Resource Type : <b><?php echo $resource['res_type_name'] ?></b>
												    				</div>    
								                    <div class="col-md-1 text-right no-padding">
								                    	Vendor : 
							                    	</div>
								                    <div class="col-md-3">
								                    	<select name="vendor" class="form-control">
								                    		<option value="<?php echo $resource['vendor_id'] ?>"><?php echo $resource['vendor'] ?></option>
								                    	</select>
												    				</div>    
															  </div> -->

															  <?php
                                    foreach ($plantEquip['projects'] as $proj_id => $project) {
                                            //$proj_ven_res_id        = $project['project_id'].'_'.$ven_res_id;

                                ?>


                               <div class="po-project-container">
                                    <div class="project-title">
                                          Project : <b><?php echo $project['project_name'] ?></b>
                                    </div>

                                    <div class="po-resource-list-container">

							                    		<form id="equip_order_form_<?php echo $itemId; ?>" method="POST" action="<?php echo Yii::$app->request->baseUrl.'/procurement/equipmentmovement' ?>">

									                    	<div class="row po-resource-list-header " >
											                    <div class="col-md-12" >
											                        <div class="col-md-4 text-left no-padding"  >
											                            <label style="margin-bottom:0px;">Activity Name</label>
											                        </div>
											                        <div class="col-md-2 text-center"  >
											                            <label style="margin-bottom:0px;">Equipment Name</label>
											                        </div>
											                        <div class="col-md-2 text-center no-padding"  >
											                            <label style="margin-bottom:0px; ">Date</label>
											                        </div>
											                        <div class="col-md-2 text-center " >
											                            <label style="margin-bottom:0px;  " title="">Move From</label>
											                        </div>
											                        <div class="col-md-2 text-center"  >
											                            <label style="margin-bottom:0px; ">Move To</label>
											                        </div>
											                    </div>
											                	</div>

												                <div class="row "  style="">
												                	<?php 
												                		if($equipArr = $project['equipArr']) { 
												                			foreach ($equipArr as $equipmentKey => $equipment) {
												                				$resourceId = $equipment['resourceId'];
												                				$eq_key 	 	= $equipment['resourceId'].'_'.$project['project_id'].'_'.$equipmentKey;
												                	?>
												                			<div class="col-md-12 resourceRow">
						  							                      
						  							                      <div class="col-md-4 text-left no-padding" >
						  							                      	<?php echo $equipment['activity_name'] ?>
						  							                      </div>
						  							                      <div class="col-md-2 text-center no-padding" >
						  							                      		<select id="equipment" name="equipment[]" class="form-control move_equipment" data-eq_key="<?php echo $eq_key ?>">
						                     												<option value="">Select Equipment</option>
						                     												<?php
																													foreach ($plantEquip['equipment_list'] as $equipment_list) {
																														echo '<option data-cur_proj="'.$equipment_list['current_project'].'" value="'.$equipment_list['Resource_Id'].'" >'.$equipment_list['Name'].'</option>';
																													}	        
																													?>             						
																											</select>
						  							                      </div>
													                        <div class="col-md-2 text-center" >
													                            <input class="form-control datepicker" name="equip_date[]" id="equip_date<?php echo $resourceId ?>" type="text" value="<?php echo date('Y-m-d') ?>" >
													                        </div>
													                        <div class="col-md-2 text-center">
													                            <input type="hidden" name="movefrom[]" id="movefrom_<?php echo $eq_key ?>" value="<?php echo $equipment['current_project']; ?>">
													                        		<select class="form-control" disabled id="movefromselect_<?php echo $eq_key ?>" >
						                     												<option value="">Select Project</option>
						                     												<?php
																													foreach ($activeProjects as $activeProject) {
																														$projSel = '';
																														/*if($activeProject->Project_Id == $equipment['current_project'])
																															$projSel = 'selected';*/
																														echo '<option value="'.$activeProject->Project_Id.'" '.$projSel.'>'.$activeProject->Name.'</option>';
																													}	        
																													?>             						
																											<select>
													                        </div>

													                        <div class="col-md-2 text-center">
													                            <input type="hidden" name="moveto[]" value="<?php echo $project['project_id']; ?>">
													                        		<select  class="form-control" disabled>
						                     												<option value="">Select Project</option>
						                     												<?php
																													foreach ($activeProjects as $activeProject) {
																														$projSel = '';
																														//if($project->Project_Id == $selProjId)
																														if($activeProject->Project_Id == $project['project_id'])
																															$projSel = 'selected';
																														echo '<option value="'.$activeProject->Project_Id.'" '.$projSel.'>'.$activeProject->Name.'</option>';
																													}	        
																													?>             						
																											</select>
													                            <input type="hidden" name="pricing_resourceid[]"  value="<?php echo $equipment['pricing_resourceid']; ?>">
													                        </div>
													                    </div>
												                	<?php } } ?>	

										                    </div>

									                    	<div class="row po-resource-list-footer" style="">
																					<div class="col-md-12 po-resource-total text-center">
																							<button type="submit" class="btn btn-primary equipment_submit_btn" id="equipment_submit_btn<?php echo $itemId; ?>" data-resourceid="<?php echo $resource_id; ?>"  
																								style="padding:3px 12px; font-size:13px;" 
																								value="" >
																									Submit
																							</button>
																							<input type="hidden" name="ordertype" value="2">

																					</div>
									                    	</div>

									                    </form>
					                    			</div>
					                    	</div>

	                              <?php } ?>



					                    </div>


													</div>

                    	</div>

                    	<?php
                    	} }
                    	else{
                    	?>
                    	<div style="text-align:center; padding: 50px;">No Resources Found!</div>
                    	<?php
                    	}
                    	?>



                    </div>
                    <div class="col-md-1">&nbsp;</div>
								</div>
							</div>
    					<!----- END ---------------->


    					

							</div>


					</div>

		  </div>


    </div>

<style>
    .tab-content{
        max-height:unset;
    }
    .invoice_management_container{
      display: none;
    }
    .order_management_nav_bar .nav-tabs > li {
        float:none;
        display:inline-block;
        zoom:1;
    }

    .order_management_nav_bar .nav-tabs {
        text-align:center;
    }

</style>


<script>

	var coll = document.getElementsByClassName("collapsibleSimple");
	var i;

	for (i = 0; i < coll.length; i++) {
	  coll[i].addEventListener("click", function() {
	    this.classList.toggle("collapsibleActive");
	    var content = this.nextElementSibling;
	    if (content.style.maxHeight){
	      content.style.maxHeight = null;
	    } else {
	      content.style.maxHeight = content.scrollHeight + "px";
	    } 
	  });
	}

</script>
