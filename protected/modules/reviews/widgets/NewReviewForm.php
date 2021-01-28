<?php
/**
 * Виджет формы добавления нового отзыва
 * 
 */
namespace reviews\widgets;

use reviews\models\Review;
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

class NewReviewForm extends \CWidget
{
	/**
	 * @var string ссылка на действие добавления
	 */
	public $actionUrl;
	
	/**
	 * @var boolean режим всплывающего окна
	 */
	public $popup=true;

	public $view='new_review_form';
	
	public $params = [];
	
	public $options = [];
	
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::init()
	 */
	public function init()
	{
		Y::publish([
			'path'=>dirname(__FILE__) . Y::DS . 'assets' . Y::DS . 'new_review_form',
			'js'=>'js/NewReviewFormWidget.js',
			'css'=>'css/styles.css'
		]);
		
		$t=Y::ct('ReviewsModule.widgets/new_review_form', 'reviews');
        $options=A::m([
            'w_nrf_mgs_success'=>$t('msg.success'),
            'w_nrf_mgs_error'=>$t('msg.error'),
            'w_nrf_mgs_error_max_try'=>$t('msg.error.maxTry')
        ], $this->options);
		Y::js('NewReviewFormWidget', ';window.NewReviewFormWidget.init(' . \CJavaScript::encode($options) . ');', \CClientScript::POS_READY);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::run()
	 */
	public function run()
	{
		$model=new Review('frontend_insert');
		
		$this->render($this->view, compact('model'));
	}
}
