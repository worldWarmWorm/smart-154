<?php $this->pageTitle = 'Редактирование альбома - '. $this->appName; ?>

<?php

$this->breadcrumbs=array(
    $this->getGalleryHomeTitle()=>array('gallery/index'),
    'Редактирование альбома',
);

?>

<h1>Редактирование альбома</h1>

<?php echo $this->renderPartial('gallery/_form', array('model'=>$model)); ?>
