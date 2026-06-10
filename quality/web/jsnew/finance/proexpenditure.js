$(document).on( "click", ".expenditure-report-tab", function(){  

    
        
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
       $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
       //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
   }
   if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
       $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
       $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
       
   }
      // $(this).parents('.panel-group').addClass('acco-four-active');
        $(this).parents('.panel-group').removeClass('acco-one-active');
        $(this).parents('.panel-group').removeClass('acco-two-active');
        $(this).parents('.panel-group').removeClass('acco-three-active');
        $(this).parents('.panel-group').removeClass('acco-five-active');
        $(this).parents('.panel-group').removeClass('acco-six-active');
        $(this).parents('.panel-group').removeClass('acco-seven-active');
        $(this).parents('.panel-group').removeClass('acco-eight-active');
        $(this).parents('.panel-group').removeClass('acco-nine-active');
        $(this).parents('.panel-group').removeClass('acco-ten-active');
  
  
});


$(document).on( "click", "#finexpreports", function(){

    $('#expreportsearch').trigger('click');

});

$(document).on( "click", "#proexpotypesearch", function(){

    $('#expreportsearch').trigger('click');

});

$(function(){
  
    $(document).on('click','#expreportsearch',function(){
        
            $.ajax({

                type: 'POST',

                url: '../financerequests/proexpenditure',

                beforeSend : function(){

                    $('#fin-preloader-expreporttab').show();

                },

                dataType: "json",

                data: {from: $('#proexpofromdate').val(), to: $('#proexpotodate').val()},

                success: function(data){

                    if(data.error=='No')

                    {
                        $('#finexpreport-body').html(data.result);
                    }

                    else

                    {
                        alert(data.errortext);
                    }

                    $('#fin-preloader-expreporttab').hide();

                }

            });

    });

 });