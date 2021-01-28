<div class="row">
    <?php echo $form->labelEx($model,'title'); ?>
    <?php echo $form->textField($model,'title', array('class'=>'form-control')); ?>
    <?php echo $form->error($model,'title'); ?>
</div>

<?php if ($model->blog_id): ?>
<div class="row">
    <?php echo $form->labelEx($model, 'blog_id'); ?>
    <?php echo CHtml::textField('blog_name', $model->blog->title, array('readonly'=>'readonly'))?>
    <?php echo $form->hiddenField($model, 'blog_id'); ?>
    <?php echo $form->error($model, 'blog_id'); ?>
</div>
<?php endif; ?>

<div class="row">
   <?$this->widget('admin.widget.Alias.AliasFieldWidget', compact('form', 'model'))?>
</div>

    <div class="row">
        <?php echo $form->labelEx($model, 'text'); ?>
        <?php 
            $this->widget('admin.widget.EditWidget.TinyMCE', array(
                'model'=>$model,
                'attribute'=>'text',
                'htmlOptions'=>array('class'=>'big')
            )); 
        ?>
        <?php echo $form->error($model, 'text'); ?>
    </div>
    
    <?php $this->widget('admin.widget.Accordion.AccordionList'); ?>

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
