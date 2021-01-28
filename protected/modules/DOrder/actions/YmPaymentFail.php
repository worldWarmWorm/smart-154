<?php
namespace DOrder\actions;

class YmPaymentFail extends \CAction
{
	/**
	 * (non-PHPdoc)
	 * @see CAction::run()
	 */
	public function run()
	{
		$this->controller->breadcrumbs->add('Ошибка оплаты');
		$this->controller->render('DOrder.views.ym.payment_fail');
	}
}