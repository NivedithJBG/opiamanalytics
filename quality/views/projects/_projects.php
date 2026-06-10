
<?php
use app\models\AccountsItem;
?>

<div class="panel panel-default acco-one projects-masters-tab tab-wrapper tab">
    <style type="text/css">
         .error{
            font-size: 11px;
            color: red;
            font-weight: normal;
         }
     </style>
    <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/masters/projectsfunctions.js" type="text/javascript"></script>
    <!--<input type="radio" id="rd3" class="prjct-tab" name="rd">-->
    <div class="panel-heading"  >
        <h4 class="panel-title acc_trigger prjct-tab" id="project" >
        <a data-toggle="collapse" data-parent="#accordionpromasterind" href="#collapsepromaster">
        <span class="icon-note1 acc_trigger"></span>Projects</a>
        </h4>
    </div>
    <div id="collapsepromaster" class="tab-content cOrder-body panel-collapse collapse">
                  <div class="panel-body">
                  
                    <div class="search-and-content-wrpr">
                        <div style="border-bottom: 1px solid #dfdfdf;" class="search-and-actions-wrpr row">
                            <div class="content-search-wrpr col-md-5 col-sm-5">
                                <input type="text" placeholder="Search" id="projectsearch" class="form-control" >
                                 <span class="error" style="display: none;"></span>
                                <button id="searchproject" class="btn btn-primary" type="button"><span class="icon-search5"></span></button>
                            </div>
                            <div class="col-md-5 col-sm-5"></div>
                            <div class="content-action-wrpr col-md-2 col-sm-2" style="display: none;">
                                <a href="#" class="btn btn-primary addForm"><span class="icon-add"></span> Add</a>
                                <a href="#" class="btn btn-primary list-accountType" id="listproject"><span class="icon-th-list"></span> List</a>
                            </div>
                        </div>
                        
                            <!-- form starts here -->
                            <div style="display: none;" id="addwindow" class="add-project-master-form row">
                                <form id="addprojectform">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Project Name</label>
                                                <input id="projectnameover" name="projectname" type="text" class="form-control" placeholder="Project Name">
                                                <span class="error" style="display: none;"></span>
                                            </div>

                                        </div>
                                        <div class="col-md-4">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Start Date</label>
                                                        <input id="startdate" type="Date" class="form-control editactivitystartdate" placeholder="Start Date">
                                                        <span class="error" style="display: none;"></span>
                                                    </div>
                                                    
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>End Date</label>
                                                        <input id="enddate" type="date" class="form-control editactivityenddate" placeholder="dd-mm-yyyy">
                                                        <span class="error" style="display: none;"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Duration</label>
                                                <input id="duration" type="text" class="form-control editenggactivityduration" placeholder="Project Duration">
                                                <span class="error" style="display: none;"></span>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row">
                                                                
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Project Value</label>
                                                <input type="text" id="projectvalueover" class="form-control" placeholder="Project Value">
                                                <span class="error" style="display: none;"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Client Name</label>
                                                <input id="clientname" type="text" class="form-control" placeholder="Client Name">
                                                <span class="error" style="display: none;"></span>
                                            </div>
                                            
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Work Hours</label>
                                                <input id="wrkhrs" type="text" class="form-control" placeholder="Work Hours">
                                                <span class="error" style="display: none;"></span>
                                            </div>
                                            
                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Cash Account</label>
                                                <select class="form-control cashaccount" id="cashaccount" name="cashaccount">
                                                    <option value="0">Select Account</option>
                                                    <?php $acnts = AccountsItem::find()->where(['account_type'=> 1])->orderBy(['name' => SORT_ASC])->all();
                                                    foreach($acnts AS $accounts):
                                                        echo "<option value='".$accounts->id."' >".$accounts->name."</option>";
                                                    endforeach;?>
                                                </select>
                                                <span class="error" style="display: none;"></span>
                                            </div>
                                            
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Bank Account</label>
                                                <select class="form-control bankaccount" id="bankaccount" name="bankaccount">
                                                    <option value="0">Select Account</option>
                                                    <?php $acnts = AccountsItem::find()->where(['account_type'=> 2])->orderBy(['name' => SORT_ASC])->all();
                                                    foreach($acnts AS $accounts):
                                                        echo "<option value='".$accounts->id."' >".$accounts->name."</option>";
                                                    endforeach;?>
                                                </select>
                                                <span class="error" style="display: none;"></span>
                                            </div>
                                            
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <label></label>
                                            <button type="button" class="btn btn-danger cancel" id=""><span class="icon-close"></span> Cancel</button>
                                            <button type="button" class="btn btn-primary save-btn" id="saveproject"><span class="icon-check"></span> Add Project</button>
                                        </div>

                                    </div>
                                
                                </form>  
                            </div>
                            <!-- form ends here -->
                            
                            
                            
                            <!-- edit form starts here -->
                            <div style="display: none;" id="editwindow" class="edit-project-master-form row">
                                <form id="editprojectform">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group" style="margin-left: 10px;">
                                                <label>Project Name</label>
                                                <input id="projectnames" name="projectname" type="text" class="form-control" >
                                                <span class="error" style="display: none;"></span>
                                            </div>

                                        </div>
                                        <div class="col-md-4">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Start Date</label>
                                                        <input id="startdatee" type="Date" class="form-control editactivitystartdate" >
                                                        <span class="error" style="display: none;"></span>
                                                    </div>
                                                    
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>End Date</label>
                                                        <input id="enddatee" type="date" class="form-control editactivityenddate" >
                                                        <span class="error" style="display: none;"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group" style="margin-right: 10px;">
                                                <label>Duration</label>
                                                <input id="durationn" type="text" class="form-control editenggactivityduration" >
                                                <span class="error" style="display: none;"></span>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row">
                                    
                                        <div class="col-md-4">
                                            <div class="form-group" style="margin-left: 10px;">
                                                <label>Project Value</label>
                                                <input type="text" id="projectvalues" class="form-control" >
                                                <span class="error" style="display: none;"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Client Name</label>
                                                <input id="clientnames" type="text" class="form-control" >
                                                <span class="error" style="display: none;"></span>
                                            </div>
                                            
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group" style="margin-right: 10px;">
                                                <label>Work Hours</label>
                                                <input id="wrkhrss" type="text" class="form-control" >
                                                <span class="error" style="display: none;"></span>
                                            </div>
                                            
                                        </div>

                                    </div>

                                    <div class="row">

                                        <div class="col-md-4">
                                            <div class="form-group" style="margin-left: 10px;">
                                                <label>Cash Account</label>
                                                <select class="form-control editcashaccount" id="editcashaccount" name="editcashaccount">
                                                </select>
                                                <span class="error" style="display: none;"></span>
                                            </div>
                                            
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Bank Account</label>
                                                <select class="form-control editbankaccount" id="editbankaccount" name="editbankaccount">
                                                </select>
                                                <span class="error" style="display: none;"></span>
                                            </div>
                                            
                                        </div>
                                        <div class="col-md-4 text-right" style="margin-left: 299px;">
                                            <label></label>
                                            <button type="button" class="btn btn-danger cancel" id=""><span class="icon-close"></span> Cancel</button>
                                            <button type="button" class="btn btn-primary save-btn" id="saveeditproject"><span class="icon-check"></span> Save Project</button>
                                            <input type="hidden" id="savess">
                                        </div>

                                    </div>
                                
                                </form> 
                                
                            </div>
                            <!-- edit form ends here -->
                            
                            
                            <!-- list start here -->
                            <div class="preloader" style="display: none;"><center>
                                <img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"></center>
                            </div>
                            <div id="projectitems" class="project-master-cntnt-wrpr">
                            
            
                            </div>
    
                            <!-- list end here -->
                        
                            
                        
                    </div>
                  
                  </div>
                </div>



</div>