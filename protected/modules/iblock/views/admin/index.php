<?php
/* @var $this iblock\controllers\AdminController */
/* @var $model iblock\models\InfoBlock */

$this->breadcrumbs=array(
	'Информационные блоки'
);
?>
<h1>Информационные блоки</h1>

<a href="/cp/iblock/create" type="button" class="btn btn-primary">Создать информационный блок</a>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'info-block-grid',
	'dataProvider'=>$model->search(),
	'itemsCssClass'=>'table table-striped  table-bordered table-hover items_sorter',
	'filter'=>$model,
	'columns'=>array(
		'id',
		'title',
		//'code',
		'sort',
        [
            'name' => 'active',
            'value' => function ($data) {
                return $data->active ? 'да' : 'нет';
            }
        ],
        [
            'name' => 'use_preview',
            'header' => 'Фото',
            'value' => function ($data) {
                return $data->use_preview ? 'да' : 'нет';
            },
        ],
        [
            'name' => 'use_description',
			'header' => 'Описание',
            'value' => function ($data) {
                return $data->use_description ? 'да' : 'нет';
            },
        ],
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
