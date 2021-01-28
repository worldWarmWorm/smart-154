<?php
/**
 * Тип поля "RadioButton".
 *
 * @var \common\ext\dataAttribute\widgets\DataAttribute $this 
 * @var string $name имя поля
 * @var string $value значение поля
 * @var string|array $data данные типа
 * @var string $view шаблон отображения
 * @var array $params дополнительные параметры
 * @var boolean $isTemplate генерируется шаблон нового элемента.
 */
use common\components\helpers\HArray as A;
if(preg_match('/^(.*?)(\[[^\]]+\])(\[[^\]]+\])$/', $name, $m)) {    
    $radioName=md5("{$m[1]}[{$m[3]}]");
    $hiddenId=\CHtml::getIdByName($name);
    echo \CHtml::hiddenField($name, $value, A::m(A::get($params, 'htmlOptions', []), ['class'=>'daw-inpt', 'maxlength'=>255, 'disabled'=>$isTemplate, 'id'=>$hiddenId]));
    echo \CHtml::radioButton($radioName, $value, A::m(A::get($params, 'htmlOptions', []), [
        'class'=>'daw-inpt', 
        'maxlength'=>255, 
        'disabled'=>$isTemplate,
        'onclick'=>"$('[name^=\'{$m[1]}\'][name$=\'{$m[3]}\']').val(0);$('#{$hiddenId}').val(1);"
    ]));
}
