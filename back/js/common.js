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
		
		
		$('.getstarted_form').on({
		  focusout: function () {
			$(this).data('timer', setTimeout(function () {
			  $(this).removeClass('active');
			}.bind(this), 100));
		  },
		  focusin: function () {
			clearTimeout($(this).data('timer'));
		  }
		});
		
		$('.tempformWrpr').on({
		  focusout: function () {
			$(this.hash).data('timer', setTimeout(function () {
			  $(this.hash).removeClass('active');
			}.bind(this), 2000));
		  },
		  focusin: function () {
			clearTimeout($(this.hash).data('timer'));  
		  }
		});
		
		
		
		/*var prevScrollpos = window.pageYOffset;
		window.onscroll = function() {
		var currentScrollPos = window.pageYOffset;
		  if (prevScrollpos > currentScrollPos) {
			document.getElementById("navbar").style.top = "0";
		  } else {
			document.getElementById("navbar").style.top = "-145px";
		  }
		  prevScrollpos = currentScrollPos;
		  console.log(currentScrollPos);
		  
		  if (currentScrollPos > 780){
				$('#navbar').addClass('secondfold');
			} else {
				$('#navbar').removeClass('secondfold');
			}
		  
		}*/
		
		var previousScroll = 50;
        $(window).scroll(function() {
    		
            var currentScroll = $(this).scrollTop();
    		if (currentScroll > 210) {
                NavTrans()
            } else {
    			removeNavTrans()
    		};
            if (currentScroll < 80) {
                showTopNav()
            } else if (currentScroll > 50 && currentScroll < $(document).height() - $(window).height()) {
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
		}
		function removeNavTrans() {
			$("#navbar").removeClass("whiteNav");
		}
 
		
		
		//$bannerHeight = $(".banner").height()-100;
		
		//console.log($bannerHeight);
		
		
		function bannerHeight(){
			$(".banner").height($bannerHeight);
		}
  
		$(function(){
		  // SET THE DIV TO THE WINDOW HEIGHT
		  $('.banner').css({'min-height':($(window).height()-350)});
		  // RESIZE THE HEIGHT IF THE WINDOW IS RESIZED
		  $(window).resize(function(){
			$('.banner').css({'min-height':($(window).height()-350)});
		  });
		});
  
  
  
		$(function(){
			
		  if ($("body").hasClass("desktop")) {
			  
			  
			  // SET THE DIV TO THE WINDOW HEIGHT
			  $('.banner').css({'min-height':($(window).height()-350)});
			  // RESIZE THE HEIGHT IF THE WINDOW IS RESIZED
			  $(window).resize(function(){
				$('.banner').css({'min-height':($(window).height()-350)});
			  });
		  };
		  
		  if ($("body").hasClass(".portrait") || $("body").hasClass("ipad") ) {
			  
			  //alert('portrait');
			  // SET THE DIV TO THE WINDOW HEIGHT
			  $('.banner').css({'min-height':($(window).height()-750)});
			  // RESIZE THE HEIGHT IF THE WINDOW IS RESIZED
			  $(window).resize(function(){
				$('.banner').css({'min-height':($(window).height()-750)});
			  });
		  };
		  
		  if ($("body").hasClass(".landscape") || $("body").hasClass("ipad") ) {
			  
			  //alert('landscape');
			  // SET THE DIV TO THE WINDOW HEIGHT
			  $('.banner').css({'min-height':($(window).height()-750)});
			  // RESIZE THE HEIGHT IF THE WINDOW IS RESIZED
			  $(window).resize(function(){
				$('.banner').css({'min-height':($(window).height()-750)});
			  });
		  };
		  
		  
		});
  
  
	
		
		
});