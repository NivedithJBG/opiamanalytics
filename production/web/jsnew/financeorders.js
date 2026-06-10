/**
 * Created by SolmindsDelli5 on 01-03-2018.
 */
$(document).on( "click", "#Financeorders", function(){

    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    $('#Financeordersearch').trigger('click');

});

$(function() {
    $('#Financeordersearch').click(function () {
        $.ajax({
            type: 'POST',
            url: '../FinanceRequests/CompletedOrders',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            //data: {task:$('#searchtask').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#Financeorderitems').html(data.result);
                    $('#Financeorderstable').show();
                }
                $('.preloader').hide();
            }
        });
    });
});
$(document).on( "click", ".emailorder", function(){
    var orderid=$(this).val();
    $('#orderemail')[0].reset();
    $('#succesinfo').hide();
    $('#errorinfo').hide();
    $.ajax({
        type: 'POST',
        url: '../Procurement/VendorDetails',
        dataType: "json",
        data: {orderid:orderid},
        success: function(data){
            if(data.error=='No')
            {
                $('#emailid').val(data.result);
                $('#orderid').val(orderid);

            }
        }
    });
});
$(document).on( "click", "#emailorder", function(){
    var email=$('#emailid').val();
    var subject=$('#subject').val();
    var body=$('#body').val();

    var orderid=$('#orderid').val();
    var error=0;
    $('.error').hide();

    if($('#emailid').val()=='')
    {
        $('#emailid').next("span").html('Enter Email address').show('slow');
        error=1;
    }
    if($('#subject').val()=='')
    {
        $('#subject').next("span").html('Enter Subject').show('slow');
        error=1;
    }
    if($('#body').val()=='')
    {
        $('#body').next("span").html('Enter Body').show('slow');
        error=1;
    }
    if (error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../Procurement/SendOrderemail',
            beforeSend : function(){
                $('.mailloader').show();
            },
            dataType: "json",
            data: {email:email,subject:subject,body:body,orderid:orderid},
            success: function(data){
                if(data.error=='No')
                {
                    $('.mailloader').hide();
                    $('#succesinfo').show();
                    $('#succesinfo').html('<span style="text-align: center">'+data.errortext+'</span>');
                    $('#ordererrorinfo').hide();
                }
                else {
                    $('.mailloader').hide();
                    $('#errorinfo').show();
                    $('#errorinfo').html('<span style="text-align: center">'+data.errortext+'</span>');
                    $('#succesinfo').hide();
                }
                setTimeout(function(){
                    $('#emailorderModel').modal('toggle');
                }, 5000);

            }
        });
    }

});
$(document).on( "click", "#search_completed", function(){
    var ordertype=$('#order_type').val();
    var project =$('#project_').val();
    var vendor =$('#vendor_').val();
    $.ajax({
        type: 'POST',
        url: '../FinanceRequests/CompletedOrders',
        dataType: "json",
        data: {ordertype:ordertype, project:project, vendor:vendor},
        success: function(data){
            if(data.error=='No')
            {
                $('#Financeorderitems').html(data.result);
            }
            else
            {
                alert("some error happened");
            }
        }
    });
});