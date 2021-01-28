
<?php

	#$smap = new \ext\sitemap\SitemapGenerator('http://dishman2.test'); 
	#$smap->generateSitemap();
	#die;

	renderMenuRecursive($items);
	function renderMenuRecursive($items) {
		$count=0;
		$n=count($items);

		foreach($items as $item)
		{
			$count++;
			$class=array();
			echo CHtml::openTag('li');
			$menu=renderMenuItem($item);
			echo $menu;

			if(isset($item['items']) && count($item['items']))
			{
				echo "\n".CHtml::openTag('ul')."\n";
				renderMenuRecursive($item['items']);
				echo CHtml::closeTag('ul')."\n";
			}
			echo CHtml::closeTag('li')."\n";
		}
	}


	function renderMenuItem($item)
	{	
		if(isset($item['url']))
		{
			$label=$item['label'];
			$products = Category::model()->findByPk((int)$item['url']['id']);
			$content = '';
			if(count($products->products)) {
				$content .= '<ul class="sitemap_page_category_tovar">';
				foreach($products->products as $product) {
					$content .= '<li>';
					$content .= CHtml::link($product->title ,Yii::app()->createUrl('shop/product', 
						array('id'=>$product->id)), 
						array(
							'title'=>!empty($product->alt_title) ? $product->alt_title : $product->title,
							'alt'=>!empty($product->alt_title) ? $product->alt_title : $product->title,
							)
						);
					$content .= '</li>';
				}
				$content .= '</ul>';
			}
			return CHtml::link($label,$item['url'],isset($item['linkOptions']) ? $item['linkOptions'] : array()).$content;
		}
		else
			return CHtml::tag('span',isset($item['linkOptions']) ? $item['linkOptions'] : array(), $item['label']);
	}

?>
