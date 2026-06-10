/**
 * Created by SolmindsDelli5 on 1/17/2017.
 */
$(document).on( "click", "#administration", function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#administrationlistsection').hide();
});

$(function(){
    $('#administrationsearch').click(function(){
        $('#administrationlistsection').slideDown('slow');
        $.ajax({
            type: 'POST',
            url: '../DoccumentManager/AdministrationDocs',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {folder:$('#adminfolder').val(),document:$('#searchadministration').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#administrationitems').html(data.result);
                    $('#administrationtable').show();
                }
                $('.preloader').hide();
            }
        });
    });

});
$(document).on( "click", ".deleteadmdoc", function(){
    var idval=$(this).val();
    var type=$(this).attr('data-id');
    var r = confirm("Are you sure you want to delete this Document ?");
    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../DoccumentManager/Deletedoc',
            beforeSend : function(){
                $('#deleteadmdoc'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {docid:idval,type:type},
            success: function(data){
                if(data.error=='No')
                {
                    $('#drawingrow'+idval).remove();
                    $('#administrationsearch').trigger('click');
                }

                $('#deleteadmdoc'+idval).attr("disabled", false);
            }
        });

    } else {
        return false;
    }

});
