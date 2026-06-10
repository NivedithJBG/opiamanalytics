
$(document).on('click', '.hover', function(){
    var tooltip = $(this).attr('data-tooltip');
    $('.tooltiptable').hide();
    $('#' + tooltip).fadeIn('fast');
});
$(document).on('mouseleave', '.hover', function(){
    var tooltip = $(this).attr('data-tooltip');
    $('#' + tooltip).fadeOut('slow');
});

$(document).on( "click", "#progress_act_reprt", function(){
    if($('#select_report_date').val()){
        strDate = $('#select_report_date').val();
    }
    else{
        var d = new Date();
        var strDate = d.getDate() + "-" + (month<10 ? '0' : '') +(d.getMonth()+1) + "-" + d.getFullYear();

        var d = new Date();
        var month = d.getMonth()+1;
        var day = d.getDate();
        var year = d.getFullYear();
        var strDate = (day<10 ? '0' : '') + day + "-" + (month<10 ? '0' : '') + month + "-" + year;
    }
    $('#select_report_date').val(strDate);
    $('#activity_pr_main').trigger('click');
    // $("#cntbill").css("display", "none");
});

$(function(){
    $('#activity_pr_main').click(function(){
        $.ajax({
            type: 'POST',
            url: '../report/scheduleprogressactivities',
            beforeSend : function(){
               $('.preloader').show();
            },
            dataType: "json",
            data: {dateselect:$('#select_report_date').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#schedule_report_activityitems').show();
                    $('#activitycompletehist').hide();
                    $('#schedule_report_activityitems').html(data.result);
                    $("#pgrsrpt").css("display", "block");
                    $('.activity_history_btn').show();
                    $('.activity_back_btn').hide();

                }
                $('.preloader').hide();
            }
        });
    });
});

$(document).on('click','.activity_history_btn',function(){
    $.ajax({
        type: 'POST',
        url: '../report/actreportcompletedhistory',
        dataType: "json",
        async:false,
        data: {},
        success: function(data){
            if(data.error == 'No')
            {
                $('#schedule_report_activityitems').hide();
                $('#activitycompletehist').show();
                $('.actheads').hide();
                $('.activity_history_btn').hide();
                $('.activity_back_btn').show();
                
                $('#activitycompletehist').html(data.result);

            }
            
        }
    });

});

$(document).on('click','.activity_back_btn',function(){
    $('#activity_pr_main').trigger('click');
});

/*$(document).on('click','.taskreport',function(){
    $('#schedule-task').hide();
    $('#schedule-task-reporting').show();

    $.ajax({
        type: 'POST',
        url: '../report/scheduleprogresstasklist',
        beforeSend : function(){
           $('.preloader').show();
        },
        dataType: "json",
        data: {dateselect:$('#select_report_date_task').val()},
        success: function(data){
            if(data.error=='No')
            {
                $('#scheduleactivityitemstask').html(data.result);
            }
            $('.preloader').hide();
        }
    });
});

$(document).on('change','.select_report_date_task',function(){
    $('#schedule-task').hide();
    $('#schedule-task-reporting').show();

    $.ajax({
        type: 'POST',
        url: '../report/scheduleprogresstasklist',
        beforeSend : function(){
           $('.preloader').show();
        },
        dataType: "json",
        data: {dateselect:$('#select_report_date_task').val()},
        success: function(data){
            if(data.error=='No')
            {
                $('#scheduleactivityitemstask').html(data.result);
            }
            $('.preloader').hide();
        }
    });
});*/


$(document).on('click','.activityreportcomplete',function(){

    var r = confirm("Are you sure you want to Complete this Activity?");

    if (r == true) {
        var id = $(this).data('v');
        $.ajax({
            type: 'POST',
            url: '../report/completeactivtiyreport',
            dataType: "json",
            async:false,
            data: {id:id},
            success: function(data){
                if(data.error == 'No')
                {
                    $('#progress_act_reprt').trigger('click');

                }
                
            }
        });
    }


});

$(document).on('click','.activityreportreactivate',function(){

    var r = confirm("Are you sure you want to Reactivate this Activity?");

    if (r == true) {
        var id = $(this).data('v');
        $.ajax({
            type: 'POST',
            url: '../report/reactivateactivtiyreport',
            dataType: "json",
            async:false,
            data: {id:id},
            success: function(data){
                if(data.error == 'No')
                {
                    $('#progress_act_reprt').trigger('click');

                }
                
            }
        });
    }


});


/*$(document).on('click','.taskreport',function(){
    var actid = $(this).attr('data-id');
    var date = $('#select_report_date').val();
    $('#taskhvreprt'+actid).addClass('active');

    $.ajax({
        type: 'POST',
        url: '../report/scheduleprogresstasklist',
        dataType: "json",
        data: {actid:actid,date:date},
        success: function(data){
            if(data.error=='No')
            {
                $('#tooltip'+actid).html(data.result);
            }
            $('.preloader').hide();
        }
    });
});*/

$(document).on('click','.taskreport',function(){
    var actid = $(this).attr('data-id');
    var date = $('#select_report_date').val();
    $('#taskhvreprt'+actid).addClass('active');
    $('.canceltaskprogressrpt').attr('id','canceltaskprogressrpt'+actid).attr('data-id',actid);
    $('.savetaskprogressrpt').attr('id','savetaskprogressrpt'+actid).attr('data-id',actid);
    $('.taskProgressRptSaveDraft').attr('id','taskProgressRptSaveDraft'+actid).attr('data-id',actid);
    $.ajax({
        type: 'POST',
        url: '../report/scheduleprogresstasklist',
        dataType: "json",
        data: {actid:actid,date:date},
        success: function(data){
            if(data.error=='No')
            {
                $('#taskReportData').html(data.result);
                applyHolidayToDatepicker(data);
            }
            $('.preloader').hide();
        }
    });
});


function getResourceReportForm(actid, resType = ''){
    if(resType == '') resType = 'Material';

    var date = $('#select_report_date').val();
    var material_issued_date    = $('#material_issued_date').val();
    var attendance_date         = $('#attendance_date').val();
    var equipment_issued_date   = $('#equipment_issued_date').val();

    var date = new Date();
    var today = formatDate(new Date(),'d-m-y');
    
    selDate = today;
    if(!material_issued_date) material_issued_date = selDate;
    if(!attendance_date) attendance_date = selDate;
    if(!equipment_issued_date) equipment_issued_date = selDate;


    //$('#taskhvreprt'+actid).addClass('active');
    $('.cancelresourcerpt').attr('id','cancelresourcerpt'+actid).attr('data-id',actid);
    $('.saveresourcerpt').attr('id','saveresourcerpt'+actid).attr('data-id',actid);

    $.ajax({
        type: 'POST',
        url: '../report/scheduleresourcelist',
        dataType: "json",
        data: { actid:actid,
                date:selDate, 
                material_issued_date:material_issued_date, 
                attendance_date:attendance_date, 
                equipment_issued_date:equipment_issued_date, 
                resType:resType
              },
        success: function(data){
            if(data.error=='No')
            {
                $('#resourceReportData').html(data.result);
                $('#resUsage'+resType+'Container').show();
                $('#res'+resType).parent().addClass('active');

                //var highlight_dates = ['8-11-2022','11-11-2022','18-11-2022','1-12-2022','1-8-2023', '2-8-2023'];
                
                var highlight_dates_material = data.highlight_dates_material;
                var highlight_dates_labour   = data.highlight_dates_labour;
                var highlight_dates_plant    = data.highlight_dates_plant;
                var highlight_dates_sub      = data.highlight_dates_sub;


                // Initialize material datepicker
                $('.material_issued_date').datepicker({
                    beforeShowDay: function(date){
                       var month = date.getMonth()+1;
                       var year = date.getFullYear();
                       var day = date.getDate();
                       var newdate = day+"-"+month+'-'+year;// Change format of date
                       var tooltip_text = "Issued on  " + newdate;// Set tooltip text when mouse over date
                       
                       if(jQuery.inArray(newdate, highlight_dates_material) != -1){
                            return [true, "highlight", tooltip_text ];// Pass class name and tooltip text
                       }
                       return [true];
                   },
                   defaultDate:new Date(),
                   changeMonth: true,changeYear: true,
                   dateFormat: 'dd-mm-yy'
                }).datepicker('setDate', material_issued_date);


                // Initialize attendance datepicker
                $('.attendance_date').datepicker({
                    beforeShowDay: function(date){
                       var month = date.getMonth()+1;
                       var year = date.getFullYear();
                       var day = date.getDate();
                       var newdate = day+"-"+month+'-'+year;// Change format of date
                       var tooltip_text = "Reported on  " + newdate;// Set tooltip text when mouse over date
                       
                       if(jQuery.inArray(newdate, highlight_dates_labour) != -1){
                            return [true, "highlight", tooltip_text ];// Pass class name and tooltip text
                       }
                       return [true];
                   },
                   defaultDate:new Date(),
                   changeMonth: true,changeYear: true,
                   dateFormat: 'dd-mm-yy'
                }).datepicker('setDate', attendance_date);


                // Initialize equipment_issued datepicker
                $('.equipment_issued_date').datepicker({
                    beforeShowDay: function(date){
                       var month = date.getMonth()+1;
                       var year = date.getFullYear();
                       var day = date.getDate();
                       var newdate = day+"-"+month+'-'+year;// Change format of date
                       var tooltip_text = "Issued on  " + newdate;// Set tooltip text when mouse over date
                       
                       if(jQuery.inArray(newdate, highlight_dates_plant) != -1){
                            return [true, "highlight", tooltip_text ];// Pass class name and tooltip text
                       }
                       return [true];
                   },
                   defaultDate:new Date(),
                   changeMonth: true,changeYear: true,
                   dateFormat: 'dd-mm-yy'
                }).datepicker('setDate', equipment_issued_date);

            }
            $('.preloader').hide();
        }
    });
}



$(document).on('click','.cleartaskdet',function(){

    var taskid = $(this).val();
    var cycleno = $('#currcycleshowss').val();
    var actid = $(this).attr('data-id');

    var r = confirm("Are you sure you want to Clear this Task ?");

    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../report/scheduletaskclear',
            dataType: "json",
            data: {actid:actid,cycleno:cycleno,taskid:taskid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#taskreport'+actid).trigger('click');
                }else if(data.error=='Yes')
                {
                    
                }
                $('.preloader').hide();
            }
        });
    }
    
});

$(document).on('click','.canceltaskprogressrpt',function(e){
    e.preventDefault();
    var actid = $(this).attr('data-id');
    $('#taskhvreprt'+actid).removeClass('active');
    $('#tooltip'+actid).html('');
});

$(document).on('click','.taskProgressRptSaveDraft',function(e){
    e.preventDefault();
    var actid = $(this).attr('data-id');
    $('#task_report_status'+actid).val(0);
    
    var date = $('#select_report_date').val();

    var error = 0;
    var diff = 0;
    var dur_cnt = 0;
    var val_cnt = 0;
    $('.taskactdur').each(function () {
        var id = $(this).attr('data-id');
        var duration = $('#taskactdur'+id).val();
        var strt_time = $('#taskstart_time'+id).val();
        var end_time = $('#taskend_time'+id).val();
        var strtdate = $('#task_date'+id).val(); 
        var enddate  = $('#task_enddate'+id).val();


        if(parseInt(duration) == 0 || duration == '')
            val_cnt++;
        else{
            dur_cnt++;
            if(strt_time!='' && end_time!='')
                diff = 1;

            if(strtdate < enddate)
                diff = 0;
            else if(strtdate == enddate)
            {
                if(strt_time > end_time)
                    diff = 1;
                else if(strt_time < end_time)
                    diff = 0;
            }
            else if(strtdate > enddate)
                diff = 1;
        }
    });

    if(dur_cnt == 0){
        //alert('Please enter start and end time for atleast one task!')
        $('#task-error-messages').html('<h5>Please enter start and end time for atleast one task!</h5>').show().delay(3000).fadeOut('slow');
        error = 1;
    }
    if(diff == 1){
        //alert('Start time is greater than end time!');
        $('#task-error-messages').html('<h5>Start time is greater than end time!</h5>').show().delay(3000).fadeOut('slow');
        error = 1;
    }


    if(error == 0){
        $.ajax({
            type: 'POST',
            url: '../report/scheduletaskreporting',
            dataType: "json",
            async:false,
            data: $('#schedule-task-reporting'+actid).serialize()+"&date="+date,
            success: function(data){
                if(data.error == 'No')
                {
                    $('#task-success-messages').html('<h5>Task report saved as draft Successfully!</h5>').show().delay(3000).fadeOut('slow', function(){
                        $('#progress_act_reprt').trigger('click');
                        $.ajax({
                            type: 'POST',
                            url: '../report/scheduleprogresstasklist',
                            dataType: "json",
                            data: {actid:actid,date:date},
                            success: function(data){
                                if(data.error=='No')
                                {
                                    $('#taskReportData').html(data.result);
                                    applyHolidayToDatepicker(data);
                                }
                                $('.preloader').hide();
                            }
                        });

                    });
                }
            }
        });
    }



    //$('#savetaskprogressrpt'+actid).trigger('click');
});


function applyHolidayToDatepicker(data) {
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
        maxDate: new Date(),
        dateFormat: 'dd-mm-yy',
    });
}

$(document).on('change','.issued_qunatity',function(){
    var resid = parseInt($(this).attr('data-resid'));
    var curQty = $(this).val();
    currStock = parseFloat($("#res_stock_val_"+resid).val());
    newStock  =  parseFloat(currStock-curQty);
    $("#res_stock_"+resid).html(newStock);
});

$(document).on('click','.saveresourcerpt',function(e){
    var actid = $(this).attr('data-id');
    var resType = $(this).attr('data-res-type');


    error = 0;
    if(resType == 'Material'){
        $('.issued_qunatity').each(function () {
            if(!$(this).val()) error = 1;
        });
    }
    if(resType == 'Labour'){
        $('.labour_name').each(function () {
            if(!$(this).val()) error = 1;
        });
        $('.work_hours').each(function () {
            if(!$(this).val()) error = 1;
        });
    }
    if(resType == 'PlantEquip'){
        $('.equipment_name').each(function () {
            if(!$(this).val()) error = 1;
        });
        $('.fuel_qty').each(function () {
            if(!$(this).val()) error = 1;
        });
    }

    if(!error){
        $.ajax({
            type: 'POST',
            url: '../report/scheduleresourcereporting',
            dataType: "json",
            async:false,
            data: $('#scheduleResourceReporting'+resType+actid).serialize(),
            success: function(data){
                if(data.error == 'No')
                {
                    $('#resource-report-success-messages').html('<h5>Resource Reported Successfully!</h5>').show().delay(2000).fadeOut('slow', function(){
                        $('#progress_act_reprt').trigger('click');
                        $('#cancelresourcerpt').trigger('click');

                       /* $.ajax({
                            type: 'POST',
                            url: '../report/scheduleprogresstasklist',
                            dataType: "json",
                            data: {actid:actid,date:date},
                            success: function(data){
                                if(data.error=='No')
                                {
                                    $('#taskReportData').html(data.result);
                                }
                                $('.preloader').hide();
                            }
                        });*/
                    }); 
                }
                else{
                    //alert('Please enter data for all tasks!')
                    $('#resource-report-error-messages').html('<h5>Please enter data for all resources!</h5>').show().delay(3000).fadeOut('slow');
                }
            }
        });
    }
    else{
        $('#resource-report-error-messages').html('<h5>Please enter data for all resources!</h5>').show().delay(3000).fadeOut('slow');
    }



});



$(document).on('click', '.tasks-section-toggle', function(){
    var targetId = '#' + $(this).data('target');
    var $arrow = $(this).find('.toggle-arrow');
    if ($(targetId).is(':visible')) {
        $(targetId).slideUp(200);
        $arrow.html('&#9654;');
    } else {
        $(targetId).slideDown(200);
        $arrow.html('&#9660;');
    }
});

$(document).on('change', '#workhours', function(){
    var wh = parseFloat($(this).val()) || 8;
    var aid = $('#activityid').val();
    var totalHrs = parseFloat($('#totvdurr').text()) || 0;
    if (aid) {
        $('#act_cycle_days_' + aid).text(wh > 0 ? (totalHrs / wh).toFixed(2) : '—');
    }
});

$(document).on('click', '.savetaskprogressrpt', function () {
    var actid = $(this).data('id');
    if (!$('#start_date_' + actid).val() || !$('#start_date_' + actid).val().trim()) {
        alert('Activity Start Date is required.');
        return;
    }
    if ($('#currentqnty' + actid).val() === '' || $('#currentqnty' + actid).val() === null) {
        alert('Current Quantity is required.');
        return;
    }
    var $btn = $(this);
    $btn.html('Reporting...').attr('disabled', true);
    $.ajax({
        type: 'POST',
        url: '../report/simplereportprogress',
        dataType: 'json',
        data: $('#schedule-task-reporting' + actid).serialize() + '&reportdate=' + encodeURIComponent($('#select_report_date').val()),
        success: function (data) {
            if (data.error === 'No') {
                $btn.html('Report').removeAttr('disabled');
                $('.taskReportPopupCloseBtn').trigger('click');
                $('#progress_act_reprt').trigger('click');
            } else {
                $btn.html('Report').removeAttr('disabled');
            }
        },
        error: function () {
            $btn.html('Report').removeAttr('disabled');
        }
    });
});

$(document).on('click','.backprgrsreprt',function(){
    $('#schedule-task-reporting').hide();
    $('#schedule-task').show(); 
});

$(document).on('change','#progreport_project',function(){
    //$('#progrepiowlist').val(0);
    //$('#activitytaskitems').html('<tr id="nodata"><td colspan="6" style="text-align: center;">No Activities Found</td></tr>');
    $('#activity_pr_main').trigger('click');
});

$(document).on('change','#select_report_date',function(){  
    $('#activity_pr_main').trigger('click');
});

$(document).on('change','.taskend_time',function(){  
    var id = $(this).attr('data-id');
    changeDurations(id);

});

$(document).on('change','.break_hour',function(){
    var id = $(this).attr('data-id');
    changeDurations(id);
});



$(document).on('change','.task_enddates',function(){  
    var id = $(this).attr('data-id');
    changeDurations(id);
});



    function changeDurations(id){
        
        $('#taskstart_time'+id).trigger('change');
                return;
        //var id = $(this).attr('data-id');
        /*var workhours = parseInt($('#workhours').val());
        var start_time = $('#taskstart_time'+id).val();
        var end_time = $('#taskend_time'+id).val();

        var date11 = $('#task_date'+id).val();       
        const [days, months, years] = date11.split('-');
        const xyz = new Date(+years, months - 1, +days);  
        dateStrings = new Date(xyz).toUTCString(); 
        date1 = dateStrings.split(' ').slice(0, 4).join(' '); 

        var date12 = $('#task_enddate'+id).val();   
        const [day4, month4, year4] = date12.split('-');
        const date222 = new Date(+year4, month4 - 1, +day4); 
        dateString = new Date(date222).toUTCString();
        date2 = dateString.split(' ').slice(0, 4).join(' ');

        if(start_time!='' && end_time!='' && date1!='' && date2!=''){

            function diff_hours(dt2, dt1) 
            {
                var diff =(dt2.getTime() - dt1.getTime()) / 1000;
                diff /= (60);
                return Math.abs(Math.round(diff));
            }


            dt1 = new Date(date1 + " " + start_time);
            dt2 = new Date(date2 + " " +  end_time);  
            var hours2 = diff_hours(dt1, dt2)/60;
            var hours4 = Math.floor(diff_hours(dt1, dt2)/60);
            var hours3 = parseFloat(hours2)*60;
            var minutess = hours3 % 60;
            var minutes = minutess.toFixed(0);
            if(minutes < 10) minutes = 0+minutes;
            var hours =  hours4 + ":" + minutes;  

            //---Duration calculations based on the Work Hours----
            //console.log('hours before workhours - '+hours);
            if(workhours != 24){
               hours = parseFloat(parseFloat(hours) - (Math.floor(parseFloat(hours)/24) * workhours)).toFixed(2);
            }
            //console.log('hours after workhours - '+hours);

            break_hour = $('#break_hour'+id).val();
            if(break_hour){
                hours = time_diff(hours, break_hour);
            }
            //console.log('hours after break_hour - '+hours);

            $('#taskactdur'+id).val(hours);

            var totdur = 0;


             $('.taskactdur').each(function () {
                var taskactdur =$(this).attr('data-id');
                var dur = $('#taskactdur'+taskactdur).val();
                if(dur == '') val = 0;
                else val = dur;
                totdur = parseFloat(totdur) + parseFloat(val);

            });
            $('#totvdurr').html(totdur.toFixed(2));


        }
        
        else{
           // alert('Start time is greater than end time!')
        }

        //waste hour calcualtion

        var first_taskid = $('#first_task').val(); 
        var last_taskid = $('#last_task').val(); 
        if(nextid<=last_taskid)
        {
            var nextid = parseFloat(id)+1;
            var final_end_time = $('#taskend_time'+id).val();
            var nxt_start_time = $('#taskstart_time'+nextid).val();

            var date11 = $('#task_enddate'+id).val();       
            const [dayss, monthss, yearss] = date11.split('-');
            const xyzz = new Date(+yearss, monthss - 1, +dayss);  
            dateStrings = new Date(xyzz).toUTCString(); 
            date1 = dateStrings.split(' ').slice(0, 4).join(' '); 

            var date12 = $('#task_date'+nextid).val();   
            const [days4, months4, years4] = date12.split('-');
            const dates222 = new Date(+years4, months4 - 1, +days4); 
            dateString = new Date(dates222).toUTCString();
            date2 = dateString.split(' ').slice(0, 4).join(' ');
            //alert(date11); alert( final_end_time); alert(date12); alert(nxt_start_time); 


            if(final_end_time!='' && final_end_time!=undefined && nxt_start_time!='' && nxt_start_time!=undefined && (workhours == 24 || (workhours != 24 && date1 == date2)) ){

                function diff_hours(dt2, dt1) 
                {
                    var diff =(dt2.getTime() - dt1.getTime()) / 1000;
                    diff /= (60);
                    return Math.abs(Math.round(diff));
                }

                dt1 = new Date(date1 + " " + final_end_time);
                dt2 = new Date(date2 + " " +  nxt_start_time);  
                var hours2 = diff_hours(dt1, dt2)/60;
                var hours4 = Math.floor(diff_hours(dt1, dt2)/60);
                var hours3 = parseFloat(hours2)*60;
                var minutess = hours3 % 60;
                var minutes = minutess.toFixed(0);
                if(minutes < 10) minutes = 0+minutes;
                var hours =  hours4 + ":" + minutes; 
            
                $('#taskwastedur'+nextid).val(hours);

                var totdur = 0;

                $('.taskwastedur').each(function () {
                    //var id =$(this).attr('data-id');
                    
                    var dur = $('#taskwastedur'+id).val();
                    if(dur == '')
                    {
                        val = 0;
                    }else{
                        val = dur;
                    }
                    totdur = parseFloat(totdur) + parseFloat(val);
                

                });
                $('#totwdurr').html(totdur.toFixed(2));
            }


        }*/
    }

    function time_diff( t2, t1 )
    {
        t2Array = String(t2).split(":");
        t1Array = String(t1).split(":");

        t2Mins = (parseInt(t2Array[0]) * 60) + parseInt(t2Array[1]);
        t1Mins = (parseInt(t1Array[0]) * 60) + parseInt(t1Array[1]);

        diffMins = (t2Mins - t1Mins);
        diffHours = Math.floor(diffMins/60);
        diffMins = diffMins - (diffHours * 60)
        return diffHours +':'+ diffMins;
    }



$(document).on('change','.task_dates',function(){
    var error=0;
    var id = $(this).attr('data-id');
    var d = new Date();
    var month = d.getMonth()+1;
    var day = d.getDate();
    var today = d.getFullYear() + '-' + (month<10 ? '0' : '') + month + '-' + (day<10 ? '0' : '') + day;
    var date = $('#task_date'+id).val();
    var d1 = date.split('-');
    var startdate = d1[2] + '-' + d1[1] + '-' + d1[0];

    if(startdate!=''){
        if(startdate>today){
            error=1;
            alert('Sorry you cannot report on future start dates!!!')
        }
    }
    if(error==1)
    { 
        $('#task_date'+id).val('');
    }
    else{
        if($('#currcycleshowss').val() == '1')
            $('#start_date').val($('.task_dates:first').val());
    
        changeDurations(id);
    }
});

$(document).on('change','.taskstart_time',function(){  
    var id = $(this).attr('data-id');
    var workhours = parseInt($('#workhours').val());
    

    $('#taskactdur'+id).val('');
    $('#taskwastedur'+id).val('');

    var start_time = $('#taskstart_time'+id).val();
    var end_time = $('#taskend_time'+id).val();

    var date11 = $('#task_date'+id).val();       
    const [days, months, years] = date11.split('-');
    const xyz = new Date(+years, months - 1, +days);  
    dateStrings = new Date(xyz).toUTCString(); 
    date1 = dateStrings.split(' ').slice(0, 4).join(' '); 

    var date12 = $('#task_enddate'+id).val();   
    const [day4, month4, year4] = date12.split('-');
    const date222 = new Date(+year4, month4 - 1, +day4); 
    dateString = new Date(date222).toUTCString();
    date2 = dateString.split(' ').slice(0, 4).join(' ');

    function formatDate (input) {
        var datePart = input.match(/\d+/g),
        year = datePart[0], // get only two digits
        month = datePart[1], day = datePart[2];
        return day+'-'+month+'-'+year;
    }
    

    if(start_time!='' && end_time!='' && date1!='' && date2!=''){

        function diff_hours(dt2, dt1) 
        {
            var diff =(dt2.getTime() - dt1.getTime()) / 1000;
            diff /= (60);
            return Math.abs(Math.round(diff));
        }

        dt1 = new Date(date1 + " " + start_time);
        dt2 = new Date(date2 + " " +  end_time);  

        var hours2 = diff_hours(dt1, dt2)/60;
        var hours4 = Math.floor(diff_hours(dt1, dt2)/60);
        var hours3 = parseFloat(hours2)*60;
        var minutess = hours3 % 60;
        var minutes = minutess.toFixed(0);
        if(minutes < 10) minutes = 0+minutes;
        var hours =  hours4 + "." + minutes; 

        //---Duration calculations based on the Work Hours----
        //console.log('hours before workhours - '+hours);
        if(workhours != 24){
            //hours = parseFloat(parseFloat(hours) - (Math.floor(parseFloat(hours)/24) * workhours)).toFixed(2);
            hours = parseFloat(parseFloat(hours) - (Math.floor(parseFloat(hours)/24) * (24-workhours))).toFixed(2);
            hours = hours.replace(".", ":");
        }
        //console.log('hours after workhours - '+hours);

        break_hour = $('#break_hour'+id).val();
        if(break_hour){
            hours = hours.replace(".", ":");
            hours = time_diff(hours, break_hour);
        }


        holidayCount = getHolidayCount(date1, date2);
        if(holidayCount){
            hours = hours.replace(".", ":");
            holidayHours = (holidayCount*workhours)+':00';
            hours = time_diff(hours, holidayHours);
        }
        hours = hours.replace(".", ":");

        $('#taskactdur'+id).val(hours);

        var totdur = 0;

        $('.taskactdur').each(function () {
            var id =$(this).attr('data-id');
            var dur = $('#taskactdur'+id).val();
            if(dur == '') val = 0;
            else val = dur;
            totdur = parseFloat(totdur) + parseFloat(val);

        });


        $('#totvdurr').html(totdur.toFixed(2));
        var _wh = parseFloat($('#workhours').val()) || 8;
        var _aid = $('#activityid').val();
        if (_aid) $('#act_cycle_days_' + _aid).text(_wh > 0 ? (totdur / _wh).toFixed(2) : '—');

    }

    //waste hour calcualtion
    //var lastid = parseFloat(id)-1;
    var taskkey      = $(this).attr('data-taskkey');
    var taskkey_prev = parseInt(taskkey)-1;
    var lastid = $('.taskstart_time[data-taskkey= '+ taskkey_prev +']').attr('data-id');




    var cycle_no = $('#currcycleshowss').val();
    var first_taskid = $('#first_task').val(); 
    var last_taskid = $('#last_task').val(); 
    var actid = $('#activityid').val(); 
    var sel_date = $('#sel_date').val(); 

   
    //var nextid = parseFloat(id)+1;
    //var lastid = parseFloat(id)-1; 
    var final_strt_time = $('#taskstart_time'+id).val(); //end  -next task strt time
    var last_end_time = $('#taskend_time'+lastid).val(); 
    var date11 = $('#task_enddate'+lastid).val();       
    var date12 = $('#task_date'+id).val();   



    //if(final_strt_time!='' && final_strt_time!=undefined && last_end_time!='' && last_end_time!=undefined && (workhours == 24 || (workhours != 24 && date11 == date12)) )
    if(final_strt_time!='' && final_strt_time!=undefined && last_end_time!='' && last_end_time!=undefined )
    { 
        const [dayss, monthss, yearss] = date11.split('-');
        const xyzz = new Date(+yearss, monthss - 1, +dayss);  
        dateStrings = new Date(xyzz).toUTCString(); 
        date1 = dateStrings.split(' ').slice(0, 4).join(' '); 

        const [days4, months4, years4] = date12.split('-');
        const dates222 = new Date(+years4, months4 - 1, +days4); 
        dateString = new Date(dates222).toUTCString();
        date2 = dateString.split(' ').slice(0, 4).join(' ');
        //alert(date11); alert( last_end_time); alert(date12); alert(final_strt_time); 
        


            function diff_hours(dt2, dt1) 
            {
                var diff =(dt2.getTime() - dt1.getTime()) / 1000;
                diff /= (60);
                return Math.abs(Math.round(diff));
            }

            dt1 = new Date(date1 + " " + last_end_time);
            dt2 = new Date(date2 + " " +  final_strt_time);  
            var hours2 = diff_hours(dt1, dt2)/60;
            var hours4 = Math.floor(diff_hours(dt1, dt2)/60);
            var hours3 = parseFloat(hours2)*60;
            var minutess = hours3 % 60;
            var minutes = minutess.toFixed(0);
            if(minutes < 10) minutes = 0+minutes;
            //var hours =  hours4 + ":" + minutes; 
            

            holidayCount = getHolidayCount(date1, date2);

            var date1 = new Date(date1);
            var date2 = new Date(date2);
            var diffDays = parseInt((date2 - date1) / (1000 * 60 * 60 * 24), 10); 

            var hours =  hours4 + "." + minutes;   

            if(workhours != 24){
                //hours = parseFloat(parseFloat(hours) - (Math.floor(parseFloat(hours)/24) * workhours)).toFixed(2);
                //hours = parseFloat(parseFloat(hours) - (Math.floor(parseFloat(hours)/24) * (24-workhours))).toFixed(2);
                if(hours >= workhours || diffDays >= 1)
                    hours = parseFloat(parseFloat(hours) - (Math.ceil(parseFloat(hours)/24) * (24-workhours))).toFixed(2);
                else
                    hours = parseFloat(parseFloat(hours) - (Math.floor(parseFloat(hours)/24) * (24-workhours))).toFixed(2);

                if(hours < 0) hours = '0:00';
            }
            hours = hours.replace(".", ":");

            if(holidayCount){
                hours = hours.replace(".", ":");
                holidayHours = (holidayCount*workhours)+':00';
                hours = time_diff(hours, holidayHours);
            }

            $('#taskwastedur'+id).val(hours);


            var totdur = 0;

            $('.taskwastedur').each(function () {
                var id = $(this).attr('data-id');
                var dur = $('#taskwastedur'+id).val();
                if(dur == '') val = 0;
                else          val = dur;
                totdur = parseFloat(totdur) + parseFloat(val);
            

            });
            $('#totwdurr').html(totdur.toFixed(2));
    }
    else if(last_end_time=='' || last_end_time==undefined)
    { 

        var id           = $(this).attr('data-id');
        var taskkey      = $(this).attr('data-taskkey');
        var taskkey_prev = parseInt(taskkey)-1;
        //var lastids      = parseFloat(id)-1; 

        var lastids = $('.taskstart_time[data-taskkey= '+ taskkey_prev +']').attr('data-id');


        //if(lastids>=first_taskid)
        if(taskkey_prev > 0)
            var lastid = lastids;  
        else
            var lastid = $('#last_task').val(); 
       
        //for getting last task end date&time- if multiple cycle - take earliest one
        var start_dates = $('#task_date'+id).val(); 
        var strt_times = $('#taskstart_time'+id).val(); 
        $.ajax({
            type: 'POST',
            url: '../report/gettasktime', 
            dataType: "json",
            data: {taskid:lastid,actid:actid,start_dates:start_dates,strt_times:strt_times, current_cycle : cycle_no},
            success: function(data){ 
                if(data.error=='No')
                {   
                    var date12 = data.enddate;
                    if( date12!='' && date12!=undefined )
                    {
                        var last_end_time = data.endtime;
                        var final_strt_time = $('#taskstart_time'+id).val(); 

                        var date11 = $('#task_date'+id).val();      
                        const [dayss, monthss, yearss] = date11.split('-');
                        const xyzz = new Date(+yearss, monthss - 1, +dayss);  
                        dateStrings = new Date(xyzz).toUTCString(); 
                        date1 = dateStrings.split(' ').slice(0, 4).join(' '); 

                        
                        function formatDate (input) {
                            var datePart = input.match(/\d+/g),
                            year = datePart[0], // get only two digits
                            month = datePart[1], day = datePart[2];
                            return day+'-'+month+'-'+year;
                        }
                            
                        var date22 = formatDate (date12);

                        const [day5, month5, year5] = date22.split('-');

                        const dates222 = new Date(+year5, month5 - 1, +day5); 
                        dateString = new Date(dates222).toUTCString();
                        date2 = dateString.split(' ').slice(0, 4).join(' ');
                        
                        //if(final_strt_time!='' && final_strt_time!=undefined && last_end_time!='' && last_end_time!=undefined && (workhours == 24 || (workhours != 24 && date1 == date2)) )
                        if(final_strt_time!='' && final_strt_time!=undefined && last_end_time!='' && last_end_time!=undefined )
                        {  
                        
                            function diff_hours(dt2, dt1) 
                            {
                                var diff =(dt2.getTime() - dt1.getTime()) / 1000;
                                diff /= (60);
                                return Math.abs(Math.round(diff));
                            }


                            dt1 = new Date(date1 + " " +  final_strt_time);
                            dt2 = new Date(date2 + " " +  last_end_time);  
                            var hours2 = diff_hours(dt1, dt2)/60;
                            var hours4 = Math.floor(diff_hours(dt1, dt2)/60);
                            var hours3 = parseFloat(hours2)*60;
                            var minutess = hours3 % 60;
                            var minutes = minutess.toFixed(0);
                            if(minutes < 10) minutes = 0+minutes;
                            var hours =  hours4 + "." + minutes;   
                            var diffHours =  hours4 + "." + minutes;   

                            //console.log('Hours before calculation - '+hours);

                            if(workhours != 24){
                                //hours = parseFloat(parseFloat(hours) - (Math.floor(parseFloat(hours)/24) * workhours)).toFixed(2);
                                hours = parseFloat(parseFloat(hours) - (Math.ceil(parseFloat(hours)/24) * (24-workhours))).toFixed(2);
                                if(hours < 0) hours = '0:00';
                            }

                            holidayCount = getHolidayCount(date2, date1);
                            if(holidayCount){
                                hours = hours.replace(".", ":");
                                holidayHours = (holidayCount*workhours)+':00';
                                hours = time_diff(hours, holidayHours);
                            }

                            hours = hours.replace(".", ":");
                            //console.log('Hours after calculation - '+hours);
                            $('#task_stoppage_time'+id).val('');
                            $('#taskwastedur'+id).val('');


                            if(parseFloat(diffHours) >= 24){
                                if(cycle_no != 1)
                                    $('#task_stoppage_time'+id).val(hours);
                            }
                            else{
                                $('#taskwastedur'+id).val(hours);
                            }
                            /*$('#taskwastedur'+id).val(hours);
                            $('#taskwastedursts'+id).val(data.taskid);*/
                        }

                        /*var totwastedur = 0;
                        $('.taskwastedur').each(function () {
                            var ids =$(this).attr('data-id');
                            
                            var dur = $('#taskwastedur'+ids).val();
                            if(dur == '')
                                val = 0;
                            else
                                val = dur;

                            totwastedur = parseFloat(totwastedur) + parseFloat(val);
                        

                        });
                        $('#totwdurr').html(totwastedur.toFixed(2));

                        //cum wasted total
                        var cumtotwastedur = 0;
                        $('.singlecumwaste').each(function () {
                            var ids =$(this).attr('data-id');
                            
                            var dur = $('#singtaskwastedur'+ids).val();
                            if(dur == '')   val = 0;
                            else            val = dur;
                            cumtotwastedur = parseFloat(cumtotwastedur) + parseFloat(val);
                        });
                        $('#cumtotwdurr').html(cumtotwastedur.toFixed(2));*/
                    }
                    else
                    {
                        //alert("No data found for wasted hour calculation");
                    }

                }
                else if(data.error=='Yes')
                {
                    //alert("No data found for wasted hour calculation");
                    $('#taskwastedur'+id).val('');
                }
                
            }
        });
    }
 

});


$(document).on('change','#currcycleshowss',function(){  
     //cum wasted total
     var cumtotwastedur = 0;
     $('.singlecumwaste').each(function () {
         var ids =$(this).attr('data-id');
         
         var dur = $('#singtaskwastedur'+ids).val();
         if(dur == '')
         {
             val = 0;
         }else{
             val = dur;
         }
         cumtotwastedur = parseFloat(cumtotwastedur) + parseFloat(val);
     

     });
     $('#cumtotwdurr').html(cumtotwastedur.toFixed(2));
});

$(document).ready(function(){
     //cum wasted total
     var cumtotwastedur = 0;
     $('.singlecumwaste').each(function () {
         var ids =$(this).attr('data-id');
         
         var dur = $('#singtaskwastedur'+ids).val();
         if(dur == '')
         {
             val = 0;
         }else{
             val = dur;
         }
         cumtotwastedur = parseFloat(cumtotwastedur) + parseFloat(val);
     

     });
     $('#cumtotwdurr').html(cumtotwastedur.toFixed(2));
});

$(document).on('change','.edit_start_date',function(){  
    var error=0;
    $('.error').hide();
    
    var select_report_date = $('#select_report_date').val();
    var arr = select_report_date.split('-');
    var reportdate = arr[2] + '-' + arr[1] + '-' + arr[0];
    
    var d = new Date();
    var month = d.getMonth()+1;
    var day = d.getDate();
    var today = d.getFullYear() + '-' + (month<10 ? '0' : '') + month + '-' + (day<10 ? '0' : '') + day;
    //var today = (day<10 ? '0' : '') + day + '-' + (month<10 ? '0' : '') + month + '-' + d.getFullYear();
    var dataid = $(this).attr('data-id');
    var date = $('#start_date_'+dataid).val();
    //var date = $('#select_report_date').val();

    var d1 = date.split('-');
    var startdate = d1[2] + '-' + d1[1] + '-' + d1[0];

    if(startdate!=''){
        //if(startdate>today){
        if(startdate > reportdate){
            error=1;
            alert('Sorry you cannot report on future start dates!!!')
        }
    }

    if(error==1)
    { 
        $('#start_date_'+dataid).val('');
    }

    /*$.ajax({
        type: 'POST',
        url: '../report/taskreportcheck',
        dataType: "json",
        data: {actvtyid:dataid},
        success: function(data){
            if(data.taskreported == 'No')
            {
                alert('Activity task not reported yet')
                $('#start_date_'+dataid).val('');
            }
        }
    });*/

});

$(document).on('change','.currentqnty',function(){  
    var dataid = $(this).attr('data-id');

    /*$.ajax({
        type: 'POST',
        url: '../report/taskreportcheck',
        dataType: "json",
        data: {actvtyid:dataid},
        success: function(data){
            if(data.taskreported == 'No')
            {
                alert('Activity task not reported yet')
                $('#currentqnty'+dataid).val('');
            }
        }
    });*/

});

/*$(document).on( "click", ".edit_start_date", function(){
    var actvtyid = $(this).attr('data-id'); 
    $( "#start_date_"+actvtyid ).datepicker({ defaultDate:new Date(),changeMonth: true,
        changeYear: true,dateFormat: 'dd-mm-yy' });
});

$(document).on( "click", ".edit_start_date", function(){
    var actvtyid = $(this).attr('data-id'); 
    $( "#start_date_"+actvtyid ).datepicker({ defaultDate:new Date(),changeMonth: true,
        changeYear: true,dateFormat: 'dd-mm-yy' });
});*/




$(document).on( "click", ".clear_task_reporting", function(){
    var date = $('#select_report_date').val();
    var actvtyid = $(this).attr('data-activityid'); 
    var currcycle = $('#currcycleshowss').val(); 
    var checkstr =  confirm('Are you sure! You want to clear this?');

    if(checkstr == true)
    { 
        $.ajax({
            type: 'POST',
            url: '../report/schedulereportcleartask',
            dataType: "json",
            data: {actvtyid:actvtyid, currcycle:currcycle},
            success: function(data){
                if(data.error == 'No')

                {
                    $.ajax({
                            type: 'POST',
                            url: '../report/scheduleprogresstasklist',
                            dataType: "json",
                            data: {actid:actvtyid,date:date},
                            success: function(data){
                                if(data.error=='No')
                                {
                                    $('#taskReportData').html(data.result);
                                    applyHolidayToDatepicker(data);
                                }
                                $('.preloader').hide();
                            }
                        });
                }
                else{

                }
            }
        });
    }
    else
    {
        return false;
    }
    
});



$(document).on( "click", ".reportclear", function(){
    var actvtyid = $(this).attr('data-id'); 
    var checkstr =  confirm('Are you sure you want to clear this?');

    if(checkstr == true)
    { 
        $.ajax({
            type: 'POST',
            url: '../report/schedulereportclearactivity',
            dataType: "json",
            data: {actvtyid:actvtyid},
            success: function(data){
                if(data.error == 'No')

                {
                    $('#start_date_'+actvtyid).val('');
                    $('#cumqty'+actvtyid).html('');
                    $('#currentqnty'+actvtyid).val('');
                    $('#lastupdated'+actvtyid).html('');
                    $('#activity_pr_main').trigger('click');
                }
                else{

                }
            }
        });
    }
    else
    {
        return false;
    }
    
});




$(document).on('click','#savescheduleprogressrpt',function(e){
    e.preventDefault();
    var error=0;
    var reperror=0;
    var strterror=0;
    $('.error').hide();

    var prgresreprt = $('#select_report_date').val();

    if(prgresreprt==''){
        reperror=1;
        alert('Select report date!!!')
    }

    if(reperror==0){

        strterror=1;

        $('.edit_start_date').each(function () {
            var id = $(this).attr('data-id');
            var startdate1 = $('#start_date_'+id).val();
            var d1 = startdate1.split('-');
            var startdate = d1[2] + '-' + d1[1] + '-' + d1[0];

            var d = new Date();
            var month = d.getMonth()+1;
            var day = d.getDate();
            var today = d.getFullYear() + '-' + (month<10 ? '0' : '') + month + '-' + (day<10 ? '0' : '') + day;
            //var today = (day<10 ? '0' : '') + day + '-' + (month<10 ? '0' : '') + month + '-' + d.getFullYear();
            if(startdate1!=''){
                strterror=0;
                // if(today < startdate){
                //     error = 1;
                //     alert("You can't report before the start date of this activity")
                // }
            }
        });

         $('.edit_start_date').each(function () {
            var id = $(this).attr('data-id');
            var date2 = $('#start_date_'+id).val();
            var d2 = date2.split('-');
            var startdate = d2[2] + '-' + d2[1] + '-' + d2[0];

            var date = $('#select_report_date').val();
            var d1 = date.split('-');
            var reportdate = d1[2] + '-' + d1[1] + '-' + d1[0];
            
            if(date2!=''){
                // if(startdate>reportdate){
                // //if(startdate>date){
                //     error=1;
                //     alert("You can't report before the start date of this activity")
                // }
            }
        }); 

        $('.select_report_date').each(function () {
            var date = $('#select_report_date').val();

            var d1 = date.split('-');
            var reportdate = d1[2] + '-' + d1[1] + '-' + d1[0];

            var d = new Date();
            var month = d.getMonth()+1;
            var day = d.getDate();
            var today = d.getFullYear() + '-' + (month<10 ? '0' : '') + month + '-' + (day<10 ? '0' : '') + day;
            if(reportdate!=''){
                if(today < reportdate){
                    error = 1;
                    alert('Sorry you cannot report on future start dates!!!')
                }
            }
        });

        $('.currentqnty').each(function () {
            var id = $(this).attr('data-id');
            var currentqty = $('#currentqnty'+id).val();
            var upto_qty = $('#reportqty'+id).val();
            var bqty = $('#bqty'+id).html();
            if(upto_qty!=''){
                var uptoqty = upto_qty;
            }
            else{
                var uptoqty = 0;
            }
            var totalqty = Number(uptoqty) + Number(currentqty);

            if(currentqty!=''){
                if(Number(bqty) < Number(totalqty)){
                    error = 1;
                    alert('Budgeted quantity is smaller than total reported quantity')
                }
            }
        });

    }

    if(strterror==1){
        alert('Select activity start date')
    }
    
    if(error==0 && reperror==0 && strterror==0)
    { 
    
        $.ajax({
            type: 'POST',
            url: '../report/scheduleprogressreport',
            dataType: "json",
            async:false,
            data: $('#schedule-task').serialize(),
            success: function(data){
                if(data.error == 'No')
                {
                    //$('#act_list_pr').trigger('click');
                    /*$('#success-message-schedule').html('<div style="color:green;">Reported Successfully</div>').show().delay(3000).fadeOut();  
                    $('html, body').animate({
                        scrollTop: $("#progress_act_reprt").offset().top
                    }, 500);*/
                    $('#success-messages').show().delay(3000).fadeOut('slow', function(){
                        $('#progress_act_reprt').trigger('click');
                    }); 

                    //$('#progress_act_reprt').trigger('click');//alert('sucess');

                    //$('#progress_act_reprt').removeClass('active').next().slideUp();
                    //$('#progress_act_reprt').removeClass('active').next().slideDown();
                }
                else{

                }
            }
        });

        //$('#activity_pr_main').trigger('click');

    }

});
$(document).on('change','#currcycleshowss',function(e){
    
    var date = $("#select_report_date").val(); 
    var actid = $(this).attr('data-act');
    var cycleval = $(this).val();  
    var showselect = 1;
    
    $.ajax({
        type: 'POST',
        url: '../report/scheduleprogresstasklist',
        dataType: "json",
        async:false,
        data: {date:date,cycleval:cycleval,actid:actid},
        success: function(data){
            if(data.error == 'No')
            {
                //$('#tooltip'+actid).html(data.result);
                $('#taskReportData').html(data.result);
                applyHolidayToDatepicker(data);
            }
        }
    });

}); 




function formatTime(duration){
    //$duration = number_format((float)$duration,2);
    totMinutes = (Math.floor(duration)*60)+((duration - Math.floor(duration))*100);

    hours = (Math.floor(totMinutes/60));
    minutes = Math.round(totMinutes-(hours*60));

    return hours+'.'+minutes;

}

function getHolidayCount(startDate, endDate){
    var holidayCnt      = 0;
    if($('#report_holiday_arr').val() || $('#report_holiday_week_arr').val()){
        var holiday_arr      = ($('#report_holiday_arr').val()).split(",");
        var holiday_week_arr = ($('#report_holiday_week_arr').val()).split(",");

        var startDateTemp   = new Date(startDate);
        var endDateTemp     = new Date(endDate);
        while (startDateTemp <= endDateTemp) {
            startDateTemp = new Date(startDateTemp);
            startDateTemp.setDate(startDateTemp.getDate() + 1);

            weekNo          = new Date(startDateTemp).getDay()
            formattedDate   = formatDate(startDateTemp, 'd-m-y', true);

            if(jQuery.inArray(weekNo.toString(), holiday_week_arr) != -1 || jQuery.inArray(formattedDate, holiday_arr) != -1){
                holidayCnt++;
            }
        }
    }
    return holidayCnt;
}






$(document).on('change','.res_trade_name',function(){
        var reslabourid=$(this).attr('data-reslabourid');
        var resrate=$(this).find(':selected').attr('data-resrate');
        $('#res_trade_rate_'+reslabourid).val(resrate);
    });
