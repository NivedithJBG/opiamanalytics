$(function(){

	$(document).on('click','.navbar-nav .overNow2', function(e){
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
		}else if($('.icon-dashboard').hasClass('active')){
			$( ".icon-dashboard" ).removeClass("active");
			$('.chart-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
		}else if($('.overFinreport').hasClass('active')){
			$( ".overFinreport" ).removeClass("active");
			$('.menu1-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
		}
		jQuery('body').removeClass('menu-active');
		$('.overNow2').toggleClass('active');
		if($('.overNow2').hasClass('active')){
			$('#finance-title-head').html('Finance Masters');
			$('.finmenu-popup-cntnr').addClass('active');
			$('body').css('overflow-y','hidden');
		}else{
			$('#finance-title-head').html('Finance');
			$('.finmenu-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
		}
		
    });
	$(document).on('click','.menu-win-close ', function(e){
		e.preventDefault();
		$('.finmenu-popup-cntnr').removeClass('active');
		$('body').css('overflow-y','auto');
		
	});

	//$(document).on('click','.overNow2.active', function(){
	$(document).on('click','.overNow2', function(){
		/*$('.finmenu-cntnt-wrpr .panel-group .panel:first-child .panel-title a').trigger('click');setTimeout(function(){
			$('.finmenu-cntnt-wrpr .panel-group').removeClass('active');
			$('.finmenu-cntnt-wrpr .panel-default').removeClass('active');
			$('.finmenu-cntnt-wrpr .panel-default').removeClass('prev-tab');
			$('.finmenu-cntnt-wrpr .panel-group .tab-content').removeClass('in');
	        	$('.finmenu-cntnt-wrpr .panel-group .panel-title').attr('aria-expanded','false');
			$('.finmenu-cntnt-wrpr .panel-group .tab-content').attr('aria-expanded','false');
						
		}, 10);*/

		$('.panel-group .panel:first-child .panel-title a').trigger('click');setTimeout(function(){
			$('.panel-group').removeClass('active');
			$('.panel-default').removeClass('active');
			$('.panel-default').removeClass('prev-tab');
			$('.panel-group .tab-content').removeClass('in');
	        	$('.menu4-cntnt-wrpr .panel-group .panel-title').attr('aria-expanded','false');
			$('.panel-group .tab-content').attr('aria-expanded','false');
						
		}, 10);

	});

	
});