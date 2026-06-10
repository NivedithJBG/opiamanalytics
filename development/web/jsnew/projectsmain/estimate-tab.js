$(document).on( "click", ".acco-two input[type=radio]", function(){  
   
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
       $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
       //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
   }
   if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
       $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
       $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
       
   }
});


$(document).ready(function(){
  $('input.projectvalue').keyup(function(event){
      // skip for arrow keys
      if(event.which >= 37 && event.which <= 40){
          event.preventDefault();
      }
      var $this = $(this);
      var num = $this.val().replace(/,/gi, "").split("").reverse().join("");
      
      var num2 = RemoveRougeChar(num.replace(/(.{3})/g,"$1,").split("").reverse().join(""));
      
      console.log(num2);
      
      
      // the following line has been simplified. Revision history contains original.
      $this.val(num2);
  });
});

function RemoveRougeChar(convertString){
    
    
    if(convertString.substring(0,1) == ","){
        
        return convertString.substring(1, convertString.length)            
        
    }
    return convertString;
    
}
$(document).on( "click", ".view_estimate", function(){
  $('.resource-back-button').trigger('click') ;
  });



$(document).on( "click", ".est_purchase", function(){

  var error = true;
  $(".quantity").each(function() {
    if($(this).val() != '' && parseInt($(this).val()) != 0)
          error = false;
  });
  
  if(error)
  {
    alert("Please Enter a Valid Quantity");
  }else{
    $.ajax({
          type: 'POST',
          url: '../projectsmain/estpurchaseorder',
          dataType: "json",
          data: {projectid:$('#estimateProject_Id').val(),},
          success: function(data){
              if(data.error=='No')
              {
                $('.confirm-purchase').html('Ordered Successfully!');
                $(".confirm-purchase").show().delay(5000).fadeOut();
              }
              else{
                $('.confirm-purchase').html('No resources found!');
                $(".confirm-purchase").show().delay(5000).fadeOut();
              }
          }
    });
  }
});
