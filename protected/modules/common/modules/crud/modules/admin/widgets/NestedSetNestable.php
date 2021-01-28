<?php
namespace crud\modules\admin\widgets;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use crud\components\helpers\HCrud;

class NestedSetNestable extends \common\ext\nestedset\widgets\BaseNestable
{
    /**
     * @var string идентификатор конфигурации CRUD.
     */
    public $cid;
    
    /**
     * Поведение реализующее NestedSet
     * @var string|\common\ext\nestedset\behaviors\NestedSetBehavior|null
     */
    public $behavior='nestedSetBehavior';
    
    /**
     * {@inheritDoc}
     * @see \common\ext\nestedset\widgets\BaseNestable::$itemView
     */
    public $itemView='crud.modules.admin.widgets.views._nestedset_item_view';
    
    /**
     * {@inheritDoc}
     * @see \common\ext\nestedset\widgets\BaseNestable::$view
     */
    public $view='crud.modules.admin.widgets.views.nestedset_nestable';
    
    /**
     * @var array конфигурация колонок элемента, вида type=>config, либо type.
     * Где "config" массив или строка. 
     */
    public $columns=[
        'title',
        'btn.update',
        'btn.delete'
    ];
    
    /**
     * {@inheritDoc}
     * @see \common\ext\nestedset\widgets\BaseNestable::init()
     */
    public function init()
    {
        if(is_string($this->behavior) && ($this->dataProvider instanceof \CActiveDataProvider)) {
            $this->behavior = $this->dataProvider->model->{$this->behavior};
        }
        
        if($this->behavior instanceof \common\ext\nestedset\behaviors\NestedSetBehavior) {
            $this->rootAttribute = $this->behavior->rootAttribute;
            $this->levelAttribute = $this->behavior->levelAttribute;
        }
        
        parent::init();
    }
    
    /**
     * {@inheritDoc}
     * @see \CWidget::run()
     */
    public function run()
    {
        if($this->behavior instanceof \common\ext\nestedset\behaviors\NestedSetBehavior) {
            $this->options['rootAttribute'] = $this->behavior->rootAttribute;
            $this->options['leftAttribute'] = $this->behavior->leftAttribute;
            $this->options['rightAttribute'] = $this->behavior->rightAttribute;
            $this->options['levelAttribute'] = $this->behavior->levelAttribute;
            if($this->behavior->hasManyRoots) {
                $this->options['orderingAttribute'] = $this->behavior->orderingAttribute;
            }
        }
            
        $this->publishAssets();
            
        $this->render($this->view, [
            'cid'=>$this->cid,
            'dataProvider'=>$this->dataProvider            
        ]);
    }
    
    /**
     * Получить объект действия контроллера
     * @return \CAction
     */
    public function getAction()
    {
        return $this->getOwner()->getAction();
    }
    
    /**
     * Получить элементы
     * @return array|array[]
     */
    public function getData()
    {
        $data = $this->dataProvider->getData();
        
        if($this->behavior instanceof \common\ext\nestedset\behaviors\NestedSetBehavior) {
            $b = $this->behavior;
            if($b->hasManyRoots) {
                $_data = [];
                $_ordering = [];
                foreach($data as $item) {
                    if(!isset($_data[$item->{$b->rootAttribute}])) {
                        $_data[$item->{$b->rootAttribute}] = [];
                        $_ordering[$item->{$b->rootAttribute}] = $item->{$b->orderingAttribute};
                    }
                    $_data[$item->{$b->rootAttribute}][] = $item;
                }
                asort($_ordering);
                $data = [];
                foreach($_ordering as $root=>$ordering) {
                    foreach($_data[$root] as $item) {
                        $data[] = $item;
                    }
                }
            }
        }
        
        return $data;
    }
    
    /**
     * Вывод HTML кода колонок.
     * @param mixed $data данные элемента
     * @param array $htmlOptions массив дополнительных атрибутов для элемента обертки.
     * "tag" - имя тэга обертки, по умолчанию "div"
     */
    public function printColumns($data, $htmlOptions=[])
    {
        if(count($this->columns) > 0) {
            $customOptions = [
                'tag'=>'div', 
                'columnOptions'=>['class'=>'col-md-2']
            ];
            foreach($customOptions as $optionName=>$optionDefault) {
                ${'htmlOption'.ucfirst($optionName)} = A::get($htmlOptions, $optionName, $optionDefault);
                if(A::existsKey($htmlOptions, $optionName)) {
                    unset($htmlOptions[$optionName]);
                }
            }
            
            echo \CHtml::openTag($htmlOptionTag, A::m(['class'=>'row crud__row-columns'], $htmlOptions));
            
            foreach($this->columns as $type=>$columnConfig)
            {
                if(is_numeric($type)) {
                    if(is_callable($columnConfig)) {
                        echo call_user_func($columnConfig, $data);
                        continue;
                    }
                    $type=$columnConfig;
                    $columnConfig=[];
                }
                
                $columnConfig = A::toa($columnConfig);
                
                if(strpos($type, 'relation.link.') === 0) {
                    $columnConfig['crud'] = substr($type, 14);
                    $type = 'relation.link';
                }
                elseif(strpos($type, 'title.relation.') === 0) {
                    $columnConfig['crud'] = substr($type, 15);
                    $type = 'title.relation';
                }
                
                $columnConfig['type']=$type;
                
                $columnOptions = A::m(['class'=>'col-md-2'], A::m($htmlOptionColumnOptions, A::get($columnConfig, 'htmlOptions', [])));
                echo \CHtml::tag(
                    A::get($columnConfig, 'tag', 'div'),
                    $columnOptions,
                    $this->getColumnHtml($data, $columnConfig)
                );
            }
            
            echo \CHtml::closeTag($htmlOptionTag);
        }
    }
    
    /**
     * Получить HTML код колонки
     * @param mixed $data данные элемента
     * @param array $columnConfig массив конфигурации колонки
     */
    public function getColumnHtml($data, $columnConfig)
    {
        $html = '';
        
        switch(A::get($columnConfig, 'type'))
        {
            case 'title':
                $titleAttribute = A::get($columnConfig, 'titleAttribute', 'title');
                $html = $data->$titleAttribute;
                break;
                
            case 'title.link':
                $titleAttribute = A::get($columnConfig, 'titleAttribute', 'title');
                $updateUrl=HCrud::getConfigUrl($this->cid, 'crud.update.url', '/crud/admin/default/update', ['cid'=>$this->cid, 'id'=>$data->{$this->idAttribute}], 'c');
                
                $html = \CHtml::link($data->$titleAttribute, $updateUrl);
                break;
                
            case 'title.relation':
                $titleAttribute = A::get($columnConfig, 'titleAttribute', 'title');
                $crudRelationId = A::get($columnConfig, 'crud');
                
                $relationUrl=HCrud::getConfigUrl($crudRelationId, 'crud.index.url', '/crud/admin/default/index', [
                    'cid'=>$crudRelationId,
                    $this->cid=>$data->{$this->idAttribute}
                ], 'c');
                
                $html = \CHtml::link($data->$titleAttribute, $relationUrl);
                break;
                
            case 'btn.update':
                $updateUrl=HCrud::getConfigUrl($this->cid, 'crud.update.url', '/crud/admin/default/update', ['cid'=>$this->cid, 'id'=>$data->{$this->idAttribute}], 'c');
                
                $tbtn=Y::ct('\CommonModule.btn', 'common');
                $html = \CHtml::link(
                    A::get($columnConfig, 'label', $tbtn('edit')), 
                    $updateUrl, 
                    ['class'=>'pull-right btn btn-xs btn-info', 'style'=>'margin-left:5px']
                );
                break;
                
            case 'btn.delete':
                $deleteUrl=HCrud::getConfigUrl($this->cid, 'crud.delete.url', '/crud/admin/default/delete', ['cid'=>$this->cid, 'id'=>$data->{$this->idAttribute}], 'c');
                
                $tbtn=Y::ct('\CommonModule.btn', 'common');
                $html = \CHtml::ajaxLink(
                    A::get($columnConfig, 'label', $tbtn('remove')),
                    $deleteUrl,
                    [
						'dataType'=>'json',
						'beforeSend'=>'function(){return confirm(\'Подтвердите удаление\');}',
						'success'=>'function(response){
						if(response.success){$("[data-id="+response.data.id+"]").remove();
						}}'
					],
                    ['class'=>'pull-right btn btn-xs btn-danger', 'style'=>'margin-left:5px']
                );
                break;
                
            case 'relation.link':
                $crudRelationId = A::get($columnConfig, 'crud');
                
                $relationUrl=HCrud::getConfigUrl($crudRelationId, 'crud.index.url', '/crud/admin/default/index', [
                    'cid'=>$crudRelationId,
                    $this->cid=>$data->{$this->idAttribute}
                ], 'c');
                
                $htmlCount='';
                if(A::get($columnConfig, 'relationCount')) {
                    $crudRelationId = A::get($columnConfig, 'crud');
                    $relationCrudConfig = HCrud::config($crudRelationId);
                    $relationClassName = A::get($relationCrudConfig, 'class');
                    $crudRelationAttribute = HCrud::param($this->cid, 'relations.'.$crudRelationId.'.attribute');
                    $htmlCount = ' (' . $relationClassName::model()->wcolumns([$crudRelationAttribute=>$data->id])->count() . ')';
                }
                
                $html = \CHtml::link(
                    HCrud::param($crudRelationId, 'crud.index.title', 'Элементы') . $htmlCount,
                    $relationUrl,
                    ['class'=>'pull-right btn btn-xs btn-warning', 'style'=>'margin-left:5px']
                );
                
                break; 
            
            case 'common.ext.published':
                $behaviorName=A::get($columnConfig, 'behaviorName', 'publishedBehavior');
                $html = $this->owner->widget('\common\ext\active\widgets\InList', [
					'behavior'=>$data->asa($behaviorName),
                    'changeUrl'=>$this->owner->createUrl('/common/crud/admin/default/changeActive', ['cid'=>$this->cid, 'id'=>$data->{$this->idAttribute}, 'b'=>$behaviorName]),
	        		'cssMark'=>'unmarked',
	        		'cssUnmark'=>'marked',
	        		'wrapperTagName'=>'a',
	        		'wrapperOptions'=>['class'=>'mark', 'title'=>'Опубликовать на сайте', 'style'=>'display:block;']
                ], true);
                break;
        }
        
        return $html;
    }
}
