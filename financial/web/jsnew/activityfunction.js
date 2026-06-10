$(document).on( "click", ".viewactivitiesbutton", function(){
    $('#itemofwork').removeClass('active').next().slideUp();
    $('#activities').addClass('active').next('.acc_container').slideDown();
    var id=$(this).val();
    var parents= getActivityParentnames(id);
    $('#activityprojoctname').html(parents.Project);
    $('#activityworkgroup').html(parents.Workgroup);
    $('#activityiow').html(parents.Iow);
    $('#selectedProjectId').val(parents.Project_Id);
    $('#selectedWorkgrouId').val(parents.Workgroup_Id);
    $('#selectedIOWId').val(parents.IOW_Id);
    $('#listactivities').trigger('click')
});

$(document).on( "click", "#activities", function(){
    if($('#selectedIOWId').val()!='')
    {
        $('.acc_trigger').removeClass('active').next().slideUp();
        $('#activities').addClass('active').next('.acc_container').slideDown();
        var parents= getActivityParentnames($('#selectedIOWId').val());
        $('#activityprojoctname').html(parents.Project);
        $('#activityworkgroup').html(parents.Workgroup);
        $('#activityiow').html(parents.Iow);
        $('#selectedProjectId').val(parents.Project_Id);
        $('#selectedWorkgrouId').val(parents.Workgroup_Id);
        $('#selectedIOWId').val(parents.IOW_Id);
        $('#listactivities').trigger('click')
    }

});
$(function(){


// list activity click
    $('#listactivities').click(function(){
        $('#activityaddsection').slideUp('slow');// slide down the project listing div
        $('#activitylistsection').slideDown('slow');// slide down the project listing div
        $('#listactivities').removeClass('btn-danger').addClass('btn-success');
        $('#addactive').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../itemofworks/searchactivities',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {iowid:$('#selectedIOWId').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#activityitems').html(data.result);
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
    $('#addactive').click(function(){
        $('#activitylistsection').slideUp('slow');// slide down the project listing div
        $('#activityaddsection').slideDown('slow');// slide down the project listing div
        $('#addactive').removeClass('btn-danger').addClass('btn-success');
        $('#listactivities').removeClass('btn-success').addClass('btn-danger');

    });
// add iow click
// save iow click
    $('#saveactivity').click(function(){

        var error=0;
        $('.error').hide();
        if($('#activityname').val()=='')
        {
            $('#activityname').next("span").html('Enter IOW Name').show('slow');
            error=1;
        }
        if($('#activityname').val()!='' && ActivityExist($('#activityname').val(),$('#selectedIOWId').val())=='Yes')
        {
            $('#activityname').next("span").html('Activity Name Exists').show('slow')
            error=1;
        }
        if(error==0)
        {
            $.ajax({
                type: 'POST',
                url: '../itemofworks/addactivity',
                beforeSend : function(){
                    $('#saveactivity').attr("disabled", true);
                },
                dataType: "json",
                data: {iow:$('#selectedIOWId').val(),name:$('#activityname').val()},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#listactivities').trigger('click');
                        $('#addactivityform')[0].reset();
                        $('#saveactivity').attr("disabled", false);
                    }
                    $('#saveactivity').attr("disabled", false);
                }
            });
        }


    });
    $( "#activityitems" ).sortable({
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
                url: '../itemofworks/activitysort',
                data: {datavalue:updatedrows},
                dataType: "json",
                success: function(data){}
            });
        }

    }).disableSelection()

});

$(document).on('click','.editactivitybutton',function(){
    var id=$(this).val()
    $('#editactivityname'+id).show();
    $('#saveactivitybutton'+id).show();
    $('#activityname'+id).hide();
    $('#editactivitybutton'+id).hide();
});
$(document).on('click','.deleteactivitybutton',function(){
    var activity=$(this).val();
    var r = confirm("Are you sure you want to delete this Activity?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../itemofworks/Deleteactivity',
            beforeSend : function(){
                $('#deleteactivitybutton'+activity).attr("disabled", true);
            },
            dataType: "json",
            data: {activity:activity},
            success: function(data){
                if(data.error=='No')
                {
                    $('#activityrow'+data.Id).remove();
                }
                else
                {
                    alert(data.errortext);
                }

                $('#deleteactivitybutton'+data.Id).attr("disabled", false);
            }
        });
    }

});
$(document).on('click','.saveactivitybutton',function(){
    var id=$(this).val();
    $.ajax({
        type: 'POST',
        url: '../itemofworks/Editactivity',
        async:false,
        dataType: "json",
        data: {id:id,name:$('#editactivityname'+id).val()},
        success: function(data){
            $('#editactivityname'+data.Activity_Id).text(data.Name).hide();
            $('#saveactivitybutton'+data.Activity_Id).hide();
            $('#activityname'+data.Activity_Id).text(data.Name).show();
            $('#editactivitybutton'+data.Activity_Id).show();
        }
    });
});
function getActivityParentnames(id)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../itemofworks/GetActivityParents',
        async:false,
        dataType: "json",
        data: {id:id},
        success: function(data){
            retval=data;
        }
    });
    return retval;
}
function ActivityExist(name,iowid)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../itemofworks/checkActivityname',
        async:false,
        data: {name:name,iowid:iowid},
        success: function(data){
            retval=data;
        }
    });
    return retval;

}
