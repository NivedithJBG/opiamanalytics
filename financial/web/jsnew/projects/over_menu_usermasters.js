$(function(){

	$(document).on("click",".navbar-nav .overNow6", function(e){
		//alert("hi");
		
		e.preventDefault();
		 
		jQuery('body').removeClass('menu-active');
		$('.overNow6').toggleClass('active');
		if($('.overNow6').hasClass('active')){
			$('#userprjct_head').html('User Masters');
			$('.menu6-popup-cntnr').addClass('active');
			$('body').css('overflow-y','hidden');
		}else{
			$('#asset').hide();
			$('#userprjct_head').html('User Role');
			$('.menu6-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
		}
		
	});
	$(document).on('click','.menu01-win-close ', function(e){
		e.preventDefault();
		$('.menu6-popup-cntnr').removeClass('active');
		$('body').css('overflow-y','auto');
		
	});
	
});
