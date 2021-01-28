<?php
namespace DOrder\actions;

use DOrder\models\DOrder;

class YmPayment extends \CAction
{
	/**
	 * (non-PHPdoc)
	 * @see CAction::run()
	 * 
	 * @param integer $hash хэш заказа 
	 */
	public function run($hash)
	{
		if($model=DOrder::model()->find(['condition'=>'hash=:hash', 'params'=>['hash'=>$hash]])) {
			$this->controller->breadcrumbs->add('Оплата Яндекс.Касса');
			$this->controller->render('DOrder.views.ym.payment', compact('model'));
		}
		else {
			new \CHttpException(404);
		}
	}
}