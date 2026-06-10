$(document).on('click','#fundreceiptqradio',function(){

    $('.panel-default').removeClass('active');

    $('.fundreceipt-tab').addClass('active');

    $('.account-heads-table-wrpr').hide();

    $.ajax({
        type:"post",
        dataType: "json",
        url:'../financerequests/fundreceipt',
        beforeSend : function(){
                $('.preloader').show();
            },

        data:{},
        success: function(data){
            if(data.error=='No'){
                $('#fund-header').html(data.result);
            }else{
                alert(data.errortext);
            }
             $('.preloader').hide();
        }
    }); 
    

});


$(document).on('click','.receiptacctheadid',function(){
        //e.preventDefault();alert('ff');
        //$('#Financeapproval-tab').trigger('click');
        var idCheck = $(this).attr("data-id");
        var iidCheck = $('#selectbankname_'+idCheck).val();
        var account_type=$(this).attr("data-value");
        $('.receiptacctheadid').removeClass("active prev-tab");
        $('#receiptacctheadid_'+idCheck).addClass('active prev-tab');

        if(idCheck){
            $.ajax({
                type:"post",
                dataType: "json",
                url:'../financerequests/receiptaddrows',
                data:{aheadid: $(this).attr("data-id"),aheadname: iidCheck,account_type:account_type},
                success: function(data){
                    if(data.error=='No'){
                        //$('#fin-tab-body').show();
                        //$('.add-fr-cntnr').slideDown( "slow" );
                        $('.account-heads-table-wrpr').show();
                        $('#receiptcashbankvouchadd').html(data.addbutton);
                        $('#displayaddreceiptrows').html(data.result);
                        $('#funrec-Approval-listing').html(data.finaprve);
                        $('.add-fr-cntnr').slideDown( "slow" );
                        if(account_type==1){
                            $('.cash-book-tab').css('pointer-events','');
                            $('.bank-book-tab').css('pointer-events','none');
                        }
                        else{
                            $('.cash-book-tab').css('pointer-events','none');
                            $('.bank-book-tab').css('pointer-events','');
                        }
                    }else{
                        alert(data.errortext);
                    }
                }
            }); 
        }   
    });

$(document).on('click','#selectbanks',function(){
    //e.preventDefault();alert('ff');
        //$('#Financeapproval-tab').trigger('click');
        var idCheck = $(this).attr("data-id");
        var iidCheck = $('#selectbankname_'+idCheck).val();
        var account_type=$(this).attr("data-value");
        $('.receiptacctheadid').removeClass("active prev-tab");
        $('#receiptacctheadid_'+idCheck).addClass('active prev-tab');

        if(idCheck){
            $.ajax({
                type:"post",
                dataType: "json",
                url:'../financerequests/receiptaddrows',
                data:{aheadid: $(this).attr("data-id"),aheadname: iidCheck,account_type:account_type},
                success: function(data){
                    if(data.error=='No'){
                        //$('#fin-tab-body').show();
                        //$('.add-fr-cntnr').slideDown( "slow" );
                        $('.account-heads-table-wrpr').show();
                        $('#receiptcashbankvouchadd').html(data.addbutton);
                        $('#displayaddreceiptrows').html(data.result);
                        $('#funrec-Approval-listing').html(data.finaprve);
                        $('.add-fr-cntnr').slideDown( "slow" );
                        if(account_type==1){
                            $('.cash-book-tab').css('pointer-events','');
                            $('.bank-book-tab').css('pointer-events','none');
                        }
                        else{
                            $('.cash-book-tab').css('pointer-events','none');
                            $('.bank-book-tab').css('pointer-events','');
                        }
                    }else{
                        alert(data.errortext);
                    }
                }
            }); 
        }   
});
$(document).on('click','#addcashbankreceipt',function(){
    $('.addcashbankreceipt').hide();
    $('#displayaddreceiptrows').show();
    $('#funrec-Approval-listing').hide();

});

$(document).on('click','.Cancelreceipt',function(){

    $("form").submit(function(e){
        e.preventDefault();
    });
    $('.addcashbankreceipt').show();
    $('#displayaddreceiptrows').hide();
    $('#funrec-Approval-listing').show();

});


$(document).on('click','.Saveasreceipts',function(e){
        e.preventDefault();
        var idSbutton = $(this).attr("data-id");
        var error=0;
        $('.error').hide();
        $('.datepicker').each(function(){
            var idCheck = $(this).attr("data-id");
            if($(this).val()=='')
            {
                $("#datepicker"+idCheck).next("span").html('Select Date').show('slow');
                error=1;
            }
        });

        $('.voucher_no').each(function(){
            var idCheck = $(this).attr("data-id");
            if($(this).val()=='')
            {
                $("#voucher_no"+idCheck).next("span").html('Voucher No').show('slow');
                error=1;
            }
        });

        $('.receiptpurpose').each(function(){
            var idCheck = $(this).attr("data-id");
            if($(this).val()=='')
            {
                $("#receiptpurpose"+idCheck).next("span").html('Enter Purpose').show('slow');
                error=1;
            }
        });

        $('.receiptcredit_account').each(function(){
            var idCheck = $(this).attr("data-id");
            if($(this).val()=='none')
            {
                $("#receiptcredit_account"+idCheck).next("span").html('Select Accounthead').show('slow');
                error=1;
            }
        });

        $('.receiptamount').each(function(){
            var idCheck = $(this).attr("data-id");
            if($(this).val()=='')
            {
                $("#receiptamount"+idCheck).next("span").html('Enter Amount').show('slow');
                error=1;
            }
           /* if($(this).val()>10000){
                $("#receiptamount"+idCheck).next("span").html('Enter up to 10000 only').show('slow');
                error=1;
            }*/
        });
        $('.vouchersproject').each(function(){
            var idCheck = $(this).attr("data-id");

            if($(this).val() == 0)
            {
                
                $("#vouchersproject"+idCheck).next("span").html('Select Project').show('slow');
                error=1;
            }

        });

        if (error == 0)
        {  
            $.ajax({
                type: 'POST',
                url: '../financerequests/savefundreceipt',
                beforeSend : function(){
                    $('#Saveasreceipts'+idSbutton).attr("disabled", true);
                },
                data:$('#fundreceiptform').serialize(),
                dataType:"json",
                success: function(data){
                    if(data.result=='Yes'){
                        $('#Saveasreceipts'+idSbutton).attr("disabled", false);
                        $('#Cancelreceipt').trigger('click');
                        $('#receiptacctheadid_'+idSbutton).trigger('click');

                       
                    }
                }
            });
        }
    
    });



var x = 1;

    $(document).on('click','.add_receipt_button',function(e){
        e.preventDefault();
        var idCheck = $(this).attr("data-id");
        //var rowno = $(this).attr("data-no");
        var iidCheck = $('#selectbankname_'+idCheck).val();
        x++;
        $.ajax({
            type: 'POST',
            url: '../financerequests/cashbankreceiptrow',
            /*beforeSend : function(){
                $('.preloader').show();
            },*/
            dataType: "json",
            data: {id:idCheck,rowno:x,aheadname:iidCheck},
            success: function(data){
                if(data.error=='No')
                {
                    $('#fundrecsaverow123').before(data.result);
                    //$('#cashbooktable').show();
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });
    });


$(document).on('click','.editfinrec',function(){
    var id = $(this).attr("data-id");

    $('#editfinrec'+id).hide();
    $('#finrecdatespan'+id).hide();
    $('#finrecvrnospan'+id).hide();
    $('#finrecpurpspan'+id).hide();
    $('#finrecacctheadspan'+id).hide();
    $('#finrecamntpan'+id).hide();

    $('#savefinrec'+id).show();
    $('#editfinrecdate-'+id).show();
    $('#editfinrecvrno-'+id).show();
    $('#editfinrecpurp-'+id).show();
    $('#finacctheads-'+id).show();
    $('#editfinrecamnt-'+id).show();

});


$(document).on('click','.savefinrec',function(){

    var id = $(this).attr("data-id");

    var findate = $('#editfinrecdate-'+id).val();
    var finvrno = $('#editfinrecvrno-'+id).val();
    var finpurp = $('#editfinrecpurp-'+id).val();
    var finaccthead = $('#finacctheads-'+id).val();
    

    var str = $('#editfinrecamnt-'+id).val();
    var finamnt = str.replace(',','');
    var amnt = parseInt(finamnt)

    $.ajax({
        type:"post",
        dataType: "json",
        url:'../financerequests/updatefundreceipt',
        data:{id: id,findate:findate,finvrno:finvrno,finpurp:finpurp,finaccthead:finaccthead,finamnt:finamnt},
        success: function(data){
            if(data.error=='No'){

                $('#savefinrec'+id).hide();
                $('#editfinrecdate-'+id).hide();
                $('#editfinrecvrno-'+id).hide();
                $('#editfinrecpurp-'+id).hide();
                $('#finacctheads-'+id).hide();
                $('#editfinrecamnt-'+id).hide();

                $('#editfinrec'+id).show();
                $('#finrecdatespan'+id).show();
                $('#finrecvrnospan'+id).show();
                $('#finrecpurpspan'+id).show();
                $('#finrecacctheadspan'+id).show();
                $('#finrecamntpan'+id).show();
                $('#finrecdatespan'+id).html(data.date);
                $('#finrecvrnospan'+id).html(finvrno);
                $('#editfinpurp-'+id).html(finpurp);
                $('#finrecpurpspan'+id).html(finpurp);
                $('#finrecacctheadspan'+id).html(data.accheadname);
                $('#finrecamntpan'+id).html(amnt.toFixed(2));
                
            }else{
                alert(data.errortext);
            }
        }
    });


});

$(document).on('click','.RejectMyfundrec',function(e){
    e.preventDefault();
    var finreqID = $(this).attr("data-id");
    var bankID = $('#RejectMyfundrec-'+finreqID).attr("data-id");
    var notival = $('#selectbankapp-'+bankID+' #noti-bank').text();
    var idss = $('#ApprovefundrecBank-'+finreqID).attr("data-id");
    var r = confirm("Are you sure you want to reject this request ?");
    if(finreqID != '' && r == true){

        $.ajax({
            type:"post",
            dataType: "json",
            url:'../financerequests/deletereceipt',
            data:{finreqID: finreqID,status: 2},
            success: function(data){
                if(data.error=='No'){
                    $('#funrecaprverow-'+finreqID).remove();
                    $('#selectbankapp-'+bankID+' #noti-bank').text(parseInt(notival)-1);
                    $('#RejectMyfundrec-'+finreqID).removeClass("innactive");
                    $('#ApproveMyfundrec-'+finreqID).addClass("innactive");
                    $('#receiptacctheadid_'+idss).trigger('click');

                    //alert("Rejected");
                }else{

                }
            }
        });

    }    
});


$(document).on('click','.PauseMyfundrec',function(e){
    e.preventDefault();
    var finrecID = $(this).attr("data-id");
    var bankID = $('#RejectMyfundrec-'+finrecID).attr("data-id");
    var notival = $('#selectbankapp-'+bankID+' #noti-bank').text();
    //var r = confirm("Are you sure you want to hold this request ?");
    if(finrecID != ''){

        var status = $(this).attr("data-status");

        if(status==1){
            var holdstatus = null;
        }
        else{
            var holdstatus = 1;
        }

        $.ajax({
            type:"post",
            dataType: "json",
            url:'../financerequests/holdmyfundreceipt',
            data:{finrecID: finrecID,status: holdstatus},
            success: function(data){
                if(data.error=='No'){
                    //$('#fin-Approval-body').show();
                    //$('#fin-Approval-content').html(data.resultsearch);
                    //alert("Not -----denied");
                }else if(data.error=='Yes'){
                    $('#selectbankapp-'+bankID+' #noti-bank').text(parseInt(notival)-1);
                    if(holdstatus==1){
                        $('#PauseMyfundrec-'+finrecID).removeClass("innactive");
                        $('#PauseMyfundrec-'+finrecID).addClass("active");
                        $('#editfinrec'+finrecID).attr("disabled", true);
                        $('#ApproveMyfundrec-'+finrecID).attr("disabled", true);
                        $('#RejectMyfundrec-'+finrecID).attr("disabled", true);
                        $('#PauseMyfundrec-'+finrecID).attr("data-status", holdstatus);
                    }
                    else{
                        $('#PauseMyfundrec-'+finrecID).removeClass("active");
                        $('#PauseMyfundrec-'+finrecID).addClass("innactive");
                        $('#editfinrec'+finrecID).attr("disabled", false);
                        $('#ApproveMyfundrec-'+finrecID).attr("disabled", false);
                        $('#RejectMyfundrec-'+finrecID).attr("disabled", false);
                        $('#PauseMyfundrec-'+finrecID).attr("data-status", holdstatus);
                    }
                    
                    //alert("Rejected");
                }
            }
        });

    }    
});


$(document).on('click','.ApproveMyfundrec',function(e){
    e.preventDefault();
    var finrecID = $(this).attr("data-id");
    var bankID = $('#ApprovefundrecBank-'+finrecID).attr("data-id");
    var notival = $('#selectbankapp-'+bankID+' #noti-bank').text();

    if(finrecID != ''){

        var str=$('#finrecamntpan'+finrecID).html();

        var finamount = str.replace(',','');
        var totamount = $('#recTotalAmount_'+bankID).html();

        if ($('#ApproveMyfundrec-'+finrecID).hasClass("active")) 
        {
            var newtotal = parseFloat(totamount) - parseFloat(finamount);
            $('#recTotalAmount_'+bankID).html(newtotal.toFixed(2));
            $('#recrealtotalamt_'+bankID).val(newtotal.toFixed(2));
            //$('#fndselectedreqsttotl-'+bankID).html(newtotal.toFixed(2));

            $('#ApproveMyfundrec-'+finrecID).removeClass("active");
            $('#ApproveMyfundrec-'+finrecID).addClass("innactive");

            $('#apprvestatusrec-'+finrecID).val("0");
        }
        else{
            var newtotal = parseFloat(finamount)+parseFloat(totamount);
            $('#recTotalAmount_'+bankID).html(newtotal.toFixed(2));
            $('#recrealtotalamt_'+bankID).val(newtotal.toFixed(2));
           // $('#fndselectedreqsttotl-'+bankID).html(newtotal.toFixed(2));

            $('#ApproveMyfundrec-'+finrecID).removeClass("innactive");
            $('#ApproveMyfundrec-'+finrecID).addClass("active");

            $('#apprvestatusrec-'+finrecID).val("1");
        }

        var openblnce = $('#bankrealopenbalance_'+bankID).val();

        var apprvdamt = $('#recrealtotalamt_'+bankID).val();

        var closeblnce1 = parseFloat(openblnce)+ parseFloat(apprvdamt);

        var closeblnce = Math.abs(closeblnce1);

        $('#recClosingBalance_'+bankID).html(closeblnce.toFixed(2));

    }

    
});

$(document).on('click','.apprveselectedrec',function(e){
    e.preventDefault();

   
        $.ajax({
            type:"post",
            dataType: "json",
            url:'../financerequests/approvemyfundreceipt',
            data:$('#fundreceiptacceptform').serialize(),
            success: function(data){
                if(data.error=='No'){

                    var bankID = data.bankid;

                    $('#receiptacctheadid_'+bankID).trigger('click');
                }
            }
        });

 

});
 /*$(document).on("click",'.receiptacctheadid',function(){
    var accountid=$(this).attr("data-id");
    var account_type=$(this).attr("data-value");
    $.ajax({
        type: 'POST',
        url: '../financerequests/usercashbankreceiptselect',
        dataType:"json",
        data: {accountid:accountid,account_type:account_type},
        success: function(data){
            if(data.error=='No')
            {

                //$('#selecteduseraccount').html(data.result);

            }
        }
    });
});*/