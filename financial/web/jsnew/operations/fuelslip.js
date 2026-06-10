$(document).on( "click", "#fuelslips", function(){
    $('#fuelissueslipsaddForm').trigger('click');
    $('.firtslip').hide();
    $('.scndslips').show();
    $('.raise-muster-roll').show();

});
$(document).on( "click", "#fuelsli", function(){
    $('#fuelissueslipsaddForm').trigger('click');
    $('.firtslip').hide();
    $('.scndslips').show();
    $('.raise-muster-roll').show();
    $('#fuelissueslipsitems').hide();
    $('.sechist').hide();
});

$(function() {

    $(document).on('click','#fuelissueslipsaddForm',function(){
        $.ajax({
            type: 'POST',
            url: '../report/fuelissueslipform',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            success: function(data){
                if(data.error=='No')
                {
                    $('#fuelissueslipsadd').html(data.result);
                 
                    
                    $('.nav-tabs').show();
                    $('.fissue-material-slips-list-wrpr').hide();
                    $('#fuelissueslipsitems').hide();
                 
                   // $('#fuelissueslipsitemsz').hide();
                    $('.secwrapp').hide();
                }
                $('.preloader').hide();
            }
        });
    });




    $(document).on('click','#fuelissueslipreport',function(){ 
        var error=0;
        $('.error').hide();

        $('.fissueslipdate').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='')
            {
                $("#fissueslipdate"+id).next("span").html('Select Date').show('slow');
                error=1;
            }
    
        });
        $('.fueltype').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='none')
            {
                $("#fueltype"+id).next("span").html('Select Fueltype').show('slow');
                error=1;
            }
    
        });
        $('.FuelIssueslipResource').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='none')
            {
                $("#fIssueslipResource"+id).next("span").html('Select Equipment').show('slow');
                error=1;
            }
    
        });
        $('.fIssuedQuantity').each(function(){
            var id=$(this).attr('data-id');
            if($(this).val()=='')
            {
                $("#fIssuedQuantity"+id).next("span").html('Enter Quantity').show('slow');
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
                data: $("#fuelslipsform").serialize(),
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#fuelslipsform')[0].reset();
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



    // $(document).on('click','.newisuz',function(){  
    //     var error=0;
    //     $('.error').hide();

    //     $('.fissueslipdate').each(function(){
    //         var id=$(this).attr('data-id');
    //         if($(this).val()=='')
    //         {
    //             $("#fissueslipdate"+id).next("span").html('Select Date').show('slow');
    //             error=1;
    //         }
    
    //     });
    //     $('.fueltype').each(function(){
    //         var id=$(this).attr('data-id');
    //         if($(this).val()=='none')
    //         {
    //             $("#fueltype"+id).next("span").html('Select Fueltype').show('slow');
    //             error=1;
    //         }
    
    //     });
    //     $('.FuelIssueslipResource').each(function(){
    //         var id=$(this).attr('data-id');
    //         if($(this).val()=='none')
    //         {
    //             $("#fIssueslipResource"+id).next("span").html('Select Equipment').show('slow');
    //             error=1;
    //         }
    
    //     });
    //     $('.fIssuedQuantity').each(function(){
    //         var id=$(this).attr('data-id');
    //         if($(this).val()=='')
    //         {
    //             $("#fIssuedQuantity"+id).next("span").html('Enter Quantity').show('slow');
    //             error=1;
    //         }
    
    //     });
        
    
    //     if(error==0){
    //         $.ajax({
    //             type: 'POST',
    //             url: '../report/savefuelissue',
    //             beforeSend : function(){
    //                 //$('#issueslipreport').attr("disabled", true);
    //             },
    //             dataType: "json",
    //             data: $("#fuelslipsform").serialize(),
    //             success: function(data){
    //                 if(data.error=='No')
    //                 {
    //                     $('#fuelslipsform')[0].reset();
    //                     $('.fuel_remove_field1').trigger('click') ;
    //                     $('.fuelissuemsg').fadeIn().delay(3000).fadeOut();
    //                 }
    //             }
    //         });
    //     }
    //     else{
    //         //alert("You have to enter all values for reporting");
    //         return  false;
    //     }
    // });




    

    /*$(document).on( "change",".FuelIssueslipResource", function(){
        var id=$(this).attr('data-id');
        var resource=$(this).val();
        var projectid=$('#projissuesliplist').val();
        $.ajax({
            type: 'POST',
            url: '../report/resunit',
            dataType: "json",
            data: {resid:resource,projectid:projectid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#IssueslipUnit'+id).val(data.result);
                }
            }
        });
    });

    $(document).on( "change",".editFuelIssueslipResource", function(){
        var id=$(this).attr('data-id');
        var resource=$(this).val();
        var projectid=$('#projissuesliplist').val();
        $.ajax({
            type: 'POST',
            url: '../report/resunit',
            dataType: "json",
            data: {resid:resource,projectid:projectid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#editIssueslipUnit').val(data.result);
                }
            }
        });
    });*/
    $(document).on('click','#cancelfuelissueslip',function(){
        $('.frstwrapp').hide();
        $('.fissue-material-slips-list-wrpr').show();
        $('#fuelissueslipsitems').show();

    });
    $(document).on('click','#updatefuelissueslip',function(){

        var error=0;
        $('.error').hide();

        var date = $('#editissueslipdate').val();
        var fueltype = $('#editfueltype').val();
        var resource = $('#editFuelIssueslipResource').val();
        var qty = $('#editissueslipquantity').val();

        if(date=='')
        {
            $("#editissueslipdate").next("span").html('Select Date').show('slow');
            error=1;
        }

        if(fueltype=='none')
        {
            $("#editfueltype").next("span").html('Select Fueltype').show('slow');
            error=1;
        }

        if(resource=='none')
        {
            $("#editFuelIssueslipResource").next("span").html('Select Equipment').show('slow');
            error=1;
        }

        if(qty=='')
        {
            $("#editissueslipquantity").next("span").html('Enter quantity').show('slow');
            error=1;
        }

        if(error==0){

            $.ajax({
                type: 'POST',
                url: '../report/fuelissueslipupdate',
                beforeSend : function(){
                    $('#updatefuelissueslip').attr("disabled", true);
                },
                dataType: "json",
                data: $( "#editfuelslipsform" ).serialize(),
                success: function(data){
                    if(data.error=='No')
                    {
                        $('.editfuelslipsform').hide();
                        $(".edtfrm").css("display", "none");
                        $('.fuelhistory').trigger('click');
                        //$('#listissueslips').trigger('click') ;
                        
                    }
                    else
                    {
                        alert(data.errortext);
                    }
                    $('#updatefuelissueslip').attr("disabled", false);
                }
            });
        }

    });

    $(document).on('click','.fuelhistory',function(){
        $.ajax({
            type: 'POST',
            url: '../report/fuelissueslipsearch',
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
        $('.fuelhistory').trigger('click');
    });

    $(document).on('click','.fueldeleteissueslipbutton',function(){

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

                        $('.fuelhistory').trigger('click');

                    }
    
                    $('#fueldeleteissueslipbutton'+slipid).attr("disabled", false);
                }
            });
        }
        else {
            return false;
        }
    });

    $(document).on('click','.tab-wrapper .fuelissueslipview',function(){
        $(this).parents('.tab').addClass('edit-form-active');
        $(this).parents('.tab').removeClass('add-form-active');
        var viewid=$(this).attr('data-v');
        $.ajax({
            type: 'POST',
            url: '../report/fuelviewissueslip',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {orderid: viewid},
            success: function(data){
                if(data.error=='No')
                {
                    $('.preloader').hide();
                    $('#fuelissueslipsedit').html(data.result);
                    $('#fuelissueslipseditz').html(data.result);
                    $('#fuelissueslipsitemsz').hide();
                    $('#fuelissueslipsitems').hide();
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