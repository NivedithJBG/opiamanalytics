$(document).on( "click", "#choosewrkorder", function(){
    $('#cancelorderworkorder').trigger('click');
    $('#wrksearch').trigger('click');

   

    $('.panel-group').removeClass('acco-one-active');
    $('.panel-group').removeClass('acco-two-active');
    $('.panel-group').removeClass('acco-four-active');
    $('.panel-group').removeClass('acco-three-active');
    $('.panel-group').removeClass('acco-five-active');
    $('.panel-group').addClass('acco-six-active');
    $('.panel-group').removeClass('acco-seven-active');
    $('.panel-group').removeClass('acco-eight-active');
    $('.panel-group').removeClass('acco-nine-active');
    $('.panel-group').removeClass('acco-ten-active');


});


$(function() {
    $('#wrksearch').click(function () {
        $.ajax({
            type: 'POST',
            url: '../procurement/workorderscart',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            //data: {task:$('#searchtask').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#wrkorderitems').html(data.result);
                    //$('#cartitemstable').show();
                }
                $('.preloader').hide();
            }
        });
    });
});


$(document).on('click','#wrkcartitems input',function(){
   
    $('#wrkcartitems input:checked').each(function() {
        $vend_id = $(this).val();
        $prj = $(this).attr('data-project');
        $res = $(this).attr('data-restype');
        $cartid = $(this).attr('data-id')

        $('#wvenids').val($vend_id );
        $('#wrresidss').val($res);
        $('#wprjss').val($prj);
        $('#wcrtid').val($cartid);

        
    });
   
});

$(document).on('click','#placeorder_wrkorder',function(){
    if($('.vendors').is(":checked")) {
        var venid=$(this).attr('data-ven');
        var proid=$(this).attr('data-pro');
        var resid = $('#wrresidss').val();
        var cartid = $('#wcrtid').val();

        $.ajax({
            type: 'POST',
            url: '../procurement/workordernew',
            dataType: "json",
            data: {venid: venid,proid: proid,mode:2,resid:resid,cartid:cartid},
            success: function (data) {
                if (data.error == 'No') {

                    if(data.order == 'work'){
                        ordertype="Work Order";
                        $('#wrkorders').show();
                        $('#wrkorders').html(data.workorder);  
                        $('#workform').hide();
                    }
                }
            }
        });
    }else{
        alert('Please select any vendor before proceeding.');
         return false;
    }

});


/*$(document).on('click','#placeorder_wrkorder',function(){

    var venid=$(this).attr('data-ven');
    var proid=$(this).attr('data-pro');

    $.ajax({
        type: 'POST',
        url: '../procurement/workordernew',
        dataType: "json",
        data: {venid: venid,proid: proid,mode:2},
        success: function (data) {
            if (data.error == 'No') {

                if(data.order == 'work'){
                    ordertype="Work Order";
                    $('#wrkorders').show();
                    $('#wrkorders').html(data.workorder);  
                    $('#workform').hide();
                }
            }
        }
    });

});*/
$(document).on('click','#cancelorderworkorder',function(){   


      
         $('#wrkorders').hide();
            
          $('#collapsewrk').show();
          $('#workform').show();
            
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

 $(document).on('click','.editvenresworkcart',function(){

        var id=$(this).attr('data-id');

        $('#wrkeditresname'+id).show();
        $('#wrkresname'+id).hide();
        //$('#wrkresreqty'+id).show();
        /*$('#resreordrqty'+id).hide();
        $('#resreordrlevel'+id).hide();*/
        $('#editvenresworkcart'+id).hide();

        //$('#wrkeditresreqty'+id).hide();
        /*$('#editresreordrqty'+id).show();
        $('#editresreordrlevel'+id).show();*/
        $('#savevenresworkcart'+id).show();


   });

 $(document).on('click','.savevenresworkcart',function(){

        var id=$(this).attr('data-id');

        $.ajax({
            type: 'POST',
            url: '../procurement/savewodetails',
            /*beforeSend : function(){
                $('.preloader').show();
            },*/
            dataType: "json",
            data: {cartid:id,resname:$('#wrkeditresname'+id).val()},
            success: function(data){
                if(data.error=='No')
                {

                    //$('#wrkeditresreqty'+id).hide();
                    //$('#editresreordrqty'+id).hide();
                    //$('#editresreordrlevel'+id).hide();
                    //$('#savevenresworkcart'+id).hide();
                    $('#wrkeditresname'+id).hide();
                    $('#wrkresname'+id).html($('#wrkeditresname'+id).val());
                    $('#wrkresname'+id).show();
                    $('#savevenresworkcart'+id).hide();
                    //$('#wrkresreqty'+id).html($('#wrkeditresreqty'+id).val());
                   /* $('#resreordrqty'+id).html($('#editresreordrqty'+id).val());
                    $('#resreordrlevel'+id).html($('#editresreordrlevel'+id).val());*/

                    //$('#wrkvendortotal'+data.vendorid).html(data.totalamt);

                    //$('#balancetotal'+id).html(data.remaining);

                    //$('#wrkresreqty'+id).show();
                    //$('#resreordrqty'+id).show();
                    //$('#resreordrlevel'+id).show();
                    $('#editvenresworkcart'+id).show();
                }
                //$('.preloader').hide();
            }
        });       

   });

$(document).on('click','.splitvendorwrk',function(){

    var id=$(this).attr('data-id');

    var cartid=$(this).attr('data-cartid');
    var reqty = $('#wrkeditresreqty'+cartid).val();


    $.ajax({
        type: 'POST',
        url: '../procurement/workchoosenewvendor',
        /*beforeSend : function(){
            $('.preloader').show();
        },*/
        dataType: "json",
        data: {resourceid:id,cartid:cartid,reqty:reqty},
        success: function(data){
            if(data.error=='No')
            {
                $('#wrkresreqty'+cartid).html($('#wrkeditresreqty'+cartid).val());
                $('#wrkvendortotal'+data.vendorid).html(data.totalamt);
                $('#newworkaddedresources').html(data.result);
            }
            //$('.preloader').hide();
        }
    });       

});

$(document).on('click','.workchangevendorqty',function(){

    var vendorid=$(this).attr('data-vendorid');

    var resourceid=$(this).attr('data-resourceid');

    var cartid=$(this).attr('data-cartid');

    var splitqty = $('#splitqty'+vendorid).val();

    var requirdqty = $('#wrkeditresreqty'+cartid).val();

    var r = confirm("Are you sure you want to change the vendor?");

    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../procurement/workchangevendor',
            dataType: "json",
            data: {vendorid:vendorid,resourceid:resourceid,cartid:cartid,splitqty:splitqty,requirdqty:requirdqty},
            success: function(data){
                if(data.error=='No')
                {
                    $('.changewrkvendorpopup').trigger('click');
                    $('#wrksearch').trigger('click');
                }
            }
        });      

    } 

});