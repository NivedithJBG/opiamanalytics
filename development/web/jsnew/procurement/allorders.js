
$(document).ready(function() {
   history.replaceState(null, null, ' ');
})



$(document).on( "click", ".addvendortocart", function(){

    var error=0;
    var vendorid= $(this).val();
    var jobcard=$('#jobcard'+vendorid).val();
    var resource=$('#vresource'+vendorid).val();
    var resourceqty=$('#vendorqty'+vendorid).val();
    var vendorrate=$('#vendorrate'+vendorid).val();
    //var numworkers=$('#numworkers'+vendorid).val();
    //var numdays=$('#numdays'+vendorid).val();
    //var otrate=$('#otrate'+vendorid).val();
    var numworkers=$('#editnumworkers'+vendorid).val();
    var numdays=$('#editqtttys'+vendorid).val();
    var otrate=$('#editotrate'+vendorid).val();
    var noworkers=numworkers * numdays;

    $('#collapsevendors').removeClass('in');

    $('.acco-three').removeClass('active');
    
    $('.acco-one').removeClass('active');

    $('.acco-four').addClass('active');

    $('#collapseallorders').addClass('in');

    $("#collapsevendors").attr("aria-expanded","false");

    $("#collapseallorders").attr("aria-expanded","true");

    $('#collapseallorders').css('height','');

    $('.panel-group').removeClass('acco-billofres-active');
    $('.panel-group').removeClass('acco-vendors-active');
    $('.panel-group').removeClass('acco-three-active');
    $('.panel-group').removeClass('acco-confirmorders-active');
    $('.panel-group').removeClass('acco-despatchorders-active');
    $('.panel-group').removeClass('acco-one-active');
    $('.panel-group').addClass('acco-three-active');
    $('.panel-group').removeClass('acco-five-active');
    $('.panel-group').removeClass('acco-six-active');
    $('.panel-group').removeClass('acco-seven-active');
    $('.panel-group').removeClass('acco-eight-active');
    $('.panel-group').removeClass('acco-nine-active');
    $('.panel-group').removeClass('acco-ten-active');

    
    
    
    $('#chooseallorder').trigger('click');

    if (error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../procurement/addtocart',
            dataType: "json",
            data: {vendorid:vendorid,numworkers:numworkers,numdays:numdays,otrate:otrate,jobcard:jobcard,resource:resource,vendorrate:vendorrate,resourceqty:resourceqty},
            success: function(data){
                if(data.error=='No')
                {
                    $('#choosevendors').removeClass('active').next().slideUp();

                    $('#Cart').addClass('active').next('.acc_container').slideDown();
                    $('#placesearch').trigger('click');
                    $("#addedresources tr").detach();
                }
                $('.preloader').hide();
            }
        });
    }

});




$(document).on( "click", "#chooseallorder", function(){
    $('#prepeated').trigger('click');
    $('.frstcl').addClass('active');
    

    //$('#recartsearch').trigger('click');

    $('#cancelorder').trigger('click');
    $('#cartsearch').trigger('click'); 
    

    /*$('#cancelorderworkorder').trigger('click');
    setTimeout(function(){ $('#wrksearch').trigger('click'); }, 1000);
    

    $('#cancelorderworkorder').trigger('click');
    setTimeout(function(){ $('#directwrksearch').trigger('click'); }, 3000);
    

    $('#cancelorderleaseorder').trigger('click');
    setTimeout(function(){ $('#leasesearch').trigger('click');  }, 4000);
    

    $('#cancelorderworkorder').trigger('click');
    setTimeout(function(){ $('#despsearch').trigger('click');  }, 5000);*/
    

    
    $('.panel-group').removeClass('acco-one-active');
    $('.panel-group').removeClass('acco-two-active');
    $('.panel-group').addClass('acco-three-active');
    $('.panel-group').removeClass('acco-four-active');
    $('.panel-group').removeClass('acco-three-active');


});




/*Repeated orders*/

$(function() {
    $('#recartsearch').click(function () {
        $.ajax({
            type: 'POST',
            url: '../procurement/recart',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            //data: {task:$('#searchtask').val()},
            success: function(data){
                if(data.error=='No')
                {
                    //$('#recartitems').html(data.result);
                    $('#recartitmsss').html(data.result);
                    $('#cartitemstable').show();
                    $('#wrksearch').trigger('click');
                }else if(data.error=='yes'){
                   $('#repee').hide();
                   $('#recartform').hide();
                   $('#wrksearch').trigger('click');
                }
                $('.preloader').hide();
            }
        });
    });
});

$(document).on('change','.cartresitemqty',function(){
    var qty=$(this).val();
    var resid=$(this).attr('data-id');
    var rate=$('#cartresitemrate'+resid).val();
    var amount=qty * rate;
    $('#order-amount'+resid).html(amount.toFixed(2));
    $.ajax({
        type: 'POST',
        url: '../procurement/Updatecartqty',
        dataType:"json",
        async: false,
        data:{resid:resid,qty:qty,amount:amount},
        success: function(data){

        }
    });
});
$(document).on('click','#recartitems input',function(){
    var values = [];
    var ordervalues = [];
    var restype = [];
    var project = [];
    $('#recartitems input:checked').each(function() {
        values.push($(this).val());
        ordervalues.push($(this).attr('data-id'));
        restype.push($(this).attr('data-restype'));
        project.push($(this).attr('data-project'));
    });
    $('[name="vendors"]').attr({value: values.join(', ')});
    $('[name="orders"]').attr({value: ordervalues.join(', ')});
    $('[name="restype"]').attr({value: restype.join(', ')});
    $('[name="project"]').attr({value: project.join(', ')});
});
$(document).on('click','#placeorder',function(){

    if($('.vendors').is(":checked")) {
        var selvendors=$('#vendors').val();
        var restype=$('#restype').val();
        var orders=$('#orders').val();
        if(selvendors==44){
            var vendorname="Company Owned";
        }
       
    }

    $('#placeorderdata').hide();   

    if(vendorname=='Company Owned' && restype!=33){

        $.ajax({
            type: 'POST',
            url: '../procurement/despatchorder',
            dataType: "json",
            data: {orders: orders,restype: restype},
            success: function (data) {
                if (data.error == 'No') {

                    if( vendorname=="Company Owned" && restype!=33){
                        ordertype="Despatch Order";
                        $('#despatchorderdata').html(data.despatchorder);  
                        $('#despatchorderdata').show();
                    }
                }
            }
        });

    }

    else{

        $.ajax({
            type: 'POST',
            url: '../procurement/order',
            dataType: "json",
            data: {orders: orders,restype: restype},
            success: function (data) {
                if (data.error == 'No') {

                    if(restype==19){   
                        ordertype="Work Order";  
                        $('#workorderdata').html(data.workorder);  
                        $('#workorderdata').show();
                    }
                    else if(restype==24){ 
                        ordertype="Lease Order";  
                         $('#leaseorderdata').html(data.leaseorderr);  
                         $('#leaseorderdata').show();

                    }
                    else if(restype==33){
                        ordertype="Direct Work Order";
                        $('#musterrolldata').html(data.musterroll);  
                         $('#musterrolldata').show();

                    }
                    else{
                        ordertype="Purchase Order";
                         $('#repurchaseorder').html(data.purchaseorder);  
                         $('#repurchaseorder').show();
                    }
                }
            }
        });

    }
});
$(document).on('click','#reecartitems input',function(){
   
   /* 
    $('#reecartitems input:checked').each(function() {
        $vend_id = $(this).val();
        $prj = $(this).attr('data-project');
        $res = $(this).attr('data-restype');
        $cartid = $(this).attr('data-id')

        $('#revenids').val($vend_id );
        $('#rerresidss').val($res);
        $('#reprjss').val($prj);
        $('#recrtid').val($cartid);

        
    }); */



    var values = [];
    var ordervalues = [];
    var restype = [];
    var project = [];


   
    $('#reecartitems input:checked').each(function() {


        values.push($(this).val());
        ordervalues.push($(this).attr('data-id'));
        restype.push($(this).attr('data-restype'));
        project.push($(this).attr('data-project'));
    });

    $('[name="revenids"]').attr({value: values.join(', ')});
    $('[name="recrtid"]').attr({value: ordervalues.join(', ')});
    $('[name="rerresidss"]').attr({value: restype.join(', ')});
    $('[name="reprjss"]').attr({value: project.join(', ')});
   
});
$(document).on('click','#replaceorder_new',function(){

    if($('.vendors').is(":checked")) {

        var venid=$(this).attr('data-ven');
        var proid=$(this).attr('data-pro');
        var resid = $('#rerresidss').val();
        var cartid = $('#recrtid').val();

        $.ajax({
            type: 'POST',
            url: '../procurement/ordernew',
            dataType: "json",
            data: {venid: venid,proid: proid,mode:2,resid:resid,cartid:cartid},
            success: function (data) {
                if (data.error == 'No') {

                    if(data.order == 'purchase'){
                        ordertype="Purchase Order";
                        $('#repurchaseorder').show();
                        $('#repurchaseorder').html(data.purchaseorder);  
                        $('#recartform').hide();
                        $('#carttform').hide();
                        $('#workform').hide();
                        $('#directworkform').hide();
                        $('#leaseform').hide();
                        $('#despform').hide();
                        $('.headngstyle').hide();
                        $('.orders-wrpr').hide();
                        $('.placeorder-list').hide();
                        $('.topsbars').hide();
                    
                    }
                }
            }
        });
    }else{
        alert('Please select any vendor before proceeding.');
         return false;
    }

});
$(document).on('click','.removevendor',function(){  
    var id=$(this).val();
    $.ajax({
        type: 'POST',
        url: '../procurement/removeitem',
        beforeSend : function(){
            $('#removevendor'+id).attr("disabled", true);
        },
        dataType:"json",
        data:{id:id},
        success: function(data){
            if(data.error=='No')
            {
                $('#cartitemrow'+id).remove();
            }
            else
            {
                alert(data.errortext);
            }

            $('#removevendor'+id).attr("disabled", false);

        }
    });

});

$(document).on('click','#recancelorder',function(){   

//$(document).on('click','#repurchaseorder,a.cancel',function(){ 


    $('.topsbars').show();
         $('#repurchaseorder').hide();
            
          //$('#placeorderdata').show();

          $('#recollapsecart').show();

          $('#recartform').show();
          $('#recartform').show();
          $('#carttform').show();
          $('#workform').show();
          $('#directworkform').show();
          $('#leaseform').show();
          $('#despform').show();
          $('.headngstyle').show();
          $('.orders-wrpr').show();
          $('.placeorder-list').show();
            
            // setTimeout(function(){

            //     parent.closeFrame2();
                    
            // },500);
                      
            /*parent.window.close();
            window.onunload = function (e) { 
                
             opener.refreshParentWindow();  
             };  

            $.ajax({
                type: 'POST',
                url: '../procurement/droptemp',
                beforeSend : function(){
                },
                dataType:"json",
                data:{},
                success: function(data){
                }
            }); */        
            
        });


$(document).on('focus','#dateofdelivery',function(){
        $(this).datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
    });

   $(document).on('focus','#orderofdate',function(){
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



 $(document).on('click','#repurchaseorderbtn',function(){

     var restype=$('#restype').val();
       var orders=$('#orders').val();

     // var origin=window.location.origin;
     //        var pathname=window.location.pathname;
     //        var pathArray = window.location.pathname.split( '/' );
     //        var formEl = document.forms.cartform;
     //        var formData = new FormData(formEl);
     //        var orders = formData.get('orders');
     //        var restype = formData.get('restype');
     //        //console.log(name)
     //        var baseUrl=origin+"/"+pathArray[1]+"/"+pathArray[2]+"/order?orders="+orders+"&restype="+restype;
            
            //alert(baseUrl);
           // console.log(baseUrl)
           

           

          //alert(baseUrl);

        var error=0;
        $('.error').hide();
        if($('#specification').val()=='')
        {
            $('#specification').next("span").html('Enter Description').show('slow');
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
            $('#payment').next("span").html('Enter payment mode').show('slow');
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

               $('#repurchaseorder').hide();
            
         

             //$('#placeOrderiframe').attr('src', baseUrl);
              $('#placeorderdata').show();

            
        }
        else{
            return  false;
        }
        
        
        // function UnloadWndw(){
        //     window.onunload = function (e) {  
        //         opener.refreshParentWindow();  
        //      }; 
            
        // }
        
    });


    $(document).ready(function() {
        //this calculates values automatically 
        //subAmount();
        $("#sub_total, #tax").on("keydown keyup", function() {
            subAmount();
        });
    });

$(document).ready(function(){
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
            // $("#tax").val("");
        }
    };
 
  });
$(document).on('keyup','#workgst',function(){
    var amount = $('#sub_wrktotal').val();
    
    var gst = $('#workgst').val();
    var total = amount;
    var tot_price = (total * gst / 100);
    var finaltotal=  tot_price;
    var subtotal = amount;

    var finalnumber= Number(subtotal) + Number(finaltotal); 
    var  final=parseFloat(finalnumber).toFixed(2);

    $('.wrktotal').val(final);
}); 

$(document).ready(function(){

    $(document).on('keyup','.cgsttt',function(){  
        $('.cgsttt').each(function() {
            var orderID = $(this).attr("data-id");
            var ammtt = $('.amntvalue'+orderID).val();    
            var gst_value = $('.cgstvalue'+orderID).val();  
            var igst_value = $('.igstvalue'+orderID).val();  
            var amtvalue = $('.amntvalue'+orderID).val();
            var tot_price_value = (amtvalue * gst_value / 100);   
            var finaltax_value = parseFloat(tot_price_value).toFixed(2);   

            if(gst_value){
                $('.igstvalue'+orderID).prop('readonly', true);
                $('#taxvalue'+orderID).val(finaltax_value);
            }
            else
                $('.igstvalue'+orderID).removeAttr('readonly');

            if(!gst_value && !igst_value)
                $('#taxvalue'+orderID).val('0');

            updatePurchaseOrderTotal();
        });  
    });

    $(document).on('keyup','.igsttt',function(){ 

        $('.igsttt').each(function() {
            var orderID = $(this).attr("data-id");
            var ammtt = $('.amntvalue'+orderID).val();    
            var gst_value = $('.cgstvalue'+orderID).val();  
            var igst_value = $('.igstvalue'+orderID).val();  
            var amtvalue = $('.amntvalue'+orderID).val();
            var tot_price_value = (amtvalue * igst_value / 100);   
            var finaltax_value = parseFloat(tot_price_value).toFixed(2);   

            if(igst_value){
                $('.cgstvalue'+orderID).prop('readonly', true);
                $('#taxvalue'+orderID).val(finaltax_value);
            }
            else
                $('.cgstvalue'+orderID).removeAttr('readonly');

            if(!gst_value && !igst_value)
                $('#taxvalue'+orderID).val('0');

            updatePurchaseOrderTotal();
        });  

    });

    $(document).on('keyup','#freight',function(){ 
        updatePurchaseOrderTotal();
    });

    $(document).on('keyup','#insurance',function(){ 
        updatePurchaseOrderTotal();
    });

    $(document).on('keyup','#others',function(){ 
        updatePurchaseOrderTotal();
    });


    $(document).on('keyup','#otherss',function(){ 
    //$('#cgstpurchaseorder').keyup(function() {  alert ("hi");

        var amount = $('#sub_total').val();
        var gst = $('#cgst').val();
        var total = amount;
        var tot_price = (total * gst / 100);   
        var finaltax= parseFloat(tot_price).toFixed(2);   
        //$('#tax').val(finaltax);
        var freight = $('#freight').val(); 
        var insurance = $('#insurance').val(); 
        var otherss = $('#otherss').val(); 
        //alert (finaltotal);
        var finaltotal=  finaltax; 
        var subtotal = amount;  
        var finalnumber= Number(amount) + Number(otherss); 
        var  final=parseFloat(finalnumber).toFixed(2);  

        $('.total').val(final);  // alert(final);

    });




    });



function getTotalTax() {
    var totTax = 0;
    $( ".itemTaxValue" ).each(function( index ) {
      totTax += Number($(this).val());  
    });
    totTax=parseFloat(totTax).toFixed(2);  
    return totTax;
}

   $(document).ready(function(){  

 /* $(document).on('keyup','#noofworkerss',function(){  

    //$('#igst').keyup(function(ev) {  

        var workers = $('#noofworkerss').val();
        //var qty = $('#qty').val();
        var days = $('#noofdayss').val();
        //var rates = $('#dirrate').val();

        var str = $('#dirrate').val();
        var rates = str.replace(/,/g, '');


        //$(this).val(); 
       // var total = amount;
        var amount = (parseFloat(workers) * parseFloat(days)) * (parseFloat(rates));
      // alert (amount); 

        var finalamount= parseFloat(amount).toFixed(2);   
       // $('#amountdet').val(finalamount);
        var finaltotal=  finalamount; 

        var  final=parseFloat(finaltotal).toFixed(2);  
        $('.totalamt').val(final);  // alert(final);
          // alert(final);

          $.ajax({
            type:'POST',
            url:'../procurement/saveamount',
            dataType:'json',
            data: {type:type,resid:resid},
            success:function(data){
                if(data.error=='No')
                {
                    $('.leasedrow'+resid).html(data.datarows);
                }
            }
        });
        
    });*/


    $(document).on('keyup','#noofdayss',function(){  

    //$('#igst').keyup(function(ev) {  

        var workers = $('#noofworkerss').val();
        //var qty = $('#qty').val();
        var days = $('#noofdayss').val();
        //var rates = $('#dirrate').val();

        var str = $('#dirrate').val();
        var rates = str.replace(/,/g, '');
        //$(this).val(); 
       // var total = amount;
        var amount = workers * days * rates;
      // alert (amount); 

        var finalamount= parseFloat(amount).toFixed(2);   
       // $('#amountdet').val(finalamount);
        var finaltotal=  finalamount; 

        var  final=parseFloat(finaltotal).toFixed(2);  
        $('.totalamt').val(final);  // alert(final);
          // alert(final);
        
    });

    $(document).on('keyup','#dirrate',function(){  

        //$('#igst').keyup(function(ev) {  
    
            var workers = $('#noofworkerss').val();
            //var qty = $('#qty').val();
            var days = $('#noofdayss').val();
            //var rates = $('#dirrate').val();
    
            var str = $('#dirrate').val();
            var rates = str.replace(/,/g, '');
            //$(this).val(); 
           // var total = amount;
            var amount = workers * days * rates;
          // alert (amount); 
    
            var finalamount= parseFloat(amount).toFixed(2);   
           // $('#amountdet').val(finalamount);
            var finaltotal=  finalamount; 
    
            var  final=parseFloat(finaltotal).toFixed(2);  
            $('.totalamt').val(final);  // alert(final);
              // alert(final);
            
        });


     $(document).on('keyup','#ottrates',function(){  

    //$('#igst').keyup(function(ev) {  

        var workers = $('#noofworkerss').val();
        //var qty = $('#qty').val();
        var days = $('#noofdayss').val();
        var rates = $('#ottrates').val();
        //$(this).val(); 
       // var total = amount;
        var amount = workers * days * rates;
      // alert (amount); 

        var finalamount= parseFloat(amount).toFixed(2);   
       // $('#amountdet').val(finalamount);
        var finaltotal=  finalamount; 

        var  final=parseFloat(finaltotal).toFixed(2);  
        $('.totalamt').val(final);  // alert(final);
          // alert(final);
        
    });

});











   $(document).on('click','#workorderbtn',function(){  

      var restype=$('#restype').val();
       var orders=$('#orders').val();

       
        var error=0;
        $('.error').hide();
        // if($("#tandcNow").val()==''){
        //     $('#myterms').next("span").html('select Term and condition').show('slow');
        //     error=1;
        // }
        if($('#specification').val()=='')
        {
            $('#specification').next("span").html('Enter Specification').show('slow');
            error=1;
        }
        if($('#otherss').val()=='')
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
            $('#payment').next("span").html('Enter payment mode').show('slow');
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

             $('#workorderdata').hide();
            
         

             //$('#placeOrderiframe').attr('src', baseUrl);
              $('#placeorderdata').show();
            // setTimeout(function(){
            //         parent.closeFrame2();                      
            //         return true;
                    
            // },500);
        }
        else{
            return  false;
        }
    });
$(document).on('click','#tandcedit',function(){
    var tid = $("#tandcNowedit").val();

    var orderid = $("#tandcOrderid").val();

        $.ajax({
                type: 'POST',
                url: '../termscondtns/edittermsapprove', 
                dataType: "json",
                data: {termid:tid,orderid:orderid},
                success: function(data){
                    if(data.error=='No'){
                        $('#tandcedit').prop('disabled', true);
                        $('.indias').show();
                        $('.termsbodys').html(data.result);
                        
                    }else{
                        alert(data.errortext);
                    }
                }
            });
});
 $(document).on( "click", "#editsavetermss", function(){ 
    
        var content = CKEDITOR.instances['edittermsscontent'].getData();
        //$("#tandcstatus").val('1');
        $("#tandcNowedit").val(content);
        $('.termsbodys').hide();
    });
  $(document).on( "click", "#editcanceltermss", function(){

        $('.termsbodys').hide();
        //$("#tandcstatus").val('0');
    });

  $(document).on('change','select#mytermspurchase',function(){  
    $('.tandcupdatepurchase').show();
    //$("select#myterms").change(function(){ alert ("hi");
        var selectedtandc = $(this).children("option:selected").val();
        if( selectedtandc != ''){
            $("#tandcNow").val(selectedtandc);
        }
     
    });

   $(document).ready(function(){ 
     $(document).on('change','select#myterms',function(){  
        $('.tandcupdate').show();
    //$("select#myterms").change(function(){ alert ("hi");
        var selectedtandc = $(this).children("option:selected").val();
        if( selectedtandc != ''){
            $("#tandcNow").val(selectedtandc);
        }
     
    });
     $(document).on('change','select#terms',function(){
     $('#tandcedit').show(); 
    //$("select#myterms").change(function(){ alert ("hi");
        var selectedtandc = $(this).children("option:selected").val();
        if( selectedtandc != ''){
            $("#tandcNowedit").val(selectedtandc);
        }
     
    });
     
     $(document).on('click','.termssbmt',function(){

        $.ajax({
                type: 'POST',
                url: '../termscondtns/savetermscntenttemporary', 
                dataType: "json",
                data: $('#leaseterms').serialize(),
                success: function(data){
                    if(data.error=='No'){
                        
                    }
                }
            });
     });
     $(document).on('click','.ptermssbmt',function(){

        $.ajax({
                type: 'POST',
                url: '../termscondtns/savetermscntenttemporary', 
                dataType: "json",
                data: $('#purchasesss').serialize(),
                success: function(data){
                    if(data.error=='No'){
                        
                    }
                }
            });
     });

   $(document).on('click','.tandcupdate',function(){ 
   // $(".tandcupdate").click(function(){  
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
                        //$('select#myterms').prop('disabled', true);
                       // $('.tandcupdate').hide();
                    }else{
                        alert(data.errortext);
                    }
                }
            });
        }else{
            alert('Select one Terms and condition for update');
        }
    });

   $(document).on('change','#myterms',function(){ 
    $('tandcupdate').show();
   // $(".tandcupdate").click(function(){  
        //if($('.tandcupdate').is(":hidden")){
            
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
                            //$('select#myterms').prop('disabled', true);
                           // $('.tandcupdate').hide();
                        }else{
                            alert(data.errortext);
                        }
                    }
                });
            }
            
        //}
    });

   $(document).on('click','.tandcupdatepurchase',function(){ 
   // $(".tandcupdate").click(function(){  
        var tcid = $("#tandcNow").val();
        if(tcid != ''){
            $.ajax({
                type: 'POST',
                url: '../termscondtns/edittermpurchase', 
                dataType: "json",
                data: {termid:tcid},
                success: function(data){
                    if(data.error=='No'){
                        $("#tandcstatus").val('1');
                        $('.india').show();
                        $('.termsbodyp').html(data.result);
                        //$('select#myterms').prop('disabled', true);
                        //$('.tandcupdatepurchase').hide();
                    }else{
                        alert(data.errortext);
                    }
                }
            });
        }else{
            alert('Select one Terms and condition for update');
        }
    });

   /*$(document).on('change','#mytermspurchase',function(){ 
   // $(".tandcupdate").click(function(){  
        //if($('.tandcupdatepurchase').is(":hidden")){
            
            var tcid = $("#tandcNow").val();
            if(tcid != ''){
                $.ajax({
                    type: 'POST',
                    url: '../termscondtns/edittermpurchase', 
                    dataType: "json",
                    data: {termid:tcid},
                    success: function(data){
                        if(data.error=='No'){
                            $("#tandcstatus").val('1');
                            $('.india').show();
                            $('.termsbody').html(data.result);
                            //$('select#myterms').prop('disabled', true);
                            //$('.tandcupdatepurchase').hide();
                        }else{
                            alert(data.errortext);
                        }
                    }
                });
            }
            
       // }
    });*/

    $(document).on( "click", "#editsaveterms", function(){ 
    
        var content = CKEDITOR.instances['edittermscontent'].getData();
        $("#tandcstatus").val('1');
        $("#tandcNow").val(content);
        $('.termsbody').hide();tandcNow
    });

    $(document).on( "click", "#editcancelterms", function(){

        $('.termsbody').hide();
        $("#tandcstatus").val('0');
    });


    $(document).on('focus','#date',function(){
        $(this).datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
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
    $(document).on('focus','.datedes',function(){
        $(this).datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
    });


       $(document).on('click','#leaseorderbtn',function(){

       var restype=$('#restype').val();
       var orders=$('#orders').val();

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
            $('#payment').next("span").html('Enter payment mode').show('slow');
            error=1;
        }

        if(error==0){



             $('#leaseorderdata').hide();
            
         

             //$('#placeOrderiframe').attr('src', baseUrl);
              $('#placeorderdata').show();
            // setTimeout(function(){
            //         parent.closeFrame2();

            //         return true;
                    
            // },500);
        }
        else{
            return  false;
        }
    });

  
    $(document).on('change','.whrs',function(){ 
        var resid = $(this).data('id'); 
        var wrhrs = $('#working_hours'+resid+' :selected').val();

        $('#dwotrows'+resid).val(wrhrs); 
      
    });


        $(document).on('click','.directworkorderbtnclass',function(){  

        var error=0;
        $('.error').hide();  

       /* $('.dwotrows').each(function(){ 

            var trresid = $(this).data('id');
            var wrkghrs = $('#working_hours').val(); 
            if(wrkghrs == 'none' || wrkghrs == '')
            { 
                $('#working_hours').next("span").html('Select Working Hours').show('slow');
                error = 1;
            }
        });*/

        
         var wrkghrs = $('#placeworking_hours').val();//  alert(wrkghrs);  return false;
            if(wrkghrs == 'none' || wrkghrs == '')
            { 
                $('#placeworking_hours').next("span").html('Select Working Hours').show('slow');
                error = 1;
            }


        if(error==0){


               $('#musterrolldata').hide();
            
         

             //$('#placeOrderiframe').attr('src', baseUrl);
              $('#placeorderdata').show();
            // setTimeout(function(){
            //         parent.closeFrame2();

            //         return true;
                    
            // },500);
        }
        else if(error==1){

            return  false;
        }
       
    });


  $(document).on('focus','.date',function(){
        var id=$(this).attr('data-id');
        $('#date'+id+'').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true});

    });

   $(document).on('change','.resource_item',function(){
        var resource_no = $(this).attr('data-id');
        $.ajax({
            type: 'POST',
            url: '../procurement/getmovefromprojects', 
            dataType: "json",
            data: {resourceid:$(this).val()},
            success: function(data){
                if(data.error=='No'){
                    $("#from"+resource_no).html(data.projectList);
                }else{
                    alert(data.errortext);
                }
            }
        });
   });


   $(document).on('click','#despatchorderbtn',function(){


        var error=0;
        $('.error').hide();

        var resource_item_arr = [];
        $('.resource_item').each(function(){
            var id=$(this).attr('data-id');            
            if($(this).val()=='none')
            {
                $("#resource_item"+id).next("span").html('Select Resource').show('slow');
                error=1;
            }
            else{
                if (resource_item_arr.indexOf($(this).val()) == -1)
                    resource_item_arr.push($(this).val());
                else{
                    $("#resource_item"+id).next("span").html('Duplicate Resource').show('slow');
                    error=1;
                }
            }
        });
        $('.from').each(function(){
            var id=$(this).attr('data-id');
            $('#mvefm').val($(this).val());
            
            if($(this).val()=='none')
            {
                $("#from"+id).next("span").html('Select Move from').show('slow');
                error=1;
            }
        });
        $('.to').each(function(){
            var id=$(this).attr('data-id');
            $('#mvet').val($(this).val());
            if($(this).val()=='none')
            {
                $("#to"+id).next("span").html('Select Move to').show('slow');
                error=1;
            }
        });
        $('.datedes').each(function(){  
            var ids=$(this).attr('data-id');
            $('#datede').val($(this).val());
            if($(this).val()=='')
            {
                $("#date"+ids).next("span").html('Select Date').show('slow');
                error=1;
            }
        });
        $('.vehicle_no').each(function(){
            var id=$(this).attr('data-id');
            
            if($(this).val()=='')
            {
                $("#vehicle_no"+id).next("span").html('Enter Period of Vehicle No').show('slow');
                error=1;
            }
        });

        
        
        

    
        if(error==0){
            $('form#despatchorderform').submit();

          $('#despatchorderdata').hide();
            
         

             //$('#placeOrderiframe').attr('src', baseUrl);
              $('#placeorderdata').show();
            
            // setTimeout(function(){
            //         parent.closeFrame2();

            //         return true;
                    
            // },500);
        }
        else{
            return  false;
        }
    });

   $(document).on('click','.reeditvenrescart',function(){

        var id=$(this).attr('data-id');

        $('#resreqty'+id).hide();
        $('#resreordrqty'+id).hide();
        $('#resreordrlevel'+id).hide();
        $('#reorate'+id).hide();
        $('#reeditvenrescart'+id).hide();

        $('#editresreqty'+id).show();
        $('#editresreordrqty'+id).show();
        $('#editresreordrlevel'+id).show();
        $('#recarttrate'+id).show();
        $('#resavevenrescart'+id).show();


   });

   $(document).on('click','.resavevenrescart',function(){

        var id=$(this).attr('data-id');

        var rate = $('#recarttrate'+id).val();
        var edtreq = $('#editresreqty'+id).val();
        rqty = $('#editresreordrqty'+id).val();
        rlevel = $('#editresreordrlevel'+id).val();

        if(rqty != '')
        {
            reordqty = rqty;
        }else{
            reordqty = '';
        }

        if(rlevel != '')
        {
            reorderlevel = rlevel;
        }else{
            reorderlevel = '';
        }
        var tot = edtreq * rate;

        $.ajax({
            type: 'POST',
            url: '../procurement/saverepodetails',
            /*beforeSend : function(){
                $('.preloader').show();
            },*/
            dataType: "json",
            data: {cartid:id,reqty:$('#editresreqty'+id).val(),reordqty:reordqty,reorderlevel:reorderlevel,rate:$('#recarttrate'+id).val()},
            success: function(data){
                if(data.error=='No')
                {

                    $('#editresreqty'+id).hide();
                    $('#editresreordrqty'+id).hide();
                    $('#editresreordrlevel'+id).hide();
                    $('#resavevenrescart'+id).hide();
                    $('#recarttrate'+id).hide();
                    $('#reorate'+id).show();
                    $('#resreqty'+id).show();
                    $('#resreordrqty'+id).show();
                    $('#resreordrlevel'+id).show();
                    $('#reeditvenrescart'+id).show();

                    $('#resreqty'+id).html($('#editresreqty'+id).val());
                    $('#resreordrqty'+id).html($('#editresreordrqty'+id).val());
                    $('#resreordrlevel'+id).html($('#editresreordrlevel'+id).val());
                    $('#reorate'+id).html($('#recarttrate'+id).val());

                    $('#vendortotal'+data.vendorid).html(data.totalamt);
                    $('#recarttotamount'+id).html(data.amount);

                    $('#balancetotal'+id).html(data.remaining.tofixed(2));

                    
                }
                //$('.preloader').hide();
            }
        });       

   });
    $(document).on('keyup','.recartreq',function(){

    var cartid = $(this).attr('data-id');
    var ven = $(this).attr('data-v');

    var req = $('#editresreqty'+cartid).val();

    var rate = $('#recarttrate'+cartid).val();

    var eqy = $('#reestqy'+cartid).val();

    

    if(req != ''){
         var tot = req * rate;
    }

    if(req != ''){
        
        $('#recarttotamount'+cartid).html(tot.toFixed(2));
        $('#resreotot'+cartid).val(tot);
        
    }else{
        
        var amntt = eqy * rate;
        $('#recarttotamount'+cartid).html(amntt.toFixed(2));
        $('#resreotot'+cartid).val(amntt);
        
    }

    var amn = 0;

    $('.reoresss_'+ven).each(function(){
        var samt = $(this).val();

        amn = parseInt(amn) + parseInt(samt);

    });
    
    $('.retotvenres'+ven).html(amn.toFixed(2));



 });


$(document).on('click','.resplitvendor',function(){

    var id=$(this).attr('data-id');

    var cartid=$(this).attr('data-cartid');
    

    $.ajax({
        type: 'POST',
        url: '../procurement/rechoosenewvendor',
        /*beforeSend : function(){
            $('.preloader').show();
        },*/
        dataType: "json",
        data: {resourceid:id,cartid:cartid},
        success: function(data){
            if(data.error=='No')
            {
                $('#newaddedresources').html(data.result);
            }
            //$('.preloader').hide();
        }
    });       

});

$(document).on('click','.rechangevendorqty',function(){

    var vendorid=$(this).attr('data-vendorid');

    var resourceid=$(this).attr('data-resourceid');

    var cartid=$(this).attr('data-cartid');

    var splitqty = $('#splitqty'+vendorid).val();

    var r = confirm("Are you sure you want to change the vendor?");

    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../procurement/changevendor',
            dataType: "json",
            data: {vendorid:vendorid,resourceid:resourceid,cartid:cartid,splitqty:splitqty},
            success: function(data){
                if(data.error=='No')
                {
                    $('.changevendorpopup').trigger('click');
                    $('#recartsearch').trigger('click');
                }
            }
        });      

    } 

});

$(document).on('keyup','.recartreq',function(){

    var cartid = $('.recartreq').attr('data-id');

    var req = $('.recartreq').val();

    var rate = $('#recarttrate'+cartid).val();

    var eqy = $('#reestqy'+cartid).val();

    var amnt = eqy * rate;

    if(req != ''){
         var tot = req * rate;
    }

    if(req != ''){
        $('#recarttotamount'+cartid).html(tot.toFixed(2));
    }else{
    $('#recarttotamount'+cartid).html(amnt.toFixed(2));
    }


});
$(document).on('click','.cartplacee',function(){

    //var id = $('#crtidss').val();  

    $('.cartitemexp').toggleClass('cartitems-expanded');

    $(this).parents('tr').toggleClass('aria-parent');

    var area = $('.cartplacee').attr('aria-expanded');

    if(area=='true'){
        var projid = $('#projval').val();
        $('.projctsel'+projid).trigger('click');
    }

});

$(document).on('click','.despplaceord',function(){

    //var id = $('#crtidss').val();

    /*setTimeout(function(){  $('.despcartitemexp').toggleClass('cartitems-expanded');

    $(this).parents('tr').toggleClass('aria-parent'); }, 200);*/

    $('.despcartitemexp').toggleClass('cartitems-expanded');

    $(this).parents('tr').toggleClass('aria-parent');

   
    

});

$(document).on('click','.wrkpplaceord',function(){

    //var id = $('#crtidss').val();

    /*setTimeout(function(){  $('.wrkplaceordexp').toggleClass('cartitems-expanded');
      

    $(this).parents('tr').toggleClass('aria-parent'); }, 200);*/

    $('.wrkplaceordexp').toggleClass('cartitems-expanded');
      

    $(this).parents('tr').toggleClass('aria-parent');

   
    

});
$(document).on('click','.dirwrkpplaceord',function(){

    //var id = $('#crtidss').val();

    /*setTimeout(function(){  $('.dirplaceordexp').toggleClass('cartitems-expanded');

    $(this).parents('tr').toggleClass('aria-parent'); }, 200);
*/
    $('.dirplaceordexp').toggleClass('cartitems-expanded');

    $(this).parents('tr').toggleClass('aria-parent');
    

});
$(document).on('click','.leaspplaceord',function(){

    //var id = $('#crtidss').val();

   /* setTimeout(function(){  $('.leaseplaceordexp').toggleClass('cartitems-expanded');

    $(this).parents('tr').toggleClass('aria-parent'); }, 200);*/

    $('.leaseplaceordexp').toggleClass('cartitems-expanded');

    $(this).parents('tr').toggleClass('aria-parent');

   
    

});



/*Repeated orders End*/

/*Purchase orders*/



/*function getData() { 
  return $.ajax({
            type: 'POST',
            url: '../procurement/cart',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            //data: {task:$('#searchtask').val()},
            success: function(data){
                if(data.error=='No')
                {
                    //$('#cartitemss').html(data.result);
                    $('#cartitemsss').html(data.result);

                    $('#cartitemstable').show();
                    $('#recartsearch').trigger('click');

                }
                else if(data.error=='yes'){
                    $('#purporr').hide();
                    $('#carttform').hide();
                    $('#recartsearch').trigger('click');
                }
                $('.preloader').hide();
            }
        });
};

async function test() {

  try {
    const res = await getData()
    console.log(res)
  } catch(err) {
    console.log('Error');
  }
}*/





//$(function() {
    //$('#cartsearch').click(function () {
         //var prjid = $('#projct_id').val();
        
        //test();
        /*$.ajax({
            type: 'POST',
            url: '../procurement/cart',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            //data: {task:$('#searchtask').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#cartitems').html(data.result);
                    $('#cartitemstable').show();
                }
                $('.preloader').hide();
            }
        });*/
   // });
//});
/* $(function() {
    $('#cartsearch').click(function () {
        var prj = $('.repor').attr('data-id');

        $.ajax({
            type: 'POST',
            url: '../procurement/placeorders',
            dataType: "json",
            data: {projectid:prj},
            success: function (data) {
                if (data.error == 'No') {

                       // $('#prjnamee').html(data.project);
                        $('.placeorder-list').html(data.recart);
                        //$('#cartitemsss').html(data.cart);  
                     
                }
            }
        });


        });
}); */
$(function() {
    $('#prepeated').click(function () {
        

        if ( $(".repor").hasClass("active") ) {
            var prj = $('.repor.active').attr('data-id');
        }else{
            var prj = $('.repor').attr('data-id');
        }

        $('#identord').val('repeated');
        var type = $('#identord').val();

        $.ajax({
            type: 'POST',
            url: '../procurement/placeorders',
             beforeSend : function(){
               // $('.preloaderitems').show();
                $('.preloader').show();

            },
            dataType: "json",
            data: {projectid:prj,type:type},
            success: function (data) {
                if (data.error == 'No') {

                       // $('#prjnamee').html(data.project);
                       $('.placeorder-list').html(data.recart);
                        //$('#cartitemsss').html(data.cart);  
                     
                }
                $('.preloader').hide();
            }
        });


        });
});
$(function() {
    $('#ppurchor').click(function () { 
        if ( $(".repor").hasClass("active") ) {
            var prj = $('.repor.active').attr('data-id');
        }else{
            var prj = $('.repor').attr('data-id');
        }
        $('#identord').val('purchase');
        var type = $('#identord').val();

        $.ajax({
            type: 'POST',
            url: '../procurement/placeorders',
             beforeSend : function(){
               // $('.preloaderitems').show();
                $('.preloader').show();

            },
            dataType: "json",
            data: {projectid:prj,type:type},
            success: function (data) {
                if (data.error == 'No') {

                       // $('#prjnamee').html(data.project);
                       $('.placeorder-list').html(data.recart);
                        //$('#cartitemsss').html(data.cart);  
                     
                }
                 $('.preloader').hide();
            }
        });

        });
});
$(function() {
    $('#pworko').click(function () {
        if ( $(".repor").hasClass("active") ) {
            var prj = $('.repor.active').attr('data-id');
        }else{
            var prj = $('.repor').attr('data-id');
        }
        $('#identord').val('work');
        var type = $('#identord').val();

        $.ajax({
            type: 'POST',
            url: '../procurement/placeorders',
             beforeSend : function(){
               // $('.preloaderitems').show();
                $('.preloader').show();

            },
            dataType: "json",
            data: {projectid:prj,type:type},
            success: function (data) {
                if (data.error == 'No') {

                       // $('#prjnamee').html(data.project);
                       $('.placeorder-list').html(data.recart);
                        //$('#cartitemsss').html(data.cart);  
                     
                }
                 $('.preloader').hide();
            }
        });

        });
});
$(function() {
    $('#pdirec').click(function () {
        if ( $(".repor").hasClass("active") ) {
            var prj = $('.repor.active').attr('data-id');
        }else{
            var prj = $('.repor').attr('data-id');
        }
        $('#identord').val('direct');
        var type = $('#identord').val();

        $.ajax({
            type: 'POST',
            url: '../procurement/placeorders',
             beforeSend : function(){
               // $('.preloaderitems').show();
                $('.preloader').show();

            },
            dataType: "json",
            data: {projectid:prj,type:type},
            success: function (data) {
                if (data.error == 'No') {

                       // $('#prjnamee').html(data.project);
                       $('.placeorder-list').html(data.recart);
                        //$('#cartitemsss').html(data.cart);  
                     
                }
                $('.preloader').hide();
            }
        });

        });
});

$(function() {
    $('#pleaso').click(function () {
        if ( $(".repor").hasClass("active") ) {
            var prj = $('.repor.active').attr('data-id');
        }else{
            var prj = $('.repor').attr('data-id');
        }
        $('#identord').val('lease');
        var type = $('#identord').val();

        $.ajax({
            type: 'POST',
            url: '../procurement/placeorders',
                beforeSend : function(){
               // $('.preloaderitems').show();
                $('.preloader').show();

            },
            dataType: "json",
            data: {projectid:prj,type:type},
            success: function (data) {
                if (data.error == 'No') {

                       // $('#prjnamee').html(data.project);
                       $('.placeorder-list').html(data.recart);
                        //$('#cartitemsss').html(data.cart);  
                     
                }
                 $('.preloader').hide();
            }
        });

        });
});
$(function() {
    $('#pdespto').click(function () {
        if ( $(".repor").hasClass("active") ) {
            var prj = $('.repor.active').attr('data-id');
        }else{
            var prj = $('.repor').attr('data-id');
        }
        $('#identord').val('depatch');
        var type = $('#identord').val();

        $.ajax({
            type: 'POST',
            url: '../procurement/placeorders',
               beforeSend : function(){
               // $('.preloaderitems').show();
                $('.preloader').show();

            },
            dataType: "json",
            data: {projectid:prj,type:type},
            success: function (data) {
                if (data.error == 'No') {

                       // $('#prjnamee').html(data.project);
                       $('.placeorder-list').html(data.recart);
                        //$('#cartitemsss').html(data.cart);  
                     
                }
                 $('.preloader').hide(); 
            }
        });

        });
});
$(document).on('click','#projectdire',function(){

    var projid = $(this).attr('data-id');
    var type = $('#identord').val();
    $.ajax({
            type: 'POST',
            url: '../procurement/placeorders',
               beforeSend : function(){
               // $('.preloaderitems').show();
                $('.preloader').show();

            },
            dataType: "json",
            data: {projectid:projid,type:type},
            success: function (data) {
                if (data.error == 'No') {

                       // $('#prjnamee').html(data.project);
                        $('.placeorder-list').html(data.recart);
                        //$('#cartitemsss').html(data.cart);  
                     
                }
                 $('.preloader').hide();
            }
        });


    });
$(document).on('change','.cartresitemqty',function(){
    var qty=$(this).val();
    var resid=$(this).attr('data-id');
    var rate=$('#cartresitemrate'+resid).val();
    var amount=qty * rate;
    $('#order-amount'+resid).html(amount.toFixed(2));
    $.ajax({
        type: 'POST',
        url: '../procurement/Updatecartqty',
        dataType:"json",
        async: false,
        data:{resid:resid,qty:qty,amount:amount},
        success: function(data){

        }
    });
});
/*$(document).on('click','#cartitems input',function(){
    var values = [];
    var ordervalues = [];
    var restype = [];
    var project = [];
    $('#cartitems input:checked').each(function() {
        values.push($(this).val());
        ordervalues.push($(this).attr('data-id'));
        restype.push($(this).attr('data-restype'));
        project.push($(this).attr('data-project'));
    });
    $('[name="vendors"]').attr({value: values.join(', ')});
    $('[name="orders"]').attr({value: ordervalues.join(', ')});
    $('[name="restype"]').attr({value: restype.join(', ')});
    $('[name="project"]').attr({value: project.join(', ')});
});*/

// $(document).on('click','#placeorder',function(){
//     if($('.vendors').is(":checked")) {
//         var selvendors=$('#vendors').val();

//         var selrestype=$('#restype').val();
//         var selproject=$('#cartproject').val();
//         var arr = selvendors.split(',');
//         var restype = selrestype.split(',');
//         var project = selproject.split(',');
//         var sorted_arr = arr.slice().sort(); // You can define the comparing function here.
//         var restype_arr = restype.slice().sort(); // You can define the comparing function here.
//         var project_arr = project.slice().sort(); // You can define the comparing function here.
//         var count='';
//         for (var i = 0; i < arr.length; i++) {
//             if (parseInt(arr[i]) == parseInt(sorted_arr[i])) {
//                 count=0;
//             }
//             else {
//                 count++;
//             }
//         }
//         var restypecount='';
//         for (var i = 0; i < restype.length; i++) {
//             if (parseInt(restype[i]) == parseInt(restype_arr[i])) {
//                 restypecount=0;
//             }
//             else {
//                 restypecount++;
//             }
//         }
//         var projectcount='';
//         for (var i = 0; i < project.length; i++) {
//             if (parseInt(project[i]) == parseInt(project_arr[i])) {
//                 projectcount=0;
//             }
//             else {
//                 projectcount++;
//             }
//         }

//         if(count!=0){
//             alert('Please select only one vendor');
//             return false;
//         }
//         if(restypecount!=0){
//             alert('Please select only one resource type');
//             return false;
//         }
//         if(projectcount!=0){
//             alert('Please select only one project');
//             return false;
//         }
        
//         jQuery(document).on( "click", ".placeorder2", function(){
//             setTimeout(function(){
                    
//                  jQuery("html, body").animate({ scrollTop: jQuery('.acco-cart').offset().top }, 1000);               
//          },60);
//             setTimeout(function(){
//                      placeorderModal2(); 
//              },60);
            
//         }) ; 
        

//     }
//     else {
//         alert('Please select any vendor before proceeding.');
//         return false;
//     }
// });


$(document).on('click','#placeorder',function(){

    if($('.vendors').is(":checked")) {
        var selvendors=$('#vendors').val();
        var restype=$('#restype').val();
        var orders=$('#orders').val();
        if(selvendors==44){
            var vendorname="Company Owned";
        }
       
        

    }

    

    $('#placeorderdata').hide();   

    if(vendorname=='Company Owned' && restype!=33){

        $.ajax({
            type: 'POST',
            url: '../procurement/despatchorder',
            dataType: "json",
            data: {orders: orders,restype: restype},
            success: function (data) {
                if (data.error == 'No') {

                    if( vendorname=="Company Owned" && restype!=33){
                        ordertype="Despatch Order";
                        $('#despatchorderdata').html(data.despatchorder);  
                        $('#despatchorderdata').show();
                    }
                }
            }
        });

    }

    else{

        $.ajax({
            type: 'POST',
            url: '../procurement/order',
            dataType: "json",
            data: {orders: orders,restype: restype},
            success: function (data) {
                if (data.error == 'No') {

                    if(restype==19){   
                        ordertype="Work Order";  
                        $('#workorderdata').html(data.workorder);  
                        $('#workorderdata').show();
                    }
                    else if(restype==24){ 
                        ordertype="Lease Order";  
                         $('#leaseorderdata').html(data.leaseorderr);  
                         $('#leaseorderdata').show();

                    }
                    else if(restype==33){
                        ordertype="Direct Work Order";
                        $('#musterrolldata').html(data.musterroll);  
                         $('#musterrolldata').show();

                    }
                    else{
                        ordertype="Purchase Order";
                         $('#purchaseorder').html(data.purchaseorder);  
                         $('#purchaseorder').show();
                    }
                }
            }
        });

    }
});
/*$(document).on('click','#cartitems input',function(){
   
    $('#cartitems input:checked').each(function() {
        $vend_id = $(this).val();
        $prj = $(this).attr('data-project');
        $res = $(this).attr('data-restype');
        $cartid = $(this).attr('data-id')

        $('#pvenids').val($vend_id );
        $('#prresidss').val($res);
        $('#pprjss').val($prj);
        $('#pcrtid').val($cartid);

        
    });
   
});*/

$(document).on('click','#cartitems input',function(){
    var values = [];
    var ordervalues = [];
    var restype = [];
    var project = [];
    $('#cartitems input:checked').each(function() {
        values.push($(this).val());
        ordervalues.push($(this).attr('data-id'));
        restype.push($(this).attr('data-restype'));
        project.push($(this).attr('data-project'));
    });
    $('[name="pvenids"]').attr({value: values.join(', ')});
    $('[name="pcrtid"]').attr({value: ordervalues.join(', ')});
    $('[name="prresidss"]').attr({value: restype.join(', ')});
    $('[name="pprjss"]').attr({value: project.join(', ')});
});

$(document).on('click','#placeorder_new',function(){

    if($('.vendors').is(":checked")) {
        qtyError = 0;
        $( ".editresreqty" ).each(function( index ) {
            var cartid = $(this).attr('data-id');
            var ven = $(this).attr('data-v');

            var req = $('#editresreqty'+cartid).val();
            var rate = $('#pooeditrate'+cartid).val();
            var eqy = $('#estqy'+cartid).val();
            var amnt = eqy * rate;
            var albal = $('#cartbalancetotal'+cartid).html();
            var bal = eqy-req;

            if(req != '')
                 var tot = req * rate;
            if(parseInt(req) > parseInt(eqy))
                qtyError++;
             /*if(req != ''){
                $('#restotamount'+cartid).html(tot.toFixed(2));
                $('#resstotamount'+cartid).val(tot);
                $('#cartbalancetotal'+cartid).html(bal.toFixed(3));
            }else{
                $('#restotamount'+cartid).html(amnt.toFixed(2));
                $('#resstotamount'+cartid).val(amnt);
                $('#cartbalancetotal'+cartid).html(albal.toFixed(3));
            }
           var amn = 0;
            $('.resvtot_'+ven).each(function(){
                var samt = $(this).val();
                amn = parseInt(amn) + parseInt(samt);
            });
            $('#resvenns'+ven).html(amn.toFixed(2));*/
        });

        if(qtyError > 0){
            if(confirm('Required Quantity is greater than Estimated Quantity! Do you want to Continue?'))
               qtyError = 0;
        }

        if(qtyError == 0){
            var venid = $(this).attr('data-ven');
            var proid = $(this).attr('data-pro');
            var resid = $('#prresidss').val();
            var cartid = $('#pcrtid').val();

            $.ajax({ 
                type: 'POST',
                url: '../procurement/ordernew',
                dataType: "json",
                data: {venid: venid,proid: proid,mode:1,resid:resid,cartid:cartid},
                success: function (data) {
                    if (data.error == 'No') {

                        if(data.order == 'purchase'){
                            ordertype="Purchase Order";
                            $('#carttform').hide();
                            $('#recartform').hide();
                            $('#workform').hide();
                            $('#directworkform').hide();
                            $('#leaseform').hide();
                            $('#despform').hide();
                            $('.headngstyle').hide();
                            $('.orders-wrpr').hide();
                            $('.placeorder-list').hide();
                            $('.topsbars').hide();
                            $('#purchaseorder').show();
                            $('#purchaseorder').html(data.purchaseorder);  
                        }
                    }
                }
            });
        }

    }else{
        alert('Please select any vendor before proceeding.');
         return false;
    }

});


$(document).on('click','.removevendor',function(){  
    var id=$(this).val();
    $.ajax({
        type: 'POST',
        url: '../procurement/removeitem',
        beforeSend : function(){
            $('#removevendor'+id).attr("disabled", true);
        },
        dataType:"json",
        data:{id:id},
        success: function(data){
            if(data.error=='No')
            {
                $('#cartitemrow'+id).remove();
            }
            else
            {
                alert(data.errortext);
            }

            $('#removevendor'+id).attr("disabled", false);

        }
    });

});




$(document).on('click','#cancelorder',function(){  
 

//$(document).on('click','#purchaseorder,a.cancel',function(){  


            $('.topsbars').show();
         $('#purchaseorder').hide();
            
          //$('#placeorderdata').show();

          $('#collapsecart').show();

          $('#carttform').show();
          $('#recartform').show();
          $('#workform').show();
          $('#directworkform').show();
          $('#leaseform').show();
          $('#despform').show();
          $('.headngstyle').show();
          $('.orders-wrpr').show();
          $('.placeorder-list').show();
            
            // setTimeout(function(){

            //     parent.closeFrame2();
                    
            // },500);
                      
            /*parent.window.close();
            window.onunload = function (e) { 
                
             opener.refreshParentWindow();  
             };  

            $.ajax({
                type: 'POST',
                url: '../procurement/droptemp',
                beforeSend : function(){
                },
                dataType:"json",
                data:{},
                success: function(data){
                }
            }); */        
            
        });



 $(document).on('click','#cancelorderworkorder',function(){   


      
         $('#workorderdata').hide();
            
          $('#placeorderdata').show();
            
            // setTimeout(function(){

            //     parent.closeFrame2();
                    
            // },500);
                      
            parent.window.close();
            window.onunload = function (e) { 
                
             opener.refreshParentWindow();  
             };

    $.ajax({
        type: 'POST',
        url: '../procurement/droptemp',
        beforeSend : function(){
        },
        dataType:"json",
        data:{},
        success: function(data){
        }
    });              
            
        });



 $(document).on('click','#cancelorderleaseorder',function(){   


      
         $('#leaseorderdata').hide();
            
          $('#placeorderdata').show();
            
            // setTimeout(function(){

            //     parent.closeFrame2();
                    
            // },500);
                      
            parent.window.close();
            window.onunload = function (e) { 
                
             opener.refreshParentWindow();  
             };

        $.ajax({
        type: 'POST',
        url: '../procurement/droptemp',
        beforeSend : function(){
        },
        dataType:"json",
        data:{},
        success: function(data){
        }
    });              
            
        });



  $(document).on('click','#cancelordermusterroll',function(){   


      
         $('#musterrolldata').hide();
            
          $('#placeorderdata').show();
            
            // setTimeout(function(){

            //     parent.closeFrame2();
                    
            // },500);
                      
            parent.window.close();
            window.onunload = function (e) { 
                
             opener.refreshParentWindow();  
             };            
    $.ajax({
        type: 'POST',
        url: '../procurement/droptemp',
        beforeSend : function(){
        },
        dataType:"json",
        data:{},
        success: function(data){
        }
    });  
        });


  $(document).on('click','#cancelorderdespatch',function(){   


      
         $('#despatchorderdata').hide();
            
          $('#placeorderdata').show();
            
            // setTimeout(function(){

            //     parent.closeFrame2();
                    
            // },500);
                      
            parent.window.close();
            window.onunload = function (e) { 
                
             opener.refreshParentWindow();  
             }; 


    $.ajax({
        type: 'POST',
        url: '../procurement/droptemp',
        beforeSend : function(){
        },
        dataType:"json",
        data:{},
        success: function(data){
        }
    }); 


            
        });



  


 



  $(document).on('focus','#dateofdelivery',function(){
        $(this).datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
    });

   $(document).on('focus','#orderofdate',function(){
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

     var restype=$('#restype').val();
       var orders=$('#orders').val();

     // var origin=window.location.origin;
     //        var pathname=window.location.pathname;
     //        var pathArray = window.location.pathname.split( '/' );
     //        var formEl = document.forms.cartform;
     //        var formData = new FormData(formEl);
     //        var orders = formData.get('orders');
     //        var restype = formData.get('restype');
     //        //console.log(name)
     //        var baseUrl=origin+"/"+pathArray[1]+"/"+pathArray[2]+"/order?orders="+orders+"&restype="+restype;
            
            //alert(baseUrl);
           // console.log(baseUrl)
           

           

          //alert(baseUrl);

        var error=0;
        $('.error').hide();
        if($('#specification').val()=='')
        {
            $('#specification').next("span").html('Enter Description').show('slow');
            error=1;
        }
        /*if($('#advance').val()=='')
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
            $('#payment').next("span").html('Enter payment mode').show('slow');
            error=1;
        }
        if($('#place').val()=='')
        {
            $('#place').next("span").html('Enter Place of Delivery').show('slow');
            error=1;
        }*/
        if($('#cgst').val()=='' && $('#igst').val()==''){
            //alert("Please enter either GST / IGST tax.");
            $('#commonError').html('Please enter either GST / IGST tax').show('slow');
            error=1;
        }

        if($('#cgst').val()!=''){
            if($('#igst').val()!=''){
                //alert("You can not select both IGST as well as the other tax.");
                $('#commonError').html('You can not select both IGST as well as the other tax').show('slow');
                error=1;
            }
        }

       // orderofdate

       //dateofdelivery

        var orderofdate=$('#orderofdate').val();

        var dateofdelivery=$('#dateofdelivery').val();
       
         if(dateofdelivery < orderofdate){
            //alert("Order date is later than the order delivery date.");
            $('#dateofdelivery').next("span").html('Delivery Date should later than Order Date.').show('slow');
                error=1;
        }

        //  if(dateofdelivery<orderofdate){
        //     alert("Order date is later than the order delivery date.");
        //         error=1;
        // }



        if(error==0){

               $('#purchaseorder').hide();
            
         

             //$('#placeOrderiframe').attr('src', baseUrl);
              $('#placeorderdata').show();

            
        }
        else{
            return  false;
        }
        
        
        // function UnloadWndw(){
        //     window.onunload = function (e) {  
        //         opener.refreshParentWindow();  
        //      }; 
            
        // }
        
    });



    $(document).ready(function() {
        //this calculates values automatically 
        //subAmount();
        $("#sub_total, #tax").on("keydown keyup", function() {
            subAmount();
        });
    });

$(document).ready(function(){
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
             //$("#tax").val("");
        }
    };
 
  });
$(document).on('keyup','#workgst',function(){
    var amount = $('#sub_wrktotal').val();
    
    var gst = $('#workgst').val();
    var total = amount;
    var tot_price = (total * gst / 100);
    var finaltotal=  tot_price;
    var subtotal = amount;

    var finalnumber= Number(subtotal) + Number(finaltotal); 
    var  final=parseFloat(finalnumber).toFixed(2);

    $('.wrktotal').val(final);
}); 

$(document).ready(function(){

  /*$(document).on('keyup','#cgst',function(){ 
    //$('#cgstpurchaseorder').keyup(function() {  alert ("hi");

        var amount = $('#sub_total').val();
        var gst = $('#cgst').val();
        var total = amount;
        var tot_price = (total * gst / 100);   
        var finaltax= parseFloat(tot_price).toFixed(2);   
        //$('#tax').val(finaltax);

        var orderID = $(this).attr("data-id");
        var gst_value = $('.cgstvalue'+orderID).val();
        var amtvalue = $('.amntvalue'+orderID).val();
        var tot_price_value = (amtvalue * gst_value / 100);   
        var finaltax_value= parseFloat(tot_price_value).toFixed(2);   
        $('#taxvalue'+orderID).val(finaltax_value);

        if(gst!=''){
            $('.igstvalue'+orderID).prop('disabled', true);
        }
        else{
            $('.igstvalue'+orderID).prop('disabled', false);
        }

        var freight = $('#freight').val();
        var insurance = $('#insurance').val();
        var others = $('#others').val();

        //alert (finaltotal);
        var finaltotal=  finaltax; 
        var subtotal = amount;  
        var finalnumber= Number(subtotal) + Number(finaltotal) + Number(freight) + Number(insurance) + Number(others); 
        var  final=parseFloat(finalnumber).toFixed(2);  

        $('.total').val(final);  // alert(final);

    });*/


     $(document).on('keyup','#freight',function(){ 
    //$('#cgstpurchaseorder').keyup(function() {  alert ("hi");

        var amount = $('#sub_total').val();
        if($('#cgst').val()!=''){
            var gst = $('#cgst').val();
        }
        else{
            var gst = $('#igst').val();
        }
        
        var total = amount;
        var tot_price = (total * gst / 100);   
        var finaltax= parseFloat(tot_price).toFixed(2);   
        //$('#tax').val(finaltax);
        var freight = $('#freight').val();
        var insurance = $('#insurance').val();
        var others = $('#others').val();
        //alert (finaltotal);
        var finaltotal=  finaltax; 
        var subtotal = amount;  
        var finalnumber= Number(subtotal) + Number(finaltotal) + Number(freight) + Number(insurance) + Number(others); 
        var  final=parseFloat(finalnumber).toFixed(2);  

        $('.total').val(final);  // alert(final);

    });


      $(document).on('keyup','#insurance',function(){ 
    //$('#cgstpurchaseorder').keyup(function() {  alert ("hi");

        var amount = $('#sub_total').val();
        if($('#cgst').val()!=''){
            var gst = $('#cgst').val();
        }
        else{
            var gst = $('#igst').val();
        }
        var total = amount;
        var tot_price = (total * gst / 100);   
        var finaltax= parseFloat(tot_price).toFixed(2);   
        //$('#tax').val(finaltax);
        var freight = $('#freight').val(); 
        var insurance = $('#insurance').val();
        var others = $('#others').val(); 
        //alert (finaltotal);
        var finaltotal=  finaltax; 
        var subtotal = amount;  
        var finalnumber= Number(subtotal) + Number(finaltotal) + Number(freight) + Number(insurance) + Number(others); 
        var  final=parseFloat(finalnumber).toFixed(2);  

        $('.total').val(final);  // alert(final);

    });



      $(document).on('keyup','#others',function(){ 
    //$('#cgstpurchaseorder').keyup(function() {  alert ("hi");

        var amount = $('#sub_total').val();
        if($('#cgst').val()!=''){
            var gst = $('#cgst').val();
        }
        else{
            var gst = $('#igst').val();
        }
        var total = amount;
        var tot_price = (total * gst / 100);   
        var finaltax= parseFloat(tot_price).toFixed(2);   
        //$('#tax').val(finaltax);
        var freight = $('#freight').val(); 
        var insurance = $('#insurance').val(); 
        var others = $('#others').val(); 
        //alert (finaltotal);
        var finaltotal=  finaltax; 
        var subtotal = amount;  
        var finalnumber= Number(subtotal) + Number(finaltotal) + Number(freight) + Number(insurance) + Number(others); 
        var  final=parseFloat(finalnumber).toFixed(2);  

        $('.total').val(final);  // alert(final);

    });



      $(document).on('keyup','#otherss',function(){ 
    //$('#cgstpurchaseorder').keyup(function() {  alert ("hi");

        var amount = $('#sub_total').val();
        var gst = $('#cgst').val();
        var total = amount;
        var tot_price = (total * gst / 100);   
        var finaltax= parseFloat(tot_price).toFixed(2);   
        //$('#tax').val(finaltax);
        var freight = $('#freight').val(); 
        var insurance = $('#insurance').val(); 
        var otherss = $('#otherss').val(); 
        //alert (finaltotal);
        var finaltotal=  finaltax; 
        var subtotal = amount;  
        var finalnumber= Number(amount) + Number(otherss); 
        var  final=parseFloat(finalnumber).toFixed(2);  

        $('.total').val(final);  // alert(final);

    });




    });


   /*$(document).ready(function(){  

  $(document).on('keyup','#igst',function(){ 

    //$('#igst').keyup(function(ev) {  

        var amount = $('#sub_total').val();
        //var qty = $('#qty').val();
        var igst = $('#igst').val();
        //$(this).val(); 
        var total = amount;
        var tot_price = (total * igst / 100);
        var finaltax= parseFloat(tot_price).toFixed(2);   
        //$('#tax').val(finaltax);

        var orderID = $(this).attr("data-id");
        var gst_value = $('.igstvalue'+orderID).val();
        var amtvalue = $('.amntvalue'+orderID).val();
        var tot_price_value = (amtvalue * gst_value / 100);   
        var finaltax_value= parseFloat(tot_price_value).toFixed(2);   
        $('#taxvalue'+orderID).val(finaltax_value);

        if(igst!=''){
            $('.cgstvalue'+orderID).prop('disabled', true);
        }
        else{
            $('.cgstvalue'+orderID).prop('disabled', false);
        }

        var freight = $('#freight').val();
        var insurance = $('#insurance').val();
        var others = $('#others').val();

        var finaltotal=  finaltax; 
        var subtotal = amount;  
        var finalnumber= Number(subtotal) + Number(finaltotal) + Number(freight) + Number(insurance) + Number(others); 
        var  final=parseFloat(finalnumber).toFixed(2);  
        $('.total').val(final);  // alert(final);
        
    });

});
*/

     $(document).ready(function(){  

 

     //new on change function for multiple DWO place order

      /*$(document).on('keyup','.nfw',function(){  
  
            var rowresid = $(this).data('id'); 
            var workers = $('.noofworkerss'+rowresid).val();
            if(workers > 0 && workers !=null)
            {
                var days = $('.noofdayss'+rowresid).val();
                var rates = $('.dirrate'+rowresid).val();
                
                var amount = workers * days * rates;
                var mandays = workers * days;
            
                var finalamount= parseFloat(amount).toFixed(2);   
             
                var finaltotal=  finalamount; 

                var  final=parseFloat(finaltotal).toFixed(2);  
                $('.totalamt'+rowresid).val(final);  
                $('.noofmandayss'+rowresid).val(mandays);  
                $('.noofworkerss'+rowresid).val(workers); 
            }
            
        });*/

        $(document).on('keyup','.nfw',function(){  
  
            var rowresid = $(this).data('id'); 
            var workers = $('.noofworkerss'+rowresid).val();
            if(workers > 0 && workers !=null)
            {
                var noofmandayss = $('.noofmandayss'+rowresid).val();

                noofdayss = Math.ceil(noofmandayss / workers);
                $('.noofdayss'+rowresid).val(noofdayss); 
            }
            
        });

        $(document).on('keyup','.nfd',function(){  
  
            var rowresid = $(this).data('id'); 
            var workers = $('.noofworkerss'+rowresid).val();
            var days = $('.noofdayss'+rowresid).val();
            if(days > 0 && days !=null)
            { 
                var noofmandayss = $('.noofmandayss'+rowresid).val();

                noofworkerss = Math.ceil(noofmandayss / days);
                $('.noofworkerss'+rowresid).val(noofworkerss); 
            }
            
        });

        /*$(document).on('keyup','.nfd',function(){  

            var rowresid = $(this).data('id'); 

            var workers = $('.noofworkerss'+rowresid).val();
            var days = $('.noofdayss'+rowresid).val();
            if(days > 0 && days !=null)
            { 
                
                var rates = $('.dirrate'+rowresid).val();
            
                var amount = workers * days * rates;
                var mandays = workers * days;
                
                var finalamount= parseFloat(amount).toFixed(2);   
              
                var finaltotal=  finalamount; 

                var  final=parseFloat(finaltotal).toFixed(2);  
                $('.totalamt'+rowresid).val(final);  // alert(final);
                $('.noofmandayss'+rowresid).val(mandays); 
                $('.noofdayss'+rowresid).val(days); 
            }
        });*/

        $(document).on('keyup','.nfmd',function(){  

     
            var rowresid = $(this).data('id'); 

            var mandayss = $('.noofmandayss'+rowresid).val();
            if(mandayss > 0 && mandayss !=null)
            {

                var rates = $('.dirrate'+rowresid).val();
            
                var amount = mandayss * rates;
              
                var finalamount= parseFloat(amount).toFixed(2);   
           
                var finaltotal=  finalamount; 

                var  final=parseFloat(finaltotal).toFixed(2);  
                $('.totalamt'+rowresid).val(final); 
            }
            
        });

        $(document).on('keyup','.nfdr',function(){  

     
            var rowresid = $(this).data('id'); 
            var mandayss = $('.noofmandayss'+rowresid).val();
            
            if(mandayss > 0 && mandayss !=null)
            {

                var rates = $('.dirrate'+rowresid).val();
            
                var amount = mandayss * rates;
              
                var finalamount= parseFloat(amount).toFixed(2);   
           
                var finaltotal=  finalamount; 

                var  final=parseFloat(finaltotal).toFixed(2);  
                $('.totalamt'+rowresid).val(final); 

                $('.directrate'+rowresid).val(rates); 
            }
            
        });



         $(document).on('keyup','#ottrates',function(){  

       

            var workers = $('#noofworkerss').val();
           
            var days = $('#noofdayss').val();
            var rates = $('#ottrates').val();
          
         
            var amount = workers * days * rates;
         

            var finalamount= parseFloat(amount).toFixed(2);   
         
            var finaltotal=  finalamount; 

            var  final=parseFloat(finaltotal).toFixed(2);  
            $('.totalamt').val(final);  
             
            
        });

     //end

     //confirm order screen validatins
     $(document).on('keyup','.cfnfw',function(){  
  
            var rowresid = $(this).data('id'); 
            var workers = $('.cfnoofworkerss'+rowresid).val();
            if(workers > 0 && workers !=null)
            {
                var days = $('.cfnoofdayss'+rowresid).val();
                var rates = $('.cfdirratehid'+rowresid).val();
                
                var amount = workers * days * rates;
                var mandays = workers * days;
            
                var finalamount= parseFloat(amount).toFixed(2);   
             
                var finaltotal=  finalamount; 

                var  final=parseFloat(finaltotal).toFixed(2);  
                $('.cftotalamt'+rowresid).val(final);  
                $('.cfftotalamt'+rowresid).val(final);  
                $('.cfnoofmandayss'+rowresid).val(mandays);  
                $('.cfnoofworkerss'+rowresid).val(workers); 
            }
            
        });


        $(document).on('keyup','.cfnfd',function(){  

            var rowresid = $(this).data('id'); 

            var workers = $('.cfnoofworkerss'+rowresid).val();
            var days = $('.cfnoofdayss'+rowresid).val();
            if(days > 0 && days !=null)
            { 
                
                var rates = $('.cfdirratehid'+rowresid).val();
            
                var amount = workers * days * rates;
                var mandays = workers * days;
                
                var finalamount= parseFloat(amount).toFixed(2);   
              
                var finaltotal=  finalamount; 

                var  final=parseFloat(finaltotal).toFixed(2);  
                $('.cftotalamt'+rowresid).val(final);  
                $('.cfftotalamt'+rowresid).val(final); 
                $('.cfnoofmandayss'+rowresid).val(mandays); 
                $('.cfnoofdayss'+rowresid).val(days); 
            }
        });

        $(document).on('keyup','.cfnfmd',function(){  

     
            var rowresid = $(this).data('id'); 

            var mandayss = $('.cfnoofmandayss'+rowresid).val();
            if(mandayss > 0 && mandayss !=null)
            {

                var rates = $('.cfdirratehid'+rowresid).val();
       
                var amount = mandayss * rates; 
                
                var finalamount= parseFloat(amount).toFixed(2);   
         
                var finaltotal=  finalamount; 

                var  final=parseFloat(finaltotal).toFixed(2); 
                $('.cftotalamt'+rowresid).val(final);
                $('.cfftotalamt'+rowresid).val(final); 
            }
            
        });

         $(document).on('keyup','.cfffrate',function(){  

          var rowresid = $(this).data('id'); 
      

            var mandayss = $('.cfnoofmandayss'+rowresid).val();
             
            if(mandayss > 0 && mandayss !=null)
            {

                var str = $('.cfdirrate'+rowresid).val();
                var rates = str.replace(/,/g, '');
                var amount = mandayss * rates;
              
                var finalamount= parseFloat(amount).toFixed(2);   
           
                var finaltotal=  finalamount; 

                var  final=parseFloat(finaltotal).toFixed(2);  
                $('.cftotalamt'+rowresid).val(final); 
                $('.cfftotalamt'+rowresid).val(final);
                $('.cfdirratehid'+rowresid).val(rates); 
            }   
              
            
        });



         $(document).on('keyup','.cffotrate',function(){  

             var rowresid = $(this).data('id'); 
             var cffotrate = $('.cfdirotrates'+rowresid).val();
            $('.cfdirotrates'+rowresid).val(cffotrate);   
              
            
        });

        //confirm order screen end

        //amend order screen validatins
     $(document).on('keyup','.amdnfw',function(){  
  
            var rowresid = $(this).data('id'); 
            var workers = $('.amdnoofworkerss'+rowresid).val();
            if(workers > 0 && workers !=null)
            {
                var days = $('.amdnoofdayss'+rowresid).val();
                var rates = $('.amddirratehid'+rowresid).val();
                
                var amount = workers * days * rates;
                var mandays = workers * days;
            
                var finalamount= parseFloat(amount).toFixed(2);   
             
                var finaltotal=  finalamount; 

                var  final=parseFloat(finaltotal).toFixed(2);  
                $('.amdtotalamt'+rowresid).val(final);  
                $('.amdftotalamt'+rowresid).val(final);  
                $('.amdnoofmandayss'+rowresid).val(mandays);  
                $('.amdnoofworkerss'+rowresid).val(workers); 
            }
            
        });


        $(document).on('keyup','.amdnfd',function(){  

            var rowresid = $(this).data('id'); 

            var workers = $('.amdnoofworkerss'+rowresid).val();
            var days = $('.amdnoofdayss'+rowresid).val();
            if(days > 0 && days !=null)
            { 
                
                var rates = $('.amddirratehid'+rowresid).val();
            
                var amount = workers * days * rates;
                var mandays = workers * days;
                
                var finalamount= parseFloat(amount).toFixed(2);   
              
                var finaltotal=  finalamount; 

                var  final=parseFloat(finaltotal).toFixed(2);  
                $('.amdtotalamt'+rowresid).val(final);  
                $('.amdftotalamt'+rowresid).val(final); 
                $('.amdnoofmandayss'+rowresid).val(mandays); 
                $('.amdnoofdayss'+rowresid).val(days); 
            }
        });

        $(document).on('keyup','.amdnfmd',function(){  

     
            var rowresid = $(this).data('id'); 

            var mandayss = $('.amdnoofmandayss'+rowresid).val();
            if(mandayss > 0 && mandayss !=null)
            {

                var rates = $('.amddirratehid'+rowresid).val();
       
                var amount = mandayss * rates; 
                
                var finalamount= parseFloat(amount).toFixed(2);   
         
                var finaltotal=  finalamount; 

                var  final=parseFloat(finaltotal).toFixed(2); 
                $('.amdtotalamt'+rowresid).val(final);
                $('.amdftotalamt'+rowresid).val(final); 
            }
            
        });

         $(document).on('keyup','.amdffrate',function(){  

             var rowresid = $(this).data('id'); 
             var mandayss = $('.amdnoofmandayss'+rowresid).val();
                if(mandayss > 0 && mandayss !=null)
                {

                    var str = $('.amddirrate'+rowresid).val(); 
                    var rates = str.replace(/,/g, '');
                 
                    var amount = mandayss * rates; 
                    
                    var finalamount= parseFloat(amount).toFixed(2);   
             
                    var finaltotal=  finalamount; 

                    var  final=parseFloat(finaltotal).toFixed(2); 
                    $('.amdtotalamt'+rowresid).val(final);
                    $('.amdftotalamt'+rowresid).val(final); 
                    $('.amddirratehid'+rowresid).val(rates); 
                    $('.amddirrate'+rowresid).val(rates);
                }

             //var amdfrate = $('.amddirrate'+rowresid).val();
              
              
            
        });

         $(document).on('keyup','.amdfotrate',function(){  

             var rowresid = $(this).data('id'); 
             var amdfotrate = $('.amddirotrates'+rowresid).val();
            $('.amddirotrates'+rowresid).val(amdfotrate);   
              
            
        });

        //amend order screen end

        //amend -work odr -edit strt

        $(document).on('keyup','.awr',function(){

            var rowresid = $(this).data('id');  
            var str = $(this).val();
            var rate = str.replace(/,/g, ''); 

            var quantity = $('.amddwoqnty'+rowresid).val();

            if(quantity > 0 && quantity!= null)
            {
                var amount1 = rate * quantity; 
                var  amount=parseFloat(amount1).toFixed(2); 
                $('.amddwoamount'+rowresid).val(amount);
                $('.amddhidwoamount'+rowresid).val(amount); 
                $('.amddhidworate'+rowresid).val(rate);

                var total = 0; 
           
                $('.awa').each(function(index) {
                    var amnt = $(this).val();
                    var rowresid = $(this).data('id');  
                    var tot = amnt.replace(/,/g, '');
                    total += parseInt(tot); 
                    if(total>0 && total!= null)
                    {
                        
                        var totals = total.toFixed(2); 
                        $('.amdhidsubtot'+rowresid).val(totals);
                        $('.amdsubtot'+rowresid).val(totals);

                        //gst calcultion
                        var gst = 0;
                        var gstamnt = 0;
                        var totalamount = 0;
                        var gst = $('.amdgst').val(); 
                        var gstamnt = parseFloat((total*gst)/100); 
                        var tot_tax = Number(total)+Number(gstamnt);
                        var others = $('#otherswo').val();   
                        if(others>0 && others!= null)
                        {
                            var totalss = Number(tot_tax)+ Number(others);
                        }
                        else
                        {
                            var totalss = Number(tot_tax);
                        } 

                        var totalamount = parseFloat(totalss).toFixed(2); 
                        $('.amdgrandtot').val(totalamount);
                           
                    }
                    
                });
            }

        });

        $(document).on('keyup','.awq',function(){

            var rowresid = $(this).data('id');  
            var quantity = $(this).val();
            
            var str = $('.amddworate'+rowresid).val();
            var rate = str.replace(/,/g, ''); 

            if(quantity > 0 && quantity!= null)
            {
                var amount1 = rate * quantity; 
                var  amount=parseFloat(amount1).toFixed(2); 
                $('.amddwoamount'+rowresid).val(amount);
                $('.amddhidwoamount'+rowresid).val(amount); 
                $('.amddhidwoqnty'+rowresid).val(quantity);

                var total = 0; 
                $('.awa').each(function(index) {
                    var amnt = $(this).val();
                    var rowresid = $(this).data('id');  
                    var tot = amnt.replace(/,/g, '');
                    total += parseInt(tot); 
                    if(total>0 && total!= null)
                    {
                        
                        var totals = total.toFixed(2); 
                        $('.amdhidsubtot'+rowresid).val(totals);
                        $('.amdsubtot'+rowresid).val(totals);

                        //gst calcultion
                        var gst = 0;
                        var gstamnt = 0;
                        var totalamount = 0;
                        var gst = $('.amdgst').val(); 
                        var gstamnt = parseFloat((total*gst)/100); 
                        var tot_tax = Number(total)+Number(gstamnt);
                        var others = $('#otherswo').val();   
                        if(others>0 && others!= null)
                        {
                            var totalss = Number(tot_tax)+ Number(others);
                        }
                        else
                        {
                            var totalss = Number(tot_tax);
                        } 

                        var totalamount = parseFloat(totalss).toFixed(2); 
                        $('.amdgrandtot').val(totalamount);
                           
                    }
                  
                });
            }

        });


        $(document).on('keyup','.amdgst',function(){
            var gst = $(this).val();
            $('.amdhidgst').val(gst); 
        });  



        $(document).on('keyup','#otherswo',function(){
            var others = $(this).val();
            var subt = $('#apprsub_total').val();  
            var subtot = subt.replace(/,/g, ''); 
            var gst = $('.amdhidgst').val();
            var taxs = parseFloat((subtot * gst) / 100);
            var subtotal = Number(taxs)+Number(subtot);
            var tot = Number(subtotal)+Number(others);
            var total = parseFloat(tot).toFixed(2); 
            $('.amdgrandtot').val(total);
            $('.amdhidwogrndtot').val(tot);

        }); 


        //amend -work odr -edit end

        //confirm order wo,lo strt

        $(document).on('keyup','.othercfwolo',function(){ 
            var others = $(this).val();
            var subt = $('.cfsubtotal').val();  
            var subtot = subt.replace(/,/g, ''); 
            var gst = $('.cfgst').val();
            var taxs = parseFloat((subtot * gst) / 100); 
            var subtotal = Number(taxs)+Number(subtot);
            var tot = Number(subtotal)+Number(others);
            var total = parseFloat(tot).toFixed(2); 
            $('.cftotal').val(total);


        }); 

        //confirm order wo,lo end


        //amend -purchase odr -edit strt

        $(document).on('keyup','.apr',function(){

            var rowresid = $(this).data('id');  
            var str = $(this).val();
            var rate = str.replace(/,/g, ''); 

            var quantity = $('.amddpoqnty'+rowresid).val();

            if(quantity > 0 && quantity!= null)
            {
                var amount1 = rate * quantity; 
                var  amount=parseFloat(amount1).toFixed(2); 
                $('.amddpoamount'+rowresid).val(amount);
                $('.amddhidpoamount'+rowresid).val(amount); 
                $('.amddhidporate'+rowresid).val(rate);

                var total = 0; 
                $('.apa').each(function(index) {
                    var amnt = $(this).val();
                    var rowresid = $(this).data('id');  
                    var rowresid = $(this).data('id');  
                    var tot = amnt.replace(/,/g, '');
                    total += parseInt(tot);  
                   
                    
                    
                });

                if(total>0 && total!= null)
                {
                    var totals = total.toFixed(2); 
                    $('.amdpohidsubtot').val(totals);
                    $('.amdposubtot').val(totals);

                    var gst1 = $('.cgstpo'+rowresid).val(); 
                    var gst2 = $('.igstpo'+rowresid).val();  
                    if(gst1!='' && (gst2=='' || gst2==0))
                    {
                        gst = gst1;
                    }
                    else if(gst2!='' && (gst1=='' || gst1==0))
                    {
                        gst = gst2;
                    }  
                  /*  else if(gst1!='' && gst2!='')
                    {
                        gst = 
                    }*/  
                    var tax = (total * gst / 100);  

                    //var tax1 = $('#amdhidpotax').val();
                   // var tax = tax1.replace(/,/g, ''); 
                    var total1 = $('#amdpohidsubtot').val(); 
                    var total = total1.replace(/,/g, ''); 
                    var freight1 = $('#amdhidpofreight').val();
                    var freight = freight1.replace(/,/g, ''); 
                    var insurance1 = $('#amdhidpoinsurance').val();
                    var insurance = insurance1.replace(/,/g, ''); 
                    var others1 = $('#amdhidpoothers').val();
                    var others = others1.replace(/,/g, ''); 

                    var grandtotals = Number(total) + Number(tax) + Number(freight) + Number(insurance) + Number(others); 
                    var grandtotal =  parseFloat(grandtotals).toFixed(2);
                    $('#apprtax').val(tax.toFixed(2));
                    $('#apprpototal').val(grandtotal);  
                    $('#amdhidpogrndtot').val(grandtotal); 


                }

                
            }

        });

        $(document).on('keyup','.apq',function(){

            var rowresid = $(this).data('id');  
            var quantity = $(this).val();
            
            var str = $('.amddporate'+rowresid).val();
            var rate = str.replace(/,/g, ''); 

            if(quantity > 0 && quantity!= null)
            {
                var amount1 = rate * quantity; 
                var  amount=parseFloat(amount1).toFixed(2); 
                $('.amddpoamount'+rowresid).val(amount);
                $('.amddhidpoamount'+rowresid).val(amount); 
                $('.amddhidporate'+rowresid).val(rate);

                var total = 0; 
                $('.apa').each(function(index) {
                    var amnt = $(this).val();
                    var rowresid = $(this).data('id');  
                    var rowresid = $(this).data('id');  
                    var tot = amnt.replace(/,/g, '');
                    total += parseInt(tot);  
                   
                    
                    
                });

                if(total>0 && total!= null)
                {
                    var totals = total.toFixed(2); 
                    $('.amdpohidsubtot').val(totals);
                    $('.amdposubtot').val(totals);

                    var gst1 = $('.cgstpo'+rowresid).val(); 
                    var gst2 = $('.igstpo'+rowresid).val();  
                    if(gst1!='' && (gst2=='' || gst2==0))
                    {
                        gst = gst1;
                    }
                    else if(gst2!='' && (gst1=='' || gst1==0))
                    {
                        gst = gst2;
                    }  
                  /*  else if(gst1!='' && gst2!='')
                    {
                        gst = 
                    }*/  
                    var tax = (total * gst / 100);  

                    //var tax1 = $('#amdhidpotax').val();
                   // var tax = tax1.replace(/,/g, ''); 
                    var total1 = $('#amdpohidsubtot').val(); 
                    var total = total1.replace(/,/g, ''); 
                    var freight1 = $('#amdhidpofreight').val();
                    var freight = freight1.replace(/,/g, ''); 
                    var insurance1 = $('#amdhidpoinsurance').val();
                    var insurance = insurance1.replace(/,/g, ''); 
                    var others1 = $('#amdhidpoothers').val();
                    var others = others1.replace(/,/g, ''); 

                    var grandtotals = Number(total) + Number(tax) + Number(freight) + Number(insurance) + Number(others); 
                    var grandtotal =  parseFloat(grandtotals).toFixed(2);
                    $('#apprtax').val(tax.toFixed(2));
                    $('#apprpototal').val(grandtotal);  
                    $('#amdhidpogrndtot').val(grandtotal); 


                }

                
            }


        });



        $(document).on('keyup','.amdgst',function(){
            var gst = $(this).val();
            $('.amdhidgst').val(gst); 

        });

        $(document).on('keyup','#apprpofreight',function(){
            var frg = $(this).val();
            $('#amdhidpofreight').val(frg); 

            var tax1 = $('#amdhidpotax').val();
            var tax = tax1.replace(/,/g, ''); 
            var total1 = $('#amdpohidsubtot').val();
            var total = total1.replace(/,/g, ''); 
            var freight1 = $('#amdhidpofreight').val();
            var freight = freight1.replace(/,/g, ''); 
            var insurance1 = $('#amdhidpoinsurance').val();
            var insurance = insurance1.replace(/,/g, ''); 
            var others1 = $('#amdhidpoothers').val();
            var others = others1.replace(/,/g, '');

            var grandtotals = Number(total) + Number(tax) + Number(freight) + Number(insurance) + Number(others); 
            var grandtotal =  parseFloat(grandtotals).toFixed(2);
            $('#apprpototal').val(grandtotal); 
            $('#amdhidpogrndtot').val(grandtotal);

           
        });

        $(document).on('keyup','#apprpoinsurance',function(){
            var ins = $(this).val();
            $('#amdhidpoinsurance').val(ins); 

            var tax1 = $('#amdhidpotax').val();
            var tax = tax1.replace(/,/g, ''); 
            var total1 = $('#amdpohidsubtot').val();
            var total = total1.replace(/,/g, ''); 
            var freight1 = $('#amdhidpofreight').val();
            var freight = freight1.replace(/,/g, ''); 
            var insurance1 = $('#amdhidpoinsurance').val();
            var insurance = insurance1.replace(/,/g, ''); 
            var others1 = $('#amdhidpoothers').val();
            var others = others1.replace(/,/g, '');

            var grandtotals = Number(total) + Number(tax) + Number(freight) + Number(insurance) + Number(others); 
            var grandtotal =  parseFloat(grandtotals).toFixed(2);
            $('#apprpototal').val(grandtotal); 
            $('#amdhidpogrndtot').val(grandtotal);
        });

        $(document).on('keyup','#apprpoothers',function(){
            var oth = $(this).val();
            $('#amdhidpoothers').val(oth); 

            var tax1 = $('#amdhidpotax').val();
            var tax = tax1.replace(/,/g, ''); 
            var total1 = $('#amdpohidsubtot').val();
            var total = total1.replace(/,/g, ''); 
            var freight1 = $('#amdhidpofreight').val();
            var freight = freight1.replace(/,/g, ''); 
            var insurance1 = $('#amdhidpoinsurance').val();
            var insurance = insurance1.replace(/,/g, ''); 
            var others1 = $('#amdhidpoothers').val();
            var others = others1.replace(/,/g, '');

            var grandtotals = Number(total) + Number(tax) + Number(freight) + Number(insurance) + Number(others); 
            var grandtotal =  parseFloat(grandtotals).toFixed(2);
            $('#apprpototal').val(grandtotal); 
            $('#amdhidpogrndtot').val(grandtotal);
        });

        //gsttypepo
        $(document).on('keyup','.pocg',function(){


            var rowresid = $(this).data('id');  
            var gst = $(this).val();
            var substol = $('.amdposubtot').val();
            var subtotal = substol.replace(/,/g, ''); 
            var taxs = (subtotal * gst / 100); 
            var tax = parseFloat(taxs).toFixed(2);
            $('#apprtax').val(tax);
            $('#amdhidpotax').val(tax);

            //grnd totoal change
            var tax1 = $('#amdhidpotax').val();
            var tax = tax1.replace(/,/g, ''); 
            var total1 = $('#amdpohidsubtot').val();
            var total = total1.replace(/,/g, ''); 
            var freight1 = $('#amdhidpofreight').val();
            var freight = freight1.replace(/,/g, ''); 
            var insurance1 = $('#amdhidpoinsurance').val();
            var insurance = insurance1.replace(/,/g, ''); 
            var others1 = $('#amdhidpoothers').val();
            var others = others1.replace(/,/g, '');

            if(gst!='')
            {
                $('.igstpo'+rowresid).prop('disabled', true);
            }else
            {
                $('.igstpo'+rowresid).prop('disabled', false);
            }  

            var grandtotals = Number(total) + Number(tax) + Number(freight) + Number(insurance) + Number(others); 
            var grandtotal =  parseFloat(grandtotals).toFixed(2);
            $('#apprpototal').val(grandtotal); 
            $('#amdhidpogrndtot').val(grandtotal);


        });

        $(document).on('keyup','.poig',function(){

            var rowresid = $(this).data('id');  
            var gst = $(this).val();
            var substol = $('.amdposubtot').val();
            var subtotal = substol.replace(/,/g, ''); 
            var taxs = (subtotal * gst / 100); 
            var tax = parseFloat(taxs).toFixed(2);
            $('#apprtax').val(tax);
            $('#amdhidpotax').val(tax);

            //grnd totoal change
            var tax1 = $('#amdhidpotax').val();
            var tax = tax1.replace(/,/g, ''); 
            var total1 = $('#amdpohidsubtot').val();
            var total = total1.replace(/,/g, ''); 
            var freight1 = $('#amdhidpofreight').val();
            var freight = freight1.replace(/,/g, ''); 
            var insurance1 = $('#amdhidpoinsurance').val();
            var insurance = insurance1.replace(/,/g, ''); 
            var others1 = $('#amdhidpoothers').val();
            var others = others1.replace(/,/g, '');

            if(gst!='')
            {
                $('.cgstpo'+rowresid).prop('disabled', true);
            }else
            {
                $('.cgstpo'+rowresid).prop('disabled', false);
            }

            var grandtotals = Number(total) + Number(tax) + Number(freight) + Number(insurance) + Number(others); 
            var grandtotal =  parseFloat(grandtotals).toFixed(2);
            $('#apprpototal').val(grandtotal); 
            $('#amdhidpogrndtot').val(grandtotal);


        });





        //amend -purchase odr -edit end

        //confirm -purchase odr -edit strt

        $(document).on('keyup','.cfcgst',function(){  

             var sum = 0;

              $('.cfcgst').each(function() {
        
   

            var rowresid = $(this).data('id');   
            var gst = $(this).val();  

            var ammtt=$('.ammnt'+rowresid).val();  


            var substol = $('#apprsub_total').val(); 

           // var subtotal = substol.replace(/,/g, ''); 
            var subtotal = ammtt.replace(/,/g, ''); 
            var taxs = (subtotal * gst / 100);  

            sum += Number(taxs);  


            var tax = parseFloat(sum).toFixed(2);  
            $('#apprtax').val(tax);
            $('#cfhidpotax').val(tax);

            //grnd totoal change
            var tax1 = $('#cfhidpotax').val();
            var tax = tax1.replace(/,/g, ''); 
            var total1 = $('#cfpohidsubtot').val();
            var total = total1.replace(/,/g, ''); 
            var freight1 = $('#cfhidpofreight').val();
            var freight = freight1.replace(/,/g, ''); 
            var insurance1 = $('#cfhidpoinsurance').val();
            var insurance = insurance1.replace(/,/g, ''); 
            var others1 = $('#cfhidpoothers').val();
            var others = others1.replace(/,/g, '');

            if(gst!='')
            {
                $('.cfigstpo'+rowresid).prop('disabled', true);
            }else
            {
                $('.cfigstpo'+rowresid).prop('disabled', false);
            }

            var grandtotals = Number(total) + Number(tax) + Number(freight) + Number(insurance) + Number(others); 
            var grandtotal =  parseFloat(grandtotals).toFixed(2);
            $('#apprtotal').val(grandtotal); 
            $('#cfhidpogrndtot').val(grandtotal);


        });
      });


        $(document).on('keyup','.cfigst',function(){

            var sum = 0;

              $('.cfigst').each(function() {

            var rowresid = $(this).data('id');  
            var gst = $(this).val();

             var ammtt=$('.ammnt'+rowresid).val();  


            var substol = $('#apprsub_total').val();  
            var subtotal = ammtt.replace(/,/g, ''); 
            var taxs = (subtotal * gst / 100); 
            sum += Number(taxs);  
            var tax = parseFloat(sum).toFixed(2);

          

            $('#apprtax').val(tax);
            $('#cfhidpotax').val(tax);

            //grnd totoal change
            var tax1 = $('#cfhidpotax').val();
            var tax = tax1.replace(/,/g, ''); 
            var total1 = $('#cfpohidsubtot').val();
            var total = total1.replace(/,/g, ''); 
            var freight1 = $('#cfhidpofreight').val();
            var freight = freight1.replace(/,/g, ''); 
            var insurance1 = $('#cfhidpoinsurance').val();
            var insurance = insurance1.replace(/,/g, ''); 
            var others1 = $('#cfhidpoothers').val();
            var others = others1.replace(/,/g, '');

            if(gst!='')
            {
                $('.cfcgstpo'+rowresid).prop('disabled', true);
            }else
            {
                $('.cfcgstpo'+rowresid).prop('disabled', false);
            }

            var grandtotals = Number(total) + Number(tax) + Number(freight) + Number(insurance) + Number(others); 
            var grandtotal =  parseFloat(grandtotals).toFixed(2);
            $('#apprtotal').val(grandtotal); 
            $('#cfhidpogrndtot').val(grandtotal);


        });

    });


        $(document).on('keyup','#apprcffreight',function(){
            var frg = $(this).val();
            $('#cfhidpofreight').val(frg); 

            var tax1 = $('#cfhidpotax').val();
            var tax = tax1.replace(/,/g, ''); 
            var total1 = $('#cfpohidsubtot').val();
            var total = total1.replace(/,/g, ''); 
            var freight1 = $('#cfhidpofreight').val();
            var freight = freight1.replace(/,/g, ''); 
            var insurance1 = $('#cfhidpoinsurance').val();
            var insurance = insurance1.replace(/,/g, ''); 
            var others1 = $('#cfhidpoothers').val();
            var others = others1.replace(/,/g, '');

            var grandtotals = Number(total) + Number(tax) + Number(freight) + Number(insurance) + Number(others); 
            var grandtotal =  parseFloat(grandtotals).toFixed(2);
            $('#apprtotal').val(grandtotal); 
            $('#cfhidpogrndtot').val(grandtotal);

           
        });

        $(document).on('keyup','#apprcfinsurance',function(){
            var ins = $(this).val();
            $('#cfhidpoinsurance').val(ins); 

            var tax1 = $('#cfhidpotax').val();
            var tax = tax1.replace(/,/g, ''); 
            var total1 = $('#cfpohidsubtot').val();
            var total = total1.replace(/,/g, ''); 
            var freight1 = $('#cfhidpofreight').val();
            var freight = freight1.replace(/,/g, ''); 
            var insurance1 = $('#cfhidpoinsurance').val();
            var insurance = insurance1.replace(/,/g, ''); 
            var others1 = $('#cfhidpoothers').val();
            var others = others1.replace(/,/g, '');

            var grandtotals = Number(total) + Number(tax) + Number(freight) + Number(insurance) + Number(others); 
            var grandtotal =  parseFloat(grandtotals).toFixed(2);
            $('#apprtotal').val(grandtotal); 
            $('#cfhidpogrndtot').val(grandtotal);
        });

        $(document).on('keyup','#apprcfothers',function(){
            var oth = $(this).val();
            $('#cfhidpoothers').val(oth); 

            var tax1 = $('#cfhidpotax').val();
            var tax = tax1.replace(/,/g, ''); 
            var total1 = $('#cfpohidsubtot').val();
            var total = total1.replace(/,/g, ''); 
            var freight1 = $('#cfhidpofreight').val();
            var freight = freight1.replace(/,/g, ''); 
            var insurance1 = $('#cfhidpoinsurance').val();
            var insurance = insurance1.replace(/,/g, ''); 
            var others1 = $('#cfhidpoothers').val();
            var others = others1.replace(/,/g, '');

            var grandtotals = Number(total) + Number(tax) + Number(freight) + Number(insurance) + Number(others); 
            var grandtotal =  parseFloat(grandtotals).toFixed(2);
            $('#apprtotal').val(grandtotal); 
            $('#cfhidpogrndtot').val(grandtotal);
        });

        //confirm -purchase odr -edit end



});










   $(document).on('click','#workorderbtn',function(){  

      var restype=$('#restype').val();
       var orders=$('#orders').val();

       
        var error=0;
        $('.error').hide();
        // if($("#tandcNow").val()==''){
        //     $('#myterms').next("span").html('select Term and condition').show('slow');
        //     error=1;
        // }
        if($('#specification').val()=='')
        {
            $('#specification').next("span").html('Enter Specification').show('slow');
            error=1;
        }
        if($('#otherss').val()=='')
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
            $('#payment').next("span").html('Enter payment mode').show('slow');
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

             $('#workorderdata').hide();
            
         

             //$('#placeOrderiframe').attr('src', baseUrl);
              $('#placeorderdata').show();
            // setTimeout(function(){
            //         parent.closeFrame2();                      
            //         return true;
                    
            // },500);
        }
        else{
            return  false;
        }
    });
$(document).on('click','#tandcedit',function(){
    var tid = $("#tandcNowedit").val();

    var orderid = $("#tandcOrderid").val();

        $.ajax({
                type: 'POST',
                url: '../termscondtns/edittermsapprove', 
                dataType: "json",
                data: {termid:tid,orderid:orderid},
                success: function(data){
                    if(data.error=='No'){
                        $('#tandcedit').prop('disabled', true);
                        $('.indias').show();
                        $('.termsbodys').html(data.result);
                        
                    }else{
                        alert(data.errortext);
                    }
                }
            });
});
 $(document).on( "click", "#editsavetermss", function(){ 
    
        var content = CKEDITOR.instances['edittermsscontent'].getData();
        //$("#tandcstatus").val('1');
        $("#tandcNowedit").val(content);
        $('.termsbodys').hide();
    });
  $(document).on( "click", "#editcanceltermss", function(){

        $('.termsbodys').hide();
        //$("#tandcstatus").val('0');
    });

  $(document).on('change','select#mytermspurchase',function(){  
    //$("select#myterms").change(function(){ alert ("hi");
        var selectedtandc = $(this).children("option:selected").val();
        if( selectedtandc != ''){
            $("#tandcNow").val(selectedtandc);
        }
     
    });

   $(document).ready(function(){ 
     $(document).on('change','select#myterms',function(){ 
     $('.tandcupdate').show();
    //$("select#myterms").change(function(){ alert ("hi");
        var selectedtandc = $(this).children("option:selected").val();
        if( selectedtandc != ''){
            $("#tandcNow").val(selectedtandc);
        }
     
    });
     
     $(document).on('click','.termssbmt',function(){

        $.ajax({
                type: 'POST',
                url: '../termscondtns/savetermscntenttemporary', 
                dataType: "json",
                data: $('#leaseterms').serialize(),
                success: function(data){
                    if(data.error=='No'){
                        
                    }
                }
            });
     });
     $(document).on('click','.ptermssbmt',function(){

        $.ajax({
                type: 'POST',
                url: '../termscondtns/savetermscntenttemporary', 
                dataType: "json",
                data: $('#purchasesss').serialize(),
                success: function(data){
                    if(data.error=='No'){
                        
                    }
                }
            });
     });

   $(document).on('click','.tandcupdate',function(){ 
   // $(".tandcupdate").click(function(){  
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
                        //$('select#myterms').prop('disabled', true);
                        //$('.tandcupdate').hide();
                    }else{
                        alert(data.errortext);
                    }
                }
            });
        }else{
            alert('Select one Terms and condition for update');
        }
    });

   $(document).on('change','#myterms',function(){ 
   // $(".tandcupdate").click(function(){  
        //if($('.tandcupdate').is(":hidden")){
            
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
                            //$('select#myterms').prop('disabled', true);
                            //$('.tandcupdate').hide();
                        }else{
                            alert(data.errortext);
                        }
                    }
                });
            }
            
        //}
    });

   $(document).on('click','.tandcupdatepurchase',function(){ 
   // $(".tandcupdate").click(function(){  
        var tcid = $("#tandcNow").val();
        if(tcid != ''){
            $.ajax({
                type: 'POST',
                url: '../termscondtns/edittermpurchase', 
                dataType: "json",
                data: {termid:tcid},
                success: function(data){
                    if(data.error=='No'){
                        $("#tandcstatus").val('1');
                        $('.india').show();
                        $('.termsbodyp').html(data.result);
                        //$('select#myterms').prop('disabled', true);
                        //$('.tandcupdatepurchase').hide();
                    }else{
                        alert(data.errortext);
                    }
                }
            });
        }else{
            alert('Select one Terms and condition for update');
        }
    });

   /*$(document).on('change','#mytermspurchase',function(){ 
    $('.tandcupdatepurchase').show();
   // $(".tandcupdate").click(function(){  
        //if($('.tandcupdatepurchase').is(":hidden")){
            
            var tcid = $("#tandcNow").val();
            if(tcid != ''){
                $.ajax({
                    type: 'POST',
                    url: '../termscondtns/edittermpurchase', 
                    dataType: "json",
                    data: {termid:tcid},
                    success: function(data){
                        if(data.error=='No'){
                            $("#tandcstatus").val('1');
                            $('.india').show();
                            $('.termsbody').html(data.result);
                            //$('select#myterms').prop('disabled', true);
                            //$('.tandcupdatepurchase').hide();
                        }else{
                            alert(data.errortext);
                        }
                    }
                });
            }
            
       // }
    });*/

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


    $(document).on('focus','#date',function(){
        $(this).datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
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
    $(document).on('focus','.datedes',function(){
        $(this).datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
    });


       $(document).on('click','#leaseorderbtn',function(){

       var restype=$('#restype').val();
       var orders=$('#orders').val();

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
            $('#payment').next("span").html('Enter payment mode').show('slow');
            error=1;
        }

        if(error==0){



             $('#leaseorderdata').hide();
            
         

             //$('#placeOrderiframe').attr('src', baseUrl);
              $('#placeorderdata').show();
            // setTimeout(function(){
            //         parent.closeFrame2();

            //         return true;
                    
            // },500);
        }
        else{
            return  false;
        }
    });


        $(document).on('click','#directworkorderbtn',function(){


        var error=0;
        $('.error').hide();
        /*if($('#working_hours').val()=='')
        {  
            $('#working_hours').next("span").html('Select Working Hours').show('slow');
            error=1;
        }*/




        if(error==0){


               $('#musterrolldata').hide();
            
         

             //$('#placeOrderiframe').attr('src', baseUrl);
              $('#placeorderdata').show();
            // setTimeout(function(){
            //         parent.closeFrame2();

            //         return true;
                    
            // },500);
        }
        else{
            return  false;
        }
        
    });


  $(document).on('focus','.date',function(){
        var id=$(this).attr('data-id');
        $('#date'+id+'').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true});

    });


   $(document).on('click','#despatchorderbtn_old',function(){
        var error=0;
        $('.error').hide();

        $('.from').each(function(){
            var id=$(this).attr('data-id');
            
            if($(this).val()=='none')
            {
                $("#from"+id).next("span").html('Select Move from').show('slow');
                error=1;
            }
        });
        $('.to').each(function(){
            var id=$(this).attr('data-id');
           
            if($(this).val()=='none')
            {
                $("#to"+id).next("span").html('Select Move to').show('slow');
                error=1;
            }
        });
        $('.datedes').each(function(){
            var ids=$(this).attr('data-id');
            
            if($(this).val()=='')
            {
                $("#date"+id).next("span").html('Select Date').show('slow');
                error=1;
            }
        });
        $('.vehicle_no').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='')
            {
                $("#vehicle_no"+id).next("span").html('Enter Period of Vehicle No').show('slow');
                error=1;
            }
        });

    
        if(error==0){
                
            $('form#despatchorderform').submit();

          $('#despatchorderdata').hide();
            
         

             //$('#placeOrderiframe').attr('src', baseUrl);
              $('#placeorderdata').show();
            
            // setTimeout(function(){
            //         parent.closeFrame2();

            //         return true;
                    
            // },500);
        }
        else{
            return  false;
        }
    });

   $(document).on('click','.editvenrescart',function(){

        var id=$(this).attr('data-id');

        $('#resreqty'+id).hide();
        $('#resreordrqty'+id).hide();
        $('#resreordrlevel'+id).hide();
        $('#editvenrescart'+id).hide();
        $('#poresrate'+id).hide();


        $('#editresreqty'+id).show();
        $('#editresreordrqty'+id).show();
        $('#editresreordrlevel'+id).show();
        $('#savevenrescart'+id).show();
        $('#pooeditrate'+id).show();


   });

   $(document).on('click','.savevenrescart',function(){
        var id=$(this).attr('data-id');
        var rate = $('#resrate'+id).val();
        var edtreq = $('#editresreqty'+id).val();
        rqty = $('#editresreordrqty'+id).val();
        rlevel = $('#editresreordrlevel'+id).val();

        var str = $('#pooeditrate'+id).val();
        var poeditrate = str.replace(/,/g, '');
        if(edtreq !='')
        {
            if(edtreq<=0)
            {
                alert("Required quantity should be greater than 0");
                exit();
            }
            
        }
        
        if(rqty != '')
        {
            reordqty = rqty;
        }else{
            reordqty = '';
        }

        if(rlevel != '')
        {
            reorderlevel = rlevel;
        }else{
            reorderlevel = '';
        }
        var tot = edtreq * rate;
        $.ajax({
            type: 'POST',
            url: '../procurement/savepodetails',
            /*beforeSend : function(){
                $('.preloader').show();
            },*/
            dataType: "json",
            data: {cartid:id,reqty:$('#editresreqty'+id).val(),reordqty:reordqty,reorderlevel:reorderlevel,rate:poeditrate},
            success: function(data){
                if(data.error=='No')
                {
                    if(data.totalamount<=0)
                    {
                        $('#savevenrescart'+id).parent().parent().parent().parent().find('#placeorder_new').addClass("disabled");
                    }
                    $('#editresreqty'+id).hide();
                    $('#editresreordrqty'+id).hide();
                    $('#editresreordrlevel'+id).hide();
                    $('#savevenrescart'+id).hide();
                    $('#pooeditrate'+id).hide();

                    $('#resreqty'+id).show();
                    $('#resreordrqty'+id).show();
                    $('#resreordrlevel'+id).show();
                    $('#editvenrescart'+id).show();
                    $('#poresrate'+id).show();
                   
                    $('#resreqty'+id).html($('#editresreqty'+id).val());
                    $('#poresrate'+id).html($('#pooeditrate'+id).val());
                    $('#resreordrqty'+id).html(reordqty);
                    $('#resreordrlevel'+id).html(reorderlevel);
                    $('#restotamount'+id).html(tot.toFixed(2));

                    //$('#vendortotal'+data.vendorid).html(data.totalamt);
                    $('#vendortotal'+data.vendorid).html(data.totalamt);
                    $('#totress'+id).html(data.totalamt);
                    $('#restotamount'+id).html(data.amount);
                    $('#resvenns'+data.vendorid).html(data.totalamt);

                    $('#balancetotal'+id).html(data.remaining.tofixed(2));
                    $('#cartbalancetotal'+id).html(data.remaining);

                    
                }
                //$('.preloader').hide();
            }
        });       

   });
 


$(document).on('click','.splitvendor',function(){

    var id=$(this).attr('data-id');

    var cartid=$(this).attr('data-cartid');
    var projid = $('#projval').val();

    $.ajax({
        type: 'POST',
        url: '../procurement/choosenewvendor',
        /*beforeSend : function(){
            $('.preloader').show();
        },*/
        dataType: "json",
        data: {resourceid:id,cartid:cartid,projid:projid},
        success: function(data){
            if(data.error=='No')
            {
                $('#newaddedresources').html(data.result);
            }
            //$('.preloader').hide();
        }
    });       

});

$(document).on('click','.changevendorqty',function(){

    var vendorid=$(this).attr('data-vendorid');

    var resourceid=$(this).attr('data-resourceid');

    var cartid=$(this).attr('data-cartid');

    var splitqty = $('#splitqty'+vendorid).val();

    var requiedqty = $('#editresreqty'+cartid).val();
    var projid = $('#specproj').val();


   /* if(requiedqty){

    var r = confirm("Are you sure you want to change the vendor?");

    if (r == true) {*/

        $.ajax({
            type: 'POST',
            url: '../procurement/changevendor',
            dataType: "json",
            data: {vendorid:vendorid,resourceid:resourceid,cartid:cartid,splitqty:splitqty,requiedqty:requiedqty},
            success: function(data){
                if(data.error=='No')
                {
                    $('.changevendorpopup').trigger('click');
                    $('.projctsel'+projid).trigger('click');
                }
            }
        });      
/*
    } 
}
*/
});

/*$(document).on('keyup','.editresreqty',function(){

    var cartid = $(this).attr('data-id');
    var ven = $(this).attr('data-v');

    var req = $('#editresreqty'+cartid).val();

    var rate = $('#pooeditrate'+cartid).val();

    var eqy = $('#estqy'+cartid).val();

    var amnt = eqy * rate;
    var albal = $('#cartbalancetotal'+cartid).html();

    var bal = eqy-req;

    if(req != ''){
         var tot = req * rate;
    }
    if(req > eqy)
    {
        alert('Required Quantity is greater than Estimated Quantity');
    }


    if(req != ''){
        $('#restotamount'+cartid).html(tot.toFixed(2));
        $('#resstotamount'+cartid).val(tot);
        $('#cartbalancetotal'+cartid).html(bal.toFixed(3));
    }else{
        $('#restotamount'+cartid).html(amnt.toFixed(2));
        $('#resstotamount'+cartid).val(amnt);
        $('#cartbalancetotal'+cartid).html(albal.toFixed(3));
    }
    var amn = 0;

    $('.resvtot_'+ven).each(function(){
        var samt = $(this).val();

        amn = parseInt(amn) + parseInt(samt);

    });
    $('#resvenns'+ven).html(amn.toFixed(2));



});*/



/*Purchase orders End*/

/*Work orders*/

$(function() {
    $('#wrksearch').click(function () {
        $.ajax({
            type: 'POST',
            url: '../procurement/workorderscart',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            //data: {task:$('#searchtask').val()},
            success: function(data){
                if(data.error=='No')
                {
                    //$('#wrkorderitems').html(data.result);
                    $('#wrkcartitemsss').html(data.result);
                    //$('#cartitemstable').show();
                    $('#directwrksearch').trigger('click');
                }else if(data.error=='yes'){
                    $('#wrkporr').hide();
                    $('#workform').hide();
                    $('#directwrksearch').trigger('click');
                }
                $('.preloader').hide();
            }
        });
    });
});


$(document).on('click','#wrkcartitems input',function(){

    var values = [];
    var ordervalues = [];
    var restype = [];
    var project = [];


   
    $('#wrkcartitems input:checked').each(function() {


        values.push($(this).val());
        ordervalues.push($(this).attr('data-id'));
        restype.push($(this).attr('data-restype'));
        project.push($(this).attr('data-project'));
    });

    $('[name="wvenids"]').attr({value: values.join(', ')});
    $('[name="wcrtid"]').attr({value: ordervalues.join(', ')});
    $('[name="wrresidss"]').attr({value: restype.join(', ')});
    $('[name="wprjss"]').attr({value: project.join(', ')});

   
});

$(document).on('click','#placeorder_wrkorder',function(){
    if($('.vendors').is(":checked")) {
        var venid=$(this).attr('data-ven');
        var proid=$(this).attr('data-pro');
        var resid = $('#wrresidss').val();
        var cartid = $('#wcrtid').val();

        $.ajax({
            type: 'POST',
            url: '../procurement/workordernew',
            dataType: "json",
            data: {venid: venid,proid: proid,mode:2,resid:resid,cartid:cartid},
            success: function (data) {
                if (data.error == 'No') {

                    if(data.order == 'work'){
                        ordertype="Work Order";
                        $('#wrkorders').show();
                        $('#wrkorders').html(data.workorder);  
                        $('#workform').hide();
                        $('#recartform').hide();
                        $('#recartform').hide();
                        $('#carttform').hide();
                        $('.topsbars').hide();
                
                        $('#directworkform').hide();
                        $('#leaseform').hide();
                        $('#despform').hide();
                        $('.headngstyle').hide();
                        $('.orders-wrpr').hide();
                        $('.placeorder-list').hide();
                    }
                }
            }
        });
    }else{
        alert('Please select any vendor before proceeding.');
         return false;
    }

});


/*$(document).on('click','#placeorder_wrkorder',function(){

    var venid=$(this).attr('data-ven');
    var proid=$(this).attr('data-pro');

    $.ajax({
        type: 'POST',
        url: '../procurement/workordernew',
        dataType: "json",
        data: {venid: venid,proid: proid,mode:2},
        success: function (data) {
            if (data.error == 'No') {

                if(data.order == 'work'){
                    ordertype="Work Order";
                    $('#wrkorders').show();
                    $('#wrkorders').html(data.workorder);  
                    $('#workform').hide();
                }
            }
        }
    });

});*/
$(document).on('click','#cancelorderworkorder',function(){   


         $('.topsbars').show();
         $('#wrkorders').hide();
            
          $('#collapsewrk').show();
          $('#workform').show();
          $('#recartform').show();
          $('#recartform').show();
          $('#carttform').show();

          $('#directworkform').show();
          $('#leaseform').show();
          $('#despform').show();
          $('.headngstyle').show();
          $('.orders-wrpr').show();
          $('.placeorder-list').show();
            
            // setTimeout(function(){

            //     parent.closeFrame2();
                    
            // },500);
                      
           /* parent.window.close();
            window.onunload = function (e) { 
                
             opener.refreshParentWindow();  
             };

    $.ajax({
        type: 'POST',
        url: '../procurement/droptemp',
        beforeSend : function(){
        },
        dataType:"json",
        data:{},
        success: function(data){
        }
    });        */      
            
 });

 $(document).on('click','.editvenresworkcart',function(){

        var id=$(this).attr('data-id');

        //$('#wrkeditresname'+id).show();
       // $('#wrkresname'+id).hide();
        $('#wrkeditresreqty'+id).show();
        $('#wworate'+id).hide();
        
        //$('#resreordrqty'+id).hide();
        /*$('#resreordrlevel'+id).hide()*/
        $('#editvenresworkcart'+id).hide();

        $('#wrkresreqty'+id).hide();
        /*$('#editresreordrqty'+id).show();
        $('#editresreordrlevel'+id).show();*/
        $('#savevenresworkcart'+id).show();
        $('#wooeditrate'+id).show();

   });

 $(document).on('click','.savevenresworkcart',function(){

        var id=$(this).attr('data-id');

        var str = $('#wooeditrate'+id).val();
        var woeditrate = str.replace(/,/g, '');

        $.ajax({
            type: 'POST',
            url: '../procurement/savewodetails',
            /*beforeSend : function(){
                $('.preloader').show();
            },*/
            dataType: "json",
            data: {cartid:id,reqty:$('#wrkeditresreqty'+id).val(),rate:woeditrate},
            success: function(data){
                if(data.error=='No')
                {

                    //$('#wrkeditresreqty'+id).hide();
                    //$('#editresreordrqty'+id).hide();
                    //$('#editresreordrlevel'+id).hide();
                    //$('#savevenresworkcart'+id).hide();
                    $('#wooeditrate'+id).hide();
                    //$('#wrkeditresname'+id).hide();
                    //$('#wrkresname'+id).html($('#wrkeditresname'+id).val());
                   // $('#wrkresname'+id).show();
                    $('#wworate'+id).show();
                    $('#wworate'+id).html($('#wooeditrate'+id).val());


                    $('#savevenresworkcart'+id).hide();
                    $('#wrkresreqty'+id).html($('#wrkeditresreqty'+id).val());
                    $('#wrkeditresreqty'+id).hide();
                    
                    $('#wrkresamt'+id).html(data.amnt);
                   /* $('#resreordrqty'+id).html($('#editresreordrqty'+id).val());
                    $('#resreordrlevel'+id).html($('#editresreordrlevel'+id).val());*/
                    $('#wrkvendortotal'+data.vendorid).html(data.amount);
                    

                    //$('#balancetotal'+id).html(data.remaining);

                    $('#wrkresreqty'+id).show();
                    //$('#resreordrqty'+id).show();
                    //$('#resreordrlevel'+id).show();
                    $('#editvenresworkcart'+id).show();
                }
                //$('.preloader').hide();
            }
        });       

   });

  $(document).on('click','.editvenresdircart',function(){

        var id=$(this).attr('data-id');

        //$('#wrkeditresname'+id).show();
       // $('#wrkresname'+id).hide();
        $('#dirwrkeditresreqty'+id).show();
        $('#dworate'+id).hide();
        
        //$('#resreordrqty'+id).hide();
        /*$('#resreordrlevel'+id).hide()*/
        $('#editvenresdircart'+id).hide();

        $('#dirwrkresreqty'+id).hide();
        /*$('#editresreordrqty'+id).show();
        $('#editresreordrlevel'+id).show();*/
        $('#savevenresdircart'+id).show();
        $('#dirweditrate'+id).show();

   });

 $(document).on('click','.savevenresdircart',function(){

        var id=$(this).attr('data-id');

        var str = $('#dirweditrate'+id).val();
        var eddirrate = str.replace(/,/g, '');

        $.ajax({
            type: 'POST',
            url: '../procurement/savedirwodetails',
            /*beforeSend : function(){
                $('.preloader').show();
            },*/
            dataType: "json",
            data: {cartid:id,reqty:$('#dirwrkeditresreqty'+id).val(),rate:eddirrate},
            success: function(data){
                if(data.error=='No')
                {

                    //$('#wrkeditresreqty'+id).hide();
                    //$('#editresreordrqty'+id).hide();
                    //$('#editresreordrlevel'+id).hide();
                    //$('#savevenresworkcart'+id).hide();
                    $('#dirweditrate'+id).hide();
                    //$('#wrkeditresname'+id).hide();
                    //$('#wrkresname'+id).html($('#wrkeditresname'+id).val());
                   // $('#wrkresname'+id).show();
                    $('#dworate'+id).show();
                    $('#dworate'+id).html($('#dirweditrate'+id).val());


                    $('#savevenresdircart'+id).hide();
                    $('#dirwrkresreqty'+id).html($('#dirwrkeditresreqty'+id).val());
                    $('#dirwrkeditresreqty'+id).hide();
                    
                    $('#dirwrkresamt'+id).html(data.amnt);
                   /* $('#resreordrqty'+id).html($('#editresreordrqty'+id).val());
                    $('#resreordrlevel'+id).html($('#editresreordrlevel'+id).val());*/
                    $('#dirwrkvendortotal'+data.vendorid).html(data.amount);
                    

                    //$('#balancetotal'+id).html(data.remaining);

                    $('#dirwrkresreqty'+id).show();
                    //$('#resreordrqty'+id).show();
                    //$('#resreordrlevel'+id).show();
                    $('#editvenresdircart'+id).show();
                }
                //$('.preloader').hide();
            }
        });       

   });
 
 $(document).on('keyup','.wrkeditresreqty',function(){

    var cartid = $(this).attr('data-id');
    var ven = $(this).attr('data-v');

    var req = $('#wrkeditresreqty'+cartid).val();

    var rate = $('#wooeditrate'+cartid).val();

    var eqy = $('#woesresq'+cartid).val();

    

    if(req != ''){
         var tot = req * rate;
    }

    if(req != ''){
        
        $('#wrkresamt'+cartid).html(tot.toFixed(2));
        $('#reswwotot'+cartid).val(tot);
        
    }else{
        
        var amntt = eqy * rate;
        $('#wrkresamt'+cartid).html(amntt.toFixed(2));
        $('#reswwotot'+cartid).val(amntt);
        
    }

    var amn = 0;

    $('.woresss_'+ven).each(function(){
        var samt = $(this).val();

        amn = parseInt(amn) + parseInt(samt);

    });
    
    $('.totwovenres'+ven).html(amn.toFixed(2));



 });
 $(document).on('keyup','.dirwrkeditresreqty',function(){

    var cartid = $(this).attr('data-id');
    var ven = $(this).attr('data-v');

    var req = $('#dirwrkeditresreqty'+cartid).val();

    var rate = $('#dirweditrate'+cartid).val();

    var eqy = $('#direstqy'+cartid).val();

    

    if(req != ''){
         var tot = req * rate;
    }

    if(req != ''){
        
        $('#dirwrkresamt'+cartid).html(tot.toFixed(2));
        $('#resdiwtot'+cartid).val(tot);
        
    }else{
        
        var amntt = eqy * rate;
        $('#dirwrkresamt'+cartid).html(amntt.toFixed(2));
        $('#resdiwtot'+cartid).val(amntt);
        
    }

    var amn = 0;

    $('.diworesss_'+ven).each(function(){
        var samt = $(this).val();

        amn = parseInt(amn) + parseInt(samt);

    });
    
    $('.totdirvenres'+ven).html(amn.toFixed(2));



 });

$(document).on('click','.splitvendorwrk',function(){

    var id=$(this).attr('data-id');

    var cartid=$(this).attr('data-cartid');
    var reqty = $('#wrkeditresreqty'+cartid).val();


    $.ajax({
        type: 'POST',
        url: '../procurement/workchoosenewvendor',
        /*beforeSend : function(){
            $('.preloader').show();
        },*/
        dataType: "json",
        data: {resourceid:id,cartid:cartid,reqty:reqty},
        success: function(data){
            if(data.error=='No')
            {
                $('#wrkresreqty'+cartid).html($('#wrkeditresreqty'+cartid).val());
                $('#wrkvendortotal'+data.vendorid).html(data.totalamt);
                $('#newworkaddedresources').html(data.result);
            }
            //$('.preloader').hide();
        }
    });       

});

$(document).on('click','.workchangevendorqty',function(){

    var vendorid=$(this).attr('data-vendorid');

    var resourceid=$(this).attr('data-resourceid');

    var cartid=$(this).attr('data-cartid');

    var splitqty = $('#splitqty'+vendorid).val();

    var requirdqty = $('#wrkeditresreqty'+cartid).val();

    var r = confirm("Are you sure you want to change the vendor?");

    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../procurement/workchangevendor',
            dataType: "json",
            data: {vendorid:vendorid,resourceid:resourceid,cartid:cartid,splitqty:splitqty,requirdqty:requirdqty},
            success: function(data){
                if(data.error=='No')
                {
                    $('.changewrkvendorpopup').trigger('click');
                    $('#wrksearch').trigger('click');
                }
            }
        });      

    } 

});

/*Work orders End*/

/*Direct Work orders*/

$(function() {
    $('#directwrksearch').click(function () {
        $.ajax({
            type: 'POST',
            url: '../procurement/directworkorderscart',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            //data: {task:$('#searchtask').val()},
            success: function(data){
                if(data.error=='No')
                {
                    //$('#directwrkorderitems').html(data.result);
                    $('#dircartitemss').html(data.result);
                    //$('#cartitemstable').show();
                    $('#leasesearch').trigger('click');
                }else if(data.error=='yes'){
                    $('#dwporr').hide();
                    $('#directworkform').hide();
                    $('#leasesearch').trigger('click');
                }
                $('.preloader').hide();
            }
        });
    });
});

$(document).on('click','#placeorder_dirwrkorder',function(){
    
var error = 0;

if($('.vendors').is(":checked")) {

    var checkedNum = $('.vendors:checked').length;

    if(checkedNum > 1)
    {
        var actIds = $(".vendors:checked").map(function() {
            return $(this).attr('data-actid');
          }).toArray();
            if(actIds.every( (val, i, arr) => val === arr[0] )) 
            {
                error = 0;
            }
            else
            {
                error = 1;
            } 

    }else if(checkedNum == 1)
    {
        error = 0;
    }
    else{
        error = 1;
         
    }

    var venid = $(this).attr('data-ven');
    var proid = $(this).attr('data-pro');
    var resid = $('#dworresidss').val();
    var cartid = $('#dwocrtid').val();
    var restype = $('#dworestype').val();

   /* var venid = $(this).attr('data-ven');
    var proid = $(this).attr('data-pro');
    var resid = $('#rresidss').val();
    var cartid = $('#crtid').val(); */
   

     if(error == 0)
     {

        $.ajax({
            type: 'POST',
            url: '../procurement/dirworkordernew',
            dataType: "json",
            data: {venid: venid,proid: proid,mode:3,resid:resid,cartid:cartid},
            success: function (data) {
                if (data.error == 'No') {

                    if(data.order == 'directwork'){
                        ordertype="Direct Work Order";
                        $('#directwrkorders').show();
                        $('#directwrkorders').html(data.musterroll);  
                        $('#directworkform').hide();
                        $('#workform').hide();
                        $('#recartform').hide();
                        $('#recartform').hide();
                        $('#carttform').hide();
                        $('.placeorder-list').hide();
                        $('.topsbars').hide();

                        $('#leaseform').hide();
                        $('#despform').hide();
                        $('.headngstyle').hide();
                        $('.orders-wrpr').hide();
                    }
                }
            }
        });

     }else{
         alert('Please select resources under same activity.');
         return false;
     }

    
 }else {
         alert('Please select any vendor before proceeding.');
         return false;
     }

});

$(document).on('click','#cancelorderdirworkorder',function(){   


      
         $('#directwrkorders').hide();
         $('.topsbars').show();
          $('#collapsedirectwrk').show();
          $('#directworkform').show();
          $('#workform').show();
          $('#recartform').show();
          $('#recartform').show();
          $('#carttform').show();
          $('#leaseform').show();
          $('#despform').show();
          $('.headngstyle').show();
          $('.orders-wrpr').show();
          $('.placeorder-list').show();
            
            // setTimeout(function(){

            //     parent.closeFrame2();
                    
            // },500);
                      
           /* parent.window.close();
            window.onunload = function (e) { 
                
             opener.refreshParentWindow();  
             };

    $.ajax({
        type: 'POST',
        url: '../procurement/droptemp',
        beforeSend : function(){
        },
        dataType:"json",
        data:{},
        success: function(data){
        }
    });        */      
            
 });

 $(document).on('click','.splitvendordirectwrk',function(){

    var id=$(this).attr('data-id');

    var cartid=$(this).attr('data-cartid');
    var reqty = $('#dirwrkeditresreqty'+cartid).val();


    $.ajax({
        type: 'POST',
        url: '../procurement/dirworkchoosenewvendor',
        /*beforeSend : function(){
            $('.preloader').show();
        },*/
        dataType: "json",
        data: {resourceid:id,cartid:cartid,reqty:reqty},
        success: function(data){
            if(data.error=='No')
            {
                $('#dirwrkresreqty'+cartid).html($('#dirwrkeditresreqty'+cartid).val());
                $('#dirwrkvendortotal'+data.vendorid).html(data.totalamt);
                $('#newdirworkaddedresources').html(data.result);
            }
            //$('.preloader').hide();
        }
    });       

});

$(document).on('click','.dirworkchangevendorqty',function(){

    var vendorid=$(this).attr('data-vendorid');

    var resourceid=$(this).attr('data-resourceid');

    var cartid=$(this).attr('data-cartid');

    var splitqty = $('#splitqty'+vendorid).val();

    var requirdqty = $('#dirwrkeditresreqty'+cartid).val();

    var r = confirm("Are you sure you want to change the vendor?");

    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../procurement/dirworkchangevendor',
            dataType: "json",
            data: {vendorid:vendorid,resourceid:resourceid,cartid:cartid,splitqty:splitqty,requirdqty:requirdqty},
            success: function(data){
                if(data.error=='No')
                {
                    $('.changedirwrkvendorpopup').trigger('click');
                    $('#directwrksearch').trigger('click');
                }
            }
        });      

    } 

});


//same activity- with checkbox srt old code
$(document).on('click','#dircartitems input',function(){
   
    var values = [];
    var ordervalues = [];
    var restype = [];
    var project = [];

    $('#dircartitems input:checked').each(function() {
     

        values.push($(this).val());
        ordervalues.push($(this).attr('data-id'));
        restype.push($(this).attr('data-restype'));
        project.push($(this).attr('data-project'));
        $resttype = $(this).attr('data-res');
    

        
    });
    $('[name="dwovenids"]').attr({value: values.join(', ')});
    $('[name="dwocrtid"]').attr({value: ordervalues.join(', ')});
    $('[name="dworresidss"]').attr({value: restype.join(', ')});
    $('[name="dwoprjss"]').attr({value: project.join(', ')});
     $('#dworestype').val($resttype);
   
}); 
//same activity- with checkbox end old code


/*new code start without chkbox strt*/

/*$(document).on('click','.dirwrkpplaceord',function(){

    var venids = $(this).attr('data-id');  

    var values = [];
    var ordervalues = [];
    var restype = [];
    var project = [];

    $('.itesmvendor'+venids).each(function() { 
        var cartid = $(this).attr('data-id'); 
        /*$vend_id = $(this).val();
        $prj = $(this).attr('data-project');
        $res = $(this).attr('data-restype');
        $cartid = $(this).attr('data-id')

        $('#venids').val($vend_id );
        $('#rresidss').val($res);
        $('#prjss').val($prj);
        $('#crtid').val($cartid);*/

   /*     values.push($('.vendor'+cartid).val());
        ordervalues.push($('.vendor'+cartid).attr('data-id'));
        restype.push($('.vendor'+cartid).attr('data-restype'));
        project.push($('.vendor'+cartid).attr('data-project'));
        $resttype = $('.vendor'+cartid).attr('data-res');
    

        
    });
    $('[name="dwovenids"]').attr({value: values.join(', ')});
    $('[name="dwocrtid"]').attr({value: ordervalues.join(', ')});
    $('[name="dworresidss"]').attr({value: restype.join(', ')});
    $('[name="dwoprjss"]').attr({value: project.join(', ')});
     $('#dworestype').val($resttype);
   
});*/
/*new code start without chkbox end*/

/*Direct Work orders End*/

/*Lease orders*/

$(function() {
    $('#leasesearch').click(function () {
        $.ajax({
            type: 'POST',
            url: '../procurement/leaseorderscart',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            //data: {task:$('#searchtask').val()},
            success: function(data){
                if(data.error=='No')
                {
                    //$('#leaseorderitems').html(data.result);
                    $('#leasecartitemss').html(data.result);
                    $('#despsearch').trigger('click');
                    
                }else if(data.error=='yes'){
                    $('#leord').hide();
                    $('#leaseform').hide();
                    $('#despsearch').trigger('click');
                }
                $('.preloader').hide();
            }
        });
    });
});
$(document).on('click','#leasecartitems input',function(){

    var values = [];
    var ordervalues = [];
    var restype = [];
    var project = [];
   
    $('#leasecartitems input:checked').each(function() {

        values.push($(this).val());
        ordervalues.push($(this).attr('data-id'));
        restype.push($(this).attr('data-restype'));
        project.push($(this).attr('data-project'));
        
    });
    $('[name="lvenids"]').attr({value: values.join(', ')});
    $('[name="lcrtid"]').attr({value: ordervalues.join(', ')});
    $('[name="lrresidss"]').attr({value: restype.join(', ')});
    $('[name="lprjss"]').attr({value: project.join(', ')});
   
});
$(document).on('click','#placeorder_leaseorder',function(){

if($('.vendors').is(":checked")) {

    var venid=$(this).attr('data-ven');
    var proid=$(this).attr('data-pro');
    var resid = $('#lrresidss').val();
    var cartid = $('#lcrtid').val(); 

    $.ajax({
        type: 'POST',
        url: '../procurement/leaseordernew',
        dataType: "json",
        data: {venid: venid,proid: proid,mode:4,resid:resid,cartid:cartid},
        success: function (data) {
            if (data.error == 'No') {

                if(data.order == 'lease'){
                    ordertype="Lease Order";
                    $('#lsorders').show();
                    $('#lsorders').html(data.leaseorders);  
                    $('#leaseform').hide();

                    $('#workform').hide();
                    $('#recartform').hide();
                    $('#recartform').hide();
                    $('#carttform').hide();
                    $('.topsbars').hide();
                    $('#directworkform').hide();
                   
                    $('#despform').hide();
                    $('.headngstyle').hide();
                    $('.orders-wrpr').hide();
                    $('.placeorder-list').hide();
                }
            }
        }
    });
}else{
    alert('Please select any vendor before proceeding.');
         return false;
}
});

/*$(document).on('click','#placeorder_leaseorder',function(){

    var venid=$(this).attr('data-ven');
    var proid=$(this).attr('data-pro');

    $.ajax({
        type: 'POST',
        url: '../procurement/leaseordernew',
        dataType: "json",
        data: {venid: venid,proid: proid,mode:4},
        success: function (data) {
            if (data.error == 'No') {

                if(data.order == 'lease'){
                    ordertype="Lease Order";
                    $('#lsorders').show();
                    $('#lsorders').html(data.leaseorders);  
                    $('#leaseform').hide();
                }
            }
        }
    });

});*/

$(document).on('click','#cancelorderleaseorder',function(){   


        $('.topsbars').show();
         $('#lsorders').hide();
            
          $('#collapselease').show();
          $('#leaseform').show();
          $('#workform').show();
          $('#recartform').show();
          $('#recartform').show();
          $('#carttform').show();

          $('#directworkform').show();

          $('#despform').show();
          $('.headngstyle').show();
          $('.orders-wrpr').show();
          $('.placeorder-list').show();
            
            // setTimeout(function(){

            //     parent.closeFrame2();
                    
            // },500);
                      
           /* parent.window.close();
            window.onunload = function (e) { 
                
             opener.refreshParentWindow();  
             };

    $.ajax({
        type: 'POST',
        url: '../procurement/droptemp',
        beforeSend : function(){
        },
        dataType:"json",
        data:{},
        success: function(data){
        }
    });        */      
            
 });

 $(document).on('click','.editvenresleasecart',function(){

        var id=$(this).attr('data-id');

        $('#leaseeditresreqty'+id).show();
        $('#leaseeditrate'+id).show();
        
        $('#leaseresreqty'+id).hide();
        $('#llorate'+id).hide();
        //$('#resreordrlevel'+id).hide();
        $('#editvenresleasecart'+id).hide();

        //$('#leaseeditresreqty'+id).hide();
        /*$('#editresreordrqty'+id).show();
        $('#editresreordrlevel'+id).show();*/
        $('#savevenresleasecart'+id).show();


   });

 $(document).on('click','.savevenresleasecart',function(){

        var id=$(this).attr('data-id');

        var str = $('#leaseeditrate'+id).val();
        var editlorate = str.replace(/,/g, '');

        $.ajax({
            type: 'POST',
            url: '../procurement/savelodetails',
            /*beforeSend : function(){
                $('.preloader').show();
            },*/
            dataType: "json",
            data: {cartid:id,reqty:$('#leaseeditresreqty'+id).val(),rate:editlorate},
            success: function(data){
                if(data.error=='No')
                {

                    $('#leaseeditresreqty'+id).hide();
                    //$('#editresreordrqty'+id).hide();
                    //$('#editresreordrlevel'+id).hide();
                    $('#savevenresleasecart'+id).hide();
                    $('#leaseeditrate'+id).hide();
                    $('#llorate'+id).show();

                    $('#leaseresreqty'+id).html($('#leaseeditresreqty'+id).val());
                    $('#llorate'+id).html($('#leaseeditrate'+id).val());
                   /* $('#resreordrqty'+id).html($('#editresreordrqty'+id).val());
                    $('#resreordrlevel'+id).html($('#editresreordrlevel'+id).val());*/

                    $('#leasevendortotal'+data.vendorid).html(data.amount);
                    $('#resamm'+id).html(data.amnt);


                    //$('#balancetotal'+id).html(data.remaining);

                    $('#leaseresreqty'+id).show();
                    //$('#resreordrqty'+id).show();
                    //$('#resreordrlevel'+id).show();
                    $('#editvenresleasecart'+id).show();
                }
                //$('.preloader').hide();
            }
        });       

   });
 
 $(document).on('keyup','.leaseeditresreqty',function(){

    var cartid = $(this).attr('data-id');
    var ven = $(this).attr('data-v');

    var req = $('#leaseeditresreqty'+cartid).val();

    var rate = $('#leaseeditrate'+cartid).val();

    var eqy = $('#loesq'+cartid).html();

    var amnt = eqy * rate;

    if(req != ''){
         var tot = req * rate;
    }

    if(req != ''){
        $('#resamm'+cartid).html(tot.toFixed(2));
        $('#reslotot'+cartid).val(tot);
        
    }else{

        $('#resamm'+cartid).html(amnt.toFixed(2));
        $('#reslotot'+cartid).val(amnt);
        
    }

    var amn = 0;

    $('.loresss_'+ven).each(function(){
        var samt = $(this).val();

        amn = parseInt(amn) + parseInt(samt);

    });
    
    $('.totvenres'+ven).html(amn.toFixed(2));



 });

 $(document).on('click','.splitvendorlease',function(){

    var id=$(this).attr('data-id');

    var cartid=$(this).attr('data-cartid');
    var reqty = $('#leaseeditresreqty'+cartid).val();


    $.ajax({
        type: 'POST',
        url: '../procurement/leasechoosenewvendor',
        /*beforeSend : function(){
            $('.preloader').show();
        },*/
        dataType: "json",
        data: {resourceid:id,cartid:cartid,reqty:reqty},
        success: function(data){
            if(data.error=='No')
            {
                $('#leaseresreqty'+cartid).html($('#leaseeditresreqty'+cartid).val());
                $('#leasevendortotal'+data.vendorid).html(data.totalamt);
                $('#newleaseaddedresources').html(data.result);
            }
            //$('.preloader').hide();
        }
    });       

});

$(document).on('click','.leasechangevendorqty',function(){

    var vendorid=$(this).attr('data-vendorid');

    var resourceid=$(this).attr('data-resourceid');

    var cartid=$(this).attr('data-cartid');

    var splitqty = $('#splitqty'+vendorid).val();

    var requirdqty = $('#leaseeditresreqty'+cartid).val();

    var r = confirm("Are you sure you want to change the vendor?");

    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../procurement/leasechangevendor',
            dataType: "json",
            data: {vendorid:vendorid,resourceid:resourceid,cartid:cartid,splitqty:splitqty,requirdqty:requirdqty},
            success: function(data){
                if(data.error=='No')
                {
                    $('.changeleasevendorpopup').trigger('click');
                    $('#leasesearch').trigger('click');
                }
            }
        });      

    } 

});

/*Lease orders End*/

/*Despatch orders*/

$(function() {
    $('#despsearch').click(function () {
        $.ajax({
            type: 'POST',
            url: '../procurement/despatchorderscart',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            //data: {task:$('#searchtask').val()},
            success: function(data){
                if(data.error=='No')
                {
                    //$('#desporderitems').html(data.result);
                    $('#despcartitemss').html(data.result);
                    //$('#cartitemstable').show();
                }else if(data.error=='yes'){
                    $('#desporr').hide();
                    $('#despform').hide();
                   
                }
                $('.preloader').hide();
            }
        });
    });
});

$(document).on('click','.editvenresdespcart',function(){

        var id=$(this).attr('data-id');

        $('#despresreqty'+id).hide();
        $('#despresreordrqty'+id).hide();
        $('#despresreordrlevel'+id).hide();
        $('#ddesrate'+id).hide();
        $('#editvenresdespcart'+id).hide();

        $('#despeditresreqty'+id).show();
        $('#despeditresreordrqty'+id).show();
        $('#despeditresreordrlevel'+id).show();
        $('#desprate'+id).show();
        
        $('#savevenresdespcart'+id).show();


   });

   $(document).on('click','.savevenresdespcart',function(){

        var id=$(this).attr('data-id');
        var rate = $('#desprate').val();
        var edtreq = $('#despeditresreqty'+id).val();
        var tot = edtreq * rate;

        $.ajax({
            type: 'POST',
            url: '../procurement/savedodetails',
            /*beforeSend : function(){
                $('.preloader').show();
            },*/
            dataType: "json",
            data: {cartid:id,reqty:$('#despeditresreqty'+id).val(),reordqty:$('#despeditresreordrqty'+id).val(),reorderlevel:$('#despeditresreordrlevel'+id).val(),rate:$('#desprate'+id).val()},
            success: function(data){
                if(data.error=='No')
                {

                    $('#despeditresreqty'+id).hide();
                    $('#despeditresreordrqty'+id).hide();
                    $('#despeditresreordrlevel'+id).hide();
                    $('#savevenresdespcart'+id).hide();
                    $('#ddesrate'+id).show();
                    $('#desprate'+id).hide();
                   
                    $('#despresreqty'+id).html($('#despeditresreqty'+id).val());
                    $('#despresreordrqty'+id).html($('#despeditresreordrqty'+id).val());
                    $('#despresreordrlevel'+id).html($('#despeditresreordrlevel'+id).val());
                    $('#ddesrate'+id).html($('#desprate'+id).val());

                    $('#despvendortotal'+data.vendorid).html(data.totalamt);
                    $('#destotamnt'+id).html(data.amount);

                    $('#despbalancetotal'+id).html(data.remaining);

                    $('#despresreqty'+id).show();
                    $('#despresreordrqty'+id).show();
                    $('#despresreordrlevel'+id).show();
                    $('#editvenresdespcart'+id).show();
                }
                //$('.preloader').hide();
            }
        });       

   });

   $(document).on('click','.splitvendordesp',function(){

    var id=$(this).attr('data-id');

    var cartid=$(this).attr('data-cartid');

    $.ajax({
        type: 'POST',
        url: '../procurement/despchoosenewvendor',
        /*beforeSend : function(){
            $('.preloader').show();
        },*/
        dataType: "json",
        data: {resourceid:id,cartid:cartid},
        success: function(data){
            if(data.error=='No')
            {
                $('#newdespaddedresources').html(data.result);
            }
            //$('.preloader').hide();
        }
    });       

});

$(document).on('click','.despchangevendorqty',function(){

    var vendorid=$(this).attr('data-vendorid');

    var resourceid=$(this).attr('data-resourceid');

    var cartid=$(this).attr('data-cartid');

    var splitqty = $('#splitqty'+vendorid).val();

    var requiedqty = $('#despeditresreqty'+cartid).val();

    if(requiedqty){

    var r = confirm("Are you sure you want to change the vendor?");

    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../procurement/despchnagevendor',
            dataType: "json",
            data: {vendorid:vendorid,resourceid:resourceid,cartid:cartid,splitqty:splitqty,requiedqty:requiedqty},
            success: function(data){
                if(data.error=='No')
                {
                    $('.changedespvendorpopup').trigger('click');
                    $('#despsearch').trigger('click');
                }
            }
        });      

    } 
}

});
$(document).on('click','#despcartitems input',function(){

    var values = [];
    var ordervalues = [];
    var restype = [];
    var project = [];
    $('#despcartitems input:checked').each(function() {
        values.push($(this).val());
        ordervalues.push($(this).attr('data-id'));
        restype.push($(this).attr('data-restype'));
        project.push($(this).attr('data-project'));
        $resttype = $(this).attr('data-res');
    });
    $('[name="desvenids"]').attr({value: values.join(', ')});
    $('[name="descrtid"]').attr({value: ordervalues.join(', ')});
    $('[name="desrresidss"]').attr({value: restype.join(', ')});
    $('[name="desprjss"]').attr({value: project.join(', ')});
     $('#desrestype').val($resttype);




   /*
    $('#despcartitems input:checked').each(function() {
        $vend_id = $(this).val();
        $prj = $(this).attr('data-project');
        $res = $(this).attr('data-restype');
        $cartid = $(this).attr('data-id');
        $resttype = $(this).attr('data-res');

        $('#desvenids').val($vend_id );
        $('#desrresidss').val($res);
        $('#desprjss').val($prj);
        $('#descrtid').val($cartid);
        $('#desrestype').val($resttype);

        
    });*/
   
});

$(document).on('click','#placeorder_desporder',function(){

if($('.vendors').is(":checked")) {

    var venid = $(this).attr('data-ven');
    var proid = $(this).attr('data-pro');
    var resid = $('#desrresidss').val();
    var cartid = $('#descrtid').val();
    var restype = $('#desrestype').val();
   

 

    $.ajax({
        type: 'POST',
        url: '../procurement/desporders',
        dataType: "json",
        data: {venid: venid,proid: proid,mode:5,resid:resid,orders:cartid,restype:restype},
        success: function (data) {
            if (data.error == 'No') {

                if(data.order == 'despatch'){
                    ordertype="Despatch Order";
                    $('#despporders').show();
                    $('#despporders').html(data.despatchorder);  
                    $('#despform').hide();
                    $('#workform').hide();
                    $('#recartform').hide();
                    $('#recartform').hide();
                    $('#carttform').hide();

                    $('#directworkform').hide();
                    $('#leaseform').hide();
                    $('.topsbars').hide();
                    $('.headngstyle').hide();
                    $('.orders-wrpr').hide();
                    $('.placeorder-list').hide();
                }
            }
        }
    });
 }else {
         alert('Please select any vendor before proceeding.');
         return false;
     }

});
$(document).on('click','#cancelorderdespatchhs',function(){   


    $('.topsbars').show();
         $('#despporders').hide();
            
          $('#collapssedespatch').show();
          $('#despform').show();

          $('#workform').show();
          $('#recartform').show();
          $('#recartform').show();
          $('#carttform').show();

          $('#directworkform').show();
          $('#leaseform').show();
          
          $('.headngstyle').show();
          $('.orders-wrpr').show();
          $('.placeorder-list').show();
            
            // setTimeout(function(){

            //     parent.closeFrame2();
                    
            // },500);
                      
           /* parent.window.close();
            window.onunload = function (e) { 
                
             opener.refreshParentWindow();  
             };

    $.ajax({
        type: 'POST',
        url: '../procurement/droptemp',
        beforeSend : function(){
        },
        dataType:"json",
        data:{},
        success: function(data){
        }
    });        */      
            
 });


$(document).on('keyup','.despreqq',function(){

    var cartid = $(this).attr('data-id');
    var ven = $(this).attr('data-v');

    var req = $('#despeditresreqty'+cartid).val();

    var rate = $('#desprate'+cartid).val();

    var eqy = $('#desestqy'+cartid).val();

    

    if(req != ''){
         var tot = req * rate;
    }

    if(req != ''){
        
        $('#destotamnt'+cartid).html(tot.toFixed(2));
        $('#resdeotot'+cartid).val(tot);
        
    }else{
        
        var amntt = eqy * rate;
        $('#destotamnt'+cartid).html(amntt.toFixed(2));
        $('#resdeotot'+cartid).val(amntt);
        
    }

    var amn = 0;

    $('.doresss_'+ven).each(function(){
        var samt = $(this).val();

        amn = parseInt(amn) + parseInt(samt);

    });
    
    $('.totdovenres'+ven).html(amn.toFixed(2));



 });

/* $(document).on('keyup','.fonts',function(){

    var resid= $('#residdds').val();


     $.ajax({
            type: 'POST',
            url: '../procurement/editresname',
            dataType: "json",
            data: {resid:resid,resname:$('.resrouces-hding').val()},
            success: function(data){
                if(data.error=='No')
                {
                    
                }
            }
        }); 



}); */


/*Despatch orders End*/


$(document).on('click','.delord',function(){

    var venid = $(this).attr('data-id');

    var projid = $(this).attr('data-val');

    var cartid = $(this).attr('data-cart');


    var r = confirm("Are you sure you want to delete this Order?");
        if (r == true) {
            $.ajax({
                type: 'POST',
                url: '../procurement/deletewrkorder',
                dataType: "json",
                data: {venid:venid,projid:projid,cartid:cartid},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#cartitemrow'+cartid).remove();
                        
                        $('#ven-resources'+cartid).remove();
                    }

                }
            });
        }

});

$(document).on('click','.delcartord',function(){

    var venid = $(this).attr('data-id');

    var projid = $(this).attr('data-val');

    var cartid = $(this).attr('data-cart');


    var r = confirm("Are you sure you want to delete this Order?");
        if (r == true) {
            $.ajax({
                type: 'POST',
                url: '../procurement/deletecartorder',
                dataType: "json",
                data: {venid:venid,projid:projid,cartid:cartid},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#cartitemrow'+cartid).remove();
                        
                        $('#ven-resources'+cartid).remove();
                    }

                }
            });
        }

});
$(document).on('click','.delrecartord',function(){

    var venid = $(this).attr('data-id');

    var projid = $(this).attr('data-val');

    var cartid = $(this).attr('data-cart');


    var r = confirm("Are you sure you want to delete this Order?");
        if (r == true) {
            $.ajax({
                type: 'POST',
                url: '../procurement/deleterecartorder',
                dataType: "json",
                data: {venid:venid,projid:projid,cartid:cartid},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#cartitemrow'+cartid).remove();
                        
                        $('#ven-resources'+cartid).remove();
                    }

                }
            });
        }

});
$(document).on('click','.deldirord',function(){

    var venid = $(this).attr('data-id');

    var projid = $(this).attr('data-val');

    var cartid = $(this).attr('data-cart');


    var r = confirm("Are you sure you want to delete this Order?");
        if (r == true) {
            $.ajax({
                type: 'POST',
                url: '../procurement/deletedirorder',
                dataType: "json",
                data: {venid:venid,projid:projid,cartid:cartid},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#cartitemrow'+cartid).remove();
                        
                        $('#ven-resources'+cartid).remove();
                    }

                }
            });
        }

});
$(document).on('click','.delleaseord',function(){

    var venid = $(this).attr('data-id');

    var projid = $(this).attr('data-val');

    var cartid = $(this).attr('data-cart');


    var r = confirm("Are you sure you want to delete this Order?");
        if (r == true) {
            $.ajax({
                type: 'POST',
                url: '../procurement/deleteleaseorder',
                dataType: "json",
                data: {venid:venid,projid:projid,cartid:cartid},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#cartitemrow'+cartid).remove();
                        
                        $('#ven-resources'+cartid).remove();
                    }

                }
            });
        }

});

$(document).on('click','.deldespord',function(){

    var venid = $(this).attr('data-id');

    var projid = $(this).attr('data-val');

    var cartid = $(this).attr('data-cart');


    var r = confirm("Are you sure you want to delete this Order?");
        if (r == true) {
            $.ajax({
                type: 'POST',
                url: '../procurement/deletedesporder',
                dataType: "json",
                data: {venid:venid,projid:projid,cartid:cartid},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#cartitemrow'+cartid).remove();
                        
                        $('#ven-resources'+cartid).remove();
                    }

                }
            });
        }

});


function updatePurchaseOrderTotal(){
    var finaltax= getTotalTax();  
    $('#tax').val(finaltax);

    var freight = $('#freight').val();
    var insurance = $('#insurance').val();
    var others = $('#others').val();
    var subtotal = $('#sub_total').val();  

    var finalnumber= Number(subtotal) + Number(finaltax) + Number(freight) + Number(insurance) + Number(others); 
    var  final=parseFloat(finalnumber).toFixed(2);  

    $('.newtotal').val(final);  // alert(final);
}
