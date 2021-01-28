<?php
/** @var \settings\modules\admin\controllers\DefaultController $this */
/** @var \settings\components\base\SettingsModel $model */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

$tbtn=Y::ct('CommonModule.btn', 'common');
?>
<div class="form">
	<? $form=$this->beginWidget('\CActiveForm', [
		'id'=>'settings-form',
		'enableClientValidation'=>true,
		'clientOptions'=>[
			'validateOnSubmit'=>true,
			'validateOnChange'=>false
		],
	]); ?>

	<?= $form->errorSummary($model); ?>
	
	<div class="row">
	</div>
	
	<div class="row buttons">
      <div class="left">
        <?= CHtml::submitButton($tbtn('save'), ['class'=>'btn btn-primary']); ?>
      </div>
      <div class="clr"></div>
    </div>

	<? $this->endWidget(); ?>
</div>