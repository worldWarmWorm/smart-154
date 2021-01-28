<? use common\components\helpers\HArray as A; ?>
<div class="form">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id'=>'gallery-form',
        'enableClientValidation'=>true,
        'clientOptions'=>array(
            'validateOnSubmit'=>true,
            'validateOnChange'=>false
        ),
    )); ?>
    
    <? $this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), ['attribute'=>'published'])); ?>

    <div class="row">
        <?php echo $form->labelEx($model, 'title'); ?>
        <div class="form-group">
          <?php echo $form->textField($model, 'title', array('class'=>'form-control')); ?>
        </div>
        
        <?php echo $form->error($model, 'title'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'description'); ?>
        <?php 
            $this->widget('admin.widget.EditWidget.TinyMCE', array(
                'model'=>$model,
                'attribute'=>'description',
                'full'=>false,
                'htmlOptions'=>array('class'=>'big')
            )); 
        ?>
        <?php echo $form->error($model, 'description'); ?>
    </div>

    <div class="row buttons">
        <div class="left">
		    <?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', array('class'=>'btn btn-success')); ?>
            <?php echo CHtml::link('Отмена', array('gallery/index'), array('class'=>'btn btn-warning')); ?>
        </div>
        <?php if (!$model->isNewRecord): ?>
        <div class="right with-default-button">
            <a href="<?php echo $this->createUrl('deleteAlbum', array('id'=>$model->id)); ?>"
               onclick="return confirm('Вы действительно хотите удалить альбом?')">Удалить альбом</a>
        </div>
        <?php endif; ?>
        <div class="clr"></div>
	</div>

    <?php $this->endWidget(); ?>
</div><!-- form -->
