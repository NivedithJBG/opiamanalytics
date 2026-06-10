
<?php

use app\models\Drawings;  
use app\models\OrderedResource;  
use app\models\Jobcard;        
use app\models\WorkgroupsNew;
use amnah\yii2\user\models\User;
use app\models\TermsCondtns;

?>

<?php $user=User::findOne(Yii::$app->user->id);if($user): ?>

<?php //$user=User::model()->active()->findbyPk(Yii::app()->user->id);if($user['superuser']==5): ?>
    <script type="text/javascript">
        $(function(){
            $('#cancelorder').click(function(){
               // window.location = '<?php //echo Yii::app()->createUrl('projects/report');?>'
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
    
</style>
<script type="text/javascript">
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
if($order->order_type == 2) {

    $orderResources=OrderedResource::find()->Where(['order_id'=>$order['order_id']])->all();

    if($orderResources){

        $activityNames=Jobcard::findOne($orderResources[0]->jobcard_id); 
        $activityName=$activityNames['activity'];
        $iowids=Jobcard::findOne($orderResources[0]->jobcard_id); 
        $iowid=$iowids['iow'];
        $iownames=WorkgroupsNew::findOne($iowid); 
        $iowname=$iownames['Name'];

    }
    else{
        $iowname='';
    }

    ?>
    <h3 style="padding-bottom:30px;display:none"><?= $iowname ?></h3>
<?php } ?>



<body class="procurement" style="background:#fff !important">
 <form method="POST" action="" id="placeorderform" >

  <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
        <div class="purchase-order2-wrpr" style="padding:50px; padding-bottom:0px;">
            <div class="row">
                <div class="col-md-6 col-xs-6">
                    <h2 class="text-left pOrder-title"><?php echo $type;?></h2>
                    <div class="text-left row">
                        
                        <div class="col-md-10">
                            <h3 class="grayBg commentshding" style="text-align:left;"><font color="#191818">Vendor</font></h3>
                            <div class="address">
                                <?php echo $vendor;?> <br/>
                                </b><?php echo $vendoraddress;?> ,<?php echo $vendorcity;?> <br/>
         
                                Ph:-<?php echo $vendorphone;?> <br/>
                                
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
                        Website: <a href="https://www.geotech.net.in/" target="_blank">www.geotech.net.in</a><br/><br/>
                    </div>

                     <?php if($order['order_type']==2):?>
                         <div class="address">
                        <b>Date:</b> <?php echo date('d-m-Y');?><br/>
                        <div class="poNbr"><b>WO Number:</b> <input type="text" class="form-control small75" name="lo_number" id="lo_number" value=""> </div><br/>
                        <b>GSTIN:</b> 32AAFCG7358B1ZV <br/>
                    </div>
                    <?php else:?>
                    
                    <div class="address">
                        <b>Date:</b> <?php echo date('d-m-Y');?><br/>
                        <div class="poNbr"><b>LO Number:</b> <input type="text" class="form-control small75" name="lo_number" id="lo_number" value=""> </div><br/>
                        <b>GSTIN:</b> 32AAFCG7358B1ZV <br/>
                    </div>

                     <?php endif;?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-xs-12" style="text-align:left">
                    <div class="text-left row">
                        
                        <div class="col-md-12">
                            <h3 class="grayBg commentshding" style="text-align:left;"><font color="#191818">Description</font></h3>
                            <textarea class="form-control" id="specification" name="Specification" rows="3"><?php echo $order['specification'];?></textarea>
                        <span class="error"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
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
                    </div>
                </div>
            </div>
            <table class="table table-bordered "  style="margin:30px 0">   
        <thead>
        <?php if($order['order_type']!=3):
            if($order['order_type']==5):?>
                <tr>
                    <th>Date</th>
                    <th colspan="2">Item</th>
                    <th>Move From</th>
                    <th>Move To</th>
                    <!--<th>Vehicle No</th>-->
                </tr>
            <?php else:?>
                 <tr>
                        <th>Sl.No</th>
                        <th>Item</th>
                        <th>Unit</th>
                        <th>Rate</th>
                        <th>Qnty</th>
                        <!-- <th>GST(%)</th>
                        <th>IGST(%)</th> -->
                        <th width="15%" >Amount</th>
                    </tr>
            <?php endif;?>
        <?php else:?>
            <tr>
                <th></th>
                <th>Item</th>
                <th>Unit</th>
                <th>Rate</th>
                <th>Quantity</th>
                <th>Amount</th>
                
            </tr>
        <?php endif;?>
        </thead>
        <tbody>
            <?php echo $datarows;?>
        </tbody>
    </table>
            <div class="row">
                
                <div class="col-md-1 col-xs-1"></div>
                <div class="col-md-5 col-xs-12 pull pull-right subtotalWrpr" style="padding-left: 50px;">
                    <div class="form-group">
                        <label for="sub_total" class="col-sm-5 control-label">SUB TOTAL</label>
                        <div class="col-sm-7">
                          <input type="text" class="form-control" id="apprsub_total" name="sub_total" disabled autocomplete="off" value="<?php echo $totalamount;?>">
                          
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="others" class="col-sm-5 control-label">Others</label>
                        <div class="col-sm-7">
                          <input type="text" class="form-control" id="others" name="others" placeholder="Others" autocomplete="off" value="<?php echo $order['others'];?>">  
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="total" class="col-sm-5 control-label">TOTAL</label>
                        <div class="col-sm-7">
                             <?php $grandtotal=$total + $order['freight'] + $order['insurance'] + $order['others'];?>
                           <input type="text" class="form-control" id="apprtotal" name="total" readonly autocomplete="off" value="<?php echo $grandtotal;?>">
                          
                        </div>
                    </div>  
                </div>
                
            </div>
        </div></div>
   <!--  </form> -->





<form method="POST" action="" id="placeorderform">
     <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
    
   <table>
        <tbody>
        <?php if($order['status']==2):$class="disabled";else:$class='';endif;?>
        <?php if($order['order_type']==2):?>


        

         <div class="col-md-12">
                <div class="approveForm row">
                    <div class="col-md-6">
                        <div class="row">
                          
                            <div class="col-md-6">
                                <label>Contract Period</label>
                                <input type="text" class="form-control" id="period" name="Period" <?php echo $class;?> value="<?php echo $order['Period'];?>">
                                <input type="hidden" class="form-control" name="date" id="date" value="<?php echo date('d-m-Y');?>">
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
                                        <label>Retention (%)</label>
                                        <input type="text" class="form-control" id="retention" name="Retention" <?php echo $class;?> value="<?php echo $order['Retention'];?>">
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
                                
                                   
                                    <div class="col-md-6" style="text-align:left">
                                        <label>IGST/SGST</label>
                                        <input type="radio" name="gsttype" value="1" <?php echo ($gsttype=='1'?'checked="checked"':'');?> >SGST
                <input type="radio" name="gsttype" value="2" <?php echo ($gsttype=='2'?'checked="checked"':'');?> >IGST
                                    </div>

                                  <div class="col-md-6">
                                        <label>GST</label>
                                        <input type="text" class="form-control" name="gst" id="gst" <?php echo $class;?> value="<?php echo $gstper;?>">
                <span class="error"></span>
                                    </div>




                                    
                                </div>
                        
                    </div>


          

                <div class="col-md-6">
                    <div class="row"> 
                         <div class="col-md-12"> 
                            <?php 
                            $ids= $this->context->getfolderids();
                            $docuements=Drawings::find()->Where(['IN', 'folder', $ids])->all();
                            if($order['tcStatus'] == 0){
                                $tandcview = TermsCondtns::find()->where('Status=0')->andWhere(['id'=>$order['tcContent']])->one();
                                if(!empty($tandcview)){
                                    $Tandcid = $tandcview->id;
                                    $Tandcc = $tandcview->title;
                                    $Tandccc = $tandcview->content;
                                }else{
                                    $Tandcc = '';
                                    $Tandccc = '';
                                }
                            }elseif($order['tcStatus'] == 1){
                                    
                                $Tandcid = $order['order_id '];
                                $Tandcc = 'Updated T & C';
                            }
                            ?>
                            <label>Terms and conditions </label>
                            <div class="TandC">    
                                <a class="tandcupdate_<?= $order['tcStatus']; ?>" style="font-weight: bold;"><?= $Tandcc; ?></a>
                            </div>
                            <?php if(!empty($docuements)){ ?>
                            <select class="form-control" name="documents">
                                <?php 
                                //$docuements=Drawings::model()->findAll(array('condition'=>'folder IN ('.$ids.') '));
                                foreach($docuements AS $docuement):
                                    echo '<option value="'.$docuement['drawings_id'].'">'.$docuement['tittle'].'</option>';
                                endforeach;?>
                            </select>
                            <?php } ?>
                        </div>         
                    </div>    
                </div>
                <div class="col-md-12 text-center" style="margin-top:20px;">
                    <div class="tandcorigin" style="display:none;">
                        <?= $Tandccc; ?>
                    </div>
                    <div class="tandcupdated" style="display:none;">
                        <?= $order['tcContent']; ?>
                    </div>
                </div><br>




                </div></div>        <!-- newly added -->

               <?php else:?>


                  <div class="col-md-12">
                <div class="approveForm row">

                 <div class="col-md-6">
                        <div class="row">
                               
                                <div class="col-md-6">
                                <label>Contact Person</label>
                                <input type="text" class="form-control" name="Contact" <?php echo $class;?>value="<?php echo $order['contact'];?>">
                                <input type="hidden" name="ordertype" value="4">
                                <input type="hidden" class="form-control" name="date" id="date" value="<?php echo date('d-m-Y');?>">
                              </div>

                               <div class="col-md-6">
                                <label>Period of Lease</label>
                                <input type="text" class="form-control" id="leaseperiod" name="Leaseperiod" <?php echo $class;?> value="<?php echo $order['Leaseperiod'];?>">
                             <span class="error"></span>
                                
                                
                            </div>
                                    
                                    
                                    
                                </div>
                        
                    </div>



                <div class="col-md-6">
                        <div class="row">
                                
                                   <div class="col-md-6">
                                <label>TDS (%)</label>
                                <input type="text" class="form-control" id="tdspercent" name="tdspercent" <?php echo $class;?> value="<?php echo $order['tdsper'];?>">
                <span class="error"></span>
                                
                            </div>
                                     <div class="col-md-6">
                                <label>From</label>
                                <input type="text" class="form-control" name="fromdate" id="fromdate"  <?php echo $class;?> value="<?php echo date('d-m-Y',strtotime($order['fromdate']));?>">
                <span class="error"></span>
                                
                                
                            </div>
                                    
                                </div>
                        
                    </div>




         <div class="col-md-6">
                        <div class="row">
                                
                                    <div class="col-md-6">
                                <label>To</label>
                               <input type="text" class="form-control" name="todate" id="todate"  <?php echo $class;?> value="<?php echo date('d-m-Y',strtotime($order['todate']));?>">
                <input type="hidden" name="ordertype" value="4">
                                
                            </div>
                                     <div class="col-md-6">
                                        <label>Mode of Payment</label>
                                        <input type="text" class="form-control" id="payment" name="Payment" <?php echo $class;?> value="<?php echo $order['payment'];?>">
                                        <span class="error"></span>
 
                                    </div>
  
                                </div>
                        
                    </div>


              <div class="col-md-6">
                        <div class="row">
                                
                                    
                        <div class="col-md-6" style="text-align:left">
                    <label>SGST/IGST</label>
                  <input type="radio" name="gsttype" value="1" <?php echo ($gsttype=='1'?'checked="checked"':'');?> >SGST
                    <input type="radio" name="gsttype" value="2" <?php echo ($gsttype=='2'?'checked="checked"':'');?> >IGST

                                    </div> 
                                     <div class="col-md-6">
                                  <label>GST</label>     
                <input type="text" class="form-control" name="gst" id="gst" <?php echo $class;?> value="<?php echo $gstper;?>">
                <span class="error"></span>  
                                    </div> 

                                </div>
                        
                    </div>


                <div class="col-md-6">
                        <div class="row">
                                
                                   <div class="col-md-6">
                                        <label>Advance</label>
                                        <input type="text" class="form-control" id="advance" name="Advance" <?php echo $class;?> value="<?php echo $order['advance'];?>">
                                        <span class="error"></span>
                                    </div>
                                    <div class="col-md-6">
                                <label>Terms and conditions     </label>
                                <select class="form-control" <?php echo $class;?> name="terms">
                                    <option value="none">Select Terms and conditions</option>


                                    <?php 
                                    
                                     $ids= $this->context->getfolderids(); ?>

                                      <?php
                                      
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



                  </div><br><br>
  


                  <?php endif;?>
        <?php if($order['status']==2):?>


             <div class="col-md-12 text-center approve-cancel">
                    
                    <button type="button" value="Cancel" class="btn btn-primary approve" id="cancelorder"><span class="icon-close"></span> Cancel</button>
                    <button type="submit" value="Approve" class="btn btn-primary cancel" id="approveorderbtn" name="Approve"><span class="icon-check"></span> Approve</button>
                </div>

                
        <?php else:?>

                 <div class="col-md-12 text-center approve-cancel">
                    
                    <button type="button" value="Cancel" class="btn btn-primary approve" id="cancelorder"><span class="icon-close"></span> Cancel</button>
                    <button type="submit" value="Approve" class="btn btn-primary cancel" id="approveorderbtn" name="Approve"><span class="icon-check"></span> Approve</button>
                </div>

               
             <?php endif;?>
 
                   </div></div>

        </tbody>
    </table>

</form>
</body>
<script type="text/javascript">
    $(document).on('click','#approveorderbtn',function(){
        var test='1';
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

        if(error==0){
            setTimeout(function(){
                parent.closeFrame();
                return true;
            },500);
            //return true;
             //parent.closeFrame();
        }
        else{
            ////alert("You have to enter all values for reporting");
            return  false;
        }
    });


    $(document).ready(function(){

        $(".tandcupdate_0").click(function(){  
            $('.tandcorigin').toggle();
        });
       
    });





    
</script>

