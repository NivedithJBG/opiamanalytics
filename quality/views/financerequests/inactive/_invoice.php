<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/invoice.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="invoice"><a href="javascript:void(0)">2. Invoices</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <div id="invoicelist">
                <input type="hidden" id="invoicesearch">
                <div class="row show-grid">
                    <form method="POST" action="<?php echo Yii::app()->request->baseUrl; ?>/FinanceRequests/Createjournals" id="crinvoiceform">
                        <table class="table table-bordered" id="invoicetable">
                            <thead>
                            <!--<tr>
                                <th colspan="9"><span style="float: left;font-weight: bold;padding: 10px;width: 100%;text-align: center">Cart</span></th>
                            </tr>-->
                            <tr>
                                <th>Date</th>
                                <th>Project</th>
                                <th>Order Type</th>
                                <th>Invoice No</th>
                                <th>Vendor Name</th>
                                <th>Amount</th>
                                <th colspan="3">
                                    <button type="submit" class="btn btn-primary createjournal">Create Journal</button>
                                    <input type="hidden" id="crordertype" name="crordertype">
                                    <input type="hidden" id="crvendor" name="crvendor">
                                    <input type="hidden" id="crinvoices" name="crinvoices">
                                    <input type="hidden" id="crproject" name="crproject">
                                    <input type="hidden" id="crproject" name="crorders">
                                </th>
                            </tr>
                            <tr class="preloader"><td colspan="8" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                            </thead>
                            <tbody  id="invoiceitems">

                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>