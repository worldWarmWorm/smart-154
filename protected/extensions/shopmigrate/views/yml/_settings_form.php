<?php
/** @var \CController $this */
/** @var \CActiveForm $form */
/** @var \ext\shopmigrate\models\YmlMigrateForm $model */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HFile;

$t=Y::ct('\ext\shopmigrate\Messages.actions/ymlmigrate');
?>
<div class="form">
	<?php $form=$this->beginWidget('\CActiveForm', [
		'id'=>'yml-settings-form',
		'enableClientValidation'=>true,
		'clientOptions'=>[
			'validateOnSubmit'=>true,
			'validateOnChange'=>false
		],
	]); ?> 
		
		<?= $form->errorSummary($model); ?>
		
		<div class="row">
			Папка импорта: <strong><?= \Yii::getPathOfAlias('webroot.uploads.import'); ?></strong>
			<br/>			
			Размер папки импорта: <strong><?= HFile::getDirSize(\Yii::getPathOfAlias('webroot.uploads.import'), true); ?></strong>
			<br/>			
			<?= \CHtml::submitButton($t('btn.clearImportDirs'), ['class'=>'btn btn-primary', 'name'=>'run-clearImportDirs', 'onclick'=>"return confirm('Подтвердите удаление файлов')"]); ?>
			<?= \CHtml::submitButton($t('btn.clearImport'), ['class'=>'btn btn-warning', 'name'=>'run-clearImport', 'onclick'=>"return confirm('Подтвердите удаление файлов')"]); ?>
		</div>
		
		<div class="row">
			Папка экспорта: <strong><?= \Yii::getPathOfAlias('webroot.uploads.export'); ?></strong>
			<br/>			
			Размер папки экспорта: <strong><?= HFile::getDirSize(\Yii::getPathOfAlias('webroot.uploads.export'), true); ?></strong>
			<br/>
			<?= \CHtml::submitButton($t('btn.clearExport'), ['class'=>'btn btn-primary', 'name'=>'run-clearExport', 'onclick'=>"return confirm('Подтвердите удаление файлов')"]); ?>
		</div>
		
		<div class="row">
			<br/><strong>Дополнительно:</strong>			
			<br/>
			<br/>
			<?= \CHtml::submitButton($t('btn.clearCatalog'), ['class'=>'btn btn-danger', 'name'=>'run-clearCatalog', 'onclick'=>'return confirm(\''.$t('btn.clearCatalog.confirm').'\')']); ?>
		</div>
		
	<? $this->endWidget(); ?>
</div>