$(document).on( "click", "#chooseleaseorder", function(){
    $('#cancelorderleaseorder').trigger('click');
    $('#leasesearch').trigger('click');

   

    $('.panel-group').removeClass('acco-one-active');
    $('.panel-group').removeClass('acco-two-active');
    $('.panel-group').removeClass('acco-three-active');
    $('.panel-group').removeClass('acco-four-active');
    $('.panel-group').removeClass('acco-six-active');
    $('.panel-group').removeClass('acco-five-active');
    $('.panel-group').removeClass('acco-seven-active');
    $('.panel-group').addClass('acco-eight-active');
    $('.panel-group').removeClass('acco-nine-active');
    $('.panel-group').removeClass('acco-ten-active');


});

$(function() {
    $('#leasesearch').click(function () {
        $.ajax({
            type: 'POST',
            url: '../procurement/leaseorderscart',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            //data: {task:$('#searchtask').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#leaseorderitems').html(data.result);
                    
                }
                $('.preloader').hide();
            }
        });
    });
});
$(document).on('click','#leasecartitems input',function(){
   
    $('#leasecartitems input:checked').each(function() {
        $vend_id = $(this).val();
        $prj = $(this).attr('data-project');
        $res = $(this).attr('data-restype');
        $cartid = $(this).attr('data-id')

        $('#lvenids').val($vend_id );
        $('#lrresidss').val($res);
        $('#lprjss').val($prj);
        $('#lcrtid').val($cartid);

        
    });
   
});
$(document).on('click','#placeorder_leaseorder',function(){

if($('.vendors').is(":checked")) {

    var venid=$(this).attr('data-ven');
    var proid=$(this).attr('data-pro');
    var resid = $('#lrresidss').val();
    var cartid = $('#lcrtid').val(); 

    $.ajax({
        type: 'POST',
        url: '../procurement/leaseordernew',
        dataType: "json",
        data: {venid: venid,proid: proid,mode:4,resid:resid,cartid:cartid},
        success: function (data) {
            if (data.error == 'No') {

                if(data.order == 'lease'){
                    ordertype="Lease Order";
                    $('#lsorders').show();
                    $('#lsorders').html(data.leaseorders);  
                    $('#leaseform').hide();
                }
            }
        }
    });
}else{
    alert('Please select any vendor before proceeding.');
         return false;
}
});

/*$(document).on('click','#placeorder_leaseorder',function(){

    var venid=$(this).attr('data-ven');
    var proid=$(this).attr('data-pro');

    $.ajax({
        type: 'POST',
        url: '../procurement/leaseordernew',
        dataType: "json",
        data: {venid: venid,proid: proid,mode:4},
        success: function (data) {
            if (data.error == 'No') {

                if(data.order == 'lease'){
                    ordertype="Lease Order";
                    $('#lsorders').show();
                    $('#lsorders').html(data.leaseorders);  
                    $('#leaseform').hide();
                }
            }
        }
    });

});*/

$(document).on('click','#cancelorderleaseorder',function(){   


      
         $('#lsorders').hide();
            
          $('#collapselease').show();
          $('#leaseform').show();
            
            // setTimeout(function(){

            //     parent.closeFrame2();
                    
            // },500);
                      
           /* parent.window.close();
            window.onunload = function (e) { 
                
             opener.refreshParentWindow();  
             };

    $.ajax({
        type: 'POST',
        url: '../procurement/droptemp',
        beforeSend : function(){
        },
        dataType:"json",
        data:{},
        success: function(data){
        }
    });        */      
            
 });

 $(document).on('click','.editvenresleasecart',function(){

        var id=$(this).attr('data-id');

        $('#leaseresreqty'+id).show();
        /*$('#resreordrqty'+id).hide();
        $('#resreordrlevel'+id).hide();*/
        $('#editvenresleasecart'+id).hide();

        $('#leaseeditresreqty'+id).hide();
        /*$('#editresreordrqty'+id).show();
        $('#editresreordrlevel'+id).show();*/
        $('#savevenresleasecart'+id).show();


   });

 $(document).on('click','.savevenresleasecart',function(){

        var id=$(this).attr('data-id');

        $.ajax({
            type: 'POST',
            url: '../procurement/savewodetails',
            /*beforeSend : function(){
                $('.preloader').show();
            },*/
            dataType: "json",
            data: {cartid:id,reqty:$('#leaseeditresreqty'+id).val()},
            success: function(data){
                if(data.error=='No')
                {

                    //$('#leaseeditresreqty'+id).hide();
                    //$('#editresreordrqty'+id).hide();
                    //$('#editresreordrlevel'+id).hide();
                    //$('#savevenresleasecart'+id).hide();

                    $('#leaseresreqty'+id).html($('#leaseeditresreqty'+id).val());
                   /* $('#resreordrqty'+id).html($('#editresreordrqty'+id).val());
                    $('#resreordrlevel'+id).html($('#editresreordrlevel'+id).val());*/

                    $('#leasevendortotal'+data.vendorid).html(data.totalamt);

                    //$('#balancetotal'+id).html(data.remaining);

                    //$('#leaseresreqty'+id).show();
                    //$('#resreordrqty'+id).show();
                    //$('#resreordrlevel'+id).show();
                    //$('#editvenresleasecart'+id).show();
                }
                //$('.preloader').hide();
            }
        });       

   });

 $(document).on('click','.splitvendorlease',function(){

    var id=$(this).attr('data-id');

    var cartid=$(this).attr('data-cartid');
    var reqty = $('#leaseeditresreqty'+cartid).val();


    $.ajax({
        type: 'POST',
        url: '../procurement/leasechoosenewvendor',
        /*beforeSend : function(){
            $('.preloader').show();
        },*/
        dataType: "json",
        data: {resourceid:id,cartid:cartid,reqty:reqty},
        success: function(data){
            if(data.error=='No')
            {
                $('#leaseresreqty'+cartid).html($('#leaseeditresreqty'+cartid).val());
                $('#leasevendortotal'+data.vendorid).html(data.totalamt);
                $('#newleaseaddedresources').html(data.result);
            }
            //$('.preloader').hide();
        }
    });       

});

$(document).on('click','.leasechangevendorqty',function(){

    var vendorid=$(this).attr('data-vendorid');

    var resourceid=$(this).attr('data-resourceid');

    var cartid=$(this).attr('data-cartid');

    var splitqty = $('#splitqty'+vendorid).val();

    var requirdqty = $('#leaseeditresreqty'+cartid).val();

    var r = confirm("Are you sure you want to change the vendor?");

    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../procurement/leasechangevendor',
            dataType: "json",
            data: {vendorid:vendorid,resourceid:resourceid,cartid:cartid,splitqty:splitqty,requirdqty:requirdqty},
            success: function(data){
                if(data.error=='No')
                {
                    $('.changeleasevendorpopup').trigger('click');
                    $('#leasesearch').trigger('click');
                }
            }
        });      

    } 

});