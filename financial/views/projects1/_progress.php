<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/projects/progress.js" type="text/javascript"></script>

<h2 class="acc_trigger" id="progress"><a href="javascript:void(0)">9. Activity Progress Report</a></h2>
<div class="acc_container">
    <div class="block" style="padding-bottom:50px;">
        <div class="jumbotron">
            
            <!--<div id="progresssearchdiv" class="row show-grid">
                <div class="col-md-3" id="progresswbs">

                </div>
                <div class="col-md-3">
                    <select id="progressiow" class="form-control">
                        <option value="none" data-id="none">Select Activity</option>
                    </select><span class='error' style='float: left;'></span>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger reportiow" id="reportiow" title="Report">Report</button>
                </div>
            </div>-->
            
           <!-- <div id="progressactivitylist">
                <div class="row show-grid">
                    <table class="table table-bordered" id="progressactivitytable" style="display: table; overflow: hidden;">
                        <thead>
                        <tr>
                            <th>Process</th>
                            <th>Activity</th>
                            <th>Owner</th>
                            <th>Updated By</th>
                            <th>Progress</th>
                            <th>Status</th>
                            <th>Activities</th>
                            <th>Unit</th>
                            <th>E.Qty</th>
                            <th>E.Duration</th>
                            <th>Comp.Qty</th>
                            <th>A.Start Date</th>
                            <th>A.End Date</th>
                            <th>Status</th>
                            <th>Last Updated</th>
                            <th colspan="2"></th>
                        </tr>
                        <tr class="preloader" style="display: none;"><td colspan="8" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                        </thead>
                        <tbody id="progressactivityitems">

                        </tbody>
                    </table>
                </div>
            </div>-->
            
                    <div id="activityreportactivity">

                            <div class="col-md-12">
                                <h4>Activity Progress Report</h4>
                            </div>

                           <div class="col-md-12" id="projectname" style="text-align:center;"></div>

                             <div class="row show-grid">
                                <!--<div class="col-md-3 all-activities"><input type="checkbox" style="visibility: visible;" id="all-activities" name="all-activities" value="mode"> All Activities</div> -->
                                <div id="progressactivityitems" style="display:none;">

                                </div>
                                <div class="col-md-4 scheduleactivitylist" style="float:right;">
                                    <select name="scheduleactivitylist" id="scheduleactivitylist" class="form-control">

                                    </select>
                                </div>
                                <div class="col-md-4 scheduleitemslist" style="float:right;">
                                    <select name="scheduleitemslist" id="scheduleitemslist" class="form-control">

                                    </select>
                                </div>
                                <div class="col-md-4 structurenameslist" style="float:right;">
                                    <select name="structurenameslist" id="structurenameslist" class="form-control">

                                    </select>
                                </div>
                            </div>
                
                   <table class="table table-bordered" id="activityfields">
                    <thead id="default-activityfields">
                    <tr>
                      <th style="width:20%;"><span class="headings" style="margin-bottom:40px;">Start Date</span></th> 
                      <th style="width:10%;"><span class="headings" style="margin-bottom:40px;">End Date</span></th> 
                      <th style="width: 15%"><span class="headings" id="unit" style="margin-bottom:40px;">Unit</span></th>
                      <th style="width: 15%"><span class="headings" id="cumquantity" style="margin-bottom:40px;">Cumulated Quantity</span></th> 
                      <th style="width: 10%"><span class="headings" style="margin-bottom:40px;">Today's Quantity</span></th> 
                      <th style="width: 20%"><span class="headings" style="margin-bottom:40px;">Status</span></th> 
                      <th style="width: 10%"><span class="headings" id="last-updated" style="margin-bottom:40px;">Last Updated</span></th> 
                    </tr>
                    </thead>
                   </table>
                
                   <div class="col-md-3" style="padding-left:50px;">
                        <span class="headings" id="activityname" style="padding-top: 8px;padding-right: 5px;">Activity :</span><h5 id="ActivityName" style="text-align:left;font-size:12px;"></h5>
                    </div>
                    <div class="col-md-3" style="padding-left:50px;">
                        <span class="headings" id="activityunit" style="padding-top: 8px;padding-right: 5px;">Unit :</span><h5 id="ActivityUnit" style="text-align:left;font-size:12px;"></h5>
                    </div>
                    <div class="col-md-2" style="padding-left:50px;">
                        <span class="headings" id="estimatequantity" style="padding-top: 8px;padding-right: 5px;">E.Quantity :</span><h5 id="EstimateQuantity" style="text-align:left;font-size:12px;"></h5>
                    </div>
                    <div class="col-md-2" style="padding-left:50px;">
                        <span class="headings" id="estimateduration" style="padding-top: 8px;padding-right: 5px;">E.Duration :</span><h5 id="EstimateDuration" style="text-align:left;font-size:12px;"></h5>
                    </div>
                    <div class="col-md-2" style="padding-left:50px;">
                        <span class="headings" id="currentdate" style="padding-top: 8px;padding-right: 5px;">Date :</span><h5 id="CurrentDate" style="font-size:12px;text-align:left;"><?php echo date('d-m-Y') ?></h5>
                    </div>
                
                   <form id="resourceform-activity">
                   <table class="table table-bordered" id="activityreporttable" style="display: table;">
                        <thead>
                        <tr>
                            <th class="small75">Sl.No</th>
                            <th>Resource Type</th>
                            <th>Resource</th>
                            <th>Unit</th>
                            <th>E.Quantity</th>
                            <th>Cumulated Quantity</th>
                            <!--<th>Consumption per cycle</th>-->
                            <th>Today's consumption</th>
                        </tr>
                        </thead>
                        <tr class="preloader" style="display: none;"><td colspan="7" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle" /> </td></tr>
                        <tbody id="activityreportitems">

                        </tbody>
                    </table> 
                    </form>
                   <div>
                     <div class="col-md-3" style="width: 10%;float:right;"><button type="button" class="btn btn-danger savenewprogressrpt_new" id="savenewprogressrpt_new" data-id="" value="" title="Save">Report</button></div>
                     <div class="col-md-3" style="width: 10%;float:right;"><button type="button" class="btn btn-danger savedraftprogressrpt_new" id="savedraftprogressrpt_new" data-id="" mode="save-draft" value="" title="Save as draft">Save As Draft</button></div>
                     <div class="col-md-3" style="width: 10%;float:right;"><button type="button" class="btn btn-danger cancel_savenewprogressrpt" id="cancel_savenewprogressrpt" data-id="" value="" title="Cancel">Cancel</button></div>
                   </div>
                
            </div>
            
            <div class="row show-grid" id="progreportdiv">
                <div class="col-md-4">
                    <span class="headings" id="progactivity"><h5 id="progActivity"></h5></span>
                </div>
                <!--<div class="col-md-4">
                    <span class="headings" id="progactunit">Unit : <h5 id="progActUnit"></h5></span>
                </div>-->
                <div id="iowactivities">

                </div>

            </div>
        </div>
    </div>
</div>