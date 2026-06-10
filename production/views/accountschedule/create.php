<?php
/* @var $this AccountscheduleController */
/* @var $model Accountschedule */

$this->breadcrumbs=array(
	'Accountschedules'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Accountschedule', 'url'=>array('index')),
	array('label'=>'Manage Accountschedule', 'url'=>array('admin')),
);
?>

<h1>Create Accountschedule</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>