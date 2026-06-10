<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/balancesheet.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function() {
        $( "#bsfromdate" ).datepicker({ defaultDate:new Date(),changeMonth: true,
            changeYear: true,dateFormat: 'dd-mm-yy' });
    });
    $(function() {
        $( "#bstodate" ).datepicker({  maxDate: new Date(),changeMonth: true,
            changeYear: true,dateFormat: 'dd-mm-yy' });
    });
</script>
<h2 class="acc_trigger" id="balancesheet" style="display: none;"><a href="javascript:void(0)">2. Balance Sheet</a></h2>
    <div class="acc_container">
        <div class="block">
            <div class="jumbotron">
                <div class="row show-grid">
                    <!--<div class="col-md-5">
                        <select class="form-control" id="bsproject" name="project">
                            <option value="0">Select Project</option>
                            <?php $project=Projects::model()->findAll();
                            foreach($project AS $list):
                                echo "<option value='".$list->Project_Id."'>".$list->Name."</option>";
                            endforeach;?>
                        </select>
                        <span class='error' style="float: left;"></span>
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control" id="bsfromdate" name="bsfromdate" placeholder="Select Date">
                    </div>-->
                    <div class="col-md-2">
                        <input type="text" class="form-control" id="bstodate" name="bstodate" placeholder="Select Date">
                    </div>
                    <div class="col-md-3">
                        <button id="bssearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>
                <div id="bssection">
                    <div class="row show-grid">
                        <form>
                            <div id="bsinfo" class="col-md-10">

                            </div>
                            <div class="col-md-2" id="printbalance" style="padding-top: 20px;"></div>
                            <div class="row">
                                <table class="table table-bordered" id="bstable" style="display: table; overflow: hidden;">
                                    <thead>
                                    <tr>
                                        <th>Account SubGroup</th>
                                        <th></th>
                                        <th>Note no</th>
                                        <th>Amount</th>
                                    </tr>
                                    <tr class="preloader" style="display: none;"><td colspan="4" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                                    </thead>
                                    <tbody id="bsitems">

                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>