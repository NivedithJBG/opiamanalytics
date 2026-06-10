/**
 * Created by SolmindsDelli5 on 25-02-2020.
 */
$(document).on('click','#Expenseaccounts',function(){

    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    $('#expaccountsearch').trigger('click');

    //return false; //Prevent the browser jump to the link anchor

});

$(function() {

    // project section function

    // list project click

    $('#expaccountsearch').click(function () {

        //$('#accountsaddsection').slideUp('slow');// slide down the project listing div

        $('#expaccountslistsection').slideDown('slow');// slide down the project listing div

        //$('#listaccounts').removeClass('btn-danger').addClass('btn-success');

        //$('#addaccounts').removeClass('btn-success').addClass('btn-danger');

        $.ajax({

            type: 'POST',

            url: '../AccountsItem/Expensesearch',

            beforeSend: function () {

                $('.preloader').show();

            },

            dataType: "json",

            data: {
                accounts: $('#searchexpaccounts').val(),
                subgrpid: $('#expaccountsubgrp').val()
            },

            success: function (data) {

                if (data.error == 'No') {

                    $('#expaccountsitems').html(data.result);

                    $('#expaccountstable').show();

                }


                $('.preloader').hide();

            }

        });


    });

});

$(document).on("click",".linkexpresunitbtn",function(){

    var idval=$(this).val();
    $('#linkexpaccount_id').val(idval);
    var accountid=$('#linkexpaccount_id').val();
    $.ajax({

        type: 'POST',

        url: '../AccountsItem/getacntresitem',

        dataType:"json",

        data:{account_id:accountid},

        success: function(data){

            if(data.error=='No')

            {
                $('#msexpacntname').html(data.accname);
                $('#expaccountsubgrplink').html(data.accval);
                $('#expresourcegrouplink').html(data.resourcetypelist);
                $('.preloader').hide();
            }

        }

    });

});

$(document).on( "change", ".expaccountsubgrplink", function(){
    var accountgroup=$(this).val();
        $.ajax({
            type: 'POST',
            url: '../AccountsItem/getresourcegroup',
            dataType:"json",
            data: {subgroup:accountgroup},
            success: function(data){
                $('#expresourcegrouplink').html(data.resourcetypelist);
            }
        });

});

$(document).on( "click", "#updatelinkexpaccount", function(){
    $.ajax({
        type: 'POST',
        url: '../AccountsItem/Updateresitem',
        data: $( "#expaccountlinkform" ).serialize(),
        success: function(data){
            $('#LinkExpRes').modal('toggle');
            $('#expaccountsearch').trigger('click');
        }
    });
});
