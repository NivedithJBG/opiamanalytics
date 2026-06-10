$(document).on( "click", "#Purchasevieworders", function(){  
    $('#projordersearch').trigger('click');
    $('.close-material-history-btn').trigger('click');
    $(".Purchase-Orders-History").css("display", "none");
    $("#Purchase-Orders-List").css("display", "block");

     // $('#collapseorder').show();

});

 // $(document).on( "click", "#Purchasevieworders", function(){   
   
      
 //       //$('.imdata').hide();
 //       $('.rmdata').show();
 //       $('#projordersearch').trigger('click');

 //   });


 //         $(document).on( "click", "#issueslips", function(){  
   
 //      $('.imdata').show();
 //      $('.rmdata').hide();

 //   });



$(function() {
    $('#projordersearch').click(function () {  
        $('#purchasehistorysection').hide();
        //$('#Purchase-Orders-History').hide();
        $('#projorderslist').show();
        $.ajax({
            type: 'POST',
            url: '../projects/purchaseorders',
            beforeSend : function(){
               // $('.preloader').show();
            },
            dataType: "json",
            data: {vendor:$('#searchpovendor').val()},
            success: function(data){
                if(data.error=='No')
                {
                    //$('#opprojectname').html(data.projectinfo);
                    $('#Purchase-Orders-List').html(data.result);
                    $("#Purchase-Orders-List").css("display", "block");
                    $("#Purchase-Orders-History").css("display", "none");
                    $("#rmdtaa").css("display", "none");
                    
                    
                    //$('#Purchase-Orders-List').show();
                }
                $('.preloader').hide();
            }
        });
    });
    $('#posearch').click(function(){
        $('#projordersearch').trigger('click');
    });
    $('#purchasehistory').click(function () {
        
        $.ajax({
            type: 'POST',
            url: '../projects/purchasehostory',
            beforeSend : function(){
                //$('.preloader').show();
            },
            dataType: "json",
            data: {vendor:$('#searchpohistory').val()},
            success: function(data){
                if(data.error=='No')
                {
                    //$('#opprojectname2').html(data.projectinfo);
                    $('#Purchase-Orders-History').html(data.result);
                    $('#Purchase-Orders-History').css('display','block');
                    $('#coh').css('display','block');
                    $('#headerzz').css('display','block');
                        $("#Purchase-Orders-Receive-Materials").css("display", "none");

                    //$('#Purchase-Orders-History').show();
                }
                $('.preloader').hide();
            }
        });
    });
    $('#pohistorysearch').click(function(){
        $('#purchasehistory').trigger('click') ;
          $('#headerzz').css('display','block');
    });
    $(document).on( "click", "#POreceivematerials", function(){
        var orderid=$(this).attr('data-v');
        $('#Purchase-Orders-Receive-Materials').empty();
        $.ajax({
            type: 'POST',
            url: '../projects/receiveorder',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {orderid: orderid},
            success: function(data){
                if(data.error=='No')
                {
                    //$('#opprojectname2').html(data.projectinfo);
                    $('#Purchase-Orders-Receive-Materials').html(data.result);
                     $("#Purchase-Orders-List").css("display", "none");
                     $("#rmdtaa").css("display", "block");
                    //$('#Purchase-Orders-Receive-Materials').show();
                }
                $('.preloader').hide();
            }
        });
    });
    $(document).on( "click", "#POInvoice", function(){
        $("#rmdtaa").css("display", "block");
        var orderid=$(this).attr('data-v');
        $('#Purchase-Orders-Receive-Materials').empty();
        $.ajax({
            type: 'POST',
            url: '../projects/viewinvoice',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {orderid: orderid},
            success: function(data){
                if(data.error=='No')
                {
                    //$('#opprojectname2').html(data.projectinfo);
                    
                    $('#Purchase-Orders-Receive-Materials').html(data.result);
                    $("#Purchase-Orders-Receive-Materials").css("display", "block");
                    //$("#Purchase-Orders-History").css("display", "none");
                    //$("#coh").css("display", "none");
                    
                    //$('#Purchase-Orders-Receive-Materials').show();
                }
                $('.preloader').hide();
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
    $(document).on('focus','#invoicedate',function(){
        $(this).datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
    })
    $(document).on('click','#invoicebtn',function(){
        var error=0;
        $('.error').hide();
        $('.resourceqnty').each(function(){
            var qty=$(this).val();
            var resid=$(this).attr('data-id');
            var maxqty=parseFloat($('#resmaxqty'+resid).val());
            var resqty=parseFloat($('#resourceqnty'+resid).val());
            //alert($('#resourceqnty'+resid).val())
            if (resqty > maxqty)
            {
                //$('#resourceqnty'+resid).next("span").html('Quantity cannot be greater than order quantity').show('slow');
                //error=1;
                $('#resourceqnty'+resid).next("span").html('Quantity greater than order quantity').show('slow');
            }
            if ($('#resourceqnty'+resid).val()==''){
                $('#resourceqnty'+resid).next("span").html('Quantity cannot be blank').show('slow');
                error=1;
            }
        });
        if ($('#invoicenum').val()==''){
            $('#invoicenum').next("span").html('Please enter Invoice Number').show('slow');
            error=1;
        }
        if (error==0){
            $.ajax({
                type: 'POST',   
                url: '../projects/receiveordersave',  
                beforeSend : function(){  
                   // $('#invoicebtn').attr("disabled", true);
                },  
                dataType: "json",  
                data: $( "#invoiceformdata" ).serialize(),   
                success: function(data){   
                    if(data.error=='No')  
                    {  
                        $('#invoiceformdata')[0].reset();
                        //$('#invoicebtn').attr("disabled", false); 
                        //$('.close-order-history-btn').trigger('click');  
                        $('#cancelinvoice').trigger('click');  
                    } 
                }  
            });
        }
        else {
            return false;
        }
    });
    $(document).on('click','#completebtn',function(){
        var error=0;
        $('.error').hide();
        $('.resourceqnty').each(function(){
            var qty=$(this).val();
            var resid=$(this).attr('data-id');
            var maxqty=parseFloat($('#resmaxqty'+resid).val());
            var resqty=parseFloat($('#resourceqnty'+resid).val());
            //alert($('#resourceqnty'+resid).val())
            if (resqty > maxqty)
            {
                //$('#resourceqnty'+resid).next("span").html('Quantity cannot be greater than order quantity').show('slow');
                //error=1;
            }
            if ($('#resourceqnty'+resid).val()==''){
                $('#resourceqnty'+resid).next("span").html('Quantity cannot be blank').show('slow');
                error=1;
            }
        });
        if ($('#invoicenum').val()==''){
            $('#invoicenum').next("span").html('Please enter Invoice Number').show('slow');
            error=1;
        }
        if (error==0){
            $.ajax({
                type: 'POST',   
                url: '../projects/receiveordercomplete',
                beforeSend : function(){  
                    $('#completebtn').attr("disabled", true);
                },  
                dataType: "json",  
                data: $( "#invoiceformdata" ).serialize(),   
                success: function(data){   
                    if(data.error=='No')  
                    {  
                        $('#invoiceformdata')[0].reset();
                        $('#completebtn').attr("disabled", false);
                        //$('.close-order-history-btn').trigger('click');
                        $('#cancelinvoice').trigger('click');   
                    } 
                }  
            });
        }
        else {
            return false;
        }
    });
    $(document).on('click','#cancelinvoice', function(e){
        $('#searchpovendor').val('none');
        $('#posearch').trigger('click');
        $('#headerzz').css('display','block');
        $('#projordersearch').trigger('click');
        $("#Purchase-Orders-List").css("display", "block");
        
    });
    $(document).on('keyup change','.resourceqnty',function(){
        var resid=$(this).attr('data-id');
        var qty=parseFloat($(this).val());
        var rate=parseFloat($('#poresrate'+resid).val());
        //alert('rate: '+rate)
        var amount=qty * rate;
        var gstper=0;
        if ($('#poresgst'+resid).val()!=0)
        {
            gstper=$('#poresgst'+resid).val();
        }
        else if ($('#poresigst'+resid).val()!=0)
        {
            gstper=$('#poresigst'+resid).val();
        }
        var gstamount=(amount * gstper) / 100;
        var totamount=amount + gstamount;
        if(isNaN(qty)){
            amount = 0;
            gstamount = 0;
            totamount = 0;
            $('#poresamount'+resid).html(amount.toFixed(2));
            $('#poresgstamnt'+resid).html(gstamount.toFixed(2));
            $('#poresnetht'+resid).html(totamount.toFixed(2));
            $('#poresnet'+resid).val(totamount);
        }
        else{
            $('#poresamount'+resid).html(amount.toFixed(2));
            $('#poresgstamnt'+resid).html(gstamount.toFixed(2));
            $('#poresnetht'+resid).html(totamount.toFixed(2));
            $('#poresnet'+resid).val(totamount);
        }
        
        var totalamount=0;
        $('.total-class').each(function(){
            var newid = $(this).attr('data-id');
            totalamount=totalamount + parseFloat($('#poresnet'+newid).val());
        });
        //$('#totalpoamount').html(totamount.toFixed(2));
        //$('#totalpoamount1').val(totamount.toFixed(2));

        $('#totalpoamount').html(totalamount.toFixed(2));
        $('#totalpoamount1').val(totalamount.toFixed(2));
    });
    $(document).on('click','.order-history-btn ', function(e){
        e.preventDefault();
        $('.receive-materials-tab').addClass('order-history-cntnt-active');
        $('#projorderslist').hide();
        $('#purchasehistorysection').show();
    });
    
    $(document).on('click','.close-order-history-btn ', function(e){
        e.preventDefault();
        $('.receive-materials-tab').removeClass('order-history-cntnt-active');
        $('#projorderslist').show();
        $('#purchasehistorysection').hide();
        $(".Purchase-Orders-History").css("display", "none");
        
    });


    $(document).on('change','#otdeductions',function(){
        var nettotal=parseFloat($('#nettotal').val());
        var otded=parseFloat($(this).val());
        var netamount=nettotal - otded;
        $('#billnetamount').html(netamount.toFixed(2));
        $('#billnetamountval').val(netamount);
        var advance=parseFloat($('#advance').val());
        var amntpay=netamount - advance;
        $('#amountpayabe').html(amntpay.toFixed(2));
        $('#amountpayval').val(amntpay);
    });

    $(document).on('click','#savebill',function(){
        var error=0;
        $('.resourceqnty').each(function(){
            var resid=$(this).attr('data-id');
            var cqty=$(this).val();
            var actreportqty=$('#actreportqty'+resid).val();
            if(parseFloat(cqty) > parseFloat(actreportqty))
            {
                //alert('Current Quantity cannot be greater than reported quantity')
                error=0;
            }
        });
        if($('#billno').val()==''){
            $('#billno').next("span").html('Please enter Bill Number').show('slow');
            error=1;
        }
        if (error==0)
        {
            return true;
        }
        else {
            return false;
        }
    });
    $(document).on('click','#completebill',function(){
        var error=0;
        $('.resourceqnty').each(function(){
            var resid=$(this).attr('data-id');
            var cqty=$(this).val();
            var actreportqty=$('#actreportqty'+resid).val();
            if(parseFloat(cqty) > parseFloat(actreportqty))
            {
                //alert('Current Quantity cannot be greater than reported quantity')
                error=0;
            }
        });
        if($('#billno').val()==''){
            $('#billno').next("span").html('Please enter Bill Number').show('slow');
            error=1;
        }
        if (error==0)
        {
            return true;
        }
        else {
            return false;
        }
    });
});

$(document).on( "click", ".emailorder", function(){
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
});

$(document).on( "click", "#emailorder", function(){
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


});
function getProjectname(id)

{

    var retval;

    $.ajax({

        type: 'POST',

        url: '../projects/Getname',

        async:false,

        data: {id:id},

        success: function(data){

            retval=data;

        }

    });

    return retval;

}