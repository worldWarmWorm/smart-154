<div id="<?php echo $this->createFieldName(); ?>_container" class="uploadedList photos_container clear_fix">
    <?php foreach($items as $item) $this->render('_item_image', compact('item')); ?>
</div>

<script type="text/javascript">
    $(function() {
        var iboxs = $('#<?php echo $this->createFieldName(); ?>_container .img');

        $('#<?php echo $this->createFieldName(); ?>_container').sortable({
            scrollSensitivity: 50,
            distance: 5,
            stop: function(event, ui) {
                var order = $('#<?php echo $this->createFieldName(); ?>_container').sortable('serialize');
                $.post('<?php echo $this->controller->createUrl('default/imageOrder'); ?>', order);
                $(iboxs).removeClass('hover');
            }
        });

        $(iboxs).on('hover', function() {
            $(this).toggleClass('hover');
        });

        $('body').on('click', '#<?php echo $this->createFieldName(); ?>_container .remove-icon', function(e) {
            
            var t = $(this);

            $.get($(this).attr('href'), function(data) {
                if (data == 'ok')
                    $(t).parents('.photo_box').remove();
            });
            return false;
        });
    });

    function saveImageDesc(id) {
        var data = {'desc': $('#desc-'+id).val(), 'id': id};

        $.post('<?php echo Yii::app()->createUrl('admin/default/saveImageDesc'); ?>', data, function(result) {
            if (!parseInt(result)) {
                $('#status-'+id).text('Сохранено!').show(100).delay(2000).hide(100);
            } else {
                $('#status-'+id).text('Ошибка сохранения!');
            }
        });
    }


    function openDialog(id) {
        $('#uplImgModal-'+id).modal({

        });
    }
    $('.modal').on('show.bs.modal', function(){
        var $bodyWidth = $('body').width();
        $('body').css({'overflow-y': "hidden"}).css({'padding-right': ($('body').width()-$bodyWidth)});
    });

    $('.modal').on("hidden.bs.modal", function(){
        $("body").css({'padding-right': "0", 'overflow-y': "auto"});
    });

    function insertImage(self, fancy_tag, id) {
        var alt_title = $('#desc-'+id).val();
        var src = $(self).parents('.photo_box').find('img').attr('src');
        var src_full = src.replace('tmb_', '');
        var ed  = tinyMCE.activeEditor;
        ed.focus();
        ed.execCommand('mceInsertContent', false, '<a class="image-full" href="'+ src_full +'"><img id="__img_tmp" /></a>');
        ed.dom.setAttrib('__img_tmp', 'src', src);
        ed.dom.setAttrib('__img_tmp', 'alt', alt_title);
        ed.dom.setAttrib('__img_tmp', 'title', alt_title);
        ed.dom.setAttrib('__img_tmp', 'id', '');
    }
</script>
