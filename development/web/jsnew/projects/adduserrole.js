/*$(document).on( "click", ".accounttype-tabs", function(){

			 $('#finance-type-cntnt-wrpr').show();
			 $('#userrolelists').trigger('click');
			 

		});*/





	
$(document).on('click','#userrole-tab',function(){
$('#edituserroles').hide();
	

		$.ajax({
            type: 'POST',
            url: '../projects/listuserrole',
            beforeSend : function(){},
            dataType: "json",
            data: {},
            success: function(data){
                if(data.error=='No')
                {
                    $('#finance-type-cntnt-wrpr').show();
                    $('#listuserrole-data').html(data.result);
                    
                }
                else
                {
                    alert(data.errortext);
                }

               
            }
        });


	});


$(document).on('click','#adduserrole',function(){


   $('#functform')[0].reset();
	
	$('.creuserroles').show(); 
	$('#listuserrole-data').hide();
	$('#adduserrole').hide();
	$('#edituserroles').hide();
	$('#userolecreate').attr("disabled", false);

});

$(document).on('click','.deleterolebutton',function(){
	var roleid=$(this).attr('data-v');

	var r = confirm("Are you sure you want to delete this User Role?");
	if(r==true){
		$.ajax({
                type: 'POST',
                url: '../projects/deleteuserrole',
                beforeSend : function(){
                    $('#deleterolebutton'+roleid).attr("disabled", true);
                },
                dataType: "json",
                data: {roleid:roleid},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#roleidrow'+data.id).remove();
                    }
                    else
                    {
                        alert(data.errortext);
                    }
    
                    $('#deleterolebutton'+data.id).attr("disabled", false);
                }
            });
	}
});

$(document).on('click','.editrolebutton',function(){

	$('#edituserroless').show();
	$('#listuserrole-data').hide();
	$('#adduserrole').hide();

	var idval=$(this).data('id');
	$('#rolesave').val(idval);
	 var error=0;
    $('.error').hide();
    if(error==0){
        $.ajax({
            type: 'POST',
            url: '../projects/updaterole',
            beforeSend : function(){
               
            },
            dataType: "json",
            data: {roleid:idval},
            success: function(data){
                 // alert(data.durat);

                    $('#edituserroless').html(data.datarow);

                }
            });
        }
   
	 
	
});

$(document).on('click','#useroleupdate',function(){


	var error=0;
    $('.error').hide();
    
        if(error==0){
        	
        $.ajax({
            type: 'POST',
            url: '../projects/edituserrole',
            beforeSend : function(){
                //$('#saveeditproject'+idval).attr("disabled", true);
            },
            dataType: "json",
            data:$('#functform').serialize(),
            success: function(data){
                if(data.error=='No')
                {
                    
                    $('#userrole-tab').trigger('click');
	                $(".finance-type-cntnt-wrpr").show();
	                $("#listuserrole-data").show();
	                $(".edituserroless").hide();
	                $(".creuserroles").hide();
	                $("#adduserrole").show();
                       
                   
                }

            }
            });
        }

	});

$(document).on('click','.userolecreate',function(){

		 
		var urname = $('#urname').val();
		var urstatus = $('#urstatus').val();

		var error = 0;

		if(urname == '')
		{
			$("#urname").next("span").html('Enter User Role').fadeIn().delay(3000).fadeOut();
            error=1;
		}

		//var functions = $('#functio').val();
		//var functions = $('#functio:checked').val();
		//var functions = $('input[name="fucn[]"]:checked').serialize();
		//alert(functions)
		//var functiontab=$('#functtabss:checked').val();
		//alert(functiontab)

		if(error == 0)
		{

			 $.ajax({
		        type:"post",
		        dataType: "json",
		        url:'../projects/createuserrole',
		        beforeSend:function(){
                    $('#userolecreate').attr("disabled", true);
                },
		        data:$('#functform').serialize(),
		        success: function(data){
		            if(data.error=='No'){
		            	$("#functform")[0].reset();
		            	$('#userolecreate').attr("disabled", false);
		                $('#userrole-tab').trigger('click');
		                $(".finance-type-cntnt-wrpr").show();
		                $("#listuserrole-data").show();
		                $(".edituserroless").hide();
		                $(".creuserroles").hide();
		                $("#adduserrole").show();
		                $('#vall').html(data.dropdwn);
		                $('#vall').val(data.values);
		            }
		        }
		      });

		}


		
	});
		$(document).on('click','#canceledit',function(){
				$('#userrole-tab').trigger('click');
                $(".finance-type-cntnt-wrpr").show();
                $("#listuserrole-data").show();
                $(".edituserroless").hide();
                $(".creuserroles").hide();
                $("#adduserrole").show();

});
		$(document).on('click','#cancelcreate',function(){
				$('#userrole-tab').trigger('click');
                $(".finance-type-cntnt-wrpr").show();
                $("#listuserrole-data").show();
                $(".edituserroless").hide();
                $(".creuserroles").hide();
                $("#adduserrole").show();

});

$(document).on('click','#checkallproj',function(){
        

    
    
    if($('#checkhidden').val()==0){
		$('#checkhidden').val('1');
		$(".checkclassproj").prop('checked', true);
	}else{
		$('#checkhidden').val('0');
		$(".checkclassproj").prop('checked', false);
	}
	


 });
     
 
 $(document).on('click','#checkallprocu',function(){
    
    if($('#check1hidden').val()==0){
		$('#check1hidden').val('1');
		$(".checkclassprocu").prop('checked', true);
	}else{
		$('#check1hidden').val('0');
		$(".checkclassprocu").prop('checked', false);
	}
 });

 $(document).on('click','#checkallfin',function(){
        
    
    
    if($('#check2hidden').val()==0){
		$('#check2hidden').val('1');
		$(".checkclassfin").prop('checked', true);
	}else{
		$('#check2hidden').val('0');
		$(".checkclassfin").prop('checked', false);
	}
 });

$(document).on('click','#checkalloper',function(){
        
   

    if($('#check3hidden').val()==0){
		$('#check3hidden').val('1');
		$(".checkclassoper").prop('checked', true);
	}else{
		$('#check3hidden').val('0');
		$(".checkclassoper").prop('checked', false);
	}
 });
    



$(document).on('click', '.userrole-project', function(){
			var dep_id=$(this).data('id');
			//alert(dep_id)

			
			if(dep_id==1){
				$('.user-role-finance-tab').hide();
				$('.userrole-functions-list').removeClass('active');
				$(this).addClass('active');
				$('.user-role-tab-wrpr').removeClass('active');
				$('.user-role-tab-wrpr.user-role-project-tab').addClass('active');

					}
					else if(dep_id==2){
						$('.user-role-finance-tab').hide();
						$('.userrole-functions-list').removeClass('active');
						$(this).addClass('active');
						$('.user-role-tab-wrpr').removeClass('active');
						$('.user-role-tab-wrpr.user-role-procurement-tab').addClass('active');
					}
					else if(dep_id==3){
						$('.user-role-finance-tab').show();
						$('.userrole-functions-list').removeClass('active');
					    $(this).addClass('active');
					    $('.user-role-tab-wrpr').removeClass('active');
						$('.user-role-tab-wrpr.user-role-finance-tab').addClass('active');
					}
					
					else if(dep_id==4){
						$('.user-role-finance-tab').hide();
						$('.userrole-functions-list').removeClass('active');
						$(this).addClass('active');
						$('.user-role-tab-wrpr').removeClass('active');
						$('.user-role-tab-wrpr.user-role-operation-tab').addClass('active');
					}

					
				});

		$(document).on('click', '.finance_role', function(){
			$tabid=$(this).data('id');
			//alert($tabid)
			if($tabid==11){
			/*$('.user-roles-body').removeClass('active');
			$(this).addClass('active');
			$('.user-role-micro-permission').removeClass('active');
			$('.user-role-micro-permission.fincancetab-micro').addClass('active');*/
			}
		});


		    