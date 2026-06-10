$(document).on( "click", "#directworkerscost", function(){

    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }
    $('#listdirectworkerscost').trigger('click') ;
});
$(function(){
    $('#listdirectworkerscost').click(function(){
        //$('#accountspiesection').slideUp('slow');// slide down the project listing div

        //$('#findashboardsection').slideDown('slow');// slide down the project listing div

        var error=0;

        $('.error').hide();
        //$('#findashproject').val(36);
        if($('#projdirectworkerscost').val()=='0')

        {

            $("#projdirectworkerscost").next("span").html('Select Place').show('slow');

            $('#directworkerscostlistsection').slideUp('slow');

            error=1;

        }

        if(error==0)

        {

            $.ajax({

                type: 'POST',

                url: '../report/directworkersCost',
                beforeSend : function(){

                    $('.preloader').show();

                },

                dataType: "json",

                data: {project:$('#projdirectworkerscost').val()},

                success: function(data){

                    if(data.error=='No')

                    {
                        $('.preloader').hide();
                        $('#directworkerscosttable').show();
                        $('#directworkerscostitems').html(data.result);
                        //$('#pedashinfo').html(data.peinfo);

                    }

                }

            });
        }

    });
});