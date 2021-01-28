<?php
use YiiHelper as Y;

class PageController extends AdminController
{
	public function actionIndex()
	{
        $pages = Page::model()->findAll();

        $this->render('index', compact('pages'));
	}

	public function actionCreate($blog_id = 0)
	{
		$model = new Page;

        //$this->performAjaxValidation($model);

		if(isset($_POST['Page']))
		{
			$model->attributes = $_POST['Page'];

			if ($model->save()) {
                if ($model->blog_id)
                    $this->redirect(array('blog/index', 'id'=>$model->blog_id));
                else {
                	if(isset($_POST['saveout'])) {
                		Y::setFlash(Y::FLASH_SYSTEM_SUCCESS, Yii::t('AdminModule.page', 'success.pageCreatedWithName', ['{name}'=>$model->title]));
                		$this->redirect(['index']);
                	}
                	else {
                		$this->redirect(['update', 'id'=>$model->id]);
                	}
                }
            }
		}

        $model->blog_id = $blog_id;

		$this->render('create',array('model'=>$model));
	}


	public function actionUpdate($id)
	{
        $model = $this->loadModel($id);

        //$this->performAjaxValidation($model);

		if (isset($_POST['Page']))
		{
			$model->attributes = $_POST['Page'];

			if ($model->save()) {
                if ($model->blog_id)
                    $this->redirect(array('blog/index', 'id'=>$model->blog_id));
                else {
                	if(isset($_POST['saveout'])) {
                		Y::setFlash(Y::FLASH_SYSTEM_SUCCESS, Yii::t('AdminModule.page', 'success.pageUpdatedWithName', ['{name}'=>$model->title]));
                		$this->redirect(['index']);
                	}
                	else {
                		$this->refresh();
                	}
                }
                    
            }
		}

		$this->render('update', compact('model'));
	}

    public function actionDelete($id)
    {
        if ($id == 1) {
            throw new CHttpException('500', 'Вы не можете удалить главную страницу!');
        }

        $model = $this->loadModel($id);
        $model->delete();
        
        if(\Yii::app()->request->isAjaxRequest) {
        	\Yii::app()->end();
        	die;
        }

        $this->redirect(array('deleteok'));
    }

    public function actionDeleteOk()
    {
        $this->render('delete_ok');
    }

    /**
     * @param $id
     * @return Page
     * @throws CHttpException
     */
	public function loadModel($id)
	{
		$model = Page::model()->findByPk((int)$id);
		if ($model === null)
			throw new CHttpException(404, 'Страница не найдена');
		return $model;
	}

    /**
   	 * Performs the AJAX validation.
   	 * @param CModel the model to be validated
   	 */
   	protected function performAjaxValidation($model)
   	{
   		if(isset($_POST['ajax']) && $_POST['ajax']==='page-form')
   		{
   			echo CActiveForm::validate($model);
   			Yii::app()->end();
   		}
   	}
}
