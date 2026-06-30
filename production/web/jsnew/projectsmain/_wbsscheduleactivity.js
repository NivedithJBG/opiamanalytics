$(document).on( "click", ".listscheduleactivity", function(){
    //$('.schedule_act_list').trigger('click');
    $('#wbs_schedule_block').hide();
    $('#scheduleactpage').show();
    var id=$(this).attr('data-v');
    sessionStorage.setItem("schdid", id);
    $('#selectedScheduleItem').val(id);
    $('#wbs_schedule_relation_newid').val(id);
    $('#scheduleitemnamedisplay').html(getItemname(id));
    $('#listscheduleact').trigger('click');
    $('#projectnameScheduleActivity').show();
});


$(document).on( "click", ".close-scheduleactvty", function(){
    $('#scheduleactpage').hide();
    $('#listscreenshow').show();
    $('#wbs_schedule_block').show();
    $('#wbs_schedule_block').show();
});

$(document).on( "click", ".reltnmain1", function(){
    $('.close-schedulerelatn').attr("data-id","1");
});

$(function(){ 
    const startDate  =$('#scheduleactivitiesStartDate').val();
    const endDate    =$('#scheduleactivitiesEndDate').val();
    getduration(startDate,endDate);

    /*$(document).on('change','.date_field', function(){
    var startDates  =$('#scheduleactivitiesStartDate').val();
    var endDates    =$('#scheduleactivitiesEndDate').val();
    getduration(startDates,endDates);
    });*/
    
    /*$(document).on('focus','.date_field',function(){
        $(this).datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
    })
*/
    $('#listscheduleact').click(function(){
        $('#ScheduleActivity-Relation-body').hide();
        $('#ScheduleActivity-main-body').show();
        $.ajax({
            type: 'POST',
            url: '../projectsmain/listscheduleactivities',
            beforeSend : function(){
                $('#Promain-preloader-ScheduleActivity').show();
            },
            dataType: "json",
            data: {projectId:$('#selectedProjectId').val(), itemId:$('#selectedScheduleItem').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#actlistingshow').show();
                    $('#schedule-activity-header').show();
                    $('#scheduleactivityitems').html(data.result);
                    $('#ganttchartshow').html(data.gantt);

                    //highlight_holidays = ["1-8-2023", "2-8-2023", "8-8-2023", "21-8-2023"];
                    highlight_holidays = data.holiday_arr;
                    holiday_weeks      = data.holiday_week_arr;

                    // Initialize Holiday datepicker
                    $('.holidayAppliedDatepicker').datepicker({
                        beforeShowDay: function(date){
                            var month = date.getMonth()+1;
                            var year = date.getFullYear();
                            var day = date.getDate();
                            var newdate = day+"-"+month+'-'+year;// Change format of date

                            var weekNo =  date.getDay();
                            //Sunday = 0, Monday = 1, Tuesday = 2, Wednesday = 3, Thursday = 4, Friday = 5, Saturday = 6

                            if(jQuery.inArray(weekNo.toString(), holiday_weeks) != -1 || jQuery.inArray(newdate, highlight_holidays) != -1)
                                return [false, "holidayFaded", "Holiday!"];// Pass class name and tooltip text
                            else
                                return [true, "" ];// Pass class name and tooltip text
                        },
                        defaultDate:new Date(),
                        changeMonth: true,changeYear: true,
                        dateFormat: 'yy-mm-dd',
                    });



                }
                else
                {
                    alert(data.errortext);
                }
                $('#Promain-preloader-ScheduleActivity').hide();
            }
        });

    });

    $(document).on('click','.btn-assignuser',function(){  
        var error=0;
        var id = $(this).attr('data-act');
        if($('#activity_assign_user'+id).val()==''){
            $('#activity_assign_user'+id).next("span").html('Select one user to assign').show('slow');
            error=1;
        }
        else
            $('#activity_assign_user'+id).next("span").html('');
        
        if(error==0)
        {
            $.ajax({
                type: 'POST',
                url: '../projectsmain/assignuser',
                beforeSend : function(){
                    $('#btn-assignuser'+id).attr("disabled", true);
                },
                dataType: "json",
                data: {id:id, user_id:$('#activity_assign_user'+id).val()},
                success: function(data){
                    if(data.error=='No'){
                        $('#assignuser_btn_contanier'+id).html('<span style="color:green;">Asigned!</span>');
                    }
                    $('#btn-assignuser'+id).attr("disabled", false);
                }
            });
        }
    });


    $(document).on('click','.editscheduleactivitybutton',function(){  
        var id=$(this).attr('data-v');
        var type=$(this).attr('data-type');
        type = type.replace(/ +/g, "");
       // $('#edit'+type+'activityname'+id).show();
        $('#edit'+type+'activityunit'+id).show();
        $('#edit'+type+'activityduration'+id).show();
        $('#edit'+type+'activitystartdate'+id).show();
        $('#edit'+type+'activityenddate'+id).show();
        //$('#edit'+type+'activitylag'+id).show();
        //$('#editenggprocess'+id).show();
        $("input#estimate"+id).removeAttr("disabled");
        $('#save'+type+'activitybutton'+id).show();
       // $('#'+type+'activityname'+id).hide();
        $('#'+type+'activityunit'+id).hide();
        $('#'+type+'activityduration'+id).hide();
        $('#'+type+'activitystartdate'+id).hide();
        $('#'+type+'activityenddate'+id).hide();
        //$('#'+type+'activitylag'+id).hide();
        $('#'+type+'process'+id).hide();
        $('#edit'+type+'activitybut'+id).hide(); 
    });
    $(document).on('click','.savescheduleactivitybutton',function(){
        var id =$(this).attr('data-v');
        var type =$(this).attr('data-type');
        type = type.replace(/ +/g, "");
        var name= $('#edit'+type+'activityname'+id).val();
        var unit= $('#edit'+type+'activityunit'+id).val();
        var startdate= $('#edit'+type+'activitystartdate'+id).val();
        var enddate= $('#edit'+type+'activityenddate'+id).val();
        var quantity= $('#edit'+type+'activityquantity'+id).val();
        //var lag= $('#edit'+type+'activitylag'+id).val();
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
                url: '../projectsmain/scheduleactivityupdate',
                beforeSend : function(){
                    $('#savescheduleactivitybutton'+id).attr("disabled", true);
                },
                dataType: "json",
                data: {id:id,name:name,unit:unit,type:type,startdate:startdate,enddate:enddate,quantity:quantity,lag:'',duration:duration},
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
                        $('#'+type+'activityquantity'+data.Id).text(data.Quantity).show();
                        $('#'+type+'activityduration'+data.Id).text(data.Duration).show();
                        $('#'+type+'activitystartdate'+data.Id).text(data.Startdate).show();
                        $('#'+type+'activityenddate'+data.Id).text(data.Enddate).show();
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

    $(document).on( "click", "#savescheduleactivities", function(){
        var error=0;
    $('.error').hide();
        var actname = $('#scheduleactivitiesname').val();
        var unit = $('#scheduleactivitiesunit').val();
        var startDate = $('#scheduleactivitiesStartDate').val();
        var endDate = $('#scheduleactivitiesEndDate').val();
        var duration = $('#scheduleActivityDuration').val();
        var quantity = $('#scheduleactivitiesQuantity').val();
        var resourceunits = 1;
        console.log(startDate);
        if(actname == ''){ 
            $('#scheduleactivitiesname').next("span").html('Enter Activity Name').show('slow');
            error=1;
        }
        
        if(unit == '')
        {
            $('#scheduleactivitiesunit').next("span").html('Enter Unit').show('slow');
            error=1; 
        }
        



         if(quantity == '')
        {
            $('#scheduleactivitiesQuantity').next("span").html('Enter Quantity').show('slow');
            error=1; 
        }
         

        if(startDate == '')
        {
            $('#scheduleactivitiesStartDate').next("span").html('Select Date').show('slow');
            error=1; 
        }
        if(endDate == '')
        {
            $('#scheduleactivitiesEndDate').next("span").html('Select Date').show('slow');
            error=1; 
        }
       if(error == 0){

        $.ajax({
            type: 'POST',
            url: '../projectsmain/addscheduleactivities',
            beforeSend : function(){
                $('#savescheduleactivities').attr("disabled", true);
            },
            dataType: "json",
            data: {activityname:actname,activityunit:unit,startDate:startDate,endDate:endDate,duration:duration,quantity:quantity,projectId:$('#selectedProjectId').val(),itemId:$('#selectedScheduleItem').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#enggactivitiesform')[0].reset();
                    $('#savescheduleactivities').attr("disabled", false);
                    $('#cancelscheduleactivity').trigger('click');
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
    }
    
       
    });

    $('#cancelscheduleactivity').click(function(){
        $('#enggactivitiesform')[0].reset();
        $('#actlistingshow').show();
        $(this).parents('.tab').removeClass('add-form-active');
        $('#listscheduleact').trigger('click');
    });

    $(document).on('click', '#savescheduleqty', function(){
        var scheduleItemId = $('#selectedScheduleItem').val();
        if(!scheduleItemId){
            alert('Please select a schedule item first.');
            return;
        }
        var ids = [];
        var quantities = [];
        $('#scheduleactivityitems .editenggactivityquantity').each(function(){
            var current  = $(this).val();
            var original = $(this).data('original');
            if(current != original){
                var actId = $(this).closest('.activitiess').attr('data-id');
                if(actId){
                    ids.push(actId);
                    quantities.push(current);
                }
            }
        });
        if(ids.length === 0){
            alert('No changes to save.');
            return;
        }
        $.ajax({
            type: 'POST',
            url: '../projectsmain/savescheduleqty',
            beforeSend: function(){
                $('#savescheduleqty').attr('disabled', true).html('Saving...');
            },
            dataType: 'json',
            data: {ids: ids, quantities: quantities},
            success: function(data){
                if(data.error == 'No'){
                    $('#listscheduleact').trigger('click');
                } else {
                    alert(data.errortext);
                }
                $('#savescheduleqty').attr('disabled', false).html('<span class="icon-check"></span> SAVE');
            }
        });
    });

    $(document).on('click','.deletescheduleactivity',function(){ 
        var id=$(this).attr('data-v');
        var type =$(this).attr('data-type');
        var r = confirm("Are you sure you want to delete this Activity ?");
        if (r == true) {
            $.ajax({
                type: 'POST',
                url: '../projectsmain/scheduleactivitydelete',
                async:false,
                dataType:"json",
                data: {id:id,type:type},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#enggactivities'+id).remove();
                        $('#listscheduleact').trigger('click');
                    }
                    else {
                        //alert('Cannot delete this activity as it is used in Pricing and Scheduling');
                        alert(data.errortext);
                    }
                }
            });
        }
    });

    $(document).on('change','.editrelationprecedentitem',function(){
        var scheduleItem= $(this).val();
       var dataid= $(this).attr("data-id");
      // alert(scheduleItem)
       // if($('#schedule_item_second').val()=='' )
       //     $('#schedule_item_second').val(scheduleItem);
       $.ajax({
           type: 'POST',
           url: '../projectsmain/getscheduleactivity',
           dataType: "json",
           data: {scheduleItem: scheduleItem,projectid:$('#selectedProjectId').val()},
           success: function(data){
               if(data.error=='No')
               {
                   $('#editrelationprecedentactivity'+dataid).html(data.result);
               }
           }
       });
   });
   
   $(document).on('change','.editrelationdependentitem',function(){
       var scheduleItem= $(this).val();
       var dataid= $(this).attr("data-id");
       //alert(scheduleItem)
       // if($('#schedule_item_second').val()=='' )
       //     $('#schedule_item_second').val(scheduleItem);
       $.ajax({
           type: 'POST',
           url: '../projectsmain/getscheduleactivity',
           dataType: "json",
           data: {scheduleItem: scheduleItem,projectid:$('#selectedProjectId').val()},
           success: function(data){
               if(data.error=='No')
               {
                   $('#editrelationdependentactivity'+dataid).html(data.result);
               }
           }
       });
   });

   // resource allocation tab

   $(document).on('click','.resource-list-btn2', function(e){

        e.preventDefault();
        $('.Schedule-tab').addClass('add-allocation-list-active');
        setTimeout(function() {
           //$("html, body").animate({ scrollTop: $('.allocate-resource-tabs').offset().top }, 1000);
        }, 10);
    });
                    
    $(document).on('click','.close-resource-list-btn2', function(e){
        e.preventDefault();
        $('.Schedule-tab').removeClass('add-allocation-list-active');
    });

    $(document).on('click','.resource-back-button', function(e){
        e.preventDefault();
        $("#schedule-allocate-body-one-head").show();
        $("#schedule-allocate-body-one").show();
        $("#schedule-allocate-body-two").hide();
    });

   $(document).on('click','.assignresource',function(){
    var activityid=$(this).attr('data-v');
    var proid=$(this).attr('data-proid');
    $("#schedule-allocate-body-one-head").hide();
    $("#schedule-allocate-body-one").hide();
    $("#schedule-allocate-body-two").show();
    $('.close-resource-list-btn2').trigger('click');

    $.ajax({
        type: 'POST',
        url: '../projectsmain/resourceassign',
        beforeSend : function(){
            $('.preloader').show();
        },
        dataType: "json",
        data: {activityid:activityid,projectid:proid},
        success: function(data){
            if(data.error=='No')
            {
                $('#headone').html(data.headone);
                $('#scheduleassignlist-schedule').html(data.addedones);
                $('#SAbuttonlist').html(data.SAbuttonlist);
            }
            $('.preloader').hide();
        }
    });
   });




   $(document).on('click','#resource_search123',function(){
        $('.resourcesearch1').trigger('click');
   });

   $(document).on('click','.resourcesearch1',function(){
        var resorcetype=$(this).attr('data-v');
        var Project_Id=$('#Project_Id').val();
        if(resorcetype!=''){
            $('#selectresource_id').val(resorcetype);
        }
        var name=$('#resource_name').val();
        // console.log(resorcetypeid);
        $.ajax({
            type: 'POST',
            url: '../projectsmain/resourcesearchbytyid',
            dataType: "json",
            data: {resourcetypeid:$('#selectresource_id').val(),name:name,resourcegroup:$('#resource_groupselection').val(),Project_Id:Project_Id},
            success: function(data){
                if(data.error=='No')
                {
                    $('#projects-Activity-Search-Lists2').html(data.result);
                    $('#resource_groupselection').html(data.group);

                }
                else
                {
                    alert(data.errortext);
                }

                $('.preloader').hide();
            }
        });
    });

    $(document).on( "click", ".addresource", function(){
        var resid=$(this).val();
        var activityid=$('#activityid').val();
        var Project_Id=$('#Project_Id').val();
        $.ajax({
            type:'POST',
            url:'../projectsmain/addresourceassign',
            beforeSend:function(){
                $('#addresourcebutton'+resid).attr("disabled",true);
                $('.preloader').show();
            },
            dataType:"json",
            data:{activityid:activityid,resid:resid,Project_Id:Project_Id},
            success:function(data){
                //$('#addedresources').html(data.result);
                $('#added_resources').append(data.result);
                $('#addresource_button'+resid).attr("disabled",false);
                $('#assignresourceload').trigger('click');
                $('.preloader').hide();
                $('#resourcerow'+resid).remove();
                //$('#productratetotal').val(data.price);
            }
        });
    });

    $(document).on('click','#assignresourceload',function(){
        var activityid=$('#activityid').val();
        var Project_Id=$('#Project_Id').val();
        $.ajax({
            type: 'POST',
            url: '../projectsmain/resourceassign',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {activityid:activityid,projectid:Project_Id},
            success: function(data){
                if(data.error=='No')
                {
                    //$('#headone').html(data.headone);
                    $('#scheduleassignlist-schedule').html(data.addedones);
                    //$('#SAbuttonlist').html(data.SAbuttonlist);
                }
                $('.preloader').hide();
            }
        });
       });

    $(document).on( "click",".editresourceschitem", function(){

        var itemid=$(this).attr('data-v');

        $('#editresourceschitem'+itemid).hide();
        $('#resource_cpcty'+itemid).hide();
        $('#resource_utilzd'+itemid).hide();
        $('#resourcecpcty'+itemid).show();
        $('#resourceutilzd'+itemid).show();
        $('#saveresourceschitem'+itemid).show();
        
    });

    $(document).on( "click",".saveresourceschitem", function(){
        var itemid=$(this).attr('data-v');
        var capacity = $('#resourcecpcty'+itemid).val();
        var utilised = $('#resourceutilzd'+itemid).val();
        $.ajax({
            type: 'POST',
            url: '../projectsmain/savescheduleres',
            beforeSend : function(){
                $('#saveresourceschitem'+itemid).attr("disabled", true);
            },
            dataType: "json",
            data: {projresid:itemid,capacity:capacity,utilised:utilised},
            success: function(data){
                if(data.error=='No')
                {
                    $('#editresourceschitem'+itemid).show();
                    $('#resource_cpcty'+itemid).html(capacity);
                    $('#resource_utilzd'+itemid).html(utilised);
                    $('#resource_cpcty'+itemid).show();
                    $('#resource_utilzd'+itemid).show();
                    $('#resourcecpcty'+itemid).hide();
                    $('#resourceutilzd'+itemid).hide();
                    $('#saveresourceschitem'+itemid).hide();
                }
                else {
                    alert(data.errortext);
                }

                $('#saveresourceschitem'+itemid).attr("disabled", false);
            }
        });
    });

    $(document).on( "click",".removeresourceschitem", function(){
        var itemid=$(this).attr('data-v');
        var r = confirm("Are you sure you want to delete this Resource ?");
        if (r == true) {
            $.ajax({
                type: 'POST',
                url: '../projectsmain/deletescheduleres',
                beforeSend : function(){
                    $('#removeresourceschitem'+itemid).attr("disabled", true);
                },
                dataType: "json",
                data: {projresid:itemid},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#scheresrow'+itemid).remove();
                    }
                    else {
                        alert(data.errortext);
                    }

                    $('#removeresourceschitem'+itemid).attr("disabled", false);
                }
            });
        }
    });

});

function getItemname(id)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../projectsmain/getitemname',
        async:false,
        dataType: "json",
        data: {id:id},
        success: function(data){
            retval=data.itemName;
        }
    });
    return retval;
}

function getduration(startDate,endDate)
{  
    var diffInMs   = new Date(endDate) - new Date(startDate);
    var diffInDays = diffInMs / (1000 * 60 * 60 * 24);
    /*if(diffInDays < 0) {
        $('#scheduleActivityDuration').val(0);
    }else{
        $('#scheduleActivityDuration').val(diffInDays);
    }*/
}

/*$(function() {
$( "#scheduleactivityitems" ).sortable({
        placeholder: "ui-state-highlight",
        helper:'clone',
        
        update:function( event, ui ) {
            //alert($(this).index());

            var updatedrows=[];
            $('.activitiess').each(function() {
                var rowid=$(this).attr('data-id');
                var rowindex=$(this).index();
                updatedrows.push({
                    rowid: rowid,
                    rowindex:rowindex
                })
            });
            $.ajax({
                type: 'POST',
                url: '../projectsmain/updateganttsort',
                data: {datavalue:updatedrows},
                dataType: "json",
                success: function(data){}
            });
        }

    }).disableSelection();
});*/

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
        endDate = getDateAfterHoliday(startDate, duration);
        $('#editactivityenddate'+id).val(endDate);
    }
});

var duration_cnt = 1;
function getDateAfterHoliday(date, duration){
    var holiday_arr      = ($('#holiday_arr').val()).split(",");
    var holiday_week_arr = ($('#holiday_week_arr').val()).split(",");
 
    weekNo          = new Date(date).getDay()
    formattedDate   = formatDate(date, 'd-m-y', true);

    var newdate = new Date(date);
    var newdate_temp = new Date(date).setDate(newdate.getDate() + 1);

    if(jQuery.inArray(weekNo.toString(), holiday_week_arr) != -1 || jQuery.inArray(formattedDate, holiday_arr) != -1){
        return getDateAfterHoliday(formatDate(newdate_temp), duration);
    }
    else if(duration > duration_cnt){
        duration_cnt++;
        return getDateAfterHoliday(formatDate(newdate_temp), duration);
    }
    else{
        duration_cnt = 1;
        return date;
    } 

}


$(document).on('keyup','.editenggactivityduration',function(){
    
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



