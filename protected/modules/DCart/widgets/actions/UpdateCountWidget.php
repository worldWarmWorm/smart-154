<?php
/**
 * DCart "Update count" action widget
 * 
 * @use \AjaxHelper
 */
namespace DCart\widgets\actions;

class UpdateCountWidget extends BaseActionWidget
{
	public function run()
	{
		$ajaxHelper = new \AjaxHelper();
		
		$hash = \Yii::app()->request->getPost('hash');
		$count = \Yii::app()->request->getPost('count', -1);
		
		if($count <= 0) {
			$ajaxHelper->success = \Yii::app()->cart->remove($hash);
		}
		elseif($count > 0) {
			$ajaxHelper->success = \Yii::app()->cart->updateCount($hash, $count);
		}
		
		if($ajaxHelper->success) {
			$ajaxHelper->data['count']=$count;
			$ajaxHelper->data['totalPrice']=\HtmlHelper::priceFormat(\Yii::app()->cart->getTotalPrice($hash));
			$ajaxHelper->data['cartTotalPrice']=\HtmlHelper::priceFormat(\Yii::app()->cart->getTotalPrice());
			$ajaxHelper->data['cartTotalCount']=\Yii::app()->cart->getTotalCount();
		}
			
		$ajaxHelper->endFlush();
	}
}