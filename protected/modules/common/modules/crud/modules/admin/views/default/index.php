<?php
/** @var \crud\modules\admin\controllers\DefaultController $this */
/** @var string $cid индетификатор настроек CRUD для модели. */
/** @var \CActiveDataProvider $dataProvider */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use crud\components\helpers\HCrud;

$t=Y::ct('\crud\modules\admin\AdminModule.controllers/default', 'common.crud');
$tbtn=Y::ct('\CommonModule.btn', 'common');

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
    if($btnSettingsId=HCrud::param($cid, 'buttons.settings.id')) {
      	echo CHtml::link(
      		'<span class="glyphicon glyphicon-cog"></span>&nbsp;'.$btnSettings?:$tbtn('settings'), 
      	    ['/admin/settings/', 'id'=>$btnSettingsId], 
      		A::m(['class'=>'btn btn-warning pull-right'], HCrud::param($cid, 'buttons.settings.htmlOptions', []))
      	);
    }
}

$customBtn=HCrud::param($cid, 'buttons.custom');
if(is_callable($customBtn)) $customBtn=call_user_func($customBtn);
if(!empty($customBtn)) echo $customBtn;
?></div><?
$this->renderPartial($this->viewPathPrefix.'_gridview', compact('cid', 'dataProvider'));
?>
