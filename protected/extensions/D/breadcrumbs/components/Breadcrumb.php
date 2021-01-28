<?php
namespace ext\D\breadcrumbs\components;

class Breadcrumb 
{
	private $_breadcrumbs=array();
	
	/**
	 * 
	 * @param unknown $title
	 * @param string $url
	 * @param unknown $htmlOptions 
	 * дополнительные параметры 
	 * 	'linkOptions' - дополнительные аттрибуты для ссылки array(attribute=>value) 
	 */
	public function add($title, $url=null, $htmlOptions=array())
	{
		$this->_breadcrumbs[]=array('title'=>$title, 'url'=>$url, 'htmlOptions'=>$htmlOptions);
	}
	
	public function addByNestedSet($model, $url=null, $attributeId='id', $attributeTitle='title', $htmlOptions=array())
	{
		$parents=$model->ancestors()->findAll(array('select'=>"{$attributeId}, {$attributeTitle}"));
		if($parents) {
			foreach($parents as $parent)
				$this->add($parent->$attributeTitle, ($url ? array($url, 'id'=>$parent->$attributeId) : null), $htmlOptions);
		}
	}
	
	public function addByCmsMenu($model, $htmlOptions=array(), $lastOnlyText=false)
	{
		$id=(is_object($model) && $model->id) ? $model->id : null;
		
		$options='{"model":"'.strtolower(get_class($model)).'"';
		if($id) $options.=',"id":"'.$id.'"';
		$options.='}';
		
		if($item=\Menu::model()->findByAttributes(array('options'=>$options))) {
			$items=\Menu::model()->findAll(array('index'=>'id'));

			$fixeded=preg_replace('/[^\d,]+/', '', \D::cms('treemenu_fixed_id'));
            if(!empty($fixeded)) $fixeded=explode(',', $fixeded);
			else $fixeded=[];

			$descentants=array();
			$fAdd=function($id) use (&$descentants, &$items, &$htmlOptions, &$fAdd, $fixeded) {
				if(!$id || !isset($items[$id]) || in_array($id, $fixeded)) return;
				$descentants[]=array($items[$id]->title, \CmsMenuHelper::siteRoute($items[$id]), $htmlOptions);
				$fAdd($items[$id]->parent_id);
			};
			$fAdd($item->id);
			
			$count=count($descentants);
			$i=0;
			foreach(array_reverse($descentants) as $d) {
				$url=($lastOnlyText && (++$i==$count)) ? null : $d[1];
				$this->add($d[0], $url, $d[2]);
			}
		}
	}
	
	public function get()
	{
		return $this->_breadcrumbs;
	}
}
