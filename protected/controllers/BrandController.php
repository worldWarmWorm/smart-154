<?php
/**
 * Контроллер Брендов
 */
class BrandController extends Controller
{
	/**
	 * (non-PHPdoc)
	 * @see AdminController::filters()
	 */
	public function filters()
	{
		return CMap::mergeArray(parent::filters(), array(
			array('DModuleFilter', 'name'=>'shop'),
		));
	}
	
	/**
	 * Список брендов
	 */
	public function actionIndex()
	{
		$this->prepareSeo(Yii::t('brand', 'title'));
		$this->seoTags(array(
			'meta_h1'=>BrandSettings::model()->meta_h1,
			'meta_title'=>BrandSettings::model()->meta_title,
			'meta_key'=>BrandSettings::model()->meta_key,
			'meta_desc'=>BrandSettings::model()->meta_desc
		)); 

		ContentDecorator::decorate(BrandSettings::model(), 'index_page_content');

		$this->breadcrumbs->add($this->getHomeTitle());
		
		$dataProvider=\Brand::model()->previewColumns()->getDataProvider(null, ['pageVar'=>'p', 'pageSize'=>999999]);

		$this->render('index', compact('dataProvider'));	
	}
	
	/**
	 * Просмотр бренда
	 * @param integer $alias ЧПУ бренда
	 */
	public function actionView($alias)
	{
		$brand=Brand::model()->findByAttributes(['alias'=>$alias]);
		if(!$brand) 
			throw new CHttpException('404');

		$categories=Category::model()->findAll([
			'scopes'=>['byBrandId'=>$brand->id],
			'select'=>'`t`.`id`, `t`.`title`, `t`.`root`, `t`.`lft`, `t`.`rgt`, `t`.`level`',
			'order'=>'`t`.`root`, `t`.`lft`'
		]);
		$productDataProvider=Product::model()->cardColumns()->visibled()
			->getDataProvider(['condition'=>'brand_id=:brandId', 'params'=>[':brandId'=>$brand->id]], ['pageVar'=>'p']);

		$this->seoTags($brand);
		ContentDecorator::decorate($brand, 'detail_text');
		
		$this->breadcrumbs->add($this->getHomeTitle(), '/brands');
		$this->breadcrumbs->add($brand->title);
		
		$this->render('view', compact('brand', 'categories', 'productDataProvider'));
	}	
	
	/**
	 * Получить основной заголовок
	 * @return string
	 */
	public function getHomeTitle()
	{
		return Yii::t('brand', 'title');
	}
}