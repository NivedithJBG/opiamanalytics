$(document).on( "click", ".acco-ten input[type=radio]", function(){  

    
        
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
$(document).on( "click", "#trialbalnc", function(){

  

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

    $('#trialfromdate').attr('value', today);
    $('#trialtodate').attr('value', today);

  $('#tbresourcetypesearch').trigger('click');

  });

$(function(){
    // $( "#trialfromdate" ).datepicker({
    //     defaultDate:new Date(),changeMonth: true,
    //     changeYear: true,dateFormat: 'dd-mm-yy'
    // });
    // $( "#trialtodate" ).datepicker({
    //     maxDate: new Date(),changeMonth: true,
    //     changeYear: true,dateFormat: 'dd-mm-yy'
    // });

    $(document).on('change','.datepickerfrom',function(){
        $('#dfrom').text($(this).val());
    });
    $(document).on('change','.datepickerto',function(){
        $('#dto').text($(this).val());
    });

  
    $(document).on('click','#tbresourcetypesearch',function(){
        
        if($('#trialfromdate').val() != '' && $('#trialtodate').val()){

            $.ajax({

                type: 'POST',

                url: '../financerequests/trialbalance',

                beforeSend : function(){

                    //$('#projectsearch').attr("disabled", true);

                    $('#fin-preloader-trialtab').show();

                },

                dataType: "json",

                data: {fromdate:$('#trialfromdate').val(),todate:$('#trialtodate').val()},

                success: function(data){

                    if(data.error=='No')

                    {
                        //$('#trialinfo').html(data.trial);
                        $('#trial-body').html(data.result);

                        //$('#trialtable').show();

                    // $('#printtrial').html(data.print);
                        //$('#exporttrial').html(data.export);

                        //$('#trialinfo').html(data.trial);

                    }

                    else

                    {

                        alert(data.errortext);

                    }

                    $('#fin-preloader-trialtab').hide();

                }

            });
    }else{

    }

    });

 });