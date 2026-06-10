<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/_task.js" type="text/javascript"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap.min.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/ckeditor/ckeditor.js"></script>
<style type="text/css">
    textarea{


        height:500px;
        min-height:500px;
        max-height:500px;

    }
</style>

<h2 class="acc_trigger" id="protask"><a href="#">4. Tasks</a></h2>
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
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
            <div class="row show-grid">

                <div class="col-md-2" id="projectnamedisplay">

                </div>
                <input type="hidden" id="iowProjectId">
                <div class="col-md-2" id="workgroupnamedisplay">

                </div>
                <input type="hidden" id="IOWWorkgroupId">
                <div class="col-md-2"><button type="button" class="btn btn-success" id="addtask"><span class="glyphicon glyphicon-plus-sign"></span>Add Task</button></div>
                <div class="col-md-2"><button type="button" class="btn btn-danger" id="listtasks"><span class="glyphicon glyphicon-list-alt"></span>List Task</button></div>

            </div>
            <div id="taskslistsection" >

                <div class="row show-grid">
                    <form id="protasksaveform">
                        <input type="hidden" id="IOWorkgroupssId" name="IOWorkgroupId">
                        <input type="hidden" id="activityId" name="activityId">
                        <input type="hidden" id="processId" name="processId">
                        <table class="table table-bordered " id="iowtable" style="display: table;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Tasks</th>
                                <th >Delete</th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="12" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="taskitems">

                            </tbody>
                        </table>
                        <div class="col-md-10"></div>
                        <div class="col-md-2"><button type="button" id="saveprotaskbutton" class="btn btn-primary pull-right">Save Tasks</button></div>
                    </form>
                </div>
            </div>
            <div id="tasksaddsection" class="row show-grid">
                 <div class="row show-grid">
                    <form id="protaskaddform">
                        <!--<input type="hidden" id="IOWorkgroupsId" name="IOWorkgroupId">
                        <input type="hidden" id="activityId" name="activityId">
                        <input type="hidden" id="processId" name="processId">-->
                        <table class="table table-bordered " id="taskaddtable" style="display: table;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Tasks</th>
                                <th >Add</th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="12" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="deletedtaskitems">

                            </tbody>
                        </table>
                        <div class="col-md-10"></div>
                        <!--<div class="col-md-2"><button type="button" id="saveprotaskbutton" class="btn btn-primary pull-right">Save Activities</button></div>-->
                    </form>
                </div>
            </div>
        </div>


    </div>

</div>
