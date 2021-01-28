<? 

$breadcrumbs = array();

$breadcrumbs[D::cms('shop_title', 'Каталог')] = array('shop/index');
if($model->category instanceof Category) {
	if($ancestors = $model->category->ancestors()->findAll()) { 
	  foreach($ancestors as $i=>$cat){
    	$breadcrumbs[$cat->title] = array('shop/category', 'id'=>$cat->id);
	  }
	}

	$breadcrumbs[$model->category->title] = array('shop/category', 'id'=>$model->category->id);
}
if($model->isNewRecord){
  $breadcrumbs[] = 'Создание товара';
}
else {
  $breadcrumbs[] = $model->title . ' - редактирование';
}
$this->breadcrumbs = $breadcrumbs;

?>

<?php



?>
<div class="form">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id'=>'page-form',
        'enableClientValidation'=>true,
        'clientOptions'=>array(
            'validateOnSubmit'=>true,
            'validateOnChange'=>false
        ),
        'htmlOptions' => array('enctype'=>'multipart/form-data'),
    )); ?>

     <?=$form->errorSummary($model)?>
    




    <?php 
    $tabs = array(
      'Основное'=>array('content'=>$this->renderPartial('_form_product', compact('model', 'form'), true), 'id'=>'tab-general'),
      'Seo'=>array('content'=>$this->renderPartial('_form_product_seo', compact('model', 'form'), true), 'id'=>'tab-seo'),            
    );

    if(Yii::app()->params['attributes'])
        $tabs['Атрибуты'] = array('content'=>$this->renderPartial('_form_product_attributes', compact('model', 'form', 'fixAttributes'), true), 'id'=>'tab-attrs');

    if(!$model->isNewRecord)
        $tabs['Дополнительные категории'] = array('content'=>$this->renderPartial('_form_product_categories', compact('model', 'form', 'categoryList', 'relatedCategories'), true), 'id'=>'tab-categories');
       
    $this->widget('zii.widgets.jui.CJuiTabs', array(
        'tabs'=> $tabs,
        'options'=>array()
    )); ?>

    <div class="row buttons">
      <div class="left">
        <?=CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', array('class'=>'btn btn-primary'))?>
        <?=CHtml::submitButton($model->isNewRecord ? 'Создать и выйти' : 'Сохранить и выйти', array('class'=>'btn btn-info', 'name'=>'saveout'))?>
        <?=CHtml::link('Отмена', array('index'), array('class'=>'btn btn-default')); ?>
      </div>

      <?php if (!$model->isNewRecord): ?>
        <div class="right">
          <a href="<?php echo $this->createUrl('shop/productDelete', array('id'=>$model->id)); ?>"onclick="return confirm('Вы действительно хотите удалить товар?')" class="btn btn-danger">Удалить товар</a>
        </div>
        <div class="right">
          <a href="<?php echo $this->createUrl('shop/productclone', array('id'=>$model->id)); ?>" class="btn btn-info">Клонировать товар</a>
        </div>
      <?php endif; ?>
      <div class="clr"></div>
    </div>

    <?php $this->endWidget(); ?>
</div><!-- form -->
