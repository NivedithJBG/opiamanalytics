
<script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/finance/over_menu_findashboard.js" type="text/javascript"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type='text/javascript' src='https://prod-apnortheast-a.online.tableau.com/javascripts/api/viz_v1.js'></script>

<div class="chart-popup-cntnrr">
	<div class="chart-contnt-wrpr">

		<a href="#" title="Close" class="icon-close chart-win-close"></a>

		<div class="row show-grid"> </div>

		<div id="finance-dashboard">
			<input type="hidden" id="listfindasboard">

			<div class='tableauPlaceholder' >
			    <object class='tableauViz' style='display:none;'>
			        <param name='host_url' value='https%3A%2F%2Fprod-apnortheast-a.online.tableau.com%2F' /> 
			        <param name='embed_code_version' value='3' /> 
			        <param name='site_root' value='&#47;t&#47;opiam' />
			        <param name='name' value='BalanceSheet2&#47;Main' />
			        <param name='tabs' value='no' />
			        <param name='toolbar' value='no' />
			        <param name='showAppBanner' value='false' />
		        </object>
	        </div>
			
		</div>
	</div>
</div>

<script type="text/javascript">

	var width = $(window).width();

	var height = $(window).height();

	$(".tableauPlaceholder").css('width', width);
	$(".tableauPlaceholder").css('height', height);

	$(".tableauViz").css('width', width);
	$(".tableauViz").css('height', height);

</script>