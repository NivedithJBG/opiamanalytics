
<div class="panel panel-default profit-loss-tab acco-ten tab">
	<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/finance/_profit_loss.js" type="text/javascript"></script>
	<!-- <input type="radio" id="rd5" name="rd"> -->

	<div class="panel-heading" >
	  <h4 class="panel-title" id="profit_n_loss">
		<a data-toggle="collapse" data-parent="#accordionfin" href="#collapseprofloss">
		<span class="icon-banknote"></span>Profit & Loss Account</a>
	  </h4>
	</div>
					
					
	<div id="collapseprofloss" class="tab-content panel-collapse cOrder-body collapse">
	  	<div class="panel-body">
			<div class="search-and-content-wrpr">
				<div class="search-and-actions-wrpr row">
					<div class="content-search-wrpr col-md-12 col-sm-12">
						
						<input class="form-control datepickerfrom" id="plfromdate" name="plfromdate" type="date" placeholder="Select Date"> 

						<!-- <input class="form-control" type="date" id="plfromdate" name="plfromdate" value="" /> 
 -->
						<input class="form-control datepickerto"  id="pltodate" name="pltodate"  type="date" placeholder="Select Date">
						
                        <button id="plresourcetypesearch" class="btn btn-primary" type="button"><span class="icon-search5"></span></button>
                    </div>
                    
				</div>
				<div class="content-wrpr">
                    <div class="preloader" id="fin-preloader-pltab" style="display: none;" align="center">
                        <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                    </div>
					<div class="pl-list-wrpr" >
						
                        <div id="pl-body"></div>
					</div>
				</div>
				
			</div>
			
	  	</div>
	</div>
</div>