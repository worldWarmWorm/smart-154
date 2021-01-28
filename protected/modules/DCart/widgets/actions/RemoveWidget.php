<?php
/**
 * DCart "Remove" action widget
 * 
 * @use \AjaxHelper
 */
namespace DCart\widgets\actions;

class RemoveWidget extends BaseActionWidget
{
	public function run()
	{
		$ajax = new \AjaxHelper();
		
		$hash = \Yii::app()->request->getPost('hash');

		$ajax->success = \Yii::app()->cart->remove($hash);
		
		if($ajax->success) {
			$ajax->data['cartTotalPrice']=\HtmlHelper::priceFormat(\Yii::app()->cart->getTotalPrice());
			$ajax->data['cartTotalCount']=\Yii::app()->cart->getTotalCount();
		}
			
		$ajax->endFlush();
	}
}