<?php
/**
 * Ajax frontend controller
 * 
 */
namespace feedback\controllers;

use \AttributeHelper as A;
use \feedback\components\controllers\FrontController;
use \feedback\components\FeedbackFactory;
use common\components\helpers\HEvent;

class AjaxController extends FrontController
{
	/**
	 * (non-PHPdoc)
	 * @see \feedback\components\controllers\FrontController::filters()
	 */
	// @todo filters!
// 	public function filters()
// 	{
// 		return /*\CMap::mergeArray(parent::filters(), */array(
// 			'ajaxOnly + send'
// 		)/*)*/;
// 	} 
	
	/**
	 * (non-PHPdoc)
	 * @see CController::behaviors()
	 */
	public function behaviors()
	{
		return array(
			'AjaxControllerBehavior' => array(
				'class'=>'\AjaxControllerBehavior',
			)
		);
	} 
	
	/**
	 * Send 
	 */
	public function actionSend()
	{
		$result['success'] = false;
		 
		$feedbackId = \Yii::app()->request->getParam('feedbackId');
		$formId = \Yii::app()->request->getPost('formId');
		
		$factory = FeedbackFactory::factory($feedbackId);
		
		$isAjaxValidation = $this->isAjaxValidation($formId);
		$model = $factory->getModelFactory()->getModel();
		$model->scenario = $isAjaxValidation ? 'active' : 'insert';
		 
		$className = preg_replace('/\\\\+/', '_', get_class($model));
		$values = \Yii::app()->request->getPost($className);
		if($values) {
			// Задаем значения
			foreach($factory->getModelFactory()->getAttributes() as $name=>$typeFactory) {
				$model->$name = $typeFactory->getModel()->normalize(A::get($values, $name));
			}
			 
			if($isAjaxValidation) {
				$this->performAjaxValidation($model, $formId);
			}
			elseif($model->validate()) {
				$result['success'] = $model->save(false);
				$result['message'] = $result['success'] ? 'Ваша заявка принята.' : 'Возникла ошибка на сервере, повторите подачу заявки позже.';
				
				// Отправка уведомления на почту
				if($result['success']) {
                    HEvent::raise('OnFeedbackNewMessageSuccess', compact('factory', 'model'));
				}
				
				//$this->createAction('captcha')->refresh();
			}
		}
		echo \CJSON::encode($result);
		\Yii::app()->end();
	}
}
