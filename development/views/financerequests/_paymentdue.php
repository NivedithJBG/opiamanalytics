<div class="panel panel-default work-order-tab tab tab-wrapper acco-six">
	<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/finance/_paymentdue.js" type="text/javascript"></script>

	<div class="panel-heading" >
      <h4 class="panel-title" id="viewpaymentdue">
        <a  data-toggle="collapse" data-parent="#accordionfin" href="#collapsepay">
        <span class="icon-dollar1"></span>Payment Due</a>
      </h4>
    </div>

    <div id="collapsepay" class="tab-content cOrder-body panel-collapse collapse">

    	<div class="panel-body">
    		<div class="search-and-content-wrpr">

    			<a href="#" type="hidden" id="paymentsearch"></a>

    			<div class="content-wrpr" style="overflow: hidden;">

    				<div class="work-orderbill-list-wrpr">
    						
    						
                        <div class="preloader" style="display: none;" align="center">
                            <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                        </div>
                        <div id="paymentdues" style="padding: 25px;"></div>

                    </div>
    			</div>

    		</div>
    	</div>

    </div>


</div>