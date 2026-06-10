<?php
/* @var $this FinanceRequestsController */
/* @var $model FinanceRequests */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'finance-requests-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'User_Id'); ?>
		<?php echo $form->textField($model,'User_Id'); ?>
		<?php echo $form->error($model,'User_Id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'Amount'); ?>
		<?php echo $form->textField($model,'Amount',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'Amount'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'Purpose'); ?>
		<?php echo $form->textArea($model,'Purpose',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'Purpose'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'Status'); ?>
		<?php echo $form->textField($model,'Status'); ?>
		<?php echo $form->error($model,'Status'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'Approved_By'); ?>
		<?php echo $form->textField($model,'Approved_By'); ?>
		<?php echo $form->error($model,'Approved_By'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'Requested_On'); ?>
		<?php echo $form->textField($model,'Requested_On'); ?>
		<?php echo $form->error($model,'Requested_On'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'Approved_On'); ?>
		<?php echo $form->textField($model,'Approved_On'); ?>
		<?php echo $form->error($model,'Approved_On'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->