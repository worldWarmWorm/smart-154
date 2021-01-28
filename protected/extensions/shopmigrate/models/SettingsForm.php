<?php
/**
 * Модель настроек миграции каталога
 */
namespace ext\shopmigrate\models;

use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use common\components\helpers\HFile;

class SettingsForm extends \common\components\base\FormModel
{
	/**
	 * @var array конфигурация
	 * "tmpdir" string путь к временной директории. По умолчанию "webroot.uploads.export".
	 */
	private $_config = [
		'tmpdirImport'=>'webroot.uploads.import',
		'tmpdirExport'=>'webroot.uploads.export'
	];
	
	/**
	 * {@inheritDoc}
	 * @see CModel::rules()
	 */
	public function rules()
	{
		return $this->getRules([
		]);
	}
	
	/**
	 * {@inheritDoc}
	 * @see CModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		return $this->getAttributeLabels([
		]);
	}
	
	/**
	 * Очистка директории импорта каталога
	 * @param array конфигурация. Подробнее YmlExportForm::$_config.
	 */
	public function clearImportDir($config=[])
	{
		$removed=[];
		
		$this->_config=A::m($this->_config, $config);
		
		if($this->_config['onlyTmpDirs']) {
			foreach(HFile::getDirs(\Yii::getPathOfAlias($this->_config['tmpdirImport']), true) as $dirname) {
				$removed=A::m($removed, HFile::rm($dirname, true, false));
			}
		}
		else {
			$removed=HFile::rm(\Yii::getPathOfAlias($this->_config['tmpdirImport']), true, true);
		}
		
		return $removed;
	}
	
	/**
	 * Очистка директории экспорта каталога
	 * @param array конфигурация. Подробнее YmlExportForm::$_config.
	 */
	public function clearExportDir($config=[])
	{
		$this->_config=A::m($this->_config, $config);
		
		return HFile::rm(\Yii::getPathOfAlias($this->_config['tmpdirExport']), true, true);
	}	
}