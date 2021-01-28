<?php
namespace common\components\pagers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

/**
 * Постраничный вывод кнопкой "Показать еще"
 * 
 * Пример добавления в CListView
 * 'pager'=>[
 *      'class' => '\common\components\pagers\MorePager',
 *      'htmlOptions'=>['class'=>'pager-btn', 'style'=>'margin:0 auto'],
 *  ],
 */
class MorePager extends \CBasePager
{
    public $label='Показать еще';
    
    /**
     * Имя параметра, который указывает, что необходимо
     * @var string
     */
    public $moreVar='more';
    
    /**
     * @var array HTML attributes for the pager container tag.
     */
    public $htmlOptions=[];
    
    /**
     * Дополнительные параметры
     * 
     * @see \common\widgets\listing\ButtonMore
     */
    public $buttonOptions=[];
    
    
    /**
     * Дополнительный js скрипт, который будет отработан 
     * при отрисовке кнопки.
     * @var string|null
     */
    public $js=null;
    
    /**
     * Initializes the pager by setting some default property values.
     */
    public function init()
    {
        if(!isset($this->htmlOptions['id'])) {
            $this->htmlOptions['id']=$this->getId();
        }
        
        if(!isset($this->htmlOptions['class'])) {
            $this->htmlOptions['class']='yiiPager';
        }
    }
    
    /**
     * Executes the widget.
     * This overrides the parent implementation by displaying the generated page buttons.
     */
    public function run()
    {
        $this->createButton();
        
        if($this->js) {
            Y::js(false, $this->js, null);
        }
    }
    
    public function createButton()
    {
        $pageCount=$this->getPageCount();
        
        if($pageCount > 1) {
            $currentPage=$this->getCurrentPage();
            
            if($currentPage < $pageCount-1) {
                $owner=$this->getOwner();
                
                if(!A::existsKey($this->buttonOptions, 'dataProvider') && property_exists($owner, 'dataProvider')) {
                    $this->buttonOptions['dataProvider']=$owner->dataProvider;
                }
                
                if(!A::existsKey($this->buttonOptions, 'container') && property_exists($owner, 'itemsCssClass')) {
                    $this->buttonOptions['container']='.' . trim(preg_replace('/\s+/', '.', $owner->itemsCssClass), '.');
                }
                
                if(!A::existsKey($this->buttonOptions, 'id')) {
                    $this->buttonOptions['id']='btn'.$this->getId();
                }
                
                if(!A::existsKey($this->buttonOptions, 'label')) {
                    $this->buttonOptions['label']=$this->label;
                }
                
                if(!A::existsKey($this->buttonOptions, 'htmlOptions')) {
                    $this->buttonOptions['htmlOptions']=$this->htmlOptions;
                }
                
                if(!A::existsKey($this->buttonOptions, 'url')) {
                    $this->buttonOptions['url']=trim(preg_replace("/[?|&]{$this->getPages()->pageVar}=[^&]*/i", '', $_SERVER['REQUEST_URI']), '?');
                }
                
                $this->getOwner()->widget('\common\widgets\listing\ButtonMore', A::m($this->buttonOptions, [
                    'pageVar'=>$this->getPages()->pageVar,
                    'pageSize'=>$this->getPageSize(),
                    'pageCount'=>$this->getPageCount()
                ]));
            }
        }
    }    
}
