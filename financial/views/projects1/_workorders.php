<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/workorders.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="workorders"><a href="javascript:void(0)">3.Work Orders</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-3" style="text-align: left;">
                    <h4 id="workorderprojname"></h4>
                </div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="workordersearch"><span class="glyphicon glyphicon-list-alt"></span>List Work Orders</button></div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="workorderhistory"><span class="glyphicon glyphicon-list-alt"></span>Work Order History</button></div>
            </div>
            <div id="workorderslist">
                <!--<input type="hidden" id="workordersearch">-->
                <div class="row show-grid">
                    <table class="table table-bordered" id="workorderstable">
                        <thead>
                        <!--<tr>
                            <th colspan="9"><span style="float: left;font-weight: bold;padding: 10px;width: 100%;text-align: center">Cart</span></th>
                        </tr>-->
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
                        <tbody  id="workorderitems">

                        </tbody>
                    </table>
                </div>
            </div>
            <div id="workorderhistorysection">
                <div class="row show-grid">
                    <table class="table table-bordered" id="workorderhistorytable" style="display: table; overflow: hidden;">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Bill No</th>
                            <th>Project</th>
                            <th >Vendor Name</th>
                            <!--<th >Activity Name</th>-->
                            <th >Amount</th>
                            <th></th>
                        </tr>
                        <tr class="preloader" style="display: none;"><td colspan="6" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                        </thead>
                        <tbody id="workorderhistoryitems">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>