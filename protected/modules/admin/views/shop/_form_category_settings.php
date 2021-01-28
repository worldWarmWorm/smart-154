<?php
use common\components\helpers\HArray as A;

if(D::cms('shop_show_categories')):
    $this->widget('\common\widgets\form\DropDownListField', A::m(compact('form', 'model'), [
        'attribute'=>'show_categories_mode',
        'data'=>Category::model()->showCategoriesModes(),
        'htmlOptions'=>['class'=>'form-control w50']
    ]));    
endif;
?>
