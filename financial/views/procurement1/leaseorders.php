<div class="panel panel-default acco-eight tab">
	<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/procurement/leaseorders.js" type="text/javascript">
    </script>

    <div class="panel-heading">
      <h4 class="panel-title " id="chooseleaseorder">
        <a data-toggle="collapse" data-parent="#accordionindex" href="#collapselease">
        <span class="icon-shopping_cart"></span>Lease Orders</a>
      </h4>
    </div>

    <div id="collapselease" class="tab-content panel-collapse cOrder-body panel-collapse collapse">
        <div class="panel-body">    
            <input type="hidden" id="leasesearch">
            <form id="leaseform">
                <div class="placeorder-list">
                    <table class="table table-bordered indent-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Vendor Name</th>
                                <th>Amount</th>
                                <th colspan="2"></th>
                            </tr>
                       
                        </thead>
                        <tbody id="leaseorderitems">

                        </tbody>
                    </table>
                </div>
            
            </form>

            <div id="lsorders"></div>
   
        </div>
    </div>


</div>