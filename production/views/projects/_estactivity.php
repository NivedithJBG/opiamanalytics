<?php

use app\models\EstimateWorkType;
use app\models\EstimateActivityType;
use app\models\Resources;
?>

<div class="panel panel-default activities-masters-tab tab-wrapper tab acco-three allocate-resource-tabss" id="estactivity">
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
    <!--<input type="radio" id="rd5" class="prjct-estactvts" name="rd">-->
    <div class="panel-heading">
      <h4 class="panel-title prjct-estactvts" id="act-lib-actvty">
        <a data-toggle="collapse" data-parent="#accordionpromasterind" href="#collapsemasteract">
        <span class="icon-directions_run"></span>Activities</a>
      </h4>
   </div>

    <div id="collapsemasteract" class="tab-content cOrder-body panel-collapse collapse">
        <div class="panel-body ">

                    <form id="estactivityform">
                    <input type="hidden" id="editingActivityId" name="activity_id" value="">
                    <div class="search-and-content-wrpr">
                        <div class="search-and-actions-wrpr row" id="AR-allocate-body-one-head">

                    <div class="col-md-3" id="searchestworktypediv">
                        <select id="searchestworktypelist" class="form-control" >
                            <option value="0">All Project Types</option>
                            <?php
                                $typelist=EstimateWorkType::find()->orderBy(['estworktype_name'=>SORT_ASC])->all();
                           // $tabs = UserTabs::find()->where(['user_id' => $userid])->andWhere('function_id =11')->all();
                            foreach($typelist AS $list):
                                echo "<option value='".$list->estworktype_id."'>".$list->estworktype_name."</option>";
                            endforeach;
                            ?>
                        </select>
                        <span class="error" style="display: none;"></span>
                            <input type="hidden" name="estworktypedisplay" id="estworktypedisplay">
                    </div>

                    
                            <div class="content-search-wrpr col-md-5 col-sm-5" >
                                <input type="text" placeholder="Search" id="searchestactivityname" class="form-control" >
                                <button id="estactivitysearch" class="btn btn-primary" type="button"><span class="icon-search5"></span></button>
                            </div>
                            <div class="col-md-2 col-sm-2" id="space"></div>
                            <div class="content-action-wrpr col-md-2 col-sm-2" >
                                <a href="#" class="btn btn-primary addForm" id="addestactivity" title="Add Activities"><span class="icon-add"></span> Add</a>
                                <a href="#" class="btn btn-primary list-accountType" id="listestactivity"><span class="icon-th-list"></span> List</a>
                            </div>
                        </div>
                        <div class="content-wrpr">
                            
                            <!-- form starts here -->

                            <div class="add-form add-activity-form">

                                <div class="row" id="estactivityaddrow">

                                    <!-- Project Type + Activity Type + Name + Unit + Working Hours -->
                                    <div class="row" style="margin-top:10px;">
                                        <div class="col-md-1"></div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Project Type</label>
                                                <select id="estworktypelistss1" data-id="1" class="form-control estworktypelistses" name="worktypeid">
                                                    <option value="0">Select Project Type</option>
                                                    <?php
                                                    $typelist=EstimateWorkType::find()->orderBy(['estworktype_name'=>SORT_ASC])->all();
                                                    foreach($typelist AS $list):
                                                        echo "<option value='".$list->estworktype_id."'>".$list->estworktype_name."</option>";
                                                    endforeach;
                                                    ?>
                                                </select>
                                                <span style="color:red;"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
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
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Activity Name</label>
                                                <input class="form-control estactivityname" type="text" id="estactivityname1" data-id="1" name="estactivityname[]" placeholder="Activity Name">
                                                <span style="color:red;"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Activity Unit</label>
                                                <input class="form-control estactivityunit" type="text" id="estactivityunit1" data-id="1" name="estactivityunit[]" placeholder="Activity Unit">
                                                <span style="color:red;"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label>Wk.Hrs</label>
                                                <select class="form-control" id="est_working_hours" name="working_hours">
                                                    <option value="8">8</option>
                                                    <option value="10">10</option>
                                                    <option value="12">12</option>
                                                    <option value="24">24</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-1"></div>
                                    </div>

                                    <!-- Tasks header -->
                                    <div class="row" style="margin-top:6px;">
                                        <div class="col-md-1"></div>
                                        <div class="col-md-4"><label style="font-weight:600;">Tasks</label></div>
                                        <div class="col-md-2"><label style="font-weight:600;">Unit</label></div>
                                        <div class="col-md-3"><label style="font-weight:600;">Productivity</label></div>
                                        <div class="col-md-1"></div>
                                    </div>

                                    <!-- Task rows container -->
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
                                                <input type="number" step="0.001" min="0" class="form-control task-productivity" name="task_productivity[]" placeholder="Productivity/Wh">
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="add-task-row" style="background:none;border:none;padding:6px 4px;"><span class="icon-add" style="color:#337ab7;font-size:18px;"></span></button>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div style="height:60px;"></div>
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <button type="button" class="btn btn-danger cancel cancelactivity"><span class="icon-close"></span> Cancel</button>
                                        &nbsp;&nbsp;
                                        <button type="button" class="btn btn-primary save-btn" id="saveestactivity"><span class="icon-check"></span> Add Activity</button>
                                    </div>
                                </div>
                                <div style="height:40px;"></div>
                            </div>
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
                            </div></form>
                            
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
                
        </div>
    </div>
</div>


