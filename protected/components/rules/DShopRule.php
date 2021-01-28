<?php
/**
 * Правило маршрутизации для каталога
 * 
 * $this->createUrl('shop/category', ['id'=>category-id]);
 * $this->createUrl('shop/product', ['id'=>product-id]);
 * 
 * 1) /baseUrl/aliased/path/to/category/product-alias
 * 2) /baseUrl/aliased/path/to/category/product-id
 * 3) /baseUrl/category-id/product-alias
 * 4) /baseUrl/category-id/product-id
 */
use AttributeHelper as A;

class DShopRule extends CBaseUrlRule
{
	/**
	 * @var boolean создавать/разбирать ЧПУ товара используя только категорию, которой он принадлежит (TRUE), 
	 * либо создавать относительно категории, переданной в параметре/разрешать разбирать ЧПУ товара 
	 * относительно привязанных категорий (FALSE).
	 */
	public $productOneSef=true;
	
	public $baseUrl='catalog';
	
	public $moduleName='shop';
	
	public $baseControllerName='shop';
	public $baseControllerAction='index';
	
	public $categoryModel='Category';
	public $categoryControllerName='shop';
	public $categoryControllerAction='category';
	
	public $productModel='Product';
	public $productControllerName='shop';
	public $productControllerAction='product';
	
	public $brandBaseAlias='brand';
	
	protected $cacheLoaded=false;
	protected $cacheData=[];
	protected $cacheVar='dshoprule_data';
	
	protected $cacheRoutesLoaded=false;
	protected $cacheRoutes=[];
	protected $cacheRoutesVar='dshoprule_routes';
	
	/**
	 * (non-PHPdoc)
	 * @see CBaseUrlRule::createUrl()
	 */
	public function createUrl($manager, $route, $params, $ampersand)
	{
		if(!\Yii::app()->d->isActive($this->moduleName)) 
			return false;
		
		if(empty($params['id'])) {
			if(($route == ($this->baseControllerName . '/' . $this->baseControllerAction)) 
				|| ($route == $this->baseControllerName)) 
			{
				return $this->getBaseUrl() . $this->createPathInfo($manager, $params, $ampersand);
			}
		}
		else {
			if($route == ($this->categoryControllerName . '/' . $this->categoryControllerAction)) {
				$brand=null;
				if(!empty($params['brand_id'])) {
					$brand=Brand::model()->actived()->findByPk($params['brand_id'], ['select'=>'id, alias']);
					unset($params['brand_id']);
				}

				if($brand) {
                    $prefix='brand_' . $brand->id . '_';
                }
                else {
                    $prefix='';
                }
				if($url=$this->cacheCategoryGet($params['id'], $prefix)) {
					unset($params['id']);
					return $url . $this->createPathInfo($manager, $params, $ampersand);
				}
				elseif($url=$this->createCategoryUrl($manager, $route, $params, $ampersand)) {
					$id=$params['id'];
					unset($params['id']);
					if(!empty($brand)) {
						$url.='/'.$this->brandBaseAlias.'/'.$brand->alias;
					}
					return $this->cacheCategorySet(
						$id, 
						$this->getUrl([$this->getBaseUrl(), $url]),
						$prefix
					) . $this->createPathInfo($manager, $params, $ampersand);
				}
			}
			elseif($route == ($this->productControllerName . '/' . $this->productControllerAction)) {
				if(($url=$this->cacheProductGet($params['id'], (empty($params['category_id']) ? '' : ($this->productOneSef ? 'c'.$params['category_id'] : 'c'))))) {
					unset($params['id']);
					if(!empty($params['category_id'])) {
						unset($params['category_id']);
					}
					return $url . $this->createPathInfo($manager, $params, $ampersand);
				}
				else {
					$productModel=$this->productModel;
					$product=$productModel::model()->findByPk($params['id'], ['select'=>'`t`.`id`, `t`.`alias`, `t`.`category_id`']);
					
					if(!empty($product)) {
						$categoryId=$product->category_id;
						if(!empty($params['category_id'])) {
							if(!$this->productOneSef) {
								$categoryId=(int)$params['category_id'];
							}
							unset($params['category_id']);
						}
						$url=$this->createCategoryUrl($manager, $route, ['id'=>$categoryId], $ampersand);
						if(empty($url)) {
							$url=$categoryId;
						}
						
						$id=$params['id'];
						unset($params['id']);
						return $this->cacheProductSet(
							$id, 
							$this->getUrl([$this->getBaseUrl(), $url, (empty($product->alias) ? $product->id : $product->alias)]) 
						) . $this->createPathInfo($manager, $params, $ampersand);
					}
				}
			}
		}
		
		return false;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CBaseUrlRule::parseUrl()
	 */
	public function parseUrl($manager, $request, $pathInfo, $rawPathInfo)
	{
		if(!\Yii::app()->d->isActive($this->moduleName))
			return false;
		
		if($route=$this->cacheRouteGet($pathInfo)) {
			return $route; 
		}
		
		if(preg_match('/^'.$this->baseUrl.'$/', $pathInfo)) {
			return $this->baseControllerName . '/' . $this->baseControllerAction;
		}
		elseif(preg_match('#^'.$this->baseUrl.'/([^/]+)$#', $pathInfo, $path)) {
			if($category=$this->getByRoute($this->categoryModel, $path[1])) {
				$url=$this->createUrl($manager, "{$this->productControllerName}/{$this->categoryControllerAction}", ['id'=>$category->id], '&');
				if($url != $pathInfo) {
					header("HTTP/1.1 301 Moved Permanently"); 
					header("Location: /{$url}"); 
					exit(); 
				}
				$_GET['id']=$category->id;
				return $this->cacheRouteSet(
					$pathInfo, 
					$this->categoryControllerName . '/' . $this->categoryControllerAction,
					['id'=>$category->id]
				);
			}
		}
		else {
			$path=explode('/', $pathInfo);
			if(!empty($path) && ($path[0]==$this->baseUrl)) {
				unset($path[0]);
			
				$baseCategoryUrl=array_shift($path);
				$baseCategory=$this->getByRoute($this->categoryModel, $baseCategoryUrl, [
					'select'=>'id, alias, root, lft, rgt, level',
					'condition'=>'lft=1'
				]);
				
				if(empty($baseCategory) || (empty($baseCategory->alias) && (count($path) > 2)))
					return false;
				
				$fGetFinalRoute=function($category, $path) use ($pathInfo) {
					if(empty($category->alias)) {
						$product=$this->getByRoute($this->productModel, $path);
						if(!empty($product)) {
							$cacheParams=['id'=>$product->id];
							if(!$this->productOneSef) {
								$_GET['category_id']=$category->id;
								$cacheParams['category_id']=$category->id;
							}
							if(!empty($_GET['brand_id'])) {
								$cacheParams['brand_id']=$_GET['brand_id'];
							}
							$url=$this->createUrl($manager, "{$this->productControllerName}/{$this->productControllerAction}", ['id'=>$product->id], '&');
							if($url != $pathInfo) {
								header("HTTP/1.1 301 Moved Permanently"); 
								header("Location: /{$url}"); 
								exit(); 
							}
							$_GET['id']=$product->id;
							return $this->cacheRouteSet(
								$pathInfo, 
								$this->productControllerName . '/' . $this->productControllerAction,
								$cacheParams
							);
						}
					}
					else {
						$children=$category->children()->findAll(['select'=>'id, alias, lft, rgt, root, level']);
						if(!empty($children)) {
							$id=null;
							foreach($children as $child) {
								if(is_numeric($path) && ($child->id == $path)) {
									$id=$child->id;
									break;
								} 
								elseif($child->alias == $path) {
									$id=$child->id;
									break;
								}
							}
							if($id) {
								$_GET['id']=$id;
								$cacheParams=['id'=>$id];
								
								if(!empty($_GET['brand_id'])) {
									$cacheParams['brand_id']=$_GET['brand_id'];
								}

								$url=$this->createUrl($manager, "{$this->productControllerName}/{$this->categoryControllerAction}", ['id'=>$id], '&');
    							if($url != $pathInfo) {
    								header("HTTP/1.1 301 Moved Permanently"); 
    								header("Location: /{$url}"); 
    								exit(); 
    							}
								
								return $this->cacheRouteSet(
									$pathInfo,
									$this->categoryControllerName . '/' . $this->categoryControllerAction,
									$cacheParams
								);
							}
						}
						
						$product=$this->getByRoute($this->productModel, $path, ['select'=>'id, alias, category_id']);
						
						if(!empty($product)) {
							$url=$this->createUrl($manager, "{$this->productControllerName}/{$this->productControllerAction}", ['id'=>$product->id], '&');
							if($url != $pathInfo) {
								header("HTTP/1.1 301 Moved Permanently"); 
								header("Location: /{$url}"); 
								exit(); 
							}
							// проверка на принадлежность данной категории 
							// (в том числе и как привязанный товар)
							$cacheParams=[];
							if($product->category_id != $category->id) {
								if(!RelatedCategory::model()->exists(
									'category_id=:categoryId AND product_id=:productId',
									[':categoryId'=>$category->id, ':productId'=>$product->id]
								)) {
									// @FIXME в настройках магазина, может быть выставлен уровень вложеннности категорий,
									// но в данном случае, проверяется только на существование потомка у текущей категории.
									if(!$category->descendants()->exists('`t`.`id`=:id', [':id'=>$product->category_id])) {
										return false;
									}
								} 
								$_GET['category_id']=$category->id;
								$cacheParams['category_id']=$category->id;
							}
							
							if(!empty($_GET['brand_id'])) {
								$cacheParams['brand_id']=$_GET['brand_id'];
							}
							 
							$_GET['id']=$product->id;
							$cacheParams['id']=$product->id;
							return $this->cacheRouteSet(
								$pathInfo,
								$this->productControllerName . '/' . $this->productControllerAction,
								$cacheParams
							);
						}
					}
					return false;
				};
				
				if(count($path) >= 2) {
					if($path[count($path)-2] == $this->brandBaseAlias) {
						$brand=Brand::model()->findByAttributes(['alias'=>array_pop($path)]);
						if(empty($brand)) {
							return false;
						}
						$_GET['brand_id']=$brand->id;
						array_pop($path);
					}
				}
				
				if(empty($path)) {
					$_GET['id']=$baseCategory->id;
					$cacheParams=['id'=>$baseCategory->id];
					
					if(!empty($_GET['brand_id'])) {
						$cacheParams['brand_id']=$_GET['brand_id'];
					}
					
					return $this->cacheRouteSet(
							$pathInfo,
							$this->categoryControllerName . '/' . $this->categoryControllerAction,
							$cacheParams
					);
				}
				elseif(count($path) == 1) {
					return $fGetFinalRoute($baseCategory, $path[0]);
				}
				else {
					$finalPath=array_pop($path);
					
					$fGetCategory=function($category, $path) use (&$fGetCategory) {
						if(empty($path)) return $category;
						$alias=array_shift($path);					
						$children=$category->children()->findAll([
							'select'=>'id, alias, root, lft, rgt, level'
						]);
						if(!empty($children)) {
							foreach($children as $child) {
								if($child->alias == $alias) {
									return $fGetCategory($child, $path);
								}
							}
						}
						return false;
					};
					if($category=$fGetCategory($baseCategory, $path)) {
						return $fGetFinalRoute($category, $finalPath);
					}
				}
			}
		}

		return false;
	}
	
	/**
	 * Содание ЧПУ для категории. 
	 * @see CBaseUrlRule::createUrl()
	 */
	protected function createCategoryUrl($manager, $route, $params, $ampersand)
	{
		if(empty($params['id'])) 
			return false;
		
		if($url=$this->cacheCategoryGet($params['id'], 'c')) {
			return $url;
		}
		
		$categoryModel=$this->categoryModel;
		$category=$categoryModel::model()->findByPk($params['id'], [
			'select'=>'id, alias, root, lft, rgt, level'
		]);
	
		if($category) {
			if($category->alias) {
				$path=[];
					
				$ancestors=$category->ancestors()->findAll([
					'select'=>'id, alias, root, lft, rgt, level'
				]);
	
				if($ancestors) {
					foreach($ancestors as $ancestor) {
						if(!$ancestor->alias) {
							return $this->cacheCategorySet($params['id'], $category->id, 'c');
						}
						$path[]=$ancestor->alias;
					}
				}
				$path[]=$category->alias;
				
				return $this->cacheCategorySet($params['id'], $this->getUrl($path), 'c');
			}
			
			return $this->cacheCategorySet($params['id'], $category->id, 'c');
		}
		
		return false;
	}
	
	/**
	 * Получить ссылку.
	 * @param array $path части маршрута. 
	 * @return string
	 */
	protected function getUrl($path) 
	{
		return implode('/', $path);
	}
	
	protected function getBaseUrl() 
	{
		return $this->baseUrl;
	}
	
	protected function createPathInfo($manager, $params, $ampersand)
	{
		return empty($params) ? '' : ('?' . $manager->createPathInfo($params, '=', $ampersand));
	}
	

	/**
	 * 
	 * @param string $route
	 */
	protected function getByRoute($className, $route, $criteria=null)
	{
		if(!($criteria instanceof CDbCriteria)) {
			if(empty($criteria)) $criteria=[];
			$criteria=new CDbCriteria($criteria);
		}
		if($criteria->select == '*') {
			$criteria->select='id, alias';
		}
		return is_numeric($route)
			? $className::model()->findByPk($route, ['select'=>$criteria->select])
			: $this->findByAlias($className, $route, $criteria);
	}
	
	/**
	 * Find model by alias
	 * @param string $className model class
	 * @param string $alias alias
	 * @param string $select select criteria. Default "id, alias".
	 */
	protected function findByAlias($className, $alias, $criteria=null)
	{
		if(!($criteria instanceof CDbCriteria)) {
			$emptySelect=(empty($criteria) || (is_array($criteria) && empty($criteria['select'])));
			
			if(empty($criteria)) $criteria=[];
			$criteria=new CDbCriteria($criteria);
			if($emptySelect) {
				$criteria->select='id, alias';
			}
		}
		$criteria->addColumnCondition(['alias'=>$alias]);
		
		return $className::model()->find($criteria);
	}
	
	protected function cacheGet($key, $refresh=false)
	{
		if($refresh || !$this->cacheLoaded) { 
			$this->cacheData=\Yii::app()->cache->get($this->cacheVar);
			if(empty($this->cacheData)) {
				$this->cacheData=[];
			}
			$this->cacheLoaded=true;
		}		
		
		if(!empty($this->cacheData[$key])) { 
			return $this->cacheData[$key];
		}
		
		return false;
	}
	
	protected function cacheCategoryGet($id, $prefix='')
	{
		return $this->cacheGet($prefix.'c'.$id);
	}
	
	protected function cacheProductGet($id, $prefix='')
	{
		return $this->cacheGet($prefix.'p'.$id);
	}
	
	
	protected function cacheSet($key, $value)
	{
		$this->cacheData[$key]=$value;
		\Yii::app()->cache->set($this->cacheVar, $this->cacheData);
	
		return $value;
	}
	
	protected function cacheCategorySet($id, $value, $prefix='')
	{
		return $this->cacheSet($prefix.'c'.$id, $value);
	}
	
	protected function cacheProductSet($id, $value, $prefix='')
	{
		return $this->cacheSet($prefix.'p'.$id, $value);
	}
	
	protected function cacheRouteGet($pathInfo, $prefix='', $refresh=false)
	{
		$route=false;
		
		if($refresh || !$this->cacheRoutesLoaded) {
			$this->cacheRoutes=\Yii::app()->cache->get($this->cacheRoutesVar);
			if(empty($this->cacheRoutes)) {
				$this->cacheRoutes=[];
			} 
			$this->cacheRoutesLoaded=true;
		}
		
		$key=$prefix.'r'.sha1($pathInfo);
		if(!empty($this->cacheRoutes[$key])) {
			$data=$this->cacheRoutes[$key];
			if(!empty($data['params']) && is_array($data['params'])) {
				foreach($data['params'] as $k=>$v) $_GET[$k]=$v;
			}
			$route=A::get($data, 'route', false);
		}
		
		return $route;		
	}
	
	protected function cacheRouteSet($pathInfo, $route, $params=[], $prefix='')
	{
		$key=$prefix.'r'.sha1($pathInfo);
		$this->cacheRoutes[$key]=[
			'params'=>$params,
			'route'=>$route
		];
			
		\Yii::app()->cache->set($this->cacheRoutesVar, $this->cacheRoutes);
		
		return $route;
	}
	/**
	 * Initialize
	 */
	public function init()
	{
		if(empty($this->cache)) {
			$this->cache=\Yii::app()->cache;
		}
	
		$this->cacheData=$this->cache->get($this->cacheVar);
		if(empty($this->cacheData)) {
			$this->cacheData=[];
		}
	
		$this->cacheRoutes=$this->cache->get($this->cacheRoutesVar);
		if(empty($this->cacheRoutes)) {
			$this->cacheRoutes=[];
		}
	}
}
