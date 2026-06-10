$(document).on( "click", "#receipt", function(){

    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#listreceipt').trigger('click');
});
$(function(){
    //test();
    $('#listreceipt').click(function(){
        //$('#productaddsection').slideUp('slow');// slide down the project listing div
        $('#receiptlistsection').slideDown('slow');// slide down the project listing div
        $('#listreceipt').removeClass('btn-danger').addClass('btn-success');
        $('#addreceipt').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../FinanceRequests/receiptsearch',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {name:$('#searchreceipt').val(),project:$('#searchreceipt').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#receiptitems').html(data.result);
                    $('#receipttable').show();
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });
    });

    $('#receiptsearch').click(function(){
        $('#listreceipt').trigger('click');
    });

    $('#savereceipt').click(function(){
        var error=0;
        $('.error').hide();
        if($('#Amount').val()=='')
        {
            $("#Amount").next("span").html('Enter Amount').show('slow');
            error=1;
        }
        if($('#Purpose').val()=='')
        {
            $("#Purpose").next("span").html('Enter Purpose').show('slow');
            error=1;
        }
        if($('#receiptproject').val()=='0')
        {
            $("#receiptproject").next("span").html('Select Project').show('slow');
            error=1;
        }
        if($('#place').val()=='0')
        {
            $("#place").next("span").html('Select Place').show('slow');
            error=1;
        }

        if(error==0)
        {
            $.ajax({
                type: 'POST',
                url: '../FinanceRequests/Fundreceipt',
                dataType: "json"
            });
        }
        else
        {
            return false;
        }

    });
    $('#receiptapprove').click(function(){
        var error=0;
        if($('#receiptstatus').val()=='0')
        {
            $("#receiptstatus").next("span").html('Select Status').show('slow');
            error=1;
        }
        if(!$('.paymenttype'+':checked').val())
        {
            alert('Select Payment Mode');
            error=1;
        }
        if($('#accountshead').val()=='0')
        {
            $("#accountshead").next("span").html('Select Account head').show('slow');
            error=1;
        }
        if(error==0)
        {
            $.ajax({
                type: 'POST',
                url: '../FinanceRequests/Receiptapprove',
                dataType: "json"
            });
        }
        else
        {
            return false;
        }

    });
    $(document).on('click','.deletefundrecptbutton',function(){
        var recptid=$(this).val();
        var r = confirm("Are you sure you want to delete this Receipt ?");
        if (r == true) {
            $.ajax({
                type: 'POST',
                url: '../FinanceRequests/Receiptdelete/',
                beforeSend : function(){
                    $('#deletefundrecptbutton'+recptid).attr("disabled", true);
                },
                dataType: "json",
                data: {recptid:recptid},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#receiptrow'+data.Id).remove();
                        $('#listreceipt').trigger('click');
                    }
                    else
                    {
                        alert(data.errortext);
                    }

                    $('#deletefundrecptbutton'+data.Id).attr("disabled", false);
                }
            });
        }
        else {
            return false;
        }
    });
});
