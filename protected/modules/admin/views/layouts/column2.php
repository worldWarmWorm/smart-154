<?php $this->beginContent('/layouts/main'); ?>
    <div class="right-col">
      <div id="content" class="content">
      		<? $this->widget('\common\widgets\ui\flash\Yii', ['view'=>'system']); ?>
          	<?php echo $content; ?>
      </div>
    </div>
    <div class="clr"></div>
<?php $this->endContent(); ?>
