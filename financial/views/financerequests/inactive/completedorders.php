<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/financeorders.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="Financeorders"><a href="javascript:void(0)">1. Completed Orders</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div id="Completedorderslist">
                <input type="hidden" id="Financeordersearch">
                <div class="row show-grid">
                    <table class="table table-bordered" id="">
                        <thead>
                        <tr>
                            <!-- <th colspan="9"><span style="float: left;font-weight: bold;padding: 10px;width: 100%;text-align: center">Cart</span></th> -->
                            <!-- filters -->
                            <!-- <th></th>
                            <th></th> -->
                            <th>
                                <!-- Order Type -->
                                <select name="order_type" class="form-control" id="order_type">
                                    <option value="">Select Order Type</option>
                                    <option value="1">Purchase Order</option>
                                    <option value="2">Work Order</option>
                                    <option value="3">Muster Roll</option>
                                    <option value="4">Lease Order</option>
                                </select>
                            </th>
                            <th>
                                <!-- Project -->
                                <?php
                                $projects = Projects::model()->findAll(array('condition'=>'Project_Delete_Status != 1'));
                                if(count($projects) > 0) {
                                    ?>
                                    <select name="project_" class="form-control" id="project_">
                                        <option value="">Select Project</option>
                                        <?php foreach($projects AS $project) { ?>
                                            <option value="<?= $project->Project_Id ?>"><?= $project->Name ?></option>
                                        <?php } ?>
                                    </select>
                                <?php } ?>
                            </th>
                            <th>
                                <!-- Vendor Name -->
                                <?php
                                $vendors = Vendors::model()->findAll(array('condition'=>'Status = 0','order' => 'Name ASC'));
                                if(count($vendors) > 0) {
                                    ?>
                                    <select name="vendor_" class="form-control" id="vendor_">
                                        <option  value="">Select Vendor Name</option>
                                        <?php foreach($vendors AS $vendor) { ?>
                                            <option value="<?= $vendor->Vendor_Id ?>"><?= $vendor->Name ?></option>
                                        <?php } ?>
                                    </select>
                                <?php } ?>
                            </th>
                            <!-- <th></th>
                            <th></th> -->
                            <th colspan="5"><button type="button" class="btn btn-primary" id="search_completed">Search</button></th>
                        </tr>
                        </thead>
                    </table>
                    <table class="table table-bordered" id="Financeorderstable">
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
                            <th colspan="5"></th>
                        </tr>
                        <tr class="preloader"><td colspan="11" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                        </thead>
                        <tbody  id="Financeorderitems">

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
                        <input type="email" class="form-control" id="emailid" required>
                        <span class="error"></span>
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject:</label>
                        <input type="text" class="form-control" id="subject" required>
                        <span class="error"></span>
                    </div>
                    <div class="form-group">
                        <label for="body">Body:</label>
                        <textarea rows="8" cols="25" class="form-control" id="body" required></textarea>
                        <span class="error"></span>
                        <!--<input type="text" class="form-control" id="body" required>-->
                    </div>
                    <div class="mailloader" style="display: none">
                        <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/mail.gif" align="middle">
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="orderid">
                    <div class="alert alert-success" id="succesinfo" style="display: none">

                    </div>
                    <div class="alert alert-warning" id="errorinfo" style="display: none">

                    </div>
                    <button type="button" class="btn btn-default" id="emailorder">Send</button>
                </div>
            </form>
        </div>

    </div>
</div>