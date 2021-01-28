<?php
/**
 * Виджет модуля Акции
 */
namespace widget\sale;

class SaleList extends \CWidget
{
	/**
	 * @var boolean показать/скрыть заголовок. По умолчанию FALSE(скрыть).
	 */
    public $showTitle=false;
    
	/**
	 * @var boolean показать/скрыть заголовок акции. По умолчанию TRUE(показывать).
	 */
    public $showSaleTitle=true;
    
    /**
     * @var integer кол-во акций в ленте.
     */
    public $limit=null;
    
    /**
     * @var boolean показать/скрыть ссылку "Все Акции". По умолчанию FALSE(скрыть).
     */
    public $showLinkAll=true;
    
    /**
     * @var string тэг общей обертки 
     */
    public $wrapperTagName='div';
    
    /**
     * @var string тэг обертки для элементов
     */
    public $itemsTagName='ul';
    
    /**
     * @var string тэг элемента
     */
    public $itemTagName='li';
    
    /**
     * @var array HTML атрибуты для элемента обертки 
     */
    public $htmlOptions=array();
    
    /**
     * @var array HTML атрибуты для элемента общей обертки 
     */
    public $wrapperOptions=array();
    
    /**
     * @var array HTML атрибуты для элемента ссылки на страницу со списком
     */
    public $linkAllOptions=array();

    /**
     * (non-PHPdoc)
     * @see CWidget::run()
     */
    public function run()
    {
    	if(!\D::yd()->isActive('sale')) return false;
    	
    	$criteria=array();
    	
    	if($this->limit !== null) 
    		$criteria['limit']=(int)$this->limit;
    	 
        $models=\Sale::model()->actived()->previewColumns()->findAll($criteria);

        if($models) { 
        	$this->render('list', compact('models'));
        }
    }
}
