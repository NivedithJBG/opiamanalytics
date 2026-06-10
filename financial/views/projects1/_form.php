<?php
/* @var $this ProjectsController */
/* @var $model projects */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'projects-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'Name'); ?>
		<?php echo $form->textField($model,'Name',array('size'=>60,'maxlength'=>250)); ?>
		<?php echo $form->error($model,'Name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'Added_On'); ?>
		<?php echo $form->textField($model,'Added_On'); ?>
		<?php echo $form->error($model,'Added_On'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'Updated_On'); ?>
		<?php echo $form->textField($model,'Updated_On'); ?>
		<?php echo $form->error($model,'Updated_On'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'Added_By'); ?>
		<?php echo $form->textField($model,'Added_By'); ?>
		<?php echo $form->error($model,'Added_By'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'Status'); ?>
		<?php echo $form->textField($model,'Status'); ?>
		<?php echo $form->error($model,'Status'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->