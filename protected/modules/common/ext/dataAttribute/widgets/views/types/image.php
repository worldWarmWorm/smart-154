<?php
/**
 * Тип поля "Изображение".
 *
 * @var \common\ext\dataAttribute\widgets\DataAttribute $this
 * @var string $name имя поля
 * @var string $value значение поля
 * @var string|array $data данные типа
 * @var string $view шаблон отображения
 * @var array $params дополнительные параметры
 * @var boolean $isTemplate генерируется шаблон нового элемента.
 */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

$htmlOptions=A::get($params, 'htmlOptions', []);
$htmlOptions['imageOptions']=A::get($htmlOptions, 'imageOptions', ['style'=>'width:100%;']);
$imageOptions=A::m($htmlOptions['imageOptions'], ['data-js'=>'img', 'style'=>'display:none']);
unset($htmlOptions['imageOptions']);

echo \CHtml::openTag('div', ['class'=>'js-dataattribute-image', 'style'=>'width:100%;']);
    echo \CHtml::openTag('div', ['style'=>'width:100%;text-align:center;']);
        echo \CHtml::textField($name, $value, A::m($htmlOptions, ['class'=>'daw-inpt form-control js-dataattribute-image-url', 'maxlength'=>255, 'disabled'=>$isTemplate]));
        echo \CHtml::image($value, '', $imageOptions);
    echo \CHtml::closeTag('div');
echo \CHtml::closeTag('div');

Y::css('ext-dataattribute-typeimage', '.js-dataattribute-image:hover{outline:2px solid #0644A0;cursor:pointer}.js-dataattribute-image img{max-width:70px;max-height:70px}');
ob_start(function($output){
    Y::js('ext-dataattribute-typeimage', $output, \CClientScript::POS_READY);
});
?>setInterval(function(){
	$('.js-dataattribute-image-url:visible').each(function(){
		if($(this).val().length > 0) {
			$(this).hide();
			$(this).val($(this).val().replace(window.location.origin, ''));
			$(this).attr('value', $(this).val());
			$(this).siblings('img').attr('src', $(this).val());
			$(this).siblings('img').show();
		}
	});
},200);
$(document).on('click', '.js-dataattribute-image img', function(e){
	if(confirm('Удалить выбранное фото торгового предложения?')) {
		$(e.target).hide();
		let f=$(e.target).siblings('.js-dataattribute-image-url');
		f.val('');f.attr('value','');f.show();
	}
	e.preventDefault();
	e.stopImmediatePropagation();
	return false;
});
<?php ob_end_flush(); ?>