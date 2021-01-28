<?php $this->pageTitle = D::cms('shop_title').' - '. $this->appName; ?>
<?php
    $this->breadcrumbs=array(
    	D::cms('shop_title', 'Каталог') => array('shop/index')
    );
?>
<div class="left">
  <h1><?=D::cms('shop_title', 'Каталог')?></h1>
</div>
<div class="right">
  <?php /* echo CHtml::link('Очистить кеш картинок  <i class="glyphicon glyphicon-warning-sign"></i>',
  	array('shop/clearImageCache'),
  	array('class'=>' btn btn-danger', 'title'=>'Обновить все картинки на сайте до первоначального вида')); ?>
  <?php echo CHtml::link('Настройки <i class="glyphicon glyphicon-cog"></i>', array('settings/index', 'id'=>'shop'), array('class'=>'btn btn-warning')); */ ?>

  <?php
  if(D::role('sadmin') && D::isDevMode()) {
  	echo CHtml::link('Форма заказа <i class="glyphicon glyphicon-cog"></i>', array('shop/orderFields'), array('class'=>'btn btn-info'));
  }
  ?>


</div>
<div class="clr"></div>

<?php $this->renderPartial('_categories', compact('categories')); ?>
<? if($productDataProvider->getTotalItemCount() > 0): ?>
    <?php $this->renderPartial('_category_controls', ['categories'=>$categories, 'model'=>new \Category]); ?>
<? endif; ?>

<?php $this->renderPartial('_products', array(
    'productDataProvider'=>$productDataProvider, 
    'category'=>$model, 
    'modeRelatedHidden'=>$modeRelatedHidden,
)); ?>
