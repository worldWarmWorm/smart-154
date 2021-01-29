<div class="row">
	<?= $form->labelEx($model, 'data'); ?>
	<? $this->widget('\common\ext\dataAttribute\widgets\DataAttribute', [
		'behavior' => $model->dataAttributeBehavior,
		'header'=>$model->offerHeaders,
		'default' =>[
			['title'=>'', 'hex'=>''],
		]
	]); ?>
</div>