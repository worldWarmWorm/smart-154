<div id="shop-cart">
    <div class="wrap">
        <div class="module<?php if (!count($cart->products)) echo ' empty'; ?>">
            <div class="module-main">
                <div class="module-head">
                    <div class="summary">
                        <?php $this->render('_summary', compact('cart')); ?>
                    </div>
                </div>

                <div class="module-content">
                    <div id="cart-products">
                        <?php $this->render('_products', compact('cart')); ?>
                    </div>

                    <?php $this->render('_js'); ?>

                    <p class="clear-cart">
                        <?php echo CHtml::link('Очистить', array('shop/clearCart'), array('onclick'=>'return ShopCart.clear(this);')); ?>
                    </p>

                    <p class="goto-order">
                        <?php echo CHtml::link('Перейти к оформлению', array('shop/order'), array('class'=>'shop-button')) ?>
                    </p>
                    <p class="minimize"><a id="cart-minimize" class="link">Свернуть</a></p>
                </div>
            </div>
            <a class="cart-open-link"></a>
        </div>
    </div>
</div>
