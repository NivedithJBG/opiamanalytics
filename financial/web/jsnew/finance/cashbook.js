$(document).on( "click", "#cashbook", function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#cashbooksection').hide();

});
$(document).on( "click", "#cash-book", function(){

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

        $('#fromdate').attr('value', today);
        $('#todate').attr('value', today);

        //$('#fromdate').val(today);
        //$('#todate').val(today);

        $('#cashbooksearch').trigger('click');

        /*$('#cashbookaccounthead').val('');

         $.ajax({
                type: 'POST',
                url: '../financerequests/currentcashbook',
                beforeSend : function(){
                    $('.preloader').show();
                },
                dataType: "json",
                data: {},
                success: function(data){
                    if(data.error=='No')
                    {
                        
                        $('#curr-cash-booklist').html(data.result);
                    }
                    else
                    {
                        alert(data.errortext);
                    }
                    $('.preloader').hide();
                }
            });*/
});

$(document).on( "change", "#cashbookaccounthead", function(){  

   $('#cashbooksearch').trigger('click');

});

$(document).on( "click", "#cashbookaccounthead", function(){  

    var searchvalue = $('#cashbookaccounthead').val();

    if(searchvalue!=''){
        $('#cashbookaccounthead').val('');
        $('#cashbooksearch').trigger('click');
    }

});


$(function(){
    $('#cashbooksearch').click(function(){
        var error=0;
        $('.error').hide();
        /*if($('#place').val()=='0')
        {
            $("#bank_bookhead").next("span").html('Select Place').show('slow');
            $('#cashbooksection').slideUp('slow');
            error=1;
        }*/
        var shownVal = document.getElementById("cashbookaccounthead").value;
        if(shownVal){
            var accountid = document.querySelector("#cashbookaccountheadlist option[value='"+shownVal+"']").dataset.value;
        }
        else{
            var accountid = null;
        }
        
        if(error==0)
        {
            $('#cashbooksection').slideDown('slow');
            $.ajax({
                type: 'POST',
                url: '../financerequests/cashbook',
                beforeSend : function(){
                    $('.preloader').show();
                },
                dataType: "json",
                data: {account:accountid,fromdate:$('#fromdate').val(),todate:$('#todate').val()},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#cashbookitems').html(data.result);
                        $('#curr-cash-booklist').hide();
                        //$('#cashbooktable').show();
                    }
                    else
                    {
                        alert(data.errortext);
                    }
                    $('.preloader').hide();
                }
            });
        }
    });
});