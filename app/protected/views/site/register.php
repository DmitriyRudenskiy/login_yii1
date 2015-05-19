<?php
/* @var $this SiteController */
/* @var $model Register */
/* @var $form CActiveForm */

$this->pageTitle = Yii::app()->name . ' - Регистрация';
?>

<h1>Регистрация</h1>

<?php if(Yii::app()->user->hasFlash('success')): ?>

<div class="flash-success">
	<?php echo Yii::app()->user->getFlash('success'); ?>
</div>

<?php else: ?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'contact-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email'); ?>
		<?php echo $form->error($model,'email'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Зарегистрироваться'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<?php endif; ?>