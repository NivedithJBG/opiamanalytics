<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/operationorders.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="operationorders"><a href="javascript:void(0)">2.Purchase Orders</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-3" style="text-align: left;">
                    <h4 id="projorderprojname"></h4>
                </div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="projordersearch"><span class="glyphicon glyphicon-list-alt"></span>List Purchase Orders</button></div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="purchasehistory"><span class="glyphicon glyphicon-list-alt"></span>Purchase Order History</button></div>
            </div>
            <div id="projorderslist">
                <!--<input type="hidden" id="projordersearch">-->
                <div class="row show-grid">
                    <table class="table table-bordered" id="projorderstable">
                        <thead>
                        <!--<tr>
                            <th colspan="9"><span style="float: left;font-weight: bold;padding: 10px;width: 100%;text-align: center">Cart</span></th>
                        </tr>-->
                        <tr>
                            <th>Date</th>
                            <th>Order Type</th>
                            <th>Project</th>
                            <th>Vendor Name</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th colspan="2"></th>
                        </tr>
                        <tr class="preloader"><td colspan="8" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                        </thead>
                        <tbody  id="projorderitems">

                        </tbody>
                    </table>
                </div>
            </div>
            <div id="purchasehistorysection">
                <div id="searchdiv" class="row show-grid" style="display: block;">
                    <div class="col-md-3">
                        <input type="text" placeholder="Search" id="searchpohistory" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button id="pohistorysearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>
                <div class="row show-grid">
                    <table class="table table-bordered" id="purchasehistorytable" style="display: table; overflow: hidden;">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Project</th>
                            <th >Vendor Name</th>
                            <th >Activity Name</th>
                            <th >Amount</th>
                            <th></th>
                        </tr>
                        <tr class="preloader" style="display: none;"><td colspan="6" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                        </thead>
                        <tbody id="purchasehistoryitems">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="emailorderModel" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Email Order</h4>
            </div>
            <form action="" id="orderemail" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="email">Email address:</label>
                        <input type="email" class="form-control" id="orderemailid" required>
                        <span class="error"></span>
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject:</label>
                        <input type="text" class="form-control" id="ordersubject" required>
                        <span class="error"></span>
                    </div>
                    <div class="form-group">
                        <label for="body">Body:</label>
                        <textarea rows="8" cols="25" class="form-control" id="orderbody" required></textarea>
                        <span class="error"></span>
                        <!--<input type="text" class="form-control" id="body" required>-->
                    </div>
                    <div class="mailloader" style="display: none">
                        <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/mail.gif" align="middle">
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="orderid">
                    <div class="alert alert-success" id="ordersuccesinfo" style="display: none">

                    </div>
                    <div class="alert alert-warning" id="ordererrorinfo" style="display: none">

                    </div>
                    <button type="button" class="btn btn-default" id="emailorder">Send</button>
                </div>
            </form>
        </div>

    </div>
</div>