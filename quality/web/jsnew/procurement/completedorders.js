/**
 * Created by SolmindsDelli5 on 08-09-2017.
 */

$(document).on( "click", "#CompletedOrders", function(){

    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    $('#Completedordersearch').trigger('click');

});

$(function() {
    $('#Completedordersearch').click(function () {
        $.ajax({
            type: 'POST',
            url: '../procurement/completedorders',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            //data: {task:$('#searchtask').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#Completedorderitems').html(data.result);
                    $('#Completedorderstable').show();
                }
                $('.preloader').hide();
            }
        });
    });
});
$(document).on( "click", ".cancelorder", function(){
    var orderid=$(this).val();
    $.ajax({
        type: 'POST',
        url: '../procurement/cancelorder',
        dataType: "json",
        data: {orderid:orderid},
        success: function(data){
            if(data.error=='No')
            {
                $('#Completedordersearch').trigger('click');
            }
            else
            {
                alert(data.errortext);
            }
        }
    });
});

$(document).on( "click", "#search_completed", function(){
    var ordertype=$('#order_type').val();
    var project =$('#project_').val();
    var vendor =$('#vendor_').val();
    $.ajax({
        type: 'POST',
        url: '../Procurement/CompletedOrders',
        dataType: "json",
        data: {ordertype:ordertype, project:project, vendor:vendor},
        success: function(data){
            if(data.error=='No')
            {
                $('#Completedorderitems').html(data.result);
            }
            else
            {
                alert("some error happened");
            }
        }
    });
});
