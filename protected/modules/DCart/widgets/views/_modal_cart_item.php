<?php
/** @var \DCart\widgets\CartWidget $this */
/** @var \DCart\components\DCart $cart */
/** @var string $hash cart item hash */
/** @var string $data cart item data */
?>
<tr class="dcart-mcart-item js-mcart-item" data-hash="<?=$hash?>">
	<td class="image">
		<?$itemLink = Yii::app()->createUrl('shop/product', array('id'=>$data[$cart->attributeId]));?>
		<?=\CHtml::link(CHtml::image($cart->getImage($hash)?:'http://placehold.it/36'), $itemLink)?>
	</td>
	<td class="cart-name">
		<?=\CHtml::link(
			$data['attributes'][$cart->attributeTitle],
			array('shop/product', 'id'=>$data[$cart->attributeId]),
			array('title'=>$data['attributes'][$cart->attributeTitle])
		)?>
		<?php
		// вывод дополнительных аттрибутов
		foreach($cart->getAttributes(true, false, true) as $attribute):
			list($label, $value) = $cart->getAttributeValue($hash, $attribute, true);
			if($value):?>
				<p><small>
					<?=D::c($label, mb_strtolower($label).':')?>
					<i><?=$value?></i>
				</small></p>
			<?endif;
		endforeach;
		?>
	</td>
	<td class="count">
		<div class="number">
			<span class="down"></span>
			<?=\CHtml::textField('count', $data['count'], array('data-hash'=>$hash, 'size'=>7,'maxlength'=>20));?>
			<span class="up"></span>
		</div>
	</td>
	<td>
		<span class="unit-price"><?=HtmlHelper::priceFormat($data['price'])?></span>
	</td>
	<td>
		<span class="total-price"><?=HtmlHelper::priceFormat($data['count']*$data['price'])?></span>
	</td>
	<td class="delete">
		<?if($this->hidePayButton):?>
			<?=\CHtml::image('/images/shop/delete.png','Удалить',array(
				'class'=>'dcart-mcart-btn-remove js-mcart-btn-remove',
				'title'=>'Удалить',
				'data-hash'=>$hash
			))?>
		<?else:?>
			<?=\CHtml::image('/images/shop/delete.png','',array('class'=>'dcart-mcart-btn-remove js-mcart-btn-remove','data-hash'=>$hash))?>
			<?=\CHtml::link('Удалить', $this->owner->createUrl('dCart/delete'), array(
				'class'=>'dcart-mcart-btn-remove js-mcart-btn-remove',
				'data-hash'=>$hash
			)); ?>
		<?endif?>
	</td>
</tr>