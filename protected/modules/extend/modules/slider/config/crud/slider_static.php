<?php
/**
 * Файл настроек модели \slider\models\Slider
 */
use common\components\helpers\HYii as Y;
use extend\modules\slider\models\Slider;
use common\components\helpers\HRequest;

$extraAccess=D::isDevMode() || D::cms('slider_many');
$onBeforeLoad=function() use ($extraAccess) { if(!$extraAccess) HRequest::e404(); };

$t=Y::ct('\extend\modules\slider\SliderModule.crud', 'extend.slider');
return [
	'use'=>['extend.modules.slider.config.crud.slider', null],
	'buttons'=>[
		'create'=>['label'=>$extraAccess?$t('slider.button.create'):''],
	],	
	'crud'=>[		
		'index'=>[
			'gridView'=>[ 
			    'sortable'=>($extraAccess ? [] : [
			        'disabled'=>true
			    ]),
				'columns'=>[
				    'title'=>[
				        'value'=>'"<strong>".CHtml::link($data->title,["/cp/crud/index", "cid"=>"slide", "slider"=>$data->id])."</strong><small>"'
				        . '. ("<br/><span>'. $t('slider.label.options.widget').':</span> ".(($i=$data->optionsBehavior->find("code","widget"))?(($v=$i["value"])?$v:"Slick"):"Slick"))'
				        . '. ("<br/><span>'. $t('slider.label.options.config').':</span> ".(($i=$data->optionsBehavior->find("code","config"))?(($v=$i["value"])?$v:"default"):"default"))'
				        . '. ("<br/><span>'. $t('slider.label.options.width').':</span> ".(($i=$data->optionsBehavior->find("code","width"))?(($w=$i["value"])?$w."px":"'.$t('emptyValue').'"):"<span class=\'label label-danger\'><i class=\'glyphicon glyphicon-exclamation-sign\'></i> '.$t('emptyValue').'</span>"))'
				        . '. ("<br/><span>'. $t('slider.label.options.height').':</span> ".(($i=$data->optionsBehavior->find("code","height"))?(($h=$i["value"])?$h."px":"'.$t('emptyValue').'"):"<span class=\'label label-danger\'><i class=\'glyphicon glyphicon-exclamation-sign\'></i> '.$t('emptyValue').'</span>"))'
				        . ($extraAccess 
				            ? '. ("<br/><span>'. $t('slider.label.options.proportional').':</span> ".(($i=$data->optionsBehavior->find("code","proportional"))?$i["value"]:"yes")."</span>")'
        				        . '. ("<br/><span>'. $t('slider.label.options.adaptive').':</span> ".(($i=$data->optionsBehavior->find("code","adaptive"))?$i["value"]:"no")."</span>")'
        				        . '. ("<br/><span>'. $t('slider.label.options.description').':</span> ".(($i=$data->optionsBehavior->find("code","description"))?$i["value"]:"no")."</span>")'
        				        . '. ("<br/><span>'. $t('slider.label.options.link').':</span> ".(($i=$data->optionsBehavior->find("code","link"))?$i["value"]:"yes")."</span>")'
        				        . '. ("<br/>".($data->description?\common\components\helpers\HHtml::intro($data->description,100):""))'
				            : ''
				        ) . '. "</small>"'
				    ],
					'crud.buttons'=>[
						'params'=>[
							'template'=>$extraAccess?'{edit_slides}&nbsp;&nbsp;{update}{delete}':'{edit_slides}'
						]
					]
				]
			]
		],
		'create'=>[
			'onBeforeLoad'=>$onBeforeLoad
		],
		'update'=>[
			'onBeforeLoad'=>$onBeforeLoad
		],
		'delete'=>[
			'onBeforeLoad'=>$onBeforeLoad
		],
	]
];