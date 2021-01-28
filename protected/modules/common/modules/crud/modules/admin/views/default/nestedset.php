<?php
/** @var \crud\modules\admin\controllers\DefaultController $this */
/** @var string $cid индетификатор настроек CRUD для модели. */
/** @var \common\components\base\ActiveRecord $model */
/** @var \CDataProvider $dataProvider */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use crud\components\helpers\HCrud;
use crud\components\helpers\HCrudView;

$t=Y::ct('\crud\modules\admin\AdminModule.controllers/default', 'common.crud');
$tbtn=Y::ct('\CommonModule.btn', 'common');

$nestedSetConfig = HCrud::param($cid, 'crud.index.nestedset');
if(!is_array($nestedSetConfig)) $nestedSetConfig=[];
if(A::existsKey($nestedSetConfig, 'dataProvider')) {
    unset($nestedSetConfig['dataProvider']);
}

Y::module('common.crud.admin')->publishLess('css/styles.less');
?>
<h1><?=HCrud::param($cid, 'crud.index.title')?></h1>

<div style="margin-bottom: 15px;"><?
$btnCreate=HCrud::param($cid, 'buttons.create.label');
if($btnCreate !== '') {
	echo CHtml::link(
		$btnCreate?:$tbtn('create'), 
		HCrud::getConfigUrl($cid, 'crud.create.url', '/crud/admin/default/create', ['cid'=>$cid], 'c'), ['class'=>'btn btn-primary']); 
}
$btnSettings=HCrud::param($cid, 'buttons.settings.label');
if($btnSettings !== '') {
	if($settings=HCrud::param($cid, 'settings'))
  	echo CHtml::link(
  		'<span class="glyphicon glyphicon-cog"></span>&nbsp;'.$btnSettings?:$tbtn('settings'), 
  		['/admin/settings/', 'id'=>key($settings)], 
  		['class'=>'btn btn-warning pull-right']
  	);
}
?></div><?
    $this->widget('\crud\modules\admin\widgets\NestedSetNestable', A::m([
        'id'=>HCrudView::getGridId($cid),
        'cid'=>$cid,
        'dataProvider'=>$dataProvider,
        'saveUrl'=>$this->createUrl('nestableSave', ['cid'=>$cid])
    ], $nestedSetConfig));
?>