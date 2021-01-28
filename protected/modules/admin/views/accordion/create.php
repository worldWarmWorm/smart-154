<?php
/* @var $this AccardionController */
/* @var $model Accardion */

$this->breadcrumbs=array(
	'Аккордеоны'=>array('index'),
	'Создание',
);
?>

<h1>Создание аккордеона</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>