<?if(D::yd()->isActive('gallery') && D::role('sadmin')):?>
<div class="row">
    <?php echo $form->labelEx($model, 'gallery_title'); ?>
    <?php echo $form->textField($model, 'gallery_title', array('class'=>'form-control')); ?>
    <?php echo $form->error($model,'gallery_title'); ?>
</div>
<?endif?>