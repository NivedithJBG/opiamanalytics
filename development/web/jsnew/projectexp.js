$(document).on( "click", "#viewprojectexpd", function(){

    //$('.acc_container').slideUp();

    $('#financereports').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

    //$(this).toggleClass('active').next().slideDown();

    $('#projectexp').addClass('active').next('.acc_container').slideDown();

    $('#projectexp').show();

    $('#pesection').hide();



});



$(document).on( "click", "#projectexp", function(){

    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    $('#pesection').hide();

});

$(function(){

    $('#pesearch').click(function(){

        var error=0;

        $('.error').hide();

        if($('#projexpproject').val()=='0')

        {

            $("#projexpproject").next("span").html('Select Place').show('slow');

            $('#pesection').slideUp('slow');

            error=1;

        }

        if(error==0)

        {

            $('#pesection').slideDown('slow');

            $.ajax({

                type: 'POST',

                url: '../FinanceRequests/ProjectExpenditure',

                beforeSend : function(){

                    $('.preloader').show();

                },

                dataType: "json",

                data: {project:$('#projexpproject').val(),fromdate:$('#pefromdate').val(),todate:$('#petodate').val()},

                success: function(data){

                    if(data.error=='No')

                    {

                        $('#petable').show();

                        $('#peitems').html(data.result);

                        $('#peinfo').html(data.peinfo);

                        $('#printprojexp').html(data.print);

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
$(document).on('click','.hover',function(){    
    var tooltip=$(this).attr('data-tooltip');    
    $('.tooltiptable').hide();
    $('#'+tooltip).fadeIn('fast');
    // alert('#'+tooltip);
});
$(document).on('mouseleave','.hover',function(){
    var tooltip=$(this).attr('data-tooltip');
    $('#'+tooltip).fadeOut('slow');
});
