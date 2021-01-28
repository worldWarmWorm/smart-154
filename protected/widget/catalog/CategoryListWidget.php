<?php
/**
 * Список категорий каталога
 *
 */
class CategoryListWidget extends \CWidget
{
	/**
	 * @var integer|NULL id категории. Если передан, то будет 
	 * выведены подразделы данной категории. 
	 * По умолчанию (NULL) корневые разделы.
	 */
	public $id=null;
	
	/**
	 * @var string имя шаблона отображения
	 */
	public $view='category_list';
	
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::run()
	 */
	public function run()
	{
		if($this->id) {
			$category=Category::model()->findByPk($this->id);
			if(empty($category)) return;
			
			$categories=$category->children()->findAll(['order'=>'lft']);
		}
		else {
			$categories=Category::model()->roots()->findAll(['order'=>'ordering']);
		}
			
		$this->render($this->view, compact('categories'));
	}
}
