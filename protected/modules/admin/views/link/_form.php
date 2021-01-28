<div class="form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id'=>'link-form',
        'enableClientValidation'=>true,
        'clientOptions'=>array(
            'validateOnSubmit'=>true,
            'validateOnChange'=>false
        ),
    )); ?>

    <div class="row">
        <?php echo $form->labelEx($model, 'title'); ?>
        <?php echo $form->textField($model, 'title', array('class'=>'form-control')); ?>
        <?php echo $form->error($model, 'title'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'url'); ?>
        <?php echo $form->textField($model, 'url', array('class'=>'form-control')); ?>
        <?php echo $form->error($model, 'url'); ?>
    </div>

    <div class="row buttons">
        <div class="left">
		    <?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', array('class'=>'btn btn-primary')); ?>
            <?php echo CHtml::link('Отмена', array('page/index'), array('class'=>'btn btn-default'))?>
        </div>
        <?php if (!$model->isNewRecord): ?>
        <div class="right with-default-button">
            <a href="<?php echo $this->createUrl('link/delete', array('id'=>$model->id)); ?>"
               onclick="return confirm('Вы действительно хотите удалить ссылку?')">Удалить ссылку</a>
        </div>
        <?php endif; ?>
        <div class="clr"></div>
	</div>

    <?php $this->endWidget(); ?>
</div><!-- form -->
