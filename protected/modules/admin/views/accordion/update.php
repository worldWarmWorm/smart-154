<?php
/* @var $this AccordionController */
/* @var $model Accordion */

$this->breadcrumbs=array(
	'Аккордеоны'=>array('index'),
	'<span class="js-acc-title">'.$model->title . '</span> - Обновление',
);

?>

<h1>Обновление аккордеона <span class="js-acc-title"><?php echo $model->title; ?></span></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>