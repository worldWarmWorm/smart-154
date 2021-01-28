<div class="photo_box" id="image-<?php echo $item->id; ?>">
  <div class="img">
    <?php echo CHtml::link('', array('default/removeImage', 'id'=>$item->id), array('class'=>'remove-icon')); ?>
	<? if($item->tmbUrl && file_exists($_SERVER['DOCUMENT_ROOT'].$item->tmbUrl)): ?>
    	<img src="<?php echo $item->tmbUrl; ?>" alt="" />
    <? else: ?>
    	<p style="color:#f00">Файл не может быть загружен.</p>
    	<?=CHtml::link('оригинал', $item->url, ['target'=>'_blank'])?>
    <? endif; ?>
  </div>

  <div class="buttons clear_fix">
    <a class="js-link left" onclick="openDialog(<?php echo $item->id; ?>);">изменить</a>
    <div>
      <a class="js-link right" onclick="insertImage(this, true, <?php echo $item->id; ?>)">вставить</a>
    </div>
  </div>

  <div class="modal fade" id="uplImgModal-<?php echo $item->id; ?>">
      <div class="modal-dialog modal-content">

      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" >Описание изображения</h4>
        </div>
        <div class="modal-body">
            <div class="row" style="text-align: center;">
              <img class='img-thumbnail' src="<?php echo $item->url; ?>" alt="" width="300" />
            </div>
            <div class="row">
              <div id="status-<?php echo $item->id; ?>" class="right"></div>
              <label for="desc-<?php echo $item->id; ?>">Описание</label>
              <textarea class="form-control" id="desc-<?php echo $item->id; ?>" name="desc-<?php echo $item->id; ?>"><?php echo $item->description; ?></textarea>
            </div>
        </div>
        <div class="modal-footer">
          <input type="button" class="btn btn-primary" value="Сохранить описание" onclick="saveImageDesc(<?php echo $item->id; ?>)" />
          <a class="link btn btn-danger" href="<?php echo Yii::app()->createUrl('admin/default/removeImage', array('id'=>$item->id)); ?>">Удалить фото</a>
          <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
        </div>
      </div>
    </div>
  </div>
</div>

