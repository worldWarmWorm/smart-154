<?php
/** @var $this ProductCarouselWidget */
/** @var $dataProvider CActiveDataProvider[Product] */
?>
<div class="adaptive-product-carousel">
	<div id="adaptive-product-carousel">
		<? foreach($dataProvider->getData() as $data): ?>
			<figure class="adaptive-product__item">
                <ul class="product adaptive-product<?= array_reduce(['sale','hit','new'],function($r,$v)use($data){return $r.(($data->$v)?" {$v}":'');}); ?>">
					<li class="product_img">
                        <?= CHtml::link(
                                CHtml::image($data->mainImg, $data->alt_title?:$data->title, ['title'=>$data->alt_title?:$data->title]), 
                                Yii::app()->createUrl('shop/product', ['id'=>$data->id])
                            ); ?>
					</li>
					<li class="product_name">
						<figcaption>
                            <?= CHtml::link($data->title, ['shop/product', 'id'=>$data->id], ['title'=>$data->link_title]); ?>
						</figcaption>
					</li>
					<li class="product_price">
						<span><?=D::c(($data->price > 0), 'Цена: <i>'. HtmlHelper::priceFormat($data->price).'</i> руб.')?></span>
					</li>
					<li class="product_button">
                        <?if($data->notexist):?>
                            Нет в наличии
                        <?else:?>
                        <?$this->widget('\DCart\widgets\AddToCartButtonWidget', array(
                            'id' => $data->id,
                            'model' => $data,
                            'title'=>'<span>В корзину</span>', 
                            'cssClass'=>'btn btn-default shop-button to-cart open-cart')); 
                        ?>
                        <?endif?>					
					</li>
				</ul>
			</figure>
		<? endforeach; ?>
	</div>
	<div class="adaptive-product_control"></div>
</div>