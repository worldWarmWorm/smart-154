<?php
/**
 * Тип поля "Дата".
 *
 * @use common.vendors.EJuiTimePicker.EJuiTimePicker
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

Y::cssFile('/js/datetimepicker/jquery.datetimepicker.css');
Y::jsFile('/js/datetimepicker/jquery.datetimepicker.full.min.js');

?><style>.daw-inpt{width:100% !important;}.ui-tpicker-grid-label{border-collapse:separate !important;}</style><?
$jsVar='js5h6MKsZzDate';
Y::js($jsVar,
	';window.'.$jsVar.'=function(_this) {
		if(!$(_this).data("picker-initialized")) {
            $.datetimepicker.setLocale("ru");
            $(_this).datetimepicker({
                format: "d.m.Y",
                formatDate: "d.m.Y",
    			dayOfWeekStart: 1,
                timepicker: false,
    			scrollInput: false,
                closeOnDateSelect: true,
                i18n:{
                    ru:{
                        months:[
                            "Январь","Февраль","Март","Апрель",
                            "Май","Июнь","Июль","Август",
                            "Сентябрь","Октябрь","Ноябрь","Декабрь"
                        ],
                        dayOfWeek:[
                            "Вс", "Пн", "Вт", "Ср", "Чт",
                            "Пт", "Сб"
                        ]
                    }
                },
                minDate: 0
            }); 
			$(_this).attr("data-picker-initialized", 1);
            $(_this).datetimepicker("show");
		}
	};',
	\CClientScript::POS_READY
);

echo \CHtml::textField($name, $value, [
	'class'=>'daw-inpt form-control', 
	'maxlength'=>255,
	'disabled'=>$isTemplate, 
	// 'readonly'=>true,
	'onclick'=>$jsVar.'(this)'
]);

?>
