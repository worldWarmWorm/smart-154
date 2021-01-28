
<div class="sitemap_page">
	<h1>Карта сайта</h1>
	<h3>Меню сайта</h3>
	<?if(D::yd()->isActive('treemenu')) {
		$this->widget('\menu\widgets\menu\MenuWidget', array(
			'rootLimit'=>D::cms('menu_limit'),
			'cssClass'=>'sitemap_page_page clearfix'
		));
	} else {
		$this->widget('zii.widgets.CMenu', array(
			'items'=>$this->menu,
	        'linkLabelWrapper' => 'span',
			'htmlOptions'=>array('class'=>'sitemap_page_page clearfix'),
		));
	}?>

	<?if(D::yd()->isActive('shop')): ?>
	<h3>Категории</h3>
		<ul class="sitemap_page_category">
			<?$this->widget('\widget\ShopCategories\SiteMap', array('listClass'=>''))?>
		</ul>
	<?endif;?>
	<div>
		<?=D::cms('sitemap'); ?>
	</div>
</div>