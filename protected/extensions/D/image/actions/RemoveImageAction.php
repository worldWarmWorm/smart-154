<?php
/**
 * Действие удаления картинки
 *
 */
namespace ext\D\image\actions;

class RemoveImageAction extends \CAction
{
	public $modelName;
	
	public $imageBehaviorName='imageBehavior';
	
	public $ajax=true;
	
	public function run($id)
	{
		$modelName=$this->modelName;
		
		$model=$modelName::model();
		$model->id=$id;
		$model->{$this->imageBehaviorName}->delete();
		
		if($this->ajax) {
			\Yii::app()->end();
			die;
		}
	}
}