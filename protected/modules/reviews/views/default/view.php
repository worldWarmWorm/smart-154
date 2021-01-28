<?php
/** @var \reviews\controllers\DefaultController $this */
/** @var \reviews\models\Review $model */
use common\components\helpers\HYii as Y;
use common\components\helpers\HHtml;

$t=Y::ct('CommonModule.labels', 'common');
?>
<div class="review__content">
	<div class="review__author"><?= $model->author; ?></div>
	<? if((int)\Yii::app()->dateFormatter->format('yyyy', $model->publish_date)): ?>
		<div class="review__publish_date"><?= Y::formatDateVsRusMonth($model->publish_date); ?></div>
	<? endif; ?>
	<div class="review__text"><?= $model->detail_text; ?></div>
</div>
<div class="review__link_back"><?= HHtml::linkBack($t('link.back'), $this->createUrl('index')); ?></div>
