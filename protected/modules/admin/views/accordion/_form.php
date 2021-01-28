<?php
/* @var $this AccordionController */
/* @var $model Accordion */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'accordion-form',
	'enableAjaxValidation'=>false,
)); 

?>


	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textField($model,'title', array('class'=>'form-control', 'style'=>'display:inline-block;')); ?>
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', array('class'=>'btn btn-primary'.($model->isNewRecord ? '' : ' js-save-acc-name'))); ?>
		<span id="Accordion_title_msg_updated" class="bg-success" style="padding: 7px 15px; opacity: 0;">Сохранено!</span> 
		<?php echo $form->error($model,'title'); ?>
	</div>

	<?php if (!$model->isNewRecord): ?>
		<div>
			<?$this->renderPartial('_accordion_items', compact('model'));?>
		</div>
	<?endif;?>


<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
	$(document).ready(function(){
		$(document).on('click', '.js-save-acc-name', function(e) {
			e.preventDefault();
			$.post("/cp/accordion/update/<?=$model->id?>", {Accordion: {title: $("#Accordion_title").val()}}, function() {
				 $(".js-acc-title").html($("#Accordion_title").val());
				  $("#Accordion_title_msg_updated").animate({opacity:1}, 200, function() { $("#Accordion_title_msg_updated").delay(2000).animate({opacity:0}, 200); });
			});
			return false;
		});

		$(document).on('click', '.deleteElement', function(){

			if(!confirm('Вы действительно хотите удалить эту запись?')){
				return false;
			} 

			var data_id = $(this).parents('.elementaryId').data('id');

			$.ajax({
				url: "/cp/accordion/deleteItem/"+data_id,
			})
			.done(function(status) {
				if(status==1){
					$('.elementaryId[data-id='+data_id+']').remove();
				}
			});
		});


		$(document).on('click', '.saveItem', function(){
			tinyMCE.triggerSave();
			var data_id = $(this).parents('.elementaryId').data('id');
			var title = $('.elementaryId[data-id='+data_id+']').find('.title').val();

			var this_item = $(this);
			var description = $('.elementaryId[data-id='+data_id+']').children('.row').children('textarea').val();
			var ordering = $('.elementaryId[data-id='+data_id+']').children('.row').children('.orderingInput').val();

			$.ajax({
				method: 'POST',
				url: "/cp/accordion/updateItem/"+data_id,
				data: {
					title: title,
					order: ordering,
					description: description	
				}
			})
			.done(function(status) {
				if(status==1){
					var $statusOk=$(this_item).siblings('.status_ok');
					$statusOk.animate({opacity:1}, 200, function() { $statusOk.delay(2000).animate({opacity:0}, 200); });
				} else {
					alert('При сохранении произошла ошибка');
				}

			});
		});

		$('.addItem').on('click', function() {
				$.ajax({
					url: "/cp/accordion/addItem/<?=Yii::app()->request->getQuery('id', 0)?>"
				})
				.done(function( html ) {
					$("#contentInput").prepend(html);
				});
		});
	});
</script>