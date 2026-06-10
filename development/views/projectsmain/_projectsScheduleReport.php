<?php
use app\models\ProjuserSelection;
?>
<div class="panel panel-default schedule acco-three tab tab-wrapper" >
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