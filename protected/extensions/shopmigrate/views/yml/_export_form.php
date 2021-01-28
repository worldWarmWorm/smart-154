<?php
/** @var \CController $this */
/** @var \CActiveForm $form */
/** @var \ext\shopmigrate\models\YmlMigrateForm $model */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HFile;

$t=Y::ct('\ext\shopmigrate\Messages.actions/ymlmigrate');

if(!$model->filename) {
	$model->filename=$model->generateFilename();
}
if(!$model->shop_name) {
	$model->shop_name=\D::cms('sitename');
}
if(!$model->shop_company) {
	$model->shop_company=\D::cms('firm_name');
}
?>
<div class="form">
	<?php $form=$this->beginWidget('\CActiveForm', [
		'id'=>'yml-export-form',
		'enableClientValidation'=>true,
		'clientOptions'=>[
			'validateOnSubmit'=>true,
			'validateOnChange'=>false,
			'afterValidate'=>'js:function(form,data,hasError){if(!hasError){$("[name=\'run-export\']").button("loading");return true;}}'
		],
	]); ?> 
		
		<?= $form->errorSummary($model); ?>
		
		<?php $this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
			'attribute'=>'filename',
			'note'=>$t('tab.export.attribute.filename.note'),
			'htmlOptions'=>['class'=>'form-control']
		])); ?>
		
		<?php $this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
			'attribute'=>'shop_name',
			'htmlOptions'=>['class'=>'form-control']
		])); ?>
		<?php $this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
			'attribute'=>'shop_company',
			'htmlOptions'=>['class'=>'form-control']
		])); ?>
		<?php $this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
			'attribute'=>'shop_url',
			'htmlOptions'=>['class'=>'form-control', 'placeholder'=>\Yii::app()->createAbsoluteUrl('/')]
		])); ?>
		
		<?php $this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), ['attribute'=>'as_yml'])); ?>
		
		<?= \CHtml::submitButton($t('btn.export'), [
			'class'=>'btn btn-primary', 
			'name'=>'run-export', 
			'data-loading-text'=>$t('btn.export.loading')
		]); ?>
	
	<? $this->endWidget(); ?>
	
	<?php $exportFiles=HFile::readDir(\Yii::getPathOfAlias('webroot.uploads.export'), false, function($dirname, $entry) {
		$filename=HFile::path([$dirname, $entry]);
		if(is_file($filename) && (pathinfo($filename, PATHINFO_EXTENSION) == 'zip')) {
			return [$entry, $filename];
		}
	}); ?>	
	<?php if($exportFiles): ?>
		<br/>
		<table class="table table-striped">
			<tr><th colspan="2"><?= $t('export.filelist.title'); ?></th></tr>
			<?php foreach($exportFiles as $filename): ?>
				<tr>
					<td><?=\CHtml::link($filename[0], '/uploads/export/'.$filename[0]); ?></td>
					<td><?= HFile::formatSize(filesize($filename[1])); ?></td>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php endif; ?>
</div>