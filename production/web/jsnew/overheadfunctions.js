// $(function(){
//     var type = window.location.hash.substr(1);
//     if(type=='Overheads')
//     {
//         $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
//         //$(this).toggleClass('active').next().slideDown();
//         $('#Overheads').addClass('active').next('.acc_container').slideDown();
//         //$('#productaddsection').slideUp('slow');// slide down the project listing div
//         $('#overheadslistsection').slideDown('slow');// slide down the project listing div
//         $('#listoverheads').removeClass('btn-danger').addClass('btn-success');
//         $('#addoverheads').removeClass('btn-success').addClass('btn-danger');
//         $.ajax({
//             type: 'POST',
//             url: '../overheads/search',
//             beforeSend : function(){
//                 $('.preloader').show();
//             },
//             dataType: "json",
//             data: {overheadname:$('#searchoverheadname').val()},
//             success: function(data){
//                 if(data.error=='No')
//                 {
//                     $('#overheaditems').html(data.result);
//                     $('#overheadstable').show();
//                 }
//                 else
//                 {
//                     alert(data.errortext);
//                 }
//                 $('.preloader').hide();
//             }
//         });
//     }
// });
$(document).on( "click", "#Overheads", function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#listoverheads').trigger('click') ;
});
$(function(){
    $('#listoverheads').click(function(){
        $('#overheadslistsection').slideDown('slow');// slide down the project listing div
        $('#listoverheads').removeClass('btn-danger').addClass('btn-success');
        $('#addoverheads').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../overheads/search',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {overheadname:$('#searchoverheadname').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#overheaditems').html(data.result);
                    $('#overheadstable').show();
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });
    });
    $('#overheadsearch').click(function(){
        $('#listoverheads').trigger('click');
    });
    $('#resourcesearchoverhead').click(function(){
        var toshow=$('#selecttype').val();
        var searchval=$('#searchname').val();
        $.ajax({
            type: 'POST',
            url: '../overheads/resourcesearch',
            beforeSend : function(){
                $('#resourcesearchoverhead').attr("disabled", true);
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

                $('#resourcesearchoverhead').attr("disabled", false);
                $('.preloader').hide();
            }
        });
    });
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
    $( "#overheaditems" ).sortable({ 
        items: '.no',
        update:function( event, ui ) {
            //alert($(this).index());
            var updatedrows=[];
            $(this).closest('table').find('tbody tr').each(function (i) {
                var rowid=$(this).attr('data-id');
                var rowindex=$(this).index();
                updatedrows.push({
                    rowid: rowid,
                    rowindex:rowindex
                })
            });
            $.ajax({
                type: 'POST',
                url: '../overheads/updatesort',
                data: {datavalue:updatedrows},
                dataType: "json",
                success: function(data){
                    if(data.error=='No')
                    {
                       $('#listoverheads').trigger('click');
                    }

                }
            });
        }

    }).disableSelection()
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
            url:'../overheads/addresourcetemp',
            beforeSend:function(){
                $('#addresourcebutton'+resid).attr("disabled",true);
                $('.preloaderitems').show();
            },
            dataType:"json",
            data:{overhead_id:$('#OverheadId').val(),resid:resid,rate:price,srate:sprice,quantity:quantity},
            success:function(data){
                $('#addedresources').html(data.result);
                $('#addresourcebutton'+resid).attr("disabled",false);
                $('#resourcequantity'+resid).val('');
                $('.preloaderitems').hide();
                $('#resourcerow'+resid).remove();
                $('#overheadratetotal').val(data.price);
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
        url:'../overheads/deleteresourcetemp',
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
$(document).on('click','.deleteoverhead',function(){
    var id=$(this).val();
    var r = confirm("Are you sure you want to delete this Overhead ?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../overheads/delete/'+id,
            async:false,
            dataType:"json",
            success: function(data){
                if(data.error=='No')
                {
                    $('#listoverheads').trigger('click');
                }
            }
        });
    }
});

$(document).on('click','.editlogisticsbutton',function(){
    var idval=$(this).val();
    $('#overheadname'+idval).hide();
    $('#overheadunit'+idval).hide();
    $('#editlogisticsbut'+idval).hide();
    $('#editoverheadname'+idval).show();
    $('#editoverheadunit'+idval).show();
    $('#savelogisticsbutton'+idval).show();
});

$(document).on('click','.saveoverheadsbutton',function(){
    var idval=$(this).val();
    var error=0;
    $('.error').hide();
    if($('#editoverheadname'+idval).val()=='')
    {
        $('#editoverheadname'+idval).next("span").html('Enter Name').show('slow');
        error=1;
    }
    if($('#editoverheadunit'+idval).val()=='')
    {
        $('#editoverheadunit'+idval).next("span").html('Enter Unit').show('slow');
        error=1;
    }
  
    if(error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../overheads/ActUpdate',
            beforeSend : function(){
                $('#savelogisticsbutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {id:idval,name:$('#editoverheadname'+idval).val(),unit:$('#editoverheadunit'+idval).val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#editoverheadname'+data.Id).hide();
                    $('#editoverheadunit'+data.Id).hide();
                    $('#savelogisticsbutton'+data.Id).hide();
                    $('#overheadname'+data.Id).text(data.Name).show();
                    $('#overheadunit'+data.Id).text(data.Unit).show();
                    $('#editlogisticsbut'+data.Id).show();

                    

                }
                else
                {
                    alert(data.errortext);
                }

                $('#savelogisticsbutton'+data.Id).attr("disabled", false);
            }
        });
    }
});

function OverheadName(name)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../overheads/checkname',
        async:false,
        data: {name:name},
        success: function(data){
            retval=data;
        }
    });
    return retval;

}