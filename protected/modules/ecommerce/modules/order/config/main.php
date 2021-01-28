<?php
return [
    'modules'=>[
		'admin'=>[
			'class'=>'\ecommerce\modules\order\modules\admin\AdminModule'
		]	
	],
	'controllerMap'=>[
		'default'=>[
			'class'=>'\ecommerce\modules\order\modules\admin\controllers\DefaultController'
		]
	]
];
