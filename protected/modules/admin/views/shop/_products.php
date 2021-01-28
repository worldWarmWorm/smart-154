<?
/** @var boolean $modeRelatedHidden режим без отображения привязанных товаров */

use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;

\Yii::app()->getModule('common');  

/** @var boolean $isIndexPage главная страница каталога */
$isIndexPage=YiiHelper::isAction($this,'shop','index');
$modeRelatedHidden=(!empty($modeRelatedHidden) && $modeRelatedHidden);
?>

<? if(!$modeRelatedHidden) $this->widget('\common\ext\sort\widgets\Sortable', [
    'options'=>[
        'category'=>'shop_category',
        'key'=>empty($category) ? null : $category->id,
        'level'=>$productDataProvider ? R::get($productDataProvider->getPagination()->pageVar, 0) : 0,
        'saveUrl'=>$this->createUrl('saveProductSort'),
        'selector'=>'#product-list'
    ]
]); ?>
<script type="text/javascript">
$(document).ready(function() {
    $("#site-menu").disableSelection();
});
</script>
<? if($isIndexPage): ?>
<h2>Товары на главной странице каталога</h2>
<?endif?>
<div id="product-list-module">
  <?php if ($productDataProvider && ($productDataProvider->getItemCount() > 0)): ?>
  <ul id="product-list" class="product-list row">
    <?php foreach($productDataProvider->getData() as $product): ?>
    <li id="item_<?php echo $product->id ?>" data-sort-id="<?= $product->id; ?>" class="col-xs-3<? if(!$isIndexPage && ($product->category_id <> $category->id)) echo ' bg-warning'; ?><? if($product->hidden) echo ' mark-hidden'; ?>">
      <div class="product thumbnail">
        <div class="checkbox">
            <?= CHtml::checkBox('Product_CheckBox['.$product->id.']', isset($_POST['Product_CheckBox'][$product->id]), [
                'value'=>$product->id,
            ]); ?>
        </div>
        <div class="img">
        	<?= CHtml::link($product->img(195, 195), ['shop/productUpdate', 'id'=>$product->id]); ?>
        </div>
        <div class="caption">
          <p class="title" title="<?php echo $product->title ?>"><?php echo Chtml::link($product->title, array('shop/productUpdate', 'id'=>$product->id)); ?></p>
          <?= CHtml::link('<div class="btn btn-default btn-sm"><span class="price">'.$product->price.'</span> руб.</div>', ['shop/productUpdate', 'id'=>$product->id]); ?>
          <?= CHtml::link('Удалить', ['shop/productDelete', 'id'=>$product->id], [
          	'class'=>'btn btn-danger btn-sm pull-right',
          	'onclick'=>"return confirm('Вы действительно хотите удалить товар?')"
		  ]); ?>
        </div> 
      </div>  
    </li>
    <?php endforeach; ?>
  </ul>
  <? if(!$isIndexPage && !$modeRelatedHidden && ($category->productCount != $category->getProductsCount(false, true))): ?>
  <div class="well well-sm">
	<span class="label bg-warning table-bordered" style="color:#000">Цвет фона карточки</span> вложенного или привязанного товара.
  </div>
  <? endif; ?>
  <?php else: ?>
    <p>Нет товаров</p>
  <?php endif; ?>
</div>

<?php 
if($productDataProvider):
    $this->widget('CLinkPager', array(
        'pages'=>$productDataProvider->getPagination(),
        'nextPageLabel'=>'&gt;',
        'prevPageLabel'=>'&lt;',
        'header'=>false,
        'cssFile'=>false,
        'htmlOptions'=>array('class'=>'pagination')
    )); 
endif; 
?>
