$(document).on( "click", "#viewlogbook", function(){

    var id= $(this).val();

    $('#projid').val(id);

    //$('#projectname').html(getProjectname(id));

    $('#selectedProjectId').val(id);

    $('#logprojectid').val(id);

    //$('#listdespatchorders').trigger('click') ;
    $('#receivvvorders').trigger('click') ;

});

$(function(){

    $('#listmovtoorders').click(function(){
        $.ajax({
            type: 'POST',
            url: '../projects/movetoorders',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            success: function(data){
                if(data.error=='No'&& data.check=="")
                {
                    $('#projectName-head').html(data.projectName);
                    $('#movetoorderitems').html(data.result);
                    $("#ndataz").css("display", "block");
                     $("#hdedataz").css("display", "block");

                }else if(data.error=='No' && data.check==1){
                    $('#projectName-head').html(data.projectName);
                    $('#movetoorderitems').html(data.result);
                   // $("#ndatazd").css("display", "block");
                   // $("#hdedataz").css("display", "none");
                    // $("#hdedata").css("display", "block");
                    
                    
                }
                $('.preloader').hide();
            }
        });
    });
    
    $('#listmovfromorders').click(function(){
        $.ajax({
            type: 'POST',
            url: '../projects/movefromorders',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            success: function(data){
                if(data.error=='No' && data.chkdata=="")
                {
                    $('#movefromorderitems').html(data.result);
                    $("#desp").css("display", "block");
                    $("#hdedata").css("display", "block");
                    $("#hdedataz").css("display", "none");
                     $("#ndataz").css("display", "none");
                     $("#mnndese").css("display", "block");
                    $("#movetoorderitems").css("display", "none");

                    
                    // $("#ndataz").css("display", "none");
                    // $("#mndes").css("display", "none");
                    
                    
                    $('.plantss').hide();
                  
                    // $("#desp").hide();

                    
                }else if(data.error=='No' && data.chkdata==1){

                    $('#movefromorderitems').html(data.result);
                    $("#desp").css("display", "block");
                    $("#ndatazd").css("display", "block");
                    $("#hdedata").css("display", "block");
                    $("#ndataz").css("display", "none");
                    // $("#mnndese").css("display", "block");
                    // $("#movetoorderitems").css("display", "none");
                    


                    // $("#ndataz").css("display", "none");
                    // $("#mndes").css("display", "none");
                    
                    
                    $('.plantss').hide();
                  
                    // $("#desp").hide();

                }
                $('.preloader').hide();
            }
        });
    });
    $(document).on('click','#receivvvorders',function(){
        $('#listmovtoorders').trigger('click') ;
        $('.plantss').show();
        $('#movetoorderitems').show();
        $('.operations-receive-orders-list-wrpr').show();
        $('.operations-despatch-orders-list-wrpr').hide();
    });

    $(document).on('click','input',function(){
        var orderid = [];
        var orderres = [];
        var order_res_ids = [];
        $('input:checked').each(function() {
            orderres.push($(this).val());
            orderid.push($(this).attr('data-id'));
            order_res_ids.push($(this).attr('data-order_res_id'));
        });
        $('[name="orderres"]').attr({value: orderres.join(', ')});
        $('[name="orderids"]').attr({value: orderid.join(', ')});
        $('[name="recorderres"]').attr({value: orderres.join(', ')});
        $('[name="recorderids"]').attr({value: orderid.join(', ')});
        $('[name="order_res_id"]').attr({value: order_res_ids.join(', ')});

    });
    $(document).on('click','#receiveeq',function(){
        if($('.darecequipments').is(":checked")) {
    
            var selresources=$('#recorderres').val();
            var selorders=$('#recorderids').val();
            var resources = selresources.split(',');
            var orders = selorders.split(',');
            var resource_arr = resources.slice().sort(); // You can define the comparing function here.
            var orders_arr = orders.slice().sort(); // You can define the comparing function here.
            var count='';
            if(orders.length == 2){
                //count=0;
                for (var i = 0; i < orders.length; i++) { 
                    if (parseInt(orders[i]) == parseInt(orders_arr[i])) {
                         count=0;
                     }
                     else {
                         count++;
                     }
                }
            }else{
                count=orders.length - 1;
            }
            if(count!=0){
                alert('Please select only one order');
                return false;
            }
            else {
    
                var error=0;
                $('.error').hide();
                if($('#dateofrec').val()==''){
                    $('#dateofrec').next('span').html('Select Date of receipt').show();
                    error=1;
                }
                if(error==0)
                {
                    $.ajax({
                          type: 'POST',
                        url: '../projects/receiveequipment',
                        beforeSend : function(){
                            $('#receiveeq').attr("disabled", true);
                        },
                        data: {orderid:$('#recorderids').val(),orderres:$('#recorderres').val(),date:$('#dateofrec').val()}, 
                        dataType: "json",   
                        success: function(data){
                            $('#receiveeq').attr("disabled", false);
                            $('#dateofrec').val('');
                            
                            $('#receiveeqmsg').html('Received Successfully!');
                            $("#receiveeqmsg").show().delay(5000).fadeOut();


                            $('#listmovtoorders').trigger('click') ;   
                        }  
                    });
                }
                else {
                    return false;
                }
            }
        }
        else {
            alert('Please select any Equipments before proceeding.');
            return false;
        }
    });



    $(document).on('click','#cancel_despatch',function(){
        if($('.daequipments').is(":checked")) {
            var selresources=$('#orderres').val();
            var selorders=$('#orderids').val();
            var order_res_id=$('#order_res_id').val();
            
            var resources = selresources.split(',');
            var orders = selorders.split(',');
            var resource_arr = resources.slice().sort(); // You can define the comparing function here.
            var orders_arr = orders.slice().sort(); // You can define the comparing function here.
            var count='';
            if(orders.length == 2){
                //count=0;
                for (var i = 0; i < orders.length; i++) { 
                    if (parseInt(orders[i]) == parseInt(orders_arr[i])) {
                         count=0;
                     }
                     else {
                         count++;
                     }
                }
            }else{
                count=orders.length - 1;
            }
            if(count!=0){
                alert('Please select only one order');
                return false;
            }
            else {
                
                if(confirm('Do you want to cancel the Despatch order?'))
                {
                    $.ajax({   
                        type: 'POST',
    
                        url: '../projects/cancelorderresource',
                        beforeSend : function(){
                            $('#cancel_despatch').attr("disabled", true);
                        },
                        data: {orderid:$('#orderids').val(),orderres:$('#orderres').val(),order_res_id:$('#order_res_id').val(),vehiclenum:$('#vehiclenum').val()},   
                        dataType: "json",   
                        success: function(data){
                            $('#cancel_despatch').attr("disabled", false);
                            $('#vehiclenum').val('');

                            $('#despatcheqmsg').html('Cancelled Successfully!');
                            $("#despatcheqmsg").show().delay(5000).fadeOut();

                            $('#listmovfromorders').trigger('click') ;  
                        }   
                    });
                }
                
                else {
                    return false;
                }
            }
        }
        else {
            alert('Please select any Equipments before proceeding.');
            return false;
        }
    });

    $(document).on('click','#despatcheq',function(){
        if($('.daequipments').is(":checked")) {
            var selresources=$('#orderres').val();
            var selorders=$('#orderids').val();
            var resources = selresources.split(',');
            var orders = selorders.split(',');
            var resource_arr = resources.slice().sort(); // You can define the comparing function here.
            var orders_arr = orders.slice().sort(); // You can define the comparing function here.
            var count='';
            if(orders.length == 2){
                //count=0;
                for (var i = 0; i < orders.length; i++) { 
                    if (parseInt(orders[i]) == parseInt(orders_arr[i])) {
                         count=0;
                     }
                     else {
                         count++;
                     }
                }
            }else{
                count=orders.length - 1;
            }
            if(count!=0){
                alert('Please select only one order');
                return false;
            }
            else {
                var error=0;
                $('.error').hide();
                if($('#vehiclenum').val()==''){
                    $('#vehiclenum').next('span').html('Enter Vehicle Number').show();
                    error=1;
                }
                if(error==0)
                {
                    $.ajax({   
                        type: 'POST',
    
                        url: '../projects/despatchequipment',
                        beforeSend : function(){
                            $('#despatcheq').attr("disabled", true);
                        },
                        data: {orderid:$('#orderids').val(),orderres:$('#orderres').val(),vehiclenum:$('#vehiclenum').val()},   
                        dataType: "json",   
                        success: function(data){
                            $('#despatcheq').attr("disabled", false);
                            $('#vehiclenum').val('');

                            $('#despatcheqmsg').html('Despatched Successfully!');
                            $("#despatcheqmsg").show().delay(5000).fadeOut();

                            $('#listmovfromorders').trigger('click') ;  
                        }   
                    });
                }
                
                else {
                    return false;
                }
            }
        }
        else {
            alert('Please select any Equipments before proceeding.');
            return false;
        }
    });

    $(document).on('focus','.datepicker',function(){
        $(this).datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
    });


});
$(document).on('change','.endreading',function(){
    id= $(this).data('id');

    val = $('#endreading'+id).val();

    if(val == '')
    {
        $('#hours'+id).attr('readonly', false);
    }else{
        $('#hours'+id).attr('readonly', true);
    }

    

});

$(document).on('change','.hourss',function(){
    id= $(this).data('id');

    val = $('#hours'+id).val();

    if(val == '')
    {
        $('#startreading'+id).attr('readonly', false);
        $('#endreading'+id).attr('readonly', false);
    }else{
        $('#startreading'+id).attr('readonly', true);
        $('#endreading'+id).attr('readonly', true);
    }

    

});