<script src="<?php echo Yii::app()->request->baseUrl; ?>/jsnew/productreport.js" type="text/javascript"></script>
<h2 class="acc_trigger" id="prodreport"><a href="#">4. Production Report</a></h2>
    <div class="acc_container">
        <div class="block">
            <div class="jumbotron">
                <div class="row show-grid">
                    <div class="col-md-5" style="text-align: left;" id="projectname"></div>
                    <input type="hidden" value="" id="projid">
                </div>
                <div id="productsection" >

                    <div class="row show-grid">
                        <form>
                            <table class="table table-bordered " id="product" style="display: table;">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Unit</th>
                                    <th>Quantity</th>
                                    <th>Specific Rate</th>
                                    <th></th>
                                </tr>
                                <tr class="preloader" style="display: none;"><td colspan="6" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                                </thead>
                                <tbody id="productitems">

                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
                <div id="prodresources">
                    <div class="row show-grid">
                        <form id="productreport" method="post" action="">
                            <table class="table table-bordered" id="projproddetails">
                                <tr id="prodetails"></tr>
                            </table>
                            <table id="projproductres" style="display: table;">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Resources Consumed</th>
                                    <th>Unit</th>
                                    <th>Total Quantity</th>
                                    <th>Used Quantity</th>
                                </tr>
                                <tr class="preloader" style="display: none;"><td colspan="5" align="center"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                                </thead>
                                <tbody id="productres">

                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
