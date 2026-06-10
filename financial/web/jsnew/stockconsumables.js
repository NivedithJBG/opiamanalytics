/**
 * Created by SolmindsDelli5 on 26-04-2019.
 */
$(document).on( "click", "#viewstockconsumables", function(){

    //$('.acc_container').slideUp();

    $('#procurementreports').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

    //$(this).toggleClass('active').next().slideDown();

    $('#stockconsumables').addClass('active').next('.acc_container').slideDown();

    $('#stockconsumables').show();
    $('#stockconssearch').trigger('click');

});
$(document).on( "click", "#stockconsumables", function(){

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
    $('#stockconssearch').click(function () {

        $.ajax({

            type: 'POST',

            url: '../Procurement/StockConsumables',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {project:$('#stockconsproject').val(),item:$('#stockconsitem').val()},

            success: function(data){

                if(data.error=='No')

                {

                    $('#stockconsresourceitems').html(data.result);
                    $('#stockconsitem').html(data.dataresitems);

                    $('#stockconsresourcetable').show();

                }

                $('.preloader').hide();

            }

        });

    });

});
