<?php $this->pageTitle = 'Новый товар - '. $this->appName; ?>

<h1>Новый товар</h1>

<?php echo $this->renderPartial('form_product_general', compact('model', 'fixAttributes')); ?>
