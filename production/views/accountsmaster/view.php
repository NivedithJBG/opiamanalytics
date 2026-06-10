<?php
/* @var $this AccountsmasterController */
/* @var $model Accountsmaster */

$this->breadcrumbs=array(
	'Accountsmasters'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List Accountsmaster', 'url'=>array('index')),
	array('label'=>'Create Accountsmaster', 'url'=>array('create')),
	array('label'=>'Update Accountsmaster', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Accountsmaster', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Accountsmaster', 'url'=>array('admin')),
);
?>

<h1>View Accountsmaster #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
	),
)); ?>
