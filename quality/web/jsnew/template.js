/**
 * Created by SolmindsDelli5 on 1/18/2017.
 */
$(document).on('click','#template',function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#listtemplate').trigger('click');
    //return false; //Prevent the browser jump to the link anchor
});

$(function(){
    $('#listtemplate').click(function () {

        $.ajax({
            type: 'POST',
            url: '../doccumentManager/Listtemplate',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {name:$('#searchtemplate').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#templateitems').html(data.result);
                    $('#templatetable').show();
                }

                $('.preloader').hide();
            }
        });

    });
    $('#templatesearch').click(function(){
        $('#listtemplate').trigger('click')
    });
});

$(document).on( "click", ".deletetemp", function(){
    var idval=$(this).val();
    var r = confirm("Are you sure you want to delete this Template ?");
    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../doccumentManager/DeleteTemplate',
            beforeSend : function(){
                $('#deletetemp'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {tempid:idval},
            success: function(data){
                if(data.error=='No')
                {
                    $('#templaterow'+idval).remove();
                    $('#listtemplate').trigger('click');
                }
                $('#deletetemp'+idval).attr("disabled", false);
            }
        });

    } else {
        return false;
    }

});
