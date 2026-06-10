$(document).on( "click", "#choosedirectwrkorder", function(){
    //$('#cancelorderworkorder').trigger('click');
    $('#directwrksearch').trigger('click');

   

    $('.panel-group').removeClass('acco-one-active');
    $('.panel-group').removeClass('acco-two-active');
    $('.panel-group').removeClass('acco-three-active');
    $('.panel-group').removeClass('acco-five-active');
    $('.panel-group').removeClass('acco-four-active');
    $('.panel-group').removeClass('acco-six-active');
    $('.panel-group').addClass('acco-seven-active');
    $('.panel-group').removeClass('acco-eight-active');
    $('.panel-group').removeClass('acco-nine-active');
    $('.panel-group').removeClass('acco-ten-active');

});

$(function() {
    $('#directwrksearch').click(function () {
        $.ajax({
            type: 'POST',
            url: '../procurement/directworkorderscart',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            //data: {task:$('#searchtask').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#directwrkorderitems').html(data.result);
                    //$('#cartitemstable').show();
                }
                $('.preloader').hide();
            }
        });
    });
});

$(document).on('click','#placeorder_dirwrkorder',function(){

if($('.vendors').is(":checked")) {

    var venid = $(this).attr('data-ven');
    var proid = $(this).attr('data-pro');
    var resid = $('#rresidss').val();
    var cartid = $('#crtid').val(); 
   

 

    $.ajax({
        type: 'POST',
        url: '../procurement/dirworkordernew',
        dataType: "json",
        data: {venid: venid,proid: proid,mode:3,resid:resid,cartid:cartid},
        success: function (data) {
            if (data.error == 'No') {

                if(data.order == 'directwork'){
                    ordertype="Direct Work Order";
                    $('#directwrkorders').show();
                    $('#directwrkorders').html(data.musterroll);  
                    $('#directworkform').hide();
                }
            }
        }
    });
 }else {
         alert('Please select any vendor before proceeding.');
         return false;
     }

});

$(document).on('click','#cancelorderdirworkorder',function(){   


      
         $('#directwrkorders').hide();
            
          $('#collapsedirectwrk').show();
          $('#directworkform').show();
            
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

 $(document).on('click','.splitvendordirectwrk',function(){

    var id=$(this).attr('data-id');

    var cartid=$(this).attr('data-cartid');
    var reqty = $('#dirwrkeditresreqty'+cartid).val();


    $.ajax({
        type: 'POST',
        url: '../procurement/dirworkchoosenewvendor',
        /*beforeSend : function(){
            $('.preloader').show();
        },*/
        dataType: "json",
        data: {resourceid:id,cartid:cartid,reqty:reqty},
        success: function(data){
            if(data.error=='No')
            {
                $('#dirwrkresreqty'+cartid).html($('#dirwrkeditresreqty'+cartid).val());
                $('#dirwrkvendortotal'+data.vendorid).html(data.totalamt);
                $('#newdirworkaddedresources').html(data.result);
            }
            //$('.preloader').hide();
        }
    });       

});

$(document).on('click','.dirworkchangevendorqty',function(){

    var vendorid=$(this).attr('data-vendorid');

    var resourceid=$(this).attr('data-resourceid');

    var cartid=$(this).attr('data-cartid');

    var splitqty = $('#splitqty'+vendorid).val();

    var requirdqty = $('#dirwrkeditresreqty'+cartid).val();

    var r = confirm("Are you sure you want to change the vendor?");

    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../procurement/dirworkchangevendor',
            dataType: "json",
            data: {vendorid:vendorid,resourceid:resourceid,cartid:cartid,splitqty:splitqty,requirdqty:requirdqty},
            success: function(data){
                if(data.error=='No')
                {
                    $('.changedirwrkvendorpopup').trigger('click');
                    $('#directwrksearch').trigger('click');
                }
            }
        });      

    } 

});

$(document).on('click','#dircartitems input',function(){
   
    $('#dircartitems input:checked').each(function() {
        $vend_id = $(this).val();
        $prj = $(this).attr('data-project');
        $res = $(this).attr('data-restype');
        $cartid = $(this).attr('data-id')

        $('#venids').val($vend_id );
        $('#rresidss').val($res);
        $('#prjss').val($prj);
        $('#crtid').val($cartid);

        
    });
   
});