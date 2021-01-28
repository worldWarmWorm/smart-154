<?
/** @var \common\widgets\form\ColorPickerField $this */
use common\components\helpers\HArray as A;
use common\components\helpers\HYii as Y;

A::set($this->htmlOptions, 'class', ' jscolor', true, 1);

Y::module('common')->publishJs('js/vendors/jscolor/jscolor_jsc.js');
Y::js('ColorPickerField', ';$(".jscolor").on("click", function(e){window.jscolor.register();});', \CClientScript::POS_READY);

echo $this->openTag();
echo $this->labelTag();

echo $this->form->textField($this->model, $this->attribute, $this->htmlOptions);

echo $this->errorTag();

if($this->note) {
	echo \CHtml::tag($this->noteTag, $this->noteOptions, $this->note);
}

echo $this->closeTag();