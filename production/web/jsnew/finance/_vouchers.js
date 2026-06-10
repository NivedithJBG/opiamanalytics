$(document).on( "click", ".acco-five input[type=radio]", function(){  

    $('#cashvoucher').trigger('click');
 
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
       $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
       //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
   }
   if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
       $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
       $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
       
   } 
   
   $('#cashvoucher').trigger('click');
});

$(function(){
    $('#cashvoucher').click(function(){
        $.ajax({
            type: 'POST',
            url: '../voucher/cashsearch',
            beforeSend : function(){
                $('#fin-preloader-vtabcashpayment').show();
            },
            dataType: "json",
            success: function(data){
                if(data.error=='No')
                {
                    $('#cash-paymenttable').html(data.result);
                }
                else
                {
                    alert(data.errortext);
                }
                $('#fin-preloader-vtabcashpayment').hide();
            }
        });
    });

    $('#cashreceipt').click(function(){
        $.ajax({
            type: 'POST',
            url: '../voucher/cashreceipt',
            beforeSend : function(){
                $('#fin-preloader-vtabcashreceipt').show();
            },
            dataType: "json",
            success: function(data){
                if(data.error=='No')
                {
                    $('#cash-receipttable').html(data.result);
                }
                else
                {
                    alert(data.errortext);
                }
                $('#fin-preloader-vtabcashreceipt').hide();
            }
        });
    });
    $('#bankvoucher').click(function(){
        $.ajax({
            type: 'POST',
            url: '../voucher/banksearch',
            beforeSend : function(){
                $('#fin-preloader-vtabbankpayment').show();
            },
            dataType: "json",
            success: function(data){
                if(data.error=='No')
                {
                    $('#bank-paymenttable').html(data.result);
                }
                else
                {
                    alert(data.errortext);
                }
                $('#fin-preloader-vtabbankpayment').hide();
            }
        });
    });
    $('#bankreceipt').click(function(){
        $.ajax({
            type: 'POST',
            url: '../voucher/bankreceipt',
            beforeSend : function(){
                $('#fin-preloader-vtabbankreceipt').show();
            },
            dataType: "json",
            success: function(data){
                if(data.error=='No')
                {
                    $('#bank-receipttable').html(data.result);
                }
                else
                {
                    alert(data.errortext);
                }
                $('#fin-preloader-vtabbankreceipt').hide();
            }
        });
    });
    $('#vjournal').click(function(){
        $.ajax({
            type: 'POST',
            url: '../voucher/journals',
            beforeSend : function(){
                $('#fin-preloader-vtabjournal').show();
            },
            dataType: "json",
            //data: {name:$('#searchbankvoucher').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#journaltable').html(data.result);
                }
                else
                {
                    alert(data.errortext);
                }
                $('#fin-preloader-vtabjournal').hide();
            }
        });
    });
    $(document).on('click','.generate-voucher-btn', function(){	
        var generateID = $(this).attr('data-id');
        if(generateID != ''){
            $('.vouchers-tab').addClass('generateVoucherFormActive');
            var link = '../voucher/'+$(this).attr('data-v');//alert(link);
            $.ajax({
                type: 'POST',
                url: link,
                beforeSend : function(){
                    $('#fin-preloader-GenarateVoucher').show();
                },
                dataType: "json",
                data: {generateID:generateID},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#generate-voucher-table').html(data.result);
                    }
                    else
                    {
                        alert(data.errortext);
                    }
                    $('#fin-preloader-GenarateVoucher').hide();
                }
            });
        }

    });
    $(document).on('click','#cancelvoucher',function(){
        $('.vouchers-tab').removeClass('generateVoucherFormActive');
        $('#bankreceipt').trigger('click');
        $('#bankvoucher').trigger('click');
        $('#cashreceipt').trigger('click');
        $('#cashvoucher').trigger('click');
    });
    $(document).on('click','#canceljournal',function(){
        $('.vouchers-tab').removeClass('generateVoucherFormActive');
        $('#vjournal').trigger('click');
    });
    $(document).on('click','#generatevoucher',function(){
        var value=$('#Gprojectid').val();
        $('#prjids').val(value);
        //alert('inside'); exit;
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
        /*if($('#bankid').val()==0)
        {
            $('#bankid').next("span").html('Select bank').show('slow');
            error=1;
        }*/
        /*if($('#chequenum').val()=='')
        {
            $('#chequenum').next("span").html('Enter Cheque no').show('slow');
            error=1;
        }*/
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
                url: '../voucher/paymentvoucher',
                beforeSend : function(){
                    $('#generatevoucher').attr("disabled", true);
                },
                dataType: "json",
                data: $( "#generateVfrom" ).serialize(),
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#generatevoucher').hide();
                        $('#printvoucher').show();
                        $("#printvoucher").attr("href", data.url);
                        $("#cash-vouche").addClass("active");
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
                url: '../voucher/generatecontra',
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
    $(document).on('click','.deletepayvoucher',function(){
        var voucherid=$(this).attr('data-id');
        if(voucherid != ''){
            var r = confirm("Are you sure you want to delete this Voucher ?");
            if (r == true) {
                $.ajax({
                    type: 'POST',
                    url: '../voucher/deletepayment',
                    beforeSend : function(){
                        $('#deletepayvoucher'+voucherid).attr("disabled", true);
                    },
                    dataType: "json",
                    data: {voucherid:voucherid},
                    success: function(data){
                        if(data.error=='No')
                        {
                            $('#cashsearch-'+data.Id).remove();
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
        }
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
       /* if($('#bankid').val()==0)
        {
            $('#bankid').next("span").html('Select bank').show('slow');
            error=1;
        }*/
        /*if($('#chequenum').val()=='')
        {
            $('#chequenum').next("span").html('Enter Cheque no').show('slow');
            error=1;
        }*/
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
                url: '../voucher/receiptvoucher',
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
    $(document).on('click','.deletereceiptvoucher',function(){
        var voucherid=$(this).attr('data-id');
        var r = confirm("Are you sure you want to delete this Voucher ?");
        if (r == true) {
            $.ajax({
                type: 'POST',
                url: '../voucher/deletereceipt',
                beforeSend : function(){
                    $('#deletereceiptvoucher'+voucherid).attr("disabled", true);
                },
                dataType: "json",
                data: {voucherid:voucherid},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#cashreceipt-'+data.Id).remove();
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
    $(document).on('click','.deletebankpayvoucher',function(){
        var voucherid=$(this).attr('data-id');
        var r = confirm("Are you sure you want to delete this Voucher ?");
        if (r == true) {
            $.ajax({
                type: 'POST',
                url: '../voucher/deletepayment',
                beforeSend : function(){
                    $('#deletebankpayvoucher'+voucherid).attr("disabled", true);
                },
                dataType: "json",
                data: {voucherid:voucherid},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#banksearch-'+data.Id).remove();
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
    $(document).on('click','.deletebankreceiptvoucher',function(){
        var voucherid=$(this).attr('data-id');
        var r = confirm("Are you sure you want to delete this Voucher ?");
        if (r == true) {
            $.ajax({
                type: 'POST',
                url: '../voucher/deletereceipt',
                beforeSend : function(){
                    $('#deletebankreceiptvoucher'+voucherid).attr("disabled", true);
                },
                dataType: "json",
                data: {voucherid:voucherid},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#bankreceipt-'+data.Id).remove();
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
    $(document).on('click','#journalvoucher',function(){ 
        var error=0;
        $('.error').hide();
        // alert('proid'+$('#projectid').val());
        // if($('#projectid').val()==0)
        // {
        //     $('#projectid').next("span").html('Select Project').show('slow');
        //     error=1;
        // }
        // alert(error);
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
        /*if($('#debitaccounts').val()==0)
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
        }*/
        if($('#journalamount').val()=='')
        {
            $('#journalamount').next("span").html('Enter Amount').show('slow');
            error=1;
        }

        var debitotal=0;
        var creditotal=0;
        var debtot=0;
        $('.debitamount').each(function(){
            var id=$(this).attr('data-id');
            debitotal=debitotal+$(this).val()*1;
            debtot=debitotal.toFixed(2);//alert(debitotal)
            if($("#debitamount_"+id).val()=='')
            {
                $("#debitamount_"+id).next("span").html('Enter Debit Amount').show('slow');
                error=1;
            }
        });
        
        $('.creditamount').each(function(){
            var id=$(this).attr('data-id');
            creditotal=creditotal+$(this).val()*1;//alert(creditotal)
            credtotal = creditotal.toFixed(2);
            if($("#creditamount_"+id).val()=='')
            {
                $("#creditamount_"+id).next("span").html('Enter Credit Amount').show('slow');
                error=1;
            }
        });
        if (debtot!=credtotal)
        {
            alert("Debit amount and credit amount must be same");
            error=1;
        }
       
        if(error==0)
        {
            $.ajax({
                type: 'POST',
                url: '../voucher/generatejournal',
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
                        // location.reload();
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
    $(document).on('click','.deletejournalsbutton',function(){
        var journalid=$(this).attr('data-id');
        var r = confirm("Are you sure you want to delete this Journal ?");
        if (r == true) {
            $.ajax({
                type: 'POST',
                url: '../voucher/deletejournal',
                beforeSend : function(){
                    $('#deletejournalsbutton'+journalid).attr("disabled", true);
                },
                dataType: "json",
                data: {journalid:journalid},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#journelsrow-'+data.Id).remove();
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

    $(document).on('click','.add_credit_row',function(){

        var numItems = $('.remove_credit_row').length;

        var numcreditItems = $('.credit_row').length;

        if(numcreditItems>0){

            $(".add_debit_row").attr('disabled','disabled');

        }
        else{

            $(".add_debit_row").removeAttr('disabled');

        }

        $.ajax({
            type: 'POST',
            url: '../voucher/addcreditrow',
            dataType: "json",
            data: {numItems:numItems},
            success: function(data){
                if(data.error=='No')
                {
                    $('#credit_row_'+data.lastrow).after(data.result);
                }
                else
                {
                    alert(data.errortext);
                }
            }
        });
    });

    $(document).on('click','.remove_credit_row',function(){

        var journalid=$(this).attr('data-id');

        $('#credit_row_'+journalid).remove();

        var numcreditItems = $('.credit_row').length;

        if(numcreditItems>1){

            $(".add_debit_row").attr('disabled','disabled');

        }
        else{

            $(".add_debit_row").removeAttr('disabled');

        }
        
    });

    $(document).on('click','.add_debit_row',function(){

        var numItems = $('.remove_debit_row').length;

        var numcreditItems = $('.debit_row').length;

        if(numcreditItems>0){

            $(".add_credit_row").attr('disabled','disabled');

        }
        else{

            $(".add_credit_row").removeAttr('disabled');

        }

        $.ajax({
            type: 'POST',
            url: '../voucher/adddebitrow',
            dataType: "json",
            data: {numItems:numItems},
            success: function(data){
                if(data.error=='No')
                {
                    $('#debit_row_'+data.lastrow).after(data.result);
                }
                else
                {
                    alert(data.errortext);
                }
            }
        });
    });

    $(document).on('click','.remove_debit_row',function(){

        var journalid=$(this).attr('data-id');

        $('#debit_row_'+journalid).remove();

        var numcreditItems = $('.debit_row').length;

        if(numcreditItems>1){

            $(".add_credit_row").attr('disabled','disabled');

        }
        else{

            $(".add_credit_row").removeAttr('disabled');

        }

    });

});

$(document).on( "change", "#debitaccounts_0", function(){ 

    var debitid = $('#debitaccounts_0').val();

    $.ajax({
        type: 'POST',
        url: '../voucher/addscheduledropdown',
        dataType: "json",
        data: {debitid:debitid},
        success: function(data){
            if(data.error=='No')
            {
                //$('#accntschitem').html(data.result);
                $('.scheduleitemdrop').html(data.result);
            }
            else
            {
                alert(data.errortext);
            }
        }
    });

});