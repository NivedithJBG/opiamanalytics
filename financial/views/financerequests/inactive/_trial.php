<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/trial.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function() {
        $( "#trialfromdate" ).datepicker({
            defaultDate:new Date(),changeMonth: true,
            changeYear: true,dateFormat: 'dd-mm-yy'
        });
    });
    $(function() {
        $( "#trialtodate" ).datepicker({
            maxDate: new Date(),changeMonth: true,
            changeYear: true,dateFormat: 'dd-mm-yy'
        });
    });
</script>
<h2 class="acc_trigger" id="trial"><a href="javascript:void(0)">12. Trial Balance</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-2">
                    <input type="text" class="form-control" id="trialfromdate" name="trialfromdate" placeholder="Select Date">
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control" id="trialtodate" name="trialtodate" placeholder="Select Date">
                </div>
                <div class="col-md-2">
                    <button id="trialsearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                </div>
            </div>
            <div id="trialsection">
                <div class="row show-grid">
                    <form>
                        <div id="trialinfo" class="col-md-10">
                            <!--<h4>Geotech Offshore Structures (P) Ltd</h4>
                            <h4>Trial Balance as on <?php /*echo date("d/m/Y");*/?></h4>-->
                        </div>
                        <div class="col-md-2" id="printtrial" style="padding-top: 20px;"></div>
                        <div class="col-md-2" id="exporttrial" style="padding-top: 20px;"></div>
                        <div class="row">
                            <table class="table table-bordered" id="trialtable" style="display: table; overflow: hidden;">
                                <thead>
                                <tr>
                                    <th>Account Head</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                </tr>
                                <tr class="preloader" style="display: none;"><td colspan="4" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"></td></tr>
                                </thead>
                                <tbody id="trialitems">
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>