<?php 
    $img_url = '/images/'.$this->params['model'].'/'.$this->params['img']; 
    $modificator = $this->params['modificator'];
?>

<div data-id=<?php echo $this->params['id']; ?> class="product-image-resize">
    Редактировать
</div>

<script type="text/javascript">
jQuery(function($){

    $("[data-edit="+<?php echo $this->params['id']; ?>+"] .crop-image").on('click', function(){
            $.post( "/aimg/ajaxcrop", { 
            id: <?php echo $this->params['id']; ?>, 
            image: "<?php echo $img_url; ?>", 
            x: $("#x_<?php echo $this->params['id']; ?>").val(),
            y: $("#y_<?php echo $this->params['id']; ?>").val(),
            w: $("#w_<?php echo $this->params['id']; ?>").val(),
            h: $("#h_<?php echo $this->params['id']; ?>").val(),
            }).done(function( data ) {
                $.fancybox.close();
                });  
    });
    var jcrop_api,
        boundx,
        boundy,
        $preview = $("#pimg<?php echo $this->params['id']; ?>"),
        $pcnt = $("#pimg<?php echo $this->params['id']; ?> .preview-container"),
        $pimg = $("#pimg<?php echo $this->params['id']; ?> .preview-container"),
        xsize = $pcnt.width(),
        ysize = $pcnt.height();
    
    $("#img<?php echo $this->params['id']; ?>").Jcrop({
      aspectRatio: "<?php echo $this->params['ratio']; ?>",
      onChange: updatePreview,
      onSelect: updatePreview,

    },function(){
      // Use the API to get the real image size
      var bounds = this.getBounds();
      boundx = bounds[0];
      boundy = bounds[1];
      // Store the API in the jcrop_api variable
      jcrop_api = this;
      // Move the preview into the jcrop container for css positioning
      $preview.appendTo(jcrop_api.ui.holder);
    });

    function updatePreview(c)
    {
    $("#x_<?php echo $this->params['id']; ?>").val(c.x);
    $("#y_<?php echo $this->params['id']; ?>").val(c.y);
    $("#w_<?php echo $this->params['id']; ?>").val(c.w);
    $("#h_<?php echo $this->params['id']; ?>").val(c.h);

      if (parseInt(c.w) > 0)
      {
        var rx = xsize / c.w;
        var ry = ysize / c.h;

        $pimg.css({
          width: Math.round(rx * boundx) + 'px',
          height: Math.round(ry * boundy) + 'px',
          marginLeft: '-' + Math.round(rx * c.x) + 'px',
          marginTop: '-' + Math.round(ry * c.y) + 'px'
        });
      }
    };

  });


</script>

<div style="display:none" data-edit="<?php echo $this->params['id']; ?>">


  <div class="crop-img">
    <img id="img<?php echo $this->params['id']; ?>" src="<?php echo $img_url; ?>">
  </div>

<!--   <div class="pimg_pane">
    <div class="preview-container" id="pimg<?php echo $this->params['id']; ?>">
      <img src="<?php echo $img_url; ?>" class="jcrop-preview" alt="Preview" />
    </div>
  </div> -->




  <form action="aimg/ajaxcrop" method="post">
      <input type="hidden" id="x_<?php echo $this->params['id']; ?>" name="x" />
      <input type="hidden" id="y_<?php echo $this->params['id']; ?>" name="y" />
      <input type="hidden" id="w_<?php echo $this->params['id']; ?>" name="w" />
      <input type="hidden" id="h_<?php echo $this->params['id']; ?>" name="h" />
  </form>
    <button class="crop-image crop-image-btn">Применить</button>
</div>


<?php 
    
/*    $this->widget('ext.jcrop.EJcrop', array(
        'url' => $img_url,
        'id'=>'img'.$this->params['id'],
        'alt' => 'Модификация изображения',
        'options' => array(
            'minSize' => array(150, 150),
            'maxSize' => array($this->params['maxSize_w'], $this->params['maxSize_h']),
           # 'allowSelect' => 1,
            'aspectRatio' => $this->params['ratio'],
           # 'onChange'  => 'js:function(coords) { showPreview(coords) }',
            #'onSelect'  => 'js:function(coords) { showPreview(coords); }',
            'onRelease' => 'js:function() { ejcrop_cancelCrop(this); die_fancybox('. $this->params['id'] .'); }',
        ),
        'buttons' => array(
            'start' => array(
                'label' => 'Редактировать',
                'htmlOptions' => array(
                    'class' => 'edit_image',
                )
            ),
            'crop' => array(
                'label' => 'Применить',
            ),
            'cancel' => array(
                'label' => 'Отмена'
            )
        ),
        'ajaxUrl' => Yii::app()->createUrl('aimg/ajaxcrop'),
        'ajaxParams' => array('product_id' => $this->params['id'], 'image' => $img_url, 'modificator'=>$modificator),
    ));*/
?>

<style type="text/css">

</style>