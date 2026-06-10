/**
 * Created by SolmindsDelli5 on 08-04-2019.
 */
$(document).on( "click", "#viewstockstatement", function(){

    //$('.acc_container').slideUp();

    $('#procurementreports').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

    //$(this).toggleClass('active').next().slideDown();

    $('#stockstatement').addClass('active').next('.acc_container').slideDown();

    $('#stockstatement').show();
    $('#stocksearch').trigger('click');

});
$(document).on( "click", "#stockstatement", function(){

    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

});
/*$(document).on( "click", "#stockstatement", function(){

    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    $('#stocksearch').trigger('click');

});*/
$(function() {
    $('#stocksearch').click(function () {

        $.ajax({

            type: 'POST',

            url: '../Procurement/StockResources',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {project:$('#stockproject').val(),item:$('#stockitem').val()},

            success: function(data){

                if(data.error=='No')

                {

                    $('#stockresourceitems').html(data.result);
                    $('#stockitem').html(data.dataresitems);

                    $('#stockresourcetable').show();

                }

                $('.preloader').hide();

            }

        });

    });

});