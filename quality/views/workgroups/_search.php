<?php
/* @var $this WorkgroupsController */
/* @var $model Workgroups */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'Workgroup_Id'); ?>
		<?php echo $form->textField($model,'Workgroup_Id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'Project_Id'); ?>
		<?php echo $form->textField($model,'Project_Id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'Name'); ?>
		<?php echo $form->textField($model,'Name',array('size'=>60,'maxlength'=>250)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'Added_On'); ?>
		<?php echo $form->textField($model,'Added_On'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'Updated_On'); ?>
		<?php echo $form->textField($model,'Updated_On'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'Added_By'); ?>
		<?php echo $form->textField($model,'Added_By'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'Status'); ?>
		<?php echo $form->textField($model,'Status'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->