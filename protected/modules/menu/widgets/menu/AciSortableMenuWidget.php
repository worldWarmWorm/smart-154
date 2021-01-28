<?php
/**
 * Use aciSortable jQuery plugin
 * @link http://plugins.jquery.com/aciSortable/
 *
 * @author Boris Drevetsky (drevetsky@kontur-nsk.ru)
 *
 * @FIXME For Page Model.
 */
namespace menu\widgets\menu;

use \menu\components\helpers\UrlHelper;

class AciSortableMenuWidget extends BaseMenuWidget
{
	/**
	 * (non-PHPdoc)
	 * @see BaseMenuWidget::$id
	 */
	public $id = 'aci-sortable-menu';
	
	/**
	 * @var mixed массив или значение id пунктов, 
	 * которым не устанавиливать ссылку (фиксированных).
	 */
	public $fixedId = array();

	/**
	 * (non-PHPdoc)
	 * @see BaseMenuWidget::$cssClass
	 */
	public $cssClass = 'sort aci-sortable-menu-widget-list';
	
	/**
	 * aciSortable plugin options
	 * @var array
	 */
	public $options = array();//'child'=>100, 'childHolderSelector'=>'ui-state-highlight');
	
	/**
	 * @var boolean показать id элеметов меню.
	 */
	public $showId = false;

	/**
	 * (non-PHPdoc)
	 * @see \menu\widgets\BaseMenuWidget::init()
	 */
	public function init()
	{
		parent::init();
		
		// publish assets
		\AssetHelper::publish(array(
			'path' 	=> \Yii::getPathOfAlias('menu.widgets.menu.assets'),
			'js' 	=> array(
				'js/jquery/jquery.aciPlugin.min.js', 
				'js/jquery/jquery.aciSortable.js', 
				'js/AciSortableMenuWidget.js'),
			'css'	=> array('css/aciSortable.css')
		));

		if(!is_array($this->fixedId) && is_numeric($this->fixedId)) $this->fixedId = array((int)$this->fixedId);
		elseif(!is_array($this->fixedId)) $this->fixedId = explode(',', $this->fixedId);
		
		$this->fixedId = array_map(function($id) { return (int)$id; }, $this->fixedId); 
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \menu\widgets\BaseMenuWidget::run()
	 */	
	public function run()
	{
		$tree = $this->getTree();
		$menu = $this->renderItems($tree, 0, true);
		
		$this->render('aci_sortable_menu', compact('menu'));
	}

	/**
	 * (non-PHPdoc)
	 * @see \menu\widgets\BaseMenuWidget::renderItems()
	 */
	protected function renderItems(&$items, $level=0, $return=false)
	{
		$html = '<ul ';
		if(!$level) $html .= \HtmlHelper::AttributesToString(array('id'=>$this->id, 'class'=>$this->cssClass));
		$html .= '>';
		
		$i=0;
		foreach($items as $item) {
			if((!$level && $this->rootLimit) && ($i++ >=$this->rootLimit)) break;

			$html .= '<li data-item="' . $item['model']->id . '">';
			$html .= \CHtml::checkBox('visible', !(bool)$item['model']->hidden, array(
				'onclick'=>'AciSortableMenuWidget.save()', 
				'title'=>'Отображать/скрыть на сайте')
			);
			
			if($this->showId) $html .= '['.$item['model']->id.']';

			if(in_array($item['model']->id, $this->fixedId)) {
				$html .= '<span class="disabled">' . $item['model']->title . '</span>';
			}
			else {
				$html .= '<span class="dragHandle">&nbsp;&nbsp;&nbsp;&nbsp;</span>';  
				$html .= \CHtml::link($item['model']->title, UrlHelper::createUrl($item['model'], $this->adminMode), array('title'=>$item['model']->title));
			}
			
			if(!empty($item['childs'])) {
				$html .= $this->renderItems($item['childs'], ($level + 1), true);
			}
			
			$html .= '</li>';
		}
		
		$html .= '</ul>';
		
		if($return) return $html; 
		else echo $html;
	}
}