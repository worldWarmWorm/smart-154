<?php
/**
 * Виджет мини-корзины
 * 
 * @use \AssetHelper
 */
namespace DCart\widgets;

class ModalCartWidget extends BaseCartWidget
{
	/**
	 * Ссылка на страницу оформления заказа.
	 * @var string|array
	 */
	public $orderUrl = '/order';
	
	public $hidePayButton = false;

	/**
	 * (non-PHPdoc)
	 * @see \CWidget::init()
	 */
	public function init()
	{
		parent::init();
		
		\AssetHelper::publish(array(
			'path' => __DIR__ . DIRECTORY_SEPARATOR . 'assets',
			'js' => array('js/classes/DCartModalWidget.js', 'js/dcart_cart_modal_widget.js'),
			'css' => 'css/cart.css',
		));

		if(\Yii::app()->request->isAjaxRequest && (strcmp(substr(\Yii::app()->request->urlReferrer, -6), '/order') === 0)) {
			$this->hidePayButton = true;
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::run()
	 */
	public function run()
	{
		$cart = \Yii::app()->cart;
		
		$this->render('modal_cart', compact('cart'));
	}
	
	/**
	 * Render cart items
	 * @param boolean $return Возвращать контент или выводить в поток.
	 * @return string|void
	 */
	public static function renderItems($return=false)
	{
		$widget = new self;
		$cart = \Yii::app()->cart;

		if(\Yii::app()->request->isAjaxRequest && (strcmp(substr(\Yii::app()->request->urlReferrer, -6), '/order') === 0)) {
			$widget->hidePayButton = true;
		}
	
		return $widget->render('modal_cart', compact('cart'), $return);
	}
	
	/**
	 * Render cart item
	 * @param boolean $return Возвращать контент или выводить в поток.
	 * @return string|void
	 */
	public static function renderItem($hash, $return=false)
	{
		$widget = new self;
		$cart = \Yii::app()->cart;
	
		if(\Yii::app()->request->isAjaxRequest && (strcmp(substr(\Yii::app()->request->urlReferrer, -6), '/order') === 0)) {
			$widget->hidePayButton = true;
		}
		
		$data=$cart->get($hash);
	
		return $widget->render('_modal_cart_item', compact('cart', 'hash', 'data'), $return);
	}
}