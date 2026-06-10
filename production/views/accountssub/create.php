<?php
/* @var $this AccountsSubController */
/* @var $model AccountsSub */

$this->breadcrumbs=array(
	'Accounts Subs'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List AccountsSub', 'url'=>array('index')),
	array('label'=>'Manage AccountsSub', 'url'=>array('admin')),
);
?>

<h1>Create AccountsSub</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>