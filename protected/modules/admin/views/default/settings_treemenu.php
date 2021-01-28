<div class="row">
    <?php echo $form->checkBox($model, 'treemenu_show_breadcrumbs'); ?>
    <?php echo $form->labelEx($model, 'treemenu_show_breadcrumbs', array('class'=>'inline')); ?>
	<?php echo $form->error($model, 'treemenu_show_breadcrumbs'); ?>
</div>

<?if($model->isDevMode()):?>
<div class="row">
    <?php echo $form->labelEx($model, 'treemenu_depth'); ?>
    <?php echo $form->dropDownList($model, 'treemenu_depth', [1=>'1', '-'=>'Неограниченно'], array('class'=>'form-control w25')); ?>
    <?php echo $form->error($model,'treemenu_depth'); ?>
</div>

<div class="row">
    <?php echo $form->labelEx($model, 'treemenu_fixed_id'); ?>
    <?php echo $form->textField($model, 'treemenu_fixed_id', array('class'=>'form-control')); ?>
    <?php echo $form->error($model,'treemenu_fixed_id'); ?>
</div>

<div class="row">
    <?php echo $form->checkBox($model, 'treemenu_show_id'); ?>
    <?php echo $form->labelEx($model, 'treemenu_show_id', array('class'=>'inline')); ?>
	<?php echo $form->error($model, 'treemenu_show_id'); ?>
</div>
<?endif?>
