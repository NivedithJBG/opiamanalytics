<?php
/* @var $this AccountsmasterController */
/* @var $model Accountsmaster */

$this->breadcrumbs=array(
	'Accountsmasters'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Accountsmaster', 'url'=>array('index')),
	array('label'=>'Create Accountsmaster', 'url'=>array('create')),
	array('label'=>'View Accountsmaster', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Accountsmaster', 'url'=>array('admin')),
);
?>

<h1>Update Accountsmaster <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>