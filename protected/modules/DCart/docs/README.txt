Использование модуля DCart
--------------------------
1. Виджет мини-корзины:
<?php $this->widget('\DCart\widgets\MiniCartWidget'); ?>

2. Стандартный виджет корзины:
<?php $this->widget('\DCart\widgets\СartWidget'); ?>

3. Виджет кнопки добавления в корзину
Простое использование:
Пример 1:
<?php $this->widget('\DCart\widgets\AddToCartButtonWidget', array(
	'id' => $model->id,
	'model' => $model
)); ?>

Пример 2:
<?php $this->widget('\DCart\widgets\AddToCartButtonWidget', array(
	'id' => $product->id,
	'model' => $product,
	'title'=>'<span>В корзину</span>', 
	'cssClass'=>'shop-button to-cart')); 
?>

Пример разширенного использования, для товаров с дополнительными параметрами:
<?php $this->widget('\DCart\widgets\AddToCartButtonWidget', array(
	'id' => $product->id,
	'model' => $product,
	'title' => '<span>Купить</span>', 
	'cssClass' => 'shop-button to-cart',
	'attributes' => array(
		array($product->attributeColor->attributeOne, $product, $product->attributeColor->attribute),
		array($product->attributeTSize->attributeOne, $product, $product->attributeTSize->attribute),
	)
)); ?>

В последних версиях доступно следующая установка значений атрибутов для AddToCartButtonWidget
'attributes'=>[ 
    ['count', 'js:(function(){ return "myValue"; })'
]

Пример подключения и использования дополнительных атрибутов с изменением цены
Сопутствующие товары:
1) protected\config\defaults.php
    ...
	'modules'=>array(
		...
        'common'=>['modules'=>['crud'=>['config'=>[
        	'ecommerce_concurrent_goods'=>'ecommerce.modules.concurrentGoods.config.crud.concurrent_goods_mini'
        ]]]],

2) protected\config\params.php
	...
    'backend'=>['menu'=>['catalog'=>['crud'=>['ecommerce_concurrent_goods']]]],
    ...
                
3) protected\config\dcart.php
return array(
	'class' => '\DCart\components\DCart',
	'attributeImage' => 'mainImg',
	'extendKeys' => ['cart_concurrent_goods'], 
	'cartAttributes' => ['cart_concurrent_goods'],
	'attributes' => [
		'code', 
		'cart_concurrent_goods',
		'cart_concurrent_goods_ids'=>['onAfterAdd'=>'afterAddCart']
	]
);

4) protected\models\Product.php
use ecommerce\modules\concurrentGoods\models\ConcurrentGoods;

class Product extends \common\components\base\ActiveRecord
{
    public $cart_concurrent_goods;
    public $cart_concurrent_goods_ids;
    
    public function behaviors()
    {
        return array(
        	'concurrentGoodsBehavior'=>[
        		'class'=>'\common\behaviors\ARAttributeListBehavior',
        		'attribute'=>'concurrent_goods',
        		'attributeLabel'=>'Сопутствующие товары',
        		'rel'=>'\ecommerce\modules\concurrentGoods\models\ConcurrentGoods',
        		'searchAttribute'=>'id',
        		'cacheTime'=>HCache::YEAR
        	]	
        );
    }

    public function rules() {
        return [
            ['cart_concurrent_goods, cart_concurrent_goods_ids', 'safe']
        ];
    }
    
    public function attributeLabels()
    {
        return $this->getAttributeLabels([
            'cart_concurrent_goods'=>'Дополнительно',
        ]);
    }

    /**
     * Event: onAfterAdd 
     * Обработчик события после добавления товара в корзину.
     * @param array &$item элемент позиции в массиве конфигурации корзины.
     * @param string $attribute имя атрибута. 
     * @param mixed $value значение атрибута.
     */
    public function afterAddCart(&$item, $attribute, $value)
    {
    	if(!empty($value) && ($attribute == 'cart_concurrent_goods_ids')) {
    		foreach(explode(';', $value) as $id) {
    			if($model=ConcurrentGoods::modelById($id, ['scopes'=>'activly'])) {
    				$item['price'] += (float)$model->price;
    			}
    		}
    	}
    }
    
5) protected\views\dOrder\order.php
$this->widget('\DOrder\widgets\actions\OrderWidget', array(
	'mailAttributes' => array('cart_concurrent_goods'),
	'adminMailAttributes' => array('cart_concurrent_goods')
));

6) views\shop\product.php

<?php
use common\components\helpers\HYii as Y;
use common\components\helpers\HHtml;
Y::module('common')->publishJs('js/kontur/common/tools/number_format.js'); 
?>

...

<div class="concurrent_goods">
    <? $this->widget('\ecommerce\modules\concurrentGoods\widgets\ConcurrentGoodsList', [
    	'name'=>'concurrent_goods',
    	'ids'=>$product->concurrent_goods,
		'select'=>'price',
		'criteria'=>['scopes'=>'activly'],
		'textField'=>function(&$model, $attribute) {
			$price=HHtml::price($model->price);
			$model->$attribute.=" <span>{$price} руб.</span>"
				. "<span data-id='{$model->id}' data-price='".(float)$model->price."' style='display:none'>{$model->title}</span>";
		}
	]); ?>
</div>
<? Y::js(null, ';$(document).on("change", "#concurrent_goods :checkbox", function(e) {
    var total=parseFloat($("#product-price").data("price")), price=0;
    $("#concurrent_goods :checked").each(function(){ 
        price=parseFloat($(this).parent().find("span[data-id=\'"+$(this).val()+"\']").data("price"));
        total+=isNaN(price) ? 0 : price;
    });
    $("#product-price").html(total.format(2));
});'); ?>
    
    ...
    
    <div class="buy">
    	<input type="number" id="product-count" value="1" />
        <?if($product->price > 0 || D::role('admin')):?> 
    	    <span class="price" id="product-price" data-price="<?=(float)$product->price?>">
    	    	<?=HtmlHelper::priceFormat($product->price)?>
    	    </span> <span class="rub">руб</span>
        <?endif?>
        <?if($product->notexist):?>
	        нет в наличии
        <?else:?>
              <?php $this->widget('\DCart\widgets\AddToCartButtonWidget', array(
	            'id' => $product->id,
    	        'model' => $product,
        	    'title'=>'<span>В корзину</span>',
            	'cssClass'=>'btn btn-default shop-button to-cart open-cart',
          		'attributes'=>[
          			['count', '#product-count'],
          			['cart_concurrent_goods', 'js:window.get_concurrent_goods', ';window.get_concurrent_goods=function() {'
          				. 'var concurrent_goods=""; $("#concurrent_goods :checked").each(function() {'
          				. 'concurrent_goods+= (concurrent_goods.length ? ", " : "") + $(this).parent().find("span[data-id=\'"+$(this).val()+"\']").text();'
          				. '}); return concurrent_goods;}' ],	
          			['cart_concurrent_goods_ids', 'js:window.get_concurrent_goods_ids', ';window.get_concurrent_goods_ids=function() {'
          				. 'var ids=""; $("#concurrent_goods :checked").each(function() {ids+= (ids.length ? ";" : "") + $(this).val();}); return ids;}' 
			        ]	
          		]          		
          )); ?>
        <?endif?>
    </div>
