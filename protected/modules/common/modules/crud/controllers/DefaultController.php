<?php
/**
 * Контроллер
 *
 */
namespace crud\controllers;

use common\components\helpers\HArray as A;
use common\components\helpers\HYii as Y;
use common\components\helpers\HAjax;
use crud\components\BaseController;
use crud\components\helpers\HCrud;
use crud\components\helpers\HCrudPublic;
use seo\components\helpers\HSeo;

class DefaultController extends BaseController
{
	/**
	 * (non-PHPDoc)
	 * @see BaseController::$viewPathPrefix;
	 */
	public $viewPathPrefix='crud.views.default.';
	
	/**
	 * (non-PHPdoc)
	 * @see \CController::filters()
	 */
	public function filters()
	{
		return A::m(parent::filters(), [
		    'accessControl',
		    'ajaxOnly +ajax'
		]);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \AdminController::accessRules()
	 */
	public function accessRules()
	{
	    $accessRules=[];
	    
	    if($cid=A::get($_REQUEST, 'cid')) {
	        $accessRules=HCrud::param($cid, 'public.access', []);
	    }
	    
	    return A::m($accessRules, parent::accessRules(), [['deny', 'users'=>['*']]]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CController::actions()
	 */
	public function actions()
	{
		return A::m(parent::actions(), [
		]);
	}
	
	/**
	 * Action: Запросы по Ajax.
	 * @param string $cid индетификатор настроек CRUD для модели.
	 *
	 * В запросе обязан передаваться параметр "action", в котором
	 * содержится имя действия.
	 *
	 * В запросе может быть передан параметр "actionname", в котором
	 * содержится имя параметра с именем действия.
	 */
	public function actionAjax()
	{
	    $cid=A::get($_REQUEST, 'cid');
	    $action=A::get($_REQUEST, A::get($_REQUEST, 'actionname', 'action'));
	    
	    if(!empty($cid) && !empty($action)) {
	        $behaviors=HCrud::param($cid, 'public.controllers');
	        if(!empty($behaviors)) {
	            $this->attachBehaviors($behaviors);
	            $this->{'action'.$action}($cid);
	        }
	    }
	    
	    Y::end();
	}
	
	/**
	 * Action: Главная страница
	 */
	public function actionIndex()
	{
	    $cid=$this->getCidByRequest();
		if($onBeforeLoad=HCrud::param($cid, 'public.index.onBeforeLoad')) $onBeforeLoad();
			
		$model=HCrud::getById($cid, true);
		
		$options=HCrud::param($cid, 'public.index.listView.dataProvider', []);
		$dataProvider=$model->getDataProvider(A::m([
			'pagination'=>[
				'pageVar'=>'p',
				'pageSize'=>Y::request()->getQuery('sz')?:30
			],
			'sort'=>[
				'sortVar'=>'s',
				'descTag'=>'d'
			]
		], $options));

		if($layout=HCrud::param($cid, 'public.index.layout', HCrud::param($cid, 'public.layout'))) {
		    $this->layout=$layout;
		}		
		
		if(Y::isAjaxRequest()) {
		    $this->renderPartial(HCrud::param($cid, 'public.index.viewlist', $this->viewPathPrefix.'_listview'), compact('cid', 'dataProvider'), false, true);
		}
		else {
			$t=Y::ct('\CrudModule.controllers/default');
			$title=HCrud::param($cid, 'public.index.title', $t('page.index.title'));
				
			$this->setPageTitle(HCrud::param($cid, 'public.index.titleBreadcrumb', $title));
			$breadcrumbs=HCrud::param($cid, 'public.index.breadcrumbs', HCrud::param($cid, 'public.breadcrumbs', []));
			$breadcrumbs[]=HCrud::param($cid, 'public.index.titleBreadcrumb', $title);
			$this->setBreadcrumbs($breadcrumbs);
				
			$this->render(HCrud::param($cid, 'public.index.view', $this->viewPathPrefix.'index'), compact('cid', 'dataProvider'));
		}
	}
	
	/**
	 * Action: Детальная страница
	 * @param integer $id индетификатор модели
	 */
	public function actionView()
	{
	    $cid=$this->getCidByRequest();
	    $id=$this->getIdByRequest();
		if($onBeforeLoad=HCrud::param($cid, 'public.view.onBeforeLoad')) $onBeforeLoad();
		
		$model=HCrud::getById($cid, $id);
		
		$attributeTitle=HCrud::param($cid, 'public.view.attributeTitle', 'title');
	    $this->setPageTitle($model->$attributeTitle);
		
	    $breadcrumbs=HCrud::param($cid, 'public.index.breadcrumbs', HCrud::param($cid, 'public.breadcrumbs', []));
	    if($indexTitle=HCrud::param($cid, 'public.index.titleBreadcrumb', HCrud::param($cid, 'public.index.title'))) {
	        if($indexUrl=HCrudPublic::getIndexUrl($cid)) {
	            $breadcrumbs[$indexTitle]=$indexUrl;
	        }
	        else {
	            $breadcrumbs[]=$indexTitle;
	        }
	    }
	    $breadcrumbs[]=$model->$attributeTitle;
		$this->setBreadcrumbs($breadcrumbs);
	    
		if($seoBehavior=$model->asa('seoBehavior')) {
		    HSeo::seo($model);
		    HSeo::publish([
		        'title'=>true,
		        'keywords'=>true,
		        'desc'=>true,
		        'robots'=>false,
		        'charset'=>false,
		        'canonical'=>false,
		        'noskype'=>false
		    ]);
		}

		if($layout=HCrud::param($cid, 'public.view.layout', HCrud::param($cid, 'public.layout'))) {
		    $this->layout=$layout;
		}
		
		$this->render(HCrud::param($cid, 'public.view.view', $this->viewPathPrefix.'view'), compact('model'));
	}
	
	/**
	 * Action: Получить основной заголовок
	 * @return string
	 */
	public function getHomeTitle()
	{
		return \Yii::t('CrudModule.controllers/default', 'title');
	}

	/**
	 * Получение идентификатора конфигурации из запроса
	 * @param boolean $throw бросать исключение HTTP 404
	 * @throws \CHttpException
	 * @return string
	 */
	protected function getCidByRequest($throw=true)
	{
	    $cid=A::get($_REQUEST, 'cid');
	    if(!$cid && $throw) {
	        throw new \CHttpException(404);
	    }
	    return $cid;
	}
	
	/**
	 * Получение идентификатора модели из запроса
	 * @param boolean $throw бросать исключение HTTP 404
	 * @throws \CHttpException
	 * @return string
	 */
	protected function getIdByRequest($throw=true)
	{
	    $id=A::get($_REQUEST, 'id');
	    if(!$id && $throw) {
	        throw new \CHttpException(404);
	    }
	    return $id;
	}
}
