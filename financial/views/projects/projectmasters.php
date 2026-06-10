<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projects/over_menu_projectmaster.js" type="text/javascript"></script>
<input type="hidden" id="selectedSectorid">
<input type="hidden" id="selectedwbsid">
<input type="hidden" id="selectedsectoriowid">

<div class="container-fluid procu-accordion">
	<div class="row">
		<div class="menu4-popup-cntnr">
			<div class="menu4-cntnt-wrpr">
				<div class="icon-groups type"> 
					<!-- <a href="#" title="Close" class="btn btn-primary text-button menu-win-close">&#10006; Close</a> -->
				</div>
				<!--<div style="text-align: center;"><b><h4>Project Master</h4></b></div>-->
				<div class="col-md-12">
					<div class="panel-group acco-one-active" id="accordionpromasterind">

					<?php //echo $this->render('_projects'); ?>
					<?php echo $this->render('_worktypes'); ?>
					<?php echo $this->render('_activitytypes'); ?>
					<?php echo $this->render('_estactivity'); ?>
					<?php //echo $this->render('_resourceallocation'); ?><!-- tab copy inside _estactivity -->
					</div>
				</div>
			</div>
		</div>
	</div>
</div>