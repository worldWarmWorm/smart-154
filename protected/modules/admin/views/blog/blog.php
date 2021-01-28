<?php $this->pageTitle = 'Блог '. $model->title .' - '. $this->appName; 
$this->breadcrumbs=array(
    'Страницы'=>array('page/index'),
    'Блог - '.$model->title,
);
?>
<div class="left">
    <h1><?=$model->title; ?></h1>
</div>
<div class="right">
    <?php echo CHtml::link('Изменить', array('update', 'id'=>$model->id), array('class'=>'btn btn-warning')); ?>
</div>
<div class="clr"></div>

<div style="margin-bottom: 15px;">
    <?php echo CHtml::link('Добавить статью', array('page/create', 'blog_id'=>$model->id), array('class'=>'btn btn-primary')); ?>
</div>

<?php if ($model->posts) : ?>
<table class="adminList table table-striped table-hover">
    <?php foreach($model->posts as $id=>$post): ?>
    <tr class="row<?php echo $id % 2 ? 0 : 1; ?>">
        <td><?php echo CHtml::link($post->title, array('page/update', 'id'=>$post->id)); ?></td>
        <td width="1%"><?php echo CHtml::link("Удалить", array('page/delete', 'id' => $post->id), array('class'=>'btn btn-danger btn-xs')); ?></td>
    </tr>
    <?php endforeach; ?>
</table>
<?php else: ?>
    <p>Нет статей!</p>
<?php endif; ?>
