/**
 * Created by SolmindsDelli5 on 26-07-2018.
 */
$(document).on( "click", ".viewOpdocuments", function(){

    $('#rproject').removeClass('active').next().slideUp();

    $('#opprojdocuments').addClass('active').next('.acc_container').slideDown();

    var id= $(this).val();

    $('#selectedProjectId').val(id);

    $('#opprojdocprojectname').html(getProjectname(id));

    $('#opprojdocumentsearch').trigger('click');
    $('#outboxdocumentsearch').trigger('click');

});

$(document).on( "click", "#opadddoc", function(){

    var projectid=$('#selectedProjectId').val();

    //var url='../doccumentManager/Createdoc?project='+projectid+'&function=1&mode=operations';
    var url='../doccumentManager/Createdoc?project='+projectid+'&mode=operations';

    window.location.href = url;

});

$(document).on( "click", "#opuploaddoc", function(){

    var projectid=$('#selectedProjectId').val();

    //var url='../doccumentManager/Uploaddoc?project='+projectid+'&function=1&mode=operations';
    var url='../doccumentManager/Uploaddoc?project='+projectid+'&mode=operations';

    window.location.href = url;

});

$(function(){
    $('#oplistddoc').click(function(){
        $('#opprojdocumentsearch').trigger('click');
        $('#outboxdocumentsearch').trigger('click');
    });

    $('#opprojdocumentsearch').click(function(){
        $('#opprojdoclistsection').slideDown('slow');// slide down the project listing div
        $('#oparchiveddoclist').slideUp('slow');
        $('#oplistddoc').removeClass('btn-danger').addClass('btn-success');
        $('#oparchiveddoc').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../DoccumentManager/InboxDocsearch',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {project:$('#selectedProjectId').val(),function:$('#opprojfunction').val(),subject:$('#searchinboxdocument').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#opprojdocitems').html(data.result);
                    $('#opprojdoctable').show();
                }
                $('.preloader').hide();
            }
        });
    });
    $('#outboxdocumentsearch').click(function(){
        $.ajax({
            type: 'POST',
            url: '../DoccumentManager/OutboxDocsearch',
            dataType: "json",
            data: {project:$('#selectedProjectId').val(),subject:$('#searchoutboxdocument').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#outboxdocitems').html(data.result);
                    $('#outboxdoctable').show();
                }
            }
        });
    });
    $('#funtiondocsarch').click(function(){
        $('#oparchiveddoc').trigger('click');
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
                    $('#opprojdocumentsearch').trigger('click');
                }
                $('#assigndocument'+idval).attr("disabled", false);
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
$(document).on( "click", "#forwarddoc", function(){
    $('#fwdtype').val($(this).attr('data-type'));
    $('#fwddocid').val($(this).val());
    $('#opfwddocument')[0].reset();
    $('#fwdsuccesinfo').hide();
    $('#fwderrorinfo').hide();
});

$(document).on( "click", "#documentfwd", function(){
    var department=$('#functionlist').val();
    var remarks=$('#remarks').val();
    var type=$('#fwdtype').val();
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
                    $('#opfwdModel').modal('toggle');
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
                $('#outboxdocumentsearch').trigger('click');
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
                        $('#opprojdocumentsearch').trigger('click');
                    }
                    else {
                        $('#outboxrow'+idval).remove();
                        $('#outboxdocumentsearch').trigger('click');
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

                    $('#oparchiveddoc').trigger('click');

                }



                $('#deletetechdoc'+idval).attr("disabled", false);

            }

        });



    } else {

        return false;

    }



});

$(document).on( "click", "#oparchiveddoc", function(){
    $('#opprojdoclistsection').slideUp('slow');
    $('#oparchiveddoclist').slideDown('slow');
    $('#oparchiveddoc').removeClass('btn-danger').addClass('btn-success');
    $('#oplistddoc').removeClass('btn-success').addClass('btn-danger');
    $.ajax({

        type: 'POST',

        url: '../DoccumentManager/OperationDocs',

        beforeSend : function(){

            $('.preloader').show();

        },

        dataType: "json",

        data: {document:$('#funtiondocstext').val(),projectid:$('#selectedProjectId').val()},

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