/**
 * Created by SolmindsDelli5 on 09-11-2018.
 */
/**
 * Created by SolmindsDelli5 on 26-07-2018.
 */
$(document).on( "click", "#financedocuments", function(){   

    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    $('#financeinboxdocsearch').trigger('click');
    $('#financeoutboxdocsearch').trigger('click');

});

$(document).on( "click", "#financeadddoc", function(){

    var url='../doccumentManager/Createdoc?mode=finance';

    window.location.href = url;
});

$(document).on( "click", "#financeuploaddoc", function(){

    var url='../doccumentManager/Uploaddoc?mode=finance';

    window.location.href = url;

});

$(function(){
    $('#financelistddoc').click(function(){
        $('#financeinboxdocsearch').trigger('click');
        $('#financeoutboxdocsearch').trigger('click');
    });
    
    $('#financeinboxdocsearch').click(function(){
        $('#financearchiveddoclist').slideUp('slow');
        $('#financedoclistsection').slideDown('slow');// slide down the project listing div
        $('#financelistddoc').removeClass('btn-danger').addClass('btn-success');
        $('#financearchiveddoc').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../DoccumentManager/FinanceInboxDocsearch',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {subject:$('#searchinboxdocument').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#financedocitems').html(data.result);
                    $('#financedoctable').show();
                }
                $('.preloader').hide();
            }
        });
    });
    $('#financeoutboxdocsearch').click(function(){
        $.ajax({
            type: 'POST',
            url: '../DoccumentManager/FinanceOutboxDocsearch',
            dataType: "json",
            data: {subject:$('#searchoutboxdocument').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#outboxdocitems').html(data.result);
                    $('#outboxdoctable').show();
                }
                $('.preloader').hide();
            }
        });
    });
    $('#funtiondocsarch').click(function(){
        $('#financearchiveddoc').trigger('click');
    });

});
$(document).on( "click", ".assigndocument", function(){
    var idval=$(this).val();
    var type=$(this).attr('data-id');
    var error=0;
    $('.error').hide();
    if(error==0){
        $.ajax({
            type: 'POST',
            url: '../doccumentManager/AssignDoc',
            beforeSend : function(){
                $('#assigndocument'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {docid:idval,type:type},
            success: function(data){
                if(data.error=='No')
                {
                    $('#financeinboxdocsearch').trigger('click');
                }
                $('#assigndocument'+idval).attr("disabled", false);
            }
        });
    }

});

$(document).on( "click", "#forwarddoc", function(){
    $('#fwddoctype').val($(this).attr('data-type'));
    $('#fwddocid').val($(this).val());
    $('#forwarddocument')[0].reset();
    $('#fwdsuccesinfo').hide();
    $('#fwderrorinfo').hide();
});

$(document).on( "click", "#documentfwd", function(){
    var department=$('#functionlist').val();
    var remarks=$('#remarks').val();
    var type=$('#fwddoctype').val();
    var docid=$('#fwddocid').val();
    var error=0;
    $('.error').hide();

    if($('#functionlist').val()=='none')
    {
        $('#functionlist').next("span").html('Select Function').show('slow');
        error=1;
    }
    if($('#remarks').val()=='')
    {
        $('#remarks').next("span").html('Enter Remarks').show('slow');
        error=1;
    }

    if (error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../DoccumentManager/Forwarddoc',
            beforeSend : function(){
                $('.mailloader').show();
            },
            dataType: "json",
            data: {department:department,remarks:remarks,type:type,docid:docid},
            success: function(data){
                if(data.error=='No')
                {
                    $('.mailloader').hide();
                    $('#fwdsuccesinfo').show();
                    $('#fwdsuccesinfo').html('<span style="text-align: center">Document forwarded to '+data.depname+'.</span>');
                    $('#fwderrorinfo').hide();
                }
                else {
                    $('.mailloader').hide();
                    $('#fwderrorinfo').show();
                    $('#fwderrorinfo').html('<span style="text-align: center">'+data.errortext+'</span>');
                    $('#fwdsuccesinfo').hide();
                }
                setTimeout(function(){
                    $('#forwarddocModel').modal('toggle');
                }, 3000);

            }
        });
    }
});
$(document).on( "click", "#email", function(){
    $('#type').val($(this).attr('data-type'));
    $('#docid').val($(this).val());
    $('#emaildocument')[0].reset();
    $('#succesinfo').hide();
    $('#errorinfo').hide();
});
$(document).on( "click", "#sendemail", function(){
    var email=$('#emailid').val();
    var subject=$('#subject').val();
    var body=$('#body').val();
    var type=$('#type').val();
    var docid=$('#docid').val();
    var error=0;
    $('.error').hide();

    if($('#emailid').val()=='')
    {
        $('#emailid').next("span").html('Enter Email address').show('slow');
        error=1;
    }
    if($('#subject').val()=='')
    {
        $('#subject').next("span").html('Enter Subject').show('slow');
        error=1;
    }
    if($('#body').val()=='')
    {
        $('#body').next("span").html('Enter Body').show('slow');
        error=1;
    }
    if (error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../DoccumentManager/Sendemail',
            beforeSend : function(){
                $('.mailloader').show();
            },
            dataType: "json",
            data: {email:email,subject:subject,body:body,type:type,docid:docid},
            success: function(data){
                if(data.error=='No')
                {
                    $('.mailloader').hide();
                    $('#succesinfo').show();
                    $('#succesinfo').html('<span style="text-align: center">'+data.errortext+'</span>');
                    $('#errorinfo').hide();
                }
                else {
                    $('.mailloader').hide();
                    $('#errorinfo').show();
                    $('#errorinfo').html('<span style="text-align: center">'+data.errortext+'</span>');
                    $('#succesinfo').hide();
                }
                setTimeout(function(){
                    $('#emailModel').modal('toggle');
                }, 3000);

            }
        });
    }


});
$(document).on('click','.sendforapp',function(){
    var docid=$(this).val();
    var type=$(this).attr('data-type');
    $.ajax({
        type: 'POST',
        url: '../doccumentManager/Sendforapproval',
        dataType: "json",
        data: {docid:docid,type:type},
        success: function(data){
            if(data.error=='No')
            {
                $('#financeoutboxdocsearch').trigger('click');
            }
        }
    });
});

$(document).on( "click", ".deletedoc", function(){
    var idval=$(this).val();
    var type=$(this).attr('data-id');
    var listtype=$(this).attr('data-type');
    var r = confirm("Are you sure you want to delete this Document ?");
    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../DoccumentManager/Deletedoc',
            beforeSend : function(){
                $('#deletedoc'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {docid:idval,type:type},
            success: function(data){
                if(data.error=='No')
                {
                    if (listtype=='inbox'){
                        $('#inboxrow'+idval).remove();
                        $('#financeinboxdocsearch').trigger('click');
                    }
                    else {
                        $('#outboxrow'+idval).remove();
                        $('#financeoutboxdocsearch').trigger('click');
                    }
                }

                $('#deletedoc'+idval).attr("disabled", false);
            }
        });

    } else {
        return false;
    }

});

$(document).on( "click", ".deletetechdoc", function(){

    var idval=$(this).val();

    var type=$(this).attr('data-id');

    var r = confirm("Are you sure you want to delete this Document ?");

    if (r == true) {



        $.ajax({

            type: 'POST',

            url: '../DoccumentManager/Deletedoc',

            beforeSend : function(){

                $('#deletetechdoc'+idval).attr("disabled", true);

            },

            dataType: "json",

            data: {docid:idval,type:type},

            success: function(data){

                if(data.error=='No')

                {

                    $('#drawingrow'+idval).remove();

                    $('#financearchiveddoc').trigger('click');

                }



                $('#deletetechdoc'+idval).attr("disabled", false);

            }

        });



    } else {

        return false;

    }



});

$(document).on( "click", "#financearchiveddoc", function(){
    $('#financedoclistsection').slideUp('slow');
    $('#financearchiveddoclist').slideDown('slow');
    $('#financearchiveddoc').removeClass('btn-danger').addClass('btn-success');
    $('#financelistddoc').removeClass('btn-success').addClass('btn-danger');
    $.ajax({

        type: 'POST',

        url: '../DoccumentManager/FinanceArchivedDocs',

        beforeSend : function(){

            $('.preloader').show();

        },

        dataType: "json",

        data: {document:$('#funtiondocstext').val()},

        success: function(data){

            if(data.error=='No')

            {

                $('#funcspan').html(data.fuctname);
                $('#funtiondocsitems').html(data.result);

                $('#funtiondocstable').show();

            }

            $('.preloader').hide();

        }

    });

});
