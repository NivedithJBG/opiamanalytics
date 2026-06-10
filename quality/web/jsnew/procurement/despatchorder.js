/**
 * Created by SolmindsDelli5 on 10-11-2017.
 */

$(document).on( "click", "#despatch-Order", function(){
    history.replaceState(null, null, ' ');
    /*if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }*/

    /*$('.panel-group').removeClass('acco-billofres-active');
    $('.panel-group').removeClass('acco-vendors-active');
    $('.panel-group').removeClass('acco-one-active');
    $('.panel-group').removeClass('acco-confirmorders-active');
    $('.panel-group').addClass('acco-despatchorders-active');*/

    $('.panel-group').removeClass('acco-one-active');
    $('.panel-group').removeClass('acco-two-active');
    $('.panel-group').removeClass('acco-three-active');
    $('.panel-group').removeClass('acco-four-active');
    $('.panel-group').addClass('acco-five-active');
    $('.panel-group').removeClass('acco-six-active');
    $('.panel-group').removeClass('acco-seven-active');
    $('.panel-group').removeClass('acco-eight-active');
    $('.panel-group').removeClass('acco-nine-active');
    $('.panel-group').removeClass('acco-ten-active');
    $('.panel-group').removeClass('acco-eleven-active');

    //$('#despatchordersearch').trigger('click');
    $('#desppurchor').trigger('click');
    $('.depurrr').addClass('active');

});

/* $(function() {
    $('#despatchordersearch').click(function () {
        $.ajax({
            type: 'POST',
            url: '../procurement/despatchorders',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            //data: {task:$('#searchtask').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#despatchorderitems').html(data.result);
                    $('#despatchordertable').show();
                    $('#historiesdsptch').hide();
                }
                $('.preloader').hide();
            }
        });
    });
}); */
$(document).on( "click", ".historydes", function(){  
    var type= $('#indetifydesp').val();
    $.ajax({
            type: 'POST',
            url: '../procurement/despatchhistory',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {type:type},
            success: function(data){
                if(data.error=='No')
                {
                    $('#historydespatchitems').html(data.result);
                    $('#despatchhistrytable').show();
                    $('#historiesdsptch').show();
                    $('#historydes').hide();
                    $('.despshwnav').hide();
                    $('#despatchordertable').hide();
                }
                $('.preloader').hide();
            }
        });
});
$(document).on( "click", ".despatchback", function(){ 
    $('#despatchordertable').show();
    $('#historiesdsptch').hide();
    $('#historydes').show();
    $('.despshwnav').show();
});
$(document).on( "click", ".emailorder", function(){  
    var orderid=$(this).val();
    $('#orderemail')[0].reset();
    $('#ordersuccesinfo').hide();
    $('#ordererrorinfo').hide();
    $.ajax({
        type: 'POST',
        url: '../procurement/vendordetails',
        dataType: "json",
        data: {orderid:orderid},
        success: function(data){
            if(data.error=='No')
            {
                $('#orderemailid').val(data.result);
                $('#orderid').val(orderid);
                var urlso = '../pdf/order-receipt-'+orderid+'.pdf';
                var urlst = '../pdf/terms-and-conditions-'+orderid+'.pdf';
                $("#Orderw3s").attr("href",urlso);
                $("#Termsw3s").attr("href",urlst);

                //var editor=CKEDITOR.replace( 'orderbody' );
                //editor.config.height = 500;

            }
        }
    });
});


$(document).on( "click", ".printorder", function(){  
    
    $('#orderitemrow'+$(this).data('order')).remove();

});



$(document).on( "click", "#emailorder1", function(){  
    //alert('asd');
    var email=$('#orderemailid').val();
    var ccemail=$('#orderccemail').val();
    var subject=$('#ordersubject').val();
    //var orderbody=$('#orderbody').val();
    var body=CKEDITOR.instances.orderbody.getData();

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
    if(body=='')
    {
        $('#cke_orderbody').next("span").html('Enter Body').show('slow');
        error=1;
    }
    if (error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../procurement/sendorderemail',
            beforeSend : function(){
                $('.mailloader').show();
            },
            dataType: "json",
            data: {email:email,ccemail:ccemail,subject:subject,body:body,orderid:orderid},
            success: function(data){
                if(data.error=='No')
                {
                    $('.mailloader').hide();
                    $('#ordersuccesinfo').show();
                    $('#ordersuccesinfo').html('<span style="text-align: center">'+data.errortext+'</span>');
                    $('#ordererrorinfo').hide();
                    $('#despatchordersearch').trigger('click');
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

$(document).on( "click", ".canceldespatchorder", function(){
    var orderid=$(this).val();
    $.ajax({
        type: 'POST',
        url: '../procurement/cancelorder',
        dataType: "json",
        data: {orderid:orderid},
        success: function(data){
            if(data.error=='No')
            {
                $('#despdirec').trigger('click');
                $('#despatchordersearch').trigger('click');
            }
            else
            {
                alert(data.errortext);
            }
        }
    });
});

$(document).on('click','#desppurchor',function(){

    $('#indetifydesp').val('depor');

    var type = $('#indetifydesp').val();

    $.ajax({
        type: 'POST',
        url: '../procurement/despatchorders',
        beforeSend : function(){
            $('.preloader').show();
        },
        dataType: "json",
        data: {type:type},
        success: function(data){
            if(data.error=='No')
            {
                $('#despatchorderitems').html(data.result);
                $('#despatchordertable').show();
                $('#historiesdsptch').hide();
            }
            $('.preloader').hide();
        }
    });


});
$(document).on('click','#despworko',function(){

    $('#indetifydesp').val('dewrkr');

    var type = $('#indetifydesp').val();

    $.ajax({
        type: 'POST',
        url: '../procurement/despatchorders',
        beforeSend : function(){
            $('.preloader').show();
        },
        dataType: "json",
        data: {type:type},
        success: function(data){
            if(data.error=='No')
            {
                $('#despatchorderitems').html(data.result);
                $('#despatchordertable').show();
                $('#historiesdsptch').hide();
            }
            $('.preloader').hide();
        }
    });

});
$(document).on('click','#despdirec',function(){

    $('#indetifydesp').val('dedir');

    var type = $('#indetifydesp').val();

    $.ajax({
        type: 'POST',
        url: '../procurement/despatchorders',
        beforeSend : function(){
            $('.preloader').show();
        },
        dataType: "json",
        data: {type:type},
        success: function(data){
            if(data.error=='No')
            {
                $('#despatchorderitems').html(data.result);
                $('#despatchordertable').show();
                $('#historiesdsptch').hide();
            }
            $('.preloader').hide();
        }
    });

});
$(document).on('click','#despleaso',function(){

    $('#indetifydesp').val('delease');

    var type = $('#indetifydesp').val();

    $.ajax({
        type: 'POST',
        url: '../procurement/despatchorders',
        beforeSend : function(){
            $('.preloader').show();
        },
        dataType: "json",
        data: {type:type},
        success: function(data){
            if(data.error=='No')
            {
                $('#despatchorderitems').html(data.result);
                $('#despatchordertable').show();
                $('#historiesdsptch').hide();
            }
            $('.preloader').hide();
        }
    });

});
$(document).on('click','#despdespto',function(){

    $('#indetifydesp').val('dedesp');

    var type = $('#indetifydesp').val();

    $.ajax({
        type: 'POST',
        url: '../procurement/despatchorders',
        beforeSend : function(){
            $('.preloader').show();
        },
        dataType: "json",
        data: {type:type},
        success: function(data){
            if(data.error=='No')
            {
                $('#despatchorderitems').html(data.result);
                $('#despatchordertable').show();
                $('#historiesdsptch').hide();
            }
            $('.preloader').hide();
        }
    });

});

$(document).on('click','.amend',function(){

    var orderid = $(this).data('value');
    var ordertype = $(this).data('id');
    $.ajax({
        type: 'POST',
        url: '../procurement/amend',
        beforeSend : function(){
            $('.preloader').show();
        },
        dataType: "json",
        data: {orderid:orderid,ordertype:ordertype},
        success: function(data){
            if(data.error=='No')
            {
                $('#amenddata').html(data.otherorder);  
                $('#amenddata').show();
                $('#historiesdsptch').hide();
                $('.preloader').hide();
            }
            
        }
    });

});

$(document).on('click','#cancelamend',function(){  
    $('#historiesdsptch').show();
    $('.topbar').show();   

    $('#amenddata').hide();
           
    parent.window.close();
    window.onunload = function (e) { 
        
     opener.refreshParentWindow();  
     };            
            
 });