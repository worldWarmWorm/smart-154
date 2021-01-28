<?php
/**
 * Ajax frontend controller
 * 
 */
namespace iblock\controllers;

use \AttributeHelper as A;
use \iblock\components\controllers\FrontController;

class AjaxController extends FrontController
{
	/**
	 * (non-PHPdoc)
	 * @see CController::behaviors()
	 */
	public function behaviors()
	{
		return array(
			'AjaxControllerBehavior' => array(
				'class'=>'\AjaxControllerBehavior',
			)
		);
	}

}
