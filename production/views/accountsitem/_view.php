<?php
/* @var $this AccountsItemController */
/* @var $data AccountsItem */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::encode($data->name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('sub_id')); ?>:</b>
	<?php echo CHtml::encode($data->sub_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('master_id')); ?>:</b>
	<?php echo CHtml::encode($data->master_id); ?>
	<br />


</div>