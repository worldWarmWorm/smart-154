<?php
use YiiHelper as Y;

class EventController extends AdminController
{
	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
        $eventDataProvider = Event::model()->getDataProvider(['order'=>'created DESC']);
		$this->render('index', compact('eventDataProvider'));
	}


	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model = new Event;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if (isset($_POST['Event'])) {
			$model->attributes=$_POST['Event'];
            // $model->created = new CDbExpression('NOW()');
            $model->created = date('Y-m-d');

			if ($model->save()) {
				if(isset($_POST['saveout'])) {
					Y::setFlash(Y::FLASH_SYSTEM_SUCCESS, Yii::t('AdminModule.event', 'success.eventCreatedWithName', ['{name}'=>$model->title]));
					$this->redirect(['index']);
				}
				else {
					$this->redirect(['update', 'id'=>$model->id]);
				}
			}
		}

		$this->render('create', compact('model'));
	}

	public function actionKillImage(){
		$id = Yii::app()->request->getQuery('model_id');
		$id = (int)($id);
		$model = Event::model()->findByPk($id);
		if(count($model)){
			$path=Yii::getPathOfAlias('webroot').'/images/event/'.$model->preview;
			if(is_file($path)){
				unlink($path);
			}
			$model->preview = '';
			$model->enable_preview = 0; 
			$model->save();
			echo "Успешно удалено";
		}
	}
	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if (isset($_POST['Event'])) {
			$model->attributes=$_POST['Event'];

			    if($model->validate()){

					$model->files = CUploadedFile::getInstance($model,'files');
					if(!empty($model->files)){
						$ext = $model->files->getExtensionName();
						//Получаем число, для имени картинки
						$rand = substr(md5(microtime()),rand(0,26),12);

						//Формируем новое имя картинки.
						$model->preview = $rand.'.'.$ext;
					    $path=Yii::getPathOfAlias('webroot').'/images/event/'.$model->preview;

						//Сохраняем картинку (оригинал).
						$model->files->saveAs( $path );
						//Посылаем картинку на обрезку
						$img = Yii::app()->image->load($path);
						$masterSize = $img->width > $img->height ? Image::HEIGHT : Image::WIDTH;
						// $img->resize(321, 4095, 2);
						#$img->crop(320, 4096, 2);

						//После манипуляций сохраняем превьюху
						$img->save($path);	
					}

			    	$model->save();

			    	if(isset($_POST['saveout'])) {
			    		Y::setFlash(Y::FLASH_SYSTEM_SUCCESS, Yii::t('AdminModule.event', 'success.eventUpdatedWithName', ['{name}'=>$model->title]));
			    		$this->redirect(['index']);
			    	}
			    	else {
			    		$this->refresh();
			    	}
			    }
		}
		

		$this->render('update', compact('model'));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if (Yii::app()->request->isPostRequest) {
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_POST['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		} else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}


	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model = Event::model()->findByPk((int)$id);
		if ($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	public function getEventHomeTitle()
	{
		return D::cms('events_title') ?: \Yii::t('AdminModule.event', 'title');
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='event-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
