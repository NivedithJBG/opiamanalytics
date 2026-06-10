/**
 * Created by SolmindsDelli5 on 18-04-2019.
 */
$(document).on( "click", "#procurementreports", function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#stockstatement').hide();
    $('#stockconsumables').hide();
    $('#stockpurchasein').hide();
    $('#estresourcein').hide();
    $('#stocktoolsin').hide();
    $('#subcontin').hide();
});
