<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/wbstask.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="Wbsestimatetasklist"><a href="#">6. Tasks</a></h2>
<div class="acc_container">
    <input type="hidden" id="workgroupid">
    <input type="hidden" id="projeid">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-2" id="showprojectname"></div>
                <!--<div class="col-md-3" id="showworkgroupname"></div>-->
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="addnewtask" style="width:50%;"><span class="glyphicon glyphicon-plus-sign"></span>Add Tasks</button></div>
            </div>
            <div class="alert alert-success" id="showschedule" style="display:none;"> This has been Scheduled </div>
            <table class="table table-bordered" style="overflow: hidden; display: none; background-color: rgb(226, 226, 226);" id="addnewtaskbox">
                   <tbody>
                    <tr>
                        <th>#</th>
                        <th>
                            <input type="text" class="form-control" id="tasknamenew" placeholder="Task Name" value="">
                            <span class="error"></span>
                            <input type="hidden" class="form-control" id="activityid" value="">
                        </th>
                        <th></th>
                        <th><button type="button" class="btn btn-danger" id="savetaskname"><span class="glyphicon glyphicon-saved"></span>Add</button></th>
                        <th><button type="button" class="btn btn-danger" id="canceltaskname">Cancel</button></th>
                    </tr>
                    </tbody>
            </table>
            <div class="row show-grid" id="wbsestimateactivities">

            </div>
        </div>
    </div>
</div>