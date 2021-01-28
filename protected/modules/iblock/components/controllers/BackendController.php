<?php
/**
 * Base backend controller
 *
 * Use \AdminController from dishman admin module, for integrate into "Dishman".
 * 
 */
namespace iblock\components\controllers;

class BackendController extends \AdminController
{
	/**
	 * Объект внешнего контроллера, который создал текущий.
	 * @var \CController
	 */
	public $ownerController;
	
	/**
	 * (non-PHPdoc)
	 * @see \iblock\components\controllers\Controller::filters()
	 */
	/*public function filters()
	{
		return parent::filters();
	}*/
}