/**
 * Created by SolmindsDelli5 on 27-05-2016.
 */
$(document).on( "click", "#journals", function(){

    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#listjournals').trigger('click');
});
$(function(){
    $('#listjournals').click(function(){
        //$('#productaddsection').slideUp('slow');// slide down the project listing div
        $('#journallistsection').slideDown('slow');// slide down the project listing div
        $('#listjournals').removeClass('btn-danger').addClass('btn-success');
        $('#addjournal').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../DoccumentManager/approvedpurchaseorder',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            //data: {name:$('#searchreceipt').val(),project:$('#searchreceipt').val()},
            data: {name:$('#searchrequest').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#journelitems').html(data.result);
                    $('#journeltable').show();
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });
    });
});
