<?php if (count($products)): ?>
<div class="product-list">
    <?php foreach($products as $id=>$product): ?>
        <?php $last_in_row = ($id+1) % 4 == 0 ? true : false; ?>
        <div class="product<?php if ($product->sale) echo ' sale'; elseif ($product->new) echo ' new'; if ($last_in_row) echo ' last'; ?>">
            <div class="img">
                <?php if ($product->sale): ?>
                <div class="sale-img"></div>
                <?php elseif ($product->new): ?>
                <div class="new-img"></div>
                <?php endif; ?>
                
                <a href="<?php echo Yii::app()->createUrl('shop/product', array('id'=>$product->id)); ?>"><img src="<?php echo $product->mainImg; ?>" alt="" /></a>
            </div>
            <div class="text_info">
                <div class="title"><?php echo CHtml::link($product->title, array('shop/product', 'id'=>$product->id)) ?></div>
                <div class="code">Арт: <?php echo($product->code); ?></div>
                <div class="price"><?php echo $product->price; ?> руб.</div>
            </div>
            <div>
                <?php if ($product->notexist): ?>
                Нет в наличии
                <?php else: ?>
                <a class="shop-button to-cart" href="<?php echo Yii::app()->createUrl('shop/addtocart', array('id'=>$product->id)) ?>"><span>В корзину</span></a>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($last_in_row && $id+1 < count($products)) echo '<div class="row-separator"></div>'; ?>
    <?php endforeach; ?>
    <div class="clr"></div>
</div>
<?php else: ?>
<p>Нет товаров</p>
<?php endif;?>
