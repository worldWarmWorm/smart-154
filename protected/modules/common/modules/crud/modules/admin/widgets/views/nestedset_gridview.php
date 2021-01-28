<?php
/** @var $this [\common\widgets\nestable\BaseNestable] */

$this->getOwner()->renderPartial('crud.modules.admin.views.default._gridview', [
    'cid'=>$this->cid,
    'dataProvider'=>$this->dataProvider
]);
?>
