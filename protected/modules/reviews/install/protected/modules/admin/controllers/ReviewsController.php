<?php
Yii::setPathOfAlias('reviews', Yii::getPathOfAlias('application.modules.reviews'));
Yii::import('reviews.ReviewsModule');

class ReviewsController extends \reviews\modules\admin\controllers\DefaultController
{	
}