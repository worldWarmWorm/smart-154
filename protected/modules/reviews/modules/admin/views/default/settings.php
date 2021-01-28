<?php
/** @var \reviews\modules\admin\controllers\DefaultController $this */
/** @var \reviews\models\Settings $model */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

$t=Y::ct('\reviews\modules\admin\AdminModule.controllers/default');
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
	return $this->renderPartial($this->viewPathPrefix.$view, compact('model', 'form'), true);
};
?>

<?
$this->widget('zii.widgets.jui.CJuiTabs', [
    'tabs'=>[
	    'Основное'=>['content'=>$fGetTabContent('_form_settings_general'), 'id'=>'tab-general'],
		'Seo'=>['content'=>$fGetTabContent('_form_settings_seo'), 'id'=>'tab-seo']
    ],
    'options'=>[]
]); 
?>


<div class="row buttons">
    <div class="left">
      <?=CHtml::submitButton($tbtn('save'), ['class'=>'btn btn-primary'])?>
      <?=CHtml::submitButton($tbtn('saveAndExit'), ['class'=>'btn btn-info', 'name'=>'saveout'])?>
      <?=CHtml::link($tbtn('cancel'), ['reviews/index'], ['class'=>'btn btn-default'])?>
    </div>
    
    <div class="clr"></div>
</div>

<? $this->endWidget(); ?>

</div>
