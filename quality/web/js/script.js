$(function() {

    $('#ms').magicSuggest({
        maxSelection:1,
        data: '../Voucher/Getaccounts',
        valueField: 'id',
        displayField: 'name'
    });
    $('#credit').magicSuggest({
        maxSelection:1,
        data: '../Voucher/Getaccounts',
        valueField: 'id',
        displayField: 'name'
    });
    $('#debit').magicSuggest({
        maxSelection:1,
        data: '../Voucher/Getaccounts',
        valueField: 'id',
        displayField: 'name'
    });
    $('#bank').magicSuggest({
        maxSelection:1,
        data: '../Voucher/bankaccounts',
        valueField: 'id',
        displayField: 'name'
    });
    $('#resources').magicSuggest({
        maxSelection:10,
        ///data: '../Voucher/bankaccounts',
        valueField: 'id',
        displayField: 'name'
    });
    $('#users').magicSuggest({
        //maxSelection:1,
        required: true,
        data: '../Task/Users',
        valueField: 'id',
        displayField: 'username'
    });
    var ms = $('#users').magicSuggest({
        valueField: 'id',
        data: 'data.json'
    });
    $(ms).on(
        'selectionchange', function(e, cb, s){

            if(cb.getValue()==''){
                $('#user').val('none');
                $('#userinfo').show('slow');
            }
            else {
                $('#user').val(1);
                $('#userinfo').hide('slow');
            }
        }
    );

});