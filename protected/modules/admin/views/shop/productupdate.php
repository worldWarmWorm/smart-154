<?php $this->pageTitle = 'Редактирование товар - '. $this->appName; ?>

<h1>Редактирование товара</h1>

<?php echo $this->renderPartial('form_product_general', compact('model', 'fixAttributes', 'categoryList', 'relatedCategories')); ?>


<?php Yii::app()->clientscript->registerScriptFile($this->module->assetsUrl.'/js/admin_shop.js'); ?>
