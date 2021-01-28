<?php
namespace DOrder\actions;

class RobokassaPaymentSuccess extends \CAction
{
	/**
	 * (non-PHPdoc)
	 * @see CAction::run()
	 */
	public function run()
	{
		$this->controller->breadcrumbs->add('Завершение оплаты');
		$this->controller->render('DOrder.views.robokassa.payment_success');
	}
}