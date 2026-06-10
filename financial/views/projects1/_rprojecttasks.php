<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/opprojtasks.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="opprojtasks"><a href="javascript:void (0)">2. Tasks</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-3"><a href="<?php echo Yii::app()->request->baseUrl; ?>/Task/Create?mode=operations"><button type="button" class="btn btn-danger"  id="addtask"><span class="glyphicon glyphicon-plus-sign"></span>Create Task</button></a></div>
                <div class="col-md-3"><button type="button" class="btn btn-success" id="listoptasks"><span class="glyphicon glyphicon-list-alt"></span>List Tasks</button></div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="opcompletedtask"><span class="glyphicon glyphicon-list-alt"></span>Completed Tasks</button></div>
            </div>
            <div id="optasklistsection">
                <div id="opsearchdiv" class="row show-grid" style="display: block;">
                    <div class="col-md-6">
                        <input type="text" placeholder="Search..." id="searchoptask" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button id="optasksearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="optasktable" style="display: table; overflow: hidden;">
                            <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>Created Date</th>
                                <th>Task</th>
                                <th>Created By</th>
                                <th>Assigned To</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th colspan="2"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="8" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="optaskitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            <div id="opcompletedtasklist" style="display: none">
                <div id="searchdiv" class="row show-grid" style="display: block;">
                    <div class="col-md-6">
                        <input type="text" placeholder="Search..." id="optasktext" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button id="opcompletedtasksearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="opcompletedtasktable" style="display: table; overflow: hidden;">
                            <thead>
                            <tr>
                                <th>Created Date</th>
                                <th>Task</th>
                                <th>Created By</th>
                                <th>Assigned To</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th colspan="2"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="8" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="opcompletedtaskitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>