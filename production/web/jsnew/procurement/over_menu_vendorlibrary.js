$(function(){

    $(document).on('click', '.navbar-nav .overNowVendorLib', function(e){
        e.preventDefault();

        // Close any other open overlay
        var others = {
            '.overNow':  '.menu-popup-cntnr',
            '.overNow4': '.menu4-popup-cntnr',
            '.overNow2': '.finmenu-popup-cntnr',
            '.overNow8': '.menu8-popup-cntnr',
            '.overNow5': '.menu5-popup-cntnr'
        };
        $.each(others, function(cls, cntnr){
            if($(cls).hasClass('active')){
                $(cls).removeClass('active');
                $(cntnr).removeClass('active');
                $('body').css('overflow-y', 'auto');
            }
        });

        jQuery('body').removeClass('menu-active');
        $('.overNowVendorLib').toggleClass('active');

        if($('.overNowVendorLib').hasClass('active')){
            $('.vendorlib-popup-cntnr').addClass('active');
            $('body').css('overflow-y', 'hidden');
        } else {
            $('.vendorlib-popup-cntnr').removeClass('active');
            $('.po-preview-cntnr').removeClass('active');
            $('body').css('overflow-y', 'auto');
            $('#vendorliblistsection').html('');
            $('#vendor-list-body').html('<tr><td colspan="8" style="text-align:center;padding:30px;color:#aaa;font-size:13px;border:1px solid #eee;">Select a Resource Type to view vendors</td></tr>');
            $('#search-restype').val('0');
            $('#search-resid').html('<option value="0">-- Resource --</option>').prop('disabled', true);
        }
    });

});
