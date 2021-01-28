<?php
namespace DOrder\actions;

class RobokassaPaymentFail extends \CAction
{
	/**
	 * (non-PHPdoc)
	 * @see CAction::run()
	 */
	public function run()
	{
		$this->controller->breadcrumbs->add('Ошибка оплаты');
		$this->controller->render('DOrder.views.robokassa.payment_fail');
	}
}