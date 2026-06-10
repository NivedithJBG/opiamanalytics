
<div class="panel panel-default balance-sheet-tab acco-ten tab">
	<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/finance/_balance_sheet.js" type="text/javascript"></script>
	<!-- <input type="radio" id="rd5" name="rd"> -->

	<div class="panel-heading" >
	  <h4 class="panel-title" id="balance_sheet">
		<a data-toggle="collapse" data-parent="#accordionfin" href="#collapsebalsheet">
		<span class="icon-banknote"></span>Balance Sheet</a>
	  </h4>
	</div>
					
					
	<div id="collapsebalsheet" class="tab-content panel-collapse cOrder-body collapse">
	  	<div class="panel-body">
			<div class="search-and-content-wrpr">
				<div class="search-and-actions-wrpr row">
					<div class="content-search-wrpr col-md-12 col-sm-12">
						
						<input class="form-control datepickerfrom" id="bsfromdate" name="bsfromdate" type="date" placeholder="Select Date"> 

						<!-- <input class="form-control" type="date" id="bsfromdate" name="bsfromdate" value="" /> 
 -->
						<input class="form-control datepickerto"  id="bstodate" name="bstodate"  type="date" placeholder="Select Date">
						
                        <button id="bsresourcetypesearch" class="btn btn-primary" type="button"><span class="icon-search5"></span></button>
                    </div>
                    
				</div>
				<div class="content-wrpr">
                    <div class="preloader" id="fin-preloader-bstab" style="display: none;" align="center">
                        <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                    </div>
					<div class="bs-list-wrpr" >
						
                        <div id="bs-body"></div>
					</div>
				</div>
				
			</div>
			
	  	</div>
	</div>
</div>