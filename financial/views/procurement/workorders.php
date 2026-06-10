<div class="panel panel-default acco-six tab">
	<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/procurement/workorders.js" type="text/javascript">
    </script>

	<div class="panel-heading">
      <h4 class="panel-title " id="choosewrkorder">
        <a data-toggle="collapse" data-parent="#accordionindex" href="#collapsewrk">
        <span class="icon-shopping_cart"></span>Work Orders</a>
      </h4>
    </div>

    <div id="collapsewrk" class="tab-content panel-collapse cOrder-body panel-collapse collapse">
        <div class="panel-body">    
            <input type="hidden" id="wrksearch">
            <form id="workform">
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
                        <tbody id="wrkorderitems">

                        </tbody>
                    </table>
                </div>
            
            </form>

            <div id="wrkorders"></div>
   
        </div>
    </div>

</div>