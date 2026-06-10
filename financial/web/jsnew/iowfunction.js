$(document).on( "click", ".listiows", function(){
    $('#workgroup').removeClass('active').next().slideUp();
    $('#itemofwork').addClass('active').next('.acc_container').slideDown();
    var id=$(this).val();
    var parents= getParentnames(id)
    $('#selectedProjectId').val(id);
    $('#projectnamedisplay').html(parents.Project);
    $('#workgroupnamedisplay').html(parents.Workgroup);
    $('#selectedProjectId').val(parents.Project_Id);
    $('#selectedWorkgrouId').val(parents.Workgroup_Id);
    $('#listiow').trigger('click') ;
});

$(document).on( "click", "#itemofwork", function(){
    if($('#selectedWorkgrouId').val()!='')
    {
        $('.acc_trigger').removeClass('active').next().slideUp();
        $('#itemofwork').addClass('active').next('.acc_container').slideDown();
        var parents= getParentnames($('#selectedWorkgrouId').val())
        $('#projectnamedisplay').html(parents.Project);
        $('#workgroupnamedisplay').html(parents.Workgroup);
        $('#selectedProjectId').val(parents.Project_Id);
        $('#selectedWorkgrouId').val(parents.Workgroup_Id);
        $('#listiow').trigger('click') ;
    }
});

$(document).on("click",'#saveiow',function(){
    var error=0;
    $('.error').hide();
    if($('#IOW_Name').val()=='')
    {
        $('#IOW_Name').next("span").html('Enter IOW Name').show('slow');
        error=1;
    }
    if($('#IOW_Name').val()!='' && IOWNameExist($('#IOW_Name').val(),$('#selectedWorkgrouId').val())=='Yes')
    {
        $('#IOW_Name').next("span").html('Work Group Name Exists').show('slow')
        error=1;
    }
    if($('#IOW_Unit').val()=='')
    {
        $('#IOW_Unit').next("span").html('Enter IOW Unit').show('slow');
        error=1;
    }
    if($('#IOW_Quantity').val()=='')
    {
        $('#IOW_Quantity').next("span").html('Enter IOW Quantity').show('slow');
        error=1;
    }
    if(error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../itemofworks/create',
            beforeSend : function(){
                $('#saveiow').attr("disabled", true);
            },
            dataType: "json",
            data: {Project_Id:$('#selectedProjectId').val(),Workgroup_Id:$('#selectedWorkgrouId').val(),IOW_Name:$('#IOW_Name').val(),IOW_Unit:$('#IOW_Unit').val(),IOW_Quantity:$('#IOW_Quantity').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#addiowform')[0].reset();
                    $('#listiow').trigger('click');

                    /*$('#workgroupaddsection').slideUp('slow');// slide down the project listing div
                     $('#workgrouplistsection').slideDown('slow');// slide down the project listing div

                     $('#listworkgroup').removeClass('btn-danger').addClass('btn-success');
                     $('#addworkgroup').removeClass('btn-success').addClass('btn-danger');

                     $('#addworkgroup').trigger('click');
                     $('#workgroupsearch').trigger('click')
                     */
                }
                else
                {
                    $('#saveiow').attr("disabled", false);
                }
                $('#saveiow').attr("disabled", false);
            }
        });
    }
});
$(function(){
    $( "#iowitems" ).sortable({

        deactivate:function(event, ui){
            //alert('test')
        },
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
                url: '../itemofworks/updatesort',
                data: {datavalue:updatedrows},
                dataType: "json",
                success: function(data){}
            });
        }

    }).disableSelection()
    // list iow click
    $('#listiow').click(function(){
        $('#iowaddsection').slideUp('slow');// slide down the project listing div
        $('#iowlistsection').slideDown('slow');// slide down the project listing div
        $('#listiow').removeClass('btn-danger').addClass('btn-success');
        $('#addiow').removeClass('btn-success').addClass('btn-danger');

        $.ajax({
            type: 'POST',
            url: '../itemofworks/search',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {Workgroup_Id:$('#selectedWorkgrouId').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#iowitems').html(data.result);
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });

    });
    // list iow click  \
    // add iow click
    $('#addiow').click(function(){
        $('#iowlistsection').slideUp('slow');// slide down the project listing div
        $('#iowaddsection').slideDown('slow');// slide down the project listing div
        $('#addiow').removeClass('btn-danger').addClass('btn-success');
        $('#listiow').removeClass('btn-success').addClass('btn-danger');

    });
    // add iow click
    // save iow click
    //iow function ends here

});
/*$(document).on('click','.disabled',function(){
    alert('This Project has Items Linked in gant chart. Please delete link for this project to enable sorting.')
});*/
$(document).on('click','.editiowbutton',function(){
        var id=$(this).val()
        $('#editiowname'+id).show();
        $('#editiowunit'+id).show();
        $('#editquantity'+id).show();
        $('#saveiowbutton'+id).show();
        $('#iowtext'+id).hide();
        $('#iowunit'+id).hide();
        $('#iowquantity'+id).hide();
        $('#editiowbutton'+id).hide();
});
$(document).on('click','.saveiowbutton',function(){
    var id =$(this).val();
    var name= $('#editiowname'+id).val();
    var unit= $('#editiowunit'+id).val();
    var quantity= $('#editquantity'+id).val();


    var error=0;
    $('.error').hide();
    if($('#editiowname'+id).val()=='')
    {
        $('#editiowname'+id).next("span").html('Enter IOW Name').show('slow');
        error=1;
    }
    if($('#editiowunit'+id).val()=='')
    {
        $('#editiowunit'+id).next("span").html('Enter IOW Unit').show('slow');
        error=1;
    }
    if($('#editquantity'+id).val()=='')
    {
        $('#editquantity'+id).next("span").html('Enter IOW Quantity').show('slow');
        error=1;
    }
    if(error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../itemofworks/update',
            beforeSend : function(){
                $('#saveiow').attr("disabled", true);
            },
            dataType: "json",
            data: {id:id,name:name,unit:unit,quantity:quantity},
            success: function(data){
                if(data.error=='No')
                {
                    $('#editiowname'+data.Id).hide();
                    $('#editiowunit'+data.Id).hide();
                    $('#editquantity'+data.Id).hide();
                    $('#saveiowbutton'+data.Id).hide();
                    $('#iowtext'+data.Id).text(data.Name).show();
                    $('#iowunit'+data.Id).text(data.Unit).show();
                    $('#iowquantity'+data.Id).text(data.Quantity).show();
                    $('#editiowbutton'+data.Id).show();
                }
                $('#saveiow').attr("disabled", false);
            }
        });
    }



});



$(document).on('click','.deleteiowbutton',function(){
    var iowid=$(this).val();
    var r = confirm("Are you sure you want to delete this Item Of Work?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../itemofworks/deleteiow',
            beforeSend : function(){
                $('#deleteiowbutton'+iowid).attr("disabled", true);
            },
            dataType: "json",
            data: {iowid:iowid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#iowrow'+data.Id).remove();
                }
                else
                {
                    alert(data.errortext);
                }

                $('#deleteiowbutton'+data.Id).attr("disabled", false);
            }
        });
    }
});

function getParentnames(id)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../itemofworks/GetParents',
        async:false,
        dataType: "json",
        data: {id:id},
        success: function(data){
            retval=data;
        }
    });
    return retval;
}
function IOWNameExist(name,workgroup)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../itemofworks/checkname',
        async:false,
        data: {name:name,workgroup:workgroup},
        success: function(data){
            retval=data;
        }
    });
    return retval;

}