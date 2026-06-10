$(document).ready(function () {
			
			//check the orientation and add a relative class to body
			window.onresize = function (event) {
			    applyOrientation();
			  }
			  window.onload = function (event) {
			    applyOrientation();
			  }
			  
			  $('.navbar-toggler-icon').on('click', function(){
					$('.navbar-toggler-icon').toggleClass('active');
				})
	
	
	
			  
			  function applyOrientation() {
			    if (window.innerHeight > window.innerWidth) {
			        $('body').addClass('portrait');	  
			        $('body').removeClass('landscape');
			    } else {
			        $('body').removeClass('portrait');	  
			        $('body').addClass('landscape');
			    }
  			}
			
			// detect devices and add a relative class to body 
			var customizeForDevice = function(){
			var ua = navigator.userAgent;
			var checker = {
			  iphone: ua.match(/(iPhone|iPod)/),
			  ipad: ua.match(/(iPad)/),
			  blackberry: ua.match(/BlackBerry/),
			  android: ua.match(/Android/)
			};
			if (checker.android){
				$('body').addClass('android');
			}
			else if (checker.iphone){
				$('body').addClass('iphone');
			}
			else if (checker.ipad){
				$('body').addClass('ipad');
			}
			else {
				$('body').addClass('desktop');
			}
		}
		customizeForDevice();
		
		// Detect browser and version and add a relative class to body
		navigator.sayswho= (function(){
			var ua= navigator.userAgent, tem, 
			M= ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];

			if(/trident/i.test(M[1])){
				tem=  /\brv[ :]+(\d+)/g.exec(ua) || [];
				return 'IE '+(tem[1] || '');
			}
			if(M[1]=== 'Chrome'){
				tem= ua.match(/\b(OPR|Edge)\/(\d+)/);
				if(tem!= null) return tem.slice(1).join('').replace('OPR', 'Opera');
			}
			M= M[2]? [M[1], M[2]]: [navigator.appName, navigator.appVersion, '-?'];
			if((tem= ua.match(/version\/(\d+)/i))!= null) M.splice(1, 1, tem[1]);
			return M.join('-');
		})();

		
		$('body').addClass(navigator.sayswho);
		
		navigator.sayswho2= (function(){
			var ua= navigator.userAgent, tem,
			M= ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];
			if(/trident/i.test(M[1])){
				tem=  /\brv[ :]+(\d+)/g.exec(ua) || [];
				return 'IE '+(tem[1] || '');
			}
			if(M[1]=== 'Chrome'){
				tem= ua.match(/\b(OPR|Edge)\/(\d+)/);
				if(tem!= null) return tem.slice(1).join(' ').replace('OPR', 'Opera');
			}
			M= M[2]? [M[1], M[2]]: [navigator.appName, navigator.appVersion, '-?'];
			if((tem= ua.match(/version\/(\d+)/i))!= null) M.splice(1, 1, tem[1]);
			return M.join(' ');
		})();
		
		$('body').addClass(navigator.sayswho2);
		
		
		
		//$( ".tempform input" ).focus(function(e) {
		  //$('.getstarted_form').addClass('active');
		 // $('.tempform input').blur();
		  
		//});
		
		$('.tempformWrpr').on('click', function () {
				$(this.hash).addClass('active').focus();
				setTimeout(function(){ 
					$('.getstarted_form form .your-name input').focus();
				}, 500);
				
			});
		
		
		
		
		
		
		
		
		/*var prevScrollpos = window.pageYOffset;
		window.onscroll = function() {
		var currentScrollPos = window.pageYOffset;
		  if (prevScrollpos > currentScrollPos) {
			document.getElementById("navbar").style.top = "0";
			$('body').removeClass('winScrolling');
			$('body').addClass('scrollmenu-active');
		  } else {
			document.getElementById("navbar").style.top = "-145px";
			$('body').addClass('winScrolling');
			$('body').removeClass('scrollmenu-active');
		  }
		  prevScrollpos = currentScrollPos;
		  console.log(currentScrollPos);
		  
		  if (currentScrollPos > 780){
				$('#navbar').addClass('secondfold');
			} else {
				$('#navbar').removeClass('secondfold');
				
			}
		if (currentScrollPos == 0){
				$('#navbar').addClass('menuontop');
			} else {
				$('#navbar').removeClass('menuontop');
				
			}
		  
		}
		*/
		
		
		
		var previousScroll = 0;
        $(window).scroll(function() {
    		
            var currentScroll = $(this).scrollTop();
    		if (currentScroll > 210) {
                NavTrans()
            } else {
    			removeNavTrans();
				
    		};
            if (currentScroll < 80) {
                showTopNav();
            } else if (currentScroll > 0 && currentScroll < $(document).height() - $(window).height()) {
                if (currentScroll > previousScroll) {
                    hideNav()
					
                } else {
                    showNav()
					
                }
                previousScroll = currentScroll
            }
    		
        });
	
	function hideNav() {
        $("#navbar").removeClass("is-visible").addClass("is-hidden")
            }
            function showNav() {
                $("#navbar").removeClass("is-hidden").addClass("is-visible").addClass("scrolling")
            }
            function showTopNav() {
                $("#navbar").removeClass("is-hidden").addClass("is-visible").removeClass("scrolling")
            }
            function NavTrans() {
				$("#navbar").addClass("whiteNav");
				$(".page-event-submemnu.hidden-submenu").addClass("showCustom-menu");
    }
    function removeNavTrans() {
        $("#navbar").removeClass("whiteNav");
		$(".page-event-submemnu.hidden-submenu").removeClass("showCustom-menu");
		
    }
	
		
		
		$('.gotoTop').each(function(){
			$(this).click(function(){ 
				$('html,body').animate({ scrollTop: 0 }, 'slow');
				return false; 
			});
		});
		
		
		$(function() {
			$('a[href*=\\#]:not([href=\\#])').on('click', function() {
						var target = $(this.hash);
						target = target.length ? target : $('[name=' + this.hash.substr(1) +']');
						if (target.length) {
							$('html,body').animate({
								scrollTop: target.offset().top
							}, 1000);
							return false;
						}
					});
		});
		
		
		
			
			$('.signup-wrpr .signup, .login-myaccount').click(function (event) {
					
				var _opened = $(".navbar-collapse").hasClass("navbar-collapse collapse show");
				
				if (_opened === true ) {
					$(".navbar-toggler").trigger( "click" );
				}
			});
 
 
			/*search Nov 23 2020*/
 
			//$('.search-btn-wrpr .custom-search').click(function (event) {
					
				//$('.custom-search-results-wrpr').addClass('active');
				//$('body').addClass('custom-search-active');
				
			//});
			
			$('.custom-search-win-close').click(function (event) {
					
				$('.custom-search-results-wrpr').removeClass('active');
				$('body').removeClass('custom-search-active');
				
			});
  
			/*search Nov 23 2020 end*/
  
  
			/*search Nov 24 2020 start*/
			$('.header-search input').focus(function (event) {
				
				$('.custom-search-results-wrpr').addClass('active');
				
			});
			
			
			$('body').click(function (event) {	
				$('.custom-search-results-wrpr').removeClass('active');
				$('body').removeClass('custom-search-active');
				//$('.navbar-collapse.collapse').removeClass('show');
				setTimeout(function() {
					//$('.navbar-collapse.collapse').removeClass('show');				
					//alert('df');
				},1000);
				
			});
			
			$('.custom-search-body, .custom-search').click(function (event) {	
				event.stopPropagation();
				
			});
			
			
			$('.search-btn-wrpr .custom-search').click(function (event) {
				//$('.navbar-collapse.collapse').addClass('show');
				//$('.custom-search-results-wrpr').addClass('active');
				$('body').addClass('custom-search-active');
				
			});
			
			
			/*search Nov 24 2020 end*/
			
			
			$('.sidebar-toggler').click(function(){
				$('body').toggleClass('sidebar-active');
				
			});
			
			
			$(function() {
			$('a[href*=\\#]:not([href=\\#])').on('click', function() {
						var target = $(this.hash);
						target = target.length ? target : $('[name=' + this.hash.substr(1) +']');
						if (target.length) {
							$('html,body').animate({
								scrollTop: target.offset().top
							}, 1000);
							return false;
						}
					});
		});
		
		
		
		jQuery(document).ready(function(e) {
			previmage();
			nextimage();
		});
		function previmage()
		{
			var img=$('.previmage1');
			img.css('background-image', function () {
			var bg = ('url(' + $(this).data('previmage') + ')');
			return bg;
			});
		}
		
		function nextimage()
		{
			var img=$('.nextimage1');
			img.css('background-image', function () {
			var bg = ('url(' + $(this).data('nextimage') + ')');
			return bg;
			});
		}
		
		$('.calendly-badge-widget').click(function (event) {
    		Calendly.initPopupWidget({url:'https://calendly.com/nivedith/performance-pad-introduction'});
            return false;
		});

	
		
		
});