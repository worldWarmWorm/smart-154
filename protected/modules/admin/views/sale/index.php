<? /** @var SaleController $this **/ ?>
<? /** @var [Sale]CActiveDataProvider $dataProvider **/ ?>
<?$t=\YiiHelper::createT('AdminModule.sale')?>
<?$ta=\YiiHelper::createT('AdminModule.admin')?>

<h1><?=D::cms('sale_title') ?: $t('title')?></h1>

<div style="margin-bottom: 15px;">
  <?php echo CHtml::link($ta('btn.add'), array('create'), array('class'=>'btn btn-primary')); ?>
</div>

<?if(!$dataProvider->getItemCount()):?>
  <p><?=$t('emptyText')?></p>
<?else:?>
  <table class="event-list table table-striped table-hover"> <!-- adminList -->
    <thead>
      <tr>
        <th><?=$t('table.header.title')?></th>
        <th class="col-md-1 text-center"><?=$t('table.header.create_time')?></th>
        <th class="col-md-1 text-center"><?=$t('table.header.active')?></th>
        <th class="col-md-1 text-center"></th>
      </tr>
    </thead>
    <tbody>
    <?foreach($dataProvider->getData() as $item):?>
      <tr id="sale-<?=$item->id?>" class="row<?=($item->id % 2) ? 0 : 1?>">
        <td class="title">
          <?=CHtml::link($item->title, array('sale/update', 'id'=>$item->id))?>
          <a title="<?=$ta('a.edit.title')?>" class=" pull-right" href="<?=$this->createUrl('sale/update', array('id'=>$item->id)) ?>"><span class="glyphicon glyphicon-pencil"></span></a>
        </td>
        <td class="text-center"><?=$item->date?></td>
        <td class="text-center">
        	<?$this->widget('\admin\widget\form\ActiveInList', array(
        		'behavior'=>$item->activeBehavior, 
        		'changeUrl'=>$this->createUrl('sale/changeActive', array('id'=>$item->id)), 
        		'cssMark'=>'unmarked', 
        		'cssUnmark'=>'marked', 
        		'wrapperOptions'=>array('class'=>'mark', 'title'=>$t('table.active.placeholder').' "'.$t('title').'"')
        	))?>
        </td>
        <td><?=CHtml::ajaxLink($ta('btn.remove'), $this->createUrl('sale/delete', array('id'=>$item->id)),
          array(
            'type'=>'post',
            'data'=>array('ajax'=>1),
            'beforeSend'=>'function() { return confirm("'.$ta('confirm.remove').'"); }',
            'success'=>'function() {$("#sale-'. $item->id .'").remove();}'
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
