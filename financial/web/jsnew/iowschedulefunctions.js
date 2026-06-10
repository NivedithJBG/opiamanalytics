/**
 * Created by SolmindsDelli5 on 08-05-2018.
 */
$(document).on( "click", ".iowschschedulebtn", function(){
    $('#iowschactivity').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
    $('#iowscheduleitems').addClass('active').next('.acc_container').slideDown();
    var projid = $('#selectedProjectId').val();
    var activityid= $(this).val();
    var processid = $(this).attr('data-type');
    var wbsactid = $(this).attr('data-id');
    var workgrpid =  $('#selectedWorkgrouId').val();
    $('#iowwbsid').val(workgrpid);
    $('#iowprojeid').val(projid);
    $('#iowshowprojectname').html(getScheduleProjectname(projid));
    $('#iowshowworkgroupname').html(getWorkgroupname(workgrpid));
    $('#iowscheduleactivities').show();
    $('#iowschedulelistiow').hide();
    var retval;
    $.ajax({
        type: 'POST',
        url: '../projects/Getiowschedulefrom',
        async:false,
        data: {projectid:projid,processid:processid,wbsid:workgrpid,activityid:activityid,wbsactid:wbsactid},
        success: function(data){
            retval=data;
        }
    });
    $('#iowscheduleactivities').html(retval);
});
