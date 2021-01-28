<h1><?=$page->getMetaH1()?></h1>

<div class="shop-filter-page">
	<? if($filterData=HFilter::rangeof()): ?>
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<div class="shop-filter-block">
				<div class="filter-header">
					<p>Выбор по параметрам</p>
				</div>
				<div class="row">
					<? $this->widget('widget.filters.ProductFilter', ['data'=>$filterData]); ?>			
				</div>				
			</div>			
		</div>
	</div>
	<? endif; ?>
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<? $this->widget('\extend\modules\pages\widgets\ListWidget', [
				'cid'=>'rangeof', 
				'sort'=>'rangeof', 
				'view'=>'theme.rangeof.rangeof_list',
				'options'=>['pagination'=>['pageSize'=>9999999]]
			]); ?>
			<div id="product-list-module">
				<? $dataProvider=\Product::model()->getDataProvider([
					'criteria'=>['condition'=>'LENGTH(rangeof)>0'],
					'pagination'=>['pageSize'=>12, 'pageVar'=>'p']
				]); ?>
				<? $this->renderPartial('//shop/_products_listview', compact('dataProvider')); ?>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<div class="page__detail-text"><?= $page->text; ?></div>
		</div>
	</div>
</div>
