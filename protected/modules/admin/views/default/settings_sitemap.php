<?
use common\components\helpers\HArray as A;

$this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), ['attribute'=>'sitemap_auto_generate']));
?>

<div class="row">
    <?php echo $form->label($model, 'sitemap_priority'); ?>
    <?php echo $form->numberField($model, 'sitemap_priority', ['step'=>0.01, 'class'=>'form-control w10']); ?>
    <?php echo $form->error($model,'sitemap_priority'); ?>
    <p class="note">Допустимый диапазон значений — от 0,0 до 1,0.</p>
</div>

<div class="row">
    <?php echo $form->label($model, 'sitemap_changefreq'); ?>
    <?php echo $form->dropDownList($model, 'sitemap_changefreq', [
        'always'=>'always', 
        'hourly'=>'hourly', 
        'daily'=>'daily', 
        'weekly'=>'weekly', 
        'monthly'=>'monthly', 
        'yearly'=>'yearly', 
        'never'=>'never'
    ], ['class'=>'form-control w25']); ?>
    <?php echo $form->error($model,'sitemap_changefreq'); ?>
</div>
<? /* ?>
<div class="row">
    <?php echo $form->label($model, 'sitemap_auto'); ?>
    <?php echo $form->numberField($model, 'sitemap_auto', ['step'=>0.01, 'class'=>'form-control w25 inline']); ?><span> сек.</span>
    <?php echo $form->error($model,'sitemap_auto'); ?>
    <p class="note">Пустое значение или 0(нуль) - отключить автогененрацию.<br/>Рекомендуемое значение 86400 (24 часа)</p>
</div>
<? /**/ ?>

<div class="row">
    <?php $this->widget('admin.widget.Seo.generateSitemap'); ?>
</div>
