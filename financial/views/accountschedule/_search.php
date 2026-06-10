<?php
/* @var $this AccountscheduleController */
/* @var $model Accountschedule */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'id'); ?>
		<?php echo $form->textField($model,'id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>50,'maxlength'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'master_id'); ?>
		<?php echo $form->textField($model,'master_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'sub_id'); ?>
		<?php echo $form->textField($model,'sub_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'sortorder'); ?>
		<?php echo $form->textField($model,'sortorder'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->