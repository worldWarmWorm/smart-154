<?php $this->pageTitle = $this->getGalleryHomeTitle().' - '. $this->appName; 

$this->breadcrumbs=array(
    $this->getGalleryHomeTitle()=>array('gallery/index'),
);

?>

<script type="text/javascript">
    $(document).ready(function(){
        $('body').on('dblclick', '#album-grid tbody tr', function() {
            var id = $.fn.yiiGridView.getKey(
                'album-grid',
                $(this).prevAll().length 
            );
            window.location.pathname = '/cp/gallery/images/'+id;
        });
    });

</script>
<a href="<?=Yii::app()->createUrl('cp/gallery/createGallery')?>" type="button" class="btn btn-primary">Создать альбом</a>

<?php
    $str_js = "
        var fixHelper = function(e, ui) {
            ui.children().each(function() {
                $(this).width($(this).width());
            });
            return ui;
        };
 
        $('#album-grid table.items_sorter tbody').sortable({
            forcePlaceholderSize: true,
            forceHelperSize: true,
            items: 'tr',
            update : function () {
                var data = $(this).sortable('toArray');
                console.log(data);
                $.ajax({
                    'url': '" . $this->createUrl('orderAlbums') . "',
                    'type': 'GET',
                    'data': {items: data},
                    'success': function(data){
                    },
                    'error': function(request, status, error){
                    //    alert('We are unable to set the sort order at this time.  Please try again in a few minutes.');
                    }
                });
            },
            helper: fixHelper
        }).disableSelection();
    ";
 
    Yii::app()->clientScript->registerScript('sortable-project', $str_js);
?>

<?php 

$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'album-grid',
    'dataProvider'=>$albums,
    'itemsCssClass'=>'table table-striped  table-bordered table-hover items_sorter',
    'rowHtmlOptionsExpression' => 'array("id"=>$data->id)',
    'enableHistory'=>true,
    'columns'=>array(
        'id',   // display the 'content' attribute as purified HTML
        'title',

        array( 'name'=>'description', 'type'=>'raw', 'value'=>function($data){
             return HtmlHelper::getIntro($data->description);
        }),
        
        [
        	'name'=>'published',
        	'type'=>'raw',
        	'headerHtmlOptions'=>['style'=>'width:10%'],
        	'htmlOptions'=>['style'=>'text-align:center'],
        	'value'=>'$this->grid->owner->widget("\common\\\\ext\active\widgets\InList", [
        		"behavior"=>$data->publishedBehavior,
        		"changeUrl"=>$this->grid->owner->createUrl("gallery/changePublished", ["id"=>$data->id]),
        		"cssMark"=>"unmarked",
        		"cssUnmark"=>"marked",
        		"wrapperOptions"=>["class"=>"mark"]
        	], true)'
         ],         										 

        array(            // display a column with "view", "update" and "delete" buttons
            'class'=>'CButtonColumn',
            'template'=>'{add_img}{update}{delete}',
            'updateButtonImageUrl'=>false,
            'deleteButtonImageUrl'=>false,
            'buttons'=>array
            (
                'add_img' => array
                (   
                    'label'=>'<span class="glyphicon glyphicon-picture"></span> ',
                    'url'=>'Yii::app()->createUrl("cp/gallery/images", array("id"=>$data->id))',
                    'options'=>array('title'=>'Добавить изображения в альбом'),
                ),
                'delete' => array
                (   
                    'label'=>'<span class="glyphicon glyphicon-remove"></span> ',
                    'url'=>'Yii::app()->createUrl("cp/gallery/deleteGallery", array("id"=>$data->id))',
                    'options'=>array('title'=>'Удалить'),
                ),
                'update' => array
                (      
                    'label'=>'<span class="glyphicon glyphicon-pencil"></span> ',
                    'url'=>'Yii::app()->createUrl("cp/gallery/updateGallery", array("id"=>$data->id))',
                    'options'=>array('title'=>'Редактировать'),
                ),
            ),
        ),

    ),
));

?>


<style>
    .grid-view .button-column {
        width: 80px;
    }
</style>