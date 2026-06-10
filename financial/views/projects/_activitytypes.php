
<div class="panel panel-default activitytype-masters-tab tab-wrapper tab acco-two">
    <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/masters/activitytype.js" type="text/javascript"></script>
    <!--<input type="radio" id="rd5" class="prjct-acttype" name="rd">-->
    <div class="panel-heading" >
      <h4 class="panel-title prjct-acttype">
        <a data-toggle="collapse" data-parent="#accordionpromasterind" href="#collapseacttype">
        <span class="icon-beenhere"></span>Activity Type</a>
      </h4>
    </div>

    <div id="collapseacttype" class="tab-content cOrder-body panel-collapse collapse">
        <div class="panel-body">
            <div class="search-and-content-wrpr">
                        <div class="search-and-actions-wrpr row">
                            <div class="content-search-wrpr col-md-5 col-sm-5">
                                <input type="text" placeholder="Search" id="searchactivitytypename" class="form-control">
                                <button id="activitytypesearch" class="btn btn-primary" type="button"><span class="icon-search5"></span></button>
                            </div>
                            <div class="col-md-5 col-sm-5"></div>
                            <div class="content-action-wrpr col-md-2 col-sm-2">
                                <a href="#" class="btn btn-primary addForm" id="addactivitytype" title="Add Activity Type"><span class="icon-add"></span> Add</a>
                                <a href="#" class="btn btn-primary list-accountType" id="listactivitytype"><span class="icon-th-list"></span> List</a>
                            </div>
                        </div>
                        <div class="content-wrpr">
                            <!-- form starts here -->
                            <div class="add-form add-activity-type-form">
                                <div class="row">
                                    <form id="activitytypeform">
                                    <div class="col-md-1">
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Activity Type</label>
                                            <input id="activitytypename" type="text" class="form-control" placeholder="Activity Type">
                                            <span class="error" style="display: none;"></span>
                                        </div>  
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <label>Schedule</label>
                                            <input id="scheduletype" name="schedule_type" type="checkbox" placeholder="Schedule" value="1">
                                            <span class="error" style="display: none;"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-left">
                                        <label></label>
                                        <button type="button" class="btn btn-danger cancel" id="cancelactivitytype"><span class="icon-close"></span> Cancel</button>
                                        <button type="button" class="btn btn-primary save-btn" id="saveactivitytype"><span class="icon-check"></span> Add Activity Type</button>
                                    </div>
                                    
                                </form>
                                </div>
                            </div>
                            <!-- form ends here -->
                            
                            
                            
                            <!-- edit form starts here -->
                            <div class="edit-form edit-activity-type-form">
                                <div class="row">
                                    <div class="col-md-1"></div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Activity Type</label>
                                            <input type="text" class="form-control" id="editactivitytype" placeholder="Activity Type">
                                            <span style="color: red"></span>
                                        </div>  
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <label>Schedule</label>
                                            <input id="editscheduletype" name="editschedule_type" type="checkbox" placeholder="Schedule" value="1">
                                            <span class="error" style="display: none;"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-left">
                                        <label></label>
                                        <button type="button" class="btn btn-danger cancel" id="canceladdtype"><span class="icon-close"></span> Cancel</button>
                                        <button type="button" class="btn btn-primary saveactivitytypebutton" id=""><span class="icon-check"></span> Edit Activity Type</button>
                                    </div>
                                    
                                </div>
                            </div>  
                            
                            <!-- edit form ends here -->
                            
                            
                            
                            
                            
                            <!-- list start here -->
                            <div class="preloader" style="display: none;"><center>
                                <img src="/opiamnew/web/images/loader.gif" align="middle"></center>
                            </div>
                        <div class="activity-type-cntnt-wrpr data-content-list" id="activitytypeitems">
                            
                                
                            </div>
                            
                    
                            
                            <!-- list end here -->
                        
                            
                        </div>
                    </div>


        </div>
    </div>
</div>

