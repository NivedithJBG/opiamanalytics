<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/projects/over_menu_projectmaster.js?v=<?php echo time(); ?>" type="text/javascript"></script>
<?php if(Yii::$app->controller->id == 'projects' && Yii::$app->controller->action->id == 'projectmasters'): ?>
<script>
$(function(){
    if (!$('.overNow4').hasClass('active')) {
        $('.menu4-popup-cntnr').addClass('active');
        $('body').css('overflow-y','hidden');
        $('#project-title-head, #prjct_head, #procurement-title-head').html('Activity Library');
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
			<div class="menu4-rs menu4-rs-e"  data-dir="e"></div>
			<div class="menu4-rs menu4-rs-w"  data-dir="w"></div>
			<div class="menu4-rs menu4-rs-s"  data-dir="s"></div>
			<div class="menu4-rs menu4-rs-se" data-dir="se"></div>
			<div class="menu4-rs menu4-rs-sw" data-dir="sw"></div>
			<div class="menu4-drag-hdr">
				<span>&#128218; Activity Library &mdash; <span style="font-weight:400;opacity:.75;font-size:11px;">drag to move</span></span>
				<a href="#" class="menu4-win-close" style="color:#fff;font-size:18px;text-decoration:none;line-height:1;">&times;</a>
			</div>
			<div class="menu4-cntnt-wrpr">
				<div class="icon-groups type"></div>
				<div style="padding:10px 20px 6px;"><h4 style="margin:0;font-weight:700;color:#1a202c;">Activity Resource Allocation</h4></div>
				<div class="col-md-12">
					<?php //echo $this->render('_projects'); ?>
					<?php echo $this->render('_estactivity'); ?>
					<?php //echo $this->render('_resourceallocation'); ?><!-- tab copy inside _estactivity -->
				</div>
			</div>
		</div>
	</div>
</div>