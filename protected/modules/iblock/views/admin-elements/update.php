<?php
/* @var $this iblock\controllers\AdminElementsController */
/* @var $model iblock\models\InfoBlockElement */
/* @var $iblock iblock\models\InfoBlock */

$this->breadcrumbs=array(
    $iblock->title => ['/admin/iblockElements/index', 'block_id' => $iblock->id],
	$model->title,
);

?>

<h1>Изменение записи #<?php echo $model->id; ?></h1>

<?php $this->renderPartial('iblock.views.admin-elements._form', compact('model', 'iblock')); ?>