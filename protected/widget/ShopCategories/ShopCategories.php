<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 13.01.12
 * Time: 14:37
 * To change this template use File | Settings | File Templates.
 */
use common\components\helpers\HYii as Y;
use common\ext\updateTime\behaviors\UpdateTimeBehavior;

class ShopCategories extends CWidget
{
    public $listClass = 'shop-menu';
    public $subClass='shop-menu__sub';

    public $setActiveCategoryInProduct = true;
    public $activeIDs = [];

    private $_categories;

    public function run()
    {
        if ($this->setActiveCategoryInProduct && Yii::app()->controller->id == 'shop' && Yii::app()->controller->action->id == 'product') {
            $product = Product::model()->findByPk(Yii::app()->request->getQuery('id'));

            $category = $product->category;

            $IDs = array_keys($category->ancestors()->findAll(['index' => 'id']));
            $IDs[] = $category->id;

            $this->activeIDs = $IDs;
        }

        UpdateTimeBehavior::setStaticAutoSendLastModified(false);

		$cacheId='widgetShopCategoriesItems_' . md5(serialize($this->activeIDs));
		$items=Y::cache()->get($cacheId);
        if(empty($items)) {
	        //$categories = Category::model()->findAll(array('order'=>'ordering'));
    	    $this->_categories = Category::model()->findAll(array(
        	    'select'=>'id, title, lft, rgt, root, level, ordering', 
            	'order'=>'ordering, lft'
	        ));

	        //$items = CmsCore::prepareTreeMenu($categories);
    	    $items = $this->prepareTree();
			Y::cache()->set($cacheId, $items);
		}

        $this->render('default', compact('items'));
        
        UpdateTimeBehavior::setStaticAutoSendLastModified(null);
    }

    private function prepareTree($level = 1, $parent = null)
    {
        $items = array();

        foreach($this->_categories as $cat) {
            /* @var Menu|NestedSetBehavior $cat */

            if ($cat->level!=$level)
                continue;

            if ($parent && !$cat->isDescendantOf($parent))
                continue;

            $isLeaf = $cat->isLeaf();

            $item = array(
                'id' => $cat->id,
                'label'=>!$isLeaf ? $cat->title . CHtml::tag('span', ['class' => 'deploy'], '') : $cat->title,
                'url'=>array('/shop/category', 'id'=>$cat->id),
                'linkOptions'=>array('title'=>$cat->getMetaATitle()),
            );

            if (!$isLeaf) {
                if($this->subClass) $item['itemOptions']=['class'=>$this->subClass];
                $item['items'] = $this->prepareTree($cat->level+1, $cat);
            }

            if (in_array($cat->id, $this->activeIDs)) {
                $item['itemOptions']['class'] = !empty($item['itemOptions']['class']) ? $this->subClass . ' active' : 'active';
            }

            $items[] = $item;
        }
        return $items;
    }
}
