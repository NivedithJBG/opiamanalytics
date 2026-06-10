<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/profitandloss.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function() {
        $( "#prlfromdate" ).datepicker({ defaultDate:new Date(),changeMonth: true,
            changeYear: true,dateFormat: 'dd-mm-yy' });
    });
    $(function() {
        $( "#prltodate" ).datepicker({  maxDate: new Date(),changeMonth: true,
            changeYear: true,dateFormat: 'dd-mm-yy' });
    });
</script>
<h2 class="acc_trigger" id="profitandloss" style="display: none;"><a href="javascript:void(0)">3. Profit And Loss</a></h2>
    <div class="acc_container">
        <div class="block">
            <div class="jumbotron">
                <div class="row show-grid">
                    <!--<div class="col-md-5">
                        <select class="form-control" id="prlproject" name="prlproject">
                            <option value="0">Select Project</option>
                            <?php /*$project=Projects::model()->findAll(array('condition'=>'Project_Delete_Status=0'));
                            foreach($project AS $list):
                                echo "<option value='".$list->Project_Id."'>".$list->Name."</option>";
                            endforeach;*/?>
                        </select>
                        <span class='error' style="float: left;"></span>
                    </div>-->
                    <div class="col-md-2">
                        <input type="text" class="form-control" id="prlfromdate" name="prlfromdate" placeholder="Select Date">
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control" id="prltodate" name="prltodate" placeholder="Select Date">
                    </div>
                    <div class="col-md-3">
                        <button id="prlsearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>
                <div id="prlsection">
                    <div class="row show-grid">
                        <form>
                            <div id="prlinfo" class="col-md-10">

                            </div>
                            <div class="col-md-2" id="printprofitloss" style="padding-top: 20px;"></div>
                            <div class="row">
                                <table class="table table-bordered" id="prltable" style="display: table; overflow: hidden;">
                                    <thead>
                                    <tr>
                                        <th>Account</th>
                                        <th colspan="3">Amount</th>
                                    </tr>
                                    <tr class="preloader" style="display: none;"><td colspan="5" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                                    </thead>
                                    <tbody id="prlitems">

                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>