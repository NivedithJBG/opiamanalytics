$(function(){

	$(document).on("click",".navbar-nav .overNow5", function(e){
		//alert("hi");
		$('#asset').show();
		e.preventDefault();
		 if($('.overNow4').hasClass('active')){
			$( ".overNow4" ).removeClass("active");
			$('.menu4-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
		}else if($('.overNow2').hasClass('active')){
			$( ".overNow2" ).removeClass("active");
			$('.finmenu-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
		}else if($('.overNow7').hasClass('active')){
			$( ".overNow7" ).removeClass("active");
			$('.menu7-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
		}else if($('.icon-dashboard').hasClass('active')){
			$( ".icon-dashboard" ).removeClass("active");
			$('.chart-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
		}
		jQuery('body').removeClass('menu-active');
		$('.overNow5').toggleClass('active');
		if($('.overNow5').hasClass('active')){
			$('#prjct_head').html('Operations Report');
			$('.menu5-popup-cntnr').addClass('active');
			$('body').css('overflow-y','hidden');
		}else{
			$('#asset').hide();
			$('#prjct_head').html('Operations');
			$('.menu5-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
		}
		
	});


	$(document).on("click",".navbar-nav .overNow7", function(e){
		//alert("hi");
		$('#asset_library').show();
		e.preventDefault();
		 if($('.overNow4').hasClass('active')){
			$( ".overNow4" ).removeClass("active");
			$('.menu4-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
		}
		else if($('.overNow2').hasClass('active')){
			$( ".overNow2" ).removeClass("active");
			$('.finmenu-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
		}
		else if($('.overNow5').hasClass('active')){
			$( ".overNow5" ).removeClass("active");
			$('.menu5-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
		}
		else if($('.icon-dashboard').hasClass('active')){
			$( ".icon-dashboard" ).removeClass("active");
			$('.chart-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
		}

		jQuery('body').removeClass('menu-active');
		$('.overNow7').toggleClass('active');
		if($('.overNow7').hasClass('active')){
			$('#prjct_head').html('Asset Library');
			$('.menu7-popup-cntnr').addClass('active');
			$('body').css('overflow-y','hidden');
		}else{
			$('#asset').hide();
			$('#prjct_head').html('Operations');
			$('.menu7-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
		}
		
	});

	$(document).on('click','.menu01-win-close ', function(e){
		e.preventDefault();
		$('.menu5-popup-cntnr').removeClass('active');
		$('body').css('overflow-y','auto');
		
	});
	
});
