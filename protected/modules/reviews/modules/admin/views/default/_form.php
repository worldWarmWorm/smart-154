<?php
/** @var \reviews\modules\admin\controllers\DefaultController $this */
/** @var \reviews\models\Review $model */
use common\components\helpers\HYii as Y;

$tpd=Y::ct('\reviews\modules\admin\AdminModule.controllers/default');
$tbtn=Y::ct('CommonModule.btn', 'common');
?>

<div class="form">

<?
$form = $this->beginWidget('CActiveForm', [
	'id'=>'review-form',
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

$tabs=[
   	'Основное'=>['content'=>$fGetTabContent('_form_general'), 'id'=>'tab-general'],
	'Seo'=>['content'=>$fGetTabContent('_form_seo'), 'id'=>'tab-seo']
];

// if(D::role('sadmin')) {
// 	$tabs['Настройки']=['content'=>$fGetTabContent('_form_settings'), 'id'=>'tab-settings'];
// }

echo $form->errorSummary($model);

$this->widget('zii.widgets.jui.CJuiTabs', [
    'tabs'=>$tabs,
    'options'=>[]
]); 
?>

<div class="row buttons">
    <div class="left">
      <?=CHtml::submitButton($model->isNewRecord ? $tbtn('create') : $tbtn('save'), ['class'=>'btn btn-primary'])?>
      <?=CHtml::submitButton($model->isNewRecord ? $tbtn('createAndExit') : $tbtn('saveAndExit'), ['class'=>'btn btn-info', 'name'=>'saveout'])?>
      <?=CHtml::link($tbtn('cancel'), ['reviews/index'], ['class'=>'btn btn-default'])?>
    </div>

    <? if(!$model->isNewRecord): ?>
    <div class="right">
      <a class="btn btn-danger delete-b" href="<?= $this->createUrl('reviews/delete', ['id'=>$model->id]); ?>"
         onclick="return confirm('<?=$tpd('page.confirm.remove')?>');"><span><?=$tbtn('remove')?></span></a>
    </div>
    <? endif; ?>

    <div class="clr"></div>
</div>

<? $this->endWidget(); ?>

</div>
