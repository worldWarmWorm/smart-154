<?php
/**
 * Шаблон отображения формы
 * 
 * @var \common\widgets\form\ActiveForm $this
 */
use common\components\helpers\HArray as A;

if($this->tag) echo \CHtml::openTag($this->tag, $this->tagOptions);

/** @var \CActiveForm $form */
$form=$this->owner->beginWidget('\CActiveForm', A::m([
    'id'=>$this->getFormId(),
    'enableClientValidation'=>true,
    'clientOptions'=>[
        'validateOnSubmit'=>true,
        'validateOnChange'=>false
    ],
    'htmlOptions'=>$this->getFormHtmlOptions()
], $this->formOptions));

    if($this->errorSummary) {
		echo $form->errorSummary($this->model, $this->errorSummaryHeader, $this->errorSummaryFooter, $this->errorSummaryOptions);
    }
    
    if($this->attributeFormId) {
        echo \CHtml::hiddenField($this->attributeFormId, $this->getFormId());
    }
    
    if(!empty($this->fieldsets)) {
        foreach($this->fieldsets as $fieldset) {
            if($render=A::get($fieldset, 'render')) {
                if(!is_string($render) && is_callable($render)) {
                    call_user_func_array($render, [$this, $form, $fieldset]);
                }
            }
            else {
                echo \CHtml::openTag('fieldset', A::get($fieldset, 'htmlOptions', []));
                if($legend=A::get($fieldset, 'legend')) {
                    echo \CHtml::tag('legend', A::get($fieldset, 'legendOptions', []), $legend);
                }
                $attributes=A::get($fieldset, 'attributes', []);
                if(!is_string($attributes) && is_callable($attributes)) {
                    call_user_func_array($attributes, [$this, $form, $fieldset]);
                }
                else {
                    foreach($attributes as $attribute) {
                        $this->renderAttribute($form, $attribute);
                    }
                }
                echo \CHtml::closeTag('fieldset');
            }
        }
    }
    else {
        foreach($this->attributes as $attribute) {        
            $this->renderAttribute($form, $attribute);        
        }
    }
    
    if($this->privacyLabel) {
        if(!is_string($this->privacyLabel) && is_callable($this->privacyLabel)) {
            call_user_func_array($this->privacyLabel, [$this, $form]);
        }
        else {
            $this->renderRowOpenTag(['data-privacy'=>'1']);
                $privacyNameID=[];
                \CHtml::resolveNameID($this->model, $this->privacyAttribute, $privacyNameID);
                echo $form->checkBox($this->model, $this->privacyAttribute, A::get($this->getAttributeOptions($this->privacyAttribute), 'input', []));
                echo \CHtml::label($this->privacyLabel, $privacyNameID['id'], A::get($this->getAttributeOptions($this->privacyAttribute), 'label', []));
                $this->renderAttributeError($form, $this->privacyAttribute);
            $this->renderRowCloseTag();
        }
    }
    
    if($this->submitLabel) {
        $this->renderSubmitOpenTag();
            if(is_callable($this->submitLabel)) {
                call_user_func_array($this->submitLabel, [$this, $form]);
            }
            else {
                echo \CHtml::submitButton($this->submitLabel, $this->submitOptions);
            }
        $this->renderSubmitCloseTag();
    }
$this->owner->endWidget();

if($this->tag) echo \CHtml::closeTag($this->tag);
