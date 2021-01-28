<?php
/** @var \feedback\widgets\types\ListTypeWidget $this */
/** @var FeedbackFactory $factory */
/** @var string $name attribute name. */
use common\components\helpers\HArray as A;

if($this->params['selected']) { 
	$options=[
		$this->params['selected']=>[
			'selected'=>true, 
			'label'=>$this->items[$this->params['selected']]
		]
	];
}
else {
	$options=[];
}

$htmlOptions=A::m([
	'class'=>'inpt',
	'placeholder'=>$factory->getOption("attributes.{$name}.placeholder", ''),
	'options'=>$options 
], $factory->getOption("attributes.{$name}.htmlOptions", []));
?>
<?php //echo $form->labelEx($factory->getModelFactory()->getModel(), $name); ?>
<?= \CHtml::tag('div', ['style'=>'display:none'], $form->error($factory->getModelFactory()->getModel(), $name)); ?>
<?= $form->dropDownList($factory->getModelFactory()->getModel(), $name, $this->items, $htmlOptions); ?>