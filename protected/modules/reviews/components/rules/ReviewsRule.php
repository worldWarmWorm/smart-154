<?
namespace reviews\components\rules;

class ReviewsRule extends \CBaseUrlRule
{
	/**
	 * (non-PHPdoc)
	 * @see CBaseUrlRule::createUrl()
	 */
	public function createUrl($manager, $route, $params, $ampersand)
	{
		if(!\Yii::app()->d->isActive('reviews'))
			return false;

		$url=false;

		if($route == 'reviews/default/index') {
			$url='reviews';
			if(!empty($params)) {
				$url.='?' . $manager->createPathInfo($params, '=', $ampersand);
			}
			return $url;
		}
		elseif(($route == 'reviews/default/view') && !empty($params['id'])) {
			if($review=\reviews\models\Review::model()->findByPk($params['id'], ['select'=>'id, alias'])) {
				if(empty($review->alias)) {
					$url='review/'.$params['id'];
					unset($params['id']);
				}
				else {
					$url=$review->alias;
				}
			}
		}
		if(!empty($url) && !empty($params)) {
        	$url.='?' . $manager->createPathInfo($params, '=', $ampersand);
        }

		return $url;
	}

	/**
 	 * (non-PHPdoc)
	 * @see CBaseUrlRule::parseUrl()
	 */
	public function parseUrl($manager, $request, $pathInfo, $rawPathInfo)
	{
		if($pathInfo == 'reviews/index') {
			return 'reviews/default/index';
		}
		elseif($review=\reviews\models\Review::model()->findByAttributes(['alias'=>$pathInfo], ['select'=>'id'])) {
			$_GET['id']=$review['id'];
			return 'reviews/default/view';
		}

		return false;
	}
}
