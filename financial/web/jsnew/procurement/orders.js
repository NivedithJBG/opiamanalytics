/**
 * Created by SolmindsDelli5 on 23-08-2017.
 */
$(document).on( "click", "#confirm-Orders", function(){
    history.replaceState(null, null, ' ');
//$(document).on( "click", ".confirmorder_tab", function(){  

    /*if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }*/

    /*$('.panel-group').removeClass('acco-billofres-active');
    $('.panel-group').removeClass('acco-vendors-active');
    $('.panel-group').removeClass('acco-one-active');
    $('.panel-group').addClass('acco-confirmorders-active');
    $('.panel-group').removeClass('acco-despatchorders-active');*/

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
    $('.panel-group').removeClass('acco-eleven-active');

    $('#cfmppurchor').trigger('click');
    $('.purrr').addClass('active');
    $('#cnfrmhistory').show();

});

$(function() {
   /*  $('#ordersearch').click(function () {
        $.ajax({
            type: 'POST',
            url: '../procurement/orders',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            //data: {task:$('#searchtask').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#orderitems').html(data.result);
                    $('#orderitemstable').show();
                    $('#historiescnfrm').hide();
                }
                $('.preloader').hide();
            }
        });
    }); */
});

$(document).on( "click", ".historycnfrm", function(){

    var type = $('#identifycf').val();

    
    $.ajax({
            type: 'POST',
            url: '../procurement/orderhistory',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {type:type},
            success: function(data){

               //var p= $.isNumeric("0");

                 var p = data.otypee;
                 var chk=$.isNumeric(p);
               

                // if(data.otypee==true){
                //     alert ("asad");
                // }


                    
                if(data.error=='No' && chk==true){
                    // alert("sdad");

                      $('#cnfrmorderitemscf').html(data.result);
                      $('#cnfrmorderitemscf').show();
                       $('#cnfrmorderitems').hide();
                      
                       $('#confrmhstrytable').hide();
                       $('#confrmhstrytablecf').show();
                       $('#historiescnfrm').show();

                      $('#historycnfrm').hide();
                       $('.topbar').hide();
                       $('#orderitemstablecf').hide();
                        $('#orderitemstable').hide();

                }else if(data.error=='No' && chk==false)
                {
                    $('#cnfrmorderitems').html(data.result);
                    $('#confrmhstrytable').show();
                     $('#cnfrmorderitemscf').hide();
                    $('#confrmhstrytablecf').hide();
                    $('#historiescnfrm').show();
                    $('#historycnfrm').hide();
                    $('.topbar').hide();
                    $('#orderitemstable').hide();
                    $('#orderitemstablecf').hide();
                }
                $('.preloader').hide();
            }
        });

    });
$(document).on( "click", "#cnfrmback", function(){
     $('#orderitemstable').show();
     $('#historiescnfrm').hide();
     $('#historycnfrm').show();
     $('.topbar').show();
});

$(document).on( "click", ".cancelconfirmorder", function(){
    var orderid=$(this).val();
    $.ajax({
        type: 'POST',
        url: '../procurement/cancelorder',
        dataType: "json",
        data: {orderid:orderid},
        success: function(data){
            if(data.error=='No')
            {
                $('#ordersearch').trigger('click');
                $('#cfmppurchor').trigger('click');
            }
            else
            {
                alert(data.errortext);
            }
        }
    });
});




$(document).on('click','.approve',function(){
$('#historiescnfrm').hide();
$('#historycnfrm').hide();
$('.topbar').hide();
     // var id=$(this).val();
     //var ordertype=$('#ordertype').val();
    var order = $(this).attr("data-id");

     var id=$(this).data('value');

     

      //alert (order);

   
      


  $.ajax({
            type: 'POST',
            url: '../procurement/view?id='+id,
            dataType: "json",
            data: {id: id},
            success: function (data) {
                if (data.error == 'No') {

                    $('.confirmlists').hide();


                
                    if(order==2 || order==4){   
                  //ordertype="Work Order";  
                $('#approveworkandleaseorder').html(data.workandleaseorder);  
                 $('#approveworkandleaseorder').show();
            }

            else{
                  $('#approveotherdata').html(data.otherorder);  
                 $('#approveotherdata').show();
            }

              
         //$('#resourcessearch').trigger('click');  

                }
            }
        });


    });




  $(document).on('click','#approveorderbtn',function(){
        var test='1';

        //var id=$(this).data('value');

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

             $('#approveworkandleaseorder').hide();
            
         

             //$('#placeOrderiframe').attr('src', baseUrl);
              $('#approveworkandleaseorder').show();
           
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


$(document).on('click','#cancelordernew',function(){    


      
         $('#approveworkandleaseorder').hide();
         $('#historycnfrm').show();
         $('.topbar').show();
            
          $('#new').show();
            
            // setTimeout(function(){

            //     parent.closeFrame2();
                    
            // },500);
                      
            parent.window.close();
            window.onunload = function (e) { 
                
             opener.refreshParentWindow();  
             };            
            
        });



     $(document).on('click','#cancelorderneww',function(){  
        $('#historycnfrm').show();
        $('.topbar').show();   


      
         $('#approveotherdata').hide();
            
          $('#new').show();
            
            // setTimeout(function(){

            //     parent.closeFrame2();
                    
            // },500);
                      
            parent.window.close();
            window.onunload = function (e) { 
                
             opener.refreshParentWindow();  
             };            
            
        });






     $(document).on('click','#approveorderbtnnew',function(){ 
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
        /*if(!$.isNumeric($('#advance').val()))
        {
            $('#advance').next("span").html('Enter Valid Amount').show('slow');
            error=1;
        }*/

        if($('#advance').val()!=undefined)
        {
            if(!$.isNumeric($('#advance').val()))
            {
                $('#advance').next("span").html('Enter Valid Amount').show('slow');
                error=1;
            }
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


        var orderofdate=$('#orderofdate').val();

        var dateofdelivery=$('#dateofdelivery').val();

         if(dateofdelivery>orderofdate){
            alert("Order date is later than the order delivery date.");
                error=1;
        }



        /*if($('#cgst').val()=='0' && $('#igst').val()=='0'){
            alert("Please enter valid  GST / IGST tax.");
            error=1;
        }*/
        /* if($('#cgst').val()!='' ||  $('#igst').val()!=''){
           // alert("Please enter either GST / IGST tax.");
            error=0;
        }
        else{
             alert("You can not select both IGST as well as the other tax.");
                error=1;
        }*/

      /*  if($('#cgst').val()!=''){
            if($('#igst').val()!=''){
                alert("You can not select both IGST as well as the other tax.");
                error=1;
            }
        }*/

        // if($('#cgst').val()!=''){
        //     if($('#igst').val()!=''){
               
        //     }
        // }


        if(error==0){  

          $('#approveotherdata').hide();
            
         

             //$('#placeOrderiframe').attr('src', baseUrl);
              $('#approveotherdata').show();
            
           
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



/*$(document).on( "click", ".emailorder", function(){
    var orderid=$(this).val();
    $('#orderemail')[0].reset();
    $('#ordersuccesinfo').hide();
    $('#ordererrorinfo').hide();
    $.ajax({
        type: 'POST',
        url: '../Procurement/VendorDetails',
        dataType: "json",
        data: {orderid:orderid},
        success: function(data){
            if(data.error=='No')
            {
                $('#orderemailid').val(data.result);
                $('#orderid').val(orderid);

            }
        }
    });
});*/

/*$(document).on( "click", "#emailorder", function(){
    var email=$('#orderemailid').val();
    var subject=$('#ordersubject').val();
    var body=$('#orderbody').val();

    var orderid=$('#orderid').val();
    var error=0;
    $('.error').hide();

    if($('#orderemailid').val()=='')
    {
        $('#orderemailid').next("span").html('Enter Email address').show('slow');
        error=1;
    }
    if($('#ordersubject').val()=='')
    {
        $('#ordersubject').next("span").html('Enter Subject').show('slow');
        error=1;
    }
    if($('#orderbody').val()=='')
    {
        $('#orderbody').next("span").html('Enter Body').show('slow');
        error=1;
    }
    if (error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../Procurement/SendOrderemail',
            beforeSend : function(){
                $('.mailloader').show();
            },
            dataType: "json",
            data: {email:email,subject:subject,body:body,orderid:orderid},
            success: function(data){
                if(data.error=='No')
                {
                    $('.mailloader').hide();
                    $('#ordersuccesinfo').show();
                    $('#ordersuccesinfo').html('<span style="text-align: center">'+data.errortext+'</span>');
                    $('#ordererrorinfo').hide();
                }
                else {
                    $('.mailloader').hide();
                    $('#ordererrorinfo').show();
                    $('#ordererrorinfo').html('<span style="text-align: center">'+data.errortext+'</span>');
                    $('#ordersuccesinfo').hide();
                }
                setTimeout(function(){
                    $('#emailorderModel').modal('toggle');
                }, 5000);

            }
        });
    }


});*/

$(document).on('click','#cfmppurchor',function(){

    $('#identifycf').val('cfpurchase');

    var type = $('#identifycf').val();

    $.ajax({
        type: 'POST',
        url: '../procurement/orders',
        beforeSend : function(){
            $('.preloader').show();
        },
        dataType: "json",
        data: {type:type},
        success: function(data){
            if(data.error=='No')
            {
                $('#orderitems').html(data.result);
                $('#orderitems').show();
                $('#orderitemstable').show();
                 $('#orderitemscf').hide();
                $('#orderitemstablecf').hide();
                $('#historiescnfrm').hide();
            }
            $('.preloader').hide();
        }
    });
    
});
$(document).on('click','#cfmpworko',function(){

    $('#identifycf').val('cfwork');

    var type = $('#identifycf').val();

    $.ajax({
        type: 'POST',
        url: '../procurement/orders',
        beforeSend : function(){
            $('.preloader').show();
        },
        dataType: "json",
        data: {type:type},
        success: function(data){
            if(data.error=='No')
            {
                $('#orderitems').html(data.result);
                $('#orderitems').show();
                $('#orderitemstable').show();
                 $('#orderitemscf').hide();
                $('#orderitemstablecf').hide();
                $('#historiescnfrm').hide();
            }
            $('.preloader').hide();
        }
    });


    
});
$(document).on('click','#cfmpdirec',function(){

    $('#identifycf').val('cfdirect');

    var type = $('#identifycf').val();

    $.ajax({
        type: 'POST',
        url: '../procurement/orders',
        beforeSend : function(){
            $('.preloader').show();
        },
        dataType: "json",
        data: {type:type},
        success: function(data){
            if(data.error=='No')
            {
                $('#orderitemscf').html(data.result);
                 $('#orderitems').hide();
                $('#orderitemscf').show();
                $('#orderitemstable').hide();
                $('#orderitemstablecf').show();
                $('#historiescnfrm').hide();
            }
            $('.preloader').hide();
        }
    });
    
});
$(document).on('click','#cfmpleaso',function(){

    $('#identifycf').val('cflease');

    var type = $('#identifycf').val();

    $.ajax({
        type: 'POST',
        url: '../procurement/orders',
        beforeSend : function(){
            $('.preloader').show();
        },
        dataType: "json",
        data: {type:type},
        success: function(data){
            if(data.error=='No')
            {
                $('#orderitems').html(data.result);
                $('#orderitems').show();
                $('#orderitemstable').show();
                $('#orderitemscf').hide();
                $('#orderitemstablecf').hide();

                $('#historiescnfrm').hide();
            }
            $('.preloader').hide();
        }
    });
    
});
$(document).on('click','#cfmpdespto',function(){

    $('#identifycf').val('cfdesp');

    var type = $('#identifycf').val();

    $.ajax({
        type: 'POST',
        url: '../procurement/orders',
        beforeSend : function(){
            $('.preloader').show();
        },
        dataType: "json",
        data: {type:type},
        success: function(data){
            if(data.error=='No')
            {
                $('#orderitems').html(data.result);
                $('#orderitems').show();
                $('#orderitemstable').show();
                 $('#orderitemscf').hide();
                $('#orderitemstablecf').hide();
                $('#historiescnfrm').hide();
            }
            $('.preloader').hide();
        }
    });
    
});
 