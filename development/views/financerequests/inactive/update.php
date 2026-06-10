<?php
/* @var $this FinanceRequestsController */
/* @var $model FinanceRequests */

$this->breadcrumbs=array(
	'Finance Requests'=>array('index'),
	$model->Id=>array('view','id'=>$model->Id),
	'Update',
);

$this->menu=array(
	array('label'=>'List FinanceRequests', 'url'=>array('index')),
	array('label'=>'Create FinanceRequests', 'url'=>array('create')),
	array('label'=>'View FinanceRequests', 'url'=>array('view', 'id'=>$model->Id)),
	array('label'=>'Manage FinanceRequests', 'url'=>array('admin')),
);
?>

<h1>Update FinanceRequests <?php echo $model->Id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>