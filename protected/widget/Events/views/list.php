
<?php if ($this->show_title): ?>
	<h2><?=D::cms('events_title',Yii::t('events','events_title'))?></h2>
<?php endif; ?>

<ul class="news">
  <?php foreach($events as $event): ?>
	  <li>
	    <p class="news-date"><?php echo $event->date; ?></p>
	    <?php echo CHtml::link($event->title, array('site/event', 'id'=>$event->id), array('class'=>'news-head')); ?>
	    <div class="news-intro">
	    	<? if($event->previewEnable && !empty($event->preview)): ?>
	    		<div class="news_img">
	    			<a href="<?= Yii::app()->createUrl('site/event', array('id'=>$event->id)); ?>">
	    				<img src="<?=$event->previewImg?>" alt="<?php echo $event->title; ?>" title="<?php echo $event->title; ?>">
	    			</a>
	    		</div>
				<? endif; ?>
	    	<p><?php echo $event->intro; ?></p>
	    </div>
	  </li>
  <?php endforeach; ?>
</ul>
<div class="all_events_wrap">
	<?php if ($show_all) echo CHtml::link(D::cms('events_link_all_text', Yii::t('events','link_all_text')), array('site/events'), array('class'=>'all_events')); ?>
</div>
