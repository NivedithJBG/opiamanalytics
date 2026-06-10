
<?php

use app\models\Drawings;
use amnah\yii2\user\models\User;

?>


 

<?php $user=User::findOne(Yii::$app->user->id);if($user): ?>



 

    <script type="text/javascript">
        $(function(){
            $('#cancelorder').click(function(){
                //window.location = '<?php //echo Yii::app()->createUrl('projects/report');?>'
                parent.closeFrame();
                
            });
        });

    </script>
    <?php else:?>
    <script type="text/javascript">
        $(function(){
            $('#cancelorder').click(function(){
                //window.location = '<?php //echo Yii::app()->createUrl('procurement/index');?>'
                parent.closeFrame();
            });
        });

    </script>
<?php endif;?>
<style>
    .header, .homeurl{
        display:none;
    }
    .jumbotron{
        padding:0px;
    }
    .fonts{
        font-family: sans-serif;
        font-size: 17px;
    }
    label {
        display: inline-block;
        width: 8em;
    }
    .direct-work-order-cntnr label {
        display: block;
    }
    .purchase-order2-wrpr {
        text-align: left;
    }
</style>
<script type="text/javascript">

    $(document).on('focus','#dateofdelivery',function(){
        $(this).datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
    });
    $(document).on('focus','#date',function(){
        $(this).datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
    });
</script>
<script type="text/javascript">

    $(document).on('focus','#fromdate',function(){
        $(this).datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
    });
    $(document).on('focus','#todate',function(){
        $(this).datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
    })
</script>
<h1 style="display:none;">Approve <?php echo $type;?></h1>
<?php
/*if($order->order_type == 2) {
    $orderResources = OrderedResource::model()->findAll(array('condition'=>'order_id='.$order['order_id'].' '));
    $activityName = Jobcard::model()->findByPk($orderResources[0]->jobcard_id)->activity;
    */?><!--
    <h3 style="padding-bottom:30px;"><?/*= $activityName */?></h3>
--><?php /*} */?>
<body class="procurement" style="background:#fff !important">

<form method="POST" action="" id="placeorderform" >
    <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />

      <?php if($order['order_type']==1):?>
        <div class="purchase-order2-wrpr" style="padding:50px; padding-bottom:0px;">
	        <div class="row">
    	        <div class="col-md-6 col-xs-6">
    			    <h2 class="text-left pOrder-title"><?php echo $type;?></h2>
    			    <div class="text-left row">
    			        <!-- <div class="col-md-2">-->
    					<!--</div>-->
    					<div class="col-md-10">
    						<h3 class="grayBg commentshding" style="text-align:left;"><font color="#191818">Vendor</font></h3>
    						<div class="address">
    	                        <?php echo $vendor;?> <br/>
    		                    </b><?php echo $vendoraddress;?> ,<?php echo $vendorcity;?> <br/>
    	 
    	                        Ph:-<?php echo $vendorphone;?> <br/>
    		                    <!--Phone: (000) 000-0000 <br/>-->
    	                        <!--   Fax: (000) 000-0000 <br/>-->
                            </div>
    					</div>
    			    </div>
    		    </div>
    		    <div class="col-md-6 col-xs-6" style="text-align:left">
    			    <h3><font color="black">Geo Tech Construction Company Pvt Ltd</font></h3>
    			    <div class="address">
    				    8th Floor, KSHB Office Complex, <br/>
    			        Panampilly Nagar, Cochin, <br/>
    				    Ernakulam, Kerala 680036 <br/>
    				    <!--Fax: (000) 000-0000 <br/>-->
    				    Website: <a href="https://www.geotech.net.in/">www.geotech.net.in</a><br/><br/>
    			    </div>
    			    <div class="address">
                        <b>Date:</b> <?php echo date('d-m-Y');?><br/>
                        <div class="poNbr"><b>PO Number:</b> <input type="text" class="form-control small75" name="po_number" id="po_number" value=""> </div><br/>
                        <b>GSTIN:</b> 32AAFCG7358B1ZV <br/>
                    </div>
    		    </div>
	        </div>
    	    <div class="row">
                <div class="col-md-12 col-xs-12" style="text-align:left">
                    <div class="text-left row">
    		            <!-- <div class="col-md-2">-->
    					<!--</div>-->
    					<div class="col-md-12">
    						<h3 class="grayBg commentshding" style="text-align:left;"><font color="#191818">Description</font></h3>
    						<textarea class="form-control" id="specification" name="Specification" rows="3"><?php echo $order['specification'];?></textarea>
                            <span class="error"></span>
    					</div>
    		        </div>
                </div>
            </div>
    	    <div class="row" >
    	        <div class="col-md-5 col-xs-5" style="text-align:left">
    		        <h4 class="grayBg">Bill To</h4>
    		        <div class="address">
    		            <textarea  class="form-control" id="billing_to" name="billing_to" rows="7"><?php echo $order['billing_to'];?></textarea>
                        <span class="error"></span>
    		        </div>
    	        </div>
    	        <div class="col-md-1 col-xs-2"></div>
                <div class="col-md-6 col-xs-5" style="text-align:left">
                    <h4 class="grayBg">Ship To</h4>
    	            <div class="address">
    	                <textarea  class="form-control" id="billing_address" name="billing_address" <?php echo $class;?> rows="7"><?php echo $order['billing_address'];?></textarea>
                        <span class="error"></span>
    	            </div>
                </div>
            </div>
         <?php endif;?>
                <?php if($order['order_type']!=3):
                    if($order['order_type']==5):?>
                         <div class="purchase-order2-wrpr" style="padding:50px;">
                            <div class="col-md-12" >
                                <h2 class="pOrder-title" style="text-align: left;">Despatch Order</h2>
                            </div>
                            <?php echo  $datarows; ?>
                            <div class="row direct-work-order-cntnr">
                                <div class="col-md-4" style="text-align: left;">
                                    <label>Date</label>
                                   <!--  <input class="form-control date"  name="date[]" id="date" type="text" placeholder="Enter Date"   /> -->
                                   <?php echo $date;?>
                                </div>
                                <div class="col-md-4 " style="text-align: left;">
                                    <label>GSTN</label>
                                    <input class="form-control" type="text" value="32AAFCG7358B1ZV" placeholder="Enter GSTN"    disabled />
                                    <input type="hidden" name="ordertype" value="5">
                                </div>
                                <div class="col-md-4 ">
                                    <div style="height:13px; ">&nbsp;</div>
                                   <!--  <div class="center approve-cancel">
                                        <button type="button" value="Cancel" class="btn btn-primary cancel" id="cancelorder"><span class="icon-close"></span> Cancel</button>
                                        <button type="submit" value="Approve" class="btn btn-primary approve" id="approveorderbtn" name="Approve"><span class="icon-check"></span> Approve</button>
                                    </div> -->
                                </div>
                            </div>
                        </div>
                    <?php else:?>
                    <table class="table table-bordered ">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Item</th>
                                <th>Unit</th>
                                <th class="small75">Rate</th>
                                <th class="small75">Quantity</th>
                                <th class="small75">GST(%)</th>
                                <th class="small75">IGST(%)</th>
                                <th class="small75">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php echo $datarows;?>
                        </tbody>
                    </table>
                    <?php endif;?>
                <?php else:?>
                    <div class="purchase-order2-wrpr" style="padding:50px;">
                        <div class="col-md-12">
                           
                		    <h2 class="pOrder-title">Approve Direct Work Order</h2>
                	    </div>
                         <?php echo $activitydata;?>
                          <?php echo $datarows;?>
                    </div>
                   <!--<tr>-->
                    <!--    <th></th>-->
                    <!--    <th>Item</th>-->
                    <!--    <th>Unit</th>-->
                    <!--    <th>Rate</th>-->
                    <!--    <th>Working hours</th>-->
                    <!--    <th>No of Workers</th>-->
                    <!--    <th>No of Days</th>-->
                    <!--    <th>OT Rate</th>-->
                    <!--    <th>Amount</th>-->
                    <!--</tr>-->
                <?php endif;?>
         <?php if($order['order_type']==1):?>
        <div class="row">
	        <!--<div class="col-md-7 col-xs-7">-->
	        <!--	<h3 class="grayBg commentshding"><font color="black">Description</h3>-->
	        <!--<textarea class="form-control" id="specification" name="Specification" rows="5"></textarea>-->
            <!--    <span class="error"></span>-->
	        <!--</div>-->
	        <div class="col-md-1 col-xs-1"></div>
	        <div class="col-md-5 col-xs-12 pull pull-right" style="padding-left: 38px;">
                <div class="form-group">
                    <label for="sub_total" class="col-sm-5 control-label">SUB TOTAL</label>
                    <div class="col-sm-7">
                       <input type="text" class="form-control" id="apprsub_total" name="sub_total" readonly autocomplete="off" value="<?php echo $totalamount;?>">
                      <!--<input type="hidden" class="form-control" id="sub_total_value" name="sub_total_value" autocomplete="off">-->
                    </div>
                </div>
                <div class="form-group">
                    <label for="tax" class="col-sm-5 control-label">TAX</label>
                    <div class="col-sm-7">
                      <input type="text" class="form-control" id="apprtax" name="tax" readonly autocomplete="off" value="<?php echo $totalgstamount;?>">  
                    </div>
                </div>
                <div class="form-group">
                    <label for="total" class="col-sm-5 control-label">Freight</label>
                    <div class="col-sm-7">
                      <input type="text" class="form-control" id="apprfreight" name="freight" readonly autocomplete="off" value="<?php echo $order['freight'];?>">
                      <!--<input type="hidden" class="form-control" id="net_total_value" name="net_total_value" autocomplete="off">-->
                    </div>
                </div>
                <div class="form-group">
                    <label for="total" class="col-sm-5 control-label">Insurance</label>
                    <div class="col-sm-7">
                      <input type="text" class="form-control" id="apprinsurance" name="insurance" readonly autocomplete="off" value="<?php echo $order['insurance'];?>">
                      <!--<input type="hidden" class="form-control" id="net_total_value" name="net_total_value" autocomplete="off">-->
                    </div>
                </div>
                <div class="form-group">
                    <label for="total" class="col-sm-5 control-label">Others</label>
                    <div class="col-sm-7">
                      <input type="text" class="form-control" id="approthers" name="others" readonly autocomplete="off" value="<?php echo $order['others'];?>">
                      <!--<input type="hidden" class="form-control" id="net_total_value" name="net_total_value" autocomplete="off">-->
                    </div>
                </div>
                <div class="form-group">
                    <label for="total" class="col-sm-5 control-label">TOTAL</label>
                    <div class="col-sm-7">
                        <?php $grandtotal=$total + $order['freight'] + $order['insurance'] + $order['others'];?>
                      <input type="text" class="form-control" id="apprtotal" name="total" readonly autocomplete="off" value="<?php echo $grandtotal;?>">
                      <!--<input type="hidden" class="form-control" id="net_total_value" name="net_total_value" autocomplete="off">-->
                    </div>
                </div>
            </div>
        </div>
    </div>
     <?php endif;?>
    <table>
        <tbody>
        <?php if($order['status']==2):$class="disabled";else:$class='';endif;?>
        <?php if($order['order_type']==1):?>
            <div class="col-md-12">
                <div class="approveForm row">
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-md-12">
                                <label>Contact Person</label>
                                <input type="text" class="form-control" name="Contact"  <?php echo $class;?>value="<?php echo $order['contact'];?>">
                                <input type="hidden" name="date" id="date" value="<?php echo date('d-m-Y',strtotime($order['date']));?>">
                                <input type="hidden" name="ordertype" value="1">
                            </div>
                            
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="row">
                            <div class="col-md-12">
                                <label>Mode of Payment</label>
                                <input type="text" class="form-control" id="payment" name="Payment" <?php echo $class;?> value="<?php echo $order['payment'];?>">
                                <span class="error"></span>
                            </div>
                            
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-md-12">
                                <label>Advance</label>
                                <input type="text" class="form-control" id="advance" name="Advance" <?php echo $class;?> value="<?php echo $order['advance'];?>">
                                <span class="error"></span>
                            </div>
                            
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-md-12">
                                <label>Date of Delivery </label>
                                <input type="text" class="form-control" <?php echo $class;?> name="dateofdelivery" id="dateofdelivery" value="<?php echo date('d-m-Y',strtotime($order['deliverydate']));?>">
                            </div>
                            
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-md-12">
                                <label>Place of Delivery</label>
                                <input type="text" class="form-control" <?php echo $class;?> id="place" name="Place" value="<?php echo $order['place'];?>">
                                <span class="error"></span>
                            </div>
                            
                        </div>
                        
                    </div>
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-md-12">
                                <label>Terms and conditions     </label>
                                <select class="form-control" <?php echo $class;?> name="terms">
                                    <option value="none">Select Terms and conditions</option>


                                  
                                    

                                  <?php $ids= $this->context->getfolderids(); ?> 

                                  <?php
                                  // $docuements=Drawings::model()->findAll(array('condition'=>'folder IN ('.$ids.') '));

                                             $docuements=Drawings::find()->Where(['IN', 'folder', $ids])->all();
                             

                                    foreach($docuements AS $docuement):
                                        if($docuement['drawings_id']==$order['terms']):
                                            $selected='selected';
                                        else:
                                            $selected='';
                                        endif;
                                        echo '<option value="'.$docuement['drawings_id'].'" '.$selected.'>'.$docuement['tittle'].'</option>';
                                    endforeach;?>
                                </select>
                            </div>
                            
                        </div>
                        
                    </div>
                   
                </div>
            </div>
        <?php elseif($order['order_type']==2):?>



              <div class="col-md-12">
                <div class="approveForm row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Date</label>
                                <input type="text" class="form-control" name="date" id="date" <?php echo $class;?> value="<?php echo date('d-m-Y',strtotime($order['date']));?>">
                                
                            </div>
                            <div class="col-md-6">
                                <label>Specification</label>
                                <input type="text" class="form-control" id="specification" name="Specification" <?php echo $class;?> value="<?php echo $order['specification'];?>">
                    <span class="error"></span>
                                
                            </div>
                        </div>
                        
                      </div>



            <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Contract Period</label>
                                <input type="text" class="form-control" id="period" name="Period" <?php echo $class;?> value="<?php echo $order['Period'];?>">
                                
                            </div>
                            <div class="col-md-6">
                                <label>Advance</label>
                               <input type="text" class="form-control" id="advance" name="Advance" <?php echo $class;?> value="<?php echo $order['advance'];?>">
                    <span class="error"></span>
                                
                            </div>
                        </div>
                        
                      </div>



              <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Retention (%)</label>
                                <input type="text" class="form-control" id="retention" name="Retention" <?php echo $class;?> value="<?php echo $order['Retention'];?>">
                    <span class="error"></span>
                                
                            </div>
                            <div class="col-md-6">
                                <label>Mode of Payment</label>
                               <input type="text" class="form-control" name="Payment" id="payment" <?php echo $class;?> value="<?php echo $order['payment'];?>">
                    <input type="hidden" name="ordertype" value="2">
                                
                            </div>
                        </div>
                        
                      </div>




             <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Terms and conditions</label>
                                <select class="form-control" <?php echo $class;?> name="terms">
                        <option value="none">Select Terms and conditions</option>


                        <?php
                         //$ids=$this->getfolderids();?>
                        <?php 
                        //$docuements=Drawings::model()->findAll(array('condition'=>'folder IN ('.$ids.') '));

                        $ids= $this->context->getfolderids(); ?>

                         <?php
                         // $docuements=Drawings::model()->findAll(array('condition'=>'folder IN ('.$ids.') '));

                                             $docuements=Drawings::find()->Where(['IN', 'folder', $ids])->all();


                        foreach($docuements AS $docuement):
                            if($docuement['drawings_id']==$order['terms']):
                                $selected='selected';
                            else:
                                $selected='';
                            endif;
                            echo '<option value="'.$docuement['drawings_id'].'" '.$selected.'>'.$docuement['tittle'].'</option>';
                        endforeach;?>
                    </select>
                                
                            </div>
                           
                        </div>
                        
                      </div>




               </div></div>




        <?php elseif($order['order_type']==3):?>

        <?php elseif($order['order_type']==5):?>


           
            <!--<div class="col-md-12">-->
            <!--    <div class="approveForm row">-->
            <!--        <div class="col-md-6">-->
            <!--            <div class="row">-->
            <!--                <div class="col-md-6">-->
            <!--                    <label>GSTn No</label>-->
            <!--                   <input type="text" class="form-control" <//?php echo //$class;?> name="gstn_no" value="<//?php echo// $order['gstn_no'];?>" readonly>-->
            <!--        <input type="hidden" name="ordertype" value="5">-->
                                
            <!--                </div>-->
                           
            <!--            </div>-->
                        
            <!--          </div>-->


            <!-- </div></div>-->



          
        <?php else:?>


         
          
            <div class="col-md-12">
                <div class="approveForm row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Specification</label>
                               <input type="text" class="form-control" id="specification" name="Specification" <?php echo $class;?> value="<?php echo $order['specification'];?>">
                    <span class="error"></span>
                                
                            </div>
                           

                            <div class="col-md-6">
                                <label>Advance</label>
                                <input type="text" class="form-control" id="advance" name="Advance" <?php echo $class;?> value="<?php echo $order['advance'];?>">
                    <span class="error"></span>
                                
                            </div>

                        </div>
                        
                      </div>


              <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Period of Lease</label>
                                <input type="text" class="form-control" id="leaseperiod" name="Leaseperiod" <?php echo $class;?> value="<?php echo $order['Leaseperiod'];?>">
                    <span class="error"></span>
                                
                            </div>
                           

                            <div class="col-md-6">
                                <label>From</label>
                               <input type="text" class="form-control" name="fromdate" id="fromdate" <?php echo $class;?> value="<?php echo date('d-m-Y',strtotime($order['fromdate']));?>">
                    <span class="error"></span>
                                
                            </div>

                        </div>
                        
                      </div>


              <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <label>To</label>
                               <input type="text" class="form-control" name="todate" id="todate" <?php echo $class;?> value="<?php echo date('d-m-Y',strtotime($order['todate']));?>">
                                
                            </div>
                           

                            <div class="col-md-6">
                                <label>Contact Person</label>
                               <input type="text" class="form-control" <?php echo $class;?> value="<?php echo $order['contact'];?>" name="Contact">
                    <input type="hidden" name="ordertype" value="4">
                                
                            </div>

                        </div>
                        
                      </div>


            <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Mode of payment</label>
                                <input type="text" class="form-control" id="payment" name="Payment" <?php echo $class;?> value="<?php echo $order['payment'];?>">
                    <span class="error"></span>
                  <!--  <input type="hidden" name="ordertype" value="5"> -->
                                
                            </div>
                           

                            <div class="col-md-6">
                                <label>Date</label>
                               <input type="text" class="form-control" name="date" id="date" <?php echo $class;?> value="<?php echo date('d-m-Y',strtotime($order['date']));?>">
                                
                            </div>

                        </div>
                        
                      </div>

                       <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Terms and conditions</label>
                              <select class="form-control" <?php echo $class;?> name="terms">
                        <option value="none">Select Terms and conditions</option>


                        <?php
                         //$ids=$this->getfolderids();?>

                        <?php $ids= $this->context->getfolderids(); ?>

                        <?php
                        // $docuements=Drawings::model()->findAll(array('condition'=>'folder IN ('.$ids.') '));

                                             $docuements=Drawings::find()->Where(['IN', 'folder', $ids])->all();

                       


                        foreach($docuements AS $docuement):
                            if($docuement['drawings_id']==$order['terms']):
                                $selected='selected';
                            else:
                                $selected='';
                            endif;
                            echo '<option value="'.$docuement['drawings_id'].'" '.$selected.'>'.$docuement['tittle'].'</option>';
                        endforeach;?>
                    </select>
                                
                            </div>
                           

                           

                        </div>
                        
                      </div>



             </div></div>

            <?php endif;?>


       
        <?php if($order['status']==2):?>
            <?php if($order['order_type']!=3):?>
                <div class="col-md-12 text-center approve-cancel">
                    <button type="button" value="Cancel" class="btn btn-primary cancel" id="cancelorder"><span class="icon-close"></span> Cancel</button>
                </div>
            <?php else:?>
                <div class="col-md-12 text-center approve-cancel">
                    <button><input type="button" value="Cancel" class="btn btn-primary" id="cancelorder"><span class="icon-close"></span> Cancel</button>
                </div>
            <?php endif;?>
        <?php else:?>
            <?php if($order['order_type']!=3):?>
                <div class="col-md-12 text-center approve-cancel">
                    <button type="button" value="Cancel" class="btn btn-primary approve" id="cancelorder"><span class="icon-close"></span> Cancel</button>
                    <button type="submit" value="Approve" class="btn btn-primary cancel" id="approveorderbtn" name="Approve"><span class="icon-check"></span> Approve</button>
                </div>
                <?php else:?>
                <div class="col-md-12 text-center approve-cancel">  
                    <button type="button" value="Cancel" class="btn btn-primary cancel" id="cancelorder"><span class="icon-close"></span> Cancel</button>
                    <button type="submit" value="Approve" class="btn btn-primary approve" id="approveorderbtn" name="Approve"><span class="icon-check"></span> Approve</button>
                </div>
            <?php endif;?>
        <?php endif;?>
        </tbody>
    </table>
</form>
</body>

<script type="text/javascript">
    $(document).on('click','#approveorderbtn',function(){
        var abc;
        var error=0;
        $('.error').hide();
        if($('#specification').val()=='')
        {
            $('#specification').next("span").html('Enter Specification').show('slow');
            error=1;
        }
        if($('#advance').val()=='')
        {
            $('#advance').next("span").html('Enter Advance').show('slow');
            error=1;
        }
        if(!$.isNumeric($('#advance').val()))
        {
            $('#advance').next("span").html('Enter Valid Amount').show('slow');
            error=1;
        }
        if($('#payment').val()=='')
        {
            $('#payment').next("span").html('Enter Mode of payment').show('slow');
            error=1;
        }
        if($('#place').val()=='')
        {
            $('#place').next("span").html('Enter Place of Delivery').show('slow');
            error=1;
        }
        if($('#cgst').val()=='' && $('#igst').val()==''){
            alert("Please enter either GST / IGST tax.");
            error=1;
        }
        /*if($('#cgst').val()=='0' && $('#igst').val()=='0'){
            alert("Please enter valid  GST / IGST tax.");
            error=1;
        }*/
        if (typeof($('#cgst').val()) == "undefined") {
            error=0;
        }
        else {
            if($('#cgst').val()!=''){ 
                if($('#igst').val()!=''){
                    alert("You can not select both IGST as well as the other tax.");
                    error=1;
                }
            }
        }


        if(error==0){   
            setTimeout(function(){
                window.onunload = function (e) {  
                   // opener.refreshConfirmWindow();  
                };
                parent.closeFrame();
                return true;
            },500);
        }
        else{
            //alert("You have to enter all values for reporting");
            return  false;
        }
    });
    
    
   $(document).ready(function() {
    //this calculates values automatically 
    //subAmount();
    $("#sub_total, #tax").on("keydown keyup", function() {
        //subAmount();
    });
});


function subAmount() {
            var num1 = document.getElementById('sub_total').value;
            var num2 = document.getElementById('tax').value;
            var result = parseInt(num1) + parseInt(num2);
            //var result1 = parseInt(num2) - parseInt(num1);
            if (!isNaN(result)) {
                document.getElementById('total').value = result;
                //document.getElementById('subt').value = result1;
            }
        }


$('#cgst').keyup(function(ev) {  
    var amount = $('#apprsub_total').val();
    //var qty = $('#qty').val();
    var gst = $('#cgst').val();
    //$(this).val(); 
    //var total = amount;
    var finaltax = (amount * gst / 100);  
    console.log(finaltax)
    //var finaltax= parseFloat(tot_price).toFixed(2);  
    
    $('#apprtax').val(finaltax);
   


    //alert (finaltotal);
    //var finaltotal=  finaltax; 
     //var subtotal = amount;  
     var amountincgst= Number(amount) + Number(finaltax); 
     //var  final=parseFloat(finalnumber).toFixed(2);  

    $('#apprtotal').val(amountincgst);  // alert(final);

  });



 //if($('#igst').val()!=''){
$('#igst').keyup(function(ev) {  
    var amount = $('#apprsub_total').val();
    //var qty = $('#qty').val();
    var igst = $('#igst').val();
    //$(this).val(); 
    //var total = amount;
    var finaltax = (amount * igst / 100);

   //var finaltax= parseFloat(tot_price).toFixed(2);  
    
    $('#apprtax').val(finaltax);
   

   //var finaltotal=  finaltax; 
     //var subtotal = amount;  
     var amountincgst= Number(amount) + Number(finaltax); 
     //var  final=parseFloat(finalnumber).toFixed(2);  

    $('#apprtotal').val(amountincgst);  // alert(final);
    
  });

    
    
</script>