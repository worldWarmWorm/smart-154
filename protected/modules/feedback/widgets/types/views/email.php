<?php
/** @var \feedback\widgets\types\EmailTypeWidget $this */
/** @var FeedbackFactory $factory */
/** @var string $name attribute name. */
use common\components\helpers\HArray as A;

$htmlOptions=A::m(A::m([
	'class'=>'inpt',
	'placeholder'=>$factory->getOption("attributes.{$name}.placeholder", '') 
], $factory->getOption("attributes.{$name}.htmlOptions", [])), A::get($this->params, 'htmlOptions', []));
?>
<?php //echo $form->labelEx($factory->getModelFactory()->getModel(), $name); ?>
<?= \CHtml::tag('div', ['style'=>'display:none'], $form->error($factory->getModelFactory()->getModel(), $name)); ?>
<?= $form->textField($factory->getModelFactory()->getModel(), $name, $htmlOptions); ?>
