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
            // Activate Resource Types tab by default
            activateReslibTab('type');
        } else {
            $('.menu-popup-cntnr').removeClass('active');
            $('body').css('overflow-y', 'auto');
        }
    });

    // Piano tab switching
    $(document).on('click', '.reslib-tab-btn', function(){
        var tab = $(this).data('tab');
        activateReslibTab(tab);
    });

    function activateReslibTab(tab){
        $('.reslib-tab-btn').removeClass('active');
        $('.reslib-tab-btn[data-tab="' + tab + '"]').addClass('active');
        $('.reslib-tab-content').removeClass('active');
        $('#reslib-tab-' + tab).addClass('active');
        setTimeout(function(){
            if (tab === 'type')  $('#listrestype').trigger('click');
            if (tab === 'group') $('#listresgroup').trigger('click');
            if (tab === 'res')   $('#listresource').trigger('click');
        }, 100);
    }

    $(document).on('click', '.menu-win-close', function(e){
        e.preventDefault();
        $('.overNow').removeClass('active');
        $('.menu-popup-cntnr').removeClass('active');
        $('body').css('overflow-y', 'auto');
    });

});
