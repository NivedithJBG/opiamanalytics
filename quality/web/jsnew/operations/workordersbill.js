 $(document).on( "click", "#viewworkordersbil", function(){

    $('#workorderbillsearch').trigger('click') ;
    $('.wokbbb').addClass('active');
    $('.leasebillshwng').hide();
    $('.work-orderbill-list-wrpr').show();
    $('#wrkbillhistbutton').show();
    $('#lsbillhistbutton').hide();
});

$(document).on( "click", "#wrkbls", function(){
    $('#workorderbillsearch').trigger('click') ;
    $('.close-bill-history-btn').trigger('click') ;
    $('.leasebillshwng, .wrkbillviews').hide();
    $('.work-orderbill-list-wrpr').show();
    $('#wrkbillhistbutton').show();
    $('#lsbillhistbutton').hide();
    $('#leaseorderbillitems').html('');
});



$(function() {
    $('#workorderbillsearch').click(function () {
        $.ajax({
            type: 'POST',
            url: '../projects/workorderbills',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {},
            success: function(data){
                if(data.error=='No')
                {
                    $('#workorderbills').html(data.result);
                    $('.history-bill-list').hide();
                    $('#wbhis').show();
                }
                $('.preloader').hide();
            }
        });
    });
});

/*
Commented by Sreejith, updated
$(document).on("click", '.viewworkbillitems', function(){
    var orderid = $(this).attr('data-v');
        $.ajax({
        type: 'POST',
        url: '../projects/viewworkorderbills',
        beforeSend : function(){
            $('.preloader').show();
        },
        dataType: "json",
        data: {orderid:orderid},
        success: function(data){
            if(data.error=='No')
            {
            	$('.work-orderbill-list-wrpr').hide();
            	$('.history-bill-list').hide();
                $("#workorderbillitemsview").html(data.result);
                $(".wrkbillviews").show();
            }
            $('.preloader').hide(); 
        }
    });
});*/

$(document).on("click", '.viewworkbillitems', function(){
    var orderid = $(this).attr('data-value');
    $.ajax({
        type: 'POST',
        url: '../projects/viewworkorderbills',
        beforeSend : function(){
            $('.preloader').show();
        },
        dataType: "json",
        data: {orderid:orderid},
        success: function(data){
            if(data.error=='No')
            {
                $('.work-orderbill-list-wrpr').hide();
                $('.history-bill-list').hide();
                $("#workorderbillitemsview").html(data.result);
                $("#leasebillitemsview").html('');
                $(".wrkbillviews").show();
            }
            $('.preloader').hide(); 
        }
    });
});

$(document).on("click", '.billcancel', function(){

	
	$('.work-orderbill-list-wrpr').show();
	$('.history-bill-list').hide();
	$(".wrkbillviews").hide();
	});


$(document).on('click','.deleteworkordbill',function(){
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
                $('#deleteworkordbill'+billid).attr("disabled", false);
            }
        });
    }
    else {
        return false;
    }
});


$(document).on('keyup','#otherDeductions',function(){
    var otdedct = $('#otherDeductions').val();
    otdedct = (otdedct) ? otdedct : 0;
    $('#currentdeductions').html(parseFloat(otdedct).toFixed(2));
    
    wotdeductions = $('#wotdeductions').val();
    wotdeductions = (wotdeductions) ? wotdeductions : 0;
    totDeduction  = parseFloat(wotdeductions) + parseFloat(otdedct);
    
    //$('#wotdeductions').val(totDeduction);
    $('#billotdeduct').html(totDeduction.toFixed(2));

    workOrderBillChanges();
});

$(document).on('keyup','.resourceqntty',function(){
    var resid = $(this).attr('data-id');
    var cqty = $('#resourceqntty'+resid).val();
    var uqty=$('#qtyuptolastt'+resid).val();
    var resrate = $('#rateres'+resid).html();
    var qtyamount = cqty * resrate;
    var camount=cqty * resrate;
    var uamount=$('#amountuptolastt'+resid).val();
    var totalqty=parseFloat(cqty) + parseFloat(uqty);
    var totalamount= parseFloat(camount) + parseFloat(uamount);

    $('#currentamountt'+resid).html(camount.toFixed(2));
    $('#camounttval'+resid).val(camount);
    $('#resamnnt'+resid).val(totalamount.toFixed(2));
    $('#totalqtty'+resid).html(totalqty.toFixed(2));
    $('#qtyamnt'+resid).html(totalamount.toFixed(2));

    workOrderBillChanges();
});

function workOrderBillChanges(){
    var sgst=$('#ssgst').val();
    var cgst=$('#scgst').val();
    var igst=$('#sigst').val();

    var grossamount=0;
    $('.currentamountt').each(function(){
        var gamnt = $(this).val();
        if(gamnt)
            grossamount=grossamount+parseFloat(gamnt);
    });
    $('#currenttotal').html(grossamount.toFixed(2));

    gstamount=(grossamount * sgst) / 100;
    igstamount=(grossamount * igst) / 100;
    $('#currentsgstsmountt').html(gstamount.toFixed(2));
    $('#currentcgstamountt').html(gstamount.toFixed(2));
    $('#currentigstamountt').html(igstamount.toFixed(2));

    if (typeof sgst!='undefined')
        var amountinc=parseFloat(grossamount) + parseFloat(gstamount) + parseFloat(gstamount);
    else if (typeof igst!='undefined')
        var amountinc=parseFloat(grossamount) + parseFloat(igstamount);
    else 
        var amountinc=parseFloat(grossamount);
    $('#currentamounttinclusive').html(amountinc.toFixed(2));

    var retention=$('#rretention').val();
    var retenamount=(grossamount * retention) / 100;
    $('#currentretention').html(retenamount.toFixed(2));

    var otdedct = $('#otherDeductions').val();
    otdedct = (otdedct) ? otdedct : 0;
    $('#currentdeductions').html(parseFloat(otdedct).toFixed(2));
    if (otdedct){
        var nettot = amountinc - retenamount;
        var netamount = nettot - parseFloat(otdedct);
    }
    else {
        var netamount = amountinc - retenamount;
    }
    $('#currentnetamount').html(netamount.toFixed(2));

	var finaltotal=0;
	$('.reourceamount').each(function(){
		var amnt = $(this).val();
        finaltotal=parseInt(finaltotal)+ parseInt(amnt);
    });

    $('#billstotal').html(finaltotal.toFixed(2));
    $('#grssamnt').val(finaltotal.toFixed(2));

    var grossamount = $('#grssamnt').val();

    var gstamount=(grossamount * sgst) / 100;
    var igstamount=(grossamount * igst) / 100;
    $('#sgstsmountt').html(gstamount.toFixed(2));
    $('#cgstamountt').html(gstamount.toFixed(2));
    $('#igstamountt').html(igstamount.toFixed(2));

    if (typeof sgst!='undefined')
        var amountinc=parseFloat(grossamount) + parseFloat(gstamount) + parseFloat(gstamount);
    else if (typeof igst!='undefined')
        var amountinc=parseFloat(grossamount) + parseFloat(igstamount);
    else 
        var amountinc=parseFloat(grossamount);
    
    $('#amounttinclusive').html(amountinc.toFixed(2));

    var retention=$('#rretention').val();
    var retenamount=(grossamount * retention) / 100;

    $('#wbillretention').html(retenamount.toFixed(2));
    $('#wnettotal').val(amountinc - retenamount);

    var wotdeductions = $('#wotdeductions').val();
    wotdeductions = (wotdeductions) ? wotdeductions : 0;
    var totOthdedct = parseFloat(wotdeductions) + parseFloat(otdedct);
    if (totOthdedct!=''){
        $('#tot_other_deductions').val(totOthdedct);
        var nettot=parseFloat($('#wnettotal').val());
        var netamount=nettot - parseFloat(totOthdedct);
    }
    else {
        var netamount=amountinc - retenamount;
    }
    $('#wbillnetamount').html(netamount.toFixed(2));
    
    var amountpay=netamount;
    $('#wamountpayabe').html(amountpay.toFixed(2));
    $('#amountpayval').val(amountpay);
}

$(document).on("click", '.tab-wrapper .viewhistbillitems', function(){
  
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
                $("#billitemhist").show();
                $("#billitemhist").html(data.result);
                $('.history-bill-list').hide();
            }
            $('.preloader').hide(); 
        }
    });
});

$(document).on("click", '.billcomplete', function(){
    if(confirm("Are you Sure? Do you want to approve and close this order?")){
        if(approveBill()){
            var orderid = $('#ordid').val();
            $.ajax({
                type: 'POST',
                //url: '../projects/closeorderbills',
                url: '../procurement/closeorderbills',
                beforeSend : function(){
                    $('.preloader').show();
                },
                dataType: "json",
                data: {orderid:orderid},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('.billcancel').trigger('click');
                        $('#wrkbls').trigger('click');
                    }
                    $('.preloader').hide(); 
                }
            });
        }
    }
});



$(document).on("click", '.billapprove', function(){
    approveBill();
});

function approveBill(){

    var orderid = $('#ordid').val();
    var billid = $('#ordbillid').val();
    var error = 0;

    $('.resourceqntty').each(function(){
        currentQty = parseInt($(this).val());
        if(!currentQty || currentQty <= 0) 
            error = 1;
        else{
            resourceId  = $(this).attr('data-id');
            qtyuptolast = parseInt($('#qtyuptolastt'+resourceId).val());
            orderQty    = parseInt($('#orderQty'+resourceId).val());
            if(orderQty < (qtyuptolast + currentQty)){
                if(!confirm($('#resourceName'+resourceId).val()+" Bill quantity exceeded the Estimate quantity! Do you want to continue?"))
                    error = 2;
            }
        }
    });


    if(error == 1){
        alert('Quantity value should be greater than Zero!');
    }
    else if(error == 0){
        $.ajax({
            type: 'POST',
            //url: '../projects/approveorderbills',
            url: '../procurement/approveorderbills',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: $( "#billssview" ).serialize(),
            success: function(data){
                if(data.error=='No')
                {
                    location.reload();
                    $('#wrkbls').trigger('click');
                    //$('#histry').trigger('click');
                	//$('.history-bill-list').show();
                    return true;
                    location.reload();
                }
                $('.preloader').hide(); 
            }
        });
    }
    return false;
}


$(function() {
    $('#histry').click(function () {
        $.ajax({
            type: 'POST',
            url: '../projects/workorderbillshistory',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {},
            success: function(data){
                if(data.error=='No')
                {
                    $('#workorderbillitemshistory').html(data.result);
                    $('#workorderbillitemshistory').show();
                    $('.wrkbillviews').hide();
                    $('#wrkbillhead').hide();
                    $('.history-bill-list').show();
                    $('.work-orderbill-list-wrpr').hide();
                }
                $('.preloader').hide();
            }
        });
    });
});

$(document).on("click", '.close-bill-history-btn', function(){

	$('#workorderbillitemshistory').hide();
	$('.history-bill-list').hide();
    $('.work-orderbill-list-wrpr').show();
    $('#billitemhist').hide();
});
$(document).on("click",'.cancellbillview',function() {
   $('#billitemhist').hide();
   $('.history-bill-list').show();
});
