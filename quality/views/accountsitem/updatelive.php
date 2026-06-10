<?php
/* @var $this AccountsItemController */
/* @var $model AccountsItem */

$this->breadcrumbs=array(
	'Accounts Items'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List AccountsItem', 'url'=>array('index')),
	array('label'=>'Create AccountsItem', 'url'=>array('create')),
	array('label'=>'View AccountsItem', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage AccountsItem', 'url'=>array('admin')),
);
?>

<h1>Update AccountsItem <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>