$(document).on( "click", ".listscheduleactivity", function(){
    $('#workgroup').removeClass('active').next().slideUp();
    $('#wbs_scheduleactivity').addClass('active').next('.acc_container').slideDown();
    var id=$(this).val();
    $('#selectedScheduleItem').val(id);
    $('#listscheduleact').trigger('click');
    $('#scheduleitemnamedisplay').html(getItemname(id));
});

$(document).on( "click", "#savescheduleactivities", function(){
    var actname = $('#scheduleactivitiesname').val();
    var unit = $('#scheduleactivitiesunit').val();
    var startDate = $('#scheduleactivitiesStartDate').val();
    var endDate = $('#scheduleactivitiesEndDate').val();
    var duration = $('#scheduleActivityDuration').val();
    var quantity = $('#scheduleactivitiesQuantity').val();
    console.log(startDate);
    if(actname == ''){
        $('#scheduleactivitiesname').next("span").html('Enter Activity Name').show('slow');
        return
    }
    if(unit == '')
    {
        $('#scheduleactivitiesunit').next("span").html('Enter Unit').show('slow');
        return 
    }
    if(startDate == '')
    {
        $('#scheduleactivitiesStartDate').next("span").html('Select Date').show('slow');
        return 
    }
    if(endDate == '')
    {
        $('#scheduleactivitiesEndDate').next("span").html('Select Date').show('slow');
        return 
    }
    if(quantity == '')
    {
        $('#scheduleactivitiesQuantity').next("span").html('Enter Quantity').show('slow');
        return 
    }
    $.ajax({
        type: 'POST',
        url: '../projects1/addscheduleactivities',
        beforeSend : function(){
            $('#savescheduleactivities').attr("disabled", true);
        },
        dataType: "json",
        data: {activityname:actname,activityunit:unit,startDate:startDate, endDate:endDate,duration:duration, quantity:quantity, projectId:$('#selectedProjectId').val(), itemId:$('#selectedScheduleItem').val()},
        success: function(data){
            if(data.error=='No')
            {
                $('#enggactivitiesform')[0].reset();
                $('#savescheduleactivities').attr("disabled", false);
                $('#listscheduleact').trigger('click');

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
                $('#savescheduleactivities').attr("disabled", false);
            }
            $('#savescheduleactivities').attr("disabled", false);
        }
    });

   
});
$(document).on('click','.editscheduleactivitybuttonv',function(){
    alert('dddd');
    var id=$(this).val();
    var type=$(this).attr('data-type');
    type = type.replace(/ +/g, "");
    $('#edit'+type+'activityname'+id).show();
    $('#edit'+type+'activityunit'+id).show();
    $('#edit'+type+'activityduration'+id).show();
    $('#edit'+type+'activitystartdate'+id).show();
    $('#edit'+type+'activityenddate'+id).show();
    $('#edit'+type+'activityquantity'+id).show();
    $('#edit'+type+'activitylag'+id).show();
    //$('#editenggprocess'+id).show();
    $("input#estimate"+id).removeAttr("disabled");
    $('#save'+type+'activitybutton'+id).show();
    $('#'+type+'activityname'+id).hide();
    $('#'+type+'activityunit'+id).hide();
    $('#'+type+'activityduration'+id).hide();
    $('#'+type+'activitystartdate'+id).hide();
    $('#'+type+'activityenddate'+id).hide();
    $('#'+type+'activityquantity'+id).hide();
    $('#'+type+'activitylag'+id).hide();
    $('#'+type+'process'+id).hide();
    $('#edit'+type+'activitybut'+id).hide(); 
});
    
    $(document).on('change','.editactivityenddate',function(){
    var id = $(this).attr('data-id');
    var startDate = $('#editactivitystartdate'+id).val();
    var endDate = $('#editactivityenddate'+id).val();
    //alert(endDate)
    if(endDate != '' && startDate != '')
    {
        var startDate1 = Date.parse(startDate);
        var endDate1 = Date.parse(endDate);
        var timeDiff = endDate1 - startDate1;
        daysDiff = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
        console.log(daysDiff);
        $('#editactivityduration'+id).val(daysDiff+1);
    }
    });
    
    $(document).on('change','.editactivitystartdate',function(){
       var id = $(this).attr('data-id');
       var startDate = new Date($('#editactivitystartdate'+id).val());
       var duration = $('#editactivityduration'+id).val();
       if(duration != '' && startDate != '')
        {
            var newdate = new Date(startDate).setDate(startDate.getDate() + (+duration) - 1);
            var endDate1 = new Date(newdate);
            var tempoMonth = (endDate1.getMonth() + 1);
            if (tempoMonth < 10) tempoMonth = '0' + tempoMonth;
            var tempoDate = (endDate1.getDate());
            if (tempoDate < 10) tempoDate = '0' + tempoDate;
            var endDate = endDate1.getFullYear() + '-' + tempoMonth + '-' + tempoDate;
            
            $('#editactivityenddate'+id).val(endDate);
        }
    });

    $(document).on('change','.editenggactivityduration',function(){
       var id = $(this).attr('data-id');
       var startDate = new Date($('#editactivitystartdate'+id).val());
       var duration = $('#editactivityduration'+id).val();
       if(duration != '' && startDate != '')
        {
            var newdate = new Date(startDate).setDate(startDate.getDate() + (+duration) - 1);
            var endDate1 = new Date(newdate);
            var tempoMonth = (endDate1.getMonth() + 1);
            if (tempoMonth < 10) tempoMonth = '0' + tempoMonth;
            var tempoDate = (endDate1.getDate());
            if (tempoDate < 10) tempoDate = '0' + tempoDate;
            var endDate = endDate1.getFullYear() + '-' + tempoMonth + '-' + tempoDate;
            
            $('#editactivityenddate'+id).val(endDate);
        }
    });
    
 /*   $('.editenggactivitylag').change(function(){
       var id = $(this).attr('data-id');
       var startDate = new Date($('#editactivitystartdate'+id).val());
       var endDate = new Date($('#editactivityenddate'+id).val());
       var lag = $('#editactivitylag'+id).val();
       if(lag != '' && startDate != '')
        {
            var newdate = new Date(startDate).setDate(startDate.getDate() + (+lag));                                                                                    
            var startDate1 = new Date(newdate);
            var tempoMonth = (startDate1.getMonth() + 1);
            if (tempoMonth < 10) tempoMonth = '0' + tempoMonth;
            var tempoDate = (startDate1.getDate());
            if (tempoDate < 10) tempoDate = '0' + tempoDate;
            var newstartDate = startDate1.getFullYear() + '-' + tempoMonth + '-' + tempoDate;
            
            var newdate = new Date(endDate).setDate(endDate.getDate() + (+lag));                                                                                    
            var endDate1 = new Date(newdate);
            var tempoMonth = (endDate1.getMonth() + 1);
            if (tempoMonth < 10) tempoMonth = '0' + tempoMonth;
            var tempoDate = (endDate1.getDate());
            if (tempoDate < 10) tempoDate = '0' + tempoDate;
            var newendDate = endDate1.getFullYear() + '-' + tempoMonth + '-' + tempoDate;
            
            $('#editactivitystartdate'+id).val(newstartDate);
            $('#editactivityenddate'+id).val(newendDate);
        }
    }); */
    
//});

$(document).on('click','.savescheduleactivitybutton',function(){
    var id =$(this).val();
    var type =$(this).attr('data-type');
    type = type.replace(/ +/g, "");
    var name= $('#edit'+type+'activityname'+id).val();
    var unit= $('#edit'+type+'activityunit'+id).val();
    var startdate= $('#edit'+type+'activitystartdate'+id).val();
    var enddate= $('#edit'+type+'activityenddate'+id).val();
    var quantity= $('#edit'+type+'activityquantity'+id).val();
    var lag= $('#edit'+type+'activitylag'+id).val();
    var duration= $('#edit'+type+'activityduration'+id).val();
    var cumqty= $('#cumulatedqty'+id).val();

    var error=0;
    $('.error').hide();
    if($('#edit'+type+'activityname'+id).val()=='')
    {
        $('#edit'+type+'activityname'+id).next("span").html('Enter Activity Name').show('slow');
        error=1;
    }
    if($('#edit'+type+'activityunit'+id).val()=='')
    {
        $('#edit'+type+'activityunit'+id).next("span").html('Enter Activity Unit').show('slow');
        error=1;
    }
    if($('#edit'+type+'activitystartdate'+id).val()=='')
    {
        $('#edit'+type+'activitystartdate'+id).next("span").html('Select Date').show('slow');
        error=1;
    }
    if($('#edit'+type+'activityenddate'+id).val()=='')
    {
        $('#edit'+type+'activityenddate'+id).next("span").html('Select Date').show('slow');
        error=1;
    }
    if($('#edit'+type+'activityquantity'+id).val()=='')
    {
        $('#edit'+type+'activityquantity'+id).next("span").html('Enter Quantity').show('slow');
        error=1;
    }
    if(Number(cumqty)>Number(quantity))
    {
        alert('Reported quantity is greater than budgeted quantity')
        error=1;
    }
    
   /* if($('#edit'+type+'activityduration'+id).val()=='')
    {
        $('#edit'+type+'activityduration'+id).next("span").html('Enter Duration').show('slow');
        error=1;
    } */

    if ($('#estimate'+id).is(':checked')){
        var estimate = 1;
    }
    else {
        var estimate = 0;
    }

    if(error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../projects1/Scheduleactivityupdate',
            beforeSend : function(){
                $('#savescheduleactivitybutton'+id).attr("disabled", true);
            },
            dataType: "json",
            data: {id:id,name:name,unit:unit,type:type,startdate:startdate,enddate:enddate, quantity:quantity,lag:lag,duration:duration},
            success: function(data){
                if(data.error=='No')
                {
                    $('#edit'+type+'activityname'+data.Id).hide();
                    $('#edit'+type+'activityunit'+data.Id).hide();
                    $('#edit'+type+'activityduration'+data.Id).hide();
                    $('#edit'+type+'activitystartdate'+data.Id).hide();
                    $('#edit'+type+'activityenddate'+data.Id).hide();                   
                    $('#edit'+type+'activitystartdate'+data.Id).val(data.Editstartdate);
                    $('#edit'+type+'activityenddate'+data.Id).val(data.Editenddate);                    
                    $('#edit'+type+'activityquantity'+data.Id).hide();
                    $('#edit'+type+'activitylag'+data.Id).hide();
                    $('#save'+type+'activitybutton'+data.Id).hide();
                    $('#'+type+'activityname'+data.Id).text(data.Name).show();
                    $('#'+type+'activityunit'+data.Id).text(data.Unit).show();
                    $('#'+type+'activityduration'+data.Id).text(data.Duration).show();
                    $('#'+type+'activitystartdate'+data.Id).text(data.Startdate).show();
                    $('#'+type+'activityenddate'+data.Id).text(data.Enddate).show();
                    $('#'+type+'activityquantity'+data.Id).text(data.Quantity).show();
                    $('#'+type+'activitylag'+data.Id).text(data.Lag).show();
                    $('#edit'+type+'activitybut'+data.Id).show();
                    $("input#estimate"+id).attr("disabled", true);
                    $('#save'+type+'activitybutton'+id).attr("disabled", false);
                    $('#listscheduleact').trigger('click');                  
                }
                $('#save'+type+'activitybutton'+id).attr("disabled", false);
            }
        });
    }
});

$(document).on('click','.deletescheduleactivity',function(){
    var id=$(this).val();
    var type =$(this).attr('data-type');
    var r = confirm("Are you sure you want to delete this Activity ?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../projects1/scheduleactivitydelete/',
            async:false,
            dataType:"json",
            data: {id:id,type:type},
            success: function(data){
                if(data.error=='No')
                {
                    $('#listscheduleact').trigger('click');
                }
                else {
                    //alert('Cannot delete this activity as it is used in Pricing and Scheduling');
                    alert(data.message);
                }
            }
        });
    }
});

$(document).on( "click", "#itemofwork", function(){
    if($('#selectedWorkgrouId').val()!='')
    {
        $('.acc_trigger').removeClass('active').next().slideUp();
        $('#itemofwork').addClass('active').next('.acc_container').slideDown();
        // var parents= getParentnames($('#selectedWorkgrouId').val())
        // $('#projectnamedisplay').html(parents.Project);
        // $('#workgroupnamedisplay').html(parents.Workgroup);
        // $('#selectedProjectId').val(parents.Project_Id);
        // $('#selectedWorkgrouId').val(parents.Workgroup_Id);
        $('#listscheduleact').trigger('click') ;
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
            url: '../activity/create',
            beforeSend : function(){
                $('#saveiow').attr("disabled", true);
            },
            dataType: "json",
            data: {Project_Id:$('#selectedProjectId').val(),Workgroup_Id:$('#selectedWorkgrouId').val(),IOW_Name:$('#IOW_Name').val(),IOW_Unit:$('#IOW_Unit').val(),IOW_Quantity:$('#IOW_Quantity').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#addiowform')[0].reset();
                    $('#listscheduleact').trigger('click');

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
    /*$( "#iowitems" ).sortable({

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
                url: '../activity/updatesort',
                data: {datavalue:updatedrows},
                dataType: "json",
                success: function(data){}
            });
        }

    }).disableSelection()*/

    $( "#iowitems" ).sortable({
        items: '.no',
        update:function( event, ui ) {
            //alert($(this).index());
            var updatedrows=[];
            $(this).closest('table').find('tbody tr').each(function (i) {
                var rowid=$(this).attr('data-id');
                var type=$(this).attr('data-type');
                var rowindex=$(this).index();
                updatedrows.push({
                    rowid: rowid,
                    type: type,
                    rowindex:rowindex
                })
            });
            $.ajax({
                type: 'POST',
                url: '../activity/updatesort',
                data: {datavalue:updatedrows},
                dataType: "json",
                success: function(data){}
            });
        }

    }).disableSelection();
    // list iow click
    $('#listscheduleact').click(function(){
        $('#scheduleactaddsection').slideUp('slow');// slide down the project listing div
        $('#wbsactivityrelation').slideUp('slow');
        $('#scheduleactivitylistsection').slideDown('slow');// slide down the project listing div
        $('#listscheduleact').removeClass('btn-danger').addClass('btn-success');
        $('#addscheduleact').removeClass('btn-success').addClass('btn-danger');
        $('#wbs_schedule_relation_new').removeClass('btn-success').addClass('btn-danger');

        // console.log($('#selectedProjectId').val());
        $.ajax({
            type: 'POST',
            url: '../projects1/Listscheduleactivities',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectId:$('#selectedProjectId').val(), itemId:$('#selectedScheduleItem').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#scheduleactivityitems').html(data.result);
                    $('#ganttchartshow1').html(data.gantt);
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
    $('#addscheduleact').click(function(){
        $('#scheduleactivitylistsection').slideUp('slow');// slide down the project listing div
        $('#scheduleactaddsection').slideDown('slow');// slide down the project listing div
        $('#addscheduleact').removeClass('btn-danger').addClass('btn-success');
        $('#listscheduleact').removeClass('btn-success').addClass('btn-danger');

        //  $.ajax({
        //     type: 'POST',
        //     url: '../activity/AddIowActivities',
        //     beforeSend : function(){
        //         $('.preloader').show();
        //     },
        //     dataType: "json",
        //     data: {Workgroup_Id:$('#selectedWorkgrouId').val(),worktypeid:$('#selectedWorktypeId').val(),activityname:$('#searchenggactname').val()},
        //     success: function(data){
        //         if(data.error=='No')
        //         {
        //             $('#deletedactivityitems').html(data.result);
        //         }
        //         else
        //         {
        //             alert(data.errortext);
        //         }
        //         $('.preloader').hide();
        //     }
        // });

    });

    $('#enggactsearch').click(function(){

        $('#addscheduleact').trigger('click')

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
            url: '../activity/update',
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



$(document).on('click','.deleteproactivitybutton',function(){
    var actid=$(this).val();
    // var type=$('#process_id').val();
    
    console.log(actid);
    // console.log(type);
    $('#activityrow'+actid).remove();

});
$(document).on('click','.saveproactivitybutton',function(){
    // console.log(id);
   
   $.ajax({
            type: 'POST',
            url: '../activity/saveactivities', 
            beforeSend : function(){
                $('.saveproactivitybutton').attr("disabled", true);
            },
            dataType: "json",
            // Workgroup_Id:$('#selectedWorkgrouId').val()
            data: $('#proactivitysaveform').serialize(),
            success: function(data){
                if(data.error=='No')
                {
                    console.log("success request");
                    $('#listscheduleact').trigger('click');
                    $('.saveproactivitybutton').attr("disabled", false);
                    $('.succmsg').html("Activities saved successfully").fadeIn('slow');
                    $('.succmsg').delay(5000).fadeOut('slow');
                }
            }
        });
    console.log("successfully saved");

});
$(document).on('click','.adddeletedactivitybutton',function(){
    var estimateid=$(this).val();
    var processid=$(this).attr('data-id');
    var workgp_Id = $('#IOWWorkgroupId2').val();
    // var estimateid = $('#estimateid'+processid+activityid).val();
    // alert('#addactivityrow'+estimateid);
   
    $.ajax({
            type: 'POST',
            url: '../activity/savedeletedtasks',
            // beforeSend : function(){
            //     $('#addprotaskbutton').attr("disabled", true);
            // },
            dataType: "json",
            // Workgroup_Id:$('#selectedWorkgrouId').val()
            data: {processid:processid, WgId:workgp_Id,estimateid:estimateid },
            success: function(data){
                if(data.error=='No')
                {
                    console.log("success request");
                    // $('#listtasks').trigger('click');
                    // $('addprotaskbutton').attr("disabled", false);
                }
            }
        });
        
    // $('#taskrow1'+taskid).remove();
    $('#addactivityrow'+estimateid).remove();

});

$(document).on('click','.editiowactivitybutton',function(){
    var id=$(this).val();
    $('#editiowactivityname'+id).show();
    $('#editiowactivityunit'+id).show();
    $('#saveiowactivitybtn'+id).show();
    $('#iowactivityname'+id).hide();
    $('#iowactivityunit'+id).hide();
    $('#editiowactivitybutton'+id).hide();
    $("input#checkestimate"+id).removeAttr("disabled");
});

$(document).on('click','.saveiowactivitybtn',function(){
    var id =$(this).val();
    var name= $('#editiowactivityname'+id).val();
    var unit= $('#editiowactivityunit'+id).val();
    var type= $(this).attr('data-type');

    var error=0;
    $('.error').hide();
    if($('#editiowactivityname'+id).val()=='')
    {
        $('#editiowactivityname'+id).next("span").html('Enter Activity Name').show('slow');
        error=1;
    }
    if($('#editiowactivityunit'+id).val()=='')
    {
        $('#editiowactivityunit'+id).next("span").html('Enter Activity Unit').show('slow');
        error=1;
    }
    if (type=='new'){
        var estimate=$('#editiowactivityestimate'+id).val();
    }
    else {
        if ($('#checkestimate'+id).is(':checked')){
            var estimate = 1;
        }
        else {
            var estimate = 0;
        }
    }
    /*if ($('#checkestimate'+id).is(':checked')){
        var estimate = 1;
    }
    else {
        var estimate = 0;
    }*/

    if(error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../activity/UpdateIowActivity',
            beforeSend : function(){
                $('#saveiowactivitybtn').attr("disabled", true);
            },
            dataType: "json",
            data: {id:id,name:name,unit:unit,estimate:estimate},
            success: function(data){
                if(data.error=='No')
                {
                    $('#editiowactivityname'+data.Id).hide();
                    $('#editiowactivityunit'+data.Id).hide();
                    $('#saveiowactivitybtn'+data.Id).hide();
                    $('#iowactivityname'+data.Id).text(data.Name).show();
                    $('#iowactivityunit'+data.Id).text(data.Unit).show();
                    $('#editiowactivitybutton'+data.Id).show();
                    $("input#checkestimate"+id).attr("disabled", true);
                }
                $('#saveiowactivitybtn').attr("disabled", false);
            }
        });
    }
});

$(document).on('click','.deleteiowactivity',function(){
    var actid=$(this).val();
    $('#iowactivitiesrow'+actid).remove();
});

$(document).on('click','.iowactdelbtn',function(){
    var id=$(this).val();
    var actid=$(this).attr('data-id');
    var r = confirm("Are you sure you want to delete this Activity?");
    if (r == true) {
        $.ajax({
            type: 'POST',
            url: '../activity/deleteiowactivity',
            beforeSend : function(){
                $('#iowactdelbtn'+id).attr("disabled", true);
            },
            dataType: "json",
            data: {actid:id},
            success: function(data){
                if(data.error=='No')
                {
                    $('#iowactivitiesrow'+id).remove();
                }
                else
                {
                    alert(data.errortext);
                    $('#iowactdelbtn'+id).attr("disabled", false);
                }
            }
        });
    }

});

/*$(document).on('click','.saveiowactivitybutton',function(){
    $.ajax({
        type: 'POST',
        url: '../activity/saveiowactivities',
        beforeSend : function(){
            $('.saveproactivitybutton').attr("disabled", true);
        },
        dataType: "json",
        data: $('#iowactivitysaveform').serialize(),
        success: function(data){
            if(data.error=='No')
            {
                $('#listscheduleact').trigger('click');
                $('.saveiowactivitybutton').attr("disabled", false);
                $('.succmsg').html("Activities saved successfully").fadeIn('slow');
                $('.succmsg').delay(5000).fadeOut('slow');
            }
        }
    });

}); */

$(document).on('click','.addiowactivity',function(){
    var actid=$(this).val();
    var processid=$(this).attr('data-id');
    var wbsid=$('#IOWWorkgroupId2').val();
    var worktypeid=$('#selectedWorktypeId').val();
    var activityunit=$('#activunit'+actid).val();
    var projectid=$('#selectedProjectId').val();
    var type=$('#type'+actid).val();
    if (type=='new'){
        var estimate=$('#estimate'+actid).val();
    }
    else {
        if ($('#checkestimate'+actid).is(':checked')){
            var estimate = 1;
        }
        else {
            var estimate = 0;
        }
    }
    var error=0;
    if($('#iowactivityname'+actid).val()!='' && ActivityNameExist($('#iowactivityname'+actid).val(),wbsid)=='Yes')
    {
        $('#iowactivityname'+actid).next("span").html('Activity Name Exists').show('slow');
        error=1;
    }
    var activityname=$('#iowactivityname'+actid).val();
    if(error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../activity/addiowactivity',
            beforeSend : function(){
                $('.addiowactivity'+actid).attr("disabled", true);
            },
            dataType: "json",
            data: {actid:actid,processid:processid,wbsid:wbsid,worktypeid:worktypeid,activityunit:activityunit,estimate:estimate,projectid:projectid,type:type,activityname:activityname},
            success: function(data){
                if(data.error=='No')
                {
                    console.log("success request");
                    $('#addiowactivityrow'+actid).remove();
                    // $('#listtasks').trigger('click');
                    // $('addprotaskbutton').attr("disabled", false);
                }
            }
        });
    }



});

$(document).on('click','#wbs_schedule_relation_new',function(){
    $('#wbsactivityrelation').slideDown('slow');// slide down the project listing div
    $('#listscheduleact').removeClass('btn-success').addClass('btn-danger');
    $('#addscheduleact').removeClass('btn-success').addClass('btn-danger');

    $('#wbs_schedule_relation_new').removeClass('btn-danger').addClass('btn-success');

    $.ajax({
        type: 'POST',
        url: '../projects1/activityrelation',
        beforeSend : function(){
            $('#workgroupsearch').attr("disabled", true);
            $('.preloader').show();
        },
        dataType: "json",
        data: {projectid:$('#selectedProjectId').val(),workgroupname:'',structureid:'',scheduleid:$('#selectedScheduleItem').val()},
        success: function(data){
            if(data.error=='No')
            {
                $('#scheduleactivitylistsection').hide();
                $('#activity_relation_content').html(data.result);
                $('#wbsactivityrelation').show();
                $('#structure_relation_list').html(data.relationList);
                $('#structure_relation_list').show();
                $('#activity_relation_list').hide();
            }
            else
            {
                alert(data.errortext);
            }

            $('#workgroupsearch').attr("disabled", false);
            $('.preloader').hide();
        }
    });

});

/* DEACTIVATED: duplicate .save_relation_new handler — reads wrong field IDs (missing -new suffix,
   so firstItem is always empty and validation returns immediately with no visible feedback) and
   posts to wrong controller (projects1 instead of projectsmain).
   Active handler is in projectsmain/_schedulerelation.js → ../projectsmain/saverelation
$(document).on('click','.save_relation_new',function(){
    // var workid=$(this).val();
    var firstItem = $('#schedule_item_first').val();
    var firstActivity = $('#schedule_activity_first').val();
    var secondItem = $('#schedule_item_second').val();
    var secondActivity = $('#schedule_activity_second').val();
    var relationType = $('#relation_type').val();
    var lag = $('#lag').val();
    //validation
    if(firstItem == '')
    {
        $('#first_item_error').show();
        return;
    }
    else
    {
        $('#first_item_error').hide();
    }
    if(firstActivity == '')
    {
        $('#first_activity_error').show();
        return;
    }
    else
    {
        $('#first_activity_error').hide();
    }
    if(secondItem == '')
    {
        $('#second_item_error').show();
        return;
    }
    else
    {
        $('#second_item_error').hide();
    }
    if(secondActivity == '')
    {
        $('#second_avtivity_error').show();
        return;
    }
    else
    {
        $('#second_activity_error').hide();
    }
    if(relationType == '')
    {
        $('#relation_error').show();
        return;
    }
    else
    {
        $('#relation_error').hide();
    }

        $.ajax({
            type: 'POST',
            url: '../projects1/saverelation',
            beforeSend : function(){
                // $(this).attr("disabled", true);
                $('.save_relation').attr("disabled", true);
            },
            dataType: "json",
            data: {lag:lag, firstItem: firstItem, firstActivity: firstActivity, secondItem: secondItem, secondActivity: secondActivity, relationType: relationType, projectId: $('#selectedProjectId').val(), structureid:$('#mode-edit').val() },
            success: function(data){
                if(data.error=='No')
                {
                    //$('#wbs_schedule_relation').trigger('click');
                    $('#structure_relation_list').html(data.relationList);
                    $('#wbs_schedule_relation_new').trigger('click');
                }
                else if(data.mode=='Edit')
                {
                    $('#structure_relation_list').html(data.relationList);
                }
                else
                {
                    alert(data.errortext);
                }

                $('.save_relation_new').attr("disabled", false);
            }
        });

});
*/

$(document).on('change','#relation_type',function(){
    var lag = $('#relation_type').val();
    //if(lag==1 || lag==3){
       $('#lag').show();
    /*}
    else{
      $('#lag').val(''); 
      $('#lag').hide(); 
    }*/
});

function getItemname(id)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../projects1/GetItemName',
        async:false,
        dataType: "json",
        data: {id:id},
        success: function(data){
            retval=data.itemName;
        }
    });
    return retval;
}
function ActivityNameExist(name)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../activity1/CheckActivityname',
        async:false,
        data: {name:name},
        success: function(data){
            retval=data;
        }
    });
    return retval;

}
