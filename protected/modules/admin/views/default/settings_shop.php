<? use common\components\helpers\HArray as A; ?>
<?if($model->isDevMode()):?>
<div class="row">
    <?php echo $form->labelEx($model, 'shop_title'); ?>
    <?php echo $form->textField($model, 'shop_title', array('class'=>'form-control')); ?>
    <?php echo $form->error($model,'shop_title'); ?>
</div>

<div class="row">
    <?php echo $form->labelEx($model, 'shop_product_page_size'); ?>
    <?php echo $form->textField($model, 'shop_product_page_size', array('class'=>'w10 form-control')); ?>
    <?php echo $form->error($model,'shop_product_page_size'); ?>    
</div>

<div class="row">
    <?php echo $form->labelEx($model, 'shop_pos_description'); ?>
    <?php echo $form->error($model,'shop_pos_description'); ?>
    <?$model->shop_pos_description = $model->shop_pos_description<>1 ? 0 : 1;?>
    <?php echo $form->radioButtonList($model, 'shop_pos_description', array(0=>'перед списком товаров', 1=>'после списка товаров'), array(
    	'labelOptions'=>array('class'=>'inline'), 
    )); ?>
</div>

<? $this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), ['attribute'=>'shop_enable_brand'])); ?> 


<div class="row">
    <?php echo $form->checkBox($model, 'shop_enable_carousel'); ?>
    <?php echo $form->labelEx($model, 'shop_enable_carousel', array('class'=>'inline')); ?>
	<?php echo $form->error($model, 'shop_enable_carousel'); ?>
</div>

<div class="row">
    <?php echo $form->checkBox($model, 'shop_enable_reviews'); ?>
    <?php echo $form->labelEx($model, 'shop_enable_reviews', array('class'=>'inline')); ?>
	<?php echo $form->error($model, 'shop_enable_reviews'); ?>
</div>

<div class="row">
    <?php echo $form->checkBox($model, 'shop_enable_attributes'); ?>
    <?php echo $form->labelEx($model, 'shop_enable_attributes', array('class'=>'inline')); ?>
	<?php echo $form->error($model, 'shop_enable_attributes'); ?>
</div>

<div class="row">
    <?php echo $form->checkBox($model, 'shop_enable_hit_on_top'); ?>
    <?php echo $form->labelEx($model, 'shop_enable_hit_on_top', array('class'=>'inline')); ?>
	<?php echo $form->error($model, 'shop_enable_hit_on_top'); ?>
</div>

<div class="row">
    <?php echo $form->checkBox($model, 'shop_enable_old_price'); ?>
    <?php echo $form->labelEx($model, 'shop_enable_old_price', array('class'=>'inline')); ?>
	<?php echo $form->error($model, 'shop_enable_old_price'); ?>
</div>

<div class="row">
    <?php echo $form->checkBox($model, 'shop_show_categories'); ?>
    <?php echo $form->labelEx($model, 'shop_show_categories', array('class'=>'inline')); ?>
	<?php echo $form->error($model, 'shop_show_categories'); ?>
</div>

<div class="row">
    <?php echo $form->labelEx($model, 'shop_category_descendants_level'); ?>
    <?php echo $form->textField($model, 'shop_category_descendants_level', array('class'=>'w10 inline form-control')); ?>
    <?php echo $form->error($model, 'shop_category_descendants_level'); ?>
    <p class="note">
    	Чтобы выводились товары только самой категории оставьте поле пустым, или 0(нуль)<br>
    	Чтобы выводились товары из всех вложенных категорий, введите большое число (напр: 999)
    </p>
</div>

<? $this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), ['attribute'=>'shop_menu_enable'])); ?>
<? $this->widget('\common\widgets\form\NumberField', A::m(compact('form', 'model'), [
    'attribute'=>'shop_menu_level', 
    'htmlOptions'=>['class'=>'form-control w10']
])); ?>
         
<?endif?>
