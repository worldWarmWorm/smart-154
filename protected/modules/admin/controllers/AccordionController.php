<?php

class AccordionController extends AdminController
{

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Accordion;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Accordion']))
		{
			$model->attributes=$_POST['Accordion'];
			if($model->save()){
				$this->redirect(array('accordion/update', 'id'=>$model->id));
			}
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	public function actionAddItem( $id ) {

		$title = Yii::app()->request->getQuery('title');
		$model = new AccordionItems;
		$model->accordion_id = intval($id);
		$model->save();

		Yii::app()->clientScript->registerCoreScript('jquery.ui');
		
		$editorId=uniqid('id');
		$this->renderPartial('_item_part', compact('model','editorId'), false, true);
	}

	public function actionDeleteItem( $id ) {

		$model = AccordionItems::model()->findByPk((int)$id);
		if($model->delete()){
			echo 1;
		}

	}


	public function actionUpdateItem($id) {
		$model = AccordionItems::model()->findByPk((int)$id);

		$title = Yii::app()->request->getPost('title');
		$order = Yii::app()->request->getPost('order');
		$description  = Yii::app()->request->getPost('description');

		$model->title = $title;
		$model->accordion_order = $order;
		$model->description  = $description;
		if($model->save()){
			echo 1;	
		} else {
			echo 0;
		}

	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{

		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Accordion']))
		{
			$model->attributes=$_POST['Accordion'];
			if($model->save() && !\Yii::app()->request->isAjaxRequest) {
				$this->redirect(array('index'));
			}
		}
		
		if(\Yii::app()->request->isAjaxRequest) {
			\Yii::app()->end();
			die;
		}	

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$model=new Accordion('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Accordion']))
			$model->attributes=$_GET['Accordion'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	public function loadModel($id)
	{
		$model=Accordion::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Accordion $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='accordion-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
