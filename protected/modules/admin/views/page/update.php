<?php 
$this->pageTitle='Редактирование страницы - '. $this->appName; 
$this->breadcrumbs=array('Страницы'=>array('page/index'));
if($model->blog) {
	$this->breadcrumbs[$model->blog->title]=array('blog/index', 'id'=>$model->blog->id);
	$this->breadcrumbs[]='Редактирование статьи - '.$model->title;
}
else $this->breadcrumbs[]='Редактирование страницы - '.$model->title;
?>

<h1>Редактирование страницы</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
