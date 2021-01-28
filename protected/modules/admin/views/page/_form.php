<?php
/* @var PageController $this */
?>
<div class="form">
  <?php $form = $this->beginWidget('CActiveForm', array(
    'id'=>'page-form',
    'enableClientValidation'=>true,
    'clientOptions'=>array(
        'validateOnSubmit'=>true,
        'validateOnChange'=>false
    ),
    'htmlOptions' => array('enctype'=>'multipart/form-data'),
  )); ?>

  <?php 
  $tabs=array(
    'Основное'=>array('content'=>$this->renderPartial('_form_general', compact('model', 'form'), true), 'id'=>'tab-general'),
  	'Seo'=>array('content'=>$this->renderPartial('_form_seo'    , compact('model', 'form'), true), 'id'=>'tab-seo'),
  );
  if(D::role('sadmin')) {
  	$tabs['Настройки']=array('content'=>$this->renderPartial('_form_settings', compact('model', 'form'), true), 'id'=>'tab-settings');
  }
  $this->widget('zii.widgets.jui.CJuiTabs', array(
    'tabs'=>$tabs,
    'options'=>array()
  )); ?>

	<div class="row buttons">
    <div class="left">
      <?=CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', array('class'=>'btn btn-primary'))?>
      <?=CHtml::submitButton($model->isNewRecord ? 'Создать и выйти' : 'Сохранить и выйти', array('class'=>'btn btn-info', 'name'=>'saveout'))?>
      <?=CHtml::link('отмена', $model->blog_id ? array('blog/index', 'id'=>$model->blog_id) : array('page/index'), array('class'=>'btn btn-default'))?>
    </div>

    <?php if (!$model->isNewRecord && !$model->isDefault()): ?>
    <div class="right">
      <a class="btn btn-danger delete-b" href="<?php echo $this->createUrl('page/delete', array('id'=>$model->id)); ?>"
         onclick="return confirm('Вы действительно хотите удалить страницу?');"><span>Удалить страницу</span></a>
    </div>
    <?php endif; ?>

    <div class="clr"></div>
	</div>
    <?php $this->endWidget(); ?>
</div><!-- form -->
