$(document).on( "click", ".viewwbsestimatetask", function(){
    //$('.acc_container').slideUp();
    $('#wbs_scheduleactivity').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
    //$(this).toggleClass('active').next().slideDown();
    $('#Wbsestimatetasklist').addClass('active').next('.acc_container').slideDown();
    var projid = $('#selectedProjectId').val();
    var wrkactivityid= $(this).val();
    var activityid = $(this).attr('data-id');
    var workgrpid =  $('#selectedWorkgrouId').val();
    $('#workgroupid').val(workgrpid);
    $('#projeid').val(projid);
    $('#showprojectname').html(getScheduleProjectname(projid));
    //$('#showworkgroupname').html(getWorkgroupname(workgrpid));
	
	$.ajax({
        type: 'POST',
        url: '../projects1/WbsEstimateTaskList',
        beforeSend : function(){ 
            $('.preloader').show(); 
        },
        dataType: "json",
        data: {wrkactivityid:wrkactivityid,activityid:activityid}, 
        success: function(data){
            if(data.error=='No')
            {
                $('#wbsestimateactivities').html(data.result);
                $('#activityid').val(wrkactivityid);
            }
            else
            {
                alert(data.errortext);
            }
            $('.preloader').hide();
        }
    });
	
});

$(document).on( "click", "#addnewtask", function(){
    $('#addnewtaskbox').show();
    $('#wbsestimateactivities').hide();
});

$(document).on( "click", "#canceltaskname", function(){
    $('#addnewtaskbox').hide();  
    $('#wbsestimateactivities').show();
});

/*$(document).on('click','#cancelschedule_new',function(){
    var dataid= $(this).attr("data-id");
    if(dataid!=''){
       
       $.ajax({
        type: 'POST',
        url: '../projects1/WbsEstimateTaskList',
        beforeSend : function(){ 
            $('.preloader').show();
        },
        dataType: "json",
        data: {wrkactivityid:dataid},
        success: function(data){
            if(data.error=='No')
            {
                $('#wbsestimateactivities').html(data.result);
                $('#activityid').val(dataid);
            }
            else
            {
                alert(data.errortext);
            }
            $('.preloader').hide();
        }
    });
        
   }
});*/

$(document).on('click','#cancelschedule_new',function(){
    var dataid= $(this).attr("data-id");
    var dataiow= $(this).attr("data-iow");
    $('#workgroup').trigger('click');
    $('#listscheduleactivity'+dataiow).trigger('click');
});

$(document).on('click','#savetaskname',function(){
          var activityid = $('#activityid').val();
          var error=0;
          if($('#tasknamenew').val()=='')
          {
            $('#tasknamenew').next('span').html('Enter Task Name').show('slow');
            error=1;
          }
          
          if(error==0){
            $.ajax({
                type: 'POST',
                url: '../projects1/newtaskcreate',
                beforeSend : function(){

                },
                dataType: "json",
                data: {activityid:activityid,taskname:$('#tasknamenew').val()},
                success: function(data){
                    if(data.error=='No')
                    {
                     $('#addnewtaskbox').hide();
                     $('#wbsestimateactivities').show();
                     $('#tasknamenew').val('');
                     //$('.activitylist').append(data.result);
                     $(data.result).insertBefore('.activitylist');
                     //$('#wbsestimateactivities').html(data.result);
                    }
                    else
                    {
                     
                    }
                  //  $('#oldstructure').attr("disabled", true);
                }
            });
          }
});

$(document).on('click','#saveschedule_new',function(){

    $.ajax({
        type: 'POST',
        url: '../projects1/Schedulecreate',
        beforeSend : function(){
            $('#saveschedule_new').attr("disabled", true);

        },
        dataType: "json",
        data: $( "#scheduleform" ).serialize(),
        success: function(data){
            if(data.error=='No')
            {
               //$('.viewwbsestimatetask').trigger('click');
               $('#cancelschedule_new').trigger('click');
               $("#showschedule").show().delay(5000).fadeOut();
            }
            else
            {
                alert(data.errortext);
            }
            $('#saveschedule_new').attr("disabled", false);
        }
    });
    //return false;

});

$(document).on('click','#removetask',function(){
    var id = $(this).attr('data-id');
    var r = confirm("Are you sure you want to delete this task ?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../projects1/TaskDelete',
            dataType: "json",
            data: {id:id},
            success: function(data){
                if(data.error=='No')
                {
                   $('#activityrow'+id).remove();
                }
            }
        });
    }
    else {
        return false;
    }

});

$(document).on('click','.edittaskname',function(){
   var id = $(this).attr('data-id'); 
    
   $('#taskname'+id).hide();
   $('#taskname_edit'+id).show();
   $('#edittaskname'+id).hide();
   $('#savetaskname'+id).show();
   
});

$(document).on('click','.savetaskname',function(){
    var id = $(this).attr('data-id');
    var taskname = $('#taskname_edit'+id).val();
        $.ajax({
            type: 'POST',
            url: '../projects1/TaskEditName',
            dataType: "json",
            data: {id:id, taskname:taskname},
            success: function(data){
                if(data.error=='No')
                {
                   $('#taskname'+id).html(taskname);
                   $('#taskname_edit'+id).val(taskname);
                   $('#taskname'+id).show();
                   $('#taskname_edit'+id).hide();
                   $('#edittaskname'+id).show();
                   $('#savetaskname'+id).hide();
                }
            }
        });

});

function getScheduleProjectname(id)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../projects1/Getname',
        async:false,
        data: {id:id},
        success: function(data){
            retval=data;
        }
    });
    return retval;
}

$(document).on('blur','.bduration',function(){
    var identifier=$(this).attr('data-id');
    var cycleatotalb=0
    var cycleatotale=0
    $('#eduration'+identifier).val($(this).val() * 1);
    $(".bduration").each(function(){
        cycleatotalb=cycleatotalb+($(this).val()*1)
    });
    /*$(".eduration").each(function(){
        cycleatotale=cycleatotale+($(this).val()*1)
    });*/
    $('#cyclebud').text(cycleatotalb);
    $('#budcycle').val(cycleatotalb);
    //$('#cycleact').text(cycleatotale);
    //$('#actcycle').val(cycleatotale);
});