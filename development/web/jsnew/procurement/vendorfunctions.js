
$(document).on( "click", "#master-vendortab", function(){  
      
        if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
            $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
            //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
        }
        if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
            $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
            $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
            
        }

        $('#listvendor').trigger('click');
        $(this).parents('.panel-group').removeClass('acco-one-active');
        $(this).parents('.panel-group').removeClass('acco-two-active');      
       
});

$(document).on('click','#listvendor',function(){  


    $('#vendoraddsection').slideUp('slow');// slide down the project listing div
    $('#vendorlistsection').slideDown('slow');// slide down the project listing div
    $('#listvendor').removeClass('btn-danger').addClass('btn-success');
    $('#addvendors').removeClass('btn-success').addClass('btn-danger');
    $.ajax({
        type: 'POST',
        url: '../vendors/search',
        beforeSend : function(){
            $('.preloader').show();
        },
        dataType: "json",
        data: {vendorname:$('#searchvendorname').val()},
        success: function(data){
            if(data.error=='No')
            {
                $('#vendoritems').html(data.result);

            }
            else
            {
                alert(data.errortext);
            }
            $('.preloader').hide();
        }
    });

});


$(document).on( "click", "#cancelsedit", function(){   

    $('.vendors-tab').removeClass('editVendorsForm-active');
    $('#vendoraddsection').hide();
    $('#vendortable').show();

   // $('#vendoritems').show();

});  

// list project click  \
// add project click
$('#addvendors').click(function(){
    $('#vendorlistsection').slideUp('slow');// slide down the project listing div
    $('#vendoraddsection').slideDown('slow');// slide down the project listing div
    $('#addvendors').removeClass('btn-danger').addClass('btn-success');
    $('#listvendor').removeClass('btn-success').addClass('btn-danger');

});
// add project click
// save project click
$('#savevendor').click(function(){

    var error=0;
    $('.error').hide();
    if($('#vendorname').val()=='')
    {
        $("#vendorname").next("span").html('Enter Vendor Name').show('slow');
        error=1;
    }
    if(error==0){
        $.ajax({
            type:'POST',
            url:'../vendors/create',
            beforeSend:function(){
                $('#savevendor').attr("disabled", true);
            },
            dataType:'json',
            data: $('#addvendorform').serialize(),
            success:function(data){
                if(data.error=='No')
                {
                    $('#addvendorform')[0].reset();
                    $('#listvendor').trigger('click');
                    $('#savevendor').attr("disabled", false);

                }
                else
                {
                    alert(data.errortext);
                }
            }
        });
    }
    else {
        return false;
    }

});

$(document).on('click','.vendormasterpage',function(){ 
    $('#listvendor').trigger('click');
});

$(document).on('click','#vendorsearch',function(){ 
    $('#listvendor').trigger('click');
});
$(document).on("keyup", "#searchvendorname", function(){
        $('#listvendor').trigger('click')
    });
// save project click
//project function ends here

$(document).on('change','#choosevvendortype',function(){
    var idval=$(this).val();
    $.ajax({
        type: 'POST',
        url: '../vendors/vendorgroups',

        dataType: "json",
        data: {vendortype:idval},
        success: function(data){
            if(data.error=='No')
            {
                $('#choosevvendorgroup').html(data.result);
            }
            else
            {
                alert(data.errortext);
            }

        }
    });
});

$(document).on('change','#vendorvtypelist',function(){
    var idval=$(this).val();
    $.ajax({
        type: 'POST',
        url: '../vendors/vendorgroups',

        dataType: "json",
        data: {vendortype:idval},
        success: function(data){
            if(data.error=='No')
            {
                $('#vendorvgrouplist').html(data.result);
            }
            else
            {
                alert(data.errortext);
            }

        }
    });
});

$(document).on('click','.vendorresources',function(){
        var vendorid=$(this).val();
        $('#searcselecttype').val('none');
        $('#searcselectvendor').val(vendorid);
        $('#Resources').trigger('click');
});
// edit resource button click function
$(document).on( "click", ".editvendorbutton", function(){
    $('.vendors-tab').addClass('editVendorsForm-active');
    var idval=$(this).val();

    var vendorname= $('#vendorname'+idval).val();

   // alert (vendorname);
    var vendoraddress= $('#vendoraddress'+idval).val();
    var vendorphone= $('#vendorphone'+idval).val();
    var vendorcity= $('#vendorcity'+idval).val();
    var vendoremail= $('#vendoremail'+idval).val();

    $('#savevendortypeval').val(idval);
     $('#vendornames').val(vendorname);
    $('#vendoraddresss').val(vendoraddress);
    $('#vendorphones').val(vendorphone);
    $('#vendorcityy').val(vendorcity);
    $('#vendoremails').val(vendoremail);
     $('#editvendorbutton'+idval).show();
    $('#vendortable').hide();

   
} );

// save edited resources function
$(document).on( "click", "#savevendorbutton", function(){    
   // var idval=$(this).val();
     var idval= $('#savevendortypeval').val();
   // var test=$('#vendoraddresss').val()
    //alert (idval);
    var error=0;
    $('.error').hide();
    if($('#vendornames').val()=='')
    {
        $('#vendornames').next("span").html('Enter Vendor Name').show('slow');
        error=1;
    }
    if(error==0){
        $.ajax({
            type: 'POST',
            url: '../vendors/update',
            beforeSend : function(){
                $('#savevendorbutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            // data: {vendorid:idval,vendortype:$('#edittype'+idval).val(),vendorgroup:$('#editvendorgroup'+idval).val(),vendoraccount:$('#editaccount'+idval).val(),name:$('#vendornames'+idval).val(),brand:$('#editbrandname'+idval).val(),address:$('#vendoraddresss'+idval).val(),city:$('#vendorcityy'+idval).val(),email:$('#vendoremails'+idval).val(),phone:$('#vendorphones'+idval).val()},
            data: {vendorid:idval,name:$('#vendornames').val(),address:$('#vendoraddresss').val(),city:$('#vendorcityy').val(),email:$('#vendoremails').val(),phone:$('#vendorphones').val()},
            success: function(data){   
                if(data.error=='No')
                {
                     
                    $('#editvendor')[0].reset();
                    $('.vendors-tab').removeClass('editVendorsForm-active');
                    $('#vendoraddsection').hide();
                    $('#vendortable').show();
                    //$('#vendoritems').show();
                    $('#listvendor').trigger('click');

                    $("select#searcselectvendor option[value='"+data.Id+"']").remove(); 
                    $('#searcselectvendor').append($('<option>', {
                        value: data.Id,
                        text: data.Name
                    }));

                }
                else
                {
                    alert(data.errortext);
                }

                $('#savevendorbutton'+data.Id).attr("disabled", false);
            }
        });
    }

} );


$(document).on( "click", ".deletevendorbutton", function(){
    var idval=$(this).val();
    var r = confirm("Are you sure you want to delete this Vendor?");
    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../vendors/deletevendor',
            beforeSend : function(){
                $('#deletevendorbutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {vendor:idval},
            success: function(data){
                if(data.error=='No')
                {
                    $('#vendorrow'+data.Id).remove();
                    $("select#searcselectvendor option[value='"+data.Id+"']").remove();
                     $('#listvendor').trigger('click')
                }
                else
                {
                    alert(data.errortext);
                }

                $('#deletevendorbutton'+data.Id).attr("disabled", false);
            }
        });

    } else {
        return false;
    }

});
function VendorNameExists(name)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../vendors/checkname',
        async:false,
        data: {name:name},
        success: function(data){
            retval=data;
        }
    });
    return retval;

}