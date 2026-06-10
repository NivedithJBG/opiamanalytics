$(document).on( "click", "#fuelslips", function(){
    $('#fuelissueslipsaddFormnew').trigger('click');
    $('.firtslip').hide();
    $('.scndslips').show();
    $('.raise-muster-roll').show();

});
$(document).on( "click", "#fuelslips", function(){
    $('#fuelissueslipsaddFormnew').trigger('click');
    $('.firtslip').hide();
    $('.scndslips').show();
    $('.raise-muster-roll').show();
    $('#fuelissueslipsitems').hide();
    $('.sechist').hide();
});

$(function() {

$(document).on('click','#fuelissueslipsaddFormnew',function(){
        $.ajax({
            type: 'POST',
            url: '../report/fuelissueslipformnew',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            success: function(data){
                if(data.error=='No')
                {
                    //$('#fuelissueslipsadd').html(data.result);
                    $('#fuelissueslipsaddz').html(data.result);
                    
                    $('.nav-tabs').show();
                    $('.fissue-material-slips-list-wrpr').hide();
                    //$('#fuelissueslipsitems').hide();
                    $("#fuelissueslipsitemsz").css("display", "none");
                    $(".edtfrm").css("display", "none");
                   // $('#fuelissueslipsitemsz').hide();
                    $('.secwrapp').hide();
                }
                $('.preloader').hide();
            }
        });
    });


    

     $(document).on('click','#fuelissueslipreportnew',function(){  
        var error=0;
        $('.error').hide();

        $('.fissueslipdatenew').each(function(){  
            var id=$(this).attr('data-id'); 
            
            if($(this).val()=='')
            {
                
                $("#fissueslipdatee"+id).next("span").html('Select Date').show('slow');
                error=1;
            }
    
        });

        $('.fueltypee').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='none')
            {

                $("#fueltypee"+id).next("span").html('Select Fueltype').show('slow');
                error=1;
            }
    
        });
        $('.FuelIssueslipResourcee').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='none')
            {
                $("#fIssueslipResourcee"+id).next("span").html('Select Equipment').show('slow');
                error=1;
            }
    
        });
        $('.fIssuedQuantityy').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='')
            {
                $("#fIssuedQuantityy"+id).next("span").html('Enter Quantity').show('slow');
                error=1;
            }
    
        });
        
    
        if(error==0){
            
            $.ajax({
                type: 'POST',
                url: '../report/savefuelissue',
                beforeSend : function(){
                    //$('#issueslipreport').attr("disabled", true);
                },
                dataType: "json",
                data: $("#fuelslipsformnew").serialize(),
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#fuelslipsformnew')[0].reset();
                        $('.fuel_remove_field1').trigger('click') ;
                        $('.fuelissuemsg').fadeIn().delay(3000).fadeOut();
                    }
                }
            });
        }
        else{
            //alert("You have to enter all values for reporting");
            return  false;
        }
    });



    
    $(document).on('click','#cancelfuelissueslip',function(){
        $('.frstwrapp').hide();
        $('.fissue-material-slips-list-wrpr').show();
        $('#fuelissueslipsitems').show();


    });

     $(document).on('click','.cancel',function(){

      $(".edtfrm").css("display", "none");
      $(".listzzful").css("display", "block");
      $('.fissue-material-slips-list-wrpr').hide();
      $(".raise-muster-roll").css("display", "block");
      


    });



    $(document).on('click','#updatefuelissueslipnew',function(){

        var error=0;
        $('.error').hide();

        var date = $('#editissueslipdatee').val();
        var fueltype = $('#editfueltypee').val();
        var resource = $('#editFuelIssueslipResourcee').val();
        var qty = $('#editissueslipquantityy').val();

        if(date=='')
        {
            $("#editissueslipdatee").next("span").html('Select Date').show('slow');
            error=1;
        }

        if(fueltype=='none')
        {
            $("#editfueltypee").next("span").html('Select Fueltype').show('slow');
            error=1;
        }

        if(resource=='none')
        {
            $("#editFuelIssueslipResourcee").next("span").html('Select Equipment').show('slow');
            error=1;
        }

        if(qty=='')
        {
            $("#editissueslipquantityy").next("span").html('Enter quantity').show('slow');
            error=1;
        }

        if(error==0){

            $.ajax({
                type: 'POST',
                url: '../report/fuelissueslipupdate',
                beforeSend : function(){
                    $('#updatefuelissueslipnew').attr("disabled", true);
                },
                dataType: "json",
                data: $( "#editfuelslipsformnew" ).serialize(),
                success: function(data){
                    if(data.error=='No')
                    {
                        $('.editfuelslipsformnew').hide();
                        $(".edtfrm").css("display", "none");
                        $('.fuelhistoryy').trigger('click');
                        //$('#listissueslips').trigger('click') ;
                        
                    }
                    else
                    {
                        alert(data.errortext);
                    }
                    $('#updatefuelissueslipnew').attr("disabled", false);
                }
            });
        }

    });

    $(document).on('click','.fuelhistoryy',function(){
        $.ajax({
            type: 'POST',
            url: '../report/fuelissueslipsearchnew',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {resourcename:$('#fuelissresval').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('.issue-fuel-slips-tab').removeClass("add-form-active");
                    $('.issue-fuel-slips-tab').removeClass("edit-form-active");
                    $('#fuelissueslipsitems').html(data.result);
                    $('#fuelissueslipsitemsz').html(data.result);
                    $('#fuelprodropname').html(data.pname);
                    $('.fissue-material-slips-list-wrpr').show();
                    $('.raise-muster-roll').hide();
                    $('#fuelissueslipsitems').show();
                    $('#fuelissueslipsitemsz').show();
                    $('.sechist').show();
                    $('.nav-tabs').hide();
                    $('.secwrapp').show();
                    $('#headlog').css('display','block');
                }
                $('.preloader').hide();
            }
        });
    });

    $(document).on('click','#fuellistissuesearch',function(){
        //$('#fuellistissueslips').trigger('click');
        $('.fuelhistoryy').trigger('click');
    });

    $(document).on('click','.fueldeleteissueslipbuttonnew',function(){

        var slipid=$(this).attr('data-v');
        var r = confirm("Are you sure you want to delete this Issue slip ?");
        if (r == true) {
            $.ajax({
                type: 'POST',
                url: '../report/fueldeleteissueslip',
                beforeSend : function(){
                    $('#fueldeleteissueslipbutton'+slipid).attr("disabled", true);
                },
                dataType: "json",
                data: {slipid:slipid},
                success: function(data){
                    if(data.error=='No')
                    {
                        /*$('#issuesliprow'+gp_id).remove();
                        $('#listissueslips').trigger('click');
                        $('#totissueqty').html(bal.toFixed(2));*/

                        $('.fuelhistoryy').trigger('click');

                    }
    
                    $('#fueldeleteissueslipbutton'+slipid).attr("disabled", false);
                }
            });
        }
        else {
            return false;
        }
    });

    $(document).on('click','.tab-wrapper .fuelissueslipvieww',function(){
        $(this).parents('.tab').addClass('edit-form-active');
        $(this).parents('.tab').removeClass('add-form-active');
        var viewid=$(this).attr('data-v');
        $.ajax({
            type: 'POST',
            url: '../report/fuelviewissueslipnew',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {orderid: viewid},
            success: function(data){
                if(data.error=='No')
                {
                    $('.preloader').hide();
                    //$('#fuelissueslipsedit').html(data.result);
                    $('#fuelissueslipseditz').html(data.result);
                    $('#fuelissueslipsitemsz').hide();
                    $(".edtfrm").css("display", "block");
                    
                    //$('#fuelissueslipsitems').hide();
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });
    });

});    