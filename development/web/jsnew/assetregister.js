/**
 * Created by SolmindsDelli5 on 28-08-2019.
 */
$(document).on( "click", ".viewassetregister", function(){
    $('#rproject').removeClass('active').next().slideUp();
    $('#assetregister').addClass('active').next('.acc_container').slideDown();
    var id= $(this).val();
    $('#assetregprojectid').val(id);
    $('#assetregsearch').trigger('click');
});
$(function() {
    $('#assetregsearch').click(function () {
        $('#assetregisterlist').slideDown('slow');
        $('#assetregprojname').html('Project: '+getProjectname($('#assetregprojectid').val()));
        $.ajax({
            type: 'POST',
            url: '../report/Assetregister',
            beforeSend: function () {
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectid: $('#assetregprojectid').val()},
            success: function (data) {
                if (data.error == 'No') {
                    $('#assetregisteritems').html(data.result);
                    $('#assetregistertable').show();
                    $('.preloader').hide();
                }
            }
        });
    });
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