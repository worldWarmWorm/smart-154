<?
/** @var \common\widgets\form\TextAreaField $this */

echo $this->openTag();
echo $this->labelTag();

echo $this->form->textArea($this->model, $this->attribute, $this->htmlOptions);

if($this->note) {
    echo \CHtml::tag($this->noteTag, $this->noteOptions, $this->note);
}

echo $this->errorTag();
echo $this->closeTag();
