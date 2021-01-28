<? $this->breadcrumbs = array('Атрибуты товара'=>array('attributes/index'));?>

<h1>Атрибуты товара</h1>

<a class="btn btn-primary" href="<?php echo $this->createUrl('attributes/add')?>">Новый атрибут</a>



<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'attributes-grid',
    'itemsCssClass'=>'table table-striped table-bordered table-hover',
    'dataProvider'=>$model->search(),
    'filter'=>$model,
    'columns'=>array(
        #'name',
        array('name'=>'name',
            'filter'=>CHtml::textField(
                'Attributes[name]', 
                isset($_GET['Attributes']['name']) ? $_GET['Attributes']['name'] : '',
                array('class'=>'form-control')
            )),
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
)); 

?>

