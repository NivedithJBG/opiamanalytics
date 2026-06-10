<div class="panel panel-default activity-report-tab tab tab-wrapper acco-six">
	<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/operations/progressreport.js" type="text/javascript"></script>
  	<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/operations/clientbill.js" type="text/javascript"></script>
			  
	  <!-- <input type="radio" class="progress_act_reprt" id="rd5" name="rd"> -->
		<div class="panel-heading" >
		  <h4 class="panel-title" id="progress_act_reprt">
			<a data-toggle="collapse" data-parent="#accordionoper" href="#collapseschedulerep">
			<span class="icon-files1"></span>Reporting</a>
		  </h4>
		</div>

				<div id="collapseschedulerep" class="tab-content cOrder-body panel-collapse collapse">
				  <div class="panel-body ">


					<!-- 	<ul class="nav nav-tabs">
	                <li class="active"><a data-toggle="tab" href="#rpt"><span class="icon-document-text"></span> Progress Report</a></li>
	                
	                </ul> -->


	                <!-- <div class="container">
		                <ul class="nav nav-tabs nav-justified mb-3" id="headersz">
		                <li class="active"><a data-toggle="tab" href="#homepr" style="background: #F5F5F5;"><span class="icon-document-text"></span> Progress Report</a></li>
		                <li><a data-toggle="tab" href="#menucb" id="clientbill" style="background: #F5F5F5;margin: inherit;"><span class="icon-document-text"></span>Client Bill</a></li>
		                
		                </ul>
	                </div> -->



	                   <!-- progress report start -->
	                    <div id="homepr" class="tab-pane fade in active">

						<div class="search-and-content-wrpr" id="pgrsrpt">
							<!-- <div class="col-md-12 text-center" style="padding-top: 35px;">
								<div class="row prgheads">
	                            
	                                <label style="font-size: 15px;">Progress Report</label>
	                            </div>
	                           
	                        </div> -->
	                        <form id="schedule-task">
								<div class="search-and-actions-wrpr row">
									<div class="content-search-wrpr col-md-6 col-sm-6 text-left ">
										<!-- <span style="font-weight: bold;padding-right: 10px;">Report Date</span> -->
										<!-- <input type="hidden" class="form-control datepicker select_report_date" id="select_report_date" name="select_report_date" value="<?php //echo date("d-m-Y");?>" data-id="1" /> -->
										<input type="hidden" name="select_report_date" id="select_report_date" value="<?php echo date("d-m-Y");?>" />
									</div>
									<div class="col-md-2 col-sm-2"></div>
									<div class="content-action-wrpr col-md-4 col-sm-4">
										<!--<button type="button" class="btn btn-danger taskreport" id="taskreport">Tasks</button>-->

										<button type="button" class="btn btn-primary cause_of_delay_btn" id="cause_of_delay_btn" data-toggle="modal" data-target="#causeOfDelayPopup" href="#causeOfDelayPopup">Cause of Delay</button>
										<button type="button" class="btn btn-primary activity_history_btn" id="activity_history_btn">History</button>
										<button type="button" class="btn btn-primary activity_back_btn" id="activity_back_btn">Back</button>

										<input type="hidden" id="activity_pr_main"/>
									</div>
								</div>
								<div class="content-wrpr">
									<!-- form starts here -->
										
									<!-- form ends here -->
									
									<!-- edit form starts here -->
									
									
									<!-- edit form ends here -->
									
									
									<!-- list start here -->
									<div class="asset-register-list-wrpr data-content-list">
		                                <div class="preloader"style="display: none;" align="center">
		                                    <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
		                                </div>
		                                <div id="schedule_report_activityitems"></div>
		                                <div id="activitycompletehist"></div>
		                                <div id="success-messages"></div>	
		                            </div>
		                            <!-- list end here -->
								</div>
							</form> 

							<!--<form id="schedule-task-reporting" style="display:none">
								<div class="search-and-actions-wrpr row">
									<div class="content-search-wrpr col-md-6 col-sm-6 text-left ">
										<input type="text" class="form-control datepicker select_report_date_task" id="select_report_date_task" name="select_report_date_task" value="<php echo date("d-m-Y");?>" data-id="1" />
									</div>
									<div class="col-md-4 col-sm-4"></div>
									<div class="content-action-wrpr col-md-2 col-sm-2">
										<button type="button" class="btn btn-danger backprgrsreprt" id="backprgrsreprt">Back</button>
									</div>
								</div>
								<div class="content-wrpr">
																	
									<div class="asset-register-list-wrpr data-content-list">
		                                <div id="scheduleactivityitemstask"></div>
		                                <div id="success-messages-task"></div>	
		                            </div>
								</div>
							</form>-->

						</div>
					</div>

                <!-- progress report end -->


                  




				  </div>
				</div>
</div>


</div>



<!---  CAUSE OF DELAY POPUP ---->
<div class="modal fade causeOfDelayPopup" id="causeOfDelayPopup" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title"  style="float: left;">Manage Cause Of Delays</h4>
                <button type="button" class="close causeOfDelayPopup" data-dismiss="modal" style="float:right; font-size: 30px;">×</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">


                    <div class="row">
                            
                            <div class="col-md-12">
                                <div class="preloader" id="Promain-preloader-Listwbs" style="display: none;" align="center">
                                    <img src="/sreejith/opiam_analytics/web/images/loader.gif" align="middle">
                                </div>


                                <div class="row">
                                    <form id="estworktypeform">
                                        <div class="col-md-3"></div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Casuse of Delay</label>
                                                <input type="text" class="form-control" id="causeofdelay" placeholder="Casuse of Delay">
                                                <span class="error" style="display: none;"></span>
                                            </div>  
                                        </div>
                                        <div class="col-md-4 text-left" style="padding-top: 5px;">
                                            <label style="width: 100%;"></label>
                                            
                                            <button type="button" class="btn btn-primary save-btn" id="saveCauseofDelay"><span class="icon-check"></span> Add</button>
                                        </div>
                                    </form>
                                </div>

                                <hr>

                                <div id="causeOfDelayListContainer" class="row ">
                                    
                                </div>


                            </div>
                            
                    </div>

            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <!--<button type="button" class="btn btn-primary" ><span class="icon-check"></span> Save</button>
                <button type="button" class="btn btn-danger cancel" data-dismiss="modal" ><span class="icon-close"></span> Cancel</button>-->
        
            </div>

        </div>
    </div>
</div>
<!-------------->


<style type="text/css">
	.form-control{
		padding: 6px 8px !important;
	}
</style>

<script>

$(document).ready(function(){
   
   $(document).on('focus','.datepicker',function(){
        $(this).datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true,
            maxDate: new Date() 
        });
    });
	
    var id=location.hash;
    
    if(id=='#back'){
    	$('.view_wbsSchedule').trigger('click');
	    $('.schdl').addClass('acco-five-active');
	    $('.schdl').removeClass('acco-one-active');
	    $('.schdl').removeClass('acco-two-active');
	    $('.schdl').removeClass('acco-three-active');
	    $('.schdl').removeClass('acco-four-active');
	    $('.schdl').removeClass('acco-six-active');
	    $('.Schedule-tab').addClass('active prev-tab');
	    
	     //$('#listwbs_schedule').trigger('click');
	    setTimeout(function(){ $('#listwbs_schedule').trigger('click'); }, 1000);

	    $('#collapseschedule').addClass('in');

	    //window.location.hash = '';

	   history.replaceState(null, null, ' ');
	    	    
	}else if(id=='#backact'){
        $('.listscheduleactivity').trigger('click');
        $('.schdl').addClass('acco-five-active');
        $('.schdl').removeClass('acco-one-active');
        $('.schdl').removeClass('acco-two-active');
        $('.schdl').removeClass('acco-three-active');
        $('.schdl').removeClass('acco-four-active');
	    $('.schdl').removeClass('acco-six-active');
        $('.Schedule-tab').addClass('active prev-tab');

        //setTimeout(function(){ $('#listscheduleact').trigger('click'); }, 1000);
         
        
        $('#collapseschedule').addClass('in');

        var ids= sessionStorage.getItem("schdid");
        $('#wbs_schedule_block').hide();
        $('#ganttchartshow').show();
        $('#scheduleactpage').show();
        $('#selectedScheduleItem').val(ids);
        $('#wbs_schedule_relation_newid').val(ids);
        $('#scheduleitemnamedisplay').html(getItemname(ids));
        setTimeout(function(){ $('#listscheduleact').trigger('click'); }, 1000);
        $('#projectnameScheduleActivity').show();

        history.replaceState(null, null, ' ');
        
    }
   
});
</script>