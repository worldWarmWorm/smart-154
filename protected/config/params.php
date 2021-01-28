<?php
/**
 * Параметры для конфигурации \Yii::app()->params
 */
return [
    'clientCombineScriptFiles'=>false,
    'uploadSettingsPath' => '/files/settings/',
    'month' => true,
    'adminEmail'            => 'cms@kontur-agency.ru',
    'menu_limit'            => 5,
    'news_limit'            => 7,
    'posts_limit'           => 10,
    'hide_news'             => false,
    'tmb_height'            => 380,
    'tmb_width'             => 350,
    'max_image_width'       => 1400,
    'dev_year'              => 2020,
    'subcategories'         => true,
	// раздел администрирования
	'backend'=>[
		// дополнительные настройки основного меню
		'menu'=>[
			// раздел Каталог
			'catalog'=>[
				// модуль common/crud array(pos=>id, id2)
				'crud'=>[],
				// модуль common/settings array(pos=>id, id2)
				'settings'=>[]
			],
			// раздел Модули
			'modules'=>[
				// модуль common/crud array(pos=>id, id2)
				'crud'=>[],
				// модуль common/settings array(pos=>id, id2)
				'settings'=>[]
			],
		]
	],
	// Модуль common
	'common'=>['ext'=>[
        'updateTime'=>['behaviors'=>['UpdateTimeBehavior'=>['autoSendLastModified'=>true]]],
        'email'=>[
            'debug'=>0,
            'language'=>'ru',
            'charset'=>'utf-8',
            /*'smtp'=>[
                'Host'=>
                'Port'=>
                'SMTPSecure'=>
                'SMTPAuth'=>
                'Username'=>
                'Password'=>
            ]*/
        ]
    ]],
	'extend'=>['modules'=>[
	]]
];
