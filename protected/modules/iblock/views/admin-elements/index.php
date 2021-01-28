<?php
/* @var $this iblock\controllers\AdminElementsController */
/* @var $model iblock\models\InfoBlockElement */
/* @var $iblock iblock\models\InfoBlock */

$this->breadcrumbs=array(
	$iblock->title,
);
?>
<h1><?=$iblock->title?></h1>

<?=CHtml::link(
	'Добавить запись',
	['/admin/iblockElements/create', 'block_id' => $iblock->id],
	['type' => 'button', 'class' => 'btn btn-primary']
)?>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'info-block-element-grid',
	'dataProvider'=>$model->search(),
	'itemsCssClass'=>'table table-striped  table-bordered table-hover items_sorter',
	'filter'=>$model,
	'columns'=>array(
		'id',
		//'code',
		'title',
        [
			'name' => 'preview',
			'value' => function ($data) {
				/* @var \iblock\models\InfoBlockElement $data */
				if (empty($data->preview)) {
					return '';
				}
				return $data->imageBehavior->img(50, 50, 1);
			},
			'type' => 'raw',
            'visible' => $iblock->use_preview,

		],
        [
            'name' => 'active',
            'value' => function ($data) {
                return (int)$data->active ? 'да' : 'нет';
            }
        ],
        /*'description',
        'created_at',
        'updated_at',
        'sort',
        'info_block_id',
        */
        array(            // display a column with "view", "update" and "delete" buttons
            'class'=>'CButtonColumn',
            'template'=>'{update}{delete}',
            'updateButtonImageUrl'=>false,
            'deleteButtonImageUrl'=>false,
            'buttons'=>array
            (
                'delete' => array
                (   
                    'label'=>'<span class="glyphicon glyphicon-remove"></span> ',
                    'options'=>array('title'=>'Удалить'),
                ),
                'update' => array
                (      
                    'label'=>'<span class="glyphicon glyphicon-pencil"></span> ',
                    'options'=>array('title'=>'Редактировать'),
                ),
            ),
        ),
	),
)); ?>
