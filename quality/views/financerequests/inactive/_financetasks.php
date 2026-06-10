<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/financetask.js" type="text/javascript"></script>
<!--<script type="text/javascript">
    $(function(){
        var type = 'task';
        //alert(type)
        setTimeout(function() {
            $('#'+type).trigger('click');
        },1000);
        //$('#request').addClass('active').next('.acc_container').slideUp();
    });
</script>-->
<script>
    $(document).on('mouseenter','.hover',function(){
        var tooltip=$(this).attr('data-tooltip');
        $('.tooltiptable').hide();
        $('#'+tooltip).fadeIn('fast');
    });
    $(document).on('mouseleave','.hover',function(){
        var tooltip=$(this).attr('data-tooltip');
        $('#'+tooltip).fadeOut('slow');
    });
</script>
<h2 class="acc_trigger" id="task"><a href="javascript:void(0)">1. Action Plans</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-3"><a href="<?php echo Yii::app()->request->baseUrl; ?>/Task/Create?mode=finance"><button type="button" class="btn btn-danger"  id="addtask"><span class="glyphicon glyphicon-plus-sign"></span>Create Action Plans</button></a></div>
                <div class="col-md-3"><button type="button" class="btn btn-success" id="listtasks"><span class="glyphicon glyphicon-list-alt"></span>List Action Plans</button></div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="completedtask"><span class="glyphicon glyphicon-list-alt"></span>Completed Action Plans</button></div>
            </div>
            <div id="tasklistsection">
                <div id="searchdiv" class="row show-grid" style="display: block;">
                    <div class="col-md-6">
                        <input type="text" placeholder="Search..." id="searchtask" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button id="tasksearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="tasktable" style="display: table; overflow: hidden;">
                            <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>Created Date</th>
                                <th>Action Plan</th>
                                <th>Created By</th>
                                <th>Assigned To</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th colspan="2"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="9" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody class="ui-sortable" id="taskitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            <div id="completedtasklist" style="display: none">
                <div id="searchdiv" class="row show-grid" style="display: block;">
                    <div class="col-md-6">
                        <input type="text" placeholder="Search..." id="tasktext" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button id="completedtasksearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="completedtasktable" style="display: table; overflow: hidden;">
                            <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>Created Date</th>
                                <th>Action Plan</th>
                                <th>Created By</th>
                                <th>Assigned To</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th colspan="2"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="9" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="completedtaskitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<style type="text/css">
    .user{
        overflow: hidden !important;
        text-overflow: ellipsis;
        display: inline-block;
        width: 100px;
        white-space: nowrap;
    }
</style>