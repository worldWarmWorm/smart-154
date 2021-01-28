<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 24.07.12
 * Time: 12:35
 * To change this template use File | Settings | File Templates.
 */
class RelatedProducts extends CWidget
{
    public $product;

    public function run()
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'category_id = ? AND id <> ?';
        $criteria->params = array($this->product->category_id, $this->product->id);
        $criteria->order = 'RAND()';
        $criteria->limit = 4;

        $products = Product::model()->findAll($criteria);

        if ($products)
            $this->render('default', compact('products'));
    }
}
