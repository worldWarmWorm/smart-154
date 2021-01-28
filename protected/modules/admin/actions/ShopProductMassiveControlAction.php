<?php
/**
 * Действие обработки массовых действий над товарами
 * 
 * Требуется метод HTools::alias(), который доступен с версии 2.5.34
 */
namespace admin\actions;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use common\components\helpers\HAjax;
use common\components\helpers\HTools;
use common\components\helpers\HDb;

class ShopProductMassiveControlAction extends \CAction
{    
	protected $ajax;

    protected function getRequestParamAction()
    {
        return R::rget('action');
    }
    
    protected function getRequestParamCategoryId()
    {
        return (int)R::rget('category_id', 0);
    }
    
    protected function getRequestParamFromCategoryId()
    {
        return (int)R::rget('from_category_id', 0);
    }
    
    protected function getRequestParamCategoryName()
    {
        return R::rget('category_name', false);
    }
    
    protected function getRequestParamProducts()
    {
        return R::rget('products', []);
    }
    
    protected function getRequestParamForcy()
    {
        return (bool)R::rget('forcy', false);
    }
    
    public function run()
    {
        $this->ajax=HAjax::start();
        
        $productIds=[];
        $action=$this->getRequestParamAction();
        if($this->getRequestParamForcy()) {
            $product=new \Product;
            $criteria=$product->search(false, true);
            $criteria->select='`t`.`id`';
            $criteria->index='id';
            if($products=$product->findAll($criteria)) {
                $productIds=array_keys($products);
            }
        }
        else {
            $productIds=$this->getRequestParamProducts();
        }
        if($action && !empty($productIds)) {
            $categoryId=$this->getRequestParamCategoryId();
            $categoryName=$this->getRequestParamCategoryName();
            if($category=$this->getCategory($categoryId, $categoryName, ['remove'])) {
                switch($action) {
                    // копирование/клонирование товаров
                    case 'copy':
                        $this->copyTo($category, $productIds);
                        
                        Y::setFlash(
                            'SHOP_PRODUCT_MASSIVE_CONTROL_SUCCESS', 
                            'Выбранные товары скопированы в категорию '
                            . \CHtml::link($category->title, ['/cp/shop/category', 'id'=>$category->id])
                        );
                        
                        $this->ajax->success=true;
                        break;
                    // перенос товаров в другую категорию
                    case 'move':
                        $this->moveTo($category, $productIds);
                        
                        Y::setFlash(
                            'SHOP_PRODUCT_MASSIVE_CONTROL_SUCCESS', 
                            'Выбранные товары перенесены в категорию '
                            . \CHtml::link($category->title, ['/cp/shop/category', 'id'=>$category->id])
                        );
                        
                        $this->ajax->success=true;
                        break;
                    
                    // привязка категорий
                    case 'rel':
                        $this->relTo($category, $productIds);
                        
                        Y::setFlash(
                            'SHOP_PRODUCT_MASSIVE_CONTROL_SUCCESS', 
                            'Выбранным товарам привязана категория '
                            . \CHtml::link($category->title, ['/cp/shop/category', 'id'=>$category->id])
                        );
                        
                        $this->ajax->success=true;
                        break;
                        
                    // отвязка категорий
                    case 'unrel':
                        $this->unrelTo($category, $productIds);
                        
                        Y::setFlash(
                            'SHOP_PRODUCT_MASSIVE_CONTROL_SUCCESS', 
                            'Выбранные товары отвязаны от категории '
                            . \CHtml::link($category->title, ['/cp/shop/category', 'id'=>$category->id])
                        );
                        
                        $this->ajax->success=true;
                        break;
                        
                    // удаление товаров
                    case 'remove':
                        $this->remove($productIds);
                        
                        Y::setFlash(
                            'SHOP_PRODUCT_MASSIVE_CONTROL_SUCCESS', 
                            'Выбранные товары удалены'
                        );
                        
                        $this->ajax->success=true;
                        break;
                }
            }
        }
        
        $this->ajax->end();
    }
    
    protected function getCategory($parentId, $categoryName=false, $except=[])
    {
        if(!$parentId && !$categoryName) {
            return false;
        }
        
        $parentCategory=false;
        if($parentId) {
            $parentCategory=\Category::model()->findByPk($parentId);
            if(!$parentCategory) {
                return false;
            }
        }
        
        if(!in_array($this->getRequestParamAction(), $except) && $categoryName) {
            $category=new \Category;
            $category->title=$categoryName;
            $category->alias=$this->getAlias($categoryName);
            
            if($parentCategory) {
                $category->appendTo($parentCategory);
            }
            else {
                $category->saveNode();
            }
        }
        else {
            $category=$parentCategory;
        }
        
        return $category;
    }
    
    protected function getAlias($categoryName)
    {
        $alias=HTools::alias($categoryName);
        
        $postfix=2;
        $originAlias=$alias;
        while($category=\Category::model()->wcolumns(['alias'=>$alias])->find(['select'=>'id'])) {
            $alias=$originAlias . '-' . $postfix;
            $postfix++;
        }
        
        return $alias;
    }
    
    protected function setProductAlias(&$clone, $product, $category)
    {
        if($category->id == $this->getRequestParamFromCategoryId()) {
            $clone->title = trim($product->title).'_копия';
        }
        else {
            $clone->title=$product->title;
        }
        
        if(!$product->alias) {
            $clone->alias=HTools::alias($clone->title);
        }
        else {
            $clone->alias=$product->alias;
        }
        
        $postfix=2;
        $originAlias=$clone->alias;
        while($found=\Product::model()->wcolumns(['alias'=>$clone->alias])->find(['select'=>'id'])) {
            $clone->alias=$originAlias . '-' . $postfix;
            $postfix++;
        }
    }
    
    protected function moveTo($category, $productIds)
    {
        $criteria=HDb::criteria();
        $criteria->addInCondition('id', $productIds);        
        \Product::model()->updateAll(['category_id'=>$category->id], $criteria);
    }
    
    protected function relTo($category, $productIds)
    {
        $query='REPLACE INTO '.HDb::qt(\RelatedCategory::model()->tableName()).' (category_id, product_id) VALUES ';
        $values=[];
        foreach($productIds as $id) {
            $values[]="({$category->id}, {$id})";
        }
        $query.=implode(',', $values);
        
        HDb::execute($query);
    }
    
    protected function unrelTo($category, $productIds)
    {
        $queries=[];
        foreach($productIds as $id) {
            $queries[]='DELETE FROM '.HDb::qt(\RelatedCategory::model()->tableName())." WHERE category_id={$category->id} AND product_id={$id}";
        }
        $query=implode(';', $queries);
        
        HDb::execute($query);
    }
    
    protected function copyTo($category, $productIds)
    {
        foreach($productIds as $id) {
            $this->cloneProduct($category, $id);
        }
    }
    
    protected function remove($productIds)
    {
        $criteria=HDb::criteria();
        $criteria->addInCondition('id', $productIds);
        $criteria->select='id';
        if($products=\Product::model()->findAll($criteria)) {
            $fhelp=new \CFileHelper;
            foreach($products as $product) {
                if($product->asa('mainImageBehavior')) {
                    $product->mainImageBehavior->delete(false);
                }
                $files_to_copy=glob('images/product/{'.$id.','.$id.'_*}.*', GLOB_BRACE);
                if(!empty($files_to_copy)) {
                    foreach($files_to_copy as $key=>$file) {
                        $ext = $fhelp->getExtension($file);
                        $tmp = explode('/', $file);
                        $tmp = explode('.', $tmp[2]);
                        $tmp = explode('_', $tmp[0]);
                        if(isset($tmp[1])) {
                            unlink($_SERVER['DOCUMENT_ROOT'].'/images/product/'.$cloned_product->id.'_'.$tmp[1].'.'.$ext); 
                        }
                        else {
                            unlink($_SERVER['DOCUMENT_ROOT'].'/images/product/'.$cloned_product->id.'.'.$ext);
                        }
                    }
                }
                
                $product->delete();
            }
        }
    }
    
    protected function cloneProduct($category, $id)
    {
        if($product=\Product::model()->findByPk((int)$id)) {
            $cloned_product = new \Product;
            $cloned_product->attributes = $product->attributes;
            $cloned_product->category_id=$category->id;
            $this->setProductAlias($cloned_product, $product, $category);
            // $cloned_product->created=new \CDbExpression('NOW()');
            // $cloned_product->ordering=0;
            //Если продукт сохранен, то начинаем работу с картинками.
            //Объявляем хелпер.
            $fhelp=new \CFileHelper;
            //Получаем изображения.
            $id=$product->id;
            $files_to_copy = glob('images/product/{'.$id.','.$id.'_*}.*', GLOB_BRACE); 
            //Если продукт склонировался выполняем нужные действия
            if($cloned_product->save()){
                if($cloned_product->asa('mainImageBehavior')) {
                    if($oldMainImageFile=$cloned_product->mainImageBehavior->getFilename(true)) {
                        $cloned_product->main_image=$cloned_product->mainImageBehavior->getBasename() . '.' . pathinfo($cloned_product->mainImageBehavior->getFilename(true), PATHINFO_EXTENSION);
                        $newMainImageFile=$cloned_product->mainImageBehavior->getFilename(true);
						if(is_file($newMainImageFile)) {
	                        copy($oldMainImageFile, $newMainImageFile);
	                        $cloned_product->save();
						}
                    }
                }
                if(!empty($files_to_copy)) {
                    foreach ($files_to_copy as $key => $file) {
                        $ext = $fhelp->getExtension($file);
                        $tmp = explode('/', $file);
                        $tmp = explode('.', $tmp[2]);
                        $tmp = explode('_', $tmp[0]);
                        if(isset($tmp[1])){
                            copy( $file, 'images/product/'.$cloned_product->id.'_'.$tmp[1].'.'.$ext); 
                        }
                        else{

                            copy( $file, 'images/product/'.$cloned_product->id.'.'.$ext);

                        }
                    }
                }
                //Обработка дополнительных фотографий.
                $imgages = \CImage::model()->findAll(array('condition'=>"item_id = $product->id"));
                if($imgages) {
                    foreach ($imgages as $key => $img) {
                        $new_image = new \CImage;
                        $new_image->attributes = $img->attributes;
                        $uid = uniqid();
                        $ext = $fhelp->getExtension('/images/product/'.$img->filename);
                        $fname = $uid.'.'.$ext;
                        $new_image->filename = $fname;
                        $new_image->item_id = $cloned_product->id;
                        if(is_file(\Yii::getPathOfAlias('webroot.images.product').'/'.$img->filename) && copy('images/product/'.$img->filename, 'images/product/'.$fname)){
                            $new_image->save();
                        }
                    }
                }
            }
			else {
				$this->ajax->addError($cloned_product->getErrors());
			}
        }
    }
}

