$(document).on( "click", ".audit-log", function(){
    $('#listaudit').trigger('click') ;
});

$(function(){ 
    $('#listaudit').click(function(){
        $.ajax({
            type: 'POST',
            url: '../projectsmain/resourceauditlog',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {},
            success: function(data){
                if(data.error=='No')
                {
                    $("#auditlog_items").html(data.result);
                }

                $('.preloader').hide();
            }
        });
    });

});