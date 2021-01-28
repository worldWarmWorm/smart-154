<?php $this->pageTitle = 'Новый блог - '. $this->appName; 
$this->breadcrumbs=array(
	'Страницы'=>array('page/index'),
	'Добавление блога',
);
?>

<h1 class="with-select">Добавление</h1>
<?php $this->widget('admin.widget.MenuTypes.MenuTypes'); ?>

<?php echo $this->renderPartial('_form', compact('model')); ?>
