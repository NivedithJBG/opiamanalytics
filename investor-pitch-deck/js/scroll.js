$(document).ready(function () {

    $('a[href^="#site-main"]').addClass('active');

//smoothscroll
    $('#hidden-submenu  li a').on('click', function (e) {
        e.preventDefault();
        //  $(document).off("scroll");
        var athis = this;
        var target = this.hash,
                menu = target;
        $target = $(target);
        $('html, body').stop().animate({
            'scrollTop': $target.offset().top + 2
        }, 800, 'swing', function () {
            window.location.hash = target;
            $('#hidden-submenu  li a').removeClass('active');
            $(athis).addClass('active');

        });

    });


    $(window).scroll(function (event) {
        var scrollPos = $(document).scrollTop();
        if (scrollPos === 0)
        {
            $('a[href^="#site-main"]').addClass('active');
            return;
        }

        $('#hidden-submenu  li a').each(function () {
            var currLink = $(this);
            var refElement = $(currLink.attr("href"));
            if (refElement.position().top <= scrollPos && refElement.position().top + refElement.height() > scrollPos) {
                $('#hidden-submenu  li a').removeClass("active");
                currLink.addClass("active");
            } else {
                currLink.removeClass("active");
            }
        });

    })

});


/*$(document).ready(function () {
    $(document).on("scroll", onScroll);
    
    //smoothscroll
    $('a[href^="#"]').on('click', function (e) {
        e.preventDefault();
        $(document).off("scroll");
        
        $('a').each(function () {
			
            $(this).removeClass('active');
        })
        $(this).addClass('active');
      
        var target = this.hash,
            menu = target;
        $target = $(target);
        $('html, body').stop().animate({
            'scrollTop': $target.offset().top
        }, 500, 'swing', function () {
            window.location.hash = target;
            $(document).on("scroll", onScroll);
        });
    });
});	

function onScroll(event){
    var scrollPos = $(document).scrollTop()+150;
    $('#hidden-submenu a').each(function () {
        var currLink = $(this);
        var refElement = $(currLink.attr("href"));
        if (refElement.position().top <= scrollPos && refElement.position().top + refElement.height() > scrollPos) {
            $('#hidden-submenu  li a').removeClass("active");
            currLink.addClass("active");
        }
        else{
            currLink.removeClass("active");
        }
    });
}*/