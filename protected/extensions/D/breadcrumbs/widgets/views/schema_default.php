<?php
echo \CHtml::openTag('div', $this->htmlOptions);
if($this->homeTitle):?>
<div itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb">
	<a href="<?=$this->homeUrl?>" itemprop="url"><span itemprop="title"><?=$this->homeTitle?></span></a>
</div>
<?endif?>

<?foreach($this->breadcrumbs as $breadcrumb):?>
	<div itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb">
		<?php if($breadcrumb === end($this->breadcrumbs)):?>
			<?php $link=\Yii::app()->createAbsoluteUrl(preg_replace('#\?.*$#', '', $_SERVER['REQUEST_URI'])); ?>
			<noindex><span itemprop="url" rel="nofollow" href="<?= $link ?>" content="<?= $link ?>"><span itemprop="title"><?= $breadcrumb['title'] ?></span></span></noindex>
		<?php else:
			$params=(array)$breadcrumb['url'];
			$link=\Yii::app()->createUrl(array_shift($params), $params);
			?>
			<a href="<?= $link ?>" itemprop="url"><span itemprop="title"><?= $breadcrumb['title'] ?></span></a>
		<?php endif;?>
	</div>
<?endforeach?>
	
<?=\CHtml::closeTag('div')?>
