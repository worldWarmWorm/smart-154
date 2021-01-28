<?php
/**
 * Виджет кнопки добавления в крозину.
 * 
 * @use \AssetHelper
 */
namespace DCart\widgets;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

class AddToCartButtonWidget extends BaseCartWidget
{
	/**
	 * Id модели
	 * @var integer
	 */
	public $id;
	
	/**
	 * Добавляемая в корзину модель или имя класса модели. 
	 * @var object|stirng
	 */
	public $model;
	
	/**
	 * Заголовок кнопки
 	 * @var string
	 */
	public $title = 'Buy';
	
	/**
	 * Основной класс стиля 
	 * @var string
	 */
	public $cssClass;
	
	/**
	 * Массив дополнительных аттрибутов, из которых будут братся значения.
	 * Элемент массива может быть:
	 * 1. массивом вида array(cartAttribute, selector), где
	 * cartAttribute: аттрибут корзины, в который будет записано значение.
	 * selector: выражение jQuery селектора 
	 * 2. массивом вида array(cartAttribute, model, attribute), где
	 * cartAttribute: аттрибут корзины, в который будет записано значение.
	 * model: модель, может передана строкой, как имя класса, либо объект.
	 * attribute: аттрибут модели, в котором хранится значение.
	 * 3. массивом вида array(cartAttribute, "js:myfunction"), где
	 * cartAttribute: аттрибут корзины, в который будет записано значение.
	 * "js:myfunction" - строка начинающаяся на "js:", "myfunction" - 
	 * имя js-функции, которая возвращает значение атрибута.
	 * Пример: ['myattribute', 'js:myfunction']
	 * <script>function myfunction() { return 'myvalue'; }</script> 
	 * 4. массивом вида array(cartAttribute, "js:myfunction", code, pos), где
	 * cartAttribute: аттрибут корзины, в который будет записано значение.
	 * "js:myfunction" - строка начинающаяся на "js:", "myfunction" - 
	 * имя js-функции, которая возвращает значение атрибута.
	 * code: js-код функции, которая возвращает значение атрибута.  
	 * pos: (необязательный) позиция публикации js-кода. По умолчанию \CClientScript::POS_READY
	 * Пример: ['myattribute', 'js:window.myfunction', 'window.myfunction=function() { return "myvalue"; }']
	 * Т.к. регистрация происходит внутри POS_READY, необходимо объявлять функцию глобально.
	 * 
	 * В случае 2, генерится селектор вида: "[name='<model>[<attribute>]']"
	 * 
	 * @var array
	 */
	public $attributes = array();
	
	/**
	 * (non-PHPdoc)
	 * @see \DCart\widgets\BaseCartWidget::init()
	 */
	public function init()
	{
		parent::init();
		
		\AssetHelper::publish(array(
			'path' => __DIR__ . DIRECTORY_SEPARATOR . 'assets',
			'js' => 'js/dcart_add_to_button_widget.js'
		));
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::run()
	 */
	public function run()
	{
		if(!is_string($this->model) && !is_object($this->model)) return false;
		
		$modelClass = is_object($this->model) ? get_class($this->model) : $this->model;
		
		$this->render('add_to_cart_default', compact('modelClass'));
	}

	public function getAttributesAsJSON()
	{
		$data = array();
		foreach($this->attributes as $attribute) {
			if(!isset($attribute[0]) || !isset($attribute[1])) continue;
			
			$cartAttribute = $attribute[0];
			if(!is_string($cartAttribute) && !\Yii::app()->cart->attributeExists($cartAttribute)) 
				continue;
			
			if((strpos($attribute[1], 'js:') !== 0) && isset($attribute[2])) {
				if(!is_string($attribute[2])) continue;
				
				$modelClass = is_object($attribute[1]) ? get_class($attribute[1]) : $attribute[1];
				$selector = '[name=\'' . $modelClass . '[' . $attribute[2] . ']\']';
			}
			else {
				if(!is_string($attribute[1])) continue;
				
				if(strpos($attribute[1], 'js:') === 0) {
					if(isset($attribute[2])) {
						Y::js(null, $attribute[2], A::get($attribute, 3, \CClientScript::POS_READY));
					}
					$selector=$attribute[1];
				}
				else {
					$selector = $attribute[1];
				}
			}
			 
			$data[$cartAttribute] = $selector;
		}
		
		return \CJSON::encode($data);
	} 
}