<?php
/**
 * Iblock module
 * 
 * v.1.0
 */
class IblockModule extends CWebModule
{
	/**
	 * Url rules for CUrlManager
	 * @var array
	 */
	public $urlRules = array(
        /*'cp/iblock' => 'admin/iblock/index',
        'cp/iblock/create' => 'admin/iblock/create',
        'cp/iblock/update/<id:\d+>' => 'admin/iblock/update',
        'cp/iblock/delete/<id:\d+>' => 'admin/iblock/delete',
        'cp/iblockElements/index/block_id/<block_id:\d+>' => 'admin/iblockElements/index',
        'cp/iblockElements/create/<block_id:\d+>' => 'admin/iblockElements/create',
        'cp/iblockElements/update/<id:\d+>' => 'admin/iblockElements/update',
        'cp/iblockElements/delete/<id:\d+>' => 'admin/iblockElements/delete',*/




	);
	
	/**
	 * Controller map
	 * @var array
	 */
	public $controllerMap = array(
		'ajax' => '\iblock\controllers\AjaxController',
	);

	/**
	 * (non-PHPdoc)
	 * @see CModule::init()
	 */
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'iblock.components.*',
			'iblock.components.controllers.*',
			'iblock.components.types.*',
			'iblock.configs.*',
			// 'iblock.configs.forms.*',
			'iblock.models.*',
		));
	}

	/**
	 * (non-PHPdoc)
	 * @see CWebModule::beforeControllerAction()
	 */
	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			// this method is called before any module controller action is performed
			// you may place customized code here
			return true;
		}
		else
			return false;
	}
	
	public function getVersion()
	{
		return '1.00';
	}
}
