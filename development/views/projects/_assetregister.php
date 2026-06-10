
<div class="panel panel-default asset-register-tab tab tab-wrapper acco-seven">
	<!-- <input type="radio" class="AssetregisterTab" id="rd5" name="rd"> -->
	<div class="panel-heading" >
	  <h4 class="panel-title" id="AssetregisterTab">
		<a data-toggle="collapse" data-parent="#accordionoperre" href="#collapseasset">
		<span class="icon-archive1"></span>Asset Register</a>
	  </h4>
	</div>
	<div id="collapseasset" class="tab-content cOrder-body panel-collapse collapse">
	  <div class="panel-body ">
		<div class="search-and-content-wrpr">
			<div class="content-wrpr">
	        <a href="#" type="hidden" id="assetregsearch"></a>	
				<!-- form starts here -->	
				<!-- form ends here -->	
				<!-- edit form starts here -->
				<!-- edit form ends here -->
				<!-- list start here -->
				<div class="asset-register-list-wrpr data-content-list">
	                <div class="preloader" style="display: none;" align="center">
	                    <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
	                </div>
	                <div id="assetregisteritems"></div>
				</div>
				<!-- list end here -->
			</div>
		</div>
	  </div>
	</div>
</div>