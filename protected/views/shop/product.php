<?php CmsHtml::fancybox(); ?>
<h1><?=$product->getMetaH1()?></h1>

<div class="product-page">
	<div class="images">
		<?php if(!Yii::app()->user->isGuest) echo CHtml::link('редактировать', array('cp/shop/productUpdate', 'id' =>$product->id), array('class'=>'btn-product-edit', 'target' => 'blank'));?>
			<div class="js__main-photo product-main-img<?=HtmlHelper::getMarkCssClass($product, ['sale','new','hit'])?>">
				<?php if($product->mainImageBehavior->isEnabled()): ?>
					<?=CHtml::link(CHtml::image(ResizeHelper::resize($product->getSrc(), 310, 200)), $product->mainImageBehavior->getSrc(), ['class'=>'image-full', 'data-fancybox'=>'group']); ?>
					<?else:?>
					<img src="<?=ResizeHelper::resize($product->getSrc(), 310, 200); ?>" alt="">
					<?endif?>
				</div>

				<div class="more-images">
					<?foreach($product->moreImages as $id=>$img):?>
					<div class="more-images-item">
						<a class="image-full" data-fancybox="group" href="<?=$img->url?>" title="<?=$img->description?>"><?=CHtml::image(ResizeHelper::resize($img->tmbUrl, 300, 230), $img->description); ?></a>
					</div>
					<?endforeach?>
				</div>
			</div>
			<div class="options">

				<?if(!empty($product->brand_id)):?>
				<div class="product-brand">
					<strong>Бренд:</strong> <?=$product->brand->title?>
				</div>
				<?endif?>
				<?if(!empty($product->code)):?>
				<div class="product-code">
					<strong>Артикул:</strong> <?=$product->code?>
				</div>
				<?endif?>

<?/*

	<div class="product-params">

		<p><strong>Фотокамера (Мп):</strong> 12</p>
		<p><strong>Разрешение фотосъемки (пикс):</strong> 4000 x 3000</p>
		<p><strong>Автофокус:</strong> фазовый</p>
		<p><strong>Вспышка:</strong> светодиодная</p>
		<p><strong>Видеозапись:</strong> да</p>
		<p><strong>Разрешение видеосъемки (пикс):</strong> 1920 x 1080</p>
		<p><strong>Частота кадров видеосъемки:</strong> 30</p>
		<p><strong>Фронтальная камера (Мп):</strong> 8</p>
	</div>

*/?>



<?/*

	<div class="product-filter-block">
		<form action="">

			<div class="product-params-filter product-params-filter__color">
				<div class="product-params-filter-name">
					<strong>Выбор цвета:</strong>
				</div>
				<div class="product-params-filter-attr">
					<div class="product-params-filter-item"><input type="radio" id="pf-1-1" name="f1" /><label style="background-color: #D50000" for="pf-1-1"></label></div>
					<div class="product-params-filter-item"><input type="radio" id="pf-1-2" name="f1" /><label style="background-color: #F26521" for="pf-1-2"></label></div>
					<div class="product-params-filter-item"><input type="radio" id="pf-1-3" name="f1" /><label style="background-color: #FBC02D" for="pf-1-3"></label></div>
					<div class="product-params-filter-item"><input type="radio" id="pf-1-4" name="f1" /><label style="background-color: #588526" for="pf-1-4"></label></div>
					<div class="product-params-filter-item"><input type="radio" id="pf-1-5" name="f1" /><label style="background-color: #29B6F6" for="pf-1-5"></label></div>
					<div class="product-params-filter-item"><input type="radio" id="pf-1-6" name="f1" /><label style="background-color: #0E2FD7" for="pf-1-6"></label></div>
				</div>
			</div>
			<div class="product-params-filter product-params-filter__size">
				<div class="product-params-filter-name">
					<strong>Выбор размера:</strong>
				</div>
				<div class="product-params-filter-attr">
					<div class="product-params-filter-item"><input type="radio" id="pf-2-1" name="f2" /><label for="pf-2-1">120 х 130</label></div>
					<div class="product-params-filter-item"><input type="radio" id="pf-2-2" name="f2" /><label for="pf-2-2">130 х 130</label></div>
					<div class="product-params-filter-item"><input type="radio" id="pf-2-3" name="f2" /><label for="pf-2-3">130 х 140</label></div>
					<div class="product-params-filter-item"><input type="radio" id="pf-2-4" name="f2" /><label for="pf-2-4">140 х 140</label></div>
					<div class="product-params-filter-item"><input type="radio" id="pf-2-5" name="f2" /><label for="pf-2-5">140 х 150</label></div>
					<div class="product-params-filter-item"><input type="radio" id="pf-2-6" name="f2" /><label for="pf-2-6">150 х 150</label></div>
				</div>
			</div>
		</form>

	</div>

*/?>

<?if(D::yd()->isActive('shop') && (int)D::cms('shop_enable_attributes') && count($product->productAttributes)):?>
<div class="product-attributes">
	<ul>
		<?php foreach ($product->productAttributes as $productAttribute):?>
			<li><span><?=$productAttribute->eavAttribute->name;?></span><span><?=$productAttribute->value;?></span></li>
		<?php endforeach;?>
	</ul>
</div>
<?php endif;?>

<div class="buy">
	 <!--  <ul class="counter_number">
            <li class="counter_numbe_minus">-</li>
            <li class="counter_number_input">
                <input type="text" name="counter" value="1" class="counter_input total-num" maxlength="4">
            </li>
            <li class="counter_number_plus">+</li>
        </ul> -->
        <p class="order__price">Цена:
        	<? if(D::cms('shop_enable_old_price')): ?>
        	<?php if($product->old_price > 0): ?>
        		<span class="old_price"><?= HtmlHelper::priceFormat($product->old_price); ?> 
        			<i class="rub">руб</i>
        		</span>
        	<?php endif; ?>
        <? endif; ?>
        <span class="new_price"><?= HtmlHelper::priceFormat($product->price); ?>
        	<span class="rub">руб</span>
        </span>
    </p>
    
    

    <?if($product->notexist):?>
    нет в наличии
    <?else:?>
    <?php $this->widget('\DCart\widgets\AddToCartButtonWidget', array(
    	'id' => $product->id,
    	'model' => $product,
    	'title'=>'<span>В корзину</span>',
    	'cssClass'=>'btn shop-button to-cart button_1 js__photo-in-cart open-cart',
    	'attributes'=>[
								// ['count', '.counter_input'],
    	]
    	));
    	?>
    	<?endif?>
    </div>
</div>
<div class="clr"></div>

<?if(!empty($product->description)):?>
<div class="description">
	<?=$product->description?>
</div>
<?endif?>
</div>

<?if(D::cms('shop_enable_reviews')) $this->widget('widget.productReviews.ProductReviews', array('product_id' => $product->id))?>
