$(document).on( "click", "#voucher", function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#vouchersection').hide();
    $('#cashvoucher').trigger('click');
});

$(function(){

    /*$('#cashvouchersearch').click(function(){
        $('#cashvoucher').trigger('click');
    });*/
    $('#cashreceipt').click(function(){
        $('#cashreceiptsection').slideDown('slow');// slide down the project listing div
        //$('#bankvouchersection').slideUp('slow');// slide down the project listing div
        $('#cashvouchersection').hide();
        $('#bankvouchersection').hide();
        $('#bankreceiptsection').hide();
        $('#billssection').hide();
        $('#journalsection').hide();
        $('#contrasection').hide();
        $('#cashreceipt').removeClass('btn-danger').addClass('btn-success');
        $('#cashvoucher').removeClass('btn-success').addClass('btn-danger');
        $('#bankvoucher').removeClass('btn-success').addClass('btn-danger');
        $('#bankreceipt').removeClass('btn-success').addClass('btn-danger');
        $('#bill').removeClass('btn-success').addClass('btn-danger');
        $('#journal').removeClass('btn-success').addClass('btn-danger');
        $('#contra').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../Voucher/cashreceipt',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            //data: {name:$('#searchcashvoucher').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#cashreceiptitems').html(data.result);
                    $('#cashreceipttable').show();
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });
    });

    $('#bankvoucher').click(function(){
        $('#bankvouchersection').slideDown('slow');
        //$('#cashvouchersection').slideUp('slow');// slide down the project listing div
        $('#cashvouchersection').hide();
        $('#cashreceiptsection').hide();
        $('#bankreceiptsection').hide();
        $('#billssection').hide();
        $('#journalsection').hide();
        $('#contrasection').hide();
        $('#bankvoucher').removeClass('btn-danger').addClass('btn-success');
        $('#cashvoucher').removeClass('btn-success').addClass('btn-danger');
        $('#cashreceipt').removeClass('btn-success').addClass('btn-danger');
        $('#bankreceipt').removeClass('btn-success').addClass('btn-danger');
        $('#bill').removeClass('btn-success').addClass('btn-danger');
        $('#journal').removeClass('btn-success').addClass('btn-danger');
        $('#contra').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../Voucher/banksearch',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {name:$('#searchbankvoucher').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#bankvoucheritems').html(data.result);
                    $('#bankvouchertable').show();
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });
    });
    /*$('#bankvouchersearch').click(function(){
        $('#bankvoucher').trigger('click');
    });*/
    $('#bankreceipt').click(function(){
        $('#bankreceiptsection').slideDown('slow');
        //$('#cashvouchersection').slideUp('slow');// slide down the project listing div
        $('#cashvouchersection').hide();
        $('#cashreceiptsection').hide();
        $('#bankvouchersection').hide();
        $('#billssection').hide();
        $('#journalsection').hide();
        $('#contrasection').hide();
        $('#bankreceipt').removeClass('btn-danger').addClass('btn-success');
        $('#cashvoucher').removeClass('btn-success').addClass('btn-danger');
        $('#cashreceipt').removeClass('btn-success').addClass('btn-danger');
        $('#bankvoucher').removeClass('btn-success').addClass('btn-danger');
        $('#bill').removeClass('btn-success').addClass('btn-danger');
        $('#journal').removeClass('btn-success').addClass('btn-danger');
        $('#contra').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../Voucher/Bankreceipt',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            //data: {name:$('#searchbankvoucher').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#bankreceiptitems').html(data.result);
                    $('#bankreceipttable').show();
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });
    });
    $('#bill').click(function(){
        $('#billssection').slideDown('slow');
        //$('#cashvouchersection').slideUp('slow');// slide down the project listing div
        $('#cashvouchersection').hide();
        $('#cashreceiptsection').hide();
        $('#bankvouchersection').hide();
        $('#bankreceiptsection').hide();
        $('#journalsection').hide();
        $('#contrasection').hide();
        $('#bill').removeClass('btn-danger').addClass('btn-success');
        $('#cashvoucher').removeClass('btn-success').addClass('btn-danger');
        $('#cashreceipt').removeClass('btn-success').addClass('btn-danger');
        $('#bankvoucher').removeClass('btn-success').addClass('btn-danger');
        $('#bankreceipt').removeClass('btn-success').addClass('btn-danger');
        $('#journal').removeClass('btn-success').addClass('btn-danger');
        $('#contra').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../Voucher/Bills',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            //data: {name:$('#searchbankvoucher').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#billsitems').html(data.result);
                    $('#billtable').show();
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });
    });
    $('#journal').click(function(){
        $('#journalsection').slideDown('slow');
        //$('#cashvouchersection').slideUp('slow');// slide down the project listing div
        $('#cashvouchersection').hide();
        $('#cashreceiptsection').hide();
        $('#bankvouchersection').hide();
        $('#bankreceiptsection').hide();
        $('#billssection').hide();
        $('#contrasection').hide();
        $('#journal').removeClass('btn-danger').addClass('btn-success');
        $('#cashvoucher').removeClass('btn-success').addClass('btn-danger');
        $('#cashreceipt').removeClass('btn-success').addClass('btn-danger');
        $('#bankvoucher').removeClass('btn-success').addClass('btn-danger');
        $('#bankreceipt').removeClass('btn-success').addClass('btn-danger');
        $('#bill').removeClass('btn-success').addClass('btn-danger');
        $('#contra').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../Voucher/Journals',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            //data: {name:$('#searchbankvoucher').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#journalitems').html(data.result);
                    $('#journaltable').show();
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });
    });
    $('#contra').click(function(){
        $('#contrasection').slideDown('slow');
        //$('#cashvouchersection').slideUp('slow');// slide down the project listing div
        $('#cashvouchersection').hide();
        $('#cashreceiptsection').hide();
        $('#bankvouchersection').hide();
        $('#bankreceiptsection').hide();
        $('#billssection').hide();
        $('#journalsection').hide();
        $('#contra').removeClass('btn-danger').addClass('btn-success');
        $('#cashvoucher').removeClass('btn-success').addClass('btn-danger');
        $('#cashreceipt').removeClass('btn-success').addClass('btn-danger');
        $('#bankvoucher').removeClass('btn-success').addClass('btn-danger');
        $('#bankreceipt').removeClass('btn-success').addClass('btn-danger');
        $('#bills').removeClass('btn-success').addClass('btn-danger');
        $('#journal').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../Voucher/Contra',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            //data: {name:$('#searchbankvoucher').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#contraitems').html(data.result);
                    $('#contratable').show();
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });
    });
    $(document).on('click','.editbillbutton',function(){
        var idval=$(this).val();
        $('#billpurpose'+idval).hide();
        $('#billamount'+idval).hide();
        $('#editbillbutton'+idval).hide();
        $('#editbillpurpose'+idval).show();
        $('#editbillamount'+idval).show();
        //$('#editworkgroupname'+idval).focus();
        $('#savebillbutton'+idval).show();
    });
    $(document).on('click','.savebillbutton',function(){
        var idval=$(this).val();
        var error=0;
        $('.error').hide();
        if($('#editbillpurpose'+idval).val()=='')
        {
            $('#editbillpurpose'+idval).next("span").html('Enter Purpose').show('slow');
            error=1;
        }
        if($('#editbillamount'+idval).val()=='')
        {
            $('#editbillamount'+idval).next("span").html('Enter Amount').show('slow');
            error=1;
        }
        if(error==0)
        {
            $.ajax({
                type: 'POST',
                url: '../Voucher/update',
                beforeSend : function(){
                    $('#savebillbutton'+idval).attr("disabled", true);
                },
                dataType: "json",
                data: {billid:idval,purpose:$('#editbillpurpose'+idval).val(),amount:$('#editbillamount'+idval).val()},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#billamount'+data.Id).show();
                        $('#billpurpose'+data.Id).show();
                        $('#editbillbutton'+data.Id).show();
                        $('#editbillpurpose'+data.Id).hide();
                        $('#editbillamount'+data.Id).hide();
                        $('#savebillbutton'+data.Id).hide();
                        $('#editbillpurpose'+data.Id).val(data.Purpose);
                        $('#billpurpose'+data.Id).text(data.Purpose);
                        $('#editbillamount'+data.Id).val(data.Amount);
                        $('#billamount'+data.Id).text(data.Amount);

                    }
                    else
                    {
                        alert(data.errortext);
                    }

                    $('#savebillbutton'+data.Id).attr("disabled", false);
                }
            });
        }
    });
    $(document).on('click','.editpayment',function(){
        var idval=$(this).val();
        $('#purpose'+idval).hide();
        $('#amount'+idval).hide();
        $('#editpayment'+idval).hide();
        $('#editpurpose'+idval).show();
        $('#editamount'+idval).show();
        $('#savepayment'+idval).show();
    });
    $(document).on('click','.savepayment',function(){
        var idval=$(this).val();
        var error=0;
        $('.error').hide();
        var actualamount=$('#amount'+idval).hide();
        var amount=$('#editamount'+idval).val();
        
        if(amount>actualamount)
        {
          $('#amounterrror'+idval).show();
          error=1  
        }   
        if(error==0)
        {
            $.ajax({
                type: 'POST',
                url: '../Voucher/paymentupdate',
                beforeSend : function(){
                    $('#savepayment'+idval).attr("disabled", true);
                },
                dataType: "json",
                data: {id:idval,purpose:$('#editpurpose'+idval).val(),amount:$('#editamount'+idval).val()},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#amount'+data.Id).show();
                        $('#purpose'+data.Id).show();
                        $('#editpayment'+data.Id).show();
                        $('#editpurpose'+data.Id).hide();
                        $('#editamount'+data.Id).hide();
                        $('#savepayment'+data.Id).hide();
                        $('#editpurpose'+data.Id).val(data.Purpose);
                        $('#purpose'+data.Id).text(data.Purpose);
                        $('#editamount'+data.Id).val(data.Amount);
                        $('#amount'+data.Id).text(data.Amount);

                    }
                    else
                    {
                        alert(data.errortext);
                    }

                    $('#savepayment'+data.Id).attr("disabled", false);
                }
            });
        }
    });
    $(document).on('click','.editreceipt',function(){
        var idval=$(this).val();
        $('#receiptpurpose'+idval).hide();
        $('#receiptamount'+idval).hide();
        $('#editreceipt'+idval).hide();
        $('#editreceiptpurpose'+idval).show();
        $('#editreceiptamount'+idval).show();
        $('#savereceipt'+idval).show();
    });
    $(document).on('click','.savereceipt',function(){
        var idval=$(this).val();
        var error=0;

        if(error==0)
        {
            $.ajax({
                type: 'POST',
                url: '../Voucher/receiptupdate',
                beforeSend : function(){
                    $('#savereceipt'+idval).attr("disabled", true);
                },
                dataType: "json",
                data: {id:idval,purpose:$('#editreceiptpurpose'+idval).val(),amount:$('#editreceiptamount'+idval).val()},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#receiptamount'+data.Id).show();
                        $('#receiptpurpose'+data.Id).show();
                        $('#editreceipt'+data.Id).show();
                        $('#editreceiptpurpose'+data.Id).hide();
                        $('#editreceiptamount'+data.Id).hide();
                        $('#savereceipt'+data.Id).hide();
                        $('#editreceiptpurpose'+data.Id).val(data.Purpose);
                        $('#receiptpurpose'+data.Id).text(data.Purpose);
                        $('#editreceiptamount'+data.Id).val(data.Amount);
                        $('#receiptamount'+data.Id).text(data.Amount);

                    }
                    else
                    {
                        alert(data.errortext);
                    }

                    $('#savereceipt'+data.Id).attr("disabled", false);
                }
            });
        }
    });
    $(document).on('click','.deletepayvoucher',function(){
        var voucherid=$(this).val();
        var r = confirm("Are you sure you want to delete this Voucher ?");
        if (r == true) {
            $.ajax({
                type: 'POST',
                url: '../Voucher/deletepayment',
                beforeSend : function(){
                    $('#deletepayvoucher'+voucherid).attr("disabled", true);
                },
                dataType: "json",
                data: {voucherid:voucherid},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#voucherrow'+data.Id).remove();
                        $('#cashvoucher').trigger('click');
                    }
                    else
                    {
                        alert(data.errortext);
                    }

                    $('#deletepayvoucher'+data.Id).attr("disabled", false);
                }
            });
        }
    });
    $(document).on('click','.deletebankpayvoucher',function(){
        var voucherid=$(this).val();
        var r = confirm("Are you sure you want to delete this Voucher ?");
        if (r == true) {
            $.ajax({
                type: 'POST',
                url: '../Voucher/deletepayment',
                beforeSend : function(){
                    $('#deletebankpayvoucher'+voucherid).attr("disabled", true);
                },
                dataType: "json",
                data: {voucherid:voucherid},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#voucherrow'+data.Id).remove();
                        $('#bankvoucher').trigger('click');
                    }
                    else
                    {
                        alert(data.errortext);
                    }

                    $('#deletebankpayvoucher'+data.Id).attr("disabled", false);
                }
            });
        }
    });
    $(document).on('click','.deletereceiptvoucher',function(){
        var voucherid=$(this).val();
        var r = confirm("Are you sure you want to delete this Voucher ?");
        if (r == true) {
            $.ajax({
                type: 'POST',
                url: '../Voucher/deletereceipt',
                beforeSend : function(){
                    $('#deletereceiptvoucher'+voucherid).attr("disabled", true);
                },
                dataType: "json",
                data: {voucherid:voucherid},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#receiptrow'+data.Id).remove();
                        $('#cashreceipt').trigger('click');
                    }
                    else
                    {
                        alert(data.errortext);
                    }

                    $('#deletereceiptvoucher'+data.Id).attr("disabled", false);
                }
            });
        }
    });
    $(document).on('click','.deletebankreceiptvoucher',function(){
        var voucherid=$(this).val();
        var r = confirm("Are you sure you want to delete this Voucher ?");
        if (r == true) {
            $.ajax({
                type: 'POST',
                url: '../Voucher/deletereceipt',
                beforeSend : function(){
                    $('#deletebankreceiptvoucher'+voucherid).attr("disabled", true);
                },
                dataType: "json",
                data: {voucherid:voucherid},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#receiptrow'+data.Id).remove();
                        $('#bankreceipt').trigger('click');
                    }
                    else
                    {
                        alert(data.errortext);
                    }

                    $('#deletebankreceiptvoucher'+data.Id).attr("disabled", false);
                }
            });
        }
    });
    $(document).on('click','.deletebillbutton',function(){
        var billid=$(this).val();
        var r = confirm("Are you sure you want to delete this Journal Voucher ?");
        if (r == true) {
            $.ajax({
                type: 'POST',
                url: '../Voucher/deletebill',
                beforeSend : function(){
                    $('#deletebillbutton'+billid).attr("disabled", true);
                },
                dataType: "json",
                data: {billid:billid},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#billrow'+data.Id).remove();
                        $('#bill').trigger('click');
                    }
                    else
                    {
                        alert(data.errortext);
                    }

                    $('#deletebillbutton'+data.Id).attr("disabled", false);
                }
            });
        }
    });
    $(document).on('click','.deletejournalsbutton',function(){
        var journalid=$(this).val();
        var r = confirm("Are you sure you want to delete this Journal ?");
        if (r == true) {
            $.ajax({
                type: 'POST',
                url: '../Voucher/deletejournal',
                beforeSend : function(){
                    $('#deletejournalsbutton'+journalid).attr("disabled", true);
                },
                dataType: "json",
                data: {journalid:journalid},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#journelsrow'+data.Id).remove();
                        $('#journal').trigger('click');
                    }
                    else
                    {
                        alert(data.errortext);
                    }

                    $('#deletejournalsbutton'+data.Id).attr("disabled", false);
                }
            });
        }
    });
    $('#schedule').change(function(){
        var scheduleid=($(this).val());
        $.ajax({
            type: 'POST',
            url: '../Voucher/subschedule',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {scheduleid:scheduleid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#subschedule').html(data.result);
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });
    });
});

$(document).on('click','#generatevoucher',function(){
    var error=0;
    $('.error').hide();
    var id=$('#voucherid').val();
    if($('#schedule').val()=='none')
    {
        $('#schedule').next("span").html('Select Schedule').show('slow');
        error=1;
        //return false;
    }
    if($('#paymentdate').val()=='')
    {
        $('#paymentdate').next("span").html('Select Date').show('slow');
        error=1;
    }
    if($('#narration').val()=='')
    {
        $('#narration').next("span").html('Enter narration').show('slow');
        error=1;
    }
    if($('#bankid').val()==0)
    {
        $('#bankid').next("span").html('Select bank').show('slow');
        error=1;
    }
    if($('#chequenum').val()=='')
    {
        $('#chequenum').next("span").html('Enter Cheque no').show('slow');
        error=1;
    }
    if($('#allotedamount').val()=='')
    {
        $('#allotedamount').next("span").html('Enter Amount').show('slow');
        error=1;
    }
    if($('#creditacnt').val()=='0')
    {
        $('#creditacnt').next("span").html('Select Credit Account').show('slow');
        error=1;
    }
    if(error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../Voucher/PaymentVoucher',
            beforeSend : function(){
                $('#generatevoucher').attr("disabled", true);
            },
            dataType: "json",
            data: $( "#generatefrom" ).serialize(),
            success: function(data){
                if(data.error=='No')
                {
                    $('#generatevoucher').hide();
                    $('#printvoucher').show();
                    $("#printvoucher").attr("href", data.url);
                }
                else
                {
                    alert(data.errortext);
                }
                $('#generatevoucher').attr("disabled", false);
            }

        });

    }

});
$(document).on('click','#printvoucher',function(){
    var href=$(this).attr('href');
    $('<a href="'+href+'" target="_blank">&nbsp;</a>')[0].click();
    $('#cancelvoucher').trigger('click');
});

$(document).on('click','#receiptvoucher',function(){
    var error=0;
    $('.error').hide();
    if($('#receiptdate').val()=='')
    {
        $('#receiptdate').next("span").html('Select Date').show('slow');
        error=1;
    }
    if($('#narration').val()=='')
    {
        $('#narration').next("span").html('Enter narration').show('slow');
        error=1;
    }
    if($('#bankid').val()==0)
    {
        $('#bankid').next("span").html('Select bank').show('slow');
        error=1;
    }
    if($('#chequenum').val()=='')
    {
        $('#chequenum').next("span").html('Enter Cheque no').show('slow');
        error=1;
    }
    if($('#amount').val()=='')
    {
        $('#amount').next("span").html('Enter Amount').show('slow');
        error=1;
    }
    var creditacnt=$('.creditacnt').val();
    var accountid=$('#accountid').val();
    if(creditacnt=='')
    {
        $('#acntinfo').show('slow');
        error=1;
    }
    if(typeof(accountid)!== "undefined")
    {
        if(creditacnt=='')
        {
            $('#acntinfo').hide('slow');
            error=0;
        }
    }
    if(error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../Voucher/ReceiptVoucher',
            beforeSend : function(){
             $('#receiptvoucher').attr("disabled", true);
             },
            dataType: "json",
            data: $( "#receiptform" ).serialize(),
            success: function(data){
                if(data.error=='No')
                {
                    $('#receiptvoucher').hide();
                    $('#printreceipt').show();
                    $("#printreceipt").attr("href", data.url);
                }
                else
                {
                    alert(data.errortext);
                }
                $('#receiptvoucher').attr("disabled", false);
            }
        });
    }


});
$(document).on('click','#printreceipt',function(){
    var href=$(this).attr('href');
    $('<a href="'+href+'" target="_blank">&nbsp;</a>')[0].click();
    $('#cancelreceipt').trigger('click');
});

$(document).on('click','#billvoucher',function(){
    var error=0;
    $('.error').hide();
    if($('#Purpose').val()=='')
    {
        $('#Purpose').next("span").html('Enter Narration').show('slow');
        error=1;
    }
    if($('#duedate').val()=='')
    {
        $('#duedate').next("span").html('Select Due date').show('slow');
        error=1;
    }
    if(error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../Voucher/BillVoucher',
            beforeSend : function(){
             $('#billvoucher').attr("disabled", true);
             },
            dataType: "json",
            data: $( "#billform" ).serialize(),
            success: function(data){
                if(data.error=='No')
                {
                    $('#billvoucher').hide();
                    $('#printbillvoucher').show();
                    $("#printbillvoucher").attr("href", data.url);
                }
                else
                {
                    alert(data.errortext);
                }
                $('#billvoucher').attr("disabled", false);
            }
        });
    }


});
$(document).on("change", "#iowid", function () {
    //var itemid = $(this).attr('data-id');
    var iowid = $(this).val();

    $.ajax({
        type:'POST',
        url:'../voucher/ResourcesearchVoucher',
        dataType:"json",
        data:{iowid:iowid},
        success:function (data) {
            if (data.error == 'No') {
                var option = data.result;
                $("#resourceid").empty().append(option);
            }
            else {
                alert(data.errortext);
            }

        }
    });
});
$(document).on('click','#printbillvoucher',function(){
    $('#cancelbillvoucher').trigger('click');
});

$(document).on('click','#journalvoucher',function(){
    var error=0;
    $('.error').hide();
    if($('#projectid').val()==0)
    {
        $('#projectid').next("span").html('Select Project').show('slow');
        error=1;
    }
    if($('#schedule').val()=='none')
    {
        $('#schedule').next("span").html('Select Schedule').show('slow');
        error=1;
        //return false;
    }

    if($('#journaldate').val()=='')
    {
        $('#journaldate').next("span").html('Select Date').show('slow');
        error=1;
    }
    if($('#debitaccounts').val()==0)
    {
        $('#debitaccounts').next("span").html('Select Debit Account').show('slow');
        error=1;
    }
    if($('#creditaccounts').val()==0)
    {
        $('#creditaccounts').next("span").html('Select Credit Account').show('slow');
        error=1;
    }
    if($('#narration').val()=='')
    {
        $('#narration').next("span").html('Enter Narration').show('slow');
        error=1;
    }
    if($('#journalamount').val()=='')
    {
        $('#journalamount').next("span").html('Enter Amount').show('slow');
        error=1;
    }
    if(error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../Voucher/Generatejournal',
            beforeSend : function(){
             $('#journalvoucher').attr("disabled", true);
             },
            dataType: "json",
            data: $( "#journalvoucherform" ).serialize(),
            success: function(data){
                if(data.error=='No')
                {
                    $('#journalvoucher').hide();
                    $('#printjournal').show();
                    $("#printjournal").attr("href", data.url);
                }
                else
                {
                    alert(data.errortext);
                }
                $('#journalvoucher').attr("disabled", true);
            }
        });
    }


});
$(document).on('click','#printjournal',function(){
    var href=$(this).attr('href');
    $('<a href="'+href+'" target="_blank">&nbsp;</a>')[0].click();
    $('#canceljournal').trigger('click');
});

$(document).on('click','#contravoucher',function(){
    var error=0;
    $('.error').hide();
    if($('#narration').val()=='')
    {
        $('#narration').next("span").html('Enter Narration').show('slow');
        error=1;
    }
    if($('#contraamount').val()=='')
    {
        $('#contraamount').next("span").html('Enter Amount').show('slow');
        error=1;
    }
    if($('#chequeno').val()=='')
    {
        $('#chequeno').next("span").html('Enter Cheque no').show('slow');
        error=1;
    }
    var creditacnt=$('.creditacnt').val();
    var accountid=$('#accountid').val();
    if(creditacnt=='')
    {
        $('#acntinfo').show('slow');
        error=1;
    }
    if(typeof(accountid)!== "undefined")
    {
        if((creditacnt=='') && ($('#chequeno').val()!=''))
        {
            $('#acntinfo').hide('slow');
            error=0;
        }
    }
    if(error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../Voucher/GenerateContra',
            beforeSend : function(){
             $('#contravoucher').attr("disabled", true);
             },
            dataType: "json",
            data: $( "#contraform" ).serialize(),
            success: function(data){
                if(data.error=='No')
                {
                    $('#contravoucher').hide();
                    $('#printcontra').show();
                    $("#printcontra").attr("href", data.url);
                }
                else
                {
                    alert(data.errortext);
                }
                $('#contravoucher').attr("disabled", false);

            }
        });
    }


});
$(document).on('click','#printcontra',function(){
    var href=$(this).attr('href');
    $('<a href="'+href+'" target="_blank">&nbsp;</a>')[0].click();
    $('#cancelcontra').trigger('click');
});

$(document).on("change", "#place", function () {
    var place = $(this).val();
    var payment=$('#payment').val();
    $.ajax({
        type:'POST',
        url:'../../voucher/Projectaccount',
        dataType:"json",
        data:{place:place,payment:payment},
        success:function (data) {
            if (data.error == 'No') {
                $("#creditacnt").val(data.result);
            }
        }
    });
});

$(document).on('click','#savevoucher',function(){
    var error=0;
    $('.error').hide();
    var id=$('#voucherid').val();

    if($('#paymentdate').val()=='')
    {
        $('#paymentdate').next("span").html('Select Date').show('slow');
        error=1;
    }
    if($('#projectid').val()=='0')
    {
        $('#projectid').next("span").html('Select Project').show('slow');
        error=1;
    }
    if($('#place').val()=='0')
    {
        $('#place').next("span").html('Select Place').show('slow');
        error=1;
    }
    if($('#debitacnt').val()=='none')
    {
        $('#debitacnt').next("span").html('Select Account head').show('slow');
        error=1;
    }
    if($('#creditacnt').val()=='none')
    {
        $('#creditacnt').next("span").html('Select Account head').show('slow');
        error=1;
    }
    if($('#narration').val()=='')
    {
        $('#narration').next("span").html('Enter narration').show('slow');
        error=1;
    }
    if($('#bankid').val()==0)
    {
        $('#bankid').next("span").html('Select bank').show('slow');
        error=1;
    }
    if($('#chequenum').val()=='')
    {
        $('#chequenum').next("span").html('Enter Cheque no').show('slow');
        error=1;
    }
    if($('#amount').val()=='')
    {
        $('#amount').next("span").html('Enter Amount').show('slow');
        error=1;
    }
    if(error==0)
    {
        return true;
    }
    else
    {
        return false;
    }

});
$(document).on('click','#savejournalvoucher',function(){
    var error=0;
    $('.error').hide();

    if($('#paymentdate').val()=='')
    {
        $('#paymentdate').next("span").html('Select Date').show('slow');
        error=1;
    }
    if($('#place').val()=='0')
    {
        $('#place').next("span").html('Select Place').show('slow');
        error=1;
    }
    if($('#project').val()=='0')
    {
        $('#project').next("span").html('Select Project').show('slow');
        error=1;
    }
    if($('#debitacnt').val()=='none')
    {
        $('#debitacnt').next("span").html('Select Account head').show('slow');
        error=1;
    }
    if($('#creditaccount').val()=='none')
    {
        $('#creditaccount').next("span").html('Select Account head').show('slow');
        error=1;
    }
    if($('#narration').val()=='')
    {
        $('#narration').next("span").html('Enter narration').show('slow');
        error=1;
    }

    if($('#amount').val()=='')
    {
        $('#amount').next("span").html('Enter Amount').show('slow');
        error=1;
    }
    if(error==0)
    {
        return true;
    }
    else
    {
        return false;
    }

});
/*$(document).on('click','.generatevoucher',function(){
    $('#cashvouchersection').hide();
    $('#bankvouchersection').hide();
    $('#vouchersection').show();
    var id=$(this).val();
    var retval;
    $.ajax({
        type: 'POST',
        url: '../Voucher/Generate',

        async:false,
        data: {id:id},
        success: function(data){
            retval=data;

        }

    });
    $('#generatevoucher').html(retval);
});*/
/*$(document).on('click','#generatevoucher',function(){

    $.ajax({
        type: 'POST',
        url: '../Voucher/Voucher',
        beforeSend : function(){
            $('#generate').attr("disabled", true);
        },
        dataType: "json",
        data: $( "#generatevoucherform" ).serialize(),
        success: function(data){
            if(data.error=='No')
            {
                $('#vouchersection').hide();
                $('#cashvouchersection').show();
                $('#scheduleworkgroups').trigger('click');

            }
            else
            {
                alert(data.errortext);
            }
            $('#generate').attr("disabled", false);
        }
});
});*/

/*$(document).onchange("#selectaccounts",function(){
    var id=$(this).val();
    alert(id)
})*/
/*$(document).on('change','#selectaccounts',function(){
    var id=$(this).val();
    $.ajax({
        type: 'POST',
        url: '../Voucher/Getaccounts',
        async:false,
        data: {subgrpid:id},
        success: function(data){
            retval=data;
        }
    });
    $('#getacntname').html(retval);
});*/
/*$(document).on('focus','#searcaccounts',function(){
    var restype=$('#searcaccounts').val();
    $.ajax({
        type: 'POST',
        url: '../Voucher/Getaccounts',
        async:false,
        dataType: "json",
        data: {accounts:restype},
        success: function(data){
            var names=[];
            var i=0
            $.each(data, function(idx, obj) {
                *//**//*names[i]='"'+data[idx]+'"';*//**//*
                names[i]=data[idx];
                i++
            });
            $( "#searcaccounts" ).autocomplete({
                source: names
            });

        }
    });

});*/


