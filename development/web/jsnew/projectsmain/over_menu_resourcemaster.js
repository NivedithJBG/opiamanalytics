$(function(){

	$(document).on('click','.navbar-nav .overNow', function(e){
		e.preventDefault();
		if($('.overNow4').hasClass('active')){
			$( ".overNow4" ).removeClass("active");
			$('.menu4-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
		}else if($('.overNow1').hasClass('active')){
			$( ".overNow1" ).removeClass("active");
			$('.menu1-popup-cntnr').removeClass('active');
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
		$('.overNow').toggleClass('active');

		if($('.overNow').hasClass('active')){
			console.log('inside');
			$('#project-title-head').html('Resource Library');
			$('#procurement-title-head').html('Resource Library');
			$('.menu-popup-cntnr').addClass('active');
			$('body').css('overflow-y','hidden');
		}else{
			$('#project-title-head').html('Projects');
			$('#procurement-title-head').html('Procurement Process');
			$('.menu-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
		}
		
	});
	$(document).on('click','.menu-win-close ', function(e){
		e.preventDefault();
		$('.menu-popup-cntnr').removeClass('active');
		$('body').css('overflow-y','auto');
		
	});

	//$(document).on('click','.overNow.active', function(){
	$(document).on('click','.overNow', function(){
		/*$('.menu-cntnt-wrpr .panel-group .panel:first-child .panel-title a').trigger('click');setTimeout(function(){
			$('.menu-cntnt-wrpr .panel-group').removeClass('active');
			$('.menu-cntnt-wrpr .panel-default').removeClass('active');
			$('.menu-cntnt-wrpr .panel-default').removeClass('prev-tab');
			$('.menu-cntnt-wrpr .panel-group .tab-content').removeClass('in');
	        	$('.menu-cntnt-wrpr .panel-group .panel-title').attr('aria-expanded','false');
			$('.menu-cntnt-wrpr .panel-group .tab-content').attr('aria-expanded','false');
						
		}, 10);*/

		$('.panel-group .panel:first-child .panel-title a').trigger('click');setTimeout(function(){
			$('.panel-group').removeClass('active');
			$('.panel-default').removeClass('active');
			$('.panel-default').removeClass('prev-tab');
			$('.panel-group .tab-content').removeClass('in');
	        	$('.menu4-cntnt-wrpr .panel-group .panel-title').attr('aria-expanded','false');
			$('.panel-group .tab-content').attr('aria-expanded','false');
						
		}, 10);

		$('#accordionmaster').addClass('acco-one-active');
		$('#accordionmaster').removeClass('acco-two-active');
    	$('#accordionmaster').removeClass('acco-three-active');
    	$('#accordionmaster').removeClass('acco-four-active');

    	$('#accordionindex').addClass('acco-billofres-active');
		$('#accordionindex').removeClass('acco-vendors-active');
    	$('#accordionindex').removeClass('acco-cart-active');
    	$('#accordionindex').removeClass('acco-confirmorders-active');
    	$('#accordionindex').removeClass('acco-despatchorders-active');

	});

	
});
