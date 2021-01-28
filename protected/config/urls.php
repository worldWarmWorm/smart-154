<?php
//require_once($_SERVER['DOCUMENT_ROOT'].'/protected/modules/reviews/components/rules/ReviewsRule.php');

return array(
	['class'=>'\iblock\components\rules\IblockRule'],
	'settings/admin/default/index/<id:\w+>'=>'settings/admin/default/index',
	'admin/settings/<id:\w+>'=>'admin/settings/index',
	'cp/settings/<id:\w+>'=>'admin/settings/index',

    array('class'=>'application.components.rules.DShopRule'),
    // Admin
    'cp'=>'admin/default/index',
    'cp/<controller>/<action:\w+>/<id:\d+>'=>'admin/<controller>/<action>',
    'cp/<controller>/<action>'=>'admin/<controller>/<action>',
    'cp/<controller>'=>'admin/<controller>',

    // Admin
    'devcp'=>'devadmin/default/index',
    'devcp/<controller>/<action:\w+>/<id:\d+>'=>'devadmin/<controller>/<action>',
    'devcp/<controller>/<action>'=>'devadmin/<controller>/<action>',
    'devcp/<controller>'=>'devadmin/<controller>',

    // Site Defaults
	'/download/<filename:.*>'=>'site/downloadFile',
    'brands'=>'brand/index',
    'brands/<alias>'=>'brand/view',
    '<code:(404)>'=>'error/index',
    ''=>'site/index',
    'cart'=>'dCart/index',
    'questions'=>'question/index',
    'sitemap'=>'site/sitemap',
	'private-policy'=>'site/privacyPolicy',
    'search/index' => 'search/index',

    array('class'=>'application.components.rules.DAliasRule'),
//	['class'=>'\reviews\components\rules\ReviewsRule'],
    'news/<id:\d+>'=>'site/event',
    'news'=>'site/events',
	'sale'=>'sale/list',
	'sale/index'=>'sale/list',
	'sale/<id:\d+>'=>'sale/view',
	'review/<id:\d+>'=>'reviews/default/view',
	'reviews'=>'reviews/default/index',

    // Default Rules
    '<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
    '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
    '<module>/<controller>/<action:\w+>/<id:\d+>'=>'<module>/<controller>/<action>',
    '<module>/<controller>/<action:\w+>'=>'<module>/<controller>/<action>',
);
