<?php
use common\components\helpers\HArray as A;
?>
  <div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textField($model,'title', array('size'=>60,'maxlength'=>255, 'class'=>'form-control')); ?>
		<?php echo $form->error($model,'title'); ?>
	</div>

  <div class="row">
    <?$this->widget('admin.widget.Alias.AliasFieldWidget', compact('form', 'model'))?>
  </div>

  <? $this->widget('\common\widgets\form\DateField', A::m(compact('form', 'model'), [
	'attribute'=>'created',
	'options'=>['dateFormat'=>'dd/mm/yy'],
	'initDateFormat'=>'d/m/Y'
  ])); ?>

  <?php if(!$model->isNewRecord): ?>
    <div class="row">
      <?php if(strlen($model->preview)): ?>
        <div class="image_content">
          <img src="/images/event/<?=$model->preview?>" class="img-thumbnail">
          <a class="btn btn-danger delete_img" href="#">Удалить</a>
          <br>


          <label for="Event_enable_preview" class="display_module">
            <?php echo $form->checkBox($model, 'enable_preview'); ?><span> Отображать в модуле</span>
          </label>
          <?php echo $form->error($model, 'enable_preview'); ?>
        </div>
        <br>
      <?php endif ?>
      <?php echo $form->labelEx($model,'files'); ?>
      <?php echo $form->fileField($model,'files',array('class'=>'btn btn-primary','maxlength'=>255)); ?>
      <?php echo $form->error($model,'files'); ?>
    </div>
  <?php endif; ?>

  <div class="row">
    <label><?=\Yii::t('AdminModule.event', 'form.label.intro')?></label>
    <?php echo $form->textArea($model,'intro', array('class'=>'form-control')); ?>
    <?php echo $form->error($model,'intro'); ?>
  </div>

  <div class="row">
    <label><?=\Yii::t('AdminModule.event', 'form.label.text')?></label>
    <?php 
      $this->widget('admin.widget.EditWidget.TinyMCE', array(
        'model'=>$model,
        'attribute'=>'text',
        'htmlOptions'=>array('class'=>'big')
      )); 
    ?>
    <?php echo $form->error($model, 'text'); ?>
  </div>

  <?php if (!$model->isNewRecord): ?>
    <?php $this->widget('admin.widget.ajaxUploader.ajaxUploader', array(
      'fieldName'=>'images',
      'fieldLabel'=>'Загрузка фото',
      'model'=>$model,
      'fileType'=>'image'
    )); ?>

    <?php $this->widget('admin.widget.ajaxUploader.ajaxUploader', array(
      'fieldName'=>'files',
      'fieldLabel'=>'Загрузка файлов',
      'model'=>$model,
    )); ?>
  <?php endif; ?>
