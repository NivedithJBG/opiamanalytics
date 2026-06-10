<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/bills.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="bills"><a href="javascript:void(0)">1. Bills</a></h2>
    <div class="acc_container">
        <div class="block">
            <div class="jumbotron">
                <div class="row show-grid">
                    <!--<div class="col-md-3"><button type="button" class="btn btn-success listbills" id="listpurchasebills" data-id="1"><span class="glyphicon glyphicon-list-alt"></span>Purchase Bills</button></div>
                    <div class="col-md-3"><button type="button" class="btn btn-danger listbills" id="listworkbills" data-id="2"><span class="glyphicon glyphicon-list-alt"></span>Workbill Bills</button></div>
                    <div class="col-md-3"><button type="button" class="btn btn-danger listbills" id="listclientbills" data-id="3"><span class="glyphicon glyphicon-list-alt"></span>Cient Bills</button></div>
                    <div class="col-md-3"><button type="button" class="btn btn-danger listbills" id="listutilitybills" data-id="0"><span class="glyphicon glyphicon-list-alt"></span>Utility Bills</button></div>-->
                    <div class="col-md-2" style="text-align: center ; padding-right: 0">Select Bill Type</div>
                    <div class="col-md-3" style="padding-left:0 ">
                        <select class="form-control" name="Billtype" id="billtypelist">
                            <option value="0">Utility Bill</option>
                            <option value="2" selected>Purchase Bill</option>
                            <option value="1">Work Bill</option>
                            <option value="3">Client Bill</option>
                            <option value="4">Travel Bill</option>
                            <option value="5">Cash Bill</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <a id="billscreate" href="<?php echo Yii::app()->request->baseUrl; ?>/FinanceRequests/bills?billtype=2">
                            <button type="button" class="btn btn-success"  id="addbills"><span class="glyphicon glyphicon-plus-sign"></span>Add Bills</button>
                        </a>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger" id="listapprovedbills" value="1">
                            <span class="glyphicon glyphicon-list-alt"></span>List Approved Bills</button>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger" id="listnonapprovedbills" value="0">
                            <span class="glyphicon glyphicon-list-alt"></span>List Non Approved Bills</button>
                    </div>
                </div>

                <div id="billslistsection">
                    <div class="row show-grid" id="filters" style="display: none">
                        <div class="col-md-4">
                            <select class="form-control" name="vendor" id="vendorlist">
                                <option value="0">Select Vendor</option>
                                <?php $vendors=Vendors::model()->findAll();
                                foreach($vendors AS $vendor):?>
                                    <option value="<?php echo $vendor['Vendor_Id']?>"><?php echo $vendor['Name']?></option>
                                <?php endforeach;?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-control" name="place" id="placebill">
                                <option value="0">Select Project</option>
                                <?php $projects = Projects::model()->findAll(array('condition'=>'Project_Delete_Status=0'));
                                foreach ($projects AS $project):?>
                                    <option value="<?php echo $project['Project_Id']?>"><?php echo $project['Name']?></option>
                                <?php endforeach;?>
                            </select>
                        </div>
                        <!--<div class="col-md-3">
                            <button type="button" class="btn btn-danger" id="listasdapprovedbills" value="1">
                            <span class="glyphicon glyphicon-list-alt"></span>List Approved Bills</button>
                        </div>-->
                        <div style="display: none"><button type="button" class="btn btn-danger " id="listbills" data-id="2"><span class="glyphicon glyphicon-list-alt"></span>Purchase Bills</button></div>
                    </div>
                    <div class="row show-grid">
                        <div class="col-md-12" id="billsinfo" style="font-size: large;">Purchase Bill</div>
                    </div>
                    <div class="row show-grid">
                        <!--Table-->
                        <form>
                            <table class="table table-bordered" id="billstable" style="display: table; overflow: hidden;">
                                <?php $user=User::model()->active()->findbyPk(Yii::app()->user->id);if(User::model()->isAdmin() || $user['superuser']==2): ?>
                                <thead>
                                <tr>
                                    <th>Bill No</th>
                                    <th>Date</th>
                                    <th>User</th>
                                    <th>Place</th>
                                    <th >Party</th>
                                    <th >Total Amount</th>
                                    <th >Due Date</th>
                                    <th colspan="3"></th>
                                </tr>
                                <tr class="preloader" style="display: none;"><td colspan="10" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                                </thead>
                                <?php else:?>
                                <thead>
                                <tr>
                                    <th>Bill No</th>
                                    <th>Date</th>
                                    <th>User</th>
                                    <th>Project</th>
                                    <th >Party</th>
                                    <th >Total Amount</th>
                                    <th >Due Date</th>
                                    <th ></th>
                                </tr>
                                <tr class="preloader" style="display: none;"><td colspan="8" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                                </thead>
                                <?php endif;?>
                                <tbody id="billitems">

                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script type="text/javascript">
    $('#listpurchasebills').click(function () {
        $("#billscreate").attr("href", "<?php echo Yii::app()->request->baseUrl; ?>/FinanceRequests/bills?billtype=2");
        $('#listbills').attr('data-id',2);
        $('#listpurchasebills').removeClass('btn-danger').addClass('btn-success');
        $('#listworkbills').removeClass('btn-success').addClass('btn-danger');
        $('#listclientbills').removeClass('btn-success').addClass('btn-danger');
        $('#listutilitybills').removeClass('btn-success').addClass('btn-danger');
        $('#listbills').trigger('click');
    });
    $('#listworkbills').click(function () {

        $("#billscreate").attr("href", "<?php echo Yii::app()->request->baseUrl; ?>/FinanceRequests/bills?billtype=1");
        $('#listbills').attr('data-id',1);
        $('#listworkbills').removeClass('btn-danger').addClass('btn-success');
        $('#listpurchasebills').removeClass('btn-success').addClass('btn-danger');
        $('#listclientbills').removeClass('btn-success').addClass('btn-danger');
        $('#listutilitybills').removeClass('btn-success').addClass('btn-danger');
        $('#listbills').trigger('click');
    });
    $('#listclientbills').click(function () {
        $("#billscreate").attr("href", "<?php echo Yii::app()->request->baseUrl; ?>/FinanceRequests/bills?billtype=3");
        $('#listbills').attr('data-id',3);
        $('#listclientbills').removeClass('btn-danger').addClass('btn-success');
        $('#listpurchasebills').removeClass('btn-success').addClass('btn-danger');
        $('#listworkbills').removeClass('btn-success').addClass('btn-danger');
        $('#listutilitybills').removeClass('btn-success').addClass('btn-danger');
        $('#listbills').trigger('click');
    });
    $('#listutilitybills').click(function () {
        $("#billscreate").attr("href", "<?php echo Yii::app()->request->baseUrl; ?>/FinanceRequests/bills?billtype=0");
        $('#listutilitybills').removeClass('btn-danger').addClass('btn-success');
        $('#listpurchasebills').removeClass('btn-success').addClass('btn-danger');
        $('#listworkbills').removeClass('btn-success').addClass('btn-danger');
        $('#listclientbills').removeClass('btn-success').addClass('btn-danger');
        $('#listbills').attr('data-id',0);
        $('#listbills').trigger('click');
    });
    $('#listapprovedbills').click(function () {

        $('#listbills').trigger('click');
    });
    $(document).on("change", "#billtypelist", function () {
        var billtype = $(this).val();
        if(billtype==0){
            $('#billsinfo').html('Utility Bill');
            $("#billscreate").attr("href", "<?php echo Yii::app()->request->baseUrl; ?>/FinanceRequests/bills?billtype=0");
            $('#listbills').attr('data-id',0);
            //$('#listapprovedbills').val('0');
            $('#listnonapprovedbills').trigger('click');
        }
        else if(billtype==2){
            $('#billsinfo').html('Purchase Bill');
            $("#billscreate").attr("href", "<?php echo Yii::app()->request->baseUrl; ?>/FinanceRequests/bills?billtype=2");
            $('#listbills').attr('data-id',2);
            //$('#listapprovedbills').val('0');
            $('#listnonapprovedbills').trigger('click');
        }
        else if(billtype==1){
            $('#billsinfo').html('Work Bill');
            $("#billscreate").attr("href", "<?php echo Yii::app()->request->baseUrl; ?>/FinanceRequests/bills?billtype=1");
            $('#listbills').attr('data-id',1);
            //$('#listapprovedbills').val('0');
            $('#listnonapprovedbills').trigger('click');
        }
        else if(billtype==3){
            $('#billsinfo').html('Client Bill');
            $("#billscreate").attr("href", "<?php echo Yii::app()->request->baseUrl; ?>/FinanceRequests/bills?billtype=3");
            $('#listbills').attr('data-id',3);
            //$('#listapprovedbills').val('0');
            $('#listnonapprovedbills').trigger('click');
        }
        else if(billtype==4){
            $('#billsinfo').html('Travel Bill');
            $("#billscreate").attr("href", "<?php echo Yii::app()->request->baseUrl; ?>/FinanceRequests/bills?billtype=4");
            $('#listbills').attr('data-id',4);
            //$('#listapprovedbills').val('0');
            $('#listnonapprovedbills').trigger('click');
        }
        else if(billtype==5){
            $('#billsinfo').html('Cash Bill');
            $("#billscreate").attr("href", "<?php echo Yii::app()->request->baseUrl; ?>/FinanceRequests/bills?billtype=5");
            $('#listbills').attr('data-id',5);
            //$('#listapprovedbills').val('0');
            $('#listnonapprovedbills').trigger('click');
        }

    });


</script>