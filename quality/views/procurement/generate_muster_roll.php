<?php
use app\models\WorkorderItems;
use app\models\WorkorderBills;


/*$vendor 					= 	$purchaseOrder->vendor;
$project 					= 	$purchaseOrder->project;
$resource_id 			= 	$resource->Resource_Id;
*/
?>

<form method="POST" action="<?php echo Yii::$app->request->baseUrl.'/procurement/reportmusterroll' ?>" id="raisemusterform" autocomplete="off">  
        	<div class="col-md-12 raise-muster-roll">
              <div class="row">
                  <div class="col-md-4  type">
                      <span>Trade : <?php echo $musters[0]['resource_name']; ?> </span>
                  </div>

                  <div class="col-md-4  type text-center">
                      <span>Vendor Name : <?php echo $musters[0]['vendor_name']; ?> </span>
                  </div>                              
               
                  <div class="col-md-4 type text-right">
                      <span>Activity : <?php echo $musters[0]['activity_name']; ?> </span>
                  </div>
                  <input type="hidden" name="activity_id" value="<?php echo $musters[0]['activity_id']; ?>">
                  <input type="hidden" name="allmusterids" value="<?php echo $muster_ids; ?>"> 
              </div>
      
              <div class="row" style="display:none;">
                  
                  <div class="col-md-4 type">
                      <label></label>
                      <span>Vendor Name : Vinod </span>
                  </div>                                      
                  <div class="col-md-3 type" style="display:none;">
                      <div class="form-group">
                          <label>Date From</label>
                          <input class="form-control datepicker" id="raisemustfromdate" name="fromdate" placeholder="Select From Date" type="text" value="">
                      </div>
                  </div>
                  <div class="col-md-4 type" style="display:none;">
                      <div class="form-group">
                          <label>Date To</label>
                          <input class="form-control datepicker" id="raisemusttodate" name="enddate" placeholder="Select To Date" type="text" value=""> 
                      </div>
                  </div>
                  <div class="col-md-1 type" style="display:none;">
                      <div class="form-group">
                          <label>&nbsp;</label> 
                          <button class="btn btn-primary search-btn" type="button" id="raisemustsearch"><span class="icon-search5"></span></button>
                      </div>
                  </div>
              </div>
         	</div>
	        <div class="col-md-12">
	            <table class="table table-bordered">
	                <tr>
	                    <th width="50">#</th>
	                    <th width="250">Name of Resource</th>
	                    <th width="100">Daily Rate</th>
	                    <th width="100">OT Rate</th>
	                    <th width="120">Days Worked</th>
	                    <th width="100">OT Hours</th>
	                    <th  width="150" class="text-right">Wages Earned</th>
	                    <th width="100">Deductions</th>
	                    <th  width="150">Net Amount</th>
	                </tr>
	                <?php 
	                foreach ($musters as $key => $muster) { 
	                	$muster_no = $key+1;
	                ?>
		            	<tr class="fulldata" id="fulldata1" data-value="2" data-rate="900.00" data-otrate="100" data-othrs="0" data-id="1">
		                    <td><?php echo $muster_no; ?></td>
		                    <td>
		                    		<input class="form-control not-allowed worker" name="worker[]" size="2" type="text" readonly="" value="<?php echo $muster['name']; ?>">
		                    		<input type="hidden" id="raisetrade<?php echo $muster_no; ?>" data-id="<?php echo $muster_no; ?>" name="trade[]" value="<?php echo $muster['trade_id']; ?>"></td>
		                    <td>
		                    		<span id="raiserate<?php echo $muster_no; ?>"><input type="text" id="raiseratess<?php echo $muster_no; ?>" class="form-control sml-input ratechange" data-id="<?php echo $muster_no; ?>" value="<?php echo $muster['rate']; ?>"></span>
		                        <input type="hidden" id="raiserateval<?php echo $muster_no; ?>" name="rateval<?php echo $muster_no; ?>" class="ratevalall" value="<?php echo $muster['rate']; ?>">
		                        <input type="hidden" id="edtraiserateval<?php echo $muster_no; ?>" name="edtrateval[]" class="edtratevalall" value="<?php echo $muster['rate']; ?>">
		                    </td>
		                    <td>
		                      	<span><input type="text" id="raiseot<?php echo $muster_no; ?>" class="form-control sml-input otratechange" data-id="<?php echo $muster_no; ?>" value="<?php echo $muster['ot_rate']; ?>"></span>
		                      	<input type="hidden" id="raiseotval<?php echo $muster_no; ?>" class="otratevaal" name="otval<?php echo $muster_no; ?>" value="<?php echo $muster['ot_rate']; ?>">
		                  	</td>
		                    <td>
		                      	<input class="form-control not-allowed raiseworkedhrs" data-id="<?php echo $muster_no; ?>" size="1" name="workedhrs[]" id="raiseworkedhrs<?php echo $muster_no; ?>" type="text" readonly="" value="<?php echo $muster['no_of_days']; ?>">
		                      	<input name="edworkedhrs[]" id="edraiseworkedhrs<?php echo $muster_no; ?>" type="hidden" value="<?php echo $muster['no_of_days']; ?>">
		                    </td>
		                    <td>
		                      	<input class="form-control not-allowed raiseovertime" data-id="<?php echo $muster_no; ?>" size="1" name="overtime[]" id="raiseovertime<?php echo $muster_no; ?>" type="text" readonly="" value="<?php echo $muster['othours']; ?>">
		                        <input name="edovertime[]" id="edraiseovertime<?php echo $muster_no; ?>" type="hidden" value="<?php echo $muster['othours']; ?>">
		                        <input type="hidden" id="ordeidshw<?php echo $muster_no; ?>" value="922" data-value="306">
		                    </td>
		                    <td class="text-right">
		                      	<span id="raisewages<?php echo $muster_no; ?>"><?php echo $muster['tot_wages']; ?></span>
		                        <input type="hidden" id="raisewagesval<?php echo $muster_no; ?>" class="wagesval" name="wages[]" value="<?php echo $muster['tot_wages']; ?>">
		                        <input type="hidden" id="raisemusteractivity" name="musteractivity" value="<?php echo $musters[0]['activity_id']; ?>">
		                        <input type="hidden" id="raiseprocess" name="process" value="1">
		                        <input type="hidden" name="project_id" value="<?php echo $musters[0]['project_id']; ?>">
		                        <input type="hidden" id="raise_Order_ID" name="orderid" value="0">
		                    </td>
		                    <td>
		                    		<input class="form-control deduction" data-id="<?php echo $muster_no; ?>" name="deduction[]" id="deduction<?php echo $muster_no; ?>" size="1" type="text" value="0">
		                    </td>
		                    <td>
		                    		<span class="netamount" id="netamount<?php echo $muster_no; ?>"><?php echo $muster['net_amount']; ?></span>
		                    </td>                     
		                </tr>
	                <?php } ?>
			              <tr class="total-row" id="workerrow">
					            <td colspan="5"><strong>Total Wages</strong></td>
					            <td colspan="3" class="text-right"><strong id="totw"><?php echo $total_amt; ?></strong></td>
					            <td></td>
					            <td><span id="totalwages"></span></td>
			        			</tr>
	    				</table> 
    			</div>
			    <div class="row">
			        <div class="col-md-12 text-center">
			            <label>&nbsp;</label>
                	<button type="button" value="Cancel" class="btn btn-primary cancel" id="closemusterroll" data-dismiss="modal"><span class="icon-close"></span> Cancel</button>
			            <button type="submit" class="btn btn-primary" name="musterreport" id="raisemusterbtn"><span class="icon-check"></span> Submit</button>
			        </div>
			    </div>
</form>