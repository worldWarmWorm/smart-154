<?php
/** @var $this refers to the owner of this list view widget. For example, if the widget is in the view of a controller, then $this refers to the controller. */
/** @var $data refers to the data item currently being rendered. */
/** @var $index refers to the zero-based index of the data item currently being rendered. */
/** @var $widget refers to this list view widget instance. */
use reviews\models\Settings;
use common\components\helpers\HYii as Y;
use common\components\helpers\HHtml;


$w=Settings::model()->tmb_width;
$h=Settings::model()->tmb_height;
?>
<li class="reviews__item">
	<img class="reviews__item-image" src="<?= $data->getSrc() ?: HHtml::pImage(['w'=>$w,'h'=>$h,'c'=>'ffffff','bg'=>'ffffff','t'=>'.']); ?>" />
	<ul>
		<li class="reviews__item-author"><?= $data->author; ?>
			<? if((int)\Yii::app()->dateFormatter->format('yyyy', $data->publish_date)): ?>
				<span class="reviews__item-publish_date"><?= Y::formatDateVsRusMonth($data->publish_date); ?></span>
			<? endif; ?>
		</li>
		<li class="reviews__item-preview_text"><?= $data->preview_text; ?></li>
		<? if($data->has_detail_text): ?>
			<li class="reviews__item-read_more"><?= CHtml::link('подробнее', ['default/view', 'id'=>$data->id]); ?></li>
		<? endif; ?>
	</ul>
</li>
