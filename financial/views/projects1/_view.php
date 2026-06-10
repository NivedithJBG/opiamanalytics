<?php
/* @var $this ProjectsController */
/* @var $data projects */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('Project_Id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->Project_Id), array('view', 'id'=>$data->Project_Id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Name')); ?>:</b>
	<?php echo CHtml::encode($data->Name); ?>
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

	<b><?php echo CHtml::encode($data->getAttributeLabel('Status')); ?>:</b>
	<?php echo CHtml::encode($data->Status); ?>
	<br />


</div>