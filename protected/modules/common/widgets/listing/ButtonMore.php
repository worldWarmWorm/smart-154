<?php
namespace common\widgets\listing;

use common\components\helpers\HYii as Y;
use common\components\helpers\HHash;

/**
 * Кнопка "Загрузить еще"
 *
 */
class ButtonMore extends \CWidget
{
    /**
     * JS-идентификатор кнопки
     * @var string|null
     */
    public $id=null;
    
    /**
     * 
     * Провайдер данных.
     * Вместо провайдера данных, может быть переданы 
     * параметры $pageVar, $pageSize и $pageCount. Данный параметр 
     * является более приориетным перед $pageVar, $pageSize и $pageCount.  
     * 
     * @var \CDataProvider
     */
    public $dataProvider;
    
    /**
     * Имя переменной пагинации.
     * @var string
     */
    public $pageVar=null;
    
    /**
     * Кол-во элементов на странице.
     * @var string
     */
    public $pageSize=null;
    
    /**
     * Кол-во страниц
     * @var string
     */
    public $pageCount=null;
    
    /**
     * 
     * URL получения HTML кода элементов.
     * @var string
     */
    public $url;
    
    /**
     * Javascript код получения URL
     * @var string|null
     */
    public $jsGetUrl=null;
    
    /**
     * Проверить кол-во страниц
     * @var string|null
     */
    public $checkPageCount=false;
    
    /**
     * Javascript код обработчика вызываемого, 
     * после обновления контейнера данных
     * @var string|null
     */
    public $onAfterUpdate=null;
    
    /**
     *  
     * jQuery выражение для получения DOM-элемента контейнера,
     * в который будет добавлен список товаров.
     * @var string
     */
    public $container;
    
    /**
     * Подпись кнопки "Загрузить еще"
     * @var string
     */
    public $label='Загрузить еще';
    
    /**
     * Дополнительные HTML атрибуты кнопки
     * @var array
     */
    public $htmlOptions=[];
    
    /**
     * 
     * {@inheritDoc}
     * @see \CWidget::run()
     */
    public function run()
    {
        if((!$this->pageCount || !$this->pageSize) && !$this->dataProvider) {
            return false;
        }
        
        if($this->dataProvider) {
            $this->pageVar=$this->dataProvider->getPagination()->pageVar;
            $this->pageCount=$this->dataProvider->getPagination()->getPageCount();
            $this->pageSize=(int)$this->dataProvider->getPagination()->getPageSize();
			$this->htmlOptions['data-page']=(int)$this->dataProvider->getPagination()->getCurrentPage() + 1;
			if($this->htmlOptions['data-page'] < 2) {
                $this->htmlOptions['data-page']=2;
            }
        }
		else {
			$this->htmlOptions['data-page']=2;
		}
        
        if(($this->url || $this->jsGetUrl) && $this->container) {
            if($this->pageCount > 1) {
                if(!$this->id) {
                    $this->id=HHash::ujs();
                }                
                
                $this->htmlOptions['data-js']=$this->id;
                
                echo \CHtml::link($this->label, ($this->url ?: '#'), $this->htmlOptions);
                
                $this->registerScript($this->id);
            }
        }
    }
    
    public function registerScript($jsId)
    {
        $script=<<<EOJS
        \$(document).on("click", "[data-js='{$jsId}']", function(e){ 
            e.preventDefault();
            var btn=\$("[data-js={$jsId}]");
EOJS;
        
        if($this->jsGetUrl) $script.='var url=(function(){'.$this->jsGetUrl.'})();';
        else $script.='var url=btn.attr("href");';
        
        $script.=<<<EOJS
            \$.get(url,{{$this->pageVar}:btn.attr("data-page"),limit:{$this->pageSize}},function(html){
                \$("{$this->container}").append(\$(html).find("{$this->container}").html());
EOJS;
        
        if($this->checkPageCount) $isLastPageExpression='!$(html).find("[data-js=\''. $jsId . '\']").length';
        else $isLastPageExpression="((+btn.attr('data-page') + 1) > {$this->pageCount})";
        
        $script.=<<<EOJS
                if($isLastPageExpression){ btn.hide(); }
                else { btn.attr("data-page",+btn.attr("data-page")+1); }
                (function(){{$this->onAfterUpdate}})();
            });
        });
EOJS;
        
        Y::js($jsId, $script, \CClientScript::POS_READY);
    }
}
