

<?php

use app\models\Drawings;
use app\models\TermsCondtns;

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
    .approveHdrw {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 80px;
    padding: 0;
    }  
    .approveHdrw h1{
        margin:0px;
        padding-bottom:0px !important;
            font-size: 23px;
    }
    
    .fonts{
        font-family: sans-serif;
        font-size: 17px;
    }

    .termscondtnerror{
        position: relative!important;
    }
    
</style>



<body class="procurement" style="background:#fff !important">

    <div class="approveHdrl" style="display:none;"><h1>Work Order </h1></div>
    <form method="POST" action="" id="placeorderform" >
        <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />

        <div class="purchase-order2-wrpr" style="padding:50px; padding-bottom:0px;">

            <div class="row">
                <div class="col-md-6 col-xs-6">
                    <h2 class="text-left pOrder-title">Work Order</h2>
                    <div class="text-left row">
                        <!-- <div class="col-md-2">-->
                        <!--</div>-->
                        <div class="col-md-10">
                            <h3 class="grayBg commentshding" style="text-align:left;"><font color="#191818">Vendor</font></h3>
                            <div class="address">
                                <?php echo $vendor;?> <br/>
                                </b><?php echo $address;?> ,<?php echo $city;?> <br/>
         
                                Ph:-<?php echo $phone;?> <br/>
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
                        <div class="poNbr"><b>WO Number:</b> <input type="text" class="form-control small75" name="po_number" id="po_number" value=""> </div><br/>
                        <b>GSTIN:</b> 32AAFCG7358B1ZV <br/>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 col-xs-12" style="text-align:left">
                    <div class="text-left row">
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
                          <input type="text" class="form-control" id="others" name="others" placeholder="0.00" autocomplete="off">                          
                        </div>
                        <div class="col-sm-5">
                        </div>
                        <div class="col-sm-7">
                            <span style="float:right;" class="error otherserror"></span> 
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
                            <label>Contract Period</label>
                            <input type="text" class="form-control" id="period" name="Period">
                            <input type="hidden" class="form-control" name="date" id="date" value="<?php echo date('d-m-Y');?>">
                        </div>
                               
                        <div class="col-md-6">
                            <label>Mode of Payment</label>
                            <input type="text" class="form-control" id="payment" name="Payment">
                            <span class="error"></span>
                            <input type="hidden" name="ordertype" value="2">
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Retention (%) </label>
                            <input type="text" class="form-control" id="retention" name="Retention">
                            <span class="error"></span>
                        </div> 

                        <div class="col-md-6">
                            <label>Advance</label>
                            <input type="text" class="form-control" id="advance" name="Advance">
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
                        <div class="col-md-12">
                            <input type="hidden" class="form-control" id="tandcstatus" name="tandcstatus" value="0"> 
                            <input type="hidden" class="form-control" id="tandcNow" name="tandc" value="">  
                            <?php 
                                $all_Terms = TermsCondtns::find()->where('Status=0')->all();
                                $ids= $this->context->getfolderids();
                                $docuements=Drawings::find()->Where(['IN', 'folder', $ids])->all();
                            ?>
                            <label>Terms and conditions </label>
                            <select id="myterms">
                                <option value="none">Select Terms and conditions</option>
                                <?php 
                                    foreach($all_Terms as $all_Term):        
                                ?>
                                    <option value="<?= $all_Term->id; ?>"><?= $all_Term->title; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="error termscondtnerror"></span>
                            <span class="pull-right"><a class="tandcupdate" style="color:orange;font-weight:bold;cursor:pointer;">View</a></span>
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

                <div class="col-md-12 text-center india" style="margin-top:20px;display:none">
                    <div class="termsbody"></div>
                </div><br>

                <div class="col-md-12 text-center approve-cancel" style="margin-top:30px;">
                    <button type="button" value="Cancel" class="btn btn-primary cancel" id="cancelorder"><span class="icon-close"></span> Cancel</button>
                    <button type="submit" value="Submit" id="workorderbtn" class="btn btn-primary approve" name="submit"><span class="icon-check"></span> Submit</button>
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
    $(document).on('focus','#date',function(){
        $(this).datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
    });

    $(document).on('click','#workorderbtn',function(){
        var error=0;
        $('.error').hide();
        if($("#tandcNow").val()==''){
            $('#myterms').next("span").html('select Term and condition').show('slow');
            error=1;
        }
        if($('#specification').val()=='')
        {
            $('#specification').next("span").html('Enter Specification').show('slow');
            error=1;
        }
        if($('#others').val()=='')
        {
            $('.otherserror').html('Enter a default value').show('slow');
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
        if($('#retention').val()=='')
        {
            $('#retention').next("span").html('Enter Retention').show('slow');
            error=1;
        }
        if(typeof($('input[name=gsttype]:checked', '#placeorderform').val())=='undefined')
        {
            //alert('Please select either SGST / IGST tax');
            //error=1;
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

$(document).ready(function(){
    $("select#myterms").change(function(){
        var selectedtandc = $(this).children("option:selected").val();
        if( selectedtandc != ''){
            $("#tandcNow").val(selectedtandc);
        }
     
    });
    $(".tandcupdate").click(function(){  
        var tcid = $("#tandcNow").val();
        if(tcid != ''){
            $.ajax({
                type: 'POST',
                url: '../termscondtns/edittermss',
                dataType: "json",
                data: {termid:tcid},
                success: function(data){
                    if(data.error=='No'){
                        $("#tandcstatus").val('1');
                        $('.india').show();
                        $('.termsbody').html(data.result);
                        $('select#myterms').prop('disabled', true);
                        $('.tandcupdate').hide();
                    }else{
                        alert(data.errortext);
                    }
                }
            });
        }else{
            alert('Select one Terms and condition for update');
        }
    });

    $(document).on( "click", "#editsaveterms", function(){
    
        var content = CKEDITOR.instances['edittermscontent'].getData();
        $("#tandcstatus").val('1');
        $("#tandcNow").val(content);
        $('.termsbody').hide();
    });

    $(document).on( "click", "#editcancelterms", function(){

        $('.termsbody').hide();
        $("#tandcstatus").val('0');
    });
});


</script>