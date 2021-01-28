<?php
/** @var $this SaleController */
/** @var $model Sale */
/** @var $form CActiveForm */
$ta=\YiiHelper::createT('AdminModule.admin');
?>
<div class="form">
  <?$form=$this->beginWidget('CActiveForm', array(
    'id'=>'sale-form',
    'enableClientValidation'=>true,
    'clientOptions'=>array(
        'validateOnSubmit'=>true,
    ),
    'htmlOptions'=>array('enctype'=>'multipart/form-data'),
  ))?>
  
    <?=$form->errorSummary($model)?>
  
	<?$this->widget('zii.widgets.jui.CJuiTabs', array(
	  'tabs'=>array(
	      $ta('tab.general')=>array('content'=>$this->renderPartial('_form_general', compact('model', 'form'), true), 'id'=>'tab-general'),
	      $ta('tab.seo')=>array('content'=>$this->renderPartial('_form_seo', compact('model', 'form'), true), 'id'=>'tab-seo'),
	  )
	));?>

	<div class="row buttons">
      <?=CHtml::submitButton($model->isNewRecord ? $ta('btn.create') : $ta('btn.save'), array('class'=>'btn btn-primary')); ?>
	  <?=CHtml::submitButton($model->isNewRecord ? 'Создать и выйти' : 'Сохранить и выйти', array('class'=>'btn btn-info', 'name'=>'saveout'))?>
      <?=CHtml::link($ta('btn.cancel'), array('index'), array('class'=>'btn btn-default')); ?>
	</div>

  <?$this->endWidget()?>
</div><!-- form -->
