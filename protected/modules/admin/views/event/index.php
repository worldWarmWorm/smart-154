<?php /** @var EventController $this **/ ?>
<?php /** @var [Event]CActiveDataProvider $eventDataProvider **/ ?>

<?php $this->pageTitle = $this->getEventHomeTitle().' - '. $this->appName; 

$this->breadcrumbs=array(
    $this->getEventHomeTitle()=>array('event/index'),
);


?>

<h1><?=$this->getEventHomeTitle()?></h1>

<div style="margin-bottom: 15px;">
  <?php echo CHtml::link('Добавить', array('create'), array('class'=>'btn btn-primary')); ?>
</div>

<?php if (!$eventDataProvider->getItemCount()): ?>
  <p><?=\Yii::t('AdminModule.event', 'emptyText')?></p>
  <?php else: ?>
  <table class="event-list table table-striped table-hover"> <!-- adminList -->
    <thead>
      <tr>
        <th>Название</th>
        <th style="width: 1%">Дата</th>
        <th style="width: 1%"></th>
      </tr>
    </thead>
    <tbody>
    <?php foreach($eventDataProvider->getData() as $event): ?>
      <tr id="event-<?php echo $event->id; ?>" class="row<?php echo $event->id % 2 ? 0 : 1; ?>">
        <td class="title">
          <?php echo CHtml::link($event->title, array('event/update', 'id'=>$event->id)); ?>
          <a title="Редактировать" class=" pull-right" href="<?=$this->createUrl('event/update', array('id'=>$event->id)) ?>"><span class="glyphicon glyphicon-pencil"></span></a>
        </td>
        <td><?php echo $event->date; ?></td>
        <td><?php echo CHtml::ajaxLink('Удалить', $this->createUrl('event/delete', array('id'=>$event->id)),
          array(
            'type'=>'post',
            'data'=>array('ajax'=>1),
            'beforeSend'=>'function() { return confirm("Подтвердите удаление"); }',
            'success'=>'function() {$("#event-'. $event->id .'").remove();}'
          ),
          array('class'=>'btn btn-danger btn-xs')
          ); ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table> 

  <?php $this->widget('CLinkPager', array(
    'pages' => $eventDataProvider->getPagination(),
    'htmlOptions'=>array('class'=>'pagination'),
    'header'=>false,
    'firstPageLabel'=>false,
    'lastPageLabel'=>false

  )); ?>
<?php endif; ?>
