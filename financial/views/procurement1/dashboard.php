<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/procurement/dashboard.js" type="text/javascript"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<div class="chart-popup-cntnr">

    <div class="chart-cntnt-wrpr">
        <a href="#" title="Close" class="icon-close chart-win-close procdash-close"></a>

        <div class="row show-grid"> </div>

        <div id="proc-dashboard">
            <input type="hidden" id="listprocdasboard">

            <div class="col-md-12 text-center space-between mainlist"> 

            <script type='text/javascript' src='https://prod-apnortheast-a.online.tableau.com/javascripts/api/viz_v1.js'></script><div class='tableauPlaceholder' style='width: 1280px; height: 524px;'><object class='tableauViz' width='1280' height='524' style='display:none;'><param name='host_url' value='https%3A%2F%2Fprod-apnortheast-a.online.tableau.com%2F' /> <param name='embed_code_version' value='3' /> <param name='site_root' value='&#47;t&#47;opiam' /><param name='name' value='RoRoJetty&#47;ScheduleManagementDashboard' /><param name='tabs' value='yes' /><param name='toolbar' value='yes' /><param name='showAppBanner' value='false' /></object></div>

                <div class="col-md-4 space-between" style="display:none;">
                    <div class="chart-headings2">
                        <b style="">Materials</b>
                    </div>
                    <div class="twocol-cntnr">

                        <div class="prj-allchart restyp-materials" id="restyp-materials">
                            
                        </div>

                    </div>

                </div>

                <div id="errormessage"> </div>

            </div>

            <!-- Chart first drill down start -->

            <div class="col-md-12 text-center space-between drilldownlist1" style="display:none"> 

                <div class="col-md-6 space-between resourcelisting" data-id="0">
                    <div class="chart-headings2">
                        <b style=""><span id="resourname0">Cement Bulker-OPC</span><span class="icon-arrow-left-thick mainproclistback"></span></b>
                    </div>
                    <div class="twocol-cntnr">

                        <div class="prj-allchart restyp-resource" id="restyp-resource">
                            
                        </div>

                    </div>

                </div>

                <div id="errormessage"> </div>

            </div>
            <!-- Chart first drill down end -->

            <!-- Chart first drill down start -->

            <div class="col-md-12 text-center space-between drilldownlist2" style="display:none"> 

                <div class="col-md-12 space-between">
                    <div class="chart-headings2">
                        <b style="">Quantity Chart<span class="icon-arrow-left-thick mainproclistback1"></span></b>
                    </div>
                    <div class="twocol-cntnr">

                        <div class="prj-allchart resource-barchart" id="resource-barchart">
                            
                        </div>

                    </div>

                </div>

                <div id="resactvtydial"> </div>

                <div id="errormessage"> </div>

            </div>
            <!-- Chart first drill down end -->

        </div>

    </div>

</div>
