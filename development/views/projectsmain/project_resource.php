<div class="panel panel-default resource acco-two tab tab-wrapper">
	<input type="radio" id="rd1" class="projectresources" name="rd" >
	<div class="panel-heading" >
		<input type="hidden" id="listdasboard">
		<h4 class="panel-title acc_trigger">
			<a  href="#">
            <span class="icon-note1 acc_trigger"></span>Project Resource Report</a>
		</h4>
	</div>
	<div class="tab-content cOrder-body panel-collapse ">
		<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projectsmain/project_resource.js" type="text/javascript"></script>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <div class="panel-body">
        	<div class="search-and-content-wrpr">
        		<div class="row" id="reporthead" >
        			
        				 <div class="backbttn" style="margin-left: 243px;">
        				 	<a href="#" id="listestimateitems"></a>
                            <a href="#" id="listrestypes"></a>
                          </div>
                          <div class="col-md-5 type"><br>
                                &nbsp;&nbsp;<span id="projectname"></span>
                                &nbsp;&nbsp;<span class="projectnameDetails" id="projectnameDetails" style="display: none;"></span>
                           </div>
        				<div class="resources-cntnt-wrpr row">
        					<table class="table table-bordered" id="resourcestable" style="display: table; overflow: hidden;margin-left: 19px;">
                        <thead>
                        <tr style="background-color:#f9fafa;">
                            <th>#</th>
                            <th colspan="9">Resource Type</th>
                            <th colspan="9" style="width: 10%;">Amount</th>
                            <th></th>
                            <!-- <th colspan="1"></th> -->
                        </tr>
                        <tr class="preloader" style="display: none;"><td colspan="10" align="center"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                        </thead>
                        <tbody id="resourcedetail">

                        </tbody>
                    </table>
        				</div>
        			
        		</div>
        		<!-- details -->
        		<div id="resourcedetailsreport" style="display: none;padding: 12px;">
        			<div class="detailedworkers" style="display: block;">
        				<div class="row  alloc-activity-title" style="background-color: #ecedef;">
                        <div class="col-md-5 type">
                        </div>
                        <div class="col-md-5 type">
                        <h3 class="pull-left" id="resourcetype" style="font-size: 24px;"></h3>
                        </div>
                        <div class="col-md-2 type text-right">
                        <button class="btn btn-primary drilldowns" type="button" style="margin-right: 62px;" ><span><font color="white">Back</font></span></button>
                        </div>
                        </div>
                        
                    	
                         <table class="table table-bordered" id="subdetailstable">
                            <thead>
                            <tr style="background-color: #f9fafa;">
                                <th><b>#</b></th>
                                <th>Resource Name</th>
                                <th>Quantity</th>
                                <th width="25px">Amount</b></th>
                            </tr>
                            <tr class="preloaderitems">
                                <td colspan="9" align="center">
                                    <img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                                </td>
                            </tr>
                            </thead>
                            <tbody id="resourcerows">
                                
                            </tbody>
                        </table>
        			</div>
        		</div>
        		<!-- details end -->
        	</div>
        </div>
	</div>
</div>