$(document).on( "click", "#bankbook", function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#bankbooksection').hide();

});

$(document).on( "click", "#bank_book", function(){

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

    $('#bankbookfromdate').attr('value', today);
    $('#bankbooktodate').attr('value', today);

    //$('#fromdate').val(today);
    //$('#todate').val(today);

    $('#bankbooksearch').trigger('click');

        /*$('#bankbookaccounthead').val('');
    

         $.ajax({
                type: 'POST',
                url: '../financerequests/currentbankbook',
                beforeSend : function(){
                    $('.preloader').show();
                },
                dataType: "json",
                data: {},
                success: function(data){
                    if(data.error=='No')
                    {
                        
                        $('#current-bank-list').html(data.result);
                    }
                    else
                    {
                        alert(data.errortext);
                    }
                    $('.preloader').hide();
                }
            });*/
});

$(document).on( "change", "#bankbookaccounthead", function(){  

   $('#bankbooksearch').trigger('click');

});

$(document).on( "click", "#bankbookaccounthead", function(){  

    var searchvalue = $('#bankbookaccounthead').val();

    if(searchvalue!=''){
        $('#bankbookaccounthead').val('');
        $('#bankbooksearch').trigger('click');
    }

});

$(function(){
    $('#bankbooksearch').click(function(){
        var error=0;
        $('.error').hide();
        var place = $('#bankproject').val();
        var project = $('#bankbookproject').val();
        /*if(place=='0' && project=='0')
        {
            $("#cash_bookhead").next("span").html('Select Place or Project').show('slow');
            $('#bankbooksection').slideUp('slow');
            error=1;
        }*/
        var shownVal = document.getElementById("bankbookaccounthead").value;
        if(shownVal){
            var accountid = document.querySelector("#bankbookaccountheadlist option[value='"+shownVal+"']").dataset.value;
        }
        else{
            var accountid = null;
        }
        if($('#bank').val()=='0')
        {
            $("#bank").next("span").html('Select Bank').show('slow');
            $('#bankbooksection').slideUp('slow');
            error=1;
        }
        if(error==0)
        {
            $('#bankbooksection').slideDown('slow');
            $.ajax({
                type: 'POST',
                url: '../financerequests/bankbook',
                beforeSend : function(){
                    $('.preloader').show();
                },
                dataType: "json",
                data: {account:accountid,bankid:$('#bank').val(),fromdate:$('#bankbookfromdate').val(),todate:$('#bankbooktodate').val()},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#bankbookitems').html(data.result);
                        $('#current-bank-list').hide();
                        //$('#bankhead').html(data.projectname);
                        //$('#printbank').html(data.print);
                        //$('#bankbooktable').show();
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