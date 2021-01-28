<?php
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

$ta=Y::ct('AdminModule.admin');
$model->privacy_policy=1;
?>
<?php echo $form->hiddenField($model, 'privacy_policy'); ?>
<? $this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), ['attribute'=>'published'])); ?>
<? //$this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), ['attribute'=>'privacy_policy'])); ?>
<? $this->widget('\common\widgets\form\DateField', A::m(compact('form', 'model'), ['attribute'=>'publish_date'])); ?>
<? $this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), ['attribute'=>'author'])); ?>
<? $this->widget('\common\widgets\form\TextAreaField', A::m(compact('form', 'model'), ['attribute'=>'preview_text'])); ?>

<?if(!$model->isNewRecord):?>
  <div class="row">
  	<?$this->widget('\ext\D\image\widgets\UploadImage', [
  		'behavior'=>$model->imageBehavior, 
  		'form'=>$form,
  		'ajaxUrlDelete'=>$this->createAbsoluteUrl('removeImage', ['id'=>$model->id])
  	])?>
  </div>
<?endif?>

<? $this->widget('\common\widgets\form\TinyMceField', A::m(compact('form', 'model'), ['attribute'=>'detail_text'])); ?>

<? $this->widget('\common\widgets\form\TinyMceField', A::m(compact('form', 'model'), [
	'attribute'=>'comment', 
	'full'=>false
])); ?>
