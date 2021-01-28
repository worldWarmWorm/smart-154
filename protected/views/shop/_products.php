<?
/**
 * @var Product $data
 */
$cache=\Yii::app()->user->isGuest;
if(!$cache || $this->beginCache('shop__product_card', ['varyByParam'=>[$data->id]])): // cache begin

if(empty($category)) $categoryId=$data->category_id;
else $categoryId=$category->id;
$productUrl=Yii::app()->createUrl('shop/product', ['id'=>$data->id, 'category_id'=>$categoryId]);
?>
<div class="product-item">
	<? if(!\Yii::app()->user->isGuest) echo CHtml::link('редактировать', ['/cp/shop/productUpdate', 'id'=>$data->id], ['class'=>'btn-product-edit', 'target'=>'_blank']); ?>
	<div class="product<?=HtmlHelper::getMarkCssClass($data, array('sale','new','hit'))?>">
		<div class="product__image product-block">
			<?=CHtml::link(CHtml::image(ResizeHelper::resize($data->getSrc(), 205, 165)), $productUrl); ?>
		</div>
		<div class="product__title product-block">
				<?=CHtml::link($data->title, $productUrl, array('title'=>$data->link_title)); ?>
		</div>
		<div class="product__price">
        	<?php if($data->price > 0): ?>
				<p class="order__price">Цена:
                    <? if(D::cms('shop_enable_old_price')): ?>
					<?php if($data->old_price > 0): ?>
						<span class="old_price"><?= HtmlHelper::priceFormat($data->old_price); ?> 
							<i class="rub">руб</i>
						</span>
					<?php endif; ?>
                    <? endif; ?>
					<span class="new_price"><?= HtmlHelper::priceFormat($data->price); ?>
						<i class="rub">руб</i>
					</span>
				</p>
			<?php endif; ?>
		</div>
		<div class="product__to-cart">
			<?if($data->notexist):?>
				Нет в наличии
			<?else:?>
				<?$this->widget('\DCart\widgets\AddToCartButtonWidget', array(
					'id' => $data->id,
					'model' => $data,
					'title'=>'<span>В корзину</span>',
					'cssClass'=>'shop-button to-cart button_1 js__in-cart open-cart',
					'attributes'=>[
						// ['count', '#js-product-count-' . $data->id],
					]
				));
				?>
			<?endif?>
		</div>
	</div>
</div>

<? if($cache) { $this->endCache(); } endif; // cache end ?>
