/**
 * Created by SolmindsDelli5 on 19-09-2018.
 */
//$(document).on( "click", ".viewissuedslips", function(){
$(document).on( "click", "#issueslips", function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    //$('#rproject').removeClass('active').next().slideUp();
    //$('#issueslips').addClass('active').next('.acc_container').slideDown();
    //var id= $(this).val();
    //$('#selectedProjectId').val(id);
    //$('#issueslipprojectid').val(id);
    //$('#issueslipsprojname').html(getProjectname(id));
    $('#addissueslips').trigger('click');
});
$(function(){
    $('#listissueslips').click(function(){
        $('#issueslipsaddsection').slideUp('slow');
        $('#issueslipslistsection').slideDown('slow');
        $('#listissueslips').removeClass('btn-danger').addClass('btn-success');
        $('#addissueslips').removeClass('btn-success').addClass('btn-danger');
        //$('#projissueslip').show();
        $.ajax({
            type: 'POST',
            url: '../report/Issueslipsearch',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectid:$('#projissueslip').val(),resourcename:$('#issresval').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#issueslipsitems').html(data.result);
                    $('#issreslist').html(data.resource);
                    $('#issueslipstable').show();
                }
                $('.preloader').hide();
            }
        });
    });
    /*$('#projissueslip').change(function(){
        $('#listissueslips').trigger('click');
    });*/
    $('#issslipressearch').click(function(){
        $('#listissueslips').trigger('click');
    });
    $('#addissueslips').click(function(){
        $('#issueslipslistsection').slideUp('slow');
        $('#issueslipsaddsection').slideDown('slow');
        $('#addissueslips').removeClass('btn-danger').addClass('btn-success');
        $('#listissueslips').removeClass('btn-success').addClass('btn-danger');
        //$('#projissueslip').hide();
        $.ajax({
            type: 'POST',
            url: '../report/Getiow',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectid:$('#projissuesliplist').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('.issueslipiowlist').html(data.result);
                }
                $('.preloader').hide();
            }
        });
    });
});
$(document).on( "change","#projissuesliplist", function(){
    $.ajax({
        type: 'POST',
        url: '../report/Getiow',
        beforeSend : function(){
            $('.preloader').show();
        },
        dataType: "json",
        data: {projectid:$(this).val()},
        success: function(data){
            if(data.error=='No')
            {
                //$('#isslipwbsstructlist').html(data.result);
                $('.issueslipiowlist').html(data.result);
            }
            $('.preloader').hide();
        }
    });
});

$(document).on( "change",".isslipwbsstructlist", function(){
    var id=$(this).attr('data-id');
    var structure=$(this).val();
    var projectid=$('#projissuesliplist').val();
    $.ajax({
        type: 'POST',
        url: '../report/Getiow',
        dataType: "json",
        data: {projectid:projectid,structureid:structure},
        success: function(data){
            if(data.error=='No')
            {
                $('#issueslipiowlist'+id).html(data.result);
            }
        }
    });
});

$(document).on( "change",".issueslipiowlist", function(){
    var id=$(this).attr('data-id');
    var iow=$(this).val();
    var projectid=$('#projissuesliplist').val();
    $.ajax({
        type: 'POST',
        url: '../report/Getactivity',
        dataType: "json",
        data: {projectid:projectid,iow:iow},
        success: function(data){
            if(data.error=='No')
            {
                $('#issueslipactivity'+id).html(data.result);
            }
        }
    });
});
$(document).on( "change",".issueslipactivity", function(){
    var id=$(this).attr('data-id');
    var activity=$(this).val();
    $.ajax({
        type: 'POST',
        url: '../report/Getresources',
        dataType: "json",
        data: {activityid:activity},
        success: function(data){
            if(data.error=='No')
            {
                $('#IssueslipResource'+id).html(data.result);
                //$('#issueslipunit').html(data.unit);
                //$('#issueslipunit').val(data.unit);
            }
        }
    });
});
$(document).on( "change",".IssueslipResource", function(){
    var id=$(this).attr('data-id');
    var resource=$(this).val();
    var activityid=$('#issueslipactivity'+id).val();
    var iowid=$('#issueslipiowlist'+id).val();
    var projectid=$('#projissuesliplist').val();
    $.ajax({
        type: 'POST',
        url: '../report/Resdetails',
        dataType: "json",
        data: {resid:resource,activityid:activityid,projectid:projectid,iowid:iowid},
        success: function(data){
            if(data.error=='No')
            {
                $('#IssueslipUnit'+id).val(data.result);
            }
        }
    });
});
$(document).on('click','#issueslipreport',function(){
    var error=0;
    $('.error').hide();
    /*if($('#issueslipactivity').val()=='none')
    {
        $('#issueslipactivity').next("span").html('Select Activity').show('slow');
        error=1;
    }
    if($('#issueslipquantity').val()=='')
    {
        $('#issueslipquantity').next("span").html('Enter Quantity').show('slow');
        error=1;
    }*/
    $('.issueslipdate').each(function(){
        var id=$(this).attr('data-id');
        if($(this).val()=='')
        {
            $("#issueslipdate"+id).next("span").html('Select Date').show('slow');
            error=1;
        }

    });
    /*$('.isslipwbsstructlist').each(function(){
        var id=$(this).attr('data-id');
        if($(this).val()=='none')
        {
            $("#isslipwbsstructlist"+id).next("span").html('Select Structure').show('slow');
            error=1;
        }

    });*/
    $('.issueslipiowlist').each(function(){
        var id=$(this).attr('data-id');
        if($(this).val()=='none')
        {
            $("#issueslipiowlist"+id).next("span").html('Select IOW').show('slow');
            error=1;
        }

    });
    $('.issueslipactivity').each(function(){
        var id=$(this).attr('data-id');
        if($(this).val()=='none')
        {
            $("#issueslipactivity"+id).next("span").html('Select Activity').show('slow');
            error=1;
        }

    });
    $('.IssueslipResource').each(function(){
        var id=$(this).attr('data-id');
        if($(this).val()=='none')
        {
            $("#IssueslipResource"+id).next("span").html('Select Resource').show('slow');
            error=1;
        }

    });
    $('.IssuedQuantity').each(function(){
        var id=$(this).attr('data-id');
        if($(this).val()=='')
        {
            $("#IssuedQuantity"+id).next("span").html('Enter Quantity').show('slow');
            error=1;
        }

    });


    if(error==0){
        $.ajax({
            type: 'POST',
            url: '../report/Saveissueslips',
            beforeSend : function(){
                $('#issueslipreport').attr("disabled", true);
            },
            dataType: "json",
            data: $( "#issueslipsform" ).serialize(),
            success: function(data){
                if(data.error=='No')
                {
                    $('#issueslipsform')[0].reset();
                    $('.remove_field').trigger('click');
                    //window.location.href = url;
                    $('#listissueslips').trigger('click') ;
                }

                $('#issueslipreport').attr("disabled", false);
            }
        });
    }
    else{
        //alert("You have to enter all values for reporting");
        return  false;
    }
});
$(document).on('click','.deleteissueslipbutton',function(){
    var slipid=$(this).val();
    var r = confirm("Are you sure you want to delete this Issue slip ?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../report/Deleteissueslip/',
            beforeSend : function(){
                $('#deleteissueslipbutton'+slipid).attr("disabled", true);
            },
            dataType: "json",
            data: {slipid:slipid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#issuesliprow'+slipid).remove();
                    $('#listissueslips').trigger('click');
                }

                $('#deleteissueslipbutton'+slipid).attr("disabled", false);
            }
        });
    }
    else {
        return false;
    }
});