$(document).on( "click", ".acco-eleven input[type=radio]", function(){  

    
        
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
  
  
});
$(document).on( "click", "#profit_n_loss", function(){

  

  var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth()+1; //January is 0!

    var yyyy = today.getFullYear();
    if(dd<10)
    {
        dd='0'+dd
    } 
    if(mm<10){
        mm='0'+mm
    } 

    today = yyyy+'-'+mm+'-'+dd;

    $('#plfromdate').attr('value', today);
    $('#pltodate').attr('value', today);

  $('#plresourcetypesearch').trigger('click');

  });

$(function(){
    // $( "#plfromdate" ).datepicker({
    //     defaultDate:new Date(),changeMonth: true,
    //     changeYear: true,dateFormat: 'dd-mm-yy'
    // });
    // $( "#pltodate" ).datepicker({
    //     maxDate: new Date(),changeMonth: true,
    //     changeYear: true,dateFormat: 'dd-mm-yy'
    // });

    $(document).on('change','.datepickerfrom',function(){
        $('#dfrom').text($(this).val());
    });
    $(document).on('change','.datepickerto',function(){
        $('#dto').text($(this).val());
    });

  
    $(document).on('click','#plresourcetypesearch',function(){
        
        if($('#plfromdate').val() != '' && $('#pltodate').val()){

            $.ajax({

                type: 'POST',

                url: '../financerequests/profitnloss',

                beforeSend : function(){

                    //$('#projectsearch').attr("disabled", true);

                    $('#fin-preloader-pltab').show();

                },

                dataType: "json",

                data: {fromdate:$('#plfromdate').val(),todate:$('#pltodate').val()},

                success: function(data){

                    if(data.error=='No')

                    {
                        $('#pl-body').html(data.result);
                    }

                    else

                    {
                        alert(data.errortext);
                    }

                    $('#fin-preloader-pltab').hide();

                }

            });
    }else{

    }

    });

 });