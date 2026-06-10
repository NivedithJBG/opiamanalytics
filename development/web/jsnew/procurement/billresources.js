/**
 * Created by SolmindsDelli5 on 01-08-2017.
 */
$(document).on( "click", "#billresources", function(){
    history.replaceState(null, null, ' ');

    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }
    $('.panel-group').removeClass('acco-cart-active');
    //$('.panel-group').addClass('acco-vendor-active');
    

    $('#resourcessearch').trigger('click');

});


$(function() {
    $('#resourcessearch').click(function () {

        $.ajax({

            type: 'POST',

            url: '../procurement/searchresources',

            beforeSend : function(){

                $('.preloader').show();

            },

            dataType: "json",
            
            success: function(data){

                if(data.error=='No')

                {

                    $('#resourceitems').html(data.result);

                    $('#resourcetable').show();

                }

                $('.preloader').hide();

            }

        });

    });

});

$(document).on( "click", ".deletebillresource", function(){
    var jobid=$(this).val();
    var r = confirm("Are you sure you want to delete this Resource ?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../procurement/deleteresource',
            dataType: "json",
            data: {jobid: jobid},
            success: function (data) {
                if (data.error == 'No') {
                    $('#resourcessearch').trigger('click');

                }
            }
        });
    }
    else {
        return false;
    }
});
$(document).on('click','.editqtybutton',function(){
  
    var idval=$(this).attr('data-v');
   

    $('#resqtyy'+idval).hide();
    $('#editqtyress'+idval).show();
    $('#editbillresource'+idval).hide();
    $('#savereqty'+idval).show();

    });

$(document).on('click','.saveqtybutton',function(){
    var idval=$(this).attr('data-v');
    var error=0;
    $('.error').hide();

    if(error==0)
        {
            $.ajax({
                type: 'POST',
                url: '../procurement/updatebill',
                beforeSend : function(){
                    $('#savereqty'+idval).attr("disabled", true);
                },
                dataType: "json",
                data: {id:idval,quantity:$('#editqtyress'+idval).val()},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#resqtyy'+data.Id).show();
                        $('#editqtyress'+data.Id).hide();
                        $('#editbillresource'+data.Id).show();
                        $('#savereqty'+data.Id).hide();
                        $('#resqtyy'+data.Id).text(data.Quantity);
    
                    }
                    else
                    {
                        alert(data.errortext);
                    }
    
                    $('#savereqty'+data.Id).attr("disabled", false);
                }
            });
        }
});