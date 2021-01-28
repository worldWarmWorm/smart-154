<?php
/** @var $this [\common\modules\crud\modules\admin\widgets\NestedSetNestable] */
/** @var $data mixed модель */
/** @var $viewData array дополнительные данные */
use common\components\helpers\HArray as A;

$this->printColumns($data, A::get(A::toa($viewData), 'columnsOptions', []));
?>
