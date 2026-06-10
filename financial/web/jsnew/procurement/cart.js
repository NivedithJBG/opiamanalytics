/**
 * Created by SolmindsDelli5 on 18-08-2017.
 */
/*$(document).on( "click", ".addvendortocart", function(){
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

    $('.acco-vendors').removeClass('active');

    $('.acco-four').addClass('active');

    $('#collapsecart').addClass('in');

    $("#collapsevendors").attr("aria-expanded","false");

    $("#collapsecart").attr("aria-expanded","true");

    $('#collapsecart').css('height','');

    $('.panel-group').removeClass('acco-billofres-active');
    $('.panel-group').removeClass('acco-vendors-active');
    $('.panel-group').addClass('acco-cart-active');
    $('.panel-group').removeClass('acco-confirmorders-active');
    $('.panel-group').removeClass('acco-despatchorders-active');
    
    
    $('#chooseorder').trigger('click');

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
                    $('#addedresources').html('');
                    $('#choosevendors').removeClass('active').next().slideUp();

                    $('#Cart').addClass('active').next('.acc_container').slideDown();
                    $('#cartsearch').trigger('click');
                }
                $('.preloader').hide();
            }
        });
    }

});*/

$(document).on( "click", "#chooseorder", function(){
    $('#cancelorder').trigger('click');
    $('#cartsearch').trigger('click');

    /*$('.panel-group').removeClass('acco-billofres-active');
    $('.panel-group').removeClass('acco-vendors-active');
    $('.panel-group').addClass('acco-cart-active');
    $('.panel-group').removeClass('acco-confirmorders-active');
    $('.panel-group').removeClass('acco-despatchorders-active');*/

    $('.panel-group').removeClass('acco-two-active');
    $('.panel-group').removeClass('acco-one-active');
    $('.panel-group').removeClass('acco-two-active');
    $('.panel-group').removeClass('acco-three-active');
    $('.panel-group').addClass('acco-four-active');
    $('.panel-group').removeClass('acco-five-active');
    $('.panel-group').removeClass('acco-six-active');
    $('.panel-group').removeClass('acco-seven-active');
    $('.panel-group').removeClass('acco-eight-active');
    $('.panel-group').removeClass('acco-nine-active');
    $('.panel-group').removeClass('acco-ten-active');
  

});


$(document).on( "click", ".acco-cartsssss input[type=radio]", function(){
    //$('.acco-cart input[type=radio]').on('click', function(){
   	
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }
    $('#cartsearch').trigger('click');

});

$(function() {
    $('#cartsearch').click(function () {
        $.ajax({
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
    				
//     				jQuery("html, body").animate({ scrollTop: jQuery('.acco-cart').offset().top }, 1000);				
//     		},60);
//             setTimeout(function(){
//     			        placeorderModal2();	
//     			},60);
            
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
                    $('#purchaseorder').show();
                    $('#purchaseorder').html(data.purchaseorder);  
                    
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




$(document).on('click','#cancelorder',function(){  
 

//$(document).on('click','#purchaseorder,a.cancel',function(){  


      
         $('#purchaseorder').hide();
            
          //$('#placeorderdata').show();

          $('#collapsecart').show();

          $('#carttform').show();
            
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
             $("#tax").val("");
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

  $(document).on('keyup','#cgst',function(){ 
    //$('#cgstpurchaseorder').keyup(function() {  alert ("hi");

        var amount = $('#sub_total').val();
        var gst = $('#cgst').val();
        var total = amount;
        var tot_price = (total * gst / 100);   
        var finaltax= parseFloat(tot_price).toFixed(2);   
        $('#tax').val(finaltax);

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

    });


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
        $('#tax').val(finaltax);
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
        $('#tax').val(finaltax);
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
        $('#tax').val(finaltax);
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
        $('#tax').val(finaltax);
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


   $(document).ready(function(){  

  $(document).on('keyup','#igst',function(){ 

    //$('#igst').keyup(function(ev) {  

        var amount = $('#sub_total').val();
        //var qty = $('#qty').val();
        var igst = $('#igst').val();
        //$(this).val(); 
        var total = amount;
        var tot_price = (total * igst / 100);
        var finaltax= parseFloat(tot_price).toFixed(2);   
        $('#tax').val(finaltax);

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


   $(document).ready(function(){  

  $(document).on('keyup','#noofworkerss',function(){  

    //$('#igst').keyup(function(ev) {  

        var workers = $('#noofworkerss').val();
        //var qty = $('#qty').val();
        var days = $('#noofdayss').val();
        var rates = $('#dirrate').val();
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


    $(document).on('keyup','#noofdayss',function(){  

    //$('#igst').keyup(function(ev) {  

        var workers = $('#noofworkerss').val();
        //var qty = $('#qty').val();
        var days = $('#noofdayss').val();
        var rates = $('#dirrate').val();
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

   $(document).on('change','#myterms',function(){ 
   // $(".tandcupdate").click(function(){  
        if($('.tandcupdate').is(":hidden")){
            
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
                            $('.tandcupdate').hide();
                        }else{
                            alert(data.errortext);
                        }
                    }
                });
            }
            
        }
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
                        $('.tandcupdatepurchase').hide();
                    }else{
                        alert(data.errortext);
                    }
                }
            });
        }else{
            alert('Select one Terms and condition for update');
        }
    });

   $(document).on('change','#mytermspurchase',function(){ 
   // $(".tandcupdate").click(function(){  
        if($('.tandcupdatepurchase').is(":hidden")){
            
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
                            $('.tandcupdatepurchase').hide();
                        }else{
                            alert(data.errortext);
                        }
                    }
                });
            }
            
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
            $('#payment').next("span").html('Enter Mode of payment').show('slow');
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
        if($('#working_hours').val()=='')
        {
            $('#working_hours').next("span").html('Select Working Hours').show('slow');
            error=1;
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
        else{
            return  false;
        }
        
    });


  $(document).on('focus','.date',function(){
        var id=$(this).attr('data-id');
        $('#date'+id+'').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true});

    });


   $(document).on('click','#despatchorderbtn',function(){
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

        $('#editresreqty'+id).show();
        $('#editresreordrqty'+id).show();
        $('#editresreordrlevel'+id).show();
        $('#savevenrescart'+id).show();


   });

   $(document).on('click','.savevenrescart',function(){

        var id=$(this).attr('data-id');
        var rate = $('#resrate'+id).val();
        var edtreq = $('#editresreqty'+id).val();
        var tot = edtreq * rate;
        $.ajax({
            type: 'POST',
            url: '../procurement/savepodetails',
            /*beforeSend : function(){
                $('.preloader').show();
            },*/
            dataType: "json",
            data: {cartid:id,reqty:$('#editresreqty'+id).val(),reordqty:$('#editresreordrqty'+id).val(),reorderlevel:$('#editresreordrlevel'+id).val()},
            success: function(data){
                if(data.error=='No')
                {

                    $('#editresreqty'+id).hide();
                    $('#editresreordrqty'+id).hide();
                    $('#editresreordrlevel'+id).hide();
                    $('#savevenrescart'+id).hide();
                   
                    $('#resreqty'+id).html($('#editresreqty'+id).val());
                    $('#resreordrqty'+id).html($('#editresreordrqty'+id).val());
                    $('#resreordrlevel'+id).html($('#editresreordrlevel'+id).val());
                    $('#restotamount'+id).html(tot.toFixed(2));

                    $('#vendortotal'+data.vendorid).html(data.totalamt);
                    $('#totress'+data.vendorid).html(data.totalamt);

                    $('#balancetotal'+id).html(data.remaining);

                    $('#resreqty'+id).show();
                    $('#resreordrqty'+id).show();
                    $('#resreordrlevel'+id).show();
                    $('#editvenrescart'+id).show();
                }
                //$('.preloader').hide();
            }
        });       

   });
  /* $(document).on('change','.editresreqty',function(){

    var id = $(this).attr('data-id');

    
    var rate = $('#resrate'+id).val();alert(rate)
    var qty = $('#editresreqty'+id).val();alert(qty)
    var amnt = parseInt(rate) * parseInt(qty);

    $('#restotamount'+id).html(amnt);

   });*/


$(document).on('click','.splitvendor',function(){

    var id=$(this).attr('data-id');

    var cartid=$(this).attr('data-cartid');

    $.ajax({
        type: 'POST',
        url: '../procurement/choosenewvendor',
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

$(document).on('click','.changevendorqty',function(){

    var vendorid=$(this).attr('data-vendorid');

    var resourceid=$(this).attr('data-resourceid');

    var cartid=$(this).attr('data-cartid');

    var splitqty = $('#splitqty'+vendorid).val();

    var requiedqty = $('#editresreqty'+cartid).val();

    var r = confirm("Are you sure you want to change the vendor?");

    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../procurement/changevendor',
            dataType: "json",
            data: {vendorid:vendorid,resourceid:resourceid,cartid:cartid,splitqty:splitqty,requiedqty:requiedqty},
            success: function(data){
                if(data.error=='No')
                {
                    $('.changevendorpopup').trigger('click');
                    $('#cartsearch').trigger('click');
                }
            }
        });      

    } 

});








 


