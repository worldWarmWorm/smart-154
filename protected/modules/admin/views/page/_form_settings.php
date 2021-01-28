<?if(D::role('sadmin')):?>
<div class="row">
    <?php echo $form->label($model, 'view_template'); ?>
    <?php echo $form->textField($model, 'view_template', array('class'=>'form-control')); ?>
    <?php echo $form->error($model,'view_template'); ?>
    <p class="note">По умолчанию "page"</p>
</div>
<?endif?>