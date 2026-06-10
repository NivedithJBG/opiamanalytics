$(document).on( "click", "#journals", function(){

    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#listjournals').trigger('click');
});
$(function(){
    $('#listjournals').click(function(){
        //$('#productaddsection').slideUp('slow');// slide down the project listing div
        $('#journallistsection').slideDown('slow');// slide down the project listing div
        $('#listjournals').removeClass('btn-danger').addClass('btn-success');
        $('#addjournal').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../FinanceRequests/journalsearch',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            //data: {name:$('#searchreceipt').val(),project:$('#searchreceipt').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#journelitems').html(data.result);
                    $('#journeltable').show();
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });
    });
    $('#savejournal').click(function(){
        var error=0;
        $('.error').hide();
        if($('#Narration').val()=='')
        {
            $("#Narration").next("span").html('Enter Narration').show('slow');
            error=1;
        }
        if($('#creditaccount').val()=='0')
        {
            $("#creditaccount").next("span").html('Select Credit Account').show('slow');
            error=1;
        }
        if($('#place').val()=='0')
        {
            $("#place").next("span").html('Select Place').show('slow');
            error=1;
        }
        if($('#projectid').val()=='0')
        {
            $("#projectid").next("span").html('Select Project').show('slow');
            error=1;
        }
        var debitotal=0;
        var creditotal=0;
        $('.debitamount').each(function(){
            var id=$(this).attr('data-id');
            debitotal=debitotal+$(this).val()*1;
            if($(this).val()=='')
            {
                $("#debitamount"+id).next("span").html('Enter Debit Amount').show('slow');
                error=1;
            }
        });
        $('.creditamount').each(function(){
            var id=$(this).attr('data-id');
            creditotal=creditotal+$(this).val()*1;
            if($(this).val()=='')
            {
                $("#creditamount"+id).next("span").html('Enter Credit Amount').show('slow');
                error=1;
            }
        });
        if (debitotal!=creditotal)
        {
            alert("Debit amount and credit amount must be same");
            error=1;
        }

        $('.debitaccount').each(function(){

            var id=$(this).attr('data-id');

            var creditaccount=$("#creditaccount").val();

            if($(this).val()==creditaccount)
            {

                $("#debitaccount"+id).next("span").html('Cannot select same account head as credit   ').show('slow');
                error=1;
            }
        });


        if(error==0)
        {
            $.ajax({
                type: 'POST',
                url: '../FinanceRequests/journals',
                dataType: "json"
            });
        }
        else
        {
            return false;
        }

    });
    $('#saveandcreate').click(function(){
        var error=0;
        $('.error').hide();
        if($('#Narration').val()=='')
        {
            $("#Narration").next("span").html('Enter Narration').show('slow');
            error=1;
        }
        if($('#creditaccount').val()=='0')
        {
            $("#creditaccount").next("span").html('Select Credit Account').show('slow');
            error=1;
        }
        if($('#place').val()=='0')
        {
            $("#place").next("span").html('Select Place').show('slow');
            error=1;
        }
        if($('#projectid').val()=='0')
        {
            $("#projectid").next("span").html('Select Project').show('slow');
            error=1;
        }
        $('.amount').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='')
            {
                $("#Amount"+id).next("span").html('Enter Amount').show('slow');
                error=1;
            }
        });
        $('.debitaccount').each(function(){

            var id=$(this).attr('data-id');

            var creditaccount=$("#creditaccount").val();

            if($(this).val()==creditaccount)
            {

                $("#debitaccount"+id).next("span").html('Cannot select same account head as credit   ').show('slow');
                error=1;
            }
        });


        if(error==0)
        {
            $.ajax({
                type: 'POST',
                url: '../FinanceRequests/journals',
                dataType: "json"
            });
        }
        else
        {
            return false;
        }

    });
    $(document).on('click','.deletejournalbutton',function(){
        var journalid=$(this).val();
        var r = confirm("Are you sure you want to delete this Journal ?");
        if (r == true) {
            $.ajax({
                type: 'POST',
                url: '../FinanceRequests/Journaldelete/',
                beforeSend : function(){
                    $('#deletejournalbutton'+journalid).attr("disabled", true);
                },
                dataType: "json",
                data: {journalid:journalid},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#journalrow'+data.Id).remove();
                        $('#listjournals').trigger('click');
                    }
                    else
                    {
                        alert(data.errortext);
                    }

                    $('#deletejournalbutton'+data.Id).attr("disabled", false);
                }
            });
        }
        else {
            return false;
        }
    });
    /*$(document).on( "blur",".debitamount", function(){
        var totalrate=0;
        $('.debitamount').each(function(){
            totalrate=totalrate+$(this).val()*1;
        });
        $('#amount').html(totalrate);
        $('#creditamount0').val(totalrate);
    });
    $(document).on( "blur",".creditamount", function(){
        var totalrate=0;
        $('.creditamount').each(function(){
            totalrate=totalrate+$(this).val()*1;
        });

        $('#amount').html(totalrate);
        $('#debitamount0').val(totalrate);
    });*/
});