/**
 * Created by SolmindsDelli5 on 1/17/2017.
 */
$(document).on( "click", "#finance", function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#financelistsection').hide();
});

$(function(){
    $('#financesearch').click(function(){
        $('#financelistsection').slideDown('slow');
        $.ajax({
            type: 'POST',
            url: '../DoccumentManager/FinanceDocs',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {folder:$('#finfolder').val(),document:$('#searchfinance').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#financeitems').html(data.result);
                    $('#financetable').show();
                }
                $('.preloader').hide();
            }
        });
    });

});
$(document).on( "click", ".deletefindoc", function(){
    var idval=$(this).val();
    var type=$(this).attr('data-id');
    var r = confirm("Are you sure you want to delete this Document ?");
    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../DoccumentManager/Deletedoc',
            beforeSend : function(){
                $('#deletefindoc'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {docid:idval,type:type},
            success: function(data){
                if(data.error=='No')
                {
                    $('#drawingrow'+idval).remove();
                    $('#financesearch').trigger('click');
                }

                $('#deletefindoc'+idval).attr("disabled", false);
            }
        });

    } else {
        return false;
    }

});
