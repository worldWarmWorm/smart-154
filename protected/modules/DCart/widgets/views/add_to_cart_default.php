<?php
/** @var \DCart\widgets\AddToCartButtonWidget $this */
/** @var string $modelClass имя класса добавляемой в корзину модели */
?>
<?php echo \CHtml::link($this->title, 'javascript:;', array(
	'class' => $this->cssClass . ' dcart-add-to-cart-btn',
	'data-dcart-model' => $modelClass,
	'data-dcart-attributes' => $this->getAttributesAsJSON(),
    'rel'=>'nofollow',
	'data-item'=>$this->id
)); ?>

