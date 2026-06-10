
<input type="hidden" id="selectedSectorid">
<input type="hidden" id="selectedwbsid">
<input type="hidden" id="selectedsectoriowid">
<div class="container-fluid procu-accordion">
	<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/finance/over_menu_financemaster.js" type="text/javascript"></script>
	<div class="row">
		<div class="finmenu-popup-cntnr">
			<div class="finmenu-cntnt-wrpr">
				<div class="icon-groups type"> 
					<!-- <a href="#" title="Close" class="btn btn-primary text-button menu-win-close">&#10006; Close</a> -->
				</div>
				<!--<div style="text-align: center;"><b><h4>Finance Master</h4></b></div>-->
				<div class="col-md-12">
					<div class="panel-group acco-one-active" id="accordionfinmaster">

					<?php echo $this->render('_accounttypes'); ?>
					<?php echo $this->render('_accountgroups'); ?>
					<?php echo $this->render('_accountsubgroups'); ?>
					<?php echo $this->render('_accounts'); ?>
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
