$(document).on( "click", "#assetregistrtab", function(){
    $('#collapsesbill').removeClass('in');
    $('#assetreg_search').trigger('click');
});

$(function() { 
    $('#assetreg_search').click(function () {
        $.ajax({
            type: 'POST',
            url: '../report/assetregister',
            beforeSend: function () {
                $('.regstrpreloader').show();
            },
            dataType: "json",
            success: function (data) {
                if (data.error == 'No') {
                    $('#assetregister_items').html(data.result);
                    $('.regstrpreloader').hide();
                }
            }
        });
    });


});