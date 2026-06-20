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
            // Auto-load Resource Types list in the piano tab
            setTimeout(function(){
                $('#listrestype').trigger('click');
            }, 200);
        } else {
            $('.menu-popup-cntnr').removeClass('active');
            $('body').css('overflow-y', 'auto');
            // Reset accordion tabs for next open
            $('.menu-popup-cntnr .panel-group .tab-content').removeClass('in');
            $('.menu-popup-cntnr .panel-group .tab-content').attr('aria-expanded', 'false');
        }
    });

    // Piano tab toggle for Resource Types
    $(document).on('click', '#restype-piano-toggle', function(){
        var $body = $('#restype-piano-body');
        var $caret = $('#restype-piano-caret');
        if($body.is(':visible')){
            $body.slideUp(150);
            $caret.html('&#9660;');
        } else {
            $body.slideDown(150);
            $caret.html('&#9650;');
            setTimeout(function(){ $('#listrestype').trigger('click'); }, 200);
        }
    });

    // Resource Groups tab heading clicked — load list
    $(document).on('click', '.resgroup-tab', function(){
        setTimeout(function(){
            if($('#reslib-group').hasClass('in')){
                $('#listresgroup').trigger('click');
            }
        }, 400);
    });

    // Resources tab heading clicked — load list
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
