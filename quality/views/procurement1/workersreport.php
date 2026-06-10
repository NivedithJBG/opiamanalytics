<?php
use app\models\Projects;
 use app\models\UserProjects;
?>
    
    
    <div class="panel panel-default acco-two tab tab-wrapper">  
	
    <!-- <input type="radio" id="rd1" name="rd" > -->
    
    <div class="panel-heading" >
        <input type="hidden" id="listdasboard">
      <h4 class="panel-title acc_trigger" id="wrks_report">
        <a data-toggle="collapse" data-parent="#accordionprocureport" href="#collapseworkers">
        <span class="icon-note1 acc_trigger"></span>Wage Rolls</a>
      </h4>
    </div>
    <div id="collapseworkers" class="tab-content cOrder-body panel-collapse collapse">
        <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/procurement/workersreport.js" type="text/javascript"></script>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <div class="panel-body">
    
    	<div class="report-list-wrpr1" >
    		 <div id="workersreports" style="display: none;">
    		 	<div class="search-and-content-wrpr" >
    		 		 <div class="search-and-actions-wrpr row" style="padding-bottom:0px; display: block;">
    		 		 	
    		 		 	 <input type="hidden" id="estimateProject_Id" name="" value="63">
    		 		 	 <div style="display: flex;" class="col-md-12 col-sm-12 typeGroup-indication ">
    		 		 	 	<div style="display: flex; margin-left: 123px;" class="col-md-6">
                                <div class="content-search-wrpr col-md-3 col-sm-3">
    		 		 	 		 <select class="form-control" id="projdirectworkerscost" name="directworkerscostproject" style="
                                            margin-left: -39px; width: 232px;">
                        <option value="none">Select Project</option>
                        <?php
                            $projects = Projects::find()->where(['status' => 0])->andWhere(['Project_Delete_Status' => 0])->all();
                            if(count($projects) > 0) {
                                $userproject=UserProjects::find()->where(['userid' => Yii::$app->user->id])->one();
                                foreach($projects AS $project) {
                                    if(56==$project->Project_Id):
                                        $selected="selected";
                                    else:
                                        $selected="";
                                    endif;
                        ?>
                                <option value="<?= $project->Project_Id ?>" <?= $selected;?>><?= $project->Name ?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                    
                    <button id="listdirectworkerscost" class="btn btn-primary" type="button" style="margin-left:-5px;"><span class="icon-search5"></span></button>
                </div>
    		 		 	 	</div>
    		 		 	 </div>

    		 		 </div>
    		 		 <div class="content-wrpr">
    		 		 	<div class="resources-cntnt-wrpr row">
    		 		 		<table class="table table-bordered" id="directworkerscosttable" style="display: table; overflow: hidden;">
                        <thead>
                        <tr style="background-color:#f9fafa;">
                            <th>#</th>
                            
                            <th>Activity Name</th>
                           
                            <th>Unit</th>
                            <th>Average Rate</th>
                            <th>No of Days</th>
                            <th>Amount</th>
                            <th></th>
                            <!-- <th colspan="1"></th> -->
                        </tr>
                        <tr class="preloader" style="display: none;"><td colspan="10" align="center"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                        </thead>
                        <tbody id="directworkerscostitems">

                        </tbody>
                    </table>
    		 		 	</div>
    		 		 </div>
    		 	</div>
    		 	</div>
                <!-- wagerollsdetails -->
                 <div id="wagedetails" style="display: none;padding: 12px;">
                    <div class="detailedworkers" style="display: block;">
                        <div class="drilldown2back" style="margin-left: 1231px;">
                             <a href="javascript:void(0)" class="drilldown2" style="display: inline-block;position: relative;z-index: 9999;padding: 2em;margin: -2em;margin-top: 1px;color: inherit;">
                                <span class="icon-arrow-left-thick " ></span></a>
                        </div>
                        <h2 style="text-align: left;background-color: #eceef7;">Details of reporting</h2>
                        <h3 class="pull-left" id="activityname"></h3>
                        <table class="table table-bordered" id="subdetailstable">
                            <thead>
                            <tr style="background-color: #f9fafa;">
                                <th><b>Sl no</b></th>
                                <!-- <th><b>Date</b></th>  --> 
                                <th>Resource</th>
                                <th>Name of the Employee</th>
                                <th><b>Days Worked</b></th>
                                <th>Rate</th>
                                <th>Overtime Hours</th>
                                <th>OT Rate</th>
                                <th>Wages Earned</th>
                            <!--     <th>GST</th>
                                <th>Amount Inc GST</th> -->
                            </tr>
                            <tr class="preloaderitems">
                                <td colspan="9" align="center">
                                    <img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                                </td>
                            </tr>
                            </thead>
                            <tbody id="wagerows">
                                
                            </tbody>
                        </table>

                    </div>
                 </div>
    	</div>
    </div>
    </div>
</div>
