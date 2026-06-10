
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
  
  
    .size {
        width: 45px;
    }
    
    .fonts{
        font-family: sans-serif;
        font-size: 17px;
    }
    
</style>

<div class="approveHdrl" style="display:none;"><h1>Purchase Order </h1></div>
    <form method="POST" action="" id="placeorderform" >
        <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
        
        
        
       
    </form>

<script type="text/javascript">

    $(function(){
         $(document).on('click','#cancelorder',function(){  
            
            setTimeout(function(){

                parent.closeFrame2();
                    
            },500);
                      
            parent.window.close();
            window.onunload = function (e) { 
                
             opener.refreshParentWindow();  
             };            
            
        });
    });

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
    })

    $(document).on('click','#purchaseorderbtn',function(){
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

        if($('#cgst').val()!=''){
            if($('#igst').val()!=''){
                alert("You can not select both IGST as well as the other tax.");
                error=1;
            }
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
        
        
        function UnloadWndw(){
            window.onunload = function (e) {  
                opener.refreshParentWindow();  
             }; 
            
        }
        
    });
    
    $(document).ready(function() {
        //this calculates values automatically 
        subAmount();
        $("#sub_total, #tax").on("keydown keyup", function() {
            subAmount();
        });
    });


    function subAmount() {
        var num1 = document.getElementById('sub_total').value;
        var num2 = document.getElementById('tax').value;
        var result = parseInt(num1) + parseInt(num2);
        if (!isNaN(result)) {
            document.getElementById('total').value = result;
        }
    }
 
    document.onkeyup = function (e) {
        if (e.keyCode === 109 || e.keyCode === 189) { 
             $("#tax").val("");
        }
    };
 
    $('#cgst').keyup(function(ev) { 

        var amount = $('#sub_total').val();
        var gst = $('#cgst').val();
        var total = amount;
        var tot_price = (total * gst / 100);   
        var finaltax= parseFloat(tot_price).toFixed(2);   
        $('#tax').val(finaltax);

        //alert (finaltotal);
        var finaltotal=  finaltax; 
        var subtotal = amount;  
        var finalnumber= Number(subtotal) + Number(finaltotal); 
        var  final=parseFloat(finalnumber).toFixed(2);  

        $('.total').val(final);  // alert(final);

    });


    $('#igst').keyup(function(ev) {  

        var amount = $('#sub_total').val();
        //var qty = $('#qty').val();
        var igst = $('#igst').val();
        //$(this).val(); 
        var total = amount;
        var tot_price = (total * igst / 100);
        var finaltax= parseFloat(tot_price).toFixed(2);   
        $('#tax').val(finaltax);
        var finaltotal=  finaltax; 
        var subtotal = amount;  
        var finalnumber= Number(subtotal) + Number(finaltotal); 
        var  final=parseFloat(finalnumber).toFixed(2);  
        $('.total').val(final);  // alert(final);
        
    });
 
</script>