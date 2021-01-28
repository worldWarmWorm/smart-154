<?
/** @var BrandController $this */
/** @var dataProvider[Brand] $dataProvider */ 
?>
<h1><?=$this->contentTitle?></h1>

<? if(!BrandSettings::model()->index_page_content_pos_footer): ?>
<div class="brands__description">
	<?= BrandSettings::model()->index_page_content; ?>
</div>
<? endif; ?>

<div class="brands__list">
  <?foreach($dataProvider->getData() as $data):?>
  <div class="brands__item">
      <div class="brands__item-logo">
      <?if($data->logo):?>
          <a href="<?=Yii::app()->createUrl('brand/view', ['alias'=>$data->alias])?>">
            <img src="<?=$data->getSrc()?>" alt="<?=$data->title?>" title="<?=$data->title?>" />
          </a>
      <? 
      	else: ?>&nbsp;<? 
      	endif; 
      ?>
      </div>
      <div class="brands__item-desc">
      	<h2><?=CHtml::link($data->title, ['brand/view', 'alias'=>$data->alias])?></h2>
      	<p><?=$data->preview_text?></p>
      	<?=CHtml::link(Yii::t('brand', 'link.detail'), ['brand/view', 'alias'=>$data->alias], array('class'=>'more-info'))?>
      </div>
  </div>
  <div class="clearfix"></div>
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
      

<? if(BrandSettings::model()->index_page_content_pos_footer): ?>
<div class="brands__description">
	<?= BrandSettings::model()->index_page_content; ?>
</div>
<? endif; ?>
