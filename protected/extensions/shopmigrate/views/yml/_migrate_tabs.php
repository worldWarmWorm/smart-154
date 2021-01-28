<?php
/** @var \CController $this */
/** @var \ext\shopmigrate\models\YmlExportForm $modelExport */
/** @var \ext\shopmigrate\models\YmlImportForm $modelImport */
/** @var \ext\shopmigrate\models\SettingsForm $modelSettings */
use common\components\helpers\HYii as Y;

$t=Y::ct('\ext\shopmigrate\Messages.actions/ymlmigrate');
 
$this->widget('zii.widgets.jui.CJuiTabs', [
	'tabs'=>[
		$t('tab.export.title') => [
			'id'=>'tab-export',
			'content'=>$this->renderPartial('ext.shopmigrate.views.yml._export_form', ['model'=>$modelExport, 'form'=>$form], true) 
		],
		$t('tab.import.title') => [
			'id'=>'tab-import',
			'content'=>$this->renderPartial('ext.shopmigrate.views.yml._import_form', ['model'=>$modelImport, 'form'=>$form], true)
		],
		$t('tab.settings.title') => [
			'id'=>'tab-settings',
			'content'=>$this->renderPartial('ext.shopmigrate.views.yml._settings_form', ['model'=>$modelSettings, 'form'=>$form], true)
		]
	],
	'options'=>[
		'active'=>isset($_POST['run-import']) ? 1 : 0	
	]
]);
?>