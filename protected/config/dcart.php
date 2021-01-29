<?php
/**
 * Конфигурационный файл для компонента \DCart\components\DCart
 */
return array(
	'class' => '\DCart\components\DCart',
	'attributeImage' => 'cartImg',
	'extendKeys'=>['offer'],
	'cartAttributes' => [ // аттрибуты которые будут отображены дополнительно в виджете корзины
		'hex',
		'offer'
	],
	'attributes' => [ // аттрибуты, которые будут сохранены для заказа
		'code', // => ['onAfterAdd'=>'afterAddCart', 'onAfterUpdate'=>'afterAddCart']
		'hex' => ['label' => false],
		'offer' => [
			'onAfterAdd' => 'afterAddCart',
			'onAfterUpdate' => 'afterAddCart'
		],
	]
);
