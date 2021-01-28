<?php $this->pageTitle = \Yii::t('AdminModule.event', 'edit.title').' - '. $this->appName; 

$this->breadcrumbs=array(
    $this->getEventHomeTitle()=>array('event/index'),
    \Yii::t('AdminModule.event', 'edit.title').' - '.$model->title,
);


?>

<h1><?=\Yii::t('AdminModule.event', 'edit.title')?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
