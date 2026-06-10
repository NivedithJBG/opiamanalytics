/**
 * Created by SolmindsDelli5 on 26-04-2019.
 */

$(document).on( "click", "#viewstockpurchasein", function(){

    //$('.acc_container').slideUp();

    $('#procurementreports').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

    //$(this).toggleClass('active').next().slideDown();

    $('#stockpurchasein').addClass('active').next('.acc_container').slideDown();

    $('#stockpurchasein').show();
    $('#stockpurchsearch').trigger('click');

});
$(document).on( "click", "#stockpurchasein", function(){

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
    $('#stockpurchsearch').click(function () {

        $.ajax({

            type: 'POST',

            url: '../Procurement/StockPurchasedInputs',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {project:$('#stockpurchproject').val(),item:$('#stockpurchitem').val()},

            success: function(data){

                if(data.error=='No')

                {

                    $('#stockpurchresourceitems').html(data.result);
                    $('#stockpurchitem').html(data.dataresitems);

                    $('#stockpurchresourcetable').show();

                }

                $('.preloader').hide();

            }

        });

    });

});

