$(function(){
    var type = window.location.hash.substr(1);
    if(type=='Purchasedinputs')
    {
        $('.acc_trigger').removeClass('active').next().slideUp();
        $('#Purchasedinputs').addClass('active').next('.acc_container').slideDown();

        $('#pilistsection').slideDown('slow');// slide down the project listing div
        $('#listpurchasedinputs').removeClass('btn-danger').addClass('btn-success');
        $('#addpurchasedinputs').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../purchasedinputs/search',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {piname:$('#searchpiname').val(),projectid:$('#selectedProjectId').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#piitems').html(data.result);
                    $('#pitable').show();
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

/*$(document).on( "click", ".viewpurchasedinputs", function(){
    //$('.acc_container').slideUp();
    $('#project').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
    //$(this).toggleClass('active').next().slideDown();
    $('#Purchasedinputs').addClass('active').next('.acc_container').slideDown();
    var id= $(this).val();
    $('#pidispprojectname').html(getProjectname(id));
    $('#selectedProjectId').val(id);
    $('#Purchasedinputs').trigger('click');
});*/

$(document).on( "click", "#Purchasedinputs", function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...
        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    }
        $('#listpurchasedinputs').trigger('click') ;

});
$(function(){
    $('#listpurchasedinputs').click(function(){
        $('#piaddsection').slideUp('slow');// slide down the project listing div
        $('#pilistsection').slideDown('slow');// slide down the project listing div
        $('#listpurchasedinputs').removeClass('btn-danger').addClass('btn-success');
        $('#addpurchasedinputs').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../purchasedinputs/search',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {piname:$('#searchpiname').val(),projectid:$('#selectedProjectId').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#piitems').html(data.result);
                    $('#pitable').show();
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });
    });

    $('#pisearch').click(function(){
        $('#listpurchasedinputs').trigger('click');
    })
    /*$('#addpurchasedinputs').click(function(){
        $('#pilistsection').slideUp('slow');// slide down the project listing div
        $('#piaddsection').slideDown('slow');// slide down the project listing div
        $('#addpurchasedinputs').removeClass('btn-danger').addClass('btn-success');
        $('#listpurchasedinputs').removeClass('btn-success').addClass('btn-danger');
        $('.error').hide();

    });*/
    $('#savepi').click(function(){
        var error=0;
        $('.error').hide();
        if($('#piname').val()=='')
        {
            $("#piname").next("span").html('Enter Purchased Inputs Name').show('slow');
            error=1;
        }
        if($('#piname').val()!='' && PINameExists($('#piname').val(),$('#selectedProjectId').val())=='Yes')
        {
            $('#piname').next("span").html('Purchased Inputs Name Exists').show('slow')
            error=1;
        }
        if($('#piunit').val()=='')
        {
            $("#piunit").next("span").html('Enter Purchased Inputs Unit').show('slow');
            error=1;
        }
        if($('#piqty').val()=='')
        {
            $("#piqty").next("span").html('Enter Purchased Inputs Quantity').show('slow');
            error=1;
        }

        if(error==0)
        {
            $.ajax({
                type: 'POST',
                url: '../purchasedinputs/create',
                beforeSend : function(){
                    $('#savepi').attr("disabled", true);
                },
                dataType: "json",
                data: {Project_Id:$('#selectedProjectId').val(),piname:$('#piname').val(),piunit:$('#piunit').val(),piqty:$('#piqty').val()},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#addpiform')[0].reset();
                        $('#addpurchasedinputs').trigger('click');


                    }
                    else
                    {
                        $("#piname").next("span").html(data.errortext).show('slow');
                        $('#savepi').attr("disabled", false);
                    }
                    $('#savepi').attr("disabled", false);
                }
            });
        }
    });
});
$(document).on('click','.editpibutton',function(){
    var idval=$(this).val();
    $('#piname'+idval).hide();
    $('#piunit'+idval).hide();
    $('#piqty'+idval).hide();
    $('#editpibutton'+idval).hide();
    $('#editpiname'+idval).show();
    $('#editpiunit'+idval).show();
    $('#editpiqty'+idval).show();
    $('#savepibutton'+idval).show();
});
$(document).on('click','.savepibutton',function(){
    var idval=$(this).val();
    var error=0;
    $('.error').hide();
    if($('#editpiname'+idval).val()=='')
    {
        $('#editpiname'+idval).next("span").html('Enter Name').show('slow');
        error=1;
    }
    if($('#editpiunit'+idval).val()=='')
    {
        $('#editpiunit'+idval).next("span").html('Enter Unit').show('slow');
        error=1;
    }
    if($('#editpiqty'+idval).val()=='')
    {
        $('#editpiqty'+idval).next("span").html('Enter Quantity').show('slow');
        error=1;
    }
    if(error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../purchasedinputs/update',
            beforeSend : function(){
                $('#savepibutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {piid:idval,name:$('#editpiname'+idval).val(),unit:$('#editpiunit'+idval).val(),qty:$('#editpiqty'+idval).val(),rate:$('#editpirate'+idval).val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#piname'+data.Id).show();
                    $('#piunit'+data.Id).show();
                    $('#piqty'+data.Id).show();
                    $('#editpibutton'+data.Id).show();
                    $('#editpiname'+data.Id).hide();
                    $('#editpiunit'+data.Id).hide();
                    $('#editpiqty'+data.Id).hide();
                    $('#savepibutton'+data.Id).hide();
                    $('#editpiname'+data.Id).val(data.Name);
                    $('#editpiunit'+data.Id).val(data.Unit);
                    $('#editpiqty'+data.Id).val(data.Qty);
                    $('#editpiamount'+data.Id).val(data.Amount);
                    $('#piname'+data.Id).text(data.Name);
                    $('#piunit'+data.Id).text(data.Unit);
                    $('#piqty'+data.Id).text(data.Qty);
                    $('#piamount'+data.Id).text(data.Amount);

                }
                else
                {
                    alert(data.errortext);
                }

                $('#savepibutton'+data.Id).attr("disabled", false);
            }
        });
    }
});
$(document).on('click','.deletepi',function(){
    var id=$(this).val();
    var r = confirm("Are you sure you want to delete this Purchased Inputs ?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../purchasedinputs/delete/'+id,
            async:false,
            dataType:"json",
            success: function(data){
                if(data.error=='No')
                {
                    $('#listpurchasedinputs').trigger('click');
                }
            }
        });
    }
});

/*$(document).on('click','#addpurchasedinputs',function(){

    window.location.href="../purchasedinputs/create?projectid="+ $('#selectedProjectId').val() ;
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
function PINameExists(name,project)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../purchasedinputs/checkname',
        async:false,
        data: {name:name,project:project},
        success: function(data){
            retval=data;
        }
    });
    return retval;

}
