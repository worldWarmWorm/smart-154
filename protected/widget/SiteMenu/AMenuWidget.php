<?php 
/**
 * Use aMenu jQuery plugin
 * @link http://plugins.jquery.com/jquery-amenu/
 * 
 * @author BorisDrevetsky
 *
 * @FIXME For Page Model.
 */
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'SiteMenuWidget.php');

class AMenuWidget extends SiteMenuWidget
{
	public $id='amenu-list';
	/**
	 * AMenu plugin options
	 * @var array
	 */	
	public $options = array('speed'=>100, 'animation'=>'none'); // animation: show, fade, slide, wind, none
	/**
	 * Max count visible root items. 
	 * Zero value for unlimit.
	 * @var int
	 */
	public $rootLimit=7;

	public function run()
	{
		$pages = Page::model()->getItems();
		 
		if(!$pages) return false;
		
		$this->order($pages);
		
		$this->_publishAssets();
		
		$menu = $this->getMenuItems($pages);
		
		$this->render('amenu', compact('menu'));
	}
	
	/**
	 * Echo menu HTML blocks.
	 * @param unknown $items
	 */
	public function block(&$items, $isRoot=false)
	{
		$i=0;
		foreach($items as $item) 
		{			
			if($isRoot && $this->rootLimit) 
				if($i++ >=$this->rootLimit) 
					break;
				
			echo '<li>';
			echo CHtml::link($item['params']['title'], $item['params']['url']);
			if($item['items']) {
				echo '<ul>';
				$this->block($item['items']);
				echo '</ul>';
			}
			echo '</li>';
		}
	}
	
	/**
	 * Publish assets.
	 */
	private function _publishAssets()
	{
		$assets = dirname(__FILE__).'/assets';
		$baseUrl = Yii::app()->assetManager->publish($assets);
	
		$cs=Yii::app()->getClientScript();
		$cs->registerScriptFile("{$baseUrl}/js/amenu.js", CClientScript::POS_HEAD);
// 		$cs->registerScript('AMenuWidget', 
// 			'$(document).ready(function(){ $("#'.$this->id.'").amenu('.CJSON::encode($this->options).'); });');
			
		$cs->registerCssFile("{$baseUrl}/css/amenu.css");
	}
	
	private function order(&$pages)
	{
		$menu = Menu::model()->findAll();
		if(!$menu) return false;
		
		$_pages = array();
		foreach($pages as $page) {
			$_pages[(int)$page->id] = $page;
		}
		
		$order = array();
		foreach($menu as $item) {
			if(is_array($item->options) 
				&& isset($item->options['id']) 
				&& isset($item->options['model']) 
				&& ($item->options['model'] == 'page')) 
			{
				$order[(int)$item->options['id']] = (int)$item->ordering;
			}
		}		
		asort($order);
		
		$pages=array();		
		foreach($order as $id=>$ordering) 
			$pages[] = $_pages[$id];
	}
}