<?php
/**
 * Модуль "Отзывы"
 *
 */
use common\components\base\WebModule;
use common\components\helpers\HYii as Y;

class ReviewsModule extends WebModule
{
	/**
	 * (non-PHPdoc)
	 * @see CModule::init()
	 */
	public function init()
	{
		// import the module-level models and components
		$this->setImport(array(
			'reviews.models.*',
			'reviews.components.*',
		));
	}
}
