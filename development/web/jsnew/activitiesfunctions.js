$(function(){
    var type = window.location.hash.substr(1);
    if(type=='Costactivities')
    {
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown();
        $('#Costactivities').addClass('active').next('.acc_container').slideDown();
        //$('#productaddsection').slideUp('slow');// slide down the project listing div
        $('#costactivitieslist').slideDown('slow');// slide down the project listing div
        $('#listcostactivities').removeClass('btn-danger').addClass('btn-success');
        $('#addcostactivities').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../activities/search',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {activitiesname:$('#searchcostactivitiesname').val(),groupid:$('#activitygrp').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#costactivitiesitems').html(data.result);
                    $('#costactivitiestable').show();
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
$(document).on( "click", "#Costactivities", function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#listcostactivities').trigger('click') ;
});
$(function(){
    $('#listcostactivities').click(function(){
        //$('#productaddsection').slideUp('slow');// slide down the project listing div
        $('#costactivitieslist').slideDown('slow');// slide down the project listing div
        $('#listcostactivities').removeClass('btn-danger').addClass('btn-success');
        $('#addcostactivities').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../activities/search',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {activitiesname:$('#searchcostactivitiesname').val(),groupid:$('#activitygrp').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#costactivitiesitems').html(data.result);
                    $('#costactivitiestable').show();
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });
    });
    $('#costactivitiessearch').click(function(){
        $('#listcostactivities').trigger('click');
    });
    $('#resourcesearch').click(function(){
        var toshow=$('#selecttype').val();
        var searchval=$('#searchname').val();
        $.ajax({
            type: 'POST',
            url: '../activities/resourcesearch',
            beforeSend : function(){
                $('#resourcesearch').attr("disabled", true);
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

                $('#resourcesearch').attr("disabled", false);
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
            url:'../activities/addresourcetemp',
            beforeSend:function(){
                $('#addresourcebutton'+resid).attr("disabled",true);
                $('.preloaderitems').show();
            },
            dataType:"json",
            data:{Activity_id:$('#Activity_id').val(),resid:resid,rate:price,srate:sprice,quantity:quantity},
            success:function(data){
                $('#addedresources').html(data.result);
                $('#addresourcebutton'+resid).attr("disabled",false);
                $('#resourcequantity'+resid).val('');
                $('.preloaderitems').hide();
                $('#resourcerow'+resid).remove();
                $('#Activity_Rate').val(data.price);
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
    $('#Activity_Rate').val(totalrate.toFixed(2));
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
    $('#Activity_Rate').val(totalrate.toFixed(2));
});
$(document).on('click','#updatecostactivities',function(){
    var error=0;
    $('.error').hide();
    if($('#Activity_Name').val()=='')
    {
        $('#Activity_Name').next("span").html('Enter Name').show('slow');
        error=1;
    }
    /*if(ProductName($('#Name').val())=='Yes')
     {
     $('#Name').next("span").html('Product Already Exists').show('slow');
     error=1;
     }*/
    if($('#Unit').val()=='')
    {
        $('#Unit').next("span").html('Enter Unit').show('slow');
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
$(document).on( "click", ".removeresourceitem", function(){
    var resid=$(this).val();
    $.ajax({
        type:'POST',
        url:'../activities/deleteresourcetemp',
        beforeSend:function(){
            $('#removeresourceitem'+resid).attr("disabled",true);
            $('.preloaderitems').show();
        },
        dataType:"json",
        data:{resid:resid},
        success:function(data){
            $('#addedresources').html(data.result);
            $('#Activity_Rate').val(data.price);
            $('#amount').val(data.amount);
            $('#removeresourceitem'+resid).attr("disabled",false);
            $('.preloaderitems').hide();
        }
    });
});
$(document).on('click','#savecostactivities',function(){
    var error=0;
    $('.error').hide();
    if($('#Activities_Name').val()=='')
    {
        $('#Activities_Name').next("span").html('Enter Activity Name').show('slow');
        error=1;
    }
    if(ActivitiesName($('#Activities_Name').val())=='Yes')
    {
        $('#Activities_Name').next("span").html('Activity Already Exists').show('slow');
        error=1;
    }
    if($('#Activities_Unit').val()=='')
    {
        $('#Activities_Unit').next("span").html('Enter Unit').show('slow');
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
$(document).on('click','.deletecostactivities',function(){
    var id=$(this).val();
    var r = confirm("Are you sure you want to delete this Activity ?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../activities/delete/'+id,
            async:false,
            dataType:"json",
            success: function(data){
                if(data.error=='No')
                {
                    $('#listcostactivities').trigger('click');
                }
            }
        });
    }
});
function ActivitiesName(name)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../activities/checkname',
        async:false,
        data: {name:name},
        success: function(data){
            retval=data;
        }
    });
    return retval;

}