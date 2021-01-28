<?php
$prefix = (D_MODE_LOCAL && is_file(__DIR__ . '/local.db.php')) ? 'local.' : '';

return CMap::mergeArray(
	require(dirname(__FILE__).'/defaults.php'),
	array(
		'components'=>array(
			'db'=>include(dirname(__FILE__)."/{$prefix}db.php"),
		),
        //'theme'=>'template_flex',
        'theme'=>'adaptive_template_4',
	)
);
