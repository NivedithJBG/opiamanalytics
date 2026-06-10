$(document).on( "click", "#subcost", function(){

    

    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }
    $('#listsubcost').trigger('click') ;
});
$(function(){
    $('#listsubcost').click(function(){
        //$('#accountspiesection').slideUp('slow');// slide down the project listing div

        //$('#findashboardsection').slideDown('slow');// slide down the project listing div

        var error=0;

        $('.error').hide();
        //$('#findashproject').val(36);
        if($('#projmaterialscost').val()=='0')

        {

            $("#projmaterialscost").next("span").html('Select Place').show('slow');

            $('#subcostlistsection').slideUp('slow');

            error=1;

        }

        if(error==0)

        {

            $.ajax({

                type: 'POST',

                url: '../report/SubCost',
                beforeSend : function(){

                    $('.preloader').show();

                },

                dataType: "json",

                data: {project:$('#projmaterialscost').val()},

                success: function(data){

                    if(data.error=='No')

                    {
                        $('.preloader').hide();
                        $('#subcosttable').show();
                        $('#subcostitems').html(data.result);
                        //$('#pedashinfo').html(data.peinfo);

                    }

                }

            });
        }

    });
});