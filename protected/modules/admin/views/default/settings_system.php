<?
use common\components\helpers\HArray as A;

if($model->isDevMode()) {
    $this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), ['attribute'=>'system_admins']));
}