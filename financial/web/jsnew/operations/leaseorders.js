$(document).on( "click", "#viewleaseordersbill", function(){
    //$('#leaseorderbillsearch').trigger('click') ;
});
$(document).on( "click", "#lskbls", function(){ 
    $('#leaseorderbillsearch').trigger('click');
    $(".history-bill-list data-content-list").css("display", "none");
    $(".content-wrpr leasebillshwng").css("display", "none");
    $('.work-orderbill-list-wrpr').hide();
    $('.leasebillshwng').show();
    $('#lsbillhistbutton').show();
    $('#wrkbillhistbutton').hide();
    $('#lsbillhistbutton #leaseorderhistory').show();
});
$(function() {

     $('#leaseorderbillsearch').click(function () {
        $.ajax({
            type: 'POST',
            url: '../projects/leaseordersbill',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            success: function(data){
                if(data.error=='No')
                {
                   $('.bilhdd').hide();
                    $('#leaseorderbillitems').html(data.result);
                    $('#leaseorderbillitems').show();
                     $(".history-bill-list data-content-list").css("display", "none");
                     $(".content-wrpr leasebillshwng").css("display", "none");
                }
                $('.preloader').hide();
            }
        });
    });

     $(document).on("click", '.viewleasebillitems', function(){
        var orderid = $(this).attr('data-v');
            $.ajax({
            type: 'POST',
            url: '../projects/viewleaseorderbills',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {orderid:orderid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#leaseorderbillitems').hide();
                    $('#leashd').hide();
                    $("#leasebillitemsview").html(data.result);
                    $("#leasebillitemsview").show();
                    $('.leasbillviews').show();
                }
                $('.preloader').hide(); 
            }
        });
});


     $(document).on("click", '.leasbillcancel', function(){
         $('#leaseorderbillsearch').trigger('click') ;
        $('#leaseorderbillitems').show();
        $('#leashd').show();
        $("#leasebillitemsview").hide();
        $('.leasbillviews').hide();
     });

     $(document).on("click", '.lbillapprove', function(){


        var orderid = $('#lordbillid').val();
        var billid = $('#ordbillid').val();
            $.ajax({
            type: 'POST',
            url: '../projects/approveleaseorderbills',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: $( "#lbillssview" ).serialize(),
            success: function(data){
                if(data.error=='No')
                {
                    $('#leaseorderhistory').trigger('click');
                    $('#leaseorderbillitems').hide();
                    $('#leashd').hide();
                    $('.leasbillviews').hide();
                    $('.bilhis').show();
                    $('#leaseorderbillitemshistory').show();
                }
                $('.preloader').hide(); 
            }
        });
    });

    //$('#leaseorderhistory').click(function () {
        $(document).on('click','#leaseorderhistory',function(){

        
        $.ajax({
            type: 'POST',
            url: '../projects/leasorderbillshistory',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {},
            success: function(data){
                if(data.error=='No')
                {
                    $('.bilhdd').show();
                    $('#leaseorderbillitems').hide();
                    $('#leashd').hide();
                    $('#leaseorderbillitemshistory').html(data.result);
                    $('.leasbillviews').hide();
                    $('#leaseorderbillitemshistory').show();
                    $('.bilhis').show();
                    $('.lbillhis').show();
                    $('#lsbillhistbutton #leaseorderhistory').hide();
                }
                $('.preloader').hide();
            }
        });
    });

    $(document).on('change','.lresourceqntty',function(){

    var resid = $(this).attr('data-id');
    var cqty = $('#lresourceqntty'+resid).val();
    var uqty=$('#lqtyuptolastt'+resid).val();
    var resrate = $('#lrateres'+resid).html();
    var qtyamount = cqty * resrate;
    var camount=cqty * resrate;
    var uamount=$('#lamountuptolastt'+resid).val();
    var totalqty=parseFloat(cqty) + parseFloat(uqty);
    var totalamount= parseFloat(camount) + parseFloat(uamount);
    $('#lcurrentamountt'+resid).html(camount.toFixed(2));
    $('#lcamounttval'+resid).val(camount);
    
    $('#lresamnnt'+resid).val(totalamount.toFixed(2));
    $('#ltotalqtty'+resid).html(totalqty.toFixed(2));
    $('#lqtyamnt'+resid).html(totalamount.toFixed(2));

    var grossamount=0;
    $('.lcurrentamountt').each(function(){
        var gamnt = $(this).val();
        grossamount=grossamount+parseInt(gamnt);
    });


    var finaltotal=0;
    $('.lreourceamount').each(function(){
        var amnt = $(this).val();
        finaltotal=parseInt(finaltotal)+ parseInt(amnt);
    });

    $('#lbillstotal').html(finaltotal.toFixed(2));
    $('#lgrssamnt').val(finaltotal.toFixed(2));

    var sgst=$('#lssgst').val();
    var cgst=$('#lscgst').val();
    var igst=$('#lsigst').val();

    var grossamount = $('#lgrssamnt').val();


    var gstamount=(grossamount * sgst) / 100;
    var igstamount=(grossamount * igst) / 100;
    $('#lsgstsmountt').html(gstamount.toFixed(2));
    $('#lcgstamountt').html(gstamount.toFixed(2));
    $('#ligstamountt').html(igstamount.toFixed(2));


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


    $('#lamounttinclusive').html(amountinc.toFixed(2));


    var retention=$('#lrretention').val();
    var retenamount=(grossamount * retention) / 100;

    $('#lbillretention').html(retenamount.toFixed(2));
    $('#lnettotal').val(amountinc - retenamount);

    var otdedct=$('#lotdeductions').val();

    if (otdedct!=''){
        var nettot=parseFloat($('#lnettotal').val());
        var netamount=nettot - parseFloat(otdedct);
    }
    else {
        var netamount=amountinc - retenamount;
    }
    $('#lbillnetamount').html(netamount.toFixed(2));

    
    var amountpay=netamount;
    $('#lamountpayabe').html(amountpay.toFixed(2));
    //$('#amountpayval').val(amountpay);
    




    });
    $(document).on('click','.lbillhis',function(){


        $('#leaseorderbillitems').show();
        $('#leashd').show();
        $('#leaseorderbillitemshistory').hide();
        $('.bilhis').hide();
        $('#leaseorderbillsearch').trigger('click') ;
        $('#leaseorderhistory').show();
        $('.lbillhis').hide();
        $('#lsbillhistbutton #leaseorderhistory').show();
    });

     
});

