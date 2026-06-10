/**
 * Created by SolmindsDelli5 on 26-04-2019.
 */

$(document).on( "click", "#viewstocktools", function(){
    
    //$('.acc_container').slideUp();

    $('#procurementreports').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

    //$(this).toggleClass('active').next().slideDown();

    $('#stocktoolsin').addClass('active').next('.acc_container').slideDown();

    $('#stocktoolsin').show();
    $('#stocktoolssearch').trigger('click');

});
$(document).on( "click", "#stocktoolsin", function(){

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
    $('#stocktoolssearch').click(function () {

        $.ajax({

            type: 'POST',

            url: '../Procurement/StockToolsItems',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {project:$('#stocktoolproject').val(),item:$('#stocktoolitem').val()},

            success: function(data){

                if(data.error=='No')

                {

                    $('#stocktoolresourceitems').html(data.result);
                    $('#stocktoolitem').html(data.dataresitems);

                    $('#stocktoolresourcetable').show();

                }

                $('.preloader').hide();

            }

        });

    });

});

