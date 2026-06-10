<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/projects/activity.js" type="text/javascript"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap.min.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/ckeditor/ckeditor.js"></script>
<style type="text/css">
    textarea{
        height:500px;
        min-height:500px;
        max-height:500px;
    }
</style>
<h2 class="acc_trigger" id="activity"><a href="#">5. Project Activities</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="modal fade " id="solutionModel" >
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title"><span id="modalprojectname"></span></h4>
                        </div>
                        <div class="panel-heading">
                            <div id="itemname">


                            </div>
                        </div>
                        <div class="modal-body" >
                            <div class="preloader" style="display: none;"><span align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </span></div>
                            <div id="methedologyshow"></div>
                            <input type="hidden" id="currentiow" value="0">
                        </div>
                        <div class="modal-footer">
                            <div class="col-md-6"></div>
                            <div class="col-md-3"><button type="button" class="btn btn-default" id="savemethedology" data-dismiss="modal">Save</button></div>
                            <div class="col-md-3"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div>


                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
            <div class="modal fade " id="specsheetModel" >
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title"><span id="modalprojectnamespecsheet"></span></h4>
                        </div>
                        <div class="panel-heading">
                            <div id="itemnamespecshhet">


                            </div>
                        </div>
                        <div class="modal-body" >
                            <div class="preloader" style="display: none;"><span align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </span></div>
                            <div id="specsheetshow"></div>
                            <input type="hidden" id="currentiowspecsheet" value="0">
                        </div>
                        <div class="modal-footer">
                            <div class="col-md-6"></div>
                            <div class="col-md-3"><button type="button" class="btn btn-default" id="savespecsheet" data-dismiss="modal">Save</button></div>
                            <div class="col-md-3"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div>


                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
            <div class="modal fade " id="checklistModel" >
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title"><span id="checklistmodalprojectname"></span></h4>
                        </div>
                        <div class="panel-heading">
                            <div id="itemnamechecklist">


                            </div>
                        </div>
                        <div class="modal-body" >
                            <div class="preloader" style="display: none;"><span align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </span></div>
                            <div id="checklistshow"></div>
                            <input type="hidden" id="currentiowchecklist" value="0">
                        </div>
                        <div class="modal-footer">
                            <div class="col-md-6"></div>
                            <div class="col-md-3"><button type="button" class="btn btn-default" id="savechecklist" data-dismiss="modal">Save</button></div>
                            <div class="col-md-3"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div>


                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->

            <div class="modal fade " id="Boqmap" >
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title"><span>Search BOQ Items</span></h4>
                        </div>
                        <div class="modal-body" >
                            <div class="col-md-4" style="float:right;margin-top: -8px;">
                                <button type="button" class="btn btn-danger" id="boqactsearch" value=""><span class="glyphicon glyphicon-search"></span>Search</button>
                            </div>
                            <div class="col-md-8" style="float:right;margin-top: -10px;">
                                <input class="form-control" id="searchboqitem" type="text" placeholder="Search BOQ slno or item">
                            </div>
                            <div class="col-md-12" id="boqsearchlisting">
                                
                            </div>
                        </div>
                        <div class="modal-footer">

                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- /.boq map modal -->

            <div class="row show-grid">
                <div class="col-md-2" id="projectnamedisplay">

                </div>
                <!--<input type="hidden" id="iowProjectId">-->
                <input type="hidden" id="selectedWorktypeId">
                <div class="col-md-2" id="workgroupnamedisplay">
                </div>
                <input type="hidden" id="IOWWorkgroupId2">
                <div class="col-md-2"><button type="button" class="btn btn-success" id="addiow"><span class="glyphicon glyphicon-plus-sign"></span>Add Activity</button></div>
                <div class="col-md-2"><button type="button" class="btn btn-danger" id="listiow"><span class="glyphicon glyphicon-list-alt"></span>List Activity</button></div>

            </div>
            <div id="iowlistsection" >

                <div class="row show-grid">
                    <form id="iowactivitysaveform">
                        <!-- <div class="col-md-2 pull-right"><button type="button" id="saveiowactivitybutton1" class="btn btn-primary saveiowactivitybutton">Save Activities</button></div> -->
                        <div class="col-md-6 alert alert-success succmsg" style="display:none; float: none; margin: 0 auto;"></div>
                    <!--<input type="hidden" id="ProId" name="ProId">
                    <input type="hidden" id="IOWorkgroupId" name="IOWorkgroupId">-->
                        <table class="table table-bordered " id="iowtable" style="display: table;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <!-- <th>Process</th> -->
                                <th>Activity Name</th>
                                <th>BOQ No</th>
                                <th>Unit</th>
                                <!-- <th></th> -->
                                <!-- <th>Duration</th> -->
                                <th>Amount</th>
                                <th colspan="4"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="10" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="iowitems">

                            </tbody>
                        </table>
                        <div class="col-md-6 alert alert-success succmsg" style="display:none; float: none; margin: 0 auto;"></div>
                        <!-- <div class="col-md-2 pull-right"><button type="button" id="saveiowactivitybutton" class="btn btn-primary pull-right saveiowactivitybutton">Save Activities</button></div> -->
                    </form>
                </div>
            </div>
            <div id="iowaddsection" class="row show-grid">
                <div class="row show-grid">
                    <div class="col-md-2" style="float:right;">
                        <button type="button" class="btn btn-danger" id="enggactsearch"><span class="glyphicon glyphicon-search" ></span>Search</button>
                    </div>
                    <div class="col-md-4" style="float:right;">
                        <input class="form-control" id="searchenggactname" type="text" placeholder="Search">
                    </div>
                    <div class="col-md-3" style="float:left;">
                        <select name="wbsworktypelist" class="form-control" id="wbsworktypelist">
                            <option value="0">Select Work Type</option>
                            <?php
                            $typelist=EstimateWorkType::model()->findAll(array('order'=>'estworktype_name ASC'));
                            foreach($typelist AS $list):
                                echo "<option value='".$list->estworktype_id."'>".$list->estworktype_name."</option>";
                            endforeach;
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3" style="float:left;">
                        <select name="wbsactivitytypelist" class="form-control" id="wbsactivitytypelist">
                            <option value="0">Select Activity Type</option>
                            <?php
                            $typelist=EstimateActivityType::model()->findAll(array('order'=>'activitytype_name ASC'));
                            foreach($typelist AS $list):
                                echo "<option value='".$list->activitytype_id."'>".$list->activitytype_name."</option>";
                            endforeach;
                            ?>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <form id="iowactivityaddform">
                            <input type="hidden" id="ProId" name="ProId">
                            <input type="hidden" id="IOWorkgroupsId" name="IOWorkgroupId">
                            <table class="table table-bordered " id="iowtables" style="display: none;">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Activity Type</th>
                                    <th>Work Type</th>
                                    <th>Activity Name</th>
                                    <th>Unit</th>
                                    <th>Amount</th>
                                    <th colspan="1"></th>
                                </tr>
                                <tr class="preloader" style="display: none;"><td colspan="9" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                                </thead>
                                <tbody id="deletedactivityitems">

                                </tbody>
                            </table>                        
                        </form>
                    </div>
                </div>
            </div>
        </div>


    </div>

</div>
<script type="text/javascript">
 $(document).on("click",'.methedologies',function(){

        var id=$(this).val();

        var name=$(this).attr('data-name');
        $('#itemname').html(name);
        $('#currentiow').val(id);
        $.ajax({
            type: 'POST',
            url: '../activity/Methedology',
            beforeSend : function(){

                $('.preloader').show();
            },
            dataType: "json",
            data: {id:id},
            success: function(data){
                if(data.error=='No')
                {
                    //$('#methedologyshow').html(data.result);
                    //$('#modalprojectname').html(data.project);

                    $('#methedologyshow').html(data.result);
                    var editor=CKEDITOR.replace( 'editor2' );
                    editor.config.height = 500;
                    $('#modalprojectname').html(data.project);
                }
                else
                {
                    alert(data.errortext);
                }


                $('.preloader').hide();
            }
        });
    });
       $(document).on("click",'#savemethedology',function(){

            //var methedology=$('#methedology').val();
            var methedology=CKEDITOR.instances.editor2.getData();
            var estimateid=$('#currentiow').val();

            $.ajax({
                type: 'POST',
                url: '../activity/SaveMethedology',

                dataType: "json",
                data: {methedology:methedology,estimateid:estimateid,},
                /*success: function(data){
                    if(data.error=='No')
                    {
                        $('#methedologyshow').html(data.result);
                        $('#modalprojectname').html(data.project);
                    }
                    else
                    {
                        alert(data.errortext);
                    }


                    $('.preloader').hide();
                }*/
            });
        });
        $(document).on("click",'.specsheets',function(){

            var id=$(this).val();

            var name=$(this).attr('data-name');
            $('#itemnamespecshhet').html(name);
            $('#currentiowspecsheet').val(id);
            $.ajax({
                type: 'POST',
                url: '../activity/Specsheet',
                beforeSend : function(){

                    $('.preloader').show();
                },
                dataType: "json",
                data: {id:id},
                success: function(data){
                    if(data.error=='No')
                    {
                        $('#specsheetshow').html(data.result);
                        $('#modalprojectnamespecsheet').html(data.project);
                    }
                    else
                    {
                        alert(data.errortext);
                    }


                    $('.preloader').hide();
                }
            });
        });
           $(document).on("click",'#savespecsheet',function(){

            var methedology=$('#specsheettext').val();
            var estimateid=$('#currentiowspecsheet').val();

            $.ajax({
                type: 'POST',
                url: '../activity/SaveSpecsheet',

                dataType: "json",
                data: {methedology:methedology,estimateid:estimateid,},
                /*success: function(data){
                 if(data.error=='No')
                 {
                 $('#methedologyshow').html(data.result);
                 $('#modalprojectname').html(data.project);
                 }
                 else
                 {
                 alert(data.errortext);
                 }


                 $('.preloader').hide();
                 }*/
            });
        });
         $(document).on("click",'.checklist',function(){

        var id=$(this).val();

        var name=$(this).attr('data-name');
        $('#itemnamechecklist').html(name);
        $('#checklistshow').val(id);
        $.ajax({
            type: 'POST',
            url: '../activity/Checklist',
            beforeSend : function(){

                $('.preloader').show();
            },
            dataType: "json",
            data: {id:id},
            success: function(data){
                if(data.error=='No')
                {
                    $('#checklistshow').html(data.result);
                    var editor=CKEDITOR.replace( 'editor1' );
                    editor.config.height = 500;
                    $('#checklistmodalprojectname').html(data.project);
                }
                else
                {
                    alert(data.errortext);
                }


                $('.preloader').hide();
            }
        });
    });
    $(document).on("click",'#savechecklist',function(){

        var methedology=CKEDITOR.instances.editor1.getData();
        var estimate=$('#checklistshow').val();

        $.ajax({
            type: 'POST',
            url: '../activity/SaveChecklist',

            dataType: "json",
            data: {methedology:methedology,estimateid:estimate,},

        });
    });

</script>