<?php 
use amnah\yii2\user\models\User;


?>            
                <!--<div class="panel panel-default ScheduleActivity acco-seven tab tab-wrapper allocate-resource-tabs">-->
                <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projectsmain/_wbsscheduleactivity.js" type="text/javascript"></script>  
				  <!--<input type="radio" id="rd5" class="schedule_act_list" name="rd">
					
					<div class="panel-heading" >
					  <h4 class="panel-title">
						<a  href="#">
						<span class="icon-directions_run"></span>Schedule Activities</a>
					  </h4>
					</div>
					
					
					<div  class="tab-content cOrder-body panel-collapse ">
					  <div class="panel-body">
						<div class="search-and-content-wrpr" >-->
                        <div id="scheduleactpage" style="display: none;">
                            <div id="schedule-activity-header" style="display: none;">
                                <div class="search-and-actions-wrpr row" id="schedule-allocate-body-one-head">
                                    <input type="hidden" id="scidd">
                                    <div class="content-search-wrpr col-md-3 col-sm-3" >
                                        <label></label>
                                    </div>
                                    
                                    <div class="content-action-wrpr col-md-9 col-sm-9" id="actlistingshow">
                                        
                                        <a href="javascript:void(0);" class="btn btn-primary addForm" title="Add Activities"><span class="icon-add"></span> Add Activities</a>
                                        
                                        <a href="#" class="btn btn-primary list-fundreceipt" id="listscheduleact"><span class="icon-th-list"></span> List</a>

                                        <a href="#" class="btn btn-primary list-fundreceipt schedule_relation-tab reltnmain1" title="Create Relationship"><span class="icon-dns"></span>Create Relationship</a>

                                        <span id="ganttchartshow"></span>
                                        <input type="hidden" id="wbs_schedule_relation_newid" value="" >
                                        <!-- <a class="btn btn-primary list-fundreceipt" id="SAGanttChart"><span class="icon-chart6"></span> Gantt Chart</a> -->
                                    </div>
                                </div>
                            </div>
							<div class="scactheads text-center">
                                <label>Schedule Activities</label>
                            </div>
							<div class="content-wrpr">
                            
                            	<!-- add activity form -->
								<div class="add-form add-wbs-form" id="add-Schedule-Activity-form">
                                    <form id="enggactivitiesform">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label>Activity Name</label>
                                                    <input class="form-control" type="text" id="scheduleactivitiesname" name="scheduleactivitiesname" placeholder="Activity Name">
                                                    <span class="error" style="display: none;"></span>
                                                    
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Unit</label>
                                                    <input class="form-control" type="text" id="scheduleactivitiesunit" name="scheduleactivitiesunit" placeholder="Unit"><span class="error" style="display: none;"></span>
                                                    
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Quantity</label>
                                                    <input class="form-control" type="text" id="scheduleactivitiesQuantity" name="scheduleactivitiesQuantity" placeholder="Quantity"><span class="error" style="display: none;"></span>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                    
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Start Date</label>
                                                    <input class="form-control date_field editactivitystartdate" type="date" id="scheduleactivitiesStartDate" name="scheduleactivitiesStartDate" placeholder="Start Date" value="<?php echo date('Y-m-d'); ?>"><span class="error" style="display: none;"></span>
                                                    
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Duration</label>
                                                    <input class="form-control enggactivityduration" type="text" id="scheduleActivityDuration" name="scheduleActivityDuration" placeholder="Duration"><span class="error" style="display: none;"></span>
                                                    
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>End Date</label>
                                                    <input class="form-control date_field editactivityenddate" type="date" id="scheduleactivitiesEndDate" name="scheduleactivitiesEndDate" placeholder="End Date" disabled><span class="error"></span>
                                                    
                                                </div>
                                            </div>
                                            
                                        </div>
                                        <div class="col-md-12 text-center">
                                            <label>&nbsp;</label>
                                            <button type="button" class="btn btn-danger cancel" id="cancelscheduleactivity"><span class="icon-close"></span> Cancel</button>&nbsp;
                                            <button type="button" class="btn btn-primary" id="savescheduleactivities"><span class="icon-check"></span> Add</button>
                                        </div>
                                        <div class="col-md-12">
                                            <label>&nbsp;</label>
                                        </div>
                                    </form>
																		
                                </div>
                                
                                <!-- add activity form end-->
								<!-- schedule Activity list start-->
								<div class="wbs-list-wrpr data-content-list" id="schedule-allocate-body-one">
                                        <div class="row">
                                            <div id="projectnameScheduleActivity" style="display: none;">
                                                <div class="col-md-12 type project-boq">
                                                    <label ><h5 id="scheduleitemnamedisplay" style="color: black;"></h5></label>
                                                    <a href="#" class="btn btn-primary text-button back-butn-new close-scheduleactvty" title="Back">Back</a>
                                                    <input type="hidden" id="selectedScheduleItem" value="">
                                                </div>
                                            </div>
                                        </div>   
                                        <div id="ScheduleActivity-main-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="preloader" id="Promain-preloader-ScheduleActivity" style="display: none;" align="center">
                                                        <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                                                    </div>
                                                    <div id="scheduleactivityitems"><div style="text-align: center;">Select Activities from Schedule</div></div>
                                                </div>
                                                
                                            </div>
                                        </div>
                                        <!-- schedule Activity list end-->
                                        <!-- schedule Activity Relation list start  /////////-->
                                        <!-- <div id="ScheduleActivity-Relation-body" style="display: none;">
                                        <div id="ScheduleActivity-Relation-add-form123"></div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="preloader" id="Promain-preloader-ScheduleActivity-Relation1" style="display: none;" align="center">
                                                        <img src="/images/loader.gif" align="middle">
                                                    </div>
                                                    <div id="scheduleactivityitems-Relation1"></div>
                                                </div>

                                            </div>
                                        </div> -->
                                         <!-- schedule Activity Relation list end-->
                                </div>

                                <!-- Estimate-allocate body start here -->
                                <div class="schedule-allocate" id="schedule-allocate-body-two" style="display: none;">
                                    <div class="allocated-list-cntnt-wrpr added-alloc-items-wrpr data-content-list">
								        <div class="row added-alloc-items-heading scdmaihd">
								            <div class="col-md-12 ">
									           <div class="row  alloc-activity-title">
								                    <div class="col-md-8 type" id="headone">
								                    </div>
                                                    <div class="col-md-4 text-right icon-groups">
                                                	   <!--<a href="#" class="btn btn-primary resource-list-btn2" id="resource-list-btn2">Add Allocation</a>
                                                        <a href="#" class="btn btn-primary close-resource-list-btn2">Close Allocation</a>-->
                                                        <input type="hidden" id="assignresourceload">
                                                        <a href="javascript:void(0);" class="btn btn-primary text-button resource-back-button"> Back</a>
                                                    </div>
								                </div>
								            </div>
								        </div>
								        <div class="allocation-list-items-cntnr">
                                            <div class="added-activity-list-wrpr collapse in" id="scheduleassignlist-schedule"></div><br>
                                            <div class="col-md-12 type project-boq" >
                                                <a href="javascript:void(0);"  class="btn btn-primary add-project-activities-btnnew resource-list-btn2" id="resource-list-btn2"><span class="icon-add"></span>Add Resource</a>
                                                <a href="#" class="btn btn-primary close-resource-list-btn2">Close Allocation</a>&nbsp;&nbsp;&nbsp;
                                                <a href="javascript:void(0);" class="expand-collapse-palist icon-arrow-up1" data-toggle="collapse" data-target=".added-activity-list-wrpr" style="display: none;"></a>
                                            </div>
                                        </div>
                                    </div>
                            
                                    <div class="allocation-cntnt-wrpr resource-not-allocated">
					
        								<div class="allocation-left-bar items-select-bar" id="SAbuttonlist">
        									<a class="btn btn-primary rounded-coner-btn" href="#">Investments</a>
        									<a class="btn btn-primary rounded-coner-btn" href="#">Project Setup</a>
        									<a class="btn btn-primary rounded-coner-btn active" href="#">Materials</a>
        									<a class="btn btn-primary rounded-coner-btn" href="#">Sub. Contractors</a>
        								</div>

        								<div class="resource-not-allocated-info">
        									<div class="icon-info3"></div>
        									<div class="info-text">
        										<p>Resources not allocated to the activity - Investments in Floating Crafts</p>
        										<span>To allocate resources, click on the left tab</span>
        									</div>
        								</div>

        								<div class="resources-from-selected-item-wrpr ">
        									<div class="row">
        										<div class="col-md-12 ">
        											<div class="search-and-allocate-content-wrpr">
            											<div class="search-and-actions-wrpr row">
            												<div class="content-search-wrpr col-md-12 col-sm-12">
            													<!--<div class="col-md-3">
            														<select id="resource_groupselection" class="form-control">

            														</select>
            														
            													</div>-->
            													<div class="col-md-9">
            														<input class="form-control" id="resource_name" type="text" placeholder="Search">
                                                                    <input type="hidden" id="selectresource_id">
            													</div>
            													<div class="col-md-3">
            														<button type="button" class="btn btn-primary resourcesearch123" id="resource_search123">
            															<span class="icon-search5"></span>
            														</button>
            													</div>
            												</div>
            											</div>
        											
                                                        <div class="panel-group allocation-items" id="accordion">
        												    <div class="panel panel-default active">
        												        <div class="preloader" style="display: none;" align="center">
        													       <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
        												        </div>
        												        <div id="projects-Activity-Search-Lists2">
        												
        												        </div>
        												    </div>
        											    </div>
        											
        										    </div>	
        										
        								        </div>
        									
        								    </div>
        					
        				                </div>
        												
        							<!-- list end here -->
						
							
				                    </div>
                                </div>
							</div>
                        </div>
						<!--</div>
					  
					  
				    </div>
				</div>
            </div>-->
 <!-- <script type="text/javascript">
    $('#scheduleactivitiesEndDate').change(function(){
        var startDate = $('#scheduleactivitiesStartDate').val();
        var endDate = $('#scheduleactivitiesEndDate').val();
        // console.log(endDate);
        if(endDate != '' && startDate != '')
        {
            var startDate1 = Date.parse(startDate);
            var endDate1 = Date.parse(endDate);
            var timeDiff = endDate1 - startDate1;
            daysDiff = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
            console.log(daysDiff);
            $('#scheduleActivityDuration').val(daysDiff+1);
        }
    });
    
    $('#scheduleActivityDuration').change(function(){
        var today = new Date();
        var tempstart = (today.getDate());
        if (tempstart < 10) tempstart = '0' + tempstart;
        var tempMonth = (today.getMonth() + 1);
        if (tempMonth < 10) tempMonth = '0' + tempMonth;
        var startDate = today.getFullYear() + '-' + tempMonth + '-' + tempstart;
        var duration = $('#scheduleActivityDuration').val();
        //var endDate = $('#scheduleactivitiesEndDate').val();
        // console.log(endDate);
        if(duration != '' && startDate != '')
        {
            var newdate = new Date(today).setDate(today.getDate() + (+duration) - 1);
            var endDate1 = new Date(newdate);
            var tempoMonth = (endDate1.getMonth() + 1);
            if (tempoMonth < 10) tempoMonth = '0' + tempoMonth;
            var tempoDate = (endDate1.getDate());
            if (tempoDate < 10) tempoDate = '0' + tempoDate;
            var endDate = endDate1.getFullYear() + '-' + tempoMonth + '-' + tempoDate;
            
            /*$('#scheduleactivitiesStartDate').val(startDate);*/
            $('#scheduleactivitiesEndDate').val(endDate);
        }
    });
    
    $('#scheduleactivitiesStartDate').change(function(){
       var startDate = new Date($('#scheduleactivitiesStartDate').val());
       var duration = $('#scheduleActivityDuration').val();
       if(duration != '' && startDate != '')
        {
            var newdate = new Date(startDate).setDate(startDate.getDate() + (+duration) - 1);
            var endDate1 = new Date(newdate);
            var tempoMonth = (endDate1.getMonth() + 1);
            if (tempoMonth < 10) tempoMonth = '0' + tempoMonth;
            var tempoDate = (endDate1.getDate());
            if (tempoDate < 10) tempoDate = '0' + tempoDate;
            var endDate = endDate1.getFullYear() + '-' + tempoMonth + '-' + tempoDate;
            
            $('#scheduleactivitiesEndDate').val(endDate);
        }
    });
</script>
 -->

<script type="text/javascript">
    
    $(document).on('change','.editactivityenddate',function(){
    
    var startDate = $('#scheduleactivitiesStartDate').val();
    var endDate = $('#scheduleactivitiesEndDate').val();
    //alert(endDate)
    if(endDate != '' && startDate != '')
    {
        var startDate1 = Date.parse(startDate);
        var endDate1 = Date.parse(endDate);
        var timeDiff = endDate1 - startDate1;
        daysDiff = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
        console.log(daysDiff);
        $('#scheduleActivityDuration').val(daysDiff+1);
    }
    });

$(document).on('change','.editactivitystartdate',function(){
       var startDate = new Date($('#scheduleactivitiesStartDate').val());
       var duration = $('#scheduleActivityDuration').val();
       if(duration != '' && startDate != '')
        {
            var newdate = new Date(startDate).setDate(startDate.getDate() + (+duration) - 1);
            var endDate1 = new Date(newdate);
            var tempoMonth = (endDate1.getMonth() + 1);
            if (tempoMonth < 10) tempoMonth = '0' + tempoMonth;
            var tempoDate = (endDate1.getDate());
            if (tempoDate < 10) tempoDate = '0' + tempoDate;
            var endDate = endDate1.getFullYear() + '-' + tempoMonth + '-' + tempoDate;
            
            $('#scheduleactivitiesEndDate').val(endDate);
        }
    });
$(document).on('change','.enggactivityduration',function(){
       var startDate = new Date($('#scheduleactivitiesStartDate').val());
       var duration = $('#scheduleActivityDuration').val();
       if(duration != '' && startDate != '')
        {
            var newdate = new Date(startDate).setDate(startDate.getDate() + (+duration) - 1);
            var endDate1 = new Date(newdate);
            var tempoMonth = (endDate1.getMonth() + 1);
            if (tempoMonth < 10) tempoMonth = '0' + tempoMonth;
            var tempoDate = (endDate1.getDate());
            if (tempoDate < 10) tempoDate = '0' + tempoDate;
            var endDate = endDate1.getFullYear() + '-' + tempoMonth + '-' + tempoDate;
            
            $('#scheduleactivitiesEndDate').val(endDate);
        }
    });
</script>