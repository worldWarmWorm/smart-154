<?php
/* @var $this iblock\controllers\AdminController */
/* @var $model iblock\models\InfoBlock */
/* @var $prop_model iblock\models\InfoBlockProp */

$this->breadcrumbs=array(
	'Инфо-блоки'=>array('index'),
	'Создание',
);
?>

<h1>Новый инфо-блок</h1>

<?php $this->renderPartial('iblock.views.admin._form', compact('model', 'prop_model')); ?>