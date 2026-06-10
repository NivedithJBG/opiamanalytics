$(document).ready(function () {
				
		$('.panel-title a').on('click', function(){
			$('.panel-group').find('.active').removeClass('active');
			$(this).parents('.panel-default').toggleClass('active');
		});	
		
		$('.round-icons .icon-menu1').on('click', function(){
			
			$('body').toggleClass('menu-active');
		})
		
		
		$('.wizard-nav .col-md-2 span').on('click', function(){
			$('.wizard-nav .col-md-2 span').removeClass('active');
			$(this).toggleClass('active');
		});
		
		$('.wizard-nav .bill-of-resources').on('click', function(){
			//$('.procu-accordion a[href="#collapse1"] ').trigger('click');
			$('.custom-percentage-bar .percentage').css('width','0%');	
			//$('.tab input[type=radio]').removeAttr('checked');
			//$('.acco-billofresources input[type=radio]').attr('checked','checked');
			setTimeout(function(){
				$('.acco-billofresources input[type=radio]').trigger('click');	
				
			}, 10);
			
					
			
		});
		
		$('.wizard-nav .vendors').on('click', function(){
			//$('.procu-accordion a[href="#collapse2"] ').trigger('click');
			$('.custom-percentage-bar .percentage').css('width','25%');	
			setTimeout(function(){
				$('.acco-vendors input[type=radio]').trigger('click');		
				
			}, 10);
			
		});
		
		$('.wizard-nav .cart').on('click', function(){
			//$('.procu-accordion a[href="#collapse3"] ').trigger('click');
			$('.custom-percentage-bar .percentage').css('width','50%');
			setTimeout(function(){
				$('.acco-cart input[type=radio]').trigger('click');			
				
			}, 10);
						
		});
		
		$('.wizard-nav .confirm-orders').on('click', function(){
			//$('.procu-accordion a[href="#collapse4"] ').trigger('click');
			$('.custom-percentage-bar .percentage').css('width','75%');	
			setTimeout(function(){
				$('.acco-confirmorders input[type=radio]').trigger('click');		
				
			}, 10);
			
		});
		
		$('.wizard-nav .despatch-orders').on('click', function(){
			//$('.procu-accordion a[href="#collapse5"] ').trigger('click');
			$('.custom-percentage-bar .percentage').css('width','100%');
			setTimeout(function(){
				$('.acco-despatchorders input[type=radio]').trigger('click');		
				
			}, 10);
						
		});
		
		
		
		 
		 
		$('.acco-billofresources').on('click', function(){
			//acco-confirmorders
			$(this).parent('.panel-group').addClass('acco-billofres-active');
			$(this).parent('.panel-group').removeClass('acco-vendors-active');
			$(this).parent('.panel-group').removeClass('acco-cart-active');
			$(this).parent('.panel-group').removeClass('acco-confirmorders-active');
			$(this).parent('.panel-group').removeClass('acco-despatchorders-active');
			
			$('.wizard-nav .col-md-2 span').removeClass('active');
			$('.wizard-nav .col-md-2 span.bill-of-resources').addClass('active');
			$('.custom-percentage-bar .percentage').css('width','0%');
		});
		
		$('.acco-vendors').on('click', function(){
			$(this).parent('.panel-group').removeClass('acco-billofres-active');
			$(this).parent('.panel-group').addClass('acco-vendors-active');
			$(this).parent('.panel-group').removeClass('acco-cart-active');
			$(this).parent('.panel-group').removeClass('acco-confirmorders-active');
			$(this).parent('.panel-group').removeClass('acco-despatchorders-active');
			
			$('.wizard-nav .col-md-2 span').removeClass('active');
			$('.wizard-nav .col-md-2 span.vendors').addClass('active');
			$('.custom-percentage-bar .percentage').css('width','25%');	
					
		});
		$('.acco-cart').on('click', function(){
			$(this).parent('.panel-group').removeClass('acco-billofres-active');
			$(this).parent('.panel-group').removeClass('acco-vendors-active');
			$(this).parent('.panel-group').addClass('acco-cart-active');
			$(this).parent('.panel-group').removeClass('acco-confirmorders-active');
			$(this).parent('.panel-group').removeClass('acco-despatchorders-active');
			
			$('.wizard-nav .col-md-2 span').removeClass('active');
			$('.wizard-nav .col-md-2 span.cart').addClass('active');
			$('.custom-percentage-bar .percentage').css('width','50%');	
					
		});
		$('.acco-confirmorders').on('click', function(){
			$(this).parent('.panel-group').removeClass('acco-billofres-active');
			$(this).parent('.panel-group').removeClass('acco-vendors-active');
			$(this).parent('.panel-group').removeClass('acco-cart-active');
			$(this).parent('.panel-group').addClass('acco-confirmorders-active');
			$(this).parent('.panel-group').removeClass('acco-despatchorders-active');
			
			$('.wizard-nav .col-md-2 span').removeClass('active');
			$('.wizard-nav .col-md-2 span.confirm-orders').addClass('active');
			$('.custom-percentage-bar .percentage').css('width','75%');	
					
		});
		$('.acco-despatchorders').on('click', function(){
			$(this).parent('.panel-group').removeClass('acco-billofres-active');
			$(this).parent('.panel-group').removeClass('acco-vendors-active');
			$(this).parent('.panel-group').removeClass('acco-cart-active');
			$(this).parent('.panel-group').removeClass('acco-confirmorders-active');
			$(this).parent('.panel-group').addClass('acco-despatchorders-active');
			
			$('.wizard-nav .col-md-2 span').removeClass('active');
			$('.wizard-nav .col-md-2 span.despatch-orders').addClass('active');
			$('.custom-percentage-bar .percentage').css('width','100%');	
					
		});
		
		
		
		$('.acco-one').on('click', function(){
			//acco-confirmorders
			$(this).parent('.panel-group').addClass('acco-one-active');
			$(this).parent('.panel-group').removeClass('acco-two-active');
			$(this).parent('.panel-group').removeClass('acco-three-active');
			$(this).parent('.panel-group').removeClass('acco-four-active');
			$(this).parent('.panel-group').removeClass('acco-five-active');
			$(this).parent('.panel-group').removeClass('acco-six-active');
			
			
		});
		
		$('.acco-two').on('click', function(){
			//acco-confirmorders
			$(this).parent('.panel-group').addClass('acco-two-active');
			$(this).parent('.panel-group').removeClass('acco-one-active');
			$(this).parent('.panel-group').removeClass('acco-three-active');
			$(this).parent('.panel-group').removeClass('acco-four-active');
			$(this).parent('.panel-group').removeClass('acco-five-active');
			$(this).parent('.panel-group').removeClass('acco-six-active');
			
			
		});
		
		$('.acco-three').on('click', function(){
			//acco-confirmorders
			$(this).parent('.panel-group').addClass('acco-three-active');
			$(this).parent('.panel-group').removeClass('acco-one-active');
			$(this).parent('.panel-group').removeClass('acco-two-active');
			$(this).parent('.panel-group').removeClass('acco-four-active');
			$(this).parent('.panel-group').removeClass('acco-five-active');
			$(this).parent('.panel-group').removeClass('acco-six-active');
			
			
		});
		
		$('.acco-four').on('click', function(){
			//acco-confirmorders
			$(this).parent('.panel-group').addClass('acco-four-active');
			$(this).parent('.panel-group').removeClass('acco-one-active');
			$(this).parent('.panel-group').removeClass('acco-two-active');
			$(this).parent('.panel-group').removeClass('acco-three-active');
			$(this).parent('.panel-group').removeClass('acco-five-active');
			$(this).parent('.panel-group').removeClass('acco-six-active');
			
			
		});
		
		$('.acco-five').on('click', function(){
			//acco-confirmorders
			$(this).parent('.panel-group').addClass('acco-five-active');
			$(this).parent('.panel-group').removeClass('acco-one-active');
			$(this).parent('.panel-group').removeClass('acco-two-active');
			$(this).parent('.panel-group').removeClass('acco-three-active');
			$(this).parent('.panel-group').removeClass('acco-four-active');
			$(this).parent('.panel-group').removeClass('acco-six-active');
			
			
		});
		
		
		$('.acco-six').on('click', function(){
			//acco-confirmorders
			$(this).parent('.panel-group').addClass('acco-six-active');
			$(this).parent('.panel-group').removeClass('acco-one-active');
			$(this).parent('.panel-group').removeClass('acco-two-active');
			$(this).parent('.panel-group').removeClass('acco-three-active');
			$(this).parent('.panel-group').removeClass('acco-four-active');
			$(this).parent('.panel-group').removeClass('acco-five-active');
			
			
		});
		
		
		
		
		
		
		
		$('.approve').on('click', function(){
			$('.approveOrder-cntnt').toggleClass('active');
			setTimeout(function(){
				
				$("html, body").animate({ scrollTop: $('.acco-confirmorders').offset().top }, 1000);				
			},60);
			
			
			
		});
		
		$('.cancel, .icon-close').on('click', function(){
			$('.approveOrder-cntnt').removeClass('active');
			
			
		});
		
		
		
		
		
		
		
		
		
		$('.choosevendor').on('click', function(){
			//alert('sdf');
			
			setTimeout(function(){
				$('.acco-vendors input[type=radio]').trigger('click');
				
			}, 10);
			
			
			
		});
		
		
		$('.addtocart').on('click', function(){
			//alert('sdf');
			
			setTimeout(function(){
				$('.acco-cart input[type=radio]').trigger('click');
				
			}, 10);
			
			
		});
		
		
		
		$('.addtocartsssdfsdf').on('click', function(){
			
			$('.panel-group').removeClass('koooi');
			$('.procu-accordion a[href="#collapse3"] ').trigger('click');
			$('.wizard-nav .col-md-2 span').removeClass('active');
			$('.wizard-nav .col-md-2 span.cart').addClass('active');
			$('.custom-percentage-bar .percentage').css('width','50%');
		});
		  
		
		
});