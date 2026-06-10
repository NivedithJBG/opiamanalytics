/**
 * Created by SolmindsDelli5 on 14-12-2018.
 */
$(document).on( "click", "#expenseapproval", function(){

    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }
    $('#listexpenseapproval').trigger('click') ;
});

$(function(){
    $('#listexpenseapproval').click(function(){
        $('#appexpenselistsection').slideDown('slow');// slide down the project listing div
        $.ajax({

            type: 'POST',

            url: '../FinanceRequests/Expenseforapproval',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            //data: {name:$('#searchcashadvance').val()},

            success: function(data){

                if(data.error=='No')

                {

                    $('#expenseappprovalitems').html(data.result);

                    $('#expenseappprovaltable').show();

                    $('.preloader').hide();
                }
            }
        });
    });
    $('#closeexpfappr').click(function(){
        //alert('sa')
        if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...

            $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

            //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

        }
    });
});
$(document).on('click','#approveexpenserequest',function(){
    var error=0;
    $('.error').hide();
    $('.expensestatus').each(function(){
        var id=$(this).attr('data-id');
        var advancestatus=($('input[name=expensestatus'+id+']:checked').val());
        if(advancestatus==1)
        {
            if($('#expenseacnthd'+id).val()=='none'){
                $('#expenseacnthd'+id).next('span').html('Select Accounthead').show('slow');
                error++;
            }
            if($('#expensepurpose'+id).val()==''){
                $('#expensepurpose'+id).next('span').html('Enter Narration').show('slow');
                error++;
            }
            var payment=($('input[name=paymenttype'+id+']:checked').val());
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
    if (error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../FinanceRequests/ApproveExpense',
            beforeSend : function(){
                //$('#approveadvrequest').attr("disabled", true);
            },
            dataType: "json",
            data: $( "#expenseapprovalform" ).serialize(),
            success: function(data){
                if(data.error=='No')
                {
                    $('#approveexpenserequest').attr("disabled", false);
                    $('#closeexpfappr').trigger('click') ;
                }
            }
        });
    }
    else {
        return false;
    }

});
$(document).on('click','.deleteexpenseapp',function(){
    var expenseid=$(this).val();
    var r = confirm("Are you sure you want to delete this Expense?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../FinanceRequests/Deleteexpense',
            beforeSend : function(){
                $('#deleteexpenseapp'+expenseid).attr("disabled", true);
            },
            dataType: "json",
            data: {expenseid:expenseid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#expenseapprovalrow'+expenseid).remove();
                    //$('#listadvanceapproval').trigger('click') ;
                }
                else
                {
                    alert(data.errortext);
                }

                $('#deleteexpenseapp'+expenseid).attr("disabled", false);
            }
        });
    }
});