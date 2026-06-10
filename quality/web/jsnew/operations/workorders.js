
$(document).on( "click", "#viewworkorders", function(){
    $('#wrkbls').trigger('click') ;
    $('#wrkbls').parent().addClass('active') ;
    //$('#workorderbillsearch').trigger('click');
    //$('#posearch').trigger('click') ;
    $('.ordss').addClass('active');

    $('.leord').hide();
    $('.workss').hide();
    $('.poss').show();
});

$(document).on( "click", "#poord", function(){
    $('#posearch').trigger('click') ;
    
    $('.leord').hide();
    $('.workss').hide();
    $('.poss').show();



});

$(document).on( "click", "#woord", function(){
    
    $('#workordersearch').trigger('click') ;
    $('.workss').show();
    $('.leord').hide();
    $('.poss').hide();

});
$(document).on( "click", "#loord", function(){
    
    
    $('#leaseorderbillsearch').trigger('click') ;
    $('.leord').show();
    $('.workss').hide();
    $('.poss').hide();

});
/*$(function() {

    $('#leaseorderbillsearch').click(function () {
        $.ajax({
            type: 'POST',
            url: '../projects/leaseorders',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            success: function(data){
                if(data.error=='No')
                {
                    $('#projectname-ILE').html(data.projectName);
                    $('#leaseorderitems').html(data.result);
                     $(".history-bill-list data-content-list").css("display", "none");
                     $(".content-wrpr leasebillshwng").css("display", "none");
                }
                $('.preloader').hide();
            }
        });
    });
});*/

$(function() {

    $('#posearch').click(function () {
        $.ajax({
            type: 'POST',
            url: '../projects/poorders',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {vendor:$('#searchwovendor').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#poorderitems').html(data.result);
                     $('#headerzz').css('display','block');
                }
                $('.preloader').hide();
            }
        });
    });

});

$(function() {
    $('#workordersearch').click(function () {
        $.ajax({
            type: 'POST',
            url: '../projects/workorders',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {vendor:$('#searchwovendor').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#workorderitems').html(data.result);
                }
                $('.preloader').hide();
            }
        });
    });
    $('#searchwovendor').change(function(){
        $('#workordersearch').trigger('click') ;
    });
    $('.close-bill-history-btn').click(function() {
        $('#workordersearch').trigger('click');
    });
    $('#workorderhistory').click(function () {
        $.ajax({
            type: 'POST',
            url: '../projects/workorderhistory',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            success: function(data){
                if(data.error=='No')
                {
                    $('#workorderitemshistory').html(data.result);
                }
                $('.preloader').hide();
            }
        });
    });
});

$(document).on('click','.deleteworkorderbutton',function(){
    var orderid=$(this).attr("data-v");alert(orderid);
    var r = confirm("Are you sure you want to delete this Order ?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../projects/deleteworkorder',
            beforeSend : function(){
                $('#deleteworkorderbutton'+orderid).attr("disabled", true);
            },
            dataType: "json",
            data: {orderid:orderid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#workorderitemrow'+orderid).remove();
                    //$('#listappjobcard').trigger('click');
                }
                else

                {

                    alert(data.errortext);

                }

                $('#deleteworkorderbutton'+orderid).attr("disabled", false);
            }
        });
    }
    else {
        return false;
    }
});

$(document).on('click','.deleteworkbill',function(){
    var billid=$(this).attr('data-v');
    var r = confirm("Are you sure you want to delete this Bill ?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../projects/deleteworkorderbill',
            beforeSend : function(){
                $('#deleteworkbill'+billid).attr("disabled", true);
            },
            dataType: "json",
            data: {billid:billid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#workorderbillrow'+billid).remove();
                    //$('#listappjobcard').trigger('click');
                }
                $('#deleteworkbill'+billid).attr("disabled", false);
            }
        });
    }
    else {
        return false;
    }
});

$(document).on("click", '.tab-wrapper .viewbillitems', function(){
    $(this).parents('.tab').addClass('edit-form-active');
    $(this).parents('.tab').removeClass('add-form-active');
    var orderid = $(this).attr('data-v');
        $.ajax({
        type: 'POST',
        url: '../projects/viewworkorder',
        beforeSend : function(){
            $('.preloader').show();
        },
        dataType: "json",
        data: {orderid:orderid},
        success: function(data){
            if(data.error=='No')
            {
                $("#raisebillitemsview").html(data.result);
            }
            $('.preloader').hide(); 
        }
    });
});

$(document).on("click", '.viewWorkorders', function(){

    var orderid = $(this).attr('data-value');
        $.ajax({
        type: 'POST',
        url: '../projects/receiveorder',
        beforeSend : function(){
            
        },
        dataType: "json",
        data: {orderid:orderid},
        success: function(data){
            if(data.error=='No')
            {
                $("#raisebillitems").html(data.result);
            }
            
        }
    });

});

$(document).on('change','.resourceqnty',function(){
    var resid=$(this).attr('data-id');
    var cqty=$(this).val();
    var uqty=$('#qtyuptolast'+resid).val();
    var rate=$('#resrate'+resid).val();
    var camount=cqty * rate;
    var uamount=$('#amountuptolast'+resid).val();
    var totalqty=parseFloat(cqty) + parseFloat(uqty);
    var totalamount= parseFloat(camount) + parseFloat(uamount);
    $('#currentamount'+resid).html(camount.toFixed(2));
    $('#camountval'+resid).val(camount);
    $('#totalamount'+resid).html(totalamount.toFixed(2));
    $('#totalamountval'+resid).val(totalamount);
    $('#totalqty'+resid).html(totalqty.toFixed(2));
    var grossamount=0;
    $('.currentamount').each(function(){
        grossamount=grossamount+($(this).val()*1)
    });
    var finaltotal=0;
    $('.totalamountval').each(function(){
        finaltotal=finaltotal+($(this).val()*1)
    });
    //$('#billtotal').html(grossamount.toFixed(2));
    $('#billtotal').html(finaltotal.toFixed(2));
    $('#grossamount').val(finaltotal);
    var grossamount=$('#grossamount').val();
    var sgst=$('#sgst').val();
    var cgst=$('#cgst').val();
    var igst=$('#igst').val();
    var gstamount=(grossamount * sgst) / 100;
    var igstamount=(grossamount * igst) / 100;
    $('#sgstsmount').html(gstamount.toFixed(2));
    $('#cgstamount').html(gstamount.toFixed(2));
    $('#igstamount').html(igstamount.toFixed(2));
    //alert(igst)
    if (typeof sgst!='undefined'){
        //alert(gstamount)
        var amountinc=parseFloat(grossamount) + parseFloat(gstamount) + parseFloat(gstamount);
    }
    else if (typeof igst!='undefined'){
        var amountinc=parseFloat(grossamount) + parseFloat(igstamount);
    }
    else {
        var amountinc=parseFloat(grossamount);
    }
    //alert("yes")
    $('#amountinclusive').html(amountinc.toFixed(2));
    var retention=$('#retention').val();
    var retenamount=(grossamount * retention) / 100;
    $('#billretention').html(retenamount.toFixed(2));
    $('#nettotal').val(amountinc - retenamount);
    var otdedct=$('#otdeductions').val();
    if (otdedct!=''){
        var nettot=parseFloat($('#nettotal').val());
        var netamount=nettot - parseFloat(otdedct);
    }
    else {
        var netamount=amountinc - retenamount;
    }
    $('#billnetamount').html(netamount.toFixed(2));
    var advance=$('#advance').val();
    var amountpay=netamount - advance;
    $('#amountpayabe').html(amountpay.toFixed(2));
    $('#amountpayval').val(amountpay);
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
    $('.error').hide();
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
        $.ajax({
            type: 'POST',
            url: '../projects/workordersave',
            beforeSend : function(){
                $('#savebill').attr("disabled", true);
            },
            dataType: "json",
            data: $( "#workorderform" ).serialize(),
            success: function(data){
                if(data.error=='No')
                {
                    $('#workorderform')[0].reset();
                    $('.cancel').trigger('click');
                    $('#workorderhistory').trigger('click') ;
                    $('#leaseorderhistory').trigger('click') ;
                }
                $('#savebill').attr("disabled", false);
            }
        });
    }
    else {
        return false;
    }
});

$(document).on('click','#completebill',function(){
    var error=0;
    $('.error').hide();
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
        $.ajax({
            type: 'POST',
            url: '../projects/workorderscomplete',
            beforeSend : function(){
                $('#completebill').attr("disabled", true);
            },
            dataType: "json",
            data: $( "#workorderform" ).serialize(),
            success: function(data){
                if(data.error=='No')
                {
                    $('#workorderform')[0].reset();
                    $('.cancel').trigger('click');
                    $('#workorderhistory').trigger('click') ;
                }
                $('#completebill').attr("disabled", false);
            }
        });
    }
    else {
        return false;
    }
});