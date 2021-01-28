<?
/** @var BrandController $this */
/** @var Brand $brand */ 
/** @var array[Category] $categories */ 
/** @var CActiveDataProvider[Product] $productDataProvider */ 
?>
<h1><?=$brand->getMetaH1()?></h1>

<div class="brand__logo">
	<img src="<?= $brand->getSrc(); ?>" />
</div>
<div class="brand__description">
	<?= $brand->detail_text; ?>
</div>

<? //if($this->beginCache('brand__view', ['varyByParam'=>[$brand->id]])): ?>
<? if (!empty($categories)): ?>
<div class="brand__categories">
	<h2>Разделы каталога</h2>
	<ul>
	<? foreach($categories as $category): ?>
		<li>
			<a href="<?= $this->createUrl('shop/category', ['id'=>$category->id, 'brand_id'=>$brand->id]); ?>"><?= $category->title; ?></a>
			<span><?= Product::model()->visibled()->byBrand($brand->id)->byCategory($category->id)->count(); ?></span>
		</li>
	<? endforeach; ?>
	</ul>
</div>
<? endif; ?>
<? //$this->endCache(); endif; ?>

<? if($productDataProvider->getItemCount() > 0): ?>
<div id="product-list-module" class="brand__product-list">
<? $this->widget('widget.listView.DSizerListView', array(
	'dataProvider'=>$productDataProvider,
	'itemView'=>'//shop/_products',
	'enableHistory'=>true,
	'sorterHeader'=>'Сортировка:',
	'pagerCssClass'=>'pagination',
	'pager'=>[
		'class' => 'DLinkPager',
		'maxButtonCount'=>'5',
		'header'=>'',
	],
	'loadingCssClass'=>'loading-content',
	'itemsTagName'=>'div',
	'emptyText' => '<div class="category-empty">Нет товаров для отображения.</div>',
	'itemsCssClass'=>'product-list',
	'sortableAttributes'=>[
		'title',
		'price',
	],
	'id'=>'ajaxListView',
	'sizerHeader'=>'Показать: ',
	'sizerVariants'=>[15, 30, 60, 120],
	//'template'=>'{sizer}{sorter}{items}{pager}{sizer}<div class="sort-hidden">{sorter}</div>', // with sizer 
	'template'=>'{sorter}{items}{pager}<div class="sort-hidden">{sorter}</div>',
));
?>
</div>
<? endif; ?>

<?=HtmlHelper::linkBack('Назад', '/brands', '/brands')?>
