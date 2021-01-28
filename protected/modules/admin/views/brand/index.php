<?php
/* @var $this BrandController */
/** @var [Brand]CActiveDataProvider $dataProvider **/ 
?>
<?$t=\YiiHelper::createT('AdminModule.brand')?>
<?$ta=\YiiHelper::createT('AdminModule.admin')?>

<h1><?= $t('title'); ?></h1>

<div style="margin-bottom: 15px;">
  <?= CHtml::link($ta('btn.add'), array('create'), array('class'=>'btn btn-primary')); ?>
  <?= CHtml::link('<span class="glyphicon glyphicon-cog"></span>&nbsp;'.$ta('btn.settings'), ['brand/settings'], ['class'=>'btn btn-warning pull-right']); ?>
</div>

<?if(!$dataProvider->getItemCount()):?>
  <p><?=$t('emptyText')?></p>
<?else:?>
  <table class="event-list table table-striped table-hover"> <!-- adminList -->
    <thead>
      <tr>
        <th class="col-md-1 text-center"><?=$t('table.header.logo')?></th>
        <th><?=$t('table.header.title')?></th>
        <th><?=$t('table.header.alias')?></th>
        <th class="col-md-1 text-center"><?=$t('table.header.active')?></th>
        <th class="col-md-1 text-center"></th>
      </tr>
    </thead>
    <tbody>
    <?foreach($dataProvider->getData() as $item):?>
      <tr id="brand-<?=$item->id?>" class="row<?=($item->id % 2) ? 0 : 1?>">
        <td class="text-center"><img src="<?=$item->getSrc()?>" style="max-width:100px;max-height:100px" /></td>
        <td class="title">
          <?=CHtml::link($item->title, array('brand/update', 'id'=>$item->id))?>
          <a title="<?=$ta('a.edit.title')?>" class=" pull-right" href="<?=$this->createUrl('brand/update', array('id'=>$item->id)) ?>"><span class="glyphicon glyphicon-pencil"></span></a>
        </td>
        <td class="text-center"><?=$item->alias?></td>
        <td class="text-center">
        	<?$this->widget('\admin\widget\form\ActiveInList', array(
        		'behavior'=>$item->activeBehavior, 
        		'changeUrl'=>$this->createUrl('brand/changeActive', array('id'=>$item->id)), 
        		'cssMark'=>'unmarked', 
        		'cssUnmark'=>'marked', 
        		'wrapperOptions'=>array('class'=>'mark', 'title'=>$t('table.active.placeholder').' "'.$t('title').'"')
        	))?>
        </td>
        <td><?=CHtml::ajaxLink($ta('btn.remove'), $this->createUrl('brand/delete', array('id'=>$item->id)),
          array(
            'type'=>'post',
            'data'=>array('ajax'=>1),
            'beforeSend'=>'function() { return confirm("'.$ta('confirm.remove').'"); }',
            'success'=>'function() {$("#brand-'. $item->id .'").remove();}'
          ),
          array('class'=>'btn btn-danger btn-xs')
        )?>
        </td>
      </tr>
    <?endforeach?>
    </tbody>
  </table>
  <?$this->widget('CLinkPager', array(
    'pages' => $dataProvider->getPagination(),
    'htmlOptions'=>array('class'=>'pagination'),
    'header'=>false,
    'firstPageLabel'=>false,
    'lastPageLabel'=>false
  ))?>
<?endif?>