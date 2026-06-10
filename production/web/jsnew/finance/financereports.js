$(document).on( "click", "#financereports", function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
});

$(document).on( "click", "#viewprojectexpd", function(){

    $('#projectexp').show();

});

$(function(){

    $('#pesearch').click(function(){

        var error=0;

        $('.error').hide();

        if($('#projexpproject').val()=='0')

        {

            $("#rep_place_error").html('Select Place').show('slow');

            $('#pesection').slideUp('slow');

            error=1;

        }

        if(error==0)

        {

            $('#pesection').slideDown('slow');

            $.ajax({

                type: 'POST',

                url: '../financerequests/projectexpenditure',

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