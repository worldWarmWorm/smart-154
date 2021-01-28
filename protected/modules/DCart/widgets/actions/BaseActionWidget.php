<?php
/**
 * DCart base widget class for actions.
 *   
 * @use \AjaxHelper
 */
namespace DCart\widgets\actions;

class BaseActionWidget extends \CWidget
{
	/**
	 * Подготовить данные к отдаче
	 * @param \AjaxHelper &$ajaxHelper объект ajax-помощника
	 */
	protected function prepareAjaxData(\AjaxHelper &$ajaxHelper)
	{
// 		$ajaxHelper->data['ModalCartItems'] = \DCart\widgets\ModalCartWidget::renderItems(true);
		
// 		$ajaxHelper->data['cartTotalPrice'] = \Yii::app()->cart->getTotalPrice();
// 		$ajaxHelper->data['cartTotalCount'] = \Yii::app()->cart->getTotalCount();
// 		$ajaxHelper->data['cartHashes'] = \Yii::app()->cart->getHashes();
	}
}