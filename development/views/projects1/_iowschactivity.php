<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/iowschactivity.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="iowschactivity"><a href="#">5. IOW Activities</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-2" id="iowschprojectnamedisp">

                </div>
                <div class="col-md-2" id="iowschworkgroupnamedisp">
                </div>
                <input type="hidden" id="iowschwbsid">
                <div class="col-md-2"><button type="button" class="btn btn-success" id="addiowsch"><span class="glyphicon glyphicon-plus-sign"></span>Add IOW Activity</button></div>
                <div class="col-md-2"><button type="button" class="btn btn-danger" id="listiowsch"><span class="glyphicon glyphicon-list-alt"></span>List IOW Activity</button></div>
            </div>
            <div id="iowschlistsection">
                <div class="col-md-2 pull-right"><button type="button" id="saveiowschactivitybutton1" class="btn btn-primary saveiowschactivitybutton">Save Activities</button></div>
                <div class="col-md-6 alert alert-success iowschsuccmsg" style="display:none; float: none; margin: 0 auto;"></div>
                <div class="row show-grid">
                    <form id="iowschactivitysaveform">
                        <!--<input type="hidden" id="ProId" name="ProId">
                        <input type="hidden" id="IOWorkgroupId" name="IOWorkgroupId">-->
                        <table class="table table-bordered " id="iowschtable" style="display: table;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Process</th>
                                <th>Activity Name</th>
                                <th>Unit</th>
                                <th>Duration</th>
                                <th>Amount</th>
                                <th colspan="3"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="10" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="iowschitems">

                            </tbody>
                        </table>
                        <div class="col-md-6 alert alert-success iowschsuccmsg" style="display:none; float: none; margin: 0 auto;"></div>
                        <div class="col-md-2 pull-right"><button type="button" id="saveiowschactivitybutton" class="btn btn-primary pull-right saveiowschactivitybutton">Save Activities</button></div>
                    </form>
                </div>
            </div>
            <div id="iowschaddsection" class="row show-grid">
                <div class="row show-grid">
                    <form id="iowschactivityaddform">
                        <input type="hidden" id="ProIdiowsch" name="ProId">
                        <table class="table table-bordered " id="iowschtables" style="display: table;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Process</th>
                                <th>Activity Name</th>
                                <th>Unit</th>
                                <th>Duration</th>
                                <th>Amount</th>
                                <th colspan="2"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="8" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody id="deletediowschactivity">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>