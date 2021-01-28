<?php
/** @var \common\ext\file\widgets\UploadFile $this */
/** @var boolean $labelDisable не отображать наименование атрибута. По умолчанию (FALSE) отображать. */
use common\components\helpers\HArray as A;

$labelDisable=true;

echo \CHtml::openTag($this->tag, $this->tagOptions);
	?><div class="panel-heading">
		<?= $form->labelEx($model, $b->attribute); ?>
	</div>
	<div class="panel-body">
		<? $this->render('upload_image', compact('b', 'form', 'model', 'labelDisable')); ?>
		<?= A::get($this->params, 'bottom_text'); ?>
	</div><?
echo \CHtml::closeTag($this->tag);
?>
