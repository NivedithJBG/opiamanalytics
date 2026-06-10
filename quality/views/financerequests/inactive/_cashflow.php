<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/cashflow.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="cashflowreport" style="display: none;"><a href="javascript:void(0)">4. Cash Flow Report</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div id="cashflowlistsection">
                <div class="row show-grid">
                    <div class="col-md-12" style="font-size: large; display: block;">Geotech Offshore Structures (P) Ltd</div>
                    <div class="col-md-12" style="font-size: large; display: block;">Cash flow report</div>
                    <table class="table table-bordered" id="cashflowtable" style="display: table; overflow: hidden;">
                        <thead id="cashflowhead">
                            <tr class="preloader" style="display: none;"><td colspan="5" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"></td></tr>
                        </thead>
                        <tbody id="cashflowitems">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>