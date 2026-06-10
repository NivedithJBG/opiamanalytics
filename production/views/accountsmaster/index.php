<?php
/* @var $this AccountsmasterController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Accountsmasters',
);

$this->menu=array(
	array('label'=>'Create Accountsmaster', 'url'=>array('create')),
	array('label'=>'Manage Accountsmaster', 'url'=>array('admin')),
);
?>

<h1>Accountsmasters</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
