<?php
use common\components\helpers\HYii as Y;
use common\components\helpers\HDb;

class HFilter 
{
	public static function rangeof($model=null)
	{
		if(!$model) {
			return self::filterByProducts(HDb::queryColumn('SELECT id FROM product WHERE LENGTH(rangeof)>0'));
		}
		else {
			return self::filterByProducts(HDb::queryColumn('SELECT id FROM product WHERE rangeof LIKE \'%|'.$model->id.'|%\''));
		}
	}
	
	public static function sale()
	{
		return self::filterByProducts(HDb::queryColumn('SELECT id FROM product WHERE sale=1'));
	}
	
	public static function category($category)
	{
		if($cached=Y::cache()->get('filter_category_'.$category->id)) {
			if((time() - (int)$cached[0]) < 60) return $cached[1];
		}
		
		$categoryIDs=[];
		$descendantsLevel=(int)D::cms('shop_category_descendants_level');
		if($category && $descendantsLevel) {
			$descendants=$category->descendants($descendantsLevel)->findAll(['index'=>'id', 'select'=>'`t`.`id`']);
			if($descendants)
				$categoryIDs=array_keys($descendants);
		}
		$categoryIDs[]=$category->id;
		
		$product=new Product();
		$criteria=new \CDbCriteria(['select'=>'`t`.`id`', 'index'=>'id']);
		$criteria->mergeWith($product->getRelatedCriteria($categoryIDs));
		if($products=$product->findAll($criteria)) {
			$productIDs=array_keys($products);
		}
		
		$query='SELECT id FROM product WHERE (category_id IN ('.implode(',',$categoryIDs).'))';
		if(!empty($productIDs)) {
			$query.=' OR id IN ('.implode(',',$productIDs).')';
		}	
			
		$ids=self::filterByProducts(HDb::queryColumn($query));
		Y::cache()->set('filter_category_'.$category->id, [time(), $ids]);
		
		return $ids;
	}
	
	public static function getRequestData()
	{
		$data=[];
		$data_json = Yii::app()->getRequest()->getQuery('data');
		if(isset($data_json)){
			$attr_filter = json_decode($data_json);
			if(count($attr_filter)>0){
				foreach ($attr_filter as $key => $attr) {
					if($attr->value=="none" || $attr->name == "_method") continue;
					$data[$attr->name]=(int)$attr->value;
				}
			}
		}
		return $data;
	}
	
	protected static function filterByProducts($ids)
	{
		if(empty($ids)) return null;
		
		$query='select t.id as value_id, ta.id as attribute_id, ta.name as attribute_name, t.value, ta.filter' 
			. ' from eav_value as t'
			. ' left join eav_attribute as ta on (ta.id=t.id_attrs)'
			. ' where t.id_product in ('.implode(',', $ids).')' 
			. ' group by t.value order by ta.name, t.value';
		
		return self::fetchResult(HDb::queryAll($query));		
	}
	
	/**
	 * 
	 * @param array $result item as array(0=>value_id, 1=>attribute_id, 2=>attribute_name, 3=>value, 4=>filter)
	 */
	protected static function fetchResult($result)
	{
		$data=[];
		
		if($result) {
			foreach($result as $item) {
				foreach([0=>'value_id', 1=>'attribute_id', 2=>'attribute_name', 3=>'value', 4=>'filter'] as $idx=>$attribute) {
					if(array_key_exists($attribute, $item)) {
						$$attribute=$item[$attribute];
					}
					else {
						$$attribute=$item[$idx];
					}
				}
				if(!(int)$filter|| !trim($attribute_name) || !trim($value)) continue;
				if(!isset($data[$attribute_id])) {
					$data[$attribute_id]=[
						'name'=>$attribute_name, 
						'values'=>[$value_id=>$value]
					];
				}
				else {
					$data[$attribute_id]['values'][$value_id]=$value;
				}
			}
		}
		
		return $data;
	}
}