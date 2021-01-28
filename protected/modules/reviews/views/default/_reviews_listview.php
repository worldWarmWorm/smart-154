<?php
/** @var \reviews\controllers\DefaultController $this */
/** @var \CActiveDataProvider[\reviews\models\Review] $dataProvider */
use common\components\helpers\HYii as Y;

$t=Y::ct('ReviewsModule.controllers/default');
$tcl=Y::ct('CommonModule.labels', 'common');

$this->widget('\common\widgets\listing\SizerListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_reviews_item',
	'enableHistory'=>true,
	'sorterHeader'=>$tcl('sort').':',
	'pagerCssClass'=>'pagination',
 	'pager'=>array(
 		'class' => 'DLinkPager',
 		'maxButtonCount'=>'5',
 		'header'=>''
 	),
	'loadingCssClass'=>'loading-content',
	'itemsTagName'=>'ul',
	'emptyText' => $t('list.emptyText'),
	'itemsCssClass'=>'list__reviews',
	'sortableAttributes'=>false,
	'id'=>'ajaxListView',
	'sizerHeader'=>$tcl('showAt').': ',
	'sizerVariants'=>[15, 30, 60, 120],
	'template'=>'{items}{pager}' // {sizer}{sorter}
));
?>
