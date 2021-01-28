<?php
use common\components\helpers\HYii as Y;

class ProductFilter extends \CWidget
{
	/**
	 * @var array data of HFilter::fetchResult()
	 */
	public $data;
	
	public $category=null;
	
	private function _publishAssets()
	{
		$assets = dirname(__FILE__).'/assets';
		$baseUrl = Yii::app()->assetManager->publish($assets);
	
		$cs=Yii::app()->getClientScript();
	
		$cs->registerScriptFile("{$baseUrl}/js/attr_filter.js", CClientScript::POS_HEAD);
		$cs->registerScriptFile("{$baseUrl}/js/jquery-ui.min.js", CClientScript::POS_HEAD);
		$cs->registerCssFile("{$baseUrl}/css/jquery-ui.css");
		# $cs->registerCssFile("http://code.jquery.com/ui/1.10.3/themes/south-street/jquery-ui.css");
		Y::module('common')->publishJs('js/kontur/common/tools/form2object.js');	
	}
	
	public function run()
	{
		$this->_publishAssets();
		$this->render('product_filter');
	}
}