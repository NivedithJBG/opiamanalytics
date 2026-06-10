$(document).on( "click", "#audit-tab", function(){ 

$('.vouchers-tab').removeClass('generateVoucherFormActive'); 

    $('#cash-voucher').trigger('click');
 
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
       $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
       //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
   }
   if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
       $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
       $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
       
   } 
   
   $('#cash-voucher').trigger('click');
});

$(document).on('click','.commentsave',function(){

    var voucherID = $(this).attr("data-id");

    var comment = $('#auditcmnt-'+voucherID).val();

    $.ajax({
        type:"post",
        dataType: "json",
        url:'../voucher/auditcomment',
        data:{voucherID: voucherID,comment: comment},
        success: function(data){
            if(data.error=='No'){
                $('#cash-voucher').trigger('click');
            }
        }
    });

});
$(document).on('click','.commentapprove',function(){

    var voucherID = $(this).attr("data-id");

    //var comment = $('#commentapprove-'+voucherID).val();
    var comment = $('#auditcmnt-'+voucherID).val();

     $.ajax({
        type:"post",
        dataType: "json",
        url:'../voucher/auditapprove',
        data:{voucherID: voucherID,comment: comment},
        success: function(data){
            if(data.error=='No'){
                $('#cash-voucher').trigger('click');
            }
        }
    });

    });




$(document).on('click','.commentbanksave',function(){

    var voucherID = $(this).attr("data-id");

    var comment = $('#auditcmnt-'+voucherID).val();

    $.ajax({
        type:"post",
        dataType: "json",
        url:'../voucher/auditcomment',
        data:{voucherID: voucherID,comment: comment},
        success: function(data){
            if(data.error=='No'){
                $('#bank-voucher').trigger('click');
            }
        }
    });

});

$(document).on('click','.commentbankapprove',function(){

    var voucherID = $(this).attr("data-id");

    //var comment = $('#commentbankapprove-'+voucherID).val();
    var comment = $('#auditcmnt-'+voucherID).val();

     $.ajax({
        type:"post",
        dataType: "json",
        url:'../voucher/auditapprove',
        data:{voucherID: voucherID,comment: comment},
        success: function(data){
            if(data.error=='No'){
                $('#bank-voucher').trigger('click');
            }
        }
    });

    });


$(document).on('click','.commentjournalsave',function(){

    var voucherID = $(this).attr("data-id");

    var comment = $('#auditjournalcmnt-'+voucherID).val();

    $.ajax({
        type:"post",
        dataType: "json",
        url:'../voucher/auditjournalcomment',
        data:{voucherID: voucherID,comment: comment},
        success: function(data){
            if(data.error=='No'){
                $('#journal-voucher').trigger('click');
            }
        }
    });

});

$(document).on('click','.commentjournalapprove',function(){

    var voucherID = $(this).attr("data-id");

    //var comment = $('#commentjournalapprove-'+voucherID).val();
    var comment = $('#auditjournalcmnt-'+voucherID).val();

     $.ajax({
        type:"post",
        dataType: "json",
        url:'../voucher/auditjournalapprove',
        data:{voucherID: voucherID,comment: comment},
        success: function(data){
            if(data.error=='No'){
                $('#journal-voucher').trigger('click');
            }
        }
    });

    });

$(document).on('click','.auditvouchersearch',function(){

    var vouchtype = $('#vouchertype').val();

    var fromdate = $('#voucherfromdate').val();

    var todate = $('#vouchertodate').val();

    var error=0;
    $('.error').hide();

    if(vouchtype=='')
    {
        $("#vouchertype").next("span").html('Select Type').fadeIn().delay(1000).fadeOut();
        error=1;
    }

    if (error == 0)
    {  

        $.ajax({
            type: 'POST',
            url: '../voucher/auditvouchersearch',
            beforeSend : function(){
                $('#tabaudithistory').show();
            },
            dataType: "json",
            data:{vouchtype:vouchtype,fromdate:fromdate,todate:todate},
            success: function(data){
                if(data.error=='No')
                {
                    $('#audit-historytable').html(data.result);
                    $('#audit-historytable').show();
                    // $('#cash-vouchertable').hide();
                     
                    
                }
                else
                {
                    alert(data.errortext);
                }
                $('#tabaudithistory').hide();
            }
        });

    }

});

$(function(){
    $('#cash-voucher').click(function(){  

      $.ajax({
            type: 'POST',
            url: '../voucher/auditcashsearch',
            beforeSend : function(){
                $('#tabcashvoucher').show();
            },
            dataType: "json",
            success: function(data){
                if(data.error=='No')
                {
                    $('#bank-vouchertable').hide();
                    $('#journal-vouchertable').hide();
                    $('#audit-historytable').hide();
                    $('.bank-voucher').removeClass("in active");
                    $('.journal-voucher').removeClass("in active");
                    $('.audit-history').removeClass("in active");
                    $('.cash-voucher').addClass("in active");
                    $('#cash-vouchertable').html(data.result);
                    $('#cash-vouchertable').show();
                }
                else
                {
                    alert(data.errortext);
                }
                $('#tabcashvoucher').hide();
            }
        });




    });


    $('#bank-voucher').click(function(){

        $.ajax({
            type: 'POST',
            url: '../voucher/auditbanksearch',
            beforeSend : function(){
                $('#tabbankvoucher').show();
            },
            dataType: "json",
            success: function(data){
                if(data.error=='No')
                {
                    $('#cash-vouchertable').hide();
                    $('#journal-vouchertable').hide();
                    $('#audit-historytable').hide();
                    $('.cash-voucher').removeClass("in active");
                    $('.journal-voucher').removeClass("in active");
                    $('.audit-history').removeClass("in active");
                    $('.bank-voucher').addClass("in active");
                    $('#bank-vouchertable').html(data.result);
                    $('#bank-vouchertable').show();
                    // $('#cash-vouchertable').hide();
                     
                    
                }
                else
                {
                    alert(data.errortext);
                }
                $('#tabbankvoucher').hide();
            }
        });

    });

    $('#journal-voucher').click(function(){

        $.ajax({
            type: 'POST',
            url: '../voucher/auditjounalsearch',
            beforeSend : function(){
                $('#tabjournalvoucher').show();
            },
            dataType: "json",
            success: function(data){
                if(data.error=='No')
                {
                    $('#cash-vouchertable').hide();
                    $('#bank-vouchertable').hide();
                    $('#audit-historytable').hide();
                    $('.cash-voucher').removeClass("in active");
                    $('.bank-voucher').removeClass("in active");
                    $('.audit-history').removeClass("in active");
                    $('.journal-voucher').addClass("in active");
                    $('#journal-vouchertable').html(data.result);
                    $('#journal-vouchertable').show();
                    // $('#cash-vouchertable').hide();
                     
                    
                }
                else
                {
                    alert(data.errortext);
                }
                $('#tabjournalvoucher').hide();
            }
        });

    });

    $('#audit-history').click(function(){

        $('#cash-vouchertable').hide();
        $('#bank-vouchertable').hide();
        $('#journal-vouchertable').hide();
        $('.cash-voucher').removeClass("in active");
        $('.bank-voucher').removeClass("in active");
        $('.journal-voucher').removeClass("in active");
        $('.audit-history').addClass("in active");

        /*$.ajax({
            type: 'POST',
            url: '../voucher/auditjounalsearch',
            beforeSend : function(){
                $('#tabjournalvoucher').show();
            },
            dataType: "json",
            success: function(data){
                if(data.error=='No')
                {
                    $('#cash-vouchertable').hide();
                    $('#bank-vouchertable').hide();
                    $('.cash-voucher').removeClass("in active");
                    $('.bank-voucher').removeClass("in active");
                    $('.journal-voucher').addClass("in active");
                    $('#journal-vouchertable').html(data.result);
                    $('#journal-vouchertable').show();
                    // $('#cash-vouchertable').hide();
                     
                    
                }
                else
                {
                    alert(data.errortext);
                }
                $('#tabjournalvoucher').hide();
            }
        });*/

    });


});