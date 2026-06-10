<?php
/* @var $this WorkgroupsController */
/* @var $model Workgroups */

$this->breadcrumbs=array(
	'Workgroups'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Workgroups', 'url'=>array('index')),
	array('label'=>'Manage Workgroups', 'url'=>array('admin')),
);
?>

<h1>Create Workgroups</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>