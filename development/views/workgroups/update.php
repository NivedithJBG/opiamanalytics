<?php
/* @var $this WorkgroupsController */
/* @var $model Workgroups */

$this->breadcrumbs=array(
	'Workgroups'=>array('index'),
	$model->Name=>array('view','id'=>$model->Workgroup_Id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Workgroups', 'url'=>array('index')),
	array('label'=>'Create Workgroups', 'url'=>array('create')),
	array('label'=>'View Workgroups', 'url'=>array('view', 'id'=>$model->Workgroup_Id)),
	array('label'=>'Manage Workgroups', 'url'=>array('admin')),
);
?>

<h1>Update Workgroups <?php echo $model->Workgroup_Id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>