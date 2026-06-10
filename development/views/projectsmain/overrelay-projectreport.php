<?php
use app\models\Vendors;  
use app\models\Resourcetype;  
use app\models\Brand;  
use app\models\AccountsItem;  
use app\models\AccountsSub;
use app\models\TermsCondtns;
use app\models\ProjuserSelection;
?>
<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projectsmain/over_menu_projectreport.js" type="text/javascript"></script>
<div class="container-fluid procu-accordion">
    <div class="row">
        
        <div class="menu1-popup-cntnr">

            <div class="menu1-cntnt-wrpr">
                <div class="icon-groups type"> 
                    <!-- <a href="#" title="Close" class="btn btn-primary text-button menu1-win-close">&#10006; Close</a> -->
                </div>
                <div class="row show-grid"> </div>
                <div style="text-align: center;"><b><h4>Project Report</h4></b></div>
                <div class="col-md-12">
                    <div class="panel-group acco-one-active" >

                    <!-- tab 1  -->

                    <div class="panel panel-default project-tab acco-one tab tab-wrapper">
                    <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projectsmain/rProjectestimate.js" type="text/javascript"></script>
                    <input type="radio" id="rd5" class="R-ProjectEstimate" name="rd">
                    <div class="panel-heading" >
                        <h4 class="panel-title">
                            <a  href="#">
                            <span class="icon-note1"></span>Project Report</a>
                        </h4>
                    </div>
                    <div  class="tab-content cOrder-body panel-collapse ">
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

                    <!-- tab 2  -->
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
    
                       

                        <!-- tab 3  -->

                        <div class="panel panel-default schedule acco-three tab tab-wrapper" style="display: none;">
                            <input type="radio" id="rd1" class="projectschedule" name="rd" >
                            <div class="panel-heading" >
                                <input type="hidden" id="listdasboard">
                                <h4 class="panel-title acc_trigger">
                                    <a  href="#">
                                    <span class="icon-note1 acc_trigger"></span>Project Schedule Report</a>
                                </h4>
                            </div>
                            <div class="tab-content cOrder-body panel-collapse ">
                                <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projectsmain/project_schedule.js" type="text/javascript"></script>
                                <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
                                <div class="panel-body">
                                    <div class="search-and-content-wrpr">
                                        <div class="row" id="reporsthead" >
                                        <div class="backbttn" style="margin-left: 243px;">
                                                    <a href="#" id="listschedule"></a>
                                                  </div>
                                        <div class="col-md-12" style=" height: 173px;">
                                            <div class="preloader" id="Promain-preloader-Schedulewbs" style="display: none;" align="center">
                                                <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                                            </div>
                                            <div style="display: flex;" class="col-md-6">
                                            <select class="form-control" id="listworkgroup-Schedul-datas" name="resource" style="margin-left: 38px;width: 233px;    margin-top: 25px;">
                                                <option value="none">Select Item</option>

                                            </select>
                                            <?php  $uid = Yii::$app->user->Id; 
                                                $projuser = ProjuserSelection::find()->where(['userid' => $uid])->one();
                                                $prjectid=$projuser->projectid; ?>
                                                <input type="hidden" value="<?php echo $prjectid; ?>" id="prjid">
                                            <button value="1" name="Product_saveproduct" style="margin-left: 8px;width: 85px;height: 40px;margin-top: 24px;" id="ganttt" class="btn btn-primary" type="button"><span class="icon-print"></span></button> 
                                        </div>
                                           <!--  <div id="listworkgroup-Schedul-datas"></div> -->
                                        </div>
                                       </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- tab 4  -->

                        


                        <!-- tabs end  -->

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
