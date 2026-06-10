<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/voucher.js" type="text/javascript"></script>
<script type="text/javascript">

$(function(){
$('#cashvoucher').click(function(){
    $('#cashvouchersection').slideDown('slow');// slide down the project listing div
    $('#bankvouchersection').slideUp('slow');// slide down the project listing div
    $('#cashreceiptsection').hide();
    $('#bankvouchersection').hide();
    $('#bankreceiptsection').hide();
    $('#billssection').hide();
    $('#journalsection').hide();
    $('#contrasection').hide();
    $('#cashvoucher').removeClass('btn-danger').addClass('btn-success');
    $('#cashreceipt').removeClass('btn-success').addClass('btn-danger');
    $('#bankvoucher').removeClass('btn-success').addClass('btn-danger');
    $('#bankreceipt').removeClass('btn-success').addClass('btn-danger');
    $('#bill').removeClass('btn-success').addClass('btn-danger');
    $('#journal').removeClass('btn-success').addClass('btn-danger');
    $('#contra').removeClass('btn-success').addClass('btn-danger');
    $.ajax({
        type: 'POST',
        url: '../voucher/cashsearch',
        beforeSend : function(){
            $('.preloader').show();
        },
        dataType: "json",
        data: {name:$('#searchcashvoucher').val()},
        success: function(data){
            if(data.error=='No')
            {
                $('#cashvoucheritems').html(data.result);
                $('#cashvouchertable').show();
            }
            else
            {
                alert(data.errortext);
            }
            $('.preloader').hide();
        }
    });
});
});

</script>
<h2 class="acc_trigger" id="voucher"><a href="javascript:void(0)">7. Vouchers</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <!--<div class="col-md-3"><a href="<?php /*echo Yii::app()->request->baseUrl; */?>/FinanceRequests/create"><button type="button" class="btn btn-success"  id="addrequest"><span class="glyphicon glyphicon-plus-sign"></span>Add Request</button></a> </div>
                <div class="col-md-3"><button type="button" class="btn btn-success"  id="addproduct"><span class="glyphicon glyphicon-plus-sign"></span>Add Product</button> </div>-->
                <div class="col-md-2"><button type="button" class="btn btn-danger" id="cashvoucher"><span class="glyphicon glyphicon-list-alt"></span>Cash Payment</button></div>
                <div class="col-md-2"><button type="button" class="btn btn-danger" id="cashreceipt"><span class="glyphicon glyphicon-list-alt"></span>Cash Receipt</button></div>
                <div class="col-md-2"><button type="button" class="btn btn-danger" id="bankvoucher"><span class="glyphicon glyphicon-list-alt"></span>Bank Payment</button></div>
                <div class="col-md-2"><button type="button" class="btn btn-danger" id="bankreceipt"><span class="glyphicon glyphicon-list-alt"></span>Bank Receipt</button></div>
                <!--<div class="col-md-2"><button type="button" class="btn btn-danger" id="bill"><span class="glyphicon glyphicon-list-alt"></span>Bills</button></div>-->
                <div class="col-md-2"><button type="button" class="btn btn-danger" id="journal"><span class="glyphicon glyphicon-list-alt"></span>Journal</button></div>
                <!--<div class="col-md-2"><button type="button" class="btn btn-danger" id="contra"><span class="glyphicon glyphicon-list-alt"></span>Contra</button></div>-->
            </div>
            <div id="cashvouchersection">
                <!--<div id="searchcashdiv" class="row show-grid" style="display: block;">
                    <div class="col-md-9">
                        <input type="text" placeholder="Search" id="searchcashvoucher" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button id="cashvouchersearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>-->
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="cashvouchertable" style="display: table; overflow: hidden;">

                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>User</th>
                                <th>Place</th>
                                <th>Project</th>
                                <th >Purpose</th>
                                <th colspan="2">Approved amount</th>
                                <th colspan="2"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="8" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>

                            <tbody id="cashvoucheritems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            <div id="cashreceiptsection">
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="cashreceipttable" style="display: table; overflow: hidden;">

                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>User</th>
                                <th>Place</th>
                                <th>Project</th>
                                <th >Purpose</th>
                                <th colspan="2">Amount</th>
                                <th colspan="2"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="8" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>

                            <tbody id="cashreceiptitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            <div id="bankvouchersection">
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="bankvouchertable" style="display: table; overflow: hidden;">

                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>User</th>
                                <th>Place</th>
                                <th>Project</th>
                                <th >Purpose</th>
                                <th colspan="2">Approved amount</th>
                                <th colspan="2"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="8" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>

                            <tbody id="bankvoucheritems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            <div id="bankreceiptsection">
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="bankreceipttable" style="display: table; overflow: hidden;">

                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>User</th>
                                <th>Place</th>
                                <th>Project</th>
                                <th >Purpose</th>
                                <th colspan="2">Amount</th>
                                <th colspan="2"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="8" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>

                            <tbody id="bankreceiptitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            <div id="billssection">
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="billtable" style="display: table; overflow: hidden;">

                            <thead>
                            <tr>
                                <th>Bill No</th>
                                <th>Date</th>
                                <th>User</th>
                                <th>Place</th>
                                <th >Party</th>
                                <th >Total Amount</th>
                                <th colspan="3"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="7" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>

                            <tbody id="billsitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            <div id="journalsection">
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="journaltable" style="display: table; overflow: hidden;">

                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>User</th>
                                <th>Project</th>
                                <th >Purpose</th>
                                <th >Amount</th>
                                <th colspan="3"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="7" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>

                            <tbody id="journalitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            <div id="contrasection">
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="contratable" style="display: table; overflow: hidden;">

                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>User</th>
                                <th>Project</th>
                                <th >Purpose</th>
                                <th colspan="2">Approved amount</th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="7" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>

                            <tbody id="contraitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

