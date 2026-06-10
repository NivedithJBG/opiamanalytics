<div class="panel panel-default project-tab acco-three tab tab-wrapper">
    <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projectsmain/auditlog.js" type="text/javascript"></script>
    <!--<input type="radio" id="rd5" class="R-ProjectEstimate" name="rd">-->
    <div class="panel-heading" >
        <h4 class="panel-title audit-log">
            <a data-toggle="collapse" data-parent="#accordionproreports" href="#collapseauditlog">
            <span class="icon-note1"></span>Audit Log</a>
        </h4>
    </div>
    <div id="collapseauditlog" class="tab-content cOrder-body panel-collapse collapse">
        <div class="panel-body">
            <div class="search-and-content-wrpr">
                <div class="content-wrpr">
                    <a href="#" id="listaudit"></a>
                    <div class="preloader" style="display: none;" align="center">
                        <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                    </div>
                    
                    <div class="resources-cntnt-wrpr row" id="estimatereports">
                        <div class="audit-list-wrpr" >
                            <div class="table-wrpr">
                                <div id="auditlog_items"></div>
                                <!-- <table class="table table-bordered" id="procresourcetable" style="display: table; overflow: hidden; background-color: #ffffff ;">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Resource</th>
                                        <th>User</th>
                                        <th>Action</th>
                                        <th>Date</th>                     
                                    </tr>
                                    </thead>
                                    <tbody id="auditlog_items">

                                    </tbody> 
                                </table>    -->  
                            </div>
                        </div>
                    </div>

                    <!-- list end here -->
                    
                </div>
            </div>
        </div>
    </div>
</div>