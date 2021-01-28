<?php $this->pageTitle = 'Редактирование ссылки - '. $this->appName; 
$this->breadcrumbs=array(
    'Страницы'=>array('page/index'),
	'Редактирование ссылки - '.$model->title
);
?>
<h1>Редактирование ссылки</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
