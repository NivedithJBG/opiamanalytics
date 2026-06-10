<div class="panel panel-default project-tab acco-one tab tab-wrapper">
    <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projectsmain/rProjectestimate.js" type="text/javascript"></script>
    <!--<input type="radio" id="rd5" class="R-ProjectEstimate" name="rd">-->
    <div class="panel-heading" >
        <h4 class="panel-title R-ProjectEstimate">
            <a data-toggle="collapse" data-parent="#accordionproreports" href="#collapseproreport">
            <span class="icon-note1"></span>Project Report</a>
        </h4>
    </div>
    <div id="collapseproreport" class="tab-content cOrder-body panel-collapse collapse">
        <div class="panel-body">
            <div class="search-and-content-wrpr">
                    <div class="row added-alloc-items-heading">
                        <div class="col-md-12 ">
                            <div class="row  alloc-activity-title">
                                <div class="col-md-5 type"><br>
                                    &nbsp;&nbsp;<span id="projectname"></span>
                                    &nbsp;&nbsp;<span class="projectnameDetails" id="projectnameDetails" style="display: none;"></span>
                                </div>
                                <div class="col-md-5 type"><br>
                                    &nbsp;&nbsp;<span class="projectnameDetails" id="projectnameDetails2" style="display: none;"></span>
                                </div>
                                <div class="col-md-2 text-right"><br>
                                    <a href="#" id="listestimateitems"></a>
                                    <a href="#" id="listEstimateR"></a>
                                    <button class="btn btn-primary drilldown2" type="button" style="display: none;"><span> Back</span></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="content-wrpr">
                        <div class="preloader" style="display: none;" align="center">
                            <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                        </div>
                        <div class="resources-cntnt-wrpr row" id="estimatereports">
                            <table class="table table-bordered" id="procresourcetable" style="display: table; overflow: hidden; background-color: #ffffff ;">
                                <thead>
                                <tr>
                                    <th colspan="2">#</th>
                                    <th>Activity Type</th>
                                    <th>Activity</th>
                                    <th style="text-align: center">Unit</th>
                                    <th style="text-align: center">Quantity</th>
                                    <th style="text-align: center">Rate</th>
                                    <th style="text-align: center">Amount</th>                           
                                    <th colspan="2" style="width:100px;" ></th>
                                </tr>
                                </thead>
                                <tbody id="estimateitems1">
                                </tbody> 
                            </table>     
                        </div>

                        <!-- list end here -->
                        <div class="resources-cntnt-wrpr row" id="estimatedetails" style="display: none;">
                            <!--<h5><b>Detailed Report</b></h5>-->
                            <table class="table table-bordered" id="procresourcetabledetails" style="display: table; overflow: hidden; background-color: #ffffff ;">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Resources Name</th>
                                    <th style="text-align: center">Unit</th>
                                    <th style="text-align: center">Quantity</th>
                                    <th style="text-align: center">Rate</th>
                                    <th style="text-align: center">Amount</th>
                                </tr>
                                </thead>
                                <tbody id="estimateitemsview"> 
                                </tbody> 
                            </table>  
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>