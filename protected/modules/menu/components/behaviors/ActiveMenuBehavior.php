<?php
/**
 * Active menu behavior
 * 
 * Поведение объекта, как пункта меню.
 * Необходимо, чтобы у объекта были аттрибуты "id", "title"
 */
namespace menu\components\behaviors;

use common\components\helpers\HDb;
use \menu\models\Menu;

class ActiveMenuBehavior extends \CActiveRecordBehavior
{
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecordBehavior::afterSave()
	 */
	public function afterSave()
	{
	    // @hook for Page model
	    if ((get_class($this->owner) == 'Page') && $this->owner->blog_id) {
	        return true;
	    }

	    $menu = $this->findByOptionId($this->owner->id);

	    if (!$menu) {
	        // Добавление нового пункта меню
	        $menu = new Menu();

	        $menu->type = 'model';
	        $menu->options = array('model' => lcfirst(get_class($this->owner)), 'id' => $this->owner->id);
	        $menu->ordering = (int)HDb::queryScalar('SELECT MAX(`ordering`) FROM `menu`') + 1;
	    }

	    $menu->title = $this->owner->title;

	    if (!$menu->save()) {
	        throw new \ErrorException("Пункт меню \"{$this->title}\" не был добавлен.", 0, E_NOTICE);
	    }

	    return true;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecordBehavior::afterDelete()
	 */
	public function afterDelete()
	{
		// Удаление пункта меню
		if($menu = $this->findByOptionId($this->owner->id)) {
			$model=Menu::model()->findByPk($menu->id);
			if($model)
				$model->delete();
		}
		
		return true;
	}
	
	/**
	 * Find \menu\models\Menu model by option id
	 * @param integer $optionId options id.
	 * @return \menu\models\Menu|NULL
	 */
	protected function findByOptionId($optionId)
	{
		$items = Menu::model()->findAll();
		foreach($items as $item) {
			if(isset($item->options['model']) && ($item->options['model'] == strtolower(get_class($this->owner)))
				&& isset($item->options['id']) && ($item->options['id'] == $optionId)) {
				return $item;
			}
		}
		
		return null;
	}
}
