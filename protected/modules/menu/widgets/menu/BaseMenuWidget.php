<?php
/**
 * Base widget class for menu module
 * 
 * use \TreeModelHelper::getTree()
 */
namespace menu\widgets\menu;

use \menu\models\Menu;
use \menu\components\helpers\UrlHelper;

abstract class BaseMenuWidget extends \CWidget
{
	/**
	 * Root DOM-element id
	 * @var string
	 */
	public $id;
	
	/**
	 * Css class name of root DOM-element
	 * @var string
	 */
	public $cssClass = '';
	
	/**
	 * Max count of visible root items.
	 * Zero value for unlimit.
	 * @var int
	 */
	public $rootLimit=0;

	/**
	 * @var integer максимальная глубина вложенности меню для отображения.
	 */
	public $maxLevel = null;

	/**
	 * Is admin section. 
	 * @var boolean
	 */
	public $adminMode = false;
	
	/**
	 * Menu tree. For pseudo-caching of generate menu tree. 
	 * Default "false" - not initialized.  
	 * @var boolean|array
	 */
	protected $tree = false;
	
	/**
	 * (non-PHPdoc)
	 * @see CWidget::run()
	 */
	public function run() 
	{
		// for example	
		// $tree = $this->getTree();
		// $menu = $this->renderItems($tree, 0, true);
		// $this->render('default', compact('menu'));
	} 
	
	/**
	 * Get tree of menu items
	 * @param boolean $visibled if set as true return only visible items. Default (false) get all nonsystem.
	 * @param boolean $reload regenerate tree or not. Default "not" (false).
	 * @return array menu tree (for more @see \TreeModelHelper::getTree()).
	 */
	protected function getTree($visibled=false, $reload=false)
	{
		if($reload || ($this->tree === false)) {
			$model = Menu::model()->nonsystem();
			if($visibled) $model = $model->visibled();
			$this->tree = \TreeModelHelper::getTree($model->findAll(array('order'=>'ordering')), 'id', 'parent_id');
		}
		
		return $this->tree;
	}
	
	/**
	 * Render menu items
	 * @param array $items menu items, like as, result of \TreeModelHelper::getTree().
	 * @param boolean $level deep level. Zero value is root.
	 * @param boolean $return return html code or output to broweser. Default "false" - output to browser. 
	 * @return void|string if parameter $return is true, return HTML code of menu items.
	 */
	protected function renderItems(&$items, $level=0, $return=false) {
		
		if($this->maxLevel && ($level >= $this->maxLevel)) return '';
		$html = '<ul ';
		if($level>0){
			$html .= 'class="sub-menu__'.$level.'"';
		}


		if(!$level) $html .= \HtmlHelper::AttributesToString(array('id'=>$this->id, 'class'=>$this->cssClass));
		
		$html .= '>';
		$i=0;

        $shopIndexUrl=\Yii::app()->createUrl('/shop/index');
		foreach($items as $item) {
			
			if((!$level && $this->rootLimit) && ($i++ >=$this->rootLimit)) break;
			$url = UrlHelper::createUrl($item['model'], $this->adminMode);


			$html .= '<li';

			$class = '';

			if(!empty($item['childs'])){
				$class = 'has-sub__' . ($level+1);
			} else {
				
			}

			if(preg_match('/^\/?(' . preg_replace('/^([^\/]+)\/?.*/', '\\1', \Yii::app()->request->pathInfo) . ')$/Ui', $url)) {
				$class .= ' active';
			} elseif (!empty($item['childs']) && $this->checkChildsActive($item['childs'])) {
				$class .= ' active';
			}

			if($class)
				$html .= ' class="' . $class . '"';
			
			$html .= '>';  
			$html .= \CHtml::link('<span>'.$item['model']->title.'</span>', $url, array('title'=>$item['model']->seo_a_title));

			if(!empty($item['childs'])) {
				$html .= $this->renderItems($item['childs'], ($level + 1), true);
			}

            if(\D::cms('shop_menu_enable')) {
                if ($url == $shopIndexUrl) {
                    if($roots=\Category::model()->roots()->findAll(['select'=>'id, title, lft, rgt, level, root', 'order'=>'ordering'])) {
                        $html.=$this->getCategoryMenuHtml($roots);
                    }
                }
            }

			$html .= '</li>';
		}

		$html .= '</ul>';

		if($return) return $html; 
		else echo $html;
	}
    
    protected function getCategoryMenuHtml($categories, $level=0)
    {
        $maxLevel=(int)\D::cms('shop_menu_level', 1);
        if($level > $maxLevel) return '';
        
        $html='<ul>';
        foreach ($categories as $category) {
            $html.=\CHtml::openTag('li'); 
            $html.=\CHtml::link($category->title, ['/shop/category', 'id'=>$category->id]);
            if(($level + 1) < $maxLevel) {
                if($subcategories=$category->children()->findAll(['select'=>'id, title, lft, rgt, level, root', 'order'=>'lft'])) {
                    $html.=$this->getCategoryMenuHtml($subcategories, $level+1);
                }
                
            }
            $html.=\CHtml::closeTag('li');
        }
        $html.='</ul>';
        
        return $html;
    }

	/**
	 * Получение потомков по переданному id предка.
	 * @param array $tree дерево, результат выполнения \TreeModelHelper::getTree().
	 * @param int $parentId parent id.
	 * @return mixed в случае успеха, возращает массив, 
	 * иначе NULL, если элемент не найден, либо у него нет потомков.
	 */
	protected function getChildren(&$tree, $parentId)
	{
		foreach($tree as $id=>$node) {
			if(($id == $parentId)) {
				return isset($node['childs']) ? $node['childs'] : array(); 
			}
			elseif(isset($node['childs'])) {
				$result = $this->getChildren($node['childs'], $parentId);
				if(!is_null($result)) {
					return $result;
				}
			}
		}
		
		return null;
	}

	protected function checkChildsActive($items)
	{
		foreach ($items as $item) {
			$url = UrlHelper::createUrl($item['model'], $this->adminMode);

			if (preg_match('/^\/?(' . preg_replace('/^([^\/]+)\/?.*/', '\\1', \Yii::app()->request->pathInfo) . ')$/Ui', $url)) {
				return true;
			}

			if (!empty($item['childs']) && $this->checkChildsActive($item['childs'])) {
				return true;
			}
		}

		return false;

	}
}
