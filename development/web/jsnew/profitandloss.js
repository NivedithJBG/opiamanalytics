$(document).on( "click", "#viewprofitandloss", function(){
    //$('.acc_container').slideUp();
    $('#financereports').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
    //$(this).toggleClass('active').next().slideDown();
    $('#profitandloss').addClass('active').next('.acc_container').slideDown();
    $('#profitandloss').show();
    $('#projectexp').hide();
    $('#balancesheet').hide();
    $('#prlsection').hide();

});

$(document).on( "click", "#profitandloss", function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#prlsection').hide();
});
$(function(){
    $('#prlsearch').click(function(){
        var error=0;
        $('.error').hide();
        if($('#prlproject').val()=='0')
        {
            $("#prlproject").next("span").html('Select Project').show('slow');
            $('#prlsection').slideUp('slow');
            error=1;
        }
        if(error==0)
        {
            $('#prlsection').slideDown('slow');
            $.ajax({
                type: 'POST',
                url: '../FinanceRequests/Profitandloss',
                beforeSend : function(){
                    $('.preloader').show();
                },
                dataType: "json",
                data: {project:$('#prlproject').val(),fromdate:$('#prlfromdate').val(),todate:$('#prltodate').val()},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#prltable').show();
                        $('#prlitems').html(data.result);
                        $('#prlinfo').html(data.peinfo);
                        $('#printprofitloss').html(data.print);
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