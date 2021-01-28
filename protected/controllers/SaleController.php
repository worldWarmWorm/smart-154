<?php
/**
 * Контроллер модуля Акций 
 */
class SaleController extends Controller
{
	/**
	 * (non-PHPdoc)
	 * @see AdminController::filters()
	 */
	public function filters()
	{
		return CMap::mergeArray(parent::filters(), array(
			array('DModuleFilter', 'name'=>'sale'),
		));
	}
	
	/**
	 * Просмотр акции
	 * @param integer $id id модели.
	 */
	public function actionView($id)
	{
		$model=$this->loadModel('Sale', $id, true, Sale::model()->detailColumns()->actived()->getDbCriteria());
		
		$this->seoTags($model);
		ContentDecorator::decorate($model);
		
		$this->breadcrumbs->add($this->getHomeTitle(), '/sale');
		$this->breadcrumbs->add($model->title);
		
		$this->render('view', compact('model'));
	}
	
	/**
	 * Список акций
	 */
	public function actionList()
	{
		$this->seoTags(array(
			'meta_h1'=>D::cms('sale_meta_h1', $this->getHomeTitle()),
			'meta_title'=>D::cms('sale_meta_title', $this->getHomeTitle()),
			'meta_key'=>D::cms('sale_meta_key'),
			'meta_desc'=>D::cms('sale_meta_desc')
		));
		$this->breadcrumbs->add($this->getHomeTitle());
		
		$dataProvider=\Sale::model()
			->previewColumns()
			->actived()
			->getDataProvider(['order' => '`t`.`id` DESC'], array('pageVar'=>'p'));
		
		$this->render('list', compact('dataProvider'));	
	}
	
	/**
	 * Получить основной заголовок
	 * @return string
	 */
	public function getHomeTitle()
	{
		return D::cms('sale_title', Yii::t('sale', 'sale_title'));
	}
}
