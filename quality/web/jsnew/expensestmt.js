/**
 * Created by SolmindsDelli5 on 30-11-2018.
 */

$(document).on( "click", ".viewexpense", function(){
    $('#advance').removeClass('active').next().slideUp();
    $('#expensestmt').addClass('active').next('.acc_container').slideDown();
    var id= $(this).val();
    $('#advancegroupid').val(id);
    $('#listexpensestmt').trigger('click') ;
});
/*$(document).on( "click", "#expensestmt", function(){

    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }
    $('#listexpensestmt').trigger('click') ;
});*/
$(function() {

    $('#listexpensestmt').click(function(){

        //$('#productaddsection').slideUp('slow');// slide down the project listing div

        $('#expensestmtlistsection').slideDown('slow');// slide down the project listing div
        $('#appexpensestmtlistsection').slideUp('slow');// slide down the project listing div

        $('#listexpensestmt').removeClass('btn-danger').addClass('btn-success');
        $('#listappexpensestmt').removeClass('btn-success').addClass('btn-danger');

        $.ajax({

            type: 'POST',

            url: '../FinanceRequests/Expensesearch',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {groupid:$('#advancegroupid').val()},

            success: function(data){

                if(data.error=='No')

                {
                    if(data.acntlistc==0)
                    {
                        $('#acnthdth').show();
                    }
                    else {
                        $('#acnthdth').hide();
                    }

                    $('#expensestmtitems').html(data.result);

                    $('#expensestmttable').show();
                    $('#datepicker0').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true});

                }

                $('.preloader').hide();

            }

        });

    });
    $('#closeexpensestmt').click(function(){
        if( $(this).next().is(':hidden') ) { //If immediate next container is closed...

            $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

            $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

        }
    });
    $('#listappexpensestmt').click(function(){

        //$('#productaddsection').slideUp('slow');// slide down the project listing div

        $('#expensestmtlistsection').slideUp('slow');// slide down the project listing div
        $('#appexpensestmtlistsection').slideDown('slow');// slide down the project listing div

        $('#listappexpensestmt').removeClass('btn-danger').addClass('btn-success');
        $('#listexpensestmt').removeClass('btn-success').addClass('btn-danger');

        $.ajax({

            type: 'POST',

            url: '../FinanceRequests/ApprovedExpense',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            //data: {name:$('#searchcashadvance').val()},

            success: function(data){

                if(data.error=='No')

                {

                    $('#appexpensestmtitems').html(data.result);

                    $('#appexpensestmttable').show();

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

$(document).on("change",'.expamount',function(){
    var totalamount=0;
    $('.expamount').each(function(){
        totalamount+=$(this).val()*1;
    });
    $('#expnpaytot').html(totalamount.toFixed(2));
    $('#expnpaytotval').val(totalamount);
    var payamount=$('#expnpaytotval').val();
    var recamount=$('#expnrectotval').val();
    //var closingbal=payamount - recamount;
    var closingbal=recamount - payamount;
    $('#expntot').html(closingbal.toFixed(2));
});

$(document).on("click",'#expensereport',function(){
    var error=0;
    if ($('#expntot').text()!=0)
    {
        error=0;
    }
    if (error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../FinanceRequests/expensestatement',
            beforeSend : function(){
                $('#expensereport').attr("disabled", true);
            },
            dataType: "json",
            data: $( "#expensereportform" ).serialize(),
            success: function(data){
                if(data.error=='No')
                {
                    $('#expensereport').hide();
                    $('#printexpstmt').show();
                    $("#printexpstmt").attr("href", data.url);
                    //$('#expensereport').attr("disabled", false);
                    //$('#expensereportform')[0].reset();
                    //$('#closeexpensestmt').trigger('click') ;
                }
            }
        });
    }
    else
    {
        alert('Closing balance must be zero')
    }

});
$(document).on('click','#printexpstmt',function(){
    $('#closeexpensestmt').trigger('click');
});