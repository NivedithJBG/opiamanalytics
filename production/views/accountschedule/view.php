<?php
/* @var $this AccountscheduleController */
/* @var $model Accountschedule */

$this->breadcrumbs=array(
	'Accountschedules'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List Accountschedule', 'url'=>array('index')),
	array('label'=>'Create Accountschedule', 'url'=>array('create')),
	array('label'=>'Update Accountschedule', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Accountschedule', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Accountschedule', 'url'=>array('admin')),
);
?>

<h1>View Accountschedule #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'master_id',
		'sub_id',
		'sortorder',
	),
)); ?>
