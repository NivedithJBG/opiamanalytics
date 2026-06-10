/**
 * Created by SolmindsDelli5 on 19-09-2019.
 */
$(function(){
    $('.resourcesearch').click(function(){
        var resorcetype=$(this).val();
        if(resorcetype!=''){
            $('#selectresourceid').val(resorcetype);
        }
        var name=$('#resourcename').val();
        $.ajax({
            type: 'POST',
            url: '../ProjectSetup/resourcesearchbytyid',
            dataType: "json",
            data: {resourcetypeid:$('#selectresourceid').val(),name:name,resourcegroup:$('#resourcegroupselection').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#resourceitems').html(data.result);
                    $('#resourcegroupselection').html(data.group);
                    $('#resourcetable').show();

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
$(document).on( "click", ".addresource", function(){
    var resid=$(this).val();
    var error=0;

    $('.error').hide();
    if(!$.isNumeric($('#resourcequantity'+resid).val()))
    {
        $('#resourcequantity'+resid).next("span").html('Enter Valid Quantity').show('slow');
        error=1;
    }
    if(error==0)
    {
        var price=$('#resourceprice'+resid).val();
        var sprice=$('#specificrate'+resid).val();
        var quantity=$('#resourcequantity'+resid).val();
        $.ajax({
            type:'POST',
            url:'../investments/addresourcetemp',
            beforeSend:function(){
                $('#addresourcebutton'+resid).attr("disabled",true);
                $('.preloaderitems').show();
            },
            dataType:"json",
            data:{investment_id:$('#InvestmentId').val(),resid:resid,rate:price,srate:sprice,quantity:quantity},
            success:function(data){
                $('#addedresources').html(data.result);
                $('#addresourcebutton'+resid).attr("disabled",false);
                $('#resourcequantity'+resid).val('');
                $('.preloaderitems').hide();
                $('#resourcerow'+resid).remove();
                $('#investmentratetotal').val(data.price);
                $('#amount').val(data.amount);
            }
        });
    }
});

$(document).on( "blur",".resourceqty", function(){
    var itemid=$(this).attr('data-id');
    var error=0;
    $('.error').hide();
    if($('#quantity'+itemid).val()==0)
    {
        $('#quantity'+itemid).next("span").html('Enter Valid Quantity').show('slow');
        error=1;
    }
    var quantity=$(this).val()*1;
    var rate=$('#rate'+itemid).val()*1;
    var amount=rate*quantity;
    $('#amount'+itemid).text(amount.toFixed(2));
    var totalrate=0;
    $('.resource-amount').each(function(){
        totalrate=totalrate+($(this).text()*1)
    });
    $('#investmentratetotal').val(totalrate.toFixed(2));
});
$(document).on( "blur",".resourcerate", function(){
    var itemid=$(this).attr('data-id');
    var quantity=$('#quantity'+itemid).val()*1
    var rate=$(this).val()*1;
    var amount=rate*quantity;
    $('#amount'+itemid).text(amount.toFixed(2))
    var totalrate=0;
    $('.resource-amount').each(function(){
        totalrate=totalrate+($(this).text()*1)
    });
    $('#investmentratetotal').val(totalrate.toFixed(2));
});

$(document).on( "click", ".removeresourceitem", function(){
    var resid=$(this).val();
    $.ajax({
        type:'POST',
        url:'../investments/deleteresourcetemp',
        beforeSend:function(){
            $('#removeresourceitem'+resid).attr("disabled",true);
            $('.preloaderitems').show();
        },
        dataType:"json",
        data:{resid:resid},
        success:function(data){
            $('#addedresources').html(data.result);
            $('#investmentratetotal').val(data.price);
            $('#amount').val(data.amount);
            $('#removeresourceitem'+resid).attr("disabled",false);
            $('.preloaderitems').hide();
        }
    });
});
