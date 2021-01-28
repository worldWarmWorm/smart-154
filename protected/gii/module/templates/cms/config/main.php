<?php echo "<?php\n"; ?>
return [
	'modules'=>[
		'admin'=>[
			'class'=>'\<?=$this->moduleID?>\modules\admin\AdminModule'
		]	
	],
	'controllerMap'=>[
		'default'=>[
			'class'=>'\<?=$this->moduleID?>\controllers\DefaultController'
		]	
	]
];