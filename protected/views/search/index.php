<?php
use common\components\helpers\HArray as A;
?>
<h1>Результаты поиска «<?= $query; ?>»</h1>
<?php 
$isEmpty=true;
foreach($dataProviders as $config) {
    $dataProvider = A::get($config, 'dataProvider');
    if(!empty($dataProvider) && $dataProvider->getTotalItemCount()) {
		$isEmpty=false;
        echo A::get($config, 'wrapperOpen') ?: '<div class="search__block">';

        if($title = A::get($config, 'title')) {
            echo \CHtml::tag('h2', [], $title);
        }
        
        $this->widget('zii.widgets.CListView', A::m([
            'dataProvider'=>$dataProvider,
            'itemView'=>'_item_default',
            'sorterHeader'=>'Сортировка:',
            'itemsTagName'=>'div',
            'emptyText'=>'<div class="search-empty">Не найдено.</div>',
            'itemsCssClass'=>'search-list row',
            // 'sortableAttributes'=>['title'],
            'id'=>'ajaxListView' . md5(A::get($config, 'modelClass')),
            'template'=>'{items}{pager}',
			'viewData'=>compact('config'),
            /* 
			'pagerCssClass'=>'pagination',
            'pager'=>[
                'class' => 'DLinkPager',
                'maxButtonCount'=>'5',
                'header'=>'',
            ],
			/**/
			'pagerCssClass'=>'pager search-pager',
			'pager' => [
	            'header'=>'Страницы: ',
    	        'nextPageLabel'=>'&gt;',
        	    'prevPageLabel'=>'&lt;',
            	'cssFile'=>false,
	            'htmlOptions'=>array('class'=>'news-pager')
    	    ],
			/**/
        ], A::get($config, 'listView')));
        
        echo A::get($config, 'wrapperClose') ?: '</div>';
    }
}
?><?php
if($isEmpty): ?>
<div class="search__result-is-empty">По вашему запросу ничего не найдено</div>
<?php
endif;
?>
