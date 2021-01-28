<?
use common\components\helpers\HArray as A;

// $this->widget('\common\widgets\form\DropDownListField', A::m(compact('form', 'model'), [
// 	'attribute'=>'cropTop',
// 	'data'=>['top'=>'Верх', 'center'=>'Центр', 0=>'Нет'],
// ]));
if(D::cms('shop_show_categories')):
    $this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), ['attribute'=>'show_categories_on_shop_page'])); 
    $this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), [
        'attribute'=>'show_categories_on_category_page_default',
        'note'=>'Если снять галочку, то для категорий у которых установлен параметр "Показать список категорий" в значение "По умолчанию" показ списка категорий будет отключен'
    ])); 
    $this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), [
        'attribute'=>'show_categories_on_category_page',
        'note'=>'Можно снять галочку для принудительного отключения показа списка категорий на страницах категорий каталога'
    ])); 
endif;

$this->widget('\common\widgets\form\TinyMceField', A::m(compact('form', 'model'), [
    'attribute'=>'main_text', 
    'uploadImages'=>false, 
    'uploadFiles'=>false, 
    'showAccordion'=>false
]));
$this->widget('\common\widgets\form\TinyMceField', A::m(compact('form', 'model'), ['attribute'=>'main_text2']));
?>
