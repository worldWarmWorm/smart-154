<?php
use YiiHelper as Y;

class MenuController extends AdminController
{
	public function filters()
	{
		return CMap::mergeArray(parent::filters(), array(
			'ajaxOnly +changeSort'
		));
	}
	
	public function actionChangeSort()
	{
		$data=json_decode(Y::request()->getPost('data', json_encode(array())));
		
		$ids=array();
		$cPId='`parent_id`=CASE `id`';
		$cOrd='`ordering`=CASE `id`';
		$fQuery=function($items, $parentId='NULL') use (&$cPId, &$cOrd, &$ids, &$fQuery) {
			foreach($items as $i=>$item) {
				$ids[]=$item->id;
				$cPId.=" WHEN {$item->id} THEN {$parentId}";
				$cOrd.=" WHEN {$item->id} THEN ".($i+1);
				if(property_exists($item, 'children')) $fQuery($item->children, $item->id);
			}
		};
		$fQuery($data);
		$query='UPDATE '.Menu::model()->tableName()." SET {$cPId} END, {$cOrd} END WHERE `id` IN (".implode(',', $ids).')';
		
		\Yii::app()->db->createCommand($query)->execute();
		
		Y::end();
	}
}