<?php
use YiiHelper as Y;

class SaleController extends AdminController
{
	/**
	 * (non-PHPdoc)
	 * @see AdminController::filters()
	 */
	public function filters()
	{
		return CMap::mergeArray(parent::filters(), array(
			array('DModuleFilter', 'name'=>'sale'),
			'postOnly +delete, deletePreview, changeActive'
		));
	}
	
	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$this->pageTitle=\Yii::t('AdminModule.sale', 'title').' - '.$this->appName;
		$this->breadcrumbs=array((D::cms('sale_title') ?: \Yii::t('AdminModule.sale', 'title'))=>array('sale/index'));
		
		$dataProvider=Sale::model()->getDataProvider(
			array('select'=>'id,title,create_time,active'), 
			array('pageSize'=>20)
		);
		
		$this->render('index', compact('dataProvider'));
	}
	
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Sale;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Sale'])) {
			$model->attributes=$_POST['Sale'];
			if($model->save()) {
				if(isset($_POST['saveout'])) {
					Y::setFlash(Y::FLASH_SYSTEM_SUCCESS, Yii::t('AdminModule.sale', 'success.saleCreatedWithName', ['{name}'=>$model->title]));
					$this->redirect(['index']);
				}
				else {
					$this->redirect(['update', 'id'=>$model->id]);
				}
			} 
		}
		
		$t=Y::createT('AdminModule.sale');
		$this->pageTitle = $t('create.title').' - '.$this->appName;
		$this->breadcrumbs=array(
			(D::cms('sale_title') ?: $t('title'))=>array('sale/index'),
			$t('create.title')
		);

		$this->render('create', compact('model'));
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

		if(isset($_POST['Sale']))
		{
			$model->attributes=$_POST['Sale'];
			if($model->save()) {
				if(isset($_POST['saveout'])) {
					Y::setFlash(Y::FLASH_SYSTEM_SUCCESS, Yii::t('AdminModule.sale', 'success.saleUpdatedWithName', ['{name}'=>$model->title]));
					$this->redirect(['index']);
				}
				else {
					$this->refresh();
				}
			}
		}
		
		$t=Y::createT('AdminModule.sale');
		$this->pageTitle=$t('edit.title').' - '.$this->appName;
		$this->breadcrumbs=array(
			(D::cms('sale_title') ?: $t('title'))=>array('sale/index'),
			$t('edit.title').' - '.$model->title
		);
		
		$this->render('update',compact('model'));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();
	}
	
	/**
	 * Удаление картинки превью
	 * @param integer $id идентификатор модели
	 */
	public function actionDeletePreview($id)
	{
		$model=Sale::model();
		$model->id=$id;
		$model->imageBehavior->delete();
		\Yii::app()->end();
		die;
	}
	
	/**
	 * Сменить активность
	 * @param integer $id идентификатор модели
	 */
	public function actionChangeActive($id)
	{
		$ajax=new AjaxHelper();
		
		$model=Sale::model();
		$model->id=$id;
		$ajax->success=$model->activeBehavior->changeActive(true);
		$ajax->endFlush();
	}

	/**
	 * (non-PHPdoc)
	 * @see DController::loadModel()
	 */
	public function loadModel($id)
	{
		return parent::loadModel('Sale', $id);
	}

	/**
	 * Performs the AJAX validation.
	 * @param Sale $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		parent::performAjaxValidation($model, 'sale-form');
	}
}
