/**
 * Created by SolmindsDelli5 on 18-08-2017.
 * 
 * 
 */
 
 /* $(document).on( "click", "#acco-vendors", function(){
    
    $('#choosevendorsform')[0].reset();
     $("#addedresources tr").detach();
 }); */
$(document).on( "click", ".choosevendor", function(){
    

    /*$('#collapseindents').removeClass('in');

    $('.acco-billofresources').removeClass('active').next().slideUp();

    $('.acco-vendors').addClass('active');

    $('#acco-vendors').show();

    $('#collapsevendors').addClass('in');*/

    //$('.acco-vendors').addClass('active').next('.acc_container123').slideDown();
    
    //$('.acco-vendors input[type=radio]').trigger('click');

    $('#collapseindents').removeClass('in');

    $('.acco-two').removeClass('active');

    $('.acco-three').addClass('active');

    $('#collapsevendors').addClass('in');

    $("#collapseindents").attr("aria-expanded","false");

    $("#collapsevendors").attr("aria-expanded","true");

    $('#collapsevendors').css('height','');

    $('.panel-group').removeClass('acco-billofres-active');
    $('.panel-group').addClass('acco-vendors-active');
    $('.panel-group').removeClass('acco-three-active');
    $('.panel-group').removeClass('acco-confirmorders-active');
    $('.panel-group').removeClass('acco-despatchorders-active');

    $('#acco-vendors').trigger('click');

    var jobid= $(this).val();
    var resource=$('#resource'+jobid).val();
    var resourceid=$('#resourceid'+jobid).val();
    var resourceunit=$('#billresourceunit'+jobid).val();

    $.ajax({
        type: 'POST',
        url: '../procurement/vendors',
        beforeSend : function(){
            $('.preloader').show();
        },
        dataType: "json",
        data: {jobid:jobid,resource:resource,resourceid:resourceid,resourceunit:resourceunit},
        success: function(data){
            if(data.error=='No')
            {
                //$('#acco-vendors').show();
                $('#addedresources').html(data.result);
                $('#choosevendorstable').show();
                 $('#newdata').html(data.datarowsnew);
                 $('#newdatarestype').html(data.datarowsrestype);
                 $('#newdataactivity').html(data.datarowsactivity);
                
                if (data.restype=='33'){
                    $('#Numworkersh').show();
                    $('#Numdaysh').show();
                    $('#Otrateh').show();
                    $('#Quantityh').hide();
                }
                else {
                    $('#Quantityh').show();
                    $('#Numworkersh').hide();
                    $('#Numdaysh').hide();
                    $('#Otrateh').hide();
                }
            }
            $('.preloader').hide();
        }
    });
});

$(document).on( "click", "#acco-vendors", function(){

    $('.panel-group').removeClass('acco-billofres-active');
    $('.panel-group').addClass('acco-vendors-active');
    $('.panel-group').removeClass('acco-three-active');
    $('.panel-group').removeClass('acco-confirmorders-active');
    $('.panel-group').removeClass('acco-despatchorders-active');

});

$(document).on('change','.vendorrate',function(){
    var rate=$(this).val();
    var resid=$(this).attr('data-id');
    $.ajax({
        type: 'POST',
        url: '../procurement/Updaterate',
        dataType:"json",
        async: false,
        data:{resid:resid,rate:rate},
        success: function(data){

        }
    });
});

$(document).on( "click", ".emailvendor", function(){
    var email=$(this).attr('data-email');
    //alert(email)
   // $('#vendoremail')[0].reset();
    $('#succesinfo').hide();
    $('#errorinfo').hide();
    $('#vendoremailid').val(email);
});
$(document).on( "click", ".phonevendor", function(){
    var phone=$(this).attr('data-id');
    //alert(email)
    $.ajax({
        type: 'POST',
        url: '../procurement/vendorphone',
        dataType:"json",
        async: false,
        data:{phone:phone},
        success: function(data){
            if(data.error=='No'){
               $('#vendcontno').html(data.phone); 
            }
            
        }
    });
});
$(document).on( "click", "#emailvendor", function(){
    var email=$('#vendoremailid').val();
    var subject=$('#subject').val();
    var body=$('#body').val();

    var error=0;
    $('.error').hide();

    if($('#vendoremailid').val()=='')
    {
        $('#vendoremailid').next("span").html('Enter Email address').show('slow');
        error=1;
    }
    if($('#subject').val()=='')
    {
        $('#subject').next("span").html('Enter Subject').show('slow');
        error=1;
    }
    if($('#body').val()=='')
    {
        $('#body').next("span").html('Enter Body').show('slow');
        error=1;
    }
    if (error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../procurement/sendvendoremail',
            beforeSend : function(){
                $('.mailloader').show();
            },
            dataType: "json",
            data: {email:email,subject:subject,body:body},
            success: function(data){
                if(data.error=='No')
                {
                    $('.mailloader').hide();
                    $('#succesinfo').show();
                    $('#succesinfo').html('<span style="text-align: center">'+data.errortext+'</span>');
                    $('#errorinfo').hide();

                  
                    setTimeout(function() {
                        $('#emailvendorModel').hide();
                    }, 2100);

                    $(".modal-backdrop.fade.in").css("display", "none");

                  

                }
                else {
                    $('.mailloader').hide();
                    $('#errorinfo').show();
                    $('#errorinfo').html('<span style="text-align: center">'+data.errortext+'</span>');
                    $('#succesinfo').hide();
                }
                // setTimeout(function(){
                //     $('#emailvendorModel').modal('toggle');
                // }, 5000);

            }
        });
    }


});

$(document).on('click','.editvendorbutton',function(){
    var idval=$(this).attr('data-v');

    $('#pricee'+idval).hide();
    $('#quantts'+idval).hide();
    $('#nowrks'+idval).hide();
    $('#ottrat'+idval).hide();

    $('#editpriceres'+idval).show();
    $('#editqtttys'+idval).show();
    $('#editnumworkers'+idval).show();
    $('#editotrate'+idval).show();
    $('#editzvendorbutton'+idval).hide();
    $('#savevendorssbutton'+idval).show();

    });


$(document).on('click','.savevendorbutton',function(){
    var idval=$(this).attr('data-v');
    var rate=$('#editpriceres'+idval).val();

    var error=0;
    $('.error').hide();

    if(error==0)
        {
            $.ajax({
                type: 'POST',
                url: '../procurement/updatevendors',
                beforeSend : function(){
                    $('#savevendorssbutton'+idval).attr("disabled", true);
                },
                dataType: "json",
                data: {id:idval,rate:rate,jobcardid:$('#jobcard'+idval).val(),resourceid:$('#vresource'+idval).val(),price:$('#editpriceres'+idval).val(),quantity:$('#editqtttys'+idval).val(),nuwrkrs:$('#editnumworkers'+idval).val(),Otrate:$('#editotrate'+idval).val()},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#pricee'+data.Id).show();
                        $('#quantts'+data.Id).show();
                        $('#nowrks'+idval).show();
                        $('#ottrat'+idval).show();
                        $('#editpriceres'+idval).hide();
                        $('#editqtttys'+idval).hide();
                        $('#editnumworkers'+idval).hide();
                        $('#editotrate'+idval).hide();
                        $('#editzvendorbutton'+data.Id).show();
                        $('#savevendorssbutton'+data.Id).hide();
                        $('#pricee'+data.Id).text(data.Price);
                        $('#quantts'+data.Id).text(data.Quantity);
                        $('#nowrks'+data.Id).text(data.Wrkhrs);
                        $('#ottrat'+data.Id).text(data.otrate);
                        $('#editotrate'+idval).val(data.otrate);
                        $('#editnumworkers'+idval).val(data.Wrkhrs);
                        $('#editqtttys'+idval).val(data.Quantity);

                        $('#jobcardqty'+data.Id).val(data.Quantity);
                        $('#vendorqty'+data.Id).val(data.Quantity);
                        $('#vendorrate'+data.Id).val(data.Price);
    
                    }
                    else
                    {
                        alert(data.errortext);
                    }
    
                    $('#savevendorssbutton'+data.Id).attr("disabled", false);
                }
            });
        }
});
