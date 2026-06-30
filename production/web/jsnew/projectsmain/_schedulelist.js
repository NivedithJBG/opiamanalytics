$(document).on( "click", ".view_wbsSchedule", function(){
    /*$('.acco-three input[type=radio]').trigger('click');
    var id= $(this).attr('data-id');
    $('#workgroup_name').html('<span class="icon-time1"></span> Schedule');
    $('#dispprojectname').html(getProjectname(id)); 
    $('#dispprojectname_schedule').html(getProjectname(id));
    $('#wbs_schedule_block').show();
    $('#wbs_estimate_block').hide();
    $('#selectedProjectId').val(id);
    $('#wbs-schedule-header').show();
    $('#projectnameScheduleWBS').show();*/
    $('#scheduleactpage').hide();
    $('#wbs_schedule_block').show();
    $('#wbs-schedule-header').show();
    $('#relation-tabscreen').hide();
    $('#mode-edit').val('');
    $('#listwbs_schedule').trigger('click');
    $('#cancelschedulegroup').trigger('click');
    $('.close-scheduleactvty').trigger('click');
    
    
});

$(document).on( "click", ".reltnmain", function(){
    $('.close-schedulerelatn').attr("data-id","0");
});

$(function(){
    $('#listwbs_schedule').click(function(){

        var structureid = $('#mode-edit').val();
        if(structureid!=''){
          var structureid = $('#mode-edit').val();
        }
        else{
          var structureid = 0;
        }
        $.ajax({
            type: 'POST',
            url: '../projectsmain/searchscheduleitem',
            beforeSend : function(){
                $('#workgroupsearch').attr("disabled", true);
                $('#Promain-preloader-Schedulewbs').show();
            },
            dataType: "json",
            data: {projectid:$('#selectedProjectId').val(),workgroupname:'',structureid:structureid},
            success: function(data){
                if(data.error=='No')
                {  //athira
                    //$('#wbsstructureitems').show();
                    //$('#ganttchartshow').show();
                    $('#scheduleactpage').hide();
                    $('#addschedule_item').show();
                    $('#ganttchartshows').html(data.gantt);	
                    $('#ganttchartshow').html(data.gantt); 
                    $('#listworkgroup-Schedul-data').html(data.result);
                    //$('#oldstructure').html(data.names);
                    $('#selectedProjectId').val(data.projectID);
                    $('#dispprojectname_schedule').html(getProjectname($('#selectedProjectId').val()));
                    $('#projectnameScheduleWBS').show();
                }
                else if(data.error=='No project')
                {
                    $('#addschedule_item').hide();
                    $('#ganttchartshow').html(data.gantt);  
                    $('#listworkgroup-Schedul-data').html(data.result);
                }
                else if(data.mode=='Edit')
                {
                    $('#listworkgroup-Schedul-data').html(data.result);
                    //$('#oldstructure').html(data.names);
                }
                else
                {
                    alert(data.errortext);
                }

                $('#accordionprojindex').removeClass('acco-one-active');
                $('#accordionprojindex').removeClass('acco-two-active');
                $('#accordionprojindex').removeClass('acco-three-active');
                $('#accordionprojindex').removeClass('acco-four-active');
                $('#accordionprojindex').addClass('acco-five-active');

                $('#workgroupsearch').attr("disabled", false);
                $('#Promain-preloader-Schedulewbs').hide();
            }
        });

    });

    $('#savescheduleitem').click(function(){
        var error=0;
        if($('#scheduleitem_name').val()=='')
        {
            $('#scheduleitem_name').next("span").html('Enter Item Name').show('slow')
            error=1;
        }
       
        if(error==0)
        {
            $.ajax({
                type: 'POST',
                url: '../projectsmain/addwbschedulegroup',
                beforeSend : function(){
                    $('#savescheduleitem').attr("disabled", true);
                },
                dataType: "json",
                //data: {Project_Id:$('#selectedProjectId').val(),schedulegroupname:$('#schedulegroup_name').val(), scheduleitemname:$('#scheduleitem_name').val(), structureid:$('#savescheduleitem').val()},
                data: {Project_Id:$('#selectedProjectId').val(),schedulegroupname:$('#schedulegroup_name').val(), scheduleitemname:$('#scheduleitem_name').val()},
                success: function(data){

                    if(data.error=='No')
                    {
                        $('#addscheduleitemform')[0].reset();
                        $('#listscreenshow').show();
                        $('#cancelschedulegroup').trigger('click');
                        $('#listwbs_schedule').trigger('click');
                     
                    }
                    else
                    {
                        $("#scheduleitem_name").next("span").html(data.Message).show('slow');
                        $('#savescheduleitem').attr("disabled", false);
                    }
                    $('#savescheduleitem').attr("disabled", false);
                }
            });
        }
    });

    $(document).on('click','.editscheduleitemgroupbuttonq',function(){
        var idval=$(this).attr('data-v');
        $('#schedulegroupname'+idval).hide();
        $('#scheduleitemname'+idval).hide();
        $('#editscheduleitemgroupbutton'+idval).hide();
        $('#editscheduleitemname'+idval).show();
        $('#editscheduleitemname'+idval).focus();
        $('#savewbsscheduleitembutton'+idval).show();
    });

    $(document).on('click','.savewbsscheduleitembutton',function(){
        var idval=$(this).attr('data-v');
        var error=0;
        $('.error').hide();
        if($('#editscheduleitemname'+idval).val()=='')
        {
            $('#editscheduleitemname'+idval).next("span").html('Enter Name').show('slow');
            error=1;
        }
        if(error==0)
        {
            $.ajax({
                type: 'POST',
                url: '../projectsmain/updatescheduleitem',
                beforeSend : function(){
                    $('#savewbsscheduleitembutton'+idval).attr("disabled", true);
                },
                dataType: "json",
                data: {itemid:idval,Itemname:$('#editscheduleitemname'+idval).val()},
                success: function(data){
                    if(data.error=='No')
                    {
                        //$('#schedulegroupname'+data.Id).show();
                        $('#scheduleitemname'+data.Id).show();
                        $('#editscheduleitemgroupbuttonq'+data.Id).show();
                       // $('#editschedulegroupname'+data.Id).hide();
                        $('#editscheduleitemname'+data.Id).hide();
                        $('#savewbsscheduleitembutton'+data.Id).hide();
                        $('#editschedulegroupname'+data.Id).val(data.groupName);
                        $('#editscheduleitemname'+data.Id).text(data.itemName);
                        $('#listwbs_schedule').trigger('click');
    
                    }
                    else
                    {
                        alert(data.errortext);
                    }
    
                    $('#savewbsscheduleitembutton'+data.Id).attr("disabled", false);
                }
            });
        }
    });

    $(document).on('click','.deletewbsscheduleitembutton',function(){
        var workid=$(this).attr('data-v');
        var r = confirm("Are you sure you want to delete this Schedule Item?");
        if (r == true) {
            $.ajax({
                type: 'POST',
                url: '../projectsmain/deletescheduleitem',
                beforeSend : function(){
                    $('#deletewbsscheduleitembutton'+workid).attr("disabled", true);
                },
                dataType: "json",
                data: {itemId:workid},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#scheduleitemrow'+data.Id).remove();
                    }
                    else
                    {
                        alert(data.errortext);
                    }
    
                    $('#deletewbsscheduleitembutton'+data.Id).attr("disabled", false);
                }
            });
        }
    });

    $('#cancelschedulegroup').click(function(){
        $('#addscheduleitemform')[0].reset();
        $(this).parents('.tab').removeClass('add-form-active');
        $('#listscreenshow').show();
        $('#listwbs_schedule').trigger('click');
    });
});

// sort order in project schedule


// $(function() {
// $( "#listworkgroup-Schedul-data" ).sortable({ 
//         placeholder: "ui-state-highlight",
//         helper:'clone',
        
//         update:function( event, ui ) {  
//             //alert($(this).index());

//             var updatedrows=[];
//             $('.datalists').each(function() {
//                 var rowid=$(this).attr('data-id');
//                 var type=$(this).attr('data-type');
//                 var rowindex=$(this).index();
                
//                 updatedrows.push({
//                     rowid: rowid,
//                     type: type,
//                     rowindex:rowindex
//                 })
//             });
//             $.ajax({
//                 type: 'POST',
//                 url: '../projectsmain/updateitemlistsort',
//                 data: {datavalue:updatedrows},
//                 dataType: "json",
//                 success: function(data){}
//             });
//         }

//     }).disableSelection();
// });

function getProjectname(id)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../projectsmain/getname',
        async:false,
        data: {id:id},
        success: function(data){
            retval=data;
        }
    });
    return retval;
}


$(document).on( "click", ".viewwbsestimatetasks", function(){ 
    //$('.acc_container').slideUp();
       //$('#wbs_scheduleactivity').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
    //$(this).toggleClass('active').next().slideDown();
       //$('#Wbsestimatetasklist').addClass('active').next('.acc_container').slideDown();

    //var projid = $('#selectedProjectId').val();
     var projid =$(this).attr('data-p'); 
    //alert (projid);
    var wrkactivityid= $(this).attr('data-q');    
    var activityid = $(this).attr('data-id');
    var workgrpid =  $('#selectedWorkgrouId').val();
  
    $('#workgroupid').val(workgrpid);
    $('#projeid').val(projid);
    $('#showprojectname').html(getScheduleProjectname(projid));
    //$('#showworkgroupname').html(getWorkgroupname(workgrpid));
    
    $.ajax({
        type: 'POST',
        url: '../projectsmain/wbsestimatetasklists',
        beforeSend : function(){ 
            $('.preloader').show();
        },
        dataType: "json",
        data: {wrkactivityid:wrkactivityid,activityid:activityid},
        success: function(data){
            if(data.error=='No')
            {
                $("#schedule-allocate-body-one").hide();
                $("#scheduleactpage").hide();
                $("#tasklistt").show();
                
                $('#wbsestimateactivities').html(data.result);
                $('#activityid').val(wrkactivityid);
                $('.tastrow').each(function(){ calcTaskDuration($(this)); });
            }
            else
            {
                alert(data.errortext);
            }
            $('.preloader').hide();
        }
    });
    
});

function getScheduleProjectname(id)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../projectsmain/getnames',
        async:false,
        data: {id:id},
        success: function(data){
            retval=data;
        }
    });
    return retval;
}

$(document).on( "click", "#addnewtask", function(){ 
    $('#addnewtaskbox').show();
    //$('#wbsestimateactivities').hide();
});

$(document).on( "click", "#canceltaskname_new", function(){
    $('#addnewtaskbox').hide();  
    $("#scheduleactpage").hide();
    //$('#wbsestimateactivities').show();
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
                url: '../projectsmain/newtaskcreate',
                beforeSend : function(){

                },
                dataType: "json",
                data: {activityid:activityid,taskname:$('#tasknamenew').val()},
                success: function(data){
                    if(data.error=='No')
                    {
                     //$('#addnewtaskbox').hide();
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
            url: '../projectsmain/taskeditname',
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


$(document).on('click','#removetask',function(){ 
    var id = $(this).attr('data-id');
    var r = confirm("Are you sure you want to delete this task ?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../projectsmain/taskdelete',
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


$(document).on('keyup','#cycle_qty',function(){ 
    //no_of_cycles = Math.ceil(parseFloat($('#est_qty').val()) / parseFloat($(this).val()));
    no_of_cycles = parseFloat(parseFloat($('#est_qty').val()) / parseFloat($(this).val())).toFixed(2);
    $('#no_of_cycles').val(no_of_cycles);
});


$(document).on('click','#saveschedule_new',function(){ //alert ("hi66");

    var error=0;
    $('.error').hide();
    
    if(error==0)
    {

        $.ajax({
            type: 'POST',
            url: '../projectsmain/schedulecreate',
            beforeSend : function(){
                $('#saveschedule_new').attr("disabled", true);

            },
            dataType: "json",
            data: $( "#scheduleform" ).serialize(),
            success: function(data){
                if(data.error=='No')
                {
                    $('#tasklistt').hide();
                    $('#schedule-allocate-body-one').show();
                    $('#scheduleactpage').show();
                    $('#listscheduleact').trigger('click');
                }
                else
                {
                    alert(data.errortext);
                }
            },
            error: function(xhr){
                alert('Save failed: ' + xhr.responseText.substring(0, 300));
            },
            complete: function(){
                $('#saveschedule_new').attr("disabled", false);
            }
        });

    }
   /*  else{
        alert('Fields cannot be empty!')
        return false;
    } */
    //return false;

});


$(document).on('click','#cancelschedule_new',function(){ 
    var dataid= $(this).attr("data-id");
    var dataiow= $(this).attr("data-iow");
    $('#tasklistt').hide();
    $('#schedule-allocate-body-one').show();
    $('#scheduleactpage').show();
});

/*var cycle_unit = 0;
$(document).on('change','.cycle_type',function(){ 
    cycle_type = $('input[name="cycle_type"]:checked').val();
    if(!cycle_unit) cycle_unit = $('#cycle_units').val();

    if(cycle_type == 'fixed_qty'){
        $('#cycle_units').val(cycle_unit);
        $('#cycle_units').removeAttr('readonly');
        $('#cycle_unit_type').removeAttr('readonly');
    }
    else{    
        cycle_unit = $('#cycle_units').val();
        $('#cycle_units').val('0');
        $('#cycle_units').attr('readonly','readonly');
        $('#cycle_unit_type').attr('readonly','readonly');
    }
});*/

/* DEACTIVATED: superseded by calcTaskDuration function.
   taskduration_edit is readonly so blur only fires programmatically.
   data-id and #eduration{n} no longer exist in the current form structure.
$(document).on('blur','.taskduration_edit',function(){
    var identifier=$(this).attr('data-id');
    var cycleatotalb=0
    var cycleatotale=0
    $('#eduration'+identifier).val($(this).val() * 1);
    $(".taskduration_edit").each(function(){
        cycleatotalb=cycleatotalb+($(this).val()*1)
    });
    /*$(".eduration").each(function(){
        cycleatotale=cycleatotale+($(this).val()*1)
    });* /
    $('#cyclebud').text(cycleatotalb);
    $('#budcycle').val(cycleatotalb);
    //$('#cycleact').text(cycleatotale);
    //$('#actcycle').val(cycleatotale);
});
*/

$(document).on('click','#tasknewrow',function(){

    var numItems = $('.tastrow').length;
    var key = numItems + 1;

    $('#activitytasktable .tastrow:last').after(
        '<tr class="tastrow" id="activityrow'+key+'">' +
        '<td><input type="hidden" name="tasknewid[]" value=""><input type="text" class="form-control taskname_edit" name="taskname[]" value=""></td>' +
        '<td></td>' +
        '<td><input type="number" step="0.001" class="form-control task-productivity-val" name="task_productivity_val[]" value=""></td>' +
        '<td><input type="number" step="0.001" class="form-control" name="task_qty[]" value=""></td>' +
        '<td><input type="number" step="0.001" min="0.001" class="form-control task-resource-units-val" name="task_resource_units[]" value="1"></td>' +
        '<td><input type="number" class="form-control taskduration_edit" name="taskduration[]" value="" readonly style="background-color:#e9ecef;"></td>' +
        '<td style="text-align:center;"><a class="btn btn-danger icon-times taskremoverow" data-id="'+key+'" id="taskremoverow'+key+'" title="Remove Task"></a></td>' +
        '</tr>'
    );

});

function calcTaskDuration(row) {
    var qty      = parseFloat(row.find('[name="task_qty[]"]').val()) || 0;
    var prod     = parseFloat(row.find('.task-productivity-val').val()) || 0;
    var resUnits = parseFloat(row.find('.task-resource-units-val').val()) || 1;

    if (prod > 0 && qty > 0 && resUnits > 0) {
        row.find('.taskduration_edit').val((qty / prod / resUnits).toFixed(4));
    }

    var total = 0;
    $('.taskduration_edit').each(function(){ total += parseFloat($(this).val()) || 0; });
    $('#cyclebud').text(total.toFixed(4));
    $('#budcycle').val(total.toFixed(4));
}

$(document).on('input', '[name="task_qty[]"], [name="task_productivity_val[]"], [name="task_resource_units[]"]', function(){
    calcTaskDuration($(this).closest('tr'));
});

$(document).on('click','.taskremoverow',function(){

    var id=$(this).attr('data-id');

    $('#activityrow'+id).remove();

    var cycleatotalb=0
    $(".taskduration_edit").each(function(){
        cycleatotalb=cycleatotalb+($(this).val()*1)
    });
    /*$(".eduration").each(function(){
        cycleatotale=cycleatotale+($(this).val()*1)
    });*/
    $('#cyclebud').text(cycleatotalb.toFixed(4));
    $('#budcycle').val(cycleatotalb.toFixed(4));

});