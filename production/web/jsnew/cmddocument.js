/**
 * Created by SolmindsDelli5 on 23-10-2018.
 */
$(document).on( "click", "#documents", function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#listcmddoc').trigger('click');
    $('#cmdoutboxsearch').trigger('click');
});
$(function(){
    $('#listcmddoc').click(function(){
        $('#doclistsection').slideDown('slow');// slide down the project listing div
        $('#templatelist').slideUp('slow');// slide down the project listing div
        $('#cmdarchiveddoclist').slideUp('slow');// slide down the project listing div
        $('#listcmddoc').removeClass('btn-danger').addClass('btn-success');
        $('#listtemp').removeClass('btn-success').addClass('btn-danger');
        $('#listarchcmddoc').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../Cmd/CmdDocsearch',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {document:$('#searchdocument').val(),project:$('#selectedProjectId').val(),function:$('#function').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#docitems').html(data.result);
                    $('#doctable').show();
                }
                $('.preloader').hide();
            }
        });
    });
    $('#cmddocumentsearch').click(function(){
        $('#listcmddoc').trigger('click')
    });
    $('#cmdoutboxsearch').click(function(){
        $.ajax({
            type: 'POST',
            url: '../Cmd/Cmdoutbox',
            /*beforeSend : function(){
                $('.preloader').show();
            },*/
            dataType: "json",
            data: {document:$('#cmdoutboxsubject').val(),project:$('#selectedProjectId').val(),function:$('#cmdoutboxfunction').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#cmdoutboxitems').html(data.result);
                    $('#cmdoutboxtable').show();
                }
                $('.preloader').hide();
            }
        });
    });
    $('#listtemp').click(function(){
        $('#templatelist').slideDown('slow');// slide down the project listing div
        $('#doclistsection').slideUp('slow');// slide down the project listing div
        $('#cmdarchiveddoclist').slideUp('slow');// slide down the project listing div
        $('#listtemp').removeClass('btn-danger').addClass('btn-success');
        $('#listcmddoc').removeClass('btn-success').addClass('btn-danger');
        $('#listarchcmddoc').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../DoccumentManager/Templateearch',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {name:$('#searchtemp').val(),department:$('#tempfunction').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#tempitems').html(data.result);
                    $('#temptable').show();
                }
                $('.preloader').hide();
            }
        });
    });
    $('#tempsearch').click(function(){
        $('#listtemp').trigger('click')
    });
    $('#cmdsearch').click(function(){
        $('#listarchcmddoc').trigger('click')
    });
});

$(document).on( "click", "#listarchcmddoc", function(){
    $('#cmdarchiveddoclist').slideDown('slow');
    $('#doclistsection').slideUp('slow');
    $('#templatelist').slideUp('slow');// slide down the project listing div
    $('#listarchcmddoc').removeClass('btn-danger').addClass('btn-success');
    $('#listcmddoc').removeClass('btn-success').addClass('btn-danger');
    $('#listtemp').removeClass('btn-success').addClass('btn-danger');
    $.ajax({
        type: 'POST',
        url: '../DoccumentManager/CMDDocs',
        beforeSend : function(){
            $('.preloader').show();
        },
        dataType: "json",
        data: {folder:"none",document:$('#searchcmd').val()},
        success: function(data){
            if(data.error=='No')
            {
                $('#cmditems').html(data.result);
                $('#cmdtable').show();
            }
            $('.preloader').hide();
        }
    });
});

$(document).on('click','.approvedocs',function(){
    var docid=$(this).val();
    var type=$(this).attr('data-type');
    var r = confirm("Are you sure you want to approve this Document ?");
    if(r== true){
        $.ajax({
            type: 'POST',
            url: '../Cmd/Approvedocs',
            beforeSend : function(){
                $('#approvedocs'+docid).attr("disabled", true);
            },
            dataType: "json",
            data: {docid:docid,type:type},
            success: function(data){
                if(data.error=='No')
                {
                    $('#listcmddoc').trigger('click');
                }
            }
        });
    }
});

$(document).on( "click", ".archivecmdoutbox", function(){
    var idval=$(this).val();
    var type=$(this).attr('data-type');
    var error=0;
    $('.error').hide();
    if(error==0){
        $.ajax({
            type: 'POST',
            url: '../doccumentManager/AssignDoc',
            beforeSend : function(){
                $('#archivecmdoutbox'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {docid:idval,type:type},
            success: function(data){
                if(data.error=='No')
                {
                    $('#cmdoutboxsearch').trigger('click');
                    $('#cmddocumentsearch').trigger('click');
                }
                $('#archivecmdoutbox'+idval).attr("disabled", false);
            }
        });
    }

});

$(document).on('click','.forwarddocs',function(){
    var docid=$(this).val();
    var type=$(this).attr('data-type');
    var r = confirm("Are you sure you want to forward this Document ?");
    if(r== true){
        $.ajax({
            type: 'POST',
            url: '../Cmd/Forwarddocs',
            beforeSend : function(){
                $('#forwarddocs'+docid).attr("disabled", true);
            },
            dataType: "json",
            data: {docid:docid,type:type},
            success: function(data){
                if(data.error=='No')
                {
                    $('#listcmddoc').trigger('click');
                }
            }
        });
    }
});
$(document).on( "click", ".deletedoc", function(){
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
                    $('#listcmddoc').trigger('click');
                }

                $('#deletedoc'+idval).attr("disabled", false);
            }
        });

    } else {
        return false;
    }

});

$(document).on( "click", "#emailcmdoutbox", function(){
    $('#cmdtype').val($(this).attr('data-type'));
    $('#cmddocid').val($(this).val());
    $('#cmdemaildocument')[0].reset();
    $('#cmdsuccesinfo').hide();
    $('#cmderrorinfo').hide();
});
$(document).on( "click", "#cmdemaildoc", function(){
    var email=$('#cmdemailid').val();
    var subject=$('#cmdsubject').val();
    var body=$('#cmdbody').val();
    var type=$('#cmdtype').val();
    var docid=$('#cmddocid').val();
    var error=0;
    $('.error').hide();

    if($('#cmdemailid').val()=='')
    {
        $('#cmdemailid').next("span").html('Enter Email address').show('slow');
        error=1;
    }
    if($('#cmdsubject').val()=='')
    {
        $('#cmdsubject').next("span").html('Enter Subject').show('slow');
        error=1;
    }
    if($('#cmdbody').val()=='')
    {
        $('#cmdbody').next("span").html('Enter Body').show('slow');
        error=1;
    }
    if (error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../DoccumentManager/Sendemail',
            dataType: "json",
            data: {email:email,subject:subject,body:body,type:type,docid:docid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#cmdsuccesinfo').show();
                    $('#cmdsuccesinfo').html('<span style="text-align: center">'+data.errortext+'</span>');
                    $('#cmderrorinfo').hide();
                }
                else {
                    $('#cmderrorinfo').show();
                    $('#cmderrorinfo').html('<span style="text-align: center">'+data.errortext+'</span>');
                    $('#cmdsuccesinfo').hide();
                }
                setTimeout(function(){
                    $('#cmdemailModel').modal('toggle');
                }, 3000);

            }
        });
    }


});
$(document).on( "click", "#fwdcmdoutbox", function(){
    $('#cmdfwdtype').val($(this).attr('data-type'));
    $('#cmdfwddocid').val($(this).val());
    $('#cmdfwddocument')[0].reset();
    $('#cmdfwdsuccesinfo').hide();
    $('#cmdfwderrorinfo').hide();
});
$(document).on( "click", "#cmdfwddoc", function(){
    var department=$('#functionlist').val();
    var remarks=$('#remarks').val();
    var type=$('#cmdfwdtype').val();
    var docid=$('#cmdfwddocid').val();
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
                    $('#cmdfwdsuccesinfo').show();
                    $('#cmdfwdsuccesinfo').html('<span style="text-align: center">Document forwarded to '+data.depname+'.</span>');
                    $('#cmdfwderrorinfo').hide();
                }
                else {
                    $('.mailloader').hide();
                    $('#cmdfwderrorinfo').show();
                    $('#cmdfwderrorinfo').html('<span style="text-align: center">'+data.errortext+'</span>');
                    $('#cmdfwdsuccesinfo').hide();
                }
                setTimeout(function(){
                    $('#cmdfwdModel').modal('toggle');
                }, 3000);

            }
        });
    }


});

$(document).on( "click", "#cmdadddoc", function(){

    var projectid=$('#selectedProjectId').val();

    //var url='../doccumentManager/Createdoc?project='+projectid+'&function=1&mode=operations';
    var url='../doccumentManager/Createdoc?mode=cmd';

    window.location.href = url;

});

$(document).on( "click", "#cmduploaddoc", function(){

    var projectid=$('#selectedProjectId').val();

    //var url='../doccumentManager/Uploaddoc?project='+projectid+'&function=1&mode=operations';
    var url='../doccumentManager/Uploaddoc?mode=cmd';

    window.location.href = url;

});