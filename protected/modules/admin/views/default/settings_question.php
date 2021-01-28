<div class="row">
    <?php echo $form->checkBox($model, 'question_collapsed'); ?>
    <?php echo $form->labelEx($model, 'question_collapsed', array('class'=>'inline')); ?>
	<?php echo $form->error($model, 'question_collapsed'); ?>
</div>