/**
 * Created by SolmindsDelli5 on 09-01-2018.
 */

$(document).on( "click", ".viewenggDocuments", function(){

    $('#enggproject').removeClass('active').next().slideUp();

    $('#enggdocuments').addClass('active').next('.acc_container').slideDown();

    var id= $(this).val();

    $('#enggprojectid').val(id);

    $('#enggdocprojectname').html(getProjectname(id));

    $('#engglistdoc').trigger('click');

});

$(document).on( "click", "#enggadddoc", function(){

    var projectid=$('#enggprojectid').val();

    var url='../doccumentManager/Createdoc?project='+projectid+'&mode=engineering';

    window.location.href = url;

});

$(document).on( "click", "#engguploaddoc", function(){

    var projectid=$('#enggprojectid').val();

    var url='../doccumentManager/Uploaddoc?project='+projectid+'&mode=engineering';

    window.location.href = url;

});

$(function() {
    $('#engglistdoc').click(function(){
        $('#enggarchiveddoclist').slideUp('slow');
        $('#enggdoclistsection').slideDown('slow');// slide down the project listing div
        $('#engglistdoc').removeClass('btn-danger').addClass('btn-success');
        $('#enggarchiveddoc').removeClass('btn-success').addClass('btn-danger');
        $('#engginboxdocsearch').trigger('click');
        $('#enggoutboxdocsearch').trigger('click');
    });
    $('#engginboxdocsearch').click(function(){
        $.ajax({
            type: 'POST',
            url: '../DoccumentManager/EnggInboxDocsearch',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {subject:$('#searchinboxdocument').val(),project:$('#enggprojectid').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#enggdocitems').html(data.result);
                    $('#enggdoctable').show();
                }
                $('.preloader').hide();
            }
        });
    });
    $('#enggoutboxdocsearch').click(function(){
        $.ajax({
            type: 'POST',
            url: '../DoccumentManager/EnggOutboxDocsearch',
            dataType: "json",
            data: {subject:$('#searchoutboxdocument').val(),project:$('#enggprojectid').val()},
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
        $('#enggarchiveddoc').trigger('click');
    });
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
                $('#enggoutboxdocsearch').trigger('click');
            }
        }
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
                    $('#engginboxdocsearch').trigger('click');
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
    $('#enggfwddocument')[0].reset();
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
                    $('#enggfwdModel').modal('toggle');
                }, 3000);

            }
        });
    }


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

$(document).on( "click", "#enggarchiveddoc", function(){
    $('#enggdoclistsection').slideUp('slow');
    $('#enggarchiveddoclist').slideDown('slow');
    $('#enggarchiveddoc').removeClass('btn-danger').addClass('btn-success');
    $('#engglistdoc').removeClass('btn-success').addClass('btn-danger');
    $.ajax({

        type: 'POST',

        url: '../DoccumentManager/EnggArchivedDocs',

        beforeSend : function(){

            $('.preloader').show();

        },

        dataType: "json",

        data: {document:$('#funtiondocstext').val(),projectid:$('#enggprojectid').val()},

        success: function(data){

            if(data.error=='No')

            {

                //$('#funcspan').html(data.fuctname);
                $('#funtiondocsitems').html(data.result);

                $('#funtiondocstable').show();

            }

            $('.preloader').hide();

        }

    });

});

/*$(document).on( "click", ".assigndocument", function(){

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

                    $('#listdoc').trigger('click');

                }

                $('#assigndocument'+idval).attr("disabled", false);

            }

        });

    }



});*/

$(document).on( "click", ".documentdespatch", function(){

    var idval=$(this).val();

    var type=$(this).attr('data-id');

    var error=0;

    $('.error').hide();

    if(error==0){

        $.ajax({

            type: 'POST',

            url: '../doccumentManager/DocumentDesp',

            beforeSend : function(){

                $('#documentdespatch'+idval).attr("disabled", true);

            },

            dataType: "json",

            data: {docid:idval,type:type},

            success: function(data){

                if(data.error=='No')

                {

                    $('#listdoc').trigger('click');

                }

                $('#documentdespatch'+idval).attr("disabled", false);

            }

        });

    }



});

/*$(document).on( "click", ".deletedoc", function(){

    var idval=$(this).val();

    var type=$(this).attr('data-id');

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

                    $('#docrow'+idval).remove();

                    $('#listdoc').trigger('click');

                }



                $('#deletedoc'+idval).attr("disabled", false);

            }

        });



    } else {

        return false;

    }



});*/

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
