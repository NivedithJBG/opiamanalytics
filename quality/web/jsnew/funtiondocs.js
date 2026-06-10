/**
 * Created by SolmindsDelli5 on 02-11-2018.
 */
/**

 * Created by SolmindsDelli5 on 1/17/2017.

 */

$(document).on( "click", ".viewfuntiondocs", function(){

    $('#rproject').removeClass('active').next().slideUp();

    $('#funtiondocs').addClass('active').next('.acc_container').slideDown();

    var id= $(this).val();

    $('#selectedProjectId').val(id);

    $('#sitefuntionname').html(getProjectname(id));

    $('#funtiondocssearch').trigger('click');

});



$(function(){

    $('#funtiondocssearch').click(function(){

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


