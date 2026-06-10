<?php
/* @var $this FinanceRequestsController */
/* @var $model FinanceRequests */

$this->breadcrumbs=array(
	'Finance Requests'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List FinanceRequests', 'url'=>array('index')),
	array('label'=>'Manage FinanceRequests', 'url'=>array('admin')),
);
?>

<h1>Create FinanceRequests</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>