<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/corpcashflow.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="corpcashflow"><a href="javascript:void(0)">14. Cash Flow Statement</a></h2>

<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <form action="" id="cashflowstmtform" method="post">
                <div class="row show-grid" id="projcashfilter">
                    <div class="col-md-3">
                        <select class="form-control" id="cashflowrepproj" name="cashflowrepproj">
                            <option value="none">Select Project</option>
                            <?php $project=Projects::model()->findAll(array('condition'=>'Status=0 AND Project_Delete_Status=0'));
                            foreach($project AS $list):
                                echo "<option value='".$list->Project_Id."'>".$list->Name."</option>";
                            endforeach;?>
                        </select>
                        <span class='error' style="float: left;"></span>
                    </div>
                    <div class="col-md-2">
                        <select class="form-control" id="cashflowrepduration" name="duration">
                            <option value="none">Select Duration</option>
                            <?php
                            for ($m=1; $m<=12; $m++) {
                                echo '<option value="'.$m.'">'.$m.'</option>';
                            }
                            ?>
                        </select>
                        <span class='error' style="float: left;"></span>
                    </div>
                    <div class="col-md-3">
                        <?php
                        $monthcurrent = date("F Y", strtotime( date( 'Y-m-01' )));
                        $months='<option value="none">Select Begining Month</option>';
                        //$months.="<option value='".date("Y-m-01", strtotime( date( 'Y-m-01' )))."'>$monthcurrent</option>";
                        for ($m=1; $m<=12; $m++) {
                            $month = date('F Y', mktime(0, 0, 0, $m, 1));
                            $months.="<option value='".date("Y-m-01", mktime(0, 0, 0, $m, 1))."'>$month</option>";
                        }
                        ?>
                        <select class='form-control' name="cashflowrepmonth" id="cashflowrepmonth"><?php echo $months;?></select>
                        <span class="error" style="float: left"></span>
                    </div>
                    <div class="col-md-3">
                        <input type="button" id="projcashflowreport" class="btn btn-primary" value="Cash Flow">
                    </div>
                    <!--<div class="col-md-3">
                        <input type="button" id="cashflow" class="btn btn-primary" value="Cash Flow">
                    </div>-->
                </div>
                <div id="cocashflowlist" style="display: none">
                    <div class="row show-grid">
                        <div class="col-md-12" style="font-size: large; display: block;">Geotech Offshore Structures (P) Ltd</div>
                        <div class="col-md-12" style="font-size: large; display: block;" id="projectdiv"></div>
                        <!--<div class="col-md-3" style="font-size: large" id="quarterdiv">
                            <select id="quarterselector" class="form-control">
                                <option value="1"><?php /*echo date('jS F Y ', strtotime('first day of april')).' - '.date('jS F Y ', strtotime('last day of june'));*/?> </option>
                                <option value="2"><?php /*echo date('jS F Y ', strtotime('first day of july')).' - '.date('jS F Y ', strtotime('last day of september'));*/?> </option>
                                <option value="3"><?php /*echo date('jS F Y ', strtotime('first day of october')).' - '.date('jS F Y ', strtotime('last day of december'));*/?> </option>
                                <option value="4"><?php /*echo date('jS F Y ', strtotime('first day of january this year')).' - '.date('jS F Y ', strtotime('last day of march this year'));*/?> </option>
                            </select>
                        </div>-->
                        <!--<div class="col-md-6" style="padding-right: 0px">-->
                            <table class="table table-bordered" id="cocashflowtable" style="display: table; overflow: hidden;">
                                <thead>
                                <tr class="preloader" style="display: none;"><td colspan="8" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"></td></tr>
                                </thead>
                                <tbody id="cocashflowitems">

                                </tbody>
                            </table>
                        <!--</div>-->
                        <!--<div class="col-md-6" style="padding-left: 0px;left: -2px">
                            <table class="table table-bordered" id="actcashflowtable" style="display: table; overflow: hidden;">
                                <thead>
                                <tr class="preloader" style="display: none;"><td colspan="4" align="center"><img src="<?php /*echo Yii::app()->request->baseUrl; */?>/images/loader.gif" align="middle"></td></tr>
                                </thead>
                                <tbody id="actcashflowitems">

                                </tbody>
                            </table>
                        </div>-->
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<style type="text/css">
    .cashflowtxt{
        text-align: right !important;
    }
</style>