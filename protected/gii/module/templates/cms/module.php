<?php echo "<?php\n"; ?>
/**
 * Модуль
 */
use common\components\helpers\HYii as Y;
use common\components\base\WebModule;

class <?php echo $this->moduleClass; ?> extends WebModule
{
	/**
	 * (non-PHPdoc)
	 * @see CModule::init()
	 */
	public function init()
	{
		parent::init();
		
		// $this->assetsBaseUrl=Y::publish($this->getAssetsBasePath());

		$this->setImport(array(
			'<?=$this->moduleID?>.models.*',
			'<?=$this->moduleID?>.behaviors.*',
			'<?=$this->moduleID?>.components.*',
		));		
	}
}