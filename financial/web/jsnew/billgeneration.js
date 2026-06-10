/**
 * Created by SolmindsDelli5 on 27-03-2017.
 */
$(document).on( "click", ".viewbills", function(){
    $('#projects').removeClass('active').next().slideUp();
    $('#billgeneration').addClass('active').next('.acc_container').slideDown();
    var id= $(this).val();
    $('#selectedProjectId').val(id);
    $('#boqproject').val(id);
    $('#billgenprojectname').html(getProjectname(id));
    $('#billwbs').html(getWBS(id));
    $('#listboqbills').trigger('click');
});
$(function(){
    $('#listboqbills').click(function(){
        $.ajax({
            type: 'POST',
            url: '../ProjectPricing/Boqbills',
            dataType: "json",
            data: {projectid:$('#selectedProjectId').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#boqbills').html(data.result);
                    $('#client').val(data.client);
                    $('#boqbilldate').val(data.date);
                }
            }
        });
    });
    $('#billwbs').change(function(){
        var wbs=$(this).val();
        $.ajax({
            type: 'POST',
            url: '../ProjectPricing/IOW',
            dataType: "json",
            data: {wbs:wbs},
            success: function(data){
                if(data.error=='No')
                {
                    $('#billiow').html(data.result);
                }
            }
        });
    });
    $('#boqs').click(function(){
        var wbs=$('#billwbs').val();
        var iow=$('#billiow').val();
        var boqname=$('#boqname').val();
        $.ajax({
            type: 'POST',
            url: '../ProjectPricing/Boqs',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectid:$('#selectedProjectId').val(),wbs:wbs,iow:iow,item:boqname},
            success: function(data){
                if(data.error=='No')
                {
                    $('#billboqitems').html(data.result);
                    $('#boqsection').show();
                }
                $('.preloader').hide();
            }
        });
    });
    $('#boqsearch').click(function(){
        $('#boqs').trigger('click');
    });

});
$(document).on( "click", ".addboqitem", function(){
    var itemid=$(this).val();
    var unit=$('#unit'+itemid).val();
    var quantity=$('#quantity'+itemid).val();
    var actquantity=$('#actquantity'+itemid).val();
    var rate=$('#rate'+itemid).val();
    var error=0;
    $('.error').hide();
    if($('#actquantity'+itemid).val()==0 || $('#actquantity'+itemid).val()=='')
    {
        $('#actquantity'+itemid).next("span").html('Enter Valid Quantity').show('slow');
        error=1;
    }
    if(!$.isNumeric($('#actquantity'+itemid).val()))
    {
        $('#actquantity'+itemid).next("span").html('Enter Valid Quantity').show('slow');
        error=1;
    }
    if(error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../ProjectPricing/AddBoqbills',
            beforeSend : function(){
                $('#addboqitem'+itemid).attr("disabled", true);
            },
            dataType: "json",
            data: {projectid:$('#selectedProjectId').val(),itemid:itemid,unit:unit,quantity:quantity,rate:rate,actquantity:actquantity},
            success: function(data){
                if(data.error=='No')
                {
                    $('#addboqitem'+itemid).attr("disabled", false);
                    $('#boqrow'+itemid).remove();
                    $('#listboqbills').trigger('click');
                }
            }
        });
    }
});
$(document).on( "click", ".deleteboqitem", function(){
    var itemid=$(this).val();
    var projectid=$('#selectedProjectId').val();
    $.ajax({
        type: 'POST',
        url: '../ProjectPricing/DeleteBoqitem',
        beforeSend : function(){
            $('#deleteboqitem'+itemid).attr("disabled", true);
        },
        dataType: "json",
        data:{itemid:itemid,projectid:projectid},
        success: function(data){
            if(data.error=='No')
            {
                $('#boqitemsrow'+itemid).remove();
                //$('#listboqbills').trigger('click');
            }
            $('#deleteboqitem'+itemid).attr("disabled", false);
        }
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
function getWBS(id)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../ProjectPricing/Wbssearch',
        async:false,
        data: {projectid:id},
        dataType: "json",
        success: function(data){
            if(data.error=='No')
            {
                retval=data.result;
            }
        }
    });
    return retval;
}
