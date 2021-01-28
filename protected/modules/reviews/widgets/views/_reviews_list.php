<?php
/** @var \reviews\widgets\NewReviewForm $this */
/** @var CActiveDataProvider[\reviews\models\Review] $dataProvider */

$this->widget('widget.listView.DSizerListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_reviews_list_item',
	'enableHistory'=>true,
	'sorterHeader'=>'',
	'pagerCssClass'=>'pagination',
	'pager'=>[
		'class' => 'DLinkPager',
		'maxButtonCount'=>'5',
		'header'=>'',
	],
	'loadingCssClass'=>'loading-content',
	'itemsTagName'=>'ul',
	'emptyText' => '<div class="reviews-empty"></div>',
	'itemsCssClass'=>'reviews__list',
	'sortableAttributes'=>[],
	'id'=>'ajaxReviewsListView',
//	'sizerHeader'=>'Показать: ',
//	'sizerVariants'=>[15, 30, 60, 120],
	//'template'=>'{sizer}{sorter}{items}{pager}{sizer}<div class="sort-hidden">{sorter}</div>', // with sizer 
//	'template'=>'{sorter}{items}{pager}<div class="sort-hidden">{sorter}</div>',
	'template'=>'{items}',
));
