$(document).on( "click", "#billing", function(){
    //$('.acc_container').slideUp();
    $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
    //$(this).toggleClass('active').next().slideDown();
    $('#billing').addClass('active').next('.acc_container').slideDown();
    $('#measure_book').trigger('click');
});

/*$(function(){
    $('#measure_book').click(function(){
        $('#measurement-book-list').slideDown('slow');
        $('#measure_book').removeClass('btn-danger').addClass('btn-success');
        $('#abstract').removeClass('btn-danger').addClass('btn-danger');
        $('#bill').removeClass('btn-danger').addClass('btn-danger');
    });
    
});*/

$(document).on( "click", "#measure_book", function(){
        $('#abstract-list').slideUp('slow');
        $('#bill-list').slideUp('slow');
        $('#billed-list').slideUp('slow');
        $('#measurement-book-list').slideDown('slow');
        $('#measure_book').removeClass('btn-danger').addClass('btn-success');
        $('#abstract').removeClass('btn-success').addClass('btn-danger');
        $('#bill').removeClass('btn-success').addClass('btn-danger');
        $('#billed-listing').removeClass('btn-success').addClass('btn-danger');
        $('#measurementdate').val('');
        $('#boqslno').val('none');
        $('#boq-item').html('');
        $('#boq-notes').val('');
        $('#boq-itemhead').val('');
        $('#item-details').hide();
});

$(document).on( "click", "#abstract", function(){
        $('#measurement-book-list').slideUp('slow');
        $('#billed-list').slideUp('slow');
        $('#abstract-list').slideDown('slow');
        $('#bill-list').slideUp('slow');
        $('#abstract').removeClass('btn-danger').addClass('btn-success');
        $('#measure_book').removeClass('btn-success').addClass('btn-danger');
        $('#bill').removeClass('btn-success').addClass('btn-danger');
        $('#billed-listing').removeClass('btn-success').addClass('btn-danger');
    
        $.ajax({

            type: 'POST',

            url: '../report/Abstractlist',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            success: function(data){

                if(data.error=='No')

                {
                    $('#abstractitemslist').html(data.result);
                }

                $('.preloader').hide();

            }

        });
});

$(document).on( "click", "#bill", function(){
        $('#measurement-book-list').slideUp('slow');
        $('#abstract-list').slideUp('slow');
        $('#billed-list').slideUp('slow');
        $('#bill-list').slideDown('slow');
        $('#bill').removeClass('btn-danger').addClass('btn-success');
        $('#measure_book').removeClass('btn-success').addClass('btn-danger');
        $('#abstract').removeClass('btn-success').addClass('btn-danger');
        $('#billed-listing').removeClass('btn-success').addClass('btn-danger');
        $('#backbutn').hide();
        $.ajax({

            type: 'POST',

            url: '../report/Billlist',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            success: function(data){

                if(data.error=='No')

                {
                    $('#billitemslist').html(data.result);
                }

                $('.preloader').hide();

            }

        });
});

$(document).on( "change",".slno", function(){
    var slno = $('#boqslno').val();
    var date = $('#measurementdate').val();
    $.ajax({

        type: 'POST',

        url: '../report/Selectedboq',

        beforeSend : function(){

            $('.preloader').show();

        },

        dataType: "json",

        data: {boqid:slno,date:date},

        success: function(data){

            if(data.error=='No')

            {

                $('#boq-item').html(data.boqitem);
                $('#boqunit').html(data.boqunit);
                $('#item-details').show();
                $('#boqitemslist').html(data.result);
                $('#boq-notes').val(data.notes);
                $('#boq-itemhead').val(data.itemhead);
                $('#reprt-btn').html(data.reprtbtn);
            }

            $('.preloader').hide();

        }

    });
});  

$(document).on( "change",".prjctid", function(){
    var prjctid = $('#prjctid').val();
    $.ajax({

        type: 'POST',

        url: '../report/Selectedproject',

        beforeSend : function(){

            $('.preloader').show();

        },

        dataType: "json",

        data: {prjctid:prjctid},

        success: function(data){

            if(data.error=='No')

            {

                $('#boqslno').html(data.result);
            }

            $('.preloader').hide();

        }

    });
});

/*$(document).on( "click", ".addnewrow", function(){
    var oldslno = $(this).attr('data-id');
    var slno = $('#slno_'+oldslno).html();
    var date = $('#measurementdate').val();
    var boqid = $('#boqslno').val();
    
    var length = $('#length_'+oldslno).val();
    var error = 0;
    if(length == ''){
        $('#length_'+oldslno).next("span").html('Enter length').show('slow');
        error=1;
    }
    else{
        $('#length_'+oldslno).next("span").html('Enter length').hide('slow');
        error=0;
    }
    if(error==0){
        $.ajax({

            type: 'POST',

            url: '../report/Selectedboq',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {newslno:slno,date:date,boqid:boqid},

            success: function(data){

                if(data.error=='No')

                {
                    $('#removerow_'+slno).show();
                    $('#addnewrow_'+slno).hide();
                    //$('#boq-item').html(data.boqitem);
                    //$('#boqunit').html(data.boqunit);
                    $('#item-details').show();
                    $('#boqitemslist').append(data.result);
                }

                $('.preloader').hide();

            }

        });
    }
});*/

$(document).on("change", ".measurement-validate", function() {
    var id = $(this).attr("data-id");
    var number1 = $('#number1_'+id).val();
    var number2 = $('#number2_'+id).val();
    var number3 = $('#number3_'+id).val();
    var length = $('#length_'+id).val();
    var width = $('#width_'+id).val();
    var depth = $('#depth_'+id).val();
    if(number1!='' || number2!='' || number3!='' || length!='' || width!='' || depth!=''){
        if(number1==0 && number1==''){
            var number_1 = 1;
        }
        else{
            var number_1 = number1;
        }
        if(number2==0 && number2==''){
            var number_2 = 1;
        }
        else{
            var number_2 = number2;
        }
        if(number3==0 && number3==''){
            var number_3 = 1;
        }
        else{
            var number_3 = number3;
        }
        if(length==0 && length==''){
            var length_1 = 1;
        }
        else{
            var length_1 = length;
        }
        if(width==0 && width==''){
            var width_1 = 1;
        }
        else{
            var width_1 = width;
        }
        if(depth==0 && depth==''){
            var depth_1 = 1;
        }
        else{
            var depth_1 = depth;
        }
        var quantity = (number_1 * number_2 * number_3 * length_1 * width_1 * depth_1);
        /*$('#quantity_'+id).val(quantity.toFixed(2));
        var total = 0;
        $('.quantity').each(function (index, element) {
            total = total + parseFloat($(element).val());
        });*/
        $('#quantity_'+id).html(quantity.toFixed(3));
        $('#actualqty_'+id).val(quantity.toFixed(3));
    }
});

$(document).on("click", "#measurementrprtbtn", function() {   
    var date = $('#measurementdate').val();
    var error = 0;
    if(date == ''){
        $('#measurementdate').next("span").html('Select Date').show('slow').delay(3000).fadeOut();
        error=1;
    }
    $('.length').each(function(){
        var id = $(this).attr("data-id");
        var length = $('#length_'+id).val();
        if(length == ''){
            $('#length_'+id).next("span").html('Enter Length').show('slow');
            error=1;
        }
    });
    
    if(error==0){
        $.ajax({

            type: 'POST',

            url: '../report/Measurementbook',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: $('#measurement-form').serialize(),

            success: function(data){

                if(data.error=='No')

                {
                    $('.preloader').hide();
                    $('#success-message').html('Reported successfully').show().delay(3000).fadeOut();
                    $('#measure_book').trigger("click");     
                }

            }

        });
    }
});

$(document).on("click", "#updatemeasurementrprtbtn", function() { 
    var date = $('#measurementdate').val();
    var error = 0;
    if(date == ''){
        $('#measurementdate').next("span").html('Select Date').show('slow').delay(3000).fadeOut();
        error=1;
    }
    
    $('.length').each(function(){
        var id = $(this).attr("data-id");
        var length = $('#length_'+id).val();
        if(length == ''){
            $('#length_'+id).next("span").html('Enter Length').show('slow');
            error=1;
        }
    });
    
    if(error==0){
        $.ajax({

            type: 'POST',

            url: '../report/Updatemeasurementbook',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: $('#measurement-form').serialize(),

            success: function(data){

                if(data.error=='No')

                {
                    $('.preloader').hide();
                    $('#success-message').html('Updated successfully').show().delay(3000).fadeOut();
                    $('#measure_book').trigger("click");    
                }

            }

        });
    }
});

$(document).on( "change",".datepicker", function(){
    var slno = $('#boqslno').val();
    var date = $('#measurementdate').val();
    if(slno!='none' && date!=''){
        $.ajax({

            type: 'POST',

            url: '../report/Selectedboq',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {boqid:slno,date:date},

            success: function(data){

                if(data.error=='No')

                {

                    $('#boq-item').html(data.boqitem);
                    $('#boqunit').html(data.boqunit);
                    $('#item-details').show();
                    $('#boqitemslist').html(data.result);
                    $('#boq-notes').val(data.notes);
                    $('#boq-itemhead').val(data.itemhead);
                    $('#reprt-btn').html(data.reprtbtn);
                }

                $('.preloader').hide();

            }

        });
    }
}); 

$(document).on( "click", ".removerow", function(){
    var id = $(this).attr('data-id');
    $('#clipper-row_'+id).remove();

});

$(document).on( "change",".billper", function(){
    var boqid=$(this).attr('data-id');
    var billamount=$('#bill_amount'+boqid).val();
    var billper=$('#billper'+boqid).val();
    var netamount=(billamount * billper)/100;
    //alert(netamount)
    $('#net_amount'+boqid).html(netamount.toFixed(2));
    $('#net_amountval'+boqid).val(netamount);
    var total_netamount=0;
    $('.net_amountval').each(function () {
        total_netamount+=$(this).val()*1;
    });
    //alert(total_netamount)
    $('#netamnttotal').html(total_netamount.toFixed(2));
});

$(document).on("click", "#billrprtbtn", function() {  
    var billdate = $('#billdate').val();
    var billno = $('#billno').val();
    var error = 0;
    if(billdate == ''){
        $('#billdate').next("span").html('Select Date').show('slow').delay(3000).fadeOut();
        error=1;
    }
    if(billno == ''){
        $('#billno').next("span").html('Enter BillNo').show('slow').delay(3000).fadeOut();
        error=1;
    }
    
    if(error==0){
        $.ajax({

            type: 'POST',

            url: '../report/Measurementbill',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: $('#bill-form').serialize(),

            success: function(data){

                if(data.error=='No')

                {
                    $('.preloader').hide();
                    $('#billno').val('');
                    $('#billdate').val('');
                    $('#bill').trigger("click");     
                }

            }

        });
    }
});  

$(document).on("click", "#billed-listing", function() { 
    $('#abstract-list').slideUp('slow');
    $('#bill-list').slideUp('slow');
    $('#measurement-book-list').slideUp('slow');
    $('#billed-list').slideDown('slow');
    $('#billed-listing').removeClass('btn-danger').addClass('btn-success');
    $('#bill').removeClass('btn-success').addClass('btn-danger');
    $('#measure_book').removeClass('btn-success').addClass('btn-danger');
    $('#abstract').removeClass('btn-success').addClass('btn-danger');
    
    $.ajax({

        type: 'POST',

        url: '../report/Billedlist',

        beforeSend : function(){

            $('.preloader').show();

        },

        dataType: "json",

        success: function(data){

            if(data.error=='No')

            {
                $('.preloader').hide();
                $('#back-button').hide();
                $('#billed-list').html(data.result);     
            }

        }

    });
});  

$(document).on("click", ".viewbilled", function() { 
    var id = $(this).attr('data-id');
    
    $.ajax({

        type: 'POST',

        url: '../report/Viewbilled',

        beforeSend : function(){

            $('.preloader').show();

        },

        dataType: "json",

        data: {mbookid:id},

        success: function(data){

            if(data.error=='No')

            {
                $('.preloader').hide();
                $('#back-button').show();
                $('#billed-list').html(data.result); 
            }

        }

    });
});

$(document).on("click", "#backbutn", function() { 
    $('#billed-listing').trigger("click"); 
});   