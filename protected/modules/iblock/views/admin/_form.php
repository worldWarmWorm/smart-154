<?php
/* @var $this iblock\controllers\AdminController */
/* @var $model iblock\models\InfoBlock */
/* @var $prop_model iblock\models\InfoBlockProp */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'info-block-form',
	'enableAjaxValidation'=>false,
)); ?>


	<?php echo $form->errorSummary($model); ?>

	<?php
	$this->widget('zii.widgets.jui.CJuiTabs', [
		'tabs'=>[
			'Основые данные'=>['content'=>$this->renderPartial('iblock.views.admin.__base_form', compact('model', 'form'), true), 'id'=>'tab-main'],
			'Свойства'=>['content'=>$this->renderPartial('iblock.views.admin.__properties_form', compact('model', 'form', 'prop_model'), true), 'id'=>'tab-properties']
		],
		'options'=>[]
	]);
	?>

	<div class="row buttons">
		<div class="left">
			<?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', array('class'=>'btn btn-primary')); ?>
		</div>

		<?php if (!$model->isNewRecord): ?>
		<div class='left'>
		<a class='btn btn-danger delete-b' href="<?=$this->createUrl('/cp/iblock/delete', array("id"=>$model->id))?>"
		onclick="return confirm('Вы действительно хотите удалить запись?');">
			<span>Удалить</span></a>
		</div>
		<?php endif; ?>
		<div class="clr"></div>

	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->