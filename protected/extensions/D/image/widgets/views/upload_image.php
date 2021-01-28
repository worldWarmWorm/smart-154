<?php
/** @var \ext\D\image\widgets\UploadImage $this */
$ta=\YiiHelper::createT('AdminModule.admin');
$t=\YiiHelper::createT('\ext\D\image\widgets\UploadImage.uploadImage');
$ib=&$this->behavior;
$src=$ib->getSrc();
$btnid=uniqid('btn-delete');
if($src):?>
  <div class="image_content">
	<img src="<?=$src?>" class="img-thumbnail">
	<a class="btn btn-danger js-<?= $btnid; ?>" href="#"><?=$ta('btn.remove')?></a>
	<?if($ib->hasEnabled()):?>
	<br>
	<label for="<?=\CHtml::activeId($ib->owner, $ib->attributeEnable)?>" class="display_module">
	  <?=$this->form->checkBox($ib->owner, $ib->attributeEnable)?><span> <?=$ib->owner->getAttributeLabel($ib->attributeEnable)?></span>
	</label>
	<?endif?>
	<br/>
  </div>
<?endif?>
<?=$this->form->labelEx($ib->owner, $ib->attributeFile); ?>
<p class="text-info"><?=$t('warning.gifNotResized')?></p>
<?=$this->form->fileField($ib->owner, $ib->attributeFile, array('class'=>'btn btn-primary','maxlength'=>255))?>
<?=$this->form->error($ib->owner, $ib->attributeFile)?>
<?if($src):?>
<script>
$(document).on("click", ".js-<?= $btnid; ?>", function(e) {
	e.preventDefault();
	var $this=this;
	$.post("<?=$this->ajaxUrlDelete?>", function() {
		$this.closest(".image_content").remove();
	}, "json");
	return false;
});
</script>
<?endif?>