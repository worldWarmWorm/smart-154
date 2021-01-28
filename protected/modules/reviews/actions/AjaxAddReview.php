<?php
/**
 * Ajax-действие добавления отзыва
 * 
 */
namespace reviews\actions;

use common\ext\email\components\helpers\HEmail;
use reviews\models\Review;
use common\components\helpers\HAjax;
use common\components\helpers\HDb;

class AjaxAddReview extends \CAction
{
	public function run()
	{
		$model=new Review('frontend_insert');
		
		if(HDb::massiveAssignment($model)) {
			$save = $model->save();

			if ($save) {
				HEmail::cmsAdminSend(true, ['model'=>$model], 'application.modules.reviews.views.default._email');
			}

			HAjax::end($save, ['message'=>''], $model->getErrors());
		}
		
		HAjax::end(false);
	}
}