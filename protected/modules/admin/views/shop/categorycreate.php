<?php $this->pageTitle = 'Новая категория - '. $this->appName; ?>

<h1>Новая категория</h1>
<?php echo $this->renderPartial('form_category_general', compact('model')); ?>
