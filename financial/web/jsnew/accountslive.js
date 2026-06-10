$(document).on('click','#Accounts',function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#listaccounts').trigger('click');
    //return false; //Prevent the browser jump to the link anchor
});

$(function(){
    // project section function
    // list project click
    $('#listaccounts').click(function(){
        $('#accountsaddsection').slideUp('slow');// slide down the project listing div
        $('#accountslistsection').slideDown('slow');// slide down the project listing div
        $('#listaccounts').removeClass('btn-danger').addClass('btn-success');
        $('#addaccounts').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../AccountsItem/search',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {accounts:$('#searchaccounts').val(),subgrpid:$('#accountsubgrp').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#accountsitems').html(data.result);
                    $('#accountstable').show();
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });

    });
    $('#accountsearch').click(function(){
        $('#listaccounts').trigger('click')
    });
    /*$('#addaccounts').click(function(){
        $('#accountslistsection').slideUp('slow');// slide down the project listing div
        $('#accountsaddsection').slideDown('slow');// slide down the project listing div
        $('#addaccounts').removeClass('btn-danger').addClass('btn-success');
        $('#listaccounts').removeClass('btn-success').addClass('btn-danger');

    });*/
    $(document).on( "change","#accountgrps", function(){
        $.ajax({
            type: 'POST',
            url: '../AccountsItem/AccountSubgroups',
            dataType:"json",
            data:{grpid:$('#accountgrps').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#subgroups').html(data.result);

                }
                else
                {
                    alert(data.error);
                }

            }
        });
    });
    /*$(function() {
        $('#accountgrps').change(function(e) {
            var selected = $(e.target).val();
            alert(selected);
        });
    });*/
    $('#saveaccounts').click(function(){
        var error=0;
        $('.error').hide();
        if($('#accountsname').val()=='')
        {
            $("#accountsname").next("span").html('Enter Account Name').show('slow');
            error=1;
        }
        if(AccountName($('#accountsname').val())=='Yes')
        {
            $('#accountsname').next("span").html('Account Name Exists').show('slow');
            error=1;
        }
        if($('#accounttds').val()=='')
        {
            $("#accounttds").next("span").html('Enter TDS').show('slow');
            error=1;
        }
        if($('#accountservtax').val()=='')
        {
            $("#accountservtax").next("span").html('Enter Service Tax').show('slow');
            error=1;
        }

        if(error==0){
            /*if ($('#cash').is(':checked')) {
              var  account_type=1;
            }
            if ($('#bank').is(':checked')) {
                var  account_type=2;
            }*/
            if ($('#schedule').is(':checked')) {
                var  schedule=3;
            }

            $.ajax({
                type:'POST',
                url:'../AccountsItem/create',
                beforeSend:function(){
                    $('#saveaccounts').attr("disabled", true);
                },
                dataType:'json',
                data: {accountname:$('#accountsname').val(),accounttds:$('#accounttds').val(),servicetax:$('#accountservtax').val(),account_type:$('#accounttype').val(),schedule:schedule},
                success:function(data){
                    if(data.error=='No')
                    {
                        $('#accountsform')[0].reset();
                        $('#listaccounts').trigger('click');
                        $('#saveaccounts').attr("disabled", false);
                        /*$('#searcselecttype').append($('<option>', {
                         value: data.Id,
                         text: data.Name
                         }));*/
                    }
                    else
                    {
                        alert(data.errortext);
                    }
                    $('#saveaccounts').attr("disabled", false);
                }
            });
        }
    });
    $('.accountype').change(function() {
        if ($('.accountype').is(':checked')) {
            $('#projectlist').show();
        }
        else{
            $('#projectlist').hide();
        }


    });
});

function AccountName(name)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../AccountsItem/checkname',
        async:false,
        data: {name:name},
        success: function(data){
            retval=data;
        }
    });
    return retval;

}
$(document).on( "click", ".editaccountsbutton", function(){
    var idval=$(this).val();
    $('#editaccountsname'+idval).show();
    $('#editaccountstds'+idval).show();
    $('#editaccountservtax'+idval).show();
    $('#editaccountype'+idval).show();
    $('#editschedule'+idval).show();
    $('#saveaccountsbutton'+idval).show();
    $('#accountstext'+idval).hide();
    $('#accountstdstext'+idval).hide();
    $('#accountservtaxtext'+idval).hide();
    $('#accountype'+idval).hide();
    $('#schedule'+idval).hide();
    $('#editaccountsbutton'+idval).hide();
} );
$(document).on( "click", ".saveaccountsbutton", function(){
    var idval=$(this).val();
    var error=0;
    $('.error').hide();
    if($('#editaccountsname'+idval).val()=='')
    {
        $('#editaccountsname'+idval).next("span").html('Enter Name').show('slow');
        error=1;
    }
    /*if($('#editaccountype'+idval).val()=='0')
    {
        $('#editaccountype'+idval).next("span").html('Select Accounthead').show('slow');
        error=1;
    }*/
    if($('#editaccountstds'+idval).val()=='')
    {
        $('#editaccountstds'+idval).next("span").html('Enter TDS').show('slow');
        error=1;
    }
    if($('#editaccountservtax'+idval).val()=='')
    {
        $('#editaccountservtax'+idval).next("span").html('Enter Service Tax').show('slow');
        error=1;
    }
    /*var  account_type=0;
    if ($('#cash'+idval).is(':checked')) {
        account_type=1;
    }
    if ($('#bank'+idval).is(':checked')) {
        account_type=2;
    }*/

    if ($('#schedulecheck'+idval).is(':checked')) {
        var  schedule=3;
    }

    if(error==0){
        $.ajax({
            type: 'POST',
            url: '../AccountsItem/update',
            beforeSend : function(){
                $('#saveaccountsbutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {accountid:idval,accountname:$('#editaccountsname'+idval).val(),accounttds:$('#editaccountstds'+idval).val(),servicetax:$('#editaccountservtax'+idval).val(),account_type:$('#editaccountype'+idval).val(),schedule:schedule},
            success: function(data){
                if(data.error=='No')
                {
                    $('#editaccountsname'+data.Id).hide();
                    $('#editaccountstds'+data.Id).hide();
                    $('#editaccountservtax'+data.Id).hide();
                    $('#editaccountype'+data.Id).hide();
                    $('#editschedule'+data.Id).hide();
                    $('#editacntsubgrp'+data.Id).hide();
                    $('#saveaccountsbutton'+data.Id).hide();
                    $('#accountstext'+data.Id).text($('#editaccountsname'+data.Id).val()).show();
                    $('#accountstdstext'+data.Id).text($('#editaccountstds'+data.Id).val()).show();
                    $('#accountservtaxtext'+data.Id).text($('#editaccountservtax'+data.Id).val()).show();
                    $('#accountype'+data.Id).text(data.type).show();
                    $('#schedule'+data.Id).text(data.scheduletype).show();
                    $('#acntsubgrpstext'+data.Id).text($('#editacntsubgrp'+idval+' option:selected').text()).show();
                    $('#editaccountsbutton'+data.Id).show();
                    //$("select#searcselecttype option[value='"+data.Id+"']").remove();
                    /*$('#searcselecttype').append($('<option>', {
                     value: data.Id,
                     text: data.Name
                     }));*/

                }
                else
                {
                    alert(data.errortext);
                }

                $('#saveaccountsbutton'+data.Id).attr("disabled", false);
            }
        });
    }

});
$(document).on( "click", ".deleteaccountsbutton", function(){
    var idval=$(this).val();
    var r = confirm("Are you sure you want to delete this Account ?");
    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../AccountsItem/DeleteItem',
            beforeSend : function(){
                $('#deleteaccountsbutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {accountid:idval},
            success: function(data){
                if(data.error=='No')
                {
                    $('#accountsrow'+data.Id).remove();
                    $('#listaccounts').trigger('click');
                    //$("select#searcselecttype option[value='"+data.Id+"']").remove();
                }
                else
                {
                    alert(data.errortext);
                }

                $('#deleteaccountsbutton'+data.Id).attr("disabled", false);
            }
        });

    } else {
        return false;
    }

});
/*$(document).on('click','.childaccounts',function(){
 var resid=$(this).val();
 //$('#searcselecttype').val(resid);
 //$('#searcselectvendor').val('none');
 $('#Accountsubgroups').trigger('click');
 });*/
