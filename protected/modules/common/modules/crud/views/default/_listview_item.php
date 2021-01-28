<?php
use common\components\helpers\HYii as Y;
use crud\components\helpers\HCrud;
use crud\components\helpers\HCrudPublic;

$t=Y::ct('CrudModule.common', 'crud');
$attributeTitle=HCrud::param($cid, 'public.index.attributeTitle', 'title');
$attributeText=HCrud::param($cid, 'public.index.attributeText', 'text');
if(!is_string($attributeText) && is_callable($attributeText)) {
    $content=call_user_func_array($attributeText, [$data]);
}
else {
    $content=$data->$attributeText;
}
?>
<div class="crud__listview-item">
	<div class="crud__listview-item_title"><?= \CHtml::link($data->$attributeTitle, HCrudPublic::getViewUrl($cid, $data->id)); ?></div>
	<div class="crud__listview-item_text"><?= $content; ?></div>
</div>
