<?php

class ProductCarouselWidget extends CWidget
{
	public $title=null;
	public $criteria=['condition'=>'in_carousel=1'];
	public $view='product_carousel';

	public function run()
	{
		if(!D::cms('shop_enable_carousel')) return false;
		
		$dataProvider=Product::model()
			->cardColumns()
			->visibled()
			->scopeSort('product_carousel')
			->getDataProvider($this->criteria, false);
		
		if(!$dataProvider->totalItemCount) return false;

		Yii::app()->clientScript->registerScriptFile('/js/slick.min.js');
		
		$this->render($this->view, compact('dataProvider'));
	}
}