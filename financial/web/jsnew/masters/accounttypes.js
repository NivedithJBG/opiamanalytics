
$(document).on( "click", "#acc-types", function(){ 
        //$('.acco-one').on('click', function(){
            //acco-confirmorders
            
       if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
            $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
            //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
        }
        if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
            $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
            $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
        }


        $('#listaccounttypes').trigger('click');
                    
        $(this).parent('.panel-group').addClass('acco-one-active');
        $(this).parent('.panel-group').removeClass('acco-two-active');
        $(this).parent('.panel-group').removeClass('acco-three-active');
        $(this).parent('.panel-group').removeClass('acco-four-active');
       // $(this).parent('.panel-group').removeClass('acco-five-active');
       // $(this).parent('.panel-group').removeClass('acco-six-active');
            
            
});



$(function(){
    // project section function
    // list project click
    $('#listaccounttypes').click(function(){    

        $('.accounttype-tab').removeClass('addAccountTypeForm-active');
        $('#accounttypesaddsection').slideUp('slow');// slide down the project listing div
        $('#accounttypeslistsection').slideDown('slow');// slide down the project listing div
        $('#listaccounttypes').removeClass('btn-danger').addClass('btn-success');
        $('#addaccountgroups').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../accountsitem/accounttypes',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {acntgrpname:$('#searchaccounttypes').val()},  
            success: function(data){
                if(data.error=='No')
                {
                    $('#accounttypesitems').html(data.result);
                   // $('#accounttypestable').show();
                }

                $('.preloader').hide();
            }
        });

    });
    $('#accounttypessearch').click(function(){
        $('#listaccounttypes').trigger('click')
    });
    $('#addaccounttypes').click(function(){
        $('#accounttypeslistsection').slideUp('slow');// slide down the project listing div
        $('#accounttypesaddsection').slideDown('slow');// slide down the project listing div
        $('#addaccounttypes').removeClass('btn-danger').addClass('btn-success');
        $('#listaccounttypes').removeClass('btn-success').addClass('btn-danger');

    });
    $('#saveaccounttypes').click(function(){
        var error=0;
        $('.error').hide();
        if($('#accounttypesname').val()=='')
        {
            $("#accounttypesname").next("span").html('Enter Account Type Name').show('slow');
            error=1;
        }

        if(error==0){
            $.ajax({
                type:'POST',
                url:'../accountsitem/createaccountype',
                beforeSend:function(){
                    $('#saveaccounttypes').attr("disabled", true);
                },
                dataType:'json',
                data: {accounttypename:$('#accounttypesname').val()},
                success:function(data){
                    if(data.error=='No')
                    {
                        $( "#account_heads_listing" ).load(window.location.href + " #account_heads_listing" );
                        $('#accounttypesform')[0].reset();
                        $('#listaccounttypes').trigger('click');
                        $('#saveaccounttypes').attr("disabled", false);
                    }
                }
            });
        }
    });
});

$(document).on( "click", ".editacnttypesbutton", function(){
    var idval=$(this).val();
    $('#editacnttypesname'+idval).show();
    $('#saveacnttypesbutton'+idval).show();
    $('#acnttypestext'+idval).hide();
    $('#editacnttypesbutton'+idval).hide();
} );

$(document).on( "click", ".saveacnttypesbutton", function(){
    var idval=$(this).val();
    var error=0;
    $('.error').hide();
    if($('#editacnttypesname'+idval).val()=='')
    {
        $('#editacnttypesname'+idval).next("span").html('Enter Name').show('slow');
        error=1;
    }
    if(error==0){
        $.ajax({
            type: 'POST',
            url: '../accountsitem/updateaccounttype',
            beforeSend : function(){
                $('#saveacnttypesbutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {acnttypeid:idval,name:$('#editacnttypesname'+idval).val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#editacnttypesname'+idval).hide();
                    $('#saveacnttypesbutton'+idval).hide();
                    $('#acnttypestext'+idval).text($('#editacnttypesname'+idval).val()).show();
                    $('#editacnttypesbutton'+idval).show();
                }

                $('#saveacnttypesbutton'+idval).attr("disabled", false);
            }
        });
    }

});
$(document).on( "click", ".deletecnttypesbutton", function(){
    var idval=$(this).val();
    var r = confirm("Are you sure you want to delete this Account Type ?");
    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../accountsitem/deleteaccounttype',
            beforeSend : function(){
                $('#deletecnttypesbutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {acnttypeid:idval},
            success: function(data){
                if(data.error=='No')
                {
                    $('#acnttypesrow'+idval).remove();
                    $('#listaccounttypes').trigger('click');
                }

                $('#deletecnttypesbutton'+idval).attr("disabled", false);
            }
        });

    } else {
        return false;
    }

});
$(document).on('click','.accountheads',function(){ 
    var accounttypeid=$(this).val();
    $('#accounttype').val(accounttypeid);
    $('#accountsubgrp').val('none');
    $('#searchaccounts').val('');
    $('#acc-heads').trigger('click'); 
    //$('#Accounts').trigger('click');
    //$('#listaccounts').trigger('click');

    $('#identify').val('accounttype');

    $('#collapseaccnttyp').removeClass('in');

    $('.accounttype-tab').removeClass('active');

    $('.accountheads-tab').addClass('active');

    $('#collapseaccnts').addClass('in');

    $("#collapseaccnttyp").attr("aria-expanded","false");
    
    $("#collapseaccnts").attr("aria-expanded","true");

    $('#collapseaccnts').css('height','');
});
