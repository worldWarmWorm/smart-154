<?php
/** @var \extend\modules\slider\widgets\Slick $this */
?>
<? foreach($this->getSlides() as $slide) { ?>
    <? if($img=$this->getSlideImage($slide, ['class'=>''])) { ?>
		<?= $img; ?>
    <? } ?>
<? } ?>
