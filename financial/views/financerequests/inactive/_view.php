<?php
/* @var $this FinanceRequestsController */
/* @var $data FinanceRequests */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('Id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->Id), array('view', 'id'=>$data->Id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('User_Id')); ?>:</b>
	<?php echo CHtml::encode($data->User_Id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Amount')); ?>:</b>
	<?php echo CHtml::encode($data->Amount); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Purpose')); ?>:</b>
	<?php echo CHtml::encode($data->Purpose); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Status')); ?>:</b>
	<?php echo CHtml::encode($data->Status); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Approved_By')); ?>:</b>
	<?php echo CHtml::encode($data->Approved_By); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Requested_On')); ?>:</b>
	<?php echo CHtml::encode($data->Requested_On); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('Approved_On')); ?>:</b>
	<?php echo CHtml::encode($data->Approved_On); ?>
	<br />

	*/ ?>

</div>