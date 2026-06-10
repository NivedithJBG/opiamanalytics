/**
 * Created by SolmindsDelli5 on 02-11-2018.
 */
$(document).on( "click", "#cmd", function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    //$('#cmdlistsection').hide();
});

$(function(){
    $('#cmdsearch').click(function(){
        $('#cmdlistsection').slideDown('slow');
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

});
