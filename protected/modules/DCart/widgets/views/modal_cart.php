<?php
/** @var \DCart\widgets\CartWidget $this */
/** @var \DCart\components\DCart $cart */
?>
<div class="dcart-mcart<?=D::c($this->hidePayButton,' in-content')?> js-dcart-mcart">
<?if($cart->isEmpty()):?>
	<div class="dcart-mcart-empty">
    	Ваша корзина пуста
	</div>
<?else:?>
    <table class="dcart-mcart-items" cellpadding="0" cellspacing="0" border="0">
    	<thead>
            <tr>
                <th class="cart-name" colspan="2">Наименование товара</td>
                <th class="count">Кол-во, шт</td>
                <th class="unit-price">Цена, руб</td>
                <th class="total-price">Итого, руб</td>
                <th class="delete">&nbsp;</td>
            </tr>
        </thead>
		<tbody class="js-mcart-items">
        <?foreach($cart->getData() as $hash=>$data) $this->render('_modal_cart_item', compact('cart', 'hash', 'data'));?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="6">
	                <div class="forget" colspan="3">Что-то забыли? <a href="/catalog">Хотите вернуться и положить еще?</a></div>
	               	<?if(!$this->hidePayButton):?>
		                <div class="cart-checkout">
	    	           		<a href="/order" class="checkout"><?=Yii::t('DCartModule.widgets', 'modalCart.btnCheckout')?></a>
		               	</div>
	               	<?endif?>
	                <div class="cart-total-price" colspan="<?=($this->hidePayButton)?3:2?>">
	                	<div class="wrapper">
	                		<span class="label">Итого: </span>
	                		<span class="dcart-total-price"><?=HtmlHelper::priceFormat($cart->getTotalPrice())?></span>
	                		<span> руб.</span>
	                	</div>
	                </div>
               	</td>
            </tr>
		</tfoot>
    </table>
<?endif?>
</div>