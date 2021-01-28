<?php
/** @var \menu\widgets\breadcrumbs\SimpleWidget $this */
/** @var array $breadcrumbs */
?>
<ul class="breadcrumbs-classic">
	<li><?php echo CHtml::link('Главная', '/'); ?></li>
	<?php foreach($breadcrumbs as $item):?>
		<li>/</li>
		<li><?php echo CHtml::link($item['title'], $item['url']); ?></li>
	<?php endforeach; ?>
</ul>