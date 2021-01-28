<?php

class SearchController extends AdminController
{
    /**
	 * (non-PHPdoc)
	 * @see AdminController::filters()
	 */
	public function filters()
	{
		return CMap::mergeArray(parent::filters(), array(
			['DModuleFilter', 'name'=>'shop'],
		));
	}
    
    public function actionAutoComplete() 
    {
        if (isset($_GET['q']) && (mb_strlen($query, 'UTF-8') < 2)) {
            $query = Yii::app()->request->getQuery('q');
            $criteria = new CDbCriteria();
            $criteria->select='id,alias,code,title';
            $criteria->addSearchCondition('code', $query, true, 'OR');
			$criteria->addSearchCondition('title', $query, true, 'OR');
			$criteria->addSearchCondition('alias', $query, true, 'OR');
			$criteria->addSearchCondition('id', $query, true, 'OR');
            $criteria->limit = 20;
             
            $products = Product::model()->findAll($criteria);
                 
            $resStr = '';
            foreach ($products as $product) {
                $resStr .= $product->title."\n";
            }
            echo $resStr;
        }
    }
    
	public function actionIndex()
	{
		$query = Yii::app()->request->getQuery('q');
		
		if (mb_strlen($query, 'UTF-8') < 2) {
			//$this->prepareSeo('Слишком короткий запрос');
			$this->render('index');
			return;
		}
		
		// поиск по продукции
		$criteria = new CDbCriteria();
		$criteria->addSearchCondition('code', $query, true, 'OR');
		$criteria->addSearchCondition('title', $query, true, 'OR');
		$criteria->addSearchCondition('alias', $query, true, 'OR');
		$criteria->addSearchCondition('id', $query, true, 'OR');

        $productDataProvider = new CActiveDataProvider('Product', array(
            'sort'=>array(
                'defaultOrder'=>'id DESC',
            ),
            'pagination'=>array(
                'pageSize' => 9999999,
            ),
            'criteria'=>$criteria,
        ));
		
        $category=Category::model();
        
		$this->render('index', compact('productDataProvider', 'category'));
	}
}
