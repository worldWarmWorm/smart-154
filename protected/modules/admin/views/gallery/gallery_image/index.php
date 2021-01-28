
<link rel="stylesheet" href="/js/jfileupload/css/jquery.fileupload.css">
<link rel="stylesheet" href="/js/fancybox/jquery.fancybox.min.css">


<?php
    $album_id = (int)$_GET['id'];
    $g = Gallery::getAlbumLink($album_id);
    $this->breadcrumbs=array(
        $this->getGalleryHomeTitle()=>array('gallery/index'),
        $g->title=>array('gallery/updateGallery', 'id'=>$g->id),
        'Фотографии альбома',
    );
?>


<?php 


$current_images = GalleryImg::getAlbumImages($album_id); ?>

<h1><?=$g->title?></h1>

<span class="btn btn-success fileinput-button">
  <i class="glyphicon glyphicon-plus"></i>
  <span>Загрузить фото...</span>
  <!-- The file input field used as target for the file upload widget -->
  <input id="fileupload" type="file" name="files[]" multiple>
</span>

<br>
<br>
<!-- The global progress bar -->
<div id="progress" class="progress">
  <div class="progress-bar progress-bar-success"></div>
</div>
<!-- The container for the uploaded files -->
<div id="files" class="files"></div>
<div class="admin_gallery_sort_box" id="sortable">
  <?php 
  if($current_images)
  foreach($current_images as $data_image):?>
      
    <div class="item-preview" id="<?=$data_image->id?>">
      
      <div class="admin_gallery_sort_img">
        <a href="/images/gallery/<?=$data_image->image?>" class="fancybox">
          <img src="/images/gallery/tmb_<?=$data_image->image?>"/>
        </a>
      </div>

      <div class="admin_gallery_sort_discription">
        <div data-img_id="<?=$data_image->id?>" class="desc-dynamic">Описание</div>
        <textarea class="description-input form-control" data-img_id="<?=$data_image->id?>" rows="10" cols="45" name="text"><?=$data_image->description?></textarea>
        <label class="do_cover">
          <input data-img_id="<?=$data_image->id?>" data-id="<?=(int)$_GET["id"]?>" class="preview_checkbox" type="checkbox" <?=Gallery::isTmbExist($data_image->image) ? 'checked' : ''?>>
          <span>Сделать обложкой</span>
        </label>
        <a data-img_id="<?=$data_image->id?>" class="delete" href="#">Удалить</a>
      </div>
    </div>

  <?php endforeach;?>
</div>


<style type="text/css">
    .item-preview{
        cursor: move;
    }
    .in_drag{
        background-color: #EDF1F5;
    }
</style>

<script>



/*jslint unparam: true */
/*global window, $ */
$(function () {

    
    $( "#sortable" ).sortable({

        start: function(event, ui) {
            $(ui.item).addClass('in_drag');
        },
        stop: function(event, ui) {
            $(ui.item).removeClass('in_drag');
        },
        update: function (event, ui) {
            var data = $(this).sortable('toArray');
            $.ajax({
                data: {sort: data, album_id: <?=$album_id?>},
                url: '/cp/gallery/orderAlbumImages',
            });
        }
        }
    );


    var fancyboxImages = $('a.fancybox'); 
    if (fancyboxImages.length) {
        $(fancyboxImages).fancybox({
            overlayColor: '#333',
            overlayOpacity: 0.8,
            titlePosition : 'over'
        });
    }

    var currentImageId;
    var typingTimer;                //timer identifier
    var doneTypingInterval = 1500;  //time in ms, 5 second for example
    var $input = $('.description-input');

    //Когда вводим текст, таймер пошёл для конктреного ИД
    $(document).on('keyup', '.description-input', function () {
        currentImageId = $(this).data('img_id');
        $('.desc-dynamic[data-img_id='+currentImageId+']').html('Редактирование.');
        clearTimeout(typingTimer);
        typingTimer = setTimeout(function(){
            doneTyping(currentImageId);
        }, doneTypingInterval);
    });

    //Когда вводим текст таймер начинает отсчет с нуля
    $input.on('keyup', '.description-input', function () {
        clearTimeout(typingTimer);
    });

    $(document).on('blur', '.description-input', function () {
        currentImageId = $(this).data('img_id');
        var text = $('.description-input[data-img_id='+currentImageId+']').val();
        changeDesctiption(currentImageId, text);
    });

    function changeDesctiption( id, text ){
        $.ajax({
            url: '/cp/gallery/updateDescription',
            data: { 
                image_id: id,
                image_desc_text: text
             }
        })
        .done(function(data){
            $('.desc-dynamic[data-img_id='+id+']').html(data);
        });
    }
    //Когда закончили вводить.
    function doneTyping ( id ) {
        var text = $('.description-input[data-img_id='+id+']').val();
        changeDesctiption(id, text);
    }

    $(document).on('click', '.preview_checkbox', function(e){
        $('.preview_checkbox').prop('checked', false);
        var image_id = $(this).data('img_id');
        var album_id = $(this).data('id');

        $.ajax({
            url: '/cp/gallery/updateAlbumPreview',
            data: { 
                image_id: image_id,
                album_id: album_id
             }
        });

        $(this).prop('checked', true);
    });

    $(document).on('click', '.item-preview .delete', function(){
        var image_id = $(this).data('img_id');
        var clicker = $(this).parents('.item-preview');
        $.ajax({
            url: '/cp/gallery/deleteImage',
            data: { image_id: image_id }
        })
        .done(function(data){
            clicker.html(data);
            var ifChecked = $('.do_cover input:checked'); 
            if(ifChecked.length==0){
                ifChecked = $('.do_cover input');
                if(ifChecked.length!=0){
                    $(ifChecked[0]).prop('checked', true);
                    var image_id = $(ifChecked[0]).data('img_id');
                    var album_id = $(ifChecked[0]).data('id');
                    $.ajax({
                        url: '/cp/gallery/updateAlbumPreview',
                        data: { 
                            image_id: image_id,
                            album_id: album_id
                         }
                    });
                }
            }
        });
        return false;
    });

    'use strict';
    var url = '/cp/gallery/upload';
    $('#fileupload').fileupload({
        url: url,
        formData: {id: <?=Yii::app()->request->getQuery('id')?>},
        dataType: 'json',
        done: function (e, data) {

            if(data.result.error==0){
                $('#sortable').append(' \
                    <div class="item-preview" id="'+data.result.image_id+'"> \
                    <div class="admin_gallery_sort_img"> \
                      <a href="/images/gallery/' +data.result.filename+ '" class="fancybox"> \
                         <img src="/' +data.result.img+ '"/> \
                      </a> \
                    </div> \
                        <div class="admin_gallery_sort_discription"> \
                            <div data-img_id="'+data.result.image_id+'" class="desc-dynamic">Описание</div> \
                            <textarea class="description-input form-control" data-img_id="'+data.result.image_id+'" rows="10" cols="45" name="text"></textarea> \
                            <label class="do_cover"> \
                            <input data-img_id="'+data.result.image_id+'" data-id="<?=(int)$_GET["id"]?>" class="preview_checkbox" type="checkbox"> \
                            <span>Сделать обложкой</span> \
                            </label> \
                            <a data-img_id="'+data.result.image_id+'" class="delete" href="#">Удалить</a> \
                        </div> \
                    </div>');
            }
            else{
                //$('<div/>').html('<img src="/' +data.result.img+ '"/>').appendTo('#files');
                $.each(data.result.errors, function (index, error) {
                    $('<p/>').text(error).appendTo('#files');
                });   
            }

            var ifChecked = $('.do_cover input:checked'); 
            if(ifChecked.length==0){
                ifChecked = $('.do_cover input');
                if(ifChecked.length!=0){
                    $(ifChecked[0]).prop('checked', true);
                    var image_id = $(ifChecked[0]).data('img_id');
                    var album_id = $(ifChecked[0]).data('id');
                    $.ajax({
                        url: '/cp/gallery/updateAlbumPreview',
                        data: { 
                            image_id: image_id,
                            album_id: album_id
                         }
                    });
                }
            }

        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .progress-bar').css(
                'width',
                progress + '%'
            );
        }
    }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');
});
</script>
<br>
<?php echo CHtml::link('Вернутся к альбомам', array('gallery/index'), array('class'=>'btn btn-warning')); ?>

<script src="/js/fancybox/jquery.fancybox.min.js"></script>
<script src="/js/jfileupload/js/vendor/jquery.ui.widget.js"></script>
<script src="/js/jfileupload/js/jquery.fileupload.js"></script>
<script src="/js/jfileupload/js/jquery.fileupload-process.js"></script>
<script src="/js/jfileupload/js/jquery.fileupload-audio.js"></script>
<script src="/js/jfileupload/js/jquery.fileupload-video.js"></script>
<script src="/js/jfileupload/js/jquery.fileupload-validate.js"></script>

