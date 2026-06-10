$(document).on( "click", "#schedule", function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#schedulesection').hide();
});

$(document).on( "change",".schaccnthead", function(){
    var accnthead = $('#schaccnthead').val();
    $.ajax({

        type: 'POST',

        url: '../FinanceRequests/Scheduleitems',

        beforeSend : function(){

            $('.preloader').show();

        },

        dataType: "json",

        data: {accnthead:accnthead},

        success: function(data){

            if(data.error=='No')

            {
                $('#scheduleitem').html(data.result);
            }

            $('.preloader').hide();

        }

    });
});

$(function(){
    $('#schedulesearch').click(function(){
        var error=0;
        $('.error').hide();
        if($('#scheduleprjct').val()=='0')
        {
            $("#scheduleprjct").next("span").html('Select Project').show('slow');
            $('#schedulesection').slideUp('slow');
            error=1;
        }
        if($('#schaccnthead').val()=='0')
        {
            $("#schaccnthead").next("span").html('Select Account head').show('slow');
            $('#schedulesection').slideUp('slow');
            error=1;
        }

        if(error==0)
        {
            $('#schedulesection').slideDown('slow');
            $.ajax({
                type: 'POST',
                url: '../FinanceRequests/schedule',
                beforeSend : function(){
                    $('.preloader').show();
                },
                dataType: "json",
                data: {projectid:$('#scheduleprjct').val(),accountid:$('#schaccnthead').val(),scheduleitem:$('#scheduleitem').val()},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#scheduletable').show();
                        $('#scheduleitems').html(data.result);
                        $('#accountname').html(data.accountname);
                        $('#printschedule').html(data.print);
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