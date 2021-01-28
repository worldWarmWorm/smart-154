<?php
/**
 * Конфигурационный файл для компонента \DCart\components\DCart
 */
return array(
	'class' => '\DCart\components\DCart',
	'attributeImage' => 'cartImg',
	// 'extendKeys'=>[],
	// 'cartAttributes' => [ // аттрибуты которые будут отображены дополнительно в виджете корзины
	// ],
	'attributes' => [ // аттрибуты, которые будут сохранены для заказа
		'code' // => ['onAfterAdd'=>'afterAddCart', 'onAfterUpdate'=>'afterAddCart']
	]
);
