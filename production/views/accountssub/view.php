<?php
/* @var $this AccountsSubController */
/* @var $model AccountsSub */

$this->breadcrumbs=array(
	'Accounts Subs'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List AccountsSub', 'url'=>array('index')),
	array('label'=>'Create AccountsSub', 'url'=>array('create')),
	array('label'=>'Update AccountsSub', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete AccountsSub', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage AccountsSub', 'url'=>array('admin')),
);
?>

<h1>View AccountsSub #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'master_id',
	),
)); ?>
