// $(function(){
//     var type = window.location.hash.substr(1);
//     if(type=='Projectsetup')
//     {
//         $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
//         //$(this).toggleClass('active').next().slideDown();
//         $('#Projectsetup').addClass('active').next('.acc_container').slideDown();
//         //$('#productaddsection').slideUp('slow');// slide down the project listing div
//         $('#projectsetuplistsection').slideDown('slow');// slide down the project listing div
//         $('#listprojectsetup').removeClass('btn-danger').addClass('btn-success');
//         $('#addprojectsetup').removeClass('btn-success').addClass('btn-danger');
//         $.ajax({
//             type: 'POST',
//             url: '../ProjectSetup/search',
//             beforeSend : function(){
//                 $('.preloader').show();
//             },
//             dataType: "json",
//             data: {overheadname:$('#searchsetupname').val()},
//             success: function(data){
//                 if(data.error=='No')
//                 {
//                     $('#Setupitems').html(data.result);
//                     $('#Seuptable').show();
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
$(document).on( "click", "#Projectsetup", function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#listprojectsetup').trigger('click') ;
});
$(function(){
    $('#listprojectsetup').click(function(){
        $('#projectsetuplistsection').slideDown('slow');// slide down the project listing div
        $('#listprojectsetup').removeClass('btn-danger').addClass('btn-success');
        $('#addprojectsetup').removeClass('btn-success').addClass('btn-danger');
        // alert($('#searchinvestmentname').val());
        $.ajax({
            type: 'POST',
            url: '../ProjectSetup/search',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {investmentname:$('#searchsetupname').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#Setupitems').html(data.result);
                    $('#Seuptable').show();
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });
    });
    $('#Setupsearch').click(function(){
        $('#listprojectsetup').trigger('click');
    });
    $('#resourcesearchsetup').click(function(){
        var toshow=$('#selecttype').val();
        var searchval=$('#searchname').val();
        $.ajax({
            type: 'POST',
            url: '../ProjectSetup/resourcesearch',
            beforeSend : function(){
                $('#resourcesearchsetup').attr("disabled", true);
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

                $('#resourcesearchsetup').attr("disabled", false);
                $('.preloader').hide();
            }
        });
    });
    // $('#selectedresource').click(function(){
       
    //    $('#selectresourceid').val($(this).val());
    // //    alert($('#selectresourceid').val());

    // });

    $('.resourcesearch').click(function(){
        var resorcetype=$(this).val();
        if(resorcetype!=''){
            $('#selectresourceid').val(resorcetype);
        }
        var name=$('#resourcename').val();
        // console.log(resorcetypeid);
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

       $( "#Setupitems" ).sortable({ 
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
                url: '../projectSetup/updatesort',
                data: {datavalue:updatedrows},
                dataType: "json",
                success: function(data){
                    if(data.error=='No')
                    {
                       $('#listprojectsetup').trigger('click');
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
            url:'../ProjectSetup/addresourcetemp',
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
        url:'../ProjectSetup/deleteresourcetemp',
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
$(document).on('click','.deleteprojectsetup',function(){
    var id=$(this).val();
    var r = confirm("Are you sure you want to delete this ProjectSetup ?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../ProjectSetup/delete/'+id,
            async:false,
            dataType:"json",
            success: function(data){
                if(data.error=='No')
                {
                    $('#listprojectsetup').trigger('click');
                }
            }
        });
    }
});
$(document).on('click','.editprojectsetup',function(){
        var id=$(this).val()
        $('#editprosetupname'+id).show();
        $('#editprosetupunit'+id).show();
        $('#saveprojectsetupbutton'+id).show();
        $('#overheadname'+id).hide();
        $('#overheadunit'+id).hide();
        $('#editprojectsetup'+id).hide();
});
$(document).on('click','.saveprojectsetup',function(){
    var id =$(this).val();
    var name= $('#editprosetupname'+id).val();
    var unit= $('#editprosetupunit'+id).val();

    var error=0;
    $('.error').hide();
    if($('#editprosetupname'+id).val()=='')
    {
        $('#editprosetupname'+id).next("span").html('Enter Project Setup Name').show('slow');
        error=1;
    }
    if($('#editprosetupunit'+id).val()=='')
    {
        $('#editprosetupunit'+id).next("span").html('Enter Project Setup Unit').show('slow');
        error=1;
    }
    if(error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../ProjectSetup/ActUpdate',
            beforeSend : function(){
                $('#saveprojectsetupbutton'+id).attr("disabled", true);
            },
            dataType: "json",
            data: {id:id,name:name,unit:unit},
            success: function(data){
                if(data.error=='No')
                {
                    $('#editprosetupname'+data.Id).hide();
                    $('#editprosetupunit'+data.Id).hide();
                    $('#saveprojectsetupbutton'+data.Id).hide();
                    $('#overheadname'+data.Id).text(data.Name).show();
                    $('#overheadunit'+data.Id).text(data.Unit).show();
                    $('#editprojectsetup'+data.Id).show();
                }
                $('#saveprojectsetupbutton'+id).attr("disabled", false);
            }
        });
    }



});

function OverheadName(name)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../ProjectSetup/checkname',
        async:false,
        data: {name:name},
        success: function(data){
            retval=data;
        }
    });
    return retval;

}