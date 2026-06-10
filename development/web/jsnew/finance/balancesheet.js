$(document).on( "click", ".balance-sheet-tab", function(){  

    
        
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


$(document).on( "click", "#finbsreports", function(){

    $('#bsreportsearch').trigger('click');

});

$(function(){
  
    $(document).on('click','#bsreportsearch',function(){
        
            $.ajax({

                type: 'POST',

                url: '../financerequests/bsreport',

                beforeSend : function(){

                    $('#fin-preloader-bsreporttab').show();

                },

                dataType: "json",

                data: {},

                success: function(data){

                    if(data.error=='No')

                    {
                        $('#finbsreport-body').html(data.result);
                    }

                    else

                    {
                        alert(data.errortext);
                    }

                    $('#fin-preloader-bsreporttab').hide();

                }

            });

    });

 });