$(document).on( "click", "#viewbalancesheet", function(){
    //$('.acc_container').slideUp();
    $('#financereports').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
    //$(this).toggleClass('active').next().slideDown();
    $('#balancesheet').addClass('active').next('.acc_container').slideDown();
    $('#balancesheet').show();
    $('#bssection').hide();

});

$(document).on( "click", "#balancesheet", function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#bssection').hide();
});
$(function(){
    $('#bssearch').click(function(){
        var error=0;
        $('.error').hide();

        if(error==0)
        {
            $('#bssection').slideDown('slow');
            $.ajax({
                type: 'POST',
                url: '../FinanceRequests/balancesheet',
                beforeSend : function(){
                    $('.preloader').show();
                },
                dataType: "json",
                data: {project:$('#bsproject').val(),fromdate:$('#bsfromdate').val(),todate:$('#bstodate').val()},
                success: function(data){

                    if(data.error=='No')
                    {
                        $('#bstable').show();
                        $('#bsitems').html(data.result);
                        $('#bsinfo').html(data.peinfo);
                        $('#printbalance').html(data.print);
                    }
                    else
                    {
                        alert(data.errortext);
                    }
                    $('.preloader').hide();
                }
            });
        }
    });
});