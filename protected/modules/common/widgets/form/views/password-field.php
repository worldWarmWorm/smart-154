<?
/** @var \common\widgets\form\PasswordField $this */

echo $this->openTag();
echo $this->labelTag();

echo $this->form->passwordField($this->model, $this->attribute, $this->htmlOptions);
if($this->unit) {
	echo \CHtml::tag($this->unitTag, $this->unitOptions, $this->unit);
}

echo $this->errorTag();

if($this->note) {
	echo \CHtml::tag($this->noteTag, $this->noteOptions, $this->note);
}

echo $this->closeTag();