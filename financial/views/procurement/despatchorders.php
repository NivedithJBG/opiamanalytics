<div class="panel panel-default acco-nine tab">
	<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/procurement/despatchorders.js" type="text/javascript">
    </script>

    <div class="panel-heading">
      <h4 class="panel-title " id="choosedesporder">
        <a data-toggle="collapse" data-parent="#accordionindex" href="#collapsedesspatch">
        <span class="icon-shopping_cart"></span>Despatch Orders</a>
      </h4>
    </div>


    <div id="collapsedesspatch" class="tab-content panel-collapse cOrder-body panel-collapse collapse">
        <div class="panel-body">    
            <input type="hidden" id="despsearch">
            <form id="despform">
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
                        <tbody id="desporderitems">

                        </tbody>
                    </table>
                </div>
            
            </form>

            <div id="despporders"></div>
   
        </div>
    </div>


</div>