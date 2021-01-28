<?
use settings\components\helpers\HSettings;
$shopSettings=HSettings::getById('shop');

if(D::role('admin')) CmsHtml::editPricePlugin();
?>
<h1><?=$category->getMetaH1()?></h1>

<? if(!empty($brand)): ?>
	<div class="brand__category">
		<h2><span>Бренд</span><?=CHtml::link($brand->title, '/brands/'.$brand->alias);?></h2>
		<div class="brand__info">
			<div class="brand__logo"><img src="<?= $brand->getSrc(); ?>" /></div>
			<div class="brand__preview-text"><?= $brand->preview_text; ?></div>
		</div>
	</div>
<? else: ?>
    <? if(D::cms('shop_show_categories') && $category->isShowCategoriesList()): ?>
        <? $this->widget('widget.catalog.CategoryListWidget', ['id'=>$category->id]); ?>
    <? endif; ?>
<? endif; ?>

<?if($category->description && (D::cms('shop_pos_description') <> 1)):?>
<div id="category-description" class="category-description">
    <?=$category->description?>
</div>
<?endif?>

<div id="product-list-module">
	<?$this->renderPartial('_products_listview', compact('model', 'category'))?>
</div>

<?if($category->description && (D::cms('shop_pos_description') == 1)):?>
<div id="category-description" class="category-description">
    <?=$category->description?>
</div>
<?endif?>
