<?php
/* @var $this AccountscheduleController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Accountschedules',
);

$this->menu=array(
	array('label'=>'Create Accountschedule', 'url'=>array('create')),
	array('label'=>'Manage Accountschedule', 'url'=>array('admin')),
);
?>

<h1>Accountschedules</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
