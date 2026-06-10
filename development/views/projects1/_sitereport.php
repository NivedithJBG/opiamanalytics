<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/sitereport.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#datepicker0').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true,maxDate: new Date()});
        $('#editdatepicker').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true,maxDate: new Date()});
    });
</script>
<h2 class="acc_trigger" id="sitereport"><a href="javascript:void(0)">8. Activity Report</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-6" style="text-align: left;" id="siteprojectname">
                </div>
            </div>
            <!--<div id="processlistsection">
                <div id="processsearchdiv" class="row show-grid">
                    <div class="col-md-3" id="processlist">


                    </div>
                    <div class="col-md-3">
                        <select id="activity" class="form-control">
                            <option value="none">Select Activity</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger report" title="Report">Report</button>
                    </div>
                </div>

            </div>-->
            <div id="activitylistsection">
                <div class="row show-grid">
                    <table class="table table-bordered" id="activitylisttable" style="display: table; overflow: hidden;">
                        <thead>
                        <tr>
                            <th>Process</th>
                            <th>Activity</th>
                            <th>Owner</th>
                            <th>Updated By</th>
                            <th>Progress</th>
                            <th>Status</th>
                            <th colspan="3"></th>
                        </tr>
                        <tr class="preloader" style="display: none;"><td colspan="9" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                        </thead>
                        <tbody id="activitylistitems">

                        </tbody>
                    </table>
                </div>
            </div>
            <div id="reportactivities">
                <form id="resourceform">
                    <div class="col-md-12">
                        <h4>Activity Report</h4>
                    </div>
                    <div class="col-md-2">
                        <span class="headings" id="process">Process : <h5 id="Process"></h5></span>
                    </div>
                    <div class="col-md-6">
                        <span class="headings" id="activity">Activity : <h5 id="Activity"></h5></span>
                    </div>

                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>
                                <span class="headings">Date</span>
                                <input type="text" class="form-control datepicker" name="Report_Date" id="datepicker0" value="<?php echo date("d-m-Y");?>">
                            </th>
                            <!--<th>
                                <span class="headings" id="activity">Activity<h5 id="Activity"></h5></span>
                            </th>-->
                            <!--<th>
                                <span class="headings" id="cyclenumber">Cycle Number<h5 id="Cyclenumber"></h5></span>
                                <select class="form-control" id="cyclenumoptions" name="Cyclenumber"></select>
                            </th>-->
                            <!--<th><span class="headings" id="cycleunit">Cycle Unit<h5 id="Cycleunit"></h5></span></th>
                            <th><span class="headings" id="compcycles">Completed Cycle<h5 id="Compcycles"></h5></span></th>-->
                            <th><span class="headings" id="unit">Activity Unit<h5 id="Unit"></h5></span></th>
                            <th><span class="headings" id="cumquantity">Cumulated Work Done<h5 id="Cumquantity"></h5></span></th>
                            <!--<th><span class="headings">Quantity per cycle</span><input type="text" class="form-control" id="uptodateqty" name="Activity_Qty" readonly="readonly"  value="0"><span class='error'></span></th>-->
                            <th style="width: 20%">
                                <span class="headings">Today's Work Done</span>
                                <input type="text" class="form-control" id="qtyproduced" name="Qtyproduced" value="">
                                <span class='error'></span>
                                <input type="hidden" id="activityid" name="activityid">
                                <input type="hidden" id="activityunit" name="activityunit">
                                <input type="hidden" id="resgroupid" name="resgroupid">
                                <input type="hidden" id="qtyupto" name="qtyupto">
                            </th>
                            <th>
                                <div class="col-md-8" id="draftdatediv" style="padding-bottom: 10px">

                                </div>
                            </th>
                        </tr>
                        </thead>
                    </table>
                    <table class="table table-bordered" id="reporttable" style="display: table;">
                        <thead>
                        <tr>
                            <th class="small75">Sl.No</th>
                            <th>Resource Type</th>
                            <th>Resource</th>
                            <th>Unit</th>
                            <th>Cumulated Quantity</th>
                            <!--<th>Consumption per cycle</th>-->
                            <th>Today's consumption</th>
                        </tr>
                        <tr class="preloader" style="display: none;"><td colspan="7" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                        <tbody id="reportitems">

                        </tbody>
                    </table>
                </form>
            </div>

            <div id="editreportactivities">
                <form id="editresourceform">
                    <div class="col-md-12">
                        <h4>Activity Report</h4>
                    </div>
                    <div class="col-md-2">
                        <span class="headings" id="editprocess">Process : <h5 id="editProcess"></h5></span>
                    </div>
                    <div class="col-md-6">
                        <span class="headings" id="editactivity">Activity : <h5 id="editActivity"></h5></span>
                    </div>
                    <div class="col-md-4" id="reportdatediv">

                    </div>
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>
                                <span class="headings">Date</span>
                                <input type="text" class="form-control datepicker" name="Report_Date" id="editdatepicker" value="<?php echo date("d-m-Y");?>">
                            </th>
                            <!--<th>
                                <span class="headings" id="activity">Activity<h5 id="Activity"></h5></span>
                            </th>-->
                            <!--<th>
                                <span class="headings" id="editcyclenumber">Cycle Number<h5 id="editCyclenumber"></h5></span>
                                <select class="form-control" id="editcyclenumoptions" name="Cyclenumber"></select>
                            </th>-->
                            <!--<th><span class="headings" id="editcycleunit">Cycle Unit<h5 id="editCycleunit"></h5></span></th>-->
                            <th><span class="headings" id="editunit">Activity Unit<h5 id="editUnit"></h5></span></th>
                            <th><span class="headings">Cumulated Work Done</span><input type="text" class="form-control" id="edituptodateqty" name="Activity_Qty" readonly="readonly"  value="0"><span class='error'></span></th>
                            <th>
                                <span class="headings">Today's Work Done</span>
                                <input type="text" class="form-control" id="editqtyproduced" name="Qtyproduced" value="">
                                <span class='error'></span>
                                <input type="hidden" id="editactivityid" name="activityid">
                                <input type="hidden" id="editactivityunit" name="activityunit">
                                <input type="hidden" id="editresgroupid" name="resgroupid">
                                <input type="hidden" id="editqtyupto" name="qtyupto">
                            </th>

                        </tr>
                        </thead>
                    </table>
                    <table class="table table-bordered" id="editreporttable" style="display: table;">
                        <thead>
                        <tr>
                            <th class="small75">Sl.No</th>
                            <th>Resource Type</th>
                            <th>Resource</th>
                            <th>Unit</th>
                            <th>Cumulated Quantity</th>
                            <th>Today's consumption</th>
                        </tr>
                        <tr class="preloader" style="display: none;"><td colspan="6" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                        <tbody id="editreportitems">

                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>