<?php
/* @var $this iblock\controllers\AdminController */
/* @var $model iblock\models\InfoBlock */
/* @var $prop_model iblock\models\InfoBlockProp */

$this->breadcrumbs=array(
	'Инфо-блоки'=>array('index'),
	$model->title . ' - Обновление',
);

?>

<h1>Изменение инфо-блока #<?php echo $model->id; ?></h1>

<?php $this->renderPartial('iblock.views.admin._form', compact('model', 'prop_model')); ?>