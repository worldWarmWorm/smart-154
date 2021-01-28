<?php if(Yii::app()->user->hasFlash('contact')): ?>
    <div class="flash-success">
        <?php echo Yii::app()->user->getFlash('contact'); ?>
    </div>
<?php else: ?>
    <div class="form">

        <?php $form=$this->beginWidget('CActiveForm', array(
            'id'=>'contact-form',
            'enableClientValidation'=>true,
            'clientOptions'=>array(
                'validateOnSubmit'=>true,
                'validateOnChange'=>false
            ),
        )); ?>

        <div class="row">
            <?php echo $form->labelEx($model,'name'); ?>
            <?php echo $form->textField($model,'name'); ?>
            <?php echo $form->error($model,'name'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model,'email'); ?>
            <?php echo $form->textField($model,'email'); ?>
            <?php echo $form->error($model,'email'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model,'phone'); ?>
            <?php echo $form->textField($model,'phone'); ?>
            <?php echo $form->error($model,'phone'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model,'body'); ?>
            <?php echo $form->textArea($model,'body',array('rows'=>6, 'cols'=>50)); ?>
            <?php echo $form->error($model,'body'); ?>
        </div>
        
        <div class="row">
            <?php echo $form->checkBox($model,'privacy_policy',array('class'=>'inpt inpt-privacy_policy')); ?>
            <?php echo $form->labelEx($model,'privacy_policy'); ?>
            <?php echo $form->error($model,'privacy_policy'); ?>
        </div>

        <div class="row buttons">
            <?php echo CHtml::submitButton('Отправить', ['class' => 'btn']); ?>
        </div>

        <div class="note">Поля отмеченные <span class="required">*</span> обязательны для заполнения.</div>
        <?php echo $form->hiddenField($model, 'verifyCode'); ?>
        
        <script type="text/javascript">
            $(function() {
                $('#contact-form :submit').click(function(){
                    $('#ContactForm_verifyCode').val('test_ok');
                });
            });
        </script>
        <?php $this->endWidget(); ?>
    </div><!-- form -->
<?php endif; ?>
