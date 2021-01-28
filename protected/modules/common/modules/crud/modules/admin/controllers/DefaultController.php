<?php
/**
 * Основной контроллер раздела администрирования модуля
 *
 */
namespace crud\modules\admin\controllers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HHook;
use common\components\helpers\HAjax;
use crud\modules\admin\components\BaseController;
use crud\components\helpers\HCrud;
use crud\components\helpers\HCrudForm;

class DefaultController extends BaseController
{
	/**
	 * (non-PHPDoc)
	 * @see BaseController::$viewPathPrefix;
	 */
	public $viewPathPrefix='crud.modules.admin.views.default.';
	
	/**
	 * (non-PHPdoc)
	 * @see \CController::actions()
	 */
	public function actions()
	{
		return A::m(parent::actions(), [
			'changeActive'=>[
				'class'=>'\common\ext\active\actions\AjaxChangeActive',
				'className'=>HCrud::param(Y::requestGet('cid'), 'class'),
				'behaviorName'=>Y::requestGet('b', 'activeBehavior')
			],				
			'removeImage'=>[
				'class'=>'\common\ext\file\actions\RemoveFileAction',
				'modelName'=>HCrud::param(Y::requestGet('cid'), 'class'),
				'behaviorName'=>Y::requestGet('b', 'imageBehavior'),
				'ajaxMode'=>true
			],
			'removeFile'=>[
                'class'=>'\common\ext\file\actions\RemoveFileAction',
                'modelName'=>HCrud::param(Y::requestGet('cid'), 'class'),
                'behaviorName'=>Y::requestGet('b', 'fileBehavior'),
                'ajaxMode'=>true
            ],
			'sortableSave'=>[
				'class'=>'\common\ext\sort\actions\SaveAction',
				'categories'=>[Y::requestGet('category')],
			],
		    'nestableSave'=>[
		        'class'=>'\common\ext\nestedset\actions\SaveAction',
		        'modelClass'=>HCrud::param(Y::requestGet('cid'), 'class'),
		        'nestedSetBehaviorName'=>HCrud::param(Y::requestGet('cid'), 'crud.index.nestedset.behavior', 'nestedSetBehavior'),
		        'attributeOrdering'=>true,
		        'paramData'=>Y::requestGet('paramdata', 'data')
		    ],
			'saveSortField'=>[
                'class'=>'\common\ext\sort\actions\AjaxSaveSortFieldAction',
                'className'=>HCrud::param(Y::requestGet('cid'), 'class'),
                'behaviorName'=>Y::requestGet('b', 'sortFieldBehavior'),
                'value'=>Y::requestGet('v'),
            ],
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \AdminController::filters()
	 */
	public function filters()
	{
	    return A::m(parent::filters(), [
	        'accessControl',
			'ajaxOnly +ajax, changeActive, removeImage, removeFile'	
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
	        $accessRules=HCrud::param($cid, 'access', []);	        
	    }
	    
	    return A::m($accessRules, parent::accessRules());
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
    	    $behaviors=HCrud::param($cid, 'crud.controllers');
    	    if(!empty($behaviors)) {
    	        $this->attachBehaviors($behaviors);
    	        $this->{'action'.$action}($cid);
    	    }
	    }
	    
	    Y::end();
	}

	/**
	 * Action: Запросы не по Ajax.
	 * @param string $cid индетификатор настроек CRUD для модели.
	 *
	 * В запросе обязан передаваться параметр "action", в котором
	 * содержится имя действия.
	 *
	 * В запросе может быть передан параметр "actionname", в котором
	 * содержится имя параметра с именем действия.
	 */
	public function actionAction()
	{
	    $cid=A::get($_REQUEST, 'cid');
	    $action=A::get($_REQUEST, A::get($_REQUEST, 'actionname', 'action'));
	    
	    if(!empty($cid) && !empty($action)) {
	        $behaviors=HCrud::param($cid, 'crud.acontrollers');
	        if(!empty($behaviors)) {
	            $this->attachBehaviors($behaviors);
	            $this->{'action'.$action}($cid);
	        }
	    }
	    
	    Y::end();
	}
	
	/**
	 * Action: Главная страница.
	 * @param string $cid индетификатор настроек CRUD для модели.
	 */
	public function actionIndex($cid)
	{	
	    if(HCrud::param($cid, 'crud.index.nestedset')) {
            return $this->actionIndexNestedset($cid);
	    }
	    
	    if($onBeforeLoad=HCrud::param($cid, 'crud.onBeforeLoad')) call_user_func($onBeforeLoad, $cid);
	    if($onBeforeLoad=HCrud::param($cid, 'crud.index.onBeforeLoad')) call_user_func($onBeforeLoad, $cid);		 
	    
		$model=HCrud::getById($cid, true, null, true, HCrud::param($cid, 'crud.index.scenario', 'view'));

		if($onBeforeModelLoad=HCrud::param($cid, 'crud.index.onBeforeModelLoad')) call_user_func_array($onBeforeModelLoad, [&$model]);
		
		$options=HCrud::param($cid, 'crud.index.gridView.dataProvider', []);
		$dataProviderOptions=A::m([
			'pagination'=>[
				'pageVar'=>'p',	
				'pageSize'=>Y::request()->getQuery('gridSize')?:30
			], 
			'sort'=>[
				'sortVar'=>'s', 
				'descTag'=>'d'
			]
		], $options);
		
		$sort=Y::requestGet(A::rget($dataProviderOptions, 'sort.sortVar'));
		if(Y::requestGet('usort')) {
			$sort='usort';
		}
		elseif(Y::requestGet('usortd')) {
			$sort='usortd';
		}
		elseif(!$sort) {
			$sort='usort'; 
		}
		
		if((($sort == 'usort') || ($sort == 'usortd'))
			&& ($sortable=HCrud::param($cid, 'crud.index.gridView.sortable')) 
			&& ($sortableCategory=A::get($sortable, 'category'))) 
		{
			$model=$model->scopeSort($sortableCategory, A::get($sortable, 'key'), ((int)Y::requestGet('usortd')===1));
		}
		$dataProvider=$model->getDataProvider($dataProviderOptions);
		
		if(Y::isAjaxRequest()) {
			$this->renderPartial($this->viewPathPrefix.'_gridview', compact('cid', 'dataProvider'), false, true);
		}
		else {
			$t=Y::ct('\crud\modules\admin\AdminModule.controllers/default');
			$title=HCrud::param($cid, 'crud.index.title', $t('page.index.title'));
			$onBeforeSetTitle=HCrud::param($cid, 'crud.index.onBeforeSetTitle');
			if($onBeforeSetTitle && is_callable($onBeforeSetTitle)) $title=call_user_func($onBeforeSetTitle, $model);
			$this->setPageTitle(HCrud::param($cid, 'crud.index.titleBreadcrumb', $title));
			$this->breadcrumbs=HCrud::param($cid, 'crud.index.breadcrumbs', HCrud::param($cid, 'crud.breadcrumbs', [])); 
			$this->breadcrumbs[]=HCrud::param($cid, 'crud.index.titleBreadcrumb', $title);
			
			$this->render($this->viewPathPrefix.'index', compact('cid', 'dataProvider'));
		}
	}
	
	/**
	 * Action: Создание модели.
	 * @param string $cid индетификатор настроек CRUD для модели.
	 */
	public function actionCreate($cid)
	{	
	    if($onBeforeLoad=HCrud::param($cid, 'crud.onBeforeLoad')) call_user_func($onBeforeLoad, $cid);
	    if($onBeforeLoad=HCrud::param($cid, 'crud.create.onBeforeLoad')) call_user_func($onBeforeLoad, $cid);
		
		$model=HCrud::getById($cid, null, null, true, HCrud::param($cid, 'crud.create.scenario', 'insert'));		
		$formView=$this->getFormView($cid);

		if($onBeforeModelLoad=HCrud::param($cid, 'crud.create.onBeforeModelLoad')) call_user_func_array($onBeforeModelLoad, [&$model]);
		
		$formProperties=HCrudForm::getFormProperties($cid, 'crud.create');
		$this->save($model, [], A::get($formProperties, 'id', 'crud-form'), [
			'afterSave'=>function() use ($cid, $model, $formProperties) {
				if($onAfterSave=HCrud::param($cid, 'crud.create.onAfterSave')) {
    			    HHook::hexec($onAfterSave, [$model]);
    			}
				$t=Y::ct('\crud\modules\admin\AdminModule.controllers/default');
				if(isset($_POST['saveout'])) {
					Y::setFlash(Y::FLASH_SYSTEM_SUCCESS, $t('success.created', ['{id}'=>$model->id]));
					$this->redirect(HCrud::getConfigUrl($cid, 'crud.index.url', '/crud/admin/default/index', ['cid'=>$cid], 'c'));
				}
				else {
					$this->redirect(HCrud::getConfigUrl($cid, 'crud.update.url', '/crud/admin/default/update', ['cid'=>$cid, 'id'=>$model->id], 'c'));
				}
			}]
		);
		
		$t=Y::ct('\crud\modules\admin\AdminModule.controllers/default');
		$title=HCrud::param($cid, 'crud.create.title', $t('page.create.title'));
		$onBeforeSetTitle=HCrud::param($cid, 'crud.create.onBeforeSetTitle');
		if($onBeforeSetTitle && is_callable($onBeforeSetTitle)) $title=call_user_func($onBeforeSetTitle, $model);
		$this->setPageTitle(HCrud::param($cid, 'crud.create.titleBreadcrumb', $title));
		
		$this->breadcrumbs=HCrud::param($cid, 'crud.create.breadcrumbs', HCrud::param($cid, 'crud.breadcrumbs', []));
		if($indexBreadcrumb=HCrud::param($cid, 'crud.index.titleBreadcrumb', HCrud::param($cid, 'crud.index.title'))) {
            $this->breadcrumbs[$indexBreadcrumb]=HCrud::getConfigUrl($cid, 'crud.index.url', '/crud/admin/default/index', ['cid'=>$cid], 'c');
		}
		$this->breadcrumbs[]=HCrud::param($cid, 'crud.create.titleBreadcrumb', $title);
		
		$this->render($this->viewPathPrefix.'create', compact('cid', 'model', 'formView'));
	}
	
	/**
	 * Action: Редатирование модели.
	 * @param string $cid индетификатор настроек CRUD для модели.
	 * @param integer $id индетификатор модели
	 */
	public function actionUpdate($cid, $id)
	{
	    if($onBeforeLoad=HCrud::param($cid, 'crud.onBeforeLoad')) call_user_func($onBeforeLoad, $cid);
	    if($onBeforeLoad=HCrud::param($cid, 'crud.update.onBeforeLoad')) call_user_func($onBeforeLoad, $cid);
		
		$model=HCrud::getById($cid, $id, null, true, HCrud::param($cid, 'crud.update.scenario', 'update'));
		$formView=$this->getFormView($cid);

		if($onBeforeModelLoad=HCrud::param($cid, 'crud.update.onBeforeModelLoad')) call_user_func_array($onBeforeModelLoad, [&$model]);
		
		$formProperties=HCrudForm::getFormProperties($cid, 'crud.update');
		$this->save($model, [], A::get($formProperties, 'id', 'crud-form'), [
			'afterSave'=>function() use ($cid, $model, $formProperties) {
				if($onAfterSave=HCrud::param($cid, 'crud.update.onAfterSave')) {
    			    HHook::hexec($onAfterSave, [$model]);
    			}
				$t=Y::ct('\crud\modules\admin\AdminModule.controllers/default');
				Y::setFlash(Y::FLASH_SYSTEM_SUCCESS, $t('success.updated', ['{id}'=>$model->id]));
				if(isset($_POST['saveout'])) {
					$this->redirect(HCrud::getConfigUrl($cid, 'crud.index.url', '/crud/admin/default/index', ['cid'=>$cid], 'c'));
				}
				else {
					$this->redirect(HCrud::getConfigUrl($cid, 'crud.update.url', '/crud/admin/default/update', ['cid'=>$cid, 'id'=>$model->id], 'c'));
				}
			}]
		);
		
		$t=Y::ct('\crud\modules\admin\AdminModule.controllers/default');
		$title=HCrud::param($cid, 'crud.update.title', $t('page.update.title'));
		$onBeforeSetTitle=HCrud::param($cid, 'crud.update.onBeforeSetTitle');
		if($onBeforeSetTitle && is_callable($onBeforeSetTitle)) $title=call_user_func($onBeforeSetTitle, $model);
		$this->setPageTitle(HCrud::param($cid, 'crud.update.titleBreadcrumb', $title));
		
		$this->breadcrumbs=HCrud::param($cid, 'crud.update.breadcrumbs', HCrud::param($cid, 'crud.breadcrumbs', []));
		if($indexBreadcrumb=HCrud::param($cid, 'crud.index.titleBreadcrumb', HCrud::param($cid, 'crud.index.title'))) {
            $this->breadcrumbs[$indexBreadcrumb]=HCrud::getConfigUrl($cid, 'crud.index.url', '/crud/admin/default/index', ['cid'=>$cid], 'c') ;
		}
		$this->breadcrumbs[]=HCrud::param($cid, 'crud.update.titleBreadcrumb', $title);
		
		$this->render($this->viewPathPrefix.'update', compact('cid', 'model', 'formView'));
	}
	
	/**
	 * Action: Удаление модели.
	 * @param string $cid индетификатор настроек CRUD для модели.
	 * @param integer $id индетификатор модели
	 */
	public function actionDelete($cid, $id)
	{
	    if($onBeforeLoad=HCrud::param($cid, 'crud.onBeforeLoad')) call_user_func($onBeforeLoad, $cid);
	    if($onBeforeLoad=HCrud::param($cid, 'crud.delete.onBeforeLoad')) call_user_func($onBeforeLoad, $cid);
		
		$model=HCrud::getById($cid, $id, null, true, HCrud::param($cid, 'crud.index.scenario', 'delete'));
		
		$model->delete();
		
		if(Y::request()->isAjaxRequest) {
			HAjax::end(true, ['id'=>$id]);
		}
		
		$this->redirect([HCrud::param($cid, 'crud.index.url', '/crud/admin/default/index'), 'cid'=>$cid]);
	}
	
	/**
	 * Action: Главная страница.
	 * @param string $cid индетификатор настроек CRUD для модели.
	 */
	public function actionIndexNestedset($cid)
	{
	    if($onBeforeLoad=HCrud::param($cid, 'crud.onBeforeLoad')) call_user_func($onBeforeLoad, $cid);
	    if($onBeforeLoad=HCrud::param($cid, 'crud.index.onBeforeLoad')) call_user_func($onBeforeLoad, $cid);
	    
	    $model=HCrud::getById($cid, true, null, true, HCrud::param($cid, 'crud.index.scenario', 'view'));
	    
	    $nestedSetConfig = HCrud::param($cid, 'crud.index.nestedset');
	    $nestedSetBehaviorName = A::get($nestedSetConfig, 'behavior', 'nestedSetBehavior');
	    
	    $options=HCrud::param($cid, 'crud.index.nestedset.dataProvider', []);
	    
	    $defaultOrder = implode(',', [
	        $model->$nestedSetBehaviorName->rootAttribute,
	        $model->$nestedSetBehaviorName->leftAttribute,
	        $model->$nestedSetBehaviorName->orderingAttribute
	    ]);
	    if(empty($options['criteria']['order'])) {
	        $options['criteria']['order'] = $defaultOrder;
	    }
	    if(empty($options['criteria']['select'])) {
	        $options['criteria']['select']='*';
	    }
	    $options['criteria']['select'] .= ',' . $model->$nestedSetBehaviorName->getCriteriaSelect();
	    
	    $dataProviderOptions=A::m([
	        'pagination'=>[
	            'pageVar'=>'p',
	            'pageSize'=>999999
	        ],
	        'sort'=>[
	            'sortVar'=>'s',
	            'descTag'=>'d',
	            'defaultOrder'=>$defaultOrder
	        ]
	    ], $options);
	    
	    $dataProvider=$model->getDataProvider($dataProviderOptions);	    
	    
	    $t=Y::ct('\crud\modules\admin\AdminModule.controllers/default');
	    $title=HCrud::param($cid, 'crud.index.title', $t('page.index.title'));
	    
	    $this->setPageTitle(HCrud::param($cid, 'crud.index.titleBreadcrumb', $title));
	    $this->breadcrumbs=HCrud::param($cid, 'crud.index.breadcrumbs', HCrud::param($cid, 'crud.breadcrumbs', []));
	    $this->breadcrumbs[]=HCrud::param($cid, 'crud.index.titleBreadcrumb', $title);
	    
	    $this->render($this->viewPathPrefix.'nestedset', compact('cid', 'model', 'dataProvider'));
	}
	
	/**
	 * Получить имя шаблона формы
	 * @param string $cid идентификатор конфигурации CRUD для модели.
	 * @throws string|\CHttpException
	 */
	protected function getFormView($cid)
	{
		if($tabsConfig=HCrud::param($cid, 'crud.tabs')) {
			return A::get($tabsConfig, 'view', $this->viewPathPrefix.'_tabs');
		}
		elseif($formConfig=HCrud::param($cid, 'crud.form')) {
			return A::get($formConfig, 'view', $this->viewPathPrefix.'_form');
		}
		else {
			throw new \CHttpException(404);
		}
	}
}
