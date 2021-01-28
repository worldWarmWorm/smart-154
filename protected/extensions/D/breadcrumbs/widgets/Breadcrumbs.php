<?php
/**
 * Виджет хлебных крошек
 * 
 */
namespace ext\D\breadcrumbs\widgets;

class Breadcrumbs extends \CWidget
{
	public $breadcrumbs;
	
	public $htmlOptions=array('class'=>'breadcrumbs');
	
	/**
	 * @var string название домашней страницы. 
	 * Если передано значение NULL элемент отображен не будет
	 */
	public $homeTitle='';
	
	public $homeUrl='/';
	
	/**
	 * @var string использовать микроразметку
	 */
	public $useSchema=true;
	public $view='schema_list';
	
	/**
	 * 
	 * @var array
	 * linkOptions
	 */
	public $homeHtmlOptions=array();
	
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::init()
	 */
	public function init()
	{
		if(!$this->homeTitle && ($this->homeTitle !== null)) { 
			$this->homeTitle=\Yii::t('\ext\D\breadcrumbs\widgets\Breadcrumbs.breadcrumbs', 'homeTitle');
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::run()
	 */
	public function run()
	{
		if($this->view) $view=$this->view;
		elseif($this->useSchema) $view='schema_default';
		else $view='default';

		$this->render($view);
	}
}
