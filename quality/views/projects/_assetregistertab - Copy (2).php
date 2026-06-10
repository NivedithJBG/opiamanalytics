
<div class="panel panel-default asset-register-tab tab tab-wrapper acco-fourteen">
    <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/operations/assetregistertab.js" type="text/javascript"></script>
	<!-- <input type="radio" class="AssetregisterTab" id="rd5" name="rd"> -->
	<div class="panel-heading" >
	  <h4 class="panel-title" id="assetregistrtab">
		<a data-toggle="collapse" data-parent="#accordionoperre" href="#collapse_asset">
		<span class="icon-archive1"></span>Asset Register</a>
	  </h4>
	</div>
	<div id="collapse_asset" class="tab-content cOrder-body panel-collapse collapse">
	  <div class="panel-body ">
		<div class="search-and-content-wrpr">
			<div class="content-wrpr">
	        <a href="#" type="hidden" id="assetreg_search"></a>	
				<!-- form starts here -->	
				<!-- form ends here -->	
				<!-- edit form starts here -->
				<!-- edit form ends here -->
				<!-- list start here -->
				<div class="asset-register-list-wrpr data-content-list">
	                <div class="regstrpreloader" style="display: none;" align="center">
	                    <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
	                </div>
	                <div id="assetregister_items"></div>
				</div>
				<!-- list end here -->
			</div>
		</div>
	  </div>
	</div>
</div>