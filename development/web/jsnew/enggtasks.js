/**
 * Created by SolmindsDelli5 on 11-01-2018.
 */

$(document).on( "click", ".taskmanagement", function(){

    $('#enggactivities').removeClass('active').next().slideUp();

    $('#enggtasks').addClass('active').next('.acc_container').slideDown();

    var id= $(this).val();

    var type= $(this).attr('data-type');

    $('#activityid').val(id);

    $('#process').val(type);

    $('#enggactivityname').html(getActivityname(id,type));

    $('#listenggtasks').trigger('click');
});

$(function(){
    $('#listenggtasks').click(function(){
        $('#taskaddsection').slideUp('slow');// slide down the project listing div
        $('#tasklistsection').slideDown('slow');// slide down the project listing div
        $('#listenggtasks').removeClass('btn-danger').addClass('btn-success');
        $('#addenggtasks').removeClass('btn-success').addClass('btn-danger');

        var activityid=$('#activityid').val();

        var process=$('#process').val();

        var url='';

        if (process=='Project Setup'){
            url='../ProjectSetup/listtask';
        }
        else if (process=='Production'){
            url='../Products/listtask';
        }
        else if (process=='Logistics'){
            url='../Logistics/listtask';
        }
        else if (process=='Overheads'){
            url='../Overheads/listtask';
        }
        else {
            url='../Construction/listtask';
        }

        $.ajax({
            type: 'POST',
            url: url,
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {id:activityid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#taskitems').html(data.result);
                    $('#tasktable').show();
                }

                $('.preloader').hide();
            }
        });

    });

    $('#addenggtasks').click(function(){
        $('#tasklistsection').slideUp('slow');// slide down the project listing div
        $('#taskaddsection').slideDown('slow');// slide down the project listing div
        $('#addenggtasks').removeClass('btn-danger').addClass('btn-success');
        $('#listenggtasks').removeClass('btn-success').addClass('btn-danger');
    });

    $('#saveenggtask').click(function(){

        var error=0;

        $('.error').hide();

        if($('#taskname').val()=='')
        {
            $("#taskname").next("span").html('Enter Task Name').show('slow');
            error=1;
        }

        var activityid=$('#activityid').val();

        var process=$('#process').val();

        var url='';

        if (process=='Project Setup'){
            url='../ProjectSetup/taskcreate';
        }
        else if (process=='Production'){
            url='../Products/taskcreate';
        }
        else if (process=='Logistics'){
            url='../Logistics/taskcreate';
        }
        else if (process=='Overheads'){
            url='../Overheads/taskcreate';
        }
        else {
            url='../Construction/taskcreate';
        }

        if(error==0){

            $.ajax({
                type:'POST',
                url:url,
                beforeSend:function(){
                    $('#saveenggtask').attr("disabled", true);
                },
                dataType:'json',
                data: {taskname:$('#taskname').val(),activityid:activityid},
                success:function(data){

                    $('#addtaskform')[0].reset();
                    $('#listenggtasks').trigger('click');
                    $('#saveenggtask').attr("disabled", false);
                }

            });
        }

    });

});

$(document).on( "click", ".edittask_button", function(){
    var idval=$(this).val();
    $('#edittask'+idval).show();
    $('#savetaskbutton'+idval).show();
    $('#tasktext'+idval).hide();
    $('#edittask_button'+idval).hide();
});

$(document).on( "click", ".savetaskbutton", function(){
    var idval=$(this).val();

    var error=0;

    $('.error').hide();

    if($('#edittask'+idval).val()=='')
    {
        $('#edittask'+idval).next("span").html('Enter Name').show('slow');
        error=1;
    }

    var process=$('#process').val();

    var url='';

    if (process=='Project Setup'){
        url='../ProjectSetup/updatetask';
    }
    else {
        url='../ProjectSetup/updatetask';
    }

    if(error==0){
        $.ajax({
            type: 'POST',
            url: '../ProjectSetup/updatetask',
            beforeSend : function(){
                $('#savetaskbutton'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {taskid:idval,name:$('#edittask'+idval).val()},
            success: function(data){
                if(data.error='No')
                {
                    $('#edittask'+data.Id).hide();
                    $('#savetaskbutton'+data.Id).hide();
                    $('#tasktext'+data.Id).text($('#edittask'+data.Id).val()).show();
                    $('#edittask_button'+data.Id).show();
                }

                $('#savetaskbutton'+data.Id).attr("disabled", false);
            }
        });
    }
});

$(document).on( "click", ".deletetask_prosetup", function(){
    var idval=$(this).val();

    var r = confirm("Are you sure you want to delete this task?");

    if (r == true) {


        $.ajax({
            type: 'POST',
            url: '../ProjectSetup/deletetask',
            beforeSend : function(){
                $('#deletetask_prosetup'+idval).attr("disabled", true);
            },
            dataType: "json",
            data: {taskid:idval},
            success: function(data){
                if(data.error=='No')
                {
                    $('#taskrow'+data.Id).remove();
                }

                $('#deletetask_prosetup'+data.Id).attr("disabled", false);
            }
        });


    } else {
        return false;
    }

});

function getActivityname(id,type)

{

    var retval;

    $.ajax({

        type: 'POST',

        url: '../engineering/GetActivityname',

        async:false,

        data: {id:id,type:type},

        success: function(data){

            retval=data;

        }

    });

    return retval;

}
