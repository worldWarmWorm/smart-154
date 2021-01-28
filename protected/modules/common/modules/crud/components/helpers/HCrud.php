<?php
/**
 * Класс-помощник для модуля "CRUD"
 */
namespace crud\components\helpers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use common\components\helpers\HFile;
use common\components\helpers\HEvent;

class HCrud
{
    const RELATION_DEFAULT = 'has_many';
    const RELATION_HAS_MANY = 'has_many';
    const RELATION_BELONGS_TO = 'belongs_to';
    
    const CACHE_ALIAS = 'application.runtime.crud';
    
    const LOCAL_ALIAS = 'application.local.crud';
    
    /**
	 * @var NULL|array подготовленная конфигурация.
	 * По умолчанию (NULL) - не подготовлена.
	 */
	protected static $configPrepared=null;
	protected static $configAliases=[];

	/**
	 * Сбросить кэш конфигурации
	 */
	public static function resetConfigPrepared()
	{
	    static::$configPrepared=null;
	}
	
	/**
	 * Получить полный путь к файлу кэша
	 * @param string $id идентифкатор настроек CRUD для модели.
	 * @param boolean $returnArray возвращать в виде массива или только полный путь к файлу.
	 * Возвращается в массиве: [
	 *  'delimiter'=>символ разделителя хэша полного имени класса и хэша конфигурации,
     *  'class_hash'=>хэша конфигурации,
     *  'config_hash'=>хэша конфигурации,
     *  'path'=>$path,
     *  'filename'=>$filename,
     *  'fullpath'=>$fullpath
	 * ] 
	 * @return string|array
	 */
	public static function getCacheFilename($id, $returnArray=false)
	{
	    $delimiter = '_';
	    $hashClass = md5(static::param($id, 'class'));
	    $hashConfig = md5(json_encode(static::config($id)));
	    $path = \Yii::getPathOfAlias(self::CACHE_ALIAS);
	    $filename = $hashClass . $delimiter . $hashConfig . '.php';
	    $fullpath = $path . '/' . $filename;
	    
	    if($returnArray) {
	        return [
	            'delimiter'=>$delimiter,
	            'class_hash'=>$hashClass,
	            'config_hash'=>$hashConfig,
	            'path'=>$path,
	            'filename'=>$filename,
	            'fullpath'=>$fullpath
	        ];
	    }
	    	    
	    return $fullpath;
	}
	
	/**
	 * Проверить изменилась ли конфигурация для файла в кэше.
	 * @param string $id идентифкатор настроек CRUD для модели.
	 * @param boolean $clear очищать кэш от старых файлов кэша переданной конфигурации.
	 * По умолчанию (true) очищать.
	 */
	public static function isCacheFileModified($id, $clear=true)
	{
	    $modified = true;
	    
	    $fileData = static::getCacheFilename($id, true);
	    if(is_file($fileData['fullpath'])) {
	        $modified = false;
	    }
	    else {
	        $files = HFile::getFiles($fileData['path']);
	        foreach($files as $filename) {
	            if(strpos($filename, $fileData['class_hash'] . $fileData['delimiter']) === 0) {
	                @unlink($fileData['path'] . '/' . $filename);
	            }
	        }
	    }
	    
	    return $modified;
	}
	
	/**
	 * Проверить изменилась ли конфигурация для файла в кэше.
	 * @param string $id идентифкатор настроек CRUD для модели.
	 * @param mixed $data данные для записи в файл кэша
	 */
	public static function saveCacheFile($id, $data)
	{
	    $fileData = static::getCacheFilename($id, true);
	    
	    if(!is_dir($fileData['path'])) {
	        HFile::mkDir($fileData['path'], 0755, true);
        }
        
        return file_put_contents($fileData['fullpath'], $data);	    
	}
	
	/**
	 * Получить объект модели.
	 * @param string $id идентифкатор настроек CRUD для модели.
	 * @param integer|TRUE|NULL $pk идентификатор модели. По умолчанию NULL, 
	 * будет возвращена новая модель. 
	 * Может быть передано строгое TRUE, в этом случае будет возвращен 
	 * результат вызова статического метода \CActiveRecord::model().
	 * @param array|\CDbCriteria|NULL $criteria объект или конфигурация 
	 * дополнительных параметров для выборки модели. 
	 * По умолчанию NULL не задана.
	 * @param boolean $http404 если модель не найдена бросать исключение HTTP 404.
	 * По умолчанию (TRUE) - бросать исключение.
	 * @param string $scenario имя сценария новой модели. По умолчанию "insert".
	 * @return \CActiveRecord объект модели, либо NULL, если 
	 * возникли ошибки в конфигурации.
	 */
	public static function getById($id, $pk=null, $criteria=null, $http404=true, $scenario='insert')
	{
	    if($className=static::param($id, 'class')) {
			if(is_array($className)) {
				$scenario=A::get($className, 1, $scenario);
				$className=A::get($className, 0);
			} 
			$model=new $className($scenario);
			if(($pk !== true) && ($pk !== null)) {
				if($criteria === null) $criteria='';
				$model=$model->findByPk($pk, $criteria);
				
				if(!$model && $http404) throw new \CHttpException(404);
				$model->setScenario($scenario);
			}
			return $model;
		}
		
		if($http404) throw new \CHttpException(404);
		
		return null;
	}
	
	/**
	 * Получить алиас пути к конфигурации
	 * @param string $id идентификатор конфигурации
	 * @return string|null
	 */
	public static function getConfigAlias($id)
	{
	    return A::get(static::$configAliases, $id);
	}
	
	/**
	 * Получить конфигурацию настроек.
	 * @param integer $id идентифкатор настроек CRUD для модели. 
	 * По умолчанию (NULL) - получить все настройки.  
	 * @return array|NULL
	 */
	public static function config($id=null)
	{
		if($module=Y::module('common.crud')) {
		    $config=static::prepareConfig($module->config);		    
			if($id) {
				return A::get($config, $id);
			}
			return $config; 
		}
		
		return null;
	} 
	
	/**
	 * Получить значение параметра конфигурации.
	 * @param string $id идентифкатор настроек CRUD для модели.
	 * @param string|array $name имя параметра или массив значений параметра.
	 * @param mixed $default значение по умолчанию. По умолчанию NULL.
	 * @param boolean $use проверять наличие параметра "use" и обработать его. 
	 * По умолчанию (FALSE) - не проверять.
	 * @return mixed
	 */
	public static function param($id, $name, $default=null, $use=false)
	{
	    if($config=static::config($id)) {
			if(is_array($name)) {
				$value=empty($name) ? $default : $name;
			}
			else {
				$value=A::rget($config, $name, $default);
			}
			
			if(!empty($value) && $use) {
			    if(is_string($name) && !is_callable($name)) $valueUse=static::getParamUse($id, $name.'.use', $default);
			    elseif(is_array($name)) $valueUse=static::getUse($id, A::get($name, 'use'), $default);
				if(is_array($value) && isset($value['use'])) unset($value['use']);
				if(empty($value)) $value=[];
				
				return empty($valueUse) ? $value : A::m($valueUse, $value);
			}
			return $value;
		}
		
		return $default;
	}
	
	/**
	 * Получить данные для ссылки конфигурации.
	 * @param string $id идентифкатор настроек CRUD для модели.
	 * @param string $path путь к ссылке в конфигурации. Напр. "crud.update.url".
	 * @param string $default ссылка по умолчанию. По умолчанию "#".
	 * @param array $params дополнительные параметры для ссылки. По умолчанию пустой массив.
	 * @param string|NULL $mode режим преобразования. По умолчанию (NULL) без преобразования.
	 * Режимы:
	 * "toString" (или "s") - преобразовывать параметры в строку PHP кода массива.
	 * "byCreateUrl" (или "с") - получить массив для использования создания ссылки, напр.,
	 * методом CController::createUrl().  
	 * @return array массив вида [link, params] 
	 */
	public static function getConfigUrl($id, $path, $default='#', $params=[], $mode=null)
	{
		$url=HCrud::param($id, $path, $default);
		if(is_array($url)) {
			$link=array_shift($url);
			$params=A::m($url, $params);
		}
		else $link=$url;
		
		if(($mode == "byCreateUrl") || ($mode == "c")) {
			return A::m([$link], $params);
		}
		if(($mode == "toString") || ($mode == "s")) {
			$params=A::toPHPString($params, true, true);
		}
		
		return [$link, $params];
	}
	
	/**
	 * Получить значение параметра "use".
	 * Дополнительный параметр конфигурации "use" может содержать значение
	 * 1) (string) "путь внутри текущей конфигурации"
	 * 2) (array) ["пусть к файлу конфигурации", "путь к значению внтури массива конфигурации"]
	 * @param string $id идентифкатор настроек CRUD для модели.
	 * @param string $path путь к параметру "use", включая сам "use". Напр., "crud.create.form.use"
	 * @param mixed $default значение по умолчанию. По умолчанию NULL.
	 */
	public static function getParamUse($id, $path, $default=null)
	{
	    return static::getUse($id, A::rget(static::config($id), $path), $default);
	}
	
	/**
	 * Получить значение параметра "use".
	 * @param string|array $id идентифкатор настроек CRUD для модели. Может быть 
	 * передан массив конфигурации.
	 * @param string|array $use идентифкатор настроек CRUD для модели.
	 * Дополнительный параметр конфигурации "use" может содержать значение
	 * 1) (string) "путь внутри текущей конфигурации"
	 * 2) (array) ["пусть к файлу конфигурации", "путь к значению внтури массива конфигурации"]
	 * @param mixed $default значение по умолчанию. По умолчанию NULL.
	 * @return mixed
	 */
	public static function getUse($id, $use, $default=null)
	{
		if(is_array($id)) $config=$id;
		else $config=static::config($id);
		
		$value=$default;
		if(is_array($use)) {
			if((count($use) == 2) && ($cfg=HFile::includeByAlias($use[0]))) {
				if($use[1] === null) $value=$cfg;
				else {
					$value=A::rget($cfg, $use[1], $default);
				}
			}
		}
		elseif(is_string($use)) {
			$value=A::rget($config, $use, $default);
		}
		
		return $value;
	}
	
	/**
	 * Получить конфигурацию параметра "sortable" (сортировки).
	 * @param string $id идентифкатор настроек CRUD для модели.
	 * @param string $path путь к параметру "sortable". По умолчанию "crud.index.gridView".
	 * @param boolean $checkDisabled проверять параметр "disabled" или нет. Если установлено
	 * (TRUE) то при наличии параметра "disabled"=>true, будет возвращено NULL.
	 * По умолчанию (FALSE) - не проверять.
	 * @return array|NULL массив конфигурации сортировки.
	 */
	public static function getSortable($id, $path='crud.index.gridView', $checkDisabled=false)
	{
	    if($sortable=static::param($id, $path.'.sortable', null, true)) {
			if(A::get($sortable, 'category')) return $sortable;
		}
		return null;
	}
	
	/**
	 * Получить пункты меню для виджета zii.widgets.CMenu (для раздела администрирования).
	 * @param \CController $controller объект контроллера, 
	 * который будет использован для создания ссылки.
	 * @param string|array|NULL $id идетификатор настроек модели 
	 * для которых возвращать пункт меню. Может быть передан массив идентфикаторов.
	 * По умолчанию (NULL) - возвращать все пункты меню. 
	 * @param string $baseUrl базовая ссылка для пунктов меню.
	 * @param boolean $returnItem возвращать только конфигурацию одного пункта меню. 
	 * По умолчанию (FALSE) - если результат будут содеражать только один пункт меню, 
	 * возвратиться как массив из одного пункта меню.
	 * @return multitype:multitype:\common\components\helpers\mixed NULL
	 */
	public static function getMenuItems($controller, $id=null, $baseUrl='/crud/admin/default/index', $returnItem=false)
	{
		$items=[];
		
		$module=Y::module('common.crud.admin');
		
		if($config=static::config()) {
			// @var callable получить пункт меню.
			$fAddItem=function($id, $params) use (&$items, $controller, $baseUrl, $config) {
				if(!A::rget($params, 'menu.backend.disabled', false)) {
					if($label=A::rget($params, 'menu.backend.label', A::rget($params, 'crud.index.title'))) {
						$items[]=[
							'label'=>$label,
							'url'=>[$baseUrl, 'cid'=>$id],
							'active'=>isset($_REQUEST['cid']) && ($_REQUEST['cid'] == $id)
						];
					}
				}
			};
			
			if(is_string($id)) {
				$fAddItem($id, A::get($config, $id));
			}
			else {
				if(is_array($id)) {
					$config=array_intersect_key($config, array_flip($id));
				}
				
				foreach($config as $id=>$params) {
					$fAddItem($id, $params);
				}
			}
		}
		
		if($returnItem) return array_pop($items);
		else return $items;
	}
	
	/**
	 * Обработчик перед загрузкой для модели с отношением HCrud::RELATION_BELONGS_TO 
	 * @param string $id идентифкатор настроек CRUD для модели.
	 * @return boolean
	 */
	public static function relationBelongsToOnBeforeLoad($cid)
	{
	    $config = static::config($cid);
	    $relations = A::get($config, 'relations', []);
	    $hasError = true;
	    foreach($relations as $parentCrudId=>$relationConfig) {
	        if(A::get($relationConfig, 'type') == self::RELATION_BELONGS_TO) {
	            if($relationAttribute = A::get($relationConfig, 'attribute')) {
	                if($relationId = (int)R::get($parentCrudId)) {
	                    if($parentConfig = static::config($parentCrudId)) {
	                        $parentClass = A::get($parentConfig, 'class');
	                        $parentTitleAttribute = A::get($relationConfig, 'titleAttribute', 'title');
	                        if($parent=$parentClass::modelById($relationId, ['select'=>'id, ' . $parentTitleAttribute])) {
	                            $dataProviderCondition = A::rget($config, 'crud.index.gridView.dataProvider.criteria.condition', '');
	                            if($dataProviderCondition) {
	                                $dataProviderCondition .= ' AND ';
	                            }
	                            static::$configPrepared[$cid]['crud']['index']['gridView']['dataProvider']['criteria']['condition'] = $dataProviderCondition . "(`{$relationAttribute}`" . ($relationId ? "='{$relationId}'" : "<>`$relationAttribute`") . ")";
	                            
	                            foreach(['index', 'update', 'create', 'delete'] as $crudSection) {
	                                if(!empty(static::$configPrepared[$cid]['crud'][$crudSection]['url'])) {
	                                    $url = static::$configPrepared[$cid]['crud'][$crudSection]['url'];
	                                    if(is_array($url)) {
	                                        if(empty($url[$parentCrudId])) {
	                                            $url[$parentCrudId] = $relationId;
	                                        }
	                                    }
	                                    elseif(is_string($url)) {
	                                        $url = [$url, $parentCrudId=>$relationId];
	                                    }
	                                    static::$configPrepared[$cid]['crud'][$crudSection]['url'] = $url;
	                                }
	                            }
	                            
	                            if(empty(static::$configPrepared[$cid]['crud']['breadcrumbs'])) {
	                                static::$configPrepared[$cid]['crud']['breadcrumbs'] = [];
	                            }
	                            
	                            $breadcrumbTitle = A::rget($parentConfig, 'crud.index.title');
	                            if($breadcrumbTitle) {
	                                $breadcrumbUrl = A::rget($parentConfig, 'crud.index.url', '/crud/admin/default/index');
	                                if(is_array($breadcrumbUrl)) {
	                                    $breadcrumbUrl['cid'] = $parentCrudId;
	                                }
	                                else {
	                                    $breadcrumbUrl = [$breadcrumbUrl, 'cid'=>$parentCrudId];
	                                }
	                                static::$configPrepared[$cid]['crud']['breadcrumbs'][$breadcrumbTitle]=$breadcrumbUrl;
	                            }
	                            
	                            static::$configPrepared[$cid]['crud']['breadcrumbs'][] = $parent->$parentTitleAttribute;
	                            
	                            $hasError = false;
	                        }
	                    }
	                }
	            }
	        }
	    }
	    
	    if(!$hasError) {
            return true;
	    }
	    
	    R::e404();
	}
	
	/**
	 * Подготовка конфигурации
	 * @param array|string $config конфигурация
	 * @return array
	 */
	protected static function prepareConfig($config, $loadUses=true)
	{
	    if(static::$configPrepared === null) {
			$prepared=[];
				
			if(is_string($config)) {
				$config=HFile::includeByAlias($config);
			}
	
			if(is_array($config)) {
				foreach($config as $id=>$cfg) {
					if(is_string($id)) {
					    $path=$cfg;
						if(is_string($cfg) && ($cfg=HFile::includeByAlias($cfg))) {
							$prepared[$id]=$cfg;
							static::$configAliases[$id]=$path;
						}
						elseif(is_array($cfg)) {
						    $prepared[$id]=$cfg;
						}
					}
					elseif(is_string($cfg)) {
						$mainConfig=HFile::includeByAlias($cfg);
						if(is_array($mainConfig)) {
							foreach($mainConfig as $id2=>$cfg2) {
							    $path=$cfg2;
								if(is_string($id2) && is_string($cfg2) && ($cfg2=HFile::includeByAlias($cfg2))) {
									$prepared[$id2]=$cfg2;
									static::$configAliases[$id2]=$path;
								}
							}
						}
					}
				}
			}
			
			if($loadUses) {
				$prevLevel=0;
				A::rwalk($prepared, function(&$cfg, $key, &$path, $level) use (&$prepared, &$prevLevel) {
				    if(($key === 'use') && is_array($cfg)) {
					    $cfg=static::getUse([], $cfg);
						if(!empty($path)) {
							$spath=implode('.', array_slice($path, 0, $level));
							A::runset($prepared, $spath.'.use');
							A::rset($prepared, $spath, $cfg, true, -1);
						}
					}
					else {
					    if($level === 0) $path=[];
						elseif($level < $prevLevel) {
							if(!empty($path) && $level) array_splice($path, $level);
							else $path=[];
						}
						elseif($level == $prevLevel) {
							if(!empty($path)) array_pop($path);
						}
						$path[]=$key;				
						$prevLevel=$level;
					}
				}, []);
			}
			
			
			
			static::$configPrepared=static::afterPrepareConfig($prepared);

			HEvent::raise('onCrudAfterConfigPrepared');
		}
	
		return static::$configPrepared;
	}
	
	/**
	 * Пост-обработка конфигурации
	 * @param array $config конфигурация
	 * @return array
	 */
	protected static function afterPrepareConfig($config)
	{
	    foreach($config as $cid=>$cfg) {
	        if($relations = A::get($cfg, 'relations')) {
	            foreach($relations as $relationCrudId=>$relationConfig) {
	                switch(A::get($relationConfig, 'type', self::RELATION_DEFAULT)) {
	                    case self::RELATION_BELONGS_TO:
	                        if($relationCrudConfig = $config[$relationCrudId]) {
	                            foreach(['index', 'update', 'create', 'delete'] as $crudSection) {
	                                if(!empty($cfg['crud'][$crudSection]['url'])) {
	                                    $url = $cfg['crud'][$crudSection]['url'];
	                                    if(is_array($url)) {
	                                        if(empty($url[$relationCrudId])) {
	                                            $url[$relationCrudId] = R::get($relationCrudId);
	                                        }
	                                    }
	                                    elseif(is_string($url)) {
	                                        $url = [$url, $relationCrudId=>R::get($relationCrudId)];
	                                    }
	                                    $config[$cid]['crud'][$crudSection]['url'] = $url;
	                                }
	                            }
	                        }
	                        
	                        if(empty($config[$cid]['crud']['onBeforeLoad'])) {
	                           $config[$cid]['crud']['onBeforeLoad'] = ['\crud\components\helpers\HCrud', 'relationBelongsToOnBeforeLoad'];
	                        }
	                        break;
	                    case self::RELATION_HAS_MANY:
	                        break;
	                }
	            }
	        }
	        elseif($events = A::get($cfg, 'events')) {
	            HEvent::registerByConfig($events);
	        }
	    }
	    	    
	    return $config;
	}
}
