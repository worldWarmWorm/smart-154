<?php
/**
 *@var Product $model
 */
use common\components\helpers\HArray as A;
?>
<?php $this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), ['attribute'=>'hidden'])); ?>

<?php
$this->widget('\common\widgets\form\DropDownListField', A::m(compact('form', 'model'), [
    'attribute'=>'category_id',
    'data'=>\Category::model()->getCategories()
]));
?>

<?php $this->widget('\common\widgets\form\TextAreaField', A::m(compact('form', 'model'), [
    'attribute'=>'title',
    'htmlOptions'=>['class'=>'form-control', 'style'=>'min-height:50px']
])); ?>

<div class="row">
   <?php $this->widget('\common\widgets\form\AliasField', A::m(compact('form', 'model'), [
       'tag'=>false
   ])); ?>
   <div class="inline"><?=\CHtml::link('посмотреть на сайте', ['/shop/product', 'id'=>$model->id], ['target'=>'_blank'])?></div>
</div>

<?php $this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), ['attribute'=>'code'])); ?>

<?php 
if(D::cmsIs('shop_enable_brand')) {
    $this->widget('\common\widgets\form\DropDownListField', A::m(compact('form', 'model'), [
        'attribute'=>'category_id',
        'data'=>Brand::getListData(true),
        'htmlOptions'=>['class'=>'form-control w50', 'empty'=>'Не указан']
    ]));
}
?>

<div class="row">
	<?php
	   $this->widget('\common\widgets\form\NumberField', A::m(compact('form', 'model'), [
	       'tagOptions'=>['class'=>'col-md-4', 'style'=>'padding-left:0'],
	       'attribute'=>'price',
	       'unit'=>' руб.',
	       'htmlOptions'=>['class'=>'form-control w50 inline', 'step'=>'0.01']
	   ]));
	?>
    <?php
    if(D::cms('shop_enable_old_price')) {
        $this->widget('\common\widgets\form\NumberField', A::m(compact('form', 'model'), [
            'tagOptions'=>['class'=>'col-md-4'],
            'attribute'=>'old_price',
            'unit'=>' руб.',
            'htmlOptions'=>['class'=>'form-control w50 inline', 'step'=>'0.01']
        ]));
    }
    ?>
</div>

<div class="panel panel-default">
    <div class="panel-body" style="padding:10px 15px 0">
        <div class="row">
            <?php $this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), ['attribute'=>'notexist', 'tagOptions'=>['class'=>'col-md-3']])); ?>
            <?php $this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), ['attribute'=>'new', 'tagOptions'=>['class'=>'col-md-3']])); ?>
            <?php $this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), ['attribute'=>'sale', 'tagOptions'=>['class'=>'col-md-3']])); ?>
            <?php $this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), ['attribute'=>'hit', 'tagOptions'=>['class'=>'col-md-3']])); ?>
        </div>
        <div class="row">
        	<?php
            if(D::cms('shop_enable_carousel')==1) {
                $this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), ['attribute'=>'in_carousel', 'tagOptions'=>['class'=>'col-md-6']]));
            }
            ?>
      
    		<?php $this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), ['attribute'=>'on_shop_index', 'tagOptions'=>['class'=>'col-md-6']])); ?>
        </div>
    </div>
</div>


<? $this->widget('\common\ext\file\widgets\UploadFile', [
    'behavior'=>$model->mainImageBehavior, 
    'form'=>$form, 
    'actionDelete'=>$this->createAction('removeProductMainImage'),
    'tmbWidth'=>200,
    'tmbHeight'=>200,
    'view'=>'panel_upload_image'
]); ?>

<div class="row">
    <?php echo $form->labelEx($model,'description'); ?>
    <?php 
        $this->widget('admin.widget.EditWidget.TinyMCE', array(
            'model'=>$model,
            'attribute'=>'description',
            'htmlOptions'=>array('class'=>'big')
        )); 
    ?>
    <?php echo $form->error($model,'description'); ?>
</div>

<?php if($model->isNewRecord): ?>
    <div class="alert alert-info">Загрузка дополнительных изображений и файлов будет доступна после создания товара</div>
<?php else: ?>
    <?php $this->widget('admin.widget.ajaxUploader.ajaxUploader', array(
        'fieldName'=>'images',
        'fieldLabel'=>'Загрузка фото',
        'model'=>$model,
        'tmb_height'=>100,
        'tmb_width'=>100,
        'fileType'=>'image'
    )); ?>

    <?php $this->widget('admin.widget.ajaxUploader.ajaxUploader', array(
        'fieldName'=>'files',
        'fieldLabel'=>'Загрузка файлов',
        'model'=>$model,
    )); ?>
<?php endif; ?>





