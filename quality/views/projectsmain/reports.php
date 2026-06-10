
<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projectsmain/over_menu_projectreport.js" type="text/javascript"></script>
<div class="container-fluid procu-accordion">
	<div class="row">
		<div class="menu1-popup-cntnr">
			<div class="menu1-cntnt-wrpr">
				<div class="icon-groups type"> 
					<!-- <a href="#" title="Close" class="btn btn-primary text-button menu-win-close">&#10006; Close</a> -->
				</div>
				<!--<div style="text-align: center;"><b><h4>Project Report</h4></b></div>-->
				<div class="col-md-12">
					<div class="panel-group acco-one-active" id="accordionproreports">

					<?php echo $this->render('_projectsReport'); ?>
                    <?php echo $this->render('_projectsResourceReport'); ?>
                    <?php echo $this->render('auditlog'); ?>
                    <!-- <?php //echo $this->render('_projectsScheduleReport'); ?> -->
					</div>
				</div>
			</div>
		</div>
	</div>
</div>