$(document).on('click','#user-tab',function(){


$('#listusrs').trigger('click');
$('.finance-type-cntnt-wrpr').hide();

});




$(document).on('click','.createuser',function(){
		var Ufname = $('#usfname').val();
		var Ulname = $('#uslname').val();
		var Email = $('#usemail').val();
		var Username = $('#usrname').val();
		var Password = $('#uspswd').val();
		var Role = $('#usrole').val();
		var Status = $('#usstatus').val();
		var project = $('#usprjct').val();
        var accounthd = $('#usracnt').val();
		var account_type = $('#account_type').val();


        var error=0;

        if(Ufname == ''){
            $("#usfname").next("span").html('Enter firstname').show('slow');
            error=1;

        }
        if(Username == ''){
            $("#usrname").next("span").html('Enter Username').show('slow');
            error=1;

        }
        if(Password == ''){
            $("#uspswd").next("span").html('Enter Password').show('slow');
            error=1;

        }
        if(Role == ''){
            $("#usrole").next("span").html('Select User Role').show('slow');
            error=1;

        }
        if(UserNameExists($('#usrname').val())=='Yes')
        {
            $("#usrname").next("span").html('Username Exists').show('slow');
            error=1;
        }else{
            $("#usrname").next("span").html('').show('slow');
            error=0;
        }




        if(error==0){


		 $.ajax({
        type:"post",
        dataType: "json",
        url:'../projects/createuser',
        beforeSend:function(){
            $('#createuser').attr("disabled", true);
        },
        data:{firstname:Ufname,lastname:Ulname,email:Email,Username:Username,Password:Password,role:Role,Status:Status,project:project,accounthd:accounthd, account_type:account_type},
        success: function(data){
            if(data.error=='No'){
                $('#createuser').attr("disabled", false);

                $("#cretuss")[0].reset();
               
                $('#listusrs').trigger('click');
            }
        }
    });
    }
	});

$(function() {

	$('#listusrs').click(function () {

		$.ajax({
            type: 'POST',
            url: '../projects/listcreateuser',
            beforeSend : function(){
                
            },
            dataType: "json",
           
            success: function(data){
                if(data.error=='No')
                {
                    $('.listiserss').html(data.result);
                    $('.listiserss').show();
                    $('#userheader').show();
                    $('.finance-type-cntnt-wrpr').hide();
                }
               
            }
        });
   

	});
});
$(document).on('click','#addcruserrole',function(){
    $('.edituserss').hide();
	$('.listiserss').hide();
    $('#userheader').hide();
    $('.finance-type-cntnt-wrpr').show();

     $.ajax({
                type: 'POST',
                url: '../projects/userole',
                beforeSend : function(){
                   
                },
                dataType: "json",
                data: {},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#addusrr').html(data.result);
                        
                    }
                    
                }
            });



	});

$(document).on('click','#canceluser',function(){
	$('#listusrs').trigger('click');
	$('.listiserss').show();
    $('#userheader').show();
    $('.finance-type-cntnt-wrpr').hide();

});

$(document).on('click','.deleteusrbutton',function(){
    var userid=$(this).attr('data-v');

    var r = confirm("Are you sure you want to delete this User?");
    if(r==true){
        

        $.ajax({
                type: 'POST',
                url: '../projects/deleteusers',
                beforeSend : function(){
                    $('#deleteusrbutton'+userid).attr("disabled", true);
                },
                dataType: "json",
                data: {userid:userid},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#userrid'+data.id).remove();
                    }
                    else
                    {
                        alert(data.errortext);
                    }
    
                    $('#deleteusrbutton'+data.id).attr("disabled", false);
                }
            });
    }
});
$(document).on('click','.editusrbutton',function(){

    $('.edituserss').show();
    $('.listiserss').hide();
    $('#userheader').hide();
    

    var idval=$(this).data('id');
    
    
    $('.error').hide();
    
        $.ajax({
            type: 'POST',
            url: '../projects/updateuser',
            beforeSend : function(){
               
            },
            dataType: "json",
            data: {userid:idval},
            success: function(data){
                 // alert(data.durat);

                    $('.edituserss').html(data.datarow);

                }
            });
        
   
     
    
});

$(document).on('click','#updateuser',function(){

    var error = 0;
    var userid = $('#usrrname').attr('data-id');
    var uname = $('#usrrname').val();
     if(UserNameExistsupdate(uname,userid)=='Yes')
        {
            $("#usrrname").next("span").html('Username Exists').show('slow');
            error=1;
        }
         if(error==0){
                
            $.ajax({
                type: 'POST',
                url: '../projects/edituserss',
                beforeSend : function(){
                    //$('#saveeditproject'+idval).attr("disabled", true);
                },
                dataType: "json",
                data:$('#updateusers').serialize(),
                success: function(data){
                    if(data.error=='No')
                    { 
                        
                        $('#listusrs').trigger('click');
                        $(".finance-type-cntnt-wrpr").show();
                        $("#listuserrole-data").show();
                        $(".edituserss").hide();
                        
                        $(".addcruserrole").show();
                           
                       
                    }

                }
                });
        }
       

    });
$(document).on('click','#canceledittuser',function(){
    $('.edituserss').hide();
    $('.listiserss').show();
    $('#userheader').show();
    
    });
function UserNameExists(name)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../projects/checkusername',
        async:false,
        data: {name:name},
        success: function(data){
            retval=data;
        }
    });
    return retval;

}
function UserNameExistsupdate(name,id)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../projects/checkusernameupdate',
        async:false,
        data: {name:name,id:id},
        success: function(data){
            retval=data;
        }
    });
    return retval;

}
