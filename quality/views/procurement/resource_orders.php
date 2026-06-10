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
														<span class="icon-th-list"></span> Muster Rolls
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
												<!-- <li class="">
													<a data-toggle="pill" class="orderManagementTab" data-type="Invoice" href="#Invoice">
														<span class="icon-file-text2"></span> Invoices
														<?php if($invoice_due_cnt) { ?>
														<span class="due_cnt_type_notification"><?php echo $invoice_due_cnt ?></span>
														<?php } ?>
													</a>
												</li> -->
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
                    	<div style="margin-bottom: 30px !important;background: #eee;padding: 10px;border: 1px solid #ccc;">
	                    	<div class="row "  >
					                    <div class="col-md-12 text-center" style="padding-bottom: 10px; font-weight: bold;">
					                    		Search & Filter
					                    </div>
		                    </div>
	                    	<div class="row " style="display: flex;align-items: center;" >
					                    <div class="col-md-4 text-left" style="">
					                    		<input type="text" class="form-control" placeholder="Enter Resource Name" name="">
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
			                    		<input type="hidden" id="selectedResource" value="">
	                  		</div>
                  		</div>


                    	<?php
                    		foreach ($resourcesArr as $key => $resource) {
                    			$vendor_id 	 = $resource['vendor_id'];
                    			$resource_id = $resource['resource_id'];
                    			$ven_res_id	 = $vendor_id.'_'.$resource_id;

	                				$dueDateClass = ($resource['acty_resources'][0]['due_notify_flag']) ? 'text-due' : '';
                    	?>

                    	<div class="row "  style="margin-bottom: 30px !important;">
                    		<form id="place_order_form_<?php echo $ven_res_id; ?>" method="POST">
                    			<div class="collapsibleSimple">
                    				<div class="collapsibleSimpleTitle row">
						                  	<div class="col-md-4" style="font-weight:600;" > 
						                  		<?php echo $resource['resource_name'] ?> 
					                    		<input type="hidden" name="resource_id" value="<?php echo $resource['resource_id'] ?> ">
						                  	</div>
						                  	<div class="col-md-3 text-center <?php echo $dueDateClass ?> ">
						                    		<?php echo $resource['acty_resources'][0]['due_date'] ?> 
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
								                    	<select name="vendor" class="form-control">
								                    		<option value="<?php echo $resource['vendor_id'] ?>"><?php echo $resource['vendor'] ?></option>
								                    	</select>
												    				</div>    
															  </div>

					                    	<div class="row po-resource-list-header " >
							                    <div class="col-md-12" >
							                        <div class="col-md-5 text-left no-padding" >
							                        		<div class="row" >
												                    <div class="col-md-12 no-padding" >
												                        <div class="col-md-1 no-padding text-center" >
											                        		<input type="checkbox" class="actvtyResSelectAll" data-venresid="<?php echo $ven_res_id ?>"> 
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
								                	<?php 
								                		if($activity_resources = $resource['acty_resources']) { 
								                			foreach ($activity_resources as $key => $activity_res) {
								                				$dueResourceRow = ($activity_res['due_notify_flag']) ? 'resourceRowDue' : '';
								                				$ven_res_act_id = $ven_res_id.'_'.$activity_res['activity_id'];
								                	?>
								                			<div class="col-md-12 resourceRow <?php echo $dueResourceRow ?>" style="">


		  							                      <div class="col-md-5 text-left no-padding" >
									                        		<div class="row" >
														                    <div class="col-md-12 no-padding" >
														                        <div class="col-md-1 no-padding text-center">
														                        	<input type="checkbox" class="actvtyResSelect actvtyResSelect<?php echo $ven_res_id ?>" name="activity[]" value="<?php echo $activity_res['activity_id'] ?>" data-venresactid="<?php echo $ven_res_act_id ?>"  data-venresid="<?php echo $ven_res_id ?>"> 
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
									                            <input class="form-control resourceRate" name="rate[<?php echo $activity_res['activity_id'] ?>]" id="resourceRate<?php echo $ven_res_act_id ?>" type="number" value="<?php echo $activity_res['rate'] ?>" data-venresid="<?php echo $ven_res_id ?>" data-venresactid="<?php echo $ven_res_act_id ?>" style="padding: 6px 7px; display:inline;">
									                        </div>
									                        <div class="col-md-1 text-center "  style="padding-right: 0px;">
									                            <input class="form-control resourceQty" name="quantity[<?php echo $activity_res['activity_id'] ?>]" id="resourceQty<?php echo $ven_res_act_id ?>" type="number" value="<?php echo $activity_res['quantity'] ?>" data-venresid="<?php echo $ven_res_id ?>" data-venresactid="<?php echo $ven_res_act_id ?>" style="padding: 6px 7px; display:inline; ">
									                        </div>									                        

									                        <div class="col-md-2 text-right"  id="resourceAmt<?php echo $ven_res_act_id ?>">
								                            <?php echo $activity_res['amount'] ?>
							                            </div>
									                    </div>
								                	<?php } } ?>	

						                    </div>

					                    	<div class="row po-resource-list-footer" style="">
																	<div class="col-md-12 po-resource-total">
																		<div  class="col-md-2 text-left no-padding">
																			<button type="button" class="btn btn-primary place_order_btn" id="place_order_btn_<?php echo $ven_res_id; ?>" 
																				href="#placeOrderPopup" 
																				data-target="#placeOrderPopup" 
																				data-toggle="modal" 
																				data-vendorid="<?php echo $vendor_id; ?>"
																				data-resourceid="<?php echo $resource_id; ?>"  
																				style="padding:3px 12px; font-size:13px;" 
																				value="" 
																				disabled
																				title="Select atleast one Resource" >
																					Place Order
																			</button>
																			<input type="hidden" name="ordertype" value="1">
																		</div>

																		<div class="col-md-7 text-right" style="">
																			Total
																		</div>
																		<div class="col-md-1 text-center" id="qtyTotal<?php echo $ven_res_id ?>"  >
																			00
																		</div>
																		<div class="col-md-2 text-right" id="amtTotal<?php echo $ven_res_id ?>" >00.00</div>
																	</div>
					                    	</div>

					                    </div>

													</div>
												</form>

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


    					<!------SUB CONTRACTOR ---------------->
							<div class="order_management_container" id="orderSubContractorContainer"  style="display:none;">
								<div class="row">
                    <div class="col-md-1">&nbsp;</div>
                    <div class="col-md-10" >

                    	<?php
                    	if($subContArr){
                    	?>
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


                    	<?php
                    		foreach ($subContArr as $key => $resource) {
                    			$vendor_id 	 = $resource['vendor_id'];
                    			$resource_id = $resource['resource_id'];
                    			$ven_res_id	 = $vendor_id.'_'.$resource_id;

	                				$dueDateClass = ($resource['acty_resources'][0]['due_notify_flag']) ? 'text-due' : '';
                    	?>

                    	<div class="row "  style="margin-bottom: 30px !important;">
                    		<form id="place_order_form_<?php echo $ven_res_id; ?>" method="POST">
                    			<div class="collapsibleSimple">
                    				<div class="collapsibleSimpleTitle row">
						                  	<div class="col-md-4" style="font-weight:600;" > 
						                  		<?php echo $resource['resource_name'] ?> 
					                    		<input type="hidden" name="resource_id" value="<?php echo $resource['resource_id'] ?> ">
						                  	</div>
						                  	<div class="col-md-3 text-center <?php echo $dueDateClass ?> ">
						                    		<?php echo $resource['acty_resources'][0]['due_date'] ?> 
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
								                    	<select name="vendor" class="form-control">
								                    		<option value="<?php echo $resource['vendor_id'] ?>"><?php echo $resource['vendor'] ?></option>
								                    	</select>
												    				</div>    
															  </div>



					                    	<div class="row po-resource-list-header " >
							                    <div class="col-md-12" >
							                        <div class="col-md-5 text-left no-padding" >
							                        		<div class="row" >
												                    <div class="col-md-12 no-padding" >
												                        <div class="col-md-1 no-padding text-center" >
											                        		<input type="checkbox" class="actvtyResSelectAll" data-venresid="<?php echo $ven_res_id ?>"> 
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
								                	<?php 
								                		if($activity_resources = $resource['acty_resources']) { 
								                			foreach ($activity_resources as $key => $activity_res) {
								                				$dueResourceRow = ($activity_res['due_notify_flag']) ? 'resourceRowDue' : '';
								                				$ven_res_act_id = $ven_res_id.'_'.$activity_res['activity_id'];
								                	?>
								                			<div class="col-md-12 resourceRow <?php echo $dueResourceRow ?>" style="">


		  							                      <div class="col-md-5 text-left no-padding" >
									                        		<div class="row" >
														                    <div class="col-md-12 no-padding" >
														                        <div class="col-md-1 no-padding text-center">
														                        	<input type="checkbox" class="actvtyResSelect actvtyResSelect<?php echo $ven_res_id ?>" name="activity[]" value="<?php echo $activity_res['activity_id'] ?>" data-venresactid="<?php echo $ven_res_act_id ?>"  data-venresid="<?php echo $ven_res_id ?>"> 
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
									                            <input class="form-control resourceRate" name="rate[<?php echo $activity_res['activity_id'] ?>]" id="resourceRate<?php echo $ven_res_act_id ?>" type="number" value="<?php echo $activity_res['rate'] ?>" data-venresid="<?php echo $ven_res_id ?>" data-venresactid="<?php echo $ven_res_act_id ?>" style="padding: 6px 7px; display:inline;">
									                        </div>
									                        <div class="col-md-1 text-center " style="padding-right: 0px;">
									                            <input class="form-control resourceQty" name="quantity[<?php echo $activity_res['activity_id'] ?>]" id="resourceQty<?php echo $ven_res_act_id ?>" type="number" value="<?php echo $activity_res['quantity'] ?>" data-venresid="<?php echo $ven_res_id ?>" data-venresactid="<?php echo $ven_res_act_id ?>" style="padding: 6px 7px; display:inline; ">
									                        </div>									                        

									                        <div class="col-md-2 text-right"  id="resourceAmt<?php echo $ven_res_act_id ?>">
								                            <?php echo $activity_res['amount'] ?>
							                            </div>

									                    </div>
								                	<?php } } ?>	

						                    </div>

					                    	<div class="row po-resource-list-footer" style="">
																	<div class="col-md-12 po-resource-total">
																		<div  class="col-md-2 text-left no-padding">
																			<button type="button" class="btn btn-primary place_order_btn" id="place_order_btn_<?php echo $ven_res_id; ?>" 
																				href="#placeOrderPopup" 
																				data-target="#placeOrderPopup" 
																				data-toggle="modal" 
																				data-vendorid="<?php echo $vendor_id; ?>"
																				data-resourceid="<?php echo $resource_id; ?>"  
																				style="padding:3px 12px; font-size:13px;" 
																				value="" 
																				disabled
																				title="Select atleast one Resource" >
																					Place Order
																			</button>
																			<input type="hidden" name="ordertype" value="2">
																		</div>

																		<div class="col-md-7 text-right" style="">
																			Total
																		</div>
																		<div class="col-md-1 text-center" id="qtyTotal<?php echo $ven_res_id ?>"  >
																			00
																		</div>
																		<div class="col-md-2 text-right" id="amtTotal<?php echo $ven_res_id ?>" >00.00</div>
																	</div>
					                    	</div>

					                    </div>


													</div>
												</form>

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


                    	<?php
                    		foreach ($labourArr as $key => $resource) {
                    			$vendor_id 	 = $resource['vendor_id'];
                    			$resource_id = $resource['resource_id'];
                    			$ven_res_id	 = $vendor_id.'_'.$resource_id;

	                				$dueDateClass = ($resource['acty_resources'][0]['due_notify_flag']) ? 'text-due' : '';
                    	?>

                    	<div class="row "  style="margin-bottom: 30px !important;">
                    		<form id="generate_muster_form_<?php echo $ven_res_id; ?>" method="POST">
                    			<div class="collapsibleSimple">
                    				<div class="collapsibleSimpleTitle row">
						                  	<div class="col-md-4" style="font-weight:600;" > 
						                  		<?php echo $resource['resource_name'] ?> 
					                    		<input type="hidden" name="resource_id" value="<?php echo $resource['resource_id'] ?> ">
						                  	</div>
						                  	<div class="col-md-3 text-center <?php echo $dueDateClass ?> ">
						                    		<?php echo $resource['acty_resources'][0]['due_date'] ?> 
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
								                    	<select name="vendor" class="form-control">
								                    		<option value="<?php echo $resource['vendor_id'] ?>"><?php echo $resource['vendor'] ?></option>
								                    	</select>
												    				</div>    
															  </div>



					                    	<div class="row po-resource-list-header " >
							                    <div class="col-md-12" >
							                        <div class="col-md-6 text-left no-padding" >
							                        		<div class="row" >
												                    <div class="col-md-12 no-padding" >
												                        <div class="col-md-1 no-padding text-center" >
											                        		<input type="checkbox" class="actvtyResSelectAll" data-venresid="<?php echo $ven_res_id ?>" data-resource_type_id="<?php echo $resource['resource_type_id'] ?>"> 
										                            </div>
												                        <div class="col-md-11 " >
											                            <label style="margin-bottom:0px;">Activity</label>
										                            </div>
								                            </div>
								                          </div>
							                        </div>
							                        <div class="col-md-4 text-center " >
							                            <label style="margin-bottom:0px;  " title="">Due Date</label>
							                        </div>
							                        <div class="col-md-2 text-center no-padding"  >
							                            <label style="margin-bottom:0px; ">Total Workers</label>
							                        </div>


							                    </div>
							                	</div>

								                <div class="row "  style="">
								                	<?php 
								                		if($activity_resources = $resource['acty_resources']) { 
								                			foreach ($activity_resources as $key => $activity_res) {
								                				$dueResourceRow = ($activity_res['due_notify_flag']) ? 'resourceRowDue' : '';
								                				$ven_res_act_id = $ven_res_id.'_'.$activity_res['activity_id'];
								                	?>
								                			<div class="col-md-12 resourceRow <?php echo $dueResourceRow ?>" style="">


		  							                      <div class="col-md-6 text-left no-padding" >
									                        		<div class="row" >
														                    <div class="col-md-12 no-padding" >
														                        <div class="col-md-1 no-padding text-center">
														                        	<input type="checkbox" class="actvtyResSelect actvtyResSelect<?php echo $ven_res_id ?>" name="muster_ids[]" value="<?php echo $activity_res['musterIds'] ?>" data-venresactid="<?php echo $ven_res_act_id ?>"  data-venresid="<?php echo $ven_res_id ?>"  data-resource_type_id="<?php echo $resource['resource_type_id'] ?>" > 
														                        </div>
														                        <div class="col-md-11">
														                            <?php echo $activity_res['activity_name'] ?>
														                        </div>
										                            </div>
										                          </div>
									                        </div>

									                        <div class="col-md-4 text-center" >
									                            <?php echo $activity_res['due_date'] ?>
									                        </div>
									                        <div class="col-md-2 text-center">
									                            <?php echo $activity_res['tot_quantity'] ?>
									                        </div>
									                    </div>
								                	<?php } } ?>	

						                    </div>

					                    	<div class="row po-resource-list-footer" style="">
																	<div class="col-md-12 po-resource-total">
																		<div  class="col-md-2 text-left no-padding">
																			<button type="button" class="btn btn-primary generate_muster_btn" id="place_order_btn_<?php echo $ven_res_id; ?>" 
																				href="#generateMusterPopup" 
																				data-target="#generateMusterPopup" 
																				data-toggle="modal" 
																				data-vendorid="<?php echo $vendor_id; ?>"
																				data-resourceid="<?php echo $resource_id; ?>"  
																				style="padding:3px 12px; font-size:13px;" 
																				value="" 
																				disabled
																				title="Select atleast one Resource" >
																					 Generate Muster Roll
																			</button>
																		</div>

																		<div class="col-md-10 text-right" style=""></div>
																	</div>
					                    	</div>

					                    </div>


													</div>
												</form>

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


                    	<?php
                    		foreach ($plantEquipArr['items'] as $key => $plantEquip) {
                    			$itemId = $plantEquip['itemId'];
                    	?>

                    	<div class="row "  style="margin-bottom: 30px !important;">
                    		<form id="equip_order_form_<?php echo $itemId; ?>" method="POST" action="<?php echo Yii::$app->request->baseUrl.'/procurement/equipmentmovement' ?>">
                    			<div class="collapsibleSimple">
                    				<div class="collapsibleSimpleTitle row">
						                  	<div class="col-md-4" style="font-weight:600;" > 
						                  		<?php echo $plantEquip['itemName'] ?> 
					                    		<input type="hidden" name="resource_id" value="<?php echo $resource['resource_id'] ?> ">
						                  	</div>
						                  	<div class="col-md-3 text-center  ">
						                  	</div>
						                    <div class="col-md-4 text-left" > 
						                    		<?php echo 'Vendor name' ?> 
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

 

					                    	<div class="row po-resource-list-header " >
							                    <div class="col-md-12" >
							                        <div class="col-md-4 text-left no-padding" >
							                        		<div class="row" >
												                    <div class="col-md-12 no-padding" >
												                        <!-- <div class="col-md-1 no-padding text-center" >
											                        		<input type="checkbox" class="actvtyResSelectAll" data-venresid="<?php echo $itemId ?>"> 
										                            </div> -->
												                        <div class="col-md-12 " >
											                            <label style="margin-bottom:0px;">Equipment Name</label>
										                            </div>
								                            </div>
								                          </div>
							                        </div>
							                        <div class="col-md-2 text-center no-padding"  >
							                            <label style="margin-bottom:0px; ">Date</label>
							                        </div>
							                        <div class="col-md-3 text-center " >
							                            <label style="margin-bottom:0px;  " title="">Move From</label>
							                        </div>
							                        <div class="col-md-3 text-center"  >
							                            <label style="margin-bottom:0px; ">Move To</label>
							                        </div>
							                    </div>
							                	</div>

								                <div class="row "  style="">
								                	<?php 
								                		if($equipArr = $plantEquip['equipArr']) { 
								                			foreach ($equipArr as  $equipment) {
								                				$resourceId =  $equipment['resourceId'];
								                	?>
								                			<div class="col-md-12 resourceRow">


		  							                      <div class="col-md-4 text-left no-padding" >
									                        		<div class="row" >
														                    <div class="col-md-12 no-padding" >
														                        <!-- <div class="col-md-1 no-padding text-center">
														                        	<input type="checkbox" class="actvtyResSelect actvtyResSelect<?php echo $itemId ?>" name="activity[]" value="<?php echo $equipment['resourceId'] ?>" data-venresactid="<?php echo $ven_res_act_id ?>"  data-venresid="<?php echo $itemId ?>"> 
														                        </div> -->
														                        <div class="col-md-12">


														                        		<select id="equipment" name="equipment[]" class="form-control">
							                     												<option value="">Select Equipment</option>
							                     												<?php
																														foreach ($plantEquip['equipment_list'] as $equipment_list) {
																															$equSel = '';
																															/*if($project->Project_Id == $equipment['current_project'])
																																$projSel = 'selected';*/
																															echo '<option value="'.$equipment_list->Resource_Id.'" '.$equSel.'>'.$equipment_list->Name.'</option>';
																														}	        
																														?>             						
																												<select>




														                        </div>
										                            </div>
										                          </div>
									                        </div>

									                        <div class="col-md-2 text-center" >
									                            <input class="form-control datepicker" name="equip_date[]" id="equip_date<?php echo $resourceId ?>" type="text" value="<?php echo date('Y-m-d') ?>" >
									                        </div>
									                        <div class="col-md-3 text-center">
									                            <input type="hidden" name="movefrom[]" value="<?php echo $equipment['current_project']; ?>">
									                        		<select class="form-control" disabled>
		                     												<option value="">Select Project</option>
		                     												<?php
																									foreach ($plantEquipArr['projects'] as $project) {
																										$projSel = '';
																										if($project->Project_Id == $equipment['current_project'])
																											$projSel = 'selected';
																										echo '<option value="'.$project->Project_Id.'" '.$projSel.'>'.$project->Name.'</option>';
																									}	        
																									?>             						
																							<select>
									                        </div>

									                        <div class="col-md-3 text-center">
									                            <input type="hidden" name="moveto[]" value="<?php echo $selProjId; ?>">
									                        		<select  class="form-control" disabled>
		                     												<option value="">Select Project</option>
		                     												<?php
																									foreach ($plantEquipArr['projects'] as $project) {
																										$projSel = '';
																										if($project->Project_Id == $selProjId)
																											$projSel = 'selected';
																										echo '<option value="'.$project->Project_Id.'" '.$projSel.'>'.$project->Name.'</option>';
																									}	        
																									?>             						
																							<select>

									                        </div>


									                    </div>
								                	<?php } } ?>	

						                    </div>

					                    	<div class="row po-resource-list-footer" style="">
																	<div class="col-md-12 po-resource-total text-center">
																			<button type="submit" class="btn btn-primary equipment_submit_btn" id="equipment_submit_btn<?php echo $itemId; ?>" 
																				data-resourceid="<?php echo $resource_id; ?>"  
																				style="padding:3px 12px; font-size:13px;" 
																				value="" 
																				title="Select atleast one Resource" >
																					Submit
																			</button>
																			<input type="hidden" name="ordertype" value="2">

																	</div>
					                    	</div>

					                    </div>


													</div>
												</form>

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


