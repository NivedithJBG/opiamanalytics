jQuery.noConflict();
jQuery(document).ready(function () {
    
        var customizeForDevice = function(){
			var ua = navigator.userAgent;
			var checker = {
			  iphone: ua.match(/(iPhone|iPod)/),
			  ipad: ua.match(/(iPad)/),
			  blackberry: ua.match(/BlackBerry/),
			  android: ua.match(/Android/)
			};
			if (checker.android){
				jQuery('body').addClass('android');
			}
			else if (checker.iphone){
				jQuery('body').addClass('iphone');
			}
			else if (checker.ipad){
				jQuery('body').addClass('ipad');
			}
			else {
				jQuery('body').addClass('desktop');
			}
		}
		customizeForDevice();
        
        
    
        
    
				
		jQuery('.panel-title a').on('click', function(){
			jQuery('.panel-group').find('.active').removeClass('active');
			jQuery(this).parents('.panel-default').toggleClass('active');
		});	
		
		jQuery('.round-icons .icon-menu1').on('click', function(){
			
			jQuery('body').toggleClass('menu-active');
		})
		
		
		/*$('.wizard-nav .col-md-2 span').on('click', function(){
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
						
		});*/
		
		
		jQuery(document).on("click", ".placeorder", function(){
		    //alert('sdf');
		})
		 
		jQuery(document).on( "click", ".acco-billofresources input[type=radio]", function(){ 
		//$('.acco-billofresources input[type=radio]').on('click', function(){
			
			
		    setTimeout(function(){
			        jQuery('#resourcessearch').trigger('click');	
			},500);
			
			
			
			jQuery(this).parents('.panel-group').addClass('acco-billofres-active');
			jQuery(this).parents('.panel-group').removeClass('acco-vendors-active');
			jQuery(this).parents('.panel-group').removeClass('acco-cart-active');
			jQuery(this).parents('.panel-group').removeClass('acco-confirmorders-active');
			jQuery(this).parents('.panel-group').removeClass('acco-despatchorders-active');
			jQuery(this).parents('.panel-group').removeClass('acco-completedorders-active');
			
			
			
		//	$('.wizard-nav .col-md-2 span').removeClass('active');
		//	$('.wizard-nav .col-md-2 span.bill-of-resources').addClass('active');
		//	$('.custom-percentage-bar .percentage').css('width','0%');
		});
		
		jQuery(document).on( "click", ".acco-vendors input[type=radio]", function(){
		//$('.acco-vendors').on('click', function(){
			jQuery(this).parents('.panel-group').removeClass('acco-billofres-active');
			jQuery(this).parents('.panel-group').addClass('acco-vendors-active');
			jQuery(this).parents('.panel-group').removeClass('acco-cart-active');
			jQuery(this).parents('.panel-group').removeClass('acco-confirmorders-active');
			jQuery(this).parents('.panel-group').removeClass('acco-despatchorders-active');
			jQuery(this).parents('.panel-group').removeClass('acco-completedorders-active');
			
		//	$('.wizard-nav .col-md-2 span').removeClass('active');
		//	$('.wizard-nav .col-md-2 span.vendors').addClass('active');
		//	$('.custom-percentage-bar .percentage').css('width','25%');	
					
		});
		
		
		
		jQuery(document).on( "click", ".nav .icon-shopping_cart", function(){
		    
		    jQuery('.acco-cart input[type=radio]').trigger('click');
		    
		});
		
		function resizeInput() {
		    
		    inputSze = jQuery('.acco-cart .resrouces-details input.small75');
		    jQuery(inputSze).attr('size','1');
            jQuery(inputSze).attr('size', $(inputSze).val().length);
        }
		
		jQuery(document).on( "click", ".acco-cart input[type=radio], #addvendortocart", function(){
		//$('.acco-cart input[type=radio]').on('click', function(){
		    
		    
		    setTimeout(function(){
			        jQuery('#cartsearch').trigger('click');
			        resizeInput();
			},500);
		    
		    
			jQuery(this).parents('.panel-group').removeClass('acco-billofres-active');
			jQuery(this).parents('.panel-group').removeClass('acco-vendors-active');
			jQuery(this).parents('.panel-group').addClass('acco-cart-active');
			jQuery(this).parents('.panel-group').removeClass('acco-confirmorders-active');
			jQuery(this).parents('.panel-group').removeClass('acco-despatchorders-active');
			jQuery(this).parents('.panel-group').removeClass('acco-completedorders-active');
			
		//	$('.wizard-nav .col-md-2 span').removeClass('active');
		//	$('.wizard-nav .col-md-2 span.cart').addClass('active');
		//	$('.custom-percentage-bar .percentage').css('width','50%');	
					
		});
		//$('.acco-confirmorders').on('click', function(){
		jQuery(document).on( "click", ".acco-confirmorders input[type=radio]", function(){ 
		    
		    setTimeout(function(){
			        jQuery('#ordersearch').trigger('click');
			        
			  var count = $("#orderitems > .row").length;
            console.log(count);
            
            if(count==1){
                jQuery('.acco-confirmorders').addClass('one');
            } else if(count==2){
                jQuery('.acco-confirmorders').addClass('two');
            } else if(count==3){
                jQuery('.acco-confirmorders').addClass('three');
            } else if(count==4){
                jQuery('.acco-confirmorders').addClass('four');
            } else if(count==5){
                jQuery('.acco-confirmorders').addClass('five');
            }
            else if(count==6){
                jQuery('.acco-confirmorders').addClass('six');
            }
            else if(count==7){
                jQuery('.acco-confirmorders').addClass('seven');
            }
            else if(count==8){
                jQuery('.acco-confirmorders').addClass('eight');
            }
            else if(count==9){
                jQuery('.acco-confirmorders').addClass('nine');
            }
            else if(count==10){
                jQuery('.acco-confirmorders').addClass('ten');
            }
            else if(count==11){
                jQuery('.acco-confirmorders').addClass('leven');
            }
            else if(count==12){
                jQuery('.acco-confirmorders').addClass('twelve');
            }
            else if(count==13){
                jQuery('.acco-confirmorders').addClass('thirteen');
            }
            else if(count==14){
                jQuery('.acco-confirmorders').addClass('fourteen');
            }
            else if(count==15){
                jQuery('.acco-confirmorders').addClass('fifteen');
            } 
			        
			        
			},500);
			
			
		    
			jQuery(this).parents('.panel-group').removeClass('acco-billofres-active');
			jQuery(this).parents('.panel-group').removeClass('acco-vendors-active');
			jQuery(this).parents('.panel-group').removeClass('acco-cart-active');
			jQuery(this).parents('.panel-group').addClass('acco-confirmorders-active');
			jQuery(this).parents('.panel-group').removeClass('acco-despatchorders-active');
			jQuery(this).parents('.panel-group').removeClass('acco-completedorders-active');
			
		//	$('.wizard-nav .col-md-2 span').removeClass('active');
		//	$('.wizard-nav .col-md-2 span.confirm-orders').addClass('active');
		//	$('.custom-percentage-bar .percentage').css('width','75%');	
					
		});
		
		
		
		//$('.acco-despatchorders').on('click', function(){
		jQuery(document).on( "click", ".acco-despatchorders input[type=radio]", function(){    
			jQuery(this).parents('.panel-group').removeClass('acco-billofres-active');
			jQuery(this).parents('.panel-group').removeClass('acco-vendors-active');
			jQuery(this).parents('.panel-group').removeClass('acco-cart-active');
			jQuery(this).parents('.panel-group').removeClass('acco-confirmorders-active');
			jQuery(this).parents('.panel-group').addClass('acco-despatchorders-active');
			jQuery(this).parents('.panel-group').removeClass('acco-completedorders-active');
			
			setTimeout(function(){
			        jQuery('#despatchordersearch').trigger('click');
			},500);
			
			
		//	$('.wizard-nav .col-md-2 span').removeClass('active');
		//	$('.wizard-nav .col-md-2 span.despatch-orders').addClass('active');
		//	$('.custom-percentage-bar .percentage').css('width','100%');	
					
		});
		jQuery(document).on( "click", ".acco-completedorders input[type=radio]", function(){    
			jQuery(this).parents('.panel-group').removeClass('acco-billofres-active');
			jQuery(this).parents('.panel-group').removeClass('acco-vendors-active');
			jQuery(this).parents('.panel-group').removeClass('acco-cart-active');
			jQuery(this).parents('.panel-group').removeClass('acco-confirmorders-active');
			jQuery(this).parents('.panel-group').removeClass('acco-despatchorders-active');
			jQuery(this).parents('.panel-group').addClass('acco-completedorders-active');
			
			setTimeout(function(){
			        jQuery('#Completedordersearch').trigger('click');
			},500);
			
			
		//	$('.wizard-nav .col-md-2 span').removeClass('active');
		//	$('.wizard-nav .col-md-2 span.despatch-orders').addClass('active');
		//	$('.custom-percentage-bar .percentage').css('width','100%');	
					
		});
		
		
		
		
	jQuery(document).on( "click", ".acco-one input[type=radio]", function(){ 
		//$('.acco-one').on('click', function(){
			//acco-confirmorders
 			jQuery(this).parents('.panel-group').addClass('acco-one-active');
 			jQuery(this).parents('.panel-group').removeClass('acco-two-active');
 			jQuery(this).parents('.panel-group').removeClass('acco-three-active');
 			jQuery(this).parents('.panel-group').removeClass('acco-four-active');
 			jQuery(this).parents('.panel-group').removeClass('acco-five-active');
 			jQuery(this).parents('.panel-group').removeClass('acco-six-active');
			
			
		});
		
		
 		jQuery(document).on( "click", ".acco-two input[type=radio]", function(){ 
 		//jQuery('.acco-two').on('click', function(){
 			//acco-confirmorders
 			jQuery(this).parents('.panel-group').addClass('acco-two-active');
 			jQuery(this).parents('.panel-group').removeClass('acco-one-active');
 			jQuery(this).parents('.panel-group').removeClass('acco-three-active');
 			jQuery(this).parents('.panel-group').removeClass('acco-four-active');
 			jQuery(this).parents('.panel-group').removeClass('acco-five-active');
 			jQuery(this).parents('.panel-group').removeClass('acco-six-active');
			
			
 		});
		
		jQuery(document).on( "click", ".acco-three input[type=radio]", function(){ 
		//$('.acco-three').on('click', function(){
			//acco-confirmorders
			jQuery(this).parents('.panel-group').addClass('acco-three-active');
			jQuery(this).parents('.panel-group').removeClass('acco-one-active');
			jQuery(this).parents('.panel-group').removeClass('acco-two-active');
			jQuery(this).parents('.panel-group').removeClass('acco-four-active');
			jQuery(this).parents('.panel-group').removeClass('acco-five-active');
			jQuery(this).parents('.panel-group').removeClass('acco-six-active');
			
			
		});
		
		jQuery(document).on( "click", ".acco-four input[type=radio]", function(){ 
		//$('.acco-four').on('click', function(){
			//acco-confirmorders
			jQuery(this).parents('.panel-group').addClass('acco-four-active');
			jQuery(this).parents('.panel-group').removeClass('acco-one-active');
			jQuery(this).parents('.panel-group').removeClass('acco-two-active');
			jQuery(this).parents('.panel-group').removeClass('acco-three-active');
			jQuery(this).parents('.panel-group').removeClass('acco-five-active');
			jQuery(this).parents('.panel-group').removeClass('acco-six-active');
			
			
		});
		
		jQuery(document).on( "click", ".acco-five input[type=radio]", function(){ 
		//$('.acco-five').on('click', function(){
			//acco-confirmorders
			jQuery(this).parents('.panel-group').addClass('acco-five-active');
			jQuery(this).parents('.panel-group').removeClass('acco-one-active');
			jQuery(this).parents('.panel-group').removeClass('acco-two-active');
			jQuery(this).parents('.panel-group').removeClass('acco-three-active');
			jQuery(this).parents('.panel-group').removeClass('acco-four-active');
			jQuery(this).parents('.panel-group').removeClass('acco-six-active');
			
			
		});
		
		jQuery(document).on( "click", ".acco-six input[type=radio]", function(){
		//$('.acco-six').on('click', function(){
			//acco-confirmorders
			jQuery(this).parents('.panel-group').addClass('acco-six-active');
			jQuery(this).parents('.panel-group').removeClass('acco-one-active');
			jQuery(this).parents('.panel-group').removeClass('acco-two-active');
			jQuery(this).parents('.panel-group').removeClass('acco-three-active');
			jQuery(this).parents('.panel-group').removeClass('acco-four-active');
			jQuery(this).parents('.panel-group').removeClass('acco-five-active');
			
			
		});
		
		
		jQuery('.resourcetype-tab .addForm').on('click', function(){
			jQuery('.resourcetype-tab').addClass('addResourceForm-active');
			jQuery('.resourcetype-tab').removeClass('editResourceForm-active');
		})
		
		jQuery('.resourcetype-tab .list-resourceType').on('click', function(){
			jQuery('.resourcetype-tab').removeClass('addResourceForm-active');
			jQuery('.resourcetype-tab').removeClass('editResourceForm-active');
		})
		
		
		jQuery('.resourcegroup-tab .addForm').on('click', function(){
			jQuery('.resourcegroup-tab').addClass('addResourceGroupForm-active');
			jQuery('.resourcegroup-tab').removeClass('editResourceGroupForm-active');
		})
		
		jQuery('.resourcegroup-tab .list-resourceType').on('click', function(){
			jQuery('.resourcegroup-tab').removeClass('addResourceGroupForm-active');
			jQuery('.resourcegroup-tab').removeClass('editResourceGroupForm-active');
		})
		
		
		jQuery('.resources-tab .addForm').on('click', function(){
			jQuery('.resources-tab').addClass('addResourcesForm-active');
			jQuery('.resources-tab').removeClass('editResourcesForm-active');
		})
		
		jQuery('.resources-tab .list-resources').on('click', function(){
			jQuery('.resources-tab').removeClass('addResourcesForm-active');
			jQuery('.resources-tab').removeClass('editResourcesForm-active');
		})
		

		$('.vendors-tab .addForm').on('click', function(){
			$('.vendors-tab').addClass('addVendorsForm-active');
			$('.vendors-tab').removeClass('editVendorsForm-active');
		})
		
		$('.vendors-tab .list-vendors').on('click', function(){
			$('.vendors-tab').removeClass('addVendorsForm-active');
			$('.vendors-tab').removeClass('editVendorsForm-active');
		})
		

		/*Aug 7 2020*/
		
		$('.resources-tab .add-vendor-at-resource').on('click', function(){ 
			
			$('.resources-tab').addClass('addVendor-at-ResourceTabForm-active');
			
		})
		
		$('.resources-tab .addvendor-resource-cntnt-wrpr .cancel ').on('click', function(){
			$('.resources-tab').removeClass('addVendor-at-ResourceTabForm-active');
		})
		



		
		
		
		$('.resource-type-cntnt-wrpr .icon-groups .icon-pencil').on('click', function(){
		    $('.resourcetype-tab').addClass('editResourceForm-active');
			
		})
		
		$('.resource-group-cntnt-wrpr .icon-groups .icon-pencil').on('click', function(){
			$('.resourcegroup-tab').addClass('editResourceGroupForm-active');
			
		})
		
		$('.resources-cntnt-wrpr  .icon-groups .icon-pencil').on('click', function(){
			$('.resources-tab').addClass('editResourcesForm-active');
			
		})
		
		


        
  //       $('.resource-type-cntnt-wrpr .icon-groups .icon-pencil').on('click', function(){
		//     $('.resourcetype-tab').addClass('editResourceForm-active');
			
		// })
		
		// $('.resource-group-cntnt-wrpr .icon-groups .icon-pencil').on('click', function(){
		// 	$('.resourcegroup-tab').addClass('editResourceGroupForm-active');
			
		// })
		
		// $('.resources-cntnt-wrpr  .icon-groups .icon-pencil').on('click', function(){
		// 	$('.resources-tab').addClass('editResourcesForm-active');
			
		// })
		
		
		$('.vendor-types-cntnt-wrpr  .icon-groups .icon-pencil').on('click', function(){
			$('.vendortypes-tab').addClass('editVendorTypesForm-active');
			
		})
		
		$('.vendor-groups-cntnt-wrpr  .icon-groups .icon-pencil').on('click', function(){
			$('.vendorgroups-tab').addClass('editVendorGroupsForm-active');
			
		})
		$('.vendors-content-wrpr  .icon-groups .icon-pencil').on('click', function(){
			$('.vendors-tab').addClass('editVendorsForm-active');
			
		})
		
		
		$('.vendors-add-cntnt-wrpr .cancel, .vendors-edit-cntnt-wrpr .cancel').on('click', function(){
			$('.vendors-tab .addForm').next('.list-vendors').trigger('click');	
		})
		
		



		
		
		jQuery(document).on( "click", ".approve", function(){ 
			//alert (document.location.origin)
		//$('.approve').on('click', function(){
			jQuery('.approveOrder-cntnt').toggleClass('active');
			//baseUrl= "http://localhost";
              baseUrl=document.location.origin;
			orderHref = baseUrl + jQuery(this).attr('data-url');
			console.log(orderHref);
			jQuery('#approveOrder').attr('src', orderHref);
			setTimeout(function(){
				
				jQuery("html, body").animate({ scrollTop: jQuery('.acco-confirmorders').offset().top }, 100);				
			},60);
			
			
			
		});
		
		function placrorderCart3(){
		    setTimeout(function(){
				alert('hai');
				
                  
				
				
								
			},600);
		}
		
		jQuery(document).on( "click", ".approveorderbtn", function(){
		    
		   
		    
		    
		})
		
		
		
		
		
		
		jQuery('.cancel, .icon-close').on('click', function(){
			jQuery('.approveOrder-cntnt').removeClass('active');
			
			
		});
		
		
		
		
		
		
		
		
		
		jQuery('.choosevendor').on('click', function(){
			//alert('sdf');
			
			setTimeout(function(){
				jQuery('.acco-vendors input[type=radio]').trigger('click');
				
			}, 10);
			
			
			
		});
		
		
		jQuery('.addtocart').on('click', function(){
			//alert('sdf');
			
			setTimeout(function(){
				jQuery('.acco-cart input[type=radio]').trigger('click');
				
			}, 10);
			
			
		});
		
		
		
		jQuery('.addtocartsssdfsdf').on('click', function(){
			
			jQuery('.panel-group').removeClass('koooi');
			jQuery('.procu-accordion a[href="#collapse3"] ').trigger('click');
			jQuery('.wizard-nav .col-md-2 span').removeClass('active');
			jQuery('.wizard-nav .col-md-2 span.cart').addClass('active');
			jQuery('.custom-percentage-bar .percentage').css('width','50%');
		});
		  
		
		
});