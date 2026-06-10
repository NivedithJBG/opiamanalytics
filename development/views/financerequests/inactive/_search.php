<?php
/* @var $this FinanceRequestsController */
/* @var $model FinanceRequests */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'Id'); ?>
		<?php echo $form->textField($model,'Id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'User_Id'); ?>
		<?php echo $form->textField($model,'User_Id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'Amount'); ?>
		<?php echo $form->textField($model,'Amount',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'Purpose'); ?>
		<?php echo $form->textArea($model,'Purpose',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'Status'); ?>
		<?php echo $form->textField($model,'Status'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'Approved_By'); ?>
		<?php echo $form->textField($model,'Approved_By'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'Requested_On'); ?>
		<?php echo $form->textField($model,'Requested_On'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'Approved_On'); ?>
		<?php echo $form->textField($model,'Approved_On'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->