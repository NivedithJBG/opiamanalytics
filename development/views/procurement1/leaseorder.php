
<?php

use app\models\Drawings;

?>

<style>
    .header, .homeurl{
        display:none;
    }
    .container-fluid {
            padding: 0;
        }
        
        .jumbotron {
            padding: 0;
        }
    .approveHdrl {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 80px;
    padding: 0;
    }  
    .approveHdrl h1{
        margin:0px;
        padding-bottom:0px !important;
            font-size: 23px;
    }

     .fonts{
        font-family: sans-serif;
        font-size: 17px;
    }
    
</style>


<body class="procurement" style="background:#fff !important">

    <div class="approveHdrl" style="display:none;"><h1>Lease Order </h1></div>
    <form method="POST" action="" id="placeorderform" >
        <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
        <div class="purchase-order2-wrpr" style="padding:50px; padding-bottom:0px;">

            <div class="row">
                <div class="col-md-6 col-xs-6">
                    <h2 class="text-left pOrder-title">Lease Order</h2>
                    <div class="text-left row">
                        <!-- <div class="col-md-2">-->
                        <!--</div>-->
                        <div class="col-md-10">
                            <h3 class="grayBg commentshding" style="text-align:left;"><font color="#191818">Vendor</font></h3>
                            <div class="address">
                                <?php echo $vendor;?> <br/>
                                </b><?php echo $address;?> ,<?php echo $city;?> <br/>
         
                                Ph:-<?php echo $phone;?> <br/>
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
                        Website: <a href="https://www.geotech.net.in/" target="_blank">www.geotech.net.in</a><br/><br/>
                    </div>
                    <div class="address">
                        <b>Date:</b> <?php echo date('d-m-Y');?><br/>
                        <div class="poNbr"><b>LO Number:</b> <input type="text" class="form-control small75" name="po_number" id="po_number" value=""> </div><br/>
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
                            <textarea class="form-control" id="specification" name="Specification"></textarea>
                            <span class="error"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-5 col-xs-5" style="text-align:left">
                    <h4 class="grayBg">Bill To</h4>
                    <div class="address">
                        <textarea  class="form-control" id="billing_to" name="billing_to" rows="7">Geo Tech Construction Company Pvt Ltd,8th Floor, KSHB Office Complex,Panampilly Nagar, Cochin,Ernakulam, Kerala 680036</textarea>
                        <span class="error"></span>
                    </div>
                </div>
                <div class="col-md-1 col-xs-2"></div>
                <div class="col-md-6 col-xs-5" style="text-align:left">
                    <h4 class="grayBg">Ship To</h4>
                    <div class="address">
                        <textarea  class="form-control" id="billing_address" name="billing_address" rows="7"><?php echo $Projects;?></textarea>
                        <span class="error"></span>
                    </div>
                </div>
            </div>

            <table class="table table-bordered" style="margin:30px 0" >
                <thead>
                    <tr>
                        <th>Sl.No</th>
                        <th>Item</th>
                        <th>Unit</th>
                        <th>Rate</th>
                        <th>Qnty</th>
                        <!--<th>GST(%)</th>-->
                        <!--<th>IGST(%)</th>-->
                        <th width="15%" >Amount</th>
                    </tr>
                </thead>
                <tbody class="tblAmountbody">
                    <?php echo $datarows;?>
                </tbody>
            </table>

            <div class="row">
                <div class="col-md-1 col-xs-1"></div>
                <div class="col-md-5 col-xs-12 pull pull-right subtotalWrpr" style="padding-left: 50px;">
                    <div class="form-group">
                        <label for="sub_total" class="col-sm-5 control-label">SUB TOTAL</label>
                        <div class="col-sm-7">
                          <input type="text" class="form-control" id="sub_total" name="sub_total" disabled autocomplete="off" value="<?php echo $ttl;?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="others" class="col-sm-5 control-label">Others</label>
                        <div class="col-sm-7">
                          <input type="text" class="form-control" id="others" name="others" placeholder="Others" autocomplete="off">  
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="total" class="col-sm-5 control-label">TOTAL</label>
                        <div class="col-sm-7">
                          <input type="text" class="form-control total" id="total" name="total" disabled autocomplete="off" value="<?php echo $amt;?>">
                        </div>
                    </div>  
                </div>      
            </div>

        </div>
       <!--  </form> -->

            
        <div class="col-md-12">
            <div class="approveForm row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Contact Person</label>
                            <input type="text" class="form-control" name="Contact">
                            <input type="hidden" name="ordertype" value="4">
                           <!--   <input type="hidden" name="ordertype" value="1"> -->
                            <input type="hidden" class="form-control" name="date" id="date" value="<?php echo date('d-m-Y');?>">
                        </div>
                               
                        <div class="col-md-6">
                            <label>Period of Lease</label>
                            <input type="text" class="form-control" id="leaseperiod" name="Leaseperiod">
                            <span class="error"></span>                             
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <label>TDS (%)</label>
                            <input type="text" class="form-control" id="tdspercent" name="tdspercent">
                            <span class="error"></span>     
                        </div>

                        <div class="col-md-6">
                            <label>From</label>
                            <input type="text" class="form-control" name="fromdate" id="fromdate" value="<?php echo date('d-m-Y');?>">
                            <span class="error"></span>   
                        </div>

                    </div>
                </div>

                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <label>To</label>
                            <input type="text" class="form-control" name="todate" id="todate" value="<?php echo date('d-m-Y');?>">
                            <input type="hidden" name="ordertype" value="4">                               
                        </div>

                        <div class="col-md-6">
                            <label>Mode of Payment</label>
                            <input type="text" class="form-control" id="payment" name="Payment">
                            <span class="error"></span> 
                        </div>

                    </div>

                </div>

                <div class="col-md-6">
                    <div class="row">            
                        <div class="col-md-6" style="text-align:left">
                            <label>SGST/IGST</label>
                            <input type="radio" name="gsttype" value="SGST">SGST
                            <input type="radio" name="gsttype" value="IGST">IGST
                        </div> 
                        <div class="col-md-6">
                            <label>GST</label>     
                            <input type="text" class="form-control" name="gst" id="gst">
                            <span class="error"></span>  
                        </div> 
                    </div>    
                </div>

                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Advance</label>
                            <input type="text" class="form-control" id="advance" name="Advance">
                            <span class="error"></span>
                        </div>
                        <div class="col-md-6">
                            <label>Terms and conditions     </label>
                            <select class="form-control" name="terms">
                                <option value="none">Select Terms and conditions</option>
                                                    
                                <?php $ids= $this->context->getfolderids(); ?>

                                <?php $docuements=Drawings::find()->Where(['in', 'folder', $ids])->all();

                                    if($docuements){
                                        foreach($docuements AS $docuement):

                                            echo '<option value="'.$docuement->drawings_id.'">'.$docuement->tittle.'</option>';
                                        endforeach;
                                     }
                                ?>

                            </select>
                        </div>                     

                    </div>
                            
                </div>

                <div class="col-md-12 text-center approve-cancel" style="margin-top:30px;">
                    <button type="button" value="Cancel" class="btn btn-primary cancel" id="cancelorder"><span class="icon-close"></span> Cancel</button>
                    <button type="submit" value="Submit" id="leaseorderbtn" class="btn btn-primary approve" name="submit"><span class="icon-check"></span> Submit</button>
                </div>                  
                            
            </div>
        </div>     
        
    </form>
</body>

<script type="text/javascript">
    $(function(){
        $(document).on('click','#cancelorder',function(){  
            
            setTimeout(function(){
                parent.closeFrame2();
                    
            },500);            
            
        });
    });

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
    });
    $(document).on('focus','#date',function(){
        $(this).datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
    });

    $(document).on('click','#leaseorderbtn',function(){
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
        if($('#leaseperiod').val()=='')
        {
            $('#leaseperiod').next("span").html('Enter Period of Lease ').show('slow');
            error=1;
        }
        if($('#payment').val()=='')
        {
            $('#payment').next("span").html('Enter Mode of payment').show('slow');
            error=1;
        }

        if(error==0){
            setTimeout(function(){
                    parent.closeFrame2();

			        return true;
			        
			},500);
        }
        else{
            return  false;
        }
    });

</script>