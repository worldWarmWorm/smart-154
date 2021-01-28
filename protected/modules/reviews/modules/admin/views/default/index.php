<?
/** @var \reviews\modules\admin\controllers\DefaultController $this */
/** @var \CActiveDataProvider $dataProvider */
use common\components\helpers\HYii as Y;

$t=Y::ct('\reviews\modules\admin\AdminModule.common');
$tpd=Y::ct('\reviews\modules\admin\AdminModule.controllers/default');
$tc=Y::ct('CommonModule.labels', 'common');
$tbtn=Y::ct('CommonModule.btn', 'common');
?>
<h1><?= $tpd('page.index.title'); ?></h1>

<div>
	<?= CHtml::link($tbtn('add'), ['reviews/create'], ['class'=>'btn btn-primary']); ?>
	<?= CHtml::link('<span class="glyphicon glyphicon-cog"></span>&nbsp;'.$tbtn('settings'), ['reviews/settings'], ['class'=>'btn btn-warning pull-right']); ?>
</div>

<div class="list__wrapper">
	<?$this->widget('zii.widgets.grid.CGridView', [
    	'id'=>'reviews-grid',
    	'dataProvider'=>$dataProvider,
    	'itemsCssClass'=>'table table-striped  table-bordered table-hover items_sorter',
		'pagerCssClass'=>'pagination',
    	'rowHtmlOptionsExpression' => 'array("id"=>$data->id)',
    	'enableHistory'=>true,
    	'columns'=>[
        	'id',   
        	[
			    'name'=>'author',
			    'type'=>'raw',
			],
			[
			    'name'=>'preview_text',
			    'type'=>'raw',
		    ],
			[
				'name'=>'published', 
				'type'=>'raw',
				'headerHtmlOptions'=>['style'=>'width:10%'],
				'htmlOptions'=>['style'=>'text-align:center'],
				'value'=>'$this->grid->owner->widget("\common\\\\ext\active\widgets\InList", [
        			"behavior"=>$data->activeBehavior, 
        			"changeUrl"=>$this->grid->owner->createUrl("reviews/changeActive", ["id"=>$data->id]), 
        			"cssMark"=>"unmarked", 
        			"cssUnmark"=>"marked", 
        			"wrapperOptions"=>["class"=>"mark"]
        		], true)'
			],
			[
				'name'=>'create_time',
				'headerHtmlOptions'=>['style'=>'width:15%;text-align:center'],
				'htmlOptions'=>['style'=>'text-align:center']
			],
	        [  
    	        'class'=>'CButtonColumn',
        	    'template'=>'{update}{delete}',
            	'updateButtonImageUrl'=>false,
            	'deleteButtonImageUrl'=>false,
            	'buttons'=>[
                	'delete' => [
	                    'label'=>'<span class="glyphicon glyphicon-remove"></span> ',
	                    'url'=>'Yii::app()->createUrl("cp/reviews/delete", array("id"=>$data->id))',
	                    'options'=>array('title'=>$tbtn('remove')),
                	],
                	'update' => [
	                    'label'=>'<span class="glyphicon glyphicon-pencil"></span> ',
	                    'url'=>'Yii::app()->createUrl("cp/reviews/update", array("id"=>$data->id))',
	                    'options'=>array('title'=>$tbtn('edit')),
                	],
            	],
        	],

    	],
	]);?>
</div>
