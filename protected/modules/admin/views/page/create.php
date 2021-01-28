<?php $this->pageTitle = 'Добавление страницы - '. $this->appName; 
$this->breadcrumbs=array('Страницы'=>array('page/index'));
if($model->blog) {
	$this->breadcrumbs[$model->blog->title]=array('blog/index', 'id'=>$model->blog->id);
	$this->breadcrumbs[]='Добавление статьи';
}
else $this->breadcrumbs[]='Добавление страницы';
?>

<h1 class="with-select">Добавление</h1>
<?php $this->widget('admin.widget.MenuTypes.MenuTypes'); ?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
