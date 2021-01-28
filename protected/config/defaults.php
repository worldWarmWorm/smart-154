<?php
return array(
	'basePath'=>dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
	'language'=>'ru',
	'name'=>'Новый сайт',
		
	'preload'=>array('log', 'd', 'kontur_common_init'),

	'aliases'=>array(
		'widget'=>'application.widget',
		'widgets'=>'application.widget',
 		'reviews'=>'application.modules.reviews',
 		'iblock'=>'application.modules.iblock'
	),

	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.components.behaviors.*',
		'application.components.filters.*',
		'application.components.helpers.*',
		'application.components.models.*',
		'application.components.validators.*',
        'ext.*',
        'ext.helpers.*',
        'ext.sitemap.*',
        'ext.CmsMenu.*',
        'ext.ContentDecorator.*',
		'application.models.Slide',
	),

	'modules'=>array(
		'modules'=>array(
		    'actions',
		),
        'admin',
        'devadmin',
        'iblock',
        'common'=>[
			'modules'=>[
				'crud'=>[
					'class'=>'common.modules.crud.CrudModule',
					'config'=>include(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'crud.php')
				],
				'settings'=>[
					'class'=>'common.modules.settings.SettingsModule',
					'config'=>include(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'settings.php')
				]
			]
        ],
        'extend',
		'ecommerce',
        'reviews'=>['class'=>'reviews.ReviewsModule'],
        /*'gii'=>array(
             'class'=>'system.gii.GiiModule',
             'password'=>'1',
             'generatorPaths'=>array(
                 'application.gii',   // псевдоним пути
             ),
             'ipFilters'=>array('*.*.*.*'),
             'newFileMode'=>0644,
             'newDirMode'=>0755,
         ),/**/
	),

	// application components
	'components'=>array(
		'kontur_common_init'=>['class'=>'\common\components\Init'],
		'd'=>array(
			'class'=>'DApi',
			'modules'=>include(dirname(__FILE__).DIRECTORY_SEPARATOR.'modules.php')?:array(),
			'configDCart'=>include(dirname(__FILE__).DIRECTORY_SEPARATOR.'dcart.php')
		),
			
		'user'=>array(
			'class'=>'DWebUser',
			'allowAutoLogin'=>true,
            'loginUrl' => array('admin/default/login'),
		),

        'cache'=>array(
            'class'=>'system.caching.CFileCache',
         ),
         'sitemapcache'=>array(
            'class'=>'system.caching.CFileCache',
            'keyPrefix'=>'sitemap'
         ),

        'settings'=>array(
            'class'     => 'CmsSettings',
            'cacheId'   => 'cmscfg_'
		        . (preg_match('#^/(cp|admin)/#', $_SERVER['REQUEST_URI']) 
        		    ? (isset($_GET['rid']) ? $_GET['rid'] 
                	: (isset($_COOKIE['current_region']) ? $_COOKIE['current_region'] : '')) 
			            : crc32($_SERVER['SERVER_NAME'])
		        ),
            'cacheTime' => 84000,
        ),

		'urlManager'=>array(
			'urlFormat'=>'path',
            'showScriptName'=>false,
			'rules'=>include(dirname(__FILE__).DIRECTORY_SEPARATOR.'urls.php')
		),

		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=',
			'emulatePrepare' => true,
			'username' => '',
			'password' => '',
			'charset' => 'utf8',
            'tablePrefix' => ''
		),

		'errorHandler'=>YII_DEBUG ? [] : array(
            'errorAction'=>'error/error',
        ),

		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// yii debug toolbar
	            /* array(
    	            'class'=>'ext.yii-debug-toolbar.YiiDebugToolbarRoute',
        	        // Access is restricted by default to the localhost
            	    //'ipFilters'=>array('127.0.0.1','192.168.1.*', 88.23.23.0/24),
	            ) /**/
			)
		),

        'image' => array(
            'class'=>'ext.image.CImageComponent',
            'driver'=> (!D_MODE_LOCAL && extension_loaded('imagick')) ? 'ImageMagick' : 'GD',
            // ImageMagick setup path
            //'params'=>array('directory'=>"C:\ImageMagick\\"),
        ),
		'ih'=>array(
        	'class'=>'CImageHandler',
	    ),
        'clientScript' => array(
            'class' => 'ext.minify.EClientScript',            
            'combineScriptFiles' => false,
            'combineCssFiles' => false,
            'optimizeCssFiles' => false,
            'optimizeScriptFiles' => false,
//            'coreScriptPosition'=>CClientScript::POS_BEGIN,
            'defaultScriptPosition'=>CClientScript::POS_END,
            'defaultScriptFilePosition'=>CClientScript::POS_BEGIN,
			'packages'=>[
                'maskedinput'=>[
                    'basePath'=>'webroot.js.jquery',
                    'baseUrl'=>'/js/jquery/',
                    'js'=>['jquery.maskedinput.min.js'],
                    'depends'=>['jquery']
                ],
				'inputmask'=>[
					'basePath'=>'webroot.js.inputmask',
					'baseUrl'=>'/js/inputmask/',
					'js'=>['jquery.inputmask.min.js'],
					'depends'=>['jquery']
				],
				'fancybox'=>[
			        'basePath'=>'webroot.js.fancybox',
			        'baseUrl'=>'/js/fancybox/',
			        'js'=>['jquery.fancybox.min.js'],
			        'css'=>['jquery.fancybox.min.css'],
			        'depends'=>['jquery']
			    ]
            ]
        ),
        'assetManager'=>array(
			'class'=>'ext.EAssetManager.EAssetManager',
			'lessCompile'=>true,
			'lessCompiledPath'=>'webroot.assets.css',
			'lessFormatter'=>'compressed',
			'lessForceCompile'=>false,
		),		
	),

	'params'=>include(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'params.php'),
);
 
