<?php
/** @var \crud\controllers\DefaultController $this */
/** @var string $cid */
/** @var \CActiveDataProvider $dataProvider */
use crud\components\helpers\HCrud;

$dataProvider->pagination->params['cid']=$cid;

$this->widget('zii.widgets.CListView', [
    'dataProvider'=>$dataProvider,
    'itemView'=>HCrud::param($cid, 'public.view.viewitem', $this->viewPathPrefix.'_listview_item'),
    'viewData'=>compact('cid'),
    'enableHistory'=>true,
    'emptyText'=>'',
    'itemsTagName'=>'div',
    'itemsCssClass'=>'row',
    'loadingCssClass'=>'loading-content',
    'template'=>'{items}{pager}',
    'pagerCssClass'=>'pagination',
    'pager'=>[
        'class' => 'DLinkPager',
        'maxButtonCount'=>'5',
        'header'=>'',
        'htmlOptions'=>['class'=>'yiiPager', 'style'=>'margin-top:10px']            
    ],
    'htmlOptions'=>['class'=>'crud__listview'],
    'afterAjaxUpdate'=>'function(){$("html, body").animate({scrollTop: ($(".crud__listview").offset().top - 50)}, 200);}',
]);
?>
