<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/workorder.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function() {
        $('#cashbilldate').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true,maxDate: new Date()});
    });
</script>
<h2 class="acc_trigger" id="workorder"><a href="javascript:void(0)">3. Cash Invoice</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <!--<div class="row show-grid">
                <div class="col-md-3"><a href="<?php /*echo Yii::app()->request->baseUrl; */?>/FinanceRequests/cashbill"><button type="button" class="btn btn-success"  id="addcashbill"><span class="glyphicon glyphicon-plus-sign"></span>Add Cash Bill</button></a> </div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="listcashbill"><span class="glyphicon glyphicon-list-alt"></span>List Cash Bill</button></div>
            </div>-->
            <div class="row show-grid">
                <div class="col-md-2"><button type="button" class="btn btn-success"  id="addcashbill"><span class="glyphicon glyphicon-plus-sign"></span>Add Cash Bill</button></div>
                <div class="col-md-2"><button type="button" class="btn btn-danger" id="listcashbill"><span class="glyphicon glyphicon-list-alt"></span>List Cash Bill</button></div>
            </div>
            <div id="cashbilllistsection">
                <div class="row show-grid">
                    <table class="table table-bordered" id="cashbilltable" style="display: table; overflow: hidden;">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>User</th>
                            <!--<th >Activity</th>-->
                            <th >Purpose</th>
                            <!--<th >Accounthead</th>-->
                            <th >Amount</th>
                            <th colspan="3"></th>
                        </tr>
                        <tr class="preloader" style="display: none;"><td colspan="10" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                        </thead>
                        <tbody id="cashbillitems">

                        </tbody>
                    </table>
                </div>
            </div>
            <div id="cashbilladdsection" style="display: none">
                <form action="" id="cashbillform">
                    <div class="row show-grid">
                        <input type="hidden" id="advanceid" name="advanceid">
                        <div class="col-md-2">
                            <input type="text" class="form-control datepicker" name="Cashbill_Date" id="cashbilldate" value="<?php echo date('d-m-Y');?>">
                        </div>
                    </div>
                    <table class="table table-bordered" id="cashbillcreatetable" style="display: table;">
                        <thead>
                            <tr>
                                <th>Invoice No</th>
                                <th>Party</th>
                                <th>Purpose</th>
                                <th>Rate</th>
                                <th>Quantity</th>
                                <th>Amount</th>
                                <th>GST</th>
                                <th>IGST</th>
                            </tr>
                        </thead>
                        <tbody id="advanceitems">

                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>