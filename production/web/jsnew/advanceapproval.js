/**
 * Created by SolmindsDelli5 on 14-12-2018.
 */

$(document).on( "click", "#advanceapproval", function(){

    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }
    $('#listadvanceapproval').trigger('click') ;
});

$(function(){
    $('#listadvanceapproval').click(function(){
        $('#appadvancelistsection').slideDown('slow');// slide down the project listing div
        $.ajax({

            type: 'POST',

            url: '../FinanceRequests/Advanceforapproval',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            //data: {name:$('#searchcashadvance').val()},

            success: function(data){

                if(data.error=='No')

                {

                    $('#advanceappprovalitems').html(data.result);

                    $('#advanceappprovaltable').show();

                    $('.preloader').hide();
                }
            }
        });
    });
    $('#closeadvfappr').click(function(){
        //alert('sa')
        if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...

            $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

            //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

        }
    });
});

$(document).on('change','.advanceamount',function(){
   var id=$(this).attr('data-id');
    var totalamount=0;
    $('.advamount'+id).each(function(){
        totalamount+=$(this).val()*1
    });
    //alert(totalamount)
    $('#advancetotal'+id).html(totalamount.toFixed(2));

});
$(document).on('click','#approveadvrequest',function(){
    var error=0;
    $('.error').hide();
    $('.advancestatus').each(function(){
        var id=$(this).attr('data-id');
        var advancestatus=($('input[type=radio][id=advancestatus'+id+']:checked').val());
        if(advancestatus==1)
        {
            if($('#advanceacnthd'+id).val()=='none'){
                $('#advanceacnthd'+id).next('span').html('Select Accounthead').show('slow');
                error++;
            }
            if($('#advanceamount'+id).val()==''){
                $('#advanceamount'+id).next('span').html('Enter Amount').show('slow');
                error++;
            }
            if($('#advancepurpose'+id).val()==''){
                $('#advancepurpose'+id).next('span').html('Enter Narration').show('slow');
                error++;
            }
            var payment=($('input[type=radio][id=paymenttype'+id+']:checked').val());
            if (typeof payment === "undefined") {
                $('#paymenterror'+id).html('Select Voucher Type').show('slow');
                error++;
            }
            /*if(payment=='2'){
                $('#statusdiv'+id).html(
                    '<input type="radio" class="advancestatus" name="advancestatus'+id+'" data-id="'+id+'" value="5" checked> Save as draft' +
                    '<input type="radio" class="advancestatus" name="advancestatus'+id+'" data-id="'+id+'" value="2"> Deny'
                );
            }*/
        }

    });
    //alert(error)
    if (error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../FinanceRequests/ApproveCashadvance',
            beforeSend : function(){
                $('#approveadvrequest').attr("disabled", true);
            },
            dataType: "json",
            data: $( "#advapprovalform" ).serialize(),
            success: function(data){
                if(data.error=='No')
                {
                    $('#approveadvrequest').attr("disabled", false);
                    $('#closeadvfappr').trigger('click') ;
                }
            }
        });
    }
    else {
        return false;
    }

});

$(document).on('click','#saveasdraftrequest',function(){
    var error=0;
    $('.error').hide();
    $('.advancestatus').each(function(){
        var id=$(this).attr('data-id');
        var advancestatus=($('input[type=radio][id=advancestatus'+id+']:checked').val());
        if(advancestatus==1)
        {
            if($('#advanceamount'+id).val()==''){
                $('#advanceamount'+id).next('span').html('Enter Amount').show('slow');
                error++;
            }
            if($('#advancepurpose'+id).val()==''){
                $('#advancepurpose'+id).next('span').html('Enter Narration').show('slow');
                error++;
            }
        }

    });
    if (error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../FinanceRequests/DraftCashadvance',
            beforeSend : function(){
                $('#saveasdraftrequest').attr("disabled", true);
            },
            dataType: "json",
            data: $( "#advapprovalform" ).serialize(),
            success: function(data){
                if(data.error=='No')
                {
                    $('#saveasdraftrequest').attr("disabled", false);
                    $('#closeadvfappr').trigger('click') ;
                }
            }
        });
    }
    else {
        return false;
    }

});

$(document).on('click','.deletecashadvanceapp',function(){
    var cashadvanceid=$(this).val();
    var advid=$(this).attr('data-id');
    var r = confirm("Are you sure you want to delete this Advance?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../FinanceRequests/Deletecashadvance',
            beforeSend : function(){
                $('#deletecashadvanceapp'+cashadvanceid).attr("disabled", true);
            },
            dataType: "json",
            data: {cashadvance:cashadvanceid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#advanceapprovalrow'+cashadvanceid).remove();
                    var totalamount=0;
                    $('.advamount'+advid).each(function(){
                        totalamount+=$(this).val()*1
                    });
                    //alert(totalamount)
                    $('#advancetotal'+advid).html(totalamount.toFixed(2));
                    //$('#listadvanceapproval').trigger('click') ;
                }
                else
                {
                    alert(data.errortext);
                }

                $('#deletecashadvanceapp'+cashadvanceid).attr("disabled", false);
            }
        });
    }
});
