<?php
namespace DOrder\actions;

class YmPaymentSuccess extends \CAction
{
	/**
	 * (non-PHPdoc)
	 * @see CAction::run()
	 */
	public function run()
	{
		$this->controller->breadcrumbs->add('Завершение оплаты');
		$this->controller->render('DOrder.views.ym.payment_success');
	}
}