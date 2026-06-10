$(document).on( "click", "#acc-heads", function(){   

    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    $('#account_heads_listing').show();
    $('#accountbsitemslist').hide();
    $('#accountbsitemsaddsection').hide();

    $('#listaccounts').trigger('click');
          
    $(this).parent('.panel-group').addClass('acco-four-active');
                
                
});

// project section function

// list project click
$(document).on( "click", "#listaccounts", function(){ 

    $('.accountheads-tab').removeClass('addAccountHeadsForm-active');
    $('#accountsaddsection').slideUp('slow');// slide down the project listing div

    $('#accountslistsection').slideDown('slow');// slide down the project listing div

    $('#listaccounts').removeClass('btn-danger').addClass('btn-success');

    $('#addaccounts').removeClass('btn-success').addClass('btn-danger');
    //alert($('#accounttype').val())

    $.ajax({

        type: 'POST',

        url: '../accountsitem/search',

        beforeSend : function(){

            $('.preloader_accnthead').show();

        },

        dataType: "json",

        data: {accounts:$('#searchaccounts').val(),subgrpid:$('#accountsubgrp').val(),type:$('#accounttype').val()},

        success: function(data){

            if(data.error=='No')

            {

                $('#accountsitems').html(data.result);
                $('#accountheadsearch').html(data.accountheadnames);
                if(data.type==8){
                    $('#addaccount').hide();
                }else{
                    $('#addaccount').show();
                }

            }

            $('.preloader_accnthead').hide();

        }

    });

});

$(document).on( "click", "#accountsearch", function(){  

   $('#listaccounts').trigger('click');

});

$(document).on( "change", "#searchaccounts", function(){  

   $('#accountsearch').trigger('click');

});

$(document).on( "click", "#searchaccounts", function(){  

    var searchvalue = $('#searchaccounts').val();

    if(searchvalue!=''){
        $('#searchaccounts').val('');
        $('#accountsearch').trigger('click');
    }

});


$(document).on( "change","#accountgrps", function(){

    $.ajax({

        type: 'POST',

        url: '../AccountsItem/AccountSubgroups',

        dataType:"json",

        data:{grpid:$('#accountgrps').val()},

        success: function(data){

            if(data.error=='No')

            {

                $('#subgroups').html(data.result);

            }

            else

            {

                alert(data.error);

            }

        }

    });

});

$(document).on( "change","#accountgrpsupdate", function(){

    $.ajax({

        type: 'POST',

        url: '../AccountSubgroups',

        dataType:"json",

        data:{grpid:$('#accountgrpsupdate').val()},

        success: function(data){

            if(data.error=='No')

            {

                $('#subgroups').html(data.result);

            }

            else

            {

                alert(data.error);

            }

        }

    });

});

$(document).on( "click", "#saveaccounts", function(){ 

    var error=0;

    $('.error').hide();

    if($('#accountsname').val()=='')

    {

        $("#accountsname").next("span").html('Enter Account Name').show('slow');

        error=1;

    }

    if(AccountName($('#accountsname').val())=='Yes')

    {

        $('#accountsname').next("span").html('Account Name Exists').show('slow');

        error=1;

    }

    if($('#accounttds').val()=='')

    {

        $("#accounttds").next("span").html('Enter TDS').show('slow');

        error=1;

    }

    if($('#accountservtax').val()=='')

    {

        $("#accountservtax").next("span").html('Enter Service Tax').show('slow');

        error=1;

    }

    if(error==0){

        if ($('#cash').is(':checked')) {

          var  account_type=1;

        }

        if ($('#bank').is(':checked')) {

            var  account_type=2;

        }

        if ($('#schedule').is(':checked')) {

            var  schedule=3;

        }

        $.ajax({

            type:'POST',

            url:'../accountsitem/create',

            beforeSend:function(){

                $('#saveaccounts').attr("disabled", true);

            },

            dataType:'json',

            data: $("#accountsform" ).serialize(),
            success:function(data){

                if(data.error=='No')

                {

                    $('#accountsform')[0].reset();

                    $('.accountheads-tab').removeClass('addAccountHeadsForm-active');
             
                    $('#accountsitems').show();

                    $('#listaccounts').trigger('click');

                    $('#saveaccounts').attr("disabled", false);

                    $('#searcselecttype').append($('<option>', {

                     value: data.Id,

                     text: data.Name

                     }));

                }

                else

                {

                    alert(data.errortext);

                }

                $('#saveaccounts').attr("disabled", false);

            }

        });

    }

});

$('.accountype').change(function() {

    if ($('.accountype').is(':checked')) {

        $('#projectlist').show();

    }

    else{

        $('#projectlist').hide();

    }

});

function AccountName(name)

{

    var retval;

    $.ajax({

        type: 'POST',

        url: '../accountsitem/checkname',

        async:false,

        data: {name:name},

        success: function(data){

            retval=data;

        }

    });

    return retval;

}
$(document).on("click",".linkresunitbtn",function(){

    var idval=$(this).val();
    $('#linkaccount_id').val(idval);
    var resunit=$('#resunitval'+idval).val();
    var accountid=$('#linkaccount_id').val();
    $.ajax({

        type: 'POST',

        url: '../AccountsItem/getacntresitem',

        dataType:"json",

        data:{resitem:resunit,account_id:accountid},

        success: function(data){

            if(data.error=='No')

            {
                $('#msacntname').html(data.accname);
                $('#accountsubgrplink').html(data.accval);
                $('#resourcegrouplink').html(data.resourcetypelist);
                $('.preloader_accnthead').hide();
                $('#linkresourceinfo').html('<input type="text" id="resourceslink" name="resources[]">');
                $('#resourceslink').magicSuggest({
                    maxSelection: 10,
                    allowFreeEntries: false,
                    data: data.grouparray
                });
                var namesassign=$('#resourceslink').magicSuggest({
                    maxSelection: 10,
                    allowFreeEntries: false,
                    data: data.grouparray
                });
                namesassign.setValue(data.grpids);

            }

        }

    });

});

$(document).on("click",".saveresunitbutton",function(){
    var idval=$(this).val();
    var resid=$('#editresunit'+idval).val();
    $.ajax({

        type: 'POST',

        url: '../AccountsItem/updateacntitem',

        dataType:"json",

        data:{acntitemid:idval,resid:resid},

        success: function(data){

            if(data.error=='No')

            {

                $('#editresunit'+idval).parents('.editresunit').hide();
                $('#editresunit'+idval).hide();
                $('.acc_container').removeClass('editing-resources');
                $('#saveresunitbutton'+idval).hide();
                $('#editresunitbtn'+idval).show();
                $('#resunittext'+idval).html(data.resname);
                $('#resunittext'+idval).show();

            }

        }

    });

});

$(document).on( "click", ".editaccountsbutton", function(){ 

    $('.accountheads-tab').addClass('editAccountHeadsForm-active');

    var idval=$(this).val();

    $.ajax({

        type: 'POST',

        url: '../accountsitem/updateaccountnew',

        beforeSend : function(){

            $('#deleteaccountsbutton'+idval).attr("disabled", true);

        },

        dataType: "json",

        data: {accountheadid:idval},

        success: function(data){

            if(data.error=='No')

            {
                $('#accountsitems').hide();
                $('#editaccountheads').html(data.result);
                
            }

        }

    });
   

} );

$(document).on( "click", ".saveaccountsbutton", function(){

    var idval=$(this).val();

    var error=0;

    $('.error').hide();

    if($('#editaccountsname'+idval).val()=='')

    {

        $('#editaccountsname'+idval).next("span").html('Enter Name').show('slow');

        error=1;

    }

    if($('#editaccountype'+idval).val()=='0')

    {

        $('#editaccountype'+idval).next("span").html('Select Accounthead').show('slow');

        error=1;

    }

    if($('#editaccountstds'+idval).val()=='')

    {

        $('#editaccountstds'+idval).next("span").html('Enter TDS').show('slow');

        error=1;

    }

    if($('#editaccountservtax'+idval).val()=='')

    {

        $('#editaccountservtax'+idval).next("span").html('Enter Service Tax').show('slow');

        error=1;

    }

    if ($('#schedulecheck'+idval).is(':checked')) {

        var  schedule=3;

    }

    if(error==0){

        $.ajax({

            type: 'POST',

            url: '../accountsitem/updateaccoundheads', 

            beforeSend : function(){

                $('#saveaccountsbutton'+idval).attr("disabled", true);

            },

            dataType: "json",

            data: {accountid:idval,accountname:$('#editaccountsname'+idval).val(),accounttds:$('#editaccountstds'+idval).val(),servicetax:$('#editaccountservtax'+idval).val(),account_type:$('#editaccountype'+idval).val(),schedule:schedule},

            success: function(data){

                if(data.error=='No')

                {

                    $('#editaccountsname'+data.Id).hide();

                    $('#editaccountstds'+data.Id).hide();

                    $('#editaccountservtax'+data.Id).hide();

                    $('#editaccountype'+data.Id).hide();

                    $('#editschedule'+data.Id).hide();

                    $('#editacntsubgrp'+data.Id).hide();

                    $('#saveaccountsbutton'+data.Id).hide();

                    $('#accountstext'+data.Id).text($('#editaccountsname'+data.Id).val()).show();

                    $('#accountstdstext'+data.Id).text($('#editaccountstds'+data.Id).val()).show();

                    $('#accountservtaxtext'+data.Id).text($('#editaccountservtax'+data.Id).val()).show();

                    $('#accountype'+data.Id).text(data.type).show();

                    $('#schedule'+data.Id).text(data.scheduletype).show();

                    $('#acntsubgrpstext'+data.Id).text($('#editacntsubgrp'+idval+' option:selected').text()).show();

                    $('#editaccountsbutton'+data.Id).show();

                }

                else

                {

                    alert(data.errortext);

                }

                $('#saveaccountsbutton'+data.Id).attr("disabled", false);

            }

        });

    }

});


$(document).on( "click", ".deleteaccountsbutton", function(){

    var idval=$(this).val();

    var r = confirm("Are you sure you want to delete this Account ?");

    if (r == true) {

        $.ajax({

            type: 'POST',

            url: '../accountsitem/deleteitem',

            beforeSend : function(){

                $('#deleteaccountsbutton'+idval).attr("disabled", true);

            },

            dataType: "json",

            data: {accountid:idval},

            success: function(data){

                if(data.error=='No')

                {

                    $('#accountsrow'+data.Id).remove();

                    $('#listaccounts').trigger('click');

                }

                else

                {

                    alert(data.errortext);

                }

                $('#deleteaccountsbutton'+data.Id).attr("disabled", false);

            }

        });

    } else {

        return false;

    }

});

$(document).on( "click", ".editaccountsbutton", function(){
    var id=$(this).val();
    $.ajax({
        type: 'POST',
        url: '../accountsitem/getsubgroups',
        beforeSend : function(){
            $('.preloader_accnthead').show();
        },
        dataType: "json",
        data: {accountid:id},
        success: function(data){
            if(data.error=='No')
            {
                $('#subgrpdetails').html(data.result);
                $('#accountid').val(id);
                $('.preloader_accnthead').hide();
            }
        }

    });

});

/*$(document).on( "change", ".accountgroup", function(){  
    var accountgroup=$(this).val();  
    if($(this). prop("checked") == true){  //alert ("hi22");
        $.ajax({
            type: 'POST',
            url: '../accountschedule/getsubgroups',
            data: {accountgroup:accountgroup},
            success: function(data){

                $('#account_subgrps'+accountgroup).html(data);  
            }
        });
        if (accountgroup==6){
            //var databsitem='<option value="0">Select BS Item</option>';
            //$('#bsitems'+accountgroup).html(databsitem);
            $('#bsitems'+accountgroup).show();
        }
    }
    else
    {
        var data='<option value="0">Select Account Sub-Groups</option>';
        $('#accountsubgrps'+accountgroup).html(data);
    }
});*/

$(document).on( "click", "#updatesubgrpacnts", function(){
    $.ajax({
        type: 'POST',
        url: '../AccountsItem/updatesubgroups',
        data: $( "#subgrpseditform" ).serialize(),
        success: function(data){
            $('#myModal').modal('toggle');
        }
    });
});

$(document).on( "change", "#resourcegrouplink", function(){
    var resourcegroup = $(this).val();
    if(resourcegroup!=0){
        $.ajax({
            type: 'POST',
            dataType:'json',
            url: '../AccountsItem/Resources',
            data: {groupid:resourcegroup},
            success: function(data){
                //console.log(data)
                $('#linkresourceinfo').html('<input type="text" id="resourceslink" name="resources[]">');
                $('#resourceslink').magicSuggest({
                    maxSelection: 10,
                    allowFreeEntries: false,
                    data: data
                });
            }
        });
    }

});
$(document).on( "click", "#updatelinkaccount", function(){
    $.ajax({
        type: 'POST',
        url: '../accountsitem/updateresitem',
        data: $( "#accountlinkform" ).serialize(),
        success: function(data){
            $('#LinkRes').modal('toggle');
        }
    });
});

$(document).on( "click", "#addaccount", function(){
    $('#accountsitems').hide();
});

$(document).on( "change","#accountsubgrp", function(){
    $.ajax({

        type: 'POST',

        url: '../accountsitem/resourcetype',

        dataType:"json",

        data:{accountsubgrp:$('#accountsubgrp').val()},

        success: function(data){

            if(data.error=='No')
            {
                $('#resourcetype').html(data.result);

            }
            else
            {
                alert(data.error);
            }

        }

    });

});

$(document).on( "change", "#resourcetype", function(){

    var resourcetype = $('#resourcetype').val();

    if(resourcetype!=0){
        $.ajax({
            type: 'POST',
            dataType:'json',
            url: '../accountsitem/resources',
            data: {groupid:resourcetype},
            success: function(data){
                //console.log(data)
                $('#linkresourceinfo').html('<input type="text" id="resources" name="resources[]">');
                $('#resources').magicSuggest({
                    maxSelection: 10,
                    allowFreeEntries: false,
                    data: data
                });
            }
        });
    }
});

// new jquery

$(document).on( "change", ".accountgroup", function(){ 
    var accountgroup=$(this).val();        
    if($(this). prop("checked") == true){             
        $.ajax({
            type: 'POST',
            url: '../accountschedule/getsubgroups',                        
            data: {accountgroup:accountgroup},
            success: function(data){
                $('#accountsubgrps'+accountgroup).html(data);   
                $('#account_subgrps'+accountgroup).html(data);             
            }
        });
        if (accountgroup==6){ 
            var databsitem='<option value="0">Select Schedule Items</option>';
            $('#bsitems'+accountgroup).html(databsitem);
            $('#bsitems'+accountgroup).show();
            $('#bs_items'+accountgroup).html(databsitem);
            $('#bs_items'+accountgroup).show();
        }
    }
    else
    {        
        var data='<option value="0">Select Account Sub-Groups</option>';
        var datasched='<option value="0">Select Account Schedule</option>';            
        $('#accountsubgrps'+accountgroup).html(data);
        $('#account_subgrps'+accountgroup).html(data);   
        $('#accountschedule'+accountgroup).html(datasched);
        $('#bsitems'+accountgroup).hide();
        $('#bs_items'+accountgroup).hide();
                
    }                                      
});     

$(document).on( "change", ".accountsubgrps", function(){          
    var accountgroup=$(this).data('id');
    var accountsubgroup=$(this).val();        
    if(accountsubgroup !=''){            
        $.ajax({
            type: 'POST',
            url: '../accountschedule/getschedules',                        
            data: {accountgroup:accountgroup,accountsubgroup:accountsubgroup},
            success: function(data){
                $('#accountschedule'+accountgroup).html(data);                
            }
        });         
    }
    else
    {                    
        var datasched='<option value="0">Select Account Schedule</option>';               
        $('#accountschedule'+accountgroup).html(datasched);  
    }        
           
});
$(document).on( "change", ".accountsubgrps", function(){   
    var accountgroup=$(this).data('id');
    var accountsubgroup=$(this).val();
    if(accountsubgroup !=''){
        $.ajax({
            type: 'POST',
            dataType: "json",
            url: '../accountssub/getbsitems',
            data: {accountsubgroup:accountsubgroup},
            success: function(data){
                $('#bsitems'+accountgroup).html(data.result);
                $('#bs_items'+accountgroup).html(data.result);
            }
        });
    }
    else
    {
        var datasched='<option value="0">Select Schedule Items</option>';
        $('#bsitems'+accountgroup).html(datasched);
        $('#bs_items'+accountgroup).html(datasched);
    }

});

$(document).on( "change", "#saveaccounts", function(){   
    var error=0;
    $('.error').hide();
    var str = $('#accountsname').val();
    if(str=='')
    {
        $("#accountsname").next("span").html('Enter Account Name').show('slow');
        error=1;
    }
    if($('#accounttype').val()==0){
        $("#accounttype").next("span").html('Select Account type').show('slow');
        error=1;
    }

    if (error==0){
        return true;
    }
    else {
        return false;
    }
});

$(document).on("click", "#cancel", function(){  

    $('.accountheads-tab').removeClass('editAccountHeadsForm-active');
    $('#accountsitems').show();

});

$(document).on("click", ".cancel", function(){  

    $('#accountsitems').show();

});

$(document).on( "click", ".saveaccountheadss", function(){  

    var idval=$(this).val();

    if ($('#schedule'+idval).is(':checked')) {  

         var  schedule=3;

    }

    $.ajax({

        type: 'POST',

        url: '../accountsitem/updateaccount', 

        beforeSend : function(){

            $('.saveaccountheadss'+idval).attr("disabled", true);

        },

        dataType: "json",

        data: $("#accountseditform" ).serialize(),

        //data: {accounthead:idval,accountsname:$('#accountsname'+idval).val(),accounttds:$('#accounttds'+idval).val(),accountservtax:$('#accountservtax'+idval).val(),accounttype:$('#accounttype'+idval).val(),schedule:schedule},
        success: function(data){

            if(data.error=='No')

            {

                $('#accountseditform')[0].reset();
                $('.accountheads-tab').removeClass('editAccountHeadsForm-active');
                $('#accountsitems').show();
                $('#listaccounts').trigger('click');

            }

            else

            {

                alert(data.errortext);

            }

            $('.saveaccountheadss'+data.Id).attr("disabled", false);

        }

    });

});


$(document).on( "click", ".starred", function(){

    var id = $(this).data("id");
    var val = $(this).data("value");

    $.ajax({
        type: 'POST',
        url: '../accountsitem/favouriteaccnt',
        async:false,
        data: {id:id,val:val},
        success: function(data){
            if(val==1)
            {
                
                $('#favourites'+id).removeClass('added-to-fav');
                $('#favourites'+id).data('value',0);
                $('#fundreqradio').trigger('click');
            }
            else
            {
                 
                $('#favourites'+id).addClass('added-to-fav');
                $('#favourites'+id).data('value',1);
                $('#fundreqradio').trigger('click');
            }
        }
    });

    });

$(document).on('click','.editschedule_item',function(){
    var accountid=$(this).val();
    $('#accounthead_id').val(accountid);
    //$('#bsitems').trigger('click');

    $.ajax({

        type: 'POST',

        url: '../accountssub/accountheadbsitems',

        dataType: "json",

        data: {accountid:accountid},
        success: function(data){

            if(data.error=='No')

            {

                $('#account_heads_listing').hide(); 
                $('#accountbsitemslist').html(data.result); 
                $('#accountbsitemslist').show(); 

            }

            else

            {

                alert(data.errortext);

            }

            //$('.preloader').hide();

        }

    });

});

$(document).on('click','#accountaddbsitems',function(){
    $('#accountbsitemslist').hide();
    $('#accountaddbsname').val('');
    $('#accountbsitemsaddsection').show();
});

$(document).on('click','.accountbackbsitems',function(){
    $('#accountbsitemslist').hide();
    $('#account_heads_listing').show();
});

$(document).on('click','#accountcancelbscreate',function(){
    $('#accountbsitemslist').show();
    $('#accountbsitemsaddsection').hide();
});

$(document).on('click','.accountsavebscreate',function(){
    var accounthead_id= $('#accounthead_id').val();

    var name = $('#accountaddbsname').val();

    if(name!=''){

        $.ajax({

            type: 'POST',

            url: '../accountssub/accountcreatebsitem',

            dataType: "json",

            data: {accounthead_id:accounthead_id,bsitemname:name},
            success: function(data){

                if(data.error=='No')

                {

                    $('#accountbsitemsaddsection').hide(); 
                    $('#accountbsitemslist').html(data.result); 
                    $('#accountbsitemslist').show(); 

                }

                else

                {

                    alert(data.errortext);

                }

                //$('.preloader').hide();

            }

        });

    }
    else{
        $("#accountaddbsname").next("span").html('Enter Schedule Item').show('slow').delay(3000).fadeOut();
    }

});

$(document).on('click','.accounteditbsitembutton',function(){
    var id=$(this).val();
    $('#accountacntbsitemtext'+id).hide();
    $('#accounteditbsname'+id).show();
    $('#accounteditbsitembutton'+id).hide();
    $('#accountsavebsitembutton'+id).show();
});

$(document).on('click','.accountsavebsitembutton',function(){
    var id= $(this).val();

    var name = $('#accounteditbsname'+id).val();

    if(name!=''){

        $.ajax({

            type: 'POST',

            url: '../accountssub/updatebsitem',

            dataType: "json",

            data: {itemid:id,name:name},
            success: function(data){

                if(data.error=='No')

                {
                   
                    $('#accountacntbsitemtext'+id).html(data.result); 
                    $('#accountacntbsitemtext'+id).show();
                    $('#accounteditbsname'+id).hide();
                    $('#accounteditbsitembutton'+id).show();
                    $('#accountsavebsitembutton'+id).hide();

                }

                else

                {

                    alert(data.errortext);

                }

                //$('.preloader').hide();

            }

        });

    }
    else{
        $("#accounteditbsname"+id).next("span").html('Enter Schedule Item').show('slow').delay(3000).fadeOut();
    }

});

$(document).on( "click", ".accountdeletebsitembutton", function(){

    var idval=$(this).val();

    var r = confirm("Are you sure you want to delete this Account ?");

    if (r == true) {

        $.ajax({

            type: 'POST',

            url: '../accountssub/deletebsitem',

            beforeSend : function(){

                $('#accountdeletebsitembutton'+idval).attr("disabled", true);

            },

            dataType: "json",

            data: {bsitemid:idval},

            success: function(data){

                if(data.error=='No')

                {

                    $('.bsitemrow'+idval).remove();


                }

                
                $('#accountdeletebsitembutton'+idval).attr("disabled", false);

            }

        });

    } else {

        return false;

    }

});
$(document).on( "click", "#backaccount", function(){ 

    var type = $('#identify').val();

    if(type == 'accounttype')
    {
      

        $('#collapseaccnttyp').addClass('in');

        $('.accounttype-tab').addClass('active');

        $('.accountheads-tab').removeClass('active');

        $('#collapseaccnts').removeClass('in');

        $('.panel-group').addClass('acco-one-active');
        $('.panel-group').removeClass('acco-four-active');

        $('.acco-two').removeClass('prev-tab');
        $('.acco-two').addClass('next-tab');

        $('.acco-three').removeClass('prev-tab');
        $('.acco-three').addClass('next-tab');

        $('.acco-four').removeClass('prev-tab');
        $('.acco-four').addClass('next-tab');

        $("#collapseaccnttyp").attr("aria-expanded","true");
        
        $("#collapseaccnts").attr("aria-expanded","false");
        $('#collapseaccnts').css('height','');

    }else if(type == 'accntsubgrp')
    {

        $('#collapseaccntsubgrp').addClass('in');

        $('.accountsubgroup-tab').addClass('active');

        $('.accountheads-tab ').removeClass('active');

        $('#collapseaccnts').removeClass('in');

        $('.panel-group').addClass('acco-three-active');
        $('.panel-group').removeClass('acco-four-active');

        $('.acco-one').removeClass('prev-tab');
        $('.acco-one').addClass('next-tab');

        $('.acco-two').removeClass('prev-tab');
        $('.acco-two').addClass('next-tab');

        $('.acco-four').removeClass('prev-tab');
        $('.acco-four').addClass('next-tab');


        $("#collapseaccntsubgrp").attr("aria-expanded","true");

        $("#collapseaccnts").attr("aria-expanded","false");
        $('#collapseaccnts').css('height','');
    }else if(type == 'accountgrphead')
    {

        
        $('#listacntsubgrps').trigger('click');

        $('#collapseaccntsubgrp').addClass('in');

        $('.accountsubgroup-tab').addClass('active');

        $('.accountheads-tab ').removeClass('active');

        $('#collapseaccnts').removeClass('in');
         
        $('.panel-group').addClass('acco-three-active');
        $('.panel-group').removeClass('acco-four-active');

        $('.acco-one').removeClass('prev-tab');
        $('.acco-one').addClass('next-tab');

        $('.acco-two').removeClass('prev-tab');
        $('.acco-two').addClass('next-tab');

        $('.acco-four').removeClass('prev-tab');
        $('.acco-four').addClass('next-tab');

        $("#collapseaccntsubgrp").attr("aria-expanded","true");

        $("#collapseaccnts").attr("aria-expanded","false");
        $('#collapseaccntsubgrp').css('height','');
    }

    

    });