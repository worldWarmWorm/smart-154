<?php
/**
 * Конфигураци основного меню администрирования
 * 
 * main - основные пункты меню
 * catalog - пункты меню Каталог
 * modules - пункты меню Модули 
 * 
 * Каждый пункт меню задается параметрами анлогичными с \CMenu::$items.
 * 
 * Дополнительно может быть указана позиция пункта в параметре "position"=>number
 * number может быть задан отрицательным числом (сортировка с конца)
 * number может быть равным (0)нулю, будет добавлен в конце. 
 * 
 * Также подпункты меню можно задавать как (string) идентификатор меню. 
 * 
 * Дополнительное значение параметра элемента "visible"=>"divider" для разделителей.  
 * Элемент будет отображен, только если существуют предыдущие пункты меню.  
 */
use common\components\helpers\HYii as Y;
use settings\components\helpers\HSettings;
use crud\components\helpers\HCrud;

$t=Y::ct('AdminModule.menu');
return [
	'main'=>[
		[
			'active'=>Y::isAction(Y::controller(), 'page'),
			'label'=>$t('page.label') . ' <b class="caret"></b>',
			'encodeLabel'=>false,
			'url'=>['page/index'],
			'itemOptions'=>[
   		        'onmouseover'=>'$(this).find(".dropdown-menu").show()',
   		        'onmouseout'=>'$(this).find(".dropdown-menu").hide()'
   		    ],
   		    'items'=>'pages'
		],
		[
			'active'=>Y::isAction(Y::controller(), 'event'),
			'label'=>D::cms('events_title', $t('event.label')), 
			'url'=>['event/index']
		],
		[
			'visible'=>D::yd()->isActive('shop'),
			'active'=>Y::isAction(Y::controller(), 'shop'),
			'label'=>D::cms('shop_title', $t('catalog.label')).' <b class="caret"></b>',
			'encodeLabel'=>false,
			'url'=>['shop/index'],
			'itemOptions'=>[
				'onmouseover'=>'$(this).find(".dropdown-menu").show()', 
				'onmouseout'=>'$(this).find(".dropdown-menu").hide()'
			],
			'items'=>'catalog'
		],
		[
			'label'=>$t('modules.label').' <b class="caret"></b>',
			'encodeLabel'=>false,
			'url'=>'#',
			'itemOptions'=>[
				'onmouseover'=>'$(this).find(".dropdown-menu").show()', 
				'onmouseout'=>'$(this).find(".dropdown-menu").hide()'
			],
			'items'=>'modules',
		]
	],
	'catalog'=>[
		[
			'visible'=>D::cmsIs('shop_enable_carousel'),
			'active'=>Y::isAction(Y::controller(), 'shop', 'carousel'),
			'label'=>$t('catalog.carousel.label'), 
			'url'=>['shop/carousel']
		],
		[
			'visible'=>D::cmsIs('shop_enable_brand'),
			'active'=>Y::isAction(Y::controller(), 'brand'),
			'label'=>$t('catalog.brands.label'),
			'url'=>['brand/index']
		],
		[
			'visible'=>D::cmsIs('shop_enable_attributes'),
			'active'=>Y::isAction(Y::controller(), 'attributes'),
			'label'=>$t('catalog.attributes.label'),
			'url'=>['attributes/index']
		],
		[
			'visible'=>D::role('sadmin') && D::isDevMode(),
			'label'=>$t('catalog.migrate.label'),
			'url'=>['/cp/shop/migrate']
		],
		['label'=>'', 'itemOptions'=>['class'=>'divider'], 'visible'=>'divider'],
		HSettings::getMenuItems(Y::controller(), 'shop', 'settings/index', true)
	],
	'modules'=>array_merge([
		[
			'visible'=>D::yd()->isActive('question'),
			'active'=>Y::isAction(Y::controller(), 'question'),
			'label'=>$t('modules.question.label'), 
			'url'=>['question/index'], 
		],
		[
			'visible'=>D::yd()->isActive('gallery'),
			'active'=>Y::isAction(Y::controller(), 'gallery'),
			'label'=>D::cms('gallery_title', $t('modules.photogallery.label')),
			'url'=>['gallery/index']
		],
		[
			'visible'=>D::yd()->isActive('sale'),
			'active'=>Y::isAction(Y::controller(), 'sale'),
			'label'=>D::cms('sale_title', $t('modules.sale.label')),
			'url'=>['sale/index']
		],
		[
			'visible'=>D::yd()->isActive('reviews'),
			'active'=>Y::isAction(Y::controller(), 'reviews'),
			'label'=>$t('modules.reviews.label'),
			'url'=>['reviews/index']
		],
	    [
	        'visible'=>D::yd()->isActive('slider'),
	        'active'=>Y::isAction(Y::controller(), 'crud') && (!empty($_REQUEST['cid']) && in_array($_REQUEST['cid'], ['slider', 'slide'])),
	        'label'=>D::cms('slider_many') ? $t('modules.slider_many.label') : $t('modules.slider.label'),
	        'url'=>D::cms('slider_many') ? ['/cp/crud/index', 'cid'=>'slider'] : ['/cp/crud/index', 'cid'=>'slide', 'slider'=>1]
	    ],
	    // HCrud::getMenuItems(Y::controller(), 'example', 'crud/index', true)
	], \iblock\models\InfoBlock::getListForMenu()),
	'pages'=>[
    	[
    		'active'=>Y::isAction(Y::controller(), 'accordion'),
    		'label'=>'<img src="/images/accordian.png" style="width: 20px;"> '.$t('modules.accordion.label'),
    		'encodeLabel'=>false,
    		'url'=>['accordion/index']
    	],
    ]
];
