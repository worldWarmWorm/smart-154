<?
/** @var \reviews\controllers\DefaultController $this */
/** @var \CActiveDataProvider[\reviews\models\Review] $dataProvider */
use reviews\models\Settings;
?>
<h1><?= $this->getHomeTitle(); ?></h1>

<? $this->widget('\reviews\widgets\NewReviewForm', ['actionUrl'=>$this->createUrl('addReview')]); ?>

<div class="content is_read_more">
	<?= Settings::model()->index_page_content; ?>
</div>

<? $this->renderPartial('_reviews_listview', compact('dataProvider')); ?>