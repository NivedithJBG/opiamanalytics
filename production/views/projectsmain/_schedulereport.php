<div class="panel panel-default activity-report-tab tab tab-wrapper acco-six" style="margin-top:12px;">
	<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/operations/progressreport.js" type="text/javascript"></script>

  <!-- <input type="radio" class="progress_act_reprt" id="rd5" name="rd"> -->
		<div class="panel-heading" >
		  <h4 class="panel-title" id="progress_act_reprt">
			<a data-toggle="collapse" data-parent="#accordionprojindex" href="#collapseschedulerep">
			<span class="icon-files1"></span>Reporting</a>
		  </h4>
		</div>

				<div id="collapseschedulerep" class="tab-content cOrder-body panel-collapse collapse">
				  <div class="panel-body ">

				    <!-- progress report start -->
				    <div id="homepr" class="tab-pane fade in active">

						<div class="search-and-content-wrpr" id="pgrsrpt">
							<form id="schedule-task">
								<div class="search-and-actions-wrpr row">
									<div class="content-search-wrpr col-md-6 col-sm-6 text-left ">
										<input type="hidden" name="select_report_date" id="select_report_date" value="<?php echo date("d-m-Y");?>" />
									</div>
									<div class="col-md-2 col-sm-2"></div>
									<div class="content-action-wrpr col-md-4 col-sm-4">
										<button type="button" class="btn btn-primary activity_history_btn" id="activity_history_btn">History</button>
										<button type="button" class="btn btn-primary activity_back_btn" id="activity_back_btn">Back</button>

										<input type="hidden" id="activity_pr_main"/>
									</div>
								</div>
								<div class="content-wrpr">
									<div class="asset-register-list-wrpr data-content-list">
			                            <div class="preloader" style="display: none;" align="center">
			                                <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
			                            </div>
			                            <div id="schedule_report_activityitems"></div>
			                            <div id="activitycompletehist"></div>
			                            <div id="success-messages"></div>
			                        </div>
								</div>
							</form>
						</div>
					</div>
				    <!-- progress report end -->

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
                                    <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                                </div>

                                <div class="row">
                                    <form id="estworktypeform">
                                        <div class="col-md-3"></div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Cause of Delay</label>
                                                <input type="text" class="form-control" id="causeofdelay" placeholder="Cause of Delay">
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

	    setTimeout(function(){ $('#listwbs_schedule').trigger('click'); }, 1000);

	    $('#collapseschedule').addClass('in');

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
