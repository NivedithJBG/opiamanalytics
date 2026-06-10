<?php
/* @var $this ProjectsController */
/* @var $model projects */

$this->breadcrumbs=array(
	'Projects'=>array('index'),
	$model->Name=>array('view','id'=>$model->Project_Id),
	'Update',
);

$this->menu=array(
	array('label'=>'List projects', 'url'=>array('index')),
	array('label'=>'Create projects', 'url'=>array('create')),
	array('label'=>'View projects', 'url'=>array('view', 'id'=>$model->Project_Id)),
	array('label'=>'Manage projects', 'url'=>array('admin')),
);
?>

<h1>Update projects <?php echo $model->Project_Id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>