$(document).on( "click", "#choosedesporder", function(){
    //$('#cancelorderworkorder').trigger('click');
    $('#despsearch').trigger('click');

   

    $('.panel-group').removeClass('acco-one-active');
    $('.panel-group').removeClass('acco-two-active');
    $('.panel-group').removeClass('acco-four-active');
    $('.panel-group').removeClass('acco-three-active');
    $('.panel-group').removeClass('acco-five-active');
    $('.panel-group').removeClass('acco-six-active');
    $('.panel-group').removeClass('acco-seven-active');
    $('.panel-group').removeClass('acco-eight-active');
    $('.panel-group').addClass('acco-nine-active');
    $('.panel-group').removeClass('acco-ten-active');
    $('.panel-group').removeClass('acco-eleven-active');


});

$(function() {
    $('#despsearch').click(function () {
        $.ajax({
            type: 'POST',
            url: '../procurement/despatchorderscart',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            //data: {task:$('#searchtask').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#desporderitems').html(data.result);
                    //$('#cartitemstable').show();
                }
                $('.preloader').hide();
            }
        });
    });
});

$(document).on('click','.editvenresdespcart',function(){

        var id=$(this).attr('data-id');

        $('#despresreqty'+id).hide();
        $('#despresreordrqty'+id).hide();
        $('#despresreordrlevel'+id).hide();
        $('#editvenrescart'+id).hide();

        $('#despeditresreqty'+id).show();
        $('#despeditresreordrqty'+id).show();
        $('#despeditresreordrlevel'+id).show();
        $('#savevenresdespcart'+id).show();


   });

   $(document).on('click','.savevenresdespcart',function(){

        var id=$(this).attr('data-id');
        var rate = $('#desprate').val();
        var edtreq = $('#despeditresreqty'+id).val();
        var tot = edtreq * rate;

        $.ajax({
            type: 'POST',
            url: '../procurement/savedodetails',
            /*beforeSend : function(){
                $('.preloader').show();
            },*/
            dataType: "json",
            data: {cartid:id,reqty:$('#despeditresreqty'+id).val(),reordqty:$('#despeditresreordrqty'+id).val(),reorderlevel:$('#despeditresreordrlevel'+id).val()},
            success: function(data){
                if(data.error=='No')
                {

                    $('#despeditresreqty'+id).hide();
                    $('#despeditresreordrqty'+id).hide();
                    $('#despeditresreordrlevel'+id).hide();
                    $('#savevenresdespcart'+id).hide();
                   
                    $('#despresreqty'+id).html($('#despeditresreqty'+id).val());
                    $('#despresreordrqty'+id).html($('#despeditresreordrqty'+id).val());
                    $('#despresreordrlevel'+id).html($('#despeditresreordrlevel'+id).val());

                    $('#despvendortotal'+data.vendorid).html(data.totalamt);
                    $('#destotamnt'+id).html(tot);

                    $('#despbalancetotal'+id).html(data.remaining);

                    $('#despresreqty'+id).show();
                    $('#despresreordrqty'+id).show();
                    $('#despresreordrlevel'+id).show();
                    $('#editvenrescart'+id).show();
                }
                //$('.preloader').hide();
            }
        });       

   });

   $(document).on('click','.splitvendordesp',function(){

    var id=$(this).attr('data-id');

    var cartid=$(this).attr('data-cartid');

    $.ajax({
        type: 'POST',
        url: '../procurement/despchoosenewvendor',
        /*beforeSend : function(){
            $('.preloader').show();
        },*/
        dataType: "json",
        data: {resourceid:id,cartid:cartid},
        success: function(data){
            if(data.error=='No')
            {
                $('#newdespaddedresources').html(data.result);
            }
            //$('.preloader').hide();
        }
    });       

});

$(document).on('click','.despchangevendorqty',function(){

    var vendorid=$(this).attr('data-vendorid');

    var resourceid=$(this).attr('data-resourceid');

    var cartid=$(this).attr('data-cartid');

    var splitqty = $('#splitqty'+vendorid).val();

    var requiedqty = $('#despeditresreqty'+cartid).val();

    var r = confirm("Are you sure you want to change the vendor?");

    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../procurement/despchnagevendor',
            dataType: "json",
            data: {vendorid:vendorid,resourceid:resourceid,cartid:cartid,splitqty:splitqty,requiedqty:requiedqty},
            success: function(data){
                if(data.error=='No')
                {
                    $('.changedespvendorpopup').trigger('click');
                    $('#despsearch').trigger('click');
                }
            }
        });      

    } 

});
$(document).on('click','#despcartitems input',function(){
   
    $('#despcartitems input:checked').each(function() {
        $vend_id = $(this).val();
        $prj = $(this).attr('data-project');
        $res = $(this).attr('data-restype');
        $cartid = $(this).attr('data-id');
        $resttype = $(this).attr('data-res');

        $('#desvenids').val($vend_id );
        $('#desrresidss').val($res);
        $('#desprjss').val($prj);
        $('#descrtid').val($cartid);
        $('#desrestype').val($resttype);

        
    });
   
});

$(document).on('click','#placeorder_desporder',function(){

if($('.vendors').is(":checked")) {

    var venid = $(this).attr('data-ven');
    var proid = $(this).attr('data-pro');
    var resid = $('#desrresidss').val();
    var cartid = $('#descrtid').val();
    var restype = $('#desrestype').val();
   

 

    $.ajax({
        type: 'POST',
        url: '../procurement/desporders',
        dataType: "json",
        data: {venid: venid,proid: proid,mode:5,resid:resid,orders:cartid,restype:restype},
        success: function (data) {
            if (data.error == 'No') {

                if(data.order == 'despatch'){
                    ordertype="Despatch Order";
                    $('#despporders').show();
                    $('#despporders').html(data.despatchorder);  
                    $('#despform').hide();
                }
            }
        }
    });
 }else {
         alert('Please select any vendor before proceeding.');
         return false;
     }

});
$(document).on('click','#cancelorderdespatchhs',function(){   


      
         $('#despporders').hide();
            
          $('#collapssedespatch').show();
          $('#despform').show();
            
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