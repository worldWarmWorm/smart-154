<?php
/** @var $this SaleController */
/** @var $model Sale */
/** @var $form CActiveForm */
$ta=\YiiHelper::createT('AdminModule.admin');
?>
<div class="row">
  <?=$form->checkBox($model, 'active'); ?>
  <?=$form->labelEx($model, 'active', array('class'=>'inline')); ?>
  <?=$form->error($model, 'active'); ?>
</div>
<div class="row">
  <?=$form->labelEx($model,'title'); ?>
  <?=$form->textField($model,'title', array('size'=>60,'maxlength'=>255, 'class'=>'form-control')); ?>
  <?=$form->error($model,'title'); ?>
</div>

<div class="row">
  <?$this->widget('admin.widget.Alias.AliasFieldWidget', compact('form', 'model'))?>
</div>

<?if(!$model->isNewRecord):?>
  <div class="row">
  	<?$this->widget('\ext\D\image\widgets\UploadImage', array(
  		'behavior'=>$model->imageBehavior, 
  		'form'=>$form,
  		'ajaxUrlDelete'=>$this->createAbsoluteUrl('deletePreview', array('id'=>$model->id))
  	))?>
  </div>
<?endif?>

<div class="row">
  <?=$form->labelEx($model,'preview_text')?>
  <?=$form->textArea($model,'preview_text', array('class'=>'form-control'))?>
  <?=$form->error($model,'preview_text')?>
</div>

<div class="row">
  <?=$form->labelEx($model, 'detail_text')?>
  <?$this->widget('admin.widget.EditWidget.TinyMCE', array(
    'model'=>$model,
    'attribute'=>'detail_text',
	'htmlOptions'=>array('class'=>'big')
  ));?>
  <?=$form->error($model, 'detail_text'); ?>
</div>

<?if(!$model->isNewRecord):?>
  <?$this->widget('admin.widget.ajaxUploader.ajaxUploader', array(
    'fieldName'=>'images',
    'fieldLabel'=>$ta('label.uploadImages'),
    'model'=>$model,
    'fileType'=>'image'
  ))?>

  <?$this->widget('admin.widget.ajaxUploader.ajaxUploader', array(
    'fieldName'=>'files',
    'fieldLabel'=>$ta('label.uploadFiles'),
    'model'=>$model,
  ))?>
<?endif?>