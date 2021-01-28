<?if($model->isDevMode()):?>
<div class="row">
    <?php echo $form->labelEx($model, 'sale_title'); ?>
    <?php echo $form->textField($model, 'sale_title', array('class'=>'form-control')); ?>
    <?php echo $form->error($model,'sale_title'); ?>
</div>
<div class="row">
    <?php echo $form->labelEx($model, 'sale_link_all_text'); ?>
    <?php echo $form->textField($model, 'sale_link_all_text', array('class'=>'form-control')); ?>
    <?php echo $form->error($model,'sale_link_all_text'); ?>
</div>
<div class="row">
    <?=$form->labelEx($model, "sale_preview_width"); ?>
    <?=$form->textField($model, "sale_preview_width", array('class'=>'w10 form-control')); ?>
    <?=$form->error($model, "sale_preview_width"); ?>
</div>

<div class="row">
    <?=$form->labelEx($model, "sale_preview_height"); ?>
    <?=$form->textField($model, "sale_preview_height", array('class'=>'w10 form-control')); ?>
    <?=$form->error($model, "sale_preview_height"); ?>
</div>
<?endif?>
<h1>SEO</h1>
<div class="row">
    <?php echo $form->label($model, 'sale_meta_h1'); ?>
    <?php echo $form->textField($model, 'sale_meta_h1', array('class'=>'form-control')); ?>
    <?php echo $form->error($model, 'sale_meta_h1'); ?>
</div>

<div class="row">
    <?php echo $form->label($model, 'sale_meta_title'); ?>
    <?php echo $form->textField($model, 'sale_meta_title', array('class'=>'form-control')); ?>
    <?php echo $form->error($model, 'sale_meta_title'); ?>
</div>

<div class="row">
    <?php echo $form->label($model, 'sale_meta_key'); ?>
    <?php echo $form->textArea($model, 'sale_meta_key', array('class'=>'form-control')); ?>
    <?php echo $form->error($model, 'sale_meta_key'); ?>
</div>

<div class="row">
    <?php echo $form->label($model, 'sale_meta_desc'); ?>
    <?php echo $form->textArea($model, 'sale_meta_desc', array('class'=>'form-control')); ?>
    <?php echo $form->error($model, 'sale_meta_desc'); ?>
</div>