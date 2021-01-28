<?php
/** @var $this ProductCarouselWidget */
/** @var $dataProvider CActiveDataProvider[Product] */
	Yii::app()->clientScript->registerScriptFile('/js/slick.min.js');
?>
<div class="product-carousel-block">
	<div class="product-carousel-header">
		<!-- <div class="carousel-name">
			<h3>Популярные товары</h3>
		</div> -->
		<div id="carousel-control" class="carousel-control"></div>
	</div>
	<div id="product-carousel" class="product-carousel">
		<?php foreach($dataProvider->getData() as $data): ?>
			<?php
			$this->controller->renderPartial('//shop/_products', [
				'data' => $data
			]);
			?>
		<?php endforeach; ?>
	</div>
</div>