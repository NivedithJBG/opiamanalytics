  



//--- Progress Report Activity - Listing -------------------
$(document).on( "click", ".activityTab", function(){
    $.ajax({
        type: 'POST',
        url: '../report/scheduleprogressactivities_mobile',
        //data: {projectid: projectid}, 
        dataType: "json",
        success: function(data){
            if(data.error == 'No'){
                $('#activity-list').html(data.result);
                applyHolidayToDatepicker(data);
            }
        }
    });
});
//-------------------------------------------------------------



$(document).on('change','#currcycleshowss',function(e){
    
    var actid = $(this).attr('data-act');
    var cycleval = $(this).val();  
    var showselect = 1;
    
    $.ajax({
        type: 'POST',
        url: '../report/scheduleprogresstasklist_mobile',
        dataType: "json",
        async:false,
        data: {cycleval:cycleval,actid:actid},
        success: function(data){
            if(data.error == 'No')
            {
                //$('#tooltip'+actid).html(data.result);
                $('.report-progress').html(data.result);
                applyHolidayToDatepicker(data);
                        Waves.init()

            }
        }
    });

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