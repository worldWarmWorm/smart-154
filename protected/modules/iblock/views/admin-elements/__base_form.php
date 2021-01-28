<?php
use common\components\helpers\HArray as A;

/* @var $this iblock\controllers\AdminElementsController */
/* @var $form CActiveForm */
/* @var $model iblock\models\InfoBlockElement */
/* @var $iblock iblock\models\InfoBlock */
?>

<div class="row">
    <?php echo $form->checkBox($model, 'active', array('class' => '')); ?>
    <?php echo $form->labelEx($model, 'active', ['class' => 'inline']); ?>
    <?php echo $form->error($model, 'active'); ?>
</div>

<div class="row">
    <?php echo $form->labelEx($model, 'title'); ?>
    <?php echo $form->textField($model, 'title', array('class' => 'form-control')); ?>
    <?php echo $form->error($model, 'title'); ?>
</div>

<!--<div class="row">
    <?php /*echo $form->labelEx($model, 'code'); */?>
    <?php /*echo $form->textField($model, 'code', array('class' => 'form-control')); */?>
    <?php /*echo $form->error($model, 'code'); */?>
</div>-->

<div class="row">
    <?php echo $form->labelEx($model, 'sort'); ?>
    <?php echo $form->textField($model, 'sort', array('class' => 'form-control')); ?>
    <?php echo $form->error($model, 'sort'); ?>
</div>


<?php
if ($iblock->use_preview) {
    $this->widget('\common\ext\file\widgets\UploadFile', [
        'behavior' => $model->imageBehavior,
        'form' => $form,
        'actionDelete' => $this->createAction('removeImage'),
        'tmbWidth' => 200,
        'tmbHeight' => 200,
        'view' => 'panel_upload_image'
    ]);
}
?>

<?php if ($iblock->use_description) { ?>
<div class="row">
    <?php $this->widget('\common\widgets\form\TinyMceField', A::m(compact('form', 'model'), ['attribute' => 'description'])); ?>
    <?php echo $form->error($model, 'description'); ?>
</div>
<?php } ?>

<!--<div class="row">
    <?php /*echo $form->labelEx($model,'created_at'); */ ?>
    <?php /*echo $form->textField($model,'created_at',array('class'=>'form-control')); */ ?>
    <?php /*echo $form->error($model,'created_at'); */ ?>
</div>

<div class="row">
    <?php /*echo $form->labelEx($model,'updated_at'); */ ?>
    <?php /*echo $form->textField($model,'updated_at',array('class'=>'form-control')); */ ?>
    <?php /*echo $form->error($model,'updated_at'); */ ?>
</div>-->
