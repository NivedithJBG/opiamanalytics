
$(document).on( "click", "#new_act_reprt", function(){
    $('#act_pr_main').trigger('click');
});

$(function(){
    $('#act_list_pr').click(function(){
       $('#new_progressreportactivity').slideUp('slow');
       $('#new_progressreportlist').slideDown('slow');
       $('#act_list_pr').removeClass('btn-danger').addClass('btn-success');
       $('#act_pr_main').removeClass('btn-success').addClass('btn-danger');
       var projectid = $('#progrep_project').val(); 
       if(projectid!=''){
          var projectId = $('#progrep_project').val(); 
       }
       else{
          var projectId = $('#projectitemss').val(); 
       }   
       $.ajax({
           type: 'POST',
           url: '../report/ActivityReportListing/',
           dataType: "json",
           beforeSend : function(){
               $('.preloader').show();
           },
           data: {projectid:projectId},
           success: function(data){
               $('.preloader').hide();
               if(data.error=='No')
               {
                   $("#new_pr_listing").html(data.list);
                   $("#projectitemss").html(data.projectlist);
               }
           }
       });
    });
    $('#act_pr_main').click(function(){
        var scheduleitemid=$('#progrepiowlist').val();
        var inputdate = $('#current_date').val();
        $.ajax({
            type: 'POST',
            url: '../report/newprogressactivities',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {inputdate:inputdate},
            success: function(data){
                if(data.error=='No')
                {
                    $('#progrep_project').html(data.proid);
                    $('#activitytaskitems').html(data.result);
                    //$('#progrepactivity').html(data.scheduleactivities);
                }
                $('.preloader').hide();
            }
        });
    });
});
$(document).on('change','#progrep_project',function(){
    $('#act_pr_main').trigger('click');
});

$(document).on('change','#progrepiowlist',function(){
    var inputdate = $('#current_date').val();
    var scheduleitemid=$(this).val();
    var projectid=$('#progrep_project').val();
    $.ajax({
        type: 'POST',
        url: '../report/NewProgressActivities',
        data: {projectid:projectid,scheduleitemid:scheduleitemid,inputdate:inputdate},
        dataType: "json",
        success: function(data){
            if(data.error=='No')
            {
                $('#activitytaskitems').html(data.tasklist);
                //$('#iowunit').html(data.iowunit);
                //$('#iowquantity').html(data.iowqty);
            }
        }
    });
});

$(document).on('change','.edit_current_date',function(){
    var inputdate = $(this).val();
    var projectid=$('#progrep_project').val();
    var iowid=$('#progrepiowlist').val(); 
    $.ajax({
        type: 'POST',
        url: '../report/reportdatechange',
        data: {inputdate:inputdate},
        dataType: "json",
        success: function(data){
            if(data.error=='No')
            {
                $('#activitytaskitems').html(data.result);
                //$('#iowunit').html(data.iowunit);
                //$('#iowquantity').html(data.iowqty);
            }
        }
    });

});

$(document).on('change','#projectitemss',function(){
    
       var projectId = $('#projectitemss').val();   
       $.ajax({
           type: 'POST',
           url: '../report/ActivityReportListing/',
           dataType: "json",
           beforeSend : function(){
               $('.preloader').show();
           },
           data: {projectid:projectId},
           success: function(data){
               $('.preloader').hide();
               if(data.error=='No')
               {
                   $("#new_pr_listing").html(data.list);
                   $("#projectitemss").html(data.projectlist);
               }
           }
       });

});

$(document).on('click','#selectedreport',function(){
    
    var iowid = $(this).attr('data-iowid');    
    var inputdate = $(this).val(); 
    var projectid= $(this).attr('data-projectid'); 
    
    $('#new_progressreportactivity').slideDown('slow');
    $('#new_progressreportlist').slideUp('slow');
    $('#progressreportlist').slideUp('slow');
    $('#act_pr_main').removeClass('btn-danger').addClass('btn-success');
    $('#act_list_pr').removeClass('btn-success').addClass('btn-danger');
    
    $.ajax({
        type: 'POST',
        url: '../report/ReportDateChange',
        data: {projectid:projectid,itemid:iowid,inputdate:inputdate},
        dataType: "json",
        success: function(data){
            if(data.error=='No')
            {
                $('#current_date').val(data.date);
                $('#progrep_project').val(projectid);
                $('#progrepiowlist').val(iowid);
                $('#activitytaskitems').html(data.activityrow);
                $('#progrepiowlist').html(data.itemsnames);
                $('#iowunit').html(data.iowunit);
                $('#iowquantity').html(data.iowqty);
                //$('#act_pr_main').trigger('click');
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
            $('#act_list_pr').trigger('click');
            //$('#structurenameslist').val(0);
            //$('#structurenameslist').trigger('change');

        }
        else{
            $('#progress1').trigger('click');
        }
    }

    // var tdyqty = $('#resourceform-activity').serialize();

});

$(document).on('click','#savenewprogressrpt_new',function(e){
    e.preventDefault();
    var error=0;
    $('.error').hide();

    var data = $('#current_date').val();
    var arr = data.split('-');
    var currentdate = arr[2] + '-' + arr[1] + '-' + arr[0];
    var startDate = new Date(currentdate);
    var endDate = new Date();

    if(startDate<=endDate){
        error=0;
    }
    else if(startDate>endDate){
        error=1;
        alert('Sorry you cannot report on future start dates!!!')
    }
    
    if(error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../report/estimateactivityreport',
            dataType: "json",
            data: $( "#activity-task" ).serialize(),
            success: function(data){
                if(data.error == 'No')
                {
                  //$('#act_list_pr').trigger('click');

                $('#success-message').html('<div style="text-align: center;color:green;"><h5>Reported Successfully</h5></div>').fadeIn('slow');
                $('#success-message').delay(5000).fadeOut('slow'); 

                 
                //   $('html, body').animate({
                //         scrollTop: $("#new_act_reprt").offset().top
                //     }, 500);
                }else if(data.error == 'Yes'){


                    $('#success-message').html('<div style="text-align: center;color:green;"><h5>The reporting date should be greater than work order date</h5></div>').fadeIn('slow');
                    $('#success-message').delay(5000).fadeOut('slow');
                   
                    
                }else if(data.error == 'billed'){

                    $('#success-message').html('<div style="text-align: center;color:green;"><h5>Cannot edit the quantity as it is already billed </h5></div>').fadeIn('slow');
                    $('#success-message').delay(5000).fadeOut('slow');

                }
                else{
                    return  false;
                }
            }
        });
        
    }else{
        //alert("You have to enter all values for reporting");
        return  false;
    }

});

$(document).on('click','#cancel_savenewprogressrpt',function(){
    //$('#progress1').trigger('click');
    $('#act_list_pr').trigger('click');
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

$(document).on('click','.activityreportcomplete',function(){

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
                $('#act_pr_main').trigger('click');

            }
            
        }
    });



});

$(document).on('click','.btn-hist',function(){
    $.ajax({
        type: 'POST',
        url: '../report/actreportcompletedhistory',
        dataType: "json",
        async:false,
        data: {},
        success: function(data){
            if(data.error == 'No')
            {
                $('#activitytaskitems').hide();
                $('#activitycompletehist').show();
                $('.actheads').hide();
                $('.btn-hist').hide();
                $('.btn-back').show();
                
                $('#activitycompletehist').html(data.result);

            }
            
        }
    });

});

$(document).on('click','#act_pr_back',function(){
    $('#activitytaskitems').show();
    $('#activitycompletehist').hide();
    $('.actheads').show();
    $('.btn-hist').show();
    $('.btn-back').hide();
    $('#act_pr_main').trigger('click');
});

