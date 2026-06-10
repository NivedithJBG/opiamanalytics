/**
 * Created by SolmindsDelli5 on 26-04-2019.
 */

$(document).on( "click", "#viewestresourcein", function(){

    //$('.acc_container').slideUp();

    $('#procurementreports').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

    //$(this).toggleClass('active').next().slideDown();

    $('#estresourcein').addClass('active').next('.acc_container').slideDown();

    $('#estresourcein').show();
    $('#estresourcesearch').trigger('click');

});
$(document).on( "click", "#estresourcein", function(){

    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

});

$(document).on('change','#estresourceproject',function(){
    $('#estresourcesearch').trigger('click');
});  

$(function() {
    $('#estresourcesearch').click(function () {
        
        var projectid = $('#estresourceproject').val();
        
       /* if(projectid=="none"){      
            $("#estresourceproject").val($("#estresourceproject option:eq(1)").val());
            var projectid = $('#estresourceproject').val();
        } */

        $.ajax({

            type: 'POST',

            url: '../Procurement/EstimateResourceslist',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",

            data: {project:projectid},

            success: function(data){

                if(data.error=='No')

                {

                    $('#estresourceitems').html(data.result);

                    $('#estresourcetable').show();

                }

                $('.preloader').hide();

            }

        });

    });

});

