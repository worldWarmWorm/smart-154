<?php
/**
 * Контроллер публичной части модуля "Отзывы".
 *
 */
namespace reviews\controllers;

use common\components\helpers\HArray as A;
use common\components\helpers\HYii as Y;
use common\components\helpers\HAjax;
use reviews\models\Review;
use reviews\models\Settings;

class DefaultController extends \Controller
{
	/**
	 * (non-PHPdoc)
	 * @see \CController::filters()
	 */
	public function filters()
	{
		return A::m(parent::filters(), [
			['\DModuleFilter', 'name'=>'reviews'],
			'ajaxOnly +addReview'
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CController::actions()
	 */
	public function actions()
	{
		return A::m(parent::actions(), [
			'addReview'=>'\reviews\actions\AjaxAddReview'
		]);
	}
	
	/**
	 * Действие отображения основной страницы. 
	 */
	public function actionIndex()
	{
		$dataProvider=Review::model()
			->hasDetailTextColumn()
			->listingColumns()
			->actived()
			->byCreateDateDesc()
			->getDataProvider();
		
		if(Y::request()->isAjaxRequest) {
			$this->renderPartial('_reviews_listview', compact('dataProvider'), false, true);
			Y::end();
		}
		
		$this->seoTags([
			'meta_h1'=>Settings::model()->meta_h1 ?: $this->getHomeTitle(),
			'meta_title'=>Settings::model()->meta_title ?: $this->getHomeTitle(),
			'meta_key'=>Settings::model()->meta_key, 
			'meta_desc'=>Settings::model()->meta_desc
		]);
		
		$this->breadcrumbs->add($this->getHomeTitle());
		
		$this->render('index', compact('dataProvider'));
	}
	
	/**
	 * Действие отображения отзыва. 
	 * @param integer $id id отзыва
	 */
	public function actionView($id)
	{
		$model=$this->loadModel('\reviews\models\Review', $id);
		
		if(!$model->meta_h1) {
			if($model->meta_title) $model->meta_h1=$model->meta_title;
			else $model->meta_h1=$model->author;
		}
		$this->seoTags($model);
		\ContentDecorator::decorate($model, 'detail_text');
		
		$this->breadcrumbs->add($this->getHomeTitle(), '/reviews');
		$this->breadcrumbs->add($model->meta_h1);
		
		$this->render('view', compact('model'));
	}
	
	/**
	 * Получить основной заголовок
	 * @return string
	 */
	public function getHomeTitle()
	{
		return Settings::model()->meta_h1 ?: \Yii::t('ReviewsModule.controllers/default', 'title');
	}
}
