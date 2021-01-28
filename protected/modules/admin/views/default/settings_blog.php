<div class="row">
    <?php echo $form->label($model, 'blog_show_created'); ?>
    <?php echo $form->dropDownList($model, 'blog_show_created', array(0=>'Нет', 1=>'Да'), array('class'=>'form-control w10')); ?>
    <?php echo $form->error($model,'blog_show_created'); ?>
</div>
