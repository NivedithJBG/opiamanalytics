<?php
/* @var $this AccountsItemController */
/* @var $model AccountsItem */

$this->breadcrumbs=array(
	'Accounts Items'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List AccountsItem', 'url'=>array('index')),
	array('label'=>'Create AccountsItem', 'url'=>array('create')),
	array('label'=>'Update AccountsItem', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete AccountsItem', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage AccountsItem', 'url'=>array('admin')),
);
?>

<h1>View AccountsItem #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'sub_id',
		'master_id',
	),
)); ?>
