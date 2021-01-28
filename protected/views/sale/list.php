<?
/** @var SaleController $this */
/** @var dataProvider[Sale] $dataProvider */ 
?>
<h1><?=$this->contentTitle?></h1>

<div class="events_page">
  <?foreach($dataProvider->getData() as $data):?>
  <div class="event">
      <h2><?=CHtml::link($data->title, array('sale/view', 'id'=>$data->id))?></h2>
      <?if($data->preview):?>
        <div class="event_img">
          <a href="<?=Yii::app()->createUrl('sale/view', array('id'=>$data->id))?>">
            <img src="<?=$data->imageBehavior->getSrc()?>" alt="<?=$data->title?>" title="<?=$data->title?>">
          </a>
        </div>
      <? endif; ?>
      <div class="intro"><p><?=$data->preview_text?></p></div>
      <div class="clearfix"></div>
      <?=CHtml::link(Yii::t('sale', 'link.detail'), array('sale/view', 'id'=>$data->id), array('class'=>'btn'))?>
  </div>
  <?endforeach?>
</div>

<?$this->widget('DLinkPager', array(
  'header'=>'Страницы: ',
  'pages'=>$dataProvider->getPagination(),
  'nextPageLabel'=>'&gt;',
  'prevPageLabel'=>'&lt;',
  'cssFile'=>false,
  'htmlOptions'=>array('class'=>'news-pager')
))?>
      

