$(document).on( "click", ".protasklistbutton", function(){
    $('#activity').removeClass('active').next().slideUp();
    $('#protask').addClass('active').next('.acc_container').slideDown();
    var actid=$(this).val();
    var processid=$(this).attr('data-id');
    // alert(id);
    // var parents= getParentnames(id)
    // $('#selectedProjectId').val(id);
    // $('#projectnamedisplay').html(parents.Project); 
    // $('#workgroupnamedisplay').html(parents.Workgroup);
    // $('#selectedProjectId').val(parents.Project_Id);
    $('#selectedWorkgrouId').val($('#wg1_id').val());
    $('#IOWorkgroupssId').val($('#wg1_id').val());
    // alert($('#IOWorkgroupssId').val());
    $('#activityId').val(actid);
    $('#processId').val(processid);
    // alert(processid);
    $('#listtasks').trigger('click') ;
});

$(function(){

    // list Task click
    $('#listtasks').click(function(){
        $('#tasksaddsection').slideUp('slow');// slide down the project listing div
        $('#taskslistsection').slideDown('slow');// slide down the project listing div
        $('#listtasks').removeClass('btn-danger').addClass('btn-success');
        $('#addtask').removeClass('btn-success').addClass('btn-danger');
        $.ajax({
            type: 'POST',
            url: '../tasks/search',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {Workgroup_Id:$('#IOWorkgroupId').val(),activityid:$('#activityId').val(),processid:$('#processId').val()},
            success: function(data){
                if(data.error=='No')
                {
                    console.log("seccess search request");
                    $('#taskitems').html(data.result);
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });

    });
    
    $('#addtask').click(function(){
        $('#taskslistsection').slideUp('slow');// slide down the project listing div
        $('#tasksaddsection').slideDown('slow');// slide down the project listing div
        $('#addtask').removeClass('btn-danger').addClass('btn-success');
        $('#listtasks').removeClass('btn-success').addClass('btn-danger');
         $.ajax({
            type: 'POST',
            url: '../tasks/deletedtasksearch',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {Workgroup_Id:$('#IOWorkgroupId').val(),activityid:$('#activityId').val(),processid:$('#processId').val()},
            success: function(data){
                if(data.error=='No')
                {
                    console.log("seccess search request");
                    $('#deletedtaskitems').html(data.result);
                }
                else
                {
                    alert(data.errortext);
                }
                $('.preloader').hide();
            }
        });


    });

});
//add deleted tasks
$(document).on('click','.addprotaskbutton',function(){
    var taskid=$(this).val();
    var actId=$('#activityId').val();
    var workgp_Id = $('#IOWorkgroupssId').val();
    console.log(taskid);
    console.log(actId);
    console.log(workgp_Id);
    $.ajax({
            type: 'POST',
            url: '../tasks/savedeletedtasks',
            // beforeSend : function(){
            //     $('#addprotaskbutton').attr("disabled", true);
            // },
            dataType: "json",
            // Workgroup_Id:$('#selectedWorkgrouId').val()
            data: {taskid:taskid, activityid:actId, WgId:workgp_Id },
            success: function(data){
                if(data.error=='No')
                {
                    console.log("success request");
                    // $('#listtasks').trigger('click');
                    // $('addprotaskbutton').attr("disabled", false);
                }
            }
        });
        
    $('#taskrow1'+taskid).remove();

});
//delete task
$(document).on('click','.deleteprotaskbutton',function(){
    var taskid=$(this).val();
    // var type=$('#process_id').val();
    
    console.log(taskid);
    // console.log(type);
    $('#taskrow'+taskid).remove();

});
$(document).on('click','#saveprotaskbutton',function(){
    // console.log(id);
   
   $.ajax({
            type: 'POST',
            url: '../tasks/savetasks',
            beforeSend : function(){
                $('#saveprotaskbutton').attr("disabled", true);
            },
            dataType: "json",
            // Workgroup_Id:$('#selectedWorkgrouId').val()
            data: $('#protasksaveform').serialize(),
            success: function(data){
                if(data.error=='No')
                {
                    console.log("success request");
                    $('#listtasks').trigger('click');
                    $('#saveprotaskbutton').attr("disabled", false);
                }
            }
        });
    console.log("successfully saved");

});

function getParentnames(id)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../tasks/GetParents',
        async:false,
        dataType: "json",
        data: {id:id},
        success: function(data){
            retval=data;
        }
    });
    return retval;
}