<?if(D::role('sadmin')):?>
<div class="row">
    <?php echo $form->labelEx($model, 'events_title'); ?>
    <?php echo $form->textField($model, 'events_title', array('class'=>'form-control')); ?>
    <?php echo $form->error($model,'events_title'); ?>
</div>
<div class="row">
    <?php echo $form->labelEx($model, 'events_link_all_text'); ?>
    <?php echo $form->textField($model, 'events_link_all_text', array('class'=>'form-control')); ?>
    <?php echo $form->error($model,'events_link_all_text'); ?>
</div>
<?endif?>