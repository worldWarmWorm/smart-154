<div class="form">

  <?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'event-form',
    'enableClientValidation'=>true,
    'clientOptions'=>array(
        'validateOnSubmit'=>true,
    ),
    'htmlOptions'=>array('enctype'=>'multipart/form-data'),
  )); ?>

	<?php echo $form->errorSummary($model); ?>

  <?php $this->widget('zii.widgets.jui.CJuiTabs', array(
    'tabs'=>array(
        'Основное'=>array('content'=>$this->renderPartial('_form_general', compact('model', 'form'), true), 'id'=>'tab-general'),
        'Seo'=>array('content'=>$this->renderPartial('_form_seo'    , compact('model', 'form'), true), 'id'=>'tab-seo'),
    ),
    'options'=>array()
  )); ?>
  


	<div class="row buttons">
    <?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', array('class'=>'btn btn-primary')); ?>
    <?=CHtml::submitButton($model->isNewRecord ? 'Создать и выйти' : 'Сохранить и выйти', array('class'=>'btn btn-info', 'name'=>'saveout'))?>
    <?php echo CHtml::link('Отмена', array('index'), array('class'=>'btn btn-default')); ?>
	</div>

  <?php $this->endWidget(); ?>
</div><!-- form -->
<?php if(!$model->isNewRecord): ?>
  <script type="text/javascript">
    $(document).ready(function(){
      $('.delete_img').on('click', function(){
        $.ajax({
          url: "/cp/event/killImage",
          data: { model_id: <?=(int)$_GET['id']?> }
        })
        .done(function( msg ) {
          $('.image_content').html(msg);
        });
        return false;
      });
    });
  </script>
<?php endif; ?>