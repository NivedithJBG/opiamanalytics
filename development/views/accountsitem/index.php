<?php
/* @var $this AccountsItemController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Accounts Items',
);

$this->menu=array(
	array('label'=>'Create AccountsItem', 'url'=>array('create')),
	array('label'=>'Manage AccountsItem', 'url'=>array('admin')),
);
?>

<h1>Accounts Items</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
