<?php
/* @var $this iblock\controllers\AdminElementsController */
/* @var $model iblock\models\InfoBlockElement */
/* @var $iblock iblock\models\InfoBlock */

$this->breadcrumbs=array(
    $iblock->title => ['/admin/iblockElements/index', 'block_id' => $iblock->id],
	'Новая запись',
);
?>

<h1>Новая запись</h1>

<?php $this->renderPartial('iblock.views.admin-elements._form', compact('model', 'iblock')); ?>