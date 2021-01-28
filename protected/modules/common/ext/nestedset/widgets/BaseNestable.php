<?php
/**
 * Базовый класс для виджетов  common\ext\nestedset\widgets\*Nestable
 *
 * Виджет использует JQuery плагин Nestable.
 * @link https://github.com/dbushell/Nestable
 */
namespace common\ext\nestedset\widgets;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

abstract class BaseNestable extends \common\components\base\Widget
{
	/**
	 * @var string id элемента обертки виджета.
	 * Для DOM-элемента с данным "id" будет инициализирован 
	 * jquery-плагин Nestable.  
	 */
	public $id='nestable-widget';
	
	/**
	 * @var string|null выражение выборки элемента обертки виджета
	 */
	public $selector=null;
	
	/**
	 * @var \IDataProvider дата-провайдер элементов.
	 */
	public $dataProvider=null;

	/**
	 * @var string имя атрибута уровня. По умолчанию "level".
	 */
	public $levelAttribute='level';
	
	/**
	 * @var string имя атрибута "id". По умолчанию "id".
	 * Требуется для отображения атрибута data-id="<id>"
	 * для DOM-элемента контейнера элемента.  
	 */
	public $idAttribute='id';
	
	/**
	 * @var string имя атрибута заголовка. По умолчанию "title".
	 * Значение данного атрибута может потребоваться, если не задан 
	 * метод или шаблон отображения содержимого элемента. 
	 */
	public $titleAttribute='title';
	
	/**
	 * @var string имя атрибута id корневого элемента. По умолчанию "root".
	 */
	public $rootAttribute='root';
	
	/**
	 * @var int максимальная глубина вложенности. 
	 * Значение 0(нуль) устанавливает глубину вложенности без ограничения.
	 * Значение данного свойства будет проигнорировано, если оно будет задано в 
	 * свойстве $nestableOptions. 
	 * По умолчанию 0(нуль).
	 */
	public $maxDepth=99;
	
	/**
	 * @var int (nestable option) group ID to allow dragging between lists (default 0)
	 * Значение данного свойства будет проигнорировано, если оно будет задано в 
	 * свойстве $nestableOptions. 
	 */
	public $group=0;
	
	/**
 	 * @var string|array имя шаблона отображения содержимого контейнера элемента, 
 	 * или массив [className, method]. В шаблон отображения или метод будет передан 
 	 * параметр "$data" (текущий элемент из $dataProvider).
	 */
	public $itemView=false;
	
	/**
	 * @var array|null дополнительный массив параметров.
	 */
	public $itemViewData=null;

	/**
	 * @var string текст, который будет отображаться, если ни одного элемента не найдено.
	 */
	public $emptyText='';
	
	/**
	 * @var array опции для query.nestable
	 * @see https://github.com/dbushell/Nestable
	 */
	public $nestableOptions=[];
	
	/**
	 * @var array html атрибуты элемента обертки списка.
	 */
	public $htmlOptions=[];
	
	/**
	 * @var string|null URL сохранения
	 */
	public $saveUrl=null;
	
	/**
	 * @var array параметры для JS скрипта виджета.
	 */
	protected $options = [];
	
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::init()
	 */
	public function init()
	{
		Y::publish([
			'path'=>dirname(__FILE__) . Y::DS . 'assets',
			'js'=>['vendors/jquery.nestable.js', 'basenestable/scripts.js'],
			'css'=>'vendors/jquery.nestable.css'
		]);
		
		$this->nestableOptions['maxDepth']=A::get($this->nestableOptions, 'maxDepth', $this->maxDepth);
		if(empty($this->nestableOptions['maxDepth']) && ($this->nestableOptions['maxDepth'] !== 0)) {
			unset($this->nestableOptions['maxDepth']);
		}
		
		$this->nestableOptions['group']=A::get($this->nestableOptions, 'group', $this->group);
		if(empty($this->nestableOptions['group']) && ($this->nestableOptions['group'] !== 0)) {
			unset($this->nestableOptions['group']);
		}
		
		$this->htmlOptions['class']=A::get($this->htmlOptions, 'class', 'dd');
		if(empty($this->htmlOptions['class'])) {
		    unset($this->htmlOptions['class']);
		}
		if(!empty($this->htmlOptions['class']) && empty($this->nestableOptions['rootClass'])) {
		    $this->nestableOptions['rootClass']=$this->htmlOptions['class'];
		}
		
		if(!$this->selector) {
		    $this->selector = '#' . $this->id;
		}
		
		$this->htmlOptions['id'] = $this->id;
		
		$this->options = [
    		'selector' => $this->selector,
            'saveUrl' => $this->saveUrl,
            'options' => $this->nestableOptions
		];
	}
	
	/**
	 * Получить имя переменной объекта JS обработчика виджета.
	 * @return string
	 */
	public function getJsVarName()
	{
	    return 'js' . $this->id;
	}
	
	/**
	 * Публикация ресурсов виджета
	 */
	public function publishAssets()
	{
	    Y::cs()->registerScript(
	        $this->getJsVarName(),
	        ';var ' . $this->getJsVarName() . '=new common_ext_nestedset_widgets_BaseNestable(' . json_encode($this->options, JSON_UNESCAPED_UNICODE) . ');',
	        \CClientScript::POS_READY
        );
	}
	
	/**
	 * {@inheritDoc}
	 * @see \common\components\base\Widget::run()
	 */
	public function run()
	{
	    $this->publishAssets();
	    
	    parent::run();
	}
	
	/**
	 * Получить id элемента.
	 * @param mixed $data данные элемента.
	 */
	public function getItemId($data) 
	{
	    return ($this->dataProvider instanceof \CArrayDataProvider) ? $data[$this->idAttribute] : $data->{$this->idAttribute};
	}
	
	/**
	 * Получить уровень вложенности элемента.
	 * @param mixed $data данные элемента.
	 * Для \CArrayDataProvider если уровень не передан, 
	 * будет возвращено 1(единица).
	 */
	public function getItemLevel($data) 
	{
		return ($this->dataProvider instanceof \CArrayDataProvider) 
			? A::get($data, $this->levelAttribute, 1) 
			: $data->{$this->levelAttribute};
	}
	
	/**
	 * Получить id корневого элемента.
	 * @param mixed $data данные элемента.
	 */
	public function getItemRoot($data) 
	{
		return ($this->dataProvider instanceof \CArrayDataProvider) 
			? A::get($data, $this->rootAttribute) 
			: $data->{$this->rootAttribute};
	}
	
	/**
	 * Получить заголовок элемента.
	 * @param mixed $data данные элемента.
	 */
	public function getItemTitle($data) 
	{
	    return ($this->dataProvider instanceof \CArrayDataProvider) ? $data[$this->titleAttribute] : $data->{$this->titleAttribute};
	}
	
	/**
	 * Получить содержимое контейнера элемента.
	 * @param mixed $data данные элемента.
	 * @return string
	 */
	public function getItemContent($data)
	{
		$content=null;
		
		if(is_callable($this->itemView) || is_array($this->itemView)) {
			$content=call_user_func($this->itemView, $data, $this->itemViewData);
		}
		elseif(!empty($this->itemView)) {
			$content=$this->render($this->itemView, ['data'=>$data, 'viewData'=>$this->itemViewData], true);	
		}
		else {
			$content=\CHtml::encode($this->getItemTitle($data));
		}
		
		return $content;
	}
}