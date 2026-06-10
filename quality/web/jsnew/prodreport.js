/**
 * Created by SolmindsDelli5 on 04-07-2019.
 */
$(document).on( "click", "#prodreport", function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#addprodreport').trigger('click');
});
$(function(){
    $('#listprodreport').click(function(){
        $('#prodreportsaddsection').slideUp('slow');
        $('#prodreportslistsection').slideDown('slow');
        $('#listprodreport').removeClass('btn-danger').addClass('btn-success');
        $('#addprodreport').removeClass('btn-success').addClass('btn-danger');
        //$('#projprodreport').show();
        $.ajax({
            type: 'POST',
            url: '../report/Prodreportsearch',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectid:$('#projprodreport').val(),name:$('#prodactname').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#prodreportsitems').html(data.result);
                    $('#prodreportstable').show();
                }
                $('.preloader').hide();
            }
        });
    });
    $('#projprodsearch').click(function(){
        $('#listprodreport').trigger('click');
    });
    $('#addprodreport').click(function(){
        $('#prodreportslistsection').slideUp('slow');
        $('#prodreportsaddsection').slideDown('slow');
        $('#addprodreport').removeClass('btn-danger').addClass('btn-success');
        $('#listprodreport').removeClass('btn-success').addClass('btn-danger');
        //$('#projprodreport').hide();
        $.ajax({
            type: 'POST',
            url: '../report/Getiow',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectid:$('#projprodreportslist').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('.prodreportiowlist').html(data.result);
                }
                $('.preloader').hide();
            }
        });
    });
});
$(document).on( "change","#projprodreportslist", function(){
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
                $('.prodreportiowlist').html(data.result);
            }
            $('.preloader').hide();
        }
    });
});
$(document).on( "change",".prodreportstructlist", function(){
    var id=$(this).attr('data-id');
    var structure=$(this).val();
    var projectid=$('#projprodreportslist').val();
    $.ajax({
        type: 'POST',
        url: '../report/Getiow',
        dataType: "json",
        data: {projectid:projectid,structureid:structure},
        success: function(data){
            if(data.error=='No')
            {
                $('#prodreportiowlist'+id).html(data.result);
            }
        }
    });
});
$(document).on( "change",".prodreportiowlist", function(){
    var id=$(this).attr('data-id');
    var iow=$(this).val();
    var projectid=$('#projprodreportslist').val();
    $.ajax({
        type: 'POST',
        url: '../report/GetProdactivity',
        dataType: "json",
        data: {projectid:projectid,iow:iow},
        success: function(data){
            if(data.error=='No')
            {
                $('#prodreportactivity'+id).html(data.result);
            }
        }
    });
});
$(document).on( "change",".prodreportactivity", function(){
    var id=$(this).attr('data-id');
    var activity=$(this).val();
    $.ajax({
        type: 'POST',
        url: '../report/Getactivitydetails',
        dataType: "json",
        data: {activityid:activity},
        success: function(data){
            if(data.error=='No')
            {
                $('#prodreportUnit'+id).val(data.actunit);
            }
        }
    });
});

$(document).on('click','#Prodreport',function(){
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
    $('.prodreportsdate').each(function(){
        var id=$(this).attr('data-id');
        if($(this).val()=='')
        {
            $("#prodreportsdate"+id).next("span").html('Select Date').show('slow');
            error=1;
        }

    });
    $('.prodreportstructlist').each(function(){
        var id=$(this).attr('data-id');
        if($(this).val()=='none')
        {
            $("#prodreportstructlist"+id).next("span").html('Select Structure').show('slow');
            error=1;
        }

    });
    $('.prodreportiowlist').each(function(){
        var id=$(this).attr('data-id');
        if($(this).val()=='none')
        {
            $("#prodreportiowlist"+id).next("span").html('Select IOW').show('slow');
            error=1;
        }

    });
    $('.prodreportactivity').each(function(){
        var id=$(this).attr('data-id');
        if($(this).val()=='none')
        {
            $("#prodreportactivity"+id).next("span").html('Select Activity').show('slow');
            error=1;
        }

    });
    $('.ProducedQuantity').each(function(){
        var id=$(this).attr('data-id');
        if($(this).val()=='' && $('#prodreportNos'+id).val()=='')
        {
            $("#ProducedQuantity"+id).next("span").html('Enter Quantity or Nos').show('slow');
            error=1;
        }

    });


    if(error==0){
        $.ajax({
            type: 'POST',
            url: '../report/SaveProdreport',
            beforeSend : function(){
                $('#Prodreport').attr("disabled", true);
            },
            dataType: "json",
            data: $( "#prodreportsform" ).serialize(),
            success: function(data){
                if(data.error=='No')
                {
                    $('#prodreportsform')[0].reset();
                    $('.remove_field').trigger('click');
                    //window.location.href = url;
                    $('#listprodreport').trigger('click') ;
                }

                $('#Prodreport').attr("disabled", false);
            }
        });
    }
    else{
        //alert("You have to enter all values for reporting");
        return  false;
    }
});
$(document).on('click','.deleteprodreportbutton',function(){
    var reportid=$(this).val();
    var r = confirm("Are you sure you want to delete this Report ?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../report/DeleteProdreport/',
            beforeSend : function(){
                $('#deleteprodreportbutton'+reportid).attr("disabled", true);
            },
            dataType: "json",
            data: {reportid:reportid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#prodreportrow'+reportid).remove();
                    //$('#listissueslips').trigger('click');
                }

                $('#deleteprodreportbutton'+reportid).attr("disabled", false);
            }
        });
    }
    else {
        return false;
    }
});