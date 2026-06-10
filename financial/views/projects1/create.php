<?php
/* @var $this ProjectsController */
/* @var $model projects */

$this->breadcrumbs=array(
	'Projects'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List projects', 'url'=>array('index')),
	array('label'=>'Manage projects', 'url'=>array('admin')),
);
?>

<h1>Create projects</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>