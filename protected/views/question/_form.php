<?php
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

Y::js(
    'questionform', 
    '$("#add-question a").click(function(){$.fancybox.open({src:"#question-form-div",scrolling:"no",titleShow:false,onComplete:function(a,b,c){$("#fancybox-wrap").addClass("formBox");}});});'
    . 'function submitForm(form, hasError){if(!hasError){$.post($(form).attr("action"),$(form).serialize(),function(data){'
    . 'if(data=="ok"){$("#question-form-div > div").html("<h2>' . Yii::t('app', 'Your question has been submitted.') . '</h2>");}'
    . 'else{$("#question-form-div > div").html("<h2>' . Yii::t('app', 'An error occurred while sending the question') . '</h2>");}'
    . '});}}',
    \CClientScript::POS_READY
);
?>

<div id="question-form-div" class="form">
    <div>
        <h2><?= Yii::t('app', 'Ask a question') ?></h2>

        <?php $form=$this->beginWidget('CActiveForm', array(
            'id'=>'question-form',
            'enableClientValidation'=>true,
            'clientOptions'=>array(
                'validateOnSubmit'=>true,
                'validateOnChange'=>false,
                'afterValidate'=>'js: function(form, data, hasError) {submitForm(form, hasError);}'
            )
        )); ?>

        <div class="row">
            <?php echo $form->labelEx($model,'username'); ?>
            <?php echo $form->textField($model,'username',array('maxlength'=>255)); ?>
            <?php echo $form->error($model,'username'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model,'question'); ?>
            <?php echo $form->textArea($model,'question',array('maxlength'=>255)); ?>
            <?php echo $form->error($model,'question'); ?>
        </div>
        <div class="row">
            <?php echo $form->checkBox($model,'privacy_policy',array('class'=>'inpt inpt-privacy_policy')); ?>
            <?php echo $form->labelEx($model,'privacy_policy'); ?>
            <div style="display:none"><?php echo $form->error($model,'privacy_policy'); ?></div>
        </div>

        <div class="row buttons">
            <?php echo CHtml::submitButton(Yii::t('app', 'Send'), ['class' => 'btn']); ?>
        </div>
        <?php $this->endWidget(); ?>
    </div>
</div><!-- form -->
