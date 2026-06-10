
<div class="container-fluid procu-accordion">
	<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/finance/over_menu_financereport.js" type="text/javascript"></script>
	<div class="row">
		<div class="menu1-popup-cntnr">
			<div class="menu1-cntnt-wrpr">
				<div class="icon-groups type"> 
					<!-- <a href="#" title="Close" class="btn btn-primary text-button menu-win-close">&#10006; Close</a> -->
				</div>
				<!--<div style="text-align: center;"><b><h4>Finance Master</h4></b></div>-->
				<div class="col-md-12">
					<div class="panel-group acco-one-active" id="accordionfinreports">

					<?php echo $this->render('proexpenditure'); ?>
					<?php echo $this->render('_balancesheet'); ?>

					</div>
				</div>
			</div>
		</div>
	</div>

	<style>
        .tab-content{
            max-height:unset;
        }

    </style>
</div>
