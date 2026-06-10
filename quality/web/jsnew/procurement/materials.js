$(document).on( "click", "#Materials", function(){
    $('#resourcesMaterial').trigger('click').addClass('active');
    $('#resourcesMaterial').parent('.frstcl').addClass('active');
});

$(function() {
    $('.resourceTypeTab').click(function () {
        //var prj = $('.repor').attr('data-project-id');
        var type = $(this).attr('data-resource-type');
        $.ajax({
            type: 'POST',
            url: '../procurement1/getresourcesbytype',
            dataType: "json",
            data: {resourcetype_Id:type},
            success: function (data) {
                if (data.error == 'No') {
                    $('#resource-list').html(data.result);                         
                }
            }
        });
    });
});

$(document).on('change','.resources',function(){
    vendorid = $(this).attr('data-vendorid');
    if ($(this).is(':checked')) {
        $(".resources").attr("disabled", true);
        $(".resource_vendor_"+vendorid).removeAttr("disabled");
        $('#sel-vendorid').val(vendorid);
    }
    else{
        checked = false;
        $( ".resources" ).each(function( index ) {
          if ($(this).is(':checked')) checked = true;
        });
        if(!checked){
            $(".resources").removeAttr("disabled");
            $('#sel-vendorid').val('');
        }
    }

});


$(document).on('click','#placeorder_new',function(){
    if($('.resources').is(":checked")) {
        var venid = $('#sel-vendorid').val();
        var proid = $('#sel-projectid').val();
        var resid = [];
        var cartid = [];

        $('.resources:checked').each(function() {
            resid.push($(this).attr('data-resid'));
            cartid.push($(this).attr('data-cartid'));
        });

        $.ajax({ 
        type: 'POST',
        url: '../procurement1/ordernew',
        dataType: "json",
        data: {venid: venid,proid: proid,mode:1,resid:resid.join(', '),cartid:cartid.join(', ')},
        success: function (data) {
            if (data.error == 'No') {

                    $('#resource-list').hide();
                    $('#order-view').show();
                    $('#order-view').html(data.purchaseorder);  
                    
            }
        }
    });

    }else{
        alert('Please select any resource before proceeding.');
         return false;
    }

    

});