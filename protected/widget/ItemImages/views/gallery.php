<div id="images-gallery" class="fotogallery_page">
    <?php
    $this->widget('zii.widgets.CListView', array(
        'dataProvider' => $imageProvider,
        'itemsTagName' => 'ul',
        'itemView' => '_image',   // refers to the partial view named '_post'
        'pager' => array(
            'maxButtonCount' => '5',
            'header' => '',
        ),
        'itemsCssClass' => 'fotogallery_inner_box',
        'template' => '{items}{pager}',
        'htmlOptions' => array('class' => 'fotogallery_page')
    ));
    ?>
</div>
