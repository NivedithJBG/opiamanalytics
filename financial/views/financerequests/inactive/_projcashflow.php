<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/projcashflow.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="projcashflow" style="display: none;"><a href="javascript:void(0)">6. Project Cash Flow Statement</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-3">
                    <select class="form-control" id="projectliststmt">
                        <option value="none">Select Project</option>
                        <?php
                        $project=Projects::model()->findAll(array('condition'=>'Project_Delete_Status=0'));
                        foreach($project AS $list):
                            echo "<option value='".$list->Project_Id."'>".$list->Name."</option>";
                        endforeach;
                        ?>
                    </select>
                    <span class="error" style="display: none;float: left"></span>
                </div>
                <div class="col-md-3" style="font-size: large" id="projquarterdiv">
                    <select id="projectquarter" class="form-control">
                        <option value="1"><?php echo date('jS F Y ', strtotime('first day of april')).' - '.date('jS F Y ', strtotime('last day of june'));?> </option>
                        <option value="2"><?php echo date('jS F Y ', strtotime('first day of july')).' - '.date('jS F Y ', strtotime('last day of september'));?> </option>
                        <option value="3"><?php echo date('jS F Y ', strtotime('first day of october')).' - '.date('jS F Y ', strtotime('last day of december'));?> </option>
                        <option value="4"><?php echo date('jS F Y ', strtotime('first day of january this year')).' - '.date('jS F Y ', strtotime('last day of march this year'));?> </option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-primary" id="projectcashflowstmt">
                        <span class="glyphicon glyphicon-search"></span>Search</button>
                </div>
            </div>
            <div id="projcashflowliststmt" style="display: none">
                <div class="col-md-12" style="font-size: large; display: block;">Geotech Offshore Structures (P) Ltd</div>
                <div class="col-md-12" style="font-size: large; display: block;" id="projectinfo"></div>
                <table class="table table-bordered" id="projcashflowtable" style="display: table; overflow: hidden;">
                    <thead>
                    <tr class="preloader" style="display: none;"><td colspan="10" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"></td></tr>
                    </thead>
                    <tbody id="projcashflowstmtitems">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>