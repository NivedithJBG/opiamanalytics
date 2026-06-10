$(document).on( "click", ".prodreport", function(){
    //$('.acc_container').slideUp();
    $('#rproject').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
    //$(this).toggleClass('active').next().slideDown();
    $('#prodreport').addClass('active').next('.acc_container').slideDown();
    var id= $(this).val();
    $('#projid').val(id);
    $('#projectname').html(getProjectname(id));
    $('#prodreport').trigger('click') ;
});
$(function(){
    $('#prodreport').click(function(){
    if($('#projid').val()!=''){
        $('#prodresources').hide();
        $('#productsection').show('slow');
        $.ajax({
            type: 'POST',
            url: '../itemofworks/ProjProducts',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {projid:$('#projid').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#product').show();
                    $('#productitems').html(data.result);

                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });
    }
});
    $(document).on('click','.reportprod',function(){
        $('#productsection').hide();
        $('#prodresources').show();
        var prodid=$(this).val();
        var projid=$('#projid').val();
        var prodname=$('#prodname'+ prodid).val();
        var produnit=$('#produnit'+ prodid).val();
        var prodqty=$('#prodqty'+ prodid).val();
        $.ajax({
            type: 'POST',
            url: '../projects/Getprodform',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {projid:projid,prodid:prodid,name:prodname,unit:produnit,quantity:prodqty},
            success: function(data){
                if(data.error=='No')
                {
                    $('#prodetails').html(data.value);
                    $('#projproddetails').show();
                    $('#productres').html(data.result);
                    $('#projproductres').show();
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });
    });
    $(document).on('click','#saveprodreport',function(){
        var error=0;
        $('.error').hide();
        if($('#usedqty').val()=='')
        {
            $("#usedqty").next("span").html('Enter Quantity').show('slow');
            error=1;
        }
        $('.resquantity').each(function(){
            if($(this).val()=='')
            {
                error=1;
            }
        });

        if(error==0)
        {
            $.ajax({
                type: 'POST',
                url: '../projects/Productreport',
                beforeSend : function(){
                    $('#saveprodreport').attr("disabled", true);

                },
                dataType: "json",
                data: $( "#productreport" ).serialize(),
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#prodresources').hide();
                        $('#productsection').show();
                        $('#prodreport').trigger('click') ;
                    }
                    else
                    {
                        alert(data.errortext);
                    }
                    $('#saveprodreport').attr("disabled", false);
                }
            });
        }
        else
        {
            alert("You have to enter values");
            return  false;
        }
        //return false;

    });
    $(document).on('click','#saveasdraft',function(){
        var error=0;
        $('.error').hide();
        if($('#usedqty').val()=='')
        {
            $("#usedqty").next("span").html('Enter Quantity').show('slow');
            error=1;
        }
        $('.resquantity').each(function(){
            if($(this).val()=='')
            {
                error=1;
            }
        });

        if(error==0)
        {
            $.ajax({
                type: 'POST',
                url: '../projects/Productdraft',
                beforeSend : function(){
                    $('#saveasdraft').attr("disabled", true);

                },
                dataType: "json",
                data: $( "#productreport" ).serialize(),
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#prodresources').hide();
                        $('#productsection').show();
                        $('#prodreport').trigger('click') ;
                    }
                    else
                    {
                        alert(data.errortext);
                    }
                    $('#saveasdraft').attr("disabled", false);
                }
            });
        }
        else
        {
            alert("You have to enter values");
            return  false;
        }
        //return false;

    });
    $(document).on('click','#cancelprodreport',function(){
        $('#prodresources').hide();
        $('#productsection').show();
    });
});
function getProjectname(id)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../projects/Getname',
        async:false,
        data: {id:id},
        success: function(data){
            retval=data;
        }
    });
    return retval;
}
