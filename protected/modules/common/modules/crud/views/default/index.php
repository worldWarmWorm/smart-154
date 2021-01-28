<?php
/** @var \crud\controllers\DefaultController $this */
/** @var string $cid */
/** @var \CActiveDataProvider $dataProvider */
use common\components\helpers\HYii as Y;
use crud\components\helpers\HCrud;

$t=Y::ct('CrudModule.common', 'crud');
?>
<h1><?= $this->getHomeTitle(); ?></h1>

<?php $this->renderPartial(HCrud::param($cid, 'public.index.viewlist', $this->viewPathPrefix.'_listview'), compact('cid', 'dataProvider')); ?>
