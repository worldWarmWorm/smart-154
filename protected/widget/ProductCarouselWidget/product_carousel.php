<?php
/** @var $this ProductCarouselWidget */
/** @var $dataProvider CActiveDataProvider[Product] */
	Yii::app()->clientScript->registerScriptFile('/js/slick.min.js');
?>
<div class="product-carousel-block">
	<div class="product-carousel-header">
		<div class="carousel-name">
			<h3>Популярные товары</h3>
		</div>
		<div id="carousel-control" class="carousel-control"></div>
	</div>
	<div id="product-carousel" class="product-carousel">
		<? foreach($dataProvider->getData() as $data): ?>
			<div class="product-item">
				<? if(!\Yii::app()->user->isGuest) echo CHtml::link('редактировать', ['/cp/shop/productUpdate', 'id'=>$data->id], ['class'=>'btn-product-edit', 'target'=>'_blank']); ?>
				<div class="product<?=HtmlHelper::getMarkCssClass($data, array('sale','new','hit'))?>">
					<div class="flex-item-wrap">
						<div class="product_img product-block">
							<?=CHtml::link($data->img(200, 200), $productUrl); ?>
						</div>
						<div class="product_name product-block">
								<?=CHtml::link($data->title, $productUrl, array('title'=>$data->link_title)); ?>
						</div>
					</div> <!-- flex-item-wrap -end -->
					<div class="flex-item-wrap">
						<div class="product_price product-block">
				        	<?php if($data->price > 0): ?>
								<span>Цена: 
									<?php if($data->old_price > 0): ?> 
						    	    	<span class="old_price"><strike><?= HtmlHelper::priceFormat($data->old_price); ?></span> <span class="rub">руб</strike></span>
						        	<?php endif; ?>
									<i><?= HtmlHelper::priceFormat($data->price); ?></i> руб
								</span>
							<?php endif; ?>
						</div>
						<div class="to-cart-wrapper">
							<?if($data->notexist):?>
								Нет в наличии
							<?else:?>
								<?$this->widget('\DCart\widgets\AddToCartButtonWidget', array(
									'id' => $data->id,
									'model' => $data,
									'title'=>'<span>В корзину</span>', 
									'cssClass'=>'shop-button to-cart button_1 js__in-cart open-cart')); 
								?>
							<?endif?>
						</div>
					</div> <!-- flex-item-wrap -end -->


					<?/*

					<?if(D::yd()->isActive('shop') && (int)D::cms('shop_enable_attributes') && count($data->productAttributes)):?>
						<div class="product-attributes product-block">
							<ul>
								<?foreach ($data->productAttributes as $productAttribute):?>
									<li><span><?=$productAttribute->eavAttribute->name?></span><span><?=$productAttribute->value?></span></li>
								<?endforeach?>
							</ul>
						</div>
					<?endif?>

					*/?>
				</div>
			</div>
		<? endforeach; ?>
	</div>
</div>