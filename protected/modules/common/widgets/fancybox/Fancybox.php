<?php
/**
 * Виджет подключения fancybox
 */
namespace common\widgets\fancybox;

use common\components\helpers\HYii as Y;

class Fancybox extends \CWidget
{
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::init()
	 */
	public function init()
	{
		self::publish();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::run()
	 */
	public function run()
	{		
	}
	
	/**
	 * Публикация ресурсов
	 */
	public static function publish()
	{
		// @FIXME hardcode скрипты подключаются из коревной папки,
		// чтобы на данный момент избежать повторного подключения.
		Y::jsFile('/js/fancybox/jquery.fancybox.pack.js');
		Y::css('/js/fancybox/jquery.fancybox.css');
	} 
}