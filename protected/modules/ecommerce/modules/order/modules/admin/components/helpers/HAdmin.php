<?php
/**
 * Класс помощник для подмодуля администрирования модуля Заказов
 */
namespace ecommerce\modules\order\modules\admin\components\helpers;

class HAdmin
{
    /**
	 * Получить массив данных для пунктов меню уведомления "Заказы"
	 * @return array возвращается массив вида array(count, items), где
	 * "count" - кол-во новых уведомлений.
	 * "items" - пункты меню уведомлений.
	 */
	public static function menuNotificationOrders()
	{
		return \admin\components\helpers\HAdmin::menuNotificationsItem([
			'hGetCount'=>function() { return \DOrder\models\DOrder::model()->uncompleted()->count(); },
			'url'=>'/cp/order',
			'title'=>\Yii::t('\ecommerce\modules\order\modules\admin\AdminModule.components/helpers/hAdmin', 'notification.orders.label'),
			'icon'=>'glyphicon-shopping-cart',
			'span'=>'dorder-order-button-widget-count',
			'module'=>'shop'
		]);
	}
}
