<?php
namespace crud\components\helpers;

use common\components\helpers\HArray as A;
use crud\components\helpers\HCrud;

class HCrudPublic
{
    const ACTION_INDEX='index';
    const ACTION_VIEW='view';
    
    private static $routes=null;
    private static $cids=[];
    
    /**
     * Получить массив публичных маршрутов активных CRUD-моделей 
     * @return array возвращает массив вида array(
     *  cid=>array(action=>rules)
     * ), где action - это имя действия
     */
    public static function getRoutes()
    {
        if(static::$routes === null) {
            static::$routes=[];
            
            $config=HCrud::config();
            $actions=[self::ACTION_INDEX, self::ACTION_VIEW];
            foreach($config as $cid=>$cfg) {
                foreach($actions as $action) {
                    if($pattern=A::rget($cfg, "public.routes.{$action}")) {
                        static::$routes[$cid][$action]=$pattern;
                        if(!isset(static::$cids[$action][$pattern])) {
                            static::$cids[$action][$pattern]=[$cid];
                        }
                        else {
                            static::$cids[$action][$pattern][]=$cid;
                        }
                    }
                }
            }
        }
        
        return static::$routes;
    }
    
    /**
     * Получить ссылку на главную страницу модели
     * @param string $cid идетификатор конфигурации
     * @param array|integer $params массив дополнительных параметров.
     */
    public static function getIndexUrl($cid, $params=[])
    {
        $routes=static::getRoutes();
        if(!empty($routes[$cid][self::ACTION_INDEX])) {
            return static::addUrlParams($routes[$cid][self::ACTION_INDEX], $params); 
        }
        return null;
    }
    
    /**
     * Получить ссылку на детальную страницу модели
     * @param string $cid идетификатор конфигурации
     * @param array|integer $params массив дополнительных параметров.
     * Если передан массив, должен содержать параметр $attributeId=>идентификатор_модели.
     * Если передано число, будет использовано в качестве идентификатора модели 
     * @param string $attributeId наименование атрибута модели "Идентификатор".
     */
    public static function getViewUrl($cid, $params=[], $attributeId='id')
    {
        $url=null;
        
        if(is_numeric($params)) {
            $id=(int)$params;
            $params=[];
        }
        elseif(!empty($params[$attributeId])) {
            $id=(int)$params[$attributeId];
            unset($params[$attributeId]);
        }
        
        if(!empty($id)) {
            $routes=static::getRoutes();
            if(!empty($routes[$cid][self::ACTION_VIEW])) {
                $pattern=$routes[$cid][self::ACTION_VIEW];
                $attributes=static::getRoutePatternAttributes($pattern);
                if(empty($attributes) || ((count($attributes) === 1) && (key($attributes) === "<{$attributeId}>"))) {
                    $url=str_replace("<{$attributeId}>", $id, $pattern);
                }
                else {
                    $attributes["<{$attributeId}>"]=$attributeId;
                    if($model=HCrud::getById($cid, $id, ['select'=>('`t`.`' . implode('`,`t`.`', $attributes) . '`')], false, 'view')) {
                        $replacements=[];
                        foreach($attributes as $tag=>$attribute) {
                            $replacements[$tag]=$model->$attribute;                            
                        }
                        $url=strtr($pattern, $replacements);
                    }
                }
            }
        }
        
        return static::addUrlParams($url, $params);
    }
    
    public static function addUrlParams($url, $params)
    {
        if(!empty($url)) {
            $url='/' . ltrim($url, '/');
            if(!empty($params)) {
                $url.=(strpos($url, '?') === false) ? '?' : '&';
                $url.=http_build_query($params);
            }
        }
        return $url;
    }
    
    public static function getRoutePatternAttributes($pattern)
    {
        $attributes=[];
        if(preg_match_all('#(<([^>]+)>)#', $pattern, $m, PREG_SET_ORDER)) {
            foreach($m as $v) $attributes[$v[1]]=$v[2];
        }
        return $attributes; 
    }
}