/**
 * Created by SolmindsDelli5 on 25-09-2017.
 */
$(document).on( "click", ".viewestimate", function(){

    $('#project').removeClass('active').next().slideUp();

    $('#estimateprojtab').addClass('active').next('.acc_container').slideDown();

    var id= $(this).val();

    $('#estimateProject_Id').val(id);

    $("span#projectname h4").html(getProjectname(id));

    $('#listestimateitems').trigger('click') ;
});

$(function(){
    $('#listestimateitems').click(function(){
        $.ajax({
            type: 'POST',
            url: '../projects/Listestimateitems',
            beforeSend : function(){
                $('.preloaderitems').show();
            },
            dataType: "json",
            data: {projectid:$('#estimateProject_Id').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#addedproducts').html(data.result);
                    $('#estimatetable').show();
                }

                $('.preloaderitems').hide();
            }
        });
    });
});

$(document).on( "blur",".quantity", function(){
    //var itemid=$(this).attr('data-id');
    var datatype=$(this).attr('data-type');
    datatype = datatype.replace(/ +/g, "");
    var dataid=$(this).attr('data-id');
    var error=0;
    $('.error').hide();
    if($('#'+datatype+'quantity'+dataid).val()==0)
    {
        $('#'+datatype+'quantity'+dataid).next("span").html('Enter Valid Quantity').show('slow');
        error=1;
    }
    var quantity=($(this).val()*1);
    var specrate=($('#'+datatype+'rate'+dataid).val()*1);
    var amount=specrate*quantity;
    $('#'+datatype+'amount'+dataid).html(amount.toFixed(2));
    $('#amount'+dataid).val(amount);
    var totalrate=0;
    $('.'+datatype+'amount').each(function(){
        //alert($(this).val()*1)
        totalrate=totalrate+($(this).val()*1);
    });

    $('#total'+datatype+'cost').html(totalrate.toFixed(2));
});

$(document).on( "click","#saveproduct", function(){
    $.ajax({
        type: 'POST',
        url: '../projects/Pricingestimate',
        beforeSend : function(){
            $('#saveproduct').attr("disabled", true);
        },
        dataType: "json",
        data: $('#pricingestimateform').serialize(),
        success: function(data){
            if(data.error=='No')
            {
                alert('Saved'); 
                location.reload();
                $('#listestimateitems').trigger('click') ;

            }

            $('#saveproduct').attr("disabled", false);
        }
    });
});

$(document).on( "click",".removeiowitem", function(){  
    var actid=$(this).val();

    var r = confirm("Are you sure you want to delete this Activity ?");

    if (r == true) {
        $.ajax({

            type:'POST',

            url:'../projects/Removeitem',

            beforeSend:function(){

                $('#removeiowitem'+actid).attr("disabled",true);

            },

            dataType:"json",

            data:{actid:actid},

            success:function(data){

                if(data.error=='No')

                {

                    $('#tempprodrow'+actid).remove();

                }
                else
                {
                    alert(data.result);
                }

                $('#removeiowitem'+actid).attr("disabled",false);

            }

        });
    }
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