<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projects/over_menu_projectmaster.js?v=<?php echo time(); ?>" type="text/javascript"></script>
<?php if(Yii::$app->controller->id == 'projects' && Yii::$app->controller->action->id == 'projectmasters'): ?>
<script>
$(function(){
    if (!$('.overNow4').hasClass('active')) {
        $('.menu4-popup-cntnr').addClass('active');
        $('body').css('overflow-y','hidden');
        $('#project-title-head, #prjct_head, #procurement-title-head').html('Activity Resource Allocation');
        $('.overNow4').addClass('active');
    }
});
</script>
<?php endif; ?>
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
				<div style="padding:10px 20px 6px;"><h4 style="margin:0;font-weight:700;color:#1a202c;">Activity Library <small style="font-size:13px;font-weight:400;color:#888;">— choose to add</small></h4></div>
				<div class="col-md-12">
					<?php //echo $this->render('_projects'); ?>
					<?php echo $this->render('_estactivity'); ?>
					<?php //echo $this->render('_resourceallocation'); ?><!-- tab copy inside _estactivity -->
				</div>
			</div>
		</div>
	</div>
</div>