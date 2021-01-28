<?php $this->pageTitle = 'Редактирование категории - '. $this->appName; ?>

<h1>Редактирование категории</h1>
<?php echo $this->renderPartial('form_category_general', compact('model')); ?>

<?php Yii::app()->clientscript->registerScriptFile($this->module->assetsUrl.'/js/admin_shop.js'); ?>
