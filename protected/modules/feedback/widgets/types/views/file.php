<?php
/** @var \feedback\widgets\types\FileTypeWidget $this */
/** @var FeedbackFactory $factory */
/** @var string $name attribute name. */
use common\components\helpers\HArray as A;

$htmlOptions=A::m([
	'class'=>'inpt',
	'placeholder'=>$factory->getOption("attributes.{$name}.placeholder", '') 
], $factory->getOption("attributes.{$name}.htmlOptions", []));
?>
<?php //echo $form->labelEx($factory->getModelFactory()->getModel(), $name); ?>
<?= \CHtml::tag('div', ['style'=>'display:none'], $form->error($factory->getModelFactory()->getModel(), $name)); ?>
<?= $form->fileField($factory->getModelFactory()->getModel(), $name, $htmlOptions); ?>