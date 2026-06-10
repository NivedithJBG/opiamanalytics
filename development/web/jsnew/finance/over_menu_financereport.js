$(function(){

	$(document).on('click','.navbar-nav .overFinreport', function(e){
		e.preventDefault(); 
		/*$('.panel-group .panel:first-child .panel-title a').trigger('click');setTimeout(function(){
			$('.panel-group').removeClass('active');
			$('.panel-default').removeClass('active');
			$('.panel-default').removeClass('prev-tab');
			$('.panel-group .tab-content').removeClass('in');
	        	$('.menu1-cntnt-wrpr .panel-group .panel-title').attr('aria-expanded','false');
			$('.panel-group .tab-content').attr('aria-expanded','false');
						
		}, 10);*/

		$('#accordionfinreports').addClass('acco-one-active');
		$('#accordionfinreports').removeClass('acco-two-active');
    	$('#accordionfinreports').removeClass('acco-three-active');
    	$('#accordionfinreports').removeClass('acco-four-active');

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
		}else if($('.overNow2').hasClass('active')){
			$( ".overNow2" ).removeClass("active");
			$('.finmenu-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
		}

		jQuery('body').removeClass('menu-active');
		$('.overFinreport').toggleClass('active');
		if($('.overFinreport').hasClass('active')){
			$('#finance-title-head').html('Finance Report');
			$('.menu1-popup-cntnr').addClass('active');
			$('body').css('overflow-y','hidden');
		}else{
			$('#finance-title-head').html('Finance');
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