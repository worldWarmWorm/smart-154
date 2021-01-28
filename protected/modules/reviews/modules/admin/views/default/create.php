<?php
/** @var \reviews\modules\admin\controllers\DefaultController $this */
/** @var \reviews\models\Review $model */
use common\components\helpers\HYii as Y;

$tpd=Y::ct('\reviews\modules\admin\AdminModule.controllers/default');
?>
<h1><?= $tpd('page.create.title'); ?></h1>

<? $this->renderPartial($this->viewPathPrefix.'_form', ['model'=>$model]); ?>
