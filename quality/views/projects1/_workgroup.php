<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/projects/workgroupfunctions.js" type="text/javascript"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap.min.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/ckeditor/ckeditor.js"></script>
<h2 class="acc_trigger" id="workgroup"><a href="#"><span id="workgroup_name">4. WBS</span></a></h2>
<div class="acc_container">
    <div class="row" style="margin-top: 10px;margin-left: 10px;display:none;">        
        <div class="col-md-4">
          <button type="button" class="btn btn-danger wbs_item_button" style="font-size: 12px;font-weight:500;padding-left:120px;padding-right:120px;width:100%;" id="wbs_estimate" disabled>WBS Estimate</button>
        </div>
        <div class="col-md-4" style="text-align:center;">
          <button type="button" class="btn btn-danger wbs_item_button" style="font-size: 12px;font-weight:500;padding-left:120px;padding-right:120px;width:100%;" id="wbs_schedule">WBS Schedule</button>
        </div>
        <div class="col-md-4" style="text-align:right;">
          <button type="button" class="btn btn-danger wbs_item_button" style="font-size: 12px;font-weight:500;padding-left:120px;padding-right:120px;width:100%;" id="wbs_cashflow">WBS Cashflow</button>
        </div>
    </div>
    <div id="wbs_estimate_block" class="wbs_item" style="display:none;">
        <div class="block">
            <div class="jumbotron"> 

                <div class="row show-grid">
                    <div class="col-md-2" style="text-align: left;" id="dispprojectname">
                    </div>
                    <div class="col-md-3"><button type="button" class="btn btn-success" id="addworkgroup"><span class="glyphicon glyphicon-plus-sign"></span>Add Item</button></div>
                    <div class="col-md-3"><button type="button" class="btn btn-danger" id="listworkgroup"><span class="glyphicon glyphicon-list-alt"></span>List Project IOW</button></div>
                    <div class="col-md-12">
                       <!-- <div style="float: left;width: 250px;">
                        <select name="estimatestructure" class="form-control" id="estimatestructure">

                        </select>
                        <input class="form-control" type="text" id="newwbsestimatename" name="estimatestructurename" value="" style="display:none;">
                        </div>
                        <div style="float: left;width: 100px;margin-left: 10px;margin-top: 3px;">
                           
                        <button style="width:auto;padding: 10px;padding-left: 12px;padding-right:12px;display:none;" type='button' class='btn btn-primary editestimatestructure' value='' id='editestimatestructure' title='Edit Estimate Structure Name'><span class="glyphicon glyphicon-pencil"></span></button>
                        <button style="width:auto;padding: 10px;padding-left: 12px;padding-right:12px;display:none;" type='button' class='btn btn-primary saveestimatestructure' value='' id='saveestimatestructure' title='Save Estimate Structure Name'><span class="glyphicon glyphicon-save"></span></button>
                        <button style="width:auto;padding: 10px;padding-left: 12px;padding-right:12px;display:none;" type='button' class='btn btn-primary deleteestimatestructure' value='' id='deleteestimatestructure' title='Delete Estimate Structure Name'><span class="glyphicon glyphicon-trash"></span></button>
                            
                        <button style="display:none;" type='button' class='btn btn-primary schedulestructure' value='' id='schedulestructure' title='Schedule Structure'>Schedule</button>
                        <input type="hidden" id="estimatestructure" name="mode" value="" />
                        </div>-->
                    </div>
                </div>
                <div class="schedulesuccess" style="float: left;margin-left: 50px;margin-bottom: 5px;display:none;"><span style="color:green;font-size: 12px;">Schedule Items Created</span></div>
                <div id="workgrouplistsection">
                    <!--<div class="row show-grid" style="background-color: rgb(186, 211, 235);padding-top: 18px;">
                        <div class="col-md-4">
                            <input class="form-control" id="searchname" type="text" placeholder="Search">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger" id="resourcesearch"><span class="glyphicon glyphicon-search" ></span>Search</button>
                        </div>
                        </div>-->
                    <div class="row show-grid">
                        <!--Table-->
                        <form>
                            <table class="table table-bordered" id="workgrouptable" style="display: table;">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <!-- <th>Worktype</th> -->
                                    <th>IOW</th>
                                    <th>Unit</th>
                                    <th>Quantity</th>
                                    <th></th>
                                    <th></th>
                                    <!-- <th></th>
                                    <th></th>
                                    <th></th> -->
                                    <th>Edit</th>
                                    <th>Delete</th>
                                </tr>
                                <tr class="preloader" style="display: none;"><td colspan="9" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                                </thead>
                                <tbody id="workgroupitems">

                                </tbody>
                            </table>
                        </form>
                       <!-- <div style="width: 100px;float: right;margin-top: 2px;">
                         <button type="button" class="btn btn-danger" id="iowstructurename"><span class="glyphicon glyphicon-saved"></span>Save as</button>
                         <button style="display:none;" type="button" class="btn btn-danger" id="duplicatewbs" value=''><span class="glyphicon glyphicon-saved"></span>Save as</button>                        
                        </div>
                       <div style="width: 250px;text-align: center;float: right;margin-right: 15px;margin-bottom: 3px;"><input class="form-control" type="text" id="wbsestimatename" name="structurename" placeholder="Wbs estimate structure name"><span style="color:red;font-size: 12px"></span></div>-->
                    </div>
                </div>
                <div class="modal fade " id="MethedologyModel" >
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title"><span id="methedologyprojectname"></span></h4>
                            </div>
                            <div class="panel-heading">
                                <div id="itemnamemethedology">


                                </div>
                            </div>
                            <div class="modal-body" >
                                <div class="methedologypreloader" style="display: none;"><span align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </span></div>
                                <div id="methedologyshow"></div>
                                <input type="hidden" id="currentiowmethedology" value="0">
                            </div>
                            <div class="modal-footer">
                                <div class="col-md-6"></div>
                                <div class="col-md-3"><button type="button" class="btn btn-default" id="savemethedology" data-dismiss="modal">Save</button></div>
                                <div class="col-md-3"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div>


                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div>
                <div class="modal fade " id="notesModel1" >
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title"><span id="notesiowname"></span></h4>
                            </div>
                            <div class="panel-heading">
                                <!--<div id="notesitems">


                                </div>-->
                            </div>
                            <div class="modal-body" >
                                <div class="preloader" style="display: none;"><span align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </span></div>
                                <div id="notesiowshow"></div>
                                <input type="hidden" id="currentiow" value="0">
                            </div>
                            <div class="modal-footer">
                                <div class="col-md-6"></div>
                                <div class="col-md-3"><button type="button" class="btn btn-default" id="saveiownotes" data-dismiss="modal">Save</button></div>
                                <div class="col-md-3"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div>


                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div>
                <div id="workgroupaddsection" class="row show-grid">
                    <form id="addworkgroupform">
                        <table class="table table-bordered" style="overflow: hidden; display: table; background-color: rgb(226, 226, 226);" id="resourcevalueadd">
                            <tbody>
                            <tr>
                                <th>#</th>
                                <!-- <th>
                                    <select name="worktypegroups" class="form-control" id="worktypegroups">
                                        <option value="none">Select Project Type</option>
                                        <?php
                                        //$worktypegroups=Worktypegroups::model()->findAll(array('order'=>'sortorder ASC'));
                                        $worktypegroups=Worktypegroups::model()->findAll(array('condition'=>'status =0','order'=>'sortorder ASC'));
                                        foreach($worktypegroups AS $worktypegroup):
                                            echo "<option value='".$worktypegroup['worktypegroup_id']."'>".$worktypegroup['name']."</option>";
                                        endforeach;?>
                                    </select>
                                    <span class="error" style="display: none;"></span>
                                </th> -->

                                <!-- <th>
                                    <select name="worktype" class="form-control" id="worktype">
                                        <option value="none">Select IOW Type</option>
                                        
                                        $worktypes=Worktype::model()->findAll(array('order'=>'sortorder ASC'));
                                        foreach($worktypes AS $worktype):
                                        echo "<option value='".$worktype['worktype_id']."'>".$worktype['name']."</option>";
                                        endforeach; 
                                    </select>
                                    <span class="error" style="display: none;"></span>
                                </th> -->
                                <th><input class="form-control" type="text" id="workgroupname" name="projectname" placeholder="IOW Name"><span class="error" style="display: none;"></span></th>
                                <th><input class="form-control" type="text" id="workgroupunit" name="workgroupunit" placeholder="IOW Unit"><span class="error" style="display: none;"></span></th>
                                <th><input class="form-control" type="text" id="workgroupquantity" name="workgroupquantity" placeholder="IOW Quantity"><span class="error" style="display: none;"></span></th>
                                
                                <th><button type="button" class="btn btn-danger" id="saveworkgroup"><span class="glyphicon glyphicon-saved"></span>Save</button></th>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                </div>

            </div>
        </div>
    </div>
    
    <!-- WBS SCHEDULE BLOCK -->
    <div id="wbs_schedule_block" class="wbs_item" style="display:none;">
        <div class="block">
            <div class="jumbotron"> 
                <div class="row show-grid">
                    <div class="col-md-2" style="text-align: left;" id="dispprojectname_schedule">
                    </div>
                    <div class="col-md-3"><button type="button" class="btn btn-success" id="addschedule_item"><span class="glyphicon glyphicon-plus-sign"></span>Add Schedule Item</button></div>
                    <div class="col-md-3"><button type="button" class="btn btn-danger" id="listwbs_schedule"><span class="glyphicon glyphicon-list-alt"></span>List Schedule Item</button></div>
                    <div class="col-md-3"><!--<button type="button" class="btn btn-danger" id="wbs_schedule_relation"><span class="glyphicon glyphicon-list-alt"></span>Relation</button>--></div>
                    <div class="col-md-12">
                  <!--  
                   <div style="width: 100px;float: right;margin-top: 2px;">
                        <button type="button" class="btn btn-danger" id="savewbsgroupname"><span class="glyphicon glyphicon-saved"></span>Save</button>
                        <button style="display:none;" type="button" class="btn btn-danger" id="duplicatewbs" value=''><span class="glyphicon glyphicon-saved"></span>Duplicate</button>
                    </div>
                    <div style="width: 250px;text-align: center;float: right;margin-right: 15px;margin-bottom: 3px;"><input class="form-control" type="text" id="wbsname" name="structurename" placeholder="Wbs structure name"><span style="color:red;font-size: 12px"></span></div> 
                  -->  
                       <!--  <div style="float: left;width: 250px;">
                        <select name="oldstructure" class="form-control" id="oldstructure">

                        </select>
                        <input class="form-control" type="text" id="newwbsschedulename" name="schedulestructurename" value="" style="display:none;">
                        </div> --> 
                        <div style="float: left;width: 100px;margin-left: 10px;margin-top: 3px;">
                        <button style="display:none;" type='button' class='btn btn-primary editoldstructure' value='' id='editoldstructure' title='Edit Structure'> <span class='glyphicon glyphicon-pencil'></span>Edit</button>
                        <button style="display:none;" type='button' class='btn btn-primary saveoldstructure' value='' id='saveoldstructure' title='Save Structure' style="display:none;"> <span class='glyphicon glyphicon-saved'></span>Save</button>
                        
                        <button type='button' class='btn btn-primary editstructurebuttonschedule' value='' id='editstructurebuttonschedule' title='Rename Structure Name' style='width:auto;padding: 10px;padding-left: 12px;padding-right:12px;display:none;display:none;'> <span class='glyphicon glyphicon-pencil'></span></button>
                        <button type='button' class='btn btn-primary savestructurebuttonschedule' value='' id='savestructurebuttonschedule' title='Save' style='width:auto;padding: 10px;padding-left: 12px;padding-right:12px;display:none;display:none;'> <span class='glyphicon glyphicon-save'></span></button>
                        <button type='button' class='btn btn-primary deletestructurebuttonschedule' value='' id='deletestructurebuttonschedule' title='Delete Structure Name' style='width:auto;padding: 10px;padding-left: 12px;padding-right:12px;display:none;display:none;'> <span class='glyphicon glyphicon-trash'></span></button>
                        
                        <input type="hidden" id="mode-edit" name="mode" value="" />
                        </div>
                        <div id="ganttchartshow" style="float: right;display:none;">
                           
                        </div>
                    </div>
                </div>
                <div id="wbsscheduleitemlistsection">
                    <!--<div class="row show-grid" style="background-color: rgb(186, 211, 235);padding-top: 18px;">
                        <div class="col-md-4">
                            <input class="form-control" id="searchname" type="text" placeholder="Search">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger" id="resourcesearch"><span class="glyphicon glyphicon-search" ></span>Search</button>
                        </div>
                        </div>-->
                    <div class="row show-grid">
                        <!--Table-->
                        <form>
                            <table class="table table-bordered" id="wbsscheduleitemtable" style="display: table;">
                                <thead>
                                <tr>
                                    <th>#</th>
                                   <!-- <th>Schedule Group</th> -->
                                    <th>Schedule Item</th>
                                    <th></th>
                                    <!-- <th></th>
                                    <th></th> -->
                                    <!--<th></th>-->
                                    <th>Edit</th>
                                    <th>Delete</th>
                                </tr>
                                <tr class="preloader" style="display: none;"><td colspan="9" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                                </thead>
                                <tbody id="wbsscheduleitems" class="ui-sortable">

                                </tbody>
                                <tbody id="wbsstructureitems" style="display: none;" class="ui-sortable">

                                </tbody>
                            </table>
                        </form>
                        
                        <!--<div style="width: 100px;float: right;margin-top: 2px;">
                        <button type="button" class="btn btn-danger" id="savewbsgroupname"><span class="glyphicon glyphicon-saved"></span>Save</button>
                        <button style="display:none;" type="button" class="btn btn-danger" id="duplicatewbs123" value=''><span class="glyphicon glyphicon-saved"></span>Duplicate</button>
                        </div>
                        <div style="width: 250px;text-align: center;float: right;margin-right: 15px;margin-bottom: 3px;"><input class="form-control" type="text" id="wbsname" name="structurename" placeholder="Wbs structure name"><span style="color:red;font-size: 12px"></span></div>-->
                    </div>
                </div>
               
                <div id="scheduleitemaddsection" class="row show-grid">
                    <form id="addscheduleitemform">
                        <table class="table table-bordered" style="overflow: hidden; display: table; background-color: rgb(226, 226, 226);" id="resourcevalueadd">
                            <tbody>
                            <tr>
                                <th>#</th>
                              <!--  <th>
                                <input type="text" class="form-control" id="schedulegroup_name" placeholder="Add Schedule Group" list="groups" />
                                <datalist id="groups">
                               
                                 <option>Volvo</option>
                                <option>Saab</option>
                                <option>Mercedes</option>
                                <option>Audi</option> 
                                </datalist>
                                     <select name="schedulegroups" class="form-control" id="schedulegroups">
                                        <option value="none">Select Schedule Group</option>
                                        <?php
                                      //  $worktypegroups=Worktypegroups::model()->findAll(array('order'=>'name ASC'));
                                      //  foreach($worktypegroups AS $worktypegroup):
                                      //      echo "<option value='".$worktypegroup['worktypegroup_id']."'>".$worktypegroup['name']."</option>";
                                      //  endforeach;?>
                                    </select> 
                                    <span class="error" style="display: none;"></span>
                                </th> -->

                                <th>
                                    <input type="text" class="form-control" id="scheduleitem_name" placeholder="Schedule Item Name">
                                    <!-- <select name="worktype" class="form-control" id="scheduleitem">
                                        <option value="none">Select Worktype</option>
                                        <?php
                                        $worktypes=Worktype::model()->findAll(array('order'=>'name ASC'));
                                        foreach($worktypes AS $worktype):
                                        echo "<option value='".$worktype['worktype_id']."'>".$worktype['name']."</option>";
                                        endforeach;?>
                                    </select> -->
                                    <span class="error" style="display: none;"></span>
                                </th>
                                <!-- <th><input class="form-control" type="text" id="workgroupname" name="projectname" placeholder="IOW Name"><span class="error" style="display: none;"></span></th> -->
                                <th></th>
                                <th><button type="button" class="btn btn-danger" id="savescheduleitem"><span class="glyphicon glyphicon-saved"></span>Save</button></th>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                </div>

                <!-- <div id="wbsactivityrelation">
                    <--<div class="row show-grid" style="background-color: rgb(186, 211, 235);padding-top: 18px;">
                        <div class="col-md-4">
                            <input class="form-control" id="searchname" type="text" placeholder="Search">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger" id="resourcesearch"><span class="glyphicon glyphicon-search" ></span>Search</button>
                        </div>
                        </div>->
                    <div class="row show-grid">
                        <--Table->
                        <form>
                            <table class="table table-bordered" id="wbsscheduleitemtable" style="display: table;">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Schedule Item</th>
                                    <th>Schedule Activity</th>
                                    <th>Schedule Item</th>
                                    <th>Schedule Activity</th>
                                    <th>Relation</th>
                                    <th> </th>
                                    <th> </th>
                                </tr>
                                <tr class="preloader" style="display: none;"><td colspan="9" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                                </thead>
                                <tbody id="activity_relation_content">

                                </tbody>
                            </table>
                        </form>
                    </div>

                     <div class="row show-grid">
                        <--Table->
                        <form>
                            <table class="table table-bordered" id="wbsscheduleitemlidttable" style="display: table;">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Schedule Item</th>
                                    <th>Schedule Activity</th>
                                    <th>Schedule Item</th>
                                    <th>Schedule Activity</th>
                                    <th>Relation</th>
                                    <th>Lag</th>
                                    <th>Edit</th>
                                    <th>Delete</th>
                                </tr>
                                <tr class="preloader" style="display: none;"><td colspan="9" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                                </thead>
                                <tbody id="activity_relation_list">

                                </tbody>
                                <tbody id="structure_relation_list" style="display: none;">

                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>-->

            </div>
        </div>
    </div>

</div>
<script type="text/javascript">
    $(document).on("click",'.viewmethedology',function(){

        var id=$(this).val();

        var name=$(this).attr('data-name');
        $('#itemnamemethedology').html(name);
        $('#currentiowmethedology').val(id);
        $.ajax({
            type: 'POST',
            url: '../workgroups1/Methedology',
            beforeSend : function(){

                $('.methedologypreloader').show();
            },
            dataType: "json",
            data: {id:id},
            success: function(data){
                if(data.error=='No')
                {
                    $('#methedologyshow').html(data.result);
                    var editor=CKEDITOR.replace( 'methedology' );
                    editor.config.height = 500;
                    $('#methedologyprojectname').html(data.project);
                    $('.methedologypreloader').hide();
                }

            }
        });
    });
    $(document).on("click",'#savemethedology',function(){
        var methedology=CKEDITOR.instances.methedology.getData();
        var iow=$('#currentiowmethedology').val();

        $.ajax({
            type: 'POST',
            url: '../workgroups1/SaveMethedology',

            dataType: "json",
            data: {methedology:methedology,iow:iow},

        });
    });

    $(document).on("click",'.viewiownotes',function(){

        var id=$(this).val();

        var name=$(this).attr('data-name');
        $('#notesitems').html(name);
        $('#currentiow').val(id);
        $.ajax({
            type: 'POST',
            url: '../workgroups1/Notes',
            beforeSend : function(){

                $('.preloader').show();
            },
            dataType: "json",
            data: {id:id},
            success: function(data){
                if(data.error=='No')
                {
                    $('#notesiowshow').html(data.result);
                    $('#notesiowname').html(data.iow);
                }
                else
                {
                    alert(data.errortext);
                }


                $('.preloader').hide();
            }
        });
    });

    $(document).on("click",'#saveiownotes',function(){

        var notes=$('#notes').val();
        var currentiow=$('#currentiow').val();

        $.ajax({
            type: 'POST',
            url: '../workgroups1/SaveNotes',

            dataType: "json",
            data: {notes:notes,currentiow:currentiow}

        });
    });
</script>
