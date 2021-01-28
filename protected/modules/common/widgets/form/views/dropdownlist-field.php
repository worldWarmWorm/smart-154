<?
/** @var \common\widgets\form\DropDownListField $this */

echo $this->openTag();

echo $this->labelTag();
echo $this->form->dropDownList($this->model, $this->attribute, $this->data, $this->htmlOptions);
echo $this->errorTag();

if($this->note) {
    echo \CHtml::tag($this->noteTag, $this->noteOptions, $this->note);
}

echo $this->closeTag();
