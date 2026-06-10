/**
 * Created by SolmindsDelli5 on 01-03-2018.
 */
$(document).on( "click", "#advance", function(){

    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }
    $('#listcashadvance').trigger('click') ;
});
$(function() {

    $('#listcashadvance').click(function(){

        //$('#productaddsection').slideUp('slow');// slide down the project listing div

        $('#cashadvancelistsection').slideDown('slow');// slide down the project listing div
        $('#appadvancelistsection').slideUp('slow');// slide down the project listing div
        $('#cashadvanceaddsection').slideUp('slow');
        $('#hoadvanceaddsection').slideUp('slow');
        $('#cashadvanceviewsection').slideUp('slow');

        $('#listcashadvance').removeClass('btn-danger').addClass('btn-success');

        $('#addcashadvance').removeClass('btn-success').addClass('btn-danger');
        $('#listappadvance').removeClass('btn-success').addClass('btn-danger');

        $.ajax({

            type: 'POST',

            url: '../FinanceRequests/Cashadvancesearch',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {name:$('#searchcashadvance').val()},

            success: function(data){

                if(data.error=='No')

                {

                    $('#cashadvanceitems').html(data.result);

                    $('#casadvancetable').show();

                }

                else

                {

                    alert(data.errortext);

                }

                $('.preloader').hide();

            }

        });

    });
    $('#listappadvance').click(function(){

        //$('#productaddsection').slideUp('slow');// slide down the project listing div

        $('#cashadvancelistsection').slideUp('slow');// slide down the project listing div
        $('#appadvancelistsection').slideDown('slow');// slide down the project listing div
        $('#cashadvanceaddsection').slideUp('slow');
        $('#hoadvanceaddsection').slideUp('slow');
        $('#cashadvanceviewsection').slideUp('slow');

        $('#listappadvance').removeClass('btn-danger').addClass('btn-success');

        $('#addcashadvance').removeClass('btn-success').addClass('btn-danger');
        $('#listcashadvance').removeClass('btn-success').addClass('btn-danger');

        $.ajax({

            type: 'POST',

            url: '../FinanceRequests/Approvedadvance',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            //data: {name:$('#searchcashadvance').val()},

            success: function(data){

                if(data.error=='No')

                {

                    $('#appadvanceitems').html(data.result);

                    $('#appadvancetable').show();

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
    $('#addcashadvance').click(function(){
        $('#cashadvancelistsection').slideUp('slow');
        $('#appadvancelistsection').slideUp('slow');
        $('#cashadvanceviewsection').slideUp('slow');
        $('#addcashadvance').removeClass('btn-danger').addClass('btn-success');
        $('#listcashadvance').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../FinanceRequests/Getcashbalance',
            dataType: "json",
            success: function(data){
                if(data.error=='No')
                {
                    if(data.type==0){
                        $('#cashadvanceaddsection').slideDown('slow');
                        $('#hoadvanceaddsection').slideUp('slow');
                        //$('#cashadvanceaddsection').show();
                        //$('#hoadvanceaddsection').hide();
                    }
                    else {
                        $('#cashadvanceaddsection').slideUp('slow');
                        $('#hoadvanceaddsection').slideDown('slow');
                        //$('#cashadvanceaddsection').hide();
                        //$('#hoadvanceaddsection').show();
                    }
                    $('#cashadvopenbal').html(data.result);
                    $('#hoadvopenbal').html(data.result);
                }
            }
        });
    });
});

$(document).on( "click",".viewcashadvance", function(){
    var reqid=$(this).val();
    $('#cashadvancelistsection').slideUp('slow');
    $('#appadvancelistsection').slideUp('slow');
    $('#cashadvanceaddsection').slideUp('slow');
    $('#cashadvanceviewsection').slideDown('slow');
    $.ajax({

        type: 'POST',

        url: '../FinanceRequests/Editdadvance',

        beforeSend : function(){

            $('.preloader').show();

        },

        dataType: "json",

        data: {reqid:reqid},

        success: function(data){

            if(data.error=='No')

            {

                $('#viewadvanceitems').html(data.result);
                $('#editadvanceprojlist').html(data.placelist);
                $('#editschedule').html(data.userlist);
                $('#editadvancedate').val(data.reqdate);
                $('#editcashadvancetable').show();

            }

            else

            {

                alert(data.errortext);

            }

            $('.preloader').hide();

        }

    });
});
/*$(document).on( "click","#editaddmore", function(){
    alert('sc')
});*/
$(document).on( "change","#advanceprojectlist", function(){
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
                $('#advanceprocesslist').html(data.result);
            }
            //$('.preloader').hide();
        }
    });
});
$(document).on( "change","#advanceprocesslist", function(){
    var project=$('#advanceprojectlist').val();
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
                $('#advanceactivitylist').html(data.result);
            }
            //$('.preloader').hide();
        }
    });
});

$(document).on('change','.advanceamount',function(){
    var totalamount=0;
    $('.advanceamount').each(function(){
        totalamount+=$(this).val()*1;
    });
    $('#totaladv').html(totalamount);
});

$(document).on('change','.hoadvanceamount',function(){
    var totalamount=0;
    $('.hoadvanceamount').each(function(){
        totalamount+=$(this).val()*1;
    });
    $('#hototaladv').html(totalamount);
});

$(document).on('change','.updateadvanceamount',function(){
    var totalamount=0;
    $('.updateadvanceamount').each(function(){
        totalamount+=$(this).val()*1;
    });
    $('.newadvanceamount').each(function(){
        totalamount+=$(this).val()*1;
    });
    $('#edittotaladv').html(totalamount);
});

$(document).on('change','.newadvanceamount',function(){
    var totalamount=0;
    $('.updateadvanceamount').each(function(){
        totalamount+=$(this).val()*1;
    });
    $('.newadvanceamount').each(function(){
        totalamount+=$(this).val()*1;
    });
    $('#edittotaladv').html(totalamount);
});
/*$(document).on('click','#cashadvancedraft',function(){
    $.ajax({
        type: 'POST',
        url: '../Projects/SaveCashadvance',
        dataType: "json",
        data: $( "#cashadvanceform" ).serialize(),
        success: function(data){
            if(data.error=='No')
            {
                $('#cashadvanceform')[0].reset();
                $('#listcashadvance').trigger('click') ;
            }
        }
    });
});*/
$(document).on('click','.cashadvancecreate',function(){
    var mode=$(this).val();
    //alert(mode)
    var error=0;
    $('.error').hide();
    if($('#advancedate').val()=='')
    {
        $('#advancedate').next("span").html('Select Date').show('slow');
        error=1;
    }
    if($('#advanceprojlist').val()=='none')
    {
        $('#advanceprojlist').next("span").html('Select Place').show('slow');
        error=1;
    }
    if($('#schedule').val()=='none')
    {
        $('#schedule').next("span").html('Select Username').show('slow');
        error=1;
    }
    $('.advanceamount').each(function(){
        var id=$(this).attr('data-id');
        if(!$.isNumeric($('#advanceamount'+id).val()))
        {
            $('#advanceamount'+id).next("span").html('Amount must be number').show('slow');
            error=1;
        }
        if($('#advanceamount'+id).val()=='')
        {
            $('#advanceamount'+id).next("span").html('Enter Amount').show('slow');
            error=1;
        }
    });
    $('.schedule').each(function(){
        var id=$(this).attr('data-id');
        if($('#schedule'+id).val()=='none')
        {
            $('#schedule'+id).next("span").html('Select Payment made to').show('slow');
            error=1;
        }
    });
    $('.purpose').each(function(){
        var id=$(this).attr('data-id');
        if($('#purpose'+id).val()=='')
        {
            $('#purpose'+id).next("span").html('Enter Purpose').show('slow');
            error=1;
        }
    });
    if (error==0){
        $.ajax({
            type: 'POST',
            url: '../Projects/SaveCashadvance',
            beforeSend : function(){
                $('.cashadvancecreate').attr("disabled", true);
            },
            dataType: "json",
            data: $( "#cashadvanceform" ).serialize()+"&mode="+mode,
            success: function(data){
                if(data.error=='No')
                {
                    $('.cashadvancecreate').attr("disabled", false);
                    $('#cashadvanceform')[0].reset();
                    $('#totaladv').html('');
                    $('.remove_field').trigger('click');
                    $('#listcashadvance').trigger('click') ;
                }
            }
        });
    }
    else {
        return false;
    }

});

$(document).on('click','.hoadvancecreate',function(){
    var mode=$(this).val();
    //alert(mode)
    var error=0;
    $('.error').hide();
    if($('#hoadvancedate').val()=='')
    {
        $('#hoadvancedate').next("span").html('Select Date').show('slow');
        error=1;
    }
    /*if($('#advanceprojlist').val()=='none')
    {
        $('#advanceprojlist').next("span").html('Select Place').show('slow');
        error=1;
    }*/
    /*if($('#schedule').val()=='none')
    {
        $('#schedule').next("span").html('Select Username').show('slow');
        error=1;
    }*/
    $('.hoadvanceamount').each(function(){
        var id=$(this).attr('data-id');
        if(!$.isNumeric($('#hoadvanceamount'+id).val()))
        {
            $('#hoadvanceamount'+id).next("span").html('Amount must be number').show('slow');
            error=1;
        }
        if($('#hoadvanceamount'+id).val()=='')
        {
            $('#hoadvanceamount'+id).next("span").html('Enter Amount').show('slow');
            error=1;
        }
    });
    $('.hoschedule').each(function(){
        var id=$(this).attr('data-id');
        if($('#hoschedule'+id).val()=='none')
        {
            $('#hoschedule'+id).next("span").html('Select Accounthead').show('slow');
            error=1;
        }
    });
    $('.hopurpose').each(function(){
        var id=$(this).attr('data-id');
        if($('#hopurpose'+id).val()=='')
        {
            $('#hopurpose'+id).next("span").html('Enter Purpose').show('slow');
            error=1;
        }
    });
    if (error==0){
        $.ajax({
            type: 'POST',
            url: '../Projects/SaveCashadvance',
            beforeSend : function(){
                $('.hoadvancecreate').attr("disabled", true);
            },
            dataType: "json",
            data: $( "#hoadvanceform" ).serialize()+"&mode="+mode,
            success: function(data){
                if(data.error=='No')
                {
                    $('.hoadvancecreate').attr("disabled", false);
                    $('#hoadvanceform')[0].reset();
                    $('#hototaladv').html('');
                    $('.remove_field').trigger('click');
                    $('#listcashadvance').trigger('click') ;
                }
            }
        });
    }
    else {
        return false;
    }

});

$(document).on('click','.cashadvanceedit',function(){
    //alert('yes')
    var mode=$(this).val();
    //alert(mode)
    var error=0;
    $('.error').hide();
    if($('#editadvanceprojlist').val()=='none')
    {
        $('#editadvanceprojlist').next("span").html('Select Place').show('slow');
        error=1;
    }
    if($('#editschedule').val()=='none')
    {
        $('#editschedule').next("span").html('Select Username').show('slow');
        error=1;
    }
    $('.updateadvanceamount').each(function(){
        var id=$(this).attr('data-id');
        if(!$.isNumeric($('#updateadvanceamount'+id).val()))
        {
            $('#updateadvanceamount'+id).next("span").html('Amount must be number').show('slow');
            error=1;
        }
        if($('#updateadvanceamount'+id).val()=='')
        {
            $('#updateadvanceamount'+id).next("span").html('Enter Amount').show('slow');
            error=1;
        }
    });
    $('.newadvanceamount').each(function(){
        var id=$(this).attr('data-id');
        if(!$.isNumeric($('#newadvanceamount'+id).val()))
        {
            $('#newadvanceamount'+id).next("span").html('Amount must be number').show('slow');
            error=1;
        }
        if($('#newadvanceamount'+id).val()=='')
        {
            $('#newadvanceamount'+id).next("span").html('Enter Amount').show('slow');
            error=1;
        }
    });
    $('.schedule').each(function(){
        var id=$(this).attr('data-id');
        if($('#schedule'+id).val()=='none')
        {
            $('#schedule'+id).next("span").html('Select Payment made to').show('slow');
            error=1;
        }
    });
    $('.updatepurpose').each(function(){
        var id=$(this).attr('data-id');
        if($('#updatepurpose'+id).val()=='')
        {
            $('#updatepurpose'+id).next("span").html('Enter Purpose').show('slow');
            error=1;
        }
    });
    $('.newpurpose').each(function(){
        var id=$(this).attr('data-id');
        if($('#newpurpose'+id).val()=='')
        {
            $('#newpurpose'+id).next("span").html('Enter Purpose').show('slow');
            error=1;
        }
    });
    if (error==0){
        $.ajax({
            type: 'POST',
            url: '../Projects/UpdateCashadvance',
            beforeSend : function(){
                $('.cashadvanceedit').attr("disabled", true);
            },
            dataType: "json",
            data: $( "#cashadvanceviewform" ).serialize()+"&mode="+mode,
            success: function(data){
                if(data.error=='No')
                {
                    $('.cashadvanceedit').attr("disabled", false);
                    $('#cashadvanceviewform')[0].reset();
                    $('#listcashadvance').trigger('click') ;
                }
            }
        });
    }
    else {
        return false;
    }
});
$(document).on('click','.deletecashadvance',function(){
    var cashadvanceid=$(this).val();
    var r = confirm("Are you sure you want to delete this Advance?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../FinanceRequests/CashadvanceDelete',
            beforeSend : function(){
                $('#deletecashadvance'+cashadvanceid).attr("disabled", true);
            },
            dataType: "json",
            data: {cashadvance:cashadvanceid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#cashadvancerow'+cashadvanceid).remove();
                    //$('#listcashadvance').trigger('click') ;
                }
                else
                {
                    alert(data.errortext);
                }

                $('#deletecashadvance'+cashadvanceid).attr("disabled", false);
            }
        });
    }
});
$(document).on('click','.createcashbill',function(){
    var cashadvanceid=$(this).val();
    $('#advanceid').val(cashadvanceid);
    $('#workorder').trigger('click');
});