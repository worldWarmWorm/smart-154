<?php

$breadcrumbs = array();
$breadcrumbs[D::cms('shop_title', 'Каталог')] = array('shop/index');
$breadcrumbs[] = 'Заказы';
$this->breadcrumbs = $breadcrumbs;
?>
<?php
/** @var DOrderController $this */

$this->widget('\DOrder\widgets\admin\actions\ListWidget');
?>