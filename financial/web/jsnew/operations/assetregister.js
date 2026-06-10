$(document).on( "click", "#AssetregisterTab", function(){
    $('#assetregsearch').trigger('click');
});

$(function() { 
    $('#assetregsearch').click(function () {
        $.ajax({
            type: 'POST',
            url: '../report/assetregister',
            beforeSend: function () {
                $('.preloader').show();
            },
            dataType: "json",
            success: function (data) {
                if (data.error == 'No') {
                    $('#assetregisteritems').html(data.result);
                    $('.preloader').hide();
                }
            }
        });
    });


});