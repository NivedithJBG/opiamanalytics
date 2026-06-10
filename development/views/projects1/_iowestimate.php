<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/iowestimate.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="iowestimate"><a href="javascript:void (0)">7. IOW Estimate</a></h2>
<div class="acc_container">
    <div class="block">
        <div class="jumbotron">
            <form method="POST" action="" id="iowestimateform">
                <input type="hidden" id="iowestimateProject_Id" name="Project_Id">
                <input type="hidden" id="iowestimatewbsid" name="IOW_Id">
                <input type="hidden" id="listiowestimateitems">
                <table class="table table-bordered">
                    <thead>
                    <tr style="background-color: ghostwhite">
                        <td>
                            <span class="headings" id="wbsname"><b>IOW Name </b><h4></h4></span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-primary" id="saveproduct" value="1" name="Product_saveproduct">
                                Save
                            </button>
                        </td>
                    </tr>
                    </thead>
                </table>
                <table class="table table-bordered" id="iowestimatetable">
                    <thead>
                    <tr>
                        <th><b>Process</b></th>
                        <th><b>Activity</b></th>
                        <th><b>Unit</b></th>
                        <th class="small75"><b>Quantity</b></th>
                        <th class="small75"><b>Rate</b></th>
                        <th class="small75"><b>Amount</b></th>
                        <th colspan="2"></th>
                        <th></th>
                    </tr>
                    <tr class="preloaderitems">
                        <td colspan="9" align="center">
                            <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle">
                        </td>
                    </tr>
                    </thead>
                    <tbody id="iowaddedproducts" class="ui-sortable">
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</div>