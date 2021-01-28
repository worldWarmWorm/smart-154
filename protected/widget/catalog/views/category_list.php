<?php
/** @var CategoryListWidget $this */
/** @var array[Category] $categories */

if($categories): 
?><ul class="sitebar">
	<? foreach($categories as $category): ?>
		<li class="sitebar-item">
			<a href="<?=$this->owner->createUrl('shop/category', ['id'=>$category->id])?>"><?
				if($category->mainImageBehavior->exists()): 
				?><div class="sitebar-images">
					<?= $category->mainImageBehavior->img(155,155); ?>
				</div><?
				else: 
				?><div class="sitebar-images no-image">&nbsp;</div><? 
				endif; ?>
				<div class="sitebar-description">
					<p><?= $category->title; ?></p>
				</div>
			</a>
		</li>
	<? endforeach; ?>
</ul><? 
endif; ?>
