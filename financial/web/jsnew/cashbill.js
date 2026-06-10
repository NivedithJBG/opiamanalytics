/**
 * Created by SolmindsDelli5 on 15-02-2018.
 */
$(document).on( "click", ".viewcashbill", function(){
    $('#rproject').removeClass('active').next().slideUp();
    $('#cashbill').addClass('active').next('.acc_container').slideDown();
    var id= $(this).val();
    $('#selectedProjectId').val(id);
    $('#cashbillProjectId').val(id);
    $('#cashbillprojname').html(getProjectname(id));
    $('#listcashbill').trigger('click') ;
});

$(function(){
    $('#listcashbill').click(function(){
        $('#cashbilllistsection').slideDown('slow');
        $('#cashbilladdsection').slideUp('slow');
        $('#listcashbill').removeClass('btn-danger').addClass('btn-success');
        $('#addcashbill').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../Projects/Cashbillsearch',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectid:$('#selectedProjectId').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#cashbillitems').html(data.result);
                    $('#cashbilltable').show();
                }
                $('.preloader').hide();
            }
        });
    });

    $('#addcashbill').click(function(){
        $('#cashbilllistsection').slideUp('slow');
        $('#cashbilladdsection').slideDown('slow');
        $('#addcashbill').removeClass('btn-danger').addClass('btn-success');
        $('#listcashbill').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../Projects/Getactivities',
            /*beforeSend : function(){
                $('.preloader').show();
            },*/
            dataType: "json",
            data: {projectid:$('#selectedProjectId').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#cashactivitylist').html(data.result);
                }
                //$('.preloader').hide();
            }
        });
    });
});

$(document).on( "change","#cashactivitylist", function(){
    var activity=$(this).val();
    var projectid=$('#selectedProjectId').val();
    $.ajax({
        type: 'POST',
        url: '../Projects/Getresources',
        dataType: "json",
        data: {activity:activity,projectid:projectid},
        success: function(data){
            if(data.error=='No')
            {
                $('#cashresourcelist').html(data.result);
                //$('#estqty').val(data.quantity);
            }
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

function getProjectname(id)

{

    var retval;

    $.ajax({

        type: 'POST',

        url: '../projects/Getname',

        async:false,

        data: {id:id},

        success: function(data){

            retval=data;

        }

    });

    return retval;

}
