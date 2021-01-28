<?php
namespace DOrder\actions;

use DOrder\models\DOrder;

class RobokassaPayment extends \CAction
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
			$this->controller->render('DOrder.views.robokassa.payment', compact('model'));
		}
		else {
			new \CHttpException(404);
		}
	}
}