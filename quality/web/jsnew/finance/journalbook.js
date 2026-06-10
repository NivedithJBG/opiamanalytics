$(document).on( "click", "#journalbook", function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#journalbooksection').hide();

});
$(document).on( "click", "#journal-book", function(){

    $('#journalproject').val(0);

    $('#journalaccounthead').val('');

         $.ajax({
                type: 'POST',
                url: '../financerequests/currentjournalbook',
                beforeSend : function(){
                    $('.preloader').show();
                },
                dataType: "json",
                data: {},
                success: function(data){
                    if(data.error=='No')
                    {
                        
                        $('#curr-journalbook-list').html(data.result);
                    }
                    else
                    {
                        alert(data.errortext);
                    }
                    $('.preloader').hide();
                }
            });
});
$(function(){
    $('#journalbooksearch').click(function(){
        var error=0;
        $('.error').hide();
        /*if($('#journalproject').val()=='0')
        {
            //$("#journalproject").next("span").html('Select Place').show('slow');
            $("#journal_bookhead").next("span").html('Select Place').show('slow');
            $('#journalbooksection').slideUp('slow');
            error=1;
        }*/

        var shownVal = document.getElementById("journalaccounthead").value;
        if(shownVal){
            var accountid = document.querySelector("#journalaccountheadlist option[value='"+shownVal+"']").dataset.value;
        }
        else{
            var accountid = null;
        }

            
        if(error==0)
        {
            $('#journalbooksection').slideDown('slow');
            $.ajax({
                type: 'POST',
                url: '../financerequests/journalbook',
                beforeSend : function(){
                    $('.preloader').show();
                },
                dataType: "json",
                data: {id:$('#journalproject').val(),account:accountid,fromdate:$('#journalbookfromdate').val(),todate:$('#journalbooktodate').val()},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#journalbookitems').html(data.result);
                        $('#curr-journalbook-list').hide();
                        //$('#journalhead').html(data.projectname);
                        //$('#printjournal').html(data.print);
                        //$('#journalbooktable').show();
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
