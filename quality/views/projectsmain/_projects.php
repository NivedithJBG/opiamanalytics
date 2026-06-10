
<?php 
use amnah\yii2\user\models\User;
use app\models\ProjuserSelection;
use app\models\Projects;
use app\models\AccountsItem;

$user_id = Yii::$app->user->id;
$user = User::find()->where(['id'=>Yii::$app->user->id])->one();

?>



<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projectsmain/projects-tab.js" type="text/javascript"></script>
 <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/masters/projectsfunctions.js" type="text/javascript"></script>


<div class="container-fluid procu-accordion" >
    <div class="row">
        <div class="col-md-12">  
            <div class="panel-group schdl acco-one-active" id="accordionprojindex">
                <div class="panel panel-default projects-tab acco-one tab tab-wrapper">
                    <!--<input type="radio" id="rd5" name="rd">-->
                    
                    <div class="panel-heading" id="selected-projctid">
                        <?php 

                            $uid = Yii::$app->user->Id; 
                            $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();

                            if($projuser){

                                $model = Projects::findOne($projuser->projectid);

                        ?>
                              <h4 class="panel-title" id="projtitle">
                                <a data-toggle="collapse" data-parent="#accordionprojindex" href="#collapseprojindex">
                                <span class="icon-note1"></span>Project - <?php echo $model->Name ?></a>
                              </h4>
                        <?php } else { ?>
                              <h4 class="panel-title" id="projtitle">
                                <a data-toggle="collapse" data-parent="#accordionprojindex" href="#collapseprojindex">
                                <span class="icon-note1"></span>Projects</a>
                              </h4>
                        <?php } ?>
                    </div>

                    <div id="collapseprojindex" class="tab-content cOrder-body panel-collapse collapse">
                        <div class="panel-body">
                            <div class="search-and-content-wrpr" >
                                <div style="border-bottom: 1px solid #dfdfdf;" class="search-and-actions-wrpr row">
                            <div class="content-search-wrpr col-md-5 col-sm-5">
                                <input type="text" placeholder="Search" id="projectsearch" class="form-control" >
                                 <span class="error" style="display: none;"></span>
                                <button id="searchproject" class="btn btn-primary" type="button"><span class="icon-search5"></span></button>
                                
                            </div>
                            <div class="col-md-7 col-sm-7 text-right">
                                <a href="#" class="btn btn-primary text-button back-butn-new closeprjct">Back</a>
                            </div>
                        </div>
                              
                            
                            <div class="content-action-wrpr col-md-12 col-sm-12" id="addprjfrm">
                                <a href="#" class="btn btn-primary listprj" id="listprj" title="Project List"><span class="icon-th-list"></span> List</a>

                                <?php if($user->account_type == 'normal'){ ?>

                                <a class="btn btn-primary addForm" title="Add New Project" data-toggle="modal" data-target="#projectFormPopup" href="#projectFormPopup"><span class="icon-add"></span> Add</a>

                                <?php } ?>

                                <a href="#" class="btn btn-primary list-fundreceipt" id="listprojects"><span class="icon-th-list"></span> List</a>
                                <a href="#" style="display: none;" class="btn btn-primary list-accountType" id="listproject"><span class="icon-th-list"></span> List</a>
                            </div>
                                
                                <div class="content-wrpr project-fav-cards-cntnr" id="projects-list-body">
                                                                
                                </div>

                                <div id="projectitems" class="project-master-cntnt-wrpr">
                            
            
                                </div>
                                <input type="hidden" id="selectedProjectId" value="">

                            </div>
                        </div> 
                    </div>
                </div>





<div class="modal fade projectFormPopup" id="projectFormPopup" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title" id="projectFormTitle" style="float: left;">Add Project</h4>
                <button type="button" class="close projectFormPopup" data-dismiss="modal" style="float:right; font-size: 30px;">×</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">

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
                                    <div class="form-group">
                                        <label>Duration</label>
                                        <input id="duration" type="number" class="form-control editenggactivityduration" placeholder="Project Duration">
                                        <span class="error" style="display: none;"></span>
                                    </div>
                                </div>

                                <div class="col-md-2" style="padding-right: 5px;">
                                    <div class="form-group">
                                        <label>Start Date</label>
                                        <input id="startdate" type="Date" class="form-control editactivitystartdate" placeholder="Start Date">
                                        <span class="error" style="display: none;"></span>
                                    </div>
                                    
                                </div>
                                <div class="col-md-2" style="padding-left: 5px;">
                                    <div class="form-group">
                                        <label>End Date</label>
                                        <input id="enddate" type="date" class="form-control editactivityenddate" placeholder="dd-mm-yyyy">
                                        <span class="error" style="display: none;"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Project Value</label>
                                        <input type="text" id="projectvalueover" class="form-control numberFormat" placeholder="Project Value">
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

                            <div class="row" >
                            <div class="col-md-8" >
                            </div>
                           
                                <div class="col-md-4" style="display:none;">
                                    <div class="form-group">
                                        <label>Cash Account</label>
                                        <select class="form-control cashaccount" id="cashaccount" name="cashaccount">
                                            <option value="0">Select Account</option>
                                            <?php $acnts = AccountsItem::find()->where(['account_type'=> 1])->andWhere(['Status'=>0])->orderBy(['name' => SORT_ASC])->all();
                                            foreach($acnts AS $accounts):
                                                echo "<option value='".$accounts->id."' >".$accounts->name."</option>";
                                            endforeach;?>
                                        </select>
                                        <span class="error" style="display: none;"></span>
                                    </div>
                                    
                                </div>
                                <div class="col-md-4" style="display:none;">
                                    <div class="form-group">
                                        <label>Bank Account</label>
                                        <select class="form-control bankaccount" id="bankaccount" name="bankaccount">
                                            <option value="0">Select Account</option>
                                            <?php $acnts = AccountsItem::find()->where(['account_type'=> 2])->andWhere(['Status'=>0])->orderBy(['name' => SORT_ASC])->all();
                                            foreach($acnts AS $accounts):
                                                echo "<option value='".$accounts->id."' >".$accounts->name."</option>";
                                            endforeach;?>
                                        </select>
                                        <span class="error" style="display: none;"></span>
                                    </div>
                                    
                                </div>
                                <div class="col-md-4">
                                    <label></label>
                                    <button type="button" class="btn btn-primary save-btn" id="saveproject"><span class="icon-check"></span> Add Project</button>
                                    <button style="border-radius: 23px;" type="button" class="btn btn-danger canceladding projectFormPopup" id="canceladding" data-dismiss="modal"><span class="icon-close"></span> Cancel</button>
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
                                    <div class="form-group" style="margin-right: 10px;">
                                        <label>Duration</label>
                                        <input id="durationn" type="number" class="form-control editenggactivityduration" >
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
                                    <button type="button" class="btn btn-primary save-btn" id="saveeditproject"><span class="icon-check"></span> Save Project</button>
                                    <button type="button" class="btn btn-danger cancel projectFormPopup data-dismiss="modal" style="border-radius: 23px;" data-dismiss="modal"><span class="icon-close"></span> Cancel</button>
                                    <input type="hidden" id="savess">
                                </div>

                            </div>
                        
                        </form> 
                        
                    </div>
                    <!-- edit form ends here -->

            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <!--<button type="button" class="btn btn-primary" ><span class="icon-check"></span> Save</button>
                <button type="button" class="btn btn-danger cancel" data-dismiss="modal" ><span class="icon-close"></span> Cancel</button>-->
        
            </div>

        </div>
    </div>
</div>

