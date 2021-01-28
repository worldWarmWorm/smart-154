<?php
/**
 * ActiveRecord Helper
 * 
 * @version 1.01
 * 
 * @history
 * 1.01
 * 	- Add massiveAssignment() method.
 */

class ARHelper extends CComponent
{
	/**
	 * @var integer Тип "Таблица базы данных".
	 */
	const DB_TTABLE = 1;
	
	/**
	 * @var integer Тип "Поле базы данных".
	 */
	const DB_TCOLUMN = 2;
	
	/**
	 * @var integer Тип "Значение поля базы данных".
	 */
	const DB_TVALUE = 3;
	
	/**
	 * Получение списка аттрибутов, существующих в таблице.
	 * Этот метод, отфильтровывает входящий массив аттрибутов,
	 * возвращая из него только те аттрибуты, которые существуют в таблице.
	 * @param \CActiveRecord $model модель.
	 * @param array $attributes массив аттрибутов.
	 * @return array 
	 */
	public static function getNonVirtualAttributes(\CActiveRecord $model, $attributes)
	{
		if(!is_array($attributes)) return array();
		
		$columns = $model->getTableSchema()->getColumnNames(); 
		foreach($attributes as $idx=>$attribute) {
			if(!in_array($attribute, $columns)) {
				unset($attributes[$idx]);
			}
		}
		
		return $attributes;
	}
	
	/**
	 * Массовое присваивание.
	 * @param mixed $model объект или имя класса модели
	 * @param bool $forciblyReturnModel в любом случае возвращать модель.  
	 * По умолчанию, если данные для присваивания не переданы возвращается false.
	 * @param bool $isPost указывает, что данные переданы исключительно методом POST.
	 * @return mixed объект модели, либо false, если не задано принудительное создание модели
	 * и не переданы значения для массового присваивания.
	 */
	public static function massiveAssignment($model, $forciblyReturnModel=false, $isPost=true)
	{
		$name = YiiHelper::slash2_($model);
		$isset = $isPost ? isset($_POST[$name]) : isset($_REQUEST[$name]);
		if($isset) {
			if(is_string($model)) $model = new $model();
			
			$model->attributes = $isPost ? $_POST[$name] : $_REQUEST[$name];
			
			return $model;
		}
		
		return $forciblyReturnModel ? (is_object($model) ? $model : new $model()) : false;  
	}
	
	/**
	 * Получить строку SQL запроса
	 * @param mixed $model модель или имя класса модели
	 * @param string $criteria объект критерия, который будет использован для построения запроса.
	 * Если не передан, возьметься критерия переданной модели. по умолчанию NULL. 
	 * @return string sql запрос
	 */
	public static function getSqlText($model, $criteria=null)
	{
		if(is_string($model) && class_exists($model))
			$model=$model::model();
	
		if(!($criteria instanceof \CDbCriteria))
			$criteria=$model->getDbCriteria();
	
		//$sql=$model->getCommandBuilder()->createFindCommand($model->getTableSchema(), $criteria)->getText();
		$command=$model->getCommandBuilder()->createFindCommand($model->getTableSchema(), $criteria);
		$command->prepare();
		$sql=$command->getText();
/*		ob_start();
		$command->getPDOStatement()->debugDumpParams();
		//$sql=preg_replace("/\n(.*)/m",'', ob_get_contents());
		$sql=ob_get_contents();
		//$sql='<pre style="white-space: pre-line;">'.preg_replace("/\n(.*)/m",'', ob_get_contents()).'</pre>';
		ob_end_clean();/**/
	
		if($criteria instanceof CDbCriteria) {
			$sql=strtr($sql, $criteria->params);
		}
	
		return $sql;
	}
	
	/**
	 * Экранирование имени таблицы базы данных.
	 * @see self::dbQ()
	 * @param string $name имя поля.
	 * @param string $db СDbConnection.
	 * @return string
	 */
	public static function dbQT($name, $db=null)
	{
		return self::dbQ($name, $db, self::DB_TTABLE);
	}
	
	/**
	 * Экранирование поля таблицы базы данных.
	 * @see self::dbQ()
	 * @param string $name имя поля.
	 * @param string $db СDbConnection.
	 * @return string
	 */
	public static function dbQC($name, $db=null)
	{
		return self::dbQ($name, $db, self::DB_TCOLUMN);
	}
	
	/**
	 * Экранирование значения поля таблицы базы данных.
	 * @see self::dbQ()
	 * @param string $str значение
	 * @param string $db СDbConnection.
	 * @return string
	 */
	public static function dbQV($str, $db=null)
	{
		return self::dbQ($str, $db, self::DB_TVALUE);
	}
	
	/**
	 * Эранирование переданного значения для запроса в базу данных.
	 * @param string $str значениe
	 * @param mixed $db СDbConnection. Если передано значение:
	 *  NULL, берется Yii::app()->db
	 *  \CActiveRecord, берется $db->getDbConnection()
	 * @param integer $type тип значения. HYii::DB_TTABLE, HYii::DB_TCOLUMN, HYii::DB_TVALUE.
	 * @return string
	 * @throws \CException
	 */
	public static function dbQ($str, $db=null, $type=self::DB_TCOLUMN)
	{
		switch($type) {
			case self::DB_TTABLE: $method = 'quoteTableName'; break;
			case self::DB_TCOLUMN: $method = 'quoteColumnName'; break;
			case self::DB_TVALUE: $method = 'quoteValue'; break;
			default:
				throw new \CException('Invalid quote type.');
		}
	
		return self::db($db)->$method($str);
	}

	/**
	 * Получить объект соединения
	 * @param mixed $db объект \CDbConnection (соединения с Базой Данных).
	 * Может быть передана модель CActiveRecord. Если для модели CActiveRecord::getDbConnection() вернул NULL,
	 * то будет возвращен \Yii::app()->db.
	 * По умолчанию NULL (\Yii::app()->db).
	 * @return Ambigous <NULL, CDbConnection>
	 */
	public static function db($db=null)
	{
		if($db instanceof \CDbConnection) 
			return $db;
		
		if($db instanceof \CActiveRecord) 
			return $db->getDbConnection() ?: \Yii::app()->db;
		
		return \Yii::app()->db;
	}

	/**
	 * Расширение метода \CDbSchema::getTable()
	 * @param string $name имя таблицы
	 * @param boolean $refresh обновить метаданные таблицы.
	 * @param mixed $db объект соединения. По умолчанию NULL (HDb::db()).
	 * @return \CDbTableSchema
	 */
	public static function getTable($name, $refresh=false, $db=null)
	{
		return self::db($db)->getSchema()->getTable($name, $refresh);
	}

}