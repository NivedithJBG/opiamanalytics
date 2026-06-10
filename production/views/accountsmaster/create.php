<?php
/* @var $this AccountsmasterController */
/* @var $model Accountsmaster */

$this->breadcrumbs=array(
	'Accountsmasters'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Accountsmaster', 'url'=>array('index')),
	array('label'=>'Manage Accountsmaster', 'url'=>array('admin')),
);
?>

<h1>Create Accountsmaster</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>