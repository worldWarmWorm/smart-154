<?php
/**
 * Abstract class for SiteMenu widgets.
 */
abstract class SiteMenuWidget extends CWidget
{
	/**
	 * Item like as (parent_id=>array(id_child1, id_child2, ...))
	 * @var array
	 */
	private $_childs = array();
	/**
	 * Array of items. Array item like as (id=>$item)
	 * $item must have attributtes "id", "title" (and "alias" for $this->getUrl()). 
	 * @var array
	 */
	private $_items = array();

	/**
	 * Menu items.
	 * @var array
	 */
	private $_menu = array();
	
	/**
	 * Get menu items.
	 * @param array $items 	Array of item. Item must have attributtes "id", "title" 
	 * 						(and "alias" for $this->getUrl()). 
	 * @return multitype:
	 */
	public function getMenuItems($items)
	{
		$this->_init($items);
		return $this->_menu;
	}
	
	/**
	 * Get url for menu item
	 * @param int $id Id item.
	 */
	protected function getUrl($id)
	{
		return Yii::app()->urlManager->createUrl("/{$this->_items[$id]->alias}");
	}
	
	/**
	 * Initialization menu.
	 * 
	 * @param array $items
	 */
	private function _init($items)
	{
		$rootItems = $this->_parse($items);
		$this->_createMenu($this->_menu, $rootItems);
	}	
	
	/**
	 * @param array $items Item must have attribute "id". 
	 * 
	 * @return array $rootItems
	 */
	private function _parse($items)
	{
		if(!$items) return array();
		
		$rootItems = array();		
		foreach($items as $item) {
			$this->_items[$item->id] = $item;
			
			if(!isset($this->_childs[$item->id])) 
				$this->_childs[$item->id] = array();
				
			if($item->parent_id) 
				$this->_childs[$item->parent_id][] = $item->id;
			else  
				$rootItems[] = $item->id;
		}
		
		return $rootItems;
	}
	
	/**
	 * 
	 * @param pointer $rootItem Pointer to $this->_tree item.
	 * @param array $items Like as $this->_items.
	 */
	private function _createMenu(&$rootItem, &$items) 
	{
		foreach($items as $id) {
			$rootItem[$id] = array('params' => array(
				'id' => $this->_items[$id]->id,
				'title' => $this->_items[$id]->title,
				'url' => $this->getUrl($id)
			), 'items'=>array());
			 
			if(count($this->_childs[$id])) $this->_createMenu($rootItem[$id]['items'], $this->_childs[$id]);			  
		}
	}
} 