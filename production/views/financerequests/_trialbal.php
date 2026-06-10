
<div class="panel panel-default trial-balance-tab acco-nine tab">
	<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/finance/_trialbal.js" type="text/javascript"></script>
	<!-- <input type="radio" id="rd5" name="rd"> -->

	<div class="panel-heading" >
	  <h4 class="panel-title" id="trialbalnc">
		<a data-toggle="collapse" data-parent="#accordionfin" href="#collapsetrailbal">
		<span class="icon-banknote"></span>Trial Balance</a>
	  </h4>
	</div>
					
					
	<div id="collapsetrailbal" class="tab-content panel-collapse cOrder-body collapse">
	  	<div class="panel-body">
			<div class="search-and-content-wrpr">
				<div class="search-and-actions-wrpr row">
					<div class="content-search-wrpr col-md-12 col-sm-12">
						
						<input class="form-control datepickerfrom" id="trialfromdate" name="trialfromdate" type="date" placeholder="Select Date"> 

						<!-- <input class="form-control" type="date" id="trialfromdate" name="trialfromdate" value="" /> 
 -->
						<input class="form-control datepickerto"  id="trialtodate" name="trialtodate"  type="date" placeholder="Select Date">
						
                        <button id="tbresourcetypesearch" class="btn btn-primary" type="button"><span class="icon-search5"></span></button>
                    </div>
                    
				</div>
				<div class="content-wrpr">
                    <div class="preloader" id="fin-preloader-trialtab" style="display: none;" align="center">
                        <img src="<?= Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle">
                    </div>
					<div class="trial-list-wrpr" >
						
                        <div id="trial-body"></div>
					</div>
				</div>
				
			</div>
			
	  	</div>
	</div>
</div>