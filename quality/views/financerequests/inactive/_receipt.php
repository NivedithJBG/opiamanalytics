<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/receipt.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="receipt"><a href="javascript:void(0)">5. Fund Receipt</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div class="row show-grid">
                <div class="col-md-3"><a href="<?php echo Yii::app()->request->baseUrl; ?>/FinanceRequests/fundreceipt"><button type="button" class="btn btn-success"  id="addreceipt"><span class="glyphicon glyphicon-plus-sign"></span>Add Receipt</button></a> </div>
                <div class="col-md-3"><button type="button" class="btn btn-danger" id="listreceipt"><span class="glyphicon glyphicon-list-alt"></span>List Receipt</button></div>
            </div>
            <div id="receiptlistsection">
                <div id="searchdiv" class="row show-grid" style="display: block;">
                    <div class="col-md-9">
                        <input type="text" placeholder="Search" id="searchreceipt" class="form-control">

                    </div>
                    <div class="col-md-3">
                        <button id="receiptsearch" class="btn btn-primary" type="button" ><span class="glyphicon glyphicon-search"></span>Search</button>
                    </div>
                </div>
                <div class="row show-grid">
                    <!--Table-->
                    <form>
                        <table class="table table-bordered" id="receipttable" style="display: table; overflow: hidden;">
                            <?php if(User::model()->isAdmin()): ?>
                            <thead>
                            <tr>
                                <th>Requested Date</th>
                                <th>User</th>
                                <th >Amount</th>
                                <th >Purpose</th>
                                <th >Place</th>
                                <th colspan="2"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="7" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <?php else:?>
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                <th >Purpose</th>
                                <th >Project</th>
                                <th >Status</th>
                                <th colspan="2"></th>
                            </tr>
                            <tr class="preloader" style="display: none;"><td colspan="6" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <?php endif;?>
                            <tbody id="receiptitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>