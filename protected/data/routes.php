<?php

$routes = array(
    'site'=>array(
        'event'=>array(
            'all'=>'site/events',
            'one'=>'site/event'
        ),
        'page'=>array(
            'one'=>'site/page'
        ),
        'link'=>array(
            'one'=>'class:LinkRoute'
        ),
        'blog'=>array(
            'one'=>'site/blog'
        ),
        'question'=>array(
            'all'=>'question/index'
        ),
        'gallery' => array(
            'all' => 'gallery/index',
        ),
        'sale' => array(
            'all' => 'sale/index',
        ),
		'reviews' => array(
			'all' => 'reviews/default/index',
		)
    ),

    'admin'=>array(
        'event'=>array(
            'all'=>'event/index'
        ),
        'page'=>array(
            'update'=>'page/update'
        ),
        'link'=>array(
            'update'=>'link/update'
        ),
        'shop'=>array(
            'all'=>'shop/index'
        ),
        'blog'=>array(
            'update'=>'blog/index'
        ),
        'question'=>array(
            'all'=>'question/index'
        ),
        'slider'=>array(
            'all'=>'slider/index'
        ),
        'order' => array(
            'all' => 'order/index',
        ),
        'review' => array(
            'all' => 'review/index',
        ),
        'feedback' => array(
            'combine' => 'feedback',
        ),
        'gallery' => array(
            'all' => 'gallery/index',
        ),
        'sale' => array(
            'all' => 'sale/index',
        ),
        'reviews' => array(
            'all' => 'reviews/index',
        ),
    )
);

if(Yii::app()->params['attributes']){
    $routes['admin']['attributes'] = array(
            'all' => 'attributes/index',
    );
}

return $routes;
