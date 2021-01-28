<?php
namespace ext\shopmigrate\actions;

use common\components\helpers\HYii as Y;
use common\components\helpers\HModel;

class YmlMigrateAction extends \CAction
{
	/**
	 * 
	 * @var array конфигурация.
	 * @see \ext\shopmigrate\models\YmlMigrateForm::process():$config
	 */
	public $ymlProcessConfig=[];
	
	/**
	 * 
	 * @var string
	 */
	public $view='ext.shopmigrate.views.yml.migrate';
	
	/**
	 * {@inheritDoc}
	 * @see CAction::run()
	 */
	public function run()
	{
		
		$modelExport=HModel::massiveAssignment('\ext\shopmigrate\models\YmlExportForm', true);
		$modelImport=HModel::massiveAssignment('\ext\shopmigrate\models\YmlImportForm', true);
		$modelSettings=HModel::massiveAssignment('\ext\shopmigrate\models\SettingsForm', true);
		
		if(isset($_POST['run-export'])) { 
			set_time_limit(0);
			$modelExport->process($this->ymlProcessConfig);
			if(!$modelExport->hasErrors()) {
				Y::cache()->flush();
				$this->controller->refresh();
			}
		}
		elseif(isset($_POST['run-import'])) {
			set_time_limit(0);
			if($modelImport->validate()) {
				if($modelImport->process($this->ymlProcessConfig)) {
					$t=Y::ct('\ext\shopmigrate\Messages.actions/ymlmigrate');
					Y::setFlash(Y::FLASH_SYSTEM_SUCCESS, $t('success.imported'));
					if(!$modelImport->hasErrors()) {
						Y::cache()->flush();
						$this->controller->refresh();
					}
				}
			}
			$modelImport->local_filename=false;
		}
		elseif(isset($_POST['run-clearImport']) || isset($_POST['run-clearImportDirs'])) {
			set_time_limit(0);
			$this->ymlProcessConfig['onlyTmpDirs']=isset($_POST['run-clearImportDirs']);
			if($removed=$modelSettings->clearImportDir($this->ymlProcessConfig)) {
				$t=Y::ct('\ext\shopmigrate\Messages.actions/ymlmigrate');
				Y::setFlash(Y::FLASH_SYSTEM_SUCCESS, $t('success.importDirCleared', [
					'{filelist}'=>array_reduce($removed, function($out, $filename) { return "{$out}<br/>{$filename}"; })
				]));
			}
			$this->controller->refresh();
		}
		elseif(isset($_POST['run-clearExport'])) {
			set_time_limit(0);
			if($removed=$modelSettings->clearExportDir($this->ymlProcessConfig)) {
				$t=Y::ct('\ext\shopmigrate\Messages.actions/ymlmigrate');
				Y::setFlash(Y::FLASH_SYSTEM_SUCCESS, $t('success.exportDirCleared', [
					'{filelist}'=>array_reduce($removed, function($out, $filename) { return "{$out}<br/>{$filename}"; })
				]));
			}
			$this->controller->refresh();
		}
		elseif(isset($_POST['run-clearCatalog'])) {
			set_time_limit(0);
			$modelImport::clearCatalog();
			$t=Y::ct('\ext\shopmigrate\Messages.actions/ymlmigrate');
			Y::setFlash(Y::FLASH_SYSTEM_SUCCESS, $t('success.catalogCleared'));
			$this->controller->refresh();
		}
		
		$this->controller->render($this->view, compact('modelExport', 'modelImport', 'modelSettings'));
	}
}