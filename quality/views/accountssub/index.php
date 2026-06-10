<?php
/* @var $this AccountsSubController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Accounts Subs',
);

$this->menu=array(
	array('label'=>'Create AccountsSub', 'url'=>array('create')),
	array('label'=>'Manage AccountsSub', 'url'=>array('admin')),
);
?>

<h1>Accounts Subs</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
