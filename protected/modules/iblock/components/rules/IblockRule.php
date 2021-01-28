<?php
/**
 * Правило маршрутизации для информационный блоков
 * 
 * Подключение
 * /config/urls.php 
 * ['class'=>'\iblock\components\rules\IblockRule'],
 * /config/defaults.php
 * 'aliases'=>[..., 'iblock'=>'application.modules.iblock']
 */
namespace iblock\components\rules;

use iblock\models\InfoBlock;
use iblock\models\InfoBlockProp;
use iblock\models\InfoBlockElementProp;
use iblock\components\InfoBlockHelper;
use common\components\helpers\HDb;

class IblockRule extends \CBaseUrlRule
{
	/**
	 * @var string базовая ссылка на страницу инфоблока
	 */
	public $iblockBaseUrl='infoblock/index';
	
	/**
	 * @var string базовая ссылка на страницу элемента инфоблока
	 */
	public $elementBaseUrl='infoblock/view';
	
	/**
	 * (non-PHPdoc)
	 * @see CBaseUrlRule::createUrl()
	 */
	public function createUrl($manager, $route, $params, $ampersand)
	{
		$url=false;
		
		if(isset($params['id']) && ($route == $this->iblockBaseUrl)) {
			if($model=InfoBlock::model()->findByPk($params['id'])) {
				if($model->code) {
					$url=$model->code;
				}
			}
		}
		elseif(isset($params['id']) && ($route == $this->elementBaseUrl)) {
			if($element=InfoBlockHelper::getElementByPk($params['id'])) {
				if($iblock=InfoBlock::model()->findByPk($element['info_block_id'])) {
					if($iblock->code) {
						$url=$iblock->code . '/';
						if(isset($element['properties']['alias']) && $element['properties']['alias']) {
							$url.=$element['properties']['alias'];
						}
						else {
							$url.=$element['id'];
						}
					}
				}
			}
		}
		
		if($url) {
			unset($params['id']);
			if(!empty($params)) {
				$url.='?' . $manager->createPathInfo($params, '=', $ampersand);
			}
		}
		
		return $url;
	}

	/**
	 * (non-PHPdoc)
	 * @see CBaseUrlRule::parseUrl()
	 */
	public function parseUrl($manager, $request, $pathInfo, $rawPathInfo)
	{
		if(!$pathInfo) return false;

		$routes=explode('/', trim(preg_replace('#/+#', '/', $pathInfo), '/'));
		
		$isView=false;
		$iblockCode=false;
		if(is_array($routes) && (count($routes) == 1)) {
			$iblockCode=$routes[0];
		}
		elseif(is_array($routes) && (count($routes) == 2)) {
			$iblockCode=$routes[0];
			$isView=true;
		}
		
		if(!$iblockCode) {
			return false;
		}
		
		if($model=InfoBlock::model()->find('code=:code', [':code'=>$iblockCode])) {
			if($isView) {
				if(is_numeric($routes[1])) {
					$elementId=$routes[1];
				}
				else {
					$query='SELECT `t1`.`element_id` FROM `info_block_element_prop` AS `t1`
					INNER JOIN `info_block_prop` AS `t2` ON(`t1`.`prop_id`=`t2`.`id` AND `t2`.`code`=\'alias\')
					WHERE `t1`.`value` LIKE :alias';
					$elementId=(int)HDb::queryScalar($query, [':alias'=>$routes[1]]);
				}
				
				if($elementId) {
					$_GET['id']=$elementId;
					return $this->elementBaseUrl;
				}
			}
			else {
				$_GET['id']=$model->id;
				return $this->iblockBaseUrl;
			}
		}
		
		return false;
	}
}
