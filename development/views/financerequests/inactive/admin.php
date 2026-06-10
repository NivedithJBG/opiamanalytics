<?php
/* @var $this FinanceRequestsController */
/* @var $model FinanceRequests */

$this->breadcrumbs=array(
	'Finance Requests'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List FinanceRequests', 'url'=>array('index')),
	array('label'=>'Create FinanceRequests', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#finance-requests-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Finance Requests</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'finance-requests-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'Id',
		'User_Id',
		'Amount',
		'Purpose',
		'Status',
		'Approved_By',
		/*
		'Requested_On',
		'Approved_On',
		*/
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
