<?php
/** @var \ecommerce\modules\order\modules\admin\controllers\DefaultController $this */
/** @var \CActiveDataProvider $ordersDataProvider[\ecommerce\modules\order\models\Order] */
use common\components\helpers\HYii as Y;

$t=Y::ct('\ecommerce\modules\order\modules\admin\AdminModule.controllers/default', 'ecommerce');
?>
<h1><?=$t('page.title')?></h1>

<? $this->renderPartial('ecommerce.modules.order.modules.admin.views.default._orders_gridview', [
    'dataProvider'=>$ordersDataProvider
]); ?>
