<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/leaseorders.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="leaseorders"><a href="javascript:void(0)">5.Lease Orders</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-3" style="text-align: left;">
                    <h4 id="leaseorderprojname"></h4>
                </div>
            </div>
            <div id="leaseorderslist">
                <input type="hidden" id="leaseordersearch">
                <div class="row show-grid">
                    <table class="table table-bordered" id="leaseorderstable">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Activity Name</th>
                            <th>Vendor Name</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th colspan="2"></th>
                        </tr>
                        <tr class="preloader"><td colspan="7" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                        </thead>
                        <tbody  id="leaseorderitems">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>