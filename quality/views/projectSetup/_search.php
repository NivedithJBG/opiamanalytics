<?php
/* @var $this ProductsController */
/* @var $model Products */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'Product_Id'); ?>
		<?php echo $form->textField($model,'Product_Id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'Name'); ?>
		<?php echo $form->textField($model,'Name',array('size'=>60,'maxlength'=>250)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'Unit'); ?>
		<?php echo $form->textField($model,'Unit',array('size'=>50,'maxlength'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'Price'); ?>
		<?php echo $form->textField($model,'Price'); ?>
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