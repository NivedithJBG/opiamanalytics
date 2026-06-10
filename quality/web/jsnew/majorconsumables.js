$(function(){
    var type = window.location.hash.substr(1);
    if(type=='Majorconsumables')
    {
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown();
        $('#Majorconsumables').addClass('active').next('.acc_container').slideDown();
        //$('#productaddsection').slideUp('slow');// slide down the project listing div
        $('#consumableslistsection').slideDown('slow');// slide down the project listing div
        $('#listconsumables').removeClass('btn-danger').addClass('btn-success');
        $('#addinvestments').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../majorConsumables/search',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {overheadname:$('#searchconsumablesname').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#consumablesitems').html(data.result);
                    $('#consumablestable').show();
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
$(document).on( "click", "#Majorconsumables", function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#listconsumables').trigger('click') ;
});
$(function(){
    $('#listconsumables').click(function(){
        $('#investmentslistsection').slideDown('slow');// slide down the project listing div
        $('#listconsumables').removeClass('btn-danger').addClass('btn-success');
        $('#addinvestments').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../majorConsumables/search',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {investmentname:$('#searchinvestmentname').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#consumablesitems').html(data.result);
                    $('#consumablestable').show();
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });
    });
    $('#consumablessearch').click(function(){
        $('#listconsumables').trigger('click');
    });
    $('#resourcesearchmajor').click(function(){
        var toshow=$('#selecttype').val();
        var searchval=$('#searchname').val();
        $.ajax({
            type: 'POST',
            url: '../majorConsumables/resourcesearch',
            beforeSend : function(){
                $('#resourcesearchmajor').attr("disabled", true);
                $('.preloader').show();
            },
            dataType: "json",
            data: {resourcetype:toshow,resourcename:searchval},
            success: function(data){
                if(data.error=='No')
                {
                    $('#resourceitems').html(data.result);
                    $('#resourcetable').show();

                }
                else
                {
                    alert(data.errortext);
                }

                $('#resourcesearchmajor').attr("disabled", false);
                $('.preloader').hide();
            }
        });
    });
});
$(document).on('click','#saveoverhead',function(){
    var error=0;
    $('.error').hide();
    if($('#Overhead_Name').val()=='')
    {
        $('#Overhead_Name').next("span").html('Enter Name').show('slow');
        error=1;
    }
    if(OverheadName($('#Overhead_Name').val())=='Yes')
    {
        $('#Overhead_Name').next("span").html('Overhead Already Exists').show('slow');
        error=1;
    }
    if($('#Overhead_Unit').val()=='')
    {
        $('#Overhead_Unit').next("span").html('Enter Unit').show('slow');
        error=1;
    }
    if(error==1)
    {
        return false;
    }
    else
    {
        return true;
    }
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
            url:'../majorConsumables/addresourcetemp',
            beforeSend:function(){
                $('#addresourcebutton'+resid).attr("disabled",true);
                $('.preloaderitems').show();
            },
            dataType:"json",
            data:{Consumables_Id:$('#Consumables_Id').val(),resid:resid,rate:price,srate:sprice,quantity:quantity},
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
    $('#overheadratetotal').val(totalrate.toFixed(2));
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
    $('#overheadratetotal').val(totalrate.toFixed(2));
});
$(document).on( "click", ".removeresourceitem", function(){
    var resid=$(this).val();
    $.ajax({
        type:'POST',
        url:'../majorConsumables/deleteresourcetemp',
        beforeSend:function(){
            $('#removeresourceitem'+resid).attr("disabled",true);
            $('.preloaderitems').show();
        },
        dataType:"json",
        data:{resid:resid},
        success:function(data){
            $('#addedresources').html(data.result);
            $('#overheadratetotal').val(data.price);
            $('#amount').val(data.amount);
            $('#removeresourceitem'+resid).attr("disabled",false);
            $('.preloaderitems').hide();
        }
    });
});
$(document).on('click','.deletemajorconsumables',function(){
    var id=$(this).val();
    var r = confirm("Are you sure you want to delete this MajorCOnsumables ?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../majorConsumables/delete/'+id,
            async:false,
            dataType:"json",
            success: function(data){
                if(data.error=='No')
                {
                    $('#listconsumables').trigger('click');
                }
            }
        });
    }
});
function OverheadName(name)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../majorConsumables/checkname',
        async:false,
        data: {name:name},
        success: function(data){
            retval=data;
        }
    });
    return retval;

}