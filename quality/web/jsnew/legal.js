/**
 * Created by SolmindsDelli5 on 1/17/2017.
 */
$(document).on( "click", "#legal", function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#legallistsection').hide();
});

$(function(){
    $('#legalsearch').click(function(){
        $('#legallistsection').slideDown('slow');
        $.ajax({
            type: 'POST',
            url: '../DoccumentManager/LegalDocs',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {folder:$('#legalfolder').val(),document:$('#searchlegal').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#legalitems').html(data.result);
                    $('#legaltable').show();
                }
                $('.preloader').hide();
            }
        });
    });

});
$(document).on( "click", ".deletelegdoc", function(){
    var idval=$(this).val();
    var type=$(this).attr('data-id');
    var r = confirm("Are you sure you want to delete this Document ?");
    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../DoccumentManager/Deletedoc',
            beforeSend : function(){
                $('#deletelegdoc'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {docid:idval,type:type},
            success: function(data){
                if(data.error=='No')
                {
                    $('#drawingrow'+idval).remove();
                    $('#legalsearch').trigger('click');
                }

                $('#deletelegdoc'+idval).attr("disabled", false);
            }
        });

    } else {
        return false;
    }

});
