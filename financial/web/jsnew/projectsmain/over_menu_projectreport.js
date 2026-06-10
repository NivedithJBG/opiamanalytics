$(function(){

	$(document).on('click','.navbar-nav .overNow1 ', function(e){
		e.preventDefault(); 
		if($('.overNow').hasClass('active')){
			$( ".overNow" ).removeClass("active");
			$('.menu-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
		}else if($('.overNow4').hasClass('active')){
			$( ".overNow4" ).removeClass("active");
			$('.menu4-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
		}else if($('.overNow2').hasClass('active')){
			$( ".overNow2" ).removeClass("active");
			$('.finmenu-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
		}else if($('.icon-dashboard').hasClass('active')){
			$( ".icon-dashboard" ).removeClass("active");
			$('.chart-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
		}
		jQuery('body').removeClass('menu-active');
		$('.overNow1').toggleClass('active');
		if($('.overNow1').hasClass('active')){
			$('#project-title-head').html('Project Report');
			$('.menu1-popup-cntnr').addClass('active');
			$('body').css('overflow-y','hidden');
		}else{
			$('#project-title-head').html('Projects');
			$('.menu1-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
		}
		
	});
	$(document).on('click','.menu1-win-close ', function(e){
		e.preventDefault();
		$('.menu1-popup-cntnr').removeClass('active');
		$('body').css('overflow-y','auto');
		
	});
	
});
