/**
 * Created by SolmindsDelli5 on 02-11-2017.
 */

$(document).on( "click", "#workorder", function(){

    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }
    var advanceid=$('#advanceid').val();
    if (advanceid=='')
    {
        $('#listcashbill').trigger('click');

    }
    else
    {
        $('#addcashbill').trigger('click');
    }

});
$(function() {
    $('#workordersearch').click(function () {
        $.ajax({
            type: 'POST',
            url: '../FinanceRequests/Workordersearch',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            //data: {projectid:$('#selectedProjectId').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#workorderitems').html(data.result);
                    $('#workordertable').show();
                }
                $('.preloader').hide();
            }
        });
    });
    $('#listcashbill').click(function(){

        //$('#productaddsection').slideUp('slow');// slide down the project listing div

        $('#cashbilllistsection').slideDown('slow');// slide down the project listing div
        $('#cashbilladdsection').slideUp('slow');

        $('#listcashbill').removeClass('btn-danger').addClass('btn-success');

        $('#addcashbill').removeClass('btn-success').addClass('btn-danger');

        $.ajax({

            type: 'POST',

            url: '../FinanceRequests/Cashbillsearch',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {name:$('#searchcashbill').val()},

            success: function(data){

                if(data.error=='No')

                {

                    $('#cashbillitems').html(data.result);

                    $('#cashbilltable').show();

                }

                else

                {

                    alert(data.errortext);

                }

                $('.preloader').hide();

            }

        });

    });
    /*$('#cashbillsearch').click(function(){

        $('#listcashbill').trigger('click');

    });*/
    $('#addcashbill').click(function(){
        $('#cashbilllistsection').slideUp('slow');
        $('#cashbilladdsection').slideDown('slow');
        $('#addcashbill').removeClass('btn-danger').addClass('btn-success');
        $('#listcashbill').removeClass('btn-success').addClass('btn-danger');
        var advanceid=$('#advanceid').val();
        $.ajax({
            type: 'POST',
            url: '../FinanceRequests/Getadvances',
            beforeSend : function(){
             $('.preloader').show();
             },
            dataType: "json",
            data: {advanceid:advanceid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#advanceitems').html(data.result);
                    $('#cashbillcreatetable').show();
                    $('.preloader').hide();
                }
            }
        });
    });
});
$(document).on( "change","#cashprojectlist", function(){
    var project=$(this).val();
    $.ajax({
        type: 'POST',
        url: '../Projects/Getprocess',
        /*beforeSend : function(){
         $('.preloader').show();
         },*/
        dataType: "json",
        data: {projectid:project},
        success: function(data){
            if(data.error=='No')
            {
                $('#cashprocesslist').html(data.result);
            }
            //$('.preloader').hide();
        }
    });
});
$(document).on( "change","#cashprocesslist", function(){
    var project=$('#cashprojectlist').val();
    var process=$(this).val();
    $.ajax({
        type: 'POST',
        url: '../Projects/Getactivities',
        /*beforeSend : function(){
         $('.preloader').show();
         },*/
        dataType: "json",
        data: {projectid:project,processid:process},
        success: function(data){
            if(data.error=='No')
            {
                $('#cashactivitylist').html(data.result);
            }
            //$('.preloader').hide();
        }
    });
});
$(document).on('click','#savecashbill',function(){
    $.ajax({
        type: 'POST',
        url: '../Projects/SaveCashbill',
        dataType: "json",
        data: $( "#cashbillform" ).serialize(),
        success: function(data){
            if(data.error=='No')
            {
                $('#cashbillform')[0].reset();
                $('#listcashbill').trigger('click') ;
            }
        }
    });
});
$(document).on('click','.deletecashbill',function(){
    var cashbillid=$(this).val();
    var r = confirm("Are you sure you want to delete this Cashbill?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../FinanceRequests/deletecashbill',
            beforeSend : function(){
                $('#deletecashbill'+cashbillid).attr("disabled", true);
            },
            dataType: "json",
            data: {cashbill:cashbillid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#cashbillrow'+cashbillid).remove();
                }
                else
                {
                    alert(data.errortext);
                }

                $('#deletecashbill'+cashbillid).attr("disabled", false);
            }
        });
    }
});