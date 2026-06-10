<?php
use app\models\WorkorderItems;
use app\models\WorkorderBills;


$vendor 				= 	$purchaseOrder->vendor;
$project 				= 	$purchaseOrder->project;
$resource_id 			= 	$resource->Resource_Id;

?>
<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/operations/workordersbill.js" type="text/javascript"></script> 


<form method="POST" action="<?php echo Yii::$app->request->baseUrl.'/procurement/receiveorder' ?>" id="billssview" autocomplete="off">  
		
	<div class="content-wrpr" id="wbhis" style="overflow: hidden; padding:20px;">

              
                          <div class="wrkbillviews row" style="margin-left: 10px; margin-right: 10px;">
                             
                            <div id="workorderbillitemsview">
                            	<div class="row">
                            		<div class="col-md-12">
                            			<div class="text-center">
                            				<label style="font-size: 15px;">Bill</label>
                            			</div>
                            		</div>
                            	</div>

					                    <div class="col-md-12 work-order-vendor-and-project" style="background-color: #eaeaef;">
					                        <div class="row">
					                            <div class="col-md-3 type">
					                                <label>Vendor</label>
					                                <span><?php echo $vendor->Name ?></span>
					                            </div>
					                            <div class="col-md-3 type">
					                                <label>Project</label>
					                                <span><?php echo $project->Name ?></span>
					                            </div>
					                            
					                            <div class="col-md-3 type">
					                                <div class="form-group">
					                                    <label>Date</label>
					                                    <span><?php echo date('d-m-Y',strtotime($purchaseOrder['orderdate'])) ?></span>
					                                    <!-- <input class="form-control" id="" name="" placeholder="Date" value="01-07-2023" type="text"> -->                                  
					                                </div>
					                            </div>
					                            <div class="col-md-3 type">
					                                <div class="form-group">
					                                    <label>Bill No.</label>
					                                    <input class="form-control" id="" name="billno" placeholder="Bill No." value="<?php echo $bill_number ?>" type="text">
					                                </div>
					                            </div>
					                        </div>
					                    </div>
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            
                            <tbody>
                            <tr style="background-color:#fafafd"></tr>
				                    <tr style="background-color:#eaeaef">
					                    <th>#</th>
					                    <th>Item</th>
					                    <th>Unit</th>
					                    <th class="text-right">Rate</th>
					                    <th class="text-right">Est Qty</th>
					                    <th class="text-right" style="width:105px;">Qty-Last Bills</th>
					                    <th class="text-right" style="width:110px;">Amt-Last Bills</th>
					                    <th class="text-right" style="width:100px;">Current Qty</th>
					                    <th class="text-right">Current Amt</th>
					                    <th class="text-right" style="width:10%;">Total Qty</th>
					                    <th class="text-right">Total Amt</th>
				                    </tr>


				                    <?php 
									$total=0;
				                    foreach ($purchaseOrder->resources as $key => $purchaseOrderResource) {

				                    	//--- LAST BILLS -------------
								        $billqty = 0;
								        $deduction_total = 0;
								        if($lastBill){
								            $billitems = WorkorderItems::find()->where(['delete_status' => 0])->andWhere(['order_id'=>$orderid])->andWhere(['resource_id'=>$purchaseOrderResource['resource_id']])->all();
								            if($billitems):
								                foreach($billitems AS $billitem){
								                    $billqty+=$billitem->resource_qty;
								                }
								            endif;
								            $deduction_total = WorkorderBills::find()->where(['order_id' => $orderid])->andwhere(['status' => 1])->sum('other_deductions');

								        }


								        $amountlast =($purchaseOrderResource['rate'] * $billqty);
								        //$amount 	= $purchaseOrderResource['rate'] * $resourceitem['resource_qty'];
								        $totamount 	= $billqty * $purchaseOrderResource['rate'];
							            $total 		= $total + $totamount;

								        $ledgerbal = Yii::$app->helper->vendorledgerbalance($purchaseOrder['place'],$vendor['account_id'] );


				                    ?>
                						<tr>
							                <td>
							                    <?php echo $key+1 ?>
						                     <input type="hidden" id="ressid" name="resourceid[]" value="<?php echo $resource_id ?>">
						                     <input type="hidden"  name="activityid[]" value="<?php echo $activityid ?>">

						                     <input type="hidden" id="ordbillid" name="ordbillid" value="<?php echo $bill_number ?>">
						                     <input type="hidden" id="ordid" name="ordid" value="<?php echo $purchaseOrder->order_id ?>">
						                     <input type="hidden" id="orderQty<?php echo $resource_id ?>" name="orderQty" value="<?php echo $purchaseOrderResource->qnty ?>">
						                     <input type="hidden" id="resourceName<?php echo $resource_id ?>" value="<?php echo $resource->Name ?>">
							                </td>
							                <td><?php echo $purchaseOrderResource->resource_name ?></td>
							                <td><?php echo $purchaseOrderResource->unit ?></td>
							                <td align="right">
							                	<span id="rateres<?php echo $resource_id ?>"><?php echo $purchaseOrderResource->rate ?></span>
							                </td>
							                <td><?php echo $purchaseOrderResource->qnty ?></td>
							                <td style="text-align: right">
							                	<?php echo $billqty ?>
							                	<input type="hidden" id="qtyuptolastt<?php echo $resource_id ?>" value="<?php echo $billqty ?>">
							                </td>
							                <td style="text-align: right">
							                	<?php echo $amountlast ?>
							                	<input type="hidden" id="amountuptolastt<?php echo $resource_id ?>" value="<?php echo $amountlast ?>">
							                </td>
							                
							                <td style="text-align: right">
							                    <input style="text-align:center;" type="number" class="form-control resourceqntty" id="resourceqntty<?php echo $resource_id ?>" data-id="<?php echo $resource_id ?>" name="resourceqnty[]" value="0" size="1">
							                </td>
							                <td align="right">
							                    <span id="currentamountt<?php echo $resource_id ?>">0.00</span>
							                    <input type="hidden" class="currentamountt" id="camounttval<?php echo $resource_id ?>" value="0">
							                </td>

							                <td style="text-align: right">
							                	<span id="totalqtty<?php echo $resource_id ?>"><?php echo $billqty ?></span>
							                </td>
							                <td style="text-align: right">
							                	<span id="qtyamnt<?php echo $resource_id ?>"><?php echo number_format((float)$totamount, 2) ?></span>
							                	<input type="hidden" class="reourceamount" id="resamnnt<?php echo $resource_id ?>" value="<?php echo $totamount ?>"> 
							                </td>   
						                </tr>
						              	<?php } 

						              	?>



                            <tr>
	                            <td colspan="5">Gross Amount</td>   
	                            <td style="text-align: right"><?php echo number_format((float)$total, 2) ?></td>       
	                            <td></td>       
	                            <td class="text-right"><span id="currenttotal">0.00</span></td>       
	                            <td colspan="2" class="text-right"><span id="billstotal"><?php echo number_format((float)$total, 2) ?></span>
	                                <input type="hidden" id="grssamnt" value="0.00">
	                            </td>
	                        	</tr>

 														<?php
 														if($purchaseOrder['sgst']!=0):
	                            $gstamount = (($total * $purchaseOrder['sgst']) / 100) * 2;
	                            $datarowv.='<tr>
	                                            <td colspan="5">SGST ('.($purchaseOrder['sgst']*2).' %)
	                                                <input type="hidden" id="ssgst" value="'.($purchaseOrder['sgst']*2).'">
	                                            </td>  
	                                            <td  style="text-align: right">'.number_format((float)$gstamount, 2).'</td>       
	                                            <td></td>  
	                                            <td class="text-right"><span id="currentsgstsmountt"></span></td>       
	                                            <td colspan="2" class="text-right"><span id="sgstsmountt">'.number_format((float)$gstamount, 2).'</span>
	                                                <input type="hidden" id="scgst" value="'.$purchaseOrder['sgst'].'">
	                                            </td>
	                                        </tr>
	                                        ';
	                            $amountinc=$total + $gstamount;

	                           
	                        elseif(($purchaseOrder['igst']!=0)):
	                            $igstamount=($total * $purchaseOrder['igst']) / 100;
	                            $datarowv.='<tr>
	                                            <td colspan="5">IGST ('.$purchaseOrder['igst'].' %)
	                                                <input type="hidden" id="sigst" value="'.$purchaseOrder['igst'].'">
	                                            </td>   
	                                            <td  style="text-align: right">'.number_format((float)$igstamount, 2).'</td>       
	                                            <td></td>  
	                                            <td class="text-right"><span id="currentigstamountt"></span></td>       
	                                            <td colspan="2" class="text-right"><span id="igstamountt">'.number_format((float)$igstamount, 2).'</span></td>
	                                        </tr>';
	                            $amountinc=$total + $igstamount;
	                        else:
	                            $amountinc=$total;
	                        endif;
	                        ?>

		                        <tr>
		                            <td colspan="5">Amount Including Taxes</td>    
		                            <td style="text-align: right"><?php echo number_format((float)$amountinc, 2) ?></td>       
		                            <td></td>       
		                            <td class="text-right"><span id="currentamounttinclusive">0.00</span></td>       
		                            <td colspan="2" class="text-right"><span id="amounttinclusive"><?php echo number_format((float)$amountinc, 2) ?></span></td>
		                        </tr>
		                        <tr>
		                            <td colspan="5">Retention (<?php echo $purchaseOrder['Retention'] ?> %)</td> 
		                            <td style="text-align: right"><?php echo number_format((float)$retamount=($total * $purchaseOrder['Retention']) / 100, 2) ?></td>       
		                            <td></td>       
		                            <td class="text-right"><span id="currentretention">0.00</span></td>       
		                            <td colspan="2" class="text-right">
		                                <span id="wbillretention"><?php echo number_format((float)$retamount=($total * $purchaseOrder['Retention']) / 100, 2) ?></span>
		                                <input type="hidden" name="retention" id="rretention" value="<?php echo $purchaseOrder['Retention'] ?>">
		                            </td><input type="hidden" name="nettotal" id="wnettotal" value="<?php echo $netamount=$amountinc - $retamount ?>">
		                        </tr>
		                        <tr>
		                            <td colspan="5">Other deductions</td> 
		                            <td style="text-align: right"><?php echo number_format((float)$deduction_total, 2) ?></td>       
		                            <td class="text-right">
		                                <input style="text-align:center;" type="number" class="form-control " id="otherDeductions" data-id="<?php echo $resource_id ?>" name="other_deductions" value="0" size="1">

		                            </td>       
		                            <td class="text-right"><span id="currentdeductions"><?php echo number_format((float)$deduction_total, 2) ?></span></td>       
		                            <td colspan="2" class="text-right">
		                            <span id="billotdeduct">0.00</span>

		                            <input type="hidden" id="wotdeductions" value="<?php echo $deduction_total ?>">
		                            <input type="hidden" name="tot_other_deductions" id="tot_other_deductions" value="<?php echo $deduction_total ?>">
		                            </td>
		                        </tr>
		                        <tr>
		                            <td colspan="5">Net Amount</td>
		                            <td style="text-align: right"><?php echo number_format((float)$netamount - $deduction_total, 2) ?></td>       
		                            <td></td>       
		                            <td class="text-right"><span id="currentnetamount"></span></td>       

		                            <td colspan="2" class="text-right"><span id="wbillnetamount"><?php echo number_format((float)$netamount - $deduction_total, 2) ?></span>
		                            </td>
		                        </tr>
		                        <tr style="border-bottom: 2px solid #465365;">
		                            <td colspan="7">Amount paid till today</td>                                    
		                            <td colspan="3" class="text-right"><span id="billadvance"><?php echo number_format((float)$ledgerbal, 2) ?></span>
		                            </td>
		                        </tr>
		                        <tr class="total-row">

		                            <td colspan="7">Amount Payable</td>
		                            <td colspan="3" class="text-right"><span id="wamountpayabe"><?php echo number_format((float)($netamount - $deduction_total - $ledgerbal), 2) ?> </span></td>
		                        </tr>       
                        
                        </tbody>
                    </table>
                    <div class="row" style="padding-bottom: 25px;">
                        <div class="col-md-6 text-left">
                            <button type="button" class="btn btn-primary billcomplete">Approve &amp; Complete</button>
                        </div>
                        <div class="col-md-6 text-right">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-primary billapprove"> Approve</button>
                            <button type="button" class="btn btn-danger cancel" data-dismiss="modal"><span class="icon-close"></span> Cancel</button>
                        </div>

                    </div> 
                </div></div>
                          </div>

                        <div class="history-bill-list data-content-list" style="display: none;">
                          <div class="text-center row wrkbillhead"><label style="font-size: 15px;">Work Bill History</label></div>
                          <div id="workorderbillitemshistory" style="display: none;"></div>
                        </div>



                        <div class="row">
                            
                            <div id="billitemhist" style="display: none;"></div>
                          </div>




              <!-- Work bills -->
              
              <div class="work-orderbill-list-wrpr" style="display: none;">

                <div class="text-center row wrkbillhead" style="margin-left: 15px;margin-right: 15px;margin-bottom: 20px;"><label style="font-size: 15px;">Work Orders</label></div>
                <div class="preloader" style="display: none;" align="center">
                    <img src="/sreejith/opiam_analytics/web/images/loader.gif" align="middle">
                </div>
                <div id="" style="padding: 0 50px 10px 50px;">
                  
                  <table class="table table-bordered ">
                      <thead style="background: #eceef7;">
                              <!--<tr>
                                  <th colspan="9"><span style="float: left;font-weight: bold;padding: 10px;width: 100%;text-align: center">Cart</span></th>
                              </tr>-->
                              <tr>
                                  <th style="width: 87px;"></th>
                                  <th style="width: 313px;">Date</th>
                                  <th>Order Type</th>
                                  
                                  <th>Vendor Name</th>
                                  <th>Amount</th>
                                  <th colspan="4" style="width:14%;"></th>
                              </tr>
                      </thead>
                      <tbody id="workorderbills"><tr><td colspan="10" style="background-color: #f7f8fc;"><b>Task Report Test</b></td></tr><tr id="orderitemrow920">
                    <td><span class="number despatch-number">1</span></td>
                    <td><span class="date"><em class="cal-icon icon-calendar1"></em>06-11-2023</span></td>
                    <td>Setup Site Office</td>
                    
                    <td>Adapts Engineering</td>
                    <td style="text-align: right;">99,999,999.99</td>
                    <td class="icon-groups icon-order">
                    <a target="_blank" href="/sreejith/opiam_analytics/web/procurement/printorder?id=920" data-url="/sreejith/opiam_analytics/web/procurement/printorder?id=920" class="btn btn-primary btn-sm icon-eye" title="View Order"></a>

                        <a href="javascript:;" class="btn btn-primary viewworkbillitems" data-value="920" data-id="2" title="View" style="min-width: min-content;">Raise Bills</a></td></tr></tbody>        
                  </table>

                </div>

              </div>
            </div>






         		</form>