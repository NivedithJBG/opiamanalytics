
$(document).on( "click", "#progress1", function(){
    
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }
    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }
    var projectid=$('#projectitemss').val();
    $('#structurenameslist').html(getStructurelist(projectid));
    $('#list_pr').trigger('click');
});

$(function(){
   $('#list_pr').click(function(){
       $('#progressreportactivity').slideUp('slow');
       $('#progressreportlist').slideDown('slow');
       $('#list_pr').removeClass('btn-danger').addClass('btn-success');
       $('#pr_main').removeClass('btn-success').addClass('btn-danger');
       $.ajax({
           type: 'POST',
           url: '../report/ProgressReportList/',
           dataType: "json",
           beforeSend : function(){
               $('.preloader').show();
           },
           data: {structureid:$('#structurenameslist').val(),projectid:$('#projectitemss').val()},
           success: function(data){
               $('.preloader').hide();
               if(data.error=='No')
               {
                   $("#pr_listing").html(data.list);
               }
           }
       });
   });
    $('#pr_main').click(function(){
        $('#progressreportactivity').slideDown('slow');
        $('#progressreportlist').slideUp('slow');
        $('#pr_main').removeClass('btn-danger').addClass('btn-success');
        $('#list_pr').removeClass('btn-success').addClass('btn-danger');
        //var structureid=$('#projectitemss').val();
        $.ajax({
            type: 'POST',
            url: '../report/ProgressActivities',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectid:$('#progrep_project').val()},
            success: function(data){
                if(data.error=='No')
                {
                    $('#progrepwbsstructlist').html(data.structurenames);
                    //$('#progrepiowlist').html(data.scheduleitemsnames);
                    //$('#progrepactivity').html(data.scheduleactivities);
                }
                $('.preloader').hide();
            }
        });
    });
});
$(document).on('change','#projectitemss',function(){
    var projectid=$(this).val();
    $('#structurenameslist').html(getStructurelist(projectid));
    $('#list_pr').trigger('click');
});
$(document).on('change','#structurenameslist',function(){
    $('#list_pr').trigger('click');
});
$(document).on('change','#progrep_project',function(){
    $('#pr_main').trigger('click');
});
$(document).on('change','#progrepwbsstructlist',function(){
    var structureid=$(this).val();
    var projectid=$('#progrep_project').val();
    $.ajax({
        type: 'POST',
        url: '../report/ProgressActivities',
        data: {structureid:structureid,projectid:projectid},
        dataType: "json",
        success: function(data){
            if(data.error=='No')
            {
                $('#progrepiowlist').html(data.scheduleitemsnames);
            }
        }
    });
});
$(document).on('change','#progrepiowlist',function(){
    var scheduleitemid=$(this).val();
    var projectid=$('#progrep_project').val();
    var structureid=$('#progrepwbsstructlist').val();
    $.ajax({
        type: 'POST',
        url: '../report/ProgressActivities',
        data: {structureid:structureid,projectid:projectid,scheduleitemid:scheduleitemid},
        dataType: "json",
        success: function(data){
            if(data.error=='No')
            {
                $('#progrepactivity').html(data.scheduleactivities);
                $('#activitytaskitems').html(data.tasklist);
            }
        }
    });
});

$(document).on('change','#progrepactivity',function(){
    var scheduleactvtyid=$(this).val();
    var projectid=$('#progrep_project').val();
    $.ajax({
        type: 'POST',
        url: '../report/ProgressReport',
        /*beforeSend : function(){
            $('.preloader').show();
        },*/
        data: {activityid:scheduleactvtyid,projectid:projectid},
        dataType: "json",
        success: function(data){
            if(data.error=='No')
            {
                //$('.preloader').hide();
                $("#progrepunit").html(data.activityunit);
                $("#progrepsdate").html(data.startdate);
                $("#progrepedate").html(data.enddate);
                //$("#editquantity_new").val(data.workdone);
                var start_date = $('#editactualstartdate_new').val();
                $('#current_date').attr('min' , start_date);
                $('#activitytaskitems').html(data.tasklist);
                $('#progrepdates').html(data.reporteddate);
                //$('#Cumquantity').html(data.cqwd);
            }
        }
    });
});
$(document).on('change','.edit_current_date',function(){
    var inputdate = $(this).val();
    var activityid=$('#progrepactivity').val();

    var start_date = $('#editactualstartdate_new').val();
    $('#current_date').attr('min' , start_date);
    var d = new Date();

    var month = d.getMonth()+1;
    var day = d.getDate();

    /*  var currentdate = d.getFullYear() + '-' +
     (month<10 ? '0' : '') + month + '-' +
     (day<10 ? '0' : '') + day; */

    var error=0;

    var currentdate = $('#current_date').val();

    if(new Date(currentdate) < new Date(inputdate))
    {
        alert('You cannot select future date');
        error=1;
    }
    else{
        error=0;
    }

    if(error ==0){

        $.ajax({
            type: 'POST',
            url: '../report/OldReportDate',
            dataType: "json",
            data: {inputdate:inputdate,activityid:activityid},
            success: function(data){
                if(data.error=='No')
                {
                    $('#editquantity_new').val(data.quantity);
                    $('#Cumquantity_new').val(data.cumltdqty);
                    $('#Cumquantity').html(data.cumltdqty);
                    $('#activitytaskitems').html(data.tasklist);

                    var quantity = $('#editquantity_new').val();
                    var cumquantity = $('#Cumquantity_new').val();
                    if(quantity!=''){
                        var startdate = $('#editactualstartdate_new').val();
                        var today = new Date(inputdate);
                        var currentdate = Date.parse(today);
                        var startdate1 = Date.parse(startdate);
                        var timeDiff = currentdate - startdate1;
                        daysDiff = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
                        //console.log(daysDiff);
                        var budgetedqty = $('#budgetedqty_new').val();
                        if(cumquantity!=''){
                            var Cumulatedquantity = $('#Cumquantity_new').val();
                        }
                        else{
                            var Cumulatedquantity = 0;
                        }
                        var totalqty = (+Cumulatedquantity) + (+quantity);
                        var totaldays = ((daysDiff+1)/totalqty) * budgetedqty;
                        if(totaldays!=''){
                            var startDate = new Date($('#editactualstartdate_new').val());
                            var newdate = new Date(startDate).setDate(startDate.getDate() + (+totaldays) - 1);
                            var endDate1 = new Date(newdate);
                            var tempoMonth = (endDate1.getMonth() + 1);
                            if (tempoMonth < 10) tempoMonth = '0' + tempoMonth;
                            var tempoDate = (endDate1.getDate());
                            if (tempoDate < 10) tempoDate = '0' + tempoDate;
                            var endDate = tempoDate + '-' + tempoMonth + '-' + endDate1.getFullYear();
                            var realendDate = endDate1.getFullYear() + '-' + tempoMonth + '-' + tempoDate;
                        }
                        $('#actualenddate_new').html(endDate);
                    }
                }
                else
                {
                    // alert(data.errortext);
                }

            }
        });

    }

});

$(document).on('change','.editquantity_new',function(){
    var activityid=$('#progrepactivity').val();
    var quantity = $('#editquantity_new').val();
    var cumquantity = $('#Cumquantity_new').val();
    if(quantity!=''){
        var startdate = $('#editactualstartdate_new').val();
        //var today = new Date();
        var today = $('#current_date').val();
        var currentdate = Date.parse(today);
        var startdate1 = Date.parse(startdate);
        var timeDiff = currentdate - startdate1;
        daysDiff = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
        //console.log(daysDiff);
        var budgetedqty = $('#budgetedqty_new').val();
        if(cumquantity!=''){
            var Cumulatedquantity = $('#Cumquantity_new').val();
        }
        else{
            var Cumulatedquantity = 0;
        }
        var totalqty = (+Cumulatedquantity) + (+quantity);
        var totaldays = ((daysDiff+1)/totalqty) * budgetedqty;
        if(totaldays!=''){
            var startDate = new Date($('#editactualstartdate_new').val());
            var newdate = new Date(startDate).setDate(startDate.getDate() + (+totaldays) - 1);
            var endDate1 = new Date(newdate);
            var tempoMonth = (endDate1.getMonth() + 1);
            if (tempoMonth < 10) tempoMonth = '0' + tempoMonth;
            var tempoDate = (endDate1.getDate());
            if (tempoDate < 10) tempoDate = '0' + tempoDate;
            var endDate = tempoDate + '-' + tempoMonth + '-' + endDate1.getFullYear();
            var realendDate = endDate1.getFullYear() + '-' + tempoMonth + '-' + tempoDate;
        }
        $('#actualenddate_new').html(endDate);
    }
});

$(document).on('change','.edit_actual_start_date_new',function(){
    var activityid=$('#progrepactivity').val();
    var quantity = $('#editquantity_new').val();
    var cumquantity = $('#Cumquantity_new').val();
    var startdate = $('#editactualstartdate_new').val();
    $('#current_date').attr('min' , startdate);
    if(quantity!=''){
        var today = new Date();
        var currentdate = Date.parse(today);
        var startdate1 = Date.parse(startdate);
        var timeDiff = currentdate - startdate1;
        daysDiff = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
        //console.log(daysDiff);
        var budgetedqty = $('#budgetedqty_new').val();
        if(cumquantity!=''){
            var Cumulatedquantity = $('#Cumquantity_new').val();
        }
        else{
            var Cumulatedquantity = 0;
        }
        var totalqty = (+Cumulatedquantity) + (+quantity);
        var totaldays = ((daysDiff+1)/totalqty) * budgetedqty;
        if(totaldays!=''){
            var startDate = new Date($('#editactualstartdate_new').val());
            var newdate = new Date(startDate).setDate(startDate.getDate() + (+totaldays) - 1);
            var endDate1 = new Date(newdate);
            var tempoMonth = (endDate1.getMonth() + 1);
            if (tempoMonth < 10) tempoMonth = '0' + tempoMonth;
            var tempoDate = (endDate1.getDate());
            if (tempoDate < 10) tempoDate = '0' + tempoDate;
            var endDate = tempoDate + '-' + tempoMonth + '-' + endDate1.getFullYear();
        }
        $('#actualenddate_new').html(endDate);
        //$('#editactualenddate_new'+activityid).val(endDate);
    }
});

$(document).on('click','#actvtylistreport',function(){
    $('#progressreportactivity').slideDown('slow');
    $('#progressreportlist').slideUp('slow');
    $('#pr_main').removeClass('btn-danger').addClass('btn-success');
    $('#list_pr').removeClass('btn-success').addClass('btn-danger');
    var id = $(this).val();
    var dataid = $(this).attr('data-id');
    var projectid=$('#projectitemss').val();
    var structureid=$('#structurenameslist').val();
    $('#progrep_project').val(projectid);
    $.ajax({
        type: 'POST',
        url: '../report/ProgressActivities',
        data: {scheduleitemid:dataid,structureid:structureid,projectid:projectid,activityid:id},
        dataType: "json",
        success: function(data){
            if(data.error=='No')
            {
                $('#progrepwbsstructlist').html(data.structurenames);
                $('#progrepiowlist').html(data.scheduleitemsnames);
                $('#progrepactivity').html(data.scheduleactivities);
                $.ajax({
                    type: 'POST',
                    url: '../report/ProgressReport',
                    /*beforeSend : function(){
                     $('.preloader').show();
                     },*/
                    data: {activityid:id,projectid:projectid,scheduleitemid:dataid},
                    dataType: "json",
                    success: function(data){
                        if(data.error=='No')
                        {
                            //$('.preloader').hide();
                            $("#progrepunit").html(data.activityunit);
                            $("#progrepsdate").html(data.startdate);
                            $("#progrepedate").html(data.enddate);
                            //$("#editquantity_new").val(data.workdone);
                            var start_date = $('#editactualstartdate_new').val();
                            $('#current_date').attr('min' , start_date);
                            $('#activitytaskitems').html(data.tasklist);
                            $('#progrepdates').html(data.reporteddate);
                            //$('#Cumquantity').html(data.cqwd);
                        }
                    }
                });
            }
        }
    });
});

$(document).on('click','#savedraftprogressrpt_new',function(){
    var id=$('#progrepactivity').val();
    var startdate = $('#editactualstartdate_new').val();
    var enddate = $('#editactualenddate_new').val();
    var todayquantity = $('#editquantity_new').val();
    var cumquantity = $('#Cumquantity_new').val();
    var quantity = (+cumquantity) + (+todayquantity);
    var status = $('#editreportstatus_new').val();
    var inputdate = $('#current_date').val();
    var error=0;
    var mode = $(this).attr('mode');
    $('.error').hide();

    var startDate = new Date($('#editactualstartdate_new').val());
    var endDate = new Date();

    if(startDate<=endDate){
        error=0;
    }
    else if(inputdate>endDate){
        error=1;
        alert('Sorry you cannot report on future start dates!!!')
    }
    else{
        error=1;
        alert('Sorry you cannot report on future start dates!!!')
    }

    if(todayquantity==''){
        $('#editquantity_new').next("span").html('Enter Qty').show().delay(3000).fadeOut();
        error=1;
    }

    var todaydate = $('#current_date').val();
    if(todaydate==''){
        error=1;
        alert("Sorry date cannot be null!!!")
    }

    if(error==0)
    {

        if(startdate!='' && enddate!='' && quantity!=''){
            $.ajax({
                type: 'POST',
                url: '../report/ProgressReportEdit',
                beforeSend : function(){
                    $('#savedraftprogressrpt_new').attr("disabled", true);
                },
                dataType: "json",
                data: {activityid: id, startdate: startdate, enddate: enddate, quantity: quantity,todayquantity:todayquantity, status: status, mode: mode,inputdate: inputdate},
                success: function(data){
                    if(data.error=='No')
                    {
                        $.ajax({
                            type: 'POST',
                            url: '../report/ReportTaskNewLog',
                            dataType: "json",
                            async:false,
                            data: $('#activity-task').serialize(),
                            success: function(data){
                                if(data.error == 'No')
                                {
                                    // alert('true');

                                }
                                else{

                                }
                            }
                        });
                        // $('#progress1').trigger('click');

                    }
                    else if(data.error=='Exist')
                    {
                        $.ajax({
                            type: 'POST',
                            url: '../report/ReportTaskNewLog',
                            dataType: "json",
                            async:false,
                            data: $('#activity-task').serialize(),
                            success: function(data){
                                if(data.error == 'No')
                                {
                                    // alert('true');

                                }
                                else{

                                }
                            }
                        });
                        // $('#progress1').trigger('click');

                    }
                    else
                    {
                        alert(data.errortext);
                    }
                    //$('#saveactivityedit').attr("disabled", false);
                }
            });
            $('#list_pr').trigger('click');
            //$('#structurenameslist').val(0);
            //$('#structurenameslist').trigger('change');

        }
        else{
            $('#progress1').trigger('click');
        }
    }

    // var tdyqty = $('#resourceform-activity').serialize();

});

$(document).on('click','#savenewprogressrpt_new',function(){
    var id=$('#progrepactivity').val();
    var startdate = $('#editactualstartdate_new').val();
    var enddate = $('#editactualenddate_new').val();
    var todayquantity = $('#editquantity_new').val();
    var cumquantity = $('#Cumquantity_new').val();
    var quantity = (+cumquantity) + (+todayquantity);
    var status = $('#editreportstatus_new').val();
    var inputdate = $('#current_date').val();
    var error=0;
    $('.error').hide();

    var startDate = new Date($('#editactualstartdate_new').val());
    var endDate = new Date();

    if(startDate<=endDate){
        error=0;
    }
    else if(inputdate>endDate){
        error=1;
        alert('Sorry you cannot report on future start dates!!!')
    }
    else{
        error=1;
        alert('Sorry you cannot report on future start dates!!!')
    }

    if(todayquantity==''){
        $('#editquantity_new').next("span").html('Enter Qty').show().delay(3000).fadeOut();
        error=1;
    }


    var todaydate = $('#current_date').val();
    if(todaydate==''){
        error=1;
        alert("Sorry date cannot be null!!!")
    }

    if(error==0)
    {

        if(startdate!='' && enddate!='' && quantity!=''){
            $.ajax({
                type: 'POST',
                url: '../report/ProgressReportEdit',
                beforeSend : function(){
                    $('#saveactivityedit').attr("disabled", true);
                },
                dataType: "json",
                data: {activityid: id, startdate: startdate, enddate: enddate, quantity: quantity, todayquantity: todayquantity, status: status,inputdate: inputdate},
                success: function(data){
                    if(data.error=='No')
                    {
                        $.ajax({
                            type: 'POST',
                            url: '../report/ReportTaskNew',
                            dataType: "json",
                            async:false,
                            data: $('#activity-task').serialize(),
                            success: function(data){
                                if(data.error == 'No')
                                {
                                    // alert('true');

                                }
                                else{

                                }
                            }
                        });
                        //$('#progress1').trigger('click');
                        //$('#list_pr').trigger('click');

                    }
                    else if(data.error=='Exist')
                    {
                        $.ajax({
                            type: 'POST',
                            url: '../report/ReportTaskNew',
                            dataType: "json",
                            async:false,
                            data: $('#activity-task').serialize(),
                            success: function(data){
                                if(data.error == 'No')
                                {
                                    // alert('true');

                                }
                                else{

                                }
                            }
                        });
                        // $('#progress1').trigger('click');
                        //$('#list_pr').trigger('click');

                    }
                    else
                    {
                        alert(data.errortext);
                    }
                    $('#list_pr').trigger('click');
                    //$('#saveactivityedit').attr("disabled", false);
                }
            });


        }
        else{
            $('#progress1').trigger('click');
        }
    }

    // var tdyqty = $('#resourceform-activity').serialize();

});

$(document).on('click','#cancel_savenewprogressrpt',function(){
    //$('#progress1').trigger('click');
    $('#list_pr').trigger('click');
});
$(document).on('click','.hover',function(){
    var tooltip=$(this).attr('data-tooltip');
    $('.tooltiptable').hide();
    $('#'+tooltip).fadeIn('fast');
    // alert('#'+tooltip);
});
$(document).on('mouseleave','.hover',function(){
    var tooltip=$(this).attr('data-tooltip');
    $('#'+tooltip).fadeOut('slow');
});
function getStructurelist(projectid)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../report/Structurelist/',
        dataType: "json",
        async:false,
        data: {projectid:$('#projectitemss').val()},
        success: function(data){
            retval=data.structurenames;
        }
    });
    return retval;
}


