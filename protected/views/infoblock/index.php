<?php
/** @var \iblock\models\InfoBlock $iblock */
use iblock\components\InfoBlockHelper;
?>
<h1><?= $iblock->title; ?></h1>
<?php if($data=InfoBlockHelper::getElements($iblock->id)): ?>
<div class="iblock__list">
	<?php foreach($data as $item): ?>
		<div class="iblock__item">
			<div class="iblock__item-preview"><?= $item['preview'] ? CHtml::image($item['preview']) : '&nbsp;'; ?></div>
			<div class="iblock__item-title"><?php 
			if($item['description']): 
				?><?= CHtml::link($item['title'], ['infoblock/view', 'id'=>$item['id']]); ?><?php
			else: 
				?><?= $item['title']; ?><?php 
			endif; ?>
			</div>
		</div>
	<?php endforeach; ?>
</div>
<?php endif; ?>