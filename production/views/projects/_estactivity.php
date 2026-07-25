<?php

use app\models\EstimateWorkType;
use app\models\EstimateActivityType;
use app\models\Resources;
?>

<div class="activities-masters-tab allocate-resource-tabss" id="estactivity">
    <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/masters/estactivity.js?v=<?php echo time(); ?>" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            var max_fields      = 11; //maximum input boxes allowed
            var wrapper         = $(".add-activity-form"); //Fields wrapper
            var add_button      = $(".add_estactivity_button"); //Add button ID
            var x = 2; //initlal text box count
            $(add_button).click(function(e){
                e.preventDefault();
                if(x < max_fields){
                    $('#estactivityaddrow').append('<div id="divrow'+x+'" class="row"><div class="col-md-1"></div><div class="col-md-5" style="padding-left:27px;"><div class="form-group"><label>Activity Name</label><input class="form-control estactivityname" type="text" id="estactivityname'+x+'" data-id="'+x+'" name="estactivityname[]" placeholder="Activity Name"><span style="color:red;"></span></div>  </div><div class="col-md-3 "><div class="form-group"><label>Activity Unit</label><input class="form-control estactivityunit" type="text" id="estactivityunit'+x+'" data-id="'+x+'" name="estactivityunit[]" placeholder="Activity Unit"></div></div><div class="col-md-1 addnext-icon-wrpr" style="margin-left:-7px;"><div class="icon-groups"><a data-id="'+x+'" class="btn btn-primary icon-remove remove_field" href="javascript:void(0)"></a></div><div class="col-md-2"></div></div><div class="col-md-1"></div></div>');
                    x++;
                }
            });
            $(wrapper).on("click",".remove_field", function(e){
                var idval = $(this).data("id");
                e.preventDefault();
                //$(this).parent('div').parent('.row').remove();
                $('#divrow'+idval).remove();
                x--;
            })

        });

      $(document).on('click', '#addmoreactivitys', function(){ 

        var max_fields = 11;
         var x = 0; 

         if(x < max_fields){ 

          

           $('#estactivityaddrow').append('<div id="divrow'+x+'" class="row"><div class="col-md-1"></div><div class="col-md-5" style="padding-left:27px;"><div class="form-group"><label>Activity Name</label><input class="form-control estactivityname" type="text" id="estactivityname'+x+'" data-id="'+x+'" name="estactivityname[]" placeholder="Activity Name"><span style="color:red;"></span></div>  </div><div class="col-md-3 "><div class="form-group"><label>Activity Unit</label><input class="form-control estactivityunit" type="text" id="estactivityunit'+x+'" data-id="'+x+'" name="estactivityunit[]" placeholder="Activity Unit"></div></div><div class="col-md-1 addnext-icon-wrpr" style="margin-left:-7px;"><div class="icon-groups"><a data-id="'+x+'" class="btn btn-primary icon-remove remove_field" href="javascript:void(0)"></a></div><div class="col-md-2"></div></div><div class="col-md-1"></div></div>');
              x++;

//   var html = '';
 // // html += '<tr>';
 // // html += '<td><input type="hidden" name="hidden_id[]"  class="hidden_id" value="'+hidden_id+'" /></td>';
 //  html += '<input class="form-control estactivityname" type="text" id="estactivityname'+x+'" data-id="'+x+'" name="estactivityname[]" placeholder="Activity Name">';
 //  html += '<input class="form-control estactivityunit" type="text" id="estactivityunit'+x+'" data-id="'+x+'" name="estactivityunit[]" placeholder="Activity Unit">';


   }

  // $('#estactivityaddrow').append(html);
       });


       
    </script>
    <div id="collapsemasteract" style="display:block;">
        <div class="panel-body">

                    <div class="search-and-content-wrpr">
                        <div class="search-and-actions-wrpr" id="AR-allocate-body-one-head" style="display:flex;align-items:center;gap:3px;padding:4px 0;flex-wrap:nowrap;justify-content:flex-start;">
                            <a href="#alProjTypePopup" class="btn btn-default" id="alOpenProjTypePopup" data-toggle="modal" data-target="#alProjTypePopup" style="background:#6b7a93;color:#fff;border-color:#56657a;border-radius:20px;">+ Project Type</a>
                            <a href="#alIowGroupPopup" class="btn btn-default" id="alOpenIowGroupPopup" data-toggle="modal" data-target="#alIowGroupPopup" style="background:#6b7a93;color:#fff;border-color:#56657a;border-radius:20px;">+ IOW Group</a>
                            <a href="#alActTypePopup" class="btn btn-default" id="alOpenActTypePopup" data-toggle="modal" data-target="#alActTypePopup" style="background:#6b7a93;color:#fff;border-color:#56657a;border-radius:20px;">+ Activity Type</a>
                            <a href="#alAddActivityPopup" class="btn" data-toggle="modal" data-target="#alAddActivityPopup" id="addestactivity" title="Add Activities" style="border-radius:20px;background:#4a5568;color:#fff;border-color:#2d3748;"><span class="icon-add"></span> Activity</a>
                            <a href="#" class="btn list-accountType" id="listestactivity" style="border-radius:20px;background:#4a5568;color:#fff;border-color:#2d3748;"><span class="icon-th-list"></span> List</a>
                            <select id="searchestworktypelist" class="form-control" style="margin-left:10px;width:160px;border-radius:20px;height:34px;display:inline-block;">
                                <option value="0">All Project Types</option>
                                <?php
                                $typelist = EstimateWorkType::find()->where(['estworktype_status' => 0])->orderBy(['estworktype_name' => SORT_ASC])->all();
                                foreach ($typelist as $list):
                                    echo "<option value='".$list->estworktype_id."'>".$list->estworktype_name."</option>";
                                endforeach;
                                ?>
                            </select>
                            <input type="text" id="searchestactivityname" placeholder="Search activity..." class="form-control" style="margin-left:6px;width:160px;border-radius:20px;height:34px;display:inline-block;">
                            <button id="estactivitysearch" class="btn btn-primary" type="button" style="margin-left:3px;border-radius:20px;"><span class="icon-search5"></span></button>
                            <input type="hidden" name="estworktypedisplay" id="estworktypedisplay">
                        </div>
                        <div class="content-wrpr">

                            <!-- add-activity-form kept as hidden placeholder so existing JS hide/show calls don't error -->
                            <div class="add-form add-activity-form" style="display:none;"></div>
<!--                         <div class="add-form add-activity-form">
                            <div class="row" id="estactivityaddrow">
                                <div class="col-md-1"></div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Activity Name</label>
                                        <input type="text" id="estactivityname1" data-id="1" name="estactivityname[]" class="form-control estactivityname" placeholder="Activity Name">
                                        <span class="error" style="display: none;"></span>
                                    </div>  
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Activity Unit</label>
                                        <input type="text" id="estactivityunit1" data-id="1" name="estactivityunit[]" class="form-control estactivityunit" placeholder="Activity Unit">
                                        <span class="error" style="display: none;"></span>
                                    </div>  
                                </div>
                                <div class="col-md-4 text-right">
                                   
                                   <button type="button"  id="addmoreactivity" class="btn btn-primary add_estactivity_button small75" id="addmoreactivity" value="Add"><span class="icon-check"></span>Add</button>
                                    <label></label>
                                    <button type="button" class="btn btn-danger cancelactivity" id=""><span class="icon-close"></span> Cancel</button>
                                    <button type="button" class="btn btn-primary" id="saveestactivity"><span class="icon-check"></span> Add Activity</button>
                                </div>
                                <div class="col-md-1"></div>
                            </div>
                        </div> -->
                            <!-- form ends here -->
                            
                            
                            
                            <!-- edit form starts here -->
                            <div class="edit-form edit-activity-form">
                                <div class="row">
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Activity Name</label>
                                            <input type="text" class="form-control" id="editactivity" placeholder="Activity Name">
                                            <span style="color:red;"></span>
                                        </div>  
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Activity Unit</label>
                                            <input type="text" class="form-control" id="editactivityunit" placeholder="Activity Unit">
                                        </div>  
                                    </div>
                                    <div class="col-md-4 text-left ">
                                        <label>&nbsp;</label>
                                        <button type="button" class="btn btn-danger cancelactivity" id="" style="border-radius: 25px;"><span class="icon-close"></span> Cancel</button>
                                        <button type="button" class="btn btn-primary text-button" id="saveactivitybutton"><span class="icon-check"></span> Edit Activity</button>
                                    </div>
                                </div>
                            </div>

                            <!-- edit form ends here -->
                            
                            
                            
                            
                            
                            <!-- list start here -->
                            <input type="hidden" id="searchestactivitytype" name="activitytypeid">
                            <div class="preloader" style="display: none;"><center>
                                <img src="/opiamnew/web/images/loader.gif" align="middle"></center>
                            </div>
                            <div class="activities-cntnt-wrpr data-content-list" id="estactivityitems">
                                                        
                            </div>
                            <div class="Estimate-allocate" id="Ar-allocate-body-two" style="display: none;">
                                                    <div class="col-md-12 project-boq" >
                                                        <br>
                                                    </div>
                                                        <div class="col-md-12 type project-boq toprws" >
                                                            <div class="col-md-1"></div>
                                                            <div class="col-md-3">
                                                            <label style="margin-top: 8px;"><span><b id="activityrate" style="color: black;"></b></span></label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                
                                                                    <div class="col-md-3"></div>
                                                            <div class="col-md-3">
                                                                <label><span><b id="activityunt" style="color: black;"></b></span></label>
                                                            </div>
                                                            <div class="col-md-3"></div>
                                                            <div class="col-md-3">
                                                            <label><span><b id="activityratenow" style="color: black;"></b></span></label>
                                                            </div>
                                                        </div>
                                                            <div class="col-md-2" style="text-align: right;">
                                                                <a href="#" class="btn btn-primary text-button back-butn-new close-prjctactactvty">Back</a>
                                                            </div>    
                                                        </div>
                                                        <div class="allocated-list-cntnt-wrpr added-alloc-items-wrpr data-content-list">
                                                <div class="infodisplay"></div>
                                                <input type="hidden" id="EstActivity_Id" name="">
                                                <a id="addedlist" href="#"></a>
                                                <div class="row added-alloc-items-heading">
                                                </div>
                                                <div class="allocation-list-items-cntnr added-activity-list-wrpr collapse in allocation-list-items-master" id="add-activity-cbody">

                                                </div>
                                                
                                            </div><br>
                                            <div class="col-md-12 type project-boq" >
                                                <a href="javascript:void(0);"  class="btn btn-primary resource-list-btn"><span class="icon-add"></span> Allocate Resources</a>
                                                <a href="javascript:void(0);" class="btn btn-primary close-resource-list-btn">Close Allocation</a>&nbsp;&nbsp;&nbsp;
                                                <a href="javascript:void(0);" class="expand-collapse-palist icon-arrow-up1" data-toggle="collapse" data-target="#add-activity-cbody" style="display: none;"></a>
                                            </div>
                                            </div></div>
                                            
                                            <div class="allocation-cntnt-wrpr resource-not-allocated">
                                    <div class="allocation-left-bar items-select-bar">
                                    </div>
                                    <div class="resource-not-allocated-info">
                                        <div class="icon-info3"></div>
                                        <div class="info-text">
                                            <p id="alredyadded">Resources not allocated to the activity</p>
                                            <span>To allocate resources, click on the left tab</span>
                                        </div>
                                    </div>
                                    <div class="resources-from-selected-item-wrpr ">
                                        <div class="row">
                                            <div class="col-md-12 ">
                                            
                                                <div class="preloader" style="display: none;"><center><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"></center></div>
                                                <div class="search-and-allocate-content-wrpr">

                                                <div class="search-and-actions-wrpr row">
                                                    <div class="content-search-wrpr col-md-12 col-sm-12">
                                                        <input type="hidden" id="resourcegroupval" name="">
                                                        <!--<input type="search" onsearch="OnSearch()" class="form-control searchdefault resources" list="resourcelist" id="resourcegroup1" name="accounthead-choice" placeholder="Search"/>-->
                                                        <input type="search" class="form-control searchdefault resources" id="resourcegroup1" name="accounthead-choice" placeholder="Search"/>
                                                        <button id="actmaster-res" class="btn btn-primary searchresoureskey" type="button"><span class="icon-search5"></span></button>

                                                                        <!--<datalist id="resourcelist">
                                                                            <?php
                                                                        
                                                    /*$resources=Resources::find()->where(['status'=>0])->andwhere(['pricing_status'=>0])->groupBy(['Name'])->all();

                                                                            foreach($resources AS $resource):
                                                                                echo "<option value='".$resource->Name."'></option>";
                                                                            endforeach;*/
                                                                            ?>
                                                                        </datalist>-->
                                                    </div>
                                                </div>


                                                <div class="panel-group allocation-items allocation-items-master" id="accordionmast">

                                                    </div>
                                                </div>
                                                
                                            </div>	
                                            
                                        </div>
                                        
                                    </div>
                                    
                                </div>

                            <!-- list end here -->
                        </div>
                    </div>

                    <div style="padding:16px 0 8px 0;text-align:left;">
                        <button type="button" class="btn menu4-win-close" style="position:static;font-size:14px;background:#e67e22;color:#fff;border-color:#d35400;border-radius:20px;padding:6px 20px;"><span class="icon-close"></span> Close</button>
                    </div>

        </div>
    </div>
</div>

<!-- ── ADD ACTIVITY MODAL ───────────────────────────────────────────── -->
<div class="modal fade" id="alAddActivityPopup" data-backdrop="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="alAddActivityTitle" style="float:left;">Add Activity</h4>
                <button type="button" class="close cancelactivity" data-dismiss="modal" style="float:right;font-size:30px;">&times;</button>
            </div>
            <div class="modal-body">
                <form id="estactivityform">
                    <input type="hidden" id="editingActivityId" name="activity_id" value="">
                    <div class="row" id="estactivityaddrow">
                        <!-- Project Type + IOW Group + Activity Type -->
                        <div class="row" style="margin-top:6px;">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Project Type</label>
                                    <select id="estworktypelistss1" data-id="1" class="form-control estworktypelistses" name="worktypeid">
                                        <option value="0">Select Project Type</option>
                                        <?php
                                        $typelist=EstimateWorkType::find()->where(['estworktype_status'=>0])->orderBy(['estworktype_name'=>SORT_ASC])->all();
                                        foreach($typelist AS $list):
                                            echo "<option value='".$list->estworktype_id."'>".$list->estworktype_name."</option>";
                                        endforeach;
                                        ?>
                                    </select>
                                    <span style="color:red;"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>IOW Group</label>
                                    <select class="form-control" id="actIowGroupId" name="iow_group_id">
                                        <option value="">Select IOW Group</option>
                                    </select>
                                    <span style="color:red;"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Activity Type</label>
                                    <select class="form-control" id="estactivitytypeid" name="activitytypeid">
                                        <option value="0">Select Type</option>
                                        <?php
                                        $activityTypes = EstimateActivityType::find()->orderBy(['activitytype_name' => SORT_ASC])->all();
                                        foreach ($activityTypes as $at):
                                            echo "<option value='" . $at->activitytype_id . "'>" . htmlspecialchars($at->activitytype_name) . "</option>";
                                        endforeach;
                                        ?>
                                    </select>
                                    <span style="color:red;"></span>
                                </div>
                            </div>
                        </div>
                        <!-- Activity Name + Unit -->
                        <div class="row" style="margin-top:2px;">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Activity Name</label>
                                    <input class="form-control estactivityname" type="text" id="estactivityname1" data-id="1" name="estactivityname[]" placeholder="Activity Name">
                                    <span style="color:red;"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Activity Unit</label>
                                    <input class="form-control estactivityunit" type="text" id="estactivityunit1" data-id="1" name="estactivityunit[]" placeholder="Activity Unit">
                                    <span style="color:red;"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top:4px;">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Working Hours</label>
                                    <select class="form-control" id="est_working_hours" name="working_hours">
                                        <option value="8">8</option>
                                        <option value="10">10</option>
                                        <option value="12">12</option>
                                        <option value="24">24</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Tasks -->
                        <div class="row" style="margin-top:10px;">
                            <div class="col-md-12"><label style="font-weight:600;border-bottom:1px solid #eee;width:100%;padding-bottom:4px;">Tasks</label></div>
                        </div>
                        <div class="row" style="margin-bottom:4px;">
                            <div class="col-md-1"></div>
                            <div class="col-md-4"><label style="color:#777;font-size:12px;">Task Name</label></div>
                            <div class="col-md-2"><label style="color:#777;font-size:12px;">Unit</label></div>
                            <div class="col-md-3"><label style="color:#777;font-size:12px;">Productivity/day</label></div>
                        </div>
                        <div id="task-rows-container">
                            <div class="row task-row" id="task-row-1" style="margin-top:4px;">
                                <div class="col-md-1"></div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control task-name" name="task_name[]" placeholder="Task Name">
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control task-unit" name="task_unit[]" placeholder="Unit">
                                </div>
                                <div class="col-md-3">
                                    <input type="number" step="0.001" min="0" class="form-control task-productivity" name="task_productivity[]" placeholder="Productivity/day">
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="add-task-row" style="background:none;border:none;padding:6px 4px;"><span class="icon-add" style="color:#337ab7;font-size:18px;"></span></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn cancelactivity" data-dismiss="modal" style="background:#e67e22;color:#fff;border-color:#d35400;border-radius:20px;"><span class="icon-close"></span> Close</button>
                <button type="button" class="btn btn-primary save-btn" id="saveestactivity"><span class="icon-check"></span> Add Activity</button>
            </div>
        </div>
    </div>
</div>

<!-- ── PROJECT TYPE MODAL ────────────────────────────────────────────── -->
<div class="modal fade" id="alProjTypePopup" data-backdrop="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" style="float:left;">Project Type</h4>
                <button type="button" class="close" data-dismiss="modal" style="float:right;font-size:30px;">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-md-6">
                        <label>Project Type</label>
                        <input type="text" class="form-control" id="alProjTypeName" placeholder="Project Type">
                        <span class="error" id="alProjTypeErr" style="color:red;display:none;"></span>
                    </div>
                    <div class="col-md-4" style="padding-top:25px;">
                        <button type="button" class="btn btn-primary" id="alSaveProjType"><span class="icon-check"></span> Add Project Type</button>
                    </div>
                </div>
                <hr>
                <div id="alProjTypeListContainer" class="row"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" style="background:#e67e22;color:#fff;border-color:#d35400;"><span class="icon-close"></span> Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ── IOW GROUP MODAL ──────────────────────────────────────────────── -->
<div class="modal fade" id="alIowGroupPopup" data-backdrop="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" style="float:left;">Manage IOW Groups</h4>
                <button type="button" class="close" data-dismiss="modal" style="float:right;font-size:30px;">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-md-8">
                        <label>IOW Group</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="alIowGroupName" placeholder="IOW Group">
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-primary" id="alSaveIowGroup"><span class="icon-check"></span> Add</button>
                            </span>
                        </div>
                        <span class="error" id="alIowGroupErr" style="color:red;display:none;"></span>
                    </div>
                    <div class="col-md-2"></div>
                </div>
                <hr>
                <div id="alIowGroupListContainer" class="row"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" style="background:#e67e22;color:#fff;border-color:#d35400;"><span class="icon-close"></span> Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ── IOW MODAL ────────────────────────────────────────────────────── -->
<div class="modal fade" id="alIowPopup" data-backdrop="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" style="float:left;">Add Work Breakdown Structure</h4>
                <button type="button" class="close" data-dismiss="modal" style="float:right;font-size:30px;">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Item of Work Name</label>
                            <input type="text" class="form-control" id="alIowName" placeholder="IOW Name">
                            <span class="error" id="alIowNameErr" style="color:red;display:none;"></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Group Name</label>
                            <select class="form-control" id="alIowGroupSel">
                                <option value="">Select IOW Group</option>
                            </select>
                            <span class="error" id="alIowGroupSelErr" style="color:red;display:none;"></span>
                        </div>
                    </div>
                    <div class="col-md-3" style="padding-top:25px;">
                        <button type="button" class="btn btn-primary" id="alSaveIow"><span class="icon-check"></span> Add WBS</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" style="background:#e67e22;color:#fff;border-color:#d35400;"><span class="icon-close"></span> Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ── ACTIVITY TYPE MODAL ───────────────────────────────────────────── -->
<div class="modal fade" id="alActTypePopup" data-backdrop="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" style="float:left;"></h4>
                <button type="button" class="close" data-dismiss="modal" style="float:right;font-size:30px;">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-md-8">
                        <label>Activity Type</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="alActTypeName" placeholder="Activity Type">
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-primary" id="alSaveActType"><span class="icon-check"></span> Add</button>
                            </span>
                        </div>
                        <span class="error" id="alActTypeErr" style="color:red;display:none;"></span>
                    </div>
                    <div class="col-md-2"></div>
                </div>
                <hr>
                <div id="alActTypeListContainer" class="row"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" style="background:#e67e22;color:#fff;border-color:#d35400;"><span class="icon-close"></span> Close</button>
            </div>
        </div>
    </div>
</div>

<script>
(function(){

/* Move modals to <body> so accordion overflow/z-index doesn't trap them */
$('#alAddActivityPopup, #alProjTypePopup, #alIowGroupPopup, #alIowPopup, #alActTypePopup').appendTo('body');

/* These are plain Bootstrap-markup modals with no drag support at all —
   Bootstrap's own modal() JS plugin isn't active on this page (confirmed:
   $.fn.modal is undefined here), so there's no shown.bs.modal to hook.
   Make the header draggable directly: switch the centered .modal-dialog to
   an absolutely-positioned box at its current screen spot the first time
   it's dragged, then move it with the mouse/touch. */
var _alIds = ['alAddActivityPopup','alProjTypePopup','alIowGroupPopup','alIowPopup','alActTypePopup'];
_alIds.forEach(function(id){
  var modal = document.getElementById(id);
  if(!modal) return;
  var dialog = modal.querySelector('.modal-dialog');
  var hdr = modal.querySelector('.modal-header');
  if(!dialog || !hdr) return;
  dialog.id = id + '-dialog';
  hdr.style.cursor = 'move';
  var dragging = false, sx = 0, sy = 0, ox = 0, oy = 0;

  function anchor(){
    var r = dialog.getBoundingClientRect();
    dialog.style.position = 'fixed';
    dialog.style.margin = '0';
    dialog.style.left = r.left + 'px';
    dialog.style.top  = r.top  + 'px';
    return r;
  }
  function start(e){
    if(e.target.closest('.close')) return;
    var r = anchor();
    dragging = true; sx = e.clientX; sy = e.clientY; ox = r.left; oy = r.top;
    e.preventDefault();
  }
  function move(e){
    if(!dragging) return;
    e.preventDefault();
    var dx = e.clientX - sx, dy = e.clientY - sy;
    dialog.style.left = Math.max(0, ox + dx) + 'px';
    dialog.style.top  = Math.max(0, oy + dy) + 'px';
  }
  function end(){ dragging = false; }

  if(typeof window.bindDragTouch === 'function'){
    window.bindDragTouch(hdr, 'mousedown', start);
    window.bindDragTouch(document, 'mousemove', move, { passive: false });
    window.bindDragTouch(document, 'mouseup', end);
  } else {
    hdr.addEventListener('mousedown', start);
    document.addEventListener('mousemove', move);
    document.addEventListener('mouseup', end);
  }

  /* Position explicitly below Activity Library's own header on open,
     rather than relying on Bootstrap's centering + a post-hoc offset —
     that combination could read a not-yet-laid-out rect and misplace the
     dialog (e.g. pinned near the top of the viewport, oversized width).
     Each of the 4 popups gets a fixed diagonal step from a shared base
     position so opening several in a row still staggers them, and it's
     independent of getBoundingClientRect() timing. */
  var alWin = document.querySelector('.menu4-popup-cntnr');
  function reCenter(){
    var base = alWin ? alWin.getBoundingClientRect() : { top: 90, left: (window.innerWidth - 700) / 2 };
    var step = _alOpenSeq() * 30;
    dialog.style.position = 'fixed';
    dialog.style.margin = '0';
    dialog.style.width = '700px';
    dialog.style.maxWidth = 'calc(100vw - 40px)';
    var top  = Math.min(window.innerHeight - 120, base.top + 70 + step);
    var left = Math.min(window.innerWidth - 720, Math.max(10, base.left + 40 + step));
    dialog.style.top  = top + 'px';
    dialog.style.left = left + 'px';
    /* .modal itself is a full-viewport position:fixed layer (Bootstrap's
       own CSS) that intercepts clicks everywhere, even outside the visible
       dialog box — with these on top (z-index 999999) that silently
       swallows clicks meant for Activity Library's own toolbar buttons
       (e.g. "+ IOW Group") once one popup is open. Only the dialog itself
       should be clickable; the rest of the modal layer should pass clicks
       through to whatever's underneath. */
    modal.style.pointerEvents = 'none';
    dialog.style.pointerEvents = 'auto';
  }
  $(modal).on('show.bs.modal', reCenter);
  $(document).on('click', '[data-target="#' + id + '"]', reCenter);
});

/* Shared open-order counter for the 4 Activity Library sub-popups, so each
   one that opens while another is already visible steps diagonally from
   the last, wrapping after a few steps. */
var _alSeqState = { n: 0 };
function _alOpenSeq(){
  var anyOpen = _alIds.some(function(pid){
    var m = document.getElementById(pid);
    return m && getComputedStyle(m).display !== 'none';
  });
  if(!anyOpen){ _alSeqState.n = 0; return 0; }
  _alSeqState.n = (_alSeqState.n % 6) + 1;
  return _alSeqState.n;
}

/* Restore action bar whenever Add Activity modal closes (estactivity.js hides it on #addestactivity click) */
$('#alAddActivityPopup').on('hidden.bs.modal', function(){
    $('.search-and-actions-wrpr').css('display','flex');
    $('.content-action-wrpr').css('display','flex');
});

/* ── Project Type modal ── */
$(document).on('click', '#alOpenProjTypePopup', function(){
    $('#alProjTypeListContainer').html('<div class="col-md-12" style="padding:8px 15px;color:#999;">Loading…</div>');
    setTimeout(function(){ alLoadProjTypeList(); }, 300);
});
$('#alProjTypePopup').on('shown.bs.modal', function(){
    $('#alProjTypeName').val('').focus();
    $('#alProjTypeErr').hide();
    alLoadProjTypeList();
});

function alLoadProjTypeList(){
    $('#alProjTypeListContainer').html('<div class="col-md-12" style="padding:8px 15px;color:#999;">Loading…</div>');
    $.ajax({
        url: '<?php echo \yii\helpers\Url::to(["/projects/listworktype"]); ?>',
        type: 'POST',
        dataType: 'json',
        data: { worktypename: '' },
        success: function(data){
            if(data && data.result){
                $('#alProjTypeListContainer').html(data.result);
            } else {
                $('#alProjTypeListContainer').html('<div class="col-md-12" style="padding:8px 15px;color:#999;">No project types yet.</div>');
            }
        },
        error: function(xhr, status, err){
            $('#alProjTypeListContainer').html('<div class="col-md-12" style="padding:8px 15px;color:red;">Error: ' + xhr.status + ' ' + err + ' — ' + xhr.responseText.substring(0,200) + '</div>');
        }
    });
}

/* ── Inline edit for project type rows ── */
$(document).on('click', '.editForm', function(){
    var id   = $(this).data('id');
    var $tr  = $(this).closest('tr');
    var cur  = $('#protype'+id).val();
    /* Replace name cell with input + save/cancel */
    $tr.find('td:nth-child(2)').html(
        '<input type="text" class="form-control input-sm al-pt-edit-input" value="'+$('<div>').text(cur).html()+'" style="display:inline-block;width:70%;margin-right:4px;">'
        + '<button class="btn btn-success btn-xs al-pt-save" data-id="'+id+'" style="border-radius:4px;padding:2px 8px;">Save</button>'
        + '<button class="btn btn-default btn-xs al-pt-cancel" data-id="'+id+'" data-name="'+$('<div>').text(cur).html()+'" style="border-radius:4px;padding:2px 6px;margin-left:3px;">✕</button>'
        + '<input type="hidden" id="protype'+id+'" value="'+$('<div>').text(cur).html()+'">'
    );
});

$(document).on('click', '.al-pt-cancel', function(){
    alLoadProjTypeList();
});

$(document).on('click', '.al-pt-save', function(){
    var id   = $(this).data('id');
    var name = $(this).closest('td').find('.al-pt-edit-input').val().trim();
    if(!name){ alert('Enter a name.'); return; }
    var $btn = $(this).prop('disabled', true);
    $.ajax({
        type: 'POST',
        url: '<?php echo \yii\helpers\Url::to(["/projects/updateworktype"]); ?>',
        dataType: 'json',
        data: { worktypeid: id, worktype: name },
        success: function(data){
            $btn.prop('disabled', false);
            alLoadProjTypeList();
        },
        error: function(){ $btn.prop('disabled', false); alert('Server error.'); }
    });
});

function alRefreshProjTypeDropdowns(){
    $.ajax({
        url: '<?php echo \yii\helpers\Url::to(["/projects/listworktype"]); ?>',
        type: 'POST',
        dataType: 'json',
        data: { worktypename: '' },
        success: function(data){
            if(!data || !data.items) return;
            var opts = '<option value="0">Select Project Type</option>';
            var filterOpts = '<option value="0">All Project Types</option>';
            data.items.forEach(function(t){
                opts       += '<option value="'+t.id+'">'+$('<div>').text(t.name).html()+'</option>';
                filterOpts += '<option value="'+t.id+'">'+$('<div>').text(t.name).html()+'</option>';
            });
            $('.estworktypelistses').html(opts);
            $('#searchestworktypelist').html(filterOpts);
        }
    });
}

$(document).on('click', '.deleteestworktypebutton', function(){
    var id = $(this).data('id');
    if(!confirm('Delete this project type?')) return;
    var $btn = $(this).prop('disabled', true);
    $.ajax({
        type: 'POST',
        url: '<?php echo \yii\helpers\Url::to(["/projects/deleteworktype"]); ?>',
        dataType: 'json',
        data: { worktypeid: id },
        success: function(data){
            $btn.prop('disabled', false);
            alLoadProjTypeList();
            alRefreshProjTypeDropdowns();
        },
        error: function(){ $btn.prop('disabled', false); alert('Server error.'); }
    });
});

$(document).on('click', '#alSaveProjType', function(){
    var name = $('#alProjTypeName').val().trim();
    if(!name){ $('#alProjTypeErr').html('Enter project type name').show(); return; }
    $('#alProjTypeErr').hide();
    var btn = $(this).prop('disabled', true);
    $.ajax({
        type: 'POST',
        url: '<?php echo \yii\helpers\Url::to(["/projects/createworktype"]); ?>',
        dataType: 'json',
        data: { worktypename: name },
        success: function(data){
            btn.prop('disabled', false);
            if(data.error === 'No'){
                $('#alProjTypeName').val('');
                alLoadProjTypeList();
                alRefreshProjTypeDropdowns();
            } else {
                $('#alProjTypeErr').html(data.errortext || 'Could not save.').show();
            }
        },
        error: function(){ btn.prop('disabled', false); alert('Server error.'); }
    });
});

/* ── Add Activity modal ── */
$('#alAddActivityPopup').on('show.bs.modal', function(){
    /* only reset when opening for a new activity — edit handler sets _editMode before show() */
    if(!window._alEditMode){
        $('#editingActivityId').val('');
        $('#estactivityform')[0].reset();
        $('#task-rows-container .task-row:not(#task-row-1)').remove();
        $('#task-row-1 .task-name, #task-row-1 .task-unit, #task-row-1 .task-productivity').val('');
        $('#alAddActivityTitle').text('Add Activity');
        $('#saveestactivity').html('<span class="icon-check"></span> Add Activity');
    }
    window._alEditMode = false;
    alRefreshActIowGroupDropdown();
});

$(document).on('click', '#addestactivity', function(){
    setTimeout(function(){ alRefreshActIowGroupDropdown(); }, 400);
});

function alRefreshActIowGroupDropdown(selectVal){
    var cur = (selectVal !== undefined) ? selectVal : $('#actIowGroupId').val();
    $.ajax({
        url: '<?php echo \yii\helpers\Url::to(["/projects/getiowgrouplist"]); ?>',
        dataType: 'json',
        success: function(data){
            var sel = $('#actIowGroupId');
            sel.html('<option value="">Select IOW Group</option>');
            (data.items||[]).forEach(function(g){
                sel.append('<option value="'+g.id+'">'+g.name+'</option>');
            });
            if(cur) sel.val(cur);
        }
    });
}

/* Edit handler is in estactivity.js alongside the delete handler */;

/* Intercept estactivity.js showing .add-activity-form (edit flow) → open modal instead */
var alActObserver = new MutationObserver(function(mutations){
    mutations.forEach(function(m){
        if(m.target.style.display !== 'none' && $(m.target).hasClass('add-activity-form')){
            m.target.style.display = 'none'; /* keep placeholder hidden */
            $('#alAddActivityPopup').modal('show');
        }
    });
});
var alActTarget = document.querySelector('.add-activity-form');
if(alActTarget) alActObserver.observe(alActTarget, { attributes: true, attributeFilter: ['style'] });

/* close modal after successful save (estactivity.js hides .add-activity-form on success) */
$(document).on('click', '#saveestactivity', function(){
    setTimeout(function(){
        if($('.add-activity-form').is(':hidden')){
            $('#alAddActivityPopup').modal('hide');
        }
    }, 600);
});

/* ── IOW Group modal ── */
$(document).on('click', '#alOpenIowGroupPopup', function(){
    $('#alIowGroupListContainer').html('<div style="padding:8px 15px;color:#999;">Loading…</div>');
    setTimeout(function(){ alLoadIowGroupList(); }, 300);
});
$('#alIowGroupPopup').on('shown.bs.modal', function(){
    $('#alIowGroupName').val('');
    alLoadIowGroupList();
});

function alLoadIowGroupList(){
    $('#alIowGroupListContainer').html('<div style="padding:8px 15px;color:#999;">Loading…</div>');
    $.ajax({
        url: '<?php echo \yii\helpers\Url::to(["/projects/getiowgrouplist"]); ?>',
        dataType: 'json',
        success: function(data){
            if(data.result){
                $('#alIowGroupListContainer').html(data.result);
            } else {
                $('#alIowGroupListContainer').html('<div style="padding:8px 15px;color:#999;">No groups yet.</div>');
            }
        }
    });
}

/* ── IOW Group inline edit (table row) ── */
$(document).on('click', '.al-edit-iowgroup', function(){
    var id  = $(this).data('id');
    var $tr = $(this).closest('tr');
    var cur = $('#iowgrp'+id).val();
    $tr.find('td:nth-child(2)').html(
        '<input type="text" class="form-control input-sm al-iowgrp-edit-input" value="'+$('<div>').text(cur).html()+'" style="display:inline-block;width:70%;margin-right:4px;">'
        + '<button class="btn btn-success btn-xs al-iowgrp-save" data-id="'+id+'" style="border-radius:4px;padding:2px 8px;">Save</button>'
        + '<button class="btn btn-default btn-xs al-iowgrp-cancel" data-id="'+id+'" style="border-radius:4px;padding:2px 6px;margin-left:3px;">&#x2715;</button>'
        + '<input type="hidden" id="iowgrp'+id+'" value="'+$('<div>').text(cur).html()+'">'
    );
});

$(document).on('click', '.al-iowgrp-cancel', function(){
    alLoadIowGroupList();
});

$(document).on('click', '.al-iowgrp-save', function(){
    var id   = $(this).data('id');
    var name = $(this).closest('td').find('.al-iowgrp-edit-input').val().trim();
    if(!name){ alert('Enter a name.'); return; }
    var $btn = $(this).prop('disabled', true);
    $.ajax({
        type: 'POST',
        url: '<?php echo \yii\helpers\Url::to(["/workgroups1/iowgroupupdate"]); ?>',
        dataType: 'json',
        data: { groupid: id, groupname: name },
        success: function(data){
            $btn.prop('disabled', false);
            if(data.error === 'No'){
                alLoadIowGroupList();
                alRefreshIowGroupDropdown();
            } else {
                alert(data.errortext || 'Could not save.');
            }
        },
        error: function(){ $btn.prop('disabled', false); alert('Server error.'); }
    });
});

$(document).on('click', '.al-delete-iowgroup', function(e){
    e.preventDefault();
    var id   = $(this).data('id');
    var name = $(this).closest('tr').find('td:nth-child(2)').text().trim();
    if(!confirm('Delete IOW Group "' + name + '"?')) return;
    $.ajax({
        type: 'POST',
        url: '<?php echo \yii\helpers\Url::to(["/workgroups1/iowgroupdelete"]); ?>',
        dataType: 'json',
        data: { groupid: id },
        success: function(data){
            if(data.error === 'No'){
                alLoadIowGroupList();
                alRefreshIowGroupDropdown();
            } else {
                alert(data.errortext || 'Could not delete.');
            }
        }
    });
});

$(document).on('click', '#alSaveIowGroup', function(){
    var name = $('#alIowGroupName').val().trim();
    if(!name){ $('#alIowGroupErr').html('Enter IOW group name').show(); return; }
    $('#alIowGroupErr').hide();
    var btn = $(this).attr('disabled', true);
    $.ajax({
        type: 'POST',
        url: '<?php echo \yii\helpers\Url::to(["/workgroups1/iowgroupcreate"]); ?>',
        dataType: 'json',
        data: { iowgroupname: name },
        success: function(data){
            btn.attr('disabled', false);
            if(data.error === 'No'){
                $('#alIowGroupName').val('');
                alLoadIowGroupList();
                alRefreshIowGroupDropdown();
            } else {
                $('#alIowGroupErr').html(data.errortext).show();
            }
        }
    });
});

/* ── IOW modal ── */
$('#alIowPopup').on('show.bs.modal', function(){
    $('#alIowName').val('');
    alRefreshIowGroupDropdown();
});

function alRefreshIowGroupDropdown(){
    $.ajax({
        url: '<?php echo \yii\helpers\Url::to(["/projects/getiowgrouplist"]); ?>',
        dataType: 'json',
        success: function(data){
            var sel = $('#alIowGroupSel');
            var cur = sel.val();
            sel.html('<option value="">Select IOW Group</option>');
            (data.items||[]).forEach(function(g){
                sel.append('<option value="'+g.id+'">'+g.name+'</option>');
            });
            if(cur) sel.val(cur);
        }
    });
}

$(document).on('click', '#alSaveIow', function(){
    var name    = $('#alIowName').val().trim();
    var groupId = $('#alIowGroupSel').val();
    var pid     = $('#selectedProjectId').val() || $('input[name="project_id"]').first().val() || '';
    if(!name)   { $('#alIowNameErr').html('Enter IOW Name').show(); return; }
    if(!groupId){ $('#alIowGroupSelErr').html('Select IOW Group').show(); return; }
    $('#alIowNameErr, #alIowGroupSelErr').hide();
    var btn = $(this).attr('disabled', true);
    $.ajax({
        type: 'POST',
        url: '<?php echo \yii\helpers\Url::to(["/projects/addiow"]); ?>',
        dataType: 'json',
        data: { name: name, group_id: groupId, project_id: pid },
        success: function(data){
            btn.attr('disabled', false);
            if(data.error){ alert('Error: '+data.error); return; }
            $('#alIowName').val('');
            $('#alIowGroupSel').val('');
            $('#alIowPopup').modal('hide');
        }
    });
});

/* ── Activity Type modal ── */
$(document).on('click', '#alOpenActTypePopup', function(){
    $('#alActTypeListContainer').html('<div class="col-md-12" style="padding:8px 15px;color:#999;">Loading…</div>');
    setTimeout(function(){ alLoadActTypeList(); }, 300);
});
$('#alActTypePopup').on('shown.bs.modal', function(){
    $('#alActTypeName').val('');
    alLoadActTypeList();
});

function alLoadActTypeList(){
    $.ajax({
        url: '<?php echo \yii\helpers\Url::to(["/projects/getactivitytypelist"]); ?>',
        dataType: 'json',
        success: function(data){
            if(!data.items || !data.items.length){
                $('#alActTypeListContainer').html('<div class="col-md-12" style="padding:8px 15px;color:#999;">No activity types yet.</div>');
                return;
            }
            var html = '<table style="width:100%;border-collapse:collapse;font-size:13px;">'
                     + '<thead><tr>'
                     + '<th style="background:#555;color:#fff;padding:8px 12px;border:1px solid #444;text-align:center;width:42px;">#</th>'
                     + '<th style="background:#555;color:#fff;padding:8px 12px;border:1px solid #444;">Activity Type</th>'
                     + '<th style="background:#555;color:#fff;padding:8px 12px;border:1px solid #444;text-align:center;width:60px;"></th>'
                     + '</tr></thead><tbody>';
            data.items.forEach(function(at, i){
                var safeName = $('<div>').text(at.name).html();
                html += '<tr data-acttype-id="'+at.id+'">'
                      + '<td style="padding:8px 12px;border:1px solid #ddd;text-align:center;color:#999;">'+(i+1)+'</td>'
                      + '<td style="padding:8px 12px;border:1px solid #ddd;">'
                      +   '<span class="al-acttype-label">'+safeName+'</span>'
                      +   '<input type="text" class="al-acttype-input form-control input-sm" value="'+safeName+'" style="display:none;margin:0;">'
                      + '</td>'
                      + '<td style="padding:6px 8px;border:1px solid #ddd;text-align:center;white-space:nowrap;">'
                      +   '<button class="al-acttype-edit btn btn-default btn-xs" data-id="'+at.id+'" title="Edit" style="border-radius:50%;width:28px;height:28px;padding:0;margin-right:4px;"><span class="icon-pencil"></span></button>'
                      +   '<button class="al-acttype-delete btn btn-danger btn-xs" data-id="'+at.id+'" title="Delete" style="border-radius:50%;width:28px;height:28px;padding:0;"><span class="icon-trash1"></span></button>'
                      +   '<button class="al-acttype-save btn btn-success btn-xs" data-id="'+at.id+'" title="Save" style="border-radius:50%;width:28px;height:28px;padding:0;display:none;"><span class="icon-check"></span></button>'
                      + '</td>'
                      + '</tr>';
            });
            html += '</tbody></table>';
            $('#alActTypeListContainer').html(html);
        }
    });
}

$(document).on('click', '.al-acttype-edit', function(){
    var tr = $(this).closest('tr');
    tr.find('.al-acttype-label').hide();
    tr.find('.al-acttype-input').show().focus();
    tr.find('.al-acttype-save').show();
});

$(document).on('click', '.al-acttype-save', function(){
    var tr = $(this).closest('tr');
    var id = tr.data('acttype-id');
    var newName = tr.find('.al-acttype-input').val().trim();
    if(!newName){ alert('Name cannot be empty.'); return; }
    var btn = $(this).prop('disabled', true);
    $.ajax({
        type: 'POST',
        url: '<?php echo \yii\helpers\Url::to(["/projects/updateactivitytype"]); ?>',
        dataType: 'json',
        data: { activitytypeid: id, activitytype: newName, schedule_type: 0 },
        success: function(data){
            btn.prop('disabled', false);
            if(data.error && data.error !== 'No'){ alert('Error saving.'); return; }
            tr.find('.al-acttype-label').text(newName).show();
            tr.find('.al-acttype-input').hide();
            tr.find('.al-acttype-save').hide();
            tr.find('.al-acttype-edit').show();
        },
        error: function(){ btn.prop('disabled', false); alert('Request failed.'); }
    });
});

$(document).on('click', '.al-acttype-delete', function(){
    var id = $(this).data('id');
    var name = $(this).closest('tr').find('.al-acttype-label').text().trim();
    if(!confirm('Delete activity type "' + name + '"?')) return;
    var $btn = $(this).prop('disabled', true);
    $.ajax({
        type: 'POST',
        url: '<?php echo \yii\helpers\Url::to(["/projects/deleteactivitytype"]); ?>',
        dataType: 'json',
        data: { activitytypeid: id },
        success: function(data){
            $btn.prop('disabled', false);
            alLoadActTypeList();
        },
        error: function(){ $btn.prop('disabled', false); alert('Server error.'); }
    });
});

$(document).on('click', '#alSaveActType', function(){
    var name = $('#alActTypeName').val().trim();
    if(!name){ $('#alActTypeErr').html('Enter activity type name').show(); return; }
    $('#alActTypeErr').hide();
    var btn = $(this).attr('disabled', true);
    $.ajax({
        type: 'POST',
        url: '<?php echo \yii\helpers\Url::to(["/projects/addactivitytype"]); ?>',
        dataType: 'json',
        data: { name: name, schedule: 0 },
        success: function(data){
            btn.attr('disabled', false);
            if(data.error){ alert('Error: '+data.error); return; }
            $('#alActTypeName').val('');
            alLoadActTypeList();
        }
    });
});

})();
</script>


