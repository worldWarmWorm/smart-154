<?php
/** @var \reviews\widgets\NewReviewForm $this */
/** @var CActiveDataProvider[\reviews\models\Review] $dataProvider */

$this->render('_reviews_list', compact('dataProvider'));