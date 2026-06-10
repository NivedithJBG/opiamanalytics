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
		    if($(inputSze).val()!==undefined){
           jQuery(inputSze).attr('size', $(inputSze).val().length);
       }
           // jQuery(inputSze).attr('size', $(inputSze).val().length);
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
		
		
		
		
		$('.acco-one').on('click', function(){
			//acco-confirmorders
			$(this).parent('.panel-group').addClass('acco-one-active');
			$(this).parent('.panel-group').removeClass('acco-two-active');
			$(this).parent('.panel-group').removeClass('acco-three-active');
			$(this).parent('.panel-group').removeClass('acco-four-active');
			$(this).parent('.panel-group').removeClass('acco-five-active');
			$(this).parent('.panel-group').removeClass('acco-six-active');
			$(this).parent('.panel-group').removeClass('acco-seven-active');
			$(this).parent('.panel-group').removeClass('acco-eight-active');
			$(this).parent('.panel-group').removeClass('acco-nine-active');
			$(this).parent('.panel-group').removeClass('acco-ten-active');
			$(this).parent('.panel-group').removeClass('acco-eleven-active');
			
			
		});
		
		$('.acco-two').on('click', function(){
			//acco-confirmorders
			$(this).parent('.panel-group').addClass('acco-two-active');
			$(this).parent('.panel-group').removeClass('acco-one-active');
			$(this).parent('.panel-group').removeClass('acco-three-active');
			$(this).parent('.panel-group').removeClass('acco-four-active');
			$(this).parent('.panel-group').removeClass('acco-five-active');
			$(this).parent('.panel-group').removeClass('acco-six-active');
			$(this).parent('.panel-group').removeClass('acco-seven-active');
			$(this).parent('.panel-group').removeClass('acco-eight-active');
			$(this).parent('.panel-group').removeClass('acco-nine-active');
			$(this).parent('.panel-group').removeClass('acco-ten-active');
			$(this).parent('.panel-group').removeClass('acco-eleven-active');
			
			
		});
		
		$('.acco-three').on('click', function(){
			//acco-confirmorders
			$(this).parent('.panel-group').addClass('acco-three-active');
			$(this).parent('.panel-group').removeClass('acco-one-active');
			$(this).parent('.panel-group').removeClass('acco-two-active');
			$(this).parent('.panel-group').removeClass('acco-four-active');
			$(this).parent('.panel-group').removeClass('acco-five-active');
			$(this).parent('.panel-group').removeClass('acco-six-active');
			$(this).parent('.panel-group').removeClass('acco-seven-active');
			$(this).parent('.panel-group').removeClass('acco-eight-active');
			$(this).parent('.panel-group').removeClass('acco-nine-active');
			$(this).parent('.panel-group').removeClass('acco-ten-active');
			$(this).parent('.panel-group').removeClass('acco-eleven-active');
			
			
		});
		
		$('.acco-four').on('click', function(){
			//acco-confirmorders
			$(this).parent('.panel-group').addClass('acco-four-active');
			$(this).parent('.panel-group').removeClass('acco-one-active');
			$(this).parent('.panel-group').removeClass('acco-two-active');
			$(this).parent('.panel-group').removeClass('acco-three-active');
			$(this).parent('.panel-group').removeClass('acco-five-active');
			$(this).parent('.panel-group').removeClass('acco-six-active');
			$(this).parent('.panel-group').removeClass('acco-seven-active');
			$(this).parent('.panel-group').removeClass('acco-eight-active');
			$(this).parent('.panel-group').removeClass('acco-nine-active');
			$(this).parent('.panel-group').removeClass('acco-ten-active');
			$(this).parent('.panel-group').removeClass('acco-eleven-active');
			
			
		});
		
		$('.acco-five').on('click', function(){
			//acco-confirmorders
			$(this).parent('.panel-group').addClass('acco-five-active');
			$(this).parent('.panel-group').removeClass('acco-one-active');
			$(this).parent('.panel-group').removeClass('acco-two-active');
			$(this).parent('.panel-group').removeClass('acco-three-active');
			$(this).parent('.panel-group').removeClass('acco-four-active');
			$(this).parent('.panel-group').removeClass('acco-six-active');
			$(this).parent('.panel-group').removeClass('acco-seven-active');
			$(this).parent('.panel-group').removeClass('acco-eight-active');
			$(this).parent('.panel-group').removeClass('acco-nine-active');
			$(this).parent('.panel-group').removeClass('acco-ten-active');
			$(this).parent('.panel-group').removeClass('acco-eleven-active');
			
			
		});
		
		
		$('.acco-six').on('click', function(){
			//acco-confirmorders
			$(this).parent('.panel-group').addClass('acco-six-active');
			$(this).parent('.panel-group').removeClass('acco-one-active');
			$(this).parent('.panel-group').removeClass('acco-two-active');
			$(this).parent('.panel-group').removeClass('acco-three-active');
			$(this).parent('.panel-group').removeClass('acco-four-active');
			$(this).parent('.panel-group').removeClass('acco-five-active');
			$(this).parent('.panel-group').removeClass('acco-seven-active');
			$(this).parent('.panel-group').removeClass('acco-eight-active');
			$(this).parent('.panel-group').removeClass('acco-nine-active');
			$(this).parent('.panel-group').removeClass('acco-ten-active');
			$(this).parent('.panel-group').removeClass('acco-eleven-active');
			
			
		});
		
		
		$('.acco-seven').on('click', function(){
			
			$(this).parent('.panel-group').addClass('acco-seven-active');
			$(this).parent('.panel-group').removeClass('acco-one-active');
			$(this).parent('.panel-group').removeClass('acco-two-active');
			$(this).parent('.panel-group').removeClass('acco-three-active');
			$(this).parent('.panel-group').removeClass('acco-four-active');
			$(this).parent('.panel-group').removeClass('acco-five-active');
			$(this).parent('.panel-group').removeClass('acco-six-active');
			$(this).parent('.panel-group').removeClass('acco-eight-active');
			$(this).parent('.panel-group').removeClass('acco-nine-active');
			$(this).parent('.panel-group').removeClass('acco-ten-active');
			$(this).parent('.panel-group').removeClass('acco-eleven-active');
			
			
		});
		
		
		$('.acco-eight').on('click', function(){
			
			$(this).parent('.panel-group').addClass('acco-eight-active');
			$(this).parent('.panel-group').removeClass('acco-one-active');
			$(this).parent('.panel-group').removeClass('acco-two-active');
			$(this).parent('.panel-group').removeClass('acco-three-active');
			$(this).parent('.panel-group').removeClass('acco-four-active');
			$(this).parent('.panel-group').removeClass('acco-five-active');
			$(this).parent('.panel-group').removeClass('acco-six-active');
			$(this).parent('.panel-group').removeClass('acco-seven-active');
			$(this).parent('.panel-group').removeClass('acco-nine-active');
			$(this).parent('.panel-group').removeClass('acco-ten-active');
			$(this).parent('.panel-group').removeClass('acco-eleven-active');
			
			
		});
		
		$('.acco-nine').on('click', function(){
			
			$(this).parent('.panel-group').addClass('acco-nine-active');
			$(this).parent('.panel-group').removeClass('acco-one-active');
			$(this).parent('.panel-group').removeClass('acco-two-active');
			$(this).parent('.panel-group').removeClass('acco-three-active');
			$(this).parent('.panel-group').removeClass('acco-four-active');
			$(this).parent('.panel-group').removeClass('acco-five-active');
			$(this).parent('.panel-group').removeClass('acco-six-active');
			$(this).parent('.panel-group').removeClass('acco-seven-active');
			$(this).parent('.panel-group').removeClass('acco-eight-active');
			$(this).parent('.panel-group').removeClass('acco-ten-active');
			$(this).parent('.panel-group').removeClass('acco-eleven-active');
			
			
		});
		
		
		$('.acco-ten').on('click', function(){
			
			$(this).parent('.panel-group').addClass('acco-ten-active');
			$(this).parent('.panel-group').removeClass('acco-one-active');
			$(this).parent('.panel-group').removeClass('acco-two-active');
			$(this).parent('.panel-group').removeClass('acco-three-active');
			$(this).parent('.panel-group').removeClass('acco-four-active');
			$(this).parent('.panel-group').removeClass('acco-five-active');
			$(this).parent('.panel-group').removeClass('acco-six-active');
			$(this).parent('.panel-group').removeClass('acco-seven-active');
			$(this).parent('.panel-group').removeClass('acco-eight-active');
			$(this).parent('.panel-group').removeClass('acco-nine-active');
			$(this).parent('.panel-group').removeClass('acco-eleven-active');
			
			
		});
		
		
		$('.acco-eleven').on('click', function(){
			
			$(this).parent('.panel-group').addClass('acco-eleven-active');
			$(this).parent('.panel-group').removeClass('acco-one-active');
			$(this).parent('.panel-group').removeClass('acco-two-active');
			$(this).parent('.panel-group').removeClass('acco-three-active');
			$(this).parent('.panel-group').removeClass('acco-four-active');
			$(this).parent('.panel-group').removeClass('acco-five-active');
			$(this).parent('.panel-group').removeClass('acco-six-active');
			$(this).parent('.panel-group').removeClass('acco-seven-active');
			$(this).parent('.panel-group').removeClass('acco-eight-active');
			$(this).parent('.panel-group').removeClass('acco-nine-active');
			$(this).parent('.panel-group').removeClass('acco-ten-active');
			
			
			
		});

		$('.acco-twelve').on('click', function(){
			
			$(this).parent('.panel-group').addClass('acco-twelve-active');
			$(this).parent('.panel-group').removeClass('acco-one-active');
			$(this).parent('.panel-group').removeClass('acco-two-active');
			$(this).parent('.panel-group').removeClass('acco-three-active');
			$(this).parent('.panel-group').removeClass('acco-four-active');
			$(this).parent('.panel-group').removeClass('acco-five-active');
			$(this).parent('.panel-group').removeClass('acco-six-active');
			$(this).parent('.panel-group').removeClass('acco-seven-active');
			$(this).parent('.panel-group').removeClass('acco-eight-active');
			$(this).parent('.panel-group').removeClass('acco-nine-active');
			$(this).parent('.panel-group').removeClass('acco-ten-active');
			$(this).parent('.panel-group').removeClass('acco-eleven-active');
			
			
			
		});

		$('.acco-thirteen').on('click', function(){
			
			$(this).parent('.panel-group').addClass('acco-thirteen-active');
			$(this).parent('.panel-group').removeClass('acco-one-active');
			$(this).parent('.panel-group').removeClass('acco-two-active');
			$(this).parent('.panel-group').removeClass('acco-three-active');
			$(this).parent('.panel-group').removeClass('acco-four-active');
			$(this).parent('.panel-group').removeClass('acco-five-active');
			$(this).parent('.panel-group').removeClass('acco-six-active');
			$(this).parent('.panel-group').removeClass('acco-seven-active');
			$(this).parent('.panel-group').removeClass('acco-eight-active');
			$(this).parent('.panel-group').removeClass('acco-nine-active');
			$(this).parent('.panel-group').removeClass('acco-ten-active');
			$(this).parent('.panel-group').removeClass('acco-eleven-active');
			$(this).parent('.panel-group').removeClass('acco-twelve-active');
			
			
			
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
		
		
		$('.resources-tab .resources-add-cntnt-wrpr .cancel ').on('click', function(){
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
		

		// $('.resources-add-cntnt-wrpr .cancel, .resources-edit-cntnt-wrpr .cancel').on('click', function(){
		// 	$('.resources-tab .addForm').next('.list-resources').trigger('click');
		
		
       	/*Finance masters*/
			
		$('.accounttype-tab .addForm').on('click', function(){
			$('.accounttype-tab').addClass('addAccountTypeForm-active');
			
		})
		$('.accounttype-tab .cancel').on('click', function(){
			$('.accounttype-tab').removeClass('addAccountTypeForm-active');
			$('.accounttype-tab').removeClass('editAccountTypeForm-active');
			
		})
		
		$('.accounttype-tab .icon-pencil').on('click', function(){
			$('.accounttype-tab').addClass('editAccountTypeForm-active');
			
		})
		
		
		$('.accountgroups-tab .addForm').on('click', function(){
			$('.accountgroups-tab').addClass('addAccountGroupsForm-active');
			
		})
		
		$('.accountgroups-tab .cancel').on('click', function(){
			$('.accountgroups-tab').removeClass('addAccountGroupsForm-active');
			$('.accountgroups-tab').removeClass('editAccountGroupsForm-active');
			
		})
		
		$('.accountgroups-tab .icon-pencil').on('click', function(){
			$('.accountgroups-tab').addClass('editAccountGroupsForm-active');
			
		})
		
		$('.accountsubgroup-tab .addForm').on('click', function(){
			$('.accountsubgroup-tab').addClass('addAccountSubGroupsForm-active');
			
		})
		
		$('.accountsubgroup-tab .icon-pencil').on('click', function(){
			$('.accountsubgroup-tab').addClass('editAccountSubGroupsForm-active');
			
		})
		
		$('.accountsubgroup-tab .cancel').on('click', function(){
			$('.accountsubgroup-tab').removeClass('addAccountSubGroupsForm-active');
			$('.accountsubgroup-tab').removeClass('editAccountSubGroupsForm-active');
			
		})
		
		
		$('.accountheads-tab .addForm').on('click', function(){
			$('.accountheads-tab').addClass('addAccountHeadsForm-active');
			
		})
		
		$('.accountheads-tab .icon-pencil').on('click', function(){
			$('.accountheads-tab').addClass('editAccountHeadsForm-active');
			
		})
		
		$('.accountheads-tab .cancel').on('click', function(){
			$('.accountheads-tab').removeClass('addAccountHeadsForm-active');
			$('.accountheads-tab').removeClass('editAccountHeadsForm-active');
			
		})
		
		
		
		
		/*Finance masters end*/













		
		
		// jQuery(document).on( "click", ".approve", function(){ 
		// //$('.approve').on('click', function(){
		// 	jQuery('.approveOrder-cntnt').toggleClass('active');
		// 	//baseUrl= "//new.solminds.in";
		// 	 baseUrl=document.location.origin;
		// 	orderHref = baseUrl + jQuery(this).attr('data-url');
		// 	console.log(orderHref);
		// 	jQuery('#approveOrder').attr('src', orderHref);
		// 	setTimeout(function(){

  //              if(jQuery('.acco-confirmorders').offset()!==undefined){  
  //                 jQuery("html, body").animate({ scrollTop: jQuery('.acco-confirmorders').offset().top }, 100);
  //              }
		     
		//     },60);
			
			
			
		//   });
		
		// function placrorderCart3(){
		//     setTimeout(function(){
		// 		alert('hai');
				
                  
				
				
								
		// 	},600);
		// }
		
		jQuery(document).on( "click", ".approveorderbtn", function(){
		    
		   
		    
		    
		})
		
		
		
		
		
		
		jQuery('.cancel, .icon-close').on('click', function(){
			jQuery('.approveOrder-cntnt').removeClass('active');
			
			
		});
		



     	/* Sep 10 2020 */
			
			$(document).on('click','.account-heads-cards-wrpr .card input', function(){
				$('.account-heads-cards-wrpr .card').removeClass('active');
				$(this).parent('.card').addClass('active');
				
			});
			
		$(document).on('click','.fund-approval-tab .addForm', function(){	
			$('.fund-approval-tab').addClass('addRequestFundForm-active');
			
		});
		
		$(document).on('click','.account-heads-fund-request-form .account-heads-table-wrpr .form-groups .btn', function(){	
			$('.fund-approval-tab').removeClass('addRequestFundForm-active');
			
		})
		
		
			
			
		
		/* Sep 10 2020 */


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
		   


		$('.add-fv-btn').on('click', function(){
			 
			setTimeout(function(){
				$('.add-fr-cntnr').slideDown( "slow" );	
			},1000);
			
		});

		$(document).on('click','.btn-show-fa-hidden', function(e){
			e.preventDefault();
			$(this).addClass('fa-hidden-active');
			$(this).parents('.fa-list-cntnr').find('.fa-hidden').slideDown( "slow" );
			
			
			});
			
			$(document).on('click','.fa-hidden-active', function(e){
			e.preventDefault();
			$(this).removeClass('fa-hidden-active');
			$(this).parents('.fa-list-cntnr').find('.fa-hidden').slideUp( "slow" );
			
			
		});

		$('.journal-tab .addForm').on('click', function(){
			$('.journal-tab').addClass('addJournalForm-active');
			
			});

		/*sep 29 2020*/
		 
		 $(document).on('click','.tab .panel-heading', function(){	
				$('.tab').removeClass( "prev-tab" );
				$('.tab').removeClass( "next-tab" );
				
				$(this).parent().addClass( "prev-tab" );
				$(this).parent().prevAll().addClass( "prev-tab" );
				$(this).parent().nextAll().addClass( "next-tab" );
				$('.next-tab').css('top','');
				$('.prev-tab').css('top','');
				if($(this).parent().hasClass('acco-two')){
					$('.next-tab').css('top','-20px');
				}else if($(this).parent().hasClass('acco-three')){
					$('.next-tab').css('top','-40px');
				}else if($(this).parent().hasClass('acco-four')){
					$('.next-tab').css('top','-60px');
				}else if($(this).parent().hasClass('acco-five')){
					$('.next-tab').css('top','-80px');
				}else if($(this).parent().hasClass('acco-six')){
					$('.next-tab').css('top','-100px');
				}else if($(this).parent().hasClass('acco-seven')){
					$('.next-tab').css('top','-120px');
				}else if($(this).parent().hasClass('acco-eight')){
					$('.next-tab').css('top','-140px');
				}else if($(this).parent().hasClass('acco-nine')){
					$('.next-tab').css('top','-160px');
				}else if($(this).parent().hasClass('acco-ten')){
					$('.next-tab').css('top','-180px');
				}else if($(this).parent().hasClass('acco-eleven')){
					$('.next-tab').css('top','-200px');
				}else if($(this).parent().hasClass('acco-twelve')){
					$('.next-tab').css('top','-220px');
				}else if($(this).parent().hasClass('acco-thirteen')){
					$('.next-tab').css('top','-240px');
				}else if($(this).parent().hasClass('acco-fourteen')){
					$('.next-tab').css('top','-260px');
				}else if($(this).parent().hasClass('acco-fifteen')){
					$('.next-tab').css('top','-280px');
				}else if($(this).parent().hasClass('acco-sixteen')){
					$('.next-tab').css('top','-300px');
				}else if($(this).parent().hasClass('acco-seventeen')){
					$('.next-tab').css('top','-320px');
				}else if($(this).parent().hasClass('acco-eighteen')){
					$('.next-tab').css('top','-340px');
				}else if($(this).parent().hasClass('acco-nineteen')){
					$('.next-tab').css('top','-360px');
				}else if($(this).parent().hasClass('acco-twenty')){
					$('.next-tab').css('top','-380px');
				}
				
			});
		 
		 /*sep 29 2020 end*/

		/*oct 5 2020 start*/

		$(document).on('click','.tab-wrapper .addForm', function(){
			$(this).parents('.tab').addClass('add-form-active');
			$(this).parents('.tab').removeClass('edit-form-active');

		});
		$(document).on('click','.tab-wrapper .editForm', function(){
			$(this).parents('.tab').addClass('edit-form-active');
			$(this).parents('.tab').removeClass('add-form-active');

		});
		$(document).on('click','button.cancel,  button.save-btn', function(){	
			
			setTimeout(function(){
			
			$('.tab').removeClass('edit-form-active');
			$('.tab').removeClass('add-form-active');				
		},60);
			
			

		});
		//$('.addForm').on('click', function(){
			//$(this).parents('.tab').addClass('add-form-active');
			//$(this).parents('.tab').removeClass('edit-form-active');

		//});
		//$('.editForm').on('click', function(){
			//$(this).parents('.tab').addClass('edit-form-active');
			//$(this).parents('.tab').removeClass('add-form-active');

		//////});
		//$(document).on('click','.add-form-active button, .edit-form-active button', function(){	
			//$(this).parents('.tab').removeClass('edit-form-active');
			//$(this).parents('.tab').removeClass('add-form-active');

		//});

/*oct 5 2020 end*/
			
			//$('.add-journal-form button').on('click', function(){
			//$('.journal-tab').removeClass('addJournalForm-active');
			
		//})
			/*Oct 7 2020 start*/
		
		
		
/*		$('.allocate-resosurces-btn').on('click', function(){
			
			$.when($('.allocate-resosurces-popup').css('display','block')).then($('.allocate-resosurces-popup').css('top','150px'))
		});
		$('.close-popup').on('click', function(){
			
			$.when($('.allocate-resosurces-popup').css('top','-100px')).then($('.allocate-resosurces-popup').css('display','none'))
		});*/
		
	
	
	/*Oct 7 2020 start*/
	///////////////////////////////////////////need check

			/*Oct 7 2020 start*/
			
			
				/*Generate Voucher form start */
				//$(document).on('click','.generate-voucher-btn', function(){	
						//$('.vouchers-tab').addClass('generateVoucherFormActive');

					//});
					
				//$(document).on('click','.generate-voucher-form button', function(){	
						//$('.vouchers-tab').removeClass('generateVoucherFormActive');

					//});
				/*Generate Voucher form end */
				
				
				
				
				$(document).on('click','.allocate-resosurces-btn', function(){
				setTimeout(function(){
				$('.modal-container').addClass('custompopup-active');
				$('body').addClass('custompopup-loaded');
				}, 10);

				});

				$('.close-popup').on('click', function(){
					$('.modal-container').removeClass('custompopup-active');
					$('body').removeClass('custompopup-loaded');
					//$.when($('.allocate-resosurces-popup').css('top','-100px')).then($('.allocate-resosurces-popup').css('display','none'))
				});				
			
			
			/*Oct 7 2020 end*/	
			/*Oct 8 2020 start*/
				
				$('.added-item-allocation-save-btn').on('click', function(){
					$(this).parent().addClass('saving');
					$('.saving').children('.button-label').text('Saving');
					setTimeout(function(){
						$('.resources-from-selected-item-wrpr').addClass('resource-saved');
						$('.allocation-right-bar').addClass('resource-saved');
						$('.ton-column').addClass('col-md-2');
						$('.amount-column').addClass('col-md-3');
					}, 400);
				});
				
					$(document).on('click','.allocation-left-bar a', function(){
					$('.allocation-cntnt-wrpr').removeClass('resource-not-allocated');
					$('.allocation-left-bar a').removeClass('active');
					$(this).addClass('active');
					$('.resource-not-allocated-info').css('display','none');
					$('.resources-from-selected-item-wrpr').css('display','block');
					$('.saving').children('.button-label').text('Save');
					$('.saving').removeClass('saving');
					setTimeout(function(){
						$('.resources-from-selected-item-wrpr').removeClass('resource-saved');
						$('.allocation-right-bar').removeClass('resource-saved');
						//$('.ton-column').removeClass('col-md-2');
						//$('.amount-column').removeClass('col-md-3');
					}, 400);
				});
				
				
				
			
			
			/*Oct 8 2020 end*/
			/*Oct 16 2020 start*/
			$(document).on('click','.expand-collapse-palist ', function(){	
				$(this).toggleClass('collapse');

			});
	
	
		/*Oct 16 2020 end*/
		
		
});


/*Oct 21 2020 start*/
	

	$(document).on('click','.fav-project-wrpr.card', function(e){
		e.preventDefault();
		$('.fav-project-wrpr').removeClass('active');
		$(this).addClass('active');

	});
	

/*Oct 21 2020 end*/
			/*Oct 22 2020 start*/
			// override @ _activity.js
			// $(document).on('click','.add-project-activities-btn', function(e){
			// 	e.preventDefault();
			// 	$('.project-activities-list-wrpr').addClass('project-activities-add-form-active');
			// 	$('.activity-results-wrpr').addClass('col-md-9');
			// 	setTimeout(function() {
			// 		$("html, body").animate({ scrollTop: $('.project-activities-tab').offset().top }, 1000);
			// 		$(".added-activity-list-wrpr").animate({ scrollTop: $('.added-activity-list-wrpr').get(0).scrollHeight }, 1000);
			// 	}, 10);

			// });
		
		$(document).on('click','.close-activity-list-btn', function(e){
				e.preventDefault();
				$('.project-activities-list-wrpr').removeClass('project-activities-add-form-active');
				$('.activity-results-wrpr').removeClass('col-md-9');
				

			});
			
			
		

		$(document).on('click','.add-activity-search-results-content-wpr .icon-add', function(e){
				e.preventDefault();
				//console.log($('.added-activity-list-wrpr').offset().top);
				//var list = $('.added-activity-list-wrpr');
				//var newList = document.createElement("div");
				//$(newList).addClass('row');
				//newList.innerHTML = '<div class="col-md-4"><div class="row"><div class="col-md-2"><span class="number">4</span></div><div class="col-md-10 type"><label>Activity Name</label><span>Constitue Set-up Team</span></div></div></div><div class="col-md-8"><div class="row"><div class="col-md-2 type"><label>BOQ No.</label><span>213</span></div><div class="col-md-3 type"><label>Unit</label><span>No of Members</span></div><div class="col-md-3 type"><label>Amount</label><span>0.00</span></div><div class="col-md-4 icon-groups"><a class="btn btn-primary text-button" href="#"><span class="icon-dns"></span>Map BOQ</a><a class="btn btn-primary icon-pencil editForm" title="Edit" href="#"></a><a class="btn btn-danger  icon-trash1" title="Delete" href="#"></a></div></div></div> ';
				//list.append(newList);
				var x=$(".added-activity-list-wrpr").offset();
				//$(".added-activity-list-wrpr").offset({top : x.top + 61});
				setTimeout(function() {
					//newList.className = newList.className + " show";
					$(".added-activity-list-wrpr").animate({ scrollTop: $('.added-activity-list-wrpr').get(0).scrollHeight }, 1000);
				}, 10);
			});
					
	
	
	
	/*Oct 22 2020 end*/
				
			
			
			/*Oct 26 2020 start*/
				
			$(document).on('click','.resource-list-btn', function(e){
				e.preventDefault();
				$('.allocate-resource-tab').addClass('add-allocation-list-active');
				setTimeout(function() {
					//$("html, body").animate({ scrollTop: $('.allocate-resource-tab').offset().top }, 1000);
				}, 10);
		});
		
		$(document).on('click','.close-resource-list-btn', function(e){
				e.preventDefault();
				$('.allocate-resource-tab').removeClass('add-allocation-list-active');
		});
		
		
		$(document).on('click','.add-alloc-item', function(e){
				e.preventDefault();
				// console.log($('.allocation-list-items-cntnr').offset().top);
				// setTimeout(function() {
				// 	$("html, body").animate({ scrollTop: $('.allocate-resource-tab').offset().top }, 1000);
				// }, 10);
				// var list2 = $('.allocation-list-items-cntnr');
				// var newList2 = document.createElement("div");
				// $(newList2).addClass('row');
				// newList2.innerHTML = '<div class="col-md-1"><span class="number">2</span></div><div class="col-md-11 type vendor-column"><span>Material</span></div><div class="col-md-1"></div><div class="col-md-3 type  "><label>Vendor:</label><br><span>Siva Blue Metals - Mr. Sermaraj</span></div><div class="col-md-6 type vendor-column "><div class="row"><div class="col-md-3 type"><label>Material:</label><br><span>M Sand</span></div><div class="col-md-3 ton-column type"><label>Ton:</label><br><span>2</span></div><div class="col-md-3 type"><label>Rate:</label><br><span>1480</span></div><div class="amount-column type col-md-3"><label>Amount:</label><br><span>2960</span></div></div></div><div class="col-md-2 icon-groups"><a href="#" class="btn btn-primary icon-trash1"></a></div>';
				// list2.prepend(newList2);
				var x1=$(".added-activity-list-wrpr").offset();
				//$(".added-activity-list-wrpr").offset({top : x.top + 61});
				setTimeout(function() {
					//newList2.className = newList2.className + " show";
					$(".allocation-list-items-cntnr").animate({ scrollTop: $('.allocation-list-items-cntnr').get(0).scrollHeight }, 1000);
				}, 10);
			});
		
		
			
			
			
		
	/*Oct 26 2020 end*/
	
	
	/*Oct 27 2020 start*/
		
		
		// $(document).on('click','.order-history-btn ', function(e){
		// 		e.preventDefault();
		// 		$('.receive-materials-tab').addClass('order-history-cntnt-active');
				
		// });  **** Override @ custom Developer JS file
		 $(document).on('click','.close-order-history-btn ', function(e){
			e.preventDefault();
			$('.receive-materials-tab').removeClass('order-history-cntnt-active');		
		});
		
		
		$(document).on('click','.bill-history-btn ', function(e){
				e.preventDefault();
				$('.work-order-tab').addClass('bill-history-list-active');
				
		});
		$(document).on('click','.close-bill-history-btn ', function(e){
				e.preventDefault();
				$('.work-order-tab').removeClass('bill-history-list-active');
				
		});
		
	
	/*Oct 27 2020 end*/

		/*Oct 28 2020 start*/
				
				
		$(document).on('click','.muster-btn ', function(e){
			e.preventDefault();
			$('.raise-wage-roll-tab').addClass('muster-list-active');
			
	});
	$(document).on('click','.close-muster-btn ', function(e){
			e.preventDefault();
			$('.raise-wage-roll-tab').removeClass('muster-list-active');
			
	});
	
/*Oct 28 2020 end*/


/*Nov 2 2020 start*/
	
	$(document).on('click','.order-history-btn ', function(e){
			e.preventDefault();
			$('.invoice-leased-equipment-tab').addClass('invoice-le-order-history-list-active');
			
	});
	$(document).on('click','.close-order-history-btn ', function(e){
			e.preventDefault();
			$('.invoice-leased-equipment-tab').removeClass('invoice-le-order-history-list-active');
			
	});

	$(document).on('click','.despatch-Orders-btn ', function(e){
		e.preventDefault();
		$('.receive-plant-and-equipment-tab').addClass('despatch-orders-list-active');
				
	});
	$(document).on('click','.close-despatch-Orders-btn ', function(e){
			e.preventDefault();
			$('.receive-plant-and-equipment-tab').removeClass('despatch-orders-list-active');
			
	});
/*Nov 2 2020 start*/
			
			
/*Nov 3 2020 start*/
	
$(document).on('click','.despatch-Orders-btn ', function(e){
	e.preventDefault();
	$('.receive-plant-and-equipment-tab').addClass('despatch-orders-list-active');
	
});
$(document).on('click','.close-despatch-Orders-btn ', function(e){
	e.preventDefault();
	$('.receive-plant-and-equipment-tab').removeClass('despatch-orders-list-active');
	
});
/*Nov 3 2020 start*/

/*Nov 4 2020 start*/

$(document).on('click','.log-list-btn ', function(e){
	e.preventDefault();
	$('.log-equipment-usage-log-book-tab').addClass('log-list-active');
	
});
$(document).on('click','.close-log-list-btn ', function(e){
	e.preventDefault();
	$('.log-equipment-usage-log-book-tab').removeClass('log-list-active');
	
});
/*Nov 4 2020 start*/
/*Nov 18 2020 start*/
	
	$(document).on('click','.navbar-nav .prjcet-dashboard', function(e){
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
			}else if($('.overNow4').hasClass('active')){
				$( ".overNow4" ).removeClass("active");
				$('.menu4-popup-cntnr').removeClass('active');
				$('body').css('overflow-y','auto');
			}
			else if($('.overNow5').hasClass('active')){
				$( ".overNow5" ).removeClass("active");
				$('.menu5-popup-cntnr').removeClass('active');
				$('body').css('overflow-y','auto');
			}
			else if($('.overNow8').hasClass('active')){
				$( ".overNow8" ).removeClass("active");
				$('.menu8-popup-cntnr').removeClass('active');
				$('body').css('overflow-y','auto');
			}
			else if($('.overNow9').hasClass('active')){
				$( ".overNow9" ).removeClass("active");
				$('.menu9-popup-cntnr').removeClass('active');
				$('body').css('overflow-y','auto');
			}
			$(this).toggleClass('active');
			if($(this).hasClass('active')){
				$('.chart-popup-cntnr').addClass('active');
				$('#prjct_head').html('Project Dashboard');
				$('body').css('overflow-y','hidden');
				$('#listdasboard').trigger('click');
			}else{
				//return true;
				$('.chart-popup-cntnr').removeClass('active');
				$('body').css('overflow-y','auto');
			}
			
	});
	$(document).on('click','.chart-win-close ', function(e){
			e.preventDefault();
			$('.chart-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
			
	});
		
	/*Nov 18 2020 end*/
	
	/*Nov 20 2020 start*/
		
		/*Bill History */
		$(document).on('click', '.client-bill-tab .btn-bill-history ', function(e){
				e.preventDefault();
				$('.client-bill-tab').addClass('bill-history-active');
				
		});
		$(document).on('click', '.client-bill-tab .btn-bill-history-close', function(e){
				e.preventDefault();
				$('.client-bill-tab').removeClass('bill-history-active');
				
		});
		
		
		/* view client bill */
		$(document).on('click', '.client-bill-tab .btn-view-client-bill ', function(e){
				e.preventDefault();
				$('.client-bill-tab').addClass('view-client-bill-active');
				
		});
		$(document).on('click', '.client-bill-tab .view-client-bill-list-wrpr .btn.cancel', function(e){
				e.preventDefault();
				$('.client-bill-tab').removeClass('view-client-bill-active');
				
		});
		
		/* Raise client bill*/
		$(document).on('click', '.client-bill-tab .btn-raise-bill ', function(e){
				e.preventDefault();
				$('.client-bill-tab').addClass('raise-client-bill-active');
				
		});
		$(document).on('click', '.client-bill-tab .raise-bill-form .btn.cancel', function(e){
				e.preventDefault();
				$('.client-bill-tab').removeClass('raise-client-bill-active');
				
		});
		
		
		
	/*Nov 20 2020 end*/

	/*Nov 26 2020 start ~~~  Added by vishnu Tab close and Open 
	$(document).on( "click", "#rd5", function(){
		
		$(this).toggleClass('active');
		if($(this).hasClass('active')){
			return true;
		}else{
			$(this).prop('checked', false);
		}
	});
	$(document).on( "click", "#rd1", function(){
        
		$(this).toggleClass('active');
		if($(this).hasClass('active')){
			return true;
		}else{
			$(this).prop('checked', false);
		}
	});
	Nov 26 2020 end ~~~  Added by vishnu Tab close and Open  */

	$(document).on('click','.navbar-nav .MyAccountNAV', function(e){
		e.preventDefault();
		$('#project-title-head').html('Projects');
		$('#prjct_head').html('Operations');
		$('#finance-title-head').html('Finance');
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
		}else if($('.overNow4').hasClass('active')){
			$( ".overNow4" ).removeClass("active");
			$('.menu4-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
		}else if($('.overNow5').hasClass('active')){
			$( ".overNow5" ).removeClass("active");
			$('.menu5-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
		}else if($('.overNow8').hasClass('active')){
			$( ".overNow8" ).removeClass("active");
			$('.menu8-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
		}else if($('.overNow9').hasClass('active')){
			$( ".overNow9" ).removeClass("active");
			$('.menu9-popup-cntnr').removeClass('active');
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

	});
	$(document).on('click','.navbar-nav .menuNAV', function(e){
		e.preventDefault();
		/*if($('.overNow').hasClass('active')){
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
		}else if($('.overNow4').hasClass('active')){
			$( ".overNow4" ).removeClass("active");
			$('.menu4-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
			}else if($('.overNow5').hasClass('active')){
			$('#asset').hide();
			$( ".overNow5" ).removeClass("active");
			$('.menu5-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
		}else if($('.icon-dashboard').hasClass('active')){
			$( ".icon-dashboard" ).removeClass("active");
			$('.chart-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
		}else if($('.overNow6').hasClass('active')){
			$( ".overNow6" ).removeClass("active");
			$('.menu6-popup-cntnr').removeClass('active');
			$('body').css('overflow-y','auto');
			}*/
			
	});
/* Dec 09 2020 end ~~~  Added by vishnu Tab close and Open  */


	/*Feb 10 2021 start*/
				/*$(document).on('click', '.userrole-label-wrpr', function(e){
					$('.user-roles-body .userrole-label-wrpr').removeClass('active');
					$(this).addClass('active');
				});
				*/
				
				
				
				
				
				
				/*$(document).on('click', '.userrole-project', function(e){
					$('.userrole-functions-list').removeClass('active');
					$(this).addClass('active');
					$('.user-role-tab-wrpr').removeClass('active');
					$('.user-role-tab-wrpr.user-role-project-tab').addClass('active');
					
				});
				
				$(document).on('click', '.userrole-procurement', function(e){
					$('.userrole-functions-list').removeClass('active');
					$(this).addClass('active');
					$('.user-role-tab-wrpr').removeClass('active');
					$('.user-role-tab-wrpr.user-role-procurement-tab').addClass('active');
					
				});
				$(document).on('click', '.userrole-finance', function(e){
					$('.userrole-functions-list').removeClass('active');
					$(this).addClass('active');
					$('.user-role-tab-wrpr').removeClass('active');
					$('.user-role-tab-wrpr.user-role-finance-tab').addClass('active');
					
				});
				$(document).on('click', '.userrole-operations', function(e){
					$('.userrole-functions-list').removeClass('active');
					$(this).addClass('active');
					$('.user-role-tab-wrpr').removeClass('active');
					$('.user-role-tab-wrpr.user-role-operation-tab').addClass('active');
					
				});
				
				*/
			
			/*Feb 10 2021 start*/