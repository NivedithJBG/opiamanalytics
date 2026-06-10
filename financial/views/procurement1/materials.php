<?php
/*use app\models\Vendors;  
use app\models\Brand;  
use app\models\AccountsItem;  
use app\models\AccountsSub;*/
?>


<div class="panel panel-default materials-tab tab acco-two">
	<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/procurement/materials.js" type="text/javascript"></script>
  	<!-- <input type="radio" id="rd5" class="res-tab" name="rd"> -->
			
	<div class="panel-heading" >
		  <h4 class="panel-title acc_trigger"  id="Materials">
			<a data-toggle="collapse" data-parent="#accordionmaster" href="#collapsematerial">
				<span class="icon-cart"></span>Materials
			</a>
		  </h4>
	</div>
			
	<div id="collapsematerial" class="tab-content cOrder-body panel-collapse collapse" id="res-tab-show">
	  	<div class="panel-body">				  
		  	<div id="materialslistsections" >
				<div class="search-and-content-wrpr">
                    <div class="search-and-actions-wrpr row" style="padding-bottom:0px;">
						<div class="row" style="width:100%;">
	                    	
	                    	<ul class="nav nav-tabs text-center topsbars">
								<li class="frstcl"><a data-toggle="pill" href="#resourcesMaterial" class="resourceTypeTab" id="resourcesMaterial" data-resource-type="16"><span class="icon-shopping_cart"></span> Materials</a></li> 
								
								<li><a data-toggle="pill" href="#resourcesPurInp" class="resourceTypeTab" id="resourcesPurInp" data-resource-type="21"><span class="icon-shopping_cart"></span> Purchase Inputs</a></li>
								
								<li><a data-toggle="pill" href="#resourcesConsumable" class="resourceTypeTab" id="resourcesConsumable" data-resource-type="20"><span class="icon-shopping_cart"></span> Consumables</a></li>
							</ul>

						</div>
						<div class="row" style="width:100%;">
							<div  class="resource-list" id="resource-list"></div>
							<div  id="order-view"></div>
						</div>
					</div>

					


				</div>
			</div>
				  
	  	</div>
	</div>
</div>
