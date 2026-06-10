/**
 * Created by SolmindsDelli5 on 27-05-2016.
 */
$(document).on("click", "#bills", function () {

    if (!$(this).next().is(':hidden')) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if ($(this).next().is(':hidden')) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#listbills').trigger('click');
});
$(function (){
$('#listbills').click(function () {
    //$('#productaddsection').slideUp('slow');// slide down the project listing div
    $('#billslistsection').slideDown('slow');// slide down the project listing div
    $('#listbills').removeClass('btn-danger').addClass('btn-success');
    $('#addbills').removeClass('btn-success').addClass('btn-danger');
    $.ajax({
        type:'POST',
        url:'../DoccumentManager/workapprovedsearch',
        beforeSend:function () {
            $('.preloader').show();
        },
        dataType:"json",
        data: {name:$('#searchrequest').val()},
        success:function (data) {
            if (data.error == 'No') {
                $('#billitems').html(data.result);
                $('#billstable').show();
            }
            else {
                alert(data.errortext);
            }
            $('.preloader').hide();
        }
    });
});

});

