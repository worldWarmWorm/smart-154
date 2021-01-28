<?php
/**
 * Базовый класс для виджетов корзины
 * 
 * @use \AssetHelper
 */
namespace DCart\widgets;

abstract class BaseCartWidget extends \CWidget
{
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::init()
	 */
	public function init()
	{
		\AssetHelper::publish(array(
			'path' => __DIR__ . DIRECTORY_SEPARATOR . 'assets',
			'js' => array(
				'js/classes/DCart.js',
				'js/phpjs/json_decode.js'
			),
			'css' => 'css/style.css'
		));
	}	
} 