$(function(){

    $(document).on('click', '.navbar-nav .overNow', function(e){
        e.preventDefault();

        // Close any other open overlay
        var others = {
            '.overNow4': '.menu4-popup-cntnr',
            '.overNow2': '.finmenu-popup-cntnr',
            '.overNow8': '.menu8-popup-cntnr',
            '.overNow9': '.menu9-popup-cntnr',
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
        $('.overNow').toggleClass('active');

        if($('.overNow').hasClass('active')){
            $('.menu-popup-cntnr').addClass('active');
            $('body').css('overflow-y', 'hidden');
            // No tab auto-opens — user clicks to open
        } else {
            $('.menu-popup-cntnr').removeClass('active');
            $('body').css('overflow-y', 'auto');
            // Collapse all tabs when library is closed
            $('#reslib-type, #reslib-group, #reslib-res').removeClass('in').attr('aria-expanded', 'false');
        }
    });

    // Load list when each tab is expanded
    $(document).on('click', '.restype-tab', function(){
        setTimeout(function(){
            if($('#reslib-type').hasClass('in')){
                $('#listrestype').trigger('click');
            }
        }, 400);
    });

    $(document).on('click', '.resgroup-tab', function(){
        setTimeout(function(){
            if($('#reslib-group').hasClass('in')){
                $('#listresgroup').trigger('click');
            }
        }, 400);
    });

    $(document).on('click', '.resres-tab', function(){
        setTimeout(function(){
            if($('#reslib-res').hasClass('in')){
                $('#listresource').trigger('click');
            }
        }, 400);
    });

    $(document).on('click', '.menu-win-close', function(e){
        e.preventDefault();
        $('.overNow').removeClass('active');
        $('.menu-popup-cntnr').removeClass('active');
        $('body').css('overflow-y', 'auto');
    });

});
