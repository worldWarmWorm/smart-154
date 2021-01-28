<?php

class AdminController extends DController
{
    public $layout  = 'column2';
    public $appName = 'Админ панель';
    private $skin_info = array();
    public $breadcrumbs;
    
    public function init()
    {
        parent::init();

		Yii::app()->clientScript->combineScriptFiles=false;
        Yii::app()->clientScript->combineCssFiles=false;

        $site_name = Yii::app()->settings->get('cms_settings', 'sitename');

        if ($site_name)
            Yii::app()->name = $site_name;

        $this->skin_info = require(Yii::getPathOfAlias('admin.skin_info').'.php');

        // Set Error hendler for module
        if(!YII_DEBUG) {
	        Yii::app()->errorHandler->errorAction = 'admin/default/error';
        }
    }

	public function filters()
    {
        return [
            'accessControl',
        ];
    }

    public function accessRules()
    {
        return [
            ['allow', 'users'=>['?'], 'actions'=>['login', 'extupdate']],
            ['allow', 'users'=>['@'], 'roles'=>['admin', 'sadmin']],
            ['deny', 'users'=>['*']]
        ];
    }

	public function actionError()
	{
        $this->layout = 'column2';
        
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
	        	$this->render('error', $error);
	    }
	}

    public function getSkin()
    {
        return isset(Yii::app()->params['skin']) ? Yii::app()->params['skin'] : 'dishman';
    }

    public function skinParam($name)
    {
        $skin = $this->getSkin();

        if (isset($this->skin_info[$skin][$name])) {
            return $this->skin_info[$skin][$name];
        }

        return false;
    }
    
    public function setPageTitle($title)
    {
		$this->pageTitle=preg_replace('/&[^;]+;/', '', $title . ' - ' . $this->appName);
    }
}
