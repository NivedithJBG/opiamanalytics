
$(document).on( "click", "#progress_act_reprt", function(){
    var d = new Date();
    var strDate = d.getDate() + "-" + (month<10 ? '0' : '') +(d.getMonth()+1) + "-" + d.getFullYear();

    var d = new Date();
    var month = d.getMonth()+1;
    var day = d.getDate();
    var year = d.getFullYear();
    var strDate = (day<10 ? '0' : '') + day + "-" + (month<10 ? '0' : '') + month + "-" + year;
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
                    $('#scheduleactivityitems').html(data.result);
                    $("#pgrsrpt").css("display", "block");
                    
                }
                $('.preloader').hide();
            }
        });
    });
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

$(document).on('click','.taskreport',function(){
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
});

$(document).on('click','.cleartaskdet',function(){

    var taskid = $(this).val();
    var re_date = $('#select_report_date').val();
    var actid = $(this).attr('data-id');

    var r = confirm("Are you sure you want to Clear this Task ?");

    if (r == true) {

        $.ajax({
            type: 'POST',
            url: '../report/scheduletaskclear',
            dataType: "json",
            data: {actid:actid,re_date:re_date,taskid:taskid},
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

$(document).on('click','.savetaskprogressrpt',function(e){
    e.preventDefault();
    var actid = $(this).attr('data-id');
    var date = $('#select_report_date').val();
    var error=0;
    $('.error').hide();
    var count = 0;
    var diff = 0;

    $('.taskactdur').each(function () {
        var id = $(this).attr('data-id');
        var duration = $('#taskactdur'+id).val();
        if(duration!='')
            count = 1;
    });

    $('.taskactdur').each(function () {
        var id = $(this).attr('data-id');
        var duration = $('#taskactdur'+id).val();
        var strt_time = $('#taskstart_time'+id).val();
        var end_time = $('#taskend_time'+id).val();
        var strtdate = $('#task_date'+id).val(); 
        var enddate  = $('#task_enddate'+id).val();

        if(duration!='')
            count = 1;

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
    });


    if(count==0)
        alert('Please enter start and end time')
    else{

        if(diff == 1)
            alert('Start time is greater than end time!')
        else{

            $.ajax({
                type: 'POST',
                url: '../report/scheduletaskreporting',
                dataType: "json",
                async:false,
                data: $('#schedule-task-reporting'+actid).serialize()+"&date="+date,
                success: function(data){
                    if(data.error == 'No')
                    {
                        $('#task-success-messages'+actid).show().delay(5000).fadeOut(); 
                    }
                    else{
    
                    }
                }
            });
        }
    }

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
        var hours =  hours4 + "." + minutes;  

       /* //var difference = end_time - start_time;
        var hours = 0;
        var timeStart = new Date("01/01/2021 " + start_time);
        var timeEnd = new Date("01/01/2021 " + end_time);
        var hours1 = Math.abs(timeEnd - timeStart)/36e5;
        var hours2 = parseFloat(hours1)*60;
        var hours3 = Math.floor(hours2 / 60); 
        var minutess = hours2 % 60;
        var minutes = minutess.toFixed(0);
        var hours =  hours3 + "." + minutes; 
       // var hours = hours.toFixed(2);
        //var hours =  hours4 + " : " + minutes; */


        $('#taskactdur'+id).val(hours);

        var totdur = 0;

        $('.taskactdur').each(function () {
            var id =$(this).attr('data-id');
            
            var dur = $('#taskactdur'+id).val();
            if(dur == '')
            {
                val = 0;
            }else{
                val = dur;
            }
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
        if(final_end_time!='' && final_end_time!=undefined && nxt_start_time!='' && nxt_start_time!=undefined ){


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
            var hours =  hours4 + "." + minutes; 
        
            $('#taskwastedur'+nextid).val(hours);

            var totdur = 0;

            $('.taskwastedur').each(function () {
                var id =$(this).attr('data-id');
                
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
    }

});


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
});

$(document).on('change','.taskstart_time',function(){  
    var id = $(this).attr('data-id');
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

        //var difference = end_time - start_time;

        /*var hours = 0;
        var timeStart = new Date("01/01/2021 " + start_time);
        var timeEnd = new Date("01/01/2021 " + end_time);
        var hours1 = Math.abs(timeEnd - timeStart)/36e5;
        var hours2 = parseFloat(hours1)*60;
        var hours3 = Math.floor(hours2 / 60); 
        var minutess = hours2 % 60;
        var minutes = minutess.toFixed(0);
        var hours =  hours3 + "." + minutes;  */
      //alert(hours.toFixed(3)) 

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
        var hours =  hours4 + "." + minutes; 

        $('#taskactdur'+id).val(hours);

        var totdur = 0;

        $('.taskactdur').each(function () {
            var id =$(this).attr('data-id');
            
            var dur = $('#taskactdur'+id).val();
            if(dur == '')
            {
                val = 0;
            }else{
                val = dur;
            }
            totdur = parseFloat(totdur) + parseFloat(val);
           

        });
        $('#totvdurr').html(totdur.toFixed(2));
        



    }

    //waste hour calcualtion
    var lastid = parseFloat(id)-1; 
    var cycle_no = $('#currcycleshowss').val();
    var first_taskid = $('#first_task').val(); 
    var last_taskid = $('#last_task').val(); 
    var actid = $('#activityid').val(); 
    var sel_date = $('#sel_date').val(); 

   
    //var nextid = parseFloat(id)+1;
    var lastid = parseFloat(id)-1; 
    var final_strt_time = $('#taskstart_time'+id).val(); //end  -next task strt time
    var last_end_time = $('#taskend_time'+lastid).val(); 

    if(final_strt_time!='' && final_strt_time!=undefined && last_end_time!='' && last_end_time!=undefined )
    { 
        var date11 = $('#task_enddate'+lastid).val();       
        const [dayss, monthss, yearss] = date11.split('-');
        const xyzz = new Date(+yearss, monthss - 1, +dayss);  
        dateStrings = new Date(xyzz).toUTCString(); 
        date1 = dateStrings.split(' ').slice(0, 4).join(' '); 

        var date12 = $('#task_date'+id).val();   
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
            var hours =  hours4 + "." + minutes; 
        
            $('#taskwastedur'+id).val(hours);
            var totdur = 0;

            $('.taskwastedur').each(function () {
                var id =$(this).attr('data-id');
                
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
    else if(last_end_time=='' || last_end_time==undefined)
    { 
        var id = $(this).attr('data-id');
        var lastids = parseFloat(id)-1;  
        if(lastids>=first_taskid)
        {
            var lastid = lastids;  
        }
        else
        {
            var lastid = $('#last_task').val(); 
        }
       
        //for getting last task end date&time- if multiple cycle - take earliest one
        var start_dates = $('#task_date'+id).val(); 
        var strt_times = $('#taskstart_time'+id).val(); 
        $.ajax({
            type: 'POST',
            url: '../report/gettasktime', 
            dataType: "json",
            data: {taskid:lastid,actid:actid,start_dates:start_dates,strt_times:strt_times},
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
                        
                        if(final_strt_time!='' && final_strt_time!=undefined && last_end_time!='' && last_end_time!=undefined)
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
                            var hours =  hours4 + "." + minutes;   

                            $('#taskwastedur'+id).val(hours);
                            $('#taskwastedursts'+id).val(data.taskid);
                        }

                        var totwastedur = 0;
                        $('.taskwastedur').each(function () {
                            var ids =$(this).attr('data-id');
                            
                            var dur = $('#taskwastedur'+ids).val();
                            if(dur == '')
                            {
                                val = 0;
                            }else{
                                val = dur;
                            }
                            totwastedur = parseFloat(totwastedur) + parseFloat(val);
                        

                        });
                        $('#totwdurr').html(totwastedur.toFixed(2));

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
                    }
                    else
                    {
                        //alert("No data found for wasted hour calculation");
                    }

                }
                else if(data.error=='Yes')
                {
                    alert("No data found for wasted hour calculation");
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
    /*var date = $('#select_report_date').val();
    var arr = date.split('-');
    var reportdate = arr[2] + '-' + arr[1] + '-' + arr[0];*/
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
        if(startdate>today){
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
                    $('#success-messages').show().delay(5000).fadeOut(); 
                    //$('#progress_act_reprt').trigger('click');alert('sucess');

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
                $('#tooltip'+actid).html(data.result);
            }
        }
    });

}); 