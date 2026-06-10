<?php
/* @var $this ProductsController */
/* @var $data Products */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('Product_Id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->Product_Id), array('view', 'id'=>$data->Product_Id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Name')); ?>:</b>
	<?php echo CHtml::encode($data->Name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Unit')); ?>:</b>
	<?php echo CHtml::encode($data->Unit); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Price')); ?>:</b>
	<?php echo CHtml::encode($data->Price); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Added_On')); ?>:</b>
	<?php echo CHtml::encode($data->Added_On); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Updated_On')); ?>:</b>
	<?php echo CHtml::encode($data->Updated_On); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Added_By')); ?>:</b>
	<?php echo CHtml::encode($data->Added_By); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('Status')); ?>:</b>
	<?php echo CHtml::encode($data->Status); ?>
	<br />

	*/ ?>

</div>