<?php
/** @var \extend\modules\slider\widgets\Slick $this */
?>
<? foreach($this->getSlides() as $slide) { ?>
    <? if($img=$this->getSlideImage($slide, ['class'=>''])) { ?>
		<?if($this->isLink() && $slide->url):?><a href="<?=$slide->url?>"><?endif?>
		<?= $img; ?>
		<?if($this->isLink() && $slide->url):?></a><?endif?>
    <? } ?>
<? } ?>
