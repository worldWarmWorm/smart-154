<?php
/**
 * Product list widget
 */
class ProductListWidget extends CWidget
{
    public $criteria;
    public $view='product_list';

    public function run()
    {
        $dataProvider = Product::model()->getDataProvider($this->criteria);

        if(!$dataProvider->totalItemCount) return false;
        
        $this->render('default', compact('dataProvider'));
    }
}
