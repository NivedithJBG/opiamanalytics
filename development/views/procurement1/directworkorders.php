<div class="panel panel-default acco-seven tab">
	
	<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/procurement/directworkorders.js" type="text/javascript">
    </script>

	<div class="panel-heading">
      <h4 class="panel-title " id="choosedirectwrkorder">
        <a data-toggle="collapse" data-parent="#accordionindex" href="#collapsedirectwrk">
        <span class="icon-shopping_cart"></span>Direct Work Orders</a>
      </h4>
    </div>

    <div id="collapsedirectwrk" class="tab-content panel-collapse cOrder-body panel-collapse collapse">
        <div class="panel-body">    
            <input type="hidden" id="directwrksearch">
            <form id="directworkform">
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
                        <tbody id="directwrkorderitems">

                        </tbody>
                    </table>
                </div>
            
            </form>

            <div id="directwrkorders"></div>
   
        </div>
    </div>

</div>