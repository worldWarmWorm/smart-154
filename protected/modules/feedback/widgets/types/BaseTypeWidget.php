<?php
/**
 * Base type widget
 * 
 */
namespace feedback\widgets\types;

class BaseTypeWidget extends \CWidget implements IBaseTypeWidget
{
	/**
	 * Widget parameters
	 * @var array
	 */
	public $params = array();
	
	/**
	 * @var string|false задание шаблона отображения элемента формы.
	 */
	public $view = false;
	
	/**
	 * (non-PHPdoc)
	 * @see \feedback\widgets\types\IBaseTypeWidget::run()
	 */
	public function run($name, \feedback\components\FeedbackFactory $factory, \CActiveForm $form)
	{
		
	}
	
	/**
	 * Получить имя шаблона отображения.
	 * @param string $default шаблон отображения по умолчанию
	 * @return string
	 */
	public function getView($default)
	{
		if($this->view) {
			return $this->view;
		}
		
		return $default;
	}
}