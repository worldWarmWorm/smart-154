<?php
/**
 * Обработчик URL правил для публичной части CRUD-моделей
 * 
 */
namespace crud\components\rules;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use crud\components\helpers\HCrud;
use crud\components\helpers\HCrudPublic;

class PublicRule extends \common\components\base\Rule
{
    public $attributeId='id';
    
    /**
     * {@inheritDoc}
     * @see \CBaseUrlRule::createUrl()
     */
    public function createUrl($manager, $route, $params, $ampersand)
    {
        // @FIXME жесткая проверка на принадлежность к разделу администрирования
        if(Y::controller() && (Y::controller() instanceof \AdminController)) {
            return false;
        }
        
        $action=empty($params[$this->attributeId]) ? HCrudPublic::ACTION_INDEX : HCrudPublic::ACTION_VIEW;
        $routes=HCrudPublic::getRoutes();
        
        if(!empty($params['cid'])) {
            $cid=$params['cid'];
            unset($params['cid']);
            if(!empty($routes[$cid][$action])) {
                $routes=[$cid=>[$action=>$routes[$cid][$action]]];                
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
        
        foreach($routes as $cid=>$actions) {
            if(!empty($actions[$action])) {
                switch($action) {
                    case HCrudPublic::ACTION_INDEX:
                        if($url=HCrudPublic::getIndexUrl($cid, $params)) {
                            return ltrim($url, '/');
                        }
                        break;
                    case HCrudPublic::ACTION_VIEW:
                        if($url=HCrudPublic::getViewUrl($cid, $params, $this->attributeId)) {
                            return ltrim($url, '/');
                        }
                        break;
                }
            }
        }
        
        return false;
    }
    
    /**
     * {@inheritDoc}
     * @see \CBaseUrlRule::parseUrl()
     */
    public function parseUrl($manager, $request, $pathInfo, $rawPathInfo)    
    {
        $viewCandidats=[];
        $paths=explode('/', $pathInfo);
        $routes=HCrudPublic::getRoutes();
        foreach($routes as $cid=>$actions) {
            foreach($actions as $action=>$pattern) {
                if(!empty($actions[$action])) {
                    $pattern=$actions[$action];
                    switch($action) {
                        case HCrudPublic::ACTION_INDEX:
                            if(strcasecmp(trim($pathInfo, '/'), trim($pattern, '/')) === 0) {
                                $_REQUEST['cid']=$cid;
                                return HCrud::param($cid, 'public.index.url', 'common/crud/default/index');
                            }
                            break;
                        case HCrudPublic::ACTION_VIEW:
                            $parts=explode('/', $pattern);
                            if(count($parts) != count($paths)) {
                                continue;
                            }
                            
                            $viewCandidats[$cid]['pattern']=$pattern;
                            $viewCandidats[$cid]['parts']=$parts;
                            $viewCandidats[$cid]['weight']=0;
                            foreach($paths as $idx=>$chunk) {
                                if(!empty($parts[$idx]) && (strcasecmp($parts[$idx], $chunk) === 0)) {
                                    $viewCandidats[$cid]['weight']++;
                                    continue;
                                }
                                break;
                            }
                            break;
                    }
                }
            }
        }
        
        if(!empty($viewCandidats)) {
            uksort($viewCandidats, function($a, $b){
                if($b['weight'] > $a['weight']) return -1;
                return 1;
            });
            foreach($viewCandidats as $cid=>$params) {
                $columns=[];
                $pattern=$params['pattern'];
                $attributes=HCrudPublic::getRoutePatternAttributes($pattern);
                if(!empty($attributes)) {
                    foreach($params['parts'] as $idx=>$subpattern) {
                        $mpattern=preg_replace('#(<[^>]+>)#', '(?P$1.*?)', $subpattern);
                        if(preg_match('#^'.$mpattern.'$#i', $paths[$idx], $m)) {
                            foreach($attributes as $attribute) {
                                if(!empty($m[$attribute])) {
                                    $pattern=str_replace("<$attribute>", $m[$attribute], $pattern);
                                    $columns[$attribute]=$m[$attribute];
                                }
                            }
                        }
                    }
                    if(!empty($columns) && (strpos($pattern, '<')===false) && (strpos($pattern, '>')===false)) {
                        if($model=HCrud::getById($cid)->findByAttributes($columns, ['select'=>'id'])) {
                            $_REQUEST['cid']=$cid;
                            $_REQUEST['id']=$model->id;
                            return HCrud::param($cid, 'public.view.url', 'common/crud/default/view');
                        }
                    }
                }
            }
        }
        
        return false;
    }
}