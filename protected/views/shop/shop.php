<?
use settings\components\helpers\HSettings;
$shopSettings=HSettings::getById('shop');

if(D::role('admin')) CmsHtml::editPricePlugin();?>
<h1><?=$shopSettings->meta_h1 ?: D::cms('shop_title',Yii::t('shop','shop_title'))?></h1>

<? if(D::cms('shop_show_categories') && $shopSettings->show_categories_on_shop_page): ?>
    <? $this->widget('widget.catalog.CategoryListWidget'); ?>
<? endif; ?>

<?if($shopSettings->main_text):?>
<div id="category-description" class="category-description"><?=$shopSettings->main_text?></div>
<?endif?>


<div id="product-list-module">
	<?php 
		$this->widget('zii.widgets.CListView', array(
		    'dataProvider'=>$dataProvider,
		    'itemView'=>'_products', 
		    'sorterHeader'=>'Сортировка:',
		    'itemsTagName'=>'div',
		    'emptyText' => '',
		    'itemsCssClass'=>'product-list row',
		    'sortableAttributes'=>array(
		        'title',
		        'price',
		    ),
		    'afterAjaxUpdate'=>'function(){}',
		    'id'=>'ajaxListView',
		    'template'=>'{items}',
		    'ajaxUpdate'=>true,
		    'enableHistory' => true,
		));
	?>	
</div>

<?if($shopSettings->main_text2):?>
<div id="category-description" class="category-description"><?=$shopSettings->main_text2?></div>
<?endif?>
