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
																									<?php if($purchaseOrder->order_type == 1) { ?>

																									<button type="button" class="btn btn-primary receive_order_btn" id="receive_order_btn_<?php echo $purchaseOrder->order_id ?>" href="#receiveOrderPopup" data-target="#receiveOrderPopup" data-toggle="modal"  data-orderid="<?php echo $purchaseOrder->order_id ?>" style="padding:3px 12px; font-size:13px;">
																											Receive Order
																									</button>

																									<?php } elseif($purchaseOrder->order_type == 2) { ?>

																									<button type="button" class="btn btn-primary raise_bill_btn" id="raise_bill_btn_<?php echo $purchaseOrder->order_id ?>" href="#raiseBillPopup" data-target="#raiseBillPopup" data-toggle="modal"  data-orderid="<?php echo $purchaseOrder->order_id ?>" style="padding:3px 12px; font-size:13px;">
																											Raise Bills
																									</button>
																									<?php } ?>
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
								                				if($purchaseOrder->order_type == 2) { 

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
																									<?php if($purchaseOrder->order_type == 1) { ?>

																									<button type="button" class="btn btn-primary receive_order_btn" id="receive_order_btn_<?php echo $purchaseOrder->order_id ?>" href="#receiveOrderPopup" data-target="#receiveOrderPopup" data-toggle="modal"  data-orderid="<?php echo $purchaseOrder->order_id ?>" style="padding:3px 12px; font-size:13px;">
																											Receive Order
																									</button>

																									<?php } elseif($purchaseOrder->order_type == 2) { ?>

																									<button type="button" class="btn btn-primary raise_bill_btn" id="raise_bill_btn_<?php echo $purchaseOrder->order_id ?>" href="#raiseBillPopup" data-target="#raiseBillPopup" data-toggle="modal"  data-orderid="<?php echo $purchaseOrder->order_id ?>" style="padding:3px 12px; font-size:13px;">
																											Raise Bills
																									</button>
																									<?php } ?>
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


    					<!------PLANT & EQUIPMENT ---------------->

    					<div class="invoice_management_container" id="invoicePlantEquipmentContainer">

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
		                  				<?php if($equipmentMovements) { ?>

					                    	<div class="row po-resource-list-header ">
							                    <div class="col-md-12">
							                        <div class="col-md-3 text-left " >
							                            <label style="margin-bottom:0px;  " title=""> 
							                            	Equipment
							                            </label>
							                        </div>
							                        <div class="col-md-2 text-left " >
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
								                			foreach ($equipmentMovements as $equipmentMovement) {
								                			
								                			?>
								                			<div class="col-md-12 resourceRow " style="padding-left: 15px;">
									                        <div class="col-md-3 text-left" >
									                            <?php echo $equipmentMovement->equipment->Name ?>									                        
									                         </div>
									                        <div class="col-md-2 text-center" >
									                            <?php echo $equipmentMovement->date ?>									                        
									                         </div>
									                        <div class="col-md-3 text-left" style="padding-left: 0;">
									                            <?php echo $equipmentMovement->fromproject->Name ?>									                        
									                         </div>
									                        <div class="col-md-2 text-left">
									                            <input class="form-control datepicker" name="received_date[]" id="received_date<?php echo $equipmentMovement->id ?>" type="text" value="<?php echo date('Y-m-d') ?>" >
									                        </div>
									                        <div class="col-md-2 text-center" id="receive_equip_btn_container<?php echo $equipmentMovement->id ?>">
									                        		<?php if($equipmentMovement->status == 1){ ?>
																							<button type="button" class="btn btn-primary receive_equip_btn" data-id="<?php echo $equipmentMovement->id ?>" id="receive_equip_btn<?php echo $equipmentMovement->id ?>" style="padding:3px 12px; font-size:13px;">
																									Receive Equipment
																							</button>
													                    <?php } else { ?>
													                    	<span style="color: green;">Receieved</span>
													                    <?php }  ?>
									                        </div>
									                    </div>
									                  <?php }  ?>

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
