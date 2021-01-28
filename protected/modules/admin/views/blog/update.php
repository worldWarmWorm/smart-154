<?php $this->pageTitle = 'Редактирование блога - '. $this->appName; 
$this->breadcrumbs=array(
    'Страницы'=>array('page/index'),
    'Редактирование блога - '.$model->title,
);
?>
<h1>Редактирование блога</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
