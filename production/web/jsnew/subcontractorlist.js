/**
 * Created by SolmindsDelli5 on 26-04-2019.
 */

$(document).on( "click", "#viewsubcontractors", function(){

    //$('.acc_container').slideUp();

    $('#procurementreports').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

    //$(this).toggleClass('active').next().slideDown();

    $('#subcontin').addClass('active').next('.acc_container').slideDown();

    $('#subcontin').show();
    //$('#subcontrctrlist').trigger('click');
    $('#subcontrctrsearch').trigger('click');

});
$(document).on( "click", "#subcontin", function(){

    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

});

$(function() {
    $('#subcontrctrsearch').click(function () {
        
        //var projectid = $('#estresourceproject').val();

        $.ajax({

            type: 'POST',

            url: '../Procurement/Subcontractorlist',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {project:$('#subcontrproject').val(),vendor:$('#vendor').val()},

            success: function(data){

                if(data.error=='No')

                {

                    $('#subcontrctritems').html(data.result);

                    $('#subcontrctrtable').show();

                }

                $('.preloader').hide();

            }

        });

    });

});