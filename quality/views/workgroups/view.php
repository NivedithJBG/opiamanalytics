<?php
/* @var $this WorkgroupsController */
/* @var $model Workgroups */

$this->breadcrumbs=array(
	'Workgroups'=>array('index'),
	$model->Name,
);

$this->menu=array(
	array('label'=>'List Workgroups', 'url'=>array('index')),
	array('label'=>'Create Workgroups', 'url'=>array('create')),
	array('label'=>'Update Workgroups', 'url'=>array('update', 'id'=>$model->Workgroup_Id)),
	array('label'=>'Delete Workgroups', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->Workgroup_Id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Workgroups', 'url'=>array('admin')),
);
?>

<h1>View Workgroups #<?php echo $model->Workgroup_Id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'Workgroup_Id',
		'Project_Id',
		'Name',
		'Added_On',
		'Updated_On',
		'Added_By',
		'Status',
	),
)); ?>
