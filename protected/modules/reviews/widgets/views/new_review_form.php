<?php
/** @var \reviews\widgets\NewReviewForm $this */
/** @var \reviews\models\Review $model */
 
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

$t=Y::ct('ReviewsModule.widgets/new_review_form', 'new_review_form');
$tbtn=Y::ct('CommonModule.btn', 'common');
?>
<? if($this->popup): ?>
<span class="reviews__add-wrapper">
    <a class="btn" href="javascript:;" data-src="#fancybox-review-add-form" data-js="add-review"><?= $t('btn.add'); ?></a>
</span>
<? endif; ?>

<div style="display: none;">
	<div id="fancybox-review-add-form" class="form reviews__add-form<?= Y::c($this->popup, 'reviews__add-form-popup'); ?>">
		<h2><?= $t('form.add.title'); ?></h2>

		<? $form=$this->beginWidget('\CActiveForm', [
			'id'=>'review-add-form',
			'action'=>$this->actionUrl,
			'enableClientValidation'=>true,
			'clientOptions'=>[
				'validateOnSubmit'=>true,
				'validateOnChange'=>false,
				'afterValidate'=>'js:window.NewReviewFormWidget.submitAddForm'
			]
		]); ?>

		<? $this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), ['attribute'=>'author'])); ?>
		<? $this->widget('\common\widgets\form\TextAreaField', A::m(compact('form', 'model'), ['attribute'=>'detail_text'])); ?>
		<div class="row">
			<?php echo $form->checkBox($model,'privacy_policy',array('class'=>'inpt inpt-privacy_policy')); ?>
			<?php echo $form->labelEx($model,'privacy_policy'); ?>
			<?php echo $form->error($model,'privacy_policy'); ?>
		</div>

		<div class="row buttons" data-js="buttons">
			<?= CHtml::submitButton($tbtn('send'), ['class' => 'btn']); ?>
			<div class="error__result" data-js="result-errors"></div>
		</div>

		<? $this->endWidget(); ?>
	</div>
</div>
