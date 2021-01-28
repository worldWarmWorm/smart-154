<?php
/* @var $this AccordionController */
/* @var $model Accordion */

$this->breadcrumbs=array(
	'Аккордеоны'=>array('index'),
	'Управление',
);
?>
<h1>Редактирование аккордеонов</h1>

<a href="/cp/accordion/create" type="button" class="btn btn-primary addItem">Добавить аккордеон</a>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'accordion-grid',
	'dataProvider'=>$model->search(),
	'itemsCssClass'=>'table table-striped  table-bordered table-hover items_sorter',
	#'filter'=>$model,
	'columns'=>array(
		'id',
		'title',
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
