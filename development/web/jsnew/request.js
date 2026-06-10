$(document).on( "click", "#request", function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#listrequest').trigger('click');
});

/*function test()
{
    if(!$('#request').next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').next().slideDown(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
        $('#listrequest')[0].click();
    }

}*/
$(function(){
    //test();
    $('#listrequest').click(function(){
        $('#requestaddsection').slideDown('slow');// slide down the project listing div
        $('#requestlistsection').slideUp('slow');// slide down the project listing div
        $('#requesthistsection').slideUp('slow');// slide down the project listing div
        $.ajax({
            type: 'POST',
            url: '../FinanceRequests/Getrequestrows',
            dataType:"json",
            success: function(data){
                if(data.count==1){
                    $('#fundreqitems').html(data.result);
                    $('#addmore').show();
                }
                else {
                    $('#fundreqitems').html('');
                }
            }
        });
    });

    $('#listpendingrequest').click(function(){
        $('#requestaddsection').slideUp('slow');// slide down the project listing div
        $('#requesthistsection').slideUp('slow');
        $('#requestlistsection').slideDown('slow');// slide down the project listing div
        $('#listpendingrequest').removeClass('btn-danger').addClass('btn-success');
        $('#addrequest').removeClass('btn-success').addClass('btn-danger');
        $('#requesthistory').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../FinanceRequests/search',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {name:$('#searchrequest').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#requestitems').html(data.result);
                    $('#requesttable').show();
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });
    });

    $('#requestsearch').click(function(){
        $('#listpendingrequest').trigger('click');
    });

    $('#requesthistory').click(function(){
        $('#requestaddsection').slideUp('slow');// slide down the project listing div
        $('#requestlistsection').slideUp('slow');// slide down the project listing div
        $('#requesthistsection').slideDown('slow');// slide down the project listing div
        $('#requesthistory').removeClass('btn-danger').addClass('btn-success');
        $('#addrequest').removeClass('btn-success').addClass('btn-danger');
        $('#listpendingrequest').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../FinanceRequests/requesthistory',
            beforeSend : function(){
                $('.preloader').show();
            },
            data: {fromdate:$('#reqhistoryfromdate').val(),todate:$('#reqhistorytodate').val()},
            dataType: "json",
            success: function(data){
                if(data.error=='No')
                {
                    $('#requesthistitems').html(data.result);
                    $('#requesthisttable').show();
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });
    });
    $('#requesthistsearch').click(function(){
        $('#requesthistory').trigger('click');
    });
    $('#addrequest').click(function(){
        $('#requestlistsection').slideUp('slow');
        $('#requesthistsection').slideUp('slow');
        $('#requestaddsection').slideDown('slow');
        $('#addrequest').removeClass('btn-danger').addClass('btn-success');
        $('#listpendingrequest').removeClass('btn-success').addClass('btn-danger');
        $('#requesthistory').removeClass('btn-success').addClass('btn-danger');
        var error=0;
        if($('#requser_account').val()==0){
            error=1;
            alert('You have not assigned a Accounthead for creating fund request.')
        }
        if($('#requser_place').val()==0){
            error=1;
            alert('You have not assigned a Place for creating fund request.')
        }
        if($('#project').val()==0){
            error=1;
            $('#project').next('span').html('Select Project').show();
        }
        if (error==0){
            $.ajax({
                type: 'POST',
                url: '../FinanceRequests/Getrequestrows',
                dataType:"json",
                success: function(data){
                    if(data.count==0){
                        $('#fundreqitems').html(data.result);
                        //$('#fundreqrow').show();
                        $('#addmore').show();
                        //$('#fundreqsaverow').show();
                    }
                    else {
                        $('#fundreqitems').html(data.result);
                        $('#addmore').show();
                        //$('#fundreqsaverow').show();
                        //$('#fundreqsaverow').before(data.result);
                    }

                }
            });
        }
    });

    $(document).on('blur','.fundamount',function(){
        var id=$(this).attr('data-id');
        var req_amount=$(this).val()*1;
        var gst_amount=($('#fundsgstamount'+id).val()*1) *2;
        var igst_amount=$('#fundigstamount'+id).val()*1;
        var netamount=req_amount + (gst_amount + igst_amount);
        $('#fundreqnet'+id).text(netamount.toFixed(2));

    });
    $(document).on('blur','.fundcgstamount',function(){
        var id=$(this).attr('data-id');
        var amount=$(this).val()*1;
        if (amount!=0)
        {
            $('#fundsgstamount'+id).val(amount);
            $('#fundigstamount'+id).val(0);
            var req_amount=$('#fundamount'+id).val()*1;
            var netamount=req_amount + (amount + amount);
            $('#fundreqnet'+id).text(netamount.toFixed(2));
        }

    });

    $(document).on('blur','.fundigstamount',function(){
        var id=$(this).attr('data-id');
        var amount=$(this).val()*1;
        if(amount!=0)
        {
            $('#fundsgstamount'+id).val(0);
            $('#fundcgstamount'+id).val(0);
            var req_amount=$('#fundamount'+id).val()*1;
            var netamount=req_amount + amount;
            $('#fundreqnet'+id).text(netamount.toFixed(2));
        }

    });
    $(document).on('click','#saveasdraftreq',function(){
        var error=0;
        $('.error').hide();
        $('.fundamount').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='' && $('#fundpurchaseadv'+id).val()==0)
            {
                $("#fundamount"+id).next("span").html('Enter Amount').show('slow');
                error=1;
            }
        });
        $('.fundpurpose').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='')
            {
                $("#fundpurpose"+id).next("span").html('Enter Purpose').show('slow');
                error=1;
            }
        });
        $('.fundpaymode').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='none')
            {
                $("#fundpaymode"+id).next("span").html('Select Payment Mode').show('slow');
                error=1;
            }
        });
        $('.fundpaytype').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='none')
            {
                $("#fundpaytype"+id).next("span").html('Select Payment Type').show('slow');
                error=1;
            }
        });
        if($('#requser_account').val()=='0')
        {
            $("#requser_account").next("span").html('Select Accounthead').show('slow');
            error=1;
        }
        if (error==0){
            $.ajax({
                type: 'POST',
                url: '../FinanceRequests/Savefundrequest',
                beforeSend : function(){
                    $('#saveasdraftreq').attr("disabled", true);
                },
                data:$('#fundrequestform').serialize()+"&status=5",
                dataType:"json",
                success: function(data){
                    if(data.result=='Yes'){
                        //$('#saveasdraftreq').attr("disabled", false);
                        $('#closefundreq').trigger('click') ;
                    }
                }
            });
        }
    });
    $('#closefundreq').click(function(){
        //alert('sa')
        if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...

            $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

            //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

        }
    });
    $(document).on('click','#savefundreq',function(){
        var error=0;
        $('.error').hide();
        $('.fundamount').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='' && $('#fundpurchaseadv'+id).val()==0)
            {
                $("#fundamount"+id).next("span").html('Enter Amount').show('slow');
                error=1;
            }
        });
        $('.fundpurpose').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='')
            {
                $("#fundpurpose"+id).next("span").html('Enter Purpose').show('slow');
                error=1;
            }
        });
        $('.fundpaymode').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='none')
            {
                $("#fundpaymode"+id).next("span").html('Select Payment Mode').show('slow');
                error=1;
            }
        });
        $('.fundpaytype').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='none')
            {
                $("#fundpaytype"+id).next("span").html('Select Payment Type').show('slow');
                error=1;
            }
        });
        if($('#requser_account').val()=='0')
        {
            $("#requser_account").next("span").html('Select Accounthead').show('slow');
            error=1;
        }
        if (error==0){
            $.ajax({
                type: 'POST',
                url: '../FinanceRequests/Savefundrequest',
                beforeSend : function(){
                    $('#savefundreq').attr("disabled", true);
                },
                data:$('#fundrequestform').serialize()+"&status=0",
                dataType:"json",
                success: function(data){
                    if(data.result=='Yes'){
                        $('#savefundreq').attr("disabled", false);
                        $('#closefundreq').trigger('click');
                        //$('#listpendingrequest').trigger('click');
                    }
                }
            });
        }
    });
    /*$('#addproduct').click(function(){
     $('#productlistsection').slideUp('slow');// slide down the project listing div
     $('#productaddsection').slideDown('slow');// slide down the project listing div
     $('#addproduct').removeClass('btn-danger').addClass('btn-success');
     $('#listproduct').removeClass('btn-success').addClass('btn-danger');
     $('.error').hide();

     });*/
    $(document).on('submit','#requestform',function(){
        var error=0;
        $('.error').hide();
        /*if($('#Amount').val()=='')
        {
            $("#Amount").next("span").html('Enter Amount').show('slow');
            error=1;
        }*/
        $('.Amount').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='')
            {
                $("#Amount"+id).next("span").html('Enter Amount').show('slow');
                error=1;
            }
        });
        $('.Purpose').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='')
            {
                $("#Purpose"+id).next("span").html('Enter Purpose').show('slow');
                error=1;
            }
        });
        $('.paymethod').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='none')
            {
                $("#paymethod"+id).next("span").html('Select Payment Type').show('slow');
                error=1;
            }
        });
        $('.billnoinfo').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='none')
            {
                $("#billnoinfo"+id).next("span").html('Select Bill no/Invoice no').show('slow');
                error=1;
            }
        });
        if($('#project').val()=='0')
        {
            $("#project").next("span").html('Select Project').show('slow');
            error=1;
        }
        if($('#place').val()=='0')
        {
            $("#place").next("span").html('Select Place').show('slow');
            error=1;
        }

        if(error==0)
        {
            $.ajax({
                type: 'POST',
                url: '../FinanceRequests/create',
                dataType: "json"
            });
        }
        else
        {
            return false;
        }

    });

});
$( document ).ready(function() {
    var numberOfChecked = $('#checkbox').prop('checked', true);
    //alert(numberOfChecked)

});
/*$(document).on( "blur",".requestamount", function(){
    var itemid=$(this).attr('data-id');
    var amount =$(this).val()*1;
    var tds=$('#tdsper'+itemid).val();
    var tax=$('#taxper'+itemid).val();
    var tdsamount=(amount * tds)/100;
    var roundtds=Math.round(tdsamount);
    var taxamount=(amount * tax)/100;
    var totalamount=amount + taxamount;
    var netamount=totalamount - roundtds;
    $('#accountstds'+itemid).text(roundtds).val();
    $('#account_tds'+itemid).val(roundtds).val();
    $('#accountservtax'+itemid).text(taxamount).val();
    $('#account_tax'+itemid).text(taxamount).val();
    $('#accountnet'+itemid).text(netamount.toFixed(2)).val();
    $('#account_net'+itemid).val(netamount);
    var totalrate=0;
    $('.requestamount').each(function(){
        totalrate=totalrate+$(this).val()*1;
    });
    $('#requestratetotal').html(totalrate);
    /!*var totalnet=0;
    $('.account_net').each(function(){
        totalnet=totalnet+$(this).val()*1;
    });
    $('#totalnetamount').html(totalnet);*!/
});*/
$(document).on( "blur",".account_tds", function(){
    var itemid=$(this).attr('data-id');
    var tdsamount =$(this).val()*1;
    var amount=$('#request_amount'+itemid).val();
    var netamount=amount - tdsamount;
    $('#accountnet'+itemid).text(netamount.toFixed(2));
    $('#netamount'+itemid).text(netamount.toFixed(2)).val();
    $('#account_net'+itemid).val(netamount);
    var totalnet=0;
    $('.account_net').each(function(){
        totalnet=totalnet+$(this).val()*1;
    });
    $('#totalnetamnt').html(totalnet);

});
$(document).on( "change",".requeststatus", function(){
    var status=$('.requeststatus').val();
    var totalnet=0;
    $('.requeststatus').each(function(){
        if($(this).val()==1){
            var requestid=$(this).attr('data-id');
            if($('#accountshead'+requestid).val()=='0')
            {
                $("#accountshead"+requestid).next("span").html('Select Account head').show('slow');
                $('.error').show();
                $("#approverequest").attr('disabled','disabled');
            }

            if($('#accounthead'+requestid).val()=='0')
            {
                $("#accounthead"+requestid).next("span").html('Select Account head').show('slow');
                $('.error').show();
            }
            var netamount=($('#account_net'+requestid).val()*1);
            if($('#contraentry'+requestid).is(":checked")) {
                totalnet=totalnet;
            }
            else
            {
                totalnet=totalnet + netamount;
            }

        }
        else
        {
            $('.error').hide();
        }

    });
    var cashamount=$('#cashamount').val()*1;
    var bankamount=$('#bankamount').val()*1;
    var totalamount=cashamount + bankamount;
    $('#totalnetamount').html(totalnet.toFixed(2));
    $('#totalnetamnt').html(totalnet.toFixed(2));
    $('#totalamount').html(totalnet.toFixed(2));
    $('#amounttotal').val(totalnet);
    /*if(status==1)
    {

        var requestid=$(this).attr('data-id');
        alert(requestid)

        var totalnet=0;

    }
    var requestid=$(this).attr('data-id');
    alert(requestid)
*/


});

$(document).on("click",".paymenttype",function(){
    var cashamount=0;
    var bankamount=0;
    $(".paymenttype").each(function(){
        var checked=$(this).is(":checked");
        var type=$(this).attr('data-type');
        if(checked==true){
            if(type=='cash')
            {
                var itemid=$(this).attr('data-id');
                cashamount=cashamount + ($('#account_net'+itemid).val()*1);
            }
            else
            {
                var itemid=$(this).attr('data-id');
                bankamount=bankamount + ($('#account_net'+itemid).val()*1);
            }
        }
    });
    //var id=$(this).attr('data-id');
    //alert($('#requestamount'+id).val());
    //var totalamount=cashamount + bankamount;
    $('#cashtotal').html(cashamount.toFixed(2));
    $('#cashamount').val(cashamount);
    $('#banktotal').html(bankamount.toFixed(2));
    $('#bankamount').val(bankamount);
    //$('#totalamount').html(totalamount.toFixed(2));
    /*
    var type=$(this).attr('data-type');
    var itemid=$(this).attr('data-id');
    var amount=$('#requestamount'+itemid).val();
    var cashtotal=0;*/

});

$(document).on( "change",".accountname", function(){
    var requestid=$(this).attr('data-id');
    var accountid=$(this).val();
    var amount=$('#request_amount'+requestid).val();
    var place=$('#place').val();
    $.ajax({
        type: 'POST',
        url: '../../FinanceRequests/accounttds',
        dataType:"json",
        data:{accountid:accountid,amount:amount,place:place},
        success: function(data){
            if(data.typeerror=='No'){
                $('.paymenttype').prop('checked', false);

                $('.paymenttype').attr('disabled', 'disabled');

            if(data.error=='No')
            {
                $('#accounttds'+requestid).text(data.TDS).val();
                $('#account_tds'+requestid).val(data.TDS);
                $('#ledgerbal'+requestid).text(data.Ledgerbal);
                $('#accountstax'+requestid).text(data.Tax).val();
                $('#serv_tax'+requestid).val(data.Tax);
                $('#accountnet'+requestid).text(data.Netamount);
                $('#netamount'+requestid).text(data.Netamount).val();
                $('#account_net'+requestid).val(data.Netamount);
                var netamount=0;
                $('.netamount').each(function(){
                    netamount=netamount+$(this).val()*1;
                });
                $('#totalnetamnt').html(netamount);
            }
            else
            {
                alert(data.errortext);
            }
            }
        }
    });
});

$(document).on( "change",".accountshead", function(){
    var requestid=$(this).attr('data-id');
    var accountid=$(this).val();
    var amount=$('#request_amount'+requestid).val();
    var place=$('#place').val();
    $.ajax({
        type: 'POST',
        url: '../../FinanceRequests/accounttds',
        dataType:"json",
        data:{accountid:accountid,amount:amount,place:place,requestid:requestid},
        success: function(data){
            if(data.error=='No')
            {
                $('#accountstds'+requestid).text(data.TDS).val();
                $('#account_tds'+requestid).val(data.TDS);
                $('#advancecheck'+requestid).html(data.advance);
                $('#ledgerbal'+requestid).text(data.ledgbal);
                $('#ledgbal'+requestid).val(data.Ledgerbal);
                $('#accountservtax'+requestid).text(data.Tax).val();
                $('#account_tax'+requestid).val(data.Tax);
                $('#tdsper'+requestid).val(data.Tdsper);
                $('#taxper'+requestid).val(data.Taxper);
                $('#accountnet'+requestid).text(data.Netamount);
                $('#account_net'+requestid).val(data.Netamount);
                $('#work'+requestid).hide();
                /*var netamount=0;
                $('.account_net').each(function(){
                    netamount=netamount+$(this).val()*1;
                });
                $('#totalnetamount').html(netamount.toFixed(2));*/
            }
            else
            {
                $('#work'+requestid).hide();
                alert(data.errortext);
            }

        }
    });
    if (accountid==0){
        $("#approverequest").attr('disabled','disabled');
    }
    else {
        $("#approverequest").removeAttr('disabled');
    }

});

$(document).on('submit','#approvalfrom',function(){
    var count=0;
    $(".requeststatus").each(function(){
        var id=$(this).attr('data-id');
        if($('#rqststatus'+id).val()==1){
            if($('#contraentry'+id).is(":checked")) {
                return true;
            }
            if(!$('.paymentmode'+id+':checked').val())
            {
                count++;
            }
        }
    });

    if(count!=0)
    {
        alert('Please select payment type');
        return false;
    }
    return true;
});

$(document).on('click','.denyrequest',function(){
    var id=$(this).val();
    $.ajax({
        type: 'POST',
        url: '../FinanceRequests/Deny/'+id,
        dataType:"json",
        success: function(data){

            $('#listrequest').trigger('click');

        }
    });

});

$(document).on('click','.deletefundrqstbutton',function(){
    var reqid=$(this).val();
    var r = confirm("Are you sure you want to delete this Request ?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../FinanceRequests/Delete/',
            beforeSend : function(){
                $('#deletefundrqstbutton'+reqid).attr("disabled", true);
            },
            dataType: "json",
            data: {reqid:reqid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#requestrow'+data.Id).remove();
                    $('#listrequest').trigger('click');
                }
                else
                {
                    alert(data.errortext);
                }

                $('#deletefundrqstbutton'+data.Id).attr("disabled", false);
            }
        });
    }
    else {
        return false;
    }
});

$(document).on('click','.deletefundrqst',function(){
    var reqid=$(this).val();
    var r = confirm("Are you sure you want to delete this Request ?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../FinanceRequests/DeleteRequest/',
            beforeSend : function(){
                $('#deletefundrqst'+reqid).attr("disabled", true);
            },
            dataType: "json",
            data: {reqid:reqid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#requestrow'+data.Id).remove();
                    $('#listpendingrequest').trigger('click');
                }
                else
                {
                    alert(data.errortext);
                }

                $('#deletefundrqst'+data.Id).attr("disabled", false);
            }
        });
    }
    else {
        return false;
    }
});
