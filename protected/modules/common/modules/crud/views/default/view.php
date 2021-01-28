<?php
/** @var \crud\controllers\DefaultController $this */
/** @var string $cid */
/** @var \common\components\base\ActiveRecord $model */
use common\components\helpers\HYii as Y;
use common\components\helpers\HHtml;
use crud\components\helpers\HCrud;
use crud\components\helpers\HCrudPublic;

$t=Y::ct('CrudModule.common', 'crud');
$attributeTitle=HCrud::param($cid, 'public.view.attributeTitle', 'title');
$attributeText=HCrud::param($cid, 'public.view.attributeText', 'text');
if(!is_string($attributeText) && is_callable($attributeText)) {
    $content=call_user_func_array($attributeText, [$model]);
}
else {
    $content=$model->$attributeText;
}
?>
<h1><?= $model->$attributeTitle; ?></h1>

<?= $content; ?>

<br/>
<?= HHtml::linkBack('Назад', HCrudPublic::getIndexUrl($cid), HCrudPublic::getIndexUrl($cid)); ?>
