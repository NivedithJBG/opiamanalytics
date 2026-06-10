$(function(){
$('.resourcesearch').click(function(){
        var resorcetypeid=$(this).val();
        //console.log(resorcetypeid);
        $.ajax({
            type: 'POST',
            url: '../ProjectSetup/resourcesearchbytyid',
            dataType: "json",
            data: {resourcetypeid:resorcetypeid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#resourceitems').html(data.result);
                    $('#restypeid').val(resorcetypeid);
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
	$('#searchresource').click(function(){
        var resorcetypeid=$('#restypeid').val();
        var name=$('#resourcename').val();
        //console.log(resorcetypeid);
        $.ajax({
            type: 'POST',
            url: '../ProjectSetup/resourcesearchbytyid',
            dataType: "json",
            data: {resourcetypeid:resorcetypeid,name:name,resourcegroup:$('#resourcegroupselection').val()},
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
            url:'../projects/addactivityresource',
            beforeSend:function(){
                $('#addresourcebutton'+resid).attr("disabled",true);
                $('.preloaderitems').show();
            },
            dataType:"json",
            data:{EstActivity_Id:$('#EstActivity_Id').val(),resid:resid,rate:price,srate:sprice,quantity:quantity},
            success:function(data){
                $('#addedresources').html(data.result);
                $('#addresourcebutton'+resid).attr("disabled",false);
                $('#resourcequantity'+resid).val('');
                $('.preloaderitems').hide();
                $('#resourcerow'+resid).remove();
                $('#estactresourceratetotal').val(data.price);
                //$('#amount').val(data.amount);
            }
        });
    }
});
$(document).on( "click", ".removeresourceitem", function(){
    var resid=$(this).val();
    $.ajax({
        type:'POST',
        url:'../projects/deleteactivityresource',
        beforeSend:function(){
            $('#removeresourceitem'+resid).attr("disabled",true);
            $('.preloaderitems').show();
        },
        dataType:"json",
        data:{resid:resid},
        success:function(data){
            $('#tempresrow'+resid).remove();
            //$('#addedresources').html(data.result);
            $('#estactresourceratetotal').val(data.price);
            //$('#amount').val(data.amount);
            $('#removeresourceitem'+resid).attr("disabled",false);
            $('.preloaderitems').hide();
        }
    });
});
$(document).on('blur',".resourceqty",function(){
    var id=$(this).attr('data-id');
    var quantity=$(this).val() * 1;
    var rate=$('#rate'+id).val() * 1;
    var amount=rate * quantity;
    $('#amount'+id).html(amount.toFixed(2));
    $('#resourceamount'+id).val(amount);
    var totalamount=0;
    $('.resourceamount').each(function(){
        totalamount=totalamount + $(this).val()*1;
    })
    $('#estactresourceratetotal').val(totalamount);
});
$(document).on('blur',".resourcerate",function(){
    var id=$(this).attr('data-id');
    var rate=$(this).val() * 1;
    var quantity=$('#quantity'+id).val() * 1;
    var amount=rate * quantity;
    $('#amount'+id).html(amount.toFixed(2));
    $('#resourceamount'+id).val(amount);
    var totalamount=0;
    $('.resourceamount').each(function(){
        totalamount=totalamount + $(this).val()*1;
    })
    $('#estactresourceratetotal').val(totalamount);
});