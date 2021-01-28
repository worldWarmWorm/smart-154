<?php

/**
 * 
 */
class MenuController extends DevadminController
{
	public $layout = "column2";

	public function filters()
	{
		return CMap::mergeArray(parent::filters(), array(
			'ajaxOnly +saveSeoATitle'
		));
	}

	public function actionIndex()
	{
		$this->render('index', compact('model'));	
	}

    public function actionEdit()
    {
    	$model = Devmenu::model()->findAll();
   		$this->render('edit', compact('model'));
    }

    public function actionChangeName($id, $newname)
    {
    	$model = Devmenu::model()->findByPk($id);	
    	$model->title = htmlspecialchars($newname);
    	$model->save();
    }

    public function actionToggleHidden($id)
    {
    	$this->layout=false;
    	$model = Devmenu::model()->findByPk($id);
    	$model->hidden = $model->hidden ? 0 : 1;
    	$model->save();
    	echo $model->hidden;
    	Yii::app()->end();
    	die();
    }

	public function actionToggleDefault($id)
    {
    	Devmenu::model()->updateAll(array('default' => 0), '`default` = 1');
    	Devmenu::model()->updateByPk($id, array('default' => 1));
    	echo 1;
    	Yii::app()->end();
    	die();
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

	public function actionSaveSeoATitle()
	{
		$model = Devmenu::model()->findByPk($_REQUEST['id']);
		if($model) {
			$model->scenario='update_seo_a_title';
			$model->seo_a_title=$_REQUEST['seo_a_title'];
			if(!$model->save()) {
				foreach($model->getErrors() as $attribute=>$errors) 
					echo array_reduce($errors, function($v, $v2) { return "{$v}\n{$v2}"; });
			} 
		}
		Yii::app()->end();
		die;
	}
}
