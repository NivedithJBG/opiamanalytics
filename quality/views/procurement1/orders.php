<div class="panel panel-default /*acco-confirmorders*/ acco-five tab">
    <!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> -->
    <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/procurement/orders.js" type="text/javascript">
      </script>
    <!--<input type="radio" id="rd1" class="confirmorder_tab" name="rd">-->
    <div class="panel-heading">
      <h4 class="panel-title " id="confirm-Orders">
        <a data-toggle="collapse" data-parent="#accordionindex" href="#collapseconfirm">
        <span class="icon-checkmark4"></span>Confirm Orders</a>
      </h4>
    </div>
    <div id="collapseconfirm" class="tab-content panel-collapse cOrder-body panel-collapse collapse">
        <div class="panel-body">
            <div class="acc_containerssss">
                <div class="block">
                    <div class="jumbotrons">
                        <div id="orderitemslistsection">
                            <div class="row" id="cnfrmhistory">
                                <div class="col-md-10 topbar">

                                <ul class="nav nav-tabs text-center">
			
                               
                                <li class="purrr"><a data-toggle="pill" href="#cfpoopord" id="cfmppurchor"><span class="icon-shopping_cart"></span> Purchase Orders</a></li>
                                <li><a data-toggle="pill" href="#cfwrord" id="cfmpworko"><span class="icon-shopping_cart"></span> Work Orders</a></li>
                                <li><a data-toggle="pill" href="#cfdirrord" id="cfmpdirec"><span class="icon-shopping_cart"></span> Direct Work Orders</a></li>
                                <li><a data-toggle="pill" href="#cflesord" id="cfmpleaso"><span class="icon-shopping_cart"></span> Lease Orders</a></li>
                                <li><a data-toggle="pill" href="#cfdesord" id="cfmpdespto"><span class="icon-shopping_cart"></span> P&M Movement</a></li>
                            </ul>
                                </div>
                                <div class="col-md-2 text-right" id="cnfrmhistorys">
                                    <button type="button" class="btn btn-primary historycnfrm" id="historycnfrm" title="History"><span></span> History</button>
                                </div>
                            </div>
                            


                            <input type="hidden" id="ordersearch">
                            <input type="hidden" id="identifycf">
                             <input type="hidden" id="ordertype" name="ordertype">
                             <form method="GET" action=""  id="orderform">

                            <div class="confirmlists" id="new">
                                <table class="table table-bordered confrm-table" id="orderitemstable">
                                    <thead>
                                        <tr>
                                            <th style="width: 87px;"></th>
                                            <th style="width: 270px;">Date</th>
                                            <th>Order Type</th>
                                            <th>Vendor Name</th> 
                                            <th>Amount</th>
                                            <th colspan="3"></th>
                                        </tr>
                                    <tr class="preloader" style="display: none;"><td colspan="8" align="center"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                                    </thead>
                                    <tbody  id="orderitems">

                                    </tbody>
                                </table>

                                <table class="table table-bordered confrm-table" id="orderitemstablecf" style="display: none;">
                                    <thead>
                                        <tr>
                                            <th style="width: 87px;"></th>
                                            <th style="width: 270px;">Date</th>
                                            <th>Order Type</th>
                                            <th>Vendor Name</th> 
                                           <!--  <th>Amount</th> -->
                                            <th colspan="3"></th>
                                        </tr>
                                    <tr class="preloader" style="display: none;"><td colspan="8" align="center"><img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"> </td></tr>
                                    </thead>
                                    <tbody  id="orderitemscf">

                                    </tbody>
                                </table>



                            </div>


                      </form>
                            <div id="historiescnfrm" style="display: none;">
                                <div class="row">
                                <div class="col-md-6"></div>
                                <div class="col-md-6 text-right" id="cnfrmback" style="padding-bottom: 10px;">
                                    <button type="button" class="btn btn-primary cnfrmback" id="cnfrmback">Back</button>
                                </div></div>
                                <table class="table table-bordered confrmhstry-table" id="confrmhstrytable" style="display: none;">
                                    <thead>
                                        <tr>
                                            <th style="width: 87px;"></th>
                                            <th style="width: 270px;">Date</th>
                                            <th>Order Type</th>
                                            <th>Vendor Name</th>
                                            <th>Amount</th>
                                            
                                        </tr>
                                        </thead>
                                        <tbody  id="cnfrmorderitems">

                                    </tbody>
                                </table>

                                  <table class="table table-bordered confrmhstry-table" id="confrmhstrytablecf" style="display: none;">
                                    <thead>
                                        <tr>
                                            <th style="width: 87px;"></th>
                                            <th style="width: 270px;">Date</th>
                                            <th>Order Type</th>
                                            <th>Vendor Name</th>
                                           <!--  <th>Amount</th> -->
                                            
                                        </tr>
                                        </thead>
                                        <tbody  id="cnfrmorderitemscf">

                                    </tbody>
                                </table>



                            </div>
                            <div id="approveworkandleaseorder"></div>
                            
                            <div id="approveotherdata"></div>





                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--<div class="approveOrder-cntnt">
        <div class="row">
            <div class="col-md-12 approveHdr">
                <h3 id="apprpoptitle">Approve Purchase Order</h3>
                <span class="icon-close"></span>
            </div>
            <iframe id="approveOrder" src="#" style="width:100%; height:520px; border:0px; " ></iframe>
            
        </div>
    
    </div>-->
    <!--<h2 class="acc_trigger" id="Orders"><a href="javascript:void(0)">4. Confirm Orders</a></h2>-->
</div> 
