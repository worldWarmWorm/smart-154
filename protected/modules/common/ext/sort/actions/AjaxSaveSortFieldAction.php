<?php
/**
 * Сохранение значения сортировки 
 *
 */
namespace common\ext\sort\actions;

use common\components\helpers\HAjax;
use common\components\helpers\HHook;

class AjaxSaveSortFieldAction extends \CAction
{
    public $className;
    public $behaviorName='sortFieldBehavior';
    public $scenario='insert';
    public $value=null;
    
    public $onBeforeSave=null;
    
    public function run($id)
    {
        $ajax=HAjax::start();
        
        if(is_numeric($this->value)) {
            $className=$this->className;
            $model=new $className($this->scenario);
            if($model instanceof \CActiveRecord) {
                if(!($model=$model->findByPk($id))) {
                    $ajax->end();
                }
            }
            $model->id=(int)$id;
            
            $sortAttribute=$model->{$this->behaviorName}->attribute;
            $sort=$model->$sortAttribute;
            $model->$sortAttribute=(int)$this->value;
            if(HHook::hexec($this->onBeforeSave, [&$model], true)) {
                if($sort != $model->$sortAttribute) {
                    $model->update([$sortAttribute]);
                }
                $ajax->success=true;
            }
            
            $ajax->addErrors($model->getErrors());
        }
        
        $ajax->end();
    }
}