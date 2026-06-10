<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projectsmain/dashboard.js" type="text/javascript"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<div class="chart-popup-cntnr">

    <div class="chart-cntnt-wrpr">
        <a href="#" title="Close" class="icon-close chart-win-close"></a>

        <div class="row show-grid"> </div>

        <div id="project-dashboard">
            <input type="hidden" id="listdasboard">

            <div class="col-md-12 text-center space-between mainlist"> 

                <div class="col-md-4 space-between">
                    <div class="chart-headings2">
                        <b style="">Project- Activity Cost</b>
                    </div>
                    <div class="twocol-cntnr">

                        <div class="prj-allchart prjctactivity-cost" id="prjctactivity-cost">
                            
                        </div>

                    </div>

                </div>

                <div class="col-md-4 space-between">
                	
                	<div class="chart-headings2">
                        <b style="">Project- Resource Cost</b>
                    </div>

                    <div class="twocol-cntnr">

                        <!--<div class="prj-allchart prjctresource-cost" id="prjctresource-cost">-->

                        <div class="prj-allchart prjctresource-cost-main" id="prjctresource-cost-main">
                            
                        </div>

                    </div>

				</div>

                <div class="col-md-4 space-between">
                    <div class="chart-headings2">
                        <b style="">Project- Cost drivers</b>
                    </div>
                    <div class="twocol-cntnr">
                        <div class="prj-allchart prjctdrivers-cost" id="prjctdrivers-cost">
                        </div>
                    </div>
                </div>

                <div class="col-md-4 space-between">
                    <div class="chart-headings2">
                        <b style="">When Will I Complete The Project</b>
                    </div>
                    <div class="twocol-cntnr">
                        <div class="prj-allchart prjct-complete" id="prjct-complete">
                        </div>
                    </div>
                </div>

                <div class="col-md-4 space-between">
                    <div class="chart-headings2">
                        <b style="">Capacity Utilization</b>
                    </div>
                    <div class="twocol-cntnr">
                        <div class="prj-allchart capactyutz_chart" id="capactyutz_chart">
                        </div>
                    </div>
                </div>

                <div class="col-md-4 space-between">
                    <div class="chart-headings2">
                        <b style="">ROI Chart</b>
                    </div>
                    <div class="twocol-cntnr">
                        <div class="prj-allchart roi_chart" id="roi_chart">
                        </div>
                    </div>
                </div>

                <div id="errormessage"> </div>

            </div>

            <!-- Chart 1 drill down start -->

            <div class="drilldownchart1"  style="display:none;">
                <div class="col-md-12 text-center">         
                    <div id="activitywisechart">
                        <div class="col-md-1"> </div>
                        <div class="col-md-10">

                            <div class="chart-headings2">
                                <b style="">IOW Cost <span class="icon-arrow-left-thick mainlistback"></span></b>
                            </div>
                            <div class="prj-allchart iow-cost" id="iow-cost" style="height: 450px;">
                                
                            </div>

                        </div>
                        <div class="col-md-1"> </div>
                    </div>
                </div>

                <div class="col-md-12 text-center">         
                    <div class="col-md-1"> </div>
                    <div class="col-md-10 iowtable"> 

                        <table class="all-list-table">
                            <thead>
                                <tr>
                                    <th>IOW</th>
                                    <th>Amount (INR)</th>
                                </tr>
                            </thead>
                            <tbody id="iow-names-list">

                            </tbody>
                        </table>

                    </div>
                    <div class="col-md-1"> </div>
                </div>

            </div>

            <div class="drilldown2chart1"  style="display:none;">
                <div class="col-md-12 text-center">         
                    <div>
                        <div class="col-md-1"> </div>
                        <div class="col-md-10">

                            <div class="chart-headings2">
                                <b id="iowheadname"></b>
                            </div>
                            <div class="prj-allchart activitywise-cost" id="activitywise-cost" style="height: 450px;">
                                
                            </div>

                        </div>
                        <div class="col-md-1"> </div>
                    </div>
                </div>

                <div class="col-md-12 text-center">         
                    <div class="col-md-1"> </div>
                    <div class="col-md-10 iowtable"> 

                        <table class="all-list-table">
                            <thead>
                                <tr>
                                    <th>Activity</th>
                                    <th>Amount (INR)</th>
                                </tr>
                            </thead>
                            <tbody id="activity-names-list">

                            </tbody>
                        </table>

                    </div>
                    <div class="col-md-1"> </div>
                </div>

            </div>

            <!-- Chart 1 drill down end -->

            <!-- Chart 2 drill down start -->

            <div class="drilldownchart2"  style="display:none;">
                <div class="col-md-12 text-center">         
                    <div id="resourcetypewisechart">
                        <div class="col-md-1"> </div>
                        <div class="col-md-10">

                            <div class="chart-headings2">
                                <b id="resourcetypename">Resource Type Cost Chart<span class="icon-arrow-left-thick mainlistback"></span></b>
                            </div>
                            <div class="prj-allchart prjctresource-cost" id="prjctresource-cost" style="height: 450px;">
                                
                            </div>

                        </div>
                        <div class="col-md-1"> </div>
                    </div>
                </div>

                <div class="col-md-12 text-center">         
                    <div class="col-md-1"> </div>
                    <div class="col-md-10 iowtable"> 

                        <table class="all-list-table">
                            <thead>
                                <tr>
                                    <th>Resource Type</th>
                                    <th>Amount (INR)</th>
                                </tr>
                            </thead>
                            <tbody id="resourcetype-names-list">

                            </tbody>
                        </table>

                    </div>
                    <div class="col-md-1"> </div>
                </div>

            </div>

            <div class="drilldown2chart2"  style="display:none;">
                <div class="col-md-12 text-center">         
                    <div id="resourcetypewisechart">
                        <div class="col-md-1"> </div>
                        <div class="col-md-10">

                            <div class="chart-headings2">
                                <b id="restypename"></b>
                            </div>
                            <div class="prj-allchart resourcetype-cost" id="resourcetype-cost" style="height: 450px;">
                                
                            </div>

                        </div>
                        <div class="col-md-1"> </div>
                    </div>
                </div>

                <div class="col-md-12 text-center">         
                    <div class="col-md-1"> </div>
                    <div class="col-md-10 iowtable"> 

                        <table class="all-list-table">
                            <thead>
                                <tr>
                                    <th>Resource</th>
                                    <th>Rate</th>
                                    <th>Quantity</th>
                                    <th>Amount (INR)</th>
                                </tr>
                            </thead>
                            <tbody id="resource-names-list">

                            </tbody>
                        </table>

                    </div>
                    <div class="col-md-1"> </div>
                </div>

            </div>

            <!-- Chart 3 drill down start -->

            <div class="drilldownchart3"  style="display:none;">
                <div class="col-md-12 text-center">         
                    <div id="resourcetypecostdriverschart">
                        <div class="col-md-1"> </div>
                        <div class="col-md-10">

                            <div class="chart-headings2">
                                <b id="restypenames"></b>
                            </div>
                            <div class="prj-allchart drivers-cost" id="drivers-cost" style="height: 450px;">
                                
                            </div>

                        </div>
                        <div class="col-md-1"> </div>
                    </div>
                </div>

                <div class="col-md-12 text-center">         
                    <div class="col-md-1"> </div>
                    <div class="col-md-10 iowtable"> 

                        <table class="all-list-table">
                            <thead>
                                <tr>
                                    <th>Resource Type Name</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody id="driverscost-list">

                            </tbody>
                        </table>

                    </div>
                    <div class="col-md-1"> </div>
                </div>

            </div>
            <!-- Chart 3 drill down end -->

            <!-- Chart 5 drill down start -->

            <div class="drilldownchart5 text-center"  style="display:none;">

                <div class="col-md-4 space-between">
                    <div class="chart-headings2">
                        <b style="">Sub:Contractors</b>
                        <span class="icon-arrow-left-thick mainlistback"></span>
                    </div>
                    <div class="chart_10drill">
                        <div id="capactyutzres_chart0" class="capactyutzres_chart">

                        </div>
                    </div>
                </div> 

                <div class="col-md-4 space-between">
                    <div class="chart-headings2">
                        <b style="">Direct Labour</b>
                        <span class="icon-arrow-left-thick mainlistback"></span>
                    </div>
                    <div class="chart_10drill">
                        <div id="capactyutzres_chart1" class="capactyutzres_chart">

                        </div>
                    </div>
                </div> 

                <div class="col-md-4 space-between">
                    <div class="chart-headings2">
                        <b style="">Plant & Equipment Usage</b>
                        <span class="icon-arrow-left-thick mainlistback"></span>
                    </div>
                    <div class="chart_10drill">
                        <div id="capactyutzres_chart2" class="capactyutzres_chart">

                        </div>
                    </div>
                </div> 

                <div class="col-md-4 space-between">
                    <div class="chart-headings2">
                        <b style="">Leased Equipment/Premises</b>
                        <span class="icon-arrow-left-thick mainlistback"></span>
                    </div>
                    <div class="chart_10drill">
                        <div id="capactyutzres_chart3" class="capactyutzres_chart">

                        </div>
                    </div>
                </div> 

            </div>
            <!-- Chart 5 drill down end -->

            <!--<div class="drilldown2chart2"  style="display:none;">
                <div class="col-md-12 text-center">         
                    <div>
                        <div class="col-md-2"> </div>

                        <div class="col-md-4">
                            <div class="chart-headings">
                                <b>Rate Chart<span class="icon-arrow-left-thick firstlistbackchart2"></span></b>
                            </div>
                            <div class="twocol-cntnr"> 
                                <div class="chartDetails-chart-wrpr">             
                                    <div class="chart-cntnr">
                                        <div class="gauge_chart" style="display: flex;align-items: center;">
                                            <div class="prj-allchart prjct-gauge avgresrate-cost" id="avgresrate-cost"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="allchartunit avgresrateunit"></div>
                        </div> 

                        <div class="col-md-4">
                            <div class="chart-headings">
                                <b>Quantity Chart<span class="icon-arrow-left-thick firstlistbackchart2"></span></b>
                            </div>
                            <div class="twocol-cntnr"> 
                                <div class="chartDetails-chart-wrpr">             
                                    <div class="chart-cntnr">
                                        <div class="gauge_chart" style="display: flex;align-items: center;">
                                            <div class="prj-allchart prjct-gauge avgresqty-cost" id="avgresqty-cost"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="allchartunit avgresqtyunit"></div>
                        </div>

                        <div class="col-md-2"> </div>
                    </div>
                </div>

            </div>-->

            <!-- Chart 2 drill down end -->

        </div>

    </div>

</div>
