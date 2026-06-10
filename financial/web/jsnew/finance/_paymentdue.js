$(document).on( "click", "#viewpaymentdue", function(){
	$('#paymentsearch').trigger('click');

});

$(function() {
    $('#paymentsearch').click(function () {
        $.ajax({
            type: 'POST',
            url: '../projects/paymentdue',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {},
            success: function(data){
                if(data.error=='No')
                {
                    $('#paymentdues').html(data.results);
                    
                }
                $('.preloader').hide();
            }
        });
    });
});

$(document).on( "click", ".paymnetdet", function(){
	var payval = $(this).attr('data-id');
	   $.ajax({
            type: 'POST',
            url: '../projects/paymentduetocashbank',
            beforeSend : function(){},
            dataType: "json",
            data: {id:payval},
            success: function(data){
                if(data.error=='No')
                {
                	alert("Saved")
                    $('#paymentsearch').trigger('click');
                    
                    
                }
                
            }
        });

	});