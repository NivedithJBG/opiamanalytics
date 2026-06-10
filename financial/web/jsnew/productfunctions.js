// $(function(){
//     var type = window.location.hash.substr(1);
//     if(type=='Products')
//     {
//         $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
//         //$(this).toggleClass('active').next().slideDown();
//         $('#Products').addClass('active').next('.acc_container').slideDown();
//         $('#productaddsection').slideUp('slow');// slide down the project listing div
//         $('#productlistsection').slideDown('slow');// slide down the project listing div
//         $('#listproduct').removeClass('btn-danger').addClass('btn-success');
//         $('#addproduct').removeClass('btn-success').addClass('btn-danger');
//         $.ajax({
//             type: 'POST',
//             url: '../products/search', 
//             beforeSend : function(){
//                 $('.preloader').show();
//             },
//             dataType: "json",
//             data: {productname:$('#searchproductname').val(),projectid:$('#selectedProjectId').val()},
//             success: function(data){
//                 if(data.error=='No')
//                 {
//                     $('#productitems').html(data.result);
//                     $('#producttable').show();
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

/*$(document).on( "click", ".viewproducts", function(){
    //$('.acc_container').slideUp();
    $('#project').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
    //$(this).toggleClass('active').next().slideDown();
    $('#Products').addClass('active').next('.acc_container').slideDown();
    var id= $(this).val();
    $('#proddispprojectname').html(getProjectname(id));
    $('#selectedProjectId').val(id);
    $('#listproduct').trigger('click');
});*/

$(document).on( "click", "#Products", function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
        $('#listproduct').trigger('click') ;
});
$(function(){
    $('#listproduct').click(function(){
        $('#productaddsection').slideUp('slow');// slide down the project listing div
        $('#productlistsection').slideDown('slow');// slide down the project listing div
        $('#listproduct').removeClass('btn-danger').addClass('btn-success');
        $('#addproduct').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../products/search',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {productname:$('#searchproductname').val(),projectid:$('#selectedProjectId').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#productitems').html(data.result);
                    $('#producttable').show();
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });
    });
    $('.resourcesearchbut').click(function(){
        var resorcetypeid=$(this).val();
        console.log(resorcetypeid);
        $.ajax({
            type: 'POST',
            url: '../ProjectSetup/resourcesearchbytyid',
            dataType: "json",
            data: {resourcetypeid:resorcetypeid},
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

                $('.preloader').hide();
            }
        });
    });

    $('#productsearch').click(function(){
        $('#listproduct').trigger('click');
    });
    /*$('#addproduct').click(function(){
        $('#productlistsection').slideUp('slow');// slide down the project listing div
        $('#productaddsection').slideDown('slow');// slide down the project listing div
        $('#addproduct').removeClass('btn-danger').addClass('btn-success');
        $('#listproduct').removeClass('btn-success').addClass('btn-danger');
        $('.error').hide();

    });*/
    $('#saveproduct').click(function(){
        var error=0;
        $('.error').hide();
        if($('#productname').val()=='')
        {
            $("#productname").next("span").html('Enter Product Name').show('slow');
            error=1;
        }
        if($('#productname').val()!='' && ProductNameExists($('#productname').val(),$('#selectedProjectId').val())=='Yes')
        {
            $('#productname').next("span").html('Product Name Exists').show('slow')
            error=1;
        }
        if($('#productunit').val()=='')
        {
            $("#productunit").next("span").html('Enter Product Unit').show('slow');
            error=1;
        }
        if($('#productqty').val()=='')
        {
            $("#productqty").next("span").html('Enter Product Quantity').show('slow');
            error=1;
        }

        if(error==0)
        {
            $.ajax({
                type: 'POST',
                url: '../products/create',
                beforeSend : function(){
                    $('#saveproduct').attr("disabled", true);
                },
                dataType: "json",
                data: {Project_Id:$('#selectedProjectId').val(),productname:$('#productname').val(),productunit:$('#productunit').val(),productqty:$('#productqty').val()},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#addproductform')[0].reset();
                        $('#addproduct').trigger('click');


                    }
                    else
                    {
                        $("#productname").next("span").html(data.errortext).show('slow');
                        $('#saveproduct').attr("disabled", false);
                    }
                    $('#saveproduct').attr("disabled", false);
                }
            });
        }
    });
    $( "#productitems" ).sortable({ 
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
                url: '../products/updatesort',
                data: {datavalue:updatedrows},
                dataType: "json",
                success: function(data){
                    if(data.error=='No')
                    {
                       $('#listproduct').trigger('click');
                    }

                }
            });
        }

    }).disableSelection()
});
$(document).on('click','.editproductbutton',function(){
    var idval=$(this).val();
    $('#productname'+idval).hide();
    $('#productunit'+idval).hide();
    $('#productqty'+idval).hide();
    $('#editproductbutton'+idval).hide();
    $('#editproductname'+idval).show();
    $('#editproductunit'+idval).show();
    $('#editproductqty'+idval).show();
    $('#saveproductbutton'+idval).show();
});
$(document).on('click','.saveproductbutton',function(){
    var idval=$(this).val();
    var error=0;
    $('.error').hide();
    if($('#editproductname'+idval).val()=='')
    {
        $('#editproductname'+idval).next("span").html('Enter Name').show('slow');
        error=1;
    }
    if($('#editproductunit'+idval).val()=='')
    {
        $('#editproductunit'+idval).next("span").html('Enter Unit').show('slow');
        error=1;
    }
    if($('#editproductqty'+idval).val()=='')
    {
        $('#editproductqty'+idval).next("span").html('Enter Quantity').show('slow');
        error=1;
    }
    if(error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../products/update',
            beforeSend : function(){
                $('#saveproductbutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {proid:idval,name:$('#editproductname'+idval).val(),unit:$('#editproductunit'+idval).val(),qty:$('#editproductqty'+idval).val(),rate:$('#editproductrate'+idval).val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#productname'+data.Id).show();
                    $('#productunit'+data.Id).show();
                    $('#productqty'+data.Id).show();
                    $('#productamount'+data.Id).show();
                    $('#editproductbutton'+data.Id).show();
                    $('#editproductname'+data.Id).hide();
                    $('#editproductunit'+data.Id).hide();
                    $('#editproductqty'+data.Id).hide();
                    $('#editproductamount'+data.Id).hide();
                    $('#saveproductbutton'+data.Id).hide();
                    $('#editproductname'+data.Id).val(data.Name);
                    $('#editproductunit'+data.Id).val(data.Unit);
                    $('#editproductqty'+data.Id).val(data.Qty);
                    $('#editproductamount'+data.Id).val(data.Amount);
                    $('#productname'+data.Id).text(data.Name);
                    $('#productunit'+data.Id).text(data.Unit);
                    $('#productqty'+data.Id).text(data.Qty);
                    $('#productamount'+data.Id).text(data.Amount);

                }
                else
                {
                    alert(data.errortext);
                }

                $('#saveproductbutton'+data.Id).attr("disabled", false);
            }
        });
    }
});

$(document).on('click','.deleteproduct',function(){
    var id=$(this).val();
 var r = confirm("Are you sure you want to delete this Product ?");
    if (r == true) {
    $.ajax({
        type: 'POST',
        url: '../products/delete/'+id,
        async:false,
        dataType:"json",
        success: function(data){
            if(data.error=='No')
            {
                $('#listproduct').trigger('click');
            }
        }
    });
    }
});

/*$(document).on('click','#addproduct',function(){

 window.location.href="../products/create?projectid="+ $('#selectedProjectId').val() ;
});*/



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

function ProductNameExists(name,project)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../products/checkname',
        async:false,
        data: {name:name,project:project},
        success: function(data){
            retval=data;
        }
    });
    return retval;

}
