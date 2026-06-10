$(document).on( "click", "#supportcost", function(){

    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }
    $('#listsupportcost').trigger('click') ;
});
$(function(){
    $('#listsupportcost').click(function(){
        //$('#accountspiesection').slideUp('slow');// slide down the project listing div

        //$('#findashboardsection').slideDown('slow');// slide down the project listing div

        var error=0;

        $('.error').hide();
        //$('#findashproject').val(36);
        if($('#projsupportcost').val()=='0')

        {

            $("#projsupportcost").next("span").html('Select Place').show('slow');

            $('#supportcostlistsection').slideUp('slow');

            error=1;

        }

        if(error==0)

        {

            $.ajax({

                type: 'POST',

                url: '../report/supportCost',
                beforeSend : function(){

                    $('.preloader').show();

                },

                dataType: "json",

                data: {project:$('#projsupportcost').val()},

                success: function(data){

                    if(data.error=='No')

                    {
                        $('.preloader').hide();
                        $('#supportcosttable').show();
                        $('#supportcostitems').html(data.result);
                        //$('#pedashinfo').html(data.peinfo);

                    }

                }

            });
        }

    });
});