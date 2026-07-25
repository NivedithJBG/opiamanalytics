<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/operations/progressreport.js" type="text/javascript"></script>

<style type="text/css">
    .form-control{
        padding: 6px 8px !important;
    }
</style>

<div class="search-and-content-wrpr" id="pgrsrpt" style="padding:20px;">
    <form id="schedule-task">
        <div class="search-and-actions-wrpr row">
            <div class="content-search-wrpr col-md-6 col-sm-6 text-left ">
                <input type="hidden" name="select_report_date" id="select_report_date" value="<?php echo date("d-m-Y");?>" />
            </div>
            <div class="col-md-2 col-sm-2"></div>
            <div class="content-action-wrpr col-md-4 col-sm-4">
                <button type="button" class="btn btn-primary activity_history_btn" id="activity_history_btn">History</button>
                <button type="button" class="btn btn-primary activity_back_btn" id="activity_back_btn" style="display:none;">Back</button>
                <input type="hidden" id="activity_pr_main"/>
            </div>
        </div>
        <div class="content-wrpr">
            <div class="asset-register-list-wrpr data-content-list">
                <div class="preloader" style="display: none;" align="center">
                    <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                </div>
                <div id="schedule_report_activityitems"></div>
                <div id="activitycompletehist"></div>
                <div id="success-messages"></div>
            </div>
        </div>
    </form>
</div>

<!---  CAUSE OF DELAY POPUP ---->
<div class="modal fade causeOfDelayPopup" id="causeOfDelayPopup" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title"  style="float: left;">Manage Cause Of Delays</h4>
                <button type="button" class="close causeOfDelayPopup" data-dismiss="modal" style="float:right; font-size: 30px;">×</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">

                    <div class="row">

                            <div class="col-md-12">
                                <div class="preloader" id="Promain-preloader-Listwbs" style="display: none;" align="center">
                                    <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                                </div>

                                <div class="row">
                                    <form id="estworktypeform">
                                        <div class="col-md-3"></div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Cause of Delay</label>
                                                <input type="text" class="form-control" id="causeofdelay" placeholder="Cause of Delay">
                                                <span class="error" style="display: none;"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-left" style="padding-top: 5px;">
                                            <label style="width: 100%;"></label>
                                            <button type="button" class="btn btn-primary save-btn" id="saveCauseofDelay"><span class="icon-check"></span> Add</button>
                                        </div>
                                    </form>
                                </div>

                                <hr>

                                <div id="causeOfDelayListContainer" class="row ">

                                </div>

                            </div>

                    </div>

            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
            </div>

        </div>
    </div>
</div>
<!-------------->

<script>
$(document).on('focus','.datepicker',function(){
    $(this).datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        maxDate: new Date()
    });
});

$('#activity_pr_main').trigger('click');
</script>
