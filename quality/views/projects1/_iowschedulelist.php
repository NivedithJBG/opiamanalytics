<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/iowschedulefunctions.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="iowscheduleitems"><a href="#">6. IOW Schedule</a></h2>
<div class="acc_container">
    <input type="hidden" id="iowwbsid">
    <input type="hidden" id="iowprojeid">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-2" id="iowshowprojectname"></div>
                <div class="col-md-3" id="iowshowworkgroupname"></div>
            </div>
            <div id="iowschedulelistiow" class="row show-grid">
                <form method="POST" action="" id="iowproductform">
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
                            <tbody id="iowschedulelist">
                            <tr><td colspan="4" style="text-align: center">This has been Scheduled</td></tr>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="row show-grid" id="iowscheduleactivities">

            </div>
        </div>
    </div>
</div>