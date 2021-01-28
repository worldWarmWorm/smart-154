<?php
/**
 * Основной класс-помощник для раздела администрирования. 
 */
namespace admin\components\helpers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HFile;
use common\components\helpers\HEvent;
use crud\components\helpers\HCrud;
use settings\components\helpers\HSettings;

class HAdmin
{
	/**
	 * @var array|NULL массив конфигурации меню. 
	 * По умолчанию (NULL) - не проинициализирован.
	 */
	protected static $_config=null;
	 
	/**
	 * Получить конфигурацию меню.
	 * @param string|NULL|FALSE $id идентификатор меню. Если передана строка, 
	 * будет возвращена конфигурацию только данного идентификатора. Если будет 
	 * передано FALSE будет возвращен пустой массив. По умолчанию (NULL) - будет 
	 * возвращена вся конфигурация.
	 * @return array
	 */
	public static function menuConfig($id=null)
	{
		if(!is_array(self::$_config)) {
			self::$_config=HFile::includeByAlias('admin.config.menu', []);
		}
		
		if($id === null) return self::$_config;
		if($id === false) return [];
		else {
			$config=A::get(self::$_config, $id, []);
			return A::m($config, Y::param('backend.menu.'.$id, []));
		}
	}

	/**
	 * Установка активности пункту меню
	 * @param array &$item конфигурация пункта меню.
	 * @return void 
	 */
	public static function menuItemSetActive(&$item)
	{
		if($url=A::get($item, 'url')) {
			$pathInfo='/'.Y::request()->getPathInfo();		
			if(is_array($url)) {
				$route=array_shift($url);
				$current=Y::createUrl($pathInfo, $url?:[]);
				$url=Y::createUrl($route, $url?:[]);
				if(A::get($item, 'active', null) === null) {
                    			$item['active']=($current == $url);
                		}
			}
			elseif(A::get($item, 'active', null) === null) {
				$item['active']=($pathInfo == $url);
			}
		}
	}
	
	/**
	 * Предобработка пунктов меню.
	 * @param array &$items
	 */
	public static function menuPrepareItems(&$items)
	{
		$prepared=[];
		
		foreach($items as $idx=>$item) {
			if($idx === 'crud') {
				if(is_array($item)) array_walk($item, function(&$id, $pos) {
					$id=HCrud::getMenuItems(Y::controller(), $id, 'crud/index', true);
					$id['position']=$pos;
				});
				if(!empty($item)) $prepared=array_merge($prepared, $item);
				continue;
			}
			
			if($idx === 'settings') {
				if(is_array($item)) array_walk($item, function(&$id, $pos) {
					$id=HSettings::getMenuItems(Y::controller(), $id, 'settings/index', true);
					$id['position']=$pos;
				});
				if(!empty($item)) $prepared=array_merge($prepared, $item);
				continue;
			}
			
			if(is_string(A::get($item, 'items'))) {
				$item['items']=self::menuPrepareItems(self::menuConfig(A::get($item, 'items', false)));
			}
			
			$prepared[]=$item;
		}
			
		$positions=[];
		$visibledCount=0;
		foreach($prepared as $idx=>&$item) {
			self::menuItemSetActive($item);
			
			if(A::get($item, 'visible') === 'divider') {
				$item['visible']=(bool)$visibledCount;
			}
			elseif(A::get($item, 'visible', true)) {
				$visibledCount++;
			}			
			
			$positions[]=A::get($item, 'position', $idx);
			if(A::existsKey($item, 'position')) {
				unset($item['position']);
			}
		}
		
		asort($positions);
		return A::sort($prepared, array_keys($positions), true);
	}
	
	/**
	 * Получить осноные пункты меню. 
	 * @return array
	 */
	public static function menuItemsMain()
	{
		return self::menuPrepareItems(self::menuConfig('main'));
	}
	
	/**
	 * Получить пункты меню "Модули"
	 * @return array
	 */
	public static function menuItemsModules()
	{
		return self::menuPrepareItems(self::menuConfig('modules'));
	}
	
	/**
	 * Получить пункты меню "Уведомления"
	 * @return array
	 */
	public static function menuItemsNotifications()
	{
		$methods=[
            ['\ecommerce\modules\order\modules\admin\components\helpers\HAdmin', 'menuNotificationOrders'],
			'menuNotificationFeedbacks', 
			'menuNotificationQuestion',
			'menuNotificationProductReviews',
			'menuNotificationReviews'
		];
		
		$items=[[
			'label'=>\Yii::t('\AdminModule.menu', 'notification.label') . ' <b class="caret"></b>',
			'encodeLabel'=>false,
			'url'=>'#',
			'itemOptions'=>['class'=>'dropdown nav_notification'],
			'linkOptions'=>['class'=>'dropdown-toggle', 'data-toggle'=>'dropdown'],
			'items'=>[]
		]];
		
		$hasNewNotifications=false;		
		foreach($methods as $method) {
			if(is_array($method)) {
                $data=call_user_func($method);
            }
            else {
                $data=static::$method();
            }

			if($data[0]) $hasNewNotifications=true;
			
			foreach($data[1] as $item) {
				static::menuItemSetActive($item);
				if(!empty($item)) {
				    $items[0]['items'][]=$item;
				}
			}
		}

		$event=HEvent::raise('onAdminMenuItemsNotifications', ['items'=>&$items[0]['items']]);
		$items[0]['items']=A::get($event->params, 'items', $items[0]['items']);
		
		$hasNewNotifications=$hasNewNotifications || (bool)A::get($event->params, 'new');
		
		if($hasNewNotifications) {
			$items[0]['label'].=\CHtml::tag('span', ['class'=>'notification_warning glyphicon glyphicon-exclamation-sign']);
		}
		
		if(empty($items[0]['items'])) {
		    return [];
		}
		
		return $items;
	}
	
	/**
	 * Получить пункт меню "Уведомления"
	 * @param array $options параметры для пункта меню. 
	 * Доступны следующие параметры:
	 * hGetCount (callable) функция получения кол-ва новых уведомлений;
	 * url (string) ссылка пункта;
	 * title (string) заголовок пункта;
	 * icon (string) css класс тэга иконки;
	 * span (string) css класс тэга кол-ва уведомлений;
	 * visible (string, TRUE) видимость;
	 * module (string, NULL) имя модуля;
	 * return (integer, 1) тип возвращаемого результата. 
	 * 0-как один элемент, 1-как один из многих.
	 * 
	 * @return array возвращается массив вида array(count, item), где
	 * "count" (integer) - кол-во новых уведомлений.
	 * "item" (array) - пункт меню уведомлений.
	 */
	public static function menuNotificationsItem($options)
	{
		$count=0;
		$item=[];
		
		$module=A::get($options, 'module');
		if(!$module || \D::yd()->isActive($module)) {
			$hGetCount=A::get($options, 'hGetCount', function() { return 0; });
			$count=$hGetCount();
			$item=[
				'visible'=>A::get($options, 'visible', true),
				'label'=>\CHtml::tag('i', ['class'=>'glyphicon '.A::get($options, 'icon')], '', true)
					. ' ' . A::get($options, 'title')
					. \CHtml::tag('span', ['class'=>'notification_new_count '.A::get($options, 'span')], $count),
				'encodeLabel'=>false,
				'url'=>A::get($options, 'url', '#')
			];
		}
		
		if(A::get($options, 'return', 1)) return [$count, [$item]];
		else return [$count, $item];
	}

	/**
	 * Получить массив данных для пунктов меню уведомления "Обратная связь"
	 * @return array возвращается массив вида array(count, items), где
	 * "count" - кол-во новых уведомлений.
	 * "items" - пункты меню уведомлений.
	 */
	protected static function menuNotificationFeedbacks()
	{
		$total=0;
		$items=[];
		
		if(\D::yd()->isActive('feedback')) {
			foreach(\feedback\components\FeedbackFactory::getFeedbackIds() as $id) {
				$factory=\feedback\components\FeedbackFactory::factory($id);
				$item=self::menuNotificationsItem([
					'hGetCount'=>function() use ($factory) { 
						return $factory->getModelFactory()->getModel()->uncompleted()->count(); 
					},
					'url'=>'/cp/feedback/'.$id, 
					'title'=>$factory->getTitle(), 
					'icon'=>'glyphicon-earphone', 
					'span'=>"feedback-{$id}-count-in-title",
					'return'=>0
				]);
				
				$total+=$item[0];
				$items[]=$item[1];
			}
		}
		
		return [$total, $items];
	}
	
	/**
	 * Получить массив данных для пунктов меню уведомления "Отзывы на товар"
	 * @return array возвращается массив вида array(count, items), где
	 * "count" - кол-во новых уведомлений.
	 * "items" - пункты меню уведомлений.
	 */
	protected static function menuNotificationProductReviews()
	{
		return self::menuNotificationsItem([
			'hGetCount'=>function() { return \ProductReview::model()->unpublished()->count(); },
			'url'=>'/cp/review/index',
			'title'=>\Yii::t('\AdminModule.menu', 'notification.reviews.label'),
			'icon'=>'glyphicon-comment',
			'visible'=>\D::cmsIs('shop_enable_reviews'),
			'module'=>'shop'
		]);
	}

	/**
	 * Получить массив данных для пунктов меню уведомления "Отзывы"
	 * @return array возвращается массив вида array(count, items), где
	 * "count" - кол-во новых уведомлений.
	 * "items" - пункты меню уведомлений.
	 */
	protected static function menuNotificationReviews()
	{
		return self::menuNotificationsItem([
			'hGetCount'=>function() { return \reviews\models\Review::model()->notActivly()->count(); },
			'url'=>'/cp/reviews/index',
			'title'=>\Yii::t('\AdminModule.menu', 'notification.mreviews.label'),
			'icon'=>'glyphicon-comment',
			'module'=>'reviews'
		]);
	}
	
	/**
	 * Получить массив данных для пунктов меню уведомления "Вопрос-ответ"
	 * @return array возвращается массив вида array(count, items), где
	 * "count" - кол-во новых уведомлений.
	 * "items" - пункты меню уведомлений.
	 */
	protected static function menuNotificationQuestion()
	{
		return self::menuNotificationsItem([
			'hGetCount'=>function() { return \Question::getCount(); },
			'url'=>'/cp/question/index',
			'title'=>\Yii::t('\AdminModule.menu', 'notification.question.label'),
			'icon'=>'glyphicon-envelope',
			'span'=>'notification-question-count',
			'module'=>'question'
		]);
	}
} 
