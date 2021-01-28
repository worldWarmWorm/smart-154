<div class="elementaryId" data-id="<?=$model->id?>">
	<div class="row">
		<small><b>Заголовок</b></small>
		<input value="<?=$model->title?>" class="title form-control" name="AccordionItems[]" type="text" maxlength="255">
	</div>

	<div class="row">
		<small><b>Порядок отображения</b></small>
		<input value="<?=$model->accordion_order?>" style="width: 130px" class="orderingInput form-control" name="accardionOrder" type="number" step="1" min="0" pattern="\d{10}" required>
	</div>

	<div class="row">
		<small><b>Описание</b></small>
		<?php                       
		    $this->widget('admin.widget.EditWidget.TinyMCE', array(
		        'model'=>$model,
		        'attribute'=>'description',
		        'editorSelector'=>'editor' . intval(rand(0,9999)*9999),
		        'editorSelectorId'=>empty($editorId) ? uniqid('id') : $editorId,
		        'registerJs'=>empty($editorId) || (AccordionItems::model()->count(['condition'=>'accordion_id='.(int)$model->accordion_id]) == 1),
		        'htmlOptions'=>array('class'=>'big elementEditor')
		    )); 
		?>
	</div>
	<?php 
	#Yii::app()->getClientScript()->registerCoreScript('jquery.ui');
	$this->widget('admin.widget.ajaxUploader.ajaxUploader', array(
	    'fieldName'=>'images' . intval(rand(0,9999)*9999),
	    'fieldLabel'=>'Загрузка фото',
	    'model'=>$model,
	    // 'tmb_height'=>100,
	    // 'tmb_width'=>100,
	    'fileType'=>'image'
	)); ?>
	<span class="btn btn-success saveItem" >Сохранить</span>
	<span class="btn btn-danger deleteElement" >Удалить</span>
	<span class="status_ok bg-success" style="padding: 7px 15px; opacity:0">Сохранено!</span> 
	<hr>
</div>