$(document).on( "click", "#actvtyrprt", function(){
    if(!$(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        //$(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }

    if( $(this).next().is(':hidden') ) { //If immediate next container is closed...

        $('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container

        $(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container

    }
    
    /*var id= $('#projectitems').val();
    $('#projid').val(id);
    $('#projectname1').html(getProjectname(id)); 
    $('#selectedProjectId').val(id);
    $('#progressiow').val('none');
    $('#progresssearchdiv').show();
    $('#progreportdiv').hide();
    $('#activityreport-new').trigger('click') ;
    $('#ar_main').trigger('click');*/
    
    var projectid = $('#projectitems').val();
        $('.estimatestructurelist').show();
        var structureid = $('#estimatestructurelist').val();
        $.ajax({
            type: 'POST',
            url: '../report/NewActivityReport',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectid:projectid,structureid:structureid},
            success: function(data){
                if(data.error=='No')
                {
                    //$('#progressactivitylist').show();
                    $('#progreportdiv').hide();
                    //$('#editreportactivities').hide();
                    $('#progressactivityitems').html(data.result);
                    $('#estimatestructurelist').html(data.structurenames);
                    $('#estimateitemslist').html(data.estimateitemsnames);
                    $('#estimateactivitylist').html(data.estimateactivities);
                    $('.newactivityprogressreport').trigger('click');
                    //$('#progressactivitytable').show();
                }

                $('.preloader').hide();
            }
        });
});
$(document).on('change','#projectitems',function(){
     var projectid = $('#projectitems').val();
        $('.estimatestructurelist').show();
        var structureid = $('#estimatestructurelist').val();
        $.ajax({
            type: 'POST',
            url: '../report/NewActivityReport',
            beforeSend : function(){
                $('.preloader').show();
            },
            dataType: "json",
            data: {projectid:projectid,structureid:structureid},
            success: function(data){
                if(data.error=='No')
                {
                    //$('#progressactivitylist').show();
                    $('#progreportdiv').hide();
                    //$('#editreportactivities').hide();
                    $('#progressactivityitems').html(data.result);
                    $('#estimatestructurelist').html(data.structurenames);
                    $('#estimateitemslist').html(data.estimateitemsnames);
                    $('#estimateactivitylist').html(data.estimateactivities);
                    $('.newactivityprogressreport').trigger('click');
                    //$('#progressactivitytable').show();
                }

                $('.preloader').hide();
            }
        }); 
});

$(document).on('change','#estimatestructurelist',function(){
    var structureid=$(this).val();
    var projectid= $('#projectitems').val();
    var activityid = $('#estimateactivitylist').val();
    if($("#list_ar").hasClass( "btn-success" )){
       $('#list_ar').trigger('click');
     }
    $.ajax({
        type: 'POST',
        url: '../report/NewActivityReport',
        data: {structureid:structureid,projectid:projectid},
        dataType: "json",
        success: function(data){
            if(data.error=='No')
            {
                //$('#progressactivitylist').show();
                $('#progreportdiv').hide();
                //$('#editreportactivities').hide();
                $('#progressactivityitems').html(data.result);
                $('#estimatestructurelist').html(data.structurenames);
                $('#estimateitemslist').html(data.estimateitemsnames);
                $('#estimateactivitylist').val(activityid);
                $('.newactivityprogressreport').trigger('click');
                $('#estimateactivitylist').html(data.estimateactivities);
                //$('#progressactivitytable').show();
                //$('#activityreportactivity').hide();
            }

            $('.preloader').hide();
        }
    });
});

$(document).on('change','#estimateitemslist',function(){
    var estimateitemid=$(this).val();
    var projectid= $('#projectitems').val();
    var structureid=$('#estimatestructurelist').val();
    var activityid = $('#estimateactivitylist').val();
    $.ajax({
        type: 'POST',
        url: '../report/NewActivityReport',
        data: {estimateitemid:estimateitemid,structureid:structureid,projectid:projectid},
        dataType: "json",
        success: function(data){
            if(data.error=='No')
            {
                $('#progressactivitylist').show();
                $('#progreportdiv').hide();
                //$('#editreportactivities').hide();
                $('#progressactivityitems').html(data.result);
                $('#estimatestructurelist').html(data.structurenames);
                $('#estimateitemslist').html(data.estimateitemsnames);
                $('#estimateactivitylist').val(activityid);
                $('.newactivityprogressreport').trigger('click');
                $('#estimateactivitylist').html(data.estimateactivities);
                $('#progressactivitytable').show();
                //$('#activityreportactivity').hide();
            }

            $('.preloader').hide();
        }
    });
});

$(document).on('change','#estimateactivitylist',function(){
    var estimateactvtyid=$(this).val();
    var projectid=$('#selectedProjectId').val();    
    $('#newactivityprogressreport').val(projectid);
    $('#newactivityprogressreport').attr('data-id' , estimateactvtyid);
    $('.newactivityprogressreport').trigger('click');
});

$(document).on('click','.newactivityprogressreport',function(){
    var error=0;
    $('.error').hide();
    
        var activityid=$(this).attr('data-id');
        var projectid= $('#projectitems').val();
        $.ajax({
            type: 'POST',
            url: '../report/NewActivityProgressReport',
            beforeSend : function(){
                $('.preloader').show();
            },
            data: {activityid:activityid,projectid:projectid},
            dataType: "json",
            success: function(data){
             if(data.error=='No')
             {
                 $('.preloader').hide();
                 $('.all-activities').hide();
                 //$('.structurenameslist').hide();
                 $('#progressactivitylist').hide();
                 $('#activityreport-new').show();
                 $('#activityreportfields').html(data.result);
                 $('#activityreportitems-new').html(data.resources);
                 $("#ActivityName").html(data.activityname);
                 $("#ActivityUnit").html(data.activityunit);
                 $("#EstimateQuantity").html(data.estimatequantity);
                 $("#EstimateDuration").html(data.estimateduration);
                 $('#saveactivityrpt_new').val(projectid);
                 $('#saveactivityrpt_new').attr('data-id' , activityid); 
                 $('#savedraftactivityrpt_new').val(projectid);
                 $('#savedraftactivityrpt_new').attr('data-id' , activityid);
                 if(data.disable=='True'){
                     $('#savedraftactivityrpt_new').attr("disabled", true);
                  }
                  else{
                     $('#savedraftactivityrpt_new').attr("disabled", false);
                  }
             }
            }
        });

});

$(document).on('click','.saveactivityrpt_new',function(){
    var id=$(this).attr('data-id');
    var todayquantity = $('#editquantity_new'+id).val();
    var reportdate = $('#current_date'+id).val();
    var pileno = $('#editpileno_new'+id).val();
    var zone = $('#editzone_new'+id).val();
    var error=0;
    $('.error').hide();
    
    if(todayquantity=='' || todayquantity==0){
       error=1;
       alert('Sorry quantity cannot be null!!!')
    }

    if(reportdate=='' || reportdate==0){
       error=1;
       alert("Sorry date cannot be null!!!")
    }
    
    if(pileno=='' || pileno==0){
       error=1;
       alert("Sorry pileno cannot be null!!!")
    }
    
    if(zone=='' || zone==0){
       error=1;
       alert("Sorry zone cannot be null!!!")
    }
    
    if(error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../report/ActivityReportEdit',
            beforeSend : function(){
                $('#saveactivityedit').attr("disabled", true);
            },
            dataType: "json",
            data: {activityid: id, quantity: todayquantity, reportdate: reportdate, pileno: pileno,zone: zone},
            success: function(data){
                if(data.error=='No')
                {
                    $.ajax({
                        type: 'POST',
                        url: '../report/ReportTaskEstimate',
                        dataType: "json",
                        async:false,
                        data: $('#resourceform-activitynew').serialize(),
                        success: function(data){
                          if(data.error == 'No')
                          {
                             alert('true');

                          }
                          else{

                          }      
                        }
                     });
                     $('.preloader').hide();
                     //$('.all-activities').show();
                     $('.estimatestructurelist').show();
                     //$('#progressactivitylist').show();
                     //$('#activityreport-new').hide();
                     //$('#actvtyrprt').trigger('click');
                }
            }
        });
      $('#list_ar').trigger('click');
   } 
    
  // var tdyqty = $('#resourceform-activity').serialize();

});

$(document).on('click','.savedraftactivityrpt_new',function(){
    var id=$(this).attr('data-id');
    var todayquantity = $('#editquantity_new'+id).val();
    var reportdate = $('#current_date'+id).val();
    var pileno = $('#editpileno_new'+id).val();
    var zone = $('#editzone_new'+id).val();
    var error=0;
    var mode = $(this).attr('mode');
    $('.error').hide();
    
    if(todayquantity=='' || todayquantity==0){
       error=1;
       alert('Sorry quantity cannot be null!!!')
    }

    if(reportdate==''){
       error=1;
       alert("Sorry today's date cannot be null!!!")
    }
    
    if(error==0)
    {
        $.ajax({
            type: 'POST',
            url: '../report/ActivityReportEdit',
            beforeSend : function(){
                $('#saveactivityedit').attr("disabled", true);
            },
            dataType: "json",
            data: {activityid: id, quantity: todayquantity, pileno: pileno, zone: zone, mode: mode,reportdate: reportdate},
            success: function(data){
                if(data.error=='No')
                {
                    $.ajax({
                        type: 'POST',
                        url: '../report/ReportTaskEstimateLog',
                        dataType: "json",
                        async:false,
                        data: $('#resourceform-activitynew').serialize(),
                        success: function(data){
                          if(data.error == 'No')
                          {
                             alert('true');

                          }
                          else{

                          }      
                        }
                     });
                     $('.preloader').hide();
                     //$('.all-activities').show();
                     $('.estimatestructurelist').show();
                     //$('#progressactivitylist').show();
                     //$('#activityreport-new').hide();
                     //$('#actvtyrprt').trigger('click');
                    $('#estimatestructurelist').val(0);
                    $('#estimatestructurelist').trigger('change');
                }
            }
        });
   } 
    
  // var tdyqty = $('#resourceform-activity').serialize();

});

$(document).on('click','.cancel_savenewactivityrpt',function(){
    //$('#actvtyrprt').trigger('click'); 
    $('#list_ar').trigger('click');
});

function getProjectname(id)
{
    var retval;
    $.ajax({
        type: 'POST',
        url: '../projects/Getname',
        async:false,
        data: {id:id},
        success: function(data){
            retval=data;
        }
    });
    return retval;
}

$(document).on('change','.edit_current_date_ar',function(){
    var inputdate = $(this).val();
    var activityid=$(this).attr('data-id');
    var estimateqty = $('#Estquantity'+activityid).html();
    
    var error=0;

    if(error ==0){

        $.ajax({
            type: 'POST',
            url: '../report/OldActivityReportDate',
            dataType: "json",
            data: {inputdate:inputdate,activityid:activityid,estimateqty:estimateqty},
            success: function(data){
                if(data.error=='No')
                {
                  $('#editquantity_new'+activityid).val(data.quantity);
                  $('#editpileno_new'+activityid).val(data.pileno);
                  $('#editzone_new'+activityid).val(data.zone);
                  $('#activityreportitems-new').html(data.resourcelist);
                  if(data.disable=='True'){
                     $('#savedraftactivityrpt_new').attr("disabled", true);
                  }
                  else{
                     $('#savedraftactivityrpt_new').attr("disabled", false);
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

$(document).on('click','#list_ar',function(){
    var structureid = $('#estimatestructurelist').val();
    var projectid=$('#projectitems').val();
    $('#ar_main').removeClass('btn-success');
    $('#ar_main').addClass('btn-danger');
    $('#list_ar').removeClass('btn-danger');
    $('#list_ar').addClass('btn-success');
    $('#activityreportlist').show();
    $('#estimateitemslist').hide();
    $('#estimateactivitylist').hide();
    $('#activityreportfields').hide();
    $('#activityreporttable').hide();
    $('#ar_buttons').hide();
    
    if(structureid!=0){
    
        $.ajax({
                type: 'POST',
                url: '../report/ActivityReportList/',
                dataType: "json",
                data: {structureid:structureid,projectid:projectid},
                success: function(data){
                    if(data.error=='No')
                    {
                      $("#ar_listing").html(data.list);
                    }
                }
            });

   }
   else{
     $("#ar_listing").html('');
   }
});

$(document).on('click','#ar_main',function(){
    $('#list_ar').removeClass('btn-success');
    $('#list_ar').addClass('btn-danger');
    $('#ar_main').removeClass('btn-danger');
    $('#ar_main').addClass('btn-success');
    $('#activityreportlist').hide();
    $('#estimateitemslist').show();
    $('#estimateactivitylist').show();
    $('#activityreportfields').show();
    $('#activityreporttable').show();
    $('#ar_buttons').show();
});

$(document).on('click','#actvtylistreport_ar',function(){
    
    var estimateactvtyid=$(this).val();
    var estimateitemid = $(this).attr('data-id');
    var projectid=$('#projectitems').val();
    var structureid=$('#estimatestructurelist').val();
    $('#estimateitemslist').val(estimateitemid);
    $.ajax({
        type: 'POST',
        url: '../report/NewActivityReport',
        data: {estimateitemid:estimateitemid,structureid:structureid,projectid:projectid},
        dataType: "json",
        success: function(data){
            if(data.error=='No')
            {
                $('#progressactivitylist').show();
                $('#progreportdiv').hide();
                //$('#editreportactivities').hide();
                $('#progressactivityitems').html(data.result);
                $('#estimatestructurelist').html(data.structurenames);
                $('#estimateitemslist').html(data.estimateitemsnames);
                //$('#estimateactivitylist').val(activityid);
                //$('.newactivityprogressreport').trigger('click');
                $('#estimateactivitylist').html(data.estimateactivities);
                $('#progressactivitytable').show();
                //$('#activityreportactivity').hide();
                $('#newactivityprogressreport').val(projectid);
                $('#newactivityprogressreport').attr('data-id' , estimateactvtyid);
                $('.newactivityprogressreport').trigger('click');
                $('#estimateactivitylist').val(estimateactvtyid);
            }

            $('.preloader').hide();
        }
    });

   $('#ar_main').trigger('click');
});