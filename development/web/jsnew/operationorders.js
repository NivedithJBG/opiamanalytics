/**
 * Created by SolmindsDelli5 on 28-09-2017.
 */
$(document).on( "click", ".vieworders", function(){
    $('#rproject').removeClass('active').next().slideUp();
    $('#operationorders').addClass('active').next('.acc_container').slideDown();
    var id= $(this).val();
    $('#selectedProjectId').val(id);
    $('#projorderprojname').html(getProjectname(id));
    $('#projordersearch').trigger('click') ;
});

$(function() {
    $('#projordersearch').click(function () {
        $('#projorderslist').slideDown('slow');

        $('#purchasehistorysection').slideUp('slow');

        $('#projordersearch').removeClass('btn-danger').addClass('btn-success');
        $('#purchasehistory').removeClass('btn-danger').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../projects/PurchaseOrders',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectid:$('#selectedProjectId').val(),vendor:$('#searchpovendor').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#projorderitems').html(data.result);
                    $('#projorderstable').show();
                }
                $('.preloader').hide();
            }
        });
    });
    $('#posearch').click(function(){
        $('#projordersearch').trigger('click') ;
    });
    $('#purchasehistory').click(function () {
        $('#projorderslist').slideUp('slow');

        $('#purchasehistorysection').slideDown('slow');

        $('#projordersearch').removeClass('btn-danger').addClass('btn-danger');
        $('#purchasehistory').removeClass('btn-danger').addClass('btn-success');
        $.ajax({
            type: 'POST',
            url: '../projects/Purchasehostory',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectid:$('#selectedProjectId').val(),vendor:$('#searchpohistory').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#purchasehistoryitems').html(data.result);
                    $('#purchasehistorytable').show();
                }
                $('.preloader').hide();
            }
        });
    });
    $('#pohistorysearch').click(function(){
        $('#purchasehistory').trigger('click') ;
    });
});

$(document).on( "click", ".emailorder", function(){
    var orderid=$(this).val();
    $('#orderemail')[0].reset();
    $('#ordersuccesinfo').hide();
    $('#ordererrorinfo').hide();
    $.ajax({
        type: 'POST',
        url: '../Procurement/VendorDetails',
        dataType: "json",
        data: {orderid:orderid},
        success: function(data){
            if(data.error=='No')
            {
                $('#orderemailid').val(data.result);
                $('#orderid').val(orderid);

            }
        }
    });
});

$(document).on( "click", "#emailorder", function(){
    var email=$('#orderemailid').val();
    var subject=$('#ordersubject').val();
    var body=$('#orderbody').val();

    var orderid=$('#orderid').val();
    var error=0;
    $('.error').hide();

    if($('#orderemailid').val()=='')
    {
        $('#orderemailid').next("span").html('Enter Email address').show('slow');
        error=1;
    }
    if($('#ordersubject').val()=='')
    {
        $('#ordersubject').next("span").html('Enter Subject').show('slow');
        error=1;
    }
    if($('#orderbody').val()=='')
    {
        $('#orderbody').next("span").html('Enter Body').show('slow');
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
                    $('#ordersuccesinfo').show();
                    $('#ordersuccesinfo').html('<span style="text-align: center">'+data.errortext+'</span>');
                    $('#ordererrorinfo').hide();
                }
                else {
                    $('.mailloader').hide();
                    $('#ordererrorinfo').show();
                    $('#ordererrorinfo').html('<span style="text-align: center">'+data.errortext+'</span>');
                    $('#ordersuccesinfo').hide();
                }
                setTimeout(function(){
                    $('#emailorderModel').modal('toggle');
                }, 5000);

            }
        });
    }


});
function getProjectname(id)

{

    var retval;

    $.ajax({

        type: 'POST',

        url: '../projects/Getname',

        async:false,

        data: {id:id},

        success: function(data){

            retval=data;

        }

    });

    return retval;

}