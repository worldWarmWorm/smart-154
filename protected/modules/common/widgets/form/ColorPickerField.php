<?php
/**
 * Виджет поля выбора цвета. 
 * 
 */
namespace common\widgets\form;

use common\components\widgets\form\BaseField;

class ColorPickerField extends BaseField
{
    /**
	 * (non-PHPDoc)
	 * @see \common\components\widgets\form\BaseField::$htmlOptions
	 */
    public $htmlOptions=['class'=>'form-control w25'];
    
    /**
	 * (non-PHPDoc)
	 * @see \common\components\widgets\form\BaseField::$view
	 */
	public $view='colorpicker-field';
}