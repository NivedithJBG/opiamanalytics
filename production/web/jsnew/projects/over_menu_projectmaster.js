$(function(){

	$(document).on('click','.navbar-nav .overNow4', function(e){
		e.preventDefault();
		if($('.overNow').hasClass('active')){
			$( ".overNow" ).removeClass("active");
			$('.menu-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
		}else if($('.overNow1').hasClass('active')){
			$( ".overNow1" ).removeClass("active");
			$('.menu1-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
		}else if($('.overNow2').hasClass('active')){
			$( ".overNow2" ).removeClass("active");
			$('.finmenu-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
		}else if($('.overNow5').hasClass('active')){
				$( ".overNow5" ).removeClass("active");
				$('.menu5-popup-cntnr').removeClass('active');
				$('body').css('overflow-y','auto');
		}else if($('.icon-dashboard').hasClass('active')){
			$( ".icon-dashboard" ).removeClass("active");
			$('.chart-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
		}
		jQuery('body').removeClass('menu-active');
		$('.overNow4').toggleClass('active');
		if($('.overNow4').hasClass('active')){
			$('#project-title-head').html('Activity Library');
			$('#prjct_head').html('Activity Library');
			$('#procurement-title-head').html('Activity Library');
			$('.menu4-popup-cntnr').addClass('active');
			$('body').css('overflow-y','hidden');
		}else{
			$('#prjct_head').html('Operations');
			$('#project-title-head').html('Projects');
			$('.menu4-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
		}
		


		
	});
	$(document).on('click','.menu4-win-close ', function(e){
		e.preventDefault();
		$('.menu4-popup-cntnr').removeClass('active');
		$('body').css('overflow-y','auto');
		
	});


	//$(document).on('click','.overNow.active', function(){
	//$(document).on('click','.overNow', function(){
		/*$('.menu-cntnt-wrpr .panel-group .panel:first-child .panel-title a').trigger('click');setTimeout(function(){
			$('.menu-cntnt-wrpr .panel-group').removeClass('active');
			$('.menu-cntnt-wrpr .panel-default').removeClass('active');
			$('.menu-cntnt-wrpr .panel-default').removeClass('prev-tab');
			$('.menu-cntnt-wrpr .panel-group .tab-content').removeClass('in');
	        	$('.menu-cntnt-wrpr .panel-group .panel-title').attr('aria-expanded','false');
			$('.menu-cntnt-wrpr .panel-group .tab-content').attr('aria-expanded','false');
						
		}, 10);*/

		/*$('.panel-group .panel:first-child .panel-title a').trigger('click');setTimeout(function(){
			$('.panel-group').removeClass('active');
			$('.panel-default').removeClass('active');
			$('.panel-default').removeClass('prev-tab');
			$('.panel-group .tab-content').removeClass('in');
	        	$('.menu4-cntnt-wrpr .panel-group .panel-title').attr('aria-expanded','false');
			$('.panel-group .tab-content').attr('aria-expanded','false');
						
		}, 10);*/
	//});
	
});
