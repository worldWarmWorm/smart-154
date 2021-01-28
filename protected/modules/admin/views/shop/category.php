<?php 
/** @var boolean $modeRelatedHidden режим без отображения привязанных товаров */

\Yii::app()->getModule('common');  

$this->pageTitle = D::cms('shop_title').' / Категория '. $model->title . ' - '. $this->appName; 
$breadcrumbs[] = $model->title;
$this->breadcrumbs = $breadcrumbs;
?>
<? if($model->productCount != $model->getProductsCount(null, D::cms('shop_category_descendants_level')?:0)): ?>
<div class="row pull-right" style="margin-top: 5px;">
	<div class="checkbox">
		<? $this->widget('\common\widgets\form\SwitchField', [
			'name'=>'switch-hide-related-products', 
			'label'=>'Скрыть вложенные и привязанные товары ',
			'checked'=>(!empty($modeRelatedHidden) && $modeRelatedHidden),
			'labelBefore'=>true,
			'wrapperTag'=>'',
			'switchOptions'=>[
				'size'=>'mini',
				'onColor'=>'success', 
				'offColor'=>'danger'
			],
			'htmlOptions'=>[
				'data-js'=>'switch-hide-related-products'
			]
		]); ?>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
	$(document).on("switchChange.bootstrapSwitch", "[data-js='switch-hide-related-products']", function(e) {
		window.location.href="?hide_related="
			+ ($("[data-js='switch-hide-related-products']").is(":checked") ? 1 : 0);
	});
});
</script>
<? endif; ?>

<h1><?php echo $model->title; ?></h1>

<?php $this->renderPartial('_categories', compact('categories', 'model')); ?>

<?php $this->renderPartial('_category_controls', compact('categories', 'model')); ?>

<?php $this->renderPartial('_products', array(
    'productDataProvider'=>$productDataProvider, 
    'category'=>$model, 
    'modeRelatedHidden'=>$modeRelatedHidden,
)); ?>
