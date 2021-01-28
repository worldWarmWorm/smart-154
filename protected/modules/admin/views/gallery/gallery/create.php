<?php $this->pageTitle = 'Новый альбом - '. $this->appName; ?>

<?php

$this->breadcrumbs=array(
    $this->getGalleryHomeTitle()=>array('gallery/index'),
    'Добавление альбома',
);

?>

<h1>Добавление альбома</h1>

<?php echo $this->renderPartial('gallery/_form', compact('model')); ?>
