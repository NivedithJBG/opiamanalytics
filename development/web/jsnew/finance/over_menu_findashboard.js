$(function(){

	$(document).on('click','.navbar-nav .finance-dashboard', function(e){
		e.preventDefault();

		if($('.overNow').hasClass('active')){
			$( ".overNow" ).removeClass("active");
			$('.menu-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
		}else if($('.overNow1').hasClass('active')){
			$( ".overNow1" ).removeClass("active");
			$('.menu1-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
		}else if($('.overNow4').hasClass('active')){
			$( ".overNow4" ).removeClass("active");
			$('.menu4-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
		}else if($('.overFinreport').hasClass('active')){
			$( ".overFinreport" ).removeClass("active");
			$('.chart-popup-cntnrr').removeClass('active');
			$('body').css('overflow-y','auto');
		}else if($('.overNow2').hasClass('active')){
			$( ".overNow2" ).removeClass("active");
			$('.finmenu-popup-cntnrr').removeClass('active');
			$('body').css('overflow-y','auto');
		}

		jQuery('body').removeClass('menu-active');
		$('.finance-dashboard').toggleClass('active');
		if($('.finance-dashboard').hasClass('active')){
			$('#finance-title-head').html('Finance Dashboard');
			$('.chart-popup-cntnrr').addClass('active');
			$('body').css('overflow-y','hidden');
			$('#listfindasboard').trigger('click');
		}else{
			$('#finance-title-head').html('Finance');
			$('.chart-popup-cntnrr').removeClass('active');
			$('body').css('overflow-y','auto');
		}
		
	});
	$(document).on('click','.menu1-win-close ', function(e){
		e.preventDefault();
		$('.menu1-popup-cntnr').removeClass('active');
		$('body').css('overflow-y','auto');
		
	});
	$(document).on('click','.chart-win-close ', function(e){
			e.preventDefault();
			$('.chart-popup-cntnrr').removeClass('active');
			$('#finance-title-head').html('Finance');
			$('body').css('overflow-y','auto');
			
	});

	});
