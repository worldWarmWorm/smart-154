<?php
/**
 * DOrder module frontend controller.
 * 
 * @version 1.0
 */
class DOrderController extends Controller
{
	/**
	 * (non-PHPdoc)
	 * @see CController::filters()
	 */
	public function filters()
	{
		return CMap::mergeArray(parent::filters(), array(
			array('DModuleFilter', 'name'=>'shop')
		));
	}
	
	public static function allowAttributes()
	{
		return array(
			'hideCartModalWidget'=>true
		);
	}

	/**
	 * Главная страница модуля заказов
	 */
	public function actionIndex() 
	{
		if(D::cms('tinymce_adaptivy')){
			$this->layout = 'clean';
		}
		$this->actionOrder();
	}
	
	/**
	 * Страница оформления заказа
	 */
	public function actionOrder()
	{
		$this->prepareSeo('Оформление заказа');
		
		if(D::cms('tinymce_adaptivy')) {
			$this->breadcrumbs->add('Корзина', '/cart');
		}
		$this->breadcrumbs->add('Оформление заказа');
		
		$this->render('order');
	}
}
