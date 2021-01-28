<div class="events_page">
  <?php foreach($posts as $post): ?>
  <div class="event">
      <p class="created"><?php echo $post->date; ?></p>
      <h2><?php echo CHtml::link($post->title, array('site/page', 'id'=>$post->id)); ?></h2>
      <div class="intro"><p><?php echo $post->getIntro(); ?></p></div>
      <div class="clearfix"></div>
      <?php echo CHtml::link('Подробнее &rarr;', array('site/page', 'id'=>$post->id), array('class'=>'more-info')); ?>
  </div>
  <?php endforeach; ?>
</div>

<?php $this->widget('DLinkPager', array(
  'header'=>'Страницы: ',
  'pages'=>$pages,
  'nextPageLabel'=>'&gt;',
  'prevPageLabel'=>'&lt;',
  'cssFile'=>false,
  'htmlOptions'=>array('class'=>'news-pager')
)); ?>
      

