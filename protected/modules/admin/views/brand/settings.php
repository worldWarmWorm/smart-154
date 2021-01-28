<?php
/** @var BrandController $this */
/** @var BrandSettings $model */
use common\components\helpers\HYii as Y;

$tbtn=Y::ct('CommonModule.btn', 'common');
?>

<div class="form">

<?
$form = $this->beginWidget('CActiveForm', [
	'id'=>'review-settings-form',
	'enableClientValidation'=>true,
	'clientOptions'=>[
		'validateOnSubmit'=>true,
		'validateOnChange'=>false
	],
	'htmlOptions' => ['enctype'=>'multipart/form-data'],
]);

$fGetTabContent=function($view) use ($model, $form) {
	return $this->renderPartial($view, compact('model', 'form'), true);
};
?>

<?
$this->widget('zii.widgets.jui.CJuiTabs', [
    'tabs'=>[
	    'Основное'=>['content'=>$fGetTabContent('_form_settings_general'), 'id'=>'tab-general'],
		'Главная страница [Seo]'=>['content'=>$fGetTabContent('_form_settings_seo'), 'id'=>'tab-seo']
    ],
    'options'=>[]
]); 
?>


<div class="row buttons">
    <div class="left">
      <?=CHtml::submitButton($tbtn('save'), ['class'=>'btn btn-primary'])?>
      <?=CHtml::submitButton($tbtn('saveAndExit'), ['class'=>'btn btn-info', 'name'=>'saveout'])?>
      <?=CHtml::link($tbtn('cancel'), ['brand/index'], ['class'=>'btn btn-default'])?>
    </div>
    
    <div class="clr"></div>
</div>

<? $this->endWidget(); ?>

</div>
