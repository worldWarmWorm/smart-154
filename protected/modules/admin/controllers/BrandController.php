<?php
use YiiHelper as Y;

class BrandController extends AdminController
{
	/**
	 * (non-PHPdoc)
	 * @see AdminController::filters()
	 */
	public function filters()
	{
		return CMap::mergeArray(parent::filters(), array(
			array('DModuleFilter', 'name'=>'shop'),
			'ajaxOnly +delete, deletePreview, changeActive'
		));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$this->breadcrumbs=$this->getBaseBreadcrumbs();

		$dataProvider=Brand::model()->getDataProvider(null, ['pageSize'=>999999]);

		$this->render('index',compact('dataProvider'));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Brand;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Brand'])) {
			$model->attributes=$_POST['Brand'];
			if($model->save()) {
				if(isset($_POST['saveout'])) {
					Y::setFlash(Y::FLASH_SYSTEM_SUCCESS, Yii::t('AdminModule.brand', 'success.created', ['{name}'=>$model->title]));
					$this->redirect(['index']);
				}
				else {
					$this->redirect(['update', 'id'=>$model->id]);
				}
			} 
		}
		
		$t=Y::createT('AdminModule.brand');
		$this->pageTitle = $t('create.title').' - '.$this->appName;
		$this->breadcrumbs=$this->getBaseBreadcrumbs();
		$this->breadcrumbs[]=$t('create.title');

		$this->render('create',compact('model'));
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

		if(isset($_POST['Brand']))
		{
			$model->attributes=$_POST['Brand'];
			if($model->save()) {
				if(isset($_POST['saveout'])) {
					Y::setFlash(Y::FLASH_SYSTEM_SUCCESS, Yii::t('AdminModule.brand', 'success.updated', ['{name}'=>$model->title]));
					$this->redirect(['index']);
				}
				else {
					$this->refresh();
				}
			}
		}
		
		$t=Y::createT('AdminModule.brand');
		$this->pageTitle=$t('edit.title');
		$this->breadcrumbs=$this->getBaseBreadcrumbs();
		$this->breadcrumbs[]=$t('edit.title').' - '.$model->title;
				
		$this->render('update',compact('model'));
	}

	/**
	 * Сменить активность
	 * @param integer $id идентификатор модели
	 */
	public function actionChangeActive($id)
	{
		$ajax=new AjaxHelper();
		
		$model=Brand::model();
		$model->id=$id;
		$ajax->success=$model->activeBehavior->changeActive(true);
		$ajax->endFlush();
	}

	/**
	 * Страница настроек.
	 */
	public function actionSettings()
	{
		$model=new BrandSettings;
		
		$modelName=\CHtml::modelName($model);
		if (isset($_POST[$modelName])) {
			$model->attributes = $_POST[$modelName];
		
			if ($model->validate()) {
				$model->saveSettings();
				if(isset($_POST['saveout'])) {
					Y::setFlash(Y::FLASH_SYSTEM_SUCCESS, Yii::t('AdminModule.brand', 'success.settings.updated'));
					$this->redirect(['index']);
				}
				else {
					$this->refresh();
				}
			}
		}
		
		$this->setPageTitle('Настройки');
		
		$this->breadcrumbs=$this->getBaseBreadcrumbs();
		$this->breadcrumbs['Настройки']=['settings'];
		
		$this->render('settings', compact('model'));
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
		$model=Brand::model();
		$model->id=$id;
		$model->imageBehavior->delete();
		\Yii::app()->end();
		die;
	}

	/**
	 * (non-PHPdoc)
	 * @see DController::loadModel()
	 */
	public function loadModel($id)
	{
		return parent::loadModel('Brand', $id);
	}

	protected function getBaseBreadcrumbs()
	{
		return [
			D::cms('shop_title', 'Каталог') => ['shop/index'],
			\Yii::t('AdminModule.brand', 'title') => ['index']
	   	];
	}
}
