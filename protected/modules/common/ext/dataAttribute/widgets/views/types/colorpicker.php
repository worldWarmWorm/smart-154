<?php
/**
 * Тип поля "Выбор цвета".
 *
 * @var \common\ext\dataAttribute\widgets\DataAttribute $this
 * @var string $name имя поля
 * @var string $value значение поля
 * @var string|array $data данные типа
 * @var string $view шаблон отображения
 * @var array $params дополнительные параметры
 * @var boolean $isTemplate генерируется шаблон нового элемента.
 * @var string $addButtonId идентификатор кнопки добавления
 */
use common\components\helpers\HArray as A;
use common\components\helpers\HYii as Y;

Y::module('common')->publishJs('js/vendors/jscolor/jscolor_jsc.js');
Y::js(false, ';$("#'.$addButtonId.'").on("click", function(e){window.jscolor.register();});', \CClientScript::POS_READY);

echo \CHtml::textField($name, $value, A::m(A::get($params, 'htmlOptions', []), [
    'class'=>'daw-inpt form-control jscolor', 
    'maxlength'=>255, 
    'disabled'=>$isTemplate
]));
?>