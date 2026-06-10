$(function(){
    $('#accountsearch').click(function(){
        var name=$('#accountname').val();
        $.ajax({
            type: 'POST',
            url: '../AccountsSub/AccountSearch',
            beforeSend : function(){
                $('#accountsearch').attr("disabled", true);
                $('.preloader').show();
            },
            dataType: "json",
            data: {name:name,subgrpid:$('#subgrpid').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#accountlists').show();
                    $('#accountitems').html(data.result);
                }
                else
                {
                    alert(data.errortext);
                }

                $('#accountsearch').attr("disabled", false);
                $('.preloader').hide();
            }
        });

    });
});
$(document).on('click','.addaccount',function(){
    var accountid=$(this).val();

    $.ajax({
        type: 'POST',
        url: '../AccountsSub/Addaccount',
        beforeSend : function(){
            $('#addaccount'+accountid).attr("disabled", true);
            $('.preloaderitems').show();
        },
        dataType: "json",
        data: {accountid:accountid,subgrpid:$('#subgrpid').val()},
        success: function(data){
            if(data.error=='No')
            {
                $('#addedaccounts').html(data.result);
                $('#accountrow'+accountid).remove();
            }
            else
            {
                alert(data.errortext);
            }

            $('#addaccount'+accountid).attr("disabled", false);
            $('.preloaderitems').hide();
        }
    });
});
$(document).on('click','.removeaccount',function(){
    var id=$(this).val();
    $.ajax({
        type: 'POST',
        url: '../AccountsSub/Removeaccount',
        beforeSend : function(){
            $('#removeaccount'+id).attr("disabled", true);
        },
        dataType: "json",
        data: {id:id},
        success: function(data){
            if(data.error=='No')
            {
                $('#tempaccountrow'+id).remove();
            }
            else
            {
                alert(data.errortext);
            }

            $('#removeaccount'+id).attr("disabled", false);
        }
    });
});