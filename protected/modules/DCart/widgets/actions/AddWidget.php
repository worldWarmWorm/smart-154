<?php
/**
 * DCart "Add to cart" action widget
 * 
 * @use \AjaxHelper
 * @use \ARHelper
 */
namespace DCart\widgets\actions;

class AddWidget extends BaseActionWidget
{
	/**
	 * Model id.
	 * @var integer
	 */
	public $id;
	
	public function run()
	{
		$debugMode = defined('YII_DEBUG') && (YII_DEBUG === true);
		 
		$ajax = new \AjaxHelper();
		
		$modelClass = \Yii::app()->request->getPost('model');
		if($modelClass && !substr_count($modelClass, '\\')) 
			$modelClass = '\\' . $modelClass;
		
		if($debugMode) { 
			if(!$modelClass) 
				$ajax->errors[] = 'Error: DCart. AddWidget. Model not defined.';
			elseif(!class_exists($modelClass)) 
				$ajax->errors[] = 'Error: DCart. AddWidget. Model class not exists.';
			elseif(!in_array('CActiveRecord', class_parents($modelClass))) 
				$ajax->errors[] = 'Error: DCart. AddWidget. Model not instanceof CActiveRecord.';
		}
		
		if($modelClass && class_exists($modelClass) && in_array('CActiveRecord', class_parents($modelClass))) {
			$attributes = \ARHelper::getNonVirtualAttributes($modelClass::model(), \Yii::app()->cart->getAllAttributes());
			$model = $modelClass::model()->findByPk($this->id, array('select' => implode(',', $attributes)));
			
			if(!$model && defined('YII_DEBUG') && (YII_DEBUG === true)) {
				$ajax->errors[] = 'Error: DCart. AddWidget. Model not found.';
			}
			elseif($model) {
				$count = 1; 

				$data = \Yii::app()->request->getPost('data');
				if(is_array($data)) {
					foreach($data as $attribute=>$value) {
						if($attribute == 'count') {
							$count = (int)$value > 0 ? $value : 1;	
						} elseif(property_exists($model, $attribute)) {
							$model->$attribute = $value;
						}
				    }
				}
				
				$isFirstItem=\Yii::app()->cart->isEmpty();
				$isNewItem=!\Yii::app()->cart->exists(\Yii::app()->cart->generateHash($model));
				if($hash=\Yii::app()->cart->add($model, $count)) {
					$ajax->success = true;
					$ajax->data['hash'] = $hash;
					$ajax->data['isNewItem'] = $isNewItem;
					$ajax->data['isFirstItem'] = $isFirstItem;
					
					if($isFirstItem) {
						$ajax->data['html'] = \DCart\widgets\ModalCartWidget::renderItems(true);
					} 
					elseif($isNewItem) {
						$ajax->data['html'] = \DCart\widgets\ModalCartWidget::renderItem($hash, true);
					}
					else {
						$ajax->data['count']=\Yii::app()->cart->getCount($hash);
						$ajax->data['totalPrice']=\HtmlHelper::priceFormat(\Yii::app()->cart->getTotalPrice($hash));
					}
					
					$ajax->data['cartTotalPrice']=\HtmlHelper::priceFormat(\Yii::app()->cart->getTotalPrice());
					$ajax->data['cartTotalCount']=\Yii::app()->cart->getTotalCount();
				}
			}
		}
		
		$ajax->endFlush();
	}
}