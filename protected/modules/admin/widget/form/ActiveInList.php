<?php
namespace admin\widget\form;

class ActiveInList extends \CWidget
{
	/**
	 * @var \DActiveBehavior поведение атрибута активности.
	 */
	public $behavior;
	
	/**
	 * @var string имя поведения, если требуется передать в действие смены активности.
	 * Данное имя будет передано в параметре "behavior".
	 */
	public $behaviorName='';
	
	/**
	 * @var string ссылка на действие смены активности.
	 */
	public $changeUrl;
	
	 /**
     * @var string тэг общей обертки 
     */
    public $wrapperTagName='div';
    
    /**
     * @var array HTML атрибуты для элемента общей обертки
     */
    public $wrapperOptions=array();
    
    /**
     * @var string css класс для активного элемента.
     */
    public $cssMark='marked'; 
    
    /**
     * @var string css класс для неактивного элемента.
     */
    public $cssUnmark='unmarked'; 
	
    /**
     * (non-PHPdoc)
     * @see CWidget::init()
     */
    public function init()
    {
    	\YiiHelper::csjs('WidgetActiveInList', 
'$(document).on("click", ".js-active-mark", function() {
    var $this=$(this);
    $.post($this.data("url"), {behavior: "'.$this->behaviorName.'"}, function(data) {
		if(data.success) {
    		if($this.hasClass("'.$this->cssUnmark.'")) {
   				$this.removeClass("'.$this->cssUnmark.'");
    			$this.addClass("'.$this->cssMark.'");
    		}
    		else {
    			$this.removeClass("'.$this->cssMark.'");
    			$this.addClass("'.$this->cssUnmark.'");
            }
    	}
    }, "json"); 
	return false; 
});', 
    		\CClientScript::POS_READY
    	);
    }
    
	/**
	 * (non-PHPdoc)
	 * @see CWidget::run()
	 */
	public function run()
	{
		$cssClass=isset($this->wrapperOptions['class']) ? $this->wrapperOptions['class'] . ' ' : '';  
		
		$this->wrapperOptions['class']=$cssClass . 'js-active-mark ' . ($this->behavior->isActive() ? $this->cssMark : $this->cssUnmark);
		$this->wrapperOptions['data-url']=$this->changeUrl;
				
		$this->render('active_in_list');
	}
}