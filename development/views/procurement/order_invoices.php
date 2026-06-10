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

<div class="panel panel-default  acco-three tab">

	<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/procurement/allorders.js" type="text/javascript"></script>

	<div class="panel-heading">
      <h4 class="panel-title " id="chooseallinvoices">
        <a data-toggle="collapse" data-parent="#accordionindex" href="#collapseallinvoices">
        <span class="icon-file-text2"></span>Invoices</a>
      </h4>
    </div>

    <div id="collapseallinvoices" class="tab-content panel-collapse cOrder-body panel-collapse collapse">

    	<div style=" padding: 20px; padding-top: 30px;">

    			<div class="row">
              <div class="col-md-1">&nbsp;</div>
              <div class="col-md-10 text-center">
	              	<div class="order_management_nav_bar">
				    					<ul class="nav nav-tabs text-center topsbars" style="padding-left:0;">
												<li class="frstcl active">
													<a data-toggle="pill" class="invoiceManagementTab" data-type="Material"  href="#Material">
														<span class="icon-shopping_cart"></span> Purchase Orders
													</a>
												</li> 
												<li class="">
													<a data-toggle="pill" class="invoiceManagementTab" data-type="SubContractor" href="#SubContractor">
														<span class="icon-tools"></span> Work Orders
													</a>
												</li>
												<li class="">
													<a data-toggle="pill" class="invoiceManagementTab" data-type="DirectLabour" href="#DirectLabour">
														<span class="icon-th-list"></span> Muster Rolls
													</a>
												</li>
												<li class="">
													<a data-toggle="pill" class="invoiceManagementTab" data-type="PlantEquipment" href="#PlantEquipment">
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
					   	<div class="invoice_management_container" id="invoiceMaterialContainer" style="display:block;">

								<div class="row">
                    <div class="col-md-1">&nbsp;</div>
                    <div class="col-md-10" >

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


                  		<div class="invoiceContent">
		                  				<?php if($purchaseOrders) { ?>

					                    	<div class="row po-resource-list-header ">
							                    <div class="col-md-12">
							                        <div class="col-md-1 text-left " style="padding-left: 0;">
							                            <label style="margin-bottom:0px;  " title="">Due Date</label>
							                        </div>
							                        <div class="col-md-1 text-left " style="padding-left: 0;">
							                            <label style="margin-bottom:0px;  " title="">Order Date</label>
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
							                        <div class="col-md-3 text-center">
							                            <label style="margin-bottom:0px;">Actions</label>
							                        </div>
							                    </div>
							                	</div>

								                <div class="row " style="">

								                			<?php
								                			foreach ($purchaseOrders as $purchaseOrder) {
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
									                        <div class="col-md-1 text-left" style="padding-left: 0;">
									                            <?php echo $purchaseOrder->orderdate ?>									                        
									                         </div>
									                        <div class="col-md-1 text-left" style="padding-left: 0;">
									                            <?php echo $purchaseOrder->orderdate ?>									                        
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
									                        <div class="col-md-3 text-right">
									                        		<div class="row  text-center" style="">
										                        		<div class="col-md-8 text-center " style="">

																									<button type="button" class="btn btn-primary receive_order_btn" id="receive_order_btn_<?php echo $purchaseOrder->order_id ?>" href="#receiveOrderPopup" data-target="#receiveOrderPopup" data-toggle="modal"  data-orderid="<?php echo $purchaseOrder->order_id ?>" style="padding:3px 12px; font-size:13px;">
																											Receive Order
																									</button>

																								</div>
										                        		<div class="col-md-4  text-center" style="">
											                            <a target="_blank" href="printorderresource?id=<?php echo $purchaseOrder->order_id ?>" data-url="printorderresource?id=<?php echo $purchaseOrder->order_id ?>" class="btn btn-primary btn-sm icon-eye" title="View Order"></a>	
											                          </div>
																							</div>

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
                    <div class="col-md-1">&nbsp;</div>
								</div>

							</div>
    					<!----- END ---------------->


    					<!------SUB CONTRACTOR ---------------->
					   	<div class="invoice_management_container" id="invoiceSubContractorContainer" >

								<div class="row">
                    <div class="col-md-1">&nbsp;</div>
                    <div class="col-md-10" >

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


                  		<div class="invoiceContent">
		                  				<?php if($woInvoiceArr) { ?>

					                    	<div class="row po-resource-list-header ">
							                    <div class="col-md-12">
							                        <div class="col-md-3  text-left" >
							                            <label style="margin-bottom:0px;" title="">Activity</label>
							                        </div>
							                        <div class="col-md-3 text-center">
							                            <label style="margin-bottom:0px;">Sub Contractor</label>
							                        </div>
							                        <div class="col-md-2  text-center no-padding">
							                            <label style="margin-bottom:0px;" title="Reported Days">Reprtd Qty</label>
							                        </div>
							                        <div class="col-md-2 text-center " >
							                            <label style="margin-bottom:0px;" title="Last Reported Date">Last Reprtd Date</label>
							                        </div>
							                        <div class="col-md-2 text-center">
							                            <label style="margin-bottom:0px;">Actions</label>
							                        </div>
							                    </div>
							                	</div>

								                <div class="row " style="">

								                			<?php

								                			foreach ($woInvoiceArr as $woInvoice) {

																				$amount =  $woInvoice['amount'];
																				if($woInvoice['cgst']!=0 & $woInvoice['sgst']!=0):
																					$gst=$woInvoice['cgst'] + $woInvoice['sgst'];
																				elseif($woInvoice['igst']!=0):
																					$gst=$woInvoice['igst'];
																				else:
																					$gst=0;
																				endif;

																        $gstamount=($amount * $gst) / 100;
																				$amntincgst=$amount + $gstamount;
								                			
								                			?>
								                			<div class="col-md-12 resourceRow " style="">

									                        <div class="col-md-3 text-left">
									                            <?php echo $woInvoice['activity_name'] ?>
									                        </div>
									                        <div class="col-md-3 text-center">
									                            <?php echo $woInvoice['resource_name'] ?>
									                            (<?php echo $woInvoice['vendor_name'] ?>)			                        
									                        </div>
									                        <div class="col-md-2 text-center">
									                            <?php echo $woInvoice['cumulated_qty'] ?>					                        
									                            /
									                            <?php echo $woInvoice['qnty'] ?>					                        
									                        </div>
									                        <div class="col-md-2 text-center" style="padding-left: 0;">
									                            <?php echo $woInvoice['updated_at'] ?>									                        
									                        </div>
									                        <div class="col-md-2 text-right no-padding">
									                        		<div class="row  text-center" style="display: flex; align-items: center;">
										                        		<div class="col-md-8 text-center " style="">
																									<?php if($woInvoice['bill_qty'] > 0) { ?>
																									<button type="button" class="btn btn-primary raise_bill_btn" id="raise_bill_btn_<?php echo $woInvoice['order_id'] ?>" href="#raiseBillPopup" data-target="#raiseBillPopup" data-toggle="modal"  
																											data-orderid="<?php echo $woInvoice['order_id'] ?>"  
																											data-actid="<?php echo $woInvoice['activity_id'] ?>" 
																											data-qty="<?php echo $woInvoice['bill_qty'] ?>" 
																											style="padding:3px 12px; font-size:13px;">
																											Raise Bills
																									</button>
																								<?php } else{ ?>
														                    	<span style="color: green;">Bills Raised</span>
																								<?php } ?>
																								</div>
										                        		<div class="col-md-4  text-center" style="">
											                            <a target="_blank" href="printorderresource?id=<?php echo $woInvoice['order_id'] ?>" data-url="printorderresource?id=<?php echo $woInvoice['order_id'] ?>" class="btn btn-primary btn-sm icon-eye" title="View Order"></a>	
											                          </div>
																							</div>

									                        </div>
									                    </div>
									                  <?php  } ?>

						                    </div>

					                    	<div class="row po-resource-list-footer" style="padding: 15px;"></div>
					                    <?php } else { ?>
																<div style="padding:50px; text-align:center;">No Invoices found!!</div>
					                    <?php }  ?>

					              </div>



                    </div>
                    <div class="col-md-1">&nbsp;</div>
								</div>

							</div>
    					<!----- END ---------------->

    					<!------MUSTER ROLLS ---------------->
							<div class="invoice_management_container" id="invoiceDirectLabourContainer" style="display:none;">
								<div class="row">
                    <div class="col-md-1">&nbsp;</div>
                    <div class="col-md-10" >

                    	<?php
                    	if($labourArr){
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



                    	<div class="row "  style="margin-bottom: 30px !important;">
                    			<div class="row po-resource-list-header " >
							                    <div class="col-md-12" >
							                        <div class="col-md-4  " >
							                            <label style="margin-bottom:0px;  " title="">Activity</label>
							                        </div>
							                        <div class="col-md-3  text-center" >
							                            <label style="margin-bottom:0px;  " title="">Resource</label>
							                        </div>
							                        <div class="col-md-1  text-center no-padding" >
							                            <label style="margin-bottom:0px;  " title="Reported Days">Reprtd Days</label>
							                        </div>
							                        <div class="col-md-2 text-center " >
							                            <label style="margin-bottom:0px;  " title="Last Reported Date">Last Reprtd Date</label>
							                        </div>
							                        <div class="col-md-2 text-center no-padding"  >
							                            <label style="margin-bottom:0px; "></label>
							                        </div>
							                    </div>
							                	</div>

								                <div class="row invoiceContent"  style="">
								                	<?php 
								                		if($activity_resources = $labourArr['acty_resources']) { 
								                			foreach ($activity_resources as $key => $activity_res) {
							                    			$vendor_id 	 = $activity_res['vendor_id'];
							                    			$resource_id = $activity_res['resource_id'];
							                    			$ven_res_id	 = $vendor_id.'_'.$resource_id;

								                				$dueResourceRow = ($activity_res['due_notify_flag']) ? 'resourceRowDue' : '';
								                				$ven_res_act_id = $ven_res_id.'_'.$activity_res['activity_id'];

								                				$act_res_ven_key = $key;
								                	?>
							                    		<form id="generate_muster_form_<?php echo $act_res_ven_key ?>" method="POST">
									                			<div class="col-md-12 resourceRow <?php echo $dueResourceRow ?>" style="">
											                        <div class="col-md-4" >
											                            <?php echo $activity_res['activity_name'] ?>

												                        	<input type="hidden" class="actvtyResSelect actvtyResSelect<?php echo $ven_res_id ?>" name="muster_ids[]" value="<?php echo $activity_res['musterIds'] ?>" data-venresactid="<?php echo $ven_res_act_id ?>"  data-venresid="<?php echo $ven_res_id ?>"  data-resource_type_id="<?php echo $activity_res['resource_type_id'] ?>" > 
											                        </div>
											                        <div class="col-md-3 text-center" >
											                            <?php echo$activity_res['resource_name'].' ('. $activity_res['vendor'].')' ?>
											                        </div>
											                        <div class="col-md-1 text-center" >
											                            <?php echo$activity_res['reported_days'].' / '.$activity_res['payment_cycle'] ; ?> 
											                        </div>
											                        <div class="col-md-2 text-center">
											                            <?php echo $activity_res['last_reported_date'] ?>
											                        </div>
											                        <div class="col-md-2 text-center">
											                        		<?php if($activity_res['musterRollFlag'] == 1){ ?>
											                            <button type="button" class="btn btn-primary generate_muster_btn" 
											                            	id="generate_muster_btn<?php echo $ven_res_id; ?>" 
																										href="#generateMusterPopup" 
																										data-target="#generateMusterPopup" 
																										data-toggle="modal" 
																										data-vendorid="<?php echo $vendor_id; ?>"
																										data-resourceid="<?php echo $resource_id; ?>"  
																										data-actid="<?php echo $activity_res['activity_id']; ?>"  
																										data-act_res_ven_key="<?php echo $act_res_ven_key; ?>"  
																										
																										style="padding:3px 12px; font-size:13px;" 
																										value="" >
																											 Approve Muster Roll
																									</button>
																								<?php }  else { ?>
														                    	<span style="color: green;">Approved</span>
																								<?php } ?>
											                        </div>
										                    </div>
							                      	</form>
								                	<?php } } ?>	

						                    </div>

					                    	<div class="row po-resource-list-footer" style="">
																	<div class="col-md-12 po-resource-total">
																		<div  class="col-md-2 text-left no-padding">
																			&nbsp;
																		</div>

																		<div class="col-md-10 text-right" style=""></div>
																	</div>
					                    	</div>

                    	</div>

                    	<?php
                    	} 
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

    					<div class="invoice_management_container" id="invoicePlantEquipmentContainer">

								<div class="row">
                    <div class="col-md-1">&nbsp;</div>
                    <div class="col-md-10" >

                  		<div>
	                        <div class="row">
	                        <div class=" col-md-6 text-right" id="desp" >
			                        <a href="javascript:void(0);" class="btn btn-primary equipment-movement-tab equipment-movement-active" id="despatch-equipment-tab" data-type="despatch" title="Despatch Orders" ><span style="margin-right: 4px;" class="icon-upload2"></span> Despatch</a>
	                        </div>
	                         <div class=" col-md-6 text-left ">
			                        <a href="javascript:void(0);" class="btn btn-primary equipment-movement-tab equipment-movement " id="receive-equipment-tab" data-type="receive" title="Receive Orders" ><span style="margin-right: 4px;" class="icon-download2"></span> Receive</a>
	                        	</div>
	                      </div>
	                    </div>


                  		<div class="invoiceContent invoiceEquipmentContainer" id="despatchEquipmentContainer">
		                  				<?php if($despatchEquipments) { ?>

					                    	<div class="row po-resource-list-header ">
							                    <div class="col-md-12">
							                        <div class="col-md-3 text-left " >
							                            <label style="margin-bottom:0px;  " title=""> 
							                            	Equipment
							                            </label>
							                        </div>
							                        <div class="col-md-2 text-center " >
							                            <label style="margin-bottom:0px;  " title=""> 
							                            	Date
							                            </label>
							                        </div>
							                        <div class="col-md-3 text-left " style="padding-left: 0;">
							                            <label style="margin-bottom:0px;  " title="">
							                            	Move To
							                          	</label>
							                        </div>
							                        <div class="col-md-2 text-center">
							                            <label style="margin-bottom:0px; ">
							                            	Recieve Date
							                            </label>
							                        </div>
							                        <div class="col-md-2 text-center">
							                            <label style="margin-bottom:0px;">Actions</label>
							                        </div>
							                    </div>
							                	</div>

								                <div class="row " style="">

								                			<?php
								                			$despEqArr = [];
								                			foreach ($despatchEquipments as $eqpMovement) {
								                				$despEqArr[$eqpMovement->equipment_id][] = $eqpMovement->id;
								                			}
								                			
								                			$despEqIdArr = [];
								                			foreach ($despatchEquipments as $eqpMovement) {
									                			$despEqIds = implode(",", $despEqArr[$eqpMovement->equipment_id]);
								                				if(!isset($despEqIdArr[$eqpMovement->equipment_id])){
								                					$despEqIdArr[$eqpMovement->equipment_id][] = $eqpMovement->id;
								                			?>
								                			<div class="col-md-12 resourceRow " style="padding-left: 15px;">
									                        <div class="col-md-3 text-left" >
									                            <?php echo $eqpMovement->equipment->Name ?>									                        
									                         </div>
									                        <div class="col-md-2 text-center" >
									                            <?php echo $eqpMovement->date ?>									                        
									                         </div>
									                        <div class="col-md-3 text-left" style="padding-left: 0;">
									                            <?php echo $eqpMovement->toproject->Name ?>									                        
									                         </div>
									                        <div class="col-md-2 text-left">
									                            <input class="form-control datepicker" name="despatched_date[]" id="despatched_date<?php echo $eqpMovement->id ?>" type="text" value="<?php echo date('Y-m-d') ?>" >
									                        </div>
									                        <div class="col-md-2 text-center" id="despatch_equip_btn_container<?php echo $eqpMovement->id ?>">
									                        		<?php if($eqpMovement->status == 1){ ?>
																							<button type="button" class="btn btn-primary despatch_equip_btn" data-id="<?php echo $despEqIds ?>" id="receive_equip_btn<?php echo $eqpMovement->id ?>" style="padding:3px 12px; font-size:13px;">
																									Despetch Equipment
																							</button>
													                    <?php } elseif($eqpMovement->status == 2){//Despatched { ?>
													                    	<span style="color: green;">Despatched</span>
													                    <?php }  ?>
									                        </div>
									                    </div>
									                  <?php } } ?>
						                    </div>
					                    	<div class="row po-resource-list-footer" style="padding: 15px;"></div>
					                    <?php } else { ?>
																<div style="padding:50px; text-align:center;">No Invoices found!!</div>
					                    <?php }  ?>
					              </div>


                  		<div class="invoiceContent invoiceEquipmentContainer" id="receiveEquipmentContainer"  style="display: none;">

		                  				<?php if($receiveEquipments) { ?>

					                    	<div class="row po-resource-list-header ">
							                    <div class="col-md-12">
							                        <div class="col-md-3 text-left " >
							                            <label style="margin-bottom:0px;  " title=""> 
							                            	Equipment
							                            </label>
							                        </div>
							                        <div class="col-md-2 text-center " >
							                            <label style="margin-bottom:0px;  " title=""> 
							                            	Date
							                            </label>
							                        </div>
							                        <div class="col-md-3 text-left " style="padding-left: 0;">
							                            <label style="margin-bottom:0px;  " title="">
							                            	Move From
							                          	</label>
							                        </div>
							                        <div class="col-md-2 text-center">
							                            <label style="margin-bottom:0px; ">
							                            	Recieve Date
							                            </label>
							                        </div>
							                        <div class="col-md-2 text-center">
							                            <label style="margin-bottom:0px;">Actions</label>
							                        </div>
							                    </div>
							                	</div>

								                <div class="row " style="">

			                  						<?php

							                			$recEqArr = [];
							                			foreach ($receiveEquipments as $eqpMovement) {
							                				$recEqArr[$eqpMovement->equipment_id][] = $eqpMovement->id;
							                			}
							                			
							                			$recEqIdArr = [];
							                			foreach ($receiveEquipments as $eqpMovement) {
								                			$recEqIds = implode(",", $recEqArr[$eqpMovement->equipment_id]);

							                				if(!isset($recEqIdArr[$eqpMovement->equipment_id])){
							                					$recEqIdArr[$eqpMovement->equipment_id][] = $eqpMovement->id;

								                			?>
								                			<div class="col-md-12 resourceRow " style="padding-left: 15px;">
									                        <div class="col-md-3 text-left" >
									                            <?php echo $eqpMovement->equipment->Name ?>									                        
									                         </div>
									                        <div class="col-md-2 text-center" >
									                            <?php echo $eqpMovement->date ?>									                        
									                         </div>
									                        <div class="col-md-3 text-left" style="padding-left: 0;">
									                            <?php echo $eqpMovement->fromproject->Name ?>									                        
									                         </div>
									                        <div class="col-md-2 text-left">
									                            <input class="form-control datepicker" name="received_date[]" id="received_date<?php echo $eqpMovement->id ?>" type="text" value="<?php echo date('Y-m-d') ?>" >
									                        </div>
									                        <div class="col-md-2 text-center" id="receive_equip_btn_container<?php echo $eqpMovement->id ?>">
									                        		<?php if($eqpMovement->status == 2){//Despatched ?>
																							<button type="button" class="btn btn-primary receive_equip_btn" data-id="<?php echo $recEqIds ?>" id="receive_equip_btn<?php echo $eqpMovement->id ?>" style="padding:3px 12px; font-size:13px;">
																									Receive Equipment
																							</button>
													                    <?php } elseif($eqpMovement->status == 3){//Received { ?>
													                    	<span style="color: green;">Receieved</span>
													                    <?php }  ?>
									                        </div>
									                    </div>
									                  <?php  } } ?>

						                    </div>

					                    	<div class="row po-resource-list-footer" style="padding: 15px;"></div>
					                    <?php } else { ?>
																<div style="padding:50px; text-align:center;">No Invoices found!!</div>
					                    <?php }  ?>

					              </div>



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
