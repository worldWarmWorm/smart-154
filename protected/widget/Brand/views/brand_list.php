<? if(!empty($brands)): ?>
	<ul class="<?= $this->listClass; ?>">
	<? 
	foreach($brands as $brand): 
		?><li><a href="/brands/<?= $brand->alias; ?>"><?= $brand->title; ?></a></li><? 
	endforeach; 
	?>
	</ul>
<? endif; ?>