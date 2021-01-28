<?php
/** @var \CController $this */
/** @var \CActiveForm $form */
/** @var \ext\shopmigrate\models\YmlMigrateForm $model */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HFile;

$t=Y::ct('\ext\shopmigrate\Messages.actions/ymlmigrate');

$maxSize=[
	HFile::formatSize(HFile::getSizeBytes(ini_get('post_max_size')), 0),
	HFile::formatSize(HFile::getSizeBytes(ini_get('upload_max_filesize')), 0)
];

?>
<div class="form">
	<?php $form=$this->beginWidget('\CActiveForm', [
		'id'=>'yml-import-form',
		'enableClientValidation'=>true,
		'clientOptions'=>[
			'validateOnSubmit'=>true,
			'validateOnChange'=>false,
			'afterValidate'=>'js:function(form,data,hasError){if(!hasError){$("[name=\'run-import\']").button("loading");return true;}}'
		],
		'htmlOptions'=>['enctype'=>'multipart/form-data'],
	]); ?> 
		
		<?php 
			$errorSummaryHeader=null;
			if($model->hasErrors('importErrors')) {
				$errorSummaryHeader='<strong>При обновлении каталога возникли следующие ошибки:</strong>';
			}
		?>
		<?= $form->errorSummary($model, $errorSummaryHeader); ?>
		
		<?= $form->hiddenField($model, 'local_filename'); ?>
		
		<?php $this->widget('\common\widgets\form\FileField', A::m(compact('form', 'model'), [
			'attribute'=>'filename',
			'note'=>""
		])); ?>
		<div class="alert alert-info">
			Файл не будет загружен, если его размер превысит одно из заданных значений в конфигурации:<br/>
			<b>post_max_size:</b>&nbsp;<?= $maxSize[0]; ?><br/>
			<b>upload_max_filesize:</b>&nbsp;<?= $maxSize[1]; ?><br/>
		</div>
		
		<?php $this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
			'attribute'=>'price_coefficient',
			'note'=>'Дробные числа задаются через точку',
			'htmlOptions'=>['class'=>'w10 inline form-control']
		])); ?>
		
		<?php $this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), [
			'attribute'=>'replace',
			'note'=>$t('import.replace.note'),
			'noteOptions'=>['class'=>'alert alert-warning'],
			'tagOptions'=>['class'=>'row panel-body']
		])); ?> 
		
		<?= \CHtml::submitButton($t('btn.import'), [
			'class'=>'btn btn-primary', 
			'name'=>'run-import',
			'data-loading-text'=>$t('btn.import.loading')
		]); ?>
	
	<? $this->endWidget(); ?>
	
	<?php $importFiles=HFile::readDir(\Yii::getPathOfAlias('webroot.uploads.import'), false, function($dirname, $entry) {
		$filename=HFile::path([$dirname, $entry]);
		$ext=pathinfo($filename, PATHINFO_EXTENSION);
		if(is_file($filename) && (($ext == 'zip') || ($ext == 'xml'))) {
			return [$entry, $filename];
		}
	}); ?>	
	<?php if($importFiles): ?>
		<?php Y::js(false, ';$(document).on("click", ".js-btn-runimport-localfile", function(e){
if(confirm("Подтвердите наполенение каталога")) {
	$("#ext_shopmigrate_models_YmlImportForm_local_filename").val($(e.target).data("file"));
	$("#yml-import-form").submit();
}
});'); ?>
		<br/>
		<table class="table table-striped">
			<tr>
				<th colspan="3">
					<?= $t('import.filelist.title'); ?> (/uploads/import/)
					<div class="alert alert-info" style="font-weight:normal;margin:0;padding:3px">При импорте будут учтены заданные параметры: "Коэффициент изменения цены" и "Перезаписать каталог"</div>
				</th>
			</tr>
			<tr>
				<th>Название файла</th>
				<th class="col-sm-2">Размер</th>
				<th class="col-sm-2">&nbsp;</th>
			</tr>
			<?php foreach($importFiles as $filename): ?>
				<tr>
					<td><?=\CHtml::link($filename[0], '/uploads/import/'.$filename[0]); ?></td>
					<td class="col-sm-2"><?= HFile::formatSize(filesize($filename[1])); ?></td>
					<td class="col-sm-2"><?= CHtml::button('Импортировать', [
						'class'=>'btn btn-sm btn-primary js-btn-runimport-localfile',
						'data-file'=>$filename[0]
					]); ?></td>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php endif; ?>
</div>