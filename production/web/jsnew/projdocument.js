/**
 * Created by SolmindsDelli5 on 25-09-2017.
 */
$(document).on( "click", ".viewDocuments", function(){

    $('#project').removeClass('active').next().slideUp();

    $('#projdocuments').addClass('active').next('.acc_container').slideDown();

    var id= $(this).val();

    $('#selectedProjectId').val(id);

    $('#projdocprojectname').html(getProjectname(id));

    $('#projdocumentsearch').trigger('click');

});

$(document).on( "click", "#adddoc", function(){

    var projectid=$('#selectedProjectId').val();

    var url='../doccumentManager/Createdoc?project='+projectid+'&function=1&mode=project';

    window.location.href = url;

});

$(document).on( "click", "#uploaddoc", function(){

    var projectid=$('#selectedProjectId').val();

    var url='../doccumentManager/Uploaddoc?project='+projectid+'&function=1&mode=project';

    window.location.href = url;

});

$(function(){
    $('#projdocumentsearch').click(function(){
        $('#projdoclistsection').slideDown('slow');// slide down the project listing div
        $.ajax({
            type: 'POST',
            url: '../DoccumentManager/ProjDocsearch',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {project:$('#selectedProjectId').val(),function:$('#projfunction').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#projdocitems').html(data.result);
                    $('#projdoctable').show();
                }
                $('.preloader').hide();
            }
        });
    });
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

                    $('#technicalsearch').trigger('click');

                }



                $('#deletetechdoc'+idval).attr("disabled", false);

            }

        });



    } else {

        return false;

    }



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