$(document).on( "click", "#issueslips", function(){
    $('#issueslipsaddForm').trigger('click');
    $('.slipsss').addClass('active');
    $('.scndslips').hide();
    $('.firtslip').show();
    $('.nav-tabs').show();
});
$(document).on( "click", "#materilslip", function(){
    $('#issueslipsaddForm').trigger('click');
    $('.scndslips').hide();
    $('.firtslip').show();
   
});


$(document).on( "click", "#historys", function(){   
   
      
       //$('.imdata').hide();
       $('.frstwrapp').show();
       $('.secwrapp').hide();
      

   });

$(document).on( "click", "#fuelhistory", function(){   
   
      
       //$('.imdata').hide();
       $('.secwrapp').show();
        $('.frstwrapp').hide();
      

   });


$(document).on( "click", "#issueslipsaddForm", function(){   
   
      
       //$('.imdata').hide();
      // $('.secwrapp').show();
        $('.frstwrapp').hide();
          $('#isuzslp').css("display", "none");
        
      

   });







$(function() { 

    //$('.cancel').click(function(){
         $(document).on('click','.cancel',function(){
        $.ajax({
            type: 'POST',
            url: '../report/issueslipsearch',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {resourcename:$('#issresval').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#issueslipsitems').html(data.result);
                    $("#rmdtaa").css("display", "none");
                    $('#shozz').css('display','block');
                    $('#tabshw').css('display','block');
                    $('#prodropname').html(data.pname);
                    //$('.nav-tabs').hide();
                    $('#headerzz').css('display','block');
                    $('#isuzslp').css("display", "block");
                    $('#cllist').css("display", "block");
                    $('#rbll').css("display", "none");
                    $('#rbll').css("display", "none");
                    $('#rbll').css("display", "none");
                    $('#msttems').css("display", "block");
                    $('#viw').css("display", "none");

                    //$('#vcbill').css("display", "none");
                    
                    

                    //$('#Issue-Material-Slips-head').show();
                }
                $('.preloader').hide();
            }
        });
    });
    $(document).on('click','#listissuesearch',function(){
        $('.cancel').trigger('click');
    });
    $(document).on('click','#issueslipsaddForm',function(){
        $.ajax({
            type: 'POST',
            url: '../report/issueslipform',
            beforeSend : function(){
               // $('.preloader').show();
            },
            dataType: "json",
            success: function(data){
                if(data.error=='No')
                {
                    $('#issueslipsadd').html(data.result);
                    $('.nav-tabs').show();
                    $('#isuzslp').css("display", "none");
                }
                $('.preloader').hide();
            }
        });
    });
    $(document).on( "change","#projissuesliplist", function(){
        $.ajax({
            type: 'POST',
            url: '../report/getiow',
            dataType: "json",
            success: function(data){
                if(data.error=='No')
                {
                    $('.issueslipiowlist').html(data.result);
                }
            }
        });
    });
    $(document).on( "change",".issueslipiowlist", function(){
        var id=$(this).attr('data-id');
        var iow=$(this).val();
        var projectid=$('#projissuesliplist').val();
        $.ajax({
            type: 'POST',
            url: '../report/getactivity',
            dataType: "json",
            data: {projectid:projectid,iow:iow},
            success: function(data){
                if(data.error=='No')
                {
                    $('#issueslipactivity'+id).html(data.result);
                }
            }
        });
    });
    $(document).on( "change",".issueslipactivity", function(){
        var id=$(this).attr('data-id');
        var activity=$(this).val();
        $.ajax({
            type: 'POST',
            url: '../report/getresources',
            dataType: "json",
            data: {activityid:activity},
            success: function(data){
                if(data.error=='No')
                {
                    $('#IssueslipResource'+id).html(data.result);
                }
            }
        });
    });
    $(document).on( "change",".IssueslipResource", function(){
        var id=$(this).attr('data-id');
        var resource=$(this).val();
        var activityid=$('#issueslipactivity'+id).val();
        var iowid=$('#issueslipiowlist'+id).val();
        var projectid=$('#projissuesliplist').val();
        $.ajax({
            type: 'POST',
            url: '../report/resdetails',
            dataType: "json",
            data: {resid:resource,activityid:activityid,projectid:projectid,iowid:iowid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#IssueslipUnit'+id).val(data.result);
                }
            }
        });
    });
    $(document).on('click','#issueslipreport',function(){
        var error=0;
        $('.error').hide();
        /*if($('#issueslipactivity').val()=='none')
        {
            $('#issueslipactivity').next("span").html('Select Activity').show('slow');
            error=1;
        }
        if($('#issueslipquantity').val()=='')
        {
            $('#issueslipquantity').next("span").html('Enter Quantity').show('slow');
            error=1;
        }*/
        $('.issueslipdate').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='')
            {
                $("#issueslipdate"+id).next("span").html('Select Date').show('slow');
                error=1;
            }
    
        });
        /*$('.isslipwbsstructlist').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='none')
            {
                $("#isslipwbsstructlist"+id).next("span").html('Select Structure').show('slow');
                error=1;
            }
    
        });*/
        $('.issueslipiowlist').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='none')
            {
                $("#issueslipiowlist"+id).next("span").html('Select IOW').show('slow');
                error=1;
            }
    
        });
        $('.issueslipactivity').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='none')
            {
                $("#issueslipactivity"+id).next("span").html('Select Activity').show('slow');
                error=1;
            }
    
        });
        $('.IssueslipResource').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='none')
            {
                $("#IssueslipResource"+id).next("span").html('Select Resource').show('slow');
                error=1;
            }
    
        });
        $('.IssuedQuantity').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='')
            {
                $("#IssuedQuantity"+id).next("span").html('Enter Quantity').show('slow');
                error=1;
            }
    
        });
    
    
        if(error==0){
            $.ajax({
                type: 'POST',
                url: '../report/saveissueslips',
                beforeSend : function(){
                    //$('#issueslipreport').attr("disabled", true);
                },
                dataType: "json",
                data: $( "#issueslipsform" ).serialize(),
                success: function(data){
                     if(data.error=='No' && data.dataa==0)
                    {
                        $('#issueslipsform')[0].reset();
                        $('#raiesd').show();
                        $('.remove_field1').trigger('click') ;
                        $('.issuemsg').fadeIn().delay(3000).fadeOut();
                        //$('.issuemsgnew').fadeIn().delay(3000).fadeOut();
                        
                        //$('#issueslipsadd').hide();
                        //$('.cancel').trigger('click');
                        //window.location.href = url;
                        //$('#issueslipsaddForm').trigger('click');
                        $('#listissueslips').trigger('click') ;
                       // $('.raise-bill-form').hide();
                    }
                    else if(data.error=='No' && data.dataa==1 ){
                        $('#issueslipsform')[0].reset();
                        $('#raiesd').show();
                        $('.remove_field1').trigger('click') ;
                       // $('.issuemsg').fadeIn().delay(3000).fadeOut();
                        $('.issuemsgnew').fadeIn().delay(3000).fadeOut();
                        
                        //$('#issueslipsadd').hide();
                        //$('.cancel').trigger('click');
                        //window.location.href = url;
                        //$('#issueslipsaddForm').trigger('click');
                        $('#listissueslips').trigger('click') ;
                        
                    }
                    else{
                        $('#issueslipsform')[0].reset();
                        $('#raiesd').show();
                        $('.remove_field1').trigger('click') ;
                        $('.issuemsg').fadeIn().delay(3000).fadeOut();
                        //$('.issuemsgnew').fadeIn().delay(3000).fadeOut();
                        
                        //$('#issueslipsadd').hide();
                        //$('.cancel').trigger('click');
                        //window.location.href = url;
                        //$('#issueslipsaddForm').trigger('click');
                        $('#listissueslips').trigger('click') ;

                    }
    
                    //$('#issueslipreport').attr("disabled", false);
                }
            });
        }
        else{
            //alert("You have to enter all values for reporting");
            return  false;
        }
    });
    $(document).on('click','.deleteissueslipbutton',function(){
        var slipid=$(this).attr('data-v');
        var gp_id = $(this).attr('data-gp');
        var issqty = $('#isswtty'+gp_id).val(); 
        var tot = $('#tottissueqtty').val();

        var bal = tot - issqty;

        var r = confirm("Are you sure you want to delete this Issue slip ?");
        if (r == true) {
            $.ajax({
                type: 'POST',
                url: '../report/deleteissueslip',
                beforeSend : function(){
                    $('#deleteissueslipbutton'+slipid).attr("disabled", true);
                },
                dataType: "json",
                data: {slipid:slipid},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#issuesliprow'+gp_id).remove();
                        $('#listissueslips').trigger('click');
                        $('#totissueqty').html(bal.toFixed(2))

                    }
    
                    $('#deleteissueslipbutton'+slipid).attr("disabled", false);
                }
            });
        }
        else {
            return false;
        }
    });

    $(document).on('click','.tab-wrapper .issueslipview',function(){
        $(this).parents('.tab').addClass('edit-form-active');
        $(this).parents('.tab').removeClass('add-form-active');
        var viewid=$(this).attr('data-v');
        $.ajax({
            type: 'POST',
            url: '../report/issueslipview',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {orderid: viewid},
            success: function(data){
                if(data.error=='No')
                {
                    $('.preloader').hide();
                    $('#issueslipsedit').html(data.result);
                    $('#issueslipsedit').css("display", "block");
                    $("#isuzslp").css("display", "none");
                    $("#viw").css("display", "block");
                    
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });
    });
    $(document).on('click','#updateissueslip',function(){
        $.ajax({
            type: 'POST',
            url: '../report/issueslipupdate',
            beforeSend : function(){
                $('#updateissueslip').attr("disabled", true);
            },
            dataType: "json",
            data: $( "#issueslipapproval" ).serialize(),
            success: function(data){
                if(data.error=='No')
                {
                    $('.cancel').trigger('click');
                    $('#listissueslips').trigger('click') ;
                }
                else
                {
                    alert(data.errortext);
                }
                $('#updateissueslip').attr("disabled", false);
            }
        });
    });

});