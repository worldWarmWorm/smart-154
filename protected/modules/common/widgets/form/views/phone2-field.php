<?
/** @var \common\widgets\form\TextField $this */

echo $this->openTag();
echo $this->labelTag();

echo $this->form->textField($this->model, $this->attribute, $this->htmlOptions);

echo $this->errorTag();

if($this->note) {
    echo \CHtml::tag($this->noteTag, $this->noteOptions, $this->note);
}

echo $this->closeTag();
