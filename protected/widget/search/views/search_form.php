<? use common\components\helpers\HYii as Y; ?>
<div class="search_block">
    <form id="search" action="<?= $this->owner->createUrl('search/index') ?>" role="search">
        <div class="input-group">
		<?= \CHtml::textField(Y::config('search', 'queryname'), \Yii::app()->request->getQuery('q'), [
			'placeholder'=>$this->placeholder,
			'id'=>$this->id,
			'autocomplete'=>'off'
		]); ?>
		<?= \CHtml::submitButton($this->submit, ['encode'=>false]); ?>
		</div>
	</form>
</div>

