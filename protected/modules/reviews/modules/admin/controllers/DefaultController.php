<?php
/**
 * Основной контроллер раздела администрирования модуля "Отзывы".
 *
 */
namespace reviews\modules\admin\controllers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use reviews\modules\admin\components\BaseController;
use reviews\models\Review;
use reviews\models\Settings;
use common\components\helpers\HAjax;

class DefaultController extends BaseController
{
	/**
	 * (non-PHPdoc)
	 * @see \CController::filters()
	 */
	public function filters()
	{
		return A::m(parent::filters(), [
			['\DModuleFilter', 'name'=>'reviews'],
			'ajaxOnly +changeActive'
		]);
	}
	
	/**
	 * (non-PHPDoc)
	 * @see BaseController::$viewPathPrefix;
	 */
	public $viewPathPrefix='reviews.modules.admin.views.default.';
	
	/**
	 * (non-PHPdoc)
	 * @see \CController::actions()
	 */
	public function actions()
	{
		return A::m(parent::actions(), [
			'removeImage'=>[
				'class'=>'\ext\D\image\actions\RemoveImageAction',
				'modelName'=>'\reviews\models\Review',
				'imageBehaviorName'=>'imageBehavior',
				'ajax'=>true
			],
			'changeActive'=>[
				'class'=>'\common\ext\active\actions\AjaxChangeActive',
				'className'=>'\reviews\models\Review',
				'behaviorName'=>'activeBehavior',
				'onBeforeSave'=>[$this, 'onBeforeSaveChangeActive']
			]
		]);
	}
	
	/**
	 * Event handler OnBeforeSave by change activity.
	 * @param \reviews\models\Review $model модель.
	 */
	public function onBeforeSaveChangeActive(&$model) 
	{
		$model=$this->loadModel('\reviews\models\Review', $model->id, true, ['select'=>'id,preview_text']);
		$model->privacy_policy=1;
		
		return $model->validate();
	}
	
	/**
	 * Действие главной страницы.
	 */
	public function actionIndex()
	{	
		$this->setPageTitle(\Yii::t('\reviews\modules\admin\AdminModule.controllers/default', 'page.index.title'));
		
		$this->breadcrumbs=$this->getModuleBreadcrumb();
				
		$dataProvider=Review::model()->byCreateDateDesc()->getDataProvider();
		
		$this->render($this->viewPathPrefix.'index', compact('dataProvider'));
	}
	
	/**
	 * Действие страницы создания отзыва.
	 */
	public function actionCreate()
	{
		$model=new Review;
		
		$this->save($model, [], 'review-form', [
			'afterSave'=>function() use ($model) {
				$t=Y::ct('\reviews\modules\admin\AdminModule.controllers/default');		
				if(isset($_POST['saveout'])) {
					Y::setFlash(Y::FLASH_SYSTEM_SUCCESS, $t('success.created', ['{id}'=>$model->id]));
					$this->redirect(['index']);
				}
				else {
					$this->redirect(['update', 'id'=>$model->id]);
				}
			}]
		);
		
		$pageTitle=\Yii::t('\reviews\modules\admin\AdminModule.controllers/default', 'page.create.title');
		$this->setPageTitle($pageTitle);
		
		$this->breadcrumbs=$this->getModuleBreadcrumb();
		$this->breadcrumbs[$pageTitle]=['reviews/create'];
		
		$this->render($this->viewPathPrefix.'create', compact('model'));
	}
	
	/**
	 * Действие страницы редактирования отзыва.
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel('\reviews\models\Review', $id);
		
		$this->save($model, [], 'review-form', [
			'afterSave'=>function() use ($model) {
				$t=Y::ct('\reviews\modules\admin\AdminModule.controllers/default');
				if(isset($_POST['saveout'])) {
					Y::setFlash(Y::FLASH_SYSTEM_SUCCESS, $t('success.updated', ['{id}'=>$model->id]));
					$this->redirect(['index']);
				}
				else {
					$this->refresh();
				}
			}]
		);
		
		$pageTitle=\Yii::t('\reviews\modules\admin\AdminModule.controllers/default', 'page.update.title', ['{id}'=>$id]);
		$this->setPageTitle($pageTitle);
		
		$this->breadcrumbs=$this->getModuleBreadcrumb();
		$this->breadcrumbs[$pageTitle]=['reviews/update', 'id'=>$model->id];
		
		$this->render($this->viewPathPrefix.'update', compact('model'));
	}
	
	/**
	 * Удаление отзыва
	 */
	public function actionDelete($id)
	{
		$model=$this->loadModel('\reviews\models\Review', $id);
		
		$model->delete();
		
		if(Y::request()->isAjaxRequest) {
			Y::end();
		}
		
		$this->redirect(['index']);
	} 
	
	/**
	 * Страница настроек.
	 */
	public function actionSettings()
	{
		$t=Y::ct('\reviews\modules\admin\AdminModule.controllers/default');
		
		$model=new Settings;
		
		$modelName=\CHtml::modelName($model);
		if (isset($_POST[$modelName])) {
			$model->attributes = $_POST[$modelName];
		
			if ($model->validate()) {
				$model->saveSettings();
				if(isset($_POST['saveout'])) {
					Y::setFlash(Y::FLASH_SYSTEM_SUCCESS, $t('success.settings.updated'));
					$this->redirect(['index']);
				}
				else {
					$this->refresh();
				}
			}
		}
		
		$this->setPageTitle($t('page.settings.title'));
		
		$this->breadcrumbs=$this->getModuleBreadcrumb();
		$this->breadcrumbs[$t('page.settings.title')]=['reviews/settings'];
		
		$this->render($this->viewPathPrefix.'settings', compact('model'));
	}
	
	protected function getModuleBreadcrumb()
	{
		return [
			\Yii::t('AdminModule.page', 'title') => ['page/index'],
			\Yii::t('\reviews\modules\admin\AdminModule.common', 'module.name') => ['reviews/index']
		];
	}
}
