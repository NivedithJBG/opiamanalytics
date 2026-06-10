<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/activityfunction.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="activities"><a href="#">4. Activities</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-2" id="activityprojoctname"></div>
                <div class="col-md-2" id="activityworkgroup"></div>
                <div class="col-md-3" id="activityiow"></div>
                <input id="activityprojoctid" value="" type="hidden">
                <input id="activityworkgroupid" value="" type="hidden">
                <input id="activityiowid" value="" type="hidden">
                <div class="col-md-2"><button type="button" class="btn btn-success" id="addactive"><span class="glyphicon glyphicon-plus-sign"></span>Add Activity</button></div>
                <div class="col-md-2"><button type="button" class="btn btn-danger" id="listactivities"><span class="glyphicon glyphicon-list-alt"></span>List Activities</button></div>

            </div>

            <div id="activitylistsection" style="display: none;">

                <div class="row show-grid">
                        <button type="button" class="btn btn-primary saveworkgroupbutton" value="10001" id="saveworkgroupbutton10001" title="Save"> <span class="glyphicon glyphicon-save"></span></button><table class="table table-bordered" id="workgrouptable" style="display: table;">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th >Activity</th>
                            <th>Edit</th>
                            <th >Delete</th>
                        </tr>
                        <tr class="preloader" style="display: none;"><td colspan="4" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                        </thead>
                        <tbody id="activityitems"><tr id="workgrouprow10001" class="workgrouprow10001"><td class="small75">10001</td>
                            <td><span id="workgroupname10001">Activity 2</span><input class="form-control editworkgroupname" type="text" id="editworkgroupname10001" value="superstructure"><span class="error"></span></td>
                            <td class="small75"><button type="button" class="btn btn-primary editworkgroupbutton" value="10000" id="editworkgroupbutton10000" title="Rename Work Group"> <span class="glyphicon glyphicon-pencil"></span></button><button type="button" class="btn btn-primary saveworkgroupbutton" value="10000" id="saveworkgroupbutton10000" title="Save"> <span class="glyphicon glyphicon-save"></span></button></td>


                            <td class="small75"><button type="button" class="btn btn-primary deleteworkgroupbutton" value="10001" id="deleteworkgroupbutton10001" title="Delete Work Group"> <span class="glyphicon glyphicon-trash"></span></button></td>
                        </tr></tbody>
                    </table>
                </div>
            </div>

            <div id="activityaddsection" class="row show-grid" style="display: block;">
                <form id="addactivityform">
                    <table class="table table-bordered" style="overflow: hidden; display: table; background-color: rgb(226, 226, 226);" id="resourcevalueadd">
                        <tbody><tr>
                            <th>#</th>
                            <th> <input class="form-control" type="text" name="activityname" id="activityname" placeholder="Activity Name"><span class="error" style="display: none;"></span></th>
                            <th><button type="button" class="btn btn-danger" id="saveactivity">Save</button></th>
                        </tr>
                        </tbody></table>
                </form>
            </div>

        </div>
    </div>


</div>