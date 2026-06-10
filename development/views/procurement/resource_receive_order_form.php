<?php


$vendor 					= 	$purchaseOrder->vendor;

$vendor_address[] = 	$vendor->Address;
$vendor_address[] = 	$vendor->City;
$vendor_address[] = 	$vendor->Phone;

?>


<form method="POST" action="<?php echo Yii::$app->request->baseUrl.'/procurement/receiveorder' ?>" id="receiveorderform" autocomplete="off">  
		<input type="hidden" name="activityData" value='<?php echo json_encode($activityData) ?>'>		 
		<input type="hidden" name="orderid" value='<?php echo $purchaseOrder->order_id ?>'>		 
		<div class="purchase-order2-wrpr receive-order-form" style="padding:0px; padding-bottom:0px;padding-top: 0;">
	    	        	
     		<div class="row">
        	<div class="col-md-12 col-xs-12 text-center">
    					<h4>Purchase Order</h4> 
					</div>
 				</div> 

			  <div class="row">
					<div class="col-md-12">
					  	<hr style=" color: #1e8cec;  border-color: #c2c2c2; margin: 15px;">
					</div>
				</div>

				<div class="row">
      				<div class="col-md-12">

								<div class="col-md-6 col-xs-6" style="text-align:left">
									<div class="text-left">	    						
										<h4 class="grayBg commentshding" style="text-align:left;"><font color="#191818">Description</font></h4>
										<textarea class="form-control" id="specification" name="Specification"><?php echo $purchaseOrder->specification ?></textarea>
										<span class="error"></span>	    						
									</div>
								</div>

								<div class="col-md-6 col-xs-6 text-center">
									<div class="text-left">		
										<h4 class="grayBg commentshding" style="text-align:left;"><font color="#191818">Vendor</font></h4>
										<p style="font-size: 12px;padding-left:7px;">
											<?php echo $vendor->Name.'<br>'.implode(', ', array_filter($vendor_address)) 		?>								
										</p>
										<span class="error"></span>				
									</div>
								</div>

							</div>

      				<div class="col-md-12">
	    	            <div class="col-md-6 col-xs-6" style="text-align:left">
	    		            <h4 class="grayBg">Bill To</h4>
	                		<div class="address">
	                		    <textarea class="form-control" id="billing_to" name="billing_to" rows="7">Geo Tech Construction Company Pvt Ltd,8th Floor, KSHB Office Complex,Panampilly Nagar, Cochin,Ernakulam, Kerala 680036</textarea>
	                            <span class="error"></span>
	                		</div>
	    	            </div>
    	          
	    	            <div class="col-md-6 col-xs-6" style="text-align:left">
	    	                <h4 class="grayBg">Ship To</h4>
	    		            <div class="address">
	    		                <textarea class="form-control" id="billing_address" name="billing_address" rows="7"><?php echo $project->Name ?></textarea>
	                            <span class="error"></span>
	    		            </div>
	    	            </div>
    	        </div>

  	    </div>

        <div class="row" style="padding-top:10px;">          
        	<div class="col-md-12 text-center">
                <span class="error" id="commonError"></span>
          </div>
				</div>

	    	         	<div class="row"> 
		    	         	<div class="col-md-12">    
		    	            	<div class="col-md-12">
		                			<table class="table table-bordered" style="border: 1px solid hsl(229 41% 95% / 1) !important; border-collapse: unset; ">
					                    <thead>
					                        <tr>
					                            <th width="70">Sl.No</th>
					                            <th>Resource &amp; Specification</th>
					                            <th width="80">Unit</th>
					                            <th width="80">Rate</th>
					                            <th width="60">Qnty</th>
					                            <th width="50">GST(%)</th>
					                            <th width="50">IGST(%)</th>
					                            <!-- <th width="10%">Tax</th> -->
					                            <th width="15%" style="text-align:center;">Amount</th>
					                        </tr>
					                    </thead>
			                    		<tbody class="tblAmountbody">
			                  <?php 
			                  	if($orderDataArr = $purchaseOrder->resources){
			                  		$rate = 0;
			                  		$total_amount = 0;
			                  		$slNo = 1;
			                  		foreach ($orderDataArr as $key => $orderData) {
			                  			$orderid = $orderData['order_id'];
			                  			$amount = ($orderData['rate'] * $orderData['qnty']);
			                  			$total_amount += $amount;

			                  ?>      		
	                   		<tr>
	                            <td>
	                            	<?php echo ($slNo++) ?>
	                                <input type="hidden" name="vendor" value="<?php echo $vendor->Vendor_Id ?>">
	                                <input type="hidden" name="Project" value="<?php echo $project->Project_Id ?>">
	                                <input type="hidden" name="resource_id[]" value="<?php echo $resource->Resource_Id ?>">
	                                <input type="hidden" name="order_resource_id[]" value="<?php echo $orderData->order_res_id ?>">
	                                <input type="hidden" name="unit[]" value="<?php echo $resource->Unit ?>">
	                                <input type="hidden" name="rate[]" value="<?php echo $orderData['rate'] ?>">
	                            </td>
	                            <td>


	                            	<input type="text" class="form-control  fonts" name="resource[]" value="<?php echo $resource->Name ?>" >


	                            </td>
	                            <td><?php echo $resource->Unit ?></td>  
	                            <td><?php echo number_format((float)$orderData['rate'], 2, '.', '') ?></td>
	                            <td>
	                            	<input type="text" class="form-control form-control-sml size " name="quantity[]" id="quantity" data-id="<?php echo $orderid ?>" value="<?php echo $orderData['qnty']  ?>">
	                            </td>
	                            <td>
	                            	<input type="text" class="form-control size cgsttt cgstvalue<?php echo $orderid ?>" name="gst[]" id="cgst" data-id="<?php echo $orderid ?>" value="<?php echo $orderData['cgst']+$orderData['sgst']  ?>">
	                            </td>
	                            <td>
	                            	<input type="text" class="form-control size igsttt igstvalue<?php echo $orderid ?>" name="igst[]" id="igst" data-id="<?php echo $orderid ?>" value="<?php echo $orderData['igst'] ?>">
	                            	<input type="hidden" class="form-control itemTaxValue" id="taxvalue<?php echo $orderid ?>">
	                            	<input type="hidden" class="amntvalue<?php echo $orderid ?>" value="<?php echo number_format((float)$amount, 2, '.', '')  ?>">
	                            </td>
	                            <td><input style="text-align:right;" type="text" class="form-control" name="amount[]" id="amountt" value="<?php echo number_format((float)$amount, 2, '.', '')  ?>" readonly=""></td>
	                        </tr>

	                      <?php } }  ?>



			                         		<tr class="total-row2">  

			                         			<td colspan="7" style="text-align:right;vertical-align: middle;"><strong>Freight<strong></strong></strong></td>
			                        			<td width="10%"><input type="text" class="form-control" id="freight" name="freight" placeholder="Freight" autocomplete="off"  value="<?php echo $purchaseOrder->freight ?>"> </td>

			                         			<!--<td style="text-align:right;vertical-align: middle;"><strong>TAX<strong></td>
						                        
					                         	<td width="10%" ><input type="text" class="form-control" id="tax" name="tax" placeholder="Tax" onkeyup="subAmount()" autocomplete="off" disabled> </td>-->
			                         		</tr>

			                         		<tr class="total-row2" style="display:none;">  

			                         			<td colspan="7" style="text-align:right;vertical-align: middle;"><strong>SUB TOTAL<strong></strong></strong></td>
			                         			<td width="15%"><input style="text-align:right;" type="text" class="form-control" id="sub_total" name="sub_total" disabled="" autocomplete="off" value="<?php echo number_format((float)$total_amount, 2, '.', '')  ?>"></td>
			                         		</tr>

			                         		<tr class="total-row2">

			                        			<td colspan="7" style="text-align:right;vertical-align: middle;"><strong>Insurance<strong></strong></strong></td>
			                        			<td width="10%"> <input type="text" class="form-control" id="insurance" name="insurance" placeholder="Insurance" autocomplete="off" value="<?php echo $purchaseOrder->insurance ?>">  </td>

			                         		</tr>
			                         		<tr class="total-row2">

			                        			<td colspan="7" style="text-align:right;vertical-align: middle;"><strong>Tax<strong></strong></strong></td>
			                        			<td width="10%"> <input type="text" class="form-control" id="tax" name="tax" placeholder="Tax" onkeyup="subAmount()" autocomplete="off" disabled="">  </td>

			                         		</tr>
			                         		<tr class="total-row2">

			                          			<td colspan="7" style="text-align:right;vertical-align: middle;"><strong>Others<strong></strong></strong></td>
			                        			<td width="10%">  <input type="text" class="form-control" id="others" name="others" placeholder="Others" value="0" autocomplete="off" value="<?php echo $purchaseOrder->others ?>">   </td>

			                         		</tr>
			                         		<tr class="total-row2">

			                         			<td colspan="7" style="text-align:right;vertical-align: middle;"><strong>TOTAL<strong></strong></strong></td>                        
			                         			<!--<td width="15%">  <input style="text-align:right;" type="text" class="form-control total" id="total" name="total" disabled autocomplete="off" value="13,200,000.00"></td> -->


			                         				<td width="15%">  <input style="text-align:right;" type="text" class="form-control newtotal" id="newtotal" name="newtotal" disabled="" autocomplete="off" value="<?php echo number_format((float)$total_amount, 2, '.', '')  ?>"></td>
			                         		</tr>
			                    		</tbody>
		                			</table>
		                		</div>
	                		</div>
	                	</div>

	                	<!--<div class="row">
	    	            	<div class="col-md-12">
	           					<hr style=" color: #1e8cec; border-color: #465365;">
	            			</div>
	        			</div>-->


	                	<div class="row">
	    	            	<div class="col-md-12">
		                    	<div style="">
		                        	<div class="approveForm row">

		                        		<div class="col-md-3">
		                        			<div class="row">
			                              <div class="">
										             			<label>PO Number:</label>
										             			<input type="text" class="form-control" name="po_number" id="po_number" value="<?php echo $purchaseOrder->ordernumber ?>">
								             				</div>                                    
			                            </div>
					            					</div>

										            <div class="col-md-3">
										            		<div class="row">
								                      <div>
    							<label>Order Date</label>
    							<input type="hidden" name="ordertype" value="1">
     							<input type="date" class="form-control" name="orderofdate"  value="<?php echo $purchaseOrder->orderdate ?>">
									     								</div>
								     							</div>
										     				</div>

			                          <div class="col-md-3">
			                                <div class="row">
			                                    <div>
			                                        <label>Receive Date</label>
			                                        <input type="date" class="form-control" name="received_date"  value="<?php echo $purchaseOrder->deliverydate ?>">
			                                        <span class="error"></span>
			                                    </div>
			                                    
			                                </div>
			                          </div>


			                            <div class="col-md-3">
			                                <div class="row">
			                                    <div>
			                                        <label>Contact Person</label>
			                                        <input type="text" class="form-control" name="Contact" value="<?php echo $purchaseOrder->contact ?>">
			                                        <input type="hidden" name="ordertype" value="1">
			                                    </div>                                    
			                                </div>
			                            </div>
			                            


		                     		<div class="col-md-3" style="height:76px">
		                     			<div class="row">
		                     				<div>
		                     					<label>Credit Period</label>
		                     					<select id="credperiod" name="creditperiod">
		                     						<option value="none">Select Credit Period</option>
		                     						<option value="0 Days">Instantly</option>
		                     						<option value="15 Days" <?php echo ($purchaseOrder->creditperiod == 15) ? 'selected' : '' ?> >15 Days</option>
		                     						<option value="30 Days" <?php echo ($purchaseOrder->creditperiod == 30) ? 'selected' : '' ?>>30 Days</option>
		                     						<option value="45 Days" <?php echo ($purchaseOrder->creditperiod == 45) ? 'selected' : '' ?>>45 Days</option>
		                     						<option value="60 Days" <?php echo ($purchaseOrder->creditperiod == 60) ? 'selected' : '' ?>>60 Days</option>
		                     						<option value="90 Days" <?php echo ($purchaseOrder->creditperiod == 90) ? 'selected' : '' ?>>90 Days</option>
		                     						
		                     					</select>
		                     				</div>
		                     			</div>
		                     		</div>

		                         		<div class="col-md-3"> 
		                                	<div class="row">
		                                    	<div>
		                                    		<input type="hidden" class="form-control" id="tandcstatus" name="tandcstatus" value="0"> 
		                            				<input type="hidden" class="form-control" id="tandcNow" name="tandc" value="">
		                                        	<label>Terms and conditions</label>
		                                        		<select class="form-control" id="mytermspurchase">
		                                            	<option value="none">Select Terms and conditions</option><option value="1"> Terms and Conditions - Sub Contractor-fixing Fenders </option><option value="2">Hello terms and conditions </option><option value="3"> Fixing fenders terms and conditions  </option><option value="4">Construction of block wall Terms and conditions </option><option value="5">Transportation of Block wall terms and conditions </option><option value="6">Transportation of PCC blocks terms and conditions </option><option value="7">Pipeline Gang terms and conditions </option><option value="8"> Vibro Hammer lease terms and conditions </option><option value="9">Concrete pump lease terms and conditions </option><option value="10">Ready mix concrete for pile terms and conditions </option><option value="11">Transit mixer lease terms and conditions </option><option value="12">Fixing of handrail terms and conditions </option>
		                                         		</select>
			                                     		<span class="error termscondtnerror"></span>
			                        					<span class="pull-right"><a class="tandcupdatepurchase" data-toggle="modal" data-target="#myModalpurchase" style="color:orange;font-weight:bold;cursor:pointer;display:none;">View</a></span>
		                                		</div>                                    
		                                	</div>                               
		                            	</div>

			                            <div class="col-md-12 text-center india" style="margin-top:20px;display:none">
						                    
						                </div><br>
		                             	<div class="col-md-12 text-center approve-cancel" style="margin-top:30px;">
		                                	<button type="button" value="Cancel" class="btn btn-primary cancel" id="cancelorder" data-dismiss="modal"><span class="icon-close"></span> Cancel</button>
		                                	<button type="submit" value="submit" id="receiveorderbtn" class="btn btn-primary"  name="submit"><span class="icon-check"></span> Receive</button>
		                            	</div>
		                        	</div>
		                    	</div>
	                    	</div>
	                	</div>
	            	</div>       
         		</form>