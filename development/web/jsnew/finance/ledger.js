$(document).on( "click", "#ledger", function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#ledgersection').hide();
});
$(document).on( "click", "#ledgerlist", function(){
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

    //$('#ledgerfromdate').attr('value', today);
    //$('#ledgertodate').attr('value', today);

     //$('#ledgersearch').trigger('click');

    //$('#accounthead').val(0);
    $('#ledgerplace').val(0);
    $('#projectid').val(0);
    $('#ledgeritems').html('');
    $('#ledgerfromdate').val('');
    $('#ledgertodate').val('');


    });
$(function(){
    $('#ledgersearch').click(function(){
        var error=0;
        $('.error').hide();
        /*if($('#ledgerplace').val()=='0')
        {
            $("#ledger_place").html('Select Place').show('slow');
            $('#ledgersection').slideUp('slow');
            error=1;
        }*/
        if($('#accounthead').val()=='')
        {
            $("#ledger_accnthead").html('Select Account head').show('slow');
            $('#ledgersection').slideUp('slow');
            error=1;
        }

        if(error==0)
        {
            if($('#ledgertodate').val()=='')
            {
                var today = new Date();
                var dd = today.getDate();

                var mm = today.getMonth()+1;
                var yyyy = today.getFullYear();
                if(dd<10)
                {
                    dd='0'+dd;
                }

                if(mm<10)
                {
                    mm='0'+mm;
                }
                today = yyyy+'-'+mm+'-'+dd;
                //alert(today)
                //$("#ledgertodate").val(today);
            }
            $('#ledgersection').slideDown('slow');

            var shownVal = document.getElementById("accounthead").value;
            if(shownVal){
                var accountid = document.querySelector("#ledgeraccountheadlist option[value='"+shownVal+"']").dataset.value;
            }
            else{
                var accountid = null;
            }



            $.ajax({
                type: 'POST',
                url: '../financerequests/ledger',
                beforeSend : function(){
                    $('.preloader').show();
                },
                dataType: "json",
                data: {place:$('#ledgerplace').val(),accountid:accountid,projectID:$('#projectid').val(),fromdate:$('#ledgerfromdate').val(),todate:$('#ledgertodate').val()},
                success: function(data){
                    if(data.error=='No')
                    {
                        //$('#ledgertable').show();
                        $('#ledgeritems').html(data.result);
                        //$('#accountname').html(data.accountname);
                        //$('#printledger').html(data.print);
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

$(document).on('click','.deleteledger',function(){
    if(confirm('Are you sure you want to delete this ledger entry?'))
    {
            var item=$(this).val();
            var type=$(this).attr('data-type');
            $.ajax({
                method: 'POST',
                url: '../financerequests/deleteledger',
                dataType: "json",
                data: {item:item,type:type},
                success: function(data){
                    if(data.error=='no')
                    {
                        $("#ledgersearch").trigger("click");
                    }
                    else
                    {
                        alert(data.errortext);
                    }
                    $('.preloader').hide();
                }
            });        
    }
    else
    {
       return false;
    }
});