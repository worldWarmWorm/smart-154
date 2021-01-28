<?php
/**
 * Набор полезных функций
 *
 */
Yii::import('admin.models.SettingsForm');

use common\components\helpers\HRequest as R;
use settings\components\helpers\HSettings;

class D
{
	public static $sloaded = false;
	/**
	 * Получить компонент d
	 * @return DApi
	 */
	public static function yd()
	{
		return \Yii::app()->d;
	}
	
	/**
	 * Результат условия.
	 * @param boolean $if результат условия.
	 * @param string $then значение на вывод при результате условия TRUE.
	 * @param string|null $else значение на вывод при результате условия FALSE.
	 * По умолчанию пустая строка.
	 * @return null|string
	 */
	public static function c($if, $then, $else=null)
	{
		return $if ? $then : $else;
	}
	
	public static function isDevMode()
	{
	    if(D::role('sadmin')) {
	        if(R::get('dev') !== null) {
	            \Yii::app()->user->setState('is_dev_mode', (int)R::get('dev') ? 1 : 0);
	        }
	        return (\Yii::app()->user->getState('is_dev_mode') === 1);
	    }
	    
	    return false;
	}
	
	public static function isTopAdmin()
	{
	    return (\Yii::app()->user->getState('is_top_admin') === 1) && (static::cms('system_admins'));
	}
	
	/**
	 * Проверяет роль пользователя
	 * @param string $role роль пользователя.
	 * @return bool
	 */
	public static function role($role)
	{
		if(!Yii::app()->user) return false;
		// return (\Yii::app()->user->role === $role);
		if($role == 'admin' && Yii::app()->user->getState('role') == 'sadmin') return true;
		
		return (Yii::app()->user->getState('role') === $role);
	}
	
	/**
	 * Получить значение переменной из настроек CMS
	 * @param string $param имя параметра.
	 * @param mixed $default значение параметра по умолчанию.
	 * @return mixed
	 */
	public static function cms($param, $default=null, $strict=false)
	{
		/** Для модуля регионов *//*
		$model = \SettingsForm::model();
        if(!static::$sloaded) {
            $model->loadSettings();
			static::$sloaded = true;
        }
        try {
	        return $model->$param ?: $default;
        } catch (\Exception $e) {
		/**/
        $value=\Yii::app()->settings->get('cms_settings', $param);
        if(!$value && $strict) {
            return ($value === null) ? $default : $value;
        }
		return $value ?: $default;
		/** Для модуля регионов *//*
		}
		/**/
	}
	
	/**
	 * Проверка значения из значений параметров CMS
	 * @param string $param имя параметра.
	 * @param mixed|NULL $value проверяемое значение параметра. По умолчанию (NULL) будет 
	 * возвращено преобразование значения параметра в тип BOOLEAN. 
	 * @param boolean $default результат возвращаемый по умолчанию, если параметр не найден.
	 * По умолчанию FALSE.
	 * @param boolean $strict строгая проверка типа. По умолчанию (FALSE) - не строгая.
	 * @return bool
	 */
	public static function cmsIs($param, $value=null, $default=false, $strict=false)
	{
		$paramValue=self::cms($param);
		return ($paramValue === null) 
			? $default 
			: (($value === null) ? (bool)$paramValue : ($strict ? ($paramValue === $value) : ($paramValue == $value)));	
	}

	/**
	 * Получить путь к файлу из настроек CMS
	 * @param string $param имя параметра.
	 * @param null|string $default
	 * @return null|string
	 */
	public static function cmsFile($param, $default=null)
	{
		$uploadPath = \Yii::app()->params['uploadSettingsPath'];
		$filename = static::cms($param);
		
		return $filename? ($uploadPath . $filename) : $default;	
	}

	/**
	 * Получить значение из настроек для Магазина 
	 * @param string $param имя параметра
	 * @param null|mixed $default значение по умолчанию
	 * @param boolean $forcyEmpty возвращать значение по умолчанию, 
	 * если значение параметра определяется как не заданное.
	 * @return null|mixed
	 */
	public static function shop($param, $default=null, $forcyEmpty=false)
	{
	    $settings=HSettings::getById('shop');
	    if(property_exists($settings, $param)) {
	        if($forcyEmpty && !$settings->$param) {
	            return $default;
	        }
	        return $settings->$param;
	    }
	    return $default;
	}
}
