/**
 * Created by SolmindsDelli5 on 09-05-2018.
 */
$(document).on( "click", ".viewiowschestimate", function(){

    $('#iowschedule').removeClass('active').next().slideUp();

    $('#actualestimate').addClass('active').next('.acc_container').slideDown();

    var id= $(this).val();
    var projid= $(this).attr('data-id');

    $('#actualestimateProject_Id').val(projid);
    $('#actualestimatewbsid').val(id);

    $('#listactualitems').trigger('click') ;
});
$(function(){
    $('#listactualitems').click(function(){

        $.ajax({
            type: 'POST',
            url: '../projects/Listactualestimateitems',
            beforeSend : function(){
                $('.preloaderitems').show();
            },
            dataType: "json",
            data: {wbsid:$('#actualestimatewbsid').val(),projectid:$('#actualestimateProject_Id').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#actualactivities').html(data.result);
                    $('#actualestimatetable').show();
                    $("span#actualwbsname h4").html(data.wbsname);
                }

                $('.preloaderitems').hide();
            }
        });
    });
});
$(document).on( "click",".iowschremoveiowitem", function(){
    var actid=$(this).val();

    var r = confirm("Are you sure you want to delete this Activity ?");

    if (r == true) {
        $.ajax({

            type:'POST',

            url:'../projects/Removeiowschitem',

            beforeSend:function(){

                $('#iowschremoveiowitem'+actid).attr("disabled",true);

            },

            dataType:"json",

            data:{actid:actid},

            success:function(data){

                if(data.error=='No')

                {

                    $('#actualactrow'+actid).remove();

                }

                $('#iowschremoveiowitem'+actid).attr("disabled",false);

            }

        });
    }
});
$(document).on( "blur",".iowquantity", function(){
    //var itemid=$(this).attr('data-id');
    var datatype=$(this).attr('data-type');
    datatype = datatype.replace(/ +/g, "");
    var dataid=$(this).attr('data-id');
    var error=0;
    $('.error').hide();
    if($('#'+datatype+'iowquantity'+dataid).val()==0)
    {
        $('#'+datatype+'iowquantity'+dataid).next("span").html('Enter Valid Quantity').show('slow');
        error=1;
    }
    var quantity=($(this).val()*1);
    var specrate=($('#'+datatype+'specrate'+dataid).val()*1);
    var amount=specrate*quantity;
    $('#'+datatype+'amount'+dataid).html(amount.toFixed(2));
    $('#amount'+dataid).val(amount);
    var totalrate=0;
    $('.'+datatype+'amount').each(function(){
        totalrate=totalrate+($(this).val()*1);
    });

    $('#totaliow'+datatype+'cost').html(totalrate.toFixed(2));
});
$(document).on( "click","#saveactualestimate", function(){
    $.ajax({
        type: 'POST',
        url: '../projects/Iowestimate',
        beforeSend : function(){
            $('#saveactualestimate').attr("disabled", true);
        },
        dataType: "json",
        data: $('#actualestimateform').serialize(),
        success: function(data){
            if(data.error=='No')
            {
                alert('Saved');
                $('#listactualitems').trigger('click') ;
            }

            $('#saveactualestimate').attr("disabled", false);
        }
    });
});
