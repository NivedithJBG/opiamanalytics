// $(function(){
//     var type = window.location.hash.substr(1);
//     if(type=='Construction')
//     {
//         $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
//         //$(this).toggleClass('active').next().slideDown();
//         $('#Construction').addClass('active').next('.acc_container').slideDown();
//         //$('#productaddsection').slideUp('slow');// slide down the project listing div
//         $('#constructionlistsection').slideDown('slow');// slide down the project listing div
//         $('#listconstruction').removeClass('btn-danger').addClass('btn-success');
//         $('#addprojectsetup').removeClass('btn-success').addClass('btn-danger');
//         $.ajax({
//             type: 'POST',
//             url: '../Construction/search',
//             beforeSend : function(){
//                 $('.preloader').show();
//             },
//             dataType: "json",
//             data: {overheadname:$('#searchconstructionname').val()},
//             success: function(data){
//                 if(data.error=='No')
//                 {
//                     $('#Constructionitems').html(data.result);
//                     $('#Constructiontable').show();
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
$(document).on( "click", "#Construction", function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    $('#listconstruction').trigger('click') ;
});
$(function(){
    $('#listconstruction').click(function(){
        $('#constructionlistsection').slideDown('slow');// slide down the project listing div
        $('#listconstruction').removeClass('btn-danger').addClass('btn-success');
        $('#addprojectsetup').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../Construction/search',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {investmentname:$('#searchconstructionname').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#Constructionitems').html(data.result);
                    $('#Constructiontable').show();
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });
    });
    $('#Constructionsearch').click(function(){
        $('#listconstruction').trigger('click');
    });
    $('#resourcesearchconstruction').click(function(){
        var toshow=$('#selecttype').val();
        var searchval=$('#searchname').val();
        $.ajax({
            type: 'POST',
            url: '../Construction/resourcesearch',
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

                $('#resourcesearchconstruction').attr("disabled", false);
                $('.preloader').hide();
            }
        });
    });
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
     $( "#Constructionitems" ).sortable({ 
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
                url: '../construction/updatesort',
                data: {datavalue:updatedrows},
                dataType: "json",
                success: function(data){
                    if(data.error=='No')
                    {
                       $('#listconstruction').trigger('click');
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
            url:'../Construction/addresourcetemp',
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
        url:'../Construction/deleteresourcetemp',
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
$(document).on('click','.deleteconstruction',function(){
    var id=$(this).val();
    var r = confirm("Are you sure you want to delete this Construction ?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../Construction/delete/'+id,
            async:false,
            dataType:"json",
            success: function(data){
                if(data.error=='No')
                {
                    $('#listconstruction').trigger('click');
                }
            }
        });
    }
});

$(document).on('click','.editconstructionbutton',function(){
    var idval=$(this).val();
    $('#constructionname'+idval).hide();
    $('#constructionunit'+idval).hide();
    $('#editconstbut'+idval).hide();
    $('#editconstname'+idval).show();
    $('#editconstunit'+idval).show();
    $('#saveconstructionbutton'+idval).show();
});

$(document).on('click','.saveconstructionbutton',function(){
    var idval=$(this).val();
    var error=0;
    $('.error').hide();
    if($('#editconstname'+idval).val()=='')
    {
        $('#editconstname'+idval).next("span").html('Enter Name').show('slow');
        error=1;
    }
    if($('#editconstunit'+idval).val()=='')
    {
        $('#editconstunit'+idval).next("span").html('Enter Unit').show('slow');
        error=1;
    }
  
    if(error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../construction/ActUpdate',
            beforeSend : function(){
                $('#saveconstructionbutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {id:idval,name:$('#editconstname'+idval).val(),unit:$('#editconstunit'+idval).val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#constructionname'+data.Id).show();
                    $('#constructionunit'+data.Id).show();
                    $('#editconstbut'+data.Id).show();
                    $('#editconstname'+data.Id).hide();
                    $('#editconstunit'+data.Id).hide();
                    $('#saveconstructionbutton'+data.Id).hide();
                    $('#editconstname'+data.Id).val(data.Name);
                    $('#editconstunit'+data.Id).val(data.Unit);
                    $('#constructionname'+data.Id).text(data.Name);
                    $('#constructionunit'+data.Id).text(data.Unit);

                }
                else
                {
                    alert(data.errortext);
                }

                $('#saveconstructionbutton'+data.Id).attr("disabled", false);
            }
        });
    }
});

function OverheadName(name)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../Construction/checkname',
        async:false,
        data: {name:name},
        success: function(data){
            retval=data;
        }
    });
    return retval;

}