<?php
/** @var \menu\widgets\breadcrumbs\SimpleWidget $this */
/** @var array $breadcrumbs */

use \menu\components\helpers\UrlHelper;
?>
<ul <?php if($this->cssClass) echo "class=\"{$this->cssClass}\""; ?>>
	<li><?php echo CHtml::link('Главная', '/'); ?></li>
	<?php foreach($breadcrumbs as $item):?>
		<li>/</li>
		<li><?php echo CHtml::link($item->title, UrlHelper::createUrl($item, $this->adminMode)); ?></li>
	<?php endforeach; ?>
</ul>