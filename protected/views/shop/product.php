<?php
CmsHtml::fancybox();
$offers = $product->dataAttributeBehavior->get(true);
?>

<div class="product-page">
	<div class="images">
		<?php
		if (!Yii::app()->user->isGuest) {
			echo CHtml::link('редактировать', array('cp/shop/productUpdate', 'id' => $product->id), array('class' => 'btn-product-edit', 'target' => 'blank'));
		}
		?>

		<div class="js__main-photo product-main-img<?=HtmlHelper::getMarkCssClass($product, ['sale', 'new', 'hit'])?>">
			<?php if ($product->mainImageBehavior->isEnabled()): ?>
				<a href="<?= $product->mainImageBehavior->getSrc() ?>" class="image-full" data-fancybox="group">
					<img src="<?= $product->getSrc() ?>" alt="">
				</a>
			<?php else: ?>
				<img src="<?= $product->getSrc() ?>" alt="">
			<?php endif?>
		</div>

		<div class="more-images">
			<?foreach ($product->moreImages as $id => $img): ?>
			<div class="more-images-item">
				<a class="image-full" data-fancybox="group" href="<?=$img->url?>" title="<?=$img->description?>"><?=CHtml::image(ResizeHelper::resize($img->tmbUrl, 300, 230), $img->description);?></a>
			</div>
			<?endforeach?>
		</div>
	</div>

	<div class="options">
		<div class="buy">
			<h1><?=$product->getMetaH1()?></h1>

			<?php if (!empty($product->brand_id)): ?>
				<div class="product-brand">
					<strong>Бренд:</strong> <?=$product->brand->title?>
				</div>
			<?php endif; ?>

			<?php if (!empty($product->code)): ?>
				<div class="product-code">
					<strong>Артикул:</strong> <?=$product->code?>
				</div>
			<?php endif; ?>

			<?php if ($offers) : ?>
				<div class="offers offers_color">
					<p>Цвет</p>

					<div class="product-offers" id="product<?= $data->id ?>-offers">
					<?php foreach ($offers as $key => $offer) : ?>
						<div class="product-offer">
							<label>
								<input 
									id="product<?= $data->id ?>-offer-<?= $key ?>" 
									class="js-offer"
									type="radio"
									name="offer-<?= $data->id ?>"
									value="<?= $offer['title'] ?>"
									<?= $key ? '' : 'checked' ?>
								>
								<div class="radio-custom" style="background-color: <?= $offer['hex'];?>;"></div>
							</label>
						</div>
					<?php endforeach ?>
					</div>
				</div>
			<?php endif; ?>

			<div class="order__price">
				<?php if (D::cms('shop_enable_old_price')): ?>
					<?php if ($product->old_price > 0): ?>
						<span class="old_price"><?=HtmlHelper::priceFormat($product->old_price);?> &#8381;/шт.</span>
					<?php endif; ?>
				<?php endif;?>
				<span class="new_price">
					<?=HtmlHelper::priceFormat($product->price);?><span class="rub"> &#8381;/шт.</span>
				</span>
			</div>
			
			<div class="product-btn-pannel">
				<div class="items-counter">
					<button class="count-down">-</button>
					<input type="text" readonly class="amount-to-cart" id="js-product-count-<?= $data->id ?>" value="1">
					<button class="count-up">+</button>
				</div>

				<?php if ($product->notexist): ?>
					нет в наличии
				<?php else: ?>
					<?php $this->widget('\DCart\widgets\AddToCartButtonWidget', [
					'id' => $product->id,
					'model' => $product,
					'title' => '
						<span>
							<svg width="28" height="31" viewBox="0 0 28 31" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M6 2L2 7.33333V26C2 26.7072 2.28095 27.3855 2.78105 27.8856C3.28115 28.3857 3.95942 28.6667 4.66667 28.6667H23.3333C24.0406 28.6667 24.7189 28.3857 25.219 27.8856C25.719 27.3855 26 26.7072 26 26V7.33333L22 2H6Z" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M2 7.33337H26" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M19.3334 12.6666C19.3334 14.0811 18.7715 15.4377 17.7713 16.4379C16.7711 17.4381 15.4146 18 14.0001 18C12.5856 18 11.229 17.4381 10.2288 16.4379C9.22865 15.4377 8.66675 14.0811 8.66675 12.6666" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
							</svg> Купить
						</span>
					',
					'cssClass' => 'shop-button to-cart button_1 js__photo-in-cart open-cart',
					'attributes' => [
						['count', '.amount-to-cart'],
						['offer', "#product{$data->id}-offers .js-offer:checked"]
					],
				]);?>
				<?php endif; ?>
			</div>
				
			<?php if (!empty($product->description)): ?>
				<h3>Описание</h3>
				<div class="description">
					<?=$product->description?>
				</div>
			<?php endif?>

			<?php if (D::yd()->isActive('shop') && (int) D::cms('shop_enable_attributes') && count($product->productAttributes)): ?>
				<h3>Характеристики</h3>
				<div class="product-attributes">
					<?php foreach ($product->productAttributes as $productAttribute): ?>
						<div class="product-attribute">
							<span class="product-attribute__name">
								<span><?=$productAttribute->eavAttribute->name;?></span>
							</span>
							<span class="product-attribute__value"><?=$productAttribute->value;?></span>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif;?>
    	</div>
	</div>
</div>

<?if (D::cms('shop_enable_reviews')) {
    $this->widget('widget.productReviews.ProductReviews', array('product_id' => $product->id));
}?>


