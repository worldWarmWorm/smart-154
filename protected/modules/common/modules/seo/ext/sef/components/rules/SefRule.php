<?php
/**
 * Правило маршрутизации для моделей с поведением DAliasBehavior
 * 
 */
namespace seo\ext\sef\components\rules;

use common\components\helpers\HArray as A;

class SefRule extends \CBaseUrlRule
{
	/**
	 * @var array массив конфигурации. 
	 * array(
	 * 	className=>array(
	 *      'base'=>базовая ссылка (по умолчанию, не задана),
	 * 		'url'=>основная ссылка, 
	 * 		'alias'=>ссылка на которую замениться основная ссылка при формировании,
	 * 		если не указана, будет использована основная ссылка. 
	 * 		'id'=>attributeId,
	 * 		'sef'=>attributeSef,
	 * 		'module'=>имя модуля, если задан, будет проверяться подключен модуль 
	 * 		или нет (используется Yii::app()->d->isActive()).
	 *      'nestedset'=>'nestedSetBehavior' имя поведения модели типа NestedSet (по умолчанию, не задано),
	 *      'only'=>[] применять только для указанных ЧПУ 		
	 * )) 
	 * id по умолчанию "id",
	 * sef по умолчанию "sef",
	 */
	public $config=[];
	
	/**
	 * (non-PHPdoc)
	 * @see \CBaseUrlRule::createUrl()
	 */
	public function createUrl($manager, $route, $params, $ampersand)
	{
		foreach($this->config as $className=>$cfg) {
		    $base = A::get($cfg, 'base', '');
		    if($base) $base .= '/';
	  		if ($route == $cfg['url'])  {
	  			if($module=A::get($cfg, 'module')) {
	  				if(!\Yii::app()->d->isActive($module)) return false;
	  			}
	   			$id=$params['id'];
	   			$alias=$this->_getAliasById($className, $id);
	   			
	   			if((count(A::get($cfg, 'only', [])) > 0) && !in_array($alias, A::get($cfg, 'only', []))) {
	   			    continue;
	   			}
	   			
	   			unset($params['id']);
				$url=empty($alias) ? sprintf(A::get($cfg, 'alias', $cfg['url']).'/%d', $id) : sprintf('%s', $alias);
	
	    		if(!empty($params)) 
	    			$url.='?' . $manager->createPathInfo($params, '=', $ampersand);
	    		
	    		return $base . $url;
	  		}
		}

		return false;
	}

	/**
	 * (non-PHPdoc)
	 * @see \CBaseUrlRule::parseUrl()
	 */
	public function parseUrl($manager, $request, $pathInfo, $rawPathInfo)
	{
		foreach($this->config as $className=>$cfg) {
		    $base = A::get($cfg, 'base', '');
		    $fullPathInfo = $pathInfo;
		    if($base) {
		        $base .= '/';
		        $fullPathInfo = substr($fullPathInfo, strlen($base));
		    }
		    
		    if((count(A::get($cfg, 'only', [])) > 0) && !in_array($fullPathInfo, A::get($cfg, 'only', []))) {
		        continue;
		    }
		    
		    if ($id=$this->_getIdByAlias($className, $fullPathInfo)) {
				$_GET['id']=$id;
				return $cfg['url'];
			}
		}
		
		return false;
	}
	
	/**
	 * Получение имени атрибута id модели
	 * @param string $className имя класса модели
	 * @return string
	 */
	private function _getAttributeId($className)
	{
		return A::get(A::get($this->config, $className, array()), 'id', 'id');
	}
	
	/**
	 * Получение имени атрибута алиаса модели
	 * @param string $className имя класса модели
	 * @return string
	 */
	private function _getAttributeAlias($className)
	{
		return A::get(A::get($this->config, $className, array()), 'sef', 'sef');
	}
	
	/**
	 * Получение id модели по алиасу 
	 * @param string $className имя класса модели
	 * @param string $alias алиас модели.
	 * @return integer
	 */
	private function _getIdByAlias($className, $alias)
	{
		return (int)\Yii::app()->db->createCommand()
			->select($this->_getAttributeId($className))
			->from($className::model()->tableName())
			->where($this->_getAttributeAlias($className).'=:alias', array(':alias'=>$alias))
			->queryScalar();
	}

	/**
	 * Получение алиаса модели по id
	 * @param string $className имя класса модели
	 * @param integer $id id модели.
	 */
	private function _getAliasById($className, $id)
	{
		return \Yii::app()->db->createCommand()
			->select($this->_getAttributeAlias($className))
			->from($className::model()->tableName())
			->where($this->_getAttributeId($className).'=:id', array(':id'=>$id))
			->queryScalar();
	}
}
