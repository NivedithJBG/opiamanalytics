<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/projects/schedulefunctions.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="scheduleitems"><a href="#">6. Project Schedule</a></h2>
<div class="acc_container">
    <input type="hidden" id="workgroupid">
    <input type="hidden" id="projeid">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-2" id="showprojectname"></div>
                <div class="col-md-3" id="showworkgroupname"></div>
            </div>
            <div id="schedulelistiow" class="row show-grid">
                <form method="POST" action="" id="productform">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Activity</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="5" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="schedulelist">
                            <tr><td colspan="4" style="text-align: center">This has been Scheduled</td></tr>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="row show-grid" id="scheduleactivities">

            </div>
        </div>
    </div>
</div>